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
  <html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="icon-overlay-close">

  <head>

    <?php $title_page = "Clientes";
    include("template/head.php"); ?>

  </head>
  <style>
    .style_tabla_datatable td,
    tr {
      font-size: 11px;
      /* Reducir el tamaño de la fuente */
      padding: 5px;
      /* Ajustar el padding */
    }
  </style>

  <body id="body-usuario">

    <?php include("template/switcher.php"); ?>
    <?php include("template/loader.php"); ?>

    <div class="page">
      <?php include("template/header.php") ?>
      <?php include("template/sidebar.php") ?>
      <?php if ($_SESSION['cobro_por_trabajador'] == 1) { ?>

        <!-- Start::app-content -->
        <div class="main-content app-content">
          <div class="container-fluid">

            <!-- Start::page-header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
              <div>
                <div class="d-md-flex d-block align-items-center ">
                  <div>
                    <p class="fw-semibold fs-18 mb-0">Lista de Cobros!</p>
                    <span class="fs-semibold text-muted">Reporte de Cobros por trabajador.</span>
                  </div>
                </div>
              </div>

              <div class="btn-list mt-md-0 mt-2 mb-2">
                <nav>
                  <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Cobros</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Home</li>
                  </ol>
                </nav>
              </div>
            </div>
            <!-- End::page-header -->

            <!-- Start::row-1 -->
            <div class="row">
              <div class="col-xxl-12 col-xl-12 ">
                <div class="card custom-card">
                  <div class="card-header div-filtro row" style="gap: 0px !important;">

                    <!-- ::::::::::::::::::::: FILTRO TRABAJADOR :::::::::::::::::::::: -->
                    <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                      <div class="form-group">
                        <label for="filtro_trabajador" class="form-label">
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_trabajador');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                          Trabajador
                          <span class="charge_filtro_trabajador"></span>
                        </label>
                        <select class="form-control" name="filtro_trabajador" id="filtro_trabajador" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> <!-- lista de categorias --> </select>
                      </div>
                    </div>
                    <!-- ::::::::::::::::::::: FILTRO AÑO DE PAGO :::::::::::::::::::::: -->
                    <div class="col-md-3 col-lg-2 col-xl-2 col-xxl-2">
                      <div class="form-group">
                        <label for="filtro_p_all_anio_pago" class="form-label">
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_anio_pago');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                          Año de Pago
                          <span class="charge_filtro_p_all_anio_pago"></span>
                        </label>
                        <select class="form-control" name="filtro_p_all_anio_pago" id="filtro_p_all_anio_pago" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> <!-- lista de categorias --> </select>
                      </div>
                    </div>

                    <!-- ::::::::::::::::::::: FILTRO MES :::::::::::::::::::::: -->
                    <div class="col-md-3 col-lg-2 col-xl-2 col-xxl-2">
                      <div class="form-group">
                        <label for="filtro_p_all_mes_pago" class="form-label">
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_p_all_mes_pago');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                          Mes de Pago
                          <span class="charge_filtro_p_all_mes_pago"></span>
                        </label>
                        <select class="form-control" name="filtro_p_all_mes_pago" id="filtro_p_all_mes_pago" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> </select>
                      </div>
                    </div>
                    <!-- ::::::::::::::::::::: FILTRO COBRO :::::::::::::::::::::: -->
                    <div class="col-md-3 col-lg-2 col-xl-2 col-xxl-2">
                      <div class="form-group">
                        <label for="filtro_p_all_es_cobro" class="form-label">
                          <!-- <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_p_all_es_cobro');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span> -->
                          ¿ ES COBRO ?
                          <span class="charge_filtro_p_all_es_cobro"></span>
                        </label>
                        <select class="form-control" name="filtro_p_all_es_cobro" id="filtro_p_all_es_cobro" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> 
                        <option value="SI">SI</option>
                        <option value="NO">NO</option>
                        </select>
                      </div>
                    </div>
                    <!-- ::::::::::::::::::::: FILTRO TIPO COMPROBANTE :::::::::::::::::::::: -->
                    <div class="col-md-3 col-lg-3 col-xl-3 col-xxl-3">
                      <div class="form-group">
                        <label for="filtro_tipo_comprob" class="form-label">
                          <span class="badge bg-info m-r-4px cursor-pointer" onclick="reload_select('filtro_tipo_comprob');" data-bs-toggle="tooltip" title="Actualizar"><i class="las la-sync-alt"></i></span>
                          Tipo Comprobante
                          <span class="charge_filtro_tipo_comprob"></span>
                        </label>
                        <select class="form-control" name="filtro_tipo_comprob" id="filtro_tipo_comprob" onchange="cargando_search(); delay(function(){filtros()}, 50 );"> <!-- lista de categorias --> </select>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <!--Tabla reporte-->
                      <div class="col-12 col-lg-6 col-xxl-6">
                        <div class="row">
                          <div class="col-12">
                            <div class="card-body">
                              <p class="fw-semibold fs-18 mb-2 text-center bg-light">Tabla de Cobros por Trabajador!</p>
                              <div id="div-tabla" class="table-responsive">
                                <table id="tabla-cliente" class="table table-bordered w-100 style_tabla_datatable" style="width: 100%;">
                                  <thead class="buscando_tabla">
                                    <tr id="id_buscando_tabla">
                                      <th colspan="20" class="bg-danger " style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
                                    </tr>
                                    <tr>
                                      <th class="text-center">#</th>
                                      <th>Cobro?</th>
                                      <th>Cliente</th>
                                      <th>Correlativo</th>
                                      <th>Total</th>
                                      <th>Asignado</th>
                                      <th>Cobrador</th>
                                      <th>Periodo</th>
                                      <th>Creación</th>
                                      <th>Nombre</th>
                                      <th>Documento</th>
                                      <th>Celular</th>

                                    </tr>
                                  </thead>
                                  <tbody></tbody>
                                  <tfoot>
                                    <tr>
                                      <th class="text-center">#</th>
                                      <th>Cobro?</th>
                                      <th>Cliente</th>
                                      <th>Correlativo</th>
                                      <th>Total</th>
                                      <th>Asignado</th>
                                      <th>Cobrador</th>
                                      <th>Periodo</th>
                                      <th>Creación</th>
                                      <th>Nombre</th>
                                      <th>Documento</th>
                                      <th>Celular</th>

                                    </tr>
                                  </tfoot>
                                </table>
                              </div>
                            </div>
                          </div>
                          <!--Tabla clientes que no pagan-->
                          <div class="col-12">
                            <div class="alert alert-solid-warning alert-dismissible fade show div_alert_c_t">
                              <h6> Lista de clientes que no realizaron el pago. <strong> Para poder ver; debe selecccionar el Trabajador, Año y Mes</strong></h6>
                            </div>
                            <div class="card-body div_tbl_cxt" style="display: none;">
                              <p class="fw-semibold fs-18 mb-2 text-center bg-light">Tabla de Clientes por Cobrar - Trabajador!</p>
                              <div id="div-tabla" class="table-responsive">
                                <table id="tabla-cliente_x_cobrar" class="table table-bordered w-100 style_tabla_datatable" style="width: 100%;">
                                  <thead class="buscando_tabla_x_c">
                                    <tr id="id_busc_tbl_cobros_x_c">
                                      <th colspan="20" class="bg-danger " style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
                                    </tr>
                                    <tr>
                                      <th class="text-center">#</th>
                                      <th>Cliente</th>
                                      <th>Plan</th>
                                      <th>Monto</th>
                                      <th>Cancelación</th>
                                      <th>Nombre Cliente</th>
                                      <th>Num. Celular</th>
                                    </tr>
                                  </thead>
                                  <tbody></tbody>
                                  <tfoot>
                                    <th class="text-center">#</th>
                                    <th>Cliente</th>
                                    <th>Plan</th>
                                    <th>Monto</th>
                                    <th>Cancelación</th>
                                    <th>Nombre Cliente</th>
                                    <th>Num. Celular</th>
                                    </tr>
                                  </tfoot>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!--Graficos del reporte-->
                      <div class="col-12 col-xl-6 col-xxl-6 ">
                        <div class="row">
                          <!-- card de montos -->
                          <div class="col-xl-12 col-xxl-12">
                            <div class="row">
                              <div class="col-xxl-4 col-lg-4 col-md-6">
                                <div class="card custom-card overflow-hidden">
                                  <div class="card-body" style=" padding: 5px !important; ">
                                    <div class="d-flex align-items-top justify-content-between">
                                      <div>
                                        <span class="avatar avatar-md avatar-rounded bg-primary">
                                          <i class="fa-solid fa-wallet"></i>
                                        </span>
                                      </div>
                                      <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                          <div>
                                            <p class="text-muted mb-0 tiket_info ">Tiket <span class="ms-1 badge bg-secondary-transparent cantidad_t count-up" data-count="0">0</span> </p>
                                            <h7 class="fw-semibold mt-1 total_tiket">S/ 0.00</h7>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-xxl-4 col-lg-4 col-md-6">
                                <div class="card custom-card overflow-hidden">
                                  <div class="card-body" style=" padding: 5px !important; ">
                                    <div class="d-flex align-items-top justify-content-between">
                                      <div>
                                        <span class="avatar avatar-md avatar-rounded bg-secondary">
                                          <i class="ti ti-wallet fs-16"></i>
                                        </span>
                                      </div>
                                      <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                          <div>
                                            <p class="text-muted mb-0 boleta_info ">Boleta <span class="ms-1 badge bg-secondary-transparent cantidad_t count-up" data-count="0">0</span> </p>
                                            <h7 class="fw-semibold mt-1 total_boleta">S/ 0.00</h7>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-xxl-4 col-lg-4 col-md-6">
                                <div class="card custom-card overflow-hidden">
                                  <div class="card-body" style=" padding: 5px !important; ">
                                    <div class="d-flex align-items-top justify-content-between">
                                      <div>
                                        <span class="avatar avatar-md avatar-rounded bg-success">
                                          <i class="ti ti-wave-square fs-16"></i>
                                        </span>
                                      </div>
                                      <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                          <div>
                                            <p class="text-muted mb-0 factura_info ">Factura <span class="ms-1 badge bg-secondary-transparent cantidad_t count-up" data-count="0">0</span> </p>
                                            <h7 class="fw-semibold mt-1 total_factura">S/ 0.00</h7>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-xxl-4 col-lg-4 col-md-6 mx-auto">
                                <div class="card custom-card overflow-hidden">
                                  <div class="card-body" style=" padding: 5px !important; ">
                                    <div class="d-flex align-items-top justify-content-between">
                                      <div>
                                        <span class="avatar avatar-md avatar-rounded bg-warning">
                                          <i class="ti ti-briefcase fs-16"></i>
                                        </span>
                                      </div>
                                      <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                          <div>
                                            <p class="text-muted mb-0 total_info ">Total <span class="ms-1 badge bg-secondary-transparent cantidad_t count-up" data-count="10">10</span> </p>
                                            <h7 class="fw-semibold mt-1 total_general">S/ 0.00</h7>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-xxl-4 col-lg-4 col-md-6 mx-auto">
                                <div class="card custom-card overflow-hidden">
                                  <div class="card-body" style=" padding: 5px !important; ">
                                    <div class="d-flex align-items-top justify-content-between">
                                      <div>
                                        <span class="avatar avatar-md avatar-rounded bg-warning">
                                          <i class="ti ti-briefcase fs-16"></i>
                                        </span>
                                      </div>
                                      <div class="flex-fill ms-3">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap">
                                          <div>
                                            <p class="text-muted mb-0 total_pendiente ">Pendiente <span class="ms-1 badge bg-secondary-transparent cantidad_t count-up" data-count="120">120</span> </p>
                                            <h7 class="fw-semibold mt-1 total_g_pend">S/ 0.00</h7>
                                          </div>
                                          <div id="crm-total-deals"></div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!--Graficos de pastel-->
                          <div class="col-xl-12 col-xxl-12">
                            <div class="card custom-card">
                              <div class="card-header">
                                <div class="card-title">Montos por Persona que Cobro</div>
                              </div>
                              <div class="card-body">
                                <div id="donut-simple"></div>
                              </div>
                            </div>
                          </div>

                          <div class="col-xl-12 col-xxl-12">
                            <div class="card custom-card">
                              <div class="card-header">
                                <div class="card-title">Montos por Producto o Servicio</div>
                              </div>
                              <div class="card-body">
                                <div class="table-responsive">
                                  <table id="tabla-x-producto" class="table text-nowrap table-bordered table-hover border-primary">
                                    <thead>
                                      <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Producto</th>
                                        <th scope="col">Cant.</th>
                                        <th scope="col">Costo</th>
                                        <th scope="col">Involucrados</th>
                                      </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                      <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Producto</th>
                                        <th scope="col">Cant.</th>
                                        <th scope="col" class="text-right total_x_producto">Costo</th>
                                        <th scope="col">Involucrados</th>
                                      </tr>
                                    </tfoot>
                                  </table>
                                </div>

                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End::row-1 -->

          </div>
        </div>
        <!-- End::app-content -->

      <?php } else {
        $title_submodulo = 'Clientes';
        $descripcion = 'Lista de Clientes del sistema!';
        $title_modulo = 'Ventas';
        include("403_error.php");
      } ?>


      <?php include("template/search_modal.php"); ?>
      <?php include("template/footer.php"); ?>

    </div>

    <?php include("template/scripts.php"); ?>
    <?php include("template/custom_switcherjs.php"); ?>

    <!-- Apex Charts JS -->
    <script src="../assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- Internal Apex Pie Charts JS 
    <script src="../assets/js/apexcharts-pie.js"></script>-->


    <script src="scripts/reporte_x_trabajador.js?version_jdl=1.39"></script>
    <script>
      $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
      });
    </script>
    <script>
      /* simple donut chart 
      var options = {
        series: [44, 55, 41, 17, 15,9],
        chart: {
          type: "donut",
          height: 290,
        },
        legend: {
          position: "bottom",
        },
        colors: ["#e6533c","#845adf", "#23b7e5", "#f5b849", "#49b6f5" , "#4eac4c"],
        labels: ["Team A", "Team B", "Team C", "Team D", "Team E","Team f"],
        dataLabels: {
          dropShadow: {
            enabled: false,
          },
        },
      };
      var chart = new ApexCharts(document.querySelector("#donut-simple"), options);
      chart.render();*/
    </script>


  </body>

  </html>
<?php
}
ob_end_flush();
?>