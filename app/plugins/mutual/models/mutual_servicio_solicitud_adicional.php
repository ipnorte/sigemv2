<?php 

class MutualServicioSolicitudAdicional extends MutualAppModel{
	
	var $name = "MutualServicioSolicitudAdicional";
	
	
	function bajaAdicional($id,$periodoHasta,$fechaCoberturaHasta,$borrar=false,$observaciones=null){
		$solAdic = $this->read(null,$id);
		
		if($borrar){
			if(!$this->del($id)) return false;
			//RECALCULAR LA ORDEN
			App::import('Model','mutual.MutualServicioSolicitud');
			$oSOL = new MutualServicioSolicitud();
			$solicitud = $oSOL->getSolicitud($solAdic['MutualServicioSolicitudAdicional']['mutual_servicio_solicitud_id']);
			App::import('Model','Mutual.MutualServicioValor');
			$oSERV_VALOR = new MutualServicioValor();			
			$oSERV_VALOR->calcularImporteMensual($solicitud['MutualServicioSolicitud']['id'],$periodoHasta,$solicitud['MutualServicioSolicitud']['beneficio_organismo'],true);
			return true;
		}
		$solAdic['MutualServicioSolicitudAdicional']['periodo_hasta'] = ($periodoHasta < $solAdic['MutualServicioSolicitudAdicional']['periodo_desde'] ? $solAdic['MutualServicioSolicitudAdicional']['periodo_desde'] : $periodoHasta);
		$solAdic['MutualServicioSolicitudAdicional']['fecha_baja'] = $fechaCoberturaHasta;
		$solAdic['MutualServicioSolicitudAdicional']['observaciones'] = $observaciones;
		$solAdic['MutualServicioSolicitudAdicional']['fecha_emision_baja'] = date("Y-m-d");
		
		return $this->save($solAdic);
	}
	
	
	function checkAdicional($adicional_id){
		$count = $this->find('count',array('conditions' => array('MutualServicioSolicitudAdicional.socio_adicional_id' => $adicional_id)));
		return ($count != 0 ? true : false);
	}
	
	
}

?>