<?php
class ClienteListado extends ClientesAppModel{
    var $name = 'ClienteListado';
    var $useTable = false;

    function facturas_periodo($fecha_desde, $fecha_hasta){
        $oFacturas = $this->importarModelo('ClienteFactura', 'clientes');
        $oCliente = $this->importarModelo('Cliente', 'clientes');

        $aFacturas = $oFacturas->find('all', array('conditions' => array('ClienteFactura.fecha_comprobante >=' => $fecha_desde, 'ClienteFactura.fecha_comprobante <=' => $fecha_hasta, 'ClienteFactura.tipo !=' => 'SA'), 'order' => array('ClienteFactura.fecha_comprobante')));

        // Armo los datos para el IVA VENTA
        $aReturnFacturas = array();
        foreach($aFacturas as $key => $factura ):
            $aCliente = $oCliente->read(null, $factura['ClienteFactura']['cliente_id']);
            $comprobante = 'FAC';
            if($factura['ClienteFactura']['tipo'] === 'NC') $comprobante = 'NCR';
            if($factura['ClienteFactura']['tipo'] === 'ND') $comprobante = 'NDE';
            $factura['ClienteFactura']['razon_social'] = $aCliente['Cliente']['razon_social'];
            $factura['ClienteFactura']['cuit'] = $aCliente['Cliente']['cuit'];
            $factura['ClienteFactura']['comprobante_libro'] = $comprobante . ' ' . $factura['ClienteFactura']['letra_comprobante'] . '-' . $factura['ClienteFactura']['punto_venta_comprobante'] . '-' . $factura['ClienteFactura']['numero_comprobante'];
            array_push($aReturnFacturas, $factura);
        endforeach;

        return $aReturnFacturas;
    }

	
    function saldo_a_fecha($fechaDesde, $fechaHasta, $cliente_id = 0){
        $oFacturas = $this->importarModelo('ClienteFactura', 'clientes');
        $oCliente = $this->importarModelo('Cliente', 'clientes');
        $oRecibo = $this->importarModelo('Recibo', 'clientes');

        $sql = "
            SELECT Cliente.*, GlobalDato.concepto_1,
            IFNULL((
                SELECT SUM(total_comprobante)
                FROM cliente_facturas ClienteFactura
                WHERE (ClienteFactura.tipo = 'SD' OR ClienteFactura.tipo='FA') AND ClienteFactura.cliente_id = Cliente.id AND 
                          ClienteFactura.fecha_comprobante < '$fechaDesde' AND ClienteFactura.anulado = 0
            ),0) -
            IFNULL(
            (
                SELECT SUM(total_comprobante)
                FROM cliente_facturas ClienteFactura
                WHERE ClienteFactura.tipo != 'SD' AND ClienteFactura.tipo != 'FA' AND ClienteFactura.cliente_id = Cliente.id AND 
                ClienteFactura.fecha_comprobante < '$fechaDesde' AND ClienteFactura.anulado = 0
            ),0) -
            IFNULL(
            (
                SELECT 	SUM(importe)
                FROM	recibos Recibo
                WHERE	Recibo.cliente_id = Cliente.id AND Recibo.anulado = 0 AND Recibo.fecha_comprobante < '$fechaDesde' AND Recibo.anulado = 0
            ),0) AS saldo_anterior,


            IFNULL((
                SELECT SUM(total_comprobante)
                FROM cliente_facturas ClienteFactura
                WHERE (ClienteFactura.tipo = 'SD' OR ClienteFactura.tipo='FA') AND ClienteFactura.cliente_id = Cliente.id AND 
                ClienteFactura.fecha_comprobante BETWEEN '$fechaDesde' AND '$fechaHasta' AND ClienteFactura.anulado = 0
            ),0) AS debito,

            IFNULL(
            (
                SELECT SUM(total_comprobante)
                FROM cliente_facturas ClienteFactura
                WHERE ClienteFactura.tipo != 'SD' AND ClienteFactura.tipo != 'FA' AND ClienteFactura.cliente_id = Cliente.id AND 
                ClienteFactura.fecha_comprobante BETWEEN '$fechaDesde' AND '$fechaHasta' AND ClienteFactura.anulado = 0
            ),0) AS credito,

            IFNULL(
            (
                SELECT 	SUM(importe)
                FROM	recibos Recibo
                WHERE	Recibo.cliente_id = Cliente.id AND Recibo.anulado = 0 AND Recibo.fecha_comprobante BETWEEN '$fechaDesde' AND '$fechaHasta' AND Recibo.anulado = 0
            ),0) AS cobro,

            IFNULL((
                SELECT SUM(total_comprobante)
                FROM cliente_facturas ClienteFactura
                WHERE (ClienteFactura.tipo = 'SD' OR ClienteFactura.tipo='FA') AND ClienteFactura.cliente_id = Cliente.id AND 
                ClienteFactura.fecha_comprobante BETWEEN '$fechaDesde' AND '$fechaHasta' AND ClienteFactura.anulado = 0
            ),0) -

            IFNULL(
            (
                SELECT SUM(total_comprobante)
                FROM cliente_facturas ClienteFactura
                WHERE ClienteFactura.tipo != 'SD' AND ClienteFactura.tipo != 'FA' AND ClienteFactura.cliente_id = Cliente.id AND 
                ClienteFactura.fecha_comprobante BETWEEN '$fechaDesde' AND '$fechaHasta' AND ClienteFactura.anulado = 0
            ),0) -
            IFNULL(
            (
                SELECT 	SUM(importe)
                FROM	recibos Recibo
                WHERE	Recibo.cliente_id = Cliente.id AND Recibo.anulado = 0 AND Recibo.fecha_comprobante BETWEEN '$fechaDesde' AND '$fechaHasta' AND Recibo.anulado = 0
            ),0) AS saldo,


            IFNULL((
                SELECT SUM(total_comprobante)
                FROM cliente_facturas ClienteFactura
                WHERE (ClienteFactura.tipo = 'SD' OR ClienteFactura.tipo='FA') AND ClienteFactura.cliente_id = Cliente.id AND 
                ClienteFactura.fecha_comprobante <= '$fechaHasta' AND ClienteFactura.anulado = 0
            ),0) -
            IFNULL(
            (
                SELECT SUM(total_comprobante)
                FROM cliente_facturas ClienteFactura
                WHERE ClienteFactura.tipo != 'SD' AND ClienteFactura.tipo != 'FA' AND ClienteFactura.cliente_id = Cliente.id AND 
                ClienteFactura.fecha_comprobante <= '$fechaHasta' AND ClienteFactura.anulado = 0
            ),0) -
            IFNULL(
            (
                SELECT 	SUM(importe)
                FROM	recibos Recibo
                WHERE	Recibo.cliente_id = Cliente.id AND Recibo.anulado = 0 AND Recibo.fecha_comprobante <= '$fechaHasta' AND Recibo.anulado = 0
            ),0) AS saldo_actual

            FROM	clientes Cliente
            INNER JOIN global_datos GlobalDato
            ON Cliente.condicion_iva = GlobalDato.id
        ";

        if($cliente_id != 0):
            $sql .= "
                WHERE Cliente.id = '$cliente_id'
            ";
        endif;

        $saldo = $this->query($sql);
        return $saldo;
    }
	

    function ctaCteFecha($id, $desdeFecha, $hastaFecha){
        $oCliente = $this->importarModelo('Cliente', 'clientes');

        $sqlCtaCte = "SELECT	
            ClienteFactura.fecha_comprobante AS fecha, 

            CONCAT(IF(ClienteFactura.tipo = 'SD' OR ClienteFactura.tipo = 'SD', 'SALDO ANTERIOR', 
            CONCAT(IF(ClienteFactura.tipo = 'FA', 'FACTURA',
            IF(ClienteFactura.tipo = 'ND', 'NOTA DEBITO', 'NOTA CREDITO')), ' ', ClienteFactura.letra_comprobante, '-', 
            ClienteFactura.punto_venta_comprobante, '-', ClienteFactura.numero_comprobante)))AS concepto,

            IF(ClienteFactura.tipo = 'FA' OR ClienteFactura.tipo = 'ND', ClienteFactura.total_comprobante, 0) AS debe,

            IF(ClienteFactura.tipo = 'NC', ClienteFactura.total_comprobante, 0) AS haber,

            ClienteFactura.total_comprobante * IF(ClienteFactura.tipo = 'SD' OR ClienteFactura.tipo='FA' OR ClienteFactura.tipo = 'ND',1, -1) AS saldo,

            ClienteFactura.id, ClienteFactura.tipo,

            IF(IFNULL(IF(ClienteFactura.tipo = 'FA' OR ClienteFactura.tipo = 'SD', (SELECT SUM(importe) FROM recibo_facturas AS ReciboFactura
            WHERE ReciboFactura.cliente_factura_id = ClienteFactura.id), (SELECT SUM(importe) FROM recibo_facturas AS ReciboFactura
            WHERE ReciboFactura.cliente_credito_id = ClienteFactura.id)),0) = 0, 0, 1) AS anular,

            ClienteFactura.comentario, ClienteFactura.orden_descuento_cobro_id AS orden_cobro,

            IFNULL(IF(ClienteFactura.tipo = 'FA' OR ClienteFactura.tipo = 'SD', (SELECT SUM(importe) FROM recibo_facturas AS ReciboFactura
            WHERE ReciboFactura.cliente_factura_id = ClienteFactura.id), (SELECT SUM(importe) FROM recibo_facturas AS ReciboFactura
            WHERE ReciboFactura.cliente_credito_id = ClienteFactura.id)),0) AS cobro_comprobante


            FROM cliente_facturas AS ClienteFactura
            WHERE ClienteFactura.cliente_id = '$id' AND ClienteFactura.anulado = 0 AND ClienteFactura.fecha_comprobante BETWEEN '$desdeFecha' AND '$hastaFecha' 

            UNION

            SELECT Recibo.fecha_comprobante AS fecha, CONCAT('RECIBO NRO.: ', Recibo.letra, '-', Recibo.sucursal, '-', Recibo.nro_recibo) AS concepto,
            0 AS debe, Recibo.importe AS haber, Recibo.importe * -1 AS saldo, Recibo.id, 'REC' AS tipo, 0 AS anular, Recibo.comentarios, 0 AS orden_cobro, 0 AS cobro_comprobante
            FROM recibos AS Recibo
            WHERE Recibo.cliente_id = '$id' AND Recibo.anulado = 0 AND Recibo.fecha_comprobante BETWEEN '$desdeFecha' AND '$hastaFecha' 
            ORDER BY fecha, tipo";

        $aCtaCte = $this->query($sqlCtaCte);

        $ctaCte = array();
        $tmpCtaCte = array();
        $saldo = 0;
        foreach($aCtaCte as $factura){
            $socio = '';
            if($factura[0]['orden_cobro'] > 0) $socio = $oCliente->getNombreSocio($factura[0]['orden_cobro']). ' ** ';
            $tmpCtaCte = array();
            $tmpCtaCte['fecha'] = $factura[0]['fecha'];
            $tmpCtaCte['concepto'] = $factura[0]['concepto'];
            $tmpCtaCte['debe'] = $factura[0]['debe'];
            $tmpCtaCte['haber'] = $factura[0]['haber'];
            $tmpCtaCte['saldo']  = $factura[0]['saldo'] + $saldo;
            $tmpCtaCte['id'] = $factura[0]['id'];
            $tmpCtaCte['tipo'] = $factura[0]['tipo']; 
            $tmpCtaCte['anular'] = $factura[0]['anular'];
            $tmpCtaCte['comentario'] = $socio . $factura[0]['comentario']; 

            $saldo = $tmpCtaCte['saldo'];
            array_push($ctaCte, $tmpCtaCte);
        }


        return $ctaCte;
    }
	
	
	function factura_tipo_asiento($fecha_desde, $fecha_hasta, $tipoAsiento = 0){

		$sql = "
                        SELECT ClienteTipoAsiento.id, ClienteTipoAsiento.concepto, 
                        IFNULL(( SELECT SUM(total_comprobante) 
                        FROM cliente_facturas ClienteFactura 
                        WHERE ClienteFactura.anulado = 0 AND ClienteFactura.tipo='FA' AND ClienteFactura.cliente_tipo_asiento_id = ClienteTipoAsiento.id AND 
                        ClienteFactura.fecha_comprobante BETWEEN '$fecha_desde' AND '$fecha_hasta' AND ClienteFactura.cliente_id 
                        IN( SELECT id FROM clientes Cliente)),0) AS facturado, 

                        IFNULL(( SELECT SUM(total_comprobante) 
                        FROM cliente_facturas ClienteFactura 
                        WHERE ClienteFactura.anulado = 0 AND ClienteFactura.tipo='NC' AND ClienteFactura.cliente_tipo_asiento_id = ClienteTipoAsiento.id AND 
                        ClienteFactura.fecha_comprobante BETWEEN '$fecha_desde' AND '$fecha_hasta' AND ClienteFactura.cliente_id 
                        IN( SELECT id FROM clientes Cliente)),0) AS credito
                        FROM cliente_tipo_asientos ClienteTipoAsiento
                ";
		
		if($tipoAsiento != 0) $sql .= " WHERE ClienteTipoAsiento.id = '$tipoAsiento'";

		return $this->query($sql);
		
	}
	
	
	function factura_tipo_asiento_detalle($tipo_asiento, $desdeFecha, $hastaFecha){
		$sql = "
				SELECT Cliente.cuit, Cliente.razon_social_resumida as razon_social, GlobalDato.concepto_1 AS tipo_iva, 
				       ClienteFactura.*
				FROM clientes Cliente
				INNER JOIN cliente_facturas ClienteFactura
				ON Cliente.id = ClienteFactura.cliente_id
				LEFT JOIN global_datos GlobalDato
				ON Cliente.condicion_iva = GlobalDato.id
				LEFT JOIN global_datos GlobalDato1
				ON ClienteFactura.tipo_comprobante = GlobalDato1.id
				WHERE ClienteFactura.anulado = 0 AND ClienteFactura.tipo NOT IN('SA', 'SD') AND ClienteFactura.cliente_tipo_asiento_id = '$tipo_asiento' AND ClienteFactura.fecha_comprobante BETWEEN '$desdeFecha' AND '$hastaFecha'";
		
				$sql .= " ORDER BY ClienteFactura.fecha_comprobante
				
		";
		
                $aFacturas = $this->query($sql);
                
                // Armo los datos para el IVA VENTA
                $aReturnFacturas = array();
                foreach($aFacturas as $key => $factura ):
                    $comprobante = 'FAC';
                    if($factura['ClienteFactura']['tipo'] === 'NC') $comprobante = 'NCR';
                    if($factura['ClienteFactura']['tipo'] === 'ND') $comprobante = 'NDE';
                    $factura['ClienteFactura']['comprobante_libro'] = $comprobante . ' ' . $factura['ClienteFactura']['letra_comprobante'] . '-' . $factura['ClienteFactura']['punto_venta_comprobante'] . '-' . $factura['ClienteFactura']['numero_comprobante'];
                    array_push($aReturnFacturas, $factura);
                endforeach;
		
		return $aReturnFacturas;
		
	}
	
        function ws_factura_afip($cliente_factura_id){
//            ini_set('default_socket_timeout', 600);

/*
                $datoAfip['cuit'] = '';
                $datoAfip['modo'] = 0;
                $datoAfip['certificado'] = '';
                $datoAfip['clave'] = '';
                $datoAfip['pem'] = '';
                $datoAfip['factura'] = 0;
                $datoAfip['debito'] = 0;
                $datoAfip['credito'] = 0;
                $datoAfip['punto'] = 0;
*/

            $oClnFactura = $this->importarModelo('ClienteFactura', 'clientes');
            $aClntFct = $oClnFactura->getFactura($cliente_factura_id);

            if(!empty($aClntFct['Afip_CodAutorizacion']))
                return $aClntFct;
            
            $oCliente = $this->importarModelo('Cliente', 'clientes');
            $aCliente = $oCliente->getCliente($aClntFct['cliente_id']);

            $include = ROOT . DS . APP_DIR . DS . 'vendors' . DS . 'afip' . DS . 'src' . DS . 'Afip.php';

            $oAfipDato = $this->importarModelo('AfipDato', 'config');
            
            $datoAfip = $oAfipDato->getAfipDato();


            include $include;
//            if($datoAfip['GlobalDato']['modo'] == 0){     // HOMOLOGACION
              $afip = new Afip(array('CUIT' => $datoAfip['AfipDato']['cuit'], 'cert' => $datoAfip['AfipDato']['pem'], 'key' => $datoAfip['AfipDato']['clave']));
//            }else{     // PRODUCCION
//                $afip = new Afip(array('CUIT' => $datoAfip['AfipDato']['cuit'], 'production' => TRUE, 'cert' => $datoAfip['AfipDato']['certificado'], 'key' => $datoAfip['AfipDato']['clave']));
//            }
            $server_status = $afip->ElectronicBilling->GetServerStatus();

/*
            $aVTypes = $afip->ElectronicBilling->GetVoucherTypes();
            $aCTypes = $afip->ElectronicBilling->GetConceptTypes();
            $aDTypes = $afip->ElectronicBilling->GetDocumentTypes();
            $aATypes = $afip->ElectronicBilling->GetAliquotTypes();
            $aMTypes = $afip->ElectronicBilling->GetCurrenciesTypes();
            $aOTypes = $afip->ElectronicBilling->GetOptionsTypes();
            $aTTypes = $afip->ElectronicBilling->GetTaxTypes();
            
debug($datoAfip);
debug($server_status);
debug($aVTypes);
debug($aCTypes);
debug($aDTypes);
debug($aATypes);
debug($aMTypes);
debug($aOTypes);
debug($aTTypes);
exit;
*/
/*
$taxpayer_details = $afip->RegisterScopeTen->GetTaxpayerDetails(20162290267);
debug($taxpayer_details);
exit;
*/

            
// $aDTypes = $afip->ElectronicBilling->GetDocumentTypes();
// debug($aDTypes);
// exit;

            $nCbteTipo = 0;
            if($aClntFct['tipo'] === 'FA')
                $nCbteTipo = $datoAfip['AfipDato']['factura'];
            
            if($aClntFct['tipo'] === 'NC')
                $nCbteTipo = $datoAfip['AfipDato']['credito'];
            
            if($aClntFct['tipo'] === 'ND')
                $nCbteTipo = $datoAfip['AfipDato']['debito'];


            $last_voucher = $afip->ElectronicBilling->GetLastVoucher($datoAfip['AfipDato']['punto'],$nCbteTipo); //Devuelve el número del último comprobante creado para el punto de venta 1 y el tipo de comprobante 6 (Factura B)

            $VoucherInfo = $afip->ElectronicBilling->GetVoucherInfo($last_voucher, $datoAfip['AfipDato']['punto'], $nCbteTipo);
            $last_CbteFch = $afip->ElectronicBilling->FormatDate($VoucherInfo->CbteFch);
            
            $aClntFct['Afip_CbteFch'] = $aClntFct['fecha_comprobante'];
            if($aClntFct['fecha_comprobante'] < $last_CbteFch) $aClntFct['Afip_CbteFch'] = $last_CbteFch;


            $voucher = $this->arma_voucher($aClntFct, $aCliente, $datoAfip, $last_voucher);

            $voucher['CbteTipo'] = $nCbteTipo;

            $info_voucher = $afip->ElectronicBilling->CreateVoucher($voucher, TRUE);


            $aClntFct['Afip_Concepto'] = $info_voucher->FeDetResp->FECAEDetResponse->Concepto;
            $aClntFct['Afip_DocTipo'] = $info_voucher->FeDetResp->FECAEDetResponse->DocTipo;
            $aClntFct['Afip_MonId'] = 'PES';
            $aClntFct['Afip_MonCotiz'] = 1;
            $aClntFct['Afip_Resultado'] = $info_voucher->FeCabResp->Resultado;
            $aClntFct['Afip_CodAutorizacion'] = $info_voucher->FeDetResp->FECAEDetResponse->CAE;
            $aClntFct['Afip_EmisionTipo'] = 'CAE';
            $aClntFct['Afip_NroCbte'] = $info_voucher->FeDetResp->FECAEDetResponse->CbteDesde;
            $aClntFct['Afip_FchVto'] = $afip->ElectronicBilling->FormatDate($info_voucher->FeDetResp->FECAEDetResponse->CAEFchVto);
            $aClntFct['Afip_FchProceso'] = $afip->ElectronicBilling->FormatDateL($info_voucher->FeCabResp->FchProceso); 
            $aClntFct['Afip_PtoVta'] = $info_voucher->FeCabResp->PtoVta;
            $aClntFct['Afip_CbteTipo'] = $info_voucher->FeCabResp->CbteTipo;
            $aClntFct['Afip_CbteFch'] = $afip->ElectronicBilling->FormatDate($info_voucher->FeDetResp->FECAEDetResponse->CbteFch);
            $aClntFct['Afip_FchServDesde'] = $afip->ElectronicBilling->FormatDate($info_voucher->FeDetResp->FECAEDetResponse->CbteFch);
            $aClntFct['Afip_FchServHasta'] = $afip->ElectronicBilling->FormatDate($info_voucher->FeDetResp->FECAEDetResponse->CbteFch);
            $aClntFct['Afip_FchVtoPago'] = $afip->ElectronicBilling->FormatDate($info_voucher->FeDetResp->FECAEDetResponse->CbteFch);
            
            $oClnFactura->save($aClntFct);		

            return $aClntFct;
            
        }
        
        function arma_voucher($aClntFct, $aCliente, $datoAfip, $last_voucher){

            $voucher = array(
                'CantReg' 	=> 1, // Cantidad de comprobantes a registrar
                'PtoVta' 	=> $datoAfip['AfipDato']['punto'], // Punto de venta
                'CbteTipo' 	=> 0, // Tipo de comprobante (ver tipos disponibles) 
                'Concepto' 	=> 2, // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
                'DocTipo' 	=> 80, // Tipo de documento del comprador (ver tipos disponibles)
                'DocNro' 	=> (float) $aCliente['Cliente']['cuit'], // Numero de documento del comprador
                'CbteDesde' 	=> $last_voucher + 1, // Numero de comprobante o numero del primer comprobante en caso de ser mas de uno
                'CbteHasta' 	=> $last_voucher + 1, // Numero de comprobante o numero del ultimo comprobante en caso de ser mas de uno
//                'CbteFch' 	=> intval(date('Ymd')), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
                'CbteFch' 	=> intval(date('Ymd', strtotime($aClntFct['Afip_CbteFch']))), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
                'ImpTotal' 	=> $aClntFct['total_comprobante'], // Importe total del comprobante
                'ImpTotConc' 	=> 0, // Importe neto no gravado
                'ImpNeto' 	=> $aClntFct['total_comprobante'], // Importe neto gravado
                'ImpOpEx' 	=> 0, // Importe exento de IVA
                'ImpIVA' 	=> 0, //Importe total de IVA
                'ImpTrib' 	=> 0, //Importe total de tributos
//                'FchServDesde' 	=> intval(date('Ymd')), // (Opcional) Fecha de inicio del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
//                'FchServHasta' 	=> intval(date('Ymd')), // (Opcional) Fecha de fin del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
//                'FchVtoPago' 	=> intval(date('Ymd')), // (Opcional) Fecha de vencimiento del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
                'FchServDesde' 	=> intval(date('Ymd', strtotime($aClntFct['Afip_CbteFch']))), // (Opcional) Fecha de inicio del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
                'FchServHasta' 	=> intval(date('Ymd', strtotime($aClntFct['Afip_CbteFch']))), // (Opcional) Fecha de fin del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
                'FchVtoPago' 	=> intval(date('Ymd', strtotime($aClntFct['Afip_CbteFch']))), // (Opcional) Fecha de vencimiento del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
                'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
                'MonCotiz' 	=> 1, // CotizaciÃ³n de la moneda usada (1 para pesos argentinos)  
                /*
                'CbtesAsoc' 	=> array( // (Opcional) Comprobantes asociados
                        ),
                'Tributos' 		=> array( // (Opcional) Tributos asociados al comprobante
                ), 
                'Iva' 			=> array( // (Opcional) AlÃ­cuotas asociadas al comprobante
                ), 
                'Opcionales' 	=> array( // (Opcional) Campos auxiliares
                ), 
                'Compradores' 	=> array( // (Opcional) Detalles de los clientes del comprobante 
                )
                 * 
                 
                'Compradores' 	=> array( // (Opcional) Detalles de los clientes del comprobante 
                        array(
                                'DocTipo' 		=> 80, // Tipo de documento (ver tipos disponibles) 
                                'DocNro' 		=> (float) $aCliente['Cliente']['cuit'], // Numero de documento
                                'Porcentaje' 	=> 50 // Porcentaje de titularidad del comprador
                        ),
                        array(
                                'DocTipo' 		=> 80, // Tipo de documento (ver tipos disponibles) 
                                'DocNro' 		=> 20162290267, // Numero de documento
                                'Porcentaje' 	=> 50 // Porcentaje de titularidad del comprador
                        )
                )*/
            );
        
        
            return $voucher;
            /*
            $voucher = array(
                    'CantReg' 		=> 1, // Cantidad de comprobantes a registrar
                    'PtoVta' 		=> $datoAfip['AfipDato']['cuit'], // Punto de venta
                    'CbteTipo' 		=> 0, // Tipo de comprobante (ver tipos disponibles) 
                    'Concepto' 		=> 2, // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
                    'DocTipo' 		=> 80, // Tipo de documento del comprador (ver tipos disponibles)
                    'DocNro' 		=> (float) $aCliente['Cliente']['cuit'], // Numero de documento del comprador
                    'CbteDesde' 	=> $last_voucher + 1, // Numero de comprobante o numero del primer comprobante en caso de ser mas de uno
                    'CbteHasta' 	=> $last_voucher + 1, // Numero de comprobante o numero del ultimo comprobante en caso de ser mas de uno
                    'CbteFch' 		=> intval(date('Ymd')), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
                    'ImpTotal' 		=> $aClntFct['total_comprobante'], // Importe total del comprobante
                    'ImpTotConc' 	=> 0, // Importe neto no gravado
                    'ImpNeto' 		=> $aClntFct['total_comprobante'], // Importe neto gravado
                    'ImpOpEx' 		=> 0, // Importe exento de IVA
                    'ImpIVA' 		=> 0, //Importe total de IVA
                    'ImpTrib' 		=> 0, //Importe total de tributos
                    'FchServDesde' 	=> intval(date('Ymd')), // (Opcional) Fecha de inicio del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
                    'FchServHasta' 	=> intval(date('Ymd')), // (Opcional) Fecha de fin del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
                    'FchVtoPago' 	=> intval(date('Ymd')), // (Opcional) Fecha de vencimiento del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
                    'MonId' 		=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
                    'MonCotiz' 		=> 1, // CotizaciÃ³n de la moneda usada (1 para pesos argentinos)  
                    'CbtesAsoc' 	=> array( // (Opcional) Comprobantes asociados
                            ),
                    'Tributos' 		=> array( // (Opcional) Tributos asociados al comprobante
                    ), 
                    'Iva' 			=> array( // (Opcional) AlÃ­cuotas asociadas al comprobante
                    ), 
                    'Opcionales' 	=> array( // (Opcional) Campos auxiliares
                    ), 
                    'Compradores' 	=> array( // (Opcional) Detalles de los clientes del comprobante 
                    )
        );
*/           
        }
        
        
        function factura_afip($id){

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

            return $aClntFct;
/*
            // Conexion con AFIP.
            $include = ROOT . DS . APP_DIR . DS . 'vendors' . DS . 'afip' . DS . 'src' . DS . 'Afip.php';
            include $include;
            if($datoAfip['GlobalDato']['modo'] == 0){     // HOMOLOGACION
                $afip = new Afip(array('CUIT' => $datoAfip['AfipDato']['cuit'], 'cert' => $datoAfip['AfipDato']['pem'], 'key' => $datoAfip['AfipDato']['clave']));
            }else{     // PRODUCCION
                $afip = new Afip(array('CUIT' => $datoAfip['GlobalDato']['cuit'], 'cert' => $datoAfip['GlobalDato']['certificado'], 'key' => $datoAfip['GlobalDato']['clave']));
            }
            $server_status = $afip->ElectronicBilling->GetServerStatus();
            $aVTypes = $afip->ElectronicBilling->GetVoucherTypes();
            $aCTypes = $afip->ElectronicBilling->GetConceptTypes();
            $aDTypes = $afip->ElectronicBilling->GetDocumentTypes();
            $aATypes = $afip->ElectronicBilling->GetAliquotTypes();
            $aMTypes = $afip->ElectronicBilling->GetCurrenciesTypes();
            $aOTypes = $afip->ElectronicBilling->GetOptionsTypes();
            $aTTypes = $afip->ElectronicBilling->GetTaxTypes();
            
debug($datoAfip);
debug($server_status);
debug($aVTypes);
debug($aCTypes);
debug($aDTypes);
debug($aATypes);
debug($aMTypes);
debug($aOTypes);
debug($aTTypes);
exit;
*/
            
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
        
        
        function arma_ejemplo($prx){
            $data = array(
                'CantReg' 		=> 1, // Cantidad de comprobantes a registrar
                'PtoVta' 		=> 1, // Punto de venta
                'CbteTipo' 		=> 11, // Tipo de comprobante (ver tipos disponibles) 
                'Concepto' 		=> 1, // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
                'DocTipo' 		=> 99, // Tipo de documento del comprador (ver tipos disponibles)
                'DocNro' 		=> 0, // Numero de documento del comprador
                'CbteDesde' 	=> $prx, // Numero de comprobante o numero del primer comprobante en caso de ser mas de uno
                'CbteHasta' 	=> $prx, // Numero de comprobante o numero del ultimo comprobante en caso de ser mas de uno
                'CbteFch' 		=> intval(date('Ymd')), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
                'ImpTotal' 		=> 189.3, // Importe total del comprobante
                'ImpTotConc' 	=> 0, // Importe neto no gravado
                'ImpNeto' 		=> 150, // Importe neto gravado
                'ImpOpEx' 		=> 0, // Importe exento de IVA
                'ImpIVA' 		=> 31.50, //Importe total de IVA
                'ImpTrib' 		=> 7.8, //Importe total de tributos
                'FchServDesde' 	=> NULL, // (Opcional) Fecha de inicio del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
                'FchServHasta' 	=> NULL, // (Opcional) Fecha de fin del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
                'FchVtoPago' 	=> NULL, // (Opcional) Fecha de vencimiento del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
                'MonId' 		=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
                'MonCotiz' 		=> 1, // CotizaciÃ³n de la moneda usada (1 para pesos argentinos)  
        /*	'CbtesAsoc' 	=> array( // (Opcional) Comprobantes asociados 

                        array(
                                'Tipo' 		=> 6, // Tipo de comprobante (ver tipos disponibles) 
                                'PtoVta' 	=> 1, // Punto de venta
                                'Nro' 		=> 1, // Numero de comprobante
                                'Cuit' 		=> 20111111112 // (Opcional) Cuit del emisor del comprobante
                                )

                        ),*/
                'Tributos' 		=> array( // (Opcional) Tributos asociados al comprobante
                        array(
                                'Id' 		=>  99, // Id del tipo de tributo (ver tipos disponibles) 
                                'Desc' 		=> 'Ingresos Brutos', // (Opcional) Descripcion
                                'BaseImp' 	=> 150, // Base imponible para el tributo
                                'Alic' 		=> 5.2, // AlÃ­cuota
                                'Importe' 	=> 7.8 // Importe del tributo
                        )
                ), 
                'Iva' 			=> array( // (Opcional) AlÃ­cuotas asociadas al comprobante
                        array(
                                'Id' 		=> 5, // Id del tipo de IVA (ver tipos disponibles) 
                                'BaseImp' 	=> 150, // Base imponible
                                'Importe' 	=> 31.50 // Importe 
                        )
                ), 
                'Opcionales' 	=> array( // (Opcional) Campos auxiliares
                        array(
                                'Id' 		=> 17, // Codigo de tipo de opcion (ver tipos disponibles) 
                                'Valor' 	=> 2 // Valor 
                        )
                ), 
        /*	'Compradores' 	=> array( // (Opcional) Detalles de los clientes del comprobante 
                        array(
                                'DocTipo' 		=> 80, // Tipo de documento (ver tipos disponibles) 
                                'DocNro' 		=> 20111111112, // Numero de documento
                                'Porcentaje' 	=> 100 // Porcentaje de titularidad del comprador
                        )
                )
        */
            );
        return $data;
        }
	
}
?>