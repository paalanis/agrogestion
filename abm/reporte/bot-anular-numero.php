<?php
/**
 * bot-anular-numero.php
 * Ubicación: /abm/reporte/bot-anular-numero.php
 * 
 * Funcionalidad: Anular números de partes descartados en el campo
 * Métodos: POST con acción='anular' o acción='eliminar_anulacion'
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

// =====================================================
// VALIDACIÓN DE ACCESO
// =====================================================

if (!isset($_SESSION['tipo_user']) || $_SESSION['tipo_user'] != 'admin') {
    http_response_code(403);
    echo json_encode([
        'ok' => false,
        'error' => 'Acceso denegado. Solo administradores pueden anular números.'
    ]);
    exit;
}

// =====================================================
// CONEXIÓN A BD
// =====================================================

include '../../../conexion/conexion.php';
$conexion = conectarServidor();

if (!$conexion) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error de conexión a base de datos']);
    exit;
}

// =====================================================
// OBTENER PARÁMETROS
// =====================================================

$accion = $_POST['accion'] ?? '';
$modulo = $_POST['modulo'] ?? '';
$numero_doc = $_POST['numero_doc'] ?? 0;
$motivo = $_POST['motivo'] ?? '';
$obs = $_POST['obs'] ?? '';

// =====================================================
// ACCIÓN: ANULAR UN NÚMERO
// =====================================================

if ($accion === 'anular') {
    
    // Validar parámetros
    if (!$modulo || !$numero_doc || !$motivo) {
        http_response_code(400);
        echo json_encode([
            'ok' => false,
            'error' => 'Faltan parámetros: modulo, numero_doc, motivo'
        ]);
        exit;
    }
    
    // Validar que numero_doc sea positivo
    $numero_doc = intval($numero_doc);
    if ($numero_doc <= 0) {
        http_response_code(400);
        echo json_encode([
            'ok' => false,
            'error' => 'El número del parte debe ser mayor que 0'
        ]);
        exit;
    }
    
    // Obtener semana y año actuales
    $semana = date('W');
    $anio = date('Y');
    
    // Formatear número con padding
    $numero_formateado = str_pad($numero_doc, 6, '0', STR_PAD_LEFT);
    
    // Verificar si ya está anulado
    $qry = "SELECT id FROM tb_numeros_anulados 
            WHERE modulo = %s 
              AND numero_doc = %d 
              AND semana = %d 
              AND anio = %d";
    
    $query = sprintf($qry,
        "'" . mysqli_real_escape_string($conexion, $modulo) . "'",
        $numero_doc,
        $semana,
        $anio
    );
    
    $result = mysqli_query($conexion, $query);
    
    if (!$result) {
        http_response_code(500);
        echo json_encode([
            'ok' => false,
            'error' => 'Error en la consulta: ' . mysqli_error($conexion)
        ]);
        exit;
    }
    
    if (mysqli_num_rows($result) > 0) {
        http_response_code(409);
        echo json_encode([
            'ok' => false,
            'error' => "El número $numero_formateado ya está anulado esta semana"
        ]);
        exit;
    }
    
    // Insertar anulación
    $usuario = $_SESSION['usuario'] ?? 'sistema';
    
    $qry = "INSERT INTO tb_numeros_anulados 
            (modulo, numero_doc, numero_formateado, semana, anio, usuario_anulo, motivo, obs)
            VALUES (%s, %d, %s, %d, %d, %s, %s, %s)";
    
    $query = sprintf($qry,
        "'" . mysqli_real_escape_string($conexion, $modulo) . "'",
        $numero_doc,
        "'" . mysqli_real_escape_string($conexion, $numero_formateado) . "'",
        $semana,
        $anio,
        "'" . mysqli_real_escape_string($conexion, $usuario) . "'",
        "'" . mysqli_real_escape_string($conexion, $motivo) . "'",
        "'" . mysqli_real_escape_string($conexion, $obs) . "'"
    );
    
    if (mysqli_query($conexion, $query)) {
        http_response_code(201);
        echo json_encode([
            'ok' => true,
            'mensaje' => "✓ Número $numero_formateado anulado correctamente",
            'modulo' => $modulo,
            'numero' => $numero_formateado,
            'motivo' => $motivo
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'ok' => false,
            'error' => 'Error al insertar: ' . mysqli_error($conexion)
        ]);
    }

// =====================================================
// ACCIÓN: DESHACER ANULACIÓN
// =====================================================

} elseif ($accion === 'eliminar_anulacion') {
    
    $id = $_POST['id'] ?? 0;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode([
            'ok' => false,
            'error' => 'ID de anulación faltante'
        ]);
        exit;
    }
    
    $id = intval($id);
    
    // Obtener datos antes de eliminar (para logging)
    $qry = "SELECT modulo, numero_formateado FROM tb_numeros_anulados WHERE id = %d";
    $query = sprintf($qry, $id);
    $result = mysqli_query($conexion, $query);
    
    if (!$result || mysqli_num_rows($result) === 0) {
        http_response_code(404);
        echo json_encode([
            'ok' => false,
            'error' => 'Anulación no encontrada'
        ]);
        exit;
    }
    
    $row = mysqli_fetch_assoc($result);
    $numero_fmt = $row['numero_formateado'];
    
    // Eliminar anulación
    $qry = "DELETE FROM tb_numeros_anulados WHERE id = %d";
    $query = sprintf($qry, $id);
    
    if (mysqli_query($conexion, $query)) {
        http_response_code(200);
        echo json_encode([
            'ok' => true,
            'mensaje' => "✓ Anulación del número $numero_fmt revertida"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'ok' => false,
            'error' => 'Error al eliminar: ' . mysqli_error($conexion)
        ]);
    }

// =====================================================
// ACCIÓN NO VÁLIDA
// =====================================================

} else {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'error' => 'Acción no reconocida. Use: anular, eliminar_anulacion'
    ]);
}

// =====================================================
// CERRAR CONEXIÓN
// =====================================================

mysqli_close($conexion);
?>
