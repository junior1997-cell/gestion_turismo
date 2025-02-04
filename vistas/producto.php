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

    <link rel="stylesheet" href="../assets/libs/quill/quill.snow.css">
    <link rel="stylesheet" href="../assets/libs/quill/quill.bubble.css">


  </head>

  <body id="body-productos">
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
                  <button class="btn-modal-effect btn btn-primary label-btn btn-agregar m-r-10px" onclick="show_hide_form(2);  limpiar_form_producto(); create_code_producto('PR');"> <i class="ri-user-add-line label-btn-icon me-2"></i>Agregar </button>
                  <button type="button" class="btn btn-danger btn-cancelar m-r-10px" onclick="show_hide_form(1);" style="display: none;"><i class="ri-arrow-left-line"></i></button>
                  <button class="btn-modal-effect btn btn-success label-btn btn-guardar m-r-10px" style="display: none;"> <i class="ri-save-2-line label-btn-icon me-2"></i> Guardar </button>
                  <div>
                    <p class="fw-semibold fs-18 mb-0">Productos</p>
                    <span class="fs-semibold text-muted">Administra los productos.</span>
                  </div>
                </div>
              </div>
              <div class="btn-list mt-md-0 mt-2">
                <nav>
                  <ol class="breadcrumb mb-0">
                    <!-- <li class="breadcrumb-item">
                    <div class="form-check form-switch mb-0">
                      <label class="form-check-label" for="generar-cod-correlativo"></label>
                      <input class="form-check-input cursor-pointer" type="checkbox" id="generar-cod-correlativo" name="generar-cod-correlativo" onchange="create_code_producto('PR');" checked data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Activar generador código de barra correlativamente automático">
                    </div>
                  </li> -->
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Productos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Articulos</li>
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
                      <!-- ::::::::::::::::::::: FILTRO CATEGORIA :::::::::::::::::::::: -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                        <div class="form-group">
                          <label for="filtro_categoria" class="form-label">
                            <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_categoria();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                            Categoría
                            <span class="charge_filtro_categoria"></span>
                          </label>
                          <select class="form-control" name="filtro_categoria" id="filtro_categoria" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> <!-- lista de categorias --> </select>
                        </div>
                      </div>
                      <!-- ::::::::::::::::::::: FILTRO UNIDAD DE MEDIDA :::::::::::::::::::::: -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                        <div class="form-group">
                          <label for="filtro_unidad_medida" class="form-label">
                            <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_unidad_medida();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                            Unidad Medida
                            <span class="charge_filtro_unidad_medida"></span>
                          </label>
                          <select class="form-control" name="filtro_unidad_medida" id="filtro_unidad_medida" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> <!-- lista de categorias --> </select>
                        </div>
                      </div>
                      <!-- ::::::::::::::::::::: FILTRO MARCA :::::::::::::::::::::: -->
                      <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                        <div class="form-group">
                          <label for="filtro_marca" class="form-label">
                            <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_filtro_marca();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                            Unidad Marca
                            <span class="charge_filtro_marca"></span>
                          </label>
                          <select class="form-control" name="filtro_marca" id="filtro_marca" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> <!-- lista de categorias --> </select>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <!-- ------------ Tabla de Productos ------------- -->
                      <div class="table-responsive" id="div-tabla">
                        <table class="table table-bordered w-100" style="width: 100%;" id="tabla-productos">
                          <thead>
                            <tr>
                              <th colspan="15" class="bg-danger buscando_tabla" style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
                            </tr>
                            <tr>
                              <th style="border-top: 1px solid #f3f3f3 !important;" class="text-center">#</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;" class="text-center">Acciones</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Código</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Nombre</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Stock</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">U.M.</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Compra</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Venta</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Descripción</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Estado</th>

                              <th style="border-top: 1px solid #f3f3f3 !important;">Categoria</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Marca</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Nombre</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Código</th>
                              <th style="border-top: 1px solid #f3f3f3 !important;">Código Alterno</th>
                            </tr>
                          </thead>
                          <tbody></tbody>
                          <tfoot>
                            <tr>
                              <th class="text-center">#</th>
                              <th class="text-center">Acciones</th>
                              <th>Código</th>
                              <th>Nombre</th>
                              <th>Stock</th>
                              <th>U.M.</th>
                              <th>Precio Compra</th>
                              <th>Precio Venta</th>
                              <th>Descripción</th>
                              <th>Estado</th>

                              <th>Categoria</th>
                              <th>Marca</th>
                              <th>Nombre</th>
                              <th>Código</th>
                              <th>Código Alterno</th>
                            </tr>
                          </tfoot>

                        </table>

                      </div>
                      <!-- ------------ Formulario de Productos ------------ -->
                      <div class="div-form" style="display: none;">
                        <form name="form-agregar-producto" id="form-agregar-producto" method="POST" class="needs-validation" novalidate>
                          <div class="row gy-2" id="cargando-1-formulario">
                            <!-- ID -->
                            <input type="hidden" name="idproducto" id="idproducto" />
                            <input type="hidden" name="tipo" id="tipo" value="PR" />

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
                                <input type="text" class="form-control " name="codigo_alterno" id="codigo_alterno" onkeyup="mayus(this);" placeholder="ejemp: PR00001" />
                              </div>
                            </div>
                            <!-- ----------------- Unidad Medida --------------- -->
                            <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2">
                              <div class="form-group">
                                <label for="u_medida" class="form-label">
                                  <!-- <span class="badge bg-success m-r-4px cursor-pointer"  onclick=" modal_add_u_medida(); limpiar_form_um();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span> -->
                                  <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_idunidad_medida();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                  U. Medida
                                  <span class="charge_idunidad_medida"></span>
                                </label>
                                <select class="form-control" name="u_medida" id="u_medida">
                                  <!-- lista de u medidas -->
                                </select>
                              </div>
                            </div>
                            <!-- ----------------- Categoria --------------- -->
                            <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                              <div class="form-group">
                                <label for="categoria" class="form-label">
                                  <span class="badge bg-success m-r-4px cursor-pointer" onclick=" modal_add_categoria(); limpiar_form_cat();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span>
                                  <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_idcategoria();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                  Categoría
                                  <span class="charge_idcategoria"></span>
                                </label>
                                <select class="form-control" name="categoria" id="categoria">
                                  <!-- lista de categorias -->
                                </select>
                              </div>
                            </div>

                            <!-- ----------------- Marca --------------- -->
                            <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                              <div class="form-group">
                                <label for="marca" class="form-label">
                                  <span class="badge bg-success m-r-4px cursor-pointer" onclick=" modal_add_marca(); limpiar_form_marca();" data-bs-toggle="tooltip" title="Agregar"><i class="las la-plus"></i></span>
                                  <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_idmarca();" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                                  Marca
                                  <span class="charge_idmarca"></span>
                                </label>
                                <select class="form-control" name="marca" id="marca">
                                  <!-- lista de marcas -->
                                </select>
                              </div>
                            </div>
                            <!-- --------- NOMBRE ------ -->
                            <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                              <div class="form-group">
                                <label for="nombre" class="form-label">Nombre(*)</label>
                                <textarea class="form-control" name="nombre" id="nombre" rows="1"></textarea>
                              </div>
                            </div>

                            <!-- --------- DESCRIPCION ------ -->
                            <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                              <div class="form-group">
                                <label for="descripcion" class="form-label">Descripción(*)</label>
                                <textarea class="form-control" name="descripcion" id="descripcion" rows="1"></textarea>
                              </div>
                            </div>

                            <!-- ----------------- STOCK --------------- -->
                            <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                              <div class="form-group">
                                <label for="stock" class="form-label">Stock(*)</label>
                                <input type="number" class="form-control" name="stock" id="stock" />
                              </div>
                            </div>

                            <!-- ----------------- STOCK MININO --------------- -->
                            <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                              <div class="form-group">
                                <label for="stock_min" class="form-label">Stock Minimo(*)</label>
                                <input type="number" class="form-control" name="stock_min" id="stock_min" />
                              </div>
                            </div>

                            <!-- ----------------- PRECIO COMPRA --------------- -->
                            <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                              <div class="form-group">
                                <label for="precio_c" class="form-label">Precio Compra(*)</label>
                                <input type="number" class="form-control" name="precio_c" id="precio_c" step="0.01" />
                              </div>
                            </div>

                            <!-- ----------------- PRECIO VENTA --------------- -->
                            <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3 mt-3">
                              <div class="form-group">
                                <label for="precio_v" class="form-label">Precio Venta(*)</label>
                                <input type="number" class="form-control" name="precio_v" id="precio_v" step="0.01" />
                              </div>
                            </div>

                            <!-- ----------------- PRECIO X MAYOR --------------- -->
                            <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2 mt-3">
                              <div class="form-group">
                                <label for="precio_x_mayor" class="form-label">Precio por Mayor</label>
                                <input type="number" class="form-control" name="precio_x_mayor" id="precio_x_mayor" step="0.01" placeholder="precioB" />
                              </div>
                            </div>

                            <!-- ----------------- PRECIO DISTRIBUIDOR --------------- -->
                            <div class="col-md-3 col-lg-3 col-xl-2 col-xxl-2 mt-3">
                              <div class="form-group">
                                <label for="precio_dist" class="form-label">Precio Distribuidor</label>
                                <input type="number" class="form-control" name="precio_dist" id="precio_dist" step="0.01" placeholder="precioC" />
                              </div>
                            </div>

                            <!-- ----------------- PRECIO ESPECIAL --------------- -->
                            <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6 mt-3">
                              <div id="editor">
                              </div>
                            </div>

                            <!-- Imgen -->
                            <div class="col-md-4 col-lg-4 mt-4">
                              <span class=""> <b>Imagen Prducto</b> </span>
                              <div class="mb-4 mt-2 d-sm-flex align-items-center">
                                <div class="mb-0 me-5">
                                  <span class="avatar avatar-xxl avatar-rounded">
                                    <img src="../assets/modulo/productos/no-producto.png" alt="" id="imagenmuestraProducto" onerror="this.src='../assets/modulo/productos/no-producto.png';">
                                    <a href="javascript:void(0);" class="badge rounded-pill bg-primary avatar-badge cursor-pointer">
                                      <input type="file" class="position-absolute w-100 h-100 op-0" name="imagenProducto" id="imagenProducto" accept="image/*">
                                      <input type="hidden" name="imagenactualProducto" id="imagenactualProducto">
                                      <i class="fe fe-camera  "></i>
                                    </a>
                                  </span>
                                </div>
                                <div class="btn-group">
                                  <a class="btn btn-primary" onclick="cambiarImagen()"><i class='bx bx-cloud-upload bx-tada fs-5'></i> Subir</a>
                                  <a class="btn btn-light" onclick="removerImagen()"><i class="bi bi-trash fs-6"></i> Remover</a>
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
                          <div class="p-l-25px col-lg-12" id="barra_progress_producto_div" style="display: none;">
                            <div class="progress progress-lg custom-progress-3" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                              <div id="barra_progress_producto" class="progress-bar" style="width: 0%">
                                <div class="progress-bar-value">0%</div>
                              </div>
                            </div>
                          </div>
                          <!-- Submit -->
                          <button type="submit" style="display: none;" id="submit-form-producto">Submit</button>

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


            <!-- MODAL - AGREGAR CATEGORIA -->
            <div class="modal fade modal-effect" id="modal-agregar-categoria" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-categoriaLabel">
              <div class="modal-dialog modal-md modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title" id="modal-agregar-categoriaLabel1">Registrar Categoría</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form name="formulario-categoria" id="formulario-categoria" method="POST" class="needs-validation" novalidate>
                      <div class="row gy-2" id="cargando-3-fomulario">
                        <input type="hidden" name="idcategoria" id="idcategoria">

                        <div class="col-md-12">
                          <div class="form-label">
                            <label for="nombre_cat" class="form-label">Nombre(*)</label>
                            <input type="text" class="form-control" name="nombre_cat" id="nombre_cat" onkeyup="mayus(this);" />
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="descr_cat" class="form-label">Descripción(*)</label>
                            <input type="text" class="form-control" name="descr_cat" id="descr_cat" onkeyup="mayus(this);" />
                          </div>
                        </div>
                      </div>
                      <div class="row" id="cargando-4-fomulario" style="display: none;">
                        <div class="col-lg-12 text-center">
                          <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                          <h4 class="bx-flashing">Cargando...</h4>
                        </div>
                      </div>
                      <button type="submit" style="display: none;" id="submit-form-categoria">Submit</button>
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_cat();"><i class="las la-times fs-lg"></i> Close</button>
                    <button type="button" class="btn btn-primary" id="guardar_registro_categoria"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::Modal-Agregar-Cartegoria -->


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
        $descripcion = 'Lista de Producto del sistema!';
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

    <!-- Internal Quill JS -->
    <script src="../assets/js/quill-editor.js"></script>

    <script src="scripts/producto.js?version_jdl=1.39"></script>
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