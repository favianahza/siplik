<?php
if (basename(__FILE__) == basename($_SERVER["PHP_SELF"])) {
    http_response_code(403);
    exit("Access denied.");
}

if (!defined('BASE_URL')) {
    define('BASE_URL', 'https://app.faps.my.id');
}

$components = __DIR__ . '/../components/';
$styles = __DIR__ . '/../styles/';

$navbar = $components ."navbar.php";
$footer = $components . "footer.php";
$modal = $components . "modal.php";
$modal_dashboard = $components . "modal_dashboard.php";
$loader = $components . "loader.php";
$ringkasan = $components . "ringkasan.php";

function secureSessionStart(): void
{
    $session_name = 'SIPLIK_SESSION';
    $secure   = isset($_SERVER['HTTPS']);  // only true if HTTPS
    $httponly = true;

    session_name($session_name);

    session_set_cookie_params([
        'lifetime' => 0,              // expires when browser closes
        'path'     => '/',
        'domain'   => '',             // default current host
        'secure'   => $secure,
        'httponly' => $httponly,
        'samesite' => 'Strict'
    ]);

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

?>

