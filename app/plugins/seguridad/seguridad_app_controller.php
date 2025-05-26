<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package seguridad
 * @subpackage controller
 */
class SeguridadAppController extends AppController {
	
	
	var $loginLayout = "login";
	
	function __construct(){
		parent::__construct();
	}
	
	function beforeFilter(){
		parent::beforeFilter();
	}
	
}
?>