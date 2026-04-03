<?php
/**
 * bot-imagenes-proxy.php
 * Descarga archivos desde GCS privado (sin redirigir)
 */

session_start();
if (!isset($_SESSION['usuario'])) {
  http_response_code(403);
  die("Acceso denegado");
}

if (empty($_GET['gcs_uri'])) {
  http_response_code(400);
  die("Parámetro gcs_uri requerido");
}

$gcs_uri = $_GET['gcs_uri'];

if (strpos($gcs_uri, 'gs://') !== 0) {
  http_response_code(400);
  die("URI inválido");
}

$BOT_API_URL = 'https://wapp-webhook-production.up.railway.app';
$ADMIN_API_KEY = $_SESSION['admin_key'] ?? '';

if (empty($ADMIN_API_KEY)) {
  http_response_code(500);
  die("Error: credenciales no disponibles");
}

try {
  $endpoint = $BOT_API_URL . "/gcs/signed-url?" . http_build_query(['gcs_uri' => $gcs_uri]);
  
  $ch = curl_init($endpoint);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Admin-Key: " . $ADMIN_API_KEY]);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
  
  $response = curl_exec($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $curl_error = curl_error($ch);
  curl_close($ch);
  
  if (!empty($curl_error)) {
    throw new Exception("curl: $curl_error");
  }
  
  if ($http_code !== 200) {
    throw new Exception("Bot HTTP $http_code");
  }
  
  $data = json_decode($response, true);
  
  if (!isset($data['ok']) || !$data['ok']) {
    throw new Exception($data['error'] ?? 'Error desconocido');
  }
  
  if (!isset($data['signed_url'])) {
    throw new Exception("No signed_url en respuesta");
  }
  
  // Descargar archivo desde signed URL
  $file_ch = curl_init($data['signed_url']);
  curl_setopt($file_ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($file_ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($file_ch, CURLOPT_SSL_VERIFYPEER, true);
  curl_setopt($file_ch, CURLOPT_FOLLOWLOCATION, true);
  
  $file_content = curl_exec($file_ch);
  $file_http_code = curl_getinfo($file_ch, CURLINFO_HTTP_CODE);
  $file_content_type = curl_getinfo($file_ch, CURLINFO_CONTENT_TYPE);
  curl_close($file_ch);
  
  if ($file_http_code !== 200 || empty($file_content)) {
    throw new Exception("No se pudo descargar el archivo de GCS");
  }
  
  // Extraer nombre del archivo
  $filename = basename(parse_url($gcs_uri, PHP_URL_PATH));
  $ext = pathinfo($filename, PATHINFO_EXTENSION);
  
  // Si no tiene extensión, inferir del Content-Type
  if (empty($ext)) {
    if (strpos($file_content_type, 'image/jpeg') !== false) $ext = 'jpg';
    elseif (strpos($file_content_type, 'image/png') !== false) $ext = 'png';
    elseif (strpos($file_content_type, 'image/webp') !== false) $ext = 'webp';
    elseif (strpos($file_content_type, 'application/pdf') !== false) $ext = 'pdf';
    else $ext = 'jpg'; // Default
  }
  
  // Nombre amigable: MODULO_NUMERO_PARTE.ext
  $modulo = $_GET['modulo'] ?? 'ARCHIVO';
  $numero_doc = $_GET['numero_doc'] ?? '';
  $friendly_name = $modulo;
  if (!empty($numero_doc)) {
    $friendly_name .= '_' . $numero_doc;
  }
  $friendly_name .= '.' . $ext;
  
  // Servir descarga con nombre amigable
  header("Content-Type: " . ($file_content_type ?: 'application/octet-stream'));
  header("Content-Length: " . strlen($file_content));
  header("Content-Disposition: attachment; filename=\"" . $friendly_name . "\"");
  header("Cache-Control: no-cache, no-store, must-revalidate");
  
  echo $file_content;
  exit;
  
} catch (Exception $e) {
  http_response_code(500);
  error_log("bot-imagenes-proxy.php: " . $e->getMessage());
  die("Error: " . htmlspecialchars($e->getMessage()));
}
