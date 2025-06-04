function cargarTodo(){
  const fechaInicio = $('#fecha_inicio').val();
  const fechaFin = $('#fecha_fin').val();

  fetch(`reporte_comunicados_tablero_consulta.php?fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`)
  .then(res => res.json())
  .then(data => {
    console.log(data.comunicados);
    console.log(data.resumen_por_gerente_y_comunicado);

    llenarTablaComunicados(data.comunicados);
    llenarTablaResumenGerente(data.resumen_por_gerente_y_comunicado);
  })
  .catch(error => {
    console.error('Error al obtener los datos: ', error);
    alert('Error al cargar datos del tablero');
  })

}

function llenarTablaComunicados(datos){
  $('#tablaComunicados').DataTable({
    destroy: true,
    data: datos,
    columns: [
      { title: 'Título', data: 'TITULO' },
      { title: 'Fecha Creado', data: 'FECHACREADO' },
      { title: 'Leídos', data: 'TOTALLEIDOS' },
      { title: 'Pendientes', data: 'TOTALPENDIENTES' },
      { title: 'Total', data: 'TOTAL' },
      { title: '% Leídos', data: 'PROCENTAJELEIDO' },
      { title: '% Pendientes', data: 'PORCENTAJEPENDIENTE' }
    ],
    language: { url: 'datatables-es.json' }
  });
}

function llenarTablaResumenGerente(datos){
  $('#tablaGerentesComunicados').DataTable({
    destroy: true, 
    data: datos,
    columns: [
      { title: 'Gerente', data: 'RESPONSABLE' },
      { title: 'Título', data: 'TITULO' },
      { title: 'Fecha Creado', data: 'FECHA_CREADO' },
      { title: 'Leídos', data: 'TOTAL_LEIDOS' },
      { title: 'Pendientes', data: 'TOTAL_PENDIENTES' },
      { title: 'Total', data: 'TOTAL' },
      { title: '% Leído', data: 'PORCENTAJE_LEIDO' },
      { title: '% Pendiente', data: 'PORCENTAJE_PENDIENTE' }
    ],
    language: { url: 'datatables-es.json' }
  });
}

$(document).ready(function(){
  cargarTodo();

  $('#fecha_inicio', '#fecha_fin').on('change', function(){
    cargarTodo();
  });
  
})

#########################################################################################################

Gráficas

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

  Highcharts.chart('graficaResumenGerentes', {
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

