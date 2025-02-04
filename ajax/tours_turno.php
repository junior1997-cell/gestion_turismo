<?php
ob_start();
if (strlen(session_id()) < 1) { session_start(); } //Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['configuracion'] == 1) {

    require_once "../modelos/Tours_turno.php";

    $tours_turno = new Tours_turno();

    $idtours_turno  = isset($_POST["idtours_turno"]) ? limpiarCadena($_POST["idtours_turno"]) : "";
    $nombre_turno   = isset($_POST["nombre_turno"]) ? limpiarCadena($_POST["nombre_turno"]) : "";


    switch ($_GET["op"]) {
      case 'guardar_y_editar_turno':
        if (empty($idtours_turno)) {
          $rspta = $tours_turno->insertar($nombre_turno);
          echo json_encode($rspta, true);
        } else {
          $rspta = $tours_turno->editar($idtours_turno, $nombre_turno);
          echo json_encode($rspta, true);
        }
      break;

      case 'desactivar_turno':
        $rspta = $tours_turno->desactivar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'eliminar_turno':
        $rspta = $tours_turno->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_turno':
        $rspta = $tours_turno->mostrar($idtours_turno);
        //Codificar el resultado utilizando json
        echo json_encode($rspta, true);
      break;

      case 'tabla_turno':
        $rspta = $tours_turno->tabla_principal();
        //Vamos a declarar un array
        $data = [];
        $cont = 1;

        $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

        if ($rspta['status'] == true) {

          foreach ($rspta['data'] as $key => $value) {

            $data[] = array(
              "0" => $cont++,
              "1" => '<button class="btn btn-icon btn-sm btn-warning-light" onclick="mostrar_turno(' . $value['idtours_turno'] . ')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>'.
                ' <button  class="btn btn-icon btn-sm btn-danger-light product-btn" onclick="eliminar_turno(' . $value['idtours_turno'] . ', \'' . encodeCadenaHtml($value['nombre']) . '\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>',         
              "2" => $value['nombre'],
              "3" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>'

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
