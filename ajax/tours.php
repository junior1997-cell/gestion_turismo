<?php
ob_start();
if (strlen(session_id()) < 1) {
  session_start();
}

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status' => 'login', 'message' => 'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => []];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['producto'] == 1) {


    require_once "../modelos/Tours.php";
    $tours = new Tours();

    date_default_timezone_set('America/Lima');
    $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    $idtours            = isset($_POST["idtours"]) ? limpiarCadena($_POST["idtours"]) : "";

    $codigo_alterno     = isset($_POST["codigo_alterno"]) ? limpiarCadena($_POST["codigo_alterno"]) : "";
    $ubigeo_distrito    = isset($_POST["ubigeo_distrito"]) ? limpiarCadena($_POST["ubigeo_distrito"]) : "";
    $tours_turno        = isset($_POST["tours_turno"]) ? limpiarCadena($_POST["tours_turno"]) : "";
    $nombre             = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
    $precio_publico     = isset($_POST["precio_publico"]) ? limpiarCadena($_POST["precio_publico"]) : "";
    $precio_corporativo = isset($_POST["precio_corporativo"]) ? limpiarCadena($_POST["precio_corporativo"]) : "";
    $precio_web         = isset($_POST["precio_web"]) ? limpiarCadena($_POST["precio_web"]) : "";
    $precio_tours       = isset($_POST["precio_tours"]) ? limpiarCadena($_POST["precio_tours"]) : "";
    $detalle_duracion   = isset($_POST["detalle_duracion"]) ? limpiarCadena($_POST["detalle_duracion"]) : "";

    $detalle_incluye    = isset($_POST["detalle_incluye_tours"]) ? limpiarCadena($_POST["detalle_incluye_tours"]) : "";
    $detalle_programa   = isset($_POST["detalle_programa_tours"]) ? limpiarCadena($_POST["detalle_programa_tours"]) : "";

    $brochure        = isset($_POST["brochure"]) ? $_POST["brochure"] : "";
    $brochure_old    = isset($_POST["brochure_old"]) ? $_POST["brochure_old"] : "";
    // $name_brochure_tours="";
    // $name_brochure_tours="";

    switch ($_GET["op"]) {

      case 'listar_tabla':
        $rspta = $tours->listar_tabla($_GET["ubigeo_distrito"], $_GET["unidad_medida"], $_GET["marca"]);
        $data = [];
        $count = 2;
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $img = empty($value['imagen']) ? 'no-producto.png' : $value['imagen'];
            $data[] = [
              "0" => $value['idtours'] == 1 ? 1 : $count++,
              "1" => ($value['idtours'] == 1 ? '<i class="bi bi-exclamation-triangle text-danger fs-6"></i>' :
                '<div class="hstack gap-2 fs-15 text-center"> 
                <button class="btn btn-icon btn-sm btn-warning-light border-warning" onclick="mostrar_producto(' . ($value['idtours']) . ')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>' .
                '<button  class="btn btn-icon btn-sm btn-danger-light border-danger product-btn" onclick="eliminar_papelera_producto(' . $value['idtours'] . '.,\'' . $value['nombre'] . '\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>' .
                '<button class="btn btn-icon btn-sm btn-info-light border-info" onclick="mostrar_detalle_producto(' . ($value['idtours']) . ')" data-bs-toggle="tooltip" title="Ver"><i class="ri-eye-line"></i></button> 
              </div>'),
              "2" => ('<i class="bi bi-upc"></i> ' . $value['codigo'] . '<br> <i class="bi bi-person"></i> ' . $value['codigo_alterno']),
              "3" => '<div class="d-flex flex-fill align-items-center">
                        <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img src="../assets/modulo/productos/' . $img . '" alt="" onclick="ver_img(\'' . $img . '\', \'' . encodeCadenaHtml(($value['nombre'])) . '\')"> </span></div>
                        <div>
                          <h6 class="d-block fw-semibold text-primary">' . $value['nombre'] . '</h6>
                          <span class="d-block fs-12 text-muted">Turno: <b>' . $value['turno'] . '</b> </span> 
                        </div>
                      </div>',
              "4" => ($value['cuidad']),
              "5" => ($value['precio_publico']),
              "6" => ($value['precio_corporativo']),
              "7" => ($value['precio_tours']),
              "8" => '<textarea class="textarea_datatable bg-light"  readonly>' . ($value['detalle_duracion']) . '</textarea>',
              "9" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>',

            ];
          }
          $results = [
            'status' => true,
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
          ];
          echo json_encode($results);
        } else {
          echo $rspta['code_error'] . ' - ' . $rspta['message'] . ' ' . $rspta['data'];
        }
        break;

      case 'guardar_editar':

        //guardar f_img_fondo fondo
        if (empty($brochure)) {

          $brochure_tours = $brochure_old;

          $flat_img1 = false;

        } else {

          $brochure = json_decode($brochure, true);

          //echo json_encode($brochure, true); 

          if (!isset($brochure['data']) || !isset($brochure['name'])) {

            $brochure_tours = '';

          } else {

            $decoded_data = base64_decode($brochure['data'], true); // Decodificar el archivo base64

            if ($decoded_data === false) {

              $brochure_tours = '';

            } else {

              // Validar extensión del archivo
              $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'tiff', 'tif', 'svg', 'ico', 'pdf'];
              $file_info = pathinfo($brochure['name']);
              $ext = strtolower($file_info['extension'] ?? '');

              if (!in_array($ext, $allowed_extensions)) {

                $brochure_tours = '';

              } else {
                $flat_img1 = true;
                // Generar un nombre único para el archivo
                $random_number = random_int(0, 20) . round(microtime(true)) . random_int(21, 41);
                $name_brochure_tours =  $date_now . "__" . $random_number . "." . $ext;

                // Ruta de destino
                $ruta_destino = realpath(dirname(__FILE__) . '/../assets/modulo/facturacion/ticket/') . '/' . $name_brochure_tours;
                //echo json_encode($ruta_destino, true); die();

                // Guardar el archivo en el servidor
                if (file_put_contents($ruta_destino, $decoded_data) !== false) {
                  $brochure_tours =  $name_brochure_tours ;
                  //$file_nombre_old[] = limpiarCadena($mp_comprobante['name']);
                } else {
                  $brochure_tours='';
                }
              }
            }
          }

        }


        if (empty($idtours)) { #Creamos el registro

          $rspta = $tours->insertar(
            $codigo_alterno,
            $ubigeo_distrito,
            $tours_turno,
            $nombre,
            $precio_publico,
            $precio_corporativo,
            $precio_web,
            $precio_tours,
            $detalle_duracion,
            $detalle_incluye,
            $detalle_programa,
            $brochure_tours
          );
          echo json_encode($rspta, true);
        } else { # Editamos el registro

          if ($flat_img == true || empty($brochure_tours)) {
            $datos_f1 = $tours->mostrar($idtours);
            $img1_ant = $datos_f1['data']['brochure'];
            if (!empty($img1_ant)) {
              unlink("../assets/modulo/productos/" . $img1_ant);
            }
          }

          $rspta = $tours->editar(
            $idtours,
            $codigo_alterno,
            $ubigeo_distrito,
            $tours_turno,
            $nombre,
            $precio_publico,
            $precio_corporativo,
            $precio_web,
            $precio_tours,
            $detalle_duracion,
            $detalle_incluye,
            $detalle_programa,
            $brochure_tours
          );
          echo json_encode($rspta, true);
        }

        break;

      case 'mostrar':
        $rspta = $tours->mostrar($idtours);
        echo json_encode($rspta, true);
        break;

      case 'mostrar_detalle_producto':
        $rspta = $tours->mostrar_detalle_producto($idtours);
        $nombre_doc = $rspta['data']['imagen'];
        $html_table = '
          <div class="my-3" ><span class="h6"> Datos del Producto </span></div>
          <table class="table text-nowrap table-bordered">        
            <tbody>
              <tr>
                <th scope="col">Nombre</th>
                <th scope="row">' . $rspta['data']['nombre'] . '</th>            
              </tr>              
              <tr>
                <th scope="col">Código</th>
                <th scope="row">' . $rspta['data']['codigo'] . '</th>
              </tr> 
              <tr>
                <th scope="col">Descripción</th>
                <th scope="row">' . $rspta['data']['precio_publico'] . '</th>
              </tr>                  
            </tbody>
          </table>

          <div class="my-3" ><span class="h6"> Detalles </span></div>
          <table class="table text-nowrap table-bordered">        
            <tbody>
              <tr>
                  <th scope="col">ubigeo_distrito</th>
                  <th scope="row">' . $rspta['data']['ubigeo_distrito'] . '</th>            
                </tr> 
              <tr>
                <th scope="col">Marca</th>
                <th scope="row">' . $rspta['data']['marca'] . '</th>            
              </tr>              
              <tr>
                <th scope="col">U. Medida</th>
                <th scope="row">' . $rspta['data']['unidad_medida'] . '</th>
              </tr> 
              <tr>
                <th scope="col">precio_corporativo</th>
                <th scope="row">' . $rspta['data']['precio_corporativo'] . '</th>
              </tr>   
              <tr>
                <th scope="col">precio_corporativo Minimo</th>
                <th scope="row">' . $rspta['data']['precio_corporativo_minimo'] . '</th>
              </tr>               
            </tbody>
          </table>

          <div class="my-3" ><span class="h6"> Precio </span></div>
          <table class="table text-nowrap table-bordered">        
            <tbody>
              <tr>
                  <th scope="col">Precio Compra</th>
                  <th scope="row"> S/ ' . $rspta['data']['precio_compra'] . '</th>            
                </tr> 
              <tr>
                <th scope="col">Precio Venta</th>
                <th scope="row">S/ ' . $rspta['data']['precio_webenta'] . '</th>            
              </tr>              
              <tr>
                <th scope="col">Precio por Mayor</th>
                <th scope="row">S/ ' . $rspta['data']['precioB'] . '</th>
              </tr> 
              <tr>
                <th scope="col">Precio Distribuidor</th>
                <th scope="row">S/ ' . $rspta['data']['precioC'] . '</th>
              </tr>   
              <tr>
                <th scope="col">Precio Especial</th>
                <th scope="row">S/ ' . $rspta['data']['precioD'] . '</th>
              </tr>               
            </tbody>
          </table>
        <div class="my-3" ><span class="h6"> Imagen </span></div>';
        $rspta = ['status' => true, 'message' => 'Todo bien', 'data' => $html_table, 'imagen' => $rspta['data']['imagen'], 'nombre_doc' => $nombre_doc];
        echo json_encode($rspta, true);

        break;

      case 'eliminar':
        $rspta = $tours->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
        break;

      case 'papelera':
        $rspta = $tours->papelera($_GET["id_tabla"]);
        echo json_encode($rspta, true);
        break;

        // ══════════════════════════════════════  VALIDACION DE CODIGO  ══════════════════════════════════════
      case 'validar_code_tours':
        $rspta = $tours->validar_code_tours($_GET["idtours"], $_GET["codigo_alterno"]);
        echo json_encode($rspta, true);
        break;

        // ══════════════════════════════════════  S E L E C T 2 - P A R A   F O R M  ══════════════════════════════════════

      case 'select_tours_turno':
        $rspta = $tours->select_tours_turno();
        $data = "";

        if ($rspta['status']) {

          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idtours_turno'] . '" title ="' . $value['descripcion'] . '" >' . $value['nombre'] . '</option>';
          }

          $retorno = array('status' => true, 'message' => 'Salió todo ok', 'data' => $data,);

          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
        break;

      case 'select_ubigeo_distrito':
        $rspta = $tours->select_ubigeo_distrito();
        $data = "";
        //ud.idubigeo_distrito, ud.nombre as distrito, up.nombre as provincia, udp.nombre as region
        if ($rspta['status']) {

          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idubigeo_distrito'] . '" title ="' . $value['distrito'] . '" data-prov_reg="' . $value['provincia'] . ' - ' . $value['region'] . '" >' . $value['distrito'] . ' </option>';
          }

          $retorno = array('status' => true, 'message' => 'Salió todo ok', 'data' => $data,);

          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
        break;

      case 'select_marca':
        $rspta = $tours->select_marca();
        $data = "";

        if ($rspta['status']) {

          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idmarca'] . '" title ="' . $value['precio_publico'] . '" >' . $value['nombre'] . '</option>';
          }

          $retorno = array('status' => true, 'message' => 'Salió todo ok', 'data' => $data,);

          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
        break;

        // ══════════════════════════════════════  S E L E C T 2 - PARA FILTROS ══════════════════════════════════════ 
      case 'select2_filtro_ubigeo_distrito':
        $rspta = $tours->select2_filtro_ubigeo_distrito();
        $data = "";

        if ($rspta['status']) {

          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idubigeo_distrito'] . '" title ="' . $value['precio_publico'] . '" >' . $value['nombre'] . '</option>';
          }

          $retorno = array('status' => true,  'message' => 'Salió todo ok', 'data' => $data,);
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
        break;

      case 'select2_filtro__tours_turno':
        $rspta = $tours->select2_filtro_tours_turno();
        $data = "";

        if ($rspta['status']) {

          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idsunat_unidad_medida'] . '" title ="' . $value['precio_publico'] . '" >' . $value['nombre'] . ' - ' . $value['abreviatura'] . '</option>';
          }

          $retorno = array('status' => true, 'message' => 'Salió todo ok', 'data' => $data,);
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
        break;

      case 'select2_filtro_marca':
        $rspta = $tours->select2_filtro_marca();
        $data = "";

        if ($rspta['status']) {

          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idmarca'] . '" title ="' . $value['precio_publico'] . '" >' . $value['nombre'] . '</option>';
          }

          $retorno = array('status' => true, 'message' => 'Salió todo ok', 'data' => $data,);

          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
        break;

      default:
        $rspta = ['status' => 'error_code', 'message' => 'Te has confundido en escribir en el <b>swich.</b>', 'data' => []];
        echo json_encode($rspta, true);
        break;
    }
  } else {
    $retorno = ['status' => 'nopermiso', 'message' => 'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => []];
    echo json_encode($retorno);
  }
}
ob_end_flush();
