<?php
/**
 * bot-anular-parte.php
 * Ubicación: /abm/reporte/bot-anular-parte.php
 * 
 * Panel para anular números de partes descartados en el campo
 * CORREGIDO: Ruta absoluta del fetch
 */

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../../index.php");
    exit;
}

if ($_SESSION['tipo_user'] !== 'admin') {
    http_response_code(403);
    die('<div class="right_col" role="main"><h3>Acceso Denegado</h3></div>');
}

include '../../conexion/conexion.php';
$conexion = conectarServidor();

$semana = date('W');
$anio = date('Y');
?>

<div class="right_col" role="main">
  <div class="clearfix"></div>
  
  <div class="col-md-12">
    <div class="x_panel">
      <div class="x_title">
        <h2>Anular Números de Partes</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        
        <!-- SECCIÓN 1: Formulario y SECCIÓN 2: Tabla -->
        <div class="row">
          
          <!-- COLUMNA IZQUIERDA: Formulario -->
          <div class="col-md-6">
            
            <p style="color:#666; font-size:13px; margin-bottom:20px;">
              <strong>¿Cuándo usar esto?</strong> Cuando un operario descarta un formulario en el campo 
              (rayones, errores graves), regístralo aquí para que NO aparezca como omisión en el reporte de correlatividad.
            </p>
            
            <form id="form-anular-numero">
              
              <div class="form-group">
                <label>Módulo/Finca:</label>
                <select name="modulo" required class="form-control">
                  <option value="">-- Seleccionar --</option>
                  <?php
                  $qry = "SELECT DISTINCT modulo FROM business_records 
                          WHERE modulo IS NOT NULL 
                          ORDER BY modulo";
                  $result = mysqli_query($conexion, $qry);
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo '<option value="' . htmlspecialchars($row['modulo']) . '">' 
                      . strtoupper($row['modulo']) . '</option>';
                  }
                  ?>
                </select>
              </div>
              
              <div class="form-group">
                <label>Número del Parte:</label>
                <input type="number" name="numero_doc" required class="form-control" 
                       min="1" max="9999" placeholder="Ej: 4">
                <small style="color:#999;">Ingresa solo el número. Se formateará como 000004</small>
              </div>
              
              <div class="form-group">
                <label>Motivo de anulación:</label>
                <select name="motivo" required class="form-control">
                  <option value="">-- Seleccionar --</option>
                  <option value="Rayones, ilegible">Rayones, ilegible</option>
                  <option value="Datos duplicados">Datos duplicados</option>
                  <option value="Error grave, no se puede usar">Error grave, no se puede usar</option>
                  <option value="Formulario perdido">Formulario perdido</option>
                  <option value="Correcciones ilegibles">Correcciones ilegibles</option>
                  <option value="Otro">Otro</option>
                </select>
              </div>
              
              <div class="form-group">
                <label>Observaciones (opcional):</label>
                <textarea name="obs" class="form-control" rows="2" 
                          placeholder="Detalles adicionales..."></textarea>
              </div>
              
              <button type="submit" class="btn btn-danger">
                <i class="fa fa-ban"></i> Anular Número
              </button>
            </form>
            
            <div id="resultado-anular" style="margin-top:15px;"></div>
            
          </div>
          
          <!-- COLUMNA DERECHA: Tabla de anulaciones -->
          <div class="col-md-6">
            
            <h4 style="margin-top:0;">Anulados esta semana (Semana <?= $semana ?>)</h4>
            
            <div class="table-responsive">
              <table class="table table-striped table-bordered">
                <thead style="background:#34495e; color:white;">
                  <tr>
                    <th>Módulo</th>
                    <th>Número</th>
                    <th>Motivo</th>
                    <th>Usuario</th>
                    <th style="width:60px;">Acción</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $qry = "SELECT * FROM tb_numeros_anulados 
                          WHERE semana = %d AND anio = %d 
                          ORDER BY modulo, numero_doc";
                  $query = sprintf($qry, $semana, $anio);
                  $result = mysqli_query($conexion, $query);
                  
                  if (!$result || mysqli_num_rows($result) === 0) {
                    echo '<tr><td colspan="5" style="text-align:center; color:#999; padding:20px;">
                            ℹ️ Ninguno anulado
                          </td></tr>';
                  } else {
                    while ($row = mysqli_fetch_assoc($result)) {
                      echo '<tr>
                        <td><strong>' . strtoupper($row['modulo']) . '</strong></td>
                        <td><code>' . htmlspecialchars($row['numero_formateado']) . '</code></td>
                        <td><small>' . htmlspecialchars($row['motivo']) . '</small></td>
                        <td><small>' . htmlspecialchars($row['usuario_anulo']) . '</small></td>
                        <td style="text-align:center;">
                          <button class="btn btn-xs btn-warning" 
                                  onclick="revertir_anulacion(' . $row['id'] . ', \'' 
                                  . htmlspecialchars($row['numero_formateado']) . '\')"
                                  title="Deshacer anulación">
                            <i class="fa fa-undo"></i>
                          </button>
                        </td>
                      </tr>';
                    }
                  }
                  ?>
                </tbody>
              </table>
            </div>
            
          </div>
          
        </div>
        
      </div>
    </div>
  </div>
  
</div>

<!-- ==================== JAVASCRIPT ==================== -->
<script>
/**
 * Manejar formulario de anulación
 * RUTA CORREGIDA: /agrogestion/abm/reporte/bot-anular-numero.php (ruta absoluta)
 */
document.getElementById('form-anular-numero').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    formData.append('accion', 'anular');
    
    try {
        const response = await fetch('/agrogestion/abm/reporte/bot-anular-numero.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        const div = document.getElementById('resultado-anular');
        
        if (result.ok) {
            div.innerHTML = '<div class="alert alert-success alert-dismissible fade in" role="alert">' 
                          + '<button type="button" class="close" data-dismiss="alert"><span>×</span></button>'
                          + '<strong>✓ Éxito:</strong> ' + result.mensaje + '</div>';
            e.target.reset();
            
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            div.innerHTML = '<div class="alert alert-danger alert-dismissible fade in" role="alert">' 
                          + '<button type="button" class="close" data-dismiss="alert"><span>×</span></button>'
                          + '<strong>✗ Error:</strong> ' + result.error + '</div>';
        }
    } catch (error) {
        document.getElementById('resultado-anular').innerHTML = 
            '<div class="alert alert-danger">Error: ' + error.message + '</div>';
    }
});

/**
 * Deshacer anulación
 * RUTA CORREGIDA: /agrogestion/abm/reporte/bot-anular-numero.php (ruta absoluta)
 */
function revertir_anulacion(id, numero_fmt) {
    if (!confirm('¿Deshacer anulación del número ' + numero_fmt + '?\n\n' +
                 'Este número volverá a aparecer como faltante en el reporte.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('accion', 'eliminar_anulacion');
    formData.append('id', id);
    
    fetch('/agrogestion/abm/reporte/bot-anular-numero.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(result => {
        if (result.ok) {
            alert(result.mensaje);
            location.reload();
        } else {
            alert('Error: ' + result.error);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}
</script>

<?php
mysqli_close($conexion);
?>
