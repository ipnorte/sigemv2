<?php 
class BancoListadosController extends CajabancoAppController{
	
	var $name = 'BancoListados';
	var $uses =  array('Mutual.ListadoService','Shells.Asincrono', 'Shells.AsincronoTemporal', 'Shells.AsincronoTemporalDetalle');
	
	var $autorizar = array('listado_concepto', 'salida', 'planilla_caja_salida', 'conciliacion_salida', 'libro_banco', 'libro_banco_salida');
	
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}		
	
	function index(){
		$this->redirect('listado_concepto');
	}
	
	
	
	function listado_concepto() {
		$disableForm = 0;
		$showAsincrono = 0;
		$showTabla = 0;
		
		$this->set('fecha_desde',date('Y-m-d'));
		$this->set('fecha_hasta',date('Y-m-d'));
		
		
		if(!empty($this->data)){
			$disableForm = 1;
			$showAsincrono = 1;
			$this->set('fecha_desde',$this->ListadoService->armaFecha($this->data['ListadoService']['fecha_desde']));
			$this->set('fecha_hasta',$this->ListadoService->armaFecha($this->data['ListadoService']['fecha_hasta']));
		}
		
		if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])):

			$aTemporal = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'])));
			$aAsincrono = $this->Asincrono->find('all', array('conditions' => array('Asincrono.id' => $this->params['url']['pid'])));


			$aCmbConcepto = $this->ComboConcepto($this->params['url']['pid']);
			$aCmbBanco = $this->ComboBanco($this->params['url']['pid']);
			$showAsincrono = 0;
			$showTabla = 1;
			$disableForm = 1;
			$this->set('fecha_desde',$aAsincrono[0]['Asincrono']['p1']);
			$this->set('fecha_hasta',$aAsincrono[0]['Asincrono']['p2']);
			
			
			$this->set('asincrono_id', $this->params['url']['pid']);
			$this->set('aDatos', $aTemporal);
			$this->set('aCmbConcepto', $aCmbConcepto);
			$this->set('aCmbBanco', $aCmbBanco);
		endif;
		$this->set('disable_form',$disableForm);
		$this->set('show_tabla',$showTabla);
		$this->set('show_asincrono',$showAsincrono);
		
		
		
	}

	
	function ComboConcepto($id){
		$sql = "SELECT	CONCAT(clave_1, texto_11) AS clave_1, texto_1
				FROM	asincrono_temporales
				WHERE	asincrono_id = $id AND texto_1 IS NOT NULL AND clave_1 IS NOT NULL
				GROUP	BY texto_1";
		
		$cmbConcepto = $this->Asincrono->query($sql);
		
		$aCmbConcepto = array('0' => 'CONCEPTO ...');
		foreach($cmbConcepto as $concepto):
			$aCmbConcepto[$concepto[0]['clave_1']] = $concepto['asincrono_temporales']['texto_1'];
		endforeach;
		
		
		return $aCmbConcepto;
	}

	
	function ComboBanco($id){
		$sql = "SELECT	clave_2, texto_2
				FROM	asincrono_temporales
				WHERE	asincrono_id = $id AND texto_2 IS NOT NULL
				GROUP	BY texto_2";
		
		$cmbBanco = $this->Asincrono->query($sql);
		

		
		$aCmbBanco = array('0' => 'CAJA Y BANCOS ...');
		foreach($cmbBanco as $banco):
			$aCmbBanco[$banco['asincrono_temporales']['clave_2']] = $banco['asincrono_temporales']['texto_2'];
		endforeach;
		
		
		
		return $aCmbBanco;
	}
	
	
	function salida($tipo_salida='PDF', $concepto=0, $banco=0, $id){
		
//		$aConcepto = $this->getConcepto($concepto, $banco);
		$conditions = array('AsincronoTemporal.asincrono_id' => $id, 'NOT' => array('AsincronoTemporal.texto_3' => null));
		
		if($concepto != 0 && $banco == 0) $conditions['CONCAT(AsincronoTemporal.clave_1,AsincronoTemporal.texto_11)'] = $concepto;

		if($concepto == 0 && $banco != 0) $conditions['AsincronoTemporal.clave_2'] = $banco;
		
		if($concepto != 0 && $banco != 0): 
			$conditions['CONCAT(AsincronoTemporal.clave_1,AsincronoTemporal.texto_11)'] = $concepto;
			$conditions['AsincronoTemporal.clave_2'] = $banco;
		endif;
		
		$aTemporal = $this->AsincronoTemporal->find('all', array('conditions' => $conditions));

		$aAsincrono = $this->Asincrono->find('all', array('conditions' => array('Asincrono.id' => $id)));
		$this->set('fecha_desde',$aAsincrono[0]['Asincrono']['p1']);
		$this->set('fecha_hasta',$aAsincrono[0]['Asincrono']['p2']);
		$this->set('aDatos', $aTemporal);

		if($tipo_salida == 'PDF'):
			$this->render('listado_concepto_tesoreria_pdf','pdf');
		elseif($tipo_salida == 'XLS'):			
			$this->render('listado_concepto_tesoreria_xls','blank');
			return true;
		else:
			parent::noDisponible();
		endif;
		
	}
	
	
	function planilla_caja_salida($id, $tipo_salida='XLS', $oficial=0){
		$this->ChequeTercero = $this->Asincrono->importarModelo('BancoChequeTercero', 'cajabanco');
		$this->BancoCuenta = $this->Asincrono->importarModelo('BancoCuenta', 'cajabanco');
		$this->BancoCuentaMovimiento = $this->Asincrono->importarModelo('BancoCuentaMovimiento', 'cajabanco');
		$this->BancoCuentaSaldo = $this->Asincrono->importarModelo('BancoCuentaSaldo', 'cajabanco');
			
		$aAsincrono = $this->Asincrono->find('all', array('conditions' => array('Asincrono.id' => $id)));
		$aTemporal = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $id), 'order' => array('AsincronoTemporal.entero_2', 'AsincronoTemporal.texto_9', 'AsincronoTemporal.entero_1')));
		$aDetalle = $this->AsincronoTemporalDetalle->find('all', array('conditions' => array('AsincronoTemporalDetalle.asincrono_id' => $id), 'order' => array('AsincronoTemporalDetalle.entero_2', 'AsincronoTemporalDetalle.texto_9', 'AsincronoTemporalDetalle.entero_1')));
			
		$ingreso = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $id, 'AsincronoTemporal.entero_2' => 0), 'fields' => array('SUM(AsincronoTemporal.decimal_1) as ingreso')));
 		$ingresoCheque = $this->AsincronoTemporalDetalle->find('all', array('conditions' => array('AsincronoTemporalDetalle.asincrono_id' => $id, 'AsincronoTemporalDetalle.entero_3' => 1), 'fields' => array('SUM(AsincronoTemporalDetalle.decimal_1) as ingreso')));
		$egreso = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $id, 'AsincronoTemporal.entero_2' => 1), 'fields' => array('SUM(AsincronoTemporal.decimal_1) as egreso')));
		$banco_id = $aAsincrono[0]['Asincrono']['p1'];
		$cuenta = $this->BancoCuenta->getCuenta($banco_id);
		
		$nroDesde = date(N, strtotime($cuenta['BancoCuenta']['fecha_conciliacion'])); 
		$nroHasta = date(N, strtotime($cuenta['BancoCuenta']['fecha_extracto']));

		if($aAsincrono[0]['Asincrono']['p3'] != '-1'):
			$cuentaSaldo = $this->BancoCuentaSaldo->read(null, $aAsincrono[0]['Asincrono']['p3']);
			$planillaAnterior = $this->BancoCuentaSaldo->planillaAnterior($aAsincrono[0]['Asincrono']['p3']);

			$cuenta['BancoCuenta']['fecha_conciliacion'] = $planillaAnterior['BancoCuentaSaldo']['fecha_cierre'];
			$cuenta['BancoCuenta']['importe_conciliacion'] = ($planillaAnterior['BancoCuentaSaldo']['tipo_conciliacion'] == 1 ? $planillaAnterior['BancoCuentaSaldo']['saldo_conciliacion'] * (-1) : $planillaAnterior['BancoCuentaSaldo']['saldo_conciliacion']);
			$cuenta['BancoCuenta']['tipo_conciliacion'] = $planillaAnterior['BancoCuentaSaldo']['tipo_conciliacion'];
			$cuenta['BancoCuenta']['banco_cuenta_saldo_id'] = $planillaAnterior['BancoCuentaSaldo']['id'];
			$cuenta['BancoCuenta']['numero_extracto'] = $cuentaSaldo['BancoCuentaSaldo']['numero_extracto'];
		    $cuenta['BancoCuenta']['fecha_extracto'] = $cuentaSaldo['BancoCuentaSaldo']['fecha_extracto'];
		    $cuenta['BancoCuenta']['saldo_extracto'] = $cuentaSaldo['BancoCuentaSaldo']['saldo_extracto'];
		endif;
		
		$nroDesde = date(N, strtotime($cuenta['BancoCuenta']['fecha_conciliacion'])); 
		$nroHasta = date(N, strtotime($cuenta['BancoCuenta']['fecha_extracto']));
		
		$dias = $this->BancoCuentaMovimiento->datediff(d, $cuenta['BancoCuenta']['fecha_conciliacion'], $cuenta['BancoCuenta']['fecha_extracto']);

		if($dias > 1):
			if($nroDesde == 5 && $nroHasta == 1) $dias = 1;
		endif;
			
		$saldo_cheque_inicial = $this->ChequeTercero->getSaldoCheque($cuenta['BancoCuenta']['fecha_conciliacion']);

		$this->set('temporal', $aTemporal);
		$this->set('detalle', $aDetalle);
		$this->set('dias', $dias);
		$this->set('ingreso', $ingreso[0][0]['ingreso']);
		$this->set('egreso', $egreso[0][0]['egreso']);
		$this->set('ingresoCheque', $ingresoCheque[0][0]['ingreso']);
		$this->set('saldo_cheque_inicial', $saldo_cheque_inicial);
		$this->set('asincrono_id', $id);
		$this->set('cuenta', $cuenta);

		if($tipo_salida == 'PDF'):
			if($oficial == 0):
				$this->render('planilla_caja_pdf','pdf');
			else:
				$this->render('planilla_caja_final_pdf','pdf');
			endif;
		elseif($tipo_salida == 'XLS'):			
			$this->render('planilla_caja_xls','blank');
			return true;
		else:
			parent::noDisponible();
		endif;
	}


	function conciliacion_salida($id, $salida='XLS'){
		
		$this->BancoCuenta = $this->Asincrono->importarModelo('BancoCuenta', 'cajabanco');
		$this->BancoCuentaSaldo = $this->Asincrono->importarModelo('BancoCuentaSaldo', 'cajabanco');

	    $saldos = $this->BancoCuentaSaldo->getSldConciliado($id);
		$cuenta = $this->BancoCuenta->getCuenta($saldos['BancoCuentaSaldo']['banco_cuenta_id']);
		$movimientos = $this->BancoCuentaSaldo->getMovimientosConciliado($id);
		
		$cuenta['BancoCuenta']['importe_conciliacion'] = ($saldos['BancoCuentaSaldo']['saldo_anterior'] < 0 ? $saldos['BancoCuentaSaldo']['saldo_anterior'] * (-1) : $saldos['BancoCuentaSaldo']['saldo_anterior']);
		$cuenta['BancoCuenta']['tipo_conciliacion']    = ($saldos['BancoCuentaSaldo']['saldo_anterior'] < 0 ? 1 : 0);
		
		$this->set('cuenta', $cuenta);
		$this->set('movimientos', $movimientos);
		$this->set('saldos', $saldos);
		
		if($salida == 'PDF'):
			$this->render('conciliacion_pdf', 'pdf');
		else:
			$this->render('conciliacion_xls', 'blank');
		endif;
		
	}


	function libro_banco($id=null){
            if(empty($id)) $this->redirect('index');
		
            $this->BancoCuenta = $this->Asincrono->importarModelo('BancoCuenta', 'cajabanco');
            $this->BancoCuentaSaldo = $this->Asincrono->importarModelo('BancoCuentaSaldo', 'cajabanco');
            $this->BancoCuentaMovimiento = $this->Asincrono->importarModelo('BancoCuentaMovimiento', 'cajabanco');
		
            $cuenta = $this->BancoCuenta->getCuenta($id);
	    $saldos = $this->BancoCuentaSaldo->getSldConciliado($cuenta['BancoCuenta']['banco_cuenta_saldo_alta_id']);
		
	    $fecha_desde = $saldos['BancoCuentaSaldo']['fecha_cierre'];
	    $fecha_hasta = date('Y-m-d');
	    $showForm = 0;
    	
            if(!empty($this->data)):
	    	$showForm = 1;
	    	$fecha_desde = $this->BancoCuenta->armaFecha($this->data['Listado']['fecha_desde']);
	    	$fecha_hasta = $this->BancoCuenta->armaFecha($this->data['Listado']['fecha_hasta']);
	    	$saldo_anterior = $this->BancoCuentaSaldo->getSaldoFechaAnterior($id, $this->BancoCuenta->armaFecha($this->data['Listado']['fecha_desde']));
                $movimientos = $this->BancoCuentaMovimiento->getLibroBanco($id, $this->BancoCuenta->armaFecha($this->data['Listado']['fecha_desde']), $this->BancoCuenta->armaFecha($this->data['Listado']['fecha_hasta']), true);

                $this->set('movimientos', $movimientos);
    		$cuenta['BancoCuenta']['importe_conciliacion'] = ($saldo_anterior < 0 ? $saldo_anterior * (-1) : $saldo_anterior);
    		$cuenta['BancoCuenta']['tipo'] = ($saldo_anterior < 0 ? 1 : 0);
    		$fecha_anterior = date("Y-m-d", strtotime("$fecha_desde -1 day"));
    		$cuenta['BancoCuenta']['fecha_conciliacion'] = $fecha_anterior;
            endif;
    	
            $this->set('fecha_desde', $fecha_desde);
            $this->set('fecha_hasta', $fecha_hasta);
            $this->set('cuenta', $cuenta);
            $this->set('showForm', $showForm);
	}


	function libro_banco_salida($id, $fecha_desde, $fecha_hasta, $tipo_salida='XLS'){
		
            $this->BancoCuenta = $this->Asincrono->importarModelo('BancoCuenta', 'cajabanco');
            $this->BancoCuentaSaldo = $this->Asincrono->importarModelo('BancoCuentaSaldo', 'cajabanco');
            $this->BancoCuentaMovimiento = $this->Asincrono->importarModelo('BancoCuentaMovimiento', 'cajabanco');

            $cuenta = $this->BancoCuenta->getCuenta($id);
            $saldos = $this->BancoCuentaSaldo->getSldConciliado($cuenta['BancoCuenta']['banco_cuenta_saldo_alta_id']);

            $saldo_anterior = $this->BancoCuentaSaldo->getSaldoFechaAnterior($id, $fecha_desde);
//            $movimientos = $this->BancoCuentaMovimiento->getMovimientosEntreFecha($id, $fecha_desde, $fecha_hasta, true);
            $movimientos = $this->BancoCuentaMovimiento->getLibroBanco($id, $fecha_desde, $fecha_hasta, true);

            $cuenta['BancoCuenta']['importe_conciliacion'] = ($saldo_anterior < 0 ? $saldo_anterior * (-1) : $saldo_anterior);
            $cuenta['BancoCuenta']['tipo'] = ($saldo_anterior < 0 ? 1 : 0);
            $fecha_anterior = date("Y-m-d", strtotime("$fecha_desde -1 day"));
            $cuenta['BancoCuenta']['fecha_conciliacion'] = $fecha_anterior;

            $this->set('fecha_desde', $fecha_desde);
            $this->set('fecha_hasta', $fecha_hasta);
            $this->set('cuenta', $cuenta);
            $this->set('movimientos', $movimientos);


            if($tipo_salida == 'PDF'):
                $this->render('libro_banco_pdf', 'pdf');
            else:
                $this->render('libro_banco_xls', 'blank');
            endif;
	}
}

?>