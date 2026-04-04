<?php
/**
 * simple-test.php
 * Ubicación: /abm/reporte/simple-test.php
 * 
 * Prueba SIMPLE: Solo JSON, sin includes, sin nada
 * Si esto funciona, el problema está en el include
 */

error_reporting(0);
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'ok' => true,
    'mensaje' => 'Servidor funciona - JSON devuelto correctamente',
    'timestamp' => date('Y-m-d H:i:s'),
    'archivo' => __FILE__,
    'php_version' => phpversion()
]);
?>
