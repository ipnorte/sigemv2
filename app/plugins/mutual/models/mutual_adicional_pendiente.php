<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */

class MutualAdicionalPendiente extends MutualAppModel{
	
	var $name = 'MutualAdicionalPendiente';
	
	/**
	 * devuelve los adicionales generados para un socio y periodo determinado
	 * @param unknown_type $socio_id
	 * @param unknown_type $periodo
	 * @return unknown
	 */
	function bySocioByPeriodo($socio_id,$periodo){
		$adicionales = $this->find('all',array('conditions'=>array('MutualAdicionalPendiente.socio_id' => $socio_id,'MutualAdicionalPendiente.periodo' => $periodo)));
		return $adicionales;
	}
	
	function borrarBySocioByPeriodo($socio_id,$periodo){
		$this->bindModel(array('belongsTo' => array('Liquidacion')));
		return $this->deleteAll("Liquidacion.periodo = $periodo and MutualAdicionalPendiente.socio_id = $socio_id");		
	}
	
	function borrarCuotasDevengadasBySocioByLiquidacionId($socio_id,$liquidacion_id){
		$conditions = array();
		$conditions['MutualAdicionalPendiente.liquidacion_id'] = $liquidacion_id;
		$conditions['MutualAdicionalPendiente.socio_id'] = $socio_id;
		$conditions['MutualAdicionalPendiente.orden_descuento_cuota_id <> '] = 0;
		$adicionales = $this->find('all',array('conditions' => $conditions));
		if(!empty($adicionales)):
			App::import('Model','Mutual.OrdenDescuentoCuota');
			$oCUOTA = new OrdenDescuentoCuota();		
			foreach($adicionales as $adicional):
                $adicional['MutualAdicionalPendiente']['orden_descuento_cuota_id'] = null;
                parent::save($adicional);
				$borrada = $oCUOTA->borrarCuota($adicional['MutualAdicionalPendiente']['orden_descuento_cuota_id']);
			endforeach;
		endif;
	}
	
        
    function calcular_adicional(){
        
    }    
    
    function generarAdicionalNoImputada($liquidacion_id,$socio_id,$codigoOrganismo,$periodo,$situacionDeuda = 'MUTUSICUMUTU',$pre_imputacion=false,$drop=TRUE){
        
        $ERROR = FALSE;
        
        if($drop){
            if(!$this->deleteAll("MutualAdicionalPendiente.liquidacion_id = $liquidacion_id AND MutualAdicionalPendiente.socio_id = $socio_id")){
                parent::notificar("ERROR AL BORRAR LOS ADICIONALES PENDIENTES DEL SOCIO #$socio_id");
                return FALSE;
            }
        }
        
        $adicionales = array();
        
        
		App::import('Model','Mutual.MutualAdicional');
		$oADICIONAL = new MutualAdicional();
        
        $parametros = $oADICIONAL->getActivosByOrganismo($codigoOrganismo,$periodo,true);
//        debug($parametros);
        
		if(empty($parametros)) return TRUE;
		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();
        
		App::import('Model','pfyj.Socio');
		$oSOCIO = new Socio(); 
        
		App::import('Model','Mutual.LiquidacionCuotaNoimputada');
		$oLC = new LiquidacionCuotaNoimputada();        
        
        foreach($parametros as $adicional){
            
//            debug($adicional);
            
            $proveedor = $adicional['MutualAdicional']['proveedor_id'];
//            $proveedor = 18;
            $proveedor_imputa = $adicional['MutualAdicional']['imputar_proveedor_id'];
            
            $punitorio = 0;
            if($adicional['MutualAdicional']['deuda_calcula'] == 5){
                $punitorio = $adicional['MutualAdicional']['valor'] / 100;
            }
            

            
            
            $saldos = array();
            $deudaCalcula = $adicional['MutualAdicional']['deuda_calcula'];
            $impoDeuda = 0;
            $adps = array();
//            $deudaCalcula = 3;
            switch ($deudaCalcula){
//                    case 1:
//                            //toda la deuda incluida el periodo liquidado
//                            $saldos = Set::extract("/OrdenDescuentoCuota/saldo_calculado",$cuotas);
//                            $impoDeuda = array_sum($saldos);
//                            break;
                    case 2:
                        //deuda anterior al periodo liquidado
                        $cuotas = $oCUOTA->cuotasAdeudadasBySocioAlPeriodoByOrganismo($socio_id,$periodo,$codigoOrganismo,$situacionDeuda,$pre_imputacion,FALSE,NULL,0,$punitorio);
                        if(!empty($proveedor))$cuotas = Set::extract("/OrdenDescuentoCuota[proveedor_id=".$proveedor."]",$cuotas);
                        $cuotas = Set::extract("/OrdenDescuentoCuota",$cuotas);                                    
                        $cuotas = Set::extract("/OrdenDescuentoCuota[periodo<".$periodo."]",$cuotas);
                        $saldos = Set::extract("/OrdenDescuentoCuota/saldo_calculado",$cuotas);
                        $impoDeuda = array_sum($saldos);
                            break;
                    case 3:
                        //deuda del periodo liquidado
                        $cuotas = $oCUOTA->cuotasAdeudadasBySocioAlPeriodoByOrganismo($socio_id,$periodo,$codigoOrganismo,$situacionDeuda,$pre_imputacion,FALSE,NULL,0,$punitorio);
                        if(!empty($proveedor))$cuotas = Set::extract("/OrdenDescuentoCuota[proveedor_id=".$proveedor."]",$cuotas);
                        $cuotas = Set::extract("/OrdenDescuentoCuota",$cuotas);                                    
                        $cuotas = Set::extract("/OrdenDescuentoCuota[periodo=".$periodo."]",$cuotas);
                        $saldos = Set::extract("/OrdenDescuentoCuota/saldo_calculado",$cuotas);
                        $impoDeuda = array_sum($saldos);
                        break;
                    case 4:
                        // SOBRE CUOTA SOCIAL
                        $codigo = 'MUTUCUOS' . substr($codigoOrganismo,8,4);
                        $impoDeuda = parent::GlobalDato('decimal_1', $codigo);
//                                    array_push($saldos, $impoCuoSoc);
                        break;

                    case 5:
                        #######################################################################
                        # PUNITORIOS
                        #######################################################################
                        $procPun = $adicional['MutualAdicional']['valor'] / 100;
//                        $cuotas = $oCUOTA->procesa_deuda($socio_id,$periodo,$periodo,TRUE,$proveedor,$codigoOrganismo,FALSE,TRUE,NULL,NULL,$procPun);
                        $cuotas = $oCUOTA->cuotasAdeudadasBySocioAlPeriodoByOrganismo($socio_id,$periodo,$codigoOrganismo,$situacionDeuda,$pre_imputacion,NULL,2,$procPun,true);
                        $cuotas = Set::extract("/OrdenDescuentoCuota",$cuotas); 
//                        debug($cuotas);
//                                                                       
                        if(!empty($cuotas)){

                            $adps = array();

                            foreach($cuotas as $cuota){

//                                debug($cuota);

                                $tmp = array();
                                $tmp['MutualAdicionalPendiente'] = array(
                                    'id' => 0,
                                    'liquidacion_id' => $liquidacion_id,
                                    'socio_id' => $socio_id,
                                    'codigo_organismo' => $codigoOrganismo,
                                    'proveedor_id' => (empty($adicional['MutualAdicional']['imputar_proveedor_id']) ? $cuota['OrdenDescuentoCuota']['proveedor_id'] : $adicional['MutualAdicional']['imputar_proveedor_id']),
                                    'imputar_proveedor_id' => (empty($adicional['MutualAdicional']['imputar_proveedor_id']) ? $cuota['OrdenDescuentoCuota']['proveedor_id'] : $adicional['MutualAdicional']['imputar_proveedor_id']),
                                    'tipo' => $adicional['MutualAdicional']['tipo'],
                                    'deuda_calcula' => $adicional['MutualAdicional']['deuda_calcula'],
                                    'valor' => $adicional['MutualAdicional']['valor'],
                                    'tipo_cuota' => $adicional['MutualAdicional']['tipo_cuota'],
                                    'periodo' => $periodo,	
                                    'total_deuda' => $cuota['OrdenDescuentoCuota']['saldo_calculado'],
                                    'importe' => $cuota['OrdenDescuentoCuota']['punitorios'],
                                    'orden_descuento_id' => $cuota['OrdenDescuentoCuota']['orden_descuento_id'],
                                    'persona_beneficio_id' =>$cuota['OrdenDescuentoCuota']['persona_beneficio_id'], 

                                );
                                $tmp['OrdenDescuentoCuota'] = $cuota['OrdenDescuentoCuota'];
                                if (!empty($cuota['OrdenDescuentoCuota']['punitorios'])) {
                                    array_push($adps, $tmp);
                                }
                            }


                        }                                        

                        
                        break;

                    default:
                        //toda la deuda incluida el periodo liquidado
                        $cuotas = $oCUOTA->cuotasAdeudadasBySocioAlPeriodoByOrganismo($socio_id,$periodo,$codigoOrganismo,$situacionDeuda,$pre_imputacion,FALSE,NULL,0,$punitorio);
                        $saldos = Set::extract("/OrdenDescuentoCuota/saldo_calculado",$cuotas);
                        $impoDeuda = array_sum($saldos);
                        break;
            }            
            
            
            switch ($adicional['MutualAdicional']['tipo']){
                case 'P':
                    
                    if($adicional['MutualAdicional']['deuda_calcula'] != 5){
                        $importeGenera = $impoDeuda * $adicional['MutualAdicional']['valor'] / 100;
                    }else{
                        $importeGenera = $punitorio;
                    }                    
                    break;
                case 'I':
                    $importeGenera = $adicional['MutualAdicional']['valor'];
                    
                    break;
                default:
                    $importeGenera = 0;
                    break;
            }            
            
            $importeGenera = round($importeGenera,2);            
            
            
            ##############################################################################           
            # GENERAR EL CALCULO DE LOS PUNITORIOS
            # genero los adicionales pendientes por orden de descuento y salgo del metodo            
            ##############################################################################           
            if($adicional['MutualAdicional']['deuda_calcula'] == 5 && $adicional['MutualAdicional']['devengado_previo'] == 0 && $adicional['MutualAdicional']['activo'] == 1 && !empty($adps)){
                
//                return TRUE;
                
                foreach($adps as $adp){
                    
//                    debug($adp);
                    parent::begin();
                    if($this->save(array('MutualAdicionalPendiente' => $adp['MutualAdicionalPendiente']))){
                        $adp['MutualAdicionalPendiente']['id'] = $this->getLastInsertID();
                        $liqCuo = array('LiquidacionCuotaNoimputada' => array(
                                    'id' => 0,
                                    'liquidacion_id' => $liquidacion_id,
                                    'socio_id' => $socio_id,
                                    'persona_beneficio_id' => $adp['MutualAdicionalPendiente']['persona_beneficio_id'],
                                    'orden_descuento_id' => $adp['MutualAdicionalPendiente']['orden_descuento_id'],
                                    'orden_descuento_cuota_id' => null,
                                    'tipo_orden_dto' => $adp['OrdenDescuentoCuota']['tipo_orden_dto'],
                                    'tipo_producto' => $adp['OrdenDescuentoCuota']['tipo_producto'],
                                    'tipo_cuota' => $adicional['MutualAdicional']['tipo_cuota'],
                                    'periodo_cuota' => $periodo,
                                    'proveedor_id' => $adp['MutualAdicionalPendiente']['proveedor_id'],
                                    'vencida' => 0,
                                    'importe' => $adp['MutualAdicionalPendiente']['importe'],
                                    'saldo_actual' => $adp['MutualAdicionalPendiente']['importe'],
                                    'codigo_organismo' => $codigoOrganismo,
                                    'mutual_adicional_pendiente_id' => $adp['MutualAdicionalPendiente']['id']
                            )); 
//                            debug($liqCuo);
                        if(!$oLC->save($liqCuo)){
                            parent::rollback();
                            $this->notificaciones = "ERROR al crear la liquidacionCuotas de los punitorios";
                            return FALSE;
                        }
                    }else{
                        
                        parent::rollback();
                        $this->notificaciones = "ERROR al crear el adicional de los punitorios";
                        return FALSE;
                        
                    }
                    
                    parent::commit();
                    $importeGenera = 0;
//                    parent::rollback();
                    
                    
                }

            }
            #############################################################################################
            # FIN DE GENERACION DE LOS PUNITORIOS
            #############################################################################################
            
                        

//            debug($importeGenera);
//            debug($punitorios);
            $adicionalPendiente = array();
            
            if($importeGenera > 0){
                
                $orden = $oSOCIO->getOrdenDtoCuotaSocial($socio_id);
                $beneficios = Set::extract("/OrdenDescuentoCuota/persona_beneficio_id",$cuotas);
                asort($beneficios);
                $beneficio_id = array_pop($beneficios);
                
                if(empty($orden) && !empty($beneficio_id)){
                    // no tiene orden para cargos mutual
                    $orden = array();
                    $orden['OrdenDescuento'] = array(
                            'id' => 0,
                            'fecha' => date('Y-m-d'),
                            'tipo_orden_dto' => parent::GlobalDato('concepto_3',"MUTUPROD0003"),
                            'numero' => $socio_id,
                            'tipo_producto' => 'MUTUPROD0003',
                            'socio_id' => $socio_id,
                            'persona_beneficio_id' => $beneficio_id,
                            'proveedor_id' => parent::GlobalDato('entero_1',"MUTUPROD0003"),
                            'mutual_producto_id' => 0,
                            'periodo_ini' => $periodo,
                            'primer_vto_socio' => date('Y-m-d'),
                            'primer_vto_proveedor' => date('Y-m-d'),
                            'importe_cuota' => $oSOCIO->getImpoCuotaSocial($socio_id),
                            'cuotas' => 1,
                            'permanente' => 1,
                            'activo' => 1,
                    );
                    App::import('Model', 'Mutual.OrdenDescuento');
                    $oORDDTO = new OrdenDescuento(null); 
                    if($oORDDTO->save($orden)){
                        $orden['OrdenDescuento']['id'] = $oORDDTO->getLastInsertID();
                    }
                    
                }
                
                $adicionalPendiente = array();
                
                if(!empty($orden['OrdenDescuento']['id']) && !empty($beneficio_id)){
                    $adicionalPendiente = array('MutualAdicionalPendiente' => array(
                         'id' => 0,
                         'liquidacion_id' => $liquidacion_id,
                         'socio_id' => $socio_id,
                         'codigo_organismo' => $codigoOrganismo,
                         'proveedor_id' => $proveedor,
                         'imputar_proveedor_id' => $proveedor_imputa,
                         'tipo' => $adicional['MutualAdicional']['tipo'],
                         'deuda_calcula' => $adicional['MutualAdicional']['deuda_calcula'],
                         'valor' => $adicional['MutualAdicional']['valor'],
                         'tipo_cuota' => $adicional['MutualAdicional']['tipo_cuota'],
                         'periodo' => $periodo,	
                         'total_deuda' => $impoDeuda,
                         'importe' => $importeGenera,
                         'orden_descuento_id' => $orden['OrdenDescuento']['id'],
                         'persona_beneficio_id' => $beneficio_id, 	
                     ));                     
                }
                
                
//                debug($orden['OrdenDescuento']['id'] ." - ".$beneficio_id." - ".$adicional['MutualAdicional']['devengado_previo']);
//                debug($adicionalPendiente);
                
                if($adicional['MutualAdicional']['devengado_previo'] == 0 && !empty($adicionalPendiente)){
                    
                    if($adicional['MutualAdicional']['activo'] == 1){
                        
                        ###########################################################################
                        #SACO ESTA OPCION DE BORRADO REDUNDANTE
                        #PORQUE CUANDO LIQUIDA PRIMERO BORRA TODO
                        #NO TIENE SENTIDO
                        #Adrian 29/09/2016
                        ############################################################################
//                        $sql = "delete from liquidacion_cuotas where liquidacion_id = $liquidacion_id and socio_id = $socio_id and ifnull(mutual_adicional_pendiente_id,0) <> 0";
//                        $oLC->query($sql);
//                        $sql = "delete from mutual_adicional_pendientes where liquidacion_id = $liquidacion_id and socio_id = $socio_id;";
//                        $this->query($sql);
                        
                        
                            if($this->save($adicionalPendiente)){
                                $adicionalPendiente['MutualAdicionalPendiente']['id'] = $this->getLastInsertID();
                                $liqCuo = array('LiquidacionCuotaNoimputada' => array(
                                            'id' => 0,
                                            'liquidacion_id' => $liquidacion_id,
                                            'socio_id' => $socio_id,
                                            'persona_beneficio_id' => $beneficio_id,
                                            'orden_descuento_id' => $orden['OrdenDescuento']['id'],
                                            'orden_descuento_cuota_id' => null,
                                            'tipo_orden_dto' => $orden['OrdenDescuento']['tipo_orden_dto'],
                                            'tipo_producto' => $orden['OrdenDescuento']['tipo_producto'],
                                            'tipo_cuota' => $adicional['MutualAdicional']['tipo_cuota'],
                                            'periodo_cuota' => $periodo,
                                            'proveedor_id' => $proveedor_imputa,
                                            'vencida' => 0,
                                            'importe' => $importeGenera,
                                            'saldo_actual' => $importeGenera,
                                            'codigo_organismo' => $codigoOrganismo,
                                            'mutual_adicional_pendiente_id' => $adicionalPendiente['MutualAdicionalPendiente']['id']
                                    )); 
            //                        debug($liqCuo);
                                $oLC->save($liqCuo); 
                            }    
                        }
                    
                    
                }else if(!empty($orden['OrdenDescuento']['id']) && !empty($beneficio_id) && $adicional['MutualAdicional']['devengado_previo'] == 1){
                    
                    
                    
                    $sql = "select OrdenDescuentoCuota.id from orden_descuento_cuotas OrdenDescuentoCuota
                            where OrdenDescuentoCuota.socio_id = $socio_id
                            and OrdenDescuentoCuota.orden_descuento_id = ".$orden['OrdenDescuento']['id']."
                            and OrdenDescuentoCuota.persona_beneficio_id = $beneficio_id
                            and OrdenDescuentoCuota.proveedor_id = $proveedor_imputa
                            and OrdenDescuentoCuota.periodo = '$periodo'
                            and OrdenDescuentoCuota.tipo_cuota = '".$adicional['MutualAdicional']['tipo_cuota']."'
                            and OrdenDescuentoCuota.id not in (select orden_descuento_cuota_id from
                            orden_descuento_cobro_cuotas cocu inner join orden_descuento_cobros co
                            on (co.id = cocu.orden_descuento_cobro_id) where co.socio_id = $socio_id)";
                    
                    $datos = $oCUOTA->query($sql);
                    if(!empty($datos)){
                        $cuotasExistentes = Set::extract("/OrdenDescuentoCuota/id",$datos);

                        $sql = "DELETE FROM liquidacion_cuota_noimputadas where liquidacion_id = $liquidacion_id and socio_id = $socio_id and orden_descuento_cuota_id in (".  implode(',', $cuotasExistentes).");";
                        $oLC->begin();
                        
                        if(!$oLC->query($sql)){
                            $oLC->rollback();
                            $this->notificaciones = "ERROR MutualAdicionalPendiente LIN#256";
                            return false;
                        }
                        $oLC->commit();
                        
                        $sql = "DELETE FROM orden_descuento_cuotas where id in (".  implode(',', $cuotasExistentes).");";
                        $oCUOTA->begin();
                        if(!$oCUOTA->query($sql)){
                            $oCUOTA->rollback();
                            $this->notificaciones = "ERROR MutualAdicionalPendiente LIN#265";
                            return false;
                        }
                        $oCUOTA->commit();
                        
                    }
                    
                    $GENERAR_CUOTA = TRUE;
                    $sql = "select OrdenDescuentoCuota.id from orden_descuento_cuotas OrdenDescuentoCuota
                            where OrdenDescuentoCuota.socio_id = $socio_id
                            and OrdenDescuentoCuota.orden_descuento_id = ".$orden['OrdenDescuento']['id']."
                            and OrdenDescuentoCuota.persona_beneficio_id = $beneficio_id
                            and OrdenDescuentoCuota.proveedor_id = $proveedor_imputa
                            and OrdenDescuentoCuota.periodo = '$periodo'
                            and OrdenDescuentoCuota.tipo_cuota = '".$adicional['MutualAdicional']['tipo_cuota']."'
                            and OrdenDescuentoCuota.id in (select orden_descuento_cuota_id from
                            orden_descuento_cobro_cuotas cocu inner join orden_descuento_cobros co
                            on (co.id = cocu.orden_descuento_cobro_id) where co.socio_id = $socio_id)";
                    $datos = $oCUOTA->query($sql); 
                    if(!empty($datos)) $GENERAR_CUOTA = FALSE;                    
                    
//                    debug($cuotasExistentes);
                    
                    $cuotaAdicional = array('OrdenDescuentoCuota' => array(
                                                    'id' => 0,
                                                    'orden_descuento_id' => $orden['OrdenDescuento']['id'],
                                                    'persona_beneficio_id' => $beneficio_id,
                                                    'socio_id' => $socio_id,
                                                    'tipo_orden_dto' => $orden['OrdenDescuento']['tipo_orden_dto'],
                                                    'tipo_producto' => $orden['OrdenDescuento']['tipo_producto'],
                                                    'periodo' => $periodo,
                                                    'nro_cuota' => 0,
                                                    'tipo_cuota' => $adicional['MutualAdicional']['tipo_cuota'],
                                                    'estado' => 'A',
                                                    'situacion' => 'MUTUSICUMUTU',
                                                    'importe' => $importeGenera,
                                                    'proveedor_id' => $proveedor_imputa,
                                                    'vencimiento' => date('Y-m-d'),
                                                    'vencimiento_proveedor' => date('Y-m-d')
                                                ));
                   
//                    debug($cuotaAdicional);
                    if($GENERAR_CUOTA && $adicional['MutualAdicional']['activo'] == 1){
                        
                        if($oCUOTA->save($cuotaAdicional)){
                            $cuotaAdicional['OrdenDescuentoCuota']['id'] = $oCUOTA->getLastInsertID();
                            $liqCuo = array('LiquidacionCuotaNoimputada' => array(
                                        'id' => 0,
                                        'liquidacion_id' => $liquidacion_id,
                                        'socio_id' => $socio_id,
                                        'persona_beneficio_id' => $beneficio_id,
                                        'orden_descuento_id' => $orden['OrdenDescuento']['id'],
                                        'orden_descuento_cuota_id' => $cuotaAdicional['OrdenDescuentoCuota']['id'],
                                        'tipo_orden_dto' => $orden['OrdenDescuento']['tipo_orden_dto'],
                                        'tipo_producto' => $orden['OrdenDescuento']['tipo_producto'],
                                        'tipo_cuota' => $adicional['MutualAdicional']['tipo_cuota'],
                                        'periodo_cuota' => $periodo,
                                        'proveedor_id' => $proveedor_imputa,
                                        'vencida' => 0,
                                        'importe' => $importeGenera,
                                        'saldo_actual' => $importeGenera,
                                        'codigo_organismo' => $codigoOrganismo,
                                        'mutual_adicional_pendiente_id' => 0
                                )); 
    //                        debug($liqCuo);
                            $oLC->save($liqCuo);
                        }
                        
                    }
                    
                }
                
                
                
            }
            
            
//            debug($importeGenera);
//            debug($adicionalPendiente);
            
//            debug($cuotas);
            
        }
        return TRUE;
        
    }


    /**
     * Refactoring del metodo generaAdicional
     * Adrian 11/11/2020
     */
    function generaAdicionalSobreLiquidacion($liquidacion_id,$socio_id,$codigoOrganismo,$periodo){
        $adicionales = array();
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();         
		App::import('Model','Mutual.MutualAdicional');
		$oADICIONAL = new MutualAdicional();       
        $parametros = $oADICIONAL->getActivosByOrganismo($codigoOrganismo,$periodo,true);
        if(empty($parametros)) return TRUE;

        foreach($parametros as $adicional){
            $proveedor = $adicional['MutualAdicional']['proveedor_id'];
            $proveedor_imputa = $adicional['MutualAdicional']['imputar_proveedor_id'];
            $punitorio = 0;
            if($adicional['MutualAdicional']['deuda_calcula'] == 5){
                $punitorio = $adicional['MutualAdicional']['valor'] / 100;
            }
            $saldos = array();
            $deudaCalcula = $adicional['MutualAdicional']['deuda_calcula'];
            $impoDeuda = 0;
            $adps = array();    
            switch ($deudaCalcula){
                case 2:
                    //deuda anterior al periodo liquidado
                    $sql = "select IFNULL(sum(saldo_actual),0) as saldo_actual from liquidacion_cuotas where liquidacion_id = $liquidacion_id
                            and socio_id = $socio_id and periodo_cuota < '$periodo' ".(!empty($proveedor) ? " and proveedor_id = $proveedor " : "").";";
                    $saldo = $oLC->query($sql);
                    $impoDeuda = $saldo[0]['saldo_actual'];    
                    break;
                case 3:
                    //deuda del periodo liquidado
                    $sql = "select IFNULL(sum(saldo_actual),0) as saldo_actual from liquidacion_cuotas where liquidacion_id = $liquidacion_id
                            and socio_id = $socio_id and periodo_cuota = '$periodo' ".(!empty($proveedor) ? " and proveedor_id = $proveedor " : "").";";
                    $saldo = $oLC->query($sql);  
                    $impoDeuda = $saldo[0]['saldo_actual'];      
                    break;
                case 4:
                    // SOBRE CUOTA SOCIAL
                    $codigo = 'MUTUCUOS' . substr($codigoOrganismo,8,4);
                    $impoDeuda = parent::GlobalDato('decimal_1', $codigo);
                    break; 
                case 5:
                    #######################################################################
                    # PUNITORIOS
                    #######################################################################
                    $procPun = $adicional['MutualAdicional']['valor'] / 100; 
                    break;                                          

                default:
                    //toda la deuda incluida el periodo liquidado
                    $sql = "select IFNULL(sum(saldo_actual),0) as saldo_actual from liquidacion_cuotas where liquidacion_id = $liquidacion_id
                            and socio_id = $socio_id";
                    $saldo = $oLC->query($sql);  
                    $impoDeuda = $saldo[0]['saldo_actual'];      
                    break;    
            }
            if(empty($impoDeuda)){return;}
            debug($sql);
            debug($impoDeuda);

        }

    }

    
    function generarAdicional($liquidacion_id,$socio_id,$codigoOrganismo,$periodo,$situacionDeuda = 'MUTUSICUMUTU',$pre_imputacion=false,$drop=TRUE,$deudaCalcula=NULL,$cantidadDebitos=0){
        
        $ERROR = FALSE;
        
        if($drop){
            if(!$this->deleteAll("MutualAdicionalPendiente.liquidacion_id = $liquidacion_id AND MutualAdicionalPendiente.socio_id = $socio_id")){
                parent::notificar("ERROR AL BORRAR LOS ADICIONALES PENDIENTES DEL SOCIO #$socio_id");
                return FALSE;
            }
        }
        
        $adicionales = array();
        
        
		App::import('Model','Mutual.MutualAdicional');
		$oADICIONAL = new MutualAdicional();
        
        $parametros = $oADICIONAL->getActivosByOrganismo($codigoOrganismo,$periodo,false,$deudaCalcula);
    //    debug($parametros);
        
		if(empty($parametros)) return NULL;
		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();
        
		App::import('Model','pfyj.Socio');
		$oSOCIO = new Socio(); 
        
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();   
		
		
		
        
        foreach($parametros as $adicional){
            
//            debug($adicional);
            
            $proveedor = $adicional['MutualAdicional']['proveedor_id'];
//            $proveedor = 18;
            $proveedor_imputa = $adicional['MutualAdicional']['imputar_proveedor_id'];
            
            $punitorio = 0;
            if($adicional['MutualAdicional']['deuda_calcula'] == 5){
                $punitorio = $adicional['MutualAdicional']['valor'] / 100;
            }
            

        //    debug($adicional);
            
            $saldos = array();
            $deudaCalcula = $adicional['MutualAdicional']['deuda_calcula'];
            $impoDeuda = 0;
            $adps = array();
//            $deudaCalcula = 3;
            switch ($deudaCalcula){
//                    case 1:
//                            //toda la deuda incluida el periodo liquidado
//                            $saldos = Set::extract("/OrdenDescuentoCuota/saldo_calculado",$cuotas);
//                            $impoDeuda = array_sum($saldos);
//                            break;
                    case 2:
                        //deuda anterior al periodo liquidado
                        $cuotas = $oCUOTA->cuotasAdeudadasBySocioAlPeriodoByOrganismo($socio_id,$periodo,$codigoOrganismo,$situacionDeuda,$pre_imputacion,FALSE,NULL,0,$punitorio);
                        if(!empty($proveedor))$cuotas = Set::extract("/OrdenDescuentoCuota[proveedor_id=".$proveedor."]",$cuotas);
                        $cuotas = Set::extract("/OrdenDescuentoCuota",$cuotas);                                    
                        $cuotas = Set::extract("/OrdenDescuentoCuota[periodo<".$periodo."]",$cuotas);
                        $saldos = Set::extract("/OrdenDescuentoCuota/saldo_calculado",$cuotas);
                        $impoDeuda = array_sum($saldos);
                            break;
                    case 3:
                        //deuda del periodo liquidado
                        $cuotas = $oCUOTA->cuotasAdeudadasBySocioAlPeriodoByOrganismo($socio_id,$periodo,$codigoOrganismo,$situacionDeuda,$pre_imputacion,FALSE,NULL,0,$punitorio);
//                        debug($cuotas);
                        if(!empty($proveedor))$cuotas = Set::extract("/OrdenDescuentoCuota[proveedor_id=".$proveedor."]",$cuotas);
                        $cuotas = Set::extract("/OrdenDescuentoCuota",$cuotas);                                    
                        $cuotas = Set::extract("/OrdenDescuentoCuota[periodo=".$periodo."]",$cuotas);
                        $saldos = Set::extract("/OrdenDescuentoCuota/saldo_calculado",$cuotas);
                        $impoDeuda = array_sum($saldos);
                        break;
                    case 4:
                        // SOBRE CUOTA SOCIAL
                        $codigo = 'MUTUCUOS' . substr($codigoOrganismo,8,4);
                        $impoDeuda = parent::GlobalDato('decimal_1', $codigo);
//                                    array_push($saldos, $impoCuoSoc);
                        break;

                    case 5:
                        #######################################################################
                        # PUNITORIOS
                        #######################################################################
                        $procPun = $adicional['MutualAdicional']['valor'] / 100;

//                        $cuotas = $oCUOTA->procesa_deuda($socio_id,$periodo,$periodo,TRUE,$proveedor,$codigoOrganismo,FALSE,TRUE,NULL,NULL,$procPun);
                        //$socio_id,$periodo,$organismo,$situacion='MUTUSICUMUTU',$preImputacion = false,$proveedor_id = null,$tipoFiltro=0,$punitorio=0,$groupByOrden = FALSE
                        $cuotas = $oCUOTA->cuotasAdeudadasBySocioAlPeriodoByOrganismo($socio_id,$periodo,$codigoOrganismo,$situacionDeuda,$pre_imputacion,$proveedor,2,$procPun,true);
//                        debug($cuotas);
                        $cuotas = Set::extract("/OrdenDescuentoCuota",$cuotas); 
//                        debug($cuotas);
//                                                                       
                        if(!empty($cuotas)){

                            $adps = array();

                            foreach($cuotas as $cuota){

//                                debug($cuota);

                                $tmp = array();
                                $tmp['MutualAdicionalPendiente'] = array(
                                    'id' => 0,
                                    'liquidacion_id' => $liquidacion_id,
                                    'socio_id' => $socio_id,
                                    'codigo_organismo' => $codigoOrganismo,
                                    'proveedor_id' => (empty($adicional['MutualAdicional']['imputar_proveedor_id']) ? $cuota['OrdenDescuentoCuota']['proveedor_id'] : $adicional['MutualAdicional']['imputar_proveedor_id']),
                                    'imputar_proveedor_id' => (empty($adicional['MutualAdicional']['imputar_proveedor_id']) ? $cuota['OrdenDescuentoCuota']['proveedor_id'] : $adicional['MutualAdicional']['imputar_proveedor_id']),
                                    'tipo' => $adicional['MutualAdicional']['tipo'],
                                    'deuda_calcula' => $adicional['MutualAdicional']['deuda_calcula'],
                                    'valor' => $adicional['MutualAdicional']['valor'],
                                    'tipo_cuota' => $adicional['MutualAdicional']['tipo_cuota'],
                                    'periodo' => $cuota['OrdenDescuentoCuota']['periodo_max'],	
                                    'total_deuda' => $cuota['OrdenDescuentoCuota']['saldo_calculado'],
                                    'importe' => $cuota['OrdenDescuentoCuota']['punitorios'],
                                    'orden_descuento_id' => $cuota['OrdenDescuentoCuota']['orden_descuento_id'],
                                    'persona_beneficio_id' =>$cuota['OrdenDescuentoCuota']['persona_beneficio_id'], 

                                );
                                $tmp['OrdenDescuentoCuota'] = $cuota['OrdenDescuentoCuota'];
                                if (!empty($cuota['OrdenDescuentoCuota']['punitorios'])) {
                                    array_push($adps, $tmp);
                                }
                            }


                        }                                        

                        
                        break;

                    // case 6:
                    //     #no hacer nada este caso es para el gasto bancario por registro
                    //     break;

                    default:
                        //toda la deuda incluida el periodo liquidado
                        $cuotas = $oCUOTA->cuotasAdeudadasBySocioAlPeriodoByOrganismo($socio_id,$periodo,$codigoOrganismo,$situacionDeuda,$pre_imputacion);
                        $saldos = Set::extract("/OrdenDescuentoCuota/saldo_calculado",$cuotas);
                        $impoDeuda = array_sum($saldos);
                        break;
            }            
            
            
            switch ($adicional['MutualAdicional']['tipo']){
                case 'P':
					$importeGenera = $impoDeuda * $adicional['MutualAdicional']['valor'] / 100;
                    break;
                case 'I':
                    $importeGenera = ($adicional['MutualAdicional']['deuda_calcula'] != 6 ? $adicional['MutualAdicional']['valor'] : $adicional['MutualAdicional']['valor'] * $cantidadDebitos);  
                    $importeGenera = ($impoDeuda != 0 ? $importeGenera : 0);
                    break;
                default:
                    $importeGenera = 0;
                    break;
            }            
            
            $importeGenera = round($importeGenera,2);    
            
            // debug($adicional);
            // debug($importeGenera);
            // exit;
            
            
            ##############################################################################           
            # GENERAR EL CALCULO DE LOS PUNITORIOS
            # genero los adicionales pendientes por orden de descuento y salgo del metodo            
            ##############################################################################           
            if($adicional['MutualAdicional']['deuda_calcula'] == 5 && $adicional['MutualAdicional']['devengado_previo'] == 0 && $adicional['MutualAdicional']['activo'] == 1 && !empty($adps)){
                
//                return TRUE;
                
                foreach($adps as $adp){
                    
//                    debug($adp);
                    parent::begin();
                    if($this->save(array('MutualAdicionalPendiente' => $adp['MutualAdicionalPendiente']))){
                        $adp['MutualAdicionalPendiente']['id'] = $this->getLastInsertID();
                        $liqCuo = array('LiquidacionCuota' => array(
                                    'id' => 0,
                                    'liquidacion_id' => $liquidacion_id,
                                    'socio_id' => $socio_id,
                                    'persona_beneficio_id' => $adp['MutualAdicionalPendiente']['persona_beneficio_id'],
                                    'orden_descuento_id' => $adp['MutualAdicionalPendiente']['orden_descuento_id'],
                                    'orden_descuento_cuota_id' => null,
                                    'tipo_orden_dto' => $adp['OrdenDescuentoCuota']['tipo_orden_dto'],
                                    'tipo_producto' => $adp['OrdenDescuentoCuota']['tipo_producto'],
                                    'tipo_cuota' => $adicional['MutualAdicional']['tipo_cuota'],
                                    'periodo_cuota' => $adp['MutualAdicionalPendiente']['periodo'],
                                    'proveedor_id' => $adp['MutualAdicionalPendiente']['proveedor_id'],
                                    'vencida' => 0,
                                    'importe' => $adp['MutualAdicionalPendiente']['importe'],
                                    'saldo_actual' => $adp['MutualAdicionalPendiente']['importe'],
                                    'codigo_organismo' => $codigoOrganismo,
                                    'mutual_adicional_pendiente_id' => $adp['MutualAdicionalPendiente']['id']
                            )); 
//                            debug($liqCuo);
                        if(!$oLC->save($liqCuo)){
                            parent::rollback();
                            $this->notificaciones = "ERROR al crear la liquidacionCuotas de los punitorios";
                            return FALSE;
                        }
                    }else{
                        
                        parent::rollback();
                        $this->notificaciones = "ERROR al crear el adicional de los punitorios";
                        return FALSE;
                        
                    }
                    
                    parent::commit();
                    $importeGenera = 0;
//                    parent::rollback();
                    
                    
                }

            }
            #############################################################################################
            # FIN DE GENERACION DE LOS PUNITORIOS
            #############################################################################################
            
            // SI EL SOCIO NO ESTA VIGENTE, NO DEVENGAR NINGUN ADICIONAL POR IMPORTE FIJO
            $socioVigente = $oSOCIO->isActivo($socio_id);
            if(!$socioVigente) {$importeGenera = 0;}
                        

//            debug($cuotas);
        //    debug($importeGenera);
        //    exit;

            $adicionalPendiente = array();
            
            if($importeGenera > 0){
                
                $orden = $oSOCIO->getOrdenDtoCuotaSocial($socio_id);

                // debug($orden);
                if(!empty($cuotas)){
                    $beneficios = Set::extract("/OrdenDescuentoCuota/persona_beneficio_id",$cuotas);
                    asort($beneficios);
    
                    $beneficio_id = array_pop($beneficios);    
                }else{
                    // si no tengo cuotas tomar el beneficio de la orden
                    $beneficio_id = $orden['OrdenDescuento']['persona_beneficio_id'];
                
                }
                
                if(empty($orden) && !empty($beneficio_id)){
                    // no tiene orden para cargos mutual
                    $orden = array();
                    $orden['OrdenDescuento'] = array(
                            'id' => 0,
                            'fecha' => date('Y-m-d'),
                            'tipo_orden_dto' => parent::GlobalDato('concepto_3',"MUTUPROD0003"),
                            'numero' => $socio_id,
                            'tipo_producto' => 'MUTUPROD0003',
                            'socio_id' => $socio_id,
                            'persona_beneficio_id' => $beneficio_id,
                            'proveedor_id' => parent::GlobalDato('entero_1',"MUTUPROD0003"),
                            'mutual_producto_id' => 0,
                            'periodo_ini' => $periodo,
                            'primer_vto_socio' => date('Y-m-d'),
                            'primer_vto_proveedor' => date('Y-m-d'),
                            'importe_cuota' => $oSOCIO->getImpoCuotaSocial($socio_id),
                            'cuotas' => 1,
                            'permanente' => 1,
                            'activo' => 1,
                    );
                    App::import('Model', 'Mutual.OrdenDescuento');
                    $oORDDTO = new OrdenDescuento(null); 
                    if($oORDDTO->save($orden)){
                        $orden['OrdenDescuento']['id'] = $oORDDTO->getLastInsertID();
                    }
                    
                }
                
                $adicionalPendiente = array();
                
                if(!empty($orden['OrdenDescuento']['id']) && $orden['OrdenDescuento']['activo'] && !empty($beneficio_id)){
                    $adicionalPendiente = array('MutualAdicionalPendiente' => array(
                         'id' => 0,
                         'liquidacion_id' => $liquidacion_id,
                         'socio_id' => $socio_id,
                         'codigo_organismo' => $codigoOrganismo,
                         'proveedor_id' => $proveedor,
                         'imputar_proveedor_id' => $proveedor_imputa,
                         'tipo' => $adicional['MutualAdicional']['tipo'],
                         'deuda_calcula' => $adicional['MutualAdicional']['deuda_calcula'],
                         'valor' => $adicional['MutualAdicional']['valor'],
                         'tipo_cuota' => $adicional['MutualAdicional']['tipo_cuota'],
                         'periodo' => $periodo,	
                         'total_deuda' => $impoDeuda,
                         'importe' => $importeGenera,
                         'orden_descuento_id' => $orden['OrdenDescuento']['id'],
                         'persona_beneficio_id' => $beneficio_id, 	
                     ));                     
                }
                
                
//                debug($orden['OrdenDescuento']['id'] ." - ".$beneficio_id." - ".$adicional['MutualAdicional']['devengado_previo']);
            //    debug($adicionalPendiente);

                /**
                 * Si es un tipo registro bancario en la liquidación tratarlo siempre como virtual.
                 * En la imputación, cargar la deuda si no tiene debito
                 */
                if($adicional['MutualAdicional']['deuda_calcula'] == 6){
                    // $adicional['MutualAdicional']['devengado_previo'] = 0;
                }            
                
                if($adicional['MutualAdicional']['devengado_previo'] == 0 && !empty($adicionalPendiente)){
                    
                    if($adicional['MutualAdicional']['activo'] == 1){
                        
                        ###########################################################################
                        #SACO ESTA OPCION DE BORRADO REDUNDANTE
                        #PORQUE CUANDO LIQUIDA PRIMERO BORRA TODO
                        #NO TIENE SENTIDO
                        #Adrian 29/09/2016
                        ############################################################################
//                        $sql = "delete from liquidacion_cuotas where liquidacion_id = $liquidacion_id and socio_id = $socio_id and ifnull(mutual_adicional_pendiente_id,0) <> 0";
//                        $oLC->query($sql);
//                        $sql = "delete from mutual_adicional_pendientes where liquidacion_id = $liquidacion_id and socio_id = $socio_id;";
//                        $this->query($sql);
                        
                        
                            if($this->save($adicionalPendiente)){
                                $adicionalPendiente['MutualAdicionalPendiente']['id'] = $this->getLastInsertID();
                                $liqCuo = array('LiquidacionCuota' => array(
                                            'id' => 0,
                                            'liquidacion_id' => $liquidacion_id,
                                            'socio_id' => $socio_id,
                                            'persona_beneficio_id' => $beneficio_id,
                                            'orden_descuento_id' => $orden['OrdenDescuento']['id'],
                                            'orden_descuento_cuota_id' => null,
                                            'tipo_orden_dto' => $orden['OrdenDescuento']['tipo_orden_dto'],
                                            'tipo_producto' => $orden['OrdenDescuento']['tipo_producto'],
                                            'tipo_cuota' => $adicional['MutualAdicional']['tipo_cuota'],
                                            'periodo_cuota' => $periodo,
                                            'proveedor_id' => $proveedor_imputa,
                                            'vencida' => 0,
                                            'importe' => $importeGenera,
                                            'saldo_actual' => $importeGenera,
                                            'codigo_organismo' => $codigoOrganismo,
                                            'mutual_adicional_pendiente_id' => $adicionalPendiente['MutualAdicionalPendiente']['id']
                                    )); 
            //                        debug($liqCuo);
                                $oLC->save($liqCuo); 
                            }    
                        }
                     
                }else if(!empty($orden['OrdenDescuento']['id']) && !empty($beneficio_id) && $adicional['MutualAdicional']['devengado_previo'] == 1){
                    
                    
                    
                    $sql = "select OrdenDescuentoCuota.* from orden_descuento_cuotas OrdenDescuentoCuota
                            where OrdenDescuentoCuota.socio_id = $socio_id
                            and OrdenDescuentoCuota.orden_descuento_id = ".$orden['OrdenDescuento']['id']."
                            and OrdenDescuentoCuota.persona_beneficio_id = $beneficio_id
                            and OrdenDescuentoCuota.proveedor_id = $proveedor_imputa
                            and OrdenDescuentoCuota.periodo = '$periodo'
                            and OrdenDescuentoCuota.tipo_cuota = '".$adicional['MutualAdicional']['tipo_cuota']."'
                            and OrdenDescuentoCuota.id not in (select orden_descuento_cuota_id from
                            orden_descuento_cobro_cuotas cocu inner join orden_descuento_cobros co
                            on (co.id = cocu.orden_descuento_cobro_id) where co.socio_id = $socio_id)";
                    
                    $datos = $oCUOTA->query($sql);
                //    debug($datos);
                //    exit;
                    if(!empty($datos)){
                        $cuotasExistentes = Set::extract("/OrdenDescuentoCuota/id",$datos);


                        $sql = "DELETE FROM liquidacion_cuotas where liquidacion_id = $liquidacion_id and socio_id = $socio_id and orden_descuento_cuota_id in (".  implode(',', $cuotasExistentes).");";
                        
                        $oLC->begin();
                        $oLC->query($sql);
                        if(!empty($oLC->getDataSource()->error)){
                            $oLC->rollback();
                            $this->notificaciones = $oLC->getDataSource()->error;
                            return false;
                        }
                        $oLC->commit();
                        
                        $sql = "DELETE FROM orden_descuento_cuotas where id in (".  implode(',', $cuotasExistentes).");";
                        $oCUOTA->begin();
                        $oCUOTA->query($sql);

                        if(!empty($oCUOTA->getDataSource()->error)){
                            $oCUOTA->rollback();
                            $this->notificaciones = $oCUOTA->getDataSource()->error;
                            return false;
                        }
                        $oCUOTA->commit();
                        
                    }
                    // exit;
                    $GENERAR_CUOTA = TRUE;
                    $sql = "select OrdenDescuentoCuota.id from orden_descuento_cuotas OrdenDescuentoCuota
                            where OrdenDescuentoCuota.socio_id = $socio_id
                            and OrdenDescuentoCuota.orden_descuento_id = ".$orden['OrdenDescuento']['id']."
                            and OrdenDescuentoCuota.persona_beneficio_id = $beneficio_id
                            and OrdenDescuentoCuota.proveedor_id = $proveedor_imputa
                            and OrdenDescuentoCuota.periodo = '$periodo'
                            and OrdenDescuentoCuota.tipo_cuota = '".$adicional['MutualAdicional']['tipo_cuota']."'
                            and OrdenDescuentoCuota.id in (select orden_descuento_cuota_id from
                            orden_descuento_cobro_cuotas cocu inner join orden_descuento_cobros co
                            on (co.id = cocu.orden_descuento_cobro_id) where co.socio_id = $socio_id)";
                    $datos = $oCUOTA->query($sql); 
                    // debug($datos);
                    // exit;

                    if(!empty($datos)) $GENERAR_CUOTA = FALSE;                    
                    
                //    debug($cuotasExistentes);
                //    exit;
                    
                    $cuotaAdicional = array('OrdenDescuentoCuota' => array(
                                                    'id' => 0,
                                                    'orden_descuento_id' => $orden['OrdenDescuento']['id'],
                                                    'persona_beneficio_id' => $beneficio_id,
                                                    'socio_id' => $socio_id,
                                                    'tipo_orden_dto' => $orden['OrdenDescuento']['tipo_orden_dto'],
                                                    'tipo_producto' => $orden['OrdenDescuento']['tipo_producto'],
                                                    'periodo' => $periodo,
                                                    'nro_cuota' => 0,
                                                    'tipo_cuota' => $adicional['MutualAdicional']['tipo_cuota'],
                                                    'estado' => 'A',
                                                    'situacion' => 'MUTUSICUMUTU',
                                                    'importe' => $importeGenera,
                                                    'proveedor_id' => $proveedor_imputa,
                                                    'vencimiento' => date('Y-m-d'),
                                                    'vencimiento_proveedor' => date('Y-m-d'),
                                                    'capital' => $importeGenera,
                                                    'interes' => 0,
                                                    'iva' => 0,
                                                ));
                   
//                    debug($cuotaAdicional);
                    if($GENERAR_CUOTA && $adicional['MutualAdicional']['activo'] == 1){
                        
                        if($oCUOTA->save($cuotaAdicional)){
                            $cuotaAdicional['OrdenDescuentoCuota']['id'] = $oCUOTA->getLastInsertID();
                            $liqCuo = array('LiquidacionCuota' => array(
                                        'id' => 0,
                                        'liquidacion_id' => $liquidacion_id,
                                        'socio_id' => $socio_id,
                                        'persona_beneficio_id' => $beneficio_id,
                                        'orden_descuento_id' => $orden['OrdenDescuento']['id'],
                                        'orden_descuento_cuota_id' => $cuotaAdicional['OrdenDescuentoCuota']['id'],
                                        'tipo_orden_dto' => $orden['OrdenDescuento']['tipo_orden_dto'],
                                        'tipo_producto' => $orden['OrdenDescuento']['tipo_producto'],
                                        'tipo_cuota' => $adicional['MutualAdicional']['tipo_cuota'],
                                        'periodo_cuota' => $periodo,
                                        'proveedor_id' => $proveedor_imputa,
                                        'vencida' => 0,
                                        'importe' => $importeGenera,
                                        'saldo_actual' => $importeGenera,
                                        'codigo_organismo' => $codigoOrganismo,
                                        'mutual_adicional_pendiente_id' => 0
                                )); 
                            $oLC->save($liqCuo);
                        }
                        
                    }
                    
                }
                
                // si el adicional es del tipo 6 (impo x registro)
                // retornar e importe
                if($adicional['MutualAdicional']['deuda_calcula'] == 6){
                    return $importeGenera;
                }

                
            } // end foreach
            
            
        //    debug($importeGenera);
//            debug($adicionalPendiente);
            
//            debug($cuotas);
            
        }
        return TRUE;
        
    }
    
    
    
	
}
?>
