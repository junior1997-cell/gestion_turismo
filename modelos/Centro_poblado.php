<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

Class CentroPoblado
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($nombre, $descripcion) {		
		$sql_0 = "SELECT * FROM centro_poblado  WHERE nombre = '$nombre';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
      
    if ( empty($existe['data']) ) {
			$sql="INSERT INTO centro_poblado(nombre, descripcion)VALUES('$nombre', '$descripcion')";
			$insertar =  ejecutarConsulta_retornarID($sql, 'C'); if ($insertar['status'] == false) {  return $insertar; } 
			
			//add registro en nuestra bitacora
			// $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('centro_poblado','".$insertar['data']."','Nueva centro_poblado registrado','" . $_SESSION['idusuario'] . "')";
			// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }   
			
			return $insertar;
		} else {
			$info_repetida = ''; 

			foreach ($existe['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['nombre'].'</span><br>
					<b>Descripción: </b>'.$value['descripcion'].'<br>
					<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
					<b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
					<hr class="m-t-2px m-b-2px">
				</li>'; 
			}
			return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
		}		
	}

	//Implementamos un método para editar registros
	public function editar($idcentro_poblado, $nombre, $descripcion) {
		$sql_0 = "SELECT * FROM centro_poblado  WHERE nombre = '$nombre' AND idcentro_poblado <> '$idcentro_poblado';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
      
    if ( empty($existe['data']) ) {
			$sql="UPDATE centro_poblado SET nombre='$nombre', descripcion ='$descripcion' WHERE idcentro_poblado='$idcentro_poblado'";
			$editar =  ejecutarConsulta($sql, 'U');	if ( $editar['status'] == false) {return $editar; } 
		
			//add registro en nuestra bitacora
			// $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) 
			// VALUES ('centro_poblado','$idcentro_poblado','centro_poblado editada','" . $_SESSION['idusuario'] . "')";
			// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }  
		
			return $editar;
		} else {
			$info_repetida = ''; 

			foreach ($existe['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['nombre'].'</span><br>
					<b>Descripción: </b>'.$value['descripcion'].'<br>
					<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
					<b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
					<hr class="m-t-2px m-b-2px">
				</li>'; 
			}
			return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
		}		
	}

	//Implementamos un método para desactivar color
	public function desactivar($idcentro_poblado) {
		$sql="UPDATE centro_poblado SET estado='0' WHERE idcentro_poblado='$idcentro_poblado'";
		$desactivar= ejecutarConsulta($sql, 'T'); if ($desactivar['status'] == false) {  return $desactivar; }
		
		// //add registro en nuestra bitacora
		// $sql_bit = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('centro_poblado','".$idcentro_poblado."','centro_poblado desactivada','" . $_SESSION['idusuario'] . "')";
		// $bitacora = ejecutarConsulta($sql_bit); if ( $bitacora['status'] == false) {return $bitacora; }   
		
		return $desactivar;
	}

	//Implementamos un método para activar centro_poblado
	public function activar($idcentro_poblado) {
		$sql="UPDATE centro_poblado SET estado='1' WHERE idcentro_poblado='$idcentro_poblado'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para eliminar centro_poblado
	public function eliminar($idcentro_poblado) {
		
		$sql="UPDATE centro_poblado SET estado_delete='0' WHERE idcentro_poblado='$idcentro_poblado'";
		$eliminar =  ejecutarConsulta($sql, 'D');	if ( $eliminar['status'] == false) {return $eliminar; }  
		
		//add registro en nuestra bitacora
		// $sql = "INSERT INTO bitacora_bd( nombre_tabla, id_tabla, accion, id_user) VALUES ('centro_poblado', '$idcentro_poblado', 'centro_poblado Eliminada','" . $_SESSION['idusuario'] . "')";
		// $bitacora = ejecutarConsulta($sql); if ( $bitacora['status'] == false) {return $bitacora; }  
		
		return $eliminar;
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idcentro_poblado) {
		$sql="SELECT * FROM centro_poblado WHERE idcentro_poblado='$idcentro_poblado'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function tabla_principal_centro_poblado() {
		$sql="SELECT * FROM centro_poblado WHERE estado=1  AND estado_delete=1 ORDER BY nombre ASC";
		return ejecutarConsulta($sql);		
	}

	//Implementar un método para listar los registros y mostrar en el select
	public function select() {
		$sql="SELECT * FROM centro_poblado where estado=1";
		return ejecutarConsulta($sql);		
	}
}
?>