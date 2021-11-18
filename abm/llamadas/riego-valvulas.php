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
?>
<div class="">

<div class="clearfix"></div>       
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                 
                 <div class="x_content">

                      <table class="table table-striped jambo_table bulk_action">
                        <thead>
                          <tr class="headings">
                            <th class="column-title">#</th>
                            <th class="column-title">Valvula</th>
                            <th class="column-title">Has</th>
                          </tr>
                        </thead>

                        <tbody>
   
<?php

$caudalimetro=$_POST['caudalimetro'];

$sqlvalvulas = "SELECT
                tb_valvula.nombre as valvula,
                tb_valvula.id_valvula as id_valvula,
                tb_operacion.nombre as operacion,
                tb_operacion_asignada.id_operacion as id_operacion,
                tb_valvula.has_asignadas as has
                FROM
                tb_operacion_asignada
                INNER JOIN tb_valvula ON tb_valvula.id_valvula = tb_operacion_asignada.id_valvula
                INNER JOIN tb_operacion ON tb_operacion.id_operacion = tb_operacion_asignada.id_operacion
                WHERE
                tb_valvula.id_caudalimetro = '$caudalimetro'
                ORDER BY
                tb_operacion.nombre ASC";
$rsvalvulas = mysqli_query($conexion, $sqlvalvulas);

$cantidad =  mysqli_num_rows($rsvalvulas);

if ($cantidad > 0) { // si existen valvulas con de esa finca se muestran, de lo contrario queda en blanco  

$contador = 0;

while ($datos = mysqli_fetch_array($rsvalvulas)){
$valvula=utf8_encode($datos['valvula']);
$id_valvula=utf8_encode($datos['id_valvula']);
$operacion=utf8_encode($datos['operacion']);
$id_operacion=utf8_encode($datos['id_operacion']);
$has=$datos['has'];

$contador = $contador + 1;

echo '

<tr class="even pointer">
<td class="a-center "><input type="checkbox" class="checkbox_vl" name="'.$id_operacion.'" value="'.$id.'" id="'.$contador.'"></td>
<td class=" ">'.$valvula.'</td>
<td class=""><input type="text" class="form-control" id="has_'.$contador.'"  value="'.$has.'" readonly></td>
</tr>';


}

$idinicial=1;
$idfinal=$contador;

  echo '<input type="hidden" class="form-control" id="idinicial" value="'.$idinicial.'">
  <input type="hidden" class="form-control" id="totalhas" value="0">
  <input type="hidden" class="form-control" id="idfinal" value="'.$idfinal.'">';    
}
?>
</tbody>
</table> 
<?php
if ($cantidad == 0){

  echo "Debe asignar válvulas a operaciones de riego.<input type='hidden' class='form-control' id='totalhas' value='0'>";
  }
?>

</div>
</div>
</div>
</div>




<script type="text/javascript">


  $(function() {
        $('.checkbox_vl').change(function() {

                var numero = $(this).val()
                var idinicial = $('#idinicial').val();
                var idfinal = $('#idfinal').val();
                var suma = 0;

                for (var i = idinicial; i <= idfinal; i++) {
                  
                  var estado = $('#'+i).prop('checked')
                  
                  if (estado == true) {

                    var ha = $('#has_'+i).val();
                    var suma = parseFloat(ha) + suma;
                    $('#totalhas').val(suma.toFixed(2));

                  }else{

                    var ha = 0
                    var suma = parseFloat(ha) + suma;
                    $('#totalhas').val(suma.toFixed(2));

                  }

                };
   
        })
      })



 </script>      