function obtenerFechasIniciales() {
  const hoy = new Date();
  const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
  console.log(primerDia);
  console.log(hoy);
  const formatoFecha = (fecha) => fecha.toISOString().split('T')[0];

  $('#fecha_inicio').val(formatoFecha(primerDia));
  $('#fecha_fin').val(formatoFecha(hoy));
}

function obtenerColorPorPorcentaje(porcentaje) {
  const valor = parseFloat(porcentaje);

  if (valor >= 90) return 'bg-verde'; // Verde
  if (valor >= 80) return 'bg-amarillo'; // Amarillo
  return 'bg-rojo'; // Rojo
}

/*
function cargarTodo() {
  const fechaInicio = $('#fecha_inicio').val();
  const fechaFin = $('#fecha_fin').val();

  mostrarLoaders();

  console.log('Fechas enviadas:', fechaInicio, fechaFin);

  fetch(`reporte_comunicados_tablero_consulta.php?fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`)
    .then(res => res.json())
    .then(data => {
      console.log('Comunicados: ', data.comunicados);
      console.log('Regiones: ', data.regiones);
      console.log('Comunicados por gerente: ', data.resumen_por_gerente_y_comunicado);
      console.log('Comunicados por semana: ', data.comunicados_semana);

      llenarTablaComunicados(data.comunicados);
      llenarTablaRegiones(data.regiones);
      llenarTablaResumenGerente(data.resumen_por_gerente_y_comunicado);

      graficarComunicados(data.comunicados);
      graficarComunicadosDia2(data.comunicados_semana);
      graficarComunicadosDia4(data.comunicados_semana);
      graficarRegiones(data.regiones);
      graficarResumenGerentes(data.resumen_por_gerente_y_comunicado);

      ocultarLoaders();
    })
    .catch(error => {
      console.error('Error al obtener los datos: ', error);
      alert('Error al cargar datos del tablero');
      ocultarLoaders();
    })
}
*/

function cargarTodo() {
  const fechaInicio = $('#fecha_inicio').val();
  const fechaFin = $('#fecha_fin').val();
 
  mostrarLoaders();
 
  console.log('Fechas enviadas:', fechaInicio, fechaFin);
 
  fetch(`reporte_comunicados_tablero_consulta.php?fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`)
    .then(res => res.json())
    .then(data => {
      console.log('Comunicados: ', data.comunicados);
      console.log('Regiones: ', data.regiones);
      console.log('Comunicados por gerente: ', data.resumen_por_gerente_y_comunicado);
      console.log('Comunicados por semana: ', data.comunicados_semana);
 
      // Tablas
      llenarTablaComunicados(data.comunicados);
      llenarTablaRegiones(data.regiones);
      llenarTablaResumenGerente(data.resumen_por_gerente_y_comunicado);
 
      // Gráficas principales
      graficarComunicados(data.comunicados);
      graficarRegiones(data.regiones);
      graficarResumenGerentes(data.resumen_por_gerente_y_comunicado);
 
      // Gráficas Día 2 y Día 4 (avances por día desde creación)
      if (data.comunicados_semana && Array.isArray(data.comunicados_semana)) {
        graficarComunicadosDia2(data.comunicados_semana);
        graficarComunicadosDia4(data.comunicados_semana);
      } else {
        $('#contenedorGraficaDia2').addClass('d-none');
        $('#contenedorGraficaDia4').addClass('d-none');
      }
 
      ocultarLoaders();
    })
    .catch(error => {
      console.error('Error al obtener los datos: ', error);
      alert('Error al cargar datos del tablero');
      ocultarLoaders();
      $('#contenedorGraficaDia2').addClass('d-none');
      $('#contenedorGraficaDia4').addClass('d-none');
    });
}

function llenarTablaComunicados(datos) {
  if (!datos || datos.length === 0) {
    // mostrarMensajeVacio('mensajeVacioComunicados');
    ocultarSeccion('contenedorComunicados', 'mensajeVacioComunicados');
    $('#tablaComunicados').DataTable().clear().draw();
    return;
  }

  mostrarSeccion('contenedorComunicados', 'mensajeVacioComunicados');

  $('#tablaComunicados').DataTable({
    destroy: true,
    dom: 'Bfrtip',
    buttons: [
      {
        extend: 'excelHtml5',
        title: 'Comunicados',
        text: 'Exportar a Excel',
        className: 'd-none'
      }
    ],
    data: datos,
    columns: [
      { title: 'Título', data: 'TITULO' },
      { title: 'Fecha Creado', data: 'FECHACREADO' },
      { title: 'Leídos', data: 'TOTALLEIDOS' },
      { title: 'Pendientes', data: 'TOTALPENDIENTES' },
      { title: 'Total', data: 'TOTAL' },
      {
        title: '% Leídos',
        data: 'PORCENTAJELEIDO',
        render: function (data) {
          const clase = obtenerColorPorPorcentaje(data);
          return `<span class='${clase} p-2 text-dark fw-bold'>${data}%</span>`;
        }
      },
      { title: '% Pendientes', data: 'PORCENTAJEPENDIENTE' }
    ],
    language: { url: 'datatables-es.json' }
  });

  $('#btnExportarComunicados').off('click').on('click', function () {
    $('#tablaComunicados').DataTable().button('.buttons-excel').trigger();
  });

}

function llenarTablaRegiones(datos) {
  if (!datos || datos.length === 0) {
    // mostrarMensajeVacio('mensajeVacioRegiones');
    ocultarSeccion('contenedorRegiones', 'mensajeVacioRegiones');
    $('#tablaRegiones').DataTable().clear().draw();
    return;
  }

  // ocultarMensajeVacio('mensajeVacioRegiones');
  mostrarSeccion('contenedorRegiones', 'mensajeVacioRegiones');

  $('#tablaRegiones').DataTable({
    destroy: true,
    dom: 'Bfrtip',
    buttons: [
      {
        extend: 'excelHtml5',
        title: 'Comunicados por Región',
        text: 'Exportar a Excel',
        className: 'd-none'
      }
    ],
    data: datos,
    columns: [
      { title: 'Región', data: 'REGION' },
      { title: 'Leídos', data: 'TOTAL_LEIDOS' },
      { title: 'Pendientes', data: 'TOTAL_PENDIENTES' },
      { title: 'Total', data: 'TOTAL' },
      {
        title: '% Leídos',
        data: 'PORCENTAJE_LEIDO',
        render: function (data) {
          const clase = obtenerColorPorPorcentaje(data);
          return `<div class='${clase} p-2 text-dark fw-bold'>${data}%</div>`;
        }
      },
      { title: '% Pendientes', data: 'PORCENTAJE_PENDIENTE' }
    ],

    language: { url: 'datatables-es.json' }
  });

  $('#btnExportarRegiones').off('click').on('click', function () {
    $('#tablaRegiones').DataTable().button('.buttons-excel').trigger();
  });
}

function llenarTablaResumenGerente(datos) {
  if (!datos || datos.length === 0) {
    // mostrarMensajeVacio('mensajeVacioGerentes');
    ocultarSeccion('contenedorGerentesComunicados', 'mensajeVacioGerentes');
    $('#tablaGerentesComunicados').DataTable().clear().draw();
    return;
  }

  // ocultarMensajeVacio('mensajeVacioGerentes');
  mostrarSeccion('contenedorGerentesComunicados', 'mensajeVacioGerentes');

  $('#tablaGerentesComunicados').DataTable({
    destroy: true,
    dom: 'Bfrtip',
    buttons: [
      {
        extend: 'excelHtml5',
        title: 'Comunicados por Gerente',
        text: 'Exportar a Excel',
        className: 'd-none'
      }
    ],
    data: datos,
    columns: [
      { title: 'Gerente', data: 'RESPONSABLE' },
      { title: 'Título', data: 'TITULO' },
      { title: 'Fecha Creado', data: 'FECHA_CREADO' },
      { title: 'Leídos', data: 'TOTAL_LEIDOS' },
      { title: 'Pendientes', data: 'TOTAL_PENDIENTES' },
      { title: 'Total', data: 'TOTAL' },
      {
        title: '% Leído',
        data: 'PORCENTAJE_LEIDO',
        render: function (data) {
          const clase = obtenerColorPorPorcentaje(data);
          return `<span class='${clase} p-2 text-dark fw-bold'>${data}%</span>`;
        }
      },
      { title: '% Pendiente', data: 'PORCENTAJE_PENDIENTE' }
    ],

    language: { url: 'datatables-es.json' }
  });

  $('#btnExportarGerentes').off('click').on('click', function () {
    $('#tablaGerentesComunicados').DataTable().button('.buttons-excel').trigger();
  });
}

function graficarComunicados(datos) {
  const titulos = datos.map(d => d.TITULO);
  const leidos = datos.map(d => parseInt(d.TOTALLEIDOS) || 0);
  const pendientes = datos.map(d => parseInt(d.TOTALPENDIENTES) || 0);

  Highcharts.chart('graficaComunicados', {
    chart: { type: 'column' },
    title: { text: 'Lectura de Comunicados' },
    xAxis: {
      categories: titulos,
      title: { text: 'Título del Comunicado' },
      labels: { rotation: -45 }
    },
    yAxis: {
      min: 0,
      title: { text: 'Cantidad' }
    },
    tooltip: { shared: true },
    plotOptions: {
      column: {
        stacking: 'normal',
        dataLabels: { enabled: true }
      }
    },
    series: [
      { name: 'Leídos', data: leidos, color: '#28A745' },
      { name: 'Pendientes', data: pendientes, color: '#DC3545' }
    ],
    credits: { enabled: false }
  });
}

function graficarRegiones(datos) {
  if (!datos || datos.length === 0) {
    console.warn('No hay datos para graficar regiones.');
    return;
  }

  const regiones = datos.map(d => d.REGION);
  const leidos = datos.map(d => parseInt(d.TOTAL_LEIDOS) || 0);
  const pendientes = datos.map(d => parseInt(d.TOTAL_PENDIENTES) || 0);

  Highcharts.chart('graficaRegiones', {
    chart: { type: 'column' },
    title: { text: 'Lectura de comunicados por región' },
    xAxis: {
      categories: regiones,
      title: { text: 'Región' },
      labels: { rotation: -45 }
    },
    yAxis: {
      min: 0,
      title: { text: 'Cantidad de comunicados' }
    },
    tooltip: {
      shared: true,
      valueSiffix: ' comunicados'
    },
    plotOptions: {
      column: {
        stacking: 'normal',
        dataLabels: {
          enabled: true
        }
      }
    },
    series: [
      {
        name: 'Leídos',
        data: leidos,
        color: '#28A745'
      },
      {
        name: 'Pendientes',
        data: pendientes,
        color: '#CD3545'
      }
    ],
    credits: { enabled: false }
  });
}

function graficarResumenGerentes(datos) {
  const titulos = [...new Set(datos.map(d => d.TITULO))];

  const gerentes = {};
  datos.forEach(d => {
    const gerente = d.RESPONSABLE;
    const titulo = d.TITULO;
    const index = titulos.indexOf(titulo);

    if (!gerentes[gerente]) {
      gerentes[gerente] = Array(titulos.length).fill(0);
    }

    gerentes[gerente][index] = parseInt(d.TOTAL_LEIDOS) || 0;
  });

  const series = Object.entries(gerentes).map(([gerente, valores]) => ({
    name: gerente,
    data: valores
  }));

  Highcharts.chart('graficaGerentesComunicados', {
    chart: { type: 'column' },
    title: { text: 'Lectura por Gerente y Comunicado' },
    xAxis: {
      categories: titulos,
      title: { text: 'Comunicado' },
      labels: { rotation: -45 }
    },
    yAxis: {
      min: 0,
      title: { text: 'Total Leídos' }
    },
    tooltip: { shared: true },
    plotOptions: {
      column: { dataLabels: { enabled: true } }
    },
    series,
    credits: { enabled: false }
  });
}

function mostrarLoaders() {
  $('#loaderComunicados').removeClass('d-none');
  $('#loaderRegiones').removeClass('d-none');
  $('#loaderGerentes').removeClass('d-none');
}

function ocultarLoaders() {
  $('#loaderComunicados').addClass('d-none');
  $('#loaderRegiones').addClass('d-none');
  $('#loaderGerentes').addClass('d-none');
}

function mostrarMensajeVacio(idMensaje) {
  $(`#${idMensaje}`).removeClass('d-none')
}

function ocultarMensajeVacio(idMensaje) {
  $(`#${idMensaje}`).addClass('d-none')
}

function mostrarSeccion(idContenedor, idMensaje) {
  $(`#${idMensaje}`).addClass('d-none');
  $(`#${idContenedor}`).removeClass('d-none');
}

function ocultarSeccion(idContenedor, idMensaje) {
  $(`#${idContenedor}`).addClass('d-none');
  $(`#${idMensaje}`).removeClass('d-none');
}

// ========================================================================================================================================

function determinarDia(fechaCreado) {
  const hoy = new Date();
  const creado = new Date(fechaCreado);
  const diffDias = Math.floor((hoy - creado) / (1000 * 60 * 60 * 24));
  return diffDias;
}

// const dia2 = datos.filter(d => determinarDia(d.FECHACREADO) === 1);
// const dia4 = datos.filter(d => determinarDia(d.FECHACREADO) === 3);

function graficarComunicadosDia2(datos) {
  const dia2 = datos.filter(d => determinarDia(d.FECHACREADO) === 1);
 
  if (dia2.length === 0) {
    $('#contenedorGraficaDia2').addClass('d-none');
    return;
  }
 
  $('#contenedorGraficaDia2').removeClass('d-none');
 
  Highcharts.chart('graficaDia2', {
    chart: { type: 'column' },
    title: { text: 'Avance Lectura - Día 2' },
    xAxis: {
      categories: dia2.map(d => d.TITULO),
      title: { text: 'Comunicado' },
      labels: { rotation: -45 }
    },
    yAxis: {
      min: 0,
      title: { text: 'Cantidad' }
    },
    tooltip: {
      shared: true,
      valueSuffix: ' comunicados'
    },
    plotOptions: {
      column: {
        stacking: 'normal',
        dataLabels: { enabled: true }
      }
    },
    series: [
      { name: 'Leídos', data: dia2.map(d => parseInt(d.LEIDOS)), color: '#28A745' },
      { name: 'Pendientes', data: dia2.map(d => parseInt(d.PENDIENTES)), color: '#DC3545' }
    ],
    credits: { enabled: false }
  });
}

function graficarComunicadosDia4(datos) {
  const dia4 = datos.filter(d => determinarDia(d.FECHACREADO) === 3);
 
  if (dia4.length === 0) {
    $('#contenedorGraficaDia4').addClass('d-none');
    return;
  }
 
  $('#contenedorGraficaDia4').removeClass('d-none');
 
  Highcharts.chart('graficaDia4', {
    chart: { type: 'column' },
    title: { text: 'Avance Lectura - Día 4' },
    xAxis: {
      categories: dia4.map(d => d.TITULO),
      title: { text: 'Comunicado' },
      labels: { rotation: -45 }
    },
    yAxis: {
      min: 0,
      title: { text: 'Cantidad' }
    },
    tooltip: {
      shared: true,
      valueSuffix: ' comunicados'
    },
    plotOptions: {
      column: {
        stacking: 'normal',
        dataLabels: { enabled: true }
      }
    },
    series: [
      { name: 'Leídos', data: dia4.map(d => parseInt(d.LEIDOS)), color: '#28A745' },
      { name: 'Pendientes', data: dia4.map(d => parseInt(d.PENDIENTES)), color: '#DC3545' }
    ],
    credits: { enabled: false }
  });
}

$(document).ready(function () {
  obtenerFechasIniciales();
  cargarTodo();

  $('#fecha_inicio, #fecha_fin').on('change', function () {
    cargarTodo();
  });

})