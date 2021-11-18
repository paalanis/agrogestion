<?php
session_start();
if (!isset($_SESSION['usuario'])) {
header("Location: ../../index.php");
}
if ($_SESSION['id_finca_usuario'] == '0') {
session_destroy();
header("Location: ../../index.php");
}
if (PHP_SAPI == 'cli')
	die('Este ejemplo sólo se puede ejecutar desde un navegador Web');

/** Incluye PHPExcel */
require_once "../../classes/PHPExcel.php";
// Crear nuevo objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Propiedades del documento
$objPHPExcel->getProperties()->setCreator("Obed Alvarado")
							 ->setLastModifiedBy("Obed Alvarado")
							 ->setTitle("Office 2010 XLSX Documento de prueba")
							 ->setSubject("Office 2010 XLSX Documento de prueba")
							 ->setDescription("Documento de prueba para Office 2010 XLSX, generado usando clases de PHP.")
							 ->setKeywords("office 2010 openxml php")
							 ->setCategory("Archivo con resultado de prueba");



// Combino las celdas desde A1 hasta E1
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:O1');

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Reporte - Labores de terceros')
            ->setCellValue('A2', 'N° Parte')
            ->setCellValue('B2', 'Fecha')
            ->setCellValue('C2', 'Finca')
            ->setCellValue('D2', 'Modulo')
            ->setCellValue('E2', 'Razon Social')
			->setCellValue('F2', 'Empresa SS.')
			->setCellValue('G2', 'Labor')
			->setCellValue('H2', 'Cuartel')
			->setCellValue('I2', 'Variedad')
			->setCellValue('J2', 'Has')
			->setCellValue('K2', 'Jornales')
			->setCellValue('L2', 'Has trabajadas')
			->setCellValue('M2', 'Rendimiento')
			->setCellValue('N2', 'Obs labor')
			->setCellValue('O2', 'Obs general');

			
// Fuente de la primera fila en negrita
$boldArray = array('font' => array('bold' => true,),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));

$objPHPExcel->getActiveSheet()->getStyle('A1:O2')->applyFromArray($boldArray);		

	
			
//Ancho de las columnas
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);	
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);	
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);	
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);	
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(40);

/*Extraer datos de MYSQL*/
	# conectare la base de datos
$fecha=$_REQUEST['dato_fecha'];

$desde = substr($fecha,0,10);
$hasta = substr($fecha,13,23);
$desde = date_create($desde);
$hasta = date_create($hasta);
$desde = date_format($desde,"Y-m-d");
$hasta = date_format($hasta,"Y-m-d");

$id_finca = $_SESSION['id_finca_usuario'];

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
include '../../conexion/conexion.php';
if (mysqli_connect_errno()) {
printf("La conexión con el servidor de base de datos falló comuniquese con su administrador: %s\n", mysqli_connect_error());
exit();
}
$sqlpartes = "SELECT
			tb_parte_diario.id_parte_diario_global AS parte,
			DATE_FORMAT(tb_parte_diario.fecha, '%d/%m/%Y') AS fecha,
			tb_finca.nombre AS finca,
			tb_empresa.empresa AS empresa,
			tb_centro_costo.centro_costo AS centro_costo,
			CONCAT(tb_personal.apellido,', ',tb_personal.nombre) AS personal,
			tb_labor.nombre AS labor,
			tb_cuartel.nombre AS cuartel,
			tb_variedad.nombre AS variedad,
			tb_cuartel.has AS has,
			FORMAT(tb_parte_diario.horas_trabajadas/8, 2) AS jornales,
			FORMAT(tb_parte_diario.has, 2) AS horas_trabajadas,
			FORMAT(tb_parte_diario.horas_trabajadas/8/tb_parte_diario.has, 2) AS rendimiento,
			tb_parte_diario.obs_labor AS obs_labor,
			tb_parte_diario.obs_general AS obs_gral
			FROM
			tb_parte_diario
			LEFT JOIN tb_finca ON tb_parte_diario.id_finca = tb_finca.id_finca
			LEFT JOIN tb_personal ON tb_parte_diario.id_personal = tb_personal.id_personal
			LEFT JOIN tb_cuartel ON tb_parte_diario.id_cuartel = tb_cuartel.id_cuartel
			LEFT JOIN tb_variedad ON tb_cuartel.id_variedad = tb_variedad.id_variedad
			LEFT JOIN tb_labor ON tb_labor.id_labor = tb_parte_diario.id_labor
			LEFT JOIN tb_empresa ON tb_empresa.id_empresa = tb_finca.id_empresa
			LEFT JOIN tb_centro_costo ON tb_centro_costo.id_centro_costo = tb_empresa.id_centro_costo
            WHERE
            tb_parte_diario.fecha BETWEEN '$desde' AND  '$hasta' AND tb_personal.eventual = '1' and tb_parte_diario.id_finca = $id_finca $consulta_personal$consulta_labor
            ORDER BY
            tb_parte_diario.fecha DESC";


	$cel=3;//Numero de fila donde empezara a crear  el reporte

	$rslabores = mysqli_query($conexion, $sqlpartes);
	$cantidad =  mysqli_num_rows($rslabores);
	while ($datos = mysqli_fetch_array($rslabores)){
	  $parte=utf8_encode($datos['parte']);
      $fecha=utf8_encode($datos['fecha']);
      $finca=utf8_encode($datos['finca']);
      $personal=utf8_encode($datos['personal']);
      $labor=utf8_encode($datos['labor']);
      $cuartel=utf8_encode($datos['cuartel']);
      $variedad=utf8_encode($datos['variedad']);
      $has=utf8_encode($datos['has']);
      $jornales=utf8_encode($datos['jornales']);
      $horas_trabajadas=utf8_encode($datos['horas_trabajadas']);
      $rendimiento=utf8_encode($datos['rendimiento']);
      $obs_labor=utf8_encode($datos['obs_labor']);
      $obs_gral=utf8_encode($datos['obs_gral']);
      $empresa=utf8_encode($datos['empresa']);
      $centro_costo=utf8_encode($datos['centro_costo']);
		
			$a="A".$cel;
			$b="B".$cel;
			$c="C".$cel;
			$d="D".$cel;
			$e="E".$cel;
			$f="F".$cel;
			$g="G".$cel;
			$h="H".$cel;
			$i="I".$cel;
			$j="J".$cel;
			$k="K".$cel;
			$l="L".$cel;
			$m="M".$cel;
			$n="N".$cel;
			$o="O".$cel;

			// Agregar datos
			$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($a, $parte)
            ->setCellValue($b, $fecha)
            ->setCellValue($c, $finca)
            ->setCellValue($d, $centro_costo)
            ->setCellValue($e, $empresa)
            ->setCellValue($f, $personal)
			->setCellValue($g, $labor)
			->setCellValue($h, $cuartel)
			->setCellValue($i, $variedad)
			->setCellValue($j, $has)
			->setCellValue($k, $jornales)
			->setCellValue($l, $horas_trabajadas)
			->setCellValue($m, $rendimiento)
			->setCellValue($n, $obs_labor)
			->setCellValue($o, $obs_gral);
			
			$cel+=1;
	}
			
	
// /*Fin extracion de datos MYSQL*/
$rango="A2:$o";
$styleArray = array('font' => array( 'name' => 'Arial','size' => 10),
'borders'=>array('allborders'=>array('style'=> PHPExcel_Style_Border::BORDER_THIN,'color'=>array('argb' => 'FFF')))
);
$objPHPExcel->getActiveSheet()->getStyle($rango)->applyFromArray($styleArray);
// Cambiar el nombre de hoja de cálculo
$objPHPExcel->getActiveSheet()->setTitle('Reporte personal tercerizado');


// Establecer índice de hoja activa a la primera hoja , por lo que Excel abre esto como la primera hoja
$objPHPExcel->setActiveSheetIndex(0);


// Redirigir la salida al navegador web de un cliente ( Excel5 )
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="reporte_terceros.xls"');
header('Cache-Control: max-age=0');
// Si usted está sirviendo a IE 9 , a continuación, puede ser necesaria la siguiente
header('Cache-Control: max-age=1');

// Si usted está sirviendo a IE a través de SSL , a continuación, puede ser necesaria la siguiente
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;