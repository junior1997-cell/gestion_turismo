<?php
require '../vendor/autoload.php';
use Luecano\NumeroALetras\NumeroALetras;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;

date_default_timezone_set('America/Lima'); $date_now = date("d_m_Y__h_i_s_A");
$imagen_error = "this.src='../dist/svg/404-v2.svg'";
$toltip = '<script> $(function () { $(\'[data-toggle="tooltip"]\').tooltip(); }); </script>';    
$scheme_host =  ($_SERVER['HTTP_HOST'] == 'localhost' ? 'http://localhost/venta_romero/' :  $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'].'/');

//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1){session_start();} 

if (!isset($_SESSION["user_nombre"])) {
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
  if ($_SESSION['facturacion'] == 1) { 
    
    require_once "../modelos/Facturacion.php";                                        // Incluímos la clase Venta
    
    $facturacion    = new Facturacion();                                              // Instanciamos a la clase con el objeto venta
    $numero_a_letra = new NumeroALetras();                                            // Instanciamos a la clase con el objeto venta

    if (!isset($_GET["id"])) { echo "Datos incompletos (indefinido)"; die(); }        // Validamos la existencia de la variable
    if (empty($_GET["id"])) {  echo "Datos incompletos (".$_GET["id"].")"; die(); }   // validamos el valor de la variable
    
    $empresa_f        = $facturacion->datos_empresa();    
    $venta_f          = $facturacion->mostrar_detalle_venta($_GET["id"]);

    if ( empty($venta_f['data']['venta']) ) { echo "Comprobante no existe"; die();  }

    $logo_empresa = "../assets/images/brand-logos/logo1.png";          

    // Emrpesa emisora ================================================================================
    $e_razon_social       = mb_convert_encoding($empresa_f['data']['nombre_razon_social'], 'UTF-8', mb_detect_encoding($empresa_f['data']['nombre_razon_social'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_comercial          = mb_convert_encoding($empresa_f['data']['nombre_comercial'], 'UTF-8', mb_detect_encoding($empresa_f['data']['nombre_comercial'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_domicilio_fiscal   = mb_convert_encoding($empresa_f['data']['domicilio_fiscal'], 'UTF-8', mb_detect_encoding($empresa_f['data']['domicilio_fiscal'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_tipo_documento     = $empresa_f['data']['tipo_documento'];
    $e_numero_documento   = $empresa_f['data']['numero_documento'];
    $e_telefono1          = $empresa_f['data']['telefono1'];
    $e_telefono2          = $empresa_f['data']['telefono2'];
    $e_correo             = mb_convert_encoding($empresa_f['data']['correo'], 'UTF-8', mb_detect_encoding($empresa_f['data']['correo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_web                = mb_convert_encoding($empresa_f['data']['web'], 'UTF-8', mb_detect_encoding($empresa_f['data']['web'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_web_consulta_cp    = mb_convert_encoding($empresa_f['data']['web_consulta_cp'], 'UTF-8', mb_detect_encoding($empresa_f['data']['web_consulta_cp'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

    $e_distrito           = mb_convert_encoding($empresa_f['data']['distrito'], 'UTF-8', mb_detect_encoding($empresa_f['data']['distrito'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_provincia          = mb_convert_encoding($empresa_f['data']['provincia'], 'UTF-8', mb_detect_encoding($empresa_f['data']['provincia'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_departamento       = mb_convert_encoding($empresa_f['data']['departamento'], 'UTF-8', mb_detect_encoding($empresa_f['data']['departamento'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $e_codubigueo         = mb_convert_encoding($empresa_f['data']['codubigueo'], 'UTF-8', mb_detect_encoding($empresa_f['data']['codubigueo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

    // Cliente receptor ================================================================================
    $c_nombre_completo    = mb_convert_encoding($venta_f['data']['venta']['cliente_nombre_completo'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['cliente_nombre_completo'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $c_tipo_documento     = $venta_f['data']['venta']['tipo_documento'];
    $c_tipo_documento_name= $venta_f['data']['venta']['nombre_tipo_documento'];
    $c_numero_documento   = $venta_f['data']['venta']['numero_documento'];
    $c_direccion          = mb_convert_encoding($venta_f['data']['venta']['direccion'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['direccion'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $c_nc_serie_y_numero  = mb_convert_encoding($venta_f['data']['venta']['nc_serie_y_numero'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['nc_serie_y_numero'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    
    $c_landing_user       = mb_convert_encoding($venta_f['data']['venta']['landing_user'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['landing_user'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $c_idventa_v2         = $venta_f['data']['venta']['idventa_v2'];

    
    // Data comprobante ================================================================================
    $metodo_pago          = mb_convert_encoding($venta_f['data']['venta']['metodo_pago'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['metodo_pago'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $mp_serie_comprobante = $venta_f['data']['venta']['mp_serie_comprobante'] == null || $venta_f['data']['venta']['mp_serie_comprobante'] == '' ? '-': mb_convert_encoding($venta_f['data']['venta']['mp_serie_comprobante'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['mp_serie_comprobante'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

    $user_en_atencion     = mb_convert_encoding($venta_f['data']['venta']['user_en_atencion'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['user_en_atencion'], "UTF-8, ISO-8859-1, ISO-8859-15", true));

    $fecha_emision        = $venta_f['data']['venta']['fecha_emision'];
    $fecha_emision_format = $venta_f['data']['venta']['fecha_emision_format'];
    $fecha_emision_dmy    = $venta_f['data']['venta']['fecha_emision_dmy'];
    $fecha_emision_hora12 = $venta_f['data']['venta']['fecha_emision_hora12'];
    $serie_comprobante    = $venta_f['data']['venta']['serie_comprobante'];
    $numero_comprobante   = $venta_f['data']['venta']['numero_comprobante'];
    $serie_y_numero_comprobante   = $venta_f['data']['venta']['serie_y_numero_comprobante'];
    $nombre_comprobante   = $venta_f['data']['venta']['tipo_comprobante'] == '12' ? 'NOTA DE VENTA' : ( $venta_f['data']['venta']['tipo_comprobante'] == '07' ? 'NOTA DE CRÉDITO' : $venta_f['data']['venta']['nombre_comprobante']);

    $venta_subtotal       = number_format( floatval($venta_f['data']['venta']['venta_subtotal']), 2, '.', ',' );
    $venta_subtotal_no_dcto = number_format( (floatval($venta_f['data']['venta']['venta_subtotal']) + floatval($venta_f['data']['venta']['venta_descuento'])), 2, '.', ',' );
    $venta_descuento      = number_format( floatval($venta_f['data']['venta']['venta_descuento']), 2, '.', ',' );
    $venta_igv            = number_format( floatval($venta_f['data']['venta']['venta_igv']), 2, '.', ',' );
    $venta_total          = number_format( floatval($venta_f['data']['venta']['venta_total']), 2, '.', ',' );
    $impuesto             = floatval($venta_f['data']['venta']['impuesto']). " %";
    $total_recibido       = number_format( floatval($venta_f['data']['venta']['total_recibido']), 2, '.', ',' );
    $total_vuelto         = number_format( floatval($venta_f['data']['venta']['total_vuelto']), 2, '.', ',' );

    $gravada              = "0.00";
    $exonerado            = number_format( floatval($venta_f['data']['venta']['venta_subtotal']), 2, '.', ',' );  

    $observacion_documento= mb_convert_encoding($venta_f['data']['venta']['observacion_documento'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['observacion_documento'], "UTF-8, ISO-8859-1, ISO-8859-15", true));
    $sunat_hash           = mb_convert_encoding($venta_f['data']['venta']['sunat_hash'], 'UTF-8', mb_detect_encoding($venta_f['data']['venta']['sunat_hash'], "UTF-8, ISO-8859-1, ISO-8859-15", true));


    // detalle x producto ================================================================================
    $html_venta = ''; $cont = 1; $cantidad = 0;
    
    foreach ($venta_f['data']['detalle'] as $key => $val) {  
      
      $es_cobro       = $val['es_cobro'];
      $p_p_month_year = $val['es_cobro'] == 'SI' ? ' - ' . $val['periodo_pago_v2']: '';
      $p_p_year       = $val['periodo_pago_year'];
    
      $html_venta .= '<tr >'.       
       '<td>' . floatval($val['cantidad'])  . '</td>' .
       '<td >' . ($val['nombre_producto'] .$p_p_month_year) . '</td>' .
       '<td style="text-align: right;">' . number_format( floatval($val['precio_venta']) , 2, '.', ',') . '</td>' .
       '<td style="text-align: right;">' . number_format( floatval($val['subtotal_no_descuento']) , 2, '.', ',') . '</td>' .
       '</tr>';
      $cantidad += floatval($val['cantidad']);
    }

    // Generar QR ================================================================================
    
    $dataTxt = $e_numero_documento . "|" . 6 . "|" . $serie_comprobante . "|" . $numero_comprobante . "|0.00|" . $venta_total . "|" . $fecha_emision_format . "|" . $c_tipo_documento_name . "|" . $c_numero_documento . "|";

    $filename = $serie_y_numero_comprobante . '.png';
    $qr_code = QrCode::create($dataTxt)->setEncoding(new Encoding('UTF-8'))->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)->setSize(600)->setMargin(10)->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)->setForegroundColor(new Color(0, 0, 0))->setBackgroundColor(new Color(255, 255, 255));

    $label = Label::create( $serie_y_numero_comprobante)->setTextColor(new Color(255, 0, 0)); // Create generic label  
    $writer = new PngWriter(); // Create IMG
    $result = $writer->write($qr_code, label: $label); 
    $result->saveToFile(__DIR__.'/generador-qr/ticket/'.$filename); // Save it to a file  
    $logoQr = $result->getDataUri();// Generate a data URI

    //NUMERO A LETRA ================================================================================    
    $total_en_letra = $numero_a_letra->toInvoice( floatval($venta_f['data']['venta']['venta_total']) , 2, " SOLES" );  

    ?>
    <html>

    <head>
      <meta http-equiv="content-type" content="text/html; charset=utf-8" />
      <title><?php echo $nombre_comprobante .' - '. $serie_y_numero_comprobante ; ?></title>
      
      <!-- Bootstrap Css -->
      <link id="style" href="../assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

      <!-- Style Css -->
      <link href="../assets/css/styles.min.css" rel="stylesheet">
      <link href="../assets/css/style_new.css" rel="stylesheet">

      <!-- Style tiket -->      
      <style> @media print {  .tm_hide_print {  display: none !important;  }  } </style>
    </head>

    <body style="background-color: white; display: flex;  justify-content: center;  align-items: center;">
      
      <div class="d-block align-items-center justify-content-between tm_hide_print">
        <a  type="button" class="btn btn-outline-info p-1 mb-2 m-l-5px w-40px" href="javascript:window.print()" data-bs-toggle="tooltip" title="Imprimir Ticket">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer">
            <polyline points="6 9 6 2 18 2 18 9"></polyline>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
            <rect x="6" y="14" width="12" height="8"></rect>
          </svg>
        </a>

        <button type="button" class="btn btn-warning p-1 mb-2 m-l-5px w-40px" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="decargar_imagen();">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
        </button>      
      </div>    

      <!-- codigo imprimir -->
      <div id="iframe-img-descarga" style="background-color: #f0f1f7; border-radius: 5px;" >      
        
        <br>
        <!-- Detalle de empresa -->
        <table class="mx-3" border="0" align="center" width="300px">
          <tbody>
            <tr><td align="center"><img src="<?php echo $logo_empresa; ?>" width="<?php echo ($empresa_f['data']['logo_c_r'] == 0 ? 150 : 100);?>"></td></tr>
            <tr align="center"><td style="font-size: 14px">.::<strong> <?php echo $e_comercial; ?> </strong>::.</td></tr>
            <tr align="center"><td style="font-size: 10px"> <?php echo $e_razon_social; ?> </td></tr>
            <tr align="center"><td style="font-size: 14px"> <strong> R.U.C. <?php echo $e_numero_documento; ?> </strong> </td></tr>
            <tr align="center"><td style="font-size: 10px"> <?php echo $e_domicilio_fiscal . ' <br> ' . $e_telefono1 . "-" . $e_telefono2; ?> </td></tr>
            <tr align="center"><td style="font-size: 10px"> <?php echo $e_correo; ?> </td></tr>
            <tr align="center"><td style="font-size: 10px"> <?php echo $e_web; ?> </td></tr>
            <tr><td style="text-align: center;"><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td></tr>
            <tr><td align="center"> <strong style="font-size: 14px"> <?php echo $nombre_comprobante ; ?> ELECTRÓNICA </strong> <br> <b style="font-size: 14px"><?php echo $serie_y_numero_comprobante ; ?> </b></td></tr>
            <tr><td style="text-align: center;"><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td></tr>
          </tbody>
        </table>

        <!-- Datos cliente -->
        <table border="0" align="center" width="300px" style="font-size: 12px">
          <tbody>
            <tr ><td>             <strong>Emisión:</strong> <?php echo $fecha_emision_dmy ; ?> </td> <td><strong>Hora:</strong> <?php echo $fecha_emision_hora12 ; ?> </td></tr>
            <tr ><td colspan="2"><strong>Cliente:</strong> <?php echo $c_nombre_completo ; ?> </td> </tr>
            <tr ><td colspan="2"><strong>DNI/RUC:</strong> <?php echo $c_numero_documento ; ?></td> </tr>
            <tr ><td colspan="2"><strong>Dir.:</strong> <?php echo $c_direccion ; ?></td></tr>                                        
            <tr ><td colspan="2"><strong>Doc. Baja:</strong> <?php echo $c_nc_serie_y_numero; ?> </td></tr>
            <tr ><td colspan="2"><strong>Atención:</strong> <?php echo $user_en_atencion; ?> </td> </tr>
            <tr ><td colspan="2"><strong>Observación:</strong> <?php echo $observacion_documento ; ?> </td></tr>
          </tbody>
        </table>            

        <!-- Mostramos los detalles de la venta en el documento HTML -->
        <table border="0" align="center" width="300px" style="font-size: 12px !important;">
          <thead>
            <tr><td colspan="4"><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td> </tr>
            <tr><th>Cant.</th> <th>Producto</th> <th>P.U.</th> <th>Importe</th></tr>
            <tr><td colspan="4"><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td></tr>
          </thead>        
          <tbody style="font-size: 11px !important;">
            <?php  echo $html_venta;  ?>
          </tbody>        
        </table>      
        
        <!-- Division -->
        <table border='0'  align='center' width='300px' style='font-size: 12px' >
          <tr><td><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td></tr>
          <tr></tr>
        </table>
        
        <!-- Detalles de totales sunat -->
        <table border='0'  align="center" width='300px' style='font-size: 12px'>
          <tr><td style="text-align: right;"><strong>Subtotal </strong></td>      <td>:</td> <td style="text-align: right;"> <?php echo $venta_subtotal_no_dcto; ?> </td></tr>
          <tr><td style="text-align: right;"><strong>Descuento </strong></td>     <td>:</td> <td style="text-align: right;"> <?php echo $venta_descuento; ?> </td></tr>
          <tr><td style="text-align: right;"><strong>Op. Gravada </strong></td>   <td>:</td> <td style="text-align: right;"> <?php echo $gravada; ?> </td></tr>
          <tr><td style="text-align: right;"><strong>Op. Exonerado </strong></td> <td>:</td> <td style="text-align: right;"> <?php echo $exonerado; ?> </td></tr>
          <tr><td style="text-align: right;"><strong>Op. Inafecto </strong></td>  <td>:</td> <td style="text-align: right;"> 0.00</td></tr>
          <tr><td style="text-align: right;"><strong>ICBPER</strong></td>         <td>:</td> <td style="text-align: right;"> 0.00 </td></tr>
          <tr><td style="text-align: right;"><strong>IGV (<?php echo $impuesto; ?>)</strong></td>         <td>:</td> <td style="text-align: right;"> <?php echo $venta_igv; ?> </td></tr>
          <tr><td style="text-align: right;"><strong>TOTAL</strong></td>          <td>:</td> <td style="text-align: right;"><strong> <?php echo $venta_total ?> </strong></td></tr>          
        </table>      

        <!-- Mostramos los totales de la venta en el documento HTML -->
        <table border='0' align="center" width='300px' style='font-size: 12px' >                
          <tr><td colspan="3"><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td></tr>
          <tr><td colspan="3"><strong>Son: </strong> <?php echo $total_en_letra; ?> </td></tr>
          <tr><td colspan="3"><div style="border-bottom: 1px dotted black; margin-top: 8px; margin-bottom: 8px;" ></div></td></tr>   
          <tr><td >           <b>Nro. Operación</b>                      <td>:</td> <td> <?php echo $c_idventa_v2; ?></td> </td></tr>
          <tr><td >           <b>Codigo Usuario</b>                      <td>:</td> <td> <?php echo $c_landing_user; ?></td> </td></tr>            
        </table>        

        <table border='0' align="center" width='300px' style='font-size: 12px'>
          <tbody>
            <tr>
              <td>
                <img src=<?php echo $logoQr; ?> width="100" height="100"><br>                
              </td>
              <td style="font-size: 11px;">
                <span>Autorizado mediante resolución Nro: 182-2016/SUNAT Representación impresa del comprobante de venta electrónico, puede ser consultada en:</span>
                <span class="fw-bold" ><b><?php echo $e_web; ?></b></span>
                <span style="font-size: 10px; margin-top: 5px;">Hash:  <?php echo $sunat_hash; ?>  </span>
              </td>
            </tr>
            <tr>
              <td style="text-align: center;" colspan="2" ></td>              
            </tr>
            <tr>
              <td style='font-size: 10px; text-align: center;' colspan="2"> <span><strong>SERVICIOS TRANSFERIDOS EN LA REGIÓN AMAZÓNICA SELVA PARA SER CONSUMIDOS EN LA MISMA.</strong></span></td>              
            </tr>
          </tbody>            
          <br>          
        </table>
        <p>&nbsp;</p>
      </div>

      <!-- Popper JS -->
      <script src="../assets/libs/@popperjs/core/umd/popper.min.js"></script>
      <!-- Bootstrap JS -->
      <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

      <!-- Dropzone JS -->
      <script src="../assets/libs/dom-to-image-master/dist/dom-to-image.min.js"></script>

      <script>
        function decargar_imagen() {
          
          var titulo = document.title; // Obtener el título de la página

          domtoimage.toJpeg(document.getElementById('iframe-img-descarga'), { quality: 0.95 }).then(function (dataUrl) {
            var link = document.createElement('a');
            link.download = `${titulo}.jpeg`;
            link.href = dataUrl;
            link.click();
          });
        }
      </script>

    </body>

    </html>
    <?php
  } else {
    echo 'No tiene permiso para visualizar el reporte';
  }
}
ob_end_flush();
?>