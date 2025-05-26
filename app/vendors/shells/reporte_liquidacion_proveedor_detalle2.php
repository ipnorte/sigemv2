<?php

/**
 * Reporte Liquidacion Proveedor Detalle
 * 
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php reporte_liquidacion_proveedor_detalle2 885 -app /home/adrian/Desarrollo/www/sigem/app/
 * "D:\develop\xampp\php\php.exe" "D:\develop\xampp\htdocs\sigem\cake\console\cake.php" reporte_liquidacion_proveedor_detalle2 29 -app "D:\develop\xampp\htdocs\sigem\app\"
 * /usr/bin/php5 /home/adrian/trabajo/www/sigem/cake/console/cake.php reporte_liquidacion_proveedor_detalle2 19080 -app /home/adrian/trabajo/www/sigem/app/
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 *
 */

class ReporteLiquidacionProveedorDetalle2Shell extends Shell {

	var $fecha_desde;
	var $fecha_hasta;
	var $uses = array(
						'Mutual.Liquidacion',
						'Mutual.LiquidacionCuota',
						'Proveedores.Proveedor',
						'Mutual.OrdenDescuentoCuota',
						'Mutual.LiquidacionSocio',
						'Config.BancoRendicionCodigo',
						'Mutual.LiquidacionSocioRendicion',
	);
	var $liquidacion_id;
	var $proveedor_id;
	var $tipo_cuota;
	var $tipo_producto;
	var $oLiquidacion;
	var $procesarSobrePreImputacion;
	
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

		$this->liquidacion_id	= $asinc->getParametro('p1');
		$this->proveedor_id		= $asinc->getParametro('p2');
		$this->tipo_cuota		= $asinc->getParametro('p3');
		$this->tipo_producto	= $asinc->getParametro('p4');
		
		$this->procesarSobrePreImputacion	= $asinc->getParametro('p5');
		$this->procesarSobrePreImputacion = (!empty($this->procesarSobrePreImputacion) ? $this->procesarSobrePreImputacion : 0);
		
		
		$liquidacion =  $this->Liquidacion->cargar($this->liquidacion_id);
		
		$this->oLiquidacion = $liquidacion;
		
		$asinc->actualizar(1,100,"ESPERE, INICIANDO PROCESO...");
		$STOP = 0;
		$total = 0;
		$i = 0;
		$asinc->actualizar(0,100,"ESPERE, CONSULTANDO LIQUIDACION...");
		
		//limpio la tabla temporal
		if(!$this->Temporal->limpiarTabla($asinc->id)){
			$asinc->fin("SE PRODUJO UN ERROR...");
			return;
		}
		
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oCCUOTA = new OrdenDescuentoCobroCuota();

		App::import('Model','Pfyj.Socio');
		$oSOCIO = new Socio();	

		App::import('Helper','Util');
		$oUT = new UtilHelper();

		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oORDCUO = new OrdenDescuentoCuota();
        
		App::import('Model','Mutual.MutualProductoSolicitud');
		$oSOLICITUD = new MutualProductoSolicitud();        
		
		App::import('Model','proveedores.MetodoCalculoCuota');
		$oMETODO = new MetodoCalculoCuota();        

		$asinc->actualizar(1,100,"ESPERE, CARGANDO LISTADO DE SOCIOS A PROCESAR...");
		
		$socios = $this->__getSocios();
		
        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
        $CALC_IVA = false;
        if(isset($INI_FILE['general']['discrimina_iva']) && $INI_FILE['general']['discrimina_iva'] != 0){
            $CALC_IVA = $INI_FILE['general']['discrimina_iva'];
        }            
        
		//ADRIAN
//		$socios = null;
	
		if(!empty($socios)){
			
//			$asinc->actualizar(0,100,"INICIANDO PROCESAMIENTO DE SOCIOS...");
			
			$total = count($socios);
			$asinc->setTotal($total);
			$i = 0;			
			
			$temp = array();
			
//			debug($socios);

			foreach($socios as $socio){
				
				$cuotas = $this->__getDetalle($socio);
//				$cuotas = null;
				
				if(!empty($cuotas)):
				
					foreach($cuotas as $cuota):
					
                    
//                        debug($cuota);
                    
						$temp = array();
						$cuota['LiquidacionCuota']['documento'] = $cuota['GlobalDato']['concepto_1'] . " - " .  $cuota['Persona']['documento'];
						$cuota['LiquidacionCuota']['socio'] = strtoupper($cuota['Persona']['apellido'] . ", " .  $cuota['Persona']['nombre']);
						$cuota['LiquidacionCuota']['tipo_cuota_desc'] = $this->OrdenDescuentoCuota->GlobalDato('concepto_1',$cuota['LiquidacionCuota']['tipo_cuota']);
						$cuota['LiquidacionCuota']['producto_cuota'] = $this->OrdenDescuentoCuota->GlobalDato('concepto_1',$cuota['LiquidacionCuota']['tipo_producto'])." - ".$cuota['LiquidacionCuota']['tipo_cuota_desc'];
						$cuota['LiquidacionCuota']['tipo_nro'] = $cuota['OrdenDescuento']['tipo_orden_dto']." #".$cuota['OrdenDescuento']['numero'];
						$cuota['LiquidacionCuota']['cuota'] = str_pad($cuota['OrdenDescuentoCuota']['nro_cuota'],2,"0",STR_PAD_LEFT)."/".str_pad($cuota['OrdenDescuento']['cuotas'],2,"0",STR_PAD_LEFT);
						$cuota['LiquidacionCuota']['codigo_comercio_referencia'] = $cuota['OrdenDescuentoCuota']['codigo_comercio_referencia'];
						$cuota['LiquidacionCuota']['nro_referencia_proveedor'] = $cuota['OrdenDescuentoCuota']['nro_referencia_proveedor'];
						$cuota['LiquidacionCuota']['nro_orden_referencia'] = $cuota['OrdenDescuentoCuota']['nro_orden_referencia'];
						
                        
                        $SOLICITADO = $PERCIBIDO = 0;
                        
                        if($CALC_IVA != 0){
                            $solicitud = $oSOLICITUD->getOrdenByPersonaAndId($cuota['Persona']['id'], $cuota['OrdenDescuento']['numero']);
                            $SOLICITADO = $solicitud['MutualProductoSolicitud']['importe_solicitado'];
                            $PERCIBIDO = $solicitud['MutualProductoSolicitud']['importe_percibido'];
                            
                        }
                        
//                        debug($solicitud);
                        
                        $CAPITAL_CUOTA = $INTERES_CUOTA = $ADICIONAL_CUOTA = $IVA_CUOTA = $ERROR_IVA = 0;
                        if(!empty($SOLICITADO) && !empty($PERCIBIDO) && $this->proveedor_id != 18){
                            
                            $oMETODO->solicitado = $PERCIBIDO;
                            $oMETODO->cuotas = $solicitud['MutualProductoSolicitud']['cuotas'];
                            $oMETODO->porcAdic = round((($SOLICITADO - $PERCIBIDO) > 0 ? (($SOLICITADO - $PERCIBIDO) / $PERCIBIDO) * 100 : 0),2);
                            $oMETODO->porcIVA = $CALC_IVA;
                            $oMETODO->tasa = $oMETODO->determinar_tasa($solicitud['MutualProductoSolicitud']['importe_cuota']);
                            $oMETODO->set_valor_cuota();
                            if($oMETODO->error){
                                $ERROR_IVA = $oMETODO->error;
                                $oMETODO->reset();
                                $oMETODO->set_valor_cuota_directo($solicitud['MutualProductoSolicitud']['importe_cuota']);
                                $INTERES_CUOTA = $oMETODO->cuota['INTERES'];
                                $CAPITAL_CUOTA = $oMETODO->cuota['CAPITAL'];
                                $ADICIONAL_CUOTA = $oMETODO->cuota['ADICIONAL']; 
                                $IVA_CUOTA = $oMETODO->cuota['IVA'];                                
                            }else{
                                $oMETODO->armar_plan();
                                if(array_key_exists($cuota['OrdenDescuentoCuota']['nro_cuota'], $oMETODO->plan)){
                                    $INTERES_CUOTA = $oMETODO->plan[$cuota['OrdenDescuentoCuota']['nro_cuota']]['INTERES'];
                                    $CAPITAL_CUOTA = $oMETODO->plan[$cuota['OrdenDescuentoCuota']['nro_cuota']]['CAPITAL'];
                                    $ADICIONAL_CUOTA = $oMETODO->plan[$cuota['OrdenDescuentoCuota']['nro_cuota']]['ADICIONAL']; 
                                    $IVA_CUOTA = $oMETODO->plan[$cuota['OrdenDescuentoCuota']['nro_cuota']]['IVA'];                                     
                                }
                            }
//                            $INTERES_CUOTA = $oMETODO->cuota['INTERES'];
//                            $CAPITAL_CUOTA = $oMETODO->cuota['CAPITAL'];
//                            $ADICIONAL_CUOTA = $oMETODO->cuota['ADICIONAL']; 
//                            $IVA_CUOTA = $oMETODO->cuota['IVA']; 
                            
//                            debug($oMETODO->cuota);
                            
                        }
                        
						$asinc->actualizar($i,$total,"$i / $total  - PROCESANDO COBROS >> " . $cuota['LiquidacionCuota']['socio']);
						
						//calcular lo reversado
						$totalReversado = $oCCUOTA->getTotalReversoByProveedorByLiquidacion($this->proveedor_id,$this->liquidacion_id,$this->tipo_producto,$this->tipo_cuota,$socio);
						
						if($cuota['LiquidacionCuota']['comision_cobranza'] == 0 && isset($cuota['ProveedorComision']['comision'])):
							$comision = round($cuota['LiquidacionCuota']['importe_debitado'],2) * $cuota['ProveedorComision']['comision'] / 100;
						else:
							$comision = $cuota['LiquidacionCuota']['comision_cobranza'];
						endif;
						
						if(empty($cuota['ProveedorComision']['comision'])) $cuota['ProveedorComision']['comision'] = 0;
						
						if($cuota['LiquidacionCuota']['alicuota_comision_cobranza'] != 0) $cuota['ProveedorComision']['comision'] = $cuota['LiquidacionCuota']['alicuota_comision_cobranza'];
						
						$calificacion = $oSOCIO->getUltimaCalificacion($socio,$cuota['OrdenDescuentoCuota']['persona_beneficio_id'],true,true,true);
//						debug($calificacion);
//						debug($oUT->periodo($calificacion[2],true));
//						debug($oUT->armaFecha($calificacion[1]));
						
						//buscar el motivo del no cobro y generar el registro REPORTE_2
						
						//ARMO EL REGISTRO DE INTERCAMBIO
						$registro = '';
						if($cuota['LiquidacionCuota']['importe_debitado'] != 0):
	//						 * 0 	(1) 	-> tipo registro (1=recsueldo, 2=caja, 3=cancelaxcaja)
	//						 * 1 	(8) 	-> documento del socio
	//						 * 2 	(30) 	-> apenom socio
	//						 * 3 	(10) 	-> nro referencia proveedor
	//						 * 4 	(10) 	-> nro expediente
	//						 * 5 	(3) 	-> nro de cuota
	//						 * 6 	(10)	-> importe cobrado
	//						 * 7 	(10)	-> comision
	//						 * 8 	(10)	-> neto proveedor	
	//						 * 9	(8)		-> fecha debito (AAAAMMDD)					
							
							$intercambio = array();
							$intercambio[0] = 1;
							$intercambio[1] = $cuota['Persona']['documento'];
							$intercambio[2] = $cuota['Persona']['apellido']." ".$cuota['Persona']['nombre'];
							$intercambio[3] = $cuota['OrdenDescuentoCuota']['nro_referencia_proveedor'];
							$intercambio[4] = $cuota['OrdenDescuento']['numero'];
							$intercambio[5] = $cuota['OrdenDescuentoCuota']['nro_cuota'];
							$intercambio[6] = round($cuota['LiquidacionCuota']['importe_debitado'],2);
							$intercambio[7] = round($comision,2);
							$intercambio[8] = round($cuota['LiquidacionCuota']['importe_debitado'] - $comision,2);
							$intercambio[9] = $this->getFechaDebito($cuota['LiquidacionCuota']['socio_id']);
							
							$registro = $this->getRegistroIntercambio($intercambio);
						
						endif;
                        $comision_IVA = 0;
                        if($CALC_IVA != 0){
                            $comision_IVA = $comision * ($CALC_IVA/100);
                            $comision_IVA = round($comision_IVA,2);					
                        }                        
						
						$temp['AsincronoTemporal'] = array(
											
								'asincrono_id' => $asinc->id,
								'clave_1' => 'REPORTE_1',
								'texto_1' => $cuota['LiquidacionCuota']['documento'],
								'texto_2' => $cuota['LiquidacionCuota']['socio'],
								'texto_3' => $cuota['LiquidacionCuota']['tipo_nro'],
								'texto_4' => $cuota['LiquidacionCuota']['producto_cuota'],
								'texto_5' => $cuota['LiquidacionCuota']['tipo_cuota_desc'],
								'texto_6' => $cuota['LiquidacionCuota']['cuota'],
								'texto_7' => $cuota['LiquidacionCuota']['periodo_cuota'],
								'texto_8' => $cuota['LiquidacionCuota']['orden_descuento_cuota_id'],
								'texto_9' => trim($cuota['LiquidacionCuota']['nro_referencia_proveedor']),
								'texto_11' => $cuota['Persona']['documento'],
								'texto_12' => (!empty($cuota['Persona']['sexo']) ? $cuota['Persona']['sexo'] : 'M'),
								'texto_13' => $calificacion[0],
								'texto_14' => $oUT->periodo($calificacion[2],true,"/"),
								'texto_15' => $registro,
                                'texto_16' => ($ERROR_IVA == 1 ? "" : "SI"),
								'decimal_1' => round($cuota['LiquidacionCuota']['saldo_actual'],2),
								'decimal_2' => round($cuota['LiquidacionCuota']['importe_debitado'],2),
								'decimal_3' => round($cuota['LiquidacionCuota']['saldo_actual'] - $cuota['LiquidacionCuota']['importe_debitado'],2),
								'decimal_4' => round($cuota['ProveedorComision']['comision'],2),
								'decimal_5' => round($comision,2),
								'decimal_6' => round($cuota['LiquidacionCuota']['importe_debitado'] - $comision - $comision_IVA,2),
								'decimal_7' => round($totalReversado,2),
								'decimal_8' => round($cuota['LiquidacionCuota']['importe'],2),
                                'decimal_9' => round($comision_IVA,2),
                                'decimal_10' => round($CAPITAL_CUOTA,2),
                                'decimal_11' => round($ADICIONAL_CUOTA,2),
                                'decimal_12' => round($INTERES_CUOTA,2),
                                'decimal_13' => round($IVA_CUOTA,2),
                                'decimal_14' => round($cuota['LiquidacionCuota']['importe'],2),
								'entero_1' => $liquidacion['Liquidacion']['periodo'],
								'entero_2' => $socio,
								'entero_3' => 1,
								
						);	
						
					
//						debug($temp);
						
						if($asinc->detenido()){
							$STOP = 1;
							break;
						}				
	
						if(!$this->Temporal->grabar($temp)){
							$STOP = 1;
							break;
						}						
						
					endforeach;
					
					#BUSCO LA INFORMACION DEL DESCUENTO
				
				
				endif;
				
				$i++;
					
			}
			
		}
		
		$asinc->actualizar(1,100,"ESPERE, INICIANDO PROCESO NO COBRADOS...");

		$noCobrados = $this->__getDetalleNoCobradosDiscriminado();
		
		//ADRIAN
//		$noCobrados = NULL;
		
		if(!empty($noCobrados)):
		
			$total = count($noCobrados);
			$asinc->setTotal($total);
			$i = 0;	
			
			$temp = array();
			
			foreach($noCobrados as $cuota):
			
			
//				debug($cuota);
			
				$cuota['LiquidacionCuota']['documento'] = $cuota['GlobalDato']['concepto_1'] . " - " .  $cuota['Persona']['documento'];
				$cuota['LiquidacionCuota']['socio'] = strtoupper($cuota['Persona']['apellido'] . ", " .  $cuota['Persona']['nombre']);
				$cuota['LiquidacionCuota']['tipo_cuota_desc'] = $this->OrdenDescuentoCuota->GlobalDato('concepto_1',$cuota['LiquidacionCuota']['tipo_cuota']);
				$cuota['LiquidacionCuota']['producto_cuota'] = $this->OrdenDescuentoCuota->GlobalDato('concepto_1',$cuota['LiquidacionCuota']['tipo_producto'])." - ".$cuota['LiquidacionCuota']['tipo_cuota_desc'];
				$cuota['LiquidacionCuota']['tipo_nro'] = $cuota['OrdenDescuento']['tipo_orden_dto']." #".$cuota['OrdenDescuento']['numero'];
				$cuota['LiquidacionCuota']['cuota'] = str_pad($cuota['OrdenDescuentoCuota']['nro_cuota'],2,"0",STR_PAD_LEFT)."/".str_pad($cuota['OrdenDescuento']['cuotas'],2,"0",STR_PAD_LEFT);
				$cuota['LiquidacionCuota']['codigo_comercio_referencia'] = $cuota['OrdenDescuentoCuota']['codigo_comercio_referencia'];
				$cuota['LiquidacionCuota']['nro_referencia_proveedor'] = $cuota['OrdenDescuentoCuota']['nro_referencia_proveedor'];
				$cuota['LiquidacionCuota']['nro_orden_referencia'] = $cuota['OrdenDescuentoCuota']['nro_orden_referencia'];
			
				$calificacion = $oSOCIO->getUltimaCalificacion($cuota['LiquidacionCuota']['socio_id'],$cuota['OrdenDescuentoCuota']['persona_beneficio_id'],true,true,true);
				
				$asinc->actualizar($i,$total,"$i / $total - PROCESANDO NO COBRADOS >> " . $cuota['LiquidacionCuota']['socio']);
				
				$cuota['LiquidacionCuota']['ultima_calificacion'] = $calificacion[0];
				
				if(!empty($cuota['LiquidacionSocioRendicion']['banco_intercambio'])){
					
					$descripcionCodigo = $this->BancoRendicionCodigo->getDescripcionCodigo($cuota['LiquidacionSocioRendicion']['banco_intercambio'],$cuota['LiquidacionSocioRendicion']['status']);
					$bancoIntercambio = $this->LiquidacionCuota->getNombreBanco($cuota['LiquidacionSocioRendicion']['banco_intercambio']);
					$indicaPago = $cuota['LiquidacionSocioRendicion']['indica_pago'];
					$codigo_status = $cuota['LiquidacionSocioRendicion']['status'];
					
					if($indicaPago == 1){
						
						$descripcionCodigo = "PRORRATEO";
						$codigo_status = "PRO";
						
					}
					
				}else if(substr($liquidacion['Liquidacion']['codigo_organismo'],8,2) == "22"){

					//si es un cbu informo el no envio a descuento
					$descripcionCodigo = "PENDIENTE DE INFORMAR";
					$bancoIntercambio = "";
					$indicaPago = 0;
					$codigo_status = "PIN";
					
				}else{

					$descripcionCodigo = "RETENCION NO INFORMADA POR EL ORGANISMO";
					$bancoIntercambio = "";
					$indicaPago = 0;
					$codigo_status = "NIN";
										
				}
				
				$cuota['LiquidacionCuota']['banco_intercambio'] = $bancoIntercambio;
				$cuota['LiquidacionCuota']['status'] = $codigo_status;
				$cuota['LiquidacionCuota']['descripcion_status_debito'] = $descripcionCodigo;
				$cuota['LiquidacionCuota']['indica_pago'] = $indicaPago;
				
				
//				debug($cuota['LiquidacionCuota']);
				
				$temp = array();
				
				$temp['AsincronoTemporal'] = array(
									
						'asincrono_id' => $asinc->id,
						'clave_1' => 'REPORTE_2',
						'texto_1' => $cuota['LiquidacionCuota']['documento'],
						'texto_2' => $cuota['LiquidacionCuota']['socio'],
						'texto_3' => $cuota['LiquidacionCuota']['tipo_nro'],
						'texto_4' => $cuota['LiquidacionCuota']['producto_cuota'],
						'texto_5' => $cuota['LiquidacionCuota']['tipo_cuota_desc'],
						'texto_6' => $cuota['LiquidacionCuota']['cuota'],
						'texto_7' => $cuota['LiquidacionCuota']['periodo_cuota'],
						'texto_8' => $cuota['LiquidacionCuota']['orden_descuento_cuota_id'],
						'texto_9' => $cuota['LiquidacionCuota']['nro_referencia_proveedor'],
						'texto_11' => $cuota['LiquidacionCuota']['status'],
						'texto_12' => $cuota['LiquidacionCuota']['descripcion_status_debito'],
						'texto_13' => $calificacion[0],
						'texto_14' => $oUT->periodo($calificacion[2],true,"/"),
						'decimal_1' => round($cuota['LiquidacionCuota']['saldo_actual'] - $cuota['LiquidacionCuota']['importe_debitado'],2),
						'entero_2' => $cuota['LiquidacionCuota']['socio_id'],
						'entero_3' => 1,
						
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
				
			
			endforeach;
			
		endif;
		
		
		
		####################################################################
		$cobrosByCaja = $this->getCobradoPorCaja();
		####################################################################
		
		
		//ADRIAN
//		$cobrosByCaja = null;
		
		$asinc->actualizar(1,100,"ESPERE, INICIANDO PROCESO OTRAS COBRANZAS...");
		
		if(!empty($cobrosByCaja)):

			$total = count($cobrosByCaja);
			$asinc->setTotal($total);
			$i = 0;	
			
			$temp = array();	

			
			App::import('Model','Mutual.CancelacionOrden');
			$oCANCELACION = new CancelacionOrden();			
			
			App::import('Model','Mutual.OrdenDescuentoCobro');
			$oCOBRO = new OrdenDescuentoCobro();
			App::import('model','Mutual.OrdenDescuentoCuota');
			$oCUOTA = new OrdenDescuentoCuota();	
	
			App::import('model','Pfyj.Socio');
			$oSOCIO = new Socio();						
			
			foreach($cobrosByCaja as $cobro):
			
				$cancelacion = null;
				
//				debug($cobro);
				
				if(!empty($cobro['OrdenDescuentoCobro']['cancelacion_orden_id'])) $cancelacion = $oCANCELACION->get($cobro['OrdenDescuentoCobro']['cancelacion_orden_id']);
				if(!empty($cancelacion)) $cancelacion = $cancelacion['CancelacionOrden'];

//				debug($cancelacion);
				
				$cobro['OrdenDescuentoCobro']['tipo_cobro_desc'] = $oCOBRO->GlobalDato('concepto_1',$cobro['OrdenDescuentoCobro']['tipo_cobro']);
				$cobro['OrdenDescuentoCobro']['apenom'] = $oSOCIO->getApenom($cobro['OrdenDescuentoCobro']['socio_id'],false);
				$cobro['OrdenDescuentoCobro']['tdocndoc'] = $oSOCIO->getTdocNdoc($cobro['OrdenDescuentoCobro']['socio_id']);
				
				$asinc->actualizar($i,$total,"$i / $total - PROCESANDO OTROS COBROS >> " . $cobro['OrdenDescuentoCobro']['apenom']);
				
				
				$cuota = $oCUOTA->getCuota($cobro['OrdenDescuentoCobroCuota']['orden_descuento_cuota_id']);
				
				if(!empty($cuota)):
				
					$cuota = $cuota['OrdenDescuentoCuota'];
					
					$temp = array();
					
					$temp['AsincronoTemporal'] = array(
										
							'asincrono_id' => $asinc->id,
							'clave_1' => 'REPORTE_3',
							'texto_1' => $cobro['OrdenDescuentoCobro']['tdocndoc'],
							'texto_2' => $cobro['OrdenDescuentoCobro']['apenom'],
							'texto_3' => $cobro['OrdenDescuentoCobro']['tipo_cobro'],
							'texto_4' => $cobro['OrdenDescuentoCobro']['tipo_cobro_desc'],
							'texto_5' => $cobro['OrdenDescuentoCobro']['fecha'],
							'texto_6' => $cobro['OrdenDescuentoCobro']['periodo_cobro'],
							'texto_7' => $cuota['orden_descuento_id'],
							'texto_8' => $cuota['tipo_nro'],
							'texto_9' => $cuota['proveedor_producto']." - ".$cuota['tipo_cuota_desc'],
							'texto_11' => $cuota['cuota'],
							'texto_12' => (!empty($cancelacion) ? $cancelacion['forma_cancelacion_desc'] : ""),
							'texto_13' => (!empty($cancelacion) ? ($cancelacion['origen_proveedor_id'] == $cancelacion['orden_proveedor_id'] ? "PROPIA" : "DE TERCEROS") : ""),
							'texto_14' => $cobro['OrdenDescuentoCuota']['nro_referencia_proveedor'],
							'decimal_1' => round($cobro['OrdenDescuentoCobroCuota']['importe'],2),
							'entero_1' => $cobro['OrdenDescuentoCobro']['id'],
							'entero_2' => $cobro['OrdenDescuentoCobro']['cancelacion_orden_id'],
							'entero_3' => $cobro['OrdenDescuentoCobro']['socio_id'],
					);	
					
					if(!$this->Temporal->grabar($temp)){
						$STOP = 1;
						break;
					}				
			
					if($asinc->detenido()){
						$STOP = 1;
						break;
					}						
					
				
				endif;
					
					
				
				$i++;
			
			endforeach;
			
		
		endif;
		
		#################################################################################
		
		$asinc->actualizar(1,100,"ESPERE, INICIANDO PROCESO ANALISIS DE BAJAS...");
		
		$bajas = $this->OrdenDescuentoCuota->getCuotasBajaByPeriodoOrganismoProveedor($liquidacion['Liquidacion']['periodo'],$liquidacion['Liquidacion']['codigo_organismo'],$this->proveedor_id);
		
		
		if(!empty($bajas)){
			
			$total = count($bajas);
			$asinc->setTotal($total);
			$i = 0;				
			
			foreach($bajas as $baja):
			
				$asinc->actualizar($i,$total,"$i / $total - INFORMANDO BAJAS >> " . $baja['persona_apenom']);
				
				$temp = array();
				$temp['AsincronoTemporal'] = array(
							'asincrono_id' => $asinc->id,
							'clave_1' => 'REPORTE_4',
							'texto_1' => $baja['persona_tdocndoc'],
							'texto_2' => $baja['persona_apenom'],
							'texto_3' => $baja['beneficio'],
							'texto_4' => $baja['tipo_nro'],
							'texto_5' => $baja['proveedor_producto'],
							'texto_6' => $baja['tipo_cuota_desc'],
							'texto_7' => $baja['situacion_desc'],
							'texto_8' => $baja['periodo_d'],
							'texto_9' => $baja['cuota'],
							'texto_10' => $baja['nro_referencia_proveedor'],
							'texto_11' => $baja['producto_cuota_ref'],
							'decimal_1' => round($baja['importe'],2),
							'entero_1' => $baja['socio_id'],
							'entero_2' => $baja['orden_descuento_id'],
							'entero_3' => $baja['cuota'],
				
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
			
			endforeach;
			
		}
		

		
		#################################################################################
		$asinc->actualizar(1,100,"ESPERE, INICIANDO PROCESO ANALISIS DE STOP DEBIT...");
		#################################################################################
		
		//SACO LOS PERIODOS ANTERIORES AL ACTUAL PARA EL MISMO ORGANISMO 
		$sql = "SELECT id,periodo from liquidaciones as Liquidacion 
				WHERE Liquidacion.periodo < '".$this->oLiquidacion['Liquidacion']['periodo']."' AND 
				Liquidacion.codigo_organismo = '".$this->oLiquidacion['Liquidacion']['codigo_organismo']."'
				GROUP BY Liquidacion.id 
				ORDER BY Liquidacion.periodo;";
		
		$liquidacionesAnteriores = $this->Liquidacion->query($sql);
		
		$total = count($liquidacionesAnteriores);
		$asinc->setTotal($total);
		$i = 0;			
		
		if(!empty($liquidacionesAnteriores)):

			foreach($liquidacionesAnteriores as $liquidacionAnterior):
			
				$liqAntId = $liquidacionAnterior['Liquidacion']['id'];
				$liqAntPe = $liquidacionAnterior['Liquidacion']['periodo'];
				
				$periodoDesc = $oUT->periodo($liqAntPe,true,"/");
				
				$this->out($liqAntId."\t".$liqAntPe);
				
				$sql = "SELECT
				 
							LiquidacionSocio.documento,
							LiquidacionSocio.apenom,
							Liquidacion.periodo,
							OrdenDescuento.id,
							OrdenDescuento.tipo_orden_dto,
							OrdenDescuento.numero,
							LiquidacionSocioRendicion.socio_id,
							SUM(OrdenDescuentoCuota.importe) AS deuda
							
						FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion
						
						INNER JOIN banco_rendicion_codigos AS BancoRendicionCodigo
							ON (BancoRendicionCodigo.banco_id = LiquidacionSocioRendicion.banco_intercambio
							AND BancoRendicionCodigo.codigo = LiquidacionSocioRendicion.status)
							
						INNER JOIN liquidacion_cuotas AS LiquidacionCuota 
							ON (LiquidacionCuota.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id
							AND LiquidacionCuota.socio_id = LiquidacionSocioRendicion.socio_id)
							
						INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = LiquidacionSocioRendicion.liquidacion_id)
							
						INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota
							ON (OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id)
							
						INNER JOIN orden_descuentos AS OrdenDescuento
							ON(OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
							
						INNER JOIN liquidacion_socios AS LiquidacionSocio 
							ON (LiquidacionSocio.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id
							AND LiquidacionSocio.socio_id = LiquidacionSocioRendicion.socio_id)
							
						WHERE 
							LiquidacionSocioRendicion.liquidacion_id = $liqAntId 
							AND LiquidacionSocioRendicion.indica_pago = 0
							AND BancoRendicionCodigo.calificacion_socio = 'MUTUCALISDEB' 
							AND LiquidacionCuota.proveedor_id = ".$this->proveedor_id."
							AND OrdenDescuentoCuota.situacion = 'MUTUSICUMUTU'
							AND OrdenDescuentoCuota.estado <> 'B'
							AND OrdenDescuentoCuota.importe > (SELECT SUM(importe)
								FROM orden_descuento_cobro_cuotas AS OrdenDescuentoCobroCuota WHERE
								OrdenDescuentoCobroCuota.orden_descuento_cuota_id = OrdenDescuentoCuota.id)

						GROUP BY 
							LiquidacionSocio.documento,
							LiquidacionSocio.apenom,
							Liquidacion.periodo,
							OrdenDescuento.id,
							OrdenDescuento.tipo_orden_dto,
							OrdenDescuento.numero,
							LiquidacionSocioRendicion.socio_id
							
						ORDER BY LiquidacionSocio.apenom";
				
				$datos = $this->Liquidacion->query($sql);
				
				if(!empty($datos)):
				
					foreach($datos as $dato):
					
					
						
					
						$temp = array();
						$temp['AsincronoTemporal'] = array();
						$temp['AsincronoTemporal']['asincrono_id'] = $asinc->id;
						$temp['AsincronoTemporal']['clave_1'] = 'REPORTE_5';
						$temp['AsincronoTemporal']['clave_2'] = $liqAntPe;
						$temp['AsincronoTemporal']['texto_1'] = $dato['LiquidacionSocio']['documento'];
						$temp['AsincronoTemporal']['texto_2'] = $dato['LiquidacionSocio']['apenom'];
						$temp['AsincronoTemporal']['texto_3'] = $dato['OrdenDescuento']['id'];
						$temp['AsincronoTemporal']['texto_4'] = $dato['OrdenDescuento']['tipo_orden_dto']." #".$dato['OrdenDescuento']['numero'];
						$temp['AsincronoTemporal']['texto_5'] = $periodoDesc;
						//$periodoDesc
					
						//traigo los saldos
						$saldos = $this->OrdenDescuentoCuota->getSaldosByOrdenDto($dato['OrdenDescuento']['id'],$liqAntPe,$this->oLiquidacion['Liquidacion']['codigo_organismo']);
						
						$temp['AsincronoTemporal']['decimal_1'] = $saldos['saldo'];
						$temp['AsincronoTemporal']['decimal_2'] = $saldos['importe_devengado'];
						$temp['AsincronoTemporal']['decimal_3'] = $saldos['importe_vencido'];
						$temp['AsincronoTemporal']['decimal_4'] = $saldos['importe_avencer'];
						$temp['AsincronoTemporal']['decimal_5'] = $saldos['importe_pagado'];
						
						
						$this->Temporal->grabar($temp);
						
						$asinc->actualizar($i,$total,"$i / $total - ANALIZANDO STOP DEBIT >> " . $periodoDesc . " | " . $dato['LiquidacionSocio']['apenom']);
					
					endforeach; //endforeach datos
					
				
				endif; //emptydatos
				
			$i++;
			endforeach; //endforeach $liquidacionesAnteriores
		
		endif; //endif $liquidacionesAnteriores
		#################################################################################	
		
		
		
		
		if($STOP == 0){
			$asinc->actualizar(99,100,"FINALIZANDO...");
			$asinc->fin("**** PROCESO FINALIZADO ****");
		}			
		
		
		
	}
	//FIN PROCESO ASINCRONO
	
	####################################################################################################
	# METODOS ESPECIFICOS DEL PROCESO
	####################################################################################################
	
	function __getSocios(){
		$conditions = array();
		$conditions['LiquidacionCuota.liquidacion_id'] = $this->liquidacion_id;
		$conditions['LiquidacionCuota.proveedor_id'] = $this->proveedor_id;
		if(!empty($this->tipo_producto)) $conditions['LiquidacionCuota.tipo_producto'] = $this->tipo_producto;
		if(!empty($this->tipo_cuota)) $conditions['LiquidacionCuota.tipo_cuota'] = $this->tipo_cuota;
		
//		$conditions['LiquidacionCuota.socio_id'] = 15158;

		$cuotas = $this->LiquidacionCuota->find('all',array(
																	'joins'	=> array(
		
																		array(
																			'table' => 'socios',
																			'alias' => 'Socio',
																			'type' => 'inner',
																			'foreignKey' => false,
																			'conditions' => array('LiquidacionCuota.socio_id = Socio.id')
																			),			
																		array(
																			'table' => 'personas',
																			'alias' => 'Persona',
																			'type' => 'inner',
																			'foreignKey' => false,
																			'conditions' => array('Socio.persona_id = Persona.id')
																			),								
																		array(
																			'table' => 'global_datos',
																			'alias' => 'GlobalDato',
																			'type' => 'inner',
																			'foreignKey' => false,
																			'conditions' => array('GlobalDato.id = Persona.tipo_documento')
																			),
																		array(
																			'table' => 'orden_descuentos',
																			'alias' => 'OrdenDescuento',
																			'type' => 'inner',
																			'foreignKey' => false,
																			'conditions' => array('OrdenDescuento.id = LiquidacionCuota.orden_descuento_id')
																			),	
																		array(
																			'table' => 'orden_descuento_cuotas',
																			'alias' => 'OrdenDescuentoCuota',
																			'type' => 'inner',
																			'foreignKey' => false,
																			'conditions' => array('OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id')
																			),																																																																											
//																		array(
//																			'table' => 'proveedor_comisiones',
//																			'alias' => 'ProveedorComision',
//																			'type' => 'left',
//																			'foreignKey' => false,
//																			'conditions' => array(
//																									"ProveedorComision.proveedor_id = LiquidacionCuota.proveedor_id",
//																									"ProveedorComision.codigo_organismo = LiquidacionCuota.codigo_organismo",
//																									"ProveedorComision.tipo_producto = LiquidacionCuota.tipo_producto",
//																									"ProveedorComision.tipo_cuota = LiquidacionCuota.tipo_cuota",
//																									"ProveedorComision.tipo = 'COB'",
//																									"ProveedorComision.comision > 0",
//																							)
//																			),																				
																	),															
																	'conditions' => $conditions,
																	'fields' => array("LiquidacionCuota.socio_id"),
																	'group' => array("LiquidacionCuota.socio_id"),
																	'order' => array("Persona.apellido,Persona.nombre")															
		));	
		$cuotas = Set::extract("/LiquidacionCuota/socio_id",$cuotas);
		return $cuotas;		
	}
	

	function __getDetalle($socio_id){
		
		$conditions = array();
		$conditions['LiquidacionCuota.liquidacion_id'] = $this->liquidacion_id;
		$conditions['LiquidacionCuota.socio_id'] = $socio_id;
		$conditions['LiquidacionCuota.proveedor_id'] = $this->proveedor_id;
		if(!empty($this->tipo_producto)) $conditions['LiquidacionCuota.tipo_producto'] = $this->tipo_producto;
		if(!empty($this->tipo_cuota)) $conditions['LiquidacionCuota.tipo_cuota'] = $this->tipo_cuota;
		
		$cuotas = $this->LiquidacionCuota->find('all',array(
																	'joins'	=> array(
		
																		array(
																			'table' => 'socios',
																			'alias' => 'Socio',
																			'type' => 'inner',
																			'foreignKey' => false,
																			'conditions' => array('LiquidacionCuota.socio_id = Socio.id')
																			),			
																		array(
																			'table' => 'personas',
																			'alias' => 'Persona',
																			'type' => 'inner',
																			'foreignKey' => false,
																			'conditions' => array('Socio.persona_id = Persona.id')
																			),								
																		array(
																			'table' => 'global_datos',
																			'alias' => 'GlobalDato',
																			'type' => 'inner',
																			'foreignKey' => false,
																			'conditions' => array('GlobalDato.id = Persona.tipo_documento')
																			),
																		array(
																			'table' => 'orden_descuentos',
																			'alias' => 'OrdenDescuento',
																			'type' => 'inner',
																			'foreignKey' => false,
																			'conditions' => array('OrdenDescuento.id = LiquidacionCuota.orden_descuento_id')
																			),	
																		array(
																			'table' => 'orden_descuento_cuotas',
																			'alias' => 'OrdenDescuentoCuota',
																			'type' => 'inner',
																			'foreignKey' => false,
																			'conditions' => array('OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id')
																			),																																																																											
//																		array(
//																			'table' => 'proveedor_comisiones',
//																			'alias' => 'ProveedorComision',
//																			'type' => 'left',
//																			'foreignKey' => false,
//																			'conditions' => array(
//																									"ProveedorComision.proveedor_id = LiquidacionCuota.proveedor_id",
//																									"ProveedorComision.codigo_organismo = LiquidacionCuota.codigo_organismo",
//																									"ProveedorComision.tipo_producto = LiquidacionCuota.tipo_producto",
//																									"ProveedorComision.tipo_cuota = LiquidacionCuota.tipo_cuota",
//																									"ProveedorComision.tipo = 'COB'",
//																									"ProveedorComision.comision > 0",
//																							)
//																			),																				
																	),															
																	'conditions' => $conditions,
//																	'fields' => array(
//																						'GlobalDato.concepto_1,
//																						Persona.documento,
//																						Persona.apellido,
//																						Persona.nombre,
//																						Persona.sexo,
//																						LiquidacionCuota.socio_id,
//																						LiquidacionCuota.orden_descuento_cuota_id,
//																						LiquidacionCuota.tipo_cuota,
//																						LiquidacionCuota.tipo_producto,
//																						LiquidacionCuota.periodo_cuota,
//																						LiquidacionCuota.importe,
//																						LiquidacionCuota.saldo_actual,
//																						LiquidacionCuota.importe_debitado,
//																						LiquidacionCuota.alicuota_comision_cobranza,
//																						LiquidacionCuota.comision_cobranza,
//																						ProveedorComision.comision,
//																						OrdenDescuentoCuota.persona_beneficio_id,
//																						OrdenDescuento.tipo_orden_dto,
//																						OrdenDescuento.numero,
//																						OrdenDescuento.cuotas,
//																						OrdenDescuentoCuota.nro_cuota,
//																						OrdenDescuentoCuota.importe,
//																						OrdenDescuentoCuota.codigo_comercio_referencia,
//																						OrdenDescuentoCuota.nro_referencia_proveedor,
//																						OrdenDescuentoCuota.nro_orden_referencia'	
//																						
//																					),
																	'fields' => array(
																						'GlobalDato.concepto_1,
                                                                                        Persona.id,    
																						Persona.documento,
																						Persona.apellido,
																						Persona.nombre,
																						Persona.sexo,
																						LiquidacionCuota.socio_id,
																						LiquidacionCuota.orden_descuento_cuota_id,
																						LiquidacionCuota.tipo_cuota,
																						LiquidacionCuota.tipo_producto,
																						LiquidacionCuota.periodo_cuota,
																						LiquidacionCuota.importe,
																						LiquidacionCuota.saldo_actual,
																						LiquidacionCuota.importe_debitado,
																						LiquidacionCuota.alicuota_comision_cobranza,
																						LiquidacionCuota.comision_cobranza,
																						OrdenDescuentoCuota.persona_beneficio_id,
																						OrdenDescuento.tipo_orden_dto,
																						OrdenDescuento.numero,
																						OrdenDescuento.cuotas,
																						OrdenDescuentoCuota.nro_cuota,
																						OrdenDescuentoCuota.importe,
																						OrdenDescuentoCuota.codigo_comercio_referencia,
																						OrdenDescuentoCuota.nro_referencia_proveedor,
																						OrdenDescuentoCuota.nro_orden_referencia'																							
																					),
																	'order' => array('Persona.apellido,Persona.nombre,
																						LiquidacionCuota.orden_descuento_id,
																						LiquidacionCuota.periodo_cuota'
																					)															
		));	
		
		return $cuotas;
		
	}
	
	
	function __getDetalleNoCobradosDiscriminado(){
		$conditions = array();
		$conditions['LiquidacionCuota.liquidacion_id'] = $this->liquidacion_id;
		$conditions['LiquidacionCuota.proveedor_id'] = $this->proveedor_id;
//		$conditions['LiquidacionCuota.imputada'] = 0;
		$conditions['(LiquidacionCuota.saldo_actual - LiquidacionCuota.importe_debitado) > '] = 0;
		if(!empty($this->tipo_producto)) $conditions['LiquidacionCuota.tipo_producto'] = $this->tipo_producto;
		if(!empty($this->tipo_cuota)) $conditions['LiquidacionCuota.tipo_cuota'] = $this->tipo_cuota;

//		$conditions['LiquidacionCuota.socio_id'] = 15158;
		
		$cuotas = $this->LiquidacionCuota->find('all',array(
																	'joins'	=> array(
		
																		array(
																			'table' => 'socios',
																			'alias' => 'Socio',
																			'type' => 'inner',
																			'foreignKey' => false,
																			'conditions' => array('LiquidacionCuota.socio_id = Socio.id')
																			),			
																		array(
																			'table' => 'personas',
																			'alias' => 'Persona',
																			'type' => 'inner',
																			'foreignKey' => false,
																			'conditions' => array('Socio.persona_id = Persona.id')
																			),								
																		array(
																			'table' => 'global_datos',
																			'alias' => 'GlobalDato',
																			'type' => 'inner',
																			'foreignKey' => false,
																			'conditions' => array('GlobalDato.id = Persona.tipo_documento')
																			),
																		array(
																			'table' => 'orden_descuentos',
																			'alias' => 'OrdenDescuento',
																			'type' => 'inner',
																			'foreignKey' => false,
																			'conditions' => array('OrdenDescuento.id = LiquidacionCuota.orden_descuento_id')
																			),	
																		array(
																			'table' => 'orden_descuento_cuotas',
																			'alias' => 'OrdenDescuentoCuota',
																			'type' => 'inner',
																			'foreignKey' => false,
																			'conditions' => array('OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id')
																			),																																																																											
																		array(
																			'table' => 'liquidacion_socio_rendiciones',
																			'alias' => 'LiquidacionSocioRendicion',
																			'type' => 'left',
																			'foreignKey' => false,
																			'conditions' => array(
																									"LiquidacionSocioRendicion.liquidacion_id = " . $this->liquidacion_id,
																									"LiquidacionSocioRendicion.socio_id = LiquidacionCuota.socio_id",
																									"IFNULL(LiquidacionSocioRendicion.status,'') <> ''",
																							)
																			),																				
																	),															
																	'conditions' => $conditions,
																	'fields' => array(
																						'GlobalDato.concepto_1,
																						Persona.documento,
																						Persona.apellido,
																						Persona.nombre,
																						Persona.sexo,
																						LiquidacionCuota.socio_id,
																						LiquidacionCuota.orden_descuento_cuota_id,
																						LiquidacionCuota.tipo_cuota,
																						LiquidacionCuota.tipo_producto,
																						LiquidacionCuota.periodo_cuota,
																						LiquidacionCuota.importe,
																						LiquidacionCuota.saldo_actual,
																						LiquidacionCuota.importe_debitado,
																						OrdenDescuentoCuota.persona_beneficio_id,
																						OrdenDescuento.tipo_orden_dto,
																						OrdenDescuento.numero,
																						OrdenDescuento.cuotas,
																						OrdenDescuentoCuota.nro_cuota,
																						OrdenDescuentoCuota.importe,
																						OrdenDescuentoCuota.codigo_comercio_referencia,
																						OrdenDescuentoCuota.nro_referencia_proveedor,
																						OrdenDescuentoCuota.nro_orden_referencia,
																						LiquidacionSocioRendicion.banco_intercambio,
																						LiquidacionSocioRendicion.status,
																						LiquidacionSocioRendicion.indica_pago'	
																						
																					),
																	'group' => array(
																						'GlobalDato.concepto_1,
																						Persona.documento,
																						Persona.apellido,
																						Persona.nombre,
																						Persona.sexo,
																						LiquidacionCuota.socio_id,
																						LiquidacionCuota.orden_descuento_cuota_id,
																						LiquidacionCuota.tipo_cuota,
																						LiquidacionCuota.tipo_producto,
																						LiquidacionCuota.periodo_cuota'
																					),																					
																	'order' => array('Persona.apellido,Persona.nombre,
																						LiquidacionCuota.orden_descuento_id,
																						LiquidacionCuota.periodo_cuota'
																					)															
		));	
//		$dbo = $this->LiquidacionCuota->getDataSource();
//		$querys = $dbo->_queriesLog;
//		debug($querys);
		return $cuotas;		
	}
	

	function getCobradoPorCaja(){
		App::import('Model','Mutual.OrdenDescuentoCobro');
		$oCOBRO = new OrdenDescuentoCobro();
		$cobros = $oCOBRO->getCobroByCajaByProveedorPeriodo($this->proveedor_id,$this->oLiquidacion['Liquidacion']['periodo'],$this->oLiquidacion['Liquidacion']['codigo_organismo'],$this->tipo_producto,$this->tipo_cuota);
		return $cobros;
	}

	
	function getFechaDebito($socio_id){
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();
		$fecha = $oLSR->getUltimaFechaDebito($socio_id,$this->oLiquidacion['Liquidacion']['id']);
		return $fecha;
	}
	
	
	
	/**
	 * Arma cadena de intercambio de datos
	 * 0 	(1) 	-> tipo registro (1=recsueldo, 2=caja, 3=cancelaxcaja)
	 * 1 	(8) 	-> documento del socio
	 * 2 	(30) 	-> apenom socio
	 * 3 	(10) 	-> nro referencia proveedor
	 * 4 	(10) 	-> nro expediente
	 * 5 	(2) 	-> nro de cuota
	 * 6 	(10)	-> importe cobrado
	 * 7 	(10)	-> comision
	 * 8 	(10)	-> neto proveedor
	 * 9	(8)		-> fecha debito (AAAAMMDD)
	 * @param unknown_type $campos
	 */
	function getRegistroIntercambio($campos){
		$cadena = "";
		$cadena .= str_pad(trim($campos[0]),1, '0', STR_PAD_LEFT);
		$cadena .= str_pad(trim($campos[1]),8, '0', STR_PAD_LEFT);
		$cadena .= str_pad(substr(trim($campos[2]),0,30),30, ' ', STR_PAD_RIGHT);
		$cadena .= str_pad(trim($campos[3]),10, '0', STR_PAD_LEFT);
		$cadena .= str_pad(trim($campos[4]),10, '0', STR_PAD_LEFT);
		$cadena .= str_pad(trim($campos[5]),2, '0', STR_PAD_LEFT);
		$cadena .= str_pad(number_format($campos[6] * 100,0,"",""), 10, '0', STR_PAD_LEFT);
		$cadena .= str_pad(number_format($campos[7] * 100,0,"",""), 10, '0', STR_PAD_LEFT);
		$cadena .= str_pad(number_format($campos[8] * 100,0,"",""), 10, '0', STR_PAD_LEFT);
		$cadena .= str_pad(trim($campos[9]),8, '0', STR_PAD_LEFT);
//		$cadena .= "\r\n";
		return $cadena;
	}
	
	
}
?>