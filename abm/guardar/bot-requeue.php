<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit();
}

$id  = $_REQUEST['id'] ?? '';
$key = $_SESSION['admin_key'] ?? '';

if (!$id || !$key) {
    echo json_encode(['ok' => false, 'error' => 'Parámetros faltantes']);
    exit();
}

$url = 'https://wapp-webhook-production.up.railway.app/admin/requeue-ocr/' . urlencode($id);

$ctx = stream_context_create([
    'http' => [
        'method'  => 'POST',
        'header'  => "x-admin-key: $key\r\nContent-Length: 0\r\n",
        'timeout' => 15,
    ]
]);

$response = @file_get_contents($url, false, $ctx);

if ($response === false) {
    echo json_encode(['ok' => false, 'error' => 'Error al conectar con la API']);
    exit();
}

echo $response;
