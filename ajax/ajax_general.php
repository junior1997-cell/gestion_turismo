<?php
ob_start();

if (strlen(session_id()) < 1) {
  session_start();
} //Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {

  $retorno = ['status' => 'login', 'message' => 'Tu sesion a terminado pe, inicia nuevamente', 'data' => []];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.

} else {

  require_once "../modelos/Ajax_general.php";
  require_once "../modelos/Ubigeo.php";

  $ajax_general = new Ajax_general($_SESSION['idusuario']);
  $_ubigeo       = new Ubigeo();

  $scheme_host  =  ($_SERVER['HTTP_HOST'] == 'localhost' ? 'http://localhost/gestion_turismo/' :  $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/');
  $imagen_error = "this.src='../dist/svg/404-v2.svg'";
  $toltip       = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

  switch ($_GET["op"]) {

      /* ══════════════════════════════════════ A P I S  ══════════════════════════════════════ */

    // RENIEC JDL
    case 'reniec_jdl':
      $dni = $_POST["dni"];
      $rspta = $ajax_general->datos_reniec_jdl($dni);
      if ( isset($rspta['success']) ) {
        if ($rspta['success'] === true) {
          echo json_encode($rspta);
        } else {
          $rspta = $ajax_general->datos_reniec_otro($dni);
          echo json_encode($rspta);
        }        
      }else{
        echo json_encode($rspta);
      }
     
    break;
    // RENIEC WFACX
    case 'reniec_otro':
      $dni = $_POST["dni"];
      $rspta = $ajax_general->datos_reniec_otro($dni);
      echo json_encode($rspta);
    break;
    // SUNAT JDL
    case 'sunat_jdl':
      $ruc = $_POST["ruc"];
      $rspta = $ajax_general->datos_sunat_jdl($ruc);
      echo json_encode($rspta, true);
    break;
    // SUNAT WFACX
    case 'sunat_otro':
      $ruc = $_POST["ruc"];
      $rspta = $ajax_general->datos_sunat_otro($ruc);
      echo json_encode($rspta);
    break;

    /* ══════════════════════════════════════ S U N A T   ══════════════════════════════════════ */

    case 'select2_tipo_documento':
      $rspta = $ajax_general->select2_tipo_documento();
      // echo json_encode($rspta, true); die;
      $data = "";

      if ($rspta['status']) {

        foreach ($rspta['data'] as $key => $value) {
          $data  .= '<option value="' . $value['code_sunat'] . '" title ="' . $value['nombre'] . '" >' . $value['abreviatura'] . '</option>';
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

    case 'selectChoice_tipo_documento':
      $rspta = $ajax_general->select2_tipo_documento();
      
      $data = [];

      if ($rspta['status'] == true) {

        foreach ($rspta['data'] as $key => $value) {
          $data[] = ['value' => $value['code_sunat'], 'label' => $value['abreviatura'], 'disabled'  => false, 'selected'  => false,];
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

    // ══════════════════════════════════════ U B I G E O - S E L E C T 2    D E P A R T A M E N T O ══════════════════════════════════════
    case 'select2_departamento':
      $rspta = $_ubigeo->select2_departamento();
      while ($reg = $rspta['data']->fetch_object()) {
        echo '<option value="' . $reg->nombre . '" iddepartamento = "' . $reg->iddepartamento . '" macroregion_minsa = "' . $reg->macroregion_minsa . '" iso_3166_2 = "' . $reg->iso_3166_2 . '" >' . $reg->nombre . '</option>';
      }
    break;

    case 'select2_departamento_id':
      $rspta = $_ubigeo->select2_departamento_id($_GET['id']);
      echo json_encode($rspta, true);
    break;

    // ══════════════════════════════════════ U B I G E O - S E L E C T 2    P R O V I N C I A ══════════════════════════════════════
    case 'select2_provincia':
      $rspta = $_ubigeo->select2_provincia();
      while ($reg = $rspta['data']->fetch_object()) {
        echo '<option value="' . $reg->nombre . '" idprovincia = "' . $reg->idprovincia . '" iddepartamento = "' . $reg->iddepartamento . '" >' . $reg->nombre . '</option>';
      }
    break;

    case 'select2_provincia_departamento':
      $rspta = $_ubigeo->select2_provincia_departamento($_GET['id']);
      while ($reg = $rspta['data']->fetch_object()) {
        echo '<option value=' . $reg->nombre . ' idprovincia = "' . $reg->idprovincia . '" iddepartamento = "' . $reg->iddepartamento . '" >' . $reg->nombre . '</option>';
      }
    break;

    case 'select2_provincia_id':
      $rspta = $_ubigeo->select2_provincia_id($_GET['id']);
      echo json_encode($rspta, true);
    break;

    // ══════════════════════════════════════ U B I G E O - S E L E C T 2    D I S T R I T O ══════════════════════════════════════
    case 'select2_distrito':
      $rspta = $_ubigeo->select2_distrito();
      $data = "";
      if ($rspta['status'] == true) {
        foreach ($rspta['data'] as $key => $reg) {
          $data .= '<option value="' . $reg['nombre'] . '" title="' . $reg['provincia'] . '" data-iddistrito="' . $reg['idubigeo_distrito'] . '"  iddistrito="' . $reg['idubigeo_distrito'] . '" iddepartamento= "' . $reg['idubigeo_departamento'] . '" ubigeo_inei="' . $reg['ubigeo_inei'] . '" latitud="' . $reg['latitud'] . '" longitud="' . $reg['longitud'] . '" Frontera="' . $reg['frontera'] . '" >' . $reg['nombre'] . '</option>';
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

    case 'selectChoice_distrito':
      $rspta = $_ubigeo->select2_distrito();
      
      $data = [];

      if ($rspta['status'] == true) {

        foreach ($rspta['data'] as $key => $value) {
          $data[] = [
            'value' => $value['nombre'], 'label' => $value['nombre'], 'disabled'  => false, 'selected'  =>  false , 
            'customProperties' => [
              'idubigeo_distrito' => $value['idubigeo_distrito'],
              'idubigeo_provincia' => $value['idubigeo_provincia'],
              'idubigeo_departamento' => $value['idubigeo_departamento']
            ]
          ];
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

    case 'select2_distrito_departamento':
      $rspta = $_ubigeo->select2_distrito_departamento($_GET['id']);
      while ($reg = $rspta['data']->fetch_object()) {
        echo '<option value="' . $reg->nombre . '" iddistrito="' . $reg->iddistrito . '" iddepartamento= "' . $reg->iddepartamento . '" ubigeo_inei="' . $reg->ubigeo_inei . '" latitud="' . $reg->latitud . '" longitud="' . $reg->longitud . '" Frontera="' . $reg->Frontera . '" >' . $reg->nombre . '</option>';
      }
    break;

    case 'select2_distrito_provincia':
      $rspta = $_ubigeo->select2_distrito_provincia($_GET['id']);
      while ($reg = $rspta['data']->fetch_object()) {
        echo '<option value="' . $reg->nombre . '" iddistrito="' . $reg->iddistrito . '" iddepartamento= "' . $reg->iddepartamento . '" ubigeo_inei="' . $reg->ubigeo_inei . '" latitud="' . $reg->latitud . '" longitud="' . $reg->longitud . '" Frontera="' . $reg->Frontera . '" >' . $reg->nombre . '</option>';
      }
    break;

    case 'select2_distrito_id':
      $rspta = $_ubigeo->select2_distrito_id($_GET['id']);
      echo json_encode($rspta, true);
    break;

    // ══════════════════════════════════════ C L I E N T E  ══════════════════════════════════════
    case 'update_nro_documento_cliente':
      $rspta = $ajax_general->update_nro_documento_cliente($_GET['idpersona_cliente'], $_GET['numero_documento']);
      echo json_encode($rspta, true);
    break;

    // ══════════════════════════════════════ U S U A R I O - S E L E C T 2  ══════════════════════════════════════
    case 'select2_usuario_trabajador':
      $rspta = $ajax_general->select2_usuario_trabajador($_GET['id']);
      // echo json_encode($rspta, true); die;
      $data = "";

      if ($rspta['status']) {

        foreach ($rspta['data'] as $key => $value) {
          $data  .= '<option value=' . $value['idpersona'] . ' title="' . $value['foto_perfil'] . '" cargo="' . $value['cargo'] . '">' . $value['nombre_razonsocial'] . ' ' . $value['apellidos_nombrecomercial'] . ' - ' . $value['numero_documento'] . '</option>';
        }

        $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data, );

        echo json_encode($retorno, true);
      } else {
        echo json_encode($rspta, true);
      }
    break;

    // ══════════════════════════════════════ C A R G O - S E L E C T 2  ══════════════════════════════════════
    case 'select2_cargo':
      $rspta = $ajax_general->select2_cargo();
      // echo json_encode($rspta, true); die;
      $data = "";

      if ($rspta['status']) {

        foreach ($rspta['data'] as $key => $value) {
          $data  .= '<option value="' . $value['idcargo_trabajador'] . '"  >' . $value['nombre']  . '</option>';
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

    // ══════════════════════════════════════ B A N C O - S E L E C T 2  ══════════════════════════════════════
    case 'select2_banco':
      $rspta = $ajax_general->select2_banco();
      // echo json_encode($rspta, true); die;
      $data = "";

      if ($rspta['status']) {

        foreach ($rspta['data'] as $key => $value) {
          $data  .= '<option value="' . $value['idbancos'] . '"  >' . $value['nombre']  . '</option>';
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

    case 'selectChoice_banco':
      $rspta = $ajax_general->select2_banco();
      
      $data = [];

      if ($rspta['status'] == true) {

        foreach ($rspta['data'] as $key => $value) {
          $data[] = ['value' => $value['idbancos'], 'label' => $value['nombre'], 'disabled'  => false, 'selected'  => false,];
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

    // ══════════════════════════════════════ C A T E G O R I A   O T R O S   G A S T O S - S E L E C T 2  ══════════════════════════════════════
    case 'select2_categoria_otros_gastos':
      $rspta = $ajax_general->select2_categoria_otros_gastos();
      // echo json_encode($rspta, true); die;
      $data = "";

      if ($rspta['status']) {

        foreach ($rspta['data'] as $key => $value) {
          $data  .= '<option value="' . $value['idotros_gastos_categoria'] . '"  >' . $value['nombre']  . '</option>';
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

    // ══════════════════════════════════════ P R O D U C T O  ══════════════════════════════════════
    case 'create_code_producto' :
      $rspta = $ajax_general->create_code_producto($_GET["pre_codigo"]);
      echo json_encode($rspta, true);
    break;

    // ══════════════════════════════════════ DEFAULT ══════════════════════════════════════
    default:
      $rspta = ['status' => 'error_code', 'message' => 'Te has confundido en escribir en el <b>swich.</b>', 'data' => []];
      echo json_encode($rspta, true);
    break;
  }
}

ob_end_flush();
