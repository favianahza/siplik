<?php

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($uri, '/'));

$id = $segments[2] ?? null;
$kecamatan_id = $_GET["kecamatan_id"] ?? null;

function getKelurahanById($id) {
    $query = "SELECT id, nama FROM ref_kelurahan WHERE id = :id";
    $param = ["id" => $id];
    return Database::fetchAll($query, $param);
}

function getKelurahanByKecamatanId($kecamatan_id) {
    $query = "SELECT id, nama FROM ref_kelurahan WHERE kecamatan_id = :kecamatan_id";
    $param = ["kecamatan_id" => $kecamatan_id];
    return Database::fetchAll($query, $param);
}

if ($method === 'GET' && $id) {
    checkId($id);
    $data = getKelurahanById((int)$id);
    echo $data ? json_encode($data) : (http_response_code(404) && json_encode(['error' => 'Kelurahan not found']));
} else if($method === 'GET' && $kecamatan_id) {
    checkId($kecamatan_id);
    $data = getKelurahanByKecamatanId((int)$kecamatan_id);
    echo $data ? json_encode($data) : (http_response_code(404) && json_encode(['error' => 'Kelurahan not found']));
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Bad request']);
}

