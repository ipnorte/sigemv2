<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */

class OrdenDescuentoCobroCuota extends MutualAppModel{
	
	var $name = 'OrdenDescuentoCobroCuota';
	
	
	function grabarPago($cuota){
		$this->id = 0;
		if(parent::save($cuota)){
			//marco las cuotas como pagadas
			App::import('Model','Mutual.OrdenDescuentoCuota');
			$oCuota = new OrdenDescuentoCuota();
			return $oCuota->marcarPagada($cuota['OrdenDescuentoCobroCuota']['orden_descuento_cuota_id']);
		}else{
			return false;
			
		}
	}
	
	/**
	 * Devuelve un Cobro con los datos seteados de la cuota
	 * @param $id
	 * @return unknown_type
	 */
	function getCobroCuota($id,$cargaCuota=true){
		$cobroCuota = $this->read(null,$id);
		if($cargaCuota):
			App::import('model','Mutual.OrdenDescuentoCuota');
			$oCUOTA = new OrdenDescuentoCuota();
			$cuota = $oCUOTA->getCuota($cobroCuota['OrdenDescuentoCobroCuota']['orden_descuento_cuota_id'],false);
			$cobroCuota['OrdenDescuentoCobroCuota']['cuota'] = $cuota['OrdenDescuentoCuota'];
		endif;
		return $cobroCuota;
	}
	
	
	function getCobrosByCuota($orden_descuento_cuota_id){
		$this->bindModel(array('belongsTo' => array('OrdenDescuentoCobro')));
		$pagos = $this->find('all',array('conditions' => array('OrdenDescuentoCobroCuota.orden_descuento_cuota_id' => $orden_descuento_cuota_id),'order' => array('OrdenDescuentoCobro.fecha ASC')));
		if(empty($pagos)) return null;
		foreach($pagos as $id => $pago){
			$pago['OrdenDescuentoCobroCuota']['nro_recibo'] = $pago['OrdenDescuentoCobro']['nro_recibo'];
			$pago['OrdenDescuentoCobroCuota']['cancelacion_orden_id'] = $pago['OrdenDescuentoCobro']['cancelacion_orden_id'];
			$pago['OrdenDescuentoCobroCuota']['fecha_cobro'] = $pago['OrdenDescuentoCobro']['fecha'];
			$pago['OrdenDescuentoCobroCuota']['tipo_cobro'] = $pago['OrdenDescuentoCobro']['tipo_cobro'];
			$pago['OrdenDescuentoCobroCuota']['tipo_cobro_desc'] = parent::GlobalDato('concepto_1',$pago['OrdenDescuentoCobro']['tipo_cobro']);
			$pagos[$id] = $pago;
		}
		$pagos = Set::extract('/OrdenDescuentoCobroCuota',$pagos);
		return $pagos;
	}
	
	function getCobrosByOrdenDescuento($orden_descuento_id){
		
//		$this->bindModel(array('belongsTo' => array('OrdenDescuentoCobro')));
//		$pagos = $this->find('all',array('conditions' => array('OrdenDescuentoCobroCuota.orden_descuento_cuota_id' => $orden_descuento_cuota_id),'order' => array('OrdenDescuentoCobro.fecha ASC')));
//		if(empty($pagos)) return null;
//		foreach($pagos as $id => $pago){
//			$pago['OrdenDescuentoCobroCuota']['nro_recibo'] = $pago['OrdenDescuentoCobro']['nro_recibo'];
//			$pago['OrdenDescuentoCobroCuota']['cancelacion_orden_id'] = $pago['OrdenDescuentoCobro']['cancelacion_orden_id'];
//			$pago['OrdenDescuentoCobroCuota']['fecha_cobro'] = $pago['OrdenDescuentoCobro']['fecha'];
//			$pago['OrdenDescuentoCobroCuota']['tipo_cobro'] = $pago['OrdenDescuentoCobro']['tipo_cobro'];
//			$pago['OrdenDescuentoCobroCuota']['tipo_cobro_desc'] = parent::GlobalDato('concepto_1',$pago['OrdenDescuentoCobro']['tipo_cobro']);
//			$pagos[$id] = $pago;
//		}
//		$pagos = Set::extract('/OrdenDescuentoCobroCuota',$pagos);
//		return $pagos;
	}	
	
	
	
	/**
	 * devuelve el id del cobro al cual esta asociado el detalle
	 * @param $id
	 */
	function getOrdenCobro($id){
		$cobroCuota = $this->read('orden_descuento_cobro_id',$id);
		return $cobroCuota['OrdenDescuentoCobroCuota']['orden_descuento_cobro_id'];
	}
	
	
	
	function getMontoPagoByCuota($orden_descuento_cuota_id, $periodoCorte = null){
		if(empty($periodoCorte)) $periodoCorte = date('Ym');
		$conditions = array();
		$conditions['OrdenDescuentoCobroCuota.orden_descuento_cuota_id'] = $orden_descuento_cuota_id;
		if(!empty($periodoCorte)) $conditions['OrdenDescuentoCobroCuota.periodo_cobro <='] = $periodoCorte;
		
		$pagos = $this->find('all',array('conditions' => $conditions,'fields' => array("SUM(OrdenDescuentoCobroCuota.importe) as pago"),'group' => array('OrdenDescuentoCobroCuota.orden_descuento_cuota_id')));
		if(count($pagos)!=0) return $pagos[0][0]['pago'];
		else return 0;
	}	
	
	/**
	 * Get Monto Pago por Orden de Cobro
	 * @param $orden_descuento_cobro_id
	 * @return unknown_type
	 */
	function getMontoPagoByOrdenCobro($orden_descuento_cobro_id){
		$this->unbindModel(array('belongsTo' => array('OrdenDescuentoCobro')));
		$pagos = $this->find('all',array('conditions' => array('OrdenDescuentoCobroCuota.orden_descuento_cobro_id' => $orden_descuento_cobro_id),'fields' => array("SUM(OrdenDescuentoCobroCuota.importe) as pago"),'group' => array('OrdenDescuentoCobroCuota.orden_descuento_cobro_id')));
		if(count($pagos)!=0) return $pagos[0][0]['pago'];
		else return 0;
	}		
	
	function getMontoPagoByPeriodo($socio_id,$periodo){
		$this->bindModel(array('belongsTo' => array('OrdenDescuentoCuota')));
		$pagos = $this->find('all',array('conditions' => array('OrdenDescuentoCuota.socio_id' => $socio_id,'OrdenDescuentoCuota.periodo' => $periodo),'fields' => array("SUM(OrdenDescuentoCobroCuota.importe) as pago"),'group' => array('OrdenDescuentoCobroCuota.orden_descuento_cuota_id')));
		if(count($pagos)!=0) return $pagos[0][0]['pago'];
		else return 0;
	}
	
	function getFechaUltimoPago($orden_descuento_cuota_id){
		$this->bindModel(array('belongsTo' => array('OrdenDescuentoCobro')));
		$pagos = $this->find('all',array('conditions' => array('OrdenDescuentoCobroCuota.orden_descuento_cuota_id' => $orden_descuento_cuota_id),'fields' => array("OrdenDescuentoCobro.fecha"),'order' => array('OrdenDescuentoCobro.fecha DESC'),'limit' => 1));
		return (isset($pagos[0]['OrdenDescuentoCobro']['fecha']) ? $pagos[0]['OrdenDescuentoCobro']['fecha'] : null);
	}
	
	
	function getDetalle($orden_descuento_cobro_id){
		$cuotas = $this->find('all',array('conditions' => array('OrdenDescuentoCobroCuota.orden_descuento_cobro_id' => $orden_descuento_cobro_id)));
		return $cuotas;
	}
	
	
	
	function getCuotasBySocioByTipoPago($socio_id,$tipo_cobro="MUTUTCOBCAJA"){
		
		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();		
		
		$this->bindModel(array('belongsTo' => array('OrdenDescuentoCobro')));
		$cobros = $this->find('all',array('conditions' => array('OrdenDescuentoCobro.socio_id' => $socio_id,'OrdenDescuentoCobro.tipo_cobro' => $tipo_cobro,'OrdenDescuentoCobroCuota.reversado' => 0),'order' => array('OrdenDescuentoCobro.periodo_cobro DESC')));
		foreach($cobros as $idx => $cobro):
		
			$cobro['OrdenDescuentoCobroCuota']['fecha'] = $cobro['OrdenDescuentoCobro']['fecha'];
			$cobro['OrdenDescuentoCobroCuota']['tipo_cobro'] = $cobro['OrdenDescuentoCobro']['tipo_cobro'];
		
			$cuota = $oCUOTA->getCuota($cobro['OrdenDescuentoCobroCuota']['orden_descuento_cuota_id'],false);
			$cobro['OrdenDescuentoCobroCuota']['cuota'] = $cuota['OrdenDescuentoCuota'];
			
			$cobros[$idx] = $cobro;
			
		endforeach;
		
		$cobros = Set::extract("/OrdenDescuentoCobroCuota",$cobros);
		
		return $cobros;
	}
	
	/**
	 * Devuelve las cuotas reversadas por un socio
	 * @param $socio_id
	 * @return unknown_type
	 */
	function getCuotasReversadasBySocio($socio_id){
		
		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();		
		
		$this->bindModel(array('belongsTo' => array('OrdenDescuentoCobro')));
		$cobros = $this->find('all',array('conditions' => array('OrdenDescuentoCobro.socio_id' => $socio_id,'OrdenDescuentoCobroCuota.reversado' => 1),'order' => array('OrdenDescuentoCobro.periodo_cobro DESC')));
		foreach($cobros as $idx => $cobro):
		
			$cobro['OrdenDescuentoCobroCuota']['fecha'] = $cobro['OrdenDescuentoCobro']['fecha'];
			$cobro['OrdenDescuentoCobroCuota']['tipo_cobro'] = $cobro['OrdenDescuentoCobro']['tipo_cobro'];
		
			$cuota = $oCUOTA->getCuota($cobro['OrdenDescuentoCobroCuota']['orden_descuento_cuota_id'],false);
			$cobro['OrdenDescuentoCobroCuota']['cuota'] = $cuota['OrdenDescuentoCuota'];
			
			$cobros[$idx] = $cobro;
			
		endforeach;
		
		$cobros = Set::extract("/OrdenDescuentoCobroCuota",$cobros);
		
		return $cobros;
	}	
	
	
	function getCuotasReversadasBySocioByPeriodo($socio_id,$periodo=null){
		
		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();

		$this->bindModel(array('belongsTo' => array('OrdenDescuentoCobro')));
		
		
		$periodos = $this->find('all',array('conditions' => array('OrdenDescuentoCobro.socio_id' => $socio_id,'OrdenDescuentoCobroCuota.reversado' => 1),'fields' => array('OrdenDescuentoCobroCuota.periodo_proveedor_reverso'),'group' => array('OrdenDescuentoCobroCuota.periodo_proveedor_reverso'),'order' => array('OrdenDescuentoCobro.periodo_cobro DESC')));
		$periodos = Set::extract("/OrdenDescuentoCobroCuota/periodo_proveedor_reverso",$periodos);
		
		if(empty($periodos)) return null;
		
		$reversos = array();
		
		foreach($periodos as $periodo):
		
			$reversos[$periodo] = array();
			$this->bindModel(array('belongsTo' => array('OrdenDescuentoCobro')));
		
			$cobros = $this->find('all',array('conditions' => array('OrdenDescuentoCobro.socio_id' => $socio_id,'OrdenDescuentoCobroCuota.reversado' => 1,'OrdenDescuentoCobroCuota.periodo_proveedor_reverso' => $periodo),'order' => array('OrdenDescuentoCobro.periodo_cobro DESC')));
			
			$importeReversado = 0;
			$importeCobrado = 0;
			
			foreach($cobros as $idx => $cobro):
			
				$cobro['OrdenDescuentoCobroCuota']['fecha'] = $cobro['OrdenDescuentoCobro']['fecha'];
				$cobro['OrdenDescuentoCobroCuota']['tipo_cobro'] = $cobro['OrdenDescuentoCobro']['tipo_cobro'];
			
				$importeReversado += $cobro['OrdenDescuentoCobroCuota']['importe_reversado'];
				$importeCobrado += $cobro['OrdenDescuentoCobroCuota']['importe'];
				
				$cuota = $oCUOTA->getCuota($cobro['OrdenDescuentoCobroCuota']['orden_descuento_cuota_id'],false);
				
				$cobro['OrdenDescuentoCobroCuota']['organismo'] = $cuota['OrdenDescuentoCuota']['organismo'];
				$cobro['OrdenDescuentoCobroCuota']['orden_descuento_id'] = $cuota['OrdenDescuentoCuota']['orden_descuento_id'];
				$cobro['OrdenDescuentoCobroCuota']['tipo_nro'] = $cuota['OrdenDescuentoCuota']['tipo_nro'];
				$cobro['OrdenDescuentoCobroCuota']['proveedor_producto'] = $cuota['OrdenDescuentoCuota']['proveedor_producto'];
				$cobro['OrdenDescuentoCobroCuota']['cuota_cuotas'] = $cuota['OrdenDescuentoCuota']['cuota'];
				$cobro['OrdenDescuentoCobroCuota']['periodo'] = $cuota['OrdenDescuentoCuota']['periodo'];
				$cobro['OrdenDescuentoCobroCuota']['socio_id'] = $cuota['OrdenDescuentoCuota']['socio_id'];
				$cobro['OrdenDescuentoCobroCuota']['cuota'] = $cuota['OrdenDescuentoCuota'];
				
				$cobros[$idx] = $cobro;
				
			endforeach;
			
			$cobros = Set::extract("/OrdenDescuentoCobroCuota",$cobros);
			$reversos[$periodo]['total_cobrado'] = $importeCobrado;
			$reversos[$periodo]['total_reversado'] = $importeReversado;
			$reversos[$periodo]['cuotas'] = $cobros;
			
		
		endforeach;
		
		return $reversos;
		
	}
	

	/**
	 * Reversa un cobro
	 * @param $CobroCuotaid
	 * @param $periodoProveedor
	 * @return unknown_type
	 */
	function reversarCobro($CobroCuotaid,$periodoProveedor,$importeReversado=null, $movBancoId = 0){
		
	    parent::begin();
	    
		$cobro = $this->getCobroCuota($CobroCuotaid,false);
		
		if($cobro['OrdenDescuentoCobroCuota']['reversado'] == 1 && $cobro['OrdenDescuentoCobroCuota']['importe'] == $cobro['OrdenDescuentoCobroCuota']['importe_reversado']) {
		    parent::rollback();
		    return false;
		}
		
		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();
		
		$cuota = $oCUOTA->read(null,$cobro['OrdenDescuentoCobroCuota']['orden_descuento_cuota_id']);
		
                $saldos = $oCUOTA->getSaldosByOrdenDto($cuota['OrdenDescuentoCuota']['orden_descuento_id']);
                $ESTADO = 'A';
                if($saldos['saldo'] == 0 && $saldos['baja_cuotas'] != 0) $ESTADO = 'B';                

		if(empty($importeReversado)) $importeReversado = $cobro['OrdenDescuentoCobroCuota']['importe'];
		
		//creo las notas de debito
		$nvaCuota = array('OrdenDescuentoCuota' => array(
						'id' => 0,
						'orden_descuento_id' => $cuota['OrdenDescuentoCuota']['orden_descuento_id'],
						'persona_beneficio_id' => $cuota['OrdenDescuentoCuota']['persona_beneficio_id'],
						'socio_id' => $cuota['OrdenDescuentoCuota']['socio_id'],
						'tipo_orden_dto' => $cuota['OrdenDescuentoCuota']['tipo_orden_dto'],
						'tipo_producto' => $cuota['OrdenDescuentoCuota']['tipo_producto'],
						'periodo' => $cuota['OrdenDescuentoCuota']['periodo'],
						'nro_cuota' => $cuota['OrdenDescuentoCuota']['nro_cuota'],
						'tipo_cuota' => 'MUTUTCUODERE',
						'estado' => $ESTADO,
						'situacion' => 'MUTUSICUMUTU',
						'importe' => $importeReversado,
						'proveedor_id' => $cobro['OrdenDescuentoCobroCuota']['proveedor_id'],
						'vencimiento' => (empty($cuota['OrdenDescuentoCuota']['vencimiento']) ? date('Y-m-d') : $cuota['OrdenDescuentoCuota']['vencimiento']),
						'vencimiento_proveedor' => (empty($cuota['OrdenDescuentoCuota']['vencimiento_proveedor']) ? date('Y-m-d') : $cuota['OrdenDescuentoCuota']['vencimiento_proveedor'])
					));
		if(!$oCUOTA->save($nvaCuota)) {parent::rollback(); return false;}

		$cobro['OrdenDescuentoCobroCuota']['reversado'] = 1;
		$cobro['OrdenDescuentoCobroCuota']['importe_reversado'] = $importeReversado;
		$cobro['OrdenDescuentoCobroCuota']['periodo_proveedor_reverso'] = $periodoProveedor;
		$cobro['OrdenDescuentoCobroCuota']['fecha_reverso'] = date('Y-m-d');
		$cobro['OrdenDescuentoCobroCuota']['debito_reverso_id'] = $oCUOTA->getLastInsertID();

		// Agregado Gustavo (Movimiento de caja y banco del Reverso)
		$cobro['OrdenDescuentoCobroCuota']['banco_cuenta_movimiento_id'] = $movBancoId;
		
		if(!empty($_SESSION['NAME_USER_LOGON_SIGEM']))$cobro['OrdenDescuentoCobroCuota']['usuario_reverso'] = $_SESSION['NAME_USER_LOGON_SIGEM'];
		
		if(!$this->save($cobro)) {
		    parent::rollback();
		}
		
		return parent::commit();
		
	}
	

	/**
	 * Total Reversado por Proveedor por Liquidacion
	 * @param unknown_type $proveedor_id
	 * @param unknown_type $liquidacion_id
	 * @param unknown_type $tipoProducto
	 * @param unknown_type $tipoCuota
	 * @param unknown_type $socio_id
	 * @return number
	 */
	function getTotalReversoByProveedorByLiquidacion($proveedor_id,$liquidacion_id,$tipoProducto=0,$tipoCuota=0,$socio_id=0){

		App::import('model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();

		$liquidacion = $oLQ->read(null,$liquidacion_id);
		$sql = "	select ifnull(sum(OrdenDescuentoCobroCuota.importe_reversado),0) as importe_reversado 
					from orden_descuento_cobro_cuotas as OrdenDescuentoCobroCuota
					inner join orden_descuento_cuotas as OrdenDescuentoCuota 
					on (OrdenDescuentoCobroCuota.orden_descuento_cuota_id = OrdenDescuentoCuota.id)
					inner join persona_beneficios as PersonaBeneficio on (OrdenDescuentoCuota.persona_beneficio_id = PersonaBeneficio.id)
					where ".(!empty($proveedor_id) ? "OrdenDescuentoCobroCuota.proveedor_id IN ($proveedor_id) AND " : "")."
					".($socio_id != '0' ? " OrdenDescuentoCuota.socio_id = $socio_id AND " : "")."
					".($tipoProducto != '0' ? " OrdenDescuentoCuota.tipo_producto = '$tipoProducto' AND " : "")."
					".($tipoCuota != '0' ? " OrdenDescuentoCuota.tipo_cuota = '$tipoCuota' AND" : "")."
					OrdenDescuentoCobroCuota.reversado = 1 AND
					OrdenDescuentoCobroCuota.periodo_proveedor_reverso = '".$liquidacion['Liquidacion']['periodo']."' AND
					PersonaBeneficio.codigo_beneficio = '".$liquidacion['Liquidacion']['codigo_organismo']."';";
		$reverso = $this->query($sql);
		if(empty($reverso)) return 0;
		return (!empty($reverso[0][0]['importe_reversado']) ? $reverso[0][0]['importe_reversado'] : 0);
		
	}
	
	function getMayorComisionReversoByProveedorByLiquidacion($proveedor_id,$liquidacion_id,$tipoProducto=0,$tipoCuota=0,$socio_id=0){

		App::import('model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();

		$liquidacion = $oLQ->read(null,$liquidacion_id);
		$sql = "	select max(OrdenDescuentoCobroCuota.alicuota_comision_cobranza) as alicuota_comision_cobranza 
					from orden_descuento_cobro_cuotas as OrdenDescuentoCobroCuota
					inner join orden_descuento_cuotas as OrdenDescuentoCuota 
					on (OrdenDescuentoCobroCuota.orden_descuento_cuota_id = OrdenDescuentoCuota.id)
					inner join persona_beneficios as PersonaBeneficio on (OrdenDescuentoCuota.persona_beneficio_id = PersonaBeneficio.id)
					where OrdenDescuentoCobroCuota.proveedor_id = $proveedor_id
					".($socio_id != '0' ? "and OrdenDescuentoCuota.socio_id = $socio_id" : "")."
					".($tipoProducto != '0' ? "and OrdenDescuentoCuota.tipo_producto = '$tipoProducto'" : "")."
					".($tipoCuota != '0' ? "and OrdenDescuentoCuota.tipo_cuota = '$tipoCuota'" : "")."
					and OrdenDescuentoCobroCuota.reversado = 1 
					and OrdenDescuentoCobroCuota.periodo_proveedor_reverso = '".$liquidacion['Liquidacion']['periodo']."'
					and PersonaBeneficio.codigo_beneficio = '".$liquidacion['Liquidacion']['codigo_organismo']."'";
		
		$reverso = $this->query($sql);
		if(empty($reverso)) return 0;
		return (!empty($reverso[0][0]['alicuota_comision_cobranza']) ? $reverso[0][0]['alicuota_comision_cobranza'] : 0);
		
	}	
	
	
	/**
	 * Devuelve el total de las comisiones por cobranza que fueron aplicadas a los pagos reversados
	 * @param unknown_type $proveedor_id
	 * @param unknown_type $liquidacion_id
	 * @return unknown_type
	 */
	function getTotalComisionAplicadaReverso($proveedor_id,$liquidacion_id){
		
		App::import('model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();

		$liquidacion = $oLQ->read(null,$liquidacion_id);
		// $sql = "	select sum(OrdenDescuentoCobroCuota.comision_cobranza) as comision_cobranza 
		// 			from orden_descuento_cobro_cuotas as OrdenDescuentoCobroCuota
		// 			INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON (OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id)
		// 			INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
		// 			where OrdenDescuentoCobroCuota.proveedor_id = $proveedor_id AND 
		// 			OrdenDescuentoCobroCuota.reversado = 1 AND
		// 			OrdenDescuentoCobroCuota.periodo_proveedor_reverso = '".$liquidacion['Liquidacion']['periodo']."'
		// 			AND PersonaBeneficio.codigo_beneficio = '".$liquidacion['Liquidacion']['codigo_organismo']."';";

		$sql = "	select sum(round((OrdenDescuentoCobroCuota.importe_reversado * (OrdenDescuentoCobroCuota.alicuota_comision_cobranza / 100)),2)) as comision_cobranza 
					from orden_descuento_cobro_cuotas as OrdenDescuentoCobroCuota
					INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON (OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id)
					INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
					where OrdenDescuentoCobroCuota.proveedor_id = $proveedor_id AND 
					OrdenDescuentoCobroCuota.reversado = 1 AND
					OrdenDescuentoCobroCuota.periodo_proveedor_reverso = '".$liquidacion['Liquidacion']['periodo']."'
					AND PersonaBeneficio.codigo_beneficio = '".$liquidacion['Liquidacion']['codigo_organismo']."';";					
		
		$reverso = $this->query($sql);
		if(empty($reverso)) return 0;
		return (!empty($reverso[0][0]['comision_cobranza']) ? $reverso[0][0]['comision_cobranza'] : 0);		
		
//		$totalReversado = $this->getTotalReversoByProveedorByLiquidacion($proveedor_id,$liquidacion_id);
//		
//		$reversos = $this->reversosByProveedorByLiquidacion($proveedor_id,$liquidacion_id);
//		
//		if(empty($reversos)) return 0;
//		
//		App::import('Model','Mutual.Liquidacion');
//		$oLQ = new Liquidacion();
//		
//		App::import('Model','Proveedores.ProveedorComision');
//		$oCOM = new ProveedorComision();		
//
//		$liquidacion = $oLQ->read(null,$liquidacion_id);
//
//		$ACU_COMISION = 0;
//		$impoReversado = 0;
//		$comision = 0;
//		
//		foreach($reversos as $reverso):
//			$comision = $oCOM->getComision($liquidacion['Liquidacion']['codigo_organismo'],$proveedor_id,$reverso['OrdenDescuentoCobroCuota']['cuota']['tipo_producto'],$reverso['OrdenDescuentoCobroCuota']['cuota']['tipo_cuota']);
//			$impoReversado = $reverso['OrdenDescuentoCobroCuota']['importe_reversado'];
//			$ACU_COMISION -= $impoReversado * $comision / 100;
//		endforeach;
//		
//		if($ACU_COMISION != 0) return $ACU_COMISION;
//		else return 0;
		
	}
	
	/**
	 * Devuelve los pagos reversos para un proveedor y liquidacion
	 * @param unknown_type $proveedor_id
	 * @param unknown_type $liquidacion_id
	 * @param unknown_type $tipoProducto
	 * @param unknown_type $tipoCuota
	 * @param unknown_type $socio_id
	 * @return unknown_type
	 */
	function reversosByProveedorByLiquidacion($proveedor_id,$liquidacion_id,$tipoProducto=0,$tipoCuota=0,$socio_id=0,$periodo = NULL){
		
            $periodo_reverso = $periodo;
            $codigo_organismo = NULL;
            
            if(!empty($liquidacion_id)){
		App::import('model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();
		$liquidacion = $oLQ->read(null,$liquidacion_id);
                $periodo_reverso = $liquidacion['Liquidacion']['periodo'];
                $codigo_organismo = $liquidacion['Liquidacion']['codigo_organismo'];
                
            }
 		
		$sql = "	select 
                        OrdenDescuentoCobroCuota.*,
                        OrdenDescuentoCuota.*,
                        Proveedor.id,
                        Proveedor.razon_social,
                        concat(Empresa.concepto_1,if(Turno.descripcion is null,'',concat(' | ',Turno.descripcion))) as turno      
                    from orden_descuento_cobro_cuotas as OrdenDescuentoCobroCuota
                    inner join orden_descuento_cuotas as OrdenDescuentoCuota
                    on (OrdenDescuentoCobroCuota.orden_descuento_cuota_id = OrdenDescuentoCuota.id)
                    inner join persona_beneficios as PersonaBeneficio on (OrdenDescuentoCuota.persona_beneficio_id = PersonaBeneficio.id)
                    left join global_datos Empresa on Empresa.id = PersonaBeneficio.codigo_empresa
                    inner join socios as Socio on (Socio.id = OrdenDescuentoCuota.socio_id)
                    inner join personas as Persona on (Persona.id = Socio.persona_id)
                    inner join proveedores as Proveedor on (Proveedor.id = OrdenDescuentoCuota.proveedor_id)
                    LEFT JOIN (select codigo_empresa,turno,descripcion from liquidacion_turnos group by codigo_empresa,turno) Turno on 
                    PersonaBeneficio.codigo_empresa = Turno.codigo_empresa and PersonaBeneficio.turno_pago = Turno.turno                            
                    where  1 = 1 
                            ".(!empty($proveedor_id) ? "and OrdenDescuentoCobroCuota.proveedor_id IN ($proveedor_id)  " : "")." 
                    ".($socio_id != '0' ? "and OrdenDescuentoCuota.socio_id = $socio_id" : "")."
                                ".($tipoProducto != '0' ? "and OrdenDescuentoCuota.tipo_producto = '$tipoProducto'" : "")."
                                ".($tipoCuota != '0' ? "and OrdenDescuentoCuota.tipo_cuota = '$tipoCuota'" : "")."
                                and OrdenDescuentoCobroCuota.reversado = 1
                                and OrdenDescuentoCobroCuota.periodo_proveedor_reverso = '$periodo_reverso'
                                                    ".(!empty($codigo_organismo) ? "and PersonaBeneficio.codigo_beneficio = '$codigo_organismo' " : "" )."    
                                order by
                                    Persona.apellido,Persona.nombre";		
		$reversos = $this->query($sql);

		if(empty($reversos)) return null;
		
		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();		

		App::import('model','Pfyj.Socio');
		$oSOCIO = new Socio();		
		
		foreach($reversos as $idx => $reverso):

// 			$reverso['OrdenDescuentoCobroCuota']['porcentaje_comision'] = $reverso['ProveedorComision']['comision'];
			// $reverso['OrdenDescuentoCobroCuota']['porcentaje_comision'] = $reverso['OrdenDescuentoCobroCuota']['alicuota_comision_cobranza'];

			$reverso['OrdenDescuentoCobroCuota']['porcentaje_comision'] = 0;

			$reverso['OrdenDescuentoCobroCuota']['comision'] = $reverso['OrdenDescuentoCobroCuota']['importe_reversado'] * $reverso['OrdenDescuentoCobroCuota']['porcentaje_comision'] / 100;
//			$reverso['OrdenDescuentoCobroCuota']['comision'] = round($reverso['OrdenDescuentoCobroCuota']['comision'],2);
// 			$reverso['OrdenDescuentoCobroCuota']['comision'] = $reverso['OrdenDescuentoCobroCuota']['comision'];
			// $reverso['OrdenDescuentoCobroCuota']['comision'] = $reverso['OrdenDescuentoCobroCuota']['alicuota_comision_cobranza'];
			$reverso['OrdenDescuentoCobroCuota']['comision'] = 0;

			// $reverso['OrdenDescuentoCobroCuota']['comision_cobranza'] = $reverso['OrdenDescuentoCobroCuota']['importe_reversado'] * $reverso['OrdenDescuentoCobroCuota']['alicuota_comision_cobranza'] / 100;
			$reverso['OrdenDescuentoCobroCuota']['comision_cobranza'] = 0;
			$reverso['OrdenDescuentoCobroCuota']['alicuota_comision_cobranza'] = 0;
		
			$cuota = $oCUOTA->getCuota($reverso['OrdenDescuentoCobroCuota']['orden_descuento_cuota_id'],false);
			$reverso['OrdenDescuentoCobroCuota']['cuota'] = $cuota['OrdenDescuentoCuota'];
			$reverso['OrdenDescuentoCobroCuota']['socio_id'] = $reverso['OrdenDescuentoCuota']['socio_id'];
			$reverso['OrdenDescuentoCobroCuota']['socio'] = $oSOCIO->getApenom($reverso['OrdenDescuentoCuota']['socio_id'],true);
			$reverso['OrdenDescuentoCobroCuota']['socio_apenom'] = $oSOCIO->getApenom($reverso['OrdenDescuentoCuota']['socio_id'],false);
			$reverso['OrdenDescuentoCobroCuota']['socio_tdocndoc'] = $oSOCIO->getTdocNdoc($reverso['OrdenDescuentoCuota']['socio_id']);
            
            $reverso['OrdenDescuentoCobroCuota']['proveedor_id'] = $reverso['Proveedor']['id'];
            $reverso['OrdenDescuentoCobroCuota']['proveedor_razon_social'] = $reverso['Proveedor']['razon_social'];
            $reverso['OrdenDescuentoCobroCuota']['turno'] = $reverso[0]['turno'];
            
            $reverso['OrdenDescuentoCobroCuota']['orden_descuento_id'] = $reverso['OrdenDescuentoCuota']['orden_descuento_id'];
                        
                        
			$reversos[$idx] = $reverso;		
		
		endforeach;
	
		$reversos = Set::extract("/OrdenDescuentoCobroCuota",$reversos);
		
		return $reversos;
		
	}
	
	
	/**
	 * Calcula la comision por cobranza
	 * @param int $cuotaID
	 * @param float $importeCalculo
	 * @return array $comisionCobranza (alicuota,comision)
	 */
	function calcularComisionCobranza($cuotaID,$importeCalculo=null){
		
		$comisionCobranza = array();
		$comisionCobranza['alicuota'] = 0;
		$comisionCobranza['comision'] = 0;
	
		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();		

		App::import('Model','Proveedores.ProveedorComision');
		$oCOMISION = new ProveedorComision();
		
		$cuota = $oCUOTA->getCuota($cuotaID,false);
		
		$codigoOrganismo = $cuota['OrdenDescuentoCuota']['codigo_organismo'];
		$tipo_producto = $cuota['OrdenDescuentoCuota']['tipo_producto'];
		$tipo_cuota = $cuota['OrdenDescuentoCuota']['tipo_cuota'];	
		$proveedor_id = $cuota['OrdenDescuentoCuota']['proveedor_id'];
		
		$importeCalculo = (!empty($importeCalculo) ? $importeCalculo : $cuota['OrdenDescuentoCuota']['importe']);
		$importeCalculo = round($importeCalculo,2);
		
		$comision = $oCOMISION->getComision($codigoOrganismo,$proveedor_id,$tipo_producto,$tipo_cuota,'COB',$cuota['OrdenDescuentoCuota']['orden_descuento_fecha']);
		
		$impoComision = round($importeCalculo * ($comision / 100),2);
		
		$comisionCobranza['alicuota'] = $comision;
		$comisionCobranza['comision'] = $impoComision;
		
		return $comisionCobranza;
		
	}

	
	function cuotasSocialesCobradasByPeriodo($periodo){
		
		$totales = array();
		
		$totales['COBRANZA'][$periodo] = array();
                $totales['PADRON'][$periodo] = array();
		
		$sql = "SELECT
				Socio.categoria,
				PersonaBeneficio.codigo_beneficio, 
				OrdenDescuentoCobro.tipo_cobro,
                                AVG(OrdenDescuentoCuota.importe) as importe_promedio,
				COUNT(DISTINCT Socio.id) AS cantidad_socios,
				SUM(OrdenDescuentoCobroCuota.importe) AS cobrado, 
                                AVG(OrdenDescuentoCobroCuota.importe) AS cobrado_promedio_1,
                                SUM(OrdenDescuentoCobroCuota.importe) / COUNT(DISTINCT Socio.id) AS cobrado_promedio
				FROM orden_descuento_cobro_cuotas AS OrdenDescuentoCobroCuota
				INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON (OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id)
				INNER JOIN orden_descuento_cobros AS OrdenDescuentoCobro ON (OrdenDescuentoCobro.id = OrdenDescuentoCobroCuota.orden_descuento_cobro_id)
				INNER JOIN socios AS Socio ON (Socio.id = OrdenDescuentoCuota.socio_id)
				INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
				WHERE
				OrdenDescuentoCobro.periodo_cobro = '$periodo'
				AND OrdenDescuentoCuota.tipo_cuota = 'MUTUTCUOCSOC'
				GROUP BY Socio.categoria,PersonaBeneficio.codigo_beneficio,OrdenDescuentoCobro.tipo_cobro";
		$datos = $this->query($sql);
		if(!empty($datos)):
			foreach($datos as $idx => $dato):
				$tmp = array();
				$tmp['cantidad_socios'] = $dato[0]['cantidad_socios'];
				$tmp['cobrado'] = $dato[0]['cobrado'];
                                $tmp['cobrado_promedio'] = $dato[0]['cobrado_promedio'];
                                $tmp['importe_promedio'] = $dato[0]['importe_promedio'];
				$totales['COBRANZA'][$periodo][$dato['Socio']['categoria']][$dato['PersonaBeneficio']['codigo_beneficio']][$dato['OrdenDescuentoCobro']['tipo_cobro']] = $tmp;
			endforeach;
		endif;
                
                $year = substr($periodo,0,4);
                $month = substr($periodo,4,2);
                
                $first_day = date('Y-m-d', strtotime("$year-$month-01"));
                $last_day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                $last_day = date('Y-m-d', strtotime("$year-$month-".$last_day));
                /*
                $sql = "select t1.concepto_1,t1.cantidad_altas,t2.cantidad_bajas,t3.cantidad_total 
                        from (select g.concepto_1,count(*) as cantidad_altas 
                        from socios s
                        inner join global_datos g on g.id = s.categoria
                        where ifnull(s.fecha_alta,'$last_day') between '$first_day'
                        and '$last_day' group by g.id) t1,
                        (select g.concepto_1,count(*) as cantidad_bajas
                        from socios s
                        inner join global_datos g on g.id = s.categoria
                        where s.activo = 0 and ifnull(s.fecha_baja,'$last_day') between '$first_day'
                        and '$last_day' group by g.id) t2,
                        (select g.concepto_1,count(*) as cantidad_total 
                        from socios s
                        inner join global_datos g on g.id = s.categoria
                        where ifnull(s.fecha_alta,'1900-01-01') between '1900-01-01'
                        and '$last_day' and (s.fecha_baja is null or (s.fecha_baja > '$last_day' and s.fecha_baja <> '2099-10-10'))
                        group by g.id) t3
                        where t1.concepto_1 = t2.concepto_1
                        and t1.concepto_1 = t3.concepto_1";
                */
                $sql = "select t1.categoria, t1.concepto_1, t1.cantidad_total
                        ,(select count(*) from socios s
                        where s.categoria = t1.categoria and ifnull(s.fecha_alta,'$last_day') between '$first_day'
                        and '$last_day') cantidad_altas
                        ,(select count(*) from socios s
                        where s.categoria = t1.categoria and s.activo = 0 and ifnull(s.fecha_baja,'$last_day') between '$first_day'
                        and '$last_day') cantidad_bajas
                        from (select s.categoria, g.concepto_1,count(*) as cantidad_total 
                        from socios s
                        inner join global_datos g on g.id = s.categoria
                        where ifnull(s.fecha_alta,'1900-01-01') between '1900-01-01'
                        and '$last_day' and (s.fecha_baja is null or (s.fecha_baja > '$last_day' and s.fecha_baja <> '2099-10-10'))
                        group by g.id) t1; ";
                
                $totales['PADRON'][$periodo] = $this->query($sql);
//                debug($datos);
		
		return $totales;
		
	}
	
	
	function getTotalCuotasSocialesCobradasEntreFechas($fecha_desde,$fecha_hasta){
		
		$totales = array();
		
		$totales = array();		
		
		$sql = "SELECT
				SUM(OrdenDescuentoCobroCuota.importe) AS cobrado 
				FROM orden_descuento_cobro_cuotas AS OrdenDescuentoCobroCuota
				INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON (OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id)
				INNER JOIN orden_descuento_cobros AS OrdenDescuentoCobro ON (OrdenDescuentoCobro.id = OrdenDescuentoCobroCuota.orden_descuento_cobro_id)
				INNER JOIN socios AS Socio ON (Socio.id = OrdenDescuentoCuota.socio_id)
				INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
				WHERE
				OrdenDescuentoCobro.fecha BETWEEN '$fecha_desde' AND '$fecha_hasta'
				AND OrdenDescuentoCuota.tipo_cuota = 'MUTUTCUOCSOC';";
		$cobrado = $this->query($sql);
		if(empty($cobrado)) return 0;
		return (!empty($cobrado[0][0]['cobrado']) ? $cobrado[0][0]['cobrado'] : 0);		
	}	
	
	
	function getMontoReversadoByCobro($orden_descuento_cobro_id){
		$this->unbindModel(array('belongsTo' => array('OrdenDescuentoCobro')));
		$pagos = $this->find('all',array('conditions' => array('OrdenDescuentoCobroCuota.orden_descuento_cobro_id' => $orden_descuento_cobro_id),'fields' => array("SUM(OrdenDescuentoCobroCuota.importe_reversado) as importe_reversado"),'group' => array('OrdenDescuentoCobroCuota.orden_descuento_cobro_id')));
		if(count($pagos)!=0) return $pagos[0][0]['importe_reversado'];
		else return 0;
		
		
		
	}
	
        
        function getTotalReversadoPorSocioPorPeriodo($socio_id){
            $reversos = array();
            $sql = "SELECT c.periodo_cobro,sum(cc.importe_reversado) importe_reversado FROM orden_descuento_cobro_cuotas cc
                    inner join orden_descuento_cobros c on c.id = cc.orden_descuento_cobro_id
                    where c.socio_id = $socio_id and  cc.reversado = 1
                    group by c.periodo_cobro order by c.periodo_cobro desc;";
            $datos = $this->query($sql);
            if(empty($datos)) return $reversos;
            foreach($datos as $dato){
                $reversos[$dato['c']['periodo_cobro']] =  $dato[0]['importe_reversado'];
            }
            return $reversos;
        }
        
}
?>