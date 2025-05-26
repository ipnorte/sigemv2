<?php
class BancoCuentaSaldosController extends CajabancoAppController {

	var $name = 'BancoCuentaSaldos';
	var $uses = array('cajabanco.BancoCuentaSaldo', 'cajabanco.BancoCuenta', 'cajabanco.BancoCuentaChequera', 'cajabanco.BancoCuentaMovimiento',
					  'Shells.Asincrono', 'Shells.AsincronoTemporal', 'Shells.AsincronoTemporalDetalle', 'Contabilidad.Ejercicio');
	var $autorizar = array('add','del','edit', 'imprimir_conciliacion', 'planillas', 'view_conciliacion', 'abrir_conciliacion', 'saldo_conciliado',
	                       'view_planilla_caja', 'abrir_planilla', 'libro_caja');
	
	function add($banco_cuenta_id = null){
		if(empty($banco_cuenta_id)) $this->redirect('/cajabanco/banco_cuentas');
		$oBANCOCUENTA = $this->BancoCuentaChequera->importarModelo("BancoCuenta","cajabanco");
		$cuenta = $oBANCOCUENTA->read(null,$banco_cuenta_id);
		if(empty($cuenta)) $this->redirect('/cajabanco/banco_cuentas');
		$this->set('banco_cuenta_id',$banco_cuenta_id);
		$this->set('cuenta',$cuenta);
		if(!empty($this->data)):
			if($this->BancoCuentaChequera->guardar($this->data)){
				$this->redirect('index/' . $this->data['BancoCuentaChequera']['banco_cuenta_id']);
			}else{
				$this->Mensaje->errores("ERRORES: ",$this->BancoCuentaChequera->notificaciones);
			}
		endif;		
	}

	function edit($id = null){
		if(empty($id)) $this->redirect('/cajabanco/banco_cuentas');
		if(!empty($this->data)):
			if($this->BancoCuentaSaldo->guardar($this->data)){
				$this->redirect('/cajabanco/banco_cuentas');
			}else{
				$this->Mensaje->errores("ERRORES: ",$this->BancoCuentaSaldo->notificaciones);
			}
		endif;

		$oBANCOCUENTA = $this->BancoCuentaSaldo->importarModelo("BancoCuenta","cajabanco");
		$cuenta = $oBANCOCUENTA->read(null,$id);
		if(empty($cuenta)) $this->redirect('/cajabanco/banco_cuentas');

		if(empty($cuenta['BancoCuenta']['banco_cuenta_saldo_alta_id'])):
			$saldo = array();
			$saldo['BancoCuentaSaldo']['id'] = 0;
			$saldo['BancoCuentaSaldo']['banco_cuenta_id'] = $id;		
			$saldo['BancoCuentaSaldo']['numero'] = 0;
			$saldo['BancoCuentaSaldo']['fecha_cierre'] = $cuenta['BancoCuenta']['fecha_conciliacion'];		
			$saldo['BancoCuentaSaldo']['saldo_anterior'] = 0;
			$saldo['BancoCuentaSaldo']['saldo_referencia_1'] = 0;
			$saldo['BancoCuentaSaldo']['saldo_referencia_2'] = 0;
			$saldo['BancoCuentaSaldo']['saldo_conciliacion'] = $cuenta['BancoCuenta']['importe_conciliacion'];		
			$saldo['BancoCuentaSaldo']['tipo_conciliacion'] = $cuenta['BancoCuenta']['tipo_conciliacion'];		
		else:
			$saldo = $this->BancoCuentaSaldo->read(null,$cuenta['BancoCuenta']['banco_cuenta_saldo_alta_id']);
		endif;
		
		$this->set('banco_cuenta_id',$id);
		$this->set('cuenta',$cuenta);
		$this->set('dato', $saldo);
//		$this->data = $saldo;
	}
	
	function del($id = null){
		if(empty($id)) $this->redirect('/cajabanco/banco_cuentas');
		$chequera = $this->BancoCuentaChequera->read(null,$id);
		if(empty($chequera)) $this->redirect('/cajabanco/banco_cuentas');		
		if (!$this->BancoCuentaChequera->del($id))$this->Mensaje->errorBorrar();
		$this->redirect('index/' . $chequera['BancoCuentaChequera']['banco_cuenta_id']);			
	}	
	
	
	function view_conciliacion($id, $menu=1){
		
//		$this->BancoCuenta = $this->BancoCuentaMovimiento->importarModelo('BancoCuenta', 'cajabanco');
//		$this->BancoCuentaSaldo = $this->BancoCuentaMovimiento->importarModelo('BancoCuentaSaldo', 'cajabanco');

	    $saldos = $this->BancoCuentaSaldo->getSldConciliado($id);
		$cuenta = $this->BancoCuenta->getCuenta($saldos['BancoCuentaSaldo']['banco_cuenta_id']);
		$movimientos = $this->BancoCuentaSaldo->getMovimientosConciliado($id);
		
		$anteriorConciliacion = $this->BancoCuentaSaldo->getAnteriorConciliacion($saldos['BancoCuentaSaldo']['banco_cuenta_id']);
		
		$cuenta['BancoCuenta']['fecha_conciliacion'] = $anteriorConciliacion[0]['BancoCuentaSaldo']['fecha_cierre'];
		$cuenta['BancoCuenta']['importe_conciliacion'] = ($saldos['BancoCuentaSaldo']['saldo_anterior'] < 0 ? $saldos['BancoCuentaSaldo']['saldo_anterior'] * (-1) : $saldos['BancoCuentaSaldo']['saldo_anterior']);
		$cuenta['BancoCuenta']['tipo_conciliacion'] = ($saldos['BancoCuentaSaldo']['saldo_anterior'] < 0 ? 1 : 0);
		
		$this->set('cuenta', $cuenta);
		$this->set('movimientos', $movimientos);
		$this->set('saldos', $saldos);
		$this->set('menu', $menu);
		
	}
	
	
	function abrir_conciliacion($banco_cuenta_saldo_id, $banco_cuenta_id){
		if(!$this->BancoCuentaSaldo->abrirConciliacion($banco_cuenta_saldo_id)):
    		$this->Mensaje->error();
		else:
    		$this->Mensaje->ok();
		endif;
		
		$this->redirect('saldo_conciliado/' . $banco_cuenta_id);
		
	}
	
	function saldo_conciliado($id=null){
            if(empty($id)) $this->redirect('index');

    //    	$this->BancoCuenta = $this->BancoCuentaMovimiento->importarModelo('BancoCuenta', 'cajabanco');
    //    	$this->BancoCuentaSaldo = $this->BancoCuentaMovimiento->importarModelo('BancoCuentaSaldo', 'cajabanco');

            $cuenta = $this->BancoCuenta->getCuenta($id);
            $conciliaciones = $this->BancoCuentaSaldo->saldosConciliados($id);

            $this->set('cuenta', $cuenta);
            $this->set('conciliaciones', $conciliaciones);

	}
	
	
	function planillas($id=null){
            if(empty($id)) $this->redirect('index');

            $this->BancoCuentaSaldo = $this->BancoCuentaSaldo->importarModelo('BancoCuentaSaldo', 'cajabanco');

            $cuenta = $this->BancoCuenta->getCuenta($id);
            $planillas = $this->BancoCuentaSaldo->saldosConciliados($id);

            $this->set('cuenta', $cuenta);
            $this->set('planillas', $planillas);
		
	}
	

	
	
	function abrir_planilla($banco_cuenta_saldo_id, $banco_cuenta_id){
            if(!$this->BancoCuentaSaldo->abrirPlanilla($banco_cuenta_saldo_id)):
                $this->Mensaje->errorBorrar();
            else:
                $this->Mensaje->okBorrar();
            endif;
		
            $this->redirect('planillas/' . $banco_cuenta_id);
		
	}
	
	
	function view_planilla_caja($banco_cuenta_saldo_id, $menu=1){
            $showAsincrono = 1;
            $planilla = $this->BancoCuentaSaldo->read(null, $banco_cuenta_saldo_id);
            $planillaAnterior = $this->BancoCuentaSaldo->planillaAnterior($banco_cuenta_saldo_id);
            $cuenta = $this->BancoCuenta->getCuenta($planilla['BancoCuentaSaldo']['banco_cuenta_id']);
		
            $cuenta['BancoCuenta']['fecha_conciliacion'] = $planillaAnterior['BancoCuentaSaldo']['fecha_cierre'];
            $cuenta['BancoCuenta']['importe_conciliacion'] = ($planillaAnterior['BancoCuentaSaldo']['tipo_conciliacion'] == 1 ? $planillaAnterior['BancoCuentaSaldo']['saldo_conciliacion'] * (-1) : $planillaAnterior['BancoCuentaSaldo']['saldo_conciliacion']);
            $cuenta['BancoCuenta']['tipo_conciliacion'] = $planillaAnterior['BancoCuentaSaldo']['tipo_conciliacion'];
            $cuenta['BancoCuenta']['banco_cuenta_saldo_id'] = $planillaAnterior['BancoCuentaSaldo']['id'];
            $cuenta['BancoCuenta']['numero_extracto'] = $planilla['BancoCuentaSaldo']['numero_extracto'];
	    $cuenta['BancoCuenta']['fecha_extracto'] = $planilla['BancoCuentaSaldo']['fecha_extracto'];
	    $cuenta['BancoCuenta']['saldo_extracto'] = $planilla['BancoCuentaSaldo']['saldo_extracto'];
		
	    if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])):

		$this->ChequeTercero = $this->BancoCuentaMovimiento->importarModelo('BancoChequeTercero', 'cajabanco');
			
		$aAsincrono = $this->Asincrono->find('all', array('conditions' => array('Asincrono.id' => $this->params['url']['pid'])));
		$aTemporal = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']), 'order' => array('AsincronoTemporal.entero_2', 'AsincronoTemporal.texto_9', 'AsincronoTemporal.entero_1')));
		$aDetalle = $this->AsincronoTemporalDetalle->find('all', array('conditions' => array('AsincronoTemporalDetalle.asincrono_id' => $this->params['url']['pid']), 'order' => array('AsincronoTemporalDetalle.entero_2', 'AsincronoTemporalDetalle.texto_9', 'AsincronoTemporalDetalle.entero_1')));
			
		$ingreso = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'], 'AsincronoTemporal.entero_2' => 0), 'fields' => array('SUM(AsincronoTemporal.decimal_1) as ingreso')));
		$ingresoCheque = $this->AsincronoTemporalDetalle->find('all', array('conditions' => array('AsincronoTemporalDetalle.asincrono_id' => $this->params['url']['pid'], 'AsincronoTemporalDetalle.entero_3' => 1), 'fields' => array('SUM(AsincronoTemporalDetalle.decimal_1) as ingreso')));
		$egreso = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'], 'AsincronoTemporal.entero_2' => 1), 'fields' => array('SUM(AsincronoTemporal.decimal_1) as egreso')));
		$id = $aAsincrono[0]['Asincrono']['p1'];
			
		$nroDesde = date(N, strtotime($cuenta['BancoCuenta']['fecha_conciliacion'])); 
		$nroHasta = date(N, strtotime($cuenta['BancoCuenta']['fecha_extracto']));

		$nroDesde = date(N, strtotime($cuenta['BancoCuenta']['fecha_conciliacion'])); 
		$nroHasta = date(N, strtotime($cuenta['BancoCuenta']['fecha_extracto']));
			
		$dias = $this->BancoCuentaMovimiento->datediff(d, $cuenta['BancoCuenta']['fecha_conciliacion'], $cuenta['BancoCuenta']['fecha_extracto']);

		if($dias > 1):
                    if($nroDesde == 5 && $nroHasta == 1) $dias = 1;
		endif;
			
		$showAsincrono = 0;
		$showTabla = 1;
			
		$saldo_cheque_inicial = $this->ChequeTercero->getSaldoCheque($cuenta['BancoCuenta']['fecha_conciliacion']);
			
		$this->set('temporal', $aTemporal);
		$this->set('detalle', $aDetalle);
		$this->set('showTabla', $showTabla);
		$this->set('dias', $dias);
		$this->set('ingreso', $ingreso[0][0]['ingreso']);
		$this->set('egreso', $egreso[0][0]['egreso']);
		$this->set('ingresoCheque', $ingresoCheque[0][0]['ingreso']);
		$this->set('saldo_cheque_inicial', $saldo_cheque_inicial);
		$this->set('asincrono_id', $this->params['url']['pid']);
            endif;
		
		
            $this->set('cuenta', $cuenta);
            $this->set('BancoCuentaId', $planilla['BancoCuentaSaldo']['banco_cuenta_id']);
            $this->set('banco_cuenta_saldo_id', $banco_cuenta_saldo_id);
            $this->set('show_asincrono',$showAsincrono);
            $this->set('fecha_cierre',$planilla['BancoCuentaSaldo']['fecha_cierre']);
            $this->set('planilla_numero', $planilla['BancoCuentaSaldo']['numero']);
		
        }
	
	
	function libro_caja($id=null){
            if(empty($id)) $this->redirect('index');

//            $this->BancoCuentaSaldo = $this->BancoCuentaSaldo->importarModelo('BancoCuentaSaldo', 'cajabanco');
// ALTER TABLE `co_ejercicios` ADD COLUMN `activo` TINYINT(1) DEFAULT 0 NULL AFTER `fecha_proceso`; 

//            $ejercicio_vigente = $this->BancoCuentaSaldo->getGlobalDato('entero_1', 'CONTEVIG');
            
//            $ejercicio = $this->Ejercicio->traeEjercicio($ejercicio_vigente['GlobalDato']['entero_1']);
            $ejercicio = $this->Ejercicio->traeEjercVigente();
debug($this->Ejercicio->getEjercicio($ejercicio['id']));

            $fecha_desde = $ejercicio['fecha_desde'];
            $fecha_hasta = $ejercicio['fecha_hasta'];
            $planilla_desde = $planilla_hasta = 0;
            $disable_form = 0;

            $cuenta = $this->BancoCuenta->getCuenta($id);

            if(!empty($this->data)):
                $fecha_desde = $this->BancoCuentaSaldo->armaFecha($this->data['LibroCaja']['fecha_desde']);
                $fecha_hasta = $this->BancoCuentaSaldo->armaFecha($this->data['LibroCaja']['fecha_hasta']);
                $planilla_desde = $this->BancoCuentaSaldo->getPrmPlanillaFecha($id, $fecha_desde);
                $planilla_hasta = $this->BancoCuentaSaldo->getUltPlanillaFecha($id, $fecha_hasta);
                $disable_form = 1;
            endif;

            if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])):

                $this->ChequeTercero = $this->BancoCuentaMovimiento->importarModelo('BancoChequeTercero', 'cajabanco');

                $aAsincrono = $this->Asincrono->find('all', array('conditions' => array('Asincrono.id' => $this->params['url']['pid'])));
                $aTemporal = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']), 'order' => array('AsincronoTemporal.entero_2', 'AsincronoTemporal.texto_9', 'AsincronoTemporal.entero_1')));
                $aDetalle = $this->AsincronoTemporalDetalle->find('all', array('conditions' => array('AsincronoTemporalDetalle.asincrono_id' => $this->params['url']['pid']), 'order' => array('AsincronoTemporalDetalle.entero_3', 'AsincronoTemporalDetalle.entero_2', 'AsincronoTemporalDetalle.texto_9', 'AsincronoTemporalDetalle.entero_1')));

                $ingreso = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'], 'AsincronoTemporal.entero_2' => 0), 'fields' => array('SUM(AsincronoTemporal.decimal_1) as ingreso')));
                $ingresoCheque = $this->AsincronoTemporalDetalle->find('all', array('conditions' => array('AsincronoTemporalDetalle.asincrono_id' => $this->params['url']['pid'], 'AsincronoTemporalDetalle.entero_3' => 1), 'fields' => array('SUM(AsincronoTemporalDetalle.decimal_1) as ingreso')));
                $egreso = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'], 'AsincronoTemporal.entero_2' => 1), 'fields' => array('SUM(AsincronoTemporal.decimal_1) as egreso')));
                $id = $aAsincrono[0]['Asincrono']['p1'];
                $cuenta = $this->BancoCuenta->getCuenta($id);

                $nroDesde = date(N, strtotime($cuenta['BancoCuenta']['fecha_conciliacion'])); 
                $nroHasta = date(N, strtotime($cuenta['BancoCuenta']['fecha_extracto']));

                $dias = $this->BancoCuentaMovimiento->datediff(d, $cuenta['BancoCuenta']['fecha_conciliacion'], $cuenta['BancoCuenta']['fecha_extracto']);

                if($dias > 1):
                    if($nroDesde == 5 && $nroHasta == 1) $dias = 1;
                endif;

                $showAsincrono = 0;
                $showTabla = 1;
                $disableForm = 1;

                $saldo_cheque_inicial = $this->ChequeTercero->getSaldoCheque($cuenta['BancoCuenta']['fecha_conciliacion']);

                $this->set('temporal', $aTemporal);
                $this->set('detalle', $aDetalle);
                $this->set('showTabla', $showTabla);
                $this->set('dias', $dias);
                $this->set('ingreso', $ingreso[0][0]['ingreso']);
                $this->set('egreso', $egreso[0][0]['egreso']);
                $this->set('ingresoCheque', $ingresoCheque[0][0]['ingreso']);
                $this->set('saldo_cheque_inicial', $saldo_cheque_inicial);
                $this->set('asincrono_id', $this->params['url']['pid']);
            endif;
		
            $this->set('cuenta', $cuenta);
            $this->set('fecha_desde', $fecha_desde);
            $this->set('fecha_hasta', $fecha_hasta);
            
            $this->set('planilla_desde', $planilla_desde);
            $this->set('planilla_hasta', $planilla_hasta);
            $this->set('disable_form', $disable_form);
            $this->set('banco_cuenta', $id);
            
		
	}
}
?>
