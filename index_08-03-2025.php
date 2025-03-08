<?php

    include "_head.php";

    include ("../../server/BD/_config/conexionBD.php");
    include ("../../server/BD/_config/consultaBD.php");
    include ("../_formatoFecha.php");
     
    include "funciones_email.php";
   

    # Obtiene el último comunicado de ATC_LECTURAS del cual ya han pasado 3 días
    function lecturas(){
        $conexion = new conexionBD();
        $con = $conexion->conectar();
        $consulta = new consultaBD();   
       
        $array_comunicados = [];
        
        /* Original
        $consulta->consultaDatos($conexion->conexion, 
        '*', 
        'ATC_LECTURA', 
        "WHERE  FECHACREADO <= TRUNC(SYSDATE) - 2 ORDER BY FECHACREADO DESC FETCH FIRST 1 ROWS ONLY");
        */

        $consulta->consultaDatos($conexion->conexion, 
        '*',
        'ATC_LECTURA',
        'WHERE TRUNC(FECHACREADO) = TRUNC(SYSDATE) - 2 
        ORDER BY FECHACREADO DESC');

        while ($resArray = oci_fetch_array($consulta->stmt)){

            # Extrae los valores de las columnas y lo agrega al arreglo
            $array_comunicados[] = [
                'idLectura' => $resArray['ID'], 
                'fechaCreado' => $resArray['FECHACREADO'], 
                'titulo' => $resArray['TITULO'],
                
            ];
        
        }

        return $array_comunicados;

        $consulta->liberarDatos();
        $conexion->cerrarConexion();

    }

    # Obtiene idLectura, fechaCreado, titulo
    $resultadoLecturas = lecturas();

    /*
    echo "<pre>";
    var_dump($resultadoLecturas);
    echo "</pre>";
    */
    #die();


    # Definida en _formatoFecha;
    $fecha = fechaCompleta($fechaCreado);
    $fecha = substr($fecha, 0, 10);
    #echo "Fecha formateada: " . $fecha . "<br/>";

    function obtenerData($idLectura){
        $conexion = new conexionBD();
        $con = $conexion->conectar();
        $consulta = new consultaBD();  

        // Consulta global
        $par = "ATC_LECTURA.FECHACREADO AS LECTURA_FECHACREADO, 
        ATC_LECTURA.TITULO AS LECTURA_TITULO, 
        ATC_REPORTELECTURA.ESTATUS AS REPORTELECTURA_ESTATUS, 
        ATC_REPORTELECTURA.FECHALEIDO AS REPORTELECTURA_FECHALEIDO,
        ATC_SUCURSAL.REGION AS SUCURSAL_REGION, 
        ATC_RESPONSABLES.REGION AS RESPONSABLE_REGION, 
        ATC_RESPONSABLES.CARGO AS RESPONSABLE_CARGO, 
        ATC_RESPONSABLES.NOMBRE AS RESPONSABLE_NOMBRE";
    
        $tb = "ATC_LECTURA 
        INNER JOIN ATC_REPORTELECTURA ON ATC_REPORTELECTURA.IDLECTURA = ATC_LECTURA.ID
        INNER JOIN ATC_STAFF ON ATC_REPORTELECTURA.USUARIORED = ATC_STAFF.USUARIORED
        INNER JOIN ATC_SUCURSAL ON ATC_SUCURSAL.SUCURSAL = ATC_STAFF.SUCURSAL
        INNER JOIN ATC_RESPONSABLES ON ATC_RESPONSABLES.HUB = ATC_SUCURSAL.HUB";
    
        # // AND ATC_RESPONSABLES.CARGO = 'Gerente ATC'";
        // $cadena = "WHERE ATC_REPORTELECTURA.IDLECTURA = '$idComunicado'";
        $cadena = "WHERE ATC_REPORTELECTURA.IDLECTURA = '$idLectura' AND ATC_STAFF.ESTADO != 'Baja' AND ATC_RESPONSABLES.NOMBRE != 'null'
        AND ATC_RESPONSABLES.CARGO = 'Gerente ATC'";

        $resultado = $consulta->consultaDatos($con, $par, $tb, $cadena);
    
        if (!$resultado) {
            die("Error al ejecutar la consulta.");
        }
    
        $total = [];

        while ($row = oci_fetch_assoc($resultado)) {

            $total[] = [
                'fechacreado' => $row["LECTURA_FECHACREADO"] ?? null,
                'titulo' => $row["LECTURA_TITULO"] ?? null,
                'estatus' => $row["REPORTELECTURA_ESTATUS"] ?? null,
                'fechaleido' => $row["REPORTELECTURA_FECHALEIDO"] ?? null,
                'sucursalregion' => $row["SUCURSAL_REGION"] ?? null,
                'responsableregion' => $row["RESPONSABLE_REGION"] ?? null,
                'responsablecargo' => $row["RESPONSABLE_CARGO"] ?? null,
                'responsablenombre' => $row["RESPONSABLE_NOMBRE"] ?? null,
            ];

        }

        return $total;

    }

    # Nueva Función
    function procesarLecturas($resultadoLecturas){
        $datosFinales = [];

        foreach($resultadoLecturas as $lectura){
            # Extrae el id
            $idLectura = $lectura["idLectura"];

            # Llama a la función obtenerData para cada idLectura
            $data = obtenerData($idLectura);

            # Se agrega el resultado al nuevo arreglo
            $datosFinales[] = [
                "idLectura" => $idLectura,
                "fechaCreado" => $lectura["fechaCreado"],
                "titulo" => $lectura["titulo"],
                "detalles" => $data
            ];
        }

        #return $data;
        return $datosFinales;
        
    }

    #$data = procesarLecturas($resultadoLecturas);
    $data = procesarLecturas($resultadoLecturas);

    
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
    
    #die();

    # ================================================================================================================================
    
    # Datos para Regiones
    $regiones = ['Metropolitana' => 0, 'Noreste' => 0, 'Occidente' => 0, 'Pacifico' => 0, 'Sureste' => 0];

    $regionesLeidos = $regionesPendientes = $regiones;

    foreach ($data as $elemento) {
        $region = $elemento['responsableregion'];

        if (isset($regiones[$region])) {
            if ($elemento['estatus'] === 'Leido') {
                $regionesLeidos[$region]++;
            } elseif ($elemento['estatus'] === 'Pendiente') {
                $regionesPendientes[$region]++;
            }
        }
    }

    $dataRegiones = [];

    foreach ($regiones as $region => $valor) {
        $total = $regionesLeidos[$region] + $regionesPendientes[$region];
        $porcentaje = $total > 0 ? round(($regionesLeidos[$region] / $total) * 100) : 0;

        $dataRegiones[] = [
            'region' => $region,
            'leidos' => $regionesLeidos[$region],
            'pendientes' => $regionesPendientes[$region],
            'total' => $total,
            'porcentaje' => $porcentaje
        ];
    }
        
    echo "<pre>";
    var_dump($dataRegiones);
    echo "</pre>";
    #die();
    
    # ================================================================================================================================

    # Datos para Gerentes ATC
    $dataGerentes = [];

    // Se recorre el arreglo para obtener los datos de los gerentes
    foreach ($data as $gerente) {
        $nombreGerente = $gerente['responsablenombre'];
    
        // Inicializamos los contadores por gerente
        if (!isset($dataGerentes[$nombreGerente])) {
            $dataGerentes[$nombreGerente] = [
                'gerente' => $nombreGerente,
                'leidos' => 0,
                'pendientes' => 0,
                'total' => 0,
                'porcentaje' => 0,
                'antes' => 0,
                'despues' => 0,
                'porcentajeAntes' => 0,
                'porcentajeDespues' => 0,
            ];
        }
    
        // Contar leídos y pendientes
        if ($gerente['estatus'] === 'Leido') {
            $dataGerentes[$nombreGerente]['leidos']++;
        } elseif ($gerente['estatus'] === 'Pendiente') {
            $dataGerentes[$nombreGerente]['pendientes']++;
        }
    
        // Calcular los días entre fechacreado y fechaleido
        $fechaCreado = DateTime::createFromFormat('d-M-y h.i.s.u A', $gerente['fechacreado']);
        $fechaLeido = DateTime::createFromFormat('d-M-y h.i.s.u A', $gerente['fechaleido']);
    
        if ($fechaCreado && $fechaLeido) {
            $diferenciaDias = $fechaCreado->diff($fechaLeido)->days;
    
            if ($diferenciaDias <= 3) {
                $dataGerentes[$nombreGerente]['antes']++;
            } else {
                $dataGerentes[$nombreGerente]['despues']++;
            }
        }
    }
 
    // Calcular los totales y porcentajes
    foreach ($dataGerentes as &$gerenteData) {
        $gerenteData['total'] = $gerenteData['leidos'] + $gerenteData['pendientes'];
    
        // Evitar divisiones por cero
        if ($gerenteData['total'] > 0) {
            $gerenteData['porcentaje'] = round(($gerenteData['leidos'] / $gerenteData['total']) * 100);
        }
    
        if ($gerenteData['leidos'] > 0) {
            $gerenteData['porcentajeAntes'] = round(($gerenteData['antes'] / $gerenteData['leidos']) * 100);
            $gerenteData['porcentajeDespues'] = round(($gerenteData['despues'] / $gerenteData['leidos']) * 100);
        }
    }
 
    // Convertimos la estructura en un array indexado para mantener la salida esperada
    $dataGerentes = array_values($dataGerentes);

    // Ordena alfabéticamente por el nombre del gerente
    usort($dataGerentes, function ($a, $b) {
        return strcmp($a['gerente'], $b['gerente']);
    });

    var_dump($dataGerentes);
    die();

    # Crea la tabla Regiones 
    function crearTablaRegiones($dataRegiones){
        $tabla = '<table id="tablaRegiones" class="miTabla">';
        $tabla .= '
            <thead>
                <tr>
                    <th colspan="5">
                        <img src="img/01_header.png" width="1200" height="120">
                    </th>
                </tr>
                <tr>
                    <th scope="col">Región</th>
                    <th class="text-center" scope="col">Leídos</th>
                    <th class="text-center" scope="col">Pendientes</th>
                    <th class="text-center" scope="col">Total</th>
                    <th class="text-center" scope="col">% Lectura</th>
                </tr>
            </thead>
            <tbody>';
                foreach ($dataRegiones as $elemento) {
                    $porcentaje = floatval($elemento["porcentaje"]);

                    // Determinar la clase de color según el porcentaje 
                    if($porcentaje >= 90){
                        $bg = 'bg-verde';
                    } else if($porcentaje >= 80){
                        $bg = 'bg-amarillo';
                    } else {
                        $bg = 'bg-rojo';
                    }

                    $tabla .= '
                        <tr>
                            <th scope="row">'.htmlspecialchars($elemento["region"]).'</th>
                            <td class="text-center">'.intval($elemento["leidos"]).'</td>
                            <td class="text-center">'.intval($elemento["pendientes"]).'</td>
                            <td class="text-center">'.intval($elemento["total"]).'</td>
                            <td class="text-center ' . $bg . '">'.floatval($elemento["porcentaje"]).'%</td>
                        </tr>';
                }
                
            $tabla .= '</tbody></table>';

        return $tabla;
    }

    # Crea la tabla Gerentes 
    function crearTablaGerentes($dataGerentes){
        $tabla = '<table id="tablaGerentes" class="miTabla">';
        $tabla .= '
            <thead>
                <tr>
                    <th colspan="9">
                        <img src="img/01_header.png" width="1200" height="120">
                    </th>
                </tr>
                <tr>
                    <th scope="col">Gerente</th>
                    <th class="text-center" scope="col">Leídos</th>
                    <th class="text-center" scope="col">Pendientes</th>
                    <th class="text-center" scope="col">Total</th>
                    <th class="text-center" scope="col">% Leídos</th>
                    <th class="text-center" scope="col"><= 3 días</th>
                    <th class="text-center" scope="col">> 3 días</th>
                    <th class="text-center" scope="col">% Antes de 3 días</th>
                    <th class="text-center" scope="col">% Después de 3 días</th>
                </tr>
            </thead>
            <tbody>';
                foreach ($dataGerentes as $elemento) {
                    $porcentaje = floatval($elemento["porcentaje"]);
                    $porcentajeAntes = floatval($elemento["porcentajeAntes"]);

                    // Determinar la clase de color según el porcentaje 
                    if($porcentaje >= 90){
                        $bgP = 'bg-verde';
                    } else if($porcentaje >= 80){
                        $bgP = 'bg-amarillo';
                    } else {
                        $bgP = 'bg-rojo';
                    }

                    if($porcentajeAntes >= 90){
                        $bgPA = 'bg-verde';
                    } else if($porcentajeAntes >= 80){
                        $bgPA = 'bg-amarillo';
                    } else {
                        $bgPA = 'bg-rojo';
                    }

                    $tabla .= '
                        <tr>
                            <th scope="row">'.htmlspecialchars($elemento["gerente"]).'</th>
                            <td class="text-center">'.intval($elemento["leidos"]).'</td>
                            <td class="text-center">'.intval($elemento["pendientes"]).'</td>
                            <td class="text-center">'.intval($elemento["total"]).'</td>
                            <td class="text-center ' . $bgP . '">'.floatval($elemento["porcentaje"]).'%</td>
                            <td class="text-center">'.intval($elemento["antes"]).'</td>
                            <td class="text-center">'.intval($elemento["despues"]).'</td>
                            <td class="text-center ' . $bgPA . '">'.floatval($elemento["porcentajeAntes"]).'%</td>
                            <td class="text-center">'.floatval($elemento["porcentajeDespues"]).'%</td>
                        </tr>';
                }

            $tabla .= '</tbody></table>';

        return $tabla;
    }

    # Crea la tabla Regiones para enviarla con correo
    function crearTablaRegionesCorreo($dataRegiones){
        $tabla = '<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px">
            <thead>
                <tr>
                    <th colspan="5" style="text-align: center;">
                        <img src="img/01_header.png" width="600" height="60">
                    </th>
                </tr>
                <tr style="background-color: #F4F4F4;">
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: left;">Región</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">Leídos</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">Pendientes</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">Total</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">Lecturas</th>
                </tr>
            </thead>
            <tbody>';
                foreach($dataRegiones as $elemento){
                    $porcentaje = floatval($elemento["porcentaje"]);

                    $color = ($porcentaje >= 90) ? '#36CB2B' : (($porcentaje >= 80) ? '#F4F745' : '#FF3A3A'); 

                    $tabla .= '
                        <tr>
                            <td style="border: 1px solid #DDDDDD; padding: 8px;">'. htmlspecialchars($elemento["region"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">'. intval($elemento["leidos"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">'. intval($elemento["pendientes"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">'. intval($elemento["total"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center; background-color: '. $color .';">'. floatval($elemento["porcentaje"]) .'%</td>
                        </tr>
                    ';
                }
        $tabla .= '</tbody></table>';

        return $tabla;
    }

    # Crea la tabla Gerentes para enviarla con correo
    function crearTablaGerentesCorreo($dataGerentes){
        $tabla = '<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px">
            <thead>
                <tr>
                    <th colspan="9" style="text-align: center;">
                        <img src="img/01_header.png" width="600" height="60">
                    </th>
                </tr>
                <tr style="background-color: #F4F4F4;">
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: left;">Gerente</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">Leídos</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">Pendientes</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">Total</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">% Leídos</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;"><= 3 días</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">> 3 días</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">Antes de 3 días</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">Después de 3 días</th>
                </tr>
            </thead>
            <tbody>';
                foreach($dataGerentes as $elemento){
                    $porcentaje = floatval($elemento["porcentaje"]);
                    $porcentajeAntes = floatval($elemento["porcentajeAntes"]);

                    $colorP = ($porcentaje >= 90) ? '#36CB2B' : (($porcentaje >= 80) ? '#F4F745' : '#FF3A3A'); 
                    $colorPA = ($porcentajeAntes >= 90) ? '#36CB2B' : (($porcentajeAntes >= 80) ? '#F4F745' : '#FF3A3A'); 

                    $tabla .= '
                        <tr>
                            <td style="border: 1px solid #DDDDDD; padding: 8px;">'. htmlspecialchars($elemento["gerente"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">'. intval($elemento["leidos"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">'. intval($elemento["pendientes"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">'. intval($elemento["total"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center; background-color: '. $colorP .';">'. floatval($elemento["porcentaje"]) .'%</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">'. intval($elemento["antes"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">'. intval($elemento["despues"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center; background-color: '. $colorPA .';">'. floatval($elemento["porcentajeAntes"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">'. intval($elemento["porcentajeDespues"]) .'%</td>
                        </tr>
                    ';
                }
        $tabla .= '</tbody></table>';

        return $tabla;
    }
    
    #$tablaRegiones = crearTablaRegiones($dataRegiones);
    #$tablaGerentes = crearTablaGerentes($dataGerentes);

    $tablaRegionesCorreo = crearTablaRegionesCorreo($dataRegiones);
    $tablaGerentesCorreo = crearTablaGerentesCorreo($dataGerentes);

    # Aquí se muestran las tablas
    echo "<div class='mt-4 container'>
        <div class='row'>
            <div class='col-md-10-offset-2'>
                <div class='d-flex justify-content-between'>
                    <h3>Lectura: " . htmlspecialchars($titulo) . "</h3>
                    <h3>Enviado: " . htmlspecialchars($fecha) . " </h3>
                </div>
            </div>
        </div>
        <div class='my-4'>
            $tablaRegionesCorreo
        </div>
        <div>
            <a href='reporte_excel_regiones.php'>Descargar Regiones</a>
        </div>
        <div class='my-4'>
            $tablaGerentesCorreo
        </div>
        <div>
            <a href='reporte_excel_gerentes.php'>Descargar Regiones</a>
        </div>
    </div>";

    #$correo = ['asaucedo@izzi.mx', 'ivdelgadoga@izzi.mx'];
    $correo = ['ivdelgadoga@izzi.mx'];

	#email($titulo, $fecha, $tablaRegionesCorreo, $tablaGerentesCorreo, $correo);
    #generarExcelGerentes($dataGerentes);
	#mensajeCorreoEnviar($dataGerentes);

    emailDos($titulo, $fecha, $tablaRegionesCorreo, $tablaGerentesCorreo, $correo);
    generarExcelGerentes($dataGerentes);
	mensajeCorreoEnviar($dataGerentes);

###################################################################################################################################################################

# Tablas

    # Crea la tabla Regiones 
    function crearTablaRegiones($dataRegiones){
        $tabla = '<table id="tablaRegiones" class="miTabla">';
        $tabla .= '
            <thead>
                <tr>
                    <th colspan="5">
                        <img src="img/01_header.png" width="1200" height="120">
                    </th>
                </tr>
                <tr>
                    <th scope="col">Región</th>
                    <th class="text-center" scope="col">Leídos</th>
                    <th class="text-center" scope="col">Pendientes</th>
                    <th class="text-center" scope="col">Total</th>
                    <th class="text-center" scope="col">% Lectura</th>
                </tr>
            </thead>
            <tbody>';
                foreach ($dataRegiones as $elemento) {
                    $porcentaje = floatval($elemento["porcentaje"]);

                    // Determinar la clase de color según el porcentaje 
                    if($porcentaje >= 90){
                        $bg = 'bg-verde';
                    } else if($porcentaje >= 80){
                        $bg = 'bg-amarillo';
                    } else {
                        $bg = 'bg-rojo';
                    }

                    $tabla .= '
                        <tr>
                            <th scope="row">'.htmlspecialchars($elemento["region"]).'</th>
                            <td class="text-center">'.intval($elemento["leidos"]).'</td>
                            <td class="text-center">'.intval($elemento["pendientes"]).'</td>
                            <td class="text-center">'.intval($elemento["total"]).'</td>
                            <td class="text-center ' . $bg . '">'.floatval($elemento["porcentaje"]).'%</td>
                        </tr>';
                }
                
            $tabla .= '</tbody></table>';

        return $tabla;
    }

    # Crea la tabla Gerentes 
    function crearTablaGerentes($dataGerentes){
        $tabla = '<table id="tablaGerentes" class="miTabla">';
        $tabla .= '
            <thead>
                <tr>
                    <th colspan="9">
                        <img src="img/01_header.png" width="1200" height="120">
                    </th>
                </tr>
                <tr>
                    <th scope="col">Gerente</th>
                    <th class="text-center" scope="col">Leídos</th>
                    <th class="text-center" scope="col">Pendientes</th>
                    <th class="text-center" scope="col">Total</th>
                    <th class="text-center" scope="col">% Leídos</th>
                    <th class="text-center" scope="col"><= 3 días</th>
                    <th class="text-center" scope="col">> 3 días</th>
                    <th class="text-center" scope="col">% Antes de 3 días</th>
                    <th class="text-center" scope="col">% Después de 3 días</th>
                </tr>
            </thead>
            <tbody>';
                foreach ($dataGerentes as $elemento) {
                    $porcentaje = floatval($elemento["porcentaje"]);
                    $porcentajeAntes = floatval($elemento["porcentajeAntes"]);

                    // Determinar la clase de color según el porcentaje 
                    if($porcentaje >= 90){
                        $bgP = 'bg-verde';
                    } else if($porcentaje >= 80){
                        $bgP = 'bg-amarillo';
                    } else {
                        $bgP = 'bg-rojo';
                    }

                    if($porcentajeAntes >= 90){
                        $bgPA = 'bg-verde';
                    } else if($porcentajeAntes >= 80){
                        $bgPA = 'bg-amarillo';
                    } else {
                        $bgPA = 'bg-rojo';
                    }

                    $tabla .= '
                        <tr>
                            <th scope="row">'.htmlspecialchars($elemento["gerente"]).'</th>
                            <td class="text-center">'.intval($elemento["leidos"]).'</td>
                            <td class="text-center">'.intval($elemento["pendientes"]).'</td>
                            <td class="text-center">'.intval($elemento["total"]).'</td>
                            <td class="text-center ' . $bgP . '">'.floatval($elemento["porcentaje"]).'%</td>
                            <td class="text-center">'.intval($elemento["antes"]).'</td>
                            <td class="text-center">'.intval($elemento["despues"]).'</td>
                            <td class="text-center ' . $bgPA . '">'.floatval($elemento["porcentajeAntes"]).'%</td>
                            <td class="text-center">'.floatval($elemento["porcentajeDespues"]).'%</td>
                        </tr>';
                }

            $tabla .= '</tbody></table>';

        return $tabla;
    }

    # Crea la tabla Regiones para enviarla con correo
    function crearTablaRegionesCorreo($dataRegiones){
        $tabla = '<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px">
            <thead>
                <tr>
                    <th colspan="5" style="text-align: center;">
                        <img src="img/01_header.png" width="600" height="60">
                    </th>
                </tr>
                <tr style="background-color: #F4F4F4;">
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: left;">Región</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">Leídos</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">Pendientes</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">Total</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">Lecturas</th>
                </tr>
            </thead>
            <tbody>';
                foreach($dataRegiones as $elemento){
                    $porcentaje = floatval($elemento["porcentaje"]);

                    $color = ($porcentaje >= 90) ? '#36CB2B' : (($porcentaje >= 80) ? '#F4F745' : '#FF3A3A'); 

                    $tabla .= '
                        <tr>
                            <td style="border: 1px solid #DDDDDD; padding: 8px;">'. htmlspecialchars($elemento["region"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">'. intval($elemento["leidos"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">'. intval($elemento["pendientes"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">'. intval($elemento["total"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center; background-color: '. $color .';">'. floatval($elemento["porcentaje"]) .'%</td>
                        </tr>
                    ';
                }
        $tabla .= '</tbody></table>';

        return $tabla;
    }

    # Crea la tabla Gerentes para enviarla con correo
    function crearTablaGerentesCorreo($dataGerentes){
        $tabla = '<table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px">
            <thead>
                <tr>
                    <th colspan="9" style="text-align: center;">
                        <img src="img/01_header.png" width="600" height="60">
                    </th>
                </tr>
                <tr style="background-color: #F4F4F4;">
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: left;">Gerente</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">Leídos</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">Pendientes</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">Total</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">% Leídos</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;"><= 3 días</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">> 3 días</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">Antes de 3 días</th>
                    <th style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">Después de 3 días</th>
                </tr>
            </thead>
            <tbody>';
                foreach($dataGerentes as $elemento){
                    $porcentaje = floatval($elemento["porcentaje"]);
                    $porcentajeAntes = floatval($elemento["porcentajeAntes"]);

                    $colorP = ($porcentaje >= 90) ? '#36CB2B' : (($porcentaje >= 80) ? '#F4F745' : '#FF3A3A'); 
                    $colorPA = ($porcentajeAntes >= 90) ? '#36CB2B' : (($porcentajeAntes >= 80) ? '#F4F745' : '#FF3A3A'); 

                    $tabla .= '
                        <tr>
                            <td style="border: 1px solid #DDDDDD; padding: 8px;">'. htmlspecialchars($elemento["gerente"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">'. intval($elemento["leidos"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">'. intval($elemento["pendientes"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">'. intval($elemento["total"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center; background-color: '. $colorP .';">'. floatval($elemento["porcentaje"]) .'%</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">'. intval($elemento["antes"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">'. intval($elemento["despues"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center; background-color: '. $colorPA .';">'. floatval($elemento["porcentajeAntes"]) .'</td>
                            <td style="border: 1px solid #DDDDDD; padding: 8px; text-align: center;">'. intval($elemento["porcentajeDespues"]) .'%</td>
                        </tr>
                    ';
                }
        $tabla .= '</tbody></table>';

        return $tabla;
    }
?>
