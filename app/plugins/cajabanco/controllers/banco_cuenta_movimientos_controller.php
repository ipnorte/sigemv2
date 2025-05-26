<?php
class BancoCuentaMovimientosController extends CajabancoAppController{
	var $name = 'BancoCuentaMovimientos';
	var $uses = array('cajabanco.BancoCuenta', 'cajabanco.BancoCuentaMovimiento', 'Mutual.ListadoService','Shells.Asincrono', 'Shells.AsincronoTemporal', 'Shells.AsincronoTemporalDetalle');
	
	var $autorizar = array('resumen', 'registracion', 'conciliacion', 'saldo_conciliado', 'cierre_caja', 'planillas', 
							'reemplazar_cheque', 'edit_comprobante', 'imprimir_comprobante_pdf', 'delete', 'grabar_movimiento', 
							'view_conciliacion', 'abrir_conciliacion', 'efectivo_cheque_cartera', 'edit_fecha');
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	

	function index(){
		$this->paginate = array(
									'limit' => 30,
									'order' => array('Banco.nombre' => 'ASC')
									);		
		$this->set('cuentas',$this->paginate());
	}

	
	function edit($id=null){
    	if(empty($id)) $this->redirect('index');
		
    	$this->redirect('edit_comprobante/' . $id);
    	if(!empty($this->data)):
//    		if($this->BancoCuentaMovimiento->registracion($this->data)):
//				$this->redirect('/cajabanco/banco_cuenta_movimientos/resumen/'.$this->data['BancoCuentaMovimiento']['banco_cuenta_id']);
//    			$this->Mensaje->okGuardar();
//    		else:
//				$this->Mensaje->errorGuardar();
//    		endif;
    	endif;

    	$movimiento = $this->BancoCuentaMovimiento->getMovimientoId($id);
    	$this->set('movimiento', $movimiento[0]);
	}
	
	
	function resumen($id=null){
    	if(empty($id)) $this->redirect('index');
		
    	$cuenta = $this->BancoCuenta->getCuenta($id);
    	$movimientos = $this->BancoCuentaMovimiento->getMovimiento($cuenta, false, true);
		
    	$this->set('cuenta', $cuenta);
    	$this->set('movimientos', $movimientos);
	}

	function registracion($id=null){
    	if(empty($id)) $this->redirect('index');
		
    	if(!empty($this->data)):
    		$nId = $this->BancoCuentaMovimiento->registracion($this->data);
    		if(!$nId):
    			$this->Mensaje->errorGuardar();
    		else:
				$this->Mensaje->okGuardar();
    			$this->redirect('/cajabanco/banco_cuenta_movimientos/edit_comprobante/'.$nId);
    		endif;
    	endif;
    	
    	$this->chqCartera = $this->BancoCuentaMovimiento->importarModelo('BancoChequeTercero', 'cajabanco');
    	$chqCarteras = $this->chqCartera->getChequeCartera();
    	
    	$cuenta = $this->BancoCuenta->getCuenta($id);
    	$combo = $this->BancoCuentaMovimiento->getComboConcepto($cuenta);
//     	$aCheques = $this->BancoCuentaMovimiento->getCheques($id);
    	
    	$this->set('cuenta', $cuenta);
    	$this->set('combo', $combo);
    	$this->set('chqCarteras', $chqCarteras);
//     	$this->set('aCheques', $aCheques);
    	
	}
	
	
	function delete($id = null, $bancoCuentaID){
		if(empty($id)) $this->redirect('resumen/' . $bancoCuentaID);
		if (!$this->BancoCuentaMovimiento->delete($id)):
			$this->Mensaje->errorBorrar();
		else:
			$this->Mensaje->okBorrar();
		endif;
		$this->redirect('resumen/' . $bancoCuentaID);			
	}
	
	
	function conciliacion($id=null){

    	if(empty($id)) $this->redirect('index');
		
	    $cuenta = $this->BancoCuenta->getCuenta($id);
	    $saldos = $this->BancoCuentaMovimiento->getSldLibro($id);
	    $conciliar = 0;
	    $cerrar = 0;
	    
	    if(!empty($this->data)):
	    	$conciliar = 1;
	    	if(isset($this->data['BancoCuenta']['cerrar']) && $this->data['BancoCuenta']['cerrar'] == 1):
	    		if(!$this->BancoCuentaMovimiento->cerrarConciliacion($id)):
	    			$this->Mensaje->errorGuardar();
	    		else:
					$this->Mensaje->okGuardar();
					$cuenta = $this->BancoCuenta->getCuenta($id);
					$this->redirect('/cajabanco/banco_cuenta_saldos/view_conciliacion/' . $cuenta['BancoCuenta']['banco_cuenta_saldo_id']);			
				endif;
	    	else:
		    	if($this->BancoCuenta->save($this->data)):
		    		$cuenta = $this->BancoCuenta->getCuenta($id);
		    		$movimientos = $this->BancoCuentaMovimiento->getMovimiento($cuenta, false, true);
		    		$saldos = $this->BancoCuentaMovimiento->getSldLibro($id);
		    		
			    	$this->set('movimientos', $movimientos);
			    else:
	    		endif;
    		endif;
    	endif;
    	
		$this->set('saldos', $saldos);
    	$this->set('cuenta', $cuenta);
		$this->set('conciliar', $conciliar);    	
    	$this->set('BancoCuentaId', $id);
		$this->set('cerrar', $cerrar);
		    	
	}
	
	
	function cierre_caja($id=null){
//    	if(empty($id)) $this->redirect('index');
		
		$disableForm = 0;
		$showAsincrono = 0;
		
		App::import('Model','mutual.MutualServicio');
		$oSERV = new MutualServicio();
		
		if(!empty($this->data)):
                    if(isset($this->data['BancoCuenta']['cerrar']) && $this->data['BancoCuenta']['cerrar'] == 1):
	    		if(!$this->BancoCuentaMovimiento->cerrarPlanillaCaja($this->data['BancoCuenta']['asincrono_id'])):
	    			$this->Mensaje->errorGuardar();
	    		else:
					$this->Mensaje->okGuardar();
					$cuenta = $this->BancoCuenta->getCuenta($id);
					$this->redirect('/cajabanco/banco_cuenta_saldos/planillas/' . $cuenta['BancoCuenta']['id']);			
			endif;
                    else:
			$this->Recibo = $this->BancoCuentaMovimiento->importarModelo('Recibo', 'clientes');
			
		    	if($this->BancoCuenta->save($this->data)):
					$disableForm = 1;
					$showAsincrono = 1;
					$this->set('fecha_cierre',$this->ListadoService->armaFecha($this->data['BancoCuenta']['fecha_extracto']));
		    	else:
		    		$this->mensaje->errorGuardar();
	    		endif;
                    endif;			
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
		
		$cuenta = $this->BancoCuenta->getCuenta($id);
                $this->set('cuenta', $cuenta);
                $this->set('BancoCuentaId', $id);
                $this->set('disable_form',$disableForm);
		$this->set('show_asincrono',$showAsincrono);
	}
	

	function reemplazar_cheque($id){

    	if(!empty($this->data)):

    		if($this->BancoCuentaMovimiento->reemplazar_cheque($this->data)):
    			$this->Mensaje->okGuardar();
    			$this->redirect('/cajabanco/banco_cuenta_movimientos/resumen/'.$this->data['BancoCuentaMovimiento']['banco_cuenta_id']);
    		else:
				$this->Mensaje->errorGuardar();
    		endif;
    	endif;
    	
		
    	$movimiento = $this->BancoCuentaMovimiento->getMovimientoId($id);
    	$cuenta = $this->BancoCuenta->getCuenta($movimiento[0]['BancoCuentaMovimiento']['banco_cuenta_id']);
    	$this->set('cuenta', $cuenta);
    	$this->set('movimiento', $movimiento[0]['BancoCuentaMovimiento']);
		
	}
	
	
	function edit_comprobante($nId){
		$BancoCuentaMovimiento = $this->BancoCuentaMovimiento->getMovimientoIdEdit($nId);
		$this->set('movimiento', $BancoCuentaMovimiento);		
	}


	function imprimir_comprobante_pdf($nId){

		$BancoCuentaMovimiento = $this->BancoCuentaMovimiento->getMovimientoIdEdit($nId);
		$this->set('movimiento', $BancoCuentaMovimiento);		
		$this->render('imprimir_comprobante_pdf', 'pdf');	
	}


	function grabar_movimiento($movimientoId, $conciliacion, $cuentaId){
		$this->BancoCuentaMovimiento->grabarMovimientoConciliacion($movimientoId, $conciliacion);
	    
	    $saldos = $this->BancoCuentaMovimiento->getSldLibro($cuentaId);
		$this->set('saldos', $saldos);
	    $this->render('saldo_conciliacion_ajax');
	}


	function efectivo_cheque_cartera($id){
		
    	if(empty($id)) $this->redirect('index');
		
    	if(!empty($this->data)):
    		if(!$this->BancoCuentaMovimiento->efectivo_cheque_cartera($this->data)):
    			$this->Mensaje->errorGuardar();
    		else:
				$this->Mensaje->okGuardar();
    			$this->redirect('/cajabanco/banco_cuenta_movimientos/resumen/'.$this->data['BancoCuentaMovimiento']['banco_cuenta_id']);
    		endif;
    	endif;
    	
    	$oChqCartera = $this->BancoCuentaMovimiento->importarModelo('BancoChequeTercero', 'cajabanco');
    	$chqCarteras = $oChqCartera->getChequeCartera();
    	$cuenta = $this->BancoCuenta->getCuenta($id);
    	 
    	$this->set('cuenta', $cuenta);
    	$this->set('chqCarteras', $chqCarteras);
    	
	}
	
	
	function edit_fecha($nId){
		
		$BancoCuentaMovimiento = $this->BancoCuentaMovimiento->getMovimientoId($nId);
		$BancoMovimiento = $this->BancoCuentaMovimiento->getMovimientoIdEdit($nId);
		
		if(!empty($this->data)):
    	
    		if(!$this->BancoCuentaMovimiento->modifica_fecha_comprobante($this->data)):
    			$this->Mensaje->errorGuardar();
    		else:
				$this->Mensaje->okGuardar();
    		endif;

    		$this->redirect('/cajabanco/banco_cuenta_movimientos/resumen/'.$this->data['BancoCuentaMovimiento']['banco_cuenta_id']);
    	endif;
    	
		$this->set('movimiento', $BancoCuentaMovimiento);		
		$this->set('documento', $BancoMovimiento);		
	}


}
?>