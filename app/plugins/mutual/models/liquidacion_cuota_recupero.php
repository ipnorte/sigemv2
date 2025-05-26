<?php 

class LiquidacionCuotaRecupero extends MutualAppModel{
	
	var $name = "LiquidacionCuotaRecupero";
	
	
	function generar($recuperoData){
		
		if(empty($recuperoData)) return null;
		
		$cuotaRecupero = array();
		
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();	

		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();
		
		App::import('Model','Mutual.OrdenDescuentoCobro');
		$oCOBRO = new OrdenDescuentoCobro();			

		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oCOBROCUOTA = new OrdenDescuentoCobroCuota();	

		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();		
		
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$liquidacion = $oLiq->cargar($recuperoData['liquidacion_id']);	
		
		App::import('Model','Proveedores.Proveedor');
		$oPROV = new Proveedor();	

		$ACUM_RECU = 0;
		$ACUM_COMI = 0;
		

		###########################################################
		#CARGO LAS FACTURAS DEL PROVEEDOR Y DEL CLIENTE PARA OPERAR
		###########################################################
		$facProveedor = null;
		$facCliente = null;
		
		App::import('Model','Proveedores.ProveedorFactura');
		$oPROVFACT = new ProveedorFactura();			
		App::import('Model','Clientes.ClienteFactura');
		$oCLIFACT = new ClienteFactura();		
		
		$facProveedor = $oPROVFACT->find('all',array('conditions' => array('ProveedorFactura.liquidacion_id' => $liquidacion['Liquidacion']['id'], 'ProveedorFactura.proveedor_id' => $recuperoData['proveedor_id'])));
		if(!empty($facProveedor)) $facProveedor = $facProveedor[0];
		
		$oCLIFACT->bindModel(array('hasMany' => array('ClienteFacturaDetalle')));
		$oCLIFACT->recursive = 2;
		$facCliente = $oCLIFACT->find('all',array('conditions' => array('ClienteFactura.liquidacion_id' => $liquidacion['Liquidacion']['id'], 'ClienteFactura.cliente_id' => $oPROV->getClienteId($recuperoData['proveedor_id']), 'ClienteFactura.anulado' => 0)));
		if(!empty($facCliente)) $facCliente = $facCliente[0];
		#############################################################
		

		foreach ($recuperoData['liquidacion_cuotas'] as $liquidacionCuotaId){
			
			$liquiCuota = $oLC->read(null,$liquidacionCuotaId);
			
//			debug($liquiCuota);
			
			#determino el saldo actual de la cuota
			$saldoCuota = $oCUOTA->getSaldo($liquiCuota['LiquidacionCuota']['orden_descuento_cuota_id']);
			
//			debug($saldoCuota);
			
			if($saldoCuota != 0):
			
				############################################################################
				#generar un cobro
				############################################################################
				$cobro = array('OrdenDescuentoCobro' => array(
						'id' => 0,
						'socio_id' => $liquiCuota['LiquidacionCuota']['socio_id'],
						'tipo_cobro' => $recuperoData['tipo_cobro_recupero'],
						'fecha' => $recuperoData['fecha_cobro'],
						'periodo_cobro' => $recuperoData['periodo_proveedor'],
						'importe' => $saldoCuota,
						'recibo_id' => 0
				));	
						
				$comision = $oCOBROCUOTA->calcularComisionCobranza($liquiCuota['LiquidacionCuota']['orden_descuento_cuota_id'],$saldoCuota);
				$cobro['OrdenDescuentoCobroCuota'] = array();
				$cobro['OrdenDescuentoCobroCuota'][0] = array(
					'periodo_cobro' => $recuperoData['periodo_proveedor'],
					'orden_descuento_cuota_id' => $liquiCuota['LiquidacionCuota']['orden_descuento_cuota_id'],
					'importe' => $saldoCuota,
					'proveedor_id' => $recuperoData['proveedor_id'],
					'alicuota_comision_cobranza' => $comision['alicuota'],
					'comision_cobranza' => $comision['comision'],
				);
				if(!$oCOBRO->saveAll($cobro)) return false;
				
				$cobroID = $oCOBRO->getLastInsertID();
				
				if(!$oCUOTA->marcarPagada($liquiCuota['LiquidacionCuota']['orden_descuento_cuota_id'])) return false;

				$ACUM_COMI += $comision['comision'];
				
				############################################################################
				# MARCAR LAS CUOTAS PAGADAS EN LA LIQUIDACION CUOTAS
				############################################################################				
				$liquiCuota['LiquidacionCuota']['importe_debitado'] = $saldoCuota;
				$liquiCuota['LiquidacionCuota']['para_imputar'] = 1;
				$liquiCuota['LiquidacionCuota']['imputada'] = 1;
				$liquiCuota['LiquidacionCuota']['orden_descuento_cobro_id'] = $cobroID;
				$liquiCuota['LiquidacionCuota']['alicuota_comision_cobranza'] = $comision['alicuota'];
				$liquiCuota['LiquidacionCuota']['comision_cobranza'] = $comision['comision'];				
				
				if(!$oLC->save($liquiCuota)) return false;
				
				$ACUM_RECU += $saldoCuota;
				
				
				############################################################################
				#grabo el recupero
				############################################################################				
				$cuotaRecupero['LiquidacionCuotaRecupero'] = array(
									'id' => 0,
									'orden_descuento_cobro_id' => $cobroID,
									'liquidacion_id' => $recuperoData['liquidacion_id'],
									'liquidacion_cuota_id' => $liquidacionCuotaId,
									'orden_descuento_cuota_id' => $liquiCuota['LiquidacionCuota']['orden_descuento_cuota_id'],
									'socio_id' => $liquiCuota['LiquidacionCuota']['socio_id'],
									'importe_liquidado' => $liquiCuota['LiquidacionCuota']['saldo_actual'],
									'saldo_actual' => $saldoCuota,
									'proveedor_id' => $recuperoData['proveedor_id'],
									'orden_descuento_id' => 0,
									'periodo_socio' => $recuperoData['periodo_socio'],
									'periodo_proveedor' => $recuperoData['periodo_proveedor'],
									'alicuota_comision_cobranza' => $comision['alicuota'],
									'comision_cobranza' => $comision['comision'],
									'proveedor_factura_id' => $facProveedor['ProveedorFactura']['id'],
									'cliente_factura_id' => $facCliente['ClienteFactura']['id'],
				);
				if(!$this->save($cuotaRecupero)) return false;
				
				$cuotaRecupero['LiquidacionCuotaRecupero']['id'] = $this->getLastInsertID();
				
				############################################################################
				#generar la orden de descuento RECAR
				############################################################################
				
				$impoCuota = $saldoCuota / $recuperoData['cantidad_cuotas'];
				
				$global = parent::getGlobalDato('concepto_1,entero_1',$recuperoData['tipo_producto_recupero']);
				
				$obsOrden = "** RECUPERO CUOTA *** ".$liquidacion['Liquidacion']['periodo_desc'] . " " . $liquidacion['Liquidacion']['organismo'] ." (".$oPROV->getRazonSocial($recuperoData['proveedor_id']).")";
				
				$ordenDto = array();
				$ordenDto['OrdenDescuento'] = array(
								'id' => 0,
								'fecha' => parent::getDiaHabil($recuperoData['periodo_socio']),
								'tipo_orden_dto' => 'RECAR',
								'numero' => $cuotaRecupero['LiquidacionCuotaRecupero']['id'],
								'tipo_producto' => $recuperoData['tipo_producto_recupero'],
								'socio_id' => $liquiCuota['LiquidacionCuota']['socio_id'],
								'persona_beneficio_id' => $liquiCuota['LiquidacionCuota']['persona_beneficio_id'],
								'proveedor_id' => $global['GlobalDato']['entero_1'],
								'periodo_ini' => $recuperoData['periodo_socio'],
								'importe_cuota' => $impoCuota,
								'importe_total' => $saldoCuota,
								'primer_vto_socio' => parent::getDiaHabil($recuperoData['periodo_socio']),
								'primer_vto_proveedor' => parent::getDiaHabil($recuperoData['periodo_socio']),
								'cuotas' => $recuperoData['cantidad_cuotas'],
								'observaciones' => $obsOrden
							);				
				$ordenDto['OrdenDescuentoCuota'] = $oCUOTA->armaCuotas($ordenDto);
				
				if(!$oORDEN->saveAll($ordenDto)) return false;
	
				
				$cuotaRecupero['LiquidacionCuotaRecupero']['orden_descuento_id'] = $oORDEN->getLastInsertID();
				
				if(!$this->save($cuotaRecupero)) return false;
		
				
			endif;
			
		}

		##############################################################################################
		#FACTURACION
		##############################################################################################
		if($ACUM_RECU != 0 && $liquidacion['Liquidacion']['facturada'] == 1 && !empty($facProveedor)){
			$facProveedor['ProveedorFactura']['importe_no_gravado'] += $ACUM_RECU;
			$facProveedor['ProveedorFactura']['total_comprobante'] += $ACUM_RECU;
			$facProveedor['ProveedorFactura']['importe_venc1'] += $ACUM_RECU;
			$oPROVFACT->save($facProveedor);
			//factura por comisiones
			if($ACUM_COMI != 0 && !empty($facCliente)){
				$facCliente['ClienteFactura']['importe_no_gravado'] += $ACUM_COMI;
				$facCliente['ClienteFactura']['total_comprobante'] += $ACUM_COMI;
				$facCliente['ClienteFactura']['importe_venc1'] += $ACUM_COMI;
				$facCliente['ClienteFacturaDetalle'][0]['precio_unitario'] += $ACUM_COMI;
				$facCliente['ClienteFacturaDetalle'][0]['precio_total'] += $ACUM_COMI;
				$oCLIFACT->saveAll($facCliente);
			}
		}
		
//		exit;
		
		return true;
		
		
		
	}
		
	
	function anular($id){

		$recupero = $this->read(null,$id);
		
		parent::begin();
		
		#########################################################
		# RESTO EL IMPORTE DE LA FACTURA DEL PROVEEDOR
		#########################################################			
		App::import('Model','Proveedores.ProveedorFactura');
		$oPROVFACT = new ProveedorFactura();			
		App::import('Model','Clientes.ClienteFactura');
		$oCLIFACT = new ClienteFactura();

		if(!empty($recupero['LiquidacionCuotaRecupero']['proveedor_factura_id'])){
			$facProveedor['ProveedorFactura'] = array();
			$facProveedor['ProveedorFactura'] = $oPROVFACT->getFactura($recupero['LiquidacionCuotaRecupero']['proveedor_factura_id']);
			if($facProveedor['ProveedorFactura']['saldo'] >= $recupero['LiquidacionCuotaRecupero']['saldo_actual']){
				$facProveedor['ProveedorFactura']['importe_no_gravado'] -= $recupero['LiquidacionCuotaRecupero']['saldo_actual'];
				$facProveedor['ProveedorFactura']['total_comprobante'] -= $recupero['LiquidacionCuotaRecupero']['saldo_actual'];
				$facProveedor['ProveedorFactura']['importe_venc1'] -= $recupero['LiquidacionCuotaRecupero']['saldo_actual'];
				if(!$oPROVFACT->save($facProveedor)) return false;
			}else{
				//la factura de liquidacion no tiene saldo para descontar -> generar una orden de pago anticipada
				parent::notificar("LA FACTURA DE LIQUIDACION " . $facProveedor['ProveedorFactura']['tipo_comprobante_desc'] . " NO TIENE SALDO PARA DESCONTAR");
				parent::rollback();
				return false;
			}
			
		}
		if(!empty($recupero['LiquidacionCuotaRecupero']['cliente_factura_id'])){
			$facCliente = $oCLIFACT->getFactura($recupero['LiquidacionCuotaRecupero']['cliente_factura_id'],true);
			if($facCliente['ClienteFactura']['saldo'] >= $recupero['LiquidacionCuotaRecupero']['comision_cobranza']){
				$facCliente['ClienteFactura']['importe_no_gravado'] -= $recupero['LiquidacionCuotaRecupero']['comision_cobranza'];
				$facCliente['ClienteFactura']['total_comprobante'] -= $recupero['LiquidacionCuotaRecupero']['comision_cobranza'];
				$facCliente['ClienteFactura']['importe_venc1'] -= $recupero['LiquidacionCuotaRecupero']['comision_cobranza'];
				$facCliente['ClienteFacturaDetalle'][0]['precio_unitario'] -= $recupero['LiquidacionCuotaRecupero']['comision_cobranza'];
				$facCliente['ClienteFacturaDetalle'][0]['precio_total'] -= $recupero['LiquidacionCuotaRecupero']['comision_cobranza'];
				if(!$oCLIFACT->saveAll($facCliente)) return false;
			}else{
				parent::notificar("LA FACTURA POR COMISIONES " . $facCliente['ClienteFactura']['tipo_comprobante_desc'] . " NO TIENE SALDO PARA DESCONTAR");
				parent::rollback();
				return false;
			}
		}
		
		#########################################################
		#ANULO EL COBRO
		#########################################################
		App::import('Model','Mutual.OrdenDescuentoCobro');
		$oCOBRO = new OrdenDescuentoCobro();			
		if(!$oCOBRO->borrarDetalle($recupero['LiquidacionCuotaRecupero']['orden_descuento_cobro_id'],true)){
			parent::notificar("NO SE PUDO ANULAR EL COBRO #" . $recupero['LiquidacionCuotaRecupero']['orden_descuento_cobro_id']);
			parent::rollback();
			return false;
		}
		
		#########################################################
		#BORRO LA ORDEN DE DESCUENTO
		#########################################################
		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();			
		
		if(!$oORDEN->eliminarOrden($recupero['LiquidacionCuotaRecupero']['orden_descuento_id'])){
			parent::notificar("NO SE PUDO ELIMINAR LA ORDEN DE DESCUENTO [RECAR #" . $recupero['LiquidacionCuotaRecupero']['orden_descuento_id']."]");
			parent::rollback();
			return false;
		}
		
		#########################################################
		#ACTUALIZO LA LIQUIDACION CUOTAS
		#########################################################
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();
		$liquiCuota = $oLC->read(null,$recupero['LiquidacionCuotaRecupero']['liquidacion_cuota_id']);
		
		$liquiCuota['LiquidacionCuota']['importe_debitado'] = 0;
		$liquiCuota['LiquidacionCuota']['para_imputar'] = 0;
		$liquiCuota['LiquidacionCuota']['imputada'] = 0;
		$liquiCuota['LiquidacionCuota']['orden_descuento_cobro_id'] = 0;
		$liquiCuota['LiquidacionCuota']['alicuota_comision_cobranza'] = 0;
		$liquiCuota['LiquidacionCuota']['comision_cobranza'] = 0;
		
		if(!$oLC->save($liquiCuota)){
			parent::notificar("NO SE PUDO ACTUALIZAR LA LIQUIDACION");
			parent::rollback();
			return false;
		}
		
		#########################################################
		#BORRO EL RECUPERO
		#########################################################
		if(!$this->del($id)){
			parent::notificar("NO SE PUDO BORRAR EL RECUPERO");
			parent::rollback();
			return false;			
		}else{
			parent::commit();
			return true;
		}

		
	}
	
	
	function anularByLiquidacion($liquidacionId,$proveedorId=null){
		
		$conditions = array();
		$conditions['LiquidacionCuotaRecupero.liquidacion_id'] = $liquidacionId;
		if(!empty($proveedorId)) $conditions['LiquidacionCuotaRecupero.proveedor_id'] = $proveedorId;
		
		$recuperos = $this->find('all',array('conditions' => $conditions));
		
		if(!empty($recuperos)){
			foreach($recuperos as $recupero){
				if(!$this->anular($recupero['LiquidacionCuotaRecupero']['id'])) return false;
			}
			return true;
		}else{
			parent::notificar("NO EXISTEN RECUPEROS PARA ANULAR!");
			return false;
		}
		
		
	}
	
	
	
	function getByLiquidacion($liquidacionId,$proveedorId=null,$socio_id = null,$cantidadCuotas = null){
		
		$sql = "SELECT 
				LiquidacionCuota.id,
				LiquidacionCuota.socio_id,
				GlobalDato_3.concepto_1,
				Persona.documento,
				Persona.apellido,
				Persona.nombre,
				Persona.id, 
				LiquidacionCuota.orden_descuento_id,
				LiquidacionCuota.orden_descuento_cuota_id,
				OrdenDescuento.tipo_orden_dto,
				OrdenDescuento.numero,
				OrdenDescuento.cuotas,
				OrdenDescuentoCuota.nro_cuota,
				GlobalDato_1.concepto_1,
				GlobalDato_2.concepto_1,
				LiquidacionCuota.periodo_cuota,
				LiquidacionCuota.importe,
				LiquidacionCuota.saldo_actual,
				Proveedor.razon_social_resumida,
				GlobalDato_4.concepto_1,
				OrdenDescuentoEmitida.tipo_orden_dto,
				OrdenDescuentoEmitida.numero,
				OrdenDescuentoEmitida.periodo_ini,
				OrdenDescuentoEmitida.importe_total,
				OrdenDescuentoEmitida.cuotas,
				OrdenDescuentoEmitida.importe_cuota,
				OrdenDescuentoEmitida.proveedor_id,				
				LiquidacionCuotaRecupero.*
				FROM liquidacion_cuota_recuperos AS LiquidacionCuotaRecupero
				INNER JOIN liquidacion_cuotas AS LiquidacionCuota ON (LiquidacionCuota.id = LiquidacionCuotaRecupero.liquidacion_cuota_id)
				INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = LiquidacionCuotaRecupero.liquidacion_id)
				INNER JOIN socios AS Socio ON (Socio.id = LiquidacionCuotaRecupero.socio_id)
				INNER JOIN personas AS Persona ON (Persona.id = Socio.persona_id)
				INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON (OrdenDescuentoCuota.id = LiquidacionCuotaRecupero.orden_descuento_cuota_id)
				INNER JOIN orden_descuentos AS OrdenDescuento ON (OrdenDescuento.id = LiquidacionCuota.orden_descuento_id)
				INNER JOIN orden_descuentos AS OrdenDescuentoEmitida ON (OrdenDescuentoEmitida.id = LiquidacionCuotaRecupero.orden_descuento_id)
				INNER JOIN proveedores AS Proveedor ON (Proveedor.id = OrdenDescuentoEmitida.proveedor_id)
				INNER JOIN global_datos AS GlobalDato_1 ON (GlobalDato_1.id = LiquidacionCuota.tipo_producto)
				INNER JOIN global_datos AS GlobalDato_2 ON (GlobalDato_2.id = LiquidacionCuota.tipo_cuota)
				INNER JOIN global_datos AS GlobalDato_3 ON (GlobalDato_3.id = Persona.tipo_documento)
				INNER JOIN global_datos AS GlobalDato_4 ON (GlobalDato_4.id = OrdenDescuentoEmitida.tipo_producto)
				WHERE 
				LiquidacionCuotaRecupero.liquidacion_id = $liquidacionId
				".(!empty($proveedorId) ? "AND LiquidacionCuotaRecupero.proveedor_id = $proveedorId" : "")."
				".(!empty($socio_id) ? "AND LiquidacionCuotaRecupero.socio_id = $socio_id" : "")."
				".(!empty($cantidadCuotas) ? "AND LiquidacionCuotaRecupero.cuotas = $cantidadCuotas" : "")."
				ORDER BY Persona.apellido,Persona.nombre, OrdenDescuento.id, OrdenDescuentoCuota.nro_cuota;";
		
//		debug($sql);
//		$conditions = array();
//		$conditions['LiquidacionCuotaRecupero.liquidacion_id'] = $liquidacionId;
//		if(!empty($proveedorId)) $conditions['LiquidacionCuotaRecupero.proveedor_id'] = $proveedorId;
//		
//		$recuperos = $this->find('all',array('conditions' => $conditions));
		$datos = $this->query($sql);
		
		$cuotas = array();
		
		if(!empty($datos)):
		
			App::import('Model','Mutual.OrdenDescuentoCuota');
			$oCUOTA = new OrdenDescuentoCuota();		
		
			foreach($datos as $ix => $dato){
				
//				debug($dato);
				
				$saldoActual = $oCUOTA->getSaldo($dato['LiquidacionCuota']['orden_descuento_cuota_id']);
				
				$dato['LiquidacionCuotaRecupero']['persona_id'] = $dato['Persona']['id'];
				$dato['LiquidacionCuotaRecupero']['persona_tdoc'] = $dato['GlobalDato_3']['concepto_1'];
				$dato['LiquidacionCuotaRecupero']['persona_ndoc'] = $dato['Persona']['documento'];
				$dato['LiquidacionCuotaRecupero']['persona_apenom'] = $dato['Persona']['apellido'] . ", " . $dato['Persona']['nombre'];
				$dato['LiquidacionCuotaRecupero']['orden_descuento_tipo_nro'] = $dato['OrdenDescuento']['tipo_orden_dto'] ." #".$dato['OrdenDescuento']['numero'];
				$dato['LiquidacionCuotaRecupero']['producto_concepto'] = $dato['GlobalDato_1']['concepto_1'] . " - " . $dato['GlobalDato_2']['concepto_1'];
				$dato['LiquidacionCuotaRecupero']['periodo'] = $dato['LiquidacionCuota']['periodo_cuota'];
				$dato['LiquidacionCuotaRecupero']['periodo_d'] = parent::periodo($dato['LiquidacionCuota']['periodo_cuota']);
				$dato['LiquidacionCuotaRecupero']['cuota'] = str_pad($dato['OrdenDescuentoCuota']['nro_cuota'],2,"0",STR_PAD_LEFT) . "/" . str_pad($dato['OrdenDescuento']['cuotas'],2,"0",STR_PAD_LEFT);
				$dato['LiquidacionCuotaRecupero']['saldo_liquidado'] = $dato['LiquidacionCuota']['saldo_actual'];
				$dato['LiquidacionCuotaRecupero']['saldo_actual'] = $saldoActual;
				$dato['LiquidacionCuotaRecupero']['orden_descuento_recupera_id'] = $dato['LiquidacionCuota']['orden_descuento_id'];
				
				$dato['LiquidacionCuotaRecupero']['orden_descuento_emitida_id'] = $dato['LiquidacionCuotaRecupero']['orden_descuento_id'];
				$dato['LiquidacionCuotaRecupero']['orden_descuento_emitida_producto'] = $dato['GlobalDato_4']['concepto_1'];
				$dato['LiquidacionCuotaRecupero']['orden_descuento_emitida_proveedor'] = $dato['Proveedor']['razon_social_resumida'];
				$dato['LiquidacionCuotaRecupero']['orden_descuento_emitida_proveedor_id'] = $dato['OrdenDescuentoEmitida']['proveedor_id'];
				$dato['LiquidacionCuotaRecupero']['orden_descuento_emitida_tipo_nro'] = $dato['OrdenDescuentoEmitida']['tipo_orden_dto'] ." #".$dato['OrdenDescuentoEmitida']['numero'];
				$dato['LiquidacionCuotaRecupero']['orden_descuento_emitida_periodo'] = $dato['OrdenDescuentoEmitida']['periodo_ini'];
				$dato['LiquidacionCuotaRecupero']['orden_descuento_emitida_periodo_d'] = parent::periodo($dato['OrdenDescuentoEmitida']['periodo_ini']);
				$dato['LiquidacionCuotaRecupero']['orden_descuento_emitida_cuotas'] = $dato['OrdenDescuentoEmitida']['cuotas'];
				$dato['LiquidacionCuotaRecupero']['orden_descuento_emitida_importe_total'] = $dato['OrdenDescuentoEmitida']['importe_total'];
				$dato['LiquidacionCuotaRecupero']['orden_descuento_emitida_importe_cuota'] = $dato['OrdenDescuentoEmitida']['importe_cuota'];
				
				$cuotas[$ix]['LiquidacionCuotaRecupero'] = $dato['LiquidacionCuotaRecupero'];
				
			}
			
		endif;
		
		
		
		return $cuotas;
		
	}
	
	
	function getImporteTotalRecupero($liquidacionId,$proveedorId = null){
		
		$sql = "SELECT 
					SUM(saldo_actual) AS saldo_actual 
				FROM liquidacion_cuota_recuperos AS LiquidacionCuotaRecupero
				WHERE
				LiquidacionCuotaRecupero.liquidacion_id = $liquidacionId
				".(!empty($proveedorId) ? "AND LiquidacionCuotaRecupero.proveedor_id = $proveedorId;" : ";");
		$imp = $this->query($sql);
		return (isset($imp[0][0]['saldo_actual']) ? $imp[0][0]['saldo_actual'] : 0);
	}
	
	
}

?>