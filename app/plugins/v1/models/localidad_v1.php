<?php
class LocalidadV1 extends V1AppModel{
	
	var $name = 'LocalidadV1';
	var $primaryKey = 'id';
	var $useTable = 'localidades';

	function save($data = null, $validate = true, $fieldList = array()){
		
//		App::import('Model', 'Config.Provincia');
//		$this->Provincia = new Provincia(null);
//		$prv = $this->Provincia->read('letra', $data['Localidad']['provincia_id']);
//		
//		$datos = array('LocalidadV1' => array(
//					'id' => $data['Localidad']['idr'],
//					'codigo_postal' => $data['Localidad']['cp'],
//					'localidad' => $data['Localidad']['nombre'],
//					'codigo_provincia' => $prv['Provincia']['letra']
//		));	
//		return parent::save($datos);
	}
	
	
}
?>