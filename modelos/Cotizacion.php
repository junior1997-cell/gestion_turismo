<?php

  require "../config/Conexion_v2.php";

  class Cotizacion
  {

    //Implementamos nuestro constructor
    public $id_usr_sesion; public $id_persona_sesion; public $id_trabajador_sesion;
    // public $id_empresa_sesion;   
    public function __construct( )
    {
      $this->id_usr_sesion        =  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
      $this->id_persona_sesion    = isset($_SESSION['idpersona']) ? $_SESSION["idpersona"] : 0;
      $this->id_trabajador_sesion = isset($_SESSION['idpersona_trabajador']) ? $_SESSION["idpersona_trabajador"] : 0;
      // $this->id_empresa_sesion = isset($_SESSION['idempresa']) ? $_SESSION["idempresa"] : 0;
    }

    public function listar_tabla_facturacion( $fecha_i, $fecha_f, $cliente, $comprobante, $estado_sunat ) {    

      $filtro_id_trabajador  = ''; $filtro_fecha = ""; $filtro_cliente = ""; $filtro_comprobante = ""; $filtro_estado_sunat = "";

      if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {  $filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";    } 

      if ( !empty($fecha_i) && !empty($fecha_f) ) { $filtro_fecha = "AND DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') BETWEEN '$fecha_i' AND '$fecha_f'"; } 
      else if (!empty($fecha_i)) { $filtro_fecha = "AND DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') = '$fecha_i'"; }
      else if (!empty($fecha_f)) { $filtro_fecha = "AND DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') = '$fecha_f'"; }
      
      if ( empty($cliente) ) { } else {  $filtro_cliente = "AND v.idpersona_cliente = '$cliente'"; } 
      if ( empty($comprobante) ) { } else {  $filtro_comprobante = "AND v.idsunat_c01 = '$comprobante'"; } 
      if ( empty($estado_sunat) ) { } else {  $filtro_estado_sunat = "AND v.sunat_estado = '$estado_sunat'"; } 

      $sql = "SELECT v.*, LPAD(v.idventa, 5, '0') AS idventa_v2, CASE v.tipo_comprobante WHEN '07' THEN v.venta_total * -1 ELSE v.venta_total END AS venta_total_v2, 
      CASE v.tipo_comprobante WHEN '03' THEN 'BOLETA' WHEN '07' THEN 'NOTA CRED.' ELSE tc.abreviatura END AS tp_comprobante_v2,
      DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') as fecha_emision_format, LEFT(v.periodo_pago_month, 3) as periodo_pago_month_v2,  p.nombre_razonsocial, p.apellidos_nombrecomercial, p.tipo_documento, 
      p.numero_documento, p.foto_perfil, tc.abreviatura as tp_comprobante_v1, sdi.abreviatura as tipo_documento, v.estado,
      CASE 
        WHEN p.tipo_persona_sunat = 'NATURAL' THEN 
          CASE 
            WHEN LENGTH(  CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial)  ) <= 27 THEN  CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
            ELSE CONCAT( LEFT(CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial ), 27) , '...')
          END         
        WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN 
          CASE 
            WHEN LENGTH(  p.nombre_razonsocial  ) <= 27 THEN  p.nombre_razonsocial 
            ELSE CONCAT(LEFT( p.nombre_razonsocial, 27) , '...')
          END
        ELSE '-'
      END AS cliente_nombre_recortado,
      CASE 
        WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
        WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
        ELSE '-'
      END AS cliente_nombre_completo
      FROM venta AS v
      INNER JOIN persona_cliente AS pc ON pc.idpersona_cliente = v.idpersona_cliente
      INNER JOIN persona AS p ON p.idpersona = pc.idpersona
      INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
      INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.idtipo_comprobante = v.idsunat_c01
      WHERE v.estado = 1 AND v.estado_delete = 1 AND v.tipo_comprobante = '100' $filtro_id_trabajador $filtro_cliente $filtro_comprobante $filtro_estado_sunat $filtro_fecha
      ORDER BY v.fecha_emision DESC, p.nombre_razonsocial ASC;"; #return $sql;
      $venta = ejecutarConsulta($sql); if ($venta['status'] == false) {return $venta; }

      return $venta;
    }
    
    public function editar( $idventa, $idpersona_cliente,  $tipo_comprobante, $serie, $impuesto, $descripcion, $venta_subtotal, $tipo_gravada, $venta_igv, $venta_total, $fecha_venta, $img_comprob,        
    $idproducto, $unidad_medida, $cantidad, $precio_sin_igv, $precio_igv, $precio_con_igv,  $descuento, $subtotal_producto) {

      $sql_1 = "UPDATE venta SET idpersona_cliente = '$idpersona_cliente', fecha_venta = '$fecha_venta', tipo_comprobante = '$tipo_comprobante', serie_comprobante = '$serie', 
      val_igv = '$impuesto', descripcion = '$descripcion', subtotal = '$venta_subtotal', igv = '$venta_igv', total = '$venta_total', comprobante = '$img_comprob'
      WHERE idventa = '$idventa'";
      $result_sql_1 = ejecutarConsulta($sql_1, 'U');if ($result_sql_1['status'] == false) { return $result_sql_1; }

      // Eliminamos los productos
      $sql_del = "DELETE FROM venta_detalle WHERE idventa = '$idventa'";
      ejecutarConsulta($sql_del);

      // Creamos los productos
      foreach ($idproducto as $ii => $producto) {
        $sql_2 = "INSERT INTO venta_detalle(idproducto, idventa, cantidad, precio_sin_igv, igv, precio_con_igv, descuento, subtotal)
        VALUES ('$idproducto[$ii]', '$idventa', '$cantidad[$ii]', '$precio_sin_igv[$ii]', '$precio_igv[$ii]', '$precio_con_igv[$ii]', '$descuento[$ii]', '$subtotal_producto[$ii]');";
        $detalle_new =  ejecutarConsulta_retornarID($sql_2, 'C'); if ($detalle_new['status'] == false) { return  $detalle_new;}        
      }  
      
      return array('status' => true, 'message' => 'Datos actualizados correctamente.');
    }   

    public function eliminar($id){
      $sql = "UPDATE venta SET sunat_estado = 'ANULADO', estado_delete = '0' WHERE idventa = '$id'";
      return ejecutarConsulta($sql, 'D');
    }

    public function papelera($id){
      $sql = "UPDATE venta SET sunat_estado = 'ANULADO', estado = '0'  WHERE idventa = '$id'";
      return ejecutarConsulta($sql, 'T');
    } 

    public function cambiar_estado_vendido($id, $estado){
      $sql = "UPDATE venta SET cot_estado = '$estado' WHERE idventa = '$id'";
      return ejecutarConsulta($sql, 'T');
    } 

    Public function mini_reporte(){

      $meses_espanol = array( 1 => "Ene", 2 => "Feb", 3 => "Mar", 4 => "Abr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Ago", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dic" );

      $filtro_id_trabajador  = '';
      if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
        $filtro_id_trabajador = "pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
      } 

      $sql_00 ="SELECT v.cot_estado, COUNT( v.idventa ) as cantidad
      FROM venta as v
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente 
      WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.estado = '1' AND v.estado_delete = '1' 
      GROUP BY v.cot_estado;";
      $coun_comprobante = ejecutarConsultaArray($sql_00); if ($coun_comprobante['status'] == false) {return $coun_comprobante; }

      $sql_01 = "SELECT ROUND( COALESCE(( ( ventas_mes_actual.total_ventas_mes_actual - COALESCE(ventas_mes_anterior.total_ventas_mes_anterior, 0) ) / COALESCE( ventas_mes_anterior.total_ventas_mes_anterior, ventas_mes_actual.total_ventas_mes_actual ) * 100 ),0), 2 ) AS porcentaje, ventas_mes_actual.total_ventas_mes_actual, ventas_mes_anterior.total_ventas_mes_anterior
      FROM ( SELECT COALESCE(SUM(venta_total), 0) total_ventas_mes_actual FROM venta WHERE MONTH (periodo_pago_format) = MONTH (CURRENT_DATE()) AND YEAR (periodo_pago_format) = YEAR (CURRENT_DATE()) AND cot_estado = 'VENDIDO' ) AS ventas_mes_actual,
      ( SELECT SUM(venta_total) AS total_ventas_mes_anterior FROM venta WHERE MONTH (periodo_pago_format) = MONTH (CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR (periodo_pago_format) = YEAR (CURRENT_DATE() - INTERVAL 1 MONTH) AND cot_estado = 'VENDIDO' ) AS ventas_mes_anterior;";
      $vendido_p = ejecutarConsultaSimpleFila($sql_01); if ($vendido_p['status'] == false) {return $vendido_p; }
      $sql_01 = "SELECT IFNULL( SUM( venta_total), 0 ) as venta_total FROM venta WHERE sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND cot_estado = 'VENDIDO' AND estado = '1' AND estado_delete = '1';";
      $vendido = ejecutarConsultaSimpleFila($sql_01); if ($vendido['status'] == false) {return $vendido; }

      $sql_03 = "SELECT ROUND( COALESCE(( ( ventas_mes_actual.total_ventas_mes_actual - COALESCE(ventas_mes_anterior.total_ventas_mes_anterior, 0) ) / COALESCE( ventas_mes_anterior.total_ventas_mes_anterior, ventas_mes_actual.total_ventas_mes_actual ) * 100 ),0), 2 ) AS porcentaje, ventas_mes_actual.total_ventas_mes_actual, ventas_mes_anterior.total_ventas_mes_anterior
      FROM ( SELECT COALESCE(SUM(venta_total), 0) total_ventas_mes_actual FROM venta WHERE MONTH (periodo_pago_format) = MONTH (CURRENT_DATE()) AND YEAR (periodo_pago_format) = YEAR (CURRENT_DATE()) AND cot_estado = 'PENDIENTE' ) AS ventas_mes_actual,
      ( SELECT SUM(venta_total) AS total_ventas_mes_anterior FROM venta WHERE MONTH (periodo_pago_format) = MONTH (CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR (periodo_pago_format) = YEAR (CURRENT_DATE() - INTERVAL 1 MONTH) AND cot_estado = 'PENDIENTE' ) AS ventas_mes_anterior;";
      $pendiente_p = ejecutarConsultaSimpleFila($sql_03); if ($pendiente_p['status'] == false) {return $pendiente_p; }
      $sql_03 = "SELECT IFNULL( SUM( venta_total), 0 ) as venta_total FROM venta WHERE sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND cot_estado = 'PENDIENTE' AND estado = '1' AND estado_delete = '1';";
      $pendiente = ejecutarConsultaSimpleFila($sql_03); if ($pendiente['status'] == false) {return $pendiente; }      

      $mes_vendido = []; $mes_nombre = []; $date_now = date("Y-m-d");  $fecha_actual = date("Y-m-d", strtotime("-5 months", strtotime($date_now)));
      for ($i=1; $i <=6 ; $i++) { 
        $nro_mes = floatval( date("m", strtotime($fecha_actual)) );
        $sql_mes = "SELECT MONTHNAME(fecha_emision) AS fecha_emision , COALESCE(SUM(venta_total), 0) AS venta_total FROM venta WHERE MONTH(fecha_emision) = '$nro_mes' AND sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND cot_estado = 'VENDIDO' AND estado = '1' AND estado_delete = '1';";
        $mes_f = ejecutarConsultaSimpleFila($sql_mes); if ($mes_f['status'] == false) {return $mes_f; }
        array_push($mes_vendido, floatval($mes_f['data']['venta_total']) ); array_push($mes_nombre, $meses_espanol[$nro_mes] );
        $fecha_actual= date("Y-m-d", strtotime("1 months", strtotime($fecha_actual)));
      }

      $mes_pendiente = [];  $date_now = date("Y-m-d");  $fecha_actual = date("Y-m-d", strtotime("-5 months", strtotime($date_now)));
      for ($i=1; $i <=6 ; $i++) { 
        $sql_mes = "SELECT MONTHNAME(fecha_emision) AS fecha_emision , COALESCE(SUM(venta_total), 0) AS venta_total FROM venta WHERE MONTH(fecha_emision) = '".date("m", strtotime($fecha_actual))."' AND sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND cot_estado = 'PENDIENTE' AND estado = '1' AND estado_delete = '1';";
        $mes_b = ejecutarConsultaSimpleFila($sql_mes); if ($mes_b['status'] == false) {return $mes_b; }
        array_push($mes_pendiente, floatval($mes_b['data']['venta_total']) ); 
        $fecha_actual= date("Y-m-d", strtotime("1 months", strtotime($fecha_actual)));
      }      

      return ['status' => true, 'message' =>'todo okey', 
        'data'=>[
          'mes_nombre'        => $mes_nombre,
          'coun_comprobante'  => $coun_comprobante['data'],
          'vendido'           => floatval($vendido['data']['venta_total']), 'vendido_p'     => floatval($vendido_p['data']['porcentaje']) , 'vendido_line'      => $mes_vendido ,
          'pendiente'         => floatval($pendiente['data']['venta_total']), 'pendiente_p' => floatval($pendiente_p['data']['porcentaje']) , 'pendiente_line'  => $mes_pendiente ,          
        ]
      ];

    }

    public function listar_producto_x_codigo($codigo){
      $sql = "SELECT p.*, um.nombre AS unidad_medida, um.abreviatura as um_abreviatura, cat.nombre AS categoria, mc.nombre AS marca
      FROM producto AS p
      INNER JOIN sunat_unidad_medida AS um ON p.idsunat_unidad_medida = um.idsunat_unidad_medida
      INNER JOIN categoria AS cat ON p.idcategoria = cat.idcategoria
      INNER JOIN marca AS mc ON p.idmarca = mc.idmarca
      WHERE (p.codigo = '$codigo' OR p.codigo_alterno = '$codigo' ) AND p.estado = 1 AND p.estado_delete = 1;";
      return ejecutarConsultaSimpleFila($sql);      
    }

    // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
    public function select2_cliente(){
      $filtro_id_trabajador  = '';
      if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
        $filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
      } 
      $sql = "SELECT LPAD(pc.idpersona_cliente, 5, '0') as idcliente, pc.idpersona_cliente, p.idpersona,  p.nombre_razonsocial, p.apellidos_nombrecomercial,
      CASE 
        WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
        WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
        ELSE '-'
      END AS cliente_nombre_completo,  
      sc06.abreviatura as nombre_tipo_documento, IFNULL(p.tipo_documento, '') as tipo_documento, IFNULL(p.numero_documento,'') as numero_documento, IFNULL(p.direccion,'') as direccion,
      pl.nombre as plan_pago, pl.costo as plan_costo
      FROM persona_cliente as pc      
      INNER JOIN persona as p ON p.idpersona = pc.idpersona
      INNER JOIN sunat_c06_doc_identidad as sc06 ON sc06.code_sunat = p.tipo_documento
      INNER JOIN plan as pl ON pl.idplan = pc.idplan
      WHERE p.estado = '1' and p.estado_delete = '1' and pc.estado = '1' and pc.estado_delete = '1' and p.idpersona > 2 $filtro_id_trabajador ORDER BY p.nombre_razonsocial ASC;"; 
      return ejecutarConsultaArray($sql);
    }

    public function select2_comprobantes_anular($tipo_comprobante){
      $filtro_id_trabajador  = '';
      if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
        $filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
      } 
      $sql = "SELECT v.idventa, v.tipo_comprobante, v.serie_comprobante, v.numero_comprobante,  
      CASE v.tipo_comprobante WHEN '03' THEN 'BOLETA' WHEN '07' THEN 'NOTA CRED.' ELSE tc.abreviatura END AS nombre_tipo_comprobante_v2,
      CASE
        WHEN TIMESTAMPDIFF(DAY, v.fecha_emision, CURDATE()) = 1 THEN 'hace 1 día'
        WHEN TIMESTAMPDIFF(DAY, v.fecha_emision, CURDATE()) > 1 THEN CONCAT('hace ', TIMESTAMPDIFF(DAY, v.fecha_emision, CURDATE()), ' días')
        ELSE 'hoy'
      END AS fecha_emision_dif
      FROM venta as v
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente
      INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.codigo = v.tipo_comprobante
      WHERE v.tipo_comprobante = '$tipo_comprobante' AND v.sunat_estado ='ACEPTADA' AND  v.fecha_emision >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)  $filtro_id_trabajador 
      ORDER BY CONVERT(v.numero_comprobante, SIGNED) DESC;";  #return $sql;
      return ejecutarConsultaArray($sql); 
    }

    public function select2_series_comprobante($codigo, $nc_tp){

      $filtro_nc = "";

      if ($codigo == '07') {        // Acciones solo si es: Nota de Credito 
      
        if ($nc_tp == '01') {
          $filtro_nc = "AND stp.abreviatura LIKE '%FACTURA'";
        }else if ($nc_tp == '03') {
          $filtro_nc = "AND stp.abreviatura LIKE '%BOLETA'";
        }
      }

      $sql = "SELECT stp.abreviatura,  stp.serie
      FROM sunat_usuario_comprobante as suc
      INNER JOIN sunat_c01_tipo_comprobante as stp ON stp.idtipo_comprobante = suc.idtipo_comprobante
      WHERE stp.codigo = '$codigo'  $filtro_nc AND suc.idusuario = '$this->id_usr_sesion';";
      return ejecutarConsultaArray($sql);      
    }

    public function select2_codigo_x_anulacion_comprobante(){
      $sql = "SELECT idsunat_c09_codigo_nota_credito as idsunat_c09, codigo, nombre, estado FROM sunat_c09_codigo_nota_credito;";
      return ejecutarConsultaArray($sql);      
    }

    public function select2_filtro_tipo_comprobante($tipos){
      $sql="SELECT idtipo_comprobante, codigo, abreviatura AS tipo_comprobante, serie,
      CASE idtipo_comprobante WHEN '3' THEN 'BOLETA' WHEN '7' THEN 'NOTA CRED. FACTURA' WHEN '8' THEN 'NOTA CRED. BOLETA' ELSE abreviatura END AS nombre_tipo_comprobante_v2
      FROM sunat_c01_tipo_comprobante WHERE codigo in ($tipos) ;";
      return ejecutarConsultaArray($sql);
    }

    public function select2_filtro_cliente(){
      $filtro_id_trabajador  = '';
      if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
        $filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
      } 
      $sql="SELECT p.idpersona, pc.idpersona_cliente, 
      CASE 
        WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
        WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
        ELSE '-'
      END AS cliente_nombre_completo, p.numero_documento, sc06.abreviatura as nombre_tipo_documento,
      count(v.idventa) as cantidad
      FROM venta as v 
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente
      INNER JOIN persona as p ON p.idpersona = pc.idpersona
      INNER JOIN sunat_c06_doc_identidad as sc06 on p.tipo_documento=sc06.code_sunat
      WHERE v.estado = '1' AND v.estado_delete = '1' AND v.tipo_comprobante = '100' $filtro_id_trabajador
      GROUP BY p.idpersona, pc.idpersona_cliente, p.numero_documento, sc06.abreviatura ORDER BY  count(v.idventa) desc, p.nombre_razonsocial asc ;";
      return ejecutarConsultaArray($sql);
    }
  }
?>