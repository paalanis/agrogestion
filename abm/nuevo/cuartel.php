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
$sqlconduccion = "SELECT
              tb_conduccion.id_conduccion as id_conduccion,
              tb_conduccion.nombre as nombre
              FROM tb_conduccion";
$rsconduccion = mysqli_query($conexion, $sqlconduccion); 

 $sqlfinca = "SELECT
              tb_finca.id_finca as id_finca,
              tb_finca.nombre as nombre
              FROM tb_finca
              WHERE
              tb_finca.id_finca = '$id_finca_usuario'";
 $rsfinca = mysqli_query($conexion, $sqlfinca); 

 $sqlriego = "SELECT
              tb_riego.id_riego as id_riego,
              tb_riego.nombre as nombre
              FROM tb_riego";
 $rsriego = mysqli_query($conexion, $sqlriego);

 $sqlvariedad = "SELECT
              tb_variedad.id_variedad as id_variedad,
              tb_variedad.nombre as nombre
              FROM tb_variedad
              ORDER BY
              nombre ASC";
 $rsvariedad = mysqli_query($conexion, $sqlvariedad);     
?>




<div class="right_col" role="main" style="min-height: auto;">
  <div class="">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Cuarteles <small></small></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          
          <form class="form-horizontal" id="formulario_nuevo" role="form" onsubmit="event.preventDefault(); nuevo('cuartel')"> 
          <input type="hidden" class="form-control" autocomplete="off" id="dato_id" value="0" aria-describedby="basic-addon1"> 
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                 <div class="form-group form-group-sm">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">N°-Nombre</label>
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
                    <label  class="control-label col-md-3 col-sm-3 col-xs-12">Variedad</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <select class="form-control" id="dato_variedad" required>   
                          <option value=""></option>
                          <?php
                          while ($sql_variedad = mysqli_fetch_array($rsvariedad)){
                            $idvariedad= $sql_variedad['id_variedad'];
                            $variedad = $sql_variedad['nombre'];

                            echo utf8_encode('<option value='.$idvariedad.'>'.$variedad.'</option>');
                            
                          }
                          ?>
                        </select>
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label col-md-3 col-sm-3 col-xs-12">Tipo de riego</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <select class="form-control" id="dato_riego" required>   
                          <option value=""></option>
                          <?php
                          while ($sql_riego = mysqli_fetch_array($rsriego)){
                            $idriego= $sql_riego['id_riego'];
                            $riego = $sql_riego['nombre'];

                            echo utf8_encode('<option value='.$idriego.'>'.$riego.'</option>');
                            
                          }
                          ?>
                        </select>
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label col-md-3 col-sm-3 col-xs-12">Tipo de conducción</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <select class="form-control" id="dato_conduccion" required>   
                          <option value=""></option>
                          <?php
                          while ($sql_conduccion = mysqli_fetch_array($rsconduccion)){
                            $idconduccion= $sql_conduccion['id_conduccion'];
                            $conduccion = $sql_conduccion['nombre'];

                            echo utf8_encode('<option value='.$idconduccion.'>'.$conduccion.'</option>');
                            
                          }
                          ?>
                        </select>
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Año</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <input type="text" class="form-control" autocomplete="off" id="dato_anio" aria-describedby="basic-addon1" required>
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Distancia.</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <input type="text" class="form-control" autocomplete="off" id="dato_distancia" aria-describedby="basic-addon1" required>
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Has</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <input type="text" class="form-control" autocomplete="off" id="dato_has" aria-describedby="basic-addon1" required>
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">ID Satelital</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <input type="text" class="form-control" autocomplete="off" id="dato_mapeo" aria-describedby="basic-addon1" required>
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Hileras</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <input type="text" class="form-control" autocomplete="off" id="dato_hileras" aria-describedby="basic-addon1" required>
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Malla antigranizo</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <input class="form-control" type="checkbox" value="false" id="dato_malla" onclick="cbx('malla')">
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
                      <th>Cuartel</th>
                      <th>Var</th>
                      <th>Has</th>
                      <th>Año</th>
                      <th>Hil</th>
                      <th>Riego</th>
                      <th>ID_SUPER</th>
                      <th>Dist.</th>
                      <th>Malla</th>
                      <th>#</th>
                      </tr>
                  </thead>
                  <tbody>
                   
                        <?php
        
                        $sqlcuarteles = "SELECT
                                          tb_cuartel.id_cuartel as id_cuartel,
                                          CAST(tb_cuartel.nombre AS SIGNED) as orden_cuartel,
                                          tb_finca.nombre as finca,
                                          tb_cuartel.nombre as cuartel,
                                          LEFT(tb_variedad.nombre, 4) as variedad,
                                          tb_cuartel.has as has,
                                          tb_cuartel.ano as ano,
                                          tb_cuartel.hileras as hileras,
                                          tb_riego.nombre as riego,
                                          tb_cuartel.id_super AS id_super,
                                          tb_cuartel.distancia as distancia,
                                          IF(tb_cuartel.malla = 'true', 'Si', 'No') as malla
                                          FROM
                                          tb_cuartel
                                          LEFT JOIN tb_finca ON tb_cuartel.id_finca = tb_finca.id_finca
                                          LEFT JOIN tb_variedad ON tb_cuartel.id_variedad = tb_variedad.id_variedad
                                          LEFT JOIN tb_conduccion ON tb_conduccion.id_conduccion = tb_cuartel.id_conduccion
                                          LEFT JOIN tb_riego ON tb_riego.id_riego = tb_cuartel.id_riego
                                          WHERE
                                          tb_cuartel.id_finca = '$id_finca_usuario'
                                          ORDER BY
                                          orden_cuartel ASC";
                        $rscuarteles = mysqli_query($conexion, $sqlcuarteles);
                        
                        $cantidad =  mysqli_num_rows($rscuarteles);

                        if ($cantidad > 0) { // si existen cuarteles con de esa cuarteles se muestran, de lo contrario queda en blanco  
                       
                        while ($datos = mysqli_fetch_array($rscuarteles)){
                        $finca=utf8_encode($datos['finca']);
                        $cuartel=utf8_encode($datos['cuartel']);
                        $id=utf8_encode($datos['id_cuartel']);
                        $variedad=utf8_encode($datos['variedad']);
                        $has=utf8_encode($datos['has']);
                        $ano=utf8_encode($datos['ano']);
                        $hileras=utf8_encode($datos['hileras']);
                        $riego=utf8_encode($datos['riego']);
                        $id_super=utf8_encode($datos['id_super']);
                        $distancia=utf8_encode($datos['distancia']);
                        $malla=utf8_encode($datos['malla']);

                        
                        echo '

                        <tr class="even pointer">
                          <td id="'.$id.'">'.$cuartel.'</td>
                          <td id="'.$id.'">'.$variedad.'</td>
                          <td id="'.$id.'">'.$has.'</td>
                          <td id="'.$id.'">'.$ano.'</td>
                          <td id="'.$id.'">'.$hileras.'</td>
                          <td id="'.$id.'">'.$riego.'</td>
                          <td id="'.$id.'">'.$id_super.'</td>
                          <td id="'.$id.'">'.$distancia.'</td>
                          <td id="'.$id.'">'.$malla.'</td>
                          <td id="'.$id.'" class=" last">
                            <a id="'.$id.'" href="javascript:modifica(4,'.$id.');" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> Modificar </a>
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