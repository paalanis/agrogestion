<?php
session_start();
if (!isset($_SESSION['usuario'])) {
header("Location: ../index.php");
}
if ($_SESSION['id_finca_usuario'] == '0') {
session_destroy();
header("Location: ../../index.php");
}
?>
<!-- <div class="right_col" role="main" style="min-height: auto; "> -->
<div class="">

<div class="clearfix"></div>       
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                 
                 <div class="x_content">

                    <div class="table-responsive">
                      <table class="table table-striped jambo_table bulk_action">
                        <thead>
                          <tr class="headings">
                            <th>
                              <input type="checkbox" id="check-all" class="flat">
                            </th>
                            <th class="column-title">Nombre</th>
                            <th class="column-title">Hileras</th>
                            <th class="column-title">Has</th>
                            <th class="column-title">Hil-Selec</th>
                            <th class="column-title">Has-Trabaja</th>
                            <th class="bulk-actions" colspan="7">
                              <a class="antoo" style="color:#fff; font-weight:500;">Cuarteles seleccionados ( <span class="action-cnt"> </span> ) <i class="fa fa-chevron-down"></i></a>
                            </th>
                          </tr>
                        </thead>

                        <tbody>

<?php

$finca=$_SESSION['id_finca_usuario'];

include '../../conexion/conexion.php';

if (mysqli_connect_errno()) {
printf("La conexión con el servidor de base de datos falló comuniquese con su administrador: %s\n", mysqli_connect_error());
exit();
}

$sqlcuarteles = "SELECT
tb_cuartel.nombre as cuartel,                        
CAST(tb_cuartel.nombre AS SIGNED) as orden_cuartel,
tb_variedad.nombre as variedad,
tb_cuartel.has as has,
tb_cuartel.id_cuartel as id_cuartel,
tb_cuartel.hileras as hileras
FROM
tb_cuartel
INNER JOIN tb_variedad ON tb_variedad.id_variedad = tb_cuartel.id_variedad
WHERE
tb_cuartel.id_finca = '$finca'
ORDER BY
orden_cuartel ASC";
$rscuarteles = mysqli_query($conexion, $sqlcuarteles);

$cantidad =  mysqli_num_rows($rscuarteles);

if ($cantidad > 0) { // si existen cuarteles con de esa finca se muestran, de lo contrario queda en blanco  

$contador = 0;

while ($datos = mysqli_fetch_array($rscuarteles)){
$nombrecuartel=utf8_encode($datos['cuartel']);
$variedad=utf8_encode($datos['variedad']);
$has=$datos['has'];
$id=$datos['id_cuartel'];
$hileras=$datos['hileras'];

$contador = $contador + 1;

echo '

<tr class="even pointer">
<td class="a-center ">
<input type="checkbox" class="flat" name="table_records" value="'.$id.'" id="dato_'.$contador.'">
</td>
<td class=" ">'.$nombrecuartel.'</td>
<td class=" " id="hileras_'.$contador.'" name="'.$hileras.'">'.$hileras.'</td>
<td class=" " id="has_'.$contador.'"  name="'.$has.'">'.$has.'</td>
<td class=" "><input type="number" class="form-control-hil" style="height:25px" min="1" id="hileras_seleccionadas'.$contador.'" name="'.$contador.'" value="'.$hileras.'" ></td>
<td class=" "><input type="text" class="form-control-has" style="height:25px" autocomplete="off" id="dato_has-seleccionadas'.$contador.'" name="'.$contador.'" value="0" ></td>
</tr>';


}   

$idinicial=1;
$idfinal=$contador;

echo'
<input type="hidden" class="form-control" id="dato_idinicial" value="'.$idinicial.'">
<input type="hidden" class="form-control" id="dato_totalhas" value="0">
<input type="hidden" class="form-control" id="dato_idfinal" value="'.$idfinal.'">
<input type="hidden" class="form-control" id="control_cuartel" value="1">'; 


}
?>


                        </tbody>
                      </table>
                    </div>
							
						
                  </div>
                </div>
              </div>


            
                  </div>
               <!--  </div> -->
          


<script>
// iCheck
$(document).ready(function() {
    if ($("input.flat")[0]) {
        $(document).ready(function () {
            $('input.flat').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });
        });
    }
});
// /iCheck

// Table
$('table input').on('ifChecked', function () {
    checkState = '';
    $(this).parent().parent().parent().addClass('selected');
    var numero = $(this).attr('id').split('_',2) 
    var has = $('#has_'+numero[1]).attr('name')
    var hil = $('#hileras_'+numero[1]).attr('name')
    var hil_t = $('#hileras_seleccionadas'+numero[1]).val()
    var has = parseFloat(has/hil*hil_t);
    var has = has.toFixed(3)
    $('#dato_has-seleccionadas'+numero[1]).val(has)

    var idinicial = $('#dato_idinicial').val();
    var idfinal = $('#dato_idfinal').val();
    var suma = 0;

    for (var i = idinicial; i <= idfinal; i++) {
      
      var ha = $('#dato_has-seleccionadas'+i).val();
      var suma = parseFloat(ha) + suma;
        
      $('#dato_totalhas').val(suma);

    };
    countChecked();
});


$('table input').on('ifUnchecked', function () {
    checkState = '';
    $(this).parent().parent().parent().removeClass('selected');
    var numero = $(this).attr('id').split('_',2) 
    $('#dato_has-seleccionadas'+numero[1]).val('0') 
    $('#hileras_seleccionadas'+numero[1]).val($('#hileras_'+numero[1]).attr('name')) 

    var idinicial = $('#dato_idinicial').val();
    var idfinal = $('#dato_idfinal').val();
    var suma = 0;

    for (var i = idinicial; i <= idfinal; i++) {
                
    var ha = $('#dato_has-seleccionadas'+i).val();
    var suma = parseFloat(ha) + suma;

    $('#dato_totalhas').val(suma);

    }
    countChecked();
});

var checkState = '';

$('.bulk_action input').on('ifChecked', function () {
    checkState = '';
    $(this).parent().parent().parent().addClass('selected');
    countChecked();
});
$('.bulk_action input').on('ifUnchecked', function () {
    checkState = '';
    $(this).parent().parent().parent().removeClass('selected');
    countChecked();
});
$('.bulk_action input#check-all').on('ifChecked', function () {
    checkState = 'all';
    countChecked();
});
$('.bulk_action input#check-all').on('ifUnchecked', function () {
    checkState = 'none';
    countChecked();
});

function countChecked() {
    if (checkState === 'all') {
        $(".bulk_action input[name='table_records']").iCheck('check');
    }
    if (checkState === 'none') {
        $(".bulk_action input[name='table_records']").iCheck('uncheck');
    }

    var checkCount = $(".bulk_action input[name='table_records']:checked").length;

    if (checkCount) {
        var total = parseFloat($('#dato_totalhas').val())
        var total = total.toFixed(3)
        $('.column-title').hide();
        $('.bulk-actions').show();
        $('.action-cnt').html(total + ' Has totales');
    } else {
        $('.column-title').show();
        $('.bulk-actions').hide();
    }
}

 
$(function() {
        $('.form-control-has').change(function() {

           var numero = $(this).attr('name')
           var estado = $('#'+numero).prop('checked')

           var idinicial = $('#dato_idinicial').val();
           var idfinal = $('#dato_idfinal').val();
           var suma = 0;

                    
           if (estado == true) {

             if ($('#dato_has-seleccionadas'+numero).val() == 0) {

                  $('#dato_has-seleccionadas'+numero).val($('#has_'+numero).attr('name'))
                
                }

           }

           for (var i = idinicial; i <= idfinal; i++) {
            
            var ha = $('#dato_has-seleccionadas'+i).val();
            var suma = parseFloat(ha) + suma;

            $('#dato_totalhas').val(suma);

            }

            var total = suma.toFixed(3)
            $('.action-cnt').html(total + ' Has totales')
   
        })
      })


$(function() {
        $('.form-control-hil').change(function() {

           var numero = $(this).attr('name')
           var estado = $('#'+numero).prop('checked')

           var idinicial = $('#dato_idinicial').val();
           var idfinal = $('#dato_idfinal').val();
           var suma = 0;

           if (estado == true) {

              var valor_hil = $('#hileras_seleccionadas'+numero).val()
      
              var has = $('#has_'+numero).attr('name')
              var hil = $('#hileras_'+numero).attr('name')
  
              var hil_t = $('#hileras_seleccionadas'+numero).val()

                  if (hil_t == '0') {

                    $('#hileras_seleccionadas'+numero).val(hil)
                    var hil_t2 = hil
                    var has = has/hil*hil_t2;
                    var has = parseFloat(has)
                    var has = has.toFixed(3)
                    $('#dato_has-seleccionadas'+numero).val(has) 
                  
                  }else{

                      var has = has/hil*hil_t;
                      var has = parseFloat(has)
                      var has = has.toFixed(3)
                      $('#dato_has-seleccionadas'+numero).val(has) 
              
                }
               }


                      for (var i = idinicial; i <= idfinal; i++) {
                       
                        var ha = $('#dato_has-seleccionadas'+i).val();
                        var suma = parseFloat(ha) + suma;
                        var suma = parseFloat(suma)

                        $('#dato_totalhas').val(suma);
                        
                        };

                        var total = suma.toFixed(3)
                        $('.action-cnt').html(total + ' Has totales')

   
        })
      })


</script>