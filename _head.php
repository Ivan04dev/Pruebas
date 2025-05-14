<?php
	
    session_start();

    if (!isset($_SESSION['usuario'])){
        
        header("Location: index.php"); 
        exit();

    }

?>

<?php

	$usuario = $_SESSION['usuario'];
	$nombre = $_SESSION['nombre'];
	$apaterno = $_SESSION['apaterno'];
	$amaterno = $_SESSION['amaterno']; 
	$usuario = $_SESSION['usuario'];
	$perfil = $_SESSION['perfil'];
	$nivel = $_SESSION['nivel'];
	$area = $_SESSION['area'];
	$estatus = $_SESSION['estatus'];
	$nombreCompleto = $_SESSION['nombreCompleto'];

	# Alerta Inactividad
	// $ultimo_acceso = $_SESSION['ultimo_acceso'] = time();
?>

# Código HTML en donde se agregan css y js
