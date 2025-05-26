<?php
class PlanCuentasController extends ContabilidadAppController{
	var $name = 'PlanCuentas';
	var $uses = array('Contabilidad.PlanCuenta');
	
	var $autorizar = array('formato_cuenta', 'codigo_cuenta', 'autocompleteDescripcion', 'comboPlanCuenta','comboPlanCuentaCompleto', 'imprimir_plan_cuenta', 'plan_cuenta_xls', 'plan_cuenta_pdf');
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	
	
	
	function index($ejercicio_id=null){
		if(!empty($this->data)):
			$datos = $this->PlanCuenta->traerPlanCuenta($this->data['Ejercicio']['id']);
			$this->set('PlanCuentas', $datos['datos']);
			$this->set('ejercicio', $datos['ejercicio']);
		elseif(!empty($ejercicio_id)):
			$datos = $this->PlanCuenta->traerPlanCuenta($ejercicio_id);
			$this->set('PlanCuentas', $datos['datos']);
			$this->set('ejercicio', $datos['ejercicio']);
		endif;
	}
	
	
	function add($nivel=1, $sumariza = 0, $ejercicio_id){
		$ejercicio = $this->PlanCuenta->traeEjercicio($ejercicio_id);
		if(!empty($this->data)):
			if($this->data['PlanCuenta']['nivel'] == 5 && $ejercicio['nivel'] > 5):
				$this->data['PlanCuenta']['codigo_5'] = str_pad($this->data['PlanCuenta']['codigo_5'],$ejercicio['nivel_5'], '0', STR_PAD_RIGHT);
				$this->data['PlanCuenta']['codigo_6'] = str_pad($this->data['PlanCuenta']['codigo_6'],$ejercicio['nivel_6'], '0', STR_PAD_RIGHT);
				$this->data['PlanCuenta']['codigo'] = $this->data['PlanCuenta']['codigo_5'] . '.' . $this->data['PlanCuenta']['codigo_6'];
				if($this->data['PlanCuenta']['codigo_5'] != str_pad('0', $ejercicio['nivel_5'], '0', STR_PAD_RIGHT) && 
				   $this->data['PlanCuenta']['codigo_6'] != str_pad('0', $ejercicio['nivel_6'], '0', STR_PAD_RIGHT)):
					$cuenta = str_replace('.', '', $this->data['PlanCuenta']['codigo_a'] . $this->data['PlanCuenta']['codigo_5'] . str_pad('0', $ejercicio['nivel_6'], '0', STR_PAD_RIGHT));
				   	$cuenta = $this->PlanCuenta->getCuentaByPlanByEjercicio($cuenta, $ejercicio_id, $nivel, $sumariza);
				   	if($cuenta['PlanCuenta']['existe'] == 0):
				   		$fCuenta = $this->PlanCuenta->formato_cuenta($cuenta['PlanCuenta']['cuenta'], $ejercicio);
						$this->Mensaje->error("NO EXISTE EL NIVEL INMEDIATO ANTERIOR DE LA CUENTA " .$fCuenta );
						// $this->redirect('/contabilidad/plan_cuentas/edit/'.$ejercicio_id.'/'.$cuenta.'/'.$nivel.'/'.$sumariza); 
					endif;
				elseif($this->data['PlanCuenta']['codigo_5'] == str_pad('0', $ejercicio['nivel_5'], '0', STR_PAD_RIGHT) && 
				   $this->data['PlanCuenta']['codigo_6'] != str_pad('0', $ejercicio['nivel_6'], '0', STR_PAD_RIGHT)):
					$cuenta = str_replace('.', '', $this->data['PlanCuenta']['codigo_a'] . $this->data['PlanCuenta']['codigo'] . $this->data['PlanCuenta']['codigo_p']);
					$ejercicio_id = $this->data['PlanCuenta']['ejercicio_id'];
					$nivel = 6;
					$sumariza = $this->data['PlanCuenta']['sumariza'];
					$this->data = array();
					$this->redirect('/contabilidad/plan_cuentas/edit/'.$ejercicio_id.'/'.$cuenta.'/'.$nivel.'/'.$sumariza); 
				elseif($this->data['PlanCuenta']['codigo_5'] != str_pad('0', $ejercicio['nivel_5'], '0', STR_PAD_RIGHT) && 
				   $this->data['PlanCuenta']['codigo_6'] == str_pad('0', $ejercicio['nivel_6'], '0', STR_PAD_RIGHT)):
					$cuenta = str_replace('.', '', $this->data['PlanCuenta']['codigo_a'] . $this->data['PlanCuenta']['codigo'] . $this->data['PlanCuenta']['codigo_p']);
					$ejercicio_id = $this->data['PlanCuenta']['ejercicio_id'];
					$nivel = 5;
					$sumariza = $this->data['PlanCuenta']['sumariza'];
					$this->data = array();
					$this->redirect('/contabilidad/plan_cuentas/edit/'.$ejercicio_id.'/'.$cuenta.'/'.$nivel.'/'.$sumariza);
				else:
					$cuenta = str_replace('.', '', $this->data['PlanCuenta']['codigo_a'] . $this->data['PlanCuenta']['codigo'] . $this->data['PlanCuenta']['codigo_p']);
					$ejercicio_id = $this->data['PlanCuenta']['ejercicio_id'];
					// $nivel = 5;
					$sumariza = $this->data['PlanCuenta']['sumariza'];
					$this->data = array();
					$this->redirect('/contabilidad/plan_cuentas/edit/'.$ejercicio_id.'/'.$cuenta.'/'.$nivel.'/'.$sumariza);
				endif;
			else:
				$cuenta = str_replace('.', '', $this->data['PlanCuenta']['codigo_a'] . $this->data['PlanCuenta']['codigo'] . $this->data['PlanCuenta']['codigo_p']);
				$ejercicio_id = $this->data['PlanCuenta']['ejercicio_id'];
				$nivel = $this->data['PlanCuenta']['nivel'];
				$sumariza = $this->data['PlanCuenta']['sumariza'];
				$this->data = array();
				$this->redirect('/contabilidad/plan_cuentas/edit/'.$ejercicio_id.'/'.$cuenta.'/'.$nivel.'/'.$sumariza); 
			endif;
		endif;
		
		// defragmento el codigo de cuenta
		$codigo = $this->codigo_cuenta($nivel, $sumariza, $ejercicio);
		$this->set('codigo_a', $codigo['codigo_a']);
		$this->set('codigo',   $codigo['codigo']);
		$this->set('codigo_p', $codigo['codigo_p']);
		$this->set('nivel_add', $nivel);
		$this->set('nivel', $ejercicio['nivel']);
		$this->set('sumariza', $sumariza);
		$this->set('ejercicio_id', $ejercicio['id']);
		if($nivel == 1) $this->set('nivel_len', $ejercicio['nivel_1']);
		if($nivel == 2) $this->set('nivel_len', $ejercicio['nivel_2']);
		if($nivel == 3) $this->set('nivel_len', $ejercicio['nivel_3']);
		if($nivel == 4) $this->set('nivel_len', $ejercicio['nivel_4']);
		if($nivel == 5) $this->set('nivel_len5', $ejercicio['nivel_5']); $this->set('nivel_len6', $ejercicio['nivel_6']);
		if($nivel == 6) $this->set('nivel_len', $ejercicio['nivel_6']);
	}
	
	
	function edit($ejercicio_id, $cuenta,$nivel,$sumariza){
		if(!empty($this->data)):
			$this->data['PlanCuenta']['co_plan_cuenta_id'] = $this->PlanCuenta->getPlanCuentaId($this->data['PlanCuenta']['co_ejercicio_id'], $this->data['PlanCuenta']['sumariza']);
			if($this->PlanCuenta->guardar($this->data)):
				$this->Mensaje->okGuardar();
				$this->redirect('/contabilidad/plan_cuentas/index/'.$this->data['PlanCuenta']['co_ejercicio_id']);
			else:
				$this->Mensaje->errorGuardar();
			endif;
		endif;
		$this->data = $this->PlanCuenta->getCuentaByPlanByEjercicio($cuenta, $ejercicio_id, $nivel, $sumariza);
		
		$this->set('nivel_add', $this->data['PlanCuenta']['nivel']);
		$this->set('sumariza', $this->data['PlanCuenta']['sumariza']);
		$this->set('ejercicio_id', $this->data['PlanCuenta']['ejercicio_id']);
	}
	
//	function formato_cuenta($dato, $ejercicio){
//		return $this->PlanCuenta->formato_cuenta($dato, $ejercicio);
//	}
	
	function codigo_cuenta($nivel, $sumariza, $ejercicio){
		$codigo = array('codigo_a' => '', 'codigo' => '', 'codigo_p' => '');
		$ini = 0;
		$codigo_1 = substr($sumariza,$ini,$ejercicio['nivel_1']);
		$ini += $ejercicio['nivel_1'];
		if($ejercicio['nivel'] >= 2) $codigo_2 = substr($sumariza,$ini,$ejercicio['nivel_2']); $ini += $ejercicio['nivel_2'];
		if($ejercicio['nivel'] >= 3) $codigo_3 = substr($sumariza,$ini,$ejercicio['nivel_3']); $ini += $ejercicio['nivel_3'];
		if($ejercicio['nivel'] >= 4) $codigo_4 = substr($sumariza,$ini,$ejercicio['nivel_4']); $ini += $ejercicio['nivel_4'];
		if($ejercicio['nivel'] >= 5) $codigo_5 = substr($sumariza,$ini,$ejercicio['nivel_5']); $ini += $ejercicio['nivel_5'];
		if($ejercicio['nivel'] >= 6) $codigo_6 = substr($sumariza,$ini,$ejercicio['nivel_6']);
		
		// asocio el plan de cuentas
		if($nivel == 1): 
			if($ejercicio['nivel'] >= 2) $codigo['codigo_p']  = '.' . str_pad($codigo_2, $ejercicio['nivel_2'], '0', STR_PAD_RIGHT);
			if($ejercicio['nivel'] >= 3) $codigo['codigo_p'] .= '.' . str_pad($codigo_3, $ejercicio['nivel_3'], '0', STR_PAD_RIGHT);
			if($ejercicio['nivel'] >= 4) $codigo['codigo_p'] .= '.' . str_pad($codigo_4, $ejercicio['nivel_4'], '0', STR_PAD_RIGHT);
			if($ejercicio['nivel'] >= 5) $codigo['codigo_p'] .= '.' . str_pad($codigo_5, $ejercicio['nivel_5'], '0', STR_PAD_RIGHT);
			if($ejercicio['nivel'] >= 6) $codigo['codigo_p'] .= '.' . str_pad($codigo_6, $ejercicio['nivel_6'], '0', STR_PAD_RIGHT);
		endif;

		if($nivel == 2): 
			if($ejercicio['nivel'] >= 1) $codigo['codigo_a']  = $codigo_1 . '.';
			if($ejercicio['nivel'] >= 3) $codigo['codigo_p'] .= '.' . str_pad($codigo_3, $ejercicio['nivel_3'], '0', STR_PAD_RIGHT);
			if($ejercicio['nivel'] >= 4) $codigo['codigo_p'] .= '.' . str_pad($codigo_4, $ejercicio['nivel_4'], '0', STR_PAD_RIGHT);
			if($ejercicio['nivel'] >= 5) $codigo['codigo_p'] .= '.' . str_pad($codigo_5, $ejercicio['nivel_5'], '0', STR_PAD_RIGHT);
			if($ejercicio['nivel'] >= 6) $codigo['codigo_p'] .= '.' . str_pad($codigo_6, $ejercicio['nivel_6'], '0', STR_PAD_RIGHT);
		endif;

		if($nivel == 3): 
			if($ejercicio['nivel'] >= 1) $codigo['codigo_a']  = $codigo_1;
			if($ejercicio['nivel'] >= 2) $codigo['codigo_a'] .= '.' . $codigo_2 . '.';
			if($ejercicio['nivel'] >= 4) $codigo['codigo_p'] .= '.' . str_pad($codigo_4, $ejercicio['nivel_4'], '0', STR_PAD_RIGHT);
			if($ejercicio['nivel'] >= 5) $codigo['codigo_p'] .= '.' . str_pad($codigo_5, $ejercicio['nivel_5'], '0', STR_PAD_RIGHT);
			if($ejercicio['nivel'] >= 6) $codigo['codigo_p'] .= '.' . str_pad($codigo_6, $ejercicio['nivel_6'], '0', STR_PAD_RIGHT);
		endif;

		if($nivel == 4): 
			if($ejercicio['nivel'] >= 1) $codigo['codigo_a']  = $codigo_1;
			if($ejercicio['nivel'] >= 2) $codigo['codigo_a'] .= '.' . $codigo_2;
			if($ejercicio['nivel'] >= 3) $codigo['codigo_a'] .= '.' . $codigo_3 . '.';
			if($ejercicio['nivel'] >= 5) $codigo['codigo_p'] .= '.' . str_pad($codigo_5, $ejercicio['nivel_5'], '0', STR_PAD_RIGHT);
			if($ejercicio['nivel'] >= 6) $codigo['codigo_p'] .= '.' . str_pad($codigo_6, $ejercicio['nivel_6'], '0', STR_PAD_RIGHT);
		endif;

		if($nivel == 5): 
			if($ejercicio['nivel'] >= 1) $codigo['codigo_a']  = $codigo_1;
			if($ejercicio['nivel'] >= 2) $codigo['codigo_a'] .= '.' . $codigo_2;
			if($ejercicio['nivel'] >= 3) $codigo['codigo_a'] .= '.' . $codigo_3;
			if($ejercicio['nivel'] >= 4) $codigo['codigo_a'] .= '.' . $codigo_4 . '.';
			if($ejercicio['nivel'] >= 6) $codigo['codigo_p'] .= '.' . str_pad($codigo_6, $ejercicio['nivel_6'], '0', STR_PAD_RIGHT);
		endif;
		
		if($nivel == 6): 
			if($ejercicio['nivel'] >= 1) $codigo['codigo_a']  = $codigo_1;
			if($ejercicio['nivel'] >= 2) $codigo['codigo_a'] .= '.' . $codigo_2;
			if($ejercicio['nivel'] >= 3) $codigo['codigo_a'] .= '.' . $codigo_3;
			if($ejercicio['nivel'] >= 4) $codigo['codigo_a'] .= '.' . $codigo_4;
			if($ejercicio['nivel'] >= 5) $codigo['codigo_a'] .= '.' . $codigo_5 . '.';
		endif;

		return $codigo;
	}
	
	function del($id = null){
		if(empty($id)) $this->redirect('index');
		$cuenta = $this->PlanCuenta->getCuenta($id);
		
		if ($this->PlanCuenta->del($id)):
			$this->Mensaje->okBorrar();
			$this->Auditoria->log();
		else:
			$this->Mensaje->error("No se puede borrar la Cuenta." );
		endif;
		$this->redirect('/contabilidad/plan_cuentas/index/'.$cuenta['PlanCuenta']['co_ejercicio_id']);
	}	
	
	function autocompleteDescripcion($ejercicio_id, $imputable = 2){
		Configure::write('debug',0);
		
		if($imputable == 2):
			$condiciones = array("PlanCuenta.co_ejercicio_id" => $ejercicio_id, 'PlanCuenta.imputable <' => $imputable,"PlanCuenta.descripcion LIKE " => $this->data['Asiento']['descripcionAproxima'] . "%");
		else:
			$condiciones = array("PlanCuenta.co_ejercicio_id" => $ejercicio_id, 'PlanCuenta.imputable' => $imputable,"PlanCuenta.descripcion LIKE " => $this->data['Asiento']['descripcionAproxima'] . "%");
		endif;
		$order = array('PlanCuenta.descripcion');
		$this->set('cuentas',$this->PlanCuenta->getPlanCuentaByCondicion($condiciones, $order));
		$this->render('autocomplete','ajax');
		
	}
	
	function comboPlanCuenta(){
		return $this->PlanCuenta->comboPlanCuenta();
	}
	
	function comboPlanCuentaCompleto(){
		return $this->PlanCuenta->comboPlanCuentaCompleto();
	}	
	
	function imprimir_plan_cuenta($ejercicioId=null){
		$planCuenta = $this->PlanCuenta->traerPlanCuenta($ejercicioId);
		debug($planCuenta);
		exit;
		
	}


	function plan_cuenta_pdf($ejercicioId){
	
		$planCuenta = $this->PlanCuenta->traerPlanCuenta($ejercicioId);
	
		$this->set('planCuenta', $planCuenta['datos']);
		$this->set('ejercicioDescripcion', $planCuenta['ejercicio']['descripcion']);
	
		$this->render('plan_cuenta_pdf', 'pdf');
	
	
			
	}
	
	
	function plan_cuenta_xls($ejercicioId){
// 		$this->PlanCuenta = $this->MutualProcesoAsiento->importarModelo('PlanCuenta', 'contabilidad');
		
		$planCuenta = $this->PlanCuenta->traerPlanCuenta($ejercicioId);

		$planCuentaXls = array();
		foreach($planCuenta['datos'] as $cuenta):
			$rubro = 'ACTIVO';
			if($cuenta['PlanCuenta']['tipo_cuenta'] == 'PA') $rubro = 'PASIVO';
			if($cuenta['PlanCuenta']['tipo_cuenta'] == 'PN') $rubro = 'PATRIMONIO NETO';
			if($cuenta['PlanCuenta']['tipo_cuenta'] == 'RP') $rubro = 'RESULTADO POSITIVO';
			if($cuenta['PlanCuenta']['tipo_cuenta'] == 'RN') $rubro = 'RESULTADO NEGATIVO';
	
			$aTmpPlanCuenta = array(
				'Cuenta' => $cuenta['PlanCuenta']['cuenta'],
				'Descripcion' => $cuenta['PlanCuenta']['descripcion'],
				'Rubro' => $rubro,
				'Asiento' => ($cuenta['PlanCuenta']['imputable'] == 0 ? 'NO' : 'SI'),
				'Sumariza' => $cuenta['PlanCuenta']['co_plan_cuenta_id']
			);
			array_push($planCuentaXls, $aTmpPlanCuenta);
		endforeach;
		
		$this->set('planCuenta', $planCuentaXls);
		$this->set('ejercicioDescripcion', $planCuenta['ejercicio']['descripcion']);
		
		$this->render('plan_cuenta_xls', 'blank');
		return true;
			
	}

}
?>