<?php
class Cliente extends ClientesAppModel{
	var $name = 'Cliente';
	
	function grabar($datos){
		$aFactura = array();

		$aFactura['id'] = 0;
		$aFactura['tipo'] = $datos['Cliente']['tipo_saldo'] == 0 ? 'SD' : 'SA';
		$aFactura['fecha_comprobante'] = $datos['Cliente']['fecha_saldo'];
		$aFactura['vencimiento1'] = $datos['Cliente']['fecha_saldo'];
		$aFactura['tipo_comprobante'] = 'SALDOCLIENTE';
		$aFactura['importe_no_gravado'] = $datos['Cliente']['importe_saldo']; 
		$aFactura['total_comprobante'] = $datos['Cliente']['importe_saldo']; 
		$aFactura['importe_venc1'] = $datos['Cliente']['importe_saldo']; 
		$aFactura['estado'] = 'A'; 
			
		if($datos['Cliente']['cliente_factura_id'] != 0) $aFactura['id'] = $datos['Cliente']['cliente_factura_id'];

		$this->begin();
		if($this->save($datos)):
			if(!isset($datos['Cliente']['id'])):
				$id = $this->getLastInsertID();
			else:
				$id = $datos['Cliente']['id'];
			endif;
				
			if($datos['Cliente']['proveedor_id'] != 0):
				$oProveedor = $this->importarModelo('Proveedor', 'Proveedores');
				$aProveedor = array('id' => $datos['Cliente']['proveedor_id'], 'cliente_id' => $id);
				if(!$oProveedor->save($aProveedor)):
					$this->rollback();
					return 0;
				endif;
			endif;
			
			if($datos['Cliente']['importe_saldo'] != 0):
				$datos['Cliente']['id'] = $id;
				$aFactura['cliente_id'] = $id;
				$oFactura = $this->importarModelo('ClienteFactura', 'Clientes');
	    		if($oFactura->save($aFactura)):
	    			$datos['Cliente']['cliente_factura_id'] = ($aFactura['id'] != 0 ? $aFactura['id'] : $oFactura->getLastInsertID());
					if($this->save($datos)):
						$this->commit();
						return $id;
					else:
						$this->rollback();
						return 0;
					endif;
				else:
					$this->rollback();
					return 0;
				endif;
			else:
				$this->commit();
				return $id;
			endif;
	    else:
			$this->rollback();
			return 0;
		endif;
		
	}
	
	
	function getEstado($datos){
		if(!empty($datos['Cliente']['cliente_factura_id'])):
    		$oFactura = $this->importarModelo('ClienteFactura', 'Clientes');
    		$estado = ($oFactura->tieneCobro($datos['Cliente']['cliente_factura_id']) ? 'P' : 'A');
    	else:
    		$estado = 'A';
    	endif;
    	
    	return $estado;
		
	}

    
	function getCliente($id){
    	
//    	$this->unbindModel(array('hasMany' => array('MutualProducto')));
    	$cliente = $this->read(null,$id);
		$cliente = $this->__armaDatos($cliente);
    	return $cliente;
    }    
    
	
    function getRazonSocial($id){
    	$cliente = $this->getCliente($id);

    	return $cliente['Cliente']['razon_social'];
    }
    
    
    function getCuit($id){
    	$cliente = $this->getCliente($id);

		return $cliente['Cliente']['formato_cuit'];
    }
    
    
    function getFormatoCuit($id){
    	$cliente = $this->getCliente($id);

		if(strlen($cliente['Cliente']['cuit']) == 11) $cliente['Cliente']['formato_cuit'] = substr($cliente['Cliente']['cuit'],0,2) . '-' . substr($cliente['Cliente']['cuit'], 2,8) . '-' . substr($cliente['Cliente']['cuit'], 10);
		else $cliente['Cliente']['formato_cuit'] = $cliente['Cliente']['cuit'];
    	
		return $cliente['Cliente']['formato_cuit'];
    }
    
    
    function getDomicilio($cliente){
		$domicilio = ltrim(rtrim($cliente['Cliente']['calle'])) . " " .($cliente['Cliente']['numero_calle'] != 0 ? $cliente['Cliente']['numero_calle'] : '');
		if(!empty($cliente['Cliente']['piso'])) $domicilio .= ' Piso ' . ltrim(rtrim($cliente['Cliente']['piso'])); 
		if(!empty($cliente['Cliente']['dpto'])) $domicilio .= ' Dpto ' . ltrim(rtrim($cliente['Cliente']['dpto']));
		$domicilio .= ' - ' . ltrim(rtrim($cliente['Cliente']['barrio']));
		$domicilio .= ' - ' . ltrim(rtrim($cliente['Cliente']['localidad'])) . ' (CP: ' . $cliente['Cliente']['codigo_postal'] . ')'; 
		return $domicilio;
	}
	

	function __armaDatos($cliente){
		if(isset($cliente['Cliente'])){
			$cliente['Cliente']['domicilio'] = $this->getDomicilio($cliente);
			
			if(strlen($cliente['Cliente']['cuit']) == 11) $cliente['Cliente']['formato_cuit'] = substr($cliente['Cliente']['cuit'],0,2) . '-' . substr($cliente['Cliente']['cuit'], 2,8) . '-' . substr($cliente['Cliente']['cuit'], 10);
			else $cliente['Cliente']['formato_cuit'] = $cliente['Cliente']['cuit'];
			
			$glb = $this->getGlobalDato('concepto_1',$cliente['Cliente']['condicion_iva']);
			$cliente['Cliente']['iva_concepto'] = $glb['GlobalDato']['concepto_1'];
			
		}
		return $cliente;
	}
	

	function facturasPendientes($cliente_id=null){
		if($cliente_id==null) return false;
		
		$cobroCuenta = $this->cobroCuenta($cliente_id);
		$facturas = $this->traerFacturas($cliente_id);
		$facturas = $this->datosAdicionales($facturas, true);

		$compSaldo = array();
		foreach($cobroCuenta as $cobro){
			if($cobro['saldo'] != 0):
				array_push($compSaldo, $cobro);
			endif;
		}

		foreach($facturas as $cobro){
			if($cobro['saldo'] != 0):
				array_push($compSaldo, $cobro);
			endif;
		}
		
		return $compSaldo;
	}
	
	function cobroCuenta($cliente_id){
		$oCobroCuenta = $this->importarModelo('ReciboDetalle', 'Clientes');
		$cobroCuenta = $oCobroCuenta->getCobroDetalleCuenta($cliente_id);
		
		if(empty($cobroCuenta)) return array();

		$oCobroFacturas = $this->importarModelo('ReciboFactura', 'Clientes');
		
		foreach($cobroCuenta as $clave => $valor){
			$cobros = $oCobroFacturas->getCobroCuenta($cobroCuenta[$clave]['id']);
			$resto = $cobros;

			$cobroCuenta[$clave]['tipo_comprobante_desc'] = 'Pago a Cuenta';
			$cobroCuenta[$clave]['tipo'] = 'AN';
			$cobroCuenta[$clave]['fecha_comprobante'] = '  /  /  ';
			$cobroCuenta[$clave]['total_comprobante'] = $cobroCuenta[$clave]['importe'] * (-1);
			$cobroCuenta[$clave]['cuota'] = '';
			$cobroCuenta[$clave]['vencimiento'] = '  /  /  ';
			$cobroCuenta[$clave]['saldo'] = $cobros - $cobroCuenta[$clave]['importe'];
			$cobroCuenta[$clave]['cobro'] = $cobros;
			$cobroCuenta[$clave]['saldo_comprobante'] = $cobros - $cobroCuenta[$clave]['importe'];
			$cobroCuenta[$clave]['cobro_comprobante'] = $cobros;
			$cobroCuenta[$clave]['signo'] = '-';
		}
		
		return $cobroCuenta;
					
	}
	
	
	function armaCtaCte($cliente_id){
		$facturas = $this->traerFacturas($cliente_id);
		$facturas = $this->datosAdicionales($facturas, false);
		$recibos = $this->traerRecibos($cliente_id);

    	$oTipoDocumento = $this->importarModelo('TipoDocumento', 'config');
		
		$ctaCte = array();
		$tmpCtaCte = array();
		foreach($facturas as $factura){
			$tmpCtaCte['fecha'] = $factura['fecha_comprobante'];
			$tmpCtaCte['concepto'] = $factura['tipo_comprobante_desc'];
			$tmpCtaCte['debe'] = 0;
			$tmpCtaCte['haber'] = 0;
			$tmpCtaCte['saldo'] = 0;
			$tmpCtaCte['id'] = $factura['id'];
			$tmpCtaCte['tipo'] =$factura['tipo']; 
			$tmpCtaCte['anular'] = ($factura['cobro_comprobante'] > 0 ? 1 : 0);
			$tmpCtaCte['comentario'] = $factura['comentario'];
			if($factura['tipo'] == 'FA'){ $tmpCtaCte['debe']  = round($factura['total_comprobante'],2); $tmpCtaCte['saldo'] = round($factura['total_comprobante'],2);}
			if($factura['tipo'] == 'NC'){ $tmpCtaCte['haber'] = round($factura['total_comprobante'],2); $tmpCtaCte['saldo'] = round($factura['total_comprobante'] * (-1),2);}
			if($factura['tipo'] == 'SD'){ $tmpCtaCte['saldo'] = round($factura['total_comprobante'],2); $tmpCtaCte['anular'] = 1;} 
			if($factura['tipo'] == 'SA'){ $tmpCtaCte['saldo'] = round($factura['total_comprobante'] * (-1),2); $tmpCtaCte['anular'] = 1;} 
			array_push($ctaCte, $tmpCtaCte);
		}
		
		
		foreach($recibos as $recibo){
			$tmpCtaCte['fecha'] = $recibo['fecha_comprobante'];
//			$tmpCtaCte['concepto'] = ($recibo['tipo_documento'] == 'RCI' ? 'RECIBO INT.NRO. : ' : 'RECIBO INT.NRO. : ') . str_pad($recibo['nro_recibo'],8,'0',STR_PAD_LEFT);
			$tmpCtaCte['concepto'] = trim($oTipoDocumento->getDocumentoDescripcion($recibo['tipo_documento'])) . ' NRO.: ' . str_pad($recibo['nro_recibo'],8,'0',STR_PAD_LEFT);
			$tmpCtaCte['debe'] = 0;
			$tmpCtaCte['haber'] = round($recibo['importe'],2);
			$tmpCtaCte['saldo'] = round($recibo['importe'] * (-1),2);
			$tmpCtaCte['id'] = $recibo['id'];
//			$tmpCtaCte['tipo_documento'] = $recibo['tipo_documento'];
			$tmpCtaCte['tipo'] = 'REC';
			$tmpCtaCte['anular'] = 0;
			$tmpCtaCte['comentario'] = $recibo['comentarios'];
			array_push($ctaCte, $tmpCtaCte);
		}
		
		asort($ctaCte);
		$saldo = 0;
		foreach($ctaCte as $clave => $valor){
			$ctaCte[$clave]['saldo'] = round($ctaCte[$clave]['saldo'] + $saldo,2);
			$saldo = $ctaCte[$clave]['saldo'];
		}
		return $ctaCte;
	}
	
	
	function traerFacturas($cliente_id){
		$oFacturas = $this->importarModelo('ClienteFactura', 'Clientes');

		$return = $oFacturas->find('all',array(
							'conditions' => array('ClienteFactura.cliente_id' => $cliente_id, 'ClienteFactura.anulado' => 0)
		));

		$return = Set::extract("{n}.ClienteFactura",$return);
		return $return;
		
	}
	
	
	function traerRecibos($cliente_id){

		$oRecibos = $this->importarModelo('Recibo', 'Clientes');
		$return = $oRecibos->find('all',array(
							'conditions' => array('Recibo.cliente_id' => $cliente_id, 'Recibo.anulado' => 0)
		));

		$return = Set::extract("{n}.Recibo",$return);
		return $return;
		
	}
	
		
	function datosAdicionales($resultados, $detalleCuota=false){
		foreach($resultados as $clave => $valor){
			
			if($valor['tipo_comprobante'] == 'FAC'):
				if($valor['tipo'] == 'FA') $resultados[$clave]['tipo_comprobante_desc'] = 'FACTURA';
				if($valor['tipo'] == 'NC') $resultados[$clave]['tipo_comprobante_desc'] = 'NOTA CREDITO';
				if($valor['tipo'] == 'ND') $resultados[$clave]['tipo_comprobante_desc'] = 'NOTA DEBITO';
			endif;
			
			if($valor['tipo_comprobante'] == 'SALDOCLIENTE') $resultados[$clave]['tipo_comprobante_desc'] = 'SALDO ANTERIOR';
			else $resultados[$clave]['tipo_comprobante_desc'] .= ' ' . $valor['letra_comprobante'] . ' ' . $valor['punto_venta_comprobante'] . '-' . $valor['numero_comprobante'];
			
			$resultados[$clave] = $this->armaSaldo($resultados[$clave]);
		}

		if($detalleCuota) $resultados = $this->detalleCuota($resultados);
		
		return $resultados;
	}
	
	function armaSaldo($facturas){

		$oReciboFacturas = $this->importarModelo('ReciboFactura', 'Clientes');
		
		if($facturas['tipo'] == 'SD' || $facturas['tipo'] == 'FA'):
			$cobros = $oReciboFacturas->getCobroFactura($facturas['id']);
			$resto = $cobros;
			$facturas['saldo'] = $facturas['total_comprobante'] - $cobros;
			for ($i = 1; $i <= 10; $i++) {
				if(!empty($facturas["importe_venc$i"])):
					if($facturas["importe_venc$i"] <= $resto):
						$facturas["saldo$i"] = 0.00;
    			   		$resto -= $facturas["importe_venc$i"];
    			   		$facturas["cobro$i"] = $facturas["importe_venc$i"];
    			   	else:
						$facturas["saldo$i"] = $facturas["importe_venc$i"] - $resto;
    			   		$facturas["cobro$i"] = $resto;
    			   		$resto = 0;
    				endif;
    			endif;				
	    	}
		else: 
			$cobros = $oReciboFacturas->getCobroNotaCredito($facturas['id']);
			$resto = $cobros;
			$facturas['saldo_comprobante'] = $cobros - $facturas['total_comprobante'];
//			$facturas['total_comprobante'] *= -1;
			for ($i = 1; $i <= 10; $i++) {
				if(!empty($facturas["importe_venc$i"])):
					if($facturas["importe_venc$i"] <= $resto):
    			   		$facturas["saldo$i"] = 0.00;
						$resto -= $facturas["importe_venc$i"];
    			   		$facturas["cobro$i"] = $facturas["importe_venc$i"];
    			   	else:
    			   		$facturas["saldo$i"] = $resto - $facturas["importe_venc$i"];
    			   		$resto = 0;
    				endif;
    			   	$facturas["importe_venc$i"] *= -1;
    			endif;				
	    	}
		endif; 
		$facturas['cobro_comprobante'] = $cobros;
		return $facturas;
	}
	

	function detalleCuota($cuotas){
		$detalleCuota = array();
		$i = 0;
		foreach($cuotas as $cuota){
			for($j = 1; $j <= 10; $j++){
				if($cuota["saldo$j"] != 0):
					$i += 1;
					$detalleCuota[$i]['id'] = $cuota['id'];
    		        $detalleCuota[$i]['cliente_id'] = $cuota['cliente_id'];
        		    $detalleCuota[$i]['recibo_id'] = '';
            		$detalleCuota[$i]['tipo_cobro'] = $cuota['tipo'];
	            	$detalleCuota[$i]['cliente_factura_id'] = $cuota['id'];
	    	        $detalleCuota[$i]['importe'] = $cuota["importe_venc$j"];
    	    	    $detalleCuota[$i]['tipo_comprobante_desc'] = $cuota['tipo_comprobante_desc'];
        	    	$detalleCuota[$i]['tipo'] = $cuota['tipo'];
	        	    $detalleCuota[$i]['fecha_comprobante'] = $cuota['fecha_comprobante'];  
    	        	$detalleCuota[$i]['total_comprobante'] = $cuota['total_comprobante'];
	        	    $detalleCuota[$i]['cuota'] = $j;
    	        	$detalleCuota[$i]['vencimiento'] = $cuota["vencimiento$j"];  
	    	        $detalleCuota[$i]['saldo'] = $cuota["saldo$j"];
    	    	    $detalleCuota[$i]['cobro'] = $cuota["cobro$j"];
        	    	$detalleCuota[$i]['saldo_comprobante'] = $cuota['saldo_comprobante'];
            		$detalleCuota[$i]['cobro_comprobante'] = $cuota['cobro_comprobante'];
            		$detalleCuota[$i]['signo'] = ($cuota['tipo'] == 'SA' ? '-' : '+');
            	endif;
			}
		}
		return $detalleCuota;
	}

	
	function guardarRecibo($datos){
		// Llamo a los modelos a utilizar
		// Recibo Cabecera
		$oRecibo = $this->importarModelo('Recibo', 'Clientes');
		
		$nReciboId = $oRecibo->guardarRecibo($datos);
		if(!$nReciboId):
			return false;
		endif;
		
		return $nReciboId;
		
	}
	
	function anularRecibo($id){
		// Llamo a los modelos a utilizar
		// Recibo Cabecera
		$oRecibo = $this->importarModelo('Recibo', 'Clientes');
		
		if(!$oRecibo->anularReciboCtaCte($id)):
			return false;
		endif;
		
		return true;
		
	}
	
	
	function getRecibo($nId){
		$oRecibos = $this->importarModelo('Recibo', 'Clientes');
		
		return $oRecibos->getRecibo($nId);
	}
	
}
?>