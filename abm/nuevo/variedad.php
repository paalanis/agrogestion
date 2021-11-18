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
?>


<div class="right_col" role="main" style="min-height: auto;">
  <div class="">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Variedades <small></small></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          
          <form class="form-horizontal" role="form" onsubmit="event.preventDefault(); ()"> 
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                 <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Nombre</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="alta_variedad" aria-describedby="basic-addon1" required autofocus="">
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Tipo de pie</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="alta_tipo" aria-describedby="basic-addon1" required>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Origen</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="alta_origen" aria-describedby="basic-addon1" required>
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
                      <th>Variedad</th>
                      <th>Tipo de pie</th>
                      <th>Origen</th>
                      </tr>
                  </thead>
                  <tbody>
                   
                        <?php
                     
                        $sqlvariedad = "SELECT
                                    tb_variedad.nombre as variedad,
                                    tb_variedad.tipo as tipo,
                                    tb_variedad.origen as origen
                                    FROM
                                    tb_variedad
                                    ORDER BY
                                    tb_variedad.nombre ASC
                                    ";
                        $rsvariedad = mysqli_query($conexion, $sqlvariedad);
                        
                        $cantidad =  mysqli_num_rows($rsvariedad);

                        if ($cantidad > 0) { // si existen variedad con de esa variedad se muestran, de lo contrario queda en blanco  
                       
                        while ($datos = mysqli_fetch_array($rsvariedad)){
                        $variedad=utf8_encode($datos['variedad']);
                        $tipo=utf8_encode($datos['tipo']);
                        $origen=utf8_encode($datos['origen']);
                        
                        echo '

                        <tr class="even pointer">
                          <td>'.$variedad.'</td>
                          <td>'.$tipo.'</td>
                          <td>'.$origen.'</td>
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