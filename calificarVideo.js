function calificarVideo(id_video, titulo, tiempoEnMin, usuario) {
    return new Promise((resolve) => {
        Swal.fire({
            title: '¿Cómo calificarías este vídeo?',
            html: `
                <div class="d-flex justify-content-between align-items-center mt-3 gap-2">
                    <button class="btn btn-success reaccion-btn" data-reaccion="5"><i class="bi bi-emoji-laughing fs-3"></i></button>
                    <button class="btn btn-success reaccion-btn" data-reaccion="4"><i class="bi bi-emoji-smile fs-3"></i></button>
                    <button class="btn btn-warning reaccion-btn" data-reaccion="3"><i class="bi bi-emoji-neutral fs-3"></i></button>
                    <button class="btn btn-danger reaccion-btn" data-reaccion="2"><i class="bi bi-emoji-frown fs-3"></i></button>
                    <button class="btn btn-danger reaccion-btn" data-reaccion="1"><i class="bi bi-emoji-angry fs-3"></i></button>
                </div>`,
            showConfirmButton: false,
            allowOutsideClick: false,
            customClass: { popup: 'rounded-4 p-4' },
            didOpen: () => {
                document.querySelectorAll('.reaccion-btn').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const reaccion = parseInt(btn.dataset.reaccion, 10);

                        try {
                            const response = await fetch('actualizaciones.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    usuario: usuario,
                                    id_video: id_video,
                                    tiempo_min: tiempoEnMin,
                                    reaccion: reaccion
                                })
                            });

                            const text = await response.text();
                            console.log('Respuesta cruda del servidor:', text);

                            let result;
                            try {
                                result = JSON.parse(text);
                            } catch (e) {
                                throw new Error('El servidor no devolvió un JSON válido');
                            }

                            if (result.status === 'ok') {
                                // Actualizar estado local del vídeo
                                const videoActualizado = window.videos?.find(v => v.id_video == id_video);
                                if (videoActualizado) {
                                    videoActualizado.estado = 'visto';
                                    videoActualizado.reaccion = reaccion;
                                    videoActualizado.veces_visto = 1;
                                }

                                // Renderizar listas actualizadas
                                const pendientes = window.videos.filter(v => v.estado === 'pendiente');
                                const vistos = window.videos.filter(v => v.estado === 'visto');
                                renderizarPendientes(pendientes);
                                renderizarVistos(vistos);

                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Gracias!',
                                    text: 'Tu opinión ha sido registrada.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => resolve());

                            } else {
                                throw new Error(result.error || 'Error inesperado del servidor');
                            }

                        } catch (err) {
                            Swal.fire('Error', err.message, 'error');
                            resolve();
                        }
                    });
                });
            }
        });
    });
}
