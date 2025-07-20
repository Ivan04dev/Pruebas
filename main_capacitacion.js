console.log("Desde main.js");

// Variable global
let videos = [];
const usuario = document.querySelector("#usuario")?.value || "";
const videosContainer = document.querySelector("#video-principal");
console.log("Usuario:", usuario);

document.addEventListener("DOMContentLoaded", async () => {
  if (!usuario) {
    console.error("No se encontró el usuario.");
    return;
  }

  try {
    const res = await fetch(`consultar_videos.php?usuario=${usuario}`);
    videos = await res.json();
    console.log("Videos:", videos);

    if (!Array.isArray(videos)) throw new Error("Error al obtener vídeos.");

    renderizarPendientes(videos.filter((v) => v.estado === "pendiente"));
    renderizarVistos(videos.filter((v) => v.estado === "visto"));
  } catch (err) {
    console.error(err);
  }
});

function renderizarPendientes(lista) {
  const contenedor = document.querySelector("#contenedor-videos");
  contenedor.innerHTML = "";

  if (lista.length === 0) {
    contenedor.innerHTML = `<div class="alert alert-success">No hay vídeos pendientes</div>`;
    return;
  }

  let fila;
  lista.forEach((video, i) => {
    if (i % 3 === 0) {
      fila = document.createElement("div");
      fila.className = "row mb-3";
      contenedor.appendChild(fila);
    }

    const col = document.createElement("div");
    col.className = "col-md-4";

    const card = document.createElement("div");
    card.className = "card card-clickable h-100";
    card.innerHTML = `
            <video src="${video.archivo}" muted playsinline currentTime="1" class="video-miniatura"></video>
            <div class="card-body p-2">
                <h6 class="card-title">${video.titulo}</h6>
                <p class="small text-muted">${video.subtitulo}</p>
                <p class="small text-muted">Duración: ${video.duracion}</p>
            </div>
        `;

    card.addEventListener("click", () => mostrarVideo(video));

    col.appendChild(card);
    fila.appendChild(col);
  });
}

function renderizarVistos(lista) {
  const contenedor = document.querySelector("#videos-vistos");
  const containerWrapper = document.querySelector("#videos-vistos-container");
  contenedor.innerHTML = "";

  if (lista.length === 0) return;

  containerWrapper.classList.remove("d-none");

  lista.forEach((video) => {
    const col = document.createElement("div");
    col.className = "col-12";

    const reaccionIcon = convertirReaccion(video.reaccion);

    const card = document.createElement("div");
    card.className = "card card-clickable";
    card.innerHTML = `
            <video src="${video.archivo}" muted playsinline class="video-miniatura"></video>
            <div class="card-body p-2">
                <h6 class="card-title">${video.titulo}</h6>
                <p class="small text-muted">${video.subtitulo}</p>
                <p class="small text-muted">Reacción: ${reaccionIcon}</p>
                <p class="small text-muted">Veces visto: ${video.veces_visto}</p>
            </div>
        `;

    card.addEventListener("click", () => mostrarVideo(video));
    col.appendChild(card);
    contenedor.appendChild(col);
  });
}

function mostrarVideo(video) {
  let tiempoInicio = null;
  const contenedorGrid = document.querySelector("#contenedor-videos");
  contenedorGrid.classList.add("d-none");
  videosContainer.innerHTML = "";

  const { titulo, subtitulo, archivo, duracion } = video;

  const videoEl = document.createElement("video");
  videoEl.src = archivo;
  videoEl.controls = true;
  videoEl.preload = "auto";
  videoEl.id = "videoActual";
  videoEl.controlsList = "nodownload";

  const infoDiv = document.createElement("div");
  infoDiv.className = "video-meta";
  infoDiv.innerHTML = `
        <h4 class="mb-1">${titulo}</h4>
        <p class="mb-1">${subtitulo}</p>
        <span class="badge bg-primary">Duración: ${duracion}</span>
    `;

  const barraProgreso = document.createElement("div");
  barraProgreso.className = "barra-progreso";
  const progresoInterno = document.createElement("div");
  progresoInterno.className = "barra-progreso-interno";
  barraProgreso.appendChild(progresoInterno);

  videoEl.addEventListener("play", () => {
    if (!tiempoInicio) tiempoInicio = Date.now();
  });

  videoEl.addEventListener("timeupdate", () => {
    const porcentaje = (videoEl.currentTime / videoEl.duration) * 100;
    progresoInterno.style.width = `${porcentaje}%`;
  });

  videoEl.addEventListener("ended", async () => {
    const tiempoFin = Date.now();
    const tiempoEnMin = ((tiempoFin - tiempoInicio) / 60000).toFixed(2);

    const yaVisto = video.estado === "visto";

    if (!yaVisto) {
      await calificarVideo(video.id_video, video.titulo, tiempoEnMin, usuario);
    } else {
      await actualizarVisualizacion(video.id_video, tiempoEnMin, usuario);
    }

    video.estado = "visto";
    video.veces_visto = (parseInt(video.veces_visto) || 0) + 1;

    videosContainer.innerHTML = "";
    renderizarPendientes(videos.filter((v) => v.estado === "pendiente"));
    renderizarVistos(videos.filter((v) => v.estado === "visto"));
  });

  videosContainer.appendChild(videoEl);
  videosContainer.appendChild(barraProgreso);
  videosContainer.appendChild(infoDiv);
}

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
              const response = await fetch('actualizaciones_dos.php', {
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

async function actualizarVisualizacion(id_video, tiempo_min, usuario) {
  try {
    await fetch("actualizaciones.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ usuario, id_video, tiempo_min, reaccion: null }),
    });
  } catch (error) {
    console.error("Error al actualizar visualización:", error);
  }
}

function convertirReaccion(reaccion) {
  switch (parseInt(reaccion)) {
    case 1:
      return '<i class="color-1 bi bi-emoji-angry fs-3"></i>';
    case 2:
      return '<i class="color-2 bi bi-emoji-frown fs-3"></i>';
    case 3:
      return '<i class="color-3 bi bi-emoji-neutral fs-3"></i>';
    case 4:
      return '<i class="color-4 bi bi-emoji-smile fs-3"></i>';
    case 5:
      return '<i class="color-5 bi bi-emoji-laughing fs-3"></i>';
    default:
      return "";
  }
}

