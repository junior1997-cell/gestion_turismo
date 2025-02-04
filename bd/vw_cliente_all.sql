CREATE VIEW `vw_cliente_all` AS

SELECT
	-- ::::::::::::::: DATOS CLIENTE ::::::::::::::: 
	per.idpersona_cliente_v2,	per.idpersona_cliente, per.ip_personal,	per.dia_cancelacion, per.dia_cancelacion_v2, per.fecha_cancelacion, per.fecha_cancelacion_format, 
	per.fecha_afiliacion,	per.descuento, per.estado_descuento, per.centro_poblado, per.nota,	per.usuario_microtick,	per.estado_pc,	per.estado_delete_pc,
	per.cliente_nombre_completo,
	IF(per.fecha_cancelacion > CURDATE(),
			DATEDIFF(per.fecha_cancelacion, CURDATE()),
			DATEDIFF(
				IF(DAY(per.fecha_cancelacion) >= DAY(CURDATE()),
					DATE_ADD(LAST_DAY(CURDATE() - INTERVAL 1 MONTH), INTERVAL DAY(per.fecha_cancelacion) DAY),
					DATE_ADD(LAST_DAY(CURDATE()), INTERVAL DAY(per.fecha_cancelacion) DAY)
				),
				CURDATE()
			) 
		) AS dias_para_proximo_pago,
		IF(per.fecha_cancelacion > CURDATE(),
			DATE_FORMAT(per.fecha_cancelacion, '%d/%m/%Y'),
			DATE_FORMAT(
				IF(DAY(per.fecha_cancelacion) >= DAY(CURDATE()),
					DATE_ADD(LAST_DAY(CURDATE() - INTERVAL 1 MONTH), INTERVAL DAY(per.fecha_cancelacion) DAY),
					DATE_ADD(LAST_DAY(CURDATE()), INTERVAL DAY(per.fecha_cancelacion) DAY)				
				),
			'%d/%m/%Y'
			)
		)	AS proximo_pago,
	-- ::::::::::::::: DATOS PERSONA CLIENTE ::::::::::::::: 
	per.idpersona, per.idtipo_persona, per.idbancos, per.idcargo_trabajador, per.tipo_persona_sunat, per.nombre_razonsocial, per.apellidos_nombrecomercial, per.tipo_documento, 
	per.numero_documento, per.fecha_nacimiento, per.celular, per.direccion, per.departamento, per.provincia, per.distrito, per.cod_ubigeo, per.correo, per.cuenta_bancaria, 
	per.cci, per.titular_cuenta, per.foto_perfil, per.estado_p, per.estado_delete_p,
	ven.periodo_pago_format_min, 
	case when per.foto_perfil is null then LEFT(per.nombre_razonsocial, 1) when per.foto_perfil = '' then LEFT(per.nombre_razonsocial, 1) else null end cliente_primera_letra,
	case when per.foto_perfil is null then 'NO' when per.foto_perfil = '' then 'NO' else 'SI' end cliente_tiene_pefil,
	per.landing_user, per.landing_descripcion, per.landing_puntuacion, per.landing_fecha, per.landing_estado,
	-- ::::::::::::::: DATOS TECNICO (TRABAJADOR A CARGO) ::::::::::::::: 
 	per.idpersona_trabajador, per.trabajador_foto_perfil, per.trabajador_celular,	per.trabajador_nombre, per.trabajador_1_nombre, per.trabajador_1_apellido, per.trabajador_numero_documento, per.trabajador_tipo_documento,
	-- ::::::::::::::: DATOS PLAN ::::::::::::::: 
	per.idplan,	per.nombre_plan,	per.costo,
	-- ::::::::::::::: DATOS ZONA ANTENA ::::::::::::::: 
	per.idzona_antena,	per.zona,	per.ip_antena,
	-- ::::::::::::::: DATOS SUNAT ::::::::::::::: 
	per.tipo_documento_abrev_nombre
FROM 
	(
		SELECT 
			LPAD (pc.idpersona_cliente, 5, '0') as idpersona_cliente_v2,
			pc.idpersona_cliente,
			pc.ip_personal,
			DAY (pc.fecha_cancelacion) AS dia_cancelacion,
			CASE WHEN pc.fecha_cancelacion > CURDATE() THEN DATE_FORMAT(pc.fecha_cancelacion, '%d/%m/%Y') ELSE CONCAT( DATE_FORMAT(pc.fecha_cancelacion, '%d'), ' de cada mes' ) END AS dia_cancelacion_v2,
			pc.fecha_cancelacion, DATE_FORMAT(pc.fecha_cancelacion, '%d/%m/%Y') AS fecha_cancelacion_format, 
			pc.fecha_afiliacion,
			pc.descuento,
			pc.estado_descuento,
			cp.nombre as centro_poblado,
			pc.nota,
			pc.usuario_microtick,
			pc.landing_user, pc.landing_descripcion, pc.landing_puntuacion, pc.landing_fecha, pc.landing_estado, 
			pc.estado as estado_pc,	pc.estado_delete as estado_delete_pc,
			CASE
				WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT (p.nombre_razonsocial,' ',p.apellidos_nombrecomercial	)
				WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial
				ELSE '-'
			END AS cliente_nombre_completo,
			p.idpersona, p.idtipo_persona, p.idbancos, p.idcargo_trabajador, p.tipo_persona_sunat, p.nombre_razonsocial, p.apellidos_nombrecomercial, 
			p.tipo_documento, p.numero_documento, p.fecha_nacimiento, p.celular, p.direccion, p.departamento, p.provincia, p.distrito, p.cod_ubigeo, p.correo, 
			p.cuenta_bancaria, p.cci, p.titular_cuenta, p.foto_perfil, p.estado as estado_p, p.estado_delete as estado_delete_p,
			pt.idpersona_trabajador, p1.foto_perfil as trabajador_foto_perfil, p1.celular as trabajador_celular,	p1.nombre_razonsocial AS trabajador_nombre, p1.apellidos_nombrecomercial as trabajador_apellido,
			SUBSTRING_INDEX(p1.nombre_razonsocial, ' ', 1) AS trabajador_1_nombre ,SUBSTRING_INDEX(p1.apellidos_nombrecomercial, ' ', 1) as trabajador_1_apellido,
			p1.numero_documento as trabajador_numero_documento , CASE p1.tipo_documento WHEN '0' THEN 'NINGUNO' WHEN '1' THEN 'DNI' WHEN '4' THEN 'CE' WHEN '6' THEN 'RUC' ELSE '' END AS  trabajador_tipo_documento ,
			pl.idplan,	pl.nombre as nombre_plan,	pl.costo,
			za.idzona_antena,za.nombre as zona,za.ip_antena,
			sc06.abreviatura as tipo_documento_abrev_nombre
		FROM persona_cliente as pc
		INNER JOIN persona AS p on pc.idpersona = p.idpersona
		INNER JOIN persona_trabajador AS pt on pc.idpersona_trabajador = pt.idpersona_trabajador
		INNER JOIN persona as p1 on pt.idpersona = p1.idpersona
		INNER JOIN plan as pl on pc.idplan = pl.idplan
		INNER JOIN zona_antena as za on pc.idzona_antena = za.idzona_antena
		INNER JOIN sunat_c06_doc_identidad as sc06 on p.tipo_documento = sc06.code_sunat
		INNER JOIN centro_poblado as cp on pc.idcentro_poblado = cp.idcentro_poblado 
	) AS per
	LEFT JOIN ( 
		SELECT MIN(vd.periodo_pago_format) as periodo_pago_format_min, v.idpersona_cliente FROM venta v 
		INNER JOIN venta_detalle AS vd ON vd.idventa = v.idventa
		WHERE  vd.es_cobro = 'SI' AND v.estado = 1 AND v.estado_delete = 1 AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante IN ('01', '03', '12')
		GROUP BY v.idpersona_cliente
  ) AS ven ON ven.idpersona_cliente = per.idpersona_cliente 
ORDER BY	per.idpersona_cliente DESC