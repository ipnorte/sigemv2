<?php
class ProveedorFactura extends ProveedoresAppModel{
	var $name = 'ProveedorFactura';
	
	function getFactura($id){
		$aFactura = $this->read(null,$id);
		
// 		$glb = $this->getGlobalDato('concepto_1',$aFactura['ProveedorFactura']['tipo_comprobante']);
// 		$aFactura['ProveedorFactura']['tipo_comprobante_desc'] = $glb['GlobalDato']['concepto_1'];
			
// 		if($aFactura['ProveedorFactura']['tipo_comprobante'] == 'SALDOPROVEED') $aFactura['ProveedorFactura']['tipo_comprobante_desc'] = 'SALDO ANTERIOR';
// 		else $aFactura['ProveedorFactura']['tipo_comprobante_desc'] .= ' ' . $aFactura['ProveedorFactura']['letra_comprobante'] . ' ' . $aFactura['ProveedorFactura']['punto_venta_comprobante'] . '-' . $aFactura['ProveedorFactura']['numero_comprobante'];

// 		#traigo los pagos
//     	$oFactura = $this->importarModelo('OrdenPagoFactura', 'proveedores');
//     	$aFactura['ProveedorFactura']['pagos'] = $oFactura->getPagoFactura($id);		
// 		$aFactura['ProveedorFactura']['saldo'] = round($aFactura['ProveedorFactura']['total_comprobante'] - $aFactura['ProveedorFactura']['pagos'],2);
    	
		$aFactura = $this->armaDatos($aFactura);
    	
		return $aFactura['ProveedorFactura'];
		
	}
	
	function tienePago($id){
    	$oFactura = $this->importarModelo('OrdenPagoFactura', 'proveedores');
    	$pago = $oFactura->getPagoFactura($id);
    	if($pago > 0) return true;
    	return false; 
		
	}

	
	function grabarFacturaLiquidacion($factura, $liquidacion){
    	$oProveedor = $this->importarModelo('Proveedor', 'proveedores');
    	$aProveedor = $oProveedor->getProveedor($factura['proveedor_id']);
    	
    	if($factura['tipo_documento_proveedor'] == 'NC') $glb = $this->getGlobalDato('entero_1','CONTPROVNCRE');
		else $glb = $this->getGlobalDato('entero_1','CONTPROVFACT');

		
		$facturaLiquidacion = array(
			'id' => 0,
			'tipo' => ($factura['tipo_documento_proveedor'] == 'FAC' ? 'FA' : 'NC'),
			'proveedor_id' => $factura['proveedor_id'],
			'fecha_comprobante' => $liquidacion['Liquidacion']['fecha_imputacion'],
			'tipo_comprobante' => ($factura['tipo_documento_proveedor'] == 'FAC' ? 'PROVDOCUFACT' : 'PROVDOCUNCRE'),
			'letra_comprobante' => 'L',
			'punto_venta_comprobante' => '00001',
			'numero_comprobante' => str_pad($liquidacion['Liquidacion']['id'], 8, 0, STR_PAD_LEFT),
			'importe_no_gravado' => $factura['importe_proveedor'],
			'total_comprobante' => $factura['importe_proveedor'],
			'co_plan_cuenta_id' => $aProveedor['Proveedor']['co_plan_cuenta_id'],
			'periodo_iva' => $liquidacion['Liquidacion']['periodo'],
			'vencimiento1' => $liquidacion['Liquidacion']['fecha_imputacion'],
			'importe_venc1' => $factura['importe_proveedor'],
			'estado' => 'A',
			'concepto_gasto' => $aProveedor['Proveedor']['concepto_gasto'],
//			'proveedor_tipo_asiento_id' => $aProveedor['Proveedor']['proveedor_tipo_asiento_id'],
			'proveedor_tipo_asiento_id' => $glb['GlobalDato']['entero_1'],
			'liquidacion_id' => $liquidacion['Liquidacion']['id'],
			'comentario' => trim($factura['descripcion_proveedor']) . ' - ' . $liquidacion['Liquidacion']['organismo']
		);

		if($this->save($facturaLiquidacion)):
			return $this->getLastInsertID();
		endif;
		return false;
	}
	
	
	function grabarFacturaCancelacion($factura){
            $oProveedor = $this->importarModelo('Proveedor', 'proveedores');
            $oOrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');

            $aProveedor = $oProveedor->getProveedor($factura['orden_proveedor_id']);
            $aOrdenDescuentoCobro = $oOrdenDescuentoCobro->getCobro($factura['orden_descuento_cobro_id']);
    	
            $glb = $this->getGlobalDato('entero_1','CONTPROVFACT');

            $facturaLiquidacion = array(
                'id' => 0,
                'tipo' => 'FA',
                'proveedor_id' => $factura['orden_proveedor_id'],
                'fecha_comprobante' => $factura['fecha_imputacion'],
                'tipo_comprobante' => 'PROVDOCUFACT',
                'letra_comprobante' => 'L',
                'punto_venta_comprobante' => '00001',
                'numero_comprobante' => str_pad($factura['id'], 8, 0, STR_PAD_LEFT),
                'importe_no_gravado' => $factura['importe_proveedor'],
                'total_comprobante' => $factura['importe_proveedor'],
                'co_plan_cuenta_id' => $aProveedor['Proveedor']['co_plan_cuenta_id'],
                'periodo_iva' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['periodo_cobro'],
                'vencimiento1' => $factura['fecha_vto'],
                'importe_venc1' => $factura['importe_proveedor'],
                'estado' => 'A',
                'concepto_gasto' => $aProveedor['Proveedor']['concepto_gasto'],
//			'proveedor_tipo_asiento_id' => $aProveedor['Proveedor']['proveedor_tipo_asiento_id'],
                'proveedor_tipo_asiento_id' => $glb['GlobalDato']['entero_1'],
    		'socio_id' => $factura['socio_id'],
    		'cancelacion_orden_id' => $factura['id'],
    		'orden_descuento_cobro_id' => $factura['orden_descuento_cobro_id'],
                'comentario' => $factura['recibo_detalle']
            );

            if($this->save($facturaLiquidacion)):
                return $this->getLastInsertID();
            endif;
            return false;
	}
	
	
	/**
	 * Setea los datos adicionales de una factura
	 * @param array $aFactura
	 * @return array
	 * Adrian
	 */
	function armaDatos($aFactura){
		
		$aFactura['ProveedorFactura']['tipo_comprobante_desc'] = parent::GlobalDato('concepto_1', $aFactura['ProveedorFactura']['tipo_comprobante']);
		
		
		if($aFactura['ProveedorFactura']['tipo_comprobante'] == 'SALDOPROVEED') $aFactura['ProveedorFactura']['tipo_comprobante_desc'] = 'SALDO ANTERIOR';
		else $aFactura['ProveedorFactura']['tipo_comprobante_desc'] .= ' ' . $aFactura['ProveedorFactura']['letra_comprobante'] . ' ' . $aFactura['ProveedorFactura']['punto_venta_comprobante'] . '-' . $aFactura['ProveedorFactura']['numero_comprobante'];

		$aFactura['ProveedorFactura']['tipo_comprobante_desc2'] = $aFactura['ProveedorFactura']['tipo'] . " " .  $aFactura['ProveedorFactura']['letra_comprobante'] . ' ' . $aFactura['ProveedorFactura']['punto_venta_comprobante'] . '-' . $aFactura['ProveedorFactura']['numero_comprobante'];
		
		
		#traigo los pagos
		$oFactura = $this->importarModelo('OrdenPagoFactura', 'proveedores');
		$aFactura['ProveedorFactura']['pagos'] = $oFactura->getPagoFactura($aFactura['ProveedorFactura']['id']);
		$aFactura['ProveedorFactura']['saldo'] = round($aFactura['ProveedorFactura']['total_comprobante'] - $aFactura['ProveedorFactura']['pagos'],2);

		//razon social proveedor
		App::import('Model','proveedores.Proveedor');
		$oPROV = new Proveedor();
		
		$rz = $oPROV->getRazonSocialAndRazonSocialResumida($aFactura['ProveedorFactura']['proveedor_id']);
		$aFactura['ProveedorFactura']['proveedor_razon_social'] = $rz['razon_social'];
		$aFactura['ProveedorFactura']['proveedor_razon_social_resumida'] = $rz['razon_social_resumida'];
		
		
		return $aFactura;
	}
	
	/**
	 * Carga las facturas asociadas a una orden de cancelacion
	 * @param int $ordenCancelacionId
	 * Adrian
	 */
	function getFacturasByCancelacionId($ordenCancelacionId){
		
		$facturas = $this->find('all',array('conditions' => array('ProveedorFactura.cancelacion_orden_id' => $ordenCancelacionId), 'order' => array('ProveedorFactura.fecha_comprobante ASC')));
		
		if(empty($facturas)) return null;
		
		foreach($facturas as $idx => $factura){
			
			$facturas[$idx] = $this->armaDatos($factura);
			
		}
		return $facturas;
		
	}
	

	/**
	 * 
	 * Graba la factura por la Cancelacion
	 * @param int $id
	 * @return int
	 * Gustavo 
	 */
	function grabarFacturaByCancelacion($id){

		$oOrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');
    	$aOrdenDescuentoCobro = $oOrdenDescuentoCobro->getCobro($id, true);

    	if(empty($aOrdenDescuentoCobro['ProveedorLiquidacion'])) return 'A';
    	
    	if($aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['facturar'] == 0) return 'A';
    	
    	
		if($aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['proveedor_origen_fondo_id'] == 
    		$aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['proveedor_id'] || 
    		$aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['proveedor_id'] == MUTUALPROVEEDORID) return 'A';
    	
    	$this->Proveedor = $this->importarModelo('Proveedor', 'proveedores');
    	$aProveedor = $this->Proveedor->getProveedor($aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['proveedor_id']);
    	
		$glb = $this->getGlobalDato('entero_1','CONTPROVFACT');

    	$facturaLiquidacion = array(
			'id' => 0,
			'tipo' => 'FA',
			'proveedor_id' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['proveedor_id'],
			'fecha_comprobante' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['fecha'],
			'tipo_comprobante' => 'PROVDOCUFACT',
			'letra_comprobante' => 'L',
			'punto_venta_comprobante' => '00001',
			'numero_comprobante' => str_pad($aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['cancelacion_orden_id'], 8, 0, STR_PAD_LEFT),
			'importe_no_gravado' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['importe'],
			'total_comprobante' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['importe'],
			'co_plan_cuenta_id' => $aProveedor['Proveedor']['co_plan_cuenta_id'],
			'periodo_iva' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['periodo_cobro'],
			'vencimiento1' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['fecha'],
			'importe_venc1' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['importe'],
			'estado' => 'A',
			'concepto_gasto' => $aProveedor['Proveedor']['concepto_gasto'],
//			'proveedor_tipo_asiento_id' => $aProveedor['Proveedor']['proveedor_tipo_asiento_id'],
			'proveedor_tipo_asiento_id' => $glb['GlobalDato']['entero_1'],
    		'socio_id' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['socio_id'],
    		'orden_descuento_id' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['orden_descuento_id'],
    		'cancelacion_orden_id' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['cancelacion_orden_id'],
    		'orden_descuento_cobro_id' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['id'],
			'comentario' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['concepto']
		);
		
		$this->id = 0;
		if($this->save($facturaLiquidacion)):
			return $this->getLastInsertID();
		endif;
		
		return false;
	}
	
	
	/**
	 * 
	 * Graba la factura por Caja
	 * @param int $id
	 * @return boolean
	 * Gustavo
	 */
	function grabarFacturaByCaja($id){

		$oOrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');
    	$aOrdenDescuentoCobro = $oOrdenDescuentoCobro->getCobro($id, true);
    	
		$this->Proveedor = $this->importarModelo('Proveedor', 'proveedores');

    	
		foreach ($aOrdenDescuentoCobro['ProveedorLiquidacion'] as $aCobro):
		   	if($aCobro['facturar'] == 1):
		
				if(!($aCobro['proveedor_origen_fondo_id'] == 
		    		$aCobro['proveedor_id'] || 
		    		$aCobro['proveedor_id'] == MUTUALPROVEEDORID)):
	    	
					$glb = $this->getGlobalDato('entero_1','CONTPROVFACT');
			
					$aProveedor = $this->Proveedor->getProveedor($aCobro['proveedor_id']);
			    	
			    	$facturaLiquidacion = array(
						'id' => 0,
						'tipo' => 'FA',
						'proveedor_id' => $aCobro['proveedor_id'],
						'fecha_comprobante' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['fecha'],
						'tipo_comprobante' => 'PROVDOCUFACT',
						'letra_comprobante' => 'L',
						'punto_venta_comprobante' => '00001',
						'numero_comprobante' => str_pad($aCobro['orden_descuento_id'], 8, 0, STR_PAD_LEFT),
						'importe_no_gravado' => $aCobro['importe'],
						'total_comprobante' => $aCobro['importe'],
						'co_plan_cuenta_id' => $aProveedor['Proveedor']['co_plan_cuenta_id'],
						'periodo_iva' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['periodo_cobro'],
						'vencimiento1' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['fecha'],
						'importe_venc1' => $aCobro['importe'],
						'estado' => 'A',
						'concepto_gasto' => $aProveedor['Proveedor']['concepto_gasto'],
//						'proveedor_tipo_asiento_id' => $aProveedor['Proveedor']['proveedor_tipo_asiento_id'],
						'proveedor_tipo_asiento_id' => $glb['GlobalDato']['entero_1'],
			    		'socio_id' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['socio_id'],
			    		'orden_descuento_id' => $aCobro['orden_descuento_id'],
			    		'cancelacion_orden_id' => $aCobro['cancelacion_orden_id'],
			    		'orden_descuento_cobro_id' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['id'],
						'comentario' => $aCobro['concepto']
					);
	
					$this->id = 0;
					if(!$this->save($facturaLiquidacion)):
						return false;
					endif;
				endif;
			endif;
		endforeach;		

		return true;
	}
	
	
	function prepararFacturaProveedor($factura){
		
		// Proveedores
		$oProveedor = $this->importarModelo('Proveedor', 'proveedores');
		$aProveedor = $oProveedor->getProveedor($factura['orden_descuento_cobro_cuotas']['proveedor_id']);
		
		$glb = $this->getGlobalDato('entero_1','CONTPROVFACT');
			
		return $facturaLiquidacion = array(
			'id' => 0,
			'tipo' => 'FA',
			'proveedor_id' => $factura['orden_descuentos']['proveedor_id'],
			'fecha_comprobante' => $factura['orden_descuento_cobro_cuotas']['fecha_comprobante'],
			'tipo_comprobante' => 'PROVDOCUFACT',
			'letra_comprobante' => 'L',
			'punto_venta_comprobante' => '00001',
			'numero_comprobante' => str_pad($factura['orden_descuentos']['id'], 8, 0, STR_PAD_LEFT),
			'importe_no_gravado' => $factura[0]['importe'],
			'total_comprobante' => $factura[0]['importe'],
			'co_plan_cuenta_id' => $aProveedor['Proveedor']['co_plan_cuenta_id'],
			'periodo_iva' => $factura['orden_descuento_cobro_cuotas']['periodo_cobro'],
			'vencimiento1' => $factura['orden_descuento_cobro_cuotas']['fecha_comprobante'],
			'importe_venc1' => $factura[0]['importe'],
			'estado' => 'A',
			'concepto_gasto' => $aProveedor['Proveedor']['concepto_gasto'],
//			'proveedor_tipo_asiento_id' => $aProveedor['Proveedor']['proveedor_tipo_asiento_id'],
			'proveedor_tipo_asiento_id' => $glb['GlobalDato']['entero_1'],
			'orden_descuento_id' => $factura['orden_descuentos']['id'],
    		'orden_caja_cobro_id' => $factura['orden_descuento_cobro_cuotas']['orden_caja_cobro_id'],
			'orden_descuento_cobro_id' => $factura['orden_descuento_cobro_cuotas']['orden_descuento_cobro_id'],
    		'socio_id' => $factura['orden_descuentos']['socio_id'],
    		'comentario' => $factura['concepto']
		);
					
	}
	
	
	
	function prepararCreditoProveedor($factura){
		
		// Proveedores
		$oProveedor = $this->importarModelo('Proveedor', 'proveedores');
		$aProveedor = $oProveedor->getProveedor($factura['OrdenDescuentoCobro']['proveedor_origen_fondo_id']);
		
		$glb = $this->getGlobalDato('entero_1','CONTPROVNCRE');
			
		return $facturaLiquidacion = array(
			'id' => 0,
			'tipo' => 'NC',
			'proveedor_id' => $factura['OrdenDescuentoCobro']['proveedor_origen_fondo_id'],
			'fecha_comprobante' => $factura['OrdenDescuentoCobro']['fecha_comprobante'],
			'tipo_comprobante' => 'PROVDOCUNCRE',
			'letra_comprobante' => 'L',
			'punto_venta_comprobante' => '00001',
			'numero_comprobante' => str_pad($factura['OrdenDescuentoCobro']['orden_caja_cobro_id'], 8, 0, STR_PAD_LEFT),
			'importe_no_gravado' => $factura['OrdenDescuentoCobro']['importe_cobro'],
			'total_comprobante' => $factura['OrdenDescuentoCobro']['importe_cobro'],
			'co_plan_cuenta_id' => $aProveedor['Proveedor']['co_plan_cuenta_id'],
			'periodo_iva' => $factura['OrdenDescuentoCobro']['periodo_cobro'],
			'vencimiento1' => $factura['OrdenDescuentoCobro']['fecha_comprobante'],
			'importe_venc1' => $factura['OrdenDescuentoCobro']['importe_cobro'],
			'estado' => 'A',
			'concepto_gasto' => $aProveedor['Proveedor']['concepto_gasto'],
//			'proveedor_tipo_asiento_id' => $aProveedor['Proveedor']['proveedor_tipo_asiento_id'],
			'proveedor_tipo_asiento_id' => $glb['GlobalDato']['entero_1'],
			'orden_descuento_id' => 0,
    		'orden_caja_cobro_id' => $factura['OrdenDescuentoCobro']['orden_caja_cobro_id'],
			'orden_descuento_cobro_id' => $factura['OrdenDescuentoCobro']['orden_descuento_cobro_id'],
    		'socio_id' => $factura['OrdenDescuentoCobro']['cabecera_socio_id'],
    		'comentario' => $factura['OrdenDescuentoCobro']['observacion']
		);
					
	}
	
	function prepararCreditoProvCancela($factura){
		
		// Proveedores
		$oProveedor = $this->importarModelo('Proveedor', 'proveedores');
		$aProveedor = $oProveedor->getProveedor($factura['CancelacionOrden']['proveedor_origen_id']);
		
		$glb = $this->getGlobalDato('entero_1','CONTPROVNCRE');
			
		return $facturaLiquidacion = array(
			'id' => 0,
			'tipo' => 'NC',
			'proveedor_id' => $factura['CancelacionOrden']['proveedor_origen_id'],
			'fecha_comprobante' => $factura['CancelacionOrden']['fecha_comprobante'],
			'tipo_comprobante' => 'PROVDOCUNCRE',
			'letra_comprobante' => 'L',
			'punto_venta_comprobante' => '00001',
			'numero_comprobante' => str_pad($factura['CancelacionOrden']['cabecera_socio_id'], 8, 0, STR_PAD_LEFT),
			'importe_no_gravado' => $factura['CancelacionOrden']['importe_cobro'],
			'total_comprobante' => $factura['CancelacionOrden']['importe_cobro'],
			'co_plan_cuenta_id' => $aProveedor['Proveedor']['co_plan_cuenta_id'],
			'periodo_iva' => $factura['CancelacionOrden']['periodo_cobro'],
			'vencimiento1' => $factura['CancelacionOrden']['fecha_comprobante'],
			'importe_venc1' => $factura['CancelacionOrden']['importe_cobro'],
			'estado' => 'A',
			'concepto_gasto' => $aProveedor['Proveedor']['concepto_gasto'],
			'proveedor_tipo_asiento_id' => $aProveedor['Proveedor']['proveedor_tipo_asiento_id'],
			'proveedor_tipo_asiento_id' => $glb['GlobalDato']['entero_1'],
			'orden_descuento_id' => 0,
//    		'orden_caja_cobro_id' => $factura['CancelacionOrden']['orden_caja_cobro_id'],
//			'orden_descuento_cobro_id' => $factura['OrdenDescuentoCobro']['orden_descuento_cobro_id'],
    		'socio_id' => $factura['CancelacionOrden']['cabecera_socio_id'],
    		'comentario' => $factura['CancelacionOrden']['observacion'],
    		'razon_social' => $aProveedor['Proveedor']['razon_social']
		);
					
	}

	
	function getCodigoPlanCuenta($id){
//		$oProveedorTipoAsientoRenglon = $this->importarModelo('TipoAsientoRenglon', 'proveedores');
		App::import('Model','proveedores.TipoAsientoRenglon');
		$oProveedorTipoAsientoRenglon = new TipoAsientorenglon();
		
		$factura = $this->read(null, $id);
		$retorno = array('co_plan_cuenta_id' => 0, 'error' => 'OK', 'tipo_asiento' => 0);
		
		if(empty($factura['ProveedorFactura']['co_plan_cuenta_id']) && empty($factura['ProveedorFactura']['proveedor_tipo_asiento_id'])):
                    $retorno['error'] = 'FALTA DEFINIR ASIENTO EN FACTURAS';
		else:
                    if($factura['ProveedorFactura']['proveedor_tipo_asiento_id'] > 0):
//                            if(empty($factura['ProveedorFactura']['proveedor_tipo_asiento_id'])):
//                                   $retorno['error'] = 'FALTA DEFINIR ASIENTO EN FACTURAS';
//                            else:
    //				$tipoAsiento = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'TOTAL')));
                                    $sqlTipoAsiento = "SELECT TipoAsientoRenglon.* FROM proveedor_tipo_asiento_renglones TipoAsientoRenglon WHERE TipoAsientoRenglon.proveedor_tipo_asiento_id = '" . $factura['ProveedorFactura']['proveedor_tipo_asiento_id'] . "' AND TipoAsientoRenglon.variable = 'TOTAL'";
                                    $tipoAsiento = $this->query($sqlTipoAsiento);

                                    if(empty($tipoAsiento)): 
                                            $retorno['error'] = 'ASIENTO NO DEFINIDO';
                                    else: 
                                            $retorno['co_plan_cuenta_id'] = $tipoAsiento[0]['TipoAsientoRenglon']['co_plan_cuenta_id'];
                                            $retorno['tipo_asiento'] = $tipoAsiento[0]['TipoAsientoRenglon']['proveedor_tipo_asiento_id'];
                                    endif;
 //                           endif;
                    else:
                            $retorno['co_plan_cuenta_id'] = $factura['ProveedorFactura']['co_plan_cuenta_id'];
                    endif;
                endif;
		
		return $retorno;
	}
	
	
	function getAsientoCredito($factura){
//		$oProveedorTipoAsientoRenglon = $this->importarModelo('TipoAsientoRenglon', 'proveedores');
		App::import('Model','proveedores.TipoAsientoRenglon');
		$oProveedorTipoAsientoRenglon = new TipoAsientorenglon();
		
		if(empty($factura['ProveedorFactura']['proveedor_tipo_asiento_id'])):
			$totalComprobante = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['total_comprobante'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
			$netoGravado = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['importe_gravado'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
			$iva = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['importe_iva'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
			$noGravado = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['importe_no_gravado'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
			$percepcion = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['percepcion'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
			$retencion = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['retencion'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
			$impuestoInterno = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['impuesto_interno'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
			$ingresoBruto = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['ingreso_bruto'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
			$otrosImpuesto = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['otro_impuesto'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
		else:
			$sqlTipoAsiento = "SELECT TipoAsientoRenglon.* FROM proveedor_tipo_asiento_renglones TipoAsientoRenglon WHERE TipoAsientoRenglon.proveedor_tipo_asiento_id = '" . $factura['ProveedorFactura']['proveedor_tipo_asiento_id'] . "'";
			$tipoAsiento = $this->query($sqlTipoAsiento);
			
			$tipoTotal      = array();
			$tipoGravado    = array();
			$tipoIva        = array();
			$tipoNGravado   = array();
			$tipoPercepcion = array();
			$tipoRetencion  = array();
			$tipoImpInt     = array();
			$tipoIngBruto   = array();
			$tipoOtroImp    = array();
			
			foreach($tipoAsiento as $renglon):			
				
				if($renglon['TipoAsientoRenglon']['variable'] == 'TOTAL') array_push($tipoTotal, $renglon);
				if($renglon['TipoAsientoRenglon']['variable'] == 'GRAVA') array_push($tipoGravado, $renglon);
				if($renglon['TipoAsientoRenglon']['variable'] == 'IVA') array_push($tipoIva, $renglon);
				if($renglon['TipoAsientoRenglon']['variable'] == 'NGRAV') array_push($tipoNGravado, $renglon);
				if($renglon['TipoAsientoRenglon']['variable'] == 'PERCE') array_push($tipoPercepcion, $renglon);
				if($renglon['TipoAsientoRenglon']['variable'] == 'RETEN') array_push($tipoRetencion, $renglon);
				if($renglon['TipoAsientoRenglon']['variable'] == 'IMINT') array_push($tipoImpInt, $renglon);
				if($renglon['TipoAsientoRenglon']['variable'] == 'INBRU') array_push($tipoIngBruto, $renglon);
				if($renglon['TipoAsientoRenglon']['variable'] == 'OTROS') array_push($tipoOtroImp, $renglon);

			endforeach;
						
//			$tipoTotal      = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'TOTAL')));
//			$tipoGravado    = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'GRAVA')));
//			$tipoIva        = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'IVA')));
//			$tipoNGravado   = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'NGRAV')));
//			$tipoPercepcion = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'PERCE')));
//			$tipoRetencion  = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'RETEN')));
//			$tipoImpInt     = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'IMINT')));
//			$tipoIngBruto   = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'INBRU')));
//			$tipoOtroImp    = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'OTROS')));
			
			if(empty($tipoTotal[0]['TipoAsientoRenglon']['co_plan_cuenta_id'])) $totalComprobante = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['total_comprobante'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $totalComprobante = array('co_plan_cuenta_id' => $tipoTotal[0]['TipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoTotal[0]['TipoAsientoRenglon']['debe_haber'] == 'D' ? $factura['ProveedorFactura']['total_comprobante'] : $factura['ProveedorFactura']['total_comprobante'] * (-1)), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');
			
			if(empty($tipoGravado[0]['TipoAsientoRenglon']['co_plan_cuenta_id'])) $netoGravado = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['importe_gravado'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $netoGravado = array('co_plan_cuenta_id' => $tipoGravado[0]['TipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoGravado[0]['TipoAsientoRenglon']['debe_haber'] == 'H' ? $factura['ProveedorFactura']['importe_gravado'] * (-1) : $factura['ProveedorFactura']['importe_gravado']), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');

			if(empty($tipoIva[0]['TipoAsientoRenglon']['co_plan_cuenta_id'])) $iva = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['importe_iva'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $iva = array('co_plan_cuenta_id' => $tipoIva[0]['TipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoIva[0]['TipoAsientoRenglon']['debe_haber'] == 'H' ? $factura['ProveedorFactura']['importe_iva'] * (-1) : $factura['ProveedorFactura']['importe_iva']), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');
				
			if(empty($tipoNGravado[0]['TipoAsientoRenglon']['co_plan_cuenta_id'])) $noGravado = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['importe_no_gravado'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $noGravado = array('co_plan_cuenta_id' => $tipoNGravado[0]['TipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoNGravado[0]['TipoAsientoRenglon']['debe_haber'] == 'H' ? $factura['ProveedorFactura']['importe_no_gravado'] * (-1) : $factura['ProveedorFactura']['importe_no_gravado']), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');
				
			if(empty($tipoPercepcion[0]['TipoAsientoRenglon']['co_plan_cuenta_id'])) $percepcion = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['percepcion'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $percepcion = array('co_plan_cuenta_id' => $tipoPercepcion[0]['TipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoPercepcion[0]['TipoAsientoRenglon']['debe_haber'] == 'H' ? $factura['ProveedorFactura']['percepcion'] * (-1) : $factura['ProveedorFactura']['percepcion']), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');

			if(empty($tipoRetencion[0]['TipoAsientoRenglon']['co_plan_cuenta_id'])) $retencion = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['retencion'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $retencion = array('co_plan_cuenta_id' => $tipoRetencion[0]['TipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoRetencion[0]['TipoAsientoRenglon']['debe_haber'] == 'H' ? $factura['ProveedorFactura']['retencion'] * (-1) : $factura['ProveedorFactura']['retencion']), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');

			if(empty($tipoImpInt[0]['TipoAsientoRenglon']['co_plan_cuenta_id'])) $impuestoInterno = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['impuesto_interno'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $impuestoInterno = array('co_plan_cuenta_id' => $tipoImpInt[0]['TipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoImpInt[0]['TipoAsientoRenglon']['debe_haber'] == 'H' ? $factura['ProveedorFactura']['impuesto_interno'] * (-1) : $factura['ProveedorFactura']['impuesto_interno']), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');

			if(empty($tipoIngBruto[0]['TipoAsientoRenglon']['co_plan_cuenta_id'])) $ingresoBruto = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['ingreso_bruto'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $ingresoBruto = array('co_plan_cuenta_id' => $tipoIngBruto[0]['TipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoIngBruto[0]['TipoAsientoRenglon']['debe_haber'] == 'H' ? $factura['ProveedorFactura']['ingreso_bruto'] * (-1) : $factura['ProveedorFactura']['ingreso_bruto']), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');

			if(empty($tipoOtroImp[0]['TipoAsientoRenglon']['co_plan_cuenta_id'])) $otrosImpuesto = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['otro_impuesto'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $otrosImpuesto = array('co_plan_cuenta_id' => $tipoOtroImp[0]['TipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoOtroImp[0]['TipoAsientoRenglon']['debe_haber'] == 'H' ? $factura['ProveedorFactura']['otro_impuesto'] * (-1) : $factura['ProveedorFactura']['otro_impuesto']), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');
		endif;
		
		$asiento = array();
		
		if($totalComprobante['importe'] != 0) array_push($asiento, $totalComprobante);
		if($netoGravado['importe'] != 0)      array_push($asiento, $netoGravado);
		if($iva['importe'] != 0) array_push($asiento, $iva);
		if($noGravado['importe'] != 0) array_push($asiento, $noGravado);
		if($percepcion['importe'] != 0) array_push($asiento, $percepcion);
		if($retencion['importe'] != 0) array_push($asiento, $retencion);
		if($impuestoInterno['importe'] != 0) array_push($asiento, $impuestoInterno);
		if($ingresoBruto['importe'] != 0) array_push($asiento, $ingresoBruto);
		if($otrosImpuesto['importe'] != 0) array_push($asiento, $otrosImpuesto);
		
		
		return $asiento;
	}
	
	
	function getAsientoFactura($factura){
//		$oProveedorTipoAsientoRenglon = $this->importarModelo('TipoAsientoRenglon', 'proveedores');
		App::import('Model','proveedores.TipoAsientoRenglon');
		$oProveedorTipoAsientoRenglon = new TipoAsientorenglon();
		
		if(empty($factura['ProveedorFactura']['proveedor_tipo_asiento_id'])):
			$netoGravado = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['importe_gravado'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
			$iva = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['importe_iva'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
			$noGravado = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['importe_no_gravado'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
			$percepcion = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['percepcion'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
			$retencion = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['retencion'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
			$impuestoInterno = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['impuesto_interno'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
			$ingresoBruto = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['ingreso_bruto'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
			$otrosImpuesto = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['otro_impuesto'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
			$totalComprobante = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['total_comprobante'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO POSEE TIPO ASIENTO');
		else:
			$sqlTipoAsiento = "SELECT TipoAsientoRenglon.* FROM proveedor_tipo_asiento_renglones TipoAsientoRenglon WHERE TipoAsientoRenglon.proveedor_tipo_asiento_id = '" . $factura['ProveedorFactura']['proveedor_tipo_asiento_id'] . "'";
			$tipoAsiento = $this->query($sqlTipoAsiento);
			
			$tipoTotal      = array();
			$tipoGravado    = array();
			$tipoIva        = array();
			$tipoNGravado   = array();
			$tipoPercepcion = array();
			$tipoRetencion  = array();
			$tipoImpInt     = array();
			$tipoIngBruto   = array();
			$tipoOtroImp    = array();
			
			foreach($tipoAsiento as $renglon):			
				
				if($renglon['TipoAsientoRenglon']['variable'] == 'TOTAL') array_push($tipoTotal, $renglon);
				if($renglon['TipoAsientoRenglon']['variable'] == 'GRAVA') array_push($tipoGravado, $renglon);
				if($renglon['TipoAsientoRenglon']['variable'] == 'IVA') array_push($tipoIva, $renglon);
				if($renglon['TipoAsientoRenglon']['variable'] == 'NGRAV') array_push($tipoNGravado, $renglon);
				if($renglon['TipoAsientoRenglon']['variable'] == 'PERCE') array_push($tipoPercepcion, $renglon);
				if($renglon['TipoAsientoRenglon']['variable'] == 'RETEN') array_push($tipoRetencion, $renglon);
				if($renglon['TipoAsientoRenglon']['variable'] == 'IMINT') array_push($tipoImpInt, $renglon);
				if($renglon['TipoAsientoRenglon']['variable'] == 'INBRU') array_push($tipoIngBruto, $renglon);
				if($renglon['TipoAsientoRenglon']['variable'] == 'OTROS') array_push($tipoOtroImp, $renglon);

			endforeach;
			
//			$tipoGravado    = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'GRAVA')));
//			$tipoIva        = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'IVA')));
//			$tipoNGravado   = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'NGRAV')));
//			$tipoPercepcion = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'PERCE')));
//			$tipoRetencion  = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'RETEN')));
//			$tipoImpInt     = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'IMINT')));
//			$tipoIngBruto   = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'INBRU')));
//			$tipoOtroImp    = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'OTROS')));
//			$tipoTotal      = $oProveedorTipoAsientoRenglon->find('all', array('conditions' => array('TipoAsientoRenglon.proveedor_tipo_asiento_id' => $factura['ProveedorFactura']['proveedor_tipo_asiento_id'], 'TipoAsientoRenglon.variable' => 'TOTAL')));

			
			if(empty($tipoGravado[0]['TipoAsientoRenglon']['co_plan_cuenta_id'])) $netoGravado = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['importe_gravado'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $netoGravado = array('co_plan_cuenta_id' => $tipoGravado[0]['TipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoGravado[0]['TipoAsientoRenglon']['debe_haber'] == 'H' ? $factura['ProveedorFactura']['importe_gravado'] * (-1) : $factura['ProveedorFactura']['importe_gravado']), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');

			if(empty($tipoIva[0]['TipoAsientoRenglon']['co_plan_cuenta_id'])) $iva = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['importe_iva'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $iva = array('co_plan_cuenta_id' => $tipoIva[0]['TipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoIva[0]['TipoAsientoRenglon']['debe_haber'] == 'H' ? $factura['ProveedorFactura']['importe_iva'] * (-1) : $factura['ProveedorFactura']['importe_iva']), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');
				
			if(empty($tipoNGravado[0]['TipoAsientoRenglon']['co_plan_cuenta_id'])) $noGravado = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['importe_no_gravado'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $noGravado = array('co_plan_cuenta_id' => $tipoNGravado[0]['TipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoNGravado[0]['TipoAsientoRenglon']['debe_haber'] == 'H' ? $factura['ProveedorFactura']['importe_no_gravado'] * (-1) : $factura['ProveedorFactura']['importe_no_gravado']), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');
				
			if(empty($tipoPercepcion[0]['TipoAsientoRenglon']['co_plan_cuenta_id'])) $percepcion = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['percepcion'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $percepcion = array('co_plan_cuenta_id' => $tipoPercepcion[0]['TipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoPercepcion[0]['TipoAsientoRenglon']['debe_haber'] == 'H' ? $factura['ProveedorFactura']['percepcion'] * (-1) : $factura['ProveedorFactura']['percepcion']), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');

			if(empty($tipoRetencion[0]['TipoAsientoRenglon']['co_plan_cuenta_id'])) $retencion = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['retencion'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $retencion = array('co_plan_cuenta_id' => $tipoRetencion[0]['TipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoRetencion[0]['TipoAsientoRenglon']['debe_haber'] == 'H' ? $factura['ProveedorFactura']['retencion'] * (-1) : $factura['ProveedorFactura']['retencion']), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');

			if(empty($tipoImpInt[0]['TipoAsientoRenglon']['co_plan_cuenta_id'])) $impuestoInterno = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['impuesto_interno'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $impuestoInterno = array('co_plan_cuenta_id' => $tipoImpInt[0]['TipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoImpInt[0]['TipoAsientoRenglon']['debe_haber'] == 'H' ? $factura['ProveedorFactura']['impuesto_interno'] * (-1) : $factura['ProveedorFactura']['impuesto_interno']), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');

			if(empty($tipoIngBruto[0]['TipoAsientoRenglon']['co_plan_cuenta_id'])) $ingresoBruto = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['ingreso_bruto'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $ingresoBruto = array('co_plan_cuenta_id' => $tipoIngBruto[0]['TipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoIngBruto[0]['TipoAsientoRenglon']['debe_haber'] == 'H' ? $factura['ProveedorFactura']['ingreso_bruto'] * (-1) : $factura['ProveedorFactura']['ingreso_bruto']), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');

			if(empty($tipoOtroImp[0]['TipoAsientoRenglon']['co_plan_cuenta_id'])) $otrosImpuesto = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['otro_impuesto'], 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $otrosImpuesto = array('co_plan_cuenta_id' => $tipoOtroImp[0]['TipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoOtroImp[0]['TipoAsientoRenglon']['debe_haber'] == 'H' ? $factura['ProveedorFactura']['otro_impuesto'] * (-1) : $factura['ProveedorFactura']['otro_impuesto']), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');

			if(empty($tipoTotal[0]['TipoAsientoRenglon']['co_plan_cuenta_id'])) $totalComprobante = array('co_plan_cuenta_id' => 0, 'importe' => $factura['ProveedorFactura']['total_comprobante'] * (-1), 'referencia' => '', 'error' => 1, 'error_descripcion' => 'NO EXISTE TIPO ASIENTO');
			else $totalComprobante = array('co_plan_cuenta_id' => $tipoTotal[0]['TipoAsientoRenglon']['co_plan_cuenta_id'], 'importe' => ($tipoTotal[0]['TipoAsientoRenglon']['debe_haber'] == 'D' ? $factura['ProveedorFactura']['total_comprobante'] : $factura['ProveedorFactura']['total_comprobante'] * (-1)), 'referencia' => '', 'error' => 0, 'error_descripcion' => '');
			
		endif;
		
		$asiento = array();
		
		if($netoGravado['importe'] != 0)      array_push($asiento, $netoGravado);
		if($iva['importe'] != 0) array_push($asiento, $iva);
		if($noGravado['importe'] != 0) array_push($asiento, $noGravado);
		if($percepcion['importe'] != 0) array_push($asiento, $percepcion);
		if($retencion['importe'] != 0) array_push($asiento, $retencion);
		if($impuestoInterno['importe'] != 0) array_push($asiento, $impuestoInterno);
		if($ingresoBruto['importe'] != 0) array_push($asiento, $ingresoBruto);
		if($otrosImpuesto['importe'] != 0) array_push($asiento, $otrosImpuesto);
		if($totalComprobante['importe'] != 0) array_push($asiento, $totalComprobante);

		
		
		return $asiento;
		
	}
}	

?>