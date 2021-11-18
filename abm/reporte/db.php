<?php
session_start();
if (!isset($_SESSION['usuario'])) {
header("Location: index.php");
}
if ($_SESSION['id_finca_usuario'] == '0') {
session_destroy();
header("Location: index.php");
}
$id_finca=$_SESSION['id_finca_usuario'];
include 'conexion/conexion.php';
if (mysqli_connect_errno()) {
printf("La conexión con el servidor de base de datos falló comuniquese con su administrador: %s\n", mysqli_connect_error());
exit();
}
date_default_timezone_set("America/Argentina/Mendoza");
$hoy = date("Y-m-d");
$mes = date("m");
$sqljor_mes = "SELECT
ROUND(Sum(tb_parte_diario.horas_trabajadas)/8) as jor_mes
FROM
tb_parte_diario
LEFT JOIN tb_personal ON tb_personal.id_personal = tb_parte_diario.id_personal
WHERE
tb_parte_diario.id_finca = 14 AND
tb_personal.eventual = '1' AND
DATE_FORMAT(tb_parte_diario.fecha, '%m') = '$mes'";
$rsjor_mes = mysqli_query($conexion, $sqljor_mes); 

$sqljor_total = "SELECT
ROUND(Sum(tb_parte_diario.horas_trabajadas)/8) as jor_total
FROM
tb_parte_diario
LEFT JOIN tb_personal ON tb_personal.id_personal = tb_parte_diario.id_personal
WHERE
tb_parte_diario.id_finca = '$id_finca' AND
tb_personal.eventual = '1' AND
tb_parte_diario.fecha BETWEEN '2017-06-01' AND '$hoy'";
$rsjor_total = mysqli_query($conexion, $sqljor_total); 

$sqltractor_mes = "SELECT
ROUND(Sum(tb_parte_diario.horas_tractor)) AS tractor_mes
FROM
tb_parte_diario
WHERE
tb_parte_diario.id_finca = '$id_finca' AND
DATE_FORMAT(tb_parte_diario.fecha, '%m') = '$mes'";
$rstractor_mes = mysqli_query($conexion, $sqltractor_mes); 

$sqltractor_total = "SELECT
ROUND(Sum(tb_parte_diario.horas_tractor)) AS tractor_total
FROM
tb_parte_diario
WHERE
tb_parte_diario.id_finca = '$id_finca' AND
tb_parte_diario.fecha BETWEEN '2017-06-01' AND '$hoy'";
$rstractor_total = mysqli_query($conexion, $sqltractor_total); 


while ($datos = mysqli_fetch_array($rsjor_mes)){
	$jor_mes=utf8_encode($datos['jor_mes']);
}

while ($datos = mysqli_fetch_array($rsjor_total)){
	$jor_total=utf8_encode($datos['jor_total']);
}

while ($datos = mysqli_fetch_array($rstractor_mes)){
	$tractor_mes=utf8_encode($datos['tractor_mes']);
}

while ($datos = mysqli_fetch_array($rstractor_total)){
	$tractor_total=utf8_encode($datos['tractor_total']);
}

$sqltop = "SELECT
tb_labor.nombre AS labor,
ROUND(Sum(tb_parte_diario.horas_trabajadas)/8) AS jornales
FROM
tb_parte_diario
LEFT JOIN tb_labor ON tb_labor.id_labor = tb_parte_diario.id_labor
LEFT JOIN tb_personal ON tb_personal.id_personal = tb_parte_diario.id_personal
WHERE
tb_personal.eventual = '1' AND tb_parte_diario.id_finca = '$id_finca' AND
tb_parte_diario.fecha BETWEEN '2017-06-01' AND '$hoy'
GROUP BY
tb_labor.id_labor
ORDER BY
jornales DESC
LIMIT 5";
$rstop = mysqli_query($conexion, $sqltop); 

$lista_lab = array();
$lista_jor = array();

while ($datos = mysqli_fetch_array($rstop)){
	$labor=strtolower(utf8_encode($datos['labor']));
	$jornales=utf8_encode($datos['jornales']);

	array_push($lista_lab, $labor);
	array_push($lista_jor, $jornales);

}

$sqljor_has = "SELECT
tb_labor.nombre AS labor,
ROUND(Sum(tb_parte_diario.horas_trabajadas)/8/Sum(tb_parte_diario.has),2) as jor_ha
FROM
tb_parte_diario
LEFT JOIN tb_labor ON tb_labor.id_labor = tb_parte_diario.id_labor
LEFT JOIN tb_personal ON tb_personal.id_personal = tb_parte_diario.id_personal
WHERE
tb_personal.eventual = '1' AND tb_parte_diario.id_finca = '$id_finca' AND
tb_parte_diario.fecha BETWEEN '2017-06-01' AND '$hoy' AND
tb_parte_diario.has NOT LIKE 0
GROUP BY
tb_labor.id_labor,
tb_labor.nombre
ORDER BY
jor_ha DESC";
$rsjor_has = mysqli_query($conexion, $sqljor_has); 

$jor_ha_lab = array();
$jor_ha_jor = array();

while ($datos = mysqli_fetch_array($rsjor_has)){
	$labor=strtolower(utf8_encode($datos['labor']));
	$jor_ha=utf8_encode($datos['jor_ha']);

	array_push($jor_ha_lab, $labor);
	array_push($jor_ha_jor, $jor_ha);

}

	

?>
<script src="js/jquery.min.js"></script>
<script type="text/javascript">
	
$(document).ready(function() {
			
if ($('#riego').length ){ 
  
  var ctx = document.getElementById("riego");
  var mybarChart = new Chart(ctx, {
	type: 'bar',
	data: {
	  labels: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
	  datasets: [{
		label: 'Mm 2017',
		backgroundColor: "#3386FF",
		data: [51, 30, 40, 28, 92, 50, 45]
	  }, {
		label: 'Mm 2016',
		backgroundColor: "#85B8F2",
		data: [41, 56, 25, 48, 72, 34, 12, 41, 56, 25, 48, 72, 34, 12]
	  }]
	},

	options: {
	  scales: {
		yAxes: [{
		  ticks: {
			beginAtZero: true
		  }
		}]
	  }
	}
  });
  
} 

if ($('#jor_has').length ){ 
  
  var ctx = document.getElementById("jor_has");
  var mybarChart = new Chart(ctx, {
	type: 'bar',
	data: {
	  labels: <?php echo json_encode($jor_ha_lab);?>,
	  datasets: [{
		label: '2017',
		backgroundColor: "#28B463",
		data: <?php echo json_encode($jor_ha_jor);?>
	  }, {
		label: '2016',
		backgroundColor: "#ABEBC6",
		data: [5, 4, 3, 2, 1, 2, 1]
	  }]
	},

	options: {
	  scales: {
		yAxes: [{
		  ticks: {
			beginAtZero: true
		  }
		}]
	  }
	}
  });
  
} 


if ($('#top_cinco').length ){
  
  var ctx = document.getElementById("top_cinco");
  var data = {
	datasets: [{
	  data: <?php echo json_encode($lista_jor);?>,
	  backgroundColor: [
		"#455C73",
		"#9B59B6",
		"#BDC3C7",
		"#26B99A",
		"#3498DB"
	  ],
	  label: 'My dataset' // for legend
	}],
	labels: <?php echo json_encode($lista_lab);?>
  };

  var pieChart = new Chart(ctx, {
	data: data,
	type: 'pie',
	otpions: {
	  legend: true
	}
  });
  
}		

			});	



</script>

