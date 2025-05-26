<?php
/**
* 	adrian
*	28/09/2010
*
*/



class TestService extends AppModel{
	
	var $name = 'TestService';
	var $useTable = false;
	
	/**
	 * Divide two numbers
	 *
	 * @param float $a
	 * @param float $b
	 * @return float
	 */
	function divide($a, $b){
		if ($b != 0) {
			return $a / $b;
		}
		return 0;
	}
	
	/**
	 * 
	 * @param int $id
	 * @return string
	 */
	function demoArray($id){
		App::import('Model', 'Config.GlobalDato');
		$oGLB = new GlobalDato();
		$datos = $oGLB->find('all');
		$demoSerialize = base64_encode(serialize($datos));
		return $demoSerialize;
	}
	

}

?>