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
 $sqlumedida = "SELECT
              tb_unidad.id_unidad as id_umedida,
              tb_unidad.nombre as nombre
              FROM tb_unidad";
 $rsumedida = mysqli_query($conexion, $sqlumedida);  

 $sqltipo_insumo = "SELECT
              tb_tipo_insumo.id_tipo_insumo as id_tipo_insumo,
              tb_tipo_insumo.nombre as nombre
              FROM tb_tipo_insumo";
 $rstipo_insumo = mysqli_query($conexion, $sqltipo_insumo);
?>

<div class="right_col" role="main" style="min-height: auto;">
  <div class="">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Insumos <small></small></h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          
          <form class="form-horizontal" id="formulario_nuevo" role="form" onsubmit="event.preventDefault(); nuevo('insumos')"> 
          <input type="hidden" class="form-control" autocomplete="off" id="dato_id" value="0" aria-describedby="basic-addon1"> 
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Nombre comercial</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="dato_nombre" aria-describedby="basic-addon1" required autofocus="">
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Principio activo</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="text" class="form-control" autocomplete="off" id="dato_principio" aria-describedby="basic-addon1" required>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Concentración</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="input-group input-group-sm">
                    <input type="text" class="form-control" autocomplete="off" id="dato_concentracion" placeholder="Porcentaje de concentración" aria-describedby="basic-addon1" required>
                    <span class="input-group-addon">%</span>
                    </div>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label  class="control-label col-md-3 col-sm-3 col-xs-12">Unidad de medida</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <select class="form-control" id="dato_umedida" required>   
                        <option value=""></option>
                        <?php
                        while ($sql_umedida = mysqli_fetch_array($rsumedida)){
                          $idumedida= $sql_umedida['id_umedida'];
                          $umedida = $sql_umedida['nombre'];

                          echo utf8_encode('<option value='.$idumedida.'>'.$umedida.'</option>');
                          
                        }
                        ?>
                      </select>
                  </div>
                </div>
                <div class="form-group form-group-sm">
                  <label  class="control-label col-md-3 col-sm-3 col-xs-12">Tipo de insumo</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <select class="form-control" id="dato_tipo" required>   
                        <option value=""></option>
                        <?php
                        while ($sql_tipo_insumo = mysqli_fetch_array($rstipo_insumo)){
                          $idtipo_insumo= $sql_tipo_insumo['id_tipo_insumo'];
                          $tipo_insumo = $sql_tipo_insumo['nombre'];

                          echo utf8_encode('<option value='.$idtipo_insumo.'>'.$tipo_insumo.'</option>');
                          
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
                      <th>Insumo</th>
                      <th>Principio Activo</th>
                      <th>% Conc.</th>
                      <th>Tipo</th>
                      <th>Unidad</th>
                      <th>#</th>
                      </tr>
                  </thead>
                  <tbody>
                   
                        <?php
                        
                        $sqlinsumo = "SELECT
                                      tb_insumo.id_insumo as id,
                                      tb_insumo.nombre_comercial as insumo,
                                      tb_insumo.principio_activo as principio,
                                      tb_insumo.concentracion as concentracion,
                                      tb_unidad.nombre as unidad,
                                      tb_tipo_insumo.nombre as tipo_insumo
                                      FROM
                                      tb_insumo
                                      LEFT JOIN tb_unidad ON tb_insumo.id_unidad = tb_unidad.id_unidad
                                      LEFT JOIN tb_tipo_insumo ON tb_tipo_insumo.id_tipo_insumo = tb_insumo.id_tipo_insumo
                                      ORDER BY
                                      insumo ASC";
                        $rsinsumo = mysqli_query($conexion, $sqlinsumo);
                        
                        $cantidad =  mysqli_num_rows($rsinsumo);

                        if ($cantidad > 0) { // si existen insumo con de esa insumo se muestran, de lo contrario queda en blanco  
                       
                        while ($datos = mysqli_fetch_array($rsinsumo)){
                        $insumo=utf8_encode($datos['insumo']);
                        $id=utf8_encode($datos['id']);
                        $principio=utf8_encode($datos['principio']);
                        $concentracion=utf8_encode($datos['concentracion']);
                        $unidad=utf8_encode($datos['unidad']);
                        $tipo_insumo=utf8_encode($datos['tipo_insumo']);
                        
                        echo '

                        <tr class="even pointer">
                          <td id="'.$id.'">'.$insumo.'</td>
                          <td id="'.$id.'">'.$principio.'</td>
                          <td id="'.$id.'">'.$concentracion.'</td>
                          <td id="'.$id.'">'.$tipo_insumo.'</td>
                          <td id="'.$id.'">'.$unidad.'</td>
                          <td id="'.$id.'" class=" last">
                            <a id="'.$id.'" href="javascript:modifica(7,'.$id.');"  class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> Modificar </a>
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