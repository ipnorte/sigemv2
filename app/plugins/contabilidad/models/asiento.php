<?php
class Asiento extends ContabilidadAppModel {
	var $name = 'Asiento';
	var $useTable = 'co_asientos';

//	function get_asiento_id($asiento_id){
//		$asiento = $this->find('all', array('conditions' => array('Asiento.id' => $asiento_id)));
//		return $asiento[0];
//	}


	
	function getAsiento($asiento_id){
		$asiento = $this->read(null,$asiento_id);
		return $this->setDatosAdicionales($asiento);
	}
	
	function setDatosAdicionales($asiento){
		App::import('Model','Contabilidad.AsientoRenglon');
		$Asiento_Renglon = new AsientoRenglon();
		$renglones = $Asiento_Renglon->getRenglonesAsiento($asiento['Asiento']['id']);
		$asiento['renglones'] = $renglones;		
		return $asiento;
	}
	
	
	function guardar($datos, $apertura=0){
            $renglones = base64_decode($datos['Asiento']['renglonesSerialize']);
            $renglones = unserialize($renglones);

            $asiento = array();

            $asiento['Asiento'] = array(
                'id' => 0,
                'nro_asiento' => 0,
                'co_ejercicio_id' => $datos['Asiento']['co_ejercicio_id'],
                'fecha' => $this->armaFecha($datos['Asiento']['fecha']),
                'tipo_documento' => $datos['Asiento']['tipo_documento'],
                'nro_documento' => $datos['Asiento']['nro_documento'],
                'referencia' => $datos['Asiento']['referencia'],
                'co_asiento_id' => (empty($datos['Asiento']['co_asiento_id']) ? null : $datos['Asiento']['co_asiento_id']),
                'tipo' => ($apertura === 1 ? 1 : 2),
                'debe' => 0,
                'haber' => 0
            );

            $nroAsiento = 1;
            if($apertura == 0){ $nroAsiento = $this->getNumeroAsiento($datos['Asiento']['co_ejercicio_id']);}
            if($nroAsiento == 0):		
                return false;
            endif;

            $this->begin();
//			$nroAsiento = $this->traeNroAsiento($datos['Asiento']['co_ejercicio_id']);

            $asiento['Asiento']['nro_asiento'] = $nroAsiento;			

            $ret = parent::save($asiento);
            if(!$ret){
                $this->rollback();
                if($apertura == 0){ $this->unLookRegistro($datos['Asiento']['co_ejercicio_id']);}
                return $ret;
            }

            $nAsiento = $this->getLastInsertID();

            $tmpRenglon = array();

            $temp = array();
            $totalDebe = 0;
            $totalHaber = 0;
            foreach($renglones as $renglon){
                $temp['id'] = 0;
                $temp['co_asiento_id'] = $nAsiento;
                $temp['fecha'] = $this->armaFecha($datos['Asiento']['fecha']);
                $temp['co_plan_cuenta_id'] = $renglon['Asiento']['co_plan_cuenta_id'];
                $temp['referencia'] = $renglon['Asiento']['referencia_renglon'];
                $temp['debe'] = ($renglon['Asiento']['tipo'] == 'D' ? $renglon['Asiento']['importe'] : 0);
                $temp['haber'] = ($renglon['Asiento']['tipo'] == 'H' ? $renglon['Asiento']['importe'] : 0);
                array_push($tmpRenglon, $temp);
                $totalDebe += $temp['debe'];			
                $totalHaber += $temp['haber'];			
            }

            $asiento['Asiento']['debe'] = $totalDebe;
            $asiento['Asiento']['haber'] = $totalHaber;

            App::import('Model','Contabilidad.AsientoRenglon');
            $oAsientoRenglon = new AsientoRenglon();
            if(!$oAsientoRenglon->saveAll($tmpRenglon)):		
                $this->rollback();
                if($apertura == 0){ $this->unLookRegistro($datos['Asiento']['co_ejercicio_id']);}
                return false;
            endif;


            if(!empty($asiento['Asiento']['co_asiento_id'])){
                $asientoAnula = array();
                $asientoAnula['Asiento']['id'] = $asiento['Asiento']['co_asiento_id'];
                $asientoAnula['Asiento']['co_asiento_id'] = $nAsiento;
                $asientoAnula['Asiento']['anulado'] = 1; 
                if(!$this->save($asientoAnula)){		
                    $this->rollback();
                    if($apertura === 0){ $this->unLookRegistro($datos['Asiento']['co_ejercicio_id']);}
                    return false;
                }
            }

            $asiento['Asiento']['id'] = $nAsiento;
            if(!$this->save($asiento)):		
                $this->rollback();
                if($apertura === 0){ $this->unLookRegistro($datos['Asiento']['co_ejercicio_id']);}
                return false;
            else:
                $this->commit();
                if($apertura === 0){ $this->putNumeroAsiento($datos['Asiento']['co_ejercicio_id'], 1);}
                return true;
            endif;
	
	}
	
	
	function getAsientoFecha($fecha_desde, $fecha_hasta, $ejercicioId){
		
		return $this->find('all', array('conditions' => array('Asiento.co_ejercicio_id' => $ejercicioId, 'Asiento.fecha >= ' => $fecha_desde, 'Asiento.fecha <= ' => $fecha_hasta), 'order' => array('Asiento.fecha', 'Asiento.id') ));
		
	}
	
	
	function getAsientoAll($ejercicioId){
		
		return $this->find('all', array('conditions' => array('Asiento.co_ejercicio_id' => $ejercicioId), 'order' => array('Asiento.fecha', 'Asiento.nro_asiento') ));
		
	}
	
	
	function guardarAsiEspecial($datos, $tipo=1){
		
            $ejercicio = $this->traeEjercicio($datos['Asiento']['co_ejercicio_id']);

            $ejercicio_id = $datos['Asiento']['co_ejercicio_id'];

            $asiento = array();

            /*
             * CABECERA DE ASIENTO.
             */
            $asiento['Asiento'] = array(
                    'id' => 0,
                    'nro_asiento' => 0,
                    'co_ejercicio_id' => $datos['Asiento']['co_ejercicio_id'],
                    'fecha' => $ejercicio['fecha_hasta'],
                    'tipo_documento' => "",
                    'nro_documento' => "",
                    'referencia' => $datos['Asiento']['referencia'],
                    'co_asiento_id' => null,
                    'tipo' => $tipo,
                    'debe' => 0,
                    'haber' => 0
            );

            $fecha_asiento = $ejercicio['fecha_hasta'];
/*
 * TRAIGO LOS RENGLONES DEL ASIENTO DE RESULTADO PARA GUARDAR. 
 * 
 */
//===========================================================================================
            if($tipo === 3 || $tipo === 4){
                $update = "
                        UPDATE co_plan_cuentas PlanCuenta
                        SET	PlanCuenta.acumulado_debe = 0, PlanCuenta.acumulado_haber = 0
                        WHERE PlanCuenta.co_ejercicio_id = '$ejercicio_id'
                        ";
                $this->query($update);

                $update = " 
                        UPDATE co_plan_cuentas PlanCuenta
                        SET	PlanCuenta.acumulado_debe = ((
                            SELECT SUM(AsientoRenglon.haber) 
                            FROM co_asiento_renglones AsientoRenglon, co_asientos Asiento 
                            WHERE AsientoRenglon.co_plan_cuenta_id = PlanCuenta.id AND AsientoRenglon.co_asiento_id = Asiento.id) - (
                            SELECT SUM(AsientoRenglon.debe) 
                            FROM co_asiento_renglones AsientoRenglon, co_asientos Asiento 
                            WHERE AsientoRenglon.co_plan_cuenta_id = PlanCuenta.id AND AsientoRenglon.co_asiento_id = Asiento.id))
                        WHERE PlanCuenta.co_ejercicio_id = '$ejercicio_id'";
                $this->query($update);

                $sqlUpdate = "
                        UPDATE co_plan_cuentas PlanCuenta
                        SET	PlanCuenta.acumulado_debe = 0
                        WHERE PlanCuenta.co_ejercicio_id = '$ejercicio_id' AND PlanCuenta.acumulado_debe IS NULL
                        ";
                $this->query($sqlUpdate);

                $sqlUpdate = "
                        UPDATE co_plan_cuentas
                        SET    acumulado_haber = acumulado_debe * (-1), acumulado_debe = 0
                        WHERE  co_ejercicio_id = '$ejercicio_id' AND acumulado_debe < 0
                        ";
                $this->query($sqlUpdate);

                if($tipo === 3){
                    $sqlDebe ="
                        SELECT PlanCuenta.*, acumulado_debe AS importe
                        FROM co_plan_cuentas PlanCuenta
                        WHERE co_ejercicio_id = '$ejercicio_id' AND tipo_cuenta IN('RN', 'RP') AND acumulado_debe > 0
                        ORDER BY PlanCuenta.cuenta
                        ";
                }
                else{
                    $sqlDebe ="
                        SELECT	PlanCuenta.*, acumulado_debe AS importe
                        FROM co_plan_cuentas PlanCuenta
                        WHERE co_ejercicio_id = '$ejercicio_id' AND acumulado_debe > 0
                        ORDER BY PlanCuenta.cuenta
                        ";
                    
                }
                $aDebe = $this->query($sqlDebe);

                if($tipo === 3){
                    $resultado = $ejercicio['resultado_co_plan_cuenta_id'];
                    $sqlResultado = "
                        SELECT PlanCuenta.*, ((
                        SELECT SUM(acumulado_debe) AS t_haber
                        FROM co_plan_cuentas
                        WHERE co_ejercicio_id = PlanCuenta.co_ejercicio_id AND tipo_cuenta IN('RN', 'RP') AND acumulado_debe > 0
                        ) - (
                        SELECT SUM(acumulado_haber)
                        FROM co_plan_cuentas
                        WHERE co_ejercicio_id = PlanCuenta.co_ejercicio_id AND tipo_cuenta IN('RN', 'RP') AND acumulado_haber > 0
                        )) AS importe
                        FROM co_plan_cuentas PlanCuenta
                        WHERE id = '$resultado' 
                        ";
                    $aResultado = $this->query($sqlResultado);
                }
                
                if($tipo === 3){
                    $sqlHaber ="
                        SELECT PlanCuenta.*, acumulado_haber AS importe
                        FROM co_plan_cuentas PlanCuenta
                        WHERE co_ejercicio_id = '$ejercicio_id' AND tipo_cuenta IN('RN', 'RP') AND acumulado_haber > 0
                        ORDER BY PlanCuenta.cuenta
                        ";
                }
                else{
                    $sqlHaber ="
                        SELECT	PlanCuenta.*, acumulado_haber AS importe
                        FROM co_plan_cuentas PlanCuenta
                        WHERE co_ejercicio_id = '$ejercicio_id' AND acumulado_haber > 0
                        ORDER BY PlanCuenta.cuenta
                        ";
                }
                $aHaber = $this->query($sqlHaber);
            }
            else {
                $fecha_asiento = $ejercicio['fecha_desde'];
                $asiento['Asiento']['fecha'] = $ejercicio['fecha_desde'];
                $ejAnterior = $this->traeEjercicioAnt($ejercicio_id);
                $asientoFinal = $this->getAsientoApertura($ejAnterior['id']);
                $aDebe = array();
                $aHaber = array();
                $temp = array();

                foreach($asientoFinal['renglones'] as $renglon){
                    if($renglon['AsientoRenglon']['haber'] > 0){
                        $temp['PlanCuenta']['id'] = $renglon['AsientoRenglon']['co_plan_cuenta_id'];
                        $temp['PlanCuenta']['importe'] = $renglon['AsientoRenglon']['haber'];
                        array_push($aDebe, $temp);
                    }else{
                        $temp['PlanCuenta']['id'] = $renglon['AsientoRenglon']['co_plan_cuenta_id'];
                        $temp['PlanCuenta']['importe'] = $renglon['AsientoRenglon']['debe'];
                        array_push($aHaber, $temp);
                    }
                }
            }

//=======================================================================================
            /*
             * PREPARO LOS RENGLONES DEL ASIENTO.
             */
            
            $tmpRenglon = array();

            $temp = array();
            $totalDebe = 0;
            $totalHaber = 0;
            foreach($aDebe as $renglon){
                $temp['id'] = 0;
                $temp['co_asiento_id'] = 0;
                $temp['fecha'] = $fecha_asiento;
                $temp['co_plan_cuenta_id'] = $renglon['PlanCuenta']['id'];
                $temp['referencia'] = $datos['Asiento']['referencia'];
                $temp['debe'] = $renglon['PlanCuenta']['importe'];
                $temp['haber'] = 0;
                array_push($tmpRenglon, $temp);
                $totalDebe += $renglon['PlanCuenta']['importe'];
            }

            if($tipo === 3){
                foreach($aResultado as $renglon){
                    $temp['id'] = 0;
                    $temp['co_asiento_id'] = 0;
                    $temp['fecha'] = $fecha_asiento;
                    $temp['co_plan_cuenta_id'] = $renglon['PlanCuenta']['id'];
                    $temp['referencia'] = $datos['Asiento']['referencia'];
                    $temp['debe'] = 0;
                    $temp['haber'] = 0;
                    if($renglon[0]['importe'] < 0):
                        $temp['debe'] = $renglon[0]['importe']*(-1);
                        $totalDebe += ($renglon[0]['importe']*(-1));
                    else:
                        $temp['haber'] = $renglon[0]['importe'];
                        $totalHaber += $renglon[0]['importe'];
                    endif;
                    array_push($tmpRenglon, $temp);
                }
            }

            foreach($aHaber as $renglon){
                $temp['id'] = 0;
                $temp['co_asiento_id'] = 0;
                $temp['fecha'] = $fecha_asiento;
                $temp['co_plan_cuenta_id'] = $renglon['PlanCuenta']['id'];
                $temp['referencia'] = $datos['Asiento']['referencia'];
                $temp['debe'] = 0;
                $temp['haber'] = $renglon['PlanCuenta']['importe'];
                array_push($tmpRenglon, $temp);
                $totalHaber += $renglon['PlanCuenta']['importe'];
            }

            $asiento['Asiento']['debe'] = $totalDebe;
            $asiento['Asiento']['haber'] = $totalHaber;

//===========================================================================  
            $nroAsiento = 1;

            if($tipo != 1){ $nroAsiento = $this->getNumeroAsiento($ejercicio_id);}
            if($nroAsiento == 0):		
                return false;
            endif;

            $this->begin();

            $asiento['Asiento']['nro_asiento'] = $nroAsiento;			

            $ret = parent::save($asiento);
            if(!$ret){
                $this->rollback();
                $this->unLookRegistro($ejercicio_id);
                return $ret;
            }

            $nAsiento = $this->getLastInsertID();

            foreach($tmpRenglon as $key => $value):
                $tmpRenglon[$key]['co_asiento_id'] = $nAsiento;
            endforeach;
            
            App::import('Model','Contabilidad.AsientoRenglon');
            $oAsientoRenglon = new AsientoRenglon();
            if(!$oAsientoRenglon->saveAll($tmpRenglon)):		
                $this->rollback();
                $this->unLookRegistro($ejercicio_id);
                return false;
            endif;

            $this->commit();
            if($tipo != 1){ $this->putNumeroAsiento($ejercicio_id, 1);}
            return true;
	
	}
        
        
        function existeInicial($ejercicio_id){
            $asientoFinal = $this->find('all', array('conditions' => array('Asiento.co_ejercicio_id' => $ejercicio_id, 'Asiento.tipo' => '1') ));
            
            if(empty($asientoFinal)) return false;

            return true;
        }
        
        
        function existeFinal($ejercicio_id){
            $asientoFinal = $this->find('all', array('conditions' => array('Asiento.co_ejercicio_id' => $ejercicio_id, 'Asiento.tipo' => '4') ));
            
            if(empty($asientoFinal)) return false;

            return true;
        }
        
        
        function existeResultado($ejercicio_id){
            $asientoResultado = $this->find('all', array('conditions' => array('Asiento.co_ejercicio_id' => $ejercicio_id, 'Asiento.tipo' => '3') ));
            
            if(empty($asientoResultado)) return false;

            return true;
        }
        
        
        function existeInicialPost($ejercicio_id){
            $asientoFinal = NULL;
            $ejercicio = $this->traeEjercicioPos($ejercicio_id);
            
            if(!empty($ejercicio)){
                $asientoFinal = $this->find('all', array('conditions' => array('Asiento.co_ejercicio_id' => $ejercicio['id'], 'Asiento.tipo' => '1') ));
            }
            
            if(empty($asientoFinal)){ return false;}

            return true;
        }
        
        
        function getAsientoResultado($ejercicio_id){
            $asientoResultado = $this->find('all', array('conditions' => array('Asiento.co_ejercicio_id' => $ejercicio_id, 'Asiento.tipo' => '3') ));
            
            return $this->getAsiento($asientoResultado[0]['Asiento']['id']);
            
        }
        
        
        function getAsientoFinal($ejercicio_id){
            $asientoFinal = $this->find('all', array('conditions' => array('Asiento.co_ejercicio_id' => $ejercicio_id, 'Asiento.tipo' => '4') ));
            
            return $this->getAsiento($asientoFinal[0]['Asiento']['id']);
            
        }
        
        
        function borrarAsiento($id=0){
            if($id === 0){ return false;}

            App::import('Model','Contabilidad.AsientoRenglon');
            $Asiento_Renglon = new AsientoRenglon();

            $Asiento_Renglon->deleteAll("AsientoRenglon.co_asiento_id = $id");
            $this->deleteAll("Asiento.id = $id");
            
            return true;
            
        }
        
        
        function getAsientoApertura($ejercicio_id){
            App::import('Model', 'contabilidad.PlanCuenta');
            $oPlanCuenta = new PlanCuenta();

            $asientoFinal = $this->getAsientoFinal($ejercicio_id);
//            $asientoFinal = $this->find('all', array('conditions' => array('Asiento.co_ejercicio_id' => $ejercicio_id, 'Asiento.tipo' => '4') ));
            $asientoApertura = $this->getAsiento($asientoFinal[0]['Asiento']['id']);
            
            foreach($asientoApertura['renglones'] as $key => $value):
		$aPlanCta = $oPlanCuenta->find('all', array('conditions' => array('PlanCuenta.vincula_co_plan_cuenta_id' => $value['AsientoRenglon']['co_plan_cuenta_id'])));
                $asientoApertura['renglones'][$key]['AsientoRenglon']['co_plan_cuenta_id'] = $aPlanCta[0]['PlanCuenta']['id'];
                $asientoApertura['renglones'][$key]['AsientoRenglon']['descripcion_cuenta'] = $aPlanCta[0]['PlanCuenta']['descripcion'];
            endforeach;

            
            return $asientoApertura; 
            
        }
}	

?>