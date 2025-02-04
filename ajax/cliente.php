<?php
ob_start();
if (strlen(session_id()) < 1) {
  session_start();
} //Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status' => 'login', 'message' => 'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => []];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['cliente'] == 1) {

    require_once "../modelos/Cliente.php";

    require_once "../modelos/Facturacion.php";
    require '../vendor/autoload.php';                   // CONEXION A COMPOSER
    $see = require '../sunat/SunatCertificado.php';   // EMISION DE COMPROBANTES

    $persona_cliente = new Cliente();   
    $facturacion        = new Facturacion();   


    date_default_timezone_set('America/Lima');
    $date_now = date("d_m_Y__h_i_s_A");
    $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    $idpersona                  = isset($_POST["idpersona"]) ? limpiarCadena($_POST["idpersona"]) : "";
    $idtipo_persona             = isset($_POST["idtipo_persona"]) ? limpiarCadena($_POST["idtipo_persona"]) : "";
    $idbancos                   = isset($_POST["idbancos"]) ? limpiarCadena($_POST["idbancos"]) : "";
    $idcargo_trabajador         = isset($_POST["idcargo_trabajador"]) ? limpiarCadena($_POST["idcargo_trabajador"]) : "";
    $idpersona_cliente          = isset($_POST["idpersona_cliente"]) ? limpiarCadena($_POST["idpersona_cliente"]) : "";
    $tipo_persona_sunat         = isset($_POST["tipo_persona_sunat"]) ? limpiarCadena($_POST["tipo_persona_sunat"]) : "";
    $tipo_documento             = isset($_POST["tipo_documento"]) ? limpiarCadena($_POST["tipo_documento"]) : "";
    $numero_documento           = isset($_POST["numero_documento"]) ? limpiarCadena($_POST["numero_documento"]) : "";
    $nombre_razonsocial         = isset($_POST["nombre_razonsocial"]) ? limpiarCadena($_POST["nombre_razonsocial"]) : "";
    $apellidos_nombrecomercial  = isset($_POST["apellidos_nombrecomercial"]) ? limpiarCadena($_POST["apellidos_nombrecomercial"]) : "";
    $fecha_nacimiento           = isset($_POST["fecha_nacimiento"]) ? limpiarCadena($_POST["fecha_nacimiento"]) : "";

    $celular                    = isset($_POST["celular"]) ? limpiarCadena($_POST["celular"]) : "";
    $direccion                  = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
    $distrito                   = isset($_POST["distrito"]) ? limpiarCadena($_POST["distrito"]) : "";
    $departamento               = isset($_POST["departamento"]) ? limpiarCadena($_POST["departamento"]) : "";
    $provincia                  = isset($_POST["provincia"]) ? limpiarCadena($_POST["provincia"]) : "";
    $ubigeo                     = isset($_POST["ubigeo"]) ? limpiarCadena($_POST["ubigeo"]) : "";
    $correo                     = isset($_POST["correo"]) ? limpiarCadena($_POST["correo"]) : "";
    $idpersona_trabajador       = isset($_POST["idpersona_trabajador"]) ? limpiarCadena($_POST["idpersona_trabajador"]) : "";
    $idzona_antena              = isset($_POST["idzona_antena"]) ? limpiarCadena($_POST["idzona_antena"]) : "";
    $idselec_centroProbl        = isset($_POST["idselec_centroProbl"]) ? limpiarCadena($_POST["idselec_centroProbl"]) : "";
    $idplan                     = isset($_POST["idplan"]) ? limpiarCadena($_POST["idplan"]) : "";
    $ip_personal                = isset($_POST["ip_personal"]) ? limpiarCadena($_POST["ip_personal"]) : "";
    $fecha_afiliacion           = isset($_POST["fecha_afiliacion"]) ? limpiarCadena($_POST["fecha_afiliacion"]) : "";
    $fecha_cancelacion          = isset($_POST["fecha_cancelacion"]) ? limpiarCadena($_POST["fecha_cancelacion"]) : "";
    $usuario_microtick          = isset($_POST["usuario_microtick"]) ? limpiarCadena($_POST["usuario_microtick"]) : "";
    $nota                       = isset($_POST["nota"]) ? limpiarCadena($_POST["nota"]) : "";

    $estado_descuento           = isset($_POST["estado_descuento"]) ? limpiarCadena($_POST["estado_descuento"]) : "";
    $descuento                  = isset($_POST["descuento"]) ? limpiarCadena($_POST["descuento"]) : "";

    // ══════════════════════════════════════  DATOS DE FACTURACION ══════════════════════════════════════

    $f_idventa                = isset($_POST["f_idventa"]) ? limpiarCadena($_POST["f_idventa"]) : "";   
    $f_impuesto               = isset($_POST["f_impuesto"]) ? limpiarCadena($_POST["f_impuesto"]) : "";   
    $f_crear_y_emitir         = isset($_POST["f_crear_y_emitir"]) ? limpiarCadena($_POST["f_crear_y_emitir"]) : "NO"; 

    $f_idsunat_c01            = isset($_POST["f_idsunat_c01"]) ? limpiarCadena($_POST["f_idsunat_c01"]) : "";    
    $f_tipo_comprobante       = isset($_POST["f_tipo_comprobante"]) ? limpiarCadena($_POST["f_tipo_comprobante"]) : "";    
    $f_serie_comprobante      = isset($_POST["f_serie_comprobante"]) ? limpiarCadena($_POST["f_serie_comprobante"]) : "";    
    $f_idpersona_cliente      = isset($_POST["f_idpersona_cliente"]) ? limpiarCadena($_POST["f_idpersona_cliente"]) : "";         
    $f_observacion_documento  = isset($_POST["f_observacion_documento"]) ? limpiarCadena($_POST["f_observacion_documento"]) : "";    

    $f_usar_anticipo          = isset($_POST["f_usar_anticipo"]) ? limpiarCadena($_POST["f_usar_anticipo"]) : "";  
    $f_ua_monto_disponible    = isset($_POST["f_ua_monto_disponible"]) ? limpiarCadena($_POST["f_ua_monto_disponible"]) : "";  
    $f_ua_monto_usado         = isset($_POST["f_ua_monto_usado"]) ? limpiarCadena($_POST["f_ua_monto_usado"]) : "";  

    $f_venta_subtotal         = isset($_POST["f_venta_subtotal"]) ? limpiarCadena($_POST["f_venta_subtotal"]) : "";    
    $f_tipo_gravada           = isset($_POST["f_tipo_gravada"]) ? limpiarCadena($_POST["f_tipo_gravada"]) : "";
    $f_venta_descuento        = isset($_POST["f_venta_descuento"]) ? limpiarCadena($_POST["f_venta_descuento"]) : "";    
    $f_venta_igv              = isset($_POST["f_venta_igv"]) ? limpiarCadena($_POST["f_venta_igv"]) : "";            
    $f_venta_total            = isset($_POST["f_venta_total"]) ? limpiarCadena($_POST["f_venta_total"]) : "";   

    $f_nc_idventa             = isset($_POST["f_nc_idventa"]) ? limpiarCadena($_POST["f_nc_idventa"]) : "";    
    $f_nc_tipo_comprobante    = isset($_POST["f_nc_tipo_comprobante"]) ? limpiarCadena($_POST["f_nc_tipo_comprobante"]) : "";    
    $f_nc_serie_y_numero      = isset($_POST["f_nc_serie_y_numero"]) ? limpiarCadena($_POST["f_nc_serie_y_numero"]) : "";    
    $f_nc_motivo_anulacion    = isset($_POST["f_nc_motivo_anulacion"]) ? limpiarCadena($_POST["f_nc_motivo_anulacion"]) : "";    

    $f_tiempo_entrega         = isset($_POST["f_tiempo_entrega"]) ? limpiarCadena($_POST["f_tiempo_entrega"]) : "";    
    $f_validez_cotizacion     = isset($_POST["f_validez_cotizacion"]) ? limpiarCadena($_POST["f_validez_cotizacion"]) : "";   

    //---id cliente no va 
    switch ($_GET["op"]) {

      case 'guardar_y_editar_cliente':

        //guardar f_img_fondo fondo
        if (!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])) {
          $img_perfil = $_POST["imagenactual"];
          $flat_img1 = false;
        } else {
          $ext1 = explode(".", $_FILES["imagen"]["name"]);
          $flat_img1 = true;
          $img_perfil = $date_now . '__' . random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . end($ext1);
          move_uploaded_file($_FILES["imagen"]["tmp_name"], "../assets/modulo/persona/perfil/" . $img_perfil);
        }


        if (empty($idpersona_cliente)) {
          $rspta = $persona_cliente->insertar_cliente(
            $idtipo_persona,
            $idbancos,
            $idcargo_trabajador,
            $tipo_persona_sunat,
            $tipo_documento,
            $numero_documento,
            $nombre_razonsocial,
            $apellidos_nombrecomercial,
            $fecha_nacimiento,
            $celular,
            $direccion,
            $distrito,
            $departamento,
            $provincia,
            $ubigeo,
            $correo,
            $idpersona_trabajador,
            $idzona_antena,
            $idselec_centroProbl,
            $idplan,
            $ip_personal,
            $fecha_afiliacion, $fecha_cancelacion, $usuario_microtick,$nota,
            $estado_descuento, 
            $descuento,            
            $img_perfil
          );
          echo json_encode($rspta, true);
        } else {

          if ($flat_img1 == true || empty($img_perfil)) {
            $datos_f1 = $persona_cliente->perfil_trabajador($idpersona);
            $img1_ant = $datos_f1['data']['foto_perfil'];
            if (!empty($img1_ant)) { unlink("../assets/modulo/persona/perfil/" . $img1_ant); }
          }


          $rspta = $persona_cliente->editar_cliente(
            $idpersona,
            $idtipo_persona,
            $idbancos,
            $idcargo_trabajador,
            $idpersona_cliente,
            $tipo_persona_sunat,
            $tipo_documento,
            $numero_documento,
            $nombre_razonsocial,
            $apellidos_nombrecomercial,
            $fecha_nacimiento,
            $celular,
            $direccion,
            $distrito,
            $departamento,
            $provincia,
            $ubigeo,
            $correo,
            $idpersona_trabajador,
            $idzona_antena,
            $idselec_centroProbl,
            $idplan,
            $ip_personal,
            $fecha_afiliacion,$fecha_cancelacion, $usuario_microtick,$nota,
            $estado_descuento,
            $descuento,            
            $img_perfil
          );
          echo json_encode($rspta, true);
        }
      break;

      case 'desactivar_cliente':
        $rspta = $persona_cliente->desactivar_cliente($_GET["id_tabla"], $_GET["descripcion"]);
        echo json_encode($rspta, true);
      break;

      case 'activar_cliente':
        $rspta = $persona_cliente->activar_cliente($_GET["id_tabla"], $_GET["descripcion"]);
        echo json_encode($rspta, true);
      break;

      case 'eliminar_cliente':
        $rspta = $persona_cliente->eliminar_cliente($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_cliente':
        $rspta = $persona_cliente->mostrar_cliente($idpersona_cliente);
        //Codificar el resultado utilizando json
        echo json_encode($rspta, true);
      break;

      case 'cant_tab_cliente':
        $rspta = $persona_cliente->cant_tab_cliente($_GET["filtro_trabajador"],$_GET["filtro_dia_pago"],$_GET["filtro_plan"],$_GET["filtro_zona_antena"]);
        echo json_encode($rspta, true);
      break;

      case 'tabla_principal_cliente':
        $rspta = $persona_cliente->tabla_principal_cliente($_GET["filtro_trabajador"],$_GET["filtro_dia_pago"],$_GET["filtro_plan"],$_GET["filtro_zona_antena"]);
        //Vamos a declarar un array
        $data = [];
        $cont = 1;         
        $class_dia = "";        

        if ($rspta['status'] == true) {
          
          foreach ($rspta['data'] as $key => $value) {
           
            $dif_dias = floatval($value['dias_para_proximo_pago']);              

            if($dif_dias>5){  $class_dia="bg-outline-success";  }elseif ($dif_dias<=5 && $dif_dias>=3){ $class_dia="bg-outline-warning";  } else{ $class_dia="bg-outline-danger";  }

            $imagen_perfil = empty($value['foto_perfil']) ? 'no-perfil.jpg' :   $value['foto_perfil'];

            $data[] = array(
              "0" => $cont++,
              "1" => '<button class="btn btn-icon btn-sm border-warning btn-warning-light" onclick="mostrar_cliente(' . $value['idpersona_cliente'] . ')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>
              <button class="btn btn-icon btn-sm border-success btn-success-light" onclick="realizar_pago(' . $value['idpersona_cliente'] . ');" data-bs-toggle="tooltip" title="Realizar Pago"> <i class="ti ti-coin font-size-18px"></i> </button>                
              <div class="btn-group ">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle py-1 px-2" data-bs-toggle="dropdown" aria-expanded="false"> <i class="ri-settings-4-line"></i></button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_pagos_x_cliente(' . $value['idpersona_cliente'] . ');" ><i class="ti ti-checkup-list"></i> Listar pagos</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);" onclick="resumen_producto_comprado(' . $value['idpersona_cliente'] . ');" ><i class="bi bi-cart4"></i> Productos vendidos</a></li> '.
                  ( $value['estado_pc'] == '1' ? '<li><a class="dropdown-item text-danger" href="javascript:void(0);" onclick="eliminar_cliente(' . $value['idpersona_cliente'] . ', \'' . encodeCadenaHtml($value['cliente_nombre_completo']) . '\')"><i class="ri-delete-bin-line"></i> Dar de baja o Eliminar</a></li>': 
                  '<li><a class="dropdown-item text-success" href="javascript:void(0);" onclick="activar(' . $value['idpersona_cliente'] . ', \'' . encodeCadenaHtml($value['cliente_nombre_completo']) . '\')"><i class="ri-check-line"></i> Reactivar</a></li>'
                  ).
                '</ul>
              </div>',
              "2" => '<div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen">'.
                  ($value['cliente_tiene_pefil'] == 'SI' ? 
                  '<span class="avatar"> <img class="w-30px h-auto" src="../assets/modulo/persona/perfil/' . $imagen_perfil . '" alt="" onclick="ver_img(\'' . $imagen_perfil . '\', \'' . encodeCadenaHtml($value['cliente_nombre_completo']) . '\')"> </span>':
                  '<span class="avatar avatar-sm bg-primary-transparent profile-timeline-avatar">' . $value['cliente_primera_letra'] . '</span>'
                  ).
                '</div>
                <div>
                  <span class="d-block fw-semibold fs-12 text-primary">' . $value['cliente_nombre_completo'] . '</span>
                  <span class="text-muted fs-10 text-nowrap">' . $value['tipo_documento_abrev_nombre'] . ' : ' . $value['numero_documento'] . '</span> |
                  <span class="text-muted fs-10 text-nowrap">Cel.: ' . '<a href="tel:+51'.$value['celular'].'" data-bs-toggle="tooltip" title="Clic para hacer llamada">'.$value['celular'].'</a>' . '</span> |
                  <span class="text-muted fs-10 text-nowrap"><i class="ti ti-fingerprint fs-15"></i> ' . $value['idpersona_cliente_v2'] . '</span> 
                </div>
              </div>',
              "3" => '<textarea cols="30" rows="2" class="textarea_datatable bg-light fs-10" readonly="">' . $value['centro_poblado'] . ' : ' . $value['direccion'] . '</textarea>',
              "4" => $value['dias_para_proximo_pago'] ,
              "5" => '<span class="badge '.$class_dia.'">'.  $value['proximo_pago']  .'</span>',
              "6" => '<span class="badge bg-outline-success">' . $value['zona'] . '</span>' . '<br>' . '<span class="badge bg-outline-success">' . $value['nombre_plan'] . ' : ' . $value['costo'] . '</span>',
              "7" => '<div class="text-start" >
                      <span class="d-block fs-10 text-primary fw-semibold text-nowrap"> <i class="bx bx-broadcast '.($value['estado_pc'] == '1' ? 'bx-burst' : '').' fa-1x" ></i> ' . $value['ip_antena'] . '</span>
                      <span class="d-block fs-10 text-muted text-nowrap"><i class="bx bx-wifi '.($value['estado_pc'] == '1' ? 'bx-burst' : '').'" ></i> ' . $value['ip_personal'] . '</span>
                      <span class="text-muted fs-10 text-nowrap"><i class="bx bx-user-pin fa-1x"></i> ' . $value['usuario_microtick'] . '</span>
                    </div>',
              "8" => '<span class="fs-10">' . $value['trabajador_nombre'] .'</span>',
              "9" => '<textarea cols="30" rows="2" class="textarea_datatable bg-light " readonly="">' . $value['nota'] . '</textarea>',
              
              "10" => $value['cliente_nombre_completo'],
              "11" => $value['tipo_documento_abrev_nombre'],
              "12" => $value['numero_documento'],
              "13" => $value['centro_poblado'],
              "14" => $value['direccion'],
              "15" => $value['nombre_plan'],
              "16" => $value['costo'],
              "17" => $value['zona'],
              "18" => $value['proximo_pago'] ,
              "19" => $value['ip_antena']

            );
          }
          $results = [
            'status'=> true,
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data,
          ];
          echo json_encode($results, true);
        } else {
          echo $rspta['code_error'] . ' - ' . $rspta['message'] . ' ' . $rspta['data'];
        }

      break;

      case 'tabla_deudores':
        $rspta = $persona_cliente->tabla_principal_cliente_deuda(  $_GET["filtro_trabajador"],$_GET["filtro_dia_pago"],$_GET["filtro_plan"],$_GET["filtro_zona_antena"]);
        //Vamos a declarar un array
        $data = [];
        $cont = 1;         
        $class_dia = "";        

        if ($rspta['status'] == true) {
          
          foreach ($rspta['data'] as $key => $value) {           
            
            $imagen_perfil = empty($value['foto_perfil']) ? 'no-perfil.jpg' :   $value['foto_perfil'];

            $data[] = array(
              "0" => $cont++,
              "1" =>  $value['cliente_nombre_completo_recorte'] .'<br>'. '<span class="text-muted fs-10 text-nowrap">' . $value['tipo_documento_abrev_nombre'] . ' : ' . $value['numero_documento'] . '</span> ',             
              "2" => '<button class="btn btn-icon btn-sm border-danger btn-danger-light fs-12" onclick="detalle_tab_cliente(\'detalle-cliente-deudor\',' . $value['idpersona_cliente'] . ')" data-bs-toggle="tooltip" title="Ver Detalle">'. $value['avance'] .'</button>',              
             
              "3" => '<button class="btn btn-icon btn-sm border-info btn-info-light" onclick="realizar_pago(' . $value['idpersona_cliente'] . ');" data-bs-toggle="tooltip" title="Realizar Pagos"><i class="ti ti-coin font-size-18px"></i></button>                
                <div class="btn-group ">
                  <button type="button" class="btn btn-info btn-sm dropdown-toggle py-1 px-2" data-bs-toggle="dropdown" aria-expanded="false"> <i class="ri-settings-4-line"></i></button>
                  <ul class="dropdown-menu">
                    
                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="mostrar_cliente(' . $value['idpersona_cliente'] . ');" ><i class="ti ti-edit"></i> Editar</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_pagos_x_cliente(' . $value['idpersona_cliente'] . ');" ><i class="ti ti-checkup-list"></i> Listar pagos</a></li> 
                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="resumen_producto_comprado(' . $value['idpersona_cliente'] . ');" ><i class="bi bi-cart4"></i> Productos vendidos</a></li> '.
                    ( $value['estado_pc'] == '1' ? '<li><a class="dropdown-item text-danger" href="javascript:void(0);" onclick="eliminar_cliente(' . $value['idpersona_cliente'] . ', \'' . encodeCadenaHtml($value['cliente_nombre_completo']) . '\')"><i class="ri-delete-bin-line"></i> Dar de baja o Eliminar</a></li>': 
                    '<li><a class="dropdown-item text-success" href="javascript:void(0);" onclick="activar(' . $value['idpersona_cliente'] . ', \'' . encodeCadenaHtml($value['cliente_nombre_completo']) . '\')"><i class="ri-check-line"></i> Reactivar</a></li>'
                    ).
                  '</ul>
              </div>',
              "4" => '<textarea cols="30" rows="2" class="textarea_datatable bg-light fs-10" readonly="">' . $value['centro_poblado'] . ' : ' . $value['direccion'] . '</textarea>',
              "5" => '<span class="badge bg-outline-success">' . $value['zona'] . '</span>' ,
              "6" => '<span class="fs-10">' . $value['trabajador_nombre'] .'</span>',

            );
          }
          $results = [
            'status'=> true,
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data,
          ];
          echo json_encode($results, true);
        } else {
          echo $rspta['code_error'] . ' - ' . $rspta['message'] . ' ' . $rspta['data'];
        }

      break;

      case 'tabla_no_deudores':
        $rspta = $persona_cliente->tabla_principal_cliente_no_deuda(  $_GET["filtro_trabajador"],$_GET["filtro_dia_pago"],$_GET["filtro_plan"],$_GET["filtro_zona_antena"]);
        //Vamos a declarar un array
        $data = [];
        $cont = 1;         
        $class_dia = "";        

        if ($rspta['status'] == true) {
          
          foreach ($rspta['data'] as $key => $value) {           
            
            $imagen_perfil = empty($value['foto_perfil']) ? 'no-perfil.jpg' :   $value['foto_perfil'];

            $data[] = array(
              "0" => $cont++,
              "1" =>  $value['cliente_nombre_completo_recorte'] .'<br>'. '<span class="text-muted fs-10 text-nowrap">' . $value['tipo_documento_abrev_nombre'] . ' : ' . $value['numero_documento'] . '</span> ',             
              "2" => '<button class="btn btn-icon btn-sm border-success btn-success-light fs-12" onclick="detalle_tab_cliente(\'detalle-cliente-no-deudor\',' . $value['idpersona_cliente'] . ')" data-bs-toggle="tooltip" title="Ver Detalle">'. $value['cant_cobrado'] .'</button>',              
             
              "3" => '<button class="btn btn-icon btn-sm border-info btn-info-light" onclick="realizar_pago(' . $value['idpersona_cliente'] . ');" data-bs-toggle="tooltip" title="Realizar Pago"><i class="ti ti-coin font-size-18px"></i></button>                
                <div class="btn-group ">
                  <button type="button" class="btn btn-info btn-sm dropdown-toggle py-1 px-2" data-bs-toggle="dropdown" aria-expanded="false"> <i class="ri-settings-4-line"></i></button>
                  <ul class="dropdown-menu">
                    
                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="mostrar_cliente(' . $value['idpersona_cliente'] . ');" ><i class="ti ti-edit"></i> Editar</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_pagos_x_cliente(' . $value['idpersona_cliente'] . ');" ><i class="ti ti-checkup-list"></i> Listar pagos</a></li> 
                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="resumen_producto_comprado(' . $value['idpersona_cliente'] . ');" ><i class="bi bi-cart4"></i> Productos vendidos</a></li> '.
                    ( $value['estado_pc'] == '1' ? '<li><a class="dropdown-item text-danger" href="javascript:void(0);" onclick="eliminar_cliente(' . $value['idpersona_cliente'] . ', \'' . encodeCadenaHtml($value['cliente_nombre_completo']) . '\')"><i class="ri-delete-bin-line"></i> Dar de baja o Eliminar</a></li>': 
                    '<li><a class="dropdown-item text-success" href="javascript:void(0);" onclick="activar(' . $value['idpersona_cliente'] . ', \'' . encodeCadenaHtml($value['cliente_nombre_completo']) . '\')"><i class="ri-check-line"></i> Reactivar</a></li>'
                    ).
                  '</ul>
              </div>',
              "4" => '<span class="fs-10">' . $value['trabajador_nombre'] .'</span>',

            );
          }
          $results = [
            'status'=> true,
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data,
          ];
          echo json_encode($results, true);
        } else {
          echo $rspta['code_error'] . ' - ' . $rspta['message'] . ' ' . $rspta['data'];
        }

      break;


      // ══════════════════════════════════════   DETALLE CLIENTE DEUDOR   ══════════════════════════════════════ 
      case 'detalle_cliente_deudor':
        $rspta = $persona_cliente->detalle_cliente_deudor($_GET["idpersona_cliente"]);
        $porcentaje_avance = empty($rspta['data']['cliente']['cant_total']) || $rspta['data']['cliente']['cant_total'] == 0 ? 0 : number_format( (($rspta['data']['cliente']['cant_cobrado'] / $rspta['data']['cliente']['cant_total']) * 100), 1, '.', ',' );
        echo'
          <div class="col-xxl-5 col-xl-5 ">
            <div class="card custom-card overflow-hidden rounded border">
              <div class="card-body p-0">
                <div class="d-sm-flex align-items-top p-4 border-bottom-0 main-profile-cover">
                  <div>
                    <span class="avatar avatar-xxl avatar-rounded online me-3 bg-warning">';
                      if ($rspta['data']['cliente']['cliente_tiene_pefil'] == 'SI') {
                        echo '<img src="../assets/modulo/persona/perfil/' . $rspta['data']['cliente']['foto_perfil'] . '" alt="">';
                      }else {
                        echo $rspta['data']['cliente']['cliente_primera_letra'];
                      }
                      
                    echo '</span>
                  </div>
                  <div class="flex-fill main-profile-info">
                    <div class="d-flex align-items-center justify-content-between">
                      <h6 class="fw-semibold mb-1 text-fixed-white">'.$rspta['data']['cliente']['cliente_nombre_completo'].'</h6>
                      <button class="btn btn-light btn-wave px-2" onclick="mostrar_cliente(' . $rspta['data']['cliente']['idpersona_cliente'] . ')"><i class="ri-edit-2-line me-1 align-middle d-inline-block"></i></button>
                    </div>
                    <p class="mb-1 text-muted text-fixed-white op-7"></p>
                    <p class="fs-12 text-fixed-white mb-4 op-5">
                      <span class="me-3"><i class="ri-building-line me-1 align-middle"></i>'.$rspta['data']['cliente']['centro_poblado'].'</span>
                      <span><i class="ri-map-pin-line me-1 align-middle"></i>'.$rspta['data']['cliente']['distrito'].'</span>
                    </p>
                    <div class="d-flex mb-0">
                      <div class="me-4 text-center">
                        <p class="fw-bold fs-20 text-fixed-white text-shadow mb-0">'.$rspta['data']['cliente']['cant_total'].'</p>
                        <p class="mb-0 fs-11 op-5 text-fixed-white">Total mes</p>
                      </div>
                      <div class="me-4 text-center">
                        <p class="fw-bold fs-20 text-fixed-white text-shadow mb-0">'.$rspta['data']['cliente']['cant_cobrado'].'</p>
                        <p class="mb-0 fs-11 op-5 text-fixed-white">Pagado</p>
                      </div>
                      <div class="me-4 text-center">
                        <p class="fw-bold fs-20 text-fixed-white text-shadow mb-0">'.$rspta['data']['cliente']['avance_v2'].'</p>
                        <p class="mb-0 fs-11 op-5 text-fixed-white"> '.($rspta['data']['cliente']['cant_cobrado']>$rspta['data']['cliente']['cant_total']?'Adelanto':($rspta['data']['cliente']['cant_cobrado']==$rspta['data']['cliente']['cant_total']? 'Ok!' :'No Pagado')).'</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="bg-light px-4 py-2 border-bottom border-block-end-dashed d-flex align-items-center border-dark">
                  <p class="fs-15 mb-0 me-4 fw-semibold">Fecha Pago:</p>
                  <span class="fs-14">'.$rspta['data']['cliente']['dia_cancelacion_v2'].'</span> 
                </div>
                <div class="bg-light p-4 border-bottom border-block-end-dashed border-dark">
                  <div class="">
                    <p class="fs-15 mb-2 fw-semibold">Observacion:</p>
                    <p class="fs-12 text-muted op-7 mb-0"> '.$rspta['data']['cliente']['nota'].'</p>
                  </div>
                  <!-- <div class="mb-0">
                    <p class="fs-15 mb-2 fw-semibold">Links :</p>
                    <div class="mb-0">
                      <p class="mb-1">
                        <a href="https://www.spruko.com/" class="text-primary"><u>https://www.spruko.com/</u></a>
                      </p>
                      <p class="mb-0">
                        <a href="https://themeforest.net/user/spruko/portfolio" class="text-primary"><u>https://themeforest.net/user/spruko/portfolio</u></a>
                      </p>
                    </div>
                  </div> -->
                </div>
                
                
                <div class="bg-light p-4 border-bottom border-block-end-dashed border-dark">
                  <p class="fs-15 mb-2 me-4 fw-semibold">Pagados:</p>
                  <div>';
                  foreach ($rspta['data']['cliente_mes'] as $key => $val) {
                    if ($val['estado_pagado'] == 'NO DEUDA') {
                      echo '<a href="javascript:void(0);">
                        <span class="badge bg-success m-1" onclick="ver_comprobante_emitido('.$val['idventa'].',\''.$val['tipo_comprobante'].'\')">'.$val['nombre_mes_capitalize'].'-'.$val['name_year'].'</span>
                      </a>';
                    }                    
                  }                                      
                  echo '</div>
                  <p class="fs-15 mb-2 me-4 fw-semibold mt-3">No pagados:</p>
                  <div>';
                  foreach ($rspta['data']['cliente_mes'] as $key => $val) {
                    if ($val['estado_pagado'] == 'DEUDA') {
                      echo '<a href="javascript:void(0);">
                      <span class="badge bg-danger m-1">'.$val['nombre_mes_capitalize'].'-'.$val['name_year'].'</span>
                    </a>';
                    }                    
                  }                   
                                            
                  echo '</div>
                </div>
                <div class="bg-light p-4 border-bottom border-block-end-dashed border-dark">
                  <p class="fs-15 mb-2 me-4 fw-semibold">Mese cortados:</p>
                  <div class="text-muted">
                   
                    <p class="mb-0">    </p>
                  </div>
                </div>
                <div class="bg-light p-4">
                  <p class="fs-15 mb-2 me-4 fw-semibold">Técnico Asignado:</p>
                  <ul class="list-group">
                    <li class="list-group-item">
                      <div class="d-sm-flex align-items-top">
                        <span class="avatar avatar-sm">
                          <img src="../assets/modulo/persona/perfil/'.$rspta['data']['cliente']['trabajador_foto_perfil'] .'" alt="img">
                        </span>
                        <div class="ms-sm-2 ms-0 mt-sm-0 mt-1 fw-semibold flex-fill">
                          <p class="mb-0 lh-1">'.$rspta['data']['cliente']['trabajador_1_nombre'] .' '. $rspta['data']['cliente']['trabajador_1_apellido'].'</p>
                          <span class="fs-11 text-muted op-7">'.$rspta['data']['cliente']['trabajador_tipo_documento'] .': '. $rspta['data']['cliente']['trabajador_numero_documento'].'</span>
                        </div>
                        <a  class="btn btn-light btn-wave btn-sm d-flex justify-content-center align-items-center" href="https://wa.me/51'.$rspta['data']['cliente']['trabajador_celular'] .'" target="_blank"><i class="bi bi-whatsapp text-success fs-12"></i></a>
                      </div>
                    </li>
                  </ul>
                </div>
                <div class="bg-light p-4 border-bottom border-block-end-dashed d-flex align-items-center">
                  <p class="fs-15 mb-2 me-4 fw-semibold">Social Networks :</p>
                  <div class="btn-list mb-0">
                    <button class="btn btn-sm btn-icon btn-primary-light btn-wave waves-effect waves-light"><i class="ri-facebook-line fw-semibold"></i></button>
                    <button class="btn btn-sm btn-icon btn-secondary-light btn-wave waves-effect waves-light"><i class="ri-twitter-line fw-semibold"></i></button>
                    <button class="btn btn-sm btn-icon btn-warning-light btn-wave waves-effect waves-light"><i class="ri-instagram-line fw-semibold"></i></button>
                    <button class="btn btn-sm btn-icon btn-success-light btn-wave waves-effect waves-light"><i class="ri-github-line fw-semibold"></i></button>
                    <button class="btn btn-sm btn-icon btn-danger-light btn-wave waves-effect waves-light"><i class="ri-youtube-line fw-semibold"></i></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xxl-7 col-xl-7">
            <div class="row" >
              <div class="col-xl-12">
                <div class="card custom-card">
                  <div class="card-body p-0 rounded border border-block-end-dashed">
                    <div class="py-3 p-r-1rem border-bottom border-block-end-dashed d-flex align-items-center justify-content-between">
                      <div>
                        <ul class="nav nav-tabs mb-0 tab-style-6 justify-content-start" id="myTab" role="tablist">
                          <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity-tab-pane" type="button" role="tab" aria-controls="activity-tab-pane" aria-selected="true"><i class="ri-gift-line me-1 align-middle d-inline-block"></i>Pagos</button>
                          </li>
                          <li class="nav-item" role="presentation">
                            <button class="nav-link" onclick="reset_ubicacion_cliente(000, 0000);" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts-tab-pane" type="button" role="tab" aria-controls="posts-tab-pane" aria-selected="false"><i class="ri-bill-line me-1 align-middle d-inline-block"></i>Ubicacion</button>
                          </li>                                            
                        </ul>
                      </div>
                      <div>
                        <p class="fw-semibold mb-2">Completado '.$porcentaje_avance.'% - <a href="javascript:void(0);" class="text-primary fs-12">('.$rspta['data']['cliente']['cant_cobrado'].'/'.$rspta['data']['cliente']['cant_total'].')</a></p>
                        <div class="progress progress-xs progress-animate">
                          <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: '.$porcentaje_avance.'%"></div>
                        </div>
                      </div>
                    </div>
                    <div class="py-2 px-2">
                      <div class="tab-content bg-light" id="myTabContent">
                        <div class="tab-pane show active fade p-0 border-0 " id="activity-tab-pane" role="tabpanel" aria-labelledby="activity-tab" tabindex="0">
                          <ul class="list-unstyled profile-timeline" id="profile-posts-scroll">';
                            foreach ($rspta['data']['cliente_mes'] as $key => $val) {
                              
                            echo '<li>
                                <div class="mt-3 me-1 mb-3">'.
                                  ($val['estado_pagado'] == 'NO DEUDA' ?
                                  '<span class="avatar avatar-sm avatar-rounded profile-timeline-avatar">
                                    <img src="../assets/modulo/persona/perfil/'.$val['foto_perfil_tecnico_cobro'].'" alt="">
                                  </span>':
                                  '<span class="avatar avatar-sm bg-primary-transparent avatar-rounded profile-timeline-avatar"><i class="bi bi-emoji-frown fs-15"></i></span>'
                                  ).
                                  '<p class="mb-2">
                                    <b class="'.($val['estado_pagado'] == 'DEUDA' ? 'text-danger':'').'" >'.$val['tecnico_cobro'].'</b> - Mes:  <a class="text-secondary" href="javascript:void(0);"><u>'.$val['nombre_mes_recortado'].'-'.$val['name_year'].'</u></a>.<span class="float-end fs-10 text-muted">'.$val['fecha_emision_format'].'</span>
                                  </p>
                                  <p class="profile-activity-media mb-0" style="display: flex; align-items: center;">'.
                                    ($val['estado_pagado'] == 'DEUDA' ?
                                    '<span class="fs-11 text-muted">Le recordamos que su pago correspondiente a este mes está pendiente.</span>':
                                    '<a href="javascript:void(0);" class="btn btn-primary-light rounded zoom-width-27px-svg" style="margin-right: 5px;" onclick="ver_comprobante_emitido('.$val['idventa'].',\''.$val['tipo_comprobante'].'\')">                                                             
                                      <svg xmlns="http://www.w3.org/2000/svg" class="svg-info secondary " enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000">
                                        <path d="M0,0h24v24H0V0z" fill="none" />
                                        <g>
                                          <path d="M19.5,3.5L18,2l-1.5,1.5L15,2l-1.5,1.5L12,2l-1.5,1.5L9,2L7.5,3.5L6,2v14H3v3c0,1.66,1.34,3,3,3h12c1.66,0,3-1.34,3-3V2 L19.5,3.5z M15,20H6c-0.55,0-1-0.45-1-1v-1h10V20z M19,19c0,0.55-0.45,1-1,1s-1-0.45-1-1v-3H8V5h11V19z" />
                                          <rect height="2" width="6" x="9" y="7" /><rect height="2" width="2" x="16" y="7" /><rect height="2" width="6" x="9" y="10" /><rect height="2" width="2" x="16" y="10" />
                                        </g>
                                      </svg>                                      
                                    </a>
                                    <span style="display: flex; flex-direction: column;">
                                      <span href="javascript:void(0);" class="fs-11 text-muted cursor-pointer zoom-text-12px" onclick="ver_comprobante_emitido('.$val['idventa'].',\''.$val['tipo_comprobante'].'\')">'.$val['tipo_comprobante_v2'].' ELECTRONICA - '.$val['serie_comprobante'].'-'.$val['numero_comprobante'].'</span> 
                                      <span class="fs-11 text-muted">432.87KB</span>
                                    </span>').
                                  '</p>
                                </div>
                              </li>';
                            }
                         echo '                                        
                          </ul>
                        </div>
                        <div class="tab-pane fade p-0 border-0" id="posts-tab-pane" role="tabpanel" aria-labelledby="posts-tab" tabindex="0">
                          <div class="card custom-card">                                              
                            <div class="card-body">
                              <div id="map-markers"></div>
                            </div>
                          </div>
                        </div>                                          
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-12 col-xl-12">
                <div class="card custom-card ">
                  <div class="card-header">
                    <div class="card-title">
                      Información Adicional:
                    </div>
                  </div>
                  <div class="card-body">
                    <ul class="list-group">
                      <li class="list-group-item">
                        <div class="d-flex flex-wrap align-items-center">
                          <div class="me-2 fw-semibold">Plan:</div>
                          <span class="fs-12 text-muted">'.$rspta['data']['cliente']['nombre_plan'] . ' S/. ' . $rspta['data']['cliente']['costo'] .'</span> 
                          
                        </div>
                      </li>
                      <li class="list-group-item">
                        <div class="d-flex flex-wrap align-items-center">
                          <div class="me-2 fw-semibold">Codigo:</div>
                          <span class="fs-12 text-muted m-r-5px">'.$rspta['data']['cliente']['idpersona_cliente_v2'].'</span> | 
                          <div class="me-2 fw-semibold m-l-5px">Usuario cliente:</div>
                          <span class="fs-12 text-muted">'.$rspta['data']['cliente']['landing_user'].'</span>
                        </div>
                      </li>
                      <li class="list-group-item">
                        <div class="d-flex flex-wrap align-items-center">
                          <div class="me-2 fw-semibold">
                            <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted"><i class="ri-mail-line align-middle fs-14"></i></span>Email :
                          </div>
                          <span class="fs-12 text-muted">'.$rspta['data']['cliente']['correo'].'</span>
                        </div>
                      </li>
                      <li class="list-group-item">
                        <div class="d-flex flex-wrap align-items-center">
                          <div class="me-2 fw-semibold">
                            <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted"> <i class="ri-phone-line align-middle fs-14"></i> </span>Phone :
                          </div>
                          <span class="fs-12 text-muted">'.$rspta['data']['cliente']['celular'].'</span>
                        </div>
                      </li>
                      <li class="list-group-item">
                        <div class="d-flex flex-wrap align-items-center">
                          <div class="me-2 fw-semibold">
                            <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted"> <i class="ri-map-pin-line align-middle fs-14"></i> </span>Direccion :
                          </div>
                          <span class="fs-12 text-muted">'.$rspta['data']['cliente']['direccion'].'</span>
                        </div>
                      </li>
                      <li class="list-group-item">
                        <div class="d-flex flex-wrap align-items-center">
                          <div class="me-2 fw-semibold">Usuario Microtick:</div>
                          <span class="fs-12 text-muted">'.$rspta['data']['cliente']['usuario_microtick'].'</span>
                        </div>
                      </li>
                      <li class="list-group-item">
                        <div class="d-flex flex-wrap align-items-center">
                          <div class="me-2 fw-semibold">
                            Zona:
                          </div>
                          <span class="fs-12 text-muted">'.$rspta['data']['cliente']['zona'] . ' | '.$rspta['data']['cliente']['ip_antena'].'</span>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
         
        ';
        

      break;
      // ══════════════════════════════════════   PAGOS ALL CLIENTES   ══════════════════════════════════════ 
      case 'ver_pagos_x_cliente':
        $rspta = $persona_cliente->ver_pagos_x_cliente($_GET["idcliente"]);
        $imagen_perfil = empty($rspta['data']['cliente']['foto_perfil']) ? 'no-perfil.jpg' : $rspta['data']['cliente']['foto_perfil'];
        $bg_light = $rspta['data']['cliente']['estado'] == 1 ? '' : 'bg-danger-transparent';
        $num_anios = $rspta['data']['cliente']['total_anios_pago']; // Asegúrate de que esto refleje el número correcto de años
        $primero = true; $tercero = true;
        echo '<table class="table table-striped table-bordered table-condensed mb-2">
          <thead>
            <th class="font-size-11px">APELLIDOS Y NOMBRES</th> <th class="font-size-11px" >CANCELACIÓN</th> <th class="font-size-11px" >IMPORTE</th> <th class="font-size-11px" >AÑO</th> <th class="font-size-11px" >ENE</th> <th class="font-size-11px" >FEB</th> <th class="font-size-11px" >MAR</th> <th class="font-size-11px" >ABR</th>
            <th class="font-size-11px" >MAY</th> <th class="font-size-11px" >JUN</th> <th class="font-size-11px" >JUL</th> <th class="font-size-11px" >AGO</th> <th class="font-size-11px" >SEP</th> <th class="font-size-11px" >OCT</th> <th class="font-size-11px" >NOV</th> <th class="font-size-11px" >DIC</th> <th class="font-size-11px" >OBSERVACIONES</th>
          </thead>
        <tbody>';
    
        if ($num_anios > 0) {
          foreach ($rspta['data']['pagos'] as $key => $val) {
              echo '<tr>';
              if ($primero) {
                echo '<td class="py-1 px-1 '.$bg_light.' text-nowrap" rowspan="'.$num_anios.'"><div class="d-flex flex-fill align-items-center">
                    <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen">
                      <span class="avatar"> <img class="w-30px h-auto" src="../assets/modulo/persona/perfil/' . $imagen_perfil . '" alt="" onclick="ver_img(\'' . $imagen_perfil . '\', \'' . encodeCadenaHtml($rspta['data']['cliente']['cliente_nombre_completo']) . '\')" alt="" > </span>
                    </div>
                    <div>
                      <span class="d-block fs-11 fw-semibold text-primary">' . $rspta['data']['cliente']['cliente_nombre_completo'] . '</span>
                      <span class="text-muted fs-10 text-nowrap">' . $rspta['data']['cliente']['tipo_doc'] . ' : ' . $rspta['data']['cliente']['numero_documento'] . '</span> |
                      <span class="text-muted fs-10 text-nowrap"><i class="ti ti-fingerprint fs-12"></i> '. $rspta['data']['cliente']['idcliente'] . '</span>
                    </div>
                  </div></td>
                  <td class="py-1 px-1 font-size-11px '.$bg_light.' text-center" rowspan="'.$num_anios.'">'.$rspta['data']['cliente']['dia_cancelacion_v2'].'</td>                            
                  <td class="py-1 px-1 font-size-11px '.$bg_light.' text-center" rowspan="'.$num_anios.'">'.$rspta['data']['cliente']['costo'].'</td>';
                  $primero = false;
              }
              echo '<td class="py-1 px-1 font-size-11px '.$bg_light.' text-nowrap">'.$val['periodo_pago_year'].'</td>
                  <td class="py-1 px-1 font-size-11px '.$bg_light.' text-center" >'.( empty( $val['venta_enero'] ) ? (  intval( ($val['periodo_pago_year'] . '01') )  <= intval(date('Ym')) ? '<i class="bi bi-x-lg text-danger"></i>' : ''  )  : '<a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . $val['periodo_pago_year'] . '-01' . '\', \'' . encodeCadenaHtml($val['periodo_pago_year']) . '\')" type="button">' . $val['venta_enero'] . '</a>' ) .'</td>
                  <td class="py-1 px-1 font-size-11px '.$bg_light.' text-center" >'.( empty( $val['venta_febrero'] ) ? (  intval( ($val['periodo_pago_year'] . '02') )  <= intval(date('Ym')) ? '<i class="bi bi-x-lg text-danger"></i>' : ''  ) : '<a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . $val['periodo_pago_year'] . '-02' . '\', \'' . encodeCadenaHtml($val['periodo_pago_year']) . '\')" type="button">' . $val['venta_febrero'] . '</a>' ) .'</td>
                  <td class="py-1 px-1 font-size-11px '.$bg_light.' text-center" >'.( empty( $val['venta_marzo'] ) ? (  intval( ($val['periodo_pago_year'] . '03') )  <= intval(date('Ym')) ? '<i class="bi bi-x-lg text-danger"></i>' : ''  ) : '<a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . $val['periodo_pago_year'] . '-03' . '\', \'' . encodeCadenaHtml($val['periodo_pago_year']) . '\')" type="button">' . $val['venta_marzo'] . '</a>' ) .'</td>
                  <td class="py-1 px-1 font-size-11px '.$bg_light.' text-center" >'.( empty( $val['venta_abril'] ) ? (  intval( ($val['periodo_pago_year'] . '04') )  <= intval(date('Ym')) ? '<i class="bi bi-x-lg text-danger"></i>' : ''  ) : '<a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . $val['periodo_pago_year'] . '-04' . '\', \'' . encodeCadenaHtml($val['periodo_pago_year']) . '\')" type="button">' . $val['venta_abril'] . '</a>' ) .'</td>
                  <td class="py-1 px-1 font-size-11px '.$bg_light.' text-center" >'.( empty( $val['venta_mayo'] ) ? (  intval( ($val['periodo_pago_year'] . '05') )  <= intval(date('Ym')) ? '<i class="bi bi-x-lg text-danger"></i>' : ''  ) : '<a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . $val['periodo_pago_year'] . '-05' . '\', \'' . encodeCadenaHtml($val['periodo_pago_year']) . '\')" type="button">' . $val['venta_mayo'] . '</a>' ) .'</td>
                  <td class="py-1 px-1 font-size-11px '.$bg_light.' text-center" >'.( empty( $val['venta_junio'] ) ? (  intval( ($val['periodo_pago_year'] . '06') )  <= intval(date('Ym')) ? '<i class="bi bi-x-lg text-danger"></i>' : ''  ) : '<a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . $val['periodo_pago_year'] . '-06' . '\', \'' . encodeCadenaHtml($val['periodo_pago_year']) . '\')" type="button">' . $val['venta_junio'] . '</a>' ) .'</td>
                  <td class="py-1 px-1 font-size-11px '.$bg_light.' text-center" >'.( empty( $val['venta_julio'] ) ? (  intval( ($val['periodo_pago_year'] . '07') )  <= intval(date('Ym')) ? '<i class="bi bi-x-lg text-danger"></i>' : ''  ) : '<a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . $val['periodo_pago_year'] . '-07' . '\', \'' . encodeCadenaHtml($val['periodo_pago_year']) . '\')" type="button">' . $val['venta_julio'] . '</a>' ) .'</td>
                  <td class="py-1 px-1 font-size-11px '.$bg_light.' text-center" >'.( empty( $val['venta_agosto'] ) ? (  intval( ($val['periodo_pago_year'] . '08') )  <= intval(date('Ym')) ? '<i class="bi bi-x-lg text-danger"></i>' : ''  ) : '<a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . $val['periodo_pago_year'] . '-08' . '\', \'' . encodeCadenaHtml($val['periodo_pago_year']) . '\')" type="button">' . $val['venta_agosto'] . '</a>' ) .'</td>
                  <td class="py-1 px-1 font-size-11px '.$bg_light.' text-center" >'.( empty( $val['venta_septiembre'] ) ? (  intval( ($val['periodo_pago_year'] . '09') )  <= intval(date('Ym')) ? '<i class="bi bi-x-lg text-danger"></i>' : ''  ) : '<a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . $val['periodo_pago_year'] . '-09' . '\', \'' . encodeCadenaHtml($val['periodo_pago_year']) . '\')" type="button">' . $val['venta_septiembre'] . '</a>' ) .'</td>
                  <td class="py-1 px-1 font-size-11px '.$bg_light.' text-center" >'.( empty( $val['venta_octubre'] ) ? (  intval( ($val['periodo_pago_year'] . '10') )  <= intval(date('Ym')) ? '<i class="bi bi-x-lg text-danger"></i>' : ''  ) : '<a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . $val['periodo_pago_year'] . '-10' . '\', \'' . encodeCadenaHtml($val['periodo_pago_year']) . '\')" type="button">' . $val['venta_octubre'] . '</a>' ) .'</td>
                  <td class="py-1 px-1 font-size-11px '.$bg_light.' text-center" >'.( empty( $val['venta_noviembre'] ) ? (  intval( ($val['periodo_pago_year'] . '11') )  <= intval(date('Ym')) ? '<i class="bi bi-x-lg text-danger"></i>' : ''  ) : '<a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . $val['periodo_pago_year'] . '-11' . '\', \'' . encodeCadenaHtml($val['periodo_pago_year']) . '\')" type="button">' . $val['venta_noviembre'] . '</a>') .'</td>
                  <td class="py-1 px-1 font-size-11px '.$bg_light.' text-center" >'.( empty( $val['venta_diciembre'] ) ? (  intval( ($val['periodo_pago_year'] . '12') )  <= intval(date('Ym')) ? '<i class="bi bi-x-lg text-danger"></i>' : ''  ) : '<a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . $val['periodo_pago_year'] . '-12' . '\', \'' . encodeCadenaHtml($val['periodo_pago_year']) . '\')" type="button">' . $val['venta_diciembre'] . '</a>' ) .'</td>';
                  
                  if ($tercero) {
                    echo '<td class="py-0 '.$bg_light.'" rowspan="'.$num_anios.'"><textarea cols="30" rows="2" class="textarea_datatable '.$bg_light.' bg-light " readonly="">' . $rspta['data']['cliente']['nota'] . '</textarea></td>';
                    $tercero = false; 
                  }
              echo '</tr>';
          }
        } else {
          echo '
          <td class="py-0 '.$bg_light.' text-nowrap"><div class="d-flex flex-fill align-items-center">
              <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen">
                <span class="avatar"> <img class="w-30px h-auto" src="../assets/modulo/persona/perfil/' . $imagen_perfil . '" alt="" onclick="ver_img(\'' . $imagen_perfil . '\', \'' . encodeCadenaHtml($rspta['data']['cliente']['cliente_nombre_completo']) . '\')" alt="" > </span>
              </div>
              <div>
                <span class="d-block fs-11 fw-semibold text-primary">' . $rspta['data']['cliente']['cliente_nombre_completo'] . '</span>
                <span class="text-muted fs-10 text-nowrap">' . $rspta['data']['cliente']['tipo_doc'] . ' : ' . $rspta['data']['cliente']['numero_documento'] . '</span> |
                <span class="text-muted fs-10 text-nowrap"><i class="ti ti-fingerprint fs-12"></i> '. $rspta['data']['cliente']['idcliente'] . '</span>
              </div>
            </div></td>
          <td class="py-0 font-size-11px '.$bg_light.' text-center">'.$rspta['data']['cliente']['fecha_cancelacion_format'].'</td>                            
          <td class="py-0 font-size-11px '.$bg_light.' text-center">'.$rspta['data']['cliente']['costo'].'</td>
          <td colspan="14" class="text-center">No se registró ningún pago</td>';
        }
    
        echo '</tbody>
        </table>';        
            
      break;

      // ══════════════════════════════════════   PAGOS ALL CLIENTES   ══════════════════════════════════════ 
      case 'ver_pagos_all_cliente':
        $rspta = $persona_cliente->ver_pagos_all_cliente($_GET["filtro_trabajador"],$_GET["filtro_dia_pago"],$_GET["filtro_anio_pago"],$_GET["filtro_plan"],$_GET["filtro_zona_antena"]);
        
        echo '<table class="table  table-hover table-bordered table-condensed">
          <thead>
            <tr id="id_buscando_tabla_pago_all"> 
              <th colspan="20" class="bg-danger " style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
            </tr>
            <tr class="fs-10"> 
              <th class="font-size-11px">N°</th> <th class="font-size-11px">APELLIDOS Y NOMBRES</th> <th class="font-size-11px" >CANCELACIÓN</th> <th class="font-size-11px" >IMPORTE</th> <th class="font-size-11px" >AÑO</th> <th class="font-size-11px" >ENE</th> <th class="font-size-11px" >FEB</th> <th class="font-size-11px" >MAR</th> <th class="font-size-11px" >ABR</th>
              <th class="font-size-11px" >MAY</th> <th class="font-size-11px" >JUN</th> <th class="font-size-11px" >JUL</th> <th class="font-size-11px" >AGO</th> <th class="font-size-11px" >SEP</th> <th class="font-size-11px" >OCT</th> <th class="font-size-11px" >NOV</th> <th class="font-size-11px" >DIC</th> <th class="font-size-11px" >OBSERVACIONES</th>
            </tr>
          </thead>
        <tbody>';

        foreach ($rspta['data'] as $key => $val) {
          $imagen_perfil = empty($val['foto_perfil']) ? 'no-perfil.jpg' :   $val['foto_perfil'];
          $bg_light = $val['estado'] == 1 ? '' : 'bg-danger-transparent';
          echo '<tr>
            <th class="py-0 '.$bg_light.' text-center">'.($key + 1).'</th>
            <td class="py-0 '.$bg_light.' text-nowrap">
              <div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen">'.
                  ($val['cliente_tiene_pefil'] == 'SI' ? 
                  '<span class="avatar"> <img class="w-30px h-auto" src="../assets/modulo/persona/perfil/' . $imagen_perfil . '" alt="" onclick="ver_img(\'' . $imagen_perfil . '\', \'' . encodeCadenaHtml($val['cliente_nombre_completo']) . '\')"> </span>':
                  '<span class="avatar avatar-sm bg-primary-transparent profile-timeline-avatar">' . $val['cliente_primera_letra'] . '</span>'
                  ).
                '</div>
                <div>
                  <span class="d-block fs-11 fw-semibold text-primary">' . $val['cliente_nombre_completo'] . '</span>
                  <span class="text-muted fs-10 text-nowrap">' . $val['tipo_doc'] . ' : ' . $val['numero_documento'] . '</span> |
                  <span class="text-muted fs-10 text-nowrap"><i class="ti ti-fingerprint fs-12"></i> '. $val['idcliente'] . '</span>
                </div>
              </div>
            </td>
            <td class="py-0 font-size-11px '.$bg_light.' text-center" >'.$val['fecha_cancelacion_format'].'</td>
            <td class="py-0 font-size-11px '.$bg_light.' text-nowrap" >S/ '.$val['costo'].'</td>
            <td class="py-0 font-size-11px '.$bg_light.' text-center" >'.$val['periodo_pago_year'].'</td>  
            <td class="py-0 font-size-11px '.$bg_light.' text-center" ><a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . encodeCadenaHtml("Enero") . '\')" type="button">' . $val['venta_enero'] . '</a></td>
            <td class="py-0 font-size-11px '.$bg_light.' text-center" ><a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . encodeCadenaHtml("Febrero") . '\')" type="button">' . $val['venta_febrero'] . '</a></td>
            <td class="py-0 font-size-11px '.$bg_light.' text-center" ><a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . encodeCadenaHtml("Marzo") . '\')" type="button">' . $val['venta_marzo'] . '</a></td>
            <td class="py-0 font-size-11px '.$bg_light.' text-center" ><a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . encodeCadenaHtml("Abril") . '\')" type="button">' . $val['venta_abril'] . '</a></td>
            <td class="py-0 font-size-11px '.$bg_light.' text-center" ><a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . encodeCadenaHtml("Mayo") . '\')" type="button">' . $val['venta_mayo'] . '</a></td>
            <td class="py-0 font-size-11px '.$bg_light.' text-center" ><a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . encodeCadenaHtml("Junio") . '\')" type="button">' . $val['venta_junio'] . '</a></td>
            <td class="py-0 font-size-11px '.$bg_light.' text-center" ><a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . encodeCadenaHtml("Julio") . '\')" type="button">' . $val['venta_julio'] . '</a></td>
            <td class="py-0 font-size-11px '.$bg_light.' text-center" ><a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . encodeCadenaHtml("Agosto") . '\')" type="button">' . $val['venta_agosto'] . '</a></td>
            <td class="py-0 font-size-11px '.$bg_light.' text-center" ><a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . encodeCadenaHtml("Septiembre") . '\')" type="button">' . $val['venta_septiembre'] . '</a></td>
            <td class="py-0 font-size-11px '.$bg_light.' text-center" ><a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . encodeCadenaHtml("Octubre") . '\')" type="button">' . $val['venta_octubre'] . '</a></td>
            <td class="py-0 font-size-11px '.$bg_light.' text-center" ><a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . encodeCadenaHtml("Noviembre") . '\')" type="button">' . $val['venta_noviembre'] . '</a></td>
            <td class="py-0 font-size-11px '.$bg_light.' text-center" ><a onclick="pagos_cliente_x_mes(\'' . $val['idpersona_cliente'] . '\', \'' . encodeCadenaHtml("Diciembre") . '\')" type="button">' . $val['venta_diciembre'] . '</a></td>
            <td class="py-0 font-size-11px '.$bg_light.'" ><textarea cols="30" rows="2" class="textarea_datatable '.$bg_light.' bg-light " readonly="">' . $val['nota'] . '</textarea></td>
          </tr>';
        }

        echo '</tbody>
        </table>';
      break;
      
      case 'ver_pagos_all_cliente_v2':
        $rspta = $persona_cliente->ver_pagos_all_cliente($_GET["filtro_trabajador"],$_GET["filtro_dia_pago"],$_GET["filtro_anio_pago"],$_GET["filtro_plan"],$_GET["filtro_zona_antena"]);
        // echo $rspta; die();
        //Vamos a declarar un array
        $data = [];
        $cont = 1; 

        if ($rspta['status'] == true) {
          
          foreach ($rspta['data'] as $key => $val) {  
            
            $imagen_perfil = empty($val['foto_perfil']) ? 'no-perfil.jpg' :   $val['foto_perfil'];
            $bg_light = $val['estado_pc'] == 1 ? '' : 'bg-danger-transparent';

            $data[] = array(
              "0" => $cont++,
              "1" => '<div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen">'.
                  ($val['cliente_tiene_pefil'] == 'SI' ? 
                  '<span class="avatar"> <img class="w-30px h-auto" src="../assets/modulo/persona/perfil/' . $imagen_perfil . '" alt="" onclick="ver_img(\'' . $imagen_perfil . '\', \'' . encodeCadenaHtml($val['cliente_nombre_completo']) . '\')"> </span>':
                  '<span class="avatar avatar-sm bg-primary-transparent profile-timeline-avatar">' . $val['cliente_primera_letra'] . '</span>'
                  ).
                '</div>
                <div>
                  <span class="d-block fs-11 fw-semibold text-nowrap text-primary">' . $val['cliente_nombre_completo'] . '</span>
                  <span class="text-muted fs-10 text-nowrap">' . $val['tipo_documento_abrev_nombre'] . ' : ' . $val['numero_documento'] . '</span> |
                  <span class="text-muted fs-10 text-nowrap"><i class="ti ti-fingerprint fs-12"></i> '. $val['idpersona_cliente_v2'] . '</span>|
                  <span class="text-muted fs-10 text-nowrap"><i class="ti ti-calendar-event fs-12"></i> '. $val['fecha_cancelacion_format'] . '</span>
                </div>
              </div>',
              "2" => $val['dia_cancelacion_v2'],
              "3" => $val['costo'] ,
              "4" => '<div class="font-size-11px" >'.  $val['periodo_pago_year']  .'</div>',
              "5" => ( empty($val['venta_enero'])  ? '': '<div class="font-size-11px" data-bs-toggle="tooltip" title="Ver detalle" >' . $val['venta_enero'] . '</div>' ),
              "6" => ( empty($val['venta_febrero'])  ? '': '<div class="font-size-11px" data-bs-toggle="tooltip" title="Ver detalle" >' . $val['venta_febrero'] . '</div>' ),
              "7" => ( empty($val['venta_marzo'])  ? '': '<div class="font-size-11px" data-bs-toggle="tooltip" title="Ver detalle" >' . $val['venta_marzo'] . '</div>' ),
              "8" => ( empty($val['venta_abril'])  ? '': '<div class="font-size-11px" data-bs-toggle="tooltip" title="Ver detalle" >' . $val['venta_abril'] . '</div>' ),
              "9" => ( empty($val['venta_mayo'])  ? '': '<div class="font-size-11px" data-bs-toggle="tooltip" title="Ver detalle" >' . $val['venta_mayo'] . '</div>' ),
              "10" => ( empty($val['venta_junio'])  ? '': '<div class="font-size-11px" data-bs-toggle="tooltip" title="Ver detalle" >' . $val['venta_junio'] . '</div>' ),
              "11" => ( empty($val['venta_julio'])  ? '': '<div class="font-size-11px" data-bs-toggle="tooltip" title="Ver detalle" >' . $val['venta_julio'] . '</div>' ),
              "12" => ( empty($val['venta_agosto'])  ? '': '<div class="font-size-11px" data-bs-toggle="tooltip" title="Ver detalle" >' . $val['venta_agosto'] . '</div>' ),
              "13" => ( empty($val['venta_septiembre'])  ? '': '<div class="font-size-11px" data-bs-toggle="tooltip" title="Ver detalle" >' . $val['venta_septiembre'] . '</div>' ),
              "14" => ( empty($val['venta_octubre'])  ? '': '<div class="font-size-11px" data-bs-toggle="tooltip" title="Ver detalle" >' . $val['venta_octubre'] . '</div>' ),
              "15" => ( empty($val['venta_noviembre'])  ? '': '<div class="font-size-11px" data-bs-toggle="tooltip" title="Ver detalle" >' . $val['venta_noviembre'] . '</div>' ),
              "16" => ( empty($val['venta_diciembre'])  ? '': '<div class="font-size-11px" data-bs-toggle="tooltip" title="Ver detalle" >' . $val['venta_diciembre'] . '</div>' ),
              "17" => '<textarea cols="30" rows="2" class="textarea_datatable bg-light " readonly="">' . $val['nota'] . '</textarea>',

              "18" => $val['idpersona_cliente'] ,
              "19" => $val['periodo_pago_year'] ,
              "20" => $val['cliente_nombre_completo'] ,
              "21" => $val['zona'] ,
              "22" => $val['centro_poblado'] .': '. $val['direccion'] ,

            );
          }
          $results = [
            'status'=> true,
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data,
          ];
          echo json_encode($results, true);
        } else {
          echo $rspta['code_error'] . ' - ' . $rspta['message'] . ' ' . $rspta['data'];
        }
      break;

      // ══════════════════════════════════════ P A G O   C L I E N T E   P O R   M E S ══════════════════════════════════════ 
      case 'pagos_cliente_x_mes':
        $rspta = $persona_cliente->pago_cliente_x_mes($_GET["id"],$_GET["mes"],$_GET["filtroA"],$_GET["filtroB"],$_GET["filtroC"],$_GET["filtroD"],$_GET["filtroE"]);
        
        $data = [];
        $cont = 1;

        echo '<table class="table  table-hover table-bordered table-condensed">
        <thead >
          <tr id="id_buscando_tabla_pago_xmes"> 
            <th colspan="20" class="bg-danger " style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
          </tr>
          <tr > 
            <th >N°</th> <th >ID</th> <th >F. Emisión</th> <th class="text-nowrap" >Periodo Pago</th> <th >Comprobante</th> <th >Monto</th> <th >Imprimir</th> <th >Estado</th> <th >Observación</th>
          </tr>
        </thead>
        <tbody>';

        foreach ($rspta['data'] as $key => $value) {
          echo '<tr>
          <th class="py-0 text-center">'.($key + 1).'</th>
          <th class="py-0 text-center">'.$value['idventa_v2'].'</th>
          <td class="py-0 text-nowrap"><div class="d-flex flex-fill align-items-center">'.$value['fecha_emision'].'</td>
          <td class="py-0 text-nowrap"><div class="d-flex flex-fill align-items-center">'.$value['periodo_pago_mes_anio'].'</td>
          <td class="py-0 text-nowrap"><div class="d-flex flex-fill align-items-center">'.$value['SNCompb'].'</td>
          <td class="py-0 text-nowrap"><div class="d-flex flex-fill align-items-center '.($value['venta_total_v2'] < 0 ? 'text-danger':'').' ">'.$value['venta_total_v2'].'</td>
          <td class="py-2 text-center" >
            <button class="btn btn-icon btn-secondary-transparent rounded-pill btn-wave" onclick="TickcetPagoCliente('.($value['idventa']).', \'' . encodeCadenaHtml($value['tipo_comprobante']) . '\')" target="_blanck" data-bs-toggle="tooltip" title="Ticket">
             <i class="ri-ticket-line"></i> 
            </button>
          </td>
          <td class="py-0 text-nowrap"><div class="d-flex flex-fill align-items-center">'.
          ($value['sunat_estado'] == 'ACEPTADA' ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>'.$value['sunat_estado'].'</span>' : 
          '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>'.$value['sunat_estado'].'</span>')
          .'</td>
          <td class="py-2  text-center" >
            <textarea cols="30" rows="2" class="textarea_datatable w-300px bg-light " readonly="">' . $value['observacion_documento'] . 'dd</textarea>
          </td>
        </tr>';
          
        }

        echo '</tbody>
        </table>';
      break;

      // ══════════════════════════════════════ R E A L I Z A R  P A G O   C L I E N T E  ══════════════════════════════════════ 

      case 'guardar_editar_facturacion':

        $rspta = ""; $mp_comprobante = ""; 
        $sunat_estado = ""; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= ""; 

        if ( floatval($f_venta_total) > 699 ) {
          # code...
        } else {
          # code...
        }            

        $file_nombre_new = [];                        // Amacenar los nombres de los documentos             
        $file_nombre_old = [];                        // Amacenar los nombres de los documentos             
        $file_size = [];                        // Amacenar los nombres de los documentos             
        
        $comprobantes = $_POST["f_mp_comprobante"]; // Recibe el array de archivos
        $resultados = [];                           // Almacenar resultados para cada archivo       
        ksort($comprobantes);                       // Reorganizar el orden de indices
        //echo json_encode($comprobantes, true); die();
        foreach ($comprobantes as $key => $comprobante) {
          $mp_comprobante = json_decode($comprobante, true);
      
          if (!$mp_comprobante || empty($mp_comprobante['data'])) {
            $resultados[] = [  'index' => $key,  'status' => 'error',  'message' => 'El archivo no tiene datos válidos.'  ]; $file_nombre_new[] = ''; $file_nombre_old[] = ''; $file_size[] = '';  continue; // Saltar al siguiente archivo
          }
      
          $decoded_data = base64_decode($mp_comprobante['data']); // Decodificar el archivo base64
      
          if ($decoded_data === false) {
            $resultados[] = [ 'index' => $key, 'status' => 'error', 'message' => 'Error al decodificar el archivo base64.'  ]; $file_nombre_new[] = ''; $file_nombre_old[] = ''; $file_size[] = ''; continue; // Saltar al siguiente archivo
          }
      
          // Validar extensión del archivo
          $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'tiff', 'tif', 'svg', 'ico', 'pdf']; $file_info = pathinfo($mp_comprobante['name']);  $ext = strtolower($file_info['extension']);
      
          if (!in_array($ext, $allowed_extensions)) {
            $resultados[] = [   'index' => $key, 'status' => 'error',  'message' => 'Extensión de archivo no permitida.'  ]; $file_nombre_new[] = ''; $file_nombre_old[] = ''; $file_size[] = ''; continue; // Saltar al siguiente archivo
          }
      
          // Generar un nombre único para el archivo
          $mp_comprobante_name =$_POST["f_metodo_pago"][$key] . '__' . $date_now . '__' . random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . $ext;
      
          // Ruta de destino
          $ruta_destino = realpath(dirname(__FILE__)) . '/../assets/modulo/facturacion/ticket/' . $mp_comprobante_name;
      
          // Guardar el archivo en el servidor
          if (file_put_contents($ruta_destino, $decoded_data) !== false) {
            $file_nombre_new[] =  $mp_comprobante_name   ;
            $file_nombre_old[] = limpiarCadena($mp_comprobante['name']);
            $file_size[] = $mp_comprobante['size'];
          } 
        }
        //echo json_encode($_POST["f_metodo_pago"], true); die();
        if (empty($f_idventa)) {
          
          $rspta = $facturacion->insertar( $f_impuesto, $f_crear_y_emitir,$f_idsunat_c01  ,$f_tipo_comprobante, $f_serie_comprobante, $f_idpersona_cliente, $f_observacion_documento,
          $_POST["f_metodo_pago"],  $_POST["f_total_recibido"],  $_POST["f_total_vuelto"],  $_POST["f_mp_serie_comprobante"], $file_nombre_new, $file_nombre_old, $file_size, $f_usar_anticipo, $f_ua_monto_disponible, $f_ua_monto_usado,   $f_venta_subtotal, $f_tipo_gravada, $f_venta_descuento, $f_venta_igv, $f_venta_total,
          $f_nc_idventa, $f_nc_tipo_comprobante, $f_nc_serie_y_numero, $f_nc_motivo_anulacion, $f_tiempo_entrega, $f_validez_cotizacion,
          $_POST["idproducto"], $_POST["pr_marca"], $_POST["pr_categoria"],$_POST["pr_nombre"], $_POST["um_nombre"],$_POST["um_abreviatura"], $_POST["es_cobro"], $_POST["periodo_pago"], $_POST["cantidad"], $_POST["precio_compra"], $_POST["precio_sin_igv"], $_POST["precio_igv"], $_POST["precio_con_igv"],  $_POST["precio_venta_descuento"], 
          $_POST["f_descuento"], $_POST["descuento_porcentaje"], $_POST["subtotal_producto"], $_POST["subtotal_no_descuento_producto"]); 
          //echo json_encode($rspta, true); die();
          $f_idventa = $rspta['id_tabla']; //echo json_encode($f_idventa, true); die();

          if ($rspta['status'] == true) {             // validacion de creacion de documento                         
        
            if ($f_tipo_comprobante == '12') {          // SUNAT TICKET     
              $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, 'ACEPTADA' , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
              echo json_encode($rspta, true);             

            } else {
              if ($f_crear_y_emitir == 'SI') {           // NO ENVIAR A SUNAT
                if ($f_tipo_comprobante == '01') {   // SUNAT FACTURA
                  
                  include( '../modelos/SunatFactura.php');
                  $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);            
                  if ( empty($sunat_observacion) && empty($sunat_error) ) {
                    echo json_encode($rspta, true); 
                  } else {   
                    if ($sunat_estado == 111  ) {       // ENCASO DE HABR CONEXION SON SUNAT
                      $retorno = array( 'status' => 'no_conexion_sunat', 'titulo' => 'SUNAT en mantenimiento.', 'message' => 'No hay conexión a SUNAT, para seguir emitiendo dar click en: Enviar más tarde. De lo contrario tendra que pedir a su administrador para corregir el error.', 'user' =>  $_SESSION['user_nombre'], 'data' => 'Actual', 'id_tabla' => $f_idventa );
                      echo json_encode($retorno, true);
                    } else {           
                      $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error . '<br>' . $sunat_observacion, 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
                      echo json_encode($retorno, true); 
                    }
                  }                
                  
                } else if ($f_tipo_comprobante == '03') {   // SUNAT BOLETA 
                  
                  include( '../modelos/SunatBoleta.php');
                  $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
                  if ( empty($sunat_observacion) && empty($sunat_error) ) {
                    echo json_encode($rspta, true); 
                  } else {    
                    if ($sunat_estado == 111  ) {       // ENCASO DE HABR CONEXION SON SUNAT
                      $retorno = array( 'status' => 'no_conexion_sunat', 'titulo' => 'SUNAT en mantenimiento.', 'message' => 'No hay conexión a SUNAT, para seguir emitiendo dar click en: Enviar más tarde. De lo contrario tendra que pedir a su administrador para corregir el error.', 'user' =>  $_SESSION['user_nombre'], 'data' => 'Actual', 'id_tabla' => $f_idventa );
                      echo json_encode($retorno, true);
                    } else {
                      $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error . '<br>' . $sunat_observacion, 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
                      echo json_encode($retorno, true);
                    }                  
                  } 
                  
                } else if ($f_tipo_comprobante == '07') {   // SUNAT NOTA DE CREDITO 
                  
                  include( '../modelos/SunatNotaCredito.php');
                  $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
                  
                  if ( empty($sunat_observacion) && empty($sunat_error) ) {
                    $update_sunat = $facturacion->actualizar_doc_anulado_x_nota_credito( $nc_idventa); // CAMBIAMOS DE ESTADO EL DOC ANULADO
                    echo json_encode($rspta, true); 
                  } else {     
                    if ($sunat_estado == 111  ) {       // ENCASO DE HABR CONEXION SON SUNAT
                      $retorno = array( 'status' => 'no_conexion_sunat', 'titulo' => 'SUNAT en mantenimiento.', 'message' => 'No hay conexión a SUNAT, para seguir emitiendo dar click en: Enviar más tarde. De lo contrario tendra que pedir a su administrador para corregir el error.', 'user' =>  $_SESSION['user_nombre'], 'data' => 'Actual', 'id_tabla' => $f_idventa );
                      echo json_encode($retorno, true);
                    } else {         
                      $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error . '<br>' . $sunat_observacion, 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
                      echo json_encode($retorno, true);
                    }
                  }
                      
                } else {
                  $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'SUNAT en mantenimiento!!', 'message' => 'El sistema de sunat esta mantenimiento, esperamos su comprención, sea paciente', 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
                  echo json_encode($retorno, true);
                }
              } else {
                $sunat_estado = "POR ENVIAR"; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= ""; 
                $update_sunat = $facturacion->actualizar_respuesta_sunat( $f_idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);              
                
                if ($update_sunat['status'] == false ) { 
                  echo json_encode($update_sunat, true);
                } else {
                  echo json_encode($rspta, true); 
                }
                
              }
            }               
            
          } else{
            echo json_encode($rspta, true);
          }

        } else {

          $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Datos incompletos', 'message' => 'No se enviaron los datos completos: idventa', 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
          echo json_encode($retorno, true);
        }
    
      break; 

      case 'mostrar_datos_cliente':
        $rspta = $persona_cliente->mostrar_cliente($_GET["idpersona_cliente"]);
        //Codificar el resultado utilizando json
        echo json_encode($rspta, true);
      break;

      case 'listar_producto_x_precio':
        $rspta=$persona_cliente->listar_producto_x_precio($_POST["precio"]);
        echo json_encode($rspta, true);
      break;

      // ══════════════════════════════════════ R E S U M E N   P R O D U C T O   C O M P R A  ══════════════════════════════════════ 
      case 'tabla_resumen_producto_venta':
        $rspta = $persona_cliente->resumen_producto_venta(  $_GET["id_cliente"] );
        //Vamos a declarar un array
        $data = [];
        $cont = 1;             

        if ($rspta['status'] == true) {
          
          foreach ($rspta['data'] as $key => $value) { 

            $data[] = array(
              "0" => $cont++,
              "1" =>   $value['pr_nombre'],             
              "2" =>  $value['precio_venta_promedio'],             
              "3" =>   $value['cant_vendida'],
              // "4" => '<span class="fs-10">' . $value['trabajador_nombre'] .'</span>',

            );
          }
          $results = [
            'status'=> true,
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data,
          ];
          echo json_encode($results, true);
        } else {
          echo $rspta['code_error'] . ' - ' . $rspta['message'] . ' ' . $rspta['data'];
        }

      break;

      case 'tabla_todos_producto_venta':
        $rspta = $persona_cliente->resumen_producto_venta_todos(  $_GET["id_cliente"] );
        //Vamos a declarar un array
        $data = [];
        $cont = 1;             

        if ($rspta['status'] == true) {
          
          foreach ($rspta['data'] as $key => $value) { 

            $data[] = array(
              "0" => $cont++,
              "1" =>  $value['pr_nombre'],             
              "2" =>  $value['precio_venta'],             
              "3" =>  $value['cantidad'],
              "4" => '<span class="fs-11">' . '<b>'.$value['tp_comprobante_v2'].'</b>' . ' ' . $value['serie_comprobante'] . '-' . $value['numero_comprobante'].'</span>',
              "5" =>   $value['fecha_emision_format'] ,

            );
          }
          $results = [
            'status'=> true,
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data,
          ];
          echo json_encode($results, true);
        } else {
          echo $rspta['code_error'] . ' - ' . $rspta['message'] . ' ' . $rspta['data'];
        }

      break;

      // ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════ 

      case 'select2_filtro_trabajador':

        $rspta = $persona_cliente->select2_filtro_trabajador();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $cant_cliente   = $value['cant_cliente'];
            $data .= '<option  value="' . $value['idpersona_trabajador']  . '">' . $value['idtrabajador']. ' '.  $value['nombre_razonsocial'] . ' ('.$cant_cliente.')' . '</option>';
          }

          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;  
      
      case 'select2_filtro_dia_pago':

        $rspta = $persona_cliente->select2_filtro_dia_pago();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $cant_cliente   = $value['cant_cliente'];
            $data .= '<option  value="' . $value['dia_cancelacion']  . '">Día ' . $value['dia_cancelacion'] . ' ('.$cant_cliente.')'.'</option>';
          }

          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;

      case 'select2_filtro_anio_pago':

        $rspta = $persona_cliente->select2_filtro_anio_pago();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $cant_cliente   = $value['cant_cliente'];
            $data .= '<option  value="' . $value['anio_cancelacion']  . '">' . $value['anio_cancelacion'] . ' ('.$cant_cliente.')'. '</option>';
          }

          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;

      case 'select2_filtro_plan':

        $rspta = $persona_cliente->select2_filtro_plan();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $cant_cliente   = $value['cant_cliente'];
            $data .= '<option  value="' . $value['idplan']  . '">' . $value['nombre'] . ' ' . $value['costo'] .' ('.$cant_cliente.')'. '</option>';
          }

          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;

      case 'select2_filtro_zona_antena':

        $rspta = $persona_cliente->select2_filtro_zona_antena();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $idzona_antena  = $value['idzona_antena'];
            $nombre         = $value['nombre'];
            $ip_antena      = $value['ip_antena'];
            $cant_cliente   = $value['cant_cliente'];
            $data .= '<option value="' . $idzona_antena . '" cant_cliente="' . $cant_cliente . '">' . $nombre . ' - IP: ' . $ip_antena .' ('.$cant_cliente.')'. '</option>';
          }

          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;

      case 'select2_plan':

        $rspta = $persona_cliente->select2_plan();
        $cont = 1;
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value=' . $value['idplan']  . '>' . $value['nombre'] . ' - Costo: ' . $value['costo'] . '</option>';
          }

          $retorno = array(
            'status' => true,
            'message' => 'Salió todo ok',
            'data' => $data,
          );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;

      case 'select2_zona_antena':

        $rspta = $persona_cliente->select2_zona_antena();
        $cont = 1;
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value=' . $value['idzona_antena']  . '>' . $value['nombre'] . ' - IP: ' . $value['ip_antena'] . '</option>';
          }

          $retorno = array(
            'status' => true,
            'message' => 'Salió todo ok',
            'data' => $data,
          );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      case 'select2_trabajador':

        $rspta = $persona_cliente->select2_trabajador();
        $cont = 1;
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value=' . $value['idpersona_trabajador']  . '>' . $value['nombre_completo'] . ' ' . $value['numero_documento'] . '</option>';
          }

          $retorno = array(
            'status' => true,
            'message' => 'Salió todo ok',
            'data' => $data,
          );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;

      case 'selec_centroProbl':

        $rspta = $persona_cliente->selec_centroProbl();
        $cont = 1;
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value=' . $value['idcentro_poblado']  . '>' . $value['nombre'] . '</option>';
          }

          $retorno = array(
            'status' => true,
            'message' => 'Salió todo ok',
            'data' => $data,
          );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;


      case 'salir':
        //Limpiamos las variables de sesión
        session_unset();
        //Destruìmos la sesión
        session_destroy();
        //Redireccionamos al login
        header("Location: ../index.php");
      break;

      default:
        $rspta = ['status' => 'error_code', 'message' => 'Te has confundido en escribir en el <b>swich.</b>', 'data' => [], 'aaData' => []];
        echo json_encode($rspta, true);
      break;
    }
  } else {
    $retorno = ['status' => 'nopermiso', 'message' => 'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => []];
    echo json_encode($retorno);
  }
}

ob_end_flush();
