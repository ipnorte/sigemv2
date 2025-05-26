<?php

/**
 * Factura Electronica (AFIP)
 * Genera las facturas del WS de Afip
 * <br/>
 * <li>PARAMETRO_1 = fecha_desde</li>
 * <li>PARAMETRO_2 = fecha_hasta</li>
 * 
 * @author GUSTAVO LUJAN
 * @package shells
 * @subpackage background-execute
 *
 
// C:\xampp\php\php.exe C:\xampp\htdocs\sigemv2\cake\console\cake.php factura_electronica 2255 -app C:\xampp\htdocs\sigemv2\app\
   /usr/bin/php5 /home/adrian/Trabajo/www/sigemv2/cake/console/cake.php factura_electronica 1788 -app /home/adrian/Trabajo/www/sigemv2/app/
 *  
 */

class FacturaElectronicaShell extends Shell {
	
	var $fecha_desde;
	var $fecha_hasta;
    var $fecha_factura;
    var $empresaId;
    var $asincId;
    var $datoAfip;
    var $Afip;
    var $eAfip = array('codigo' => 0, 'mensaje' => 'Sin errores');
	
	/**
	 * Referencia a Modelos que usa
	 * @var array
	 */
	var $uses = array('Mutual.OrdenDescuentoCobro');
	
	/**
	 * Referencia a tareas que usa
	 * @var array
	 */
	var $tasks = array('Temporal');
	
	/**
	 * Main
	 * Metodo principal
	 * // @return unknown_type
	 */
	function main() {
		
        Configure::write('debug',1);
        
        $STOP = 0;

        if(empty($this->args[0])){
                $this->out("ERROR: PID NO ESPECIFICADO");
                return;
        }

        $pid = $this->args[0];

        $asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
        $asinc->id = $pid; 
        $this->asincId = $pid;

        $this->fecha_desde      = $asinc->getParametro('p1');
        $this->fecha_hasta      = $asinc->getParametro('p2');
        $this->fecha_factura    = $asinc->getParametro('p3');
        $this->empresaId        = $asinc->getParametro('p4');

        $asinc->actualizar(0,100,"ESPERE, INICIANDO PROCESO...");
        
        /*
         * Traigo los datos de configuracion del AFIP
         */
        $this->getDatoAfip();
        
        
        /*
         * Conecto con los servidores del Afip
         */
        $server_status = $this->conexionAfip();
        if($server_status->AuthServer == 'OK'){
        }
        else{
            $asinc->actualizar(100,100,"ERROR EN LA CONEXION CON AFIP...");
            return;
        }


        $STOP = 0;
        $total = 0;
        $asinc->actualizar(1,100,"ESPERE, CONSULTANDO COBROS DEL PERIODO...");

        //limpio la tabla temporal
        if(!$this->Temporal->limpiarTabla($asinc->id)){
                $asinc->fin("SE PRODUJO UN ERROR...");
                return;
        }

// Proceso los Cobros entre fecha
        $cobros = $this->getCobros(1);
        $total = count($cobros);
        $asinc->setTotal($total);
        $nContador = 1;	

        $temp = array();

        foreach($cobros as $cobro):

            $asinc->actualizar($nContador,$total,"$nContador / $total - COBROS >> " . $cobro['OrdenDescuentoCobro']['apellido'] . ', ' . $cobro['OrdenDescuentoCobro']['nombre']);

//            if($cobro['OrdenDescuentoCobro']['importe'] > 0){
            if($cobro['OrdenDescuentoCobro']['iva'] > 0){
                $temp = $this->arma_temporal($cobro);

                if(!$this->Temporal->grabar($temp)){
                        $STOP = 1;
                        break;
                }			
            }

            if($asinc->detenido()){
                    $STOP = 2;
                    break;
            }			
            $nContador++;


        endforeach;

        
        if($STOP != 0){
            $asinc->actualizar($nContador,$total,"SE PRODUJO UN ERROR...");
            return;
        }		
	
        $facturas = $this->getFacturas($asinc->id);
        
        $total = count($facturas);
        $asinc->setTotal($total);
        $nContador = 1;	
        $asinc->actualizar(1,100,"ESPERE, FACTURANDO LOS COBROS DEL PERIODO...");
            

// Facturo los cobros contra el WS de AFIP
        foreach($facturas as $factura):
            $asinc->actualizar($nContador,$total,"$nContador / $total - FACTURANDO COBROS >> " . $factura['AsincronoTemporal']['texto_5'] . ' - ' . $factura['AsincronoTemporal']['texto_3'] . ', ' . $factura['AsincronoTemporal']['texto_4']);
            
            if($factura[0]['iva'] > 0){
                $dbFacturas = $this->arma_factura_afip($factura);

                if(!$this->fctAfip($dbFacturas)){
                    $STOP = 1;
                    break;
                    
                }
            }
            
            if($asinc->detenido()){
                $STOP = 2;
                break;
            }
            
            $nContador++;
            
        endforeach;

        
        if($STOP != 0){
            $asinc->actualizar($nContador,$total,"SE PRODUJO UN ERROR...");
            return;
        }
        
        
        //limpio la tabla temporal
        if(!$this->Temporal->limpiarTabla($asinc->id)){
                $asinc->fin("SE PRODUJO UN ERROR...");
                return;
        }

            
// Proceso los Reversos de los Cobros
        $cobros = array();
        $cobros = $this->getCobroReverso();
        $total = count($cobros);
        $asinc->setTotal($total);
        $nContador = 1;	
        $asinc->actualizar(1,100,"ESPERE, CONSULTANDO COBROS REVERSADO DEL PERIODO...");
        

        $temp = array();

        foreach($cobros as $cobro):

            $asinc->actualizar($nContador,$total,"$nContador / $total - REVERSOS >> " . $cobro['OrdenDescuentoCobro']['apellido'] . ', ' . $cobro['OrdenDescuentoCobro']['nombre']);

//            if($cobro['OrdenDescuentoCobro']['importe'] > 0){
            if($cobro['OrdenDescuentoCobro']['iva'] > 0){
                $temp = $this->arma_temporal($cobro);

                if(!$this->Temporal->grabar($temp)){
                        $STOP = 1;
                        break;
                }			
            }
                
            if($asinc->detenido()){
                    $STOP = 2;
                    break;
            }			
            $nContador++;


        endforeach;
        

        if($STOP != 0){
            $asinc->actualizar($nContador,$total,"SE PRODUJO UN ERROR...");
            return;
        }		
	
        $facturas = $this->getFacturas($asinc->id);
        
        $total = count($facturas);
        $asinc->setTotal($total);
        $nContador = 1;	
        $asinc->actualizar(1,100,"ESPERE, FACTURANDO COBROS REVERSADO DEL PERIODO...");
            

// Facturo los cobros reversados contra el WS de AFIP
        foreach($facturas as $factura):
            $asinc->actualizar($nContador,$total,"$nContador / $total - FACTURAR REVERSOS >> " . $cobro['OrdenDescuentoCobro']['apellido'] . ', ' . $cobro['OrdenDescuentoCobro']['nombre']);

            if($factura[0]['iva'] > 0){
                $dbFacturas = $this->arma_factura_afip($factura);

                if(!$this->fctAfip($dbFacturas, 3)){
                    $STOP = 1;
                    break;
                }
            }
            
            if($asinc->detenido()){
                $STOP = 2;
                break;
            }

            $nContador++;
            
        endforeach;
        
        
        if($STOP != 0){
            $asinc->actualizar($nContador,$total,"SE PRODUJO UN ERROR...");
            return;
        }
        
        if($STOP == 0){
            $asinc->actualizar($nContador,$total,"PRIMERA PARTE FINALIZADO SIN ERRORES......");
            $asinc->fin("**** ULTIMA PARTE FINALIZADO SIN ERRORES. ****");
        }
        
        return;
		
	}
	//FIN PROCESO ASINCRONO
	


    function fctAfip($aFactura, $tipo = 1){
        $aVoucherInfo = array();
        
        $tipoCbte = $this->getTipoCbte($tipo);
        
        try {
            $last_voucher = $this->Afip->ElectronicBilling->GetLastVoucher($tipoCbte['sucursal'], $tipoCbte['documento']); //Devuelve el número del último comprobante creado para el punto de venta 1 y el tipo de comprobante 6 (Factura B)
        }
        
        catch (Exception $e) {
        }
        
        if($last_voucher > 0){
            $aVoucherInfo = $this->Afip->ElectronicBilling->GetVoucherInfo($last_voucher, $tipoCbte['sucursal'], $tipoCbte['documento']);
        }


        $voucher = array(
            'CantReg' 	=> 1, // Cantidad de comprobantes a registrar
            'PtoVta' 	=> $tipoCbte['sucursal'], // Punto de venta
            'CbteTipo' 	=> $tipoCbte['documento'], // Tipo de comprobante (ver tipos disponibles) 
            'Concepto' 	=> 2, // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
            'DocTipo' 	=> 96, // Tipo de documento del comprador (ver tipos disponibles)
            'DocNro' 	=> (float) $aFactura['Factura']['numero_documento'], // Numero de documento del comprador
            'CbteDesde' 	=> $last_voucher + 1, // Numero de comprobante o numero del primer comprobante en caso de ser mas de uno
            'CbteHasta' 	=> $last_voucher + 1, // Numero de comprobante o numero del ultimo comprobante en caso de ser mas de uno
            'CbteFch' 	=> intval(date('Ymd', strtotime($aFactura['Factura']['fecha_comprobante']))), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
            'ImpTotal' 	=> $aFactura['Factura']['importe_total'], // Importe total del comprobante
            'ImpTotConc' 	=> $aFactura['Factura']['importe_total_concepto'], // Importe neto no gravado
            'ImpNeto' 	=> $aFactura['Factura']['importe_neto'], // Importe neto gravado
            'ImpOpEx' 	=> 0, // Importe exento de IVA
            'ImpIVA' 	=> $aFactura['Factura']['importe_iva'], //Importe total de IVA
            'ImpTrib' 	=> 0, //Importe total de tributos
            'FchServDesde' 	=> intval(date('Ymd', strtotime($aFactura['Factura']['fecha_desde']))), // (Opcional) Fecha de inicio del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
            'FchServHasta' 	=> intval(date('Ymd', strtotime($aFactura['Factura']['fecha_hasta']))), // (Opcional) Fecha de fin del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
            'FchVtoPago' 	=> intval(date('Ymd', strtotime($aFactura['Factura']['fecha_vto_pago']))), // (Opcional) Fecha de vencimiento del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
            'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
            'MonCotiz' 	=> 1, // CotizaciÃ³n de la moneda usada (1 para pesos argentinos)  
            'Iva'           => $aFactura['Factura']['iva'],
        );

        if($tipo != 1){
            $voucher['CbtesAsoc'] = $aFactura['Factura']['CbtesAsoc'];
        }
            

        $voucher['persona_id'] = $aFactura['Factura']['persona_id'];
        $voucher['orden_descuento_cobro_id'] = $aFactura['Factura']['orden_descuento_cobro_id'];
        
        try {
            
//             debug($voucher);
            
            $info_voucher = $this->Afip->ElectronicBilling->CreateVoucher($voucher, TRUE);
            $voucher['e_codigo'] = 0;
            $voucher['e_mensaje'] = 'SIN ERRORES';
            
//             debug($info_voucher);

        }

        catch (Exception $e) {
            
//             debug($e);
            
            $voucher['e_codigo']  = $e->getCode();
            $voucher['e_mensaje'] = $e->getMessage();
        }

        $voucher['DocNro'] = strval($aFactura['Factura']['numero_documento']);
        $this->saveFct($voucher, $info_voucher, $tipo);

        return true;

            
    }
        
        
    function saveFct($voucher, $info_voucher, $tipo = 1){
        

        App::import('Model','facturacion.Factura');
        $oFactura = new Factura();

        App::import('Model','mutual.OrdenDescuentoCobroCuota');
        $oCobroCuota = new OrdenDescuentoCobroCuota();
        
        
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
        
        $FacturaId = $oFactura->getLastInsertId();

        $CbcFactura['FacturaProducto'] = $this->getDetalleFactura($this->asincId, $voucher['orden_descuento_cobro_id']);

        foreach($CbcFactura['FacturaProducto'] as $item){
            
            if($tipo != 3){
                $oCobroCuota->updateAll(array('OrdenDescuentoCobroCuota.factura_id' => $FacturaId), array('OrdenDescuentoCobroCuota.id' => $item['orden_descuento_cobro_cuota_id']));
            }
            else{
                $oCobroCuota->updateAll(array('OrdenDescuentoCobroCuota.reverso_factura_id' => $FacturaId), array('OrdenDescuentoCobroCuota.id' => $item['orden_descuento_cobro_cuota_id']));
            }
        }
            
        return TRUE;
    }

/*
	function getCobros($tipo = 1){
            
        $sql = "SELECT 
                    OrdenDescuentoCobro.id,
                    OrdenDescuentoCobroCuota.proveedor_id,
                    GlbDato.entero_2 AS codigo_iva,
                    OrdenDescuentoCobroCuota.factura_id,";
        
        if($tipo != 3){
            $sql .= " OrdenDescuentoCobro.fecha,
                    OrdenDescuentoCobroCuota.importe AS importe,
                    ROUND(OrdenDescuentoCobroCuota.importe * (OrdenDescuentoCuota.iva / OrdenDescuentoCuota.importe),2) AS iva,
                    ROUND(OrdenDescuentoCobroCuota.importe * (OrdenDescuentoCuota.interes / OrdenDescuentoCuota.importe),2) AS neto,
                    OrdenDescuentoCobroCuota.importe - (
                    ROUND(OrdenDescuentoCobroCuota.importe * (OrdenDescuentoCuota.iva / OrdenDescuentoCuota.importe),2) +
                    ROUND(OrdenDescuentoCobroCuota.importe * (OrdenDescuentoCuota.interes / OrdenDescuentoCuota.importe),2)) AS capital,";
        }
        else{
            $sql .= " OrdenDescuentoCobroCuota.fecha_reverso AS fecha,
                    OrdenDescuentoCobroCuota.importe_reversado AS importe,
                    ROUND(OrdenDescuentoCobroCuota.importe_reversado * (OrdenDescuentoCuota.iva / OrdenDescuentoCuota.importe),2) AS iva,
                    ROUND(OrdenDescuentoCobroCuota.importe_reversado * (OrdenDescuentoCuota.interes / OrdenDescuentoCuota.importe),2) AS neto,
                    OrdenDescuentoCobroCuota.importe_reversado - (
                    ROUND(OrdenDescuentoCobroCuota.importe_reversado * (OrdenDescuentoCuota.iva / OrdenDescuentoCuota.importe),2) +
                    ROUND(OrdenDescuentoCobroCuota.importe_reversado * (OrdenDescuentoCuota.interes / OrdenDescuentoCuota.importe),2)) AS capital,";

        }
        $sql .= " OrdenDescuentoCobroCuota.id AS orden_descuento_cobro_cuota_id,
                 Persona.id,
                 Persona.tipo_documento,
                 Persona.documento,
                 Persona.apellido,
                 Persona.nombre,
                 Persona.cuit_cuil,
                 OrdenDescuentoCuota.tipo_producto,
                 OrdenDescuentoCuota.tipo_cuota,
                 CONCAT('SERVICIO C.', OrdenDescuento.numero, '- CTA.', OrdenDescuentoCuota.nro_cuota, '/',  OrdenDescuento.cuotas, ' * ', GlbDato.concepto_1) AS descripcion
            FROM orden_descuento_cobros AS OrdenDescuentoCobro
            LEFT JOIN orden_descuento_cobro_cuotas AS OrdenDescuentoCobroCuota ON (OrdenDescuentoCobroCuota.orden_descuento_cobro_id = OrdenDescuentoCobro.id)
            LEFT JOIN proveedores AS Proveedor ON (OrdenDescuentoCobroCuota.proveedor_id = Proveedor.id)
            LEFT JOIN global_datos AS GlobalDato ON (GlobalDato.id = OrdenDescuentoCobro.tipo_cobro)
            LEFT JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON (OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id)
            LEFT JOIN orden_descuentos AS OrdenDescuento ON (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
            LEFT JOIN socios AS Socio ON (Socio.id = OrdenDescuentoCuota.socio_id)
            LEFT JOIN personas AS Persona ON (Persona.id = Socio.persona_id)
            LEFT JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
            LEFT JOIN global_datos AS GlbDato ON (CONCAT(GlbDato.concepto_3,GlbDato.id) = CONCAT(OrdenDescuentoCuota.tipo_orden_dto,OrdenDescuentoCuota.tipo_producto))";
        if($tipo != 3){
            $sql .= " WHERE 
            		OrdenDescuentoCobro.fecha BETWEEN '".$this->fecha_desde."' AND '".$this->fecha_hasta."' AND
                    OrdenDescuentoCobroCuota.proveedor_id = '" . $this->empresaId . "' AND
                    OrdenDescuentoCobro.anulado = 0 AND OrdenDescuentoCobroCuota.factura_id IS NULL";

        }
        else{
            $sql .= " WHERE 
		            OrdenDescuentoCobroCuota.fecha_reverso BETWEEN '".$this->fecha_desde."' AND '".$this->fecha_hasta."' AND
                    OrdenDescuentoCobroCuota.proveedor_id = '" . $this->empresaId . "' AND
            	    OrdenDescuentoCobro.anulado = 0 AND OrdenDescuentoCobroCuota.factura_id IS NOT NULL AND
                    OrdenDescuentoCobroCuota.reverso_factura_id IS NULL AND OrdenDescuentoCobroCuota.reversado = 1";
            
        }
        
        $sql .= " ORDER BY OrdenDescuentoCobro.fecha, OrdenDescuentoCobro.id, OrdenDescuentoCuota.id";

        $cobros = $this->OrdenDescuentoCobro->query($sql);

        foreach($cobros as $idx => $cobro):
            $cobro['OrdenDescuentoCobro']['persona_id'] = $cobro['Persona']['id'];
            $cobro['OrdenDescuentoCobro']['tipo_documento'] = $cobro['Persona']['tipo_documento'];
            $cobro['OrdenDescuentoCobro']['documento'] = $cobro['Persona']['documento'];
            $cobro['OrdenDescuentoCobro']['apellido'] = $cobro['Persona']['apellido'];
            $cobro['OrdenDescuentoCobro']['nombre'] = $cobro['Persona']['nombre'];
            $cobro['OrdenDescuentoCobro']['cuit_cuil'] = $cobro['Persona']['cuit_cuil'];
            $cobro['OrdenDescuentoCobro']['orden_descuento_cobro_cuota_id'] = $cobro['OrdenDescuentoCobroCuota']['orden_descuento_cobro_cuota_id'];
            $cobro['OrdenDescuentoCobro']['factura_id'] = $cobro['OrdenDescuentoCobroCuota']['factura_id'];
            $cobro['OrdenDescuentoCobro']['tipo_producto'] = $cobro['OrdenDescuentoCuota']['tipo_producto'];
            $cobro['OrdenDescuentoCobro']['tipo_cuota'] = $cobro['OrdenDescuentoCuota']['tipo_cuota'];
            $cobro['OrdenDescuentoCobro']['descripcion'] = $cobro[0]['descripcion'];
            $cobro['OrdenDescuentoCobro']['codigo_iva'] = $cobro['GlbDato']['codigo_iva'];
//            $cobro['OrdenDescuentoCobro']['importe'] = $cobro['OrdenDescuentoCobroCuota']['importe'];
            $cobro['OrdenDescuentoCobro']['importe'] = $cobro[0]['neto'] + $cobro[0]['iva'];
            $cobro['OrdenDescuentoCobro']['iva'] = $cobro[0]['iva'];
            $cobro['OrdenDescuentoCobro']['neto'] = $cobro[0]['neto'];
            $cobro['OrdenDescuentoCobro']['capital'] = $cobro[0]['capital'];
            $cobros[$idx] = $cobro;

        endforeach;

        $cobros = Set::extract('/OrdenDescuentoCobro',$cobros);

        return $cobros;
	}
*/
    
function getCobros($tipo = 1) {
    $sql = "SELECT 
                OrdenDescuentoCobro.id,
                OrdenDescuentoCobroCuota.proveedor_id,
                GlbDato.entero_2 AS codigo_iva,
                OrdenDescuentoCobroCuota.factura_id,";
    
    switch ($tipo) {
        case 3:
            $sql .= " OrdenDescuentoCobroCuota.fecha_reverso AS fecha,
                    OrdenDescuentoCobroCuota.importe_reversado AS importe,
                    ROUND(OrdenDescuentoCobroCuota.importe_reversado * (OrdenDescuentoCuota.iva / OrdenDescuentoCuota.importe),2) AS iva,
                    ROUND(OrdenDescuentoCobroCuota.importe_reversado * (OrdenDescuentoCuota.interes / OrdenDescuentoCuota.importe),2) AS neto,
                    OrdenDescuentoCobroCuota.importe_reversado - (
                    ROUND(OrdenDescuentoCobroCuota.importe_reversado * (OrdenDescuentoCuota.iva / OrdenDescuentoCuota.importe),2) +
                    ROUND(OrdenDescuentoCobroCuota.importe_reversado * (OrdenDescuentoCuota.interes / OrdenDescuentoCuota.importe),2)) AS capital,";
            $sql .= " OrdenDescuentoCobroCuota.id AS orden_descuento_cobro_cuota_id,
                     Persona.id,
                     Persona.tipo_documento,
                     Persona.documento,
                     Persona.apellido,
                     Persona.nombre,
                     Persona.cuit_cuil,
                     OrdenDescuentoCuota.tipo_producto,
                     OrdenDescuentoCuota.tipo_cuota,
                     CONCAT('SERVICIO C.', OrdenDescuento.numero, '- CTA.', OrdenDescuentoCuota.nro_cuota, '/',  OrdenDescuento.cuotas, ' * ', GlbDato.concepto_1) AS descripcion
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
                WHERE 
                    OrdenDescuentoCobroCuota.fecha_reverso BETWEEN '".$this->fecha_desde."' AND '".$this->fecha_hasta."' AND
                    OrdenDescuentoCobroCuota.proveedor_id = '" . $this->empresaId . "' AND
                    OrdenDescuentoCobro.anulado = 0 AND OrdenDescuentoCobroCuota.factura_id IS NOT NULL AND
                    OrdenDescuentoCobroCuota.reverso_factura_id IS NULL AND OrdenDescuentoCobroCuota.reversado = 1";
            break;

        default:
            $sql .= " OrdenDescuentoCobro.fecha,
                    OrdenDescuentoCobroCuota.importe AS importe,
                    ROUND(OrdenDescuentoCobroCuota.importe * (OrdenDescuentoCuota.iva / OrdenDescuentoCuota.importe),2) AS iva,
                    ROUND(OrdenDescuentoCobroCuota.importe * (OrdenDescuentoCuota.interes / OrdenDescuentoCuota.importe),2) AS neto,
                    OrdenDescuentoCobroCuota.importe - (
                    ROUND(OrdenDescuentoCobroCuota.importe * (OrdenDescuentoCuota.iva / OrdenDescuentoCuota.importe),2) +
                    ROUND(OrdenDescuentoCobroCuota.importe * (OrdenDescuentoCuota.interes / OrdenDescuentoCuota.importe),2)) AS capital,";
            $sql .= " OrdenDescuentoCobroCuota.id AS orden_descuento_cobro_cuota_id,
                     Persona.id,
                     Persona.tipo_documento,
                     Persona.documento,
                     Persona.apellido,
                     Persona.nombre,
                     Persona.cuit_cuil,
                     OrdenDescuentoCuota.tipo_producto,
                     OrdenDescuentoCuota.tipo_cuota,
                     CONCAT('SERVICIO C.', OrdenDescuento.numero, '- CTA.', OrdenDescuentoCuota.nro_cuota, '/',  OrdenDescuento.cuotas, ' * ', GlbDato.concepto_1) AS descripcion
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
                WHERE 
                    OrdenDescuentoCobro.fecha BETWEEN '".$this->fecha_desde."' AND '".$this->fecha_hasta."' AND
                    OrdenDescuentoCobroCuota.proveedor_id = '" . $this->empresaId . "' AND
                    OrdenDescuentoCobro.anulado = 0 AND OrdenDescuentoCobroCuota.factura_id IS NULL";
            break;
    }

    $sql .= " ORDER BY OrdenDescuentoCobro.fecha, OrdenDescuentoCobro.id, OrdenDescuentoCuota.id";

    $cobros = $this->OrdenDescuentoCobro->query($sql);

    foreach($cobros as $idx => $cobro) {
        $cobro['OrdenDescuentoCobro']['persona_id'] = $cobro['Persona']['id'];
        $cobro['OrdenDescuentoCobro']['tipo_documento'] = $cobro['Persona']['tipo_documento'];
        $cobro['OrdenDescuentoCobro']['documento'] = $cobro['Persona']['documento'];
        $cobro['OrdenDescuentoCobro']['apellido'] = $cobro['Persona']['apellido'];
        $cobro['OrdenDescuentoCobro']['nombre'] = $cobro['Persona']['nombre'];
        $cobro['OrdenDescuentoCobro']['cuit_cuil'] = $cobro['Persona']['cuit_cuil'];
        $cobro['OrdenDescuentoCobro']['orden_descuento_cobro_cuota_id'] = $cobro['OrdenDescuentoCobroCuota']['orden_descuento_cobro_cuota_id'];
        $cobro['OrdenDescuentoCobro']['factura_id'] = $cobro['OrdenDescuentoCobroCuota']['factura_id'];
        $cobro['OrdenDescuentoCobro']['tipo_producto'] = $cobro['OrdenDescuentoCuota']['tipo_producto'];
        $cobro['OrdenDescuentoCobro']['tipo_cuota'] = $cobro['OrdenDescuentoCuota']['tipo_cuota'];
        $cobro['OrdenDescuentoCobro']['descripcion'] = $cobro[0]['descripcion'];
        $cobro['OrdenDescuentoCobro']['codigo_iva'] = $cobro['GlbDato']['codigo_iva'];
        $cobro['OrdenDescuentoCobro']['importe'] = $cobro[0]['neto'] + $cobro[0]['iva'];
        $cobro['OrdenDescuentoCobro']['iva'] = $cobro[0]['iva'];
        $cobro['OrdenDescuentoCobro']['neto'] = $cobro[0]['neto'];
        $cobro['OrdenDescuentoCobro']['capital'] = $cobro[0]['capital'];
        $cobros[$idx] = $cobro;
    }

    $cobros = Set::extract('/OrdenDescuentoCobro', $cobros);

    return $cobros;
}
    
        
    function getCobroReverso(){
        
        return $this->getCobros(3);
        
    }
                

    function getFacturas($vid){
        
        $sql = "SELECT AsincronoTemporal.*, SUM(decimal_1) AS total, SUM(decimal_2) AS iva, SUM(decimal_3) AS gravado, SUM(decimal_4) AS exento
                FROM asincrono_temporales AsincronoTemporal
                WHERE asincrono_id = '" . $vid . "'
                GROUP BY clave_1
                ORDER BY texto_1, clave_1
                ";

	    $facturas = $this->OrdenDescuentoCobro->query($sql);

        return $facturas;
	}

        
    function getDetalleIva($vid, $vClave){
        $sql = "SELECT AsincronoTemporal.*, SUM(decimal_1) AS recaudado, SUM(decimal_2) AS iva, SUM(decimal_3) AS gravado, SUM(decimal_4) AS total
            FROM	asincrono_temporales AsincronoTemporal
            WHERE	decimal_2 > 0 AND asincrono_id = '" . $vid . "' AND clave_1 = '" . $vClave . "'
            GROUP	BY entero_4
            ";
        
        $detalleIva = $this->OrdenDescuentoCobro->query($sql);

        $itemIva = array();
        foreach($detalleIva as $item):
            $tmpIva = array(
                'Id' 		=> $item['AsincronoTemporal']['entero_4'], // Id del tipo de IVA (ver tipos disponibles) 
                'BaseImp' 	=> $item[0]['gravado'], // Base imponible
                'Importe' 	=> $item[0]['iva'] // Importe 
            );
        
            array_push($itemIva, $tmpIva);
            
        endforeach;
        
        return $itemIva;
        
    }

        
    function getCbteAsociado($factura_id){
        App::import('Model','facturacion.Factura');
        $oFactura = new Factura();
        
        $aDatos = $oFactura->find('all', array('conditions' => array('Factura.id' => $factura_id)));

        $aCbteAsociado = array();
        foreach($aDatos as $item):
            $tmpCbte = array(
                'Tipo' 		=> $item['Factura']['tipo_comprobante'], // Tipo de comprobante (ver tipos disponibles) 
                'PtoVta' 	=> $item['Factura']['punto_venta'], // Punto de venta
                'Nro' 		=> $item['Factura']['numero_comprobante'], // Numero de comprobante
//                 'Cuit' 		=> (float) $item['Factura']['numero_documento'] // (Opcional) Cuit del emisor del comprobante
            );
                            
            array_push($aCbteAsociado, $tmpCbte);
            
        endforeach;

        return $aCbteAsociado;
        
    }
        
        
    function getDetalleFactura($vid, $vClave){
        $sql = "SELECT AsincronoTemporal.*
            FROM	asincrono_temporales AsincronoTemporal
            WHERE	AsincronoTemporal.asincrono_id = '" . $vid . "' AND AsincronoTemporal.clave_1 = '" . $vClave . "'
            ";
        
        $detalleFactura = $this->OrdenDescuentoCobro->query($sql);

        $itemFactura = array();
        foreach($detalleFactura as $item):
            $tmpFactura = array(
                'orden_descuento_cobro_cuota_id' => $item['AsincronoTemporal']['entero_3'],
                'codigo_producto' => 0,
                'tipo_producto'   => 2,
                'descripcion'     => $item['AsincronoTemporal']['texto_8'],
                'cantidad'        => 1,
                'iva' 		  => $item['AsincronoTemporal']['entero_4'], // Id del tipo de IVA (ver tipos disponibles) 
                'importe_base' 	  => $item['AsincronoTemporal']['decimal_3'], // Base imponible
                'importe_iva' 	  => $item['AsincronoTemporal']['decimal_2'], // Importe 
                'importe_total'   => $item['AsincronoTemporal']['decimal_1']
            );
        
            array_push($itemFactura, $tmpFactura);
            
        endforeach;
        
        return $itemFactura;
        
    }
        
        
    function getDatoAfip(){
        App::import('Model','config.AfipDato');
        $oAfipDato = new AfipDato();
        
        $this->datoAfip = $oAfipDato->getAfipDato();
        
        return NULL;
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
        
        
    function arma_temporal($aCobro){
        
        return array(
            'asincrono_id' => $this->asincId,
            'clave_1' => $aCobro['OrdenDescuentoCobro']['id'],
            'texto_1'   => date('Y-m-d',strtotime($this->fecha_factura)),
            'clave_2'   => $aCobro['OrdenDescuentoCobro']['persona_id'],
            'texto_2'   => $aCobro['OrdenDescuentoCobro']['tipo_documento'],
            'entero_1'  => $aCobro['OrdenDescuentoCobro']['documento'],
            'entero_2'  => $aCobro['OrdenDescuentoCobro']['factura_id'],
            'texto_3'   => $aCobro['OrdenDescuentoCobro']['apellido'],
            'texto_4'   => $aCobro['OrdenDescuentoCobro']['nombre'],
            'texto_5'   => $aCobro['OrdenDescuentoCobro']['cuit_cuil'],
            'entero_3'  => $aCobro['OrdenDescuentoCobro']['orden_descuento_cobro_cuota_id'],
            'texto_6'   => $aCobro['OrdenDescuentoCobro']['tipo_producto'],
            'texto_7'   => $aCobro['OrdenDescuentoCobro']['tipo_cuota'],
            'texto_8'   => $aCobro['OrdenDescuentoCobro']['descripcion'],
            'entero_4'  => $aCobro['OrdenDescuentoCobro']['codigo_iva'],
            'decimal_1' => $aCobro['OrdenDescuentoCobro']['importe'],
            'decimal_2' => $aCobro['OrdenDescuentoCobro']['iva'],
            'decimal_3' => $aCobro['OrdenDescuentoCobro']['neto']
/*            'decimal_4' => $aCobro['OrdenDescuentoCobro']['capital']*/
        );
        
    }

        
    function arma_factura_afip($comprobante){
        
        $detalleIva = $this->getDetalleIva($this->asincId, $comprobante['AsincronoTemporal']['clave_1']);

        $dbFacturas['Factura'] = array( 
            'fecha_comprobante' => $comprobante['AsincronoTemporal']['texto_1'],
            'persona_id' => $comprobante['AsincronoTemporal']['clave_2'],
            'orden_descuento_cobro_id' => $comprobante['AsincronoTemporal']['clave_1'],
            'factura_id' => $comprobante['AsincronoTemporal']['entero_2'],
            'punto_venta' => '',
            'tipo_comprobante' => '',
            'codigo_concepto' => '',
            'tipo_documento' => 96,
            'numero_documento' => $comprobante['AsincronoTemporal']['entero_1'],
            'fecha_desde' => $comprobante['AsincronoTemporal']['texto_1'],
            'fecha_hasta' => $comprobante['AsincronoTemporal']['texto_1'],
            'fecha_vto_pago' => $comprobante['AsincronoTemporal']['texto_1'],
            'codigo_moneda' => 'PES',
            'resultado' => '',
            'codigo_autorizacion' => '',
            'tipo_emision' => 'CAE',
            'cae_fecha_vto' => '',
            'cae_fecha_proceso' => '',
            'importe_total' => $comprobante[0]['total'],
            'importe_total_concepto' => $comprobante[0]['exento'],
            'importe_neto' => $comprobante[0]['gravado'],
            'importe_exento' => 0.00,
            'importe_iva' => $comprobante[0]['iva'],
            'importe_tributo' => 0.00,
            'iva' => $detalleIva
        );

        if($comprobante['AsincronoTemporal']['entero_2'] > 0){
            $dbFacturas['Factura']['CbtesAsoc'] = $this->getCbteAsociado($comprobante['AsincronoTemporal']['entero_2']);
        }

        return $dbFacturas;
        
    }
        
}


?>