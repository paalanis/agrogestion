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
$sqlcaudalimetro = "SELECT tb_caudalimetro.nombre as nombre, tb_caudalimetro.id_caudalimetro as id
FROM tb_caudalimetro INNER JOIN tb_finca ON tb_caudalimetro.id_finca = tb_finca.id_finca
WHERE tb_caudalimetro.id_finca = '$id_finca_usuario' and tb_caudalimetro.dilucion = '0'
ORDER BY nombre ASC";
$rscaudalimetro = mysqli_query($conexion, $sqlcaudalimetro);
?>


<div class="right_col" role="main" style="min-height: auto;">
  <div class="">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Operaciones <small></small></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          
          <form class="form-horizontal" role="form" onsubmit="event.preventDefault(); ()"> 
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                 <div class="form-group form-group-sm">
                  <label  class="control-label col-md-3 col-sm-3 col-xs-12">Caudalímetro</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <select class="form-control" id="alta_id_cauda">   
                       <option value="0"></option> 
                        <?php
                          while ($sql_caudalimetro = mysqli_fetch_array($rscaudalimetro)){
                              $caudalimetro =$sql_caudalimetro['nombre'];
                              $id_caudalimetro =$sql_caudalimetro['id'];
                                                                  
                              echo utf8_encode('<option value="'.$id_caudalimetro.'">'.$caudalimetro.'</option>');
                          }
                          ?>
                      </select>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label  class="control-label col-md-3 col-sm-3 col-xs-12">Nombre operación</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="alta_operacion" placeholder="">
                  </div>
                </div>
              </div> <!-- col1 -->
            </div> <!-- row1 -->
            
            <div class="ln_solid"></div>
            
            <div class="form-group">
              <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" id="div_mensaje_general">
                 <button class="btn btn-default" id="boton_limpiar" type="reset">Limpiar</button>
                 <button type="submit" id='boton_guardar' class="btn btn-primary">Guardar</button>
              </div>
            </div>

          </form>

            <div class="ln_solid"></div>

            <div class="row">  
              <div class="col-md-12 col-sm-12 col-xs-12">
                
                <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                    <th>Operación</th>
                    <th>Caudalímetro</th>
                    <!-- <th>Elimirar</th> -->
                    </tr>
                  </thead>
                  <tbody>
                  <?php

                  $sqloperacion_cuatro = "SELECT
                  tb_caudalimetro.nombre as cauda,
                  tb_operacion.id_operacion as id,
                  tb_operacion.nombre as nombre
                  FROM
                  tb_operacion
                  LEFT JOIN tb_caudalimetro ON tb_caudalimetro.id_caudalimetro = tb_operacion.id_caudalimetro
                  WHERE tb_operacion.id_finca = '$id_finca_usuario'
                  ORDER BY nombre ASC";
                  $rsoperacion_cuatro = mysqli_query($conexion, $sqloperacion_cuatro);

                  $cantidad =  mysqli_num_rows($rsoperacion_cuatro);

                  if ($cantidad > 0) { // si existen operaciones con de esa finca se muestran, de lo contrario queda en blanco  

                  while ($datos = mysqli_fetch_array($rsoperacion_cuatro)){
                  $operacion=utf8_encode($datos['nombre']);
                  $id_operacion=utf8_encode($datos['id']);
                  $cauda=utf8_encode($datos['cauda']);

                  echo '

                  <tr class="even pointer">
                  <td>'.$operacion.'</td>
                  <td>'.$cauda.'</td>
                  </tr>
                  ';

                  }   
                  }
                  ?>
                  </tbody>
                </table> 
                
              </div> <!-- col2 -->
            </div> <!-- row2 -->
                    

        </div> <!-- contenido -->
      </div> <!-- panel -->
    </div>
  </div>
</div>

<script type="text/javascript">init_DataTables();</script>