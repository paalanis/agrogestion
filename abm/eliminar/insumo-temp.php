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
$array=array('success'=>'false');
echo json_encode($array);
exit();
}else{
$id=$_REQUEST['id'];
mysqli_select_db($conexion,'$database');
$sql = "DELETE FROM tb_consumo_insumos_".$deposito." WHERE id_consumo_insumos = '$id'";
mysqli_query($conexion,$sql);
$array=array('success'=>'true');
echo json_encode($array);
}
?>