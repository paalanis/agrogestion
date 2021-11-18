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
// if (mysqli_connect_errno()) {
// $array=array('success'=>'false');
// echo json_encode($array);
// exit();
// }else{
$fecha=$_REQUEST['dato_fecha'];
$insumo=$_REQUEST['dato_insumo'];
$cantidad=$_REQUEST['dato_cantidad'];
$sqlsaldo = "SELECT
				tb_consumo_insumos_".$deposito.".id_consumo_insumos AS id,
				tb_consumo_insumos_".$deposito.".saldo AS saldo
				FROM
				tb_consumo_insumos_".$deposito."
				WHERE
				tb_consumo_insumos_".$deposito.".id_insumo = '$insumo'
				ORDER BY
				tb_consumo_insumos_".$deposito.".id_consumo_insumos DESC
				LIMIT 1";
$rssaldo = mysqli_query($conexion, $sqlsaldo);
$datos = mysqli_fetch_array($rssaldo);
$saldo=utf8_encode($datos['saldo']);
$saldo = $saldo - $cantidad;
mysqli_select_db($conexion,'$database');
$sql = "INSERT INTO tb_consumo_insumos_".$deposito." (id_insumo, fecha, egreso, saldo)
VALUES ('$insumo', '$fecha', '$cantidad', '$saldo')";
mysqli_query($conexion,$sql);    
$array=array('success'=>'true');
echo json_encode($array);
// }
?>