<?php

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($uri, '/'));

$resource = $segments[2] ?? null;
$pengaduan_id = $_GET["pengaduan_id"] ?? null;
$ticket_code = $_GET["ticket_code"] ?? null;

function handleUploads(string $fieldName, string $uploadDir, int $maxSize = 2097152): array
{
    $allowed = [ 'image/jpeg' => 'jpg', 'image/png'  => 'png', 'image/webp' => 'webp' ];

    if (empty($_FILES[$fieldName]) || !isset($_FILES[$fieldName]['name'])) {
        return ['ok' => false, 'uploaded' => null, 'errors' => 'No file uploaded'];
    }

    $files = [];
    $isMultiple = is_array($_FILES[$fieldName]['name']);
    if ($isMultiple) {
        foreach ($_FILES[$fieldName]['name'] as $i => $name) {
            $files[] = [
                'name'     => $_FILES[$fieldName]['name'][$i],
                'type'     => $_FILES[$fieldName]['type'][$i],
                'tmp_name' => $_FILES[$fieldName]['tmp_name'][$i],
                'error'    => $_FILES[$fieldName]['error'][$i],
                'size'     => $_FILES[$fieldName]['size'][$i],
            ];
        }
    } else {
        $files[] = $_FILES[$fieldName];
    }

    $uploaded = [];
    $errors   = [];

    foreach ($files as $file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Upload failed for {$file['name']} (error code {$file['error']})";
            break;
        }

        // Check file size
        if ($file['size'] > $maxSize) {
            // $errors[] = "{$file['name']} too large (max " . round($maxSize / 1048576, 1) . "MB)";
            $errors[] = "Ukuran file yang diupload terlalu besar! Maks: " . round($maxSize / 1048576, 1) . "MB)";
            break;
        }

        // Validate MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!isset($allowed[$mime])) {
            // $errors[] = "{$file['name']} has invalid type ($mime)";
            $errors[] = "Yang anda upload bukan gambar!";
            break;
        }

        // Generate safe unique filename
        $ext = $allowed[$mime];
        $safeName = bin2hex(random_bytes(8)) . '.' . $ext;
        $dest = rtrim($uploadDir, '/') . '/' . $safeName;

        // Move file
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            $errors[] = "Failed to store {$file['name']}";
            break;
        }
        
        $filenames[] = $safeName;

        // Build relative path (for DB)
        $relativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath($dest));
        $uploaded[] = ltrim($relativePath, '/');
    }
    $filenames = isset($filenames) ? $filenames : "empty";
    return [
        'ok' => !empty($uploaded),
        'uploaded' => $uploaded ?: 'failed!',
        'filenames' => $filenames ?: 'failed!',
        'errors' => $errors ?: '-'
    ];
}

function generateTicket()
{
    // --- Generate 10-char unique ticket ---
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $ticket = '';
    for ($i = 0; $i < 10; $i++) {
        $ticket .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $ticket;
}

function getPengaduanById($id) 
{
    $query = "SELECT * FROM pengaduan WHERE id = :id";
    $param = ["id" => $id];
    $result = Database::fetchAll($query, $param);
    return checkResource($result);
}

function getPengaduanByTicketCode($ticket_code) 
{
    $query = "SELECT * FROM pengaduan  WHERE ticket_code = :ticket_code";
    $param = ["ticket_code" => $ticket_code];
    $result = Database::fetchAll($query, $param);
    return checkResource($result);
}

function getDetailedPengaduanAll($page = 1, $limit = 10)
{
    $page  = max(1, (int)$page);
    $limit = max(1, (int)$limit);
    $offset = ($page - 1) * $limit;
    $query = "SELECT * FROM view_pengaduan_detailed ORDER BY id ASC LIMIT :limit OFFSET :offset";
    $param = [ "limit"  => $limit, "offset" => $offset ];
    $result = Database::fetchAll($query, $param);
    return checkResource($result);
}

function getDetailedPengaduanByTicketCode($ticket_code)
{
    $query = "SELECT * FROM view_pengaduan_detailed WHERE ticket_code = :ticket_code";
    $param = ["ticket_code" => $ticket_code];
    $queryImage = "SELECT nama_foto FROM ref_foto_pengaduan WHERE ticket_code = :ticket_code";
    $imagesRaw = Database::fetchAll($queryImage, $param);
    $images = array();
    foreach($imagesRaw as $image){
        $images[] = $image["nama_foto"];
    }
    $result = Database::fetchAll($query, $param);
    if($result){
        $result[0]["images"] = $images;
    }
    return checkResource($result);
}

function verifyPengaduanByTicketCode($ticket_code)
{
    $query = "SELECT ticket_code FROM pengaduan WHERE ticket_code = :ticket_code";
    $param = ["ticket_code" => $ticket_code];
    $result = Database::fetch($query, $param);
    if(!$result){
        return checkResource($result);
    }
    $update = "UPDATE pengaduan SET status = 'diverifikasi' WHERE ticket_code = :ticket_code";
    $update = Database::run($update, $param);
    return http_response_code(204);
}

function rejectPengaduanByTicketCode($ticket_code)
{
    $query = "SELECT ticket_code FROM pengaduan WHERE ticket_code = :ticket_code";
    $param = ["ticket_code" => $ticket_code];
    $result = Database::fetch($query, $param);
    if(!$result){
        return checkResource($result);
    }
    $update = "UPDATE pengaduan SET status = 'ditolak' WHERE ticket_code = :ticket_code";
    $update = Database::run($update, $param);
    return http_response_code(204);
}


function assignPetugasToPengaduan($ticket_code)
{
    $assigned_to = $_GET["petugas"];

    // Check ticket code exist or not
    $query = "SELECT id FROM users WHERE id = :id AND role = 'Petugas Lapangan'";
    $param = ["id" => $assigned_to];
    $result = Database::fetch($query, $param);
    if(!$result){
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Invalid petugas!']);
        exit;        
    }    

    // Check ticket code exist or not
    $query = "SELECT ticket_code FROM pengaduan WHERE ticket_code = :ticket_code";
    $param = ["ticket_code" => $ticket_code];
    $result = Database::fetch($query, $param);
    if(!$result){
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Invalid ticket!']);
        exit;   
    }
    $update = "UPDATE pengaduan SET assigned_to = :assigned_to, status = 'diproses', updated_at = NOW() WHERE ticket_code = :ticket_code";
    $param = [
        'assigned_to' => (int)$assigned_to,
        'ticket_code' => $ticket_code
    ];
    if (Database::run($update, $param)) {
        return http_response_code(204);
    } else {
        return http_response_code(400);
    }
    
}


function addPengaduan() {
    header('Content-Type: application/json; charset=utf-8');
    $json = fn($arr, $code=200) => (http_response_code($code) || true) && print(json_encode($arr, JSON_UNESCAPED_UNICODE));
    $sanitize = fn($s) => htmlspecialchars(trim((string)$s), ENT_QUOTES, 'UTF-8');

    // --- Collect POST data ---
    $kategori         = (int)($_POST['kategori'] ?? 0);
    $id_kecamatan     = (int)($_POST['id_kecamatan'] ?? 0);
    $id_kelurahan     = (int)($_POST['id_kelurahan'] ?? 0);
    $alamat_lengkap   = $sanitize($_POST['alamat_lengkap'] ?? '');
    $deskripsi        = $sanitize($_POST['deskripsi_laporan'] ?? '');
    $tanggal_kejadian = $sanitize($_POST['tanggal_kejadian'] ?? '');
    $anonim           = $sanitize($_POST["anonim"]);
    if($anonim == "on"){
        $anonim = 1;
        $nama_pelapor = '-';
        $email_pelapor    = '-';
        $telp_pelapor     = 00000;
    } else {
        $anonim = 0;
        $nama_pelapor     = $sanitize($_POST['nama_pelapor'] ?? '');
        $email_pelapor    = trim($_POST['email_pelapor'] ?? '');
        $telp_pelapor     = preg_replace('/[^\d+]/', '', $_POST['telp_pelapor'] ?? '');
    }

    $errors = [];

    // --- Basic validation ---
    if (!$kategori)             $errors['kategori'] = 'Required';
    if (!$id_kecamatan)         $errors['id_kecamatan'] = 'Required';
    if (!$id_kelurahan)         $errors['id_kelurahan'] = 'Required';
    if ($alamat_lengkap === '') $errors['alamat_lengkap'] = 'Required';
    if ($deskripsi === '')      $errors['deskripsi_laporan'] = 'Required';
    if ($tanggal_kejadian === '') $errors['tanggal_kejadian'] = 'Required';
    if (empty($_FILES['bukti']) || !isset($_FILES['bukti']['name'])) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'No file uploaded']);
        exit;
    }

    // --- If error found related to input data ---
    if ($errors) {
        return $json(['ok' => false, 'errors' => $errors], 422);
    }        

    $uploadDir = __DIR__ . '/../assets/uploads/bukti';
    $uploadResult = handleUploads('bukti', $uploadDir);
    if(!$uploadResult["ok"]){
        return $json($uploadResult, 422);
    }

    // Generate ticket code
    $ticket = generateTicket();

    // Query into database with transaction
    try {
        Database::begin();
        $sqlPengaduan = "
            INSERT INTO pengaduan 
            (
                ticket_code,
                kategori_id,
                alamat_lengkap,
                kecamatan_id,
                kelurahan_id,
                kronologi,
                is_anonim,
                nama_pelapor,
                email_pelapor,
                telp_pelapor,
                status,
                assigned_to,
                created_at,
                updated_at
            )
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'masuk', NULL, NOW(), NOW())
        ";

        $stmt = Database::run($sqlPengaduan, [
            $ticket,
            $kategori,
            $alamat_lengkap,
            $id_kecamatan,
            $id_kelurahan,
            $deskripsi,
            $anonim,
            $nama_pelapor,
            $email_pelapor,
            $telp_pelapor
        ]);

        if ($stmt->rowCount() <= 0) {
            throw new Exception("Insert pengaduan gagal");
        }

        if (!empty($uploadResult['filenames'])) {
            $sqlFoto = "INSERT INTO ref_foto_pengaduan (nama_foto, ticket_code) VALUES (?, ?)";
            foreach ($uploadResult['filenames'] as $filename) {
                Database::run($sqlFoto, [$filename, $ticket]);
            }
        }
        Database::commit();
    } catch (Exception $e) {
        // 5️⃣ Rollback if anything failed
        Database::rollback();
        error_log("[" . date('c') . "] Transaction failed: " . $e->getMessage() . PHP_EOL, 3, __DIR__ . '/../logs/error.log');
    }    

    return $json([
        'ok' => true,
        'ticket' => $ticket,
        'anonim' => $anonim,
        'data' => [
            'kategori' => $kategori,
            'id_kecamatan' => $id_kecamatan,
            'id_kelurahan' => $id_kelurahan,
            'alamat_lengkap' => $alamat_lengkap,
            'deskripsi_laporan' => $deskripsi,
            'tanggal_kejadian' => $tanggal_kejadian,
            'nama_pelapor' => $nama_pelapor,
            'email_pelapor' => $email_pelapor,
            'telp_pelapor' => $telp_pelapor,
            'upload_bukti' => $uploadResult["filenames"],
        ]
    ], 201);
}


if ($method === 'GET' && ctype_digit($resource) && (strlen($resource) < 10) ) {
    // api/pengaduan/<ID>
    checkId($resource);
    $data = getPengaduanById((int)$resource);
    echo $data ? json_encode($data) : (http_response_code(404) && json_encode(['error' => 'Pengaduan not found']));

} else if ($method === 'GET' && (strlen($resource) == 10) && isset($_GET["detailed"])) {
    // api/pengaduan/<TICKET_CODE>?detailed=true
    $data = getDetailedPengaduanByTicketCode($resource);
    echo $data ? json_encode($data) : (http_response_code(404) && json_encode(['error' => 'Pengaduan not found']));

} else if ($method === 'GET' && (strlen($resource) == 10)) {
    // api/pengaduan/<TICKET_CODE>
    $data = getPengaduanByTicketCode($resource);
    echo $data ? json_encode($data) : (http_response_code(404) && json_encode(['error' => 'Pengaduan not found']));

} else if ($method === 'GET' && $resource == "all") {
    // api/pengaduan/all
    $limit = $_GET["limit"] ?? 10;
    $page = $_GET["page"] ?? 1;
    $data = getDetailedPengaduanAll($page, $limit);
    echo $data ? json_encode($data) : (http_response_code(404) && json_encode(['error' => 'Pengaduan not found']));

}  else if($method === 'PATCH' && (strlen($resource) == 10) && isset($_GET["verify"])){
    // api/pengaduan/<TICKET_CODE>?verifikasi=true
    verifyPengaduanByTicketCode($resource);

}  else if($method === 'PATCH' && (strlen($resource) == 10) && isset($_GET["reject"])){
    // api/pengaduan/<TICKET_CODE>?reject=true
    rejectPengaduanByTicketCode($resource);
} else if($method === 'PATCH' && (strlen($resource) == 10) && isset($_GET["petugas"])){
    // api/pengaduan/<TICKET_CODE>?petugas=<NUM>
    assignPetugasToPengaduan($resource);

} else if($method === 'POST' && !isset($resource)){
    addPengaduan();

} else {
    http_response_code(400);
    echo json_encode(['error' => 'Bad request']);

}




