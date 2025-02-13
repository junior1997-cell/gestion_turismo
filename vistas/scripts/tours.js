var tabla_tours;
let detalle_incluye;
let detalle_programa;
let file_pond_brochure;

/* quill snow editor */
var toolbarOptions = [
  [{ header: [1, 2, 3, 4, 5, 6, false] }],
  [{ font: [] }],
  ["bold", "italic", "underline", "strike"], // toggled buttons
  ["blockquote", "code-block"],

  [{ header: 1 }, { header: 2 }], // custom button values
  [{ list: "ordered" }, { list: "bullet" }],
  [{ script: "sub" }, { script: "super" }], // superscript/subscript
  [{ indent: "-1" }, { indent: "+1" }], // outdent/indent
  [{ direction: "rtl" }], // text direction

  [{ size: ["small", false, "large", "huge"] }], // custom dropdown

  [{ color: [] }, { background: [] }], // dropdown with defaults from theme
  [{ align: [] }],

  ["image", "video"],
  ["clean"], // remove formatting button
];

function init() {
  detalle_incluye = new Quill("#detalle_incluye", {
    modules: { toolbar: toolbarOptions },
    theme: "snow",
  });
  detalle_programa = new Quill("#detalle_programa_turistico", {
    modules: { toolbar: toolbarOptions },
    theme: "snow",
  });

   listar_tabla('','','');

  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $(".btn-guardar").on("click", function (e) {
    if ($(this).hasClass("send-data") == false) {
      console.log("holaaaaaaaaaaaa");
      $("#submit-form-tours").submit();
    }
  });

  // $("#guardar_registro_marca").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-marca").submit(); } });
  // $("#guardar_registro_u_m").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-u-m").submit(); } });

  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_select2(
    "../ajax/tours.php?op=select_ubigeo_distrito",
    "#ubigeo_distrito",
    null
  );
  lista_select2(
    "../ajax/tours.php?op=select_tours_turno",
    "#tours_turno",
    null
  );

  /*lista_select2("../ajax/tours.php?op=select2_filtro_ubigeo_distrito", '#filtro_ubigeo_distrito', null);
  lista_select2("../ajax/tours.php?op=select2_filtro_tours_turno", '#filtro_unidad_medida', null);
  lista_select2("../ajax/tours.php?op=select2_filtro_marca", '#filtro_marca', null);*/

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════
  /*$("#filtro_ubigeo_distrito").select2({  theme: "bootstrap4", placeholder: "Seleccione ubigeo_distrito", allowClear: true, });
  $("#filtro_unidad_medida").select2({  theme: "bootstrap4", placeholder: "Seleccione unidad medida", allowClear: true, });
  $("#filtro_marca").select2({  theme: "bootstrap4", placeholder: "Seleccione marca", allowClear: true, });*/

  $("#ubigeo_distrito").select2({
    theme: "bootstrap4",
    placeholder: "Seleccione",
    allowClear: true,
  });
  $("#tours_turno").select2({
    theme: "bootstrap4",
    placeholder: "Seleccione",
    allowClear: true,
  });
}

//  :::::::::::::::: P R O D U C T O ::::::::::::::::
// quill.setContents([]); 
function limpiar_form_tours() {
  $("#idtours").val("");

  $("#codigo").val("");
  $("#codigo_alterno").val("");
  $("#ubigeo_distrito").val("").trigger("change");
  $("#tours_turno").val("58").trigger("change"); // por defecto: NIU
  
  $("#prov_dep").val("");
  $("#nombre").val("");
  $("#precio_publico").val("");
  $("#precio_corporativo").val("");
  $("#precio_web").val("");
  $("#precio_tours").val("");
  $("#detalle_duracion").val("");

  detalle_incluye.setContents([]); 
  detalle_programa.setContents([]); 

  
  file_pond_brochure.removeFiles();
  $("#brochure_old").val('');


  // Limpiamos las validaciones
  $(".form-control").removeClass("is-valid");
  $(".form-control").removeClass("is-invalid");
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

    limpiar_form_tours();

  } else if (flag == 2) {
    $(".card-header").hide();
    $("#div-tabla").hide();
    $(".div-form").show();

    $(".btn-agregar").hide();
    $(".btn-guardar").show();
    $(".btn-cancelar").show();
  }
}

function listar_tabla(
  filtro_ubigeo_distrito = "",
  filtro_unidad_medida = "",
  filtro_marca = ""
) {
  tabla_tours = $("#tabla-tours")
    .dataTable({
      lengthMenu: [
        [-1, 5, 10, 25, 75, 100, 200],
        ["Todos", 5, 10, 25, 75, 100, 200],
      ], //mostramos el menú de registros a revisar
      aProcessing: true, //Activamos el procesamiento del datatables
      aServerSide: true, //Paginación y filtrado realizados por el servidor
      dom: "<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
      buttons: [
        {
          text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ",
          action: function (e, dt, node, config) { if (tabla_tours) { tabla_tours.ajax.reload(null, false); } },
        },
        {
          extend: "copy", exportOptions: { columns: [0] },
          text: `<i class="fas fa-copy" ></i>`,
          className: "btn btn-outline-dark btn-wave ",
          footer: true,
        },
        {
          extend: "excel",
          exportOptions: { columns: [0] },
          title: "Lista de Productos",
          text: `<i class="far fa-file-excel fa-lg" ></i>`,
          className: "btn btn-outline-success btn-wave ",
          footer: true,
        },
        {
          extend: "pdf",
          exportOptions: { columns: [0] },
          title: "Lista de Productos",
          text: `<i class="far fa-file-pdf fa-lg"></i>`,
          className: "btn btn-outline-danger btn-wave ",
          footer: false,
          orientation: "landscape",
          pageSize: "LEGAL",
        },
        {
          extend: "colvis",
          text: `<i class="fas fa-outdent"></i>`,
          className: "btn btn-outline-primary",
          exportOptions: { columns: "th:not(:last-child)" },
        },
      ],
      ajax: {
        url: `../ajax/tours.php?op=listar_tabla&ubigeo_distrito=${filtro_ubigeo_distrito}&unidad_medida=${filtro_unidad_medida}&marca=${filtro_marca}`,
        type: "get",
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
        complete: function () {
          $(".buttons-reload")
            .attr("data-bs-toggle", "tooltip")
            .attr("data-bs-original-title", "Recargar");
          $(".buttons-copy")
            .attr("data-bs-toggle", "tooltip")
            .attr("data-bs-original-title", "Copiar");
          $(".buttons-excel")
            .attr("data-bs-toggle", "tooltip")
            .attr("data-bs-original-title", "Excel");
          $(".buttons-pdf")
            .attr("data-bs-toggle", "tooltip")
            .attr("data-bs-original-title", "PDF");
          $(".buttons-colvis")
            .attr("data-bs-toggle", "tooltip")
            .attr("data-bs-original-title", "Columnas");
          $('[data-bs-toggle="tooltip"]').tooltip();
          $(".buscando_tabla").hide();
        },
        dataSrc: function (e) {
          if (e.status != true) {
            ver_errores(e);
          }
          return e.aaData;
        },
      },
      createdRow: function (row, data, ixdex) {
        // columna: #
        if (data[0] != "") {
          $("td", row).eq(0).addClass("text-center");
        }
        // columna: #
        if (data[1] != "") {
          $("td", row).eq(1).addClass("text-nowrap text-center");
        }
        // columna: #
        if (data[2] != "") {
          $("td", row).eq(2).addClass("text-nowrap");
        }
        // columna: #
        if (data[3] != "") {
          $("td", row).eq(3).addClass("text-nowrap");
        }
        // columna: 5
        if (data[15] == 1) {
          $("td", row)
            .eq(1)
            .attr("data-bs-toggle", "tooltip")
            .attr("data-bs-original-title", "No tienes opcion a modificar");
        }
      },
      language: {
        lengthMenu: "Mostrar: _MENU_ registros",
        buttons: {
          copyTitle: "Tabla Copiada",
          copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada" },
        },
        sLoadingRecords:
          '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...',
      },
      bDestroy: true,
      iDisplayLength: 10,
      order: [[0, "asc"]],
      columnDefs: [
        {
          targets: [],
          visible: false,
          searchable: false,
        },
      ],
    })
    .DataTable();
}

function guardar_editar_tours(e) {

  var formData = new FormData(e); // Usa el formulario correcto

  // Agregar los valores de Quill al formData
  formData.append("detalle_incluye_tours", detalle_incluye.root.innerHTML);
  formData.append("detalle_programa_tours", detalle_programa.root.innerHTML);

  $.ajax({
    url: "../ajax/tours.php?op=guardar_editar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      try {
        e = JSON.parse(e);
        if (e.status == true) {
          sw_success("Exito", "producto guardado correctamente.");
          tabla_tours.ajax.reload(null, false);
          show_hide_form(1);
          limpiar_form_tours();
        } else {
          ver_errores(e);
        }
      } catch (err) {
        console.log("Error: ", err.message);
        toastr_error(
          "Error temporal!!",
          'Puede intentalo mas tarde, o comuniquese con:<br> <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>',
          700
        );
      }
      $(".btn-guardar")
        .html('<i class="ri-save-2-line label-btn-icon me-2" ></i> Guardar')
        .removeClass("disabled send-data");
    },
    xhr: function () {
      var xhr = new window.XMLHttpRequest();
      xhr.upload.addEventListener(
        "progress",
        function (evt) {
          if (evt.lengthComputable) {
            var percentComplete = (evt.loaded / evt.total) * 100;
            $("#barra_progress_tours").css({ width: percentComplete + "%" });
            $("#barra_progress_tours div").text(
              percentComplete.toFixed(2) + " %"
            );
          }
        },
        false
      );
      return xhr;
    },
    beforeSend: function () {
      $(".btn-guardar")
        .html('<i class="fas fa-spinner fa-pulse fa-lg"></i>')
        .addClass("disabled send-data");
      $("#barra_progress_tours").css({ width: "0%" });
      $("#barra_progress_tours div").text("0%");
      $("#barra_progress_tours_div").show();
    },
    complete: function () {
      $("#barra_progress_tours").css({ width: "0%" });
      $("#barra_progress_tours div").text("0%");
      $("#barra_progress_tours_div").hide();
    },
    error: function (jqXhr, ajaxOptions, thrownError) {
      ver_errores(jqXhr);
    },
  });
}

function mostrar_producto(idtours) {
  limpiar_form_tours();
  show_hide_form(2);
  $("#cargando-1-fomulario").hide();
  $("#cargando-2-fomulario").show();
  $.post(
    "../ajax/tours.php?op=mostrar",
    { idtours: idtours },
    function (e, status) {
      e = JSON.parse(e);

      $("#idtours").val(e.data.idtours);
      $("#ubigeo_distrito").val(e.data.idubigeo_distrito).trigger("change");
      $("#tours_turno").val(e.data.idtours_turno).trigger("change");

      $("#codigo").val(e.data.codigo);
      $("#codigo_alterno").val(e.data.codigo_alterno);
      $("#nombre").val(e.data.nombre);
      $("#precio_publico").val(e.data.precio_publico);
      $("#precio_corporativo").val(e.data.precio_corporativo);
      $("#precio_web").val(e.data.precio_web);
      $("#precio_tours").val(e.data.precio_tours);
      $("#detalle_duracion").val(e.data.detalle_duracion);

      detalle_incluye.root.innerHTML = e.data.detalle_incluye;
      detalle_programa.root.innerHTML = e.data.detalle_programa_turistico;
      // detalle_incluye.setContents([]); 
      // detalle_programa.setContents([]);

      $("#imagenmuestraProducto").show();
      $("#imagenmuestraProducto").attr(
        "src",
        "../assets/modulo/productos/" + e.data.imagen
      );
      $("#imagenactualProducto").val(e.data.imagen);

      $("#cargando-1-fomulario").show();
      $("#cargando-2-fomulario").hide();
      $("#form-agregar-tours").valid();
    }
  );
}

function mostrar_detalle_producto(idtours) {
  $("#modal-ver-detalle-producto").modal("show");
  $.post(
    "../ajax/tours.php?op=mostrar_detalle_producto",
    { idtours: idtours },
    function (e, status) {
      e = JSON.parse(e);
      if (e.status == true) {
        $("#html-detalle-producto").html(e.data);
        $("#html-detalle-imagen").html(
          doc_view_download_expand(
            e.imagen,
            "assets/modulo/productos/",
            e.nombre_doc,
            "100%",
            "400px"
          )
        );
      } else {
        ver_errores(e);
      }
    }
  ).fail(function (e) {
    ver_errores(e);
  });
}

function eliminar_papelera_producto(idtours, nombre) {
  $(".tooltip").remove();
  crud_eliminar_papelera(
    "../ajax/tours.php?op=papelera",
    "../ajax/tours.php?op=eliminar",
    idtours,
    "!Elija una opción¡",
    `<b class="text-danger"><del>${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`,
    function () {
      sw_success("♻️ Papelera! ♻️", "Tu registro ha sido reciclado.");
    },
    function () {
      sw_success("Eliminado!", "Tu registro ha sido Eliminado.");
    },
    function () {
      tabla_tours.ajax.reload(null, false);
    },
    false,
    false,
    false,
    false
  );
}

// :::::::::::: U N I D A D    M E D I D A  :::::::::::::::::::

function modal_add_tours_turno() {
  $("#modal-agregar-u-m").modal("show");
}

function limpiar_form_um() {
  $("#guardar_registro_u_m").html("Guardar Cambios").removeClass("disabled");

  $("#idsunat_unidad_medida").val("");
  $("#nombre_um").val("");
  $("#descr_um").val("");

  $(".form-control").removeClass("is-valid");
  $(".form-control").removeClass("is-invalid");
  $(".error.invalid-feedback").remove();
}

function guardar_editar_UM(e) {
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
        Swal.fire(
          "Correcto!",
          "Unidad de medida registrado correctamente.",
          "success"
        );
        limpiar_form_um();
        $("#modal-agregar-u-m").modal("hide");
      } else {
        ver_errores(e);
      }
      $("#guardar_registro_u_m")
        .html('<i class="bx bx-save bx-tada"></i> Guardar')
        .removeClass("disabled send-data");
    },
    error: function (jqXhr) {
      ver_errores(jqXhr);
    },
  });
}

// :::::::::::: M A R C A :::::::::::::::::::

function modal_add_marca() {
  $("#modal-agregar-marca").modal("show");
}

function limpiar_form_marca() {
  $("#guardar_registro_marca").html("Guardar Cambios").removeClass("disabled");

  $("#idmarca").val("");
  $("#nombre_marca").val("");
  $("#descr_marca").val("");

  $(".form-control").removeClass("is-valid");
  $(".form-control").removeClass("is-invalid");
  $(".error.invalid-feedback").remove();
}

function guardar_editar_marca(e) {
  var formData = new FormData($("#formulario-marca")[0]);
  $.ajax({
    url: "../ajax/marca.php?op=guardar_editar_marca",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (e) {
      e = JSON.parse(e);
      console.log(e);
      if (e.status == true) {
        lista_select2(
          "../ajax/tours.php?op=select_marca",
          "#marca",
          e.data,
          ".charge_idmarca"
        );
        Swal.fire("Correcto!", "Marca registrada correctamente.", "success");
        limpiar_form_marca();
        $("#modal-agregar-marca").modal("hide");
      } else {
        ver_errores(e);
      }
      $("#guardar_registro_marca")
        .html('<i class="bx bx-save bx-tada"></i> Guardar')
        .removeClass("disabled send-data");
    },
    error: function (jqXhr) {
      ver_errores(jqXhr);
    },
  });
}

$(document).ready(function () {
  init();
});

function mayus(e) {
  e.value = e.value.toUpperCase();
}

function generarcodigonarti() {
  var name_producto =
    $("#nombre").val() == null || $("#nombre").val() == ""
      ? ""
      : $("#nombre").val();
  if (name_producto == "") {
    toastr_warning(
      "Vacio!!",
      "El nombre esta vacio, digita para completar el codigo aletarorio.",
      700
    );
  }
  name_producto = name_producto.substring(-3, 3);
  var cod_letra = Math.random().toString(36).substring(2, 5);
  var cod_number =
    Math.floor(Math.random() * 10) + "" + Math.floor(Math.random() * 10);
  $("#codigo_alterno").val(
    `${name_producto.toUpperCase()}${cod_number}${cod_letra.toUpperCase()}`
  );
}

function create_code_tours(pre_codigo) {
  $(".charge_codigo").html(
    `<div class="spinner-border spinner-border-sm" role="status"></div>`
  );

  $.getJSON(
    `../ajax/ajax_general.php?op=create_code_tours&pre_codigo=${pre_codigo}`,
    function (e, textStatus, jqXHR) {
      if (e.status == true) {
        $("#codigo").val(e.data.nombre_codigo);
        $("#codigo").attr("readonly", "readonly").addClass("bg-light"); // Asegura que el campo esté como solo lectura
        add_tooltip_custom("#codigo", "No se puede editar"); //  Agrega tooltip personalizado a un element
        $(".charge_codigo").html(""); // limpiamos la carga
      } else {
        ver_errores(e);
      }
    }
  ).fail(function (jqxhr, textStatus, error) {
    ver_errores(jqxhr);
  });
}

$(function () {
  $("#ubigeo_distrito").on("change", function () {
    $(this).trigger("blur");
  });
  $("#tours_turno").on("change", function () {
    $(this).trigger("blur");
  });

  //  :::::::::::::::::::: F O R M U L A R I O   P R O D U C T O ::::::::::::::::::::
  $("#form-agregar-tours").validate({
    ignore: ".ql-editor",
    rules: {
      codigo: { required: true, minlength: 2, maxlength: 20 },
      ubigeo_distrito: { required: true },
      tours_turno: { required: true },
      nombre: { required: true, minlength: 2, maxlength: 250 },
      precio_publico: { required: true, min: 0, step: 0.01 },
      precio_corporativo: { required: true, min: 0, step: 0.01 },
      precio_web: { required: true, min: 0, step: 0.01 },
      precio_tours: { required: true, min: 0, step: 0.01 },
      detalle_duracion: { required: true },
      codigo_alterno: {
        required: true,
        minlength: 4,
        maxlength: 20,
        remote: {
          url: "../ajax/tours.php?op=validar_code_tours",
          type: "get",
          data: {
            action: function () {
              return "validar_codigo";
            },
            idtours: function () {
              var idtours = $("#idtours").val();
              return idtours;
            },
          },
        },
      },
    },
    messages: {
      cogido: { required: "Campo requerido" },
      tours_turno: { required: "Seleccione una opción" },
      nombre: { required: "Campo requerido" },
      precio_publico: {
        required: "Campo requerido",
        step: "Maximo 2 decimales.",
      },
      precio_corporativo: {
        required: "Campo requerido",
        step: "Maximo 2 decimales.",
      },
      precio_web: { required: "Campo requerido", step: "Maximo 2 decimales." },
      precio_tours: {
        required: "Campo requerido",
        step: "Maximo 2 decimales.",
      },
      detalle_duracion: { required: "Campo requerido" },
      codigo_alterno: { required: "Campo requerido", remote: "Código en uso." },
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
      guardar_editar_tours(e);
    },
  });

  $("#ubigeo_distrito").rules("add", {
    required: true,
    messages: { required: "Campo requerido" },
  });
  $("#tours_turno").rules("add", {
    required: true,
    messages: { required: "Campo requerido" },
  });
});

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..

function select_cuidad() {
  var distrito = $("#ubigeo_distrito").select2("val");

  // filtro de fechas
  if (distrito == "" || distrito == 0 || distrito == null) {

  }else{
    var atributo = $("#ubigeo_distrito option:selected").attr("data-prov_reg");

    console.log(atributo);

    $("#prov_dep").val(atributo);
  }
}

function cargando_search() {
  $(".buscando_tabla")
    .show()
    .html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ...`);
}

function filtros() {
  var filtro_ubigeo_distrito = $("#filtro_ubigeo_distrito").select2("val");
  var filtro_unidad_medida = $("#filtro_unidad_medida").select2("val");
  var filtro_marca = $("#filtro_marca").select2("val");

  var nombre_ubigeo_distrito = $("#filtro_ubigeo_distrito")
    .find(":selected")
    .text();
  var nombre_um = " ─ " + $("#filtro_unidad_medida").find(":selected").text();
  var nombre_marca = " ─ " + $("#filtro_marca").find(":selected").text();

  // filtro de fechas
  if (
    filtro_ubigeo_distrito == "" ||
    filtro_ubigeo_distrito == 0 ||
    filtro_ubigeo_distrito == null
  ) {
    filtro_ubigeo_distrito = "";
    nombre_ubigeo_distrito = "";
  }

  // filtro de proveedor
  if (
    filtro_unidad_medida == "" ||
    filtro_unidad_medida == 0 ||
    filtro_unidad_medida == null
  ) {
    filtro_unidad_medida = "";
    nombre_um = "";
  }

  // filtro de trabajdor
  if (filtro_marca == "" || filtro_marca == 0 || filtro_marca == null) {
    filtro_marca = "";
    nombre_marca = "";
  }

  $(".buscando_tabla")
    .show()
    .html(
      `<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${nombre_ubigeo_distrito} ${nombre_um} ${nombre_marca}...`
    );
  //console.log(filtro_ubigeo_distrito, fecha_2, filtro_marca, comprobante);

  listar_tabla(filtro_ubigeo_distrito, filtro_unidad_medida, filtro_marca);
}

function cambiarImagen() {
  var imagenInput = document.getElementById("imagenProducto");
  imagenInput.click();
}

function removerImagen() {
  $("#imagenmuestraProducto").attr(
    "src",
    "../assets/modulo/productos/no-producto.png"
  );
  $("#imagenProducto").val("");
  $("#imagenactualProducto").val("");
}


function ver_img(img, nombre) {
  $(".title-modal-img").html(`-${nombre}`);
  $("#modal-ver-img").modal("show");
  $(".html_ver_img").html(
    doc_view_extencion(img, "assets/modulo/productos", "100%", "550")
  );
  $(`.jq_image_zoom`).zoom({ on: "grab" });
}

function reload_idubigeo_distrito() {
  lista_select2(
    "../ajax/tours.php?op=select_ubigeo_distrito",
    "#ubigeo_distrito",
    null,
    ".charge_idubigeo_distrito"
  );
}
function reload_idmarca() {
  lista_select2(
    "../ajax/tours.php?op=select_marca",
    "#marca",
    null,
    ".charge_idmarca"
  );
}
function reload_tours_turno() {
  lista_select2(
    "../ajax/tours.php?op=select_tours_turno",
    "#tours_turno",
    null,
    ".charge_turno_tours"
  );
}

function reload_filtro_ubigeo_distrito() {
  lista_select2(
    "../ajax/tours.php?op=select2_filtro_ubigeo_distrito",
    "#filtro_ubigeo_distrito",
    null,
    ".charge_filtro_ubigeo_distrito"
  );
}
function reload_filtro_unidad_medida() {
  lista_select2(
    "../ajax/tours.php?op=select2_filtro_tours_turno",
    "#filtro_unidad_medida",
    null,
    ".charge_filtro_unidad_medida"
  );
}
function reload_filtro_marca() {
  lista_select2(
    "../ajax/tours.php?op=select2_filtro_marca",
    "#filtro_marca",
    null,
    ".charge_filtro_marca"
  );
}


(function () {
  "use strict"  

  // UPLOADS ===================================

  /* filepond */
  FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginImageExifOrientation,
    FilePondPluginFileValidateSize,
    FilePondPluginFileEncode,
    FilePondPluginImageEdit,
    FilePondPluginFileValidateType,
    FilePondPluginImageCrop,
    FilePondPluginImageResize,
    FilePondPluginImageTransform,
      
  );

  // Configura opciones globales para FilePond
  FilePond.setOptions({
    allowMultiple: false, // Permitir subir múltiples archivos
    maxFiles: 1, // Máximo número de archivos permitidos
    maxFileSize: '3MB', // Tamaño máximo por archivo
    acceptedFileTypes: ['image/*', 'application/pdf'], // Tipos permitidos
    // server: {
    //     process: '/ruta-del-servidor', // URL donde se enviarán los archivos
    //     revert: null, // URL para revertir la subida (opcional)
    //     headers: {
    //         'X-CSRF-TOKEN': csrfToken // Si usas CSRF, asegúrate de pasar el token aquí
    //     }
    // }
  });

  
  /* multiple upload */
  const MultipleElement = document.querySelector('.multiple-filepond');
  file_pond_brochure = FilePond.create(MultipleElement, FilePond_Facturacion_LabelsES );
  //filePondInstances.push(file_pond_brochure); // Guarda la instancia en el arreglo
  // Ensure mediumZoom is available before using it
  // document.addEventListener("DOMContentLoaded", function() {
  //   file_pond_brochure.on('addfile', (error, file) => {
  //     if (!error) {
  //       setTimeout(() => {
  //         mediumZoom('.filepond--image-preview');
  //       }, 100); // Delay to ensure image is rendered
  //     }
  //   });
  // });

})();
