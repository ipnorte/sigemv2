<?php
class ProveedorLiquidacion extends ProveedoresAppModel{
	
    var $name = 'ProveedorLiquidacion';
    var $belongsTo = array('Proveedor');

    function getLiquidacion($id){

        $liquidacion = $this->read(null,$id);
        $liquidacion = $this->__armaDatos($liquidacion);
        return $liquidacion;
    }

    function __armaDatos($liquidacion){
        $liquidacion['ProveedorLiquidacion']['razon_social'] = $liquidacion['Proveedor']['razon_social'];
        $liquidacion['ProveedorLiquidacion']['tipo_cuota_desc'] = parent::GlobalDato('concepto_1',$liquidacion['ProveedorLiquidacion']['tipo_cuota']);
        return $liquidacion;
    }
	
    function getProveedoresLiquidados($liquidacion_id){
        $resumen = array();

        $liquidaciones = $this->find('all',array('conditions' => array('ProveedorLiquidacion.liquidacion_id' => $liquidacion_id)));

        foreach($liquidaciones as $idx => $liquidacion){
            $liquidacion = $this->__armaDatos($liquidacion);
            array_push($resumen,$liquidacion['ProveedorLiquidacion']);

        }

        return $resumen;

    }
	
	
    function grabarLiquidacionByCancelacion($id){
        return true;

        $this->OrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');
        $aOrdenDescuentoCobro = $this->OrdenDescuentoCobro->getCobro($id, true);

        if(!isset($aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['proveedor_id']) || $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['proveedor_id'] == MUTUALPROVEEDORID ||
           empty($aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['proveedor_id'])) return true;

        $this->OrdenDescuentoCobroCuota = $this->importarModelo('OrdenDescuentoCobroCuota', 'mutual');

        $aProveedorLiquidacion = array('ProveedorLiquidacion' => array(
            'id' => 0,
            'origen_recibo_id' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['recibo_id'],
            'liquidacion_id' => 0,
            'proveedor_origen_fondo_id' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['proveedor_origen_fondo_id'],
            'proveedor_id' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['proveedor_id'],
            'cliente_id' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['cliente_id'],
            'tipo_factura' => 'FA',
            'fecha' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['fecha'],
            'periodo_cobro' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['periodo_cobro'],
            'importe_proveedor' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['importe'],
            'comision_cobranza' => ($aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['cliente_id'] > 0 ? $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['comision_cobranza'] : 0),
            'orden_pago_id' => 0,
            'recibo_id' => 0,
            'proveedor_factura_id' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['proveedor_factura_id'],
            'cliente_factura_id' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['cliente_factura_id'],
            'cancelacion_orden_id' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['cancelacion_orden_id'],
            'orden_descuento_cobro_id' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['id'],
            'concepto' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['concepto']
        ));

        // Grabo la cabecera de la Factura
        $this->id = 0;
        $this->begin();
        if(!$this->save($aProveedorLiquidacion)):		
            $this->rollback();
            return false;
        endif;

        $nId = $this->getLastInsertID();

        $this->OrdenDescuentoCobroCuota->updateAll(array('OrdenDescuentoCobroCuota.proveedor_liquidacion_id' => $nId), array('OrdenDescuentoCobroCuota.orden_descuento_cobro_id' => $aOrdenDescuentoCobro['ProveedorLiquidacion'][0]['orden_descuento_cobro_id']));
        $this->commit();
        return $nId;
    }
	


    function grabarLiquidacionByCaja($id){
        return true;

        $this->OrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');
        $aOrdenDescuentoCobro = $this->OrdenDescuentoCobro->getCobro($id, true);

        $this->OrdenDescuentoCobroCuota = $this->importarModelo('OrdenDescuentoCobroCuota', 'mutual');

//	$this->begin();
        $grabaOk = true;
        foreach($aOrdenDescuentoCobro['ProveedorLiquidacion'] as $aCobro):
            if($aCobro['proveedor_id'] != MUTUALPROVEEDORID):
                $aProveedorLiquidacion = array('ProveedorLiquidacion' => array(
                    'id' => 0,
                    'origen_recibo_id' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['recibo_id'],
                    'liquidacion_id' => 0,
                    'proveedor_origen_fondo_id' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['proveedor_origen_fondo_id'],
                    'proveedor_id' => $aCobro['proveedor_id'],
                    'cliente_id' => $aCobro['cliente_id'],
                    'tipo_factura' => 'FA',
                    'fecha' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['fecha'],
                    'periodo_cobro' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['periodo_cobro'],
                    'importe_proveedor' => $aCobro['importe'],
                    'comision_cobranza' => $aCobro['comision_cobranza'],
                    'orden_pago_id' => 0,
                    'recibo_id' => 0,
                    'proveedor_factura_id' => $aCobro['proveedor_factura_id'],
                    'cliente_factura_id' => $aCobro['cliente_factura_id'],
                    'cancelacion_orden_id' => $aCobro['cancelacion_orden_id'],
                    'orden_descuento_cobro_id' => $id,
                    'orden_descuento_cobro_id' => $aOrdenDescuentoCobro['OrdenDescuentoCobro']['id'],
                    'orden_caja_cobro_id' => $aCobro['orden_caja_cobro_id'],
                    'concepto' => $aCobro['concepto']
                ));

                $this->id = 0;
                // Grabo la cabecera de la Factura
                if(!$this->save($aProveedorLiquidacion)):
                    return false;
                endif;

                $nId = $this->getLastInsertID();

                if(!$this->OrdenDescuentoCobroCuota->updateAll(array('OrdenDescuentoCobroCuota.proveedor_liquidacion_id' => $nId), array('OrdenDescuentoCobroCuota.id' => $aCobro['cobro_cuota_id']))):
                    return false;
                endif;
            endif;
        endforeach;		

//		if(!$grabaOk):
//			$this->rollback();
//			return false;
//		endif;
//		
//		$this->commit();
        return true;

    }
		
		
    function grabarLiquidacion($factura, $liquidacion){
        return true;
		
    	$oProveedor = $this->importarModelo('Proveedor', 'proveedores');
    	$aProveedor = $oProveedor->getProveedor($factura['proveedor_id']);
		
		    	
    	$aProveedorLiquidacion = array('ProveedorLiquidacion' => array(
            'id' => 0,
            'origen_recibo_id' => 0,
            'liquidacion_id' => $liquidacion['Liquidacion']['id'],
            'proveedor_origen_fondo_id' => MUTUALPROVEEDORID,
            'proveedor_id' => $factura['proveedor_id'],
            'cliente_id' => $aProveedor['Proveedor']['cliente_id'],
            'tipo_factura' => $factura['tipo_documento_cliente'],
            'fecha' => $liquidacion['Liquidacion']['fecha_imputacion'],
            'periodo_cobro' => $liquidacion['Liquidacion']['periodo'],
            'importe_proveedor' => $factura['importe_proveedor'],
            'comision_cobranza' => $factura['importe_cliente'],
            'orden_pago_id' => 0,
            'recibo_id' => 0,
            'proveedor_factura_id' => $factura['proveedor_factura_id'],
            'cliente_factura_id' => $factura['cliente_factura_id'],
            'cancelacion_orden_id' => 0,
            'concepto' => trim($factura['descripcion_proveedor']) . ' - ' . $liquidacion['Liquidacion']['organismo']
        ));
	
        $this->begin();
        // Grabo la cabecera de la Factura
        if(!$this->save($aProveedorLiquidacion)):
            $this->rollback();
            return false;
        endif;

//		$nId = $this->getLastInsertID();

//		if(!$this->OrdenDescuentoCobroCuota->updateAll(array('OrdenDescuentoCobroCuota.proveedor_liquidacion_id' => $nId), array('OrdenDescuentoCobroCuota.id' => $aCobro['cobro_cuota_id']))):
//			$grabaOk = false;
//			break;		
//		endif;

        $this->commit();
        return true;


    }
	

    function grabarLiquidacionCredito($datos){

        $oProveedor = $this->importarModelo('Proveedor', 'proveedores');
        $aProveedor = $oProveedor->getProveedor($datos['OrdenDescuentoCobro']['proveedor_origen_fondo_id']);


        $aProveedorLiquidacion = array('ProveedorLiquidacion' => array(
            'id' => 0,
            'origen_recibo_id' => 0,
            'liquidacion_id' => 0,
            'proveedor_origen_fondo_id' => $datos['OrdenDescuentoCobro']['proveedor_origen_fondo_id'],
            'proveedor_id' => $datos['OrdenDescuentoCobro']['proveedor_origen_fondo_id'],
            'cliente_id' => $aProveedor['Proveedor']['cliente_id'],
            'tipo_factura' => 'NC',
            'fecha' => $datos['OrdenDescuentoCobro']['fecha_comprobante'],
            'periodo_cobro' => $datos['OrdenDescuentoCobro']['periodo_cobro'],
            'importe_proveedor' => $datos['OrdenDescuentoCobro']['importe_cobro'],
            'comision_cobranza' => 0,
            'orden_pago_id' => 0,
            'recibo_id' => 0,
            'proveedor_factura_id' => $datos['OrdenDescuentoCobro']['proveedor_factura_id'],
            'cliente_factura_id' => 0,
            'cancelacion_orden_id' => 0,
            'concepto' => $datos['OrdenDescuentoCobro']['observacion']
        ));

        // Grabo la cabecera de la Factura
        if(!$this->save($aProveedorLiquidacion)):		
            $this->rollback();
            return false;
        endif;

        $nId = $this->getLastInsertID();

        return $nId;
    }
	
	

    function grabarLiquidacionCreditoCancela($datos){

        $oProveedor = $this->importarModelo('Proveedor', 'proveedores');
        $aProveedor = $oProveedor->getProveedor($datos['CancelacionOrden']['proveedor_origen_id']);


        $aProveedorLiquidacion = array('ProveedorLiquidacion' => array(
            'id' => 0,
            'origen_recibo_id' => 0,
            'liquidacion_id' => 0,
            'proveedor_origen_fondo_id' => $datos['CancelacionOrden']['proveedor_origen_id'],
            'proveedor_id' => $datos['CancelacionOrden']['proveedor_origen_id'],
            'cliente_id' => $aProveedor['Proveedor']['cliente_id'],
            'tipo_factura' => 'NC',
            'fecha' => $datos['CancelacionOrden']['fecha_comprobante'],
            'periodo_cobro' => $datos['CancelacionOrden']['periodo_cobro'],
            'importe_proveedor' => $datos['CancelacionOrden']['importe_cobro'],
            'comision_cobranza' => 0,
            'orden_pago_id' => 0,
            'recibo_id' => 0,
            'proveedor_factura_id' => $datos['CancelacionOrden']['credito_proveedor_factura_id'],
            'cliente_factura_id' => 0,
            'cancelacion_orden_id' => 0,
            'concepto' => $datos['CancelacionOrden']['observacion']
        ));


        if(!$this->save($aProveedorLiquidacion)):		
            $this->rollback();
            return false;
        endif;

        $nId = $this->getLastInsertID();

        return $nId;
    }
	

	
    function getLiquidaciones($proveedorID){

        $this->recursive = 4;
        $this->bindModel(array('hasMany' => array('OrdenDescuentoCobroCuota')));
        $this->OrdenDescuentoCobroCuota->bindModel(array('belongsTo' => array('OrdenDescuentoCuota')));
        $this->OrdenDescuentoCobroCuota->OrdenDescuentoCuota->bindModel(array('belongsTo' => array('OrdenDescuento','Socio')));
        $this->OrdenDescuentoCobroCuota->OrdenDescuentoCuota->Socio->bindModel(array('belongsTo' => array('Persona')));
        $aProvLiquidaciones = $this->find('all', array('conditions' => array('ProveedorLiquidacion.proveedor_id' => $proveedorID), 'order' => 'ProveedorLiquidacion.fecha DESC'));

        return $this->armaDatos($aProvLiquidaciones);
    }


    function armaDatos($aProvLiquidaciones){
//		$this->OrdenDescuentoCobroCuota = $this->importarModelo('OrdenDescuentoCobroCuota', 'mutual');
//		$this->OrdenDescuentoCuota = $this->importarModelo('OrdenDescuentoCuota', 'mutual');
//		
//		foreach ($aProvLiquidaciones as $clave => $aProvLiq):
//			$aCobroCuota = $this->OrdenDescuentoCobroCuota->find('all', array('conditions' => array('OrdenDescuentoCobroCuota.proveedor_liquidacion_id' => $aProvLiq['ProveedorLiquidacion']['id'] )));
//			foreach($aCobroCuota as $idx => $cobroCuota){
//				$cuota = $this->OrdenDescuentoCuota->getCuota($cobroCuota['OrdenDescuentoCobroCuota']['orden_descuento_cuota_id']);
//				$aCobroCuota[$idx]['OrdenDescuentoCuota'] = $cuota['OrdenDescuentoCuota'];
//				$aProvLiquidaciones[$clave]['OrdenDescuentoCuota'] = $aCobroCuota;
//			}
//			
//		endforeach;

        foreach($aProvLiquidaciones as $clave => $aProvLiq):
            $this->Proveedor = $this->importarModelo('Proveedor', 'proveedores');

            $aProvLiquidaciones[$clave]['Persona'] = $aProvLiq['OrdenDescuentoCobroCuota'][0]['OrdenDescuentoCuota']['Socio']['Persona'];
            $aProvLiquidaciones[$clave]['Persona']['orden_descuento_id'] = $aProvLiq['OrdenDescuentoCobroCuota'][0]['OrdenDescuentoCuota']['OrdenDescuento']['id'];
            $aProvLiquidaciones[$clave]['Persona']['numero'] = $aProvLiq['OrdenDescuentoCobroCuota'][0]['OrdenDescuentoCuota']['OrdenDescuento']['numero'];
            $aProvLiquidaciones[$clave]['Persona']['nro_referencia_proveedor'] = $aProvLiq['OrdenDescuentoCobroCuota'][0]['OrdenDescuentoCuota']['OrdenDescuento']['nro_referencia_proveedor'];

            $aProvLiquidaciones[$clave]['ProveedorLiquidacion']['tipo_fac_str'] = 'FACTURA';
            if($aProvLiq['ProveedorLiquidacion']['tipo_factura'] == 'ND') $aProvLiquidaciones[$clave]['ProveedorLiquidacion']['tipo_fac_str'] = 'NOTA DEBITO';
            if($aProvLiq['ProveedorLiquidacion']['tipo_factura'] == 'NC') $aProvLiquidaciones[$clave]['ProveedorLiquidacion']['tipo_fac_str'] = 'NOTA CREDITO';

            $aProvLiquidaciones[$clave]['ProveedorLiquidacion']['tipo'] = 'COBRO CAJA';
            if($aProvLiq['ProveedorLiquidacion']['liquidacion_id'] > 0) $aProvLiquidaciones[$clave]['ProveedorLiquidacion']['tipo'] = 'REC.SUELDO';
            if($aProvLiq['ProveedorLiquidacion']['cancelacion_orden_id'] > 0) $aProvLiquidaciones[$clave]['ProveedorLiquidacion']['tipo'] = 'CANCELACION';

            $aProvLiquidaciones[$clave]['ProveedorLiquidacion']['responsable'] = 'MUTUAL'; // strtoupper(Configure::read('APLICACION.nombre_fantasia'));
            if($aProvLiq['ProveedorLiquidacion']['proveedor_origen_fondo_id'] == $aProvLiq['ProveedorLiquidacion']['proveedor_id']) $aProvLiquidaciones[$clave]['ProveedorLiquidacion']['responsable'] = 'RETUVO COMERCIO';
            if($aProvLiq['ProveedorLiquidacion']['proveedor_origen_fondo_id'] != $aProvLiq['ProveedorLiquidacion']['proveedor_id'] &&
            $aProvLiq['ProveedorLiquidacion']['proveedor_origen_fondo_id'] != MUTUALPROVEEDORID) $aProvLiquidaciones[$clave]['ProveedorLiquidacion']['responsable'] = $this->Proveedor->getRazonSocialResumida($aProvLiq['ProveedorLiquidacion']['proveedor_origen_fondo_id']);

        endforeach;		



        return $aProvLiquidaciones;
    }
}
?>