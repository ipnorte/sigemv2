<?php 
/**
 * ALTER TABLE `sigem_db`.`banco_cuenta_movimientos` ADD COLUMN `imprimir` TINYINT(1) DEFAULT 0 NULL AFTER `fecha_reemplazar`; 
 *
 */
class BancoCuentaMovimiento extends CajabancoAppModel{
	
	var $name = 'BancoCuentaMovimiento';
	
	
	/**
	 * Verifica si una cuenta bancaria tiene movimientos
	 * @param $banco_cuenta_id
	 * @return boolean
	 */
	function tieneMovimientosByBancoCuentaId($banco_cuenta_id){
		$conditions = array();
		$conditions['BancoCuentaMovimiento.banco_cuenta_id'] = $banco_cuenta_id;
		$cantidad = $this->find('count', array('conditions' => $conditions));
		if(empty($cantidad)) return false;
		else return true;		
	}
	
	/**
	 * Verifica si una chequera tiene movimientos
	 * @param $banco_cuenta_id
	 * @return boolean
	 */
	function tieneMovimientosByBancoCuentaChequeraId($banco_cuenta_chequera_id){
		$conditions = array();
		$conditions['BancoCuentaMovimiento.banco_cuenta_chequera_id'] = $banco_cuenta_chequera_id;
		$cantidad = $this->find('count', array('conditions' => $conditions));
		if(empty($cantidad)) return false;
		else return true;		
	}
	
	/**
	 * Verifica si una cuenta bancaria tiene movimientos
	 * @param $banco_cuenta_id
	 * @return boolean
	 */
	function tieneMovimientosByBancoConceptoId($banco_concepto_id){
		$conditions = array();
		$conditions['BancoCuentaMovimiento.banco_concepto_id'] = $banco_concepto_id;
		$cantidad = $this->find('count', array('conditions' => $conditions));
		if(empty($cantidad)) return false;
		else return true;		
	}
	
	
	function getMovimientoId($id, $recursive=true){
//		$movimiento = $this->read(null,$id);
		$movimiento = $this->find('all', array('conditions' => array('BancoCuentaMovimiento.id' => $id)));
	
		$movimiento = $this->armaDatos($movimiento, $recursive);

		return $movimiento;		
	}
	
	
	function getMovimientoIdEdit($id){
		$movimiento = array();
		
		$movPrincipal = $this->getMovimientoId($id, false);

		array_push($movimiento, $movPrincipal[0]['BancoCuentaMovimiento']);
		
		if($movPrincipal[0]['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'] > 0):
			$movSecundario = $this->getMovimientoId($movPrincipal[0]['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'], false);
			array_push($movimiento, $movSecundario[0]['BancoCuentaMovimiento']);
		
		else:
			$movSecundario = array();
		endif;
		
		if($movPrincipal[0]['BancoCuentaMovimiento']['tipo'] != 1) asort($movimiento);
		
		$bcoMovimiento = array();
		foreach($movimiento as $rengMov):
			array_push($bcoMovimiento, $rengMov);
		endforeach;
		
		return $bcoMovimiento;		
	}
	
	
	function getMovimiento($cuenta, $recursive=false, $adicional=true){
// 		if($cuenta['BancoCuenta']['banco_id'] == '99999'):
// 			$movimientos = $this->find('all', array('conditions' => array('BancoCuentaMovimiento.banco_cuenta_id' => $cuenta['BancoCuenta']['id'], 'BancoCuentaMovimiento.fecha_operacion > ' => $cuenta['BancoCuenta']['fecha_conciliacion']), 'order' => 'BancoCuentaMovimiento.fecha_operacion'));
// 		else:
			$movimientos = $this->find('all', array('conditions' => array('BancoCuentaMovimiento.banco_cuenta_id' => $cuenta['BancoCuenta']['id'], 'BancoCuentaMovimiento.banco_cuenta_saldo_id' => 0), 'order' => 'BancoCuentaMovimiento.fecha_vencimiento'));
// 		endif;
		
		if($adicional) $movimientos = $this->armaDatos($movimientos, $recursive);

		return $movimientos;		
	}
	
	function armaDatos($movimientos, $recursive=true){
            $oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
            $oBancoConcepto = $this->importarModelo('BancoConcepto', 'cajabanco');
            $oBancoChqTercero = $this->importarModelo('BancoChequeTercero', 'cajabanco');
            $this->Recibo = $this->importarModelo('Recibo', 'clientes');
            $this->ReciboForma = $this->importarModelo('ReciboForma', 'clientes');
            $this->OrdenPago = $this->importarModelo('OrdenPago', 'proveedores');
            $this->OrdenPagoForma = $this->importarModelo('OrdenPagoForma', 'proveedores');

            foreach($movimientos as $clave => $valor){

                $movimientos[$clave]['BancoCuentaMovimiento']['tipo_movimiento'] = '';
                $movimientos[$clave]['BancoCuentaMovimiento']['banco_cuenta'] = '';
                $movimientos[$clave]['BancoCuentaMovimiento']['banco_str'] = '';
                $movimientos[$clave]['BancoCuentaMovimiento']['cuenta_str'] = '';
                $movimientos[$clave]['BancoCuentaMovimiento']['plaza'] = '';
                $movimientos[$clave]['BancoCuentaMovimiento']['fecha_cheque'] = '';
                if($movimientos[$clave]['BancoCuentaMovimiento']['banco_concepto_id'] != 0):
                    $conceptos = $oBancoConcepto->read(null, $valor['BancoCuentaMovimiento']['banco_concepto_id']);
                    $movimientos[$clave]['BancoCuentaMovimiento']['concepto'] = $conceptos['BancoConcepto']['concepto'];
                    // Banco Cuenta
                    $bancoCuenta = $oBancoCuenta->getCuenta($valor['BancoCuentaMovimiento']['banco_cuenta_id']);
                    $movimientos[$clave]['BancoCuentaMovimiento']['banco_cuenta'] = $bancoCuenta['BancoCuenta']['banco'] . ' - ' . $bancoCuenta['BancoCuenta']['numero'] . ' - ' . $bancoCuenta['BancoCuenta']['denominacion'];
                    $movimientos[$clave]['BancoCuentaMovimiento']['banco_str'] = $bancoCuenta['BancoCuenta']['banco'];
                    $movimientos[$clave]['BancoCuentaMovimiento']['cuenta_str'] = $bancoCuenta['BancoCuenta']['numero'];
                else:
                    $movimientos[$clave]['BancoCuentaMovimiento']['concepto'] = 'EFECTIVO'; // $movimientos[$clave]['BancoCuentaMovimiento']['descripcion'];
                    $movimientos[$clave]['BancoCuentaMovimiento']['banco_cuenta'] = 'CAJA';
                endif;
                $chqTercero = $oBancoChqTercero->getChqTerceroById($valor['BancoCuentaMovimiento']['banco_cheque_tercero_id']);

                if(!empty($chqTercero)):
                    $movimientos[$clave]['BancoCuentaMovimiento']['concepto'] = 'CHEQUE'; // $movimientos[$clave]['BancoCuentaMovimiento']['descripcion'];
                    $movimientos[$clave]['BancoCuentaMovimiento']['banco_cuenta'] = $chqTercero['banco'];
                    $movimientos[$clave]['BancoCuentaMovimiento']['plaza'] = $chqTercero['plaza'];
                    $movimientos[$clave]['BancoCuentaMovimiento']['fecha_cheque'] = $chqTercero['fecha_cheque'];
                endif;
                $movimientos[$clave]['BancoCuentaMovimiento']['importe_letra'] = 'Son Pesos ' . $this->num2letras($movimientos[$clave]['BancoCuentaMovimiento']['importe']);
                $movimientos[$clave]['BancoCuentaMovimiento']['debe'] = $movimientos[$clave]['BancoCuentaMovimiento']['debe_haber'] == 0 ? $movimientos[$clave]['BancoCuentaMovimiento']['importe'] : 0;
                $movimientos[$clave]['BancoCuentaMovimiento']['haber'] = $movimientos[$clave]['BancoCuentaMovimiento']['debe_haber'] == 1 ? $movimientos[$clave]['BancoCuentaMovimiento']['importe'] : 0;

                # Busco el recibo por el cual ingreso el movimiento
                if($movimientos[$clave]['BancoCuentaMovimiento']['recibo_id'] > 0 && $recursive):
                    $movimientos[$clave]['BancoCuentaMovimiento']['Recibo'] = $this->Recibo->getRecibo($movimientos[$clave]['BancoCuentaMovimiento']['recibo_id']);
                    $movimientos[$clave]['BancoCuentaMovimiento']['tipo_movimiento'] = $this->ReciboForma->getFormaCobro($valor['BancoCuentaMovimiento']['id'], 'forma_cobro');
                endif;


                # Busco la Orden del Pago por el cual egreso el movimiento
                if($movimientos[$clave]['BancoCuentaMovimiento']['orden_pago_id'] > 0 && $recursive):
                    $movimientos[$clave]['BancoCuentaMovimiento']['Orden_Pago'] = $this->OrdenPago->getOrdenDePago($movimientos[$clave]['BancoCuentaMovimiento']['orden_pago_id']);
                    $movimientos[$clave]['BancoCuentaMovimiento']['tipo_movimiento'] = $this->OrdenPagoForma->getFormaPago($valor['BancoCuentaMovimiento']['id'], 'forma_pago');
                endif;

                if($movimientos[$clave]['BancoCuentaMovimiento']['reemplazar'] == 1):
                    $movimientos[$clave]['BancoCuentaMovimiento']['reemplazado'] = $this->getMovimientoId($movimientos[$clave]['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'], false);
                else:
                    if($movimientos[$clave]['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'] > 0 && $recursive):
                        $movimientos[$clave]['BancoCuentaMovimiento']['movimiento'] = $this->getMovimientoId($movimientos[$clave]['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'], false);
                    endif;
                endif;

                if($movimientos[$clave]['BancoCuentaMovimiento']['banco_cheque_tercero_id'] > 0):
                    $movimientos[$clave]['BancoCuentaMovimiento']['cheque_tercero'] = $chqTercero;
                    $movimientos[$clave]['BancoCuentaMovimiento']['tipo_movimiento'] = 'CT';
                else:
                    $movimientos[$clave]['BancoCuentaMovimiento']['cheque_tercero'] = $oBancoChqTercero->getChqTerceroByMovimiento($valor['BancoCuentaMovimiento']['id']);
                    if(!empty($movimientos['$clave']['BancoCuentaMovimiento']['cheque_tercero'])) $movimientos[$clave]['BancoCuentaMovimiento']['tipo_movimiento'] = 'CT';
                endif;

            }

            return $movimientos;
	}
	
	
	function getComboConcepto($cuenta){
		$oBancoConcepto = $this->importarModelo('BancoConcepto', 'cajabanco');
		
		if($cuenta['BancoCuenta']['banco_id'] == '99999'):
			$combo = $oBancoConcepto->getComboConcepto(true);
		else:
			$combo = $oBancoConcepto->getComboConcepto(false);
		endif;
		
		return $combo;
	}

	function getMovimientoByReciboId($recibo){
		$movimientos = $this->find('all', array('conditions' => array('BancoCuentaMovimiento.recibo_id' => $recibo)));
		$movimientos = $this->armaDatos($movimientos, false);
		
		return $movimientos;		
	}
	
	function getMovimientoByMovimId($id){
		$movimientos = $this->find('all', array('conditions' => array('BancoCuentaMovimiento.id' => $id)));
		$movimientos = $this->armaDatos($movimientos, false);

		$movimientos = Set::extract("{n}.BancoCuentaMovimiento",$movimientos);
		
		return $movimientos;		
	}
	
	
	function getCheques($id){
		$aCheques = $this->find('all', array('conditions' => array('BancoCuentaMovimiento.banco_cuenta_id' => $id, 'BancoCuentaMovimiento.tipo' => 1)));

		
		return $aCheques;
	}
	
	
	function registracion($datos){
		$this->oBancoConcepto = $this->importarModelo('BancoConcepto', 'cajabanco');
		$aBancoConcepto = $this->oBancoConcepto->getCuenta($datos['BancoCuentaMovimiento']['banco_concepto_id']);
		
		
		// EMISION DE CHEQUES
		if($aBancoConcepto['BancoConcepto']['tipo'] == 1):
			return $this->saveEmiCheques($datos, $aBancoConcepto);
//			if($this->saveEmiCheques($datos, $aBancoConcepto)) return true;
//			
//			return false;
		endif;

		// DEPOSITO (EFECTIVO / CHEQUES EN CARTERA)
		if($aBancoConcepto['BancoConcepto']['tipo'] == 2):
			if($datos['BancoCuentaMovimiento']['tipo_deposito'] == 'EF'):
				return $this->saveDepositoEfectivo($datos, $aBancoConcepto, true);
//				if($this->saveDepositoEfectivo($datos, $aBancoConcepto, true)) return true;
//				return false;
			else:
				return $this->saveDepChequeCartera($datos, $aBancoConcepto);
//				if($this->saveDepChequeCartera($datos, $aBancoConcepto)) return true;
//				return false;
			endif;
		endif;
		
		if($aBancoConcepto['BancoConcepto']['tipo'] == 3):
			return $this->saveDepositoEfectivo($datos, $aBancoConcepto, false);
//			if($this->saveDepositoEfectivo($datos, $aBancoConcepto, false)) return true;
//			return false;
		endif;
		
		if($aBancoConcepto['BancoConcepto']['tipo'] == 4):
			return $this->saveTraspasoFondo($datos, $aBancoConcepto);
//			if($this->saveTraspasoFondo($datos, $aBancoConcepto)) return true;
//			return false;
		endif;
		
		if($aBancoConcepto['BancoConcepto']['tipo'] == 5):
			return $this->saveTransferenciaBancaria($datos, $aBancoConcepto);
//			if($this->saveTransferenciaBancaria($datos, $aBancoConcepto)) return true;
//			return false;
		endif;

		if($aBancoConcepto['BancoConcepto']['tipo'] == 6):
			return $this->saveGastosBancarios($datos, $aBancoConcepto);
//			if($this->saveGastosBancarios($datos, $aBancoConcepto)) return true;
//			return false;
		endif;

		if($aBancoConcepto['BancoConcepto']['tipo'] == 7):
			return $this->saveMovimientoCaja($datos, $aBancoConcepto);
//			if($this->saveMovimientoCaja($datos, $aBancoConcepto)) return true;
//			return false;
		endif;
		
		return false;

	}


	function saveEmiCheques($datos, $aBancoConcepto){
				
		$datos['BancoCuentaMovimiento']['numero_operacion'] = $datos['BancoCuentaMovimiento']['numero_cheque'];
		$datos['BancoCuentaMovimiento']['fecha_operacion'] = $datos['BancoCuentaMovimiento']['fecha_emision'];
		$datos['BancoCuentaMovimiento']['tipo'] = $aBancoConcepto['BancoConcepto']['tipo'];
		$datos['BancoCuentaMovimiento']['debe_haber'] = $aBancoConcepto['BancoConcepto']['debe_haber'];
		$datos['BancoCuentaMovimiento']['descripcion'] = (empty($datos['BancoCuentaMovimiento']['descripcion']) ? 'EMISION DE CHEQUE' : $datos['BancoCuentaMovimiento']['descripcion']);
		$datos['BancoCuentaMovimiento']['destinatario'] = (empty($datos['BancoCuentaMovimiento']['destinatario']) ? $datos['BancoCuentaMovimiento']['cuenta_seleccionada'] : $datos['BancoCuentaMovimiento']['destinatario']); 
		if($datos['BancoCuentaMovimiento']['tipo_cheque'] == 'AN'):
			$datos['BancoCuentaMovimiento']['anulado'] = 1;
		endif;

		
		parent::begin();
		if(!$this->save($datos)):
			parent::rollback();
			return false;
		endif;
		
		
		$datos['BancoCuentaMovimiento']['id'] = $this->getLastInsertId();
		if($datos['BancoCuentaMovimiento']['tipo_cheque'] == 'CA'):
			$this->oCaja = $this->importarModelo('BancoCuenta', 'cajabanco');
			$cajaId = $this->oCaja->getCuentaCajaId();
			$cuentaCaja = $this->oCaja->getCuenta($cajaId);
			$cuentaBanco = $this->oCaja->getCuenta($datos['BancoCuentaMovimiento']['banco_cuenta_id']);
			
			$descripcion = 'CHEQUE PARA CAJA';
			
			$aDeposito = array();
			$aDeposito['BancoCuentaMovimiento']['id'] = 0;
			$aDeposito['BancoCuentaMovimiento']['banco_concepto_id'] = 0;
			$aDeposito['BancoCuentaMovimiento']['banco_cuenta_id'] = $cajaId;
			$aDeposito['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'] = $datos['BancoCuentaMovimiento']['id'];
			$aDeposito['BancoCuentaMovimiento']['numero_operacion'] = $datos['BancoCuentaMovimiento']['numero_operacion'];
			$aDeposito['BancoCuentaMovimiento']['fecha_operacion'] = $datos['BancoCuentaMovimiento']['fecha_operacion'];
			$aDeposito['BancoCuentaMovimiento']['fecha_vencimiento'] = $datos['BancoCuentaMovimiento']['fecha_operacion'];
			$aDeposito['BancoCuentaMovimiento']['descripcion'] = (empty($datos['BancoCuentaMovimiento']['descripcion']) ? $descripcion : $datos['BancoCuentaMovimiento']['descripcion']);
			$aDeposito['BancoCuentaMovimiento']['destinatario'] = $datos['BancoCuentaMovimiento']['destinatario'];
			$aDeposito['BancoCuentaMovimiento']['importe'] = $datos['BancoCuentaMovimiento']['importe'];
			$aDeposito['BancoCuentaMovimiento']['co_plan_cuenta_id'] = $cuentaBanco['BancoCuenta']['co_plan_cuenta_id'];
			$aDeposito['BancoCuentaMovimiento']['debe_haber'] = 0;
			$aDeposito['BancoCuentaMovimiento']['tipo'] = 7;

			if(!$this->save($aDeposito)):
				parent::rollback();
				return false;
			endif;
			
			$this->id = $datos['BancoCuentaMovimiento']['id'];
			$datos['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'] = $this->getLastInsertID();
		
			if(!$this->save($datos)):
				parent::rollback();
				return false;
			endif;
			
		endif;		


		parent::commit();
		
		return $datos['BancoCuentaMovimiento']['id'];
		
	}


	function saveDepositoEfectivo($datos, $aBancoConcepto, $esDeposito=true){
		$this->oCaja = $this->importarModelo('BancoCuenta', 'cajabanco');
		$cajaId = $this->oCaja->getCuentaCajaId();
		$cuentaCaja = $this->oCaja->getCuenta($cajaId);
		$cuentaBanco = $this->oCaja->getCuenta($datos['BancoCuentaMovimiento']['banco_cuenta_id']);
			
		$descripcion = 'DEPOSITO EN EFECTIVO';
		if(!$esDeposito) $descripcion = 'EXTRACCION DE FONDOS';
			
		$datos['BancoCuentaMovimiento']['fecha_operacion'] = $datos['BancoCuentaMovimiento']['fecha_deposito'];
		$datos['BancoCuentaMovimiento']['fecha_vencimiento'] = $datos['BancoCuentaMovimiento']['fecha_deposito'];
		$datos['BancoCuentaMovimiento']['descripcion'] = (empty($datos['BancoCuentaMovimiento']['descripcion']) ? $descripcion : $datos['BancoCuentaMovimiento']['descripcion']);
		$datos['BancoCuentaMovimiento']['debe_haber'] = $aBancoConcepto['BancoConcepto']['debe_haber'];
		$datos['BancoCuentaMovimiento']['co_plan_cuenta_id'] = $cuentaCaja['BancoCuenta']['co_plan_cuenta_id'];
		$datos['BancoCuentaMovimiento']['tipo'] = $aBancoConcepto['BancoConcepto']['tipo'];
		
		$aDeposito = array();
		$aDeposito['BancoCuentaMovimiento']['id'] = 0;
		$aDeposito['BancoCuentaMovimiento']['banco_concepto_id'] = $datos['BancoCuentaMovimiento']['banco_concepto_id'];
		$aDeposito['BancoCuentaMovimiento']['banco_cuenta_id'] = $cajaId;
		$aDeposito['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'] = 0;
		$aDeposito['BancoCuentaMovimiento']['numero_operacion'] = $datos['BancoCuentaMovimiento']['numero_operacion'];
		$aDeposito['BancoCuentaMovimiento']['fecha_operacion'] = $datos['BancoCuentaMovimiento']['fecha_operacion'];
		$aDeposito['BancoCuentaMovimiento']['fecha_vencimiento'] = $datos['BancoCuentaMovimiento']['fecha_operacion'];
		$aDeposito['BancoCuentaMovimiento']['banco_concepto_id'] = $datos['BancoCuentaMovimiento']['banco_concepto_id'];
		$aDeposito['BancoCuentaMovimiento']['descripcion'] = (empty($datos['BancoCuentaMovimiento']['descripcion']) ? $descripcion : $datos['BancoCuentaMovimiento']['descripcion']);
		$aDeposito['BancoCuentaMovimiento']['importe'] = $datos['BancoCuentaMovimiento']['importe'];
		$aDeposito['BancoCuentaMovimiento']['co_plan_cuenta_id'] = $cuentaBanco['BancoCuenta']['co_plan_cuenta_id'];
		$aDeposito['BancoCuentaMovimiento']['debe_haber'] = ($datos['BancoCuentaMovimiento']['debe_haber'] == 0 ? 1 : 0);
		$aDeposito['BancoCuentaMovimiento']['tipo'] = $aBancoConcepto['BancoConcepto']['tipo'];
		
		$this->begin();
		if(!$this->save($datos)):
			$this->rollback();
			return false;
		endif;
		$datos['BancoCuentaMovimiento']['id'] = $this->getLastInsertID();
		$aDeposito['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'] = $datos['BancoCuentaMovimiento']['id'];

		if(!$this->save($aDeposito)):
			$this->rollback();
			return false;
		endif;
		$datos['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'] = $this->getLastInsertID();
			
		$this->id = 0;
		if(!$this->save($datos)):
			$this->rollback();
			return false;
		endif;
		$this->commit();
		
		return $datos['BancoCuentaMovimiento']['id'];
	}

	
	function saveDepChequeCartera($datos, $aBancoConcepto){
		$this->oCaja = $this->importarModelo('BancoCuenta', 'cajabanco');
		$this->oChqTercero = $this->importarModelo('BancoChequeTercero', 'cajabanco');
		
		$cajaId = $this->oCaja->getCuentaCajaId();
		$cuentaCaja = $this->oCaja->getCuenta($cajaId);
		$cuentaBanco = $this->oCaja->getCuenta($datos['BancoCuentaMovimiento']['banco_cuenta_id']);
			
		$descripcion = 'DEPOSITO CHEQUE EN CARTERA';
			
		$datos['BancoCuentaMovimiento']['fecha_operacion'] = $datos['BancoCuentaMovimiento']['fecha_deposito'];
		$datos['BancoCuentaMovimiento']['fecha_vencimiento'] = $datos['BancoCuentaMovimiento']['fecha_deposito'];
		$datos['BancoCuentaMovimiento']['descripcion'] = (empty($datos['BancoCuentaMovimiento']['descripcion']) ? $descripcion : $datos['BancoCuentaMovimiento']['descripcion']);
		$datos['BancoCuentaMovimiento']['debe_haber'] = $aBancoConcepto['BancoConcepto']['debe_haber'];
		$datos['BancoCuentaMovimiento']['co_plan_cuenta_id'] = $cuentaCaja['BancoCuenta']['co_plan_cuenta_id'];
		$datos['BancoCuentaMovimiento']['tipo'] = $aBancoConcepto['BancoConcepto']['tipo'];
		$datos['BancoCuentaMovimiento']['importe'] = str_replace(',', '', $datos['BancoCuentaMovimiento']['importe_cheque']);
		
		$aDeposito = array();
		$aDeposito['BancoCuentaMovimiento']['id'] = 0;
		$aDeposito['BancoCuentaMovimiento']['banco_concepto_id'] = $datos['BancoCuentaMovimiento']['banco_concepto_id'];
		$aDeposito['BancoCuentaMovimiento']['banco_cuenta_id'] = $cajaId;
		$aDeposito['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'] = 0;
		$aDeposito['BancoCuentaMovimiento']['numero_operacion'] = $datos['BancoCuentaMovimiento']['numero_operacion'];
		$aDeposito['BancoCuentaMovimiento']['fecha_operacion'] = $datos['BancoCuentaMovimiento']['fecha_operacion'];
		$aDeposito['BancoCuentaMovimiento']['fecha_vencimiento'] = $datos['BancoCuentaMovimiento']['fecha_operacion'];
		$aDeposito['BancoCuentaMovimiento']['banco_concepto_id'] = $datos['BancoCuentaMovimiento']['banco_concepto_id'];
		$aDeposito['BancoCuentaMovimiento']['descripcion'] = (empty($datos['BancoCuentaMovimiento']['descripcion']) ? $descripcion : $datos['BancoCuentaMovimiento']['descripcion']);
		$aDeposito['BancoCuentaMovimiento']['co_plan_cuenta_id'] = $cuentaBanco['BancoCuenta']['co_plan_cuenta_id'];
		$aDeposito['BancoCuentaMovimiento']['debe_haber'] = ($datos['BancoCuentaMovimiento']['debe_haber'] == 0 ? 1 : 0);
		$aDeposito['BancoCuentaMovimiento']['tipo'] = $aBancoConcepto['BancoConcepto']['tipo'];
		$aDeposito['BancoCuentaMovimiento']['importe'] = str_replace(',', '', $datos['BancoCuentaMovimiento']['importe_cheque']);

		$aChqTercero = array();
		$this->begin();
		if(!$this->save($datos)):
			$this->rollback();
			return false;
		endif;
		$datos['BancoCuentaMovimiento']['id'] = $this->getLastInsertID();
		$aDeposito['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'] = $datos['BancoCuentaMovimiento']['id'];

		$this->id = 0;
		if(!$this->save($aDeposito)):
			$this->rollback();
			return false;
		endif;
		$datos['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'] = $this->getLastInsertID();
			
		if(!$this->save($datos)):
			$this->rollback();
			return false;
		endif;
			
		foreach($datos['BancoCuentaMovimiento']['id_check'] as $idCheque => $importeCheque):
			
			$aChqTercero['BancoChequeTercero']['id'] = $idCheque;
			$aChqTercero['BancoChequeTercero']['fecha_baja'] = $datos['BancoCuentaMovimiento']['fecha_operacion'];
			$aChqTercero['BancoChequeTercero']['destinatario'] = rtrim($cuentaBanco['BancoCuenta']['banco']) . ' ' . $cuentaBanco['BancoCuenta']['numero'];

			$aChqTercero['BancoChequeTercero']['salida_banco_cuenta_movimiento_id'] = $datos['BancoCuentaMovimiento']['id'];
			
			if(!$this->oChqTercero->save($aChqTercero)):
				$this->rollback();
				return false;
			endif;
			
		endforeach;

		$this->commit();
		
		return $datos['BancoCuentaMovimiento']['id'];
		
	}


	function saveTraspasoFondo($datos, $aBancoConcepto){
		
		$this->oBanco = $this->importarModelo('BancoCuenta', 'cajabanco');
		$desdeBanco = $this->oBanco->getCuenta($datos['BancoCuentaMovimiento']['banco_cuenta_id']);
		$haciaBanco = $this->oBanco->getCuenta($datos['BancoCuentaMovimiento']['hacia_banco_cuenta_id']);
			
		$descripcion = 'MOVIMIENTO ENTRE BANCO';
			
//		$datos['BancoCuentaMovimiento']['fecha_operacion'] = $datos['BancoCuentaMovimiento']['fecha_deposito'];
		$datos['BancoCuentaMovimiento']['fecha_vencimiento'] = $datos['BancoCuentaMovimiento']['fecha_operacion'];
		$datos['BancoCuentaMovimiento']['descripcion'] = (empty($datos['BancoCuentaMovimiento']['descripcion']) ? $descripcion : $datos['BancoCuentaMovimiento']['descripcion']);
		$datos['BancoCuentaMovimiento']['debe_haber'] = $aBancoConcepto['BancoConcepto']['debe_haber'];
		$datos['BancoCuentaMovimiento']['co_plan_cuenta_id'] = $haciaBanco['BancoCuenta']['co_plan_cuenta_id'];
		$datos['BancoCuentaMovimiento']['tipo'] = $aBancoConcepto['BancoConcepto']['tipo'];
		$datos['BancoCuentaMovimiento']['destinatario'] = 'HACIA: ' . rtrim($haciaBanco['BancoCuenta']['banco']) . ' ' . $haciaBanco['BancoCuenta']['numero'];
		
		$aBanco = array();
		$aBanco['BancoCuentaMovimiento']['banco_concepto_id'] = $datos['BancoCuentaMovimiento']['banco_concepto_id'];
		$aBanco['BancoCuentaMovimiento']['banco_cuenta_id'] = $datos['BancoCuentaMovimiento']['hacia_banco_cuenta_id'];
		$aBanco['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'] = 0;
		$aBanco['BancoCuentaMovimiento']['numero_operacion'] = $datos['BancoCuentaMovimiento']['numero_operacion'];
		$aBanco['BancoCuentaMovimiento']['fecha_operacion'] = $datos['BancoCuentaMovimiento']['fecha_operacion'];
		$aBanco['BancoCuentaMovimiento']['fecha_vencimiento'] = $datos['BancoCuentaMovimiento']['fecha_operacion'];
		$aBanco['BancoCuentaMovimiento']['banco_concepto_id'] = $datos['BancoCuentaMovimiento']['banco_concepto_id'];
		$aBanco['BancoCuentaMovimiento']['descripcion'] = (empty($datos['BancoCuentaMovimiento']['descripcion']) ? $descripcion : $datos['BancoCuentaMovimiento']['descripcion']);
		$aBanco['BancoCuentaMovimiento']['importe'] = $datos['BancoCuentaMovimiento']['importe'];
		$aBanco['BancoCuentaMovimiento']['co_plan_cuenta_id'] = $desdeBanco['BancoCuenta']['co_plan_cuenta_id'];
		$aBanco['BancoCuentaMovimiento']['debe_haber'] = ($datos['BancoCuentaMovimiento']['debe_haber'] == 0 ? 1 : 0);
		$aBanco['BancoCuentaMovimiento']['tipo'] = $aBancoConcepto['BancoConcepto']['tipo'];
		$aBanco['BancoCuentaMovimiento']['destinatario'] = 'DESDE: ' . rtrim($desdeBanco['BancoCuenta']['banco']) . ' ' . $desdeBanco['BancoCuenta']['numero'];
		

		$this->begin();
		if(!$this->save($datos)):
			$this->rollback();
			return false;
		endif;
		$datos['BancoCuentaMovimiento']['id'] = $this->getLastInsertID();
		$aBanco['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'] = $datos['BancoCuentaMovimiento']['id'];

		$this->id = 0;
		if(!$this->save($aBanco)):
			$this->rollback();
			return false;
		endif;
		$datos['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'] = $this->getLastInsertID();
			
		if(!$this->save($datos)):
			$this->rollback();
			return false;
		endif;
		$this->commit();
		
		return $datos['BancoCuentaMovimiento']['id'];
	}


	function saveTransferenciaBancaria($datos, $aBancoConcepto){
		
		$datos['BancoCuentaMovimiento']['fecha_vencimiento'] = $datos['BancoCuentaMovimiento']['fecha_operacion'];
		$datos['BancoCuentaMovimiento']['tipo'] = $aBancoConcepto['BancoConcepto']['tipo'];
		$datos['BancoCuentaMovimiento']['debe_haber'] = $aBancoConcepto['BancoConcepto']['debe_haber'];
		$datos['BancoCuentaMovimiento']['descripcion'] = (empty($datos['BancoCuentaMovimiento']['descripcion']) ? 'TRANSFERENCIA BANCARIA' : $datos['BancoCuentaMovimiento']['descripcion']);
		$datos['BancoCuentaMovimiento']['destinatario'] = $datos['BancoCuentaMovimiento']['destino'];
		
		if(!$this->save($datos)):
			return false;
		endif;
		
		return $this->getLastInsertID();
	}


	function saveGastosBancarios($datos, $aBancoConcepto){
		
		$datos['BancoCuentaMovimiento']['fecha_vencimiento'] = $datos['BancoCuentaMovimiento']['fecha_operacion'];
		$datos['BancoCuentaMovimiento']['tipo'] = $aBancoConcepto['BancoConcepto']['tipo'];
		$datos['BancoCuentaMovimiento']['debe_haber'] = $aBancoConcepto['BancoConcepto']['debe_haber'];
		$datos['BancoCuentaMovimiento']['descripcion'] = (empty($datos['BancoCuentaMovimiento']['descripcion']) ? 'TRANSFERENCIA BANCARIA' : $datos['BancoCuentaMovimiento']['descripcion']);
		$datos['BancoCuentaMovimiento']['destinatario'] = $datos['BancoCuentaMovimiento']['cuenta_seleccionada']; 

		if(!$this->save($datos)):
			return false;
		endif;
		
		return $this->getLastInsertID();
	}


	function saveMovimientoCaja($datos, $aBancoConcepto){
		
		$datos['BancoCuentaMovimiento']['fecha_vencimiento'] = $datos['BancoCuentaMovimiento']['fecha_operacion'];
		$datos['BancoCuentaMovimiento']['tipo'] = $aBancoConcepto['BancoConcepto']['tipo'];
		$datos['BancoCuentaMovimiento']['debe_haber'] = $aBancoConcepto['BancoConcepto']['debe_haber'];
		$datos['BancoCuentaMovimiento']['descripcion'] = (empty($datos['BancoCuentaMovimiento']['descripcion']) ? $aBancoConcepto['BancoConcepto']['concepto'] : $datos['BancoCuentaMovimiento']['descripcion']);
		$datos['BancoCuentaMovimiento']['destinatario'] = $datos['BancoCuentaMovimiento']['cuenta_seleccionada']; 

		if(!$this->save($datos)):
			return false;
		endif;
		
		return $this->getLastInsertID();
	}
	
	
	function reemplazar_cheque($datos){
		
//		$this->oCaja = $this->importarModelo('BancoCuenta', 'cajabanco');
//		$cajaId = $this->oCaja->getCuentaCajaId();
//		$cuentaCaja = $this->oCaja->getCuenta($cajaId);


		$this->oBancoConcepto = $this->importarModelo('BancoConcepto', 'cajabanco');
		$nConceptoId = $this->oBancoConcepto->getConceptoByTipoId(1);
		$aBancoConcepto = $this->oBancoConcepto->getCuenta($nConceptoId);

		
		$nIdAnulado = $datos['BancoCuentaMovimiento']['id'];
		
		$lEsPorCaja = (substr($datos['BancoCuentaMovimiento']['rescatar_banco_cuenta_id'],0,1) == 'C' ? true : false);

		
		// REEMPLAZAR CHEQUES
		$movimiento = $this->getMovimientoId($datos['BancoCuentaMovimiento']['id']);
		$datos['BancoCuentaMovimiento']['banco_cuenta_id'] = substr($datos['BancoCuentaMovimiento']['rescatar_banco_cuenta_id'],1); 
		$datos['BancoCuentaMovimiento']['numero_operacion'] = $datos['BancoCuentaMovimiento']['numero_cheque'];
//		$datos['BancoCuentaMovimiento']['fecha_operacion'] = $datos['BancoCuentaMovimiento']['fecha_emision'];
		$datos['BancoCuentaMovimiento']['tipo'] = ($lEsPorCaja ? 7 : 1);
		$datos['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'] = $datos['BancoCuentaMovimiento']['id'];
		$datos['BancoCuentaMovimiento']['debe_haber'] = 1; // $aBancoConcepto['BancoConcepto']['debe_haber'];
		$datos['BancoCuentaMovimiento']['descripcion'] = (empty($datos['BancoCuentaMovimiento']['descripcion']) ? 'REEMPLAZO CH. ' . $movimiento['numero_operacion'] . ' CTA: ' .$movimiento['cuenta_str'] : $datos['BancoCuentaMovimiento']['descripcion']);
		$datos['BancoCuentaMovimiento']['destinatario'] = (empty($datos['BancoCuentaMovimiento']['destinatario']) ? $movimiento['destinatario'] : $datos['BancoCuentaMovimiento']['destinatario']); 
		$datos['BancoCuentaMovimiento']['banco_concepto_id'] = ($lEsPorCaja ? 0 : $nConceptoId);
		$datos['BancoCuentaMovimiento']['id'] = 0;

		if($lEsPorCaja):
			$datos['BancoCuentaMovimiento']['fecha_operacion'] = $datos['BancoCuentaMovimiento']['fecha_caja'];
			$datos['BancoCuentaMovimiento']['fecha_vencimiento'] = $datos['BancoCuentaMovimiento']['fecha_caja'];
		endif;
		


		$this->begin();
		$this->id = 0;
		if(!$this->save($datos)):
			$this->rollback();
			return false;
		endif;
		
		$nId = $this->getLastInsertId();
		
		$this->id = $nIdAnulado;
		$aBcoMovimiento = array('BancoCuentaMovimiento' => array('id' => $nIdAnulado, 'banco_cuenta_movimiento_id' => $nId, 'anulado' => 1, 'reemplazar' => 1, 'fecha_reemplazar' => $datos['BancoCuentaMovimiento']['fecha_operacion']));
		
		if(!$this->save($aBcoMovimiento)):
			$this->rollback();
			return false;
		endif;
		
		$this->commit();
		
		return true;
	}


	function delete($id){
		$movimiento = $this->getMovimientoId($id, true);

		parent::begin();
		if(!$this->del($id)) return false;
		if($movimiento[0]['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'] > 0):
			if(!$this->del($movimiento[0]['BancoCuentaMovimiento']['banco_cuenta_movimiento_id'])):
				parent::rollback();
				return false;
			endif;
		endif;
		
		parent::commit();
		
		return true;
	}
	
	
	function getSldLibro($id){
		
		$sql = "SELECT importe_conciliacion * IF(tipo_conciliacion = 0, 1, -1) AS saldo_anterior, saldo_extracto AS saldo_extracto,
				(
					SELECT IFNULL(SUM(importe),0) saldo_debe
					FROM   banco_cuenta_movimientos
					WHERE  banco_cuenta_id = bc.id AND banco_cuenta_saldo_id = 0 AND debe_haber = 0 AND anulado = 0
				) -
				(
					SELECT	IFNULL(SUM(importe),0) saldo_haber
					FROM	banco_cuenta_movimientos
					WHERE	banco_cuenta_id = bc.id AND banco_cuenta_saldo_id = 0 AND debe_haber = 1 AND anulado = 0
				) AS saldo_libro,
				
				(
					SELECT	IFNULL(SUM(importe),0) saldo_haber
					FROM	banco_cuenta_movimientos
					WHERE	banco_cuenta_id = bc.id AND banco_cuenta_saldo_id = 0 AND debe_haber = 1 AND conciliado = 0 AND anulado = 0
				) -
				(
					SELECT	IFNULL(SUM(importe),0) saldo_debe
					FROM	banco_cuenta_movimientos
					WHERE	banco_cuenta_id = bc.id AND banco_cuenta_saldo_id = 0 AND debe_haber = 0 AND conciliado = 0 AND anulado = 0
				) AS saldo_no_conciliado,
			
				(
					SELECT	IFNULL(SUM(importe),0) saldo_debe
					FROM	banco_cuenta_movimientos
					WHERE	banco_cuenta_id = bc.id AND banco_cuenta_saldo_id = 0 AND debe_haber = 0 AND anulado = 0
				) AS debitos,
			
				(
					SELECT	IFNULL(SUM(importe),0) saldo_haber
					FROM	banco_cuenta_movimientos
					WHERE	banco_cuenta_id = bc.id AND banco_cuenta_saldo_id = 0 AND debe_haber = 1 AND anulado = 0
				) AS creditos
			
				FROM	banco_cuentas bc
				WHERE	id = '$id'";

		$saldo_banco = $this->query($sql);
		
		$saldos = array
						(
							'saldo_anterior' => round($saldo_banco[0][0]['saldo_anterior'],2),
							'saldo_libro' => round($saldo_banco[0][0]['saldo_libro'],2),
							'saldo_no_conciliado' => round($saldo_banco[0][0]['saldo_no_conciliado'],2),
							'saldo_banco' => round($saldo_banco[0][0]['saldo_anterior'] + $saldo_banco[0][0]['saldo_libro'] + $saldo_banco[0][0]['saldo_no_conciliado'],2),
							'saldo_extracto' => round($saldo_banco[0]['bc']['saldo_extracto'],2),
							'debitos' => round($saldo_banco[0][0]['debitos'],2),
							'creditos' => round($saldo_banco[0][0]['creditos'],2)
						);
						
//		debug($saldos);
//		exit;
		
		return $saldos;						
	}
	
	
	function grabarMovimientoConciliacion($id, $conciliacion){
		
//		debug($id);
//		exit;
		$this->id = $id;
		$this->saveField('conciliado', $conciliacion);
		
		return true;
	}
	
	
	function getMovimientosConciliacion($id){
		$movimientos = $this->find('all', array('conditions' => array('BancoCuentaMovimiento.banco_cuenta_id' => $id, 'BancoCuentaMovimiento.banco_cuenta_saldo_id' => 0, 'BancoCuentaMovimiento.conciliado' => 1), 'order' => 'BancoCuentaMovimiento.fecha_vencimiento'));

		return $movimientos;		
	}
	
	
	function cerrarConciliacion($banco_cuenta_id){
		$this->BancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$this->BancoCuentaSaldo = $this->importarModelo('BancoCuentaSaldo', 'cajabanco');

		$cuenta = $this->BancoCuenta->getCuenta($banco_cuenta_id);
	    $saldos = $this->getSldLibro($banco_cuenta_id);
		$movimiento = $this->getMovimientosConciliacion($banco_cuenta_id);
		$saldoConciliado = $this->getSldConciliado($banco_cuenta_id);
		
		$bancoCuentaSaldo = array('BancoCuentaSaldo' => 
		                    array('id' => 0,
		                          'banco_cuenta_id' => $banco_cuenta_id,
		                          'fecha_cierre' => $cuenta['BancoCuenta']['fecha_extracto'], 
		                          'saldo_anterior' => $saldos['saldo_anterior'],
		                          'saldo_referencia_1' => $saldos['saldo_libro'],
		                          'saldo_referencia_2' => $saldos['saldo_no_conciliado'],
		                          'saldo_conciliacion' => ($saldos['saldo_extracto'] < 0 ? $saldos['saldo_extracto'] *(-1) : $saldos['saldo_extracto']),
		                          'tipo_conciliacion' => ($saldos['saldo_extracto'] < 0 ? 1 : 0),
		                          'numero_extracto' => $cuenta['BancoCuenta']['numero_extracto'],
		                          'fecha_extracto' => $cuenta['BancoCuenta']['fecha_extracto'], 
		                          'debe' => $saldoConciliado['debe'], 'haber' => $saldoConciliado['haber'],
		                          'saldo_extracto' => $saldos['saldo_extracto']
		                    ));

//debug($cuenta);
//debug($saldos);
//debug($saldoConciliado);
//exit;

		$this->begin();
		if(!$this->BancoCuentaSaldo->save($bancoCuentaSaldo)):
			$this->rollback();
			return false;
		endif;
		
		$bancoCuentaSaldo['BancoCuentaSaldo']['id'] = $this->BancoCuentaSaldo->id;
		
		if(!$this->updateAll(array('BancoCuentaMovimiento.banco_cuenta_saldo_id' => $this->BancoCuentaSaldo->id), array('BancoCuentaMovimiento.banco_cuenta_id' => $banco_cuenta_id, 'BancoCuentaMovimiento.conciliado' => 1, 'BancoCuentaMovimiento.banco_cuenta_saldo_id' => 0))):
			$this->rollback();
			return false;
		endif;

		$bancoCuenta = array('BancoCuenta' => array(
            'id' => $banco_cuenta_id,
            'fecha_conciliacion' => $cuenta['BancoCuenta']['fecha_extracto'],
            'importe_conciliacion' => ($cuenta['BancoCuenta']['saldo_extracto'] < 0 ? $cuenta['BancoCuenta']['saldo_extracto'] *(-1) : $cuenta['BancoCuenta']['saldo_extracto']),
            'tipo_conciliacion' => ($cuenta['BancoCuenta']['saldo_extracto'] < 0 ? 1 : 0),
            'banco_cuenta_saldo_id' => $this->BancoCuentaSaldo->id,
            'numero_extracto' => '',
            'fecha_extracto' => '',
            'saldo_extracto' => 0.00
		));
		
		if(!$this->BancoCuenta->save($bancoCuenta)):
			$this->rollback();
			return false;
		endif;
		
		$this->commit();
		
		return true;		
	}
	

	function getSldConciliado($id){
		
		$sql = "SELECT importe_conciliacion * IF(tipo_conciliacion = 0, 1, -1) AS saldo_anterior, saldo_extracto AS saldo_extracto,
			
				(
					SELECT	IFNULL(SUM(importe),0) saldo_debe
					FROM	banco_cuenta_movimientos
					WHERE	banco_cuenta_id = bc.id AND banco_cuenta_saldo_id = 0 AND debe_haber = 0 AND anulado = 0 AND conciliado = 1
				) AS debe,
			
				(
					SELECT	IFNULL(SUM(importe),0) saldo_haber
					FROM	banco_cuenta_movimientos
					WHERE	banco_cuenta_id = bc.id AND banco_cuenta_saldo_id = 0 AND debe_haber = 1 AND anulado = 0 AND conciliado = 1
				) AS haber
			
				FROM	banco_cuentas bc
				WHERE	id = '$id'";

		$saldo_banco = $this->query($sql);
		
		$saldos = array
						(
							'saldo_anterior' => round($saldo_banco[0][0]['saldo_anterior'],2),
							'saldo_extracto' => round($saldo_banco[0][bc]['saldo_extracto'],2),
							'debe' => round($saldo_banco[0][0]['debe'],2),
							'haber' => round($saldo_banco[0][0]['haber'],2)
						);
						
//		debug($saldos);
//		exit;
		
		return $saldos;						
	}
	
	
	function getImporteRecibo($recibo_id){
		$condiciones = array(
							'conditions' => array(
								'BancoCuentaMovimiento.recibo_id' => $recibo_id,
							),
							'fields' => array('SUM(BancoCuentaMovimiento.importe) as importe'),
		);
		$importe_cajabanco = $this->find('all',$condiciones);
		return (isset($importe_cajabanco[0][0]['importe']) ? $importe_cajabanco[0][0]['importe'] : 0);		
		
	}
	
	
	function getImporteOrdenPago($orden_pago_id){
		$condiciones = array(
							'conditions' => array(
								'BancoCuentaMovimiento.orden_pago_id' => $orden_pago_id,
							),
							'fields' => array('SUM(BancoCuentaMovimiento.importe) as importe'),
		);
		$importe_pago = $this->find('all',$condiciones);
		return (isset($importe_pago[0][0]['importe']) ? $importe_pago[0][0]['importe'] : 0);		
		
	}
	

	function getMovimientosEntreFecha($cuentaId, $fecha_desde, $fecha_hasta, $adicional=false){
		$movimientos = $this->find('all', array('conditions' => array('BancoCuentaMovimiento.banco_cuenta_id' => $cuentaId, 'BancoCuentaMovimiento.fecha_operacion >= ' => $fecha_desde, 'BancoCuentaMovimiento.fecha_operacion <= ' => $fecha_hasta), 'order' => array('BancoCuentaMovimiento.fecha_operacion', 'BancoCuentaMovimiento.id')));

		if($adicional) $movimientos = $this->armaDatos($movimientos, false);


		return $movimientos;		
	}
	
	
	function cerrarPlanillaCaja($asincrono_id){
		
		$this->BancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$this->BancoCuentaSaldo = $this->importarModelo('BancoCuentaSaldo', 'cajabanco');
		$this->ChequeTercero = $this->importarModelo('BancoChequeTercero', 'cajabanco');
		$this->Asincrono = $this->importarModelo('Asincrono', 'Shells');
		$this->AsincronoTemporal = $this->importarModelo('AsincronoTemporal', 'Shells');
		$this->AsincronoTemporalDetalle = $this->importarModelo('AsincronoTemporalDetalle', 'Shells');
		
		$aAsincrono = $this->Asincrono->find('all', array('conditions' => array('Asincrono.id' => $asincrono_id)));
		$aTemporal = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $asincrono_id), 'order' => array('AsincronoTemporal.entero_2', 'AsincronoTemporal.texto_9', 'AsincronoTemporal.entero_1')));
		$aDetalle = $this->AsincronoTemporalDetalle->find('all', array('conditions' => array('AsincronoTemporalDetalle.asincrono_id' => $asincrono_id), 'order' => array('AsincronoTemporalDetalle.entero_2', 'AsincronoTemporalDetalle.texto_9', 'AsincronoTemporalDetalle.entero_1')));
			
		$ingreso = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $asincrono_id, 'AsincronoTemporal.entero_2' => 0), 'fields' => array('SUM(AsincronoTemporal.decimal_1) as ingreso')));
 		$ingresoChequeCaja = $this->AsincronoTemporalDetalle->find('all', array('conditions' => array('AsincronoTemporalDetalle.asincrono_id' => $asincrono_id, 'AsincronoTemporalDetalle.entero_3' => 1), 'fields' => array('SUM(AsincronoTemporalDetalle.decimal_1) as ingreso')));
		$ingreso = $ingreso[0][0]['ingreso'] + $ingresoChequeCaja[0][0]['ingreso'];
		$egreso = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $asincrono_id, 'AsincronoTemporal.entero_2' => 1), 'fields' => array('SUM(AsincronoTemporal.decimal_1) as egreso')));
		$egreso = $egreso[0][0]['egreso'] + $ingresoChequeCaja[0][0]['ingreso'];
		
		$ingreso_cheque = $this->AsincronoTemporalDetalle->find('all', array('conditions' => array('AsincronoTemporalDetalle.asincrono_id' => $asincrono_id, 'AsincronoTemporalDetalle.entero_2' => 0, 'AsincronoTemporalDetalle.entero_3' => 0), 'fields' => array('SUM(AsincronoTemporalDetalle.decimal_1) as ingreso')));
		$ingreso_cheque = $ingreso_cheque[0][0]['ingreso'];
		$egreso_cheque = $this->AsincronoTemporalDetalle->find('all', array('conditions' => array('AsincronoTemporalDetalle.asincrono_id' => $asincrono_id, 'AsincronoTemporalDetalle.entero_2' => 1), 'fields' => array('SUM(AsincronoTemporalDetalle.decimal_1) as egreso')));
		$egreso_cheque = $egreso_cheque[0][0]['egreso'];
		
		
		$id = $aAsincrono[0]['Asincrono']['p1'];
		$cuenta = $this->BancoCuenta->getCuenta($id);
			
		$saldo_cheque_inicial = $this->ChequeTercero->getSaldoCheque($cuenta['BancoCuenta']['fecha_conciliacion']);
			
		$bancoCuentaSaldo = array('BancoCuentaSaldo' => 
		                    array('id' => 0,
		                    	  'numero' => strval(intval($cuenta['BancoCuenta']['numero_planilla']) + 1),
		                          'banco_cuenta_id' => $cuenta['BancoCuenta']['id'],
		                          'fecha_cierre' => $cuenta['BancoCuenta']['fecha_extracto'], 
		                          'saldo_anterior' => $cuenta['BancoCuenta']['importe_conciliacion'],
		                          'saldo_referencia_1' => $ingreso,
		                          'saldo_referencia_2' => $egreso,
		                          'saldo_conciliacion' => $cuenta['BancoCuenta']['importe_conciliacion'] + $ingreso - $egreso,
		                          'tipo_conciliacion' => 0,
		                          'numero_extracto' => $cuenta['BancoCuenta']['numero_extracto'],
		                          'fecha_extracto' => $cuenta['BancoCuenta']['fecha_extracto'], 
		                          'debe' => $ingreso_cheque, 'haber' => $egreso_cheque,
		                          'saldo_extracto' => $saldo_cheque_inicial,
		                    	  'asincrono_id' => $asincrono_id
		                    ));

//debug($bancoCuentaSaldo);
//exit;
		                    
		$this->begin();
		if(!$this->BancoCuentaSaldo->save($bancoCuentaSaldo)):
			$this->rollback();
			return false;
		endif;
		
		$bancoCuentaSaldo['BancoCuentaSaldo']['id'] = $this->BancoCuentaSaldo->id;
//		$movimientos = $this->find('all', array('conditions' => array('BancoCuentaMovimiento.banco_cuenta_id' => $cuentaId, 'BancoCuentaMovimiento.banco_cuenta_saldo_id' => 0, 'BancoCuentaMovimiento.fecha_operacion >= ' => $fecha_desde, 'BancoCuentaMovimiento.fecha_operacion <= ' => $fecha_hasta), 'order' => array('BancoCuentaMovimiento.fecha_operacion', 'BancoCuentaMovimiento.id')));
		
		if(!$this->updateAll(array('BancoCuentaMovimiento.banco_cuenta_saldo_id' => $this->BancoCuentaSaldo->id), array('BancoCuentaMovimiento.banco_cuenta_id' => $id, 'BancoCuentaMovimiento.banco_cuenta_saldo_id' => 0, 'BancoCuentaMovimiento.fecha_operacion > ' => $cuenta['BancoCuenta']['fecha_conciliacion'], 'BancoCuentaMovimiento.fecha_operacion <= ' => $cuenta['BancoCuenta']['fecha_extracto']))):
			$this->rollback();
			return false;
		endif;

		$bancoCuenta = array('BancoCuenta' => array(
            'id' => $id,
		    'numero_planilla' => strval(intval($cuenta['BancoCuenta']['numero_planilla']) + 1),
			'fecha_conciliacion' => $cuenta['BancoCuenta']['fecha_extracto'],
            'importe_conciliacion' => $cuenta['BancoCuenta']['importe_conciliacion'] + $ingreso - $egreso,
            'tipo_conciliacion' => 0,
            'banco_cuenta_saldo_id' => $this->BancoCuentaSaldo->id,
            'numero_extracto' => '',
            'fecha_extracto' => '',
            'saldo_extracto' => 0.00
		));
		
		if(!$this->BancoCuenta->save($bancoCuenta)):
			$this->rollback();
			return false;
		endif;
		
		$this->commit();
		
		return true;		
	}
	
	
	function efectivo_cheque_cartera($datos){
		$oChqTercero = $this->importarModelo('BancoChequeTercero', 'cajabanco');
		
		$this->begin();
		foreach($datos['BancoCuentaMovimiento']['id_check'] as $idCheque => $importeCheque):
			
			$aChqTercero['BancoChequeTercero']['id'] = $idCheque;
			$aChqTercero['BancoChequeTercero']['fecha_baja'] = $datos['BancoCuentaMovimiento']['fecha_operacion'];
			$aChqTercero['BancoChequeTercero']['destinatario'] = 'CAJA';
			$aChqTercero['BancoChequeTercero']['caja'] = 1;
				
			if(!$oChqTercero->save($aChqTercero)):
				$this->rollback();
				return false;
			endif;
			
		endforeach;

		$this->commit();
		
		return true;
	}
	
	
	function modifica_fecha_comprobante($datos){
		$oRecibo = $this->importarModelo('Recibo', 'clientes');
		$oReciboForma = $this->importarModelo('ReciboForma', 'clientes');
		
		$oOrdenPago = $this->importarModelo('OrdenPago', 'proveedores');
		$oOrdenPagoForma = $this->importarModelo('OrdenPagoForma', 'proveedores');
		
		$aMovimiento = $this->getMovimientoId($datos['BancoCuentaMovimiento']['id']);
		
		if($aMovimiento[0]['BancoCuentaMovimiento']['recibo_id'] > 0):
			$aRecibo = $aMovimiento[0]['BancoCuentaMovimiento']['Recibo'];
			
			$aRecibo['Recibo']['fecha_comprobante'] = $this->armaFecha($datos['BancoCuentaMovimiento']['fecha_operacion']);
				
			$this->begin();
			
			
			foreach ($aRecibo['Recibo']['forma'] as $aFormaPago):
				$aFormaPago['fecha_operacion'] = $this->armaFecha($datos['BancoCuentaMovimiento']['fecha_operacion']);
				$aFormaPago['fecha_vencimiento'] = $this->armaFecha($datos['BancoCuentaMovimiento']['fecha_operacion']);
				
				if(!$oReciboForma->save($aFormaPago)):
					$this->rollback();
					return false;
				endif;

				$aBanco = $this->getMovimientoId($aFormaPago['banco_cuenta_movimiento_id']);
				$aBanco[0]['BancoCuentaMovimiento']['fecha_operacion'] = $this->armaFecha($datos['BancoCuentaMovimiento']['fecha_operacion']);
				$aBanco[0]['BancoCuentaMovimiento']['fecha_vencimiento'] = $this->armaFecha($datos['BancoCuentaMovimiento']['fecha_operacion']);
				
				if(!$this->save($aBanco[0])):
					$this->rollback();
					return false;
				endif;
			endforeach;
			
			if(!$oRecibo->save($aRecibo)):
				$this->rollback();
				return false;
			endif;
			$this->commit();
			return true;
			
		elseif($aMovimiento[0]['BancoCuentaMovimiento']['orden_pago_id'] > 0):
//			$aOrdenPago = $oOrdenPago->getOrdenDePago($aMovimiento[0]['BancoCuentaMovimiento']['orden_pago_id']);
			$aOrdenPago = $aMovimiento[0]['BancoCuentaMovimiento']['Orden_Pago'];
		
			$aOrdenPago['OrdenPago']['fecha_pago'] = $this->armaFecha($datos['BancoCuentaMovimiento']['fecha_operacion']);

			$this->begin();
				
				
			foreach ($aOrdenPago['forma'] as $aFormaPago):
				$aFormaPago['fecha_operacion'] = $this->armaFecha($datos['BancoCuentaMovimiento']['fecha_operacion']);
				$aFormaPago['fecha_vencimiento'] = $this->armaFecha($datos['BancoCuentaMovimiento']['fecha_operacion']);
			
				if(!$oOrdenPagoForma->save($aFormaPago)):
					$this->rollback();
					return false;
				endif;
			
				$aBanco = $this->getMovimientoId($aFormaPago['banco_cuenta_movimiento_id']);
				$aBanco[0]['BancoCuentaMovimiento']['fecha_operacion'] = $this->armaFecha($datos['BancoCuentaMovimiento']['fecha_operacion']);
				$aBanco[0]['BancoCuentaMovimiento']['fecha_vencimiento'] = $this->armaFecha($datos['BancoCuentaMovimiento']['fecha_operacion']);
			
				if(!$this->save($aBanco[0])):
					$this->rollback();
					return false;
				endif;
			endforeach;
				
			if(!$oOrdenPago->save($aOrdenPago)):
				$this->rollback();
				return false;
			endif;
			$this->commit();
			return true;
				
		else:
		
			$aMovimiento[0]['BancoCuentaMovimiento']['fecha_operacion'] = $this->armaFecha($datos['BancoCuentaMovimiento']['fecha_operacion']);
			$aMovimiento[0]['BancoCuentaMovimiento']['fecha_vencimiento'] = $this->armaFecha($datos['BancoCuentaMovimiento']['fecha_operacion']);
				
//			$this->begin();
				
			foreach($aMovimiento[0]['BancoCuentaMovimiento']['movimiento'] as $aIndividual):
				$aIndividual['BancoCuentaMovimiento']['fecha_operacion'] = $this->armaFecha($datos['BancoCuentaMovimiento']['fecha_operacion']);
				$aIndividual['BancoCuentaMovimiento']['fecha_vencimiento'] = $this->armaFecha($datos['BancoCuentaMovimiento']['fecha_operacion']);

				if(!$this->save($aIndividual)):
					$this->rollback();
					return false;
				endif;
			endforeach;

			if(!$this->save($aMovimiento[0])):
				$this->rollback();
				return false;
			endif;
				
			$this->commit();
			return true;
		endif;
		
		
		return true;
	}
	
	function getTipoMovimiento($cuenta, $tipo){
            $movimientos = $this->find('all', array('conditions' => array('BancoCuentaMovimiento.banco_cuenta_id' => $cuenta['BancoCuenta']['id'], 'BancoCuentaMovimiento.tipo' => $tipo, 'imprimir' => '1'), 'order' => 'BancoCuentaMovimiento.numero_operacion'));

            $movimientos = $this->armaDatos($movimientos, false);

            return $movimientos;		
	}
        
        
        function getLibroBanco($id, $fecha_desde, $fecha_hasta, $adicional=true){
            $aRecibo = array();
            $aOrdenPago = array();
            
            $oRecibo = $this->importarModelo('Recibo', 'clientes');
            $oOrdenPago = $this->importarModelo('OrdenPago', 'proveedores');

            $movimientos = $this->getMovimientosEntreFecha($id, $fecha_desde, $fecha_hasta, $adicional);

            foreach($movimientos as $clave => $valor){
                # Busco el recibo por el cual ingreso el movimiento
                if($movimientos[$clave]['BancoCuentaMovimiento']['recibo_id'] > 0):
                    $aRecibo = $this->Recibo->getRecibo($movimientos[$clave]['BancoCuentaMovimiento']['recibo_id']);
                endif;

                # Busco la Orden del Pago por el cual egreso el movimiento
                if($movimientos[$clave]['BancoCuentaMovimiento']['orden_pago_id'] > 0):
                    $aOrdenPago = $this->OrdenPago->getOrdenDePago($movimientos[$clave]['BancoCuentaMovimiento']['orden_pago_id']);
                    $movimientos[$clave]['BancoCuentaMovimiento']['destinatario'] .= ' - ' . $aOrdenPago['OrdenPago']['comentario'];
                endif;
                

            }

            return $movimientos;
        }
}

?>