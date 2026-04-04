<?php
error_reporting(0);
ini_set('display_errors', '0');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

// Validar admin
if (!isset($_SESSION['tipo_user']) || $_SESSION['tipo_user'] !== 'admin') {
    http_response_code(403);
    die(json_encode(['ok' => false, 'error' => 'No autorizado']));
}

// Incluir conexión
if (!file_exists('../../conexion/conexion.php')) {
    http_response_code(500);
    die(json_encode(['ok' => false, 'error' => 'Conexión no disponible']));
}

include '../../conexion/conexion.php';
$conexion = conectarServidor();

if (!$conexion) {
    http_response_code(500);
    die(json_encode(['ok' => false, 'error' => 'Error BD']));
}

// Obtener parámetros
$accion = isset($_POST['accion']) ? trim($_POST['accion']) : '';
$modulo = isset($_POST['modulo']) ? trim($_POST['modulo']) : '';
$numero_doc = isset($_POST['numero_doc']) ? intval($_POST['numero_doc']) : 0;
$motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';
$obs = isset($_POST['obs']) ? trim($_POST['obs']) : '';

// ANULAR
if ($accion === 'anular') {
    
    if (empty($modulo) || $numero_doc <= 0 || empty($motivo)) {
        http_response_code(400);
        die(json_encode(['ok' => false, 'error' => 'Datos incompletos']));
    }
    
    $semana = date('W');
    $anio = date('Y');
    $numero_fmt = str_pad($numero_doc, 6, '0', STR_PAD_LEFT);
    
    $mod_esc = mysqli_real_escape_string($conexion, $modulo);
    $mot_esc = mysqli_real_escape_string($conexion, $motivo);
    $obs_esc = mysqli_real_escape_string($conexion, $obs);
    $usr_esc = mysqli_real_escape_string($conexion, $_SESSION['usuario'] ?? 'sistema');
    
    // Verificar si existe
    $sql = "SELECT id FROM tb_numeros_anulados WHERE modulo='$mod_esc' AND numero_doc=$numero_doc AND semana=$semana AND anio=$anio";
    $res = mysqli_query($conexion, $sql);
    
    if ($res === false) {
        http_response_code(500);
        die(json_encode(['ok' => false, 'error' => 'Error query']));
    }
    
    if (mysqli_num_rows($res) > 0) {
        http_response_code(409);
        die(json_encode(['ok' => false, 'error' => 'Ya anulado']));
    }
    
    // Insertar
    $sql = "INSERT INTO tb_numeros_anulados (modulo, numero_doc, numero_formateado, semana, anio, usuario_anulo, motivo, obs) 
            VALUES ('$mod_esc', $numero_doc, '$numero_fmt', $semana, $anio, '$usr_esc', '$mot_esc', '$obs_esc')";
    
    if (mysqli_query($conexion, $sql)) {
        http_response_code(201);
        echo json_encode(['ok' => true, 'mensaje' => "✓ Número $numero_fmt anulado"]);
    } else {
        http_response_code(500);
        die(json_encode(['ok' => false, 'error' => 'Error insert']));
    }

// ELIMINAR ANULACIÓN
} elseif ($accion === 'eliminar_anulacion') {
    
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if ($id <= 0) {
        http_response_code(400);
        die(json_encode(['ok' => false, 'error' => 'ID inválido']));
    }
    
    $sql = "SELECT numero_formateado FROM tb_numeros_anulados WHERE id=$id";
    $res = mysqli_query($conexion, $sql);
    
    if (!$res || mysqli_num_rows($res) === 0) {
        http_response_code(404);
        die(json_encode(['ok' => false, 'error' => 'No existe']));
    }
    
    $row = mysqli_fetch_assoc($res);
    $num_fmt = $row['numero_formateado'];
    
    $sql = "DELETE FROM tb_numeros_anulados WHERE id=$id";
    
    if (mysqli_query($conexion, $sql)) {
        http_response_code(200);
        echo json_encode(['ok' => true, 'mensaje' => "✓ Revertido: $num_fmt"]);
    } else {
        http_response_code(500);
        die(json_encode(['ok' => false, 'error' => 'Error delete']));
    }

} else {
    http_response_code(400);
    die(json_encode(['ok' => false, 'error' => 'Acción inválida']));
}

mysqli_close($conexion);
?>
