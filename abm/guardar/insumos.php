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
	$principio_activo=utf8_decode($_REQUEST['dato_principio']);
	$concentracion=$_REQUEST['dato_concentracion'];
	$unidad=$_REQUEST['dato_umedida'];
	$tipo_insumo=$_REQUEST['dato_tipo'];
	
	if ($id == '0') {

	mysqli_select_db($conexion,$database);
	$sql = "INSERT INTO tb_insumo (id_unidad, id_tipo_insumo, nombre_comercial, principio_activo, concentracion)
	VALUES ('$unidad', '$tipo_insumo', '$nombre', '$principio_activo', '$concentracion')";
	mysqli_query($conexion,$sql);    

	$array=array('success'=>'true');
	echo json_encode($array);

	}else{

    mysqli_select_db($conexion,$database);
	$sql = "UPDATE tb_insumo 
	SET id_unidad = '$unidad',
		id_tipo_insumo = '$tipo_insumo',
		nombre_comercial = '$nombre',
		principio_activo = '$principio_activo',
		concentracion = '$concentracion'
	WHERE tb_insumo.id_insumo = '$id'";
	mysqli_query($conexion,$sql);    

	$array=array('success'=>'true');
	echo json_encode($array);

	}	

} //fin else
?>