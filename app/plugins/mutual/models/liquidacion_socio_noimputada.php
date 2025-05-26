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
class LiquidacionSocioNoimputada extends MutualAppModel{

	/**
	 * Referencia nombre del modelo
	 * @var string
	 */
	var $name = 'LiquidacionSocioNoimputada';
        var $use = 'liquidacion_socio_noimputadas';
	
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
		
		App::import('Model','Mutual.LiquidacionCuotaNoimputada');
		$oLC = new LiquidacionCuotaNoimputada();
		
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

			$periodos = $this->periodosBySocio($socio_id,'ASC');
			
// 			debug($periodos);
			
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
	function periodosBySocio($socio_id,$order='ASC'){
		$periodos = null;
		$this->bindModel(array('belongsTo' => array('Liquidacion')));
		$periodos = $this->find('all',array('conditions' => array('$this->name.socio_id' => $socio_id),'fields' => array('Liquidacion.periodo,LiquidacionSocioNoimputada.socio_id'),'group' => array('Liquidacion.periodo','LiquidacionSocioNoimputada.socio_id'),'order' => array("Liquidacion.periodo $order")));
		$periodos = Set::extract("{n}.Liquidacion.periodo",$periodos);
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
		$liquidacion = 	$this->find('all',array('conditions' => array('Liquidacion.periodo' => $periodo,'LiquidacionSocioNoimputada.socio_id' => $socio_id), 'fields' => array('sum(importe_dto) as importe_dto')));
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
																array('LiquidacionSocioNoimputada.liquidacion_id' => $liquidacion_id,'LiquidacionSocioNoimputada.socio_id' => $socio_id),
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
		$liquidacion = 	$this->find('all',array('conditions' => array('Liquidacion.periodo' => $periodo,'LiquidacionSocioNoimputada.socio_id' => $socio_id), 'fields' => array('sum(importe_adebitar) as importe_adebitar')));
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
		$conditions['LiquidacionSocioNoimputada.socio_id'] = $socio_id;
		if(!empty($codigoOrganismo)) $conditions['Liquidacion.codigo_organismo'] = $codigoOrganismo;
		
		$socios = $this->find('all',array('conditions' => $conditions,'order' => array('LiquidacionSocioNoimputada.codigo_organismo,LiquidacionSocioNoimputada.registro')));
		foreach($socios as $idx => $socio){

			//saco el banco de intercambio
			$banco = parent::getBanco($socio['LiquidacionSocioNoimputada']['banco_intercambio']);
			$socio['LiquidacionSocioNoimputada']['banco_intercambio_desc'] = $banco['Banco']['nombre'];
			$socio['LiquidacionSocioNoimputada']['turno'] = substr(trim($socio['LiquidacionSocioNoimputada']['turno_pago']),-5,5);
			
//			$tipoOrganismo = parent::GlobalDato('concepto_2',$socio['LiquidacionSocioNoimputada']['codigo_organismo']);

			//si tiene generado el registro de intercambio lo concateno en un atributo del modelo para mostrarlo en la vista
			$socio['LiquidacionSocioNoimputada']['intercambio_str'] = "";
			if(!empty($socio['LiquidacionSocioNoimputada']['intercambio'])) $socio['LiquidacionSocioNoimputada']['intercambio_str'] .= $socio['LiquidacionSocioNoimputada']['intercambio'];
			
			$organismo = substr($socio['LiquidacionSocioNoimputada']['codigo_organismo'],8,2);
			
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
									'conditions' => array('LiquidacionSocioNoimputada.liquidacion_id' => $liquidacion_id,'LiquidacionSocioNoimputada.codigo_organismo' => $organismo),
									'order' => array('LiquidacionSocioNoimputada.apenom,LiquidacionSocioNoimputada.codigo_empresa'),
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
		$socio['LiquidacionSocioNoimputada']['beneficio_str'] = $oPB->getStrBeneficio($socio['LiquidacionSocioNoimputada']['persona_beneficio_id']);
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
//		$string = $oPB->getStrBeneficio($socio['LiquidacionSocioNoimputada']['persona_beneficio_id']);
		$ley = $socio['LiquidacionSocioNoimputada']['nro_ley'];
		$nroBeneficio = $socio['LiquidacionSocioNoimputada']['nro_beneficio'];
		$tipo = $socio['LiquidacionSocioNoimputada']['tipo'];
		$subBeneficio = $socio['LiquidacionSocioNoimputada']['sub_beneficio'];
		$porcentaje = $socio['LiquidacionSocioNoimputada']['porcentaje'];
		
		$string = "LEY:$ley | TIPO:$tipo | BENFICIO:$nroBeneficio | SUB-BENEFICIO:$subBeneficio ($porcentaje%)";
		
		$codigo = $socio['LiquidacionSocioNoimputada']['codigo_dto'];
		$subCodigo = $socio['LiquidacionSocioNoimputada']['sub_codigo'];		
		if($conCodDto)$string .= " | CODIGO: $codigo-$subCodigo";
		$beneficio = $oPB->read('acuerdo_debito',$socio['LiquidacionSocioNoimputada']['persona_beneficio_id']);
		if($subCodigo == 1 && $beneficio['PersonaBeneficio']['acuerdo_debito'] != 0){
			$string .= " *** ACUERDO DE DEBITO $ ".number_format($beneficio['PersonaBeneficio']['acuerdo_debito'],2)." ***";
		}
		$socio['LiquidacionSocioNoimputada']['beneficio_str'] = $string;
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
		$codigo = $socio['LiquidacionSocioNoimputada']['codigo_dto'];	
		$nroBeneficio = $socio['LiquidacionSocioNoimputada']['nro_beneficio'];
		$porcentaje = $socio['LiquidacionSocioNoimputada']['porcentaje'];
		if($conCodDto)$string = "BENEFICIO: $nroBeneficio ($porcentaje%) | CODIGO: $codigo";
		else $string = "BENEFICIO: $nroBeneficio ($porcentaje%)";
		$socio['LiquidacionSocioNoimputada']['beneficio_str'] = $string;
//		$socio['LiquidacionSocioNoimputada']['beneficio_str'] = $oPB->getStrBeneficio($socio['LiquidacionSocioNoimputada']['persona_beneficio_id']) . " | CODIGO: $codigo";
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
//									'LiquidacionSocioNoimputada.banco_intercambio' => null,
//									'LiquidacionSocioNoimputada.importe_adebitar' => 0,
									'LiquidacionSocioNoimputada.importe_debitado' => 0, 
									'LiquidacionSocioNoimputada.status' => null,
									'LiquidacionSocioNoimputada.fecha_pago' => null
							),
							array(
								'LiquidacionSocioNoimputada.socio_id' => $socio_id,
								'LiquidacionSocioNoimputada.liquidacion_intercambio_id' => $liquidacion_intercambio_id
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
//									'LiquidacionSocioNoimputada.banco_intercambio' => null,
//									'LiquidacionSocioNoimputada.importe_adebitar' => 0,
									'LiquidacionSocioNoimputada.importe_debitado' => 0, 
									'LiquidacionSocioNoimputada.status' => null,
									'LiquidacionSocioNoimputada.fecha_pago' => null
							),
							array(
								'LiquidacionSocioNoimputada.liquidacion_intercambio_id' => $liquidacion_intercambio_id
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
		
		if(!$noEncontrados)$condiciones = array('LiquidacionSocioNoimputada.liquidacion_id' => $liquidacion_id,'LiquidacionSocioNoimputada.liquidacion_intercambio_id <>' => 0);
		else $condiciones = array('LiquidacionSocioNoimputada.liquidacion_id' => $liquidacion_id,'LiquidacionSocioNoimputada.importe_debitado' => 0);
		
		$bancos = $this->find('all',array(
									'conditions' => $condiciones,
									'fields' => array(
													'LiquidacionSocioNoimputada.banco_intercambio'
												),
									'group' => array(
													'LiquidacionSocioNoimputada.banco_intercambio'
													),			
									'order' => array('LiquidacionSocioNoimputada.banco_intercambio'),
		));
		$bancos = Set::extract($bancos,'{n}.LiquidacionSocioNoimputada.banco_intercambio');
		
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
			$condiciones['LiquidacionSocioNoimputada.banco_intercambio'] = $banco_id;
			$resultados = $this->find('all',array(
										'conditions' => $condiciones,
										'fields' => array(
														'LiquidacionSocioNoimputada.status',
														'COUNT(1) AS cantidad',
														'SUM(importe_dto) AS importe_dto',
														'SUM(importe_adebitar) AS importe_adebitar',
														'SUM(importe_debitado) AS importe_debitado',
														'SUM(importe_imputado) AS importe_imputado'
													),
										'group' => array(
														'LiquidacionSocioNoimputada.status'
														),			
										'order' => array('LiquidacionSocioNoimputada.importe_debitado DESC'),
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
				
				$resumen['LiquidacionSocioNoimputada']['banco_intercambio']	= $banco_id;
				$resumen['LiquidacionSocioNoimputada']['cantidad'] 			= $resumen[0]['cantidad'];
				$resumen['LiquidacionSocioNoimputada']['importe_dto'] 		= $resumen[0]['importe_dto'];
				$resumen['LiquidacionSocioNoimputada']['importe_noenviado']	= $noEnviado;
				$resumen['LiquidacionSocioNoimputada']['importe_adebitar'] 	= $resumen[0]['importe_adebitar'];
				$resumen['LiquidacionSocioNoimputada']['importe_debitado'] 	= $resumen[0]['importe_debitado'];
				$resumen['LiquidacionSocioNoimputada']['importe_imputado'] 	= $resumen[0]['importe_imputado'];
				$resumen['LiquidacionSocioNoimputada']['status_desc'] 		= $oCODIGO->getDescripcionCodigo($banco_id,$resumen['LiquidacionSocioNoimputada']['status']);
				$resumen['LiquidacionSocioNoimputada']['es_codigo_pago'] 		= $oCODIGO->isCodigoPago($banco_id,$resumen['LiquidacionSocioNoimputada']['status']);
				
				
				//calculo los porcentajes
				$CANTIDAD 	= round($ACU_CANTIDAD,2);
				$LIQUIDADO 	= round($ACU_LIQUIDADO,2);
				$NOENVIADO 	= round($ACU_NOENVIADO,2);
				$ADEBITAR 	= round($ACU_ADEBITAR,2);
				$DEBITADO 	= round($ACU_DEBITADO,2);
				$IMPUTADO 	= round($ACU_IMPUTADO,2);				
				
				$resumen['LiquidacionSocioNoimputada']['cantidad_porc'] 			= ($CANTIDAD != 0 ? ($resumen['LiquidacionSocioNoimputada']['cantidad'] / $CANTIDAD) * 100 : 0);
				$resumen['LiquidacionSocioNoimputada']['importe_dto_porc'] 		= ($LIQUIDADO != 0 ? ($resumen['LiquidacionSocioNoimputada']['importe_dto'] / $LIQUIDADO) * 100 : 0);
				$resumen['LiquidacionSocioNoimputada']['importe_noenviado_porc'] 	= ($NOENVIADO != 0 ? ($resumen['LiquidacionSocioNoimputada']['importe_noenviado'] / $NOENVIADO) * 100 : 0);
				
				if($ADEBITAR != 0)$resumen['LiquidacionSocioNoimputada']['importe_adebitar_porc'] 	= ($resumen['LiquidacionSocioNoimputada']['importe_adebitar'] / $ADEBITAR) * 100;
				else $resumen['LiquidacionSocioNoimputada']['importe_adebitar_porc'] = 0;
				
				if($DEBITADO != 0)$resumen['LiquidacionSocioNoimputada']['importe_debitado_porc'] 	= ($resumen['LiquidacionSocioNoimputada']['importe_debitado'] / $DEBITADO) * 100;
				else $resumen['LiquidacionSocioNoimputada']['importe_debitado_porc'] = 0;
				
				if($IMPUTADO != 0)$resumen['LiquidacionSocioNoimputada']['importe_imputado_porc'] 	= ($resumen['LiquidacionSocioNoimputada']['importe_imputado'] / $IMPUTADO) * 100;
				else $resumen['LiquidacionSocioNoimputada']['importe_imputado_porc'] = 0;
				
				$ACU_CANTIDAD_P 	+= $resumen['LiquidacionSocioNoimputada']['cantidad_porc'];
				$ACU_LIQUIDADO_P 	+= $resumen['LiquidacionSocioNoimputada']['importe_dto_porc'];
				$ACU_NOENVIADO_P 	+= $resumen['LiquidacionSocioNoimputada']['importe_noenviado_porc'];
				$ACU_ADEBITAR_P 	+= $resumen['LiquidacionSocioNoimputada']['importe_adebitar_porc'];
				$ACU_DEBITADO_P 	+= $resumen['LiquidacionSocioNoimputada']['importe_debitado_porc'];
				$ACU_IMPUTADO_P 	+= $resumen['LiquidacionSocioNoimputada']['importe_imputado_porc'];
				
				
				$ACU_CANTIDAD_P1 	+= round($resumen['LiquidacionSocioNoimputada']['cantidad_porc'],2);
				$ACU_LIQUIDADO_P1 	+= round($resumen['LiquidacionSocioNoimputada']['importe_dto_porc'],2);
				$ACU_NOENVIADO_P1 	+= round($resumen['LiquidacionSocioNoimputada']['importe_noenviado_porc'],2);
				$ACU_ADEBITAR_P1 	+= round($resumen['LiquidacionSocioNoimputada']['importe_adebitar_porc'],2);
				$ACU_DEBITADO_P1 	+= round($resumen['LiquidacionSocioNoimputada']['importe_debitado_porc'],2);	
				$ACU_IMPUTADO_P1 	+= round($resumen['LiquidacionSocioNoimputada']['importe_imputado_porc'],2);				
				
				$resultados[$idx] = $resumen['LiquidacionSocioNoimputada'];
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
								'LiquidacionSocioNoimputada.liquidacion_id' => $liquidacion_id,
								'LiquidacionSocioNoimputada.status' => $codigo_status,
								'LiquidacionSocioNoimputada.banco_intercambio' => $banco_id,	
								'LiquidacionSocioNoimputada.liquidacion_intercambio_id <>' => 0
		);
		$resultados = $this->find('all',array(
									'conditions' => $condiciones,
									'fields' => array(
										'LiquidacionSocioNoimputada.codigo_organismo',
										'LiquidacionSocioNoimputada.tipo_documento',
										'LiquidacionSocioNoimputada.documento',
										'LiquidacionSocioNoimputada.apenom',
										'LiquidacionSocioNoimputada.persona_beneficio_id',
										'LiquidacionSocioNoimputada.tipo',
										'LiquidacionSocioNoimputada.nro_ley',
										'LiquidacionSocioNoimputada.nro_beneficio',
										'LiquidacionSocioNoimputada.sub_beneficio',
										'LiquidacionSocioNoimputada.porcentaje',
										'LiquidacionSocioNoimputada.codigo_dto',
										'LiquidacionSocioNoimputada.sub_codigo',
										'LiquidacionSocioNoimputada.importe_dto',
										'LiquidacionSocioNoimputada.importe_adebitar',
										'LiquidacionSocioNoimputada.importe_debitado',
										'LiquidacionSocioNoimputada.importe_imputado'
									),
									'order' => array('LiquidacionSocioNoimputada.apenom'),
		));

		foreach($resultados as $idx => $resultado){

			$tipoDocumento = parent::getGlobalDato('concepto_1',$resultado['LiquidacionSocioNoimputada']['tipo_documento']);
			$resultado['LiquidacionSocioNoimputada']['tipo_documento_desc'] = $tipoDocumento['GlobalDato']['concepto_1'];
			
			$resultado['LiquidacionSocioNoimputada']['importe_noenviado'] = $resultado['LiquidacionSocioNoimputada']['importe_dto'] - $resultado['LiquidacionSocioNoimputada']['importe_adebitar'];
			
			$tipoOrganismo = parent::getGlobalDato('concepto_2',$resultado['LiquidacionSocioNoimputada']['codigo_organismo']);
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
								'LiquidacionSocioNoimputada.liquidacion_id' => $liquidacion_id,
								'LiquidacionSocioNoimputada.status' => null,
								'LiquidacionSocioNoimputada.banco_intercambio' => null,	
								'LiquidacionSocioNoimputada.liquidacion_intercambio_id' => 0
		);

		$resultados = $this->find('all',array(
									'conditions' => $condiciones,
									'fields' => array(
										'LiquidacionSocioNoimputada.codigo_organismo',
										'LiquidacionSocioNoimputada.tipo_documento',
										'LiquidacionSocioNoimputada.documento',
										'LiquidacionSocioNoimputada.apenom',
										'LiquidacionSocioNoimputada.persona_beneficio_id',
										'LiquidacionSocioNoimputada.tipo',
										'LiquidacionSocioNoimputada.nro_ley',
										'LiquidacionSocioNoimputada.nro_beneficio',
										'LiquidacionSocioNoimputada.sub_beneficio',
										'LiquidacionSocioNoimputada.porcentaje',
										'LiquidacionSocioNoimputada.codigo_dto',
										'LiquidacionSocioNoimputada.sub_codigo',
										'LiquidacionSocioNoimputada.importe_dto',
										'LiquidacionSocioNoimputada.importe_adebitar',
										'LiquidacionSocioNoimputada.importe_debitado',
										'LiquidacionSocioNoimputada.importe_imputado'
									),
									'order' => array('LiquidacionSocioNoimputada.apenom'),
		));

		foreach($resultados as $idx => $resultado){

			$tipoDocumento = parent::getGlobalDato('concepto_1',$resultado['LiquidacionSocioNoimputada']['tipo_documento']);
			$resultado['LiquidacionSocioNoimputada']['tipo_documento_desc'] = $tipoDocumento['GlobalDato']['concepto_1'];
			
			$resultado['LiquidacionSocioNoimputada']['importe_noenviado'] = $resultado['LiquidacionSocioNoimputada']['importe_dto'] - $resultado['LiquidacionSocioNoimputada']['importe_adebitar'];
			
			$tipoOrganismo = parent::getGlobalDato('concepto_2',$resultado['LiquidacionSocioNoimputada']['codigo_organismo']);
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
			$total += $reintegro['LiquidacionSocioNoimputada']['importe_reintegro'];
			$cantidad++;
			if(isset($reintegro['LiquidacionSocioNoimputada']['importe_anticipado']) && $reintegro['LiquidacionSocioNoimputada']['importe_anticipado'] != 0){
				$total_anticipos += $reintegro['LiquidacionSocioNoimputada']['importe_anticipado'];
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
		
//		$sql = "	select 
//						socio_id,
//						(select 
//							sum(importe_dto) 
//							from 
//								liquidacion_socio_noimputadas as LiquidacionSocioNoimputada
//							where 
//								LiquidacionSocioNoimputada.liquidacion_id = $liquidacion_id and 
//								LiquidacionSocioNoimputada.socio_id = LiquidacionSocioRendicion.socio_id 
//							group by  
//								LiquidacionSocioNoimputada.socio_id
//						)as importe_dto,						
//						sum(importe_debitado) as importe_debitado,
//						(select 
//							sum(importe_debitado) 
//							from 
//								liquidacion_cuota_noimputadas as LiquidacionCuota 
//							where 
//								LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id and 
//								LiquidacionCuotaNoimputada.socio_id = LiquidacionSocioRendicion.socio_id 
//								group by  LiquidacionSocioRendicion.socio_id
//						)as importe_imputado,
//						(select 
//							sum(saldo_actual) 
//							from 
//								liquidacion_cuota_noimputadas as LiquidacionCuota 
//							where 
//								LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id and 
//								LiquidacionCuotaNoimputada.socio_id = LiquidacionSocioRendicion.socio_id 
//								group by  LiquidacionSocioRendicion.socio_id
//						)as saldo_actual, 						 
//						(sum(importe_debitado) - (
//													select 
//														sum(importe_debitado) 
//													from
//														liquidacion_cuota_noimputadas as LiquidacionCuota 
//													where 
//														LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id and 
//														LiquidacionCuotaNoimputada.socio_id = LiquidacionSocioRendicion.socio_id 
//														group by  LiquidacionSocioRendicion.socio_id
//													)
//						) as importe_reintegro
//					from 
//						liquidacion_socio_rendiciones as LiquidacionSocioRendicion
//					where 
//						LiquidacionSocioRendicion.liquidacion_id = $liquidacion_id and 
//						LiquidacionSocioRendicion.socio_id <> 0 and 
//						LiquidacionSocioRendicion.indica_pago = 1
//					group by 
//						socio_id 
//					having 
//						importe_debitado > importe_imputado
//					UNION
//					select
//						socio_id,
//						(select 
//							sum(importe_dto) 
//							from 
//								liquidacion_socio_noimputadas as LiquidacionSocioNoimputada 
//							where 
//								LiquidacionSocioNoimputada.liquidacion_id = $liquidacion_id and 
//								LiquidacionSocioNoimputada.socio_id = LiquidacionSocioRendicion.socio_id 
//							group by  
//								LiquidacionSocioNoimputada.socio_id
//						)as importe_dto,						
//						sum(importe_debitado) as importe_debitado,
//						0 as importe_imputado,
//						0 as saldo_actual, 						 
//						sum(importe_debitado) as importe_reintegro
//					from 
//						liquidacion_socio_rendiciones as LiquidacionSocioRendicion
//					where 
//						LiquidacionSocioRendicion.liquidacion_id = $liquidacion_id and 
//						LiquidacionSocioRendicion.socio_id <> 0 and 
//						LiquidacionSocioRendicion.indica_pago = 1 and
//						LiquidacionSocioRendicion.socio_id not in (select socio_id from liquidacion_cuota_noimputadas where liquidacion_id = $liquidacion_id)
//					group by 
//						socio_id 
//					having 
//						importe_debitado > importe_imputado";
		
		
		$sql = "SELECT 
					socio_id,
					(SELECT 
						SUM(importe_dto) 
						FROM 
							liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada 
						WHERE 
							LiquidacionSocioNoimputada.liquidacion_id = $liquidacion_id AND 
							LiquidacionSocioNoimputada.socio_id = LiquidacionSocioRendicion.socio_id 
						GROUP BY  
							LiquidacionSocioNoimputada.socio_id
					)AS importe_dto,						
					SUM(importe_debitado) AS importe_debitado,
					(SELECT 
						SUM(importe_debitado) 
						FROM 
							liquidacion_cuota_noimputadas AS LiquidacionCuota 
						WHERE 
							LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id AND 
							LiquidacionCuotaNoimputada.socio_id = LiquidacionSocioRendicion.socio_id 
							GROUP BY  LiquidacionSocioRendicion.socio_id
					)AS importe_imputado,
					(SELECT 
						SUM(saldo_actual) 
						FROM 
							liquidacion_cuota_noimputadas AS LiquidacionCuota 
						WHERE 
							LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id AND 
							LiquidacionCuotaNoimputada.socio_id = LiquidacionSocioRendicion.socio_id 
							GROUP BY  LiquidacionSocioRendicion.socio_id
					)AS saldo_actual, 						 
					(SUM(importe_debitado) - (
												SELECT 
													SUM(importe_debitado) 
												FROM
													liquidacion_cuota_noimputadas AS LiquidacionCuota 
												WHERE 
													LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id AND 
													LiquidacionCuotaNoimputada.socio_id = LiquidacionSocioRendicion.socio_id 
													GROUP BY  LiquidacionSocioRendicion.socio_id
												)
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
							liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada 
						WHERE 
							LiquidacionSocioNoimputada.liquidacion_id = $liquidacion_id AND 
							LiquidacionSocioNoimputada.socio_id = LiquidacionSocioRendicion.socio_id 
						GROUP BY  
							LiquidacionSocioNoimputada.socio_id
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
				LiquidacionSocioRendicion.socio_id NOT IN (SELECT socio_id FROM liquidacion_cuota_noimputadas WHERE liquidacion_id = $liquidacion_id)
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
							liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada 
						WHERE 
							LiquidacionSocioNoimputada.liquidacion_id = $liquidacion_id AND 
							LiquidacionSocioNoimputada.socio_id = LiquidacionSocioRendicion.socio_id 
						GROUP BY  
							LiquidacionSocioNoimputada.socio_id
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
		
		if(empty($resultados)) return null;
		
		if(!$armaDatos) return $resultados;
		
		App::import('Model','Pfyj.Socio');
		$oSOCIO = new Socio();
		
		App::import('Model','Pfyj.SocioReintegroPago');
		$oREINTEGRO_PAGO = new SocioReintegroPago();		
		
		App::import('Model', 'Mutual.LiquidacionTurno');
		$oTURNO = new LiquidacionTurno();		
		
		foreach($resultados as $idx => $resultado):
		
//			debug($resultado);
			
			$resultado['LiquidacionSocioNoimputada']['socio_id'] = $resultado[0]['socio_id'];
			$resultado['LiquidacionSocioNoimputada']['importe_debitado'] = $resultado[0]['importe_debitado'];
			$resultado['LiquidacionSocioNoimputada']['importe_imputado'] = $resultado[0]['importe_imputado'];
			$resultado['LiquidacionSocioNoimputada']['importe_reintegro'] = $resultado[0]['importe_reintegro'];
			$resultado['LiquidacionSocioNoimputada']['importe_dto'] = $resultado[0]['importe_dto'];
			$resultado['LiquidacionSocioNoimputada']['saldo_actual'] = $resultado[0]['saldo_actual'];		

			$resultado['LiquidacionSocioNoimputada']['importe_anticipado'] = $resultado[0]['reintegro_anticipado'];
			
			//CALCULAR LOS PAGOS DEL REINTEGRO POR SOCIO Y POR LIQUIDACION_ID
			$resultado['LiquidacionSocioNoimputada']['importe_pagado_socio'] = $oREINTEGRO_PAGO->getTotalPagoByLiquidacionSocioId($resultado['LiquidacionSocioNoimputada']['socio_id'], $liquidacion_id);
			
			
//			$resultado['LiquidacionSocioNoimputada']['saldo_reintegro'] = round($resultado[0]['importe_reintegro'] - $resultado[0]['reintegro_anticipado'],2);
			$resultado['LiquidacionSocioNoimputada']['saldo_reintegro'] = round($resultado[0]['importe_reintegro'] - $resultado['LiquidacionSocioNoimputada']['importe_pagado_socio'],2);
			
			//cargo los datos adicionales
			$liquiSocio = $this->find('all',array('conditions' => 
																	array(
																			'LiquidacionSocioNoimputada.liquidacion_id' => $liquidacion_id,
																			'LiquidacionSocioNoimputada.socio_id' => $resultado['LiquidacionSocioNoimputada']['socio_id']
																	),
													'limit' => 1				
												)
			);
			
            
            $resultado['LiquidacionSocioNoimputada']['codigo_organismo'] = "";
            $resultado['LiquidacionSocioNoimputada']['tipo_documento'] = "";
            $resultado['LiquidacionSocioNoimputada']['documento'] = "";
            $resultado['LiquidacionSocioNoimputada']['apenom'] = "";
            $resultado['LiquidacionSocioNoimputada']['persona_beneficio_id'] = "";
            $resultado['LiquidacionSocioNoimputada']['tipo'] = "";
            $resultado['LiquidacionSocioNoimputada']['nro_ley'] = "";
            $resultado['LiquidacionSocioNoimputada']['nro_beneficio'] = "";
            $resultado['LiquidacionSocioNoimputada']['sub_beneficio'] = "";
            $resultado['LiquidacionSocioNoimputada']['porcentaje'] = "";
            $resultado['LiquidacionSocioNoimputada']['codigo_dto'] = "";
            $resultado['LiquidacionSocioNoimputada']['sub_codigo'] = "";
            $resultado['LiquidacionSocioNoimputada']['importe_adebitar'] = "";

            $resultado['LiquidacionSocioNoimputada']['tipo_documento_desc'] = parent::GlobalDato('concepto_1',$resultado['LiquidacionSocioNoimputada']['tipo_documento']);

            $resultado['LiquidacionSocioNoimputada']['importe_noenviado'] = $resultado['LiquidacionSocioNoimputada']['importe_dto'] - $resultado['LiquidacionSocioNoimputada']['importe_adebitar'];

            $resultado['LiquidacionSocioNoimputada']['codigo_empresa'] = "";
            $resultado['LiquidacionSocioNoimputada']['codigo_empresa_desc'] = parent::GlobalDato('concepto_1',$resultado['LiquidacionSocioNoimputada']['codigo_empresa']);
            $resultado['LiquidacionSocioNoimputada']['turno_pago'] = "";
            $resultado['LiquidacionSocioNoimputada']['turno_pago_desc'] = $oTURNO->getDescripcionByTruno($resultado['LiquidacionSocioNoimputada']['turno_pago']);
            
            
			if(!empty($liquiSocio)):
			
				$resultado['LiquidacionSocioNoimputada']['codigo_organismo'] = $liquiSocio[0]['LiquidacionSocioNoimputada']['codigo_organismo'];
				$resultado['LiquidacionSocioNoimputada']['tipo_documento'] = $liquiSocio[0]['LiquidacionSocioNoimputada']['tipo_documento'];
				$resultado['LiquidacionSocioNoimputada']['documento'] = $liquiSocio[0]['LiquidacionSocioNoimputada']['documento'];
				$resultado['LiquidacionSocioNoimputada']['apenom'] = $liquiSocio[0]['LiquidacionSocioNoimputada']['apenom'];
				$resultado['LiquidacionSocioNoimputada']['persona_beneficio_id'] = $liquiSocio[0]['LiquidacionSocioNoimputada']['persona_beneficio_id'];
				$resultado['LiquidacionSocioNoimputada']['tipo'] = $liquiSocio[0]['LiquidacionSocioNoimputada']['tipo'];
				$resultado['LiquidacionSocioNoimputada']['nro_ley'] = $liquiSocio[0]['LiquidacionSocioNoimputada']['nro_ley'];
				$resultado['LiquidacionSocioNoimputada']['nro_beneficio'] = $liquiSocio[0]['LiquidacionSocioNoimputada']['nro_beneficio'];
				$resultado['LiquidacionSocioNoimputada']['sub_beneficio'] = $liquiSocio[0]['LiquidacionSocioNoimputada']['sub_beneficio'];
				$resultado['LiquidacionSocioNoimputada']['porcentaje'] = $liquiSocio[0]['LiquidacionSocioNoimputada']['porcentaje'];
				$resultado['LiquidacionSocioNoimputada']['codigo_dto'] = $liquiSocio[0]['LiquidacionSocioNoimputada']['codigo_dto'];
				$resultado['LiquidacionSocioNoimputada']['sub_codigo'] = $liquiSocio[0]['LiquidacionSocioNoimputada']['sub_codigo'];
				$resultado['LiquidacionSocioNoimputada']['importe_adebitar'] = $liquiSocio[0]['LiquidacionSocioNoimputada']['importe_adebitar'];
				
//				$resultado['LiquidacionSocioNoimputada']['tipo_documento_desc'] = parent::GlobalDato('concepto_1',$resultado['LiquidacionSocioNoimputada']['tipo_documento']);
//				
//				$resultado['LiquidacionSocioNoimputada']['importe_noenviado'] = $resultado['LiquidacionSocioNoimputada']['importe_dto'] - $resultado['LiquidacionSocioNoimputada']['importe_adebitar'];
//				
				$resultado['LiquidacionSocioNoimputada']['codigo_empresa'] = $liquiSocio[0]['LiquidacionSocioNoimputada']['codigo_empresa'];
//				$resultado['LiquidacionSocioNoimputada']['codigo_empresa_desc'] = parent::GlobalDato('concepto_1',$resultado['LiquidacionSocioNoimputada']['codigo_empresa']);
				$resultado['LiquidacionSocioNoimputada']['turno_pago'] = $liquiSocio[0]['LiquidacionSocioNoimputada']['turno_pago'];
//				$resultado['LiquidacionSocioNoimputada']['turno_pago_desc'] = $oTURNO->getDescripcionByTruno($resultado['LiquidacionSocioNoimputada']['turno_pago']);
				
				$tipoOrganismo = substr($resultado['LiquidacionSocioNoimputada']['codigo_organismo'],8,2);
				
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
	
				
			elseif(!empty($resultado['LiquidacionSocioNoimputada']['socio_id'])):
			
				//la liquidacion del socio no existe, sacar datos para el reintegro
				$socio = $oSOCIO->read(null,$resultado['LiquidacionSocioNoimputada']['socio_id']);
				$resultado['LiquidacionSocioNoimputada']['tipo_documento'] = $socio['Persona']['tipo_documento'];
				$resultado['LiquidacionSocioNoimputada']['documento'] = $socio['Persona']['documento'];
				$resultado['LiquidacionSocioNoimputada']['apenom'] = $socio['Persona']['apellido'].', '.$socio['Persona']['nombre'];
				$resultado['LiquidacionSocioNoimputada']['beneficio_str'] = "ERROR: *** NO EXISTE LIQUIDACION PARA ESTE SOCIO ***";
				$resultado['LiquidacionSocioNoimputada']['importe_adebitar'] = 0;
				$resultado['LiquidacionSocioNoimputada']['importe_noenviado'] = 0;
			
                        else:        
                            
                            $resultado['LiquidacionSocioNoimputada']['tipo_documento'] = "";
                            $resultado['LiquidacionSocioNoimputada']['documento'] = "";
                            $resultado['LiquidacionSocioNoimputada']['apenom'] = "";                            
                            $resultado['LiquidacionSocioNoimputada']['beneficio_str'] = "ERROR: *** PERSONA DESCONOCIDA ***";           
			endif;
            
            $resultado['LiquidacionSocioNoimputada']['banco_intercambio'] = $resultado[0]['banco_intercambio'];
            $resultado['LiquidacionSocioNoimputada']['banco_intercambio_nombre'] = $resultado[0]['nombre'];            
			
			$resultados[$idx] = $resultado;
			
		endforeach;
		
//		exit;

//		debug($resultados);
//		exit;

		$resultados = Set::extract("/LiquidacionSocioNoimputada",$resultados);
		$resultados = Set::sort($resultados,"{n}.LiquidacionSocioNoimputada.apenom","asc");
		
		return $resultados;
		
	
	}		
	
	/**
	 * Total Primer Descuento No Cobrado
	 * Devuelve el total (registros y cantidad) de las liquidaciones de socios
	 * que son altas y que no se descontaron
	 * @param $liquidacion_id
	 * @return array
	 */
	function totalPrimerDtoNoCobrado($liquidacion_id){
		$total = array();
		$noCobrados = $this->primerDtoNoCobrado($liquidacion_id);
		if(empty($noCobrados)) return 0;
		$cantidad = 0;
		$total = 0;
		foreach($noCobrados as $noCobrado){
			$cantidad += 1;
			$total += $noCobrado['LiquidacionSocioNoimputada']['importe_dto'];
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
		$total = array();
		$registros = $this->liquidadosNoRendidosEnArchivos($liquidacion_id,false,$soloEnviados,$cjp,$subCodCjp);

		if(empty($registros)) return 0;
		$cantidad = 0;
		$total = 0;
		foreach($registros as $registro){
			$cantidad += 1;
			$total += $registro['LiquidacionSocioNoimputada']['importe_adebitar'];
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
		
		$resultados = $this->find('all', compact('conditions'),null,"LiquidacionSocioNoimputada.apenom");
		
		if(empty($resultados)) return null;
		
		if(!$armaDatos) return $resultados;
		
		foreach($resultados as $idx => $resultado){

			$tipoDocumento = parent::getGlobalDato('concepto_1',$resultado['LiquidacionSocioNoimputada']['tipo_documento']);
			$resultado['LiquidacionSocioNoimputada']['tipo_documento_desc'] = $tipoDocumento['GlobalDato']['concepto_1'];
			
			$resultado['LiquidacionSocioNoimputada']['importe_noenviado'] = $resultado['LiquidacionSocioNoimputada']['importe_dto'] - $resultado['LiquidacionSocioNoimputada']['importe_adebitar'];
			
			$tipoOrganismo = parent::getGlobalDato('concepto_2',$resultado['LiquidacionSocioNoimputada']['codigo_organismo']);
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
					LiquidacionSocioNoimputada.documento,
					LiquidacionSocioNoimputada.tipo_documento,
					LiquidacionSocioNoimputada.apenom,
					LiquidacionSocioNoimputada.persona_beneficio_id,
					SUM(LiquidacionSocioNoimputada.importe_dto) AS importe_dto,
					SUM(LiquidacionSocioNoimputada.importe_adebitar) AS importe_adebitar 
				FROM 
				liquidacion_socio_noimputadas AS  LiquidacionSocio
				WHERE LiquidacionSocioNoimputada.liquidacion_id = $liquidacion_id
				AND LiquidacionSocioNoimputada.socio_id NOT IN
				(SELECT socio_id FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion
				WHERE LiquidacionSocioRendicion.liquidacion_id = LiquidacionSocioNoimputada.liquidacion_id)
				AND IFNULL(LiquidacionSocioNoimputada.intercambio,0) <> 0 
				GROUP BY
					LiquidacionSocioNoimputada.documento,
					LiquidacionSocioNoimputada.tipo_documento,
					LiquidacionSocioNoimputada.apenom,
					LiquidacionSocioNoimputada.persona_beneficio_id
				ORDER BY 
					LiquidacionSocioNoimputada.apenom";
		
		if($cjp):
			$sql = "SELECT 
						LiquidacionSocioNoimputada.documento,
						LiquidacionSocioNoimputada.tipo_documento,
						LiquidacionSocioNoimputada.apenom,
						LiquidacionSocioNoimputada.persona_beneficio_id,
						".($subCodCjp == 1 ? "LiquidacionSocioNoimputada.orden_descuento_id," : "")."
						SUM(LiquidacionSocioNoimputada.importe_dto) AS importe_dto,
						SUM(LiquidacionSocioNoimputada.importe_adebitar) AS importe_adebitar 
					FROM 
					liquidacion_socio_noimputadas AS  LiquidacionSocio
					WHERE LiquidacionSocioNoimputada.liquidacion_id = $liquidacion_id
					AND LiquidacionSocioNoimputada.sub_codigo = '$subCodCjp'
					AND LiquidacionSocioNoimputada.".($subCodCjp == 0 ? "socio_id" : "orden_descuento_id")." NOT IN
					(SELECT ".($subCodCjp == 0 ? "socio_id" : "orden_descuento_id")." FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion
					WHERE LiquidacionSocioRendicion.liquidacion_id = LiquidacionSocioNoimputada.liquidacion_id
					AND LiquidacionSocioRendicion.sub_codigo = LiquidacionSocioNoimputada.sub_codigo)
					AND IFNULL(LiquidacionSocioNoimputada.intercambio,0) <> 0 
					GROUP BY
						LiquidacionSocioNoimputada.documento,
						LiquidacionSocioNoimputada.tipo_documento,
						LiquidacionSocioNoimputada.apenom,
						LiquidacionSocioNoimputada.persona_beneficio_id
						".($subCodCjp == 1 ? ",LiquidacionSocioNoimputada.orden_descuento_id" : "")."
					ORDER BY 
						LiquidacionSocioNoimputada.apenom";		
		
		endif;
		
//		debug($sql);
//		exit;
		
		$resultados = $this->query($sql);
		
		
		if(empty($resultados)) return null;
		
		foreach($resultados as $idx => $resultado):
		
			$resultado['LiquidacionSocioNoimputada']['importe_dto'] = $resultado[0]['importe_dto'];
			$resultado['LiquidacionSocioNoimputada']['importe_adebitar'] = $resultado[0]['importe_adebitar'];
		
			$resultados[$idx] = $resultado;
			
		endforeach;
		
		
		
		if(!$armaDatos) return $resultados;		
		
		App::import('Model','Pfyj.PersonaBeneficio');
		$oPB = new PersonaBeneficio();
				
		
		foreach($resultados as $idx => $resultado):
		
			$resultado['LiquidacionSocioNoimputada']['tipo_documento_desc'] = parent::GlobalDato('concepto_1',$resultado['LiquidacionSocioNoimputada']['tipo_documento']);
			$resultado['LiquidacionSocioNoimputada']['beneficio_str'] = $oPB->getStrBeneficio($resultado['LiquidacionSocioNoimputada']['persona_beneficio_id']);
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
		App::import('Model','Mutual.LiquidacionCuotaNoimputada');
		$oLC = new LiquidacionCuotaNoimputada();	
			
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();		
		
		
		$status[0] = 0;
		$status[1] = "";
		$status[2] = "";
		
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
        $CONTROL_NACION = false;
        $BANCO_CONTROL = null;
        
        $file = parse_ini_file(CONFIGS.'mutual.ini', true);
        if(isset($file['general']['banco_nacion_debito_periodo']) && $file['general']['banco_nacion_debito_periodo'] != ""){
            $CONTROL_NACION = true;
            $BANCO_CONTROL = $file['general']['banco_nacion_debito_periodo'];
        }          
        
        $LIQUIDA_DEUDA_CBU_SP = FALSE;
        // if(isset($file['general']['sp_liquida_deuda_cbu']) && $file['general']['sp_liquida_deuda_cbu'] == "1"){
        //     $LIQUIDA_DEUDA_CBU_SP = false;
        // }
        
        $DISCRIMINA_PERMANENTES = false;
        if(isset($file['general']['discrimina_conceptos_permanentes_orden_debito']) && $file['general']['discrimina_conceptos_permanentes_orden_debito'] == "1"){
            $DISCRIMINA_PERMANENTES = true;
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
                
                if(substr($organismo,8,2) == "22" && $LIQUIDA_DEUDA_CBU_SP){
                    $this->query("CALL SP_LIQUIDA_DEUDA_CBU($socio_id,'$periodo','$organismo',".($preImputa == 1 ? 'TRUE':'FALSE').")");
                }else{
                    $statusLiqui = $this->liquidar($socio_id,$periodo,$organismo,$liquidacion_id,$generaLiqSocio,$preImputa,$liqSiTieneDeuda,$CONTROL_NACION,$BANCO_CONTROL,$DISCRIMINA_PERMANENTES);
                    if(isset($statusLiqui[0]) && $statusLiqui[0] == 1):
                        $status[0] = $statusLiqui[0];
                        $status[1] = $statusLiqui[1];
                        $ERROR = TRUE;
                        break;
                    endif;
                }
                
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
		$socio_id = $liquidacionSocio['LiquidacionSocioNoimputada']['socio_id'];
		
		#controlar que no tenga cuotas devengadas anteriores al periodo que se pasa por parametro
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCuota = new OrdenDescuentoCuota();

		$cantidad = $oCuota->find('count',array('conditions' => array('OrdenDescuentoCuota.socio_id' => $socio_id, 'OrdenDescuentoCuota.periodo <' => $periodo)));
		if($cantidad == 0) $liquidacionSocio['LiquidacionSocioNoimputada']['alta'] = 1;
		else $liquidacionSocio['LiquidacionSocioNoimputada']['alta'] = 0;
		
		//marcar la ultima calificacion
		App::import('Model', 'Pfyj.Socio');
		$oSOCIO = new Socio(null);
		
		$calificacion = $oSOCIO->getUltimaCalificacion($liquidacionSocio['LiquidacionSocioNoimputada']['socio_id'],$liquidacionSocio['LiquidacionSocioNoimputada']['persona_beneficio_id']);
		$liquidacionSocio['LiquidacionSocioNoimputada']['ultima_calificacion'] = $calificacion;				
		
		return $this->save($liquidacionSocio);
		
	}
	
//	function procesarPreImputacion($socio_id,$liquidacion_id,$actualizaSaldoCuota=true){
//		App::import('Model','Mutual.LiquidacionCuotaNoimputada');
//		$oLC = new LiquidacionCuotaNoimputada();
//		$cuotas = array();
//		$cuotas['LiquidacionCuotaNoimputada'] = $oLC->armaImputacion($this->liquidacionID,$socio_id);
//		return $oLC->saveAll($cuotas);
		
//		debug($cuotasPendientesImputar);
//		debug($ACUM_IMPUTADO);
//		EXIT;
//	}
	
	/**
	 * @deprecated
	 * Este metodo no se utiliza mas porque se modifico todo el esquema de procesamiento del archivo de intercambio
	 * Se usa el metodo armaImputacion() del modelo LiquidacionCuotaNoimputada.  Este metodo devuelve un array con las cuotas
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
	 * y al socio (metodo cuotasPendientesDeImputar() del modelo LiquidacionCuotaNoimputada.
	 * Una vez que cargo los datos en la cabecera de la liquidacion del socio, con el importe debitado si indica pago el codigo
	 * de status recorre el array de cuotas y va imputando.  Este proceso se hace por todas las liquidaciones del socio que tenga
	 * una vez terminado, el array de cuotas liquidadas tiene la distribucion del importe debitado y se guarda en la liquidacion_cuota_noimputadas
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
		
		App::import('Model','Mutual.LiquidacionCuotaNoimputada');
		$oLC = new LiquidacionCuotaNoimputada();

		$oLC->updateAll(
							array('LiquidacionCuotaNoimputada.importe_debitado' => 0,'LiquidacionCuotaNoimputada.liquidacion_intercambio_id' => 0),
							array(
									'LiquidacionCuotaNoimputada.liquidacion_id' => $liquidacion_id,
									'LiquidacionCuotaNoimputada.socio_id' => $socio_id,
							)
		);			
		
		
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();		
		
		$liquidacion = $oLQ->read('codigo_organismo,imputada',$liquidacion_id);
		$organismo = $liquidacion['Liquidacion']['codigo_organismo'];
		
		$camposIgualables = $oDSGN->getCamposIgualables($organismo);

		$liquidacionSocios = $this->find('all',array('conditions' => array(
																'LiquidacionSocioNoimputada.liquidacion_id' => $liquidacion_id,
																'LiquidacionSocioNoimputada.socio_id' => $socio_id
															),
										'order' => array('LiquidacionSocioNoimputada.registro')																			
		));
		
		#PASO 1) RECORRO LA LIQUIDACION SOCIOS PARA ARMAR LOS DATOS DE LOS NO COBRADOS (CASO CBU)
		foreach($liquidacionSocios as $idx => $liquidacionSocio):
			$liquidacionSocio['LiquidacionSocioNoimputada']['importe_debitado'] = 0;
			$liquidacionSocio['LiquidacionSocioNoimputada']['importe_imputado'] = 0;
			$datosIntercambio = $oRegistroProcesado->getDatosIntercambio($liquidacionSocio,0);
			
			if(!empty($datosIntercambio)):
				$liquidacionSocio['LiquidacionSocioNoimputada']['indica_pago'] = $datosIntercambio['indica_pago'];
				$liquidacionSocio['LiquidacionSocioNoimputada']['liquidacion_intercambio_id'] = $datosIntercambio['id'];
				$liquidacionSocio['LiquidacionSocioNoimputada']['status'] = $datosIntercambio['status'];
				$liquidacionSocio['LiquidacionSocioNoimputada']['fecha_pago'] = $datosIntercambio['fecha_pago'];
				$liquidacionSocio['LiquidacionSocioNoimputada']['banco_intercambio'] = $datosIntercambio['banco_intercambio'];
				$liquidacionSocios[$idx] = $liquidacionSocio;
			endif;
						
		endforeach;
		
		
		#PASO 2) RECORRO LA LIQUIDACION SOCIOS PARA ARMAR LOS DATOS DEL DEBITO (LOS COBRADOS)
		$DEBITADO_SOCIO = 0;

		foreach($liquidacionSocios as $idx => $liquidacionSocio):
		
			#PARA LA LIQUIDACION DEL SOCIO, TRAIGO EL TOTAL COBRADO DE LA INTERCAMBIO DE ACUERDO AL CRITERIO DE IGUALACION
			#SEGUN SEA CBU, ANSES O CJP
					
			$datosIntercambio = $oRegistroProcesado->getDatosIntercambio($liquidacionSocio,1);

			if($liquidacionSocio['LiquidacionSocioNoimputada']['importe_adebitar'] == 0) $liquidacionSocio['LiquidacionSocioNoimputada']['importe_adebitar'] = $liquidacionSocio['LiquidacionSocioNoimputada']['importe_dto'];
			
			if(!empty($datosIntercambio) && $datosIntercambio['indica_pago'] == 1):
			
				$liquidacionSocio['LiquidacionSocioNoimputada']['indica_pago'] = $datosIntercambio['indica_pago'];
				$liquidacionSocio['LiquidacionSocioNoimputada']['liquidacion_intercambio_id'] = $datosIntercambio['id'];
				$liquidacionSocio['LiquidacionSocioNoimputada']['status'] = $datosIntercambio['status'];
				$liquidacionSocio['LiquidacionSocioNoimputada']['fecha_pago'] = $datosIntercambio['fecha_pago'];
				$liquidacionSocio['LiquidacionSocioNoimputada']['banco_intercambio'] = $datosIntercambio['banco_intercambio'];
				
				$totalCobradoRegistro = $oRegistroProcesado->getTotalCobrado($liquidacionSocio);
				$liquidacionSocio['LiquidacionSocioNoimputada']['importe_debitado'] = $totalCobradoRegistro - $DEBITADO_SOCIO;
//				if($organismo == "MUTUCORG2201") $DEBITADO_SOCIO += $totalCobradoRegistro;
				if(substr($organismo,8,2) == "22") $DEBITADO_SOCIO += $totalCobradoRegistro;
				
				if($liquidacionSocio['LiquidacionSocioNoimputada']['importe_adebitar'] != $liquidacionSocio['LiquidacionSocioNoimputada']['importe_debitado']) $liquidacionSocio['LiquidacionSocioNoimputada']['importe_adebitar'] = $liquidacionSocio['LiquidacionSocioNoimputada']['importe_debitado'];

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
			$liquidacionSocio['LiquidacionSocioNoimputada']['importe_imputado'] = 0;
			$liquidacionSocio['LiquidacionSocioNoimputada']['importe_reintegro'] = 0;			
			
			if($liquidacionSocio['LiquidacionSocioNoimputada']['indica_pago'] == 1):
			
				
				$liquidacion_intercambio_id = $liquidacionSocio['LiquidacionSocioNoimputada']['liquidacion_intercambio_id'];
				$saldoDebitoSocio = $liquidacionSocio['LiquidacionSocioNoimputada']['importe_debitado'];
				
				
				foreach($cuotasPendientesImputar as $idx => $cuota){
					
					
					$importe_cuota = $cuota['LiquidacionCuotaNoimputada']['saldo_actual'];
					
			
					if($cuota['LiquidacionCuotaNoimputada']['saldo_actual'] > $cuota['LiquidacionCuotaNoimputada']['importe_debitado']):
					
						if($saldoDebitoSocio >= $importe_cuota){
							
							$importeImputaCuota = $importe_cuota;
							$saldoDebitoSocio -= $importe_cuota;
							
						}else{
							
							$importeImputaCuota = $saldoDebitoSocio;
							$saldoDebitoSocio -= $importeImputaCuota;
							
						}
						
						$cuota['LiquidacionCuotaNoimputada']['importe_debitado'] = $importeImputaCuota;	
						
						if($importeImputaCuota != 0)$cuota['LiquidacionCuotaNoimputada']['liquidacion_intercambio_id'] = $liquidacion_intercambio_id;
						else $cuota['LiquidacionCuotaNoimputada']['liquidacion_intercambio_id'] = 0;
						
						$ACUM_IMPUTADO += $importeImputaCuota;
						
					
					endif;
					#guardo en el array de cuotas la cuota con los datos actualizados de la imputacion
					$cuotasPendientesImputar[$idx] = $cuota;
					
				}

				
				#guardo en la cabecera el importe imputado
				if($ACUM_IMPUTADO != 0) $liquidacionSocio['LiquidacionSocioNoimputada']['importe_imputado'] = $ACUM_IMPUTADO;

				#calculo si tiene reintegro
				if($liquidacionSocio['LiquidacionSocioNoimputada']['importe_imputado'] < $liquidacionSocio['LiquidacionSocioNoimputada']['importe_debitado']){
					$liquidacionSocio['LiquidacionSocioNoimputada']['importe_reintegro'] = $liquidacionSocio['LiquidacionSocioNoimputada']['importe_debitado'] - $liquidacionSocio['LiquidacionSocioNoimputada']['importe_imputado'];
				}					
				
			endif; //endif indica_pago = 1
			
			
			$this->save($liquidacionSocio);	

			//actualizo el ID de la liquidacion socio en la intercambio
			$intercambio = $oRegistroProcesado->read(null,$liquidacionSocio['LiquidacionSocioNoimputada']['liquidacion_intercambio_id']);
			$intercambio['LiquidacionIntercambioRegistroProcesado']['liquidacion_socio_id'] = $liquidacionSocio['LiquidacionSocioNoimputada']['id'];
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
		$registros = $this->find('count',array('conditions' => array('LiquidacionSocioNoimputada.liquidacion_id' => $liquidacion_id)));
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
//		$registros = $this->find('count',array('conditions' => array('LiquidacionSocioNoimputada.liquidacion_id' => $liquidacion_id,'LiquidacionSocioNoimputada.alta' => 1)));
//		return $registros;
		
		$sql = "SELECT COUNT(DISTINCT LiquidacionSocioNoimputada.socio_id) AS altas FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada 
				WHERE liquidacion_id = $liquidacion_id AND
				socio_id NOT IN (SELECT socio_id FROM liquidacion_socio_noimputadas
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
																'LiquidacionSocioNoimputada.liquidacion_id' => $liquidacion_id,
																'LiquidacionSocioNoimputada.socio_id' => $socio_id,
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
//																'LiquidacionSocioNoimputada.liquidacion_id' => $liquidacion_id,
////																'IFNULL(LiquidacionSocioNoimputada.importe_adebitar,0) >' => 0,
//																'LiquidacionSocioNoimputada.diskette' => 1,
//																),
//											'fields' => array('codigo_empresa,turno_pago,count(1) as cantidad,sum(importe_dto) as importe_dto,sum(importe_adebitar) as importe_adebitar'),					
//											'group' => array('LiquidacionSocioNoimputada.turno_pago')										
//										)
//		);
	
        
//        $sql = "SELECT codigo_empresa,turno_pago,count(1) as cantidad,sum(importe_dto) as importe_dto,sum(importe_adebitar) as importe_adebitar
//                ,(select sum(lc.saldo_actual) from liquidacion_cuota_noimputadas lc, persona_beneficios be where
//                lc.liquidacion_id = LiquidacionSocioNoimputada.liquidacion_id and lc.persona_beneficio_id = be.id and be.turno_pago = LiquidacionSocioNoimputada.turno_pago) as saldo_actual
//                ,(select sum(lc.importe) from liquidacion_cuota_noimputadas lc, persona_beneficios be where
//                lc.liquidacion_id = LiquidacionSocioNoimputada.liquidacion_id and lc.persona_beneficio_id = be.id and be.turno_pago = LiquidacionSocioNoimputada.turno_pago) as importe_original
//                FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada 
//                WHERE LiquidacionSocioNoimputada.liquidacion_id = $liquidacion_id AND LiquidacionSocioNoimputada.diskette = 1 GROUP BY LiquidacionSocioNoimputada.turno_pago ";

//        $sql = "SELECT codigo_empresa,turno_pago,count(1) as cantidad,sum(importe_dto) as importe_dto,sum(importe_adebitar) as importe_adebitar
//                FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada 
//                WHERE LiquidacionSocioNoimputada.liquidacion_id = $liquidacion_id AND LiquidacionSocioNoimputada.diskette = 1 GROUP BY LiquidacionSocioNoimputada.turno_pago ";
        
//        $sql = "SELECT LiquidacionSocioNoimputada.codigo_empresa,
//                IF(LiquidacionTurno.codigo_empresa IS NOT NULL,LiquidacionSocioNoimputada.turno_pago,LiquidacionSocioNoimputada.turno_pago) as turno,
//                CONCAT(TRIM(IFNULL(Empresa.concepto_1,'*** TURNO/s NO ASOCIADO A LA LIQUIDACION ***')),
//                IF(LiquidacionTurno.descripcion IS NOT NULL,CONCAT(' - ', LiquidacionTurno.descripcion), '')) AS turno_descripcion,
//                count(1) as cantidad,sum(importe_dto) 
//                as importe_dto,sum(importe_adebitar) as importe_adebitar,
//                IF(LiquidacionTurno.codigo_empresa IS NOT NULL,0,1) as error_turno
//                FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada 
//                LEFT JOIN liquidacion_turnos LiquidacionTurno ON (LiquidacionTurno.codigo_empresa = 
//                LiquidacionSocioNoimputada.codigo_empresa AND LiquidacionTurno.turno = 
//                LiquidacionSocioNoimputada.turno_pago)
//                LEFT JOIN global_datos as Empresa on (Empresa.id = LiquidacionTurno.codigo_empresa)
//                WHERE LiquidacionSocioNoimputada.liquidacion_id = $liquidacion_id AND LiquidacionSocioNoimputada.diskette = 1 
//                GROUP BY LiquidacionSocioNoimputada.turno_pago
//                ORDER BY Empresa.concepto_1,LiquidacionTurno.descripcion;";
        
        $sql = "select 
                LiquidacionSocioNoimputada.codigo_empresa,
                sum(LiquidacionSocioNoimputada.diskette) as diskette,
                IF(LiquidacionTurno.codigo_empresa IS NOT NULL,LiquidacionSocioNoimputada.turno_pago,LiquidacionSocioNoimputada.turno_pago) as turno,
                CONCAT(TRIM(IFNULL(LiquidacionTurno.empresa,'*** TURNO/s NO ASOCIADO A LA LIQUIDACION ***')),
                IF(LiquidacionTurno.descripcion IS NOT NULL,CONCAT(' - ', LiquidacionTurno.descripcion), '')) AS turno_descripcion,
                count(1) as cantidad,sum(importe_dto) 
                as importe_dto,sum(importe_adebitar) as importe_adebitar,
                IF(LiquidacionTurno.codigo_empresa IS NOT NULL,0,1) as error_turno
                from liquidacion_socio_noimputadas LiquidacionSocioNoimputada
                left join (select lt.codigo_empresa,lt.turno,lt.descripcion,Empresa.concepto_1 as empresa 
                from liquidacion_turnos lt
                LEFT JOIN global_datos as Empresa on (Empresa.id = lt.codigo_empresa)
                group by lt.codigo_empresa,lt.turno) as LiquidacionTurno on (LiquidacionTurno.codigo_empresa = 
                LiquidacionSocioNoimputada.codigo_empresa AND LiquidacionTurno.turno = 
                LiquidacionSocioNoimputada.turno_pago)
                WHERE LiquidacionSocioNoimputada.liquidacion_id = $liquidacion_id -- AND LiquidacionSocioNoimputada.diskette = 1 
                GROUP BY LiquidacionSocioNoimputada.turno_pago
                ORDER BY LiquidacionTurno.empresa,LiquidacionTurno.descripcion;";
        
        $registros = $this->query($sql);
        return $registros;
        
//		debug($registros);
//		exit;
		
//		App::import('Model', 'Mutual.LiquidacionTurno');
//		$oTURNO = new LiquidacionTurno();
//				
//		foreach($registros as $idx => $turno){
//			
//			$turno['LiquidacionSocioNoimputada']['error_turno'] = 0;
//			
//			$turno['LiquidacionSocioNoimputada']['descripcion'] = $oTURNO->getDescripcionByTruno($turno['LiquidacionSocioNoimputada']['turno_pago']);
//			
//			$turno['LiquidacionSocioNoimputada']['empresa'] = parent::GlobalDato('concepto_1',$turno['LiquidacionSocioNoimputada']['codigo_empresa']);
//			
//			if( $turno['LiquidacionSocioNoimputada']['codigo_empresa'] == 'MUTUEMPR'){
//                            $turno['LiquidacionSocioNoimputada']['empresa'] = "*** SIN DATOS ***";
//                        }
//
//			if(empty($turno['LiquidacionSocioNoimputada']['turno_pago']) || $turno['LiquidacionSocioNoimputada']['turno_pago'] == "SDATO"){
//				$turno['LiquidacionSocioNoimputada']['empresa'] = "*** SIN DATOS ***";
//				$turno['LiquidacionSocioNoimputada']['turno_pago'] = "SDATO";
//				$turno['LiquidacionSocioNoimputada']['error_turno'] = 1;
//			}
//			
//			
//			$turno['LiquidacionSocioNoimputada']['cantidad'] = $turno[0]['cantidad'];
//			$turno['LiquidacionSocioNoimputada']['importe_dto'] = $turno[0]['importe_dto'];
//			$turno['LiquidacionSocioNoimputada']['importe_adebitar'] = $turno[0]['importe_adebitar'];
//            $turno['LiquidacionSocioNoimputada']['saldo_actual'] = $turno[0]['importe_adebitar'];
////            $turno['LiquidacionSocioNoimputada']['importe_original'] = $turno[0]['importe_original'];
////			$turno['LiquidacionSocioNoimputada']['fecha_debito'] = $turno[0]['fecha_debito'];
//			
//			$turnos[$idx] = $turno['LiquidacionSocioNoimputada'];
//			
////			array_push($turnos,$turno['LiquidacionSocioNoimputada']);
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
		$conditions['LiquidacionSocioNoimputada.liquidacion_id'] = $liquidacionId;
		$conditions['LiquidacionSocioNoimputada.turno_pago'] = $turnos;
// 		$conditions['LiquidacionSocioNoimputada.error_cbu'] = $errorCBU;
		$conditions['LiquidacionSocioNoimputada.importe_adebitar >'] = 0;
		if($intercambio) $conditions['NOT'] = array('IFNULL(LiquidacionSocioNoimputada.intercambio,"0")' => 0);
		if($soloParaDiskette)$conditions['LiquidacionSocioNoimputada.diskette'] = 1;
		
		$ordenamiento = array('GlobalDato.concepto_1,LiquidacionSocioNoimputada.apenom,LiquidacionSocioNoimputada.codigo_dto,LiquidacionSocioNoimputada.sub_codigo,LiquidacionSocioNoimputada.registro');
		
		if($order == 'IMPORTE') $ordenamiento = array('LiquidacionSocioNoimputada.importe_adebitar ASC');
		
		$socios = $this->find('all',array(
										'joins' => array(
												array(
													'table' => 'global_datos',
													'alias' => 'GlobalDato',
													'type' => 'left',
													'foreignKey' => false,
													'conditions' => array('LiquidacionSocioNoimputada.codigo_empresa = GlobalDato.id')
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
			
			$socio['LiquidacionSocioNoimputada']['descripcion'] = $oTURNO->getDescripcionByTruno($socio['LiquidacionSocioNoimputada']['turno_pago']);
			$socio['LiquidacionSocioNoimputada']['empresa'] = parent::GlobalDato('concepto_1',$socio['LiquidacionSocioNoimputada']['codigo_empresa']);
			
			if( $socio['LiquidacionSocioNoimputada']['codigo_empresa'] == 'MUTUEMPR') $socio['LiquidacionSocioNoimputada']['empresa'] = "**S/D**";
			if(empty($socio['LiquidacionSocioNoimputada']['turno_pago']) || $socio['LiquidacionSocioNoimputada']['turno_pago'] == "SDATO"){
				$socio['LiquidacionSocioNoimputada']['empresa'] = "*** SIN DATOS ***";
				$socio['LiquidacionSocioNoimputada']['turno_pago'] = "SDATO";
			}
			
			//SACAR LOS REINTEGROS QUE TIENE EN LIQUIDACION ANTERIOR
			$socio['LiquidacionSocioNoimputada']['importe_reintegro_liquidacion_anterior'] = $oREINTEGRO->getTotalReintegroLiquidacionAnterior($socio['LiquidacionSocioNoimputada']['socio_id'],$socio['LiquidacionSocioNoimputada']['liquidacion_id']);

			$socios[$idx] = $socio;
		}
		
		return $socios;	
	}
	
        
    function getDetalleDeTurnoDiskette($liquidacionId,$turno,$orderBy="LiquidacionSocioNoimputada.apenom"){
        
        $sql = "select 
                LiquidacionSocioNoimputada.id,
                LiquidacionSocioNoimputada.documento,
                LiquidacionSocioNoimputada.apenom, 
                LiquidacionSocioNoimputada.socio_id,
                LiquidacionSocioNoimputada.ultima_calificacion,
                Calificacion.concepto_1,
                LiquidacionSocioNoimputada.registro,
                LiquidacionSocioNoimputada.cbu,
                LiquidacionSocioNoimputada.sucursal,
                LiquidacionSocioNoimputada.nro_cta_bco,
                LiquidacionSocioNoimputada.importe_adebitar,
                LiquidacionSocioNoimputada.diskette
                from liquidacion_socio_noimputadas LiquidacionSocioNoimputada
                left join global_datos Calificacion on Calificacion.id = LiquidacionSocioNoimputada.ultima_calificacion
                where LiquidacionSocioNoimputada.liquidacion_id = $liquidacionId
                and LiquidacionSocioNoimputada.turno_pago = '$turno'
                order by LiquidacionSocioNoimputada.apenom";
        $socios = $this->query($sql);
        return $socios;
    }    
    
    function getDatosParaDisketteCBUReporteByTurno($liquidacionId,$turno,$soloParaDiskette=false){
        $socios = array();
        $sql = "SELECT LiquidacionSocioNoimputada.documento,
                UPPER(LiquidacionSocioNoimputada.apenom) as apenom,LiquidacionSocioNoimputada.banco_id,Banco.nombre,LiquidacionSocioNoimputada.sucursal,
                LiquidacionSocioNoimputada.nro_cta_bco,LiquidacionSocioNoimputada.cbu,SUM(LiquidacionSocioNoimputada.importe_adebitar) 
                AS importe_adebitar,COUNT(*) as registro
                ,(SELECT SUM(lc.saldo_actual) FROM liquidacion_cuota_noimputadas lc WHERE
                lc.liquidacion_id = LiquidacionSocioNoimputada.liquidacion_id AND lc.socio_id = LiquidacionSocioNoimputada.socio_id) AS saldo_actual
                ,(SELECT SUM(lc.importe) FROM liquidacion_cuota_noimputadas lc WHERE
                lc.liquidacion_id = LiquidacionSocioNoimputada.liquidacion_id AND lc.socio_id = LiquidacionSocioNoimputada.socio_id) AS importe_original
                FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada 
                LEFT JOIN global_datos AS GlobalDato ON (LiquidacionSocioNoimputada.codigo_empresa = GlobalDato.id)
                LEFT JOIN bancos as Banco ON (LiquidacionSocioNoimputada.banco_id = Banco.id) 
                WHERE LiquidacionSocioNoimputada.liquidacion_id = $liquidacionId AND LiquidacionSocioNoimputada.turno_pago = '$turno'
                AND LiquidacionSocioNoimputada.importe_adebitar > 0
                ".($soloParaDiskette ? " AND LiquidacionSocioNoimputada.diskette = 1" : "")."
                GROUP BY LiquidacionSocioNoimputada.documento,
                LiquidacionSocioNoimputada.apenom, LiquidacionSocioNoimputada.persona_beneficio_id
                ORDER BY GlobalDato.concepto_1 ASC, LiquidacionSocioNoimputada.apenom ASC, 
                LiquidacionSocioNoimputada.codigo_dto ASC, 
                LiquidacionSocioNoimputada.sub_codigo ASC,LiquidacionSocioNoimputada.registro ASC";
        $datos = $this->query($sql);
        if(!empty($datos)){
            foreach($datos as $ix => $dato){
                $socios[$ix]['LiquidacionSocioNoimputada']['documento'] = $dato['LiquidacionSocioNoimputada']['documento'];
                $socios[$ix]['LiquidacionSocioNoimputada']['apenom'] = $dato[0]['apenom'];
                $socios[$ix]['LiquidacionSocioNoimputada']['registro'] = $dato[0]['registro'];
                $socios[$ix]['LiquidacionSocioNoimputada']['banco'] = $dato['Banco']['nombre'];
                $socios[$ix]['LiquidacionSocioNoimputada']['banco_id'] = $dato['LiquidacionSocioNoimputada']['banco_id'];
                $socios[$ix]['LiquidacionSocioNoimputada']['sucursal'] = $dato['LiquidacionSocioNoimputada']['sucursal'];
                $socios[$ix]['LiquidacionSocioNoimputada']['nro_cta_bco'] = $dato['LiquidacionSocioNoimputada']['nro_cta_bco'];
                $socios[$ix]['LiquidacionSocioNoimputada']['cbu'] = $dato['LiquidacionSocioNoimputada']['cbu'];
                $socios[$ix]['LiquidacionSocioNoimputada']['importe_original'] = $dato[0]['importe_original'];
                $socios[$ix]['LiquidacionSocioNoimputada']['saldo_actual'] = $dato[0]['saldo_actual'];
                $socios[$ix]['LiquidacionSocioNoimputada']['importe_adebitar'] = $dato[0]['importe_adebitar'];
                
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
//		$conditions['LiquidacionSocioNoimputada.liquidacion_id'] = $liquidacionId;
//		$conditions['LiquidacionSocioNoimputada.turno_pago'] = $turnos;
//		$conditions['LiquidacionSocioNoimputada.error_cbu'] = 0;
//		$conditions['LiquidacionSocioNoimputada.importe_adebitar >'] = 0;
//		$conditions['NOT'] = array('IFNULL(LiquidacionSocioNoimputada.intercambio,"0")' => 0);
//		$conditions['LiquidacionSocioNoimputada.diskette'] = 1;
//		$socios = $this->find('all',array(
//										'joins' => array(
//												array(
//													'table' => 'global_datos',
//													'alias' => 'GlobalDato',
//													'type' => 'inner',
//													'foreignKey' => false,
//													'conditions' => array('LiquidacionSocioNoimputada.codigo_empresa = GlobalDato.id')
//													),		
//										),	
//										'conditions' => $conditions,
//										'fields' => array('LiquidacionSocioNoimputada.codigo_empresa,count(*) as cantidad,sum(importe_adebitar) as importe_adebitar'),
//										'group' => array('LiquidacionSocioNoimputada.codigo_empresa'),	
//										'order' => array('GlobalDato.concepto_1')								
//		));	
		
		$conditions['LiquidacionSocioNoimputada.liquidacion_id'] = $liquidacionId;
		$conditions['LiquidacionSocioNoimputada.turno_pago'] = $turnos;
		$conditions['LiquidacionSocioNoimputada.error_cbu'] = 0;
		$conditions['LiquidacionSocioNoimputada.importe_adebitar >'] = 0;
		$conditions['NOT'] = array('IFNULL(LiquidacionSocioNoimputada.intercambio,"0")' => 0);
		$conditions['LiquidacionSocioNoimputada.diskette'] = 1;

		$socios = $this->find('all',array('conditions' =>$conditions,
											'fields' => array('codigo_empresa,turno_pago,count(1) as cantidad,sum(importe_dto) as importe_dto,sum(importe_adebitar) as importe_adebitar'),
											'group' => array('LiquidacionSocioNoimputada.turno_pago')										
										)
		);		
		
		
		App::import('Model', 'Mutual.LiquidacionTurno');
		$oTURNO = new LiquidacionTurno();
				
		foreach($socios as $idx => $socio){
			
			$socio['LiquidacionSocioNoimputada']['descripcion'] = $oTURNO->getDescripcionByTruno($socio['LiquidacionSocioNoimputada']['turno_pago']);
			
			$socio['LiquidacionSocioNoimputada']['empresa'] = parent::GlobalDato('concepto_1',$socio['LiquidacionSocioNoimputada']['codigo_empresa']);
			
			if( $socio['LiquidacionSocioNoimputada']['codigo_empresa'] == 'MUTUEMPR') $socio['LiquidacionSocioNoimputada']['empresa'] = "**S/D**";
			if(empty($socio['LiquidacionSocioNoimputada']['turno_pago']) || $socio['LiquidacionSocioNoimputada']['turno_pago'] == "SDATO"){
				$socio['LiquidacionSocioNoimputada']['empresa'] = "*** SIN DATOS ***";
				$socio['LiquidacionSocioNoimputada']['turno_pago'] = "SDATO";
			}
			
			$socio['LiquidacionSocioNoimputada']['cantidad'] = $socio[0]['cantidad'];
			$socio['LiquidacionSocioNoimputada']['importe_dto'] = $socio[0]['importe_dto'];
			$socio['LiquidacionSocioNoimputada']['importe_adebitar'] = $socio[0]['importe_adebitar'];
			
			$socios[$idx] = $socio;
			
//			array_push($turnos,$turno['LiquidacionSocioNoimputada']);
		}
		
		return $socios;			
	}
	
	/**
	 * Get Datos para Diskette CJP
	 * carga los datos para sacar el diskette CJP
	 * @param integer $liquidacionId
	 * @return array
	 */
	function getDatosParaDisketteCJP($liquidacionId,$order=array('LiquidacionSocioNoimputada.apenom,LiquidacionSocioNoimputada.registro'),$codDto=0,$altaBaja = 'A'){
		$conditions = array();
		$conditions['LiquidacionSocioNoimputada.liquidacion_id'] = $liquidacionId;
		$conditions['LiquidacionSocioNoimputada.importe_adebitar >'] = 0;
		$conditions['LiquidacionSocioNoimputada.sub_codigo'] = $codDto;
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
//		$sql = "SELECT * FROM liquidacion_socio_noimputadas as LiquidacionSocioNoimputada WHERE liquidacion_id = $liquidacionId
//				AND sub_codigo = '0' AND importe_adebitar > 0 and
//				socio_id NOT IN (SELECT socio_id FROM liquidacion_socio_noimputadas
//				WHERE liquidacion_id = (SELECT id FROM liquidaciones 
//										WHERE liquidaciones.codigo_organismo = 'MUTUCORG7701'
//										AND liquidaciones.id <> LiquidacionSocioNoimputada.liquidacion_id
//										ORDER BY liquidaciones.id DESC LIMIT 1)
//				AND sub_codigo = '0')
//				UNION
//				SELECT * FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada WHERE liquidacion_id = $liquidacionId
//				AND sub_codigo = '0' AND importe_adebitar <>
//					(SELECT importe_adebitar FROM liquidacion_socio_noimputadas ls
//					WHERE ls.liquidacion_id = (SELECT id FROM liquidaciones 
//							WHERE liquidaciones.codigo_organismo = 'MUTUCORG7701'
//							AND liquidaciones.id < LiquidacionSocioNoimputada.liquidacion_id
//							ORDER BY liquidaciones.id DESC LIMIT 1)
//					AND ls.sub_codigo = '0' AND LiquidacionSocioNoimputada.socio_id = ls.socio_id)				
//				order by apenom, registro";
		
//		$sql = "SELECT * FROM liquidacion_socio_noimputadas as LiquidacionSocioNoimputada WHERE liquidacion_id = $liquidacionId
//				AND sub_codigo = '0' AND importe_adebitar > 0 and
//				socio_id NOT IN (SELECT socio_id FROM liquidacion_socio_noimputadas
//				WHERE liquidacion_id = (SELECT id FROM liquidaciones 
//										WHERE liquidaciones.codigo_organismo = 'MUTUCORG7701'
//										AND liquidaciones.id <> LiquidacionSocioNoimputada.liquidacion_id
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
                    $sql = "SELECT LiquidacionSocioNoimputada.*,'A' AS tipo_novedad FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada
                                    WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto'
                                    AND orden_descuento_id NOT IN (SELECT orden_descuento_id FROM
                                    liquidacion_socio_rendiciones WHERE liquidacion_id = $liquidacion_anterior)
                                    ORDER BY LiquidacionSocioNoimputada.apenom;";		
                }else{
                    $sql = "SELECT LiquidacionSocioNoimputada.*,'A' AS tipo_novedad FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada
                                    WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto'
                                    ORDER BY LiquidacionSocioNoimputada.apenom;";		                    
                }
		
		
		$socios = $this->query($sql);
//		if(!empty($socios)):
//			foreach($socios as $idx => $socio):
//				if(isset($socio[0]))$socios[$idx]['LiquidacionSocioNoimputada'] = $socio[0];
//			endforeach;
//		
//		endif;
		return $socios;
	}

	
	function datosBajaSociosCJP($liquidacionId){
		$sql = "SELECT * FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada 
				WHERE liquidacion_id = (SELECT id FROM liquidaciones 
				WHERE liquidaciones.codigo_organismo = 'MUTUCORG7701'
				AND liquidaciones.id < $liquidacionId
				ORDER BY liquidaciones.id DESC LIMIT 1)
				AND sub_codigo = '0' AND
				socio_id NOT IN (SELECT socio_id FROM liquidacion_socio_noimputadas
				WHERE liquidacion_id = $liquidacionId AND sub_codigo = '0')
				order by apenom, registro";
		$socios = $this->query($sql);
		
		if(empty($socios)) return $socios;
		
		App::import('Model', 'Config.Banco');
		$oBanco = new Banco(null);			
		
		foreach($socios as $idx => $socio):
		
			if(empty($socio['LiquidacionSocioNoimputada']['intercambio'])):
			
				$campos = array(
								1 => $socio['LiquidacionSocioNoimputada']['tipo'],
								2 => $socio['LiquidacionSocioNoimputada']['nro_ley'],
								3 => $socio['LiquidacionSocioNoimputada']['nro_beneficio'],
								4 => $socio['LiquidacionSocioNoimputada']['sub_beneficio'],
								5 => $socio['LiquidacionSocioNoimputada']['codigo_dto'],
								6 => $socio['LiquidacionSocioNoimputada']['sub_codigo'],
								7 => $socio['LiquidacionSocioNoimputada']['importe_adebitar'],
								8 => 'I',
								9 => (parent::is_date($socio['LiquidacionSocioNoimputada']['fecha_otorgamiento']) ? $socio['LiquidacionSocioNoimputada']['fecha_otorgamiento'] : null),
								10 => $socio['LiquidacionSocioNoimputada']['importe_total'],
								11 => $socio['LiquidacionSocioNoimputada']['cuotas'],
								12 => $socio['LiquidacionSocioNoimputada']['importe_cuota'],
								13 => $socio['LiquidacionSocioNoimputada']['importe_deuda'],
								14 => $socio['LiquidacionSocioNoimputada']['importe_deuda_vencida'],
								15 => $socio['LiquidacionSocioNoimputada']['importe_deuda_no_vencida'],
								16 => $socio['LiquidacionSocioNoimputada']['orden_descuento_id'],
				
				);			
				$socio['LiquidacionSocioNoimputada']['intercambio'] = $oBanco->armaStringDebitoCJP($campos);
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
		
//		$sql = "SELECT * FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada 
//				WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto' AND
//				orden_descuento_id NOT IN (SELECT orden_descuento_id FROM liquidacion_socio_noimputadas
//				WHERE liquidacion_id = (SELECT id FROM liquidaciones 
//				WHERE liquidaciones.codigo_organismo = '$codigoBeneficio'
//				AND liquidaciones.id < $liquidacionId
//				ORDER BY liquidaciones.id DESC LIMIT 1))
//				and importe_total <> 0
//				and importe_total = importe_deuda_no_vencida $sort";
				
//		$sql = "SELECT LiquidacionSocioNoimputada.*,'A' as tipo_novedad FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada 
//				WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto' AND
//				orden_descuento_id NOT IN (SELECT orden_descuento_id FROM liquidacion_socio_noimputadas
//				WHERE liquidacion_id = (SELECT id FROM liquidaciones 
//				WHERE liquidaciones.codigo_organismo = '$codigoBeneficio'
//				AND liquidaciones.id < $liquidacionId
//				ORDER BY liquidaciones.id DESC LIMIT 1))
//				and importe_total <> 0
//				and importe_total = importe_deuda_no_vencida 
//				UNION
//				SELECT LiquidacionSocioNoimputada.*,'M' as tipo_novedad FROM liquidacion_socio_noimputadas AS LiquidacionSocio
//				WHERE liquidacion_id = $liquidacionId
//				AND sub_codigo = '$subCodigoDto' AND orden_descuento_id = (SELECT orden_descuento_id FROM liquidacion_socio_noimputadas ls2
//				WHERE liquidacion_id = (SELECT id FROM liquidaciones 
//				WHERE liquidaciones.codigo_organismo = '$codigoBeneficio'
//				AND liquidaciones.id < $liquidacionId
//				ORDER BY liquidaciones.id DESC LIMIT 1) 
//				AND ls2.orden_descuento_id = LiquidacionSocioNoimputada.orden_descuento_id AND ls2.importe_adebitar <> LiquidacionSocioNoimputada.importe_adebitar)
//				AND LiquidacionSocioNoimputada.cuotas  = 0
//				UNION
//				SELECT LiquidacionSocioNoimputada.*,'R' as tipo_novedad FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada 
//				WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto' AND
//				orden_descuento_id NOT IN (SELECT orden_descuento_id FROM liquidacion_socio_noimputadas
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
//		$SQL_altasNuevas 	= "	SELECT LiquidacionSocioNoimputada.*,'A' as tipo_novedad FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada 
//								WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto' AND
//								orden_descuento_id NOT IN (SELECT orden_descuento_id FROM liquidacion_socio_noimputadas
//								WHERE liquidacion_id = (SELECT id FROM liquidaciones 
//								WHERE liquidaciones.codigo_organismo = '$codigoBeneficio'
//								AND liquidaciones.id < $liquidacionId
//								ORDER BY liquidaciones.id DESC LIMIT 1))
//								and importe_total <> 0
//			
//								
//													
//																							and importe_total = importe_deuda_no_vencida";
		if(!empty($liquidacion_anterior)){
                    $SQL_altasNuevas = "SELECT LiquidacionSocioNoimputada.*,'A' AS tipo_novedad FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada
                                                            WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto'
                                                            AND orden_descuento_id NOT IN (SELECT orden_descuento_id FROM
                                                            liquidacion_socio_rendiciones WHERE liquidacion_id <= $liquidacion_anterior)";
                }else{
		$SQL_altasNuevas = "SELECT LiquidacionSocioNoimputada.*,'A' AS tipo_novedad FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada
							WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto'";
                }
		
		#################################################################################################################
		# MODIFICACION DE IMPORTE DE SERVICIOS
		#################################################################################################################
		$SQL_modServ 		= "	SELECT LiquidacionSocioNoimputada.*,'M' as tipo_novedad FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada
								WHERE liquidacion_id = $liquidacionId
								AND sub_codigo = '$subCodigoDto' AND orden_descuento_id = (SELECT orden_descuento_id FROM liquidacion_socio_noimputadas ls2
								WHERE liquidacion_id = (SELECT id FROM liquidaciones 
								WHERE liquidaciones.codigo_organismo = '$codigoBeneficio'
								AND liquidaciones.id < $liquidacionId
								ORDER BY liquidaciones.id DESC LIMIT 1) 
								AND ls2.orden_descuento_id = LiquidacionSocioNoimputada.orden_descuento_id AND ls2.importe_adebitar <> LiquidacionSocioNoimputada.importe_adebitar)
								AND LiquidacionSocioNoimputada.cuotas  = 0";
		#################################################################################################################
		# ORDENES DE DESCUENTOS QUE SE REASIGNARON (PEJ.: VENIAN POR CBU Y PASAN A LA CJP)
		#################################################################################################################
//		$SQL_reAsign 		= "	SELECT LiquidacionSocioNoimputada.*,'R' as tipo_novedad FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada 
//								WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto' AND
//								orden_descuento_id NOT IN (SELECT orden_descuento_id FROM liquidacion_socio_noimputadas
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
//			$altas[$idx]['LiquidacionSocioNoimputada'] = $socio[0];
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
		
		
//		$sql = "SELECT * FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada 
//				WHERE liquidacion_id = (SELECT id FROM liquidaciones 
//				WHERE liquidaciones.codigo_organismo = '$codigoBeneficio'
//				AND liquidaciones.id < $liquidacionId
//				ORDER BY liquidaciones.id DESC LIMIT 1)
//				AND sub_codigo = '$subCodigoDto' AND
//				orden_descuento_id NOT IN (SELECT orden_descuento_id FROM liquidacion_socio_noimputadas
//				WHERE liquidacion_id = $liquidacionId AND sub_codigo = '$subCodigoDto')
//				ORDER BY apenom, registro";
		
//		debug($liquidacion_anterior);
//		DEBUG($sql);
//		exit;
		
//		$sql = "SELECT DISTINCT LiquidacionSocioNoimputada.*,LiquidacionSocioRendicion.orden_descuento_id  FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion
//				INNER JOIN liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada  ON (LiquidacionSocioNoimputada.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id 
//						AND LiquidacionSocioNoimputada.socio_id = LiquidacionSocioRendicion.socio_id)
//				WHERE LiquidacionSocioRendicion.liquidacion_id = (SELECT id FROM liquidaciones 
//								WHERE liquidaciones.codigo_organismo = '$codigoBeneficio'
//								AND liquidaciones.id < $liquidacionId
//								ORDER BY liquidaciones.id DESC LIMIT 1) 
//				AND LiquidacionSocioRendicion.sub_codigo = '1'
//				AND LiquidacionSocioRendicion.orden_descuento_id NOT IN (SELECT orden_descuento_id FROM liquidacion_cuota_noimputadas
//				WHERE liquidacion_id = $liquidacionId)
//				ORDER BY LiquidacionSocioNoimputada.apenom ASC;";
		
		
		$sql = "SELECT 
					LiquidacionSocioNoimputada.documento,LiquidacionSocioNoimputada.apenom,LiquidacionSocioNoimputada.tipo,LiquidacionSocioNoimputada.nro_ley,LiquidacionSocioNoimputada.nro_beneficio,
					LiquidacionSocioNoimputada.sub_beneficio,LiquidacionSocioNoimputada.codigo_dto,LiquidacionSocioNoimputada.sub_codigo,LiquidacionSocioRendicion.orden_descuento_id
				FROM 
					liquidacion_socio_rendiciones AS LiquidacionSocioRendicion
				INNER JOIN 
					liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada  
						ON (
								LiquidacionSocioNoimputada.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id 
								AND LiquidacionSocioNoimputada.socio_id = LiquidacionSocioRendicion.socio_id 
								AND LiquidacionSocioNoimputada.codigo_dto = LiquidacionSocioRendicion.codigo_dto
							)
				WHERE 
					LiquidacionSocioRendicion.liquidacion_id = (SELECT id FROM liquidaciones 
																WHERE liquidaciones.codigo_organismo = '$codigoBeneficio'
																AND liquidaciones.id < $liquidacionId
																ORDER BY liquidaciones.id DESC LIMIT 1) 
					AND LiquidacionSocioRendicion.sub_codigo = '1'
					AND LiquidacionSocioRendicion.orden_descuento_id NOT IN 
									(SELECT orden_descuento_id FROM liquidacion_cuota_noimputadas
									WHERE liquidacion_id = $liquidacionId)
				GROUP BY
					LiquidacionSocioRendicion.orden_descuento_id
				ORDER BY 
					LiquidacionSocioNoimputada.apenom ASC;";
		
		
		$socios = $this->query($sql);
		
//		debug($sql);
//		exit;
		
		if(empty($socios)) return $socios;
		
//		debug($socios);
//		exit;
		
		App::import('Model', 'Config.Banco');
		$oBanco = new Banco(null);			
		
		foreach($socios as $idx => $socio):
		
//			if(empty($socio['LiquidacionSocioNoimputada']['intercambio'])):
			
//				$campos = array(
//								1 => $socio['LiquidacionSocioNoimputada']['tipo'],
//								2 => $socio['LiquidacionSocioNoimputada']['nro_ley'],
//								3 => $socio['LiquidacionSocioNoimputada']['nro_beneficio'],
//								4 => $socio['LiquidacionSocioNoimputada']['sub_beneficio'],
//								5 => $socio['LiquidacionSocioNoimputada']['codigo_dto'],
//								6 => $socio['LiquidacionSocioNoimputada']['sub_codigo'],
//								7 => $socio['LiquidacionSocioNoimputada']['importe_adebitar'],
//								8 => 'I',
//								9 => (parent::is_date($socio['LiquidacionSocioNoimputada']['fecha_otorgamiento']) ? $socio['LiquidacionSocioNoimputada']['fecha_otorgamiento'] : null),
//								10 => $socio['LiquidacionSocioNoimputada']['importe_total'],
//								11 => $socio['LiquidacionSocioNoimputada']['cuotas'],
//								12 => $socio['LiquidacionSocioNoimputada']['importe_cuota'],
//								13 => $socio['LiquidacionSocioNoimputada']['importe_deuda'],
//								14 => $socio['LiquidacionSocioNoimputada']['importe_deuda_vencida'],
//								15 => $socio['LiquidacionSocioNoimputada']['importe_deuda_no_vencida'],
//								16 => $socio['LiquidacionSocioRendicion']['orden_descuento_id'],
//				
//				);

				$campos = array(
								1 => $socio['LiquidacionSocioNoimputada']['tipo'],
								2 => $socio['LiquidacionSocioNoimputada']['nro_ley'],
								3 => $socio['LiquidacionSocioNoimputada']['nro_beneficio'],
								4 => $socio['LiquidacionSocioNoimputada']['sub_beneficio'],
								5 => $socio['LiquidacionSocioNoimputada']['codigo_dto'],
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
				$socio['LiquidacionSocioNoimputada']['intercambio'] = $oBanco->armaStringDebitoCJP($campos);
				$socio['LiquidacionSocioNoimputada']['orden_descuento_id'] = $socio['LiquidacionSocioRendicion']['orden_descuento_id'];
				$socio['LiquidacionSocioNoimputada']['error_cbu'] = 0;
				$socio['LiquidacionSocioNoimputada']['sub_codigo'] = 1;
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
	function getDatosParaDisketteCJPFormatoAnterior($liquidacionId,$order=array('LiquidacionSocioNoimputada.apenom,LiquidacionSocioNoimputada.registro'),$codDto=0){
		$socios = null;
		
		$conditions = array();
		$conditions['LiquidacionSocioNoimputada.liquidacion_id'] = $liquidacionId;
		$conditions['LiquidacionSocioNoimputada.importe_adebitar >'] = 0;
		$conditions['LiquidacionSocioNoimputada.sub_codigo'] = $codDto;		
		
		$socios = $this->find('all',array('conditions' => $conditions,
										'fields' => array(
															'LiquidacionSocioNoimputada.documento',
															'LiquidacionSocioNoimputada.apenom',	
															'LiquidacionSocioNoimputada.error_cbu',			
															'LiquidacionSocioNoimputada.tipo',
															'LiquidacionSocioNoimputada.nro_ley',
															'LiquidacionSocioNoimputada.nro_beneficio',
															'LiquidacionSocioNoimputada.sub_beneficio',
															'LiquidacionSocioNoimputada.codigo_dto',
															'LiquidacionSocioNoimputada.sub_codigo',
															'sum(LiquidacionSocioNoimputada.importe_adebitar) as importe_adebitar'					
														),
										'group' => array(
															'LiquidacionSocioNoimputada.documento',
															'LiquidacionSocioNoimputada.apenom',			
															'LiquidacionSocioNoimputada.tipo',
															'LiquidacionSocioNoimputada.nro_ley',
															'LiquidacionSocioNoimputada.nro_beneficio',
															'LiquidacionSocioNoimputada.sub_beneficio',
															'LiquidacionSocioNoimputada.codigo_dto',
															'LiquidacionSocioNoimputada.sub_codigo',
														),																					
										'order' => $order,
		));	
		if(empty($socios)) return null;
		
		App::import('Model', 'Config.Banco');
		$oBanco = new Banco(null);		
		
		foreach($socios as $i => $socio):
			
			if(isset($socio[0]['importe_adebitar']) && $socio[0]['importe_adebitar'] != 0):
			
				$socio['LiquidacionSocioNoimputada']['importe_adebitar'] = $socio[0]['importe_adebitar'];
				
				$campos = array(
								1 => $socio['LiquidacionSocioNoimputada']['tipo'],
								2 => $socio['LiquidacionSocioNoimputada']['nro_ley'],
								3 => $socio['LiquidacionSocioNoimputada']['nro_beneficio'],
								4 => $socio['LiquidacionSocioNoimputada']['sub_beneficio'],
								5 => $socio['LiquidacionSocioNoimputada']['codigo_dto'],
								6 => $socio['LiquidacionSocioNoimputada']['sub_codigo'],
								7 => $socio['LiquidacionSocioNoimputada']['importe_adebitar'] ,
								8 => 'I'
				
				);	
				$socio['LiquidacionSocioNoimputada']['intercambio'] = $oBanco->armaStringDebitoCJP($campos,false);	
				$socios[$i] = $socio;
				
			endif;
			
		endforeach;
		
//		debug($socios);
//		exit;

		return $socios;
		
	}
	
	
	function getErroresDisketteCJP($liquidacion_id){
		$conditions = array();
		$conditions['LiquidacionSocioNoimputada.liquidacion_id'] = $liquidacion_id;
		$conditions['LiquidacionSocioNoimputada.error_cbu'] = 1;
		
		$socios = $this->find('all',array('conditions' => $conditions,
										'order' => array('LiquidacionSocioNoimputada.apenom,LiquidacionSocioNoimputada.registro'),
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
		$conditions['LiquidacionSocioNoimputada.liquidacion_id'] = $liquidacion_id;
		$conditions['LiquidacionSocioNoimputada.importe_adebitar >'] = 0;
		if(!empty($socio_id)) $conditions['LiquidacionSocioNoimputada.socio_id'] = $socio_id;
		
		$socios = $this->find('all',array('conditions' => $conditions,
										'order' => array('LiquidacionSocioNoimputada.apenom,LiquidacionSocioNoimputada.registro'),
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
			
			$socio['LiquidacionSocioNoimputada']['error_intercambio'] = null;
			$socio['LiquidacionSocioNoimputada']['error_cbu'] = 0;			
			
			//CONTROLAR EL TIPO
			if(!isset($socio['LiquidacionSocioNoimputada']['tipo'])){
				$CONTROL = FALSE;
				$TIPOERROR = "TIPO_INCORRECTO";
				$socio['LiquidacionSocioNoimputada']['error_intercambio'] = "$TIPOERROR";
				$socio['LiquidacionSocioNoimputada']['error_cbu'] = 1;				
			}
			//CONTROLAR EL TIPO EN BASE AL NUMERO DE LEY
			if(!$oBEN->controlTipoLeyCJP($socio['LiquidacionSocioNoimputada']['tipo'],$socio['LiquidacionSocioNoimputada']['nro_ley'])){
				$CONTROL = FALSE;
				$TIPOERROR = "LEY_TIPO_ERROR";
				$socio['LiquidacionSocioNoimputada']['error_intercambio'] = "$TIPOERROR";
				$socio['LiquidacionSocioNoimputada']['error_cbu'] = 1;				
			}
			
			
			$campos = array(
							1 => $socio['LiquidacionSocioNoimputada']['tipo'],
							2 => $socio['LiquidacionSocioNoimputada']['nro_ley'],
							3 => $socio['LiquidacionSocioNoimputada']['nro_beneficio'],
							4 => $socio['LiquidacionSocioNoimputada']['sub_beneficio'],
							5 => $socio['LiquidacionSocioNoimputada']['codigo_dto'],
							6 => $socio['LiquidacionSocioNoimputada']['sub_codigo'],
							7 => $socio['LiquidacionSocioNoimputada']['importe_adebitar'],
							8 => 'I',
							9 => (parent::is_date($socio['LiquidacionSocioNoimputada']['fecha_otorgamiento']) ? $socio['LiquidacionSocioNoimputada']['fecha_otorgamiento'] : null),
							10 => $socio['LiquidacionSocioNoimputada']['importe_total'],
							11 => $socio['LiquidacionSocioNoimputada']['cuotas'],
							12 => $socio['LiquidacionSocioNoimputada']['importe_cuota'],
							13 => $socio['LiquidacionSocioNoimputada']['importe_deuda'],
							14 => $socio['LiquidacionSocioNoimputada']['importe_deuda_vencida'],
//							14 => $socio['LiquidacionSocioNoimputada']['importe_deuda'] - $socio['LiquidacionSocioNoimputada']['importe_deuda_no_vencida'],
							15 => $socio['LiquidacionSocioNoimputada']['importe_deuda_no_vencida'],
							16 => $socio['LiquidacionSocioNoimputada']['orden_descuento_id'],
			
			);	
			
			//controles exigidos por la caja
			#1) CAMPO_13 = CAMPO_14 + CAMPO_15
			
//			if($socio['LiquidacionSocioNoimputada']['socio_id'] == 10966) debug($campos);
			
			if($socio['LiquidacionSocioNoimputada']['sub_codigo'] == 1):
			
				if(round($campos[13],2) != round((round($campos[14],2) + round($campos[15],2)),2)){
					$socio['LiquidacionSocioNoimputada']['error_cbu'] = 1;
					$socio['LiquidacionSocioNoimputada']['error_intercambio'] = "DEUDA <> VENCIDO + NO VENCIDO";
					$CONTROL = FALSE;
				}
				if(intval($campos[11]) != round($campos[10] / $campos[12],0)){
					$socio['LiquidacionSocioNoimputada']['error_cbu'] = 1;
					$socio['LiquidacionSocioNoimputada']['error_intercambio'] = "IMPORTE TOTAL / IMPORTE_CUOTA <> CUOTAS";
					$CONTROL = FALSE;
				}
				if($campos[13] > $campos[10]){
					$socio['LiquidacionSocioNoimputada']['error_cbu'] = 1;
					$socio['LiquidacionSocioNoimputada']['error_intercambio'] = "SALDO > IMPORTE TOTAL";
					$CONTROL = FALSE;
				}
				if($campos[16] == 0){
					$socio['LiquidacionSocioNoimputada']['error_cbu'] = 1;
					$socio['LiquidacionSocioNoimputada']['error_intercambio'] = "FALTA NRO.OPERACION";
					$CONTROL = FALSE;
				}
				
				if(empty($campos[9])){
					$socio['LiquidacionSocioNoimputada']['error_cbu'] = 1;
					$socio['LiquidacionSocioNoimputada']['error_intercambio'] = "FALTA FECHA OTORGAMIENTO";
					$CONTROL = FALSE;
				}

				if(date('Ymd',strtotime($campos[9])) > date('Ymd')){
					$socio['LiquidacionSocioNoimputada']['error_cbu'] = 1;
					$socio['LiquidacionSocioNoimputada']['error_intercambio'] = "FECHA OTORGAMIENTO SUPERIOR A HOY";
					$CONTROL = FALSE;
				}				
				
				
//				if((int)($campos[15] / $campos[12]) != ($campos[15] / $campos[12])){
//					$socio['LiquidacionSocioNoimputada']['error_cbu'] = 1;
//					$socio['LiquidacionSocioNoimputada']['ERROR_INTERCAMBIO'] = "ERROR EN SALDO_AVENCER";
//					$CONTROL = FALSE;
//				}				

			endif;

			if($CONTROL) $socio['LiquidacionSocioNoimputada']['intercambio'] = $oBanco->armaStringDebitoCJP($campos);
			else $socio['LiquidacionSocioNoimputada']['intercambio'] = null;
		
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
													'conditions' => array('LiquidacionSocioNoimputada.codigo_empresa = GlobalDato.id')
													),		
										),
										'conditions' => array(
																	'LiquidacionSocioNoimputada.liquidacion_id' => $datos['LiquidacionSocioNoimputada']['liquidacion_id'],
																	'LiquidacionSocioNoimputada.turno_pago' => $datos['LiquidacionSocioNoimputada']['turno_pago'],
																	'LiquidacionSocioNoimputada.importe_adebitar >' => 0,
																	'LiquidacionSocioNoimputada.diskette' => 1
																),
										'order' => array('GlobalDato.concepto_1,LiquidacionSocioNoimputada.apenom,LiquidacionSocioNoimputada.registro')								
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
		$liquidacion = $this->read(null,$datos['LiquidacionSocioNoimputada']['liquidacion_id']);		
		// si la liquidacion esta cerrada no vuelvo a procesar los datos del diskette
		if($liquidacion['Liquidacion']['imputada'] == 1) return $socios;

		foreach($socios as $idx => $socio){
			
			$socio['LiquidacionSocioNoimputada']['ERROR_INTERCAMBIO'] = "OK";
			
			$socio['LiquidacionSocioNoimputada']['error_cbu'] = 0;
			$socio['LiquidacionSocioNoimputada']['fecha_debito'] = parent::armaFecha($datos['LiquidacionSocioNoimputada']['fecha_debito']);
			
			$calificacion = $oSOCIO->getUltimaCalificacion($socio['LiquidacionSocioNoimputada']['socio_id'],$socio['LiquidacionSocioNoimputada']['persona_beneficio_id']);
			$socio['LiquidacionSocioNoimputada']['ultima_calificacion'] = $calificacion;
			
			//busco la marca en la global de los que no envia diskette segun la calificacion del socio
			$noEnvia = parent::GlobalDato('logico_2',$calificacion);

			//validar CBU
			if(parent::validarCBU($socio['LiquidacionSocioNoimputada']['cbu']) && $noEnvia == 0):
				
				//BANCO CORDOBA
				if($datos['LiquidacionSocioNoimputada']['banco_intercambio'] == '00020'):
				
					$campos = array(
									2 => $socio['LiquidacionSocioNoimputada']['sucursal'],
									5 => $socio['LiquidacionSocioNoimputada']['nro_cta_bco'],
									6 => $socio['LiquidacionSocioNoimputada']['importe_adebitar'],
									7 => $socio['LiquidacionSocioNoimputada']['fecha_debito'],
									9 => 0,
									10 => $socio['LiquidacionSocioNoimputada']['cbu'],
									11 => $socio['LiquidacionSocioNoimputada']['registro'],
									12 => $this->__genDebitoID($socio)
					
					);
					
					if(intval($socio['LiquidacionSocioNoimputada']['sucursal']) != 0):
						$socio['LiquidacionSocioNoimputada']['intercambio'] = $oBanco->armaStringDebitoBcoCba($campos);
					else:
						$socio['LiquidacionSocioNoimputada']['intercambio'] = NULL;
						$socio['LiquidacionSocioNoimputada']['ERROR_INTERCAMBIO'] = "ERROR_SUCURSAL";					
					endif;
				//BANCO STANDAR				
				elseif($datos['LiquidacionSocioNoimputada']['banco_intercambio'] == '00430'):
				
					$campos = array(
									3 => substr($socio['LiquidacionSocioNoimputada']['cbu'],0,8),
									4 => substr($socio['LiquidacionSocioNoimputada']['cbu'],8,14),
									5 => $this->__genDebitoID($socio),
									6 => $socio['LiquidacionSocioNoimputada']['importe_adebitar'],
									7 => $socio['LiquidacionSocioNoimputada']['id'],
									8 => $socio['LiquidacionSocioNoimputada']['fecha_debito']					
					);
					$socio['LiquidacionSocioNoimputada']['intercambio'] = $oBanco->armaStringDebitoStandarBank($campos);
				
				//BANCO NACION
				elseif($datos['LiquidacionSocioNoimputada']['banco_intercambio'] == '00011'):
					
//					DEBUG($socio);
					$campos = array(
									2 => $socio['LiquidacionSocioNoimputada']['sucursal'],
									4 => $socio['LiquidacionSocioNoimputada']['nro_cta_bco'],
									5 => $socio['LiquidacionSocioNoimputada']['importe_adebitar'],
									9 => $socio['LiquidacionSocioNoimputada']['socio_id'],	
									10 => $this->__genDebitoID($socio),
					);
					
					//controlo la longitud del nro de cuenta
					if(strlen(trim($socio['LiquidacionSocioNoimputada']['nro_cta_bco'])) > 11){
						$socio['LiquidacionSocioNoimputada']['error_cbu'] = 1;
						$socio['LiquidacionSocioNoimputada']['intercambio'] = NULL;						
						$socio['LiquidacionSocioNoimputada']['ERROR_INTERCAMBIO'] = "NRO_CTA > 11DIG";	
					}else{
						$socio['LiquidacionSocioNoimputada']['intercambio'] = $oBanco->armaStringDebitoBcoNacion($campos);
					}

				//BANCO CREDICOOP
				elseif($datos['LiquidacionSocioNoimputada']['banco_intercambio'] == '00191'):	
					$campos = array(
									1 => substr($socio['LiquidacionSocioNoimputada']['cbu'],0,3),
									2 => substr($socio['LiquidacionSocioNoimputada']['cbu'],0,8),
									3 => substr($socio['LiquidacionSocioNoimputada']['cbu'],8,14),
									4 => $socio['LiquidacionSocioNoimputada']['socio_id'],
									5 => $socio['LiquidacionSocioNoimputada']['importe_adebitar'],
									6 => $socio['LiquidacionSocioNoimputada']['id'],
									7 => $socio['LiquidacionSocioNoimputada']['fecha_debito']					
					);
					$socio['LiquidacionSocioNoimputada']['intercambio'] = $oBanco->armaStringDebitoBancoCrediCoop($campos);
				
				
				//BANCO INTERCAMBIO NO CREADO						
				else:
					
					$socio['LiquidacionSocioNoimputada']['intercambio'] = NULL;
					$socio['LiquidacionSocioNoimputada']['ERROR_INTERCAMBIO'] = "SIN_FORMULA";
				
				endif;
				
			else:
			
				$socio['LiquidacionSocioNoimputada']['error_cbu'] = 1;
				$socio['LiquidacionSocioNoimputada']['intercambio'] = NULL;
				if($noEnvia==1)$socio['LiquidacionSocioNoimputada']['ERROR_INTERCAMBIO'] = "NO_ENVIA";
				else $socio['LiquidacionSocioNoimputada']['ERROR_INTERCAMBIO'] = "ERROR_CBU";

				
			endif;
			
			$socio['LiquidacionSocioNoimputada']['banco_intercambio'] = $datos['LiquidacionSocioNoimputada']['banco_intercambio'];

			$socio['LiquidacionSocioNoimputada']['turno'] = $oTURNO->getDescripcionByTruno($socio['LiquidacionSocioNoimputada']['turno_pago']);
			$socio['LiquidacionSocioNoimputada']['empresa'] = parent::GlobalDato('concepto_1',$socio['LiquidacionSocioNoimputada']['codigo_empresa']);

			if(empty($socio['LiquidacionSocioNoimputada']['turno_pago']) || $socio['LiquidacionSocioNoimputada']['turno_pago'] == "SDATO"){
				$socio['LiquidacionSocioNoimputada']['empresa'] = "*** SIN DATOS ***";
				$socio['LiquidacionSocioNoimputada']['turno'] = "SDATO";
			}					
			
//			$socio['LiquidacionSocioNoimputada']['descripcion_turno'] = $socio['LiquidacionSocioNoimputada']['empresa'] . (!empty($socio['LiquidacionSocioNoimputada']['turno']) ? " - ". $socio['LiquidacionSocioNoimputada']['turno'] : "");
			$socio['LiquidacionSocioNoimputada']['descripcion_turno'] = (!empty($socio['LiquidacionSocioNoimputada']['turno']) ? $socio['LiquidacionSocioNoimputada']['turno'] : "");
			
			
			//VALIDO LOS MAXIMOS Y MINIMOS
			if($socio['LiquidacionSocioNoimputada']['importe_adebitar'] < $this->impoMinDtoCBU):
				$socio['LiquidacionSocioNoimputada']['error_cbu'] = 1;
				$socio['LiquidacionSocioNoimputada']['intercambio'] = NULL;
				$socio['LiquidacionSocioNoimputada']['ERROR_INTERCAMBIO'] = "EL IMPORTE A DEBITAR ES INFERIOR AL MINIMO (".$this->impoMinDtoCBU.")";			
			endif;
			
			if($socio['LiquidacionSocioNoimputada']['importe_adebitar'] > $this->impoMaxDtoCBU):
				$socio['LiquidacionSocioNoimputada']['error_cbu'] = 1;
				$socio['LiquidacionSocioNoimputada']['intercambio'] = NULL;
				$socio['LiquidacionSocioNoimputada']['ERROR_INTERCAMBIO'] = "EL IMPORTE A DEBITAR ES SUPERIOR AL MAXIMO (".$this->impoMaxDtoCBU.")";			
			endif;
			
			if(!$this->save($socio)):
				$ERROR = 1;
				break;
			endif;
			
			$socios[$idx] = $socio;
			
//			if(empty($socio['LiquidacionSocioNoimputada']['intercambio']) && $socio['LiquidacionSocioNoimputada']['error_cbu'] == 0){
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
            $idDebito = str_pad($datos['LiquidacionSocioNoimputada']['socio_id'], 12, '0', STR_PAD_LEFT);
            $idDebito .= str_pad($datos['LiquidacionSocioNoimputada']['liquidacion_id'], 8, '0', STR_PAD_LEFT);
            $idDebito .= str_pad($datos['LiquidacionSocioNoimputada']['registro'], 2, '0', STR_PAD_LEFT);	
        }else{
            //genero un id de 10 lugares con el socio y la liquidacion
            $idDebito = str_pad($datos['LiquidacionSocioNoimputada']['liquidacion_id'], 4, '0', STR_PAD_LEFT);
            $idDebito .= str_pad($datos['LiquidacionSocioNoimputada']['socio_id'], 6, '0', STR_PAD_LEFT);
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
																	'LiquidacionSocioNoimputada.tipo_documento' => $tipo,
																	'LiquidacionSocioNoimputada.documento' => $nro,
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
																	'LiquidacionSocioNoimputada.liquidacion_id' => $liquidacion_id,
																	'LiquidacionSocioNoimputada.socio_id' => $socio_id,
																),
											'order' => array('LiquidacionSocioNoimputada.registro')					
		));
		foreach($socios as $idx => $socio){
	
			//saco el banco de intercambio
			$banco = parent::getBanco($socio['LiquidacionSocioNoimputada']['banco_intercambio']);
			$socio['LiquidacionSocioNoimputada']['banco_intercambio_desc'] = $banco['Banco']['nombre'];
			$socio['LiquidacionSocioNoimputada']['turno'] = substr(trim($socio['LiquidacionSocioNoimputada']['turno_pago']),-5,5);
			
			
			$organismo = substr($socio['LiquidacionSocioNoimputada']['codigo_organismo'],8,2);
			
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
																	'LiquidacionSocioNoimputada.persona_beneficio_id' => $persona_beneficio_id,
																),
		));
		return $socios;	
	}	
	
	
	function getPeriodosNoLiquidados($socio_id){
		$sql = "select distinct periodo from 
				liquidaciones where id not in (select liquidacion_id from liquidacion_socio_noimputadas where socio_id = $socio_id) and cerrada = 0 and imputada = 0";
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
	function liquidar($socio_id,$periodo,$organismo,$liquidacion_id,$generaLiqSocio=true,$pre_imputacion=false,$cuotaSocialSoloDeuda=false,$CONTROL_NACION=false,$BANCO_CONTROL = null,$DISCRIMINA_PERMANENTES = FALSE,$tipoFiltro=0){
		
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBen = new PersonaBeneficio();	

		App::import('Model','Mutual.MutualAdicional');
		$oADICIONAL = new MutualAdicional();		
		
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();	
		
		App::import('Model','Mutual.LiquidacionCuotaNoimputada');
		$oLC = new LiquidacionCuotaNoimputada();
		
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
		if (!$oLC->deleteAll("LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id AND LiquidacionCuotaNoimputada.socio_id = $socio_id")) {
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
			if(!$this->deleteAll("LiquidacionSocioNoimputada.liquidacion_id = $liquidacion_id and LiquidacionSocioNoimputada.socio_id = $socio_id"))$ERROR = true;
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
					OrdenDescuento.activo		 
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
                        and odc.persona_beneficio_id = be.id    
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
                        $adicionales = $oAP->generarAdicionalNoImputada($liquidacion_id, $socio_id, $organismo,$periodo, 'MUTUSICUMUTU', $pre_imputacion,FALSE);
			
			if($ERROR){
                            $status[0] = 1;
                            $status[1] = $oAP->notificaciones;
                            return $status;
                        }
			
			###########################################################################################################################
			#PASO 5: GENERAR LA CABECERA DE LIQUIDACION DEL SOCIO
			###########################################################################################################################

			if($generaLiqSocio):
			
				$LiquidacionSocios = $oLC->getInfoDto_AMAN($liquidacion_id,$socio_id,$periodo,$organismo,$CONTROL_NACION,$BANCO_CONTROL,$DISCRIMINA_PERMANENTES);
//				debug($LiquidacionSocios);
//				exit;

				if(!$this->deleteAll("LiquidacionSocioNoimputada.liquidacion_id = $liquidacion_id and LiquidacionSocioNoimputada.socio_id = $socio_id"))$ERROR = true;		
				if(!empty($LiquidacionSocios)):
					$tmp = array();
					foreach($LiquidacionSocios as $liquidacion):
						$datos['LiquidacionSocioNoimputada'] = $liquidacion['LiquidacionCuotaNoimputada'];
                                                $datos['LiquidacionSocioNoimputada']['id'] = 0;
//						$this->id = 0;
                                                $this->auditable = FALSE;
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
				endif;
			endif;
	
		else:
			//NO GENERA LIQUIDACION, BORRAR LA CABECERA DE LIQUIDACION DEL SOCIO
			if(!$this->deleteAll("LiquidacionSocioNoimputada.liquidacion_id = $liquidacion_id and LiquidacionSocioNoimputada.socio_id = $socio_id"))$ERROR = true;	
			
		endif;	
		
                $this->query("CALL SP_LIQUIDA_DEUDA_SCORING_NOIMPUTADAS($liquidacion_id,$socio_id)");
        
		###########################################################################################################################
		#FIN LIQUIDACION
		###########################################################################################################################
		return $status;
		
	}
	
	
	function getConsumosNoDescontadosCjpParaDebitoByCBU($liquidacion_id,$bancoIntercambio=null,$fechaDebito=null){
		
		$datos = array();
		
//		$sql = "select 
//				LiquidacionSocioNoimputada.socio_id,
//				LiquidacionSocioNoimputada.tipo,
//				LiquidacionSocioNoimputada.nro_ley,
//				LiquidacionSocioNoimputada.nro_beneficio,
//				LiquidacionSocioNoimputada.sub_beneficio,
//				LiquidacionSocioNoimputada.documento,
//				LiquidacionSocioNoimputada.apenom,
//				PersonaBeneficio.cbu,
//				PersonaBeneficio.nro_sucursal,
//				PersonaBeneficio.nro_cta_bco,
//				LiquidacionSocioNoimputada.cbu,
//				sum(LiquidacionSocioNoimputada.importe_cuota) as importe_cuota
//				from liquidacion_socio_noimputadas as LiquidacionSocio
//				inner join socios as Socio on (Socio.id = LiquidacionSocioNoimputada.socio_id)
//				inner join persona_beneficios as PersonaBeneficio on (PersonaBeneficio.persona_id = Socio.persona_id and PersonaBeneficio.activo = 1 and PersonaBeneficio.codigo_beneficio = 'MUTUCORG2201')
//				where LiquidacionSocioNoimputada.liquidacion_id = $liquidacion_id 
//				and LiquidacionSocioNoimputada.sub_codigo = '1'
//				and LiquidacionSocioNoimputada.orden_descuento_id not in (select orden_descuento_id from liquidacion_socio_rendiciones
//				as LiquidacionSocioRendicion 
//				where LiquidacionSocioRendicion.liquidacion_id = LiquidacionSocioNoimputada.liquidacion_id
//				and LiquidacionSocioRendicion.sub_codigo = '1')
//				and ifnull(PersonaBeneficio.cbu,'') <> ''
//				group by PersonaBeneficio.cbu
//				order by LiquidacionSocioNoimputada.apenom";
		
		
		$sql = "select 
                                LiquidacionSocioNoimputada.liquidacion_id,
				LiquidacionSocioNoimputada.id,
				LiquidacionSocioNoimputada.codigo_organismo,
				LiquidacionSocioNoimputada.socio_id,
				LiquidacionSocioNoimputada.tipo,
				LiquidacionSocioNoimputada.nro_ley,
				LiquidacionSocioNoimputada.nro_beneficio,
				LiquidacionSocioNoimputada.sub_beneficio,
				LiquidacionSocioNoimputada.documento,
				LiquidacionSocioNoimputada.apenom,
				LiquidacionSocioNoimputada.cbu,
				sum(LiquidacionSocioNoimputada.importe_cuota) as importe_cuota,
				sum(LiquidacionSocioNoimputada.importe_adebitar) as importe_adebitar
				from liquidacion_socio_noimputadas as LiquidacionSocio
				where LiquidacionSocioNoimputada.liquidacion_id = $liquidacion_id 
				and LiquidacionSocioNoimputada.sub_codigo = '1'
				and LiquidacionSocioNoimputada.orden_descuento_id not in (select orden_descuento_id from liquidacion_socio_rendiciones
				as LiquidacionSocioRendicion 
				where LiquidacionSocioRendicion.liquidacion_id = LiquidacionSocioNoimputada.liquidacion_id
				and LiquidacionSocioRendicion.sub_codigo = '1')
				group by LiquidacionSocioNoimputada.socio_id
				order by LiquidacionSocioNoimputada.apenom LIMIT 10";		
		
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
				
//				$resultado['LiquidacionSocioNoimputada']['cbu'] = $resultado['PersonaBeneficio']['cbu'];

				
				if($resultado[0]['importe_cuota'] == 0) $resultado[0]['importe_cuota'] = $resultado[0]['importe_adebitar'];
				
				//busco el beneficio para sacar el CBU
				$beneficio = $oSOCIO->getUltimoBeneficioActivosByCodOrganismo($resultado['LiquidacionSocioNoimputada']['socio_id'],'MUTUCORG2201');
				if(!empty($beneficio['PersonaBeneficio']['cbu'])) $resultado['LiquidacionSocioNoimputada']['cbu'] = $beneficio['PersonaBeneficio']['cbu'];
				
				
//				debug($resultado);
				
				if(!empty($resultado['LiquidacionSocioNoimputada']['cbu'])):
				
//					if(!empty($beneficio['PersonaBeneficio']['cbu'])) $resultado['LiquidacionSocioNoimputada']['cbu'] = $beneficio['PersonaBeneficio']['cbu'];
			
					$resultado['LiquidacionSocioNoimputada']['importe_original'] = $resultado[0]['importe_cuota'];
					$resultado['LiquidacionSocioNoimputada']['importe_adebitar'] = $resultado['LiquidacionSocioNoimputada']['importe_original'];
					//controlo el limite del banco
//					if($resultado['LiquidacionSocioNoimputada']['importe_original'] > $this->impoMaxDtoCBU) $resultado['LiquidacionSocioNoimputada']['importe_adebitar'] = $this->impoMaxDtoCBU;
//					else $resultado['LiquidacionSocioNoimputada']['importe_adebitar'] = $resultado['LiquidacionSocioNoimputada']['importe_original'];
					
					
					$decoCBU = $oBANCO->deco_cbu($resultado['LiquidacionSocioNoimputada']['cbu']);	
					$resultado['LiquidacionSocioNoimputada']['cbu_banco'] = $decoCBU['banco_id'];
					$resultado['LiquidacionSocioNoimputada']['cbu_sucursal'] = $decoCBU['sucursal'];
					$resultado['LiquidacionSocioNoimputada']['cbu_nro_cta_bco'] = $decoCBU['nro_cta_bco'];
					
					$resultado['LiquidacionSocioNoimputada']['error_cbu'] = 0;
					
//					$resultado['LiquidacionSocioNoimputada']['cbu_ok'] = $oBANCO->validarCBU($resultado['LiquidacionSocioNoimputada']['cbu']);
//					$resultado['LiquidacionSocioNoimputada']['cbu_msg'] = "";
					
//					if($resultado['LiquidacionSocioNoimputada']['cbu_ok'] == 0) $resultado['LiquidacionSocioNoimputada']['cbu_msg'] = "CBU NO VALIDO";
//					if(intval($decoCBU['nro_cta_bco']) == 0){
//						$resultado['LiquidacionSocioNoimputada']['error_cbu'] = 1;
//						$resultado['LiquidacionSocioNoimputada']['cbu_msg'] = "CUENTA NO VALIDA";
//					}
					
//					if($resultado['LiquidacionSocioNoimputada']['importe_adebitar'] < $this->impoMinDtoCBU){
//						$resultado['LiquidacionSocioNoimputada']['cbu_ok'] = 0;
//						$resultado['LiquidacionSocioNoimputada']['cbu_msg'] = "IMPORTE INF AL MINIMO POR CBU";						
//					}
					
//					if(!empty($bancoIntercambio) && $resultado['LiquidacionSocioNoimputada']['cbu_ok'] == 1):
					if(!empty($bancoIntercambio)):
					
						//GENERAR UN ID DE DEBITO DE 22
						$TIPO = substr(str_pad(trim($resultado['LiquidacionSocioNoimputada']['tipo']), 1, '0', STR_PAD_LEFT),-1);
						$LEY = substr(str_pad(trim($resultado['LiquidacionSocioNoimputada']['nro_ley']), 2, '0', STR_PAD_LEFT),-2);
						$NROBENEFICIO = substr(str_pad(trim($resultado['LiquidacionSocioNoimputada']['nro_beneficio']), 6, '0', STR_PAD_LEFT),-6);
						$SUBENEFICIO = substr(str_pad(trim($resultado['LiquidacionSocioNoimputada']['sub_beneficio']), 2, '0', STR_PAD_LEFT),-2);								
						$SOCIOID = str_pad($resultado['LiquidacionSocioNoimputada']['socio_id'], 11, '0', STR_PAD_LEFT);
	
						$IDDEBITO = $TIPO.$LEY.$NROBENEFICIO.$SUBENEFICIO.$SOCIOID;					
					
						$importe 			= $resultado['LiquidacionSocioNoimputada']['importe_adebitar'];
						$registroNro 		= $reg;
						$idDebito 			= $IDDEBITO;
						$liquidacionSocioId = $resultado['LiquidacionSocioNoimputada']['id'];
						$socioId 			= $resultado['LiquidacionSocioNoimputada']['socio_id'];
						$sucursal 			= $resultado['LiquidacionSocioNoimputada']['cbu_sucursal'];
						$cuenta 			= $resultado['LiquidacionSocioNoimputada']['cbu_nro_cta_bco'];
						$cbu 				= $resultado['LiquidacionSocioNoimputada']['cbu'];
						$codOrganismo 		= $resultado['LiquidacionSocioNoimputada']['codigo_organismo'];
						$calificacion 		= $resultado['LiquidacionSocioNoimputada']['ultima_calificacion'];
                                                $liquidacion_id		= $resultado['LiquidacionSocioNoimputada']['liquidacion_id'];
						
						$registro = $oBANCO->genRegistroDisketteBanco($bancoIntercambio,$fechaDebito,$importe,$registroNro,$idDebito,$liquidacionSocioId,$socioId,$sucursal,$cuenta,$cbu,$codOrganismo,$calificacion,NULL,$liquidacion_id);
						
//						debug($registro);
						
						$resultado['LiquidacionSocioNoimputada']['importe_adebitar'] = $registro['importe_debito'];
						$resultado['LiquidacionSocioNoimputada']['error_cbu'] = $registro['error'];
						$resultado['LiquidacionSocioNoimputada']['intercambio'] = $registro['cadena'];
						if(!empty($registro['mensaje']))$resultado['LiquidacionSocioNoimputada']['ERROR_INTERCAMBIO'] = $registro['mensaje'];
						
						$resultado['LiquidacionSocioNoimputada']['cbu_sucursal'] = $registro['sucursal_formed'];
						$resultado['LiquidacionSocioNoimputada']['cbu_nro_cta_bco'] = $registro['cuenta_formed'];					
											
						$resultado['LiquidacionSocioNoimputada']['fecha_debito'] = $fechaDebito;
						
						$ACUM_REGISTROS++;
						$ACUM_IMPORTE += $resultado['LiquidacionSocioNoimputada']['importe_adebitar'];
						
						if($registro['error'] == 0){
							$ACUM_REGISTROS_DISK++;
							$ACUM_IMPORTE_DISK += $resultado['LiquidacionSocioNoimputada']['importe_adebitar'];
							array_push($registros,$resultado);
						}else{
							$resultado['LiquidacionSocioNoimputada']['importe_adebitar'] = 0;
							$ACUM_IMPORTE_ERROR += $resultado['LiquidacionSocioNoimputada']['importe_adebitar'];
							$ACUM_REGISTROS_ERROR++;
						}						
	
																			
					endif;
					
					$datos[$idx]['LiquidacionSocioNoimputada'] = $resultado['LiquidacionSocioNoimputada'];
					
				
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
		
		$registros = Set::extract("/LiquidacionSocioNoimputada[error_cbu=0]/intercambio",$datos);
		$importes = Set::extract("/LiquidacionSocioNoimputada[error_cbu=0]/importe_adebitar",$datos);
		$resultados1['diskette'] = $oBANCO->genDisketteBanco($bancoIntercambio,$fechaDebito,$ACUM_REGISTROS_DISK,$ACUM_IMPORTE_DISK,$datos['LiquidacionSocioNoimputada']['nro_archivo'],$registros);
		
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
													'conditions' => array('LiquidacionSocioNoimputada.codigo_empresa = GlobalDato.id')
													),		
										),
										'conditions' => array(
																	'LiquidacionSocioNoimputada.liquidacion_id' => $datos['LiquidacionSocioNoimputada']['liquidacion_id'],
																	'LiquidacionSocioNoimputada.turno_pago' => $datos['LiquidacionSocioNoimputada']['turno_pago'],
																	'LiquidacionSocioNoimputada.importe_adebitar >' => 0,
																	'LiquidacionSocioNoimputada.diskette' => 1
																),
										'order' => array('GlobalDato.concepto_1,LiquidacionSocioNoimputada.apenom,LiquidacionSocioNoimputada.registro'),
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
		$liquidacion = $this->read(null,$datos['LiquidacionSocioNoimputada']['liquidacion_id']);		
		// si la liquidacion esta cerrada no vuelvo a procesar los datos del diskette
		if($liquidacion['Liquidacion']['imputada'] == 1) return $socios;
		
		
		$ACUM_REGISTROS = 0;
		$ACUM_IMPORTE = $ACUM_IMPORTE_DISK = $ACUM_REGISTROS = $ACUM_REGISTROS_ERROR =  $ACUM_REGISTROS_DISK = 0;

		$registros = array();

		foreach($socios as $idx => $socio){
			
			$socio['LiquidacionSocioNoimputada']['ERROR_INTERCAMBIO'] = "OK";
			
			$socio['LiquidacionSocioNoimputada']['error_cbu'] = 0;
			$socio['LiquidacionSocioNoimputada']['fecha_debito'] = parent::armaFecha($datos['LiquidacionSocioNoimputada']['fecha_debito']);
			
			$calificacion = $oSOCIO->getUltimaCalificacion($socio['LiquidacionSocioNoimputada']['socio_id'],$socio['LiquidacionSocioNoimputada']['persona_beneficio_id']);
			$socio['LiquidacionSocioNoimputada']['ultima_calificacion'] = $calificacion;
			
			$bancoIntercambio	= $datos['LiquidacionSocioNoimputada']['banco_intercambio'];
			$fechaDebito		= $socio['LiquidacionSocioNoimputada']['fecha_debito'];
			$importe 			= $socio['LiquidacionSocioNoimputada']['importe_adebitar'];
			$registroNro 		= $socio['LiquidacionSocioNoimputada']['registro'];
			$idDebito 			= $this->__genDebitoID($socio);
			$liquidacionSocioId = $socio['LiquidacionSocioNoimputada']['id'];
			$socioId 			= $socio['LiquidacionSocioNoimputada']['socio_id'];
			$sucursal 			= $socio['LiquidacionSocioNoimputada']['sucursal'];
			$cuenta 			= $socio['LiquidacionSocioNoimputada']['nro_cta_bco'];
			$cbu 				= $socio['LiquidacionSocioNoimputada']['cbu'];
			$codOrganismo 		= $socio['LiquidacionSocioNoimputada']['codigo_organismo'];
			$calificacion 		= $socio['LiquidacionSocioNoimputada']['ultima_calificacion'];
			$zenrise = NULL;
			$registro = $oBanco->genRegistroDisketteBanco($bancoIntercambio,$fechaDebito,$importe,$registroNro,$idDebito,$liquidacionSocioId,$socioId,$sucursal,$cuenta,$cbu,$codOrganismo,$calificacion,$zenrise);
			
			$socio['LiquidacionSocioNoimputada']['importe_adebitar'] = $registro['importe_debito'];
			$socio['LiquidacionSocioNoimputada']['error_cbu'] = $registro['error'];
			$socio['LiquidacionSocioNoimputada']['intercambio'] = $registro['cadena'];
			if(!empty($registro['mensaje']))$socio['LiquidacionSocioNoimputada']['ERROR_INTERCAMBIO'] = $registro['mensaje'];
			
			$socio['LiquidacionSocioNoimputada']['sucursal'] = $registro['sucursal_formed'];
			$socio['LiquidacionSocioNoimputada']['nro_cta_bco'] = $registro['cuenta_formed'];
			
			
			$ACUM_REGISTROS++;
			$ACUM_IMPORTE += $socio['LiquidacionSocioNoimputada']['importe_adebitar'];
			
			if($registro['error'] == 0){
				$ACUM_REGISTROS_DISK++;
				$ACUM_IMPORTE_DISK += $socio['LiquidacionSocioNoimputada']['importe_adebitar'];
				array_push($registros,$socio);
			}else{
				$ACUM_REGISTROS_ERROR++;
			}
			
			//ACTUALIZO LA TABLA LIQUIDACION_SOCIOS
			$update = array(
				'LiquidacionSocioNoimputada.importe_adebitar' => $socio['LiquidacionSocioNoimputada']['importe_adebitar'],
				'LiquidacionSocioNoimputada.error_cbu' => $socio['LiquidacionSocioNoimputada']['error_cbu'],
				'LiquidacionSocioNoimputada.banco_intercambio' => "'$bancoIntercambio'",
				'LiquidacionSocioNoimputada.fecha_debito' => "'$fechaDebito'",
				'LiquidacionSocioNoimputada.intercambio' => "'".$socio['LiquidacionSocioNoimputada']['intercambio']."'",
				'LiquidacionSocioNoimputada.sucursal' => "'".$socio['LiquidacionSocioNoimputada']['sucursal']."'",
				'LiquidacionSocioNoimputada.nro_cta_bco' => "'".$socio['LiquidacionSocioNoimputada']['nro_cta_bco']."'",
				'LiquidacionSocioNoimputada.ultima_calificacion' => "'".$socio['LiquidacionSocioNoimputada']['ultima_calificacion']."'",
			);
			
			if(!$this->updateAll($update,array('LiquidacionSocioNoimputada.id' => $socio['LiquidacionSocioNoimputada']['id']))){
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
		
		$registros = Set::extract("/LiquidacionSocioNoimputada[error_cbu=0]/intercambio",$socios);
		$importes = Set::extract("/LiquidacionSocioNoimputada[error_cbu=0]/importe_adebitar",$socios);
		$resultados['diskette'] = $oBanco->genDisketteBanco($bancoIntercambio,$fechaDebito,$ACUM_REGISTROS_DISK,$ACUM_IMPORTE_DISK,$datos['LiquidacionSocioNoimputada']['nro_archivo'],$registros);
		
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
//		debug(count(Set::extract("/LiquidacionSocioNoimputada[turno_pago=OP007]",$socios)));
//		debug(Set::extract("/LiquidacionSocioNoimputada[turno_pago=OP007]",$socios));
//		
//		exit;

		$aTurnos = Set::sort($parametros['LiquidacionSocioNoimputada']['turno_pago'], '{n}.beneficiario_apenom', 'asc');
		$tmpResumeByTurno = array();
		
		foreach($parametros['LiquidacionSocioNoimputada']['turno_pago'] as $turno => $dato):
		
			list($cantidad,$impoDto,$impoDebito,$empresaTurnoDesc) = explode("|", $dato);
			
			$SQL_RESUMEN = "SELECT LiquidacionSocioNoimputada.turno_pago,COUNT(*) AS liquidados,
							SUM(LiquidacionSocioNoimputada.importe_dto) AS importe_dto,
							SUM(LiquidacionSocioNoimputada.importe_adebitar) AS importe_adebitar, 'TOTAL' AS tipo 
							FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada
							WHERE LiquidacionSocioNoimputada.liquidacion_id = ".$parametros['LiquidacionSocioNoimputada']['liquidacion_id']."
							AND LiquidacionSocioNoimputada.turno_pago = '$turno' and LiquidacionSocioNoimputada.diskette = 1
							UNION
							SELECT LiquidacionSocioNoimputada.turno_pago,COUNT(*) AS liquidados,
							SUM(LiquidacionSocioNoimputada.importe_dto) AS importe_dto,
							SUM(LiquidacionSocioNoimputada.importe_adebitar) AS importe_adebitar, 'OK' AS tipo 
							FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada
							WHERE LiquidacionSocioNoimputada.liquidacion_id = ".$parametros['LiquidacionSocioNoimputada']['liquidacion_id']."
							AND LiquidacionSocioNoimputada.turno_pago = '$turno' and LiquidacionSocioNoimputada.diskette = 1
							AND LiquidacionSocioNoimputada.error_cbu = 0
							UNION
							SELECT LiquidacionSocioNoimputada.turno_pago,COUNT(*) AS liquidados,
							SUM(LiquidacionSocioNoimputada.importe_dto) AS importe_dto,
							SUM(LiquidacionSocioNoimputada.importe_adebitar) AS importe_adebitar, 'ERROR' AS tipo 
							FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada
							WHERE LiquidacionSocioNoimputada.liquidacion_id = ".$parametros['LiquidacionSocioNoimputada']['liquidacion_id']."
							AND LiquidacionSocioNoimputada.turno_pago = '$turno' and LiquidacionSocioNoimputada.diskette = 1
							AND LiquidacionSocioNoimputada.error_cbu = 1
							GROUP BY LiquidacionSocioNoimputada.turno_pago;";
			
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

			$SQL_OK = "	SELECT LiquidacionSocioNoimputada.documento, LiquidacionSocioNoimputada.apenom, LiquidacionSocioNoimputada.cuit_cuil,
							LiquidacionSocioNoimputada.registro, LiquidacionSocioNoimputada.ultima_calificacion, 
							LiquidacionSocioNoimputada.socio_id, LiquidacionSocioNoimputada.turno_pago, LiquidacionSocioNoimputada.sucursal, 
							LiquidacionSocioNoimputada.nro_cta_bco, LiquidacionSocioNoimputada.cbu, LiquidacionSocioNoimputada.importe_dto, 
							LiquidacionSocioNoimputada.importe_adebitar, LiquidacionSocioNoimputada.intercambio, LiquidacionSocioNoimputada.error_cbu, 
							LiquidacionSocioNoimputada.error_intercambio, GlobalDato.concepto_1, GlobalDato.concepto_2, 
							GlobalDato.logico_1 FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada 
							INNER JOIN global_datos AS GlobalDato ON (LiquidacionSocioNoimputada.codigo_empresa = GlobalDato.id) 
							WHERE LiquidacionSocioNoimputada.liquidacion_id = ".$parametros['LiquidacionSocioNoimputada']['liquidacion_id']." 
							AND LiquidacionSocioNoimputada.turno_pago = '$turno' AND LiquidacionSocioNoimputada.diskette = 1 
							AND LiquidacionSocioNoimputada.error_cbu = 0
							ORDER BY GlobalDato.concepto_1 ASC, LiquidacionSocioNoimputada.apenom ASC, 
							LiquidacionSocioNoimputada.registro ASC ";
			
			$registrosOK = $this->query($SQL_OK);
			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['registros'] = Set::extract("/LiquidacionSocioNoimputada",$registrosOK);			
			
			$SQL_ERROR = "	SELECT LiquidacionSocioNoimputada.documento, LiquidacionSocioNoimputada.apenom, LiquidacionSocioNoimputada.cuit_cuil, 
							LiquidacionSocioNoimputada.registro, LiquidacionSocioNoimputada.ultima_calificacion, 
							LiquidacionSocioNoimputada.socio_id, LiquidacionSocioNoimputada.turno_pago, LiquidacionSocioNoimputada.sucursal, 
							LiquidacionSocioNoimputada.nro_cta_bco, LiquidacionSocioNoimputada.cbu, LiquidacionSocioNoimputada.importe_dto, 
							LiquidacionSocioNoimputada.importe_adebitar, LiquidacionSocioNoimputada.intercambio, LiquidacionSocioNoimputada.error_cbu, 
							LiquidacionSocioNoimputada.error_intercambio, GlobalDato.concepto_1, GlobalDato.concepto_2, 
							GlobalDato.logico_1 FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada 
							INNER JOIN global_datos AS GlobalDato ON (LiquidacionSocioNoimputada.codigo_empresa = GlobalDato.id) 
							WHERE LiquidacionSocioNoimputada.liquidacion_id = ".$parametros['LiquidacionSocioNoimputada']['liquidacion_id']." 
							AND LiquidacionSocioNoimputada.turno_pago = '$turno' AND LiquidacionSocioNoimputada.diskette = 1 
							AND LiquidacionSocioNoimputada.error_cbu = 1
							ORDER BY GlobalDato.concepto_1 ASC, LiquidacionSocioNoimputada.apenom ASC, 
							LiquidacionSocioNoimputada.registro ASC ";
			
			$registrosERROR = $this->query($SQL_ERROR);
			$tmpResumeByTurno['info_procesada_by_turno'][$turno]['errores'] = Set::extract("/LiquidacionSocioNoimputada",$registrosERROR);
			
		
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
////		$aTurnos = Set::sort($parametros['LiquidacionSocioNoimputada']['turno_pago'], '{n}.beneficiario_apenom', 'asc');
//		
//		foreach($parametros['LiquidacionSocioNoimputada']['turno_pago'] as $turno => $dato):
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
//			$registrosTurno = Set::extract("/LiquidacionSocioNoimputada[turno_pago=".$turno."]",$socios);
//			$errores = Set::extract("/LiquidacionSocioNoimputada[error_cbu=1]",$registrosTurno);
//			$enDiskette = Set::extract("/LiquidacionSocioNoimputada[error_cbu=0]",$registrosTurno);
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
						LiquidacionSocioNoimputada.id,
						LiquidacionSocioNoimputada.documento,
						LiquidacionSocioNoimputada.apenom,
						LiquidacionSocioNoimputada.registro,
						LiquidacionSocioNoimputada.ultima_calificacion,
						LiquidacionSocioNoimputada.socio_id,
						LiquidacionSocioNoimputada.turno_pago,
						LiquidacionSocioNoimputada.sucursal,
						LiquidacionSocioNoimputada.nro_cta_bco,
						LiquidacionSocioNoimputada.cbu,
						LiquidacionSocioNoimputada.importe_dto,
						LiquidacionSocioNoimputada.importe_adebitar,
						LiquidacionSocioNoimputada.intercambio,
						LiquidacionSocioNoimputada.error_cbu,
						LiquidacionSocioNoimputada.error_intercambio,
						GlobalDato.concepto_1,
						GlobalDato.concepto_2,
						GlobalDato.logico_1
						"
		);
		
//        $order = array('GlobalDato.concepto_1,LiquidacionSocioNoimputada.apenom,LiquidacionSocioNoimputada.registro');
//        $order = array('LiquidacionSocioNoimputada.sucursal,LiquidacionSocioNoimputada.nro_cta_bco,LiquidacionSocioNoimputada.registro');
        $order = array('LiquidacionSocioNoimputada.socio_id');
        
		$socios = $this->find('all',array(
										'joins' => array(
												array(
													'table' => 'global_datos',
													'alias' => 'GlobalDato',
													'type' => 'inner',
													'foreignKey' => false,
													'conditions' => array('LiquidacionSocioNoimputada.codigo_empresa = GlobalDato.id')
													),
										),
										'conditions' => array(
																	'LiquidacionSocioNoimputada.liquidacion_id' => $parametros['LiquidacionSocioNoimputada']['liquidacion_id'],
																	'LiquidacionSocioNoimputada.turno_pago' => $parametros['LiquidacionSocioNoimputada']['turno_pago_array'],
//																	'IFNULL(LiquidacionSocioNoimputada.importe_adebitar,0) >' => 0,
																	'LiquidacionSocioNoimputada.diskette' => 1
																),
										'fields' => $fields,																
										'order' => $order,
		));

		if(empty($socios)) return null;		
		
		$resultados['parametros'] = $parametros;
		$resultados['info_procesada'] = $socios;
		$resultados['info_diskette'] = Set::extract("/LiquidacionSocioNoimputada[error_cbu=0]",$socios);
		$resultados['info_procesada_by_turno'] = $tmpResumeByTurno;
		$resultados['errores'] = Set::extract("/LiquidacionSocioNoimputada[error_cbu=1]",$socios);
		$resultados['totales'] = unserialize($asinc['Asincrono']['txt2']);
		
//		debug($resultados);
//        exit;
		
		$registros = Set::extract("/LiquidacionSocioNoimputada[error_cbu=0]/intercambio",$socios);
		$importes = Set::extract("/LiquidacionSocioNoimputada[error_cbu=0]/importe_adebitar",$socios);
		
		$bancoIntercambio = $parametros['LiquidacionSocioNoimputada']['banco_intercambio'];
//		$fechaDebito = parent::armaFecha($parametros['LiquidacionSocioNoimputada']['fecha_debito']);
        $fechaDebito = $parametros['LiquidacionSocioNoimputada']['fecha_debito'];
		$registrosDiskette = $resultados['totales']['registros_disk'];
		$importeDiskette = $resultados['totales']['importe_disk'];
		$nroArchivo = $parametros['LiquidacionSocioNoimputada']['nro_archivo'];	
		$fechaPresentacion = $parametros['LiquidacionSocioNoimputada']['fecha_presentacion'];
		
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
				
							LiquidacionSocioNoimputada.id,
							LiquidacionSocioNoimputada.codigo_organismo,
							LiquidacionSocioNoimputada.socio_id,
							LiquidacionSocioNoimputada.documento,
							LiquidacionSocioNoimputada.apenom,
							LiquidacionSocioNoimputada.banco_id,
							LiquidacionSocioNoimputada.sucursal,
							LiquidacionSocioNoimputada.nro_cta_bco,
							LiquidacionSocioNoimputada.cbu,
							LiquidacionSocioNoimputada.ultima_calificacion,
							LiquidacionSocioNoimputada.codigo_empresa,
							LiquidacionSocioNoimputada.turno_pago,					
							(SELECT SUM(saldo_actual) FROM liquidacion_cuota_noimputadas AS LiquidacionCuota2
								".($criterioFiltroDeuda == 2 ? "INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON
									(
										OrdenDescuentoCuota.id = LiquidacionCuota2.orden_descuento_cuota_id
										AND OrdenDescuentoCuota.nro_cuota = 1
									)" : "")."
								WHERE 
									LiquidacionCuota2.liquidacion_id = $liquidacionID 
									AND LiquidacionCuota2.socio_id = LiquidacionSocioNoimputada.socio_id
									" . (!empty($proveedorId) ? " AND LiquidacionCuota2.proveedor_id = $proveedorId " : "") . "
									" . (!empty($tipoCuota) && empty($tipoCuotaIN) ? " AND LiquidacionCuota2.tipo_cuota = '$tipoCuota' " : (!empty($tipoCuotaIN) ? " AND LiquidacionCuota2.tipo_cuota IN ($tipoCuotaIN)" : "")) . "
									AND LiquidacionCuota2.para_imputar = 0
									" . ($criterioFiltroDeuda == 0 ? "" : "AND LiquidacionCuota2.periodo_cuota = Liquidacion.periodo") . "
							) AS saldo_actual,
							IFNULL((
								SELECT SUM(importe) FROM orden_descuento_cobro_cuotas AS OrdenDescuentoCobroCuota
								WHERE OrdenDescuentoCobroCuota.orden_descuento_cuota_id = LiquidacionCuotaNoimputada.orden_descuento_cuota_id
							),0) AS importe_cobrado					
						
						FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada
						
						INNER JOIN liquidacion_cuota_noimputadas AS LiquidacionCuota ON 
							(
								LiquidacionCuotaNoimputada.liquidacion_id = LiquidacionSocioNoimputada.liquidacion_id 
								AND LiquidacionCuotaNoimputada.socio_id = LiquidacionSocioNoimputada.socio_id
							)
						
						INNER JOIN liquidaciones AS Liquidacion ON 
							(
								Liquidacion.id = LiquidacionSocioNoimputada.liquidacion_id
							)
						
						INNER JOIN liquidacion_socio_rendiciones AS LiquidacionSocioRendicion ON 
							(
								LiquidacionSocioRendicion.liquidacion_id = Liquidacion.id 
								AND LiquidacionSocioRendicion.socio_id = LiquidacionSocioNoimputada.socio_id
							)
						".($criterioFiltroDeuda == 2 ? "INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON
							(
								OrdenDescuentoCuota.id = LiquidacionCuotaNoimputada.orden_descuento_cuota_id
								AND OrdenDescuentoCuota.nro_cuota = 1
							)" : "")."				
						WHERE 
							LiquidacionSocioNoimputada.liquidacion_id = $liquidacionID
							AND LiquidacionSocioNoimputada.turno_pago = '$turno'
							AND LiquidacionSocioRendicion.status = '$codigo'
							" . (!empty($proveedorId) ? " AND LiquidacionCuotaNoimputada.proveedor_id = $proveedorId " : "") . "
							" . (!empty($tipoCuota) && empty($tipoCuotaIN) ? " AND LiquidacionCuotaNoimputada.tipo_cuota = '$tipoCuota' " : (!empty($tipoCuotaIN) ? " AND LiquidacionCuotaNoimputada.tipo_cuota IN ($tipoCuotaIN)" : "")) . "
							AND LiquidacionCuotaNoimputada.para_imputar = 0
							" . ($criterioFiltroDeuda == 0 ? "" : "AND LiquidacionCuotaNoimputada.periodo_cuota = Liquidacion.periodo") . "
						
						GROUP BY 
							LiquidacionSocioNoimputada.socio_id,
							LiquidacionSocioNoimputada.documento,
							LiquidacionSocioNoimputada.apenom,
							LiquidacionSocioNoimputada.banco_id,
							LiquidacionSocioNoimputada.sucursal,
							LiquidacionSocioNoimputada.nro_cta_bco,
							LiquidacionSocioNoimputada.cbu
							
						ORDER BY 
							LiquidacionSocioNoimputada.apenom;";
				
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
					
					
						$dato['LiquidacionSocioNoimputada']['registro'] = 1;
						$dato['LiquidacionSocioNoimputada']['importe_liquidado'] = $dato[0]['saldo_actual'];
						
						$importeCobrado = $dato[0]['importe_cobrado'];
						
						//ARMO EL IDENTIFICADOR DE DEBITO
						$idDebito = array();			
						$idDebito['LiquidacionSocioNoimputada']['socio_id'] = $dato['LiquidacionSocioNoimputada']['socio_id'];
						$idDebito['LiquidacionSocioNoimputada']['liquidacion_id'] = $liquidacionID;
						$idDebito['LiquidacionSocioNoimputada']['registro'] = $dato['LiquidacionSocioNoimputada']['registro'];
						$dato['LiquidacionSocioNoimputada']['identificador_debito'] = $this->__genDebitoID($idDebito);
						
						#REGISTRO PARA DISKETTE
						$importe 			= $dato['LiquidacionSocioNoimputada']['importe_liquidado'];
						$registroNro 		= $dato['LiquidacionSocioNoimputada']['registro'];
						$idDebito 			= $dato['LiquidacionSocioNoimputada']['identificador_debito'];
						$liquidacionSocioId = $dato['LiquidacionSocioNoimputada']['id'];
						$socioId 			= $dato['LiquidacionSocioNoimputada']['socio_id'];
						$sucursal 			= $dato['LiquidacionSocioNoimputada']['sucursal'];
						$cuenta 			= $dato['LiquidacionSocioNoimputada']['nro_cta_bco'];
						$cbu 				= $dato['LiquidacionSocioNoimputada']['cbu'];
						$codOrganismo 		= $dato['LiquidacionSocioNoimputada']['codigo_organismo'];
						$calificacion 		= $dato['LiquidacionSocioNoimputada']['ultima_calificacion'];
						
						$registro = $oBANCO->genRegistroDisketteBanco($bancoIntercambio,$fechaDebito,$importe,$registroNro,$idDebito,$liquidacionSocioId,$socioId,$sucursal,$cuenta,$cbu,$codOrganismo,$calificacion,NULL,$liquidacionID);
						
						
						$dato['LiquidacionSocioNoimputada']['cadena'] = $registro['cadena'];
						$dato['LiquidacionSocioNoimputada']['error'] = $registro['error'];
						$dato['LiquidacionSocioNoimputada']['mensaje'] = $registro['mensaje'];
						$dato['LiquidacionSocioNoimputada']['importe_adebitar'] = $registro['importe_debito'];
			
						$ACUM_REGISTROS++;
						$ACUM_REGISTROS_TURNO++;
						$ACUM_IMPORTE += $dato['LiquidacionSocioNoimputada']['importe_liquidado'];
						$ACUM_IMPORTE_TURNO += $dato['LiquidacionSocioNoimputada']['importe_liquidado'];
						
						$dato['LiquidacionSocioNoimputada']['banco'] = parent::getNombreBanco($dato['LiquidacionSocioNoimputada']['banco_id']);
						$dato['LiquidacionSocioNoimputada']['calificacion'] = parent::GlobalDato('concepto_1', $dato['LiquidacionSocioNoimputada']['ultima_calificacion']);
						
						$turnoDesc = $oTURNO->getDescripcionByTruno($dato['LiquidacionSocioNoimputada']['turno_pago']);
						
						$dato['LiquidacionSocioNoimputada']['empresa'] = $turnoDesc;
						
						$dato['LiquidacionSocioNoimputada']['sucursal'] = $registro['sucursal_formed'];
						$dato['LiquidacionSocioNoimputada']['nro_cta_bco'] = $registro['cuenta_formed'];
						
						//control con el monto de corte
						if(!empty($montoCorte) && $montoCorte < $dato['LiquidacionSocioNoimputada']['importe_adebitar']){
							$dato['LiquidacionSocioNoimputada']['error'] = 1;
							$dato['LiquidacionSocioNoimputada']['mensaje'] = "> $montoCorte";
						}
			
						if($importeCobrado != 0){
							if($importeCobrado >= $dato['LiquidacionSocioNoimputada']['importe_adebitar']){
								$dato['LiquidacionSocioNoimputada']['error'] = 1;
								$dato['LiquidacionSocioNoimputada']['mensaje'] = "COBRADO A LA FECHA ($importeCobrado)";
							}else if(($dato['LiquidacionSocioNoimputada']['importe_adebitar'] - $importeCobrado) > 0){
								$dato['LiquidacionSocioNoimputada']['importe_adebitar'] -= $importeCobrado;
								$dato['LiquidacionSocioNoimputada']['mensaje'] = "COBRADO A LA FECHA ($importeCobrado)";
							}
						}
						
						
						$datos[$idx]['LiquidacionSocioNoimputada'] = $dato['LiquidacionSocioNoimputada'];
						
						if($dato['LiquidacionSocioNoimputada']['error'] == 0){
							$ACUM_REGISTROS_DISK++;
							$ACUM_REGISTROS_DISK_TURNO++;
							$ACUM_IMPORTE_DISK += $dato['LiquidacionSocioNoimputada']['importe_adebitar'];
							$ACUM_IMPORTE_DISK_TURNO += $dato['LiquidacionSocioNoimputada']['importe_adebitar'];
							array_push($registros,$dato);
							$resumeByTurno['info_procesada_by_turno'][$turno]['registros'][$idx]['LiquidacionSocioNoimputada'] = $dato['LiquidacionSocioNoimputada'];
						}else{
							$resumeByTurno['info_procesada_by_turno'][$turno]['errores'][$idx]['LiquidacionSocioNoimputada'] = $dato['LiquidacionSocioNoimputada'];
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
			$registros = Set::extract("/LiquidacionSocioNoimputada[error=0]/cadena",$registros);
			$importes = Set::extract("/LiquidacionSocioNoimputada[error=0]/importe_adebitar",$registros);
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
		
					LiquidacionSocioNoimputada.id,
                                        LiquidacionSocioNoimputada.liquidacion_id,
					LiquidacionSocioNoimputada.codigo_organismo,
					LiquidacionSocioNoimputada.socio_id,
					LiquidacionSocioNoimputada.documento,
					LiquidacionSocioNoimputada.apenom,
					LiquidacionSocioNoimputada.banco_id,
					LiquidacionSocioNoimputada.sucursal,
					LiquidacionSocioNoimputada.nro_cta_bco,
					LiquidacionSocioNoimputada.cbu,
					LiquidacionSocioNoimputada.ultima_calificacion,
					LiquidacionSocioNoimputada.codigo_empresa,
					LiquidacionSocioNoimputada.turno_pago,	
					(SELECT SUM(saldo_actual) FROM liquidacion_cuota_noimputadas AS LiquidacionCuota2
						".($criterioFiltroDeuda == 2 ? "INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON
							(
								OrdenDescuentoCuota.id = LiquidacionCuota2.orden_descuento_cuota_id
								AND OrdenDescuentoCuota.nro_cuota = 1
							)" : "")."
						WHERE 
							LiquidacionCuota2.liquidacion_id = $liquidacionID 
							AND LiquidacionCuota2.socio_id = LiquidacionSocioNoimputada.socio_id
							" . (!empty($proveedorId) ? " AND LiquidacionCuota2.proveedor_id = $proveedorId " : "") . "
							" . (!empty($tipoCuota) && empty($tipoCuotaIN) ? " AND LiquidacionCuota2.tipo_cuota = '$tipoCuota' " : (!empty($tipoCuotaIN) ? " AND LiquidacionCuota2.tipo_cuota IN ($tipoCuotaIN)" : "")) . "
							AND LiquidacionCuota2.para_imputar = 0
							" . ($criterioFiltroDeuda == 0 ? "" : "AND LiquidacionCuota2.periodo_cuota = Liquidacion.periodo") . "
					) AS saldo_actual,
					IFNULL((
						SELECT SUM(importe) FROM orden_descuento_cobro_cuotas AS OrdenDescuentoCobroCuota
						WHERE OrdenDescuentoCobroCuota.orden_descuento_cuota_id = LiquidacionCuotaNoimputada.orden_descuento_cuota_id
					),0) AS importe_cobrado					
				
				FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada
				
				INNER JOIN liquidacion_cuota_noimputadas AS LiquidacionCuota ON 
					(
						LiquidacionCuotaNoimputada.liquidacion_id = LiquidacionSocioNoimputada.liquidacion_id 
						AND LiquidacionCuotaNoimputada.socio_id = LiquidacionSocioNoimputada.socio_id
					)
				
				INNER JOIN liquidaciones AS Liquidacion ON 
					(
						Liquidacion.id = LiquidacionSocioNoimputada.liquidacion_id
					)
				
				INNER JOIN liquidacion_socio_rendiciones AS LiquidacionSocioRendicion ON 
					(
						LiquidacionSocioRendicion.liquidacion_id = Liquidacion.id 
						AND LiquidacionSocioRendicion.socio_id = LiquidacionSocioNoimputada.socio_id
					)
				".($criterioFiltroDeuda == 2 ? "INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON
					(
						OrdenDescuentoCuota.id = LiquidacionCuotaNoimputada.orden_descuento_cuota_id
						AND OrdenDescuentoCuota.nro_cuota = 1
					)" : "")."				
				WHERE 
					LiquidacionSocioNoimputada.liquidacion_id = $liquidacionID
					AND LiquidacionSocioNoimputada.turno_pago IN ($turnoIN)
					AND LiquidacionSocioRendicion.status IN ($codigoRendIN)
					" . (!empty($proveedorId) ? " AND LiquidacionCuotaNoimputada.proveedor_id = $proveedorId " : "") . "
					" . (!empty($tipoCuota) && empty($tipoCuotaIN) ? " AND LiquidacionCuotaNoimputada.tipo_cuota = '$tipoCuota' " : (!empty($tipoCuotaIN) ? " AND LiquidacionCuotaNoimputada.tipo_cuota IN ($tipoCuotaIN)" : "")) . "
					AND LiquidacionCuotaNoimputada.para_imputar = 0
					" . ($criterioFiltroDeuda == 0 ? "" : "AND LiquidacionCuotaNoimputada.periodo_cuota = Liquidacion.periodo") . "
				
				GROUP BY 
					LiquidacionSocioNoimputada.socio_id,
					LiquidacionSocioNoimputada.documento,
					LiquidacionSocioNoimputada.apenom,
					LiquidacionSocioNoimputada.banco_id,
					LiquidacionSocioNoimputada.sucursal,
					LiquidacionSocioNoimputada.nro_cta_bco,
					LiquidacionSocioNoimputada.cbu
					
				ORDER BY 
					LiquidacionSocioNoimputada.apenom;";
		
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
			
			$dato['LiquidacionSocioNoimputada']['registro'] = 1;
			$dato['LiquidacionSocioNoimputada']['importe_liquidado'] = $dato[0]['saldo_actual'];
			
			$importeCobrado = $dato[0]['importe_cobrado'];
			
			//ARMO EL IDENTIFICADOR DE DEBITO
			$idDebito = array();			
			$idDebito['LiquidacionSocioNoimputada']['socio_id'] = $dato['LiquidacionSocioNoimputada']['socio_id'];
			$idDebito['LiquidacionSocioNoimputada']['liquidacion_id'] = $liquidacionID;
			$idDebito['LiquidacionSocioNoimputada']['registro'] = $dato['LiquidacionSocioNoimputada']['registro'];
			$dato['LiquidacionSocioNoimputada']['identificador_debito'] = $this->__genDebitoID($idDebito);
			
			#REGISTRO PARA DISKETTE
			$importe 			= $dato['LiquidacionSocioNoimputada']['importe_liquidado'];
			$registroNro 		= $dato['LiquidacionSocioNoimputada']['registro'];
			$idDebito 			= $dato['LiquidacionSocioNoimputada']['identificador_debito'];
			$liquidacionSocioId = $dato['LiquidacionSocioNoimputada']['id'];
			$socioId 			= $dato['LiquidacionSocioNoimputada']['socio_id'];
			$sucursal 			= $dato['LiquidacionSocioNoimputada']['sucursal'];
			$cuenta 			= $dato['LiquidacionSocioNoimputada']['nro_cta_bco'];
			$cbu 				= $dato['LiquidacionSocioNoimputada']['cbu'];
			$codOrganismo 		= $dato['LiquidacionSocioNoimputada']['codigo_organismo'];
			$calificacion 		= $dato['LiquidacionSocioNoimputada']['ultima_calificacion'];
			$apenom		 		= str_replace(","," ",$dato['LiquidacionSocioNoimputada']['apenom']);
			$ndoc		 		= $dato['LiquidacionSocioNoimputada']['documento'];
			$beneficioBancoId       = $dato['LiquidacionSocioNoimputada']['banco_id'];
                        $liquidacionID 		= $dato['LiquidacionSocioNoimputada']['liquidacion_id'];
                        $convenioBcoCba     = null;                        
			
			$registro = $oBANCO->genRegistroDisketteBanco($bancoIntercambio,$fechaDebito,$importe,$registroNro,$idDebito,$liquidacionSocioId,$socioId,$sucursal,$cuenta,$cbu,$codOrganismo,$apenom, $ndoc, $calificacion, $beneficioBancoId,$liquidacionID,$convenioBcoCba);
			
			
			$dato['LiquidacionSocioNoimputada']['cadena'] = $registro['cadena'];
			$dato['LiquidacionSocioNoimputada']['error'] = $registro['error'];
			$dato['LiquidacionSocioNoimputada']['mensaje'] = $registro['mensaje'];
			$dato['LiquidacionSocioNoimputada']['importe_adebitar'] = $registro['importe_debito'];

			$ACUM_REGISTROS++;
			$ACUM_IMPORTE += $dato['LiquidacionSocioNoimputada']['importe_liquidado'];
			
			$dato['LiquidacionSocioNoimputada']['banco'] = parent::getNombreBanco($dato['LiquidacionSocioNoimputada']['banco_id']);
			$dato['LiquidacionSocioNoimputada']['calificacion'] = parent::GlobalDato('concepto_1', $dato['LiquidacionSocioNoimputada']['ultima_calificacion']);
			
			$turnoDesc = $oTURNO->getDescripcionByTruno($dato['LiquidacionSocioNoimputada']['turno_pago']);
			
			$dato['LiquidacionSocioNoimputada']['empresa'] = $turnoDesc;
			
			$dato['LiquidacionSocioNoimputada']['sucursal'] = $registro['sucursal_formed'];
			$dato['LiquidacionSocioNoimputada']['nro_cta_bco'] = $registro['cuenta_formed'];
			
			//control con el monto de corte
			if(!empty($montoCorte) && $montoCorte < $dato['LiquidacionSocioNoimputada']['importe_adebitar']){
				$dato['LiquidacionSocioNoimputada']['error'] = 1;
				$dato['LiquidacionSocioNoimputada']['mensaje'] = "> $montoCorte";
			}

			if($importeCobrado != 0){
				if($importeCobrado >= $dato['LiquidacionSocioNoimputada']['importe_adebitar']){
					$dato['LiquidacionSocioNoimputada']['error'] = 1;
					$dato['LiquidacionSocioNoimputada']['mensaje'] = "COBRADO A LA FECHA ($importeCobrado)";
				}else if(($dato['LiquidacionSocioNoimputada']['importe_adebitar'] - $importeCobrado) > 0){
					$dato['LiquidacionSocioNoimputada']['importe_adebitar'] -= $importeCobrado;
					$dato['LiquidacionSocioNoimputada']['mensaje'] = "COBRADO A LA FECHA ($importeCobrado)";
				}
			}
			
			
			$datos[$idx]['LiquidacionSocioNoimputada'] = $dato['LiquidacionSocioNoimputada'];
			

			if($dato['LiquidacionSocioNoimputada']['error'] == 0){
				$ACUM_REGISTROS_DISK++;
				$ACUM_IMPORTE_DISK += $dato['LiquidacionSocioNoimputada']['importe_adebitar'];
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
		
		$registros = Set::extract("/LiquidacionSocioNoimputada[error=0]/cadena",$datos);
		$importes = Set::extract("/LiquidacionSocioNoimputada[error=0]/importe_adebitar",$datos);
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
		$sql = "SELECT LiquidacionSocioNoimputada.intercambio FROM liquidacion_socio_noimputadas AS LiquidacionSocioNoimputada
				INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = LiquidacionSocioNoimputada.liquidacion_id)
				WHERE LiquidacionSocioNoimputada.socio_id = 	$socio_id
				AND Liquidacion.periodo = '$periodo' AND Liquidacion.codigo_organismo = '$codigoOrganismo'
				AND IFNULL(LiquidacionSocioNoimputada.intercambio,'0') <> 0";
		$socios = $this->query($sql);
		foreach($socios as $idx => $socio){
			$str .= preg_replace("[\n|\r|\n\r]","",$socio['LiquidacionSocioNoimputada']['intercambio'])."\n";
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
		$conditions['LiquidacionSocioNoimputada.socio_id'] = $socio_id;
		$conditions['Liquidacion.liquidacion_id'] = $liquidacionId;
		$conditions['NOT'] = array('IFNULL(LiquidacionSocioNoimputada.intercambio,"0")' => 0);
		$socios = $this->find('all',array('conditions' => $conditions,'order' => array('LiquidacionSocioNoimputada.codigo_organismo,LiquidacionSocioNoimputada.registro')));
		foreach($socios as $idx => $socio){
			$str .= $socio['LiquidacionSocioNoimputada']['intercambio']."\n";
		}
		return $str;
	}	
	
	
    function getResumenByTurnoByPeriodo($periodo){

        $sql = "select 
                LiquidacionSocioNoimputada.codigo_organismo,
                LiquidacionSocioNoimputada.codigo_empresa,
                right(LiquidacionSocioNoimputada.turno_pago,5) as turno_pago,
                Organismo.concepto_1,
                Empresa.concepto_1,
                (select descripcion from liquidacion_turnos as LiquidacionTurno
                where LiquidacionTurno.codigo_empresa = LiquidacionSocioNoimputada.codigo_empresa
                and LiquidacionTurno.turno = LiquidacionSocioNoimputada.turno_pago
                limit 1) as turno,
                count(*) as cant,
                sum(LiquidacionSocioNoimputada.importe_adebitar) as importe_adebitar
                from liquidacion_socio_noimputadas as LiquidacionSocio
                inner join liquidaciones as Liquidacion on (Liquidacion.id = LiquidacionSocioNoimputada.liquidacion_id)
                inner join global_datos as Empresa on (Empresa.id = LiquidacionSocioNoimputada.codigo_empresa)
                inner join global_datos as Organismo on (Organismo.id = LiquidacionSocioNoimputada.codigo_organismo)
                where Liquidacion.periodo = '$periodo' and substring(LiquidacionSocioNoimputada.codigo_organismo,9,2) = '22'
                group by 
                LiquidacionSocioNoimputada.codigo_organismo,
                LiquidacionSocioNoimputada.codigo_empresa,
                LiquidacionSocioNoimputada.turno_pago
                order by 
                Organismo.concepto_1,
                Empresa.concepto_1,
                (select descripcion from liquidacion_turnos as LiquidacionTurno
                where LiquidacionTurno.codigo_empresa = LiquidacionSocioNoimputada.codigo_empresa
                and LiquidacionTurno.turno = LiquidacionSocioNoimputada.turno_pago
                limit 1);";

        $datos = $this->query($sql);
        return $datos;

    }
        
        
    
    function scoring($liquidacionId,$socioId){
        $this->query("CALL SP_LIQUIDA_DEUDA_SCORING($liquidacionId,$socioId)");
    }
        

    function cargar_scoring_by_socio($socio_id, $limit = 1){
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
        $sql = "select sum(importe_dto) as importe_dto from liquidacion_socio_noimputadas where liquidacion_id = $liquidacion_id;";
        $datos = $this->query($sql);
        if(empty($datos)) return 0;
        return $datos[0][0];
    }
    
    
    function get_cbu_liquidado($liquidacion_id,$socio_id){
        
        $sql = "select b.cbu from liquidacion_socio_noimputadas ls
                inner join persona_beneficios b on b.id = ls.persona_beneficio_id
                where liquidacion_id = $liquidacion_id
                and socio_id = $socio_id
                group by b.cbu limit 1;";
        $datos = $this->query($sql);
        if(empty($datos)) return NULL;
        return $datos[0]['b']['cbu'];        
        
    }
    
}
?>