document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.getElementById('formulario-video');

    formulario.addEventListener('submit', async (e) => {
        e.preventDefault();

        const titulo = document.getElementById('titulo').value.trim();
        const subtitulo = document.getElementById('subtitulo').value.trim();
        const archivo = document.getElementById('archivo').value.trim();
        const duracion = parseFloat(document.getElementById('duracion_min').value);

        // Validación rápida
        if (!titulo || !archivo || isNaN(duracion)) {
            alert('Por favor, completa todos los campos obligatorios correctamente.');
            return;
        }

        const datos = {
            titulo,
            subtitulo,
            archivo,
            duracion_min: duracion
        };

        try {
            const respuesta = await fetch('insertar_video.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            });

            const texto = await respuesta.text();
            console.log('Respuesta cruda del servidor:', texto);

            let resultado;
            try {
                resultado = JSON.parse(texto);
            } catch (err) {
                throw new Error('El servidor no devolvió un JSON válido.');
            }

            if (resultado.status === 'ok') {
                alert('Vídeo registrado correctamente.');
                formulario.reset();
            } else {
                alert('Error: ' + resultado.mensaje);
            }

        } catch (error) {
            console.error('Error al enviar el formulario:', error);
            alert('Hubo un problema al registrar el vídeo.');
        }
    });
});
