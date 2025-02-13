<?php
  //Incluímos inicialmente la conexión a la base de datos
  require "../config/Conexion_v2.php";

  class Tours
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

    function listar_tabla(){
      // $filtro_ubigeo_distrito = ""; $filtro_unidad_medida = ""; $filtro_marca = "";

      // if ( empty($ubigeo_distrito) ) { } else {  $filtro_ubigeo_distrito = "AND p.idubigeo_distrito = '$ubigeo_distrito'"; } 
      // if ( empty($unidad_medida) ) { } else {  $filtro_unidad_medida = "AND p.idsunat_unidad_medida = '$unidad_medida'"; } 
      // if ( empty($marca) ) { } else {  $filtro_marca = "AND p.idmarca = '$marca'"; } 

      $sql= "SELECT t.idtours, t.codigo, t.codigo_alterno, t.nombre, t.precio_publico, t.precio_corporativo, t.precio_tours, 
            t.precio_web, t.detalle_duracion, tt.nombre as turno, ud.nombre as cuidad, t.estado
            FROM tours as t
            INNER JOIN tours_turno as tt on t.idtours_turno = tt.idtours_turno
            INNER JOIN ubigeo_distrito as ud ON t.idubigeo_distrito = ud.idubigeo_distrito
            WHERE t.estado='1' and t.estado_delete='1';";
      return ejecutarConsulta($sql);
    }

    public function insertar($codigo_alterno,$ubigeo_distrito,$tours_turno,$nombre,$precio_publico,$precio_corporativo,
    $precio_web,$precio_tours,$detalle_duracion,$detalle_incluye,$detalle_programa,$img_tours)	{
      
      $sql_0 = "SELECT * FROM tours WHERE nombre = '$nombre';";
      $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}      
    
      if ( empty($existe['data']) ) {
        $sql = "INSERT INTO tours(idtours_turno, idubigeo_distrito, codigo_alterno, nombre, precio_publico, 
        precio_corporativo, precio_tours, precio_web, detalle_duracion, detalle_incluye, detalle_programa_turistico, brochure) 
        VALUES ('$tours_turno','$ubigeo_distrito','$codigo_alterno','$nombre','$precio_publico','$precio_corporativo',
        '$precio_tours','$precio_web','$detalle_duracion', '$detalle_incluye','$detalle_programa','$img_tours')";
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

    public function editar($idtours,$codigo_alterno,$ubigeo_distrito,$tours_turno,$nombre,$precio_publico,$precio_corporativo,
    $precio_web,$precio_tours,$detalle_duracion,$detalle_incluye,$detalle_programa,$img_tours) {

      $sql_0 = "SELECT * FROM tours WHERE nombre = '$nombre' AND idtours <> '$idtours';";
      $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
        
      if ( empty($existe['data']) ) {

        $sql = "UPDATE tours SET idtours_turno = '$tours_turno', idubigeo_distrito = '$ubigeo_distrito', codigo_alterno = '$codigo_alterno', 
        nombre = '$nombre', precio_publico = '$precio_publico', precio_corporativo = '$precio_corporativo', precio_tours = '$precio_tours',
        precio_web = '$precio_web', detalle_duracion = '$detalle_duracion', detalle_incluye = '$detalle_incluye', 
        detalle_programa_turistico = '$detalle_programa', brochure = '$img_tours'
        WHERE idtours = '$idtours';";
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
      $sql = "SELECT * FROM tours WHERE idtours = '$id';";
      return ejecutarConsultaSimpleFila($sql);
    }

    function mostrar_detalle_tours($id){
      $sql = "SELECT p.*, sum.nombre AS unidad_medida, cat.nombre AS ubigeo_distrito, mc.nombre AS marca
      FROM tours AS p
      INNER JOIN sunat_unidad_medida AS sum ON p.idsunat_unidad_medida = sum.idsunat_unidad_medida
      INNER JOIN ubigeo_distrito AS cat ON p.idubigeo_distrito = cat.idubigeo_distrito
      INNER JOIN marca AS mc ON p.idmarca = mc.idmarca
      WHERE p.idtours = '$id' ;";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function eliminar($id){
      $sql = "UPDATE tours SET estado_delete = 0
      WHERE idtours = '$id'";
      return ejecutarConsulta($sql, 'U');
    }

    public function papelera($id){
      $sql = "UPDATE tours SET estado = 0
      WHERE idtours = '$id'";
      return ejecutarConsulta($sql, 'U');
    }

    // ══════════════════════════════════════  VALIDACION DE CODIGO  ══════════════════════════════════════
    public function validar_code_tours($id, $code){
      $validar_id = empty($id) ? "" : "AND p.idtours != '$id'" ;
      $sql = "SELECT p.idtours, p.codigo_alterno, p.estado FROM tours AS p WHERE p.codigo_alterno = '$code' $validar_id;";
      $buscando =  ejecutarConsultaArray($sql); if ( $buscando['status'] == false) {return $buscando; }

      if (empty($buscando['data'])) { return true; }else { return false; }
    }
    // ══════════════════════════════════════  S E L E C T 2 - P A R A   F O R M  ══════════════════════════════════════

    public function select_ubigeo_distrito()	{
      $sql="SELECT ud.idubigeo_distrito, ud.nombre as distrito, up.nombre as provincia, udp.nombre as region
      FROM ubigeo_distrito as ud
      INNER JOIN ubigeo_provincia  as up on up.idubigeo_provincia = ud.idubigeo_provincia 
      INNER JOIN ubigeo_departamento as udp on udp.idubigeo_departamento = ud.idubigeo_departamento";
      return ejecutarConsultaArray($sql);   
    }

    public function select_marca()	{
      $sql="SELECT * FROM marca WHERE estado = 1 AND estado_delete = 1;";
      return ejecutarConsultaArray($sql);   
    }

    public function select_tours_turno()	{
      $sql="SELECT * FROM tours_turno WHERE estado = 1 AND estado_delete = 1;";
      return ejecutarConsultaArray($sql);   
    }

    // ══════════════════════════════════════  S E L E C T 2 - PARA FILTROS ══════════════════════════════════════ 
    public function select2_filtro_ubigeo_distrito()	{
      $sql="SELECT c.*
      FROM tours as p
      INNER JOIN ubigeo_distrito as c ON c.idubigeo_distrito = p.idubigeo_distrito
      WHERE c.idubigeo_distrito <> 2 AND p.estado = '1' AND p.estado_delete = '1'
      GROUP BY c.idubigeo_distrito ORDER BY c.idubigeo_distrito ASC ;";
      return ejecutarConsultaArray($sql);   
    }

    public function select2_filtro_tours_turno()	{
      $sql="SELECT um.*
      FROM tours as p
      INNER JOIN sunat_unidad_medida as um ON um.idsunat_unidad_medida = p.idsunat_unidad_medida
      WHERE p.estado = '1' AND p.estado_delete = '1'
      GROUP BY um.idsunat_unidad_medida ORDER BY um.idsunat_unidad_medida ASC;";
      return ejecutarConsultaArray($sql);   
    }

    public function select2_filtro_marca()	{
      $sql="SELECT m.*
      FROM tours as p
      INNER JOIN marca as m ON m.idmarca = p.idmarca
      WHERE p.estado = '1' AND p.estado_delete = '1'
      GROUP BY m.idmarca ORDER BY m.idmarca ASC;";
      return ejecutarConsultaArray($sql);   
    }
  }