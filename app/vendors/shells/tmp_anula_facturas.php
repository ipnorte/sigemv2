<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 * 
 * /usr/bin/php /home/adrian/trabajo/www/sigemv2/cake/console/cake.php tmp_anula_facturas 1 -app /home/adrian/trabajo/www/sigemv2/app/
 * /usr/bin/php /home/cordobas/public_html/ryvsa/cake/console/cake.php tmp_anula_facturas 1 -app /home/cordobas/public_html/ryvsa/app/
 */

Configure::write('debug', 1);

class TmpAnulaFacturasShell extends Shell {
    
    var $Afip;
    var $eAfip = array('codigo' => 0, 'mensaje' => 'Sin errores');  
    var $datoAfip;
    
    function main() {

        $this->getDatoAfip();
        
        $server_status = $this->conexionAfip();

        if($server_status->AuthServer !== 'OK'){
            echo "ERROR EN LA CONEXION CON AFIP...";
            return;
        }        
        
        App::import('Model','facturacion.Factura');
        $oFactura = new Factura();        
        
        $SQL = "
            select 
                DISTINCT 
                t.tipo_comprobante,
                t.punto_venta, 
                t.numero_comprobante,
                t.fecha_comprobante,
                t.fecha_desde,
                t.fecha_hasta,
                t.fecha_vto_pago,
                t.importe_neto,
                t.importe_iva,
                t.importe_total,
                t.codigo_concepto,
                t.tipo_documento,
                t.numero_documento,
                t.persona_id,
                t.orden_descuento_cobro_id
            FROM orden_descuento_cobros AS OrdenDescuentoCobro
            LEFT JOIN orden_descuento_cobro_cuotas AS OrdenDescuentoCobroCuota ON (OrdenDescuentoCobroCuota.orden_descuento_cobro_id = OrdenDescuentoCobro.id)
            LEFT JOIN proveedores AS Proveedor ON (OrdenDescuentoCobroCuota.proveedor_id = Proveedor.id)
            LEFT JOIN global_datos AS GlobalDato ON (GlobalDato.id = OrdenDescuentoCobro.tipo_cobro)
            LEFT JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON (OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id)
            LEFT JOIN orden_descuentos AS OrdenDescuento ON (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
            LEFT JOIN socios AS Socio ON (Socio.id = OrdenDescuentoCuota.socio_id)
            LEFT JOIN personas AS Persona ON (Persona.id = Socio.persona_id)
            LEFT JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
            LEFT JOIN global_datos AS GlbDato ON (CONCAT(GlbDato.concepto_3,GlbDato.id) = CONCAT(OrdenDescuentoCuota.tipo_orden_dto,OrdenDescuentoCuota.tipo_producto))
            inner join (select t1.*, t2.orden_descuento_cobro_id as cobro_id from (SELECT f.* from facturas f 
            inner join orden_descuento_cobros odc on odc.id = f.orden_descuento_cobro_id 
            where f.tipo_comprobante = 6 and f.fecha_comprobante >= '2024-01-01' and odc.anulado = 1) t1
            inner join (SELECT f.* from facturas f inner join orden_descuento_cobros odc on odc.id = f.orden_descuento_cobro_id 
            where f.tipo_comprobante = 6 and f.fecha_comprobante >= '2024-01-01' and odc.anulado = 0) t2
            on t2.persona_id = t1.persona_id and t2.importe_total = t1.importe_total) t on t.cobro_id = OrdenDescuentoCobro.id;            
            ";
        $resultados = $oFactura->query($SQL);
        
        $grupo_actual = null;

        foreach ($resultados as $indice => $registro) {
            // Aquí, el grupo es la clave 't.*'
            $grupo_clave = $registro['t'];
            
            $tipoCbte = $this->getTipoCbte(3);
            $aVoucherInfo = array();
            
            try {
                
                $last_voucher = $this->Afip->ElectronicBilling->GetLastVoucher($tipoCbte['sucursal'], $tipoCbte['documento']); //Devuelve el número del último comprobante creado para el punto de venta 1 y el tipo de comprobante 6 (Factura B)
                
                if($last_voucher > 0){
                    $aVoucherInfo = $this->Afip->ElectronicBilling->GetVoucherInfo($last_voucher, $tipoCbte['sucursal'], $tipoCbte['documento']);
                }            

                $voucher = array(
                    'CantReg' 	=> 1, // Cantidad de comprobantes a registrar
                    'PtoVta' 	=> $tipoCbte['sucursal'], // Punto de venta
                    'CbteTipo' 	=> $tipoCbte['documento'], // Tipo de comprobante (ver tipos disponibles) 
                    'Concepto' 	=> $registro['t']['codigo_concepto'], // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
                    'DocTipo' 	=> $registro['t']['tipo_documento'], // Tipo de documento del comprador (ver tipos disponibles)
                    'DocNro' 	=> (float) $registro['t']['numero_documento'], // Numero de documento del comprador
                    'CbteDesde' 	=> $last_voucher + 1, // Numero de comprobante o numero del primer comprobante en caso de ser mas de uno
                    'CbteHasta' 	=> $last_voucher + 1, // Numero de comprobante o numero del ultimo comprobante en caso de ser mas de uno
                    'CbteFch' 	=> 20240806, // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
                    'ImpTotal' 	=> (float) $registro['t']['importe_total'], // Importe total del comprobante
                    'ImpTotConc' => (float) $registro['t']['importe_total_concepto'], // Importe neto no gravado
                    'ImpNeto' 	=> (float) $registro['t']['importe_neto'], // Importe neto gravado
                    'ImpOpEx' 	=> 0, // Importe exento de IVA
                    'ImpIVA' 	=> (float) $registro['t']['importe_iva'], //Importe total de IVA
                    'ImpTrib' 	=> 0, //Importe total de tributos
                    'FchServDesde' 	=> 20240806, // (Opcional) Fecha de inicio del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
                    'FchServHasta' 	=> 20240806, // (Opcional) Fecha de fin del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
                    'FchVtoPago' 	=> 20240806, // (Opcional) Fecha de vencimiento del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
                    'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
                    'MonCotiz' 	=> 1, // CotizaciÃ³n de la moneda usada (1 para pesos argentinos)  
                    'Iva' => array(
                        'Id' => 5,
                        'BaseImp' => (float)$registro['t']['importe_neto'],
                        'Importe' => (float) $registro['t']['importe_iva']
                    ),
                    'CbtesAsoc' => array(
                        'CbteAsoc' => array(
                        'Tipo' => $registro['t']['tipo_comprobante'], // Tipo de comprobante (ver tipos disponibles) 
                        'PtoVta' => $registro['t']['punto_venta'], // Punto de venta
                        'Nro' => $registro['t']['numero_comprobante'], // Numero de comprobante  
                        'CbteFch' => intval(date('Ymd', strtotime($registro['t']['fecha_comprobante'])))
                    ))
                );


                $voucher['persona_id'] = $registro['t']['persona_id'];
                $voucher['orden_descuento_cobro_id'] = $registro['t']['orden_descuento_cobro_id'];     
                $voucher['e_codigo'] = 0;
                $voucher['e_mensaje'] = 'SIN ERRORES';  
                
                // genero factura
                $info_voucher = $this->Afip->ElectronicBilling->CreateVoucher($voucher, TRUE);
                
                $voucher['DocNro'] = strval($voucher['DocNro']);
                
            } catch (Exception $e) {
                
                $voucher['e_codigo']  = $e->getCode();
                $voucher['e_mensaje'] = $e->getMessage();
                
                var_dump($voucher);
                debug($e);
                exit;

            }                 
            if($voucher['e_codigo'] > 0){
               $voucher['CbteDesde'] = 0;
            }

            $CbcFactura['Factura'] = array(
                'fecha_comprobante' => $this->Afip->ElectronicBilling->FormatDate($voucher['CbteFch']),
                'persona_id' => $voucher['persona_id'],
                'orden_descuento_cobro_id' => $voucher['orden_descuento_cobro_id'],
                'factura_id' => $voucher['factura_id'],
                'punto_venta' => $voucher['PtoVta'],
                'numero_comprobante' => $voucher['CbteDesde'],
                'tipo_comprobante' => $voucher['CbteTipo'],
                'codigo_concepto' => $voucher['Concepto'],
                'tipo_documento' => $voucher['DocTipo'],
                'numero_documento' => $voucher['DocNro'],
                'fecha_desde' => $this->Afip->ElectronicBilling->FormatDate($voucher['FchServDesde']),
                'fecha_hasta' => $this->Afip->ElectronicBilling->FormatDate($voucher['FchServHasta']),
                'fecha_vto_pago' => $this->Afip->ElectronicBilling->FormatDate($voucher['FchVtoPago']),
                'codigo_moneda' => $voucher['MonId'],
                'resultado' => $info_voucher->FeCabResp->Resultado,
                'codigo_autorizacion' => $info_voucher->FeDetResp->FECAEDetResponse->CAE,
                'tipo_emision' => 'CAE',
                'cae_fecha_vto' => $this->Afip->ElectronicBilling->FormatDate($info_voucher->FeDetResp->FECAEDetResponse->CAEFchVto),
                'cae_fecha_proceso' => $this->Afip->ElectronicBilling->FormatDateL($info_voucher->FeCabResp->FchProceso),
                'e_codigo' => $voucher['e_codigo'],
                'e_mensaje' => $voucher['e_mensaje'],
                'importe_total' => $voucher['ImpTotal'],
                'importe_total_concepto' => $voucher['ImpTotConc'],
                'importe_neto' => $voucher['ImpNeto'],
                'importe_exento' => $voucher['ImpOpEx'],
                'importe_iva' => $voucher['ImpIVA'],
                'importe_tributo' => $voucher['ImpTrib'],
            );

            if(!$oFactura->save($CbcFactura)) return FALSE;

            if($voucher['e_codigo'] > 0) return FALSE;                
        }

    }
    
    function conexionAfip(){
        $include = ROOT . DS . APP_DIR . DS . 'vendors' . DS . 'afip' . DS . 'src' . DS . 'Afip.php';

        include $include;
        if($this->datoAfip['AfipDato']['modo'] == 0){     // HOMOLOGACION
            $this->Afip = new Afip(array('CUIT' => $this->datoAfip['AfipDato']['cuit'], 'cert' => $this->datoAfip['AfipDato']['pem'], 'key' => $this->datoAfip['AfipDato']['clave']));
        }
        else{     // PRODUCCION
            $this->Afip = new Afip(array('CUIT' => $this->datoAfip['AfipDato']['cuit'], 'production' => TRUE, 'cert' => $this->datoAfip['AfipDato']['certificado'], 'key' => $this->datoAfip['AfipDato']['clave']));
        }
        $server_status = $this->Afip->ElectronicBilling->GetServerStatus();
        return $server_status;
    }    
    
    
    function getDatoAfip(){
        App::import('Model','config.AfipDato');
        $oAfipDato = new AfipDato();
        $this->datoAfip = $oAfipDato->getAfipDato();
        return NULL;
        

        }    

    function getTipoCbte($tipo = 1){
        App::import('Model','config.GlobalDato');
        $oGlobalDato = new GlobalDato();
        
        App::import('Model','config.TipoDocumento');
        $oTipoCbte = new TipoDocumento();
        
        $values = $oGlobalDato->find('all',array('conditions' => array('GlobalDato.id' => 'PERSTIVA0005'),'order' => array('GlobalDato.id')));

        switch ($tipo){
            case 1:
                $aCbte = $oTipoCbte->getComprobante($values[0]['GlobalDato']['entero_1']);
                 break;
            case 2:
                $aCbte = $oTipoCbte->getComprobante($values[0]['GlobalDato']['entero_2']);
                break;
            case 3:
                $aCbte = $oTipoCbte->getComprobante(intval($values[0]['GlobalDato']['decimal_1']));
                break;
        }
        
        return $aCbte;
    }    
    
    
}