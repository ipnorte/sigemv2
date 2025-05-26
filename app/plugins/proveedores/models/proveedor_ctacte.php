<?php
class ProveedorCtacte extends ProveedoresAppModel{
	var $name = 'ProveedorCtacte';
	var $useTable = 'proveedor_ctactes';


	public function getCuentaCte($proveedor_id){
		return $this->find('all',array('conditions' => array('proveedor_id' => $proveedor_id)));
	}

}
?>

