<?php 

class LiquidacionSocioDescuento extends MutualAppModel{
	
	var $name = 'LiquidacionSocioDescuento';
	
	function getImpoDescuentoAplicado($periodo,$codOrg,$socioId,$ordenDtoId){
		$conditions = array();
		$conditions['LiquidacionSocioDescuento.periodo_liquidacion'] = $periodo;
		$conditions['LiquidacionSocioDescuento.codigo_organismo'] = $codOrg;
		$conditions['LiquidacionSocioDescuento.socio_id'] = $socioId;
		$conditions['LiquidacionSocioDescuento.orden_descuento_id'] = $ordenDtoId;
		$dtos = $this->find('all',array('conditions' => $conditions));
		if(!empty($dtos)) return $dtos[0]['LiquidacionSocioDescuento']['importe_total'];
		else return 0;
	}
	
	
}

?>