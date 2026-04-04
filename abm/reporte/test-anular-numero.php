<?php
/**
 * test-anular-numero.php
 * Ubicación: /abm/reporte/test-anular-numero.php
 * 
 * Script de diagnóstico para encontrar el error en bot-anular-numero.php
 * IMPORTANTE: Ejecutar una sola vez, luego borrar
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

$debug = [];

// TEST 1: Verificar sesión
$debug['test1_sesion'] = [
    'tipo_user' => $_SESSION['tipo_user'] ?? 'NO SET',
    'usuario' => $_SESSION['usuario'] ?? 'NO SET',
    'es_admin' => ($_SESSION['tipo_user'] === 'admin') ? 'SÍ' : 'NO'
];

// TEST 2: Verificar que el include funciona
$debug['test2_conexion'] = [
    'include_path' => '../../conexion/conexion.php',
    'file_exists' => file_exists('../../conexion/conexion.php') ? 'SÍ' : 'NO'
];

if (file_exists('../../conexion/conexion.php')) {
    include '../../conexion/conexion.php';
    $conexion = conectarServidor();
    $debug['test2_conexion']['conexion_ok'] = $conexion ? 'SÍ' : 'NO';
    if ($conexion) {
        $debug['test2_conexion']['mysqli_error'] = mysqli_error($conexion) ?: 'Ninguno';
    }
} else {
    $debug['test2_conexion']['conexion_ok'] = 'NO - Archivo no encontrado';
}

// TEST 3: Verificar tabla tb_numeros_anulados
if ($conexion) {
    $result = mysqli_query($conexion, "SHOW TABLES LIKE 'tb_numeros_anulados'");
    if ($result && mysqli_num_rows($result) > 0) {
        $debug['test3_tabla'] = [
            'existe' => 'SÍ',
            'estructura' => []
        ];
        
        // Obtener estructura
        $fields = mysqli_query($conexion, "DESCRIBE tb_numeros_anulados");
        while ($row = mysqli_fetch_assoc($fields)) {
            $debug['test3_tabla']['estructura'][] = $row['Field'];
        }
    } else {
        $debug['test3_tabla'] = [
            'existe' => 'NO',
            'error' => mysqli_error($conexion)
        ];
    }
}

// TEST 4: Simular la acción 'anular' sin insertar
if ($conexion) {
    $modulo = 'ALTAMIRA';
    $numero_doc = 2;
    $semana = date('W');
    $anio = date('Y');
    
    $query = sprintf(
        "SELECT id FROM tb_numeros_anulados WHERE modulo = '%s' AND numero_doc = %d AND semana = %d AND anio = %d",
        mysqli_real_escape_string($conexion, $modulo),
        $numero_doc,
        $semana,
        $anio
    );
    
    $result = mysqli_query($conexion, $query);
    $debug['test4_query_check'] = [
        'query' => $query,
        'resultado' => $result ? 'OK' : 'ERROR',
        'error' => mysqli_error($conexion) ?: 'Ninguno',
        'ya_existe' => ($result && mysqli_num_rows($result) > 0) ? 'SÍ' : 'NO'
    ];
}

// TEST 5: Verificar POST
$debug['test5_post'] = [
    'accion' => $_POST['accion'] ?? 'NO SET',
    'modulo' => $_POST['modulo'] ?? 'NO SET',
    'numero_doc' => $_POST['numero_doc'] ?? 'NO SET',
    'motivo' => $_POST['motivo'] ?? 'NO SET'
];

if ($conexion) {
    mysqli_close($conexion);
}

// Devolver diagnóstico como JSON
echo json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
