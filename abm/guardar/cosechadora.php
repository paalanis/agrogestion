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

	$nombre=utf8_decode($_REQUEST['dato_nombre']);
	$caracteristicas=utf8_decode($_REQUEST['dato_caracteristicas']);
	$propia=utf8_decode($_REQUEST['dato_propia']);
	$id=$_REQUEST['dato_id'];


	if ($id == '0') {

		mysqli_select_db($conexion,$database);
		$sql = "INSERT INTO tb_cosechadora (nombre, caracteristicas, propia)
		VALUES ('$nombre', '$caracteristicas', '$propia')";
		mysqli_query($conexion,$sql);    

		$array=array('success'=>'true');
		echo json_encode($array);

			
		}else{

        mysqli_select_db($conexion,$database);
		$sql = "UPDATE tb_cosechadora 
		SET nombre = '$nombre',
			caracteristicas = '$caracteristicas',
			propia = '$propia'
		WHERE tb_cosechadora.id_cosechadora = '$id'";
		mysqli_query($conexion,$sql);    

		$array=array('success'=>'true');
		echo json_encode($array);

		}	

} //fin else
?>