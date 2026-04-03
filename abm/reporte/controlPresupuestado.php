<?php
session_start();
include '../../conexion/conexion.php';
include '../querys/presupuesto.php';
$conexion = conectarServidor();

$mesInicio = $_REQUEST['dato_mesInicio'];
$mesFin = $_REQUEST['dato_mesFin'];
$campania = $_REQUEST['dato_campania'];
$version = $_REQUEST['dato_version'];
$labor = $_REQUEST['dato_labor'];
$agrupado = (bool)$_REQUEST['dato_agrupado'];

calculaPrespuestado($mesInicio, $mesFin, $campania, $agrupado);

?>

<div class="">

  <div class="clearfix"></div>
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">

      <div class="x_content">

        <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <?php if (!$agrupado) {
                echo '<th>Mes </th>';
              } ?>
              <th>Año </th>
              <th>Labor </th>
              <th>Prespuestado </th>
              <th>Unidad </th>
              </th>
            </tr>
          </thead>

          <tbody>

            <?php

            $cantidad =  reportePresupuestado($labor, $agrupado);
            if ($cantidad > 0) { // si existen control con de esa finca se muestran, de lo contrario queda en blanco  

            ?>
              <script type="text/javascript">
                document.getElementById("botonExcel1").style.visibility = "visible";
              </script>
            <?php

            }
            ?>
          </tbody>
        </table>
        <?php
        if ($cantidad == 0) {

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
    $('.form-control').change(function() {

      document.getElementById("botonExcel1").style.visibility = "hidden";

    })
  })
</script>