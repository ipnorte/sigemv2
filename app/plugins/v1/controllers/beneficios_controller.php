<?php
class BeneficiosController extends V1AppController{

	var $name = 'Beneficios';
	var $autorizar = array('index','socio','view');

	function beforeFilter(){
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();
	}

	function socio($socio_id){
		$this->set('beneficios',$this->Beneficio->bySocio($socio_id));
		$this->render();
	}

	function view($id){
		$this->set('beneficio',$this->Beneficio->getBeneficio($id));
		$this->render();
	}



}
?>
