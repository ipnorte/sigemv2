<?php
class SolicitudEstadoHist extends V1AppModel{
	
	var $name = 'SolicitudEstadoHist';
	var $primaryKey = 'id';
	var $useTable = 'solicitud_estados_hist';
	
	var $useDbConfig = 'v1';
	

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
	
	
	function grabarHistorial($nro_solicitud,$nEstado,$observaciones,$userTo=null){
		
		$ahora = date("Y/m/d H:i:s");
		
//		$estados = $this->find('all',array('conditions' => array('SolicitudEstadoHist.nro_solicitud' => $nro_solicitud),'order' => array('SolicitudEstadoHist.fecha DESC'),'limit' => 1));
		$estados = $this->find('all',array('conditions' => array('SolicitudEstadoHist.nro_solicitud' => $nro_solicitud),'order' => array('SolicitudEstadoHist.fecha DESC')));
		
		if(empty($estados)) return true;
		
		$ultimo = array_slice($estados,0,1);
		$ultimo = $ultimo[0];
		
		$horas = parent::horasHabiles($ultimo['SolicitudEstadoHist']['fecha_in'],$ahora);
		
		$update = array();
		
		$this->id = $ultimo['SolicitudEstadoHist']['id'];
		$update['SolicitudEstadoHist']['id'] = $ultimo['SolicitudEstadoHist']['id'];
		$update['SolicitudEstadoHist']['usuario_to'] = (!empty($userTo) ? $userTo : $_SESSION['NAME_USER_LOGON_SIGEM']);
		$update['SolicitudEstadoHist']['fecha_out'] = $ahora;
		$update['SolicitudEstadoHist']['horas'] = round($horas,3);
		$update['SolicitudEstadoHist']['dias'] = parent::dateDiff('w',$ultimo['SolicitudEstadoHist']['fecha_in'],$ahora);
		
		if(!parent::save($update))return false;
		
		$this->id = 0;
		$estado = array('SolicitudEstadoHist' => array(
			'id' => 0,
			'nro_solicitud' => $nro_solicitud,
			'estado' => $nEstado,
			'fecha' => $ahora,
			'observacion' => $observaciones,
			'usuario' => (!empty($userTo) ? $userTo : $_SESSION['NAME_USER_LOGON_SIGEM']),
			'fecha_ac' => $ahora,
			'fecha_in' => $ahora
		));
		
		return parent::save($estado);
		
	}
	
	
	function getUsuariosNotifica(){
		$sql = "SELECT * FROM sg_usuarios_supervisa AS SgUsuariosSupervisa";
		$datos = parent::query($sql);
		if(empty($datos)) return null;
		$datos = Set::extract("{n}.SgUsuariosSupervisa.usuario",$datos);
		return $datos;
	}
	
	
}
?>