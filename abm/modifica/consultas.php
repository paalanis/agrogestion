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
	date_default_timezone_set("America/Argentina/Mendoza");
	$id_global = date("Ymdhis");	


$formulario=$_REQUEST['formulario'];
$id=$_REQUEST['id'];
// $formulario='2';
// $id='1';
	
switch ($formulario) {
	case '1': //bodega
		
 		$sql = "SELECT
 		tb_bodega.id_bodega as id,
		tb_bodega.razon_social as nombre,
		tb_bodega.obs as obs
		FROM
		tb_bodega
		WHERE
		tb_bodega.id_bodega = '$id'
		";
        $rs = mysqli_query($conexion, $sql);

        
        $datos = mysqli_fetch_array($rs);
        $array=array('success'=>'true');
        $datos = $datos + $array;

		echo json_encode($datos);
		
		break;
	
	case '2': //caudalimetro

		$sql = "SELECT
		tb_caudalimetro.id_caudalimetro AS id,
		tb_caudalimetro.nombre AS nombre,
		tb_caudalimetro.id_finca AS finca,
		tb_caudalimetro.caracteristicas AS caracteristicas,
		tb_caudalimetro.coef AS coef
		FROM
		tb_caudalimetro
		WHERE
		tb_caudalimetro.id_caudalimetro = '$id'
		";
        $rs = mysqli_query($conexion, $sql);

        $datos = mysqli_fetch_array($rs);
        $array=array('success'=>'true');
        $datos = $datos + $array;

		echo json_encode($datos);

		break;

	case '3': //cosechadora

		$sql = "SELECT
		tb_cosechadora.id_cosechadora AS id,
		tb_cosechadora.nombre AS nombre,
		tb_cosechadora.caracteristicas AS caracteristicas
		FROM
		tb_cosechadora
		WHERE
		tb_cosechadora.id_cosechadora = '$id'
		";
        $rs = mysqli_query($conexion, $sql);

        $datos = mysqli_fetch_array($rs);
        $array=array('success'=>'true');
        $datos = $datos + $array;

		echo json_encode($datos);

		break;

	case '4': //cuartel

		$sql = "SELECT
        tb_cuartel.id_cuartel as id,
        tb_cuartel.nombre as nombre,
        tb_cuartel.ano as anio,
        tb_cuartel.distancia as distancia,
        tb_cuartel.has as has,
        tb_cuartel.id_super AS mapeo,
        tb_cuartel.hileras as hileras
        FROM
        tb_cuartel
        WHERE
        tb_cuartel.id_cuartel = '$id'
		";
        $rs = mysqli_query($conexion, $sql);

        $datos = mysqli_fetch_array($rs);
        $array=array('success'=>'true');
        $datos = $datos + $array;

		echo json_encode($datos);

		break;

	case '5': //finca

		$sql = "SELECT
		tb_finca.id_finca AS id,
		tb_finca.rs AS rs,
		tb_finca.nombre AS nombre,
		tb_finca.localidad AS localidad,
		tb_finca.provincia AS provincia,
		tb_finca.has AS has
		FROM
		tb_finca
        WHERE
        tb_finca.id_finca = '$id'
		";
        $rs = mysqli_query($conexion, $sql);

        $datos = mysqli_fetch_array($rs);
        $array=array('success'=>'true');
        $datos = $datos + $array;

		echo json_encode($datos);

		break;

	case '6': //implemento

		$sql = "SELECT
		tb_implemento.id_implemento AS id,
		tb_implemento.nombre AS nombre,
		tb_implemento.caracteristicas AS caracteristicas
		FROM
		tb_implemento
        WHERE
        tb_implemento.id_implemento = '$id'
		";
        $rs = mysqli_query($conexion, $sql);

        $datos = mysqli_fetch_array($rs);
        $array=array('success'=>'true');
        $datos = $datos + $array;

		echo json_encode($datos);

		break;

	case '7': //insumo

		$sql = "SELECT
        tb_insumo.id_insumo as id,
        tb_insumo.nombre_comercial as nombre,
        tb_insumo.principio_activo as principio,
        tb_insumo.concentracion as concentracion
        FROM
        tb_insumo
        WHERE
        tb_insumo.id_insumo = '$id'
		";
        $rs = mysqli_query($conexion, $sql);

        $datos = mysqli_fetch_array($rs);
        $array=array('success'=>'true');
        $datos = $datos + $array;

		echo json_encode($datos);

		break;

	default:
		# code...
		break;
}















 } //fin else general



?>