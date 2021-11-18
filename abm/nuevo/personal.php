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
 $sqlfinca = "SELECT
              tb_finca.id_finca as id_finca,
              tb_finca.nombre as finca
              FROM
              tb_finca
              WHERE
              tb_finca.id_finca = '$id_finca_usuario'";
 $rsfinca = mysqli_query($conexion, $sqlfinca);

 $sqlpuesto = "SELECT
              tb_puesto.id_puesto as id_puesto,
              tb_puesto.nombre as puesto
              FROM
              tb_puesto";
 $rspuesto = mysqli_query($conexion, $sqlpuesto);  
?>


<div class="right_col" role="main" style="min-height: auto;">
  <div class="">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Personal <small></small></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          
          <form class="form-horizontal" role="form" onsubmit="event.preventDefault(); ()"> 
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Nombre/Razón Social</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="alta_nombre" aria-describedby="basic-addon1" required autofocus="">
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Apellido</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="alta_apellido" aria-describedby="basic-addon1" required>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Fecha nacimiento</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="date" class="form-control" id="alta_nacimiento" aria-describedby="basic-addon1" required>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Fecha ingreso</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="date" class="form-control" id="alta_ingreso" aria-describedby="basic-addon1" required>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label  class="control-label col-md-3 col-sm-3 col-xs-12">Puesto</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <select class="form-control" id="alta_puesto" required>   
                        <option value=""></option>
                        <?php
                        while ($sql_puesto = mysqli_fetch_array($rspuesto)){
                          $idpuesto= $sql_puesto['id_puesto'];
                          $puesto = $sql_puesto['puesto'];

                          echo utf8_encode('<option value='.$idpuesto.'>'.$puesto.'</option>');
                          
                        }
                        ?>
                      </select>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label  class="control-label col-md-3 col-sm-3 col-xs-12">Finca</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <select class="form-control" id="alta_finca" required>   
                        <?php
                        while ($sql_finca = mysqli_fetch_array($rsfinca)){
                          $idfinca= $sql_finca['id_finca'];
                          $finca = $sql_finca['finca'];

                          echo utf8_encode('<option value='.$idfinca.'>'.$finca.'</option>');
                          
                        }
                        ?>
                      </select>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Tercerizado</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input class="form-control" type="checkbox" id="alta_eventual">
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
                      <th>Puesto</th>
                      <th>Tercero</th>
                      <th>#</th>
                      </tr>
                  </thead>
                  <tbody>
                        <?php
                        
                        $sqlpersonal = "SELECT
                                        tb_finca.nombre as finca,
                                        CONCAT(tb_personal.apellido, ', ', tb_personal.nombre) as nombre,
                                        tb_personal.id_personal as idpersonal,
                                        DATE_FORMAT(tb_personal.nacimiento, '%d/%m/%y') as nac, 
                                        DATE_FORMAT(tb_personal.ingreso, '%d/%m/%y') as ing,
                                        tb_puesto.nombre as puesto,
                                        IF(tb_personal.eventual = '0', 'No','Si') as eventual
                                        FROM
                                        tb_personal
                                        INNER JOIN tb_finca ON tb_personal.id_finca = tb_finca.id_finca
                                        INNER JOIN tb_puesto ON tb_puesto.id_puesto = tb_personal.id_puesto
                                        WHERE
                                        tb_personal.id_finca = '$id_finca_usuario'
                                        ORDER BY
                                        nombre ASC";
                        $rspersonal = mysqli_query($conexion, $sqlpersonal);
                        
                        $cantidad =  mysqli_num_rows($rspersonal);

                        if ($cantidad > 0) { // si existen personal con de esa personal se muestran, de lo contrario queda en blanco  
                       
                        while ($datos = mysqli_fetch_array($rspersonal)){
                        $finca=utf8_encode($datos['finca']);
                        $nombre=utf8_encode($datos['nombre']);
                        $nac=utf8_encode($datos['nac']);
                        $ing=utf8_encode($datos['ing']);
                        $puesto=utf8_encode($datos['puesto']);
                        $eventual=utf8_encode($datos['eventual']);
                        $idpersonal=utf8_encode($datos['idpersonal']);
        
                        echo '

                        <tr class="even pointer">
                          <td>'.$nombre.'</td>
                          <td>'.$puesto.'</td>
                          <td>'.$eventual.'</td>
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
        $('.btn_modifica').click(function() {

         var id = $(this).val()
         $("#panel_inicio").html('<div class="text-center"><div class="loadingsm"></div></div>');
         $('#panel_inicio').load("class/altas/modifica_personal.php", {id: id});            
         

              
        })
      })

 </script>