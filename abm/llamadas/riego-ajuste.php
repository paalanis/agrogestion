<?php
session_start();
if (!isset($_SESSION['usuario'])) {
header("Location: ../../index.php");
}
if ($_SESSION['id_finca_usuario'] == '0') {
session_destroy();
header("Location: ../../index.php");
}
include '../../conexion/conexion.php';
if (mysqli_connect_errno()) {
printf("La conexión con el servidor de base de datos falló comuniquese con su administrador: %s\n", mysqli_connect_error());
exit();
}
$caudalimetro=$_POST['caudalimetro'];
$sqlcoef = "SELECT
			tb_caudalimetro.coef as coef
			FROM tb_caudalimetro
			WHERE
			tb_caudalimetro.id_caudalimetro = '$caudalimetro'";
$rscoef = mysqli_query($conexion, $sqlcoef);
$datos = mysqli_fetch_array($rscoef);
$coef=$datos['coef'];

echo '<input type="text" class="form-control" value="'.$coef.'" id="riego_coef" readonly aria-describedby="basic-addon1" required>';

?>




