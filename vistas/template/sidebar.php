<!-- Start::app-sidebar -->
<aside class="app-sidebar sticky" id="sidebar">

  <!-- Start::main-sidebar-header -->
  <div class="main-sidebar-header">
    <a href="escritorio.php" class="header-logo">
      <img src="../assets/images/brand-logos/desktop-logo.png" alt="logo" class="desktop-logo">
      <img src="../assets/images/brand-logos/toggle-logo.png" alt="logo" class="toggle-logo">
      <img src="../assets/images/brand-logos/desktop-dark.png" alt="logo" class="desktop-dark">
      <img src="../assets/images/brand-logos/toggle-dark.png" alt="logo" class="toggle-dark">
      <img src="../assets/images/brand-logos/desktop-white.png" alt="logo" class="desktop-white">
      <img src="../assets/images/brand-logos/toggle-white.png" alt="logo" class="toggle-white">
    </a>
  </div>
  <!-- End::main-sidebar-header -->

  <!-- Start::main-sidebar -->
  <div class="main-sidebar" id="sidebar-scroll">

    <!-- Start::nav -->
    <nav class="main-menu-container nav nav-pills flex-column sub-open">
      <div class="slide-left" id="slide-left">
        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
          <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
        </svg>
      </div>
      <ul class="main-menu">
        <!-- Start::slide__category -->
        <li class="slide__category"><span class="category-name">I N I C I O</span></li>
        <!-- End::slide__category -->

        <!-- Start::slide -->
        <?php  if ($_SESSION['dashboard'] == '1') { ?>
        <li class="slide">
          <a href="escritorio.php" class="side-menu__item">
            <i class="bx bx-home side-menu__icon"></i><span class="side-menu__label"> Dashboards</span>
          </a>
        </li>
        <?php } ?>
        <!-- End::slide -->

        <!-- Start::slide__category -->
        <li class="slide__category"><span class="category-name">L O G I S T I C A</span></li>
        <!-- End::slide__category -->
        
        <!-- Start::slide -->
        <?php  if ($_SESSION['articulo'] == '1') { ?>
        <li class="slide has-sub">
          <a href="javascript:void(0);" class="side-menu__item">
            <i class="bx bx-home side-menu__icon"></i>
            <span class="side-menu__label">Turismo<span class="badge bg-warning-transparent ms-2">8</span></span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
          </a>
          <ul class="slide-menu child1">
            <li class="slide side-menu__label1"> <a href="javascript:void(0)">Turismo</a></li>
            <?php  if ($_SESSION['producto'] == '1') { ?>
            <li class="slide"> <a href="cuidades.php" class="side-menu__item">Cuidades</a></li>
            <?php } ?>
            <?php  if ($_SESSION['servicio'] == '1') { ?>
            <li class="slide"> <a href="tours.php" class="side-menu__item">Tours</a></li>
            <?php } ?>
            <?php  if ($_SESSION['categoria_y_marca'] == '1') { ?>
            <li class="slide"> <a href="hoteles.php" class="side-menu__item">Hoteles</a></li>
            <?php } ?>
            <?php  if ($_SESSION['unidad_de_medida'] == '1') { ?>
            <li class="slide"> <a href="paquetes.php" class="side-menu__item">Paquetes</a></li>  
            <?php } ?>
            <?php  if ($_SESSION['unidad_de_medida'] == '1') { ?>
            <li class="slide"> <a href="cotizaciones.php" class="side-menu__item">Cotizaciones</a></li>  
            <?php } ?>  
            <?php  if ($_SESSION['unidad_de_medida'] == '1') { ?>
            <li class="slide"> <a href="agencias.php" class="side-menu__item">Agencias</a></li>  
            <?php } ?>      
            <?php  if ($_SESSION['unidad_de_medida'] == '1') { ?>
            <li class="slide"> <a href="transportes.php" class="side-menu__item">Transportes</a></li>  
            <?php } ?> 
            <?php  if ($_SESSION['unidad_de_medida'] == '1') { ?>
            <li class="slide"> <a href="guias_viaje.php" class="side-menu__item">Guias de viaje</a></li>  
            <?php } ?>
            <?php  if ($_SESSION['unidad_de_medida'] == '1') { ?>
            <li class="slide"> <a href="aerolineas.php" class="side-menu__item">Aerolineas</a></li>  
            <?php } ?>       
          </ul>
        </li>
        <?php } ?>
        <!-- End::slide -->

        <!-- Start::slide__category -->
        <li class="slide__category"><span class="category-name">G E S T I O N - D E - V E N T A S</span></li>
        <!-- End::slide__category -->        

        <!-- Start::slide -->
        <?php  if ($_SESSION['realizar_cobro'] == '1') { ?>
        <li class="slide has-sub">
          <a href="javascript:void(0);" class="side-menu__item">
            <i class='bx bx-cart-add side-menu__icon'></i>
            <span class="side-menu__label">Realizar cobro<span class="badge bg-secondary-transparent ms-2">New</span></span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
          </a>
          <ul class="slide-menu child1">
            <li class="slide side-menu__label1"> <a href="javascript:void(0)">Realizar venta</a> </li>
            <?php  if ($_SESSION['facturacion'] == '1') { ?>
            <li class="slide"> <a href="facturacion.php" class="side-menu__item">Facturación</a></li>
            <?php } ?>            
            <?php  if ($_SESSION['cotizacion'] == '1') { ?>
            <li class="slide"> <a href="cotizacion.php" class="side-menu__item">Cotizacion</a></li>
            <?php } ?>             
            <?php  if ($_SESSION['cliente'] == '1') { ?>
            <li class="slide"> <a href="clientes.php" class="side-menu__item">Clientes</a></li>
            <?php } ?>
            <?php  if ($_SESSION['anticipos'] == '1') { ?>
            <li class="slide"> <a href="anticipo_cliente.php" class="side-menu__item">Anticipos</a></li>
            <?php } ?>
            <?php  if ($_SESSION['facturacion'] == '1') { ?>
            <li class="slide"> <a href="periodo_facturacion.php" class="side-menu__item">Periodos Facturados</a></li>
            <?php } ?>     
          </ul>
        </li>
        <?php } ?>
        <!-- End::slide --> 

        <!-- Start::slide__category -->
        <li class="slide__category"><span class="category-name">C O N T A B I L I D A D</span></li>
        <!-- End::slide__category -->        

        <!-- Start::slide -->
        <?php  if ($_SESSION['reporte'] == '1') { ?>
        <li class="slide has-sub">
          <a href="javascript:void(0);" class="side-menu__item">
            <i class='bx bx-line-chart side-menu__icon'></i>
            <span class="side-menu__label">Reportes <span class="badge bg-secondary-transparent ms-2">New</span></span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
          </a>
          <ul class="slide-menu child1 mega-menu">
            <li class="slide side-menu__label1"> <a href="javascript:void(0)">Reportes <span class="badge bg-secondary-transparent ms-2">New</span></a></li>     
            <?php  if ($_SESSION['retraso_de_cobro'] == '1') { ?>
            <li class="slide"> <a href="retraso_cobro.php" class="side-menu__item">Retraso de Cobros </a></li>
            <?php } ?>      
            <?php  if ($_SESSION['avance_de_cobro'] == '1') { ?>
            <li class="slide"> <a href="avance_cobro.php" class="side-menu__item">Avance de Cobros </a></li>
            <?php } ?>              
            <?php  if ($_SESSION['cobro_por_trabajador'] == '1') { ?>
            <li class="slide"> <a href="reporte_x_trabajador.php" class="side-menu__item">Cobros por Trabajador</a></li>
            <?php } ?>                        
            <?php  if ($_SESSION['correo_enviado'] == '1') { ?>
            <li class="slide"> <a href="no_disponible.php" class="side-menu__item">Correo enviados</a></li>
            <?php } ?>     
          </ul>
        </li>
        <?php } ?>
        <!-- End::slide -->

        <!-- Start::slide -->
        <?php  if ($_SESSION['gastos_trabajador'] == '1') { ?>
        <li class="slide">
          <a href="gasto_de_trabajador.php" class="side-menu__item">
            <i class='bx bx-dollar-circle side-menu__icon' ></i><span class="side-menu__label"> Gastos Trabajador</span>
          </a>          
        </li>
        <?php } ?>
        <!-- End::slide --> 

        <!-- Start::slide -->
        <?php  if ($_SESSION['gastos_trabajador'] == '1') { ?>
        <li class="slide">
          <a href="otros_gastos.php" class="side-menu__item">
            <i class='bx bx-dollar-circle side-menu__icon' ></i><span class="side-menu__label"> Otros Gastos</span>
          </a>          
        </li>
        <?php } ?>
        <!-- End::slide --> 
        
        <!-- Start::slide -->        
        <li class="slide">         
          <a href="incidencias.php" class="side-menu__item">
            <i class='bx bx-task side-menu__icon' ></i><span class="side-menu__label">Incidencias</span>            
          </a>
        </li>        
        <!-- End::slide --> 

        <!-- Start::slide__category -->
        <li class="slide__category"><span class="category-name">G E S T I O N - R R H H</span></li>
        <!-- End::slide__category -->

        <!-- Start::slide -->
        <?php  if ($_SESSION['administracion'] == '1') { ?>
        <li class="slide has-sub">
          <a href="javascript:void(0);" class="side-menu__item">
            <i class='bx bxs-briefcase-alt-2 side-menu__icon'  ></i>
            <span class="side-menu__label">Administracion</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
          </a>
          <ul class="slide-menu child1">
            <li class="slide side-menu__label1"><a href="javascript:void(0)">Administracion</a></li>
            <?php  if ($_SESSION['usuario'] == '1') { ?>
            <li class="slide"><a href="usuario.php" class="side-menu__item">Usuarios</a></li>     
            <?php } ?>             
          </ul>
        </li>
        <?php } ?>
        <!-- End::slide -->

        <!-- Start::slide -->
        <?php  if ($_SESSION['planilla_personal'] == '1') { ?>
        <li class="slide has-sub">
          <a href="javascript:void(0);" class="side-menu__item">
            <i class='bx bx-user-check side-menu__icon'></i>
            <span class="side-menu__label">Planilla personal</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
          </a>
          <ul class="slide-menu child1">
            <li class="slide side-menu__label1"><a href="javascript:void(0)">Planilla personal</a></li>  
            <?php  if ($_SESSION['registrar_trabajador'] == '1') { ?>          
            <li class="slide"><a href="trabajador.php" class="side-menu__item">Trabajadores</a></li>
            <?php } ?>
            <?php  if ($_SESSION['tipo_de_seguro'] == '1') { ?>
            <li class="slide"><a href="no_disponible.php" class="side-menu__item">Tipo de seguro</a></li>
            <?php } ?>
            <?php  if ($_SESSION['boleta_de_pago'] == '1') { ?>
            <li class="slide"><a href="no_disponible.php" class="side-menu__item">Boleta de pago</a></li>
            <?php } ?>
          </ul>
        </li>
        <?php } ?>
        <!-- End::slide -->

        <!-- Start::slide__category -->
        <li class="slide__category"><span class="category-name">C O N F I G U R A C I O N</span></li>
        <!-- End::slide__category -->

        <!-- Start::slide -->
        <?php  if ($_SESSION['SUNAT'] == '1') { ?>
        <li class="slide has-sub">
          <a href="javascript:void(0);" class="side-menu__item">
            <i class='bx bx-building side-menu__icon'></i>
            <span class="side-menu__label">SUNAT</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
          </a>
          <ul class="slide-menu child1">
            <li class="slide side-menu__label1"><a href="javascript:void(0)">SUNAT</a></li>
            <?php  if ($_SESSION['catalogo_de_codigo'] == '1') { ?>
            <li class="slide"><a href="catalogos_y_codigos.php" class="side-menu__item">Catálago de Códigos</a></li>
            <?php } ?>             
            <?php  if ($_SESSION['correlativo_numeracion'] == '1') { ?>          
            <li class="slide"><a href="correlacion_comprobante.php" class="side-menu__item">Tipos de Comprobantes</a></li>
            <?php } ?>
                 
          </ul>
        </li>
        <?php } ?>
        <!-- End::slide -->

        <!-- Start::slide -->
        <?php  if ($_SESSION['empresa'] == '1') { ?>
        <li class="slide has-sub">
          <a href="javascript:void(0);" class="side-menu__item">
            <i class='bx bxs-building-house side-menu__icon' ></i>
            <span class="side-menu__label">Empresa</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
          </a>
          <ul class="slide-menu child1">
            <li class="slide side-menu__label1"><a href="javascript:void(0)">Empresa</a></li>
            <?php  if ($_SESSION['empresa_configuracion'] == '1') { ?>
            <li class="slide"><a href="empresa.php" class="side-menu__item">Empresa</a></li>
            <?php } ?>            
          </ul>
        </li>
        <?php } ?>
        <!-- End::slide -->

        <!-- Start::slide -->
        <?php  if ($_SESSION['configuracion'] == '1') { ?>
        <li class="slide has-sub">
          <a href="javascript:void(0);" class="side-menu__item">
            <i class='bx bx-cog side-menu__icon'></i>
            <span class="side-menu__label">Configuración</span>
            <i class="fe fe-chevron-right side-menu__angle"></i>
          </a>
          <ul class="slide-menu child1">
            <li class="slide side-menu__label1"><a href="javascript:void(0)">Configuración</a></li>           
            <li class="slide"><a href="general.php" class="side-menu__item">General</a></li>                  
          </ul>
        </li>
        <?php } ?>
        <!-- End::slide -->

        <!-- Start::slide -->
        <li class="slide">
          <a href="papelera.php" class="side-menu__item" target="_blank">
            <i class='bx bx-trash side-menu__icon'></i><span class="side-menu__label">Papelera</span>
          </a>
        </li>
        <!-- End::slide -->

        <!-- Start::slide -->
        <li class="slide">
          <a href="landing_page.php" class="side-menu__item" target="_blank">
            <i class='bx bx-layer side-menu__icon'></i><span class="side-menu__label">Landing Page</span>
          </a>
        </li>
        <!-- End::slide -->

        <!-- Start::slide__category -->
        <li class="slide__category"><span class="category-name">S O P O R T E</span></li>
        <!-- End::slide__category --> 

        <!-- Start::slide -->
        <li class="slide">
          <a href="https://wa.link/1dpx0i" class="side-menu__item" target="_blank">
            <i class="bx bx-home side-menu__icon"></i><span class="side-menu__label"> Soporte Técnico</span>
          </a>
        </li>
        <!-- End::slide -->

        
      </ul>
      <div class="slide-right" id="slide-right">
        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24"
          height="24" viewBox="0 0 24 24">
          <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
        </svg>
      </div>
    </nav>
    <!-- End::nav -->

  </div>
  <!-- End::main-sidebar -->

</aside>
<!-- End::app-sidebar -->