<!-- =====================================================
     INSTRUCCIONES: Agregar este código a bot-dashboard.php
     UBICACIÓN: Después del tab de "Pendientes de aprobación"
     ANTES DE: </div> de cierre final
     ===================================================== -->

<div id="tab-anular-numeros" class="tab-content">
    <h3>📋 Anular Números de Partes</h3>
    
    <p style="color:#666; margin:15px 0; padding:10px; background:#f0f8ff; border-left:4px solid #2c5aa0;">
        <strong>¿Para qué sirve?</strong> Cuando un operario descarta un formulario en el campo 
        (rayones, errores), regístralo aquí para que NO aparezca como omisión en el reporte de correlatividad.
    </p>
    
    <!-- ==================== FORMULARIO ==================== -->
    <div style="background:#f9f9f9; padding:20px; border-radius:5px; max-width:600px; margin:20px 0;">
        
        <form id="form-anular-numero">
            
            <div class="form-group">
                <label><strong>Módulo/Finca:</strong></label>
                <select name="modulo" required class="form-control" style="width:100%;">
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
                <label><strong>Número del Parte:</strong></label>
                <input type="number" name="numero_doc" required class="form-control" 
                       min="1" max="9999" placeholder="Ej: 4" style="width:100%;">
                <small style="color:#999;">Ingresa solo el número (ej: 4). Se formateará como 000004</small>
            </div>
            
            <div class="form-group">
                <label><strong>Motivo de anulación:</strong></label>
                <select name="motivo" required class="form-control" style="width:100%;">
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
                <label><strong>Observaciones (opcional):</strong></label>
                <textarea name="obs" class="form-control" rows="2" 
                          placeholder="Detalles adicionales..." style="width:100%;"></textarea>
            </div>
            
            <button type="submit" class="btn btn-danger" style="width:100%;">
                🚫 Anular Número
            </button>
        </form>
        
        <div id="resultado-anular" style="margin-top:15px;"></div>
        
    </div>
    
    <!-- ==================== LISTA DE ANULACIONES ESTA SEMANA ==================== -->
    <div style="margin-top:40px;">
        <h4>Números anulados esta semana (Semana <?= date('W') ?>)</h4>
        
        <table class="table table-striped" style="background:white; margin-top:15px;">
            <thead>
                <tr style="background:#34495e; color:white;">
                    <th>Módulo</th>
                    <th>Número</th>
                    <th>Motivo</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $semana = date('W');
                $anio = date('Y');
                $qry = "SELECT * FROM tb_numeros_anulados 
                        WHERE semana = %d AND anio = %d 
                        ORDER BY modulo, numero_doc";
                $query = sprintf($qry, $semana, $anio);
                $result = mysqli_query($conexion, $query);
                
                if (!$result || mysqli_num_rows($result) === 0) {
                    echo '<tr><td colspan="6" style="text-align:center; color:#999; padding:20px;">
                            ℹ️ Ningún número anulado esta semana
                          </td></tr>';
                } else {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $fecha = date('d/m/Y H:i', strtotime($row['fecha_anulacion']));
                        echo '<tr>
                            <td><strong style="color:#2c5aa0;">' . strtoupper($row['modulo']) . '</strong></td>
                            <td><code style="background:#f0f0f0; padding:3px 6px;">' 
                                . htmlspecialchars($row['numero_formateado']) . '</code></td>
                            <td><small>' . htmlspecialchars($row['motivo']) . '</small></td>
                            <td><small>' . htmlspecialchars($row['usuario_anulo']) . '</small></td>
                            <td><small>' . $fecha . '</small></td>
                            <td>
                                <button class="btn btn-xs btn-warning" 
                                        onclick="revertir_anulacion(' . $row['id'] . ', \'' 
                                        . htmlspecialchars($row['numero_formateado']) . '\')"
                                        style="font-size:11px;">
                                    ↶ Deshacer
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

<!-- ==================== JAVASCRIPT ==================== -->
<script>
/**
 * Manejar formulario de anulación
 */
document.getElementById('form-anular-numero').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    formData.append('accion', 'anular');
    
    try {
        const response = await fetch('reporte/bot-anular-numero.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        const div = document.getElementById('resultado-anular');
        
        if (result.ok) {
            div.innerHTML = '<div class="alert alert-success" style="margin:10px 0;">' 
                          + '✓ ' + result.mensaje + '</div>';
            e.target.reset();
            
            // Recargar tabla después de 1.5 segundos
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            div.innerHTML = '<div class="alert alert-danger" style="margin:10px 0;">' 
                          + '✗ Error: ' + result.error + '</div>';
        }
    } catch (error) {
        document.getElementById('resultado-anular').innerHTML = 
            '<div class="alert alert-danger">Error en la solicitud: ' + error.message + '</div>';
    }
});

/**
 * Deshacer anulación (con confirmación)
 */
function revertir_anulacion(id, numero_fmt) {
    if (!confirm('¿Deshacer anulación del número ' + numero_fmt + '?\n\n' +
                 'Este número volvería a aparecer como faltante en el reporte.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('accion', 'eliminar_anulacion');
    formData.append('id', id);
    
    fetch('reporte/bot-anular-numero.php', {
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
        alert('Error en la solicitud: ' + error.message);
    });
}
</script>

<!-- ===================================================== -->