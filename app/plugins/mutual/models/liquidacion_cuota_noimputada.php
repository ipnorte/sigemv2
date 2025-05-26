<?php
/**
 * <b>LiquidacionCuota</b>
 * <br/>
 * Metodos Clave:
 * <ul>	
 * 	<li><b>generarDetalleLiquidacionCuota</b> --> graba una cuota en la liquidacion</li>
 *  <li><b>getInfoDto_AMAN</b> --> arma la consulta para generar la liquidacion_socio_no_imputada</li>
 *  <li><b>cuotasPendientesDeImputar</b> --> devuelve las cuotas no imputadas totalmente para procesar la imputacion (proceso archivo)</li>
 * </ul>
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 *
 */
class LiquidacionCuotaNoimputada extends MutualAppModel{

	/**
	 * Nombre del Modelo
	 * @var string
	 */
	var $name = 'LiquidacionCuotaNoimputada';
        var $use = "liquidacion_cuota_noimputadas";
	/**
	 * Indica si el registro para envio a descuento del CBU se separa en la deuda del periodo
	 * y el atraso.
	 * @var boolean
	 */
	var $divideRegCBU = TRUE;
	
	/**
	 * Cuotas Liquidadas por socio por periodo.
	 * trae las cuotas liquidadas que corresponden a la orden_descuento_cuotas (NO trae las pendientes)
	 * @param $socio_id
	 * @param $periodo
	 * @return unknown_type
	 */
	function cuotasLiquidadasBySocioByPeriodo($socio_id,$periodo,$sinAdicionalesPendientes=false,$codigoOrganismo = null, $proveedor_id = null){
            
            $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
            $NRO_CUOTA_PERM = (isset($INI_FILE['general']['numera_cuota_permanete']) && $INI_FILE['general']['numera_cuota_permanete'] != 0 ? $INI_FILE['general']['numera_cuota_permanete'] : 0);
            $NRO_CUOTA_PERM = ($NRO_CUOTA_PERM != 0 ? 1 : 0);
            
            
            $sql = "select 
                    OrdenDescuento.id,
                    OrdenDescuento.permanente,
                    Liquidacion.id,
                    Organismo.concepto_1,
                    concat(OrdenDescuento.tipo_orden_dto,' #',OrdenDescuento.numero) tipo_nro,
                    OrdenDescuentoCuota.periodo,
                    concat(Proveedor.razon_social_resumida,' / ',TipoProducto.concepto_1) as proveedor_producto,
                    OrdenDescuentoCuota.vencimiento,
                    TipoCuota.concepto_1,
                    LiquidacionCuotaNoimputada.socio_id,
                    LiquidacionCuotaNoimputada.importe,
                    LiquidacionCuotaNoimputada.saldo_actual,
                    LiquidacionCuotaNoimputada.importe_debitado,
                    (LiquidacionCuotaNoimputada.saldo_actual - LiquidacionCuotaNoimputada.importe_debitado) as saldo,
                    LiquidacionCuotaNoimputada.para_imputar,
                    LiquidacionCuotaNoimputada.imputada,
                    LiquidacionCuotaNoimputada.orden_descuento_cobro_id,
                    LiquidacionCuotaNoimputada.periodo_cuota,
                    if(OrdenDescuento.permanente = 1 and 1 = $NRO_CUOTA_PERM,concat((select lpad(trim(cast(count(*) as char(5))),2,0) from orden_descuento_cuotas c1 where c1.orden_descuento_id = OrdenDescuento.id and c1.periodo <= OrdenDescuentoCuota.periodo),'/',
                    (select lpad(trim(cast(count(*) as char(5))),2,0) from orden_descuento_cuotas c1 where c1.orden_descuento_id = OrdenDescuento.id)),concat(lpad(OrdenDescuentoCuota.nro_cuota,2,0),'/',
                    lpad(if(OrdenDescuento.permanente = 1,0,OrdenDescuento.cuotas),2,0))) as cuota
                    from liquidacion_cuota_noimputadas LiquidacionCuota
                    inner join liquidaciones Liquidacion on Liquidacion.id = LiquidacionCuotaNoimputada.liquidacion_id
                    inner join global_datos Organismo on Organismo.id = Liquidacion.codigo_organismo
                    inner join global_datos TipoProducto on TipoProducto.id = LiquidacionCuotaNoimputada.tipo_producto
                    inner join global_datos TipoCuota on TipoCuota.id = LiquidacionCuotaNoimputada.tipo_cuota
                    inner join orden_descuentos OrdenDescuento on OrdenDescuento.id = LiquidacionCuotaNoimputada.orden_descuento_id
                    inner join orden_descuento_cuotas OrdenDescuentoCuota on OrdenDescuentoCuota.id = LiquidacionCuotaNoimputada.orden_descuento_cuota_id
                    inner join proveedores Proveedor on Proveedor.id =  LiquidacionCuotaNoimputada.proveedor_id
                    where 
                    Liquidacion.periodo = '$periodo'
                    and LiquidacionCuotaNoimputada.socio_id = $socio_id 
                    ".(!$sinAdicionalesPendientes ? " and LiquidacionCuotaNoimputada.mutual_adicional_pendiente_id = 0 " : "")." 
                    ".(!empty($codigoOrganismo) ? " and Liquidacion.codigo_organismo = '$codigoOrganismo' " : "" )." 
                    ".(!empty($proveedor_id) ? " and LiquidacionCuotaNoimputada.proveedor_id = $proveedor_id " : "")." 
                    order by LiquidacionCuotaNoimputada.periodo_cuota,OrdenDescuento.tipo_orden_dto,OrdenDescuento.numero;";
            
                $cuotas = $this->query($sql);    
            
//		$this->bindModel(array('belongsTo' => array('Liquidacion','OrdenDescuento','Proveedor','OrdenDescuentoCuota')));
//		$conditions = array();
//		$conditions['Liquidacion.periodo'] = $periodo;
//		$conditions['LiquidacionCuotaNoimputada.socio_id'] = $socio_id;
//		if(!$sinAdicionalesPendientes) $conditions['LiquidacionCuotaNoimputada.mutual_adicional_pendiente_id'] = 0;
//		if(!empty($codigoOrganismo)) $conditions['Liquidacion.codigo_organismo'] = $codigoOrganismo;
//		if(!empty($proveedor_id)) $conditions['LiquidacionCuotaNoimputada.proveedor_id'] = $proveedor_id;
//		
//		$cuotas = $this->find('all',array('conditions' => $conditions,'order' => array('LiquidacionCuotaNoimputada.periodo_cuota,OrdenDescuento.tipo_orden_dto,OrdenDescuento.numero')));
//		debug($cuotas);
//                exit;
// 		if(!$sinAdicionalesPendientes)$cuotas = $this->find('all',array('conditions' => array('Liquidacion.periodo' => $periodo,'LiquidacionCuotaNoimputada.socio_id' => $socio_id,'LiquidacionCuotaNoimputada.mutual_adicional_pendiente_id' => 0),'order' => array('LiquidacionCuotaNoimputada.periodo_cuota,OrdenDescuento.tipo_orden_dto,OrdenDescuento.numero')));
// 		else $cuotas = $this->find('all',array('conditions' => array('Liquidacion.periodo' => $periodo,'LiquidacionCuotaNoimputada.socio_id' => $socio_id),'order' => array('LiquidacionCuotaNoimputada.periodo_cuota,OrdenDescuento.tipo_orden_dto,OrdenDescuento.numero')));
//                debug($cuotas);
                return $cuotas;
	}
	
	/**
	 * Cuotas pendientes por socio por periodo
	 * Devuelve las cuotas pendientes de imputacion
	 * @param $socio_id
	 * @param $periodo
	 * @return unknown_type
	 */
	function cuotasPendientesBySocioByPeriodo($socio_id,$periodo,$codigoOrganismo = null, $proveedor_id = null){
		$this->bindModel(array('belongsTo' => array('Liquidacion','OrdenDescuento','Proveedor','MutualAdicionalPendiente')));
		$conditions = array();
		$conditions['Liquidacion.periodo'] = $periodo;
		$conditions['LiquidacionCuotaNoimputada.socio_id'] = $socio_id;
		$conditions['LiquidacionCuotaNoimputada.mutual_adicional_pendiente_id <>'] = 0;
		if(!empty($codigoOrganismo)) $conditions['Liquidacion.codigo_organismo'] = $codigoOrganismo;
		if(!empty($proveedor_id)) $conditions['LiquidacionCuotaNoimputada.proveedor_id'] = $proveedor_id;
		
		$cuotas = $this->find('all',array('conditions' => $conditions,'order' => array('LiquidacionCuotaNoimputada.periodo_cuota')));
		return $cuotas;
	}
	
	/**
	 * @deprecated
	 * 
	 * Cuotas Pendientes de Imputar
	 * Devuelve las cuotas pendientes de imputar. Busca las cuotas liquidadas (con saldo actual) ordenadas de acuerdo a la prioridad 
	 * establecida en la global para el tipo de cuota.
	 * Al importe liquidado se le calcula el saldo actual (si $actualizaSaldo = true) para reflejar en la liquidacion posibles pagos efectuados sobre la cuota por otros medios
	 * @param integer $liquidacion_id
	 * @param integer $socio_id
	 * @param boolean $actualizaSaldo
	 * @return array
	 */
	function cuotasPendientesDeImputar($liquidacion_id,$socio_id,$actualizaSaldo = true){
		
		$conditions = array();
		$conditions['LiquidacionCuotaNoimputada.liquidacion_id'] = $liquidacion_id;
		$conditions['LiquidacionCuotaNoimputada.socio_id'] = $socio_id;
		$conditions['LiquidacionCuotaNoimputada.saldo_actual >'] = 'LiquidacionCuotaNoimputada.importe_debitado';
		
		$cuotas = $this->find('all',array('joins' => array(
														array(
															'table' => 'global_datos',
															'alias' => 'GlobalDato',
															'type' => 'inner',
															'foreignKey' => false,
															'conditions' => array('LiquidacionCuotaNoimputada.tipo_cuota = GlobalDato.id')
															),
														array(
															'table' => 'orden_descuento_cuotas',
															'alias' => 'OrdenDescuentoCuota',
															'type' => 'left',
															'foreignKey' => false,
															'conditions' => array('LiquidacionCuotaNoimputada.orden_descuento_cuota_id = OrdenDescuentoCuota.id')
															),															
														),
											'conditions' => $conditions,
											'fields' => array('LiquidacionCuotaNoimputada.id,
																LiquidacionCuotaNoimputada.orden_descuento_cuota_id,
																LiquidacionCuotaNoimputada.importe,
																LiquidacionCuotaNoimputada.saldo_actual,
																LiquidacionCuotaNoimputada.importe_debitado,
																LiquidacionCuotaNoimputada.orden_descuento_cobro_id'
														
														),
											'order' => array('GlobalDato.entero_2 ASC,
																LiquidacionCuotaNoimputada.periodo_cuota ASC, 
																OrdenDescuentoCuota.nro_cuota DESC,
																LiquidacionCuotaNoimputada.saldo_actual DESC'
														)									
		));

		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();
		foreach($cuotas as $idx => $cuota){
			if($cuota['LiquidacionCuotaNoimputada']['orden_descuento_cuota_id'] != 0 && $actualizaSaldo)$cuota['LiquidacionCuotaNoimputada']['saldo_actual'] = $oCUOTA->getSaldo($cuota['LiquidacionCuotaNoimputada']['orden_descuento_cuota_id']);
			else $cuota['LiquidacionCuotaNoimputada']['saldo_actual'] = $cuota['LiquidacionCuotaNoimputada']['importe'];
			$cuotas[$idx] = $cuota;
		}
	
		return $cuotas;		
	}
	
	/**
	 * Devuelve las cuotas de acuerdo al criterio de imputacion
	 * @param $liquidacion_id
	 * @param $socio_id
	 * @param $noImputadas
	 * @param $excluyeTipoOrdDto
	 * @param $proveedor_id
	 * @return array
	 */
	function getCuotasByCriterioImputacion($liquidacion_id,$socio_id,$noImputadas=false,$excluyeTipoOrdDto=null, $proveedor_id = null){
		
		#ARMO LA CLAUSULA WHERE PARA FILTRADO
		$WHERE = "LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id";
		$WHERE .= " AND LiquidacionCuotaNoimputada.socio_id = $socio_id";
		if($noImputadas) $WHERE .= " AND LiquidacionCuotaNoimputada.imputada = 0";
		if(!empty($excluyeTipoOrdDto)) $WHERE .= " AND LiquidacionCuotaNoimputada.tipo_orden_dto <> $excluyeTipoOrdDto";
		if(!empty($proveedor_id)){
			$WHERE .= " AND LiquidacionCuotaNoimputada.proveedor_id = $proveedor_id AND (LiquidacionCuotaNoimputada.saldo_actual - LiquidacionCuotaNoimputada.importe_debitado) > 0 ";
		}
		#########################################################################
		#si es anses aplico lo cobrado a lo del periodo solamente (guillermo)
		#########################################################################
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();
		$liquidacion = $oLQ->read(null,$liquidacion_id);		
		if(substr($liquidacion['Liquidacion']['codigo_organismo'],8,2) == 66){
			//analizar el monto de lo debitado con la deuda del periodo, si sobra plata y tiene atraso imputar a lo atrasado
			App::import('Model','Mutual.LiquidacionSocioRendicion');
			$oLSR = new LiquidacionSocioRendicion();
			$debitado = $oLSR->getTotalBySocioByLiquidacion($socio_id,$liquidacion_id,1);
			$LiqPeriodo = $this->getTotalLiquidadoBySocioByLiquidacion($socio_id,$liquidacion_id,'P');
			if($debitado <= $LiqPeriodo) $WHERE .= " AND LiquidacionCuotaNoimputada.periodo_cuota = " . $liquidacion['Liquidacion']['periodo'];
		}
		
		#GENERO LA CONSULTA
//		$sql = "SELECT
//					LiquidacionCuotaNoimputada.id,
//					LiquidacionCuotaNoimputada.liquidacion_id,
//					LiquidacionCuotaNoimputada.socio_id,
//					LiquidacionCuotaNoimputada.persona_beneficio_id,
//					LiquidacionCuotaNoimputada.orden_descuento_cuota_id,
//					LiquidacionCuotaNoimputada.importe,
//					LiquidacionCuotaNoimputada.saldo_actual,
//					LiquidacionCuotaNoimputada.importe_debitado,
//					LiquidacionCuotaNoimputada.orden_descuento_cobro_id,
//					LiquidacionCuotaNoimputada.proveedor_id,
//                                        LiquidacionCuotaNoimputada.orden_descuento_id
//				FROM liquidacion_cuota_noimputadas AS LiquidacionCuotaNoimputada 
//				INNER JOIN global_datos AS GlobalDato ON (LiquidacionCuotaNoimputada.tipo_cuota = GlobalDato.id) 
//				LEFT JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON (LiquidacionCuotaNoimputada.orden_descuento_cuota_id = OrdenDescuentoCuota.id)  
//				WHERE $WHERE   
//				ORDER BY 
//                                if(LiquidacionCuotaNoimputada.proveedor_id <> 18,999999,1) ASC,
//				GlobalDato.entero_2 ASC,  
//				LiquidacionCuotaNoimputada.periodo_cuota ASC,  
//				OrdenDescuentoCuota.nro_cuota ASC,  
//				LiquidacionCuotaNoimputada.saldo_actual ASC, 
//                                LiquidacionCuotaNoimputada.orden_descuento_id ASC";
		$sql = "SELECT
					LiquidacionCuotaNoimputada.id,
					LiquidacionCuotaNoimputada.liquidacion_id,
					LiquidacionCuotaNoimputada.socio_id,
					LiquidacionCuotaNoimputada.persona_beneficio_id,
					LiquidacionCuotaNoimputada.orden_descuento_cuota_id,
					LiquidacionCuotaNoimputada.importe,
					LiquidacionCuotaNoimputada.saldo_actual,
					LiquidacionCuotaNoimputada.importe_debitado,
					LiquidacionCuotaNoimputada.orden_descuento_cobro_id,
					LiquidacionCuotaNoimputada.proveedor_id,
                                        LiquidacionCuotaNoimputada.orden_descuento_id,
                                        PersonaBeneficio.codigo_beneficio,
                                        LiquidacionCuotaNoimputada.tipo_cuota,
                                        LiquidacionCuotaNoimputada.tipo_producto
				FROM liquidacion_cuota_noimputadas AS LiquidacionCuotaNoimputada 
				INNER JOIN global_datos AS GlobalDato ON (LiquidacionCuotaNoimputada.tipo_cuota = GlobalDato.id) 
				LEFT JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON (LiquidacionCuotaNoimputada.orden_descuento_cuota_id = OrdenDescuentoCuota.id)  
                                LEFT JOIN proveedores Proveedor on (Proveedor.id = LiquidacionCuotaNoimputada.proveedor_id)
                                INNER JOIN liquidaciones Liquidacion on (Liquidacion.id = LiquidacionCuotaNoimputada.liquidacion_id)
                                INNER JOIN persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
				WHERE $WHERE   
				ORDER BY 
                                if(1=1,ifnull((select prioridad from proveedor_prioridad_imputa_organismos pp where pp.proveedor_id = Proveedor.id and pp.codigo_organismo = Liquidacion.codigo_organismo),99999),1) ASC,
				GlobalDato.entero_2 ASC,  
				LiquidacionCuotaNoimputada.periodo_cuota ASC,  
				OrdenDescuentoCuota.nro_cuota DESC,   
                                LiquidacionCuotaNoimputada.orden_descuento_id ASC"; 
		$registros = $this->query($sql);
		return $registros;
	}
	
	function distribuyeImporteCuotas($cuotas,$importe){
		
// 		debug($importe);
// 		debug($cuotas);
		
		if($importe == 0) return $cuotas;

		$saldoDebitoSocio = $importe;
		$importeImputaCuota = 0;
                
                App::import('model','Proveedores.ProveedorComision');
                $oPCOM = new ProveedorComision();
		
		foreach($cuotas as $idx => $cuota):
		
// 			$importe_cuota = $cuota['LiquidacionCuotaNoimputada']['saldo_actual'];
			$importe_cuota = $cuota['LiquidacionCuotaNoimputada']['saldo_actual'] - $cuota['LiquidacionCuotaNoimputada']['importe_debitado'];
			
			if($saldoDebitoSocio >= $importe_cuota):
				$importeImputaCuota = $importe_cuota;
				$saldoDebitoSocio -= $importe_cuota;
			else:
				$importeImputaCuota = $saldoDebitoSocio;
				$saldoDebitoSocio -= $importeImputaCuota;
			endif;
			
// 			$cuota['LiquidacionCuotaNoimputada']['importe_debitado'] = $importeImputaCuota;
			$cuota['LiquidacionCuotaNoimputada']['importe_debitado'] = $cuota['LiquidacionCuotaNoimputada']['importe_debitado'] + $importeImputaCuota;
			
			$cuota['LiquidacionCuotaNoimputada']['para_imputar'] = 1;	
                        
                        $porcenajeComision = $oPCOM->getComision($cuota['PersonaBeneficio']['codigo_beneficio'],$cuota['LiquidacionCuotaNoimputada']['proveedor_id']);
                        $cuota['LiquidacionCuotaNoimputada']['alicuota_comision_cobranza'] = $porcenajeComision;
                        $cuota['LiquidacionCuotaNoimputada']['comision_cobranza'] = $cuota['LiquidacionCuotaNoimputada']['importe_debitado'] * ($porcenajeComision / 100);
                        
			$cuotas[$idx] = $cuota;
			
			if($saldoDebitoSocio == 0) break;			
		
		endforeach;
		$cuotas = Set::extract("/LiquidacionCuota[importe_debitado>0]",$cuotas);
		$cuotas = Set::extract("{n}.LiquidacionCuota",$cuotas);		
		return $cuotas;
	}
	
	/**
	 * Arma Imputacion
	 * devuelve las cuotas de un socio para una liquidacion con la imputacion. Trae el total cobrado por el socio para
	 * una liquidacion y se lo aplica a las cuotas (borra la imputacion previa)
	 * @param $liquidacion_id
	 * @param $socio_id
	 * @param $proveedor_id
	 * @return cuotasImputadas
	 */
	function armaImputacion($liquidacion_id,$socio_id,$proveedor_id = null){
		
		$conditions = array();
		$conditions['LiquidacionCuotaNoimputada.liquidacion_id'] = $liquidacion_id;
		$conditions['LiquidacionCuotaNoimputada.socio_id'] = $socio_id;

		#no viene el proveedor marco TODO como NO pre-imputado
		if(empty($proveedor_id)){
			$conditions = array();
			$conditions['LiquidacionCuotaNoimputada.liquidacion_id'] = $liquidacion_id;
			$conditions['LiquidacionCuotaNoimputada.socio_id'] = $socio_id;
			$this->updateAll(
							array(
                                                            'LiquidacionCuotaNoimputada.importe_debitado' => 0,
                                                            'LiquidacionCuotaNoimputada.para_imputar' => 0,
                                                            'LiquidacionCuotaNoimputada.alicuota_comision_cobranza' => 0,
                                                            'LiquidacionCuotaNoimputada.comision_cobranza' => 0,
                                                        ),
							$conditions
						);			
		}
		
		$cuotas = null;
		
		$cuotas = $this->getCuotasByCriterioImputacion($liquidacion_id,$socio_id,false,null,$proveedor_id);
		
// 		debug($cuotas);
		
		if(empty($cuotas)) return null;
		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();
		
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();		
		
		$importeImputaCuota = 0;
		$saldoDebitoSocio = $oLSR->getTotalBySocioByLiquidacion($socio_id,$liquidacion_id,1,$proveedor_id);

		if($saldoDebitoSocio == 0) return $cuotas;
		
		//SACA EL TOTAL DE REINTEGROS QUE DEBEN COMPENSARSE
		App::import('Model','Pfyj.SocioReintegro');
		$oSR = new SocioReintegro();

		$a_compensar = $oSR->getTotalReintegrosAnticipadosACompensar($socio_id,$liquidacion_id);
		if($a_compensar != 0) $saldoDebitoSocio = $saldoDebitoSocio - $a_compensar;
		
		$acum = 0;
		
		return $this->distribuyeImporteCuotas($cuotas,$saldoDebitoSocio);
		
	}
	

	
	/**
	 * NUEVO ESQUEMA DE IMPUTACION CJP
	 * @param $liquidacion_id
	 * @param $socio_id
	 * @return cuotasImputadas
	 */
	function armaImputacionCJP($liquidacion_id,$socio_id){
		
		$this->updateAll(
				array('LiquidacionCuotaNoimputada.importe_debitado' => 0,'LiquidacionCuotaNoimputada.para_imputar' => 0),
				array(
					'LiquidacionCuotaNoimputada.socio_id' => $socio_id,
					'LiquidacionCuotaNoimputada.liquidacion_id' => $liquidacion_id,
				)
		);		
		
		$oLQ = ClassRegistry::init('Mutual.Liquidacion');
		
		$oLSR = ClassRegistry::init('Mutual.LiquidacionSocioRendicion');		
		
		$conditions = array();
		$conditions['LiquidacionSocioRendicion.liquidacion_id'] = $liquidacion_id;
		$conditions['LiquidacionSocioRendicion.socio_id'] = $socio_id;
		$conditions['LiquidacionSocioRendicion.indica_pago'] = 1;
		
		$datos = $oLSR->find('all',array('conditions' => $conditions, 
											'order' => array('LiquidacionSocioRendicion.codigo_dto,LiquidacionSocioRendicion.sub_codigo'),
											'fields' => array('LiquidacionSocioRendicion.sub_codigo,LiquidacionSocioRendicion.orden_descuento_id,sum(LiquidacionSocioRendicion.importe_debitado) as importe_debitado'),
											'group' => array('LiquidacionSocioRendicion.sub_codigo,LiquidacionSocioRendicion.orden_descuento_id'),
										)
		);

		$liquidacion = $oLQ->read(null,$liquidacion_id);
		$periodoLiquidado = $liquidacion['Liquidacion']['periodo'];
		
		
		if(empty($datos)) return null;
		
		
		$cuotas = array();
		
		//imputo la cuota social
		
		
		
		foreach($datos as $dato):
		
			$dato['LiquidacionSocioRendicion']['importe_debitado'] = $dato[0]['importe_debitado'];
		
		
			$conditions = array();
			$conditions['LiquidacionCuotaNoimputada.liquidacion_id'] = $liquidacion_id;
			$conditions['LiquidacionCuotaNoimputada.socio_id'] = $socio_id;
			
			//proceso la cuota social del periodo
			if($dato['LiquidacionSocioRendicion']['sub_codigo'] == 0){
				$conditions['LiquidacionCuotaNoimputada.periodo_cuota'] = $periodoLiquidado;
				$conditions['LiquidacionCuotaNoimputada.tipo_cuota'] = 'MUTUTCUOCSOC';
				$cuota = $this->find('all',array('conditions' => $conditions));
				if(!empty($cuota)){
					$cuota = $cuota[0]['LiquidacionCuotaNoimputada'];
					$cuota['importe_debitado'] = $dato['LiquidacionSocioRendicion']['importe_debitado'];
					$cuota['para_imputar'] = 1;				
					array_push($cuotas,$cuota);
				}
				
			}else{
				
				$conditions['LiquidacionCuotaNoimputada.orden_descuento_id'] = $dato['LiquidacionSocioRendicion']['orden_descuento_id'];
				$cuota = $this->find('all',array('conditions' => $conditions, 'order' => array('LiquidacionCuotaNoimputada.periodo_cuota')));

				if(!empty($cuota)){
					
					$cuotas = $this->armaImputacionCJP_imputaCuotaByOrdenDescuento($cuotas,$cuota,$dato['LiquidacionSocioRendicion']['importe_debitado']);

// 					$saldoDebitoSocio = $dato['LiquidacionSocioRendicion']['importe_debitado'];
// 					$importeImputaCuota = 0;
					
// 					foreach($cuota as $idx => $reg):
// 						$importe_cuota = $reg['LiquidacionCuotaNoimputada']['saldo_actual'];
// 						if($saldoDebitoSocio >= $importe_cuota):
// 							$importeImputaCuota = $importe_cuota;
// 							$saldoDebitoSocio -= $importe_cuota;
// 						else:
// 							$importeImputaCuota = $saldoDebitoSocio;
// 							$saldoDebitoSocio -= $importeImputaCuota;
// 						endif;
						
// 						$reg['LiquidacionCuotaNoimputada']['importe_debitado'] = $importeImputaCuota;
// 						$reg['LiquidacionCuotaNoimputada']['para_imputar'] = 1;	
// 						array_push($cuotas,$reg['LiquidacionCuotaNoimputada']);
						
// 						if($saldoDebitoSocio == 0) break;			
					
// 					endforeach;

				}else{
					
					//VIENE EL NUMERO DE EXPEDIENTE
// 					DEBUG($dato);
					$referencia = $dato['LiquidacionSocioRendicion']['orden_descuento_id'];
					$importe = $dato[0]['importe_debitado'];
					$concepto = substr($referencia,-1);
					$credito = substr($referencia,0,strlen($referencia) - 1);
// 					debug($referencia .  " *** "  . $credito . " *** " . $concepto . " ** " . $importe);
                                        
                                        /*
                                         * SI EL DIGITO ES DISTINTO A 1/2 LE ASIGNO POR DEFAULT 2
                                         * COMO NRO DE CREDITO LE PONGO LA REFERENCIA TAL COMO VIENE EN EL
                                         * ARCHIVO DE LA CAJA
                                         */
                                        
                                        switch ($concepto){
                                            case 1:
                                                break;
                                            case 2:
                                                break;
                                            default:
                                                $concepto = 2;
                                                $credito = $referencia;
                                                break;
                                        }
                                        
                                        
					$sql = null;
					#CREDITO
					if($concepto == 1){
                                            $sql = "SELECT LiquidacionCuotaNoimputada.orden_descuento_id FROM liquidacion_cuota_noimputadas AS LiquidacionCuotaNoimputada
                                                            INNER JOIN orden_descuentos AS OrdenDescuento ON (OrdenDescuento.id = LiquidacionCuotaNoimputada.orden_descuento_id)
                                                            WHERE LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
                                                            AND LiquidacionCuotaNoimputada.socio_id = $socio_id
                                                            AND OrdenDescuento.numero = $credito AND OrdenDescuento.proveedor_id <> 18";
					}else if($concepto == 2){
					#SEGURO
                                            $sql = "SELECT LiquidacionCuotaNoimputada.orden_descuento_id FROM liquidacion_cuota_noimputadas AS LiquidacionCuotaNoimputada
                                                            INNER JOIN orden_descuentos AS OrdenDescuento ON (OrdenDescuento.id = LiquidacionCuotaNoimputada.orden_descuento_id)
                                                            WHERE LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
                                                            AND LiquidacionCuotaNoimputada.socio_id = $socio_id
                                                            AND OrdenDescuento.numero = $credito AND OrdenDescuento.proveedor_id = 18";						
                                        }
                                        
                                        /// ADRIAN MARILI VER TEMA IMPUTACION CAJA SOCIO 12742
                                        // VALIDAR QUE VENGA SOLAMENTE 1 / 2
                                        
					$cuota = null;
					if(!empty($sql)){
						$datos2 = $this->query($sql);
						$conditions['LiquidacionCuotaNoimputada.orden_descuento_id'] = $datos2[0]['LiquidacionCuotaNoimputada']['orden_descuento_id'];
						$cuota = $this->find('all',array('conditions' => $conditions, 'order' => array('LiquidacionCuotaNoimputada.periodo_cuota')));
						if(!empty($cuota)) $cuotas = $this->armaImputacionCJP_imputaCuotaByOrdenDescuento($cuotas,$cuota,$dato['LiquidacionSocioRendicion']['importe_debitado']);
					}
					
					//NO ENCONTRO LAS CUOTAS BUSCO SI ES UNA ORDEN NOVADA
					if(empty($cuota)){
						$referencia = $dato['LiquidacionSocioRendicion']['orden_descuento_id'];
						$sql = "SELECT OrdenDescuento.nueva_orden_descuento_id FROM liquidacion_socio_rendiciones LiquidacionSocioRendicion
								INNER JOIN orden_descuentos OrdenDescuento ON (OrdenDescuento.id = LiquidacionSocioRendicion.orden_descuento_id)
								WHERE 
								LiquidacionSocioRendicion.liquidacion_id = $liquidacion_id
								AND LiquidacionSocioRendicion.socio_id = $socio_id
								AND LiquidacionSocioRendicion.orden_descuento_id = $referencia";
						$datos3 = $this->query($sql);
						$conditions['LiquidacionCuotaNoimputada.orden_descuento_id'] = $datos3[0]['OrdenDescuento']['nueva_orden_descuento_id'];
						$cuota = $this->find('all',array('conditions' => $conditions, 'order' => array('LiquidacionCuotaNoimputada.periodo_cuota')));
						if(!empty($cuota)) $cuotas = $this->armaImputacionCJP_imputaCuotaByOrdenDescuento($cuotas,$cuota,$dato['LiquidacionSocioRendicion']['importe_debitado']);
					}
					
					//PARA EL CASO DE LAS AYUDAS ECONOMICAS BUSCO POR NUMERO DE SOLICITUD
					if(empty($cuota)){
						$referencia = $dato['LiquidacionSocioRendicion']['orden_descuento_id'];
						$sql = "SELECT LiquidacionCuotaNoimputada.orden_descuento_id FROM liquidacion_cuota_noimputadas AS LiquidacionCuotaNoimputada
								INNER JOIN orden_descuentos AS OrdenDescuento ON (OrdenDescuento.id = LiquidacionCuotaNoimputada.orden_descuento_id)
								WHERE LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
								AND LiquidacionCuotaNoimputada.socio_id = $socio_id
								AND OrdenDescuento.numero = $referencia";
						$datos3 = $this->query($sql);
						$conditions['LiquidacionCuotaNoimputada.orden_descuento_id'] = $datos3[0]['LiquidacionCuotaNoimputada']['orden_descuento_id'];
						$cuota = $this->find('all',array('conditions' => $conditions, 'order' => array('LiquidacionCuotaNoimputada.periodo_cuota')));
						if(!empty($cuota)) $cuotas = $this->armaImputacionCJP_imputaCuotaByOrdenDescuento($cuotas,$cuota,$dato['LiquidacionSocioRendicion']['importe_debitado']);
					}
					
				}
				
				
				
				
				
			}
		
		endforeach;
		
		return $cuotas;
		
	}
	
	
	function armaImputacionCJP_imputaCuotaByOrdenDescuento($cuotas,$cuota,$importeDebitado){
		$saldoDebitoSocio = $importeDebitado;
		$importeImputaCuota = 0;
			
		foreach($cuota as $idx => $reg):
			$importe_cuota = $reg['LiquidacionCuotaNoimputada']['saldo_actual'];
			if($saldoDebitoSocio >= $importe_cuota):
				$importeImputaCuota = $importe_cuota;
				$saldoDebitoSocio -= $importe_cuota;
			else:
				$importeImputaCuota = $saldoDebitoSocio;
				$saldoDebitoSocio -= $importeImputaCuota;
			endif;
			
			$reg['LiquidacionCuotaNoimputada']['importe_debitado'] = $importeImputaCuota;
			$reg['LiquidacionCuotaNoimputada']['para_imputar'] = 1;
			array_push($cuotas,$reg['LiquidacionCuotaNoimputada']);
			
			if($saldoDebitoSocio == 0) break;
			
		endforeach;	
// 		debug($cuotas);	
		return $cuotas;
	}
	
	
	/**
	 * Get Total
	 * devuelve el total total liquidado
	 * @param integer $liquidacion_id
	 * @return float
	 */
	function getTotal($liquidacion_id){
		$condiciones = array(
							'conditions' => array(
								'LiquidacionCuotaNoimputada.liquidacion_id' => $liquidacion_id,
							),
							'fields' => array('SUM(LiquidacionCuotaNoimputada.saldo_actual) as deuda'),
		);
		$cuota = $this->find('all',$condiciones);
		return (isset($cuota[0][0]['deuda']) ? $cuota[0][0]['deuda'] : 0);		
	}
	
	/**
	 * Get Total Vencido
	 * Devuelve el total liquidado. Si $vencido = 1 calcula sobre lo vencido unicamente.
	 * @param integer $liquidacion_id
	 * @param integer $vencido
	 * @return float
	 */
	function getTotalVencido($liquidacion_id,$vencido=0){
		$condiciones = array(
							'conditions' => array(
								'LiquidacionCuotaNoimputada.liquidacion_id' => $liquidacion_id,
								'LiquidacionCuotaNoimputada.vencida' => $vencido,
							),
							'fields' => array('SUM(LiquidacionCuotaNoimputada.saldo_actual) as deuda'),
		);
		$cuota = $this->find('all',$condiciones);
		return (isset($cuota[0][0]['deuda']) ? $cuota[0][0]['deuda'] : 0);
	}	
	
	/**
	 * Get Total Cuota Social
	 * Devuelve el total liquidado por cuota social. Si $vencida = 1 devuelve el total liquidado de cuota social vencida
	 * @param $liquidacion_id
	 * @param $vencida
	 * @return float
	 */
	function getTotalCuotaSocial($liquidacion_id,$vencida=0){
		$condiciones = array(
							'conditions' => array(
								'LiquidacionCuotaNoimputada.liquidacion_id' => $liquidacion_id,
								'LiquidacionCuotaNoimputada.tipo_orden_dto' => 'CMUTU',
								'LiquidacionCuotaNoimputada.tipo_producto' => 'MUTUPROD0003',
								'LiquidacionCuotaNoimputada.vencida' => $vencida,
							),
							'fields' => array('SUM(LiquidacionCuotaNoimputada.saldo_actual) as deuda'),
		);
		$cuota = $this->find('all',$condiciones);
		return (isset($cuota[0][0]['deuda']) ? $cuota[0][0]['deuda'] : 0);
	}

	/**
	 * Get Total Deuda
	 * Devuelve el total deuda liquidada que NO sea CUOTA SOCIAL. Si el parametro $vencida = 1 solamente calcula sobre lo vencido
	 * @param integer $liquidacion_id
	 * @param boolean $vencida
	 * @return float
	 */
	function getTotalDeuda($liquidacion_id,$vencida=0){
		$condiciones = array(
							'conditions' => array(
								'LiquidacionCuotaNoimputada.liquidacion_id' => $liquidacion_id,
								'LiquidacionCuotaNoimputada.tipo_orden_dto <>' => 'CMUTU',
								'LiquidacionCuotaNoimputada.tipo_producto <>' => 'MUTUPROD0003',
								'LiquidacionCuotaNoimputada.vencida' => $vencida,
							),
							'fields' => array('SUM(LiquidacionCuotaNoimputada.saldo_actual) as deuda'),
		);
		$cuota = $this->find('all',$condiciones);
		return (isset($cuota[0][0]['deuda']) ? $cuota[0][0]['deuda'] : 0);
	}
	
	/**
	 * Get Total Imputado por Socio por Liquidacion
	 * Total imputado por socio por liquidacion.
	 * devuelve el total imputado por liquidacion por socio
	 * @param integer $liquidacion_id
	 * @param integer $socio_id
	 */
	function getTotalImputadoBySocioByLiquidacion($liquidacion_id,$socio_id){
		$condiciones = array(
							'conditions' => array(
								'LiquidacionCuotaNoimputada.liquidacion_id' => $liquidacion_id,
								'LiquidacionCuotaNoimputada.socio_id' => $socio_id,
								'LiquidacionCuotaNoimputada.importe_debitado >' => 0,
							),
							'fields' => array('SUM(LiquidacionCuotaNoimputada.importe_debitado) as importe_debitado'),
		);
		$cuota = $this->find('all',$condiciones);
		return (isset($cuota[0][0]['importe_debitado']) ? $cuota[0][0]['importe_debitado'] : 0);		
	}
	
	/**
	 * Get Total Imputado por Socio por Periodo de Liquidacion
	 * @param unknown_type $socio_id
	 * @param unknown_type $periodo
	 * @param $tipo --> tipo de importe a calcular (T = TODO LO LIQUIDADO, A = SOLO ATRASO, P =  SOLO PERIODO)
	 * @return unknown_type
	 */
	function getTotalImputadoBySocioByPeriodo($socio_id,$periodo,$tipo='T'){
		$this->bindModel(array('belongsTo' => array('Liquidacion')));
		$conditions = array();
		$conditions['Liquidacion.periodo'] = $periodo;
		$conditions['LiquidacionCuotaNoimputada.socio_id'] = $socio_id;
		$conditions['LiquidacionCuotaNoimputada.imputada'] = 1;
		if($tipo != 'T'):
			if($tipo == 'A') $conditions['LiquidacionCuotaNoimputada.periodo_cuota <>'] = $periodo;
			if($tipo == 'P') $conditions['LiquidacionCuotaNoimputada.periodo_cuota'] = $periodo;
		endif;		
		$cuota = $this->find('all',array('conditions' => $conditions,'fields' => array('SUM(LiquidacionCuotaNoimputada.importe_debitado) as importe_debitado'),));
		return (isset($cuota[0][0]['importe_debitado']) ? $cuota[0][0]['importe_debitado'] : 0);		
	}
	
	/**
	 * Devuelve el total liquidado para un periodo / socio. Si el parametro $atraso es true
	 * analiza el atraso liquidado
	 * @param $socio_id
	 * @param $liquidacion_id
	 * @param $periodo
	 * @param $distintoPeriodo
	 * @return unknown_type
	 */
	function getTotalLiquidadoBySocioByPeriodo($socio_id,$liquidacion_id,$periodo,$atraso=false){
		$conditions = array();
		$this->bindModel(array('belongsTo' => array('Liquidacion')));
		$conditions['LiquidacionCuotaNoimputada.socio_id'] = $socio_id;
		$conditions['LiquidacionCuotaNoimputada.liquidacion_id'] = $liquidacion_id;
		if(!$atraso)$conditions['Liquidacion.periodo'] = $periodo;
		else $conditions['Liquidacion.periodo <>'] = $periodo;
		$cuota = $this->find('all',array('conditions' => $conditions,'fields' => array('SUM(LiquidacionCuotaNoimputada.saldo_actual) as saldo_actual'),));
		return (isset($cuota[0][0]['saldo_actual']) ? $cuota[0][0]['saldo_actual'] : 0);			
	}
	
	/**
	 * Devuelve el total liquidado para un periodo
	 * @param $socio_id
	 * @param $periodo
	 * @param $tipo --> tipo de importe a calcular (T = TODO LO LIQUIDADO, A = SOLO ATRASO, P =  SOLO PERIODO)
	 * @return unknown_type
	 */
	function getTotalLiquidadoBySocioByPeriodo2($socio_id,$periodo,$tipo='T'){
		$conditions = array();
		$this->bindModel(array('belongsTo' => array('Liquidacion')));
		$conditions['LiquidacionCuotaNoimputada.socio_id'] = $socio_id;
		$conditions['Liquidacion.periodo'] = $periodo;
		if($tipo != 'T'):
			if($tipo == 'A') $conditions['LiquidacionCuotaNoimputada.periodo_cuota <>'] = $periodo;
			if($tipo == 'P') $conditions['LiquidacionCuotaNoimputada.periodo_cuota'] = $periodo;
		endif;
		$cuota = $this->find('all',array('conditions' => $conditions,'fields' => array('SUM(LiquidacionCuotaNoimputada.saldo_actual) as saldo_actual'),));
		return (isset($cuota[0][0]['saldo_actual']) ? $cuota[0][0]['saldo_actual'] : 0);			
	}
	
	
	/**
	 * Devuelve el total liquidado para un periodo
	 * @param $socio_id
	 * @param $liquidacion_id
	 * @param $tipo --> tipo de importe a calcular (T = TODO LO LIQUIDADO, A = SOLO ATRASO, P =  SOLO PERIODO)
	 * @return unknown_type
	 */
	function getTotalLiquidadoBySocioByLiquidacion($socio_id,$liquidacion_id,$tipo='T'){
		$conditions = array();
		$this->bindModel(array('belongsTo' => array('Liquidacion')));
		$conditions['LiquidacionCuotaNoimputada.socio_id'] = $socio_id;
		$conditions['LiquidacionCuotaNoimputada.liquidacion_id'] = $liquidacion_id;
		
		App::import("Model","mutual.Liquidacion");
		$oLIQ = new Liquidacion();
		$liquidacion = $oLIQ->read('periodo',$liquidacion_id);
		
		if($tipo != 'T'):
			if($tipo == 'A') $conditions['LiquidacionCuotaNoimputada.periodo_cuota <>'] = $liquidacion['Liquidacion']['periodo'];
			if($tipo == 'P') $conditions['LiquidacionCuotaNoimputada.periodo_cuota'] = $liquidacion['Liquidacion']['periodo'];
		endif;
		$cuota = $this->find('all',array('conditions' => $conditions,'fields' => array('SUM(LiquidacionCuotaNoimputada.saldo_actual) as saldo_actual'),));
		return (isset($cuota[0][0]['saldo_actual']) ? $cuota[0][0]['saldo_actual'] : 0);			
	}	
	
	
	
	/**
	 * Get Total Pendiente Imputar por Socio por Period
	 * Devuelve el total marcado para imputar y que no se imputo en la cuenta corriente. Es para mostrar en
	 * el estado de cuenta lo que se va a aplicar a la deuda cuando se procese la imputacion
	 * @param $socio_id
	 * @param $periodo
	 * @param $tipo --> tipo de importe a calcular (T = TODO LO LIQUIDADO, A = SOLO ATRASO, P =  SOLO PERIODO)
	 * @return unknown_type
	 */
	function getTotalPendienteImputarBySocioByPeriodo($socio_id,$periodo,$tipo='T'){
		$this->bindModel(array('belongsTo' => array('Liquidacion')));
//		$condiciones = array(
//							'conditions' => array(
//								'LiquidacionCuotaNoimputada.socio_id' => $socio_id,
//								'Liquidacion.periodo' => $periodo,
//								'LiquidacionCuotaNoimputada.para_imputar' => 1,
//								'LiquidacionCuotaNoimputada.imputada' => 0,
//							),
//							'fields' => array('SUM(LiquidacionCuotaNoimputada.importe_debitado) as importe_debitado'),
//		);
		$conditions['LiquidacionCuotaNoimputada.socio_id'] = $socio_id;
		$conditions['Liquidacion.periodo'] = $periodo;
		$conditions['LiquidacionCuotaNoimputada.para_imputar'] = 1;
		$conditions['LiquidacionCuotaNoimputada.imputada'] = 0;
		if($tipo != 'T'):
			if($tipo == 'A') $conditions['LiquidacionCuotaNoimputada.periodo_cuota <>'] = $periodo;
			if($tipo == 'P') $conditions['LiquidacionCuotaNoimputada.periodo_cuota'] = $periodo;
		endif;
		$cuota = $this->find('all',array('conditions' => $conditions,'fields' => array('SUM(LiquidacionCuotaNoimputada.importe_debitado) as importe_debitado'),));
		
//		$cuota = $this->find('all',$condiciones);
		return (isset($cuota[0][0]['importe_debitado']) ? $cuota[0][0]['importe_debitado'] : 0);		
	}		
	
	/**
	 * Get Total Imputado por Liquidacion
	 * Total imputado para una liquidacion
	 * @param $liquidacion_id
	 * @return unknown_type
	 */
	function getTotalImputadoByLiquidacion($liquidacion_id){
		$condiciones = array(
							'conditions' => array(
								'LiquidacionCuotaNoimputada.liquidacion_id' => $liquidacion_id,
								'LiquidacionCuotaNoimputada.importe_debitado >' => 0,
							),
							'fields' => array('SUM(LiquidacionCuotaNoimputada.importe_debitado) as importe_debitado'),
		);
		$cuota = $this->find('all',$condiciones);
		return (isset($cuota[0][0]['importe_debitado']) ? $cuota[0][0]['importe_debitado'] : 0);		
	}	
	
	/**
	 * Get Resumen de Liquidacion por Proveedor
	 * Devuelve el resumen de liquidacion de un proveedor
	 * @param integer $liquidacion_id
	 * @param string $periodo
	 * @param integer $proveedor_id
	 * @return array
	 */
	function getResumenLiquidacionByProveedor($liquidacion_id,$periodo,$proveedor_id = null, $incluyeCobrado = false, $incluyeReverso = false, $detalleCuotas = true){
		
		$this->bindModel(array('belongsTo' => array('Proveedor')));
		
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oCCUOTA = new OrdenDescuentoCobroCuota();		
		
		//saco los proveedores
		$condiciones = array('LiquidacionCuotaNoimputada.liquidacion_id' => $liquidacion_id);
		if(!empty($proveedor_id)) $condiciones['LiquidacionCuotaNoimputada.proveedor_id'] = $proveedor_id;

		$proveedores = $this->find('all',array(
											'conditions' => $condiciones,
											'fields' => array('Proveedor.id,Proveedor.razon_social'),
											'group' => array('LiquidacionCuotaNoimputada.proveedor_id'),
											'order' => array('Proveedor.razon_social')
		));	

		$TOTAL_PROVEEDOR = 0;
		$ATRASO_PROVEEDOR = 0;
		$PERIODO_PROVEEDOR = 0;
		$TOTAL_COBRADO_PROVEEDOR = 0;
		$ATRASO_COBRADO_PROVEEDOR = 0;
		$PERIODO_COBRADO_PROVEEDOR = 0;
		$COMISION_PROVEEDOR = 0;
		$REVERSADO_PROVEEDOR = 0;
		
		foreach($proveedores as $idx => $proveedor){
			$TOTAL_PROVEEDOR = 0;
			$ATRASO_PROVEEDOR = 0;
			$PERIODO_PROVEEDOR = 0;
			$TOTAL_COBRADO_PROVEEDOR = 0;
			$ATRASO_COBRADO_PROVEEDOR = 0;
			$PERIODO_COBRADO_PROVEEDOR = 0;
			$COMISION_PROVEEDOR = 0;
			$REVERSADO_PROVEEDOR = 0;
			
			$condiciones['LiquidacionCuotaNoimputada.proveedor_id'] = $proveedor['Proveedor']['id'];
			
			$conceptos = $this->find('all',array(
												'conditions' => array(
																	'LiquidacionCuotaNoimputada.liquidacion_id' => $liquidacion_id,
																	'LiquidacionCuotaNoimputada.proveedor_id' => $proveedor['Proveedor']['id']
												),
												'fields' => array('LiquidacionCuotaNoimputada.tipo_producto,LiquidacionCuotaNoimputada.tipo_cuota'),
												'group' => array('LiquidacionCuotaNoimputada.tipo_producto,LiquidacionCuotaNoimputada.tipo_cuota'),
												'order' => array('LiquidacionCuotaNoimputada.tipo_producto,LiquidacionCuotaNoimputada.tipo_cuota')
			));
			

			
			foreach($conceptos as $idx1 => $concepto){
				
				$totalPeriodo = $this->find('all',array(
												'conditions' => array(
																	'LiquidacionCuotaNoimputada.liquidacion_id' => $liquidacion_id,
																	'LiquidacionCuotaNoimputada.proveedor_id' => $proveedor['Proveedor']['id'],
																	'LiquidacionCuotaNoimputada.tipo_producto' => $concepto['LiquidacionCuotaNoimputada']['tipo_producto'],
																	'LiquidacionCuotaNoimputada.tipo_cuota' => $concepto['LiquidacionCuotaNoimputada']['tipo_cuota'],
																	'LiquidacionCuotaNoimputada.periodo_cuota' => $periodo
												),
												'fields' => array('ifnull(sum(importe),0) as total'),
				));
				if($incluyeCobrado):
					$totalPeriodoCobrado = $this->find('all',array(
													'conditions' => array(
																		'LiquidacionCuotaNoimputada.liquidacion_id' => $liquidacion_id,
																		'LiquidacionCuotaNoimputada.proveedor_id' => $proveedor['Proveedor']['id'],
																		'LiquidacionCuotaNoimputada.tipo_producto' => $concepto['LiquidacionCuotaNoimputada']['tipo_producto'],
																		'LiquidacionCuotaNoimputada.tipo_cuota' => $concepto['LiquidacionCuotaNoimputada']['tipo_cuota'],
																		'LiquidacionCuotaNoimputada.periodo_cuota' => $periodo
													),
													'fields' => array('ifnull(sum(importe_debitado),0) as total, ifnull(sum(comision_cobranza),0) as comision'),
					));
				endif;				
				
				$totalAtraso = $this->find('all',array(
												'conditions' => array(
																	'LiquidacionCuotaNoimputada.liquidacion_id' => $liquidacion_id,
																	'LiquidacionCuotaNoimputada.proveedor_id' => $proveedor['Proveedor']['id'],
																	'LiquidacionCuotaNoimputada.tipo_producto' => $concepto['LiquidacionCuotaNoimputada']['tipo_producto'],
																	'LiquidacionCuotaNoimputada.tipo_cuota' => $concepto['LiquidacionCuotaNoimputada']['tipo_cuota'],
																	'LiquidacionCuotaNoimputada.periodo_cuota <' => $periodo
												),
												'fields' => array('ifnull(sum(importe),0) as total'),
				));
				
				if($incluyeCobrado):
					$totalAtrasoCobrado = $this->find('all',array(
													'conditions' => array(
																		'LiquidacionCuotaNoimputada.liquidacion_id' => $liquidacion_id,
																		'LiquidacionCuotaNoimputada.proveedor_id' => $proveedor['Proveedor']['id'],
																		'LiquidacionCuotaNoimputada.tipo_producto' => $concepto['LiquidacionCuotaNoimputada']['tipo_producto'],
																		'LiquidacionCuotaNoimputada.tipo_cuota' => $concepto['LiquidacionCuotaNoimputada']['tipo_cuota'],
																		'LiquidacionCuotaNoimputada.periodo_cuota <' => $periodo
													),
													'fields' => array('ifnull(sum(importe_debitado),0) as total,ifnull(sum(comision_cobranza),0) as comision'),
					));				
				endif;
				
				$concepto['LiquidacionCuotaNoimputada']['total_periodo'] = round($totalPeriodo[0][0]['total'],2);
				
				$concepto['LiquidacionCuotaNoimputada']['total_atraso'] = round($totalAtraso[0][0]['total'],2);
				
				
				$concepto['LiquidacionCuotaNoimputada']['total'] = $concepto['LiquidacionCuotaNoimputada']['total_periodo'] + $concepto['LiquidacionCuotaNoimputada']['total_atraso'];
				$concepto['LiquidacionCuotaNoimputada']['total'] = round($concepto['LiquidacionCuotaNoimputada']['total'],2);

				//saco los reversos
				$reversado = 0;
				$mayorAlicuota = 0;
				if($incluyeReverso):
					$reversado = $oCCUOTA->getTotalReversoByProveedorByLiquidacion($proveedor['Proveedor']['id'],$liquidacion_id,$concepto['LiquidacionCuotaNoimputada']['tipo_producto'],$concepto['LiquidacionCuotaNoimputada']['tipo_cuota']);
					$concepto['LiquidacionCuotaNoimputada']['reversado'] = round($reversado,2);
					$mayorAlicuota = $oCCUOTA->getMayorComisionReversoByProveedorByLiquidacion($proveedor['Proveedor']['id'],$liquidacion_id,$concepto['LiquidacionCuotaNoimputada']['tipo_producto'],$concepto['LiquidacionCuotaNoimputada']['tipo_cuota']);
				endif;
				
				
				if($incluyeCobrado):
					$concepto['LiquidacionCuotaNoimputada']['total_periodo_cobrado'] = round($totalPeriodoCobrado[0][0]['total'],2);
					$concepto['LiquidacionCuotaNoimputada']['total_atraso_cobrado'] = round($totalAtrasoCobrado[0][0]['total'],2);
					$concepto['LiquidacionCuotaNoimputada']['cobrado'] = $concepto['LiquidacionCuotaNoimputada']['total_periodo_cobrado'] + $concepto['LiquidacionCuotaNoimputada']['total_atraso_cobrado'];
					$concepto['LiquidacionCuotaNoimputada']['cobrado'] = round($concepto['LiquidacionCuotaNoimputada']['cobrado'],2);
					$concepto['LiquidacionCuotaNoimputada']['comision'] = $totalPeriodoCobrado[0][0]['comision'] + $totalAtrasoCobrado[0][0]['comision'];
					$concepto['LiquidacionCuotaNoimputada']['comision'] = round($concepto['LiquidacionCuotaNoimputada']['comision'],2);
					$concepto['LiquidacionCuotaNoimputada']['comision_reversada'] = round((-1) * $reversado * ($mayorAlicuota / 100),2);
					$concepto['LiquidacionCuotaNoimputada']['neto_comision'] = $concepto['LiquidacionCuotaNoimputada']['comision'] + $concepto['LiquidacionCuotaNoimputada']['comision_reversada'];
					$concepto['LiquidacionCuotaNoimputada']['neto_proveedor'] = $concepto['LiquidacionCuotaNoimputada']['cobrado'] - $reversado - $concepto['LiquidacionCuotaNoimputada']['neto_comision'];
					$concepto['LiquidacionCuotaNoimputada']['neto_proveedor'] = round($concepto['LiquidacionCuotaNoimputada']['neto_proveedor'],2);
					
				endif;
				
				//saco las comisiones
				
				
				
				$TOTAL_PROVEEDOR += $concepto['LiquidacionCuotaNoimputada']['total'];
				$ATRASO_PROVEEDOR += $concepto['LiquidacionCuotaNoimputada']['total_atraso'];
				$PERIODO_PROVEEDOR += $concepto['LiquidacionCuotaNoimputada']['total_periodo'];	

				if($incluyeCobrado):
					$TOTAL_COBRADO_PROVEEDOR += $concepto['LiquidacionCuotaNoimputada']['cobrado'];
					$ATRASO_COBRADO_PROVEEDOR += $concepto['LiquidacionCuotaNoimputada']['total_atraso_cobrado'];
					$PERIODO_COBRADO_PROVEEDOR += $concepto['LiquidacionCuotaNoimputada']['total_periodo_cobrado'];
					$COMISION_PROVEEDOR += $concepto['LiquidacionCuotaNoimputada']['neto_comision'];
				endif;
				
				if($incluyeReverso):
					$REVERSADO_PROVEEDOR += $concepto['LiquidacionCuotaNoimputada']['reversado'];
				endif;
				
				$glb = $this->getGlobalDato('concepto_1',$concepto['LiquidacionCuotaNoimputada']['tipo_producto']);
				$concepto['LiquidacionCuotaNoimputada']['tipo_producto_desc'] = $glb['GlobalDato']['concepto_1'];
				
				$glb = $this->getGlobalDato('concepto_1',$concepto['LiquidacionCuotaNoimputada']['tipo_cuota']);
				$concepto['LiquidacionCuotaNoimputada']['tipo_cuota_desc'] = $glb['GlobalDato']['concepto_1'];				
				
				
				$conceptos[$idx1] = $concepto;
				
			}

			$proveedor['Proveedor']['total'] = $TOTAL_PROVEEDOR;
			$proveedor['Proveedor']['total_periodo'] = $PERIODO_PROVEEDOR;
			$proveedor['Proveedor']['total_atraso'] = $ATRASO_PROVEEDOR;
			if($incluyeReverso):
				$proveedor['Proveedor']['total_reversado'] = $REVERSADO_PROVEEDOR;
			endif;			
			if($incluyeCobrado):
				$proveedor['Proveedor']['total_cobrado'] = $TOTAL_COBRADO_PROVEEDOR;
				$proveedor['Proveedor']['total_periodo_cobrado'] = $PERIODO_COBRADO_PROVEEDOR;
				$proveedor['Proveedor']['total_atraso_cobrado'] = $ATRASO_COBRADO_PROVEEDOR;
				$proveedor['Proveedor']['total_comision'] = $COMISION_PROVEEDOR;
				$proveedor['Proveedor']['neto_proveedor'] = $proveedor['Proveedor']['total_cobrado'] -  $REVERSADO_PROVEEDOR - $proveedor['Proveedor']['total_comision'];
				$proveedor['Proveedor']['neto_proveedor'] = round($proveedor['Proveedor']['neto_proveedor'],2);
				$proveedor['Proveedor']['adeudado_periodo'] = round($PERIODO_PROVEEDOR - $PERIODO_COBRADO_PROVEEDOR,2);
				$proveedor['Proveedor']['adeudado_atraso'] = round($ATRASO_PROVEEDOR - $ATRASO_COBRADO_PROVEEDOR,2);	
				$proveedor['Proveedor']['adeudado'] = round($TOTAL_PROVEEDOR - $TOTAL_COBRADO_PROVEEDOR,2);		
			endif;
			
			if($detalleCuotas) $proveedor['Proveedor']['liquidacion'] = $conceptos;
		
			$proveedores[$idx] = $proveedor;
					
		}
		return $proveedores;			
	}
	
	
	
	
	/**
	 * Info Adicional
	 * Devuelve una cuota con la informacion adicional seteada
	 * @param array $cuota
	 * @param boolean $bindProveedorRazonSocial
	 * @return array
	 */
	function infoAdicional($cuota,$bindProveedorRazonSocial=true){
		
		if($bindProveedorRazonSocial && isset($cuota['LiquidacionCuotaNoimputada']['proveedor_id'])){
			App::import('Model', 'Proveedores.Proveedor');
			$oProveedor = new Proveedor();
			$razonSocial = $oProveedor->getRazonSocialResumida($cuota['LiquidacionCuotaNoimputada']['proveedor_id']);
			
			$cuota['LiquidacionCuotaNoimputada']['razon_social'] = $razonSocial;			
		}

		$cuota['LiquidacionCuotaNoimputada']['total'] = $cuota[0]['total'];
		$cuota['LiquidacionCuotaNoimputada']['cantidad'] = $cuota[0]['cantidad'];
		
		$glb = $this->getGlobalDato('concepto_1',$cuota['LiquidacionCuotaNoimputada']['tipo_producto']);
		$cuota['LiquidacionCuotaNoimputada']['tipo_producto_desc'] = $glb['GlobalDato']['concepto_1'];
		
		$glb = $this->getGlobalDato('concepto_1',$cuota['LiquidacionCuotaNoimputada']['tipo_cuota']);
		$cuota['LiquidacionCuotaNoimputada']['tipo_cuota_desc'] = $glb['GlobalDato']['concepto_1'];
		

		$glb = $this->getGlobalDato('concepto_1',$cuota['LiquidacionCuotaNoimputada']['codigo_organismo']);
		$cuota['LiquidacionCuotaNoimputada']['codigo_organismo_desc'] = $glb['GlobalDato']['concepto_1'];			
		
		return $cuota;				
	}
	
	/**
	 * Genera detalle liquidacion cuota
	 * Graba el registro en la liquidacion_cuota_noimputadas en base a la cuota que ya viene con el saldo 
	 * actual calculado
	 * @param integer $liquidacion_id
	 * @param string $periodoLiquidado
	 * @param array $cuota
	 */
	function generarDetalleLiquidacionCuota($liquidacion_id,$periodoLiquidado,$cuota,$pre_imputacion=false){
		
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBen = new PersonaBeneficio();	

		App::import('Model', 'Mutual.OrdenDescuentoCuota');
		$oCuota = new OrdenDescuentoCuota();			
		
		$cuota = $oCuota->__calculaSaldo($cuota,null,null,$pre_imputacion);
		$organismoCuota = $oBen->getCodigoOrganismo($cuota['OrdenDescuentoCuota']['persona_beneficio_id']);
		
//		debug($cuota);
		
		if($cuota['OrdenDescuentoCuota']['saldo_cuota'] == 0) return true;
		
		$registro = 2;

		if(substr($organismoCuota,8,2) == 22){
			if($cuota['OrdenDescuentoCuota']['periodo'] == $periodoLiquidado) $registro = 1;
		# si no es CBU la cuota social va como registro 1	
		}else if($cuota['OrdenDescuentoCuota']['tipo_producto'] == 'MUTUPROD0003' && $cuota['OrdenDescuentoCuota']['tipo_cuota'] == 'MUTUTCUOCSOC'){
			$registro = 1;
		}

		$data = array('LiquidacionCuotaNoimputada' => array(
				'id' => 0,
				'liquidacion_id' => $liquidacion_id,
				'socio_id' => $cuota['OrdenDescuentoCuota']['socio_id'],
				'persona_beneficio_id' => $cuota['OrdenDescuentoCuota']['persona_beneficio_id'],
				'orden_descuento_id' => $cuota['OrdenDescuentoCuota']['orden_descuento_id'],
				'orden_descuento_cuota_id' => $cuota['OrdenDescuentoCuota']['id'],
				'tipo_orden_dto' => $cuota['OrdenDescuentoCuota']['tipo_orden_dto'],
				'tipo_producto' => $cuota['OrdenDescuentoCuota']['tipo_producto'],
				'tipo_cuota' => $cuota['OrdenDescuentoCuota']['tipo_cuota'],
				'periodo_cuota' => $cuota['OrdenDescuentoCuota']['periodo'],
				'proveedor_id' => $cuota['OrdenDescuentoCuota']['proveedor_id'],
				'vencida' => $cuota['OrdenDescuentoCuota']['vencida'],
				'importe' => $cuota['OrdenDescuentoCuota']['importe'],
				'saldo_actual' => $cuota['OrdenDescuentoCuota']['saldo_cuota'],
				'codigo_organismo' => $organismoCuota,
				'registro' => $registro 
		));	
//		debug($data);
		// grabo el detalle de la liquidacion
		if($cuota['OrdenDescuentoCuota']['saldo_cuota'] > 0):
			$this->id = 0;		
                        $this->auditable = FALSE;
			return $this->save($data);
		else:
			return true;	
		endif;
		
	}
	

	/**
	 * Get Info Dto AMAN
	 * Arma consulta para generar la cabecera de la liquidacion del socio
	 * mutual AMAN: Arma el resumen para exportar los diskettes
	 * Segun el Organismo pasado por parametro arma la consulta para traer los datos <br/>
	 * <b>CBU (__armaResumenCBU()):</b> agrupa por cbu. Registro 1 = periodo, Registro 2 = atraso <br/>
	 * <b>ANSES (__armaResumenANSES()):</b> agrupa por nro_beneficio. Registro 1 = periodo, Registro 2 = atraso <br/>
	 * <b>CJP (__armaResumenCJP()):</b> agrupa por tipo, nro_ley, nro_beneficio y sub_beneficio. Registro 1 = periodo, Registro 2 = atraso
	 * @param $liquidacion_id
	 * @param $socio_id
	 * @param $periodo
	 * @return array
	 */
	function getInfoDto_AMAN($liquidacion_id,$socio_id,$periodo,$organismo,$CONTROL_NACION=false,$BANCO_CONTROL=null,$DISCRIMINA_PERMANENTES = false){
		
		$liquidacionCuota = array();
		
		$codigoOrganismo = substr($organismo,8,2);

		$sql = "";

		switch ($codigoOrganismo){
			case 22:
				#CBU
				#AGRUPA POR CBU y SEPARA LO DEL PERIODO CON EL ATRASO
//                                $cuotas = $this->__armaResumenCBU($liquidacion_id,$socio_id,$periodo,$organismo,$CONTROL_NACION,$BANCO_CONTROL);
				$cuotas = $this->__armaResumenCBU_NUEVO($liquidacion_id,$socio_id,$periodo,$organismo,$CONTROL_NACION,$BANCO_CONTROL,$DISCRIMINA_PERMANENTES);
				break;
			case 66:
				#ANSES
				$cuotas = $this->__armaResumenANSES($liquidacion_id,$socio_id,$periodo,$organismo);
				break;
			case 77:
				#CJP
//				$cuotas = $this->__armaResumenCJP($liquidacion_id,$socio_id,$periodo,$organismo);
				$cuotas = $this->__armaNuevoResumenCJP($liquidacion_id,$socio_id,$periodo,$organismo);				
				break;		
			default:
				#AGRUPA POR CBU y SEPARA LO DEL PERIODO CON EL ATRASO
				$cuotas = $this->__armaResumenCBU($liquidacion_id,$socio_id,$periodo,$organismo);
				break;								
		}

//		$cuotas = $this->query($sql);

		
//		debug($cuotas);
//		exit;
		if(!empty($cuotas)):
		
			App::import('Model', 'Pfyj.PersonaBeneficio');
			$oBen = new PersonaBeneficio();			
			
			App::import('Model', 'Mutual.OrdenDescuentoCuota');
			$oDeuda = new OrdenDescuentoCuota();			
			
			$deudaSegunCtaCte = 0;		
		
			$resumenSocio = array();
			$datosResumen = array();
			
			$ACUMULA_IMPODTO = 0;
			$ACUMULA_IMPODEB = 0;
			
			foreach($cuotas as $idx => $cuota){
				
//				debug($cuota);
				
				$socioID = (isset($cuota[0]['socio_id']) ? $cuota[0]['socio_id'] : (isset($cuota['LiquidacionCuotaNoimputada']['socio_id']) ? $cuota['LiquidacionCuotaNoimputada']['socio_id'] : null));
				$personaBeneficioID = (isset($cuota[0]['persona_beneficio_id']) ? $cuota[0]['persona_beneficio_id'] : (isset($cuota['LiquidacionCuotaNoimputada']['persona_beneficio_id']) ? $cuota['LiquidacionCuotaNoimputada']['persona_beneficio_id'] : null));
				$org = (isset($cuota[0]['codigo_organismo']) ? $cuota[0]['codigo_organismo'] : (isset($cuota['LiquidacionCuotaNoimputada']['codigo_organismo']) ? $cuota['LiquidacionCuotaNoimputada']['codigo_organismo'] : null));
				$registro = (isset($cuota[0]['registro']) ? $cuota[0]['registro'] : (isset($cuota['LiquidacionCuotaNoimputada']['registro']) ? $cuota['LiquidacionCuotaNoimputada']['registro'] : null));
				
//				DEBUG($cuota);
//				debug($personaBeneficioID);
				
				if(!empty($socioID)):
				
					#DETERMINO SI ES UN ALTA PARA MARCAR LA CABECERA DE LA LIQUIDACION
					$datosResumen['LiquidacionCuotaNoimputada']['liquidacion_id'] = $liquidacion_id;
					$datosResumen['LiquidacionCuotaNoimputada']['persona_beneficio_id'] = $personaBeneficioID;

					$datosResumen['LiquidacionCuotaNoimputada']['codigo_organismo'] = $org;
					$datosResumen['LiquidacionCuotaNoimputada']['socio_id'] = $socioID;
					$datosResumen['LiquidacionCuotaNoimputada']['codigo_dto'] = $cuota[0]['codigo_dto'];
					$datosResumen['LiquidacionCuotaNoimputada']['sub_codigo'] = $cuota[0]['sub_codigo'];
					
					$datosResumen['LiquidacionCuotaNoimputada']['criterio_deuda'] = (isset($cuota[0]['criterio_deuda']) ? $cuota[0]['criterio_deuda'] : 0);
					$datosResumen['LiquidacionCuotaNoimputada']['formula_criterio_deuda'] = (isset($cuota[0]['formula_criterio_deuda']) ? $cuota[0]['formula_criterio_deuda'] : 0);
					$datosResumen['LiquidacionCuotaNoimputada']['periodo'] = (isset($cuota[0]['periodo']) ? $cuota[0]['periodo'] : 0);
					
					//agrego los nuevos campos que trae el resumen de CJP
					$datosResumen['LiquidacionCuotaNoimputada']['detalla'] = (isset($cuota[0]['detalla']) ? $cuota[0]['detalla'] : 0);
					$datosResumen['LiquidacionCuotaNoimputada']['fecha_otorgamiento'] = (isset($cuota[0]['fecha_otorgamiento']) ? $cuota[0]['fecha_otorgamiento'] : null);
					$datosResumen['LiquidacionCuotaNoimputada']['importe_total'] = (isset($cuota[0]['importe_total']) ? $cuota[0]['importe_total'] : null);
					$datosResumen['LiquidacionCuotaNoimputada']['cuotas'] = (isset($cuota[0]['cuotas']) ? $cuota[0]['cuotas'] : null);
					$datosResumen['LiquidacionCuotaNoimputada']['importe_cuota'] = (isset($cuota[0]['importe_cuota']) ? $cuota[0]['importe_cuota'] : null);
					$datosResumen['LiquidacionCuotaNoimputada']['importe_deuda'] = (isset($cuota[0]['importe_deuda']) ? $cuota[0]['importe_deuda'] : null);
					$datosResumen['LiquidacionCuotaNoimputada']['importe_deuda_vencida'] = (isset($cuota[0]['importe_deuda_vencida']) ? $cuota[0]['importe_deuda_vencida'] : null);
					$datosResumen['LiquidacionCuotaNoimputada']['importe_deuda_no_vencida'] = (isset($cuota[0]['importe_deuda_no_vencida']) ? $cuota[0]['importe_deuda_no_vencida'] : null);
//					$datosResumen['LiquidacionCuotaNoimputada']['orden_descuento_id'] = (isset($cuota[0]['orden_descuento_id']) && isset($cuota[0]['detalla']) && $cuota[0]['detalla'] == 1  ? $cuota[0]['orden_descuento_id'] : null);
					$datosResumen['LiquidacionCuotaNoimputada']['orden_descuento_id'] = (isset($cuota[0]['orden_descuento_id']) ? $cuota[0]['orden_descuento_id'] : 0);
					
					$datosResumen['LiquidacionCuotaNoimputada']['registro'] = $registro;
					
					$oBen->bindModel(array('belongsTo' => array('Persona')));
					$beneficio = $oBen->read(null,$personaBeneficioID);
					
					
					
//					$ACUMULA_IMPODTO += $cuota[0]['deuda'];
//					$ACUMULA_IMPODEB += $cuota[0]['importe_adebitar'];
					
					
					$datosResumen['LiquidacionCuotaNoimputada']['turno_pago'] = (isset($cuota[0]['turno_pago']) && !empty($cuota[0]['turno_pago']) ? $cuota[0]['turno_pago'] : $oBen->getTurno($personaBeneficioID));
					$datosResumen['LiquidacionCuotaNoimputada']['tipo_documento'] = $beneficio['Persona']['tipo_documento'];
					$datosResumen['LiquidacionCuotaNoimputada']['documento'] = $beneficio['Persona']['documento'];
					$datosResumen['LiquidacionCuotaNoimputada']['cuit_cuil'] = $beneficio['Persona']['cuit_cuil'];
					$datosResumen['LiquidacionCuotaNoimputada']['persona_id'] = $beneficio['Persona']['id'];
					$datosResumen['LiquidacionCuotaNoimputada']['apenom'] = $beneficio['Persona']['apellido'].','.$beneficio['Persona']['nombre'];
					$datosResumen['LiquidacionCuotaNoimputada']['codigo_empresa'] = $beneficio['PersonaBeneficio']['codigo_empresa'];
					$datosResumen['LiquidacionCuotaNoimputada']['codigo_reparticion'] = (isset($cuota[0]['codigo_reparticion']) ? $cuota[0]['codigo_reparticion'] : $beneficio['PersonaBeneficio']['codigo_reparticion']);

//					$datosResumen['LiquidacionCuotaNoimputada']['porcentaje'] = $beneficio['PersonaBeneficio']['porcentaje'];

					$datosResumen['LiquidacionCuotaNoimputada']['porcentaje']	= (isset($cuota[0]['porcentaje']) ? $cuota[0]['porcentaje'] : 100);
					
//					debug($datosResumen);
					
					#SI EL REGISTRO QUE VIENE ES EL 2 (<> CUOTA SOCIAL) Y TIENE BENEFICIOS COMPARTIDOS LOS PROCESO
					//para el caso de cjp
//					if($registro == 2 && !empty($beneficio['PersonaBeneficioCompartido']) && substr($beneficio['PersonaBeneficio']['codigo_beneficio'],8,2) == '77' ){
//
//						foreach($beneficio['PersonaBeneficioCompartido'] as $beneficioCompartido):
//							if($beneficioCompartido['activo'] == 1):
//								$datosResumen['LiquidacionCuotaNoimputada']['nro_beneficio'] = $beneficioCompartido['nro_beneficio'];
//								$datosResumen['LiquidacionCuotaNoimputada']['nro_ley'] = $beneficioCompartido['nro_ley'];
//								$datosResumen['LiquidacionCuotaNoimputada']['tipo'] = $beneficioCompartido['tipo'];
//								$datosResumen['LiquidacionCuotaNoimputada']['sub_beneficio'] = $beneficioCompartido['sub_beneficio'];
//								$datosResumen['LiquidacionCuotaNoimputada']['banco_id'] = $beneficioCompartido['banco_id'];
//								$datosResumen['LiquidacionCuotaNoimputada']['sucursal'] = $beneficioCompartido['nro_sucursal'];
//								$datosResumen['LiquidacionCuotaNoimputada']['tipo_cta_bco'] = $beneficioCompartido['tipo_cta_bco'];
//								$datosResumen['LiquidacionCuotaNoimputada']['nro_cta_bco'] = $beneficioCompartido['nro_cta_bco'];
//								$datosResumen['LiquidacionCuotaNoimputada']['cbu'] = $beneficioCompartido['cbu'];
//								$datosResumen['LiquidacionCuotaNoimputada']['porcentaje'] = $beneficioCompartido['porcentaje'];
//								$datosResumen['LiquidacionCuotaNoimputada']['importe_dto'] = $cuota[0]['importe_adebitar'] * ($beneficioCompartido['porcentaje'] / 100);
//								$datosResumen['LiquidacionCuotaNoimputada']['importe_adebitar'] = $datosResumen['LiquidacionCuotaNoimputada']['importe_dto'];
//								
//								
//								if($datosResumen['LiquidacionCuotaNoimputada']['importe_dto'] != 0) array_push($resumenSocio,$datosResumen);
//							
//							endif;
//							
//						endforeach;
//						
//					}else{

						$datosResumen['LiquidacionCuotaNoimputada']['nro_beneficio'] = $beneficio['PersonaBeneficio']['nro_beneficio'];
						$datosResumen['LiquidacionCuotaNoimputada']['nro_ley'] = $beneficio['PersonaBeneficio']['nro_ley'];
						$datosResumen['LiquidacionCuotaNoimputada']['tipo'] = $beneficio['PersonaBeneficio']['tipo'];
						$datosResumen['LiquidacionCuotaNoimputada']['sub_beneficio'] = $beneficio['PersonaBeneficio']['sub_beneficio'];
						$datosResumen['LiquidacionCuotaNoimputada']['banco_id'] = $beneficio['PersonaBeneficio']['banco_id'];
						$datosResumen['LiquidacionCuotaNoimputada']['sucursal'] = $beneficio['PersonaBeneficio']['nro_sucursal'];
						$datosResumen['LiquidacionCuotaNoimputada']['tipo_cta_bco'] = $beneficio['PersonaBeneficio']['tipo_cta_bco'];
						$datosResumen['LiquidacionCuotaNoimputada']['nro_cta_bco'] = $beneficio['PersonaBeneficio']['nro_cta_bco'];
						$datosResumen['LiquidacionCuotaNoimputada']['cbu'] = $beneficio['PersonaBeneficio']['cbu'];
						
//						$datosResumen['LiquidacionCuotaNoimputada']['importe_dto'] = $cuota[0]['deuda'];
//						$datosResumen['LiquidacionCuotaNoimputada']['importe_adebitar'] = $cuota[0]['importe_adebitar'];
//						debug($beneficio);
						//le aplico el porcentaje
						$datosResumen['LiquidacionCuotaNoimputada']['importe_dto'] = $cuota[0]['deuda'] * $datosResumen['LiquidacionCuotaNoimputada']['porcentaje'] / 100;
						$datosResumen['LiquidacionCuotaNoimputada']['importe_adebitar'] = $cuota[0]['importe_adebitar'] * $datosResumen['LiquidacionCuotaNoimputada']['porcentaje'] / 100;
						
//						debug($datosResumen);

						$ACUMULA_IMPODTO += $datosResumen['LiquidacionCuotaNoimputada']['importe_dto'];
						$ACUMULA_IMPODEB += $datosResumen['LiquidacionCuotaNoimputada']['importe_adebitar'];						
						
						
						array_push($resumenSocio,$datosResumen);
						
//						if(!empty($beneficio['PersonaBeneficioCompartido'])):
//
//							foreach($beneficio['PersonaBeneficioCompartido'] as $beneficioCompartido):
//							
//								if($beneficioCompartido['activo'] == 1):
//								
//									$datosResumen['LiquidacionCuotaNoimputada']['nro_beneficio'] = $beneficioCompartido['nro_beneficio'];
//									$datosResumen['LiquidacionCuotaNoimputada']['nro_ley'] = $beneficioCompartido['nro_ley'];
//									$datosResumen['LiquidacionCuotaNoimputada']['tipo'] = $beneficioCompartido['tipo'];
//									$datosResumen['LiquidacionCuotaNoimputada']['sub_beneficio'] = $beneficioCompartido['sub_beneficio'];
//									$datosResumen['LiquidacionCuotaNoimputada']['banco_id'] = $beneficioCompartido['banco_id'];
//									$datosResumen['LiquidacionCuotaNoimputada']['sucursal'] = $beneficioCompartido['nro_sucursal'];
//									$datosResumen['LiquidacionCuotaNoimputada']['tipo_cta_bco'] = $beneficioCompartido['tipo_cta_bco'];
//									$datosResumen['LiquidacionCuotaNoimputada']['nro_cta_bco'] = $beneficioCompartido['nro_cta_bco'];
//									$datosResumen['LiquidacionCuotaNoimputada']['cbu'] = $beneficioCompartido['cbu'];
//									$datosResumen['LiquidacionCuotaNoimputada']['turno_pago'] = $beneficioCompartido['turno_pago'];
//									$datosResumen['LiquidacionCuotaNoimputada']['porcentaje'] = $beneficioCompartido['porcentaje'];
//									$datosResumen['LiquidacionCuotaNoimputada']['importe_dto'] = $cuota[0]['importe_adebitar'] * ($beneficioCompartido['porcentaje'] / 100);
//									$datosResumen['LiquidacionCuotaNoimputada']['importe_adebitar'] = $datosResumen['LiquidacionCuotaNoimputada']['importe_dto'];
//									
//									
//									if($datosResumen['LiquidacionCuotaNoimputada']['importe_dto'] != 0) array_push($resumenSocio,$datosResumen);
//								
//								endif;
//							
//							
//							endforeach;
//						
//						
//						endif;
						
						
						
//					}
			
//					DEBUG($datosResumen);
					
				endif;				
			}
		
//			debug($resumenSocio);
			
			if(!empty($resumenSocio)):
	
				$TOTAL_CALCULADO_DTO = 0;
				$TOTAL_CALCULADO_DEB = 0;
			
				foreach($resumenSocio as $id => $resumen):
					
//					$TOTAL_CALCULADO_DTO += round($resumen['LiquidacionCuotaNoimputada']['importe_dto'],2,PHP_ROUND_HALF_UP);
//					$TOTAL_CALCULADO_DEB += round($resumen['LiquidacionCuotaNoimputada']['importe_adebitar'],2,PHP_ROUND_HALF_UP);
//	
//					$resumen['LiquidacionCuotaNoimputada']['importe_dto'] = round($resumen['LiquidacionCuotaNoimputada']['importe_dto'],2,PHP_ROUND_HALF_UP);
//					$resumen['LiquidacionCuotaNoimputada']['importe_adebitar'] = round($resumen['LiquidacionCuotaNoimputada']['importe_adebitar'],2,PHP_ROUND_HALF_UP);

					$TOTAL_CALCULADO_DTO += round($resumen['LiquidacionCuotaNoimputada']['importe_dto'],2);
					$TOTAL_CALCULADO_DEB += round($resumen['LiquidacionCuotaNoimputada']['importe_adebitar'],2);
	
					$resumen['LiquidacionCuotaNoimputada']['importe_dto'] = round($resumen['LiquidacionCuotaNoimputada']['importe_dto'],2);
					$resumen['LiquidacionCuotaNoimputada']['importe_adebitar'] = round($resumen['LiquidacionCuotaNoimputada']['importe_adebitar'],2);
					
					
					
					$resumenSocio[$id] = $resumen;
					
				
				endforeach;
			
			endif;
			
	//		$TOTAL_CALCULADO_DTO = round($TOTAL_CALCULADO_DTO,2);
	//		$TOTAL_CALCULADO_DEB = round($TOTAL_CALCULADO_DEB,2);
			
	//		debug($TOTAL_CALCULADO_DTO ." || ".$TOTAL_CALCULADO_DEB);
			
			$DIFF_DTO = round($ACUMULA_IMPODTO - $TOTAL_CALCULADO_DTO,2);
			$DIFF_DEB = round($ACUMULA_IMPODEB - $TOTAL_CALCULADO_DEB,2);
			
//			debug($DIFF_DTO ." || ".$DIFF_DEB);
			
			if($DIFF_DTO != 0) $resumenSocio[0]['LiquidacionCuotaNoimputada']['importe_dto'] += $DIFF_DTO;
			if($DIFF_DEB != 0) $resumenSocio[0]['LiquidacionCuotaNoimputada']['importe_adebitar'] += $DIFF_DEB;
			
			$resumenSocio[0]['LiquidacionCuotaNoimputada']['importe_dto'] = round($resumenSocio[0]['LiquidacionCuotaNoimputada']['importe_dto'],2);
			$resumenSocio[0]['LiquidacionCuotaNoimputada']['importe_adebitar'] = round($resumenSocio[0]['LiquidacionCuotaNoimputada']['importe_adebitar'],2);
			
			
		endif; // EMPTY CUOTAS
		
//		debug($ACUMULA_IMPODTO ." ** ".$ACUMULA_IMPODEB);
		
		
//		debug($resumenSocio);
//		exit;
//		return $liquidacionCuota;
		return (isset($resumenSocio) ? $resumenSocio : null);

	}
	
	
	/**
	 * Arma Resumen CBU.
	 * Devuelve el resumen para la cabecera del socio si es CBU.
	 * Saca la informacion de acuerdo a:<br/>
	 * <li>Busca si tiene acuerdo_pago</li>
	 * <li>Busca lo del periodo</li>
	 * <li>Busca el atraso</li>
	 * <li>Analiza si el total es menor a $50 para enviar todo en un mismo registro</li>
	 * <li>Aplica el recupero de <b>deuda de Profinsa</b></li>
	 * <ul>
	 * 		<li>
	 * 			<b>CRITERIO 1:</b><br/>
	 * 			si es deuda unica de profinsa y/o sum(profinsa) >= sum(otras_deudas), 
	 * 			enviar deuda de profinsa de acuerdo: <br/> 
	 * 			deuda_total <= 50 --> 1 PAGO <br/> 
	 * 			51 <= deuda_total <= 100 --> 2 PAGOS <br/> 
	 * 			101 <= deuda_total --> 3 PAGOS<br/> 
	 * 		</li>
	 * 		
	 * 		<li>
	 * 			<b>CRITERIO 2:</b><br/>
	 * 			si la sum(deuda_profinsa)< que sum(otras_deudas) mandar el 100% de la deuda de profinsa. 
	 * 			Si el 33% de la deuda total es MAYOR al 100% de la deuda_profinsa 
	 * 			enviar a descuento el 33% (o sea el 100% de profinsa mas otras deudas)
	 * 		</li>
	 * </ul>
	 * @param $liquidacion_id
	 * @param $socio_id
	 * @param $periodo
	 */
	function __armaResumenCBU($liquidacion_id,$socio_id,$periodo,$organismo,$CONTROL_NACION=false,$BANCO_CONTROL=null){

		$resumen = array();
        
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBENEFICIO = new PersonaBeneficio();        
        
        #############################################################################
        # VERIFICO SI TIENE ACUERDO DE DEBITO Y EL TOTAL LIQUIDADO ES MENOR
        #############################################################################
        $beneficioAcuerdo = null;
        if(!$CONTROL_NACION){
            $SQL_ACUERDO = "select PersonaBeneficio.id from persona_beneficios as PersonaBeneficio
                            where acuerdo_debito > (select sum(saldo_actual)        
                            from liquidacion_cuota_noimputadas
                            where liquidacion_cuota_noimputadas.liquidacion_id = $liquidacion_id and 
                            liquidacion_cuota_noimputadas.socio_id = $socio_id and 
                            liquidacion_cuota_noimputadas.persona_beneficio_id = PersonaBeneficio.id)
                            group by PersonaBeneficio.id";
            $beneficioAcuerdo = $this->query($SQL_ACUERDO);            
        }else{
            $SQL_ACUERDO = "select PersonaBeneficio.id from persona_beneficios as PersonaBeneficio
                            where acuerdo_debito > (select sum(saldo_actual)        
                            from liquidacion_cuota_noimputadas
                            where liquidacion_cuota_noimputadas.liquidacion_id = $liquidacion_id and 
                            liquidacion_cuota_noimputadas.socio_id = $socio_id and 
                            liquidacion_cuota_noimputadas.persona_beneficio_id = PersonaBeneficio.id
                            and liquidacion_cuota_noimputadas.periodo_cuota = '$periodo')
                            and IFNULL(PersonaBeneficio.banco_id,'') = '$BANCO_CONTROL'    
                            group by PersonaBeneficio.id";
            $beneficioAcuerdo = $this->query($SQL_ACUERDO);              
        }

        if(!empty($beneficioAcuerdo)){
            foreach ($beneficioAcuerdo as $key => $beneficio) {
                $oBENEFICIO->resetAcuerdoPago($beneficio['PersonaBeneficio']['id']);
            }
        }
        
//        $CONTROL_BANCO_NACION = FALSE;
//        $BANCO_CONTROL = null;
//        $file = parse_ini_file(CONFIGS.'mutual.ini', true);
//        if(isset($file['general']['banco_nacion_debito_periodo']) && $file['general']['banco_nacion_debito_periodo'] != ""){
//            $CONTROL_BANCO_NACION = TRUE;
//            $BANCO_CONTROL = $file['general']['banco_nacion_debito_periodo'];
//        }        
        
    $sql_SNAC = "SELECT 
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					LiquidacionCuotaNoimputada.persona_beneficio_id,
					PersonaBeneficio.cbu,
					PersonaBeneficio.codigo_reparticion,
					PersonaBeneficio.turno_pago,
					PersonaBeneficio.porcentaje,
					sum(saldo_actual) as deuda,
					sum(saldo_actual) as importe_adebitar,
					'0' as codigo_dto,
					'0' as sub_codigo,
					1 as periodo,
					1 as registro,
					PersonaBeneficio.acuerdo_debito,
                    PersonaBeneficio.banco_id
				FROM 
					liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
					INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
				WHERE 
					LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
					AND SUBSTRING(codigo_organismo,9,2) = '".substr($organismo,8,2)."'
					AND LiquidacionCuotaNoimputada.socio_id = $socio_id
					AND PersonaBeneficio.acuerdo_debito <> 0
				GROUP BY
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					PersonaBeneficio.cbu,
					PersonaBeneficio.turno_pago		
				UNION
				SELECT 
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					LiquidacionCuotaNoimputada.persona_beneficio_id,
					PersonaBeneficio.cbu,
					PersonaBeneficio.codigo_reparticion,
					PersonaBeneficio.turno_pago,
					PersonaBeneficio.porcentaje,
					sum(saldo_actual) as deuda,
					sum(saldo_actual) as importe_adebitar,
					'0' as codigo_dto,
					'0' as sub_codigo,
					0 as periodo,
					LiquidacionCuotaNoimputada.registro,
					PersonaBeneficio.acuerdo_debito,
                    PersonaBeneficio.banco_id
				FROM 
					liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
					INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
				WHERE 
					LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
					AND SUBSTRING(codigo_organismo,9,2) = '".substr($organismo,8,2)."'
					AND LiquidacionCuotaNoimputada.socio_id = $socio_id
					AND LiquidacionCuotaNoimputada.periodo_cuota <> '$periodo'
					AND PersonaBeneficio.acuerdo_debito = 0
				GROUP BY
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					PersonaBeneficio.cbu,
					PersonaBeneficio.turno_pago	
				UNION	
				SELECT 
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					LiquidacionCuotaNoimputada.persona_beneficio_id,
					PersonaBeneficio.cbu,
					PersonaBeneficio.codigo_reparticion,
					PersonaBeneficio.turno_pago,
					PersonaBeneficio.porcentaje,
					sum(saldo_actual) as deuda,
					sum(saldo_actual) as importe_adebitar,
					'0' as codigo_dto,
					'0' as sub_codigo,
					1 as periodo,
					LiquidacionCuotaNoimputada.registro,
					PersonaBeneficio.acuerdo_debito,
                    PersonaBeneficio.banco_id
				FROM 
					liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
					INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
				WHERE 
					LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
					AND SUBSTRING(codigo_organismo,9,2) = '".substr($organismo,8,2)."'
					AND LiquidacionCuotaNoimputada.socio_id = $socio_id
					AND LiquidacionCuotaNoimputada.periodo_cuota = '$periodo'	
					AND PersonaBeneficio.acuerdo_debito = 0						
				GROUP BY
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					PersonaBeneficio.cbu,
					PersonaBeneficio.turno_pago	
				UNION
				SELECT 
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					LiquidacionCuotaNoimputada.persona_beneficio_id,
					PersonaBeneficioCompartido.cbu,
					PersonaBeneficioCompartido.codigo_reparticion,
					PersonaBeneficioCompartido.turno_pago,
					PersonaBeneficioCompartido.porcentaje,
					sum(saldo_actual) as deuda,
					sum(saldo_actual) as importe_adebitar,
					'0' as codigo_dto,
					'0' as sub_codigo,
					1 as periodo,
					1 as registro,
					PersonaBeneficio.acuerdo_debito,
                    PersonaBeneficio.banco_id
				FROM 
					liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
					INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
					INNER JOIN persona_beneficio_compartidos as PersonaBeneficioCompartido on (PersonaBeneficio.id = PersonaBeneficioCompartido.persona_beneficio_id)
				WHERE 
					LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
					AND SUBSTRING(codigo_organismo,9,2) = '".substr($organismo,8,2)."'
					AND LiquidacionCuotaNoimputada.socio_id = $socio_id
					AND PersonaBeneficio.acuerdo_debito <> 0
				GROUP BY
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					PersonaBeneficioCompartido.cbu,
					PersonaBeneficioCompartido.turno_pago		
				UNION
				SELECT 
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					LiquidacionCuotaNoimputada.persona_beneficio_id,
					PersonaBeneficioCompartido.cbu,
					PersonaBeneficioCompartido.codigo_reparticion,
					PersonaBeneficioCompartido.turno_pago,
					PersonaBeneficioCompartido.porcentaje,
					sum(saldo_actual) as deuda,
					sum(saldo_actual) as importe_adebitar,
					'0' as codigo_dto,
					'0' as sub_codigo,
					0 as periodo,
					LiquidacionCuotaNoimputada.registro,
					PersonaBeneficio.acuerdo_debito,
                    PersonaBeneficio.banco_id
				FROM 
					liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
					INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
					INNER JOIN persona_beneficio_compartidos as PersonaBeneficioCompartido on (PersonaBeneficio.id = PersonaBeneficioCompartido.persona_beneficio_id)
				WHERE 
					LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
					AND SUBSTRING(codigo_organismo,9,2) = '".substr($organismo,8,2)."'
					AND LiquidacionCuotaNoimputada.socio_id = $socio_id
					AND LiquidacionCuotaNoimputada.periodo_cuota <> '$periodo'
					AND PersonaBeneficio.acuerdo_debito = 0
				GROUP BY
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					PersonaBeneficioCompartido.cbu,
					PersonaBeneficioCompartido.turno_pago	
				UNION	
				SELECT 
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					LiquidacionCuotaNoimputada.persona_beneficio_id,
					PersonaBeneficioCompartido.cbu,
					PersonaBeneficioCompartido.codigo_reparticion,
					PersonaBeneficioCompartido.turno_pago,
					PersonaBeneficioCompartido.porcentaje,
					sum(saldo_actual) as deuda,
					sum(saldo_actual) as importe_adebitar,
					'0' as codigo_dto,
					'0' as sub_codigo,
					1 as periodo,
					LiquidacionCuotaNoimputada.registro,
					PersonaBeneficio.acuerdo_debito,
                    PersonaBeneficio.banco_id
				FROM 
					liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
					INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
					INNER JOIN persona_beneficio_compartidos as PersonaBeneficioCompartido on (PersonaBeneficio.id = PersonaBeneficioCompartido.persona_beneficio_id)
				WHERE 
					LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
					AND SUBSTRING(codigo_organismo,9,2) = '".substr($organismo,8,2)."'
					AND LiquidacionCuotaNoimputada.socio_id = $socio_id
					AND LiquidacionCuotaNoimputada.periodo_cuota = '$periodo'	
					AND PersonaBeneficio.acuerdo_debito = 0						
				GROUP BY
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					PersonaBeneficioCompartido.cbu,
					PersonaBeneficioCompartido.turno_pago
				ORDER BY 
					registro DESC;";		
		
        
       
        
        
//        $CTRL_BANCO = "00020";
        
		$sql_NAC = "SELECT 
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					LiquidacionCuotaNoimputada.persona_beneficio_id,
					PersonaBeneficio.cbu,
					PersonaBeneficio.codigo_reparticion,
					PersonaBeneficio.turno_pago,
					PersonaBeneficio.porcentaje,
					sum(saldo_actual) as deuda,
					sum(saldo_actual) as importe_adebitar,
					'0' as codigo_dto,
					'0' as sub_codigo,
					1 as periodo,
					1 as registro,
					PersonaBeneficio.acuerdo_debito,
                    PersonaBeneficio.banco_id
				FROM 
					liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
					INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
				WHERE 
					LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
					AND SUBSTRING(codigo_organismo,9,2) = '".substr($organismo,8,2)."'
					AND LiquidacionCuotaNoimputada.socio_id = $socio_id
					AND PersonaBeneficio.acuerdo_debito <> 0
                    AND LiquidacionCuotaNoimputada.periodo_cuota = '$periodo'
                    -- AND IFNULL(PersonaBeneficio.banco_id,'') <> '$BANCO_CONTROL'
				GROUP BY
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					PersonaBeneficio.cbu,
					PersonaBeneficio.turno_pago		
				UNION
				SELECT 
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					LiquidacionCuotaNoimputada.persona_beneficio_id,
					PersonaBeneficio.cbu,
					PersonaBeneficio.codigo_reparticion,
					PersonaBeneficio.turno_pago,
					PersonaBeneficio.porcentaje,
					sum(saldo_actual) as deuda,
					sum(saldo_actual) as importe_adebitar,
					'0' as codigo_dto,
					'0' as sub_codigo,
					0 as periodo,
					LiquidacionCuotaNoimputada.registro,
					PersonaBeneficio.acuerdo_debito,
                    PersonaBeneficio.banco_id
				FROM 
					liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
					INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
				WHERE 
					LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
					AND SUBSTRING(codigo_organismo,9,2) = '".substr($organismo,8,2)."'
					AND LiquidacionCuotaNoimputada.socio_id = $socio_id
					AND LiquidacionCuotaNoimputada.periodo_cuota <> '$periodo'
					AND PersonaBeneficio.acuerdo_debito = 0
                    AND IFNULL(PersonaBeneficio.banco_id,'') <> '$BANCO_CONTROL'
				GROUP BY
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					PersonaBeneficio.cbu,
					PersonaBeneficio.turno_pago	
				UNION	
				SELECT 
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					LiquidacionCuotaNoimputada.persona_beneficio_id,
					PersonaBeneficio.cbu,
					PersonaBeneficio.codigo_reparticion,
					PersonaBeneficio.turno_pago,
					PersonaBeneficio.porcentaje,
					sum(saldo_actual) as deuda,
					sum(saldo_actual) as importe_adebitar,
					'0' as codigo_dto,
					'0' as sub_codigo,
					1 as periodo,
					LiquidacionCuotaNoimputada.registro,
					PersonaBeneficio.acuerdo_debito,
                    PersonaBeneficio.banco_id
				FROM 
					liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
					INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
				WHERE 
					LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
					AND SUBSTRING(codigo_organismo,9,2) = '".substr($organismo,8,2)."'
					AND LiquidacionCuotaNoimputada.socio_id = $socio_id
					AND LiquidacionCuotaNoimputada.periodo_cuota = '$periodo'	
					AND PersonaBeneficio.acuerdo_debito = 0	
                    AND IFNULL(PersonaBeneficio.banco_id,'') <> '$BANCO_CONTROL'
				GROUP BY
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					PersonaBeneficio.cbu,
					PersonaBeneficio.turno_pago
				UNION
				SELECT 
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					LiquidacionCuotaNoimputada.persona_beneficio_id,
					PersonaBeneficio.cbu,
					PersonaBeneficio.codigo_reparticion,
					PersonaBeneficio.turno_pago,
					PersonaBeneficio.porcentaje,
					sum(saldo_actual) as deuda,
					sum(saldo_actual) as importe_adebitar,
					'0' as codigo_dto,
					'0' as sub_codigo,
					0 as periodo,
					LiquidacionCuotaNoimputada.registro,
					PersonaBeneficio.acuerdo_debito,
                    PersonaBeneficio.banco_id
				FROM 
					liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
					INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
				WHERE 
					LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
					AND SUBSTRING(codigo_organismo,9,2) = '".substr($organismo,8,2)."'
					AND LiquidacionCuotaNoimputada.socio_id = $socio_id
					AND LiquidacionCuotaNoimputada.periodo_cuota <> '$periodo'
					AND PersonaBeneficio.acuerdo_debito = 0
                    AND IFNULL(PersonaBeneficio.banco_id,'') = '$BANCO_CONTROL'
				GROUP BY
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					PersonaBeneficio.cbu,
					PersonaBeneficio.turno_pago,
                    LiquidacionCuotaNoimputada.orden_descuento_cuota_id
                    LIMIT 1
				UNION	
				SELECT 
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					LiquidacionCuotaNoimputada.persona_beneficio_id,
					PersonaBeneficio.cbu,
					PersonaBeneficio.codigo_reparticion,
					PersonaBeneficio.turno_pago,
					PersonaBeneficio.porcentaje,
					sum(saldo_actual) as deuda,
					sum(saldo_actual) as importe_adebitar,
					'0' as codigo_dto,
					'0' as sub_codigo,
					1 as periodo,
					LiquidacionCuotaNoimputada.registro,
					PersonaBeneficio.acuerdo_debito,
                    PersonaBeneficio.banco_id
				FROM 
					liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
					INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
				WHERE 
					LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
					AND SUBSTRING(codigo_organismo,9,2) = '".substr($organismo,8,2)."'
					AND LiquidacionCuotaNoimputada.socio_id = $socio_id
					AND LiquidacionCuotaNoimputada.periodo_cuota = '$periodo'	
					AND PersonaBeneficio.acuerdo_debito = 0	
                    AND IFNULL(PersonaBeneficio.banco_id,'') = '$BANCO_CONTROL'   
				GROUP BY
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					PersonaBeneficio.cbu,
					PersonaBeneficio.turno_pago				                    
				ORDER BY 
					registro DESC;";        
		
//		debug($sql);
//        debug($sql_SNAC);
		if($CONTROL_NACION)$cuotas = $this->query($sql_NAC);
        else $cuotas = $this->query($sql_SNAC);
        
//        debug($cuotas);

		$saldoAcuerdo = 0;
		$beneficioActual = 0;
//		exit;
		
		#######################################################################################
		# VERIFICO SI TIENE ACUERDO DE PAGO Y EL MONTO DEL ACUERDO ES MAYOR A LA DEUDA
		# LIQUIDADA
		#######################################################################################
//		App::import('Model','Pfyj.PersonaBeneficio');
//		$oBENEFICIO = new PersonaBeneficio();
//		
//		$cuotasAcuerdoDebitoReset = array();
//		
//		foreach($cuotas as $idx => $cuota):
//		
//			if($cuota[0]['acuerdo_debito'] != 0 && $cuota[0]['importe_adebitar'] <= $cuota[0]['acuerdo_debito']){
//				
//				$oBENEFICIO->resetAcuerdoPago($cuota[0]['persona_beneficio_id']);
//				//REGENERO LA CONSULTA PARA DETECTAR EL RESET DEL ACUERDO DE DEBITO
//				$sql = "SELECT 
//							LiquidacionCuotaNoimputada.codigo_organismo,
//							LiquidacionCuotaNoimputada.socio_id,
//							LiquidacionCuotaNoimputada.persona_beneficio_id,
//							sum(saldo_actual) as deuda,
//							sum(saldo_actual) as importe_adebitar,
//							'0' as codigo_dto,
//							'0' as sub_codigo,
//							1 as periodo,
//							1 as registro,
//							PersonaBeneficio.acuerdo_debito
//						FROM 
//							liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
//							INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
//						WHERE 
//							LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
//							AND SUBSTRING(codigo_organismo,9,2) = '".substr($organismo,8,2)."'
//							AND LiquidacionCuotaNoimputada.socio_id = $socio_id
//							AND PersonaBeneficio.acuerdo_debito <> 0
//						GROUP BY
//							LiquidacionCuotaNoimputada.codigo_organismo,
//							LiquidacionCuotaNoimputada.socio_id,
//							PersonaBeneficio.id					
//						UNION
//						SELECT 
//							LiquidacionCuotaNoimputada.codigo_organismo,
//							LiquidacionCuotaNoimputada.socio_id,
//							LiquidacionCuotaNoimputada.persona_beneficio_id,
//							sum(saldo_actual) as deuda,
//							sum(saldo_actual) as importe_adebitar,
//							'0' as codigo_dto,
//							'0' as sub_codigo,
//							0 as periodo,
//							LiquidacionCuotaNoimputada.registro,
//							PersonaBeneficio.acuerdo_debito
//						FROM 
//							liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
//							INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
//						WHERE 
//							LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
//							AND SUBSTRING(codigo_organismo,9,2) = '".substr($organismo,8,2)."'
//							AND LiquidacionCuotaNoimputada.socio_id = $socio_id
//							AND LiquidacionCuotaNoimputada.periodo_cuota <> '$periodo'
//							AND PersonaBeneficio.acuerdo_debito = 0
//						GROUP BY
//							LiquidacionCuotaNoimputada.codigo_organismo,
//							LiquidacionCuotaNoimputada.socio_id,
//							PersonaBeneficio.id				
//						UNION	
//						SELECT 
//							LiquidacionCuotaNoimputada.codigo_organismo,
//							LiquidacionCuotaNoimputada.socio_id,
//							LiquidacionCuotaNoimputada.persona_beneficio_id,
//							sum(saldo_actual) as deuda,
//							sum(saldo_actual) as importe_adebitar,
//							'0' as codigo_dto,
//							'0' as sub_codigo,
//							1 as periodo,
//							LiquidacionCuotaNoimputada.registro,
//							PersonaBeneficio.acuerdo_debito
//						FROM 
//							liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
//							INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
//						WHERE 
//							LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
//							AND SUBSTRING(codigo_organismo,9,2) = '".substr($organismo,8,2)."'
//							AND LiquidacionCuotaNoimputada.socio_id = $socio_id
//							AND LiquidacionCuotaNoimputada.periodo_cuota = '$periodo'	
//							AND PersonaBeneficio.acuerdo_debito = 0						
//						GROUP BY
//							LiquidacionCuotaNoimputada.codigo_organismo,
//							LiquidacionCuotaNoimputada.socio_id,
//							PersonaBeneficio.id						
//						ORDER BY 
//							registro DESC;";
//				$cuotas = $this->query($sql);
//				break;				
//			}
//		
//		endforeach;
		
//		debug($cuotas);
//		exit;
		
		##########################################################
		# APLICO EL CRITERIO DE RECUPERO DE DEUDA
		##########################################################
		/*
		 * CRITERIO 1:(desactivado)
						si es deuda unica de profinsa y/o sum(profinsa) >= sum(otras_deudas), enviar
						deuda de profinsa de acuerdo
						deuda_total <= 100 --> 1 PAGO
						101 <= deuda_total <= 200 --> 2 PAGOS
						201 <= deuda_total --> 3 PAGOS
		    CRITERIO 2:(desactivado)
						si la sum(deuda_profinsa)< que sum(otras_deudas) mandar el
						100% de la deuda de profinsa.
						Si el 33% de la deuda total es MAYOR al 100% de la deuda_profinsa
						enviar a descuento el 33% (o sea el 100% de profinsa mas otras deudas)
			CRITERIO 3:(criterio general de deuda)
						Si no tiene profinsa y tiene deuda
						deuda_total <= 100 --> 1 PAGO
						101 <= deuda_total <= 200 --> 2 PAGOS
						201 <= deuda_total --> 3 PAGOS
			CRITERIO 4:
						Comparo el resultado del criterio 2 / 3 con el ultimo descuento efectuado
						1) Si la mora al mes actual es MAYOR que la mora cobrada del periodo anterior:
							a debitar actual < debito anterior ---> envio el debito anterior
							a debitar actual > debito anterior ---> debito actual
						2) Si la mora del mes es MENOR a la mora cobrada el mes anterior:
							a debitar --> mora del mes	
									
			CRITERIO 5:
						Si el importe resultante de cualquiera de los 3 casos anteriores es
						superior al TOPE indicado en el campo decimal_2 de la global datos
						para el codigo MUTUCORG se carga este tope.									
									
		 * */
		$cuotas_FILTRO_2 = array();
		$proveedor_id = 20;
		$coeficiente = 0.33;
		
        #MODIFICAR LOS LIMITES (REUNION GUILLERMO, ADRIAN Y ALEXIS EL 03/06/14)
//      $limite_1 = 100;
//		$limite_2 = 200;
        
		$limite_1 = parent::GlobalDato('entero_1',$organismo);
        $limite_1 = (empty($limite_1) ? 100 : $limite_1);
        
		$limite_2 = parent::GlobalDato('entero_2',$organismo);
        $limite_2 = (empty($limite_2) ? 200 : $limite_2);
		
		$topeCriterio5 = parent::GlobalDato('decimal_2',$organismo);
		
		App::import("Model",'Proveedores.Proveedor');
		$oPROVEEDOR = new Proveedor();
		$proveedorRazonSocial = $oPROVEEDOR->getRazonSocialResumida($proveedor_id);
		$proveedorRazonSocial = trim($proveedorRazonSocial);
//		debug($cuotas);
//		exit;
		
		$IMPORTE_LIQUIDADO_SOCIO = 0;
        

        
		
		foreach($cuotas as $cuota){
			
            $APLICAR = (!$CONTROL_NACION || $cuota[0]['banco_id'] != '00011' ? true : false);
            
            $cuota[0]['formula_criterio_deuda'] = "";
            
			if(!empty($cuota[0]['socio_id']) && $APLICAR):
				
				$importe_1 = 0;
				$importe_2 = 0;
				$importe_3 = 0;
				$importe_aplicado = 0;
				
				if($cuota[0]['periodo'] == 0){	
					
					$importe_3 = $this->__getImporteDeudaLiquidadaBySocioByPeriodoByCriterio($liquidacion_id,$socio_id,$periodo,"1=1");

                    #######################################################################################################
					#CRITERIO 1
					#######################################################################################################
					$cuota[0]['criterio_deuda'] = 1;
					
					$cuota[0]['formula_criterio_deuda'] = "*** FILTRO #1 ***\n";
					$cuota[0]['formula_criterio_deuda'] .= "ATRASO TOTAL: $importe_3\n";
					
					if($limite_1 >= $importe_3){
						$importe_aplicado = round($importe_3,2);
						$cuota[0]['formula_criterio_deuda'] .= "$importe_3 < $limite_1 ==> IMPORTE A DEBITAR: $importe_aplicado ( TOTAL ATRASO $importe_3)\n";
					}
												
					if(($limite_1 < $importe_3) && ($importe_3 <= $limite_2)){
						$importe_aplicado = $importe_3 / 2;
						$importe_aplicado = round($importe_aplicado,2);
						$cuota[0]['formula_criterio_deuda'] .= "$limite_1 < $importe_3 < $limite_2 ==> IMPORTE A DEBITAR: $importe_aplicado ( TOTAL ATRASO ($importe_3) / 2)\n";
					}
					
					if($limite_2 < $importe_3){
						$importe_aplicado = $importe_3 / 3;
						$importe_aplicado = round($importe_aplicado,2);
						$cuota[0]['formula_criterio_deuda'] .= "$limite_2 < $importe_3 ==> IMPORTE A DEBITAR: $importe_aplicado ( TOTAL ATRASO ($importe_3) / 3)\n";
					}
					
					$cuota[0]['importe_adebitar'] = $importe_aplicado;
					$cuota[0]['deuda'] = $importe_aplicado;						
					

					#######################################################################################################
					#CRITERIO 3 (EX 5)
					#######################################################################################################
					if($cuota[0]['importe_adebitar'] > $topeCriterio5 && $topeCriterio5 != 0){
						
						$cuota[0]['formula_criterio_deuda'] .= "*** FILTRO #3 ***\n";
						$cuota[0]['formula_criterio_deuda'] .= "CONTROL MONTO MAXIMO ESTABLECIDO ($topeCriterio5)\n";
						$cuota[0]['formula_criterio_deuda'] .= "" . $cuota[0]['importe_adebitar'] ." > $topeCriterio5 ==> IMPORTE A DEBITAR: $topeCriterio5\n";
						
						$cuota[0]['importe_adebitar'] = $topeCriterio5;
						$cuota[0]['deuda'] = $topeCriterio5;
						$cuota[0]['criterio_deuda'] = 5;
					}
					
					
//					debug($cuota);
						
				}
				

				
				
				
			endif;
            array_push($cuotas_FILTRO_2,$cuota);
		}
//		debug($cuotas_FILTRO_2);
//		exit;	
//        $resumen = $cuotas_FILTRO_2;
        
        #################################################################################
        # CONTROL BANCO NACION
        # NO ENVIAR LA DEUDA SOLO EL PERIODO
        # SI NO TIENE PERIODO, ENVIAR LA DEUDA FRACCIONADA POR PERIODO
        ################################################################################# 
        if($CONTROL_NACION){
            $impoCuotaSocial = parent::GlobalDato('decimal_1','MUTUCUOS' . substr($organismo,8,4));
//            debug($impoCuotaSocial);
//            debug($resumen);
            $cuotas_FILTRO_NAC = array();
            $PERIODO_BCONAC = false;
            foreach($cuotas_FILTRO_2 as $idx => $cuota){
                if(isset($cuota[0]['banco_id']) && $cuota[0]['banco_id'] == $BANCO_CONTROL){
                     if($cuota[0]['periodo'] == 1){
                         array_push($cuotas_FILTRO_NAC,$cuota);
//                         $PERIODO_BCONAC = true;
                         if(round($cuota[0]['importe_adebitar'],2) != round($impoCuotaSocial,2)) $PERIODO_BCONAC = true;
                         else $PERIODO_BCONAC = false;
                     }
                }else{
                    array_push($cuotas_FILTRO_NAC,$cuota);
                }
            }

            foreach($cuotas_FILTRO_2 as $idx => $cuota){
                if(isset($cuota[0]['banco_id']) && $cuota[0]['banco_id'] == $BANCO_CONTROL){
                     if($cuota[0]['periodo'] == 0 && !$PERIODO_BCONAC){
                         array_push($cuotas_FILTRO_NAC,$cuota);
                     }
                }
            }

//            $resumen = $cuotas_FILTRO_6;
//            debug($cuotas_FILTRO_NAC);
        }else{
            $cuotas_FILTRO_NAC = $cuotas_FILTRO_2;
        }        
        

		##########################################################
		# RECORRO LAS CUOTAS PARA DETECTAR ACUERDO DE PAGO
		# GENERO EL NUMERO DE REGISTRO
		##########################################################
		$registro = 1;
		$cuotas_FILTRO_3 = array();

		foreach($cuotas_FILTRO_NAC as $cuota){
			
			if(!empty($cuota[0]['socio_id'])):
			
				if($cuota[0]['acuerdo_debito'] != 0 && $cuota[0]['importe_adebitar'] > $cuota[0]['acuerdo_debito']){
					$cuota[0]['importe_adebitar'] = $cuota[0]['acuerdo_debito'];
				}
				$cuota[0]['registro'] = $registro;
				array_push($cuotas_FILTRO_3,$cuota);
				$registro++;
				
			endif;
			
		}
		
//		debug($cuotas_FILTRO_3);
//		exit;

		
		#####################################################################################
		# SI UNO DE LOS REGISTROS ES MENOR A 50 ENVIAR TODO EN UN SOLO REGISTRO
		#####################################################################################
		$cuotas_FILTRO_4 = array();
//		$minimo = 50;
		
		$result = Set::sort($cuotas_FILTRO_3, '{n}.{n}.importe_adebitar', 'asc');

		if(!empty($result) && count($result) == 2){

			if($result[0][0]['importe_adebitar'] < $this->impoMinDtoCBU){
				
				$result[1][0]['formula_criterio_deuda'] .= " *** UNIFICACION DE REGISTROS (IMPORTES MENORES A ".$this->impoMinDtoCBU.") ***\n";
				$result[1][0]['formula_criterio_deuda'] .= (isset($result[1][0]['importe_adebitar']) ? $result[1][0]['importe_adebitar'] . " + " : "") . $result[0][0]['importe_adebitar'];
				$result[1][0]['importe_adebitar'] += $result[0][0]['importe_adebitar'];
				$result[1][0]['formula_criterio_deuda'] .= " ==> IMPORTE A DEBITAR = " . number_format($result[1][0]['importe_adebitar'],2). "\n";
				
				$cuotas_FILTRO_4[0][0] = $result[1][0];
			
			}else{

				$cuotas_FILTRO_4 = $cuotas_FILTRO_3;
			
			}
		
		}else{
			
			$cuotas_FILTRO_4 = $cuotas_FILTRO_3;
			
		}
		
//		debug($cuotas_FILTRO_4);
//		exit;		
		

		########################################################################################
		#RECORRO PARA DETECTAR REGISTROS CON IMPORTE MAYOR AL TOPE PARA
		#DIVIDIRLO EN SUB-REGISTROS
		########################################################################################
		$tope = parent::GlobalDato('decimal_1',$organismo);
		$n = 0;
		$saldo = 0;
		$subRegistros = array();
		$tmp = array();
		foreach($cuotas_FILTRO_4 as $cuota):
		
			$subRegistros = array();
//			$importeCorte --> ver mas arriba == 50
			/**
			 * Si la diferencia al maximo de 1999 es menor a 50 enviar en un solo registro
			 * GABRIEL 14/09/2011
			 */
			$excedente = abs($cuota[0]['importe_adebitar'] - $tope);
			
			if($cuota[0]['importe_adebitar'] > $tope && $excedente > $this->impoMinDtoCBU):
			
				$n = floor($cuota[0]['importe_adebitar'] / $tope);
				$resto = $cuota[0]['importe_adebitar'] % $tope;
				
				for($i=0;$i<=$n;$i++):
				
					$registro = $cuota[0]['registro'] + $n - $i;
					
					if($i < $n):
					
						$tmp[0] = array(
							'codigo_organismo' => $cuota[0]['codigo_organismo'],
							'socio_id' => $cuota[0]['socio_id'],
							'persona_beneficio_id' => $cuota[0]['persona_beneficio_id'],
							'deuda' => $tope,
							'importe_adebitar' => $tope,
							'codigo_dto' => $cuota[0]['codigo_dto'],
							'sub_codigo' => $cuota[0]['sub_codigo'],
							'periodo' => $cuota[0]['periodo'],						
							'registro' => $registro,
							'acuerdo_debito' => $cuota[0]['acuerdo_debito'],
							'criterio_deuda' => (isset($cuota[0]['criterio_deuda']) ? $cuota[0]['criterio_deuda'] : 0),	
							'formula_criterio_deuda' => $cuota[0]['formula_criterio_deuda'],
                            'banco_id' => $cuota[0]['banco_id'] 
						);
						
					else:
					
						$tmp[0] = array(
							'codigo_organismo' => $cuota[0]['codigo_organismo'],
							'socio_id' => $cuota[0]['socio_id'],
							'persona_beneficio_id' => $cuota[0]['persona_beneficio_id'],
							'deuda' => $cuota[0]['importe_adebitar'] - ($tope * $n),
							'importe_adebitar' => $cuota[0]['importe_adebitar'] - ($tope * $n),
							'codigo_dto' => $cuota[0]['codigo_dto'],
							'sub_codigo' => $cuota[0]['sub_codigo'],
							'periodo' => $cuota[0]['periodo'],						
							'registro' => $registro,
							'acuerdo_debito' => $cuota[0]['acuerdo_debito'],
							'criterio_deuda' => (isset($cuota[0]['criterio_deuda']) ? $cuota[0]['criterio_deuda'] : 0),
							'formula_criterio_deuda' => $cuota[0]['formula_criterio_deuda'],
                            'banco_id' => $cuota[0]['banco_id'] 
						);
						
						
					endif;
//					debug($tmp);
					array_push($resumen,$tmp);
				
				endfor;
				
			else:
				
				array_push($resumen,$cuota);
			
			endif;
			
		endforeach;
		
//		debug($resumen);
		
		#######################################################################################################
		# CONTROL PARTICULAR DE EL MAXIMO IMPORTE POR REGISTRO DE DEBITO (SUBDIVISION)
		#######################################################################################################
		$cuotas_FILTRO_5 = array();

		foreach($resumen as $idx => $cuota){
			
			$impoDebito = $cuota[0]['importe_adebitar'];
			$impoDebitoResta = $impoDebito;
			
			$minBcoNacion = $oBENEFICIO->getImpoMaxRegistroCBU($cuota[0]['persona_beneficio_id']);
			
			if( $impoDebito > ($minBcoNacion + $this->impoMinDtoCBU) && $minBcoNacion != 0){
				
				$n = floor($impoDebito / ($minBcoNacion + $this->impoMinDtoCBU));
				$tmp = array();
				$tmp = $cuota;
				
				for($i=0; $i <= $n; $i++):
					
					if($i < $n):
					
						$tmp[0]['importe_adebitar'] = $minBcoNacion;
						$tmp[0]['deuda'] = $tmp[0]['importe_adebitar'];
						
					else:
					
						$tmp[0]['importe_adebitar'] = $cuota[0]['importe_adebitar'] - ($minBcoNacion * $n);
						$tmp[0]['deuda'] = $tmp[0]['importe_adebitar'];
					
					endif;
					
					array_push($cuotas_FILTRO_5,$tmp);
				
				endfor;
				
			}else{
				
				array_push($cuotas_FILTRO_5,$cuota);
				
			}
			
		}
		
//		debug($cuotas_FILTRO_5);
		
		$resumen = $cuotas_FILTRO_5;
		
		#######################################################################################################
		# CONTROL DE MINIMO
		#######################################################################################################
		$min = $this->impoMinDtoCBU * 10;
		foreach($resumen as $idx => $cuota){
			if($cuota[0]['importe_adebitar'] < $min){
                if(($idx - 1) < 0) break; 
				if(round(floatval(($resumen[$idx - 1][0]['importe_adebitar'] - $min )),2) > $min){
					$resumen[$idx - 1][0]['importe_adebitar'] -= $min;
					$resumen[$idx][0]['importe_adebitar'] += $min;
				}
			}
		}		
		

//        debug($resumen);

        
        
//        $file = parse_ini_file(CONFIGS.'mutual.ini', true);
//        if(isset($file['general']['banco_nacion_debito_periodo']) && $file['general']['banco_nacion_debito_periodo'] == 1){
//            
//            $cuotas_FILTRO_6 = array();
//            foreach($resumen as $idx => $cuota){
//                if(isset($cuota[0]['banco_id']) && $cuota[0]['banco_id'] == '00011'){
//                    if($cuota[0]['periodo'] == 1) array_push($cuotas_FILTRO_6,$cuota);
//                }else{
//                    array_push($cuotas_FILTRO_6,$cuota);
//                }
//            }
//            $resumen = $cuotas_FILTRO_6;            
//        }

//		debug($cuotas_FILTRO_6);

		#######################################################################################################
		# 	RENUMERAR LOS REGISTROS
		#######################################################################################################
		$r = 1;
		foreach($resumen as $idx => $cuota){
			$resumen[$idx][0]['registro'] = $r;
			$r++;
		}
//        debug($resumen);
        
        
		return $resumen;
		
	}
        
        
        
        
	/**
	 * Arma Resumen CBU.
	 * Devuelve el resumen para la cabecera del socio si es CBU.
	 * Saca la informacion de acuerdo a:<br/>
	 * <li>Busca si tiene acuerdo_pago</li>
	 * <li>Busca lo del periodo</li>
	 * <li>Busca el atraso</li>
	 * <li>Analiza si el total es menor a $50 para enviar todo en un mismo registro</li>
	 * <li>Aplica el recupero de <b>deuda de Profinsa</b></li>
	 * <ul>
	 * 		<li>
	 * 			<b>CRITERIO 1:</b><br/>
	 * 			si es deuda unica de profinsa y/o sum(profinsa) >= sum(otras_deudas), 
	 * 			enviar deuda de profinsa de acuerdo: <br/> 
	 * 			deuda_total <= 50 --> 1 PAGO <br/> 
	 * 			51 <= deuda_total <= 100 --> 2 PAGOS <br/> 
	 * 			101 <= deuda_total --> 3 PAGOS<br/> 
	 * 		</li>
	 * 		
	 * 		<li>
	 * 			<b>CRITERIO 2:</b><br/>
	 * 			si la sum(deuda_profinsa)< que sum(otras_deudas) mandar el 100% de la deuda de profinsa. 
	 * 			Si el 33% de la deuda total es MAYOR al 100% de la deuda_profinsa 
	 * 			enviar a descuento el 33% (o sea el 100% de profinsa mas otras deudas)
	 * 		</li>
	 * </ul>
	 * @param $liquidacion_id
	 * @param $socio_id
	 * @param $periodo
         * @param boolean $CONTROL_NACION
         * @param string $BANCO_CONTROL
         * @param boolean $DISCRIMINA_CS (indica si la cuota social del periodo se pide como un registro unico)
	 */
	function __armaResumenCBU_NUEVO($liquidacion_id,$socio_id,$periodo,$organismo,$CONTROL_NACION=false,$BANCO_CONTROL=null,$DISCRIMINA_PERMANENTES = FALSE){

            $resumen = array();

            App::import('Model','Pfyj.PersonaBeneficio');
            $oBENEFICIO = new PersonaBeneficio();        

            #############################################################################
            # VERIFICO SI TIENE ACUERDO DE DEBITO Y EL TOTAL LIQUIDADO ES MENOR
            #############################################################################
            $beneficioAcuerdo = null;
            
//            $SQL_ACUERDO = "select PersonaBeneficio.id from persona_beneficios as PersonaBeneficio
//                            where acuerdo_debito > (select IFNULL(sum(saldo_actual),0)        
//                            from liquidacion_cuota_noimputadas
//                            where liquidacion_cuota_noimputadas.liquidacion_id = $liquidacion_id and 
//                            liquidacion_cuota_noimputadas.socio_id = $socio_id and 
//                            liquidacion_cuota_noimputadas.persona_beneficio_id = PersonaBeneficio.id
//                            ".(!$CONTROL_NACION ? ")" : " and liquidacion_cuota_noimputadas.periodo_cuota = '$periodo') and IFNULL(PersonaBeneficio.banco_id,'') = '$BANCO_CONTROL' ")."
//                            group by PersonaBeneficio.id";    
            
            $SQL_ACUERDO = "select PersonaBeneficio.id from persona_beneficios as PersonaBeneficio
                            inner join personas Persona on Persona.id = PersonaBeneficio.persona_id
                            inner join socios Socio on Socio.persona_id = Persona.id
                            where Socio.id = $socio_id
                            and PersonaBeneficio.codigo_beneficio = '$organismo'
                            and PersonaBeneficio.acuerdo_debito > ifnull((select sum(saldo_actual)        
                            from liquidacion_cuota_noimputadas
                            where liquidacion_cuota_noimputadas.liquidacion_id = $liquidacion_id and 
                            liquidacion_cuota_noimputadas.socio_id = $socio_id and 
                            liquidacion_cuota_noimputadas.persona_beneficio_id = PersonaBeneficio.id
                            ".(!$CONTROL_NACION ? " " : " and liquidacion_cuota_noimputadas.periodo_cuota = '$periodo' and IFNULL(PersonaBeneficio.banco_id,'') = '$BANCO_CONTROL' ")."
                            ),0)
                            group by PersonaBeneficio.id;";
            $beneficioAcuerdo = $this->query($SQL_ACUERDO);
            
            if(!empty($beneficioAcuerdo)){
                foreach ($beneficioAcuerdo as $key => $beneficio) {
                    $oBENEFICIO->auditable = FALSE;
                    $oBENEFICIO->resetAcuerdoPago($beneficio['PersonaBeneficio']['id']);
                }
            }
        
           
            $sql = "SELECT 
                            LiquidacionCuotaNoimputada.codigo_organismo
                            ,LiquidacionCuotaNoimputada.socio_id
                            ,LiquidacionCuotaNoimputada.persona_beneficio_id
                            ,PersonaBeneficio.cbu
                            ,PersonaBeneficio.codigo_reparticion
                            ,PersonaBeneficio.turno_pago
                            ,PersonaBeneficio.porcentaje
                            ,sum(saldo_actual) as deuda
                            ,PersonaBeneficio.acuerdo_debito as importe_adebitar
                            , 0 as criterio_deuda
                            ,'0' as codigo_dto
                            ,'0' as sub_codigo
                            ,1 as periodo
                            ,1 as registro
                            ,PersonaBeneficio.acuerdo_debito
                            ,PersonaBeneficio.importe_max_registro_cbu
                            ,PersonaBeneficio.banco_id
                            ,0 as ultimo_debito
                            ,Organismo.entero_1
                            ,Organismo.entero_2 
                            ,Organismo.decimal_2
                            ,concat('*** ACUERDO DE DEBITO (',PersonaBeneficio.acuerdo_debito,') ***\n') as formula_criterio_deuda
                    FROM 
                            liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
                            INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
                            INNER JOIN global_datos Organismo on Organismo.id = PersonaBeneficio.codigo_beneficio
                    WHERE 
                            LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
                            AND SUBSTRING(codigo_organismo,9,2) = '".substr($organismo,8,2)."'
                            AND LiquidacionCuotaNoimputada.socio_id = $socio_id
                            AND PersonaBeneficio.acuerdo_debito <> 0
                            ".($CONTROL_NACION ? " AND LiquidacionCuotaNoimputada.periodo_cuota = '$periodo' " : "")."
                    GROUP BY
                            LiquidacionCuotaNoimputada.codigo_organismo,
                            LiquidacionCuotaNoimputada.socio_id,
                            PersonaBeneficio.cbu,
                            PersonaBeneficio.turno_pago		
                    UNION
                    SELECT 
                            LiquidacionCuotaNoimputada.codigo_organismo
                            ,LiquidacionCuotaNoimputada.socio_id
                            ,LiquidacionCuotaNoimputada.persona_beneficio_id
                            ,PersonaBeneficio.cbu
                            ,PersonaBeneficio.codigo_reparticion
                            ,PersonaBeneficio.turno_pago
                            ,PersonaBeneficio.porcentaje
                            ,sum(saldo_actual) as deuda
                            ,(select case 
                            when sum(saldo_actual) <= (select ifnull(sum(lc.importe_debitado),0) from liquidacion_cuota_noimputadas lc
                                    inner join liquidaciones l on l.id = lc.liquidacion_id
                                    where l.id < LiquidacionCuotaNoimputada.liquidacion_id 
                            and lc.socio_id = LiquidacionCuotaNoimputada.socio_id 
                            and ifnull(lc.importe_debitado,0) > 0 group by l.id order by l.id desc limit 1) then sum(saldo_actual)
                            when sum(saldo_actual) <= Organismo.entero_1 then sum(saldo_actual)
                            when sum(saldo_actual) between Organismo.entero_1 and Organismo.entero_2 then sum(saldo_actual) / 2
                            when sum(saldo_actual) >= Organismo.entero_2 then sum(saldo_actual) / 3
                            end) as importe_adebitar
                            ,(select case 
                            when sum(saldo_actual) <= (select ifnull(sum(lc.importe_debitado),0) from liquidacion_cuota_noimputadas lc
                                    inner join liquidaciones l on l.id = lc.liquidacion_id
                                    where l.id < LiquidacionCuotaNoimputada.liquidacion_id 
                            and lc.socio_id = LiquidacionCuotaNoimputada.socio_id 
                            and ifnull(lc.importe_debitado,0) > 0
                                    group by l.id order by l.id desc limit 1) then 1
                            when sum(saldo_actual) <= Organismo.entero_1 then 2
                            when sum(saldo_actual) between Organismo.entero_1 and Organismo.entero_2 then 3
                            when sum(saldo_actual) >= Organismo.entero_2 then 4
                            end) as criterio_deuda
                            ,'0' as codigo_dto
                            ,'0' as sub_codigo
                            ,0 as periodo
                            ,LiquidacionCuotaNoimputada.registro
                            ,PersonaBeneficio.acuerdo_debito
                            ,PersonaBeneficio.importe_max_registro_cbu
                            ,PersonaBeneficio.banco_id
                            ,(select ifnull(sum(lc.importe_debitado),0) from liquidacion_cuota_noimputadas lc
                                inner join liquidaciones l on l.id = lc.liquidacion_id
                                where l.id < LiquidacionCuotaNoimputada.liquidacion_id 
                                and lc.socio_id = LiquidacionCuotaNoimputada.socio_id 
                                and ifnull(lc.importe_debitado,0) > 0
                                group by l.id order by l.id desc limit 1) as ultimo_debito
                            ,Organismo.entero_1
                            ,Organismo.entero_2 
                            ,Organismo.decimal_2    
                            ,'' as formula_criterio_deuda
                    FROM 
                            liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
                            INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
                            INNER JOIN global_datos Organismo on Organismo.id = PersonaBeneficio.codigo_beneficio
                    WHERE 
                            LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
                            AND SUBSTRING(codigo_organismo,9,2) = '".substr($organismo,8,2)."'
                            AND LiquidacionCuotaNoimputada.socio_id = $socio_id
                            AND LiquidacionCuotaNoimputada.periodo_cuota <> '$periodo'
                            AND PersonaBeneficio.acuerdo_debito = 0
                            ".($CONTROL_NACION ? " AND IFNULL(PersonaBeneficio.banco_id,'') <> '$BANCO_CONTROL' " : "")."
                    GROUP BY
                            LiquidacionCuotaNoimputada.codigo_organismo,
                            LiquidacionCuotaNoimputada.socio_id,
                            PersonaBeneficio.cbu,
                            PersonaBeneficio.turno_pago	
                    UNION	
                    SELECT 
                            LiquidacionCuotaNoimputada.codigo_organismo
                            ,LiquidacionCuotaNoimputada.socio_id
                            ,LiquidacionCuotaNoimputada.persona_beneficio_id
                            ,PersonaBeneficio.cbu
                            ,PersonaBeneficio.codigo_reparticion
                            ,PersonaBeneficio.turno_pago
                            ,PersonaBeneficio.porcentaje
                            ,sum(saldo_actual) as deuda
                            ,sum(saldo_actual) as importe_adebitar
                            , 0 as criterio_deuda
                            ,'0' as codigo_dto
                            ,'0' as sub_codigo
                            ,1 as periodo
                            ,LiquidacionCuotaNoimputada.registro
                            ,PersonaBeneficio.acuerdo_debito
                            ,PersonaBeneficio.importe_max_registro_cbu
                            ,PersonaBeneficio.banco_id
                            ,0 as ultimo_debito
                            ,Organismo.entero_1
                            ,Organismo.entero_2 
                            ,Organismo.decimal_2 
                            ,'' as formula_criterio_deuda
                    FROM 
                            liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
                            INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
                            INNER JOIN global_datos Organismo on Organismo.id = PersonaBeneficio.codigo_beneficio
                            INNER JOIN orden_descuentos OrdenDescuento on OrdenDescuento.id = LiquidacionCuotaNoimputada.orden_descuento_id
                    WHERE 
                            LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
                            AND SUBSTRING(codigo_organismo,9,2) = '".substr($organismo,8,2)."'
                            AND LiquidacionCuotaNoimputada.socio_id = $socio_id
                            AND LiquidacionCuotaNoimputada.periodo_cuota = '$periodo'	
                            AND PersonaBeneficio.acuerdo_debito = 0
                            ".($CONTROL_NACION ? " AND IFNULL(PersonaBeneficio.banco_id,'') <> '$BANCO_CONTROL' " : "")."
                            ".($DISCRIMINA_PERMANENTES ? " AND OrdenDescuento.permanente = 1" : "")."  
                            -- AND 1 = 2     
                    GROUP BY
                            LiquidacionCuotaNoimputada.codigo_organismo,
                            ".($DISCRIMINA_PERMANENTES ? " OrdenDescuento.id, " : " LiquidacionCuotaNoimputada.socio_id, ")."
                            PersonaBeneficio.cbu,
                            PersonaBeneficio.turno_pago "
                            .(!$DISCRIMINA_PERMANENTES ? " " : " UNION 
                                    SELECT 
                                            LiquidacionCuotaNoimputada.codigo_organismo
                                            ,LiquidacionCuotaNoimputada.socio_id
                                            ,LiquidacionCuotaNoimputada.persona_beneficio_id
                                            ,PersonaBeneficio.cbu
                                            ,PersonaBeneficio.codigo_reparticion
                                            ,PersonaBeneficio.turno_pago
                                            ,PersonaBeneficio.porcentaje
                                            ,sum(saldo_actual) as deuda
                                            ,sum(saldo_actual) as importe_adebitar
                                            , 0 as criterio_deuda
                                            ,'0' as codigo_dto
                                            ,'0' as sub_codigo
                                            ,1 as periodo
                                            ,LiquidacionCuotaNoimputada.registro
                                            ,PersonaBeneficio.acuerdo_debito
                                            ,PersonaBeneficio.importe_max_registro_cbu
                                            ,PersonaBeneficio.banco_id
                                            ,0 as ultimo_debito
                                            ,Organismo.entero_1
                                            ,Organismo.entero_2 
                                            ,Organismo.decimal_2   
                                            ,'' as formula_criterio_deuda
                                    FROM 
                                            liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
                                            INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
                                            INNER JOIN global_datos Organismo on Organismo.id = PersonaBeneficio.codigo_beneficio
                                            INNER JOIN orden_descuentos OrdenDescuento on OrdenDescuento.id = LiquidacionCuotaNoimputada.orden_descuento_id
                                    WHERE 
                                            LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
                                            AND SUBSTRING(codigo_organismo,9,2) = '".substr($organismo,8,2)."'
                                            AND LiquidacionCuotaNoimputada.socio_id = $socio_id
                                            AND LiquidacionCuotaNoimputada.periodo_cuota = '$periodo'	
                                            AND PersonaBeneficio.acuerdo_debito = 0
                                            ".($CONTROL_NACION ? " AND IFNULL(PersonaBeneficio.banco_id,'') <> '$BANCO_CONTROL' " : "")."
                                            ".($DISCRIMINA_PERMANENTES ? " AND OrdenDescuento.permanente = 0" : "")."    
                                    GROUP BY
                                            LiquidacionCuotaNoimputada.codigo_organismo,
                                            LiquidacionCuotaNoimputada.socio_id,
                                            PersonaBeneficio.cbu,
                                            PersonaBeneficio.turno_pago ") . " ORDER BY registro DESC;";            
            
        $cuotas = $this->query($sql);    	
        $cuotas_FILTRO_2 = array();
	$IMPORTE_LIQUIDADO_SOCIO = 0;
//        debug($cuotas);
	
        foreach($cuotas as $cuota){
			
            $APLICAR = (!$CONTROL_NACION || $cuota[0]['banco_id'] != '00011' ? true : false);
                       
            if(!empty($cuota[0]['socio_id']) && $APLICAR):

                $importe_1 = 0;
                $importe_2 = 0;
                $importe_3 = 0;
                $importe_aplicado = 0;

                if($cuota[0]['periodo'] == 0){	
                    
                    $cuota[0]['deuda'] = round($cuota[0]['deuda'],2);
                    $cuota[0]['importe_adebitar'] = round($cuota[0]['importe_adebitar'],2);
                    $cuota[0]['ultimo_debito'] = round($cuota[0]['ultimo_debito'],2);
                    $cuota[0]['entero_1'] = round($cuota[0]['entero_1'],2);
                    $cuota[0]['entero_2'] = round($cuota[0]['entero_2'],2);
                    $cuota[0]['decimal_2'] = round($cuota[0]['decimal_2'],2);
                    
                    #TOTAL ADEUDADO MENOR QUE EL ULTIMO DEBITO -> ENVIO EL TOTAL ADEUDADO
                    if($cuota[0]['criterio_deuda'] == 1){
                        $cuota[0]['formula_criterio_deuda'] .= "*** FILTRO #1 ***\n";
                        $cuota[0]['formula_criterio_deuda'] .= "ULTIMO DEBITO COBRADO > DEUDA (".$cuota[0]['ultimo_debito']." > ".$cuota[0]['deuda'].")\n";                        
                        $cuota[0]['formula_criterio_deuda'] .= "IMPORTE A DEBITAR = ".$cuota[0]['deuda']."\n";
                    }else if($cuota[0]['criterio_deuda'] == 2){
                        #TOTAL ADEUDADO < LIMITE 1 ENVIO EL TOTAL DEUDA
                        $cuota[0]['formula_criterio_deuda'] .= "*** FILTRO #2 (%1) ***\n";
                        $cuota[0]['formula_criterio_deuda'] .= "DEUDA < ".$cuota[0]['entero_1']."\n";
                        $cuota[0]['formula_criterio_deuda'] .= "IMPORTE A DEBITAR = ".$cuota[0]['deuda']."\n";
                    }else if($cuota[0]['criterio_deuda'] == 3){
                        #DEUDA ESTA ENTRE LIMITE 1 Y LIMITE 2, ENVIO LA DEUDA / 2
                        $cuota[0]['formula_criterio_deuda'] .= "*** FILTRO #3 (%2)***\n";
                        $cuota[0]['formula_criterio_deuda'] .= $cuota[0]['entero_1'] ." < DEUDA < ". $cuota[0]['entero_2']."\n";
                        $cuota[0]['formula_criterio_deuda'] .= "IMPORTE A DEBITAR = ".round($cuota[0]['deuda']/ 2 ,2)."\n";
                    }else if($cuota[0]['criterio_deuda'] == 4){
                        #DEUDA MAYOR LIMITE 2 ENVIO DEUDA / 3
                        $cuota[0]['formula_criterio_deuda'] .= "*** FILTRO #4 (%3) ***\n";
                        $cuota[0]['formula_criterio_deuda'] .= "DEUDA > ".$cuota[0]['entero_2']."\n";
                        $cuota[0]['formula_criterio_deuda'] .= "IMPORTE A DEBITAR = ".round($cuota[0]['deuda']/ 3 ,2)."\n";
                    }
                    #DEUDA CALCULADA ANTERIOR ES MAYOR AL TOPE MAXIMO ENVIO EL TOPE
                    if($cuota[0]['importe_adebitar'] > $cuota[0]['decimal_2'] && $cuota[0]['criterio_deuda'] != 1){
                        $cuota[0]['criterio_deuda'] = 5;
                        $cuota[0]['formula_criterio_deuda'] .= "*** FILTRO #5 (maximo) ***\n";
                        $cuota[0]['formula_criterio_deuda'] .= "DEUDA > ".$cuota[0]['decimal_2']."\n";                        
                        $cuota[0]['formula_criterio_deuda'] .= "IMPORTE A DEBITAR = ".$cuota[0]['decimal_2']."\n";
                        $cuota[0]['importe_adebitar'] = $cuota[0]['decimal_2']; 
                    }
//                    #SI EL DEBITO CALCULADO ES MENOR QUE EL ULTIMO DEBITO Y LA DEUDA ES MAYOR AL ULTIMO DEBITO ENVIO EL ULTIMO DEBITO
                    if(($cuota[0]['importe_adebitar'] < $cuota[0]['ultimo_debito']) && ($cuota[0]['deuda'] > $cuota[0]['ultimo_debito'])){
                        $cuota[0]['criterio_deuda'] = 6;
                        $cuota[0]['formula_criterio_deuda'] .= "*** ULTIMO DEBITO ***\n";
                        $cuota[0]['formula_criterio_deuda'] .= "IMPORTE A DEBITAR < ULTIMO DEBITO COBRADO < DEUDA\n";
                        $cuota[0]['formula_criterio_deuda'] .= "IMPORTE A DEBITAR = ".$cuota[0]['ultimo_debito']." (ULTIMO DEBITO)\n";
                        $cuota[0]['importe_adebitar'] = $cuota[0]['ultimo_debito'];                        
                    }
                    
                    $cuota[0]['deuda'] = round($cuota[0]['importe_adebitar'],2);

                }
				
            endif;
                        
            array_push($cuotas_FILTRO_2,$cuota);
            
        }
//        debug($cuotas_FILTRO_2);
//        exit;	
//        $resumen = $cuotas_FILTRO_2;
        
        #################################################################################
        # CONTROL BANCO NACION
        # NO ENVIAR LA DEUDA SOLO EL PERIODO
        # SI NO TIENE PERIODO, ENVIAR LA DEUDA FRACCIONADA POR PERIODO
        ################################################################################# 
        if($CONTROL_NACION){
            $impoCuotaSocial = parent::GlobalDato('decimal_1','MUTUCUOS' . substr($organismo,8,4));
            $cuotas_FILTRO_NAC = array();
            $PERIODO_BCONAC = false;
            foreach($cuotas_FILTRO_2 as $idx => $cuota){
                if(isset($cuota[0]['banco_id']) && $cuota[0]['banco_id'] == $BANCO_CONTROL){
                     if($cuota[0]['periodo'] == 1){
                         array_push($cuotas_FILTRO_NAC,$cuota);
//                         $PERIODO_BCONAC = true;
                         if(round($cuota[0]['importe_adebitar'],2) != round($impoCuotaSocial,2)) $PERIODO_BCONAC = true;
                         else $PERIODO_BCONAC = false;
                     }
                }else{
                    array_push($cuotas_FILTRO_NAC,$cuota);
                }
            }

            foreach($cuotas_FILTRO_2 as $idx => $cuota){
                if(isset($cuota[0]['banco_id']) && $cuota[0]['banco_id'] == $BANCO_CONTROL){
                     if($cuota[0]['periodo'] == 0 && !$PERIODO_BCONAC){
                         array_push($cuotas_FILTRO_NAC,$cuota);
                     }
                }
            }
//            $resumen = $cuotas_FILTRO_6;
//            debug($cuotas_FILTRO_NAC);
        }else{
            $cuotas_FILTRO_NAC = $cuotas_FILTRO_2;
        }        
        
        $cuotas_FILTRO_3 = $cuotas_FILTRO_NAC;
		
		#####################################################################################
		# SI UNO DE LOS REGISTROS ES MENOR A 50 ENVIAR TODO EN UN SOLO REGISTRO
		#####################################################################################
		$cuotas_FILTRO_4 = array();
//		$minimo = 50;
		
		$result = Set::sort($cuotas_FILTRO_3, '{n}.{n}.importe_adebitar', 'asc');
//                debug($result);

		if(!empty($result) && count($result) == 2){

			if($result[0][0]['importe_adebitar'] < $this->impoMinDtoCBU){
				
				$result[1][0]['formula_criterio_deuda'] .= " *** UNIFICACION DE REGISTROS (IMPORTES MENORES A ".$this->impoMinDtoCBU.") ***\n";
				$result[1][0]['formula_criterio_deuda'] .= (isset($result[1][0]['importe_adebitar']) ? $result[1][0]['importe_adebitar'] . " + " : "") . $result[0][0]['importe_adebitar'];
				$result[1][0]['importe_adebitar'] += $result[0][0]['importe_adebitar'];
				$result[1][0]['formula_criterio_deuda'] .= " ==> IMPORTE A DEBITAR = " . number_format($result[1][0]['importe_adebitar'],2). "\n";
				
				$cuotas_FILTRO_4[0][0] = $result[1][0];
			
			}else{

				$cuotas_FILTRO_4 = $cuotas_FILTRO_3;
			
			}
		
		}else{
			
			$cuotas_FILTRO_4 = $cuotas_FILTRO_3;
			
		}
		
//		debug($cuotas_FILTRO_4);
//		exit;		
		

		########################################################################################
		#RECORRO PARA DETECTAR REGISTROS CON IMPORTE MAYOR AL TOPE PARA
		#DIVIDIRLO EN SUB-REGISTROS
		########################################################################################
		$tope = parent::GlobalDato('decimal_1',$organismo);
		$n = 0;
		$saldo = 0;
		$subRegistros = array();
		$tmp = array();
		foreach($cuotas_FILTRO_4 as $cuota):
		
			$subRegistros = array();
//			$importeCorte --> ver mas arriba == 50
			/**
			 * Si la diferencia al maximo de 1999 es menor a 50 enviar en un solo registro
			 * GABRIEL 14/09/2011
			 */
			$excedente = abs($cuota[0]['importe_adebitar'] - $tope);
			
			if($cuota[0]['importe_adebitar'] > $tope && $excedente > $this->impoMinDtoCBU):
			
				$n = floor($cuota[0]['importe_adebitar'] / $tope);
				$resto = $cuota[0]['importe_adebitar'] % $tope;
				
				for($i=0;$i<=$n;$i++):
				
					$registro = $cuota[0]['registro'] + $n - $i;
					
					if($i < $n):
					
						$tmp[0] = array(
							'codigo_organismo' => $cuota[0]['codigo_organismo'],
							'socio_id' => $cuota[0]['socio_id'],
							'persona_beneficio_id' => $cuota[0]['persona_beneficio_id'],
							'deuda' => $tope,
							'importe_adebitar' => $tope,
							'codigo_dto' => $cuota[0]['codigo_dto'],
							'sub_codigo' => $cuota[0]['sub_codigo'],
							'periodo' => $cuota[0]['periodo'],						
							'registro' => $registro,
							'acuerdo_debito' => $cuota[0]['acuerdo_debito'],
							'criterio_deuda' => (isset($cuota[0]['criterio_deuda']) ? $cuota[0]['criterio_deuda'] : 0),	
							'formula_criterio_deuda' => $cuota[0]['formula_criterio_deuda']."** MAXIMO POR REGISTRO ($tope) ***",
                                                        'banco_id' => $cuota[0]['banco_id'],
                                                        'importe_max_registro_cbu' => $cuota[0]['importe_max_registro_cbu']
						);
						
					else:
					
						$tmp[0] = array(
							'codigo_organismo' => $cuota[0]['codigo_organismo'],
							'socio_id' => $cuota[0]['socio_id'],
							'persona_beneficio_id' => $cuota[0]['persona_beneficio_id'],
							'deuda' => $cuota[0]['importe_adebitar'] - ($tope * $n),
							'importe_adebitar' => $cuota[0]['importe_adebitar'] - ($tope * $n),
							'codigo_dto' => $cuota[0]['codigo_dto'],
							'sub_codigo' => $cuota[0]['sub_codigo'],
							'periodo' => $cuota[0]['periodo'],						
							'registro' => $registro,
							'acuerdo_debito' => $cuota[0]['acuerdo_debito'],
							'criterio_deuda' => (isset($cuota[0]['criterio_deuda']) ? $cuota[0]['criterio_deuda'] : 0),
							'formula_criterio_deuda' => $cuota[0]['formula_criterio_deuda']."** MAXIMO POR REGISTRO (".  round($cuota[0]['importe_adebitar'] - ($tope * $n),2).") ***",
                                                        'banco_id' => $cuota[0]['banco_id'],
                                                        'importe_max_registro_cbu' => $cuota[0]['importe_max_registro_cbu']
						);
						
						
					endif;
//					debug($tmp);
					array_push($resumen,$tmp);
				
				endfor;
				
			else:
				
				array_push($resumen,$cuota);
			
			endif;
			
		endforeach;
		
//		debug($resumen);
		
		#######################################################################################################
		# CONTROL PARTICULAR DE EL MAXIMO IMPORTE POR REGISTRO DE DEBITO (SUBDIVISION)
		#######################################################################################################
		$cuotas_FILTRO_5 = array();

		foreach($resumen as $idx => $cuota){
			
			$impoDebito = $cuota[0]['importe_adebitar'];
			$impoDebitoResta = $impoDebito;
			
//			$minBcoNacion = $oBENEFICIO->getImpoMaxRegistroCBU($cuota[0]['persona_beneficio_id']);
                        $minBcoNacion = $cuota[0]['importe_max_registro_cbu'];
			
			if( $impoDebito > ($minBcoNacion + $this->impoMinDtoCBU) && $minBcoNacion != 0){
				
				$n = floor($impoDebito / ($minBcoNacion + $this->impoMinDtoCBU));
				$tmp = array();
				$tmp = $cuota;
                                
				for($i=0; $i <= $n; $i++):
					
					if($i < $n):
					
						$tmp[0]['importe_adebitar'] = $minBcoNacion;
						$tmp[0]['deuda'] = $tmp[0]['importe_adebitar'];
                                                $tmp[0]['formula_criterio_deuda'] = $cuota[0]['formula_criterio_deuda'] . "** ACUERDO DEBITO - MAXIMO POR REGISTRO ($minBcoNacion) ***";
						
					else:
					
						$tmp[0]['importe_adebitar'] = $cuota[0]['importe_adebitar'] - ($minBcoNacion * $n);
						$tmp[0]['deuda'] = $tmp[0]['importe_adebitar'];
                                                $tmp[0]['formula_criterio_deuda'] = $cuota[0]['formula_criterio_deuda'] . "** ACUERDO DEBITO - MAXIMO POR REGISTRO (".$tmp[0]['importe_adebitar'].") ***";
					
					endif;
					
					array_push($cuotas_FILTRO_5,$tmp);
				
				endfor;
				
			}else{
				
				array_push($cuotas_FILTRO_5,$cuota);
				
			}
			
		}
		
//		debug($cuotas_FILTRO_5);
		
		$resumen = $cuotas_FILTRO_5;
		
		#######################################################################################################
		# CONTROL DE MINIMO
		#######################################################################################################
		$min = $this->impoMinDtoCBU * 10;
		foreach($resumen as $idx => $cuota){
			if($cuota[0]['importe_adebitar'] < $min){
                if(($idx - 1) < 0) break; 
				if(round(floatval(($resumen[$idx - 1][0]['importe_adebitar'] - $min )),2) > $min){
					$resumen[$idx - 1][0]['importe_adebitar'] -= $min;
					$resumen[$idx][0]['importe_adebitar'] += $min;
				}
			}
		}		

		#######################################################################################################
		# 	RENUMERAR LOS REGISTROS
		#######################################################################################################
		$r = 1;
		foreach($resumen as $idx => $cuota){
			$resumen[$idx][0]['registro'] = $r;
			$r++;
		}
//        debug($resumen);
//        exit;
        
        
		return $resumen;
		
	}        
        
        
        
        
	
	/**
	 * Nuevo esquema de dise�o de registro para CJP
	 * @param $liquidacion_id
	 * @param $socio_id
	 * @param $periodo
	 * @param $organismo
	 */
	function __armaNuevoResumenCJP($liquidacion_id,$socio_id,$periodo,$organismo){
		
		
		$sqlCsocPeriodo = "	SELECT 
								LiquidacionCuotaNoimputada.codigo_organismo,
								LiquidacionCuotaNoimputada.socio_id,
								LiquidacionCuotaNoimputada.orden_descuento_id,
								LiquidacionCuotaNoimputada.persona_beneficio_id,
								SUM(saldo_actual) AS deuda,
								SUM(saldo_actual) AS importe_adebitar,
								'".$this->CJP_COD_CSOC."' AS codigo_dto,
								'".$this->CJP_SCOD_CSOC."' AS sub_codigo,
								1 AS periodo,
								LiquidacionCuotaNoimputada.registro,
								0 AS acuerdo_debito,
								0 AS detalla,
								'CUOTA_SOCIAL_PERIODO' as concepto
							FROM 	liquidacion_cuota_noimputadas AS LiquidacionCuotaNoimputada 
								INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
							WHERE 
								LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
								AND SUBSTRING(codigo_organismo,9,2) = 77
								AND LiquidacionCuotaNoimputada.socio_id = $socio_id
								AND LiquidacionCuotaNoimputada.tipo_cuota = 'MUTUTCUOCSOC'
								AND LiquidacionCuotaNoimputada.periodo_cuota = '$periodo'
							GROUP BY
								LiquidacionCuotaNoimputada.codigo_organismo,
								LiquidacionCuotaNoimputada.socio_id,
								LiquidacionCuotaNoimputada.orden_descuento_id,
								PersonaBeneficio.tipo,
								PersonaBeneficio.nro_ley,
								PersonaBeneficio.nro_beneficio,
								PersonaBeneficio.sub_beneficio";
		
		$sqlCsocAtraso = "	SELECT 
								LiquidacionCuotaNoimputada.codigo_organismo,
								LiquidacionCuotaNoimputada.socio_id,
								LiquidacionCuotaNoimputada.orden_descuento_id,
								LiquidacionCuotaNoimputada.persona_beneficio_id,
								SUM(saldo_actual) AS deuda,
								SUM(saldo_actual) AS importe_adebitar,
								'".$this->CJP_COD_CONS."' AS codigo_dto,
								'".$this->CJP_SCOD_CONS."' AS sub_codigo,
								1 AS periodo,
								LiquidacionCuotaNoimputada.registro,
								0 AS acuerdo_debito,
								0 AS detalla,
								'CUOTA_SOCIAL_ATRASO' as concepto
							FROM 	liquidacion_cuota_noimputadas AS LiquidacionCuotaNoimputada 
								INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
							WHERE 
								LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
								AND SUBSTRING(codigo_organismo,9,2) = 77
								AND LiquidacionCuotaNoimputada.socio_id = $socio_id
								AND LiquidacionCuotaNoimputada.tipo_cuota = 'MUTUTCUOCSOC'
								AND LiquidacionCuotaNoimputada.periodo_cuota < '$periodo'
							GROUP BY
								LiquidacionCuotaNoimputada.codigo_organismo,
								LiquidacionCuotaNoimputada.socio_id,
								LiquidacionCuotaNoimputada.orden_descuento_id,
								PersonaBeneficio.tipo,
								PersonaBeneficio.nro_ley,
								PersonaBeneficio.nro_beneficio,
								PersonaBeneficio.sub_beneficio";
		
		
		$sqlAdicionales = "	SELECT 
								LiquidacionCuotaNoimputada.codigo_organismo,
								LiquidacionCuotaNoimputada.socio_id,
								LiquidacionCuotaNoimputada.orden_descuento_id,
								LiquidacionCuotaNoimputada.persona_beneficio_id,
								SUM(saldo_actual) AS deuda,
								SUM(saldo_actual) AS importe_adebitar,
								'".$this->CJP_COD_CONS."' AS codigo_dto,
								'".$this->CJP_SCOD_CONS."' AS sub_codigo,
								1 AS periodo,
								LiquidacionCuotaNoimputada.registro,
								0 AS acuerdo_debito,
								0 AS detalla,
								'ADICIONALES' as concepto
							FROM 	liquidacion_cuota_noimputadas AS LiquidacionCuotaNoimputada 
								INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
							WHERE 
								LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
								AND SUBSTRING(codigo_organismo,9,2) = 77
								AND LiquidacionCuotaNoimputada.socio_id = $socio_id
								AND LiquidacionCuotaNoimputada.mutual_adicional_pendiente_id <> 0
							GROUP BY
								LiquidacionCuotaNoimputada.codigo_organismo,
								LiquidacionCuotaNoimputada.socio_id,
								LiquidacionCuotaNoimputada.orden_descuento_id,
								PersonaBeneficio.tipo,
								PersonaBeneficio.nro_ley,
								PersonaBeneficio.nro_beneficio,
								PersonaBeneficio.sub_beneficio";
		
		$sqlConsumos = "	SELECT 
								LiquidacionCuotaNoimputada.codigo_organismo,
								LiquidacionCuotaNoimputada.socio_id,
								LiquidacionCuotaNoimputada.orden_descuento_id,
								LiquidacionCuotaNoimputada.persona_beneficio_id,
								SUM(saldo_actual) AS deuda,
								SUM(saldo_actual) AS importe_adebitar,
								'".$this->CJP_COD_CONS."' AS codigo_dto,
								'".$this->CJP_SCOD_CONS."' AS sub_codigo,
								1 AS periodo,
								LiquidacionCuotaNoimputada.registro,
								0 AS acuerdo_debito,
								1 AS detalla,
								'CONSUMOS' as concepto
							FROM 	liquidacion_cuota_noimputadas AS LiquidacionCuotaNoimputada 
								INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
							WHERE 
								LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
								AND SUBSTRING(codigo_organismo,9,2) = 77
								AND LiquidacionCuotaNoimputada.socio_id = $socio_id
								AND LiquidacionCuotaNoimputada.tipo_cuota <> 'MUTUTCUOCSOC'
								AND LiquidacionCuotaNoimputada.tipo_orden_dto <> 'CMUTU'
								AND LiquidacionCuotaNoimputada.mutual_adicional_pendiente_id = 0
							GROUP BY
								LiquidacionCuotaNoimputada.codigo_organismo,
								LiquidacionCuotaNoimputada.socio_id,
								LiquidacionCuotaNoimputada.orden_descuento_id,
								PersonaBeneficio.tipo,
								PersonaBeneficio.nro_ley,
								PersonaBeneficio.nro_beneficio,
								PersonaBeneficio.sub_beneficio";
		

		$sql = "$sqlCsocPeriodo UNION $sqlConsumos";
//		$sql = "$sqlCsocPeriodo UNION $sqlCsocAtraso UNION $sqlAdicionales UNION $sqlConsumos";
		
//		debug($sql);
//		exit;
	
		$cuotas = $this->query($sql);
		
		$cuotas_FILTRO_1 = array();
		
//		DEBUG($cuotas);
//		exit;		
		
		if(empty($cuotas)) return null;
		
		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();		
		
		App::import('Model','Mutual.LiquidacionSocioDescuento');
		$oLSOCIODTO = new LiquidacionSocioDescuento();			
		
		foreach($cuotas as $cuota){
			
			
			
			$cuota[0]['fecha_otorgamiento'] = null;
			$cuota[0]['importe_total'] = 0;
			$cuota[0]['cuotas'] = 0;
			$cuota[0]['importe_cuota'] = 0;
			$cuota[0]['importe_deuda'] = 0;
			$cuota[0]['importe_deuda_vencida'] = 0;
			$cuota[0]['importe_deuda_no_vencida'] = 0;
			$cuota[0]['formula_criterio_deuda'] = null;
		
			if($cuota[0]['detalla'] == 1){
				
				#CARGA UNA ORDEN CON LOS SALDOS SEGUN EL ORGANISMO
//				$orden = $oORDEN->getOrden($cuota[0]['orden_descuento_id'],$periodo,true,true,$organismo);
//				$ordenOriginal = $oORDEN->getOrden($cuota[0]['orden_descuento_id'],$periodo,true,true);
				$orden = $oORDEN->getOrden($cuota[0]['orden_descuento_id'],$periodo,true,true);
				
				
				$fechaOtorgamiento = (parent::is_date($orden['OrdenDescuento']['fecha']) ? $orden['OrdenDescuento']['fecha'] : null);
				$importeTOTAL = $orden['OrdenDescuento']['importe_cuota'] * $orden['OrdenDescuento']['cuotas'];
				$cantCUOTAS = $orden['OrdenDescuento']['cuotas'];
				$impoCUOTA = $orden['OrdenDescuento']['importe_cuota'];
				
			
				###########################################################################################################
				#COMPARO LOS SALDOS DE LA ORDEN SEGUN EL ORGANISMO Y LOS SALDOS PUROS
				#PARA SIMULAR UNA ORDEN NUEVA PARA LA CAJA (CREDITOS QUE VENIAN POR CBU Y SE PASAN A RECIBO DE SUELDO)
				#GABRIEL 07/02/2012
				###########################################################################################################
//				if($orden['OrdenDescuento']['reprogramada'] == 1 && $orden['OrdenDescuento']['reasignada'] == 1 && $orden['OrdenDescuento']['saldo'] <= $ordenOriginal['OrdenDescuento']['saldo'] && ($orden['OrdenDescuento']['avencer'] + $orden['OrdenDescuento']['pagadas'] < $orden['OrdenDescuento']['cuotas'])){
//					$importeTOTAL = $orden['OrdenDescuento']['saldo'];
//					$cantCUOTAS = $orden['OrdenDescuento']['avencer'];
//					#FECHA DE OTORGAMIENTO / INICIO == PRIMER DIA HABIL POSTERIOR AL PERIODO QUE ESTOY LIQUIDANDO
//					$fechaOtorgamiento = parent::getDiaHabil($periodo);
//				}
				
//				debug(checkdate($orden['OrdenDescuento']['fecha']));
//				echo $orden['OrdenDescuento']['fecha']." --> ".parent::is_date($orden['OrdenDescuento']['fecha'])."<br/>";
				
				$cuota[0]['fecha_otorgamiento'] = $fechaOtorgamiento;
				$cuota[0]['importe_total'] = $importeTOTAL;
				$cuota[0]['cuotas'] = $cantCUOTAS;
				$cuota[0]['importe_cuota'] = $impoCUOTA;
//				$cuota[0]['importe_deuda'] = $cuota[0]['deuda'];
				$cuota[0]['importe_deuda'] = $orden['OrdenDescuento']['saldo'];
				
				$cuota[0]['importe_deuda_vencida'] = $orden['OrdenDescuento']['importe_vencido'];
				
//				$cuota[0]['importe_deuda_no_vencida'] = $orden['OrdenDescuento']['importe_cuota'];
				$cuota[0]['importe_deuda_no_vencida'] = $orden['OrdenDescuento']['importe_avencer'];
				
				//CONTROL SI EL SALDO FINAL DEL CREDITO ES MENOR AL IMPORTE DE UNA CUOTA
//				if($orden['OrdenDescuento']['saldo'] <= $orden['OrdenDescuento']['importe_cuota']){
//					$cuota[0]['importe_deuda_vencida'] = 0;
//					$cuota[0]['importe_deuda_no_vencida'] = $cuota[0]['importe_adebitar'];
//				}else{
//					$cuota[0]['importe_deuda_vencida'] = $cuota[0]['importe_deuda'] - $cuota[0]['importe_deuda_no_vencida'];
//				}
				
//				if($cuota[0]['importe_adebitar'] >= $cuota[0]['importe_cuota'])  $cuota[0]['importe_adebitar'] = $cuota[0]['importe_cuota'];
				
				$cuota[0]['importe_adebitar'] = $cuota[0]['importe_cuota'];
				
				//SI LA ORDEN ES PERMANENTE
				/**
				 * GUILLERMO 02/09/11 --> PARA LOS CONSUMOS PERMANENTES NO ENVIAR LA MORA, ENVIAR SOLAMENTE EL IMPORTE
				 * DEL MES. LA MORA LA GESTIONA LA MUTUAL
				 */
				if($orden['OrdenDescuento']['permanente'] == 1){
//					$cuota[0]['cuotas'] = 1;
//					$cuota[0]['importe_adebitar'] = $cuota[0]['importe_cuota'];
//					$cuota[0]['importe_deuda'] = $cuota[0]['importe_adebitar'];	
//					$cuota[0]['importe_total'] = $cuota[0]['importe_adebitar'];				
//					
//					$cuota[0]['importe_deuda_vencida'] = 0;
//					$cuota[0]['importe_deuda_no_vencida'] = $cuota[0]['importe_adebitar'];

					$cuota[0]['cuotas'] = 0;
					$cuota[0]['importe_adebitar'] = $cuota[0]['importe_cuota'];
					$cuota[0]['importe_deuda'] = 0;	
					$cuota[0]['importe_total'] = 0;				
					
					$cuota[0]['importe_deuda_vencida'] = 0;
					$cuota[0]['importe_deuda_no_vencida'] = 0;
					$cuota[0]['importe_cuota'] = 0;
					$cuota[0]['fecha_otorgamiento'] = (parent::is_date($orden['OrdenDescuento']['fecha']) ? $orden['OrdenDescuento']['fecha'] : null);
					
					$cuota[0]['concepto'] .= " (PERMANENTE)";
//					debug($cuota);
				}
				

				//CONTROLAR SI NO TIENE UN DESCUENTO PARA CALCULAR
				$dtoAplicado = $oLSOCIODTO->getImpoDescuentoAplicado($periodo,$organismo,$socio_id,$cuota[0]['orden_descuento_id']);
				
//				debug($dtoAplicado);
				
				if(!empty($dtoAplicado)):
					$cuota[0]['importe_deuda'] -= $dtoAplicado;
					$cuota[0]['importe_deuda_vencida'] -= $dtoAplicado;
				endif;				
				
				$cuota[0]['formula_criterio_deuda'] .= "ORDEN #" . $cuota[0]['orden_descuento_id']." ** ".$cuota[0]['concepto']." **\n";
				$cuota[0]['formula_criterio_deuda'] .= "TOTAL ORDEN = " . number_format($cuota[0]['importe_total'],2) . " | TOTAL ADEUDADO = " . number_format($cuota[0]['importe_deuda'],2)."\n";
				$cuota[0]['formula_criterio_deuda'] .= "VENCIDO = " . number_format($cuota[0]['importe_deuda_vencida'],2)." | NO VENCIDO = ".number_format($cuota[0]['importe_deuda_no_vencida'],2);
				$cuota[0]['formula_criterio_deuda'] .= " | CUOTA: " . number_format($cuota[0]['importe_cuota'],2) . " (".$cuota[0]['cuotas'].")";
				if(!empty($dtoAplicado)) $cuota[0]['formula_criterio_deuda'] .= " (DTO APLICADO S/DEUDA ".number_format($dtoAplicado,2).")";

				
			}
			
			//para los casos que son consumos y no tiene detalle de la orden (cuota social atrasada + gastos adicionales)
			//la cuota social atrasada no se envia a descuento
			//los gastos adicionales tampoco van a descuento
			if($cuota[0]['detalla'] == 0 && $cuota[0]['sub_codigo'] == 1):
			
//				$cuota[0]['fecha_otorgamiento'] = date('Y-m-d');
//				$cuota[0]['importe_total'] = $cuota[0]['importe_adebitar'];
//				$cuota[0]['cuotas'] = 1;
//				$cuota[0]['importe_cuota'] = $cuota[0]['importe_adebitar'];
//				$cuota[0]['importe_deuda'] = $cuota[0]['importe_adebitar'];
//				$cuota[0]['importe_deuda_vencida'] = $cuota[0]['importe_adebitar'];
//				$cuota[0]['importe_deuda_no_vencida'] = 0;
				
//				$cuota[0]['fecha_otorgamiento'] = (parent::is_date($orden['OrdenDescuento']['fecha']) ? $orden['OrdenDescuento']['fecha'] : null);
//				$cuota[0]['importe_total'] = 0;
//				$cuota[0]['cuotas'] = 0;
//				$cuota[0]['importe_cuota'] = 0;
//				$cuota[0]['importe_deuda'] = 0;
//				$cuota[0]['importe_deuda_vencida'] = 0;
//				$cuota[0]['importe_deuda_no_vencida'] = 0;				
//				
//				$cuota[0]['formula_criterio_deuda'] .= "ORDEN #" . $cuota[0]['orden_descuento_id']." ** ".$cuota[0]['concepto']." **\n";
//				$cuota[0]['formula_criterio_deuda'] .= "TOTAL ADEUDADO = " . number_format($cuota[0]['importe_deuda'],2)."\n";
//				$cuota[0]['formula_criterio_deuda'] .= "VENCIDO = " . number_format($cuota[0]['importe_deuda_vencida'],2)." | NO VENCIDO = ".number_format($cuota[0]['importe_deuda_no_vencida'],2);
				
							
			endif;
			
//			if($cuota[0]['orden_descuento_id'] == 52991 && $cuota[0]['detalla'] == 1):
//				debug($orden);
//				debug($cuota);
//			endif;
			
			array_push($cuotas_FILTRO_1,$cuota);
			
		}
		
//		DEBUG($cuotas_FILTRO_1);
//		exit;
		
		return $cuotas_FILTRO_1;
		
	}
	
	
	/**
	 * Arma Resumen CJP
	 * Devuelve el resumen para la cabecera del socio si es CJP. La cuota social del periodo va en el 
	 * codigo 207-0  el resto de los conceptos (incluido la cuota social atrasada) van en el codigo 207-1
	 * @param $liquidacion_id
	 * @param $socio_id
	 * @param $periodo
	 */
	function __armaResumenCJP($liquidacion_id,$socio_id,$periodo,$organismo){
	#AGRUPA POR TIPO | NRO_LEY | NRO_BENEFICIO | SUB_BENEFICIO
	$sql = "SELECT 
				LiquidacionCuotaNoimputada.codigo_organismo,
				LiquidacionCuotaNoimputada.socio_id,
				LiquidacionCuotaNoimputada.persona_beneficio_id,
				sum(saldo_actual) as deuda,
				sum(saldo_actual) as importe_adebitar,
				'".$this->CJP_COD_CSOC."' as codigo_dto,
				'".$this->CJP_SCOD_CSOC."' as sub_codigo,
				1 as periodo,
				LiquidacionCuotaNoimputada.registro,
				0 as acuerdo_debito
			FROM 	liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
				INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
			WHERE 
				LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
				AND SUBSTRING(codigo_organismo,9,2) = 77
				AND LiquidacionCuotaNoimputada.socio_id = $socio_id 
				AND LiquidacionCuotaNoimputada.tipo_cuota = 'MUTUTCUOCSOC'
				AND LiquidacionCuotaNoimputada.periodo_cuota = '$periodo'
			GROUP BY
				LiquidacionCuotaNoimputada.codigo_organismo,
				LiquidacionCuotaNoimputada.socio_id,
				PersonaBeneficio.tipo,
				PersonaBeneficio.nro_ley,
				PersonaBeneficio.nro_beneficio,
				PersonaBeneficio.sub_beneficio				
			UNION
			SELECT  
				LiquidacionCuotaNoimputada.codigo_organismo,
				LiquidacionCuotaNoimputada.socio_id,
				LiquidacionCuotaNoimputada.persona_beneficio_id,
				sum(saldo_actual) as deuda,
				sum(saldo_actual) as importe_adebitar,
				'".$this->CJP_COD_CONS."' as codigo_dto,
				'".$this->CJP_SCOD_CONS."' as sub_codigo,
				0 as periodo,
				LiquidacionCuotaNoimputada.registro,
				PersonaBeneficio.acuerdo_debito
			FROM 
				liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
				INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
			WHERE 
				LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
				AND SUBSTRING(codigo_organismo,9,2) = 77
				AND LiquidacionCuotaNoimputada.socio_id = $socio_id 
				AND LiquidacionCuotaNoimputada.periodo_cuota <= '$periodo'
				AND LiquidacionCuotaNoimputada.id NOT IN (select id from liquidacion_cuota_noimputadas as lc 
				where lc.liquidacion_id = LiquidacionCuotaNoimputada.liquidacion_id and
				SUBSTRING(lc.codigo_organismo,9,2) = SUBSTRING(LiquidacionCuotaNoimputada.codigo_organismo,9,2) AND
				lc.socio_id = LiquidacionCuotaNoimputada.socio_id and lc.tipo_cuota = 'MUTUTCUOCSOC' AND
				lc.periodo_cuota = '$periodo')
				AND PersonaBeneficio.acuerdo_debito = 0
			GROUP BY
				LiquidacionCuotaNoimputada.codigo_organismo,
				LiquidacionCuotaNoimputada.socio_id,
				PersonaBeneficio.tipo,
				PersonaBeneficio.nro_ley,
				PersonaBeneficio.nro_beneficio,
				PersonaBeneficio.sub_beneficio
			UNION
			SELECT  
				LiquidacionCuotaNoimputada.codigo_organismo,
				LiquidacionCuotaNoimputada.socio_id,
				LiquidacionCuotaNoimputada.persona_beneficio_id,
				sum(saldo_actual) as deuda,
				sum(saldo_actual) as importe_adebitar,
				'".$this->CJP_COD_CONS."' as codigo_dto,
				'".$this->CJP_SCOD_CONS."' as sub_codigo,
				0 as periodo,
				LiquidacionCuotaNoimputada.registro,
				PersonaBeneficio.acuerdo_debito
			FROM 
				liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
				INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
			WHERE 
				LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
				AND SUBSTRING(codigo_organismo,9,2) = 77
				AND LiquidacionCuotaNoimputada.socio_id = $socio_id 
				AND LiquidacionCuotaNoimputada.periodo_cuota <= '$periodo'
				AND LiquidacionCuotaNoimputada.id NOT IN (select id from liquidacion_cuota_noimputadas as lc 
				where lc.liquidacion_id = LiquidacionCuotaNoimputada.liquidacion_id and
				SUBSTRING(lc.codigo_organismo,9,2) = SUBSTRING(LiquidacionCuotaNoimputada.codigo_organismo,9,2) AND
				lc.socio_id = LiquidacionCuotaNoimputada.socio_id and lc.tipo_cuota = 'MUTUTCUOCSOC' AND
				lc.periodo_cuota = '$periodo')
				AND PersonaBeneficio.acuerdo_debito <> 0
			GROUP BY
				LiquidacionCuotaNoimputada.codigo_organismo,
				LiquidacionCuotaNoimputada.socio_id,
				PersonaBeneficio.tipo,
				PersonaBeneficio.nro_ley,
				PersonaBeneficio.nro_beneficio,
				PersonaBeneficio.sub_beneficio
			ORDER BY 
				codigo_organismo;";
	
//		debug($sql);
//		exit;
		
		$cuotas = $this->query($sql);
		
//		debug($cuotas);
		
		$sql_2 ="
			SELECT  
				LiquidacionCuotaNoimputada.codigo_organismo,
				LiquidacionCuotaNoimputada.socio_id,
				LiquidacionCuotaNoimputada.persona_beneficio_id,
				sum(saldo_actual) as deuda,
				sum(saldo_actual) as importe_adebitar,
				'".$this->CJP_COD_CONS."' as codigo_dto,
				'".$this->CJP_SCOD_CONS."' as sub_codigo,
				0 as periodo,
				3 as registro,
				PersonaBeneficio.acuerdo_debito
			FROM 
				liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
				INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
			WHERE 
				LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
				AND SUBSTRING(codigo_organismo,9,2) = 77
				AND LiquidacionCuotaNoimputada.socio_id = $socio_id 
				AND LiquidacionCuotaNoimputada.periodo_cuota < '$periodo'
				AND LiquidacionCuotaNoimputada.id NOT IN (select id from liquidacion_cuota_noimputadas as lc 
				where lc.liquidacion_id = LiquidacionCuotaNoimputada.liquidacion_id and
				SUBSTRING(lc.codigo_organismo,9,2) = SUBSTRING(LiquidacionCuotaNoimputada.codigo_organismo,9,2) AND
				lc.socio_id = LiquidacionCuotaNoimputada.socio_id and lc.tipo_cuota = 'MUTUTCUOCSOC' AND
				lc.periodo_cuota = '$periodo')
				AND PersonaBeneficio.acuerdo_debito = 0
			GROUP BY
				LiquidacionCuotaNoimputada.codigo_organismo,
				LiquidacionCuotaNoimputada.socio_id,
				PersonaBeneficio.tipo,
				PersonaBeneficio.nro_ley,
				PersonaBeneficio.nro_beneficio,
				PersonaBeneficio.sub_beneficio	
			UNION
			SELECT  
				LiquidacionCuotaNoimputada.codigo_organismo,
				LiquidacionCuotaNoimputada.socio_id,
				LiquidacionCuotaNoimputada.persona_beneficio_id,
				sum(saldo_actual) as deuda,
				sum(saldo_actual) as importe_adebitar,
				'".$this->CJP_COD_CONS."' as codigo_dto,
				'".$this->CJP_SCOD_CONS."' as sub_codigo,
				1 as periodo,
				4 as registro,
				PersonaBeneficio.acuerdo_debito
			FROM 
				liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
				INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
			WHERE 
				LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
				AND SUBSTRING(codigo_organismo,9,2) = 77
				AND LiquidacionCuotaNoimputada.socio_id = $socio_id 
				AND LiquidacionCuotaNoimputada.periodo_cuota = '$periodo'
				AND LiquidacionCuotaNoimputada.id NOT IN (select id from liquidacion_cuota_noimputadas as lc 
				where lc.liquidacion_id = LiquidacionCuotaNoimputada.liquidacion_id and
				SUBSTRING(lc.codigo_organismo,9,2) = SUBSTRING(LiquidacionCuotaNoimputada.codigo_organismo,9,2) AND
				lc.socio_id = LiquidacionCuotaNoimputada.socio_id and lc.tipo_cuota = 'MUTUTCUOCSOC' AND
				lc.periodo_cuota = '$periodo')
				AND PersonaBeneficio.acuerdo_debito = 0
			GROUP BY
				LiquidacionCuotaNoimputada.codigo_organismo,
				LiquidacionCuotaNoimputada.socio_id,
				PersonaBeneficio.tipo,
				PersonaBeneficio.nro_ley,
				PersonaBeneficio.nro_beneficio,
				PersonaBeneficio.sub_beneficio											
			ORDER BY 
				codigo_organismo;";		
		
		$analisis = $this->query($sql_2);
		
		$periodo = 0;
		$mora = 0;
		
		if(!empty($analisis)):
			if(isset($analisis[0][0]['importe_adebitar'])) $mora = $analisis[0][0]['importe_adebitar'];
			if(isset($analisis[1][0]['importe_adebitar'])) $periodo = $analisis[1][0]['importe_adebitar'];
		endif;
		
		//CONTROLO EL ACUERDO DE PAGO
		//SI EL MONTO A DEBITAR TOTAL ES MENOR QUE EL ACUERDO DE PAGO LE LEVANTO EL ACUERDO DE PAGO
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBENEFICIO = new PersonaBeneficio();		
		foreach($cuotas as $idx => $cuota):
			if($cuota[0]['acuerdo_debito'] != 0 && $cuota[0]['acuerdo_debito'] < $cuota[0]['importe_adebitar']){
				$cuota[0]['formula_criterio_deuda'] = "*** ACUERDO DE DEBITO ****\n";
				$cuota[0]['formula_criterio_deuda'] .= "IMPORTE ORIGINAL A DEBITAR: ".$cuota[0]['importe_adebitar']." | ACUERDO DE DEBITO: ".$cuota[0]['acuerdo_debito']."\n";
				$cuota[0]['importe_adebitar'] = $cuota[0]['acuerdo_debito'];
			}else if($cuota[0]['acuerdo_debito'] != 0 && $cuota[0]['acuerdo_debito'] >= $cuota[0]['importe_adebitar']){
				$oBENEFICIO->resetAcuerdoPago($cuota[0]['persona_beneficio_id']);
			}
			$cuotas[$idx] = $cuota;
		
		endforeach;
		
		//CONTROLO QUE EL TOTAL A DEBITAR NO SUPERE EL TOPE ESTABLECIDO EN LA GLOBAL PARA EL CODIGO DEL ORGANISMO (DECIMAL_1)
		$tope = parent::GlobalDato('decimal_1',$organismo);
		
//		$tope = 130;
//		$periodo = 100;
//		$mora = 100;
		
		$total = $periodo + $mora;
		
		if($total > $tope):
			
			$aDebitar = $tope;
			$criterio = "*** FILTRO #1 ****\n";
			$criterio .= "CONTROL DE MONTO MAXIMO ($tope)\n";
			$criterio .= "PERIODO: $periodo | ATRASO: $mora | TOTAL:  $total (TOTAL > MAXIMO) \n";
			
			if(($periodo >= $tope)){
				$aDebitar = $periodo;
				$criterio .= "PERIODO >= MAXIMO ==> IMPORTE A DEBITAR:  $aDebitar (PERIODO) \n";				
			}elseif(($periodo < $tope)){
				$aDebitar = $tope;
				$criterio .= "PERIODO < MAXIMO ==> IMPORTE A DEBITAR:  $aDebitar (MAXIMO) \n";				
				
			}
			
			if(isset($cuotas[1][0]['importe_adebitar'])){
				$cuotas[1][0]['importe_adebitar'] = $aDebitar;
				$cuotas[1][0]['formula_criterio_deuda'] = $criterio;
			}
			
		endif;
	
//		debug($cuotas);
//		exit;
		
		return $cuotas;
		
	}
	
	/**
	 * Arma Resumen ANSES
	 * Devuelve el resumen para la cabecera del socio si es ANSES
	 * @param $liquidacion_id
	 * @param $socio_id
	 * @param $periodo
	 */
	function __armaResumenANSES($liquidacion_id,$socio_id,$periodo,$organismo){
		#AGRUPA POR NRO_BENEFICIO
		$sql = "SELECT  
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					LiquidacionCuotaNoimputada.persona_beneficio_id,
					sum(saldo_actual) as deuda,
					sum(saldo_actual) as importe_adebitar,
					'".$this->ANSES_COD_CSOC."' as codigo_dto,
					'".$this->ANSES_SCOD_CSOC."' as sub_codigo,
					0 as periodo,
					LiquidacionCuotaNoimputada.registro
				FROM 
					liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
					INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id)
				WHERE 
					LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
					AND SUBSTRING(codigo_organismo,9,2) = 66
					AND LiquidacionCuotaNoimputada.socio_id = $socio_id 
					AND LiquidacionCuotaNoimputada.tipo_cuota = 'MUTUTCUOCSOC'
				GROUP BY
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					PersonaBeneficio.nro_beneficio
				UNION
				SELECT  
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					LiquidacionCuotaNoimputada.persona_beneficio_id,
					sum(saldo_actual) as deuda,
					sum(saldo_actual) as importe_adebitar,
					'".$this->ANSES_COD_CONS."' as codigo_dto,
					'".$this->ANSES_SCOD_CONS."' as sub_codigo,
					0 as periodo,
					LiquidacionCuotaNoimputada.registro	
				FROM 
					liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada 
					INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuotaNoimputada.persona_beneficio_id) 
				WHERE 
					LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id
					AND SUBSTRING(codigo_organismo,9,2) = 66
					AND LiquidacionCuotaNoimputada.socio_id = $socio_id 
					AND LiquidacionCuotaNoimputada.tipo_cuota <> 'MUTUTCUOCSOC'
				GROUP BY
					LiquidacionCuotaNoimputada.codigo_organismo,
					LiquidacionCuotaNoimputada.socio_id,
					PersonaBeneficio.nro_beneficio
				ORDER BY 
					codigo_organismo;";	
		$cuotas = $this->query($sql);
		return $cuotas;			
	}
	
	/**
	 * Reset los valores de intercambio
	 * Establece a 0 los valores de importe_debitado y liquidacion_intercambio_id
	 * @param integer $liquidacion_id
	 * @param integer $socio_id
	 * @param integer $registro
	 * @param integer $liquidacion_intercambio_id
	 * @return boolean
	 */
	function resetValoresIntercambio($liquidacion_id,$socio_id,$registro,$liquidacion_intercambio_id=0){
		return $this->updateAll(
							array('LiquidacionCuotaNoimputada.importe_debitado' => 0,'LiquidacionCuotaNoimputada.liquidacion_intercambio_id' => 0),
							array(
								'LiquidacionCuotaNoimputada.socio_id' => $socio_id,
								'LiquidacionCuotaNoimputada.liquidacion_id' => $liquidacion_id,
								'LiquidacionCuotaNoimputada.liquidacion_intercambio_id' => $liquidacion_intercambio_id,
								'LiquidacionCuotaNoimputada.registro' => $registro
							)
		);
	}
	
	

	/**
	 * Cuotas pagadas por liquidacion
	 * devuelve las cuotas para una liquidacion dada NO imputadas y que esten marcadas para imputar
	 * y que no este imputada en cuenta corriente
	 * @param integer $liquidacion_id
	 * @param integer $socio_id
	 * @param integer $registro
	 */
	function getCuotasPagadasByLiquidacion($liquidacion_id,$socio_id,$registro=null,$todo = false){
		$conditions = array();
		$conditions['LiquidacionCuotaNoimputada.liquidacion_id'] = $liquidacion_id;
		$conditions['LiquidacionCuotaNoimputada.socio_id'] = $socio_id;
		if(!empty($registro))$conditions['LiquidacionCuotaNoimputada.registro'] = $registro;
		$conditions['LiquidacionCuotaNoimputada.para_imputar'] = 1;
		if(!$todo)$conditions['LiquidacionCuotaNoimputada.imputada'] = 0;
		
		$cuotas = $this->find('all',array(
										'conditions' => $conditions,
										'order' => array('LiquidacionCuotaNoimputada.socio_id,LiquidacionCuotaNoimputada.periodo_cuota'),
		));
		return $cuotas;
	}

	/**
	 * Cuotas imputadas por liquidacion por proveedor
	 * devuelve las cuotas imputadas no vinculadas a una liquidacion del proveedor
	 * para una liquidacion de deuda especifica. Agrupa por proveedor y suma el importe debitado
	 * @param integer $liquidacion_id
	 */
	function getCuotasImputadasByLiquidacionByProveedor($liquidacion_id){
		$proveedores = $this->find('all',array(
										'conditions' => array(
															'LiquidacionCuotaNoimputada.liquidacion_id' => $liquidacion_id,
															'LiquidacionCuotaNoimputada.imputada' => 1,
															'LiquidacionCuotaNoimputada.proveedor_liquidacion_id' => 0
														),
										'fields' => array('LiquidacionCuotaNoimputada.proveedor_id,LiquidacionCuotaNoimputada.tipo_cuota,  SUM(LiquidacionCuotaNoimputada.saldo_actual) as saldo_actual ,SUM(LiquidacionCuotaNoimputada.importe_debitado) as importe_debitado'),
										'group' => array('LiquidacionCuotaNoimputada.proveedor_id,LiquidacionCuotaNoimputada.tipo_cuota')								
		));
		foreach($proveedores as $idx => $proveedor){
			$proveedor['LiquidacionCuotaNoimputada']['importe_debitado'] = $proveedor[0]['importe_debitado'];
			$proveedor['LiquidacionCuotaNoimputada']['saldo_actual'] = $proveedor[0]['saldo_actual'];
			$proveedores[$idx]['LiquidacionCuotaNoimputada'] = $proveedor['LiquidacionCuotaNoimputada'];
		}
		return $proveedores;
	}	
	
	/**
	 * Borra la liquidacion de un socio para un periodo
	 * @param $socio_id
	 * @param $periodo
	 * @return boolean
	 */
	function borrarBySocioByPeriodo($socio_id,$periodo){
		$this->bindModel(array('belongsTo' => array('Liquidacion')));
		return $this->deleteAll("Liquidacion.periodo = $periodo and LiquidacionCuotaNoimputada.socio_id = $socio_id");		
	}
	
	/**
	 * Importe de deuda liquidada por socio por periodo por criterio.
	 * Calcula la deuda liquidada en base a un criterio de filtrado
	 * @param $liquidacion_id
	 * @param $socio_id
	 * @param $periodo
	 * @param $criterio
	 * @return float
	 */
	function __getImporteDeudaLiquidadaBySocioByPeriodoByCriterio($liquidacion_id,$socio_id,$periodo,$criterio,$persona_beneficio_id = 0){
		$sql = "select socio_id,sum(saldo_actual) as saldo_actual
				from liquidacion_cuota_noimputadas where liquidacion_id = $liquidacion_id and periodo_cuota <> '$periodo' and $criterio
				AND socio_id = $socio_id ".($persona_beneficio_id != 0 ? "and persona_beneficio_id = $persona_beneficio_id ": "")."
				group by socio_id";
		$cuotas = $this->query($sql);
		if(empty($cuotas)) return 0;
		if(isset($cuotas[0][0]['saldo_actual'])) return $cuotas[0][0]['saldo_actual'];
		else return 0;
	}
	
	/**
	 * Devuelve el importe debitado correspondiente a la mora de la liquidacion anterior a la pasada
	 * por parametro.
	 * @param $socio_id
	 * @param $persona_beneficio_id
	 * @param $periodoCorte
	 * @param $codigo_organismo
	 * @param $liquidacion_id
	 */
	function getUltimoImporteImputadoBySocio($socio_id,$persona_beneficio_id,$periodoCorte,$codigo_organismo,$liquidacion_id){
		
		$sql = "select liquidacion_id,sum(importe_debitado) as importe_debitado from liquidacion_cuota_noimputadas 
				where socio_id = $socio_id and persona_beneficio_id = $persona_beneficio_id and codigo_organismo = '".$codigo_organismo."'
				and periodo_cuota < '".$periodoCorte."' and liquidacion_id <> $liquidacion_id
				group by liquidacion_id
				order by liquidacion_id desc
				limit 1";
		$datos = $this->query($sql);
		if(empty($datos)) return 0;
		if(isset($datos[0][0]['importe_debitado'])) return $datos[0][0]['importe_debitado'];
		else return 0;
		
	}
	
	
	function getTotalImputadoCuotaSocialDiscriminaCategoriaSocio($periodo){
		$totales = array();
		
		$totales[$periodo] = array();
		
		$sql = "SELECT 
				Liquidacion.codigo_organismo
				FROM liquidacion_cuota_noimputadas AS LiquidacionCuotaNoimputada
				INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = LiquidacionCuotaNoimputada.liquidacion_id)
				INNER JOIN socios AS Socio ON (Socio.id = LiquidacionCuotaNoimputada.socio_id)
				WHERE Liquidacion.periodo = '$periodo'
				AND LiquidacionCuotaNoimputada.tipo_orden_dto = 'CMUTU'
				AND LiquidacionCuotaNoimputada.tipo_producto = 'MUTUPROD0003'
				AND LiquidacionCuotaNoimputada.tipo_cuota = 'MUTUTCUOCSOC'
				AND LiquidacionCuotaNoimputada.imputada = 1
				GROUP BY Liquidacion.codigo_organismo";
		$datos = $this->query($sql);
		
		if(!empty($datos)):
			foreach($datos as $idx => $dato):
				$totales[$periodo]['organismos'][$idx] = $dato['Liquidacion']['codigo_organismo'];
			endforeach;
		endif;
		
		$sql = "SELECT 
				Socio.categoria,
				COUNT(DISTINCT Socio.id) AS cantidad,
				SUM(LiquidacionCuotaNoimputada.importe_debitado) AS importe_debitado
				FROM liquidacion_cuota_noimputadas AS LiquidacionCuotaNoimputada
				INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = LiquidacionCuotaNoimputada.liquidacion_id)
				INNER JOIN socios AS Socio ON (Socio.id = LiquidacionCuotaNoimputada.socio_id)
				WHERE Liquidacion.periodo = '$periodo'
				AND LiquidacionCuotaNoimputada.tipo_orden_dto = 'CMUTU'
				AND LiquidacionCuotaNoimputada.tipo_producto = 'MUTUPROD0003'
				AND LiquidacionCuotaNoimputada.tipo_cuota = 'MUTUTCUOCSOC'
				AND LiquidacionCuotaNoimputada.imputada = 1
				GROUP BY Socio.categoria";
		$datos = $this->query($sql);
		
		if(!empty($datos)):
		
			foreach($datos as $idx => $dato):
			
				$tmp = array();
				$tmp['categoria'] = $dato['Socio']['categoria'];
				$tmp['categoria_desc'] = parent::GlobalDato('concepto_1',$dato['Socio']['categoria']);
				$tmp['cantidad'] = $dato[0]['cantidad'];
				$tmp['importe_debitado'] = $dato[0]['importe_debitado'];
				
				$totales[$periodo]['valores'][$idx] = $tmp;

			endforeach;
		
		endif;
		
		return $totales;
		
	}
	
	/**
	 * Calcula el saldo de una cuota liquidada
	 * @param $cuotaID
	 * @return decimal
	 */
	function calculaSaldoActual($cuotaID){
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();
		return $oCUOTA->getSaldo($cuotaID);
	}
	
	/**
	 * Devuelve los proveedores liquidados
	 * @param $liquidacion_id
	 * @param $soloAdeudado (unicamente los que tienen cuotas sin imputar)
	 * @return array
	 */
	function getProveedoresLiquidados($liquidacion_id,$soloAdeudado = null){

		$conditions = array();
		$conditions['LiquidacionCuotaNoimputada.liquidacion_id'] = $liquidacion_id;
		
		if(!empty($soloAdeudado) && $soloAdeudado) {
			$conditions['LiquidacionCuotaNoimputada.para_imputar'] = 0;
			$conditions['LiquidacionCuotaNoimputada.imputada'] = 0;
		}
		if(!empty($soloAdeudado) && !$soloAdeudado) {
			$conditions['LiquidacionCuotaNoimputada.para_imputar'] = 1;
			$conditions['LiquidacionCuotaNoimputada.imputada'] = 1;
		}
		$this->bindModel(array('belongsTo' => array('Proveedor')));
		
		$proveedores = $this->find('all',array(
										'conditions' => $conditions,
										'fields' => array('LiquidacionCuotaNoimputada.proveedor_id, Proveedor.razon_social,Proveedor.razon_social_resumida'),
										'group' => array('LiquidacionCuotaNoimputada.proveedor_id'),
										'order' => array('Proveedor.razon_social')
		
		));
		return $proveedores;
		
	}
	
	/**
	 * CUOTAS ADEUDADAS POR PROVEEDOR POR LIQUIDACION
	 * 
	 * adrian - 19/01/2012
	 * @param $liquidacion_id
	 * @param $proveedor_id
	 * @param $sociosAlDia
	 */
	function getCuotasAdeudadasByProveedorByLiquidacion($liquidacion_id,$proveedor_id, $sociosAlDia = false){
		
		$sql = "select 
				LiquidacionCuotaNoimputada.id,
				LiquidacionCuotaNoimputada.socio_id,
				GlobalDato_3.concepto_1,
				Persona.documento,
				Persona.apellido,
				Persona.nombre,
				Persona.id, 
				LiquidacionCuotaNoimputada.orden_descuento_id,
				LiquidacionCuotaNoimputada.orden_descuento_cuota_id,
				OrdenDescuento.tipo_orden_dto,
				OrdenDescuento.numero,
				OrdenDescuento.cuotas,
				OrdenDescuentoCuota.nro_cuota,
				GlobalDato_1.concepto_1,
				GlobalDato_2.concepto_1,
				LiquidacionCuotaNoimputada.periodo_cuota,
				LiquidacionCuotaNoimputada.importe,
				LiquidacionCuotaNoimputada.saldo_actual
				from liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada
				inner join liquidaciones as Liquidacion on (Liquidacion.id = LiquidacionCuotaNoimputada.liquidacion_id)
				inner join proveedores as Proveedor on (Proveedor.id = LiquidacionCuotaNoimputada.proveedor_id)
				inner join socios as Socio on (Socio.id = LiquidacionCuotaNoimputada.socio_id)
				inner join personas as Persona on (Persona.id = Socio.persona_id)
				inner join orden_descuento_cuotas as OrdenDescuentoCuota on (OrdenDescuentoCuota.id = LiquidacionCuotaNoimputada.orden_descuento_cuota_id)
				inner join orden_descuentos as OrdenDescuento on (OrdenDescuento.id = LiquidacionCuotaNoimputada.orden_descuento_id)
				inner join global_datos as GlobalDato_1 on (GlobalDato_1.id = LiquidacionCuotaNoimputada.tipo_producto)
				inner join global_datos as GlobalDato_2 on (GlobalDato_2.id = LiquidacionCuotaNoimputada.tipo_cuota)
				inner join global_datos as GlobalDato_3 on (GlobalDato_3.id = Persona.tipo_documento)
				where Liquidacion.id = $liquidacion_id
				and LiquidacionCuotaNoimputada.proveedor_id = $proveedor_id
				and LiquidacionCuotaNoimputada.imputada = 0
				and LiquidacionCuotaNoimputada.periodo_cuota = Liquidacion.periodo ".
				($sociosAlDia ? "and LiquidacionCuotaNoimputada.socio_id not in (select socio_id from liquidacion_cuota_noimputadas as LiquidacionCuotaNoimputada2
				where LiquidacionCuota2.liquidacion_id = Liquidacion.id
				and LiquidacionCuota2.periodo_cuota <> Liquidacion.periodo)" : "") . "
				order by Persona.apellido,Persona.nombre, OrdenDescuento.id, OrdenDescuentoCuota.nro_cuota;";
		
		$datos = $this->query($sql);
		
		$cuotas = array();
		
		if(!empty($datos)):
		
			App::import('Model','Mutual.OrdenDescuentoCuota');
			$oCUOTA = new OrdenDescuentoCuota();		
		
			foreach($datos as $ix => $dato){
				
				$saldoActual = $oCUOTA->getSaldo($dato['LiquidacionCuotaNoimputada']['orden_descuento_cuota_id']);
				
				$dato['LiquidacionCuotaNoimputada']['persona_id'] = $dato['Persona']['id'];
				$dato['LiquidacionCuotaNoimputada']['persona_tdoc'] = $dato['GlobalDato_3']['concepto_1'];
				$dato['LiquidacionCuotaNoimputada']['persona_ndoc'] = $dato['Persona']['documento'];
				$dato['LiquidacionCuotaNoimputada']['persona_apenom'] = $dato['Persona']['apellido'] . ", " . $dato['Persona']['nombre'];
				$dato['LiquidacionCuotaNoimputada']['orden_descuento_tipo_nro'] = $dato['OrdenDescuento']['tipo_orden_dto'] ." #".$dato['OrdenDescuento']['numero'];
				$dato['LiquidacionCuotaNoimputada']['producto_concepto'] = $dato['GlobalDato_1']['concepto_1'] . " - " . $dato['GlobalDato_2']['concepto_1'];
				$dato['LiquidacionCuotaNoimputada']['periodo'] = $dato['LiquidacionCuotaNoimputada']['periodo_cuota'];
				$dato['LiquidacionCuotaNoimputada']['periodo_d'] = parent::periodo($dato['LiquidacionCuotaNoimputada']['periodo_cuota']);
				$dato['LiquidacionCuotaNoimputada']['cuota'] = str_pad($dato['OrdenDescuentoCuota']['nro_cuota'],2,"0",STR_PAD_LEFT) . "/" . str_pad($dato['OrdenDescuento']['cuotas'],2,"0",STR_PAD_LEFT);
				$dato['LiquidacionCuotaNoimputada']['saldo_liquidado'] = $dato['LiquidacionCuotaNoimputada']['saldo_actual'];
				$dato['LiquidacionCuotaNoimputada']['saldo_actual'] = $saldoActual;
//				if($saldoActual != 0)$cuotas[$ix]['LiquidacionCuotaNoimputada'] = $dato['LiquidacionCuotaNoimputada'];
				$cuotas[$ix]['LiquidacionCuotaNoimputada'] = $dato['LiquidacionCuotaNoimputada'];
				
			}
			
		endif;
		
		return $cuotas;
		
	}
	

	/**
	 * Verifica la ultima liquidacion donde figura la cuota y si esta cerrada no imputada 
	 * devuelve el id de la liquidacion para bloquear la cuota
	 * 
	 * @author adrian [14/03/2012]
	 * @param integer $ordenDescuentoCuotaId
	 * @return array
	 */
	function isCuotaOriginalBloqueada($ordenDescuentoCuotaId){
		$ret = array();

		$ret['id'] = 0;
		$ret['liquidacion'] = null;
		
		$sql = "SELECT 
					Liquidacion.id,
					Liquidacion.imputada,
					Liquidacion.cerrada,
					Liquidacion.periodo,
					Liquidacion.codigo_organismo,
					LiquidacionCuotaNoimputada.importe_debitado 
				FROM liquidacion_cuota_noimputadas AS LiquidacionCuotaNoimputada
				INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = LiquidacionCuotaNoimputada.liquidacion_id)
				WHERE LiquidacionCuotaNoimputada.orden_descuento_cuota_id = $ordenDescuentoCuotaId
				ORDER BY Liquidacion.id DESC
				LIMIT 1";
		$datos = $this->query($sql);

		if(empty($datos)) return $ret;
		$datos = $datos[0];
		//SI LA CUOTA ESTA EN UNA LIQUIDACION IMPUTADA NO LA MARCO COMO BLOQUEADA
		if($datos['Liquidacion']['imputada'] == 1) return $ret;
		if($datos['Liquidacion']['cerrada'] == 1){
			$ret['id'] = $datos['Liquidacion']['id'];
			$ret['liquidacion'] = parent::GlobalDato('concepto_1', $datos['Liquidacion']['codigo_organismo']) . " | " . parent::periodo($datos['Liquidacion']['periodo']);
		}
//		if($datos['LiquidacionCuotaNoimputada']['importe_debitado'] != "0"){
//			$ret['id'] = $datos['Liquidacion']['id'];
//			$ret['liquidacion'] = parent::GlobalDato('concepto_1', $datos['Liquidacion']['codigo_organismo']) . " | " . parent::periodo($datos['Liquidacion']['periodo'] . " | PREIMP");
//		}		
		return $ret;
	}

	
	function anularPreImputacion($liquidacion_id){
            App::import('Model','Mutual.Liquidacion');
            $oLQU = new Liquidacion();	            
            if(!$oLQU->reset_valores($liquidacion_id)) return false;
            return $this->updateAll(
                                                    array(
                                'LiquidacionCuotaNoimputada.importe_debitado' => 0,
                                'LiquidacionCuotaNoimputada.liquidacion_intercambio_id' => 0,
                                'LiquidacionCuotaNoimputada.para_imputar' => 0,
                            ),
                                                    array(
                                                            'LiquidacionCuotaNoimputada.liquidacion_id' => $liquidacion_id,
                            'LiquidacionCuotaNoimputada.para_imputar' => 1,
                            'LiquidacionCuotaNoimputada.imputada' => 0,
                                                    )
            );
	}    
    
    function generarLoteRendicion($liquidacion_id,$proveedor_id){
        $lote = array();
        $lote['cabecera'] = "";
        $sql = "select 
                concat(2,
                lpad(ifnull(OrdenDescuento.nro_referencia_proveedor,0),10,0),
                lpad(Persona.documento,8,'0'),
                lpad(LiquidacionCuotaNoimputada.orden_descuento_id,10,0),
                lpad(OrdenDescuentoCuota.nro_cuota,3,0),
                lpad(cast(LiquidacionCuotaNoimputada.importe * 100  as unsigned),12,0),
                lpad(cast(LiquidacionCuotaNoimputada.saldo_actual * 100  as unsigned),12,0),
                lpad(cast(LiquidacionCuotaNoimputada.importe_debitado * 100  as unsigned),12,0),
                substr(rpad(upper(if(LiquidacionCuotaNoimputada.importe_debitado = 0,if(ifnull(LiquidacionSocioRendicion.indica_pago,0) = 1,'PRORRATEO SALDO',ifnull(BancoRendicionCodigo.descripcion,'')),ifnull(BancoRendicionCodigo.descripcion,''))),46,' '),1,46),
                if(LiquidacionCuotaNoimputada.importe_debitado = 0 and ifnull(LiquidacionSocioRendicion.indica_pago,0) = 1,'PRO',lpad(ifnull(LiquidacionSocioRendicion.status,'NRB'),3,'9')),
                if(LiquidacionCuotaNoimputada.importe_debitado = 0,0,ifnull(LiquidacionSocioRendicion.indica_pago,0)) ,
                ifnull(date_format(LiquidacionSocioRendicion.fecha_debito, '%Y%m%d'),'00000000'),
                lpad(cast(LiquidacionCuotaNoimputada.comision_cobranza * 100  as unsigned),12,0),
                lpad(cast((LiquidacionCuotaNoimputada.importe_debitado - LiquidacionCuotaNoimputada.comision_cobranza) * 100  as unsigned),12,0)) as registro,
                ifnull(LiquidacionCuotaNoimputada.importe,0) as importe_cuota,
                ifnull(LiquidacionCuotaNoimputada.saldo_actual,0) as saldo_actual,
                ifnull(LiquidacionCuotaNoimputada.importe_debitado,0) as importe_debitado,
                ifnull(LiquidacionCuotaNoimputada.comision_cobranza,0) as comision_cobranza,
                ifnull(LiquidacionCuotaNoimputada.importe_debitado - LiquidacionCuotaNoimputada.comision_cobranza,0) as neto_proveedor,
                if(LiquidacionCuotaNoimputada.importe_debitado = 0,0,ifnull(LiquidacionSocioRendicion.indica_pago,0)) as indica_pago,
                lpad(ifnull(Proveedor.cuit,0),11,0) as cuit
                from liquidacion_cuota_noimputadas LiquidacionCuota
                inner join orden_descuento_cuotas OrdenDescuentoCuota on (OrdenDescuentoCuota.id = LiquidacionCuotaNoimputada.orden_descuento_cuota_id)
                inner join orden_descuentos OrdenDescuento on (OrdenDescuento.id = LiquidacionCuotaNoimputada.orden_descuento_id)
                left join liquidacion_socio_rendiciones LiquidacionSocioRendicion on (LiquidacionSocioRendicion.liquidacion_id = LiquidacionCuotaNoimputada.liquidacion_id 
                and LiquidacionSocioRendicion.socio_id = LiquidacionCuotaNoimputada.socio_id and ifnull(LiquidacionSocioRendicion.status,'') <> '')
                inner join socios Socio on (Socio.id = LiquidacionCuotaNoimputada.socio_id)
                inner join personas Persona on (Persona.id = Socio.persona_id)
                inner join global_datos TipoDocumento on (TipoDocumento.id = Persona.tipo_documento)
                inner join banco_rendicion_codigos BancoRendicionCodigo on (BancoRendicionCodigo.banco_id = LiquidacionSocioRendicion.banco_intercambio 
                and BancoRendicionCodigo.codigo = LiquidacionSocioRendicion.status)
                inner join proveedores Proveedor on (Proveedor.id = LiquidacionCuotaNoimputada.proveedor_id)
                where LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id and LiquidacionCuotaNoimputada.proveedor_id = $proveedor_id
                group by LiquidacionCuotaNoimputada.socio_id,LiquidacionCuotaNoimputada.orden_descuento_cuota_id
                order by Persona.apellido,Persona.nombre,LiquidacionCuotaNoimputada.orden_descuento_id,OrdenDescuentoCuota.nro_cuota;";
        $datos = $this->query($sql);
        
        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
        $CALC_IVA = false;
        if(isset($INI_FILE['general']['discrimina_iva']) && $INI_FILE['general']['discrimina_iva'] != 0){
            $CALC_IVA = $INI_FILE['general']['discrimina_iva'];
        }         
        
        $lote['detalle'] = Set::extract("{n}.0.registro",$datos);
        
        $cuit = Set::extract("{n}.0.cuit",$datos);
        $cuit = $cuit[0];

        $importeCuota = array_sum(Set::extract("{n}.0.importe_cuota",$datos));
        $importeCuota = round($importeCuota,2);
        $saldoActual = array_sum(Set::extract("{n}.0.saldo_actual",$datos));
        $saldoActual = round($saldoActual,2);
        $importeDebitado = array_sum(Set::extract("{n}.0.importe_debitado",$datos));
        $importeDebitado = round($importeDebitado,2);
        $comisionCobranza = array_sum(Set::extract("{n}.0.comision_cobranza",$datos));
        $comisionCobranza = round($comisionCobranza,2);
        $iva = 0;
        if($CALC_IVA != 0){
            $iva = round($comisionCobranza * ($CALC_IVA/100),2);
            $iva = round($iva,2);
        }
        
        $NetoProveedor = $importeDebitado - $comisionCobranza - $iva;
        $indicaPago = array_sum(Set::extract("{n}.0.indica_pago",$datos));
        
        $lote['pie'] = "";
        $lote['pie'] .= "3" . str_pad(count($lote['detalle']),10,0,STR_PAD_LEFT);
        $lote['pie'] .= str_pad($indicaPago,10,0,STR_PAD_LEFT);
        $lote['pie'] .= str_pad(number_format($importeCuota * 100,0,"",""),12,0,STR_PAD_LEFT);
        $lote['pie'] .= str_pad(number_format($saldoActual * 100,0,"",""),12,0,STR_PAD_LEFT);
        $lote['pie'] .= str_pad(number_format($importeDebitado * 100,0,"",""),12,0,STR_PAD_LEFT);
        $lote['pie'] .= str_pad(number_format($comisionCobranza * 100,0,"",""),12,0,STR_PAD_LEFT);
        $lote['pie'] .= str_pad(number_format($iva * 100,0,"",""),12,0,STR_PAD_LEFT);
        $lote['pie'] .= str_pad(number_format($NetoProveedor * 100,0,"",""),12,0,STR_PAD_LEFT);
        
        $lote['pie'] = str_pad($lote['pie'],150,' ',STR_PAD_RIGHT);
        
        $lote['cabecera'] .= "1";
        $lote['cabecera'] .= trim($cuit);
        $lote['cabecera'] .= date('Ymd');
        $lote['cabecera'] = str_pad($lote['cabecera'],150,' ',STR_PAD_RIGHT);
        
        $lote['urgencias'] = array();
        $sql = "select 
                concat(
                lpad(Persona.documento,9,'0'),
                ifnull(Persona.sexo,'M'),
                lpad(ifnull(Liquidacion.periodo,'000000'),6,0),
                lpad(round(LiquidacionCuotaNoimputada.importe_debitado,2),7,0)) as registro
                from liquidacion_cuota_noimputadas LiquidacionCuota
                inner join liquidaciones Liquidacion on (Liquidacion.id = LiquidacionCuotaNoimputada.liquidacion_id)
                inner join socios Socio on (Socio.id = LiquidacionCuotaNoimputada.socio_id)
                inner join personas Persona on (Persona.id = Socio.persona_id)
                inner join proveedores Proveedor on (Proveedor.id = LiquidacionCuotaNoimputada.proveedor_id)
                where LiquidacionCuotaNoimputada.liquidacion_id = $liquidacion_id and LiquidacionCuotaNoimputada.proveedor_id = $proveedor_id
                and LiquidacionCuotaNoimputada.importe_debitado > 0
                group by LiquidacionCuotaNoimputada.socio_id,LiquidacionCuotaNoimputada.orden_descuento_cuota_id;";
        $datos = $this->query($sql);
        $lote['urgencias'] = Set::extract("{n}.0.registro",$datos);
        return $lote;
        
    }	
    
    
        function get_detalle_mora_cuota($liquidacion_id,$nro_cuota = 1,$returnCantidad = FALSE){

            $sql = "select 
                    p.documento
                    ,p.apellido
                    ,p.nombre
                    ,p.telefono_fijo
                    ,p.telefono_movil
                    ,p.telefono_referencia                    
                    ,p.telefono_fijo_c
                    ,p.telefono_fijo_n
                    ,p.telefono_movil_c
                    ,p.telefono_movil_n
                    ,p.telefono_referencia_c
                    ,p.telefono_referencia_n                    
                    ,o.id
                    ,o.tipo_orden_dto
                    ,o.numero
                    ,pr.razon_social
                    ,pr.razon_social_resumida
                    ,tp.concepto_1
                    ,tc.concepto_1
                    ,e.concepto_1
                    ,c.periodo
                    ,c.nro_cuota
                    ,c.importe 
                    ,lc.saldo_actual
                    ,lc.importe_debitado
                    ,lc.saldo_actual-lc.importe_debitado AS saldo_cuota                    
                    from liquidacion_cuota_noimputadas lc
                    inner join liquidacion_socio_rendiciones lsr on lsr.liquidacion_id = lc.liquidacion_id
                    and lsr.socio_id = lc.socio_id                    
                    inner join orden_descuentos o on o.id = lc.orden_descuento_id
                    inner join liquidaciones l on l.id = lc.liquidacion_id
                    inner join orden_descuento_cuotas c on c.id = lc.orden_descuento_cuota_id
                    inner join global_datos tp on tp.id = c.tipo_producto
                    inner join global_datos tc on tc.id = c.tipo_cuota
                    inner join proveedores pr on pr.id = c.proveedor_id
                    inner join persona_beneficios b on b.id = c.persona_beneficio_id
                    inner join global_datos e on e.id = b.codigo_empresa
                    inner join socios s on s.id = o.socio_id
                    inner join personas p on p.id = s.persona_id
                    where lc.liquidacion_id = $liquidacion_id
                    and o.permanente = 0
                    and c.nro_cuota = $nro_cuota
                    and c.periodo = l.periodo
                    and lc.importe_debitado < lc.saldo_actual
                    group by lc.orden_descuento_id
                    order by p.apellido,p.nombre,o.id,o.numero;";
            
            $datos = $this->query($sql);
            if($returnCantidad){
                return count($datos);
            }else{
                return $datos;
            }
            
            
        }
        
        function get_detalle_mora_temprana($liquidacion_id,$returnCantidad = FALSE){
            
            $sql = "select 
                    p.documento
                    ,p.apellido
                    ,p.nombre
                    ,p.telefono_fijo
                    ,p.telefono_movil
                    ,p.telefono_referencia                    
                    ,p.telefono_fijo_c
                    ,p.telefono_fijo_n
                    ,p.telefono_movil_c
                    ,p.telefono_movil_n
                    ,p.telefono_referencia_c
                    ,p.telefono_referencia_n                    
                    ,o.id
                    ,o.tipo_orden_dto
                    ,o.numero
                    ,pr.razon_social
                    ,pr.razon_social_resumida
                    ,tp.concepto_1
                    ,tc.concepto_1
                    ,e.concepto_1
                    ,c.periodo
                    ,c.nro_cuota
                    ,c.importe
                    ,lc.saldo_actual
                    ,lc.importe_debitado
                    ,lc.saldo_actual-lc.importe_debitado AS saldo_cuota
                    ,
                    ifnull((
                    select sum(importe) from orden_descuento_cuotas c1
                    where c1.orden_descuento_id = o.id and c1.periodo < c.periodo
                    and c1.estado not in ('B','C')
                    ),0) as devengado
                    ,
                    ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
                    inner join orden_descuento_cuotas c2 on c2.id = cc.orden_descuento_cuota_id
                    inner join orden_descuentos o1 on o1.id = c2.orden_descuento_id
                    where o1.id = o.id and c2.periodo < l.periodo and cc.periodo_cobro < l.periodo
                    ),0) as pagado
                    ,
                    ifnull((
                    select sum(importe) from orden_descuento_cuotas c1
                    where c1.orden_descuento_id = o.id and c1.periodo < c.periodo
                    and c1.estado not in ('B','C')
                    ),0)
                    -
                    ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
                    inner join orden_descuento_cuotas c2 on c2.id = cc.orden_descuento_cuota_id
                    inner join orden_descuentos o1 on o1.id = c2.orden_descuento_id
                    where o1.id = o.id and c2.periodo < l.periodo  and cc.periodo_cobro < l.periodo
                    ),0) as saldo
                    from liquidacion_cuota_noimputadas lc
                    inner join liquidacion_socio_rendiciones lsr on lsr.liquidacion_id = lc.liquidacion_id
                    and lsr.socio_id = lc.socio_id                     
                    inner join orden_descuentos o on o.id = lc.orden_descuento_id
                    inner join liquidaciones l on l.id = lc.liquidacion_id
                    inner join orden_descuento_cuotas c on c.id = lc.orden_descuento_cuota_id
                    inner join global_datos tp on tp.id = c.tipo_producto
                    inner join global_datos tc on tc.id = c.tipo_cuota
                    inner join proveedores pr on pr.id = c.proveedor_id
                    inner join persona_beneficios b on b.id = c.persona_beneficio_id
                    inner join global_datos e on e.id = b.codigo_empresa
                    inner join socios s on s.id = o.socio_id
                    inner join personas p on p.id = s.persona_id
                    where lc.liquidacion_id = $liquidacion_id
                    and o.permanente = 0
                    and c.nro_cuota > 1
                    and c.periodo = l.periodo
                    and lc.importe_debitado < lc.saldo_actual
                    group by lc.orden_descuento_id
                    having saldo = 0
                    order by p.apellido,p.nombre;";
            
            $datos = $this->query($sql);
            if($returnCantidad){
                return count($datos);
            }else{
                return $datos;
            }            
            
        }
        
}
?>
