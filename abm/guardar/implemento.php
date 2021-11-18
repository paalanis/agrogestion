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
	$nombre=utf8_decode($_REQUEST['dato_nombre']);
	$caracteristicas=utf8_decode($_REQUEST['dato_caracteristicas']);
	
	if ($id == '0') {

	mysqli_select_db($conexion,$database);
	$sql = "INSERT INTO tb_implemento (caracteristicas, nombre)
	VALUES (lower('$caracteristicas'), '$nombre')";
	mysqli_query($conexion,$sql);    


	$array=array('success'=>'true');
	echo json_encode($array);

	}else{

    mysqli_select_db($conexion,$database);
	$sql = "UPDATE tb_implemento 
	SET nombre = '$nombre',
		caracteristicas = lower('$caracteristicas')
	WHERE tb_implemento.id_implemento = '$id'";
	mysqli_query($conexion,$sql);    

	$array=array('success'=>'true');
	echo json_encode($array);

	}	

} //fin else
?>