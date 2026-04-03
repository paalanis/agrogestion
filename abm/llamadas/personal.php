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
$conexion = conectarServidor();

$centroCosto = $_SESSION['centroCosto'];
$seleccion=$_POST['seleccion'];

if ($seleccion == '0'){
 $sqlpersonal = "SELECT
                tb_personalnueva.id_personal as id_personal,
                CONCAT(tb_personalnueva.apellido, ', ',tb_personalnueva.nombre) AS personal
                FROM
                tb_personalnueva
                WHERE
                tb_personalnueva.id_centro_costo = '$centroCosto' AND tb_personalnueva.activo = '1'
                ORDER BY
                personal ASC";
 $rspersonal = mysqli_query($conexion, $sqlpersonal);}

if ($seleccion == '1'){
  $sqlpersonal = "SELECT
                 tb_personalnueva.id_personal as id_personal,
                 CONCAT(tb_personalnueva.apellido, ', ',tb_personalnueva.nombre) AS personal
                 FROM
                 tb_personalnueva
                 WHERE
                 tb_personalnueva.eventual = '$seleccion' AND tb_personalnueva.activo = '1'
                 ORDER BY
                 personal ASC";
  $rspersonal = mysqli_query($conexion, $sqlpersonal);}

 $cantidad =  mysqli_num_rows($rspersonal);

?>


<select class="form-control" id="dato_personal" required>   
  <option value=""></option>
  <?php
  
  if ($cantidad > 0) { // si existen cuarteles con de esa finca se muestran, de lo contrario queda en blanco  

  while ($sql_personal = mysqli_fetch_array($rspersonal)){
    $idpersonal= $sql_personal['id_personal'];
    $personal = $sql_personal['personal'];

    echo utf8_encode('<option value='.$idpersonal.'>'.$personal.'</option>');
    
  }
  }else{
    echo utf8_encode('<option v>Sin personal</option>');
  }


  ?>
</select>