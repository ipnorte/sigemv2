<?php
class BancoSucursal extends ConfigAppModel{
	
	var $name = 'BancoSucursal';
	function save($data = null, $validate = true, $fieldList = array()){
		$ret = parent::save($data);
		if($ret && MODULO_V1){
			App::import('Model', 'V1.BancoSucursalV1');
			$this->BancoSucursalV1 = new BancoSucursalV1(null);			
			$this->BancoSucursalV1->save($data);
		}
		return $ret;
	}
	
}
?>