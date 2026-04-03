<?php
session_start();

include '../../conexion/conexion.php';
$conexion = conectarServidor();

$filtro_modulo = isset($_GET['modulo']) ? trim($_GET['modulo']) : '';
$filtro_fecha_desde = isset($_GET['fecha_desde']) ? trim($_GET['fecha_desde']) : date('Y-m-d', strtotime('-30 days'));
$filtro_fecha_hasta = isset($_GET['fecha_hasta']) ? trim($_GET['fecha_hasta']) : date('Y-m-d');
$filtro_usuario = isset($_GET['usuario']) ? trim($_GET['usuario']) : '';
$filtro_estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';

$query = "SELECT mf.gcs_uri FROM business_records br LEFT JOIN media_files mf ON mf.id = br.media_file_id WHERE br.media_file_id IS NOT NULL";

if (!empty($filtro_modulo)) {
  $query .= " AND br.modulo = '" . mysqli_real_escape_string($conexion, $filtro_modulo) . "'";
}
if (!empty($filtro_usuario)) {
  $query .= " AND br.from_number = '" . mysqli_real_escape_string($conexion, $filtro_usuario) . "'";
}
if (!empty($filtro_fecha_desde)) {
  $query .= " AND DATE(br.created_at) >= '" . mysqli_real_escape_string($conexion, $filtro_fecha_desde) . "'";
}
if (!empty($filtro_fecha_hasta)) {
  $query .= " AND DATE(br.created_at) <= '" . mysqli_real_escape_string($conexion, $filtro_fecha_hasta) . "'";
}
if (!empty($filtro_estado)) {
  if ($filtro_estado === 'OK') {
    $query .= " AND br.ocr_status = 'DONE' AND br.parse_status = 'OK'";
  } else if ($filtro_estado === 'ERROR') {
    $query .= " AND (br.ocr_status = 'FAILED' OR br.parse_status != 'OK')";
  } else if ($filtro_estado === 'PENDIENTE') {
    $query .= " AND (br.approval_status IS NULL OR br.approval_status = 'PENDING')";
  }
}

$query .= " ORDER BY br.created_at DESC LIMIT 200";

$result = mysqli_query($conexion, $query);

function convert_gcs_url($gcs_uri) {
  if (strpos($gcs_uri, 'gs://') === 0) {
    $gcs_uri = str_replace('gs://', 'https://storage.googleapis.com/', $gcs_uri);
  }
  return $gcs_uri;
}

if (isset($_GET['download']) && $_GET['download'] === 'batch') {
  header('Content-Type: text/plain; charset=utf-8');
  header('Content-Disposition: attachment; filename="imagenes_batch.txt"');
  
  while ($row = mysqli_fetch_assoc($result)) {
    if ($row['gcs_uri']) {
      echo convert_gcs_url($row['gcs_uri']) . "\n";
    }
  }
  exit;
}

mysqli_close($conexion);
?>
