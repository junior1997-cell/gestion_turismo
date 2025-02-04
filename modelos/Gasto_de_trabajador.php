<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  class Gasto_de_trabajador
  {
    //Implementamos nuestro constructor
    public $id_usr_sesion; 
    // public $id_empresa_sesion;
    //Implementamos nuestro constructor
    public function __construct( $id_usr_sesion = 0, $id_empresa_sesion = 0 )
    {
      $this->id_usr_sesion =  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
      // $this->id_empresa_sesion = isset($_SESSION['idempresa']) ? $_SESSION["idempresa"] : 0;
    }

    function insertar($idtrabajador, $descr_gastos, $tp_comprobante, $serie_comprobante, $fecha, $idproveedor, $sub_total, $igv, $val_igv, $total_gasto, $descr_comprobante, $img_comprob){
      $sql = "INSERT INTO gasto_de_trabajador (idpersona_trabajador, descripcion_gasto, tipo_comprobante, serie_comprobante, fecha_ingreso, idproveedor, precio_sin_igv, precio_igv, val_igv, precio_con_igv, descripcion_comprobante, comprobante)
      VALUES ('$idtrabajador', '$descr_gastos', '$tp_comprobante', '$serie_comprobante', '$fecha', '$idproveedor', '$sub_total', '$igv', '$val_igv', '$total_gasto', '$descr_comprobante', '$img_comprob')";
      return ejecutarConsulta_retornarID($sql, 'C');
    }

    function editar($id, $idtrabajador, $descr_gastos, $tp_comprobante, $serie_comprobante, $fecha, $idproveedor, $sub_total, $igv, $val_igv, $total_gasto, $descr_comprobante, $img_comprob){
      $sql = "UPDATE gasto_de_trabajador  SET idpersona_trabajador = '$idtrabajador', descripcion_gasto = '$descr_gastos', tipo_comprobante = '$tp_comprobante', serie_comprobante = '$serie_comprobante', fecha_ingreso = '$fecha', 
      idproveedor = '$idproveedor', precio_sin_igv = '$sub_total', precio_igv = '$igv', val_igv = '$val_igv', precio_con_igv = '$total_gasto', descripcion_comprobante = '$descr_comprobante', comprobante = '$img_comprob'
      WHERE idgasto_de_trabajador = '$id' ";
      return ejecutarConsulta($sql, 'U');
    }

    function mostrar_detalle_gasto($id){

      $sql_2 = "SELECT gdt.idgasto_de_trabajador, gdt.idproveedor, gdt.tipo_comprobante, gdt.serie_comprobante, gdt.fecha_ingreso,  DATE_FORMAT(gdt.fecha_ingreso, '%d/%m/%Y') as fecha_ingreso_f, 
      gdt.name_day, gdt.name_month, gdt.name_year, gdt.precio_sin_igv, gdt.precio_igv, gdt.val_igv, gdt.precio_con_igv, gdt.descripcion_comprobante, gdt.descripcion_gasto, gdt.comprobante,  gdt.estado,
      CASE p.tipo_persona_sunat 
        WHEN 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial )
        WHEN 'JURIDICA' THEN p.nombre_razonsocial
      END AS proveedor, p.foto_perfil as foto_perfil_proveedor, p.numero_documento as numero_documento_p, sdi_p.abreviatura as tipo_documento_nombre_p,
      CASE t.tipo_persona_sunat 
        WHEN 'NATURAL' THEN CONCAT(t.nombre_razonsocial, ' ', t.apellidos_nombrecomercial )
        WHEN 'JURIDICA' THEN t.nombre_razonsocial
      END AS trabajador, t.foto_perfil as foto_perfil_trabajador, t.numero_documento as numero_documento_t, sdi.abreviatura as tipo_documento_nombre_t
      FROM gasto_de_trabajador as gdt
      INNER JOIN persona as p ON p.idpersona = gdt.idproveedor 
      INNER JOIN sunat_c06_doc_identidad as sdi_p ON sdi_p.code_sunat = p.tipo_documento
      INNER JOIN persona_trabajador as pt ON pt.idpersona_trabajador = gdt.idpersona_trabajador
      INNER JOIN persona as t ON t.idpersona = pt.idpersona
      INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = t.tipo_documento
      WHERE gdt.estado = '1' AND gdt.estado_delete = '1' AND gdt.idgasto_de_trabajador = '$id' ;";
      return ejecutarConsultaSimpleFila($sql_2); 

    }

    function listar_tabla(){
      $sql = "SELECT gdt.idgasto_de_trabajador, gdt.idproveedor, gdt.tipo_comprobante, gdt.serie_comprobante, gdt.fecha_ingreso, gdt.name_day, gdt.name_month, 
      gdt.name_year, gdt.precio_sin_igv, gdt.precio_igv, gdt.val_igv, gdt.precio_con_igv, gdt.descripcion_comprobante, gdt.descripcion_gasto, gdt.comprobante,  gdt.estado,
      CASE p.tipo_persona_sunat 
        WHEN 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial )
        WHEN 'JURIDICA' THEN p.nombre_razonsocial
      END AS proveedor, p.foto_perfil as foto_perfil_proveedor, 
      CASE t.tipo_persona_sunat 
        WHEN 'NATURAL' THEN CONCAT(t.nombre_razonsocial, ' ', t.apellidos_nombrecomercial )
        WHEN 'JURIDICA' THEN t.nombre_razonsocial
      END AS trabajador, t.foto_perfil as foto_perfil_trabajador, t.tipo_documento, t.numero_documento, sdi.abreviatura as tipo_documento_nombre
      FROM gasto_de_trabajador as gdt
      INNER JOIN persona as p ON p.idpersona = gdt.idproveedor 
      INNER JOIN persona_trabajador as pt ON pt.idpersona_trabajador = gdt.idpersona_trabajador
      INNER JOIN persona as t ON t.idpersona = pt.idpersona
      INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = t.tipo_documento
      WHERE gdt.estado = '1' AND gdt.estado_delete = '1';";
      return ejecutarConsulta($sql);
    }

    public function desactivar($id){
      $sql="UPDATE gasto_de_trabajador SET estado='0',user_trash= '$this->id_usr_sesion' WHERE idgasto_de_trabajador='$id'";
      $desactivar =  ejecutarConsulta($sql, 'U'); if ( $desactivar['status'] == false) {return $desactivar; }  
      
      //add registro en nuestra bitacora
      // $sql_d = $id;
      // $sql = "INSERT INTO bitacora_bd(idcodigo,nombre_tabla, id_tabla, sql_d, id_user) VALUES (2,'gasto_de_trabajador','.$id.','$sql_d','$this->id_usr_sesion')";
      // $bitacora = ejecutarConsulta($sql); if ( $bitacora['status'] == false) {return $bitacora; }  

      return $desactivar;
    }

    public function eliminar($id) {
      $sql="UPDATE gasto_de_trabajador SET estado_delete='0',user_delete= '$this->id_usr_sesion' WHERE idgasto_de_trabajador='$id'";
      $eliminar =  ejecutarConsulta($sql); if ( $eliminar['status'] == false) {return $eliminar; }  

      //add registro en nuestra bitacora
      // $sql_d = $id;
      // $sql_bit = "INSERT INTO bitacora_bd(idcodigo, nombre_tabla, id_tabla, sql_d, id_user) VALUES (4,'gasto_de_trabajador','$id','$sql_d','$this->id_usr_sesion')";
		  // $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }  
		
		  return $eliminar;
    }
    
    function listar_trabajador(){
      $sql = "SELECT p.*, pt.idpersona_trabajador, sdi.nombre as nombre_tipo_documento, pt.sueldo_mensual, c.nombre as cargo
      FROM persona_trabajador as pt      
      INNER JOIN persona AS p ON pt.idpersona = p.idpersona
      INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
      INNER JOIN cargo_trabajador as c ON c.idcargo_trabajador = p.idcargo_trabajador
      WHERE p.idtipo_persona = 2 AND p.estado = 1 AND p.estado_delete = 1;";
      return ejecutarConsultaArray($sql);
    }

    function listar_proveedor(){
      $sql = "SELECT p.idpersona, p.nombre_razonsocial AS nombre, p.apellidos_nombrecomercial AS apellido, p.numero_documento, tp.nombre as tipo_persona
      FROM persona as p
      INNER JOIN tipo_persona as tp on tp.idtipo_persona = p.idtipo_persona 
      WHERE p.idtipo_persona in ( 3 ,4 ) AND p.estado_delete = 1 AND p.idpersona > 2 
      ORDER BY tp.nombre DESC, p.nombre_razonsocial ASC";
      return ejecutarConsultaArray($sql);
    }

    function mostrar_editar_gdt($id){
      $sql = "SELECT * FROM gasto_de_trabajador WHERE idgasto_de_trabajador = '$id'";
      return ejecutarConsultaSimpleFila($sql);
    }
  }
?>