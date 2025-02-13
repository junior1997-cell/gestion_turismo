<?php
//Activamos el almacenamiento en el buffer
ob_start();
date_default_timezone_set('America/Lima');
require "../config/funcion_general.php";
session_start();
if (!isset($_SESSION["user_nombre"])) {
  header("Location: index.php?file=" . basename($_SERVER['PHP_SELF']));
} else {

?>
  <!DOCTYPE html>
  <html lang="es" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-bg-img="bgimg4" data-header-styles="light" data-menu-styles="dark" data-toggled="icon-overlay-close">

  <head>
    <?php $title_page = "Productos";
    include("template/head.php"); ?>

    <!-- summernote -->
    <link rel="stylesheet" href="../assets/libs/summernote/summernote-bs4.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">


    <link rel="stylesheet" href="../assets/libs/quill/quill.snow.css">
    <link rel="stylesheet" href="../assets/libs/quill/quill.bubble.css">


    <link rel="stylesheet" href="../assets/libs/filepond/filepond.min.css">
    <link rel="stylesheet" href="../assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css">
    <link rel="stylesheet" href="../assets/libs/filepond-plugin-image-edit/filepond-plugin-image-edit.min.css">
    <link rel="stylesheet" href="../assets/libs/dropzone/dropzone.css">
    <!-- GLightbox CSS -->
    <link rel="stylesheet" href="../assets/libs/glightbox/css/glightbox.min.css">

    <style>
      .imagen-metodo-pago img {
        /*width: 100% !important;  Ajusta el ancho al contenedor */
        /*height: auto !important;  Mantén la proporción de aspecto */
        width: 140px !important;
        /* Máximo ancho permitido */
        height: 130px !important;
        /* Máximo alto permitido */
        object-fit: contain !important;
        /* Asegura que la imagen no se deforme */
        border: 1px solid #ddd !important;
        /* Opcional: agrega un borde para resaltar el contenedor */
        box-sizing: border-box !important;
      }

      .div_pago_rapido img {
        width: 60px;
        /* Ajusta el tamaño de las imágenes */
        height: 100%;
        cursor: pointer;
        border: 3px solid #ccc;
        border-radius: 5px;
        transition: border-color 0.3s ease;
        /* Suaviza la transición */
      }

      .div_pago_rapido img:hover {
        border-color: #007bff;
        /* Cambia el borde al pasar el ratón */
      }
    </style>
  </head>

  <body id="body-tours">
    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>
      <?php if ($_SESSION['producto'] == 1) { ?> <!-- .:::: PERMISO DE MODULO ::::. -->

        <!-- Start::app-content -->
        <div class="main-content app-content">
          <div class="container-fluid">

            <!-- Start::page-header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
              <div>
                <div class="d-md-flex d-block align-items-center ">
                  <button class="btn-modal-effect btn btn-primary label-btn btn-agregar m-r-10px" onclick="show_hide_form(2);  limpiar_form_tours(); create_code_tours('TR');"> <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                  <button type="button" class="btn btn-danger btn-cancelar m-r-10px" onclick="show_hide_form(1);" style="display: none;"><i class="ri-arrow-left-line"></i></button>
                  <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button>
                  <div>
                    <p class="fw-semibold fs-18 mb-0">Tours</p>
                    <span class="fs-semibold text-muted">Administra de manera eficiente todos tus Tours.</span>
                  </div>
                </div>
              </div>
              <div class="btn-list mt-md-0 mt-2">
                <nav>
                  <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Tours</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Turismo</li>
                  </ol>
                </nav>
              </div>
            </div>
            <!-- End::page-header -->

            <!-- Start::row-1 -->
            <div class="row">
              <div class="col-xxl-12 col-xl-12">
                <div>
                  <div class="card custom-card">
                    <div class="card-header">

                    </div>
                    <div class="card-body">

                      <!-- ------------ Tabla de Productos ------------- -->
                      <div class="table-responsive" id="div-tabla">
                        <table class="table table-bordered w-100" style="width: 100%;" id="tabla-tours">
                          <thead>
                            <tr>
                              <th colspan="15" class="bg-danger buscando_tabla" style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
                            </tr>
                            <tr>
                              <th style="border-top: 1px solid #f3f3f3 !important;" class="text-center">#</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;" class="text-center">Acc</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Código</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Nombre</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Cuidad</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">P. Publico</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">P. Corporativo</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">P. Tours</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Duración</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Estado</th>
                            </tr>
                          </thead>
                          <tbody></tbody>
                          <tfoot>
                            <tr>
                              <th style="border-top: 1px solid #f3f3f3 !important;" class="text-center">#</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;" class="text-center">Acc</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Código</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Nombre</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Cuidad</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">P. Publico</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">P. Corporativo</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">P. Tours</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Duración</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Estado</th>
                            </tr>
                          </tfoot>

                        </table>


                      </div>
                      <!-- ------------ Formulario de Productos ------------ -->
                      <div class="div-form" style="display: none;">
                        <form name="form-agregar-tours" id="form-agregar-tours" method="POST" class="needs-validation" novalidate>
                          <div class="row gy-2" id="cargando-1-formulario">
                            <!-- ID -->
                            <input type="hidden" name="idtours" id="idtours" />

                            <!-- ----------------- CODIGO --------------- -->
                            <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2">
                              <div class="form-group">
                                <label for="codigo" class="form-label">Código Sistema <span class="charge_codigo"></span></label>
                                <input type="text" class="form-control bg-light" name="codigo" id="codigo" onkeyup="mayus(this);" readonly data-bs-toggle="tooltip" data-bs-original-title="No se puede editar" />
                              </div>
                            </div>
                            <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2">
                              <div class="form-group">
                                <label for="codigo_alterno" class="form-label">
                                  <span class="badge bg-info m-r-4px cursor-pointer" onclick="generarcodigonarti();" data-bs-toggle="tooltip" title="Generar Codigo con el nombre de producto."><i class="las la-sync-alt"></i></span>
                                  Código Propio <span class="charge_codigo_alterno"></span>
                                </label>
                                <input type="text" class="form-control" name="codigo_alterno" id="codigo_alterno" onkeyup="mayus(this);" placeholder="ejemp: PR00001" />
                              </div>
                            </div>
                            <!-- ----------------- Turno tours --------------- -->
                            <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2">
                              <div class="form-group">
                                <label for="tours_turno" class="form-label">
                                  <span class="badge bg-success m-r-4px cursor-pointer" onclick=" modal_add_tours_turno(); limpiar_form_um();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span>
                                  <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_tours_turno();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                  Turno
                                  <span class="charge_turno_tours"></span>
                                </label>
                                <select class="form-control" name="tours_turno" id="tours_turno">
                                  <!-- lista de u medidas -->
                                </select>
                              </div>
                            </div>
                            <!-- ----------------- Ubigeo Distrito --------------- -->
                            <div class="col-md-2 col-lg-2 col-xl-2 col-xxl-2">
                              <div class="form-group">
                                <label for="ubigeo_distrito" class="form-label">
                                  <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_idubigeo_distrito();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                  Cuidad
                                  <span class="charge_idubigeo_distrito"></span>
                                </label>
                                <select class="form-control" name="ubigeo_distrito" id="ubigeo_distrito" onchange="select_cuidad();">
                                  <!-- lista de ubigeo_distritos -->
                                </select>
                              </div>
                            </div>

                            <!-- ----------------- Provincia - Departamento --------------- -->
                            <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4">
                              <div class="form-group">
                                <label for="marca" class="form-label">Provincia - Departamento</label>
                                <input type="text" class="form-control" id="prov_dep" readonly />
                              </div>
                            </div>
                            <!-- --------- nombre ------ -->
                            <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                              <div class="form-group">
                                <label for="nombre" class="form-label">Nombre(*)</label>
                                <textarea class="form-control" name="nombre" id="nombre" rows="1"></textarea>
                              </div>
                            </div>

                            <!-- --------- precio publico ------ -->
                            <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                              <div class="form-group">
                                <label for="precio_publico" class="form-label">P. Publico(*)</label>
                                <input type="number" class="form-control" name="precio_publico" id="precio_publico" />
                              </div>
                            </div>

                            <!-- ----------------- precio corporativo --------------- -->
                            <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                              <div class="form-group">
                                <label for="precio_corporativo" class="form-label">P. Corporativo(*)</label>
                                <input type="number" class="form-control" name="precio_corporativo" id="precio_corporativo" />
                              </div>
                            </div>

                            <!-- ----------------- Precio Tours --------------- -->
                            <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                              <div class="form-group">
                                <label for="precio_tours" class="form-label">P. Tours(*)</label>
                                <input type="number" class="form-control" name="precio_tours" id="precio_tours" step="0.01" />
                              </div>
                            </div>

                            <!-- ----------------- precio web --------------- -->
                            <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                              <div class="form-group">
                                <label for="precio_web" class="form-label">P. Web(*)</label>
                                <input type="number" class="form-control" name="precio_web" id="precio_web" step="0.01" />
                              </div>
                            </div>

                            <!-- ----------------- detalle duracion --------------- -->
                            <div class="col-md-3 col-lg-3 col-xl-4 col-xxl-4 mt-3">
                              <div class="form-group">
                                <label for="detalle_duracion" class="form-label">Detalle Duración</label>

                                <textarea class="form-control" name="detalle_duracion" id="detalle_duracion" rows="1" placeholder="1 hora y ...."></textarea>

                              </div>
                            </div>

                            <!-- ----------------- Incluye --------------- -->

                            <div class="col-12 col-md-6 col-lg-6 col-xl-6 col-xxl-6 mt-4">
                              <div class="form-label">
                                <label for="incluye" class="form-label">Incluye <samp>(Resumen)</samp> </label>
                                <div id="detalle_incluye">
                                </div>
                              </div>
                            </div>
                            <!-- ----------------- Program Turistico --------------- -->

                            <div class="col-12 col-md-6 col-lg-6 col-xl-6 col-xxl-6 mt-4">
                              <div class="form-label">
                                <label for="precio_publico" class="form-label">Program Turistico <samp>(Resumen)</samp> </label>
                                <div id="detalle_programa_turistico">
                                </div>
                              </div>
                            </div>

                            <!-- Imgen -->
                            <div class="col-md-4 col-lg-4 mt-4 content-metodo-pago-1">
                              <span class=""> <b>Brochure</b> </span>
                              <div class="row">
                                <!-- Baucher -->
                                <div class="col-sm-6 col-lg-6 col-xl-6 pt-3">
                                  <div class="form-group">
                                    <input type="file" class="multiple-filepond " multiple name="brochure" id="brochure_1" data-allow-reorder="true" data-max-file-size="3MB" accept="image/*, application/pdf">
                                    <input type="hidden" name="brochure_old" id="brochure_old">
                                  </div>
                                </div>
                              </div>
                            </div>

                          </div>
                          <div class="row" id="cargando-2-fomulario" style="display: none;">
                            <div class="col-lg-12 text-center">
                              <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                              <h4 class="bx-flashing">Cargando...</h4>
                            </div>
                          </div>
                          <!-- Chargue -->
                          <div class="p-l-25px col-lg-12" id="barra_progress_tours_div" style="display: none;">
                            <div class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                              <div id="barra_progress_tours" class="progress-bar" style="width: 0%">
                                <div class="progress-bar-value">0%</div>
                              </div>
                            </div>
                          </div>
                          <!-- Submit -->
                          <button type="submit" style="display: none;" id="submit-form-tours">Submit</button>

                        </form>
                      </div>
                    </div>
                    <div class="card-footer border-top-0">
                      <button type="button" class="btn btn-danger btn-cancelar" onclick="show_hide_form(1);" style="display: none;"><i class="las la-times fs-lg"></i> Cancelar</button>
                      <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::row-1 -->


            <!-- MODAL - VER DETALLE -->
            <div class="modal fade modal-effect" id="modal-ver-detalle-producto" tabindex="-1" aria-labelledby="modal-ver-detalle-productoLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title" id="modal-ver-detalle-productoLabel1"><b>Detalles</b> - Producto</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div id="html-detalle-producto"></div>
                    <div class="text-center" id="html-detalle-imagen"></div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"><i class="las la-times"></i> Close</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::Modal-VerDetalles -->


            <!-- MODAL - AGREGAR MARCA -->
            <div class="modal fade modal-effect" id="modal-agregar-marca" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-marcaLabel">
              <div class="modal-dialog modal-md modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title" id="modal-agregar-marcaLabel1">Registrar Marca</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form name="formulario-marca" id="formulario-marca" method="POST" class="needs-validation" novalidate>
                      <div class="row gy-2" id="cargando-5-fomulario">
                        <input type="hidden" name="idmarca" id="idmarca">

                        <div class="col-md-12">
                          <div class="form-label">
                            <label for="nombre_marca" class="form-label">Nombre(*)</label>
                            <input type="text" class="form-control" name="nombre_marca" id="nombre_marca" onkeyup="mayus(this);" />
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="descr_marca" class="form-label">Descripción(*)</label>
                            <input type="text" class="form-control" name="descr_marca" id="descr_marca" onkeyup="mayus(this);" />
                          </div>
                        </div>
                      </div>
                      <div class="row" id="cargando-6-fomulario" style="display: none;">
                        <div class="col-lg-12 text-center">
                          <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                          <h4 class="bx-flashing">Cargando...</h4>
                        </div>
                      </div>
                      <button type="submit" style="display: none;" id="submit-form-marca">Submit</button>
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_marca();"><i class="las la-times fs-lg"></i> Close</button>
                    <button type="button" class="btn btn-primary" id="guardar_registro_marca"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::Modal-Agregar-Marca -->


            <!-- MODAL - AGREGAR UM -->
            <div class="modal fade modal-effect" id="modal-agregar-u-m" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-u-mLabel">
              <div class="modal-dialog modal-md modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title" id="modal-agregar-u-mLabel1">Registrar Unidad de Medida</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form name="formulario-u-m" id="formulario-u-m" method="POST" class="row needs-validation" novalidate>
                      <div class="row gy-2" id="cargando-1-fomulario">
                        <input type="hidden" name="idsunat_unidad_medida" id="idsunat_unidad_medida">


                        <div class="col-md-6">
                          <div class="form-label">
                            <label for="nombre_um" class="form-label">Nombre(*)</label>
                            <input type="text" class="form-control" name="nombre_um" id="nombre_um" onkeyup="mayus(this);" />
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="descr_um" class="form-label">Descripción(*)</label>
                            <input type="text" class="form-control" name="descr_um" id="descr_um" onkeyup="mayus(this);" />
                          </div>
                        </div>
                      </div>
                      <div class="row" id="cargando-2-fomulario" style="display: none;">
                        <div class="col-lg-12 text-center">
                          <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                          <h4 class="bx-flashing">Cargando...</h4>
                        </div>
                      </div>
                      <button type="submit" style="display: none;" id="submit-form-u-m">Submit</button>
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_um();"><i class="las la-times fs-lg"></i> Close</button>
                    <button type="button" class="btn btn-primary" id="guardar_registro_u_m"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::Modal-registrar-unidad-medida -->

          </div>
        </div>
        <!-- End::app-content -->
      <?php } else {
        $title_submodulo = 'Producto';
        $precio_publico = 'Lista de Producto del sistema!';
        $title_modulo = 'Articulos';
        include("403_error.php");
      } ?>

      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>
    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>

    <!-- Quill Editor JS -->
    <script src="../assets/libs/quill/quill.min.js"></script>

    <!-- Filepond JS -->
    <script src="../assets/libs/filepond/filepond.min.js"></script>
    <script src="../assets/libs/filepond/locale/es-es.js"></script>
    <script src="../assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js"></script>
    <script src="../assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js"></script>
    <script src="../assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js"></script>
    <script src="../assets/libs/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js"></script>
    <script src="../assets/libs/filepond-plugin-image-edit/filepond-plugin-image-edit.min.js"></script>
    <script src="../assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js"></script>
    <script src="../assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js"></script>
    <script src="../assets/libs/filepond-plugin-image-crop/filepond-plugin-image-crop.min.js"></script>
    <script src="../assets/libs/filepond-plugin-image-resize/filepond-plugin-image-resize.min.js"></script>
    <script src="../assets/libs/filepond-plugin-image-transform/filepond-plugin-image-transform.min.js"></script>
    <script src="https://unpkg.com/medium-zoom/dist/medium-zoom.min.js"></script>

    <!-- Dropzone JS -->
    <script src="../assets/libs/dropzone/dropzone-min.js"></script>
    <!-- Gallery JS -->
    <script src="../assets/libs/glightbox/js/glightbox.min.js"></script>

    <!-- Select2 Cdn -->
    <script src="scripts/tours.js?version_jdl=1.39"></script>

    <script>
      $(function() {

        $('[data-bs-toggle="tooltip"]').tooltip();
      });
    </script>


  </body>



  </html>
<?php
}
ob_end_flush();
?>