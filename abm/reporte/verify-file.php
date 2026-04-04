<?php
/**
 * verify-file.php
 * Ubicación: /abm/reporte/verify-file.php
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== VERIFICACIÓN DE ARCHIVOS EN WIROOS ===\n\n";

// Archivo 1: bot-anular-numero.php
echo "1. bot-anular-numero.php\n";
echo "   Existe: " . (file_exists('bot-anular-numero.php') ? 'SÍ' : 'NO') . "\n";
if (file_exists('bot-anular-numero.php')) {
    $lines = file('bot-anular-numero.php');
    echo "   Línea 1: " . trim($lines[0]) . "\n";
    echo "   Línea 25: " . trim($lines[24] ?? 'N/A') . "\n";
    echo "   Línea 30: " . trim($lines[29] ?? 'N/A') . "\n";
    echo "   Total líneas: " . count($lines) . "\n";
    echo "   Tamaño: " . filesize('bot-anular-numero.php') . " bytes\n";
    echo "   Última modificación: " . date('Y-m-d H:i:s', filemtime('bot-anular-numero.php')) . "\n";
}

echo "\n2. bot-anular-parte.php\n";
echo "   Existe: " . (file_exists('bot-anular-parte.php') ? 'SÍ' : 'NO') . "\n";
if (file_exists('bot-anular-parte.php')) {
    $lines = file('bot-anular-parte.php');
    echo "   Línea 1: " . trim($lines[0]) . "\n";
    echo "   Total líneas: " . count($lines) . "\n";
}

echo "\n3. Archivos en /abm/reporte/\n";
$files = scandir('.');
$php_files = array_filter($files, function($f) { return strpos($f, '.php') !== false; });
foreach ($php_files as $f) {
    echo "   - $f (" . filesize($f) . " bytes)\n";
}

echo "\n4. Verificación de sesión y REQUEST\n";
echo "   \$_SERVER['REQUEST_METHOD']: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . "\n";
echo "   \$_SERVER['CONTENT_TYPE']: " . ($_SERVER['CONTENT_TYPE'] ?? 'N/A') . "\n";
echo "   session_status: " . session_status() . "\n";

session_start();
echo "   \$_SESSION['tipo_user']: " . ($_SESSION['tipo_user'] ?? 'NO SET') . "\n";
echo "   \$_SESSION['usuario']: " . ($_SESSION['usuario'] ?? 'NO SET') . "\n";

?>
