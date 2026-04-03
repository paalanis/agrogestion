<?php

$id_finca = $_SESSION['id_finca_usuario'];
$inicioCampania = '2024-06-01';
$idCampania = 1;

date_default_timezone_set("America/Argentina/Mendoza");
setlocale(LC_ALL, "es_ES");
$hoy = date("Y-m-d");
$mes = date("m");


function queryJornalesMes()
{
    global $conexion,$id_finca,$mes,$idCampania;

    try {
        $query = "SELECT
					ROUND(Sum(tb_parte_diario.horas_trabajadas)/8) AS jorMes
				FROM
					tb_parte_diario
				INNER JOIN
					tb_labor
				ON 
					tb_parte_diario.id_labor = tb_labor.id_labor
				INNER JOIN
					tb_unidad
				ON 
					tb_labor.id_unidad = tb_unidad.id_unidad
				WHERE
					tb_parte_diario.id_finca = $id_finca AND
					MONTH(tb_parte_diario.fecha) = $mes AND
					tb_unidad.id_unidad = 19 AND
  					tb_parte_diario.id_campania = $idCampania";
        $rs = mysqli_query($conexion, $query);

        while ($datos = mysqli_fetch_array($rs)) {
            $dato = utf8_encode($datos['jorMes']);
        }
        return $dato;

    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

function queryJornalesAcumulado()
{
    global $conexion,$id_finca,$hoy,$inicioCampania;

    try {
        $query = "SELECT
						ROUND(Sum(tb_parte_diario.horas_trabajadas)/8) AS jorAcumulado
					FROM
						tb_parte_diario
					INNER JOIN
						tb_labor
					ON 
						tb_parte_diario.id_labor = tb_labor.id_labor
					INNER JOIN
						tb_unidad
					ON 
						tb_labor.id_unidad = tb_unidad.id_unidad
					WHERE
						tb_parte_diario.id_finca = $id_finca AND
						tb_parte_diario.fecha BETWEEN '$inicioCampania' AND '$hoy' AND
						tb_unidad.id_unidad = 19";
        $rs = mysqli_query($conexion, $query);

        while ($datos = mysqli_fetch_array($rs)) {
            $dato = utf8_encode($datos['jorAcumulado']);
        }
        return $dato;
        
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

function queryJornalesPresupuestado()
{
    global $conexion,$id_finca,$idCampania;

    try {
        $query = "SELECT
					ROUND(Sum(tb_presupuesto_original.aplicacion),0) as jorPresupuestado
				FROM
					tb_presupuesto_original
				INNER JOIN
					tb_labor
				ON 
					tb_presupuesto_original.id_labor = tb_labor.id_labor
				INNER JOIN
					tb_unidad
				ON 
					tb_labor.id_unidad = tb_unidad.id_unidad
				WHERE
					tb_unidad.id_unidad = 19 AND
					tb_presupuesto_original.id_campania = $idCampania AND
					tb_presupuesto_original.id_finca = $id_finca";
        $rs = mysqli_query($conexion, $query);

        while ($datos = mysqli_fetch_array($rs)) {
            $dato = utf8_encode($datos['jorPresupuestado']);
        }
        return $dato;
        
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

function queryHorasTractorMes()
{
    global $conexion,$id_finca,$mes,$idCampania;

    try {
        $query = "SELECT
						ROUND(Sum(tb_parte_diario.horas_tractor)) AS HorasTractorMes
					FROM
						tb_parte_diario
					WHERE
						tb_parte_diario.id_finca = $id_finca AND
						MONTH(tb_parte_diario.fecha) = $mes AND
						tb_parte_diario.id_campania = $idCampania";
        $rs = mysqli_query($conexion, $query);

        while ($datos = mysqli_fetch_array($rs)) {
            $dato = utf8_encode($datos['HorasTractorMes']);
        }
        return $dato;
        
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

function queryHorasTractorAcumulada()
{
    global $conexion,$id_finca,$hoy,$inicioCampania;

    try {
        $query = "SELECT
						ROUND(Sum(tb_parte_diario.horas_tractor)) AS HorasTractorAcumulada
					FROM
						tb_parte_diario
					WHERE
						tb_parte_diario.id_finca = $id_finca AND
						tb_parte_diario.fecha BETWEEN '$inicioCampania' AND '$hoy'";
        $rs = mysqli_query($conexion, $query);

        while ($datos = mysqli_fetch_array($rs)) {
            $dato = utf8_encode($datos['HorasTractorAcumulada']);
        }
        return $dato;
        
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

function queryTopJornales()
{
    global $conexion,$id_finca,$hoy,$inicioCampania;

    try {
        $query = "SELECT
                    tb_labor.nombre AS topLabores, 
                    ROUND(Sum(tb_parte_diario.horas_trabajadas)/8) AS topJornales
                FROM
                    tb_parte_diario
                INNER JOIN
                    tb_labor
                ON 
                    tb_labor.id_labor = tb_parte_diario.id_labor
                INNER JOIN
                    tb_unidad
                ON 
                    tb_labor.id_unidad = tb_unidad.id_unidad
                WHERE
                    tb_parte_diario.id_finca = $id_finca AND
                    tb_parte_diario.fecha BETWEEN '$inicioCampania' AND '$hoy' AND
                    tb_unidad.id_unidad = 19
                GROUP BY
                    tb_labor.id_labor
                ORDER BY
                    topJornales DESC
                LIMIT 5";
        $rs = mysqli_query($conexion, $query);

        $listaLab = array();
        $listaJor = array();
        $topJornales = array();

        while ($datos = mysqli_fetch_array($rs)) {
            $nombre = utf8_encode($datos['topLabores']);
            $numero = utf8_encode($datos['topJornales']);

            array_push($listaLab, $nombre);
	        array_push($listaJor, $numero);
            
        }

        array_push($topJornales, $listaLab);
        array_push($topJornales, $listaJor);

        return $topJornales;
        
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

function queryHorasPersonalMes()
{
    global $conexion,$mes,$idCampania;

    $horasIdeal = calcularHorasTrabajadasIdeal();

    try {
        $query = "SELECT
                    CONCAT(tb_personal.apellido,' ',tb_personal.nombre) AS personal, 
                    ROUND(SUM(tb_parte_diario.horas_trabajadas),0) AS horas
                FROM
                    tb_parte_diario
                    LEFT JOIN
                    tb_personal
                    ON 
                    tb_parte_diario.id_personal = tb_personal.id_personal
                WHERE
                    MONTH(tb_parte_diario.fecha) = $mes AND
                    tb_parte_diario.id_campania = $idCampania AND
                    tb_personal.eventual = 1
                GROUP BY
                    tb_personal.unificador
                ORDER BY
                    personal ASC";
        $rs = mysqli_query($conexion, $query);

        $listaPersonal = array();
        $listaHoras = array();
        $listaHorasIdeal = array();
        $horasTrabajas = array();

        while ($datos = mysqli_fetch_array($rs)) {
            $nombre = utf8_encode($datos['personal']);
            $numero = utf8_encode($datos['horas']);

            array_push($listaPersonal, $nombre);
	        array_push($listaHoras, $numero);
            array_push($listaHorasIdeal, $horasIdeal);
            
        }

        array_push($horasTrabajas, $listaPersonal);
        array_push($horasTrabajas, $listaHoras);
        array_push($horasTrabajas, $listaHorasIdeal);

        return $horasTrabajas;
        
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

function queryEjecutadoActual()
{
    global $conexion;

    try {
        $query = "SELECT
                    tb_ejecutado.id_ejecutado AS id, 
                    tb_ejecutado.id_labor AS id_labor, 
                    tb_ejecutado.mes AS mes, 
                    tb_ejecutado.anio AS anio, 
                    tb_ejecutado.labor AS labores, 
                    tb_ejecutado.valoresEjecutado AS total_ejecutado, 
                    tb_ejecutado.unidadMedida AS unidades
                FROM
                    tb_ejecutado
                ORDER BY
                    tb_ejecutado.labor ASC";
        $rs = mysqli_query($conexion, $query);

        $listaLab = array();
        $listaJor = array();
        $ejecutado = array();

        while ($datos = mysqli_fetch_array($rs)) {
            $nombre = utf8_encode($datos['labores']);
            $numero = utf8_encode($datos['total_ejecutado']);

            array_push($listaLab, $nombre);
	        array_push($listaJor, $numero);
            
        }

        array_push($ejecutado, $listaLab);
        array_push($ejecutado, $listaJor);

        return $ejecutado;
        
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

function queryPresupuestadoActual()
{
    global $conexion;

    try {
        $query = "SELECT
                    tb_presupuestado.id_presupuestado AS id, 
                    tb_presupuestado.id_labor AS id_labor, 
                    tb_presupuestado.mes AS mes, 
                    tb_presupuestado.anio AS anio, 
                    tb_presupuestado.labor AS labores, 
                    tb_presupuestado.valoresPresupuestado AS total_presupuestado, 
                    tb_presupuestado.unidadMedida AS unidades
                FROM
                    tb_presupuestado
                ORDER BY
                    tb_presupuestado.labores ASC";
        $rs = mysqli_query($conexion, $query);

        $listaLab = array();
        $listaJor = array();
        $presupuestado = array();

        while ($datos = mysqli_fetch_array($rs)) {
            $nombre = utf8_encode($datos['labores']);
            $numero = utf8_encode($datos['total_presupuestado']);

            array_push($listaLab, $nombre);
	        array_push($listaJor, $numero);
            
        }

        array_push($presupuestado, $listaLab);
        array_push($presupuestado, $listaJor);

        return $presupuestado;
        
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

function queryControlPresupuestadoActual()
{
    global $conexion;

    try {
        $query = "SELECT
                    tb_control_presupuestario.id_control_presupuestario AS id,
                    tb_control_presupuestario.id_labor AS id_labor,
                    tb_control_presupuestario.labores AS labores, 
                    IFNULL(tb_control_presupuestario.total_presupuestado,0) AS total_presupuestado, 
                    IFNULL(tb_control_presupuestario.total_ejecutado,0) AS total_ejecutado, 
                    tb_control_presupuestario.unidades AS unidades
                FROM
                    tb_control_presupuestario
                ORDER BY
                    labores ASC";
        $rs = mysqli_query($conexion, $query);

        $listaLab = array();
        $listaPre = array();
        $listaEje = array();
        $listaPresupuestado = array();

        while ($datos = mysqli_fetch_array($rs)) {
            $nombre = utf8_encode($datos['labores']);
            $presupuestado = utf8_encode($datos['total_presupuestado']);
            $ejecutado = utf8_encode($datos['total_ejecutado']);

            array_push($listaLab, $nombre);
	        array_push($listaPre, $presupuestado);
            array_push($listaEje, $ejecutado);
            
        }

        array_push($listaPresupuestado, $listaLab);
        array_push($listaPresupuestado, $listaPre);
        array_push($listaPresupuestado, $listaEje);

        return $listaPresupuestado;
        
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

function promedio($valorA,$valorB)
{
	$promedio = Round($valorB / $valorA * 100, 0);
	return $promedio;
}

function eliminarTabla($tabla)
{
    global $conexion;
    try{
        $queryEliminar = "DROP TABLE IF	EXISTS $tabla";
        mysqli_query($conexion, $queryEliminar);
    }
    catch(Exception $e){
        echo 'Message: ' .$e->getMessage();
    }
}

function calculaEjecutado()
{
    global $conexion,$id_finca,$mes,$idCampania;

        eliminarTabla("tb_ejecutado");
        try{
            $query = "CREATE TABLE tb_ejecutado(id_ejecutado INT UNSIGNED AUTO_INCREMENT PRIMARY KEY) AS
                        SELECT
                            tb_parte_diario.id_labor AS id_labor,
                            MONTH(tb_parte_diario.fecha) AS mes, 
                            YEAR(tb_parte_diario.fecha) AS anio,
                            tb_labor.nombre AS labor, 
                            IF(tb_labor.id_unidad = 19,ROUND(SUM(tb_parte_diario.horas_trabajadas)/8,3),ROUND(SUM(tb_parte_diario.horas_trabajadas),2)) AS valoresEjecutado, 
                            tb_unidad.nombre AS unidadMedida
                        FROM
                            tb_parte_diario
                            INNER JOIN
                            tb_labor
                            ON 
                                tb_parte_diario.id_labor = tb_labor.id_labor
                            INNER JOIN
                            tb_unidad
                            ON 
                                tb_labor.id_unidad = tb_unidad.id_unidad
                        WHERE
                            tb_parte_diario.id_finca = $id_finca AND
                            MONTH(tb_parte_diario.fecha) BETWEEN $mes AND $mes AND
                            tb_parte_diario.id_campania = $idCampania
                        GROUP BY
                            tb_parte_diario.id_labor,
                            MONTH(tb_parte_diario.fecha)
                        ORDER BY
                            MONTH(tb_parte_diario.fecha) ASC,
                            id_labor ASC";
            mysqli_query($conexion, $query);

        }
        catch(Exception $e){
            echo 'Message: ' .$e->getMessage();
        }        
}

function calculaPrespuestado()
{
    global $conexion,$id_finca,$mes,$idCampania;
    
        eliminarTabla("tb_presupuestado");  
        try{
            $sqlEjecutado = "CREATE TABLE tb_presupuestado(id_presupuestado INT UNSIGNED AUTO_INCREMENT PRIMARY KEY) AS
                                SELECT
                                    tb_presupuesto_original.id_labor AS id_labor,
                                    tb_presupuesto_original.mes AS mes, 
                                    tb_presupuesto_original.anio AS anio,
                                    tb_labor.nombre AS labor, 
                                    ROUND(SUM(tb_presupuesto_original.aplicacion),2) AS valoresPresupuestado,
                                    tb_unidad.nombre AS unidadMedida
                                FROM
                                    tb_presupuesto_original
                                    INNER JOIN
                                    tb_labor
                                    ON 
                                        tb_presupuesto_original.id_labor = tb_labor.id_labor
                                    INNER JOIN
                                    tb_unidad
                                    ON 
                                        tb_labor.id_unidad = tb_unidad.id_unidad
                                WHERE
                                    tb_presupuesto_original.id_finca = $id_finca AND
                                    tb_presupuesto_original.mes BETWEEN $mes AND $mes AND
                                    tb_presupuesto_original.id_campania = $idCampania
                                GROUP BY
                                    tb_presupuesto_original.id_labor,
                                    tb_presupuesto_original.mes
                                ORDER BY
                                    tb_presupuesto_original.mes ASC,
                                    id_labor ASC";
            mysqli_query($conexion, $sqlEjecutado);

        }
        catch(Exception $e){
            echo 'Message: ' .$e->getMessage();
        }        
}

function calculaControlPrespuestario()
{
    global $conexion;
    
        eliminarTabla("tb_control_presupuestario");  
        try{
            $query =    "CREATE TABLE tb_control_presupuestario(
                        id_control_presupuestario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY) AS
                        SELECT
                            id_labor,
                            mes, 
                            anio, 
                            labor as labores, 
                            SUM(valoresPresupuestado) AS total_presupuestado, 
                            SUM(valoresEjecutado) AS total_ejecutado,
                            unidadMedida AS unidades
                        FROM (
                            SELECT id_labor, mes, anio, labor, valoresPresupuestado, NULL AS valoresEjecutado, unidadMedida
                            FROM tb_presupuestado
                            UNION ALL
                            SELECT id_labor, mes, anio, labor, NULL, valoresEjecutado, unidadMedida
                            FROM tb_ejecutado
                        ) AS combined_data
                        GROUP BY id_labor, mes, anio, labor";
            mysqli_query($conexion, $query);

        }
        catch(Exception $e){
            echo 'Message: ' .$e->getMessage();
        }        
}

function procesaControlPrespuestario()
{    
        calculaEjecutado();
        calculaPrespuestado();
        calculaControlPrespuestario();
}

function calcularHorasTrabajadasIdeal() 
{
    // Obtener la fecha actual
    $fechaActual = new DateTime();
    $anioActual = $fechaActual->format('Y');
    $mesActual = $fechaActual->format('m');

    // Obtener el último día del mes
    $ultimoDiaMes = new DateTime($anioActual . '-' . $mesActual . '-01');
    $ultimoDiaMes->modify('last day of this month');

    // Inicializar el contador de horas
    $horasTrabajadas = 0;

    // Iterar sobre cada día del mes
    for ($dia = 1; $dia <= $ultimoDiaMes->format('d'); $dia++) {
        $fecha = new DateTime($anioActual . '-' . $mesActual . '-' . $dia);
        $diaSemana = $fecha->format('N'); // N para obtener el número del día de la semana (1 = lunes, 7 = domingo)

        // Sumar las horas según el día de la semana
        if ($diaSemana >= 1 && $diaSemana <= 5) { // Lunes a viernes
            $horasTrabajadas += 8;
        } elseif ($diaSemana == 6) { // Sábado
            $horasTrabajadas += 4;
        }
    }

    return $horasTrabajadas;
}

function calcularHorasTrabajadasActual() {
    // Obtenemos el primer día del mes actual
    $primerDiaMes = new DateTime('first day of this month');
    // Obtenemos la fecha de hoy
    $hoy = new DateTime();

    // Inicializamos el contador de horas
    $horasTrabajadas = 0;

    // Iteramos desde el primer día del mes hasta hoy
    $intervalo = new DateInterval('P1D');
    $periodo = new DatePeriod($primerDiaMes, $intervalo, $hoy);
    foreach ($periodo as $fecha) {
        // Obtenemos el día de la semana (0 = domingo, 1 = lunes, ...)
        $diaSemana = $fecha->format('N');

        // Sumamos las horas según el día de la semana
        if ($diaSemana >= 1 && $diaSemana <= 5) {
            // Lunes a viernes: 8 horas
            $horasTrabajadas += 8;
        } elseif ($diaSemana == 6) {
            // Sábado: 4 horas
            $horasTrabajadas += 4;
        }
    }

    return $horasTrabajadas + 8;
}

?>