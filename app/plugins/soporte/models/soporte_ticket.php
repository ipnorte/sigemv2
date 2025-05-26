<?php
/**
*	10/09/2010
*	adrian
*
*/

class SoporteTicket extends SoporteAppModel{
	
	var $name = 'SoporteTicket';
	
	var $tipos = array(
		'ERR' => 'ERROR EN LA APLICACION',
		'DAT' => 'CORRECCION DE DATOS',
		'MOD' => 'MODIFICACION FUNCIONAMIENTO',
		'ADD' => 'NUEVA FUNCIONALIDAD'
	);
	
	var $prioridades = array(
		'A' => 'ALTA',
		'M' => 'MEDIA',
		'B' => 'BAJA'
	);
	
	var $estados = array(
		'SOL' => 'SOLICITADO',
		'ASI' => 'ASIGNADO',
		'DEV' => 'EN DESARROLLO',
		'FIN' => 'FINALIZADO'
	);
	
	
	
}

?>