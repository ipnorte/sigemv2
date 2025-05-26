<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class StopDebitController extends MutualAppController{
    
    var $name = 'StopDebit';
    var $uses = array('Mutual.StopdebitService');
    var $autorizar = array(
        'index'
    );
    
    
    public function __construct() {
        parent::__construct();
    }
    
    public function beforeFilter() {
        $this->Seguridad->allow($this->autorizar);
        parent::beforeFilter();
    }
    
    public function index($liquidacionId = NULL){
        
        if(empty($liquidacionId)){parent::noDisponible();}
        $this->set('liquidacion',$this->StopdebitService->cargarLiquidacion($liquidacionId));
        $this->set('bancoIntercambios',$this->StopdebitService->getBancosStopList($liquidacionId));
        $socios = $bancoIntercambio = NULL;
        if(!empty($this->data)){
                        
            $accion = $this->data['LiquidacionSocioRendicion']['accion'];
            if($accion === 'PROCESAR'){
                if(!$this->StopdebitService->cambiarDeOrganismo($this->data)){
                    $this->Mensaje->errores("ERRORES:",$this->StopdebitService->notificaciones);
                }
            }
            
//            if($accion === 'FILTRAR'){
                $liquidacionId = $this->data['LiquidacionSocioRendicion']['liquidacion_id'];
                $bancoIntercambio = $this->data['LiquidacionSocioRendicion']['banco_intercambio'];
                $socios = $this->StopdebitService->getSociosStops($liquidacionId,$bancoIntercambio);
//            }
            

            
        }
        $this->set('socios',$socios);
        $this->set('bancoIntercambio',$bancoIntercambio);
        
    }
    
    
    
}