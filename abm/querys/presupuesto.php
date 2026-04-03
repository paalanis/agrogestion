<?php
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../../index.php");
}
if ($_SESSION['id_finca_usuario'] == '0') {
    session_destroy();
    header("Location: ../../../index.php");
}

$id_finca = $_SESSION['id_finca_usuario'];

function eliminarTabla($tabla)
{
    global $conexion;
    try {
        $queryEliminar = "DROP TABLE IF	EXISTS $tabla";
        mysqli_query($conexion, $queryEliminar);
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

function calculaEjecutado($mesInicio, $mesFin, $campania, $agrupado = False)
{
    global $conexion, $id_finca;

    $groupBy = ',mes';
    if ($agrupado) {
        $groupBy = '';
    }

    eliminarTabla("tb_ejecutado");
    try {
        $query = "CREATE TABLE tb_ejecutado(id_ejecutado INT UNSIGNED AUTO_INCREMENT PRIMARY KEY) AS
                        SELECT
                            tb_parte_diario.id_labor AS id_labor,
                            MONTH(tb_parte_diario.fecha) AS mes, 
                            YEAR(tb_parte_diario.fecha) AS anio,
                            tb_labor.nombre AS labor, 
                            IF(tb_labor.id_unidad = 19,ROUND(SUM(tb_parte_diario.horas_trabajadas)/8,3),ROUND(SUM(tb_parte_diario.horas_trabajadas),2)) AS valoresEjecutado, 
                            tb_unidad.nombre AS unidadMedida,
                            tb_finca.nombre AS finca
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
                            INNER JOIN
                            tb_finca
                            ON 
                            tb_parte_diario.id_finca = tb_finca.id_finca    
                        WHERE
                            tb_parte_diario.id_finca = $id_finca AND
                            MONTH(tb_parte_diario.fecha) BETWEEN $mesInicio AND $mesFin AND
                            tb_parte_diario.id_campania = $campania
                        GROUP BY
                            tb_parte_diario.id_labor
                            $groupBy
                        ORDER BY
                            MONTH(tb_parte_diario.fecha) ASC,
                            labor ASC";
        mysqli_query($conexion, $query);
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

function calculaEjecutadoModulo($mesInicio, $mesFin, $campania, $id_modulo)
{
    global $conexion;

    eliminarTabla("tb_ejecutado");
    try {
        $query = "CREATE TABLE tb_ejecutado(id_ejecutado INT UNSIGNED AUTO_INCREMENT PRIMARY KEY) AS
                        SELECT
                            tb_parte_diario.id_labor AS id_labor, 
                            MONTH(tb_parte_diario.fecha) AS mes, 
                            YEAR(tb_parte_diario.fecha) AS anio, 
                            tb_labor.nombre AS labor, 
                            IF(tb_labor.id_unidad = 19,ROUND(SUM(tb_parte_diario.horas_trabajadas)/8,3),ROUND(SUM(tb_parte_diario.horas_trabajadas),2)) AS valoresEjecutado, 
                            tb_unidad.nombre AS unidadMedida, 
                            tb_centro_costo.centro_costo
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
                            INNER JOIN
                            tb_finca
                            ON 
                                tb_parte_diario.id_finca = tb_finca.id_finca
                            INNER JOIN
                            tb_centro_costo
                            ON 
                                tb_finca.id_centro_costo = tb_centro_costo.id_centro_costo
                        WHERE
                            tb_centro_costo.id_centro_costo = $id_modulo AND
                            MONTH(tb_parte_diario.fecha) BETWEEN $mesInicio AND $mesFin AND
                            tb_parte_diario.id_campania = $campania
                        GROUP BY
                            tb_parte_diario.id_labor,
                            MONTH(tb_parte_diario.fecha)
                        ORDER BY
                            MONTH(tb_parte_diario.fecha) ASC,
                            labor ASC";
        mysqli_query($conexion, $query);
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

function calculaPrespuestado($mesInicio, $mesFin, $campania, $agrupado = False)
{
    global $conexion, $id_finca;

    $groupBy = ',mes';
    if ($agrupado) {
        $groupBy = '';
    }

    eliminarTabla("tb_presupuestado");
    try {
        $sqlEjecutado = "CREATE TABLE tb_presupuestado(id_presupuestado INT UNSIGNED AUTO_INCREMENT PRIMARY KEY) AS
                                SELECT
                                    tb_presupuesto_original.id_labor AS id_labor,
                                    tb_presupuesto_original.mes AS mes, 
                                    tb_presupuesto_original.anio AS anio,
                                    tb_labor.nombre AS labor, 
                                    ROUND(SUM(tb_presupuesto_original.aplicacion),2) AS valoresPresupuestado,
                                    tb_unidad.nombre AS unidadMedida,
                                    tb_finca.nombre AS finca 
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
                                    INNER JOIN
                                    tb_finca
                                    ON 
                                        tb_presupuesto_original.id_finca = tb_finca.id_finca
                                WHERE
                                    tb_presupuesto_original.id_finca = $id_finca AND
                                    tb_presupuesto_original.mes BETWEEN $mesInicio AND $mesFin AND
                                    tb_presupuesto_original.id_campania = $campania
                                GROUP BY
                                    tb_presupuesto_original.id_labor
                                    $groupBy
                                ORDER BY
                                    tb_presupuesto_original.mes ASC,
                                    labor ASC";
        mysqli_query($conexion, $sqlEjecutado);
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

function calculaPrespuestadoModulo($mesInicio, $mesFin, $campania, $id_modulo)
{
    global $conexion;

    eliminarTabla("tb_presupuestado");
    try {
        $sqlEjecutado = "CREATE TABLE tb_presupuestado(id_presupuestado INT UNSIGNED AUTO_INCREMENT PRIMARY KEY) AS
                                SELECT
                                    tb_presupuesto_original.id_labor AS id_labor, 
                                    tb_presupuesto_original.mes AS mes, 
                                    tb_presupuesto_original.anio AS anio, 
                                    tb_labor.nombre AS labor, 
                                    ROUND(SUM(tb_presupuesto_original.aplicacion),2) AS valoresPresupuestado, 
                                    tb_unidad.nombre AS unidadMedida, 
                                    tb_centro_costo.centro_costo
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
                                    INNER JOIN
                                    tb_finca
                                    ON 
                                        tb_presupuesto_original.id_finca = tb_finca.id_finca
                                    INNER JOIN
                                    tb_centro_costo
                                    ON 
                                        tb_finca.id_centro_costo = tb_centro_costo.id_centro_costo
                                WHERE
                                    tb_centro_costo.id_centro_costo = $id_modulo AND
                                    tb_presupuesto_original.mes BETWEEN $mesInicio AND $mesFin AND
                                    tb_presupuesto_original.id_campania = $campania
                                GROUP BY
                                    tb_presupuesto_original.id_labor,
                                    tb_presupuesto_original.mes
                                ORDER BY
                                    tb_presupuesto_original.mes ASC,
                                    labor ASC";
        mysqli_query($conexion, $sqlEjecutado);
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

function calculaControlPrespuestario()
{
    global $conexion;

    eliminarTabla("tb_control_presupuestario");
    try {
        $query =    "CREATE TABLE tb_control_presupuestario(
                        id_control_presupuestario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY) AS
                        SELECT
                        id_labor,
                        mes,
                        anio,
                        labor AS labores,
                        ROUND(SUM( valoresPresupuestado ),2) AS total_presupuestado,
                        ROUND(SUM( valoresEjecutado ),2) AS total_ejecutado,
                        ROUND( SUM( valoresPresupuestado ) - SUM( valoresEjecutado ), 2 ) AS diferencia,
                        ROUND(
                            IF
                                (
                                    SUM( valoresPresupuestado )> SUM( valoresEjecutado ),((
                                            SUM( valoresPresupuestado )- SUM( valoresEjecutado ))/ SUM( valoresPresupuestado ))* 100,((
                                        SUM( valoresEjecutado )- SUM( valoresPresupuestado ))/ SUM( valoresEjecutado ))* 100 
                                ),
                                2 
                            ) AS incrementoPorcentual,
                            unidadMedida AS unidades 
                        FROM
                            (
                            SELECT
                                id_labor,
                                mes,
                                anio,
                                labor,
                                valoresPresupuestado,
                                0 AS valoresEjecutado,
                                unidadMedida 
                            FROM
                                tb_presupuestado UNION ALL
                            SELECT
                                id_labor,
                                mes,
                                anio,
                                labor,
                                0,
                                valoresEjecutado,
                                unidadMedida 
                            FROM
                                tb_ejecutado 
                            ) AS combined_data 
                        GROUP BY
                            id_labor";
        mysqli_query($conexion, $query);
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

function procesaControlPrespuestario($mesInicio, $mesFin, $campania)
{

    calculaEjecutado($mesInicio, $mesFin, $campania);
    calculaPrespuestado($mesInicio, $mesFin, $campania);
    calculaControlPrespuestario();
}

function procesaControlPrespuestarioModulo($mesInicio, $mesFin, $campania, $id_modulo)
{

    calculaEjecutadoModulo($mesInicio, $mesFin, $campania, $id_modulo);
    calculaPrespuestadoModulo($mesInicio, $mesFin, $campania, $id_modulo);
    calculaControlPrespuestario();
}

function reporteControlPresupuestario($datoWhere = '0', $reporte = True)
{
    global $conexion;

    try {
        switch ($datoWhere) {
            case '0':
                $where = '';
                break;

            default:
                $where = 'WHERE tb_control_presupuestario.id_labor = ' . $datoWhere;
                break;
        }

        $query = "SELECT
                        tb_control_presupuestario.id_control_presupuestario AS id, 
                        tb_control_presupuestario.labores AS labores, 
                        tb_control_presupuestario.total_presupuestado AS total_presupuestado, 
                        tb_control_presupuestario.total_ejecutado AS total_ejecutado, 
                        tb_control_presupuestario.unidades AS unidades, 
                        tb_control_presupuestario.diferencia AS diferencia, 
                        tb_control_presupuestario.incrementoPorcentual AS porcentual
                    FROM
                        tb_control_presupuestario
                    $where
                    ORDER BY
                        total_presupuestado DESC, 
                        labores ASC";
        $rs = mysqli_query($conexion, $query);

        if ($reporte) {
            $cantidad =  mysqli_num_rows($rs);
            while ($datos = mysqli_fetch_array($rs)) {
                $id = $datos['id'];
                $labores = $datos['labores'];
                $total_presupuestado = $datos['total_presupuestado'];
                $total_ejecutado = $datos['total_ejecutado'];
                $unidades = $datos['unidades'];
                $diferencia = $datos['diferencia'];
                $porcentual = $datos['porcentual'];

                echo '<tr class="even pointer">
                <td id="' . $id . '">' . $labores . '</td>
                <td id="' . $id . '">' . $total_presupuestado . '</td>
                <td id="' . $id . '">' . $total_ejecutado . '</td>
                <td id="' . $id . '">' . $unidades . '</td>
                <td id="' . $id . '">' . $diferencia . '</td>
                <td id="' . $id . '">' . $porcentual . '</td>
                </tr>';
            }
            return $cantidad;
        } else {
            return $rs;
        }
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

function reporteEjecutado($datoWhere = '0', $agrupado = False, $reporte = True)
{
    global $conexion;

    try {
        switch ($datoWhere) {
            case '0':
                $where = '';
                break;

            default:
                $where = 'WHERE tb_ejecutado.id_labor = ' . $datoWhere;
                break;
        }

        $query = "SELECT
                    tb_ejecutado.id_ejecutado AS id, 
                    tb_ejecutado.id_labor AS id_labor, 
                    tb_ejecutado.mes AS mes, 
                    tb_ejecutado.anio AS anio, 
                    tb_ejecutado.labor AS labores, 
                    tb_ejecutado.valoresEjecutado AS total_ejecutado, 
                    tb_ejecutado.unidadMedida AS unidades,
                    tb_ejecutado.finca AS finca
                FROM
                    tb_ejecutado
                    $where
                ORDER BY
                    tb_ejecutado.mes ASC";
        $rs = mysqli_query($conexion, $query);

        if ($reporte) {
            $cantidad =  mysqli_num_rows($rs);
            while ($datos = mysqli_fetch_array($rs)) {
                $id = $datos['id'];
                $id_labor = $datos['id_labor'];
                $mes = $datos['mes'];
                $anio = $datos['anio'];
                $labores = $datos['labores'];
                $total_ejecutado = $datos['total_ejecutado'];
                $unidades = $datos['unidades'];

                if ($agrupado) {
                    echo '<tr class="even pointer">
                    <td id="' . $id . '">' . $anio . '</td>
                    <td id="' . $id . '">' . $labores . '</td>
                    <td id="' . $id . '">' . $total_ejecutado . '</td>
                    <td id="' . $id . '">' . $unidades . '</td>
                    </tr>';
                } else {
                    echo '<tr class="even pointer">
                    <td id="' . $id . '">' . $mes . '</td>
                    <td id="' . $id . '">' . $anio . '</td>
                    <td id="' . $id . '">' . $labores . '</td>
                    <td id="' . $id . '">' . $total_ejecutado . '</td>
                    <td id="' . $id . '">' . $unidades . '</td>
                    </tr>';
                }
            }
            return $cantidad;
        } else {
            return $rs;
        }
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

function reportePresupuestado($datoWhere = '0', $agrupado = False, $reporte = True)
{
    global $conexion;

    try {
        switch ($datoWhere) {
            case '0':
                $where = '';
                break;

            default:
                $where = 'WHERE tb_presupuestado.id_labor = ' . $datoWhere;
                break;
        }

        $query = "SELECT
                    tb_presupuestado.id_presupuestado AS id, 
                    tb_presupuestado.id_labor AS id_labor, 
                    tb_presupuestado.mes AS mes, 
                    tb_presupuestado.anio AS anio, 
                    tb_presupuestado.labor AS labores, 
                    tb_presupuestado.valoresPresupuestado AS total_presupuestado, 
                    tb_presupuestado.unidadMedida AS unidades,
                    tb_presupuestado.finca AS finca
                FROM
                    tb_presupuestado
                $where
                ORDER BY
                    tb_presupuestado.mes ASC";
        $rs = mysqli_query($conexion, $query);

        if ($reporte) {
            $cantidad =  mysqli_num_rows($rs);
            while ($datos = mysqli_fetch_array($rs)) {
                $id = $datos['id'];
                $id_labor = $datos['id_labor'];
                $mes = $datos['mes'];
                $anio = $datos['anio'];
                $labores = $datos['labores'];
                $total_presupuestado = $datos['total_presupuestado'];
                $unidades = $datos['unidades'];

                if ($agrupado) {
                    echo '<tr class="even pointer">
                    <td id="' . $id . '">' . $anio . '</td>
                    <td id="' . $id . '">' . $labores . '</td>
                    <td id="' . $id . '">' . $total_presupuestado . '</td>
                    <td id="' . $id . '">' . $unidades . '</td>
                    </tr>';
                } else {
                    echo '<tr class="even pointer">
                    <td id="' . $id . '">' . $mes . '</td>
                    <td id="' . $id . '">' . $anio . '</td>
                    <td id="' . $id . '">' . $labores . '</td>
                    <td id="' . $id . '">' . $total_presupuestado . '</td>
                    <td id="' . $id . '">' . $unidades . '</td>
                    </tr>';
                }
            }
            return $cantidad;
        } else {
            return $rs;
        }
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}
