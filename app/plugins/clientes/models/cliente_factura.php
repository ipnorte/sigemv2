<?php
class ClienteFactura extends ClientesAppModel{
	
	var $name = 'ClienteFactura';
	
	var $hasMany = array("ClienteFacturaDetalle");
	
	function getFactura($id,$bindDetalle = false){
		
            $aFactura = $this->read(null,$id);

            if($aFactura['ClienteFactura']['tipo_comprobante'] == 'FAC'):
                    if($aFactura['ClienteFactura']['tipo'] == 'FA') $aFactura['ClienteFactura']['tipo_comprobante_desc'] = 'FACTURA';
                    if($aFactura['ClienteFactura']['tipo'] == 'NC') $aFactura['ClienteFactura']['tipo_comprobante_desc'] = 'NOTA CREDITO';
                    if($aFactura['ClienteFactura']['tipo'] == 'ND') $aFactura['ClienteFactura']['tipo_comprobante_desc'] = 'NOTA DEBITO';
            endif;

            if($aFactura['ClienteFactura']['tipo_comprobante'] == 'SALDOCLIENTE') $aFactura['ClienteFactura']['tipo_comprobante_desc'] = 'SALDO ANTERIOR';
            else $aFactura['ClienteFactura']['tipo_comprobante_desc'] .= ' ' . $aFactura['ClienteFactura']['letra_comprobante'] . ' ' . $aFactura['ClienteFactura']['punto_venta_comprobante'] . '-' . $aFactura['ClienteFactura']['numero_comprobante'];

            #traigo los pagos
            $oFactura = $this->importarModelo('ReciboFactura', 'clientes');
            $aFactura['ClienteFactura']['pagos'] = $oFactura->getCobroFactura($id);		
            $aFactura['ClienteFactura']['saldo'] = round($aFactura['ClienteFactura']['total_comprobante'] - $aFactura['ClienteFactura']['pagos'],2);

            if(!$bindDetalle)return $aFactura['ClienteFactura'];
            else return $aFactura;
		
	}
	
	function tieneCobro($id){
            $oFactura = $this->importarModelo('ReciboFactura', 'clientes');
            $cobro = $oFactura->getCobroFactura($id);
            if($cobro > 0) return true;
            return false; 
		
	}


	function grabarFacturaLiquidacion($factura, $liquidacion){
		// Proveedores
            $oProveedor = $this->importarModelo('Proveedor', 'proveedores');
            $aProveedor = $oProveedor->getProveedor($factura['proveedor_id']);

            // Clientes
            $oCliente = $this->importarModelo('Cliente', 'clientes');
            $aCliente = $oCliente->getCliente($aProveedor['Proveedor']['cliente_id']);

            // Factura Detalle
            $oFacturaDetalle = $this->importarModelo('ClienteFacturaDetalle', 'clientes');

            // Tipo de Comprobante o Documento
            $oTipoDocumento = $this->importarModelo('TipoDocumento', 'config');
            $aComprobante = $oTipoDocumento->getComprobante('FAC');
    	
            if($factura['tipo_documento_cliente'] == 'NC') $glb = $this->getGlobalDato('entero_1','CONTCLIENCRE');
            else $glb = $this->getGlobalDato('entero_1','CONTCLIEFACT');
		
            $facturaLiquidacion = array(
                'id' => 0,
                'tipo' => $factura['tipo_documento_cliente'],
                'cliente_id' => $aCliente['Cliente']['id'],
                'fecha_comprobante' => $liquidacion['Liquidacion']['fecha_imputacion'],
                'tipo_comprobante' => 'FAC',
                'letra_comprobante' => $aComprobante['letra'],
                'punto_venta_comprobante' => str_pad($aComprobante['sucursal'], 4, 0, STR_PAD_LEFT),
                'numero_comprobante' => '0',
                'importe_no_gravado' => $factura['importe_cliente'],
                'total_comprobante' => $factura['importe_cliente'],
//			'co_plan_cuenta_id' => $aCliente['Cliente']['co_plan_cuenta_id'],
                'vencimiento1' => $liquidacion['Liquidacion']['fecha_imputacion'],
                'importe_venc1' => $factura['importe_cliente'],
                'estado' => 'A',
//			'concepto_gasto' => $aCliente['Cliente']['concepto_gasto'],
                'cliente_tipo_asiento_id' => $glb['GlobalDato']['entero_1'],
                'liquidacion_id' => $liquidacion['Liquidacion']['id'],
                'comentario' => trim($factura['descripcion_cliente']) . ' - ' . $liquidacion['Liquidacion']['organismo']
            );
		
            $facturaDetalle = array(
                'id' => 0,
                'cliente_factura_id' => 0,
                'producto' => trim($factura['descripcion_cliente']) . ' ' . $liquidacion['Liquidacion']['organismo'],
                'cantidad' => $factura['cantidad'],
                'precio_unitario' => $factura['importe_cliente'],
                'precio_total' => $factura['importe_cliente']
            );
		
            // Busco el Numero de la Factura
            $nroFactura = $oTipoDocumento->getNumero('FAC');
            if($nroFactura == 0):
                return false;
            endif;
            $facturaLiquidacion['numero_comprobante'] = str_pad($nroFactura, 8, 0, STR_PAD_LEFT);
		
            // Grabo la cabecera de la Factura
            $this->begin();
            if(!$this->save($facturaLiquidacion)):		
                    $this->rollback();
                    $oTipoDocumento->unLookRegistro('FAC');
                    return false;
            endif;

            // Busco el id de la Factura
            $nFacturaId = $this->getLastInsertID();
            $facturaDetalle['cliente_factura_id'] = $nFacturaId;

            // Grabo el Detalle de la Factura
            if(!$oFacturaDetalle->save($facturaDetalle)):
                    $this->rollback();
                    $oTipoDocumento->unLookRegistro('FAC');
                    return false;
            endif;

            $this->commit();
            $oTipoDocumento->putNumero('FAC');
            return $nFacturaId;
	}
	
	
	function anularByOrdenCaja($nOrdenCajaId){
		$this->FacturaDetalle = $this->importarModelo('ClienteFacturaDetalle', 'clientes');
		
		$aFacturas = $this->find('all', array('conditions' => array('ClienteFactura.orden_caja_cobro_id' => $nOrdenCajaId)));
		
		foreach($aFacturas as $factura):

			$nFacturaId = $factura['ClienteFactura']['id'];
			if(!$this->FacturaDetalle->deleteAll("ClienteFacturaDetalle.cliente_factura_id = " . $nFacturaId)) return false;
			
			$clienteFactura = array('ClienteFactura' => array('id' => $nFacturaId, 'orden_caja_cobro_id' => 0, 'orden_descuento_cobro_id' => 0, 'anulado' => 1));
			$this->id = $nFacturaId;
			if(!$this->save($clienteFactura)) return false;
		endforeach;

		return true;
	}
	
	
	function __getFacturaCliente($factura){
		// Proveedores
		$oProveedor = $this->importarModelo('Proveedor', 'proveedores');
    	$aProveedor = $oProveedor->getProveedor($factura['orden_descuentos']['proveedor_id']);
		    	
    	// Clientes
    	$oCliente = $this->importarModelo('Cliente', 'clientes');
    	$aCliente = $oCliente->getCliente($aProveedor['Proveedor']['cliente_id']);
    	
    	// Tipo de Comprobante o Documento
    	$oTipoDocumento = $this->importarModelo('TipoDocumento', 'config');
    	$aComprobante = $oTipoDocumento->getComprobante('FAC');
    	
		return $aFactura = array(
			'id' => 0,
			'tipo' => 'FA',
			'cliente_id' => $aCliente['Cliente']['id'],
			'fecha_comprobante' => $factura['orden_descuento_cobro_cuotas']['fecha_comprobante'],
			'tipo_comprobante' => 'FAC',
			'letra_comprobante' => $aComprobante['letra'],
			'punto_venta_comprobante' => str_pad($aComprobante['sucursal'], 4, 0, STR_PAD_LEFT),
			'numero_comprobante' => '0',
			'importe_no_gravado' => $factura[0]['comision_cobranza'],
			'total_comprobante' => $factura[0]['comision_cobranza'],
//			'co_plan_cuenta_id' => $aCliente['Cliente']['co_plan_cuenta_id'],
			'vencimiento1' => $factura['orden_descuento_cobro_cuotas']['fecha_comprobante'],
			'importe_venc1' => $factura[0]['comision_cobranza'],
			'estado' => 'A',
//			'concepto_gasto' => $aCliente['Cliente']['concepto_gasto'],
//			'cliente_tipo_asiento_id' => $aCliente['Cliente']['proveedor_tipo_asiento_id'],
			'orden_caja_cobro_id' => $factura['orden_descuento_cobro_cuotas']['orden_caja_cobro_id'],
			'orden_descuento_cobro_id' => $factura['orden_descuento_cobro_cuotas']['orden_descuento_cobro_id'],
			'comentario' => $factura['concepto'],

			'factura_detalle' => array(
				'id' => 0,
				'cliente_factura_id' => 0,
				'producto' => 'COMISION ' . $factura['concepto'],
				'cantidad' => 1,
				'precio_unitario' => $factura[0]['comision_cobranza'],
				'precio_total' => $factura[0]['comision_cobranza']
			)
		);
		
	}

	
	function grabarFacturaByCancelacion($id, $aComprobante){
		
            $this->OrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');
            $aOrdenDescuentoCobro = $this->OrdenDescuentoCobro->getCobro($id, true);

            if(empty($aOrdenDescuentoCobro['ProveedorLiquidacion'])) return 'A';
    	
            if($aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['proveedor_origen_fondo_id'] == 
                $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['proveedor_id'] || 
    		$aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['proveedor_id'] == MUTUALPROVEEDORID) return 'A';
    	
            if($aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['comision_cobranza'] == 0 ||
    		$aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['facturar'] == 0) return 'A';
    	
            if($aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['cliente_id'] == 0) return 'A';
            
            // Clientes
            $oCliente = $this->importarModelo('Cliente', 'clientes');

    	
            $aCliente = $oCliente->getCliente($aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['cliente_id']);
    	
            // Factura Detalle
            $oFacturaDetalle = $this->importarModelo('ClienteFacturaDetalle', 'clientes');
    	
            $glb = $this->getGlobalDato('entero_1','CONTCLIEFACT');
    	
            $facturaLiquidacion = array('ClienteFactura' => array(
                'id' => 0,
                'tipo' => 'FA',
                'cliente_id' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['cliente_id'],
                'fecha_comprobante' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['fecha'],
                'tipo_comprobante' => $aComprobante['documento'],
                'letra_comprobante' => $aComprobante['letra'],
                'punto_venta_comprobante' => str_pad($aComprobante['sucursal'], 4, 0, STR_PAD_LEFT),
                'numero_comprobante' => str_pad($aComprobante['numero'], 8, 0, STR_PAD_LEFT),
                'importe_no_gravado' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['comision_cobranza'],
                'total_comprobante' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['comision_cobranza'],
                'co_plan_cuenta_id' => $aCliente['Cliente']['co_plan_cuenta_id'],
                'vencimiento1' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['fecha'],
                'importe_venc1' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['comision_cobranza'],
                'estado' => 'A',
                'cliente_tipo_asiento_id' => $glb['GlobalDato']['entero_1'],
                'liquidacion_id' => 0,
                'socio_id' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['socio_id'],
                'orden_descuento_id' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['orden_descuento_id'],
                'cancelacion_orden_id' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['cancelacion_orden_id'],
                'orden_descuento_cobro_id' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['id'],
    		'orden_caja_cobro_id' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['orden_caja_cobro_id'],
                'comentario' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['concepto']
            ));
		
            $facturaDetalle = array('ClienteFacturaDetalle' => array(
                'id' => 0,
                'cliente_factura_id' => 0,
                'producto' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['concepto'],
                'cantidad' => 1,
                'precio_unitario' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['comision_cobranza'],
                'precio_total' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['comision_cobranza']
            ));
		
//		// Busco el Numero de la Factura
//		$nroFactura = $oTipoDocumento->getNumero('FAC');
//		if($nroFactura == 0):
//			return false;
//		endif;
//		$facturaLiquidacion['ClienteFactura']['numero_comprobante'] = str_pad($nroFactura, 8, 0, STR_PAD_LEFT);
//		
		// Grabo la cabecera de la Factura
//		$this->begin();
            if(!$this->save($facturaLiquidacion)):		
//			$this->rollback();
//			$oTipoDocumento->unLookRegistro('FAC');
                return false;
            endif;

            // Busco el id de la Factura
            $nFacturaId = $this->getLastInsertID();
            $facturaDetalle['ClienteFacturaDetalle']['cliente_factura_id'] = $this->getLastInsertID();

            // Grabo el Detalle de la Factura
            if(!$oFacturaDetalle->save($facturaDetalle)):
//			$this->rollback();
//			$oTipoDocumento->unLookRegistro('FAC');
                return false;
            endif;
		
//		$this->commit();
//		$oTipoDocumento->putNumero('FAC');
            return $nFacturaId;
	}
	
	
	function grabarFacturaByCaja($id){
		
		$this->OrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');
    	$aOrdenDescuentoCobro = $this->OrdenDescuentoCobro->getCobro($id, true);
    	
    	// Clientes
    	$oCliente = $this->importarModelo('Cliente', 'clientes');

    	// Factura Detalle
    	$oFacturaDetalle = $this->importarModelo('ClienteFacturaDetalle', 'clientes');
    	
    	foreach($aOrdenDescuentoCobro['ProveedorLiquidacion'] as $aCobro):
    		if($aCobro['comision_cobranza'] > 0):
    	
				$glb = $this->getGlobalDato('entero_1','CONTCLIEFACT');
    		
		    	$aCliente = $oCliente->getCliente($aCobro['cliente_id']);
    	
		    	// Tipo de Comprobante o Documento
		    	$oTipoDocumento = $this->importarModelo('TipoDocumento', 'config');
		    	$aComprobante = $oTipoDocumento->getComprobante('FAC');
		    	
		    	$facturaLiquidacion = array('ClienteFactura' => array(
					'id' => 0,
					'tipo' => 'FA',
					'cliente_id' => $aCobro['cliente_id'],
					'fecha_comprobante' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['fecha'],
					'tipo_comprobante' => $aComprobante['TipoDocumento']['documento'],
					'letra_comprobante' => $aComprobante['TipoDocumento']['letra'],
					'punto_venta_comprobante' => str_pad($aComprobante['TipoDocumento']['sucursal'], 4, 0, STR_PAD_LEFT),
					'numero_comprobante' => '0',
					'importe_no_gravado' => $aCobro['comision_cobranza'],
					'total_comprobante' => $aCobro['comision_cobranza'],
					'co_plan_cuenta_id' => $aCliente['Cliente']['co_plan_cuenta_id'],
					'vencimiento1' => $aCobro['OrdenDescuentoCobro']['fecha'],
					'importe_venc1' => $aCobro['comision_cobranza'],
					'estado' => 'A',
					'cliente_tipo_asiento_id' => $glb['GlobalDato']['entero_1'],
					'liquidacion_id' => 0,
					'socio_id' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['socio_id'],
					'orden_descuento_id' => $aCobro['orden_descuento_id'],
					'cancelacion_orden_id' => $aCobro['cancelacion_orden_id'],
					'orden_descuento_cobro_id' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['id'],
		    		'orden_caja_cobro_id' => $aCobro['orden_caja_cobro_id'],
					'comentario' => 'COMISION - ' . $aCobro['concepto']
		    	));
				
				$facturaDetalle = array('ClienteFacturaDetalle' => array(
					'id' => 0,
					'cliente_factura_id' => 0,
					'producto' => 'COMISION - ' . $aCobro['concepto'],
					'cantidad' => 1,
					'precio_unitario' => $aCobro['comision_cobranza'],
					'precio_total' => $aCobro['comision_cobranza']
				));
				
				// Busco el Numero de la Factura
				$nroFactura = $oTipoDocumento->getNumero('FAC');
				if($nroFactura == 0):
					return false;
				endif;
				$facturaLiquidacion['ClienteFactura']['numero_comprobante'] = str_pad($nroFactura, 8, 0, STR_PAD_LEFT);
				
				// Grabo la cabecera de la Factura
				$this->begin();
				if(!$this->save($facturaLiquidacion)):		
					$this->rollback();
					$oTipoDocumento->unLookRegistro('FAC');
					return false;
				endif;
		
				// Busco el id de la Factura
				$facturaDetalle['ClienteFacturaDetalle']['cliente_factura_id'] = $this->getLastInsertID();
		
				// Grabo el Detalle de la Factura
				if(!$oFacturaDetalle->save($facturaDetalle)):
					$this->rollback();
					$oTipoDocumento->unLookRegistro('FAC');
					return false;
				endif;
				
				$this->commit();
				$oTipoDocumento->putNumero('FAC');
			endif;
		endforeach;
		return true;
	}
	

	function prepararFacturaCliente($factura){
		// Proveedores
		$this->Proveedor = $this->importarModelo('Proveedor', 'proveedores');
    	$aProveedor = $this->Proveedor->getProveedor($factura['orden_descuentos']['proveedor_id']);
		    	
    	// Clientes
    	$this->Cliente = $this->importarModelo('Cliente', 'clientes');
    	$aCliente = $this->Cliente->getCliente($aProveedor['Proveedor']['cliente_id']);
    	
    	// Tipo de Comprobante o Documento
    	$this->TipoDocumento = $this->importarModelo('TipoDocumento', 'config');
    	$aComprobante = $this->TipoDocumento->getComprobante('FAC');
    	
		$glb = $this->getGlobalDato('entero_1','CONTCLIEFACT');
		
    	return $aFactura = array(
			'id' => 0,
			'tipo' => 'FA',
			'cliente_id' => $aCliente['Cliente']['id'],
			'fecha_comprobante' => $factura['orden_descuento_cobro_cuotas']['fecha_comprobante'],
			'tipo_comprobante' => 'FAC',
			'letra_comprobante' => $aComprobante['letra'],
			'punto_venta_comprobante' => str_pad($aComprobante['sucursal'], 4, 0, STR_PAD_LEFT),
			'numero_comprobante' => '0',
			'importe_no_gravado' => $factura[0]['comision_cobranza'],
			'total_comprobante' => $factura[0]['comision_cobranza'],
			'vencimiento1' => $factura['orden_descuento_cobro_cuotas']['fecha_comprobante'],
			'importe_venc1' => $factura[0]['comision_cobranza'],
			'estado' => 'A',
    		'cliente_tipo_asiento_id' => $glb['GlobalDato']['entero_1'],
    		'orden_descuento_id' => $factura['orden_descuentos']['id'],
			'orden_caja_cobro_id' => $factura['orden_descuento_cobro_cuotas']['orden_caja_cobro_id'],
			'orden_descuento_cobro_id' => $factura['orden_descuento_cobro_cuotas']['orden_descuento_cobro_id'],
			'comentario' => $factura['concepto'],

			'factura_detalle' => array(
				'id' => 0,
				'cliente_factura_id' => 0,
				'producto' => 'COMISION ' . $factura['concepto'],
				'cantidad' => 1,
				'precio_unitario' => $factura[0]['comision_cobranza'],
				'precio_total' => $factura[0]['comision_cobranza']
			)
		);
		
	}
	

	function getCodigoPlanCuenta($id){
		$this->ClienteTipoAsientoRenglon = $this->importarModelo('ClienteTipoAsientoRenglon', 'clientes');
		
		$factura = $this->read(null, $id);
		$retorno = array('co_plan_cuenta_id' => 0, 'error' => 'OK', 'tipo_asiento' => 0);
		
		if(empty($factura['ClienteFactura']['co_plan_cuenta_id'])):
			if(empty($factura['ClienteFactura']['cliente_tipo_asiento_id'])):
				$retorno['error'] = 'FALTA DEFINIR ASIENTO EN FACTURAS';
			else:
				$tipoAsiento = $this->ClienteTipoAsientoRenglon->find('all', array('conditions' => array('ClienteTipoAsientoRenglon.cliente_tipo_asiento_id' => $factura['ClienteFactura']['cliente_tipo_asiento_id'], 'ClienteTipoAsientoRenglon.variable' => 'TOTAL')));

				if(empty($tipoAsiento)): 
					$retorno['error'] = 'ASIENTO NO DEFINIDO';
				else: 
					$retorno['co_plan_cuenta_id'] = $tipoAsiento[0]['ClienteTipoAsientoRenglon']['co_plan_cuenta_id'];
					$retorno['tipo_asiento'] = $tipoAsiento[0]['ClienteTipoAsientoRenglon']['cliente_tipo_asiento_id'];
				endif;
			endif;
		else:
			$retorno['co_plan_cuenta_id'] = $factura['ClienteFactura']['co_plan_cuenta_id'];
		endif;
		
		return $retorno;
	}
	
	
	function getAsientoFactura($factura){
		$this->ClienteTipoAsientoRenglon = $this->importarModelo('ClienteTipoAsientoRenglon', 'clientes');
		$this->ClienteFacturaDetalle = $this->importarModelo('ClienteFacturaDetalle', 'clientes');
		
		if(empty($factura['ClienteFactura']['cliente_tipo_asiento_id'])):
                    if(empty($factura['ClienteFactura']['co_plan_cuenta_id'])):
			$totalComprobante = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ClienteFactura']['total_comprobante'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
			$noGravado = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ClienteFactura']['importe_no_gravado'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
                    else:
                        $detalleFactura = $this->ClienteFacturaDetalle->find('all', array('conditions' => array('ClienteFacturaDetalle.cliente_factura_id' => $factura['ClienteFactura']['id'])));
			$totalComprobante = array('co_plan_cuenta_id' => $factura['ClienteFactura']['co_plan_cuenta_id'], 'importe' => $factura['ClienteFactura']['total_comprobante'], 'referencia' => '', 'error' => 0, 'error_descripcion' => '');
			$noGravado = array('co_plan_cuenta_id' => $detalleFactura[0]['ClienteFacturaDetalle']['co_plan_cuenta_id'], 'importe' => $detalleFactura[0]['ClienteFacturaDetalle']['precio_total'] * (-1), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');
                    endif;
		else:
                    $tipoTotal      = $this->ClienteTipoAsientoRenglon->find('all', array('conditions' => array('ClienteTipoAsientoRenglon.cliente_tipo_asiento_id' => $factura['ClienteFactura']['cliente_tipo_asiento_id'], 'ClienteTipoAsientoRenglon.variable' => 'TOTAL')));
                    $tipoNGravado   = $this->ClienteTipoAsientoRenglon->find('all', array('conditions' => array('ClienteTipoAsientoRenglon.cliente_tipo_asiento_id' => $factura['ClienteFactura']['cliente_tipo_asiento_id'], 'ClienteTipoAsientoRenglon.variable' => 'NGRAV')));
			
			
                    if(empty($tipoTotal[0]['ClienteTipoAsientoRenglon']['co_plan_cuenta_id'])) $totalComprobante = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ClienteFactura']['total_comprobante'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
                    else $totalComprobante = array('co_plan_cuenta_id' => $tipoTotal[0]['ClienteTipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoTotal[0]['ClienteTipoAsientoRenglon']['debe_haber'] == 'D' ? $factura['ClienteFactura']['total_comprobante'] : $factura['ClienteFactura']['total_comprobante'] * (-1)), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');
			
                    if(empty($tipoNGravado[0]['ClienteTipoAsientoRenglon']['co_plan_cuenta_id'])) $noGravado = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ClienteFactura']['importe_no_gravado'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
                    else $noGravado = array('co_plan_cuenta_id' => $tipoNGravado[0]['ClienteTipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoNGravado[0]['ClienteTipoAsientoRenglon']['debe_haber'] == 'H' ? $factura['ClienteFactura']['importe_no_gravado'] * (-1) : $factura['ClienteFactura']['importe_no_gravado']), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');
				
		endif;
		
		$asiento = array();
		
		if($totalComprobante['importe'] != 0) array_push($asiento, $totalComprobante);
		if($noGravado['importe'] != 0) array_push($asiento, $noGravado);
		
		
		return $asiento;
	}
	
	
	function getAsientoCredito($factura){
		$this->ClienteTipoAsientoRenglon = $this->importarModelo('ClienteTipoAsientoRenglon', 'clientes');
		
		if(empty($factura['ClienteFactura']['cliente_tipo_asiento_id'])):
			$noGravado = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ClienteFactura']['importe_no_gravado'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
			$totalComprobante = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ClienteFactura']['total_comprobante'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
		else:
			$tipoNGravado   = $this->ClienteTipoAsientoRenglon->find('all', array('conditions' => array('ClienteTipoAsientoRenglon.cliente_tipo_asiento_id' => $factura['ClienteFactura']['cliente_tipo_asiento_id'], 'ClienteTipoAsientoRenglon.variable' => 'NGRAV')));
			$tipoTotal      = $this->ClienteTipoAsientoRenglon->find('all', array('conditions' => array('ClienteTipoAsientoRenglon.cliente_tipo_asiento_id' => $factura['ClienteFactura']['cliente_tipo_asiento_id'], 'ClienteTipoAsientoRenglon.variable' => 'TOTAL')));
			
			if(empty($tipoNGravado[0]['ClienteTipoAsientoRenglon']['co_plan_cuenta_id'])) $noGravado = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ClienteFactura']['importe_no_gravado'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $noGravado = array('co_plan_cuenta_id' => $tipoNGravado[0]['ClienteTipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoNGravado[0]['ClienteTipoAsientoRenglon']['debe_haber'] == 'H' ? $factura['ClienteFactura']['importe_no_gravado'] * (-1) : $factura['ClienteFactura']['importe_no_gravado']), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');
				
			if(empty($tipoTotal[0]['ClienteTipoAsientoRenglon']['co_plan_cuenta_id'])) $totalComprobante = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ClienteFactura']['total_comprobante'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $totalComprobante = array('co_plan_cuenta_id' => $tipoTotal[0]['ClienteTipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoTotal[0]['ClienteTipoAsientoRenglon']['debe_haber'] == 'D' ? $factura['ClienteFactura']['total_comprobante'] : $factura['ClienteFactura']['total_comprobante'] * (-1)), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');
			
		endif;
		
		$asiento = array();
		
		if($noGravado['importe'] != 0) array_push($asiento, $noGravado);
		if($totalComprobante['importe'] != 0) array_push($asiento, $totalComprobante);
		
		
		return $asiento;
		
	}
	
	
	function grabarFacturaCliente($factura){
            // Clientes
            $oCliente = $this->importarModelo('Cliente', 'clientes');

            // Factura Detalle
            $oFacturaDetalle = $this->importarModelo('ClienteFacturaDetalle', 'clientes');

            $glb = $this->getGlobalDato('entero_1','CONTCLIEFACT');

            $aCliente = $oCliente->getCliente($factura['ClienteFactura']['cliente_id']);

            // Tipo de Comprobante o Documento
            $oTipoDocumento = $this->importarModelo('TipoDocumento', 'config');
            $aComprobante = $oTipoDocumento->read(null, $factura['ClienteFactura']['tipo_talonario']);
		    	
            $renglonesFactura = base64_decode($factura['ClienteFacturaDetalle']['renglonesSerialize']);
            $renglonesFactura = unserialize($renglonesFactura);

            $facturaCliente = array('ClienteFactura' => array(
                'id' => 0,
                'tipo' => $factura['ClienteFactura']['tipo'],
                'cliente_id' => $factura['ClienteFactura']['cliente_id'],
                'fecha_comprobante' => $factura['ClienteFactura']['fecha'],
                'tipo_comprobante' => $aComprobante['TipoDocumento']['documento'],
                'letra_comprobante' => $aComprobante['TipoDocumento']['letra'],
                'punto_venta_comprobante' => str_pad($aComprobante['TipoDocumento']['sucursal'], 4, 0, STR_PAD_LEFT),
                'numero_comprobante' => '0',
                'importe_no_gravado' => $factura['ClienteFactura']['total_factura'],
                'total_comprobante' => $factura['ClienteFactura']['total_factura'],
                'co_plan_cuenta_id' => $factura['ClienteFactura']['co_plan_cuenta_id'],
                'vencimiento1' => $factura['ClienteFactura']['fecha'],
                'importe_venc1' => $factura['ClienteFactura']['total_factura'],
                'estado' => 'A',
                'cliente_tipo_asiento_id' => 0,
                'liquidacion_id' => 0,
                'socio_id' => 0,
                'orden_descuento_id' => 0,
                'cancelacion_orden_id' => 0,
                'orden_descuento_cobro_id' => 0,
                'orden_caja_cobro_id' => 0,
                'comentario' => $factura['ClienteFactura']['observacion']
            ));

            // Busco el Numero de la Factura
            $nroFactura = $oTipoDocumento->getNumero($aComprobante['TipoDocumento']['documento']);
            if($nroFactura == 0):
                return false;
            endif;
            $facturaCliente['ClienteFactura']['numero_comprobante'] = str_pad($nroFactura, 8, 0, STR_PAD_LEFT);


            // Grabo la cabecera de la Factura
            $this->begin();
            if(!$this->save($facturaCliente)):		
                $this->rollback();
                $oTipoDocumento->unLookRegistro($aComprobante['TipoDocumento']['documento']);
                return false;
            endif;

            $facturaDetalle = array();
            foreach($renglonesFactura as $renglon):
                $tmpFacturaDetalle = array('ClienteFacturaDetalle' => array(
                    'id' => 0,
                    'cliente_factura_id' => $this->getLastInsertID(),
                    'producto' => $renglon['ClienteFacturaDetalle']['descripcion_producto'],
                    'co_plan_cuenta_id' => $renglon['ClienteFacturaDetalle']['co_plan_cuenta_id'],
                    'cantidad' => $renglon['ClienteFacturaDetalle']['cantidad'],
                    'precio_unitario' => $renglon['ClienteFacturaDetalle']['importe_unitario'],
                    'precio_total' => $renglon['ClienteFacturaDetalle']['cantidad'] * $renglon['ClienteFacturaDetalle']['importe_unitario']
                ));

                array_push($facturaDetalle, $tmpFacturaDetalle);
            endforeach;

            // Grabo el Detalle de la Factura
            if(!$oFacturaDetalle->saveAll($facturaDetalle)):
                $this->rollback();
                $oTipoDocumento->unLookRegistro($aComprobante['TipoDocumento']['documento']);
                return false;
            endif;

            $this->commit();
            $oTipoDocumento->putNumero($aComprobante['TipoDocumento']['documento']);

            return true;
		
	}
}	

?>