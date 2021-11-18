<?php
session_start();
if (!isset($_SESSION['usuario'])) {
header("Location: index.php");
}
include 'conexion/conexion.php';
$sqlfinca = "SELECT
              tb_finca.id_finca as id_finca,
              tb_finca.nombre as finca,
              lower(replace(tb_finca.nombre, ' ', '')) as finca_codigo
              FROM
              tb_finca";
$rsfinca = mysqli_query($conexion, $sqlfinca); 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="">  <!-- logo -->

    <title>Gestion Fincas</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">

  
  </head>

  <body style="background-color: rgba(39, 43, 48, 0.18);">
 
    <form class="" action="conexion/login_finca.php" method="post">  
    <div class="modal" id="modal" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
            <h4 class="modal-title">Seleccione finca</h4>
          </div>
          <div class="modal-body">

            <div class="form-group form-group-sm">
            <!-- <label  class="col-lg-2 control-label">Finca</label> -->
            <div class="col-lg-12">
              <select class="form-control" name="id_finca_elegida" id="seleccion_finca" required>   
                  <option value=''></option>
                  <?php
                  while ($sql_finca = mysqli_fetch_array($rsfinca)){
                    $idfinca= $sql_finca['id_finca'];
                    $finca = $sql_finca['finca'];
                    echo utf8_encode('<option value='.$idfinca.'>'.$finca.'</option>');
                  }
                  ?>
                </select>
            </div>
            </div>
            <br>

          </div>
          <div class="modal-footer">
            
            <button type="submit" class="btn btn-primary">Siguente</button>
          </div>
        </div>
      </div>
    </div>
    </form>


  </body>
</html>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {

$('#modal').modal('show')


});
</script>