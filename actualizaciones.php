<?php
include '../../../server/BD/_config/conexionBD.php';
include '../../../server/BD/_config/consultaBD.php';
include '../../../server/BD/_config/actualizaBD.php';
include '../../../server/BD/_config/insertaBD.php';

header('Content-Type: application/json');

try {
    $json = json_decode(file_get_contents("php://input"), true);
    if (!$json) throw new Exception("No se recibió un JSON válido");

    $usuario = $json['usuario'] ?? '';
    $id_video = $json['id_video'] ?? '';
    $tiempo_min = floatval($json['tiempo_min'] ?? 0);
    $reaccion = $json['reaccion'] ?? null;

    if (!$usuario || !$id_video || !$tiempo_min || $reaccion === null) {
        throw new Exception("Faltan datos obligatorios");
    }

    $conexion = new conexionBD();
    $con = $conexion->conectar();
    $consulta = new consultaBD();
    $actualiza = new actualizaBD();
    $inserta = new insertaBD();

    $fecha = date('d/m/Y H:i:s');

    // Verificar si ya existe el registro
    $stmt = $consulta->consultaDatos(
        $con,
        "ID",
        "ATC_CAPACITACIONES",
        "WHERE USUARIO = '$usuario' AND ID_VIDEO = $id_video"
    );
    $registro = oci_fetch_assoc($stmt);

    if ($registro) {
        $id = $registro['ID'];
        // Solo actualizar el estado
        $set = "ESTADO = 'visto' WHERE ID = $id";
        $actualiza->actualizaDatos($con, "ATC_CAPACITACIONES", $set, 0);
    } else {
        // Insertar nuevo registro completo
        $campos = "USUARIO, ID_VIDEO, VECES_VISTO, TIEMPO_TOTAL_MIN, FECHA_ULTIMA_VEZ, REACCION, ESTADO";
        $valores = "
            '$usuario',
            $id_video,
            1,
            $tiempo_min,
            TO_DATE('$fecha', 'DD/MM/YYYY HH24:MI:SS'),
            $reaccion,
            'visto'
        ";
        $inserta->insertaDatos($campos, $con, "ATC_CAPACITACIONES", $valores, 0);
    }

    echo json_encode([
        'status' => 'ok',
        'mensaje' => 'Registro actualizado o insertado correctamente'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
}
