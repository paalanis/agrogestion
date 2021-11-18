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
	$rs=utf8_decode($_REQUEST['dato_rs']);
	$nombre=utf8_decode($_REQUEST['dato_nombre']);
	$localidad=utf8_decode($_REQUEST['dato_localidad']);
	$provincia=utf8_decode($_REQUEST['dato_provincia']);
	$has=$_REQUEST['dato_has'];
	$deposito=utf8_decode($_REQUEST['dato_deposito']);
	$centrocosto=utf8_decode($_REQUEST['dato_centrocosto']);
	
	if ($id == '0') {

	mysqli_select_db($conexion,$database);
	$sql = "INSERT INTO tb_finca (rs, nombre, localidad, provincia, has, id_deposito, id_centro_costo)
	VALUES ('$rs','$nombre', '$localidad', '$provincia', '$has', '$deposito', '$centrocosto')";
	mysqli_query($conexion,$sql);    


	$array=array('success'=>'true');
	echo json_encode($array);

	}else{

    mysqli_select_db($conexion,$database);
	$sql = "UPDATE tb_finca 
	SET rs = '$rs',
		nombre = '$nombre',
		localidad = '$localidad',
		provincia = '$provincia',	
		has = '$has',
		id_deposito = '$deposito',
		id_centro_costo = '$centrocosto'
	WHERE tb_finca.id_finca = '$id'";
	mysqli_query($conexion,$sql);    

	$array=array('success'=>'true');
	echo json_encode($array);

	}	

} //fin else
?>