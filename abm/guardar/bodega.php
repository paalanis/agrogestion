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
   
	$array=array('success'=>'false');
	echo json_encode($array);
 
	exit();

}else{

	$id=utf8_decode($_REQUEST['dato_id']);
	$rs=utf8_decode($_REQUEST['dato_nombre']);
	$obs=utf8_decode($_REQUEST['dato_obs']);


	if ($id == '0') {

		mysqli_select_db($conexion,$database);
		$sql = "INSERT INTO tb_bodega (razon_social, obs)
		VALUES ('$rs', '$obs')";
		mysqli_query($conexion,$sql);    

		$array=array('success'=>'true');
		echo json_encode($array);

			
		}else{

        mysqli_select_db($conexion,$database);
		$sql = "UPDATE tb_bodega 
		SET razon_social = '$rs',
			obs = '$obs'
		WHERE tb_bodega.id_bodega = '$id'";
		mysqli_query($conexion,$sql);    

		$array=array('success'=>'true');
		echo json_encode($array);

		}	

} //fin else
?>