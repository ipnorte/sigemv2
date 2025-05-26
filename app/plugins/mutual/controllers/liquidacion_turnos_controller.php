<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class LiquidacionTurnosController extends MutualAppController{
    
    var $name = 'LiquidacionTurnos';
    var $autorizar = array(
        'index',
    );
    
	function beforeFilter(){
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();
	}    
    
    function index(){
        
        $empresas = $this->LiquidacionTurno->getEmpresaTurnos();
        
        $this->set('empresas',$empresas);
        
    }
    
}