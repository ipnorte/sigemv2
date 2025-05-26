<?php

/**
 * REPORTE DE CONTROL DE LA LIQUIDACION
 * 
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php reporte_control_liquidacion 205 -app /home/adrian/Desarrollo/www/sigem/app/
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php reporte_acumulado_proveedor_control_imputacion 893 -app /home/adrian/dev/www/sigem/app/
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 *
 */

class ReporteAcumuladoProveedorControlImputacionShell extends Shell {

	var $uses = array(
						'Mutual.Liquidacion',
						'Mutual.LiquidacionCuota',
						'Proveedores.Proveedor',
						'Mutual.OrdenDescuentoCuota',
						'Mutual.OrdenDescuento',
						'Mutual.LiquidacionSocio',
						'Proveedores.ProveedorComision',
						'Proveedores.Proveedor',
						'Config.BancoRendicionCodigo'
	);
	var $liquidacion_id;
	var $liquidaciones;
	
	var $periodo_desde;
	var $periodo_hasta;
	
	var $tasks = array('Temporal');
	
	function main() {
		
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oCCUOTA = new OrdenDescuentoCobroCuota();

		App::import('Model','Pfyj.Socio');
		$oSOCIO = new Socio();	

		App::import('Helper','Util');
		$oUT = new UtilHelper();	

		App::import('Model','Mutual.CancelacionOrden');
		$oCANCELACION = new CancelacionOrden();			
		
		App::import('Model','Mutual.OrdenDescuentoCobro');
		$oCOBRO = new OrdenDescuentoCobro();

		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();	

		App::import('model','config.Banco');
		$oBANCO = new Banco();	        
		
		$STOP = 0;
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 

		$periodo_desde = $asinc->getParametro('p1');
		$periodo_hasta = $asinc->getParametro('p2');
		$codigo_organismo = $asinc->getParametro('p3');
		$proveedor_id = $asinc->getParametro('p4');
		
		$TIPO_REPORTE = $asinc->getParametro('p5');
		
		
		$liquidaciones	= $this->Liquidacion->liquidacionesByPeriodoDesdeHasta($periodo_desde,$periodo_hasta,true,true,$codigo_organismo);
		
//		debug($liquidaciones);
		
//		$imputada = ($asinc->getParametro('p2') == 'CALCULA_IMPUTADO' ? 1 : 0);

		$asinc->actualizar(0,100,"ESPERE, INICIANDO PROCESO...");
		$STOP = 0;
		$total = 0;
		$i = 0;
		$asinc->actualizar(0,100,"ESPERE, CONSULTANDO LIQUIDACIONES...");
		
		if(empty($liquidaciones)):
			$asinc->actualizar(100,100,"NO EXISTEN LIQUIDACIONES IMPUTADAS PARA PROCESAR");
			$asinc->fin("NO EXISTEN LIQUIDACIONES IMPUTADAS PARA PROCESAR");
			return;
		endif;
		
//		$total = count($liquidaciones);
//		$asinc->setTotal($total);
		$i = 1;
		
		
		
		foreach($liquidaciones as $liquidacion):
		
			
			switch ($TIPO_REPORTE):
			
				case 1:
					
					$cuotas = $this->cargarCuotas($liquidacion['Liquidacion']['id'],$proveedor_id);
					$asinc->actualizar(1,100,"ESPERE, ANALIZANDO CUOTAS LIQUIDADAS " . $liquidacion['Liquidacion']['periodo_desc']);
					$this->out("ESPERE, ANALIZANDO CUOTAS LIQUIDADAS " . $liquidacion['Liquidacion']['periodo_desc']);
					
//					if(empty($cuotas)):
//						$cuotas = null;
//						$asinc->actualizar(100,100,"CUOTAS LIQUIDADAS *** NO EXISTEN DATOS **** PROCESO FINALIZADO ****");
//						$asinc->fin("CUOTAS LIQUIDADAS *** NO EXISTEN DATOS **** PROCESO FINALIZADO ****");
//						$this->out("CUOTAS LIQUIDADAS *** NO EXISTEN DATOS **** PROCESO FINALIZADO ****");
//						return;						
//					endif; //emptyCuotas

					if(!empty($cuotas)):
					
						$total = count($cuotas);
						$asinc->setTotal($total);
						$i = 0;
					
						foreach($cuotas as $cuota):
						
						
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
							$cuota['LiquidacionCuota']['proveedor_razon_social'] = $cuota['Proveedor']['razon_social'];
                            
                            
							$cuota['LiquidacionCuota']['tdoc'] = $cuota['GlobalDato']['concepto_1'];
							$cuota['LiquidacionCuota']['ndoc'] = $cuota['Persona']['documento'];
							$cuota['LiquidacionCuota']['tipo_orden_dto'] = $cuota['OrdenDescuento']['tipo_orden_dto'];
							$cuota['LiquidacionCuota']['numero_orden_dto'] = $cuota['OrdenDescuento']['numero'];                            
                            
                            
							if(empty($cuota['ProveedorComision']['comision'])) $cuota['ProveedorComision']['comision'] = 0;
	
							$asinc->actualizar($i,$total,"$i / $total - " . $liquidacion['Liquidacion']['periodo_desc'] . " *** " . $cuota['LiquidacionCuota']['socio']);
//							$this->out("$i / $total - " . $liquidacion['Liquidacion']['periodo_desc'] . " *** ". $cuota['LiquidacionCuota']['socio']);
							
							$totalReversado = $oCCUOTA->getTotalReversoByProveedorByLiquidacion($proveedor_id,$liquidacion['Liquidacion']['id'],0,0,$cuota['LiquidacionCuota']['socio_id']);
							
							
							$comision = round($cuota['LiquidacionCuota']['importe_debitado'],2) * $cuota['ProveedorComision']['comision'] / 100;
							
							$calificacion = $oSOCIO->getUltimaCalificacion($cuota['LiquidacionCuota']['socio_id'],$cuota['OrdenDescuentoCuota']['persona_beneficio_id'],true,true,true);
	
                            if(!empty($cuota['LiquidacionSocioRendicion']['banco_intercambio'])){
                                
								$descripcionCodigo = $this->BancoRendicionCodigo->getDescripcionCodigo($cuota['LiquidacionSocioRendicion']['banco_intercambio'],$cuota['LiquidacionSocioRendicion']['status']);
								//$bancoIntercambio = $this->LiquidacionCuota->getNombreBanco($cuota['LiquidacionSocioRendicion']['banco_intercambio']);
							
								$banco = $oBANCO->read('nombre,nro_cta_acredita_debito',$cuota['LiquidacionSocioRendicion']['banco_intercambio']);
								$bancoIntercambio = $banco['Banco']['nombre'];
								$bancoIntercambioCta = $banco['Banco']['nro_cta_acredita_debito'];
								
								$indicaPago = $cuota['LiquidacionSocioRendicion']['indica_pago'];
								$codigo_status = $cuota['LiquidacionSocioRendicion']['status'];
								$fechaDebito = $cuota['LiquidacionSocioRendicion']['fecha_debito'];
							
								if($indicaPago == 1){
										
									$descripcionCodigo = "PRORRATEO";
									$codigo_status = "PRO";
										
								}                                
                                
                            }else if(substr($liquidacion['Liquidacion']['codigo_organismo'],8,2) == "22"){
								//si es un cbu informo el no envio a descuento
								$descripcionCodigo = "NO ENVIADO A DESCUENTO";
								$bancoIntercambio = "";
								$indicaPago = 0;
								$codigo_status = "NOE";
								$fechaDebito = null;                                
							}else{
									
								$descripcionCodigo = "RETENCION NO INFORMADA POR EL ORGANISMO";
								$bancoIntercambio = "";
								$indicaPago = 0;
								$codigo_status = "NIN";
								$fechaDebito = null;
                            }    
                            
							$cuota['LiquidacionCuota']['banco_intercambio'] = $bancoIntercambio;
							$cuota['LiquidacionCuota']['banco_intercambio_cta'] = $bancoIntercambioCta;
							$cuota['LiquidacionCuota']['status'] = $codigo_status;
							$cuota['LiquidacionCuota']['descripcion_status_debito'] = $descripcionCodigo;
							$cuota['LiquidacionCuota']['indica_pago'] = $indicaPago;	
							$cuota['LiquidacionCuota']['fecha_debito'] = $fechaDebito;                            
                            
//							$temp['AsincronoTemporal'] = array(
//												
//									'asincrono_id' => $asinc->id,
//									'clave_1' => 'REPORTE_1',
//									'texto_1' => $cuota['LiquidacionCuota']['documento'],
//									'texto_2' => $cuota['LiquidacionCuota']['socio'],
//									'texto_3' => $cuota['LiquidacionCuota']['tipo_nro'],
//									'texto_4' => $cuota['LiquidacionCuota']['producto_cuota'],
//									'texto_5' => $cuota['LiquidacionCuota']['tipo_cuota_desc'],
//									'texto_6' => $cuota['LiquidacionCuota']['cuota'],
//									'texto_7' => $cuota['LiquidacionCuota']['periodo_cuota'],
//									'texto_8' => $cuota['LiquidacionCuota']['orden_descuento_cuota_id'],
//									'texto_9' => $cuota['LiquidacionCuota']['nro_referencia_proveedor'],
//									'texto_11' => $cuota['Persona']['documento'],
//									'texto_12' => (!empty($cuota['Persona']['sexo']) ? $cuota['Persona']['sexo'] : 'M'),
//									'texto_13' => $calificacion[0],
//									'texto_14' => $oUT->periodo($calificacion[2],true,"/"),
//									'decimal_1' => round($cuota['LiquidacionCuota']['saldo_actual'],2),
//									'decimal_2' => round($cuota['LiquidacionCuota']['importe_debitado'],2),
//									'decimal_3' => round($cuota['LiquidacionCuota']['saldo_actual'] - $cuota['LiquidacionCuota']['importe_debitado'],2),
//									'decimal_4' => round($cuota['ProveedorComision']['comision'],2),
//									'decimal_5' => round($comision,2),
//									'decimal_6' => round($cuota['LiquidacionCuota']['importe_debitado'] - $comision,2),
//									'decimal_7' => round($totalReversado,2),
//									'decimal_8' => round($cuota['LiquidacionCuota']['importe'],2),
//									'entero_1' => $liquidacion['Liquidacion']['periodo'],
//									'entero_2' => $cuota['LiquidacionCuota']['socio_id'],
//									'entero_3' => 1,
//									
//							);	
                            
							$temp['AsincronoTemporal'] = array(
												
									'asincrono_id' => $asinc->id,
									'clave_1' => 'REPORTE_1',
									'texto_1' => $cuota['LiquidacionCuota']['ndoc'],
									'texto_15' => $cuota['LiquidacionCuota']['tdoc'],
									'texto_2' => $cuota['LiquidacionCuota']['socio'],
									'texto_3' => $cuota['LiquidacionCuota']['tipo_orden_dto'],
									'texto_16' => $cuota['LiquidacionCuota']['numero_orden_dto'],
									'texto_4' => $cuota['LiquidacionCuota']['producto_cuota'],
									'texto_5' => $cuota['LiquidacionCuota']['tipo_cuota_desc'],
									'texto_6' => $cuota['LiquidacionCuota']['cuota'],
									'texto_7' => $cuota['LiquidacionCuota']['periodo_cuota'],
									'texto_8' => $cuota['LiquidacionCuota']['orden_descuento_cuota_id'],
									'texto_9' => $cuota['LiquidacionCuota']['proveedor_razon_social'],
									'texto_11' => $cuota['Persona']['documento'],
									'texto_12' => (!empty($cuota['Persona']['sexo']) ? $cuota['Persona']['sexo'] : 'M'),
									'texto_13' => $cuota['LiquidacionCuota']['banco_intercambio'],
									'texto_14' => $cuota['LiquidacionCuota']['fecha_debito'],
									'texto_17' => $cuota['LiquidacionCuota']['banco_intercambio_cta'],
									'texto_18' => substr($liquidacion['Liquidacion']['periodo'],-2)."/".substr($liquidacion['Liquidacion']['periodo'],0,4),
									'texto_19' => substr($cuota['LiquidacionCuota']['periodo_cuota'],-2)."/".substr($cuota['LiquidacionCuota']['periodo_cuota'],0,4),
// 									'texto_13' => $calificacion[0],
// 									'texto_14' => $oUT->periodo($calificacion[2],true,"/"),
									'decimal_1' => round($cuota['LiquidacionCuota']['saldo_actual'],2),
									'decimal_2' => round($cuota['LiquidacionCuota']['importe_debitado'],2),
									'decimal_3' => round($cuota['LiquidacionCuota']['saldo_actual'] - $cuota['LiquidacionCuota']['importe_debitado'],2),
									'decimal_4' => round($cuota['LiquidacionCuota']['alicuota_comision_cobranza'],2),
									'decimal_5' => round($cuota['LiquidacionCuota']['comision_cobranza'],2),
									'decimal_6' => round($cuota['LiquidacionCuota']['importe_debitado'] - $cuota['LiquidacionCuota']['comision_cobranza'],2),
									'decimal_7' => round($totalReversado,2),
									'decimal_8' => round($cuota['LiquidacionCuota']['importe'],2),
									'entero_1' => $liquidacion['Liquidacion']['periodo'],
									'entero_2' => $cuota['LiquidacionCuota']['socio_id'],
									'entero_3' => 1,
									
							);	                            
							
							if($asinc->detenido()){
								$STOP = 1;
								break;
							}				
		
							if(!$this->Temporal->grabar($temp)){
								$STOP = 1;
								break;
							}							
	
							$i++;
	
						endforeach;					
					
					endif;
					
					break; //case 1
					
				case 2:

					$this->out("ESPERE, ANALIZANDO MOTIVOS DE NO COBRO ".$liquidacion['Liquidacion']['periodo_desc']."...");
					$asinc->actualizar(1,100,"ESPERE, ANALIZANDO MOTIVOS DE NO COBRO ".$liquidacion['Liquidacion']['periodo_desc']."...");
					$noCobrados = $this->getDetalleNoCobradosDiscriminado($liquidacion['Liquidacion']['id'],$proveedor_id);	
					
//					if(empty($noCobrados)):
//						$noCobrados = null;
//						$asinc->actualizar(100,100,"NO EXISTEN DATOS **** PROCESO FINALIZADO ****");
//						$asinc->fin("MOTIVOS DE NO COBRO *** NO EXISTEN DATOS **** PROCESO FINALIZADO ****");
//						$this->out("MOTIVOS DE NO COBRO *** NO EXISTEN DATOS **** PROCESO FINALIZADO ****");
//						return;						
//					endif;

					if(!empty($noCobrados)):
					
						$total = count($noCobrados);
						$asinc->setTotal($total);					
						$i = 0;
						
						foreach($noCobrados as $cuota):
						
							$cuota['LiquidacionCuota']['documento'] = $cuota['GlobalDato']['concepto_1'] . " - " .  $cuota['Persona']['documento'];
							$cuota['LiquidacionCuota']['socio'] = strtoupper($cuota['Persona']['apellido'] . ", " .  $cuota['Persona']['nombre']);
							$cuota['LiquidacionCuota']['tipo_cuota_desc'] = $this->OrdenDescuentoCuota->GlobalDato('concepto_1',$cuota['LiquidacionCuota']['tipo_cuota']);
							$cuota['LiquidacionCuota']['producto_cuota'] = $this->OrdenDescuentoCuota->GlobalDato('concepto_1',$cuota['LiquidacionCuota']['tipo_producto'])." - ".$cuota['LiquidacionCuota']['tipo_cuota_desc'];
							$cuota['LiquidacionCuota']['tipo_nro'] = $cuota['OrdenDescuento']['tipo_orden_dto']." #".$cuota['OrdenDescuento']['numero'];
							$cuota['LiquidacionCuota']['cuota'] = str_pad($cuota['OrdenDescuentoCuota']['nro_cuota'],2,"0",STR_PAD_LEFT)."/".str_pad($cuota['OrdenDescuento']['cuotas'],2,"0",STR_PAD_LEFT);
							$cuota['LiquidacionCuota']['codigo_comercio_referencia'] = $cuota['OrdenDescuentoCuota']['codigo_comercio_referencia'];
							$cuota['LiquidacionCuota']['nro_referencia_proveedor'] = $cuota['OrdenDescuentoCuota']['nro_referencia_proveedor'];
							$cuota['LiquidacionCuota']['nro_orden_referencia'] = $cuota['OrdenDescuentoCuota']['nro_orden_referencia'];
						
						
							$asinc->actualizar($i,$total,"$i / $total - NO COBRADOS " . $liquidacion['Liquidacion']['periodo_desc'] . " *** ". $cuota['LiquidacionCuota']['socio']);
							$this->out("$i / $total - NO COBRADOS " . $liquidacion['Liquidacion']['periodo_desc'] . " *** ". $cuota['LiquidacionCuota']['socio']);
							
							$calificacion = $oSOCIO->getUltimaCalificacion($cuota['LiquidacionCuota']['socio_id'],$cuota['OrdenDescuentoCuota']['persona_beneficio_id'],true,true,true);
							
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
								$descripcionCodigo = "NO ENVIADO A DESCUENTO";
								$bancoIntercambio = "";
								$indicaPago = 0;
								$codigo_status = "NOE";
								
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
									'entero_1' => $liquidacion['Liquidacion']['periodo'],
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
						
						endforeach; //noCobrados					
					endif;
					break; //case 2
					
				case 3:
					
					$asinc->actualizar(1,100,"ESPERE, ANALIZANDO PAGOS REVERSADOS ".$liquidacion['Liquidacion']['periodo_desc']."...");
					$this->out("ESPERE, ANALIZANDO PAGOS REVERSADOS ".$liquidacion['Liquidacion']['periodo_desc']."...");
					$reversos = $oCCUOTA->reversosByProveedorByLiquidacion($proveedor_id,$liquidacion['Liquidacion']['id']);
					
//					if(empty($reversos)):
//					
//						$reversos = null;
//						$asinc->actualizar(100,100,"PAGOS REVERSADOS *** NO EXISTEN DATOS **** PROCESO FINALIZADO ****");
//						$asinc->fin("PAGOS REVERSADOS *** NO EXISTEN DATOS **** PROCESO FINALIZADO ****");
//						$this->out("PAGOS REVERSADOS *** NO EXISTEN DATOS **** PROCESO FINALIZADO ****");
//						return;		
//											
//					endif;
					
					if(!empty($reversos)):
					
						$total = count($reversos);
						$asinc->setTotal($total);					
						$i = 0;
						
						foreach($reversos as $reverso):
						
							$this->out("$i / $total - REVERSOS " . $liquidacion['Liquidacion']['periodo_desc'] . " *** ".  $reverso['OrdenDescuentoCobroCuota']['socio_apenom']);
							$asinc->actualizar($i,$total,"$i / $total - REVERSOS " . $liquidacion['Liquidacion']['periodo_desc'] . " *** ".  $reverso['OrdenDescuentoCobroCuota']['socio_apenom']);
						
							$temp = array();
							$temp['AsincronoTemporal'] = array(
									'asincrono_id' => $asinc->id,
									'clave_1' => 'REPORTE_4',
									'texto_1' => $reverso['OrdenDescuentoCobroCuota']['socio_tdocndoc'],
									'texto_2' => $reverso['OrdenDescuentoCobroCuota']['socio_apenom'],
									'texto_3' => $reverso['OrdenDescuentoCobroCuota']['cuota']['tipo_nro'],
									'texto_4' => $reverso['OrdenDescuentoCobroCuota']['cuota']['nro_referencia_proveedor'],
									'texto_5' => $reverso['OrdenDescuentoCobroCuota']['cuota']['producto_cuota'],
									'texto_6' => $reverso['OrdenDescuentoCobroCuota']['cuota']['cuota'],
									'texto_7' => $reverso['OrdenDescuentoCobroCuota']['cuota']['periodo'],
									'texto_8' => $oUT->periodo($reverso['OrdenDescuentoCobroCuota']['cuota']['periodo'],true,"/"),
									'texto_9' => $oUT->periodo($reverso['OrdenDescuentoCobroCuota']['periodo_proveedor_reverso'],true,"/"),
									'texto_10' => $reverso['OrdenDescuentoCobroCuota']['fecha_reverso'],
									'decimal_1' => $reverso['OrdenDescuentoCobroCuota']['importe_reversado'],
									'decimal_2' => $reverso['OrdenDescuentoCobroCuota']['porcentaje_comision'],
									'decimal_3' => $reverso['OrdenDescuentoCobroCuota']['comision'],
									'decimal_4' => $reverso['OrdenDescuentoCobroCuota']['importe_reversado'] - $reverso['OrdenDescuentoCobroCuota']['comision'],
									'entero_1' => $liquidacion['Liquidacion']['periodo'],
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
						
						endforeach; //reversos					
					
					endif;	
					
					break;// case 3
					
				case 4:
					
					$this->out("ESPERE, ANALIZANDO OTRAS COBRANZAS ".$liquidacion['Liquidacion']['periodo_desc']."...");
					$asinc->actualizar(1,100,"ESPERE, ANALIZANDO OTRAS COBRANZAS ".$liquidacion['Liquidacion']['periodo_desc']."...");
					$cobrosByCaja = $this->getCobradoPorCaja($liquidacion['Liquidacion']['periodo'],$proveedor_id,$liquidacion['Liquidacion']['codigo_organismo']);
//					if(empty($cobrosByCaja)):
//						$this->out("poraca");				
//						$cobrosByCaja = null;
//						$asinc->actualizar(100,100,"OTRAS COBRANZAS *** NO EXISTEN DATOS **** PROCESO FINALIZADO ****");
//						$asinc->fin("OTRAS COBRANZAS *** NO EXISTEN DATOS **** PROCESO FINALIZADO ****");
//						$this->out("OTRAS COBRANZAS *** NO EXISTEN DATOS **** PROCESO FINALIZADO ****");
//						return;						
//					endif;
					if(!empty($cobrosByCaja)):
						$total = count($cobrosByCaja);
						$asinc->setTotal($total);
						$i = 0;
					
						foreach($cobrosByCaja as $cobro):
						
						
							$cancelacion = null;
							
							
							if(!empty($cobro['OrdenDescuentoCobro']['cancelacion_orden_id'])) $cancelacion = $oCANCELACION->get($cobro['OrdenDescuentoCobro']['cancelacion_orden_id']);
							if(!empty($cancelacion)) $cancelacion = $cancelacion['CancelacionOrden'];
			
							$cobro['OrdenDescuentoCobro']['tipo_cobro_desc'] = $oCOBRO->GlobalDato('concepto_1',$cobro['OrdenDescuentoCobro']['tipo_cobro']);
							$cobro['OrdenDescuentoCobro']['apenom'] = $oSOCIO->getApenom($cobro['OrdenDescuentoCobro']['socio_id'],false);
							$cobro['OrdenDescuentoCobro']['tdocndoc'] = $oSOCIO->getTdocNdoc($cobro['OrdenDescuentoCobro']['socio_id']);
							
							$this->out("$i / $total - OTROS COBROS " . $liquidacion['Liquidacion']['periodo_desc'] . " *** ". $cobro['OrdenDescuentoCobro']['apenom']);
							$asinc->actualizar($i,$total,"$i / $total - OTROS COBROS " . $liquidacion['Liquidacion']['periodo_desc'] . " *** " . $cobro['OrdenDescuentoCobro']['apenom']);
							
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
										'entero_1' => $liquidacion['Liquidacion']['periodo'],
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
								
								$i++;
								
							endif;					
						
						
						endforeach; //endforeachCobroCaja					
					
					endif;
					
					break; //case 4
			
			
			endswitch;
		
		
		
		endforeach;
		

		$asinc->actualizar(100,100,"FINALIZANDO...");
		$asinc->fin("**** PROCESO FINALIZADO ****");
		return;
		

	}
	//FIN PROCESO ASINCRONO
	
	####################################################################################################
	# METODOS ESPECIFICOS DEL PROCESO
	####################################################################################################
	
	
	function cargarCuotas($liquidacion_id, $proveedor_id){
		
//        SELECT GlobalDato.concepto_1,
//        Persona.documento,
//        Persona.apellido,
//        Persona.nombre,
//        Persona.sexo,
//        LiquidacionCuota.socio_id,
//        LiquidacionCuota.orden_descuento_cuota_id,
//        LiquidacionCuota.tipo_cuota,
//        LiquidacionCuota.tipo_producto,
//        LiquidacionCuota.periodo_cuota,
//        LiquidacionCuota.importe,
//        LiquidacionCuota.saldo_actual,
//        LiquidacionCuota.importe_debitado,
//        LiquidacionCuota.alicuota_comision_cobranza,
//        LiquidacionCuota.comision_cobranza,															
//        OrdenDescuentoCuota.persona_beneficio_id,
//        OrdenDescuento.tipo_orden_dto,
//        OrdenDescuento.numero,
//        OrdenDescuento.cuotas,
//        OrdenDescuentoCuota.nro_cuota,
//        OrdenDescuentoCuota.importe,
//        OrdenDescuentoCuota.codigo_comercio_referencia,
//        OrdenDescuentoCuota.nro_referencia_proveedor,
//        OrdenDescuentoCuota.nro_orden_referencia,
//        LiquidacionSocioRendicion.banco_intercambio,
//        LiquidacionSocioRendicion.fecha_debito,
//        LiquidacionSocioRendicion.status,
//        LiquidacionSocioRendicion.indica_pago,
//        Proveedor.razon_social,
//        Proveedor.razon_social_resumida
//        FROM liquidacion_cuotas AS LiquidacionCuota 
//        inner JOIN socios AS Socio ON (LiquidacionCuota.socio_id = Socio.id) 
//        inner JOIN personas AS Persona ON (Socio.persona_id = Persona.id) 
//        inner JOIN global_datos AS GlobalDato ON (GlobalDato.id = Persona.tipo_documento) 
//        inner JOIN orden_descuentos AS OrdenDescuento ON (OrdenDescuento.id = LiquidacionCuota.orden_descuento_id) 
//        inner JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON (OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id) 
//        left JOIN liquidacion_socio_rendiciones AS LiquidacionSocioRendicion ON (LiquidacionSocioRendicion.liquidacion_id = 32 
//        AND LiquidacionSocioRendicion.socio_id = LiquidacionCuota.socio_id AND IFNULL(LiquidacionSocioRendicion.status,'') <> '') 
//        inner JOIN proveedores AS Proveedor ON (Proveedor.id = LiquidacionCuota.proveedor_id) 
//        WHERE LiquidacionCuota.liquidacion_id = 32 
//        GROUP BY
//        LiquidacionCuota.socio_id,
//        LiquidacionCuota.orden_descuento_cuota_id,
//        LiquidacionCuota.tipo_cuota,
//        LiquidacionCuota.tipo_producto,
//        LiquidacionCuota.periodo_cuota
//        ORDER BY Persona.apellido ASC,  Persona.nombre ASC,  LiquidacionCuota.orden_descuento_id ASC,  LiquidacionCuota.periodo_cuota ASC         
        
        
		$conditions = array();
		$conditions['LiquidacionCuota.liquidacion_id'] = $liquidacion_id;
		if(!empty($proveedor_id))$conditions['LiquidacionCuota.proveedor_id'] = $proveedor_id;
		
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
                                                                                        "LiquidacionSocioRendicion.liquidacion_id = " . $liquidacion_id,
                                                                                        "LiquidacionSocioRendicion.socio_id = LiquidacionCuota.socio_id",
                                                                                        "IFNULL(LiquidacionSocioRendicion.status,'') <> ''"
                                                                                )
                                                                        ),
                                                                        array(
                                                                                'table' => 'proveedores',
                                                                                'alias' => 'Proveedor',
                                                                                'type' => 'inner',
                                                                                'foreignKey' => false,
                                                                                'conditions' => array('Proveedor.id = LiquidacionCuota.proveedor_id')
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
																						OrdenDescuentoCuota.nro_orden_referencia,
																						LiquidacionSocioRendicion.banco_intercambio,
																						LiquidacionSocioRendicion.fecha_debito,
																						LiquidacionSocioRendicion.status,
																						LiquidacionSocioRendicion.indica_pago,
																						Proveedor.razon_social,
																						Proveedor.razon_social_resumida'	
                                                                        
																						
																					),
																	'order' => array('Persona.apellido,Persona.nombre,
																						LiquidacionCuota.orden_descuento_id,
																						LiquidacionCuota.periodo_cuota'
																					),
                                                                    'group' => array('LiquidacionCuota.socio_id,
                                                                                        LiquidacionCuota.orden_descuento_cuota_id,
                                                                                        LiquidacionCuota.tipo_cuota,
                                                                                        LiquidacionCuota.tipo_producto,
                                                                                        LiquidacionCuota.periodo_cuota'
                                                                                    )            
		));	
//		$dbo = $this->LiquidacionCuota->getDataSource();
//		$querys = $dbo->_queriesLog;
//		debug($querys);		
		return $cuotas;
		
	}	
	
	
	function getDetalleNoCobradosDiscriminado($liquidacion_id,$proveedor_id){
		$conditions = array();
		$conditions['LiquidacionCuota.liquidacion_id'] = $liquidacion_id;
		if(!empty($proveedor_id))$conditions['LiquidacionCuota.proveedor_id'] = $proveedor_id;
		$conditions['(LiquidacionCuota.saldo_actual - LiquidacionCuota.importe_debitado) > '] = 0;

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
																									"LiquidacionSocioRendicion.liquidacion_id = " . $liquidacion_id,
																									"LiquidacionSocioRendicion.socio_id = LiquidacionCuota.socio_id",
																									"IFNULL(LiquidacionSocioRendicion.status,'') <> ''"
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
	
	
	function getCobradoPorCaja($periodo,$proveedor_id,$codigo_organismo){
		App::import('Model','Mutual.OrdenDescuentoCobro');
		$oCOBRO = new OrdenDescuentoCobro();
		$cobros = $oCOBRO->getCobroByCajaByProveedorPeriodo($proveedor_id,$periodo,$codigo_organismo);
		return $cobros;
	}	
	
	function __cargarProductosByProveedor($liquidacion_id,$proveedor_id){
		$productos = array();
		$conditions = array();
		$conditions['LiquidacionCuota.liquidacion_id'] = $liquidacion_id;
		if(!empty($proveedor_id))$conditions['LiquidacionCuota.proveedor_id'] = $proveedor_id;
		
		$registros = $this->LiquidacionCuota->find('all',array(
											'joins'	=> array(
														array(
															'table' => 'global_datos',
															'alias' => 'GlobalDato',
															'type' => 'inner',
															'foreignKey' => false,
															'conditions' => array('GlobalDato.id = LiquidacionCuota.tipo_producto')
															),		
											),
											'conditions' => $conditions,
											'fields' => array('LiquidacionCuota.tipo_producto'),
											'group' => array('LiquidacionCuota.tipo_producto'),
											'order' => array('GlobalDato.concepto_1')
		));
		foreach($registros as $producto){
			$producto['LiquidacionCuota']['tipo_producto_desc'] = $this->LiquidacionCuota->GlobalDato('concepto_1',$producto['LiquidacionCuota']['tipo_producto']);
			array_push($productos,$producto['LiquidacionCuota']);
		}	
		return $productos;
		
	}
	
	

}
?>