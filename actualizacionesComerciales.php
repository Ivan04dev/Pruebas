<?php
    // Conexión a la base de datos
    include "../../server/BD/_config/conexionBD.php";
    include "../../server/BD/_config/consultaBD.php";
    
    $conexion = new conexionBD();
    $con = $conexion->conectar();
    $consulta = new consultaBD();
    
    if(isset($_POST['fecha'])){
        #$fecha = $_POST['fecha'];

        #$fecha = '2025-02-06';
        #$formato = 'YYYY-MM-DD HH24:MI:SS';
        #$fechaInicio = $fecha . ' 00:00:00';
        #$fechaFin = $fecha . ' 23:59:59';

        $par = "*";

        $tb = "ATC_DOCUMENTOS";

        $cadena = "
            WHERE FECHA BETWEEN TRUNC(SYSDATE, 'MM')     
            AND LAST_DAY(SYSDATE) + INTERVAL '23:59:59' 
            HOUR TO SECOND AND CLASIFICACION = 'actcomercial'
            ORDER BY FECHA DESC
            FETCH FIRST 1 ROW ONLY"
        ;

        $resultado = $consulta->consultaDatos($con, $par, $tb, $cadena);
    
        if (!$resultado) {
            die("Error al ejecutar la consulta.");
        }
    
        $actualizacionesComerciales = [];

        while ($row = oci_fetch_assoc($resultado)) {
            $actualizacionesComerciales[] = [
                'titulo' => $row["TITULO"] ?? null
            ];

        }

        // Salida JSON
        header('Content-Type: application/json');
        echo json_encode($actualizacionesComerciales);
 
    }
    
?>