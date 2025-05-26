<?php
class SocioReintegroPago extends PfyjAppModel{
	
	var $name = "SocioReintegroPago";
	var $belongsTo = array('Socio');
	

	function getPago($id){
		$pago = $this->read(null,$id);
		return $this->__armaDatos($pago);
	}
	
	
	function __armaDatos($pago){
		$glb = parent::getGlobalDato('concepto_1',$pago['SocioReintegroPago']['forma_pago']);
		$pago['SocioReintegroPago']['forma_pago_desc'] = $glb['GlobalDato']['concepto_1'];
		return $pago;
	}
	
	function generarPago($datos){
		$this->begin();
		if(!$this->save($datos)){
			$this->rollback();
			return false;
		}
		$idPago = $this->getLastInsertID();
		if(empty($idPago) || $idPago == 0){
			$this->rollback();
			return false;			
		}
		if(!isset($datos['SocioReintegro']['id']) || empty($datos['SocioReintegro']['id'])){
			$this->rollback();
			return false;			
		}
		App::import('Model','Pfyj.SocioReintegro');
		$oREINTEGRO = new SocioReintegro();
		$error = false;		
		foreach($datos['SocioReintegro']['id'] as $id => $importe){
			$reintegro = $oREINTEGRO->read(null,$id);
			$reintegro['SocioReintegro']['procesado'] = 1;
			$reintegro['SocioReintegro']['reintegrado'] = 1;
			$reintegro['SocioReintegro']['socio_reintegro_pago_id'] = $idPago;
			$reintegro['SocioReintegro']['importe_aplicado'] = $reintegro['SocioReintegro']['importe_reintegro'];
			if(!$oREINTEGRO->save($reintegro)){
				$error = true;
				break;
			}
		}
		if($error){
			$this->rollback();
			return false;			
		}else{
			$this->commit();
			return true;			
		}		
	}
	


	function generarOrdenPago($datos){
//		$datos['Movimiento']['fecha_pago'] = parent::armaFecha($datos['Movimiento']['fecha_pago']);
//		debug($datos);
//		exit;
		
		$this->begin();
		if(!$this->save($datos)){
			$this->rollback();
			return false;
		}
		$idPago = $this->getLastInsertID();
		if(empty($idPago) || $idPago == 0){
			$this->rollback();
			return false;			
		}
		if(!isset($datos['SocioReintegro']['id']) || empty($datos['SocioReintegro']['id'])){
			$this->rollback();
			return false;			
		}

		if(empty($datos['Movimiento']['observacion'])) $datos['Movimiento']['observacion'] = 'REINTEGRO A SOCIO';
		
		$datos['Movimiento']['detalle_reintegro'] = array();
		App::import('Model','Pfyj.SocioReintegro');
		$oREINTEGRO = new SocioReintegro();
		$error = false;
		foreach($datos['SocioReintegro']['id'] as $id => $importe){
			$reintegro = $oREINTEGRO->read(null,$id);
			$aDetalleReintegro = array('socio_reintegro_id' => $id, 'importe' => $reintegro['SocioReintegro']['importe_reintegro']);
			array_push($datos['Movimiento']['detalle_reintegro'], $aDetalleReintegro);
		}
		// GENERO LA ORDEN DE PAGO
		$this->oMovimiento = $this->importarModelo('Movimiento', 'proveedores');
		$this->oOPagoDetalle = $this->importarModelo('OrdenPagoDetalle', 'proveedores');

		if(!$this->oMovimiento->guardarOpago($datos, false)):
			$this->rollback();
			return false;			
		endif;
		
		$error = false;		
		foreach($datos['SocioReintegro']['id'] as $id => $importe){
			$reintegro = $oREINTEGRO->read(null,$id);
			$reintegro['SocioReintegro']['procesado'] = 1;
			$reintegro['SocioReintegro']['reintegrado'] = 1;
			$reintegro['SocioReintegro']['socio_reintegro_pago_id'] = $idPago;
			$reintegro['SocioReintegro']['importe_aplicado'] = $reintegro['SocioReintegro']['importe_reintegro'];
	    	$reintegro['SocioReintegro']['orden_pago_id'] = $this->oOPagoDetalle->getOPagoByReintegro($id); 
			if(!$oREINTEGRO->save($reintegro)){
				$error = true;
				break;
			}
			$nOrdenPago = $reintegro['SocioReintegro']['orden_pago_id'];
		}
		
		if($error){
			$this->rollback();
			return false;			
		}		
		$this->commit();
		return $nOrdenPago;

	}
	
	/**
	 * Grabar un pago parcial / total del un reintegro
	 * 
	 * @author adrian [13/02/2012]
	 * @param array $datos
	 */
	function generarOrdenPagoParcial($datos){
		
	
		if(empty($datos['SocioReintegro']['pago'])) return false;
		
		App::import('Model','Pfyj.SocioReintegro');
		$oREINTEGRO = new SocioReintegro();		
		
		########################################################################
		#GENERO LA ORDEN DE PAGO
		########################################################################
		$datos['Movimiento']['detalle_reintegro'] = array();

		App::import('Model','proveedores.Movimiento');
		$oMOVIMIENTO = new Movimiento();		

		App::import('Model','proveedores.OrdenPagoDetalle');
		$oORDENPAGO = new OrdenPagoDetalle();		
		
		foreach($datos['SocioReintegro']['id'] as $id => $importePago):
			$aDetalleReintegro = array('socio_reintegro_id' => $id, 'importe' => $datos['SocioReintegro']['pago'][$id]);
			array_push($datos['Movimiento']['detalle_reintegro'], $aDetalleReintegro);
		endforeach;
		
		$this->begin();
		
		$ordenPagoId = $oMOVIMIENTO->guardarOpago($datos, false, true);
		
		if(!$ordenPagoId):
			$this->rollback();
			return false;			
		endif;		
		
		########################################################################
		#GRABO LA SOCIO_REINTEGRO_PAGOS
		########################################################################
		$reintegroPago = array();
		$error = false;
		foreach($datos['SocioReintegro']['id'] as $id => $importePago):
			$reintegro = $oREINTEGRO->getReintegro($id);
			$reintegroPago['SocioReintegroPago']['id'] = 0;
			$reintegroPago['SocioReintegroPago']['socio_reintegro_id'] = $id;
			$reintegroPago['SocioReintegroPago']['socio_id'] = $reintegro['SocioReintegro']['socio_id'];
			$reintegroPago['SocioReintegroPago']['importe'] = $datos['SocioReintegro']['pago'][$id];
			$reintegroPago['SocioReintegroPago']['orden_pago_id'] = $ordenPagoId;
			if(!$this->save($reintegroPago)){
				$error = true;
				break;
			}
//			$reintegro['SocioReintegro']['importe_aplicado'] += $importePago;
			if($reintegro['SocioReintegro']['saldo'] == 0){
				$reintegro['SocioReintegro']['procesado'] = 1;
				$reintegro['SocioReintegro']['reintegrado'] = 1;
			}
			if(!$oREINTEGRO->save($reintegro)){
				$error = true;
				break;
			}
		endforeach;
		
		if($error){
			$this->rollback();
			return false;
		}
		
		$this->commit();
		return $ordenPagoId;
		
	}
	
	
	/**
	 * Devuelve el importe total de pagos efectuados sobre un reintegro
	 * En base a las ordenes de pago emitidas. Si es cero verifica que 
	 * no tenga un pago grabado en la socio_reintegro_pagos (casos viejos)
	 * 
	 * @author adrian [13/02/2012]
	 * @param float $socio_reintegro_id
	 */
	function getTotalPagoByReintegro($socio_reintegro_id){
		App::import('Model','proveedores.OrdenPagoDetalle');
		$oORDENPAGO = new OrdenPagoDetalle();
		$pagoOpago = $oORDENPAGO->getTotalPagoByReintegro($socio_reintegro_id);
		if($pagoOpago == 0){
			$pago = $this->find('all',array('conditions' => array('SocioReintegroPago.socio_reintegro_id' => $socio_reintegro_id), 'fields' => array('sum(importe) as importe')));
			return (isset($pago[0][0]['importe']) ? $pago[0][0]['importe'] : 0);
		}else{
			return $pagoOpago;
		}			
	}
	
	
	/**
	 * Devuelve el total pagado en reintegros para una liquidacion / socio
	 * 
	 * @author adrian [22/05/2012]
	 * @param integer $socio_id
	 * @param integer $liquidacion_id
	 * @return float
	 */
	function getTotalPagoByLiquidacionSocioId($socio_id,$liquidacion_id){
            $socio_id = (empty($socio_id) ? 0 : $socio_id);
		$sql = "SELECT IFNULL(SUM(OrdenPagoDetalle.importe),0) AS importe_pagado FROM orden_pago_detalles AS OrdenPagoDetalle
				INNER JOIN socio_reintegros AS SocioReintegro ON (SocioReintegro.id = OrdenPagoDetalle.socio_reintegro_id)
				WHERE SocioReintegro.liquidacion_id = $liquidacion_id AND SocioReintegro.socio_id = $socio_id
				GROUP BY SocioReintegro.socio_id
				UNION
				SELECT IFNULL(SUM(SocioReintegroPago.importe),0) AS importe_pagado FROM socio_reintegro_pagos AS SocioReintegroPago
				INNER JOIN socio_reintegros AS SocioReintegro ON (SocioReintegro.id = SocioReintegroPago.socio_reintegro_id)
				WHERE SocioReintegro.liquidacion_id = $liquidacion_id AND SocioReintegro.socio_id = $socio_id
				GROUP BY SocioReintegro.socio_id";
		$pago = $this->query($sql);
		return (isset($pago[0][0]['importe_pagado']) ? $pago[0][0]['importe_pagado'] : 0);
	}
	
	
	function generarOrdenPagoAnticipado($datos){
		
		
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();
		$periodo = $oLQ->read('periodo',$datos['SocioReintegro']['liquidacion_id']);		
		$periodo = $periodo['Liquidacion']['periodo'];
		
		#GENERO UN REINTEGRO ANTICIPADO
		App::import('Model','Pfyj.SocioReintegro');
		$oREINTEGRO = new SocioReintegro();				

		$reintegro = array();
		$reintegro['SocioReintegro']['id'] = 0;
		$reintegro['SocioReintegro']['anticipado'] = 1;
		$reintegro['SocioReintegro']['liquidacion_id'] = $datos['SocioReintegro']['liquidacion_id'];
		$reintegro['SocioReintegro']['socio_id'] = $datos['SocioReintegro']['socio_id'];
		$reintegro['SocioReintegro']['periodo'] = $periodo;
		$reintegro['SocioReintegro']['procesado'] = 1;
		$reintegro['SocioReintegro']['reintegrado'] = 1;
		
		$this->begin();
		
		if(!$oREINTEGRO->save($reintegro)){
			$this->rollback();
			return false;
		}
		
		$reintegro_id = $oREINTEGRO->getLastInsertID();
		
		###########################################################################
		#GENERO UNA ORDEN DE PAGO
		###########################################################################
		App::import('Model','proveedores.Movimiento');
		$oMOVIMIENTO = new Movimiento();		

		App::import('Model','proveedores.OrdenPagoDetalle');
		$oORDENPAGO = new OrdenPagoDetalle();		
		
		$datos['Movimiento']['detalle_reintegro'][0] = array('socio_reintegro_id' => $reintegro_id, 'importe' => $datos['Movimiento']['importe_pago']);
		
		$ordenPagoId = $oMOVIMIENTO->guardarOpago($datos, false, true);
		
		if(!$ordenPagoId):
			$this->rollback();
			return false;			
		endif;		
		
		###########################################################################
		#GENERO UN PAGO DEL REINTEGRO
		###########################################################################
		$reintegroPago = array();
		$reintegroPago['SocioReintegroPago']['id'] = 0;
		$reintegroPago['SocioReintegroPago']['socio_reintegro_id'] = $reintegro_id;
		$reintegroPago['SocioReintegroPago']['socio_id'] = $datos['SocioReintegro']['socio_id'];
		$reintegroPago['SocioReintegroPago']['importe'] = $datos['Movimiento']['importe_pago'];
		$reintegroPago['SocioReintegroPago']['orden_pago_id'] = $ordenPagoId;
		
		
		if(!$this->save($reintegroPago)){
			$this->rollback();
			return false;
		}		
		
		$this->commit();
		return $ordenPagoId;
		
	}
	
	
}
?>