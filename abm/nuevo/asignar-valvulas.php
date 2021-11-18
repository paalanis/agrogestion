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

$sqlvalvulas = "SELECT
        tb_valvula.nombre as valvula,
        tb_valvula.id_valvula as id_valvula,
        CAST(tb_valvula.nombre AS SIGNED) as valvula_orden
        FROM
        tb_valvula
        INNER JOIN tb_caudalimetro ON tb_valvula.id_caudalimetro = tb_caudalimetro.id_caudalimetro
        WHERE
        tb_caudalimetro.id_finca = '$id_finca_usuario' and tb_valvula.estado_op = '0'
        ORDER BY
        valvula_orden ASC
        ";
$rsvalvulas = mysqli_query($conexion, $sqlvalvulas);
$sqloperacion_dos = "SELECT tb_operacion.nombre as nombre, tb_operacion.id_operacion as id FROM tb_operacion
WHERE tb_operacion.id_finca = '$id_finca_usuario' ORDER BY nombre ASC";
$rsoperacion_dos = mysqli_query($conexion, $sqloperacion_dos); 
?>


<div class="right_col" role="main" style="min-height: auto;">
  <div class="">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Asignar <small>Válvulas a operaciones</small></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          
          <form class="form-horizontal" role="form" onsubmit="event.preventDefault(); ()"> 
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group form-group-sm">
                  <label  class="control-label col-md-3 col-sm-3 col-xs-12">Operaciones</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <select class="form-control" id="alta_operacion_bis" required>   
                       <option value="0"></option> 
                        <?php
                          if ($cantidad > 0) {
                          while ($sql_operacion_dos = mysqli_fetch_array($rsoperacion_dos)){
                              $operacion_dos =$sql_operacion_dos['nombre'];
                              $id_operacion_dos =$sql_operacion_dos['id'];
                                                                  
                              echo utf8_encode('<option value="'.$id_operacion_dos.'">'.$operacion_dos.'</option>');
                          }
                          }else{
                            echo '<option value="0">Sin operaciones para asignar</option>';
                          } 
                          ?>
                      </select>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label  class="control-label col-md-3 col-sm-3 col-xs-12">Válvula</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                   <select class="form-control" id="alta_operacion_valvula" required>   
                    <option value=""></option>
                    <?php
                    if ($cantidad > 0) { // si existen cuarteles con de esa finca se muestran, de lo contrario queda en blanco  
                    while ($sql_valvulas = mysqli_fetch_array($rsvalvulas)){
                      $idvalvula= $sql_valvulas['id_valvula'];
                      $valvula = $sql_valvulas['valvula'];
                      echo utf8_encode('<option value='.$idvalvula.'>'.$valvula.'</option>');
                    }
                    }else{
                      echo '<option value="0">Sin válvulas para asignar</option>';
                    }  
                    ?>
                  </select>
                 </div>
                </div>
              </div> <!-- col1 -->
            </div> <!-- row1 -->
            
            <div class="ln_solid"></div>
            
            <div class="form-group">
              <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" id="div_mensaje_general">
                 <button class="btn btn-default" id="boton_limpiar" type="reset">Limpiar</button>
                 <button type="submit" id='boton_guardar' class="btn btn-primary">Asignar</button>
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
                    <th>Válvula asignada</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    $sqloperaciones = "SELECT
                                tb_operacion_asignada.id_operacion_asignada AS id_operacion,
                                tb_valvula.nombre AS valvula,
                                CAST(tb_valvula.nombre AS SIGNED) as orden_valvula,
                                tb_operacion.nombre AS operacion,
                                tb_valvula.id_valvula as id_valvula
                                FROM
                                tb_operacion_asignada
                                INNER JOIN tb_valvula ON tb_valvula.id_valvula = tb_operacion_asignada.id_valvula
                                INNER JOIN tb_operacion ON tb_operacion.id_operacion = tb_operacion_asignada.id_operacion
                                WHERE
                                tb_operacion_asignada.id_finca = '$id_finca_usuario'
                                ORDER BY
                                operacion ASC,
                                orden_valvula ASC";
                    $rsoperaciones = mysqli_query($conexion, $sqloperaciones);

                    $cantidad =  mysqli_num_rows($rsoperaciones);

                    if ($cantidad > 0) { // si existen operaciones con de esa finca se muestran, de lo contrario queda en blanco  

                    while ($datos = mysqli_fetch_array($rsoperaciones)){
                    $operacion=utf8_encode($datos['operacion']);
                    $valvula=utf8_encode($datos['valvula']);
                    $id_operacion=utf8_encode($datos['id_operacion']);
                    $id_valvula=utf8_encode($datos['id_valvula']);

                    echo '

                    <tr class="even pointer">
                    <td>'.$operacion.'</td>
                    <td>'.$valvula.'</td>
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