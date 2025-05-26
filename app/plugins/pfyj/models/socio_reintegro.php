<?php
class SocioReintegro extends PfyjAppModel{
	
	var $name = "SocioReintegro";
	var $belongsTo = array('Socio');
	
	/**
	 * 
	 * 
	 * @author adrian [29/02/2012]
	 * @param unknown_type $id
	 */
	function borrar($id){
		$reintegro = $this->getReintegro($id);
		if($reintegro['SocioReintegro']['procesado'] == 0 && $reintegro['SocioReintegro']['anticipado'] == 1 && $reintegro['SocioReintegro']['importe_reintegro'] == $reintegro['SocioReintegro']['saldo'] && empty($reintegro['SocioReintegro']['ordenes_pagos']) && empty($reintegro['SocioReintegro']['cobro'])){
			return parent::del($id);
		}else{
			return false;
		}
	}
	
	
	/**
	 * devuelve los reintegros por socio
	 * @param $socio_id
	 */
	function getReintegrosBySocio($socio_id,$cargarCobro=true){
		$reintegros = $this->find('all',array('conditions' => array('SocioReintegro.socio_id' => $socio_id),'order' => array('SocioReintegro.periodo DESC,SocioReintegro.created DESC'), 'limit' => 12));
		foreach($reintegros as $idx => $reintegro){
			$reintegros[$idx] = $this->__armaDatos($reintegro,$cargarCobro);
		}
		return $reintegros;		
	}
        
	function getReintegrosBySocio2($socio_id){
            $SQL = "SELECT 
                        sr.id,
                        IF(sr.importe_reintegro - IFNULL((SELECT SUM(importe) FROM orden_pago_detalles srp WHERE srp.socio_reintegro_id = sr.id), 0) = 0, 1, sr.procesado) AS procesado,
                        sr.anticipado,
                        sr.orden_descuento_cobro_id,
                        sr.imputado_deuda,
                        sr.reintegrado,
                        sr.orden_pago_id,
                        IF(sr.anticipado = 1, 'ANTICIPADO', 'COMUN') AS tipo,
                        sr.periodo,
                        sr.created,
                        CONCAT(l.periodo, ' - ', org.concepto_1, IF(l.imputada = 1, ' ** IMPUTADA **', '')) AS liquidacion,
                        sr.importe_reintegro,
                        sr.importe_reversado,
                        IFNULL((SELECT SUM(importe) FROM orden_pago_detalles srp WHERE srp.socio_reintegro_id = sr.id), 0) AS pagado,
                        sr.importe_reintegro - IFNULL((SELECT SUM(importe) FROM orden_pago_detalles srp WHERE srp.socio_reintegro_id = sr.id), 0) AS saldo,
                        (SELECT GROUP_CONCAT(CONCAT(op.id, '|', IFNULL(op.tipo_documento, ''), ' ', IFNULL(op.sucursal, ''), '-', op.nro_orden_pago) SEPARATOR ', ')
                         FROM orden_pago_detalles opd
                         INNER JOIN orden_pagos op ON op.id = opd.orden_pago_id
                         WHERE opd.socio_reintegro_id = sr.id) AS ordenes_pagos
                    FROM socio_reintegros sr
                    LEFT JOIN liquidaciones l ON l.id = sr.liquidacion_id
                    LEFT JOIN global_datos org ON org.id = l.codigo_organismo
                    WHERE sr.socio_id = $socio_id
                    ORDER BY sr.periodo DESC;";
            return $this->query($SQL);
	}        
	
	/**
	 * devuelve los reintegros de un socio para un periodo determinado
	 * @param $socio_id
	 * @param $periodo
	 * @return unknown_type
	 */
	function getReintegrosBySocioByPeriodo($socio_id,$periodo){
		$reintegros = $this->find('all',array('conditions' => array('SocioReintegro.socio_id' => $socio_id,'SocioReintegro.periodo' => $periodo)));
		foreach($reintegros as $idx => $reintegro){
			$reintegros[$idx] = $this->__armaDatos($reintegro);
		}
		return $reintegros;			
	}
	

	/**
	 * Reintegros NO PROCESADOS del socio
	 * 
	 * @author adrian [08/02/2012]
	 * @param int $socio_id
	 * @param boolean $anticipado
	 * @param string $periodo
	 * @return array
	 */
	function getReintegrosPendientesBySocio($socio_id,$anticipado = null, $periodo = null){
		$conditions = array(); 
		$conditions['SocioReintegro.socio_id'] = $socio_id;
		$conditions['SocioReintegro.procesado'] = 0;
		if(!empty($anticipado)) $conditions['SocioReintegro.anticipado'] = $anticipado;
		if(!empty($periodo)) $conditions['SocioReintegro.periodo'] = $periodo;
		$reintegros = $this->find('all',array('conditions' => $conditions));
		foreach($reintegros as $idx => $reintegro){
			$reintegros[$idx] = $this->__armaDatos($reintegro);
		}
		return $reintegros;		
	}	
	
	/**
	 * arma datos adicionales al reintegro
	 * @param unknown_type $reintegro
	 */
	function __armaDatos($reintegro,$cargarCobro=true){
		App::import('Model','Mutual.Liquidacion');
		$oLIQUIDACION = new Liquidacion();	
		$reintegro['SocioReintegro']['liquidacion_str']	= $oLIQUIDACION->getDatoLiquidacion($reintegro['SocioReintegro']['liquidacion_id'],true);
		$reintegro['SocioReintegro']['cobro'] = array();
		if($reintegro['SocioReintegro']['orden_descuento_cobro_id'] != 0 && $cargarCobro):
			App::import('Model','Mutual.OrdenDescuentoCobro');
			$oCOB = new OrdenDescuentoCobro();
			$oCOB->bindModel(array('hasMany' => array('OrdenDescuentoCobroCuota')));
			$cobro = $oCOB->getCobro($reintegro['SocioReintegro']['orden_descuento_cobro_id']);
			$reintegro['SocioReintegro']['cobro'] = $cobro;
		endif;
		
		//armo datos relacionado a como se pago el reintegro
		$reintegro['SocioReintegro']['accion'] = "";
		if($reintegro['SocioReintegro']['imputado_deuda'] == 1):
			$reintegro['SocioReintegro']['accion'] = "IMPUTADO EN CTA.CTE. | ORDEN COBRO #" . $reintegro['SocioReintegro']['orden_descuento_cobro_id'];
		endif;
		if($reintegro['SocioReintegro']['reintegrado'] == 1):
			$reintegro['SocioReintegro']['accion'] = "ABONADO AL SOCIO | " . ($reintegro['SocioReintegro']['socio_reintegro_pago_id'] != 0 ? "PAGO #" . $reintegro['SocioReintegro']['socio_reintegro_pago_id'] : "");
		endif;
		// ESTO ES NUEVO. ES PARA LA ORDEN DE PAGO. GUSTAVO 17/10/2011.
                /*
		if($reintegro['SocioReintegro']['orden_pago_id'] > 0):
			$this->oOrdenPagoDetalle = $this->importarModelo('OrdenPagoDetalle', 'proveedores');
			$this->oOrdenPago = $this->importarModelo('OrdenPago', 'proveedores');
			$aOrdenPago = $this->oOrdenPago->getOrdenDePago($this->oOrdenPagoDetalle->getOPagoByReintegro($reintegro['SocioReintegro']['id']));
			$reintegro['SocioReintegro']['nro_orden_pago'] =  str_pad($aOrdenPago['OrdenPago']['nro_orden_pago'], 8, '0', STR_PAD_LEFT);
		endif;
		*/
		App::import('Model','Pfyj.Socio');
		$oSOCIO = new Socio();		
		$reintegro['SocioReintegro']['socio'] = $oSOCIO->getApenom($reintegro['SocioReintegro']['socio_id'],true);
		
		$reintegro['SocioReintegro']['tipo'] = ($reintegro['SocioReintegro']['anticipado'] == 1 ? "ANTICIPADO" : "COMUN");
		if($reintegro['SocioReintegro']['compensa_imputacion'] == 1) $reintegro['SocioReintegro']['tipo'] .= " *** COMPENSA ANTES DE IMPUTAR ***";
		
		#CALCULO EL SALDO EN BASE A LOS PAGOS REGISTRADOS DEL REINTEGRO
//		App::import('Model','Pfyj.SocioReintegroPago');
//		$oSOCIOREINTEGROPAGO = new SocioReintegroPago();			
//		$reintegro['SocioReintegro']['pagos'] = $oSOCIOREINTEGROPAGO->getTotalPagoByReintegro($reintegro['SocioReintegro']['id']);
//		$reintegro['SocioReintegro']['saldo'] = $reintegro['SocioReintegro']['importe_reintegro'] - $reintegro['SocioReintegro']['importe_aplicado'] - $reintegro['SocioReintegro']['pagos'];
		
		$reintegro['SocioReintegro']['pagos'] = $this->getTotalPagadoAlSocio($reintegro['SocioReintegro']['id']);
		$reintegro['SocioReintegro']['saldo'] = $reintegro['SocioReintegro']['importe_reintegro'] - $reintegro['SocioReintegro']['pagos'];
		

		if($reintegro['SocioReintegro']['reversado'] == 1) $reintegro['SocioReintegro']['saldo'] -= $reintegro['SocioReintegro']['importe_reversado'];
		
		//AGREGO UN ARRAY CON LAS ORDENES DE PAGOS
		$reintegro['SocioReintegro']['ordenes_pagos'] = array();
		$reintegro['SocioReintegro']['ordenes_pago_numeros'] = array();
		if($reintegro['SocioReintegro']['orden_pago_id'] == 0){
			$this->oOrdenPago = $this->importarModelo('OrdenPago', 'proveedores');
			$this->oOrdenPagoDetalle = $this->importarModelo('OrdenPagoDetalle', 'proveedores');
			$aOrdenPago = $this->oOrdenPagoDetalle->getOPagoByReintegro($reintegro['SocioReintegro']['id'],true);

			if(!empty($aOrdenPago)){
				foreach($aOrdenPago as $ordenPagoId){
					$ordenPago = $this->oOrdenPago->getOrdenDePago($ordenPagoId);
//					debug($ordenPago);
					array_push($reintegro['SocioReintegro']['ordenes_pagos'], str_pad($ordenPagoId, 8, '0', STR_PAD_LEFT));
//					array_push($reintegro['SocioReintegro']['ordenes_pago_numeros'], $ordenPago['OrdenPago']['tipo_comprobante_desc']);
					$reintegro['SocioReintegro']['ordenes_pago_numeros'][$ordenPagoId] = $ordenPago['OrdenPago']['tipo_comprobante_desc'];
				}
			}
		}else if($reintegro['SocioReintegro']['orden_pago_id'] != 0){
			$ordenPago = $this->oOrdenPago->getOrdenDePago($reintegro['SocioReintegro']['orden_pago_id']);
			array_push($reintegro['SocioReintegro']['ordenes_pagos'], str_pad($reintegro['SocioReintegro']['orden_pago_id'], 8, '0', STR_PAD_LEFT));
			$reintegro['SocioReintegro']['ordenes_pago_numeros'][$reintegro['SocioReintegro']['orden_pago_id']] = $ordenPago['OrdenPago']['tipo_comprobante_desc'];
		}
		return $reintegro;
	}
	
	/**
	 * Get Reintegro
	 * @param unknown_type $id
	 * @return unknown_type
	 */
	function getReintegro($id){
		$reintegro = $this->read(null,$id);
		return $this->__armaDatos($reintegro);
	}
	
	
	function getDescripcion($id){
		$reintegro = $this->read(null,$id);
		App::import('Model','Mutual.Liquidacion');
		$oLIQUIDACION = new Liquidacion();	
		$descripcion = $oLIQUIDACION->getDatoLiquidacion($reintegro['SocioReintegro']['liquidacion_id']);	
		return $descripcion;	
	}
	
	function aprobarImputacionEnCtaCte($id,$socio_id,$importeImputa){

		$imputacion = $this->armarImputacionEnCtaCte($socio_id,$importeImputa);
		
		$reintegro = $this->getReintegro($id);
	
		$this->begin();
		
		$cuotas = $imputacion['cuotas'];
		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();
		
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();			
		
		$idNC = 0;
		$error = false;
		foreach($cuotas as $idx => $cuota){
			if(!$oCUOTA->save($cuota)){
				$error = true;
				break;
			}
			if($cuota['OrdenDescuentoCuota']['id'] == 0){
				$cuota['OrdenDescuentoCuota']['id'] = $oCUOTA->getLastInsertID();
				$idNC = $cuota['OrdenDescuentoCuota']['id'];
			}
			$cuotas[$idx] = $cuota;
		}
		if($error){
			$this->rollback();
			return false;			
		}
		//armo el pago
		$periodoCobro = date('Ym');
		$pago = array('OrdenDescuentoCobro' => array(
			'tipo_cobro' => 'MUTUTCOBREIN',
			'socio_id' => $socio_id,	
			'fecha' => date('Y-m-d'),
			'periodo_cobro' => $reintegro['SocioReintegro']['periodo'],
			'socio_reintegro_id' => $reintegro['SocioReintegro']['id']
		));	

		$cuotaPagada 	= array();
		$cuotasPagadas 	= array();
		$acuPago = 0;
		
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oCOBCUO = new OrdenDescuentoCobroCuota();		
		
		foreach($cuotas as $cuota){
			
			$comision = $oCOBCUO->calcularComisionCobranza($cuota['OrdenDescuentoCuota']['id'],$cuota['OrdenDescuentoCuota']['importe_aimputar']);
			
			$cuotaPagada['periodo_cobro'] = $periodoCobro;
			$cuotaPagada['orden_descuento_cuota_id'] = $cuota['OrdenDescuentoCuota']['id'];
			$cuotaPagada['proveedor_id'] = $cuota['OrdenDescuentoCuota']['proveedor_id'];
			$cuotaPagada['importe'] = $cuota['OrdenDescuentoCuota']['importe_aimputar'];
			$cuotaPagada['alicuota_comision_cobranza'] = $comision['alicuota'];
			$cuotaPagada['comision_cobranza'] = $comision['comision'];
			
			array_push($cuotasPagadas,$cuotaPagada);
			$acuPago += $cuota['OrdenDescuentoCuota']['importe_aimputar'];			
		}
		$pago['OrdenDescuentoCobro']['importe'] = $acuPago;
		$pago['OrdenDescuentoCobroCuota'] = $cuotasPagadas;
		
		$liquidacion = $oLQ->read('nro_recibo',$this->data['SocioReintegro']['liquidacion_id']);
		
		$pago['OrdenDescuentoCobro']['nro_recibo'] = $liquidacion['Liquidacion']['nro_recibo'];
		
		App::import('Model','Mutual.OrdenDescuentoCobro');
		$oCOB = new OrdenDescuentoCobro();

		if(!$oCOB->saveAll($pago,array('atomic'=>false))){
			$this->rollback();
			return false;
		}
		$idOrdenCobro = $oCOB->getLastInsertID();
		
		
		$reintegro['SocioReintegro']['orden_descuento_cobro_id'] = $idOrdenCobro;
//		$reintegro['SocioReintegro']['procesado'] = 1;
		$reintegro['SocioReintegro']['imputado_deuda'] = 1;
		$reintegro['SocioReintegro']['importe_aplicado'] += $acuPago;
		
		//SI NO TIENE SALDO MARCO COMO PROCESADO
		if(($reintegro['SocioReintegro']['saldo'] - $acuPago) == 0) $reintegro['SocioReintegro']['procesado'] = 1;
		

		//si el total del cobro es menor al reintegro actualizar el importe del reintegro
		//y generar uno nuevo con el saldo disponible
		$nuevoReintegro = array();
		if($reintegro['SocioReintegro']['saldo'] > $reintegro['SocioReintegro']['importe_aplicado']):
		
			$nuevoReintegro = $reintegro;
			$nuevoReintegro['SocioReintegro']['id'] = 0;
			$nuevoReintegro['SocioReintegro']['importe_reintegro'] = $reintegro['SocioReintegro']['saldo'] - $reintegro['SocioReintegro']['importe_aplicado'];
			$nuevoReintegro['SocioReintegro']['orden_descuento_cobro_id'] = 0;
			$nuevoReintegro['SocioReintegro']['procesado'] = 0;
			$nuevoReintegro['SocioReintegro']['imputado_deuda'] = 0;
			$nuevoReintegro['SocioReintegro']['importe_aplicado'] = 0;
			
			$reintegro['SocioReintegro']['importe_reintegro'] = $reintegro['SocioReintegro']['importe_aplicado'];
		endif;
		
		
		if(!$this->save($reintegro)){
			$this->rollback();
			return false;
		}
		
		if(!empty($nuevoReintegro)):
			if(!$this->save($nuevoReintegro)){
				$this->rollback();
				return false;
			}		
		endif;
		
		// todo bien 
		$this->commit();
		return true;
		
	}
	
	
	function armarImputacionEnCtaCte($socio_id,$totalReintegro){
		$imputacion = array();
		//cargo las cuotas adeudadas del socio
		App::import('model','mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();		
		$cuotas = $oCUOTA->cuotasAdeudadasBySocio($socio_id);
		$cuotas = Set::extract('/OrdenDescuentoCuota',$cuotas);
		$resto = $totalReintegro;
		$importe_imputado = 0;
		$saldoCuota = 0;
		$cuotasImputadas = array();
				
		foreach($cuotas as $cuota){

			if($resto == 0) break;
			
			$montoCuota = $cuota['OrdenDescuentoCuota']['saldo_cuota'];
			
			if($resto >= $montoCuota){
				$importe_imputado = $montoCuota;
				$resto -= $montoCuota;
				$saldoCuota = 0;
			}else{
				$importe_imputado = $resto;
				$saldoCuota = $montoCuota - $importe_imputado;
				$resto -= $importe_imputado;
			}
			$cuota['OrdenDescuentoCuota']['importe_aimputar'] = round($importe_imputado,2);
			$cuota['OrdenDescuentoCuota']['n_saldocuota'] = $saldoCuota;
			if($saldoCuota == 0) $cuota['OrdenDescuentoCuota']['estado'] = 'P';
			if(round($importe_imputado,2) != 0)array_push($cuotasImputadas,$cuota);
			
		}
		
		$imputacion['importe_imputa'] = $totalReintegro;
		$imputacion['cuotas'] = $cuotasImputadas;
		
//		debug($imputacion);
		
		return $imputacion;
	}

	/**
	 * Devuelve el total anticipado para un socio y para una liquidacion
	 * @param unknown_type $socio_id
	 * @param unknown_type $liquidacion_id
	 * @return number
	 */
	function getTotalReintegrosAnticipados($socio_id,$liquidacion_id){
//		$conditions = array();
//		$conditions['SocioReintegro.socio_id'] = $socio_id;
//		$conditions['SocioReintegro.liquidacion_id'] = $liquidacion_id;
//		$conditions['SocioReintegro.anticipado'] = 1;
		
//		$total = $this->find('all',array('conditions' => $conditions,
//											'fields' => array('SUM(importe_aplicado) as importe_aplicado')					
//										)
//		);
//		return (isset($total[0][0]['importe_aplicado']) ? $total[0][0]['importe_aplicado'] : 0);
		// sacar el saldo de los reintegros anticipados
		$reintegros = $this->getReintegrosAnticipados($socio_id,$liquidacion_id);
		if(empty($reintegros)) return 0;
		$totalPagado = 0;
		foreach($reintegros as $reintegro){
			$totalPagado += $this->getTotalPagadoAlSocio($reintegro['SocioReintegro']['id']);
		}
		return $totalPagado;
	}
	
	/**
	 * Devuelve los reintegros anticipados para un socio y para una liquidacion dada
	 * @param $socio_id
	 * @param $liquidacion_id
	 * @return unknown_type
	 */
	function getReintegrosAnticipados($socio_id,$liquidacion_id){
		$conditions = array();
		$conditions['SocioReintegro.socio_id'] = $socio_id;
		$conditions['SocioReintegro.liquidacion_id'] = $liquidacion_id;
		$conditions['SocioReintegro.anticipado'] = 1;
//		return $this->find('all',array('conditions' => $conditions,'order' => array('SocioReintegro.importe_aplicado DESC')));
		$reintegros = $this->find('all',array('conditions' => $conditions));
		if(empty($reintegros)) return null;
		foreach($reintegros as $idx => $reintegro){
			$reintegros[$idx] = $this->__armaDatos($reintegro);
		}
		return $reintegros;
	}


	/**
	 * Devuelve el total de reintegros anticipados a compensar
	 * @param unknown_type $socio_id
	 * @param unknown_type $liquidacion_id
	 * @return unknown_type
	 */
	function getTotalReintegrosAnticipadosACompensar($socio_id,$liquidacion_id){
		$conditions = array();
		$conditions['SocioReintegro.socio_id'] = $socio_id;
		$conditions['SocioReintegro.liquidacion_id'] = $liquidacion_id;
		$conditions['SocioReintegro.anticipado'] = 1;
//		$conditions['SocioReintegro.compensa_imputacion'] = 1;
		
//		$total = $this->find('all',array('conditions' => $conditions,
//											'fields' => array('SUM(importe_aplicado) as importe_aplicado')					
//										)
//		);
//		return (isset($total[0][0]['importe_aplicado']) ? $total[0][0]['importe_aplicado'] : 0);

		$reintegros = $this->find('all',array('conditions' => $conditions));
		if(empty($reintegros)) return 0;
		
		$totalPagado = 0;
		
		foreach($reintegros as $reintegro){
			$totalPagado += $this->getTotalPagadoAlSocio($reintegro['SocioReintegro']['id']);
		}
		
		return $totalPagado;
	}

	
	function getPeriodos($ampliado = false){
		$periodos = array();
		$sql = "SELECT DISTINCT periodo FROM socio_reintegros AS SocioReintegro ORDER BY periodo DESC";
		$datos = $this->query($sql);
		if(empty($datos)) return null;
		foreach($datos as $dato):
			$periodos[$dato['SocioReintegro']['periodo']] = parent::periodo($dato['SocioReintegro']['periodo'],$ampliado);
		endforeach;
		return $periodos;
	}

	
	function anularOrdenPago($nOrdenPago){
		$this->OrdenPago = $this->importarModelo('OrdenPago', 'proveedores');
		$this->SocioReintegroPago = $this->importarModelo('SocioReintegroPago', 'pfyj');
				
		$aReintegros = $this->OrdenPago->getDetalle($nOrdenPago);
		
		
		$this->begin();
		if(!$this->OrdenPago->anular($nOrdenPago)):
			return false;
		endif;
		
		foreach($aReintegros as $reintegro):
		
			$aUpdateReintegro = $this->getReintegro($reintegro['socio_reintegro_id']);

			$aUpdateReintegro['SocioReintegro']['procesado'] = 0;
			$aUpdateReintegro['SocioReintegro']['reintegrado'] = 0;
			$aUpdateReintegro['SocioReintegro']['socio_reintegro_pago_id'] = 0;
			$aUpdateReintegro['SocioReintegro']['importe_aplicado'] = 0;
			$aUpdateReintegro['SocioReintegro']['orden_pago_id'] = 0;
			$this->id = $reintegro['socio_reintegro_id'];
			if(!$this->save($aUpdateReintegro)):
				$this->rollback();
				return false;
			endif;
//			$aSocioReintegroPago = $aUpdateReintegro['SocioReintegro']['socio_reintegro_pago_id'];
			
			if(!$this->SocioReintegroPago->deleteAll("SocioReintegroPago.socio_reintegro_id=".$reintegro['socio_reintegro_id'])):
				$this->rollback();
				return false;
			endif;			
			
		endforeach;

//		if(!$this->SocioReintegroPago->deleteAll("SocioReintegroPago.socio_reintegro_id=".$reintegro['socio_reintegro_id'])):
//			$this->rollback();
//			return false;
//		endif;

		$this->commit();
		
		return true;
	}
	
	
	/**
	 * Reversar un reintegro
	 * 
	 * @author adrian [08/02/2012]
	 * @param int $id
	 * @param string $periodoProveedor
	 * @param float $importeReversado
	 */
	function reversar($id,$periodoProveedor,$importeReversado, $movBancoId = 0){
		$reintegro = $this->read(null,$id);
		$reintegro['SocioReintegro']['reversado'] = 1;
		$reintegro['SocioReintegro']['importe_reversado'] += $importeReversado;
		$reintegro['SocioReintegro']['periodo_proveedor_reverso'] = $periodoProveedor;
		$reintegro['SocioReintegro']['fecha_reverso'] = date('Y-m-d');
		
		// Agregado Gustavo (Movimiento de caja y banco del Reverso)
		$reintegro['SocioReintegro']['banco_cuenta_movimiento_id'] = $movBancoId;

		if(!empty($_SESSION['NAME_USER_LOGON_SIGEM']))$reintegro['SocioReintegro']['usuario_reverso'] = $_SESSION['NAME_USER_LOGON_SIGEM'];
		return $this->save($reintegro);
	}
	
	
	function getTotalPagadoAlSocio($reintegro_id){
		App::import('Model','Pfyj.SocioReintegroPago');
		$oSOCIOREINTEGROPAGO = new SocioReintegroPago();			
		return $oSOCIOREINTEGROPAGO->getTotalPagoByReintegro($reintegro_id);
	}
	

	/**
	 * Devuelve los reintegros emitidos a la ultima liquidacion imputada anterior a la pasada por parametro
	 * Si discriminaPagos = true devuelve un array con el total de reintegros y total de pagos
	 * 
	 * @author adrian [01/03/2012]
	 * @param unknown_type $socio_id
	 * @param unknown_type $liquidacionActualId
	 * @param unknown_type $discriminaPago
	 */
	function getTotalReintegroLiquidacionAnterior($socio_id,$liquidacionActualId,$discriminaPago = false, $incluyeAnticipados = false){
		
		$totales = array('REINTEGRO' => 0, 'PAGADO' => 0);
		
		App::import('Model','Mutual.Liquidacion');
		$oLIQUIDACION = new Liquidacion();	

		$idUltimaLiquidacion = $oLIQUIDACION->getUltimaLiquidacionImputada($oLIQUIDACION->getCodigoOrganismo($liquidacionActualId));
		
		$conditions = array();
		$conditions['SocioReintegro.socio_id'] = $socio_id;
		$conditions['SocioReintegro.liquidacion_id'] = $idUltimaLiquidacion;
		if(!$incluyeAnticipados) $conditions['SocioReintegro.anticipado'] = 0;
		
		if($discriminaPago):
			
			$reintegros = $this->find('all',array('conditions' => $conditions, 'fields' => array('SocioReintegro.id,SocioReintegro.importe_reintegro')));
			
			if(empty($reintegros)) return $totales;
			$IMPORTE_REINTEGRO = 0;
			$IMPORTE_PAGADO = 0;
			foreach($reintegros as $reintegro){
				$IMPORTE_PAGADO += $this->getTotalPagadoAlSocio($reintegro['SocioReintegro']['id']);
				$IMPORTE_REINTEGRO += $reintegro['SocioReintegro']['importe_reintegro'];
			}
			$totales['REINTEGRO'] = $IMPORTE_REINTEGRO;
			$totales['PAGADO'] = $IMPORTE_PAGADO;
			return $totales;
		else:
			$reintegros = $this->find('all',array('conditions' => $conditions, 'fields' => array('SUM(importe_reintegro) as importe_reintegro')));
			return (!empty($reintegros[0][0]['importe_reintegro']) ? $reintegros[0][0]['importe_reintegro'] : 0);
		endif;
		
	}
	
	
}
?>