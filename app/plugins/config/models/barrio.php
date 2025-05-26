<?php
class Barrio extends ConfigAppModel{
	var $name = 'Barrio';
	
	
    var $validate = array(
						'nombre' => array( 
    										VALID_NOT_EMPTY => array('rule' => VALID_NOT_EMPTY,'message' => '(*)Requerido')
    									),
						'nombre' => array( 
    										VALID_NOT_EMPTY => array('rule' => VALID_NOT_EMPTY,'message' => '(*)Requerido')
    									)    										
    					); 	
	
    var $belongsTo = array(
        'Localidad' => array(
            'className'    => 'Localidad',
            'foreignKey'    => 'localidad_id'
        )
    ); 	
}
?>