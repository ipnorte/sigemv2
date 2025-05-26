<?php
class PlanCuenta extends ContabilidadAppModel {
	var $name = 'PlanCuenta';
	var $useTable = 'co_plan_cuentas';
	
	function guardar($datos){
		return parent::save($datos);
	}
	
	function traerPlanCuenta($ejercicio_id){
		$ejercicio = $this->traeEjercicio($ejercicio_id);
		$plan_cuenta = $this->find('all',array('conditions' => array('PlanCuenta.co_ejercicio_id' => $ejercicio['id']), 'order' => 'PlanCuenta.cuenta'));
		foreach($plan_cuenta as $key => $value):
			$plan_cuenta[$key]['PlanCuenta']['cuenta'] = $this->formato_cuenta($plan_cuenta[$key]['PlanCuenta']['cuenta'], $ejercicio);
			$plan_cuenta[$key]['PlanCuenta']['co_plan_cuenta_id'] = $this->formato_cuenta($plan_cuenta[$key]['PlanCuenta']['sumariza'], $ejercicio);
			$plan_cuenta[$key]['PlanCuenta']['borrar'] = $this->borrar($plan_cuenta[$key]['PlanCuenta']['id']);
		endforeach;

		$retorno = array('datos' => $plan_cuenta, 'ejercicio' => $ejercicio);
		return $retorno;
	}
	
	function formato_cuenta($codigo, $ejercicio){
		if($codigo == '0') return $codigo;
		$codigo_id = substr($codigo,0,$ejercicio['nivel_1']);
		$ini = $ejercicio['nivel_1'];
		if($ejercicio['nivel'] >= 2) $codigo_id .= '.' . substr($codigo,$ini,$ejercicio['nivel_2']); $ini += $ejercicio['nivel_2'];
		if($ejercicio['nivel'] >= 3) $codigo_id .= '.' . substr($codigo,$ini,$ejercicio['nivel_3']); $ini += $ejercicio['nivel_3'];
		if($ejercicio['nivel'] >= 4) $codigo_id .= '.' . substr($codigo,$ini,$ejercicio['nivel_4']); $ini += $ejercicio['nivel_4'];
		if($ejercicio['nivel'] >= 5) $codigo_id .= '.' . substr($codigo,$ini,$ejercicio['nivel_5']); $ini += $ejercicio['nivel_5'];
		if($ejercicio['nivel'] >= 6) $codigo_id .= '.' . substr($codigo,$ini,$ejercicio['nivel_6']);
		return $codigo_id;
	}
	
	function getCuentaByPlanByEjercicio($cuenta, $ejercicio_id, $nivel, $sumariza){
		$data = $this->find('all',array('conditions' => array('PlanCuenta.cuenta' => $cuenta, 'PlanCuenta.co_ejercicio_id' => $ejercicio_id)));
		if(empty($data)):
			$data[0] = array('PlanCuenta' => array('cuenta' => $cuenta,'co_ejercicio_id' => $ejercicio_id, 'descripcion' => '', 'imputable' => 0, 'actualiza' => 0, 'nivel' => $nivel, 'tipo_cuenta' => '', 'sumariza' => $sumariza, 'existe' => 0));			
		endif;
		return $this->armaDatos($data[0]);	
	}
	
	function armaDatos($cuenta){
		$ejercicio = $this->traeEjercicio($cuenta['PlanCuenta']['co_ejercicio_id']);
		if(empty($cuenta)){
			$cuenta['PlanCuenta']['existe'] = 0;
			return $cuenta;
		}
		$cuenta['PlanCuenta']['codigo'] = $this->formato_cuenta($cuenta['PlanCuenta']['cuenta'], $ejercicio);
		$cuenta['PlanCuenta']['existe'] = isset($cuenta['PlanCuenta']['existe'])? $cuenta['PlanCuenta']['existe'] : 1; 
		return $cuenta;
	}

	function getCuenta($id){
		$cuenta = $this->read(null,$id);
		return $this->armaDatos($cuenta);
	}
	
	function getPlanCuentaByCondicion($condiciones, $order=array(), $limit=null){
		$cuentas = $this->find('all',array("conditions" => $condiciones, 'order' => $order));
	
		if(empty($cuentas)) return $cuentas;
		foreach($cuentas as $key => $cuenta){
			$cuentas[$key] = $this->armaDatos($cuenta);
		}
		return $cuentas;
	}


	function comboPlanCuenta(){
		$glb = $this->getGlobalDato('entero_1',"CONTEVIG");
		$return = array(0 => '');
		$ejercicio = $this->traeEjercicio($glb['GlobalDato']['entero_1']);
debug($ejercicio);
exit;

		$plan_cuenta = $this->find('all',array('conditions' => array('PlanCuenta.co_ejercicio_id' => $ejercicio['id'])));
		foreach($plan_cuenta as $key => $value):
			if($plan_cuenta[$key]['PlanCuenta']['imputable'] == 1):
				$return[$plan_cuenta[$key]['PlanCuenta']['id']] = $this->formato_cuenta($plan_cuenta[$key]['PlanCuenta']['cuenta'], $ejercicio) . ' - ' . $plan_cuenta[$key]['PlanCuenta']['descripcion'];
			endif;
		endforeach;
		return $return;
	}
	
	function comboPlanCuentaCompleto(){
		$glb = $this->getGlobalDato('entero_1',"CONTEVIG");
		$return = array(0 => '');
		$ejercicio = $this->traeEjercicio($glb['GlobalDato']['entero_1']);

//		//cargo nivel 1
//		$plan_cuenta_1 = $this->find('all',array('conditions' => array('PlanCuenta.co_ejercicio_id' => $ejercicio['id'], 'PlanCuenta.nivel' => 1),'order' => array('PlanCuenta.cuenta')));
//		if(empty($plan_cuenta_1)) return $return;
//		$cuentas = array();
//		foreach($plan_cuenta_1 as $key => $value):
//		
////			debug($value);
//			$cuentas[$key]['nivel'] = 1;
//			$cuentas[$key]['imputable'] = $value['PlanCuenta']['imputable'];
//			$cuentas[$key]['cuenta'] = $this->formato_cuenta($value['PlanCuenta']['cuenta'], $ejercicio) . ' - ' . $value['PlanCuenta']['descripcion'];;
//			$cuentas[$key]['childs'] = array();
//			$plan_cuenta_2 = $this->find('all',array('conditions' => array('PlanCuenta.co_ejercicio_id' => $ejercicio['id'], 'PlanCuenta.nivel' => 2, 'PlanCuenta.co_plan_cuenta_id' => $value['PlanCuenta']['cuenta']),'order' => array('PlanCuenta.cuenta')));
//			if(!empty($plan_cuenta_2)){
//				foreach($plan_cuenta_2 as $key2 => $value2):
//					debug($value2);
//				endforeach;
//			}
////			debug($plan_cuenta_2);
//		
//		endforeach;
//		debug($cuentas);
		
		$plan_cuenta = $this->find('all',array('conditions' => array('PlanCuenta.co_ejercicio_id' => $ejercicio['id']),'order' => array('PlanCuenta.cuenta')));
		if(empty($plan_cuenta)) return $return;
		foreach($plan_cuenta as $key => $value):
			$return[$plan_cuenta[$key]['PlanCuenta']['id']]['imputable'] = $plan_cuenta[$key]['PlanCuenta']['imputable'];
			$return[$plan_cuenta[$key]['PlanCuenta']['id']]['cuenta'] = $this->formato_cuenta($plan_cuenta[$key]['PlanCuenta']['cuenta'], $ejercicio) . ' - ' . $plan_cuenta[$key]['PlanCuenta']['descripcion'];
		endforeach;
		return $return;
	}
	
	
	function borrar($id){
		$this->oAsiento = $this->importarModelo('AsientoRenglon', 'contabilidad');
		
		$cuenta = $this->read(null,$id);
		$subCuenta = $this->find('all', array('conditions' => array('PlanCuenta.co_ejercicio_id' => $cuenta['PlanCuenta']['co_ejercicio_id'], 'PlanCuenta.co_plan_cuenta_id' => $cuenta['PlanCuenta']['cuenta'])));
		$asientos = $this->oAsiento->tieneAsientos($cuenta['PlanCuenta']['id']);
		if(!empty($subCuenta)) return 1;
		if($asientos) return 1;
		return 0;
	}
	
	
	function getPlanCuentaId($ejercicio_id, $cuenta){
		$condiciones = array('PlanCuenta.co_ejercicio_id' => $ejercicio_id, 'Plancuenta.cuenta' => $cuenta);
		
		$cuentas = $this->getPlanCuentaByCondicion($condiciones);
	
		if(empty($cuentas)) return $cuentas;
		
		
		return $cuentas[0]['PlanCuenta']['id'];
	}
	
	
	function copiarPlanCuenta($id){
		$idVigente = $this->getEjercicioVigente();
		$ejercicioVigente = $this->traeEjercicio($idVigente);
		$ejercicioNuevo = $this->traeEjercicio($id);
		$okEstructura = true;
		
		// La estructura del Plan de Cuenta no son compatibles entre los Ejercicios a Copiar
		if($ejercicioNuevo['nivel'] != $ejercicioVigente['nivel']) $okEstructura = false;
		if($ejercicioNuevo['nivel_1'] != $ejercicioVigente['nivel_1']) $okEstructura = false;
		if($ejercicioNuevo['nivel_2'] != $ejercicioVigente['nivel_2']) $okEstructura = false;
		if($ejercicioNuevo['nivel_3'] != $ejercicioVigente['nivel_3']) $okEstructura = false;
		if($ejercicioNuevo['nivel_4'] != $ejercicioVigente['nivel_4']) $okEstructura = false;
		if($ejercicioNuevo['nivel_5'] != $ejercicioVigente['nivel_5']) $okEstructura = false;
		if($ejercicioNuevo['nivel_6'] != $ejercicioVigente['nivel_6']) $okEstructura = false;
		
// debug($ejercicioVigente);
// debug($ejercicioNuevo);
// exit;
		
		if(!$okEstructura) return 'ESTRUCTURA';

		// Copio el Plan de Cuentas
		$sql = "
					INSERT INTO `co_plan_cuentas` 
						(`cuenta`, 
						`co_ejercicio_id`, 
						`descripcion`, 
						`imputable`, 
						`actualiza`, 
						`nivel`, 
						`tipo_cuenta`, 
						`co_plan_cuenta_id`, 
						`sumariza`, 
						`acumulado_debe`, 
						`acumulado_haber`, 
						`vincula_co_plan_cuenta_id`
						)
						
					SELECT 	`cuenta`, 
						$id, 
						`descripcion`, 
						`imputable`, 
						`actualiza`, 
						`nivel`, 
						`tipo_cuenta`, 
						`co_plan_cuenta_id`, 
						`sumariza`, 
						`acumulado_debe`, 
						`acumulado_haber`, 
						`id`
						 
					FROM 
						`co_plan_cuentas` 
					WHERE	co_ejercicio_id = $idVigente
					ORDER	BY cuenta
				";
			
		if(!$this->query($sql)) return 'COPIAR';
		
		$sql = "
					UPDATE	co_plan_cuentas p, co_plan_cuentas p1
					SET	p.co_plan_cuenta_id = p1.id
					WHERE	p.co_ejercicio_id = $id AND p.co_ejercicio_id = p1.co_ejercicio_id AND p.sumariza = p1.cuenta
				";
		
		if(!$this->query($sql)) return 'ACTUALIZA';
		
		return true;
	}
	
	
	function getMayorDetalle($ejercicio_id, $fecha_desde, $fecha_hasta, $cuentaId=null){
		$oAsientoRenglon = $this->importarModelo('AsientoRenglon', 'contabilidad');
		
		$sql = "SELECT Asiento.id, Asiento.nro_asiento, Asiento.fecha, Asiento.referencia, PlanCuenta.cuenta, PlanCuenta.descripcion, AsientoRenglon.*
				FROM co_asiento_renglones AsientoRenglon
				INNER JOIN co_plan_cuentas PlanCuenta
				ON AsientoRenglon.co_plan_cuenta_id = PlanCuenta.id
				INNER JOIN co_asientos Asiento
				ON AsientoRenglon.co_asiento_id = Asiento.id
				WHERE Asiento.borrado = 0 AND Asiento.fecha >= '$fecha_desde' AND Asiento.fecha <= '$fecha_hasta' AND Asiento.co_ejercicio_id = '$ejercicio_id'
		";

		if(!empty($cuentaId)) $sql .= " AND PlanCuenta.id = '$cuentaId'";

		$sql .= " ORDER BY AsientoRenglon.co_plan_cuenta_id, Asiento.nro_asiento";
		
		
		return $this->query($sql);
		
	}
	
	
	
	function getMayoriza($ejercicio_id, $fecha_desde, $fecha_hasta){
		$sql = "SELECT PlanCuenta.id, PlanCuenta.cuenta, PlanCuenta.descripcion, SUM(AsientoRenglon.debe) AS debe, SUM(AsientoRenglon.haber) AS haber
				FROM co_asiento_renglones AsientoRenglon
				INNER JOIN co_plan_cuentas PlanCuenta
				ON AsientoRenglon.co_plan_cuenta_id = PlanCuenta.id
				INNER JOIN co_asientos Asiento
				ON AsientoRenglon.co_asiento_id = Asiento.id
				WHERE Asiento.borrado = 0 AND Asiento.fecha >= '$fecha_desde' AND Asiento.fecha <= '$fecha_hasta' AND Asiento.co_ejercicio_id = '$ejercicio_id'
				GROUP BY PlanCuenta.id
				ORDER BY AsientoRenglon.co_plan_cuenta_id
	
		";

		return $this->query($sql);
		
	}
	
	
	function getBalanceGeneral($ejercicio_id, $fecha_desde, $fecha_hasta, $nivel = 6){
		$ejercicio = $this->traeEjercicio($ejercicio_id);
		
		$sqlUpdate = "UPDATE co_plan_cuentas set acumulado_debe = 0.00, acumulado_haber = 0.00 WHERE co_ejercicio_id = '$ejercicio_id'";
		
		$this->query($sqlUpdate);
		
		
		$sqlUpdate = "UPDATE co_plan_cuentas PlanCuenta
						SET	PlanCuenta.acumulado_debe = (SELECT SUM(debe) FROM co_asiento_renglones WHERE co_plan_cuenta_id = PlanCuenta.id AND fecha >= '$fecha_desde' AND fecha <= '$fecha_hasta'), 
							PlanCuenta.acumulado_haber = (SELECT SUM(haber) FROM co_asiento_renglones WHERE co_plan_cuenta_id = PlanCuenta.id AND fecha >= '$fecha_desde' AND fecha <= '$fecha_hasta')
						WHERE	PlanCuenta.co_ejercicio_id = '$ejercicio_id'
				";
		
		$this->query($sqlUpdate);
		
		$nNivel = $ejercicio['nivel'] - 1;
		
		for($i = $nNivel; $i >= 1; $i--):
			$aPlanCuenta = $this->find('all',array('conditions' => array('PlanCuenta.co_ejercicio_id' => $ejercicio['id'], 'PlanCuenta.nivel' => $i, 'PlanCuenta.imputable' => 0), 'order' => 'PlanCuenta.cuenta'));
			foreach ($aPlanCuenta as $planCuenta):
				$aSuma = $this->find('all', array('conditions' => array('PlanCuenta.co_plan_cuenta_id' => $planCuenta['PlanCuenta']['id']), 'fields' => array('SUM(PlanCuenta.acumulado_debe) as debe', 'SUM(PlanCuenta.acumulado_haber) as haber')));
// debug($aSuma);
				$planCuenta['PlanCuenta']['acumulado_debe'] = $aSuma[0][0]['debe'];
				$planCuenta['PlanCuenta']['acumulado_haber'] = $aSuma[0][0]['haber'];
				
				$this->save($planCuenta);
// debug($planCuenta);
// exit;
			endforeach;
		endfor;
		
		$aPlanCuenta = $this->find('all',array('conditions' => array('PlanCuenta.co_ejercicio_id' => $ejercicio['id'], 'PlanCuenta.nivel <=' => $nivel), 'order' => 'PlanCuenta.cuenta'));
		
		return $aPlanCuenta;
		
	}
}
?>