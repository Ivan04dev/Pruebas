<?php
// ini_set('session.gc_maxlifetime', 3600); # Funciona 18 + 3 
ini_set('session.gc_maxlifetime', 7200); # Funciona 18 + 3 
session_set_cookie_params([
    // 'lifetime' => 3600,
    'lifetime' => 7200,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

header('Content-Type: application/json');

if (isset($_SESSION['usuario'])) {
    // Establece la zona horaria 
    date_default_timezone_set('America/Mexico_City');
    // Crea el objeto DateTime con la hora actual 
    $fecha_hoy = new DateTime();
    // Aplica la corrección manual
    if (date('I')) {
        $fecha_hoy->modify('-1 hour');
    }
    // Formatea la hora como en el formulario
    $hora_formateada = $fecha_hoy->format('d-m-Y H:i:s');
    // Almacena la sesión 
    $_SESSION['ultimo_acceso'] = $hora_formateada;
    // Respuesta en formato JSON 
    echo json_encode([
        "status" => "OK",
        "nuevo_ultimo_acceso" => $hora_formateada
    ]);
} else {
    echo json_encode(["status" => "NO_SESSION"]);
}
exit;
