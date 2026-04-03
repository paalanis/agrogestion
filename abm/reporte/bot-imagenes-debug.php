<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

echo "=== DEBUG bot-imagenes.php ===\n\n";

echo "1. SESSION vars:\n";
var_dump($_SESSION);
echo "\n";

echo "2. Chequeando includes:\n";

echo "   - Intentando: ../conexion/conexion.php ... ";
if (file_exists('../conexion/conexion.php')) {
  echo "✓ OK\n";
  include '../conexion/conexion.php';
} else {
  echo "✗ NO ENCONTRADO\n";
  die("ERROR: No se encuentra ../conexion/conexion.php");
}

echo "   - Intentando: ../db/querys.php ... ";
if (file_exists('../db/querys.php')) {
  echo "✓ OK\n";
  include '../db/querys.php';
} else {
  echo "⚠ NO ENCONTRADO (optional)\n";
}

echo "\n3. Chequeando conexión a BD:\n";
if (function_exists('conectarServidor')) {
  echo "   - Función conectarServidor() existe ✓\n";
  try {
    $conexion = conectarServidor();
    echo "   - Conexión a BD: ✓ OK\n";
  } catch (Exception $e) {
    echo "   - Conexión a BD: ✗ ERROR\n";
    echo "   " . $e->getMessage() . "\n";
    die();
  }
} else {
  echo "   - Función conectarServidor() NO EXISTE\n";
  die("ERROR: No se puede conectar a BD");
}

echo "\n4. Chequeando queries:\n";

echo "   - Query módulos ... ";
$query_modulos = "SELECT DISTINCT modulo FROM business_records WHERE modulo IS NOT NULL ORDER BY modulo";
$result_modulos = mysqli_query($conexion, $query_modulos);
if ($result_modulos) {
  echo "✓ OK (" . mysqli_num_rows($result_modulos) . " filas)\n";
} else {
  echo "✗ ERROR\n";
  echo "   " . mysqli_error($conexion) . "\n";
}

echo "\n5. Chequeando business_records table:\n";
$query_count = "SELECT COUNT(*) as total FROM business_records";
$result_count = mysqli_query($conexion, $query_count);
if ($result_count) {
  $row = mysqli_fetch_assoc($result_count);
  echo "   - Total registros: " . $row['total'] . "\n";
} else {
  echo "   - ERROR: " . mysqli_error($conexion) . "\n";
}

echo "\n=== FIN DEBUG ===\n";
echo "\nSi no ves errores arriba, el problema está en otro lado.\n";
echo "Abre la consola del navegador (F12) y mira la pestaña 'Network' para ver la respuesta completa.\n";

// Ahora cargar el HTML normal si no hay errores
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>DEBUG</title>
</head>
<body>
  <pre>
  DEBUG completado. Revisa arriba los errores.
  </pre>
</body>
</html>
