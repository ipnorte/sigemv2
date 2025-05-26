<?php

/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * LISTADO DE CUOTAS SOCIALES COBRADAS POR PERIODO
 * DISCRIMINADO POR CATEGORIA DEL SOCIO
 * 
 * LANZADOR
 * 	/usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php listado_inaesA9 1242 -app /home/adrian/dev/www/sigem/app/
 * 
 */

class ListadoInaesA9Shell extends Shell {

	var $periodo;
	var $codigo_producto = array('MUTUPROD1004','MUTUPROD1005');
	
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

		$periodo		= $asinc->getParametro('p1');

		
		$asinc->actualizar(0,100,"ESPERE, INICIANDO PROCESO...");
		$STOP = 0;
		$total = 0;
		$i = 0;
		$asinc->actualizar(0,100,"ESPERE, CONSULTANDO ORDENES DE CONSUMO / SERVICIO A PROCESAR...");
		
		//limpio la tabla temporal
		if(!$this->Temporal->limpiarTabla($asinc->id)){
			$asinc->fin("SE PRODUJO UN ERROR...");
			return;
		}

		
		$this->out($periodo);
		
		App::import('Model','Mutual.LiquidacionCuota');
		$oLQC = new LiquidacionCuota();
		
		$sql = "SELECT 
				DISTINCT Socio.id AS socio_id
				FROM liquidacion_cuotas AS LiquidacionCuota
				INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = LiquidacionCuota.liquidacion_id)
				INNER JOIN socios AS Socio ON (Socio.id = LiquidacionCuota.socio_id)
				INNER JOIN personas AS Persona ON (Persona.id = Socio.persona_id)
				WHERE Liquidacion.periodo = '$periodo'
				AND LiquidacionCuota.tipo_orden_dto = 'CMUTU'
				AND LiquidacionCuota.tipo_producto = 'MUTUPROD0003'
				AND LiquidacionCuota.tipo_cuota = 'MUTUTCUOCSOC'
				AND LiquidacionCuota.imputada = 1
				ORDER BY Persona.apellido,Persona.nombre";
		
		$socios = $oLQC->query($sql);
		
		if(!empty($socios)):
		
			$total = count($socios);
			$asinc->setTotal($total);
			$i = 0;			
		
		
			App::import('Model', 'Pfyj.Socio');
			$oSOCIO = new Socio();	
		
			foreach($socios as $socio):
			
				$persona = $oSOCIO->getPersonaBySocioID($socio['Socio']['socio_id']);
				
//				$str = $persona['Persona']['apellido'].", ".$persona['Persona']['nombre'];
//				$str .= " - SOCIO #" . $socio['Socio']['socio_id']. " ". $persona['Socio']['categoria'];
			
				
				//CALCULO EL TOTAL COBRADO EN CONCEPTO DE CUOTA SOCIAL
				$sql = "SELECT 
						SUM(LiquidacionCuota.importe_debitado) as importe_debitado
						FROM liquidacion_cuotas AS LiquidacionCuota
						INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = LiquidacionCuota.liquidacion_id)
						INNER JOIN socios AS Socio ON (Socio.id = LiquidacionCuota.socio_id)
						WHERE Liquidacion.periodo = '$periodo'
						AND LiquidacionCuota.socio_id = ".$socio['Socio']['socio_id']."
						AND LiquidacionCuota.tipo_orden_dto = 'CMUTU'
						AND LiquidacionCuota.tipo_producto = 'MUTUPROD0003'
						AND LiquidacionCuota.tipo_cuota = 'MUTUTCUOCSOC'
						AND LiquidacionCuota.imputada = 1";
				
				$datos = $oLQC->query($sql);	

				$impoDebitado = (isset($datos[0][0]['importe_debitado']) ? $datos[0][0]['importe_debitado'] : 0);
				
				if($impoDebitado != 0):
				
					$str = "SOCIO #" . $socio['Socio']['socio_id']. " (". substr($persona['Socio']['categoria'],8,4).")";
					$str .= " | " . $persona['Persona']['apellido'].", ".$persona['Persona']['nombre'];
					$this->out($str);
					
					$asinc->actualizar($i,$total,"PROCESANDO | $str");
					
					$temp = array();
					
					$temp['AsincronoTemporal'] = array(
							'asincrono_id' => $asinc->id,
							'clave_1' => $persona['Socio']['categoria'],
							'texto_1' => $oLQC->GlobalDato('concepto_1',$persona['Persona']['tipo_documento']) . " " . $persona['Persona']['documento'],
							'texto_2' => $persona['Persona']['apellido'].", ".$persona['Persona']['nombre'],
							'texto_3' => $socio['Socio']['socio_id'],				
							'texto_4' => $persona['Socio']['categoria'],
							'decimal_1' => $impoDebitado,
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
		
		
		
		//CARGO LAS LIQUIDACIONES PARA EL PERIODO
//		App::import('Model','Mutual.Liquidacion');
//		$oLQ = new Liquidacion();
//		
//		$liquidaciones = $oLQ->liquidacionesByPeriodoDesdeHasta($periodo,$periodo);
//		
//		if(!empty($liquidaciones)):
//		
//		
//			foreach($liquidaciones as $liquidacion):
//			
//			
//				//CARGAR LAS CUOTAS SOCIALES LIQUIDADAS E IMPUTADAS DE CADA LIQUIDACION
//				
//				debug($liquidacion);
//			
//			
//			endforeach;
//		
//		
//		endif;
		
		
		return;
		
		
		$ordenes = $this->getOrdenes();
		$total = count($ordenes);
		$asinc->setTotal($total);
		$i = 0;	
		
		
		
		

		if(empty($ordenes)){
			$asinc->fin("NO EXISTEN ORDENES PARA PROCESAR...");
			return;
		}
		
		$temp = array();
		foreach($ordenes as $orden){
			
//			$orden = $this->MutualProductoSolicitud->armaDatos($orden,false);
//			debug($orden);
			
			$str = $orden['GlobalDato']['concepto_1']."-".$orden['Persona']['documento']." ".$orden['Persona']['apellido'].", ".$orden['Persona']['nombre'] ." - ORD.#".$orden['OrdenDescuento']['id'];
			
			$this->out($str);
			
			$asinc->actualizar($i,$total,"$i / $total - PROCESANDO >> " . $str);
			
			$cuotasAdeudadas = $this->getCuotasAdeudadas($orden['OrdenDescuento']['id']);
			
			if(!empty($cuotasAdeudadas)):
			
				$discriminador = array();
				$discriminador['orden'] = array();
				
				$discriminador['vencidas'] = array();
				$discriminador['a_vencer'] = array();
				$discriminador['cobrado'] = array();
				
				$discriminador['vencidas']['0_29'] = 0;
				$discriminador['vencidas']['30_59'] = 0;
				$discriminador['vencidas']['60_179'] = 0;
				$discriminador['vencidas']['180_179'] = 0;
				$discriminador['vencidas']['180_269'] = 0;
				$discriminador['vencidas']['270_9999'] = 0;
				
				$discriminador['a_vencer']['0_3'] = 0;
				$discriminador['a_vencer']['4_6'] = 0;
				$discriminador['a_vencer']['7_9'] = 0;
				$discriminador['a_vencer']['10_12'] = 0;
				$discriminador['a_vencer']['13_9999'] = 0;
				
				$discriminador['cobrado']['importe'] = 0;
				$discriminador['cobrado']['cuotas'] = 0;
				
				
				$discriminador['orden']['id'] = $orden['OrdenDescuento']['id'];
				$discriminador['orden']['tipo'] = $orden['OrdenDescuento']['tipo_orden_dto'];
				$discriminador['orden']['numero'] = $orden['OrdenDescuento']['numero'];
				$discriminador['orden']['importe'] = $orden['OrdenDescuento']['importe_total'];
				$discriminador['orden']['cuotas'] = $orden['OrdenDescuento']['cuotas'];
				$discriminador['orden']['fecha'] = $orden['OrdenDescuento']['fecha'];
				$discriminador['orden']['periodo_ini'] = $orden['OrdenDescuento']['periodo_ini'];
			
				foreach($cuotasAdeudadas as $cuota):
				
//					debug($cuota);
					
					$dias = $cuota[0]['dias'];
					$meses = intval(abs($cuota[0]['dias']) / 30);
					
					$importe = $cuota['OrdenDescuentoCuota']['importe'] - $cuota[0]['cobrado'];
					
					if($dias > 0):
					
						//cuotas vencidas
						if((0 <= $dias) && ($dias <= 29)):
							
							$discriminador['vencidas']['0_29'] += $importe;
							
						elseif((30 <= $dias) && ($dias <= 59)):
						
							$discriminador['vencidas']['30_59'] += $importe;
						
						elseif((60 <= $dias) && ($dias <= 179)):
							
							$discriminador['vencidas']['60_179'] += $importe;
						
						elseif((180 <= $dias) && ($dias <= 179)):

							$discriminador['vencidas']['180_179'] += $importe;
						
						elseif((180 <= $dias) && ($dias <= 269)):
							
							$discriminador['vencidas']['180_269'] += $importe;
							
						elseif((270 <= $dias) && ($dias <= 9999)):
						
							$discriminador['vencidas']['270_9999'] += $importe;
																						
						endif;
						
					else:
					
						//cuotas a vencer
						if((0 <= $meses) && ($meses <= 3)):
						
							$discriminador['a_vencer']['0_3'] += $importe;
							
						elseif((4 <= $meses) && ($meses <= 6)):
						
							$discriminador['a_vencer']['4_6'] += $importe;
							
						elseif((7 <= $meses) && ($meses <= 9)):
						
							$discriminador['a_vencer']['7_9'] += $importe;
							
						elseif((10 <= $meses) && ($meses <= 12)):
						
							$discriminador['a_vencer']['10_12'] += $importe;
							
						elseif((13 <= $meses) && ($meses <= 9999)):
						
							$discriminador['a_vencer']['13_9999'] += $importe;
																					
						endif;
						
					endif;
					
					
				
				endforeach;
				
				$cobrado = $this->getCuotasPagadas($orden['OrdenDescuento']['id']);
				if(!empty($cobrado)):
				
					$discriminador['cobrado']['importe'] = (isset($cobrado[0][0]['cobrado']) ? $cobrado[0][0]['cobrado'] : 0);
					$discriminador['cobrado']['cuotas'] = (isset($cobrado[0][0]['cantidad']) ? $cobrado[0][0]['cantidad'] : 0);
				
				endif;
			
//				debug($discriminador);
				
				$temp['AsincronoTemporal'] = array(
											'asincrono_id' => $asinc->id,
											'texto_1' => $orden['GlobalDato']['concepto_1'],
											'texto_2' => $orden['Persona']['documento'],
											'texto_3' => $orden['Persona']['apellido'].", ".$orden['Persona']['nombre'],
											'texto_4' => $discriminador['orden']['id'],
											'texto_5' => $discriminador['orden']['tipo'],
											'texto_6' => $discriminador['orden']['numero'],
											'texto_7' => $discriminador['orden']['periodo_ini'],
											'texto_8' => $discriminador['orden']['fecha'],
											'decimal_1' => $discriminador['orden']['importe'],
											'entero_1' => $discriminador['orden']['cuotas'],
											'decimal_2' => $discriminador['vencidas']['0_29'],
											'decimal_3' => $discriminador['vencidas']['30_59'],
											'decimal_4' => $discriminador['vencidas']['60_179'],
											'decimal_5' => $discriminador['vencidas']['180_179'],
											'decimal_6' => $discriminador['vencidas']['180_269'],
											'decimal_7' => $discriminador['vencidas']['270_9999'],
											'decimal_8' => $discriminador['a_vencer']['0_3'],
											'decimal_9' => $discriminador['a_vencer']['4_6'],
											'decimal_10' => $discriminador['a_vencer']['7_9'],
											'decimal_11' => $discriminador['a_vencer']['10_12'],
											'decimal_12' => $discriminador['a_vencer']['13_9999'],
											'entero_2' => $discriminador['cobrado']['cuotas'],
											'decimal_13' => $discriminador['cobrado']['importe'],
											
				);
				
//				debug($temp);
				
				if(!$this->Temporal->grabar($temp)){
					$STOP = 1;
					break;
				}				
				
				
			endif; // fin !empty($cuotasAdeudadas)
			
			
			if($asinc->detenido()){
				$STOP = 1;
				break;
			}			
			
			$i++;
		}		

	
		if($STOP == 0){
			$asinc->actualizar($i,$total,"FINALIZANDO...");
			$asinc->fin("**** PROCESO FINALIZADO ****");
		}
		
		
		
	}
	//FIN PROCESO ASINCRONO
	
	####################################################################################################
	# METODOS ESPECIFICOS DEL PROCESO
	####################################################################################################
	
	function getOrdenes(){
		$ordenes = null;
		
		$sql = "select 
					GlobalDato.concepto_1,
					Persona.documento,
					Persona.apellido,
					Persona.nombre,
					OrdenDescuento.id,
					OrdenDescuento.tipo_orden_dto,
					OrdenDescuento.numero,
					OrdenDescuento.importe_total,
					OrdenDescuento.cuotas,
					OrdenDescuento.fecha,
					OrdenDescuento.periodo_ini
				from 
					orden_descuentos as OrdenDescuento
				inner join 
							socios as Socio on (Socio.id = OrdenDescuento.socio_id)
				inner join 
							personas as Persona on (Persona.id = Socio.persona_id)
				inner join 
							global_datos as GlobalDato on (GlobalDato.id = Persona.tipo_documento)
				where 
					OrdenDescuento.tipo_producto in('MUTUPROD1004','MUTUPROD1005')
					and OrdenDescuento.fecha <= '".$this->fecha_corte."'
				order by 
					Persona.apellido,Persona.nombre";
		$ordenes = $this->OrdenDescuento->query($sql);
		return $ordenes;
	}

	
	function getCuotasAdeudadas($ordenDtoID){
		$sql = "select 
					OrdenDescuentoCuota.id,
					OrdenDescuentoCuota.orden_descuento_id,
					OrdenDescuentoCuota.persona_beneficio_id,
					OrdenDescuentoCuota.socio_id,
					OrdenDescuentoCuota.nro_cuota,
					OrdenDescuentoCuota.importe,
					OrdenDescuentoCuota.periodo,
					ifnull(OrdenDescuentoCuota.vencimiento,last_day(concat(concat(concat(substr(periodo,1,4),'-'),substr(periodo,5,2),'-01')))) as vencimiento,
					ifnull((select sum(cc.importe) 
					from orden_descuento_cobro_cuotas cc, orden_descuento_cobros co 
					where 
					cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and cc.orden_descuento_cobro_id = co.id and
					co.fecha <= '".$this->fecha_corte."'
					group by cc.orden_descuento_cuota_id
					),0) as cobrado,
					datediff('".$this->fecha_corte."',ifnull(vencimiento,last_day(concat(concat(concat(substr(periodo,1,4),'-'),substr(periodo,5,2),'-01'))))) as dias
				from orden_descuento_cuotas as OrdenDescuentoCuota
				where OrdenDescuentoCuota.orden_descuento_id = $ordenDtoID 
				having importe > cobrado 
				order by OrdenDescuentoCuota.nro_cuota asc";
		$cuotas = $this->OrdenDescuentoCuota->query($sql);
		return $cuotas;	
	}
	
	
	function getCuotasPagadas($ordenDtoID){
		$sql = "select 
				sum(ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc, orden_descuento_cobros co 
				where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and cc.orden_descuento_cobro_id = co.id and
				co.fecha <= '".$this->fecha_corte."'
				group by cc.orden_descuento_cuota_id
				),0)) as cobrado,
				sum(ifnull((select count(cc.orden_descuento_cuota_id) from orden_descuento_cobro_cuotas cc, orden_descuento_cobros co 
				where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and cc.orden_descuento_cobro_id = co.id and
				co.fecha <= '".$this->fecha_corte."'
				group by cc.orden_descuento_cuota_id
				),0)) as cantidad
				from orden_descuento_cuotas as OrdenDescuentoCuota
				where OrdenDescuentoCuota.orden_descuento_id = $ordenDtoID 
				and OrdenDescuentoCuota.importe = (select sum(cc.importe) from orden_descuento_cobro_cuotas cc, orden_descuento_cobros co 
				where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and cc.orden_descuento_cobro_id = co.id and
				co.fecha <= '".$this->fecha_corte."'
				group by cc.orden_descuento_cuota_id
				) 
				having cobrado > 0";
		
		$cuotas = $this->OrdenDescuentoCuota->query($sql);
		
		return $cuotas;	
		
	}	
}
?>