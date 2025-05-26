<?php
class SolicitudCuponesAnsesController extends V1AppController{
	
	var $name = 'SolicitudCuponesAnses';
	var $autorizar = array();
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	
	
	function by_solicitud($nro_solicitud){
		App::import('Model', 'V1.SolicitudCuponesAnses');
		$this->SolicitudCuponesAnses = new SolicitudCuponesAnses();
		$cupones = $this->SolicitudCuponesAnses->cuponesBySolicitud($nro_solicitud);
		$this->set('cupones',$cupones);
		$this->set('nro_solicitud',$nro_solicitud);		
	}
	
}
?>