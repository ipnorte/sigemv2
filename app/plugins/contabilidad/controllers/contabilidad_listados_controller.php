<?php
class ContabilidadListadosController extends ContabilidadAppController{
	
	var $name = 'ContabilidadListados';
	var $uses = array('contabilidad.MutualProcesoAsiento');
	
	var $autorizar = array( 
							'plan_cuenta_xls', 'plan_cuenta_pdf', 'libro_mayor_pdf', 'libro_mayor_xls', 'libro_diario_pdf', 'libro_diario_xls'
	);
	
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}		
	
	
// 	function libro_diario_pdf($procesoId){
		
// 		$proceso = $this->MutualProcesoAsiento->read(null, $procesoId);
		
// //		$asientos = $this->MutualProcesoAsiento->getAsientos($procesoId);
// //		
// //		$this->set('asientos', $asientos);
// 		$this->set('procesoId', $procesoId);
// 		$this->set('fecha_desde', $proceso['MutualProcesoAsiento']['fecha_desde']);
// 		$this->set('fecha_hasta', $proceso['MutualProcesoAsiento']['fecha_hasta']);
		
		
// 		$this->render('libro_diario_borrador_pdf', 'pdf');
		
		
			
// 	}
	

// 	function libro_diario_xls($procesoId){
		
// 		$proceso = $this->MutualProcesoAsiento->read(null, $procesoId);
		
// //		$asientos = $this->MutualProcesoAsiento->getAsientos($procesoId);
// //		
// //		$this->set('asientos', $asientos);
// 		$this->set('procesoId', $procesoId);
// 		$this->set('fecha_desde', $proceso['MutualProcesoAsiento']['fecha_desde']);
// 		$this->set('fecha_hasta', $proceso['MutualProcesoAsiento']['fecha_hasta']);
		
		
// 		$this->render('libro_diario_borrador_xls', 'blank');
		
		
			
// 	}
	

// 	function plan_cuenta_pdf($ejercicioId){
// 		$this->PlanCuenta = $this->MutualProcesoAsiento->importarModelo('PlanCuenta', 'contabilidad');
		
// 		$planCuenta = $this->PlanCuenta->traerPlanCuenta($ejercicioId);
		
// 		$this->set('planCuenta', $planCuenta['datos']);
// 		$this->set('ejercicioDescripcion', $planCuenta['ejercicio']['descripcion']);
		
// 		$this->render('plan_cuenta_pdf', 'pdf');
		
		
			
// 	}
	

// 	function plan_cuenta_xls($ejercicioId){
// 		$this->PlanCuenta = $this->MutualProcesoAsiento->importarModelo('PlanCuenta', 'contabilidad');
		
// 		$planCuenta = $this->PlanCuenta->traerPlanCuenta($ejercicioId);

// 		$planCuentaXls = array();
// 		foreach($planCuenta['datos'] as $cuenta):
// 			$rubro = 'ACTIVO';
// 			if($cuenta['PlanCuenta']['tipo_cuenta'] == 'PA') $rubro = 'PASIVO';
// 			if($cuenta['PlanCuenta']['tipo_cuenta'] == 'PN') $rubro = 'PATRIMONIO NETO';
// 			if($cuenta['PlanCuenta']['tipo_cuenta'] == 'RP') $rubro = 'RESULTADO POSITIVO';
// 			if($cuenta['PlanCuenta']['tipo_cuenta'] == 'RN') $rubro = 'RESULTADO NEGATIVO';
	
// 			$aTmpPlanCuenta = array(
// 				'Cuenta' => $cuenta['PlanCuenta']['cuenta'],
// 				'Descripcion' => $cuenta['PlanCuenta']['descripcion'],
// 				'Rubro' => $rubro,
// 				'Asiento' => ($cuenta['PlanCuenta']['imputable'] == 0 ? 'NO' : 'SI'),
// 				'Sumariza' => $cuenta['PlanCuenta']['co_plan_cuenta_id']
// 			);
// 			array_push($planCuentaXls, $aTmpPlanCuenta);
// 		endforeach;
		
// 		$this->set('planCuenta', $planCuentaXls);
// 		$this->set('ejercicioDescripcion', $planCuenta['ejercicio']['descripcion']);
		
// 		$this->render('plan_cuenta_xls', 'blank');
// 		return true;
			
// 	}
	

// 	function libro_diario_xls($procesoId){
		
// 		$proceso = $this->MutualProcesoAsiento->read(null, $procesoId);
		
// 		$asientos = $this->MutualProcesoAsiento->getAsientos($procesoId);
		
// 		$this->set('asientos', $asientos);
// 		$this->set('fecha_desde', $proceso['MutualProcesoAsiento']['fecha_desde']);
// 		$this->set('fecha_hasta', $proceso['MutualProcesoAsiento']['fecha_hasta']);
		
		
// 		$this->render('libro_diario_borrador_xls', 'blank');
		
		
			
// 	}
	

// 	function libro_mayor_pdf($procesoId, $cuentaId = null){
		
// 		$proceso = $this->MutualProcesoAsiento->read(null, $procesoId);
		
// 		$aMayorDetalle = $this->MutualProcesoAsiento->getMayorDetalle($procesoId, $cuentaId);
// //		$aMayor = $this->MutualProcesoAsiento->getMayoriza($procesoId);

// 		$this->set('aMayorDetalle', $aMayorDetalle);
// //		$this->set('aMayor', $aMayor);
// 		$this->set('fecha_desde', $proceso['MutualProcesoAsiento']['fecha_desde']);
// 		$this->set('fecha_hasta', $proceso['MutualProcesoAsiento']['fecha_hasta']);
		
		
// 		$this->render('libro_mayor_borrador_pdf', 'pdf');
		
		
			
// 	}
	

// 	function libro_mayor_xls($procesoId){
		
// 		$proceso = $this->MutualProcesoAsiento->read(null, $procesoId);
		
// 		$aMayor = $this->MutualProcesoAsiento->getMayoriza($procesoId);
		
// 		$this->set('aMayor', $aMayor);
// 		$this->set('fecha_desde', $proceso['MutualProcesoAsiento']['fecha_desde']);
// 		$this->set('fecha_hasta', $proceso['MutualProcesoAsiento']['fecha_hasta']);
		
// // debug($this);
// // exit;
		
// 		$this->render('libro_mayor_borrador_xls', 'blank');
		
// // 		$this->render('/home/test_xls', 'blank');
		
			
// 	}
	

}
?>