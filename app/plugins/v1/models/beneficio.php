<?php
class Beneficio extends V1AppModel{

	var $name = 'Beneficio';
	var $primaryKey = 'id_beneficio';
	var $useTable = 'personas_beneficios';


	function read($fields,$id){

		$ben = parent::read($fields,$id);

		if(!empty($ben['Beneficio']['codigo_banco'])):
			$banco = parent::getBanco('banco',$ben['Beneficio']['codigo_banco']);
			$ben['Beneficio']['banco'] = $banco['BancoV1']['banco'];
		endif;

		if(!empty($ben['Beneficio']['codigo_beneficio'])):
			$glb = parent::getGlobal('concepto','XXTO'.$ben['Beneficio']['codigo_beneficio']);
			$ben['Beneficio']['beneficio_concepto'] = $glb['Tglobal']['concepto'];
		endif;

		if(!empty($ben['Beneficio']['codigo_empresa'])):
			$glb = parent::getGlobal('concepto','EMPR'.$ben['Beneficio']['codigo_empresa']);
			$ben['Beneficio']['empresa'] = $glb['Tglobal']['concepto'];
		endif;

		return $ben;
	}


	function bySocio($socio_id){

		$beneficios = array();

		$beneficios = $this->find('all',array('conditions' => array('Beneficio.id_persona' => $socio_id),'order' => array('Beneficio.principal DESC')));

		foreach($beneficios as $i => $b){
			$beneficios[$i] = $this->armaDatos($b);
		}

		return $beneficios;
	}


	function actualizarV2($personaV2_id,$beneficio){

		App::import('Model', 'Pfyj.PersonaBeneficio');
		$oBENEFICIO_V2 = new PersonaBeneficio(null);

		$conditions = array();
		$conditions['PersonaBeneficio.persona_id'] = $personaV2_id;
		$conditions['PersonaBeneficio.codigo_beneficio'] = $beneficio['codigo_beneficio'];

		$tipo = substr($beneficio['codigo_beneficio'],8,2);

		switch ($tipo) {
			case 22:
				$conditions['PersonaBeneficio.banco_id'] = $beneficio['codigo_banco'];
				$conditions['PersonaBeneficio.cbu'] = $beneficio['cbu'];
				$conditions['PersonaBeneficio.codigo_empresa'] = $beneficio['codigo_empresa'];
				break;
			case 77:
				$conditions['PersonaBeneficio.tipo'] = $beneficio['tipo'];
				$conditions['PersonaBeneficio.nro_ley'] = $beneficio['nro_ley'];
				$conditions['PersonaBeneficio.nro_beneficio'] = $beneficio['nro_beneficio'];
				$conditions['PersonaBeneficio.sub_beneficio'] = $beneficio['sub_beneficio'];
				break;
			case 66:
				$conditions['PersonaBeneficio.nro_beneficio'] = $beneficio['nro_beneficio'];
				break;
			default:
				;
				break;
		}

		$beneficiosV2 = $oBENEFICIO_V2->find('all',array('conditions' => $conditions));



		if(empty($beneficiosV2) && $personaV2_id != 0):

			$beneficio['persona_id'] = $personaV2_id;

			//verificar el turno
			if(empty($beneficio['turno_pago']) && $beneficio['codigo_empresa'] != "MUTUEMPRP001") $beneficio['turno_pago'] = $beneficio['codigo_empresa'];

			if(empty($beneficio['turno_pago']) && $beneficio['codigo_empresa'] == "MUTUEMPRP001"){

				//buscar el turno en la liquidacion_turnos
				App::import('Model', 'Mutual.LiquidacionTurno');
				$oTURNO = new LiquidacionTurno();

				$turnos = $oTURNO->find('all',array('conditions' => array('LiquidacionTurno.codigo_empresa' => $beneficio['codigo_empresa'],'LiquidacionTurno.codigo_reparticion LIKE' => ( !empty($beneficio['codigo_reparticion']) ? substr($beneficio['codigo_reparticion'],0,6).'%' : '')),'limit' => 1));
				if(isset($turnos[0]['LiquidacionTurno'])) $beneficio['turno_pago'] = $turnos[0]['LiquidacionTurno']['turno'];

			}

			$beneficiosV2 = Set::insert(array(),'PersonaBeneficio',$beneficio);
//			$oBENEFICIO_V2->save($beneficiosV2);

		endif;

//		debug($personaV2_id);
//		debug($beneficio);

	}


	function getBeneficio($id){
		$beneficio = $this->armaDatos($this->read(null,$id));
		return $beneficio;
	}


	function armaDatos($beneficio){

		if(empty($beneficio)) return $beneficio;

		$beneficio['Beneficio']['id'] = 0;
		$beneficio['Beneficio']['idr_tv2'] = $beneficio['Beneficio']['id_beneficio'];
		$beneficio['Beneficio']['idr_fv2'] = 0;
		$beneficio['Beneficio']['banco_id'] = $beneficio['Beneficio']['codigo_banco'];
		$beneficio['Beneficio']['nro_sucursal'] = $beneficio['Beneficio']['sucursal'];

		$bco = parent::getBanco('banco',$beneficio['Beneficio']['codigo_banco']);
		$glb = parent::getGlobal('concepto','XXTO'. $beneficio['Beneficio']['codigo_beneficio']);
		$beneficio['Beneficio']['banco'] = $bco['BancoV1']['banco'];
		$beneficio['Beneficio']['beneficio_concepto'] = $glb['Tglobal']['concepto'];

		$beneficio['Beneficio']['codigo_beneficio'] = "MUTUCORG" . $beneficio['Beneficio']['codigo_beneficio'];

		$beneficio['Beneficio']['empresa'] = "";

		if(!empty($beneficio['Beneficio']['codigo_empresa'])):
			$glb = parent::getGlobal('concepto',"EMPR".$beneficio['Beneficio']['codigo_empresa']);
			$beneficio['Beneficio']['codigo_empresa'] = "MUTUEMPR" . $beneficio['Beneficio']['codigo_empresa'];

			$beneficio['Beneficio']['empresa'] = $glb['Tglobal']['concepto'];
		endif;
		//armo el string
		$tipo = substr($beneficio['Beneficio']['codigo_beneficio'],8,2);
		$str = "";
		switch ($tipo) {
			case '22':
				$str = $beneficio['Beneficio']['beneficio_concepto'] . ' - EMPRESA: ' . $beneficio['Beneficio']['empresa'] . ' - ' . $beneficio['Beneficio']['banco'] . ' - CBU: '.$beneficio['Beneficio']['cbu'] ;
				break;
			case '77':
				$str = $beneficio['Beneficio']['beneficio_concepto'] . ' - NRO.: ' . $beneficio['Beneficio']['nro_beneficio'] . ' - LEY: '.$beneficio['Beneficio']['nro_ley'] ;
				break;
			case '66':
				$str = $beneficio['Beneficio']['beneficio_concepto'] . ' - NRO.: ' . $beneficio['Beneficio']['nro_beneficio'];
				break;

		}
		$beneficio['Beneficio']['string'] = $str;

		return $beneficio;
	}


	function guardar($data){

		if(isset($data['PersonaBeneficio']['idr_persona']) && $data['PersonaBeneficio']['idr_persona'] != 0):

			if(empty($data['PersonaBeneficio']['idr'])) $data['PersonaBeneficio']['idr'] = 0;

			$ben = parent::read(null,$data['PersonaBeneficio']['idr']);

			if(!empty($ben)){

				if(!isset($data['PersonaBeneficio']['idr'])) $data['PersonaBeneficio']['idr'] = $ben['Beneficio']['id_beneficio'];
				if(!isset($data['PersonaBeneficio']['codigo_beneficio'])) $data['PersonaBeneficio']['codigo_beneficio'] = "MUTUCORG".$ben['Beneficio']['codigo_beneficio'];
				if(!isset($data['PersonaBeneficio']['nro_beneficio'])) $data['PersonaBeneficio']['nro_beneficio'] = $ben['Beneficio']['nro_beneficio'];
				if(!isset($data['PersonaBeneficio']['tipo'])) $data['PersonaBeneficio']['tipo'] = $ben['Beneficio']['tipo'];
				if(!isset($data['PersonaBeneficio']['nro_ley'])) $data['PersonaBeneficio']['nro_ley'] = $ben['Beneficio']['nro_ley'];
				if(!isset($data['PersonaBeneficio']['nro_legajo'])) $data['PersonaBeneficio']['nro_legajo'] = $ben['Beneficio']['nro_legajo'];
				if(!isset($data['PersonaBeneficio']['sub_beneficio'])) $data['PersonaBeneficio']['sub_beneficio'] = $ben['Beneficio']['sub_beneficio'];
				if(!isset($data['PersonaBeneficio']['fecha_ingreso'])) $data['PersonaBeneficio']['fecha_ingreso'] = $ben['Beneficio']['fecha_ingreso'];
				if(!isset($data['PersonaBeneficio']['codigo_reparticion'])) $data['PersonaBeneficio']['codigo_reparticion'] = $ben['Beneficio']['codigo_reparticion'];
				if(!isset($data['PersonaBeneficio']['banco_id'])) $data['PersonaBeneficio']['banco_id'] = $ben['Beneficio']['codigo_banco'];
				if(!isset($data['PersonaBeneficio']['sucursal'])) $data['PersonaBeneficio']['sucursal'] = $ben['Beneficio']['sucursal'];
				if(!isset($data['PersonaBeneficio']['tipo_cta_bco'])) $data['PersonaBeneficio']['tipo_cta_bco'] = $ben['Beneficio']['tipo_cta_bco'];
				if(!isset($data['PersonaBeneficio']['nro_cta_bco'])) $data['PersonaBeneficio']['nro_cta_bco'] = $ben['Beneficio']['nro_cta_bco'];
				if(!isset($data['PersonaBeneficio']['cbu'])) $data['PersonaBeneficio']['cbu'] = $ben['Beneficio']['cbu'];
				if(!isset($data['PersonaBeneficio']['codigo_empresa'])) $data['PersonaBeneficio']['codigo_empresa'] = "MUTUEMPR".$ben['Beneficio']['codigo_empresa'];
				if(!isset($data['PersonaBeneficio']['activo'])) $data['PersonaBeneficio']['activo'] = $ben['Beneficio']['activo'];

			}

			$datos = array('Beneficio' => array(
				'id_beneficio' => (isset($data['PersonaBeneficio']['idr']) ? $data['PersonaBeneficio']['idr'] : 0),
				'id_persona' => $data['PersonaBeneficio']['idr_persona'],
				'codigo_beneficio' => substr($data['PersonaBeneficio']['codigo_beneficio'],8,4),
				'nro_beneficio' => (isset($data['PersonaBeneficio']['nro_beneficio']) ? $data['PersonaBeneficio']['nro_beneficio'] : ""),
				'tipo' => (isset($data['PersonaBeneficio']['tipo']) ? $data['PersonaBeneficio']['tipo'] : 1),
				'nro_ley' => (isset($data['PersonaBeneficio']['nro_ley']) ? $data['PersonaBeneficio']['nro_ley'] : ""),
				'nro_legajo' => (isset($data['PersonaBeneficio']['nro_legajo']) ? $data['PersonaBeneficio']['nro_legajo'] : ""),
				'sub_beneficio' => (isset($data['PersonaBeneficio']['sub_beneficio']) ? $data['PersonaBeneficio']['sub_beneficio'] : ""),
				'fecha_ingreso' => (isset($data['PersonaBeneficio']['fecha_ingreso']) ? $data['PersonaBeneficio']['fecha_ingreso']['year'].'-'.$data['PersonaBeneficio']['fecha_ingreso']['month'].'-'.$data['PersonaBeneficio']['fecha_ingreso']['day'] : null),
				'codigo_reparticion' => (isset($data['PersonaBeneficio']['codigo_reparticion']) ? $data['PersonaBeneficio']['codigo_reparticion'] : null),
				'codigo_banco' => (isset($data['PersonaBeneficio']['banco_id']) ? $data['PersonaBeneficio']['banco_id'] : ''),
				'sucursal' => (isset($data['PersonaBeneficio']['sucursal']) ? $data['PersonaBeneficio']['sucursal'] : ''),
				'tipo_cta_bco' => (isset($data['PersonaBeneficio']['tipo_cta_bco']) ? $data['PersonaBeneficio']['tipo_cta_bco'] : ''),
				'nro_cta_bco' => (isset($data['PersonaBeneficio']['nro_cta_bco']) ? $data['PersonaBeneficio']['nro_cta_bco'] : ''),
				'cbu' => (isset($data['PersonaBeneficio']['cbu']) ? $data['PersonaBeneficio']['cbu'] : null),
				'codigo_empresa' => (isset($data['PersonaBeneficio']['codigo_empresa']) ? substr($data['PersonaBeneficio']['codigo_empresa'],8,4) : null),
				'activo' => (isset($data['PersonaBeneficio']['activo']) ? $data['PersonaBeneficio']['activo'] : 1),
			));
			return parent::save($datos);
		else:
			return true;
		endif;
	}


}
?>
