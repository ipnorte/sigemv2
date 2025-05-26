<?php
class PersonaNovedadComentario extends PfyjAppModel{
	
	var $name = 'PersonaNovedadComentario';
    var $validate = array(
						'descripcion' => array( 
    										VALID_NOT_EMPTY => array('rule' => VALID_NOT_EMPTY,'message' => '(*)Requerido')
    									)
    					);		
	
	
}
?>