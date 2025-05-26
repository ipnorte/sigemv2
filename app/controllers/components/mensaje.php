<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package general
 * @subpackage components
 */

class MensajeComponent extends Object {
	
	/**
	 * Seteo el layout que uso para el mensaje de error
	 * @var unknown_type
	 */
	var $layout = "messages";

    function startup(&$controller){
		$this->controller =& $controller;
		$this->modelClass = $this->controller->modelClass;
//		$this->controller->Session->delete('Message.NOTICE');
//		$this->controller->Session->delete('Message.ERROR');
//		$this->controller->Session->delete('Message.OK');	
//		$this->controller->Session->delete('Message.ERRORES');	
    }

    /**
     * okGuardar()
     * Mensaje enviado al usuario cuando los datos se graban correctamente
     * @return unknown_type
     */
    function okGuardar(){
    	$this->controller->Session->setFlash("Datos Grabados Correctamente!",$this->layout,array(),'OK');
    }
    
    /**
     * okBorrar()
     * Mensaje enviado al usuario cuando se borra correctamente
     * @return unknown_type
     */
    function okBorrar(){
    	$this->controller->Session->setFlash("Datos Borrados Correctamente!",$this->layout,array(),'OK');
    }    
      
    /**
     * errorGuardar()
     * Mensaje enviado al usuario cuando se produce un error al guardar
     * @return unknown_type
     */
    function errorGuardar(){
    	$this->controller->Session->setFlash("Se produjo un error al guardar",$this->layout,array(),'ERROR');
    }
    
    /**
     * errorBorrar()
     * Mensaje enviado al usuario cuando se produce un error al borrar
     * @return unknown_type
     */
    function errorBorrar(){
    	$this->controller->Session->setFlash("Se produjo un error al borrar",$this->layout,array(),'ERROR');
    }
    
	/**
	 * error
	 * Envia mensajes de error al usuario
	 * @param string $msg
	 */
	function error($msg){
		$msg = $this->controller->Util->parse2HTML($msg);
		$this->controller->Session->setFlash($msg,$this->layout,array(),'ERROR');
	}

	function errores($titulo,$msgs){
		if(empty($msgs)) return null;
		$items = array();
		foreach($msgs as $msg){
			$msg = $this->controller->Util->parse2HTML($msg);
			array_push($items,$msg);
		}
		$this->controller->set('titulo',$titulo);
		$this->controller->set('errores',$items);
		$this->controller->Session->setFlash($items,$this->layout,array(),'ERRORES');
	}
	
    
	/**
	 * notice
	 * envia mensajes comunes al usuario
	 * @param string $msg
	 */
	function notice($msg){
		$msg = $this->controller->Util->parse2HTML($msg);
		$this->controller->Session->setFlash($msg,$this->layout,array(),'NOTICE');
	}
	
	/**
	 * ok
	 * Mensaje OK al usuario
	 * @param unknown_type $msg
	 */
	function ok($msg){
		$msg = $this->controller->Util->parse2HTML($msg);
		$this->controller->Session->setFlash($msg,$this->layout,array(),'OK');
	}    

	

    
}
?>