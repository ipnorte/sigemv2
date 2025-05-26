<?php
class BancoCuentaSaldo extends CajabancoAppModel{
	
	var $name = 'BancoCuentaSaldo';
	
	function guardar($datos){
		
		$datos['BancoCuentaSaldo']['fecha_cierre'] = $datos['BancoCuentaSaldo']['fecha_conciliacion'];
		$datos['BancoCuentaSaldo']['numero'] = 0;
		$datos['BancoCuentaSaldo']['saldo_anterior'] = 0;
		$datos['BancoCuentaSaldo']['saldo_referencia_1'] = $datos['BancoCuentaSaldo']['tipo_conciliacion'] == 0 ? $datos['BancoCuentaSaldo']['importe_conciliacion'] : 0;
		$datos['BancoCuentaSaldo']['saldo_referencia_2'] = $datos['BancoCuentaSaldo']['tipo_conciliacion'] == 1 ? $datos['BancoCuentaSaldo']['importe_conciliacion'] : 0;
		$datos['BancoCuentaSaldo']['saldo_conciliacion'] = $datos['BancoCuentaSaldo']['importe_conciliacion'];
		
		$this->begin();
		if(parent::save($datos)):
			$cuenta = array();
			$cuenta['BancoCuenta']['id'] = $datos['BancoCuentaSaldo']['banco_cuenta_id'];
			$cuenta['BancoCuenta']['fecha_conciliacion'] = $datos['BancoCuentaSaldo']['fecha_conciliacion'];
			$cuenta['BancoCuenta']['importe_conciliacion'] = $datos['BancoCuentaSaldo']['importe_conciliacion'];
			$cuenta['BancoCuenta']['tipo_conciliacion'] = $datos['BancoCuentaSaldo']['tipo_conciliacion'];
			$cuenta['BancoCuenta']['banco_cuenta_saldo_id'] = $datos['BancoCuentaSaldo']['id'];
			$cuenta['BancoCuenta']['banco_cuenta_saldo_alta_id'] = $datos['BancoCuentaSaldo']['id'];
			$cuenta['BancoCuenta']['numero_planilla'] = 0;
			if($datos['BancoCuentaSaldo']['id'] == 0):
				$nBancoCuentaSaldoId = $this->getLastInsertID();
				$cuenta['BancoCuenta']['banco_cuenta_saldo_id'] = $nBancoCuentaSaldoId;
				$cuenta['BancoCuenta']['banco_cuenta_saldo_alta_id'] = $nBancoCuentaSaldoId;
			endif;
			$oBancoCuenta = parent::importarModelo("BancoCuenta","cajabanco");
			if($oBancoCuenta->guardar($cuenta)):
				$this->commit();
				return true;
			else:
				$this->rollback();
				return false;
			endif;
		else:
			$this->rollback();
			return false;
		endif;
	}
	
	function getSaldo($id){
		$saldo = $this->read(null,$id);
		return $saldo;
	}
	
	
	function getSldConciliado($id){
		$saldo_libro = $this->read(null, $id);
		
//		$saldos = array
//						(
//							'fecha_cierre' => $saldo_libro['BancoCuentaSaldo']['fecha_cierre'],
//							'saldo_anterior' => round($saldo_libro['BancoCuentaSaldo']['saldo_anterior'],2),
//							'saldo_libro' => round($saldo_libro['BancoCuentaSaldo']['saldo_referencia_1'],2),
//							'saldo_no_conciliado' => round($saldo_libro['BancoCuentaSaldo']['saldo_referencia_2'],2),
//							'saldo_banco' => round($saldo_libro['BancoCuentaSaldo']['saldo_anterior'] + $saldo_libro['BancoCuentaSaldo']['saldo_referencia_1'] + $saldo_libro['BancoCuentaSaldo']['saldo_referencia_2'],2),
//							'saldo_extracto' => round($saldo_libro['BancoCuentaSaldo']['saldo_extracto'],2),
//							'debitos' => round($saldo_libro['BancoCuentaSaldo']['debe'],2),
//							'creditos' => round($saldo_libro['BancoCuentaSaldo']['haber'],2)
//						);
		return $saldo_libro;
	}
	
	
	function getMovimientosConciliado($id){
		$this->BancoCuentaMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
		$movimientos = $this->BancoCuentaMovimiento->find('all', array('conditions' => array('BancoCuentaMovimiento.banco_cuenta_saldo_id' => $id), 'order' => 'BancoCuentaMovimiento.fecha_vencimiento'));
		
		$movimientos = $this->BancoCuentaMovimiento->armaDatos($movimientos, false);
		
		return $movimientos;
	}
	
	
	function saldosConciliados($id){
		return $this->find('all', array('conditions' => array('BancoCuentaSaldo.banco_cuenta_id' => $id)));
		
	}
	
	
	function getUltimaConciliacion($id){
		$conciliacion = $this->find('all', array('conditions' => array('BancoCuentaSaldo.banco_cuenta_id' => $id), 'order' => array('BancoCuentaSaldo.id DESC'), 'limit' => 1));

		return $conciliacion;
	}
	
	
	function getAnteriorConciliacion($id){
		$ultimaConciliacion = $this->getUltimaConciliacion($id);
		$conciliacion = $this->find('all', array('conditions' => array('BancoCuentaSaldo.banco_cuenta_id' => $id, 'BancoCuentaSaldo.id <' => $ultimaConciliacion[0]['BancoCuentaSaldo']['id']), 'order' => array('BancoCuentaSaldo.id DESC'), 'limit' => 1));

		return $conciliacion;
	}
	
	
	function abrirConciliacion($id){
		$this->BancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$this->BancoCuentaMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
		
		$bancoCuentaSaldo = $this->getSaldo($id);
		
		$this->begin();
		if(!$this->del($id)):
			$this->rollback();
		 	return false;
		endif;
		 
		if(!$this->BancoCuentaMovimiento->updateAll(array('BancoCuentaMovimiento.banco_cuenta_saldo_id' => 0), array('BancoCuentaMovimiento.banco_cuenta_saldo_id' => $id))):
			$this->rollback();
			return false;
		endif;

		$cuenta = $this->BancoCuenta->getCuenta($bancoCuentaSaldo['BancoCuentaSaldo']['banco_cuenta_id']);
		
		$ultimaConciliacion = $this->getUltimaConciliacion($bancoCuentaSaldo['BancoCuentaSaldo']['banco_cuenta_id']);
		
		$cuenta['BancoCuenta']['fecha_conciliacion'] = $ultimaConciliacion[0]['BancoCuentaSaldo']['fecha_cierre'];
		$cuenta['BancoCuenta']['importe_conciliacion'] = ($ultimaConciliacion[0]['BancoCuentaSaldo']['tipo_conciliacion'] == 1 ? $ultimaConciliacion[0]['BancoCuentaSaldo']['saldo_conciliacion'] * (-1) : $ultimaConciliacion[0]['BancoCuentaSaldo']['saldo_conciliacion']);
		$cuenta['BancoCuenta']['tipo_conciliacion'] = $ultimaConciliacion[0]['BancoCuentaSaldo']['tipo_conciliacion'];
		$cuenta['BancoCuenta']['banco_cuenta_saldo_id'] = $ultimaConciliacion[0]['BancoCuentaSaldo']['id'];
		$cuenta['BancoCuenta']['numero_extracto'] = $bancoCuentaSaldo['BancoCuentaSaldo']['numero_extracto'];
	    $cuenta['BancoCuenta']['fecha_extracto'] = $bancoCuentaSaldo['BancoCuentaSaldo']['fecha_extracto'];
	    $cuenta['BancoCuenta']['saldo_extracto'] = $bancoCuentaSaldo['BancoCuentaSaldo']['saldo_extracto'];
	    
		if(!$this->BancoCuenta->save($cuenta)):
			$this->rollback();
			return false;
		endif;
		
		$this->commit();

	    
//debug($cuenta);
//debug($bancoCuentaSaldo);
//debug($ultimaConciliacion);
//exit;

		return true;
		
	}
	
	
	function abrirPlanilla($id){
		$this->BancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$this->BancoCuentaMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
		
		$bancoCuentaSaldo = $this->getSaldo($id);
		
		$this->begin();
		if(!$this->deleteAll(array('BancoCuentaSaldo.id >= ' => $id, 'BancoCuentaSaldo.banco_cuenta_id' => $bancoCuentaSaldo['BancoCuentaSaldo']['banco_cuenta_id']))):
			$this->rollback();
		 	return false;
		endif;
		 
		if(!$this->BancoCuentaMovimiento->updateAll(array('BancoCuentaMovimiento.banco_cuenta_saldo_id' => 0), array('BancoCuentaMovimiento.banco_cuenta_saldo_id >=' => $id, 'BancoCuentaMovimiento.banco_cuenta_id' => $bancoCuentaSaldo['BancoCuentaSaldo']['banco_cuenta_id']))):
			$this->rollback();
//		debug('NO ACTUALIZO LOS REGISTROS DE LA TABLA BANCO CUENTA MOVIMIENTOS');
//		exit;
		return false;
		endif;

		$cuenta = $this->BancoCuenta->getCuenta($bancoCuentaSaldo['BancoCuentaSaldo']['banco_cuenta_id']);
		
		$ultimaConciliacion = $this->getUltimaConciliacion($bancoCuentaSaldo['BancoCuentaSaldo']['banco_cuenta_id']);
		
		$cuenta['BancoCuenta']['fecha_conciliacion'] = $ultimaConciliacion[0]['BancoCuentaSaldo']['fecha_cierre'];
		$cuenta['BancoCuenta']['importe_conciliacion'] = ($ultimaConciliacion[0]['BancoCuentaSaldo']['tipo_conciliacion'] == 1 ? $ultimaConciliacion[0]['BancoCuentaSaldo']['saldo_conciliacion'] * (-1) : $ultimaConciliacion[0]['BancoCuentaSaldo']['saldo_conciliacion']);
		$cuenta['BancoCuenta']['tipo_conciliacion'] = $ultimaConciliacion[0]['BancoCuentaSaldo']['tipo_conciliacion'];
		$cuenta['BancoCuenta']['banco_cuenta_saldo_id'] = $ultimaConciliacion[0]['BancoCuentaSaldo']['id'];
		$cuenta['BancoCuenta']['numero_extracto'] = null;
	    $cuenta['BancoCuenta']['fecha_extracto'] = null;
	    $cuenta['BancoCuenta']['saldo_extracto'] = null;
	    $cuenta['BancoCuenta']['numero_planilla'] = $ultimaConciliacion[0]['BancoCuentaSaldo']['numero'];
	    
		if(!$this->BancoCuenta->save($cuenta)):
			$this->rollback();
			return false;
		endif;
		
		$this->commit();

	    
//debug($cuenta);
//debug($bancoCuentaSaldo);
//debug($ultimaConciliacion);
//exit;

		return true;
		
	}
	
	
	function getMovimientosPlanillaCaja($id){
		$this->OrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');
		$this->Proveedor = $this->importarModelo('Proveedor', 'proveedores');
		
		$this->movimientos = $this->getMovimientosConciliado($id);
				
		
		$aCaja = array();
		$aCheque = array();
		$aPlanilla = array('caja' => array(), 'cheque' => array());
		$ingreso = 0;
		$egreso = 0;
		$ingreso_cheque = 0;
		$egreso_cheque = 0;
		
		foreach($this->movimientos as $movimiento):
		
			$caja = $this->BancoCuentaMovimiento->getMovimientoId($movimiento['BancoCuentaMovimiento']['id']);
			$caja = $caja[0];

			$tiDoc = 'ERR';
			$txt3 = '';
			$error = 0;

			if(!empty($caja['BancoCuentaMovimiento']['cheque_tercero'])): 
				$tmpCheque = array();
				$tmpCheque['fecha_ingreso'] = date('d-m-Y',strtotime($caja['BancoCuentaMovimiento']['cheque_tercero']['fecha_ingreso']));
				$tmpCheque['fecha_vencimiento'] = date('d-m-Y',strtotime($caja['BancoCuentaMovimiento']['cheque_tercero']['fecha_vencimiento']));
				$tmpCheque['librador'] = strtoupper($caja['BancoCuentaMovimiento']['cheque_tercero']['librador']);
				$tmpCheque['destinatario'] = strtoupper($caja['BancoCuentaMovimiento']['cheque_tercero']['destinatario']);
				$tmpCheque['banco'] = strtoupper($caja['BancoCuentaMovimiento']['cheque_tercero']['banco']);
				$tmpCheque['plaza'] = strtoupper($caja['BancoCuentaMovimiento']['cheque_tercero']['plaza']);
				$tmpCheque['numero_cheque'] = $caja['BancoCuentaMovimiento']['cheque_tercero']['numero_cheque'];
				$tmpCheque['fecha_baja'] = '';
				$tmpCheque['fecha_ingreso'] = $caja['BancoCuentaMovimiento']['cheque_tercero']['fecha_ingreso'];
				$tmpCheque['importe'] = $caja['BancoCuentaMovimiento']['cheque_tercero']['importe'];
				$tmpCheque['cheque_tercero_id'] = $caja['BancoCuentaMovimiento']['cheque_tercero']['id'];
				$tmpCheque['debe_haber'] = 0;
				
				
				array_push($aCheque, $tmpCheque);
			endif;
			
			if($caja['BancoCuentaMovimiento']['tipo'] == 9): 
				$tiDoc = 'RVS';
				$txt3 = $caja['BancoCuentaMovimiento']['destinatario'];
			elseif($caja['BancoCuentaMovimiento']['recibo_id'] > 0): 
				$tiDoc = 'REC';
				$txt3 = $caja['BancoCuentaMovimiento']['Recibo']['Recibo']['razon_social'];
				$error = $caja['BancoCuentaMovimiento']['Recibo']['Recibo']['error'];
			elseif($caja['BancoCuentaMovimiento']['orden_pago_id'] > 0): 
				$tiDoc = 'OPA';
				$txt3 = $caja['BancoCuentaMovimiento']['Orden_Pago']['OrdenPago']['Proveedor']['razon_social'];
				$error = $caja['BancoCuentaMovimiento']['Orden_Pago']['OrdenPago']['error'];
			elseif($caja['BancoCuentaMovimiento']['cancelacion_orden_id'] > 0):
				$tiDoc = 'CAN';
			elseif($caja['BancoCuentaMovimiento']['orden_caja_cobro_id'] > 0): 
				$tiDoc = 'OCA';
				$cobro = $this->OrdenDescuentoCobro->getCobro($caja['BancoCuentaMovimiento']['orden_descuento_cobro_id']);
				$proveedor = $this->Proveedor->getProveedor($cobro['OrdenDescuentoCobro']['proveedor_origen_fondo_id']);
				$txt3 = $proveedor['Proveedor']['razon_social'];
			elseif($caja['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'] > 0): $tiDoc = 'DIN';
				$relacion = $this->BancoCuentaMovimiento->getMovimientoId($caja['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'], false);
				$relacion = $relacion[0];
				$txt3 = strtoupper($relacion['BancoCuentaMovimiento']['banco_str']);

				if(!empty($relacion['BancoCuentaMovimiento']['cheque_tercero'])): 
					$caja['BancoCuentaMovimiento']['tipo_movimiento'] = 'CT';
					foreach($relacion['BancoCuentaMovimiento']['cheque_tercero'] as $chqTercero):
						$tmpCheque = array();
						$tmpCheque['texto_1'] = date('d-m-Y',strtotime($chqTercero['BancoChequeTercero']['fecha_ingreso']));
						$tmpCheque['texto_2'] = date('d-m-Y',strtotime($chqTercero['BancoChequeTercero']['fecha_vencimiento']));
						$tmpCheque['texto_3'] = strtoupper($chqTercero['BancoChequeTercero']['librador']);
						$tmpCheque['texto_4'] = strtoupper($chqTercero['BancoChequeTercero']['destinatario']);
						$tmpCheque['texto_5'] = strtoupper($chqTercero['BancoChequeTercero']['banco']);
						$tmpCheque['texto_6'] = strtoupper($chqTercero['BancoChequeTercero']['plaza']);
						$tmpCheque['texto_7'] = $chqTercero['BancoChequeTercero']['numero_cheque'];
						$tmpCheque['texto_8'] = date('d-m-Y',strtotime($chqTercero['BancoChequeTercero']['fecha_baja']));
						$tmpCheque['texto_9'] = $chqTercero['BancoChequeTercero']['fecha_baja'];
						$tmpCheque['decimal_1'] = $chqTercero['BancoChequeTercero']['importe'];
						$tmpCheque['entero_1'] = $chqTercero['BancoChequeTercero']['id'];
						$tmpCheque['entero_2'] = 1;
						
						
						array_push($aCheque, $tmpCheque);
					endforeach;
				endif;
			elseif($caja['BancoCuentaMovimiento']['banco_concepto_id'] > 0): $tiDoc = 'DIN';
				$concepto = $this->BancoConcepto->read(null, $caja['BancoCuentaMovimiento']['banco_concepto_id']);
				$txt3 = strtoupper($concepto['BancoConcepto']['concepto']);
			endif;
			
			$txt5 = 'EFECTIVO';
			if($caja['BancoCuentaMovimiento']['tipo_movimiento'] == 'CT') $txt5 = 'CH.TERCERO';
			elseif($caja['BancoCuentaMovimiento']['tipo_movimiento'] == 'DB') $txt5 = 'DEP.BANCARIO';
			elseif($caja['BancoCuentaMovimiento']['tipo_movimiento'] == 'TR') $txt5 = 'TRANS.BANCARIO';
			elseif($caja['BancoCuentaMovimiento']['tipo_movimiento'] == 'CH') $txt5 = 'CH.PROPIO';
			
			$txt6 = '';
			if($caja['BancoCuentaMovimiento']['banco_concepto_id'] > 0):
				$concepto = $this->BancoConcepto->read(null, $caja['BancoCuentaMovimiento']['banco_concepto_id']);
				$txt6 = strtoupper($concepto['BancoConcepto']['concepto']);
			endif;
			
			$tempCaja = array();
			$tempCaja['asincrono_id'] = $asinc->id;
			$tempCaja['texto_1'] = date('d-m-Y',strtotime($caja['BancoCuentaMovimiento']['fecha_operacion']));
			$tempCaja['texto_2'] = $tiDoc;
			$tempCaja['texto_3'] = strtoupper($txt3);
			$tempCaja['texto_4'] = strtoupper($caja['BancoCuentaMovimiento']['descripcion']);
			$tempCaja['texto_5'] = strtoupper($txt5);
			$tempCaja['texto_6'] = strtoupper($txt6);
			$tempCaja['texto_7'] = $caja['BancoCuentaMovimiento']['tipo_movimiento'];
			$tempCaja['texto_8'] = $caja['BancoCuentaMovimiento']['numero_operacion'];
			$tempCaja['texto_9'] = $caja['BancoCuentaMovimiento']['fecha_operacion'];
			$tempCaja['decimal_1'] = $caja['BancoCuentaMovimiento']['importe'];
			$tempCaja['entero_1'] = $caja['BancoCuentaMovimiento']['id'];
			$tempCaja['entero_2'] = $caja['BancoCuentaMovimiento']['debe_haber'];
			$tempCaja['entero_3'] = $error;
			
			array_push($aCaja, $tempCaja);

		endforeach;
		
		array_push($aPlanilla['caja'], $aCaja);
		array_push($aPlanilla['cheque'], $aCheque);


		return $aPlanilla;
	}
	
	
	function planillaAnterior($id){
		$planilla = $this->read(null, $id);
		
		$anterior = $this->find('all', array('conditions' => array('BancoCuentaSaldo.banco_cuenta_id' => $planilla['BancoCuentaSaldo']['banco_cuenta_id'], 'BancoCuentaSaldo.id <' => $id), 'order' => array('BancoCuentaSaldo.id DESC'), 'limit' => 1));

		return $anterior[0];
	}
	
	
	function getSaldoFechaAnterior($id, $fecha){
		$this->BancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$cuenta = $this->BancoCuenta->getCuenta($id);
	    $saldos = $this->getSldConciliado($cuenta['BancoCuenta']['banco_cuenta_saldo_alta_id']);
		
		$sql = "SELECT (
				SELECT IFNULL(SUM(importe),0.00) 
				FROM banco_cuenta_movimientos
				WHERE fecha_operacion < '$fecha' AND banco_cuenta_id = '$id' AND debe_haber = 0) AS saldo_debe,
				
				(
				SELECT IFNULL(SUM(importe),0.00) 
				FROM banco_cuenta_movimientos
				WHERE fecha_operacion < '$fecha' AND banco_cuenta_id = '$id' AND debe_haber = 1) AS saldo_haber
				FROM banco_cuenta_movimientos
				WHERE  banco_cuenta_id = '$id'
				LIMIT 1	
		";
		
		$saldo_anterior = $this->query($sql);

		return ($saldos['BancoCuentaSaldo']['tipo_conciliacion'] == 0 ? $saldos['BancoCuentaSaldo']['saldo_conciliacion'] : $saldos['BancoCuentaSaldo']['saldo_conciliacion'] * (-1)) + $saldo_anterior[0][0]['saldo_debe'] - $saldo_anterior[0][0]['saldo_haber'];
		
	}
        
        
        // Trae la primera planilla a partir de esa fecha.
        function getPrmPlanillaFecha($id, $fecha){
            
            $prmPlanilla = $this->find('all', array('conditions' => array('BancoCuentaSaldo.banco_cuenta_id' => $id, 'BancoCuentaSaldo.fecha_cierre >=' => $fecha), 'limit' => 1 ));
            
            return $prmPlanilla[0]['BancoCuentaSaldo']['id'];
        }
        
        
        // Trae la primera planilla a partir de esa fecha.
        function getUltPlanillaFecha($id, $fecha){
            
            $ultPlanilla = $this->find('all', array('conditions' => array('BancoCuentaSaldo.banco_cuenta_id' => $id, 'BancoCuentaSaldo.fecha_cierre <=' => $fecha), 'order' => array('BancoCuentaSaldo.fecha_cierre DESC'), 'limit' => 1 ));
            
            return $ultPlanilla[0]['BancoCuentaSaldo']['id'];
        }
}
?>