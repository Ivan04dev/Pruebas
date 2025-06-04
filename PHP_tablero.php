<?php
include "../../server/BD/_config/conexionBD.php";
include "../../server/BD/_config/consultaBD.php";

header("Content-Type: application/json");

$conexion = new conexionBD();
$con = $conexion->conectar();
$consulta = new consultaBD();

$fechaInicio = $_GET['fechaInicio'] ?? '2025-05-01';
$fechaFin = $_GET['fechaFin'] ?? '2025-06-01';

function ejecutarConsulta($con, $sql, $fechaInicio, $fechaFin)
{
    $stmt = oci_parse($con, $sql);
    oci_bind_by_name($stmt, ':inicio', $fechaInicio);
    oci_bind_by_name($stmt, ':fin', $fechaFin);
    oci_execute($stmt);

    $data = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $data[] = $row;
    }

    oci_free_statement($stmt);
    return $data;
}


$sql_comunicados = "
            SELECT
                l.TITULO AS Titulo,
                l.FECHACREADO AS FechaCreado,
                COUNT(CASE WHEN rl.ESTATUS = 'Leido' THEN 1 END) AS TotalLeidos,
                COUNT(CASE WHEN rl.ESTATUS = 'Pendiente' THEN 1 END) AS TotalPendientes,
                COUNT(rl.ID) AS Total,
                ROUND(100.0 * COUNT(CASE WHEN rl.ESTATUS = 'Leido' THEN 1 END) / NULLIF(COUNT(rl.ID), 0), 2) AS PorcentajeLeido,
                ROUND(100.0 * COUNT(CASE WHEN rl.ESTATUS = 'Pendiente' THEN 1 END) / NULLIF(COUNT(rl.ID), 0), 2) AS PorcentajePendiente
            FROM ATC_LECTURA l
            LEFT JOIN ATC_REPORTELECTURA rl ON rl.IDLECTURA = l.ID
            WHERE l.FECHACREADO >= TO_DATE(:inicio, 'YYYY-MM-DD') AND l.FECHACREADO < TO_DATE(:fin, 'YYYY-MM-DD')
            GROUP BY l.TITULO, l.FECHACREADO
            ORDER BY l.FECHACREADO DESC
        ";
/*
 $sql = "
     WITH LECTURAS_FILTRADAS AS (
         SELECT r.USUARIORED, r.IDLECTURA, r.ESTATUS, r.APERTURAPAG, r.FECHALEIDO
         FROM ATC_REPORTELECTURA r
         JOIN ATC_LECTURA l ON r.IDLECTURA = l.ID
         WHERE l.FECHACREADO >= TO_DATE(:inicio, 'YYYY-MM-DD') AND l.FECHACREADO < TO_DATE(:fin, 'YYYY-MM-DD')
     )
     SELECT
         r.RESPONSABLE, r.REGION, r.HUB,
         st.NOMBRE AS NOMBRE_EMPLEADO, st.APPATERNO, st.APMATERNO, st.USUARIORED, st.PUESTO, st.SUCURSAL,
         COUNT(CASE WHEN lf.ESTATUS = 'Leido' THEN 1 END) AS TOTAL_LEIDO,
         COUNT(CASE WHEN lf.ESTATUS = 'Pendiente' THEN 1 END) AS TOTAL_PENDIENTE,
         ROUND(AVG(CASE
             WHEN lf.ESTATUS = 'Leido' AND lf.APERTURAPAG IS NOT NULL AND lf.FECHALEIDO IS NOT NULL
             THEN (CAST(lf.FECHALEIDO AS DATE) - CAST(lf.APERTURAPAG AS DATE)) * 24 * 60
             ELSE NULL
         END), 2) AS TIEMPO_PROMEDIO_MINUTOS
     FROM ATC_STAFF st
     JOIN ATC_SUCURSAL s ON st.SUCURSAL = s.SUCURSAL
     JOIN ATC_RESPONSABLES r ON s.HUB = r.HUB AND r.CARGO = 'Gerente ATC'
     LEFT JOIN LECTURAS_FILTRADAS lf ON st.USUARIORED = lf.USUARIORED
     WHERE st.ESTADO IN ('Activo', 'Nuevo Ingreso')
       AND st.PUESTO IN ('Ejecutivo ATC', 'Ejecutivo Sr ATC', 'Jefe ATC', 'Jefe Regional ATC')
       AND s.REGION IN ('Metropolitana', 'Noreste', 'Occidente', 'Pacifico', 'Sureste')
     GROUP BY
         r.RESPONSABLE, r.REGION, r.HUB,
         st.NOMBRE, st.APPATERNO, st.APMATERNO,
         st.USUARIORED, st.PUESTO, st.SUCURSAL
     ORDER BY r.RESPONSABLE, st.PUESTO
 ";
 */
$sql_resumen = "
            SELECT
                resp.RESPONSABLE AS RESPONSABLE,
                l.TITULO AS TITULO,
                l.FECHACREADO AS FECHA_CREADO,
                COUNT(CASE WHEN r.ESTATUS = 'Leido' THEN 1 END) AS TOTAL_LEIDOS,
                COUNT(CASE WHEN r.ESTATUS = 'Pendiente' THEN 1 END) AS TOTAL_PENDIENTES,
                COUNT(r.ID) AS TOTAL,
                ROUND(
                    100 * COUNT(CASE WHEN r.ESTATUS = 'Leido' THEN 1 END) / NULLIF(COUNT(r.ID), 0), 2
                ) AS PORCENTAJE_LEIDO,
                ROUND(
                    100 * COUNT(CASE WHEN r.ESTATUS = 'Pendiente' THEN 1 END) / NULLIF(COUNT(r.ID), 0), 2
                ) AS PORCENTAJE_PENDIENTE
            FROM ATC_LECTURA l
            JOIN ATC_REPORTELECTURA r ON r.IDLECTURA = l.ID
            JOIN ATC_STAFF st ON st.USUARIORED = r.USUARIORED
            JOIN ATC_SUCURSAL s ON st.SUCURSAL = s.SUCURSAL
            JOIN ATC_RESPONSABLES resp ON s.HUB = resp.HUB AND resp.CARGO = 'Gerente ATC'
            WHERE
                l.FECHACREADO >= TO_DATE(:inicio, 'YYYY-MM-DD')
                AND l.FECHACREADO < TO_DATE(:fin, 'YYYY-MM-DD')
                AND st.ESTADO IN ('Activo', 'Nuevo Ingreso')
                AND st.PUESTO IN (
                    'Ejecutivo ATC', 'Ejecutivo Sr ATC', 'Jefe ATC', 'Jefe Regional ATC'
                )
                AND s.REGION IN ('Metropolitana', 'Noreste', 'Occidente', 'Pacifico', 'Sureste')
            GROUP BY
                resp.RESPONSABLE,
                l.TITULO,
                l.FECHACREADO
            ORDER BY
                resp.RESPONSABLE,
                l.FECHACREADO DESC
        ";

// Ejecutar todas las consultas 
$resultado = [
    'comunicados' => ejecutarConsulta($con, $sql_comunicados, $fechaInicio, $fechaFin),
    'resumen_por_gerente_y_comunicado' => ejecutarConsulta($con, $sql_resumen, $fechaInicio, $fechaFin),
];

oci_close($con);

echo json_encode($resultado);

?>
