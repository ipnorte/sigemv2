<?php

/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php listado_padron_servicio 43 -app /home/adrian/dev/www/sigem/app/
 *  D:\Desarrollo\xampp\php\php.exe D:\Desarrollo\xampp\htdocs\sigem\cake\console\cake.php cierre_planilla_caja 9541 -app D:\Desarrollo\xampp\htdocs\sigem\app\
 * 
 * 
 */

class CierrePlanillaCajaShell extends Shell {

	var $cuentaId;
	var $fecha_cierre;
	var $recibos;
	var $orden_pagos;
	var $movimientos;
	var $cierre;
	
	var $uses = array('clientes.Recibo','Proveedores.OrdenPago', 'cajabanco.BancoCuentaMovimiento', 'cajabanco.BancoCuenta',
			  'mutual.OrdenDescuentoCobro', 'Proveedores.Proveedor', 'cajabanco.BancoConcepto', 'cajabanco.BancoCuentaSaldo', 
                          'cajabanco.BancoChequeTercero');

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
		
		$this->cierre = $asinc->getParametro('p3');
		
		if($this->cierre == '-1'):
			$this->cuentaId = $asinc->getParametro('p1');
			$this->fecha_cierre = $asinc->getParametro('p2');
		
			$cuenta = $this->BancoCuenta->getCuenta($this->cuentaId);
		else:
			$planilla = $this->BancoCuentaSaldo->read(null, $this->cierre);
			$planillaAnterior = $this->BancoCuentaSaldo->planillaAnterior($this->cierre);
			$cuenta = $this->BancoCuenta->getCuenta($planilla['BancoCuentaSaldo']['banco_cuenta_id']);
		
			$cuenta['BancoCuenta']['fecha_conciliacion'] = $planillaAnterior['BancoCuentaSaldo']['fecha_cierre'];
			$cuenta['BancoCuenta']['importe_conciliacion'] = ($planillaAnterior['BancoCuentaSaldo']['tipo_conciliacion'] == 1 ? $planillaAnterior['BancoCuentaSaldo']['saldo_conciliacion'] * (-1) : $planillaAnterior['BancoCuentaSaldo']['saldo_conciliacion']);
			$cuenta['BancoCuenta']['tipo_conciliacion'] = $planillaAnterior['BancoCuentaSaldo']['tipo_conciliacion'];
			$cuenta['BancoCuenta']['banco_cuenta_saldo_id'] = $planillaAnterior['BancoCuentaSaldo']['id'];
			$cuenta['BancoCuenta']['numero_extracto'] = $planilla['BancoCuentaSaldo']['numero_extracto'];
	    	$cuenta['BancoCuenta']['fecha_extracto'] = $planilla['BancoCuentaSaldo']['fecha_cierre'];
	    	$cuenta['BancoCuenta']['saldo_extracto'] = $planilla['BancoCuentaSaldo']['saldo_extracto'];

	    	$this->cuentaId = $planilla['BancoCuentaSaldo']['banco_cuenta_id'];
			$this->fecha_cierre = $planilla['BancoCuentaSaldo']['fecha_cierre'];
	    endif;
		
		$mkDesde = mktime(0,0,0,date('m',strtotime($cuenta['BancoCuenta']['fecha_conciliacion'])),date('d',strtotime($cuenta['BancoCuenta']['fecha_conciliacion'])),date('Y',strtotime($cuenta['BancoCuenta']['fecha_conciliacion'])));
		$fecha_desde = date('Y-m-d',$this->BancoCuenta->addDayToDate($mkDesde));
		
		
		$this->movimientos = $this->BancoCuentaMovimiento->getMovimientosEntreFecha($this->cuentaId, $fecha_desde, $cuenta['BancoCuenta']['fecha_extracto']);
		$aChequeCaja = $this->BancoChequeTercero->find('all', array('conditions' => array('BancoChequeTercero.fecha_baja >=' => $fecha_desde, 'BancoChequeTercero.fecha_baja <=' => $cuenta['BancoCuenta']['fecha_extracto'], 'BancoChequeTercero.caja' => 1), 'order' => array('BancoChequeTercero.fecha_baja', 'BancoChequeTercero.id')));
		$aChequeCajaIngreso = $this->BancoChequeTercero->find('all', array('conditions' => array('BancoChequeTercero.fecha_ingreso >=' => $fecha_desde, 'BancoChequeTercero.fecha_ingreso <=' => $cuenta['BancoCuenta']['fecha_extracto'], 'BancoChequeTercero.banco_cuenta_movimiento_id' => 0), 'order' => array('BancoChequeTercero.fecha_ingreso', 'BancoChequeTercero.id')));
		
		
		
		if(empty($this->movimientos)){
			$asinc->fin("**** PROCESO FINALIZADO :: NO EXISTEN REGISTROS PARA PROCESAR ****");
			return;
		}
		
		$total = count($this->movimientos) + count($aChequeCaja);
		$asinc->setTotal($total);
		$i = 0;		
		
		
		$this->Temporal->limpiarTabla($asinc->id);
		
		$temp = array();
		$temp1 = array();
		
		foreach($this->movimientos as $movimiento):
		
			$caja = $this->BancoCuentaMovimiento->getMovimientoId($movimiento['BancoCuentaMovimiento']['id']);
			$caja = $caja[0];

			$tiDoc = 'ERR';
			$txt3 = '';
			$error = 0;

			if(!empty($caja['BancoCuentaMovimiento']['cheque_tercero'])): 
				if($caja['BancoCuentaMovimiento']['cheque_tercero']['fecha_ingreso'] >= $fecha_desde && $caja['BancoCuentaMovimiento']['cheque_tercero']['fecha_ingreso'] <= $cuenta['BancoCuenta']['fecha_extracto']):
					$tmpDetalle = array();
					$tmpDetalle['AsincronoTemporalDetalle'] = array();
					$tmpDetalle['AsincronoTemporalDetalle']['asincrono_id'] = $asinc->id;
					$tmpDetalle['AsincronoTemporalDetalle']['texto_1'] = date('d-m-Y',strtotime($caja['BancoCuentaMovimiento']['cheque_tercero']['fecha_ingreso']));
					$tmpDetalle['AsincronoTemporalDetalle']['texto_2'] = date('d-m-Y',strtotime($caja['BancoCuentaMovimiento']['cheque_tercero']['fecha_vencimiento']));
					$tmpDetalle['AsincronoTemporalDetalle']['texto_3'] = strtoupper($caja['BancoCuentaMovimiento']['cheque_tercero']['librador']);
					$tmpDetalle['AsincronoTemporalDetalle']['texto_4'] = strtoupper($caja['BancoCuentaMovimiento']['cheque_tercero']['destinatario']);
					$tmpDetalle['AsincronoTemporalDetalle']['texto_5'] = strtoupper($caja['BancoCuentaMovimiento']['cheque_tercero']['banco']);
					$tmpDetalle['AsincronoTemporalDetalle']['texto_6'] = strtoupper($caja['BancoCuentaMovimiento']['cheque_tercero']['plaza']);
					$tmpDetalle['AsincronoTemporalDetalle']['texto_7'] = $caja['BancoCuentaMovimiento']['cheque_tercero']['numero_cheque'];
					$tmpDetalle['AsincronoTemporalDetalle']['texto_8'] = '';
					$tmpDetalle['AsincronoTemporalDetalle']['texto_9'] = $caja['BancoCuentaMovimiento']['cheque_tercero']['fecha_ingreso'];
					$tmpDetalle['AsincronoTemporalDetalle']['decimal_1'] = $caja['BancoCuentaMovimiento']['cheque_tercero']['importe'];
					$tmpDetalle['AsincronoTemporalDetalle']['entero_1'] = $caja['BancoCuentaMovimiento']['cheque_tercero']['id'];
					$tmpDetalle['AsincronoTemporalDetalle']['entero_2'] = 0;
					
					
					$this->Temporal->grabarTemporalDetalle($tmpDetalle);
				endif;
			endif;
			
			if(!empty($caja['BancoCuentaMovimiento']['cheque_tercero'])): 
				if($caja['BancoCuentaMovimiento']['cheque_tercero']['orden_pago_id'] > 0 && $caja['BancoCuentaMovimiento']['cheque_tercero']['caja'] == 0 && $caja['BancoCuentaMovimiento']['cheque_tercero']['fecha_baja'] >= $fecha_desde && $caja['BancoCuentaMovimiento']['cheque_tercero']['fecha_baja'] <= $cuenta['BancoCuenta']['fecha_extracto']):
					$tmpDetalle = array();
					$tmpDetalle['AsincronoTemporalDetalle'] = array();
					$tmpDetalle['AsincronoTemporalDetalle']['asincrono_id'] = $asinc->id;
					$tmpDetalle['AsincronoTemporalDetalle']['texto_1'] = date('d-m-Y',strtotime($caja['BancoCuentaMovimiento']['cheque_tercero']['fecha_ingreso']));
					$tmpDetalle['AsincronoTemporalDetalle']['texto_2'] = date('d-m-Y',strtotime($caja['BancoCuentaMovimiento']['cheque_tercero']['fecha_vencimiento']));
					$tmpDetalle['AsincronoTemporalDetalle']['texto_3'] = strtoupper($caja['BancoCuentaMovimiento']['cheque_tercero']['librador']);
					$tmpDetalle['AsincronoTemporalDetalle']['texto_4'] = strtoupper($caja['BancoCuentaMovimiento']['cheque_tercero']['destinatario']);
					$tmpDetalle['AsincronoTemporalDetalle']['texto_5'] = strtoupper($caja['BancoCuentaMovimiento']['cheque_tercero']['banco']);
					$tmpDetalle['AsincronoTemporalDetalle']['texto_6'] = strtoupper($caja['BancoCuentaMovimiento']['cheque_tercero']['plaza']);
					$tmpDetalle['AsincronoTemporalDetalle']['texto_7'] = $caja['BancoCuentaMovimiento']['cheque_tercero']['numero_cheque'];
					$tmpDetalle['AsincronoTemporalDetalle']['texto_8'] = $caja['BancoCuentaMovimiento']['cheque_tercero']['fecha_baja'];
					$tmpDetalle['AsincronoTemporalDetalle']['texto_9'] = $caja['BancoCuentaMovimiento']['cheque_tercero']['fecha_baja'];
					$tmpDetalle['AsincronoTemporalDetalle']['decimal_1'] = $caja['BancoCuentaMovimiento']['cheque_tercero']['importe'];
					$tmpDetalle['AsincronoTemporalDetalle']['entero_1'] = $caja['BancoCuentaMovimiento']['cheque_tercero']['id'];
					$tmpDetalle['AsincronoTemporalDetalle']['entero_2'] = 1;
					
					
					$this->Temporal->grabarTemporalDetalle($tmpDetalle);
				endif;
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
						$tmpDetalle = array();
						$tmpDetalle['AsincronoTemporalDetalle'] = array();
						$tmpDetalle['AsincronoTemporalDetalle']['asincrono_id'] = $asinc->id;
						$tmpDetalle['AsincronoTemporalDetalle']['texto_1'] = date('d-m-Y',strtotime($chqTercero['BancoChequeTercero']['fecha_ingreso']));
						$tmpDetalle['AsincronoTemporalDetalle']['texto_2'] = date('d-m-Y',strtotime($chqTercero['BancoChequeTercero']['fecha_vencimiento']));
						$tmpDetalle['AsincronoTemporalDetalle']['texto_3'] = strtoupper($chqTercero['BancoChequeTercero']['librador']);
						$tmpDetalle['AsincronoTemporalDetalle']['texto_4'] = strtoupper($chqTercero['BancoChequeTercero']['destinatario']);
						$tmpDetalle['AsincronoTemporalDetalle']['texto_5'] = strtoupper($chqTercero['BancoChequeTercero']['banco']);
						$tmpDetalle['AsincronoTemporalDetalle']['texto_6'] = strtoupper($chqTercero['BancoChequeTercero']['plaza']);
						$tmpDetalle['AsincronoTemporalDetalle']['texto_7'] = $chqTercero['BancoChequeTercero']['numero_cheque'];
						$tmpDetalle['AsincronoTemporalDetalle']['texto_8'] = date('d-m-Y',strtotime($chqTercero['BancoChequeTercero']['fecha_baja']));
						$tmpDetalle['AsincronoTemporalDetalle']['texto_9'] = $chqTercero['BancoChequeTercero']['fecha_baja'];
						$tmpDetalle['AsincronoTemporalDetalle']['decimal_1'] = $chqTercero['BancoChequeTercero']['importe'];
						$tmpDetalle['AsincronoTemporalDetalle']['entero_1'] = $chqTercero['BancoChequeTercero']['id'];
						$tmpDetalle['AsincronoTemporalDetalle']['entero_2'] = 1;
						$tmpDetalle['AsincronoTemporalDetalle']['entero_3'] = $chqTercero['BancoChequeTercero']['caja'];
						
						
						$this->Temporal->grabarTemporalDetalle($tmpDetalle);
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
			
			$temp = array();
			$temp['AsincronoTemporal'] = array();
			$temp['AsincronoTemporal']['asincrono_id'] = $asinc->id;
			$temp['AsincronoTemporal']['texto_1'] = date('d-m-Y',strtotime($caja['BancoCuentaMovimiento']['fecha_operacion']));
			$temp['AsincronoTemporal']['texto_2'] = $tiDoc;
			$temp['AsincronoTemporal']['texto_3'] = strtoupper($txt3);
			$temp['AsincronoTemporal']['texto_4'] = strtoupper($caja['BancoCuentaMovimiento']['descripcion']);
			$temp['AsincronoTemporal']['texto_5'] = strtoupper($txt5);
			$temp['AsincronoTemporal']['texto_6'] = strtoupper($txt6);
			$temp['AsincronoTemporal']['texto_7'] = $caja['BancoCuentaMovimiento']['tipo_movimiento'];
			$temp['AsincronoTemporal']['texto_8'] = $caja['BancoCuentaMovimiento']['numero_operacion'];
			$temp['AsincronoTemporal']['texto_9'] = $caja['BancoCuentaMovimiento']['fecha_operacion'];
			$temp['AsincronoTemporal']['decimal_1'] = $caja['BancoCuentaMovimiento']['importe'];
			$temp['AsincronoTemporal']['entero_1'] = $caja['BancoCuentaMovimiento']['id'];
			$temp['AsincronoTemporal']['entero_2'] = $caja['BancoCuentaMovimiento']['debe_haber'];
			$temp['AsincronoTemporal']['entero_3'] = $error;
			
			$this->Temporal->grabar($temp);

			$i++;
			$asinc->actualizar($i,$total,"$i / $total - PROCESANDO >> " . $caja['BancoCuentaMovimiento']['concepto']);
		endforeach;
		
		foreach($aChequeCajaIngreso as $chqTercero):
			$tmpDetalle = array();
			$tmpDetalle['AsincronoTemporalDetalle'] = array();
			$tmpDetalle['AsincronoTemporalDetalle']['asincrono_id'] = $asinc->id;
			$tmpDetalle['AsincronoTemporalDetalle']['texto_1'] = date('d-m-Y',strtotime($chqTercero['BancoChequeTercero']['fecha_ingreso']));
			$tmpDetalle['AsincronoTemporalDetalle']['texto_2'] = date('d-m-Y',strtotime($chqTercero['BancoChequeTercero']['fecha_vencimiento']));
			$tmpDetalle['AsincronoTemporalDetalle']['texto_3'] = strtoupper($chqTercero['BancoChequeTercero']['librador']);
			$tmpDetalle['AsincronoTemporalDetalle']['texto_4'] = strtoupper($chqTercero['BancoChequeTercero']['destinatario']);
			$tmpDetalle['AsincronoTemporalDetalle']['texto_5'] = strtoupper($chqTercero['BancoChequeTercero']['banco']);
			$tmpDetalle['AsincronoTemporalDetalle']['texto_6'] = strtoupper($chqTercero['BancoChequeTercero']['plaza']);
			$tmpDetalle['AsincronoTemporalDetalle']['texto_7'] = $chqTercero['BancoChequeTercero']['numero_cheque'];
			$tmpDetalle['AsincronoTemporalDetalle']['texto_8'] = '';
			$tmpDetalle['AsincronoTemporalDetalle']['texto_9'] = $chqTercero['BancoChequeTercero']['fecha_ingreso'];
			$tmpDetalle['AsincronoTemporalDetalle']['decimal_1'] = $chqTercero['BancoChequeTercero']['importe'];
			$tmpDetalle['AsincronoTemporalDetalle']['entero_1'] = $chqTercero['BancoChequeTercero']['id'];
			$tmpDetalle['AsincronoTemporalDetalle']['entero_2'] = 0;
				
				
			$this->Temporal->grabarTemporalDetalle($tmpDetalle);
		endforeach;
							
		foreach($aChequeCaja as $chqTercero):
			$tmpDetalle = array();
			$tmpDetalle['AsincronoTemporalDetalle'] = array();
			$tmpDetalle['AsincronoTemporalDetalle']['asincrono_id'] = $asinc->id;
			$tmpDetalle['AsincronoTemporalDetalle']['texto_1'] = date('d-m-Y',strtotime($chqTercero['BancoChequeTercero']['fecha_ingreso']));
			$tmpDetalle['AsincronoTemporalDetalle']['texto_2'] = date('d-m-Y',strtotime($chqTercero['BancoChequeTercero']['fecha_vencimiento']));
			$tmpDetalle['AsincronoTemporalDetalle']['texto_3'] = strtoupper($chqTercero['BancoChequeTercero']['librador']);
			$tmpDetalle['AsincronoTemporalDetalle']['texto_4'] = strtoupper($chqTercero['BancoChequeTercero']['destinatario']);
			$tmpDetalle['AsincronoTemporalDetalle']['texto_5'] = strtoupper($this->BancoChequeTercero->getNombreBanco($chqTercero['BancoChequeTercero']['banco_id']));
			$tmpDetalle['AsincronoTemporalDetalle']['texto_6'] = strtoupper($chqTercero['BancoChequeTercero']['plaza']);
			$tmpDetalle['AsincronoTemporalDetalle']['texto_7'] = $chqTercero['BancoChequeTercero']['numero_cheque'];
			$tmpDetalle['AsincronoTemporalDetalle']['texto_8'] = date('d-m-Y',strtotime($chqTercero['BancoChequeTercero']['fecha_baja']));
			$tmpDetalle['AsincronoTemporalDetalle']['texto_9'] = $chqTercero['BancoChequeTercero']['fecha_baja'];
			$tmpDetalle['AsincronoTemporalDetalle']['decimal_1'] = $chqTercero['BancoChequeTercero']['importe'];
			$tmpDetalle['AsincronoTemporalDetalle']['entero_1'] = $chqTercero['BancoChequeTercero']['id'];
			$tmpDetalle['AsincronoTemporalDetalle']['entero_2'] = 1;
			$tmpDetalle['AsincronoTemporalDetalle']['entero_3'] = $chqTercero['BancoChequeTercero']['caja'];
						
			$this->Temporal->grabarTemporalDetalle($tmpDetalle);
			$i++;
			$asinc->actualizar($i,$total,"$i / $total - PROCESANDO >> " . $tmpDetalle['AsincronoTemporalDetalle']['texto_5'] . 'NRO.: ' . $chqTercero['BancoChequeTercero']['numero_cheque']);
		endforeach;
		
		
		
		$asinc->actualizar(100,100,"FINALIZANDO...");
		$asinc->fin("**** PROCESO FINALIZADO ****");		
		
	
		
	}
	//FIN PROCESO ASINCRONO
	

}
?>