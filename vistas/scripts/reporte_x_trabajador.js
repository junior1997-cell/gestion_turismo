var tabla_cliente;
var tbl_cliente_x_cobrar;

//Función que se ejecuta al inicio
function init() {

  $("#bloc_Recurso").addClass("menu-open");

  $("#mRecurso").addClass("active");  

  $(".btn-guardar").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-cliente").submit(); } else { toastr_warning("Espera", "Procesando Datos", 3000); } });

  // ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════ 
  lista_select2("../ajax/reporte_x_trabajador.php?op=select2_filtro_trabajador", '#filtro_trabajador', null, '.charge_filtro_trabajador');
  lista_select2("../ajax/reporte_x_trabajador.php?op=select2_filtro_anio_pago", '#filtro_p_all_anio_pago', null, '.charge_filtro_p_all_anio_pago');
  lista_select2("../ajax/reporte_x_trabajador.php?op=select2_filtro_mes_pago", '#filtro_p_all_mes_pago', null, '.charge_filtro_p_all_mes_pago');
  lista_select2("../ajax/reporte_x_trabajador.php?op=select2_filtro_tipo_comprob", '#filtro_tipo_comprob', null, '.charge_filtro_tipo_comprob');

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
  $("#filtro_trabajador").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_p_all_anio_pago").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_p_all_mes_pago").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_p_all_es_cobro").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  
  $("#filtro_tipo_comprob").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

}

//Función Listar
function tabla_principal_cliente(filtro_trabajador, filtro_anio_pago, filtro_p_all_mes_pago, filtro_tipo_comprob,filtro_p_all_es_cobro) {

  tabla_cliente = $('#tabla-cliente').dataTable({
    lengthMenu: [[-1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200,]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-4'B><'col-md-2 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload px-2 btn btn-sm btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_cliente) { tabla_cliente.ajax.reload(null, false); } } },
      { extend: 'excel', exportOptions: { columns: [0,8,9,10,3,4,5,6,7], }, title: 'Lista de Cobros', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "px-2 btn btn-sm btn-outline-success btn-wave ", footer: true,  }, 
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "px-2 btn btn-sm btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/reporte_x_trabajador.php?op=tabla_principal_cliente&filtro_trabajador=${filtro_trabajador}&filtro_anio_pago=${filtro_anio_pago}&filtro_p_all_mes_pago=${filtro_p_all_mes_pago}&filtro_tipo_comprob=${filtro_tipo_comprob}&filtro_p_all_es_cobro=${filtro_p_all_es_cobro}`,
      type: "get",
      dataType: "json",
      error: function (e) {
        console.log(e.responseText); ver_errores(e);
      },
      complete: function () {
        $(".buttons-reload").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Recargar');
        $(".buttons-excel").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Excel');
        $(".buttons-colvis").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Columnas');
        $('[data-bs-toggle="tooltip"]').tooltip();
        $('#id_buscando_tabla').remove();
      },
      dataSrc: function (e) {
				if (e.status != true) {  ver_errores(e); }  return e.aaData;
			},
    },
    createdRow: function (row, data, ixdex) {
      // columna: Acciones
      if (data[0] != '') { $("td", row).eq(0).addClass("text-nowrap text-center"); }
      // columna: Cliente
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap text-center"); }
      // columna: Cliente
      if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap"); }
      // columna: Cliente
      if (data[3] != '') { $("td", row).eq(3).addClass("text-nowrap"); }
    },
    language: {
      lengthMenu: "_MENU_ ",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    footerCallback: function( tfoot, data, start, end, display ) {
      var api1 = this.api(); var total1 = api1.column( 4).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      $( api1.column( 4 ).footer() ).html( `<span class="float-start">S/</span> <span class="float-end">${formato_miles(total1)}</span> ` );       
    },
    "bDestroy": true,
    "iDisplayLength": 5,//Paginación
    "order": [[0, "asc"]], //Ordenar (columna,orden)
    columnDefs: [
      //{ targets: [5], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      { targets: [9,10,11], visible: false, searchable: false, },
      { targets: [4], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = ''; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-start">S/</span> <span class="float-end ${color} "> ${number} </span>`; } return number; }, },      

    ],
  }).DataTable();
}

//Función Listar
function tabla_cliente_x_cobrar(filtro_trabajador, filtro_anio_pago, filtro_p_all_mes_pago) {

  tbl_cliente_x_cobrar = $('#tabla-cliente_x_cobrar').dataTable({
    lengthMenu: [[-1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200,]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-4'B><'col-md-2 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload px-2 btn btn-sm btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tbl_cliente_x_cobrar) { tbl_cliente_x_cobrar.ajax.reload(null, false); } } },
      { extend: 'excel', exportOptions: { columns: [0,5,6,2,3,4], }, title: 'Lista de cobros Pendientes', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "px-2 btn btn-sm btn-outline-success btn-wave ", footer: true,  }, 
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "px-2 btn btn-sm btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/reporte_x_trabajador.php?op=tabla_cliente_x_c&filtro_trabajador=${filtro_trabajador}&filtro_anio_pago=${filtro_anio_pago}&filtro_p_all_mes_pago=${filtro_p_all_mes_pago}`,
      type: "get",
      dataType: "json",
      error: function (e) {
        console.log(e.responseText); ver_errores(e);
      },
      complete: function () {
        $(".buttons-reload").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Recargar');
        $(".buttons-excel").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Excel');
        $(".buttons-colvis").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Columnas');
        $('[data-bs-toggle="tooltip"]').tooltip();
        $('#id_busc_tbl_cobros_x_c').remove();
      },
      dataSrc: function (e) {
				if (e.status != true) {  ver_errores(e); }  return e.aaData;
			},
    },
    createdRow: function (row, data, ixdex) {
      // columna: Acciones
      if (data[0] != '') { $("td", row).eq(0).addClass("text-center"); }
      // columna: Acciones
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap"); }
      // columna: Cliente
      if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap"); }
    },
    language: {
      lengthMenu: "_MENU_ ",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    footerCallback: function( tfoot, data, start, end, display ) {
      var api1 = this.api(); var total1 = api1.column( 3 ).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      $( api1.column( 3 ).footer() ).html( `<span class="float-start">S/</span> <span class="float-end">${formato_miles(total1)}</span> ` );       
    },
    "bDestroy": true,
    "iDisplayLength": 5,//Paginación
    "order": [[0, "asc"]], //Ordenar (columna,orden)
    columnDefs: [
       { targets: [4], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      { targets: [5,6], visible: false, searchable: false, },
      { targets: [3], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = ''; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-start">S/</span> <span class="float-end ${color} "> ${number} </span>`; } return number; }, },      

    ],
  }).DataTable();
}

function calculando_totales_card_F_B_T(filtro_trabajador, filtro_anio_pago, filtro_p_all_mes_pago, filtro_tipo_comprob,filtro_p_all_es_cobro){
	$.post("../ajax/reporte_x_trabajador.php?op=totales_card_F_B_T", 
  { filtro_trabajador: filtro_trabajador,
    filtro_anio_pago:filtro_anio_pago,
    filtro_p_all_mes_pago:filtro_p_all_mes_pago,
    filtro_tipo_comprob:filtro_tipo_comprob,
    filtro_p_all_es_cobro:filtro_p_all_es_cobro
  }, function (e, status) {
		e = JSON.parse(e); //console.log(e.data);

   if (e.status == true) {

    $('.tiket_info').html(`${e.data['12_ticket']['nombre']} <span class="ms-1 badge bg-secondary-transparent" >${e.data['12_ticket']['cantidad']}</span>`);
    $('.total_tiket').html(`S/ ${e.data['12_ticket']['total']}`);
    /**-------------- */
    $('.boleta_info').html(`${e.data['03_boleta']['nombre']} <span class="ms-1 badge bg-secondary-transparent" >${e.data['03_boleta']['cantidad']}</span>`);
    $('.total_boleta').html(`S/ ${e.data['03_boleta']['total']}`);
    /**-------------- */
    $('.factura_info').html(`${e.data['01_factura']['nombre']} <span class="ms-1 badge bg-secondary-transparent" >${e.data['01_factura']['cantidad']}</span>`);
    $('.total_factura').html(`S/ ${e.data['01_factura']['total']}`);
    /**-------------- */
    $('.total_info').html(`${e.data['00_total']['nombre']} <span class="ms-1 badge bg-secondary-transparent">${e.data['00_total']['cantidad']}</span>`);
    $('.total_general').html(`S/ ${e.data['00_total']['total']}`);
    /**-------------- */
    $('.total_pendiente').html(`${e.data['04_pendiente']['nombre']} <span class="ms-1 badge bg-secondary-transparent">${e.data['04_pendiente']['cantidad']}</span>`);
    $('.total_g_pend').html(`S/ ${e.data['04_pendiente']['total']}`);

    } else { ver_errores(e); }

	}).fail( function(e) { ver_errores(e); } );
}
var chart; // Variable global para almacenar el gráfico
function calculando_totales_pay(filtro_trabajador, filtro_anio_pago, filtro_p_all_mes_pago, filtro_tipo_comprob,filtro_p_all_es_cobro){
	$.post("../ajax/reporte_x_trabajador.php?op=totales_pay", 
  { filtro_trabajador: filtro_trabajador,
    filtro_anio_pago:filtro_anio_pago,
    filtro_p_all_mes_pago:filtro_p_all_mes_pago,
    filtro_tipo_comprob:filtro_tipo_comprob,
    filtro_p_all_es_cobro:filtro_p_all_es_cobro
  }, function (e, status) {
		e = JSON.parse(e); //console.log(e.data.series); console.log(e.data.labels);

   if (e.status == true) {
     /* simple donut chart */
    var options = {
      series: e.data.series,
      chart: {
        type: "donut",
        height: 290,
      },
      legend: {
        position: "bottom",
      },
      colors: ["#e6533c","#845adf", "#23b7e5", "#f5b849", "#49b6f5" , "#4eac4c"],
      labels: e.data.labels,
      dataLabels: {
        dropShadow: {
          enabled: false,
        },
      },
    };
    /*var chart = new ApexCharts(document.querySelector("#donut-simple"), options);
    chart.render();*/
    if (chart) { chart.destroy(); }// Destruye el gráfico existente si hay uno
    
    chart = new ApexCharts(document.querySelector("#donut-simple"), options);
    chart.render();

    } else { ver_errores(e); }

	}).fail( function(e) { ver_errores(e); } );
}

function calculando_totales_producto(filtro_trabajador, filtro_anio_pago, filtro_p_all_mes_pago, filtro_tipo_comprob,filtro_p_all_es_cobro){

  $.getJSON("../ajax/reporte_x_trabajador.php?op=totales_x_producto", 
  { filtro_trabajador: filtro_trabajador,
    filtro_anio_pago:filtro_anio_pago,
    filtro_p_all_mes_pago:filtro_p_all_mes_pago,
    filtro_tipo_comprob:filtro_tipo_comprob,
    filtro_p_all_es_cobro:filtro_p_all_es_cobro 
  }, function (e, textStatus, jqXHR) {
    console.log(e);
    if (e.status == true) {
      var html_producto = "";
      var html_total_producto = 0;
      e.data.forEach((val, key) => {

        var html_user = "";
        val.user.forEach((val2, key2) => {
          var imagen_perfil = (val2.foto_perfil == "" || val2.foto_perfil == null ? 'no-perfil.jpg' :   val2.foto_perfil);
          html_user = html_user.concat(`<span class="avatar avatar-sm avatar-rounded" data-bs-toggle="tooltip" title="${val2.nombre_razonsocial}"><img src="../assets/modulo/persona/perfil/${imagen_perfil}" alt="img"></span>`);
        });

        html_producto = html_producto.concat(`<tr>
          <td class="text-center" ><span class="fs-11 text-muted">${key+1}</span> </td>  
          <td><span class="fs-11 text-muted">${val.nombre_producto}</span> </td>
          <td class="text-center"><span class="badge bg-primary-transparent">${ parseFloat(val.cantidad) }</span></td>
          <td class="text-right">${ formato_miles(val.subtotal)}</td>
          <td>
            <div class="avatar-list-stacked">
              ${html_user}
              <!-- <a class="avatar avatar-sm bg-primary text-fixed-white avatar-rounded" href="javascript:void(0);"> +5 </a> -->
            </div>
          </td>
        </tr>`);
        html_total_producto += parseFloat(val.subtotal);
      });

      $('#tabla-x-producto tbody').html(html_producto);
      $('.total_x_producto').html(formato_miles(html_total_producto));
      $('[data-bs-toggle="tooltip"]').tooltip();
    } else {
      ver_errores(e);
    }   
  }).fail( function(e) { ver_errores(e); } );	
}

$(document).ready(function () {
  init();
});

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..
function cargando_search() {
  if ($('#id_buscando_tabla').length) { } else {
    $('.buscando_tabla').prepend(`<tr id="id_buscando_tabla"> 
      <th colspan="20" class="bg-danger " style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
    </tr>`);
  } 
  
  if ($('#id_busc_tbl_cobros_x_c').length) { } else {
    $('.buscando_tabla_x_c').prepend(`<tr id="id_busc_tbl_cobros_x_c"> 
      <th colspan="20" class="bg-danger " style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
    </tr>`);
  } 
}


function filtros() {  

  var filtro_trabajador      = $("#filtro_trabajador").select2('val');
  var filtro_anio_pago     = $("#filtro_p_all_anio_pago").select2('val'); 
  var filtro_p_all_mes_pago  = $("#filtro_p_all_mes_pago").select2('val');
  var filtro_p_all_es_cobro  = $("#filtro_p_all_es_cobro").select2('val');
  var filtro_tipo_comprob     = $("#filtro_tipo_comprob").select2('val');
  
  
  if (filtro_trabajador == '' || filtro_trabajador == 0 || filtro_trabajador == null) { filtro_trabajador = ""; nombre_trabajador = ""; }       // filtro de trabajador  
  if (filtro_anio_pago == '' || filtro_anio_pago == 0 || filtro_anio_pago == null) { filtro_anio_pago = ""; nombre_anio_pago = ""; }                 // filtro de dia pago  
  if (filtro_p_all_mes_pago == '' || filtro_p_all_mes_pago == 0 || filtro_p_all_mes_pago == null) { filtro_p_all_mes_pago = ""; nombre_mes_pago = ""; }                                     // filtro de plan
  if (filtro_p_all_es_cobro == '' || filtro_p_all_es_cobro == 0 || filtro_p_all_es_cobro == null) { filtro_p_all_es_cobro = ""; nombre_es_pago = ""; }                                     // filtro de plan
  if (filtro_tipo_comprob == '' || filtro_tipo_comprob == 0 || filtro_tipo_comprob == null) { filtro_tipo_comprob = ""; nombre_tipo_comprob = ""; }  // filtro de zona antena

  $('#id_buscando_tabla').html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${nombre_trabajador} ${nombre_anio_pago} ${nombre_mes_pago}...`);
  $('#id_busc_tbl_cobros_x_c').html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${nombre_trabajador} ${nombre_anio_pago} ${nombre_mes_pago}...`);
  //console.log(filtro_categoria, fecha_2, filtro_p_all_mes_pago, comprobante);

  tabla_principal_cliente(filtro_trabajador, filtro_anio_pago, filtro_p_all_mes_pago, filtro_tipo_comprob, filtro_p_all_es_cobro);
  calculando_totales_card_F_B_T(filtro_trabajador, filtro_anio_pago, filtro_p_all_mes_pago, filtro_tipo_comprob, filtro_p_all_es_cobro);
  calculando_totales_pay(filtro_trabajador, filtro_anio_pago, filtro_p_all_mes_pago, filtro_tipo_comprob, filtro_p_all_es_cobro);
  calculando_totales_producto(filtro_trabajador, filtro_anio_pago, filtro_p_all_mes_pago, filtro_tipo_comprob, filtro_p_all_es_cobro);
  

  if (filtro_trabajador != "" && filtro_anio_pago != "" && filtro_p_all_mes_pago != "") {
    
    tabla_cliente_x_cobrar(filtro_trabajador, filtro_anio_pago, filtro_p_all_mes_pago, filtro_p_all_es_cobro);
    $(".div_tbl_cxt").show(); $(".div_alert_c_t").hide();

  } else {  
    $(".div_tbl_cxt").hide(); $(".div_alert_c_t").show();
  }

}

function reload_select(r_text) {

  switch (r_text) {
    case 'filtro_trabajador':
      lista_select2("../ajax/reporte_x_trabajador.php?op=select2_filtro_trabajador", '#filtro_trabajador',     null, '.charge_filtro_trabajador');
    break; 
    case 'filtro_anio_pago':
      lista_select2("../ajax/reporte_x_trabajador.php?op=select2_filtro_anio_pago",  '#filtro_p_all_anio_pago',  moment().format('YYYY'), '.charge_filtro_p_all_anio_pago');
    break;    
    case 'filtro_p_all_mes_pago':
      lista_select2("../ajax/reporte_x_trabajador.php?op=select2_filtro_p_all_mes_pago", '#filtro_p_all_mes_pago', null, '.charge_filtro_p_all_mes_pago');
    break;
    case 'filtro_tipo_comprob':
      lista_select2("../ajax/reporte_x_trabajador.php?op=select2_filtro_tipo_comprob",'#filtro_tipo_comprob', null, '.charge_filtro_tipo_comprob');
    break;   
  

    default:
      console.log('Caso no encontrado.');
  }
 
}
