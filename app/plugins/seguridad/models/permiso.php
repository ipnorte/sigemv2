<?php
class Permiso extends SeguridadAppModel {

	var $name = 'Permiso';
	var $recursive = 0;

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $hasAndBelongsToMany = array(
			'Grupo' => array('className' => 'Grupo',
						'joinTable' => 'grupos_permisos',
						'foreignKey' => 'permiso_id',
						'associationForeignKey' => 'grupo_id',
						'unique' => true,
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'limit' => '',
						'offset' => '',
						'finderQuery' => '',
						'deleteQuery' => '',
						'insertQuery' => ''
			)
	);

}
?>