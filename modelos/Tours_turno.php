<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

Class Tours_turno
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	public function insertar($nombre ) {
		$sql_0 = "SELECT * FROM tours_turno  WHERE nombre = '$nombre';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
      
    if ( empty($existe['data']) ) {
			$sql="INSERT INTO tours_turno(nombre)VALUES('$nombre')";
			$insertar =  ejecutarConsulta_retornarID($sql, 'C'); if ($insertar['status'] == false) {  return $insertar; } 
			
			return $insertar;
		} else {
			$info_repetida = ''; 

			foreach ($existe['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['nombre'].'</span><br>
					<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
					<b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
					<hr class="m-t-2px m-b-2px">
				</li>'; 
			}
			return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
		}			
	}

	public function editar($idtours_turno, $nombre, ) {
		$sql_0 = "SELECT * FROM tours_turno  WHERE nombre = '$nombre' AND idtours_turno <> '$idtours_turno';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
      
    if ( empty($existe['data']) ) {
			$sql="UPDATE tours_turno SET nombre='$nombre' WHERE idtours_turno='$idtours_turno'";
			$editar =  ejecutarConsulta($sql, 'U');	if ( $editar['status'] == false) {return $editar; } 		
			return $editar;
		} else {
			$info_repetida = ''; 

			foreach ($existe['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['nombre'].'</span><br>
					<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
					<b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
					<hr class="m-t-2px m-b-2px">
				</li>'; 
			}
			return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
		}			
	}

	public function desactivar($idtours_turno) {
		$sql="UPDATE tours_turno SET estado='0' WHERE idtours_turno='$idtours_turno'";
		$desactivar= ejecutarConsulta($sql, 'T');
		return $desactivar;
	}

	public function eliminar($idtours_turno) {
		
		$sql="UPDATE tours_turno SET estado_delete='0' WHERE idtours_turno='$idtours_turno'";
		$eliminar =  ejecutarConsulta($sql, 'D');	if ( $eliminar['status'] == false) {return $eliminar; }  

		return $eliminar;
	}

	public function mostrar($idtours_turno) {
		$sql="SELECT * FROM tours_turno WHERE idtours_turno='$idtours_turno'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function tabla_principal() {
		$sql="SELECT * FROM tours_turno WHERE estado=1  AND estado_delete=1 ORDER BY nombre ASC";
		return ejecutarConsulta($sql);		
	}


}
?>