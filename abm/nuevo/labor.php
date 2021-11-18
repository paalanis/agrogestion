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
 $sqltipo_labor = "SELECT
              tb_tipo_labor.id_tipo_labor as id_tipo_labor,
              tb_tipo_labor.nombre as nombre
              FROM tb_tipo_labor";
 $rstipo_labor = mysqli_query($conexion, $sqltipo_labor);  
?>

<div class="right_col" role="main" style="min-height: auto;">
  <div class="">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Labores <small></small></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          
          <form class="form-horizontal" role="form" onsubmit="event.preventDefault(); ()"> 
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Nombre</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="alta_labor" aria-describedby="basic-addon1" required autofocus="">
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label  class="control-label col-md-3 col-sm-3 col-xs-12">Tipo de labor</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <select class="form-control" id="alta_tipo" required>   
                        <option value=""></option>
                        <?php
                        while ($sql_tipo_labor = mysqli_fetch_array($rstipo_labor)){
                          $idtipo_labor= $sql_tipo_labor['id_tipo_labor'];
                          $tipo_labor = $sql_tipo_labor['nombre'];

                          echo utf8_encode('<option value='.$idtipo_labor.'>'.$tipo_labor.'</option>');
                          
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
                      <th>Labor</th>
                      <th>Tipo</th>
                      <th>#</th>
                      </tr>
                  </thead>
                  <tbody>
                   
                        <?php
                       
                        $sqllabor = "SELECT
                                      tb_labor.id_labor as id_labor,
                                      tb_labor.nombre as labor,
                                      tb_tipo_labor.nombre as tipo
                                      FROM
                                      tb_labor
                                      LEFT JOIN tb_tipo_labor ON tb_labor.id_tipo_labor = tb_tipo_labor.id_tipo_labor
                                      ORDER BY
                                      labor ASC
                                      ";
                        $rslabor = mysqli_query($conexion, $sqllabor);
                        
                        $cantidad =  mysqli_num_rows($rslabor);

                        if ($cantidad > 0) { // si existen labor con de esa labor se muestran, de lo contrario queda en blanco  
                       
                        while ($datos = mysqli_fetch_array($rslabor)){
                        $labor=utf8_encode($datos['labor']);
                        $id_labor=utf8_encode($datos['id_labor']);
                        $tipo=utf8_encode($datos['tipo']);

                        echo '

                        <tr class="even pointer">
                          <td>'.$labor.'</td>
                          <td>'.$tipo.'</td>
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


<script type="text/javascript">
  $(function() {
        $('.ver_riego-default').click(function() {

         var id = $(this).val()
         $("#panel_inicio").html('<div class="text-center"><div class="loadingsm"></div></div>');
         $('#panel_inicio').load("class/altas/modifica_labor.php", {id: id});            
         

              
        })
      })

  </script>