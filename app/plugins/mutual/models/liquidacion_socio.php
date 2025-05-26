<?php
/**
 * Liquidacion Socio
 * <br/>
 * Metodos Clave:
 * <ul>	
 * 	<li><b>Reliquidar</b> --> Reliquidacion puntual para un socio</li>
 *  <li><b>ProcesarArchivoIntercambio</b> --> Procesa los datos del archivo de intercambio</li>
 * </ul>
 *  
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */
class LiquidacionSocio extends MutualAppModel{

	/**
	 * Referencia nombre del modelo
	 * @var string
	 */
	var $name = 'LiquidacionSocio';
	
	/**
	 * Liquidaciones por Periodo por Socio
	 * funcion que genera la informacion para mostrar en la vista del padron de personas
	 * para la opcion -> estado de cuenta -> liquidaciones
	 * @param integer $socio_id
	 * @param string $periodo
	 * @return array
	 */
	function liquidacionesByPeriodoBySocio($socio_id,$periodo = null,$periodo_hasta=null,$codigoOrganismo = null, $proveedor_id = null){
		
		$liquidaciones = array();
		
		App::import('Model','Mutual.MutualAdicionalPendiente');
		$oAdic = new MutualAdicionalPendiente();
		
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();
		
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();		
		
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();
        
		App::import('Model','Mutual.LiquidacionSocioEnvio');
		$oLSE = new LiquidacionSocioEnvio();        

		App::import('Model','Pfyj.SocioReintegro');
		$oREINT = new SocioReintegro();	

		$periodo_hasta = (empty($periodo_hasta) ? $periodo : $periodo_hasta);
		
		if(!empty($periodo) && $periodo == $periodo_hasta):
		
			$liquidaciones[$periodo] = array(
								'cabecera_liquidacion' => $this->cuotasLiquidadasBySocioByPeriodo($socio_id,$periodo,$codigoOrganismo),
								'cuotas' => $oLC->cuotasLiquidadasBySocioByPeriodo($socio_id,$periodo,false,$codigoOrganismo,$proveedor_id),
								'adicionales_pendientes' => $oLC->cuotasPendientesBySocioByPeriodo($socio_id,$periodo,$codigoOrganismo,$proveedor_id),
								'rendicion' => $oLSR->getBySocioPeriodo($socio_id,$periodo,$codigoOrganismo),
								'reintegros' => $oREINT->getReintegrosBySocioByPeriodo($socio_id,$periodo),
								'intercambio_emitido' => $this->getStringIntercambioEmitido($socio_id, $periodo, $codigoOrganismo),
								'intercambio_recibido' => $oLSR->getStringIntercambioRecibido($socio_id, $periodo, $codigoOrganismo),
								'status'=> ($oLQ->isLiquidadaSobrePreImputacionAnterior($periodo) ? " *** SALDOS PROVISORIOS *** Liquidaciones anteriores pendientes de imputar y cerrar." : ""),
                                'envios' => $oLSE->getEnviosBySocioByPeriodo($socio_id, $periodo),
			);
			return $liquidaciones;
					
		else:

			App::import('model','Mutual.LiquidacionCuota');
			$oLCUO = new LiquidacionCuota();
			$periodos = $oLCUO->periodosBySocio($socio_id,'ASC');
			
			foreach($periodos as $periodo_socio){
				
				if(($periodo_socio >= $periodo) && ($periodo_socio <= $periodo_hasta)):
				
					$liquidaciones[$periodo_socio] = array(
										'cabecera_liquidacion' => $this->cuotasLiquidadasBySocioByPeriodo($socio_id,$periodo_socio,$codigoOrganismo),
										'cuotas' => $oLC->cuotasLiquidadasBySocioByPeriodo($socio_id,$periodo_socio,false,$codigoOrganismo,$proveedor_id),
										'adicionales_pendientes' => $oLC->cuotasPendientesBySocioByPeriodo($socio_id,$periodo_socio,$codigoOrganismo,$proveedor_id),
										'rendicion' => $oLSR->getBySocioPeriodo($socio_id,$periodo_socio,$codigoOrganismo),
										'reintegros' => $oREINT->getReintegrosBySocioByPeriodo($socio_id,$periodo_socio),
										'intercambio_emitido' => $this->getStringIntercambioEmitido($socio_id, $periodo_socio, $codigoOrganismo),
										'intercambio_recibido' => $oLSR->getStringIntercambioRecibido($socio_id, $periodo_socio, $codigoOrganismo),
										'status'=> ($oLQ->isLiquidadaSobrePreImputacionAnterior($periodo_socio) ? " *** SALDOS PROVISORIOS *** Liquidaciones anteriores pendientes de imputar y cerrar." : ""),
                                        'envios' => $oLSE->getEnviosBySocioByPeriodo($socio_id, $periodo_socio),
					);

					endif;
			}
			
			return $liquidaciones;
			
		endif;
	}
	
	
	/**
	 * Periodos por Socio
	 * devuelve los periodos liquidados para un socio determinado
	 * @param integer $socio_id
	 * @param string $order
	 * @return array
	 */
	function periodosBySocio($socio_id,$order='ASC',$limit=0){
		$periodos = null;
		$this->bindModel(array('belongsTo' => array('Liquidacion')));
		if(empty($socio_id)){return null;}
		$sql = "SELECT 
                    Liquidacion.periodo, LiquidacionSocio.socio_id
                FROM
                    `liquidacion_socios` AS `LiquidacionSocio`
                        INNER JOIN
                    `liquidaciones` AS `Liquidacion` ON (`LiquidacionSocio`.`liquidacion_id` = `Liquidacion`.`id`)
                WHERE
                    `LiquidacionSocio`.`socio_id` = $socio_id
                GROUP BY `Liquidacion`.`periodo` , `LiquidacionSocio`.`socio_id`
                ORDER BY `Liquidacion`.`periodo` $order " . (!empty($limit) ? " LIMIT $limit" : " ");
		$result = $this->query($sql);
		$periodos = Set::extract("{n}.Liquidacion.periodo",$result);
		return $periodos;
	}	
	
	/**
	 * Importe Liquidado por Socio por Periodo
	 * devuelve el total enviado a descuento para un socio para un periodo determinado
	 * @param integer $socio_id
	 * @param string $periodo
	 * @return float
	 */
	function importeLiquidadoBySocioByPeriodo($socio_id,$periodo){
		$this->bindModel(array('belongsTo' => array('Liquidacion')));
		$liquidacion = 	$this->find('all',array('conditions' => array('Liquidacion.periodo' => $periodo,'LiquidacionSocio.socio_id' => $socio_id), 'fields' => array('sum(importe_dto) as importe_dto')));
		$importeLiquidado = (isset($liquidacion[0][0]['importe_dto']) ? $liquidacion[0][0]['importe_dto'] : 0);
		return $importeLiquidado;
	}
	
	/**
	 * Importe a Debitar por Socio por Liquidacion
	 * @param unknown_type $socio_id
	 * @param unknown_type $liquidacion_id
	 * @return number
	 */
	function importeADebitarBySocioByLiquidacion($socio_id,$liquidacion_id){
		$this->bindModel(array('belongsTo' => array('Liquidacion')));
		$liquidacion = 	$this->find('all',array('conditions' => 
																array('LiquidacionSocio.liquidacion_id' => $liquidacion_id,'LiquidacionSocio.socio_id' => $socio_id),
												'fields' => array('sum(importe_adebitar) as importe_adebitar')				
		));
		
		$importe = (isset($liquidacion[0][0]['importe_adebitar']) ? $liquidacion[0][0]['importe_adebitar'] : 0);
		return $importe;
	}	
	
	/**
	 * Importe a Debitar por Socio por Periodo
	 * Devuelve el total a debitar para un socio / periodo determinado
	 * @param integer $socio_id
	 * @param string $periodo
	 * @return float
	 */
	function importeADebitarBySocioByPeriodo($socio_id,$periodo){
		$this->bindModel(array('belongsTo' => array('Liquidacion')));
		$liquidacion = 	$this->find('all',array('conditions' => array('Liquidacion.periodo' => $periodo,'LiquidacionSocio.socio_id' => $socio_id), 'fields' => array('sum(importe_adebitar) as importe_adebitar')));
		$importe = (isset($liquidacion[0][0]['importe_adebitar']) ? $liquidacion[0][0]['importe_adebitar'] : 0);
		return $importe;
	}			
	/**
	 * Cuotas Liquidadas por Socio por Periodo
	 * Devuelve el detalle de la liquidacion para el socio para un periodo determinado
	 * @param integer $socio_id
	 * @param string $periodo
	 * @return array
	 */
	function cuotasLiquidadasBySocioByPeriodo($socio_id,$periodo,$codigoOrganismo = null){
		$this->bindModel(array('belongsTo' => array('Liquidacion')));
		$conditions = array();
		$conditions['Liquidacion.periodo'] = $periodo;
		$conditions['LiquidacionSocio.socio_id'] = $socio_id;
		if(!empty($codigoOrganismo)) $conditions['Liquidacion.codigo_organismo'] = $codigoOrganismo;
		
		$socios = $this->find('all',array('conditions' => $conditions,'order' => array('LiquidacionSocio.codigo_organismo,LiquidacionSocio.registro')));
		foreach($socios as $idx => $socio){

			//saco el banco de intercambio
			$banco = parent::getBanco($socio['LiquidacionSocio']['banco_intercambio']);
			$socio['LiquidacionSocio']['banco_intercambio_desc'] = $banco['Banco']['nombre'];
			$socio['LiquidacionSocio']['turno'] = substr(trim($socio['LiquidacionSocio']['turno_pago']),-5,5);
			
			//si tiene generado el registro de intercambio lo concateno en un atributo del modelo para mostrarlo en la vista
			$socio['LiquidacionSocio']['intercambio_str'] = "";
			if(!empty($socio['LiquidacionSocio']['intercambio'])) $socio['LiquidacionSocio']['intercambio_str'] .= $socio['LiquidacionSocio']['intercambio'];
			
			$organismo = substr($socio['LiquidacionSocio']['codigo_organismo'],8,2);
			
			switch($organismo){
				case 22:
					$socios[$idx] = $this->__armaStrCBU($socio);
					break;
				case 77:
					$socios[$idx] = $this->__armaStrCJP($socio);
					break;
				case 66:
					$socios[$idx] = $this->__armaStrJN($socio);
					break;										
			}
		}
		return $socios;
	}
	
	/**
	 * Get Liquidacion por ID
	 * Carga una LiquidacionSocio para un id de liquidacion y organismo determinado
	 * @param integer $liquidacion_id
	 * @param string $organismo
	 * @return array
	 */
	function getLiquidacionByID($liquidacion_id,$organismo){
		$socios = $this->find('all',array(
									'conditions' => array('LiquidacionSocio.liquidacion_id' => $liquidacion_id,'LiquidacionSocio.codigo_organismo' => $organismo),
									'order' => array('LiquidacionSocio.apenom,LiquidacionSocio.codigo_empresa'),
		));
		
		$beneficioStr = "";
		$tipoOrganismo = parent::GlobalDato('concepto_2',$organismo);
		switch ($tipoOrganismo){
			case 'AC':
				foreach($socios as $idx => $socio){
					$socios[$idx] = $this->__armaStrCBU($socio);	
				}
				break;
			case 'JP':
				foreach($socios as $idx => $socio){
					$socios[$idx] = $this->__armaStrCJP($socio);	
				}
				break;
			case 'JN':
				foreach($socios as $idx => $socio){
					$socios[$idx] = $this->__armaStrJN($socio);	
				}
				break;								
		}
			
		return $socios;
	}
	
	
	
	
	/**
	 * Arma String CBU
	 * Setea en el array pasado por parametro el string que identifica el beneficio por CBU
	 * @param array $socios
	 * @return array
	 */
	function __armaStrCBU($socio){
		App::import('Model','Pfyj.PersonaBeneficio');
		$oPB = new PersonaBeneficio();
		$socio['LiquidacionSocio']['beneficio_str'] = $oPB->getStrBeneficio($socio['LiquidacionSocio']['persona_beneficio_id']);
		return $socio;
	}
	
	/**
	 * Arma String CJP
	 * Setea en el array pasado por parametro el string que identifica el beneficio por CJP
	 * @param array $socios
	 * @return array
	 */
	function __armaStrCJP($socio,$conCodDto=true){
		App::import('Model','Pfyj.PersonaBeneficio');
		$oPB = new PersonaBeneficio();
//		$string = $oPB->getStrBeneficio($socio['LiquidacionSocio']['persona_beneficio_id']);
		$ley = $socio['LiquidacionSocio']['nro_ley'];
		$nroBeneficio = $socio['LiquidacionSocio']['nro_beneficio'];
		$tipo = $socio['LiquidacionSocio']['tipo'];
		$subBeneficio = $socio['LiquidacionSocio']['sub_beneficio'];
		$porcentaje = $socio['LiquidacionSocio']['porcentaje'];
		
		$string = "LEY:$ley | TIPO:$tipo | BENFICIO:$nroBeneficio | SUB-BENEFICIO:$subBeneficio ($porcentaje%)";
		
		$codigo = $socio['LiquidacionSocio']['codigo_dto'];
		$subCodigo = $socio['LiquidacionSocio']['sub_codigo'];		
		if($conCodDto)$string .= " | CODIGO: $codigo-$subCodigo";
		$beneficio = $oPB->read('acuerdo_debito',$socio['LiquidacionSocio']['persona_beneficio_id']);
		if($subCodigo == 1 && $beneficio['PersonaBeneficio']['acuerdo_debito'] != 0){
			$string .= " *** ACUERDO DE DEBITO $ ".number_format($beneficio['PersonaBeneficio']['acuerdo_debito'],2)." ***";
		}
		$socio['LiquidacionSocio']['beneficio_str'] = $string;
		return $socio;		
	}

	/**
	 * Arma String JN (ANSES)
	 * Setea en el array pasado por parametro el string que identifica el beneficio por ANSES
	 * @param array $socio
	 * @return array
	 */
	function __armaStrJN($socio,$conCodDto=true){
		App::import('Model','Pfyj.PersonaBeneficio');
		$oPB = new PersonaBeneficio();	
		$codigo = $socio['LiquidacionSocio']['codigo_dto'];	
		$nroBeneficio = $socio['LiquidacionSocio']['nro_beneficio'];
		$porcentaje = $socio['LiquidacionSocio']['porcentaje'];
		if($conCodDto)$string = "BENEFICIO: $nroBeneficio ($porcentaje%) | CODIGO: $codigo";
		else $string = "BENEFICIO: $nroBeneficio ($porcentaje%)";
		$socio['LiquidacionSocio']['beneficio_str'] = $string;
//		$socio['LiquidacionSocio']['beneficio_str'] = $oPB->getStrBeneficio($socio['LiquidacionSocio']['persona_beneficio_id']) . " | CODIGO: $codigo";
		return $socio;		
	}	
	
	/**
	 * Reset Valorores de Intercambio para un socio especifico
	 * Limpia los campos utilizados por el intercambio (importe_debitado,status,fecha_pago)
	 * @param integer $liquidacion_intercambio_id
	 * @param integer $socio_id
	 * @return boolean
	 */
	function resetValoresIntercambio($liquidacion_intercambio_id,$socio_id){
		return $this->updateAll(
							array(
//									'LiquidacionSocio.banco_intercambio' => null,
//									'LiquidacionSocio.importe_adebitar' => 0,
									'LiquidacionSocio.importe_debitado' => 0, 
									'LiquidacionSocio.status' => null,
									'LiquidacionSocio.fecha_pago' => null
							),
							array(
								'LiquidacionSocio.socio_id' => $socio_id,
								'LiquidacionSocio.liquidacion_intercambio_id' => $liquidacion_intercambio_id
							)
		);
	}
	
	/**
	 * Reset general de valores de intercambio
	 * Limpia los campos utilizados por el intercambio (importe_debitado,status,fecha_pago)
	 * @param integer $liquidacion_intercambio_id
	 * @return boolean
	 */
	function resetValoresIntercambioTODOS($liquidacion_intercambio_id){
		return $this->updateAll(
							array(
//									'LiquidacionSocio.banco_intercambio' => null,
//									'LiquidacionSocio.importe_adebitar' => 0,
									'LiquidacionSocio.importe_debitado' => 0, 
									'LiquidacionSocio.status' => null,
									'LiquidacionSocio.fecha_pago' => null
							),
							array(
								'LiquidacionSocio.liquidacion_intercambio_id' => $liquidacion_intercambio_id
							)
		);
	}	
	
	/**
	 * Resumen de Imputacion
	 * Devuelve un array con los datos para el resumen de imputacion del CBU
	 * @param integer $liquidacion_id
	 * @param boolean $noEncontrados
	 * @return array
	 */
	function resumenImputacion($liquidacion_id,$noEncontrados=false){
		$informe = array();
		$resumenes = array();
		
		if(!$noEncontrados)$condiciones = array('LiquidacionSocio.liquidacion_id' => $liquidacion_id,'LiquidacionSocio.liquidacion_intercambio_id <>' => 0);
		else $condiciones = array('LiquidacionSocio.liquidacion_id' => $liquidacion_id,'LiquidacionSocio.importe_debitado' => 0);
		
		$bancos = $this->find('all',array(
									'conditions' => $condiciones,
									'fields' => array(
													'LiquidacionSocio.banco_intercambio'
												),
									'group' => array(
													'LiquidacionSocio.banco_intercambio'
													),			
									'order' => array('LiquidacionSocio.banco_intercambio'),
		));
		$bancos = Set::extract($bancos,'{n}.LiquidacionSocio.banco_intercambio');
		
		App::import('Model','Config.BancoRendicionCodigo');
		$oCODIGO = new BancoRendicionCodigo();		

		foreach($bancos as $banco_id){
			
			$ACU_CANTIDAD 		= 0;
			$ACU_LIQUIDADO 		= 0;
			$ACU_NOENVIADO 		= 0;
			$ACU_ADEBITAR 		= 0;
			$ACU_DEBITADO 		= 0;
			$ACU_IMPUTADO 		= 0;	

			$ACU_CANTIDAD_P 	= 0;
			$ACU_LIQUIDADO_P 	= 0;
			$ACU_NOENVIADO_P 	= 0;
			$ACU_ADEBITAR_P 	= 0;
			$ACU_DEBITADO_P 	= 0;
			$ACU_IMPUTADO_P 	= 0;				

			$ACU_CANTIDAD_P1 	= 0;
			$ACU_LIQUIDADO_P1 	= 0;
			$ACU_NOENVIADO_P1	= 0;
			$ACU_ADEBITAR_P1	= 0;
			$ACU_DEBITADO_P1 	= 0;
			$ACU_IMPUTADO_P1 	= 0;			
			
			$ACU_CANTIDAD_PR 	= 100;
			$ACU_LIQUIDADO_PR 	= 100;
			$ACU_NOENVIADO_PR	= 100;
			$ACU_ADEBITAR_PR	= 100;
			$ACU_DEBITADO_PR 	= 100;	
			$ACU_IMPUTADO_PR 	= 100;			
			
			$banco = parent::getBanco($banco_id);
			$condiciones['LiquidacionSocio.banco_intercambio'] = $banco_id;
			$resultados = $this->find('all',array(
										'conditions' => $condiciones,
										'fields' => array(
														'LiquidacionSocio.status',
														'COUNT(1) AS cantidad',
														'SUM(importe_dto) AS importe_dto',
														'SUM(importe_adebitar) AS importe_adebitar',
														'SUM(importe_debitado) AS importe_debitado',
														'SUM(importe_imputado) AS importe_imputado'
													),
										'group' => array(
														'LiquidacionSocio.status'
														),			
										'order' => array('LiquidacionSocio.importe_debitado DESC'),
			));
			//acumulo los totales
			foreach($resultados as $idx => $resumen){
				
				$ACU_CANTIDAD 	+= $resumen[0]['cantidad'];
				$ACU_LIQUIDADO 	+= $resumen[0]['importe_dto'];
				$ACU_NOENVIADO 	+= $resumen[0]['importe_dto'] - $resumen[0]['importe_adebitar'];
				$ACU_ADEBITAR 	+= $resumen[0]['importe_adebitar'];
				$ACU_DEBITADO 	+= $resumen[0]['importe_debitado'];
				$ACU_IMPUTADO 	+= $resumen[0]['importe_imputado'];
								
			}
						
			$noEnviado = 0;
			$INDICE = 0;
			foreach($resultados as $idx => $resumen){
				
				$noEnviado = $resumen[0]['importe_dto'] - $resumen[0]['importe_adebitar'];
				
				$resumen['LiquidacionSocio']['banco_intercambio']	= $banco_id;
				$resumen['LiquidacionSocio']['cantidad'] 			= $resumen[0]['cantidad'];
				$resumen['LiquidacionSocio']['importe_dto'] 		= $resumen[0]['importe_dto'];
				$resumen['LiquidacionSocio']['importe_noenviado']	= $noEnviado;
				$resumen['LiquidacionSocio']['importe_adebitar'] 	= $resumen[0]['importe_adebitar'];
				$resumen['LiquidacionSocio']['importe_debitado'] 	= $resumen[0]['importe_debitado'];
				$resumen['LiquidacionSocio']['importe_imputado'] 	= $resumen[0]['importe_imputado'];
				$resumen['LiquidacionSocio']['status_desc'] 		= $oCODIGO->getDescripcionCodigo($banco_id,$resumen['LiquidacionSocio']['status']);
				$resumen['LiquidacionSocio']['es_codigo_pago'] 		= $oCODIGO->isCodigoPago($banco_id,$resumen['LiquidacionSocio']['status']);
				
				
				//calculo los porcentajes
				$CANTIDAD 	= round($ACU_CANTIDAD,2);
				$LIQUIDADO 	= round($ACU_LIQUIDADO,2);
				$NOENVIADO 	= round($ACU_NOENVIADO,2);
				$ADEBITAR 	= round($ACU_ADEBITAR,2);
				$DEBITADO 	= round($ACU_DEBITADO,2);
				$IMPUTADO 	= round($ACU_IMPUTADO,2);				
				
				$resumen['LiquidacionSocio']['cantidad_porc'] 			= ($CANTIDAD != 0 ? ($resumen['LiquidacionSocio']['cantidad'] / $CANTIDAD) * 100 : 0);
				$resumen['LiquidacionSocio']['importe_dto_porc'] 		= ($LIQUIDADO != 0 ? ($resumen['LiquidacionSocio']['importe_dto'] / $LIQUIDADO) * 100 : 0);
				$resumen['LiquidacionSocio']['importe_noenviado_porc'] 	= ($NOENVIADO != 0 ? ($resumen['LiquidacionSocio']['importe_noenviado'] / $NOENVIADO) * 100 : 0);
				
				if($ADEBITAR != 0)$resumen['LiquidacionSocio']['importe_adebitar_porc'] 	= ($resumen['LiquidacionSocio']['importe_adebitar'] / $ADEBITAR) * 100;
				else $resumen['LiquidacionSocio']['importe_adebitar_porc'] = 0;
				
				if($DEBITADO != 0)$resumen['LiquidacionSocio']['importe_debitado_porc'] 	= ($resumen['LiquidacionSocio']['importe_debitado'] / $DEBITADO) * 100;
				else $resumen['LiquidacionSocio']['importe_debitado_porc'] = 0;
				
				if($IMPUTADO != 0)$resumen['LiquidacionSocio']['importe_imputado_porc'] 	= ($resumen['LiquidacionSocio']['importe_imputado'] / $IMPUTADO) * 100;
				else $resumen['LiquidacionSocio']['importe_imputado_porc'] = 0;
				
				$ACU_CANTIDAD_P 	+= $resumen['LiquidacionSocio']['cantidad_porc'];
				$ACU_LIQUIDADO_P 	+= $resumen['LiquidacionSocio']['importe_dto_porc'];
				$ACU_NOENVIADO_P 	+= $resumen['LiquidacionSocio']['importe_noenviado_porc'];
				$ACU_ADEBITAR_P 	+= $resumen['LiquidacionSocio']['importe_adebitar_porc'];
				$ACU_DEBITADO_P 	+= $resumen['LiquidacionSocio']['importe_debitado_porc'];
				$ACU_IMPUTADO_P 	+= $resumen['LiquidacionSocio']['importe_imputado_porc'];
				
				
				$ACU_CANTIDAD_P1 	+= round($resumen['LiquidacionSocio']['cantidad_porc'],2);
				$ACU_LIQUIDADO_P1 	+= round($resumen['LiquidacionSocio']['importe_dto_porc'],2);
				$ACU_NOENVIADO_P1 	+= round($resumen['LiquidacionSocio']['importe_noenviado_porc'],2);
				$ACU_ADEBITAR_P1 	+= round($resumen['LiquidacionSocio']['importe_adebitar_porc'],2);
				$ACU_DEBITADO_P1 	+= round($resumen['LiquidacionSocio']['importe_debitado_porc'],2);	
				$ACU_IMPUTADO_P1 	+= round($resumen['LiquidacionSocio']['importe_imputado_porc'],2);				
				
				$resultados[$idx] = $resumen['LiquidacionSocio'];
				$INDICE++;
				
			}
			
			//saco la diferencia a 100 para cargarselo al ultimo
			$dif_cantidad 	= round(100 - $ACU_CANTIDAD_P1,2);
			$dif_liquidado 	= round(100 - $ACU_LIQUIDADO_P1,2);
			$dif_noenviado 	= round(100 - $ACU_NOENVIADO_P1,2);
			$dif_enviado 	= round(100 - $ACU_ADEBITAR_P1,2);
			$dif_debitado 	= round(100 - $ACU_DEBITADO_P1,2);
			$dif_imputado 	= round(100 - $ACU_IMPUTADO_P1,2);
			
			$resultados[$idx]['cantidad_porc'] += $dif_cantidad;
			$resultados[$idx]['importe_dto_porc'] += $dif_liquidado;
			$resultados[$idx]['importe_noenviado_porc'] += $dif_noenviado;
			$resultados[$idx]['importe_adebitar_porc'] += $dif_enviado;
			$resultados[$idx]['importe_debitado_porc'] += $dif_debitado;
			$resultados[$idx]['importe_imputado_porc'] += $dif_imputado;
			
		
			$resumenes['codigo_banco_intercambio'] = $banco_id;
			$resumenes['denominacion'] = $banco['Banco']['nombre'];
			$resumenes['detalle'] = $resultados;
			$resumenes['totales'] = array(
							'cantidad' => $ACU_CANTIDAD,
							'importe_dto' => $ACU_LIQUIDADO,
							'importe_noenviado' => $ACU_NOENVIADO,
							'importe_adebitar' => $ACU_ADEBITAR,
							'importe_debitado' => $ACU_DEBITADO,
							'importe_imputado' => $ACU_IMPUTADO,
							'cantidad_porc' => $ACU_CANTIDAD_P,
							'importe_dto_porc' => $ACU_LIQUIDADO_P,
							'importe_noenviado_porc' => $ACU_NOENVIADO_P,
							'importe_adebitar_porc' => $ACU_ADEBITAR_P,	
							'importe_debitado_porc' => $ACU_DEBITADO_P,	
							'importe_imputado_porc' => $ACU_IMPUTADO_P,	
			);			
						
			array_push($informe,$resumenes);
		}
	
		return $informe;
	}
	
	/**
	 * Resumen de Imputacion por Codigo
	 * Devuelve el resumen de imputacion para un codigo de status indicado
	 * @param integer $liquidacion_id
	 * @param string $codigo_status
	 * @param integer $banco_id
	 * @return array
	 */
	function resumenImputacionByCodigo($liquidacion_id,$codigo_status,$banco_id){

		$condiciones = array(
								'LiquidacionSocio.liquidacion_id' => $liquidacion_id,
								'LiquidacionSocio.status' => $codigo_status,
								'LiquidacionSocio.banco_intercambio' => $banco_id,	
								'LiquidacionSocio.liquidacion_intercambio_id <>' => 0
		);
		$resultados = $this->find('all',array(
									'conditions' => $condiciones,
									'fields' => array(
										'LiquidacionSocio.codigo_organismo',
										'LiquidacionSocio.tipo_documento',
										'LiquidacionSocio.documento',
										'LiquidacionSocio.apenom',
										'LiquidacionSocio.persona_beneficio_id',
										'LiquidacionSocio.tipo',
										'LiquidacionSocio.nro_ley',
										'LiquidacionSocio.nro_beneficio',
										'LiquidacionSocio.sub_beneficio',
										'LiquidacionSocio.porcentaje',
										'LiquidacionSocio.codigo_dto',
										'LiquidacionSocio.sub_codigo',
										'LiquidacionSocio.importe_dto',
										'LiquidacionSocio.importe_adebitar',
										'LiquidacionSocio.importe_debitado',
										'LiquidacionSocio.importe_imputado'
									),
									'order' => array('LiquidacionSocio.apenom'),
		));

		foreach($resultados as $idx => $resultado){

			$tipoDocumento = parent::getGlobalDato('concepto_1',$resultado['LiquidacionSocio']['tipo_documento']);
			$resultado['LiquidacionSocio']['tipo_documento_desc'] = $tipoDocumento['GlobalDato']['concepto_1'];
			
			$resultado['LiquidacionSocio']['importe_noenviado'] = $resultado['LiquidacionSocio']['importe_dto'] - $resultado['LiquidacionSocio']['importe_adebitar'];
			
			$tipoOrganismo = parent::getGlobalDato('concepto_2',$resultado['LiquidacionSocio']['codigo_organismo']);
			switch($tipoOrganismo['GlobalDato']['concepto_2']){
				case 'AC':
					$resultados[$idx] = $this->__armaStrCBU($resultado);
					break;
				case 'JP':
					$resultados[$idx] = $this->__armaStrCJP($resultado);
					break;
				case 'JN':
					$resultados[$idx] = $this->__armaStrJN($resultado);
					break;										
			}
			
		}		

//		debug($resultados);
//		exit;	
		return $resultados;
		
	}
	

	/**
	 * Resumen Registros No Encontrados
	 * Devuelve los registros que no se encontraron en el archivo de intercambio
	 * @param integer $liquidacion_id
	 * @return array
	 */
	function resumenRegistrosNoEncontrados($liquidacion_id){

		$condiciones = array(
								'LiquidacionSocio.liquidacion_id' => $liquidacion_id,
								'LiquidacionSocio.status' => null,
								'LiquidacionSocio.banco_intercambio' => null,	
								'LiquidacionSocio.liquidacion_intercambio_id' => 0
		);

		$resultados = $this->find('all',array(
									'conditions' => $condiciones,
									'fields' => array(
										'LiquidacionSocio.codigo_organismo',
										'LiquidacionSocio.tipo_documento',
										'LiquidacionSocio.documento',
										'LiquidacionSocio.apenom',
										'LiquidacionSocio.persona_beneficio_id',
										'LiquidacionSocio.tipo',
										'LiquidacionSocio.nro_ley',
										'LiquidacionSocio.nro_beneficio',
										'LiquidacionSocio.sub_beneficio',
										'LiquidacionSocio.porcentaje',
										'LiquidacionSocio.codigo_dto',
										'LiquidacionSocio.sub_codigo',
										'LiquidacionSocio.importe_dto',
										'LiquidacionSocio.importe_adebitar',
										'LiquidacionSocio.importe_debitado',
										'LiquidacionSocio.importe_imputado'
									),
									'order' => array('LiquidacionSocio.apenom'),
		));

		foreach($resultados as $idx => $resultado){

			$tipoDocumento = parent::getGlobalDato('concepto_1',$resultado['LiquidacionSocio']['tipo_documento']);
			$resultado['LiquidacionSocio']['tipo_documento_desc'] = $tipoDocumento['GlobalDato']['concepto_1'];
			
			$resultado['LiquidacionSocio']['importe_noenviado'] = $resultado['LiquidacionSocio']['importe_dto'] - $resultado['LiquidacionSocio']['importe_adebitar'];
			
			$tipoOrganismo = parent::getGlobalDato('concepto_2',$resultado['LiquidacionSocio']['codigo_organismo']);
			switch($tipoOrganismo['GlobalDato']['concepto_2']){
				case 'AC':
					$resultados[$idx] = $this->__armaStrCBU($resultado);
					break;
				case 'JP':
					$resultados[$idx] = $this->__armaStrCJP($resultado);
					break;
				case 'JN':
					$resultados[$idx] = $this->__armaStrJN($resultado);
					break;										
			}
			
		}		

//		debug($resultados);
//		exit;
		
		return $resultados;
		
	}	
	
	/**
	 * Total Reintegros
	 * Devuelve el total de reintegros para una liquidacion
	 * @param integer $liquidacion_id
	 * @return float
	 */
	function totalReintegros($liquidacion_id){
		$total = array();
		$reintegros = $this->reintegros($liquidacion_id);
		if(empty($reintegros)) return 0;
		$cantidad = 0;
		$cantidad_anticipos = 0;
		$total = 0;
		$total_anticipos = 0;
		foreach($reintegros as $reintegro){
			$total += $reintegro['LiquidacionSocio']['importe_reintegro'];
			$cantidad++;
			if(isset($reintegro['LiquidacionSocio']['importe_anticipado']) && $reintegro['LiquidacionSocio']['importe_anticipado'] != 0){
				$total_anticipos += $reintegro['LiquidacionSocio']['importe_anticipado'];
				$cantidad_anticipos++;
			}
		}
		$total = array('cantidad' => $cantidad, 'total' => $total - $total_anticipos, 'cantidad_anticipos' => $cantidad_anticipos, 'total_anticipos' => $total_anticipos);
		return $total;
	}
	
	/**
	 * Reintegros
	 * Devuelve los reintegros para una liquidacion
	 * @param integer $liquidacion_id
	 * @return array
	 */
	function reintegros($liquidacion_id,$armaDatos=true , $soloConAnticipos = false){
		
		$sql = "SELECT 
					socio_id,
					(SELECT 
						SUM(importe_dto) 
						FROM 
							liquidacion_socios AS LiquidacionSocio 
						WHERE 
							LiquidacionSocio.liquidacion_id = $liquidacion_id AND 
							LiquidacionSocio.socio_id = LiquidacionSocioRendicion.socio_id 
						GROUP BY  
							LiquidacionSocio.socio_id
					)AS importe_dto,						
					SUM(importe_debitado) AS importe_debitado,
					(SELECT 
						SUM(importe_debitado) 
						FROM 
							liquidacion_cuotas AS LiquidacionCuota 
						WHERE 
							LiquidacionCuota.liquidacion_id = $liquidacion_id AND 
							LiquidacionCuota.socio_id = LiquidacionSocioRendicion.socio_id 
							GROUP BY  LiquidacionSocioRendicion.socio_id
					)AS importe_imputado,
					(SELECT 
						SUM(saldo_actual) 
						FROM 
							liquidacion_cuotas AS LiquidacionCuota 
						WHERE 
							LiquidacionCuota.liquidacion_id = $liquidacion_id AND 
							LiquidacionCuota.socio_id = LiquidacionSocioRendicion.socio_id 
							GROUP BY  LiquidacionSocioRendicion.socio_id
					)AS saldo_actual, 						 
					(SUM(importe_debitado) - IFNULL((
												SELECT 
													SUM(importe_debitado) 
												FROM
													liquidacion_cuotas AS LiquidacionCuota 
												WHERE 
													LiquidacionCuota.liquidacion_id = $liquidacion_id AND 
													LiquidacionCuota.socio_id = LiquidacionSocioRendicion.socio_id 
													GROUP BY  LiquidacionSocioRendicion.socio_id
												),0)
					) AS importe_reintegro,
					IFNULL((
						SELECT SUM(importe_reintegro) FROM socio_reintegros AS SocioReintegro 
						WHERE 
							SocioReintegro.liquidacion_id = $liquidacion_id
							AND SocioReintegro.anticipado = 1
							AND SocioReintegro.socio_id = LiquidacionSocioRendicion.socio_id
						GROUP BY SocioReintegro.socio_id	
					),0) AS reintegro_anticipado,
                LiquidacionSocioRendicion.banco_intercambio,
                Banco.nombre
				FROM 
				liquidacion_socio_rendiciones AS LiquidacionSocioRendicion
                inner join bancos as Banco on (Banco.id = LiquidacionSocioRendicion.banco_intercambio)
				WHERE 
				LiquidacionSocioRendicion.liquidacion_id = $liquidacion_id AND 
				LiquidacionSocioRendicion.socio_id <> 0 AND 
				LiquidacionSocioRendicion.indica_pago = 1
				GROUP BY 
				socio_id 
				HAVING 
				(importe_debitado - importe_imputado) > 0
				UNION
				SELECT
					socio_id,
					IFNULL((SELECT 
						SUM(importe_dto) 
						FROM 
							liquidacion_socios AS LiquidacionSocio 
						WHERE 
							LiquidacionSocio.liquidacion_id = $liquidacion_id AND 
							LiquidacionSocio.socio_id = LiquidacionSocioRendicion.socio_id 
						GROUP BY  
							LiquidacionSocio.socio_id
					),0)AS importe_dto,						
					SUM(importe_debitado) AS importe_debitado,
					0 AS importe_imputado,
					0 AS saldo_actual, 						 
					SUM(importe_debitado) AS importe_reintegro,
					IFNULL((
						SELECT SUM(importe_reintegro) FROM socio_reintegros AS SocioReintegro 
						WHERE 
							SocioReintegro.liquidacion_id = $liquidacion_id
							AND SocioReintegro.anticipado = 1
							AND SocioReintegro.socio_id = LiquidacionSocioRendicion.socio_id
						GROUP BY SocioReintegro.socio_id	
					),0) AS reintegro_anticipado,
                LiquidacionSocioRendicion.banco_intercambio,
                Banco.nombre                    
				FROM 
				liquidacion_socio_rendiciones AS LiquidacionSocioRendicion
                inner join bancos as Banco on (Banco.id = LiquidacionSocioRendicion.banco_intercambio)
				WHERE 
				LiquidacionSocioRendicion.liquidacion_id = $liquidacion_id AND 
				LiquidacionSocioRendicion.socio_id <> 0 AND 
				LiquidacionSocioRendicion.indica_pago = 1 AND
				LiquidacionSocioRendicion.socio_id NOT IN (SELECT socio_id FROM liquidacion_cuotas WHERE liquidacion_id = $liquidacion_id)
				GROUP BY 
				socio_id 
				HAVING 
				(importe_debitado - importe_imputado) > 0
                                UNION
                                    SELECT
					socio_id,
					IFNULL((SELECT 
						SUM(importe_dto) 
						FROM 
							liquidacion_socios AS LiquidacionSocio 
						WHERE 
							LiquidacionSocio.liquidacion_id = $liquidacion_id AND 
							LiquidacionSocio.socio_id = LiquidacionSocioRendicion.socio_id 
						GROUP BY  
							LiquidacionSocio.socio_id
					),0)AS importe_dto,						
					SUM(importe_debitado) AS importe_debitado,
					0 AS importe_imputado,
					0 AS saldo_actual, 						 
					SUM(importe_debitado) AS importe_reintegro,
					IFNULL((
						SELECT SUM(importe_reintegro) FROM socio_reintegros AS SocioReintegro 
						WHERE 
							SocioReintegro.liquidacion_id = $liquidacion_id
							AND SocioReintegro.anticipado = 1
							AND SocioReintegro.socio_id = LiquidacionSocioRendicion.socio_id
						GROUP BY SocioReintegro.socio_id	
					),0) AS reintegro_anticipado,
                LiquidacionSocioRendicion.banco_intercambio,
                Banco.nombre                    
				FROM 
				liquidacion_socio_rendiciones AS LiquidacionSocioRendicion
                inner join bancos as Banco on (Banco.id = LiquidacionSocioRendicion.banco_intercambio)
				WHERE 
				LiquidacionSocioRendicion.liquidacion_id = $liquidacion_id AND 
				ifnull(LiquidacionSocioRendicion.socio_id,0) = 0 AND 
				LiquidacionSocioRendicion.indica_pago = 1 
				GROUP BY 
				socio_id 
				HAVING 
				(importe_debitado - importe_imputado) > 0                                 
				UNION
				SELECT socio_id,0 AS importe_debitado,0 AS importe_imputado,0 AS importe_reintegro,0 AS importe_dto,0 AS saldo_actual,importe_reintegro AS reintegro_anticipado,'' as banco_intercambio,'' as nombre
				FROM socio_reintegros AS SocioReintegro
				WHERE liquidacion_id = $liquidacion_id AND anticipado = 1
				AND socio_id NOT IN (SELECT socio_id FROM liquidacion_socio_rendiciones WHERE liquidacion_id = $liquidacion_id);";
		
// 		debug($sql);
//        exit;
		$resultados = $this->query($sql);
		
		if(empty($resultados)) {return null;}
		
		if(!$armaDatos) {return $resultados;}
		
		App::import('Model','Pfyj.Socio');
		$oSOCIO = new Socio();
		
		App::import('Model','Pfyj.SocioReintegroPago');
		$oREINTEGRO_PAGO = new SocioReintegroPago();		
		
		App::import('Model', 'Mutual.LiquidacionTurno');
		$oTURNO = new LiquidacionTurno();		
		
		foreach($resultados as $idx => $resultado):
		
//			debug($resultado);
			
			$resultado['LiquidacionSocio']['socio_id'] = $resultado[0]['socio_id'];
			$resultado['LiquidacionSocio']['importe_debitado'] = $resultado[0]['importe_debitado'];
			$resultado['LiquidacionSocio']['importe_imputado'] = $resultado[0]['importe_imputado'];
			$resultado['LiquidacionSocio']['importe_reintegro'] = $resultado[0]['importe_reintegro'];
			$resultado['LiquidacionSocio']['importe_dto'] = $resultado[0]['importe_dto'];
			$resultado['LiquidacionSocio']['saldo_actual'] = $resultado[0]['saldo_actual'];		

			$resultado['LiquidacionSocio']['importe_anticipado'] = $resultado[0]['reintegro_anticipado'];
			
			//CALCULAR LOS PAGOS DEL REINTEGRO POR SOCIO Y POR LIQUIDACION_ID
			$resultado['LiquidacionSocio']['importe_pagado_socio'] = $oREINTEGRO_PAGO->getTotalPagoByLiquidacionSocioId($resultado['LiquidacionSocio']['socio_id'], $liquidacion_id);
			
			
//			$resultado['LiquidacionSocio']['saldo_reintegro'] = round($resultado[0]['importe_reintegro'] - $resultado[0]['reintegro_anticipado'],2);
			$resultado['LiquidacionSocio']['saldo_reintegro'] = round($resultado[0]['importe_reintegro'] - $resultado['LiquidacionSocio']['importe_pagado_socio'],2);
			
			//cargo los datos adicionales
			$liquiSocio = $this->find('all',array('conditions' => 
																	array(
																			'LiquidacionSocio.liquidacion_id' => $liquidacion_id,
																			'LiquidacionSocio.socio_id' => $resultado['LiquidacionSocio']['socio_id']
																	),
													'limit' => 1				
												)
			);
			
            
            $resultado['LiquidacionSocio']['codigo_organismo'] = "";
            $resultado['LiquidacionSocio']['tipo_documento'] = "";
            $resultado['LiquidacionSocio']['documento'] = "";
            $resultado['LiquidacionSocio']['apenom'] = "";
            $resultado['LiquidacionSocio']['persona_beneficio_id'] = "";
            $resultado['LiquidacionSocio']['tipo'] = "";
            $resultado['LiquidacionSocio']['nro_ley'] = "";
            $resultado['LiquidacionSocio']['nro_beneficio'] = "";
            $resultado['LiquidacionSocio']['sub_beneficio'] = "";
            $resultado['LiquidacionSocio']['porcentaje'] = "";
            $resultado['LiquidacionSocio']['codigo_dto'] = "";
            $resultado['LiquidacionSocio']['sub_codigo'] = "";
            $resultado['LiquidacionSocio']['importe_adebitar'] = "";

            $resultado['LiquidacionSocio']['tipo_documento_desc'] = parent::GlobalDato('concepto_1',$resultado['LiquidacionSocio']['tipo_documento']);

            $resultado['LiquidacionSocio']['importe_noenviado'] = $resultado['LiquidacionSocio']['importe_dto'] - $resultado['LiquidacionSocio']['importe_adebitar'];

            $resultado['LiquidacionSocio']['codigo_empresa'] = "";
            $resultado['LiquidacionSocio']['codigo_empresa_desc'] = parent::GlobalDato('concepto_1',$resultado['LiquidacionSocio']['codigo_empresa']);
            $resultado['LiquidacionSocio']['turno_pago'] = "";
            $resultado['LiquidacionSocio']['turno_pago_desc'] = $oTURNO->getDescripcionByTruno($resultado['LiquidacionSocio']['turno_pago']);
            
            
			if(!empty($liquiSocio)):
			
				$resultado['LiquidacionSocio']['codigo_organismo'] = $liquiSocio[0]['LiquidacionSocio']['codigo_organismo'];
				$resultado['LiquidacionSocio']['tipo_documento'] = $liquiSocio[0]['LiquidacionSocio']['tipo_documento'];
				$resultado['LiquidacionSocio']['documento'] = $liquiSocio[0]['LiquidacionSocio']['documento'];
				$resultado['LiquidacionSocio']['apenom'] = $liquiSocio[0]['LiquidacionSocio']['apenom'];
				$resultado['LiquidacionSocio']['persona_beneficio_id'] = $liquiSocio[0]['LiquidacionSocio']['persona_beneficio_id'];
				$resultado['LiquidacionSocio']['tipo'] = $liquiSocio[0]['LiquidacionSocio']['tipo'];
				$resultado['LiquidacionSocio']['nro_ley'] = $liquiSocio[0]['LiquidacionSocio']['nro_ley'];
				$resultado['LiquidacionSocio']['nro_beneficio'] = $liquiSocio[0]['LiquidacionSocio']['nro_beneficio'];
				$resultado['LiquidacionSocio']['sub_beneficio'] = $liquiSocio[0]['LiquidacionSocio']['sub_beneficio'];
				$resultado['LiquidacionSocio']['porcentaje'] = $liquiSocio[0]['LiquidacionSocio']['porcentaje'];
				$resultado['LiquidacionSocio']['codigo_dto'] = $liquiSocio[0]['LiquidacionSocio']['codigo_dto'];
				$resultado['LiquidacionSocio']['sub_codigo'] = $liquiSocio[0]['LiquidacionSocio']['sub_codigo'];
				$resultado['LiquidacionSocio']['importe_adebitar'] = $liquiSocio[0]['LiquidacionSocio']['importe_adebitar'];
				
//				$resultado['LiquidacionSocio']['tipo_documento_desc'] = parent::GlobalDato('concepto_1',$resultado['LiquidacionSocio']['tipo_documento']);
//				
//				$resultado['LiquidacionSocio']['importe_noenviado'] = $resultado['LiquidacionSocio']['importe_dto'] - $resultado['LiquidacionSocio']['importe_adebitar'];
//				
				$resultado['LiquidacionSocio']['codigo_empresa'] = $liquiSocio[0]['LiquidacionSocio']['codigo_empresa'];
//				$resultado['LiquidacionSocio']['codigo_empresa_desc'] = parent::GlobalDato('concepto_1',$resultado['LiquidacionSocio']['codigo_empresa']);
				$resultado['LiquidacionSocio']['turno_pago'] = $liquiSocio[0]['LiquidacionSocio']['turno_pago'];
//				$resultado['LiquidacionSocio']['turno_pago_desc'] = $oTURNO->getDescripcionByTruno($resultado['LiquidacionSocio']['turno_pago']);
				
				$tipoOrganismo = substr($resultado['LiquidacionSocio']['codigo_organismo'],8,2);
				
//				debug($liquiSocio);
				
				switch($tipoOrganismo){
					case 22:
						$resultado = $this->__armaStrCBU($resultado);
						break;
					case 77:
						$resultado = $this->__armaStrCJP($resultado,false);
						break;
					case 66:
						$resultado = $this->__armaStrJN($resultado,false);
						break;										
				}
	
				
			elseif(!empty($resultado['LiquidacionSocio']['socio_id'])):
			
				//la liquidacion del socio no existe, sacar datos para el reintegro
				$socio = $oSOCIO->read(null,$resultado['LiquidacionSocio']['socio_id']);
				$resultado['LiquidacionSocio']['tipo_documento'] = $socio['Persona']['tipo_documento'];
				$resultado['LiquidacionSocio']['documento'] = $socio['Persona']['documento'];
				$resultado['LiquidacionSocio']['apenom'] = $socio['Persona']['apellido'].', '.$socio['Persona']['nombre'];
				$resultado['LiquidacionSocio']['beneficio_str'] = "ERROR: *** NO EXISTE LIQUIDACION PARA ESTE SOCIO ***";
				$resultado['LiquidacionSocio']['importe_adebitar'] = 0;
				$resultado['LiquidacionSocio']['importe_noenviado'] = 0;
			
                        else:        
                            
                            $resultado['LiquidacionSocio']['tipo_documento'] = "";
                            $resultado['LiquidacionSocio']['documento'] = "";
                            $resultado['LiquidacionSocio']['apenom'] = "";                            
                            $resultado['LiquidacionSocio']['beneficio_str'] = "ERROR: *** PERSONA DESCONOCIDA ***";           
			endif;
            
            $resultado['LiquidacionSocio']['banco_intercambio'] = $resultado[0]['banco_intercambio'];
            $resultado['LiquidacionSocio']['banco_intercambio_nombre'] = $resultado[0]['nombre'];            
			
			$resultados[$idx] = $resultado;
			
		endforeach;
		
//		exit;

//		debug($resultados);
//		exit;

		$resultadosLiqSoc = Set::extract("/LiquidacionSocio",$resultados);
		$resultadosApenom = Set::sort($resultadosLiqSoc,"{n}.LiquidacionSocio.apenom","asc");
		
		return $resultadosApenom;
		
	
	}		
	
	/**
	 * Total Primer Descuento No Cobrado
	 * Devuelve el total (registros y cantidad) de las liquidaciones de socios
	 * que son altas y que no se descontaron
	 * @param $liquidacion_id
	 * @return array
	 */
	function totalPrimerDtoNoCobrado($liquidacion_id){
            $noCobrados = $this->primerDtoNoCobrado($liquidacion_id);
            if(empty($noCobrados)) {return 0;}
            $cantidad = 0;
            $totalAcum = 0;
            foreach($noCobrados as $noCobrado){
                    $cantidad += 1;
                    $totalAcum += $noCobrado['LiquidacionSocio']['importe_dto'];
            }
            $total = array('cantidad' => $cantidad, 'total' => $total);
            return $total;
	}
	
	/**
	 * Total Liquidado No rendido en Archivos de intercambio
	 * @param $liquidacion_id
	 * @return unknown_type
	 */
	function totalLiquidadosNoRendidosEnArchivos($liquidacion_id,$soloEnviados=false,$cjp=false,$subCodCjp=1){
            $registros = $this->liquidadosNoRendidosEnArchivos($liquidacion_id,false,$soloEnviados,$cjp,$subCodCjp);

            if (empty($registros)) {return 0;}
            $cantidad = 0;
            $totalAcum = 0;
            foreach($registros as $registro){
                    $cantidad += 1;
                    $totalAcum += $registro['LiquidacionSocio']['importe_adebitar'];
            }
            $total = array('cantidad' => $cantidad, 'total' => $total);
            return $total;
	}	
	
	/**
	 * Primer Descuento No Cobrado
	 * devuelve las liquidaciones de socios que son altas y que no se cobraron
	 * @param integer $liquidacion_id
	 * @return array
	 */
	function primerDtoNoCobrado($liquidacion_id,$armaDatos=true){

		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();		
		
		$conditionsSubQuery['`LiquidacionSocioRendicion`.`liquidacion_id`'] = $liquidacion_id;
		
		$dbo = $oLSR->getDataSource();
		$subQuery = $dbo->buildStatement(
		    array(
		        'fields' => array('`LiquidacionSocioRendicion`.`socio_id`'),
		        'table' => $dbo->fullTableName($oLSR),
		        'alias' => 'LiquidacionSocioRendicion',
		        'limit' => null,
		        'offset' => null,
		        'joins' => array(),
		        'conditions' => $conditionsSubQuery,
		        'order' => null,
		        'group' => null
		    ),
		   	$oLSR
		);		
		
		$subQuery = ' 	`LiquidacionSocio`.`liquidacion_id` = '.$liquidacion_id.' AND 
						`LiquidacionSocio`.`alta` = 1 AND 
						`LiquidacionSocio`.`socio_id` NOT IN (' . $subQuery . ') ';
		$subQueryExpression = $dbo->expression($subQuery);
		
		
		
		$conditions[] = $subQueryExpression;
		
		$resultados = $this->find('all', compact('conditions'),null,"LiquidacionSocio.apenom");
		
		if(empty($resultados)) return null;
		
		if(!$armaDatos) return $resultados;
		
		foreach($resultados as $idx => $resultado){

			$tipoDocumento = parent::getGlobalDato('concepto_1',$resultado['LiquidacionSocio']['tipo_documento']);
			$resultado['LiquidacionSocio']['tipo_documento_desc'] = $tipoDocumento['GlobalDato']['concepto_1'];
			
			$resultado['LiquidacionSocio']['importe_noenviado'] = $resultado['LiquidacionSocio']['importe_dto'] - $resultado['LiquidacionSocio']['importe_adebitar'];
			
			$tipoOrganismo = parent::getGlobalDato('concepto_2',$resultado['LiquidacionSocio']['codigo_organismo']);
			switch($tipoOrganismo['GlobalDato']['concepto_2']){
				case 'AC':
					$resultados[$idx] = $this->__armaStrCBU($resultado);
					break;
				case 'JP':
					$resultados[$idx] = $this->__armaStrCJP($resultado);
					break;
				case 'JN':
					$resultados[$idx] = $this->__armaStrJN($resultado);
					break;										
			}
			
		}		
		
		return $resultados;
		
	}	
	
	/**
	 * Liquidados no Rendidos en Archivos de Intercambio
	 * @param unknown_type $liquidacion_id
	 * @param unknown_type $armaDatos
	 * @return unknown_type
	 */
	function liquidadosNoRendidosEnArchivos($liquidacion_id,$armaDatos=true,$soloEnviados=false,$cjp=false,$subCodCjp=1){
		
		$sql = "SELECT 
					LiquidacionSocio.documento,
					LiquidacionSocio.tipo_documento,
					LiquidacionSocio.apenom,
					LiquidacionSocio.persona_beneficio_id,
					SUM(LiquidacionSocio.importe_dto) AS importe_dto,
					SUM(LiquidacionSocio.importe_adebitar) AS importe_adebitar 
				FROM 
				liquidacion_socios AS  LiquidacionSocio

				LEFT JOIN (SELECT socio_id FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion
				WHERE LiquidacionSocioRendicion.liquidacion_id = $liquidacion_id) t on t.socio_id = LiquidacionSocio.socio_id

				WHERE LiquidacionSocio.liquidacion_id = $liquidacion_id
		
				AND (t.socio_id  IS NULL AND IFNULL(LiquidacionSocio.intercambio,0) <> 0)

				GROUP BY
					LiquidacionSocio.documento,
					LiquidacionSocio.tipo_documento,
					LiquidacionSocio.apenom,
					LiquidacionSocio.persona_beneficio_id
				ORDER BY 
					LiquidacionSocio.apenom";
		
		if($cjp):
			$sql = "SELECT 
						LiquidacionSocio.documento,
						LiquidacionSocio.tipo_documento,
						LiquidacionSocio.apenom,
						LiquidacionSocio.persona_beneficio_id,
						".($subCodCjp == 1 ? "LiquidacionSocio.orden_descuento_id," : "")."
						SUM(LiquidacionSocio.importe_dto) AS importe_dto,
						SUM(LiquidacionSocio.importe_adebitar) AS importe_adebitar 
					FROM 
					liquidacion_socios AS  LiquidacionSocio
					
                    LEFT JOIN 
					(SELECT socio_id,orden_descuento_id FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion
					WHERE LiquidacionSocioRendicion.liquidacion_id =  $liquidacion_id
					AND LiquidacionSocioRendicion.sub_codigo = '".$subCodCjp."')  t 
					ON ".($subCodCjp == 0 ? "t.socio_id = LiquidacionSocio.socio_id" : "t.orden_descuento_id = LiquidacionSocio.orden_descuento_id")."
				
					
					WHERE LiquidacionSocio.liquidacion_id = $liquidacion_id
					AND LiquidacionSocio.sub_codigo = '$subCodCjp'

					".($subCodCjp == 0 ? " AND t.socio_id IS NULL " : " AND t.orden_descuento_id IS NULL")."

					AND IFNULL(LiquidacionSocio.intercambio,0) <> 0 
					GROUP BY
						LiquidacionSocio.documento,
						LiquidacionSocio.tipo_documento,
						LiquidacionSocio.apenom,
						LiquidacionSocio.persona_beneficio_id
						".($subCodCjp == 1 ? ",LiquidacionSocio.orden_descuento_id" : "")."
					ORDER BY 
						LiquidacionSocio.apenom";		
		
		endif;
		
//		debug($sql);
//		exit;
		
		$resultados = $this->query($sql);
		
		
		if(empty($resultados)) return null;
		
		foreach($resultados as $idx => $resultado):
		
			$resultado['LiquidacionSocio']['importe_dto'] = $resultado[0]['importe_dto'];
			$resultado['LiquidacionSocio']['importe_adebitar'] = $resultado[0]['importe_adebitar'];
		
			$resultados[$idx] = $resultado;
			
		endforeach;
		
		
		
		if(!$armaDatos) return $resultados;		
		
		App::import('Model','Pfyj.PersonaBeneficio');
		$oPB = new PersonaBeneficio();
				
		
		foreach($resultados as $idx => $resultado):
		
			$resultado['LiquidacionSocio']['tipo_documento_desc'] = parent::GlobalDato('concepto_1',$resultado['LiquidacionSocio']['tipo_documento']);
			$resultado['LiquidacionSocio']['beneficio_str'] = $oPB->getStrBeneficio($resultado['LiquidacionSocio']['persona_beneficio_id']);
			$resultados[$idx] = $resultado;
			
		endforeach;		
		
		
		return $resultados;
		
	}	
	
	
	
	
	
	/**
	 * Reliquidar
	 * Reliquida la deuda de un socio a un periodo determinado. Procesa todas las liquidaciones abiertas no imputadas (segun parametros y segun Organismo) para
	 * el periodo indicado y va armando la liquidacion. En caso de detectar conceptos permanentes no liquidados para el periodo los liquida.
	 * Si las liquidaciones tienen cargado al informacion de cobro, procesa la misma.
	 * 
	 * @author adrian [23/01/2012]
	 * @param $socio_id
	 * @param $periodo
	 * @param $cerrada si == true carga unicamente las que estan cerradas
	 * @param $imputada == true carga las que estan imputadas
	 * @param $organismo
	 * @param $soloProcesaIntercambio == true procesa solamente el archivo de intercambio
	 * @param $excludeLiquidacionBloquedas = true filtra las liquidaciones que estan marcadas como bloqueadas
	 * @param $excludeLiquidacionEnProceso = true filtra las liquidaciones que estan marcadas como en proceso
	 * 
	 * @return array $status
	 */
	function reliquidar($socio_id,$periodo,$cerrada=FALSE,$imputada=FALSE,$organismo=null,$soloProcesaIntercambio=false,$excludeLiquidacionBloquedas = true, $excludeLiquidacionEnProceso = true,$liquidacion_id = NULL,$generaLiqSocio = TRUE){
		

		$status = array();
		
		//inicio una transaccion
		$ERROR = FALSE;
//		$this->begin();
		
	
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();	
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();	
			
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();		
		
		
		$status[0] = 0;
		$status[1] = "";
		$status[2] = "";
		
		$BANCO_CONTROL = NULL;
		
//		exit;
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		// DETERMINO QUE LIQUIDACIONES EXISTEN PARA EL PERIODO QUE SE RELIQUIDA
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                if(empty($liquidacion_id)){
                    $liquiPeriodos = $oLQ->query("select Liquidacion.codigo_organismo,Liquidacion.cerrada,Liquidacion.imputada from liquidaciones AS Liquidacion where periodo = '$periodo'");
                    foreach($liquiPeriodos as $liq){
                            $status[2] .= parent::GlobalDato('concepto_1', $liq['Liquidacion']['codigo_organismo']) . ": *** ".($liq['Liquidacion']['cerrada'] == 1 ? "CERRADA" : "ABIERTA")." *** | ";
                    }
                    $liquidaciones = $oLQ->getLiquidacionesByPeriodo($periodo,$cerrada,$imputada,$organismo,$excludeLiquidacionBloquedas,$excludeLiquidacionEnProceso);
                }else{
                    $liquidaciones = $oLQ->find('all',array('conditions' => array('Liquidacion.id' => $liquidacion_id)));
                }
//		debug($liquidacion_id);
//                debug($liquidaciones);
//                exit;

		if(empty($liquidaciones)){
			$status[0] = 1;
			$status[1] = "NO EXISTEN LIQUIDACIONES ABIERTAS PARA RELIQUIDAR AL PERIODO INDICADO";
//			$this->rollback();
			return $status;		
		}		
		

                ///////////////////////////////////////////////////////////////////
                //VERIFICAR SI TIENE SETEADO EN mutual.ini EL CONTROL PARA LIQUIDAR
                //LA CUOTA SOCIAL SOLO SI TIENE DEUDA
                ///////////////////////////////////////////////////////////////////        
                $liqSiTieneDeuda = false;
                $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
                if(isset($INI_FILE['general']['cuota_social_permanente']) && $INI_FILE['general']['cuota_social_permanente'] == 0){
                    $liqSiTieneDeuda = true;
                }        

                ##################################################################################################################
                $file = parse_ini_file(CONFIGS.'mutual.ini', true);
                $CONTROL_NACION = false;
                if(isset($file['general']['banco_nacion_debito_periodo']) && $file['general']['banco_nacion_debito_periodo'] != ""){
                    $CONTROL_NACION = true;
                    $BANCO_CONTROL = $file['general']['banco_nacion_debito_periodo'];
                }          

                $LIQUIDA_DEUDA_CBU_SP = FALSE;
                // if(isset($file['general']['sp_liquida_deuda_cbu']) && $file['general']['sp_liquida_deuda_cbu'] == "1"){
                //     $LIQUIDA_DEUDA_CBU_SP = FALSE;
                // }
                if(isset($file['general']['discrimina_conceptos_permanentes_orden_debito']) && $file['general']['discrimina_conceptos_permanentes_orden_debito'] == "1"){
                    $DISCRIMINA_PERMANENTES = true;
                }
                if(isset($file['general']['enviar_periodo_mas_mora']) && $file['general']['enviar_periodo_mas_mora'] == "1"){
                    $CONSOLIDADO = true;
                }          
        
        
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
		// REPROCESO LAS LIQUIDACIONES ABIERTAS PARA EL SOCIO
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		foreach($liquidaciones as $liquidacion):

			$organismo = $liquidacion['Liquidacion']['codigo_organismo'];
			$liquidacion_id = $liquidacion['Liquidacion']['id'];
			$preImputa = $liquidacion['Liquidacion']['sobre_pre_imputacion'];
		
			if(!$soloProcesaIntercambio):
				//$socio_id,$periodo,$organismo,$liquidacion_id,$generaLiqSocio=true,$pre_imputacion=false
                
                // if(substr($organismo,8,2) == "22" && $LIQUIDA_DEUDA_CBU_SP){
                //     //$this->query("CALL SP_LIQUIDA_DEUDA_CBU($socio_id,'$periodo','$organismo',".($preImputa == 1 ? 'TRUE':'FALSE').")");
                // }else{
                    //$socio_id,$periodo,$organismo,$liquidacion_id,$generaLiqSocio=true,$pre_imputacion=false,$cuotaSocialSoloDeuda=false,$CONTROL_NACION=false,$BANCO_CONTROL = null,$DISCRIMINA_PERMANENTES = FALSE,$tipoFiltro=0,$CONSOLIDADO = false
                    $statusLiqui = $this->liquidar($socio_id,$periodo,$organismo,$liquidacion_id,$generaLiqSocio,$preImputa,$liqSiTieneDeuda,$CONTROL_NACION,$BANCO_CONTROL,$DISCRIMINA_PERMANENTES,0,$CONSOLIDADO);
                    if(isset($statusLiqui[0]) && $statusLiqui[0] == 1):
                        $status[0] = $statusLiqui[0];
                        $status[1] = $statusLiqui[1];
                        $ERROR = TRUE;
                        break;
                    endif;
                // }
                
			endif;
			##############################################################################################
			#PASO 4: BUSCO SI HAY PROCESADO UN ARCHIVO PARA ESTA LIQUIDACION Y LO APLICO
			##############################################################################################
			//reproceso el intercambio
			$oLSR->reprocesaIntercambio($liquidacion_id,$socio_id,$organismo);
			
			
//			$cuotas = $oLC->armaImputacion($liquidacion_id,$socio_id);
//			debug($cuotas);

			//DE LA LIQUIDACION SOCIO RENDICION SACO LOS DISTINTOS PROVEEDORES POR
			//SI ME CARGARON UN ARCHIVO PARA IMPUTAR A UN PROVEEDOR ESPECIFICO
// 			$proveedores = $oLSR->find("all",array('conditions' => array("LiquidacionSocioRendicion.liquidacion_id" => $liquidacion_id, "LiquidacionSocioRendicion.socio_id" => $socio_id), "fields" => array("LiquidacionSocioRendicion.proveedor_id"), "group" => array("LiquidacionSocioRendicion.proveedor_id")));
			
			$condProv = array();
			$condProv['LiquidacionSocioRendicion.liquidacion_id'] = $liquidacion_id;
			$condProv['LiquidacionSocioRendicion.socio_id'] = $socio_id;
			$condProv['LiquidacionSocioRendicion.indica_pago'] = 1;
			$fieldsProv = array("LiquidacionSocioRendicion.proveedor_id, sum(LiquidacionSocioRendicion.importe_debitado) as importe_debitado");
			$groupProv = array("LiquidacionSocioRendicion.proveedor_id");
			$ordProv = array("LiquidacionSocioRendicion.proveedor_id ASC");
			
			$proveedores = $oLSR->find("all",array('conditions' => $condProv, "fields" => $fieldsProv, "group" => $groupProv, "order" => $ordProv));

			if(!empty($proveedores)):
				//imputo en base a si un archivo fue vinculado a un proveedor especifico
				foreach($proveedores as $proveedor):
					$cuotas = array();
					if(substr($organismo,8,2) != 77) $cuotas = $oLC->armaImputacion($liquidacion_id,$socio_id,$proveedor['LiquidacionSocioRendicion']['proveedor_id']);
					else $cuotas = $oLC->armaImputacion($liquidacion_id,$socio_id);
					if(!empty($cuotas)):
						if(!$oLC->saveAll($cuotas)){
							$ERROR = TRUE;
							$status[0] = 1;
							$status[1] = "ERROR AL GUARDAR LAS CUOTAS DE LA LIQUIDACION CON LA PRE-IMPUTACION";
							break;
						}
					endif;					
				endforeach;
			else:
				//esquema de imputacion normal
				$cuotas = array();
				if(substr($organismo,8,2) != 77) $cuotas = $oLC->armaImputacion($liquidacion_id,$socio_id);
				else $cuotas = $oLC->armaImputacionCJP($liquidacion_id,$socio_id);
				if(!empty($cuotas)):
					if(!$oLC->saveAll($cuotas)){
						$ERROR = TRUE;
						$status[0] = 1;
						$status[1] = "ERROR AL GUARDAR LAS CUOTAS DE LA LIQUIDACION CON LA PRE-IMPUTACION";
						break;
					}
				endif;				
			endif;
			
                        
            $this->query("CALL SP_LIQUIDA_DEUDA_SOCIOS_SCORING($liquidacion_id,$socio_id)");           
			
// 			if(substr($organismo,8,2) != 77) $cuotas = $oLC->armaImputacion($liquidacion_id,$socio_id);
// 			else $cuotas = $oLC->armaImputacionCJP($liquidacion_id,$socio_id);
			
			

		
//			if(!$this->procesarArchivoIntercambio($socio_id,$liquidacion_id,($soloProcesaIntercambio ? false : true))){
//				$status[0] = 1;
//				$status[1] = "ERROR AL PROCESAR EL REGISTRO DE INTERCAMBIO";					
//				$ERROR = TRUE;
//				break;
//			}

		endforeach;
//        debug(func_get_args());
//        debug($status);
// 		if($organismo == "MUTUCORG2206") exit;
//                exit;
		
		return $status;
		
	
	}
	
	/**
	 * Genera Resumen de Liquidacion Socio
	 * Graba el registro. Controla si es un alta para marcar la liquidacion del socio como alta (primer dto) y graba la ultima calificacion
	 * del socio.
	 * @param integer $liquidacionSocio
	 * @param string $periodo
	 * @return boolean
	 */
	function generarResumenLiquidacionSocio($liquidacionSocio,$periodo){
		
		#verificar si es una nueva alta
		$socio_id = $liquidacionSocio['LiquidacionSocio']['socio_id'];
		
		#controlar que no tenga cuotas devengadas anteriores al periodo que se pasa por parametro
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCuota = new OrdenDescuentoCuota();

		$cantidad = $oCuota->find('count',array('conditions' => array('OrdenDescuentoCuota.socio_id' => $socio_id, 'OrdenDescuentoCuota.periodo <' => $periodo)));
		if($cantidad == 0) $liquidacionSocio['LiquidacionSocio']['alta'] = 1;
		else $liquidacionSocio['LiquidacionSocio']['alta'] = 0;
		
		//marcar la ultima calificacion
		App::import('Model', 'Pfyj.Socio');
		$oSOCIO = new Socio(null);
		
		$calificacion = $oSOCIO->getUltimaCalificacion($liquidacionSocio['LiquidacionSocio']['socio_id'],$liquidacionSocio['LiquidacionSocio']['persona_beneficio_id']);
		$liquidacionSocio['LiquidacionSocio']['ultima_calificacion'] = $calificacion;				
		
		return $this->save($liquidacionSocio);
		
	}
	
//	function procesarPreImputacion($socio_id,$liquidacion_id,$actualizaSaldoCuota=true){
//		App::import('Model','Mutual.LiquidacionCuota');
//		$oLC = new LiquidacionCuota();
//		$cuotas = array();
//		$cuotas['LiquidacionCuota'] = $oLC->armaImputacion($this->liquidacionID,$socio_id);
//		return $oLC->saveAll($cuotas);
		
//		debug($cuotasPendientesImputar);
//		debug($ACUM_IMPUTADO);
//		EXIT;
//	}
	
	/**
	 * @deprecated
	 * Este metodo no se utiliza mas porque se modifico todo el esquema de procesamiento del archivo de intercambio
	 * Se usa el metodo armaImputacion() del modelo LiquidacionCuota.  Este metodo devuelve un array con las cuotas
	 * imputadas. Este metodo saca el valor pagado por la funcion getTotalCobrado() del modelo LiquidacionSocioRendicion
	 * y se lo aplica a las cuotas.
	 * 
	 * Procesar Archivo de Intercambio.
	 *  
	 * procesa el archivo de intercambio generado para una liquidacion dada
	 * 
	 * Este proceso carga la liquidacion socios y la recorre.  Por cada iteracción va buscando
	 * en la liquidacion_registros_procesados para las condiciones de igualacion definida en la tabla de diseño de
	 * registro y va actualizando la liquidacion socio.
	 * Antes de procesar, carga en un array todas las cuotas con saldo pendiente de imputar vinculada a la liquidacion
	 * y al socio (metodo cuotasPendientesDeImputar() del modelo LiquidacionCuota.
	 * Una vez que cargo los datos en la cabecera de la liquidacion del socio, con el importe debitado si indica pago el codigo
	 * de status recorre el array de cuotas y va imputando.  Este proceso se hace por todas las liquidaciones del socio que tenga
	 * una vez terminado, el array de cuotas liquidadas tiene la distribucion del importe debitado y se guarda en la liquidacion_cuotas
	 * 
	 * @param integer $socio_id
	 * @param integer $liquidacion_id
	 * @param string $organismo
	 * @return boolean 
	 */
	function procesarArchivoIntercambio($socio_id,$liquidacion_id,$actualizaSaldoCuota=true){
		
		$procesado = true;

		App::import('Model','Mutual.LiquidacionIntercambioRegistroProcesado');
		$oRegistroProcesado = new LiquidacionIntercambioRegistroProcesado();
		
		App::import('Model','Mutual.LiquidacionDisenioRegistro');
		$oDSGN = new LiquidacionDisenioRegistro();			
		
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();

		$oLC->updateAll(
							array('LiquidacionCuota.importe_debitado' => 0,'LiquidacionCuota.liquidacion_intercambio_id' => 0),
							array(
									'LiquidacionCuota.liquidacion_id' => $liquidacion_id,
									'LiquidacionCuota.socio_id' => $socio_id,
							)
		);			
		
		
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();		
		
		$liquidacion = $oLQ->read('codigo_organismo,imputada',$liquidacion_id);
		$organismo = $liquidacion['Liquidacion']['codigo_organismo'];
		
		$camposIgualables = $oDSGN->getCamposIgualables($organismo);

		$liquidacionSocios = $this->find('all',array('conditions' => array(
																'LiquidacionSocio.liquidacion_id' => $liquidacion_id,
																'LiquidacionSocio.socio_id' => $socio_id
															),
										'order' => array('LiquidacionSocio.registro')																			
		));
		
		#PASO 1) RECORRO LA LIQUIDACION SOCIOS PARA ARMAR LOS DATOS DE LOS NO COBRADOS (CASO CBU)
		foreach($liquidacionSocios as $idx => $liquidacionSocio):
			$liquidacionSocio['LiquidacionSocio']['importe_debitado'] = 0;
			$liquidacionSocio['LiquidacionSocio']['importe_imputado'] = 0;
			$datosIntercambio = $oRegistroProcesado->getDatosIntercambio($liquidacionSocio,0);
			
			if(!empty($datosIntercambio)):
				$liquidacionSocio['LiquidacionSocio']['indica_pago'] = $datosIntercambio['indica_pago'];
				$liquidacionSocio['LiquidacionSocio']['liquidacion_intercambio_id'] = $datosIntercambio['id'];
				$liquidacionSocio['LiquidacionSocio']['status'] = $datosIntercambio['status'];
				$liquidacionSocio['LiquidacionSocio']['fecha_pago'] = $datosIntercambio['fecha_pago'];
				$liquidacionSocio['LiquidacionSocio']['banco_intercambio'] = $datosIntercambio['banco_intercambio'];
				$liquidacionSocios[$idx] = $liquidacionSocio;
			endif;
						
		endforeach;
		
		
		#PASO 2) RECORRO LA LIQUIDACION SOCIOS PARA ARMAR LOS DATOS DEL DEBITO (LOS COBRADOS)
		$DEBITADO_SOCIO = 0;

		foreach($liquidacionSocios as $idx => $liquidacionSocio):
		
			#PARA LA LIQUIDACION DEL SOCIO, TRAIGO EL TOTAL COBRADO DE LA INTERCAMBIO DE ACUERDO AL CRITERIO DE IGUALACION
			#SEGUN SEA CBU, ANSES O CJP
					
			$datosIntercambio = $oRegistroProcesado->getDatosIntercambio($liquidacionSocio,1);

			if($liquidacionSocio['LiquidacionSocio']['importe_adebitar'] == 0) $liquidacionSocio['LiquidacionSocio']['importe_adebitar'] = $liquidacionSocio['LiquidacionSocio']['importe_dto'];
			
			if(!empty($datosIntercambio) && $datosIntercambio['indica_pago'] == 1):
			
				$liquidacionSocio['LiquidacionSocio']['indica_pago'] = $datosIntercambio['indica_pago'];
				$liquidacionSocio['LiquidacionSocio']['liquidacion_intercambio_id'] = $datosIntercambio['id'];
				$liquidacionSocio['LiquidacionSocio']['status'] = $datosIntercambio['status'];
				$liquidacionSocio['LiquidacionSocio']['fecha_pago'] = $datosIntercambio['fecha_pago'];
				$liquidacionSocio['LiquidacionSocio']['banco_intercambio'] = $datosIntercambio['banco_intercambio'];
				
				$totalCobradoRegistro = $oRegistroProcesado->getTotalCobrado($liquidacionSocio);
				$liquidacionSocio['LiquidacionSocio']['importe_debitado'] = $totalCobradoRegistro - $DEBITADO_SOCIO;
//				if($organismo == "MUTUCORG2201") $DEBITADO_SOCIO += $totalCobradoRegistro;
				if(substr($organismo,8,2) == "22") $DEBITADO_SOCIO += $totalCobradoRegistro;
				
				if($liquidacionSocio['LiquidacionSocio']['importe_adebitar'] != $liquidacionSocio['LiquidacionSocio']['importe_debitado']) $liquidacionSocio['LiquidacionSocio']['importe_adebitar'] = $liquidacionSocio['LiquidacionSocio']['importe_debitado'];

			endif;
			$liquidacionSocios[$idx] = $liquidacionSocio;
			
		endforeach;

		#PASO 3) RECORRO LA LIQUIDACION SOCIOS PARA IMPUTAR LAS CUOTAS Y VOY IMPUTANDO LAS CUOTAS
		$cuotasPendientesImputar = $oLC->cuotasPendientesDeImputar($liquidacion_id,$socio_id,$actualizaSaldoCuota);
				
		foreach($liquidacionSocios as $idx => $liquidacionSocio){

			###########################################################################################################
			# LA LIQUIDACION SOCIOS ESTA ACTUALIZADA CON EL RESULTADO DEL INTERCAMBIO
			# IMPUTO LAS CUOTAS ASOCIADAS A LA LIQUIDACION DEL SOCIO
			###########################################################################################################		

			$ACUM_IMPUTADO = 0;	
			#recorro las cuotas para ir aplicando el importe debitado
			$importeImputaCuota = 0;
			$liquidacionSocio['LiquidacionSocio']['importe_imputado'] = 0;
			$liquidacionSocio['LiquidacionSocio']['importe_reintegro'] = 0;			
			
			if($liquidacionSocio['LiquidacionSocio']['indica_pago'] == 1):
			
				
				$liquidacion_intercambio_id = $liquidacionSocio['LiquidacionSocio']['liquidacion_intercambio_id'];
				$saldoDebitoSocio = $liquidacionSocio['LiquidacionSocio']['importe_debitado'];
				
				
				foreach($cuotasPendientesImputar as $idx => $cuota){
					
					
					$importe_cuota = $cuota['LiquidacionCuota']['saldo_actual'];
					
			
					if($cuota['LiquidacionCuota']['saldo_actual'] > $cuota['LiquidacionCuota']['importe_debitado']):
					
						if($saldoDebitoSocio >= $importe_cuota){
							
							$importeImputaCuota = $importe_cuota;
							$saldoDebitoSocio -= $importe_cuota;
							
						}else{
							
							$importeImputaCuota = $saldoDebitoSocio;
							$saldoDebitoSocio -= $importeImputaCuota;
							
						}
						
						$cuota['LiquidacionCuota']['importe_debitado'] = $importeImputaCuota;	
						
						if($importeImputaCuota != 0)$cuota['LiquidacionCuota']['liquidacion_intercambio_id'] = $liquidacion_intercambio_id;
						else $cuota['LiquidacionCuota']['liquidacion_intercambio_id'] = 0;
						
						$ACUM_IMPUTADO += $importeImputaCuota;
						
					
					endif;
					#guardo en el array de cuotas la cuota con los datos actualizados de la imputacion
					$cuotasPendientesImputar[$idx] = $cuota;
					
				}

				
				#guardo en la cabecera el importe imputado
				if($ACUM_IMPUTADO != 0) $liquidacionSocio['LiquidacionSocio']['importe_imputado'] = $ACUM_IMPUTADO;

				#calculo si tiene reintegro
				if($liquidacionSocio['LiquidacionSocio']['importe_imputado'] < $liquidacionSocio['LiquidacionSocio']['importe_debitado']){
					$liquidacionSocio['LiquidacionSocio']['importe_reintegro'] = $liquidacionSocio['LiquidacionSocio']['importe_debitado'] - $liquidacionSocio['LiquidacionSocio']['importe_imputado'];
				}					
				
			endif; //endif indica_pago = 1
			
			
			$this->save($liquidacionSocio);	

			//actualizo el ID de la liquidacion socio en la intercambio
			$intercambio = $oRegistroProcesado->read(null,$liquidacionSocio['LiquidacionSocio']['liquidacion_intercambio_id']);
			$intercambio['LiquidacionIntercambioRegistroProcesado']['liquidacion_socio_id'] = $liquidacionSocio['LiquidacionSocio']['id'];
			$oRegistroProcesado->save($intercambio);
			
			
		} // fin foreach --> $liquidacionSocios
		
//		debug($liquidacionSocios);
		
//		EXIT;

//		debug($liquidacionSocio);
		
		###########################################################################################################
		# GUARDO LAS CUOTAS CON LA IMPUTACION
		###########################################################################################################		
		foreach($cuotasPendientesImputar as $idx => $cuota){
			$oLC->save($cuota);
		}

		return $procesado;
		
	}
	
	
	/**
	 * Get Total Registros
	 * devuelve el total de registros generados para una liquidacion
	 * @param integer $liquidacion_id
	 * @return integer
	 */
	function getTotalRegistros($liquidacion_id){
		$registros = $this->find('count',array('conditions' => array('LiquidacionSocio.liquidacion_id' => $liquidacion_id)));
		return $registros;
	}

	/**
	 * Get Total Altas
	 * devuelve la cantidad de altas para una liquidacion. Ojo, cuenta los registros si es un socio nuevo y
	 * tiene mas de un registro (cs y deuda por separado) interpreta que son dos altas
	 * @param integer $liquidacion_id
	 * @return integer
	 */
	function getTotalAltas($liquidacion_id, $codigo_organismo){
//		$registros = $this->find('count',array('conditions' => array('LiquidacionSocio.liquidacion_id' => $liquidacion_id,'LiquidacionSocio.alta' => 1)));
//		return $registros;
		
		$sql = "SELECT COUNT(DISTINCT LiquidacionSocio.socio_id) AS altas FROM liquidacion_socios AS LiquidacionSocio 
				WHERE liquidacion_id = $liquidacion_id AND
				socio_id NOT IN (SELECT socio_id FROM liquidacion_socios
				WHERE liquidacion_id = (SELECT id FROM liquidaciones 
				WHERE liquidaciones.codigo_organismo = '$codigo_organismo'
				AND liquidaciones.id < $liquidacion_id ORDER BY liquidaciones.id DESC LIMIT 1));";
		$registros = $this->query($sql);
		return (isset($registros[0][0]['altas']) ? $registros[0][0]['altas'] : 0);
	}	
	
	
	/**
	 * Get Total Importe Liquidado por Socio
	 * @param $liquidacion_id
	 * @param $socio_id
	 * @return unknown_type
	 */
	function getTotalImporteLiquidadoBySocio($liquidacion_id,$socio_id){
		$total = $this->find('all',array('conditions' => array(
																'LiquidacionSocio.liquidacion_id' => $liquidacion_id,
																'LiquidacionSocio.socio_id' => $socio_id,
																),
											'fields' => array('SUM(importe_dto) as importe_dto')					
										)
		);
		return (isset($total[0][0]['importe_dto']) ? $total[0][0]['importe_dto'] : 0);
	}	


	/**
	 * Get Resumen por Turno
	 * Arma los datos para mostrar la grilla de turnos para exportar
	 * @param integer $liquidacion_id
	 * @return array
	 */
	function getResumenByTurno($liquidacion_id){
//		$turnos = array();
//		$registros = $this->find('all',array('conditions' => array(
//																'LiquidacionSocio.liquidacion_id' => $liquidacion_id,
////																'IFNULL(LiquidacionSocio.importe_adebitar,0) >' => 0,
//																'LiquidacionSocio.diskette' => 1,
//																),
//											'fields' => array('codigo_empresa,turno_pago,count(1) as cantidad,sum(importe_dto) as importe_dto,sum(importe_adebitar) as importe_adebitar'),					
//											'group' => array('LiquidacionSocio.turno_pago')										
//										)
//		);
	
        
//        $sql = "SELECT codigo_empresa,turno_pago,count(1) as cantidad,sum(importe_dto) as importe_dto,sum(importe_adebitar) as importe_adebitar
//                ,(select sum(lc.saldo_actual) from liquidacion_cuotas lc, persona_beneficios be where
//                lc.liquidacion_id = LiquidacionSocio.liquidacion_id and lc.persona_beneficio_id = be.id and be.turno_pago = LiquidacionSocio.turno_pago) as saldo_actual
//                ,(select sum(lc.importe) from liquidacion_cuotas lc, persona_beneficios be where
//                lc.liquidacion_id = LiquidacionSocio.liquidacion_id and lc.persona_beneficio_id = be.id and be.turno_pago = LiquidacionSocio.turno_pago) as importe_original
//                FROM liquidacion_socios AS LiquidacionSocio 
//                WHERE LiquidacionSocio.liquidacion_id = $liquidacion_id AND LiquidacionSocio.diskette = 1 GROUP BY LiquidacionSocio.turno_pago ";

//        $sql = "SELECT codigo_empresa,turno_pago,count(1) as cantidad,sum(importe_dto) as importe_dto,sum(importe_adebitar) as importe_adebitar
//                FROM liquidacion_socios AS LiquidacionSocio 
//                WHERE LiquidacionSocio.liquidacion_id = $liquidacion_id AND LiquidacionSocio.diskette = 1 GROUP BY LiquidacionSocio.turno_pago ";
        
//        $sql = "SELECT LiquidacionSocio.codigo_empresa,
//                IF(LiquidacionTurno.codigo_empresa IS NOT NULL,LiquidacionSocio.turno_pago,LiquidacionSocio.turno_pago) as turno,
//                CONCAT(TRIM(IFNULL(Empresa.concepto_1,'*** TURNO/s NO ASOCIADO A LA LIQUIDACION ***')),
//                IF(LiquidacionTurno.descripcion IS NOT NULL,CONCAT(' - ', LiquidacionTurno.descripcion), '')) AS turno_descripcion,
//                count(1) as cantidad,sum(importe_dto) 
//                as importe_dto,sum(importe_adebitar) as importe_adebitar,
//                IF(LiquidacionTurno.codigo_empresa IS NOT NULL,0,1) as error_turno
//                FROM liquidacion_socios AS LiquidacionSocio 
//                LEFT JOIN liquidacion_turnos LiquidacionTurno ON (LiquidacionTurno.codigo_empresa = 
//                LiquidacionSocio.codigo_empresa AND LiquidacionTurno.turno = 
//                LiquidacionSocio.turno_pago)
//                LEFT JOIN global_datos as Empresa on (Empresa.id = LiquidacionTurno.codigo_empresa)
//                WHERE LiquidacionSocio.liquidacion_id = $liquidacion_id AND LiquidacionSocio.diskette = 1 
//                GROUP BY LiquidacionSocio.turno_pago
//                ORDER BY Empresa.concepto_1,LiquidacionTurno.descripcion;";
        
        $sql = "select 
                LiquidacionSocio.codigo_empresa,
                sum(LiquidacionSocio.diskette) as diskette,
                IF(LiquidacionTurno.codigo_empresa IS NOT NULL,LiquidacionSocio.turno_pago,LiquidacionSocio.turno_pago) as turno,
                CONCAT(TRIM(IFNULL(LiquidacionTurno.empresa,'*** TURNO/s NO ASOCIADO A LA LIQUIDACION ***')),
                IF(LiquidacionTurno.descripcion IS NOT NULL,CONCAT(' - ', LiquidacionTurno.descripcion), '')) AS turno_descripcion,
                count(1) as cantidad,sum(importe_dto) 
				as importe_dto,sum(importe_adebitar) as importe_adebitar,
				ifnull((select sum(importe_adebitar) from liquidacion_socios ls 
				where ls.liquidacion_id = LiquidacionSocio.liquidacion_id
				and diskette = 1 and ls.codigo_empresa = LiquidacionSocio.codigo_empresa
				and ls.turno_pago = LiquidacionSocio.turno_pago),0) as importe_seleccionado,				
                IF(LiquidacionTurno.codigo_empresa IS NOT NULL,0,1) as error_turno
                from liquidacion_socios LiquidacionSocio
                left join (select lt.codigo_empresa,lt.turno,lt.descripcion,Empresa.concepto_1 as empresa 
                from liquidacion_turnos lt
                LEFT JOIN global_datos as Empresa on (Empresa.id = lt.codigo_empresa)
                group by lt.codigo_empresa,lt.turno) as LiquidacionTurno on (LiquidacionTurno.codigo_empresa = 
                LiquidacionSocio.codigo_empresa AND LiquidacionTurno.turno = 
                LiquidacionSocio.turno_pago)
                WHERE LiquidacionSocio.liquidacion_id = $liquidacion_id -- AND LiquidacionSocio.diskette = 1 
                GROUP BY LiquidacionTurno.empresa,LiquidacionSocio.turno_pago
                ORDER BY LiquidacionTurno.empresa,LiquidacionTurno.descripcion;";
        // debug($sql);
        $registros = $this->query($sql);
        return $registros;
        
//		debug($registros);
//		exit;
		
//		App::import('Model', 'Mutual.LiquidacionTurno');
//		$oTURNO = new LiquidacionTurno();
//				
//		foreach($registros as $idx => $turno){
//			
//			$turno['LiquidacionSocio']['error_turno'] = 0;
//			
//			$turno['LiquidacionSocio']['descripcion'] = $oTURNO->getDescripcionByTruno($turno['LiquidacionSocio']['turno_pago']);
//			
//			$turno['LiquidacionSocio']['empresa'] = parent::GlobalDato('concepto_1',$turno['LiquidacionSocio']['codigo_empresa']);
//			
//			if( $turno['LiquidacionSocio']['codigo_empresa'] == 'MUTUEMPR'){
//                            $turno['LiquidacionSocio']['empresa'] = "*** SIN DATOS ***";
//                        }
//
//			if(empty($turno['LiquidacionSocio']['turno_pago']) || $turno['LiquidacionSocio']['turno_pago'] == "SDATO"){
//				$turno['LiquidacionSocio']['empresa'] = "*** SIN DATOS ***";
//				$turno['LiquidacionSocio']['turno_pago'] = "SDATO";
//				$turno['LiquidacionSocio']['error_turno'] = 1;
//			}
//			
//			
//			$turno['LiquidacionSocio']['cantidad'] = $turno[0]['cantidad'];
//			$turno['LiquidacionSocio']['importe_dto'] = $turno[0]['importe_dto'];
//			$turno['LiquidacionSocio']['importe_adebitar'] = $turno[0]['importe_adebitar'];
//            $turno['LiquidacionSocio']['saldo_actual'] = $turno[0]['importe_adebitar'];
////            $turno['LiquidacionSocio']['importe_original'] = $turno[0]['importe_original'];
////			$turno['LiquidacionSocio']['fecha_debito'] = $turno[0]['fecha_debito'];
//			
//			$turnos[$idx] = $turno['LiquidacionSocio'];
//			
////			array_push($turnos,$turno['LiquidacionSocio']);
//		}
//		
//		#ordeno el array en base a la descripcion (empresa - turno)
//		$turnos = Set::sort($turnos, '{n}.descripcion', 'asc');
//
//		return $turnos;

	}
	
	/**
	 * Get Datos para Diskette CBU
	 * carga los datos para sacar el diskette CBU
	 * @param integer $liquidacionId
	 * @param integer $turnos
	 * @param integer $errorCBU
	 * @param boolean $intercambio
	 * @return array
	 */
	function getDatosParaDisketteCBU($liquidacionId,$turnos,$errorCBU=0,$intercambio=true,$soloParaDiskette=false,$order='SOCIO'){
		$conditions = array();
		$conditions['LiquidacionSocio.liquidacion_id'] = $liquidacionId;
		$conditions['LiquidacionSocio.turno_pago'] = $turnos;
// 		$conditions['LiquidacionSocio.error_cbu'] = $errorCBU;
		$conditions['LiquidacionSocio.importe_adebitar >'] = 0;
		if($intercambio) $conditions['NOT'] = array('IFNULL(LiquidacionSocio.intercambio,"0")' => 0);
		if($soloParaDiskette)$conditions['LiquidacionSocio.diskette'] = 1;
		
		$ordenamiento = array('GlobalDato.concepto_1,LiquidacionSocio.apenom,LiquidacionSocio.codigo_dto,LiquidacionSocio.sub_codigo,LiquidacionSocio.registro');
		
		if($order == 'IMPORTE') $ordenamiento = array('LiquidacionSocio.importe_adebitar ASC');
		
		$socios = $this->find('all',array(
										'joins' => array(
												array(
													'table' => 'global_datos',
													'alias' => 'GlobalDato',
													'type' => 'left',
													'foreignKey' => false,
													'conditions' => array('LiquidacionSocio.codigo_empresa = GlobalDato.id')
													),		
										),	
										'conditions' => $conditions,
										'order' => $ordenamiento								
		));	
		
		App::import('Model', 'Mutual.LiquidacionTurno');
		$oTURNO = new LiquidacionTurno();

		App::import('Model', 'pfyj.SocioReintegro');
		$oREINTEGRO = new SocioReintegro();
		
		
		foreach($socios as $idx => $socio){
			
			$socio['LiquidacionSocio']['descripcion'] = $oTURNO->getDescripcionByTruno($socio['LiquidacionSocio']['turno_pago']);
			$socio['LiquidacionSocio']['empresa'] = parent::GlobalDato('concepto_1',$socio['LiquidacionSocio']['codigo_empresa']);
			
			if( $socio['LiquidacionSocio']['codigo_empresa'] == 'MUTUEMPR') $socio['LiquidacionSocio']['empresa'] = "**S/D**";
			if(empty($socio['LiquidacionSocio']['turno_pago']) || $socio['LiquidacionSocio']['turno_pago'] == "SDATO"){
				$socio['LiquidacionSocio']['empresa'] = "*** SIN DATOS ***";
				$socio['LiquidacionSocio']['turno_pago'] = "SDATO";
			}
			
			//SACAR LOS REINTEGROS QUE TIENE EN LIQUIDACION ANTERIOR
			$socio['LiquidacionSocio']['importe_reintegro_liquidacion_anterior'] = $oREINTEGRO->getTotalReintegroLiquidacionAnterior($socio['LiquidacionSocio']['socio_id'],$socio['LiquidacionSocio']['liquidacion_id']);

			$socios[$idx] = $socio;
		}
		
		return $socios;	
	}
	
        
    function getDetalleDeTurnoDiskette($liquidacionId,$turno,$orderBy="LiquidacionSocio.apenom"){
        
        $sql = "select 
                LiquidacionSocio.id,
                LiquidacionSocio.documento,
                LiquidacionSocio.apenom, 
                LiquidacionSocio.socio_id,
                LiquidacionSocio.ultima_calificacion,
                Calificacion.concepto_1,
                LiquidacionSocio.registro,
                LiquidacionSocio.cbu,
                LiquidacionSocio.sucursal,
                LiquidacionSocio.nro_cta_bco,
                LiquidacionSocio.importe_adebitar,
                LiquidacionSocio.diskette
                from liquidacion_socios LiquidacionSocio
                left join global_datos Calificacion on Calificacion.id = LiquidacionSocio.ultima_calificacion
                where LiquidacionSocio.liquidacion_id = $liquidacionId
                and LiquidacionSocio.turno_pago = '$turno'
                order by LiquidacionSocio.apenom";
        $socios = $this->query($sql);
        return $socios;
    }    
    
    function getDatosParaDisketteCBUReporteByTurno($liquidacionId,$turno,$soloParaDiskette=false){
        $socios = array();
        $sql = "SELECT LiquidacionSocio.documento,
                UPPER(LiquidacionSocio.apenom) as apenom
                ,Persona.telefono_fijo,Persona.telefono_movil,Persona.telefono_referencia
                ,LiquidacionSocio.banco_id,Banco.nombre,LiquidacionSocio.sucursal,
                LiquidacionSocio.nro_cta_bco,LiquidacionSocio.cbu,SUM(LiquidacionSocio.importe_adebitar) 
                AS importe_adebitar,COUNT(*) as registro
                ,(SELECT SUM(lc.saldo_actual) FROM liquidacion_cuotas lc WHERE
                lc.liquidacion_id = LiquidacionSocio.liquidacion_id AND lc.socio_id = LiquidacionSocio.socio_id) AS saldo_actual
                ,(SELECT SUM(lc.importe) FROM liquidacion_cuotas lc WHERE
                lc.liquidacion_id = LiquidacionSocio.liquidacion_id AND lc.socio_id = LiquidacionSocio.socio_id) AS importe_original
                FROM liquidacion_socios AS LiquidacionSocio 
                INNER JOIN personas Persona on Persona.id = LiquidacionSocio.persona_id
                LEFT JOIN global_datos AS GlobalDato ON (LiquidacionSocio.codigo_empresa = GlobalDato.id)
                LEFT JOIN bancos as Banco ON (LiquidacionSocio.banco_id = Banco.id) 
                WHERE LiquidacionSocio.liquidacion_id = $liquidacionId AND LiquidacionSocio.turno_pago = '$turno'
                AND LiquidacionSocio.importe_adebitar > 0
                ".($soloParaDiskette ? " AND LiquidacionSocio.diskette = 1" : "")."
                GROUP BY LiquidacionSocio.documento,
                LiquidacionSocio.apenom, LiquidacionSocio.persona_beneficio_id
                ORDER BY GlobalDato.concepto_1 ASC, LiquidacionSocio.apenom ASC, 
                LiquidacionSocio.codigo_dto ASC, 
                LiquidacionSocio.sub_codigo ASC,LiquidacionSocio.registro ASC";
        $datos = $this->query($sql);
        if(!empty($datos)){
            foreach($datos as $ix => $dato){
                $socios[$ix]['LiquidacionSocio']['documento'] = $dato['LiquidacionSocio']['documento'];
                $socios[$ix]['LiquidacionSocio']['apenom'] = $dato[0]['apenom'];
                $socios[$ix]['LiquidacionSocio']['registro'] = $dato[0]['registro'];
                $socios[$ix]['LiquidacionSocio']['banco'] = $dato['Banco']['nombre'];
                $socios[$ix]['LiquidacionSocio']['banco_id'] = $dato['LiquidacionSocio']['banco_id'];
                $socios[$ix]['LiquidacionSocio']['sucursal'] = $dato['LiquidacionSocio']['sucursal'];
                $socios[$ix]['LiquidacionSocio']['nro_cta_bco'] = $dato['LiquidacionSocio']['nro_cta_bco'];
                $socios[$ix]['LiquidacionSocio']['cbu'] = $dato['LiquidacionSocio']['cbu'];
                $socios[$ix]['LiquidacionSocio']['importe_original'] = $dato[0]['importe_original'];
                $socios[$ix]['LiquidacionSocio']['saldo_actual'] = $dato[0]['saldo_actual'];
                $socios[$ix]['LiquidacionSocio']['importe_adebitar'] = $dato[0]['importe_adebitar'];
                
                $socios[$ix]['LiquidacionSocio']['persona_telefono_fijo'] = $dato['Persona']['telefono_fijo'];
                $socios[$ix]['LiquidacionSocio']['persona_telefono_movil'] = $dato['Persona']['telefono_movil'];
                $socios[$ix]['LiquidacionSocio']['persona_telefono_referencia'] = $dato['Persona']['telefono_referencia'];
                
            }
        }
        return $socios;
    }
    
    
	/**
	 * Get Resumen por Turno Diskette CBU
	 * @param unknown_type $liquidacionId
	 * @param unknown_type $turnos
	 * @return array
	 */
	function getResumenByTurnoDisketteCBU($liquidacionId,$turnos){
//		$conditions['LiquidacionSocio.liquidacion_id'] = $liquidacionId;
//		$conditions['LiquidacionSocio.turno_pago'] = $turnos;
//		$conditions['LiquidacionSocio.error_cbu'] = 0;
//		$conditions['LiquidacionSocio.importe_adebitar >'] = 0;
//		$conditions['NOT'] = array('IFNULL(LiquidacionSocio.intercambio,"0")' => 0);
//		$conditions['LiquidacionSocio.diskette'] = 1;
//		$socios = $this->find('all',array(
//										'joins' => array(
//												array(
//													'table' => 'global_datos',
//													'alias' => 'GlobalDato',
//													'type' => 'inner',
//													'foreignKey' => false,
//													'conditions' => array('LiquidacionSocio.codigo_empresa = GlobalDato.id')
//													),		
//										),	
//										'conditions' => $conditions,
//										'fields' => array('LiquidacionSocio.codigo_empresa,count(*) as cantidad,sum(importe_adebitar) as importe_adebitar'),
//										'group' => array('LiquidacionSocio.codigo_empresa'),	
//										'order' => array('GlobalDato.concepto_1')								
//		));	
		
		$conditions['LiquidacionSocio.liquidacion_id'] = $liquidacionId;
		$conditions['LiquidacionSocio.turno_pago'] = $turnos;
		$conditions['LiquidacionSocio.error_cbu'] = 0;
		$conditions['LiquidacionSocio.importe_adebitar >'] = 0;
		$conditions['NOT'] = array('IFNULL(LiquidacionSocio.intercambio,"0")' => 0);
		$conditions['LiquidacionSocio.diskette'] = 1;

		$socios = $this->find('all',array('conditions' =>$conditions,
											'fields' => array('codigo_empresa,turno_pago,count(1) as cantidad,sum(importe_dto) as importe_dto,sum(importe_adebitar) as importe_adebitar'),
											'group' => array('LiquidacionSocio.turno_pago')										
										)
		);		
		
		
		App::import('Model', 'Mutual.LiquidacionTurno');
		$oTURNO = new LiquidacionTurno();
				
		foreach($socios as $idx => $socio){
			
			$socio['LiquidacionSocio']['descripcion'] = $oTURNO->getDescripcionByTruno($socio['LiquidacionSocio']['turno_pago']);
			
			$socio['LiquidacionSocio']['empresa'] = parent::GlobalDato('concepto_1',$socio['LiquidacionSocio']['codigo_empresa']);
			
			if( $socio['LiquidacionSocio']['codigo_empresa'] == 'MUTUEMPR') $socio['LiquidacionSocio']['empresa'] = "**S/D**";
			if(empty($socio['LiquidacionSocio']['turno_pago']) || $socio['LiquidacionSocio']['turno_pago'] == "SDATO"){
				$socio['LiquidacionSocio']['empresa'] = "*** SIN DATOS ***";
				$socio['LiquidacionSocio']['turno_pago'] = "SDATO";
			}
			
			$socio['LiquidacionSocio']['cantidad'] = $socio[0]['cantidad'];
			$socio['LiquidacionSocio']['importe_dto'] = $socio[0]['importe_dto'];
			$socio['LiquidacionSocio']['importe_adebitar'] = $socio[0]['importe_adebitar'];
			
			$socios[$idx] = $socio;
			
//			array_push($turnos,$turno['LiquidacionSocio']);
		}
		
		return $socios;			
	}
	
	/**
	 * Get Datos para Diskette CJP
	 * carga los datos para sacar el diskette CJP
	 * @param integer $liquidacionId
	 * @return array
	 */
	function getDatosParaDisketteCJP($liquidacionId,$order=array('LiquidacionSocio.apenom,LiquidacionSocio.registro'),$codDto=0,$altaBaja = 'A'){
		$conditions = array();
		$conditions['LiquidacionSocio.liquidacion_id'] = $liquidacionId;
		$conditions['LiquidacionSocio.importe_adebitar >'] = 0;
		$conditions['LiquidacionSocio.sub_codigo'] = $codDto;
		if($codDto == 0 and $altaBaja == 'A') return $this->datosAltaSociosCJP($liquidacionId);
		if($codDto == 0 and $altaBaja == 'B') return $this->datosBajaSociosCJP($liquidacionId);
		
		if($codDto == 1 and $altaBaja == 'A') return $this->altaConsumosCJP($liquidacionId);
		if($codDto == 1 and $altaBaja == 'B') return $this->bajaConsumosCJP($liquidacionId);
		
		$socios = $this->find('all',array('conditions' => $conditions,
										'order' => $order,
		));	
		return $socios;	
	}	
	
	/**
	 * Altas y modificaciones de importes de cuotas social
	 * @param unknown_type $liquidacionId
	 * @return unknown
	 */
	function datosAltaSociosCJP($liquidacionId){
//		$sql = "SELECT * FROM liquidacion_socios as LiquidacionSocio WHERE liquidacion_id = $liquidacionId
//				AND sub_codigo = '0' AND importe_adebitar > 0 and
//				socio_id NOT IN (SELECT socio_id FROM liquidacion_socios
//				WHERE liquidacion_id = (SELECT id FROM liquidaciones 
//										WHERE liquidaciones.codigo_organismo = 'MUTUCORG7701'
//										AND liquidaciones.id <> LiquidacionSocio.liquidacion_id
//										ORDER BY liquidaciones.id DESC LIMIT 1)
//				AND sub_codigo = '0')
//				UNION
//				SELECT * FROM liquidacion_socios AS LiquidacionSocio WHERE liquidacion_id = $liquidacionId
//				AND sub_codigo = '0' AND importe_adebitar <>
//					(SELECT importe_adebitar FROM liquidacion_socios ls
//					WHERE ls.liquidacion_id = (SELECT id FROM liquidaciones 
//							WHERE liquidaciones.codigo_organismo = 'MUTUCORG7701'
//							AND liquidaciones.id < LiquidacionSocio.liquidacion_id
//							ORDER BY liquidaciones.id DESC LIMIT 1)
//					AND ls.sub_codigo = '0' AND LiquidacionSocio.socio_id = ls.socio_id)				
//				order by apenom, registro";
		
//		$sql = "SELECT * FROM liquidacion_socios as LiquidacionSocio WHERE liquidacion_id = $liquidacionId
//				AND sub_codigo = '0' AND importe_adebitar > 0 and
//				socio_id NOT IN (SELECT socio_id FROM liquidacion_socios
//				WHERE liquidacion_id = (SELECT id FROM liquidaciones 
//										WHERE liquidaciones.codigo_organismo = 'MUTUCORG7701'
//										AND liquidaciones.id <> LiquidacionSocio.liquidacion_id
//										ORDER BY liquidaciones.id DESC LIMIT 1)
//				AND sub_codigo = '0')				
//				order by apenom, registro";	

		$codigoBeneficio = "MUTUCORG7701";
		$subCodigoDto = "0";
		
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();

		$organismo = $oLiq->getCodigoOrganismo($liquidacionId);
		
		$liquidacion_anterior = $oLiq->getUltimaLiquidacionImputada($organismo);
		
                if(!empty($liquidacion_anterior)){
                    $sql = "SELECT LiquidacionSocio.*,'A' AS tipo_novedad FROM liquidacion_socios AS LiquidacionSocio
                                    WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto'
                                    AND orden_descuento_id NOT IN (SELECT orden_descuento_id FROM
                                    liquidacion_socio_rendiciones WHERE liquidacion_id = $liquidacion_anterior)
                                    ORDER BY LiquidacionSocio.apenom;";		
                }else{
                    $sql = "SELECT LiquidacionSocio.*,'A' AS tipo_novedad FROM liquidacion_socios AS LiquidacionSocio
                                    WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto'
                                    ORDER BY LiquidacionSocio.apenom;";		
                    
                }
		
		
		$socios = $this->query($sql);
//		if(!empty($socios)):
//			foreach($socios as $idx => $socio):
//				if(isset($socio[0]))$socios[$idx]['LiquidacionSocio'] = $socio[0];
//			endforeach;
//		
//		endif;
		return $socios;
	}

	
	function datosBajaSociosCJP($liquidacionId){
		$sql = "SELECT * FROM liquidacion_socios AS LiquidacionSocio 
				WHERE liquidacion_id = (SELECT id FROM liquidaciones 
				WHERE liquidaciones.codigo_organismo = 'MUTUCORG7701'
				AND liquidaciones.id < $liquidacionId
				ORDER BY liquidaciones.id DESC LIMIT 1)
				AND sub_codigo = '0' AND
				socio_id NOT IN (SELECT socio_id FROM liquidacion_socios
				WHERE liquidacion_id = $liquidacionId AND sub_codigo = '0')
				order by apenom, registro";
		$socios = $this->query($sql);
		
		if(empty($socios)) return $socios;
		
		App::import('Model', 'Config.Banco');
		$oBanco = new Banco(null);			
		
		foreach($socios as $idx => $socio):
		
			if(empty($socio['LiquidacionSocio']['intercambio'])):
			
				$campos = array(
								1 => $socio['LiquidacionSocio']['tipo'],
								2 => $socio['LiquidacionSocio']['nro_ley'],
								3 => $socio['LiquidacionSocio']['nro_beneficio'],
								4 => $socio['LiquidacionSocio']['sub_beneficio'],
								5 => $socio['LiquidacionSocio']['codigo_dto'],
								6 => $socio['LiquidacionSocio']['sub_codigo'],
								7 => $socio['LiquidacionSocio']['importe_adebitar'],
								8 => 'I',
								9 => (parent::is_date($socio['LiquidacionSocio']['fecha_otorgamiento']) ? $socio['LiquidacionSocio']['fecha_otorgamiento'] : null),
								10 => $socio['LiquidacionSocio']['importe_total'],
								11 => $socio['LiquidacionSocio']['cuotas'],
								12 => $socio['LiquidacionSocio']['importe_cuota'],
								13 => $socio['LiquidacionSocio']['importe_deuda'],
								14 => $socio['LiquidacionSocio']['importe_deuda_vencida'],
								15 => $socio['LiquidacionSocio']['importe_deuda_no_vencida'],
								16 => $socio['LiquidacionSocio']['orden_descuento_id'],
				
				);			
				$socio['LiquidacionSocio']['intercambio'] = $oBanco->armaStringDebitoCJP($campos);
				$socios[$idx] = $socio;			
			
			endif;
		
		endforeach;		
		
		return $socios;
	}	
	
	/**
	 * Alta consumos CJP
	 * 
	 * Carga todos los consumos nuevos detectados en la liquidacion y tambi�n 
	 * procesa todos aquellos consumos permanentes que venian liquidandose y
	 * sufren una variaci�n en el importe mensual (en mas o en menos).
	 * Al array de salida (LiquidacionSocio) se le agrega un campo mas "tipo_novedad"
	 * para indicar si es una ALTA o una MODIFICION.
	 * 
	 * @author adrian [31/01/2012]
	 * @param int $liquidacionId
	 * @param string $sort
	 * @return array
	 */
	function altaConsumosCJP($liquidacionId,$sort="ORDER BY apenom ASC"){
		
		$codigoBeneficio = "MUTUCORG7701";
		$subCodigoDto = "1";
		
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();

		$organismo = $oLiq->getCodigoOrganismo($liquidacionId);
		
		$liquidacion_anterior = $oLiq->getUltimaLiquidacionImputada($organismo);
		
//		$sql = "SELECT * FROM liquidacion_socios AS LiquidacionSocio 
//				WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto' AND
//				orden_descuento_id NOT IN (SELECT orden_descuento_id FROM liquidacion_socios
//				WHERE liquidacion_id = (SELECT id FROM liquidaciones 
//				WHERE liquidaciones.codigo_organismo = '$codigoBeneficio'
//				AND liquidaciones.id < $liquidacionId
//				ORDER BY liquidaciones.id DESC LIMIT 1))
//				and importe_total <> 0
//				and importe_total = importe_deuda_no_vencida $sort";
				
//		$sql = "SELECT LiquidacionSocio.*,'A' as tipo_novedad FROM liquidacion_socios AS LiquidacionSocio 
//				WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto' AND
//				orden_descuento_id NOT IN (SELECT orden_descuento_id FROM liquidacion_socios
//				WHERE liquidacion_id = (SELECT id FROM liquidaciones 
//				WHERE liquidaciones.codigo_organismo = '$codigoBeneficio'
//				AND liquidaciones.id < $liquidacionId
//				ORDER BY liquidaciones.id DESC LIMIT 1))
//				and importe_total <> 0
//				and importe_total = importe_deuda_no_vencida 
//				UNION
//				SELECT LiquidacionSocio.*,'M' as tipo_novedad FROM liquidacion_socios AS LiquidacionSocio
//				WHERE liquidacion_id = $liquidacionId
//				AND sub_codigo = '$subCodigoDto' AND orden_descuento_id = (SELECT orden_descuento_id FROM liquidacion_socios ls2
//				WHERE liquidacion_id = (SELECT id FROM liquidaciones 
//				WHERE liquidaciones.codigo_organismo = '$codigoBeneficio'
//				AND liquidaciones.id < $liquidacionId
//				ORDER BY liquidaciones.id DESC LIMIT 1) 
//				AND ls2.orden_descuento_id = LiquidacionSocio.orden_descuento_id AND ls2.importe_adebitar <> LiquidacionSocio.importe_adebitar)
//				AND LiquidacionSocio.cuotas  = 0
//				UNION
//				SELECT LiquidacionSocio.*,'R' as tipo_novedad FROM liquidacion_socios AS LiquidacionSocio 
//				WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto' AND
//				orden_descuento_id NOT IN (SELECT orden_descuento_id FROM liquidacion_socios
//				WHERE liquidacion_id = (SELECT id FROM liquidaciones 
//				WHERE liquidaciones.codigo_organismo = '$codigoBeneficio'
//				AND liquidaciones.id < $liquidacionId
//				ORDER BY liquidaciones.id DESC LIMIT 1))
//				and importe_total <> 0
//				and importe_total <> importe_deuda_no_vencida				
//				$sort";				

		#################################################################################################################
		#OPERACIONES NUEVAS
		#################################################################################################################		
//		$SQL_altasNuevas 	= "	SELECT LiquidacionSocio.*,'A' as tipo_novedad FROM liquidacion_socios AS LiquidacionSocio 
//								WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto' AND
//								orden_descuento_id NOT IN (SELECT orden_descuento_id FROM liquidacion_socios
//								WHERE liquidacion_id = (SELECT id FROM liquidaciones 
//								WHERE liquidaciones.codigo_organismo = '$codigoBeneficio'
//								AND liquidaciones.id < $liquidacionId
//								ORDER BY liquidaciones.id DESC LIMIT 1))
//								and importe_total <> 0
//								and importe_total = importe_deuda_no_vencida";
		
                $liquidacion_anterior = (!empty($liquidacion_anterior) ? $liquidacion_anterior : 0);
                
		$SQL_altasNuevas = "SELECT LiquidacionSocio.*,'A' AS tipo_novedad FROM liquidacion_socios AS LiquidacionSocio
							WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto'
							AND orden_descuento_id NOT IN (SELECT orden_descuento_id FROM
							liquidacion_socio_rendiciones WHERE liquidacion_id <= $liquidacion_anterior)";
		
		#################################################################################################################
		# MODIFICACION DE IMPORTE DE SERVICIOS
		#################################################################################################################
		$SQL_modServ 		= "	SELECT LiquidacionSocio.*,'M' as tipo_novedad FROM liquidacion_socios AS LiquidacionSocio
								WHERE liquidacion_id = $liquidacionId
								AND sub_codigo = '$subCodigoDto' AND orden_descuento_id = (SELECT orden_descuento_id FROM liquidacion_socios ls2
								WHERE liquidacion_id = (SELECT id FROM liquidaciones 
								WHERE liquidaciones.codigo_organismo = '$codigoBeneficio'
								AND liquidaciones.id < $liquidacionId
								ORDER BY liquidaciones.id DESC LIMIT 1) 
								AND ls2.orden_descuento_id = LiquidacionSocio.orden_descuento_id AND ls2.importe_adebitar <> LiquidacionSocio.importe_adebitar)
								AND LiquidacionSocio.cuotas  = 0";
		#################################################################################################################
		# ORDENES DE DESCUENTOS QUE SE REASIGNARON (PEJ.: VENIAN POR CBU Y PASAN A LA CJP)
		#################################################################################################################
//		$SQL_reAsign 		= "	SELECT LiquidacionSocio.*,'R' as tipo_novedad FROM liquidacion_socios AS LiquidacionSocio 
//								WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto' AND
//								orden_descuento_id NOT IN (SELECT orden_descuento_id FROM liquidacion_socios
//								WHERE liquidacion_id = (SELECT id FROM liquidaciones 
//								WHERE liquidaciones.codigo_organismo = '$codigoBeneficio'
//								AND liquidaciones.id < $liquidacionId
//								ORDER BY liquidaciones.id DESC LIMIT 1))
//								and importe_total <> 0
//								and importe_total <> importe_deuda_no_vencida";
		

//		$sql = "$SQL_altasNuevas UNION $SQL_modServ UNION $SQL_reAsign $sort;";
		$sql = "$SQL_altasNuevas $sort;";
		
//		debug($liquidacion_anterior);
//		exit;
		
		$socios = $this->query($sql);
		
		
		if(empty($socios)) return $socios;
		
		return $socios;
		
//		$altas = array();
//		
//		foreach($socios as $idx => $socio):
//			$altas[$idx]['LiquidacionSocio'] = $socio[0];
//		endforeach;
//
//		return $altas;		
	}
	
	/**
	 * Baja Consumos CJP
	 * 
	 * Carga todos los consumos liquidados con anterioridad a la liquidacion pasada por parametro y
	 * que no existen en la misma.
	 * 
	 * @author adrian [31/01/2012]
	 * @param int $liquidacionId
	 */
	function bajaConsumosCJP($liquidacionId){
		
		$codigoBeneficio = "MUTUCORG7701";
		$subCodigoDto = "1";
		
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();

		$organismo = $oLiq->getCodigoOrganismo($liquidacionId);
		
		$liquidacion_anterior = $oLiq->getUltimaLiquidacionImputada($organismo);
		
		
//		$sql = "SELECT * FROM liquidacion_socios AS LiquidacionSocio 
//				WHERE liquidacion_id = (SELECT id FROM liquidaciones 
//				WHERE liquidaciones.codigo_organismo = '$codigoBeneficio'
//				AND liquidaciones.id < $liquidacionId
//				ORDER BY liquidaciones.id DESC LIMIT 1)
//				AND sub_codigo = '$subCodigoDto' AND
//				orden_descuento_id NOT IN (SELECT orden_descuento_id FROM liquidacion_socios
//				WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto')
//				ORDER BY apenom, registro";
		
//		debug($liquidacion_anterior);
//		DEBUG($sql);
//		exit;
		
//		$sql = "SELECT DISTINCT LiquidacionSocio.*,LiquidacionSocioRendicion.orden_descuento_id  FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion
//				INNER JOIN liquidacion_socios AS LiquidacionSocio  ON (LiquidacionSocio.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id 
//						AND LiquidacionSocio.socio_id = LiquidacionSocioRendicion.socio_id)
//				WHERE LiquidacionSocioRendicion.liquidacion_id = (SELECT id FROM liquidaciones 
//								WHERE liquidaciones.codigo_organismo = '$codigoBeneficio'
//								AND liquidaciones.id < $liquidacionId
//								ORDER BY liquidaciones.id DESC LIMIT 1) 
//				AND LiquidacionSocioRendicion.sub_codigo = '1'
//				AND LiquidacionSocioRendicion.orden_descuento_id NOT IN (SELECT orden_descuento_id FROM liquidacion_cuotas
//				WHERE liquidacion_id = $liquidacionId)
//				ORDER BY LiquidacionSocio.apenom ASC;";
		
		
		$sql = "SELECT 
					LiquidacionSocio.documento,LiquidacionSocio.apenom,LiquidacionSocio.tipo,LiquidacionSocio.nro_ley,LiquidacionSocio.nro_beneficio,
					LiquidacionSocio.sub_beneficio,LiquidacionSocio.codigo_dto,LiquidacionSocio.sub_codigo,LiquidacionSocioRendicion.orden_descuento_id
				FROM 
					liquidacion_socio_rendiciones AS LiquidacionSocioRendicion
				INNER JOIN 
					liquidacion_socios AS LiquidacionSocio  
						ON (
								LiquidacionSocio.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id 
								AND LiquidacionSocio.socio_id = LiquidacionSocioRendicion.socio_id 
								AND LiquidacionSocio.codigo_dto = LiquidacionSocioRendicion.codigo_dto
							)
				WHERE 
					LiquidacionSocioRendicion.liquidacion_id = (SELECT id FROM liquidaciones 
																WHERE liquidaciones.codigo_organismo = '$codigoBeneficio'
																AND liquidaciones.id < $liquidacionId
																ORDER BY liquidaciones.id DESC LIMIT 1) 
					AND LiquidacionSocioRendicion.sub_codigo = '1'
					AND LiquidacionSocioRendicion.orden_descuento_id NOT IN 
									(SELECT orden_descuento_id FROM liquidacion_cuotas
									WHERE liquidacion_id = $liquidacionId)
				GROUP BY
					LiquidacionSocioRendicion.orden_descuento_id
				ORDER BY 
					LiquidacionSocio.apenom ASC;";
		
		
		$socios = $this->query($sql);
		
//		debug($sql);
//		exit;
		
		if(empty($socios)) return $socios;
		
//		debug($socios);
//		exit;
		
		App::import('Model', 'Config.Banco');
		$oBanco = new Banco(null);			
		
		foreach($socios as $idx => $socio):
		
//			if(empty($socio['LiquidacionSocio']['intercambio'])):
			
//				$campos = array(
//								1 => $socio['LiquidacionSocio']['tipo'],
//								2 => $socio['LiquidacionSocio']['nro_ley'],
//								3 => $socio['LiquidacionSocio']['nro_beneficio'],
//								4 => $socio['LiquidacionSocio']['sub_beneficio'],
//								5 => $socio['LiquidacionSocio']['codigo_dto'],
//								6 => $socio['LiquidacionSocio']['sub_codigo'],
//								7 => $socio['LiquidacionSocio']['importe_adebitar'],
//								8 => 'I',
//								9 => (parent::is_date($socio['LiquidacionSocio']['fecha_otorgamiento']) ? $socio['LiquidacionSocio']['fecha_otorgamiento'] : null),
//								10 => $socio['LiquidacionSocio']['importe_total'],
//								11 => $socio['LiquidacionSocio']['cuotas'],
//								12 => $socio['LiquidacionSocio']['importe_cuota'],
//								13 => $socio['LiquidacionSocio']['importe_deuda'],
//								14 => $socio['LiquidacionSocio']['importe_deuda_vencida'],
//								15 => $socio['LiquidacionSocio']['importe_deuda_no_vencida'],
//								16 => $socio['LiquidacionSocioRendicion']['orden_descuento_id'],
//				
//				);

				$campos = array(
								1 => $socio['LiquidacionSocio']['tipo'],
								2 => $socio['LiquidacionSocio']['nro_ley'],
								3 => $socio['LiquidacionSocio']['nro_beneficio'],
								4 => $socio['LiquidacionSocio']['sub_beneficio'],
								5 => $socio['LiquidacionSocio']['codigo_dto'],
								6 => 1,
								7 => 0,
								8 => 'I',
								9 => null,
								10 => 0,
								11 => 0,
								12 => 0,
								13 => 0,
								14 => 0,
								15 => 0,
								16 => $socio['LiquidacionSocioRendicion']['orden_descuento_id'],
				
				);					
				$socio['LiquidacionSocio']['intercambio'] = $oBanco->armaStringDebitoCJP($campos);
				$socio['LiquidacionSocio']['orden_descuento_id'] = $socio['LiquidacionSocioRendicion']['orden_descuento_id'];
				$socio['LiquidacionSocio']['error_cbu'] = 0;
				$socio['LiquidacionSocio']['sub_codigo'] = 1;
				$socios[$idx] = $socio;			
			
//			endif;
		
		endforeach;
//		debug($socios);
//		exit;
		return $socios;		
	}
	
	
	/**
	 * Datos diskette CJP formato viejo
	 * @param $liquidacionId
	 * @param $order
	 */
	function getDatosParaDisketteCJPFormatoAnterior($liquidacionId,$order=array('LiquidacionSocio.apenom,LiquidacionSocio.registro'),$codDto=0){
		$socios = null;
		
		$conditions = array();
		$conditions['LiquidacionSocio.liquidacion_id'] = $liquidacionId;
		$conditions['LiquidacionSocio.importe_adebitar >'] = 0;
		$conditions['LiquidacionSocio.sub_codigo'] = $codDto;		
		
		$socios = $this->find('all',array('conditions' => $conditions,
										'fields' => array(
															'LiquidacionSocio.documento',
															'LiquidacionSocio.apenom',	
															'LiquidacionSocio.error_cbu',			
															'LiquidacionSocio.tipo',
															'LiquidacionSocio.nro_ley',
															'LiquidacionSocio.nro_beneficio',
															'LiquidacionSocio.sub_beneficio',
															'LiquidacionSocio.codigo_dto',
															'LiquidacionSocio.sub_codigo',
															'sum(LiquidacionSocio.importe_adebitar) as importe_adebitar'					
														),
										'group' => array(
															'LiquidacionSocio.documento',
															'LiquidacionSocio.apenom',			
															'LiquidacionSocio.tipo',
															'LiquidacionSocio.nro_ley',
															'LiquidacionSocio.nro_beneficio',
															'LiquidacionSocio.sub_beneficio',
															'LiquidacionSocio.codigo_dto',
															'LiquidacionSocio.sub_codigo',
														),																					
										'order' => $order,
		));	
		if(empty($socios)) return null;
		
		App::import('Model', 'Config.Banco');
		$oBanco = new Banco(null);		
		
		foreach($socios as $i => $socio):
			
			if(isset($socio[0]['importe_adebitar']) && $socio[0]['importe_adebitar'] != 0):
			
				$socio['LiquidacionSocio']['importe_adebitar'] = $socio[0]['importe_adebitar'];
				
				$campos = array(
								1 => $socio['LiquidacionSocio']['tipo'],
								2 => $socio['LiquidacionSocio']['nro_ley'],
								3 => $socio['LiquidacionSocio']['nro_beneficio'],
								4 => $socio['LiquidacionSocio']['sub_beneficio'],
								5 => $socio['LiquidacionSocio']['codigo_dto'],
								6 => $socio['LiquidacionSocio']['sub_codigo'],
								7 => $socio['LiquidacionSocio']['importe_adebitar'] ,
								8 => 'I'
				
				);	
				$socio['LiquidacionSocio']['intercambio'] = $oBanco->armaStringDebitoCJP($campos,false);	
				$socios[$i] = $socio;
				
			endif;
			
		endforeach;
		
//		debug($socios);
//		exit;

		return $socios;
		
	}
	
	
	function getErroresDisketteCJP($liquidacion_id){
		$conditions = array();
		$conditions['LiquidacionSocio.liquidacion_id'] = $liquidacion_id;
		$conditions['LiquidacionSocio.error_cbu'] = 1;
		
		$socios = $this->find('all',array('conditions' => $conditions,
										'order' => array('LiquidacionSocio.apenom,LiquidacionSocio.registro'),
		));
		return $socios;		
	}
	
	
	/**
	 * Generar Diskette CJP
	 * Procesa los datos y genera el string para el diskette
	 * @param integer $liquidacion_id
	 * @return array 
	 */
	function generarDisketteCJP($liquidacion_id,$socio_id = null){
		$conditions = array();
		$conditions['LiquidacionSocio.liquidacion_id'] = $liquidacion_id;
		$conditions['LiquidacionSocio.importe_adebitar >'] = 0;
		if(!empty($socio_id)) $conditions['LiquidacionSocio.socio_id'] = $socio_id;
		
		$socios = $this->find('all',array('conditions' => $conditions,
										'order' => array('LiquidacionSocio.apenom,LiquidacionSocio.registro'),
//										'limit' => 100														
		));
		
		App::import('Model', 'Config.Banco');
		$oBanco = new Banco(null);
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBEN = new PersonaBeneficio();
		
		$liquidacion = $this->read(null,$liquidacion_id);
		// si la liquidacion esta imputada no vuelvo a procesar los datos del diskette
		if($liquidacion['Liquidacion']['imputada'] == 1) return $socios;
		
		foreach($socios as $idx => $socio){
	
			$CONTROL = TRUE;
			$TIPOERROR = "OK";
			
			$socio['LiquidacionSocio']['error_intercambio'] = null;
			$socio['LiquidacionSocio']['error_cbu'] = 0;			
			
			//CONTROLAR EL TIPO
			if(!isset($socio['LiquidacionSocio']['tipo'])){
				$CONTROL = FALSE;
				$TIPOERROR = "TIPO_INCORRECTO";
				$socio['LiquidacionSocio']['error_intercambio'] = "$TIPOERROR";
				$socio['LiquidacionSocio']['error_cbu'] = 1;				
			}
			//CONTROLAR EL TIPO EN BASE AL NUMERO DE LEY
			if(!$oBEN->controlTipoLeyCJP($socio['LiquidacionSocio']['tipo'],$socio['LiquidacionSocio']['nro_ley'])){
				$CONTROL = FALSE;
				$TIPOERROR = "LEY_TIPO_ERROR";
				$socio['LiquidacionSocio']['error_intercambio'] = "$TIPOERROR";
				$socio['LiquidacionSocio']['error_cbu'] = 1;				
			}
			
			
			$campos = array(
							1 => $socio['LiquidacionSocio']['tipo'],
							2 => $socio['LiquidacionSocio']['nro_ley'],
							3 => $socio['LiquidacionSocio']['nro_beneficio'],
							4 => $socio['LiquidacionSocio']['sub_beneficio'],
							5 => $socio['LiquidacionSocio']['codigo_dto'],
							6 => $socio['LiquidacionSocio']['sub_codigo'],
							7 => $socio['LiquidacionSocio']['importe_adebitar'],
							8 => 'I',
							9 => (parent::is_date($socio['LiquidacionSocio']['fecha_otorgamiento']) ? $socio['LiquidacionSocio']['fecha_otorgamiento'] : null),
							10 => $socio['LiquidacionSocio']['importe_total'],
							11 => $socio['LiquidacionSocio']['cuotas'],
							12 => $socio['LiquidacionSocio']['importe_cuota'],
							13 => $socio['LiquidacionSocio']['importe_deuda'],
							14 => $socio['LiquidacionSocio']['importe_deuda_vencida'],
//							14 => $socio['LiquidacionSocio']['importe_deuda'] - $socio['LiquidacionSocio']['importe_deuda_no_vencida'],
							15 => $socio['LiquidacionSocio']['importe_deuda_no_vencida'],
							16 => $socio['LiquidacionSocio']['orden_descuento_id'],
			
			);	
			
			//controles exigidos por la caja
			#1) CAMPO_13 = CAMPO_14 + CAMPO_15
			
//			if($socio['LiquidacionSocio']['socio_id'] == 10966) debug($campos);
			
			if($socio['LiquidacionSocio']['sub_codigo'] == 1):
			
				if(round($campos[13],2) != round((round($campos[14],2) + round($campos[15],2)),2)){
					$socio['LiquidacionSocio']['error_cbu'] = 1;
					$socio['LiquidacionSocio']['error_intercambio'] = "DEUDA <> VENCIDO + NO VENCIDO";
					$CONTROL = FALSE;
				}
				if(intval($campos[11]) != round($campos[10] / $campos[12],0)){
					$socio['LiquidacionSocio']['error_cbu'] = 1;
					$socio['LiquidacionSocio']['error_intercambio'] = "IMPORTE TOTAL / IMPORTE_CUOTA <> CUOTAS";
					$CONTROL = FALSE;
				}
				if($campos[13] > $campos[10]){
					$socio['LiquidacionSocio']['error_cbu'] = 1;
					$socio['LiquidacionSocio']['error_intercambio'] = "SALDO > IMPORTE TOTAL";
					$CONTROL = FALSE;
				}
				if($campos[16] == 0){
					$socio['LiquidacionSocio']['error_cbu'] = 1;
					$socio['LiquidacionSocio']['error_intercambio'] = "FALTA NRO.OPERACION";
					$CONTROL = FALSE;
				}
				
				if(empty($campos[9])){
					$socio['LiquidacionSocio']['error_cbu'] = 1;
					$socio['LiquidacionSocio']['error_intercambio'] = "FALTA FECHA OTORGAMIENTO";
					$CONTROL = FALSE;
				}

				if(date('Ymd',strtotime($campos[9])) > date('Ymd')){
					$socio['LiquidacionSocio']['error_cbu'] = 1;
					$socio['LiquidacionSocio']['error_intercambio'] = "FECHA OTORGAMIENTO SUPERIOR A HOY";
					$CONTROL = FALSE;
				}				
				
				
//				if((int)($campos[15] / $campos[12]) != ($campos[15] / $campos[12])){
//					$socio['LiquidacionSocio']['error_cbu'] = 1;
//					$socio['LiquidacionSocio']['ERROR_INTERCAMBIO'] = "ERROR EN SALDO_AVENCER";
//					$CONTROL = FALSE;
//				}				

			endif;

			if($CONTROL) $socio['LiquidacionSocio']['intercambio'] = $oBanco->armaStringDebitoCJP($campos);
			else $socio['LiquidacionSocio']['intercambio'] = null;
		
//			debug($socio);
			
			$this->save($socio);
			$socios[$idx] = $socio;
		}
		return $socios;		
	}
	
	
	/**
	 * @deprecated (ver metodo generarDisketteCBUNuevo)
	 * Genera Diskette CBU
	 * Procesa los datos y genera el string para el diskette. El array pasado por paramentro viene del formulario de la pagina donde se 
	 * elijen los turnos, banco de intercambio y fecha
	 * @param array $datos
	 * @return array
	 */
	function generarDisketteCBU($datos){
		
		
		$socios = $this->find('all',array(
										'joins' => array(
												array(
													'table' => 'global_datos',
													'alias' => 'GlobalDato',
													'type' => 'inner',
													'foreignKey' => false,
													'conditions' => array('LiquidacionSocio.codigo_empresa = GlobalDato.id')
													),		
										),
										'conditions' => array(
																	'LiquidacionSocio.liquidacion_id' => $datos['LiquidacionSocio']['liquidacion_id'],
																	'LiquidacionSocio.turno_pago' => $datos['LiquidacionSocio']['turno_pago'],
																	'LiquidacionSocio.importe_adebitar >' => 0,
																	'LiquidacionSocio.diskette' => 1
																),
										'order' => array('GlobalDato.concepto_1,LiquidacionSocio.apenom,LiquidacionSocio.registro')								
		));
		
		App::import('Model', 'Mutual.LiquidacionTurno');
		$oTURNO = new LiquidacionTurno();

		App::import('Model', 'Config.Banco');
		$oBanco = new Banco(null);		
		
		App::import('Model', 'Pfyj.Socio');
		$oSOCIO = new Socio(null);		

		$cadena = "";
		$ERROR = 0;
		
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$liquidacion = $this->read(null,$datos['LiquidacionSocio']['liquidacion_id']);		
		// si la liquidacion esta cerrada no vuelvo a procesar los datos del diskette
		if($liquidacion['Liquidacion']['imputada'] == 1) return $socios;

		foreach($socios as $idx => $socio){
			
			$socio['LiquidacionSocio']['ERROR_INTERCAMBIO'] = "OK";
			
			$socio['LiquidacionSocio']['error_cbu'] = 0;
			$socio['LiquidacionSocio']['fecha_debito'] = parent::armaFecha($datos['LiquidacionSocio']['fecha_debito']);
			
			$calificacion = $oSOCIO->getUltimaCalificacion($socio['LiquidacionSocio']['socio_id'],$socio['LiquidacionSocio']['persona_beneficio_id']);
			$socio['LiquidacionSocio']['ultima_calificacion'] = $calificacion;
			
			//busco la marca en la global de los que no envia diskette segun la calificacion del socio
			$noEnvia = parent::GlobalDato('logico_2',$calificacion);

			//validar CBU
			if(parent::validarCBU($socio['LiquidacionSocio']['cbu']) && $noEnvia == 0):
				
				//BANCO CORDOBA
				if($datos['LiquidacionSocio']['banco_intercambio'] == '00020'):
				
					$campos = array(
									2 => $socio['LiquidacionSocio']['sucursal'],
									5 => $socio['LiquidacionSocio']['nro_cta_bco'],
									6 => $socio['LiquidacionSocio']['importe_adebitar'],
									7 => $socio['LiquidacionSocio']['fecha_debito'],
									9 => 0,
									10 => $socio['LiquidacionSocio']['cbu'],
									11 => $socio['LiquidacionSocio']['registro'],
									12 => $this->__genDebitoID($socio)
					
					);
					
					if(intval($socio['LiquidacionSocio']['sucursal']) != 0):
						$socio['LiquidacionSocio']['intercambio'] = $oBanco->armaStringDebitoBcoCba($campos);
					else:
						$socio['LiquidacionSocio']['intercambio'] = NULL;
						$socio['LiquidacionSocio']['ERROR_INTERCAMBIO'] = "ERROR_SUCURSAL";					
					endif;
				//BANCO STANDAR				
				elseif($datos['LiquidacionSocio']['banco_intercambio'] == '00430'):
				
					$campos = array(
									3 => substr($socio['LiquidacionSocio']['cbu'],0,8),
									4 => substr($socio['LiquidacionSocio']['cbu'],8,14),
									5 => $this->__genDebitoID($socio),
									6 => $socio['LiquidacionSocio']['importe_adebitar'],
									7 => $socio['LiquidacionSocio']['id'],
									8 => $socio['LiquidacionSocio']['fecha_debito']					
					);
					$socio['LiquidacionSocio']['intercambio'] = $oBanco->armaStringDebitoStandarBank($campos);
				
				//BANCO NACION
				elseif($datos['LiquidacionSocio']['banco_intercambio'] == '00011'):
					
//					DEBUG($socio);
					$campos = array(
									2 => $socio['LiquidacionSocio']['sucursal'],
									4 => $socio['LiquidacionSocio']['nro_cta_bco'],
									5 => $socio['LiquidacionSocio']['importe_adebitar'],
									9 => $socio['LiquidacionSocio']['socio_id'],	
									10 => $this->__genDebitoID($socio),
					);
					
					//controlo la longitud del nro de cuenta
					if(strlen(trim($socio['LiquidacionSocio']['nro_cta_bco'])) > 11){
						$socio['LiquidacionSocio']['error_cbu'] = 1;
						$socio['LiquidacionSocio']['intercambio'] = NULL;						
						$socio['LiquidacionSocio']['ERROR_INTERCAMBIO'] = "NRO_CTA > 11DIG";	
					}else{
						$socio['LiquidacionSocio']['intercambio'] = $oBanco->armaStringDebitoBcoNacion($campos);
					}

				//BANCO CREDICOOP
				elseif($datos['LiquidacionSocio']['banco_intercambio'] == '00191'):	
					$campos = array(
									1 => substr($socio['LiquidacionSocio']['cbu'],0,3),
									2 => substr($socio['LiquidacionSocio']['cbu'],0,8),
									3 => substr($socio['LiquidacionSocio']['cbu'],8,14),
									4 => $socio['LiquidacionSocio']['socio_id'],
									5 => $socio['LiquidacionSocio']['importe_adebitar'],
									6 => $socio['LiquidacionSocio']['id'],
									7 => $socio['LiquidacionSocio']['fecha_debito']					
					);
					$socio['LiquidacionSocio']['intercambio'] = $oBanco->armaStringDebitoBancoCrediCoop($campos);
				
				
				//BANCO INTERCAMBIO NO CREADO						
				else:
					
					$socio['LiquidacionSocio']['intercambio'] = NULL;
					$socio['LiquidacionSocio']['ERROR_INTERCAMBIO'] = "SIN_FORMULA";
				
				endif;
				
			else:
			
				$socio['LiquidacionSocio']['error_cbu'] = 1;
				$socio['LiquidacionSocio']['intercambio'] = NULL;
				if($noEnvia==1)$socio['LiquidacionSocio']['ERROR_INTERCAMBIO'] = "NO_ENVIA";
				else $socio['LiquidacionSocio']['ERROR_INTERCAMBIO'] = "ERROR_CBU";

				
			endif;
			
			$socio['LiquidacionSocio']['banco_intercambio'] = $datos['LiquidacionSocio']['banco_intercambio'];

			$socio['LiquidacionSocio']['turno'] = $oTURNO->getDescripcionByTruno($socio['LiquidacionSocio']['turno_pago']);
			$socio['LiquidacionSocio']['empresa'] = parent::GlobalDato('concepto_1',$socio['LiquidacionSocio']['codigo_empresa']);

			if(empty($socio['LiquidacionSocio']['turno_pago']) || $socio['LiquidacionSocio']['turno_pago'] == "SDATO"){
				$socio['LiquidacionSocio']['empresa'] = "*** SIN DATOS ***";
				$socio['LiquidacionSocio']['turno'] = "SDATO";
			}					
			
//			$socio['LiquidacionSocio']['descripcion_turno'] = $socio['LiquidacionSocio']['empresa'] . (!empty($socio['LiquidacionSocio']['turno']) ? " - ". $socio['LiquidacionSocio']['turno'] : "");
			$socio['LiquidacionSocio']['descripcion_turno'] = (!empty($socio['LiquidacionSocio']['turno']) ? $socio['LiquidacionSocio']['turno'] : "");
			
			
			//VALIDO LOS MAXIMOS Y MINIMOS
			if($socio['LiquidacionSocio']['importe_adebitar'] < $this->impoMinDtoCBU):
				$socio['LiquidacionSocio']['error_cbu'] = 1;
				$socio['LiquidacionSocio']['intercambio'] = NULL;
				$socio['LiquidacionSocio']['ERROR_INTERCAMBIO'] = "EL IMPORTE A DEBITAR ES INFERIOR AL MINIMO (".$this->impoMinDtoCBU.")";			
			endif;
			
			if($socio['LiquidacionSocio']['importe_adebitar'] > $this->impoMaxDtoCBU):
				$socio['LiquidacionSocio']['error_cbu'] = 1;
				$socio['LiquidacionSocio']['intercambio'] = NULL;
				$socio['LiquidacionSocio']['ERROR_INTERCAMBIO'] = "EL IMPORTE A DEBITAR ES SUPERIOR AL MAXIMO (".$this->impoMaxDtoCBU.")";			
			endif;
			
			if(!$this->save($socio)):
				$ERROR = 1;
				break;
			endif;
			
			$socios[$idx] = $socio;
			
//			if(empty($socio['LiquidacionSocio']['intercambio']) && $socio['LiquidacionSocio']['error_cbu'] == 0){
//				$ERROR = 1;
//				break;
//			}
				
		}
//		debug($socios);
//		exit;
		return ($ERROR == 0 ? $socios : NULL);
		
	}
	
	


	/**
	 * Gen Debito ID
	 * genera un identificador univoco del debito con longitud = 22 caracteres
	 * <ul>
	 * 		<li>socio_id (12 completados con ceros a la izq)</li>
	 * 		<li>liquidacion_id (8 completados con ceros a la izq)</li>
	 * 		<li>registro (2 completados con ceros a la izq)</li>
	 * </ul>
	 * @param $datos
	 */
	function __genDebitoID($datos,$full = true){
        $idDebito = null;
        if($full){
            $idDebito = str_pad($datos['LiquidacionSocio']['socio_id'], 12, '0', STR_PAD_LEFT);
            $idDebito .= str_pad($datos['LiquidacionSocio']['liquidacion_id'], 8, '0', STR_PAD_LEFT);
            $idDebito .= str_pad($datos['LiquidacionSocio']['registro'], 2, '0', STR_PAD_LEFT);	
        }else{
            //genero un id de 10 lugares con el socio y la liquidacion
            $idDebito = str_pad($datos['LiquidacionSocio']['liquidacion_id'], 4, '0', STR_PAD_LEFT);
            $idDebito .= str_pad($datos['LiquidacionSocio']['socio_id'], 6, '0', STR_PAD_LEFT);
        }
		return $idDebito;
	}
	
	/**
	 * Get Liquidaciones por Documento.
	 * Devuelve las liquidaciones del socio en base al tipo y numero de documento
	 * @param string $tipo
	 * @param string $nro
	 * @return array
	 */
	function getLiquidacionesByDocumento($tipo,$nro){
		$socios = $this->find('all',array('conditions' => array(
																	'LiquidacionSocio.tipo_documento' => $tipo,
																	'LiquidacionSocio.documento' => $nro,
																),
		));

		return $socios;	
	}
	
	/**
	 * GetLiquidacionBySocio
	 * Devuelve los registros para enviar a descuento para un socio y liquidacion
	 * @param $socio_id
	 * @param $liquidacion_id
	 * @return unknown_type
	 */
	function getLiquidacionBySocio($socio_id,$liquidacion_id){
		$socios = $this->find('all',array('conditions' => array(
																	'LiquidacionSocio.liquidacion_id' => $liquidacion_id,
																	'LiquidacionSocio.socio_id' => $socio_id,
																),
											'order' => array('LiquidacionSocio.registro')					
		));
		foreach($socios as $idx => $socio){
	
			//saco el banco de intercambio
			$banco = parent::getBanco($socio['LiquidacionSocio']['banco_intercambio']);
			$socio['LiquidacionSocio']['banco_intercambio_desc'] = $banco['Banco']['nombre'];
			$socio['LiquidacionSocio']['turno'] = substr(trim($socio['LiquidacionSocio']['turno_pago']),-5,5);
			
			
			$organismo = substr($socio['LiquidacionSocio']['codigo_organismo'],8,2);
			
			switch($organismo){
				case 22:
					$socios[$idx] = $this->__armaStrCBU($socio);
					break;
				case 77:
					$socios[$idx] = $this->__armaStrCJP($socio);
					break;
				case 66:
					$socios[$idx] = $this->__armaStrJN($socio);
					break;										
			}
		}
		return $socios;	
	}	

	/**
	 * Get Liquidaciones por Beneficio
	 * Devuelve las liquidaciones para un id de beneficio pasado por parametro
	 * @param integer $persona_beneficio_id
	 * @return array
	 */
	function getLiquidacionesByBeneficioID($persona_beneficio_id){
		$socios = $this->find('all',array('conditions' => array(
																	'LiquidacionSocio.persona_beneficio_id' => $persona_beneficio_id,
																),
		));
		return $socios;	
	}	
	
	
	function getPeriodosNoLiquidados($socio_id){
		$sql = "select distinct periodo from 
				liquidaciones where id not in (select liquidacion_id from liquidacion_socios where socio_id = $socio_id) and cerrada = 0 and imputada = 0";
		$periodos = $this->query($sql);
		return $periodos;
	}
	
	
	/**
	 * Liquidar deuda de un socio para un periodo, organismo
	 * @param $socio_id
	 * @param $periodo
	 * @param $organismo
	 * @param $liquidacion_id
	 * @param boolean $generaLiqSocio
	 * @return unknown_type
	 */
	function liquidar($socio_id,$periodo,$organismo,$liquidacion_id,$generaLiqSocio=true,$pre_imputacion=false,$cuotaSocialSoloDeuda=false,$CONTROL_NACION=false,$BANCO_CONTROL = null,$DISCRIMINA_PERMANENTES = FALSE,$tipoFiltro=0,$CONSOLIDADO = false){
		
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBen = new PersonaBeneficio();	

		App::import('Model','Mutual.MutualAdicional');
		$oADICIONAL = new MutualAdicional();		
		
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();	
		
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();
		
		App::import('Model','Mutual.MutualAdicionalPendiente');
		$oAP = new MutualAdicionalPendiente();
		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCuota = new OrdenDescuentoCuota();
		
		App::import('Model','Mutual.OrdenDescuento');
		$oDto = new OrdenDescuento();	
		
		App::import('Model', 'Pfyj.SocioCalificacion');
		$oCALIFICACION = new SocioCalificacion(null);

                #seteo si esta marcada como auditable la llamada
                $oBen->auditable = FALSE;
                $oADICIONAL->auditable = FALSE;
                $oLQ->auditable = FALSE;
                $oLC->auditable = FALSE;
                $oAP->auditable = FALSE;
                $oCuota->auditable = FALSE;
                $oDto->auditable = FALSE;
                $oCALIFICACION->auditable = FALSE;
		
		$status = array();
		
		$ERROR = FALSE;
		
		$status[0] = 0;
		$status[1] = "OK";		
		
                
		###########################################################################################################################
		# borro la liquidacion cuota para la liquidacion id y para el socio
		###########################################################################################################################
		if (!$oLC->deleteAll("LiquidacionCuota.liquidacion_id = $liquidacion_id AND LiquidacionCuota.socio_id = $socio_id")) {
                    $ERROR = true;
                }

                if($ERROR){
			$status[0] = 1;
			$status[1] = "ERROR EN BORRADO DE LIQUIDACION CUOTAS";
			return $status;
		}
		###########################################################################################################################
		# borro los adicionales pendientes que tenga para la liquidacion y socio
		###########################################################################################################################
        # verifico si tiene adicionales con devengado previo
//        $sql = "select id,orden_descuento_cuota_id from mutual_adicional_pendientes MutualAdicionalPendiente
//                where liquidacion_id = $liquidacion_id and socio_id = $socio_id and orden_descuento_cuota_id <> 0";
//        debug($sql);
        
//        $datos = $oAP->query($sql);
//        debug($datos);
//        exit;
//        if(!empty($datos)){
//            foreach($datos as $dato){
//                if($oCuota->borrarCuota($dato['MutualAdicionalPendiente']['orden_descuento_cuota_id'])){
//                    $oAP->id = $dato['MutualAdicionalPendiente']['id'];
//                    $oAP->saveField('orden_descuento_cuota_id',null);
//                    $oAP->id = 0;
//                }
//            }
//        }
//        exit;

		
//		$SQL = "delete cu.* from mutual_adicional_pendientes ap, orden_descuento_cuotas cu
//                where ap.liquidacion_id = $liquidacion_id
//                and ap.socio_id = $socio_id and ap.socio_id = cu.socio_id and ap.proveedor_id = cu.proveedor_id
//                and ap.periodo = cu.periodo and ap.tipo_cuota = cu.tipo_cuota
//                and cu.id not in (select orden_descuento_cuota_id from orden_descuento_cobro_cuotas cc inner join orden_descuento_cobros co
//                on co.id = cc.orden_descuento_cobro_id where co.socio_id = $socio_id);";  
                
                $SQL = "delete cu.* from mutual_adicional_pendientes ap, orden_descuento_cuotas cu
                        where ap.liquidacion_id = $liquidacion_id
                        and ap.socio_id = $socio_id and ap.socio_id = cu.socio_id and ap.proveedor_id = cu.proveedor_id
                        and ap.periodo = cu.periodo and ap.tipo_cuota = cu.tipo_cuota
                        and cu.id not in (select orden_descuento_cuota_id from orden_descuento_cobro_cuotas cc inner join orden_descuento_cobros co
                        on co.id = cc.orden_descuento_cobro_id where co.socio_id = $socio_id)
                        and cu.id not in (select orden_descuento_cuota_id from cancelacion_orden_cuotas coc inner join cancelacion_ordenes co on co.id = coc.cancelacion_orden_id
                        where co.socio_id = $socio_id);";
                
                
		$oAP->query($SQL);
        
            if (!$oAP->deleteAll("MutualAdicionalPendiente.liquidacion_id = $liquidacion_id AND MutualAdicionalPendiente.socio_id = $socio_id")) {
                $ERROR = true;
            }


        if($ERROR){
			$status[0] = 1;
			$status[1] = "ERROR EN BORRADO DE ADICIONALES PENDIENTES";
			return $status;
		}

		###########################################################################################################################
		# borro los consumos permanentes devengados para el periodo
		###########################################################################################################################		
		//$oCuota->borrarConsumoPermanenteDevengado($socio_id,$periodo);
		
		###########################################################################################################################
		# borro las cuotas sociales devengadas para el periodo
		###########################################################################################################################		
		if($cuotaSocialSoloDeuda){
                    $oCuota->borrarCuotaSocialDevengada($socio_id,$periodo,$organismo);
                }
                $oCuota->borrarCuotaSocialDevengada($socio_id,$periodo,$organismo);
		
		###########################################################################################################################
		# VERIFICAR QUE LA PERSONA NO ESTE MARCADA COMO FALLECIDA
		###########################################################################################################################
		App::import('Model', 'Pfyj.Socio');
		$oSOCIO = new Socio(null); 
                $oSOCIO->auditable = $this->auditable;
				
		$persona = $oSOCIO->getPersonaBySocioID($socio_id);
		if(!empty($persona) && $persona['Persona']['fallecida'] == 1){
			###########################################################################################################################
			# borro la cabecera de la liquidacion
			###########################################################################################################################		
			if(!$this->deleteAll("LiquidacionSocio.liquidacion_id = $liquidacion_id and LiquidacionSocio.socio_id = $socio_id"))$ERROR = true;
			return $status;
		}
		
		###########################################################################################################################
		#PASO 1: GENERAR CONCEPTOS PERMANENTES
		###########################################################################################################################
		$sql = "SELECT
					PersonaBeneficio.codigo_beneficio,
                    PersonaBeneficio.codigo_empresa,
					OrdenDescuento.id,
					OrdenDescuento.fecha,
					OrdenDescuento.tipo_orden_dto,
					OrdenDescuento.tipo_producto,
					OrdenDescuento.socio_id,
					OrdenDescuento.persona_beneficio_id,
					OrdenDescuento.proveedor_id,
					OrdenDescuento.nro_referencia_proveedor,
					OrdenDescuento.importe_cuota,
					OrdenDescuento.activo,
					OrdenDescuento.numero		 
				FROM 
					orden_descuentos as OrdenDescuento, 
					socios as Socio,
					persona_beneficios as PersonaBeneficio  
				WHERE
					Socio.id = $socio_id  
					AND OrdenDescuento.socio_id = Socio.id 
					AND OrdenDescuento.tipo_orden_dto <> 'CMUTU'
					AND OrdenDescuento.tipo_producto <> 'MUTUPROD0003'
					AND OrdenDescuento.periodo_ini <= '$periodo'
					AND IF(Socio.activo = 0,IFNULL(OrdenDescuento.periodo_hasta,'$periodo'),IF(ISNULL(OrdenDescuento.periodo_hasta) AND OrdenDescuento.activo = 1,'999999',OrdenDescuento.periodo_hasta)) > '$periodo'
					AND OrdenDescuento.persona_beneficio_id = PersonaBeneficio.id
					AND PersonaBeneficio.codigo_beneficio = '$organismo'
					AND OrdenDescuento.permanente = 1 
                    AND OrdenDescuento.id NOT IN (select orden_descuento_id 
                    from orden_descuento_cuotas odc, persona_beneficios be 
                    where odc.orden_descuento_id = OrdenDescuento.id and odc.periodo = '$periodo'
                    and odc.persona_beneficio_id = be.id    
                    and odc.tipo_cuota = ifnull((SELECT concepto_2 FROM global_datos gl
                    where gl.id = OrdenDescuento.tipo_producto),'MUTUTCUOCONS') and odc.estado <> 'B'
                    and be.codigo_beneficio = PersonaBeneficio.codigo_beneficio);";		
			$ordenes = $oDto->query($sql);
			if(count($ordenes) != 0):
				foreach($ordenes as $orden):
					$oCuota->generaCuotaPermanente($orden,$periodo);
				endforeach;
			endif;
		
		###########################################################################################################################	
		#PASO 2: GENERAR CUOTA SOCIAL
		###########################################################################################################################
		$sql = "SELECT
					PersonaBeneficio.codigo_beneficio, 
                    PersonaBeneficio.codigo_empresa,
					OrdenDescuento.id,
					OrdenDescuento.fecha,
					OrdenDescuento.tipo_orden_dto,
					OrdenDescuento.tipo_producto,
					OrdenDescuento.socio_id,
					OrdenDescuento.persona_beneficio_id,
					OrdenDescuento.proveedor_id,
					OrdenDescuento.nro_referencia_proveedor,
					OrdenDescuento.importe_cuota,
					OrdenDescuento.activo,
                                        CASE 
                                            WHEN '202407' > IFNULL(Socio.periodo_hasta_importe_cuota_social, '999999') THEN 0
                                            ELSE IFNULL(Socio.importe_cuota_social, 0)
                                        END as importe_cuota_social,
                                        (SELECT ifnull(max(MutualProducto.cuota_social_diferenciada),0) FROM orden_descuentos AS OrdenDescuento
						INNER JOIN mutual_productos AS MutualProducto ON
						(
							MutualProducto.tipo_orden_dto = OrdenDescuento.tipo_orden_dto
							AND MutualProducto.tipo_producto = OrdenDescuento.tipo_producto
							AND MutualProducto.proveedor_id = OrdenDescuento.proveedor_id
						)
						WHERE OrdenDescuento.socio_id = Socio.id
						AND OrdenDescuento.activo = 1
						AND OrdenDescuento.tipo_orden_dto <> 'CMUTU'
						AND MutualProducto.cuota_social_diferenciada <> 0
						AND OrdenDescuento.socio_id NOT IN
						(SELECT socio_id FROM orden_descuentos WHERE tipo_orden_dto <> 'CMUTU'
						AND proveedor_id <> OrdenDescuento.proveedor_id)
						ORDER BY MutualProducto.cuota_social_diferenciada DESC) as cuota_social_diferenciada                                        
				FROM 
					orden_descuentos as OrdenDescuento, 
					socios as Socio,
					persona_beneficios as PersonaBeneficio  
				WHERE
					Socio.id = $socio_id  
					AND OrdenDescuento.socio_id = Socio.id 
					AND OrdenDescuento.tipo_orden_dto = 'CMUTU'
					AND OrdenDescuento.tipo_producto = 'MUTUPROD0003'
					AND OrdenDescuento.periodo_ini <= '$periodo'
					AND IF(Socio.activo = 0,IFNULL(OrdenDescuento.periodo_hasta,'$periodo'),IF(ISNULL(OrdenDescuento.periodo_hasta) AND OrdenDescuento.activo = 1,'999999',OrdenDescuento.periodo_hasta)) > '$periodo'
					AND OrdenDescuento.persona_beneficio_id = PersonaBeneficio.id
					AND PersonaBeneficio.codigo_beneficio = '$organismo'
					AND OrdenDescuento.permanente = 1
                                        AND IF(OrdenDescuento.activo = 0,IFNULL(OrdenDescuento.periodo_hasta,'$periodo'),IF(ISNULL(OrdenDescuento.periodo_hasta) AND OrdenDescuento.activo = 1,'999999',OrdenDescuento.periodo_hasta)) > '$periodo'
					AND OrdenDescuento.id not in 
						(select orden_descuento_id from orden_descuento_cuotas odc, persona_beneficios be 
						where odc.orden_descuento_id = OrdenDescuento.id and odc.periodo = '$periodo'
                                                -- and odc.persona_beneficio_id = be.id    
						and odc.tipo_cuota = 'MUTUTCUOCSOC' and odc.estado <> 'B'
                        and be.codigo_beneficio = '$organismo')";			
		$ordenes = $oDto->query($sql);
//        debug($ordenes);
// 		DEBUG($sql);
		if(count($ordenes) != 0):
			foreach($ordenes as $orden):
				$oCuota->generaCuotaSocial($orden,$periodo,$cuotaSocialSoloDeuda);
			endforeach;
		endif;		
		
		###########################################################################################################################
		#	PASO 3: GENERAR LA LIQUIDACION CUOTAS
		#	CONTROLAR SI LA ULTIMA CALIFICACION DEL SOCIO ES STOP DEBIT.  SI METIO UN STOP NO GENERAR LIQUIDACION
		###########################################################################################################################
		$generaLiquidacion = true;
		if(substr($organismo,8,2) == "22"){
			if($oCALIFICACION->isStopDebit($socio_id,$periodo)) $generaLiquidacion = false;
			else $generaLiquidacion = true;
		}
		$cuotas = null;
		
		if($generaLiquidacion):
		
                        //$situacion='MUTUSICUMUTU',$preImputacion = false,$proveedor_id = null,$tipoFiltro=0
			$cuotas = $oCuota->cuotasAdeudadasBySocioAlPeriodoByOrganismo($socio_id,$periodo,$organismo,'MUTUSICUMUTU',FALSE,NULL,$tipoFiltro);
//			if(!empty($cuotas))debug($cuotas);
//			exit;
			if(!empty($cuotas)):
                                $pre_imputacion = TRUE;
				foreach($cuotas as $cuota):
                                        $oLC->auditable = FALSE;
                                        
					if(!$oLC->generarDetalleLiquidacionCuota($liquidacion_id,$periodo,$cuota,$pre_imputacion)):
						$status[0] = 1;
						$status[1] = "ERROR AL GENERAR LA LIQUIDACION CUOTAS";	
						$ERROR = TRUE;
						break;
					endif;				
				endforeach;
			endif;
	
			if($ERROR):
				return $status;
			endif;		
	
			###########################################################################################################################
			#PASO 4: GENERAR CONCEPTOS ADICIONALES
			###########################################################################################################################
			$adicionales = $oAP->generarAdicional($liquidacion_id, $socio_id, $organismo,$periodo, 'MUTUSICUMUTU', $pre_imputacion,FALSE);

			// debug($adicionales);
			// exit;
			
			if($ERROR){
				$status[0] = 1;
				$status[1] = $oAP->notificaciones;
				return $status;
			}
			
			###########################################################################################################################
			#PASO 5: GENERAR LA CABECERA DE LIQUIDACION DEL SOCIO
			#CUANDO SE IMPUTA NO SE VUELVE A GENERAR PARA DEJAR EL ULTIMO QUE SE PROCESO
			###########################################################################################################################

			if($generaLiqSocio):
			
				$LiquidacionSocios = $oLC->getInfoDto_AMAN($liquidacion_id,$socio_id,$periodo,$organismo,$CONTROL_NACION,$BANCO_CONTROL,$DISCRIMINA_PERMANENTES,$CONSOLIDADO);


				if(!$this->deleteAll("LiquidacionSocio.liquidacion_id = $liquidacion_id and LiquidacionSocio.socio_id = $socio_id"))$ERROR = true;		
				if(!empty($LiquidacionSocios)):
// 					$tmp = array();

					// sacar el valor del costo por registro
// 					$impoAdicionalGtoBco = $oAP->generarAdicional($liquidacion_id, $socio_id, $organismo,$periodo, 'MUTUSICUMUTU', $pre_imputacion,FALSE,6,count($LiquidacionSocios));
					// debug($this->notificaciones);
					// debug($impoAdicionalGtoBco);
					// debug(count($LiquidacionSocios));
					// exit;
// 					$costoPorRegistro = NULL;
// // 					if(!empty($impoAdicionalGtoBco)){
// // 						$costoPorRegistro = round($impoAdicionalGtoBco / count($LiquidacionSocios),2);
// // 					}


					foreach($LiquidacionSocios as $liquidacion):
						$datos['LiquidacionSocio'] = $liquidacion['LiquidacionCuota'];
                                                $datos['LiquidacionSocio']['id'] = 0;
//						$this->id = 0;
                                                $this->auditable = FALSE;
						
// 						if(!empty($costoPorRegistro)){
// 							$datos['LiquidacionSocio']['importe_dto'] += $costoPorRegistro;
// 							$datos['LiquidacionSocio']['importe_adebitar'] += $costoPorRegistro;
// 						}						

						if(!$this->save($datos)):
							$status[0] = 1;
							$status[1] = "ERROR AL GRABAR EL RESUMEN DE LIQUIDACION DEL SOCIO";							
							$ERROR = TRUE;
							break;
						else:
							//SI ES CJP GENERO LA CANDENA DEL INTERCAMBIO
							if(substr($organismo,8,2) == "77") $this->generarDisketteCJP($liquidacion_id,$socio_id);
						endif;
					endforeach;

					//CALCULAR EL ADICIONAL POR CANTIDAD DE REGISTROS
					//APLICAR EL VALOR AL IMPORTE DE CADA REGISTRO



				endif;
			endif;
	
		else:
			//NO GENERA LIQUIDACION, BORRAR LA CABECERA DE LIQUIDACION DEL SOCIO
			if(!$this->deleteAll("LiquidacionSocio.liquidacion_id = $liquidacion_id and LiquidacionSocio.socio_id = $socio_id"))$ERROR = true;	
			
		endif;	
		
                // $dbCONFIG = new DATABASE_CONFIG();
                // $link = mysqli_connect($dbCONFIG->default['host'],$dbCONFIG->default['login'], $dbCONFIG->default['password'],$dbCONFIG->default['database']);            
                // mysqli_query($link,"CALL SP_LIQUIDA_GTO_BANCARIOS($liquidacion_id,$socio_id)");                
                // mysqli_query($link,"CALL SP_LIQUIDA_DEUDA_SOCIOS_SCORING($liquidacion_id,$socio_id)");
                
		$this->query("CALL SP_LIQUIDA_GTO_BANCARIOS($liquidacion_id,$socio_id)");
                // $this->query("CALL SP_LIQUIDA_DEUDA_SOCIOS_SCORING($liquidacion_id,$socio_id)");                
                
		###########################################################################################################################
		#FIN LIQUIDACION
		###########################################################################################################################
		return $status;
		
	}
	
	
	function getConsumosNoDescontadosCjpParaDebitoByCBU($liquidacion_id,$bancoIntercambio=null,$fechaDebito=null){
		
		$datos = array();
		
//		$sql = "select 
//				LiquidacionSocio.socio_id,
//				LiquidacionSocio.tipo,
//				LiquidacionSocio.nro_ley,
//				LiquidacionSocio.nro_beneficio,
//				LiquidacionSocio.sub_beneficio,
//				LiquidacionSocio.documento,
//				LiquidacionSocio.apenom,
//				PersonaBeneficio.cbu,
//				PersonaBeneficio.nro_sucursal,
//				PersonaBeneficio.nro_cta_bco,
//				LiquidacionSocio.cbu,
//				sum(LiquidacionSocio.importe_cuota) as importe_cuota
//				from liquidacion_socios as LiquidacionSocio
//				inner join socios as Socio on (Socio.id = LiquidacionSocio.socio_id)
//				inner join persona_beneficios as PersonaBeneficio on (PersonaBeneficio.persona_id = Socio.persona_id and PersonaBeneficio.activo = 1 and PersonaBeneficio.codigo_beneficio = 'MUTUCORG2201')
//				where LiquidacionSocio.liquidacion_id = $liquidacion_id 
//				and LiquidacionSocio.sub_codigo = '1'
//				and LiquidacionSocio.orden_descuento_id not in (select orden_descuento_id from liquidacion_socio_rendiciones
//				as LiquidacionSocioRendicion 
//				where LiquidacionSocioRendicion.liquidacion_id = LiquidacionSocio.liquidacion_id
//				and LiquidacionSocioRendicion.sub_codigo = '1')
//				and ifnull(PersonaBeneficio.cbu,'') <> ''
//				group by PersonaBeneficio.cbu
//				order by LiquidacionSocio.apenom";
		
		
		$sql = "select 
                                LiquidacionSocio.liquidacion_id,
				LiquidacionSocio.id,
				LiquidacionSocio.codigo_organismo,
				LiquidacionSocio.socio_id,
				LiquidacionSocio.tipo,
				LiquidacionSocio.nro_ley,
				LiquidacionSocio.nro_beneficio,
				LiquidacionSocio.sub_beneficio,
				LiquidacionSocio.documento,
				LiquidacionSocio.apenom,
				LiquidacionSocio.cbu,
				sum(LiquidacionSocio.importe_cuota) as importe_cuota,
				sum(LiquidacionSocio.importe_adebitar) as importe_adebitar
				from liquidacion_socios as LiquidacionSocio
				where LiquidacionSocio.liquidacion_id = $liquidacion_id 
				and LiquidacionSocio.sub_codigo = '1'
				and LiquidacionSocio.orden_descuento_id not in (select orden_descuento_id from liquidacion_socio_rendiciones
				as LiquidacionSocioRendicion 
				where LiquidacionSocioRendicion.liquidacion_id = LiquidacionSocio.liquidacion_id
				and LiquidacionSocioRendicion.sub_codigo = '1')
				group by LiquidacionSocio.socio_id
				order by LiquidacionSocio.apenom LIMIT 10";		
		
//		debug($sql);
		
		$resultados = $this->query($sql);
		
//		debug($resultados);
//		exit;
		
		if(!empty($resultados)):
		
			App::import('Model', 'Config.Banco');
			$oBANCO = new Banco(null);		
			
			App::import('Model', 'pfyj.Socio');
			$oSOCIO = new Socio(null);			
			
//			$tope = parent::GlobalDato('decimal_1',"MUTUCORG2201");
			
			$reg = 0;
			
			$ACUM_REGISTROS = 0;
			$ACUM_IMPORTE = $ACUM_IMPORTE_DISK = $ACUM_REGISTROS = $ACUM_IMPORTE_ERROR = $ACUM_REGISTROS_ERROR =  $ACUM_REGISTROS_DISK = 0;
			$registros = array();
			
			foreach($resultados as $idx => $resultado):
			
//				debug($resultado);
			
				$reg++;
				
//				$resultado['LiquidacionSocio']['cbu'] = $resultado['PersonaBeneficio']['cbu'];

				
				if($resultado[0]['importe_cuota'] == 0) $resultado[0]['importe_cuota'] = $resultado[0]['importe_adebitar'];
				
				//busco el beneficio para sacar el CBU
				$beneficio = $oSOCIO->getUltimoBeneficioActivosByCodOrganismo($resultado['LiquidacionSocio']['socio_id'],'MUTUCORG2201');
				if(!empty($beneficio['PersonaBeneficio']['cbu'])) $resultado['LiquidacionSocio']['cbu'] = $beneficio['PersonaBeneficio']['cbu'];
				
				
//				debug($resultado);
				
				if(!empty($resultado['LiquidacionSocio']['cbu'])):
				
//					if(!empty($beneficio['PersonaBeneficio']['cbu'])) $resultado['LiquidacionSocio']['cbu'] = $beneficio['PersonaBeneficio']['cbu'];
			
					$resultado['LiquidacionSocio']['importe_original'] = $resultado[0]['importe_cuota'];
					$resultado['LiquidacionSocio']['importe_adebitar'] = $resultado['LiquidacionSocio']['importe_original'];
					//controlo el limite del banco
//					if($resultado['LiquidacionSocio']['importe_original'] > $this->impoMaxDtoCBU) $resultado['LiquidacionSocio']['importe_adebitar'] = $this->impoMaxDtoCBU;
//					else $resultado['LiquidacionSocio']['importe_adebitar'] = $resultado['LiquidacionSocio']['importe_original'];
					
					
					$decoCBU = $oBANCO->deco_cbu($resultado['LiquidacionSocio']['cbu']);	
					$resultado['LiquidacionSocio']['cbu_banco'] = $decoCBU['banco_id'];
					$resultado['LiquidacionSocio']['cbu_sucursal'] = $decoCBU['sucursal'];
					$resultado['LiquidacionSocio']['cbu_nro_cta_bco'] = $decoCBU['nro_cta_bco'];
					
					$resultado['LiquidacionSocio']['error_cbu'] = 0;
					
//					$resultado['LiquidacionSocio']['cbu_ok'] = $oBANCO->validarCBU($resultado['LiquidacionSocio']['cbu']);
//					$resultado['LiquidacionSocio']['cbu_msg'] = "";
					
//					if($resultado['LiquidacionSocio']['cbu_ok'] == 0) $resultado['LiquidacionSocio']['cbu_msg'] = "CBU NO VALIDO";
//					if(intval($decoCBU['nro_cta_bco']) == 0){
//						$resultado['LiquidacionSocio']['error_cbu'] = 1;
//						$resultado['LiquidacionSocio']['cbu_msg'] = "CUENTA NO VALIDA";
//					}
					
//					if($resultado['LiquidacionSocio']['importe_adebitar'] < $this->impoMinDtoCBU){
//						$resultado['LiquidacionSocio']['cbu_ok'] = 0;
//						$resultado['LiquidacionSocio']['cbu_msg'] = "IMPORTE INF AL MINIMO POR CBU";						
//					}
					
//					if(!empty($bancoIntercambio) && $resultado['LiquidacionSocio']['cbu_ok'] == 1):
					if(!empty($bancoIntercambio)):
					
						//GENERAR UN ID DE DEBITO DE 22
						$TIPO = substr(str_pad(trim($resultado['LiquidacionSocio']['tipo']), 1, '0', STR_PAD_LEFT),-1);
						$LEY = substr(str_pad(trim($resultado['LiquidacionSocio']['nro_ley']), 2, '0', STR_PAD_LEFT),-2);
						$NROBENEFICIO = substr(str_pad(trim($resultado['LiquidacionSocio']['nro_beneficio']), 6, '0', STR_PAD_LEFT),-6);
						$SUBENEFICIO = substr(str_pad(trim($resultado['LiquidacionSocio']['sub_beneficio']), 2, '0', STR_PAD_LEFT),-2);								
						$SOCIOID = str_pad($resultado['LiquidacionSocio']['socio_id'], 11, '0', STR_PAD_LEFT);
	
						$IDDEBITO = $TIPO.$LEY.$NROBENEFICIO.$SUBENEFICIO.$SOCIOID;					
					
						$importe 			= $resultado['LiquidacionSocio']['importe_adebitar'];
						$registroNro 		= $reg;
						$idDebito 			= $IDDEBITO;
						$liquidacionSocioId = $resultado['LiquidacionSocio']['id'];
						$socioId 			= $resultado['LiquidacionSocio']['socio_id'];
						$sucursal 			= $resultado['LiquidacionSocio']['cbu_sucursal'];
						$cuenta 			= $resultado['LiquidacionSocio']['cbu_nro_cta_bco'];
						$cbu 				= $resultado['LiquidacionSocio']['cbu'];
						$codOrganismo 		= $resultado['LiquidacionSocio']['codigo_organismo'];
						$calificacion 		= $resultado['LiquidacionSocio']['ultima_calificacion'];
                                                $liquidacion_id		= $resultado['LiquidacionSocio']['liquidacion_id'];
						
						$registro = $oBANCO->genRegistroDisketteBanco($bancoIntercambio,$fechaDebito,$importe,$registroNro,$idDebito,$liquidacionSocioId,$socioId,$sucursal,$cuenta,$cbu,$codOrganismo,$calificacion,NULL,$liquidacion_id);
						
//						debug($registro);
						
						$resultado['LiquidacionSocio']['importe_adebitar'] = $registro['importe_debito'];
						$resultado['LiquidacionSocio']['error_cbu'] = $registro['error'];
						$resultado['LiquidacionSocio']['intercambio'] = $registro['cadena'];
						if(!empty($registro['mensaje']))$resultado['LiquidacionSocio']['ERROR_INTERCAMBIO'] = $registro['mensaje'];
						
						$resultado['LiquidacionSocio']['cbu_sucursal'] = $registro['sucursal_formed'];
						$resultado['LiquidacionSocio']['cbu_nro_cta_bco'] = $registro['cuenta_formed'];					
											
						$resultado['LiquidacionSocio']['fecha_debito'] = $fechaDebito;
						
						$ACUM_REGISTROS++;
						$ACUM_IMPORTE += $resultado['LiquidacionSocio']['importe_adebitar'];
						
						if($registro['error'] == 0){
							$ACUM_REGISTROS_DISK++;
							$ACUM_IMPORTE_DISK += $resultado['LiquidacionSocio']['importe_adebitar'];
							array_push($registros,$resultado);
						}else{
							$resultado['LiquidacionSocio']['importe_adebitar'] = 0;
							$ACUM_IMPORTE_ERROR += $resultado['LiquidacionSocio']['importe_adebitar'];
							$ACUM_REGISTROS_ERROR++;
						}						
	
																			
					endif;
					
					$datos[$idx]['LiquidacionSocio'] = $resultado['LiquidacionSocio'];
					
				
				endif;
			
			endforeach;
		
		endif;
		
		
		
		$resultados1['info_procesada'] = $datos;
		$resultados1['totales'] = array(
			'registros' => $ACUM_REGISTROS,
			'liquidado' => $ACUM_IMPORTE,
			'errores' => $ACUM_REGISTROS_ERROR,
			'registros_disk' => $ACUM_REGISTROS_DISK,
			'importe_disk' => $ACUM_IMPORTE_DISK,
			'importe_error' => $ACUM_IMPORTE_ERROR,
		);
		
		$registros = Set::extract("/LiquidacionSocio[error_cbu=0]/intercambio",$datos);
		$importes = Set::extract("/LiquidacionSocio[error_cbu=0]/importe_adebitar",$datos);
		$resultados1['diskette'] = $oBANCO->genDisketteBanco($bancoIntercambio,$fechaDebito,$ACUM_REGISTROS_DISK,$ACUM_IMPORTE_DISK,$datos['LiquidacionSocio']['nro_archivo'],$registros);
		
		return $resultados1;
		
	}
	
	
	/**
	 * Genera Diskette CBU
	 * Procesa los datos y genera el string para el diskette. El array pasado por paramentro viene del formulario de la pagina donde se 
	 * elijen los turnos, banco de intercambio y fecha
	 * @param array $datos
	 * @return array
	 */
	function generarDisketteCBUNuevo($datos){
		
//		debug($datos);
		
		$socios = $this->find('all',array(
										'joins' => array(
												array(
													'table' => 'global_datos',
													'alias' => 'GlobalDato',
													'type' => 'inner',
													'foreignKey' => false,
													'conditions' => array('LiquidacionSocio.codigo_empresa = GlobalDato.id')
													),		
										),
										'conditions' => array(
																	'LiquidacionSocio.liquidacion_id' => $datos['LiquidacionSocio']['liquidacion_id'],
																	'LiquidacionSocio.turno_pago' => $datos['LiquidacionSocio']['turno_pago'],
																	'LiquidacionSocio.importe_adebitar >' => 0,
																	'LiquidacionSocio.diskette' => 1
																),
										'order' => array('GlobalDato.concepto_1,LiquidacionSocio.apenom,LiquidacionSocio.registro'),
//										'limit' => 100								
		));
		
		App::import('Model', 'Mutual.LiquidacionTurno');
		$oTURNO = new LiquidacionTurno();

		App::import('Model', 'Config.Banco');
		$oBanco = new Banco(null);		
		
		App::import('Model', 'Pfyj.Socio');
		$oSOCIO = new Socio(null);		

		$cadena = "";
		$ERROR = 0;
		
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$liquidacion = $this->read(null,$datos['LiquidacionSocio']['liquidacion_id']);		
		// si la liquidacion esta cerrada no vuelvo a procesar los datos del diskette
		if($liquidacion['Liquidacion']['imputada'] == 1) return $socios;
		
		
		$ACUM_REGISTROS = 0;
		$ACUM_IMPORTE = $ACUM_IMPORTE_DISK = $ACUM_REGISTROS = $ACUM_REGISTROS_ERROR =  $ACUM_REGISTROS_DISK = 0;

		$registros = array();

		foreach($socios as $idx => $socio){
			
			$socio['LiquidacionSocio']['ERROR_INTERCAMBIO'] = "OK";
			
			$socio['LiquidacionSocio']['error_cbu'] = 0;
			$socio['LiquidacionSocio']['fecha_debito'] = parent::armaFecha($datos['LiquidacionSocio']['fecha_debito']);
			
			$calificacion = $oSOCIO->getUltimaCalificacion($socio['LiquidacionSocio']['socio_id'],$socio['LiquidacionSocio']['persona_beneficio_id']);
			$socio['LiquidacionSocio']['ultima_calificacion'] = $calificacion;
			
			$bancoIntercambio	= $datos['LiquidacionSocio']['banco_intercambio'];
			$fechaDebito		= $socio['LiquidacionSocio']['fecha_debito'];
			$importe 			= $socio['LiquidacionSocio']['importe_adebitar'];
			$registroNro 		= $socio['LiquidacionSocio']['registro'];
			$idDebito 			= $this->__genDebitoID($socio);
			$liquidacionSocioId = $socio['LiquidacionSocio']['id'];
			$socioId 			= $socio['LiquidacionSocio']['socio_id'];
			$sucursal 			= $socio['LiquidacionSocio']['sucursal'];
			$cuenta 			= $socio['LiquidacionSocio']['nro_cta_bco'];
			$cbu 				= $socio['LiquidacionSocio']['cbu'];
			$codOrganismo 		= $socio['LiquidacionSocio']['codigo_organismo'];
			$calificacion 		= $socio['LiquidacionSocio']['ultima_calificacion'];
			$apenom 			= $socio['LiquidacionSocio']['apenom'];
			$ndoc	 			= $socio['LiquidacionSocio']['documento'];
			
			// $bancoIntercambio, $fechaDebito, $importe, $registroNro, $idDebito, $liquidacionSocioId, $socioId, $sucursal, $cuenta, $cbu, $codOrganismo, $apenom, $ndoc, $calificacion = null, $beneficioBancoId = null,$liquidacionID = null,$convenioBcoCba = null,$socioCuitCuil = null,$nroArchivo = NULL,$fechaPresentacion = NULL,$fechaMaxima = NULL,$ciclos = NULL,$idDebitoMin = NULL
			$registro = $oBanco->genRegistroDisketteBanco($bancoIntercambio,$fechaDebito,$importe,$registroNro,$idDebito,$liquidacionSocioId,$socioId,$sucursal,$cuenta,$cbu,$codOrganismo,$apenom,$ndoc,$calificacion);
			
			$socio['LiquidacionSocio']['importe_adebitar'] = $registro['importe_debito'];
			$socio['LiquidacionSocio']['error_cbu'] = $registro['error'];
			$socio['LiquidacionSocio']['intercambio'] = $registro['cadena'];
			if(!empty($registro['mensaje']))$socio['LiquidacionSocio']['ERROR_INTERCAMBIO'] = $registro['mensaje'];
			
			$socio['LiquidacionSocio']['sucursal'] = $registro['sucursal_formed'];
			$socio['LiquidacionSocio']['nro_cta_bco'] = $registro['cuenta_formed'];
			
			
			$ACUM_REGISTROS++;
			$ACUM_IMPORTE += $socio['LiquidacionSocio']['importe_adebitar'];
			
			if($registro['error'] == 0){
				$ACUM_REGISTROS_DISK++;
				$ACUM_IMPORTE_DISK += $socio['LiquidacionSocio']['importe_adebitar'];
				array_push($registros,$socio);
			}else{
				$ACUM_REGISTROS_ERROR++;
			}
			
			//ACTUALIZO LA TABLA LIQUIDACION_SOCIOS
			$update = array(
				'LiquidacionSocio.importe_adebitar' => $socio['LiquidacionSocio']['importe_adebitar'],
				'LiquidacionSocio.error_cbu' => $socio['LiquidacionSocio']['error_cbu'],
				'LiquidacionSocio.banco_intercambio' => "'$bancoIntercambio'",
				'LiquidacionSocio.fecha_debito' => "'$fechaDebito'",
				'LiquidacionSocio.intercambio' => "'".$socio['LiquidacionSocio']['intercambio']."'",
				'LiquidacionSocio.sucursal' => "'".$socio['LiquidacionSocio']['sucursal']."'",
				'LiquidacionSocio.nro_cta_bco' => "'".$socio['LiquidacionSocio']['nro_cta_bco']."'",
				'LiquidacionSocio.ultima_calificacion' => "'".$socio['LiquidacionSocio']['ultima_calificacion']."'",
			);
			
			if(!$this->updateAll($update,array('LiquidacionSocio.id' => $socio['LiquidacionSocio']['id']))){
				$ERROR = 1;
				break;
			}

//			debug($update);
			
			
//			if(!$this->save($socio)):
//				$ERROR = 1;
//				break;
//			endif;
			
			$socios[$idx] = $socio;
			
		}
		
		$resultados['info_procesada'] = $socios;
		$resultados['totales'] = array(
			'registros' => $ACUM_REGISTROS,
			'liquidado' => $ACUM_IMPORTE,
			'errores' => $ACUM_REGISTROS_ERROR,
			'registros_disk' => $ACUM_REGISTROS_DISK,
			'importe_disk' => $ACUM_IMPORTE_DISK,
			'importe_error' => $ACUM_IMPORTE - $ACUM_IMPORTE_DISK,
		);
		
		$registros = Set::extract("/LiquidacionSocio[error_cbu=0]/intercambio",$socios);
		$importes = Set::extract("/LiquidacionSocio[error_cbu=0]/importe_adebitar",$socios);
		$resultados['diskette'] = $oBanco->genDisketteBanco($bancoIntercambio,$fechaDebito,$ACUM_REGISTROS_DISK,$ACUM_IMPORTE_DISK,$datos['LiquidacionSocio']['nro_archivo'],$registros);
		
		return ($ERROR == 0 ? $resultados : NULL);
		
	}	
	
	function generarDisketteCBUFromAsincronoProcess($asincrono_id){
		
		App::import('model','Shells.Asincrono');
		$oASINC = new Asincrono();
		
		App::import('Model', 'Config.Banco');
		$oBanco = new Banco(null);		
					
		$asinc = $oASINC->read("txt1,txt2",$asincrono_id);	

// 		$parametros = unserialize(base64_decode($asinc['Asincrono']['txt1']));
// 		$resultadoProceso = unserialize(base64_decode($asinc['Asincrono']['txt2']));
		
		$parametros = unserialize($asinc['Asincrono']['txt1']);
		$resultadoProceso = unserialize($asinc['Asincrono']['txt2']);
		
		
//		debug(count($socios));
//		debug(count(Set::extract("/LiquidacionSocio[turno_pago=OP007]",$socios)));
//		debug(Set::extract("/LiquidacionSocio[turno_pago=OP007]",$socios));
//		
//		exit;

		$aTurnos = Set::sort($parametros['LiquidacionSocio']['turno_pago'], '{n}.beneficiario_apenom', 'asc');
		$tmpResumeByTurno = array();

		$empresas = array();
		$turnos = array();		
		
		foreach($parametros['LiquidacionSocio']['turno_pago'] as $turno => $dato):

			list($empresa,$turno) = explode('|',$turno);
			array_push($empresas,$empresa);
			array_push($turnos,$turno);				
		
			list($cantidad,$impoDto,$impoDebito,$empresaTurnoDesc) = explode("|", $dato);
			
			$SQL_RESUMEN = "SELECT LiquidacionSocio.turno_pago,COUNT(*) AS liquidados,
							SUM(LiquidacionSocio.importe_dto) AS importe_dto,
							SUM(LiquidacionSocio.importe_adebitar) AS importe_adebitar, 'TOTAL' AS tipo 
							FROM liquidacion_socios AS LiquidacionSocio
							WHERE LiquidacionSocio.liquidacion_id = ".$parametros['LiquidacionSocio']['liquidacion_id']."
							AND LiquidacionSocio.codigo_empresa = '$empresa' 
							AND LiquidacionSocio.turno_pago = '$turno' and LiquidacionSocio.diskette = 1
							UNION
							SELECT LiquidacionSocio.turno_pago,COUNT(*) AS liquidados,
							SUM(LiquidacionSocio.importe_dto) AS importe_dto,
							SUM(LiquidacionSocio.importe_adebitar) AS importe_adebitar, 'OK' AS tipo 
							FROM liquidacion_socios AS LiquidacionSocio
							WHERE LiquidacionSocio.liquidacion_id = ".$parametros['LiquidacionSocio']['liquidacion_id']."
							AND LiquidacionSocio.codigo_empresa = '$empresa' 
							AND LiquidacionSocio.turno_pago = '$turno' and LiquidacionSocio.diskette = 1
							AND LiquidacionSocio.error_cbu = 0
							UNION
							SELECT LiquidacionSocio.turno_pago,COUNT(*) AS liquidados,
							SUM(LiquidacionSocio.importe_dto) AS importe_dto,
							SUM(LiquidacionSocio.importe_adebitar) AS importe_adebitar, 'ERROR' AS tipo 
							FROM liquidacion_socios AS LiquidacionSocio
							WHERE LiquidacionSocio.liquidacion_id = ".$parametros['LiquidacionSocio']['liquidacion_id']."
							AND LiquidacionSocio.codigo_empresa = '$empresa' 
							AND LiquidacionSocio.turno_pago = '$turno' and LiquidacionSocio.diskette = 1
							AND LiquidacionSocio.error_cbu = 1
							GROUP BY LiquidacionSocio.turno_pago;";
			
			$resumen = $this->query($SQL_RESUMEN);
//			debug($resumen);

			
			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['descripcion'] = $empresaTurnoDesc;
			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['importe_dto'] = (isset($resumen[0][0]['importe_dto']) ? round($resumen[0][0]['importe_dto'],2) : 0);
			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['importe_adebitar'] = (isset($resumen[0][0]['importe_adebitar']) ? round($resumen[0][0]['importe_adebitar'],2) : 0);
			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['importe_adebitar_ok'] = (isset($resumen[1][0]['importe_adebitar']) ? round($resumen[1][0]['importe_adebitar'],2) : 0);
			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['importe_error'] = (isset($resumen[2][0]['importe_adebitar']) ? $resumen[2][0]['importe_adebitar'] : 0);
			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['registros_liquidados'] = (isset($resumen[0][0]['liquidados']) ? $resumen[0][0]['liquidados'] : 0);
			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['cantidad_ok'] = (isset($resumen[1][0]['liquidados']) ? $resumen[1][0]['liquidados'] : 0);
			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['cantidad_errores'] = (isset($resumen[2][0]['liquidados']) ? $resumen[2][0]['liquidados'] : 0);

			$SQL_OK = "	SELECT LiquidacionSocio.documento, LiquidacionSocio.apenom, LiquidacionSocio.cuit_cuil,
							LiquidacionSocio.registro, LiquidacionSocio.ultima_calificacion, 
							LiquidacionSocio.socio_id, LiquidacionSocio.turno_pago, LiquidacionSocio.sucursal, 
							LiquidacionSocio.nro_cta_bco, LiquidacionSocio.cbu, LiquidacionSocio.importe_dto, 
							LiquidacionSocio.importe_adebitar, LiquidacionSocio.intercambio, LiquidacionSocio.error_cbu, 
							LiquidacionSocio.error_intercambio, GlobalDato.concepto_1, GlobalDato.concepto_2, 
							GlobalDato.logico_1 FROM liquidacion_socios AS LiquidacionSocio 
							INNER JOIN global_datos AS GlobalDato ON (LiquidacionSocio.codigo_empresa = GlobalDato.id) 
							WHERE LiquidacionSocio.liquidacion_id = ".$parametros['LiquidacionSocio']['liquidacion_id']." 
							AND LiquidacionSocio.codigo_empresa = '$empresa' 
							AND LiquidacionSocio.turno_pago = '$turno' AND LiquidacionSocio.diskette = 1 
							AND LiquidacionSocio.error_cbu = 0
							ORDER BY GlobalDato.concepto_1 ASC, LiquidacionSocio.apenom ASC, 
							LiquidacionSocio.registro ASC ";
			
			$registrosOK = $this->query($SQL_OK);
			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['registros'] = Set::extract("/LiquidacionSocio",$registrosOK);			
			
			$SQL_ERROR = "	SELECT LiquidacionSocio.documento, LiquidacionSocio.apenom, LiquidacionSocio.cuit_cuil, 
							LiquidacionSocio.registro, LiquidacionSocio.ultima_calificacion, 
							LiquidacionSocio.socio_id, LiquidacionSocio.turno_pago, LiquidacionSocio.sucursal, 
							LiquidacionSocio.nro_cta_bco, LiquidacionSocio.cbu, LiquidacionSocio.importe_dto, 
							LiquidacionSocio.importe_adebitar, LiquidacionSocio.intercambio, LiquidacionSocio.error_cbu, 
							LiquidacionSocio.error_intercambio, GlobalDato.concepto_1, GlobalDato.concepto_2, 
							GlobalDato.logico_1 FROM liquidacion_socios AS LiquidacionSocio 
							INNER JOIN global_datos AS GlobalDato ON (LiquidacionSocio.codigo_empresa = GlobalDato.id) 
							WHERE LiquidacionSocio.liquidacion_id = ".$parametros['LiquidacionSocio']['liquidacion_id']." 
							AND LiquidacionSocio.codigo_empresa = '$empresa' 
							AND LiquidacionSocio.turno_pago = '$turno' AND LiquidacionSocio.diskette = 1 
							AND LiquidacionSocio.error_cbu = 1
							ORDER BY GlobalDato.concepto_1 ASC, LiquidacionSocio.apenom ASC, 
							LiquidacionSocio.registro ASC ";
			
			$registrosERROR = $this->query($SQL_ERROR);
			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['errores'] = Set::extract("/LiquidacionSocio",$registrosERROR);
			
		
		endforeach;
		
//		debug($tmpResumeByTurno);
//		
//		exit;
//		
//		
////		debug($parametros);
//		
//		$tmpResumeByTurno = array();
//		
////		$aTurnos = Set::sort($parametros['LiquidacionSocio']['turno_pago'], '{n}.beneficiario_apenom', 'asc');
//		
//		foreach($parametros['LiquidacionSocio']['turno_pago'] as $turno => $dato):
//		
//			list($cantidad,$impoDto,$impoDebito,$empresaTurnoDesc) = explode("|", $dato);
//			
//			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['descripcion'] = $empresaTurnoDesc;
//			
//			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['importe_dto'] = round($impoDto/100,2);
//			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['importe_adebitar'] = round($impoDebito/100,2);
//			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['cantidad_errores'] = 0;
//			
//			
//			$registrosTurno = Set::extract("/LiquidacionSocio[turno_pago=".$turno."]",$socios);
//			$errores = Set::extract("/LiquidacionSocio[error_cbu=1]",$registrosTurno);
//			$enDiskette = Set::extract("/LiquidacionSocio[error_cbu=0]",$registrosTurno);
//			
//			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['registros_liquidados'] = count($registrosTurno);
//			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['registros'] = $enDiskette;
//			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['cantidad_ok'] = count($enDiskette);
//			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['cantidad_errores'] = count($errores);
//			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['errores'] = $errores;
//			
//		
//		endforeach;
		
		
//		debug($tmpResumeByTurno);
//		exit;
		
		
		$fields = array(
						"
						LiquidacionSocio.id,
						LiquidacionSocio.documento,
						LiquidacionSocio.apenom,
						LiquidacionSocio.registro,
						LiquidacionSocio.ultima_calificacion,
						LiquidacionSocio.socio_id,
						LiquidacionSocio.turno_pago,
						LiquidacionSocio.sucursal,
						LiquidacionSocio.nro_cta_bco,
						LiquidacionSocio.cbu,
						LiquidacionSocio.importe_dto,
						LiquidacionSocio.importe_adebitar,
						LiquidacionSocio.intercambio,
						LiquidacionSocio.error_cbu,
						LiquidacionSocio.error_intercambio,
						GlobalDato.concepto_1,
						GlobalDato.concepto_2,
						GlobalDato.logico_1
						"
		);
		
//        $order = array('GlobalDato.concepto_1,LiquidacionSocio.apenom,LiquidacionSocio.registro');
//        $order = array('LiquidacionSocio.sucursal,LiquidacionSocio.nro_cta_bco,LiquidacionSocio.registro');
        $order = array('LiquidacionSocio.socio_id');
        
		$socios = $this->find('all',array(
										'joins' => array(
												array(
													'table' => 'global_datos',
													'alias' => 'GlobalDato',
													'type' => 'inner',
													'foreignKey' => false,
													'conditions' => array('LiquidacionSocio.codigo_empresa = GlobalDato.id')
													),
										),
										'conditions' => array(
																	'LiquidacionSocio.liquidacion_id' => $parametros['LiquidacionSocio']['liquidacion_id'],
																	'LiquidacionSocio.codigo_empresa' => $empresas,
																	'LiquidacionSocio.turno_pago' => $turnos,
//																	'IFNULL(LiquidacionSocio.importe_adebitar,0) >' => 0,
																	'LiquidacionSocio.diskette' => 1
																),
										'fields' => $fields,																
										'order' => $order,
		));

		if(empty($socios)) return null;		
		
		$resultados['parametros'] = $parametros;
		$resultados['info_procesada'] = $socios;
		$resultados['info_diskette'] = Set::extract("/LiquidacionSocio[error_cbu=0]",$socios);
		$resultados['info_procesada_by_turno'] = $tmpResumeByTurno;
		$resultados['errores'] = Set::extract("/LiquidacionSocio[error_cbu=1]",$socios);
		$resultados['totales'] = unserialize($asinc['Asincrono']['txt2']);
		
//		debug($resultados);
//        exit;
		
		$registros = Set::extract("/LiquidacionSocio[error_cbu=0]/intercambio",$socios);
		$importes = Set::extract("/LiquidacionSocio[error_cbu=0]/importe_adebitar",$socios);
		
		$bancoIntercambio = $parametros['LiquidacionSocio']['banco_intercambio'];
//		$fechaDebito = parent::armaFecha($parametros['LiquidacionSocio']['fecha_debito']);
        $fechaDebito = $parametros['LiquidacionSocio']['fecha_debito'];
		$registrosDiskette = $resultados['totales']['registros_disk'];
		$importeDiskette = $resultados['totales']['importe_disk'];
		$nroArchivo = (isset($parametros['LiquidacionSocio']['nro_archivo']) ? $parametros['LiquidacionSocio']['nro_archivo'] : 1);	
		$fechaPresentacion = $parametros['LiquidacionSocio']['fecha_presentacion'];
		
		$resultados['diskette'] = $oBanco->genDisketteBanco($bancoIntercambio,$fechaDebito,$registrosDiskette,$importeDiskette,$nroArchivo,$registros,$fechaPresentacion,$parametros);

		return $resultados;
		
	}
	
	
	/**
	 * Reprocesa los datos de un archivo en base a los parametros enviados
	 * $params['liquidacion_id']
	 * $params['proveedor_id']
	 * $params['criterio_deuda'] 0 = TODO, 1 => SOLO PERIODO, 2 => PRIMER CUOTA
	 * $params['banco_intercambio']
	 * $params['fecha_debito']
	 * $params['nro_archivo']
	 * $params['tipo_cuota'] => array(tipoCuota1,tipoCuota2,...); null = TODAS
	 * $params['filtro'] => array("codEmpresa1|turnoPago1|codigoStatus1", "codEmpresa2|turnoPago2|codigoStatus2",...)
	 * @param array $params
	 * @param boolean $resumirByTurno RESUMEN LA INFORMACION EN UN ARRAY ASOCIATIVO POR TURNO
	 * @return array 
	 */	
	function reprocesarDisketteCBU($params,$resumirByTurno = false){		
		
		$resultados = array();
		
		$liquidacionID = $params['liquidacion_id'];
		$proveedorId = $params['proveedor_id'];
		$criterioFiltroDeuda = $params['criterio_deuda'];
		$bancoIntercambio = $params['banco_intercambio'];
		$fechaDebito = $params['fecha_debito'];
		$nroArchivo = $params['nro_archivo'];
		$montoCorte = $params['monto_corte'];
        $fechaPresentacion = $params['fecha_presentacion'];

		
		$tipoCuota = array_keys($params['tipo_cuota']);
		$filtro = $params['filtro'];
		
		$tipoCuotaIN = null;
		if(is_array($tipoCuota)){
			$tipoCuotaIN = "'".implode("','", $tipoCuota)."'";
		}
		
		##############################################################################################################################
		# PROCESAR DATOS POR TURNO
		##############################################################################################################################
		if($resumirByTurno):
			if(empty($filtro)) return null;
			
			App::import('Model','config.Banco');
			$oBANCO = new Banco();
			
			$ACUM_REGISTROS = 0;
			$ACUM_IMPORTE_TURNO = $ACUM_IMPORTE_DISK_TURNO = $ACUM_REGISTROS_TURNO = $ACUM_REGISTROS_ERROR_TURNO =  $ACUM_REGISTROS_DISK_TURNO = 0;
			$ACUM_IMPORTE = $ACUM_IMPORTE_DISK = $ACUM_REGISTROS = $ACUM_REGISTROS_ERROR =  $ACUM_REGISTROS_DISK = 0;
			
			$registros = array();
			
			$datos = array();
			
			App::import('Model', 'Mutual.LiquidacionTurno');
			$oTURNO = new LiquidacionTurno();
			$resumeByTurno = array();	
				
			foreach($filtro as $cadenaFiltro):
				
				list($empresa,$turno,$codigo) = explode("|", $cadenaFiltro);
				//ARMO LA CONSULTA EN BASE AL TURNO
				$sql = "SELECT 
				
							LiquidacionSocio.id,
							LiquidacionSocio.codigo_organismo,
							LiquidacionSocio.socio_id,
							LiquidacionSocio.documento,
							LiquidacionSocio.apenom,
							LiquidacionSocio.banco_id,
							LiquidacionSocio.sucursal,
							LiquidacionSocio.nro_cta_bco,
							LiquidacionSocio.cbu,
							LiquidacionSocio.ultima_calificacion,
							LiquidacionSocio.codigo_empresa,
							LiquidacionSocio.turno_pago,					
							(SELECT SUM(saldo_actual) FROM liquidacion_cuotas AS LiquidacionCuota2
								".($criterioFiltroDeuda == 2 ? "INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON
									(
										OrdenDescuentoCuota.id = LiquidacionCuota2.orden_descuento_cuota_id
										AND OrdenDescuentoCuota.nro_cuota = 1
									)" : "")."
								WHERE 
									LiquidacionCuota2.liquidacion_id = $liquidacionID 
									AND LiquidacionCuota2.socio_id = LiquidacionSocio.socio_id
									" . (!empty($proveedorId) ? " AND LiquidacionCuota2.proveedor_id = $proveedorId " : "") . "
									" . (!empty($tipoCuota) && empty($tipoCuotaIN) ? " AND LiquidacionCuota2.tipo_cuota = '$tipoCuota' " : (!empty($tipoCuotaIN) ? " AND LiquidacionCuota2.tipo_cuota IN ($tipoCuotaIN)" : "")) . "
									AND LiquidacionCuota2.para_imputar = 0
									" . ($criterioFiltroDeuda == 0 ? "" : "AND LiquidacionCuota2.periodo_cuota = Liquidacion.periodo") . "
							) AS saldo_actual,
							IFNULL((
								SELECT SUM(importe) FROM orden_descuento_cobro_cuotas AS OrdenDescuentoCobroCuota
								WHERE OrdenDescuentoCobroCuota.orden_descuento_cuota_id = LiquidacionCuota.orden_descuento_cuota_id
							),0) AS importe_cobrado					
						
						FROM liquidacion_socios AS LiquidacionSocio
						
						INNER JOIN liquidacion_cuotas AS LiquidacionCuota ON 
							(
								LiquidacionCuota.liquidacion_id = LiquidacionSocio.liquidacion_id 
								AND LiquidacionCuota.socio_id = LiquidacionSocio.socio_id
							)
						
						INNER JOIN liquidaciones AS Liquidacion ON 
							(
								Liquidacion.id = LiquidacionSocio.liquidacion_id
							)
						
						INNER JOIN liquidacion_socio_rendiciones AS LiquidacionSocioRendicion ON 
							(
								LiquidacionSocioRendicion.liquidacion_id = Liquidacion.id 
								AND LiquidacionSocioRendicion.socio_id = LiquidacionSocio.socio_id
							)
						".($criterioFiltroDeuda == 2 ? "INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON
							(
								OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id
								AND OrdenDescuentoCuota.nro_cuota = 1
							)" : "")."				
						WHERE 
							LiquidacionSocio.liquidacion_id = $liquidacionID
							AND LiquidacionSocio.turno_pago = '$turno'
							AND LiquidacionSocioRendicion.status = '$codigo'
							" . (!empty($proveedorId) ? " AND LiquidacionCuota.proveedor_id = $proveedorId " : "") . "
							" . (!empty($tipoCuota) && empty($tipoCuotaIN) ? " AND LiquidacionCuota.tipo_cuota = '$tipoCuota' " : (!empty($tipoCuotaIN) ? " AND LiquidacionCuota.tipo_cuota IN ($tipoCuotaIN)" : "")) . "
							AND LiquidacionCuota.para_imputar = 0
							" . ($criterioFiltroDeuda == 0 ? "" : "AND LiquidacionCuota.periodo_cuota = Liquidacion.periodo") . "
						
						GROUP BY 
							LiquidacionSocio.socio_id,
							LiquidacionSocio.documento,
							LiquidacionSocio.apenom,
							LiquidacionSocio.banco_id,
							LiquidacionSocio.sucursal,
							LiquidacionSocio.nro_cta_bco,
							LiquidacionSocio.cbu
							
						ORDER BY 
							LiquidacionSocio.apenom;";
				
	//			debug($sql);
				$rows = $this->query($sql);
	
				if(!empty($rows)):
				
					$resumeByTurno['info_procesada_by_turno'][$turno]['descripcion'] = $oTURNO->getDescripcionByTruno($turno);	
					$resumeByTurno['info_procesada_by_turno'][$turno]['registros_liquidados'] = 0;
					$resumeByTurno['info_procesada_by_turno'][$turno]['cantidad_ok'] = 0;
					$resumeByTurno['info_procesada_by_turno'][$turno]['cantidad_errores'] = 0;					
					$resumeByTurno['info_procesada_by_turno'][$turno]['liquidado'] = 0;
					$resumeByTurno['info_procesada_by_turno'][$turno]['importe_adebitar'] = 0;
					$resumeByTurno['info_procesada_by_turno'][$turno]['registros'] = array();
					$resumeByTurno['info_procesada_by_turno'][$turno]['errores'] = array();
					
	
					foreach($rows as $idx => $dato):
					
					
						$dato['LiquidacionSocio']['registro'] = 1;
						$dato['LiquidacionSocio']['importe_liquidado'] = $dato[0]['saldo_actual'];
						
						$importeCobrado = $dato[0]['importe_cobrado'];
						
						//ARMO EL IDENTIFICADOR DE DEBITO
						$idDebito = array();			
						$idDebito['LiquidacionSocio']['socio_id'] = $dato['LiquidacionSocio']['socio_id'];
						$idDebito['LiquidacionSocio']['liquidacion_id'] = $liquidacionID;
						$idDebito['LiquidacionSocio']['registro'] = $dato['LiquidacionSocio']['registro'];
						$dato['LiquidacionSocio']['identificador_debito'] = $this->__genDebitoID($idDebito);
						
						#REGISTRO PARA DISKETTE
						$importe 			= $dato['LiquidacionSocio']['importe_liquidado'];
						$registroNro 		= $dato['LiquidacionSocio']['registro'];
						$idDebito 			= $dato['LiquidacionSocio']['identificador_debito'];
						$liquidacionSocioId = $dato['LiquidacionSocio']['id'];
						$socioId 			= $dato['LiquidacionSocio']['socio_id'];
						$sucursal 			= $dato['LiquidacionSocio']['sucursal'];
						$cuenta 			= $dato['LiquidacionSocio']['nro_cta_bco'];
						$cbu 				= $dato['LiquidacionSocio']['cbu'];
						$codOrganismo 		= $dato['LiquidacionSocio']['codigo_organismo'];
						$calificacion 		= $dato['LiquidacionSocio']['ultima_calificacion'];
						
						$registro = $oBANCO->genRegistroDisketteBanco($bancoIntercambio,$fechaDebito,$importe,$registroNro,$idDebito,$liquidacionSocioId,$socioId,$sucursal,$cuenta,$cbu,$codOrganismo,$calificacion,NULL,$liquidacionID);
						
						
						$dato['LiquidacionSocio']['cadena'] = $registro['cadena'];
						$dato['LiquidacionSocio']['error'] = $registro['error'];
						$dato['LiquidacionSocio']['mensaje'] = $registro['mensaje'];
						$dato['LiquidacionSocio']['importe_adebitar'] = $registro['importe_debito'];
			
						$ACUM_REGISTROS++;
						$ACUM_REGISTROS_TURNO++;
						$ACUM_IMPORTE += $dato['LiquidacionSocio']['importe_liquidado'];
						$ACUM_IMPORTE_TURNO += $dato['LiquidacionSocio']['importe_liquidado'];
						
						$dato['LiquidacionSocio']['banco'] = parent::getNombreBanco($dato['LiquidacionSocio']['banco_id']);
						$dato['LiquidacionSocio']['calificacion'] = parent::GlobalDato('concepto_1', $dato['LiquidacionSocio']['ultima_calificacion']);
						
						$turnoDesc = $oTURNO->getDescripcionByTruno($dato['LiquidacionSocio']['turno_pago']);
						
						$dato['LiquidacionSocio']['empresa'] = $turnoDesc;
						
						$dato['LiquidacionSocio']['sucursal'] = $registro['sucursal_formed'];
						$dato['LiquidacionSocio']['nro_cta_bco'] = $registro['cuenta_formed'];
						
						//control con el monto de corte
						if(!empty($montoCorte) && $montoCorte < $dato['LiquidacionSocio']['importe_adebitar']){
							$dato['LiquidacionSocio']['error'] = 1;
							$dato['LiquidacionSocio']['mensaje'] = "> $montoCorte";
						}
			
						if($importeCobrado != 0){
							if($importeCobrado >= $dato['LiquidacionSocio']['importe_adebitar']){
								$dato['LiquidacionSocio']['error'] = 1;
								$dato['LiquidacionSocio']['mensaje'] = "COBRADO A LA FECHA ($importeCobrado)";
							}else if(($dato['LiquidacionSocio']['importe_adebitar'] - $importeCobrado) > 0){
								//$dato['LiquidacionSocio']['importe_adebitar'] -= $importeCobrado;
								$dato['LiquidacionSocio']['mensaje'] = "COBRADO A LA FECHA ($importeCobrado)";
							}
						}
						
						
						$datos[$idx]['LiquidacionSocio'] = $dato['LiquidacionSocio'];
						
						if($dato['LiquidacionSocio']['error'] == 0){
							$ACUM_REGISTROS_DISK++;
							$ACUM_REGISTROS_DISK_TURNO++;
							$ACUM_IMPORTE_DISK += $dato['LiquidacionSocio']['importe_adebitar'];
							$ACUM_IMPORTE_DISK_TURNO += $dato['LiquidacionSocio']['importe_adebitar'];
							array_push($registros,$dato);
							$resumeByTurno['info_procesada_by_turno'][$turno]['registros'][$idx]['LiquidacionSocio'] = $dato['LiquidacionSocio'];
						}else{
							$resumeByTurno['info_procesada_by_turno'][$turno]['errores'][$idx]['LiquidacionSocio'] = $dato['LiquidacionSocio'];
							$ACUM_REGISTROS_ERROR++;
							$ACUM_REGISTROS_ERROR_TURNO++;
						}				
					
					endforeach;
				
					$resumeByTurno['info_procesada_by_turno'][$turno]['registros_liquidados'] = $ACUM_REGISTROS_TURNO;
					$resumeByTurno['info_procesada_by_turno'][$turno]['cantidad_ok'] = $ACUM_REGISTROS_DISK_TURNO;
					$resumeByTurno['info_procesada_by_turno'][$turno]['cantidad_errores'] = $ACUM_REGISTROS_ERROR_TURNO;					
					$resumeByTurno['info_procesada_by_turno'][$turno]['liquidado'] = $ACUM_IMPORTE_TURNO;
					$resumeByTurno['info_procesada_by_turno'][$turno]['importe_adebitar'] = $ACUM_IMPORTE_DISK_TURNO;
					
					
					$ACUM_IMPORTE_TURNO = $ACUM_IMPORTE_DISK_TURNO = $ACUM_REGISTROS_TURNO = $ACUM_REGISTROS_ERROR_TURNO =  $ACUM_REGISTROS_DISK_TURNO = 0;
					
				endif;
				
				
			endforeach;

			$resultados['parametros'] = $params;
			$resultados['info_procesada'] = $datos;
			$resultados['info_diskette'] = $registros;
			$resultados['info_procesada_by_turno'] = $resumeByTurno;	
			$resultados['totales'] = array(
				'registros' => $ACUM_REGISTROS,
				'liquidado' => $ACUM_IMPORTE,
				'errores' => $ACUM_REGISTROS_ERROR,
				'registros_disk' => $ACUM_REGISTROS_DISK,
				'importe_disk' => $ACUM_IMPORTE_DISK,
				'importe_error' => $ACUM_IMPORTE - $ACUM_IMPORTE_DISK,
			);
			$registros = Set::extract("/LiquidacionSocio[error=0]/cadena",$registros);
			$importes = Set::extract("/LiquidacionSocio[error=0]/importe_adebitar",$registros);
			$resultados['diskette'] = $oBANCO->genDisketteBanco($bancoIntercambio,$fechaDebito,$ACUM_REGISTROS_DISK,$ACUM_IMPORTE_DISK,$nroArchivo,$registros,$fechaPresentacion);
			
			return $resultados;

		endif;	
		
		##############################################################################################################################
		$turnoIN = null;
		$codigoRendIN = null;
		
		$empresa = null;
		$turno = null;
		$codigo = null;
		
		if(is_array($filtro)){

			$aTurno = array();
			$aCodRend = array();
			
			foreach($filtro as $ix => $value){
				
				list($empresa,$turno,$codigo) = explode("|", $value);
				if(!in_array($turno, $aTurno))array_push($aTurno,$turno);
				if(!in_array($codigo, $aCodRend))array_push($aCodRend,$codigo);
				
			}
			
			$turnoIN = "'".implode("','", $aTurno)."'";
			$codigoRendIN = "'".implode("','", $aCodRend)."'";
			
		}else{
			
			list($empresa,$turno,$codigo) = explode("|", $filtro);
			
			$turnoIN = "'".$turno."'";
			$codigoRendIN = "'".$codigo."'";
			
		}
		
//		debug($aTurno);
		
		$params['banco_intercambio_nombre'] = parent::getNombreBanco($bancoIntercambio);
		
		App::import('Model','proveedores.Proveedor');
		$oPROV = new Proveedor();
		$razonSocial = $oPROV->getRazonSocialAndRazonSocialResumida($proveedorId);
		$params['proveedor_razon_social'] = $razonSocial['razon_social'];
		$params['proveedor_razon_social_resumida'] = $razonSocial['razon_social_resumida'];
		
		//$liquidacionID,$codigo_empresa,$turno,$proveedorId,$criterioFiltroDeuda, $bancoIntercambio, $fechaDebito, $nroArchivo		
		
		$resultados['params'] = $params;
		
		$sql = "SELECT 
		
					LiquidacionSocio.id,
                                        LiquidacionSocio.liquidacion_id,
					LiquidacionSocio.codigo_organismo,
					LiquidacionSocio.socio_id,
					LiquidacionSocio.documento,
					LiquidacionSocio.apenom,
					LiquidacionSocio.banco_id,
					LiquidacionSocio.sucursal,
					LiquidacionSocio.nro_cta_bco,
					LiquidacionSocio.cbu,
					LiquidacionSocio.ultima_calificacion,
					LiquidacionSocio.codigo_empresa,
					LiquidacionSocio.turno_pago,	
					(SELECT SUM(saldo_actual) FROM liquidacion_cuotas AS LiquidacionCuota2
						".($criterioFiltroDeuda == 2 ? "INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON
							(
								OrdenDescuentoCuota.id = LiquidacionCuota2.orden_descuento_cuota_id
								AND OrdenDescuentoCuota.nro_cuota = 1
							)" : "")."
						WHERE 
							LiquidacionCuota2.liquidacion_id = $liquidacionID 
							AND LiquidacionCuota2.socio_id = LiquidacionSocio.socio_id
							" . (!empty($proveedorId) ? " AND LiquidacionCuota2.proveedor_id = $proveedorId " : "") . "
							" . (!empty($tipoCuota) && empty($tipoCuotaIN) ? " AND LiquidacionCuota2.tipo_cuota = '$tipoCuota' " : (!empty($tipoCuotaIN) ? " AND LiquidacionCuota2.tipo_cuota IN ($tipoCuotaIN)" : "")) . "
							AND LiquidacionCuota2.para_imputar = 0
							" . ($criterioFiltroDeuda == 0 ? "" : "AND LiquidacionCuota2.periodo_cuota = Liquidacion.periodo") . "
					) AS saldo_actual,
					IFNULL((
						SELECT SUM(importe) FROM orden_descuento_cobro_cuotas AS OrdenDescuentoCobroCuota
						WHERE OrdenDescuentoCobroCuota.orden_descuento_cuota_id = LiquidacionCuota.orden_descuento_cuota_id
					),0) AS importe_cobrado					
				
				FROM liquidacion_socios AS LiquidacionSocio
				
				INNER JOIN liquidacion_cuotas AS LiquidacionCuota ON 
					(
						LiquidacionCuota.liquidacion_id = LiquidacionSocio.liquidacion_id 
						AND LiquidacionCuota.socio_id = LiquidacionSocio.socio_id
					)
				
				INNER JOIN liquidaciones AS Liquidacion ON 
					(
						Liquidacion.id = LiquidacionSocio.liquidacion_id
					)
				
				INNER JOIN liquidacion_socio_rendiciones AS LiquidacionSocioRendicion ON 
					(
						LiquidacionSocioRendicion.liquidacion_id = Liquidacion.id 
						AND LiquidacionSocioRendicion.socio_id = LiquidacionSocio.socio_id
					)
				".($criterioFiltroDeuda == 2 ? "INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON
					(
						OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id
						AND OrdenDescuentoCuota.nro_cuota = 1
					)" : "")."				
				WHERE 
					LiquidacionSocio.liquidacion_id = $liquidacionID
					AND LiquidacionSocio.turno_pago IN ($turnoIN)
					AND LiquidacionSocioRendicion.status IN ($codigoRendIN)
					" . (!empty($proveedorId) ? " AND LiquidacionCuota.proveedor_id = $proveedorId " : "") . "
					" . (!empty($tipoCuota) && empty($tipoCuotaIN) ? " AND LiquidacionCuota.tipo_cuota = '$tipoCuota' " : (!empty($tipoCuotaIN) ? " AND LiquidacionCuota.tipo_cuota IN ($tipoCuotaIN)" : "")) . "
					AND LiquidacionCuota.para_imputar = 0
					" . ($criterioFiltroDeuda == 0 ? "" : "AND LiquidacionCuota.periodo_cuota = Liquidacion.periodo") . "
				
				GROUP BY 
					LiquidacionSocio.socio_id,
					LiquidacionSocio.documento,
					LiquidacionSocio.apenom,
					LiquidacionSocio.banco_id,
					LiquidacionSocio.sucursal,
					LiquidacionSocio.nro_cta_bco,
					LiquidacionSocio.cbu
					
				ORDER BY 
					LiquidacionSocio.apenom;";
		
//		debug($sql);
		$rows = $this->query($sql);
		
		if(empty($rows)) return null;
		
		App::import('Model','config.Banco');
		$oBANCO = new Banco();
		
		$ACUM_REGISTROS = 0;
		$ACUM_IMPORTE = $ACUM_IMPORTE_DISK = $ACUM_REGISTROS = $ACUM_REGISTROS_ERROR =  $ACUM_REGISTROS_DISK = 0;
		
		$registros = array();
		
		$datos = array();
		
		App::import('Model', 'Mutual.LiquidacionTurno');
		$oTURNO = new LiquidacionTurno();	

		
		
		
		foreach($rows as $idx => $dato){
			
			$dato['LiquidacionSocio']['registro'] = 1;
			$dato['LiquidacionSocio']['importe_liquidado'] = $dato[0]['saldo_actual'];
			
			$importeCobrado = $dato[0]['importe_cobrado'];
			
			//ARMO EL IDENTIFICADOR DE DEBITO
			$idDebito = array();			
			$idDebito['LiquidacionSocio']['socio_id'] = $dato['LiquidacionSocio']['socio_id'];
			$idDebito['LiquidacionSocio']['liquidacion_id'] = $liquidacionID;
			$idDebito['LiquidacionSocio']['registro'] = $dato['LiquidacionSocio']['registro'];
			$dato['LiquidacionSocio']['identificador_debito'] = $this->__genDebitoID($idDebito);
			
			#REGISTRO PARA DISKETTE
			$importe 			= $dato['LiquidacionSocio']['importe_liquidado'];
			$registroNro 		= $dato['LiquidacionSocio']['registro'];
			$idDebito 			= $dato['LiquidacionSocio']['identificador_debito'];
			$liquidacionSocioId = $dato['LiquidacionSocio']['id'];
			$socioId 			= $dato['LiquidacionSocio']['socio_id'];
			$sucursal 			= $dato['LiquidacionSocio']['sucursal'];
			$cuenta 			= $dato['LiquidacionSocio']['nro_cta_bco'];
			$cbu 				= $dato['LiquidacionSocio']['cbu'];
			$codOrganismo 		= $dato['LiquidacionSocio']['codigo_organismo'];
			$calificacion 		= $dato['LiquidacionSocio']['ultima_calificacion'];
			$apenom		 		= str_replace(","," ",$dato['LiquidacionSocio']['apenom']);
			$ndoc		 		= $dato['LiquidacionSocio']['documento'];
			$beneficioBancoId       = $dato['LiquidacionSocio']['banco_id'];
                        $liquidacionID 		= $dato['LiquidacionSocio']['liquidacion_id'];
                        $convenioBcoCba     = null;                        
			
			$registro = $oBANCO->genRegistroDisketteBanco($bancoIntercambio,$fechaDebito,$importe,$registroNro,$idDebito,$liquidacionSocioId,$socioId,$sucursal,$cuenta,$cbu,$codOrganismo,$apenom, $ndoc, $calificacion, $beneficioBancoId,$liquidacionID,$convenioBcoCba);
			
			
			$dato['LiquidacionSocio']['cadena'] = $registro['cadena'];
			$dato['LiquidacionSocio']['error'] = $registro['error'];
			$dato['LiquidacionSocio']['mensaje'] = $registro['mensaje'];
			$dato['LiquidacionSocio']['importe_adebitar'] = $registro['importe_debito'];

			$ACUM_REGISTROS++;
			$ACUM_IMPORTE += $dato['LiquidacionSocio']['importe_liquidado'];
			
			$dato['LiquidacionSocio']['banco'] = parent::getNombreBanco($dato['LiquidacionSocio']['banco_id']);
			$dato['LiquidacionSocio']['calificacion'] = parent::GlobalDato('concepto_1', $dato['LiquidacionSocio']['ultima_calificacion']);
			
			$turnoDesc = $oTURNO->getDescripcionByTruno($dato['LiquidacionSocio']['turno_pago']);
			
			$dato['LiquidacionSocio']['empresa'] = $turnoDesc;
			
			$dato['LiquidacionSocio']['sucursal'] = $registro['sucursal_formed'];
			$dato['LiquidacionSocio']['nro_cta_bco'] = $registro['cuenta_formed'];
			
			//control con el monto de corte
			if(!empty($montoCorte) && $montoCorte < $dato['LiquidacionSocio']['importe_adebitar']){
				$dato['LiquidacionSocio']['error'] = 1;
				$dato['LiquidacionSocio']['mensaje'] = "> $montoCorte";
			}

			if($importeCobrado != 0){
				if($importeCobrado >= $dato['LiquidacionSocio']['importe_adebitar']){
					$dato['LiquidacionSocio']['error'] = 1;
					$dato['LiquidacionSocio']['mensaje'] = "COBRADO A LA FECHA ($importeCobrado)";
				}else if(($dato['LiquidacionSocio']['importe_adebitar'] - $importeCobrado) > 0){
					//$dato['LiquidacionSocio']['importe_adebitar'] -= $importeCobrado;
					$dato['LiquidacionSocio']['mensaje'] = "COBRADO A LA FECHA ($importeCobrado)";
				}
			}
			
			
			$datos[$idx]['LiquidacionSocio'] = $dato['LiquidacionSocio'];
			

			if($dato['LiquidacionSocio']['error'] == 0){
				$ACUM_REGISTROS_DISK++;
				$ACUM_IMPORTE_DISK += $dato['LiquidacionSocio']['importe_adebitar'];
				array_push($registros,$dato);
			}else{
				$ACUM_REGISTROS_ERROR++;
			}
			
		}

		$resultados['info_procesada'] = $datos;
		$resultados['totales'] = array(
			'registros' => $ACUM_REGISTROS,
			'liquidado' => $ACUM_IMPORTE,
			'errores' => $ACUM_REGISTROS_ERROR,
			'registros_disk' => $ACUM_REGISTROS_DISK,
			'importe_disk' => $ACUM_IMPORTE_DISK,
			'importe_error' => $ACUM_IMPORTE - $ACUM_IMPORTE_DISK,
		);
		
		$registros = Set::extract("/LiquidacionSocio[error=0]/cadena",$datos);
		$importes = Set::extract("/LiquidacionSocio[error=0]/importe_adebitar",$datos);
		$resultados['diskette'] = $oBANCO->genDisketteBanco($bancoIntercambio,$fechaDebito,$ACUM_REGISTROS_DISK,$ACUM_IMPORTE_DISK,$nroArchivo,$registros,$fechaPresentacion);
		
		return $resultados;
		
	}
	

	/**
	 * Devuelve las cadenas de intercambios generadas para un periodo / organismo dado
	 * @author adrian [02/02/2012]
	 * @param int $socio_id
	 * @param string $periodo
	 * @param string $codigoOrganismo
	 * @return string
	 */
	function getStringIntercambioEmitido($socio_id,$periodo,$codigoOrganismo){
		$str = null;
		$sql = "SELECT LiquidacionSocio.intercambio FROM liquidacion_socios AS LiquidacionSocio
				INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = LiquidacionSocio.liquidacion_id)
				WHERE LiquidacionSocio.socio_id = 	$socio_id
				AND Liquidacion.periodo = '$periodo' AND Liquidacion.codigo_organismo = '$codigoOrganismo'
				AND IFNULL(LiquidacionSocio.intercambio,'0') <> 0";
		$socios = $this->query($sql);
		foreach($socios as $idx => $socio){
			$str .= preg_replace("[\n|\r|\n\r]","",$socio['LiquidacionSocio']['intercambio'])."\n";
		}
		$str = str_replace(" ",".",$str);
		return $str;
	}
	
	/**
	 * Devuelve la cadena de intercambio para un socio / liquidacion
	 * 
	 * @author adrian [02/02/2012]
	 * @param int $socio_id
	 * @param int $liquidacionId
	 * @return string
	 */
	function getStringIntercambioEmitidoByLiquidacion($socio_id,$liquidacionId){
		$str = null;
		$conditions = array();
		$conditions['LiquidacionSocio.socio_id'] = $socio_id;
		$conditions['Liquidacion.liquidacion_id'] = $liquidacionId;
		$conditions['NOT'] = array('IFNULL(LiquidacionSocio.intercambio,"0")' => 0);
		$socios = $this->find('all',array('conditions' => $conditions,'order' => array('LiquidacionSocio.codigo_organismo,LiquidacionSocio.registro')));
		foreach($socios as $idx => $socio){
			$str .= $socio['LiquidacionSocio']['intercambio']."\n";
		}
		return $str;
	}	
	
	
    function getResumenByTurnoByPeriodo($periodo){

        $sql = "select 
                LiquidacionSocio.codigo_organismo,
                LiquidacionSocio.codigo_empresa,
                right(LiquidacionSocio.turno_pago,5) as turno_pago,
                Organismo.concepto_1,
                Empresa.concepto_1,
                (select descripcion from liquidacion_turnos as LiquidacionTurno
                where LiquidacionTurno.codigo_empresa = LiquidacionSocio.codigo_empresa
                and LiquidacionTurno.turno = LiquidacionSocio.turno_pago
                limit 1) as turno,
                count(*) as cant,
                sum(LiquidacionSocio.importe_adebitar) as importe_adebitar
                from liquidacion_socios as LiquidacionSocio
                inner join liquidaciones as Liquidacion on (Liquidacion.id = LiquidacionSocio.liquidacion_id)
                inner join global_datos as Empresa on (Empresa.id = LiquidacionSocio.codigo_empresa)
                inner join global_datos as Organismo on (Organismo.id = LiquidacionSocio.codigo_organismo)
                where Liquidacion.periodo = '$periodo' and substring(LiquidacionSocio.codigo_organismo,9,2) = '22'
                group by 
                LiquidacionSocio.codigo_organismo,
                LiquidacionSocio.codigo_empresa,
                LiquidacionSocio.turno_pago
                order by 
                Organismo.concepto_1,
                Empresa.concepto_1,
                (select descripcion from liquidacion_turnos as LiquidacionTurno
                where LiquidacionTurno.codigo_empresa = LiquidacionSocio.codigo_empresa
                and LiquidacionTurno.turno = LiquidacionSocio.turno_pago
                limit 1);";

        $datos = $this->query($sql);
        return $datos;

    }
        
        
    
    function scoring($liquidacionId,$socioId){
        $this->query("CALL SP_LIQUIDA_DEUDA_SCORING($liquidacionId,$socioId)");
    }
        

    function cargar_scoring_by_socio($socio_id, $limit = 1,$scores = FALSE,$score = FALSE){
//        $sql = "select CodigoOrganismo.concepto_1,
//                Liquidacion.periodo,LiquidacionSocioScore.*
//                from liquidacion_socio_scores LiquidacionSocioScore
//                inner join liquidaciones Liquidacion on (Liquidacion.id = LiquidacionSocioScore.liquidacion_id)
//                inner join global_datos CodigoOrganismo on (CodigoOrganismo.id = Liquidacion.codigo_organismo)
//                where LiquidacionSocioScore.socio_id = $socio_id
//                order by Liquidacion.periodo desc".(!empty($limit) ? " limit $limit;" : "");
        
        $sql = "select CodigoOrganismo.concepto_1,
                Liquidacion.periodo,LiquidacionSocioScore.*,round((LiquidacionSocioScore.`13`/LiquidacionSocioScore.saldo_actual) * 100,2) as porc_13
                ,round((LiquidacionSocioScore.`12`/saldo_actual) * 100,2) as porc_12,
                sum(`09`) as `09`,round((sum(`09`)/sum(saldo_actual)) * 100,2) as porc_09,
                sum(`06`) as `06`,round((sum(`06`)/sum(saldo_actual)) * 100,2) as porc_06,
                sum(`03`) as `03`,round((sum(`03`)/sum(saldo_actual)) * 100,2) as porc_03,
                sum(`00`) as `00`,round((sum(`00`)/sum(saldo_actual)) * 100,2) as porc_00,
                sum(cargos_adicionales) as cargos_adicionales,
                sum(`13`+ `12`+ `09`+ `06`+ `03`+ `00`+cargos_adicionales) as total,
                sum(saldo_actual) as saldo_actual
                from liquidacion_socio_scores  LiquidacionSocioScore
                inner join liquidaciones Liquidacion on (Liquidacion.id = LiquidacionSocioScore.liquidacion_id)
                inner join global_datos CodigoOrganismo on (CodigoOrganismo.id = Liquidacion.codigo_organismo)
                where LiquidacionSocioScore.socio_id = $socio_id group by Liquidacion.periodo 
                order by Liquidacion.periodo desc".(!empty($limit) ? " limit $limit;" : "");    
				
		if($scores){
			$sql = "select trim(Liquidacion.periodo) periodo,ifnull(LiquidacionSocioScore.score,0) score from liquidacion_socio_scores LiquidacionSocioScore 
			inner join liquidaciones Liquidacion on (Liquidacion.id = LiquidacionSocioScore.liquidacion_id)
			where socio_id = $socio_id order by Liquidacion.periodo";
		}
		
		if($score){
			$sql = "select CEILING(sum(score) / count(*)) score from liquidacion_socio_scores where socio_id = $socio_id";
		}

        $datos = $this->query($sql);
        return (!empty($datos) ? $datos : null);        
    }    
    
    
//    function cargarTotalesScoring($liquidacion_id){
//        
//        $sql = "select count(socio_id) as cantidad_socios, sum(`13`) as `13`,round((sum(`13`)/sum(saldo_actual)) * 100,2) as porc_13
//                ,sum(`12`) as `12`,round((sum(`12`)/sum(saldo_actual)) * 100,2) as porc_12,
//                sum(`09`) as `09`,round((sum(`09`)/sum(saldo_actual)) * 100,2) as porc_09,
//                sum(`06`) as `06`,round((sum(`06`)/sum(saldo_actual)) * 100,2) as porc_06,
//                sum(`03`) as `03`,round((sum(`03`)/sum(saldo_actual)) * 100,2) as porc_03,
//                sum(`00`) as `00`,round((sum(`00`)/sum(saldo_actual)) * 100,2) as porc_00,
//                sum(cargos_adicionales) as cargos_adicionales,
//                sum(`13`+ `12`+ `09`+ `06`+ `03`+ `00`+cargos_adicionales) as total,
//                sum(saldo_actual) as saldo_actual
//                from liquidacion_socio_scores  LiquidacionSocioScore
//                inner join liquidaciones Liquidacion on (Liquidacion.id = LiquidacionSocioScore.liquidacion_id)
//                inner join global_datos CodigoOrganismo on (CodigoOrganismo.id = Liquidacion.codigo_organismo)
//                where LiquidacionSocioScore.socio_id = $socio_id
//                order by Liquidacion.periodo desc".(!empty($limit) ? " limit $limit;" : "");
//        $datos = $this->query($sql);
//        return (!empty($datos) ? $datos[0][0] : null);
//
//    }    
    
    
    function getTotalEnviadoDto($liquidacion_id){
        $sql = "select sum(importe_dto) as importe_dto from liquidacion_socios where liquidacion_id = $liquidacion_id;";
        $datos = $this->query($sql);
        if(empty($datos)) return 0;
        return $datos[0][0];
    }
    
    
    function get_cbu_liquidado($liquidacion_id,$socio_id){
        
        $sql = "select b.cbu from liquidacion_socios ls
                inner join persona_beneficios b on b.id = ls.persona_beneficio_id
                where liquidacion_id = $liquidacion_id
                and socio_id = $socio_id
                group by b.cbu limit 1;";
        $datos = $this->query($sql);
        if(empty($datos)) return NULL;
        return $datos[0]['b']['cbu'];        
        
	}
	
	/**
	 * GENENAR LA LIQUIDACION SOCIO EN BASE A UNA LIQUIDACION YA ARMADA POR UN PROCESO ASINCRONO
	 * Y PROCEDIMIENTOS ALMACENADOS
	 */
	function reliquidar_sp($socio_id,$periodo){
		$status = array();
		
		$ERROR = FALSE;
		$pre_imputacion = FALSE;
		
		$status[0] = 0;
		$status[1] = "OK";
		$status[2] = NULL;

		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();	
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();	
			
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();


		$liquiPeriodos = $oLQ->query("select Liquidacion.codigo_organismo,Liquidacion.cerrada,Liquidacion.imputada from liquidaciones AS Liquidacion where periodo = '$periodo'");
		foreach($liquiPeriodos as $liq){
			$status[2] .= " * " . $oLQ->GlobalDato('concepto_1', $liq['Liquidacion']['codigo_organismo']) . ": *** ".($liq['Liquidacion']['cerrada'] == 1 ? "CERRADA" : "ABIERTA")." *** | ";
		}
		$liquidaciones = $oLQ->getLiquidacionesByPeriodo($periodo,FALSE,FALSE,NULL,FALSE,TRUE);		
		if(empty($liquidaciones)){
			$this->Mensaje->error();
			$status[0] = 1;
			$status[1] = "NO EXISTEN LIQUIDACIONES ABIERTAS PARA RELIQUIDAR AL PERIODO INDICADO";
			return $status;				
		}
		foreach($liquidaciones as $liquidacion){

			$organismo = $liquidacion['Liquidacion']['codigo_organismo'];
			$liquidacion_id = $liquidacion['Liquidacion']['id'];
			$pre_imputacion = ($liquidacion['Liquidacion']['sobre_pre_imputacion'] ? TRUE : FALSE);

			$SPCALL = "CALL SP_LIQUIDA_DEUDA('".$periodo."','".$organismo."',0,".($pre_imputacion ? 'TRUE' : 'FALSE').",$socio_id);";
			// debug($SPCALL);

			$this->query($SPCALL);
			if(!empty($this->getDataSource()->error)){
				$status[0] = 1;
				$status[1] = $this->getDataSource()->error;
				return $status;			
			}
	
	
			$SPCALL = "CALL SP_LIQUIDA_ADICIONALES($liquidacion_id,$socio_id);";
			// debug($SPCALL);
			$this->query($SPCALL);
			if(!empty($this->getDataSource()->error)){
				$status[0] = 1;
				$status[1] = $this->getDataSource()->error;
				return $status;			
			}
	
	
			$SPCALL = "CALL SP_LIQUIDA_PUNITORIOS($liquidacion_id,$socio_id);";
			// debug($SPCALL);
			$this->query($SPCALL);
			if(!empty($this->getDataSource()->error)){
				$status[0] = 1;
				$status[1] = $this->getDataSource()->error;
				return $status;			
			}		
	
			$SPCALL = "CALL SP_LIQUIDA_DEUDA_CBU_ACUERDO_DEBITO($liquidacion_id,$socio_id);";
			// debug($SPCALL);
			$this->query($SPCALL);
			if(!empty($this->getDataSource()->error)){
				$status[0] = 1;
				$status[1] = $this->getDataSource()->error;
				return $status;			
			}		
	
	
			$SP_PERIODO = $this->GlobalDato('concepto_4',$organismo);
			$SP_MORA = $this->GlobalDato('concepto_5',$organismo);	
			
			if(!empty($SP_PERIODO)){
				$SPCALL = "CALL $SP_PERIODO($liquidacion_id,$socio_id);";
				// debug($SPCALL);
				$this->query($SPCALL);
				if(!empty($this->getDataSource()->error)){
					$status[0] = 1;
					$status[1] = $this->getDataSource()->error;
					return $status;			
				}
			}
		
			if(!empty($SP_MORA)){
				$SPCALL = "CALL $SP_MORA($liquidacion_id,$socio_id);";
				// debug($SPCALL);
				$this->query($SPCALL);
				if(!empty($this->getDataSource()->error)){
					$status[0] = 1;
					$status[1] = $this->getDataSource()->error;
					return $status;			
				}				
			}


			$SPCALL = "CALL SP_LIQUIDA_DEUDA_REGISTRO_RENUMERA($liquidacion_id,$socio_id);";
			// debug($SPCALL);
			$this->query($SPCALL);
			if(!empty($this->getDataSource()->error)){
				$status[0] = 1;
				$status[1] = $this->getDataSource()->error;
				return $status;			
			}
			
			##############################################################################################
			#PASO 4: BUSCO SI HAY PROCESADO UN ARCHIVO PARA ESTA LIQUIDACION Y LO APLICO
			##############################################################################################
			
			//reproceso el intercambio
			$oLSR->reprocesaIntercambio($liquidacion_id,$socio_id,$organismo);
			
			
//			$cuotas = $oLC->armaImputacion($liquidacion_id,$socio_id);
//			debug($cuotas);

			//DE LA LIQUIDACION SOCIO RENDICION SACO LOS DISTINTOS PROVEEDORES POR
			//SI ME CARGARON UN ARCHIVO PARA IMPUTAR A UN PROVEEDOR ESPECIFICO
// 			$proveedores = $oLSR->find("all",array('conditions' => array("LiquidacionSocioRendicion.liquidacion_id" => $liquidacion_id, "LiquidacionSocioRendicion.socio_id" => $socio_id), "fields" => array("LiquidacionSocioRendicion.proveedor_id"), "group" => array("LiquidacionSocioRendicion.proveedor_id")));
			
			$condProv = array();
			$condProv['LiquidacionSocioRendicion.liquidacion_id'] = $liquidacion_id;
			$condProv['LiquidacionSocioRendicion.socio_id'] = $socio_id;
			$condProv['LiquidacionSocioRendicion.indica_pago'] = 1;
			$fieldsProv = array("LiquidacionSocioRendicion.proveedor_id, sum(LiquidacionSocioRendicion.importe_debitado) as importe_debitado");
			$groupProv = array("LiquidacionSocioRendicion.proveedor_id");
			$ordProv = array("LiquidacionSocioRendicion.proveedor_id ASC");
			
			$proveedores = $oLSR->find("all",array('conditions' => $condProv, "fields" => $fieldsProv, "group" => $groupProv, "order" => $ordProv));

			if(!empty($proveedores)):
				//imputo en base a si un archivo fue vinculado a un proveedor especifico
				foreach($proveedores as $proveedor):
					$cuotas = array();
					if(substr($organismo,8,2) != 77) $cuotas = $oLC->armaImputacion($liquidacion_id,$socio_id,$proveedor['LiquidacionSocioRendicion']['proveedor_id']);
					else $cuotas = $oLC->armaImputacionCJP($liquidacion_id,$socio_id);
					if(!empty($cuotas)):
						if(!$oLC->saveAll($cuotas)){
							$ERROR = TRUE;
							$status[0] = 1;
							$status[1] = "ERROR AL GUARDAR LAS CUOTAS DE LA LIQUIDACION CON LA PRE-IMPUTACION";
							break;
						}
					endif;					
				endforeach;
			else:
				//esquema de imputacion normal
				$cuotas = array();
				if(substr($organismo,8,2) != 77) $cuotas = $oLC->armaImputacion($liquidacion_id,$socio_id);
				else $cuotas = $oLC->armaImputacionCJP($liquidacion_id,$socio_id);
				if(!empty($cuotas)):
					if(!$oLC->saveAll($cuotas)){
						$ERROR = TRUE;
						$status[0] = 1;
						$status[1] = "ERROR AL GUARDAR LAS CUOTAS DE LA LIQUIDACION CON LA PRE-IMPUTACION";
						break;
					}
				endif;				
			endif;			


		}

		return $status;

	}
    
}
?>