<?php
ob_start();
if (strlen(session_id()) < 1) { session_start(); }

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['lista_de_compras'] == 1) {

    require_once "../modelos/Compras.php";
    require_once "../modelos/Gasto_de_trabajador.php";
    require_once "../modelos/Correlacion_comprobante.php";
    require_once "../modelos/Producto.php";

    $compras            = new Compras();    
    $gasto_trab         = new Gasto_de_trabajador();    
    $correlacion_compb  = new Correlacion_comprobante();    
    $productos          = new Producto();

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    $idcompra         = isset($_POST["idcompra"]) ? limpiarCadena($_POST["idcompra"]) : "";    
    $idproveedor      = isset($_POST["idproveedor"]) ? limpiarCadena($_POST["idproveedor"]) : "";    
    $tipo_comprobante = isset($_POST["tipo_comprobante"]) ? limpiarCadena($_POST["tipo_comprobante"]) : "";    
    $serie            = isset($_POST["serie"]) ? limpiarCadena($_POST["serie"]) : "";    
    $descripcion      = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";    
    $subtotal_compra  = isset($_POST["subtotal_compra"]) ? limpiarCadena($_POST["subtotal_compra"]) : "";    
    $tipo_gravada     = isset($_POST["tipo_gravada"]) ? limpiarCadena($_POST["tipo_gravada"]) : "";    
    $igv_compra       = isset($_POST["igv_compra"]) ? limpiarCadena($_POST["igv_compra"]) : "";    
    $total_compra     = isset($_POST["total_compra"]) ? limpiarCadena($_POST["total_compra"]) : "";   
    $impuesto         = isset($_POST["impuesto"]) ? limpiarCadena($_POST["impuesto"]) : ""; 
    $img_comprob      = isset($_POST["doc_old_1"]) ? limpiarCadena($_POST["doc_old_1"]) : ""; 
    $fecha_compra     = isset($_POST["fecha_compra"]) ? limpiarCadena($_POST["fecha_compra"]) : "";   


    switch ($_GET["op"]){

      // :::::::::::: S E C C I O N   C O M P R A S ::::::::::::

      case 'listar_tabla_compra':

        $rspta = $compras->listar_tabla_compra();
        $data = []; $count = 1;

        if($rspta['status'] == true){

          foreach($rspta['data'] as $key => $value){

            $img_proveedor = empty($value['foto_perfil']) ? 'no-perfil.jpg' : $value['foto_perfil'];

            $data[] = [
              "0" => $count,
              "1" => '<div class="hstack gap-2 fs-15">' .                        
                        '<button class="btn btn-icon btn-sm btn-info-light" onclick="mostrar_detalle_compra('.($value['idcompra']).')" data-bs-toggle="tooltip" title="Ver"><i class="ri-eye-line"></i></button>'.
                      '</div>',
              "2" =>  $value['fecha_compra'],
              "3" => '<div class="d-flex flex-fill align-items-center">
                        <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen">
                          <span class="avatar"> <img src="../assets/modulo/persona/perfil/' . $img_proveedor . '" alt="" onclick="ver_img_proveedor(\'' . $img_proveedor . '\', \'' . encodeCadenaHtml(($value['nombre_razonsocial']) .' '. ($value['apellidos_nombrecomercial'])) . '\')"> </span>
                        </div>
                        <div>
                          <span class="d-block fw-semibold text-primary">'.$value['nombre_razonsocial'] .' '. $value['apellidos_nombrecomercial'].'</span>
                          <span class="text-muted"><b>'.$value['tipo_documento'] .'</b>: '. $value['numero_documento'].'</span>
                        </div>
                      </div>',
              "4" =>  '<div class="textarea_datatable bg-light" style="overflow: auto; resize: vertical; height: 45px;">'.
                        '<span> <b>Tipo :</b>' . $value['tp_comprobante'] . '</span> <br>'. 
                        '<span> <b>Serie :</b>' . $value['serie_comprobante'] . '</span><br>'.
                      '</div>',
              "5" => '<b> S/ ' . $value['total'] .'</b>', 
              "6" => '<div class="textarea_datatable bg-light" style="overflow: auto; resize: vertical; height: 45px;">' . $value['descripcion'] . '</div>',
              
              "7" => !empty($value['comprobante']) ? '<div class="d-flex justify-content-center"><button class="btn btn-icon btn-sm btn-info-light" onclick="ver_img_comprobante('.($value['idcompra']).');" data-bs-toggle="tooltip" title="Ver"><i class="ti ti-file-dollar fs-lg"></i></button></div>' : 
                     '<div class="d-flex justify-content-center"><button class="btn btn-icon btn-sm btn-danger-light" data-bs-toggle="tooltip" title="no encontrado"><i class="ti ti-file-alert fs-lg"></i></button></div>',
                    
                    
                    
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

      case 'guardar_editar_compra':

        if ( !file_exists($_FILES['doc1']['tmp_name']) || !is_uploaded_file($_FILES['doc1']['tmp_name']) ) {
          $img_comprob = $_POST["doc_old_1"];
          $flat_img = false; 
        } else {          
          $ext = explode(".", $_FILES["doc1"]["name"]);
          $flat_img = true;
          $img_comprob = $date_now . '__' . random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . end($ext);
          move_uploaded_file($_FILES["doc1"]["tmp_name"], "../assets/modulo/comprobante_compra/" . $img_comprob);          
        } 

        if (empty($idcompra)) {
          
          $rspta = $compras->insertar( $idproveedor,  $tipo_comprobante, $serie, $impuesto, $descripcion,
          $subtotal_compra, $tipo_gravada, $igv_compra, $total_compra, $fecha_compra, $img_comprob,
          $_POST["idproducto"], $_POST["unidad_medida"], $_POST["cantidad"], $_POST["precio_sin_igv"], $_POST["precio_igv"], $_POST["precio_con_igv"], 
          $_POST["descuento"], $_POST["subtotal_producto"]);

          echo json_encode($rspta, true);
        } else {

          $rspta = $compras->editar( $idcompra, $idproveedor,  $tipo_comprobante, $serie, $impuesto, $descripcion,
          $subtotal_compra, $tipo_gravada, $igv_compra, $total_compra, $fecha_compra, $img_comprob,
          $_POST["idproducto"], $_POST["unidad_medida"], $_POST["cantidad"], $_POST["precio_sin_igv"], $_POST["precio_igv"], $_POST["precio_con_igv"], 
          $_POST["descuento"], $_POST["subtotal_producto"]);
    
          echo json_encode($rspta, true);
        }
    
      break; 

      case 'mostrar_detalle_compra':
        $rspta=$compras->mostrar_detalle_compra($idcompra);
        

        echo '<div class="tab-pane fade active show" id="rol-compra-pane" role="tabpanel" tabindex="0">';
        echo '<div class="table-responsive p-0">
          <table class="table table-hover table-bordered  mt-4">          
            <tbody>
              <tr> <th>Proveedor</th>        <td>'.$rspta['data']['compra']['nombre_razonsocial'].'  '.$rspta['data']['compra']['apellidos_nombrecomercial'].'
              <div class="font-size-12px" >Cel: <a href="tel:+51'.$rspta['data']['compra']['celular'].'">'.$rspta['data']['compra']['celular'].'</a></div> 
              <div class="font-size-12px" >E-mail: <a href="mailto:'.$rspta['data']['compra']['correo'].'">'.$rspta['data']['compra']['correo'].'</a></div> </td> </tr>            
              <tr> <th>Total compra</th>      <td>'.$rspta['data']['compra']['total'].'</td> </tr>             
              <tr> <th>Fecha</th>         <td>'.$rspta['data']['compra']['fecha_compra'].'</td> </tr>                
              <tr> <th>Comprobante</th>   <td>'.$rspta['data']['compra']['tp_comprobante']. ' | '.$rspta['data']['compra']['serie_comprobante'].'</td> </tr>
              <tr> <th>Observacion</th>   <td>'.$rspta['data']['compra']['descripcion'].'</td> </tr>         
            </tbody>
          </table>
        </div>';
        echo '</div>'; # div-content

        echo'<div class="tab-pane fade" id="rol-detalle-pane" role="tabpanel" tabindex="0">';
        echo '<div class="table-responsive p-0">
          <table class="table table-hover table-bordered  mt-4">  
            <thead>
              <tr> <th>#</th> <th>Nombre</th> <th>Cantidad</th> <th>P/U</th> <th>Dcto.</th>  <th>Subtotal</th> </tr>
            </thead>        
            <tbody>';
            foreach ($rspta['data']['detalle'] as $key => $val) {
              echo '<tr> <td>'. $key + 1 .'</td> <td>'.$val['nombre'].'</td> <td class="text-center">'.$val['cantidad'].'</td> <td class="text-right">'.$val['precio_con_igv'].'</td> <td class="text-right">'.$val['descuento'].'</td> <td class="text-right" >'.$val['subtotal'].'</td> </tr>';
            }
        echo '</tbody>
            <tfoot>
              <td colspan="4"></td>

              <th class="text-right">
                <h6 class="tipo_gravada">SUBTOTAL</h6>
                <h6 class="val_igv">IGV (18%)</h6>
                <h5 class="font-weight-bold">TOTAL</h5>
              </th>
              <th class="text-right text-nowrap"> 
                <h6 class="font-weight-bold subtotal_compra">S/ '.$rspta['data']['compra']['subtotal'].'</h6> 
                <h6 class="font-weight-bold igv_compra">S/ '.$rspta['data']['compra']['igv'].'</h6>                 
                <h5 class="font-weight-bold total_compra">S/ '.$rspta['data']['compra']['total'].'</h5>                 
              </th>              
            </tfoot>
          </table>
        </div>';
        echo'</div>';# div-content
      break; 

      case 'mostrar_compra':
        $rspta=$compras->mostrar_compra($_POST["idcompra"]);
        echo json_encode($rspta, true);
      break; 

      case 'mostrar_editar_detalles_compra':
        $rspta=$compras->mostrar_editar_detalles_compra($_POST["idcompra"]);
        echo json_encode($rspta, true);
      break;

      case 'listar_producto_x_codigo':
        $rspta=$compras->listar_producto_x_codigo($_POST["codigo"]);
        echo json_encode($rspta, true);
      break;

      case 'eliminar':
        $rspta = $compras->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'papelera':
        $rspta = $compras->desactivar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_producto':
        $rspta=$compras->mostrar_producto($_POST["idproducto"]);
        echo json_encode($rspta, true);
      break; 

      case 'listar_tabla_producto':
          
        $rspta = $compras->listar_tabla_producto(); 

        $datas = []; 

        if ($rspta['status'] == true) {

          foreach($rspta['data'] as $key => $value){

            $img = empty($value['imagen']) ? 'no-producto.png' : $value['imagen'];
            $data_btn_1 = 'btn-add-producto-1-'.$value['idproducto']; $data_btn_2 = 'btn-add-producto-2-'.$value['idproducto'];
            $datas[] = [
              "0" => '<button class="btn btn-warning '.$data_btn_1.' mr-1 px-1 py-1" onclick="agregarDetalleComprobante(' . $value['idproducto'] . ', false)" data-toggle="tooltip" data-original-title="Agregar continuo"><span class="fa fa-plus"></span></button>
              <button class="btn btn-success '.$data_btn_2.' px-1 py-1" onclick="agregarDetalleComprobante(' . $value['idproducto'] . ', true)" data-toggle="tooltip" data-original-title="Agregar individual"><i class="fa-solid fa-list-ol"></i></button>',
              "1" => ('<i class="bi bi-upc"></i> '.$value['codigo'] .'<br> <i class="bi bi-person"></i> '.$value['codigo_alterno']) ,
              "2" =>  '<div class="d-flex flex-fill align-items-center">
                        <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img src="../assets/modulo/productos/' . $img . '" alt="" onclick="ver_img(\'' . $img . '\', \'' . encodeCadenaHtml(($value['nombre'])) . '\')"> </span></div>
                        <div>
                          <h6 class="d-block fw-semibold text-primary nombre_producto_' . $value['idproducto'] . '">'.$value['nombre'] .'</h6>
                          <span class="d-block fs-12 text-muted">Marca: <b>'.$value['marca'].'</b> | Categoría: <b>'.$value['categoria'].'</b></span> 
                        </div>
                      </div>',             
              "3" => ($value['precio_venta']),
              "4" => '<textarea class="textarea_datatable bg-light"  readonly>' .($value['descripcion']). '</textarea>'
            ];
          }
  
          $results = [
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($datas), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($datas), //enviamos el total registros a visualizar
            "aaData" => $datas,
          ];
          echo json_encode($results, true);
        } else {
          echo $rspta['code_error'] .' - '. $rspta['message'] .' '. $rspta['data'];
        }
    
      break;

      // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
      case 'listar_proveedor':
        $rspta = $gasto_trab->listar_proveedor(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value=' . $value['idpersona']  . '>' . $value['tipo_persona'] . ': ' . $value['nombre'] . ' '. $value['apellido'] . ' - '. $value['numero_documento'] . '</option>';
          }

          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => '<option  value="2" >PROVEEDOR VARIOS</option>'.$data, 
          );
          echo json_encode($retorno, true);

        } else { echo json_encode($rspta, true); }      
      break; 

      case 'listar_crl_comprobante':
        $rspta = $correlacion_compb->listar_crl_comprobante($_GET["tipos"]); $cont = 1; $data = "";
          if($rspta['status'] == true){
            foreach ($rspta['data'] as $key => $value) {
              $data .= '<option  value=' . $value['codigo']  . '>' . $value['tipo_comprobante'] . '</option>';
            }
    
            $retorno = array(
              'status' => true, 
              'message' => 'Salió todo ok', 
              'data' => $data, 
            );
            echo json_encode($retorno, true);
    
          } else { echo json_encode($rspta, true); }
      break;

      case 'select_categoria':
        $rspta = $productos->select_categoria();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idcategoria'] . '" title ="' . $value['nombre'] . '" >' . $value['nombre'] . '</option>';
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

      case 'select_u_medida':
        $rspta = $productos->select_u_medida();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idsunat_unidad_medida'] . '" title ="' . $value['nombre'] . '" >' . $value['nombre'] .' - '. $value['abreviatura'] . '</option>';
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

      case 'select_marca':
        $rspta = $productos->select_marca();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idmarca'] . '" title ="' . $value['nombre'] . '" >' . $value['nombre'] . '</option>';
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

?>