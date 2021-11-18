<?php
error_reporting(E_ALL ^ E_NOTICE);
session_start();
$dbhost=$_SESSION['db_host'];
$dbuser=$_SESSION['db_user'];
$dbpassword=$_SESSION['db_pass'];
$database=$_SESSION['database'];
$conexion=@mysqli_connect($dbhost,$dbuser,$dbpassword,$database);
if (mysqli_connect_errno()) {
printf("La conexion con el servidor de base de datos fallo (en la conexion 2): %s\n", mysqli_connect_error());
exit();
}
?>