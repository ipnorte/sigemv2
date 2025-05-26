<?php
class MovimientosController extends ProveedoresAppController{
	var $name = 'Movimientos';
	var $uses = array('Proveedores.Proveedor', 'Proveedores.Movimiento', 'Proveedores.ProveedorCtacte');
	
	var $autorizar = array('cta_cte', 'factura', 'orden_pago', 'cargar_renglones', 'cargar_renglones_remover', 'imprimir_opago', 
							'anular', 'borrar', 'imprimir_ctacte','liquidacion', 'pendientes', 'factura_detalle', 'salida',
							'cancelaciones', 'compensar_pagos', 'cargar_cheques', 'cta_cte_operativo', 'view_ctacte');
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	

	
	function index(){
		
		$search = null;
		$condiciones = null;
		
		if(!empty($this->data)){
			$this->Session->del($this->name.'.search');
			$search = $this->data;
			$condiciones = array(
								'Proveedor.cuit  LIKE ' => $search['Movimiento']['cuit'] ."%",
								'Proveedor.razon_social LIKE ' => "%" . $search['Movimiento']['razon_social']."%",
							);				
				
		}else if($this->Session->check($this->name.'.search')){
				
			$search = $this->Session->read($this->name.'.search');
			$this->data = $search;
				 
		}

//		if(!empty($this->data)):
//				$condiciones = array(
//									'Proveedor.cuit  LIKE ' => $search['Movimiento']['cuit'] ."%",
//									'Proveedor.razon_social LIKE ' => "%" . $search['Movimiento']['razon_social']."%",
//								);					
//		endif;
		
		$this->Session->write($this->name.'.search', $search);
		
		$this->paginate = array(
								'limit' => 50,
								'order' => array('Proveedor.razon_social' => 'ASC')
								);
									
		$this->set('proveedores', $this->paginate(null, $condiciones));			
		$this->render('index');		
	}

	
	function view($id=null){
    	if(empty($id)) $this->redirect('index');

	    $proveedor = $this->Movimiento->traerProveedor($id);
	    
		$this->set('proveedores', $proveedor);			
	}

	
	function edit($id=null){
    	if(empty($id)) $this->redirect('index');
		
		// traer Orden de Pago
		$aOrdenDePago = $this->Movimiento->traerOrdenDePago($id);
	    
		$proveedor = $this->Movimiento->traerProveedor($aOrdenDePago['OrdenPago']['proveedor_id']);
		
		$this->set('proveedores', $proveedor);
		$this->set('aOrdenDePago', $aOrdenDePago);
    	
	}
	
	
	function cta_cte($id=null){
            if(empty($id)) $this->redirect('index');

            $proveedor = $this->Movimiento->traerProveedor($id);

            $ctaCte = $this->Movimiento->armaCtaCte($id);

            $this->redirect('view_ctacte/' . $id);
            $this->set('ctaCte', $ctaCte);
            $this->set('proveedores', $proveedor);			

	}

        
        function view_ctacte($id){
            $proveedor = $this->Movimiento->traerProveedor($id);
            $this->paginate = array(
                        'limit' => 50,
                        'order' => array('ProveedorCtacte.fecha' => 'DESC')
                        );
            $this->set('ctaCte', $this->paginate('ProveedorCtacte', array('ProveedorCtacte.proveedor_id' => $id)));
            $this->set('proveedores', $proveedor);			

        }
        
        
	function factura($id=null){
            if (empty($id)) {
                $this->redirect('index');
            }

            $bloquearCabecera = 0;
            $existeFactura = 0;
		if(!empty($this->data)):
    		$bloquearCabecera = 1;
    		$this->data['Movimiento']['punto_venta_comprobante'] = str_pad($this->data['Movimiento']['punto_venta_comprobante'],5,'0',STR_PAD_LEFT); 
    		$this->data['Movimiento']['numero_comprobante'] = str_pad($this->data['Movimiento']['numero_comprobante'],8,'0',STR_PAD_LEFT);
    		$this->data['Movimiento']['proveedor_id'] = $id;
    		if(!$this->existeFactura($this->data['Movimiento'])):
	    		if(isset($this->data['Movimiento']['importe_gravado'])):
					for ($i = 1; $i <= 10; $i++) {
    			    	if($this->data['Movimiento']["importe_venc$i"] == 0):
    			    		$this->data['Movimiento']["vencimiento$i"] = null;
    		    		endif;				
	    			}
    				$this->data['Movimiento']['periodo_iva'] = $this->data['Movimiento']['periodo']['year'] . $this->data['Movimiento']['periodo']['month'];
			
    				$glb = $this->Movimiento->getGlobalDato('logico_1',$this->data['Movimiento']['tipo_comprobante']);
					$this->data['Movimiento']['tipo'] = $glb['GlobalDato']['logico_1'] == 0 ? 'FA' : 'NC';
			
    				App::import('Model','Proveedores.ProveedorFactura');
    				$oFactura = new ProveedorFactura();	
		    		$vGrabar = $oFactura->save($this->data['Movimiento']);
 	    			if($vGrabar):
    					$this->redirect('cta_cte/'.$id);
	    			else:
	    				$this->Mensaje->notice('Los datos no fueron grabados correctamente');
	    			endif;
	    		endif;
	    	else:
	    		$existeFactura = 1;
	    		$this->Mensaje->notice('La Factura existe para este Proveedor');
    		endif;
		endif;
		
	    $proveedor = $this->Movimiento->traerProveedor($id);
		    	    	
		$this->set('proveedores', $proveedor);			
		$this->set('bloquearCabecera', $bloquearCabecera);			
		$this->set('existeFactura', $existeFactura);			
		
	}

	function orden_pago($id=null){
		$this->Session->del('grilla_pagos');
		
		if(empty($id)) $this->redirect('index');

    	$bloquear = 0;
    	$lFacturaPendiente = false;
    	
		if(!empty($this->data)):
			if(isset($this->data['Movimiento']['Cabecera'])):
    			$bloquear = 1;
    			$lFacturaPendiente = true;
    			# 1) ######################################################
    			$this->set('uuid', $this->Movimiento->generarPIN(20));
    			//este UUID se guarda como hidden en el formulario del detalle
    			#######################################################
    		elseif(isset($this->data['Movimiento']['detalle_pago'])):
    		
    			# 5) ######################################################
    			# reconstruyo el campo renglonesSerialize con los datos de la sessi�n 
    			# con el uuid para que el modelo ni se entere del cambio
    			if(!isset($this->data['Movimiento']['renglonesSerialize'])){
    				$renglones = $this->Session->read('grilla_pagos_' . $this->data['Movimiento']['uuid']);	
    				$this->data['Movimiento']['renglonesSerialize'] = base64_encode(serialize($renglones));
    			}
    			
    			######################################################
    			if($this->Movimiento->guardarOpago($this->data)):
					$this->Mensaje->okGuardar();
				else:
					$this->Mensaje->errorGuardar();
				endif;
    		endif;
    	endif;

    	$this->CajaSaldo = $this->Movimiento->importarModelo('BancoCuentaSaldo', 'cajabanco');
    	$this->Caja = $this->Movimiento->importarModelo('BancoCuenta', 'cajabanco');
    	    	
    	$aCaja = $this->Caja->find('all', array('conditions' => array('BancoCuenta.banco_id' => '99999')));
    	$aCajaSaldo = $this->CajaSaldo->find('first', array('conditions' => array('BancoCuentaSaldo.banco_cuenta_id' => $aCaja[0]['BancoCuenta']['id']), 'order' => array('BancoCuentaSaldo.fecha_cierre' => 'DESC'))); 
    	
	    $proveedor = $this->Movimiento->traerProveedor($id, true);
    	    	    	
		$this->set('proveedores', $proveedor);			
    	
//	 	$saldo = $this->Movimiento->traerSaldo($id);
 		$this->set('saldo', $proveedor['Proveedor']['saldo']);
 		
		if($lFacturaPendiente):
			$facturaPendiente = $this->Movimiento->facturasPendientes($id);
 			$this->set('facturaPendiente', $facturaPendiente);
 		endif;
		$this->set('bloquear', $bloquear);

		$this->chqCartera = $this->Movimiento->importarModelo('BancoChequeTercero', 'cajabanco');
		$chqCarteras = $this->chqCartera->getChequeCartera();
		 
		$this->set('chqCarteras', $chqCarteras);
		$this->set('fCierreCaja', $aCajaSaldo['BancoCuentaSaldo']['fecha_cierre']);
		
	}
	

	function existeFactura($datos){
		$existe = true;
    	App::import('Model','Proveedores.ProveedorFactura');
    	$oFactura = new ProveedorFactura();	

		$factura = $oFactura->find('all',array(
							'conditions' => array('ProveedorFactura.proveedor_id' => $datos['proveedor_id'],
													'ProveedorFactura.tipo_comprobante' => $datos['tipo_comprobante'],
													'ProveedorFactura.letra_comprobante' => $datos['letra_comprobante'],
													'ProveedorFactura.punto_venta_comprobante' => $datos['punto_venta_comprobante'],
													'ProveedorFactura.numero_comprobante' => $datos['numero_comprobante'],
											)
		));
		
		if(empty($factura)) $existe = false; 

		return $existe;
	}


	function cargar_renglones(){
		Configure::write('debug',3);
		$renglones = array();
		$Ok = true;

		# 2) ######################################################
		# genero un id de session unico en base al uuid recibido por post
		if(isset($this->data['Movimiento']['uuid']))$ID_SESSION = 'grilla_pagos_' . $this->data['Movimiento']['uuid'];
		else $ID_SESSION = 'grilla_pagos';
		#######################################################
		
		if(!$this->Session->check($ID_SESSION))$this->Session->write($ID_SESSION,$renglones);
		else $renglones = $this->Session->read($ID_SESSION);	


		if(($this->params['data']['Movimiento']['tipo_pago'] == 'CH' || $this->params['data']['Movimiento']['tipo_pago'] == 'TR') && empty($this->params['data']['Movimiento']['numero_operacion'])):
				$Ok = false;
				$msgError = 'Si es un Cheque debe poner el Numero y si es una Transferencia el Numero de Operacion.';
				$this->set('msgError', $msgError);
		else:
			$this->params['data']['Movimiento']['denominacion'] = '';
		
			if(!isset($this->data['Movimiento']['fpago'])):
				if(isset($this->data['Movimiento']['fecha_operacion']) && !empty($this->data['Movimiento']['fecha_operacion'])):
					$this->data['Movimiento']['fpago'] = array('day' => substr($this->data['Movimiento']['fecha_operacion'],-2), 
																'month' => substr($this->data['Movimiento']['fecha_operacion'],5,2), 
																'year' => substr($this->data['Movimiento']['fecha_operacion'],0,4));
				else:
					if(isset($this->data['Movimiento']['fecha_pago'])):
						$this->data['Movimiento']['fpago'] = $this->data['Movimiento']['fecha_pago'];
					endif;
				endif;
				
			endif;
				
				
			if($this->params['data']['Movimiento']['tipo_pago'] == 'CH' || $this->params['data']['Movimiento']['tipo_pago'] == 'TR'):
				
				$oCuentaBanco = $this->Movimiento->importarModelo('BancoCuenta', 'cajabanco');
				$aBancoCuenta = $oCuentaBanco->getCuenta($this->params['data']['Movimiento']['banco_cuenta_id']);
				$this->params['data']['Movimiento']['denominacion'] = $aBancoCuenta['BancoCuenta']['banco'] . ' - ' . $aBancoCuenta['BancoCuenta']['numero'] . ' - ' . $aBancoCuenta['BancoCuenta']['denominacion']; 
			endif;
			$acumulado = 0;
			foreach($renglones as $renglon){
				$acumulado += $renglon['Movimiento']['importe_efectivo'];
			}
			$acumulado += $this->params['data']['Movimiento']['importe_efectivo'];
			
			if($this->params['data']['Movimiento']['importe_efectivo'] > 0):
				if(round($acumulado,2) > round($this->params['data']['Movimiento']['importe_pago'],2)):
					$Ok = false;
					$msgError = 'El importe de los Valores no puede ser mayor al Importe de la Orden de Pago';
					$this->set('msgError', $msgError);
				else:
					array_push($renglones,$this->data);
				endif;
			else: 
				$Ok = false;
				$msgError = 'El importe debe tener un valor positivo';
				$this->set('msgError', $msgError);
			endif;
		endif;
		
		$acumulado = 0;
		foreach($renglones as $renglon){
			$acumulado += $renglon['Movimiento']['importe_efectivo'];
		}
		$this->Session->write($ID_SESSION,$renglones);		
		$this->set('renglones',$renglones);
		$this->set('acumulado', round($acumulado,2));
		$this->set('Ok', $Ok);
		# 3) ######################################################
		# mando a la vista el uuid recibido por post para pasarlo como
		# segundo parametro al metodo cargar_renglones_remover
		$this->set('uuid', (isset($this->data['Movimiento']['uuid']) ? $this->data['Movimiento']['uuid'] : null));
		# en la vista cargar_renglones cambiar el hidden del serializado por un hidden con el uuid
		########################################################
		$this->render('cargar_renglones','ajax');
	}
	
	
	function cargar_renglones_remover($key, $uuid = null){
		Configure::write('debug',2);
		# 4) ######################################################
		if(!empty($uuid)) $ID_SESSION = 'grilla_pagos_' . $uuid;
		else $ID_SESSION = 'grilla_pagos';
		########################################################
		$renglones = $this->Session->read($ID_SESSION);
		
		if(!empty($renglones)):
	    	array_splice($renglones,$key,1);
	    	if(count($renglones) == 0)$this->Session->del($ID_SESSION);
	    	else $this->Session->write($ID_SESSION,$renglones);
                endif;
		$acumulado = 0;
		foreach($renglones as $renglon){
			$acumulado += $renglon['Movimiento']['importe_efectivo'];
		}
                $this->set('renglones',$renglones);
                $this->set('Ok', true);
                $this->set('acumulado', round($acumulado,2));
                $this->set('uuid', $uuid);

                $this->render('cargar_renglones','ajax');		
	}
	
	
	function imprimir_opago($id){
		// traer Orden de Pago
		$aOrdenDePago = $this->Movimiento->traerOrdenDePago($id);
		$this->set('aOrdenDePago', $aOrdenDePago);
		$this->render('imprimir_opago','pdf');	
		
	}
	
	
	function imprimir_ctacte($id){
	    $proveedor = $this->Movimiento->traerProveedor($id);
    	
		// $this->Movimiento->armaCtaCte($id);

		$ctaCte = $this->ProveedorCtacte->getCuentaCte($id);

   		$this->set('ctacte', $ctaCte);
   		$this->set('proveedores', $proveedor);			
		$this->render('imprimir_ctacte','pdf');	
   		
	}
	
	
	function anular($id=null, $proveedor_id = null){
        if(empty($id)) parent::noDisponible();
        if(empty($proveedor_id)) parent::noDisponible();
		if ($this->Movimiento->anular($id)):
			$this->Mensaje->okGuardar();
		else:
			$this->Mensaje->errorGuardar();
		endif;
		
   		$this->redirect('cta_cte/'.$proveedor_id);
	}


	function borrar($id=null, $proveedor_id = null){
        if(empty($id)) parent::noDisponible();
        if(empty($proveedor_id)) parent::noDisponible();
		if ($this->Movimiento->borrar($id)):
			$this->Mensaje->okGuardar();
		else:
			$this->Mensaje->errorGuardar();
		endif;
		
   		$this->redirect('cta_cte/'.$proveedor_id);
	}
	
	
	
	function liquidacion($proveedor_id = null){
		
		if(empty($proveedor_id)) parent::noDisponible();
		
		$this->ProveedorLiquidacion = $this->Movimiento->importarModelo('ProveedorLiquidacion', 'proveedores');
		
		$count = $this->ProveedorLiquidacion->find('count', array('conditions' => array('ProveedorLiquidacion.proveedor_id' => $proveedor_id)));
		
	    $proveedor = $this->Movimiento->traerProveedor($proveedor_id, true);
	    
   		$this->set('proveedores', $proveedor);

   		$iniLimit = 51;
   		$finLimit = 50;
		$liquidacion = $this->ProveedorLiquidacion->getLiquidaciones($proveedor_id);

		if(!empty($this->data)):
   		endif;
   		
		$this->set('liquidaciones', $liquidacion);				
	}
	

	function pendientes($id=null){
            if (empty($id)) {
                $this->redirect('index');
            }

            $proveedor = $this->Movimiento->traerProveedor($id);
    	
            $facturaPendiente = $this->Movimiento->facturasPendientes2($id);
            
            $this->set('facturaPendiente', $facturaPendiente);

            $this->set('proveedores', $proveedor);
            
            $this->render('pendientes2');
		
	}
	
	
	function factura_detalle($id, $tipo_pago){
		
		$facturaDetalle = $this->Movimiento->getCobroDetalle($id, $tipo_pago);
		
		$this->set('aPagos', $facturaDetalle);
		$this->render('factura_detalle_ajax');
	}
	

	function salida($id=null, $tipo_salida=0){
    	if(empty($id)) $this->redirect('index');

	    $proveedor = $this->Movimiento->traerProveedor($id);
    	
		$facturaPendiente = $this->Movimiento->facturasPendientes($id);
		$this->set('aPendientes', $facturaPendiente);

   		$this->set('proveedores', $proveedor);

		if($tipo_salida == 'PDF'):
			$this->render('pendiente_pdf','pdf');
		elseif($tipo_salida == 'XLS'):			
			$aPendienteXLS = array();
			foreach($facturaPendiente as $aPendiente):
				$aTmpPendiente = array(
					'numero_comprobante' => $aPendiente['tipo_comprobante_desc'],
					'comentario' => $aPendiente['comentario'],
					'fecha_comprobante' => $aPendiente['fecha_comprobante'],
					'vencimiento' => $aPendiente['vencimiento'],
					'importe_comprobante' => $aPendiente['total_comprobante'],
					'vencimiento_comprobante' => $aPendiente['importe'],
					'pago_comprobante' => $aPendiente['pago'],
					'saldo_comprobante' => $aPendiente['saldo']
				);
				array_push($aPendienteXLS, $aTmpPendiente);
			endforeach;		
			$this->set('aPendientes', $aPendienteXLS);
			$this->render('pendiente_xls','blank');
		else:
			parent::noDisponible();
		endif;
		
   		
		
	}
	
	
	function cancelaciones ($proveedor_id = null){
		if(empty($proveedor_id)) $this->redirect('index');
		$proveedor = $this->Movimiento->traerProveedor($proveedor_id);
		
		$fecha_desde = date('Y-m-d');
		$fecha_hasta = date('Y-m-d');
		$cancelaciones = null;
		
		if(!empty($this->data)){
			
			$cancelacionesRecibidas = array();
			$cancelacionesEfectuadas = array();
			$ambas = true;
			
			if($this->data['Movimientos']['imprimir'] == 0){
				$fecha_desde = $this->Movimiento->armaFecha($this->data['Movimientos']['cance_fecha_desde']);
				$fecha_hasta = $this->Movimiento->armaFecha($this->data['Movimientos']['cance_fecha_hasta']);
			}else{
				$fecha_desde = $this->data['Movimientos']['cance_fecha_desde'];
				$fecha_hasta = $this->data['Movimientos']['cance_fecha_hasta'];
				$ambas = false;
				if(!empty($this->data['CancelacionOrdenRecibidas']['id'])){
					$cancelacionesRecibidas = array_keys($this->data['CancelacionOrdenRecibidas']['id']);
				}
				if(!empty($this->data['CancelacionOrdenEfectuadas']['id'])){
					$cancelacionesEfectuadas = array_keys($this->data['CancelacionOrdenEfectuadas']['id']);
				}
				
			}

			$cancelaciones = $this->Movimiento->cargarCancelaciones($proveedor_id,$fecha_desde,$fecha_hasta,$ambas,$cancelacionesEfectuadas,$cancelacionesRecibidas);
			
			
			
		}
		
		$this->set('proveedores', $proveedor);
		$this->set('fecha_desde',$fecha_desde);	
		$this->set('fecha_hasta',$fecha_hasta);
		$this->set('cancelaciones',$cancelaciones);
		
		if($this->data['Movimientos']['imprimir'] == 1){
			$this->render('cancelaciones_pdf','pdf');
		}
		
		
	}
	
	
	function compensar_pagos($proveedor_id = null){
		if(empty($proveedor_id)) $this->redirect('index');
		
		if(!empty($this->data)):
    		if($this->Movimiento->guardarCompensarPago($this->data)):
				$this->Mensaje->okGuardar();
			else:
				$this->Mensaje->errorGuardar();
			endif;
		endif;
		
		$proveedor = $this->Movimiento->traerProveedor($proveedor_id);
		$facturaPendiente = $this->Movimiento->facturasPendientes($proveedor_id);
		$rFac = 0;
		$rAnt = 0;
		
		foreach($facturaPendiente as $pendiente):
			if($pendiente['tipo_pago'] == 'FA'):
				$rFac += 1;
			else:
				$rAnt += 1;
			endif;
		endforeach;
		
		$this->set('proveedores', $proveedor);
		$this->set('facturaPendiente', $facturaPendiente);
		$this->set('rFac', $rFac);
		$this->set('rAnt', $rAnt);
		
		
	}
	
	
	function cargar_cheques($chqId, $checked, $fecha, $importe_detalle, $importe_pago, $clave, $valor, $uuid=NULL){
		Configure::write('debug',3);
		$renglones = array();
		$Ok = true;

		# 2) ######################################################
		# genero un id de session unico en base al uuid recibido por post
		if(!empty($uuid)) $ID_SESSION = 'grilla_pagos_' . $uuid;
		else $ID_SESSION = 'grilla_pagos';
		#######################################################
		
		if(!$this->Session->check($ID_SESSION))$this->Session->write($ID_SESSION,$renglones);
		else $renglones = $this->Session->read($ID_SESSION);	

		$this->chqCartera = $this->Movimiento->importarModelo('BancoChequeTercero', 'cajabanco');
		$chqRenglon = $this->chqCartera->getChqTerceroById($chqId);
			
		$datos = array('Movimiento' => array(
				'importe_detalle' => $importe_detalle,
				'tipo_pago' => 'CT',
				'banco_cuenta_id' => 0,
				'numero_operacion' => $chqRenglon['numero_cheque'],
				'fpago' => array('day' => substr($fecha,-2), 'month' => substr($fecha,5,2), 'year' => substr($fecha,0,4)),
				'fvenc' => array('day' => substr($fecha,-2), 'month' => substr($fecha,5,2), 'year' => substr($fecha,0,4)),
				'importe_efectivo' => $chqRenglon['importe'],
// 				'proveedor_id' => $aProveedor['Proveedor']['id'],
// 				'destinatario' => $destinatario,
				'detalle_pago' => 1,
				'fecha_operacion' => $fecha,
				'tipo_pago_desc' => 'CHEQUE CARTERA',
				'acumula' => 0,
				'importe_cabecera' => 0,
				'importe_pago' => $importe_pago,
				'observacion' => '',
				'formadetalle' => 1,
				'uuid' => $uuid,
				'denominacion' => $chqRenglon['librador'] . ' -- ' . $chqRenglon['banco'],
				'banco_cheque_tercero_id' => $chqId
				
		));
		
		switch ($clave){
			case 'proveedor_id' :
				$aProveedor = $this->Movimiento->traerProveedor($valor, true);
				$datos['Movimiento']['proveedor_id'] = $valor;
				$datos['Movimiento']['destinatario'] = $aProveedor['Proveedor']['razon_social_resumida'];
				break;
				
			case 'id_persona' :
				$oPersonaV1 = $this->Movimiento->importarModelo('PersonaV1', 'v1');
				$aPersonaV1 = $oPersonaV1->getPersona($valor);
				$datos['Movimiento']['id_persona'] = $valor;
				$datos['Movimiento']['destinatario'] = $aPersonaV1['PersonaV1']['apellido'] . ', ' . $aPersonaV1['PersonaV1']['nombre'];
				break;
				
			case 'mutual_producto_solicitud_id' :
				$oMutualProductoSolicitud = $this->Movimiento->importarModelo('MutualProductoSolicitud', 'mutual');
				$solicitud = $oMutualProductoSolicitud->read(null,$valor);
				$solicitud = $oMutualProductoSolicitud->armaDatos($solicitud);
				$datos['Movimiento']['socio_id'] = $solicitud['MutualProductoSolicitud']['socio_id'];
				$datos['Movimiento']['destinatario'] = $solicitud['MutualProductoSolicitud']['beneficiario_apenom'];
				break;
				
			case 'socio_id' :
				$oSocio = $this->Movimiento->importarModelo('Socio', 'pfyj');
				$oSocio->bindModel(array('belongsTo' => array('Persona')));
				$socio = $oSocio->read(null,$valor);
				$datos['Movimiento']['socio_id'] = $valor;
				$datos['Movimiento']['destinatario'] = rtrim($socio['Persona']['apellido']) . ', ' . ltrim(rtrim($socio['Persona']['nombre']));
				break;
		}
		    	    	    	
		$acumulado = 0;
		foreach($renglones as $renglon){
			$acumulado += $renglon['Movimiento']['importe_efectivo'];
		}
		
		$datos['Movimiento']['acumula'] = $acumulado;
		$acumulado += $chqRenglon['importe'];
		if(round($acumulado,2) <= round($importe_pago,2)):
			array_push($renglones,$datos);
		else:
			$Ok = false;
			$msgError = 'El importe de los Valores no puede ser mayor al Importe de la Orden de Pago';
			$this->set('msgError', $msgError);
		endif;
		
		$acumulado = 0;
		foreach($renglones as $renglon){
			$acumulado += $renglon['Movimiento']['importe_efectivo'];
		}
		$this->Session->write($ID_SESSION,$renglones);		
		$this->set('renglones',$renglones);
		$this->set('acumulado', round($acumulado,2));
		$this->set('Ok', $Ok);
		# 3) ######################################################
		# mando a la vista el uuid recibido por post para pasarlo como
		# segundo parametro al metodo cargar_renglones_remover
		$this->set('uuid', $uuid);
		# en la vista cargar_renglones cambiar el hidden del serializado por un hidden con el uuid
		########################################################
		$this->render('cargar_renglones','ajax');
	}
        
        
        function cta_cte_operativo($id){
            if(empty($id)) $this->redirect('index');

            $proveedor = $this->Movimiento->traerProveedor($id);

            $ctaCte = $this->Movimiento->armaCtaCteOperativo($id);
            
//            debug($ctaCte);
//            exit;
            
            $this->set('ctaCte', $ctaCte);
            $this->set('proveedores', $proveedor);			
		
            
        }
	
}
?>