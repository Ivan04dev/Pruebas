<?php
include("reporte_comunicados_funciones.php");
include("_formatoFecha.php");
?>

<!DOCTYPE html>
<html lang="es">

<head lang="es">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->
    <link rel="stylesheet" href="bootstrapv5/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrapv5/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="css/estilo.css">
    <?php

    include("_headPlantilla.php");

    ?>

</head>

<body>
    <!-- Header -->
    <header class="header" id="site-header">
        <div class="container menuprincipal">
            <!-- Inicio Menú Principal -->
            <?php

            include("_menu_Principal.php");

            ?>
            <!-- Fin Menú Principal -->
        </div>
    </header>
    <!-- ... End Header -->
    <!-- Menú derecho -->

    <?php

    include("_menuDerecho.php");

    ?>
    <!-- ... Fin Menú derecho -->

    <div class="content-wrapper">
        <!-- Stunning header -->
        <div class="stunning-header stunning-header-bg-breez">
            <div class="stunning-header-content">
                <h1 class="stunning-header-title">Reporte Lectura Tablero</h1>
            </div>
        </div>
        <!-- End Stunning header -->
        <!-- Overlay Search -->
        <!-- Código del  buscador -->
        <!-- End Overlay Search -->
        <!-- Inicio Formulario -->
        <div class="container">
            <div class="contact-form">

            </div>
        </div>
        <!-- Fin Formulario -->
        <!-- Contactos -->
        <!-- Fin Contactos -->
    </div>

    <div class="container-fluid">
        <div class='row'>
            <div class="col-lg-4"></div>
            <div class="col-lg-4" align="center">
                <?php
                echo "<div id='desctotal'><a href='reporte_lectura_graf.php?lectura=$lectura&us=$usuario&puesto=$puesto'><div class='btn btn-small btn--dark btn-hover-shadow'><span class='text'>Descargar</span></div></a></div>";
                echo "<div id='descnorte'><a href='reporte_lectura_graf.php?lectura=$lectura&div=Norte'><div class='btn btn-small btn--dark btn-hover-shadow'><span class='text'>Descargar</span></div></a></div>";
                echo "<div id='descmsur'><a href='reporte_lectura_graf.php?lectura=$lectura&div=Msur'><div class='btn btn-small btn--dark btn-hover-shadow'><span class='text'>Descargar</span></div></a></div>";
                ?>
            </div>
            <div class="col-lg-4"></div>
        </div>
        <div class='row medium-padding120'>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12"></div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                <table width="220" height="120">
                    <tr class="bgverde">
                        <td>&nbsp Satisfactorio</td>
                        <td> 90% - 100% </td>
                    </tr>
                    <tr class="bgamarillo">
                        <td>&nbsp Insatisfactorio</td>
                        <td> 80% - 89% </td>
                    </tr>
                    <tr class="bgrojo">
                        <td>&nbsp Deficiente</td>
                        <td> =< 79% </td>
                    </tr>
                </table>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12"></div>
        </div>

    </div>



    <div class='row'>

        <!-- Calendario -->
        <div class="container mt-2">
            <div class="row justify-content-around">
                <div class="col-lg-10 col-md-8 offset-2">
                    <h5 class="mb-4 text-center text-white">Selecciona el rango de fechas</h5>
                    <form id="form-fechas">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 my-2">

            <!-- COMUNICADOS  -->
            <div class="col-md-12 mt-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Comunicados</h5>
                            <hr>
                            <p>
                                <button class="btn btn-dark mb-3" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#com" aria-expanded="false" aria-controls="collapseExample"
                                    id="botonCom">
                                    Ocultar
                                </button>
                            </p>
                        </div>


                        <div class="collapse" id="com">
                            <!-- Spinner  -->
                            <div id="loaderComunicados" class="text-center my-4 d-none">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>

                            <div id="mensajeVacioComunicados" class="text-center text-muted my-3 d-none">
                                No hay datos disponibles para el periodo seleccionado
                            </div>

                            <div class="container mt-4 d-none" id="contenedorComunicados">
                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="text-center">Comunicados</h4>
                                        <table class="table table-bordered table-striped" id="tablaComunicados">
                                            <thead>
                                                <tr>
                                                    <th>Título</th>
                                                    <th>Fecha Creado</th>
                                                    <th>Leídos</th>
                                                    <th>Pendientes</th>
                                                    <th>Total</th>
                                                    <th>% Leídos</th>
                                                    <th>% Pendientes</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="mt-4" id="graficaComunicados"></div>
                                    </div>
                                </div>

                                <!-- Gráfica Semanal -->
                                <div class="row d-none" id="contenedorGraficaDia2">
                                    <div class="col-12 mt-4">
                                        <div id="graficaDia2">Gráfica 2 días después</div>
                                    </div>
                                </div>

                                <div class="row d-none" id="contenedorGraficaDia4">
                                    <div class="col-12 mt-4">
                                        <div id="graficaDia4">Gráfica 2 días despué4</div>
                                    </div>
                                </div>

                                <!-- Botón Exportar -->
                                <div class="d-flex justify-content-center mt-4">
                                    <div id="btnExportarComunicados" class="btnExcel mt-4">
                                        <img src="bootstrapv5/img/_excel.png" alt="btnExcel">
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div> <!-- Fin Collapse -->
                </div>

            </div>

            <!-- ############################################################################################  -->

            <!-- REGIONES -->
            <div class="col-md-12 mt-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Regiones</h5>
                            <hr>
                            <p>
                                <button class="btn btn-dark mb-3" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#reg" aria-expanded="false" aria-controls="collapseExample"
                                    id="botonReg">
                                    Ocultar
                                </button>
                            </p>
                        </div>

                        <div class="collapse" id="reg">

                            <!-- Spinner  -->
                            <div id="loaderRegiones" class="text-center my-4 d-none">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>

                            <div id="mensajeVacioRegiones" class="text-center text-muted my-3 d-none">
                                No hay datos disponibles para el periodo seleccionado
                            </div>

                            <div class="heading">
                                <p class="heading-text">Comunicados del mes por regiones</p>

                                <div class="mt-2 d-none" id="contenedorRegiones">
                                    <div class="d-flex justify-content-between">
                                        Tabla
                                        <div class="col-md-6">
                                            <table class="table table-striped" id="tablaRegiones">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Región</th>
                                                        <th scope="col">Leídos</th>
                                                        <th scope="col">Pendientes</th>
                                                        <th scope="col">Total</th>
                                                        <th scope="col">% Leídos</th>
                                                        <th scope="col">% Pendientes</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- Gráfica -->
                                        <div class="col-md-6">
                                            <div id="graficaRegiones"></div>
                                        </div>
                                    </div>

                                    <!-- Botón Exportar -->
                                    <div class="d-flex justify-content-center mt-4">
                                        <div id="btnExportarRegiones" class="btnExcel mt-4">
                                            <img src="bootstrapv5/img/_excel.png" alt="btnExcel">
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>
                </div>

            </div>

            <!-- ############################################################################################  -->

            <!-- GERENTES ATC -->
            <div class="col-md-12 mt-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Gerentes ATC</h5>
                            <hr>
                            <p>
                                <button class="btn btn-dark mb-3" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#gc" aria-expanded="false" aria-controls="collapseExample"
                                    id="botonGC">
                                    Ocultar
                                </button>
                            </p>
                        </div>

                        <div class="collapse" id="gc">

                            <!-- Spinner  -->
                            <div id="loaderGerentes" class="text-center my-4 d-none">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>

                            <div id="mensajeVacioGerentes" class="text-center text-muted my-3 d-none">
                                No hay datos disponibles para el periodo seleccionado
                            </div>

                            <div class="heading">
                                <p class="heading-text">Lectura por gerentes y comunicados</p>

                                <div class="mt-2 d-none" id="contenedorGerentesComunicados">
                                    <div class="d-flex justify-content-between">
                                        <!-- Tabla -->
                                        <div class="col-md-6">
                                            <table class="table table-striped" id="tablaGerentesComunicados">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Gerente</th>
                                                        <th scope="col">Titulo</th>
                                                        <th scope="col">Fecha Creado</th>
                                                        <th scope="col">Leidos</th>
                                                        <th scope="col">Pendientes</th>
                                                        <th scope="col">Total</th>
                                                        <th scope="col">% Leidos</th>
                                                        <th scope="col">% Pendientes</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>

                                    </div>

                                    <!-- Gráfica -->
                                    <div class="col-md-6">
                                        <div id="graficaGerentesComunicados"></div>
                                    </div>

                                    <!-- Botón Exportar -->
                                    <div class="d-flex justify-content-center mt-4">
                                        <div id="btnExportarGerentes" class="btnExcel mt-4">
                                            <img src="bootstrapv5/img/_excel.png" alt="btnExcel">
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

    </div>

    <!-- Inicio Footer -->
    <footer class="footer">
        <?php include("_footerHome.php"); ?>
    </footer>
    <!-- Fin Footer -->


    <!-- JS Script -->
    <script src="js/jquery-3.4.1.js"></script>
    <?php include("_scripts.php"); ?>
    <link href="css/estilo.css" rel="stylesheet">
    <link href="css/datatables.css" rel="stylesheet">
    <script src="js/jquery.dataTables.js"></script>
    <script src="js/jquery.tablesorter.js"></script>
    <script src="js/jquery.tablesorter.widgets.js"></script>
    <script src="js/jquery.tablesorter.combined.js"></script>
    <script src="js/tablascx.js"></script>
    <script src="js/rangoFechas.js"></script>
    <script src="Graficas/js/highcharts.js"></script>
    <script src="Graficas/js/modules/data.js"></script>
    <script src="Graficas/js/modules/exporting.js"></script>
    <script src="js/cm_graficarmn.js"></script>
    <script src="js/cm_graficarmn_ctrl.js"></script>
    <!-- ...end JS Script -->

    <!-- DataTable  -->
    <script src="bootstrapv5/js/dataTables.buttons.min.js"></script>
    <script src="bootstrapv5/js/buttons.html5.min.js"></script>
    <script src="bootstrapv5/js/jszip.min.js"></script>

    <!-- Nuevos -->
    <script src="bootstrapv5/js/bootstrap.min.js"></script>
    <script src="bootstrapv5/js/moment.min.js"></script>
    <script src="bootstrapv5/js/highcharts.js"></script>
    <script src="bootstrapv5/js/exporting.js"></script>
    <!-- <script src="bootstrapv5/js/__funciones.js"></script> -->
    <!-- <script src="assets/js/__prueba.js"></script> -->
    <!-- <script src="bootstrapv5/js/_reporte_comunicados_regiones.js"></script> -->
    <script src="bootstrapv5/js/__generar_graficas.js"></script>
    <script src="bootstrapv5/js/_reporte_comunicados_tablero.js"></script>
    <!-- <script src="bootstrapv5/js/__generar_graficas.js"></script> -->


    <script src="bootstrapv5/js"></script>
</body>

</html>