<?php
$headers = getallheaders();
header('Content-Type: application/json; charset=utf-8');

if (!isset($headers['Authorization']) || $headers['Authorization'] !== 'Bearer MY_SUPER_SECRET_TOKEN') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . "/../config/database.php";

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

$segments = explode('/', $uri);

function sanitize($content){
    $sanitized = trim($content);
    $sanitized = strip_tags($content);
    $sanitized = htmlspecialchars($content, ENT_QUOTES, "UTF-8");
    return $sanitized;
}

function checkResource($result)
{
    if($result){
        return $result;
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Resource not found']);
    }
}

function checkId($id){
    if (!ctype_digit($id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid ID']);
        exit;
    } else {
        return $id;
    }
}


// Basic routing
if ($segments[0] === 'api' && isset($segments[1])) {
    $resource = $segments[1];

    switch ($resource) {
        case 'kelurahan':
            require __DIR__ . '/kelurahan.php';
            break;

        case 'pengaduan':
            require __DIR__ . '/pengaduan.php';
            break;

        case 'users':
            require __DIR__ . '/users.php';
            break;

        case 'auth':
            require __DIR__ . '/auth.php';
            break;

        case 'tindak_lanjut':
        require __DIR__ . '/tindak_lanjut.php';
        break;            

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Resource not found']);
            break;
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Invalid API endpoint']);
}
