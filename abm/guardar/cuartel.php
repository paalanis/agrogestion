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
   
        $array=array('success'=>'false');
        echo json_encode($array);
         
        exit();

}else{

        $id=utf8_decode($_REQUEST['dato_id']);
        $nombre=utf8_decode($_REQUEST['dato_nombre']);
        $finca=$_REQUEST['dato_finca'];
        $variedad=$_REQUEST['dato_variedad'];
        $riego=$_REQUEST['dato_riego'];
        $conduccion=$_REQUEST['dato_conduccion'];
        $año=$_REQUEST['dato_anio'];
        $distancia=$_REQUEST['dato_distancia'];
        $has=$_REQUEST['dato_has'];
        $mapeo=$_REQUEST['dato_mapeo'];
        $hileras=$_REQUEST['dato_hileras'];
        $malla=$_REQUEST['dato_malla'];
   
        
        if ($id == '0') {

        mysqli_select_db($conexion,$database);
        $sql = "INSERT INTO tb_cuartel (id_finca, nombre, id_variedad, id_riego, id_conduccion, ano, distancia, has, id_super, malla, hileras)
        VALUES ('$finca', lower('$nombre'), '$variedad', '$riego', '$conduccion', '$año', '$distancia', '$has', '$mapeo', '$malla', '$hileras')";
        mysqli_query($conexion,$sql);    


        $array=array('success'=>'true');

        echo json_encode($array);
        }else{

        mysqli_select_db($conexion,$database);
        $sql = "UPDATE tb_cuartel 
        SET id_finca = '$finca',
            nombre = lower('$nombre'),
            id_variedad = '$variedad',
            id_riego = '$riego',
            id_conduccion = '$conduccion',
            ano = '$año',
            distancia = '$distancia',
            has = '$has',
            id_super = '$mapeo',
            malla = '$malla',
            hileras = '$hileras'
        WHERE tb_cuartel.id_cuartel = '$id'";
        mysqli_query($conexion,$sql);    

        $array=array('success'=>'true');
        echo json_encode($array);

        }       

} //fin else
?>