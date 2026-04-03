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
  br.from_number,
  br.modulo,
  br.numero_doc,
  br.approval_status,
  br.insert_status,
  br.parsed_json,
  DATE_FORMAT(br.created_at, '%d/%m/%Y %H:%i') AS fecha,
  TIMESTAMPDIFF(HOUR, br.created_at, NOW()) AS horas_transcurridas
FROM business_records br
WHERE br.document_type = 'parte_diario'
  AND br.parse_status = 'OK'
  AND br.ocr_status = 'DONE'
  AND br.approval_status IS NULL
ORDER BY br.id DESC
LIMIT 50";
$rs = mysqli_query($conexion, $sql);
?>

<div class="right_col" role="main" style="min-height: auto;">
  <div class="clearfix"></div>
  <div class="col-md-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>Bot WhatsApp <small>Partes pendientes de aprobación</small></h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">

        <?php
        $total = mysqli_num_rows($rs);
        if ($total == 0):
        ?>
        <div class="alert alert-success">No hay partes pendientes de aprobación.</div>
        <?php else: ?>

        <p class="text-muted">Partes procesados correctamente que el usuario aún no confirmó ni rechazó. Expiran a las 24hs.</p>

        <table class="table table-striped table-bordered dt-responsive" id="datatable-bot-pendientes" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Número</th>
              <th>Módulo</th>
              <th>Teléfono</th>
              <th>Horas</th>
              <th>Estado</th>
              <th>Detalle</th>
            </tr>
          </thead>
          <tbody>
            <?php
            mysqli_data_seek($rs, 0);
            while ($row = mysqli_fetch_assoc($rs)):
              $horas = (int)$row['horas_transcurridas'];
              if ($horas >= 20) {
                $badge = 'danger';
                $label = "Vence en " . (24 - $horas) . "h";
              } elseif ($horas >= 12) {
                $badge = 'warning';
                $label = "Hace {$horas}h";
              } else {
                $badge = 'info';
                $label = "Hace {$horas}h";
              }

              // Parsear JSON para mostrar resumen
              $parsed = json_decode($row['parsed_json'], true);
              $modalidad = $parsed['modalidad'] ?? '—';
              $personal  = implode(', ', $parsed['personal'] ?? []);
              $empresa   = $parsed['empresa'] ?? '';
              $quien     = $modalidad == 'propio' ? $personal : $empresa;
              $labores   = implode(', ', $parsed['labores'] ?? []);
            ?>
            <tr>
              <td><?php echo $row['fecha']; ?></td>
              <td><?php echo $row['numero_doc'] ?: '—'; ?></td>
              <td><?php echo strtoupper($row['modulo'] ?: '—'); ?></td>
              <td><?php echo $row['from_number']; ?></td>
              <td><span class="badge bg-<?php echo $badge; ?>"><?php echo $label; ?></span></td>
              <td><?php echo ucfirst($modalidad); ?></td>
              <td style="font-size:11px">
                <?php echo htmlspecialchars($quien); ?><br>
                <em><?php echo htmlspecialchars($labores); ?></em>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  $('#datatable-bot-pendientes').DataTable({
    responsive: true,
    order: [[0, 'desc']]
  });
});
</script>
