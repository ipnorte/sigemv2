<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class Factura extends FacturacionAppModel{
	
	var $name = 'Factura';
	
//	var $hasMany = array('FacturaProducto' => array('dependent' => true));


	function getFactura($factura_id){
            
            App::import('model','Pfyj.Persona');
            $oPersona = new Persona();

            App::import('Model','config.TipoDocumento');
            $oComprobante = new TipoDocumento();
            
            $aFactura = $this->read(null, $factura_id);

            $aCbte = $oComprobante->find('first', array('conditions' => array('TipoDocumento.documento' => $aFactura['Factura']['tipo_comprobante'])));
            $persona = $oPersona->read(null,$aFactura['Factura']['persona_id']);

            $aFactura['Factura']['numero_dni'] = $persona['Persona']['documento'];
            $aFactura['Factura']['nom_apel'] = ltrim(rtrim($persona['Persona']['nombre'])) . ' ' . ltrim(rtrim($persona['Persona']['apellido']));
            
            $aFactura['Factura']['domicilio'] = ltrim(rtrim($persona['Persona']['calle'])) . " " .($persona['Persona']['numero_calle'] != 0 ? $persona['Persona']['numero_calle'] : '');
            if(!empty($persona['Persona']['piso'])) $persona['Persona']['domicilio'] .= ' Piso ' . ltrim(rtrim($persona['Persona']['piso'])); 
            if(!empty($persona['Persona']['dpto'])) $persona['Persona']['domicilio'] .= ' Dpto ' . ltrim(rtrim($persona['Persona']['dpto']));        

            $aFactura['Factura']['nombre_comprobante'] = $aCbte['TipoDocumento']['descripcion'];
            $aFactura['Factura']['descripcion_comprobante'] = $aCbte['TipoDocumento']['tipo_documento'];
            $aFactura['Factura']['letra_comprobante'] = $aCbte['TipoDocumento']['letra'];
            
            $aFactura['Factura']['comprobante'] = ltrim(rtrim($aCbte['TipoDocumento']['tipo_documento'])) . ' ' .
                    ltrim(rtrim($aCbte['TipoDocumento']['letra'])) . '-' . str_pad($aFactura['Factura']['punto_venta'],5,0, STR_PAD_LEFT) . '-' .
                    str_pad($aFactura['Factura']['numero_comprobante'],12,0, STR_PAD_LEFT);


            return $aFactura;

        }

        
        function getFacturaEntreFecha($fecha_desde, $fecha_hasta){
            
            App::import('model','Pfyj.Persona');
            $oPersona = new Persona();

            App::import('Model','config.TipoDocumento');
            $oComprobante = new TipoDocumento();
            
            $aFacturas = $this->find('all', array('conditions' => array('Factura.fecha_comprobante >=' => $fecha_desde, 'Factura.fecha_comprobante <=' => $fecha_hasta), 'order' => array('Factura.fecha_comprobante', 'Factura.id')));
            $aReturn = array();
            
            foreach ($aFacturas as $item){
                $aCbte = $oComprobante->find('first', array('conditions' => array('TipoDocumento.documento' => $item['Factura']['tipo_comprobante'])));
                $persona = $oPersona->read(null,$item['Factura']['persona_id']);

                $item['Factura']['nom_apel'] = ltrim(rtrim($persona['Persona']['nombre'])) . ' ' . ltrim(rtrim($persona['Persona']['apellido']));
                $item['Factura']['descripcion_comprobante'] = $aCbte['TipoDocumento']['tipo_documento'];
                $item['Factura']['letra_comprobante'] = $aCbte['TipoDocumento']['letra'];
                $item['Factura']['comprobante'] = ltrim(rtrim($aCbte['TipoDocumento']['tipo_documento'])) . ' ' .
                        ltrim(rtrim($aCbte['TipoDocumento']['letra'])) . '-' . str_pad($item['Factura']['punto_venta'],5,0, STR_PAD_LEFT) . '-' .
                        str_pad($item['Factura']['numero_comprobante'],12,0, STR_PAD_LEFT);
                        

                array_push($aReturn, $item);
            }
            
            return $aReturn;

        }
        
        
        function factura_afip($id){

            // Datos de la Facturas
            $aFactura = $this->getFactura($id);
            $datoAfip = $this->getAfipDatoFct();
            
            if($aFactura['Factura']['factura_id'] > 0){
                $aDetalle = $this->getDetalleFactura($aFactura['Factura']['factura_id'], 3);

            }
            else{
                $aDetalle = $this->getDetalleFactura($aFactura['Factura']['id'], 1);
                
            }
            $aFactura['AfipDato'] = &$datoAfip['AfipDato'];
            $aFactura['Documento'] = &$datoAfip['documento'];
            $aFactura['Detalle'] = &$aDetalle;
            
/*
            $aClntFct['Cliente'] = &$aCliente['Cliente'];
            $aClntFct['AfipDato'] = &$datoAfip['AfipDato'];
            $aClntFct['Documento'] = &$datoAfip['documento'];
*/
            $aFactura['Factura']['Afip_CodBarra'] = $aFactura['AfipDato']['cuit']; 
            $aFactura['Factura']['Afip_CodBarra'] .= str_pad($aFactura['Factura']['tipo_comprobante'],3,0, STR_PAD_LEFT); 
            $aFactura['Factura']['Afip_CodBarra'] .= str_pad($aFactura['Factura']['punto_venta'],5,0, STR_PAD_LEFT); 
            $aFactura['Factura']['Afip_CodBarra'] .= $aFactura['Factura']['codigo_autorizacion']; 
            $aFactura['Factura']['Afip_CodBarra'] .= date('Ymd', strtotime($aFactura['Factura']['cae_fecha_vto'])); 
            
//            $num = '20162290267015000026901734619471120190113';
            $digito = $this->get_digito_verificador_afip($aFactura['Factura']['Afip_CodBarra']);
            $aFactura['Factura']['Afip_CodBarra'] .= $digito;

            return $aFactura;

        }

        
        
        function getAfipDatoFct(){
            App::import('Model','config.AfipDato');
            $oAfipDato = new AfipDato();
            
            return $oAfipDato->getAfipDatoFct();
            
        }
        
       
        function getDetalleFactura($vid, $tipo = 1){

/*
            App::import('Model','mutual.OrdenDescuentoCobroCuota');
            $oCobroCuota = new OrdenDescuentoCobroCuota();

            if($tipo == 1){
                $aDetalleFactura = $oCobroCuota->find('all', array('conditions' => array('OrdenDescuentoCobroCuota.factura_id' => $vid)));
                
            }
            else{
                $aDetalleFactura = $oCobroCuota->find('all', array('conditions' => array('OrdenDescuentoCobroCuota.reverso_factura_id' => $vid)));

            }
*/            
// debug($vid);
// debug($aDetalleFactura);
// exit;
            $sql = "SELECT	OrdenDescuentoCobroCuota.*,
                    CONCAT('SERVICIO C.', OrdenDescuento.numero, '- CTA.', OrdenDescuentoCuota.nro_cuota, '/',  OrdenDescuento.cuotas, ' *', GlbDato.concepto_1) AS descripcion,
                    ROUND(OrdenDescuentoCobroCuota.importe * (OrdenDescuentoCuota.iva / OrdenDescuentoCuota.importe),2) AS iva,
                    ROUND(OrdenDescuentoCobroCuota.importe * (OrdenDescuentoCuota.interes / OrdenDescuentoCuota.importe),2) AS neto,
                    ROUND(OrdenDescuentoCobroCuota.importe * (OrdenDescuentoCuota.iva / OrdenDescuentoCuota.importe),2) + ROUND(OrdenDescuentoCobroCuota.importe * (OrdenDescuentoCuota.interes / OrdenDescuentoCuota.importe),2) AS total

                    FROM orden_descuento_cobro_cuotas AS OrdenDescuentoCobroCuota
                    INNER JOIN orden_descuento_cobros AS OrdenDescuentoCobro ON (OrdenDescuentoCobroCuota.orden_descuento_cobro_id = OrdenDescuentoCobro.id)
                    LEFT JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON (OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id)
                    LEFT JOIN orden_descuentos AS OrdenDescuento ON (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
                    LEFT JOIN global_datos AS GlbDato ON (CONCAT(GlbDato.concepto_3,GlbDato.id) = CONCAT(OrdenDescuentoCuota.tipo_orden_dto,OrdenDescuentoCuota.tipo_producto)) ";
                    
            if($tipo == 1){
                $sql .= "WHERE OrdenDescuentoCobroCuota.factura_id = '" . $vid . "'";
            }
            else{
                $sql .= "WHERE OrdenDescuentoCobroCuota.reverso_factura_id = '" . $vid . "'";
                
            }

            $aDetalleFactura = $this->query($sql);
            
            return $aDetalleFactura;
            
        }
        
	function get_digito_verificador_afip($num){
		$num = trim($num);
		$ln = strlen($num);
		$b = 0;
		$sumI = 0;
		$sumP = 0;
                $sum = 0;
		for($a=0;$a<$ln;$a++){
                    $b += 1;
                    if(($b % 2) == 0) $sumP += substr($num,$a,1);
                    else $sumI += substr($num,$a,1);
		}
		$sum = $sumP + ($sumI * 3);
                $sum = $sum % 10;
		if($sum != 0) $sum = 10 - $sum;
		return $sum;
	}

/*
            // Datos de la Facturas
            App::import('Model','clientes.ClienteFactura');
            $oClnFactura = new ClienteFactura();
            $aClntFct = $oClnFactura->getFactura($id, TRUE);

            App::import('Model','clientes.Cliente');
            $oCliente = new Cliente();
            $aCliente = $oCliente->getCliente($aClntFct['ClienteFactura']['cliente_id']);
            
            $aCliente['Cliente']['domicilio'] = ltrim(rtrim($cliente['Cliente']['calle'])) . " " .($aCliente['Cliente']['numero_calle'] != 0 ? $aCliente['Cliente']['numero_calle'] : '');
            if(!empty($aCliente['Cliente']['piso'])) $aCliente['Cliente']['domicilio'] .= ' Piso ' . ltrim(rtrim($aCliente['Cliente']['piso'])); 
            if(!empty($aCliente['Cliente']['dpto'])) $aCliente['Cliente']['domicilio'] .= ' Dpto ' . ltrim(rtrim($aCliente['Cliente']['dpto']));        

            // Datos de la configuracion de la AFIP
            APP::import('Model', 'config.AfipDato');
            $oAfipDato = new AfipDato();
            $datoAfip = $oAfipDato->getAfipDatoFct();
// debug($datoAfip);

            // Datos de la configuracion de la AFIP
            APP::import('Model', 'contabilidad.PlanCuenta');
            $oPlanCuenta = new PlanCuenta();

            if($aClntFct['ClienteFactura']['tipo'] === 'FA'){
                $datoAfip['documento']['descripcion'] = $datoAfip['documento']['factura']['descripcion'];
                $datoAfip['documento']['letra'] = $datoAfip['documento']['factura']['letra'];
            }
            
            if($aClntFct['ClienteFactura']['tipo'] === 'NC'){
                $datoAfip['documento']['descripcion'] = $datoAfip['documento']['credito']['descripcion'];
                $datoAfip['documento']['letra'] = $datoAfip['documento']['credito']['letra'];
            }
            
            if($aClntFct['ClienteFactura']['tipo'] === 'ND'){
                $datoAfip['documento']['descripcion'] = $datoAfip['documento']['debito']['descripcion'];
                $datoAfip['documento']['letra'] = $datoAfip['documento']['debito']['letra'];
            }
        
// debug($datoAfip);
            unset($datoAfip['documento']['factura']);
            unset($datoAfip['documento']['credito']);
            unset($datoAfip['documento']['debito']);

            foreach($aClntFct['ClienteFacturaDetalle'] as $i => $dato){
                if($dato['producto'] == ''){
                    $planCuenta = $oPlanCuenta->getCuenta($dato['co_plan_cuenta_id']);
                    $aClntFct['ClienteFacturaDetalle'][$i]['producto'] = $planCuenta['PlanCuenta']['descripcion'];
                }
            }
            
            $aClntFct['Cliente'] = &$aCliente['Cliente'];
            $aClntFct['AfipDato'] = &$datoAfip['AfipDato'];
            $aClntFct['Documento'] = &$datoAfip['documento'];

            $aClntFct['ClienteFactura']['Afip_CodBarra'] = $aClntFct['AfipDato']['cuit']; 
            $aClntFct['ClienteFactura']['Afip_CodBarra'] .= str_pad($aClntFct['ClienteFactura']['Afip_CbteTipo'],3,0, STR_PAD_LEFT); 
            $aClntFct['ClienteFactura']['Afip_CodBarra'] .= str_pad($aClntFct['ClienteFactura']['Afip_PtoVta'],5,0, STR_PAD_LEFT); 
            $aClntFct['ClienteFactura']['Afip_CodBarra'] .= $aClntFct['ClienteFactura']['Afip_CodAutorizacion']; 
            $aClntFct['ClienteFactura']['Afip_CodBarra'] .= date('Ymd', strtotime($aClntFct['ClienteFactura']['Afip_FchVto'])); 
            
//            $num = '20162290267015000026901734619471120190113';
            $digito = $this->get_digito_verificador_afip($aClntFct['ClienteFactura']['Afip_CodBarra']);
            $aClntFct['ClienteFactura']['Afip_CodBarra'] .= $digito;
// debug($aClntFct);
// exit;
*/        
}

?>