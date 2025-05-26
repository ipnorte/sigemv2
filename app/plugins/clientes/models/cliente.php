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
				$oProveedor = $this->importarModelo('Proveedor', 'proveedores');
				$aProveedor = array('id' => $datos['Cliente']['proveedor_id'], 'cliente_id' => $id);
				if(!$oProveedor->save($aProveedor)):
					$this->rollback();
					return 0;
				endif;
			endif;
			
			if($datos['Cliente']['importe_saldo'] != 0):
				$datos['Cliente']['id'] = $id;
				$aFactura['cliente_id'] = $id;
				$oFactura = $this->importarModelo('ClienteFactura', 'clientes');
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
    		$oFactura = $this->importarModelo('ClienteFactura', 'clientes');
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
	    
	    $cliente['Cliente']['saldo'] = $this->traerSaldo($id);

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
		
		$facturas = $this->traerFacturas($cliente_id, true);
		
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
		
		# ORDENO EL ARRAY POR FECHA DE VENCIMIENTO
	    foreach($compSaldo as $c=>$key) {
    	    $sort_id[] = $key['id'];
        	$sort_venc[] = $key['vencimiento'];
	    }

    	array_multisort($sort_venc, SORT_ASC, $compSaldo);

		
		return $compSaldo;
	}
	
	function cobroCuenta($cliente_id){
		$oCobroCuenta = $this->importarModelo('ReciboDetalle', 'clientes');
		$cobroCuenta = $oCobroCuenta->getCobroDetalleCuenta($cliente_id);
		
		if(empty($cobroCuenta)) return array();

		$oCobroFacturas = $this->importarModelo('ReciboFactura', 'clientes');
		
		foreach($cobroCuenta as $clave => $valor){
			$cobros = $oCobroFacturas->getCobroCuenta($cobroCuenta[$clave]['id']);
			$resto = $cobros;

			$cobroCuenta[$clave]['tipo_comprobante_desc'] = 'Cobro a Cuenta (Anticipo)';
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
	
	
	function armaCtaCte($id){
            $this->query("CALL SP_CTACTE_CLIENTE($id)");
            return true;
            
        }
        
        
	function armaCtaCte___($cliente_id){
            $oFacturas = $this->importarModelo('ClienteFactura', 'clientes');
            $oRecibo = $this->importarModelo('Recibo', 'clientes');
            $oReciboFacturas = $this->importarModelo('ReciboFactura', 'clientes');
            $oReciboDetalle = $this->importarModelo('ReciboDetalle', 'clientes');
            $oCtaCte = $this->importarModelo('ClienteCtacte', 'clientes');

            $sqlCtaCte = "SELECT
                        ClienteFactura.fecha_comprobante as fecha, 

                        concat(if(ClienteFactura.tipo = 'SD' or ClienteFactura.tipo = 'SD', 'SALDO ANTERIOR', 
                        concat(if(ClienteFactura.tipo = 'FA', 'FACTURA',
                        IF(ClienteFactura.tipo = 'ND', 'NOTA DEBITO', 'NOTA CREDITO')), ' ', ClienteFactura.letra_comprobante, '-', 
                        ClienteFactura.punto_venta_comprobante, '-', ClienteFactura.numero_comprobante)))as concepto,

                        if(ClienteFactura.tipo = 'FA' or ClienteFactura.tipo = 'ND', ClienteFactura.total_comprobante, 0) as debe,

                        if(ClienteFactura.tipo = 'NC', ClienteFactura.total_comprobante, 0) as haber,

                        ClienteFactura.total_comprobante * if(ClienteFactura.tipo = 'SD' or ClienteFactura.tipo='FA' or ClienteFactura.tipo = 'ND',1, -1) AS saldo,

                        ClienteFactura.id, ClienteFactura.tipo,

                        if(ifnull(if(ClienteFactura.tipo = 'FA' or ClienteFactura.tipo = 'SD', (select sum(importe) FROM recibo_facturas AS ReciboFactura
                        WHERE ReciboFactura.cliente_factura_id = ClienteFactura.id), (select sum(importe) FROM recibo_facturas AS ReciboFactura
                        WHERE ReciboFactura.cliente_credito_id = ClienteFactura.id)),0) = 0, 0, 1) as anular,

                        ClienteFactura.comentario, ClienteFactura.orden_descuento_cobro_id as orden_cobro, ClienteFactura.liquidacion_id as liquidacion,

                        ifnull(if(ClienteFactura.tipo = 'FA' or ClienteFactura.tipo = 'SD', (select sum(importe) FROM recibo_facturas AS ReciboFactura
                        WHERE ReciboFactura.cliente_factura_id = ClienteFactura.id), (select sum(importe) FROM recibo_facturas AS ReciboFactura
                        WHERE ReciboFactura.cliente_credito_id = ClienteFactura.id)),0) as cobro_comprobante


                        FROM cliente_facturas AS ClienteFactura
                        WHERE ClienteFactura.cliente_id = '$cliente_id' and ClienteFactura.anulado = 0

                        UNION

                        select	Recibo.fecha_comprobante as fecha, concat('RECIBO NRO.: ', Recibo.letra, '-', Recibo.sucursal, '-', Recibo.nro_recibo) as concepto,
                        0 as debe, Recibo.importe as haber, Recibo.importe * -1 as saldo, Recibo.id, 'REC' as tipo, 
                        IF((SELECT COUNT(*) FROM banco_cuenta_movimientos WHERE recibo_id = Recibo.id AND banco_cuenta_saldo_id > 0) > 0, 1, 0) AS anular,
                        Recibo.comentarios, 0 as orden_cobro, 0 as liquidacion, 0 as cobro_comprobante
                        from	recibos as Recibo
                        where	Recibo.cliente_id = '$cliente_id' and Recibo.anulado = 0
                        ORDER BY fecha, tipo

            ";

            $aCtaCte = $this->query($sqlCtaCte);


//            $oCtaCte->delete(['ClienteCtacte.proveedor_id' => $id]);
            $sqlCtaCte = "DELETE FROM cliente_ctactes WHERE cliente_id = '$cliente_id'";
            $aDelete = $this->query($sqlCtaCte);
            
            $ctaCte = array();
            $tmpCtaCte = array();
            $saldo = 0;
            $item = 0;
            foreach($aCtaCte as $factura){
                $socio = '';
                $factura[0]['pagos'] = 0;/*
                if($factura[0]['tipo'] == 'REC'){
                    $aRecDetalle = $oReciboDetalle->find('all',array('conditions' => array('ReciboDetalle.recibo_id' => $factura[0]['id'], 'ReciboDetalle.tipo_cobro' => 'AN', 'importe >' => 0)));

                    if(!empty($aRecDetalle)){
                        $aDetalle = $oReciboDetalle->find('all',array('conditions' => array('ReciboDetalle.recibo_detalle_id' => $aRecDetalle[0]['ReciboDetalle']['id'])));
                        if(!empty($aDetalle)){
                            $factura[0]['pagos'] = $aDetalle[0]['ReciboDetalle']['importe'];
                            $factura[0]['anular'] = 1;
                        }
                    }
                }else{
                    $aClieFct = $oFacturas->getFactura($factura[0]['id']);
                    $factura[0]['pagos'] = $aClieFct['pagos'];
                    if($aClieFct['pagos'] > 0){
                        $factura[0]['anular'] = 1;
                    }
                }*/
                if($factura[0]['orden_cobro'] > 0) $socio = $this->getNombreSocio($factura[0]['orden_cobro']). ' ** ';
                if($factura[0]['orden_cobro'] > 0 || $factura[0]['liquidacion'] > 0) $factura[0]['anular'] = 1;

                $item += 1;
                $tmpCtaCte = array();
                $tmpCtaCte['item'] = $item;
                $tmpCtaCte['cliente_id'] = $cliente_id;
                $tmpCtaCte['fecha'] = $factura[0]['fecha'];
                $tmpCtaCte['concepto'] = $factura[0]['concepto'];
                $tmpCtaCte['debe'] = $factura[0]['debe'];
                $tmpCtaCte['haber'] = $factura[0]['haber'];
                $tmpCtaCte['saldo']  = $factura[0]['saldo'] + $saldo;
                $tmpCtaCte['id'] = $factura[0]['id'];
                $tmpCtaCte['tipo'] = $factura[0]['tipo']; 
                $tmpCtaCte['anular'] = $factura[0]['anular'];
                $tmpCtaCte['comentario'] = $socio . $factura[0]['comentario']; 
                $tmpCtaCte['pagos'] = $factura[0]['cobro_comprobante'];

                $saldo = $tmpCtaCte['saldo'];
                $oCtaCte->save($tmpCtaCte);
//                array_push($ctaCte, $tmpCtaCte);
            }
//            $ctaCte = array_reverse($ctaCte);


            return true;
	}
	
	
	function traerFacturas($cliente_id, $soloSaldo=false){
		$sql = "SELECT 
				ClienteFactura.*,
				ifnull(if(ClienteFactura.tipo = 'FA' or ClienteFactura.tipo = 'SA', (select sum(importe) FROM recibo_facturas AS ReciboFactura
				WHERE ReciboFactura.cliente_factura_id = ClienteFactura.id), (select sum(importe) FROM recibo_facturas AS ReciboFactura
				WHERE ReciboFactura.cliente_credito_id = ClienteFactura.id)),0) as pago_comprobante,
				
				if(ClienteFactura.tipo = 'SD' or ClienteFactura.tipo='FA',ClienteFactura.total_comprobante - 
				IFNULL((SELECT SUM(importe) FROM recibo_facturas AS ReciboFactura
				WHERE ReciboFactura.cliente_factura_id = ClienteFactura.id),0), ClienteFactura.total_comprobante -
				IFNULL((SELECT SUM(importe) FROM recibo_facturas AS ReciboFactura
				WHERE ReciboFactura.cliente_credito_id = ClienteFactura.id),0)) AS saldo
					
				FROM 	cliente_facturas AS ClienteFactura
				WHERE	ClienteFactura.anulado = 0 and
				cliente_id = $cliente_id";
		
		if($soloSaldo):
			$sql .= "
					and
					if(ClienteFactura.tipo = 'SA' or ClienteFactura.tipo='FA',ClienteFactura.total_comprobante - 
					IFNULL((SELECT SUM(importe) FROM recibo_facturas AS ReciboFactura
					WHERE ReciboFactura.Cliente_factura_id = ClienteFactura.id),0), ClienteFactura.total_comprobante -
					IFNULL((SELECT SUM(importe) FROM recibo_facturas AS ReciboFactura
					WHERE ReciboFactura.Cliente_credito_id = ClienteFactura.id),0)) != 0
			";
		endif;
                
                ##############################################################################################
                # 
                ##############################################################################################
                $sql .= " order by fecha_comprobante DESC limit 50";

		$aFacturas = $this->query($sql);
		
		return $aFacturas;
//		
//		$oFacturas = $this->importarModelo('ClienteFactura', 'Clientes');
//
//		$return = $oFacturas->find('all',array(
//							'conditions' => array('ClienteFactura.cliente_id' => $cliente_id, 'ClienteFactura.anulado' => 0)
//		));
//
//		$return = Set::extract("{n}.ClienteFactura",$return);
//		return $return;
		
	}
	
	
	function traerRecibos($cliente_id){

		$oRecibos = $this->importarModelo('Recibo', 'clientes');
		$return = $oRecibos->find('all',array(
							'conditions' => array('Recibo.cliente_id' => $cliente_id, 'Recibo.anulado' => 0)
		));

		$return = Set::extract("{n}.Recibo",$return);
		return $return;
		
	}
	
		
	function datosAdicionales($resultados, $detalleCuota=false){
		$tmpReturn = array();
		$return = array();

		foreach($resultados as $clave => $valor){
			$resultados[$clave]['ClienteFactura']['socio'] = '';
			if($valor['ClienteFactura']['orden_descuento_cobro_id'] > 0) $resultados[$clave]['ClienteFactura']['socio'] = $this->getNombreSocio($valor['ClienteFactura']['orden_descuento_cobro_id']);
			
			if($valor['ClienteFactura']['tipo_comprobante'] == 'FAC'):
				if($valor['ClienteFactura']['tipo'] == 'FA') $resultados[$clave]['ClienteFactura']['tipo_comprobante_desc'] = 'FACTURA';
				if($valor['ClienteFactura']['tipo'] == 'NC') $resultados[$clave]['ClienteFactura']['tipo_comprobante_desc'] = 'NOTA CREDITO';
				if($valor['ClienteFactura']['tipo'] == 'ND') $resultados[$clave]['ClienteFactura']['tipo_comprobante_desc'] = 'NOTA DEBITO';
			endif;
			
			if($valor['ClienteFactura']['tipo_comprobante'] == 'SALDOCLIENTE') $resultados[$clave]['ClienteFactura']['tipo_comprobante_desc'] = 'SALDO ANTERIOR';
			else $resultados[$clave]['ClienteFactura']['tipo_comprobante_desc'] .= ' ' . $valor['ClienteFactura']['letra_comprobante'] . ' ' . $valor['ClienteFactura']['punto_venta_comprobante'] . '-' . $valor['ClienteFactura']['numero_comprobante'];
			
			$tmpReturn = $this->armaSaldo($resultados[$clave], $detalleCuota);

			if($detalleCuota):
				foreach($tmpReturn as $renglon):
					array_push($return, $renglon);
				endforeach; 
			else:
				array_push($return, $tmpReturn);
			endif;
			
		}

		
		return $return;
	}
	
	function armaSaldo($facturas, $detalleCuota=false){
		$tmpFactura = array();
		$returnFactura = array();
		
		if($facturas['ClienteFactura']['tipo'] == 'SD' || $facturas['ClienteFactura']['tipo'] == 'FA' || $facturas['ClienteFactura']['tipo'] == 'ND'):
			$resto = $facturas[0]['pago_comprobante'];
			$facturas['ClienteFactura']['saldo_comprobante'] = 0;
			$facturas['ClienteFactura']['saldo'] = $facturas['ClienteFactura']['total_comprobante'] - $facturas[0]['pago_comprobante'];
			$j = 0;
			for ($i = 1; $i <= 10; $i++) {
				if(!empty($facturas['ClienteFactura']["importe_venc$i"])):
					$j += 1;
					if($facturas['ClienteFactura']["importe_venc$i"] <= $resto):
						$facturas['ClienteFactura']["saldo$i"] = 0.00;
    			   		$resto -= $facturas['ClienteFactura']["importe_venc$i"];
    			   		$facturas['ClienteFactura']["pago$i"] = $facturas['ClienteFactura']["importe_venc$i"];
    			   	else:
						$facturas['ClienteFactura']["saldo$i"] = $facturas['ClienteFactura']["importe_venc$i"] - $resto;
    			   		$facturas['ClienteFactura']["pago$i"] = $resto;
    			   		$resto = 0;
    			   		if($detalleCuota):
    			   			$tmpFactura = $this->detalleCuota($facturas['ClienteFactura'], $i);
    			   			array_push($returnFactura, $tmpFactura);
    			   		endif;
    				endif;
    			endif;				
	    	}
		else: 
			$resto = $facturas[0]['pago_comprobante'];
			$facturas['ClienteFactura']['saldo_comprobante'] = 0;
			$facturas['ClienteFactura']['saldo'] = $facturas[0]['pago_comprobante'] - $facturas['ClienteFactura']['total_comprobante'];
			for ($i = 1; $i <= 10; $i++) {
				if(!empty($facturas['ClienteFactura']["importe_venc$i"])):
					if($facturas['ClienteFactura']["importe_venc$i"] <= $resto):
    			   		$facturas['ClienteFactura']["saldo$i"] = 0.00;
						$resto -= $facturas['ClienteFactura']["importe_venc$i"];
    			   		$facturas['ClienteFactura']["pago$i"] = $facturas['ClienteFactura']["importe_venc$i"];
    			   	else:
    			   		$facturas['ClienteFactura']["saldo$i"] = $resto - $facturas['ClienteFactura']["importe_venc$i"];
    			   		$resto = 0;
    				endif;
    			   	$facturas['ClienteFactura']["importe_venc$i"] *= -1;
   			   		if($detalleCuota):
   			   			$tmpFactura = $this->detalleCuota($facturas['ClienteFactura'], $i);
    			   		array_push($returnFactura, $tmpFactura);
   			   		endif;
    			endif;
	    	}
		endif; 
		
		if(!$detalleCuota) $returnFactura = $facturas['ClienteFactura'];
		
		return $returnFactura;
	}
	


	function detalleCuota($cuota, $j){
		$detalleCuota = array();
					$detalleCuota['id'] = $cuota['id'];
    		        $detalleCuota['cliente_id'] = $cuota['cliente_id'];
        		    $detalleCuota['recibo_id'] = '';
            		$detalleCuota['tipo_cobro'] = $cuota['tipo'];
	            	$detalleCuota['cliente_factura_id'] = $cuota['id'];
	    	        $detalleCuota['importe'] = $cuota["importe_venc$j"];
    	    	    $detalleCuota['tipo_comprobante_desc'] = $cuota['tipo_comprobante_desc'];
        	    	$detalleCuota['tipo'] = $cuota['tipo'];
	        	    $detalleCuota['fecha_comprobante'] = $cuota['fecha_comprobante'];  
    	        	$detalleCuota['total_comprobante'] = $cuota['total_comprobante'];
	        	    $detalleCuota['cuota'] = $j;
    	        	$detalleCuota['vencimiento'] = $cuota["vencimiento$j"];  
	    	        $detalleCuota['saldo'] = $cuota["saldo$j"];
    	    	    $detalleCuota['cobro'] = $cuota["cobro$j"];
        	    	$detalleCuota['saldo_comprobante'] = $cuota['saldo_comprobante'];
            		$detalleCuota['cobro_comprobante'] = $cuota['cobro_comprobante'];
            		$detalleCuota['signo'] = ($cuota['tipo'] == 'SA' || $cuota['tipo'] == 'NC' ? '-' : '+');
            		$detalleCuota['comentario'] = $cuota['socio'] . ' ' . $cuota['comentario'];
            		
        return $detalleCuota;
	}

	
	function guardarRecibo($datos){
		// Llamo a los modelos a utilizar
		// Recibo Cabecera
		$oRecibo = $this->importarModelo('Recibo', 'clientes');
		
		$nReciboId = $oRecibo->guardarRecibo($datos);
		if(!$nReciboId):
			return false;
		endif;
		
		return $nReciboId;
		
	}
	
	function anularRecibo($id){
		// Llamo a los modelos a utilizar
		// Recibo Cabecera
		$oRecibo = $this->importarModelo('Recibo', 'clientes');
		
		if(!$oRecibo->anularReciboCtaCte($id)):
			return false;
		endif;
		
		return true;
		
	}
	
	
	function getRecibo($nId){
		$oRecibos = $this->importarModelo('Recibo', 'clientes');
		
		return $oRecibos->getRecibo($nId);
	}
	
	function getNombreSocio($ordenDescuentoCobroId){
		$this->OrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');
		$this->Socio = $this->importarModelo('Socio', 'pfyj');
		
		$aOrdenDescCobro = $this->OrdenDescuentoCobro->read(null, $ordenDescuentoCobroId);

		return $this->Socio->getApenom($aOrdenDescCobro['OrdenDescuentoCobro']['socio_id']);
		
	}


	function traerSaldo($cliente_id){
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
				sum(if(ClienteFactura.tipo = 'SD' or ClienteFactura.tipo = 'FA', total_comprobante, total_comprobante *-1)) + 
				sum(if(ClienteFactura.tipo != 'SD' and ClienteFactura.tipo != 'FA', (SELECT SUM(importe) FROM recibo_facturas AS ReciboFactura
				WHERE ReciboFactura.cliente_credito_id = ClienteFactura.id), 0)) as total_comprobante,
				
				sum(IFNULL((SELECT SUM(importe) FROM recibo_facturas AS ReciboFactura
				WHERE ReciboFactura.cliente_factura_id = ClienteFactura.id),0)) + ifnull((select sum(importe) from recibo_detalles as ReciboDetalle
				where ReciboDetalle.tipo_cobro = 'AN' and ReciboDetalle.cliente_id = ClienteFactura.cliente_id and ReciboDetalle.id not in(
				select recibo_detalle_id from recibo_facturas)),0)  AS pago_comprobante,
				
				sum(if(ClienteFactura.tipo = 'SD' or ClienteFactura.tipo = 'FA', total_comprobante, total_comprobante *-1)) + 
				sum(if(ClienteFactura.tipo != 'SD' and ClienteFactura.tipo != 'FA', (SELECT SUM(importe) FROM recibo_facturas AS ReciboFactura
				WHERE ReciboFactura.cliente_credito_id = ClienteFactura.id), 0)) -
				(sum(IFNULL((SELECT SUM(importe) FROM recibo_facturas AS ReciboFactura
				WHERE ReciboFactura.cliente_factura_id = ClienteFactura.id),0)) + ifnull((select sum(importe) from recibo_detalles as ReciboDetalle
				where ReciboDetalle.tipo_cobro = 'AN' and ReciboDetalle.cliente_id = ClienteFactura.cliente_id and ReciboDetalle.id not in(
				select recibo_detalle_id from recibo_facturas)),0))  AS saldo
				
				FROM cliente_facturas AS ClienteFactura
				WHERE
				cliente_id = $cliente_id and anulado = 0
				group	by cliente_id
			";
		$aFacturas = $this->query($sql);

		$saldo = 0;
		if(!empty($aFacturas)) $saldo = $aFacturas[0][0]['saldo'];
		
		return $saldo;
		
	}
	

	function guardarFactura($facturas){
		$oFacturasCliente = $this->importarModelo('ClienteFactura', 'clientes');

		if(!$oFacturasCliente->grabarFacturaCliente($facturas)) return false;
		
		return true;
		
	}
	
	
	function guardarCompensarPago($datos){

		// Orden de Pago Facturas
		$oReciboFactura = $this->importarModelo('ReciboFactura', 'Clientes');
			
		if(isset($datos['Cliente']['fac']) && isset($datos['Cliente']['ant'])):
			$facturas = array();
			foreach($datos['Cliente']['fac']['check'] as $id => $importe):
				$tmpFactura = array();
				$tmpFactura['id'] = 0;
				$tmpFactura['cliente_id'] = $datos['Cliente']['cliente_id'];
				$tmpFactura['cliente_factura_id'] = $datos['Cliente']['fac']['id'][$id];
				$tmpFactura['fecha'] = $datos['Cliente']['fpago'];
				$tmpFactura['recibo_id'] = 0;
				$tmpFactura['tipo_cobro'] = 'FA';
				$tmpFactura['cliente_credito_id'] = 0;
				$tmpFactura['recibo_detalle_id'] = 0;
				$tmpFactura['importe'] = $datos['Cliente']['fac']['importe_a_pagar'][$id];
				$tmpFactura['saldo'] = $datos['Cliente']['fac']['importe_a_pagar'][$id];
				array_push($facturas, $tmpFactura);
			endforeach;
			
			$anticipos = array();
			foreach($datos['Cliente']['ant']['check'] as $id => $importe):
				$tmpAnticipo = array();
				$tmpAnticipo['id'] = 0;
				$tmpAnticipo['cliente_id'] = $datos['Cliente']['cliente_id'];
				$tmpAnticipo['cliente_factura_id'] = 0;
				$tmpFactura['fecha'] = $datos['Cliente']['fpago'];
				$tmpFactura['recibo_id'] = 0;
				$tmpAnticipo['tipo_cobro'] = 'AN';
				$tmpAnticipo['cliente_credito_id'] = 0;
				$tmpAnticipo['recibo_detalle_id'] = 0;
				$tmpAnticipo['importe'] = $datos['Cliente']['ant']['importe_a_pagar'][$id] * (-1);
				$tmpAnticipo['saldo'] = $datos['Cliente']['ant']['importe_a_pagar'][$id] * (-1);
				if($datos['Cliente']['ant']['tipo'][$id] == 'AN'):
					$tmpAnticipo['recibo_detalle_id'] = $datos['Cliente']['ant']['id'][$id];
				else:
					$tmpAnticipo['tipo_cobro'] = 'NC';
					$tmpAnticipo['cliente_factura_id'] = $datos['Cliente']['ant']['id'][$id];
					$tmpAnticipo['cliente_credito_id'] = $datos['Cliente']['ant']['id'][$id];
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
							$tmpFacturaAnticipo['cliente_id'] = $valorF['cliente_id'];
							$tmpFacturaAnticipo['fecha'] = $valorF['fecha'];
							$tmpFacturaAnticipo['recibo_id'] = 0;
							$tmpFacturaAnticipo['cliente_factura_id'] = $valorF['cliente_factura_id'];
							$tmpFacturaAnticipo['cliente_credito_id'] = $anticipos[$claveA]['cliente_credito_id'];
							$tmpFacturaAnticipo['recibo_detalle_id'] = $anticipos[$claveA]['recibo_detalle_id'];
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
		

			if(!$oReciboFactura->saveAll($aFacturaAnticipo)):		
				return false;
			endif;
			
			return true;
			

		endif;
		
		return false;
	}
        
        function traerClienteXCuit($cuit){
            $aCliente = $this->find('all', array('conditions' => array('Cliente.cuit' => $cuit)));
            return $aCliente;
        }
	
	
}
?>