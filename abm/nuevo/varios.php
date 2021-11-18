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
if (mysqli_connect_errno()) {
   printf("La conexión con el servidor de base de datos falló comuniquese con su administrador: %s\n", mysqli_connect_error());
   exit();
}


 $sqlconduccion = "SELECT tb_conduccion.nombre as nombre, tb_conduccion.id_conduccion as id FROM tb_conduccion ORDER BY nombre ASC";
 $rsconduccion = mysqli_query($conexion, $sqlconduccion); 

 $sqlunidad = "SELECT tb_unidad.nombre as nombre, tb_unidad.id_unidad as id FROM tb_unidad ORDER BY nombre ASC";
 $rsunidad = mysqli_query($conexion, $sqlunidad);  

 $sqlriego = "SELECT tb_riego.nombre as nombre, tb_riego.id_riego as id FROM tb_riego ORDER BY nombre ASC";
 $rsriego = mysqli_query($conexion, $sqlriego); 

 $sqltipo_labor = "SELECT tb_tipo_labor.nombre as nombre, tb_tipo_labor.id_tipo_labor as id FROM tb_tipo_labor ORDER BY nombre ASC";
 $rstipo_labor = mysqli_query($conexion, $sqltipo_labor);
 
 $sqlpuesto = "SELECT tb_puesto.nombre as nombre, tb_puesto.id_puesto as id FROM tb_puesto ORDER BY nombre ASC";
 $rspuesto = mysqli_query($conexion, $sqlpuesto);

 $sqltipo_insumo = "SELECT tb_tipo_insumo.nombre as nombre, tb_tipo_insumo.id_tipo_insumo as id FROM tb_tipo_insumo ORDER BY nombre ASC";
 $rstipo_insumo = mysqli_query($conexion, $sqltipo_insumo);

 ?>

<div class="right_col" role="main" style="min-height: auto;">
<div class="">

<div class="clearfix"></div>
<div class="row">
<div class="col-md-12 col-sm-12 col-xs-12">

  <div class="x_panel">
    <div class="x_title">
      <h2>Varios <small></small></h2>
      <div class="clearfix"></div>
    </div>

    <div class="x_content">
      <br>

<form class="form-horizontal" role="form" onsubmit="event.preventDefault(); modifica_parametros()">
 
<div class="row">
 <div class="col-lg-12">
   <fieldset>
      <div class="form-group form-group-sm">
        <label for="inputPassword" class="col-lg-2 control-label">Conducción</label>
        <div class="col-lg-10">
          <div class="col-md-6">            
           <div class="input-group input-group-sm">
              <input type="text" class="form-control" autocomplete="off" id="parametros_conduccion" placeholder="Nuevo">
            <span class="input-group-btn">
              <button class="btn btn-default" type="button" onclick="parametro_conduccion()"><span class="glyphicon glyphicon-ok" aria-hidden="true"></button>
              </span>
           </div>
          </div>
          <div class="col-md-6">
            <div class="input-group input-group-sm">
            <select class="form-control" id="modifica_conduccion" >
              <option value="0">Modificar</option>
                                <?php
                                while ($sql_conduccion = mysqli_fetch_array($rsconduccion)){
                                    $conduccion =$sql_conduccion['nombre'];
                                    $id_conduccion =$sql_conduccion['id'];
                                                                        
                                    echo utf8_encode('<option value="'.$id_conduccion.'">'.$conduccion.'</option>');
                                }
                                ?>
            </select>
            <span class="input-group-btn">
              <button class="btn btn-default" value="conduccion" type="button"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group form-group-sm">
        <label for="inputPassword" class="col-lg-2 control-label">Unidad de medida</label>
        <div class="col-lg-10">
          <div class="col-md-6">            
           <div class="input-group input-group-sm">
              <input type="text" class="form-control" autocomplete="off" id="parametros_umedida" placeholder="Nuevo">
            <span class="input-group-btn">
              <button class="btn btn-default" type="button" onclick="parametro_umedida()"><span class="glyphicon glyphicon-ok" aria-hidden="true"></button>
              </span>
           </div>
          </div>
          <div class="col-md-6">
            <div class="input-group input-group-sm">
            <select class="form-control" id="modifica_umedida" >
              <option value="0">Modificar</option>
                                <?php
                                while ($sql_unidad = mysqli_fetch_array($rsunidad)){
                                    $unidad =$sql_unidad['nombre'];
                                    $id_unidad =$sql_unidad['id'];
                                                                        
                                    echo utf8_encode('<option value="'.$id_unidad.'">'.$unidad.'</option>');
                                }
                                ?>
            </select>
            <span class="input-group-btn">
              <button class="btn btn-default" value="umedida" type="button"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group form-group-sm">
        <label for="inputPassword" class="col-lg-2 control-label">Tipo de insumo</label>
        <div class="col-lg-10">
          <div class="col-md-6">            
           <div class="input-group input-group-sm">
              <input type="text" class="form-control" autocomplete="off" id="parametros_tipo_insumo" placeholder="Nuevo">
            <span class="input-group-btn">
              <button class="btn btn-default" type="button" onclick="parametro_tipo_insumo()"><span class="glyphicon glyphicon-ok" aria-hidden="true"></button>
              </span>
           </div>
          </div>
          <div class="col-md-6">
            <div class="input-group input-group-sm">
            <select class="form-control" id="modifica_tipo_insumo" >
              <option value="0">Modificar</option>
                                <?php
                                while ($sql_tipo_insumo = mysqli_fetch_array($rstipo_insumo)){
                                    $tipo_insumo =$sql_tipo_insumo['nombre'];
                                    $id_tipo_insumo =$sql_tipo_insumo['id'];
                                                                        
                                    echo utf8_encode('<option value="'.$id_tipo_insumo.'">'.$tipo_insumo.'</option>');
                                }
                                ?>
            </select>
            <span class="input-group-btn">
              <button class="btn btn-default" value="tipo_insumo" type="button"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group form-group-sm">
        <label for="inputPassword" class="col-lg-2 control-label">Riego</label>
        <div class="col-lg-10">
          <div class="col-md-6">            
           <div class="input-group input-group-sm">
              <input type="text" class="form-control" autocomplete="off" id="parametros_riego" placeholder="Nuevo">
            <span class="input-group-btn">
              <button class="btn btn-default" type="button" onclick="parametro_riego()"><span class="glyphicon glyphicon-ok" aria-hidden="true"></button>
              </span>
           </div>
          </div>
          <div class="col-md-6">
            <div class="input-group input-group-sm">
            <select class="form-control" id="modifica_riego" >
              <option value="0">Modificar</option>
                                <?php
                                while ($sql_riego = mysqli_fetch_array($rsriego)){
                                    $riego =$sql_riego['nombre'];
                                    $id_riego =$sql_riego['id'];
                                                                        
                                    echo utf8_encode('<option value="'.$id_riego.'">'.$riego.'</option>');
                                }
                                ?>
            </select>
            <span class="input-group-btn">
              <button class="btn btn-default" value="riego" type="button"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group form-group-sm">
        <label for="inputPassword" class="col-lg-2 control-label">Tipos de labor</label>
        <div class="col-lg-10">
          <div class="col-md-6">            
           <div class="input-group input-group-sm">
              <input type="text" class="form-control" autocomplete="off" id="parametros_tipo_labor" placeholder="Nuevo">
            <span class="input-group-btn">
              <button class="btn btn-default" type="button" onclick="parametro_tipo_labor()"><span class="glyphicon glyphicon-ok" aria-hidden="true"></button>
              </span>
           </div>
          </div>
          <div class="col-md-6">
            <div class="input-group input-group-sm">
            <select class="form-control" id="modifica_tipo_labor" >
              <option value="0">Modificar</option>
                                <?php
                                while ($sql_tipo_labor = mysqli_fetch_array($rstipo_labor)){
                                    $tipo_labor =$sql_tipo_labor['nombre'];
                                    $id_tipo_labor =$sql_tipo_labor['id'];
                                                                        
                                    echo utf8_encode('<option value="'.$id_tipo_labor.'">'.$tipo_labor.'</option>');
                                }
                                ?>
            </select>
            <span class="input-group-btn">
              <button class="btn btn-default" value="tipo_labor" type="button"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="form-group form-group-sm">
        <label for="inputPassword" class="col-lg-2 control-label">Puesto</label>
        <div class="col-lg-10">
          <div class="col-md-6">            
           <div class="input-group input-group-sm">
              <input type="text" class="form-control" autocomplete="off" id="parametros_puesto" placeholder="Nuevo">
            <span class="input-group-btn">
              <button class="btn btn-default" type="button" onclick="parametro_puesto()"><span class="glyphicon glyphicon-ok" aria-hidden="true"></button>
              </span>
           </div>
          </div>
          <div class="col-md-6">
            <div class="input-group input-group-sm">
            <select class="form-control" id="modifica_puesto" >
              <option value="0">Modificar</option>
                                <?php
                                while ($sql_puesto = mysqli_fetch_array($rspuesto)){
                                    $puesto =$sql_puesto['nombre'];
                                    $id_puesto =$sql_puesto['id'];
                                                                        
                                    echo utf8_encode('<option value="'.$id_puesto.'">'.$puesto.'</option>');
                                }
                                ?>
            </select>
            <span class="input-group-btn">
              <button class="btn btn-default" value="puesto" type="button"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-2"></div>
      <div class="col-lg-10">
        <div class="col-md-6"></div>
        <div class="col-md-6"> 
        <div id="div_modifica"></div>
        </div> 
      </div>
   </fieldset>
 
   </div>
 </div>  
  
  
</form>

 <div class="ln_solid"></div>

      <div id="div_msj_parametros"></div>
   </div>
  </div>
              
</div>
</div>
</div>
</div>
<script type="text/javascript">
    
$(function() {
        $('.ver_riego-default').click(function() {

          var idconduccion = $('#modifica_conduccion').val()
          var conduccion = document.getElementById("modifica_conduccion")
          var conduccion = conduccion.options[conduccion.selectedIndex].text;
          var idumedida = $('#modifica_umedida').val()
          var umedida = document.getElementById("modifica_umedida")
          var umedida = umedida.options[umedida.selectedIndex].text;
          var idtipo_insumo = $('#modifica_tipo_insumo').val()
          var tipo_insumo = document.getElementById("modifica_tipo_insumo")
          var tipo_insumo = tipo_insumo.options[tipo_insumo.selectedIndex].text;
          var idriego = $('#modifica_riego').val()
          var riego = document.getElementById("modifica_riego")
          var riego = riego.options[riego.selectedIndex].text;
          // var idtipo_labor = $('#modifica_tipo_labor').val()
          // var tipo_labor = document.getElementById("modifica_tipo_labor")
          // var tipo_labor = tipo_labor.options[tipo_labor.selectedIndex].text;
          var idpuesto = $('#modifica_puesto').val()
          var puesto = document.getElementById("modifica_puesto")
          var puesto = puesto.options[puesto.selectedIndex].text;
          var cual = $(this).val()

          
      switch(cual){

        case 'conduccion':
        if (idconduccion != "0") {
        $("#div_modifica").load("class/parametros/modifica_parametros.php", {id: idconduccion, texto: conduccion, opcion: cual});
        }
        break;

        case 'umedida':
        if (idumedida != "0") {
        $("#div_modifica").load("class/parametros/modifica_parametros.php", {id: idumedida, texto: umedida, opcion: cual});
        }
        break;

        case 'riego':
        if (idriego != "0") {
        $("#div_modifica").load("class/parametros/modifica_parametros.php", {id: idriego, texto: riego, opcion: cual});
        }
        break;

        // case 'tipo_labor':
        // if (idtipo_labor != "0") {
        // $("#div_modifica").load("class/parametros/modifica_parametros.php", {id: idtipo_labor, texto: tipo_labor, opcion: cual});
        // }
        // break;

        case 'puesto':
        if (idpuesto != "0") {
        $("#div_modifica").load("class/parametros/modifica_parametros.php", {id: idpuesto, texto: puesto, opcion: cual});
        }
        break;

        case 'tipo_insumo':
        if (idtipo_insumo != "0") {
        $("#div_modifica").load("class/parametros/modifica_parametros.php", {id: idtipo_insumo, texto: tipo_insumo, opcion: cual});
        }
        break;

      }


        })


      })


      
</script>
