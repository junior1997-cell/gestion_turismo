<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  class Producto
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

    function listar_tabla($categoria, $unidad_medida, $marca){
      $filtro_categoria = ""; $filtro_unidad_medida = ""; $filtro_marca = "";

      if ( empty($categoria) ) { } else {  $filtro_categoria = "AND p.idcategoria = '$categoria'"; } 
      if ( empty($unidad_medida) ) { } else {  $filtro_unidad_medida = "AND p.idsunat_unidad_medida = '$unidad_medida'"; } 
      if ( empty($marca) ) { } else {  $filtro_marca = "AND p.idmarca = '$marca'"; } 

      $sql= "SELECT p.*, sum.nombre AS unidad_medida, cat.nombre AS categoria, mc.nombre AS marca
      FROM producto AS p
      INNER JOIN sunat_unidad_medida AS sum ON p.idsunat_unidad_medida = sum.idsunat_unidad_medida
      INNER JOIN categoria AS cat ON p.idcategoria = cat.idcategoria
      INNER JOIN marca AS mc ON p.idmarca = mc.idmarca
      WHERE p.idcategoria <> 2 AND p.estado = 1 AND p.estado_delete = 1 $filtro_categoria $filtro_unidad_medida $filtro_marca
      ORDER BY p.nombre ASC;";
      return ejecutarConsulta($sql);
    }

    public function insertar($tipo, $codigo_alterno, $categoria, $u_medida, $marca, $nombre, $descripcion, $stock, 
      $stock_min, $precio_v, $precio_c, $precio_x_mayor, $precio_dist, $precio_esp, $img_producto)	{
      
      $sql_0 = "SELECT * FROM producto WHERE nombre = '$nombre';";
      $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}      
    
      if ( empty($existe['data']) ) {
        $sql = "INSERT INTO producto (idsunat_unidad_medida, idcategoria, idmarca, tipo, codigo_alterno, nombre, stock, stock_minimo, 
        precio_compra, precio_venta, precioB, precioC, precioD, descripcion, imagen) VALUES 
        ('$u_medida', '$categoria', '$marca', '$tipo', '$codigo_alterno', '$nombre', '$stock', '$stock_min', '$precio_c', '$precio_v', '$precio_x_mayor', 
        '$precio_dist', '$precio_esp', '$descripcion', '$img_producto');";
        $id_new = ejecutarConsulta($sql, 'C');	if ($id_new['status'] == false) {  return $id_new; }

        return $id_new;
      } else {
        $info_repetida = ''; 
  
        foreach ($existe['data'] as $key => $value) {
          $info_repetida .= '<li class="text-left font-size-13px">
            <span class="font-size-15px text-danger"><b>'.$value['nombre'].'</span><br>
            <b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
            <b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
            <hr class="m-t-2px m-b-2px">
          </li>'; 
        }
        return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
      }    
	  }

    public function editar($idproducto, $tipo, $codigo_alterno, $categoria, $u_medida, $marca, $nombre, $descripcion, $stock, 
    $stock_min, $precio_v, $precio_c, $precio_x_mayor, $precio_dist, $precio_esp, $img_producto) {

      $sql_0 = "SELECT * FROM producto WHERE nombre = '$nombre' AND idproducto <> '$idproducto';";
      $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
        
      if ( empty($existe['data']) ) {

        $sql = "UPDATE producto SET idsunat_unidad_medida = '$u_medida', idcategoria = '$categoria', idmarca = '$marca', tipo = '$tipo', 
        codigo_alterno = '$codigo_alterno', nombre = '$nombre', stock = '$stock', stock_minimo = '$stock_min', precio_compra = '$precio_c', precio_venta = '$precio_v', 
        precioB = '$precio_x_mayor', precioC = '$precio_dist', precioD = '$precio_esp', descripcion = '$descripcion', imagen = '$img_producto'
        WHERE idproducto = '$idproducto'";
        $edit_user = ejecutarConsulta($sql, 'U'); if ($edit_user['status'] == false) {  return $edit_user; }

        return $edit_user;

      } else {
        $info_repetida = ''; 

        foreach ($existe['data'] as $key => $value) {
          $info_repetida .= '<li class="text-left font-size-13px">
            <span class="font-size-15px text-danger"><b>'.$value['nombre'].'</span><br>
            <b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
            <b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
            <hr class="m-t-2px m-b-2px">
          </li>'; 
        }
        return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
      }	
    }

    function mostrar($id){
      $sql = "SELECT * FROM producto WHERE idproducto = '$id';";
      return ejecutarConsultaSimpleFila($sql);
    }

    function mostrar_detalle_producto($id){
      $sql = "SELECT p.*, sum.nombre AS unidad_medida, cat.nombre AS categoria, mc.nombre AS marca
      FROM producto AS p
      INNER JOIN sunat_unidad_medida AS sum ON p.idsunat_unidad_medida = sum.idsunat_unidad_medida
      INNER JOIN categoria AS cat ON p.idcategoria = cat.idcategoria
      INNER JOIN marca AS mc ON p.idmarca = mc.idmarca
      WHERE p.idproducto = '$id' ;";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function eliminar($id){
      $sql = "UPDATE producto SET estado_delete = 0
      WHERE idproducto = '$id'";
      return ejecutarConsulta($sql, 'U');
    }

    public function papelera($id){
      $sql = "UPDATE producto SET estado = 0
      WHERE idproducto = '$id'";
      return ejecutarConsulta($sql, 'U');
    }

    // ══════════════════════════════════════  VALIDACION DE CODIGO  ══════════════════════════════════════
    public function validar_code_producto($id, $code){
      $validar_id = empty($id) ? "" : "AND p.idproducto != '$id'" ;
      $sql = "SELECT p.idproducto, p.codigo_alterno, p.estado FROM producto AS p WHERE p.codigo_alterno = '$code' $validar_id;";
      $buscando =  ejecutarConsultaArray($sql); if ( $buscando['status'] == false) {return $buscando; }

      if (empty($buscando['data'])) { return true; }else { return false; }
    }
    // ══════════════════════════════════════  S E L E C T 2 - P A R A   F O R M  ══════════════════════════════════════

    public function select_categoria()	{
      $sql="SELECT * FROM categoria WHERE idcategoria <> 2 AND estado = 1 AND estado_delete = 1;";
      return ejecutarConsultaArray($sql);   
    }

    public function select_marca()	{
      $sql="SELECT * FROM marca WHERE estado = 1 AND estado_delete = 1;";
      return ejecutarConsultaArray($sql);   
    }

    public function select_u_medida()	{
      $sql="SELECT * FROM sunat_unidad_medida WHERE estado = 1 AND estado_delete = 1;";
      return ejecutarConsultaArray($sql);   
    }

    // ══════════════════════════════════════  S E L E C T 2 - PARA FILTROS ══════════════════════════════════════ 
    public function select2_filtro_categoria()	{
      $sql="SELECT c.*
      FROM producto as p
      INNER JOIN categoria as c ON c.idcategoria = p.idcategoria
      WHERE c.idcategoria <> 2 AND p.estado = '1' AND p.estado_delete = '1'
      GROUP BY c.idcategoria ORDER BY c.idcategoria ASC ;";
      return ejecutarConsultaArray($sql);   
    }

    public function select2_filtro_u_medida()	{
      $sql="SELECT um.*
      FROM producto as p
      INNER JOIN sunat_unidad_medida as um ON um.idsunat_unidad_medida = p.idsunat_unidad_medida
      WHERE p.estado = '1' AND p.estado_delete = '1'
      GROUP BY um.idsunat_unidad_medida ORDER BY um.idsunat_unidad_medida ASC;";
      return ejecutarConsultaArray($sql);   
    }

    public function select2_filtro_marca()	{
      $sql="SELECT m.*
      FROM producto as p
      INNER JOIN marca as m ON m.idmarca = p.idmarca
      WHERE p.estado = '1' AND p.estado_delete = '1'
      GROUP BY m.idmarca ORDER BY m.idmarca ASC;";
      return ejecutarConsultaArray($sql);   
    }
  }