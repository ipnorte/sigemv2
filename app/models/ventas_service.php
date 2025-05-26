<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
App::import('Vendor','sigem_service');
App::import('model','ventas.SolicitudService');
App::import('Model', 'pfyj.Persona');
App::import('Helper', 'Util');

class VentasService extends SIGEMService{
    
    
    var $useTable = false;
    var $name = 'VentasService'; 
    var $response = array(
            'error' => 0,
            'msg_error' => '',
            'find' => 0,
            'result' => array()
    );   
    
	/**
	 * Obtiene un token
	 * @param string $PIN
         * @return string
	 */        
        function getToken($PIN) {
            $this->validatePIN($PIN, false);
            return $this->getResponse();            
        }    
    
     /**
     * getPersona
     * @param String $cuit_cuil
     * @return String
     */
    function getPersona($cuit_cuil){
        $oUT = new UtilHelper();
        $oSSERVICE = new SolicitudService();
        $persona = array();
        if(!$oUT->validar_cuit($cuit_cuil)){
            $this->setResponse('error',1);
            $this->setResponse('msg_error','CUIT-CUIL INCORRECTO.');
            return $this->getResponse();
        }
        $persona = $oSSERVICE->get_persona_by_cuit($cuit_cuil);
        if(empty($persona)){
            $persona['Persona']['cuit_cuil'] = $cuit_cuil;
            $persona['Persona']['documento'] = substr($persona['Persona']['cuit_cuil'],2,8);
        }
        $this->setResponse('find',1);
        $this->setResponse('result',$persona['Persona']);
        return $this->getResponse();
    }



    
    
}

?>