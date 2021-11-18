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
$hoy = date("Y-m-d");
$sqllabores = "SELECT
              tb_labor.id_labor as id,
              tb_labor.nombre as nombre 
              FROM
              tb_labor
              ORDER BY
              tb_labor.nombre ASC";
$rslabores = mysqli_query($conexion, $sqllabores); 
$sqltractor = "SELECT
              tb_tractor.id_tractor as id,
              tb_tractor.nombre as nombre 
              FROM
              tb_tractor
              ORDER BY
              tb_tractor.nombre ASC";
$rstractor = mysqli_query($conexion, $sqltractor); 
$sqlimplemento = "SELECT
              tb_implemento.id_implemento as id,
              tb_implemento.nombre as nombre 
              FROM
              tb_implemento
              ORDER BY
              tb_implemento.nombre ASC";
$rsimplemento = mysqli_query($conexion, $sqlimplemento); 
$sqlfinca = "SELECT
              tb_finca.id_finca as id_finca,
              tb_finca.nombre as finca
              FROM
              tb_finca
              WHERE
              tb_finca.id_finca = '$id_finca_usuario'";
$rsfinca = mysqli_query($conexion, $sqlfinca); 
$sqlinsumos = "SELECT
                tb_insumo.id_insumo as id,
                CONCAT(tb_insumo.nombre_comercial, ' - ',tb_unidad.nombre) as nombre
                FROM
                tb_insumo
                INNER JOIN tb_unidad ON tb_insumo.id_unidad = tb_unidad.id_unidad
                ORDER BY
                tb_insumo.nombre_comercial ASC
                ";
$rsinsumos = mysqli_query($conexion, $sqlinsumos); 

mysqli_select_db($conexion,'$database');
$sql = "DELETE FROM tb_consumo_insumos_".$deposito." WHERE estado = '0'";
mysqli_query($conexion,$sql);
echo '<input class="form-control" id="deposito" value="'.$deposito.'"  type="hidden">';
?>
<div class="right_col" role="main" style="min-height: auto;">
<div class="">

<div class="clearfix"></div>
<div class="row">
<div class="col-md-12 col-sm-12 col-xs-12">

  <div class="x_panel">
    <div class="x_title">
      <h2>Parte Diario <small>Ingrese las tareas diarias del personal propio o tercercizado</small></h2>
      <div class="clearfix"></div>
    </div>

    <div class="x_content">
      <br>

      <form class="form-horizontal" id="formulario_nuevo" role="form" onsubmit="event.preventDefault(); nuevo('partediario')">
           
        <div class="form-group form-group-sm">
            <label  class="control-label col-md-3 col-sm-3 col-xs-12">Tipo de Personal</label>
          <div class="col-md-6 col-sm-6 col-xs-12">
            <select class="form-control col-md-7 col-xs-12" id="tipo_personal" required autofocus>   
              <option value=''></option>
              <option value='0'>Propio</option>
              <option value='1'>Tercerizado</option>
            </select>
          </div>
        </div>
        
        <div class="form-group form-group-sm">
            <label  class="control-label col-md-3 col-sm-3 col-xs-12">Personal/Empresa</label>
          <div class="col-md-6 col-sm-6 col-xs-12" id="personal_seleccionado">
            Aqui se carga el tipo de personal seleccionado.
          </div>
        </div>
                       
        <div class="form-group form-group-sm">
          <label for="inputPassword" class="control-label col-md-3 col-sm-3 col-xs-12">Fecha</label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input type="date" class="form-control col-md-7 col-xs-12" id="dato_fecha" value="<?php echo $hoy;?>" aria-describedby="basic-addon1" required  autofocus>
        </div>
        </div>
        
        <div class="form-group form-group-sm">
            <label  class="control-label col-md-3 col-sm-3 col-xs-12">Finca</label>
          <div class="col-md-6 col-sm-6 col-xs-12">
            <select class="form-control col-md-7 col-xs-12" id="dato_finca" required>   
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

        <div class="form-group form-group-sm">
            <label  class="control-label col-md-3 col-sm-3 col-xs-12">Labor</label>
          <div class="col-md-6 col-sm-6 col-xs-12">
            <select class="form-control col-md-7 col-xs-12" id="dato_labor" required>   
              <option value=""></option>
              <?php
              while ($sql_labores = mysqli_fetch_array($rslabores)){
                $idlabores= $sql_labores['id'];
                $labores = $sql_labores['nombre'];
                echo utf8_encode('<option value='.$idlabores.'>'.$labores.'</option>');
              }
              ?>
            </select>
          </div>
        </div>
        
        <div class="form-group form-group-sm">
            <label for="textArea" class="control-label col-md-3 col-sm-3 col-xs-12">Observación</label>
          <div class="col-md-6 col-sm-6 col-xs-12">
            <textarea class="form-control col-md-7 col-xs-12" autocomplete="off" rows="1" id="dato_obslabor"></textarea>
            <span class="help-block">En caso de ser necesario detalle la tarea realizada.</span>
          </div>
        </div>    
      
        <div class="form-group form-group-sm">
          <label for="textArea" class="control-label col-md-3 col-sm-3 col-xs-12">Cuarteles</label>
          <div class="col-md-6 col-sm-6 col-xs-12" id="div_cuarteles">
           
          </div>
        
        </div>
   
        <div class="form-group form-group-sm">
            <label class="control-label col-md-3 col-sm-3 col-xs-12">Insumos</label>
          <div class="col-md-6 col-sm-6 col-xs-12">
            <select class="form-control col-md-7 col-xs-12" id="lista_insumo">   
              <option value=""></option>
              <?php
              while ($sql_insumos = mysqli_fetch_array($rsinsumos)){
              $idinsumos= $sql_insumos['id'];
              $insumos = $sql_insumos['nombre'];
              echo utf8_encode('<option value='.$idinsumos.'>'.$insumos.'</option>');
              }
              ?>
            </select>
          <div id="div_saldo"></div>
          <div class="input-group input-group-sm">
            <input class="form-control col-md-7 col-xs-12" id="cantidad_insumo" placeholder="Cantidad" type="text">
            <span class="input-group-btn">
            <button class="btn btn-primary" id='boton_insumo' style="width: 75px;" type="button">Cargar</button>
            </span>
          </div>
          </div>
        </div>

        <div class="form-group form-group-sm">
          <label for="textArea" class="control-label col-md-3 col-sm-3 col-xs-12">Insumos cargados</label>
          <div class="col-md-6 col-sm-6 col-xs-12" id="div_insumos_cargados">
           Lista de insumos cargados.
          </div>
        
        </div>

        <div class="form-group form-group-sm">
            <label  class="control-label col-md-3 col-sm-3 col-xs-12">Maquinaria</label>
          <div class="col-md-6 col-sm-6 col-xs-12">
            <select class="form-control col-md-7 col-xs-12" id="dato_tractor">   
              <option value="0"></option>
              <?php
              while ($sql_tractor = mysqli_fetch_array($rstractor)){
                $idtractor= $sql_tractor['id'];
                $tractor = $sql_tractor['nombre'];
                echo utf8_encode('<option value='.$idtractor.'>'.$tractor.'</option>');
              }
              ?>
            </select>
          </div>
        </div>
        
        <div class="form-group form-group-sm">
            <label for="textArea" class="control-label col-md-3 col-sm-3 col-xs-12">Hora Inicial</label>
          <div class="col-md-6 col-sm-6 col-xs-12">
            <input class="form-control col-md-7 col-xs-12" id="valor_inicial" placeholder="h. inicial" autocomplete="off" onblur="calculo_in_fi()" type="text">
          </div>
        </div>
        <div class="form-group form-group-sm">
            <label for="textArea" class="control-label col-md-3 col-sm-3 col-xs-12">Hora Final</label>
          <div class="col-md-6 col-sm-6 col-xs-12">
            <input class="form-control col-md-7 col-xs-12" id="valor_final" placeholder="h. final" autocomplete="off" onblur="calculo_in_fi()" type="text">
          </div>
        </div> 
        <div class="form-group form-group-sm">
            <label for="textArea" class="control-label col-md-3 col-sm-3 col-xs-12">Horas Total</label>
          <div class="col-md-6 col-sm-6 col-xs-12">
            <input class="form-control col-md-7 col-xs-12" id="dato_calculo" placeholder="" readonly type="text">
          </div>
        </div>   
              
        <div class="form-group form-group-sm">
            <label  class="control-label col-md-3 col-sm-3 col-xs-12">Implemento</label>
          <div class="col-md-6 col-sm-6 col-xs-12">
            <select class="form-control col-md-7 col-xs-12" id="dato_implemento">   
              <option value=""></option>
              <?php
              while ($sql_implemento = mysqli_fetch_array($rsimplemento)){
                $idimplemento= $sql_implemento['id'];
                $implemento = $sql_implemento['nombre'];
                echo utf8_encode('<option value='.$idimplemento.'>'.$implemento.'</option>');
              }
              ?>
            </select>
          </div>
        </div>

        <div class="form-group form-group-sm">
            <label  class="control-label col-md-3 col-sm-3 col-xs-12">Horas trabajadas</label>
          <div class="col-md-6 col-sm-6 col-xs-12">
            <input class="form-control col-md-7 col-xs-12" type="text" autocomplete="off" id="dato_horas" required>  
          </div>
        </div>

        <div class="form-group form-group-sm">
            <label for="textArea" class="control-label col-md-3 col-sm-3 col-xs-12">Observación</label>
          <div class="col-md-6 col-sm-6 col-xs-12">
            <textarea class="form-control col-md-7 col-xs-12" autocomplete="off" rows="1" id="dato_obsgeneral"></textarea>
            <span class="help-block">En caso de ser necesario detalle el parte a cargar.</span>
          </div>
        </div>

        <div class="ln_solid"></div>
        
        <div class="form-group">
          <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" id="div_mensaje_general">
             <button class="btn btn-default" id="boton_limpiar" type="reset">Limpiar</button>
             <button type="submit" id='boton_guardar' class="btn btn-primary">Guardar</button>
          </div>
        </div>

      </form>
    </div>
  </div>
              
</div>
</div>
</div>
</div>
<script type="text/javascript">
 $(document).ready(function () {

  $("#div_cuarteles").html('<div class="text-center"><div class="loadingsm"></div></div>');
  $("#div_cuarteles").load("abm/llamadas/tabla-cuartel.php");
  // $('#diario_rendimiento').mask("##.00", {reverse: true});
  // $('#diario_horas').mask("##.00", {reverse: true});
  // $('#diario_hora_i').mask("##.00", {reverse: true});
  // $('#diario_hora_f').mask("##.00", {reverse: true});
  // $('#cantidad_insumo').mask("##.00", {reverse: true});
  });


$(function() {
        $('#tipo_personal').change(function() {


  $("#personal_seleccionado").html('<div class="text-center"><div class="loadingsm"></div></div>');
  $('#personal_seleccionado').load("abm/llamadas/personal.php", {finca:<?php echo $id_finca_usuario;?>, seleccion: $(this).val()});
       
   
        })
      })

$(function() {
        $('#lista_insumo').change(function() {


    $("#div_saldo").html('<div class="text-center"><div class="loadingsm"></div></div>');
    $("#div_saldo").load("abm/llamadas/saldo.php", {insumo: $(this).val()});      
   
        })
      })

$(function() {
        $('#boton_insumo').click(function() {


    var insumo = $("#lista_insumo").val()
    var cantidad = $("#cantidad_insumo").val()
    var fecha = $("#dato_fecha").val()
    if (insumo == '' || cantidad == '' || fecha == '') {
      if (insumo == '') {
        $("#lista_insumo").tooltip({title: "Debe seleccionar", placement: "top"});
                $("#lista_insumo").tooltip('show');
      };
      if (cantidad == '') {
        $("#cantidad_insumo").tooltip({title: "No debe ser cero", placement: "top"});
                $("#cantidad_insumo").tooltip('show');
      };
      if (fecha == '') {
        $("#dato_fecha").tooltip({title: "Debe seleccionar", placement: "top"});
                $("#dato_fecha").tooltip('show');
      };
    }else{
    var pars = "dato_insumo=" + insumo + "&" + "dato_cantidad=" + cantidad + "&" + "dato_fecha=" + fecha + "&";
    // alert(pars);
          $("#div_insumos_cargados").html('<div class="text-center"><div class="loadingsm"></div></div>');
          $(this).attr('disabled', true);

          $.ajax({
              url : "abm/guardar/insumo-temp.php",
              data : pars,
              dataType : "json",
              type : "get",

              success: function(data){
                  
                if (data.success == 'true') {

                  $("#lista_insumo").val('');
                  $("#cantidad_insumo").val('');
                  $('#boton_insumo').attr('disabled', false);
                  $("#div_saldo").html('');
                  $("#div_insumos_cargados").load("abm/llamadas/insumo-cargado.php");

                } else {
                  $('#div_insumos_cargados').html('<div id="mensaje_general" class="alert alert-danger alert-dismissible" style="height:47px" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Error reintente!</div>');        
                  setTimeout("$('#mensaje_general').alert('close')", 2000);
                }
              
              }

          });
          
    } 


   
        })
      })

  </script>
  