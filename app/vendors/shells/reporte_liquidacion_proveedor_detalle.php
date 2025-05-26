<?php

/**
 * 
 * NO SE ESTA USANDO VER reporte_liquidacion_proveedor_detalle2.php
 * 
 * listado_ordenes_dto_entre_fechas
 * 
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php reporte_liquidacion_proveedor_detalle 885 -app /home/adrian/Desarrollo/www/sigem/app/
 * 
 * 
 * LANZADOR NT
 * 
 * "D:\develop\xampp\php\php.exe" "D:\develop\xampp\htdocs\sigem\cake\console\cake.php" reporte_liquidacion_proveedor_detalle2 1 -app "D:\develop\xampp\htdocs\sigem\app\"
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 *
 */

class ReporteLiquidacionProveedorDetalleShell extends Shell {

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
		
		$liquidacion =  $this->Liquidacion->cargar($this->liquidacion_id);
		
		$asinc->actualizar(0,100,"ESPERE, INICIANDO PROCESO...");
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
		
		$socios = $this->__getDetalle();
	
		if(!empty($socios)){
			
			$total = count($socios);
			$asinc->setTotal($total);
			$i = 0;			
			
			$temp = array();
			
//			debug($socios);
			
			foreach($socios as $socio){

					$temp = array();
				
					$socio['LiquidacionCuota']['documento'] = $socio['GlobalDato']['concepto_1'] . " - " .  $socio['Persona']['documento'];
					$socio['LiquidacionCuota']['socio'] = strtoupper($socio['Persona']['apellido'] . ", " .  $socio['Persona']['nombre']);
					
					$socio['LiquidacionCuota']['tipo_cuota_desc'] = $this->OrdenDescuentoCuota->GlobalDato('concepto_1',$socio['LiquidacionCuota']['tipo_cuota']);
					$socio['LiquidacionCuota']['producto_cuota'] = $this->OrdenDescuentoCuota->GlobalDato('concepto_1',$socio['LiquidacionCuota']['tipo_producto'])." - ".$socio['LiquidacionCuota']['tipo_cuota_desc'];
					$socio['LiquidacionCuota']['tipo_nro'] = $socio['OrdenDescuento']['tipo_orden_dto']." #".$socio['OrdenDescuento']['numero'];
					$socio['LiquidacionCuota']['cuota'] = str_pad($socio['OrdenDescuentoCuota']['nro_cuota'],2,"0",STR_PAD_LEFT)."/".$socio['OrdenDescuento']['cuotas'];
					$socio['LiquidacionCuota']['codigo_comercio_referencia'] = $socio['OrdenDescuentoCuota']['codigo_comercio_referencia'];
					$socio['LiquidacionCuota']['nro_referencia_proveedor'] = $socio['OrdenDescuentoCuota']['nro_referencia_proveedor'];
					$socio['LiquidacionCuota']['nro_orden_referencia'] = $socio['OrdenDescuentoCuota']['nro_orden_referencia'];
					
					$asinc->actualizar($i,$total,"$i / $total - PROCESANDO >> " . $socio['LiquidacionCuota']['socio']);
					
					if(empty($socio['ProveedorComision']['comision'])) $socio['ProveedorComision']['comision'] = 0;
					
					//calcular lo reversado
					$totalReversado = $oCCUOTA->getTotalReversoByProveedorByLiquidacion($this->proveedor_id,$this->liquidacion_id,$this->tipo_producto,$this->tipo_cuota,$socio['LiquidacionCuota']['socio_id']);
					
					
					$comision = $socio['LiquidacionCuota']['importe_debitado'] * $socio['ProveedorComision']['comision'] / 100;
					
					
					
					$temp['AsincronoTemporal'] = array(
										
							'asincrono_id' => $asinc->id,
							'texto_1' => $socio['LiquidacionCuota']['documento'],
							'texto_2' => $socio['LiquidacionCuota']['socio'],
							'texto_3' => $socio['LiquidacionCuota']['tipo_nro'],
							'texto_4' => $socio['LiquidacionCuota']['producto_cuota'],
							'texto_5' => $socio['LiquidacionCuota']['tipo_cuota_desc'],
							'texto_6' => $socio['LiquidacionCuota']['cuota'],
							'texto_7' => $socio['LiquidacionCuota']['periodo_cuota'],
							'texto_8' => $socio['LiquidacionCuota']['orden_descuento_cuota_id'],
							'texto_9' => $socio['LiquidacionCuota']['nro_referencia_proveedor'],
							'texto_10' => 'REPORTE_1',
							'texto_11' => $socio['Persona']['documento'],
							'texto_12' => (!empty($socio['Persona']['sexo']) ? $socio['Persona']['sexo'] : 'M'),
							'decimal_1' => round($socio['LiquidacionCuota']['saldo_actual'],2),
							'decimal_2' => round($socio['LiquidacionCuota']['importe_debitado'],2),
							'decimal_3' => round($socio['LiquidacionCuota']['saldo_actual'] - $socio['LiquidacionCuota']['importe_debitado'],2),
							'decimal_4' => round($socio['ProveedorComision']['comision'],2),
							'decimal_5' => round($comision,2),
							'decimal_6' => round($socio['LiquidacionCuota']['importe_debitado'] - $comision,2),
							'decimal_7' => round($totalReversado,2),
							'decimal_8' => round($socio['LiquidacionCuota']['importe'],2),
							'entero_1' => $liquidacion['Liquidacion']['periodo'],
							'entero_2' => $socio['LiquidacionCuota']['socio_id'],
							
					);	
					
			
//					debug($temp);
					
					if($asinc->detenido()){
						$STOP = 1;
						break;
					}				

					if(!$this->Temporal->grabar($temp)){
						$STOP = 1;
						break;
					}
					
					$i++;
					
			}
			
			
		}
		
		$noCobrados = $this->__getDetalleNoCobrados();
		
		if(!empty($noCobrados)):
		
			$total = count($noCobrados);
			$asinc->setTotal($total);
			$i = 0;	
			
			$temp = array();
			
			foreach($noCobrados as $socio){

				$asinc->actualizar($i,$total,"$i / $total - PROCESANDO NO COBRADOS >> " . $socio['LiquidacionSocioRendicion']['status']. ' -- ' . $socio['LiquidacionSocio']['apenom']);
				
				$descripcionCodigo = $this->BancoRendicionCodigo->getDescripcionCodigo($socio['LiquidacionSocioRendicion']['banco_intercambio'],$socio['LiquidacionSocioRendicion']['status']);

				$tipoDocumento = $this->LiquidacionCuota->GlobalDato('concepto_1',$socio['LiquidacionSocio']['tipo_documento']);
				$bancoIntercambio = $this->LiquidacionCuota->getNombreBanco($socio['LiquidacionSocioRendicion']['banco_intercambio']);
				
				$temp['AsincronoTemporal'] = array(
									
						'asincrono_id' => $asinc->id,
						'texto_1' => $tipoDocumento .'-'.$socio['LiquidacionSocio']['documento'],
						'texto_2' => $socio['LiquidacionSocio']['apenom'],
						'texto_3' => $bancoIntercambio,
						'texto_4' => $socio['LiquidacionSocioRendicion']['status'],
						'texto_5' => $descripcionCodigo,
						'texto_10' => 'REPORTE_2',
						'decimal_1' => round($socio['LiquidacionSocio']['importe_dto'],2),
						'decimal_2' => round($socio['LiquidacionSocioRendicion']['importe_debitado'],2),
//						'decimal_3' => $socio['LiquidacionSocio']['importe_debitado'],
//						'decimal_4' => $socio['LiquidacionSocio']['importe_imputado'],
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
			
			// ANALIZO COMISIONES
			
			

		endif;
		
		if($STOP == 0){
			$asinc->actualizar($total,$total,"FINALIZANDO...");
			$asinc->fin("**** PROCESO FINALIZADO ****");
		}			
		
		
		
	}
	//FIN PROCESO ASINCRONO
	
	####################################################################################################
	# METODOS ESPECIFICOS DEL PROCESO
	####################################################################################################
	

	function __getDetalle(){
		
		$conditions = array();
		$conditions['LiquidacionCuota.liquidacion_id'] = $this->liquidacion_id;
		$conditions['LiquidacionCuota.proveedor_id'] = $this->proveedor_id;
		if(!empty($this->tipo_producto)) $conditions['LiquidacionCuota.tipo_producto'] = $this->tipo_producto;
		if(!empty($this->tipo_cuota)) $conditions['LiquidacionCuota.tipo_cuota'] = $this->tipo_cuota;
		
		$socios = $this->LiquidacionCuota->find('all',array(
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
																			'table' => 'proveedor_comisiones',
																			'alias' => 'ProveedorComision',
																			'type' => 'left',
																			'foreignKey' => false,
																			'conditions' => array(
																									"ProveedorComision.proveedor_id = LiquidacionCuota.proveedor_id",
																									"ProveedorComision.codigo_organismo = LiquidacionCuota.codigo_organismo",
																									"ProveedorComision.tipo_producto = LiquidacionCuota.tipo_producto",
																									"ProveedorComision.tipo_cuota = LiquidacionCuota.tipo_cuota",
																									"ProveedorComision.tipo = 'COB'",
																									"ProveedorComision.comision > 0",
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
																						ProveedorComision.comision,
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
//		debug($socios);

		
		
		
		return $socios;
		
	}
	
	/**
	 * Esta mal este metodo porque si una persona tiene varios proveedores y algunos descuenta y otros no,
	 * informa como no cobrados a los que le desconto. Acordarse del caso de casa mabi
	 */
	function __getDetalleNoCobrados(){
		
//		$conditionsSubQuery['`LiquidacionCuota`.`liquidacion_id`'] = $this->liquidacion_id;
//		$conditionsSubQuery['`LiquidacionCuota`.`proveedor_id`'] = $this->proveedor_id;
//		if(!empty($this->tipo_cuota)) $conditionsSubQuery['`LiquidacionCuota`.`tipo_cuota`'] = $this->tipo_cuota;
//		if(!empty($this->tipo_producto)) $conditionsSubQuery['`LiquidacionCuota`.`tipo_producto`'] = $this->tipo_producto;
//		
//		$dbo = $this->LiquidacionCuota->getDataSource();
//		$subQuery = $dbo->buildStatement(
//		    array(
//		        'fields' => array('`LiquidacionCuota`.`socio_id`'),
//		        'table' => $dbo->fullTableName($this->LiquidacionCuota),
//		        'alias' => 'LiquidacionCuota',
//		        'limit' => null,
//		        'offset' => null,
//		        'joins' => array(),
//		        'conditions' => $conditionsSubQuery,
//		        'order' => null,
//		        'group' => null
//		    ),
//		    $this->LiquidacionCuota
//		);		
//		
//		$subQuery = ' 	`LiquidacionSocioRendicion`.`liquidacion_id` = '.$this->liquidacion_id.' AND 
//						`LiquidacionSocioRendicion`.`indica_pago` = 0 AND 
//						`LiquidacionSocioRendicion`.`socio_id` IN (' . $subQuery . ') ';
//		$subQueryExpression = $dbo->expression($subQuery);
//		$conditions[] = $subQueryExpression;
//		$registros = $this->LiquidacionSocioRendicion->find('all', compact('conditions'));
		
		$sql = "	select 
						LiquidacionSocio.tipo_documento,
						LiquidacionSocio.documento,
						LiquidacionSocio.apenom,
						LiquidacionSocioRendicion.banco_intercambio,
						LiquidacionSocioRendicion.status,
						LiquidacionSocioRendicion.importe_debitado
					from liquidacion_socio_rendiciones as LiquidacionSocioRendicion
						inner join liquidacion_socios as LiquidacionSocio on
						(LiquidacionSocio.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id and
					LiquidacionSocio.socio_id = LiquidacionSocioRendicion.socio_id)
					where
						LiquidacionSocioRendicion.liquidacion_id = ".$this->liquidacion_id." and
						LiquidacionSocioRendicion.indica_pago = 0 and 
						LiquidacionSocioRendicion.socio_id in
						(select socio_id from liquidacion_cuotas as LiquidacionCuota 
						where LiquidacionCuota.liquidacion_id = ".$this->liquidacion_id." and 
						LiquidacionCuota.proveedor_id = ".$this->proveedor_id . (!empty($this->tipo_producto) ? " and LiquidacionCuota.tipo_producto = '".$this->tipo_producto."'" : "") .  
						(!empty($this->tipo_cuota) ? " and LiquidacionCuota.tipo_cuota = '".$this->tipo_cuota."'" : "").")
					order by LiquidacionSocio.apenom";
		
		$registros = $this->LiquidacionSocioRendicion->query($sql);
		return $registros;
	}
	
}
?>