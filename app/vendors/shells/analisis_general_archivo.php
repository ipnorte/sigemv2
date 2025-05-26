<?php

/**
 * REPORTE DE CONTROL DE LA LIQUIDACION
 * 
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php analisis_general_archivo 320 -app /home/adrian/Desarrollo/www/sigem/app/
 * /usr/bin/php5 /home/adrian/trabajo/www/sigem/cake/console/cake.php analisis_general_archivo 46634 -app /home/adrian/trabajo/www/sigem/app/
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 *
 */

class AnalisisGeneralArchivoShell extends Shell {

	var $uses = array('Mutual.LiquidacionSocioRendicion');
	
	var $tasks = array('Temporal');
	
	function main() {
		
		$STOP = 0;
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 

		$liquidacion_id = $asinc->getParametro('p1');
		$archivo_id = $asinc->getParametro('p2');
		
		$conditions = array();
		$conditions['LiquidacionSocioRendicion.liquidacion_id'] = $liquidacion_id;
		if($archivo_id != 0) $conditions['LiquidacionSocioRendicion.liquidacion_intercambio_id'] = $archivo_id;

		$rendiciones = $this->LiquidacionSocioRendicion->find("all",array('conditions' => $conditions));
		
		if(!empty($rendiciones)):
		
			$total = count($rendiciones);
			$asinc->setTotal($total);
			$i = 0;			
		
			App::import('Model', 'Mutual.LiquidacionSocio');
			$oLS = new LiquidacionSocio();
			
			App::import('Model', 'Mutual.LiquidacionTurno');
			$oTURNO = new LiquidacionTurno();

			App::import('Model', 'Pfyj.Socio');
			$oSOCIO = new Socio();	
            
			App::import('Model', 'Pfyj.Persona');
			$oPERSONA = new Persona();	            

			App::import('Model','Mutual.LiquidacionIntercambio');
			$oFILE = new LiquidacionIntercambio();
		
			foreach($rendiciones as $rendicion):
			
			
				$rendicion = $this->LiquidacionSocioRendicion->armaDatos($rendicion);
				
				$rendicion['LiquidacionSocioRendicion']['persona_documento'] = $oSOCIO->getTdocNdoc($rendicion['LiquidacionSocioRendicion']['socio_id']);
				$rendicion['LiquidacionSocioRendicion']['persona_nombre'] = $oSOCIO->getApenom($rendicion['LiquidacionSocioRendicion']['socio_id'],false);
                
                $persona = $oSOCIO->getPersonaBySocioID($rendicion['LiquidacionSocioRendicion']['socio_id']);
                
		$dom = $oPERSONA->getDomicilioByPersonaId($persona['Persona']['id'],TRUE);	
                
                $rendicion['LiquidacionSocioRendicion']['persona_domicilio'] = $dom['calle']." ".$dom['numero_calle']." - " . $dom['localidad'] . " (CP " . $dom['localidad'] . ") - " . $dom['provincia'];
                $rendicion['LiquidacionSocioRendicion']['persona_domicilio_localidad'] = $dom['localidad'];
                $rendicion['LiquidacionSocioRendicion']['persona_domicilio_provincia'] = $dom['provincia'];
                
//                $rendicion['LiquidacionSocioRendicion']['persona_domicilio'] = $oPERSONA->getDomicilioByPersonaId($persona['Persona']['id']);
                                
                                
                $rendicion['LiquidacionSocioRendicion']['persona_contacto'] = $oPERSONA->getMediosContacto($persona['Persona']['id']);
                $contacto = $oPERSONA->getMediosContacto($persona['Persona']['id'],true);
                $rendicion['LiquidacionSocioRendicion']['persona_contacto_movil'] = $contacto['telefono_movil'];
                
                
				$rendicion['LiquidacionSocioRendicion']['descripcion'] = "";
				$rendicion['LiquidacionSocioRendicion']['empresa'] = "";
				$rendicion['LiquidacionSocioRendicion']['turno_pago'] = "";		

				$liqSoc = $oLS->getLiquidacionBySocio($rendicion['LiquidacionSocioRendicion']['socio_id'],$rendicion['LiquidacionSocioRendicion']['liquidacion_id']);
				
				if(isset($liqSoc[0])):
				
					$liqSoc = $liqSoc[0];
					$rendicion['LiquidacionSocioRendicion']['descripcion'] = $oTURNO->getDescripcionByTruno($liqSoc['LiquidacionSocio']['turno_pago']);
					$rendicion['LiquidacionSocioRendicion']['empresa'] = $oTURNO->GlobalDato('concepto_1',$liqSoc['LiquidacionSocio']['codigo_empresa']);
					$rendicion['LiquidacionSocioRendicion']['turno_pago'] = $liqSoc['LiquidacionSocio']['turno_pago'];
					if( $liqSoc['LiquidacionSocio']['codigo_empresa'] == 'MUTUEMPR') $rendicion['LiquidacionSocioRendicion']['empresa'] = "**S/D**";
					
					if(empty($liqSoc['LiquidacionSocio']['turno_pago']) || $liqSoc['LiquidacionSocio']['turno_pago'] == "SDATO"){
						$rendicion['LiquidacionSocioRendicion']['empresa'] = "*** SIN DATOS ***";
						$rendicion['LiquidacionSocioRendicion']['turno_pago'] = "SDATO";
					}
					
					
				endif;
				//AGREGO LOS DATOS DE LA CUENTA BANCARIA SI LOS TIENE
				$rendicion['LiquidacionSocioRendicion']['banco_id_nombre'] = null;
				$rendicion['LiquidacionSocioRendicion']['banco_sucursal_cuenta'] = null;
				if(!empty($rendicion['LiquidacionSocioRendicion']['banco_id'])):
					$rendicion['LiquidacionSocioRendicion']['banco_id_nombre'] = $oTURNO->getNombreBanco($rendicion['LiquidacionSocioRendicion']['banco_id']);
					$sucursal = substr(str_pad(trim($rendicion['LiquidacionSocioRendicion']['sucursal']),5,'0',STR_PAD_LEFT),-5);
					$cuenta = substr(str_pad(trim($rendicion['LiquidacionSocioRendicion']['nro_cta_bco']),11,'0',STR_PAD_LEFT),-11);
					$rendicion['LiquidacionSocioRendicion']['banco_sucursal_cuenta'] =  $sucursal . "-" . $cuenta;
				endif;
				
//				debug($rendicion);
				$rendicion['LiquidacionSocioRendicion']['banco_intercambio_nombre'] = $oTURNO->getNombreBanco($rendicion['LiquidacionSocioRendicion']['banco_intercambio']);
				
				$intercambio = $oFILE->read('archivo_nombre',$rendicion['LiquidacionSocioRendicion']['liquidacion_intercambio_id']);$rendicion['LiquidacionSocioRendicion']['banco_id_nombre'] = $oTURNO->getNombreBanco($rendicion['LiquidacionSocioRendicion']['banco_id']);
				
				
				$asinc->actualizar($i,$total,"PROCESANDO|" . $intercambio['LiquidacionIntercambio']['archivo_nombre'] . "|" . $rendicion['LiquidacionSocioRendicion']['persona_nombre']);
				
				$temp = array();
				
				$temp['AsincronoTemporal'] = array(
						'asincrono_id' => $asinc->id,
						'clave_1' => 'REPORTE_1',
						'texto_1' => $rendicion['LiquidacionSocioRendicion']['persona_documento'],
						'texto_2' => $rendicion['LiquidacionSocioRendicion']['persona_nombre'],
						'texto_3' => $rendicion['LiquidacionSocioRendicion']['socio_id'],
						'texto_4' => $rendicion['LiquidacionSocioRendicion']['identificacion'],				
						'texto_5' => $rendicion['LiquidacionSocioRendicion']['empresa'],
						'texto_6' => substr(trim($rendicion['LiquidacionSocioRendicion']['turno_pago']),-5,5),
						'texto_7' => $rendicion['LiquidacionSocioRendicion']['descripcion'],	
						'texto_8' => $rendicion['LiquidacionSocioRendicion']['banco_intercambio_desc'],
						'texto_9' => $rendicion['LiquidacionSocioRendicion']['status'],			
						'texto_10' => $rendicion['LiquidacionSocioRendicion']['status_desc'],
						'texto_11' => ($rendicion['LiquidacionSocioRendicion']['indica_pago'] == 1 ? 'SI' : 'NO'),
						'texto_12' => date('d-m-Y',strtotime($rendicion['LiquidacionSocioRendicion']['fecha_debito'])),
						'texto_13' => $intercambio['LiquidacionIntercambio']['archivo_nombre'],
						'texto_14' => $rendicion['LiquidacionSocioRendicion']['banco_id_nombre'],
						'texto_15' => $rendicion['LiquidacionSocioRendicion']['banco_intercambio_nombre'],
                                                'texto_16' => $rendicion['LiquidacionSocioRendicion']['persona_domicilio'],
                                                'texto_17' => $rendicion['LiquidacionSocioRendicion']['persona_contacto'],
                                                'texto_18' => $rendicion['LiquidacionSocioRendicion']['persona_contacto_movil'],
                                                'texto_19' => $rendicion['LiquidacionSocioRendicion']['persona_domicilio_localidad'],
                                                'texto_20' => $rendicion['LiquidacionSocioRendicion']['persona_domicilio_provincia'],
						'decimal_1' => $rendicion['LiquidacionSocioRendicion']['importe_debitado'],
                                                'entero_1' => intval($rendicion['LiquidacionSocioRendicion']['socio_id']),
                                                'entero_2' => intval($rendicion['LiquidacionSocioRendicion']['indica_pago']),
                                    
				);
				
//				debug($temp);

				if(!$this->Temporal->grabar($temp)){
					$STOP = 1;
					break;
				}				
				
				if($asinc->detenido()){
					$STOP = 1;
					break;
				}				
				
				$i++;
		
			endforeach;
		
		
		endif;
		
                ##########################################################################################
                # TOTALIZO POR ESTADO NO COBRADO Y POR PERSONA
                ##########################################################################################
                $sql = "select texto_1,texto_2,texto_14,texto_4,texto_12,texto_9,texto_10,sum(decimal_1)
                        as decimal_1,texto_8,texto_18,texto_19,texto_20,texto_16 
                        from asincrono_temporales
                        where asincrono_id = $asinc->id
                        and clave_1 = 'REPORTE_1'
                        and entero_2 = 0
                        group by entero_1,entero_2;";
                $datos = $oLS->query($sql);
                if(!empty($datos)){
                    
                    $total = count($datos);
                    $asinc->setTotal($total);
                    $i = 0;                    
                    
                    foreach($datos as $dato){
                        
                        $asinc->actualizar($i,$total,"TOTALIZANDO NO COBRADOS|" . $dato['asincrono_temporales']['texto_1'] . "|" . $dato['asincrono_temporales']['texto_2']);
                        
                        $temp = array();
                        $temp['AsincronoTemporal'] = array(
                            'asincrono_id' => $asinc->id,
                            'clave_1' => 'REPORTE_2',
                            'texto_1' => $dato['asincrono_temporales']['texto_1'],
                            'texto_2' => $dato['asincrono_temporales']['texto_2'],
                            'texto_3' => $dato['asincrono_temporales']['texto_14'],
                            'texto_4' => $dato['asincrono_temporales']['texto_4'],
                            'texto_5' => $dato['asincrono_temporales']['texto_12'],
                            'texto_6' => $dato['asincrono_temporales']['texto_9'],
                            'texto_7' => $dato['asincrono_temporales']['texto_10'],
                            'texto_8' => $dato['asincrono_temporales']['texto_8'],
                            'texto_9' => $dato['asincrono_temporales']['texto_18'],
                            'texto_10' => $dato['asincrono_temporales']['texto_19'],
                            'texto_11' => $dato['asincrono_temporales']['texto_20'],
                            'texto_12' => $dato['asincrono_temporales']['texto_16'],
                            'decimal_1' => $dato[0]['decimal_1'],
                        );
                        
                        if(!$this->Temporal->grabar($temp)){
                                $STOP = 1;
                                break;
                        }				

                        if($asinc->detenido()){
                                $STOP = 1;
                                break;
                        }                        
                        
                        $i++;
                        
                    }
                    
                }                
                
                

		$asinc->actualizar(100,100,"FINALIZANDO...");
		$asinc->fin("**** PROCESO FINALIZADO ****");
		return;
		

	}
	//FIN PROCESO ASINCRONO
	

	

}
?>