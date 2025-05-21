<?php
ini_set('session.gc_maxlifetime', 7200);
session_set_cookie_params([
    'lifetime' => 7200,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

# Asegurar la zona horaria correcta
date_default_timezone_set('America/Mexico_City');

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$fecha_hoy = new DateTime();

if (date('I')) {
    $fecha_hoy->modify('-1 hour');
}

$_SESSION['ultimo_acceso'] = $fecha_hoy->format('d-m-Y H:i:s');

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

?>

<title>cx tiendas | CDM</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<meta name="description" content="" />
<meta name="keywords" content="">
<meta name="author" content="">
<link rel="icon" type="image/x-icon" href="#">

<!-- Google fonts -->
<!--<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">-->

<!-- Icon fonts -->
<link rel="stylesheet" href="assets/fonts/fontawesome.css">
<link rel="stylesheet" href="assets/fonts/ionicons.css">
<link rel="stylesheet" href="assets/fonts/linearicons.css">
<link rel="stylesheet" href="assets/fonts/open-iconic.css">
<link rel="stylesheet" href="assets/fonts/pe-icon-7-stroke.css">
<link rel="stylesheet" href="assets/fonts/feather.css">

<!-- Core stylesheets -->
<link rel="stylesheet" href="assets/css/bootstrap-material.css">
<link rel="stylesheet" href="assets/css/shreerang-material.css">
<link rel="stylesheet" href="assets/css/uikit.css">
<link rel="stylesheet" href="assets/css/botones.css">

<!-- Libs -->
<link rel="stylesheet" href="assets/libs/perfect-scrollbar/perfect-scrollbar.css">
<link rel="stylesheet" href="assets/libs/flot/flot.css">

<!-- Core scripts -->
<script src="assets/js/pace.js"></script>
<!-- <script src="assets/js/jquery-3.3.1.min.js"></script> -->
<script src="../head/jquery-3.5.1.min.js"></script>
<script src="assets/libs/popper/popper.js"></script>
<script src="assets/js/bootstrap.js"></script>
<!-- <script src="../head/bootstrap-3.4.1/docs/dist/js/bootstrap.js"></script> -->
<script src="assets/js/sidenav.js"></script>
<script src="assets/js/layout-helpers.js"></script>
<script src="assets/js/material-ripple.js"></script>

<!-- Libs -->
<!-- 
<script src="assets/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="assets/libs/eve/eve.js"></script>
<script src="assets/libs/flot/flot.js"></script>
<script src="assets/libs/flot/curvedLines.js"></script>
<script src="assets/libs/chart-am4/core.js"></script>
<script src="assets/libs/chart-am4/charts.js"></script>
<script src="assets/libs/chart-am4/animated.js"></script>
<script src="assets/libs/raphael/raphael.js"></script> 
-->

<!-- Demo -->
<script src="assets/js/demo.js"></script>
<!-- <script src="assets/js/analytics.js"></script>
<script src="assets/js/pages/dashboards_index.js"></script> -->

<!-- jquery validate -->
<script src="assets/js/jquery.validate.js"></script>
<script src="assets/js/messages_es.js"></script>
<script src="assets/js/additional-methods.min.js"></script>

<!--- Tablas -->
<!-- <link rel="stylesheet" href="assets/css/jquery.dataTables.css"> -->
<!-- <script src="assets/js/jquery.dataTables.js"></script>-->
<!-- <script src="assets/js/jquery.tablesorter.js"></script> -->
<!-- <script src="assets/js/tablas.js"></script> -->

<!-- <script src="assets/js/jquery.expander.min.js"></script> -->
<!-- <script src="assets/js/fcn_acortar.js"></script> -->

<link rel="stylesheet" href="assets/css/estilo.css">
<link rel="stylesheet" href="assets/css/botones.css">

<script src="../head/sweetalert/sweetalert2.min.js"></script>
<link href="../head/sweetalert/sweetalert2.min.css" rel="stylesheet">

<!-- <script src="assets/js/cargador.js"></script> -->

<link href="../head/dataTables/jquery.dataTables.css" rel="stylesheet">
<script src="../head/dataTables/jquery.dataTables.js"></script>
<script src="../head/dataTables/jquery.tablesorter.js"></script>
<script src="../head/dataTables/jquery.tablesorter.widgets.js"></script>
<script src="../head/dataTables/jquery.tablesorter.combined.js"></script>

<script src="assets/js//quita_acentos.js"></script>

<!-- <script src="assets/js/script_modal.js"></script> -->
