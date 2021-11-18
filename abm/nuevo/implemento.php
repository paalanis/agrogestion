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
          <h2>Implementos <small></small></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          
          <form class="form-horizontal" id="formulario_nuevo" role="form" onsubmit="event.preventDefault(); nuevo('implemento')"> 
          <input type="hidden" class="form-control" autocomplete="off" id="dato_id" value="0" aria-describedby="basic-addon1"> 
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Nombre</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="dato_nombre" aria-describedby="basic-addon1" required autofocus="">
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Características</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="dato_caracteristicas" aria-describedby="basic-addon1" required>
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
                        <th>Implemento</th>
                        <th>Características</th>
                        <th>#</th>
                      </tr>
                  </thead>
                  <tbody>
                   
                        <?php
                      
                        $sqlimplemento = "SELECT
                                      tb_implemento.id_implemento as id,
                                      tb_implemento.nombre as nombre,
                                      tb_implemento.caracteristicas as caracteristicas
                                      FROM
                                      tb_implemento
                                      ORDER BY
                                      nombre ASC
                                      ";
                        $rsimplemento = mysqli_query($conexion, $sqlimplemento);
                        
                        $cantidad =  mysqli_num_rows($rsimplemento);

                        if ($cantidad > 0) { // si existen implemento con de esa implemento se muestran, de lo contrario queda en blanco  
                       
                        while ($datos = mysqli_fetch_array($rsimplemento)){
                        $implemento=utf8_encode($datos['nombre']);
                        $id=utf8_encode($datos['id']);
                        $caracteristicas=utf8_encode($datos['caracteristicas']);

                        echo '
                        <tr class="even pointer">
                          <td id="'.$id.'">'.$implemento.'</td>
                          <td id="'.$id.'">'.$caracteristicas.'</td>
                          <td id="'.$id.'" class=" last">
                            <a id="'.$id.'" href="javascript:modifica(6,'.$id.');"  class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> Modificar </a>
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
