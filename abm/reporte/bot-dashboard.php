<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../../../index.php");
}
if ($_SESSION['id_finca_usuario'] == '0') {
  session_destroy();
  header("Location: ../../../index.php");
}
// ==================== VALIDACIÓN: Solo admin puede acceder al Dashboard ====================
if ($_SESSION['tipo_user'] !== 'admin') {
  http_response_code(403);
  die('<div style="padding:20px; text-align:center; font-family:Arial; margin-top:100px;">
        <h2>Acceso Denegado</h2>
        <p>Solo administradores pueden acceder al Dashboard del Bot WhatsApp.</p>
        <a href="../../index2.php" style="color:#26B99A; text-decoration:none;">Volver al inicio</a>
       </div>');
}
// ==================== FIN VALIDACIÓN ====================
include '../../conexion/conexion.php';
$conexion = conectarServidor();

// ==================== NUEVO: Función Control de Correlatividad ====================
function get_correlatividad_omisiones($conexion) {
    $query = "
    WITH RECURSIVE 
    
    partes_unicos AS (
      SELECT DISTINCT
        t.id_parte_diario_global,
        b.modulo,
        t.fecha,
        CAST(t.obs_general AS UNSIGNED) as numero_doc
      FROM tb_parte_diario_test t
      INNER JOIN business_records b ON t.id_parte_diario_global = b.id_parte_diario_global
      WHERE t.origen = 'bot'
        AND t.obs_general IS NOT NULL
        AND WEEK(t.fecha, 1) = WEEK(CURDATE(), 1)
        AND YEAR(t.fecha) = YEAR(CURDATE())
    ),
    
    estadisticas AS (
      SELECT 
        modulo,
        WEEK(fecha, 1) as semana,
        YEAR(fecha) as anio,
        DATE_SUB(fecha, INTERVAL WEEKDAY(fecha) DAY) as fecha_lunes,
        DATE_ADD(DATE_SUB(fecha, INTERVAL WEEKDAY(fecha) DAY), INTERVAL 6 DAY) as fecha_domingo,
        COUNT(DISTINCT id_parte_diario_global) as total_recibidos,
        1 as numero_minimo_esperado,
        MAX(numero_doc) as numero_maximo,
        MAX(numero_doc) as rango_esperado
      FROM partes_unicos
      GROUP BY modulo, WEEK(fecha, 1), YEAR(fecha)
    ),
    
    numeros_serie AS (
      SELECT 
        e.modulo,
        1 as numero
      FROM estadisticas e
      
      UNION ALL
      
      SELECT 
        ns.modulo,
        ns.numero + 1
      FROM numeros_serie ns
      INNER JOIN estadisticas e ON ns.modulo = e.modulo
      WHERE ns.numero < e.numero_maximo
    ),
    
    numeros_faltantes AS (
      SELECT 
        ns.modulo,
        ns.numero,
        LPAD(ns.numero, 6, '0') as numero_formateado
      FROM numeros_serie ns
      WHERE NOT EXISTS (
        SELECT 1 FROM partes_unicos pu
        WHERE pu.modulo = ns.modulo
          AND pu.numero_doc = ns.numero
      )
    )
    
    SELECT 
      e.modulo,
      e.semana,
      e.anio,
      DATE_FORMAT(e.fecha_lunes, '%Y-%m-%d') as fecha_lunes,
      DATE_FORMAT(e.fecha_domingo, '%Y-%m-%d') as fecha_domingo,
      e.total_recibidos,
      LPAD(e.numero_minimo_esperado, 6, '0') as numero_minimo,
      LPAD(e.numero_maximo, 6, '0') as numero_maximo,
      e.rango_esperado,
      (e.rango_esperado - e.total_recibidos) as cantidad_omisiones,
      COALESCE(
        GROUP_CONCAT(nf.numero_formateado ORDER BY nf.numero SEPARATOR ', '),
        'NINGUNA'
      ) as numeros_omitidos
    FROM estadisticas e
    LEFT JOIN numeros_faltantes nf ON e.modulo = nf.modulo
    GROUP BY e.modulo, e.semana, e.anio
    ORDER BY e.modulo, e.semana
    ";
    
    try {
        $resultado = mysqli_query($conexion, $query);
        
        if (!$resultado) {
            throw new Exception('Query Error: ' . mysqli_error($conexion));
        }
        
        $datos = [];
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $datos[] = $fila;
        }
        
        return [
            'exito' => true,
            'datos' => $datos,
            'error' => null
        ];
        
    } catch (Exception $e) {
        return [
            'exito' => false,
            'datos' => [],
            'error' => $e->getMessage()
        ];
    }
}

// Ejecutar query de correlatividad
$corr = get_correlatividad_omisiones($conexion);
// ==================== FIN: Función Control de Correlatividad ====================

// Resumen del día de hoy
$sqlHoy = "SELECT
  COUNT(*) AS total,
  SUM(CASE WHEN approval_status = 'APPROVED' THEN 1 ELSE 0 END) AS aprobados,
  SUM(CASE WHEN approval_status = 'REJECTED' THEN 1 ELSE 0 END) AS rechazados,
  SUM(CASE WHEN approval_status IS NULL AND ocr_status = 'DONE' AND parse_status = 'OK' THEN 1 ELSE 0 END) AS pendientes,
  SUM(CASE WHEN insert_status = 'OK' THEN 1 ELSE 0 END) AS insertados,
  SUM(CASE WHEN insert_status = 'ERROR' THEN 1 ELSE 0 END) AS insert_error,
  SUM(CASE WHEN ocr_status = 'FAILED' THEN 1 ELSE 0 END) AS ocr_fallido,
  SUM(CASE WHEN parse_status = 'REVIEW' THEN 1 ELSE 0 END) AS con_errores
FROM business_records
WHERE DATE(created_at) = CURDATE()";
$rsHoy = mysqli_query($conexion, $sqlHoy);
$hoy = mysqli_fetch_assoc($rsHoy);

// Resumen últimos 7 días
$sqlSemana = "SELECT
  DATE(created_at) AS dia,
  COUNT(*) AS total,
  SUM(CASE WHEN approval_status = 'APPROVED' THEN 1 ELSE 0 END) AS aprobados,
  SUM(CASE WHEN insert_status = 'ERROR' THEN 1 ELSE 0 END) AS errores
FROM business_records
WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
GROUP BY DATE(created_at)
ORDER BY dia DESC";
$rsSemana = mysqli_query($conexion, $sqlSemana);

// Últimos partes procesados
$sqlUltimos = "SELECT
  br.id,
  br.idempotency_key,
  br.from_number,
  br.modulo,
  br.numero_doc,
  br.parse_status,
  br.approval_status,
  br.insert_status,
  br.ocr_status,
  br.id_parte_diario_global,
  DATE_FORMAT(br.created_at, '%d/%m %H:%i') AS fecha
FROM business_records br
WHERE br.document_type = 'parte_diario'
ORDER BY br.id DESC
LIMIT 20";
$rsUltimos = mysqli_query($conexion, $sqlUltimos);
?>

<div class="right_col" role="main" style="min-height: auto;">
    <?php include 'bot-dashboard-status-bar.php'; ?>
  <div class="clearfix"></div>
  <div class="col-md-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>Bot WhatsApp <small>Dashboard de monitoreo — hoy</small></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">

        <!-- Tiles del día -->
        <div class="row tile_count" style="margin:0 0 20px 0">
          <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            <span class="count_top"><i class="fa fa-image"></i> Recibidos</span>
            <div class="count"><?php echo $hoy['total']; ?></div>
            <span class="count_bottom">hoy</span>
          </div>
          <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            <span class="count_top"><i class="fa fa-check" style="color:#26B99A"></i> Aprobados</span>
            <div class="count" style="color:#26B99A"><?php echo $hoy['aprobados']; ?></div>
            <span class="count_bottom">confirmados por usuario</span>
          </div>
          <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            <span class="count_top"><i class="fa fa-database" style="color:#26B99A"></i> Insertados</span>
            <div class="count" style="color:#26B99A"><?php echo $hoy['insertados']; ?></div>
            <span class="count_bottom">en tb_parte_diario</span>
          </div>
          <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            <span class="count_top"><i class="fa fa-clock-o" style="color:#E8A838"></i> Pendientes</span>
            <div class="count" style="color:#E8A838"><?php echo $hoy['pendientes']; ?></div>
            <span class="count_bottom">esperando confirmación</span>
          </div>
          <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            <span class="count_top"><i class="fa fa-times" style="color:#E74C3C"></i> Rechazados</span>
            <div class="count" style="color:#E74C3C"><?php echo $hoy['rechazados']; ?></div>
            <span class="count_bottom">por el usuario</span>
          </div>
          <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
            <span class="count_top"><i class="fa fa-exclamation-triangle" style="color:#E74C3C"></i> Errores</span>
            <div class="count" style="color:#E74C3C"><?php echo $hoy['ocr_fallido'] + $hoy['insert_error']; ?></div>
            <span class="count_bottom">OCR: <?php echo $hoy['ocr_fallido']; ?> / Insert: <?php echo $hoy['insert_error']; ?></span>
          </div>
        </div>

        <!-- ==================== NUEVO: CARD Control de Correlatividad ==================== -->
        <?php if ($corr['exito']): ?>
        <div class="row" style="margin-left:0; margin-right:0">
          <div class="col-md-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>
                  <i class="fa fa-check-circle"></i> 
                  Control de Correlatividad de Numeración
                </h2>
                <ul class="nav navbar-right panel_toolbox">
                  <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
              </div>
              
              <div class="x_content">
                <?php if (empty($corr['datos'])): ?>
                    <div class="alert alert-info">
                        <strong>Sin datos:</strong> No hay partes del bot en la semana actual.
                    </div>
                <?php else: ?>
                    <table class="table table-striped table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Módulo</th>
                                <th class="text-center">Semana</th>
                                <th class="text-center">Período</th>
                                <th class="text-center">Recibidos</th>
                                <th class="text-center">Rango</th>
                                <th class="text-center">Omisiones</th>
                                <th>Números Faltantes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($corr['datos'] as $fila): 
                                $tiene_omisiones = (int)$fila['cantidad_omisiones'] > 0;
                                $clase_fila = $tiene_omisiones ? 'table-warning' : '';
                            ?>
                                <tr class="<?php echo $clase_fila; ?>">
                                    <td>
                                        <strong><?php echo htmlspecialchars($fila['modulo']); ?></strong>
                                    </td>
                                    
                                    <td class="text-center">
                                        <span class="badge" style="background:#26B99A">
                                            Sem. <?php echo $fila['semana']; ?> (<?php echo $fila['anio']; ?>)
                                        </span>
                                    </td>
                                    
                                    <td class="text-center text-muted" style="font-size:11px">
                                        <?php echo $fila['fecha_lunes']; ?> a <?php echo $fila['fecha_domingo']; ?>
                                    </td>
                                    
                                    <td class="text-center">
                                        <span class="badge" style="background:#3398DC">
                                            <?php echo $fila['total_recibidos']; ?>
                                        </span>
                                    </td>
                                    
                                    <td class="text-center text-muted">
                                        <?php echo $fila['numero_minimo']; ?> - <?php echo $fila['numero_maximo']; ?>
                                        <br>
                                        <small>(<?php echo $fila['rango_esperado']; ?> números)</small>
                                    </td>
                                    
                                    <td class="text-center">
                                        <?php if ($tiene_omisiones): ?>
                                            <span class="badge" style="background:#E74C3C">
                                                <?php echo $fila['cantidad_omisiones']; ?> omisiones
                                            </span>
                                        <?php else: ?>
                                            <span class="badge" style="background:#26B99A">
                                                ✓ Completo
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td>
                                        <?php if ($tiene_omisiones): ?>
                                            <code style="background-color:#fff3cd; padding:3px 6px; border-radius:3px; font-size:11px">
                                                <?php echo htmlspecialchars($fila['numeros_omitidos']); ?>
                                            </code>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
        <?php else: ?>
          <div class="alert alert-danger">
              <strong>Error:</strong> <?php echo htmlspecialchars($corr['error']); ?>
          </div>
        <?php endif; ?>
        <!-- ==================== FIN: CARD Control de Correlatividad ==================== -->

        <!-- Tabla últimos 7 días -->
        <div class="row" style="margin-left:0; margin-right:0; margin-top:20px">
          <div class="col-md-4">
            <div class="x_panel">
              <div class="x_title"><h2>Últimos 7 días</h2><div class="clearfix"></div></div>
              <div class="x_content">
                <table class="table table-striped table-bordered table-condensed">
                  <thead><tr><th>Día</th><th>Total</th><th>Aprobados</th><th>Errores</th></tr></thead>
                  <tbody>
                    <?php while ($row = mysqli_fetch_assoc($rsSemana)): ?>
                    <tr>
                      <td><?php echo date('d/m', strtotime($row['dia'])); ?></td>
                      <td><?php echo $row['total']; ?></td>
                      <td><span class="badge" style="background:#26B99A"><?php echo $row['aprobados']; ?></span></td>
                      <td>
                        <?php if ($row['errores'] > 0): ?>
                          <span class="badge" style="background:#E74C3C"><?php echo $row['errores']; ?></span>
                        <?php else: ?>
                          <span class="badge" style="background:#26B99A">0</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Tabla últimos partes -->
          <div class="col-md-8">
            <div class="x_panel">
              <div class="x_title"><h2>Últimos partes procesados</h2><div class="clearfix"></div></div>
              <div class="x_content">
                <table class="table table-striped table-bordered table-condensed" style="font-size:12px">
                  <thead>
                    <tr>
                      <th>Fecha</th>
                      <th>Número</th>
                      <th>Módulo</th>
                      <th>OCR</th>
                      <th>Parse</th>
                      <th>Aprobación</th>
                      <th>Insert</th>
                      <th>Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($row = mysqli_fetch_assoc($rsUltimos)):
                      $badgeOcr    = $row['ocr_status'] == 'DONE' ? 'success' : ($row['ocr_status'] == 'FAILED' ? 'danger' : 'warning');
                      $badgeParse  = $row['parse_status'] == 'OK' ? 'success' : ($row['parse_status'] == 'ERROR' ? 'danger' : 'warning');
                      $badgeApr    = $row['approval_status'] == 'APPROVED' ? 'success' : ($row['approval_status'] == 'REJECTED' ? 'danger' : ($row['approval_status'] == 'CANCELLED' ? 'default' : 'warning'));
                      $badgeIns    = $row['insert_status'] == 'OK' ? 'success' : ($row['insert_status'] == 'ERROR' ? 'danger' : ($row['insert_status'] == 'CANCELLED' ? 'default' : 'default'));
                      $cancelable  = $row['approval_status'] == 'APPROVED' && $row['insert_status'] == 'OK';
                    ?>
                    <tr id="row-<?php echo $row['id']; ?>">
                      <td><?php echo $row['fecha']; ?></td>
                      <td><?php echo $row['numero_doc'] ?: '—'; ?></td>
                      <td><?php echo strtoupper($row['modulo'] ?: '—'); ?></td>
                      <td><span class="badge bg-<?php echo $badgeOcr; ?>"><?php echo $row['ocr_status']; ?></span></td>
                      <td><span class="badge bg-<?php echo $badgeParse; ?>"><?php echo $row['parse_status'] ?: '—'; ?></span></td>
                      <td><span class="badge bg-<?php echo $badgeApr; ?>"><?php echo $row['approval_status'] ?: 'PENDING'; ?></span></td>
                      <td><span class="badge bg-<?php echo $badgeIns; ?>"><?php echo $row['insert_status'] ?: '—'; ?></span></td>
                      <td>
                        <?php if ($cancelable): ?>
                        <button class="btn btn-xs btn-danger btn-cancelar"
                          data-id="<?php echo htmlspecialchars($row['idempotency_key']); ?>"
                          data-global="<?php echo htmlspecialchars($row['id_parte_diario_global']); ?>"
                          data-row="<?php echo $row['id']; ?>">
                          <i class="fa fa-times"></i> Cancelar
                        </button>
                        <?php else: ?>
                        —
                        <?php endif; ?>
                      </td>
                    </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  $('#msg-cancelar').remove();

  $(document).on('click', '.btn-cancelar', function() {
    var btn      = $(this);
    var ik       = btn.data('id');
    var global   = btn.data('global');
    var rowId    = btn.data('row');

    if (!confirm('¿Cancelar el parte N°' + btn.closest('tr').find('td:nth-child(2)').text() + '? Se borrará de tb_parte_diario_test.')) return;

    btn.attr('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

    $.ajax({
      url: 'abm/guardar/bot-cancelar.php',
      type: 'POST',
      data: { idempotency_key: ik, id_parte_diario_global: global },
      dataType: 'json',
      success: function(data) {
        if (data.success == 'true') {
          $('#row-' + rowId).find('.badge').first().closest('td').siblings().last().html('—');
          $('#row-' + rowId + ' td:nth-child(6) span').removeClass().addClass('badge bg-default').text('CANCELLED');
          $('#row-' + rowId + ' td:nth-child(7) span').removeClass().addClass('badge bg-default').text('CANCELLED');
          btn.closest('td').html('—');
        } else {
          alert('Error: ' + (data.error || 'desconocido'));
          btn.attr('disabled', false).html('<i class="fa fa-times"></i> Cancelar');
        }
      },
      error: function() {
        alert('Error de conexión.');
        btn.attr('disabled', false).html('<i class="fa fa-times"></i> Cancelar');
      }
    });
  });
});
</script>
