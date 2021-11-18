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
                            <th>Labor </th>
                            <th>Cuarteles </th>
                            <th>Horas </th>
                            <th>Has </th>
                            <th>Obs </th>
                            <th>Obs Gral </th>
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


        $labor=$_REQUEST['dato_labor'];
        $personal=$_REQUEST['dato_personal'];

        $consulta_personal = "";
        $consulta_labor = "";

        if ($personal != "") {
        $consulta_personal = "AND tb_parte_diario.id_personal = '$personal' ";
        }
        if ($labor != "") {
        $consulta_labor = "AND tb_parte_diario.id_labor = '$labor' ";
        }
        
       
        $sqlparte = "SELECT
                          tb_parte_diario.id_parte_diario AS id,
                          tb_parte_diario.id_parte_diario_global AS parte,
                          DATE_FORMAT(tb_parte_diario.fecha, '%d/%m/%Y') AS fecha,
                          CONCAT (tb_personal.apellido, ', ', tb_personal.nombre) AS personal,
                          tb_labor.nombre AS labor,
                          GROUP_CONCAT(tb_cuartel.nombre ORDER BY CAST(tb_cuartel.nombre AS SIGNED) ASC ) AS cuartel,
                          ROUND(Sum(tb_parte_diario.horas_trabajadas), 2) AS horas,
                          ROUND(Sum(tb_parte_diario.has), 2) AS has,
                          tb_parte_diario.obs_labor AS obs_labor,
                          tb_parte_diario.obs_general AS obs_gral
                          FROM
                          tb_parte_diario
                          LEFT JOIN tb_personal ON tb_personal.id_personal = tb_parte_diario.id_personal
                          LEFT JOIN tb_cuartel ON tb_cuartel.id_cuartel = tb_parte_diario.id_cuartel
                          LEFT JOIN tb_labor ON tb_labor.id_labor = tb_parte_diario.id_labor
                          WHERE
                          tb_personal.eventual = '0' AND tb_parte_diario.fecha BETWEEN '$desde' AND '$hasta' and tb_parte_diario.id_finca = $id_finca $consulta_personal$consulta_labor
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
          $personal=utf8_encode($datos['personal']);
          $labor=utf8_encode($datos['labor']);
          $cuartel=utf8_encode($datos['cuartel']);
          $horas=utf8_encode($datos['horas']);
          $has=utf8_encode($datos['has']);
          $obs_labor=utf8_encode($datos['obs_labor']);
          $obs_gral=utf8_encode($datos['obs_gral']);

          
          echo '<tr class="even pointer">
            <td id="'.$id.'">'.$parte.'</td>
            <td id="'.$id.'">'.$fecha.'</td>
            <td id="'.$id.'">'.$personal.'</td>
            <td id="'.$id.'">'.$labor.'</td>
            <td id="'.$id.'">'.$cuartel.'</td>
            <td id="'.$id.'">'.$horas.'</td>
            <td id="'.$id.'">'.$has.'</td>
            <td id="'.$id.'">'.$obs_labor.'</td>
            <td id="'.$id.'">'.$obs_gral.'</td>
          </tr>';
                    ?>
          <script type="text/javascript">
            document.getElementById("botonExcel1").style.visibility = "visible";
          </script>
          <?php
      
          }   
        
        }
        ?>
  </tbody>
</table> 
<?php
 if ($cantidad == 0){

  // echo "No se encontraron registros con el filtro seleccionado";
?>
  <script type="text/javascript">
  document.getElementById("botonExcel1").style.visibility = "hidden";
  </script>
  <?php          
 }
?>
     
				          </div>
                </div>
              </div>
            </div>

<script type="text/javascript">

  init_DataTables();

  $(function() {
        $('#dato_fecha').change(function() {

          document.getElementById("botonExcel1").style.visibility = "hidden";
          
        })
     })
  $(function() {
        $('#dato_labor').change(function() {

          document.getElementById("botonExcel1").style.visibility = "hidden";
          
        })
     })
  $(function() {
        $('#dato_personal').change(function() {

          document.getElementById("botonExcel1").style.visibility = "hidden";
          
        })
     })
</script>            