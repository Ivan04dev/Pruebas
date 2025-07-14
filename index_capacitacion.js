// localStorage.clear();

console.log('Desde index_.js');

let usuario = 'usuario_1';
let tiempoInicioPagina = Date.now();
let tiempoInicioVideo = null;
let tiempoFin = null;
// Variable temporal 
let tiempoEnMin = 2;
// Dentro de DOMContentLoaded
const videosContainer = document.querySelector('#video-principal');
const listaVistos = document.querySelector('#videos-vistos');
const contenedorVistos = document.querySelector('#videos-vistos-container');

let videosVistos = JSON.parse(localStorage.getItem("videos_vistos") || "[]");
let videosReproducidos = JSON.parse(localStorage.getItem("videos_reproducidos") || "{}");

let videos = [];

window.addEventListener('load', () => {
    const ahora = new Date();
    const horaFormateada = ahora.toLocaleTimeString();
    console.log(horaFormateada);
    // document.querySelector('#tiempo_inicio').textContent = horaFormateada;
});

document.addEventListener('DOMContentLoaded', async () => {

    try {
        const response = await fetch('videos.json');
        videos = await response.json();
    } catch (error) {
        console.error('Error al cargar los vídeos:', error);
        videosContainer.innerHTML = `<p class="text-danger">No fue posible cargar el contenido.</p>`;
        return;
    }

    if (videosVistos.length > 0) {
        contenedorVistos.classList.remove('d-none');

        videos.forEach(video => {
            if (videosVistos.includes(video.titulo)) {
                const col = document.createElement('div');
                col.className = 'col-12';

                const card = document.createElement('div');
                card.className = 'card';
                card.style.cursor = 'pointer';
                card.setAttribute('data-titulo', video.titulo);

                const miniatura = document.createElement('video');
                miniatura.src = video.archivo;
                miniatura.muted = true;
                miniatura.playsInline = true;
                miniatura.style.width = '100%';
                miniatura.style.borderRadius = '0.25rem';
                miniatura.style.pointerEvents = 'none';
                miniatura.currentTime = 1;

                const body = document.createElement('div');
                body.className = 'card-body p-2';
                body.innerHTML = `
                    <h6 class="card-title mb-1">${video.titulo}</h6>
                    <p class="card-text small text-muted mb-0">${video.subtitulo}</p>
                    <p class="card-text small text-muted mb-0">Veces visto: ${videosReproducidos[video.titulo] || 1}</p>
                    <p class="card-text small text-muted mb-0">Fecha: ${videosReproducidos[video.fecha] || 1}</p>
                `;

                card.appendChild(miniatura);
                card.appendChild(body);
                col.appendChild(card);

                card.addEventListener('click', () => {
                    mostrarVideo(video);
                });

                listaVistos.appendChild(col);
            }
        });
    }

    const progresoGuardado = JSON.parse(localStorage.getItem("ultimo_progreso"));
    if (progresoGuardado) {
        const videoGuardado = videos.find(v => v.titulo === progresoGuardado.titulo);
        if (videoGuardado) {
            mostrarVideo(videoGuardado, progresoGuardado);
        }
    }

    const contenedorGrid = document.querySelector('#contenedor-videos');
    crearGrid();
    const videosNoVistos = videos.filter(v => !videosVistos.includes(v.titulo));

    if (videosNoVistos.length > 0) {
        for (let i = 0; i < videosNoVistos.length; i += 3) {
            const fila = document.createElement('div');
            fila.className = 'row mt-4';

            const grupo = videosNoVistos.slice(i, i + 3);

            grupo.forEach(video => {
                const col = document.createElement('div');
                col.className = 'col-md-4';

                const card = document.createElement('div');
                card.className = 'card h-100';
                card.style.cursor = 'pointer';
                card.setAttribute('data-titulo', video.titulo);

                const miniatura = document.createElement('video');
                miniatura.src = video.archivo;
                miniatura.muted = true;
                miniatura.playsInline = true;
                miniatura.style.width = '100%';
                miniatura.style.borderRadius = '0.25rem';
                miniatura.style.pointerEvents = 'none';
                miniatura.currentTime = 1;

                const body = document.createElement('div');
                body.className = 'card-body p-2';
                body.innerHTML = `
                    <h6 class="card-title mb-1">${video.titulo}</h6>
                    <p class="card-text small text-muted mb-0">${video.subtitulo}</p>
                    <span class="badge bg-primary mt-2">Duración: ${video.duracion}</span>
                `;

                card.appendChild(miniatura);
                card.appendChild(body);
                col.appendChild(card);

                card.addEventListener('click', () => {
                    contenedorGrid.classList.add('d-none');
                    mostrarVideo(video);
                });

                fila.appendChild(col);
            });

            contenedorGrid.appendChild(fila);
        }
    } else {
        videosContainer.innerHTML = `
            <div class="alert alert-info text-center" role="alert">
                <h5 class="mb-2">No hay vídeos nuevos por ver</h5>
                <p class="mb-0">Actualmente no se han subido nuevos vídeos de capacitación.</p>
            </div>
        `;
    }
});
