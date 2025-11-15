<?php
require_once __DIR__ . "/../config/init.php";
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($uri, '/'));

$action = $segments[2] ?? null;

function loginUser() {
    header('Content-Type: application/json; charset=utf-8');

    // --- JSON helper for clean output ---
    $json = fn($arr, $code = 200) => (http_response_code($code) || true) && print(json_encode($arr, JSON_UNESCAPED_UNICODE));

    try {
        // --- Step 1: Sanitize input ---
        $sanitize = fn($v) => htmlspecialchars(trim((string)$v), ENT_QUOTES, 'UTF-8');

        $email    = trim($_POST['login-email'] ?? '');
        $password = $_POST['login-password'] ?? '';
        $remember = isset($_POST['login-remember']) && $_POST['login-remember'] === 'on';

        // --- Step 2: Validate input ---
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $json([
                'ok'      => false,
                'message' => 'Format email yang dimasukan tidak valid!',
                'errors'  => ['email' => 'Invalid format!']
            ], 422);
        }

        if ($password === '') {
            return $json([
                'ok'      => false,
                'message' => 'Password wajib diisi!',
                'errors'  => ['password' => 'Empty!']
            ], 422);
        }

        // --- Step 3: Fetch user record ---
        $sql = "SELECT id, name, email, password_hash, role, is_active FROM users WHERE email = ? LIMIT 1";
        $user = Database::run($sql, [$email])->fetch();

        if (!$user || !$user['is_active']) {
            return $json([
                'ok' => false,
                'message' => 'Email atau Password yang dimasukan salah!',
                'errors' => ['auth' => 'Invalid credentials!']
            ], 401);
        }

        // --- Step 4: Verify password ---
        if (!password_verify($password, $user['password_hash'])) {
            return $json([
                'ok' => false,
                'message' => 'Email atau Password yang dimasukan salah!',
                'errors' => ['auth' => 'Invalid credentials!']
            ], 401);
        }

        // --- Step 5: Secure session setup ---
        secureSessionStart();
        session_regenerate_id(true);

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        $_SESSION['last_activity'] = time();

        // --- Step 6: Handle remember-me cookie ---
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expires = time() + (86400 * 30); // 30 days

            // Set secure cookie
            setcookie(
                'remember_me',
                $user['id'] . ':' . $token,
                [
                    'expires'  => $expires,
                    'path'     => '/',
                    'secure'   => true,   // only send over HTTPS
                    'httponly' => true,   // not accessible via JS
                    'samesite' => 'Strict'
                ]
            );
        }

        return $json([
            'ok' => true,
            'message' => 'Login berhasil!',
            'data' => [
                'id'    => $user['id'],
                'name'  => $user['name'],
                'email' => $user['email'],
                'role'  => $user['role']
            ]
        ], 200);

    } catch (Exception $e) {
        error_log("[" . date('c') . "] authUser() failed: " . $e->getMessage() . PHP_EOL, 3, __DIR__ . '/../logs/error.log');

        return $json([
            'ok' => false,
            'error' => 'Terjadi kesalahan pada server.',
            'detail' => $e->getMessage()
        ], 500);
    }
}

function logoutUser(){
    header('Content-Type: application/json; charset=utf-8');

    $json = fn($arr, $code = 200) => (http_response_code($code) || true) && print(json_encode($arr, JSON_UNESCAPED_UNICODE));

    try {
        secureSessionStart();

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                [
                    'expires'  => time() - 3600, // expire now
                    'path'     => $params['path'],
                    'domain'   => $params['domain'],
                    'secure'   => $params['secure'],
                    'httponly' => $params['httponly'],
                    'samesite' => $params['samesite'] ?? 'Lax'
                ]
            );
        }

        session_destroy();

        if (isset($_COOKIE['remember_me'])) {
            setcookie('remember_me', '', [
                'expires'  => time() - 3600,
                'path'     => '/',
                'secure'   => true,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }

        return $json([
            'ok'      => true,
            'message' => 'Logout berhasil. Anda telah keluar dari sesi.'
        ], 200);

    } catch (Exception $e) {
        error_log("[" . date('c') . "] logoutUser() failed: " . $e->getMessage() . PHP_EOL, 3, __DIR__ . '/../logs/error.log');

        return $json([
            'ok' => false,
            'error' => 'Gagal melakukan logout.',
            'detail' => $e->getMessage()
        ], 500);
    }
}

if ($method === 'POST' && $action == "login") {
    loginUser();
} else if ($method === 'POST' && $action == "logout") {
    logoutUser();
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Bad request']);
}

