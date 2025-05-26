<?php
class TipoDocumento extends ConfigAppModel{
	
	var $name = 'TipoDocumento';
	
	function lookRegistro($tipo){
		$i = 0;
		while ($i <= 50):
			$look = $this->find('all', array('conditions' => array('TipoDocumento.documento' => $tipo)));
			if($look[0]['TipoDocumento']['look'] == 0):
				$look[0]['TipoDocumento']['look'] = 1;
				if($this->save($look[0]['TipoDocumento'])):
					return true;
				endif;
			endif;
			$i++;
		endwhile;
			
		return false;
	}
	
	
	function unLookRegistro($tipo){
		$i = 0;
		
		while ($i <= 50):
			$unLook = $this->find('all', array('conditions' => array('TipoDocumento.documento' => $tipo)));
			if($unLook[0]['TipoDocumento']['look'] == 1):
				$unLook[0]['TipoDocumento']['look'] = 0;
				if($this->save($unLook[0]['TipoDocumento'])):
					return true;
				endif;
			endif;
			$i++;
		endwhile;
			
		return false;
	}
	
	
	function getNumero($tipo){
		$i = 0;
		
		while ($i <= 50):
			if($this->lookRegistro($tipo)):
				$look = $this->find('all', array('conditions' => array('TipoDocumento.documento' => $tipo)));
				return $look[0]['TipoDocumento']['numero'] + 1;
			endif;
			$i++;
		endwhile;
			
		return 0;
	}
	
	function putNumero($tipo, $nCantidad=1){
		$i = 0;
		
		while ($i <= 50):
			$look = $this->find('all', array('conditions' => array('TipoDocumento.documento' => $tipo)));
			if($look[0]['TipoDocumento']['look'] == 1):
				$look[0]['TipoDocumento']['look'] = 0;
				$look[0]['TipoDocumento']['numero'] += $nCantidad;
				if($this->save($look[0]['TipoDocumento'])):
					return true;
				endif;
			endif;
			$i++;
		endwhile;
			
		return false;
		
	}
	
	function comboRecibo(){
		$aRecibos = $this->find('all', array('conditions' => array('TipoDocumento.documento' => 'RCB')));
		$aCombo = array();
		if(empty($aRecibos)) return $aCombo;
		
		foreach($aRecibos as $dato){
			$aCombo[$dato['TipoDocumento']['tipo_documento']] = $dato['TipoDocumento']['descripcion'];
		}
		
		return $aCombo;
		
	}
	
	function getDocumentoDescripcion($documento){
		$descripcion = $this->find('all', array('conditions' => array('TipoDocumento.documento' => $documento)));
		
		return $descripcion[0]['TipoDocumento']['descripcion'];
		
	}

	function getComprobante($documento){
		$descripcion = $this->find('all', array('conditions' => array('TipoDocumento.documento' => $documento)));
		
		return $descripcion[0]['TipoDocumento'];
		
	}
	
	
	function getLetra($documento){
		$descripcion = $this->find('all', array('conditions' => array('TipoDocumento.documento' => $documento)));
		
		return $descripcion[0]['TipoDocumento']['letra'];
		
	}

}
?>