<?php
    # Obtiene la data para los gerentes
    require_once("funciones.php");

    # Librería pra Excel
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
    */
    #die();
    
    
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

    # Datos Procesados para Gerente ATC
    /*
    #echo 'Datos Gerentes: ' . '<br/>';
    #var_dump($dataGerentes);
    */

    # Excel Gerentes
    function generarExcelGerentes($titulo, $fecha, $dataGerentes){

        $spreadsheet = new Spreadsheet();
        
        $spreadsheet->getProperties()
            ->setTitle('reporte cx tiendas')
            ->setSubject('reporte cx tiendas')
            ->setDescription('reporte cx tiendas')
            ->setCreator('cx tiendas')
            ->setLastModifiedBy('cx tiendas');

        $sheet = $spreadsheet->getActiveSheet();

        $fechaFormato = str_replace('-', '', $fecha);
        $tituloFormato = str_replace(' ', '_', $titulo);

        $tituloHoja = strtolower($tituloFormato . '_' . $fechaFormato);

        // Asigna el titulo al worksheet
        $tituloSanitizado = preg_replace('/[\\\\\\/*\\[\\]:?]/', '', $tituloHoja);
        $sheet->setTitle(substr($tituloSanitizado, 0, 31));

        // Encabezados para Titulo y Fecha 
        $sheet->setCellValue('A1', $titulo)
            ->setCellValue('H1', $fecha);

        // Aplica estilo a los encabezados 
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('H1')->getFont()->setBold(true)->setSize(12);

        // Unir las celdas para entrar los encabezados 
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('H1:I1');

        // Ajustar la alineación de los encabezados
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Encabezados
        $sheet->setCellValue('A3', 'Gerente')
            ->setCellValue('B3', 'Leídos')
            ->setCellValue('C3', 'Pendientes')
            ->setCellValue('D3', 'Total')
            ->setCellValue('E3', '% Leídos')
            ->setCellValue('F3', '<= 3 días')
            ->setCellValue('G3', '> 3 días')
            ->setCellValue('H3', '% Antes de 3 días')
            ->setCellValue('I3', '% Después de 3 días');

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
        $sheet->getStyle('A3:I3')->applyFromArray($headerStyle);

        // Comienza en la fila 4 dspués de los encabezados
        $i = 4;

        # Determinar el color según el %
        $colores = [
            'verde' => '36CB2B',
            'amarillo' => 'F4F745',
            'rojo' => 'FF3A3A'
        ];

        // Obtiene el color según el valor de cada columna 
        function obtenerColor($valor, $colores){
            if($valor > 90){
                return $colores['verde'];
            } else if($valor > 80){
                return $colores['amarillo'];
            } else {
                return $colores['rojo'];
            }
        }

        if(!empty($dataGerentes)){
            foreach($dataGerentes as $elemento) {
                $sheet->setCellValue('A'.$i, $elemento['gerente'])
                ->setCellValue('B'.$i, $elemento['leidos'])
                ->setCellValue('C'.$i, $elemento['pendientes'])
                ->setCellValue('D'.$i, $elemento['total'])
                ->setCellValue('E'.$i, $elemento['porcentaje'])
                ->setCellValue('F'.$i, $elemento['antes'])
                ->setCellValue('G'.$i, $elemento['despues'])
                ->setCellValue('H'.$i, $elemento['porcentajeAntes'])
                ->setCellValue('I'.$i, $elemento['porcentajeDespues']);

                // Aplicar color a % Leídos (E)
                $colorE = obtenerColor($elemento['porcentaje'], $colores);
                $sheet->getStyle('E' . $i)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($colorE);

                // Aplicar color a % Antes de 3 días (H)
                $colorH = obtenerColor($elemento['porcentajeAntes'], $colores);
                $sheet->getStyle('H' . $i)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($colorH);

                $i++;
            }

        }

        // Aplicar bordes a toda la tabla
        $ultimaFila = $i - 1; // Última fila con datos
        $rangoTabla = "A3:I$ultimaFila"; // Rango completo de la tabla
     
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN, // Borde delgado
                    'color' => ['rgb' => '000000'], // Color negro
                ],
            ],
        ];

        $sheet->getStyle($rangoTabla)->applyFromArray($borderStyle);

        // Ajusta el ancho de las columnas automáticamente
        foreach(range('A', 'I') as $col){
            $spreadsheet->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
        }

        $nombreArchivo = "reporte_de_lectura_gerentes_" . $tituloHoja. ".xlsx";

        // Generar y descargar el archivo Excel 
        ob_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save("php://output");

        exit;
    }

    generarExcelGerentes($titulo, $fecha, $dataGerentes);

?>