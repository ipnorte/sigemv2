<?php
class GruposPermiso extends SeguridadAppModel {

	var $name = 'GruposPermiso';
	var $primaryKey = 'permiso_id';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Grupo' => array('className' => 'Grupo',
								'foreignKey' => 'grupo_id',
								'conditions' => '',
								'fields' => '',
								'order' => ''
			),
			'Permiso' => array('className' => 'Permiso',
								'foreignKey' => 'permiso_id',
								'conditions' => '',
								'fields' => '',
								'order' => ''
			)
	);
	
	

}
?>