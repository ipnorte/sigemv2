<?php 

class ServiciosController extends MutualAppController{
	
	var $name = "Servicios";
	
	var $autorizar = array('index','add','edit','getServiciosActivos');
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}

	
}

?>