<?php
class BancoV1 extends V1AppModel{
	
	var $name = 'BancoV1';
	var $primaryKey = 'codigo_banco';
	var $useTable = 'bancos';

	function save($data = null, $validate = true, $fieldList = array()){
		$datos = array('BancoV1' => array(
					'codigo_banco' => $data['Banco']['id'],
					'banco' => $data['Banco']['nombre'],
					'activo' => $data['Banco']['activo'],
					'beneficio' => $data['Banco']['beneficio'],
					'forma_pago' => $data['Banco']['fpago']
		));		
		return parent::save($datos);
	}
	
	
}
?>