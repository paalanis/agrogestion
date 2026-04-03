<?php
$id_finca_elegida=$_POST['id_finca_elegida'];

include 'conexion.php';
$conexion = conectarServidor();

$sqlfinca = "SELECT
				lower(tb_finca.nombre) AS nombre, 
				tb_deposito.nombre AS deposito, 
				tb_finca.id_centro_costo AS centroCosto
			FROM
				tb_finca
			LEFT JOIN
				tb_deposito
			ON 
				tb_deposito.id_deposito = tb_finca.id_deposito
			WHERE
				tb_finca.id_finca = '$id_finca_elegida'";
$rsfinca = mysqli_query($conexion, $sqlfinca);

if (mysqli_num_rows($rsfinca) > 0){
	$sql_finca = mysqli_fetch_array($rsfinca);
	$finca_nombre= $sql_finca['nombre'];
	$deposito= $sql_finca['deposito'];
	$centroCosto= $sql_finca['centroCosto'];
	session_start();
	$_SESSION['finca_usuario']=$finca_nombre;
	$_SESSION['id_finca_usuario']=$id_finca_elegida;
	$_SESSION['deposito']=$deposito;
	$_SESSION['centroCosto']=$centroCosto;
	?>
		<script type="text/javascript">
		window.location="../index2.php"
		</script>
	<?php
}else{
	session_destroy();
	header("Location: ../index.php");
}
?>
<script src="js/jquery.min.js"></script>