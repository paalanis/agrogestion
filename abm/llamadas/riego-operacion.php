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
                            <th class="column-title">Operacion</th>
                            </tr>
                        </thead>

                        <tbody>
<?php

$caudalimetro=$_POST['caudalimetro'];

$sqloperacion = "SELECT
                tb_operacion.nombre as operacion,
                tb_operacion.id_operacion as id_operacion,
                tb_operacion.id_finca as id_finca
                FROM
                tb_operacion
                WHERE
                tb_operacion.id_caudalimetro = '$caudalimetro'
                ORDER BY
                operacion ASC
                ";
$rsoperacion = mysqli_query($conexion, $sqloperacion);

$cantidad =  mysqli_num_rows($rsoperacion);

if ($cantidad > 0) { // si existen operacion con de esa finca se muestran, de lo contrario queda en blanco  

$contador = 0;

while ($datos = mysqli_fetch_array($rsoperacion)){
$operacion=utf8_encode($datos['operacion']);
$id_operacion=utf8_encode($datos['id_operacion']);
$id_finca=utf8_encode($datos['id_finca']);

$contador = $contador + 1;

echo '

<tr class="even pointer">
<td class="a-center "><input type="checkbox" class="checkbox_op" value="'.$id_operacion.'" id="op_'.$contador.'"></td>
<td class=" ">'.$operacion.'</td>
</tr>';


}

}
?>
</tbody>
</table> 
<?php
if ($cantidad == 0){

  echo "Debe asignar válvulas a operaciones de riego.";
  }
?>
</div>
</div>
</div>
</div>

<script type="text/javascript">




  $(function() {
        $('.checkbox_op').click(function() {


           var numero = $(this).val()
           var estado = $(this).prop('checked')
           var inicio = $('#idinicial').val()
           var fin = $('#idfinal').val()
           var suma = 0
                     
           if (estado == true) {
            for (var i = inicio; i <= fin; i++) {
             
              var valvula = $('#'+i).attr('name')
                if (valvula == numero) {

                    $('#'+i).prop('checked',true)
                    
                }
               }
           
           }else{

            for (var i = inicio; i <= fin; i++) {

                  var valvula = $('#'+i).attr('name')
                  if (valvula == numero) {

                    $('#'+i).prop('checked',false)
                    
                }
               }
           }

           for (var i = inicio; i <= fin; i++) {

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