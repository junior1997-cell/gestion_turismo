<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

Class Categoria_otros_gastos
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($nombre_otros_gastos, $descripcion_otros_gastos) {
		$sql_0 = "SELECT * FROM otros_gastos_categoria  WHERE nombre = '$nombre_otros_gastos';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
      
    if ( empty($existe['data']) ) {
			$sql="INSERT INTO otros_gastos_categoria(nombre, descripcion)VALUES('$nombre_otros_gastos', '$descripcion_otros_gastos')";
			$insertar =  ejecutarConsulta_retornarID($sql, 'C'); if ($insertar['status'] == false) {  return $insertar; } 
			
			return $insertar;
		} else {
			$info_repetida = ''; 

			foreach ($existe['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['nombre'].'</span><br>
					<span class="font-size-15px "><b>Descripcion: </b>'.$value['descripcion'].'</span><br>
					<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
					<b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
					<hr class="m-t-2px m-b-2px">
				</li>'; 
			}
			return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
		}			
	}

	//Implementamos un método para editar registros
	public function editar($idotros_gastos_categoria, $nombre_otros_gastos, $descripcion_otros_gastos ) {
		$sql_0 = "SELECT * FROM otros_gastos_categoria  WHERE nombre = '$nombre_otros_gastos' AND idotros_gastos_categoria <> '$idotros_gastos_categoria';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
      
    if ( empty($existe['data']) ) {
			$sql="UPDATE otros_gastos_categoria SET nombre='$nombre_otros_gastos', descripcion='$descripcion_otros_gastos' WHERE idotros_gastos_categoria='$idotros_gastos_categoria'";
			$editar =  ejecutarConsulta($sql, 'U');	if ( $editar['status'] == false) {return $editar; } 		
			return $editar;
		} else {
			$info_repetida = ''; 

			foreach ($existe['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['nombre'].'</span><br>
					<span class="font-size-15px text-danger"><b>Descripcion: </b>'.$value['descripcion'].'</span><br>
					<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
					<b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
					<hr class="m-t-2px m-b-2px">
				</li>'; 
			}
			return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
		}			
	}

	//Implementamos un método para desactivar color
	public function desactivar($idotros_gastos_categoria) {
		$sql="UPDATE otros_gastos_categoria SET estado='0' WHERE idotros_gastos_categoria='$idotros_gastos_categoria'";
		$desactivar= ejecutarConsulta($sql, 'T');
		return $desactivar;
	}

	//Implementamos un método para eliminar otros_gastos_categoria
	public function eliminar($idotros_gastos_categoria) {
		
		$sql="UPDATE otros_gastos_categoria SET estado_delete='0' WHERE idotros_gastos_categoria='$idotros_gastos_categoria'";
		$eliminar =  ejecutarConsulta($sql, 'D');	if ( $eliminar['status'] == false) {return $eliminar; }  

		return $eliminar;
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idotros_gastos_categoria) {
		$sql="SELECT * FROM otros_gastos_categoria WHERE idotros_gastos_categoria='$idotros_gastos_categoria'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function tabla_principal() {
		$sql="SELECT * FROM otros_gastos_categoria WHERE estado=1  AND estado_delete=1 ORDER BY nombre ASC";
		return ejecutarConsulta($sql);		
	}


}
?>