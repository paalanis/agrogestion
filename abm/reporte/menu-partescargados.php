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
$id_finca_usuario=$_SESSION['id_finca_usuario'];
include '../../conexion/conexion.php';
if (mysqli_connect_errno()) {
printf("La conexión con el servidor de base de datos falló comuniquese con su administrador: %s\n", mysqli_connect_error());
exit();
}
date_default_timezone_set("America/Argentina/Mendoza");
$hoy = date("m-d-Y");

mysqli_select_db($conexion,'$database');
$sql = "DELETE FROM tb_consumo_insumos_".$deposito." WHERE estado = '0'";
mysqli_query($conexion,$sql);
?>
<div class="right_col" role="main" style="min-height: auto;">
<div class="">

<div class="clearfix"></div>
<div class="row">
<div class="col-md-12 col-sm-12 col-xs-12">

  <div class="x_panel">
    <div class="x_title">
      <h2>Partes Cargados <small>Seleccione el período de fechas y luego buscar</small></h2>
      <div class="clearfix"></div>
    </div>

    <div class="x_content">
      <br>

      <form class="form-horizontal" id="formulario_reporte" role="form" onsubmit="event.preventDefault(); reporte('partescargados')">

        <div class="well" style="overflow: auto">
                      <div class="col-md-4">
                          <fieldset>
                            <div class="control-group">
                              <div class="controls">
                                <div class="input-prepend input-group">
                                  <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                  <input type="text" name="reservation" id="dato_fecha" class="form-control" value="<?php echo $hoy;?> - <?php echo $hoy;?>">
                                  <span class="input-group-btn" style="width: 0%;"><button type="submit" id="boton_buscar" class="btn btn-primary">Buscar</button></span>
                                </div>
                              </div>
                            </div>
                          </fieldset>
                       </div>
                       <div class="col-md-8" id="div_mensaje_general"></div>
                       
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
   
    init_daterangepicker();
    init_daterangepicker_reservation();
        
  }); 
        
     
  </script>
 