<?php
ob_start();
if (strlen(session_id()) < 1) { session_start(); }

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['producto'] == 1) {
   

    require_once "../modelos/Producto.php";
    $productos = new Producto();

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    $idproducto     = isset($_POST["idproducto"])? limpiarCadena($_POST["idproducto"]):"";

    $tipo           = isset($_POST["tipo"])? limpiarCadena($_POST["tipo"]):"";
    $codigo_alterno = isset($_POST["codigo_alterno"])? limpiarCadena($_POST["codigo_alterno"]):"";
    $categoria      = isset($_POST["categoria"])? limpiarCadena($_POST["categoria"]):"";
    $u_medida       = isset($_POST["u_medida"])? limpiarCadena($_POST["u_medida"]):"";
    $marca          = isset($_POST["marca"])? limpiarCadena($_POST["marca"]):"";
    $nombre         = isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
    $descripcion    = isset($_POST["descripcion"])? limpiarCadena($_POST["descripcion"]):"";
    $stock          = isset($_POST["stock"])? limpiarCadena($_POST["stock"]):"";
    $stock_min      = isset($_POST["stock_min"])? limpiarCadena($_POST["stock_min"]):"";
    $precio_v       = isset($_POST["precio_v"])? limpiarCadena($_POST["precio_v"]):"";
    $precio_c       = isset($_POST["precio_c"])? limpiarCadena($_POST["precio_c"]):"";
    $precio_x_mayor = isset($_POST["precio_x_mayor"])? limpiarCadena($_POST["precio_x_mayor"]):"";
    $precio_dist    = isset($_POST["precio_dist"])? limpiarCadena($_POST["precio_dist"]):"";
    $precio_esp     = isset($_POST["precio_esp"])? limpiarCadena($_POST["precio_esp"]):"";
    
    switch ($_GET["op"]){

      case 'listar_tabla':
        $rspta = $productos->listar_tabla($_GET["categoria"], $_GET["unidad_medida"], $_GET["marca"]);
        $data = []; $count = 2;
        if($rspta['status'] == true){
          foreach($rspta['data'] as $key => $value){
            $img = empty($value['imagen']) ? 'no-producto.png' : $value['imagen'];
            $data[]=[
              "0" => $value['idproducto'] == 1 ? 1 : $count++,
              "1" => ($value['idproducto'] == 1 ? '<i class="bi bi-exclamation-triangle text-danger fs-6"></i>' :
              '<div class="hstack gap-2 fs-15 text-center"> 
                <button class="btn btn-icon btn-sm btn-warning-light border-warning" onclick="mostrar_producto('.($value['idproducto']).')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                '<button  class="btn btn-icon btn-sm btn-danger-light border-danger product-btn" onclick="eliminar_papelera_producto('.$value['idproducto'].'.,\''.$value['nombre'].'\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>'.
                '<button class="btn btn-icon btn-sm btn-info-light border-info" onclick="mostrar_detalle_producto('.($value['idproducto']).')" data-bs-toggle="tooltip" title="Ver"><i class="ri-eye-line"></i></button> 
              </div>'),
              "2" =>  ('<i class="bi bi-upc"></i> '.$value['codigo'] .'<br> <i class="bi bi-person"></i> '.$value['codigo_alterno']),
              "3" => '<div class="d-flex flex-fill align-items-center">
                        <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img src="../assets/modulo/productos/' . $img . '" alt="" onclick="ver_img(\'' . $img . '\', \'' . encodeCadenaHtml(($value['nombre'])) . '\')"> </span></div>
                        <div>
                          <h6 class="d-block fw-semibold text-primary">'.$value['nombre'] .'</h6>
                          <span class="d-block fs-12 text-muted">Marca: <b>'.$value['marca'].'</b> | Categoría: <b>'.$value['categoria'].'</b></span> 
                        </div>
                      </div>',
              "4" => ($value['stock']),
              "5" => ($value['unidad_medida']),
              "6" => ($value['precio_compra']),
              "7" => ($value['precio_venta']),
              "8" => '<textarea class="textarea_datatable bg-light"  readonly>' .($value['descripcion']). '</textarea>',
              "9" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>',
              
              "10" =>($value['categoria']),
              "11" =>($value['marca']),
              "12" =>($value['nombre']),
              "13" =>($value['codigo']),
              "14" =>($value['codigo_alterno']),
              "15" =>($value['idproducto'])
            ];
          }
          $results =[
            'status'=> true,
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
          ];
          echo json_encode($results);

        } else { echo $rspta['code_error'] .' - '. $rspta['message'] .' '. $rspta['data']; }
      break;

      case 'guardar_editar':
        //guardar f_img_fondo fondo
        if ( !file_exists($_FILES['imagenProducto']['tmp_name']) || !is_uploaded_file($_FILES['imagenProducto']['tmp_name']) ) {
          $img_producto = $_POST["imagenactualProducto"];
          $flat_img = false; 
        } else {          
          $ext = explode(".", $_FILES["imagenProducto"]["name"]);
          $flat_img = true;
          $img_producto = $date_now . '__' . random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . end($ext);
          move_uploaded_file($_FILES["imagenProducto"]["tmp_name"], "../assets/modulo/productos/" . $img_producto);          
        }        

        if ( empty($idproducto) ) { #Creamos el registro

          $rspta = $productos->insertar($tipo,$codigo_alterno, $categoria, $u_medida, $marca, $nombre, $descripcion, $stock, 
          $stock_min, $precio_v, $precio_c, $precio_x_mayor, $precio_dist, $precio_esp, $img_producto);
          echo json_encode($rspta, true);

        } else { # Editamos el registro

          if ($flat_img == true || empty($img_producto)) {
            $datos_f1 = $productos->mostrar($idproducto);
            $img1_ant = $datos_f1['data']['imagen'];
            if (!empty($img1_ant)) { unlink("../assets/modulo/productos/" . $img1_ant); }         
          }  
        
          $rspta = $productos->editar($idproducto, $tipo, $codigo_alterno, $categoria, $u_medida, $marca, $nombre, $descripcion, 
          $stock, $stock_min, $precio_v, $precio_c, $precio_x_mayor, $precio_dist, $precio_esp, $img_producto);
          echo json_encode($rspta, true);
        }        

      break; 

      case 'mostrar' :
        $rspta = $productos->mostrar($idproducto);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_detalle_producto':
        $rspta = $productos->mostrar_detalle_producto($idproducto);
        $nombre_doc = $rspta['data']['imagen'];
        $html_table = '
          <div class="my-3" ><span class="h6"> Datos del Producto </span></div>
          <table class="table text-nowrap table-bordered">        
            <tbody>
              <tr>
                <th scope="col">Nombre</th>
                <th scope="row">'.$rspta['data']['nombre'].'</th>            
              </tr>              
              <tr>
                <th scope="col">Código</th>
                <th scope="row">'.$rspta['data']['codigo'].'</th>
              </tr> 
              <tr>
                <th scope="col">Descripción</th>
                <th scope="row">'.$rspta['data']['descripcion'].'</th>
              </tr>                  
            </tbody>
          </table>

          <div class="my-3" ><span class="h6"> Detalles </span></div>
          <table class="table text-nowrap table-bordered">        
            <tbody>
              <tr>
                  <th scope="col">Categoria</th>
                  <th scope="row">'.$rspta['data']['categoria'].'</th>            
                </tr> 
              <tr>
                <th scope="col">Marca</th>
                <th scope="row">'.$rspta['data']['marca'].'</th>            
              </tr>              
              <tr>
                <th scope="col">U. Medida</th>
                <th scope="row">'.$rspta['data']['unidad_medida'].'</th>
              </tr> 
              <tr>
                <th scope="col">Stock</th>
                <th scope="row">'.$rspta['data']['stock'].'</th>
              </tr>   
              <tr>
                <th scope="col">Stock Minimo</th>
                <th scope="row">'.$rspta['data']['stock_minimo'].'</th>
              </tr>               
            </tbody>
          </table>

          <div class="my-3" ><span class="h6"> Precio </span></div>
          <table class="table text-nowrap table-bordered">        
            <tbody>
              <tr>
                  <th scope="col">Precio Compra</th>
                  <th scope="row"> S/ '.$rspta['data']['precio_compra'].'</th>            
                </tr> 
              <tr>
                <th scope="col">Precio Venta</th>
                <th scope="row">S/ '.$rspta['data']['precio_venta'].'</th>            
              </tr>              
              <tr>
                <th scope="col">Precio por Mayor</th>
                <th scope="row">S/ '.$rspta['data']['precioB'].'</th>
              </tr> 
              <tr>
                <th scope="col">Precio Distribuidor</th>
                <th scope="row">S/ '.$rspta['data']['precioC'].'</th>
              </tr>   
              <tr>
                <th scope="col">Precio Especial</th>
                <th scope="row">S/ '.$rspta['data']['precioD'].'</th>
              </tr>               
            </tbody>
          </table>
        <div class="my-3" ><span class="h6"> Imagen </span></div>';
        $rspta = ['status' => true, 'message' => 'Todo bien', 'data' => $html_table, 'imagen' => $rspta['data']['imagen'], 'nombre_doc'=> $nombre_doc];
        echo json_encode($rspta, true);

      break;

      case 'eliminar':
        $rspta = $productos->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break; 

      case 'papelera':
        $rspta = $productos->papelera($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      // ══════════════════════════════════════  VALIDACION DE CODIGO  ══════════════════════════════════════
      case 'validar_code_producto':
        $rspta = $productos->validar_code_producto($_GET["idproducto"], $_GET["codigo_alterno"]);
        echo json_encode($rspta, true);
      break;

      // ══════════════════════════════════════  S E L E C T 2 - P A R A   F O R M  ══════════════════════════════════════

      case 'select_categoria':
        $rspta = $productos->select_categoria();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idcategoria'] . '" title ="' . $value['descripcion'] . '" >' . $value['nombre'] . '</option>';
          }
  
          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data, );
  
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      case 'select_u_medida':
        $rspta = $productos->select_u_medida();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idsunat_unidad_medida'] . '" title ="' . $value['descripcion'] . '" >' . $value['nombre'] .' - '. $value['abreviatura']. '</option>';
          }
  
          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data, );
  
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      case 'select_marca':
        $rspta = $productos->select_marca();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idmarca'] . '" title ="' . $value['descripcion'] . '" >' . $value['nombre'] . '</option>';
          }
  
          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data, );
  
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      // ══════════════════════════════════════  S E L E C T 2 - PARA FILTROS ══════════════════════════════════════ 
      case 'select2_filtro_categoria':
        $rspta = $productos->select2_filtro_categoria();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idcategoria'] . '" title ="' . $value['descripcion'] . '" >' . $value['nombre'] . '</option>';
          }
  
          $retorno = array( 'status' => true,  'message' => 'Salió todo ok', 'data' => $data, );  
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      case 'select2_filtro_u_medida':
        $rspta = $productos->select2_filtro_u_medida();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idsunat_unidad_medida'] . '" title ="' . $value['descripcion'] . '" >' . $value['nombre'] .' - '. $value['abreviatura']. '</option>';
          }
  
          $retorno = array('status' => true, 'message' => 'Salió todo ok', 'data' => $data, );  
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      case 'select2_filtro_marca':
        $rspta = $productos->select2_filtro_marca();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idmarca'] . '" title ="' . $value['descripcion'] . '" >' . $value['nombre'] . '</option>';
          }
  
          $retorno = array('status' => true,'message' => 'Salió todo ok', 'data' => $data, );
  
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      default: 
        $rspta = ['status'=>'error_code', 'message'=>'Te has confundido en escribir en el <b>swich.</b>', 'data'=>[]]; echo json_encode($rspta, true); 
      break;
    }

  }else {
    $retorno = ['status'=>'nopermiso', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);
  }


}
ob_end_flush();