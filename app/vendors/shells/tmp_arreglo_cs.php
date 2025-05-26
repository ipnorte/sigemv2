<?php
/**
 * PROCESO PARA ARREGLO DE LA CUOTA SOCIAL DICIEMBRE DESCONTADA POR CJP Y QUE NOSOTROS LIQUIDAMOS E IMPUTAMOS POR CBU
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * LANZADOR
 * 
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php tmp_arreglo_cs -app /home/adrian/Desarrollo/www/sigem/app/
 *
 */

class TmpArregloCsShell extends Shell{

	var $uses = array(
						'Mutual.LiquidacionIntercambioRegistroProcesado',
						'Mutual.LiquidacionCuota',
						'Mutual.OrdenDescuento',
						'Mutual.OrdenDescuentoCobroCuota',
						'Pfyj.Persona',
						'Pfyj.PersonaBeneficio'
	);
	
	
	function main(){
		
		return null;
		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();

		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();
		
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();		
		
		$registros = $this->LiquidacionIntercambioRegistroProcesado->find('all',array(
									'joins' => array(
														array(
															'table' => 'personas',
															'alias' => 'Persona',
															'type' => 'inner',
															'foreignKey' => false,
															'conditions' => array('LiquidacionIntercambioRegistroProcesado.documento = Persona.documento')
															),
														array(
															'table' => 'socios',
															'alias' => 'Socio',
															'type' => 'inner',
															'foreignKey' => false,
															'conditions' => array('Persona.id = Socio.persona_id')
															),																			
												),
									'conditions' => array(
													'LiquidacionIntercambioRegistroProcesado.liquidacion_id' => 69,
													'LiquidacionIntercambioRegistroProcesado.liquidacion_socio_id' => 0,
													'LiquidacionIntercambioRegistroProcesado.importe_debitado' => 19
													),
									'fields' => array(
														'Socio.id',
														'Socio.orden_descuento_id',
														'Persona.id',	
														'Persona.apellido',
														'Persona.nombre',
														'LiquidacionIntercambioRegistroProcesado.importe_debitado',
														'LiquidacionIntercambioRegistroProcesado.nro_ley',
														'LiquidacionIntercambioRegistroProcesado.tipo',
														'LiquidacionIntercambioRegistroProcesado.nro_beneficio',
														'LiquidacionIntercambioRegistroProcesado.sub_beneficio',
													)					
		));
		foreach($registros as $registro){
			
			$SOCIO_ID = $registro['Socio']['id'];
			$PERSONA_ID = $registro['Persona']['id'];
			
			
			$LEY 			= $registro['LiquidacionIntercambioRegistroProcesado']['nro_ley'];
			$TIPO 			= $registro['LiquidacionIntercambioRegistroProcesado']['tipo'];
			$BENEFICIO 		= $registro['LiquidacionIntercambioRegistroProcesado']['nro_beneficio'];
			$SBENEFICIO 	= $registro['LiquidacionIntercambioRegistroProcesado']['sub_beneficio'];
			
			$msg = $SOCIO_ID.'|'.$PERSONA_ID."\t\t".$registro['Persona']['apellido'].', '.$registro['Persona']['nombre'];
			
			$this->out($msg,true);
			$this->out("\t\tBENEFICIO CJP: $LEY-$TIPO-$BENEFICIO-$SBENEFICIO",true);
			$this->out("\t\tRELIQUIDAR CJP DICIEMBRE",true);
			$oLS->reliquidar($SOCIO_ID,"200912",false,FALSE,'MUTUCORG7701');
			$this->out("\t\t   --> OK",true);
			$this->out("-----------------------------------------------------------------------------------------------------------",true);							
			
			
		}
		
		//proceso las cuotas sociales a imputar del cbu
		App::import('Model','Shells.AsincronoTemporal');
		$oTMP = new AsincronoTemporal();
		
		App::import('Model','Mutual.OrdenDescuentoCobro');
		$oCOB = new OrdenDescuentoCobro();	

		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();		

		$registros = $oTMP->find('all',array('conditions' => array('AsincronoTemporal.asincrono_id' => 9999)));
		$i = 1;
		foreach($registros as $registro){
			
			$SOCIO_ID = $registro['AsincronoTemporal']['entero_2'];
			$LIQUI_ID = $registro['AsincronoTemporal']['entero_1'];
			$IMPORTE = $registro['AsincronoTemporal']['decimal_1'];
			
			$IMPUTADO = $oLSR->getTotalBySocioByLiquidacion($SOCIO_ID,$LIQUI_ID,1);
			$IMPUTADO_CUOTAS = $oLC->getTotalImputadoBySocioByLiquidacion($LIQUI_ID,$SOCIO_ID);
			
			$SALDO = $IMPUTADO - $IMPUTADO_CUOTAS;
			
			$this->out("$i --> $SOCIO_ID | $IMPORTE -- $IMPUTADO -- $IMPUTADO_CUOTAS --> $SALDO",true);
			
			$cuotas = $oLC->cuotasPendientesDeImputar($LIQUI_ID,$SOCIO_ID,false);
			
			
			
			$importeImputaCuota = 0;
			$saldoDebitoSocio = $SALDO;
			

			$cuotasActualizadas = array();
			$tmp = array();
			
			$acu_debitado = 0;
			
			foreach($cuotas as $idx => $cuota){
				
				$saldo_cuota = $cuota['LiquidacionCuota']['saldo_actual'] - $cuota['LiquidacionCuota']['importe_debitado'];
				
				if($saldo_cuota > 0):
				
					if($saldoDebitoSocio >= $saldo_cuota){
						
						$importeImputaCuota = $saldo_cuota;
						$saldoDebitoSocio -= $saldo_cuota;
						
					}else{
						
						$importeImputaCuota = $saldoDebitoSocio;
						$saldoDebitoSocio -= $importeImputaCuota;
						
					}
					
					$cuota['LiquidacionCuota']['importe_debitado'] += $importeImputaCuota;
					
				
				endif;
				
				$acu_debitado += $cuota['LiquidacionCuota']['importe_debitado'];
				
				$cuotas[$idx] = $cuota;
				
				array_push($tmp,$cuota['LiquidacionCuota']);
				
				
			}
			
			
			$cuotasActualizadas['LiquidacionCuota'] = $tmp;
			
			$oLC->saveAll($cuotasActualizadas['LiquidacionCuota']);
			
			
//			debug($cuotasActualizadas);
//			debug($acu_debitado);
			
			$pagoDetalle = array();
			$pagoCuota = array();
			
			foreach($cuotasActualizadas['LiquidacionCuota'] as $cuotaActualizada){

				$cuota = $oLC->read(null,$cuotaActualizada['id']);
				if($cuotaActualizada['importe_debitado'] != 0):
					$pagoCuota['periodo_cobro'] = '201001';
					$pagoCuota['orden_descuento_cuota_id'] = $cuotaActualizada['orden_descuento_cuota_id'];
					$pagoCuota['proveedor_id'] = $cuota['LiquidacionCuota']['proveedor_id'];
					$pagoCuota['importe'] = $cuotaActualizada['importe_debitado'];
					array_push($pagoDetalle,$pagoCuota);
				endif;
			}

			// saco el pago para anexarle las cuotas
			$liquidacionSocio = $oLS->find('all',array('conditions' => array('LiquidacionSocio.liquidacion_id' => $LIQUI_ID, 'LiquidacionSocio.socio_id' => $SOCIO_ID)));
			
			$ID_COBRO = $liquidacionSocio[0]['LiquidacionSocio']['orden_descuento_cobro_id'];
			$cobro = $oCOB->read(null,$ID_COBRO);
			$oCOB->borrarDetalle($ID_COBRO,false);
			$cobro['OrdenDescuentoCobroCuota'] = $pagoDetalle;
			$oCOB->saveAll($cobro);
			
			$i++;
			
		}
//		debug($registros);
		
	}
	
	
}

?>