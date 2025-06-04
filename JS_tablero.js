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
