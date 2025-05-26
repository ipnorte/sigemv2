<?php
class BancoSucursalV1 extends V1AppModel{
	
	var $name = 'BancoSucursalV1';
	var $primaryKey = 'id_sucursal';
	var $useTable = 'bancos_sucursales';

	function save($data = null, $validate = true, $fieldList = array()){
		$datos = array('BancoSucursalV1' => array(
					'id_sucursal' => $data['BancoSucursal']['id'],
					'nro_sucursal' => $data['BancoSucursal']['nro_sucursal'],
					'sucursal' => $data['BancoSucursal']['nombre'],
					'codigo_banco' => $data['BancoSucursal']['banco_id'],
					'direccion' => $data['BancoSucursal']['direccion']
		));		
		return parent::save($datos);
	}
	
	
}
?>