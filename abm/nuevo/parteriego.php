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
$hoy = date("Y-m-d");
$sqlcaudalimetro = "SELECT
              tb_caudalimetro.id_caudalimetro as id_caudalimetro,
              tb_caudalimetro.nombre as nombre
              FROM tb_caudalimetro
              WHERE
              tb_caudalimetro.id_finca = '$id_finca_usuario'
              ";
 $rscaudalimetro = mysqli_query($conexion, $sqlcaudalimetro);
?>


<div class="right_col" role="main" style="min-height: auto;">
  <div class="">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Parte de riego <small></small></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          
          <form class="form-horizontal" role="form" onsubmit="event.preventDefault(); ()"> 
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Fecha</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="date" class="form-control" id="dato_fecha" value="<?php echo $hoy;?>" aria-describedby="basic-addon1" required>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label  class="control-label col-md-3 col-sm-3 col-xs-12">Caudalímetro</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <select class="form-control" id="dato_caudalimetro" required onchange="llama_riego_valvula_operacion()" autofocus="">   
                        <option value=""></option>
                        <?php
                        while ($sql_caudalimetro = mysqli_fetch_array($rscaudalimetro)){
                          $idcaudalimetro= $sql_caudalimetro['id_caudalimetro'];
                          $caudalimetro = utf8_decode($sql_caudalimetro['nombre']);
                                        
                          echo utf8_encode('<option value='.$idcaudalimetro.'>'.$caudalimetro.'</option>');
                          
                        }
                        ?>
                      </select>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Metros cúbicos</label>
                  <div class="col-md-2 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="dato_inicial" placeholder="Lectura I." aria-describedby="basic-addon1" onblur="calculo_riego()" required>
                  </div>
                  <div class="col-md-2 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="dato_final" placeholder="Lectura F." aria-describedby="basic-addon1" onblur="calculo_riego()" required>
                  </div>
                  <div class="col-md-2 col-sm-6 col-xs-12" id="div_coef">
                    <label class="control-label">xCoef</label>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Cálculo ajustado</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control"  autocomplete="off" id="dato_resultado" placeholder="" readonly aria-describedby="basic-addon1" required>
                  </div>
                </div>
              </div> <!-- col1 -->
            </div> <!-- row1 -->
            
            <div class="ln_solid"></div>

            <div class="row">  
              <div class="col-md-12 col-sm-12 col-xs-12">
                
                 <div class="col-lg-6" id="div_operacion"></div>
                 <div class="col-lg-6" id="div_valvula"></div>
                
              </div> <!-- col2 -->
            </div> <!-- row2 -->

            <div class="ln_solid"></div>
            
            <div class="form-group">
              <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" id="div_mensaje_general">
                 <button class="btn btn-default" id="boton_limpiar" type="reset">Limpiar</button>
                 <button type="submit" id='boton_guardar' class="btn btn-primary">Guardar</button>
              </div>
            </div>

          </form>
                    

        </div> <!-- contenido -->
      </div> <!-- panel -->
    </div>
  </div>
</div>

<script type="text/javascript">

  function calculo_riego(){

    var mm_inicial = $('#dato_inicial').val()
    var mm_final = $('#dato_final').val()
    var coef = $('#riego_coef').val()
    var consumo = (mm_final - mm_inicial) * coef

    if(consumo > 0){
  
      $('#dato_resultado').val(consumo)

    }else{

      $('#dato_resultado').val('')
      // $('#dato_inicial').val('')
      $('#dato_final').val('')

    }
   }

  function llama_riego_valvula_operacion(){

    var caudalimetro = $('#dato_caudalimetro').val()

    if (caudalimetro != "") {
        $("#div_valvula").html('<div class="text-center"><div class="loadingsm"></div></div>');
        $("#div_operacion").html('<div class="text-center"><div class="loadingsm"></div></div>');
        $("#div_coef").load("abm/llamadas/riego-ajuste.php", {caudalimetro: caudalimetro});
        $("#div_valvula").load("abm/llamadas/riego-valvulas.php", {caudalimetro: caudalimetro});
        $("#div_operacion").load("abm/llamadas/riego-operacion.php", {caudalimetro: caudalimetro});
        $('#riego_resultado').val('')
        $('#riego_inicial').val('')
        $('#riego_final').val('')

    }else{

      $("#div_coef").html('<label class="control-label">xCoef</label>')
      $("#div_operacion").html('')
      $("#div_valvula").html('')
    }

   }

  </script>