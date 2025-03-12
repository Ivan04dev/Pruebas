<?php
    # Obtiene la data para los gerentes
    require_once("funciones.php");

    # Librería para crear archivos xlxs
    require_once("excel/phpspreadsheet/vendor/autoload.php");

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Style\Fill;
    use PhpOffice\PhpSpreadsheet\Style\Alignment;
    use PhpOffice\PhpSpreadsheet\Style\Border;

    /*
    # Variables de prueba
    $idLectura = '11442';
    $titulo = 'AMPLIACION DE DESCUENTOS 6 MESES';
    $fecha = '03-03-2025';
    $data = obtenerData($idLectura);
    */
    
    $idLectura = isset($_GET['idLectura']) ? intval($_GET['idLectura']) : 0;
    $titulo = isset($_GET['titulo']) ? urldecode($_GET['titulo']) : 'Titulo no definido';
    $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : 'Fecha no definida';

    $data = obtenerData($idLectura);

    /*
    echo $idLectura . "<br/>";
    echo $titulo . "<br/>";
    echo $fecha . "<br/>";
    echo "Datos: " . "<br/>";
    var_dump($data);
    #die();
    */

    $dataRegiones = [];

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

    # Datos Procesados para las regiones
    /*
    echo '<br/> ################################################################## <br/>';
    echo 'Datos Regiones: ' . '<br/>';
    var_dump($dataRegiones);
    */
    #die();

    function generarExcelRegiones($titulo, $fecha, $dataRegiones) {
        $spreadsheet = new Spreadsheet();
     
        $spreadsheet->getProperties()
            ->setTitle('Reporte CX Tiendas')
            ->setSubject('Reporte CX Tiendas')
            ->setDescription('Reporte CX Tiendas')
            ->setCreator('CX Tiendas')
            ->setLastModifiedBy('CX Tiendas');
     
        $sheet = $spreadsheet->getActiveSheet();

        $fechaFormato = str_replace('-', '', $fecha);
        $tituloFormato = str_replace(' ', '_', $titulo);

        $tituloHoja = strtolower($tituloFormato . '_' . $fechaFormato);

        // Asigna el titulo al worksheet
        $tituloSanitizado = preg_replace('/[\\\\\\/*\\[\\]:?]/', '', $tituloHoja);
        $sheet->setTitle(substr($tituloSanitizado, 0, 31));
     
        // Encabezados para título y fecha
        $sheet->setCellValue('A1', $titulo)
              ->setCellValue('H1', $fecha);
     
        // Estilos para el título y la fecha
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('H1')->getFont()->setBold(true)->setSize(12);
     
        // Unir celdas para centrar título y fecha
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('H1:I1');
     
        // Alineación centrada
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
     
        // Encabezados de la tabla
        $sheet->setCellValue('A3', 'Región')
              ->setCellValue('B3', 'Leídos')
              ->setCellValue('C3', 'Pendientes')
              ->setCellValue('D3', 'Total')
              ->setCellValue('E3', '% Lectura');
     
        // Estilos para los encabezados
        $headerStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D3D3D3'], // Gris
            ],
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $sheet->getStyle('A3:E3')->applyFromArray($headerStyle);
     
        // Comienza en la fila 4 después de los encabezados
        $i = 4;
     
        // Colores según porcentaje
        $colores = [
            'verde' => '36CB2B',
            'amarillo' => 'F4F745',
            'rojo' => 'FF3A3A'
        ];
     
        function obtenerColor($valor, $colores) {
            if ($valor > 90) {
                return $colores['verde'];
            } elseif ($valor > 80) {
                return $colores['amarillo'];
            } else {
                return $colores['rojo'];
            }
        }
     
        if (!empty($dataRegiones)) {
            foreach ($dataRegiones as $elemento) {
                $sheet->setCellValue('A' . $i, $elemento['region'])
                      ->setCellValue('B' . $i, $elemento['leidos'])
                      ->setCellValue('C' . $i, $elemento['pendientes'])
                      ->setCellValue('D' . $i, $elemento['total'])
                      ->setCellValue('E' . $i, $elemento['porcentaje']);
     
                // Aplicar color a la celda de porcentaje de lectura (columna E)
                $colorE = obtenerColor($elemento['porcentaje'], $colores);
                $sheet->getStyle('E' . $i)->getFill()->setFillType(Fill::FILL_SOLID)
                      ->getStartColor()->setARGB($colorE);
     
                $i++;
            }
        }
     
        // Aplicar bordes a toda la tabla
        $ultimaFila = $i - 1; // Última fila con datos
        $rangoTabla = "A3:E$ultimaFila"; // Rango completo de la tabla
     
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN, // Borde delgado
                    'color' => ['rgb' => '000000'], // Color negro
                ],
            ],
        ];
        
        $sheet->getStyle($rangoTabla)->applyFromArray($borderStyle);
     
        // Ajustar ancho de columnas automáticamente
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $nombreArchivo = "reporte_de_lectura_regiones_" . $tituloHoja. ".xlsx";
     
        // Generar y descargar el archivo Excel
        ob_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
        header('Cache-Control: max-age=0');
     
        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save("php://output");
     
        exit;
    }

    generarExcelRegiones($titulo, $fecha, $dataRegiones);

?>