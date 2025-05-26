<?php
class AsincronoError extends ShellsAppModel{
	
	var $name = 'AsincronoError';
	var $auditable = false;
	
	function getErroresByAsincronoId($id){
		$errores = $this->find('all',array('conditions' => array('AsincronoError.asincrono_id' => $id)));
		return $errores;
	}
}
?>