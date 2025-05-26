<?php
/**
 * PROCESO PARA AGREGAR A LA ORDEN DE DESCUENTO EL PRODUCTOR
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * LANZADOR
 * 
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php tmp_procesa_cobro_cbu_cjp 7 -app /home/adrian/dev/www/sigem/app/
 *
 *
 */

class TmpProcesaCobroCbuCjpShell extends Shell{

	var $liquidacionID = 187;
	var $periodo = '201110';
	var $organismo = 'MUTUCORG7701';
	

	function main(){
		

		$this->out("PROCESO COBRANZA CJP POR CBU");
		
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();		
		
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();		
		
		App::import('Model','Pfyj.SocioReintegro');
		$oSR = new SocioReintegro();	

		App::import('Model','Mutual.OrdenDescuentoCobro');
		$oCOB = new OrdenDescuentoCobro();	
		
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oCOBROCUOTA = new OrdenDescuentoCobroCuota();		
		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();		
		
		
		$conditions = array();
		$conditions['LiquidacionSocioRendicion.liquidacion_id'] = $this->liquidacionID;
		$conditions['LiquidacionSocioRendicion.indica_pago'] = 1;
		$conditions['LiquidacionSocioRendicion.socio_id <>'] = 0;
		$conditions['LiquidacionSocioRendicion.banco_intercambio'] = '00430';
		$fields = array('LiquidacionSocioRendicion.socio_id');
		$group = array('LiquidacionSocioRendicion.socio_id');
		$socios = $oLSR->find('all',array('conditions' => $conditions,'fields' => $fields, 'group' => $group));
		$socios = Set::extract('/LiquidacionSocioRendicion/socio_id',$socios);
		
		if(!empty($socios)):
		
//			debug($socios);

			$ACU_TOTAL_DEBITADO = $ACU_TOTAL_IMPUTADO = $ACU_TOTAL_REINTEGRO = 0;
			
			foreach($socios as $socio_id):
			
//				$saldoDebitoSocio = $oLSR->getTotalBySocioByLiquidacion($socio_id,$this->liquidacionID,1);
				
				$total = $oLSR->find('all',array(
														'conditions' => 
															array(
																	'LiquidacionSocioRendicion.liquidacion_id' => $this->liquidacionID,
																	'LiquidacionSocioRendicion.socio_id' => $socio_id, 
																	'LiquidacionSocioRendicion.indica_pago' => 1,
																	'LiquidacionSocioRendicion.banco_intercambio' => '00430',
															),
														'fields' => array('sum(importe_debitado) as importe_debitado'),
													)
										);				
//				debug($total);
				
				$saldoDebitoSocio = (isset($total[0][0]['importe_debitado']) ? $total[0][0]['importe_debitado'] : 0);
				
				$ACU_TOTAL_DEBITADO += $saldoDebitoSocio;
				
				$a_compensar = $oSR->getTotalReintegrosAnticipadosACompensar($socio_id,$this->liquidacionID);
				
				
				
				if($a_compensar != 0) $saldoDebitoSocio = $saldoDebitoSocio - $a_compensar;
				
				
//				if($socio_id == 12941):
				
			
//				$cuotas = $oLC->armaImputacion($this->liquidacionID,$socio_id);
				$cuotas = array();
				$liquidadas = $oLC->getCuotasByCriterioImputacion($this->liquidacionID,$socio_id,false,"CMUTU");
				
				//DETERMINO EL SALDO ACTUAL DE LA CUOTA LIQUIDADA
				foreach($liquidadas as $liquidada):
//					$liquidada['LiquidacionCuota']['saldo_actual_recalc'] = $oLC->calculaSaldoActual($liquidada['LiquidacionCuota']['orden_descuento_cuota_id']);
					if(round($liquidada['LiquidacionCuota']['saldo_actual'],2) > round($liquidada['LiquidacionCuota']['importe_debitado'],2)){
						$liquidada['LiquidacionCuota']['importe_debitado_ant'] = $liquidada['LiquidacionCuota']['importe_debitado'];
						
						//cargo las cuotas liquidadas que no sean cargos mutual
						array_push($cuotas,$liquidada);
					}
				
				endforeach;
				
//				if($socio_id == 16533)debug($cuotas);
				
				
//				$cuotas = $oLC->distribuyeImporteCuotas($cuotas,$saldoDebitoSocio);
				
				
				$saldoDebitoSocio1 = $saldoDebitoSocio;
				$importeImputaCuota = 0;
				
				$IMPO_DEBITADO_ANT = 0;
				
				
				foreach($cuotas as $idx => $cuota):
				
					$IMPO_DEBITADO_ANT += $cuota['LiquidacionCuota']['importe_debitado_ant'];
				
					$importe_cuota = $cuota['LiquidacionCuota']['saldo_actual'] - $cuota['LiquidacionCuota']['importe_debitado_ant'];
					if($saldoDebitoSocio1 >= $importe_cuota):
						$importeImputaCuota = $importe_cuota;
						$saldoDebitoSocio1 -= $importe_cuota;
					else:
						$importeImputaCuota = $saldoDebitoSocio1;
						$saldoDebitoSocio1 -= $importeImputaCuota;
					endif;
					
					$cuota['LiquidacionCuota']['importe_debitado'] += $importeImputaCuota;
					$cuota['LiquidacionCuota']['para_imputar'] = 1;	
						
					$cuotas[$idx] = $cuota;
					
					if($saldoDebitoSocio1 == 0) break;			
				
				endforeach;
				$cuotas = Set::extract("/LiquidacionCuota[importe_debitado>0]",$cuotas);
				$cuotas = Set::extract("{n}.LiquidacionCuota",$cuotas);					
				
				$IMPO_DEBITADO_ANT = round($IMPO_DEBITADO_ANT,2);
				
				$IMPU = 0;
				foreach($cuotas as $cuota){
					$IMPU += $cuota['importe_debitado'];
				}
				
				$IMPU -= $IMPO_DEBITADO_ANT;
				
				$ACU_TOTAL_IMPUTADO += $IMPU;
				
				$IMPU = round($IMPU,2);
				$saldoDebitoSocio = round($saldoDebitoSocio,2);
				
				$reintegro = $saldoDebitoSocio - $IMPU;
				
				$ACU_TOTAL_REINTEGRO += $reintegro;
				
				$reintegro = round($reintegro,2);
				
				$this->out($socio_id ."\t COMPENSA:".$a_compensar."\tDEBITO:".$saldoDebitoSocio."\tIMPU:".$IMPU."\tREINT:".$reintegro);
					
//				debug($cuotas);
				
				//GENERO UN REINTEGRO
				if(empty($cuotas) && $reintegro != 0){
					
					
					$reintegro = array('SocioReintegro' => array(
								'id' => 0,
								'socio_id' => $socio_id,
								'liquidacion_id' => $this->liquidacionID,
								'periodo' => $this->periodo,
								'importe_dto' => 0,
								'importe_debitado' => $saldoDebitoSocio,
								'importe_imputado' => $IMPU,
								'importe_reintegro' => $reintegro
					));					
					$oSR->save($reintegro);
					
				}else{
					
					
					$pago = array('OrdenDescuentoCobro' => array(
						'tipo_cobro' => 'MUTUTCOBRECS',
						'socio_id' => $socio_id,	
						'fecha' => "2011-11-04",
						'nro_recibo' => "COBRO_ESP_CJPXCBU",
						'importe' => $IMPU,
						'periodo_cobro' => $this->periodo
					));					
					
					
					$cuotaPagada = $cuotasPagadas = array();
					
					foreach($cuotas as $cuota){
						
						$cuotaPagada = array();

						$cuotaLiq = $oLC->read(null,$cuota['id']);

						$comision = $oCOBROCUOTA->calcularComisionCobranza($cuota['orden_descuento_cuota_id'],$cuota['importe_debitado']);
						
						$cuotaLiq['LiquidacionCuota']['importe_debitado'] = $cuota['importe_debitado'];
						$cuotaLiq['LiquidacionCuota']['para_imputar'] = 1;
						$cuotaLiq['LiquidacionCuota']['imputada'] = 1;
						$cuotaLiq['LiquidacionCuota']['alicuota_comision_cobranza'] = $comision['alicuota'];
						$cuotaLiq['LiquidacionCuota']['comision_cobranza'] = $comision['comision'];
						
						$cuotaPagada['periodo_cobro'] = $this->periodo;
						$cuotaPagada['orden_descuento_cuota_id'] = $cuota['orden_descuento_cuota_id'];
						$cuotaPagada['proveedor_id'] = $cuotaLiq['LiquidacionCuota']['proveedor_id'];
						$cuotaPagada['importe'] = round($cuota['importe_debitado'] - $cuota['importe_debitado_ant'],2);
						
						$comisionCobro = $oCOBROCUOTA->calcularComisionCobranza($cuotaPagada['orden_descuento_cuota_id'],$cuotaPagada['importe']);
						$cuotaPagada['alicuota_comision_cobranza'] = $comisionCobro['alicuota'];
						$cuotaPagada['comision_cobranza'] = $comisionCobro['comision'];	
						
						$cuotaPagada['pago_total_cuota'] = ($cuota['importe_debitado'] == $cuota['saldo_actual'] ? 1 : 0);

						array_push($cuotasPagadas,$cuotaPagada);
						
						
						//GRABO $cuotaLiq
						$oLC->save($cuotaLiq);
						
						
					}
					
					$pago['OrdenDescuentoCobroCuota'] = $cuotasPagadas;
					
					//GRABO EL PAGO Y TOMO EL ID
					$oCOB->saveAll($pago,array('atomic'=>false));

					//MARCO LAS CUOTAS PAGADAS TOTALMENTE
					$pagadasTotalmente = Set::extract('/OrdenDescuentoCobroCuota[pago_total_cuota=1]',$pago);
					foreach($pagadasTotalmente as $pagoCuota){
						$oCUOTA->setPagoTotal($pagoCuota['OrdenDescuentoCobroCuota']['orden_descuento_cuota_id']);
					}
					$idPAGO = $oCOB->getLastInsertID();
					unset($cuotaLiq);
					//GRABO EL CUOTALIQ CON EL ID DEL PAGO
					foreach($cuotas as $cuota){
						$cuotaLiq = $oLC->read(null,$cuota['id']);
						$cuotaLiq['LiquidacionCuota']['orden_descuento_cobro_id'] = $idPAGO;
						$oLC->save($cuotaLiq);
					}
					
					//ACTUALIZO EL ID DEL COBRO EN LA SOCIO RENDICION
					$imputacion = $oLSR->updateAll(
													array(
															'LiquidacionSocioRendicion.orden_descuento_cobro_id' => $idPAGO,
														),
													array(
															'LiquidacionSocioRendicion.liquidacion_id' => $this->liquidacionID, 
															'LiquidacionSocioRendicion.socio_id' => $socio_id,
															'LiquidacionSocioRendicion.banco_intercambio' => "00430",
															'LiquidacionSocioRendicion.indica_pago' => 1
													)
					);					
					
					

				}
				
				
				
			
			endforeach;
			
			$this->out("\tDEBITO TOTAL:".$ACU_TOTAL_DEBITADO."\tIMPU TOTAL:".$ACU_TOTAL_IMPUTADO."\tREINT TOTAL:".$ACU_TOTAL_REINTEGRO);
			
		
		endif;
		
	}
	
	
	function dbLinkTmp(){
		$db = new DATABASE_CONFIG();
		$link = mysql_connect($db->tmp['host'],$db->tmp['login'],$db->tmp['password'])
		or die ("No se establecio conexion a la base de datos");
		mysql_select_db ($db->tmp['database'],$link);
		return $link;
	}	
	
	
	
}

?>