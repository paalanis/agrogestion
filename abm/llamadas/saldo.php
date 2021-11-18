<?php
session_start();
if (!isset($_SESSION['usuario'])) {
header("Location: ../../index.php");
}
if ($_SESSION['id_finca_usuario'] == '0') {
session_destroy();
header("Location: ../../index.php");
}
$deposito=$_SESSION['deposito'];
include '../../conexion/conexion.php';
if (mysqli_connect_errno()) {
printf("La conexión con el servidor de base de datos falló comuniquese con su administrador: %s\n", mysqli_connect_error());
exit();
}
$insumo=$_REQUEST['insumo'];

if ($insumo == "") {
	?>
	<script type="text/javascript">
	$("#div_saldo").html("")
	</script>
	<?php
}else{
$sqlsaldo = "SELECT
				tb_consumo_insumos_".$deposito.".id_consumo_insumos AS id,
				tb_consumo_insumos_".$deposito.".saldo AS saldo,
				tb_unidad.nombre as unidad
				FROM
				tb_consumo_insumos_".$deposito."
				INNER JOIN tb_insumo ON tb_consumo_insumos_".$deposito.".id_insumo = tb_insumo.id_insumo
				INNER JOIN tb_unidad ON tb_insumo.id_unidad = tb_unidad.id_unidad
				WHERE
				tb_consumo_insumos_".$deposito.".id_insumo = '$insumo'
				ORDER BY
				tb_consumo_insumos_".$deposito.".id_consumo_insumos DESC
				LIMIT 1";
$rssaldo = mysqli_query($conexion, $sqlsaldo); 
$cantidad =  mysqli_num_rows($rssaldo);
if ($cantidad > 0) { 
$datos = mysqli_fetch_array($rssaldo);
$saldo=utf8_encode($datos['saldo']);
$unidad=utf8_encode($datos['unidad']);

echo '<span class="help-block">Saldo actual: '.$saldo.' '.$unidad.'</span>';
}else{
$saldo = 0;
$unidad = "";
echo '<span class="help-block">Saldo actual: '.$saldo.' '.$unidad.'</span>';
}
}
?>

