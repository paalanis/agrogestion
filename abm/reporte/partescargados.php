<?php
session_start();
if (!isset($_SESSION['usuario'])) {
header("Location: ../../../index.php");
}
if ($_SESSION['id_finca_usuario'] == '0') {
session_destroy();
header("Location: ../../../index.php");
}
$id_finca = $_SESSION['id_finca_usuario'];
include '../../conexion/conexion.php';
if (mysqli_connect_errno()) {
printf("La conexión con el servidor de base de datos falló comuniquese con su administrador: %s\n", mysqli_connect_error());
exit();
}
?>




<!-- <div class="right_col" role="main" style="min-height: 3842px;"> -->
      <div class="">
        <div class="clearfix"></div>       
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                 
                  <div class="x_content">

                    <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th>Parte </th>
                            <th>Fecha </th>
                            <th>Personal </th>
                            <th>Finca </th>
                            <th>Labor </th>
                            <th>Cuarteles </th>
                            <th>Horas/Jor </th>
                            <th>#</span>
                            </th>
                           </tr>
                        </thead>

                        <tbody>

        <?php

        $fecha=$_REQUEST['dato_fecha'];

        $desde = substr($fecha,0,10);
        $hasta = substr($fecha,13,23);
        $desde = date_create($desde);
        $hasta = date_create($hasta);
        $desde = date_format($desde,"Y-m-d");
        $hasta = date_format($hasta,"Y-m-d");
        
       
        $sqlparte = "SELECT
                  tb_parte_diario.id_parte_diario AS id,
                  tb_parte_diario.id_parte_diario_global AS parte,
                  DATE_FORMAT(tb_parte_diario.fecha, '%d/%m/%Y') AS fecha,
                  CONCAT(tb_personal.apellido, ', ',tb_personal.nombre) AS personal,
                  tb_finca.nombre AS finca,
                  GROUP_CONCAT(tb_cuartel.nombre ORDER BY tb_cuartel.nombre) AS cuartel,
                  tb_labor.nombre AS labor,
                  IF(tb_personal.eventual = '0', round(SUM(tb_parte_diario.horas_trabajadas),2), round(SUM(tb_parte_diario.horas_trabajadas)/8,2)) AS horas
                  FROM
                  tb_parte_diario
                  LEFT JOIN tb_finca ON tb_finca.id_finca = tb_parte_diario.id_finca
                  LEFT JOIN tb_cuartel ON tb_cuartel.id_cuartel = tb_parte_diario.id_cuartel
                  LEFT JOIN tb_labor ON tb_parte_diario.id_labor = tb_labor.id_labor
                  LEFT JOIN tb_personal ON tb_parte_diario.id_personal = tb_personal.id_personal
                  WHERE
                  tb_parte_diario.fecha BETWEEN '$desde' AND '$hasta' AND tb_parte_diario.id_finca = '$id_finca'
                  GROUP BY
                  tb_parte_diario.id_parte_diario_global
                  ORDER BY
                  tb_parte_diario.fecha DESC";
        $rsparte = mysqli_query($conexion, $sqlparte);
        
        $cantidad =  mysqli_num_rows($rsparte);

        if ($cantidad > 0) { // si existen parte con de esa finca se muestran, de lo contrario queda en blanco  
        
          while ($datos = mysqli_fetch_array($rsparte)){
          $id=utf8_encode($datos['id']);
          $parte=utf8_encode($datos['parte']);
          $fecha=utf8_encode($datos['fecha']);
          $finca=utf8_encode($datos['finca']);
          $personal=utf8_encode($datos['personal']);
          $cuartel=utf8_encode($datos['cuartel']);
          $labor=utf8_encode($datos['labor']);
          $horas=utf8_encode($datos['horas']);
          
          echo '

          <tr class="even pointer">
            <td id="'.$id.'">'.$parte.'</td>
            <td id="'.$id.'">'.$fecha.'</td>
            <td id="'.$id.'">'.$personal.'</td>
            <td id="'.$id.'">'.$finca.'</td>
            <td id="'.$id.'">'.$labor.'</td>
            <td id="'.$id.'">'.$cuartel.'</td>
            <td id="'.$id.'">'.$horas.'</td>
            <td id="'.$id.'" class=" last">
              <a id="'.$id.'" href="#" class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> Ver </a>
              <a id="'.$id.'" href="javascript:elimina_parte('.$parte.');" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Eliminar </a>
            </td>
          </tr>';
        
          }   
   
        }
        ?>

                        </tbody>                        
                      </table>
                   </div>
                </div>
              </div>
            </div>
                <!-- </div> -->

<script type="text/javascript">

init_DataTables();


function elimina_parte(id){
                                 
var pars = "id_global=" + id + "&";


$("#div_reporte").html('<div class="text-center"><div class="loadingsm"></div></div>');
$.ajax({
url : "abm/eliminar/partescargados.php",
data : pars,
dataType : "json",
type : "get",

success: function(data){

if (data.success == 'true') {
$('#div_mensaje_general').html('<div id="mensaje_general" class="alert alert-info alert-dismissible" style="height:47px" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Se eliminó el parte!</div>');
setTimeout("$('#mensaje_general').alert('close')", 2000);
$("#div_reporte").load("abm/reporte/partescargados.php");
} else {
$('#div_mensaje_general').html('<div id="mensaje_general" class="alert alert-danger alert-dismissible" style="height:47px" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Error reintente!</div>');        
setTimeout("$('#mensaje_general').alert('close')", 2000);
}

}

});

}

</script>

