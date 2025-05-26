<?php
class SolicitudEstadosController extends V1AppController{
	
	var $name = 'SolicitudEstados';
	var $autorizar = array();
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	
	
	function historial($nro_solicitud){
		$historial = $this->SolicitudEstado->getHistorial($nro_solicitud);
		$this->set('historial',$historial);
		$this->set('nro_solicitud',$nro_solicitud);
	}
	
}