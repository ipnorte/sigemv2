<?php
class ClientesController extends ClientesAppController{
	var $name = 'Clientes';
	var $uses = array('Clientes.Cliente', 'Clientes.ClienteCtacte');
	
	
	var $autorizar = array('cta_cte', 'recibo', 'anular_recibo', 'imprimir_recibo_pdf', 'editRecibo', 'view_recibo', 'facturas', 
            'cargar_factura_detalle', 'remover_factura_detalle', 'pendientes', 'compensar_pagos', 'view_ctacte');
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	

	
	function index(){

		if(!empty($this->data)){
			$this->Session->del($this->name.'.search');
			$search = $this->data;
				
		}else if($this->Session->check($this->name.'.search')){
				
			$search = $this->Session->read($this->name.'.search');
			$this->data = $search;
				 
		}

		if(!empty($this->data)):
				$condiciones = array(
									'Cliente.cuit  LIKE ' => $search['Cliente']['cuit'] ."%",
									'Cliente.razon_social LIKE ' => "%" . $search['Cliente']['razon_social']."%",
								);					
		endif;
		
		$this->Session->write($this->name.'.search', $search);
		
		$this->paginate = array(
								'limit' => 50,
								'order' => array('Cliente.razon_social' => 'ASC')
								);
									
		$this->set('clientes', $this->paginate(null, $condiciones));
		$this->render('index');
	}

	
	function add(){
		if(!empty($this->data)):
			$id = $this->Cliente->grabar($this->data);
			if($id != 0):
				$this->Mensaje->okGuardar();
			   	$this->redirect('index');
			else:
	    		$this->Mensaje->errorGuardar();
			   	$this->redirect('index');
	    	endif;
		endif;
	}
	

	function edit($id = null){
		if(!empty($this->data)){
			if($this->Cliente->grabar($this->data)):
				$this->Mensaje->okGuardar();
				$this->redirect('index');
			else:
	    		$this->Mensaje->errorGuardar();
				$this->redirect('index');
	    	endif;
//			$id = $this->data['Cliente']['id'];
		}		
		if(empty($id)) parent::noAutorizado();
		$this->data = $this->Cliente->read(null,$id);
		
    	$this->data['Cliente']['estado'] = $this->Cliente->getEstado($this->data);
	}
	

	function cta_cte($id=null){
            if(empty($id)) $this->redirect('index');

	    $cliente = $this->Cliente->getCliente($id);
    	
            $ctaCte = $this->Cliente->armaCtaCte($id);
            
            $this->redirect('view_ctacte/' . $id);
            $this->set('ctaCte', $ctaCte);
            $this->set('cliente', $cliente);

	}

        
        function view_ctacte($id){
	    $cliente = $this->Cliente->getCliente($id);
            $this->paginate = array(
                        'limit' => 50,
                        'order' => array('ClienteCtacte.item' => 'DESC')
                        );

// debug($this->ProveedorCtacte->paginate(null, array('ProveedorCtacte.proveedor_id' => $id)));
// exit;

            $this->set('ctaCte', $this->paginate('ClienteCtacte', array('ClienteCtacte.cliente_id' => $id)));
            $this->set('cliente', $cliente);			

        }
        
	
	function recibo($id=null){
		$this->Session->del('grilla_cobros');
		
		if(empty($id)) $this->redirect('index');

    	$bloquear = 0;
    	$lFacturaPendiente = false;
    	
    	if(!empty($this->data)):
			if(isset($this->data['Recibo']['Cabecera'])):
    			$bloquear = 1;
    			$lFacturaPendiente = true;
    		elseif(isset($this->data['Recibo']['detalle_cobro'])):
    			$this->Recibo = $this->Cliente->importarModelo('Recibo', 'clientes');
    			$ReciboId = $this->Cliente->guardarRecibo($this->data);
    			if(!$ReciboId):
					$this->Mensaje->errores("ERRORES:",$this->Cliente->notificaciones);
    				$this->Mensaje->errores("ERRORES:",$this->Recibo->notificaciones);
//    				$this->Mensaje->errorGuardar();
    			else:
					$this->Mensaje->okGuardar();
    				$this->redirect('editRecibo/' . $ReciboId . '/' . $this->data['Recibo']['cabecera_cliente_id']);
				endif;
    		endif;
    	endif;

	    $cliente = $this->Cliente->getCliente($id);
    	    	    	
		$this->set('clientes', $cliente);			
    	
 		$this->set('saldo', $cliente['Cliente']['saldo']);
		
		if($lFacturaPendiente):
	 		$facturaPendiente = $this->Cliente->facturasPendientes($id);
   			$this->set('facturaPendiente', $facturaPendiente);
   		endif;
		$this->set('bloquear', $bloquear);
	}
	
	function anular_recibo($id=null, $cliente_id){

		if ($this->Cliente->anularRecibo($id)):
			$this->Mensaje->okGuardar();
		else:
			$this->Mensaje->errorGuardar();
		endif;
		
   		$this->redirect('cta_cte/'.$cliente_id);
	}


	function imprimir_recibo_pdf($id){
		// traer Recibo
		$aRecibo = $this->Cliente->getRecibo($id);
		$this->set('aRecibo', $aRecibo);
		$this->render('imprimir_recibo_pdf', 'pdf');	
		
	}
	
	
	function editRecibo($nReciboId=null, $nClienteId){
		if(empty($nReciboId)) $this->redirect('cta_cte/'.$nClienteId);

		if(!empty($this->data)):
			if ($this->Cliente->anularRecibo($nReciboId)):
				$this->Mensaje->okGuardar();
			else:
				$this->Mensaje->errorGuardar();
			endif;
			
   			$this->redirect('cta_cte/'.$nClienteId);
				
		endif;

		$aRecibo = $this->Cliente->getRecibo($nReciboId);
//		$aRecibo['Recibo']['liquidacion_intercambio_id'] = $lqdIntercambioId;
//		$aRecibo['Recibo']['liquidacion_id'] = $liquidacionId;
		$aRecibo['Recibo']['action'] = "editRecibo/" . $nReciboId . '/' . $nClienteId;
		$aRecibo['Recibo']['url'] = '/Clientes/clientes/editRecibo/0/' . $nClienteId;
		
		$this->set('Recibo', $aRecibo);
//		$this->render('recibos/recibo');
		
	}
	

	function view_recibo($nReciboId=null){
		$aRecibo = $this->Cliente->getRecibo($nReciboId);
		
		$this->set('aRecibo', $aRecibo);
//		$this->render('recibos/recibo');
		
	}
	
	
	function facturas($id=0){
            $this->Session->del('grilla_factura_detalle');
            $uuid = 0;
            $cabecera = 1;
            $fecha_factura = date('Ymd');
            $oTalonario = $this->Cliente->importarModelo('TipoDocumento', 'Config');

            if(empty($id)) $this->redirect('index');

            // Busco el talonario para facturar
            $aTalonario = $oTalonario->find('all', array('conditions' => array('TipoDocumento.documento' => 'FAC')));	
            $aComboTalonario = array();
            foreach($aTalonario as $talonario):
                $aComboTalonario[$talonario['TipoDocumento']['id']] = $talonario['TipoDocumento']['descripcion'];
            endforeach;
		
	    $cliente = $this->Cliente->getCliente($id);
    	    	    	
            if(!empty($this->data)):
                if($this->data['ClienteFactura']['cabecera'] == 1):
                    $cabecera = 0;
                    # 1) ######################################################
                    $uuid = $this->Cliente->generarPIN(20);
                    //este UUID se guarda como hidden en el formulario del detalle
                    #######################################################
                    $fecha_factura = $this->Cliente->armaFecha($this->data['ClienteFactura']['fecha']);
                else:
                    $cabecera = 1;

                    # 5) ######################################################
                    # reconstruyo el campo renglonesSerialize con los datos de la sessi�n 
                    # con el uuid para que el modelo ni se entere del cambio
                    $renglones = $this->Session->read('grilla_factura_detalle_' . $this->data['ClienteFacturaDetalle']['uuid']);	
                    $this->data['ClienteFacturaDetalle']['renglonesSerialize'] = base64_encode(serialize($renglones));
                    ######################################################

                    if($this->Cliente->guardarFactura($this->data)):
                        $this->Mensaje->okGuardar();
                    else:
                        $this->Mensaje->errorGuardar();
                    endif;
                endif;
                $cliente['Cliente']['co_plan_cuenta_id'] = $this->data['ClienteFactura']['co_plan_cuenta_id'];
            endif;
		
            $this->set('cliente', $cliente);	
            $this->set('fecha_factura', $fecha_factura);
            $this->set('aComboTalonario', $aComboTalonario);		
            $this->set('cabecera', $cabecera);
            $this->set('uuid', $uuid);
            $this->set('mutual_proveedor_id', MUTUALPROVEEDORID);

            $this->set('saldo', $cliente['Cliente']['saldo']);
		
		
	}
	
	
	function cargar_factura_detalle(){
		$oPlanCuenta = $this->Cliente->importarModelo('PlanCuenta', 'contabilidad');
		
		$factura_detalle = array();
		$Ok = true;
		
		# 2) ######################################################
		# genero un id de session unico en base al uuid recibido por post
		$ID_SESSION = 'grilla_factura_detalle_' . $this->data['ClienteFacturaDetalle']['uuid'];
		#######################################################
		
		if(!$this->Session->check($ID_SESSION)) $this->Session->write($ID_SESSION,$factura_detalle);
		else $factura_detalle = $this->Session->read($ID_SESSION);	

		$var_explode = explode('|', $this->data['ClienteFacturaDetalle']['tipo_producto_mutual_producto_id']);
		$glb = $this->Cliente->getGlobalDato('concepto_1',$var_explode[1]);

		$var_plan_cuenta = $oPlanCuenta->read(null, $this->data['ClienteFacturaDetalle']['co_plan_cuenta_id']);
		
		$this->data['ClienteFacturaDetalle']['codigo_producto'] = $var_explode[1];
		$this->data['ClienteFacturaDetalle']['descripcion_producto'] = $glb['GlobalDato']['concepto_1'];
		$this->data['ClienteFacturaDetalle']['descripcion_cuenta'] = $var_plan_cuenta['PlanCuenta']['descripcion'];
		
		array_push($factura_detalle,$this->data);

		$nTotal = 0;
		foreach($factura_detalle as $key => $renglon):
			$nTotalRenglon =  $renglon['ClienteFacturaDetalle']['cantidad'] * $renglon['ClienteFacturaDetalle']['importe_unitario'];
			$nTotal += $nTotalRenglon;
		endforeach;
		

		$this->Session->write($ID_SESSION,$factura_detalle);		
		$this->set('factura_detalle',$factura_detalle);
		$this->set('total_factura', $nTotal);
		$this->set('Ok', $Ok);
		# 3) ######################################################
		# mando a la vista el uuid recibido por post para pasarlo como
		# segundo parametro al metodo cargar_renglones_remover
		$this->set('uuid', (isset($this->data['ClienteFacturaDetalle']['uuid']) ? $this->data['ClienteFacturaDetalle']['uuid'] : null));
		# en la vista cargar_renglones cambiar el hidden del serializado por un hidden con el uuid
		########################################################
		$this->render('grilla_factura_detalle','ajax');
		
	}
	

	function remover_factura_detalle($key, $uuid = null){
//		Configure::write('debug',0);
//debug($uuid);
		
		# 4) ######################################################
		$ID_SESSION = 'grilla_factura_detalle_' . $uuid;
		########################################################
		$factura_detalle = $this->Session->read($ID_SESSION);
//debug($factura_detalle);
//debug($key);
		
		if(!empty($factura_detalle)):
	    	array_splice($factura_detalle,$key,1);
	    	if(count($factura_detalle) == 0)$this->Session->del($ID_SESSION);
	    	else $this->Session->write($ID_SESSION,$factura_detalle);
    	endif;
		
    	$nTotal = 0;
		foreach($factura_detalle as $renglon):
			$nTotalRenglon =  $renglon['ClienteFacturaDetalle']['cantidad'] * $renglon['ClienteFacturaDetalle']['importe_unitario'];
			$nTotal += $nTotalRenglon;
		endforeach;
		
    	$this->set('factura_detalle',$factura_detalle);
		$this->set('Ok', true);
		$this->set('total_factura', $nTotal);
		$this->set('uuid', $uuid);
		$this->render('grilla_factura_detalle','ajax');
	}
	

	function pendientes($id=null){
    	if(empty($id)) $this->redirect('index');

	    $cliente = $this->Cliente->getCliente($id);
    	
		$facturaPendiente = $this->Cliente->facturasPendientes($id);
		
//debug($cliente);
//debug($facturaPendiente);
//exit;

		$this->set('facturaPendiente', $facturaPendiente);

   		$this->set('clientes', $cliente);			
		
	}
	
	
	function compensar_pagos($cliente_id = null){
		if(empty($cliente_id)) $this->redirect('index');
		
		if(!empty($this->data)):

    		if($this->Cliente->guardarCompensarPago($this->data)):
				$this->Mensaje->okGuardar();
			else:
				$this->Mensaje->errorGuardar();
			endif;
		endif;
		
	    $cliente = $this->Cliente->getCliente($cliente_id);
    	
		$facturaPendiente = $this->Cliente->facturasPendientes($cliente_id);
				$rFac = 0;
		$rAnt = 0;
		
		foreach($facturaPendiente as $pendiente):
			if($pendiente['tipo_cobro'] == 'FA'):
				$rFac += 1;
			else:
				$rAnt += 1;
			endif;
		endforeach;
		
		$this->set('clientes', $cliente);
		$this->set('facturaPendiente', $facturaPendiente);
		$this->set('rFac', $rFac);
		$this->set('rAnt', $rAnt);
		
		
	}
	
	
}
?>