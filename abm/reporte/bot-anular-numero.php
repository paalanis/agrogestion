<?php
/**
 * bot-anular-numero.php
 * Ubicación: /abm/reporte/bot-anular-numero.php
 * 
 * Funcionalidad: Anular números de partes descartados
 */

// ==================== INICIALIZACIÓN CRÍTICA ====================
error_reporting(0);  // Suprimir warnings que generen salida extra
ini_set('display_errors', '0');

// Iniciar sesión ANTES de cualquier output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ESTABLECER HEADERS JSON INMEDIATAMENTE
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// ==================== VALIDACIÓN DE ACCESO ====================

if (!isset($_SESSION['tipo_user']) || $_SESSION['tipo_user'] !== 'admin') {
    http_response_code(403);
    die(json_encode([
        'ok' => false,
        'error' => 'Acceso denegado. Solo administradores pueden anular números.'
    ]));
}

// ==================== CONEXIÓN A BD ====================

// Verificar que el archivo de conexión existe
if (!file_exists('../../conexion/conexion.php')) {
    http_response_code(500);
    die(json_encode([
        'ok' => false,
        'error' => 'Archivo de configuración no encontrado'
    ]));
}

include '../../conexion/conexion.php';

// Validar que la función existe
if (!function_exists('conectarServidor')) {
    http_response_code(500);
    die(json_encode([
        'ok' => false,
        'error' => 'Función conectarServidor no disponible'
    ]));
}

$conexion = conectarServidor();

if (!$conexion) {
    http_response_code(500);
    die(json_encode([
        'ok' => false,
        'error' => 'Error de conexión a base de datos'
    ]));
}

// ==================== VALIDAR TABLA ====================

$tabla_check = mysqli_query($conexion, "SHOW TABLES LIKE 'tb_numeros_anulados'");
if (!$tabla_check || mysqli_num_rows($tabla_check) === 0) {
    http_response_code(500);
    die(json_encode([
        'ok' => false,
        'error' => 'Tabla tb_numeros_anulados no existe'
    ]));
}

// ==================== OBTENER PARÁMETROS ====================

$accion = isset($_POST['accion']) ? trim($_POST['accion']) : '';
$modulo = isset($_POST['modulo']) ? trim($_POST['modulo']) : '';
$numero_doc = isset($_POST['numero_doc']) ? intval($_POST['numero_doc']) : 0;
$motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';
$obs = isset($_POST['obs']) ? trim($_POST['obs']) : '';

// ==================== ACCIÓN: ANULAR ====================

if ($accion === 'anular') {
    
    // Validación exhaustiva
    if (empty($modulo) || $numero_doc <= 0 || empty($motivo)) {
        http_response_code(400);
        die(json_encode([
            'ok' => false,
            'error' => 'Parámetros inválidos o incompletos',
            'debug' => [
                'modulo' => $modulo ?: 'vacío',
                'numero_doc' => $numero_doc,
                'motivo' => $motivo ?: 'vacío'
            ]
        ]));
    }
    
    $semana = date('W');
    $anio = date('Y');
    $numero_formateado = str_pad($numero_doc, 6, '0', STR_PAD_LEFT);
    
    // Escapar string
    $modulo_esc = mysqli_real_escape_string($conexion, $modulo);
    $motivo_esc = mysqli_real_escape_string($conexion, $motivo);
    $obs_esc = mysqli_real_escape_string($conexion, $obs);
    $usuario_esc = mysqli_real_escape_string($conexion, $_SESSION['usuario'] ?? 'sistema');
    
    // Verificar si ya existe
    $qry = "SELECT id FROM tb_numeros_anulados 
            WHERE modulo = '$modulo_esc' 
            AND numero_doc = $numero_doc 
            AND semana = $semana 
            AND anio = $anio";
    
    $result = mysqli_query($conexion, $qry);
    
    if ($result === false) {
        http_response_code(500);
        die(json_encode([
            'ok' => false,
            'error' => 'Error en consulta de verificación: ' . mysqli_error($conexion),
            'query' => $qry
        ]));
    }
    
    if (mysqli_num_rows($result) > 0) {
        http_response_code(409);
        die(json_encode([
            'ok' => false,
            'error' => "El número $numero_formateado ya está anulado esta semana"
        ]));
    }
    
    // Insertar
    $qry = "INSERT INTO tb_numeros_anulados 
            (modulo, numero_doc, numero_formateado, semana, anio, usuario_anulo, motivo, obs)
            VALUES ('$modulo_esc', $numero_doc, '$numero_formateado', $semana, $anio, '$usuario_esc', '$motivo_esc', '$obs_esc')";
    
    if (mysqli_query($conexion, $qry)) {
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
        die(json_encode([
            'ok' => false,
            'error' => 'Error al insertar: ' . mysqli_error($conexion),
            'query' => $qry
        ]));
    }

// ==================== ACCIÓN: ELIMINAR ANULACIÓN ====================

} elseif ($accion === 'eliminar_anulacion') {
    
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if ($id <= 0) {
        http_response_code(400);
        die(json_encode([
            'ok' => false,
            'error' => 'ID inválido'
        ]));
    }
    
    // Obtener datos antes de eliminar
    $qry = "SELECT modulo, numero_formateado FROM tb_numeros_anulados WHERE id = $id";
    $result = mysqli_query($conexion, $qry);
    
    if (!$result || mysqli_num_rows($result) === 0) {
        http_response_code(404);
        die(json_encode([
            'ok' => false,
            'error' => 'Anulación no encontrada'
        ]));
    }
    
    $row = mysqli_fetch_assoc($result);
    $numero_fmt = $row['numero_formateado'];
    
    // Eliminar
    $qry = "DELETE FROM tb_numeros_anulados WHERE id = $id";
    
    if (mysqli_query($conexion, $qry)) {
        http_response_code(200);
        echo json_encode([
            'ok' => true,
            'mensaje' => "✓ Anulación del número $numero_fmt revertida"
        ]);
    } else {
        http_response_code(500);
        die(json_encode([
            'ok' => false,
            'error' => 'Error al eliminar: ' . mysqli_error($conexion)
        ]));
    }

// ==================== ACCIÓN NO RECONOCIDA ====================

} else {
    http_response_code(400);
    die(json_encode([
        'ok' => false,
        'error' => 'Acción no reconocida',
        'accion_recibida' => $accion,
        'acciones_validas' => ['anular', 'eliminar_anulacion']
    ]));
}

mysqli_close($conexion);
?>
