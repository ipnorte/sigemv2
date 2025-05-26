<?php
class PersonaBeneficioCompartido extends PfyjAppModel{
	
	var $name = 'PersonaBeneficioCompartido';
	
	
	function agregar($datos){
		
		
		if(empty($datos['PersonaBeneficioCompartido']['porcentaje'])) return "DEBE INDICAR EL PORCENTAJE!";
		
		//controlar el codigo de reparticion
		$conditions = array();
		$conditions['PersonaBeneficioCompartido.persona_beneficio_id'] = $datos['PersonaBeneficioCompartido']['persona_beneficio_id'];
		$conditions['PersonaBeneficioCompartido.codigo_reparticion'] = $datos['PersonaBeneficioCompartido']['codigo_reparticion'];
		if($this->find('count',array('conditions' => $conditions)) != 0){
			return "YA EXISTE EL CODIGO DE REPARTICION!";
		}

		//cargo el OP
		if(empty($datos['PersonaBeneficioCompartido']['turno_pago']) && $datos['PersonaBeneficioCompartido']['codigo_empresa'] == 'MUTUEMPRP001'){
			App::import('Model', 'Mutual.LiquidacionTurno');
			$oTURNO = new LiquidacionTurno();
			$datos['PersonaBeneficioCompartido']['turno_pago'] = $oTURNO->getTurnoByCodRepa($datos['PersonaBeneficioCompartido']['codigo_empresa'],$datos['PersonaBeneficioCompartido']['codigo_reparticion']);
			if(empty($datos['PersonaBeneficioCompartido']['turno_pago'])) return "EL TURNO DE PAGO NO SE ENCUENTRA PARA EL CODIGO DE REPARTICION INDICADO!";
		}else if($datos['PersonaBeneficioCompartido']['codigo_empresa'] != 'MUTUEMPRP001'){
			$datos['PersonaBeneficioCompartido']['turno_pago'] = $datos['PersonaBeneficioCompartido']['codigo_empresa'];
		}
		
		//controlar la suma de los porcentajes
		$conditions1 = array();
		$conditions1['PersonaBeneficioCompartido.persona_beneficio_id'] = $datos['PersonaBeneficioCompartido']['persona_beneficio_id'];
		$sumPorc = $this->find('all',array('conditions' => $conditions1,'fields' => array('sum(porcentaje) as porcentaje')));
		$porcentaje = $datos['PersonaBeneficioCompartido']['porcentaje'];
		if(isset($sumPorc[0][0]['porcentaje'])) $porcentaje += $sumPorc[0][0]['porcentaje'];
		
		if($porcentaje >= 100){
			return "LA SUMA TOTAL DE LOS PORCENTAJES NO PUEDE SUPERAR EL 100%";
		}
		
		//ACTUALIZO EL PORCENTAJE DEL BENEFICIO PRINCIPAL
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBENEFICIO = new PersonaBeneficio();	

		$beneficio = $oBENEFICIO->read(null,$datos['PersonaBeneficioCompartido']['persona_beneficio_id']);
		$beneficio['PersonaBeneficio']['porcentaje'] = 100 - $porcentaje;
//		debug($beneficio);
		if(!$oBENEFICIO->save($beneficio)) return "ERROR AL ACTUALIZAR EL PORCENTAJE EN EL BENEFICIO PRINCIPAL!";
		
		if(!$this->save($datos)) return "ERROR AL GRABAR EL NUEVO SUB-BENEFICIO!";
		
		return null;
	}
	
	
	function borrar($id){
		
		$bc = $this->read(null,$id);
		
		if(!$this->del($id)) return false;
		
		$conditions1 = array();
		$conditions1['PersonaBeneficioCompartido.persona_beneficio_id'] = $bc['PersonaBeneficioCompartido']['persona_beneficio_id'];
		$sumPorc = $this->find('all',array('conditions' => $conditions1,'fields' => array('sum(porcentaje) as porcentaje')));
		$porcentaje = 0;
		if(isset($sumPorc[0][0]['porcentaje'])) $porcentaje = $sumPorc[0][0]['porcentaje'];
		
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBENEFICIO = new PersonaBeneficio();	

		$beneficio = $oBENEFICIO->read(null,$bc['PersonaBeneficioCompartido']['persona_beneficio_id']);
		$beneficio['PersonaBeneficio']['porcentaje'] = 100 - $porcentaje;
//		debug($beneficio);
		if(!$oBENEFICIO->save($beneficio)) return false;
		
		return true;
		
	}
	
	
}
?>