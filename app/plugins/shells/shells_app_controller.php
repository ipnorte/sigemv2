<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @package controller
 */
class ShellsAppController extends AppController{
	
	var $pathToResponser = "";
	var $refreshPogressBar = 1;
	var $layout = "asincronos";
	
	function __construct(){
		$this->pathToResponser = "/".Configure::read('APLICACION.folder_install')."/responser_asincrono.php";
		parent::__construct();
	}
		
}
?>