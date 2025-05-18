console.log('Desde alerta_inactividad_copia.js');
let tiempoInactividad;
const tiempoMaxInactividad = 5 * 60 * 1000; // 25 Minutos Producción || 5 Desarrollo
let alertaMostrada = false;
let interval;

let tiempoespera = 300;

// Si aún no permite notificaciones solicita habilitarlas
if(Notification.permission != 'granted'){
    Notification.requestPermission();
}

// Nuevo
let usuarioVolvio = false;
let timeoutAlerta;

function notificarSesionPorExpirar(){
    usuarioVolvio = false;

    // Notificación del sistema
    if(Notification.permission === 'granted'){
        const noti = new Notification('Tu sesión está por expirar', {
            body: 'Tienes un minuto para regresar al formulario de registro de actividades cdm',
            icon: "assets/img/cdm_128.png"
        });

        noti.onclick = function(e){
            e.preventDefault();
            window.focus();
            usuarioVolvio = true;

            // Si el usuario regresa antes de que aparezca la alertaMostrada, se cancela 
            if(timeoutAlerta){
                clearInterval(timeoutAlerta);
                timeoutAlerta = null;
                console.log('Alerta cancelada porque el usuario volvió a tiempo');
            }
        }
    }

    /* Lanza la alerta (10 segundos después)
    setTimeout(() => {
        mostrarAlerta();
    }, 10 * 1000);
    */

    timeoutAlerta = setTimeout(() => {
        if(!usuarioVolvio){
            mostrarAlerta();
        } else {
            console.log('Usuario regresó antes de mostrar la alerta, no se muestra el Swal');
        }
    }, 10 * 1000);
}

function formatearTiempo(segundos){
    let minutos = Math.floor(segundos / 60);
    let segundosSolo = segundos % 60;
    let resultado = '';

    if(minutos > 0){
        resultado += `${minutos} minuto${minutos > 1 ? 's' : ''}`;
    }

    if(segundosSolo > 0 || minutos === 0){
        if(resultado !== '') resultado += ' ';
        resultado += `${segundosSolo} segundo${segundosSolo !== 1 ? 's' : ''}`;
    }

    return resultado;
}

function mostrarAlerta() {
    if (alertaMostrada) return;
    alertaMostrada = true;

    let segundosRestantes = tiempoespera;

    Swal.fire({
        title: '¿Deseas guardar o renovar sesión?',
        html: `<p>Tu sesión está a punto de expirar por inactividad, los cambios no guardados se perderán.</p>
                
                <p><strong>Se cerrará automáticamente en <span id="contador">${formatearTiempo(segundosRestantes)}</span>.</strong></p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Renovar Sesión',
        reverseButtons: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        customClass: {
            confirmButton: 'btn-confirm',
            cancelButton: 'btn-cancel'
        },
        /*
        didOpen: () => {
            const contadorElem = document.getElementById('contador');
            interval = setInterval(() => {
                segundosRestantes--;
                
               if(contadorElem){
                contadorElem.textContent = formatearTiempo(segundosRestantes);
               }
            //    document.title = `${segundosRestantes}s para cerrar sesión`;
               document.title = `La sesión se cerrará en ${formatearTiempo(segundosRestantes)}`;

               if(segundosRestantes <= 0){
                clearInterval(interval);
                document.title = 'cdm actividades';
                Swal.close();
                window.location.href = '_logout.php'; // Aquí redirige si no se hace nada
            }
            }, 1000);
        },
        */
        didOpen: () => {
            const contadorElem = document.getElementById('contador');
            const tiempoMaxAlerta = 5 * 60 * 1000; // 5 minutos
            const tiempoExpiracion = Date.now() + tiempoMaxAlerta;
        
            interval = setInterval(() => {
                const ahora = Date.now();
                const milisegRestantes = tiempoExpiracion - ahora;
        
                if (milisegRestantes <= 0) {
                    clearInterval(interval);
                    document.title = 'cdm actividades';
                    Swal.close();
                    window.location.href = '_logout.php'; // Redirección automática
                    return;
                }
        
                const segundosRestantes = Math.floor(milisegRestantes / 1000);
                if (contadorElem) {
                    contadorElem.textContent = formatearTiempo(segundosRestantes);
                }
        
                document.title = `La sesión se cerrará en ${formatearTiempo(segundosRestantes)}`;
            }, 1000);
        },
        willClose: () => {
            clearInterval(interval);
            document.title = 'cdm actividades';
        }
    }).then((result) => {
        alertaMostrada = false;
        clearInterval(interval);

        if (result.isConfirmed) {
            console.log('Guardar cambios (acción personalizada)');
            // Valida que los campos obligatorios del formulario estén llenos
            $("#form_registro_actividad_cdm").validate({
                onfocusout: false,
                rules: {
                    actividad: { required: true },
                    incidencia: { required: true },
                    tipo: { required: true },
                    region: { required: true },
                    ciudad: { required: true },
                    comentarios: { maxlength: 1000 }
                },
                
                submitHandler: function (form) {
                    guardarFormulario(form);
                }
            });

            // Dispara el envío del formulario tras la validación 
            $('#form_registro_actividad_cdm').submit();

        } else if (result.dismiss === Swal.DismissReason.cancel) {
            fetch('_renovar_sesion_copia.php', {
                method: 'GET',
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                console.log('Respuesta renovar sesión:', data);

                if(data.status === 'OK'){
                    console.log('Sesión renovada: ', data.nuevo_ultimo_acceso);
                    const access_text = document.getElementById('last_access');
                    if(access_text) access_text.innerText = data.nuevo_ultimo_acceso;

                    reiniciarTemporizador();

                    Swal.fire({
                        title: 'Sesión Renovada',
                        text: 'Tu sesión ha sido extendida.',
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                        customClass: { confirmButton: 'btn-confirm' },
                        buttonsStyling: false
                    });
                } else if(data.status === 'NO_SESSION'){
                    Swal.fire('Sesión no activa', 'Debes iniciar sesión nuevamente', 'error')
                        .then(() => window.location.href = 'index.php');
                } else {
                    Swal.fire('Error inesperado', 'Intenta nuevamente', 'error');
                }
            })
            .catch(error => {
                console.error('Error al renovar la sesión:', error);
                window.location.href = 'index.php';
            });
        }
    });
}

function reiniciarTemporizador() {
    clearTimeout(tiempoInactividad);
    alertaMostrada = false;
    tiempoespera = 300;
    tiempoInactividad = setTimeout(notificarSesionPorExpirar, tiempoMaxInactividad);
}

// Eventos que reinician el temporizador (interacción del usuario)
if (!window.eventosInactividadRegistrados) {
    ['mousemove', 'keydown', 'click'].forEach(evento => {
        document.addEventListener(evento, reiniciarTemporizador);
    });
    window.eventosInactividadRegistrados = true;
}

// Reinicio si vuelve a la pestaña 
document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
        reiniciarTemporizador();
    }
})


// Inicia el temporizador al cargar la página 
reiniciarTemporizador();

function guardarFormulario(form){
    const data = new FormData(form);

    let hora_inicio_raw = $('#hora_inicio').val();
    let hora_inicio_ = hora_inicio_raw.length === 5 ? hora_inicio_raw + ':00' : hora_inicio_raw;
    let fechaInicio = new Date('1970-01-01T' + hora_inicio_);

    let now = new Date();
    let hora_fin = [
        String(now.getHours()).padStart(2, '0'),
        String(now.getMinutes()).padStart(2, '0'),
        String(now.getSeconds()).padStart(2, '0'),
    ].join(':');

    let hora_fin_ = hora_fin;
    let fechaFin = new Date('1970-01-01T' + hora_fin_);

    if(fechaFin < fechaInicio){
        fechaFin.setDate(fechaFin.getDate() + 1);
    }

    let diferencia = fechaFin - fechaInicio;
    let tiempoFormateado = convertirMilisegundosAHorasMinutosSegundos(diferencia);
    let tiempoLegible = convertirMilisegundosALegible(diferencia);

    $('#hora_fin').val(hora_fin);
    $('#text_tiempo').val(tiempoFormateado);
    $('#text_tiempoDeshabilitado').val(tiempoFormateado);
    $('#grupo_hora_fin').removeClass('d-none');
    $('#grupo_tiempo').removeClass('d-none');

    data.append('hora_fin', hora_fin);
    data.append('text_tiempo', tiempoFormateado);

    $.ajax({
        url: 'guarda.php',
        type: 'POST',
        enctype: 'multipart/form-data',
        processData: false,
        contentType: false,
        cache: false,
        dataType: 'html',
        data: data,
        beforeSend: function(){
            $('#form_button').val('Guardando...');
            $('#form_button').prop('disabled', true);
        },
        success: function(data){
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Registro exitoso',
                html: `<p>${data}</p><p>Tiempo capturado: <strong>${tiempoLegible}</strong></p>`,
                showConfirmButton: false,
                timer: 10000,
                timerProgressBar: true
            });

            setTimeout(() => {
                window.location.href = 'home.php';
            }, 10000);
        },
        error: function(){
            console.error('Error al procesar el formulario.');
        },
        complete: function(){
            $('#form_button').val('Guardando...');
            $('#form_button').prop('disabled', true);
        }
    });
}
