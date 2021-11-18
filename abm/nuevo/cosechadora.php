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
?>

<div class="right_col" role="main" style="min-height: auto;">
  <div class="">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Cosechadoras <small></small></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          
          <form class="form-horizontal" id="formulario_nuevo" role="form" onsubmit="event.preventDefault(); nuevo('cosechadora')"> 
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
                    <input type="text" class="form-control" autocomplete="off" id="dato_caracteristicas" aria-describedby="basic-addon1">
                  </div>
                </div>
                
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Propia</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input class="form-control" type="checkbox" value="false" id="dato_propia" onclick="cbx('propia')">
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
                      <th>Nombre</th>
                      <th>Características</th>
                      <th>Propia/Alquiler</th>
                      <th>#</th>
                      </tr>
                  </thead>
                  <tbody>
                        <?php
                    
                        $sqlcosechadora = "SELECT
                                        tb_cosechadora.nombre as nombre,
                                        tb_cosechadora.caracteristicas as caracteristicas,
                                        tb_cosechadora.id_cosechadora as id,
                                        IF(tb_cosechadora.propia = 'false', 'Alquiler','Propia') as propia
                                        FROM
                                        tb_cosechadora
                                        ORDER BY
                                        nombre ASC";
                        $rscosechadora = mysqli_query($conexion, $sqlcosechadora);
                        
                        $cantidad =  mysqli_num_rows($rscosechadora);

                        if ($cantidad > 0) { // si existen cosechadora con de esa cosechadora se muestran, de lo contrario queda en blanco  

                        while ($datos = mysqli_fetch_array($rscosechadora)){
                        $nombre=utf8_encode($datos['nombre']);
                        $caracteristicas=utf8_encode($datos['caracteristicas']);
                        $id=utf8_encode($datos['id']);
                        $propia=utf8_encode($datos['propia']);
                        
                        echo '

                        <tr class="even pointer">
                          <td id="'.$id.'">'.$nombre.'</td>
                          <td id="'.$id.'">'.$caracteristicas.'</td>
                          <td id="'.$id.'">'.$propia.'</td>
                          <td id="'.$id.'" class=" last">
                            <a id="'.$id.'" href="javascript:modifica(3,'.$id.');"" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> Modificar </a>
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