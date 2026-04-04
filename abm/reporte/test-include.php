<?php
/**
 * test-include.php
 * Ubicación: /abm/reporte/test-include.php
 * 
 * Prueba SOLO el include de conexion.php
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: application/json; charset=utf-8');

$debug = [];

// Test 1: Ver directorio actual
$debug['directorio_actual'] = getcwd();

// Test 2: Listar archivos en directorio actual
$debug['archivos_actuales'] = array_slice(scandir('.'), 2, 10);

// Test 3: Ver si existe ../../conexion/conexion.php
$ruta = '../../conexion/conexion.php';
$debug['ruta_buscada'] = $ruta;
$debug['ruta_absoluta'] = realpath($ruta) ?: 'NO EXISTE';
$debug['existe'] = file_exists($ruta) ? 'SÍ' : 'NO';

// Test 4: Intentar listar directorios hacia arriba
$debug['directorio_padre'] = realpath('..') ?: 'NO EXISTE';
$debug['directorio_abuelo'] = realpath('../..') ?: 'NO EXISTE';

// Test 5: Intentar ver si existe la carpeta conexion
$debug['carpeta_conexion'] = realpath('../../conexion') ?: 'NO EXISTE';

// Test 6: Listar archivos en ../../ 
$abuelo = realpath('../..');
if ($abuelo && is_dir($abuelo)) {
    $debug['archivos_en_abuelo'] = array_slice(scandir($abuelo), 2, 15);
}

// Test 7: Intentar include
if (file_exists($ruta)) {
    include $ruta;
    $debug['include'] = 'SUCCESS';
    $debug['funcion_disponible'] = function_exists('conectarServidor') ? 'SÍ' : 'NO';
} else {
    $debug['include'] = 'FAILED - Archivo no existe';
}

echo json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
