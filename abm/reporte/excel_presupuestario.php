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
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:F1');

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Reporte - Control presupuestario')
            ->setCellValue('A2', 'Labor')
			->setCellValue('B2', 'Presupuestado')
            ->setCellValue('C2', 'Ejecutado')
            ->setCellValue('D2', 'Unidad')
            ->setCellValue('E2', 'Diferencia')
            ->setCellValue('F2', '% Desviación');

			
// Fuente de la primera fila en negrita
$boldArray = array('font' => array('bold' => true,),'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER));

$objPHPExcel->getActiveSheet()->getStyle('A1:F2')->applyFromArray($boldArray);		

//Ancho de las columnas
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(55);	
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);	
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);	
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);	
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);	

/*Extraer datos de MYSQL*/
	# conectare la base de datos
include '../../conexion/conexion.php';
include '../querys/presupuesto.php';
$conexion = conectarServidor();
    
$mesInicio = $_REQUEST['dato_mesInicio'];
$mesFin = $_REQUEST['dato_mesFin'];
$campania = $_REQUEST['dato_campania'];
$version = $_REQUEST['dato_version'];
$labor = $_REQUEST['dato_labor'];

if ($presentacion = $_REQUEST['dato_presentacion'] == '1'){
    procesaControlPrespuestario($mesInicio,$mesFin,$campania);
    $tipo = 'finca';
    $modulo = '';
}

if ($presentacion = $_REQUEST['dato_presentacion'] == '2'){
    $id_modulo = $_REQUEST['dato_modulo'];
    $tipo = 'modulo';
    
    if ($id_modulo == '1'){
        $modulo = "ugarteche";
    }

    if ($id_modulo == '2'){
        $modulo = "altamira";
    }

    procesaControlPrespuestarioModulo($mesInicio,$mesFin,$campania,$id_modulo);
}

$rs = reporteControlPresupuestario($labor,False);

	$cel=3;//Numero de fila donde empezara a crear  el reporte

	$cantidad =  mysqli_num_rows($rs);
	while ($datos = mysqli_fetch_array($rs)){
            $id = $datos['id'];
            $labores = $datos['labores'];
            $total_presupuestado = $datos['total_presupuestado'];
            $total_ejecutado = $datos['total_ejecutado'];
            $unidades = $datos['unidades'];
            $diferencia = $datos['diferencia'];
            $porcentual = $datos['porcentual'];
		
			$a="A".$cel;
			$b="B".$cel;
			$c="C".$cel;
			$d="D".$cel;
			$e="E".$cel;
            $f="F".$cel;
			// Agregar datos
			$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($a, $labores)
            ->setCellValue($b, $total_presupuestado)
            ->setCellValue($c, $total_ejecutado)
            ->setCellValue($d, $unidades)
            ->setCellValue($e, $diferencia)
            ->setCellValue($f, $porcentual);

			$cel+=1;
	}
			
	
// /*Fin extracion de datos MYSQL*/
$rango="A2:$f";
$styleArray = array('font' => array( 'name' => 'Arial','size' => 10),
'borders'=>array('allBorders'=>array('borderStyle'=> Border::BORDER_THIN,'color'=>array('argb' => '00000000')))
);
$objPHPExcel->getActiveSheet()->getStyle($rango)->applyFromArray($styleArray);
// Cambiar el nombre de hoja de cálculo
$objPHPExcel->getActiveSheet()->setTitle('Control presupuestario');


// Establecer índice de hoja activa a la primera hoja , por lo que Excel abre esto como la primera hoja
$objPHPExcel->setActiveSheetIndex(0);


// Redirigir la salida al navegador web de un cliente ( Excel5 )
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="control_presupuestario_'.$tipo.'_'.$modulo.'_del_mes_'.$mesInicio.'_al_'.$mesFin.'.xls"');
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