<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../../../index.php");
}
if ($_SESSION['id_finca_usuario'] == '0') {
  session_destroy();
  header("Location: ../../../index.php");
}

include '../../conexion/conexion.php';
$conexion = conectarServidor();

// Parámetros de filtro
$filtro_modulo = isset($_GET['modulo']) ? trim($_GET['modulo']) : '';
$filtro_fecha_desde = isset($_GET['fecha_desde']) ? trim($_GET['fecha_desde']) : date('Y-m-d', strtotime('-30 days'));
$filtro_fecha_hasta = isset($_GET['fecha_hasta']) ? trim($_GET['fecha_hasta']) : date('Y-m-d');
$filtro_usuario = isset($_GET['usuario']) ? trim($_GET['usuario']) : '';
$filtro_estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';

// Solo cargar datos si HAY parámetros GET
$hay_filtros = !empty($_GET);

// Módulos (siempre cargar)
$query_modulos = "SELECT DISTINCT modulo FROM business_records WHERE modulo IS NOT NULL ORDER BY modulo";
$result_modulos = mysqli_query($conexion, $query_modulos);
$modulos = [];
while ($row = mysqli_fetch_assoc($result_modulos)) {
  $modulos[] = $row['modulo'];
}

// Usuarios (siempre cargar)
$query_usuarios = "SELECT DISTINCT from_number FROM business_records ORDER BY from_number";
$result_usuarios = mysqli_query($conexion, $query_usuarios);
$usuarios = [];
while ($row = mysqli_fetch_assoc($result_usuarios)) {
  $usuarios[] = $row['from_number'];
}

// Query - SOLO si hay filtros
$imagenes = [];
if ($hay_filtros) {
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
    if ($filtro_estado === 'EXITO') {
      $query .= " AND br.ocr_status = 'DONE' AND br.parse_status = 'OK'";
    } else if ($filtro_estado === 'ERROR_OCR') {
      $query .= " AND br.ocr_status = 'FAILED'";
    } else if ($filtro_estado === 'ERROR_VALIDACION') {
      $query .= " AND br.ocr_status != 'FAILED' AND br.parse_status != 'OK'";
    } else if ($filtro_estado === 'PROCESANDO') {
      $query .= " AND (br.ocr_status = 'PENDING' OR br.ocr_status IS NULL)";
    } else if ($filtro_estado === 'APROBADO') {
      $query .= " AND br.approval_status = 'APPROVED'";
    } else if ($filtro_estado === 'RECHAZADO') {
      $query .= " AND br.approval_status = 'REJECTED'";
    } else if ($filtro_estado === 'ESPERANDO') {
      $query .= " AND (br.approval_status IS NULL OR br.approval_status = 'PENDING')";
    }
  }

  $query .= " ORDER BY br.created_at DESC LIMIT 200";

  $result = mysqli_query($conexion, $query);
  while ($row = mysqli_fetch_assoc($result)) {
    $imagenes[] = $row;
  }
}

// Función para generar URL del proxy
function get_proxy_url($gcs_uri) {
  return "abm/reporte/bot-imagenes-proxy.php?gcs_uri=" . urlencode($gcs_uri);
}
?>

<div class="right_col" role="main" style="min-height: auto;">
  <div class="clearfix"></div>
  <div class="col-md-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>📸 Imágenes del Bot WhatsApp</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">

        <!-- FILTROS -->
        <div class="row" style="margin-bottom:20px;">
          <div class="col-md-12">
            <div class="form-inline">
              <div class="form-group" style="margin-right:15px;">
                <label style="margin-right:8px;">Módulo:</label>
                <select id="filtro_modulo" class="form-control" style="width:180px;">
                  <option value="">-- Todos --</option>
                  <?php foreach ($modulos as $mod): ?>
                    <option value="<?php echo htmlspecialchars($mod); ?>" <?php echo $filtro_modulo === $mod ? 'selected' : ''; ?>><?php echo strtoupper($mod); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group" style="margin-right:15px;">
                <label style="margin-right:8px;">Usuario:</label>
                <select id="filtro_usuario" class="form-control" style="width:180px;">
                  <option value="">-- Todos --</option>
                  <?php foreach ($usuarios as $usr): ?>
                    <option value="<?php echo htmlspecialchars($usr); ?>" <?php echo $filtro_usuario === $usr ? 'selected' : ''; ?>><?php echo $usr; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group" style="margin-right:15px;">
                <label style="margin-right:8px;">Desde:</label>
                <input type="date" id="filtro_fecha_desde" class="form-control" value="<?php echo $filtro_fecha_desde; ?>" style="width:150px;">
              </div>

              <div class="form-group" style="margin-right:15px;">
                <label style="margin-right:8px;">Hasta:</label>
                <input type="date" id="filtro_fecha_hasta" class="form-control" value="<?php echo $filtro_fecha_hasta; ?>" style="width:150px;">
              </div>

              <div class="form-group" style="margin-right:15px;">
                <label style="margin-right:8px;">Estado:</label>
                <select id="filtro_estado" class="form-control" style="width:220px;">
                  <option value="">-- Todos --</option>
                  <option value="EXITO" <?php echo $filtro_estado === 'EXITO' ? 'selected' : ''; ?>>✓ Extracción OK</option>
                  <option value="ERROR_OCR" <?php echo $filtro_estado === 'ERROR_OCR' ? 'selected' : ''; ?>>✗ Error en OCR</option>
                  <option value="ERROR_VALIDACION" <?php echo $filtro_estado === 'ERROR_VALIDACION' ? 'selected' : ''; ?>>✗ Error en validación</option>
                  <option value="PROCESANDO" <?php echo $filtro_estado === 'PROCESANDO' ? 'selected' : ''; ?>>⏳ Procesando</option>
                  <option value="APROBADO" <?php echo $filtro_estado === 'APROBADO' ? 'selected' : ''; ?>>✓ Aprobado</option>
                  <option value="RECHAZADO" <?php echo $filtro_estado === 'RECHAZADO' ? 'selected' : ''; ?>>✗ Rechazado</option>
                  <option value="ESPERANDO" <?php echo $filtro_estado === 'ESPERANDO' ? 'selected' : ''; ?>>⏳ Esperando aprobación</option>
                </select>
              </div>

              <button type="button" class="btn btn-primary" onclick="filtrar_imagenes_ajax()">🔍 Filtrar</button>
              <button type="button" class="btn btn-default" onclick="limpiar_imagenes_ajax()">🔄 Limpiar</button>
            </div>
          </div>
        </div>

        <!-- CONTENIDO DINÁMICO -->
        <?php if (!$hay_filtros): ?>
          <!-- Sin filtros aplicados -->
          <div class="alert alert-info">
            <strong>ℹ️ Selecciona filtros y haz click en "Filtrar" para ver imágenes</strong>
          </div>

        <?php else: ?>
          <!-- Con filtros aplicados -->
          
          <!-- ESTADÍSTICAS -->
          <div class="alert alert-info" style="margin-bottom:20px;">
            <strong>📊 Resultados:</strong> 
            <span style="margin-left:20px;">
              <strong><?php echo count($imagenes); ?></strong> imágenes encontradas
            </span>
            <span style="margin-left:30px;">
              <i class="fa fa-check" style="color:#26B99A;"></i> Extracción OK: <strong><?php echo count(array_filter($imagenes, function($i) { return $i['ocr_status'] === 'DONE' && $i['parse_status'] === 'OK'; })); ?></strong>
            </span>
            <span style="margin-left:30px;">
              <i class="fa fa-exclamation-triangle" style="color:#E74C3C;"></i> Con errores: <strong><?php echo count(array_filter($imagenes, function($i) { return $i['ocr_status'] === 'FAILED' || $i['parse_status'] !== 'OK'; })); ?></strong>
            </span>
            <span style="margin-left:30px;">
              <i class="fa fa-clock-o" style="color:#E8A838;"></i> Procesando: <strong><?php echo count(array_filter($imagenes, function($i) { return $i['approval_status'] === null || $i['approval_status'] === 'PENDING'; })); ?></strong>
            </span>
          </div>

          <!-- GALERÍA -->
          <?php if (empty($imagenes)): ?>
            <div class="alert alert-warning">
              <strong>⚠ Sin resultados:</strong> No hay imágenes con los filtros aplicados.
            </div>
          <?php else: ?>
            <div class="row">
              <?php foreach ($imagenes as $img): ?>
                <div class="col-md-6 col-lg-4" style="margin-bottom:30px;">
                  <div class="x_panel" style="height:100%; display:flex; flex-direction:column;">
                    <div class="x_content" style="padding:0; flex:1; display:flex; flex-direction:column;">
                      
                      <!-- Imagen con proxy -->
                      <div style="width:100%; height:250px; background:#f5f5f5; border:1px solid #ddd; display:flex; align-items:center; justify-content:center; overflow:hidden; margin-bottom:15px;">
                        <?php if ($img['gcs_uri']): ?>
                          <img src="<?php echo htmlspecialchars(get_proxy_url($img['gcs_uri'])); ?>" style="width:100%; height:100%; object-fit:cover;" alt="Imagen" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22250%22 height=%22200%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22250%22 height=%22200%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22%3EError cargando%3C/text%3E%3C/svg%3E'">
                        <?php else: ?>
                          <span style="color:#999;">Sin imagen</span>
                        <?php endif; ?>
                      </div>

                      <div style="padding:0 15px;">
                        <p style="margin:8px 0;"><strong>📍 Módulo:</strong> <span style="color:#2A3F5F; font-weight:bold;"><?php echo strtoupper($img['modulo'] ?? 'N/A'); ?></span></p>
                        <p style="margin:8px 0;"><strong>👤 Usuario:</strong> <?php echo $img['from_number']; ?></p>
                        <p style="margin:8px 0;"><strong>📄 Parte:</strong> <?php echo $img['numero_doc'] ?? 'N/A'; ?></p>
                        <p style="margin:8px 0;"><strong>📦 Batch:</strong> <small style="color:#666;"><?php echo substr($img['batch_id'] ?? 'N/A', 0, 30); ?></small></p>
                        <p style="margin:8px 0;"><strong>📅 Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($img['created_at'])); ?></p>

                        <div style="margin:12px 0;">
                          <?php if ($img['ocr_status'] === 'DONE' && $img['parse_status'] === 'OK'): ?>
                            <span class="badge" style="background:#26B99A; color:white;">✓ Extracción OK</span>
                          <?php elseif ($img['ocr_status'] === 'FAILED'): ?>
                            <span class="badge" style="background:#E74C3C; color:white;">✗ Error en OCR</span>
                          <?php elseif ($img['parse_status'] !== 'OK'): ?>
                            <span class="badge" style="background:#E74C3C; color:white;">✗ Error en validación</span>
                          <?php else: ?>
                            <span class="badge" style="background:#E8A838; color:white;">⏳ Procesando...</span>
                          <?php endif; ?>

                          <?php if ($img['approval_status'] === 'APPROVED'): ?>
                            <span class="badge" style="background:#27AE60; color:white;">✓ Aprobado</span>
                          <?php elseif ($img['approval_status'] === 'REJECTED'): ?>
                            <span class="badge" style="background:#E74C3C; color:white;">✗ Rechazado</span>
                          <?php elseif ($img['approval_status'] === 'PENDING'): ?>
                            <span class="badge" style="background:#3498DB; color:white;">⏳ Esperando aprobación</span>
                          <?php endif; ?>
                        </div>

                        <?php if ($img['gcs_uri']): ?>
                          <button class="btn btn-primary btn-sm btn-block" style="margin-top:10px;" onclick="descargar_imagen('<?php echo htmlspecialchars($img['gcs_uri']); ?>', '<?php echo htmlspecialchars($img['modulo']); ?>', '<?php echo htmlspecialchars($img['numero_doc']); ?>')">⬇️ Descargar</button>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

<script>
function filtrar_imagenes_ajax() {
  var modulo = $('#filtro_modulo').val();
  var usuario = $('#filtro_usuario').val();
  var fecha_desde = $('#filtro_fecha_desde').val();
  var fecha_hasta = $('#filtro_fecha_hasta').val();
  var estado = $('#filtro_estado').val();

  var params = '?modulo=' + encodeURIComponent(modulo) + 
               '&usuario=' + encodeURIComponent(usuario) + 
               '&fecha_desde=' + encodeURIComponent(fecha_desde) + 
               '&fecha_hasta=' + encodeURIComponent(fecha_hasta) + 
               '&estado=' + encodeURIComponent(estado);

  $("#panel_inicio").html('<div class="text-center"><div class="loadingsm"></div></div>');
  $('#panel_inicio').load("abm/reporte/bot-imagenes.php" + params);
}

function limpiar_imagenes_ajax() {
  $("#panel_inicio").html('<div class="text-center"><div class="loadingsm"></div></div>');
  $('#panel_inicio').load("abm/reporte/bot-imagenes.php");
}

function descargar_imagen(gcs_uri, modulo, numero_doc) {
  var proxy_url = 'abm/reporte/bot-imagenes-proxy.php?gcs_uri=' + encodeURIComponent(gcs_uri) + 
                  '&modulo=' + encodeURIComponent(modulo) + 
                  '&numero_doc=' + encodeURIComponent(numero_doc);
  
  fetch(proxy_url)
    .then(response => {
      if (!response.ok) throw new Error('Error al descargar');
      return response.blob();
    })
    .then(blob => {
      // Construir nombre amigable: MODULO_NUMERO_PARTE.jpg
      var ext = 'jpg'; // Default
      var filename = modulo;
      if (numero_doc) {
        filename += '_' + numero_doc;
      }
      filename += '.' + ext;
      
      var url = window.URL.createObjectURL(blob);
      var a = document.createElement('a');
      a.href = url;
      a.download = filename;
      document.body.appendChild(a);
      a.click();
      window.URL.revokeObjectURL(url);
      document.body.removeChild(a);
    })
    .catch(error => {
      alert('Error al descargar: ' + error.message);
    });
}

</script>
