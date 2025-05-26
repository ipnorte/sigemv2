<?php
class RecibosController extends ClientesAppController{
	
	var $name = 'Recibos';
	var $dir_up;
	var $dir_url;

	
	var $autorizar = array( 
                            'cargar_renglones',
                            'cargar_renglones_remover',
                            'imprimir_recibo_pdf',
                            'recibos_entre_fecha',
                            'recibos_por_numero',
                            'informe_recibo_fecha',
                            'informe_recibo_numero',
                            'detalle_pago_facturas'
	);
	
	function beforeFilter(){
		
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();
		
		$this->dir_up = 'files' . DS . 'intercambio' . DS;
		$this->dir_url = 'files/intercambio/';		
		
	}


	function index(){
		$this->redirect('recibos_entre_fecha');
	}
	
	
	function recibos_entre_fecha(){
		$disableForm = 0;
		
		$this->set('fecha_desde',date('Y-m-d'));
		$this->set('fecha_hasta',date('Y-m-d'));
		
		if(!empty($this->data)):
			$disableForm = 1;
			$this->set('fecha_desde',$this->Recibo->armaFecha($this->data['Recibo']['fecha_desde']));
			$this->set('fecha_hasta',$this->Recibo->armaFecha($this->data['Recibo']['fecha_hasta']));
			$aReciboFechas = $this->Recibo->recibos_entre_fecha($this->Recibo->armaFecha($this->data['Recibo']['fecha_desde']), $this->Recibo->armaFecha($this->data['Recibo']['fecha_hasta']));
			
//			$this->paginate = array(
//								'limit' => 30,
//								);
//			$this->set('aReciboFechas', $this->paginate(null, $aReciboFechas));
			$this->set('aReciboFechas', $aReciboFechas);
		endif;
		
		$this->set('disable_form',$disableForm);
		
	}
	
	
	function recibos_por_numero(){
		$disableForm = 0;
		
		$this->set('letra','C');
		$this->set('sucursal','0001');
		$this->set('numero_desde','00000001');
		$this->set('numero_hasta','00000001');
		
		if(!empty($this->data)):
			$disableForm = 1;
			$this->set('letra',$this->data['Recibo']['letra']);
			$this->set('sucursal',$this->data['Recibo']['sucursal']);
			$this->set('numero_desde',str_pad($this->data['Recibo']['numero_desde'],8,0,STR_PAD_LEFT));
			$this->set('numero_hasta',str_pad($this->data['Recibo']['numero_hasta'],8,0,STR_PAD_LEFT));
		
			$aReciboNumero = $this->Recibo->recibos_por_numero($this->data);
			
			$this->set('aReciboNumero', $aReciboNumero);
		endif;
		
		$this->set('disable_form',$disableForm);
		
	}
	
	
	function cargar_renglones(){
		Configure::write('debug',0);
		$renglones = array();
		$Ok = true;
		$model = $this->params['data']['Referencia']['modelo'];

		if(!$this->Session->check('grilla_cobros'))$this->Session->write('grilla_cobros',$renglones);
		else $renglones = $this->Session->read('grilla_cobros');	

		if($this->params['data'][$model]['forma_cobro'] == 'CT' && empty($this->params['data'][$model]['numero_cheque'])):
				$Ok = false;
				$msgError = 'Debe Ingresar el Numero de Cheque.';
				$this->set('msgError', $msgError);
		elseif($this->params['data'][$model]['forma_cobro'] == 'DB' && empty($this->params['data'][$model]['numero_deposito'])):
				$Ok = false;
				$msgError = 'Debe Ingresar el Numero de Operacion.';
				$this->set('msgError', $msgError);
		else:
			$this->params['data'][$model]['denominacion'] = 'CAJA';
			if($this->params['data'][$model]['forma_cobro'] == 'DB'):
				$oCuentaBanco = $this->Recibo->importarModelo('BancoCuenta', 'cajabanco');
				$aBancoCuenta = $oCuentaBanco->getCuenta($this->params['data'][$model]['banco_cuenta_id']);
				$this->params['data'][$model]['denominacion'] = $aBancoCuenta['BancoCuenta']['banco'] . ' - ' . $aBancoCuenta['BancoCuenta']['numero'] . ' - ' . $aBancoCuenta['BancoCuenta']['denominacion'];
				$this->params['data'][$model]['numero_operacion'] = $this->params['data'][$model]['numero_deposito'];
				$this->params['data'][$model]['fcobro'] = $this->params['data'][$model]['fdeposito']; 
			endif;
			if($this->params['data'][$model]['forma_cobro'] == 'CT'):
				$banco = $this->Recibo->getBanco($this->params['data'][$model]['banco_id']);
				$this->params['data'][$model]['denominacion'] =  $banco['Banco']['nombre'];
				$this->params['data'][$model]['numero_operacion'] = $this->params['data'][$model]['numero_cheque'];
				$this->params['data'][$model]['fcobro'] = $this->params['data'][$model]['fcheque']; 
			endif;
			$acumulado = 0;
			foreach($renglones as $renglon){
				$acumulado += $renglon[$model]['importe'];
			}
			$acumulado += $this->params['data'][$model]['importe'];
			$acumulado = round($acumulado,2);
			$this->params['data'][$model]['importe_cobro'] = round($this->params['data'][$model]['importe_cobro'],2);
			if($this->params['data'][$model]['importe'] > 0):
				if($acumulado <= $this->params['data'][$model]['importe_cobro']):
					array_push($renglones,$this->data);
				else:
					$Ok = false;
					$msgError = 'El importe del Recibo no puede ser mayor al Importe Cobrado ' . $this->params['data'][$model]['importe_cobro'] . " *** " . $acumulado;
					$this->set('msgError', $msgError);
				endif;
			else: 
				$Ok = false;
				$msgError = 'El importe debe ser positivo';
				$this->set('msgError', $msgError);
			endif;
		endif;
		
		$acumulado = 0;
		foreach($renglones as $renglon){
			$acumulado += $renglon[$model]['importe'];
		}
		$this->Session->write('grilla_cobros',$renglones);		
		$this->set('renglones',$renglones);
		$this->set('acumulado', $acumulado);
		$this->set('Ok', $Ok);
		$this->set('model', $model);
		$this->render('cargar_renglones','ajax');
	}
	
	function cargar_renglones_remover($key,$model='Recibo'){
		Configure::write('debug',0);
		$renglones = $this->Session->read('grilla_cobros');
		if(!empty($renglones)):
	    	array_splice($renglones,$key,1);
	    	if(count($renglones) == 0)$this->Session->del('grilla_cobros');
	    	else $this->Session->write('grilla_cobros',$renglones);
    	endif;
		$acumulado = 0;
		foreach($renglones as $renglon){
			$acumulado += $renglon[$model]['importe'];
		}
    	$this->set('renglones',$renglones);
		$this->set('Ok', true);
    	$this->set('acumulado', $acumulado);
		$this->set('model', $model);
    	$this->render('cargar_renglones','ajax');		
	}


	function imprimir_recibo_pdf($id){
		
		$this->TipoDocumento = $this->Recibo->importarModelo('TipoDocumento', 'Config');
		$aCmpRecibo = $this->TipoDocumento->getComprobante('REC');
		
		// traer Recibo
		$aRecibo = $this->Recibo->getRecibo($id);
		$this->set('aRecibo', $aRecibo);
		$this->set('copias', $aCmpRecibo['copias']);
		$this->render('imprimir_recibo_pdf', 'pdf');	
		
	}


	function informe_recibo_fecha($fecha_desde, $fecha_hasta, $tipo_salida){
//		$datos = array('Recibo' => array('letra' => $letra, 'sucursal' => $sucursal, 'numero_desde' => $numero_desde, 'numero_hasta' => $numero_hasta));

		$aReciboInforme = $this->Recibo->recibos_entre_fecha($fecha_desde, $fecha_hasta);
		
		$this->set('fecha_desde',$fecha_desde);
		$this->set('fecha_hasta',$fecha_hasta);
		$this->set('tipo', 'FECHA');
		
		if($tipo_salida == 'PDF'):
			$this->set('aReciboInforme', $aReciboInforme);
			$this->render('informe_recibo_pdf','pdf');
		elseif($tipo_salida == 'XLS'):			
			$aReciboXLS = array();
			foreach($aReciboInforme as $campos):
				$aTmpRecibo = array(
					'fecha' => $campos['Recibo']['fecha_comprobante'],
					'numero_comp' => $campos['Recibo']['letra'] . '-' . $campos['Recibo']['sucursal'] . '-' . $campos['Recibo']['nro_recibo'],
					'razon_social' => $campos['Recibo']['razon_social'],
					'cuit_documento' => $campos['Recibo']['cuit'],
					'iva_concepto' => $campos['Recibo']['iva_concepto'],
					'importe' => $campos['Recibo']['importe'],
					'comentario' => $campos['Recibo']['comentarios']
				);
				array_push($aReciboXLS, $aTmpRecibo);
			endforeach;		
			$this->set('aReciboInforme', $aReciboXLS);
			$this->render('informe_recibo_xls','blank');
		else:
			parent::noDisponible();
		endif;
		
		
	}


	function informe_recibo_numero($letra, $sucursal, $numero_desde, $numero_hasta, $tipo_salida){
		$datos = array('Recibo' => array('letra' => $letra, 'sucursal' => $sucursal, 'numero_desde' => $numero_desde, 'numero_hasta' => $numero_hasta));

		$aReciboInforme = $this->Recibo->recibos_por_numero($datos);
		
		$this->set('letra','C');
		$this->set('sucursal','0001');
		$this->set('numero_desde','00000001');
		$this->set('numero_hasta','00000001');
		$this->set('tipo', 'NUMERO');
		
		if($tipo_salida == 'PDF'):
			$this->set('aReciboInforme', $aReciboInforme);
			$this->render('informe_recibo_pdf','pdf');
		elseif($tipo_salida == 'XLS'):	
			$aReciboXLS = array();
			foreach($aReciboInforme as $campos):
				$aTmpRecibo = array(
					'fecha' => $campos['Recibo']['fecha_comprobante'],
					'numero_comp' => $campos['Recibo']['letra'] . '-' . $campos['Recibo']['sucursal'] . '-' . $campos['Recibo']['nro_recibo'],
					'razon_social' => $campos['Recibo']['razon_social'],
					'cuit_documento' => $campos['Recibo']['cuit'],
					'iva_concepto' => $campos['Recibo']['iva_concepto'],
					'importe' => $campos['Recibo']['importe'],
					'comentario' => $campos['Recibo']['comentarios']
				);
				array_push($aReciboXLS, $aTmpRecibo);
			endforeach;		
			$this->set('aReciboInforme', $aReciboXLS);
			$this->render('informe_recibo_xls','blank');
		else:
			parent::noDisponible();
		endif;
		
				
	}

        function detalle_pago_facturas($cliente_factura_id){
            $oClnFactura = $this->Recibo->importarModelo('ClienteFactura', 'clientes');
            $oRecFactura = $this->Recibo->importarModelo('ReciboFactura', 'clientes');

            $aClntFct = $oClnFactura->getFactura($cliente_factura_id);
            $aDetalleFactura = $oRecFactura->DetallePagoFacturas($cliente_factura_id);

            $this->set('aClntFct', $aClntFct);
            $this->set('aDetalleFactura', $aDetalleFactura);
        }
}
?>