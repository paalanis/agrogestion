<?php
session_start();
include '../../conexion/conexion.php';
include '../querys/filtros.php';
include '../querys/presupuesto.php';
$conexion = conectarServidor();
?>


<div class="right_col" role="main" style="min-height: auto;">
  <div class="">

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">

        <div class="x_panel">
          <div class="x_title">
            <h2>Presupuesto <small>Reporte presupuesto</small></h2>
            <div class="clearfix"></div>
          </div>

          <div class="x_content">
            <br>

            <form class="form-horizontal" id="formulario_reporte" role="form" method="post" action="abm/reporte/excel_presupuestado.php">

              <div class="well" style="overflow: auto">
                <div class="col-md-2 col-sm-2 col-xs-12">
                  <fieldset>
                    <select class="form-control col-md-7 col-xs-12" name="dato_mesInicio" id="dato_mesInicio">
                      <option value="0">Mes inicio</option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                      <option value="6">6</option>
                      <option value="7">7</option>
                      <option value="8">8</option>
                      <option value="9">9</option>
                      <option value="10">10</option>
                      <option value="11">11</option>
                      <option value="12">12</option>
                    </select>
                  </fieldset>
                </div>

                <div class="col-md-2 col-sm-2 col-xs-12">
                  <fieldset>
                    <select class="form-control col-md-7 col-xs-12" name="dato_mesFin" id="dato_mesFin">
                      <option value="0">Mes fin</option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                      <option value="6">6</option>
                      <option value="7">7</option>
                      <option value="8">8</option>
                      <option value="9">9</option>
                      <option value="10">10</option>
                      <option value="11">11</option>
                      <option value="12">12</option>
                    </select>
                  </fieldset>
                </div>

                <div class="col-md-2 col-sm-3 col-xs-12">
                  <fieldset>
                    <select class="form-control col-md-7 col-xs-12" name="dato_labor" id="dato_labor">
                      <option value="0">Labores</option>
                      <?php
                      $rslabores = queryLabores(false);
                      while ($sql_labores = mysqli_fetch_array($rslabores)) {
                        $idlabores = $sql_labores['id'];
                        $labores = $sql_labores['nombre'];
                        echo utf8_encode('<option value=' . $idlabores . '>' . $labores . '</option>');
                      }
                      ?>
                    </select>
                  </fieldset>
                </div>

                <div class="col-md-2 col-sm-4 col-xs-12">
                  <fieldset>
                    <select class="form-control col-md-7 col-xs-12" name="dato_campania" id="dato_campania">
                      <option value="0">Campaña</option>
                      <?php
                      $rscampania = queryCampania();
                      while ($sql_campania = mysqli_fetch_array($rscampania)) {
                        $idcampania = $sql_campania['id'];
                        $campania = $sql_campania['nombre'];
                        echo ('<option value=' . $idcampania . '>' . $campania . '</option>');
                      }
                      ?>
                    </select>
                  </fieldset>
                </div>

                <div class="col-md-2 col-sm-4 col-xs-12">
                  <fieldset>
                    <select class="form-control col-md-7 col-xs-12" name="dato_agrupado" id="dato_agrupado">
                      <option value="">Agrupado Labor</option>
                      <option value="1">Si</option>
                      <option value="">No</option>
                    </select>
                  </fieldset>
                </div>

                <div class="col-md-2 col-sm-4 col-xs-12">
                  <fieldset>
                    <select class="form-control col-md-7 col-xs-12" name="dato_version" id="dato_version">
                      <option value="0">Versión</option>
                      <option value="1">Original</option>
                      <option value="2">Ajustado</option>
                    </select>
                  </fieldset>
                </div>

              </div>

              <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-0">
                  <button class="btn btn-default" id="boton_limpiar" type="reset">Limpiar</button>
                  <button type="button" id="boton_buscar" class="btn btn-primary" onclick="reporte('controlPresupuestado')">Buscar</button>
                  <button type="submit" class="btn btn-info" id="botonExcel1" aria-label="Left Align">
                    <span class="glyphicon glyphicon-save" aria-hidden="true"></span> Descargar</button>
                </div>
              </div>

              <div class="ln_solid"></div>

              <div id="div_reporte"></div>

            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    document.getElementById("botonExcel1").style.visibility = "hidden";
    init_daterangepicker();
    init_daterangepicker_reservation();

  });
</script>