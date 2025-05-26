<?php
class TipoAsiento extends ProveedoresAppModel{
	var $name = 'TipoAsiento';
	var $useTable = 'proveedor_tipo_asientos';
	
	
//	function traerCuentas(){
//		$glb = $this->getGlobalDato('entero_1',"CONTEVIG");
//		$tmp = array();
//		$return = array();
//		
//		$oPlanCuentas = $this->importarModelo('PlanCuenta', 'contabilidad');
//		$planCuentas = $oPlanCuentas->comboPlanCuenta();
//		
////		foreach($planCuentas['datos'] as $cuenta){
////			if($cuenta['PlanCuenta']['imputable'] == 1):
////				$return[$cuenta['PlanCuenta']['id']] = $cuenta['PlanCuenta']['cuenta'] . ' - ' . $cuenta['PlanCuenta']['descripcion'];
////			endif;
////		}
//
////		return $return;
//		return $planCuentas;
//	}
	
	
	function traerTipoAsiento($id){
		$datos = array();
		$aTipoAsiento = $this->read(null,$id);
		
		// Llamo al modelo de los renglones
		$oTipoAsientoRenglon = $this->importarModelo('TipoAsientoRenglon', 'proveedores');
		$aRegnlones = $oTipoAsientoRenglon->findAll("proveedor_tipo_asiento_id = $id");
		
		$datos['TipoAsiento']['id'] = $aTipoAsiento['TipoAsiento']['id'];
		$datos['TipoAsiento']['descripcion'] = $aTipoAsiento['TipoAsiento']['concepto'];
		$datos['TipoAsiento']['tipo_asiento'] = $aTipoAsiento['TipoAsiento']['tipo_asiento'];
		
		foreach($aRegnlones as $renglon){
			
			if($renglon['TipoAsientoRenglon']['variable'] == 'TOTAL'){
				$datos['TipoAsiento']['totalid'] = $renglon['TipoAsientoRenglon']['id'];
				$datos['TipoAsiento']['totalcuenta'] = $renglon['TipoAsientoRenglon']['co_plan_cuenta_id'];
				$datos['TipoAsiento']['totaltipo'] = $renglon['TipoAsientoRenglon']['debe_haber'];
			}
			if($renglon['TipoAsientoRenglon']['variable'] == 'GRAVA'){
				$datos['TipoAsiento']['gravaid'] = $renglon['TipoAsientoRenglon']['id'];
				$datos['TipoAsiento']['gravacuenta'] = $renglon['TipoAsientoRenglon']['co_plan_cuenta_id'];
				$datos['TipoAsiento']['gravatipo'] = $renglon['TipoAsientoRenglon']['debe_haber'];
			}
			if($renglon['TipoAsientoRenglon']['variable'] == 'IVA'){
				$datos['TipoAsiento']['ivaid'] = $renglon['TipoAsientoRenglon']['id'];
				$datos['TipoAsiento']['ivacuenta'] = $renglon['TipoAsientoRenglon']['co_plan_cuenta_id'];
				$datos['TipoAsiento']['ivatipo'] = $renglon['TipoAsientoRenglon']['debe_haber'];
			}
			if($renglon['TipoAsientoRenglon']['variable'] == 'NGRAV'){
				$datos['TipoAsiento']['ngravid'] = $renglon['TipoAsientoRenglon']['id'];
				$datos['TipoAsiento']['ngravcuenta'] = $renglon['TipoAsientoRenglon']['co_plan_cuenta_id'];
				$datos['TipoAsiento']['ngravtipo'] = $renglon['TipoAsientoRenglon']['debe_haber'];
			}
			if($renglon['TipoAsientoRenglon']['variable'] == 'PERCE'){
				$datos['TipoAsiento']['perceid'] = $renglon['TipoAsientoRenglon']['id'];
				$datos['TipoAsiento']['percecuenta'] = $renglon['TipoAsientoRenglon']['co_plan_cuenta_id'];
				$datos['TipoAsiento']['percetipo'] = $renglon['TipoAsientoRenglon']['debe_haber'];
			}
			if($renglon['TipoAsientoRenglon']['variable'] == 'RETEN'){
				$datos['TipoAsiento']['retenid'] = $renglon['TipoAsientoRenglon']['id'];
				$datos['TipoAsiento']['retencuenta'] = $renglon['TipoAsientoRenglon']['co_plan_cuenta_id'];
				$datos['TipoAsiento']['retentipo'] = $renglon['TipoAsientoRenglon']['debe_haber'];
			}
			if($renglon['TipoAsientoRenglon']['variable'] == 'IMINT'){
				$datos['TipoAsiento']['imintid'] = $renglon['TipoAsientoRenglon']['id'];
				$datos['TipoAsiento']['imintcuenta'] = $renglon['TipoAsientoRenglon']['co_plan_cuenta_id'];
				$datos['TipoAsiento']['iminttipo'] = $renglon['TipoAsientoRenglon']['debe_haber'];
			}
			if($renglon['TipoAsientoRenglon']['variable'] == 'INBRU'){
				$datos['TipoAsiento']['inbruid'] = $renglon['TipoAsientoRenglon']['id'];
				$datos['TipoAsiento']['inbrucuenta'] = $renglon['TipoAsientoRenglon']['co_plan_cuenta_id'];
				$datos['TipoAsiento']['inbrutipo'] = $renglon['TipoAsientoRenglon']['debe_haber'];
			}
			if($renglon['TipoAsientoRenglon']['variable'] == 'OTROS'){
				$datos['TipoAsiento']['otrosid'] = $renglon['TipoAsientoRenglon']['id'];
				$datos['TipoAsiento']['otroscuenta'] = $renglon['TipoAsientoRenglon']['co_plan_cuenta_id'];
				$datos['TipoAsiento']['otrostipo'] = $renglon['TipoAsientoRenglon']['debe_haber'];
			}
			
		}
		
		return $datos;
	}
	
	
	function grabar($datos){
		$tipoAsiento = array();
		$tipoAsientoRenglones = array();


		// Cabecera
		$tipoAsiento['id'] = isset($datos['TipoAsiento']['id']) ?  $datos['TipoAsiento']['id'] : 0;
		$tipoAsiento['concepto'] = $datos['TipoAsiento']['descripcion'];
		$tipoAsiento['tipo_asiento'] = $datos['TipoAsiento']['tipo_asiento'];

		$this->begin();
		if(!$this->save($tipoAsiento)):		
			$this->rollback();
			return false;
		endif;

		$nTipoAsientoId = isset($datos['TipoAsiento']['id']) ?  $datos['TipoAsiento']['id'] : $this->getLastInsertID();

		// Renglones
		// Neto Gravado
		$tipoAsientoRenglones[0]['id'] = isset($datos['TipoAsiento']['gravaid']) ?  $datos['TipoAsiento']['gravaid'] : 0;
		$tipoAsientoRenglones[0]['variable'] = 'GRAVA';
		$tipoAsientoRenglones[0]['proveedor_tipo_asiento_id'] = $nTipoAsientoId;
		$tipoAsientoRenglones[0]['co_plan_cuenta_id'] = $datos['TipoAsiento']['gravacuenta'];
		$tipoAsientoRenglones[0]['debe_haber'] = $datos['TipoAsiento']['gravatipo'];
		$tipoAsientoRenglones[0]['tipo_asiento'] = $datos['TipoAsiento']['tipo_asiento'];
		
		// I.V.A.
		$tipoAsientoRenglones[1]['id'] = isset($datos['TipoAsiento']['ivaid']) ?  $datos['TipoAsiento']['ivaid'] : 0;
		$tipoAsientoRenglones[1]['variable'] = 'IVA';
		$tipoAsientoRenglones[1]['proveedor_tipo_asiento_id'] = $nTipoAsientoId;
		$tipoAsientoRenglones[1]['co_plan_cuenta_id'] = $datos['TipoAsiento']['ivacuenta'];
		$tipoAsientoRenglones[1]['debe_haber'] = $datos['TipoAsiento']['ivatipo'];
		$tipoAsientoRenglones[1]['tipo_asiento'] = $datos['TipoAsiento']['tipo_asiento'];
		
		// Neto No Gravado
		$tipoAsientoRenglones[2]['id'] = isset($datos['TipoAsiento']['ngravid']) ?  $datos['TipoAsiento']['ngravid'] : 0;
		$tipoAsientoRenglones[2]['variable'] = 'NGRAV';
		$tipoAsientoRenglones[2]['proveedor_tipo_asiento_id'] = $nTipoAsientoId;
		$tipoAsientoRenglones[2]['co_plan_cuenta_id'] = $datos['TipoAsiento']['ngravcuenta'];
		$tipoAsientoRenglones[2]['debe_haber'] = $datos['TipoAsiento']['ngravtipo'];
		$tipoAsientoRenglones[2]['tipo_asiento'] = $datos['TipoAsiento']['tipo_asiento'];
		
		// Percepciones
		$tipoAsientoRenglones[3]['id'] = isset($datos['TipoAsiento']['perceid']) ?  $datos['TipoAsiento']['perceid'] : 0;
		$tipoAsientoRenglones[3]['variable'] = 'PERCE';
		$tipoAsientoRenglones[3]['proveedor_tipo_asiento_id'] = $nTipoAsientoId;
		$tipoAsientoRenglones[3]['co_plan_cuenta_id'] = $datos['TipoAsiento']['percecuenta'];
		$tipoAsientoRenglones[3]['debe_haber'] = $datos['TipoAsiento']['percetipo'];
		$tipoAsientoRenglones[3]['tipo_asiento'] = $datos['TipoAsiento']['tipo_asiento'];
		
		// Retenciones
		$tipoAsientoRenglones[4]['id'] = isset($datos['TipoAsiento']['retenid']) ?  $datos['TipoAsiento']['retenid'] : 0;
		$tipoAsientoRenglones[4]['variable'] = 'RETEN';
		$tipoAsientoRenglones[4]['proveedor_tipo_asiento_id'] = $nTipoAsientoId;
		$tipoAsientoRenglones[4]['co_plan_cuenta_id'] = $datos['TipoAsiento']['retencuenta'];
		$tipoAsientoRenglones[4]['debe_haber'] = $datos['TipoAsiento']['retentipo'];
		$tipoAsientoRenglones[4]['tipo_asiento'] = $datos['TipoAsiento']['tipo_asiento'];
		
		// Impuestos Internos
		$tipoAsientoRenglones[5]['id'] = isset($datos['TipoAsiento']['imintid']) ?  $datos['TipoAsiento']['imintid'] : 0;
		$tipoAsientoRenglones[5]['variable'] = 'IMINT';
		$tipoAsientoRenglones[5]['proveedor_tipo_asiento_id'] = $nTipoAsientoId;
		$tipoAsientoRenglones[5]['co_plan_cuenta_id'] = $datos['TipoAsiento']['imintcuenta'];
		$tipoAsientoRenglones[5]['debe_haber'] = $datos['TipoAsiento']['iminttipo'];
		$tipoAsientoRenglones[5]['tipo_asiento'] = $datos['TipoAsiento']['tipo_asiento'];
		
		// Ingresos Brutos
		$tipoAsientoRenglones[6]['id'] = isset($datos['TipoAsiento']['inbruid']) ?  $datos['TipoAsiento']['inbruid'] : 0;
		$tipoAsientoRenglones[6]['variable'] = 'INBRU';
		$tipoAsientoRenglones[6]['proveedor_tipo_asiento_id'] = $nTipoAsientoId;
		$tipoAsientoRenglones[6]['co_plan_cuenta_id'] = $datos['TipoAsiento']['inbrucuenta'];
		$tipoAsientoRenglones[6]['debe_haber'] = $datos['TipoAsiento']['inbrutipo'];
		$tipoAsientoRenglones[6]['tipo_asiento'] = $datos['TipoAsiento']['tipo_asiento'];
		
		// Otros Impuestos
		$tipoAsientoRenglones[7]['id'] = isset($datos['TipoAsiento']['otrosid']) ?  $datos['TipoAsiento']['otrosid'] : 0;
		$tipoAsientoRenglones[7]['variable'] = 'OTROS';
		$tipoAsientoRenglones[7]['proveedor_tipo_asiento_id'] = $nTipoAsientoId;
		$tipoAsientoRenglones[7]['co_plan_cuenta_id'] = $datos['TipoAsiento']['otroscuenta'];
		$tipoAsientoRenglones[7]['debe_haber'] = $datos['TipoAsiento']['otrostipo'];
		$tipoAsientoRenglones[7]['tipo_asiento'] = $datos['TipoAsiento']['tipo_asiento'];
		
		// Total Comprobante
		if($datos['TipoAsiento']['tipo_asiento'] == 'GR'):
			$tipoAsientoRenglones[8]['id'] = isset($datos['TipoAsiento']['totalid']) ?  $datos['TipoAsiento']['totalid'] : 0;
			$tipoAsientoRenglones[8]['variable'] = 'TOTAL';
			$tipoAsientoRenglones[8]['proveedor_tipo_asiento_id'] = $nTipoAsientoId;
			$tipoAsientoRenglones[8]['co_plan_cuenta_id'] = $datos['TipoAsiento']['totalcuenta'];
			$tipoAsientoRenglones[8]['debe_haber'] = $datos['TipoAsiento']['totaltipo'];
			$tipoAsientoRenglones[8]['tipo_asiento'] = $datos['TipoAsiento']['tipo_asiento'];
		endif;

		
		// Llamo al modelo de los renglones
		$oTipoAsientoRenglon = $this->importarModelo('TipoAsientoRenglon', 'proveedores');
		if(!$oTipoAsientoRenglon->saveAll($tipoAsientoRenglones)):
			$this->rollback();
			return false;
		endif;
					
		$this->commit();
		return true;
	}

	function combo(){
		$return = array(0 => 'Seleccionar. . .');
		$aCombo = $this->findAll();
		foreach($aCombo as $key => $value):
			$return[$aCombo[$key]['TipoAsiento']['id']] = $aCombo[$key]['TipoAsiento']['concepto'];
		endforeach;
		
		return $return;
	}
	
	
	function borrar($id){
		// Llamo al modelo de los renglones
		$oTipoAsientoRenglon = $this->importarModelo('TipoAsientoRenglon', 'proveedores');
		
		$this->begin();
		if(!$oTipoAsientoRenglon->deleteAll('TipoAsientoRenglon.proveedor_tipo_asiento_id = ' . $id)):
			$this->rollback();
			return false;
		endif;
		
		if(!$this->del($id)):
			$this->rollback();
			return false;
		endif;
		
		$this->commit();
		return true;
	}
}
?>