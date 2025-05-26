<?php
class ProveedorV1 extends V1AppModel{
    
	var $name = 'ProveedorV1';
	var $primaryKey = 'codigo_proveedor';
	var $useTable = 'proveedores';	
	
	public function getProveedor($codigo){
	    return $this->read(null,$codigo);
	}
	
	public function getActivos() {
	    $proveedores = $this->find('all',array('conditions' => array('ProveedorV1.activo' => 1),'order' => array('ProveedorV1.razon_social')));
	    return $proveedores;
	}
	
}
?>