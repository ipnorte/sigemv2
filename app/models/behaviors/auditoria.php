<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package general
 * @subpackage behaviors
 */
class AuditoriaBehavior extends ModelBehavior{
	
	var $model;
	
	/**
	 * (non-PHPdoc)
	 * @see cake/libs/model/ModelBehavior#setup($model, $config)
	 */
	function setup($model, $config = array()) {
		$this->model = $model;
	}
	
	
	function grabarAuditoria($data){
		debug(debug_backtrace());
	}
	
}
?>