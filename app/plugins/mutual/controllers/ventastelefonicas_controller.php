<?php

/**
* MODULO DE VENTAS TELEFONICAS
* ventastelefonicas_controller.php
* 
* VIEWS = app/plugin/mutual/views/ventastelefonicas/
* MODEL = app/plugin/mutual/models/ventatelefonica.php
* 
* @author adrian [* 22/05/2012]
*/

class VentastelefonicasController extends MutualAppController{
	
	/**
	 * Referencia a modelos
	 * @var array
	 */
	var $uses = array('mutual.Ventatelefonica');
	
	/**
	 * METODOS PUBLICOS!!!
	 * Para definir metodos SEGUROS deben cargarse en la tabla permisos y habilitarlo al grupo respectivo. Para
	 * definir un metodo como publico debe cargarse en el array
	 * @var array
	 */
	var $autorizar = array('index');
	
	/**
	 * 
	* @override
	* adrian - 23/05/2012
	 * @see AppController::beforeFilter()
	 */
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	
	
	/**
	 * Pagina default
	 * 
	 * @author adrian [23/05/2012]
	 */
	function index($persona_id = null,$opcionMenu = 0){
		
		
		if($opcionMenu == 2 && !empty($this->data)){
			if($this->Ventatelefonica->altaNuevoAdicional($this->data)){
				$this->redirect("index/".$this->data['SocioAdicional']['persona_id']);
			}else{
				$this->Mensaje->errores("ERRORES: ",$this->Ventatelefonica->notificaciones);
			}			
		}		
		
		if($opcionMenu == 3 && !empty($this->data)){
			if($this->Ventatelefonica->altaNuevaSolicitud($this->data)){
				$this->redirect("index/".$this->data['MutualServicioSolicitud']['persona_id']);
			}else{
				$this->Mensaje->errores("ERRORES: ",$this->Ventatelefonica->notificaciones);
			}			
		}
		
		
		$this->set('persona_id',$persona_id);
		$this->set('persona',(!empty($persona_id) ? $this->Ventatelefonica->oPERSONA->getPersona($persona_id) : null));
		$this->set('opcionMenu',$opcionMenu);
		$fechaEmision = date('Y-m-d');
		$this->set('fechaEmision',$fechaEmision);
		$this->set('fechaCobertura',$this->Ventatelefonica->oSERVICIO->calculaFechaCobertura($fechaEmision));
		
		
		
		
		
	}
	

	
}

?>