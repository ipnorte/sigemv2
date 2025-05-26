<?php
class OrdenPago extends ProveedoresAppModel{
	
	var $name = 'OrdenPago';
	
	
	function getOrdenDePago($id=null){
            $aOrdenDePago = array();

            if(empty($id)) return $aOrdenDePago;

            $oProvFactura = $this->importarModelo('ProveedorFactura', 'proveedores');
            $oSocioReintegro = $this->importarModelo('SocioReintegro', 'pfyj');
            $oOrdenPagoDetalle = $this->importarModelo('OrdenPagoDetalle', 'proveedores');
            $oOrdenPagoForma = $this->importarModelo('OrdenPagoForma', 'proveedores');
            $oBancoMovimientos = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');

            $aOrdenDePago = $this->read(null,$id);
            $aOrdenDePago['OrdenPago']['importe_letra'] = 'Son Pesos ' . $this->num2letras($aOrdenDePago['OrdenPago']['importe']);

            $aOrdenDePago['OrdenPago']['tipo_comprobante_desc'] = $aOrdenDePago['OrdenPago']['tipo_documento'] . " " . $aOrdenDePago['OrdenPago']['sucursal'] . "-" . $aOrdenDePago['OrdenPago']['nro_orden_pago'];


            $aOrdenDePago['OrdenPago']['importe_detalle'] = $oOrdenPagoDetalle->getImporte($id);
            $aOrdenDePago['OrdenPago']['importe_pago'] = $oOrdenPagoForma->getImporte($id);
            $aOrdenDePago['OrdenPago']['importe_cajabanco'] = $oBancoMovimientos->getImporteOrdenPago($id);
            $aOrdenDePago['OrdenPago']['error'] = 0;

            if($aOrdenDePago['OrdenPago']['importe'] != $aOrdenDePago['OrdenPago']['importe_detalle'] || 
            $aOrdenDePago['OrdenPago']['importe'] != $aOrdenDePago['OrdenPago']['importe_pago'] || 
            $aOrdenDePago['OrdenPago']['importe'] != $aOrdenDePago['OrdenPago']['importe_cajabanco'] ||
            $aOrdenDePago['OrdenPago']['importe_detalle'] != $aOrdenDePago['OrdenPago']['importe_pago'] ||
            $aOrdenDePago['OrdenPago']['importe_detalle'] != $aOrdenDePago['OrdenPago']['importe_cajabanco'] ||
            $aOrdenDePago['OrdenPago']['importe_pago'] != $aOrdenDePago['OrdenPago']['importe_cajabanco']
            ) $aOrdenDePago['OrdenPago']['error'] = 1;		



            $aOrdenPagoDetalle = $this->getDetalle($id);
            $aOrdenDePago['detalle'] = $aOrdenPagoDetalle;
            foreach($aOrdenDePago['detalle'] as $indice => $facturaDetalle){

                if($facturaDetalle['tipo_pago'] == 'FA' || $facturaDetalle['tipo_pago'] == 'NC' || $facturaDetalle['tipo_pago'] == 'ND'):
                    if($facturaDetalle['proveedor_factura_id'] != 0):
                        $aOrdenDePago['OrdenPago']['proveedor_factura_id'] = $facturaDetalle['proveedor_factura_id'];
                        $aTmp = $oProvFactura->getFactura($facturaDetalle['proveedor_factura_id']);
                        $aOrdenDePago['detalle'][$indice]['tipo_comprobante_desc'] = $aTmp['tipo_comprobante_desc'] . ' - ' . $aTmp['comentario'];
                    elseif($facturaDetalle['mutual_producto_solicitud_id'] != 0):
                        $aOrdenDePago['OrdenPago']['mutual_producto_solicitud_id'] = $facturaDetalle['mutual_producto_solicitud_id'];
                        $this->MutualProductoSolicitud = $this->importarModelo('MutualProductoSolicitud', 'mutual');
                        $aTmp = $this->MutualProductoSolicitud->read(null,$facturaDetalle['mutual_producto_solicitud_id']);
                        $aTmp = $this->MutualProductoSolicitud->armaDatos($aTmp);
                        $aOrdenDePago['detalle'][$indice]['tipo_comprobante_desc'] = 'SOLICITUD: ' . $aTmp['MutualProductoSolicitud']['id'] . ' - ' . $aTmp['MutualProductoSolicitud']['producto'];
                    elseif($facturaDetalle['socio_reintegro_id'] != 0):
                        $aOrdenDePago['OrdenPago']['socio_reintegro_id'] = $facturaDetalle['socio_reintegro_id'];
                        //					$aOrdenDePago['detalle'][$indice]['tipo_comprobante_desc'] = rtrim($aOrdenDePago['OrdenPago']['comentario']) . ' Nro.: ' . $facturaDetalle['socio_reintegro_id'];
                        $aOrdenDePago['detalle'][$indice]['tipo_comprobante_desc'] = rtrim($aOrdenDePago['OrdenPago']['comentario']) . ' Nro.: ' . $facturaDetalle['socio_reintegro_id'] . " (".$oSocioReintegro->getDescripcion($facturaDetalle['socio_reintegro_id']).")";
                    elseif($facturaDetalle['nro_solicitud'] != 0):
                        $aOrdenDePago['OrdenPago']['nro_solicitud'] = $facturaDetalle['nro_solicitud'];
                        $aOrdenDePago['detalle'][$indice]['tipo_comprobante_desc'] = 'NUMERO DE SOLICITUD: ' . $facturaDetalle['nro_solicitud'];
                    else:
                        $aOrdenDePago['detalle'][$indice]['tipo_comprobante_desc'] = 'CONCEPTO VARIOS';
                    endif;
                else:
                    $aOrdenDePago['detalle'][$indice]['tipo_comprobante_desc'] = 'PAGO ADELANTO (ANTICIPO)';
                endif;
            }

            $aOrdenPagoForma = $this->getForma($id);
            $aOrdenDePago['forma'] = $aOrdenPagoForma;

            foreach($aOrdenDePago['forma'] as $indice => $formaPago){
                if($formaPago['forma_pago'] == 'CH'):
                    $aOrdenDePago['forma'][$indice]['forma_pago_desc'] = 'CHEQUE NRO.: ' . $formaPago['numero_operacion']; //. ' CUENTA: ' . $formaPago['numero_cuenta'];
                endif;
                if($formaPago['forma_pago'] == 'TR'):
                    $aOrdenDePago['forma'][$indice]['forma_pago_desc'] = 'TRANSFERENCIA: ' . $formaPago['numero_operacion']; // . ' CUENTA: ' . $formaPago['numero_cuenta'];
                endif;
                if($formaPago['forma_pago'] == 'EF'):
                    $aOrdenDePago['forma'][$indice]['forma_pago_desc'] = 'EFECTIVO';
                endif;
                if($formaPago['forma_pago'] == 'DB'):
                    $aOrdenDePago['forma'][$indice]['forma_pago_desc'] = 'DEPOSITO BANCARIO ' . $formaPago['numero_operacion'];
                endif;
                if($formaPago['forma_pago'] == 'CT'):
                    $aOrdenDePago['forma'][$indice]['forma_pago_desc'] = 'CHEQUE CARTERA ' . $formaPago['numero_operacion'];
                endif;

            }


            if(isset($aOrdenDePago['OrdenPago']['proveedor_factura_id'])):

                $oProveedores = $this->importarModelo('Proveedor', 'proveedores');
                $aProveedor = $oProveedores->getProveedor($aOrdenDePago['OrdenPago']['proveedor_id']);
                $aOrdenDePago['OrdenPago']['Proveedor'] = $aProveedor['Proveedor']; 


            elseif(isset($aOrdenDePago['OrdenPago']['mutual_producto_solicitud_id'])):

                $this->persona = $this->importarModelo('Persona', 'pfyj');
                $this->MutualProductoSolicitud = $this->importarModelo('MutualProductoSolicitud', 'mutual');
                $orden = $this->MutualProductoSolicitud->read(null,$aOrdenDePago['OrdenPago']['mutual_producto_solicitud_id']);
                $aPersona = $this->persona->getPersona($orden['MutualProductoSolicitud']['persona_id']); 
                $aOrdenDePago['OrdenPago']['Proveedor'] = array(
                'id' => $aPersona['Persona']['id'],
                'razon_social' => $aPersona['Persona']['apenom'],
                'domicilio' => $aPersona['Persona']['domicilio'],
                'iva_concepto' => 'CONSUMIDOR FINAL',
                'formato_cuit' => $aPersona['Persona']['documento'],
                'nro_ingresos_brutos' => '',
                'destinatario' => $aPersona['Persona']['nombre'] . ' ' . $aPersona['Persona']['apellido']
                );


            elseif(isset($aOrdenDePago['OrdenPago']['socio_reintegro_id'])):

                $this->Socio = $this->importarModelo('Socio', 'pfyj');
                $this->persona = $this->importarModelo('Persona', 'pfyj');

                $aSocio = $this->Socio->getPersonaBySocioID($aOrdenDePago['OrdenPago']['socio_id']);

                $aPersona = $this->persona->getPersona($aSocio['Persona']['id']); 
                $aOrdenDePago['OrdenPago']['Proveedor'] = array(
                'id' => $aPersona['Persona']['id'],
                'razon_social' => $aPersona['Persona']['apenom'],
                'domicilio' => $aPersona['Persona']['domicilio'],
                'iva_concepto' => 'CONSUMIDOR FINAL',
                'formato_cuit' => $aPersona['Persona']['documento'],
                'nro_ingresos_brutos' => '',
                'destinatario' => $aPersona['Persona']['nombre'] . ' ' . $aPersona['Persona']['apellido']
                );


            elseif(isset($aOrdenDePago['OrdenPago']['nro_solicitud'])):
                $this->Solicitud = $this->importarModelo('Solicitud', 'v1');
                $solicitud = $this->Solicitud->getSolicitud($aOrdenDePago['OrdenPago']['nro_solicitud']);

                $aOrdenDePago['OrdenPago']['Proveedor'] = array(
                'id' => $solicitud['PersonaV1']['id_persona'],
                'razon_social' => $solicitud['PersonaV1']['apellido'] . ' ' . $solicitud['PersonaV1']['nombre'],
                'domicilio' => $solicitud['PersonaV1']['calle'] . ' Nro.:' . $solicitud['PersonaV1']['nro_calle'],
                'iva_concepto' => 'CONSUMIDOR FINAL',
                'formato_cuit' => $solicitud['PersonaV1']['documento'],
                'nro_ingresos_brutos' => '',
                'destinatario' => $solicitud['PersonaV1']['nombre'] . ' ' . $solicitud['PersonaV1']['apellido']
                );
            else:
                if($aOrdenDePago['OrdenPago']['proveedor_id'] > 0):
                    $oProveedores = $this->importarModelo('Proveedor', 'proveedores');
                    $aProveedor = $oProveedores->getProveedor($aOrdenDePago['OrdenPago']['proveedor_id']);
                    $aOrdenDePago['OrdenPago']['Proveedor'] = $aProveedor['Proveedor']; 


                elseif($aOrdenDePago['OrdenPago']['socio_id'] > 0):
                    $this->Socio = $this->importarModelo('Socio', 'pfyj');
                    $this->persona = $this->importarModelo('Persona', 'pfyj');

                    $aSocio = $this->Socio->getPersonaBySocioID($aOrdenDePago['OrdenPago']['socio_id']);

                    $aPersona = $this->persona->getPersona($aSocio['Persona']['id']); 
                    $aOrdenDePago['OrdenPago']['Proveedor'] = array(
                    'id' => $aPersona['Persona']['id'],
                    'razon_social' => $aPersona['Persona']['apenom'],
                    'domicilio' => $aPersona['Persona']['domicilio'],
                    'iva_concepto' => 'CONSUMIDOR FINAL',
                    'formato_cuit' => $aPersona['Persona']['documento'],
                    'nro_ingresos_brutos' => '',
                    'destinatario' => $aPersona['Persona']['nombre'] . ' ' . $aPersona['Persona']['apellido']
                    );

                elseif($aOrdenDePago['OrdenPago']['persona_id'] > 0):
                    $this->persona = $this->importarModelo('Persona', 'pfyj');
                    $aPersona = $this->persona->getPersona($aOrdenDePago['OrdenPago']['persona_id']); 
                    $aOrdenDePago['OrdenPago']['Proveedor'] = array(
                    'id' => $aPersona['Persona']['id'],
                    'razon_social' => $aPersona['Persona']['apenom'],
                    'domicilio' => $aPersona['Persona']['domicilio'],
                    'iva_concepto' => 'CONSUMIDOR FINAL',
                    'formato_cuit' => $aPersona['Persona']['documento'],
                    'nro_ingresos_brutos' => '',
                    'destinatario' => $aPersona['Persona']['nombre'] . ' ' . $aPersona['Persona']['apellido']
                    );

                endif;
            endif;

            return $aOrdenDePago;
	}
	
	
	function getDetalle($id=null){
		$aDetalle = array();
		
		if(empty($id)) return $aDetalle;
		
		// Orden de Pago Detalle
		App::import('Model','Proveedores.OrdenPagoDetalle');
		$oOrdenPagoDetalle = new OrdenPagoDetalle();
		
		$aDetalle = $oOrdenPagoDetalle->getOrdenPagoDetalle($id);
		
		return $aDetalle;
	}
	
	
	function getForma($id=null){
		$aForma = array();
		
		if(empty($id)) return $aForma;
		
		// Orden de Pago Valores de Cobros
		App::import('Model','Proveedores.OrdenPagoForma');
		$oOrdenPagoForma = new OrdenPagoForma();
				
		$aForma = $oOrdenPagoForma->getOrdenPagoForma($id);
		
		return $aForma;
			
	}
	
	function grabarPagoAnticipado($datos, $importe, $nroOrdenPago){
			
		// Llamo a los modelos a utilizar
		// Orden de Pago Cabecera
		$oOrdenPago = $this->importarModelo('OrdenPago', 'proveedores');
			
		// Orden de Pago Detalle
		$oOrdenPagoDetalle = $this->importarModelo('OrdenPagoDetalle', 'proveedores');
			
		// Orden de Pago Facturas
		$oOrdenPagoFactura = $this->importarModelo('OrdenPagoFactura', 'proveedores');
			
		// Orden de Pago Valores de Cobros
		$oOrdenPagoForma = $this->importarModelo('OrdenPagoForma', 'proveedores');
			
		// Tipo de Documento a utilizar ('Orden de Pago')
		$oTipoDocumento = $this->importarModelo('TipoDocumento', 'config');
			
		// Caja y Banco Movimientos. ('Banco Cuenta Movimientos').
		$oBancoMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
			
		// Caja y Banco Cuentas. ('Banco Cuentas').
		$oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$cajaId = $oBancoCuenta->getCuentaCajaId();
			
		// Caja y Banco Conceptos. ('Banco Cuentas').
		$oBancoConcepto = $this->importarModelo('BancoConcepto', 'cajabanco');
		$cncChequeId = $oBancoConcepto->getConceptoByTipoId(1);
		$cncTransferenciaId = $oBancoConcepto->getConceptoByTipoId(5);
		$cncCajaId = 0;
		
		// Busco el Numero de la Orden de Pago
//		$nroOrdenPago = $oTipoDocumento->getNumero('OPA');
//		if($nroOrdenPago == 0):		
//			return false;
//		endif;
		$nroOrdenPago = str_pad($nroOrdenPago, 8, 0, STR_PAD_LEFT);
		// Armo la Cabecera de la Orden de pago
		$ordenPagoCabecera = array(
			'id' => 0,
			'nro_orden_pago' => $nroOrdenPago,
			'fecha_pago' => $datos['fecha_operacion'],
			'proveedor_id' => $datos['proveedor_id'],
			'importe' => $importe,
			'comentario' => $datos['comentario'],
		);
		
		if(!$oOrdenPago->save($ordenPagoCabecera)):		
			return false;
		endif;

		$nOrdenPagoId = $oOrdenPago->getLastInsertID();
			
		// Armo los renglones de la Forma de pago
		$formaPago = array();
			$formaPago['id'] = 0;
			$formaPago['proveedor_id'] = $datos['proveedor_id'];
			$formaPago['banco_cuenta_id'] = $cajaId;
			$formaPago['orden_pago_id'] = $nOrdenPagoId;
			$formaPago['numero_operacion'] = '';
			$formaPago['fecha_operacion'] = $datos['fecha_operacion'];
			$formaPago['fecha_vencimiento'] = $datos['fecha_operacion'];
			$formaPago['banco_concepto_id'] = $cncCajaId;
			$formaPago['tipo'] = 7; 
			$formaPago['concepto'] = 'CAJA';
			$formaPago['destinatario'] = $datos['comentario'];
			$formaPago['descripcion'] = 'Orden Pago Nro.: ' . $nroOrdenPago;
			$formaPago['importe'] = $datos['importe'];
			$formaPago['debe_haber'] = 1;
			$formaPago['forma_pago'] = 'EF';
			$formaPago['banco_cuenta_movimiento_id'] = 0;
			$formaPago['descripcion_pago'] = 'EFECTIVO';
					
			
		$anticipoDetalle = array(
			'id' => 0,
			'proveedor_id' => $datos['proveedor_id'],
			'orden_pago_id' => $nOrdenPagoId,
			'tipo_pago' => 'AN',
			'proveedor_factura_id' => 0,
			'importe' => importe
		);
			
		if(!$oOrdenPagoDetalle->save($anticipoDetalle)):		
			return false;
		endif;


		// Grabar los movimiento de Caja y Banco
		if(!$oBancoMovimiento->save($formaPago)):
			return false;
		endif;
		$formaPago['banco_cuenta_movimiento_id'] = $oBancoMovimiento->getLastInsertID();

		
		// Grabar los valores del pago
		if(!$oOrdenPagoForma->saveAll($formaPago)):		
			return false;
		endif;

		return $nOrdenPagoId;
	
	}


	function anular($id){
        
        // adrian: 29/03/2016
        if(empty($id)) return false;
        
		$aOPago = array();

		$this->id = $id;
		$aOPago['OrdenPago']['id'] = $id;
		$aOPago['OrdenPago']['anulado'] = 1;

		
		// Llamo a los modelos a utilizar
			
		// Orden de Pago Detalle
		$oOrdenPagoDetalle = $this->importarModelo('OrdenPagoDetalle', 'proveedores');
			
		// Orden de Pago Facturas
		$oOrdenPagoFactura = $this->importarModelo('OrdenPagoFactura', 'proveedores');
			
		// Orden de Pago Valores de Cobros
		$oOrdenPagoForma = $this->importarModelo('OrdenPagoForma', 'proveedores');
			
		// Caja y Banco Movimientos. ('Banco Cuenta Movimientos').
		$oBancoMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
			
		// Cheques en cartera. ('Banco Cheque Tercero').
		$oBancoChequeTercero = $this->importarModelo('BancoChequeTercero', 'cajabanco');
			
		
		$this->begin();
		if(!$oOrdenPagoForma->deleteAll("OrdenPagoForma.orden_pago_id = " . $id)){
			$this->rollback();
			return false;
		}
		
		if(!$oOrdenPagoFactura->deleteAll("OrdenPagoFactura.orden_pago_id = " . $id)){
			$this->rollback();
			return false;
		}
		
		if(!$oOrdenPagoDetalle->deleteAll("OrdenPagoDetalle.orden_pago_id = " . $id)){
			$this->rollback();
			return false;
		}
		
		if(!$oBancoMovimiento->deleteAll("BancoCuentaMovimiento.orden_pago_id = " . $id)){
			$this->rollback();
			return false;
		}
		
		if(!$this->save($aOPago)){
			$this->rollback();
			return false;
		}

		$aChqTerceros = $oBancoChequeTercero->find('all', array('conditions' => array('BancoChequeTercero.orden_pago_id' => $id)));
		if(!empty($aChqTerceros)):
			foreach($aChqTerceros as $chqTercero):
				$chqTercero['BancoChequeTercero']['salida_banco_cuenta_movimiento_id'] = 0;
				$chqTercero['BancoChequeTercero']['orden_pago_id'] = 0;
				$chqTercero['BancoChequeTercero']['destinatario'] = '';
				$chqTercero['BancoChequeTercero']['fecha_baja'] = NULL;

				if(!$oBancoChequeTercero->save($chqTercero)):
					$this->rollback();
					return false;
				endif;
			endforeach;
		endif;
		
		$this->commit();
		return true;
	}


	function grabarOrdenPagoAnticipoCaja($datos){
		
		// Llamo a los modelos a utilizar
			
		// Orden de Pago Detalle
		$this->OrdenPagoDetalle = $this->importarModelo('OrdenPagoDetalle', 'proveedores');
			
		// Orden de Pago Facturas
		$this->OrdenPagoFactura = $this->importarModelo('OrdenPagoFactura', 'proveedores');
			
		// Orden de Pago Valores de Cobros
		$this->OrdenPagoForma = $this->importarModelo('OrdenPagoForma', 'proveedores');
			
		// Caja y Banco Movimientos. ('Banco Cuenta Movimientos').
		$oBancoMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
			
		// Caja y Banco Cuentas. ('Banco Cuentas').
		$oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$cajaId = $oBancoCuenta->getCuentaCajaId(); 
		
		// Caja y Banco Conceptos. ('Banco Cuentas').
		$oBancoConcepto = $this->importarModelo('BancoConcepto', 'cajabanco');
		$cncBancoId = $oBancoConcepto->getConceptoByTipoId(2);
		$cncCajaId = 0;
		
		$fechaCobro = parent::armaFecha($datos['OrdenDescuentoCobro']['fecha_comprobante']);
		
		$aAnticipo = array(
			'proveedor_id' => $datos['OrdenDescuentoCobro']['proveedor_origen_fondo_id'],
			'nro_orden_pago' => str_pad($datos['OrdenDescuentoCobro']['numero_orden_pago'], 8, 0, STR_PAD_LEFT),
			'fecha_comprobante' => $fechaCobro,
			'importe' => $datos['OrdenDescuentoCobro']['importe_cobro'],
			'caja_id' => $cajaId,
			'cnc_caja_id' => 0,
			'orden_caja_cobro_id' => $datos['OrdenDescuentoCobro']['orden_caja_cobro_id'],
			'orden_descuento_cobro_id' => $datos['OrdenDescuentoCobro']['orden_descuento_cobro_id'],
			'comentario' => ''
		);
		
		
		$aOrdenPagoAnticipo = $this->prepararPagoAnticipadoCaja($aAnticipo);
		if(!$this->save($aOrdenPagoAnticipo)):
			return false;
		endif;
		$nOPagoId = $this->getLastInsertID();
		$aOrdenPagoAnticipo['detalle']['orden_pago_id'] = $this->getLastInsertID();
		$aOrdenPagoAnticipo['forma_pago']['orden_pago_id'] = $this->getLastInsertID();
		if(!$this->OrdenPagoDetalle->save($aOrdenPagoAnticipo['detalle'])):
			return false;
		endif;
		if(!$oBancoMovimiento->save($aOrdenPagoAnticipo['forma_pago'])):
			return false;
		endif;
		$aOrdenPagoAnticipo['forma_pago']['banco_cuenta_movimiento_id'] = $oBancoMovimiento->getLastInsertID();
		if(!$this->OrdenPagoForma->save($aOrdenPagoAnticipo['forma_pago'])):
			return false;
		endif;
		
		
		return $nOPagoId;
		
	}


	function prepararPagoAnticipadoCaja($OPago){
			
		$this->Proveedor = $this->importarModelo('Proveedor', 'proveedores');
		$razon_social = $this->Proveedor->getRazonSocial($OPago['proveedor_id']);
		 
		// Armo la Cabecera de la Orden de pago
		$ordenPago = array(
			'id' => 0,
			'nro_orden_pago' => $OPago['nro_orden_pago'],
			'fecha_pago' => $OPago['fecha_comprobante'],
			'proveedor_id' => $OPago['proveedor_id'],
			'importe' => $OPago['importe'],
			'comentario' => 'COBRADO EN COMERCIO',
		
			'detalle' => array(
				'id' => 0,
				'proveedor_id' => $OPago['proveedor_id'],
				'orden_pago_id' => 0,
				'tipo_pago' => 'AN',
				'proveedor_factura_id' => 0,
				'importe' => $OPago['importe']
			),

			'forma_pago' => array(
				'id' => 0,
				'proveedor_id' => $OPago['proveedor_id'],
				'banco_cuenta_id' => $OPago['caja_id'],
				'orden_pago_id' => 0,
				'numero_operacion' => '',
				'fecha_operacion' => $OPago['fecha_comprobante'],
				'fecha_vencimiento' => $OPago['fecha_comprobante'],
				'banco_concepto_id' => $OPago['cnc_caja_id'],
				'tipo' => 7,
				'concepto' => 'CAJA',
				'destinatario' => $razon_social . ' (ANTICIPO)',
				'descripcion' => 'Orden Pago Nro.: ' . str_pad($OPago['nro_orden_pago'], 8, 0, STR_PAD_LEFT),
				'importe' => $OPago['importe'],
				'debe_haber' => 1,
				'forma_pago' => 'EF',
				'banco_cuenta_movimiento_id' => 0,
				'descripcion_pago' => 'EFECTIVO'
			),
		);
		
		return $ordenPago;
	}


	function getOrdenPagoAmpliado($id){
		$aOrdenDePago = $this->getOrdenDePago($id);
		
		if(isset($aOrdenDePago['OrdenPago']['proveedor_factura_id'])):

			$oProveedores = $this->importarModelo('Proveedor', 'proveedores');
			$aProveedor = $oProveedores->getProveedor($aOrdenDePago['OrdenPago']['proveedor_id']);
			$aOrdenDePago['Proveedor'] = $aProveedor['Proveedor']; 

			
		elseif(isset($aOrdenDePago['OrdenPago']['mutual_producto_solicitud_id'])):

			$this->persona = $this->importarModelo('Persona', 'pfyj');
			$this->MutualProductoSolicitud = $this->importarModelo('MutualProductoSolicitud', 'mutual');
			$orden = $this->MutualProductoSolicitud->read(null,$aOrdenDePago['OrdenPago']['mutual_producto_solicitud_id']);
			$aPersona = $this->persona->getPersona($orden['MutualProductoSolicitud']['persona_id']); 
			$aOrdenDePago['Proveedor'] = array(
				'id' => $aPersona['Persona']['id'],
				'razon_social' => $aPersona['Persona']['apenom'],
				'domicilio' => $aPersona['Persona']['domicilio'],
				'iva_concepto' => 'CONSUMIDOR FINAL',
				'formato_cuit' => $aPersona['Persona']['documento'],
				'nro_ingresos_brutos' => ''
			);
			
			
		elseif(isset($aOrdenDePago['OrdenPago']['socio_reintegro_id'])):
		
			$this->Socio = $this->importarModelo('Socio', 'pfyj');
			$this->persona = $this->importarModelo('Persona', 'pfyj');
	
			$aSocio = $this->Socio->getPersonaBySocioID($aOrdenDePago['OrdenPago']['socio_id']);
	
			$aPersona = $this->persona->getPersona($aSocio['Persona']['id']); 
			$aOrdenDePago['Proveedor'] = array(
				'id' => $aPersona['Persona']['id'],
				'razon_social' => $aPersona['Persona']['apenom'],
				'domicilio' => $aPersona['Persona']['domicilio'],
				'iva_concepto' => 'CONSUMIDOR FINAL',
				'formato_cuit' => $aPersona['Persona']['documento'],
				'nro_ingresos_brutos' => ''
			);
		
		
		elseif(isset($aOrdenDePago['OrdenPago']['nro_solicitud'])):
			$this->Solicitud = $this->importarModelo('Solicitud', 'v1');
			$solicitud = $this->Solicitud->getSolicitud($aOrdenDePago['OrdenPago']['nro_solicitud']);
		
			$aOrdenDePago['Proveedor'] = array(
				'id' => $solicitud['PersonaV1']['id_persona'],
				'razon_social' => $solicitud['PersonaV1']['apellido'] . ' ' . $solicitud['PersonaV1']['nombre'],
				'domicilio' => $solicitud['PersonaV1']['calle'] . ' Nro.:' . $solicitud['PersonaV1']['nro_calle'],
				'iva_concepto' => 'CONSUMIDOR FINAL',
				'formato_cuit' => $solicitud['PersonaV1']['documento'],
				'nro_ingresos_brutos' => ''
			);
		else:
			if($aOrdenDePago['OrdenPago']['proveedor_id'] > 0):
				$oProveedores = $this->importarModelo('Proveedor', 'proveedores');
				$aProveedor = $oProveedores->getProveedor($aOrdenDePago['OrdenPago']['proveedor_id']);
				$aOrdenDePago['Proveedor'] = $aProveedor['Proveedor']; 

			
			elseif($aOrdenDePago['OrdenPago']['socio_id'] > 0):
				$this->Socio = $this->importarModelo('Socio', 'pfyj');
				$this->persona = $this->importarModelo('Persona', 'pfyj');
		
				$aSocio = $this->Socio->getPersonaBySocioID($aOrdenDePago['OrdenPago']['socio_id']);
		
				$aPersona = $this->persona->getPersona($aSocio['Persona']['id']); 
				$aOrdenDePago['Proveedor'] = array(
					'id' => $aPersona['Persona']['id'],
					'razon_social' => $aPersona['Persona']['apenom'],
					'domicilio' => $aPersona['Persona']['domicilio'],
					'iva_concepto' => 'CONSUMIDOR FINAL',
					'formato_cuit' => $aPersona['Persona']['documento'],
					'nro_ingresos_brutos' => ''
				);
		
			elseif($aOrdenDePago['OrdenPago']['persona_id'] > 0):
				$this->persona = $this->importarModelo('Persona', 'pfyj');
				$aPersona = $this->persona->getPersona($aOrdenDePago['OrdenPago']['persona_id']); 
				$aOrdenDePago['Proveedor'] = array(
					'id' => $aPersona['Persona']['id'],
					'razon_social' => $aPersona['Persona']['apenom'],
					'domicilio' => $aPersona['Persona']['domicilio'],
					'iva_concepto' => 'CONSUMIDOR FINAL',
					'formato_cuit' => $aPersona['Persona']['documento'],
					'nro_ingresos_brutos' => ''
				);
			
			endif;
		endif;
		
		return $aOrdenDePago;
	}
	
	
	function getOrdenPagoFecha($BancoCuentaId){
		$this->BancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$cuenta = $this->BancoCuenta->getCuenta($BancoCuentaId);
		
		$mkDesde = mktime(0,0,0,date('m',strtotime($cuenta['BancoCuenta']['fecha_conciliacion'])),date('d',strtotime($cuenta['BancoCuenta']['fecha_conciliacion'])),date('Y',strtotime($cuenta['BancoCuenta']['fecha_conciliacion'])));
		$fecha_desde = date('Y-m-d',$this->addDayToDate($mkDesde));
		
		$orden_pagos = $this->getOrdenPagoEntreFecha($fecha_desde, $cuenta['BancoCuenta']['fecha_extracto']);

		return $orden_pagos;
		
	}
	
	
	function getOrdenPagoEntreFecha($fecha_desde, $fecha_hasta){
		$orden_pagos = $this->find('all', array('conditions' => array('OrdenPago.fecha_pago >=' => $fecha_desde, 'OrdenPago.fecha_pago <=' => $fecha_hasta, 'OrdenPago.anulado' => 0), 'order' => array('OrdenPago.fecha_pago', 'OrdenPago.id')));
		
		$tmpOPago = array();
		$aOPago = array();
		foreach ($orden_pagos as $orden_pago):
                
			$tmpOPago = $this->getOrdenDePago($orden_pago['OrdenPago']['id']);
			array_push($aOPago, $tmpOPago);
		endforeach;
		
		
		return $aOPago;
	}
}
?>