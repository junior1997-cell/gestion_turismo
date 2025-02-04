<?php

  require "../config/Conexion_v2.php";

  class Compras
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

    public function listar_tabla_compra() {

      $sql = "SELECT c.*, p.*, tc.abreviatura as tp_comprobante, sdi.abreviatura as tipo_documento, c.estado
      FROM compra AS c
      INNER JOIN persona AS p ON c.idproveedor = p.idpersona
      INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
      INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.codigo = c.tipo_comprobante
      WHERE c.estado = 1 AND c.estado_delete = 1";
      $compra = ejecutarConsulta($sql); if ($compra['status'] == false) {return $compra; }

      return $compra;
    }

    public function insertar(
      // DATOS TABLA COMPRA
      $idproveedor,  $tipo_comprobante, $serie, $impuesto, $descripcion,
      $subtotal_compra, $tipo_gravada, $igv_compra, $total_compra, $fecha_compra, $img_comprob,
      //DATOS TABLA COMPRA DETALLE
      $idproducto, $unidad_medida, $cantidad, $precio_sin_igv, $precio_igv, $precio_con_igv, 
      $descuento, $subtotal_producto    
    ){
      $sql_1 = "INSERT INTO compra(idproveedor, fecha_compra, tipo_comprobante, serie_comprobante, val_igv, descripcion, subtotal, igv, total, comprobante) 
      VALUES ('$idproveedor', '$fecha_compra', '$tipo_comprobante', '$serie', '$impuesto', '$descripcion', '$subtotal_compra', '$igv_compra', '$total_compra', '$img_comprob')";
      $newdata = ejecutarConsulta_retornarID($sql_1, 'C'); if ($newdata['status'] == false) { return  $newdata;}
      $id = $newdata['data'];

      $i = 0;
      $detalle_new = "";

      if ( !empty($newdata['data']) ) {      
        while ($i < count($idproducto)) {

          $sql_2 = "INSERT INTO compra_detalle(idproducto, idcompra, cantidad, precio_sin_igv, igv, precio_con_igv, descuento, subtotal)
          VALUES ('$idproducto[$i]', '$id', '$cantidad[$i]', '$precio_sin_igv[$i]', '$precio_igv[$i]', '$precio_con_igv[$i]', '$descuento[$i]', '$subtotal_producto[$i]');";
          $detalle_new =  ejecutarConsulta_retornarID($sql_2, 'C'); if ($detalle_new['status'] == false) { return  $detalle_new;}          
          $id_d = $detalle_new['data'];
          $i = $i + 1;
        }
      }
      return $detalle_new;
    }

    public function editar( $idcompra, $idproveedor,  $tipo_comprobante, $serie, $impuesto, $descripcion, $subtotal_compra, $tipo_gravada, $igv_compra, $total_compra, $fecha_compra, $img_comprob,        
    $idproducto, $unidad_medida, $cantidad, $precio_sin_igv, $precio_igv, $precio_con_igv, $descuento, $subtotal_producto) {

      $sql_1 = "UPDATE compra SET idproveedor = '$idproveedor', fecha_compra = '$fecha_compra', tipo_comprobante = '$tipo_comprobante', serie_comprobante = '$serie', 
      val_igv = '$impuesto', descripcion = '$descripcion', subtotal = '$subtotal_compra', igv = '$igv_compra', total = '$total_compra', comprobante = '$img_comprob'
      WHERE idcompra = '$idcompra'";
      $result_sql_1 = ejecutarConsulta($sql_1, 'U');if ($result_sql_1['status'] == false) { return $result_sql_1; }

      // Eliminamos los productos
      $sql_del = "DELETE FROM compra_detalle WHERE idcompra = '$idcompra'";
      ejecutarConsulta($sql_del);

      // Creamos los productos
      foreach ($idproducto as $ii => $producto) {
        $sql_2 = "INSERT INTO compra_detalle(idproducto, idcompra, cantidad, precio_sin_igv, igv, precio_con_igv, descuento, subtotal)
        VALUES ('$idproducto[$ii]', '$idcompra', '$cantidad[$ii]', '$precio_sin_igv[$ii]', '$precio_igv[$ii]', '$precio_con_igv[$ii]', '$descuento[$ii]', '$subtotal_producto[$ii]');";
        $detalle_new =  ejecutarConsulta_retornarID($sql_2, 'C'); if ($detalle_new['status'] == false) { return  $detalle_new;}        
      }  
      
      return array('status' => true, 'message' => 'Datos actualizados correctamente.');
    }
  

    public function mostrar_detalle_compra($idcompra){

      $sql_1 = "SELECT c.*, p.*, tc.abreviatura as tp_comprobante, sdi.abreviatura as tipo_documento, c.estado
      FROM compra AS c
      INNER JOIN persona AS p ON c.idproveedor = p.idpersona
      INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
      INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.codigo = c.tipo_comprobante
      WHERE c.idcompra = '$idcompra'
      AND c.estado = 1 AND c.estado_delete = 1";
      $compra = ejecutarConsultaSimpleFila($sql_1); if ($compra['status'] == false) {return $compra; }


      $sql_2 = "SELECT cd.*, pd.*
      FROM compra_detalle AS cd
      INNER JOIN producto AS pd ON cd.idproducto = pd.idproducto
      WHERE  cd.idcompra = '$idcompra'
      AND cd.estado = 1 AND cd.estado_delete = 1";
      $detalle = ejecutarConsultaArray($sql_2); if ($detalle['status'] == false) {return $detalle; }

      return $datos = ['status' => true, 'message' => 'Todo ok', 'data' => ['compra' => $compra['data'], 'detalle' => $detalle['data']]];

    }


    public function mostrar_compra($id){
      $sql = "SELECT * FROM compra WHERE idcompra = '$id'";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function mostrar_editar_detalles_compra($id){
      $sql = "SELECT * FROM compra WHERE idcompra = '$id'";
      $compra = ejecutarConsultaSimpleFila($sql);

      $sql = "SELECT dc.*, p.nombre, p.codigo, p.codigo_alterno, p.imagen, sum.nombre AS unidad_medida, cat.nombre AS categoria, mc.nombre AS marca
      FROM compra_detalle AS dc
        INNER JOIN producto AS p ON p.idproducto = dc.idproducto
        INNER JOIN sunat_unidad_medida AS sum ON p.idsunat_unidad_medida = sum.idsunat_unidad_medida
        INNER JOIN categoria AS cat ON p.idcategoria = cat.idcategoria
        INNER JOIN marca AS mc ON p.idmarca = mc.idmarca
      WHERE dc.idcompra = '$id'
        AND p.estado = 1
        AND p.estado_delete = 1;";
      $compra_detalle = ejecutarConsultaArray($sql);
      return ['status' => true, 'message' =>'todo okey', 'data'=>['compra' => $compra['data'], 'compra_detalle' => $compra_detalle['data'],]];
    }

    public function eliminar($id){
      $sql = "UPDATE compra SET estado_delete = 0
      WHERE idcompra = '$id'";
      return ejecutarConsulta($sql, 'U');
    }

    public function desactivar($id){
      $sql = "UPDATE compra SET estado = 0
      WHERE idcompra = '$id'";
      return ejecutarConsulta($sql, 'U');
    }



    public function listar_tabla_producto(){
      $sql = "SELECT p.*, sum.nombre AS unidad_medida, cat.nombre AS categoria, mc.nombre AS marca
      FROM producto AS p
      INNER JOIN sunat_unidad_medida AS sum ON p.idsunat_unidad_medida = sum.idsunat_unidad_medida
      INNER JOIN categoria AS cat ON p.idcategoria = cat.idcategoria
      INNER JOIN marca AS mc ON p.idmarca = mc.idmarca
      WHERE p.idcategoria <> 2  AND p.estado = 1 AND p.estado_delete = 1;";
      return ejecutarConsulta($sql);
    }

    public function mostrar_producto($idproducto){
      $sql = "SELECT p.*, sum.nombre AS unidad_medida, cat.nombre AS categoria, mc.nombre AS marca
      FROM producto AS p
      INNER JOIN sunat_unidad_medida AS sum ON p.idsunat_unidad_medida = sum.idsunat_unidad_medida
      INNER JOIN categoria AS cat ON p.idcategoria = cat.idcategoria
      INNER JOIN marca AS mc ON p.idmarca = mc.idmarca
      WHERE p.idproducto = '$idproducto'  AND p.estado = 1 AND p.estado_delete = 1;";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function listar_producto_x_codigo($codigo){
      $sql = "SELECT p.*, sum.nombre AS unidad_medida, cat.nombre AS categoria, mc.nombre AS marca
      FROM producto AS p
      INNER JOIN sunat_unidad_medida AS sum ON p.idsunat_unidad_medida = sum.idsunat_unidad_medida
      INNER JOIN categoria AS cat ON p.idcategoria = cat.idcategoria
      INNER JOIN marca AS mc ON p.idmarca = mc.idmarca
      WHERE (p.codigo = '$codigo' OR p.codigo_alterno = '$codigo' ) AND p.estado = 1 AND p.estado_delete = 1;";
        return ejecutarConsultaSimpleFila($sql);
      
    }

  }

  




?>