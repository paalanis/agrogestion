<?php
session_start();

$db_host = $_SESSION['db_host'] ?? 'localhost';
$db_user = $_SESSION['db_user'] ?? '';
$db_pass = $_SESSION['db_pass'] ?? '';
$database = $_SESSION['database'] ?? '';

$conexion = mysqli_connect($db_host, $db_user, $db_pass, $database);
if (!$conexion) {
  die("ERROR: " . mysqli_connect_error());
}
mysqli_set_charset($conexion, "utf8mb4");

$filtro_modulo = isset($_GET['modulo']) ? trim($_GET['modulo']) : '';
$filtro_fecha_desde = isset($_GET['fecha_desde']) ? trim($_GET['fecha_desde']) : date('Y-m-d', strtotime('-30 days'));
$filtro_fecha_hasta = isset($_GET['fecha_hasta']) ? trim($_GET['fecha_hasta']) : date('Y-m-d');
$filtro_usuario = isset($_GET['usuario']) ? trim($_GET['usuario']) : '';
$filtro_estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';

// Módulos
$query_modulos = "SELECT DISTINCT modulo FROM business_records WHERE modulo IS NOT NULL ORDER BY modulo";
$result_modulos = mysqli_query($conexion, $query_modulos);
$modulos = [];
while ($row = mysqli_fetch_assoc($result_modulos)) {
  $modulos[] = $row['modulo'];
}

// Usuarios
$query_usuarios = "SELECT DISTINCT from_number FROM business_records ORDER BY from_number";
$result_usuarios = mysqli_query($conexion, $query_usuarios);
$usuarios = [];
while ($row = mysqli_fetch_assoc($result_usuarios)) {
  $usuarios[] = $row['from_number'];
}

// Query
$query = "SELECT br.id, br.from_number, br.modulo, br.numero_doc, br.batch_id, br.ocr_status, br.parse_status, br.approval_status, mf.gcs_uri, br.created_at FROM business_records br LEFT JOIN media_files mf ON mf.id = br.media_file_id WHERE br.media_file_id IS NOT NULL";

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
$imagenes = [];
while ($row = mysqli_fetch_assoc($result)) {
  $imagenes[] = $row;
}

// Función para convertir gs:// a https://
function convert_gcs_url($gcs_uri) {
  if (strpos($gcs_uri, 'gs://') === 0) {
    $gcs_uri = str_replace('gs://', 'https://storage.googleapis.com/', $gcs_uri);
  }
  return $gcs_uri;
}

// Convertir URLs
foreach ($imagenes as &$img) {
  if (!empty($img['gcs_uri'])) {
    $img['gcs_uri'] = convert_gcs_url($img['gcs_uri']);
  }
}
unset($img);

mysqli_close($conexion);
?>

<div class="x_panel">
  <div class="x_title">
    <h2>📸 Imágenes del Bot WhatsApp</h2>
    <div class="clearfix"></div>
  </div>
  <div class="x_content">

    <!-- FILTROS -->
    <div class="row" style="margin-bottom:20px;">
      <div class="col-md-12">
        <form id="form_filtros_imagenes" class="form-inline">
          <div class="form-group" style="margin-right:15px;">
            <label style="margin-right:8px;">Módulo:</label>
            <select name="modulo" class="form-control" style="width:180px;">
              <option value="">-- Todos --</option>
              <?php foreach ($modulos as $mod): ?>
                <option value="<?php echo htmlspecialchars($mod); ?>" <?php echo $filtro_modulo === $mod ? 'selected' : ''; ?>><?php echo strtoupper($mod); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group" style="margin-right:15px;">
            <label style="margin-right:8px;">Usuario:</label>
            <select name="usuario" class="form-control" style="width:180px;">
              <option value="">-- Todos --</option>
              <?php foreach ($usuarios as $usr): ?>
                <option value="<?php echo htmlspecialchars($usr); ?>" <?php echo $filtro_usuario === $usr ? 'selected' : ''; ?>><?php echo $usr; ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group" style="margin-right:15px;">
            <label style="margin-right:8px;">Desde:</label>
            <input type="date" name="fecha_desde" class="form-control" value="<?php echo $filtro_fecha_desde; ?>" style="width:150px;">
          </div>

          <div class="form-group" style="margin-right:15px;">
            <label style="margin-right:8px;">Hasta:</label>
            <input type="date" name="fecha_hasta" class="form-control" value="<?php echo $filtro_fecha_hasta; ?>" style="width:150px;">
          </div>

          <div class="form-group" style="margin-right:15px;">
            <label style="margin-right:8px;">Estado:</label>
            <select name="estado" class="form-control" style="width:150px;">
              <option value="">-- Todos --</option>
              <option value="OK" <?php echo $filtro_estado === 'OK' ? 'selected' : ''; ?>>✓ OK</option>
              <option value="ERROR" <?php echo $filtro_estado === 'ERROR' ? 'selected' : ''; ?>>✗ Error</option>
              <option value="PENDIENTE" <?php echo $filtro_estado === 'PENDIENTE' ? 'selected' : ''; ?>>⏳ Pendiente</option>
            </select>
          </div>

          <button type="button" class="btn btn-primary" onclick="filtrar_imagenes()">🔍 Filtrar</button>
          <button type="button" class="btn btn-default" onclick="limpiar_imagenes()">🔄 Limpiar</button>
        </form>
      </div>
    </div>

    <!-- ESTADÍSTICAS -->
    <div class="alert alert-info" style="margin-bottom:20px;">
      <strong>📊 Resultados:</strong> 
      <span style="margin-left:20px;">
        <strong><?php echo count($imagenes); ?></strong> imágenes encontradas
      </span>
      <span style="margin-left:30px;">
        <i class="fa fa-check" style="color:#26B99A;"></i> OK: <strong><?php echo count(array_filter($imagenes, function($i) { return $i['ocr_status'] === 'DONE' && $i['parse_status'] === 'OK'; })); ?></strong>
      </span>
      <span style="margin-left:30px;">
        <i class="fa fa-exclamation-triangle" style="color:#E74C3C;"></i> Error: <strong><?php echo count(array_filter($imagenes, function($i) { return $i['ocr_status'] === 'FAILED' || $i['parse_status'] !== 'OK'; })); ?></strong>
      </span>
      <span style="margin-left:30px;">
        <i class="fa fa-clock-o" style="color:#E8A838;"></i> Pendiente: <strong><?php echo count(array_filter($imagenes, function($i) { return $i['approval_status'] === null || $i['approval_status'] === 'PENDING'; })); ?></strong>
      </span>
      <div style="margin-top:10px;">
        <button type="button" class="btn btn-success btn-xs" onclick="descargar_batch_imagenes()">📥 Descargar URLs (Batch)</button>
      </div>
    </div>

    <!-- GALERÍA DE IMÁGENES -->
    <?php if (empty($imagenes)): ?>
      <div class="alert alert-warning">
        <strong>⚠ Sin resultados:</strong> No hay imágenes con los filtros aplicados.
      </div>
    <?php else: ?>
      <div class="row" style="margin-left:0; margin-right:0;">
        <?php foreach ($imagenes as $img): ?>
          <div class="col-md-6 col-lg-4" style="margin-bottom:30px;">
            <div class="x_panel" style="height:100%; display:flex; flex-direction:column;">
              <div class="x_content" style="padding:0; flex:1; display:flex; flex-direction:column;">
                
                <!-- Imagen -->
                <div style="width:100%; height:250px; background:#f5f5f5; border:1px solid #ddd; display:flex; align-items:center; justify-content:center; overflow:hidden; margin-bottom:15px;">
                  <?php if ($img['gcs_uri']): ?>
                    <img src="<?php echo htmlspecialchars($img['gcs_uri']); ?>" style="width:100%; height:100%; object-fit:cover;" alt="Imagen">
                  <?php else: ?>
                    <span style="color:#999;">Sin imagen</span>
                  <?php endif; ?>
                </div>

                <!-- Información -->
                <div style="padding:0 15px;">
                  <p style="margin:8px 0;"><strong>📍 Módulo:</strong> <span style="color:#2A3F5F; font-weight:bold;"><?php echo strtoupper($img['modulo'] ?? 'N/A'); ?></span></p>
                  <p style="margin:8px 0;"><strong>👤 Usuario:</strong> <?php echo $img['from_number']; ?></p>
                  <p style="margin:8px 0;"><strong>📄 Parte:</strong> <?php echo $img['numero_doc'] ?? 'N/A'; ?></p>
                  <p style="margin:8px 0;"><strong>📦 Batch:</strong> <small style="color:#666;"><?php echo substr($img['batch_id'] ?? 'N/A', 0, 30); ?></small></p>
                  <p style="margin:8px 0;"><strong>📅 Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($img['created_at'])); ?></p>

                  <!-- Badges -->
                  <div style="margin:12px 0;">
                    <?php if ($img['ocr_status'] === 'DONE' && $img['parse_status'] === 'OK'): ?>
                      <span class="badge" style="background:#26B99A; color:white;">✓ OK</span>
                    <?php elseif ($img['ocr_status'] === 'FAILED'): ?>
                      <span class="badge" style="background:#E74C3C; color:white;">✗ <?php echo $img['ocr_status']; ?></span>
                    <?php else: ?>
                      <span class="badge" style="background:#E8A838; color:white;">⏳ <?php echo $img['ocr_status'] ?? 'PROCESANDO'; ?></span>
                    <?php endif; ?>

                    <?php if ($img['approval_status'] === 'APPROVED'): ?>
                      <span class="badge" style="background:#26B99A; color:white;">✓ Aprobado</span>
                    <?php elseif ($img['approval_status'] === 'REJECTED'): ?>
                      <span class="badge" style="background:#E74C3C; color:white;">✗ Rechazado</span>
                    <?php endif; ?>
                  </div>

                  <!-- Botón Descargar -->
                  <?php if ($img['gcs_uri']): ?>
                    <a href="<?php echo htmlspecialchars($img['gcs_uri']); ?>" download class="btn btn-primary btn-sm btn-block" style="margin-top:10px;">⬇️ Descargar imagen</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</div>

<script>
function filtrar_imagenes() {
  var modulo = $('select[name="modulo"]').val();
  var usuario = $('select[name="usuario"]').val();
  var fecha_desde = $('input[name="fecha_desde"]').val();
  var fecha_hasta = $('input[name="fecha_hasta"]').val();
  var estado = $('select[name="estado"]').val();

  var params = '?modulo=' + encodeURIComponent(modulo) + 
               '&usuario=' + encodeURIComponent(usuario) + 
               '&fecha_desde=' + encodeURIComponent(fecha_desde) + 
               '&fecha_hasta=' + encodeURIComponent(fecha_hasta) + 
               '&estado=' + encodeURIComponent(estado);

  $("#panel_inicio").html('<div class="text-center"><div class="loadingsm"></div></div>');
  $('#panel_inicio').load("abm/reporte/bot-imagenes.php" + params);
}

function limpiar_imagenes() {
  $("#panel_inicio").html('<div class="text-center"><div class="loadingsm"></div></div>');
  $('#panel_inicio').load("abm/reporte/bot-imagenes.php");
}

function descargar_batch_imagenes() {
  var modulo = $('select[name="modulo"]').val();
  var usuario = $('select[name="usuario"]').val();
  var fecha_desde = $('input[name="fecha_desde"]').val();
  var fecha_hasta = $('input[name="fecha_hasta"]').val();
  var estado = $('select[name="estado"]').val();

  var params = 'abm/reporte/bot-imagenes-download.php?download=batch&modulo=' + encodeURIComponent(modulo) + 
               '&usuario=' + encodeURIComponent(usuario) + 
               '&fecha_desde=' + encodeURIComponent(fecha_desde) + 
               '&fecha_hasta=' + encodeURIComponent(fecha_hasta) + 
               '&estado=' + encodeURIComponent(estado);

  window.location.href = params;
}
</script>
