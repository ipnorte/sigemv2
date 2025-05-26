<?php 

class CajabancoAppController extends AppController{
	
	var $autorizar = array();
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	
	
}

?>