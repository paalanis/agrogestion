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

$sqldeposito = "SELECT
              tb_deposito.id_deposito as id_deposito,
              tb_deposito.nombre as nombre
              FROM tb_deposito
              ORDER BY
              nombre ASC";
$rsdeposito = mysqli_query($conexion, $sqldeposito);     
$sqlcentrocosto = "SELECT
              tb_centro_costo.id_centro_costo as id_centrocosto,
              tb_centro_costo.nombre as nombre
              FROM tb_centro_costo
              ORDER BY
              nombre ASC";
$rscentrocosto = mysqli_query($conexion, $sqlcentrocosto);
?>




<div class="right_col" role="main" style="min-height: auto;">
  <div class="">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Fincas <small></small></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          
          <form class="form-horizontal" id="formulario_nuevo" role="form" onsubmit="event.preventDefault(); nuevo('finca')"> 
          <input type="hidden" class="form-control" autocomplete="off" id="dato_id" value="0" aria-describedby="basic-addon1"> 
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Razón Social</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="dato_rs" aria-describedby="basic-addon1" required autofocus>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Nombre</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="dato_nombre" aria-describedby="basic-addon1" required>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Localidad</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="dato_localidad" aria-describedby="basic-addon1" required>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Provincia</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="dato_provincia" aria-describedby="basic-addon1" required>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Has</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="dato_has" aria-describedby="basic-addon1" required>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                    <label  class="control-label col-md-3 col-sm-3 col-xs-12">Depósito</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <select class="form-control" id="dato_deposito" required>   
                          <option value=""></option>
                          <?php
                          while ($sql_deposito = mysqli_fetch_array($rsdeposito)){
                            $iddeposito= $sql_deposito['id_deposito'];
                            $deposito = $sql_deposito['nombre'];

                            echo utf8_encode('<option value='.$iddeposito.'>'.$deposito.'</option>');
                            
                          }
                          ?>
                        </select>
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label col-md-3 col-sm-3 col-xs-12">Centro de Costo</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <select class="form-control" id="dato_centrocosto" required>   
                          <option value=""></option>
                          <?php
                          while ($sql_centrocosto = mysqli_fetch_array($rscentrocosto)){
                            $idcentrocosto= $sql_centrocosto['id_centrocosto'];
                            $centrocosto = $sql_centrocosto['nombre'];

                            echo utf8_encode('<option value='.$idcentrocosto.'>'.$centrocosto.'</option>');
                            
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
                      <th>Razón Social</th>
                      <th>Finca</th>
                      <th>Localidad</th>
                      <th>Provincia</th>
                      <th>Has</th>
                      <th>Depósito</th>
                      <th>Centro Costo</th>
                      <th>#</th>
                      </tr>
                  </thead>
                  <tbody>
                        <?php
                        $sqlfinca = "SELECT
                        tb_finca.id_finca AS id,
                        tb_finca.rs AS rs,
                        tb_finca.nombre AS nombre,
                        tb_finca.localidad AS localidad,
                        tb_finca.provincia AS provincia,
                        tb_finca.has AS has,
                        tb_deposito.nombre AS deposito,
                        tb_centro_costo.nombre AS centro
                        FROM
                        tb_finca
                        LEFT JOIN tb_deposito ON tb_deposito.id_deposito = tb_finca.id_deposito
                        LEFT JOIN tb_centro_costo ON tb_centro_costo.id_centro_costo = tb_finca.id_centro_costo
                        ORDER BY
                        nombre ASC";
                        $rsfinca = mysqli_query($conexion, $sqlfinca);
                        
                        $cantidad =  mysqli_num_rows($rsfinca);

                        if ($cantidad > 0) { // si existen finca con de esa finca se muestran, de lo contrario queda en blanco  
                       
                        while ($datos = mysqli_fetch_array($rsfinca)){
                        $id=utf8_encode($datos['id']);
                        $rs=utf8_encode($datos['rs']);
                        $nombre=utf8_encode($datos['nombre']);
                        $localidad=utf8_encode($datos['localidad']);
                        $provincia=utf8_encode($datos['provincia']);
                        $has=$datos['has'];
                        $deposito=utf8_encode($datos['deposito']);
                        $centro=utf8_encode($datos['centro']);
                        
                        echo '

                        <tr class="even pointer">
                          <td id="'.$id.'">'.$rs.'</td>
                          <td id="'.$id.'">'.$nombre.'</td>
                          <td id="'.$id.'">'.$localidad.'</td>
                          <td id="'.$id.'">'.$provincia.'</td>
                          <td id="'.$id.'">'.$has.'</td>
                          <td id="'.$id.'">'.$deposito.'</td>
                          <td id="'.$id.'">'.$centro.'</td>
                          <td id="'.$id.'" class=" last">
                            <a id="'.$id.'" href="javascript:modifica(5,'.$id.');"  class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> Modificar </a>
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
