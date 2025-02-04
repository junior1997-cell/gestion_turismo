<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

class Reporte_x_trabajador
{
	public $id_usr_sesion;
	public $id_persona_sesion;
	public $id_trabajador_sesion;
	//Implementamos nuestro constructor
	public function __construct()
	{
		$this->id_usr_sesion =  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
		$this->id_persona_sesion = isset($_SESSION['idpersona']) ? $_SESSION["idpersona"] : 0;
		$this->id_trabajador_sesion = isset($_SESSION['idpersona_trabajador']) ? $_SESSION["idpersona_trabajador"] : 0;
	}
	/*T-- paepelera --desacctivar
	C-- crear
	R-- read
	U-- actualizar
	D-- delete -- eliminar*/

	// validar inicio de sesión del usuario cliente
	public function verificar($login, $clave)
	{
		$sql = "SELECT pc.idpersona_cliente, p.idpersona, p.nombre_razonsocial, p.apellidos_nombrecomercial
		FROM persona_cliente AS pc
		INNER JOIN persona AS p ON pc.idpersona = p.idpersona
		WHERE landing_user = '$login' AND landing_password = '$clave'
		AND estado = 1 AND estado_delete = 1";
		$user = ejecutarConsultaSimpleFila($sql);
		if ($user['status'] == false) {
			return $user;
		}
		$data = ['status' => true, 'message' => 'todo okey', 'data' => ['usuario' => $user['data']]];
		return $data;
	}

	//Implementar un método para listar los registros
	public function tabla_principal_cliente($filtro_trabajador, $filtro_anio_pago, $filtro_p_all_mes_pago, $filtro_tipo_comprob, $filtro_p_all_es_cobro)
	{

		$filtro_sql_trab  = '';
		$filtro_sql_ap  = '';
		$filtro_sql_mp  = '';
		$filtro_sql_tc  = '';
		$filtro_sql_es_c  = '';

		/*filtro_trabajador: 7
		filtro_anio_pago: 2024
		filtro_p_all_mes_pago: Mayo
		filtro_tipo_comprob: 03*/

		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_sql_trab = "AND pt.idpersona_trabajador = '$this->id_trabajador_sesion'";
		}

		if (empty($filtro_trabajador) 	   || $filtro_trabajador 	   == 'TODOS') {
		} else {
			$filtro_sql_trab	= "AND pt.idpersona_trabajador = '$filtro_trabajador'";
		}
		if (empty($filtro_anio_pago) 	   || $filtro_anio_pago 		 == 'TODOS') {
		} else {
			$filtro_sql_ap 	= "AND v.name_year             = '$filtro_anio_pago'";
		}
		if (empty($filtro_p_all_mes_pago) || $filtro_p_all_mes_pago == 'TODOS') {
		} else {
			$filtro_sql_mp 		= "AND v.name_month            = '$filtro_p_all_mes_pago'";
		}
		if (empty($filtro_tipo_comprob)   || $filtro_tipo_comprob   == 'TODOS') {
		} else {
			$filtro_sql_tc 		= "AND v.tipo_comprobante      = '$filtro_tipo_comprob'";
		}
		if (empty($filtro_p_all_es_cobro)   || $filtro_p_all_es_cobro   == 'TODOS') {
		} else {
			$filtro_sql_es_c 		= "AND v.es_cobro      = '$filtro_p_all_es_cobro'";
		}

		$sql = "SELECT v.idventa, 		
		CASE 
		WHEN p1.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p1.nombre_razonsocial, ' ', p1.apellidos_nombrecomercial) 
		WHEN p1.tipo_persona_sunat = 'JURÍDICA' THEN p1.nombre_razonsocial 
		ELSE '-'
		END AS nombre_completoCliente, 
		i.abreviatura as tipoDocumentoCliente, 
		CASE v.tipo_comprobante WHEN '03' THEN 'BOLETA' WHEN '07' THEN 'NOTA CRED.' ELSE tc.abreviatura END AS tp_comprobante_v2,
		p1.numero_documento nroDocumentoCliente, p1.celular as cellCliente, 
		p1.foto_perfil as foto_perfilCliente, pc.idpersona_cliente as idCliente, 
		p2.nombre_razonsocial as nombre_completoTrabajador, 
		pt.idpersona_trabajador as idTrabajador,tc.abreviatura as tipo_comprobante, v.user_created, 
		u.idusuario, u.idpersona, pu.nombre_razonsocial as user_created_pago  ,
		v.crear_enviar_sunat, v.es_cobro,  CONCAT(v.serie_comprobante,'-', v.numero_comprobante) as correlativo, v.fecha_emision, v.name_day, 
		v.name_month, v.name_year, v.periodo_pago, v.periodo_pago_format, CONCAT(v.periodo_pago_month,' ', v.periodo_pago_year ) as peridoPago,
		v.venta_total total_general,vd.subtotal as total_Pag_servicio , v.observacion_documento, 
		v.estado, v.estado_delete, v.created_at, v.updated_at, v.user_trash, v.user_delete, v.user_created, v.user_updated
		FROM venta as v
    INNER JOIN venta_detalle as vd on v.idventa = vd.idventa
		INNER JOIN sunat_c01_tipo_comprobante as tc on v.tipo_comprobante = tc.codigo
		INNER JOIN persona_cliente as pc on v.idpersona_cliente= pc.idpersona_cliente
		INNER JOIN persona_trabajador as pt on pc.idpersona_trabajador = pt.idpersona_trabajador
		INNER JOIN persona as p1 on pc.idpersona = p1.idpersona
		INNER JOIN sunat_c06_doc_identidad as i on p1.tipo_documento=i.code_sunat  
		INNER JOIN persona as p2 on pt.idpersona = p2.idpersona
		INNER JOIN usuario as u on v.user_created = u.idusuario
		INNER JOIN persona as pu on u.idpersona = pu.idpersona
		WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') and v.estado='1' and v.estado_delete ='1' and vd.um_nombre='SERVICIOS'
		and v.tipo_comprobante !='07'
		
		$filtro_sql_trab $filtro_sql_ap $filtro_sql_mp $filtro_sql_tc $filtro_sql_es_c
		ORDER BY v.idventa DESC";
		return ejecutarConsulta($sql);
	}

	public function tabla_cliente_x_c($filtro_trabajador, $filtro_anio_pago, $filtro_p_all_mes_pago)
	{
		$sql = "SELECT 	pc.idpersona_cliente, pc.idpersona, pc.idpersona_trabajador ,	
					CASE 
						WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
						WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
					ELSE '-'
					END AS nombre_completoCliente, pc.idplan, pl.nombre as nombre_plan, pl.costo, pc.fecha_cancelacion,i.abreviatura as tipoDocumentoCliente, 
					 p.celular as cellCliente, p.foto_perfil as foto_perfilCliente,p.numero_documento as nroDocumentoCliente
					FROM persona_cliente AS pc
					LEFT JOIN venta AS v ON pc.idpersona_cliente = v.idpersona_cliente AND v.name_year='$filtro_anio_pago' AND v.name_month='$filtro_p_all_mes_pago'
					JOIN persona_trabajador as pt ON pc.idpersona_trabajador = pt.idpersona_trabajador
					INNER JOIN plan AS pl ON pc.idplan = pl.idplan
					INNER JOIN persona as p ON pc.idpersona = p.idpersona
					INNER JOIN persona as p1 ON pt.idpersona = p1.idpersona
					INNER JOIN sunat_c06_doc_identidad as i on p.tipo_documento=i.code_sunat  
					WHERE v.idpersona_cliente IS NULL AND pt.idpersona_trabajador ='$filtro_trabajador'
					AND pc.estado='1' AND pc.estado_delete = '1';";
		return ejecutarConsulta($sql);
	}

	/* ════════════════════════════════════════════════════════════════════════════
                              CARD MONTOS TOTALES
/* ════════════════════════════════════════════════════════════════════════════ */

	public function totales_card_F_B_T($filtro_trabajador, $filtro_anio_pago, $filtro_p_all_mes_pago, $filtro_tipo_comprob,$filtro_p_all_es_cobro)
	{

		$filtro_sql_trab  = '';
		$filtro_sql_ap  = '';
		$filtro_sql_mp  = '';
		$filtro_sql_tc  = '';
		$filtro_sql_trab_pend  = '';
		$filtro_sql_es_c  = '';

		//$data = Array(); $array_ticket = []; $array_factura = []; $array_boleta = [];  $array_total = [];
		$data  = array();


		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_sql_trab = "AND pt.idpersona_trabajador = '$this->id_trabajador_sesion'";
		}

		if (empty($filtro_trabajador) 	   || $filtro_trabajador 	   == 'TODOS') {
		} else {
			$filtro_sql_trab	= "AND pt.idpersona_trabajador = '$filtro_trabajador'";
		}
		if (empty($filtro_anio_pago) 	   || $filtro_anio_pago 		 == 'TODOS') {
		} else {
			$filtro_sql_ap 	= "AND v.name_year             = '$filtro_anio_pago'";
		}
		if (empty($filtro_p_all_mes_pago) || $filtro_p_all_mes_pago == 'TODOS') {
		} else {
			$filtro_sql_mp 		= "AND v.name_month            = '$filtro_p_all_mes_pago'";
		}
		if (empty($filtro_tipo_comprob)   || $filtro_tipo_comprob   == 'TODOS') {
		} else {
			$filtro_sql_tc 		= "AND v.tipo_comprobante      = '$filtro_tipo_comprob'";
		}
		if (empty($filtro_p_all_es_cobro)   || $filtro_p_all_es_cobro   == 'TODOS') {
		} else {
			$filtro_sql_es_c 		= "AND v.es_cobro      = '$filtro_p_all_es_cobro'";
		}

		// WHEN v.tipo_comprobante = '03' THEN 'BOLETA' 
		// WHEN v.tipo_comprobante = '07' THEN 'NOTA CRED.' 
		// WHEN v.tipo_comprobante = '01' THEN 'FACTURA'
		// WHEN v.tipo_comprobante = '08' THEN 'TICKET'

		$sql_boleta = "SELECT tc.abreviatura AS tp_comprobante_v2,
				COUNT(v.idventa) AS total_ventas,
				SUM(vd.subtotal) AS total_general
		FROM venta AS v
		INNER JOIN venta_detalle AS vd ON v.idventa = vd.idventa
		INNER JOIN sunat_c01_tipo_comprobante AS tc ON v.tipo_comprobante = tc.codigo
		INNER JOIN persona_cliente AS pc ON v.idpersona_cliente = pc.idpersona_cliente
		INNER JOIN persona_trabajador AS pt ON pc.idpersona_trabajador = pt.idpersona_trabajador
		INNER JOIN persona AS p1 ON pc.idpersona = p1.idpersona
		INNER JOIN sunat_c06_doc_identidad AS i ON p1.tipo_documento = i.code_sunat  
		INNER JOIN persona AS p2 ON pt.idpersona = p2.idpersona
		INNER JOIN usuario AS u ON v.user_created = u.idusuario
		INNER JOIN persona AS pu ON u.idpersona = pu.idpersona
		WHERE v.estado = '1' AND v.estado_delete = '1'  AND vd.um_nombre = 'SERVICIOS' and
			v.tipo_comprobante = '03' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR')
			$filtro_sql_trab $filtro_sql_ap $filtro_sql_mp $filtro_sql_tc $filtro_sql_es_c ";
		$sqlboleta = ejecutarConsultaSimpleFila($sql_boleta);
		if ($sqlboleta['status'] == false) {
			return $sqlboleta;
		}

		$nombre_tipo_comprob_b = (empty($sqlboleta['data']) ? 0 : (empty($sqlboleta['data']['tp_comprobante_v2']) ? "BOLETA" : "BOLETA"));
		$cantidad_comprob_b    = (empty($sqlboleta['data']) ? 0 : (empty($sqlboleta['data']['total_ventas']) ? 0 : floatval($sqlboleta['data']['total_ventas'])));
		$total_comprob_b       = (empty($sqlboleta['data']) ? 0 : (empty($sqlboleta['data']['total_general']) ? 0 : floatval($sqlboleta['data']['total_general'])));

		$new_boleta = array('nombre' => $nombre_tipo_comprob_b, 'cantidad' => $cantidad_comprob_b, 'total' => $total_comprob_b);


		$sql_factura = "SELECT tc.abreviatura AS tp_comprobante_v2,
				COUNT(v.idventa) AS total_ventas,
				SUM(vd.subtotal) AS total_general
		FROM venta AS v
		INNER JOIN venta_detalle AS vd ON v.idventa = vd.idventa
		INNER JOIN sunat_c01_tipo_comprobante AS tc ON v.tipo_comprobante = tc.codigo
		INNER JOIN persona_cliente AS pc ON v.idpersona_cliente = pc.idpersona_cliente
		INNER JOIN persona_trabajador AS pt ON pc.idpersona_trabajador = pt.idpersona_trabajador
		INNER JOIN persona AS p1 ON pc.idpersona = p1.idpersona
		INNER JOIN sunat_c06_doc_identidad AS i ON p1.tipo_documento = i.code_sunat  
		INNER JOIN persona AS p2 ON pt.idpersona = p2.idpersona
		INNER JOIN usuario AS u ON v.user_created = u.idusuario
		INNER JOIN persona AS pu ON u.idpersona = pu.idpersona
		WHERE v.estado = '1' AND v.estado_delete = '1'  AND vd.um_nombre = 'SERVICIOS' and
			v.tipo_comprobante = '01' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR')
			$filtro_sql_trab $filtro_sql_ap $filtro_sql_mp $filtro_sql_tc $filtro_sql_es_c ";

		$sqlfactura = ejecutarConsultaSimpleFila($sql_factura);
		if ($sqlfactura['status'] == false) {
			return $sqlfactura;
		}

		$nombre_tipo_comprob_f = (empty($sqlfactura['data']) ? 0 : (empty($sqlfactura['data']['tp_comprobante_v2']) ? "FACTURA" : $sqlfactura['data']['tp_comprobante_v2']));
		$cantidad_comprob_f    = (empty($sqlfactura['data']) ? 0 : (empty($sqlfactura['data']['total_ventas']) ? 0 : floatval($sqlfactura['data']['total_ventas'])));
		$total_comprob_f       = (empty($sqlfactura['data']) ? 0 : (empty($sqlfactura['data']['total_general']) ? 0 : floatval($sqlfactura['data']['total_general'])));

		$new_factura = array('nombre' => $nombre_tipo_comprob_f, 'cantidad' => $cantidad_comprob_f, 'total' => $total_comprob_f);

		$sql_ticket = "SELECT tc.abreviatura AS tp_comprobante_v2,
		COUNT(v.idventa) AS total_ventas, SUM(vd.subtotal) AS total_general
		FROM venta AS v
		INNER JOIN venta_detalle AS vd ON v.idventa = vd.idventa
		INNER JOIN sunat_c01_tipo_comprobante AS tc ON v.tipo_comprobante = tc.codigo
		INNER JOIN persona_cliente AS pc ON v.idpersona_cliente = pc.idpersona_cliente
		INNER JOIN persona_trabajador AS pt ON pc.idpersona_trabajador = pt.idpersona_trabajador
		INNER JOIN persona AS p1 ON pc.idpersona = p1.idpersona
		INNER JOIN sunat_c06_doc_identidad AS i ON p1.tipo_documento = i.code_sunat  
		INNER JOIN persona AS p2 ON pt.idpersona = p2.idpersona
		INNER JOIN usuario AS u ON v.user_created = u.idusuario
		INNER JOIN persona AS pu ON u.idpersona = pu.idpersona
		WHERE v.estado = '1' AND v.estado_delete = '1' AND vd.um_nombre = 'SERVICIOS' and
			v.tipo_comprobante = '12' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR')
			$filtro_sql_trab $filtro_sql_ap $filtro_sql_mp $filtro_sql_tc $filtro_sql_es_c ";
		$sqlticket = ejecutarConsultaSimpleFila($sql_ticket);
		if ($sqlticket['status'] == false) {
			return $sqlticket;
		}

		$nombre_tipo_comprob_tik = (empty($sqlticket['data']) ? 0 : (empty($sqlticket['data']['tp_comprobante_v2']) ? "TICKET" : $sqlticket['data']['tp_comprobante_v2']));
		$cantidad_comprob_tik    = (empty($sqlticket['data']) ? 0 : (empty($sqlticket['data']['total_ventas']) ? 0 : floatval($sqlticket['data']['total_ventas'])));
		$total_comprob_tik       = (empty($sqlticket['data']) ? 0 : (empty($sqlticket['data']['total_general']) ? 0 : floatval($sqlticket['data']['total_general'])));

		$new_ticket = array('nombre' => $nombre_tipo_comprob_tik, 'cantidad' => $cantidad_comprob_tik, 'total' => $total_comprob_tik);

		$sql_total = "SELECT 'TOTAL' AS tp_comprobante_v2,
				COUNT(v.idventa) AS total_ventas, SUM(vd.subtotal) AS total_general
		FROM venta AS v
		INNER JOIN venta_detalle AS vd ON v.idventa = vd.idventa
		INNER JOIN sunat_c01_tipo_comprobante AS tc ON v.tipo_comprobante = tc.codigo
		INNER JOIN persona_cliente AS pc ON v.idpersona_cliente = pc.idpersona_cliente
		INNER JOIN persona_trabajador AS pt ON pc.idpersona_trabajador = pt.idpersona_trabajador
		INNER JOIN persona AS p1 ON pc.idpersona = p1.idpersona
		INNER JOIN sunat_c06_doc_identidad AS i ON p1.tipo_documento = i.code_sunat  
		INNER JOIN persona AS p2 ON pt.idpersona = p2.idpersona
		INNER JOIN usuario AS u ON v.user_created = u.idusuario
		INNER JOIN persona AS pu ON u.idpersona = pu.idpersona
		WHERE v.estado = '1' AND v.estado_delete = '1' AND vd.um_nombre = 'SERVICIOS' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR')
		and v.tipo_comprobante !='07'
				$filtro_sql_trab $filtro_sql_ap $filtro_sql_mp $filtro_sql_tc $filtro_sql_es_c";

		$sqltotal = ejecutarConsultaSimpleFila($sql_total);
		if ($sqltotal['status'] == false) {
			return $sqltotal;
		}

		$nombre_tipo_comprob_tt = (empty($sqltotal['data']) ? 0 : (empty($sqltotal['data']['tp_comprobante_v2']) ? "TOTAL" : $sqltotal['data']['tp_comprobante_v2']));
		$cantidad_comprob_tt    = (empty($sqltotal['data']) ? 0 : (empty($sqltotal['data']['total_ventas']) ? 0 : floatval($sqltotal['data']['total_ventas'])));
		$total_comprob_tt       = (empty($sqltotal['data']) ? 0 : (empty($sqltotal['data']['total_general']) ? 0 : floatval($sqltotal['data']['total_general'])));

		$new_total = array('nombre' => $nombre_tipo_comprob_tt, 'cantidad' => $cantidad_comprob_tt, 'total' => $total_comprob_tt);

		// ------------------------ pendiente
		$new_totalpendiente = array('nombre' => 'PENDIENTE', 'cantidad' => 0, 'total' => 0);

		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_sql_trab_pend = "AND pt.idpersona_trabajador = '$this->id_trabajador_sesion'";
		}

		if (empty($filtro_trabajador) || $filtro_trabajador == 'TODOS' || empty($filtro_anio_pago) || $filtro_anio_pago == 'TODOS' || empty($filtro_p_all_mes_pago) || $filtro_p_all_mes_pago == 'TODOS') {
		} else {
			$filtro_sql_trab_pend	= "AND pt.idpersona_trabajador = '$filtro_trabajador'";
			$sql_pendiente = "SELECT COUNT(pc.idpersona_cliente) as cantidad, SUM(pl.costo) as total_pendiente
			FROM persona_cliente AS pc
			LEFT JOIN venta AS v ON pc.idpersona_cliente = v.idpersona_cliente AND v.name_year='$filtro_anio_pago' AND v.name_month='$filtro_p_all_mes_pago'
			JOIN persona_trabajador as pt ON pc.idpersona_trabajador = pt.idpersona_trabajador
			INNER JOIN plan AS pl ON pc.idplan = pl.idplan
			INNER JOIN persona as p ON pc.idpersona = p.idpersona
			INNER JOIN persona as p1 ON pt.idpersona = p1.idpersona
			INNER JOIN sunat_c06_doc_identidad as i on p.tipo_documento=i.code_sunat  
			WHERE v.idpersona_cliente IS NULL  $filtro_sql_trab_pend
			AND pc.estado='1' AND pc.estado_delete = '1';";

			$sqlpendiente = ejecutarConsultaSimpleFila($sql_pendiente);
			if ($sqlpendiente['status'] == false) {
				return $sqlpendiente;
			}

			$cantidad_comprob_tp    = (empty($sqlpendiente['data']) ? 0 : (empty($sqlpendiente['data']['cantidad']) ? 0 : floatval($sqlpendiente['data']['cantidad'])));
			$total_comprob_tp       = (empty($sqlpendiente['data']) ? 0 : (empty($sqlpendiente['data']['total_pendiente']) ? 0 : floatval($sqlpendiente['data']['total_pendiente'])));

			// Modificamos la cantidad y el total a 0
			$new_totalpendiente['cantidad'] = $cantidad_comprob_tp;
			$new_totalpendiente['total'] = $total_comprob_tp;
		};


		$data = array(
			'03_boleta'    => $new_boleta,
			'01_factura'   => $new_factura,
			'12_ticket'    => $new_ticket,
			'00_total'     => $new_total,
			'04_pendiente' => $new_totalpendiente
		);

		return $retorno = ['status' => true, 'message' => 'todo ok pe.', 'data' => $data, 'affected_rows' => $sqlboleta['affected_rows'],];
	}

	/**============================================================================ */
	/**============================================================================ */
	public function grafico_pay($filtro_trabajador, $filtro_anio_pago, $filtro_p_all_mes_pago, $filtro_tipo_comprob,$filtro_p_all_es_cobro)
	{
		//$dataarray  = array();
		$array_pay_total  = array();
		$array_pay_nombre  = array();

		$filtro_sql_trab  = '';
		$filtro_sql_ap  = '';
		$filtro_sql_mp  = '';
		$filtro_sql_tc  = '';
		$filtro_sql_trab_pend  = '';
		$filtro_sql_es_c = '';

		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_sql_trab = "AND pt.idpersona_trabajador = '$this->id_trabajador_sesion'";
		}

		if (empty($filtro_trabajador) 	   || $filtro_trabajador 	   == 'TODOS') {
		} else {
			$filtro_sql_trab	= "AND pt.idpersona_trabajador = '$filtro_trabajador'";
		}
		if (empty($filtro_anio_pago) 	   || $filtro_anio_pago 		 == 'TODOS') {
		} else {
			$filtro_sql_ap 	= "AND v.name_year             = '$filtro_anio_pago'";
		}
		if (empty($filtro_p_all_mes_pago) || $filtro_p_all_mes_pago == 'TODOS') {
		} else {
			$filtro_sql_mp 		= "AND v.name_month            = '$filtro_p_all_mes_pago'";
		}
		if (empty($filtro_tipo_comprob)   || $filtro_tipo_comprob   == 'TODOS') {
		} else {
			$filtro_sql_tc 		= "AND v.tipo_comprobante      = '$filtro_tipo_comprob'";
		}
		if (empty($filtro_p_all_es_cobro)   || $filtro_p_all_es_cobro   == 'TODOS') {
		} else {
			$filtro_sql_es_c 		= "AND v.es_cobro      = '$filtro_p_all_es_cobro'";
		}

		// ------------------------ pendiente
		array_push($array_pay_nombre, "PENDIENTE");
		$total_comprob_tp = 0;

		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_sql_trab_pend = "AND pt.idpersona_trabajador = '$this->id_trabajador_sesion'";
		}

		if (empty($filtro_trabajador) || $filtro_trabajador == 'TODOS' || empty($filtro_anio_pago) || $filtro_anio_pago == 'TODOS' || empty($filtro_p_all_mes_pago) || $filtro_p_all_mes_pago == 'TODOS') {
			$total_comprob_tp = 0;
		} else {
			$filtro_sql_trab_pend	= "AND pt.idpersona_trabajador = '$filtro_trabajador'";
			$sql_pendiente = "SELECT SUM(pl.costo) as total_pendiente
					FROM persona_cliente AS pc
					LEFT JOIN venta AS v ON pc.idpersona_cliente = v.idpersona_cliente AND v.name_year='$filtro_anio_pago' AND v.name_month='$filtro_p_all_mes_pago'
					JOIN persona_trabajador as pt ON pc.idpersona_trabajador = pt.idpersona_trabajador
					INNER JOIN plan AS pl ON pc.idplan = pl.idplan
					INNER JOIN persona as p ON pc.idpersona = p.idpersona
					INNER JOIN persona as p1 ON pt.idpersona = p1.idpersona
					INNER JOIN sunat_c06_doc_identidad as i on p.tipo_documento=i.code_sunat  
					WHERE v.idpersona_cliente IS NULL  $filtro_sql_trab_pend
					AND pc.estado='1' AND pc.estado_delete = '1';";

			$sqlpendiente = ejecutarConsultaSimpleFila($sql_pendiente);
			if ($sqlpendiente['status'] == false) {
				return $sqlpendiente;
			}

			$total_comprob_tp       = (empty($sqlpendiente['data']) ? 0 : (empty($sqlpendiente['data']['total_pendiente']) ? 0 : floatval($sqlpendiente['data']['total_pendiente'])));
		};

		// Modificamos la cantidad y el total a 0
		array_push($array_pay_total, $total_comprob_tp);


		$sql = "SELECT v.user_created,pu.nombre_razonsocial, SUM(vd.subtotal) as total 
		FROM venta as v
    INNER JOIN venta_detalle as vd on v.idventa = vd.idventa
		INNER JOIN persona_cliente as pc on v.idpersona_cliente= pc.idpersona_cliente
		INNER JOIN persona_trabajador as pt on pc.idpersona_trabajador = pt.idpersona_trabajador
		INNER JOIN persona as p2 on pt.idpersona = p2.idpersona
		INNER JOIN usuario as u on v.user_created = u.idusuario
		INNER JOIN persona as pu on u.idpersona = pu.idpersona
		WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') and v.estado='1' and v.estado_delete ='1'  
		and vd.um_nombre='SERVICIOS' AND  v.tipo_comprobante !='07'
		 $filtro_sql_trab $filtro_sql_ap $filtro_sql_mp $filtro_sql_tc $filtro_sql_es_c
    GROUP by v.user_created,pu.nombre_razonsocial;";

		$totales  = ejecutarConsultaArray($sql);

		foreach ($totales['data'] as $key => $value) {
			array_push($array_pay_total, (empty($value['total']) ? 0 : floatval($value['total'])));
			array_push($array_pay_nombre, $value['nombre_razonsocial']);
		}

		return $retorno = ['status' => true, 'message' => 'todo ok pe.', 'data' => ['series' => $array_pay_total, 'labels' => $array_pay_nombre],];
	}

	public function ventas_por_producto($filtro_trabajador, $filtro_anio_pago, $filtro_p_all_mes_pago, $filtro_tipo_comprob,$filtro_p_all_es_cobro)
	{
		$data = [];
		$filtro_sql_trab  = '';
		$filtro_sql_ap  = '';
		$filtro_sql_mp  = '';
		$filtro_sql_tc  = '';
		$filtro_sql_es_c  = '';
		$filtro_sql_trab_pend  = '';

		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_sql_trab = "AND pt.idpersona_trabajador = '$this->id_trabajador_sesion'";
		}

		if (empty($filtro_trabajador) 	  || $filtro_trabajador 	 	== 'TODOS') {
		} else {
			$filtro_sql_trab	= "AND pc.idpersona_trabajador = '$filtro_trabajador'";
		}
		if (empty($filtro_anio_pago) 	   	|| $filtro_anio_pago 		 	== 'TODOS') {
		} else {
			$filtro_sql_ap 		= "AND v.name_year             = '$filtro_anio_pago'";
		}
		if (empty($filtro_p_all_mes_pago) || $filtro_p_all_mes_pago	== 'TODOS') {
		} else {
			$filtro_sql_mp 		= "AND v.name_month            = '$filtro_p_all_mes_pago'";
		}
		if (empty($filtro_tipo_comprob)   || $filtro_tipo_comprob   == 'TODOS') {
		} else {
			$filtro_sql_tc 		= "AND v.tipo_comprobante      = '$filtro_tipo_comprob'";
		}
		if (empty($filtro_p_all_es_cobro)   || $filtro_p_all_es_cobro   == 'TODOS') {
		} else {
			$filtro_sql_es_c 		= "AND v.es_cobro      = '$filtro_p_all_es_cobro'";
		}

		$sql_0 = "SELECT vd.idproducto, pro.nombre as nombre_producto, SUM(vd.cantidad) as cantidad, SUM(vd.subtotal) as subtotal
		FROM venta_detalle as vd
    INNER JOIN venta as v ON v.idventa = vd.idventa
		INNER JOIN persona_cliente as pc on v.idpersona_cliente= pc.idpersona_cliente
		INNER JOIN producto as pro ON pro.idproducto = vd.idproducto
    WHERE v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') and v.estado='1' and v.estado_delete ='1' 
		and v.tipo_comprobante !='07'
		$filtro_sql_trab $filtro_sql_ap $filtro_sql_mp $filtro_sql_tc $filtro_sql_es_c
		GROUP BY vd.idproducto ORDER BY SUM(vd.cantidad) DESC;";
		$producto  = ejecutarConsultaArray($sql_0);

		foreach ($producto['data'] as $key => $val) {
			$id = $val['idproducto'];
			$sql_1 = "SELECT v.user_created, pu.nombre_razonsocial, pu.foto_perfil
			FROM venta as v
			INNER JOIN persona_cliente as pc on v.idpersona_cliente= pc.idpersona_cliente
			INNER JOIN venta_detalle as vd on vd.idventa = v.idventa
			INNER JOIN usuario as u on v.user_created = u.idusuario
			INNER JOIN persona as pu on u.idpersona = pu.idpersona
			WHERE vd.idproducto = '$id' and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') and  v.estado='1' and v.estado_delete ='1' 
			and v.tipo_comprobante !='07'
			$filtro_sql_trab $filtro_sql_ap $filtro_sql_mp $filtro_sql_tc $filtro_sql_es_c
			GROUP BY v.user_created, pu.nombre_razonsocial, pu.foto_perfil;";
			$user  = ejecutarConsultaArray($sql_1);
			$data[] = [
				'idproducto' => $val['idproducto'],
				'nombre_producto' => $val['nombre_producto'],
				'cantidad' => $val['cantidad'],
				'subtotal' => $val['subtotal'],
				'user' => $user['data'],
			];
		}

		return $retorno = ['status' => true, 'message' => 'todo ok pe.', 'data' => $data,];
	}

	// ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════
	public function select2_filtro_trabajador()
	{
		$filtro_id_trabajador  = '';
		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_id_trabajador = "WHERE pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
		}
		$sql = "SELECT LPAD(pt.idpersona_trabajador, 5, '0') as idtrabajador, pt.idpersona_trabajador, pt.idpersona,  per_t.nombre_razonsocial
		FROM  persona_cliente as pc 	
		INNER JOIN persona_trabajador as pt ON pt.idpersona_trabajador = pc.idpersona_trabajador
		INNER JOIN persona as per_t ON per_t.idpersona = pt.idpersona
		$filtro_id_trabajador
		GROUP BY pc.idpersona_trabajador
		ORDER BY per_t.nombre_razonsocial;";
		return ejecutarConsulta($sql);
	}


	public function select2_filtro_anio_pago()
	{
		$filtro_id_trabajador  = '';
		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
		}
		$sql = "SELECT DISTINCT v.name_year as anio_cancelacion
		FROM venta as v 
    INNER JOIN persona_cliente as pc on v.idpersona_cliente = pc.idpersona_cliente
		$filtro_id_trabajador
		ORDER BY v.name_year DESC;";
		return ejecutarConsulta($sql);
	}

	public function select2_filtro_mes_pago()
	{
		$filtro_id_trabajador  = '';
		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
		}
		$sql = "SELECT DISTINCT v.name_month as mes_cancelacion
		FROM venta as v 
    INNER JOIN persona_cliente as pc on v.idpersona_cliente = pc.idpersona_cliente
		$filtro_id_trabajador
		ORDER BY v.name_month DESC;";
		return ejecutarConsulta($sql);
	}

	public function select2_filtro_tipo_comprob()
	{
		$filtro_id_trabajador  = '';
		if ($_SESSION['user_cargo'] == 'TÉCNICO DE RED') {
			$filtro_id_trabajador =  "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
		}
		$sql = "SELECT DISTINCT v.tipo_comprobante, tc.abreviatura
		FROM venta as v 
    INNER JOIN sunat_c01_tipo_comprobante as tc on v.tipo_comprobante = tc.codigo
    INNER JOIN persona_cliente as pc on v.idpersona_cliente = pc.idpersona_cliente
		where tc.codigo != '07'
    $filtro_id_trabajador
		ORDER BY tc.abreviatura DESC;";
		return ejecutarConsulta($sql);
	}
}
