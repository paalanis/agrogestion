<?php
session_start();
if (!isset($_SESSION['usuario'])) {
header("Location: ../../index.php");
}
if ($_SESSION['id_finca_usuario'] == '0') {
session_destroy();
header("Location: ../../index.php");
}
$id_finca_usuario=$_SESSION['id_finca_usuario'];
include '../../conexion/conexion.php';
if (mysqli_connect_errno()) {
printf("La conexión con el servidor de base de datos falló comuniquese con su administrador: %s\n", mysqli_connect_error());
exit();
}
date_default_timezone_set("America/Argentina/Mendoza");
$hoy = date("m-d-Y");
$sqllabores = "SELECT
tb_labor.id_labor as id,
tb_labor.nombre as nombre
FROM
tb_labor
ORDER BY
tb_labor.nombre ASC";
$rslabores = mysqli_query($conexion, $sqllabores); 
$sqlpersonal = "SELECT
tb_personal.id_personal AS id_personal,
CONCAT(tb_personal.apellido, ', ',tb_personal.nombre) AS personal
FROM
tb_personal
WHERE
tb_personal.eventual = '0' and tb_personal.id_finca = '$id_finca_usuario'
ORDER BY
personal ASC";
$rspersonal = mysqli_query($conexion, $sqlpersonal); 
$cantidad =  mysqli_num_rows($rspersonal);
?>
<div class="right_col" role="main" style="min-height: auto;">
<div class="">

<div class="clearfix"></div>
<div class="row">
<div class="col-md-12 col-sm-12 col-xs-12">

  <div class="x_panel">
    <div class="x_title">
      <h2>Reporte Propios <small>Reporte de partes realizados por el personal propio</small></h2>
      <div class="clearfix"></div>
    </div>

    <div class="x_content">
      <br>

      <form class="form-horizontal" id="formulario_reporte" role="form" method="post" action="abm/reporte/excel_propios.php">

        <div class="well" style="overflow: auto">
                      <div class="col-md-4 col-sm-4 col-xs-12">
                          <fieldset>
                            <div class="control-group">
                              <div class="controls">
                                <div class="input-prepend input-group">
                                  <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                  <input type="text" name="dato_fecha" id="dato_fecha" class="form-control" value="<?php echo $hoy;?> - <?php echo $hoy;?>">
                                </div>
                              </div>
                            </div>
                          </fieldset>
                       </div>

                     <div class="col-md-4 col-sm-4 col-xs-12">
                      <fieldset>
                      <select class="form-control col-md-7 col-xs-12" name="dato_labor" id="dato_labor">   
                        <option value="">Labores</option>
                        <?php
                        while ($sql_labores = mysqli_fetch_array($rslabores)){
                          $idlabores= $sql_labores['id'];
                          $labores = $sql_labores['nombre'];
                          echo utf8_encode('<option value='.$idlabores.'>'.$labores.'</option>');
                        }
                        ?>
                      </select>
                      </fieldset>
                    </div>

                     <div class="col-md-4 col-sm-4 col-xs-12">
                      <fieldset>
                      <select class="form-control col-md-7 col-xs-12" name="dato_personal" id="dato_personal">   
                        <option value="">Personal</option>
                          <?php
                          if ($cantidad > 0) { 
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
                      </fieldset>
                    </div>

        </div>

        <div class="form-group">
          <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-0">
             <button class="btn btn-default" id="boton_limpiar" type="reset">Limpiar</button>
             <button type="button" id="boton_buscar" class="btn btn-primary" onclick="reporte('partespropios')">Buscar</button>
              <button type="submit" class="btn btn-info" id="botonExcel1" aria-label="Left Align">
              <span class="glyphicon glyphicon-save" aria-hidden="true"></span> Descargar</button>
          </div>
        </div>
           
        <div class="ln_solid"></div>

        <div id="div_reporte"></div>
        
      </form>
    </div>
  </div>
              
</div>
</div>
</div>
</div>
<script type="text/javascript">
   
    
  $(document).ready(function() {
    document.getElementById("botonExcel1").style.visibility = "hidden";
    init_daterangepicker();
    init_daterangepicker_reservation();
        
  }); 
        
     
  </script>
 