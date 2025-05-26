<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::import('Vendor','sigem_service');

class IntranetService extends SIGEMService{
    
	var $name = 'IntranetService';
	var $useTable = false;   
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
         * Busqueda de persona por documento
         * @param String $ndoc
         * @return String persona
         */
        public function getPersonaByDocumento($ndoc){
            App::import('model','pfyj.Persona');
            $oPERSONA = new Persona();
            $persona = NULL;
            $persona = $oPERSONA->getByNdoc($ndoc);
            $persona = (!empty($persona) ? $persona['Persona'] : NULL);
            $this->setResponse('find',(!empty($persona) ? 1 : 0), FALSE);
            $this->setResponse('result',$persona, FALSE);
            return $this->getResponse();         
        }
        
        
    
}