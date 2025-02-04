<?php
ob_start();
if (strlen(session_id()) < 1) { session_start(); } //Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['configuracion'] == 1) {

    require_once "../modelos/Categoria_otros_gastos.php";

    $categoria_otros_gastos = new Categoria_otros_gastos();

    $idotros_gastos_categoria = isset($_POST["idotros_gastos_categoria"]) ? limpiarCadena($_POST["idotros_gastos_categoria"]) : "";
    $nombre_otros_gastos      = isset($_POST["nombre_categoria_otros_gastos"]) ? limpiarCadena($_POST["nombre_categoria_otros_gastos"]) : "";
    $descripcion_otros_gastos = isset($_POST["descripcion_categoria_otros_gastos"]) ? limpiarCadena($_POST["descripcion_categoria_otros_gastos"]) : "";


    switch ($_GET["op"]) {
      case 'guardar_y_editar':
        if (empty($idotros_gastos_categoria)) {
          $rspta = $categoria_otros_gastos->insertar($nombre_otros_gastos, $descripcion_otros_gastos);
          echo json_encode($rspta, true);
        } else {
          $rspta = $categoria_otros_gastos->editar($idotros_gastos_categoria, $nombre_otros_gastos, $descripcion_otros_gastos);
          echo json_encode($rspta, true);
        }
      break;

      case 'desactivar':
        $rspta = $categoria_otros_gastos->desactivar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'eliminar':
        $rspta = $categoria_otros_gastos->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_datos':
        $rspta = $categoria_otros_gastos->mostrar($_GET["id"]);
        //Codificar el resultado utilizando json
        echo json_encode($rspta, true);
      break;

      case 'tabla_principal':
        $rspta = $categoria_otros_gastos->tabla_principal();
        //Vamos a declarar un array
        $data = [];
        $cont = 1;

        $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

        if ($rspta['status'] == true) {

          foreach ($rspta['data'] as $key => $value) {

            $data[] = array(
              "0" => $cont++,
              "1" => '<button class="btn btn-icon btn-sm btn-warning-light" onclick="mostrar_categoria_otros_gastos(' . $value['idotros_gastos_categoria'] . ')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                ' <button  class="btn btn-icon btn-sm btn-danger-light product-btn" onclick="eliminar_categoria_otros_gastos(' . $value['idotros_gastos_categoria'] . ', \'' . encodeCadenaHtml($value['nombre']) . '\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>',         
              "2" => $value['nombre'],
              "3" => $value['descripcion'],
              "4" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>'

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

      case 'salir':
        //Limpiamos las variables de sesión
        session_unset();
        //Destruìmos la sesión
        session_destroy();
        //Redireccionamos al login
        header("Location: ../index.php");
      break;

      default:
        $rspta = ['status' => 'error_code', 'message' => 'Te has confundido en escribir en el <b>swich.</b>', 'data' => []];
        echo json_encode($rspta, true);
      break;
    }
    
  } else {
    $retorno = ['status'=>'nopermiso', 'message'=>'No tienes acceso a este modulo, pide acceso a tu administrador', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);
  }  
}
ob_end_flush();
