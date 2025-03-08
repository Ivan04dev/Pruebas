$dataPorId = []

Es un arreglo de objetos con la siguiente estructura:

["fechacreado"] => "06-MAR-25 04.04.53.000000 PM" 
["titulo"] => "CONSECUTIVO PROMOCIONAL MARZO 2025" 
["estatus"] => "Leido" 
["fechaleido"] => "06-MAR-25 04.50.46.000000 PM" 
["sucursalregion"] => "Noreste" 
["responsableregion"] "Noreste" 
["responsablecargo"] "Gerente ATC" 
["responsablenombre"] "Emilio Ricardo Blanco Ponce" 

# Datos para Regiones
    $regiones = ['Metropolitana' => 0, 'Noreste' => 0, 'Occidente' => 0, 'Pacifico' => 0, 'Sureste' => 0];
    $regionesLeidos = $regionesPendientes = $regiones;
    
    foreach ($dataPorId as $elemento) {
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

    var_dump($dataRegiones);
    print_r($dataRegiones);
