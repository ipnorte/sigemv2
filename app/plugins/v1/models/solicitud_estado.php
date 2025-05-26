<?php
class SolicitudEstado extends V1AppModel{
	var $name = 'SolicitudEstado';
	var $primaryKey = 'codigo';
	var $useTable = 'solicitud_codigo_estados';

	function getHistorial($nro_solicitud){
		$sql = "SELECT solicitud_estados_hist.fecha,solicitud_estados_hist.observacion,solicitud_estados_hist.usuario,
				solicitud_estados_hist.estado,solicitud_estados_hist.dias,solicitud_estados_hist.horas,
				solicitud_estados_hist.fecha_in,solicitud_estados_hist.fecha_out,solicitud_estados_hist.usuario_to,
				solicitud_codigo_estados.descripcion 
				FROM solicitud_estados_hist  
				LEFT JOIN solicitud_codigo_estados ON solicitud_estados_hist.estado = solicitud_codigo_estados.codigo  
				WHERE solicitud_estados_hist.nro_solicitud  = $nro_solicitud  
				ORDER BY solicitud_estados_hist.fecha DESC ";
		$datos = parent::query($sql);
		$datos = $this->calculaHorasHabiles($datos);
		return $datos;
	}
	
	
	function calculaHorasHabiles($datos){
		foreach($datos as $idx => $valor){
			$horas = parent::horasHabiles($valor['solicitud_estados_hist']['fecha_in'],date('Y-m-d H:m:s'));
			$datos[$idx]['solicitud_estados_hist']['horas_habiles'] = round($horas,2);
		}
		return $datos;	
	}
	
	
	function grabarHistorial($nro_solicitud,$estado,$observaciones){
		$ahora = date("Y/m/d H:i:s");
		$sql = "insert into solicitud_estados_hist (nro_solicitud,estado,fecha,observacion,usuario,fecha_ac,fecha_in)
				values($nro_solicitud,$estado,'$ahora','$observaciones','','$ahora','$ahora')";
		debug($sql);
		#TRAER EL ID DEL ULTIMO ESTADO
		#PONER LA FECHA DE SALIDA Y CALCULAR LAS HORAS
		
//		return parent::query($sql);
	}
	
}
?>