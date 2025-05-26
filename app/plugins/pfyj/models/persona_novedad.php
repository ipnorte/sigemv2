<?php
class PersonaNovedad extends PfyjAppModel{
	
	var $name = 'PersonaNovedad';
	
    var $hasMany = array(        
    						'PersonaNovedadComentario' => array(            
    											'className'  => 'PersonaNovedadComentario',            
    											'order'      => 'PersonaNovedadComentario.created DESC'        
    					));	
    var $validate = array(
						'descripcion' => array( 
    										VALID_NOT_EMPTY => array('rule' => VALID_NOT_EMPTY,'message' => '(*)Requerido')
    									)
    					);	    					
	
	
}
?>