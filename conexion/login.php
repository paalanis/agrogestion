<?php
$usuario=$_POST['usuario'];
$pass=$_POST['pass'];
if (isset($usuario) && isset($pass)) {
$dbhost = 'localhost';
$dbuser = 'apsscom_sacsa';
$dbpassword = '.phxpAY9_gbD';
$database = 'apsscom_sacsa';
$conexion = @mysqli_connect($dbhost,$dbuser,$dbpassword,$database);
$sqlusuario = "SELECT
			tb_usuarios.id_finca AS id_finca_usuario,
			lower(replace(tb_finca.nombre, ' ', '')) AS finca,
			tb_usuarios.db_host AS db_host,
			tb_usuarios.db_user AS db_user,
			tb_usuarios.db_pass AS db_pass,
			tb_usuarios.data_base AS data_base,
			tb_usuarios.tipo_user AS tipo_user,
			tb_usuarios.nom_ape AS nom_ape,
			tb_deposito.nombre as deposito
			FROM
			tb_usuarios
			LEFT JOIN tb_finca ON tb_usuarios.id_finca = tb_finca.id_finca
			LEFT JOIN tb_deposito ON tb_deposito.id_deposito = tb_finca.id_deposito
			WHERE
            tb_usuarios.nombre = '$usuario' AND
            tb_usuarios.pass = '$pass'
            ";
$rsusuario = mysqli_query($conexion, $sqlusuario);            
if (mysqli_num_rows($rsusuario) > 0) {
$sql_usuario = mysqli_fetch_array($rsusuario);
$finca_usuario= $sql_usuario['finca'];
$id_finca_usuario= $sql_usuario['id_finca_usuario'];
$db_host= $sql_usuario['db_host'];
$db_user= $sql_usuario['db_user'];
$db_pass= $sql_usuario['db_pass'];
$data_base= $sql_usuario['data_base'];
$deposito= $sql_usuario['deposito'];
$tipo_user=$sql_usuario['tipo_user'];
$nom_ape=$sql_usuario['nom_ape'];
session_start();
$_SESSION['usuario']=$usuario;
$_SESSION['finca_usuario']=$finca_usuario;
$_SESSION['id_finca_usuario']=$id_finca_usuario;
$_SESSION['db_host']=$db_host;
$_SESSION['db_user']=$db_user;
$_SESSION['db_pass']=$db_pass;
$_SESSION['database']=$data_base;
$_SESSION['deposito']=$deposito;
$_SESSION['tipo_user']=$tipo_user;
$_SESSION['nom_ape']=$nom_ape;
if ($id_finca_usuario == '0') {
?>
<script type="text/javascript">
window.location="../index_finca.php"
</script>
<?php
}else{
?>
<script type="text/javascript">
window.location="../index2.php"
</script>
<?php
}
}else{
?>
<script type="text/javascript">
window.location= "../error.php"
</script>
<?php
}
}
?>
<script src="js/jquery.min.js"></script>