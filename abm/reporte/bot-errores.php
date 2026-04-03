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

$sql = "SELECT
  br.id,
  br.idempotency_key,
  br.from_number,
  br.modulo,
  br.numero_doc,
  br.ocr_status,
  br.parse_status,
  br.insert_status,
  br.ocr_error,
  br.parse_error,
  br.final_failure_notified,
  DATE_FORMAT(br.created_at, '%d/%m/%Y %H:%i') AS fecha
FROM business_records br
WHERE br.document_type = 'parte_diario'
  AND (
    br.ocr_status = 'FAILED'
    OR br.parse_status = 'ERROR'
    OR br.parse_status = 'MAPPING_ERROR'
    OR br.insert_status = 'ERROR'
    OR br.parse_status = 'REVIEW'
  )
ORDER BY br.id DESC
LIMIT 50";
$rs = mysqli_query($conexion, $sql);
?>

<div class="right_col" role="main" style="min-height: auto;">
  <div class="clearfix"></div>
  <div class="col-md-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>Bot WhatsApp <small>Partes con errores</small></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">

        <div id="msg_requeue"></div>

        <table class="table table-striped table-bordered dt-responsive" id="datatable-bot-errores" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Número</th>
              <th>Módulo</th>
              <th>Teléfono</th>
              <th>OCR</th>
              <th>Parse</th>
              <th>Insert</th>
              <th>Detalle</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($rs)):
              $badgeOcr   = $row['ocr_status'] == 'FAILED' ? 'danger' : 'success';
              $badgeParse = in_array($row['parse_status'], ['ERROR','MAPPING_ERROR']) ? 'danger' : ($row['parse_status'] == 'REVIEW' ? 'warning' : 'success');
              $badgeIns   = $row['insert_status'] == 'ERROR' ? 'danger' : 'default';
              $detalle    = $row['ocr_error'] ?: $row['parse_error'] ?: '—';
              $detalle    = mb_substr($detalle, 0, 60);
            ?>
            <tr>
              <td><?php echo $row['fecha']; ?></td>
              <td><?php echo $row['numero_doc'] ?: '—'; ?></td>
              <td><?php echo strtoupper($row['modulo'] ?: '—'); ?></td>
              <td><?php echo $row['from_number']; ?></td>
              <td><span class="badge bg-<?php echo $badgeOcr; ?>"><?php echo $row['ocr_status']; ?></span></td>
              <td><span class="badge bg-<?php echo $badgeParse; ?>"><?php echo $row['parse_status'] ?: '—'; ?></span></td>
              <td><span class="badge bg-<?php echo $badgeIns; ?>"><?php echo $row['insert_status'] ?: '—'; ?></span></td>
              <td style="font-size:11px;max-width:200px;word-break:break-all"><?php echo htmlspecialchars($detalle); ?></td>
              <td>
                <?php if ($row['ocr_status'] == 'FAILED' || $row['parse_status'] == 'ERROR' || $row['parse_status'] == 'MAPPING_ERROR'): ?>
                <button class="btn btn-xs btn-warning btn-requeue"
                  data-id="<?php echo htmlspecialchars($row['idempotency_key']); ?>">
                  <i class="fa fa-refresh"></i> Reenviar
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

<script>
$(document).ready(function() {

  $('#datatable-bot-errores').DataTable({
    responsive: true,
    language: { url: '' },
    order: [[0, 'desc']]
  });

  $('.btn-requeue').click(function() {
    var id = $(this).data('id');
    var btn = $(this);
    btn.attr('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

    $.ajax({
      url: 'abm/guardar/bot-requeue.php',
      type: 'POST',
      data: { id: id },
      dataType: 'json',
      success: function(data) {
        if (data.ok) {
          $('#msg_requeue').html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>Job reencolado correctamente.</div>');
          btn.html('<i class="fa fa-check"></i> Reenviado');
        } else {
          $('#msg_requeue').html('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>Error: ' + (data.error || 'desconocido') + '</div>');
          btn.attr('disabled', false).html('<i class="fa fa-refresh"></i> Reenviar');
        }
      },
      error: function() {
        $('#msg_requeue').html('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>Error de conexión con la API.</div>');
        btn.attr('disabled', false).html('<i class="fa fa-refresh"></i> Reenviar');
      }
    });
  });

});
</script>
