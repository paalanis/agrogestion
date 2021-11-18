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
	$finca=$_REQUEST['dato_finca'];
	$caracteristicas=utf8_decode($_REQUEST['dato_caracteristicas']);
	$coef=utf8_decode($_REQUEST['dato_coef']);
	$dilucion=utf8_decode($_REQUEST['dato_dilucion']);
	
	if ($id == '0') {

	mysqli_select_db($conexion,$database);
	$sql = "INSERT INTO tb_caudalimetro (id_finca, nombre, caracteristicas, dilucion, coef)
	VALUES ('$finca', '$nombre', lower('$caracteristicas'), '$dilucion', '$coef')";
	mysqli_query($conexion,$sql);    


	$array=array('success'=>'true');
	echo json_encode($array);

	}else{

    mysqli_select_db($conexion,$database);
	$sql = "UPDATE tb_caudalimetro 
	SET id_finca = '$finca',
		nombre = '$nombre',
		caracteristicas = lower('$caracteristicas'),
		dilucion = '$dilucion',
		coef = '$coef'
	WHERE tb_caudalimetro.id_caudalimetro = '$id'";
	mysqli_query($conexion,$sql);    

	$array=array('success'=>'true');
	echo json_encode($array);

	}	

} //fin else
?>