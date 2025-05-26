<?php
/**
 * Proceso que busca los socios no encontrados en la liquidacion_socio_rendiciones y
 * si detecta alguno le imputa lo cobrado
 * 
 * @author ADRIAN TORRES
 *
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php control_imputa_pago 88 -app /home/adrian/Desarrollo/www/sigem/app/
 * /usr/bin/php5 /var/www/sigem/cake/console/cake.php control_imputa_pago 97 -app /var/www/sigem/app/
 *
 */
class ReimputarRendicionShell extends Shell{
	
	var $liquidacion_id = 0;
	
	var $uses = array(
						'Mutual.OrdenDescuentoCobro',
						'Mutual.OrdenDescuentoCobroCuota',
						'Mutual.OrdenDescuentoCuota',
						'Mutual.Liquidacion',
						'Mutual.LiquidacionCuota',
						'Mutual.LiquidacionSocio',
						'Mutual.LiquidacionSocioRendicion',
						'Pfyj.Socio',
						'Pfyj.PersonaBeneficio'
	);
	
	function main(){
		
		$this->liquidacion_id = $this->args[0];
		$liquidacion =  $this->Liquidacion->cargar($this->liquidacion_id);
		
		$registros = $this->getRegistros();
		
		if(!empty($registros)):
		
			App::import('Model','Pfyj.Socio');
			$oSOCIO = new Socio();
			
			App::import('Model','Mutual.LiquidacionCuota');
			$oLC = new LiquidacionCuota();	

			App::import('Model','Mutual.OrdenDescuentoCuota');
			$oCUOTA = new OrdenDescuentoCuota();			
		
			$organismo = substr($liquidacion['Liquidacion']['codigo_organismo'],8,2);

			foreach($registros as $idx => $registro):
			
//				debug($registro);

				if($organismo == 77):
				
					$tipo = $registro['LiquidacionSocioRendicion']['tipo'];
					$ley = $registro['LiquidacionSocioRendicion']['nro_ley'];
					
					
					$nro_beneficio = str_pad(intval($registro['LiquidacionSocioRendicion']['nro_beneficio']),6,0,STR_PAD_LEFT);
					
					$sub_beneficio = $registro['LiquidacionSocioRendicion']['sub_beneficio'];
					$codigo_dto = $registro['LiquidacionSocioRendicion']['codigo_dto'];
					$sub_codigo = $registro['LiquidacionSocioRendicion']['sub_codigo'];
					
					$socio = $this->LiquidacionSocio->find('all',array('conditions' => array(
								'LiquidacionSocio.liquidacion_id' => $this->liquidacion_id,
								'LiquidacionSocio.tipo' => $tipo,
								'LiquidacionSocio.nro_ley' => $ley,
								'LiquidacionSocio.nro_beneficio' => $nro_beneficio,
								'LiquidacionSocio.sub_beneficio' => $sub_beneficio,
								'LiquidacionSocio.codigo_dto' => $codigo_dto,
								'LiquidacionSocio.sub_codigo' => $sub_codigo,
					)));

					if(empty($socio)):
					
						#SOCIO SIN LIQUIDACION PARA EL PERIODO
						#BUSCAR EL ID MEDIANTE BENEFICIO
					
						$conditions = array();
						$conditions['PersonaBeneficio.codigo_beneficio'] = $liquidacion['Liquidacion']['codigo_organismo'];
						$conditions['PersonaBeneficio.tipo'] = $tipo;
						$conditions['PersonaBeneficio.nro_ley'] = $ley;
						$conditions['PersonaBeneficio.nro_beneficio'] = $nro_beneficio;
						$conditions['PersonaBeneficio.sub_beneficio'] = $sub_beneficio;
						
						
						$beneficio = $this->PersonaBeneficio->find('all',array('conditions' => $conditions));
						
						if(!empty($beneficio)):
							
							$oSOCIO->unbindModel(array('belongsTo' => array('Persona'),'hasMany' => array('SocioCalificacion')));
							$socio1 = $oSOCIO->getSocioByPersonaId($beneficio[0]['PersonaBeneficio']['persona_id']);
							
							$this->out("REINTEGRO DIRECTO: ".$socio1['Socio']['id']."\tIMPORTE:" . $registro['LiquidacionSocioRendicion']['importe_debitado']);
							
//							debug($socio1);

							$reintegro = array('SocioReintegro' => array(
										'id' => 0,
										'socio_id' => $socio1['Socio']['id'],
										'liquidacion_id' => $this->liquidacion_id,
										'periodo' => $liquidacion['Liquidacion']['periodo'],
										'importe_dto' => 0,
										'importe_debitado' => $registro['LiquidacionSocioRendicion']['importe_debitado'],
										'importe_imputado' => 0,
										'importe_reintegro' => $registro['LiquidacionSocioRendicion']['importe_debitado']
							));							
//							debug($registro);
//							debug($reintegro);
							
						endif;

					else:

						$this->out("IMPUTAR: ".$socio[0]['LiquidacionSocio']['socio_id']."\tIMPORTE:" . $registro['LiquidacionSocioRendicion']['importe_debitado']);
						
						$cuotas = $oLC->getCuotasByCriterioImputacion($this->liquidacion_id,$socio[0]['LiquidacionSocio']['socio_id'],true);
						if(!empty($cuotas)):
							$cuotas = $oLC->distribuyeImporteCuotas($cuotas,$registro['LiquidacionSocioRendicion']['importe_debitado']);
							
//							debug($cuotas);
							
							$ACUM_IMPUTADO 		= 0;
							$saldoActual 		= 0;
							$importeDebitado 	= 0;
							
							$cuotaPagada 	= array();
							$cuotasPagadas 	= array();
							
							//genero un pago
							foreach($cuotas as $idx => $cuota):
							
								debug($cuota);
							
								$importeDebitado = $cuota['importe_debitado'];
								$saldoActual = $oCUOTA->getSaldo($cuota['orden_descuento_cuota_id']);
								
								$cuota['LiquidacionCuota']['saldo_actual'] = $saldoActual;
								
								if($importeDebitado > $saldoActual) $cuota['LiquidacionCuota']['importe_debitado'] = $saldoActual;
								
								$ACUM_IMPUTADO += $cuota['LiquidacionCuota']['importe_debitado'];
								
								$cuotaPagada['periodo_cobro'] = $liquidacion['Liquidacion']['periodo'];
								$cuotaPagada['orden_descuento_cuota_id'] = $cuota['LiquidacionCuota']['orden_descuento_cuota_id'];
								$cuotaPagada['proveedor_id'] = $cuota['LiquidacionCuota']['proveedor_id'];
								$cuotaPagada['importe'] = $cuota['LiquidacionCuota']['importe_debitado'];
								
								$cuotaPagada['pago_total_cuota'] = ($cuota['LiquidacionCuota']['importe_debitado'] == $saldoActual ? 1 : 0);
								
								//controlo que la cuota ya no este pagada totalmente para no generar un nuevo pago
								if($saldoActual != 0)array_push($cuotasPagadas,$cuotaPagada);
								
								$cuotas[$idx] = $cuota;

							endforeach;
							
							$pago = array('OrdenDescuentoCobro' => array(
								'tipo_cobro' => 'MUTUTCOBRECS',
								'socio_id' => $socio[0]['LiquidacionSocio']['socio_id'],	
								'fecha' => date('Y-m-d'),
								'nro_recibo' => "CORR-".$liquidacion['Liquidacion']['id']."-".$liquidacion['Liquidacion']['periodo'],
								'importe' => $ACUM_IMPUTADO,
								'periodo_cobro' => $liquidacion['Liquidacion']['periodo']
							));
					
							$pago['OrdenDescuentoCobroCuota'] = $cuotasPagadas;
							
							debug($pago);
							
						else:
							
							//NO TIENE CUOTAS ADEUDADAS LE METO UN REINTEGRO
							
							$this->out("REINTEGRO DIRECTO: ".$socio[0]['LiquidacionSocio']['socio_id']."\tIMPORTE:" . $registro['LiquidacionSocioRendicion']['importe_debitado']);
								
							$reintegro = array('SocioReintegro' => array(
										'id' => 0,
										'socio_id' => $socio[0]['LiquidacionSocio']['socio_id'],
										'liquidacion_id' => $this->liquidacion_id,
										'periodo' => $liquidacion['Liquidacion']['periodo'],
										'importe_dto' => 0,
										'importe_debitado' => $registro['LiquidacionSocioRendicion']['importe_debitado'],
										'importe_imputado' => 0,
										'importe_reintegro' => $registro['LiquidacionSocioRendicion']['importe_debitado']
							));								
							
						endif;
						
						
						
					endif;
					
					
				
				endif;
				
			
			endforeach;
		
		endif;
			
		
	}
	
	
	function getRegistros(){
		$conditions = array();
		$conditions['LiquidacionSocioRendicion.liquidacion_id'] = $this->liquidacion_id;
		$conditions['LiquidacionSocioRendicion.indica_pago'] = 1;
		$conditions['LiquidacionSocioRendicion.socio_id'] = 0;
		$registros = $this->LiquidacionSocioRendicion->find('all',array('conditions' => $conditions));	
		return $registros;
	}
	
}

?>