console.log('Desde alerta_inactividad_copia_dos.js');
 
let tiempoInactividad;
const tiempoMaxInactividad = 25 * 60 * 1000; // 25 minutos
let alertaMostrada = false;
let interval;
let tiempoespera = 300; // 5 minutos (en segundos)
// let tiempoespera = 180; // 5 minutos (en segundos)
let timeoutAlerta;

let contadorRenovaciones = 0;
 
function notificarSesionPorExpirar() {
    if (Notification.permission === 'granted') {
        const noti = new Notification('Tu sesión está por expirar', {
            body: 'Tienes un minuto para regresar al formulario de registro de actividades cdm',
            icon: "assets/img/cdm_128.png"
        });
 
        noti.onclick = (e) => {
            e.preventDefault();
            window.focus();
            console.log('Usuario hizo clic en la notificación.');
        };
    }
 
    /*
    timeoutAlerta = setTimeout(() => {
        console.log('Mostrando alerta tras 10 segundos de notificación.');
        mostrarAlerta();
    }, 10 * 1000);
    */
}
 
function formatearTiempo(segundos) {
    let minutos = Math.floor(segundos / 60);
    let segundosSolo = segundos % 60;
    let resultado = '';
 
    if (minutos > 0) {
        resultado += `${minutos} minuto${minutos > 1 ? 's' : ''}`;
    }
    if (segundosSolo > 0 || minutos === 0) {
        if (resultado !== '') resultado += ' ';
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
        didOpen: () => {
            const contadorElem = document.getElementById('contador');
            const tiempoExpiracion = Date.now() + (5 * 60 * 1000); // 5 minutos
            // const tiempoExpiracion = Date.now() + (3 * 60 * 1000); // 3 minutos
 
            interval = setInterval(() => {
                const ahora = Date.now();
                const milisegRestantes = tiempoExpiracion - ahora;
 
                if (milisegRestantes <= 0) {
                    clearInterval(interval);
                    document.title = 'cdm actividades';
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sesión expirada',
                        text: 'No hubo respuesta en 5 minutos. Serás redirigido.',
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '_logout.php';
                    });
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
            console.log('Guardar cambios (renovando sesión antes de enviar formulario)');
 
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
                    fetch('_renovar_sesion_copia.php', {
                        method: 'GET',
                        credentials: 'include'
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'OK') {
                            console.log('Sesión renovada antes de guardar');
                            guardarFormulario(form);
                        } else {
                            Swal.fire('Sesión no activa', 'Debes iniciar sesión nuevamente', 'error')
                                .then(() => window.location.href = 'index.php');
                        }
                    })
                    .catch(error => {
                        console.error('Error al renovar sesión:', error);
                        Swal.fire('Error', 'No se pudo renovar la sesión. Intenta nuevamente.', 'error');
                    });
                }
            });
 
            $('#form_registro_actividad_cdm').submit();
 
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            renovarSesion();
        }
    });
}
 
function reiniciarTemporizador() {
    console.log('Temporizador reiniciado manualmente');
    clearTimeout(tiempoInactividad);
    clearTimeout(timeoutAlerta);
    alertaMostrada = false;
    tiempoespera = 300;
    tiempoInactividad = setTimeout(notificarSesionPorExpirar, tiempoMaxInactividad);
}
 
async function renovarSesion() {
    try {
        const response = await fetch('_renovar_sesion_copia.php', {
            method: 'GET',
            credentials: 'same-origin'
        });
        const data = await response.json();
 
        if (data.status === 'OK') {
            const access_text = document.getElementById('last_access');
            if (access_text) access_text.innerText = data.nuevo_ultimo_acceso;
 
            reiniciarTemporizador();
 
            Swal.fire({
                title: 'Sesión Renovada',
                html: `<p>Tu sesión ha sido extendida.</p>
                        <p>Último acceso actualizado a <br><strong>${data.nuevo_ultimo_acceso}</strong></p>`,
                icon: 'success',
                confirmButtonText: 'Aceptar',
                customClass: { confirmButton: 'btn-confirm' },
                buttonsStyling: false
            });
        } else {
            Swal.fire('Sesión no activa', 'Debes iniciar sesión nuevamente', 'error')
                .then(() => window.location.href = 'index.php');
        }
    } catch (error) {
        console.error('Error al renovar la sesión:', error);
        Swal.fire('Error', 'No se pudo renovar la sesión. Intenta nuevamente.', 'error');
    }
}
 
// Inicia al cargar
reiniciarTemporizador();
 
// ---------------------
// GUARDAR FORMULARIO
// ---------------------
 
function guardarFormulario(form) {
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
 
    let fechaFin = new Date('1970-01-01T' + hora_fin);
    if (fechaFin < fechaInicio) {
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
        beforeSend: () => {
            $('#form_button').val('Guardando...');
            $('#form_button').prop('disabled', true);
        },
        success: (data) => {
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
        error: () => {
            console.error('Error al procesar el formulario.');
        },
        complete: () => {
            $('#form_button').val('Guardando...');
            $('#form_button').prop('disabled', true);
        }
    });
}

function iniciarContadorSesion() {
    const output = document.getElementById('tiempo_sesion');
    let accesoTexto = document.getElementById('last_access')?.innerText;
    if (!accesoTexto || !output) return;
 
    // Extraer solo la fecha y hora con una expresión regular
    const match = accesoTexto.match(/(\d{2}-\d{2}-\d{4} \d{2}:\d{2}:\d{2})/);
    if (!match) return;
 
    const acceso = match[1]; // Ej: "19-05-2025 15:00:00"
 
    const [fecha, hora] = acceso.split(' ');
    const [dia, mes, anio] = fecha.split('-');
    const [horaStr, minutoStr, segundoStr] = hora.split(':');
 
    const inicioSesion = new Date(anio, mes - 1, dia, horaStr, minutoStr, segundoStr);
 
    function actualizar() {
        const ahora = new Date();
        const diffMs = ahora - inicioSesion;
 
        const horas = Math.floor(diffMs / 3600000);
        const minutos = Math.floor((diffMs % 3600000) / 60000);
        const segundos = Math.floor((diffMs % 60000) / 1000);
 
        const tiempoFormateado = `${horas > 0 ? horas + 'h ' : ''}${minutos}m ${segundos}s`;
 
        output.innerText = `Tiempo Activo: ${tiempoFormateado}`;
        console.log(`Tiempo transcurrido: ${tiempoFormateado}`);
    }
 
    actualizar();
    setInterval(actualizar, 1000);
}
 
document.addEventListener('DOMContentLoaded', iniciarContadorSesion);

// Renueva la sesión manualmente
document.addEventListener('DOMContentLoaded', () => {
    // Botón Guardar del formulario 
    const form = document.querySelector('#form_registro_actividad_cdm');
    if(form) return;

    form.addEventListener('submit', function(e){
        e.preventDefault();

        fetch('_renovar_sesion_copia.php', {
            method: 'GET',
            credentials: 'include'
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'OK'){
                console.log(`Se actualiza la sesión desde el botón guardar del formulario, nueva sesión: ${data.nuevo_ultimo_acceso}`);
                guardarFormulario(form);
            } else {
                Swal.fire('Sesión no activa', 'Debes iniciar sesión nuevamente','error')
                    .then(() => window.location.href = 'index.php');
            }
        })
        .catch(error => {
            console.error('Error al renovar sesión: ', error);
            Swal.fire('Error', 'No se puede renovar la sesión. Intente nuevamente.', 'error');
        });
    })

    const contenedorBoton = document.querySelector('#contenedor-renovar-sesion');
    const botonRenovar = document.querySelector('#btn_renovar_sesion');
    const mensaje = document.querySelector('#mensaje_renovacion');

    // Muestra el botón cada 5 minutos 
    setInterval(() => {
        notificarSesionPorExpirar();
        contenedorBoton.classList.remove('d-none');
    }, 10 * 60 * 1000);

        botonRenovar.addEventListener('click', async () => {
            botonRenovar.disabled = true;
            botonRenovar.textContent = 'Renovando...';

            try {
                const response = await fetch('_renovar_sesion_copia.php', {
                    method: 'GET',
                    credentials: 'include'
                });

                const data = await response.json();

                if(data.status === 'OK'){
                    Swal.fire({
                        icon: 'success',
                        title: 'Sesión Renovada',
                        html: `<p>Último acceso actualizado a <br><strong>${data.nuevo_ultimo_acceso}</strong></p>`,
                        confirmButtonText: 'Aceptar'
                    })

                    console.log('Sesión renovada manualmente');
                    reiniciarTemporizador();

                    contenedorBoton.classList.add('d-none');

                    const access_text = document.querySelector('#last_access');

                    if(access_text){
                        access_text.innerText = data.nuevo_ultimo_acceso;
                    }

                    contadorRenovaciones++;
                    console.log(`Veces que se ha renoado la sesión: ${contadorRenovaciones}`);

                    // Mensaje temporal 
                    mensaje.classList.remove('d-none');
                    mensaje.classList.add('d-inline');

                    setTimeout(() => {
                        mensaje.classList.remove('d-inline');
                        mensaje.classList.add('d-none');
                    }, 5000);
                } else {
                    Swal.fire('Sesion no activa', 'Debes iniciar sesión nuevamente', 'error')
                        .then(() => window.location.href = 'index.php');
                }
            }catch(error){
                console.error('Error al renovar sesión manual: ', error);
                Swal.fire('Error', 'No se pudo renovar la sesión. Intenta nuevamente', 'error')
            }finally{
                botonRenovar.disabled = false;
                botonRenovar.textContent = 'Renovar sesión';
            }
        });
});

