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
$sqlcaudalimetro = "SELECT
            tb_caudalimetro.id_caudalimetro as id_caudalimetro,
            tb_caudalimetro.nombre as nombre
            FROM tb_caudalimetro
            WHERE
            tb_caudalimetro.id_finca = '$id_finca_usuario'
            and tb_caudalimetro.dilucion = '0'";
$rscaudalimetro = mysqli_query($conexion, $sqlcaudalimetro);

$sqlcuartel = "SELECT
                tb_cuartel.id_cuartel AS id_cuartel,
                CONCAT(tb_cuartel.nombre, ' - Has ', tb_cuartel.has) AS nombre
                FROM
                tb_cuartel
                WHERE
                tb_cuartel.id_finca = '$id_finca_usuario'
                ";
$rscuartel = mysqli_query($conexion, $sqlcuartel);  
$cantidad =  mysqli_num_rows($rscuartel);
?>

<div class="right_col" role="main" style="min-height: auto;">
  <div class="">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Válvulas <small></small></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          
          <form class="form-horizontal" role="form" onsubmit="event.preventDefault(); ()"> 
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group form-group-sm">
                  <label  class="control-label col-md-3 col-sm-3 col-xs-12">Caudalímetro</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <select class="form-control" id="alta_caudalimetro" required>
                        <option></option>  
                        <?php
                        while ($sql_caudalimetro = mysqli_fetch_array($rscaudalimetro)){
                          $idcaudalimetro= $sql_caudalimetro['id_caudalimetro'];
                          $caudalimetro = $sql_caudalimetro['nombre'];

                          echo utf8_encode('<option value='.$idcaudalimetro.'>'.$caudalimetro.'</option>');
                          
                        }
                        ?>
                      </select>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Nombre de válvula</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="alta_valvula" aria-describedby="basic-addon1" required autofocus="">
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label  class="control-label col-md-3 col-sm-3 col-xs-12">Seleccionar cuarteles</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                   <select class="form-control" id="alta_asignar" required>   
                    <option value=""></option>
                    <?php
                    if ($cantidad > 0) { // si existen cuarteles con de esa finca se muestran, de lo contrario queda en blanco  
                    while ($sql_cuartel = mysqli_fetch_array($rscuartel)){
                      $idcuartel= $sql_cuartel['id_cuartel'];
                      $cuartel = $sql_cuartel['nombre'];
                      echo utf8_encode('<option value='.$idcuartel.'>'.$cuartel.'</option>');
                    }
                    }else{
                      echo utf8_encode('<option value="0">Sin cuarteles</option>');
                    }  
                    ?>
                  </select>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label  class="control-label col-md-3 col-sm-3 col-xs-12">Asignar has</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                     <div class="input-group input-group-sm">
                     <input class="form-control" autocomplete="off" placeholder='has que riega la válvula' id="alta_asignar_has" type="text" required>
                     <span class="input-group-btn">
                     <button class="btn btn-default" type="submit">Ok</button>
                     </span>
                    </div>
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
                      <th>Caudalímetro</th>
                      <th>Válvula</th>
                      <th>Cuartel</th>
                      <th>Has</th>
                      <th>#</th>
                    </tr>
                  </thead>
                  <tbody>
                      <?php

                      $sqlcuarteles = "SELECT
                              tb_caudalimetro.nombre as caudalimetro,
                              tb_valvula.nombre as valvula,
                              CAST(tb_valvula.nombre AS SIGNED) as orden_valvula,
                              tb_cuartel.nombre as cuartel,
                              tb_valvula.has_asignadas as has
                              FROM
                              tb_valvula
                              INNER JOIN tb_caudalimetro ON tb_valvula.id_caudalimetro = tb_caudalimetro.id_caudalimetro
                              INNER JOIN tb_finca ON tb_caudalimetro.id_finca = tb_finca.id_finca
                              INNER JOIN tb_cuartel ON tb_cuartel.id_cuartel = tb_valvula.id_cuartel
                              WHERE
                              tb_finca.id_finca = '$id_finca_usuario'
                              ORDER BY
                              caudalimetro ASC,
                              orden_valvula ASC";
                      $rscuarteles = mysqli_query($conexion, $sqlcuarteles);

                      $cantidad =  mysqli_num_rows($rscuarteles);

                      if ($cantidad > 0) { // si existen cuarteles con de esa finca se muestran, de lo contrario queda en blanco  

                      while ($datos = mysqli_fetch_array($rscuarteles)){
                      $caudalimetro=utf8_encode($datos['caudalimetro']);
                      $valvula=utf8_encode($datos['valvula']);
                      $cuartel=utf8_encode($datos['cuartel']);
                      $has=$datos['has'];

                      echo '

                      <tr class="even pointer">
                      <td>'.$caudalimetro.'</td>
                      <td>'.$valvula.'</td>
                      <td>'.$cuartel.'</td>
                      <td>'.$has.'</td>
                      <td class=" last">
                        <a href="#" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> Modificar </a>
                      </td>
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