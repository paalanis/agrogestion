<?php
$id_finca_elegida=$_POST['id_finca_elegida'];
include 'conexion.php';
$sqlfinca = "SELECT
		lower(tb_finca.nombre) AS finca_codigo,
		tb_deposito.nombre as deposito
		FROM
		tb_finca
		LEFT JOIN tb_deposito ON tb_deposito.id_deposito = tb_finca.id_deposito
              WHERE
              tb_finca.id_finca = '$id_finca_elegida'";
$rsfinca = mysqli_query($conexion, $sqlfinca); 
if (mysqli_num_rows($rsfinca) > 0){
$sql_finca = mysqli_fetch_array($rsfinca);
$finca_codigo= $sql_finca['finca_codigo'];
$deposito= $sql_finca['deposito'];
session_start();
$_SESSION['finca_usuario']=$finca_codigo;
$_SESSION['id_finca_usuario']=$id_finca_elegida;
$_SESSION['deposito']=$deposito;
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