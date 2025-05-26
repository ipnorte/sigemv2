<?php

require_once(dirname(__FILE__).'/xtcpdf.php');


/**
 * ListadoPDF
 * @author adrian
 *
 */

class GenericoPDF  extends XTCPDF{

	/**
	 * Construcctor
	 * @param $orientation
	 * @param $unit
	 * @param $format
	 * @param $unicode
	 * @param $encoding
	 * @param $diskcache
	 * @return unknown_type
	 */
	function GenericoPDF($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false){
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
	}
	
	/**
	 * redefino el metodo Footer para que no imprima el pie de pagina por defecto
	 */
	function Footer(){}
    
 
}
?>