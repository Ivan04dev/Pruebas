<?php
header('Content-Type: application/json');

include '../../../server/BD/_config/conexionBD.php';
include '../../../server/BD/_config/insertaBD.php';

try {
    // Leer el JSON del cuerpo de la petición
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);

    if (!$datos) {
        throw new Exception("No se recibió un JSON válido");
    }

    // Extraer y validar campos
    $titulo = trim($datos['titulo'] ?? '');
    $subtitulo = trim($datos['subtitulo'] ?? '');
    $archivo = trim($datos['archivo'] ?? '');
    $duracion_min = floatval($datos['duracion_min'] ?? 0);

    if (!$titulo || !$archivo || $duracion_min <= 0) {
        throw new Exception("Faltan datos obligatorios o la duración es inválida");
    }

    // Preparar inserción
    $conexion = new conexionBD();
    $con = $conexion->conectar();
    $insertar = new insertaBD();

    $campos = "TITULO, SUBTITULO, ARCHIVO, DURACION_MIN";
    $valores = ":titulo, :subtitulo, :archivo, :duracion_min";

    $sql = "INSERT INTO ATC_VIDEOS_CAPACITACION ($campos) VALUES ($valores)";
    $stmt = oci_parse($con, $sql);

    oci_bind_by_name($stmt, ":titulo", $titulo);
    oci_bind_by_name($stmt, ":subtitulo", $subtitulo);
    oci_bind_by_name($stmt, ":archivo", $archivo);
    oci_bind_by_name($stmt, ":duracion_min", $duracion_min);

    if (!oci_execute($stmt)) {
        $error = oci_error($stmt);
        throw new Exception("Error al insertar: " . $error['message']);
    }

    echo json_encode([
        "status" => "ok",
        "mensaje" => "Vídeo insertado correctamente"
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "mensaje" => $e->getMessage()
    ]);
}
