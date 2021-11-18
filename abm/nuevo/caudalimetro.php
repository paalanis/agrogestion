<?php
session_start();
if (!isset($_SESSION['usuario'])) {
header("Location: ../../index.php");
}
if ($_SESSION['id_finca_usuario'] == '0') {
session_destroy();
header("Location:../../index.php");
}
$id_finca_usuario=$_SESSION['id_finca_usuario'];
include '../../conexion/conexion.php';
if (mysqli_connect_errno()) {
printf("La conexión con el servidor de base de datos falló comuniquese con su administrador: %s\n", mysqli_connect_error());
exit();
}
 $sqlfinca = "SELECT
              tb_finca.id_finca as id_finca,
              tb_finca.nombre as nombre
              FROM tb_finca
              WHERE
              tb_finca.id_finca = '$id_finca_usuario'";
 $rsfinca = mysqli_query($conexion, $sqlfinca); 
?>

<div class="right_col" role="main" style="min-height: auto;">
  <div class="">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Caudalímetros <small></small></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          
          <form class="form-horizontal" id="formulario_nuevo" role="form" onsubmit="event.preventDefault(); nuevo('caudalimetro')"> 
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
                    <label  class="control-label col-md-3 col-sm-3 col-xs-12">Finca</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <select class="form-control" id="dato_finca" required>   
                          <?php
                          while ($sql_finca = mysqli_fetch_array($rsfinca)){
                            $idfinca= $sql_finca['id_finca'];
                            $finca = $sql_finca['nombre'];

                            echo utf8_encode('<option value='.$idfinca.'>'.$finca.'</option>');
                            
                          }
                          ?>
                        </select>
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Características</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <input type="text" class="form-control" autocomplete="off" id="dato_caracteristicas" aria-describedby="basic-addon1" required>
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Coef. corrección</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <div align="left">
                      <input type="text" class="form-control" autocomplete="off" id="dato_coef" aria-describedby="basic-addon1" required>
                      </div>
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Caudalímetro de dilución</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <div align="left">
                      <input class="form-control" type="checkbox" value='false' id="dato_dilucion" onclick="cbx('dilucion')">
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
                      <th>Finca</th>
                      <th>Caudalímetro</th>
                      <th>Características</th>
                      <th>Coef</th>
                      <th>Dilución</th>
                      <th>#</th>
                      </tr>
                  </thead>
                  <tbody>
                   
                        <?php
                    
                        $sqlcaudalimetro = "SELECT
                                      tb_caudalimetro.id_caudalimetro as id,
                                      tb_finca.nombre as finca,
                                      tb_caudalimetro.nombre as caudalimetro,
                                      tb_caudalimetro.caracteristicas as caracteristica,
                                      tb_caudalimetro.coef as coef,
                                      if(tb_caudalimetro.dilucion = 'false', 'No', 'Si') as dilucion
                                      FROM
                                      tb_caudalimetro
                                      INNER JOIN tb_finca ON tb_caudalimetro.id_finca = tb_finca.id_finca
                                      WHERE
                                      tb_caudalimetro.id_finca = '$id_finca_usuario'
                                      ORDER BY
                                      finca ASC
                                      ";
                        $rscaudalimetro = mysqli_query($conexion, $sqlcaudalimetro);
                        
                        $cantidad =  mysqli_num_rows($rscaudalimetro);

                        if ($cantidad > 0) { // si existen caudalimetro con de esa caudalimetro se muestran, de lo contrario queda en blanco  
                       
                        while ($datos = mysqli_fetch_array($rscaudalimetro)){
                        $id=utf8_encode($datos['id']);
                        $finca=utf8_encode($datos['finca']);
                        $caudalimetro=utf8_encode($datos['caudalimetro']);
                        $caracteristica=utf8_encode($datos['caracteristica']);
                        $dilucion=utf8_encode($datos['dilucion']);
                        $coef=utf8_encode($datos['coef']);
                        
                        echo '

                        <tr class="even pointer">
                          <td id="'.$id.'">'.$finca.'</td>
                          <td id="'.$id.'">'.$caudalimetro.'</td>
                          <td id="'.$id.'">'.$caracteristica.'</td>
                          <td id="'.$id.'">'.$coef.'</td>
                          <td id="'.$id.'">'.$dilucion.'</td>
                          <td id="'.$id.'" class=" last">
                            <a id="'.$id.'" href="javascript:modifica(2,'.$id.');"  class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> Modificar </a>
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







