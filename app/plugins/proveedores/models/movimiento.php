<?php
class Movimiento extends ProveedoresAppModel{
	var $name = 'Movimiento';
	var $useTable = false;
	
        function facturasPendientes2($proveedor_id = null) {
            
            if ($proveedor_id == null) {
                return false;
            }               
            
            $SQL = "SELECT * FROM (SELECT
                            ProveedorFactura.id,
                            'B' ordenador,
                            IF (ProveedorFactura.tipo_comprobante = 'SALDOPROVEED',
                            'SALDO ANTERIOR',
                            concat(gd2.concepto_1, ' ', ProveedorFactura.letra_comprobante, ' ', ProveedorFactura.punto_venta_comprobante, '-', ProveedorFactura.numero_comprobante)
                    ) tipo_comprobante_desc,
                            concat(gd.concepto_1, ' ', p.documento, ' - ', p.apellido, ', ', p.nombre, ' ', ltrim(rtrim(ProveedorFactura.comentario))) as comentario,
                            ProveedorFactura.fecha_comprobante,
                            ProveedorFactura.vencimiento1 as vencimiento,
                            ProveedorFactura.total_comprobante,
                            ProveedorFactura.importe_venc1 importe,
                            ifnull(if(ProveedorFactura.tipo = 'FA' or ProveedorFactura.tipo = 'SD', (select sum(importe) FROM orden_pago_facturas AS OrdenPagoFactura WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id), (select sum(importe) FROM orden_pago_facturas AS OrdenPagoFactura WHERE OrdenPagoFactura.proveedor_credito_id = ProveedorFactura.id)), 0) as pago,
                            if(ProveedorFactura.tipo = 'SD'
                                    or ProveedorFactura.tipo = 'FA',
                                    ProveedorFactura.total_comprobante - IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id), 0),
                                    ProveedorFactura.total_comprobante - IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura WHERE OrdenPagoFactura.proveedor_credito_id = ProveedorFactura.id), 0)) AS saldo,
                    '' tipo_pago
                    FROM
                            proveedor_facturas AS ProveedorFactura
                    INNER JOIN global_datos gd2 on
                            gd2.id = ProveedorFactura.tipo_comprobante
                    LEFT JOIN orden_descuento_cobros odc on
                            odc.id = ProveedorFactura.orden_descuento_cobro_id
                    LEFT JOIN socios s on
                            s.id = odc.socio_id
                    LEFT JOIN personas p on
                            p.id = s.persona_id 
                    LEFT JOIN global_datos gd on
                            gd.id = p.tipo_documento
                    WHERE
                            proveedor_id = $proveedor_id
                    having
                            saldo > 0
                    UNION 
                    SELECT
                    op.id,
                    'A' ordenador,
                    'Pago a Cuenta' as tipo_comprobante_desc,
                    CONCAT('ORDEN DE PAGO NRO. : ', RIGHT(CONCAT('00000000', op.nro_orden_pago), 8)) comentario,
                    op.fecha_pago,
                    op.fecha_pago as vencimiento,
                    OrdenPagoDetalle.importe * -1 as total_comprobante,
                    OrdenPagoDetalle.importe * -1 as importe,
                    0 as pago,
                    OrdenPagoDetalle.importe * -1 as saldo,
                    OrdenPagoDetalle.tipo_pago
                    FROM
                            orden_pago_detalles AS OrdenPagoDetalle
                    INNER JOIN orden_pagos op on op.id = OrdenPagoDetalle.orden_pago_id	
                    WHERE
                            OrdenPagoDetalle.proveedor_id = $proveedor_id
                            AND OrdenPagoDetalle.tipo_pago = 'AN'
                            AND OrdenPagoDetalle.importe > 0) t ORDER BY ordenador, vencimiento, comentario;";
            
            $datos = $this->query($SQL);
            return $datos;
            
        }
	
	function facturasPendientes($proveedor_id=null){
            if ($proveedor_id == null) {
                return false;
            }   

        $pagoCuenta = $this->pagoCuenta($proveedor_id);


            $facturas = $this->traerFacturas($proveedor_id, true);

            $facturas = $this->datosAdicionales($facturas, true);

            $compSaldo = array();

            # PRIMERO ORDENO EL ARRAY POR FECHA DE VENCIMIENTO
            $sort_venc = $sort_id = array();
            foreach($pagoCuenta as $c=>$key) {
                $sort_id[] = $key['id'];
                $sort_venc[] = $key['vencimiento'];
            }
            array_multisort($sort_venc, SORT_ASC, $pagoCuenta);
            foreach($pagoCuenta as $pago){
                if($pago['saldo'] != 0):
                    array_push($compSaldo, $pago);
                endif;
            }

            # PRIMERO ORDENO EL ARRAY POR FECHA DE VENCIMIENTO
            $sort_fvenc = $sort_fid = array();
            foreach($facturas as $c=>$key) {
                $sort_fid[] = $key['id'];
                $sort_fvenc[] = $key['vencimiento'];
            }
            array_multisort($sort_fvenc, SORT_ASC, $facturas);
            foreach($facturas as $pago){
                if($pago['saldo'] != 0):
                    array_push($compSaldo, $pago);
                endif;
            }

		
//		# ORDENO EL ARRAY POR FECHA DE VENCIMIENTO
//	    foreach($compSaldo as $c=>$key) {
//    	    $sort_id[] = $key['id'];
//        	$sort_venc[] = $key['vencimiento'];
//	    }
//
//    	array_multisort($sort_venc, SORT_ASC, $compSaldo);

		
            return $compSaldo;
	}

	
	function pagoCuenta($proveedor_id){
		$oPagoCuenta = $this->importarModelo('OrdenPagoDetalle', 'proveedores');
		$pagoCuenta = $oPagoCuenta->getPagoDetalleCuenta($proveedor_id);
		
		if(empty($pagoCuenta)) return array();

		$oPagoFacturas = $this->importarModelo('OrdenPagoFactura', 'proveedores');
		
		foreach($pagoCuenta as $clave => $valor){
			$pagos = $oPagoFacturas->getPagoCuenta($pagoCuenta[$clave]['id']);
			$resto = $pagos;

			$pagoCuenta[$clave]['tipo_comprobante_desc'] = 'Pago a Cuenta';
			$pagoCuenta[$clave]['tipo'] = 'AN';
			$pagoCuenta[$clave]['fecha_comprobante'] = $valor['fecha_comprobante'];
			$pagoCuenta[$clave]['total_comprobante'] = $pagoCuenta[$clave]['importe'] * (-1);
			$pagoCuenta[$clave]['cuota'] = '';
			$pagoCuenta[$clave]['vencimiento'] = $valor['fecha_comprobante'];
			$pagoCuenta[$clave]['saldo'] = $pagos - $pagoCuenta[$clave]['importe'];
			$pagoCuenta[$clave]['pago'] = $pagos;
			$pagoCuenta[$clave]['saldo_comprobante'] = $pagos - $pagoCuenta[$clave]['importe'];
			$pagoCuenta[$clave]['pago_comprobante'] = $pagos;
			$pagoCuenta[$clave]['signo'] = '-';
			$pagoCuenta[$clave]['comentario'] = $valor['comentario'];
			$pagoCuenta[$clave]['orden_descuento_cobro_id'] = 0;
			$pagoCuenta[$clave]['importe'] *= -1;
		}
		
		return $pagoCuenta;
					
	}
	
	
//	function armaCtaCte($proveedor_id){
//$tInicio = $this->microtime_float();
//		$facturas = $this->traerFacturas($proveedor_id);
//		$facturas = $this->datosAdicionales($facturas, false);
//		$ordenPagos = $this->traerOrdenPagos($proveedor_id);
//$tMedio = $this->microtime_float();
//		
//		$ctaCte = array();
//		foreach($facturas as $factura){
//			$tmpCtaCte = array();
//			$tmpCtaCte['fecha'] = $factura['fecha_comprobante'];
//			$tmpCtaCte['concepto'] = $factura['tipo_comprobante_desc'];
//			$tmpCtaCte['debe'] = 0;
//			$tmpCtaCte['haber'] = 0;
//			$tmpCtaCte['saldo'] = 0;
//			$tmpCtaCte['id'] = $factura['id'];
//			$tmpCtaCte['tipo'] =$factura['tipo']; 
//			$tmpCtaCte['anular'] = ($factura['pago_comprobante'] > 0 ? 1 : 0);
//			if($factura['tipo'] == 'FA'){ $tmpCtaCte['haber'] = $factura['total_comprobante']; $tmpCtaCte['saldo'] = $factura['total_comprobante'] * (-1);}
//			if($factura['tipo'] == 'NC'){ $tmpCtaCte['debe'] = $factura['total_comprobante']; $tmpCtaCte['saldo'] = $factura['total_comprobante'];}
//			if($factura['tipo'] == 'SD'){ $tmpCtaCte['saldo'] = $factura['total_comprobante'] * (-1); $tmpCtaCte['anular'] = 1;} 
//			if($factura['tipo'] == 'SA'){ $tmpCtaCte['saldo'] = $factura['total_comprobante']; $tmpCtaCte['anular'] = 1;}
//			$tmpCtaCte['comentario'] = $factura['socio'] . ' ' . $factura['comentario']; 
//			array_push($ctaCte, $tmpCtaCte);
//		}
//		
//		
//		foreach($ordenPagos as $ordenPago){
//			$tmpCtaCte = array();
//			$tmpCtaCte['fecha'] = $ordenPago['fecha_pago'];
//			$tmpCtaCte['concepto'] = 'ORDEN DE PAGO NRO. : ' . str_pad($ordenPago['nro_orden_pago'],8,'0',STR_PAD_LEFT);
//			$tmpCtaCte['debe'] = $ordenPago['importe'];
//			$tmpCtaCte['haber'] = 0;
//			$tmpCtaCte['saldo'] = $ordenPago['importe'];
//			$tmpCtaCte['id'] = $ordenPago['id'];
//			$tmpCtaCte['tipo'] = 'OPA';
//			$tmpCtaCte['anular'] = 0;
//			$tmpCtaCte['comentario'] = $ordenPago['comentario']; 
//			
//			array_push($ctaCte, $tmpCtaCte);
//		}
//		
////		asort($ctaCte);
//	    foreach($ctaCte as $c=>$key) {
//	        $sort_fecha[] = $key['fecha'];
////	        $sort_venc[] = $key['vencimiento'];
//	    }
//	
//	    array_multisort($sort_fecha, SORT_ASC, $ctaCte);
//
//		foreach($ctaCte as $clave => $valor){
//			$ctaCte[$clave]['saldo'] += $saldo;
//			$saldo = $ctaCte[$clave]['saldo'];
//		}
//debug($ctaCte);
//$tFinal = $this->microtime_float();
//debug($tInicio);
//debug($tMedio);
//debug($tFinal);
//debug($tMedio-$tInicio);
//debug($tFinal-$tMedio);
//debug($tFinal-$tInicio);		
//		return $ctaCte;
//	}
	
	
//	function traerFacturas($proveedor_id){
//		$oFacturas = $this->importarModelo('ProveedorFactura', 'proveedores');
//
//		$return = $oFacturas->find('all',array(
//							'conditions' => array('ProveedorFactura.proveedor_id' => $proveedor_id)
//		));
//		$return = Set::extract("{n}.ProveedorFactura",$return);
//
//		return $return;
//		
//	}
	

	function traerOrdenPagos($proveedor_id){

		$oOrdenPagos = $this->importarModelo('OrdenPago', 'proveedores');
		$return = $oOrdenPagos->find('all',array(
							'conditions' => array('OrdenPago.proveedor_id' => $proveedor_id, 'OrdenPago.anulado' => 0)
		));

		$return = Set::extract("{n}.OrdenPago",$return);

		return $return;
		
	}
	
//	function datosAdicionales($resultados, $detalleCuota=false){
//		$retrun = array();
//		foreach($resultados as $clave => $valor){
//			$resultados[$clave]['socio'] = '';
//			if($valor['orden_descuento_cobro_id'] > 0) $resultados[$clave]['socio'] = $this->getNombreSocio($valor['orden_descuento_cobro_id']);
//			
//			$glb = $this->getGlobalDato('concepto_1',$valor['tipo_comprobante']);
//			$resultados[$clave]['tipo_comprobante_desc'] = $glb['GlobalDato']['concepto_1'];
//			
//			if($valor['tipo_comprobante'] == 'SALDOPROVEED') $resultados[$clave]['tipo_comprobante_desc'] = 'SALDO ANTERIOR';
//			else $resultados[$clave]['tipo_comprobante_desc'] .= ' ' . $valor['letra_comprobante'] . ' ' . $valor['punto_venta_comprobante'] . '-' . $valor['numero_comprobante'];
//			
//			$resultados[$clave] = $this->armaSaldo($resultados[$clave]);
//		}
//		if($detalleCuota) $resultados = $this->detalleCuota($resultados);
//		
//		return $resultados;
//	}
//	
//	
//	function armaSaldo($facturas){
//
//		$oPagoFacturas = $this->importarModelo('OrdenPagoFactura', 'proveedores');
//		
//		if($facturas['tipo'] == 'SD' || $facturas['tipo'] == 'FA'):
//			$pagos = $oPagoFacturas->getPagoFactura($facturas['id']);
//			$resto = $pagos;
//			$facturas['saldo'] = $facturas['total_comprobante'] - $pagos;
//			for ($i = 1; $i <= 10; $i++) {
//				if(!empty($facturas["importe_venc$i"])):
//					if($facturas["importe_venc$i"] <= $resto):
//						$facturas["saldo$i"] = 0.00;
//    			   		$resto -= $facturas["importe_venc$i"];
//    			   		$facturas["pago$i"] = $facturas["importe_venc$i"];
//    			   	else:
//						$facturas["saldo$i"] = $facturas["importe_venc$i"] - $resto;
//    			   		$facturas["pago$i"] = $resto;
//    			   		$resto = 0;
//    				endif;
//    			endif;				
//	    	}
//		else: 
//			$pagos = $oPagoFacturas->getPagoNotaCredito($facturas['id']);
//			$resto = $pagos;
//			$facturas['saldo_comprobante'] = $pagos - $facturas['total_comprobante'];
////			$facturas['total_comprobante'] *= -1;
//			for ($i = 1; $i <= 10; $i++) {
//				if(!empty($facturas["importe_venc$i"])):
//					if($facturas["importe_venc$i"] <= $resto):
//    			   		$facturas["saldo$i"] = 0.00;
//						$resto -= $facturas["importe_venc$i"];
//    			   		$facturas["pago$i"] = $facturas["importe_venc$i"];
//    			   	else:
//    			   		$facturas["saldo$i"] = $resto - $facturas["importe_venc$i"];
//    			   		$resto = 0;
//    				endif;
//    			   	$facturas["importe_venc$i"] *= -1;
//    			endif;				
//	    	}
//		endif; 
//		$facturas['pago_comprobante'] = $pagos;
//		return $facturas;
//	}

//	function detalleCuota($cuotas){
//		$detalleCuota = array();
//		$returnCuota = array();
//		$i = 0;
//		foreach($cuotas as $cuota){
//			for($j = 1; $j <= 10; $j++){
//				if(!isset($cuota["saldo$j"])):
//					break;
//				endif;
//				if($cuota["saldo$j"] != 0):
//					$i += 1;
//					$detalleCuota['id'] = $cuota['id'];
//    		        $detalleCuota['proveedor_id'] = $cuota['proveedor_id'];
//        		    $detalleCuota['orden_pago_id'] = '';
//            		$detalleCuota['tipo_pago'] = $cuota['tipo'];
//	            	$detalleCuota['proveedor_factura_id'] = $cuota['id'];
//	    	        $detalleCuota['importe'] = $cuota["importe_venc$j"];
//    	    	    $detalleCuota['tipo_comprobante_desc'] = $cuota['tipo_comprobante_desc'];
//        	    	$detalleCuota['tipo'] = $cuota['tipo'];
//	        	    $detalleCuota['fecha_comprobante'] = $cuota['fecha_comprobante'];  
//    	        	$detalleCuota['total_comprobante'] = $cuota['total_comprobante'];
//	        	    $detalleCuota['cuota'] = $j;
//    	        	$detalleCuota['vencimiento'] = $cuota["vencimiento$j"];  
//	    	        $detalleCuota['saldo'] = $cuota["saldo$j"];
//    	    	    $detalleCuota['pago'] = $cuota["pago$j"];
//        	    	$detalleCuota['saldo_comprobante'] = $cuota['saldo_comprobante'];
//            		$detalleCuota['pago_comprobante'] = $cuota['pago_comprobante'];
//            		$detalleCuota['signo'] = ($cuota['tipo'] == 'SA' ? '-' : '+');
//            		$detalleCuota['comentario'] = $cuota['socio'] . ' ' . $cuota['comentario'];
//            		array_push($returnCuota, $detalleCuota);
//            	endif;
//			}
//		}
//
//		return $returnCuota;
//	}

	function guardarOpago($datos, $transaccion=true, $returnNroOrdenPago = false){
		$renglones = base64_decode($datos['Movimiento']['renglonesSerialize']);
		$renglones = unserialize($renglones);


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
		
		// Caja y Banco Cheques de terceros
		$oChequeTercero = $this->importarModelo('BancoChequeTercero', 'cajabanco');
		
		// Busco el Numero de la Orden de Pago
		$nroOrdenPago = $oTipoDocumento->getNumero('OPA');
		if($nroOrdenPago == 0):		
			return false;
		endif;
		$nroOrdenPago = str_pad($nroOrdenPago, 8, 0, STR_PAD_LEFT);
//		$ordenPagoCabecera['nro_orden_pago'] = $nroOrdenPago;

		// Armo la Cabecera de la Orden de pago
		$ordenPagoCabecera = array(
			'id' => 0,
			'nro_orden_pago' => $nroOrdenPago,
			'fecha_pago' => (isset($datos['Movimiento']['fecha_operacion']) ? $datos['Movimiento']['fecha_operacion'] : $datos['Movimiento']['fecha_pago']),
			'proveedor_id' => (isset($datos['Movimiento']['proveedor_id']) ? $datos['Movimiento']['proveedor_id'] : 0),
			'socio_id' => (isset($datos['Movimiento']['socio_id']) ? $datos['Movimiento']['socio_id'] : 0),
			'id_persona' => (isset($datos['Movimiento']['id_persona']) ? $datos['Movimiento']['id_persona'] : 0),
			'importe' => $datos['Movimiento']['importe_pago'],
			'comentario' => (isset($datos['Movimiento']['observacion']) ? $datos['Movimiento']['observacion'] : ""),
		);
		
		if($transaccion) $this->begin();
		if(!$oOrdenPago->save($ordenPagoCabecera)):		
			if($transaccion) $this->rollback();
			$oTipoDocumento->unLookRegistro('OPA');
			return false;
		endif;

		$nOrdenPagoId = $oOrdenPago->getLastInsertID();
			
		$ordenPagoDetalle = array();
			
		$tmpFactura = array();
		$facturas = array();
		$detallePago = array();
		$tmpAnticipo = array();
		$anticipos = array();
		$importeAnticipo = 0;
		
		if(isset($datos['Movimiento']['detalle'])):
			foreach($datos['Movimiento']['detalle']['check'] as $id => $importe){
				if($datos['Movimiento']['detalle']['tipo'][$id] == 'FA' || $datos['Movimiento']['detalle']['tipo'][$id] == 'SD'):
					$tmpFactura['id'] = 0;
					$tmpFactura['proveedor_id'] = $datos['Movimiento']['proveedor_id'];
					$tmpFactura['proveedor_factura_id'] = $datos['Movimiento']['detalle']['id'][$id];
					$tmpFactura['orden_pago_id'] = $nOrdenPagoId;
					$tmpFactura['tipo_pago'] = 'FA';
					$tmpFactura['proveedor_credito_id'] = 0;
					$tmpFactura['orden_pago_detalle_id'] = 0;
					$tmpFactura['importe'] = $datos['Movimiento']['detalle']['importe_a_pagar'][$id];
					$tmpFactura['saldo'] = $datos['Movimiento']['detalle']['importe_a_pagar'][$id];
					array_push($facturas, $tmpFactura);
					array_push($detallePago, $tmpFactura);
				else:
					$tmpAnticipo['id'] = 0;
					$tmpAnticipo['proveedor_id'] = $datos['Movimiento']['proveedor_id'];
					$tmpAnticipo['proveedor_factura_id'] = 0;
					$tmpAnticipo['orden_pago_id'] = $nOrdenPagoId;
					$tmpAnticipo['tipo_pago'] = 'AN';
					$tmpAnticipo['proveedor_credito_id'] = 0;
					$tmpAnticipo['orden_pago_detalle_id'] = 0;
					$tmpAnticipo['importe'] = $datos['Movimiento']['detalle']['importe_a_pagar'][$id] * (-1);
					$tmpAnticipo['saldo'] = $datos['Movimiento']['detalle']['importe_a_pagar'][$id] * (-1);
					if($datos['Movimiento']['detalle']['tipo'][$id] == 'AN'):
						$tmpAnticipo['orden_pago_detalle_id'] = $datos['Movimiento']['detalle']['id'][$id];
					else:
						$tmpAnticipo['tipo_pago'] = 'NC';
						$tmpAnticipo['proveedor_factura_id'] = $datos['Movimiento']['detalle']['id'][$id];
						$tmpAnticipo['proveedor_credito_id'] = $datos['Movimiento']['detalle']['id'][$id];
					endif;
					array_push($anticipos, $tmpAnticipo);
					$tmpAnticipo['importe'] = $datos['Movimiento']['detalle']['importe_a_pagar'][$id];
					$tmpAnticipo['saldo'] = $datos['Movimiento']['detalle']['importe_a_pagar'][$id];
					array_push($detallePago, $tmpAnticipo);
				endif;
			}
		endif;
			
		if(isset($datos['Movimiento']['detalle_producto'])):
			foreach($datos['Movimiento']['detalle_producto'] as $id => $producto):
			
				$tmpFactura['id'] = 0;
				$tmpFactura['socio_id'] = $datos['Movimiento']['socio_id'];
				$tmpFactura['mutual_producto_solicitud_id'] = (isset($datos['Movimiento']['mutual_producto_solicitud_id']) ? $datos['Movimiento']['mutual_producto_solicitud_id'] : 0);
				$tmpFactura['tipo_producto'] = $producto['tipo_producto'];
				$tmpFactura['orden_pago_id'] = $nOrdenPagoId;
				$tmpFactura['tipo_pago'] = 'FA';
				$tmpFactura['proveedor_credito_id'] = 0;
				$tmpFactura['orden_pago_detalle_id'] = 0;
				$tmpFactura['importe'] = $datos['Movimiento']['importe_pago'];
				array_push($facturas, $tmpFactura);
				array_push($detallePago, $tmpFactura);
			endforeach;
		endif;
				
		if(isset($datos['Movimiento']['detalle_reintegro'])):
			foreach($datos['Movimiento']['detalle_reintegro'] as $id => $reintegro):
				$tmpFactura['id'] = 0;
				$tmpFactura['socio_id'] = $datos['Movimiento']['socio_id'];
				$tmpFactura['socio_reintegro_id'] = $reintegro['socio_reintegro_id'];
				$tmpFactura['orden_pago_id'] = $nOrdenPagoId;
				$tmpFactura['tipo_pago'] = 'FA';
				$tmpFactura['proveedor_credito_id'] = 0;
				$tmpFactura['orden_pago_detalle_id'] = 0;
				$tmpFactura['importe'] = $reintegro['importe'];
				array_push($facturas, $tmpFactura);
				array_push($detallePago, $tmpFactura);
			endforeach;
		endif;
				
		if(isset($datos['Movimiento']['detalle_solicitud'])):
			foreach($datos['Movimiento']['detalle_solicitud'] as $id => $producto):
				$tmpFactura['id'] = 0;
				$tmpFactura['id_persona'] = $datos['Movimiento']['id_persona'];
				$tmpFactura['nro_solicitud'] = $datos['Movimiento']['nro_solicitud'];
				$tmpFactura['orden_pago_id'] = $nOrdenPagoId;
				$tmpFactura['tipo_pago'] = 'FA';
				$tmpFactura['proveedor_credito_id'] = 0;
				$tmpFactura['orden_pago_detalle_id'] = 0;
				$tmpFactura['importe'] = $datos['Movimiento']['importe_pago'];
				array_push($facturas, $tmpFactura);
				array_push($detallePago, $tmpFactura);
			endforeach;
		endif;

				
		$totalFactura = 0;
		$tmpFacturaAnticipo = array();
		$aFacturaAnticipo = array();
		foreach($anticipos as $claveA => $valorA){
			$saldos = round($anticipos[$claveA]['importe'],2);
			foreach($facturas as $claveF => $valorF){
//				if($anticipos[$claveA]['importe'] > 0):
				if($saldos > 0.00):
					if(round($facturas[$claveF]['importe'],2) > 0.00):
						$tmpFacturaAnticipo['proveedor_id'] = $valorF['proveedor_id'];
						$tmpFacturaAnticipo['socio_id'] = (isset($valorF['socio_id']) ? $valorF['socio_id'] : 0);
						$tmpFacturaAnticipo['proveedor_factura_id'] = $valorF['proveedor_factura_id'];
						$tmpFacturaAnticipo['orden_pago_id'] = $valorF['orden_pago_id'];
						$tmpFacturaAnticipo['proveedor_credito_id'] = $anticipos[$claveA]['proveedor_credito_id'];
						$tmpFacturaAnticipo['orden_pago_detalle_id'] = $anticipos[$claveA]['orden_pago_detalle_id'];
						if($facturas[$claveF]['importe'] >= $saldos):
							$tmpFacturaAnticipo['importe'] = $saldos;
							$facturas[$claveF]['importe'] -= $saldos;
							$anticipos[$claveA]['importe'] = 0.00;
							$saldos = 0.00;
						else:
							$tmpFacturaAnticipo['importe'] = $facturas[$claveF]['importe'];
							$anticipos[$claveA]['importe'] -= $facturas[$claveF]['importe'];
							$saldos -= $facturas[$claveF]['importe'];
							$facturas[$claveF]['importe'] = 0.00;
						endif;
						array_push($aFacturaAnticipo, $tmpFacturaAnticipo);
					endif;
				endif;
			}
		}

		
		$aFacturas = array();
		foreach($facturas as $claveF => $valorF){
			if($valorF['importe'] > 0):
				$totalFactura += $valorF['importe'];
				array_push($aFacturas, $valorF);
			endif;
		}
		
		// Armo los renglones de la Forma de pago
		$formaPago = array();
		$tmpPagos = array();
		foreach($renglones as $claveP => $valorP){
			$tmpPagos['id'] = 0;
			$tmpPagos['proveedor_id'] = (isset($valorP['Movimiento']['proveedor_id']) ? $valorP['Movimiento']['proveedor_id'] : 0);
			$tmpPagos['socio_id'] = (isset($valorP['Movimiento']['socio_id']) ? $valorP['Movimiento']['socio_id'] : 0);
			$tmpPagos['banco_cuenta_id'] = $valorP['Movimiento']['tipo_pago'] == 'EF' || $valorP['Movimiento']['tipo_pago'] == 'DB' || $valorP['Movimiento']['tipo_pago'] == 'CT' ? $cajaId : $valorP['Movimiento']['banco_cuenta_id'];
			$tmpPagos['orden_pago_id'] = $nOrdenPagoId;
			$tmpPagos['numero_operacion'] = $valorP['Movimiento']['numero_operacion'];
			$tmpPagos['fecha_operacion'] = (isset($datos['Movimiento']['fecha_operacion']) ? $datos['Movimiento']['fecha_operacion'] : $datos['Movimiento']['fecha_pago']);
			$tmpPagos['fecha_vencimiento'] = $valorP['Movimiento']['fvenc'];
			$tmpPagos['banco_cheque_tercero_id'] = 0;
			$tmpPagos['destinatario'] = $valorP['Movimiento']['destinatario'];
			$tmpPagos['descripcion'] = 'Orden Pago Nro.: ' . $nroOrdenPago;
			$tmpPagos['importe'] = $valorP['Movimiento']['importe_efectivo'];
			$tmpPagos['debe_haber'] = 1;
			$tmpPagos['forma_pago'] = $valorP['Movimiento']['tipo_pago'];
			$tmpPagos['banco_cuenta_movimiento_id'] = 0;
			$tmpPagos['descripcion_pago'] = $valorP['Movimiento']['tipo_pago_desc'];
			if($valorP['Movimiento']['tipo_pago'] == 'EF' || $valorP['Movimiento']['tipo_pago'] == 'DB' || $valorP['Movimiento']['tipo_pago'] == 'CT'):
				$tmpPagos['banco_concepto_id'] = $cncCajaId;
				$tmpPagos['tipo'] = 7; 
				$tmpPagos['concepto'] = 'CAJA';
				if($valorP['Movimiento']['tipo_pago'] == 'CT'):
					$tmpPagos['banco_cheque_tercero_id'] = $valorP['Movimiento']['banco_cheque_tercero_id'];
					$tmpPagos['concepto'] = $valorP['Movimiento']['denominacion'];
				endif;
			elseif($valorP['Movimiento']['tipo_pago'] == 'CH'):
				$tmpPagos['banco_concepto_id'] = $cncChequeId;
				$tmpPagos['tipo'] = 1; 
				$tmpPagos['concepto'] = $valorP['Movimiento']['denominacion'];
			else:
				$tmpPagos['banco_concepto_id'] = $cncTransferenciaId;
				$tmpPagos['tipo'] = 5; 
				$tmpPagos['concepto'] = $valorP['Movimiento']['denominacion'];
			endif;
			array_push($formaPago, $tmpPagos);
		}
					
		if(!empty($aFacturas)):
			// Grabar las Facturas
			if(isset($datos['Movimiento']['proveedor_id']) && $datos['Movimiento']['proveedor_id'] > 0):
				if(!$oOrdenPagoFactura->saveAll($aFacturas)):		
					if($transaccion) $this->rollback();
					$oTipoDocumento->unLookRegistro('OPA');
					return false;
				endif;
			endif;
		
			// Grabar el Detalle de la Orden de Pago
//			if(!$oOrdenPagoDetalle->saveAll($aFacturas)):		
//				if($transaccion) $this->rollback();
//				$oTipoDocumento->unLookRegistro('OPA');
//				return false;
//			endif;

		endif;

		if(!empty($detallePago)):
			if(!$oOrdenPagoDetalle->saveAll($detallePago)):		
				if($transaccion) $this->rollback();
				$oTipoDocumento->unLookRegistro('OPA');
				return false;
			endif;
		endif;
		
		if(!empty($aFacturaAnticipo)):
			if(!$oOrdenPagoFactura->saveAll($aFacturaAnticipo)):
				if($transaccion) $this->rollback();
				$oTipoDocumento->unLookRegistro('OPA');
				return false;
			endif;
		endif;

		$importeAnticipo = round($datos['Movimiento']['importe_pago'] - $totalFactura,2);
		if($importeAnticipo > 0):
			$anticipoDetalle = array(
				'id' => 0,
				'proveedor_id' => $datos['Movimiento']['proveedor_id'],
				'orden_pago_id' => $nOrdenPagoId,
				'tipo_pago' => 'AN',
				'proveedor_factura_id' => 0,
				'mutual_producto_solicitud_id' => (isset($datos['MutualProductoSolicitud']['id']) ? $datos['MutualProductoSolicitud']['id'] : 0),
				'importe' => $importeAnticipo
			);
			
			if(!$oOrdenPagoDetalle->save($anticipoDetalle)):		
				if($transaccion) $this->rollback();
				$oTipoDocumento->unLookRegistro('OPA');
				return false;
			endif;

		endif;

		// Grabar los movimiento de Caja y Banco
		foreach($formaPago as $key => $valor){
			if(!$oBancoMovimiento->save($valor)):
				if($transaccion) $this->rollback();
				$oTipoDocumento->unLookRegistro('OPA');
				return false;
			endif;
			$formaPago[$key]['banco_cuenta_movimiento_id'] = $oBancoMovimiento->getLastInsertID();
			
			if($valor['forma_pago'] == 'CT'):
				$aChequeTercero = array('BancoChequeTercero' => 
						array(
								'id' => $valor['banco_cheque_tercero_id'],
								'salida_banco_cuenta_movimiento_id' => $oBancoMovimiento->getLastInsertID(),
								'orden_pago_id' => $nOrdenPagoId,
								'destinatario' => $valor['destinatario'],
								'fecha_baja' => (isset($datos['Movimiento']['fecha_operacion']) ? $datos['Movimiento']['fecha_operacion'] : $datos['Movimiento']['fecha_pago'])
				));
				
				if(!$oChequeTercero->save($aChequeTercero)):
					if($transaccion) $this->rollback();
					$oTipoDocumento->unLookRegistro('OPA');
					return false;
				endif;
			endif;
		}

		
		// Grabar los valores del pago
		if(!$oOrdenPagoForma->saveAll($formaPago)):		
			if($transaccion) $this->rollback();
			$oTipoDocumento->unLookRegistro('OPA');
			return false;
		endif;

		
		if($transaccion) $this->commit();
		$oTipoDocumento->putNumero('OPA');
		
		if($returnNroOrdenPago) return $nOrdenPagoId;
		else return true;
	
	}


	function traerOrdenDePago($id=null){
		$aOrdenDePago = array();
		
		if(empty($id)) return $aOrdenDePago;
		
		$oOrdenDePago = $this->importarModelo('OrdenPago', 'proveedores');
		$aOrdenDePago = $oOrdenDePago->getOrdenDePago($id); 
		
		$oProveedores = $this->importarModelo('Proveedor', 'proveedores');
//		$aProveedor = $oProveedores->read(null, $aOrdenDePago['OrdenPago']['proveedor_id']);
		$aProveedor = $oProveedores->getProveedor($aOrdenDePago['OrdenPago']['proveedor_id']);

//		$glb = $this->getGlobalDato('concepto_1',$aProveedor['Proveedor']['condicion_iva']);
//		$aProveedor['Proveedor']['iva_concepto'] = $glb['GlobalDato']['concepto_1'];
//			
		
		$aOrdenDePago['Proveedor'] = $aProveedor['Proveedor'];
		
		return $aOrdenDePago;
	}
	
	
	function anular($id){
		$aOPago = array();

		$aOPago['OrdenPago']['id'] = $id;
		$aOPago['OrdenPago']['anulado'] = 1;
		// Llamo a los modelos a utilizar
		// Orden de Pago Cabecera
		$oOrdenPago = $this->importarModelo('OrdenPago', 'proveedores');
			
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
		
		if(!$oOrdenPago->save($aOPago)){
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



	function borrar($id){

		// Llamo al modelo proveedor_facturas
		$oProveedorFactura = $this->importarModelo('ProveedorFactura', 'proveedores');
			
		if(!$oProveedorFactura->deleteAll("ProveedorFactura.id = " . $id)){
			return false;
		}
		
		return true;
	}
	
	
	function traerProveedor($id, $conSaldo=false){
    	$oPROVEEDOR = $this->importarModelo('Proveedor', 'proveedores');		
	    $aProveedor = $oPROVEEDOR->getProveedor($id);
	    
	    if($conSaldo) $aProveedor['Proveedor']['saldo'] = $this->traerSaldo($id);

	    return $aProveedor;
    	
		
	}
	
	
	function getNombreSocio($ordenDescuentoCobroId){
		$this->OrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');
		$this->Socio = $this->importarModelo('Socio', 'pfyj');
		
		$aOrdenDescCobro = $this->OrdenDescuentoCobro->read(null, $ordenDescuentoCobroId);

		return $this->Socio->getApenom($aOrdenDescCobro['OrdenDescuentoCobro']['socio_id']);
		
	}


	function traerFacturas($proveedor_id, $soloSaldo=false){
		$oFacturas = $this->importarModelo('ProveedorFactura', 'proveedores');
//		$sql = "SELECT 
// 				ProveedorFactura.*,
//				if(ProveedorFactura.tipo = 'FA' or ProveedorFactura.tipo = 'SA', (select sum(importe) FROM orden_pago_facturas AS OrdenPagoFactura
// 				WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id), (select sum(importe) FROM orden_pago_facturas AS OrdenPagoFactura
// 				WHERE OrdenPagoFactura.proveedor_credito_id = ProveedorFactura.id)) as pago_comprobante,
//
///* 				
// 				
//// 				IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
//// 				WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id),0)AS pago_comprobante,
//*/
// 				if(ProveedorFactura.tipo = 'SD' or ProveedorFactura.tipo='FA',ProveedorFactura.total_comprobante - IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
// 				WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id),0),0)AS saldo,
//
// 				if(ProveedorFactura.tipo != 'SD' And ProveedorFactura.tipo!='FA', IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
// 				WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id)-ProveedorFactura.total_comprobante,0),0)AS saldo_comprobante
//
//				FROM proveedor_facturas AS ProveedorFactura
//				WHERE
//				proveedor_id = $proveedor_id";
//				
//		if($soloSaldo):
//		$sql .= " and
//				ProveedorFactura.total_comprobante - IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
// 				WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id),0) != 0";
//		endif;

		$sql = "SELECT 
 				ProveedorFactura.*,
				ifnull(if(ProveedorFactura.tipo = 'FA' or ProveedorFactura.tipo = 'SD', (select sum(importe) FROM orden_pago_facturas AS OrdenPagoFactura
 				WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id), (select sum(importe) FROM orden_pago_facturas AS OrdenPagoFactura
 				WHERE OrdenPagoFactura.proveedor_credito_id = ProveedorFactura.id)),0) as pago_comprobante,

 				if(ProveedorFactura.tipo = 'SD' or ProveedorFactura.tipo='FA',ProveedorFactura.total_comprobante - 
				IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
 				WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id),0), ProveedorFactura.total_comprobante -
				IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
 				WHERE OrdenPagoFactura.proveedor_credito_id = ProveedorFactura.id),0)) AS saldo
 				
				
				FROM proveedor_facturas AS ProveedorFactura
				WHERE
				proveedor_id = $proveedor_id";
		
		if($soloSaldo):
			$sql .= " and
	 				if(ProveedorFactura.tipo = 'SD' or ProveedorFactura.tipo='FA',ProveedorFactura.total_comprobante - 
					IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
	 				WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id),0), ProveedorFactura.total_comprobante -
					IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
	 				WHERE OrdenPagoFactura.proveedor_credito_id = ProveedorFactura.id),0)) != 0";
		endif;

		$aFacturas = $this->query($sql);
		
		return $aFacturas;
		
	}
	

	function datosAdicionales($resultados, $detalleCuota=false){
		$tmpReturn = array();
		$return = array();

		foreach($resultados as $clave => $valor){
			$resultados[$clave]['ProveedorFactura']['socio'] = '';
			if($valor['ProveedorFactura']['orden_descuento_cobro_id'] > 0) $resultados[$clave]['ProveedorFactura']['socio'] = $this->getNombreSocio($valor['ProveedorFactura']['orden_descuento_cobro_id']);
			
			$glb = $this->getGlobalDato('concepto_1',$valor['ProveedorFactura']['tipo_comprobante']);
			$resultados[$clave]['ProveedorFactura']['tipo_comprobante_desc'] = $glb['GlobalDato']['concepto_1'];
			
			if($valor['ProveedorFactura']['tipo_comprobante'] == 'SALDOPROVEED') $resultados[$clave]['ProveedorFactura']['tipo_comprobante_desc'] = 'SALDO ANTERIOR';
			else $resultados[$clave]['ProveedorFactura']['tipo_comprobante_desc'] .= ' ' . $valor['ProveedorFactura']['letra_comprobante'] . ' ' . $valor['ProveedorFactura']['punto_venta_comprobante'] . '-' . $valor['ProveedorFactura']['numero_comprobante'];

			$tmpReturn = $this->armaSaldo($resultados[$clave], $detalleCuota);
			
			if($detalleCuota):
				foreach($tmpReturn as $renglon):
					array_push($return, $renglon);
				endforeach; 
			else:
				array_push($return, $tmpReturn);
			endif;
			
		}
//exit;		
		return $return;
	}
	
	
	function armaSaldo($facturas, $detalleCuota=false){
		$tmpFactura = array();
		$returnFactura = array();
		
		$facturas['ProveedorFactura']['pago_comprobante'] = $facturas[0]['pago_comprobante'];
		 
		if($facturas['ProveedorFactura']['tipo'] == 'SD' || $facturas['ProveedorFactura']['tipo'] == 'FA'):
			$resto = $facturas[0]['pago_comprobante'];
			$facturas['ProveedorFactura']['saldo_comprobante'] = 0;
			$facturas['ProveedorFactura']['saldo'] = $facturas['ProveedorFactura']['total_comprobante'] - $facturas[0]['pago_comprobante'];
			$j = 0;
			for ($i = 1; $i <= 10; $i++) {
				if(!empty($facturas['ProveedorFactura']["importe_venc$i"])):
					$j += 1;
					if($facturas['ProveedorFactura']["importe_venc$i"] <= $resto):
						$facturas['ProveedorFactura']["saldo$i"] = 0.00;
    			   		$resto -= $facturas['ProveedorFactura']["importe_venc$i"];
    			   		$facturas['ProveedorFactura']["pago$i"] = $facturas['ProveedorFactura']["importe_venc$i"];
    			   	else:
						$facturas['ProveedorFactura']["saldo$i"] = $facturas['ProveedorFactura']["importe_venc$i"] - $resto;
    			   		$facturas['ProveedorFactura']["pago$i"] = $resto;
    			   		$resto = 0;
    			   		if($detalleCuota):
    			   			$tmpFactura = $this->detalleCuota($facturas['ProveedorFactura'], $i);
    			   			array_push($returnFactura, $tmpFactura);
    			   		endif;
    				endif;
    			endif;				
	    	}
		else: 
			$resto = $facturas[0]['pago_comprobante'];
			$facturas['ProveedorFactura']['saldo_comprobante'] = 0;
			$facturas['ProveedorFactura']['saldo'] = $facturas[0]['pago_comprobante'] - $facturas['ProveedorFactura']['total_comprobante'];
			for ($i = 1; $i <= 10; $i++) {
				if(!empty($facturas['ProveedorFactura']["importe_venc$i"])):
					if($facturas['ProveedorFactura']["importe_venc$i"] <= $resto):
    			   		$facturas['ProveedorFactura']["saldo$i"] = 0.00;
						$resto -= $facturas['ProveedorFactura']["importe_venc$i"];
    			   		$facturas['ProveedorFactura']["pago$i"] = $facturas['ProveedorFactura']["importe_venc$i"];
    			   	else:
    			   		$facturas['ProveedorFactura']["saldo$i"] = $resto - $facturas['ProveedorFactura']["importe_venc$i"];
    			   		$facturas['ProveedorFactura']["pago$i"] = $resto;
    			   		$resto = 0;
    				endif;
    			   	$facturas['ProveedorFactura']["importe_venc$i"] *= -1;
   			   		if($detalleCuota):
   			   			$tmpFactura = $this->detalleCuota($facturas['ProveedorFactura'], $i);
    			   		array_push($returnFactura, $tmpFactura);
   			   		endif;
    			endif;
	    	}
		endif; 
		
		if(!$detalleCuota) $returnFactura = $facturas['ProveedorFactura'];
		
		return $returnFactura;
	}
	

	function traerSaldo($proveedor_id){
		$oFacturas = $this->importarModelo('ProveedorFactura', 'proveedores');
//		$sql = "SELECT 
//				ProveedorFactura.*, sum(ProveedorFactura.total_comprobante) as total,
//				sum(IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
//				WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id),0)) AS pago_comprobante,
//				
//				sum(if(ProveedorFactura.tipo = 'SD' or ProveedorFactura.tipo='FA',ProveedorFactura.total_comprobante, ProveedorFactura.total_comprobante * -1) - 
//				IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id),0)) AS saldo,
//
//				sum(if(ProveedorFactura.tipo != 'SD' And ProveedorFactura.tipo!='FA', IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
//				WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id)-ProveedorFactura.total_comprobante,0),0)) AS saldo_comprobante
//				
//				FROM proveedor_facturas AS ProveedorFactura
//				WHERE
//				proveedor_id = $proveedor_id
//				group	by proveedor_id";

		$sql = "SELECT 
					sum(if(ProveedorFactura.tipo = 'SD' or ProveedorFactura.tipo = 'FA', total_comprobante, total_comprobante *-1)) + 
					sum(if(ProveedorFactura.tipo != 'SD' and ProveedorFactura.tipo != 'FA', (SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
					WHERE OrdenPagoFactura.proveedor_credito_id = ProveedorFactura.id), 0)) as total_comprobante,
				
					sum(IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
					WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id),0)) + ifnull((select sum(importe) from orden_pago_detalles as OrdenPagoDetalle
					where OrdenPagoDetalle.tipo_pago = 'AN' and OrdenPagoDetalle.proveedor_id = ProveedorFactura.proveedor_id and OrdenPagoDetalle.id not in(
					select orden_pago_detalle_id from orden_pago_facturas)),0)  AS pago_comprobante,
					
					sum(if(ProveedorFactura.tipo = 'SD' or ProveedorFactura.tipo = 'FA', total_comprobante, total_comprobante *-1)) + 
					sum(if(ProveedorFactura.tipo != 'SD' and ProveedorFactura.tipo != 'FA', (SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
					WHERE OrdenPagoFactura.proveedor_credito_id = ProveedorFactura.id), 0)) -
					(sum(IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
					WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id),0)) + ifnull((select sum(importe) from orden_pago_detalles as OrdenPagoDetalle
					where OrdenPagoDetalle.tipo_pago = 'AN' and OrdenPagoDetalle.proveedor_id = ProveedorFactura.proveedor_id and OrdenPagoDetalle.id not in(
					select orden_pago_detalle_id from orden_pago_facturas)),0))  AS saldo
				
					FROM proveedor_facturas AS ProveedorFactura
					WHERE
					proveedor_id = $proveedor_id
					group	by proveedor_id
				";
		$aFacturas = $this->query($sql);

		$saldo = 0;
		if(!empty($aFacturas)) $saldo = $aFacturas[0][0]['saldo'];
		
		return $saldo;
		
	}
	


	function detalleCuota($cuota, $j){
            $detalleCuota = array();
            $detalleCuota['id'] = $cuota['id'];
            $detalleCuota['proveedor_id'] = $cuota['proveedor_id'];
            $detalleCuota['orden_pago_id'] = '';
            $detalleCuota['tipo_pago'] = $cuota['tipo'];
            $detalleCuota['proveedor_factura_id'] = $cuota['id'];
            $detalleCuota['importe'] = $cuota["importe_venc$j"];
            $detalleCuota['tipo_comprobante_desc'] = $cuota['tipo_comprobante_desc'];
            $detalleCuota['tipo'] = $cuota['tipo'];
            $detalleCuota['fecha_comprobante'] = $cuota['fecha_comprobante'];  
            $detalleCuota['total_comprobante'] = $cuota['total_comprobante'];
            $detalleCuota['cuota'] = $j;
            $detalleCuota['vencimiento'] = $cuota["vencimiento$j"];  
            $detalleCuota['saldo'] = $cuota["saldo$j"];
            $detalleCuota['pago'] = $cuota["pago$j"];
            $detalleCuota['saldo_comprobante'] = $cuota['saldo_comprobante'];
            $detalleCuota['pago_comprobante'] = $cuota['pago_comprobante'];
            $detalleCuota['signo'] = ($cuota['tipo'] == 'SA' || $cuota['tipo'] == 'NC' ? '-' : '+');
            $detalleCuota['comentario'] = $cuota['socio'] . ' ' . $cuota['comentario'];
            $detalleCuota['orden_descuento_cobro_id'] = $cuota['orden_descuento_cobro_id'];

            return $detalleCuota;
	}

	
	function armaCtaCte($id){
            $this->query("CALL SP_CTACTE_PROVEEDOR($id)");
            return true;
        }
        
        function armaCtaCte2($proveedor_id = NULL) {
            
            if ($proveedor_id == null) {
                return false;
            }  
            $this->query("DELETE FROM proveedor_ctactes WHERE proveedor_id = $proveedor_id;");
            $SQL = "
                    INSERT INTO `proveedor_ctactes` (`item`, `proveedor_id`, `fecha`, `concepto`, `debe`, `haber`, `saldo`, `id`, `tipo`, `anular`, `comentario`, `pagos`)
                    SELECT
                            cast(@item := @item + 1 as unsigned) item,
                            t.*
                    FROM
                            (
                            SELECT
                                    $proveedor_id AS proveedor_id,
                                    ProveedorFactura.fecha_comprobante AS fecha,
                                    CONCAT(IF(ProveedorFactura.tipo_comprobante = 'SALDOPROVEED', 'SALDO ANTERIOR', gd2.concepto_1), ' ',
                            ProveedorFactura.letra_comprobante, '-', ProveedorFactura.punto_venta_comprobante, '-', ProveedorFactura.numero_comprobante) AS concepto,
                                    IF(ProveedorFactura.tipo = 'NC',
                                    ProveedorFactura.total_comprobante,
                                    0) AS debe,
                                    IF(ProveedorFactura.tipo = 'FA',
                                    ProveedorFactura.total_comprobante,
                                    0) AS haber,
                                    ProveedorFactura.total_comprobante * IF(ProveedorFactura.tipo = 'ND'
                                            OR ProveedorFactura.tipo = 'FA',
                                            -1,
                                            1) + 
                                            IFNULL((select SUM(importe) from orden_pago_facturas
                    where proveedor_factura_id = ProveedorFactura.id OR proveedor_credito_id = ProveedorFactura.id),0)
                                            AS saldo,
                                    ProveedorFactura.id AS id,
                                    ProveedorFactura.tipo,
                                    IF(
                                            IFNULL((select SUM(importe) from orden_pago_facturas
                                            where proveedor_factura_id = ProveedorFactura.id OR proveedor_credito_id = ProveedorFactura.id),0) = 0
                                    OR IFNULL(ProveedorFactura.liquidacion_id, 0) <> 0 OR IFNULL(ProveedorFactura.orden_descuento_cobro_id, 0) <> 0,
                                    1,
                                    0) AS anular,
                                    SUBSTR(concat(gd.concepto_1, ' ', p.documento, ' - ', p.apellido, ', ', p.nombre, ' ', ltrim(rtrim(ProveedorFactura.comentario))), 1, 100) as comentario,
                                    IFNULL((select SUM(importe) from orden_pago_facturas
                                    where proveedor_factura_id = ProveedorFactura.id OR proveedor_credito_id = ProveedorFactura.id),0)  AS pagos
                            FROM
                                    proveedor_facturas AS ProveedorFactura
                            INNER JOIN global_datos gd2 on
                                    gd2.id = ProveedorFactura.tipo_comprobante		
                            LEFT JOIN orden_descuento_cobros odc on
                                    odc.id = ProveedorFactura.orden_descuento_cobro_id
                            LEFT JOIN socios s on
                                    s.id = odc.socio_id
                            LEFT JOIN personas p on
                                    p.id = s.persona_id
                            LEFT JOIN global_datos gd on
                                    gd.id = p.tipo_documento
                            WHERE
                                    ProveedorFactura.proveedor_id = $proveedor_id
                    UNION
                            SELECT
                                    $proveedor_id as proveedor_id,
                                    OrdenPago.fecha_pago AS fecha,
                                    CONCAT('ORDEN DE PAGO NRO. : ', RIGHT(CONCAT('00000000', OrdenPago.nro_orden_pago), 8)) AS concepto,
                                    OrdenPago.importe AS debe,
                                    0 AS haber,
                                    OrdenPago.importe AS saldo,
                                    OrdenPago.id AS id,
                                    'OPA' AS tipo,
                                    IF((
                                    SELECT
                                            COUNT(*)
                                    FROM
                                            banco_cuenta_movimientos
                                    WHERE
                                            orden_pago_id = OrdenPago.id
                                            AND banco_cuenta_saldo_id > 0) > 0,
                                    1,
                                    0) AS anular,
                                    OrdenPago.comentario,
                                    IFNULL((SELECT sum(importe) FROM orden_pago_detalles opd
                                                    WHERE opd.orden_pago_id = OrdenPago.id), 0) as pagos
                            FROM
                                    orden_pagos AS OrdenPago
                            WHERE
                                    OrdenPago.proveedor_id = $proveedor_id
                                    AND OrdenPago.anulado = 0
                            ORDER BY
                                    fecha,
                                    tipo) t,
                            (
                            SELECT
                                    @item := 0) r
                    ORDER BY
                            fecha DESC,
                            comentario ;";
            
            $datos = $this->query($SQL);
            return $this->query("SELECT * FROM proveedor_ctactes as ProveedorCtacte WHERE proveedor_id = $proveedor_id ORDER BY fecha DESC, comentario;");
            
        }




        function armaCtaCte___($id){
            $oFacturas = $this->importarModelo('ProveedorFactura', 'proveedores');
            $oOPago = $this->importarModelo('OrdenPago', 'proveedores');
            $oPagoFacturas = $this->importarModelo('OrdenPagoFactura', 'proveedores');
            $oOPagoDetalle = $this->importarModelo('OrdenPagoDetalle', 'proveedores');
            $oCtaCte = $this->importarModelo('ProveedorCtacte', 'proveedores');

            $sqlCtaCte = "SELECT	ProveedorFactura.id as id,
                            ProveedorFactura.fecha_comprobante as fecha, 

                            concat(if(ProveedorFactura.tipo_comprobante = 'SALDOPROVEED', 'SALDO ANTERIOR', 
                            (select concepto_1 from global_datos where id = ProveedorFactura.tipo_comprobante)), ' ',
                            ProveedorFactura.letra_comprobante, '-', ProveedorFactura.punto_venta_comprobante, '-', ProveedorFactura.numero_comprobante) as concepto,

                            if(ProveedorFactura.tipo = 'NC', ProveedorFactura.total_comprobante, 0) as debe,

                            if(ProveedorFactura.tipo = 'FA', ProveedorFactura.total_comprobante, 0) as haber,

                            ProveedorFactura.total_comprobante * if(ProveedorFactura.tipo = 'SD' or ProveedorFactura.tipo='FA',-1, 1) AS saldo,

                            ProveedorFactura.id, ProveedorFactura.tipo, 

                            if(ifnull(if(ProveedorFactura.tipo = 'FA' or ProveedorFactura.tipo = 'SD', (select sum(importe) FROM orden_pago_facturas AS OrdenPagoFactura
                            WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id), (select sum(importe) FROM orden_pago_facturas AS OrdenPagoFactura
                            WHERE OrdenPagoFactura.proveedor_credito_id = ProveedorFactura.id)),0) = 0, 0, 1) as anular,

                            ProveedorFactura.comentario, ProveedorFactura.orden_descuento_cobro_id as orden_cobro, ProveedorFactura.liquidacion_id as liquidacion

                            FROM proveedor_facturas AS ProveedorFactura
                            WHERE proveedor_id = $id

                            UNION

                            SELECT	OrdenPago.id as id, OrdenPago.fecha_pago as fecha, concat('ORDEN DE PAGO NRO. : ', right(concat('00000000', OrdenPago.nro_orden_pago),8)) as concepto,
                            OrdenPago.importe as debe, 0 as haber, OrdenPago.importe as saldo, OrdenPago.id, 'OPA' as tipo, 
                            IF((SELECT COUNT(*) FROM banco_cuenta_movimientos WHERE orden_pago_id = OrdenPago.id AND banco_cuenta_saldo_id > 0) > 0, 1, 0) AS anular, OrdenPago.comentario, 
                            0 AS orden_cobro, 0 as liquidacion
                            -- 0 as anular, OrdenPago.comentario, 0 as orden_cobro, 0 as liquidacion
                            FROM	orden_pagos as OrdenPago
                            WHERE proveedor_id = '$id' and anulado = 0
                            ORDER BY fecha, tipo";
				
            $aCtaCte = $this->query($sqlCtaCte);


//			if($valor['ProveedorFactura']['orden_descuento_cobro_id'] > 0) $resultados[$clave]['ProveedorFactura']['socio'] = $this->getNombreSocio($valor['ProveedorFactura']['orden_descuento_cobro_id']);

//            $oCtaCte->delete(['ProveedorCtacte.proveedor_id' => $id]);
            $sqlCtaCte = "DELETE FROM proveedor_ctactes WHERE proveedor_id = '$id'";
            $aDelete = $this->query($sqlCtaCte);
//            exit;
            
            $ctaCte = array();
            $tmpCtaCte = array();
            $saldo = 0;
            $item = 0;
            foreach($aCtaCte as $factura){

                $socio = '';
                $factura[0]['pagos'] = 0;
                if($factura[0]['orden_cobro'] > 0) $socio = $this->getNombreSocio($factura[0]['orden_cobro']). ' ** ';
                if($factura[0]['orden_cobro'] > 0 || $factura[0]['liquidacion'] > 0) $factura[0]['anular'] = 1;
                $item += 1;
                $tmpCtaCte = array();
                $tmpCtaCte['item'] = $item;
                $tmpCtaCte['proveedor_id'] = $id;
                $tmpCtaCte['fecha'] = $factura[0]['fecha'];
                $tmpCtaCte['concepto'] = $factura[0]['concepto'];
                $tmpCtaCte['debe'] = $factura[0]['debe'];
                $tmpCtaCte['haber'] = $factura[0]['haber'];
                $tmpCtaCte['saldo']  = $factura[0]['saldo'] + $saldo;
                $tmpCtaCte['id'] = $factura[0]['id'];
                $tmpCtaCte['tipo'] = $factura[0]['tipo']; 
                $tmpCtaCte['anular'] = $factura[0]['anular'];
                $tmpCtaCte['comentario'] = $socio . $factura[0]['comentario']; 
                $tmpCtaCte['pagos'] = $factura[0]['pagos'];

                $saldo = $tmpCtaCte['saldo'];
                $oCtaCte->save($tmpCtaCte);
//                array_push($ctaCte, $tmpCtaCte);
            }
/*            
            $ctaCte = array_reverse($ctaCte);
            
            $oCtaCte->delete(['ProveedorCtacte.proveedor_id' => $id]);
            foreach($ctaCte as $dato){
                $oCtaCte->saveAll($dato);
            }
*/
		
            return true;			
	}

	
	function armaCtaCte_1($id){
            $oFacturas = $this->importarModelo('ProveedorFactura', 'proveedores');
            $oOPago = $this->importarModelo('OrdenPago', 'proveedores');
            $oPagoFacturas = $this->importarModelo('OrdenPagoFactura', 'proveedores');
            $oOPagoDetalle = $this->importarModelo('OrdenPagoDetalle', 'proveedores');
            $oCtaCte = $this->importarModelo('ProveedorCtacte', 'proveedores');

            $sqlCtaCte = "SELECT	ProveedorFactura.id as id,
                            ProveedorFactura.fecha_comprobante as fecha, 

                            concat(if(ProveedorFactura.tipo_comprobante = 'SALDOPROVEED', 'SALDO ANTERIOR', 
                            (select concepto_1 from global_datos where id = ProveedorFactura.tipo_comprobante)), ' ',
                            ProveedorFactura.letra_comprobante, '-', ProveedorFactura.punto_venta_comprobante, '-', ProveedorFactura.numero_comprobante) as concepto,

                            if(ProveedorFactura.tipo = 'NC', ProveedorFactura.total_comprobante, 0) as debe,

                            if(ProveedorFactura.tipo = 'FA', ProveedorFactura.total_comprobante, 0) as haber,

                            ProveedorFactura.total_comprobante * if(ProveedorFactura.tipo = 'SD' or ProveedorFactura.tipo='FA',-1, 1) AS saldo,

                            ProveedorFactura.id, ProveedorFactura.tipo, 

                            if(ifnull(if(ProveedorFactura.tipo = 'FA' or ProveedorFactura.tipo = 'SD', (select sum(importe) FROM orden_pago_facturas AS OrdenPagoFactura
                            WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id), (select sum(importe) FROM orden_pago_facturas AS OrdenPagoFactura
                            WHERE OrdenPagoFactura.proveedor_credito_id = ProveedorFactura.id)),0) = 0, 0, 1) as anular,

                            ProveedorFactura.comentario, ProveedorFactura.orden_descuento_cobro_id as orden_cobro, ProveedorFactura.liquidacion_id as liquidacion

                            FROM proveedor_facturas AS ProveedorFactura
                            WHERE proveedor_id = $id

                            UNION

                            SELECT	OrdenPago.id as id, OrdenPago.fecha_pago as fecha, concat('ORDEN DE PAGO NRO. : ', right(concat('00000000', OrdenPago.nro_orden_pago),8)) as concepto,
                            OrdenPago.importe as debe, 0 as haber, OrdenPago.importe as saldo, OrdenPago.id, 'OPA' as tipo, 
                            IF((SELECT COUNT(*) FROM banco_cuenta_movimientos WHERE orden_pago_id = OrdenPago.id AND banco_cuenta_saldo_id > 0) > 0, 1, 0) AS anular, OrdenPago.comentario, 
                            0 AS orden_cobro, 0 as liquidacion
                            -- 0 as anular, OrdenPago.comentario, 0 as orden_cobro, 0 as liquidacion
                            FROM	orden_pagos as OrdenPago
                            WHERE proveedor_id = '$id' and anulado = 0
                            ORDER BY fecha, tipo";
				
            $aCtaCte = $this->query($sqlCtaCte);

//			if($valor['ProveedorFactura']['orden_descuento_cobro_id'] > 0) $resultados[$clave]['ProveedorFactura']['socio'] = $this->getNombreSocio($valor['ProveedorFactura']['orden_descuento_cobro_id']);

//            $oCtaCte->delete(['ProveedorCtacte.proveedor_id' => $id]);
            $sqlCtaCte = "DELETE FROM proveedor_ctactes WHERE proveedor_id = '$id'";
            $aDelete = $this->query($sqlCtaCte);
//            exit;
            
            $ctaCte = array();
            $tmpCtaCte = array();
            $saldo = 0;
            $item = 0;
            foreach($aCtaCte as $factura){

                $socio = '';
                $factura[0]['pagos'] = 0;
                if($factura[0]['tipo'] == 'OPA'){
                    $aOPDetalle = $oOPagoDetalle->find('all',array('conditions' => array('OrdenPagoDetalle.orden_pago_id' => $factura[0]['id'], 'OrdenPagoDetalle.tipo_pago' => 'AN', 'importe >' => 0)));

                    if(!empty($aOPDetalle)){
                        $aDetalle = $oOPagoDetalle->find('all',array('conditions' => array('OrdenPagoDetalle.orden_pago_detalle_id' => $aOPDetalle[0]['OrdenPagoDetalle']['id'])));
                        if(!empty($aDetalle)){
                            $factura[0]['pagos'] = $aDetalle[0]['OrdenPagoDetalle']['importe'];
                            $factura[0]['anular'] = 1;
                        }
                    }
                }else{
                    $aProvFct = $oFacturas->getFactura($factura[0]['id']);
                    $factura[0]['pagos'] = $aProvFct['pagos'];
                    if($aProvFct['pagos'] > 0){
                        $factura[0]['anular'] = 1;
                    }
                }
                if($factura[0]['orden_cobro'] > 0) $socio = $this->getNombreSocio($factura[0]['orden_cobro']). ' ** ';
                if($factura[0]['orden_cobro'] > 0 || $factura[0]['liquidacion'] > 0) $factura[0]['anular'] = 1;
                $item += 1;
                $tmpCtaCte = array();
                $tmpCtaCte['item'] = $item;
                $tmpCtaCte['proveedor_id'] = $id;
                $tmpCtaCte['fecha'] = $factura[0]['fecha'];
                $tmpCtaCte['concepto'] = $factura[0]['concepto'];
                $tmpCtaCte['debe'] = $factura[0]['debe'];
                $tmpCtaCte['haber'] = $factura[0]['haber'];
                $tmpCtaCte['saldo']  = $factura[0]['saldo'] + $saldo;
                $tmpCtaCte['id'] = $factura[0]['id'];
                $tmpCtaCte['tipo'] = $factura[0]['tipo']; 
                $tmpCtaCte['anular'] = $factura[0]['anular'];
                $tmpCtaCte['comentario'] = $socio . $factura[0]['comentario']; 
                $tmpCtaCte['pagos'] = $factura[0]['pagos'];

                $saldo = $tmpCtaCte['saldo'];
                $oCtaCte->save($tmpCtaCte);
//                array_push($ctaCte, $tmpCtaCte);
            }
/*            
            $ctaCte = array_reverse($ctaCte);
            
            $oCtaCte->delete(['ProveedorCtacte.proveedor_id' => $id]);
            foreach($ctaCte as $dato){
                $oCtaCte->saveAll($dato);
            }
*/
		
            return true;			
	}
	
	
	function microtime_float(){
            list($usec, $sec) = explode(" ", microtime());
            return ((float)$usec + (float)$sec);
	}
	
	
	function getCobroDetalle($id, $tipo_pago){
            $this->OrdenCajaCobro = $this->importarModelo('OrdenCajaCobro', 'mutual');
            $this->CancelacionOrden = $this->importarModelo('CancelacionOrden', 'mutual');
            $this->OrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');
            $this->ProveedorFactura = $this->importarModelo('ProveedorFactura', 'proveedores');

            $aDetalleCobros = array();
            $nCobroId = 0;
            $aCobroId = array();
            $aTmpCobro = array();

            if($tipo_pago == 'AN'):
                $aCobro = $this->CancelacionOrden->find('all', array('conditions' => array('CancelacionOrden.orden_pago_id' => $id)));
                if(empty($aCobro)):
                    $aCobro = $this->OrdenCajaCobro->find('all', array('conditions' => array('OrdenCajaCobro.orden_pago_id' => $id)));
                    if(!empty($aCobro)):
                        $nCobroId = $aCobro[0]['OrdenCajaCobro']['orden_descuento_cobro_id'];
                        $nOrdenCajaCobroId = $aCobro[0]['OrdenCajaCobro']['id'];
                        array_push($aCobroId, array('orden_descuento_cobro_id' => $nCobroId, 'liquidacion_id' => 0, 'cancelacion_orden_id' => 0, 'orden_caja_cobro_id' => $aCobro[0]['OrdenCajaCobro']['id']));
                    endif;
                else:	
                    foreach($aCobro as $cobro):				
                        $nCobroId = $cobro['CancelacionOrden']['orden_descuento_cobro_id'];
                        array_push($aCobroId, array('orden_descuento_cobro_id' => $nCobroId, 'liquidacion_id' => 0, 'cancelacion_orden_id' => $cobro['CancelacionOrden']['id'], 'orden_caja_cobro_id' => 0));
                    endforeach;
                endif;
            else:
                $aCobro = $this->ProveedorFactura->find('all', array('conditions' => array('ProveedorFactura.id' => $id)));
                if($aCobro[0]['ProveedorFactura']['orden_descuento_cobro_id'] > 0):	
                    $nCobroId = $aCobro[0]['ProveedorFactura']['orden_descuento_cobro_id'];
                endif;
                array_push($aCobroId, array('orden_descuento_cobro_id' => $nCobroId, 'liquidacion_id' => $aCobro[0]['ProveedorFactura']['liquidacion_id'], 'cancelacion_orden_id' => $aCobro[0]['ProveedorFactura']['cancelacion_orden_id'], 'orden_caja_cobro_id' => $aCobro[0]['ProveedorFactura']['orden_caja_cobro_id']));
            endif;


            foreach($aCobroId as $nCobroId):
                $aTmpCobro = $this->OrdenDescuentoCobro->getCobro($nCobroId['orden_descuento_cobro_id']);
                $aTmpCobro['liquidacion_id'] = $nCobroId['liquidacion_id'];
                $aTmpCobro['cancelacion_orden_id'] = $nCobroId['cancelacion_orden_id'];
                $aTmpCobro['orden_caja_cobro_id'] = $nCobroId['orden_caja_cobro_id'];
                array_push($aDetalleCobros, $aTmpCobro);
            endforeach;
//debug($aDetalleCobros);

            return $aDetalleCobros;
	}

	
	function cargarCancelaciones($proveedor_id,$fechaDesde = null, $fechaHasta = null,$ambas = true,$cancelacionesEfectuadas = null, $cancelacionesRecibidas = null){
		
            $fechaDesde = (!empty($fechaDesde) ? $fechaDesde : date('Y-m-d'));
            $fechaHasta = (!empty($fechaHasta) ? $fechaHasta : date('Y-m-d'));

            $sql = "SELECT 
                        CancelacionOrden.id,
                        CancelacionOrden.fecha_vto,
                        Socio.id,
                        Persona.documento,
                        Persona.apellido,
                        Persona.nombre, 
                        OrdenDescuentoCancela.id,
                        OrdenDescuentoCancela.tipo_orden_dto,
                        OrdenDescuentoCancela.numero,
                        ProveedorDestino.razon_social_resumida AS destino_cancelacion,
                        OrdenDescuentoNuevo.id,
                        OrdenDescuentoNuevo.tipo_orden_dto,
                        OrdenDescuentoNuevo.numero,
                        ProveedorPagador.razon_social_resumida AS origen_fondo,
                        OrdenDescuentoCobro.id,
                        OrdenDescuentoCobro.fecha,
                        OrdenDescuentoCobro.periodo_cobro,
                        OrdenDescuentoCobro.importe,
                        CONCAT(CONCAT(CONCAT(CONCAT(Recibo.tipo_documento,' '),Recibo.sucursal),'-'),Recibo.nro_recibo) AS recibo,
                        Recibo.fecha_comprobante,
                        Recibo.importe,
                        CONCAT(CONCAT(CONCAT(CONCAT(ProveedorFacturas.tipo,' '),ProveedorFacturas.punto_venta_comprobante),'-'),ProveedorFacturas.numero_comprobante) AS nc,
                        ProveedorFacturas.fecha_comprobante,
                        ProveedorFacturas.total_comprobante,
                        IFNULL((
                            SELECT SUM(importe) 
                            FROM orden_pago_facturas AS OrdenPagoFactura,proveedor_facturas AS ProveedorFacturas
                            WHERE 
                                OrdenPagoFactura.proveedor_factura_id = ProveedorFacturas.id
                                AND ProveedorFacturas.cancelacion_orden_id = CancelacionOrden.id
                        ),0) AS importe_pagado
                        FROM cancelacion_ordenes AS CancelacionOrden
                        INNER JOIN socios AS Socio ON (Socio.id = CancelacionOrden.socio_id)
                        INNER JOIN personas AS Persona ON (Persona.id = Socio.persona_id)
                        INNER JOIN orden_descuentos AS OrdenDescuentoCancela ON (OrdenDescuentoCancela.id = CancelacionOrden.orden_descuento_id)
                        LEFT JOIN orden_descuentos AS OrdenDescuentoNuevo ON (OrdenDescuentoNuevo.id = CancelacionOrden.nueva_orden_dto_id)
                        INNER JOIN proveedores AS ProveedorDestino ON (ProveedorDestino.id = CancelacionOrden.orden_proveedor_id)
                        LEFT JOIN orden_descuento_cobros AS OrdenDescuentoCobro ON (OrdenDescuentoCobro.cancelacion_orden_id = CancelacionOrden.id)
                        LEFT JOIN proveedores AS ProveedorPagador ON (ProveedorPagador.id = OrdenDescuentoCobro.proveedor_origen_fondo_id)
                        LEFT JOIN recibos AS Recibo ON (Recibo.id = CancelacionOrden.recibo_id)
                        LEFT JOIN proveedor_facturas AS ProveedorFacturas ON (ProveedorFacturas.id = CancelacionOrden.credito_proveedor_factura_id)
                        WHERE CancelacionOrden.origen_proveedor_id = $proveedor_id 
						AND OrdenDescuentoCobro.anulado = 0
                        ".(!empty($cancelacionesEfectuadas) ? " AND CancelacionOrden.id IN (".implode(',', $cancelacionesEfectuadas).")" : "")."
                        AND CancelacionOrden.fecha_vto >= '$fechaDesde' AND CancelacionOrden.fecha_vto <= '$fechaHasta'
                        ORDER BY CancelacionOrden.fecha_vto, Persona.apellido,Persona.nombre;";
//		debug($sql);
//                exit;

            $datos = $this->query($sql);

            $cancelaciones = array();
            $cancelaciones['realizadas'] = array();

            if(!empty($datos)):

                foreach ($datos as $idx => $dato):

//				debug($dato);

                    $cancelaciones['realizadas'][$idx]['cancelacion_id'] = $dato['CancelacionOrden']['id'];
                    $cancelaciones['realizadas'][$idx]['cancelacion_vto'] = $dato['CancelacionOrden']['fecha_vto'];
                    $cancelaciones['realizadas'][$idx]['socio_id'] = $dato['Socio']['id'];
                    $cancelaciones['realizadas'][$idx]['socio'] = $dato['Persona']['documento']. " - " .$dato['Persona']['apellido'].", ".$dato['Persona']['nombre'];
                    $cancelaciones['realizadas'][$idx]['cancela_orden_dto'] = $dato['OrdenDescuentoCancela']['id'];
                    $cancelaciones['realizadas'][$idx]['cancela_expediente'] = $dato['OrdenDescuentoCancela']['tipo_orden_dto']." #".$dato['OrdenDescuentoCancela']['numero'];
                    $cancelaciones['realizadas'][$idx]['cancela_comercio'] = $dato['ProveedorDestino']['destino_cancelacion'];
                    $cancelaciones['realizadas'][$idx]['nueva_orden_dto'] = $dato['OrdenDescuentoNuevo']['id'];
                    if(!empty($dato['OrdenDescuentoNuevo']['id'])){
                        $cancelaciones['realizadas'][$idx]['nuevo_expediente'] = $dato['OrdenDescuentoNuevo']['tipo_orden_dto']." #".$dato['OrdenDescuentoNuevo']['numero'];
                    }else{
                        $cancelaciones['realizadas'][$idx]['nuevo_expediente'] = "";
                    }
                    $cancelaciones['realizadas'][$idx]['origen_fondo'] = $dato['ProveedorPagador']['origen_fondo'];
                    $cancelaciones['realizadas'][$idx]['socio_orden_cobro_id'] = $dato['OrdenDescuentoCobro']['id'];
                    $cancelaciones['realizadas'][$idx]['socio_orden_cobro_fecha'] = $dato['OrdenDescuentoCobro']['fecha'];
                    $cancelaciones['realizadas'][$idx]['socio_orden_cobro_importe'] = $dato['OrdenDescuentoCobro']['importe'];
                    $cancelaciones['realizadas'][$idx]['comprobante_numero'] = (!empty($dato[0]['nc']) ? $dato[0]['nc'] : $dato[0]['recibo']);
                    $cancelaciones['realizadas'][$idx]['comprobante_fecha'] = (!empty($dato[0]['nc']) ? $dato['ProveedorFacturas']['fecha_comprobante'] : $dato['Recibo']['fecha_comprobante']);
                    $cancelaciones['realizadas'][$idx]['comprobante_importe'] = (!empty($dato[0]['nc']) ? $dato['ProveedorFacturas']['total_comprobante'] : $dato['Recibo']['importe']);
                    if(!empty($cancelaciones['realizadas'][$idx]['comprobante_numero'])):
                        $cancelaciones['realizadas'][$idx]['importe_retenido'] = $cancelaciones['realizadas'][$idx]['socio_orden_cobro_importe'];
                        if($cancelaciones['realizadas'][$idx]['socio_orden_cobro_importe'] != 0)$cancelaciones['realizadas'][$idx]['saldo'] = $dato['OrdenDescuentoCobro']['importe'] - $cancelaciones['realizadas'][$idx]['comprobante_importe'];
                        else $cancelaciones['realizadas'][$idx]['saldo'] = 0;
                    else:
                        $cancelaciones['realizadas'][$idx]['importe_retenido'] = $cancelaciones['realizadas'][$idx]['saldo'] = 0;
                    endif;
                    $cancelaciones['realizadas'][$idx]['saldo'] = 0;

                endforeach;

            endif;

            if(!$ambas && empty($cancelacionesEfectuadas)){
                $cancelaciones['realizadas'] = array();
            }

            #CANCELACIONES RECIBIDAS

            $sql = "SELECT 
                            CancelacionOrden.id,
                            CancelacionOrden.fecha_vto,
                            Socio.id,
                            Persona.documento,
                            Persona.apellido,
                            Persona.nombre, 
                            OrdenDescuentoCancela.id,
                            OrdenDescuentoCancela.tipo_orden_dto,
                            OrdenDescuentoCancela.numero,
                            ProveedorDestino.razon_social_resumida AS destino_cancelacion,
                            OrdenDescuentoNuevo.id,
                            OrdenDescuentoNuevo.tipo_orden_dto,
                            OrdenDescuentoNuevo.numero,
                            ProveedorOrigen.razon_social_resumida AS origen_cancelacion,
                            ProveedorPagador.razon_social_resumida AS origen_fondo,
                            OrdenDescuentoCobro.id,
                            OrdenDescuentoCobro.fecha,
                            OrdenDescuentoCobro.periodo_cobro,
                            OrdenDescuentoCobro.importe,
                            CONCAT(CONCAT(CONCAT(CONCAT(Recibo.tipo_documento,' '),Recibo.sucursal),'-'),Recibo.nro_recibo) AS recibo,
                            Recibo.fecha_comprobante,
                            Recibo.importe,
                            CONCAT(CONCAT(CONCAT(CONCAT(ProveedorFacturas.tipo,' '),ProveedorFacturas.punto_venta_comprobante),'-'),ProveedorFacturas.numero_comprobante) AS nc,
                            ProveedorFacturas.fecha_comprobante,
                            ProveedorFacturas.total_comprobante,
                            IFNULL((
                                    SELECT SUM(importe) 
                                    FROM orden_pago_facturas AS OrdenPagoFactura,proveedor_facturas AS ProveedorFacturas
                                    WHERE 
                                            OrdenPagoFactura.proveedor_factura_id = ProveedorFacturas.id
                                            AND ProveedorFacturas.cancelacion_orden_id = CancelacionOrden.id
                            ),0) AS importe_pagado
                            FROM cancelacion_ordenes AS CancelacionOrden
                            INNER JOIN socios AS Socio ON (Socio.id = CancelacionOrden.socio_id)
                            INNER JOIN personas AS Persona ON (Persona.id = Socio.persona_id)
                            INNER JOIN orden_descuentos AS OrdenDescuentoCancela ON (OrdenDescuentoCancela.id = CancelacionOrden.orden_descuento_id)
                            LEFT JOIN orden_descuentos AS OrdenDescuentoNuevo ON (OrdenDescuentoNuevo.id = CancelacionOrden.nueva_orden_dto_id)
                            INNER JOIN proveedores AS ProveedorDestino ON (ProveedorDestino.id = CancelacionOrden.orden_proveedor_id)
                            INNER JOIN proveedores AS ProveedorOrigen ON (ProveedorOrigen.id = CancelacionOrden.origen_proveedor_id)
                            INNER JOIN orden_descuento_cobros AS OrdenDescuentoCobro ON (OrdenDescuentoCobro.cancelacion_orden_id = CancelacionOrden.id)
                            INNER JOIN proveedores AS ProveedorPagador ON (ProveedorPagador.id = OrdenDescuentoCobro.proveedor_origen_fondo_id)
                            LEFT JOIN recibos AS Recibo ON (Recibo.id = CancelacionOrden.recibo_id)
                            LEFT JOIN proveedor_facturas AS ProveedorFacturas ON (ProveedorFacturas.id = CancelacionOrden.credito_proveedor_factura_id)
                            WHERE CancelacionOrden.orden_proveedor_id = $proveedor_id
                            ".(!empty($cancelacionesRecibidas) ? " AND CancelacionOrden.id IN (".implode(',', $cancelacionesRecibidas).")" : "")."
                            AND CancelacionOrden.fecha_vto >= '$fechaDesde' AND CancelacionOrden.fecha_vto <= '$fechaHasta'
                            ORDER BY CancelacionOrden.fecha_vto, Persona.apellido,Persona.nombre;";

            $datos = $this->query($sql);

            $cancelaciones['recibidas'] = array();

            if(!empty($datos)):

                    foreach ($datos as $idx => $dato):

//				debug($dato);

                            $cancelaciones['recibidas'][$idx]['cancelacion_id'] = $dato['CancelacionOrden']['id'];
                            $cancelaciones['recibidas'][$idx]['cancelacion_vto'] = $dato['CancelacionOrden']['fecha_vto'];
                            $cancelaciones['recibidas'][$idx]['socio_id'] = $dato['Socio']['id'];
                            $cancelaciones['recibidas'][$idx]['socio'] = $dato['Persona']['documento']. " - " .$dato['Persona']['apellido'].", ".$dato['Persona']['nombre'];
                            $cancelaciones['recibidas'][$idx]['cancela_orden_dto'] = $dato['OrdenDescuentoCancela']['id'];
                            $cancelaciones['recibidas'][$idx]['cancela_expediente'] = $dato['OrdenDescuentoCancela']['tipo_orden_dto']." #".$dato['OrdenDescuentoCancela']['numero'];
                            $cancelaciones['recibidas'][$idx]['cancela_comercio'] = $dato['ProveedorDestino']['destino_cancelacion'];
                            $cancelaciones['recibidas'][$idx]['nueva_orden_dto'] = $dato['OrdenDescuentoNuevo']['id'];
                            if(!empty($dato['OrdenDescuentoNuevo']['id'])){
                                    $cancelaciones['recibidas'][$idx]['nuevo_expediente'] = $dato['OrdenDescuentoNuevo']['tipo_orden_dto']." #".$dato['OrdenDescuentoNuevo']['numero'];
                                    $cancelaciones['recibidas'][$idx]['nuevo_expediente_proveedor'] = $dato['ProveedorOrigen']['origen_cancelacion'];
                            }else{
                                    $cancelaciones['recibidas'][$idx]['nuevo_expediente'] = "";
                                    $cancelaciones['recibidas'][$idx]['nuevo_expediente_proveedor'] = "";
                            }

                            $cancelaciones['recibidas'][$idx]['origen_fondo'] = $dato['ProveedorPagador']['origen_fondo'];
                            $cancelaciones['recibidas'][$idx]['socio_orden_cobro_id'] = $dato['OrdenDescuentoCobro']['id'];
                            $cancelaciones['recibidas'][$idx]['socio_orden_cobro_fecha'] = $dato['OrdenDescuentoCobro']['fecha'];
                            $cancelaciones['recibidas'][$idx]['socio_orden_cobro_importe'] = $dato['OrdenDescuentoCobro']['importe'];
                            $cancelaciones['recibidas'][$idx]['comprobante_numero'] = (!empty($dato[0]['nc']) ? $dato[0]['nc'] : $dato[0]['recibo']);
                            $cancelaciones['recibidas'][$idx]['comprobante_fecha'] = (!empty($dato[0]['nc']) ? $dato['ProveedorFacturas']['fecha_comprobante'] : $dato['Recibo']['fecha_comprobante']);
                            $cancelaciones['recibidas'][$idx]['comprobante_importe'] = (!empty($dato[0]['nc']) ? $dato['ProveedorFacturas']['total_comprobante'] : $dato['Recibo']['importe']);
                            $cancelaciones['recibidas'][$idx]['importe_pagado'] = $dato[0]['importe_pagado'];
                            $cancelaciones['recibidas'][$idx]['saldo'] = $dato['OrdenDescuentoCobro']['socio_orden_cobro_importe'] - $cancelaciones['realizadas'][$idx]['importe_pagado'];

                    endforeach;

            endif;		

            if(!$ambas && empty($cancelacionesRecibidas)){
                    $cancelaciones['recibidas'] = array();
            }		

            return $cancelaciones;
		
	}
	
	
	function guardarCompensarPago($datos){

		// Orden de Pago Facturas
		$oOrdenPagoFactura = $this->importarModelo('OrdenPagoFactura', 'proveedores');

		if(isset($datos['Movimiento']['fac']) && isset($datos['Movimiento']['ant'])):
			$facturas = array();
			foreach($datos['Movimiento']['fac']['check'] as $id => $importe):
				$tmpFactura = array();
				$tmpFactura['id'] = 0;
				$tmpFactura['proveedor_id'] = $datos['Movimiento']['proveedor_id'];
				$tmpFactura['proveedor_factura_id'] = $datos['Movimiento']['fac']['id'][$id];
				$tmpFactura['fecha'] = $datos['Movimiento']['fpago'];
				$tmpFactura['orden_pago_id'] = 0;
				$tmpFactura['tipo_pago'] = 'FA';
				$tmpFactura['proveedor_credito_id'] = 0;
				$tmpFactura['orden_pago_detalle_id'] = 0;
				$tmpFactura['importe'] = $datos['Movimiento']['fac']['importe_a_pagar'][$id];
				$tmpFactura['saldo'] = $datos['Movimiento']['fac']['importe_a_pagar'][$id];
				array_push($facturas, $tmpFactura);
			endforeach;
			
			$anticipos = array();
			foreach($datos['Movimiento']['ant']['check'] as $id => $importe):
				$tmpAnticipo = array();
				$tmpAnticipo['id'] = 0;
				$tmpAnticipo['proveedor_id'] = $datos['Movimiento']['proveedor_id'];
				$tmpAnticipo['proveedor_factura_id'] = 0;
				$tmpFactura['fecha'] = $datos['Movimiento']['fpago'];
				$tmpFactura['orden_pago_id'] = 0;
				$tmpAnticipo['tipo_pago'] = 'AN';
				$tmpAnticipo['proveedor_credito_id'] = 0;
				$tmpAnticipo['orden_pago_detalle_id'] = 0;
				$tmpAnticipo['importe'] = $datos['Movimiento']['ant']['importe_a_pagar'][$id] * (-1);
				$tmpAnticipo['saldo'] = $datos['Movimiento']['ant']['importe_a_pagar'][$id] * (-1);
				if($datos['Movimiento']['ant']['tipo'][$id] == 'AN'):
					$tmpAnticipo['orden_pago_detalle_id'] = $datos['Movimiento']['ant']['id'][$id];
				else:
					$tmpAnticipo['tipo_pago'] = 'NC';
					$tmpAnticipo['proveedor_factura_id'] = $datos['Movimiento']['ant']['id'][$id];
					$tmpAnticipo['proveedor_credito_id'] = $datos['Movimiento']['ant']['id'][$id];
				endif;
				array_push($anticipos, $tmpAnticipo);
			endforeach;

				
			$totalFactura = 0;
			$tmpFacturaAnticipo = array();
			$aFacturaAnticipo = array();
			foreach($anticipos as $claveA => $valorA){
				$saldos = round($anticipos[$claveA]['importe'],2);
				foreach($facturas as $claveF => $valorF){
					if($saldos > 0.00):
						if(round($facturas[$claveF]['importe'],2) > 0.00):
							$tmpFacturaAnticipo['proveedor_id'] = $valorF['proveedor_id'];
							$tmpFacturaAnticipo['socio_id'] = (isset($valorF['socio_id']) ? $valorF['socio_id'] : 0);
							$tmpFacturaAnticipo['fecha'] = $valorF['fecha'];
							$tmpFacturaAnticipo['orden_pago_id'] = 0;
							$tmpFacturaAnticipo['proveedor_factura_id'] = $valorF['proveedor_factura_id'];
							$tmpFacturaAnticipo['proveedor_credito_id'] = $anticipos[$claveA]['proveedor_credito_id'];
							$tmpFacturaAnticipo['orden_pago_detalle_id'] = $anticipos[$claveA]['orden_pago_detalle_id'];
							if($facturas[$claveF]['importe'] >= $saldos):
								$tmpFacturaAnticipo['importe'] = $saldos;
								$facturas[$claveF]['importe'] -= $saldos;
								$anticipos[$claveA]['importe'] = 0.00;
								$saldos = 0.00;
							else:
								$tmpFacturaAnticipo['importe'] = $facturas[$claveF]['importe'];
								$anticipos[$claveA]['importe'] -= $facturas[$claveF]['importe'];
								$saldos -= $facturas[$claveF]['importe'];
								$facturas[$claveF]['importe'] = 0.00;
							endif;
							array_push($aFacturaAnticipo, $tmpFacturaAnticipo);
						endif;
					endif;
				}
			}
	

			
			$aFacturas = array();
			foreach($facturas as $claveF => $valorF){
				if($valorF['importe'] > 0):
					$totalFactura += $valorF['importe'];
					array_push($aFacturas, $valorF);
				endif;
			}
		
			if(!$oOrdenPagoFactura->saveAll($aFacturaAnticipo)):		
				return false;
			endif;
			
			return true;
			
//debug($aFacturas);
//debug($aFacturaAnticipo);
//exit;

		endif;
		
		return false;
	}
	

	
	function armaCtaCteOperativo($id){
		$sqlCtaCte = "SELECT
                                NOW() AS fecha, CONCAT('LIQUIDACION # ', l.id) AS concepto, 0.00 AS debe,  SUM(c.importe_debitado) AS haber, SUM(c.importe_debitado) * -1 AS saldo,
                                l.id, 'FA' AS tipo, 0 AS anular, CONCAT('PERIODO: ', RIGHT(l.periodo,2),'/', LEFT(l.periodo,4), ' - ', g.concepto_1) AS comentario, 0 AS orden_cobro
                                FROM	liquidacion_cuotas c, liquidaciones l, global_datos AS g
                                WHERE	c.liquidacion_id = l.id AND l.facturada = 0 AND c.proveedor_id = '$id' AND l.codigo_organismo = g.id
                                GROUP	BY c.liquidacion_id
                                HAVING	SUM(c.importe_debitado) > 0

                                UNION

                                SELECT	NOW() AS fecha, CONCAT('LIQUIDACION # ', l.id) AS concepto, SUM(c.comision_cobranza) AS debe,  0.00 AS haber, SUM(c.comision_cobranza) AS saldo,
                                l.id, 'OP' AS tipo, 0 AS anular, CONCAT('PERIODO: ', RIGHT(l.periodo,2),'/', LEFT(l.periodo,4), ' - ', g.concepto_1) AS comentario, 0 AS orden_cobro
                                FROM	liquidacion_cuotas c, liquidaciones l, global_datos AS g
                                WHERE	c.liquidacion_id = l.id AND l.facturada = 0 AND c.proveedor_id = '$id' AND l.codigo_organismo = g.id
                                GROUP	BY c.liquidacion_id
                                HAVING	SUM(c.comision_cobranza) > 0

                                UNION

                                SELECT	
			 	ProveedorFactura.fecha_comprobante as fecha, 
			
				concat(if(ProveedorFactura.tipo_comprobante = 'SALDOPROVEED', 'SALDO ANTERIOR', 
				(select concepto_1 from global_datos where id = ProveedorFactura.tipo_comprobante)), ' ',
				ProveedorFactura.letra_comprobante, '-', ProveedorFactura.punto_venta_comprobante, '-', ProveedorFactura.numero_comprobante) as concepto,
			
				if(ProveedorFactura.tipo = 'NC', ProveedorFactura.total_comprobante, 0) as debe,
			
				if(ProveedorFactura.tipo = 'FA', ProveedorFactura.total_comprobante, 0) as haber,
			
			 	ProveedorFactura.total_comprobante * if(ProveedorFactura.tipo = 'SD' or ProveedorFactura.tipo='FA',-1, 1) AS saldo,
			
				ProveedorFactura.id, ProveedorFactura.tipo, 
			 	
				if(ifnull(if(ProveedorFactura.tipo = 'FA' or ProveedorFactura.tipo = 'SD', (select sum(importe) FROM orden_pago_facturas AS OrdenPagoFactura
 				WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id), (select sum(importe) FROM orden_pago_facturas AS OrdenPagoFactura
 				WHERE OrdenPagoFactura.proveedor_credito_id = ProveedorFactura.id)),0) = 0, 0, 1) as anular,

				ProveedorFactura.comentario, ProveedorFactura.orden_descuento_cobro_id as orden_cobro
			
				FROM proveedor_facturas AS ProveedorFactura
				WHERE proveedor_id = '$id'
			
				UNION
			
				SELECT	OrdenPago.fecha_pago as fecha, concat('ORDEN DE PAGO NRO. : ', right(concat('00000000', OrdenPago.nro_orden_pago),8)) as concepto,
				OrdenPago.importe as debe, 0 as haber, OrdenPago.importe as saldo, OrdenPago.id, 'OPA' as tipo, 
                        	IF((SELECT COUNT(*) FROM banco_cuenta_movimientos WHERE orden_pago_id = OrdenPago.id AND banco_cuenta_saldo_id > 0) > 0, 1, 0) AS anular, OrdenPago.comentario, 0 AS orden_cobro
                                -- 0 as anular, OrdenPago.comentario, 0 as orden_cobro
				FROM	orden_pagos as OrdenPago
				WHERE proveedor_id = '$id' and anulado = 0

                                UNION

                                SELECT	
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

                                ClienteFactura.comentario, ClienteFactura.orden_descuento_cobro_id AS orden_cobro

                                FROM cliente_facturas AS ClienteFactura, proveedores AS Proveedor
                                WHERE Proveedor.id = '$id' AND ClienteFactura.cliente_id = Proveedor.cliente_id AND ClienteFactura.anulado = 0

                                UNION

                                SELECT	Recibo.fecha_comprobante AS fecha, CONCAT('RECIBO NRO.: ', Recibo.letra, '-', Recibo.sucursal, '-', Recibo.nro_recibo) AS concepto,
                                0 AS debe, Recibo.importe AS haber, Recibo.importe * -1 AS saldo, Recibo.id, 'REC' AS tipo, 0 AS anular, Recibo.comentarios, 0 AS orden_cobro
                                FROM	recibos AS Recibo, proveedores AS Proveedor
                                WHERE	Proveedor.id = '$id' AND Recibo.cliente_id = Proveedor.cliente_id AND Recibo.anulado = 0 AND Recibo.cliente_id > 0
                                                                ORDER BY fecha, tipo
                        ";
				
		$aCtaCte = $this->query($sqlCtaCte);
				
//			if($valor['ProveedorFactura']['orden_descuento_cobro_id'] > 0) $resultados[$clave]['ProveedorFactura']['socio'] = $this->getNombreSocio($valor['ProveedorFactura']['orden_descuento_cobro_id']);

		$ctaCte = array();
		$tmpCtaCte = array();
		$saldo = 0;
		foreach($aCtaCte as $factura){
			$socio = '';
			if($factura[0]['orden_cobro'] > 0) $socio = $this->getNombreSocio($factura[0]['orden_cobro']). ' ** ';
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
	

	
	function saldoOperativo($id){
            $sqlOperativo = "SELECT	
                            (
                                SELECT IFNULL(SUM(c.importe_debitado * -1),0.00)
                                FROM	liquidacion_cuotas c, liquidaciones l, global_datos AS g
                                WHERE	c.liquidacion_id = l.id AND l.facturada = 0 AND c.proveedor_id = p.id AND l.codigo_organismo = g.id
                            ) +
                            (
                                SELECT	IFNULL(SUM(c.comision_cobranza), 0.00)
                                FROM	liquidacion_cuotas c, liquidaciones l, global_datos AS g
                                WHERE	c.liquidacion_id = l.id AND l.facturada = 0 AND c.proveedor_id = p.id AND l.codigo_organismo = g.id
                            ) +
                            (
                                SELECT	IFNULL(SUM(ProveedorFactura.total_comprobante * IF(ProveedorFactura.tipo = 'SD' OR ProveedorFactura.tipo='FA',-1, 1)), 0.00)
                                FROM proveedor_facturas AS ProveedorFactura
                                WHERE proveedor_id = p.id
                            ) +
                            (
                                SELECT	IFNULL(SUM(OrdenPago.importe), 0.00)
                                FROM	orden_pagos AS OrdenPago
                                WHERE proveedor_id = p.id AND anulado = 0
                            ) +
                            (
                                SELECT	IFNULL(SUM(ClienteFactura.total_comprobante * IF(ClienteFactura.tipo = 'SD' OR ClienteFactura.tipo='FA' OR ClienteFactura.tipo = 'ND',1, -1)), 0.00)
                                FROM cliente_facturas AS ClienteFactura, proveedores AS Proveedor
                                WHERE Proveedor.id = p.id AND ClienteFactura.cliente_id = Proveedor.cliente_id AND ClienteFactura.anulado = 0
                            ) +
                            (
                                SELECT	IFNULL(SUM(Recibo.importe * -1), 0.00)
                                FROM	recibos AS Recibo, proveedores AS Proveedor
                                WHERE	Proveedor.id = p.id AND Recibo.cliente_id = Proveedor.cliente_id AND Recibo.anulado = 0 AND Recibo.cliente_id > 0
                            ) AS importe

                            FROM	proveedores p 
                            WHERE	p.id = '$id'

                    ";
            
            $aSaldoOperativo = $this->query($sqlOperativo);
            
            return $aSaldoOperativo;

                
        }
	
	
}
?>
