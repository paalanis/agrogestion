<?php
session_start();
if (!isset($_SESSION['usuario'])) {
header("Location: ../../index.php");
}
if ($_SESSION['id_finca_usuario'] == '0') {
session_destroy();
header("Location: ../../index.php");
}
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

require '../../vendor/autoload.php';

$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

    return;
}
$objPHPExcel = new Spreadsheet();

// Propiedades del documento
$objPHPExcel->getProperties()->setCreator("Obed Alvarado")
							 ->setLastModifiedBy("Obed Alvarado")
							 ->setTitle("Office 2010 XLSX Documento de prueba")
							 ->setSubject("Office 2010 XLSX Documento de prueba")
							 ->setDescription("Documento de prueba para Office 2010 XLSX, generado usando clases de PHP.")
							 ->setKeywords("office 2010 openxml php")
							 ->setCategory("Archivo con resultado de prueba");

// Combino las celdas desde A1 hasta E1
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:Q1');

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Reporte - Labores propias')
            ->setCellValue('A2', 'N° Parte')
			->setCellValue('B2', 'Personal')
            ->setCellValue('C2', 'Fecha')
            ->setCellValue('D2', 'Mes')
            ->setCellValue('E2', 'Año')
            ->setCellValue('F2', 'Empresa')
            ->setCellValue('G2', 'Finca')
            ->setCellValue('H2', 'Modulo')
			->setCellValue('I2', 'Cuartel')
			->setCellValue('J2', 'Has')
			->setCellValue('K2', 'Horas')
			->setCellValue('L2', 'Labor')
			->setCellValue('M2', 'Tractor')
			->setCellValue('N2', 'Implemento')
			->setCellValue('O2', 'Horas_Tractor')
			->setCellValue('P2', 'Obs_labor')
			->setCellValue('Q2', 'Obs_gral');

			
// Fuente de la primera fila en negrita
$boldArray = array('font' => array('bold' => true,),'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER));

$objPHPExcel->getActiveSheet()->getStyle('A1:Q2')->applyFromArray($boldArray);		

//Ancho de las columnas
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);	
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);	
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);	
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);	
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(35);		

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

$labor=$_POST['dato_labor'];
$personal=$_POST['dato_personal'];

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
				CONCAT (tb_personal.apellido, ', ', tb_personal.nombre) AS personal,
				DATE_FORMAT(tb_parte_diario.fecha, '%d/%m/%Y') AS fecha,
				DATE_FORMAT(tb_parte_diario.fecha, '%m') AS mes,
				DATE_FORMAT(tb_parte_diario.fecha, '%Y') AS anio,
				tb_empresa.empresa AS empresa,
				tb_finca.nombre AS finca,
				tb_centro_costo.centro_costo AS modulo,
				GROUP_CONCAT(tb_cuartel.nombre ORDER BY tb_cuartel.nombre ASC ) AS cuartel,
				ROUND(Sum(tb_parte_diario.has), 2) AS has,
				ROUND(Sum(tb_parte_diario.horas_trabajadas), 2) AS horas,
				tb_labor.nombre AS labor,
				tb_tractor.nombre AS tractor,
				tb_implemento.nombre AS implemento,
				ROUND(Sum(tb_parte_diario.horas_tractor),2) AS horas_tractor,
				tb_parte_diario.obs_labor AS obs_labor,
				tb_parte_diario.obs_general AS obs_gral
				FROM
				tb_parte_diario
				LEFT JOIN tb_finca ON tb_finca.id_finca = tb_parte_diario.id_finca
				LEFT JOIN tb_personal ON tb_personal.id_personal = tb_parte_diario.id_personal
				LEFT JOIN tb_cuartel ON tb_cuartel.id_cuartel = tb_parte_diario.id_cuartel
				LEFT JOIN tb_tractor ON tb_tractor.id_tractor = tb_parte_diario.id_tractor
				LEFT JOIN tb_implemento ON tb_implemento.id_implemento = tb_parte_diario.id_implemento
				LEFT JOIN tb_labor ON tb_labor.id_labor = tb_parte_diario.id_labor
				LEFT JOIN tb_empresa ON tb_empresa.id_empresa = tb_finca.id_empresa
				LEFT JOIN tb_centro_costo ON tb_centro_costo.id_centro_costo = tb_empresa.id_centro_costo
				WHERE
				tb_personal.eventual = '0' AND tb_parte_diario.fecha BETWEEN '$desde' AND '$hasta' $consulta_personal$consulta_labor
				GROUP BY
				tb_parte_diario.id_parte_diario_global
				ORDER BY
				tb_parte_diario.fecha aSC,
				finca ASC,
				labor ASC";

	$cel=3;//Numero de fila donde empezara a crear  el reporte

	$rslabores = mysqli_query($conexion, $sqlpartes);
	$cantidad =  mysqli_num_rows($rslabores);
	while ($datos = mysqli_fetch_array($rslabores)){
	$parte=$datos['parte'];
	$personal=utf8_encode($datos['personal']);
	$fecha=$datos['fecha'];
	$mes=$datos['mes'];
	$anio=$datos['anio'];
	$empresa=$datos['empresa'];
	$finca=utf8_encode($datos['finca']);
	$modulo=$datos['modulo'];
	$cuartel=$datos['cuartel'];
	$has=$datos['has'];
	$horas=$datos['horas'];
	$labor=utf8_encode($datos['labor']);
	$tractor=utf8_encode($datos['tractor']);
	$implemento=utf8_encode($datos['implemento']);
	$horas_tractor=$datos['horas_tractor'];
	$obs_labor=utf8_encode($datos['obs_labor']);
	$obs_gral=utf8_encode($datos['obs_gral']);
		
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
			$p="P".$cel;
			$q="Q".$cel;
			// Agregar datos
			$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($a, $parte)
            ->setCellValue($b, $personal)
            ->setCellValue($c, $fecha)
            ->setCellValue($d, $mes)
            ->setCellValue($e, $anio)
            ->setCellValue($f, $empresa)
            ->setCellValue($g, $finca)
            ->setCellValue($h, $modulo)
			->setCellValue($i, $cuartel)
			->setCellValue($j, $has)
			->setCellValue($k, $horas)
			->setCellValue($l, $labor)
			->setCellValue($m, $tractor)
			->setCellValue($n, $implemento)
			->setCellValue($o, $horas_tractor)
			->setCellValue($p, $obs_labor)
			->setCellValue($q, $obs_gral);

			$cel+=1;
	}
			
	
// /*Fin extracion de datos MYSQL*/
$rango="A2:$q";
$styleArray = array('font' => array( 'name' => 'Arial','size' => 10),
'borders'=>array('allBorders'=>array('borderStyle'=> Border::BORDER_THIN,'color'=>array('argb' => '00000000')))
);
$objPHPExcel->getActiveSheet()->getStyle($rango)->applyFromArray($styleArray);
// Cambiar el nombre de hoja de cálculo
$objPHPExcel->getActiveSheet()->setTitle('Reporte personal propio');


// Establecer índice de hoja activa a la primera hoja , por lo que Excel abre esto como la primera hoja
$objPHPExcel->setActiveSheetIndex(0);


// Redirigir la salida al navegador web de un cliente ( Excel5 )
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="reporte_propias-'.$fecha.'.xls"');
header('Cache-Control: max-age=0');
// Si usted está sirviendo a IE 9 , a continuación, puede ser necesaria la siguiente
header('Cache-Control: max-age=1');

// Si usted está sirviendo a IE a través de SSL , a continuación, puede ser necesaria la siguiente
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx');
$objWriter->save('php://output');
exit;