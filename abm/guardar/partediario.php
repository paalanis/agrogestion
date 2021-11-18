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
	$array=array('success'=>'false');
	echo json_encode($array);
	exit();
}else{
	date_default_timezone_set("America/Argentina/Mendoza");
	$id_global = date("Ymdhis");	

	$personal=$_REQUEST['dato_personal'];
	$fecha=$_REQUEST['dato_fecha'];
	$finca=$_REQUEST['dato_finca'];
	$labor=$_REQUEST['dato_labor'];
	$obs_labor=utf8_decode($_REQUEST['dato_obslabor']);
	$tractor=$_REQUEST['dato_tractor'];
	$implemento=$_REQUEST['dato_implemento'];
	$hora_t=$_REQUEST['dato_calculo'];
	$horas=$_REQUEST['dato_horas'];
	
	$obs_general=utf8_decode($_REQUEST['dato_obsgeneral']);

	$totalhas=$_REQUEST['dato_totalhas'];

	$idinicial=$_REQUEST['dato_idinicial']; // id inicial de cuarteles
	$idfinal=$_REQUEST['dato_idfinal'];

	if ($totalhas == 0) {

		mysqli_select_db($conexion,'$database');
		$sql = "INSERT INTO tb_parte_diario (id_finca, id_personal, fecha, horas_trabajadas, obs_general, id_cuartel, has, obs_labor, id_labor, id_parte_diario_global, id_tractor, horas_tractor, id_implemento)
		VALUES ('$finca', '$personal', '$fecha', '$horas', '$obs_general', '0', '0', '$obs_labor', '$labor', '$id_global', '$tractor', '$hora_t', '$implemento')";
		mysqli_query($conexion,$sql);

			$sqlinsumos = "SELECT
							tb_consumo_insumos_".$deposito.".id_insumo AS id,
							tb_consumo_insumos_".$deposito.".egreso AS cantidad
							FROM
							tb_consumo_insumos_".$deposito."
							WHERE
							tb_consumo_insumos_".$deposito.".estado = '0'
							ORDER BY
							tb_consumo_insumos_".$deposito.".id_consumo_insumos ASC";
		    $rsinsumos = mysqli_query($conexion, $sqlinsumos);
		    
		    $filas =  mysqli_num_rows($rsinsumos);

		    if ($filas > 0) { // si existen insumos con de esa finca se muestran, de lo contrario queda en blanco  
		   
		    	
		        while ($datos = mysqli_fetch_array($rsinsumos)){ //
		       
			        $cantidad=utf8_encode($datos['cantidad']);
			        $id=utf8_encode($datos['id']);
    			
					mysqli_select_db($conexion,'$database');
					$sql = "INSERT INTO tb_insumo_proporcional_".$deposito." (id_insumo, id_cuartel, id_labor, proporcion, fecha, id_parte_diario_global)
					VALUES ('$id', '0', '$labor', '$cantidad', '$fecha', '$id_global')";
					mysqli_query($conexion,$sql); 	
		    					   
		    	} // fin while

		    	mysqli_select_db($conexion,'$database');
				$sqlagregaparte = "UPDATE tb_consumo_insumos_".$deposito."
						   SET tb_consumo_insumos_".$deposito.".id_parte_diario_global = '$id_global'
						   WHERE tb_consumo_insumos_".$deposito.".estado = '0'"; 
				mysqli_query($conexion, $sqlagregaparte);

		    	mysqli_select_db($conexion,'$database');
				$sqlinsumo = "UPDATE tb_consumo_insumos_".$deposito." SET tb_consumo_insumos_".$deposito.".estado = '1' WHERE tb_consumo_insumos_".$deposito.".estado = '0'"; 
				mysqli_query($conexion, $sqlinsumo);

				$array=array('success'=>'true');
			echo json_encode($array);
			
		    }else{

		    	$array=array('success'=>'true');
			echo json_encode($array);

		    }		

	}else{

			$coef_horas = $horas/$totalhas;
			$coef_horas_t = $hora_t/$totalhas;

			for ($i=$idinicial; $i <=$idfinal ; $i++) { 

				$cuartel=$_GET['dato_'.$i.''];
				$has=$_GET['dato_has-seleccionadas'.$i.''];

				$hectareas[''.$i.''] = ''.$has.'';
				$cuarteles[''.$i.''] = ''.$cuartel.'';

				if ($has == 0)
				        continue;

				    $horas_proporcional = $has * $coef_horas;
				    $horas_proporcional_t = $has * $coef_horas_t;
				
				mysqli_select_db($conexion,'$database');
				$sql = "INSERT INTO tb_parte_diario (id_finca, id_personal, fecha, horas_trabajadas, obs_general, id_cuartel, has, obs_labor, id_labor, id_parte_diario_global, id_tractor, horas_tractor, id_implemento)
				VALUES ('$finca', '$personal', '$fecha', '$horas_proporcional', '$obs_general', '$cuartel', '$has', '$obs_labor', '$labor', '$id_global', '$tractor', '$horas_proporcional_t', '$implemento')";
				mysqli_query($conexion,$sql);    

			}

			$sqlinsumos = "SELECT
							tb_consumo_insumos_".$deposito.".id_insumo AS id,
							tb_consumo_insumos_".$deposito.".egreso AS cantidad
							FROM
							tb_consumo_insumos_".$deposito."
							WHERE
							tb_consumo_insumos_".$deposito.".estado = '0'
							ORDER BY
							tb_consumo_insumos_".$deposito.".id_consumo_insumos ASC";
		    $rsinsumos = mysqli_query($conexion, $sqlinsumos);
		    
		    $filas =  mysqli_num_rows($rsinsumos);

		    if ($filas > 0) { // si existen insumos con de esa finca se muestran, de lo contrario queda en blanco  
		   
		    	$contador = 0;
		        while ($datos = mysqli_fetch_array($rsinsumos)){ //
		       
			        $cantidad=utf8_encode($datos['cantidad']);
			        $id=utf8_encode($datos['id']);
			        $contador++;

			            	for ($i=$idinicial; $i <= $idfinal; $i++) { 
							
								if ($hectareas[$i] == 0)
					        	continue;

								$proporcion = $cantidad/ $totalhas * $hectareas[$i];	
								$idcuartel = $cuarteles[$i];

								mysqli_select_db($conexion,'$database');
								$sql = "INSERT INTO tb_insumo_proporcional_".$deposito." (id_insumo, id_cuartel, id_labor, proporcion, fecha, id_parte_diario_global)
								VALUES ('$id', '$idcuartel', '$labor', '$proporcion', '$fecha', '$id_global')";
								mysqli_query($conexion,$sql); 	
					    		
					        }

				   
		    	} // fin while

		    	mysqli_select_db($conexion,'$database');
				$sqlagregaparte = "UPDATE tb_consumo_insumos_".$deposito."
						   SET tb_consumo_insumos_".$deposito.".id_parte_diario_global = '$id_global'
						   WHERE tb_consumo_insumos_".$deposito.".estado = '0'"; 
				mysqli_query($conexion, $sqlagregaparte);

		    	mysqli_select_db($conexion,'$database');
				$sqlinsumo = "UPDATE tb_consumo_insumos_".$deposito." SET tb_consumo_insumos_".$deposito.".estado = '1' WHERE tb_consumo_insumos_".$deposito.".estado = '0'"; 
				mysqli_query($conexion, $sqlinsumo);

				$array=array('success'=>'true');
			echo json_encode($array);
			
		    }else{

		    	$array=array('success'=>'true');
			echo json_encode($array);

		    } // fin else filas 

		 } // fin else has 0   

} //fin else conexion

// if ($control_txt == "ok") {
	
// 		mysqli_select_db($conexion,'$database');
// 		$sql_txt_pd = "SELECT
// 						tb_parte_diario.id_finca as id_finca,
// 						tb_parte_diario.id_personal as id_personal,
// 						tb_parte_diario.id_cuartel as id_cuartel,
// 						tb_parte_diario.id_labor as id_labor,
// 						tb_parte_diario.id_parte_diario_global as id_global,
// 						tb_parte_diario.fecha as fecha,
// 						tb_parte_diario.horas_trabajadas as horas,
// 						tb_parte_diario.obs_general as ob_gral,
// 						tb_parte_diario.obs_labor as ob_labor,
// 						tb_parte_diario.has as has, 
// 						tb_parte_diario.horas_tractor as horas_tractor,
// 						tb_parte_diario.id_tractor id_tractor,
// 						tb_parte_diario.id_implemento as id_implemento
// 						FROM
// 						tb_parte_diario
// 						WHERE
// 						tb_parte_diario.id_parte_diario_global = '$id_global'"; 
// 		$rs_sql_txt_pd = mysqli_query($conexion, $sql_txt_pd);
		
// 		$cantidad =  mysqli_num_rows($rs_sql_txt_pd);

// 		if ($cantidad > 0) {

// 		$file = fopen("../pendientes/01_datos_".$id_global."_.txt", "w");

// 		 while ($datos = mysqli_fetch_array($rs_sql_txt_pd)){ //
// 				$id_finca=$datos['id_finca'];
// 				$id_personal=$datos['id_personal'];
// 				$id_cuartel=$datos['id_cuartel'];
// 				$id_labor=$datos['id_labor'];
// 				$id_global2=$datos['id_global'];
// 				$fecha=$datos['fecha'];
// 				$horas=$datos['horas'];
// 				$ob_gral=utf8_encode($datos['ob_gral']);
// 				$ob_labor=utf8_encode($datos['ob_labor']);
// 				$has=$datos['has'];
// 				$horas_tractor=$datos['horas_tractor'];
// 				$id_tractor=$datos['id_tractor'];
// 				$id_implemento=$datos['id_implemento'];

// 			fwrite($file, $id_finca);
// 			fwrite($file, "~");	
// 			fwrite($file, $id_personal);
// 			fwrite($file, "~");
// 			fwrite($file, $id_cuartel);
// 			fwrite($file, "~");
// 			fwrite($file, $id_labor);
// 			fwrite($file, "~");
// 			fwrite($file, $id_global2);
// 			fwrite($file, "~");
// 			fwrite($file, $fecha);
// 			fwrite($file, "~");
// 			fwrite($file, $horas);
// 			fwrite($file, "~");
// 			fwrite($file, $ob_gral);
// 			fwrite($file, "~");
// 			fwrite($file, $ob_labor);
// 			fwrite($file, "~");
// 			fwrite($file, $has);
// 			fwrite($file, "~");
// 			fwrite($file, $horas_tractor);
// 			fwrite($file, "~");
// 			fwrite($file, $id_tractor);
// 			fwrite($file, "~");
// 			fwrite($file, $id_implemento.PHP_EOL);

// 		}
// 			fclose($file);
// 		 // rename ("alta_finca.txt", "../../../../public_ftp/incoming/nuevos/alta_finca_".$id_global.".txt"); 

// 		}
		
// 		mysqli_select_db($conexion,'$database');
// 		$sql_txt_con_insu = "SELECT
// 					tb_consumo_insumos_".$deposito.".id_insumo as id_insumo,
// 					tb_consumo_insumos_".$deposito.".id_parte_diario_global as id_global,
// 					tb_consumo_insumos_".$deposito.".id_deposito_origen as depo_or,
// 					tb_consumo_insumos_".$deposito.".id_deposito_destino as depo_des,
// 					tb_consumo_insumos_".$deposito.".reingreso as reingreso,
// 					tb_consumo_insumos_".$deposito.".fecha as fecha,
// 					tb_consumo_insumos_".$deposito.".ingreso as ingreso,
// 					tb_consumo_insumos_".$deposito.".egreso as egreso,
// 					tb_consumo_insumos_".$deposito.".saldo as saldo,
// 					tb_consumo_insumos_".$deposito.".estado as estado,
// 					tb_consumo_insumos_".$deposito.".obs as obs
// 					FROM
// 					tb_consumo_insumos_".$deposito."
// 					WHERE
// 					tb_consumo_insumos_".$deposito.".id_parte_diario_global = '$id_global'"; 
// 		$rs_sql_txt_con_insu = mysqli_query($conexion, $sql_txt_con_insu);
		
// 		$cantidad =  mysqli_num_rows($rs_sql_txt_con_insu);

// 		if ($cantidad > 0) {

// 		$file = fopen("../pendientes/02_datos_".$id_global."_.txt", "w");

// 		 while ($datos = mysqli_fetch_array($rs_sql_txt_con_insu)){ //
// 				$deposito=$deposito;
// 				$id_insumo=$datos['id_insumo'];
// 				$id_global2=$datos['id_global'];
// 				$depo_or=$datos['depo_or'];
// 				$depo_des=$datos['depo_des'];
// 				$reingreso=$datos['reingreso'];
// 				$fecha=$datos['fecha'];
// 				$ingreso=$datos['ingreso'];
// 				$egreso=$datos['egreso'];
// 				$saldo=$datos['saldo'];
// 				$estado=$datos['estado'];
// 				$obs=$datos['obs'];
				
// 			fwrite($file, $deposito);
// 			fwrite($file, "~");	
// 			fwrite($file, $id_insumo);
// 			fwrite($file, "~");
// 			fwrite($file, $id_global2);
// 			fwrite($file, "~");
// 			fwrite($file, $depo_or);
// 			fwrite($file, "~");
// 			fwrite($file, $depo_des);
// 			fwrite($file, "~");
// 			fwrite($file, $reingreso);
// 			fwrite($file, "~");
// 			fwrite($file, $fecha);
// 			fwrite($file, "~");
// 			fwrite($file, $ingreso);
// 			fwrite($file, "~");
// 			fwrite($file, $egreso);
// 			fwrite($file, "~");
// 			fwrite($file, $saldo);
// 			fwrite($file, "~");
// 			fwrite($file, $estado);
// 			fwrite($file, "~");
// 			fwrite($file, $obs.PHP_EOL);
			
// 		}
// 			fclose($file);
// 		 // rename ("alta_finca.txt", "../../../../public_ftp/incoming/nuevos/alta_finca_".$id_global.".txt"); 

// 		}

// 		mysqli_select_db($conexion,'$database');
// 		$sql_txt_con_insu_prop = "SELECT
// 					tb_insumo_proporcional_".$deposito.".id_insumo as id_insumo,
// 					tb_insumo_proporcional_".$deposito.".id_cuartel as id_cuartel,
// 					tb_insumo_proporcional_".$deposito.".id_labor as id_labor,
// 					tb_insumo_proporcional_".$deposito.".fecha as fecha,
// 					tb_insumo_proporcional_".$deposito.".proporcion as proporcion,
// 					tb_insumo_proporcional_".$deposito.".id_parte_diario_global as id_global
// 					FROM
// 					tb_insumo_proporcional_".$deposito."
// 					WHERE
// 					tb_insumo_proporcional_".$deposito.".id_parte_diario_global = '$id_global'
// 					"; 
// 		$rs_sql_txt_con_insu_prop = mysqli_query($conexion, $sql_txt_con_insu_prop);
		
// 		$cantidad =  mysqli_num_rows($rs_sql_txt_con_insu_prop);

// 		if ($cantidad > 0) {

// 		$file = fopen("../pendientes/03_datos_".$id_global."_.txt", "w");

// 		 while ($datos = mysqli_fetch_array($rs_sql_txt_con_insu_prop)){ //
// 				$deposito=$deposito;
// 				$id_insumo=$datos['id_insumo'];
// 				$id_cuartel=$datos['id_cuartel'];
// 				$id_labor=$datos['id_labor'];
// 				$fecha=$datos['fecha'];
// 				$proporcion=$datos['proporcion'];
// 				$id_global2=$datos['id_global'];
				
// 			fwrite($file, $deposito);
// 			fwrite($file, "~");	
// 			fwrite($file, $id_insumo);
// 			fwrite($file, "~");
// 			fwrite($file, $id_cuartel);
// 			fwrite($file, "~");
// 			fwrite($file, $id_labor);
// 			fwrite($file, "~");
// 			fwrite($file, $fecha);
// 			fwrite($file, "~");
// 			fwrite($file, $proporcion);
// 			fwrite($file, "~");
// 			fwrite($file, $id_global2.PHP_EOL);
			
// 		}
// 			fclose($file);
// 		 // rename ("alta_finca.txt", "../../../../public_ftp/incoming/nuevos/alta_finca_".$id_global.".txt"); 

// 		}

// 			$array=array('success'=>'true');
// 			echo json_encode($array);

// }

?>