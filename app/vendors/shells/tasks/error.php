<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-task
 */
class ErrorTask extends Shell{
	
	var $pid;

	function execute(){}
	
	function limpiarTabla(){
		App::import('Model','Shells.AsincronoError');
		$oERROR = new AsincronoError();
		return $oERROR->deleteAll("AsincronoError.asincrono_id = " . $this->pid,true);
	}
	
	function grabarError($error){
		$error['asincrono_id'] = $this->pid;
		$error = array('AsincronoError' => $error);
		App::import('Model','Shells.AsincronoError');
		$oERROR = new AsincronoError();
		return $oERROR->save($error);
	}
	
}
?>