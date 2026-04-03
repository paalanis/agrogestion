<?php
// TEST PURO - Sin includes, sin validaciones
echo "<h2>TEST bot-imagenes.php</h2>";
echo "<p>Session usuario: " . ($_SESSION['usuario'] ?? 'NO') . "</p>";
echo "<p>Session tipo_user: " . ($_SESSION['tipo_user'] ?? 'NO') . "</p>";

if (isset($_GET['modulo'])) {
  echo "<p>GET modulo: " . $_GET['modulo'] . "</p>";
}
if (isset($_GET['usuario'])) {
  echo "<p>GET usuario: " . $_GET['usuario'] . "</p>";
}

echo "<p>✓ Archivo cargado correctamente</p>";
?>

<div class="x_panel">
  <div class="x_title">
    <h2>📸 Imágenes del Bot WhatsApp</h2>
  </div>
  <div class="x_content">
    <p>Botón de prueba:</p>
    <button type="button" onclick="alert('Botón funcionando')">Test</button>
  </div>
</div>
