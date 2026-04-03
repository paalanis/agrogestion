<?php
session_start();
include '../../conexion/conexion.php';
include '../querys/filtros.php';
$conexion = conectarServidor();

date_default_timezone_set("America/Argentina/Mendoza");
$hoy = date("m-d-Y");

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
                        $rslabores = queryLabores(false);
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
                          $rspersonal = queryPersonal(false,'propio');
                          while ($sql_personal = mysqli_fetch_array($rspersonal)){
                            $idpersonal= $sql_personal['id'];
                            $personal = $sql_personal['nombre'];
                            echo utf8_encode('<option value='.$idpersonal.'>'.$personal.'</option>');
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
 