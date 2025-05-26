<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package v1
 * @subpackage controller
 */
class V1AppController extends AppController{
	
//	var $components = array('Crypter');
	
	function __construct(){
		parent::__construct();
	}
	
	function beforeRender(){
		App::import('Model', 'V1.SolicitudCuponesAnses');
		$this->SolicitudCuponesAnses = new SolicitudCuponesAnses();		
		$db = & ConnectionManager::getDataSource($this->SolicitudCuponesAnses->useDbConfig);
		$conexion['host'] = $db->config['host'];
		$conexion['login'] = $db->config['login'];
		$conexion['password'] = $db->config['password'];
		$conexion['database'] = $db->config['database'];
		$this->set('conexion',base64_encode(serialize($conexion)));		
		$this->set('pathToViewCuponAnses',Configure::read('APLICACION.folder_install')."/cupon_anses.php");
		parent::beforeRender();	
	}
	
}
?>