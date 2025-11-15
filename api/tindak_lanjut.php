<?php

$method   = $_SERVER['REQUEST_METHOD'];
$uri      = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($uri, '/'));

$id = $segments[2] ?? null;

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

function getAllTindakLanjut() {
    $query = "SELECT * FROM tindak_lanjut ORDER BY id DESC";
    return Database::fetchAll($query);
}

function getDetailedTindakLanjut($id_tindak_lanjut)
{
    $query = "
        SELECT 
            tl.id AS tindak_id,
            tl.pengaduan_id,
            tl.petugas_id,
            tl.catatan,
            tl.created_at,

            p.ticket_code,
            p.kategori_id,
            p.kecamatan_id,
            p.kelurahan_id,
            p.status,

            u.name  AS petugas_nama,
            u.email AS petugas_email,

            f.nama_foto
        FROM tindak_lanjut AS tl
        LEFT JOIN pengaduan AS p 
               ON tl.pengaduan_id = p.id
        LEFT JOIN users AS u 
               ON tl.petugas_id = u.id
        LEFT JOIN ref_foto_tindak_lanjut AS f 
               ON f.id_tindak_lanjut = tl.id
        WHERE tl.id = :id
    ";
    $param = ["id" => $id_tindak_lanjut];

    $rows = Database::fetchAll($query, $param);

    if (!$rows) {
        return null; // not found
    }

    // Build normalized single record
    $result = [
        'id'             => $rows[0]['tindak_id'],
        'pengaduan_id'   => $rows[0]['pengaduan_id'],
        'petugas_id'     => $rows[0]['petugas_id'],
        'catatan'        => $rows[0]['catatan'],
        'created_at'     => $rows[0]['created_at'],
        'ticket_code'    => $rows[0]['ticket_code'],
        'kategori_id'    => $rows[0]['kategori_id'],
        'kecamatan_id'   => $rows[0]['kecamatan_id'],
        'kelurahan_id'   => $rows[0]['kelurahan_id'],
        'status'         => $rows[0]['status'],
        'petugas_nama'   => $rows[0]['petugas_nama'],
        'petugas_email'  => $rows[0]['petugas_email'],
        'fotos'          => []
    ];

    // Push each foto into array
    foreach ($rows as $r) {
        if (!empty($r['nama_foto'])) {
            $result['fotos'][] = $r['nama_foto'];
        }
    }

    return $result;
}


function getDetailedTindakLanjutByTicketCode($ticket_code)
{
    // Step 1 — get pengaduan_id
    $p = Database::fetch(
        "SELECT id FROM pengaduan WHERE ticket_code = :tc LIMIT 1",
        ["tc" => $ticket_code]
    );

    if (!$p) {
        return null; // ticket not found
    }

    // Step 2 — get tindak_lanjut.id for this pengaduan
    $tl = Database::fetch(
        "SELECT id FROM tindak_lanjut WHERE pengaduan_id = :pid LIMIT 1",
        ["pid" => $p['id']]
    );

    if (!$tl) {
        return null; // no tindak_lanjut exists yet
    }

    // Step 3 — reuse your existing logic (fetchAll JOIN)
    $rows = Database::fetchAll("
        SELECT 
            tl.id AS tindak_id,
            tl.pengaduan_id,
            tl.petugas_id,
            tl.catatan,
            tl.created_at,

            p.ticket_code,
            p.kategori_id,
            p.kecamatan_id,
            p.kelurahan_id,
            p.status,

            u.name AS petugas_nama,
            u.email AS petugas_email,
            f.nama_foto
        FROM tindak_lanjut AS tl
        LEFT JOIN pengaduan AS p 
                ON tl.pengaduan_id = p.id
        LEFT JOIN users AS u 
                ON tl.petugas_id = u.id
        LEFT JOIN ref_foto_tindak_lanjut AS f 
                ON f.id_tindak_lanjut = tl.id
        WHERE tl.id = :id
    ", ["id" => $tl['id']]);

    if (!$rows) return null;

    // Build unified output with photo array
    $result = [
        'id'             => $rows[0]['tindak_id'],
        'pengaduan_id'   => $rows[0]['pengaduan_id'],
        'petugas_id'     => $rows[0]['petugas_id'],
        'catatan'        => $rows[0]['catatan'],
        'created_at'     => $rows[0]['created_at'],
        'ticket_code'    => $rows[0]['ticket_code'],
        'kategori_id'    => $rows[0]['kategori_id'],
        'kecamatan_id'   => $rows[0]['kecamatan_id'],
        'kelurahan_id'   => $rows[0]['kelurahan_id'],
        'status'         => $rows[0]['status'],
        'petugas_nama'   => $rows[0]['petugas_nama'],
        'petugas_email'  => $rows[0]['petugas_email'],
        'fotos'          => []
    ];

    foreach ($rows as $r) {
        if (!empty($r['nama_foto'])) {
            $result['fotos'][] = $r['nama_foto'];
        }
    }

    return $result;
}


function addTindakLanjut($ticket_code)
{
    $catatan    = trim($_POST['catatan'] ?? '');
    $petugas_id = $_POST['petugas_id'] ?? null;

    if ($catatan === '' || !$petugas_id) {
        http_response_code(422);
        echo json_encode([
            'ok'      => false,
            'message' => 'Field catatan dan petugas_id wajib diisi.'
        ]);
        return;
    }

    // 1. Ambil pengaduan_id dari ticket_code
    $pengaduan = Database::fetch(
        "SELECT id, ticket_code FROM pengaduan WHERE ticket_code = :tc LIMIT 1",
        ["tc" => $ticket_code]
    );

    if (!$pengaduan) {
        http_response_code(404);
        echo json_encode([
            'ok'      => false,
            'message' => 'Ticket code tidak ditemukan.'
        ]);
        return;
    }

    $pengaduan_id = (int)$pengaduan['id'];

    // 2. Validasi petugas_id
    $checkPetugas = Database::fetch(
        "SELECT id FROM users WHERE id = :uid LIMIT 1",
        ["uid" => $petugas_id]
    );

    if (!$checkPetugas) {
        http_response_code(404);
        echo json_encode([
            'ok'      => false,
            'message' => 'Petugas tidak ditemukan.'
        ]);
        return;
    }

    // 3. Upload bukti (WAJIB)
    $uploadDir = __DIR__ . '/../assets/uploads/tindak_lanjut';
    $upload = handleUploads('bukti_tindak_lanjut', $uploadDir);

    if (
        !$upload['ok'] ||
        empty($upload['filenames']) ||
        $upload['filenames'] === 'empty'
    ) {
        http_response_code(422);
        echo json_encode([
            'ok'      => false,
            'message' => 'Bukti foto wajib diupload!',
            'errors'  => $upload['errors']
        ], JSON_UNESCAPED_UNICODE);
        return;
    }

    // Pastikan filenames array
    $filenames = is_array($upload['filenames']) ? $upload['filenames'] : [];

    // 4. Mulai transaksi
    try {
        Database::begin();

        // 4.1 Insert ke tindak_lanjut
        $sqlInsert = "
            INSERT INTO tindak_lanjut (pengaduan_id, petugas_id, catatan, created_at)
            VALUES (:pengaduan_id, :petugas_id, :catatan, NOW())
        ";

        Database::run($sqlInsert, [
            "pengaduan_id" => $pengaduan_id,
            "petugas_id"   => $petugas_id,
            "catatan"      => $catatan
        ]);

        $id_tindak_lanjut = (int)Database::pdo()->lastInsertId();

        // 4.2 Insert foto ke ref_foto_tindak_lanjut
        foreach ($filenames as $fn) {
            Database::run("
                INSERT INTO ref_foto_tindak_lanjut (nama_foto, id_tindak_lanjut)
                VALUES (:foto, :id)
            ", [
                "foto" => $fn,
                "id"   => $id_tindak_lanjut
            ]);
        }

        // 4.3 Update status pengaduan -> selesai
        Database::run(
            "UPDATE pengaduan 
             SET status = 'selesai', updated_at = NOW() 
             WHERE id = :id",
            ["id" => $pengaduan_id]
        );

        // 4.4 Commit transaksi
        Database::commit();

        http_response_code(201);
        echo json_encode([
            'ok'               => true,
            'message'          => 'Tindak lanjut berhasil ditambahkan.',
            'ticket_code'      => $ticket_code,
            'pengaduan_id'     => $pengaduan_id,
            'id_tindak_lanjut' => $id_tindak_lanjut,
            'uploaded_files'   => $upload
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        // Rollback DB
        Database::rollback();

        // Hapus file yang sudah terupload (supaya konsisten)
        if (!empty($filenames)) {
            foreach ($filenames as $fn) {
                $path = rtrim($uploadDir, '/') . '/' . $fn;
                if (is_file($path)) {
                    @unlink($path);
                }
            }
        }

        http_response_code(500);
        echo json_encode([
            'ok'      => false,
            'message' => 'Terjadi kesalahan saat menyimpan tindak lanjut.',
            'error'   => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}



if ($method === 'GET' && $segments[1] === 'tindak_lanjut' && $segments[2] === 'ticket') {
    // GET /api/tindak_lanjut/ticket/{ticket_id}
    $ticket = $segments[3] ?? null;
    $data = getDetailedTindakLanjutByTicketCode($ticket);
    echo $data ? json_encode($data) 
               : (http_response_code(404) && json_encode(['error'=>'Tindak lanjut not found']));
}
else if ($method === 'GET' && $id) {
    // GET /api/tindak_lanjut/{id}
    checkId($id);
    $data = getDetailedTindakLanjut((int)$id);
    echo $data ? json_encode($data)
               : (http_response_code(404) && json_encode(['error' => 'Tindak lanjut tidak ditemukan']));
}
else if ($method === 'GET') {
    // GET /api/tindak_lanjut
    $data = getAllTindakLanjut();
    echo json_encode($data);
}
else if ($method === 'POST' && $segments[1] === 'tindak_lanjut' && $segments[2] === 'ticket' ) {
    // POST /api/tindak_lanjut/ticket/{ticket_code}
    $ticket_code = $segments[3] ?? null;
    addTindakLanjut($ticket_code);
}
else {
    http_response_code(400);
    echo json_encode(['error' => 'Bad request']);
}

?>
