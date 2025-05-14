<?php
    session_start();

    $tiempoMaxInactividad = 20 * 60; # 20 Minutos

    if(!isset($_SESSION['usuario']) || !isset($_SESSION['ultima_acceso'])){
        header('Location: index.php');
        exit;
    }

    $ahora = time();
    $tiempoInactivo = $ahora - $_SESSION['ultimo_acceso'];

    if($tiempoInactivo > $tiempoMaxInactividad){
        # La sesión ha caducado
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit;
    }

    # Si todo está bien, actualiza el último acceso
    $_SESSION['ultimo_acceso'] = $ahora;
