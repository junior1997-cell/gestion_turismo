var tabla_productos;

function init(){

  // listar_tabla();

  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $(".btn-guardar").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-producto").submit(); }  });

	$("#guardar_registro_categoria").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-categoria").submit(); } });
	$("#guardar_registro_marca").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-marca").submit(); } });
	$("#guardar_registro_u_m").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-u-m").submit(); } });

  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_select2("../ajax/producto.php?op=select_categoria", '#categoria', null);
  lista_select2("../ajax/producto.php?op=select_u_medida", '#u_medida', null);
  lista_select2("../ajax/producto.php?op=select_marca", '#marca', null);

  lista_select2("../ajax/producto.php?op=select2_filtro_categoria", '#filtro_categoria', null);
  lista_select2("../ajax/producto.php?op=select2_filtro_u_medida", '#filtro_unidad_medida', null);
  lista_select2("../ajax/producto.php?op=select2_filtro_marca", '#filtro_marca', null);

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════ 
  $("#filtro_categoria").select2({  theme: "bootstrap4", placeholder: "Seleccione categoria", allowClear: true, });
  $("#filtro_unidad_medida").select2({  theme: "bootstrap4", placeholder: "Seleccione unidad medida", allowClear: true, });
  $("#filtro_marca").select2({  theme: "bootstrap4", placeholder: "Seleccione marca", allowClear: true, });

  $("#categoria").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#u_medida").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#marca").select2({  theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

}

//  :::::::::::::::: P R O D U C T O :::::::::::::::: 

function limpiar_form_producto(){

	$('#idproducto').val('');
  
	$('#tipo').val('PR');
	$('#codigo').val('');
	$('#codigo_alterno').val('');
	$('#categoria').val('').trigger('change');
	$('#u_medida').val('58').trigger('change'); // por defecto: NIU
	$('#marca').val('').trigger('change');
	$('#nombre').val('');
	$('#descripcion').val('');
	$('#stock').val('');
	$('#stock_min').val('');
	$('#precio_v').val('');
	$('#precio_c').val('');
	$('#precio_x_mayor').val('');
	$('#precio_dist').val('');
	$('#precio_esp').val('');

  $("#imagenProducto").val("");
  $("#imagenactualProducto").val("");
  $("#imagenmuestraProducto").attr("src", "../assets/modulo/productos/no-producto.png");
  $("#imagenmuestraProducto").attr("src", "../assets/modulo/productos/no-producto.png").show();
  var imagenMuestra = document.getElementById('imagenmuestraProducto');
  if (!imagenMuestra.src || imagenMuestra.src == "") {
    imagenMuestra.src = '../assets/modulo/productos/no-producto.png';
  }


  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function show_hide_form(flag) {
	if (flag == 1) {
    $(".card-header").show();
		$("#div-tabla").show();
		$(".div-form").hide();

		$(".btn-agregar").show();
		$(".btn-guardar").hide();
		$(".btn-cancelar").hide();
		
	} else if (flag == 2) {
    $(".card-header").hide();
		$("#div-tabla").hide();
		$(".div-form").show();

		$(".btn-agregar").hide();
		$(".btn-guardar").show();
		$(".btn-cancelar").show();
	}
}

function listar_tabla(filtro_categoria = '', filtro_unidad_medida = '', filtro_marca = ''){
  tabla_productos = $('#tabla-productos').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_productos) { tabla_productos.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,13,14,12,10,11,4,5,6,7,8], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,13,14,12,10,11,4,5,6,7,8], }, title: 'Lista de Productos', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,13,14,12,10,11,4,5,6,7,8], }, title: 'Lista de Productos', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    "ajax":	{
			url: `../ajax/producto.php?op=listar_tabla&categoria=${filtro_categoria}&unidad_medida=${filtro_unidad_medida}&marca=${filtro_marca}`,
			type: "get",
			dataType: "json",
			error: function (e) {
				console.log(e.responseText);
			},
      complete: function () {
        $(".buttons-reload").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Recargar');
        $(".buttons-copy").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Copiar');
        $(".buttons-excel").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Excel');
        $(".buttons-pdf").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'PDF');
        $(".buttons-colvis").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Columnas');
        $('[data-bs-toggle="tooltip"]').tooltip();
        $('.buscando_tabla').hide()
      },
      dataSrc: function (e) {
				if (e.status != true) {  ver_errores(e); }  return e.aaData;
			},
		},
    createdRow: function (row, data, ixdex) {
      // columna: #
      if (data[0] != '') { $("td", row).eq(0).addClass("text-center"); }
      // columna: #
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap text-center") }
      // columna: #
      if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap"); }
      // columna: #
      if (data[3] != '') { $("td", row).eq(3).addClass("text-nowrap"); }
      // columna: 5
      if (data[15] == 1 ) { $("td", row).eq(1).attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'No tienes opcion a modificar'); }
    },
    language: {
      lengthMenu: "Mostrar: _MENU_ registros",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    "bDestroy": true,
    "iDisplayLength": 10,
    "order": [[0, "asc"]],
    columnDefs:[
      { targets: [10,11,12,13,14,15],  visible: false,  searchable: false,  },
    ],
  }).DataTable();
}

function guardar_editar_producto(e){
  var formData = new FormData($("#form-agregar-producto")[0]);

	$.ajax({
		url: "../ajax/producto.php?op=guardar_editar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (e) {
			try {
				e = JSON.parse(e);
        if (e.status == true) {	
					sw_success('Exito', 'producto guardado correctamente.');
					tabla_productos.ajax.reload(null, false);          
					show_hide_form(1);
          limpiar_form_producto();
				} else {
					ver_errores(e);
				}				
			} catch (err) { console.log('Error: ', err.message); toastr_error("Error temporal!!",'Puede intentalo mas tarde, o comuniquese con:<br> <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>', 700); }      
      $(".btn-guardar").html('<i class="ri-save-2-line label-btn-icon me-2" ></i> Guardar').removeClass('disabled send-data');
		},
		xhr: function () {
			var xhr = new window.XMLHttpRequest();
			xhr.upload.addEventListener("progress", function (evt) {
				if (evt.lengthComputable) {
					var percentComplete = (evt.loaded / evt.total) * 100;
					$("#barra_progress_producto").css({ "width": percentComplete + '%' });
					$("#barra_progress_producto div").text(percentComplete.toFixed(2) + " %");
				}
			}, false);
			return xhr;
		},
		beforeSend: function () {
			$(".btn-guardar").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
			$("#barra_progress_producto").css({ width: "0%", });
			$("#barra_progress_producto div").text("0%");
      $("#barra_progress_producto_div").show();
		},
		complete: function () {
			$("#barra_progress_producto").css({ width: "0%", });
			$("#barra_progress_producto div").text("0%");
      $("#barra_progress_producto_div").hide();
		},
		error: function (jqXhr, ajaxOptions, thrownError) {
			ver_errores(jqXhr);
		}
	});
}

function mostrar_producto(idproducto){
  limpiar_form_producto();
	show_hide_form(2);
	$('#cargando-1-fomulario').hide();	$('#cargando-2-fomulario').show(); 
	$.post("../ajax/producto.php?op=mostrar", { idproducto: idproducto }, function (e, status) {
		e = JSON.parse(e);

		$('#idproducto').val(e.data.idproducto);
    $('#categoria').val(e.data.idcategoria).trigger('change');
    $('#u_medida').val(e.data.idsunat_unidad_medida).trigger('change');
    $('#marca').val(e.data.idmarca).trigger('change');

    $('#codigo').val(e.data.codigo);
    $('#codigo_alterno').val(e.data.codigo_alterno);
    $('#nombre').val(e.data.nombre);
    $('#descripcion').val(e.data.descripcion);
    $('#stock').val(e.data.stock);
    $('#stock_min').val(e.data.stock_minimo);
    $('#precio_v').val(e.data.precio_venta);
    $('#precio_c').val(e.data.precio_compra);
    $('#precio_x_mayor').val(e.data.precioB);
    $('#precio_dist').val(e.data.precioC);
    $('#precio_esp').val(e.data.precioD);

    $("#imagenmuestraProducto").show();
		$("#imagenmuestraProducto").attr("src", "../assets/modulo/productos/" + e.data.imagen);
		$("#imagenactualProducto").val(e.data.imagen);

    $('#cargando-1-fomulario').show();	$('#cargando-2-fomulario').hide();
    $('#form-agregar-producto').valid();
	});	
}

function mostrar_detalle_producto(idproducto){
  $("#modal-ver-detalle-producto").modal('show');
  $.post("../ajax/producto.php?op=mostrar_detalle_producto", { idproducto: idproducto }, function (e, status) {
    e = JSON.parse(e);
    if (e.status == true) {

      $("#html-detalle-producto").html(e.data);
      $("#html-detalle-imagen").html(doc_view_download_expand(e.imagen, 'assets/modulo/productos/', e.nombre_doc, '100%', '400px'));
      
    }else{
      ver_errores(e);
    }
  }).fail( function(e) { ver_errores(e); } );
}

function eliminar_papelera_producto(idproducto, nombre){
  $('.tooltip').remove();
	crud_eliminar_papelera(
    "../ajax/producto.php?op=papelera",
    "../ajax/producto.php?op=eliminar", 
    idproducto, 
    "!Elija una opción¡", 
    `<b class="text-danger"><del>${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_productos.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}


//  :::::::::::::::: C A T E G O R I A :::::::::::::::: 

function modal_add_categoria() {
  $("#modal-agregar-categoria").modal('show');
}

function limpiar_form_cat(){
  $("#guardar_registro_categoria").html('Guardar Cambios').removeClass('disabled');
  
  $("#idcategoria").val("");
  $("#nombre_cat").val("");
  $("#descr_cat").val("");
  
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function guardar_editar_categoria(e){
  var formData = new FormData($("#formulario-categoria")[0]);
  $.ajax({
    url: "../ajax/categoria.php?op=guardar_editar_cat",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false, 

    success: function (e) {
      e = JSON.parse(e);  console.log(e);  
      if (e.status == true) {
        lista_select2("../ajax/producto.php?op=select_categoria", '#categoria', e.data, '.charge_idcategoria');
        Swal.fire("Correcto!", "Categoría registrada correctamente.", "success");
				limpiar_form_cat();
        $("#modal-agregar-categoria").modal("hide");        
			}else{
				ver_errores(e);
			}
      $("#guardar_registro_categoria").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');
      
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}

// :::::::::::: U N I D A D    M E D I D A  :::::::::::::::::::

function modal_add_u_medida() {
  $("#modal-agregar-u-m").modal('show');
}

function limpiar_form_um() {
  $("#guardar_registro_u_m").html('Guardar Cambios').removeClass('disabled');
  
  $("#idsunat_unidad_medida").val("");
  $("#nombre_um").val("");
  $("#descr_um").val("");
  
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function guardar_editar_UM(e){
  var formData = new FormData($("#formulario-u-m")[0]);
  $.ajax({
    url: "../ajax/unidad_medida.php?op=guardar_editar_UM",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false, 

    success: function (e) {
      e = JSON.parse(e); 
      if (e.status == true) {
        Swal.fire("Correcto!", "Unidad de medida registrado correctamente.", "success");
				limpiar_form_um();
        $("#modal-agregar-u-m").modal("hide");        
			}else{
				ver_errores(e);
			}
      $("#guardar_registro_u_m").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');
      
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}


// :::::::::::: M A R C A :::::::::::::::::::

function modal_add_marca() {
  $("#modal-agregar-marca").modal('show');
}

function limpiar_form_marca(){
  $("#guardar_registro_marca").html('Guardar Cambios').removeClass('disabled');

  $("#idmarca").val("");
  $("#nombre_marca").val("");
  $("#descr_marca").val("");
  
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function guardar_editar_marca(e){
  var formData = new FormData($("#formulario-marca")[0]);
  $.ajax({
    url: "../ajax/marca.php?op=guardar_editar_marca",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false, 

    success: function (e) {
      e = JSON.parse(e);  console.log(e);  
      if (e.status == true) {
        lista_select2("../ajax/producto.php?op=select_marca", '#marca', e.data, '.charge_idmarca');
        Swal.fire("Correcto!", "Marca registrada correctamente.", "success");
				limpiar_form_marca();
        $("#modal-agregar-marca").modal("hide");        
			}else{
				ver_errores(e);
			}
      $("#guardar_registro_marca").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');
      
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}

$(document).ready(function () {
  init();
});

function mayus(e) {
  e.value = e.value.toUpperCase();
}

function generarcodigonarti() {

  var name_producto = $("#nombre").val() == null || $("#nombre").val() == '' ? '' : $("#nombre").val() ;  
  if (name_producto == '') { toastr_warning('Vacio!!','El nombre esta vacio, digita para completar el codigo aletarorio.', 700); }
  name_producto = name_producto.substring(-3, 3);
  var cod_letra = Math.random().toString(36).substring(2, 5);  
  var cod_number = Math.floor(Math.random() * 10) +''+ Math.floor(Math.random() * 10);  
  $("#codigo_alterno").val(`${name_producto.toUpperCase()}${cod_number}${cod_letra.toUpperCase()}`);
}

function create_code_producto(pre_codigo) {
  $('.charge_codigo').html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);
 
  $.getJSON(`../ajax/ajax_general.php?op=create_code_producto&pre_codigo=${pre_codigo}`, function (e, textStatus, jqXHR) {
    if (e.status == true) {
      $('#codigo').val(e.data.nombre_codigo);
      $('#codigo').attr('readonly', 'readonly').addClass('bg-light'); // Asegura que el campo esté como solo lectura
      add_tooltip_custom('#codigo', 'No se puede editar');            //  Agrega tooltip personalizado a un element
      $('.charge_codigo').html('')                                    // limpiamos la carga
    } else {
      ver_errores(e);
    }      
  }).fail( function(jqxhr, textStatus, error) { ver_errores(jqxhr); } );
  
}


$(function () {
  $('#categoria').on('change', function() { $(this).trigger('blur'); });
  $('#u_medida').on('change', function() { $(this).trigger('blur'); });
  $('#marca').on('change', function() { $(this).trigger('blur'); });

  //  :::::::::::::::::::: F O R M U L A R I O   P R O D U C T O ::::::::::::::::::::
  $("#form-agregar-producto").validate({
    ignore: "",
    rules: {           
      codigo:         { required: true, minlength: 2, maxlength: 20, },       
      categaria:    	{ required: true },       
      u_medida:    		{ required: true },       
      marca:    			{ required: true },       
      nombre:    			{ required: true, minlength: 2, maxlength: 250,  },       
      descripcion:    { minlength: 2, maxlength: 500, },       
      stock:          { required: true, min: 0, step: 0.01,},       
      stock_min:      { required: true, min: 0, step: 0.01,}, 
      precio_v:       { required: true, min: 0, step: 0.01,},       
      precio_c:       { required: true, min: 0, step: 0.01,},	
      precio_x_mayor: { min: 0, step: 0.01,},	
      precio_dist:    { min: 0, step: 0.01,},	
      precio_esp:     { min: 0, step: 0.01,},	
      codigo_alterno: { required: true, minlength: 4, maxlength: 20,
        remote: {
          url: "../ajax/producto.php?op=validar_code_producto",
          type: "get",
          data: {
            action: function () { return "validar_codigo";  },
            idproducto: function() { var idproducto = $("#idproducto").val(); return idproducto; }
          }
        }
      },
    },
    messages: {     
      cogido:    			{ required: "Campo requerido", },
      categaria:    	{ required: "Seleccione una opción", },
      u_medida:    		{ required: "Seleccione una opción", },
      marca:    			{ required: "Seleccione una opción", },
      nombre:    			{ required: "Campo requerido", }, 
      descripcion:    { minlength: "Minimo {0} caracteres.", },       
      stock:          { required: "Campo requerido", step: 'Maximo 2 decimales.'},       
      stock_min:      { required: "Campo requerido", step: 'Maximo 2 decimales.'}, 
      precio_v:       { required: "Campo requerido", step: 'Maximo 2 decimales.'},       
      precio_c:       { required: "Campo requerido", step: 'Maximo 2 decimales.'},	
      precio_x_mayor: { step: 'Maximo 2 decimales.'},	
      precio_dist:    { step: 'Maximo 2 decimales.'},	
      precio_esp:     { step: 'Maximo 2 decimales.'},
      codigo_alterno:    			{ required: "Campo requerido", remote:"Código en uso."},
    },
        
    errorElement: "span",

    errorPlacement: function (error, element) {
      error.addClass("invalid-feedback");
      element.closest(".form-group").append(error);
    },

    highlight: function (element, errorClass, validClass) {
      $(element).addClass("is-invalid").removeClass("is-valid");
    },

    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass("is-invalid").addClass("is-valid");   
    },
    submitHandler: function (e) {
      $(".modal-body").animate({ scrollTop: $(document).height() }, 600);
      guardar_editar_producto(e);      
    },
  });

  //  :::::::::::::::::::: F O R M U L A R I O   C A T E G O R I A ::::::::::::::::::::

  $("#formulario-categoria").validate({
    rules: {
      nombre_cat: { required: true },
      descr_cat:  { required: true } 
    },
    messages: {
      nombre_cat: {  required: "Campo requerido.", },
      descr_cat:  {  required: "Campo requerido.", },
    },
        
    errorElement: "span",

    errorPlacement: function (error, element) {
      error.addClass("invalid-feedback");
      element.closest(".form-group").append(error);
    },

    highlight: function (element, errorClass, validClass) {
      $(element).addClass("is-invalid").removeClass("is-valid");
    },

    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass("is-invalid").addClass("is-valid");   
    },
    submitHandler: function (e) { 
      $(".modal-body").animate({ scrollTop: $(document).height() }, 600); 
      guardar_editar_categoria(e);      
    },

  });

  //  :::::::::::::::::::::: F O R M U L A R I O   U.   M E D I D A :::::::::::::::::::::::::::

  $("#formulario-u-m").validate({
    rules: {
      nombre_um: { required: true } ,
      descr_um:  { required: true } 
    },
    messages: {
      nombre_um: {  required: "Campo requerido.", },
      descr_um:  {  required: "Campo requerido.", },
    },
        
    errorElement: "span",

    errorPlacement: function (error, element) {
      error.addClass("invalid-feedback");
      element.closest(".form-group").append(error);
    },

    highlight: function (element, errorClass, validClass) {
      $(element).addClass("is-invalid").removeClass("is-valid");
    },

    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass("is-invalid").addClass("is-valid");   
    },
    submitHandler: function (e) { 
      $(".modal-body").animate({ scrollTop: $(document).height() }, 600);
      guardar_editar_UM(e);      
    },

  });

  //  :::::::::::::::::::::: F O R M U L A R I O   M A R C A :::::::::::::::::::::::::::

  $("#formulario-marca").validate({
    rules: {
      nombre_marca: { required: true } ,
      descr_marca:  { required: true }
    },
    messages: {
      nombre_marca: {  required: "Campo requerido.", },
      descr_marca:  {  required: "Campo requerido.", },
    },
        
    errorElement: "span",

    errorPlacement: function (error, element) {
      error.addClass("invalid-feedback");
      element.closest(".form-group").append(error);
    },

    highlight: function (element, errorClass, validClass) {
      $(element).addClass("is-invalid").removeClass("is-valid");
    },

    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass("is-invalid").addClass("is-valid");   
    },
    submitHandler: function (e) { 
      $(".modal-body").animate({ scrollTop: $(document).height() }, 600);
      guardar_editar_marca(e);      
    },

  });

  $('#categoria').rules('add', { required: true, messages: {  required: "Campo requerido" } });
  $('#u_medida').rules('add', { required: true, messages: {  required: "Campo requerido" } });
  $('#marca').rules('add', { required: true, messages: {  required: "Campo requerido" } });
});

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..

function cargando_search() {
  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ...`);
}

function filtros() {  

  var filtro_categoria      = $("#filtro_categoria").select2('val');
  var filtro_unidad_medida  = $("#filtro_unidad_medida").select2('val');  
  var filtro_marca          = $("#filtro_marca").select2('val');
  
  var nombre_categoria      = $('#filtro_categoria').find(':selected').text();
  var nombre_um             = ' ─ ' + $('#filtro_unidad_medida').find(':selected').text();
  var nombre_marca          = ' ─ ' + $('#filtro_marca').find(':selected').text();

  // filtro de fechas
  if (filtro_categoria == '' || filtro_categoria == 0 || filtro_categoria == null) { filtro_categoria = ""; nombre_categoria = ""; }

  // filtro de proveedor
  if (filtro_unidad_medida == '' || filtro_unidad_medida == 0 || filtro_unidad_medida == null) { filtro_unidad_medida = ""; nombre_um = ""; }

  // filtro de trabajdor
  if (filtro_marca == '' || filtro_marca == 0 || filtro_marca == null) { filtro_marca = ""; nombre_marca = ""; }

  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${nombre_categoria} ${nombre_um} ${nombre_marca}...`);
  //console.log(filtro_categoria, fecha_2, filtro_marca, comprobante);

  listar_tabla(filtro_categoria, filtro_unidad_medida, filtro_marca);
}


function cambiarImagen() {
	var imagenInput = document.getElementById('imagenProducto');
	imagenInput.click();
}

function removerImagen() {
	$("#imagenmuestraProducto").attr("src", "../assets/modulo/productos/no-producto.png");
	$("#imagenProducto").val("");
  $("#imagenactualProducto").val("");
}

document.addEventListener('DOMContentLoaded', function () {
	var imagenMuestra = document.getElementById('imagenmuestraProducto');
	var imagenInput = document.getElementById('imagenProducto');

	imagenInput.addEventListener('change', function () {
		if (imagenInput.files && imagenInput.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) { imagenMuestra.src = e.target.result;	}
			reader.readAsDataURL(imagenInput.files[0]);
		}
	});
});

function ver_img(img, nombre) {
	$(".title-modal-img").html(`-${nombre}`);
  $('#modal-ver-img').modal("show");
  $('.html_ver_img').html(doc_view_extencion(img, 'assets/modulo/productos', '100%', '550'));
  $(`.jq_image_zoom`).zoom({ on:'grab' });
}

function reload_idcategoria(){ lista_select2("../ajax/producto.php?op=select_categoria", '#categoria', null, '.charge_idcategoria'); }
function reload_idmarca(){ lista_select2("../ajax/producto.php?op=select_marca", '#marca', null, '.charge_idmarca'); }
function reload_idunidad_medida(){ lista_select2("../ajax/producto.php?op=select_u_medida", '#u_medida', null, '.charge_idunidad_medida'); }

function reload_filtro_categoria() { lista_select2("../ajax/producto.php?op=select2_filtro_categoria", '#filtro_categoria', null, '.charge_filtro_categoria'); }
function reload_filtro_unidad_medida() { lista_select2("../ajax/producto.php?op=select2_filtro_u_medida", '#filtro_unidad_medida', null, '.charge_filtro_unidad_medida'); }
function reload_filtro_marca() { lista_select2("../ajax/producto.php?op=select2_filtro_marca", '#filtro_marca', null, '.charge_filtro_marca'); }
