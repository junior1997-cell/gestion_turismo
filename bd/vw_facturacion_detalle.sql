CREATE VIEW `vw_facturacion_detalle` AS

SELECT 
-- Datos venta cabecera
v.idventa, LPAD(v.idventa, 5, '0') AS idventa_v2, v.idperiodo_contable,v.iddocumento_relacionado,v.crear_enviar_sunat,v.idsunat_c01,v.tipo_comprobante,v.serie_comprobante,
v.numero_comprobante,v.fecha_emision,v.name_day,v.name_month,v.name_year,v.impuesto,v.venta_subtotal,v.venta_descuento,v.venta_igv,
v.venta_total,v.metodo_pago,v.mp_serie_comprobante,v.mp_comprobante,v.mp_monto,v.venta_credito,v.vc_numero_operacion,v.vc_fecha_proximo_pago,
v.total_recibido,v.total_vuelto,v.usar_anticipo,v.ua_monto_disponible,v.ua_monto_usado,v.nc_motivo_nota,v.nc_tipo_comprobante,v.nc_serie_y_numero,
v.cot_tiempo_entrega,v.cot_validez,v.cot_estado,v.sunat_estado,v.sunat_observacion,v.sunat_code,v.sunat_mensaje,v.sunat_hash,v.sunat_error,
v.observacion_documento,v.estado as estado_v,v.estado_delete as estado_delete_v,v.created_at as created_at_v,v.updated_at as updated_at_v,
v.user_trash as user_trash_v,v.user_delete as user_delete_v,v.user_created as user_created_v,v.user_updated as user_updated_v,
 CASE v.tipo_comprobante WHEN '07' THEN v.venta_total * -1 ELSE v.venta_total END AS venta_total_v2, 
 DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') as fecha_emision_format,
 CASE v.tipo_comprobante WHEN '03' THEN 'BOLETA' WHEN '07' THEN 'NOTA CRED.' ELSE tc.abreviatura END AS tipo_comprobante_v2,
 DATE_FORMAT(v.fecha_emision, '%d, %b %Y - %h:%i %p') as fecha_emision_format_v2,
-- Datos venta detalle  
vd.idventa_detalle,vd.idproducto,vd.pr_nombre,vd.pr_marca,vd.pr_categoria,vd.pr_unidad_medida,vd.tipo,vd.cantidad,vd.precio_compra,vd.precio_venta,
vd.precio_venta_descuento,vd.descuento,vd.descuento_porcentaje,vd.subtotal,vd.subtotal_no_descuento,vd.um_nombre,vd.um_abreviatura,vd.es_cobro,vd.periodo_pago,
vd.periodo_pago_format,vd.periodo_pago_month,vd.periodo_pago_year, LEFT(vd.periodo_pago_month, 3) as periodo_pago_month_recorte, CONCAT( LEFT(vd.periodo_pago_month, 3), '-',  vd.periodo_pago_year) as periodo_pago_mes_anio,
-- Datos Cliente
pc.idpersona_cliente, p.idpersona, 
p.nombre_razonsocial, p.apellidos_nombrecomercial, p.tipo_documento, 
p.numero_documento, p.foto_perfil, 
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
END AS cliente_nombre_completo, 
-- Tipo de comprobante
tc.abreviatura as tipo_comprobante_v1, 
-- Tipo de documento cliente
sdi.abreviatura as tipo_documento_abreviatura, 
-- Datos del tecnico a cargo
pt.idpersona_trabajador, p2.nombre_razonsocial as nombre_tecnico, p2.apellidos_nombrecomercial as apellido_tecnico,
-- Usuario en atencion
CONCAT( fn_capitalize_texto(SUBSTRING_INDEX(pu.nombre_razonsocial, ' ', 1)),' ', fn_capitalize_texto(SUBSTRING_INDEX(pu.apellidos_nombrecomercial, ' ', 1))) AS user_en_atencion,
LPAD(v.user_created, 3, '0') AS user_created_v2
FROM venta AS v
INNER JOIN venta_detalle AS vd ON vd.idventa = v.idventa
INNER JOIN persona_cliente AS pc ON pc.idpersona_cliente = v.idpersona_cliente
INNER JOIN persona AS p ON p.idpersona = pc.idpersona
INNER JOIN persona_trabajador as pt on pt.idpersona_trabajador = pc.idpersona_trabajador
INNER JOIN persona as p2 on p2.idpersona = pt.idpersona
INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.idtipo_comprobante = v.idsunat_c01
LEFT JOIN usuario as u ON u.idusuario = v.user_created
LEFT JOIN persona as pu ON pu.idpersona = u.idpersona
WHERE v.estado = 1 AND v.estado_delete = 1 AND v.tipo_comprobante <> '100'
ORDER BY v.fecha_emision DESC, p.nombre_razonsocial ASC;