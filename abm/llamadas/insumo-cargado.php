<?php
session_start();
if (!isset($_SESSION['usuario'])) {
header("Location: ../../index.php");
}
if ($_SESSION['id_finca_usuario'] == '0') {
session_destroy();
header("Location: ../../index.php");
}
$deposito=$_SESSION['deposito'];
include '../../conexion/conexion.php';
if (mysqli_connect_errno()) {
printf("La conexión con el servidor de base de datos falló comuniquese con su administrador: %s\n", mysqli_connect_error());
exit(); } 

$sqlinsumos = "SELECT
tb_consumo_insumos_".$deposito.".id_consumo_insumos as id,
tb_insumo.nombre_comercial as nombre,
tb_consumo_insumos_".$deposito.".egreso as cantidad,
tb_unidad.nombre as unidad
FROM
tb_consumo_insumos_".$deposito."
INNER JOIN tb_insumo ON tb_consumo_insumos_".$deposito.".id_insumo = tb_insumo.id_insumo
INNER JOIN tb_unidad ON tb_insumo.id_unidad = tb_unidad.id_unidad
WHERE
tb_consumo_insumos_".$deposito.".estado = '0'
ORDER BY
tb_consumo_insumos_".$deposito.".id_consumo_insumos ASC
";
$rsinsumos = mysqli_query($conexion, $sqlinsumos);

$cantidad =  mysqli_num_rows($rsinsumos);
if ($cantidad > 0) {
$contador = 0;

echo '<div class="">

<div class="clearfix"></div>       
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                 
                 <div class="x_content">

                 	<table class="table table-hover">
                      
                      <tbody>';

while ($datos = mysqli_fetch_array($rsinsumos)){
$nombreinsumo=utf8_encode($datos['nombre']);
$cantidad=utf8_encode($datos['cantidad']);
$unidad=utf8_encode($datos['unidad']);
$id=$datos['id'];
$contador = $contador + 1;
echo '
<tr>
<td id="insumo_'.$contador.'" name="'.$nombreinsumo.'">'.$nombreinsumo.'</td>
<td id="cantidad_'.$contador.'" name="'.$cantidad.'">'.$cantidad.' '.$unidad.'</td>
<td id="'.$id.'" class=" last"><a href="javascript:elimina_insumo('.$id.');" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Eliminar </a></td>
</tr>
';
}    

echo '      </tbody>
                    </table>';      

}else{

echo 'Lista de insumos cargados';

}


?>



<script type="text/javascript">

function elimina_insumo(id){

pars="id="+id+'&';

$("#div_insumos_cargados").html('<div class="text-center"><div class="loadingsm"></div></div>');
$.ajax({
url : "abm/eliminar/insumo-temp.php",
data : pars,
dataType : "json",
type : "get",
success: function(data){
if (data.success == 'true') {
$("#div_insumos_cargados").load("abm/llamadas/insumo-cargado.php");
} else {
$('#div_insumos_cargados').html('<div id="mensaje_general" class="alert alert-danger alert-dismissible" style="height:47px" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Error reintente!</div>');        
setTimeout("$('#mensaje_general').alert('close')", 2000);
}
}
});

}

</script>