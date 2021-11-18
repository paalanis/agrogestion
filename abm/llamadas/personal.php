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
$finca=$_POST['finca'];
$seleccion=$_POST['seleccion'];

 $sqlpersonal = "SELECT
                tb_personal.id_personal as id_personal,
                CONCAT(tb_personal.apellido, ', ',tb_personal.nombre) AS personal
                FROM
                tb_personal
                WHERE
                tb_personal.id_finca = '$finca' AND
                tb_personal.eventual = '$seleccion'
                ORDER BY
                personal ASC";
 $rspersonal = mysqli_query($conexion, $sqlpersonal); 

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