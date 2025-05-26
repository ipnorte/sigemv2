<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of stopdebit_service
 *
 * @author adrian
 */

App::import('Model','Mutual.Liquidacion');
App::import('Model','Mutual.LiquidacionSocioRendicion');
App::import('Model','Mutual.LiquidacionSocioRendicionStop');
App::import('Model','Pfyj.PersonaBeneficio');
App::import('Model', 'Mutual.OrdenDescuento');

class StopdebitService extends MutualAppModel{
    //put your code here
    var $name = 'StopdebitService';
    var $useTable = false;  
    var $oLIQ;
    var $oLSR;
    var $oLSRS;
    var $oBEN;
    var $oORD;
    
    public function __construct() {
        parent::__construct();
        $this->oLIQ = new Liquidacion(); 
        $this->oLSR = new LiquidacionSocioRendicion();
        $this->oLSRS = new LiquidacionSocioRendicionStop();
        $this->oBEN = new PersonaBeneficio();
        $this->oORD = new OrdenDescuento();
    }

    public function cargarLiquidacion($id){
        return $this->oLIQ->cargar($id);
    }
    
    public function getBancosStopList($id){        
        return $this->oLSR->getBancoIntercambiosStops($id);
    }
    
    public function getSociosStops($liquidacionId,$bancoId){
        return $this->oLSR->getSociosStop($liquidacionId,$bancoId);
    }
    
    public function cambiarDeOrganismo($formData){
        
        $codigoOrganismo = $formData['LiquidacionSocioRendicion']['codigo_organismo'];
        $liquidacionId = $formData['LiquidacionSocioRendicion']['liquidacion_id'];
        $bancoId = $formData['LiquidacionSocioRendicion']['banco_intercambio'];
        $codBaja = $formData['LiquidacionSocioRendicion']['codigo_baja'];
        $periodo = $formData['LiquidacionSocioRendicion']['periodo'];
        
        $socios = $formData['LiquidacionSocioRendicion']['socios'];
        
        $this->oLSRS->begin();
        
        if(!empty($socios)){
            
            foreach($socios as $socioId){
                
                $stop = array(
                    'LiquidacionSocioRendicionStop' => array(
                        'id' => 0,
                        'liquidacion_id' => $liquidacionId,
                        'banco_id' => $bancoId,
                        'socio_id' => $socioId,
                        'nuevo_organismo' => $codigoOrganismo,
                    )
                );
                if(!$this->oLSRS->save($stop)){
                    parent::notificar("Error al grabar LiquidacionSocioRendicionStop");
                    $this->oLSRS->rollback();
                    return FALSE;
                }
                
                #cargar los beneficios que involucra
                $sql = "select LiquidacionCuota.persona_beneficio_id 
                        from liquidacion_cuotas LiquidacionCuota
                        where LiquidacionCuota.liquidacion_id = $liquidacionId
                        and LiquidacionCuota.socio_id = $socioId
                        group by LiquidacionCuota.persona_beneficio_id;";
                $datos = $this->oLSRS->query($sql);
                
                if(!empty($datos)){
                    
                    foreach($datos as $dato){
                        
                        $beneficioId = $dato['LiquidacionCuota']['persona_beneficio_id'];
                        $nuevoBeneficio = $beneficio = $this->oBEN->read(NULL,$beneficioId);

                        $nuevoBeneficio['PersonaBeneficio']['id'] = 0;
                        $nuevoBeneficio['PersonaBeneficio']['codigo_beneficio'] = $codigoOrganismo;
                        $nuevoBeneficio['PersonaBeneficio']['activo'] = 1;
                        $nuevoBeneficio['PersonaBeneficio']['codigo_baja'] = NULL;
                        $nuevoBeneficio['PersonaBeneficio']['fecha_baja'] = NULL;
                        $nuevoBeneficio['PersonaBeneficio']['accion'] = NULL;
                        $nuevoBeneficio['PersonaBeneficio']['reasignado_id'] = NULL;
                        
                        
                        if(!$this->oBEN->save($nuevoBeneficio)){
                            $this->oLSRS->rollback();
                            parent::notificar("Error al grabar nuevo beneficio");
                            return FALSE;
                        }
                        
//                        $nuevoBeneficioID = $this->oBEN->getLastInsertId();
                        
                        $beneficio['PersonaBeneficio']['activo'] = 0;
                        $beneficio['PersonaBeneficio']['codigo_baja'] = $codBaja;
                        $beneficio['PersonaBeneficio']['fecha_baja'] = date('Y-m-d');
                        $beneficio['PersonaBeneficio']['accion'] = 'R';
                        $beneficio['PersonaBeneficio']['reasignado_id'] = $this->oBEN->getLastInsertId();
                        
                        if(!$this->oBEN->save($beneficio,FALSE)){
                            $this->oLSRS->rollback();
                            parent::notificar("Error al dar de baja al beneficio actual");
                            return FALSE;
                        }                        
                        
                        #cargar las ordenes de descuentos asociadas al beneficio
                        $sql = "select LiquidacionCuota.orden_descuento_id 
                                from liquidacion_cuotas LiquidacionCuota
                                where LiquidacionCuota.liquidacion_id = $liquidacionId
                                and LiquidacionCuota.socio_id = $socioId
                                and LiquidacionCuota.persona_beneficio_id = $beneficioId
                                group by LiquidacionCuota.orden_descuento_id;";
                        $ordenes = $this->oLSRS->query($sql);
                        if(!empty($ordenes)){
                            
                            foreach($ordenes as $orden){
                                
                                $ordenId = $orden['LiquidacionCuota']['orden_descuento_id'];
                                
                                if(!$this->oORD->novarOrden($ordenId, $beneficio['PersonaBeneficio']['reasignado_id'],'*** STOP DEBIT ***',$periodo)){
                                    parent::notificar("Error al novar la orden de descuento $ordenId");
                                    $this->oLSRS->rollback();
                                    return FALSE;                                    
                                }
                                
                            }
                            
                        }
                        
                    }
                }
                
            }
////            $this->oLSRS->rollback();
//            exit;
            return $this->oLSRS->commit();
            
        }
        
        
        
    }
    
    
}
