<?php
class ClienteTipoAsiento extends ClientesAppModel{
	var $name = 'ClienteTipoAsiento';
	var $useTable = 'cliente_tipo_asientos';
	
	
	
	
	function traerTipoAsiento($id){
		$datos = array();
		$aTipoAsiento = $this->read(null,$id);
		
		// Llamo al modelo de los renglones
		$oTipoAsientoRenglon = $this->importarModelo('ClienteTipoAsientoRenglon', 'clientes');
		$aRegnlones = $oTipoAsientoRenglon->findAll("cliente_tipo_asiento_id = $id");
		
		$datos['ClienteTipoAsiento']['id'] = $aTipoAsiento['ClienteTipoAsiento']['id'];
		$datos['ClienteTipoAsiento']['descripcion'] = $aTipoAsiento['ClienteTipoAsiento']['concepto'];
		$datos['ClienteTipoAsiento']['tipo_asiento'] = $aTipoAsiento['ClienteTipoAsiento']['tipo_asiento'];
		
		foreach($aRegnlones as $renglon){
			
			if($renglon['ClienteTipoAsientoRenglon']['variable'] == 'TOTAL'){
				$datos['ClienteTipoAsiento']['totalid'] = $renglon['ClienteTipoAsientoRenglon']['id'];
				$datos['ClienteTipoAsiento']['totalcuenta'] = $renglon['ClienteTipoAsientoRenglon']['co_plan_cuenta_id'];
				$datos['ClienteTipoAsiento']['totaltipo'] = $renglon['ClienteTipoAsientoRenglon']['debe_haber'];
			}
			if($renglon['ClienteTipoAsientoRenglon']['variable'] == 'NGRAV'){
				$datos['ClienteTipoAsiento']['ngravid'] = $renglon['ClienteTipoAsientoRenglon']['id'];
				$datos['ClienteTipoAsiento']['ngravcuenta'] = $renglon['ClienteTipoAsientoRenglon']['co_plan_cuenta_id'];
				$datos['ClienteTipoAsiento']['ngravtipo'] = $renglon['ClienteTipoAsientoRenglon']['debe_haber'];
			}
			
		}
		
		return $datos;
	}
	
	
	function grabar($datos){
		$tipoAsiento = array();
		$tipoAsientoRenglones = array();

		// Cabecera
		$tipoAsiento['id'] = isset($datos['ClienteTipoAsiento']['id']) ?  $datos['ClienteTipoAsiento']['id'] : 0;
		$tipoAsiento['concepto'] = $datos['ClienteTipoAsiento']['descripcion'];
		$tipoAsiento['tipo_asiento'] = $datos['ClienteTipoAsiento']['tipo_asiento'];

		$this->begin();
		if(!$this->save($tipoAsiento)):		
			$this->rollback();
			return false;
		endif;

		$nTipoAsientoId = isset($datos['ClienteTipoAsiento']['id']) ?  $datos['ClienteTipoAsiento']['id'] : $this->getLastInsertID();

		// Renglones
		// Total Comprobante
		if($datos['ClienteTipoAsiento']['tipo_asiento'] == 'GR'):
			$tipoAsientoRenglones[0]['id'] = isset($datos['ClienteTipoAsiento']['totalid']) ?  $datos['ClienteTipoAsiento']['totalid'] : 0;
			$tipoAsientoRenglones[0]['variable'] = 'TOTAL';
			$tipoAsientoRenglones[0]['cliente_tipo_asiento_id'] = $nTipoAsientoId;
			$tipoAsientoRenglones[0]['co_plan_cuenta_id'] = $datos['ClienteTipoAsiento']['totalcuenta'];
			$tipoAsientoRenglones[0]['debe_haber'] = $datos['ClienteTipoAsiento']['totaltipo'];
			$tipoAsientoRenglones[0]['tipo_asiento'] = $datos['ClienteTipoAsiento']['tipo_asiento'];
		endif;

		
		// Neto No Gravado
		$tipoAsientoRenglones[1]['id'] = isset($datos['ClienteTipoAsiento']['ngravid']) ?  $datos['ClienteTipoAsiento']['ngravid'] : 0;
		$tipoAsientoRenglones[1]['variable'] = 'NGRAV';
		$tipoAsientoRenglones[1]['cliente_tipo_asiento_id'] = $nTipoAsientoId;
		$tipoAsientoRenglones[1]['co_plan_cuenta_id'] = $datos['ClienteTipoAsiento']['ngravcuenta'];
		$tipoAsientoRenglones[1]['debe_haber'] = $datos['ClienteTipoAsiento']['ngravtipo'];
		$tipoAsientoRenglones[1]['tipo_asiento'] = $datos['ClienteTipoAsiento']['tipo_asiento'];
		
		
		// Llamo al modelo de los renglones
		$oTipoAsientoRenglon = $this->importarModelo('ClienteTipoAsientoRenglon', 'clientes');
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
			$return[$aCombo[$key]['ClienteTipoAsiento']['id']] = $aCombo[$key]['ClienteTipoAsiento']['concepto'];
		endforeach;
		
		return $return;
	}
	
	
	function borrar($id){
		// Llamo al modelo de los renglones
		$oTipoAsientoRenglon = $this->importarModelo('ClienteTipoAsientoRenglon', 'clientes');
		
		$this->begin();
		if(!$oTipoAsientoRenglon->deleteAll('ClienteTipoAsientoRenglon.cliente_tipo_asiento_id = ' . $id)):
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