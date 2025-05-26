<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage controller
 *
 */
class MutualAppController extends AppController{
	
	var $components = array('Zip');	
	
	var $tiposOrdenDto = array(
		'CMUTU' => 'CMUTU - CARGOS MUTUAL',
		'EXPTE' => 'EXPTE - EXPEDIENTE',
		'OCOMP' => 'OCOMP - ORDEN DE COMPRA / SERVICIO',
	);	
	
	function __construct(){
		parent::__construct();
	}
	
    
	function leerArchivo($path){
		if(!file_exists($path)) return false;
		$registros = array();
		$registros = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if(!is_array($registros)) return null;
		foreach ($registros as $i => $registro) {
		    // $registros[$i] = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $registro);
                    $registros[$i] = preg_replace("[^A-Za-z0-9]", "",$registro);
		}
		return $registros;		
	} 
    
}
?>