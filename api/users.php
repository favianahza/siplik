<?php
require_once __DIR__ . "/../config/init.php";
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($uri, '/'));

$resource = $segments[2] ?? null;


function getUserById($id) {
    $query = "SELECT * FROM users WHERE id = :id";
    $param = ["id" => $id];
    return Database::fetchAll($query, $param);
}

function getPetugasLapangan()
{
    $json = fn($arr, $code=200) => (http_response_code($code) || true) && print(json_encode($arr, JSON_UNESCAPED_UNICODE));
    $query = "SELECT 
            u.id AS user_id,
            u.name AS nama_petugas,
            u.email AS email_petugas,
            COUNT(p.id) AS total_pengaduan
        FROM users AS u LEFT JOIN pengaduan AS p ON u.id = p.assigned_to WHERE u.role = 'Petugas Lapangan' GROUP BY u.id, u.name, u.email";
    $result = Database::fetchAll($query);
    return $json($result);
}


function addUsers() {
    header('Content-Type: application/json; charset=utf-8');
    $json = fn($arr, $code=200) => (http_response_code($code) || true) && print(json_encode($arr, JSON_UNESCAPED_UNICODE));
    $sanitize = fn($s) => htmlspecialchars(trim((string)$s), ENT_QUOTES, 'UTF-8');

    try {
        $fullname = $sanitize($_POST['fullname'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = $sanitize($_POST['role'] ?? '');

        // --- Validation ---
        if ($fullname === '') {
            return $json([
                'ok'      => false,
                'message' => 'Silahkan isi nama secara lengkap!',
                'errors'  => [
                    'fullname' => 'Nama tidak boleh kosong!'
                ]
            ], 422);
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $json([
                'ok'      => false,
                'message' => 'Format email yang dimasukan tidak valid!',
                'errors'  => [
                    'email' => 'Email tidak valid!'
                ]
            ], 422);
        }

        if ($password === '') {
            return $json([
                'ok'      => false,
                'message' => 'Password wajib diisi!',
                'errors'  => [
                    'password' => 'Password tidak boleh kosong!'
                ]
            ], 422);
        }

        if (strlen($password) < 6) {
            return $json([
                'ok'      => false,
                'message' => 'Password minimal terdiri dari 6 karakter!',
                'errors'  => [
                    'password' => 'Password terlalu pendek!'
                ]
            ], 422);
        }

        if ($role === '') {
            return $json([
                'ok'      => false,
                'message' => 'Role wajib dipilih!',
                'errors'  => [
                    'role' => 'Role tidak valid!'
                ]
            ], 422);
        }

        $sqlCheck = "SELECT COUNT(*) AS cnt FROM users WHERE email = ?";
        $check = Database::run($sqlCheck, [$email])->fetch();

        if ($check && (int)$check['cnt'] > 0) {
            return $json([
                'ok' => false,
                'message' => 'Email yang dimasukan sudah terdaftar!',
                'errors' => ['email' => 'Email sudah terdaftar']
            ], 409); // 409 Conflict
        }
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        Database::begin();

        $sql = "
            INSERT INTO users
            (name, email, password_hash, role, is_active, created_at)
            VALUES (?, ?, ?, ?, 1, NOW())
        ";

        $stmt = Database::run($sql, [
            $fullname,
            $email,
            $password_hash,
            $role
        ]);

        if ($stmt->rowCount() <= 0) {
            throw new Exception("Gagal menambahkan pengguna baru");
        }

        Database::commit();

        return $json([
            'ok' => true,
            'message' => 'Pengguna berhasil ditambahkan',
            'data' => [
                'fullname' => $fullname,
                'email'    => $email,
                'role'     => $role
            ]
        ], 201);

    } catch (Exception $e) {
        Database::rollback();
        error_log(
            "[" . date('c') . "] addUsers() failed: " . $e->getMessage() . PHP_EOL, 3, __DIR__ . '/../logs/error.log'
        );

        return $json([
            'ok' => false,
            'error' => 'Gagal menambahkan pengguna. Silakan coba lagi.',
            'detail' => $e->getMessage() // optional; remove in production
        ], 500);
    }
}

function changeUserPassword($id)
{
    header('Content-Type: application/json; charset=utf-8');

    // --- Unified JSON output helper ---
    $json = fn($arr, $code = 200) => (http_response_code($code) || true) && print(json_encode($arr, JSON_UNESCAPED_UNICODE));

    try {
        // --- Step 1: Sanitize inputs ---
        $sanitize = fn($v) => htmlspecialchars(trim((string)$v), ENT_QUOTES, 'UTF-8');

        $id = $sanitize($id);
        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';

        // --- Step 2: Validate inputs ---
        if ($old_password === '' || $new_password === '') {
            return $json([
                'ok' => false,
                'message' => 'Password lama dan password baru wajib diisi!',
                'errors' => ['password' => 'Empty field']
            ], 422);
        }

        if (strlen($new_password) < 6) {
            return $json([
                'ok' => false,
                'message' => 'Password baru minimal 6 karakter!',
                'errors' => ['new_password' => 'Too short']
            ], 422);
        }

        // --- Step 3: Fetch existing user ---
        $sqlCheck = "SELECT id, password_hash FROM users WHERE id = :id LIMIT 1";
        $user = Database::fetch($sqlCheck, ['id' => $id]);

        if (!$user) {
            return $json([
                'ok' => false,
                'message' => 'Akun tidak ditemukan!',
                'errors' => ['id' => 'User not found']
            ], 404);
        }

        // --- Step 4: Verify old password ---
        if (!password_verify($old_password, $user['password_hash'])) {
            return $json([
                'ok' => false,
                'message' => 'Password lama yang dimasukkan salah!',
                'errors' => ['old_password' => 'Incorrect']
            ], 401);
        }

        // --- Step 5: Hash new password securely ---
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);

        // --- Step 6: Update password ---
        $sqlUpdate = "UPDATE users SET password_hash = :new_hash WHERE id = :id";
        Database::run($sqlUpdate, [
            'new_hash' => $new_hash,
            'id' => $user['id']
        ]);

        // --- Step 7: Return success ---
        return $json([
            'ok' => true,
            'message' => 'Password berhasil diperbarui!'
        ], 200);

    } catch (Exception $e) {
        error_log(
            "[" . date('c') . "] changeUserPassword() failed: " . $e->getMessage() . PHP_EOL,
            3,
            __DIR__ . '/../logs/error.log'
        );

        return $json([
            'ok' => false,
            'error' => 'Terjadi kesalahan saat memperbarui password.',
            'detail' => $e->getMessage()
        ], 500);
    }
}

function getPetugasLapanganStatus() {
    $query = "SELECT 
    u.id AS petugas_id,
    u.name AS petugas_nama,

    -- Aduan yang sedang dikerjakan (status != selesai)
    SUM(CASE WHEN p.status IN ('masuk','diverifikasi','diproses') 
             AND p.assigned_to = u.id 
             THEN 1 ELSE 0 END) AS tugas_aktif,

    -- Aduan selesai berdasarkan tindak lanjut
    SUM(CASE WHEN p.status = 'selesai' 
             AND p.assigned_to = u.id 
             THEN 1 ELSE 0 END) AS tugas_selesai

    FROM users u
    LEFT JOIN pengaduan p 
       ON p.assigned_to = u.id
    WHERE u.role = 'Petugas Lapangan'
    GROUP BY u.id, u.name
    ORDER BY tugas_aktif DESC;";

    return Database::fetchAll($query);
}

if ($method === 'GET' && is_numeric($resource)) {
    // api/users/<ID>
    $id = $resource;
    checkId($id);
    $data = getUserById((int)$id);
    echo $data ? json_encode($data) : (http_response_code(404) && json_encode(['error' => 'User not found']));

} else if($method === 'GET' && $resource == "petugas" && isset($_GET["status"])) {
    // api/users/petugas?status
    $data = getPetugasLapanganStatus();
    echo $data ? json_encode($data) : (http_response_code(404) && json_encode(['error' => 'User not found']));

}    
    else if($method === 'GET' && $resource == "petugas" ){
    // api/users/petugas
    getPetugasLapangan();

} else if($method === 'POST' && $resource) {
    // api/users/<EMAIL>
    $email = $resource;
    changeUserPassword($email);

} else if($method === 'POST'){
    // api/users
    addUsers();
    
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Bad request']);

}

