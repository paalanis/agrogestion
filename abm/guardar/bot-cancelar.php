<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => 'false', 'error' => 'No autorizado']);
    exit();
}

$idempotency_key    = $_REQUEST['idempotency_key'] ?? '';
$id_parte_global    = $_REQUEST['id_parte_diario_global'] ?? '';

if (!$idempotency_key || !$id_parte_global) {
    echo json_encode(['success' => 'false', 'error' => 'Parámetros faltantes']);
    exit();
}

include '../../conexion/conexion.php';
$conexion = conectarServidor();

if (mysqli_connect_errno()) {
    echo json_encode(['success' => 'false', 'error' => 'Error de conexión']);
    exit();
}

$ik  = mysqli_real_escape_string($conexion, $idempotency_key);
$idg = mysqli_real_escape_string($conexion, $id_parte_global);

// 1. Verificar que el parte esté APPROVED+OK antes de cancelar
$sqlCheck = "SELECT approval_status, insert_status FROM business_records WHERE idempotency_key='$ik'";
$rsCheck  = mysqli_query($conexion, $sqlCheck);
$row      = mysqli_fetch_assoc($rsCheck);

if (!$row || $row['approval_status'] !== 'APPROVED' || $row['insert_status'] !== 'OK') {
    echo json_encode(['success' => 'false', 'error' => 'El parte no está en estado cancelable']);
    exit();
}

// 2. Marcar como cancelado en business_records
$sqlUpdate = "UPDATE business_records
              SET approval_status='CANCELLED', insert_status='CANCELLED'
              WHERE idempotency_key='$ik'";
mysqli_query($conexion, $sqlUpdate);

// 3. Borrar filas de tb_parte_diario_test
$sqlDelete = "DELETE FROM tb_parte_diario_test WHERE id_parte_diario_global='$idg'";
mysqli_query($conexion, $sqlDelete);

echo json_encode(['success' => 'true']);
