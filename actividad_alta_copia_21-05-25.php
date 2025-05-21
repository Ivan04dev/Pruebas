<!DOCTYPE html>

<html lang="es" class="material-style layout-fixed">

<head>
    <?php require_once("_head_copia.php"); ?>

    <link rel="stylesheet" href="assets/css/select2.min.css">
</head>

<body>
    <!-- [ Preloader ] Start -->
    <div class="page-loader">
        <div class="bg-primary"></div>
    </div>
    <!-- [ Preloader ] End -->
    <!-- [ Layout wrapper ] Start -->
    <div class="layout-wrapper layout-2">
        <div class="layout-inner">
            <!-- [ Layout sidenav ] Start -->
            <?php require_once("_sidenav.php"); ?>
            <!-- [ Layout sidenav ] End -->
            <!-- [ Layout container ] Start -->
            <div class="layout-container">
                <!-- [ Layout navbar ( Header ) ] Start -->
                <?php include("_navbarheader.php"); ?>
                <!-- [ Layout navbar ( Header ) ] End -->
                <!-- [ Layout content ] Start -->
                <div class="layout-content">
                    <!-- [ content ] Start -->
                    <div class="container-fluid flex-grow-1 container-p-y">
                        <h4 class="font-weight-bold py-3 mb-0">CDM</h4>
                        <input type="hidden" name="ultimo_acceso" value="<?php echo $ultimoAcceso; ?>">
                        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                                <!-- <li class="breadcrumb-item"><a href="#">Library</a></li> -->
                                <li class="breadcrumb-item active">Alta registro</li>
                            </ol>
                        </div><!--action="guarda.php" method="POST" enctype="multipart/form-data"-->
                        <form name='form_registro_actividad_cdm' id='form_registro_actividad_cdm'>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card mb-4">

                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="card-header">Detalle del registro</h6>
                                            <h6 class="card-header" id="last_access">Último Acceso: <?= $_SESSION['ultimo_acceso'] ?></h6>
                                            <h6 class="card-header" id="tiempo_sesion">Tiempo Activo: </h6>
                                        </div>
                                        
                                        <div class="card-body">

                                            <?php

                                            include("_fecha.php");
                                            $hoy = obtenerFecha();

                                            ?>
                                            

                                            <div class="form-row">
                                                <div class="form-group col-md-3 col-lg-4">
                                                    <label class="form-label">Nombre usuario</label>
                                                    <input type='hidden' name='origen' id='origen' value='alta_registro'>
                                                    <?php echo "<input type='text' class='form-control' name='nombre_usuario' id='nombre_usuario' value='$nombreCompleto' readonly>"; ?>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="form-group col-md-1 col-lg-4">
                                                    <label class="form-label">Usuario</label>
                                                    <?php echo "<input type='text' class='form-control' name='usuario' id='usuario' value='$usuario' readonly>"; ?>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="form-group col-md-2 col-lg-4">
                                                    <label class="form-label">Fecha</label>
                                                    <?php echo "<input type='text' class='form-control' name='fecha' id='fecha' value='$hoy' readonly>"; ?>
                                                    <!-- <input type='text' class='form-control' name='fecha' id='fecha' value=' #$_SESSION['ultimo_acceso'] ' readonly> -->
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="form-group col-md-2"></div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-2">
                                                    <label class="form-label">Hora inicio</label>
                                                    <?php //echo "<input type='text' class='form-control' placeholder='00:00'>"; 
                                                    ?>
                                                    <?php
                                                    $fecha = DateTime::createFromFormat('d-m-Y H:i:s', $hoy);
                                                    $hora = $fecha->format('H:i:s');
                                                    // echo $hora;
                                                    ?>
                                                    <!-- <input type='time' name='hora_inicio' id='hora_inicio' class='form-control' placeholder='00:00' readonly> -->
                                                    <input type='text' name='hora_inicio' id='hora_inicio' class='form-control' placeholder='<?php echo $hora; ?>' value="<?php echo $hora; ?>" readonly>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="form-group col-md-2 d-none" id="grupo_hora_fin">
                                                    <label class="form-label">Hora final</label>
                                                    <?php //echo "<input type='text' class='form-control' placeholder='00:00'>"; 
                                                    ?>
                                                    <!-- <input type='time' name='hora_fin' id='hora_fin' class='form-control' placeholder='00:00' readonly> -->
                                                    <input type='text' name='hora_fin' id='hora_fin' class='form-control' readonly>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="form-group col-md-2 d-none" id="grupo_tiempo">
                                                    <label class="form-label">Tiempo</label>
                                                    <div class="clearfix"></div>
                                                    <?php

                                                    echo "<input type='text' class='form-control' name='text_tiempoDeshabilitado' id='text_tiempoDeshabilitado' placeholder='00:00' disabled>";
                                                    echo "<input type='hidden' class='form-control' name='text_tiempo' id='text_tiempo'>";

                                                    ?>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label class="form-label">Actividad</label>
                                                    <div class="clearfix"></div>
                                                    <select class="custom-select flex-grow-1" name='actividad' id='actividad' required>
                                                        <option value=''>Selecciona un opción</option>
                                                        <option value='Monitoreo'>Monitoreo</option>
                                                        <option value='Otras Actividades'>Otras Actividades</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-3" id="grupo_incidencia">
                                                    <label class="form-label">Incidencia</label>
                                                    <div class="clearfix"></div>
                                                    <select class="custom-select flex-grow-1" name='incidencia' id='incidencia' required>
                                                        <option value=''>Selecciona un opción</option>
                                                        <option value='Monitoreo en vivo'>Monitoreo en vivo</option>
                                                        <option value='Revision de grabaciones'>Revision de grabaciones</option>
                                                        <option value='Siniestros'>Siniestros</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-row" id="grupo_tipo">
                                                <div class="form-group col-md-3">
                                                    <label class="form-label">Tipo</label>
                                                    <div class="clearfix"></div>
                                                    <select class="custom-select flex-grow-1" name='tipo' id='tipo' required>
                                                        <option value=''>Selecciona un opción</option>
                                                        <option value='Call Center'>Call Center</option>
                                                        <option value='Hibrida'>Hibrida</option>
                                                        <option value='Tienda'>Tienda</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- <div class="form-row d-none" id="grupo_otros"> -->
                                            <div class="form-row" id="grupo_otros">
                                                <div class="form-group col-md-3">
                                                    <label class="form-label">Recinto</label>
                                                    <div class="clearfix"></div>
                                                    <select class='custom-select flex-grow-1 js-example-basic-single' name='recinto' id='recinto' data-live-search='true' required>
                                                        <option value=''>Selecciona un opción</option>
                                                        <?php

                                                        $conexion = new conexionBD();
                                                        $con = $conexion->conectar();
                                                        $consulta = new consultaBD();

                                                        $campos = "SUCURSAL";
                                                        $tabla  = "ATC_SUCURSAL";
                                                        // $condiciones = "WHERE ENFUNCIONAMIENTO = 'si' AND DIVISION NOT IN ('StandAlone Norte', 'Cerco', 'StandAlone MetroSur') AND SUCURSAL NOT IN ('Acambaro', 'Acaponeta', 'Acatic', 'Acayucan', 'Actopan', 'Agua Dulce', 'Ahuacatlan', 'Ahualulco', 'Ahuateno', 'Altea Huinala', 'Altea Rio Nilo',   'Alvarado', 'Amatitan', 'Amealco', 'Anahuac', 'Arandas Centro', 'Arenal', 'Arenas', 'Cacahoatan', 'Capilla de Guadalupe', 'Casimiro Castillo', 'Catemaco', 'Cd Mendoza', 'Cd.Sahagun', 'Cedral', 'Centro Sur GDL', 'Chapala Centro', 'Chietla', 'Cholula',  'Compostela',  'Crystal Tuxpan', 'Cunduacan', 'El Salto', 'Etzatlan', 'Federico Medrano', 'Garcia Salinas', 'Gomez Farias', 'Gran Plaza Mazatlan', 'HB Chilpancingo', 'HB Cosmopol', 'HB Mexicali', 'HB Nogales', 'HB Pachuca', 'HB Palmas Acapulco', 'HB Perisur', 'HB Plaza Centella', 'HB Plaza Chalco', 'HB Salamanca', 'HB Villahermosa', 'Hidalgo Reynosa', 'Huatusco', 'Huimanguillo', 'Interlomas', 'Iramuco', 'Ixtlan', 'Jala', 'Jalostotitlan', 'Jesus Ma.Tepatitlan', 'Jocotepec', 'Lardizabal', 'Las Choapas', 'Lerdo De Tejada', 'Macuspana', 'Madero Durango', 'Manuel Doblado', 'Matriz Matehuala', 'Matriz Orizaba', 'Morelos', 'Ojuelos', 'Palmas', 'Panuco', 'Papantla', 'Paraiso', 'Parrilla', 'Patio Acapulco', 'Piedras Negras', 'Pipila', 'Plaza Crystal', 'Plaza Periferico', 'Plaza Poncitlan', 'Portal Durango', 'Portales', 'Purepero', 'Purisima', 'Republica', 'Riberas', 'Ruiz', 'San Andres Tuxtla', 'San Fernando', 'San Gaspar', 'San Ignacio', 'San Julian', 'San Miguel', 'Santa Ana Pacueco', 'Santiago', 'Santiago Tuxtla', 'Soriana San Andres', 'Soriana Tapachula', 'Tapachula Centro', 'Taxco', 'Tecuala', 'Tepatitlan Los Altos', 'Tequila', 'Teziutlan Centro', 'Tlajomulco Centro', 'Tlaquepaque Centro', 'Tula', 'Tuxpan', 'Urbi', 'Valladolid', 'Valle De Santiago', 'Valle Hermoso',  'Yuriria', 'Zacapu', 'Zapotiltic', 'Zapotlanejo') GROUP BY SUCURSAL ORDER BY SUCURSAL ASC";
                                                        
                                                        $condiciones = "WHERE ENFUNCIONAMIENTO = 'si' 
                                                            AND DIVISION NOT IN ('StandAlone Norte', 'Cerco', 'StandAlone MetroSur') 
                                                            AND SUCURSAL NOT IN ('Acambaro', 'Acaponeta', 'Acatic', 'Acayucan', 'Actopan', 'Agua Dulce', 'Ahuacatlan', 'Ahualulco', 'Ahuateno', 'Altea Huinala', 'Altea Rio Nilo',   'Alvarado', 'Amatitan', 'Amealco', 'Anahuac', 'Arandas Centro', 'Arenal', 'Arenas', 'BO Movil', 'BO Tiendas', 'Cacahoatan', 'Capilla de Guadalupe', 'Casimiro Castillo', 'Catemaco', 'Cd Mendoza', 'Cedral', 'Centro Sur GDL', 'Chapala Centro', 'Chietla', 'Cholula',  'Compostela',  'Crystal Tuxpan', 'Cunduacan', 'El Salto', 'Etzatlan', 'Federico Medrano', 'Garcia Salinas', 'Gomez Farias', 'Gran Plaza Mazatlan', 'HB Chilpancingo', 'HB Cosmopol', 'HB Mexicali', 'HB Nogales', 'HB Pachuca', 'HB Palmas Acapulco', 'HB Perisur', 'HB Plaza Centella', 'HB Plaza Chalco', 'HB Salamanca', 'HB Villahermosa', 'Hidalgo Reynosa', 'Huatusco', 'Huimanguillo', 'Interlomas', 'Iramuco', 'Ixtlan', 'Jala', 'Jalostotitlan', 'Jesus Ma.Tepatitlan', 'Jocotepec', 'Lardizabal', 'Las Choapas', 'Lerdo De Tejada', 'Macuspana', 'Madero Durango', 'Manuel Doblado', 'Matriz Matehuala', 'Matriz Orizaba', 'Morelos', 'Ojuelos', 'Palmas', 'Panuco', 'Papantla', 'Paraiso', 'Parrilla', 'Patio Acapulco', 'Piedras Negras', 'Pipila', 'Plaza Crystal', 'Plaza Periferico', 'Plaza Poncitlan', 'Portal Durango', 'Portales', 'Purepero', 'Purisima', 'Riberas', 'Ruiz', 'San Andres Tuxtla', 'San Fernando', 'San Gaspar', 'San Ignacio', 'San Julian', 'San Miguel', 'Santa Ana Pacueco', 'Santiago', 'Santiago Tuxtla', 'Soriana San Andres', 'Soriana Tapachula', 'Tapachula Centro', 'Taxco', 'Tecuala', 'Tepatitlan Los Altos', 'Tequila', 'Teziutlan Centro', 'Tlajomulco Centro', 'Tlaquepaque Centro', 'Tula', 'Tuxpan', 'Urbi', 'Valladolid', 'Valle De Santiago', 'Valle Hermoso',  'Yuriria', 'Zacapu', 'Zapotiltic', 'Zapotlanejo') 
                                                            GROUP BY SUCURSAL ORDER BY SUCURSAL ASC";
                                                        

                                                        $consulta->consultaDatos($conexion->conexion, $campos, $tabla, $condiciones);

                                                        while ($resArray = oci_fetch_row($consulta->stmt)) {

                                                            echo "<option value='" . $resArray[0] . "'>" . $resArray[0] . "</option>";
                                                            //echo "<option>".$resArray[0]."</option>";

                                                        }

                                                        $consulta->liberarDatos();
                                                        $conexion->cerrarConexion();

                                                        ?>
                                                    </select>
                                                </div>
                                                
                                                <div class="form-group col-md-3" id="grupo_region">
                                                    <label class="form-label">Región</label>
                                                    <div class="clearfix"></div>
                                                    <!-- <div class="input-group"> -->
                                                        <input type='text' name='text_region' id='text_region' class='form-control' placeholder='Región' readonly>
                                                    <!-- </div> -->
                                                </div>

                                                <div class="form-group col-md-3" id="grupo_ciudad">
                                                    <label class="form-label">Ciudad</label>
                                                    <div class="clearfix"></div>
                                                    <!-- <div class="input-group"> -->
                                                        <input type='text' name='text_ciudad' id='text_ciudad' class='form-control' placeholder='Ciudad' readonly>
                                                        <!--<select class="custom-select flex-grow-1" name='ciudad' id='ciudad' required>
                                                            <option value=''>Selecciona un opción</option>
                                                        </select>-->
                                                    <!-- </div> -->
                                                </div>

                                                <div class="form-group col-md-3" id="grupo_nombre_gerente">
                                                    <label class="form-label">Gerente</label>
                                                    <div class="clearfix"></div>
                                                    <!-- <div class="input-group"> -->
                                                        <input type='text' name='text_nombre_gerente' id='text_nombre_gerente' class='form-control' placeholder='Nombre gerente' readonly>
                                                    <!-- </div> -->
                                                </div>
                                                
                                            </div>
                                            <!-- <div class="d-none" id='resultado_consulta'></div> -->
                                            <div id='resultado_consulta'></div>

                                            <div class="form-row">
                                                <div class="form-group col-md-12">
                                                    <label class="form-label">Comentarios</label>
                                                    <div class="clearfix"></div>
                                                    <!-- <div class="input-group"> -->
                                                        <textarea class='form-control' name='comentarios' id='comentarios' rows="8" placeholder="Ingresa una breve descripción"></textarea>
                                                    <!-- </div> -->
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-4"></div>
                                                <div class="form-group col-md-4">
                                                    <div align="center"><input type='submit' name='form_button' id='form_button' value='Guardar'></div>
                                                </div>
                                                <div class="form-group col-md-4"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- [ content ] End -->
                <!-- [ Layout footer ] Start -->
                <?php include("_footer.php"); ?>
                <script src="assets/js/select2.min.js"></script>
                <!-- [ Layout footer ] End -->
            </div>
            <!-- [ Layout content ] Start -->
        </div>
        <!-- [ Layout container ] End -->
    </div>
    <!-- Overlay -->
    <div class="layout-overlay layout-sidenav-toggle"></div>
    <!-- [ Layout wrapper] End -->
    <script src="assets/js/jquery-ui.js"></script>
    <!-- <script src="assets/js/actividad_alta.js"></script> -->
    <!-- Muestra la leyenda de horas, minutos u horas y minutos después de ingresar la hora final  -->
    <script src="assets/js/actividad_alta_copia.js"></script>
    <!-- Nueva Alerta -->
    <!-- <script src="assets/js/alerta_inactividad_copia.js"></script> -->
    <script src="assets/js/alerta_inactividad_copia_dos.js"></script>
</body>

</html>
