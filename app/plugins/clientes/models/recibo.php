<?php
class Recibo extends ClientesAppModel{
	var $name = 'Recibo';
	
	function getRecibo($id=null,$detalle=true,$forma=true){
		
		$aRecibo = array();

		if(empty($id)) return $aRecibo;
		
                App::import('model','pfyj.Persona');
                $oPersonas = new Persona(); 
                App::import('model','pfyj.Socio');
                $oSocios = new Socio(); 
                App::import('model','clientes.Cliente');
                $oClientes = new Cliente();   
                App::import('model','cajabanco.BancoCuentaMovimiento');
                $oBancoMovimientos = new BancoCuentaMovimiento(); 
                
                
                App::import('model','clientes.ReciboDetalle');
                $oReciboDetalle = new ReciboDetalle();  
                App::import('model','clientes.ReciboForma');
                $oReciboForma = new ReciboForma(); 

                $oSolicitud = NULL;
                if(MODULO_V1){
                    App::import('model','v1.Solicitud');
                    $oSolicitud = new Solicitud();   
                }
                
                App::import('model','proveedores.Proveedor');
                $oProveedor = new Proveedor();                
                
//		$oPersonas = $this->importarModelo('Persona', 'pfyj');
//		$oSocios = $this->importarModelo('Socio', 'pfyj');
//		$oClientes = $this->importarModelo('Cliente', 'clientes');
//		$oBancoMovimientos = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
//		$this->Solicitud = $this->importarModelo('Solicitud', 'v1');
		
//		$oReciboDetalle = $this->importarModelo('ReciboDetalle', 'clientes');
//		$oReciboForma = $this->importarModelo('ReciboForma', 'clientes');

		$aRecibo = $this->read(null, $id);
                if(empty($aRecibo)){
                    return NULL;
                }
		
		// Proveedores
//		$oProveedor = $this->importarModelo('Proveedor', 'proveedores');
//    	$aProveedor = $oProveedor->getProveedor($aRecibo['Recibo']['proveedor_id']);
    	
		//		$liquidacion['Liquidacion']['organismo'] = parent::GlobalDato('concepto_1',$liquidacion['Liquidacion']['codigo_organismo']);

		

		
		$aRecibo['Recibo']['numero_string'] = $aRecibo['Recibo']['letra'] . "-" .$aRecibo['Recibo']['sucursal'] . "-" . $aRecibo['Recibo']['nro_recibo'];
		$aRecibo['Recibo']['numero_string2'] = $aRecibo['Recibo']['tipo_documento'] . " " . $aRecibo['Recibo']['letra'] . " " .$aRecibo['Recibo']['sucursal'] . "-" . $aRecibo['Recibo']['nro_recibo'];
		
		$aRecibo['Recibo']['importe_letra'] = 'Recibí la cantidad de Pesos ' . $this->num2letras($aRecibo['Recibo']['importe']);

    	$aRecibo['Recibo']['cuit'] = 'C.U.I.T.: ';
		$aRecibo['Recibo']['domicilio'] = '';
		$aRecibo['Recibo']['localidad'] = '';
		$aRecibo['Recibo']['iva_concepto'] = '';
		
		if(!empty($aRecibo['Recibo']['codigo_organismo'])) $aRecibo['Recibo']['razon_social'] = parent::GlobalDato('concepto_1',$aRecibo['Recibo']['codigo_organismo']);
		
		if(!empty($aRecibo['Recibo']['banco_id'])) $aRecibo['Recibo']['razon_social'] .= parent::getNombreBanco($aRecibo['Recibo']['banco_id']);
		
		if($aRecibo['Recibo']['persona_id'] > 0):
			$aPersona = $oPersonas->getPersona($aRecibo['Recibo']['persona_id']);
			$aRecibo['Recibo']['razon_social'] = $aPersona['Persona']['apenom']; 
			$aRecibo['Recibo']['cuit'] = $aPersona['Persona']['tdoc_ndoc'];
		 	$aRecibo['Recibo']['domicilio'] = $aPersona['Persona']['domicilio'];
		 	$aRecibo['Recibo']['iva_concepto'] = 'CONSUMIDOR FINAL';
		 endif;
			
		if($aRecibo['Recibo']['socio_id'] > 0):
			$aSocio = $oSocios->getPersonaBySocioID($aRecibo['Recibo']['socio_id']);
			$aPersona = $oPersonas->getPersona($aSocio['Persona']['id']);
			$aRecibo['Recibo']['razon_social'] = $aPersona['Persona']['apenom']; 
			$aRecibo['Recibo']['cuit'] = $aPersona['Persona']['tdoc_ndoc'];
		 	$aRecibo['Recibo']['domicilio'] = $aPersona['Persona']['domicilio'];
		 	$aRecibo['Recibo']['socio'] = $aRecibo['Recibo']['socio_id'];
		 	$aRecibo['Recibo']['iva_concepto'] = 'CONSUMIDOR FINAL';
		endif;
		
		if($aRecibo['Recibo']['cliente_id'] > 0):
			$aCliente = $oClientes->getCliente($aRecibo['Recibo']['cliente_id']);
		 	$aRecibo['Recibo']['razon_social'] = $aCliente['Cliente']['razon_social']; 
		 	$aRecibo['Recibo']['cuit'] = 'C.U.I.T.: ' . $aCliente['Cliente']['formato_cuit'];
		 	$aRecibo['Recibo']['domicilio'] = $aCliente['Cliente']['domicilio'];
		 	$aRecibo['Recibo']['iva_concepto'] = $aCliente['Cliente']['iva_concepto'];
		endif;
		
		if($aRecibo['Recibo']['nro_solicitud'] > 0 && !empty($oSolicitud)):
			$solicitud = $oSolicitud->getSolicitud($aRecibo['Recibo']['nro_solicitud']);
    		$aProveedor = $oProveedor->getProveedor($solicitud['Producto']['Proveedor']['idr']);
			$aCliente = $oClientes->getCliente($aProveedor['Proveedor']['cliente_id']);
		 	$aRecibo['Recibo']['razon_social'] = $aCliente['Cliente']['razon_social']; 
		 	$aRecibo['Recibo']['cuit'] = 'C.U.I.T.: ' . $aCliente['Cliente']['formato_cuit'];
		 	$aRecibo['Recibo']['domicilio'] = $aCliente['Cliente']['domicilio'];
		 	$aRecibo['Recibo']['iva_concepto'] = $aCliente['Cliente']['iva_concepto'];
		endif;
				
		if($aRecibo['Recibo']['anulado'] == 1):
		 	$aRecibo['Recibo']['razon_social'] = 'A N U L A D O'; 
		 	$aRecibo['Recibo']['cuit'] = '';
		 	$aRecibo['Recibo']['domicilio'] = '';
		 	$aRecibo['Recibo']['iva_concepto'] = '';
		 	$aRecibo['Recibo']['importe'] = 0.00;
		endif;
		
		$aRecibo['Recibo']['importe_detalle'] = $oReciboDetalle->getImporte($id);
		$aRecibo['Recibo']['importe_cobro'] = $oReciboForma->getImporte($id);
		$aRecibo['Recibo']['importe_cajabanco'] = $oBancoMovimientos->getImporteRecibo($id);
		$aRecibo['Recibo']['error'] = 0;
		if($aRecibo['Recibo']['importe'] != $aRecibo['Recibo']['importe_detalle'] || 
		   $aRecibo['Recibo']['importe'] != $aRecibo['Recibo']['importe_cobro'] || 
		   $aRecibo['Recibo']['importe'] != $aRecibo['Recibo']['importe_cajabanco'] ||
		   $aRecibo['Recibo']['importe_detalle'] != $aRecibo['Recibo']['importe_cobro'] ||
		   $aRecibo['Recibo']['importe_detalle'] != $aRecibo['Recibo']['importe_cajabanco'] ||
		   $aRecibo['Recibo']['importe_cobro'] != $aRecibo['Recibo']['importe_cajabanco']
		   ) $aRecibo['Recibo']['error'] = 1;		

		if($detalle) $aRecibo['Recibo']['detalle'] = $oReciboDetalle->getReciboDetalle($id);
		if($forma) $aRecibo['Recibo']['forma'] = $oReciboForma->getReciboFormaByRecibo($id);
		
		return $aRecibo;
    }

    
	function guardarRecibo($datos){
//		if(!isset($datos['Recibo']['renglonesSerialize'])):
//			return false;
//		endif;

		$renglones = base64_decode($datos['Recibo']['renglonesSerialize']);
		$renglones = unserialize($renglones);
		
		// Recibo Detalle
		$oReciboDetalle = $this->importarModelo('ReciboDetalle', 'clientes');

		// Recibo Factura
		$oReciboFactura = $this->importarModelo('ReciboFactura', 'clientes');

		// Recibo Forma
		$oReciboForma = $this->importarModelo('ReciboForma', 'clientes');

		// Liquidacion
		$oLiquidacion = $this->importarModelo('Liquidacion', 'mutual');
		
		// Liquidacion Intercambio
		$oLqdInterCambio = $this->importarModelo('LiquidacionIntercambio', 'mutual');
		
		// Caja y Banco Movimientos. ('Banco Cuenta Movimientos').
		$oBancoMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
			
		// Caja y Banco Cuentas. ('Banco Cuentas').
		$oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$cajaId = $oBancoCuenta->getCuentaCajaId(); // NO SE PARA QUE SE USA
		
		// Caja y Banco Conceptos. ('Banco Cuentas').
		$oBancoConcepto = $this->importarModelo('BancoConcepto', 'cajabanco');
		$cncBancoId = $oBancoConcepto->getConceptoByTipoId(2);
		$cncCajaId = 0;
		
		// Caja y Banco Cheques de Terceros. ('Banco Cheque Terceros').
		$oBancoChequeTercero = $this->importarModelo('BancoChequeTercero', 'cajabanco');
			
		// Tipo de Documento a utilizar ('Recibo')
		$oTipoDocumento = $this->importarModelo('TipoDocumento', 'config');
		
		// Busco el Numero de Recibo
		$nroRecibo = $oTipoDocumento->getNumero($datos['Recibo']['tipo_documento']);
		if($nroRecibo == 0):		
			parent::notificar("RECIBO BLOQUEADO POR OTRO USUARIO");
			return false;
		endif;
		$nroRecibo = str_pad($nroRecibo, 8, 0, STR_PAD_LEFT);

		// Establezco de donde es el Ingreso
		$aOrigen = array();
		$aOrigen['persona_id'] = (isset($datos['Recibo']['cabecera_persona_id']) ? $datos['Recibo']['cabecera_persona_id'] : 0);
		$aOrigen['socio_id']   = (isset($datos['Recibo']['cabecera_socio_id'])   ? $datos['Recibo']['cabecera_socio_id']   : 0);
		$aOrigen['cliente_id'] = (isset($datos['Recibo']['cabecera_cliente_id']) ? $datos['Recibo']['cabecera_cliente_id'] : 0);
		$aOrigen['banco_id']   = (isset($datos['Recibo']['cabecera_banco_id'])   ? $datos['Recibo']['cabecera_banco_id']   : null);
		$aOrigen['codigo_organismo']  = (isset($datos['Recibo']['cabecera_codigo_organismo'])  ? $datos['Recibo']['cabecera_codigo_organismo'] : null);
		$aOrigen['nro_solicitud'] = (isset($datos['Recibo']['cabecera_nro_solicitud']) ? $datos['Recibo']['cabecera_nro_solicitud'] : 0);
		
		$personaId = (isset($datos['Recibo']['cabecera_persona_id']) ? $datos['Recibo']['cabecera_persona_id'] : 0);
		$socioId   = (isset($datos['Recibo']['cabecera_socio_id'])   ? $datos['Recibo']['cabecera_socio_id']   : 0);
		$clienteId = (isset($datos['Recibo']['cabecera_cliente_id']) ? $datos['Recibo']['cabecera_cliente_id'] : 0);
		$bancoId   = (isset($datos['Recibo']['cabecera_banco_id'])   ? $datos['Recibo']['cabecera_banco_id']   : null);
		$codOrgan  = (isset($datos['Recibo']['cabecera_codigo_organismo'])  ? $datos['Recibo']['cabecera_codigo_organismo'] : null);
		$nroSolicitud = (isset($datos['Recibo']['cabecera_nro_solicitud']) ? $datos['Recibo']['cabecera_nro_solicitud'] : 0);
		
		// Armo la cabecera del Recibo
		$aRecibo = array(
			'id' => 0,
			'tipo_documento' => $datos['Recibo']['tipo_documento'],
			'letra' => $oTipoDocumento->getLetra($datos['Recibo']['tipo_documento']),
			'sucursal' => '0001',
			'nro_recibo' => $nroRecibo,
			'fecha_comprobante' => $datos['Recibo']['fecha_comprobante'],
			'persona_id' => $aOrigen['persona_id'],
			'socio_id' => $aOrigen['socio_id'],
			'cliente_id' => $aOrigen['cliente_id'],
			'banco_id' => $aOrigen['banco_id'],
			'codigo_organismo' => $aOrigen['codigo_organismo'],
			'nro_solicitud' => $aOrigen['nro_solicitud'],
			'importe' => $datos['Recibo']['importe_cobro'],
			'aporte_socio' => (isset($datos['Recibo']['aporte_socio']) ? $datos['Recibo']['aporte_socio'] : 0),
			'importe_cancela' => (isset($datos['Recibo']['importe_cancela']) ? $datos['Recibo']['importe_cancela'] : 0),
			'comentarios' => $datos['Recibo']['observacion']
		);
		
		$this->begin();
		if(!$this->save($aRecibo)):		
			parent::notificar("NO SE GENERO LA CABECERA DEL RECIBO");
			$this->rollback();
			$oTipoDocumento->unLookRegistro($datos['Recibo']['tipo_documento']);
			return false;
		endif;

		$nReciboId = $this->getLastInsertID();

		
		// Detalle del Recibo
		if(isset($datos['Recibo']['detalle'])):
			$tmpFactura = array();
			$facturas = array();
			$detalleCobro = array();
			$tmpAnticipo = array();
			$anticipos = array();
			$importeAnticipo = 0;
			foreach($datos['Recibo']['detalle']['check'] as $id => $importe){
				if($datos['Recibo']['detalle']['tipo'][$id] == 'FA' || $datos['Recibo']['detalle']['tipo'][$id] == 'SD' || $datos['Recibo']['detalle']['tipo'][$id] == 'ND'):
					$tmpFactura['id'] = 0;
					$tmpFactura['cliente_id'] = $aRecibo['cliente_id'];
					$tmpFactura['cliente_factura_id'] = $datos['Recibo']['detalle']['id'][$id];
					$tmpFactura['recibo_id'] = $nReciboId;
					$tmpFactura['tipo_cobro'] = 'FA';
					$tmpFactura['cliente_credito_id'] = 0;
					$tmpFactura['recibo_detalle_id'] = 0;
					$tmpFactura['importe'] = $datos['Recibo']['detalle']['importe_a_cobrar'][$id];
					$tmpFactura['concepto'] = $this->getConcepto($datos['Recibo']['detalle']['id'][$id]);
					array_push($facturas, $tmpFactura);
					array_push($detalleCobro, $tmpFactura);
				else:
					$tmpAnticipo['id'] = 0;
					$tmpAnticipo['cliente_id'] = $aRecibo['cliente_id'];
					$tmpAnticipo['cliente_factura_id'] = 0;
					$tmpAnticipo['recibo_id'] = $nReciboId;
					$tmpAnticipo['tipo_cobro'] = 'AN';
					$tmpAnticipo['cliente_credito_id'] = 0;
					$tmpAnticipo['recibo_detalle_id'] = 0;
					$tmpAnticipo['importe'] = $datos['Recibo']['detalle']['importe_a_cobrar'][$id] * (-1);
					if($datos['Recibo']['detalle']['tipo'][$id] == 'AN'):
						$tmpAnticipo['concepto'] = 'RECIBIDO POR ADELANTADO (ANTICIPO)';
						$tmpAnticipo['recibo_detalle_id'] = $datos['Recibo']['detalle']['id'][$id];
					else:
						$tmpAnticipo['tipo_cobro'] = 'NC';
						$tmpAnticipo['concepto'] = $this->getConcepto($datos['Recibo']['detalle']['id'][$id]);
						$tmpAnticipo['cliente_factura_id'] = $datos['Recibo']['detalle']['id'][$id];
						$tmpAnticipo['cliente_credito_id'] = $datos['Recibo']['detalle']['id'][$id];
					endif;
					array_push($anticipos, $tmpAnticipo);
					$tmpAnticipo['importe'] = $datos['Recibo']['detalle']['importe_a_cobrar'][$id];
					array_push($detalleCobro, $tmpAnticipo);
				endif;
			}
						
			$tmpFacturaAnticipo = array();
			$aFacturaAnticipo = array();
			foreach($anticipos as $claveA => $valorA){
				$saldos = round($anticipos[$claveA]['importe'],2);
				foreach($facturas as $claveF => $valorF){
					$anticipo = array();
					if($saldos > 0.00):
						if(round($facturas[$claveF]['importe'],2) > 0.00):
							$tmpFacturaAnticipo['cliente_id'] = $valorF['cliente_id'];
							$tmpFacturaAnticipo['cliente_factura_id'] = $valorF['cliente_factura_id'];
							$tmpFacturaAnticipo['recibo_id'] = $valorF['recibo_id'];
							$tmpFacturaAnticipo['cliente_credito_id'] = $anticipos[$claveA]['cliente_credito_id'];
							$tmpFacturaAnticipo['recibo_detalle_id'] = $anticipos[$claveA]['recibo_detalle_id'];
							if($facturas[$claveF]['importe'] >= $saldos):
								$tmpFacturaAnticipo['importe'] = $saldos;
								$facturas[$claveF]['importe'] -= $saldos;
								$anticipos[$claveA]['importe'] = 0.00;
								$saldos = 0.00;
							else:
								$tmpFacturaAnticipo['importe'] = $facturas[$claveF]['importe'];
								$anticipo[$claveA]['importe'] -= $facturas[$claveF]['importe'];
								$saldos -= $facturas[$claveF]['importe'];
								$facturas[$claveF]['importe'] = 0.00;
							endif;
							array_push($aFacturaAnticipo, $tmpFacturaAnticipo);
						endif;
					endif;
				}
			}
		
			$totalFactura = 0;
			$aFacturas = array();
			foreach($facturas as $claveF => $valorF){
				if($valorF['importe'] > 0):
					$totalFactura += $valorF['importe'];
					array_push($aFacturas, $valorF);
				endif;
			}

			if(!empty($aFacturas)):
				// Grabar las Facturas
				if(!$oReciboFactura->saveAll($aFacturas)):		
					parent::notificar("NO SE PUDO GRABAR EL DETALLE DE LAS FACTURAS");
					$this->rollback();
					$oTipoDocumento->unLookRegistro('REC');
					return false;
				endif;
			
//				// Grabar el Detalle del Recibo
//				if(!$oReciboDetalle->saveAll($aFacturas)):		
//					$this->rollback();
//					$oTipoDocumento->unLookRegistro('REC');
//					return false;
//				endif;
			endif;
				
			if(!empty($detalleCobro)):
				// Grabar el Detalle del Recibo
				if(!$oReciboDetalle->saveAll($detalleCobro)):		
					parent::notificar("NO SE PUDO GRABAR EL DETALLE DEL RECIBO");
					$this->rollback();
					$oTipoDocumento->unLookRegistro('REC');
					return false;
				endif;
			endif;
			
			if(!empty($aFacturaAnticipo)):
				if(!$oReciboFactura->saveAll($aFacturaAnticipo)):
					parent::notificar("NO SE PUDO GRABAR EL ANTICIPO");
					$this->rollback();
					$oTipoDocumento->unLookRegistro('REC');
					return false;
				endif;
			endif;

			$importeAnticipo = round($datos['Recibo']['importe_cobro'] - $totalFactura,2);
			if($importeAnticipo > 0):
				$anticipoDetalle = array(
					'id' => 0,
					'cliente_id' => $aRecibo['cliente_id'],
					'recibo_id' => $nReciboId,
					'tipo_cobro' => 'AN',
					'cliente_factura_id' => 0,
					'concepto' => 'RECIBIDO POR ADELANTADO (ANTICIPO)',
					'importe' => $importeAnticipo
				);
				
				if(!$oReciboDetalle->save($anticipoDetalle)):		
					parent::notificar("NO SE PUDO GRABAR EL DETALLE DEL ANTICIPO");
					$this->rollback();
					$oTipoDocumento->unLookRegistro('REC');
					return false;
				endif;

			endif;
		elseif(isset($datos['Recibo']['detalle_solicitud'])):
			foreach($datos['Recibo']['detalle_solicitud'] as $clave => $detalle):
				$datos['Recibo']['detalle_solicitud'][$clave]['recibo_id'] = $nReciboId;
			endforeach;
			if(!$oReciboDetalle->saveAll($datos['Recibo']['detalle_solicitud'])):
				$this->rollback();
				$oTipoDocumento->unLookRegistro($datos['Recibo']['tipo_documento']);
				return false;
			endif;
		else:
			$aReciboDetalle = array(
				'id' => 0,
				'recibo_id' => $nReciboId,
				'concepto' => $datos['Recibo']['observacion'],
				'importe' => $datos['Recibo']['importe_cobro']
			);
			if(!$oReciboDetalle->saveAll($aReciboDetalle)):
				$this->rollback();
				$oTipoDocumento->unLookRegistro($datos['Recibo']['tipo_documento']);
				return false;
			endif;
		endif;		
		
		// Armo los renglones de la Forma de cobro
    	$formaCobro = array();
    	$tmpCobro = array();
    	foreach($renglones as $valorP){
			if($valorP['Recibo']['forma_cobro'] == 'DB'):
				$tmpCobro = $this->formaCobroBanco($valorP['Recibo'], $aOrigen);
				$tmpCobro['banco_concepto_id'] = $cncBancoId;
			else:
				$tmpCobro = $this->formaCobroCaja($valorP['Recibo'], $aOrigen);
				$tmpCobro['banco_cuenta_id'] = $cajaId;
			endif;
			$tmpCobro['recibo_id'] = $nReciboId;
			$tmpCobro['descripcion'] = 'Recibo Nro.: ' . $nroRecibo;
			array_push($formaCobro, $tmpCobro);
		}
			
		// Grabar los movimiento de Caja y Banco
		foreach($formaCobro as $key => $valor){
			if(!$oBancoMovimiento->save($valor)):
				parent::notificar("NO SE PUDO GRABAR LOS MOVIMIENTO DE CAJA Y BANCO");
				$this->rollback();
				$oTipoDocumento->unLookRegistro($datos['Recibo']['tipo_documento']);
				return false;
			endif;
			$formaCobro[$key]['banco_cuenta_movimiento_id'] = $oBancoMovimiento->getLastInsertID();
		}

		
		// Grabar los valores del Cobro
		if(!$oReciboForma->saveAll($formaCobro)):		
			parent::notificar("NO SE PUDO GRABAR LOS VALORES DEL RECIBO");
			$this->rollback();
			$oTipoDocumento->unLookRegistro($datos['Recibo']['tipo_documento']);
			return false;
		endif;

		// Grabar los Cheques de Terceros
		$aChqTercero = $this->ChequeTercero($formaCobro, $nReciboId);
		foreach($aChqTercero as $key => $chqTercero){
			if(!$oBancoChequeTercero->save($chqTercero)):
				parent::notificar("NO SE PUDO GRABAR CHEQUES DE TERCEROS");
				$this->rollback();
				$oTipoDocumento->unLookRegistro($datos['Recibo']['tipo_documento']);
				return false;
			endif;
			$aChqTercero[$key]['id'] = $oBancoChequeTercero->getLastInsertID();
			$aBancoCuentaMovimiento = array(
				'id' => $chqTercero['banco_cuenta_movimiento_id'],
				'banco_cheque_tercero_id' => $aChqTercero[$key]['id']
			);
			if(!$oBancoMovimiento->save($aBancoCuentaMovimiento)):
				parent::notificar("NO SE PUDO GRABAR CHEQUES DE TERCERO EN LA CAJA");
				$this->rollback();
				$oTipoDocumento->unLookRegistro($datos['Recibo']['tipo_documento']);
				return false;
			endif;
		}
		
		// Actualizo la Liquidacion Intercambio con el recibo id
		if(isset($datos['Recibo']['liquidacion_intercambio_id']) && $datos['Recibo']['liquidacion_intercambio_id'] > 0):
			$aLiquidacionIntercambio = array(
				'id' => $datos['Recibo']['liquidacion_intercambio_id'],
				'recibo_id' => $nReciboId
			);
			// Grabar
			if(!$oLqdInterCambio->save($aLiquidacionIntercambio)):		
				$this->rollback();
				$oTipoDocumento->unLookRegistro($datos['Recibo']['tipo_documento']);
				return false;
			endif;
		elseif(isset($datos['Recibo']['liquidacion_id']) && $datos['Recibo']['liquidacion_id'] > 0):
			$aLiquidacionIntercambio = $oLqdInterCambio->find('all',array('conditions' => array('LiquidacionIntercambio.liquidacion_id' => $datos['Recibo']['liquidacion_id'])));
			foreach($aLiquidacionIntercambio as $LiquidacionIntercambio){
				$LiquidacionIntercambio['LiquidacionIntercambio']['recibo_id'] = $nReciboId;
			
				// Grabar
				if(!$oLqdInterCambio->save($LiquidacionIntercambio['LiquidacionIntercambio'])):		
					$this->rollback();
					$oTipoDocumento->unLookRegistro($datos['Recibo']['tipo_documento']);
					return false;
				endif;
			}	
			$aLiquidacion = array(
				'id' => $datos['Recibo']['liquidacion_id'],
				'recibo_id' => $nReciboId
			);
			// Grabar
			if(!$oLiquidacion->save($aLiquidacion)):		
				$this->rollback();
				$oTipoDocumento->unLookRegistro($datos['Recibo']['tipo_documento']);
				return false;
			endif;
		endif;

		$this->commit();
		$oTipoDocumento->putNumero($datos['Recibo']['tipo_documento']);
		return $nReciboId;
		
	}
    
        
	function anularReciboCtaCte($id){
        $datos = array();
		$datos['Recibo']['id'] = $id;
		if(!$this->anularRecibo($datos)):
			return false;
		endif;
		
		return true;
	}
	
	function anularRecibo($datos){
    	
    	$id = $datos['Recibo']['id'];
    	$aRecibo = array('id' => $id, 'anulado' => 1, 'nro_solicitud' => 0);
    	
		// Recibo Detalle
		$oReciboDetalle = $this->importarModelo('ReciboDetalle', 'clientes');

		// Recibo Factura
		$oReciboFactura = $this->importarModelo('ReciboFactura', 'clientes');

		// Recibo Forma
		$oReciboForma = $this->importarModelo('ReciboForma', 'clientes');

		// Liquidacion
		$oLiquidacion = $this->importarModelo('Liquidacion', 'mutual');
		
		// Liquidacion Intercambio
		$oLqdInterCambio = $this->importarModelo('LiquidacionIntercambio', 'mutual');
		
		// Caja y Banco Movimientos. ('Banco Cuenta Movimientos').
		$oBancoMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
			
		// Caja y Banco Cheques de Terceros. ('Banco Cheque Terceros').
		$oBancoChequeTercero = $this->importarModelo('BancoChequeTercero', 'cajabanco');
			
		$this->begin();
    	if(!$oReciboDetalle->deleteAll(array('ReciboDetalle.recibo_id' => $id))):
    		$this->rollback();
    		return false;
    	endif;
    	
    	if(!$oReciboFactura->deleteAll(array('ReciboFactura.recibo_id' => $id))):
    		$this->rollback();
    		return false;
    	endif;
    	
    	if(!$oReciboForma->deleteAll(array('ReciboForma.recibo_id' => $id))):
    		$this->rollback();
    		return false;
    	endif;
    	
    	if(!$oBancoMovimiento->deleteAll(array('BancoCuentaMovimiento.recibo_id' => $id))):
    		$this->rollback();
    		return false;
    	endif;
    	
    	if(!$oBancoChequeTercero->deleteAll(array('BancoChequeTercero.recibo_id' => $id))):
    		$this->rollback();
    		return false;
    	endif;
    	
    	// Actualizo la Liquidacion Intercambio con el recibo id
		if(isset($datos['Recibo']['liquidacion_intercambio_id']) && $datos['Recibo']['liquidacion_intercambio_id'] > 0):
    		$aLiquidacionIntercambio = array(
				'id' => $datos['Recibo']['liquidacion_intercambio_id'],
				'recibo_id' => 0
			);
			// Grabar
			if(!$oLqdInterCambio->save($aLiquidacionIntercambio)):		
				$this->rollback();
				return false;
			endif;
		elseif(isset($datos['Recibo']['liquidacion_id']) && $datos['Recibo']['liquidacion_id'] > 0):
			$aLiquidacionIntercambio = $oLqdInterCambio->find('all',array('conditions' => array('LiquidacionIntercambio.liquidacion_id' => $datos['Recibo']['liquidacion_id'])));
			foreach($aLiquidacionIntercambio as $LiquidacionIntercambio){
				$LiquidacionIntercambio['LiquidacionIntercambio']['recibo_id'] = 0;
			
				// Grabar
				if(!$oLqdInterCambio->save($LiquidacionIntercambio['LiquidacionIntercambio'])):		
					$this->rollback();
					return false;
				endif;
			}	
			$aLiquidacion = array(
				'id' => $datos['Recibo']['liquidacion_id'],
				'recibo_id' => 0
			);
			// Grabar
			if(!$oLiquidacion->save($aLiquidacion)):		
				$this->rollback();
				return false;
			endif;
		endif;

    	if(!$this->save($aRecibo)):
    		$this->rollback();
    		return false;
    	endif;
    	
		$this->commit();
		return true;
    }
    

    function formaCobroBanco($renglon, $aOrigen){
    	
    	$tmpCobro = array();
		$tmpCobro['id'] = 0;
		$tmpCobro['cliente_id'] = $aOrigen['cliente_id'];
		$tmpCobro['socio_id'] = $aOrigen['socio_id'];
		$tmpCobro['persona_id'] = $aOrigen['persona_id'];
		$tmpCobro['codigo_organismo'] = $aOrigen['codigo_organismo'];
		$tmpCobro['banco_id'] = $aOrigen['banco_id'];
		$tmpCobro['banco_cuenta_id'] = $renglon['banco_cuenta_id'];
		$tmpCobro['recibo_id'] = 0;
		$tmpCobro['numero_operacion'] = $renglon['numero_operacion'];
		$tmpCobro['fecha_operacion'] = $renglon['fecha_comprobante'];
		$tmpCobro['fecha_vencimiento'] = $renglon['fecha_comprobante'];
		
		$tmpCobro['banco_concepto_id'] = 0;
		$tmpCobro['tipo'] = 2;
		$tmpCobro['concepto'] = $renglon['denominacion'];

		$tmpCobro['destinatario'] = $renglon['destinatario'];
		$tmpCobro['descripcion'] = '';
		$tmpCobro['importe'] = $renglon['importe'];
		$tmpCobro['debe_haber'] = 0;
		$tmpCobro['forma_cobro'] = $renglon['forma_cobro'];
		$tmpCobro['banco_cuenta_movimiento_id'] = 0;
		$tmpCobro['descripcion_cobro'] = $renglon['forma_cobro_desc'];
		
		return $tmpCobro;
    }
		
    

    function formaCobroCaja($renglon, $aOrigen){
		
    	$tmpCobro = array();

    	$tmpCobro['id'] = 0;
		$tmpCobro['cliente_id'] = $aOrigen['cliente_id'];
		$tmpCobro['socio_id'] = $aOrigen['socio_id'];
		$tmpCobro['persona_id'] = $aOrigen['persona_id'];
		$tmpCobro['codigo_organismo'] = $aOrigen['codigo_organismo'];
		$tmpCobro['banco_id'] = $aOrigen['banco_id'];
		$tmpCobro['banco_cuenta_id'] = 0;
		$tmpCobro['recibo_id'] = 0;
		$tmpCobro['numero_operacion'] = (isset($renglon['numero_operacion']) ? $renglon['numero_operacion'] : null);
		$tmpCobro['fecha_operacion'] = (isset($renglon['fecha_comprobante']) ? $renglon['fecha_comprobante'] : null);
		$tmpCobro['fecha_vencimiento'] = (isset($renglon['fvenc']) ? $renglon['fvenc'] : null);
		$tmpCobro['fecha_cheque'] = (isset($renglon['fcobro']) ? $renglon['fcobro'] : null);
			
		$tmpCobro['banco_concepto_id'] = 0;
		$tmpCobro['tipo'] = 7; 
		$tmpCobro['concepto'] = 'CAJA';
		
		$tmpCobro['destinatario'] = (isset($renglon['destinatario']) ? $renglon['destinatario'] : null);
		$tmpCobro['descripcion'] = '';
		$tmpCobro['importe'] = $renglon['importe'];
		$tmpCobro['debe_haber'] = 0;
		$tmpCobro['forma_cobro'] = $renglon['forma_cobro'];
		$tmpCobro['banco_cuenta_movimiento_id'] = 0;
		$tmpCobro['descripcion_cobro'] = $renglon['forma_cobro_desc'];
		// para cheque de terceros
		$tmpCobro['cheque_banco_id'] = (isset($renglon['banco_id']) ? $renglon['banco_id'] : null);
		$tmpCobro['plaza'] = (isset($renglon['plaza']) ? $renglon['plaza'] : null);
		$tmpCobro['librador'] = (isset($renglon['librador']) ? $renglon['librador'] : null);
		
		return $tmpCobro;
    }
		
    
    function ChequeTercero($chqTercero, $nReciboId){
    	$aChqTercero = array();
    	$aTmpChqTrc = array();

    	foreach($chqTercero as $cheque){
    		if($cheque['forma_cobro'] == 'CT'):
    			$aTmpChqTrc['id'] = 0;
    			$aTmpChqTrc['recibo_id'] = $nReciboId;
    			$aTmpChqTrc['banco_cuenta_movimiento_id'] = $cheque['banco_cuenta_movimiento_id'];
    			$aTmpChqTrc['banco_id'] = $cheque['cheque_banco_id'];
    			$aTmpChqTrc['plaza'] = $cheque['plaza'];
    			$aTmpChqTrc['numero_cheque'] = $cheque['numero_operacion'];
    			$aTmpChqTrc['fecha_ingreso'] = $cheque['fecha_operacion'];
    			$aTmpChqTrc['fecha_cheque'] = $cheque['fecha_cheque'];
    			$aTmpChqTrc['fecha_vencimiento'] = $cheque['fecha_vencimiento'];
    			$aTmpChqTrc['librador'] = $cheque['librador'];
				$aTmpChqTrc['cliente_id'] = $cheque['cliente_id'];
				$aTmpChqTrc['socio_id'] = $cheque['socio_id'];
				$aTmpChqTrc['persona_id'] = $cheque['persona_id'];
				$aTmpChqTrc['codigo_organismo'] = $cheque['codigo_organismo'];
				$aTmpChqTrc['importe'] = $cheque['importe'];
				array_push($aChqTercero, $aTmpChqTrc);
			endif;
    	}
    	return $aChqTercero;
    }
    



    
	function grabarReciboCancelacion($datos, $TRANSACTION=false){

		if(!isset($datos['Recibo']['renglonesSerialize'])):
			return 0;
		endif;

		$renglones = base64_decode($datos['Recibo']['renglonesSerialize']);
		$renglones = unserialize($renglones);
		
		// Recibo Detalle
		$oReciboDetalle = $this->importarModelo('ReciboDetalle', 'clientes');

		// Recibo Factura
		$oReciboFactura = $this->importarModelo('ReciboFactura', 'clientes');

		// Recibo Forma
		$oReciboForma = $this->importarModelo('ReciboForma', 'clientes');

		// Liquidacion
		$oLiquidacion = $this->importarModelo('Liquidacion', 'mutual');
		
		// Liquidacion Intercambio
		$oLqdInterCambio = $this->importarModelo('LiquidacionIntercambio', 'mutual');
		
		// Caja y Banco Movimientos. ('Banco Cuenta Movimientos').
		$oBancoMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
			
		// Caja y Banco Cuentas. ('Banco Cuentas').
		$oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$cajaId = $oBancoCuenta->getCuentaCajaId(); // NO SE PARA QUE SE USA
		
		// Caja y Banco Conceptos. ('Banco Cuentas').
		$oBancoConcepto = $this->importarModelo('BancoConcepto', 'cajabanco');
		$cncBancoId = $oBancoConcepto->getConceptoByTipoId(2);
		$cncCajaId = 0;
		
		// Caja y Banco Cheques de Terceros. ('Banco Cheque Terceros').
		$oBancoChequeTercero = $this->importarModelo('BancoChequeTercero', 'cajabanco');
			
		// Tipo de Documento a utilizar ('Recibo')
		$oTipoDocumento = $this->importarModelo('TipoDocumento', 'config');
			
		// Busco el Numero de Recibo
		if($TRANSACTION):
			$nroRecibo = $oTipoDocumento->getNumero($datos['CancelacionOrden']['tipo_documento']);
			if($nroRecibo == 0):		
				return false;
			endif;
		else:
			$nroRecibo = $datos['CancelacionOrden']['numero_recibo'];
		endif;
		$nroRecibo = str_pad($nroRecibo, 8, 0, STR_PAD_LEFT);
		
		// Establezco de donde es el Ingreso
		$aOrigen = array();
		$aOrigen['persona_id'] = (isset($datos['CancelacionOrden']['cabecera_persona_id']) ? $datos['CancelacionOrden']['cabecera_persona_id'] : 0);
		$aOrigen['socio_id']   = (isset($datos['CancelacionOrden']['cabecera_socio_id'])   ? $datos['CancelacionOrden']['cabecera_socio_id']   : 0);
		$aOrigen['cliente_id'] = (isset($datos['CancelacionOrden']['cabecera_cliente_id']) ? $datos['CancelacionOrden']['cabecera_cliente_id'] : 0);
		$aOrigen['banco_id']   = (isset($datos['CancelacionOrden']['cabecera_banco_id'])   ? $datos['CancelacionOrden']['cabecera_banco_id']   : null);
		$aOrigen['codigo_organismo']  = (isset($datos['CancelacionOrden']['cabecera_codigo_organismo'])  ? $datos['CancelacionOrden']['cabecera_codigo_organismo'] : null);

		// Armo la cabecera del Recibo
		$aRecibo = array(
			'id' => 0,
			'tipo_documento' => $datos['CancelacionOrden']['tipo_documento'],
			'letra' => $oTipoDocumento->getLetra($datos['CancelacionOrden']['tipo_documento']),
			'sucursal' => '0001',
			'nro_recibo' => $nroRecibo,
			'fecha_comprobante' => $datos['CancelacionOrden']['fecha_comprobante'],
			'persona_id' => $aOrigen['persona_id'],
			'socio_id' => $aOrigen['socio_id'],
			'cliente_id' => $aOrigen['cliente_id'],
			'banco_id' => $aOrigen['banco_id'],
			'codigo_organismo' => $aOrigen['codigo_organismo'],
			'importe' => $datos['CancelacionOrden']['importe_cobro'],
			'comentarios' => (empty($datos['CancelacionOrden']['observacion']) ? 'CANCELACION' : $datos['CancelacionOrden']['observacion']) 
		);
		

		#ABRIR UNA TRANSACCION
		if($TRANSACTION)parent::begin();
			
		if(!$this->save($aRecibo)):		
			if($TRANSACTION){ parent::rollback(); $oTipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);}
			return 0;
		endif;

		$nReciboId = $this->getLastInsertID();
		
		// Armo los renglones de la Forma de cobro
    	$formaCobro = array();
    	$tmpCobro = array();
    	foreach($renglones as $valorP){
			if($valorP['CancelacionOrden']['forma_cobro'] == 'DB'):
				$tmpCobro = $this->formaCobroBanco($valorP['CancelacionOrden'], $aOrigen);
				$tmpCobro['banco_concepto_id'] = $cncBancoId;
			else:
				$tmpCobro = $this->formaCobroCaja($valorP['CancelacionOrden'], $aOrigen);
				$tmpCobro['banco_cuenta_id'] = $cajaId;
			endif;
			$tmpCobro['recibo_id'] = $nReciboId;
			$tmpCobro['descripcion'] = 'Recibo Nro.: ' . $nroRecibo;
			array_push($formaCobro, $tmpCobro);
		}
			
		// Grabar los movimiento de Caja y Banco
		foreach($formaCobro as $key => $valor){
			if(!$oBancoMovimiento->save($valor)):
				if($TRANSACTION){ parent::rollback(); $oTipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);}
				return false;
			endif;
			$formaCobro[$key]['banco_cuenta_movimiento_id'] = $oBancoMovimiento->getLastInsertID();
		}

		
		// Grabar los valores del Cobro
		if(!$oReciboForma->saveAll($formaCobro)):		
			if($TRANSACTION){ parent::rollback(); $oTipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);}
			return false;
		endif;

		// Grabar los Cheques de Terceros
		$aChqTercero = $this->ChequeTercero($formaCobro, $nReciboId);
		foreach($aChqTercero as $key => $chqTercero){
			if(!$oBancoChequeTercero->save($chqTercero)):
				if($TRANSACTION){ parent::rollback(); $oTipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);}
				return false;
			endif;
			$aChqTercero[$key]['id'] = $oBancoChequeTercero->getLastInsertID();
			$aBancoCuentaMovimiento = array(
				'id' => $chqTercero['banco_cuenta_movimiento_id'],
				'banco_cheque_tercero_id' => $aChqTercero[$key]['id']
			);
			if(!$oBancoMovimiento->save($aBancoCuentaMovimiento)):
				if($TRANSACTION){ parent::rollback(); $oTipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);}
				return false;
			endif;
		}
		
		// Genero la Factura del Proveedor, lo que hay que pagar por la cancelacion

		if($TRANSACTION) parent::commit();			
		if($TRANSACTION) $oTipoDocumento->putNumero($datos['CancelacionOrden']['tipo_documento']);
		return $nReciboId;
		
	}
    
	
	function recaudarOrdenCobroCaja($datos){

		if(!isset($datos['Recibo']['renglonesSerialize'])):
			return 0;
		endif;

		$renglones = base64_decode($datos['Recibo']['renglonesSerialize']);
		$renglones = unserialize($renglones);

		// Recibo Detalle
        App::import('model','clientes.ReciboDetalle');
        $oReciboDetalle = new ReciboDetalle();
		// $oReciboDetalle = $this->importarModelo('ReciboDetalle', 'clientes');

		// Recibo Factura
		$oReciboFactura = $this->importarModelo('ReciboFactura', 'clientes');

		// Recibo Forma
		$oReciboForma = $this->importarModelo('ReciboForma', 'clientes');

		// Liquidacion
		$oLiquidacion = $this->importarModelo('Liquidacion', 'mutual');
		
		// Liquidacion Intercambio
		$oLqdInterCambio = $this->importarModelo('LiquidacionIntercambio', 'mutual');
		
		// Caja y Banco Movimientos. ('Banco Cuenta Movimientos').
		$oBancoMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
			
		// Caja y Banco Cuentas. ('Banco Cuentas').
		$oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$cajaId = $oBancoCuenta->getCuentaCajaId(); 
		
		// Caja y Banco Conceptos. ('Banco Cuentas').
		$oBancoConcepto = $this->importarModelo('BancoConcepto', 'cajabanco');
		$cncBancoId = $oBancoConcepto->getConceptoByTipoId(2);
		$cncCajaId = 0;
		
		// Caja y Banco Cheques de Terceros. ('Banco Cheque Terceros').
		$oBancoChequeTercero = $this->importarModelo('BancoChequeTercero', 'cajabanco');
			
		// Tipo de Documento a utilizar ('Recibo')
		$oTipoDocumento = $this->importarModelo('TipoDocumento', 'config');
			
		// Busco el Numero de Recibo
		$nroRecibo = $oTipoDocumento->getNumero($datos['OrdenDescuentoCobro']['tipo_documento']);
		if($nroRecibo == 0):		
			return false;
		endif;
		$nroRecibo = str_pad($nroRecibo, 8, 0, STR_PAD_LEFT);
		
		// Establezco de donde es el Ingreso
		$aOrigen = array();
		$aOrigen['persona_id'] = (isset($datos['OrdenDescuentoCobro']['cabecera_persona_id']) ? $datos['OrdenDescuentoCobro']['cabecera_persona_id'] : 0);
		$aOrigen['socio_id']   = (isset($datos['OrdenDescuentoCobro']['cabecera_socio_id'])   ? $datos['OrdenDescuentoCobro']['cabecera_socio_id']   : 0);
		$aOrigen['cliente_id'] = (isset($datos['OrdenDescuentoCobro']['cabecera_cliente_id']) ? $datos['OrdenDescuentoCobro']['cabecera_cliente_id'] : 0);
		$aOrigen['banco_id']   = (isset($datos['OrdenDescuentoCobro']['cabecera_banco_id'])   ? $datos['OrdenDescuentoCobro']['cabecera_banco_id']   : null);
		$aOrigen['codigo_organismo']  = (isset($datos['OrdenDescuentoCobro']['cabecera_codigo_organismo'])  ? $datos['OrdenDescuentoCobro']['cabecera_codigo_organismo'] : null);

		// Armo la cabecera del Recibo
		$aRecibo = array(
			'id' => 0,
			'tipo_documento' => $datos['OrdenDescuentoCobro']['tipo_documento'],
			'letra' => $oTipoDocumento->getLetra($datos['OrdenDescuentoCobro']['tipo_documento']),
			'sucursal' => '0001',
			'nro_recibo' => $nroRecibo,
			'fecha_comprobante' => $datos['OrdenDescuentoCobro']['fecha_comprobante'],
			'persona_id' => $aOrigen['persona_id'],
			'socio_id' => $aOrigen['socio_id'],
			'cliente_id' => $aOrigen['cliente_id'],
			'banco_id' => $aOrigen['banco_id'],
			'codigo_organismo' => $aOrigen['codigo_organismo'],
			'importe' => $datos['OrdenDescuentoCobro']['importe_cobro'],
			'comentarios' => (empty($datos['OrdenDescuentoCobro']['observacion']) ? 'COBRO POR CAJA' : $datos['OrdenDescuentoCobro']['observacion']) 
		);
		

		#ABRIR UNA TRANSACCION
		parent::begin();
			
		if(!$this->save($aRecibo)):		
			parent::rollback(); 
			$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
			return false;
		endif;
		
		$nReciboId = $this->getLastInsertID();
		$datos['OrdenDescuentoCobro']['recibo_id'] = $nReciboId;
		
		// Armo los renglones de la Forma de cobro
    	$formaCobro = array();
    	$tmpCobro = array();
    	foreach($renglones as $valorP){
			if($valorP['OrdenDescuentoCobro']['forma_cobro'] == 'DB'):
				$tmpCobro = $this->formaCobroBanco($valorP['OrdenDescuentoCobro'], $aOrigen);
				$tmpCobro['banco_concepto_id'] = $cncBancoId;
			else:
				$tmpCobro = $this->formaCobroCaja($valorP['OrdenDescuentoCobro'], $aOrigen);
				$tmpCobro['banco_cuenta_id'] = $cajaId;
			endif;
			$tmpCobro['recibo_id'] = $nReciboId;
			$tmpCobro['descripcion'] = 'Recibo Nro.: ' . $nroRecibo;
			array_push($formaCobro, $tmpCobro);
		}
			
		foreach($formaCobro as $key => $valor){
			// Grabar los movimiento de Caja y Banco
			if(!$oBancoMovimiento->save($valor)):
				parent::rollback(); 
				$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
				return false;
			endif;
			$formaCobro[$key]['banco_cuenta_movimiento_id'] = $oBancoMovimiento->getLastInsertID();
			$valor['banco_cuenta_movimiento_id'] = $oBancoMovimiento->getLastInsertID();
			// Grabar los movimiento de Caja y Banco
			if(!$oReciboForma->save($valor)):
				parent::rollback(); 
				$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
				return false;
			endif;
		}

		

		// Grabar los Cheques de Terceros
		$aChqTercero = $this->ChequeTercero($formaCobro, $nReciboId);
		foreach($aChqTercero as $key => $chqTercero){
			if(!$oBancoChequeTercero->save($chqTercero)):
				parent::rollback(); 
				$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
				return false;
			endif;
			$aChqTercero[$key]['id'] = $oBancoChequeTercero->getLastInsertID();
			$aBancoCuentaMovimiento = array(
				'id' => $chqTercero['banco_cuenta_movimiento_id'],
				'banco_cheque_tercero_id' => $aChqTercero[$key]['id']
			);
			if(!$oBancoMovimiento->save($aBancoCuentaMovimiento)):
				parent::rollback(); 
				$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
				return false;
			endif;
		}

		# MODELOS A UTILIZAR PARA GRABAR EL COBRO
		$this->OrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');
		
		$this->OrdenDescuentoCobroCuota = $this->importarModelo('OrdenDescuentoCobroCuota', 'mutual');	

		$this->OrdenDescuentoCuota = $this->importarModelo('OrdenDescuentoCuota', 'mutual');
		
		$this->OrdenCajaCobro = $this->importarModelo('OrdenCajaCobro', 'mutual');	
		
		$this->OrdenCajaCobroCuota = $this->importarModelo('OrdenCajaCobroCuota', 'mutual');	

		$oCOMISION = $this->importarModelo('ProveedorComision', 'proveedores');		
		
		#GRABAR LA CABECERA DEL COBRO
		$cTipoCobro = 'MUTUTCOBCAJA';
		if($datos['OrdenDescuentoCobro']['proveedor_origen_id'] != MUTUALPROVEEDORID):
	   		// TIPO DE COBRO EN CASO DE QUE LA COBRANZA HAYA SIDO EN COMERCIO.
			$cTipoCobro = 'MUTUTCOBCACO';
		endif;
		
		$fechaCobro = parent::armaFecha($datos['OrdenDescuentoCobro']['fecha_comprobante']);
		$datos['OrdenDescuentoCobro']['tipo_cobro'] = $cTipoCobro;
		$datos['OrdenDescuentoCobro']['fecha'] = $fechaCobro;
		$datos['OrdenDescuentoCobro']['importe'] = $datos['OrdenCajaCobro']['orden_caja_cobro_importe'];
		$datos['OrdenDescuentoCobro']['socio_id'] = $datos['OrdenDescuentoCobro']['cabecera_socio_id'];
		$datos['OrdenDescuentoCobro']['proveedor_origen_fondo_id'] = $datos['OrdenDescuentoCobro']['proveedor_origen_id'];

		$periodo_cobro = date('Ym',strtotime($fechaCobro));
		$datos['OrdenDescuentoCobro']['periodo_cobro'] = $periodo_cobro;
		
		if(!$this->OrdenDescuentoCobro->save($datos)):
			parent::rollback(); 
			$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
			return false;
		endif;
		$this->OrdenDescuentoCobro->id = $this->OrdenDescuentoCobro->getLastInsertID();
		$datos['OrdenDescuentoCobro']['orden_descuento_cobro_id'] = $this->OrdenDescuentoCobro->getLastInsertID();
		
		# GRABO EL DETALLE DEL RECIBO DE INGRESO
		$aReciboDetalle = $oReciboDetalle->detalleReciboCaja($datos);
		if(!$oReciboDetalle->saveAll($aReciboDetalle)):
			parent::rollback(); 
			$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
			return false;
		endif;
		
		#GRABAR EL DETALLE DEL COBRO
		//saco las cuotas de la orden
		$cuotas = $this->OrdenCajaCobroCuota->findAllByOrdenCajaCobroId($datos['OrdenDescuentoCobro']['orden_caja_cobro_id']);

		foreach($cuotas as $cuota){
			
			$proveedor_id = $this->OrdenDescuentoCuota->field('proveedor_id',"OrdenDescuentoCuota.id = ".$cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id']);
			
			#CALCULO LA COMISION POR LA COBRANZA
			$comision = $this->OrdenDescuentoCobroCuota->calcularComisionCobranza($cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'],$cuota['OrdenCajaCobroCuota']['importe_abonado']);
				
			#GUARDO EL DETALLE DEL PAGO DE LA CUOTA
			$cobroCuota = array();
			$cobroCuota['OrdenDescuentoCobroCuota'] = array();
			$cobroCuota['OrdenDescuentoCobroCuota']['periodo_cobro'] = $periodo_cobro;
			$cobroCuota['OrdenDescuentoCobroCuota']['orden_descuento_cobro_id'] = $this->OrdenDescuentoCobro->id;
			$cobroCuota['OrdenDescuentoCobroCuota']['orden_descuento_cuota_id'] = $cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'];
			$cobroCuota['OrdenDescuentoCobroCuota']['importe'] = $cuota['OrdenCajaCobroCuota']['importe_abonado'];
			$cobroCuota['OrdenDescuentoCobroCuota']['proveedor_id'] = $proveedor_id;
			$cobroCuota['OrdenDescuentoCobroCuota']['alicuota_comision_cobranza'] = $comision['alicuota'];
			$cobroCuota['OrdenDescuentoCobroCuota']['comision_cobranza'] = $comision['comision'];
			
			
			if($this->OrdenDescuentoCobroCuota->save($cobroCuota)){
				$flag = true;
				$datos['OrdenDescuentoCobro']['orden_descuento_cobro_cuota_id'] = $this->OrdenDescuentoCobroCuota->getLastInsertID();
				$datos['OrdenDescuentoCobro']['importe_cuota'] = $cuota['OrdenCajaCobroCuota']['importe_abonado'];
				#MARCO LA CUOTA COMO PAGADA SI LA PAGA TOTALMENTE
				if($cuota['OrdenCajaCobroCuota']['importe_abonado'] == $cuota['OrdenCajaCobroCuota']['importe']){
					$this->OrdenDescuentoCuota->id = $cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'];
					if(!$this->OrdenDescuentoCuota->saveField('estado','P')){
						$flag = false;
						break;
					}
					if(!$this->__setPeriodoCobro($cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'],$periodo_cobro)){
						$flag = false;
						break;				
					}					
				}
			}else{
				$flag = false;
				break;				
			}
			
			$this->OrdenDescuentoCobroCuota->id = 0;
		}

		if(!$flag):
			parent::rollback(); 
			$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
			return false;
		endif;
		
		# MODELOS A UTILIZAR PARA EL PROVEEDOR
                $oProveedor = $this->importarModelo('Proveedor', 'proveedores');
                
		$this->ProveedorFactura = $this->importarModelo('ProveedorFactura', 'proveedores');
		
		$this->ClienteFactura = $this->importarModelo('ClienteFactura', 'clientes');
		
    	// Factura Detalle
    	$this->FacturaDetalle = $this->importarModelo('ClienteFacturaDetalle', 'clientes');
		
    	# TRAIGO DE LA ORDEN DESCUENTO COBRO CUOTAS ACUMULADO POR PROVEEDOR LOS MONTOS A FACTURAR CORRESPONDIENTE A CADA UNO INVOLUCRADO EN EL COBRO.
		$aCobroProveedor = $this->__getCobroCuotaByProveedor($this->OrdenDescuentoCobro->id);

		$importe_anticipo = 0;
		$importe_contado = 0;
		// Busco el Numero de la Factura
		$nroFactura = $oTipoDocumento->getNumero('FAC');
		if($nroFactura == 0):
			parent::rollback(); 
			$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
			return false;
		endif;
		$nroFactura -= 1;
		$nCantidadFactura = 0;

		foreach($aCobroProveedor as $aCobro):
			$aCobro['orden_descuento_cobro_cuotas']['orden_caja_cobro_id'] = $datos['OrdenDescuentoCobro']['orden_caja_cobro_id'];
			$aCobro['orden_descuento_cobro_cuotas']['fecha_comprobante'] = $fechaCobro;
			$aCobro['orden_descuento_cobro_cuotas']['periodo_cobro'] = $periodo_cobro;
			if($aCobro['orden_descuentos']['proveedor_id'] != MUTUALPROVEEDORID):
			   	if($aCobro['orden_descuentos']['proveedor_id'] != $datos['OrdenDescuentoCobro']['proveedor_origen_id']):
			   		// PREPARO LA ORDEN DE PAGO DE ANTICIPO EN CASO DE QUE LA COBRANZA HAYA SIDO EN COMERCIO.
					if(isset($datos['OrdenDescuentoCobro']['compensa_pago']) && $datos['OrdenDescuentoCobro']['proveedor_origen_id'] != MUTUALPROVEEDORID):
						$importe_anticipo += $aCobro[0]['importe'];
					endif;
					// GRABO LA FACTURA DEL PROVEEDOR
					if($aCobro[0]['importe'] > 0):
						$aFacturaProveedor = $this->__getFacturaProveedor($aCobro);
						if(!$this->ProveedorFactura->save($aFacturaProveedor)):
							$flag = false;
							break;				
						endif;
					endif;
					// GRABO LA FACTURA DEL CLIENTE POR LA COMISION
                                        $aProveedor = $oProveedor->getProveedor($aCobro['orden_descuento_cobro_cuotas']['proveedor_id']);
                                        if($aCobro[0]['comision_cobranza'] > 0 && $aProveedor['Proveedor']['cliente_id'] > 0):
						$nCantidadFactura += 1;
						$nroFactura += 1;
						$aFacturaCliente = $this->__getFacturaCliente($aCobro);
						$aFacturaCliente['numero_comprobante'] = str_pad($nroFactura, 8, 0, STR_PAD_LEFT);
						if(!$this->ClienteFactura->save($aFacturaCliente)):
							$flag = false;
							break;				
						endif;
						$aFacturaCliente['factura_detalle']['cliente_factura_id'] = $this->ClienteFactura->getLastInsertID();
						if(!$this->FacturaDetalle->save($aFacturaCliente['factura_detalle'])):
							$flag = false;
							break;				
						endif;
						$this->ClienteFactura->id = 0;
					endif;
				else:
					// PREPARO UN PAGO DE CONTADO SI FUE RECAUDADO EN ESE COMERCIO
					$importe_contado += $aCobro[0]['importe'];
                                endif;
			else:
				// PREPARO LA ORDEN DE PAGO DE ANTICIPO EN CASO DE QUE LA COBRANZA HAYA SIDO EN COMERCIO.
				if(isset($datos['OrdenDescuentoCobro']['compensa_pago']) && $datos['OrdenDescuentoCobro']['proveedor_origen_id'] != MUTUALPROVEEDORID):
					$importe_anticipo += $aCobro[0]['importe'];
				endif;
			endif;
		endforeach;
		
		if(!$flag):
			parent::rollback(); 
			$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
			$oTipoDocumento->unLookRegistro('FAC');
			return false;
		endif;
		
		// GENERO EL PAGO DE CONTADO SI CORRESPONDE
		if($importe_contado > 0 && $datos['OrdenDescuentoCobro']['proveedor_origen_id'] != MUTUALPROVEEDORID):
	    	$aPagoContado = array(
    			'caja_id' => $cajaId,
				'fecha_operacion' => $fechaCobro,
				'importe' => $importe_contado,
				'orden_caja_cobro_id' => $datos['OrdenDescuentoCobro']['orden_caja_cobro_id'],
    			'orden_descuento_cobro_id' => $datos['OrdenDescuentoCobro']['orden_descuento_cobro_id']
	    	);
    		$aContado = $this->__contado($aPagoContado);
			if(!$oBancoMovimiento->save($aContado)):
				parent::rollback(); 
				$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
				$oTipoDocumento->unLookRegistro('FAC');
				$oTipoDocumento->unLookRegistro('OPA');
				return false;
			endif;
			$datos['OrdenDescuentoCobro']['banco_cuenta_movimiento_id'] = $oBancoMovimiento->getLastInsertID();
		endif;
		
		# GENERO UNA ORDEN DE PAGO ANTICIPADO SI CORRESPONDE
		if($importe_anticipo > 0 && $datos['OrdenDescuentoCobro']['proveedor_origen_id'] != MUTUALPROVEEDORID):
			// Llamo a los modelos a utilizar
			// Orden de Pago Cabecera
			$this->OrdenPago = $this->importarModelo('OrdenPago', 'proveedores');
			
			// Orden de Pago Detalle
			$this->OrdenPagoDetalle = $this->importarModelo('OrdenPagoDetalle', 'proveedores');
			
			// Orden de Pago Facturas
			$this->OrdenPagoFactura = $this->importarModelo('OrdenPagoFactura', 'proveedores');
			
			// Orden de Pago Valores de Cobros
			$this->OrdenPagoForma = $this->importarModelo('OrdenPagoForma', 'proveedores');
			
			$cncCajaId = 0;
			
			$aOrdenPago = $this->getOrdenPago();
			// Busco el Numero de la Orden de Pago
			$nroOPago = $oTipoDocumento->getNumero('OPA');
			if($nroOPago == 0):
				parent::rollback(); 
				$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
				$oTipoDocumento->unLookRegistro('FAC');
				$oTipoDocumento->unLookRegistro('OPA');
				return false;
			endif;
			$aAnticipo = array(
				'proveedor_id' => $datos['OrdenDescuentoCobro']['proveedor_origen_id'],
				'nro_orden_pago' => $nroOPago,
				'fecha_comprobante' => $fechaCobro,
				'importe' => $importe_anticipo,
				'caja_id' => $cajaId,
				'cnc_caja_id' => 0,
				'orden_caja_cobro_id' => $datos['OrdenDescuentoCobro']['orden_caja_cobro_id'],
				'orden_descuento_cobro_id' => $datos['OrdenDescuentoCobro']['orden_descuento_cobro_id'],
				'comentario' => ''
			);
			$aOrdenPagoAnticipo = $this->__getPagoAnticipado($aAnticipo);
			if(!$this->OrdenPago->save($aOrdenPagoAnticipo)):
				parent::rollback(); 
				$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
				$oTipoDocumento->unLookRegistro('FAC');
				$oTipoDocumento->unLookRegistro('OPA');
				return false;
			endif;
			$nOPagoId = $this->OrdenPago->getLastInsertID();
			$datos['OrdenDescuentoCobro']['orden_pago_id'] = $this->OrdenPago->getLastInsertID();
			$aOrdenPagoAnticipo['detalle']['orden_pago_id'] = $this->OrdenPago->getLastInsertID();
			$aOrdenPagoAnticipo['forma_pago']['orden_pago_id'] = $this->OrdenPago->getLastInsertID();
			if(!$this->OrdenPagoDetalle->save($aOrdenPagoAnticipo['detalle'])):
				parent::rollback(); 
				$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
				$oTipoDocumento->unLookRegistro('FAC');
				$oTipoDocumento->unLookRegistro('OPA');
				return false;
			endif;
			if(!$oBancoMovimiento->save($aOrdenPagoAnticipo['forma_pago'])):
				parent::rollback(); 
				$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
				$oTipoDocumento->unLookRegistro('FAC');
				$oTipoDocumento->unLookRegistro('OPA');
				return false;
			endif;
			$aOrdenPagoAnticipo['forma_pago']['banco_cuenta_movimiento_id'] = $oBancoMovimiento->getLastInsertID();
			if(!$this->OrdenPagoForma->save($aOrdenPagoAnticipo['forma_pago'])):
				parent::rollback(); 
				$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
				$oTipoDocumento->unLookRegistro('FAC');
				$oTipoDocumento->unLookRegistro('OPA');
				return false;
			endif;
			
		endif;
		#MARCO LA ORDEN DE COBRO POR CAJA COMO PROCESADA
		$flag = $this->OrdenCajaCobro->marca_procesada($datos['OrdenDescuentoCobro']['orden_caja_cobro_id']);
		$aOrdenCajaCobro = array(
			'id' => $datos['OrdenDescuentoCobro']['orden_caja_cobro_id'],
			'orden_descuento_cobro_id' =>  $datos['OrdenDescuentoCobro']['orden_descuento_cobro_id'],
			'orden_pago_id' => $datos['OrdenDescuentoCobro']['orden_pago_id'],
			'banco_cuenta_movimiento_id' => $datos['OrdenDescuentoCobro']['banco_cuenta_movimiento_id'],
			'recibo_id' => $datos['OrdenDescuentoCobro']['recibo_id'],
			'importe_contado' => $importe_contado,
			'importe_orden_pago' => $importe_anticipo
		);
		$flag = $this->OrdenCajaCobro->save($aOrdenCajaCobro);

		
		########################
		# Genero la Provedor Liquidaciones con el id de la orden descuento cobro.
		########################
/*
 * A pedido de M22S no tiene que grabar la comision de Comercio, la tabla no permite grabar el campo cliente_id en 0 o NULL.
 * Es una tabla que no tiene importancia, era solo para control. No es necesario grabar los datos en esta tabla.
 * esta funcion queda obsoleta.
		$this->ProveedorLiquidacion = $this->importarModelo('ProveedorLiquidacion', 'proveedores');
		if(!$this->ProveedorLiquidacion->grabarLiquidacionByCaja($datos['OrdenDescuentoCobro']['orden_descuento_cobro_id'])):
			$this->rollback();
			$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
			$oTipoDocumento->unLookRegistro('FAC');
			$oTipoDocumento->unLookRegistro('OPA');
			return false;
		endif;
*/		
		
		########################
		#TODO BIEN --> CERRAR LA TRANSACCION
		########################
		if($flag):
			$this->commit();
			# ACUMULO LOS NUMERO DE FACTURAS QUE HE GENERADO
			$oTipoDocumento->putNumero($datos['OrdenDescuentoCobro']['tipo_documento']);
			$oTipoDocumento->putNumero('FAC', $nCantidadFactura);
			$oTipoDocumento->putNumero('OPA');
		else: 
			$this->rollback();
			$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
			$oTipoDocumento->unLookRegistro('FAC');
			$oTipoDocumento->unLookRegistro('OPA');
			return false;
		endif;

		return $nReciboId;		
	}


	/**
	 * traer la suma de cada proveedor de las cuotas cobradas para facturarlas y luego pagarlas.
	 * Tambien se genera la comision por cobranza.
	 */
	function __getCobroCuotaByProveedor($OrdenDescuentoCobroId){
//		$sql = "SELECT
//				proveedor_id,
//				orden_descuento_cobro_id,
//				SUM(importe) AS importe,
//				SUM(comision_cobranza) AS comision_cobranza
//				FROM orden_descuento_cobro_cuotas
//				WHERE orden_descuento_cobro_id = '$OrdenDescuentoCobroId'
//				GROUP BY proveedor_id";

		$sql = "SELECT  global_datos.concepto_1,
				globaldatos.concepto_1,
				proveedores.razon_social,
				orden_descuentos.*,
				orden_descuento_cobro_cuotas.orden_descuento_cobro_id,
				SUM(orden_descuento_cobro_cuotas.importe) AS importe,
				SUM(orden_descuento_cobro_cuotas.comision_cobranza) AS comision_cobranza
				FROM orden_descuento_cobro_cuotas
				inner join proveedores
				on proveedores.id = proveedor_id
				inner join orden_descuento_cuotas
				on orden_descuento_cuotas.id = orden_descuento_cobro_cuotas.orden_descuento_cuota_id
				inner join orden_descuentos
				on orden_descuentos.id = orden_descuento_cuotas.orden_descuento_id
				inner join global_datos
				on global_datos.id = orden_descuentos.tipo_producto
				inner join global_datos as globaldatos
				on globaldatos.id = global_datos.concepto_2
				WHERE orden_descuento_cobro_cuotas.orden_descuento_cobro_id = '$OrdenDescuentoCobroId'
				GROUP BY orden_descuentos.id";
//				order by razon_social";
				
		$aOrdenProveedor = $this->query($sql);
		
		foreach($aOrdenProveedor as $key => $aOrDescuento):
			$OrdenDescuentoId = $aOrDescuento['orden_descuentos']['id'];
			$sqlCuotas = "select	OrdenDescuentoCuota.nro_cuota as nro_cuota, OrdenDescuentoCuota.periodo
							from	orden_descuento_cuotas OrdenDescuentoCuota
							inner	join orden_caja_cobro_cuotas OrdenCajaCobroCuota
							on	OrdenDescuentoCuota.id = OrdenCajaCobroCuota.orden_descuento_cuota_id
							where	OrdenDescuentoCuota.orden_descuento_id = '$OrdenDescuentoId'";
			$aOrdenDescuentoCuotas = $this->query($sqlCuotas);
			
			$cuotas = Set::extract('/OrdenDescuentoCuota/nro_cuota',$aOrdenDescuentoCuotas);
			$strDesc = implode('-', $cuotas) ."/" . $aOrDescuento['orden_descuentos']['cuotas'];

			$periodo = Set::extract('/OrdenDescuentoCuota/periodo', $aOrdenDescuentoCuotas);
			foreach($periodo as $clave => $valor):
				$periodo[$clave] = substr($valor,0,4) . "/" . substr($valor,-2);
			endforeach;
			$strPeriodo = implode('-', $periodo);

			$aOrdenProveedor[$key]['concepto'] = 'EXPTE: ' . $aOrDescuento['orden_descuentos']['numero'] . ' - ' . $aOrDescuento['global_datos']['concepto_1'] . ' - ctas: ' . $strDesc;
			if($aOrDescuento['orden_descuentos']['tipo_orden_dto'] == 'CMUTU'):
				$aOrdenProveedor[$key]['concepto'] = $aOrDescuento['globaldatos']['concepto_1'] . ' - PER.: ' . $strPeriodo;
			endif; 
		endforeach;
		
//		return $this->query($sql);
		return $aOrdenProveedor;
					
	}
	
	
	/**
	 * actualiza el periodo de la cuota al periodo del cobro, si el periodo de cobro es menor al de la cuota
	 * mueve los periodos de la cuota al periodo de cobro y guarda el periodo original
//	 * @param unknown_type $orden_descuento_cuota_id
//	 * @param unknown_type $periodo_cobro
	 */
	function __setPeriodoCobro($orden_descuento_cuota_id,$periodo_cobro){
		/*App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();
		$periodoCuota = $oCUOTA->getPeriodo($orden_descuento_cuota_id);
		if($periodo_cobro < $periodoCuota && !empty($periodoCuota)){
			$oCUOTA->id = $orden_descuento_cuota_id;
			$periodo_origen = $oCUOTA->read('periodo',$orden_descuento_cuota_id);
			if(!$oCUOTA->saveField('periodo_origen',$periodoCuota)) return false;
			if(!$oCUOTA->saveField('periodo',$periodo_cobro)) return false;
		}*/		
		return true;				
	}
	
	
	function __getFacturaProveedor($factura){
		
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
			'punto_venta_comprobante' => '0001',
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
	
	
	function __getFacturaCliente($factura){
            // Proveedores
            $oProveedor = $this->importarModelo('Proveedor', 'proveedores');
            $aProveedor = $oProveedor->getProveedor($factura['orden_descuentos']['proveedor_id']);

            // Clientes
            $oCliente = $this->importarModelo('Cliente', 'clientes');
            $aCliente = $oCliente->getCliente($aProveedor['Proveedor']['cliente_id']);

            // Tipo de Comprobante o Documento
            $oTipoDocumento = $this->importarModelo('TipoDocumento', 'config');
            $aComprobante = $oTipoDocumento->getComprobante('FAC');
    	
            $glb = $this->getGlobalDato('entero_1','CONTCLIEFACT');

            return $aFactura = array(
                'id' => 0,
                'tipo' => 'FA',
                'cliente_id' => $aCliente['Cliente']['id'],
                'fecha_comprobante' => $factura['orden_descuento_cobro_cuotas']['fecha_comprobante'],
                'tipo_comprobante' => 'FAC',
                'letra_comprobante' => $aComprobante['letra'],
                'punto_venta_comprobante' => str_pad($aComprobante['sucursal'], 4, 0, STR_PAD_LEFT),
                'numero_comprobante' => '0',
                'importe_no_gravado' => $factura[0]['comision_cobranza'],
                'total_comprobante' => $factura[0]['comision_cobranza'],
//			'co_plan_cuenta_id' => $aCliente['Cliente']['co_plan_cuenta_id'],
                'vencimiento1' => $factura['orden_descuento_cobro_cuotas']['fecha_comprobante'],
                'importe_venc1' => $factura[0]['comision_cobranza'],
                'estado' => 'A',
//			'concepto_gasto' => $aCliente['Cliente']['concepto_gasto'],
                'cliente_tipo_asiento_id' => $glb['GlobalDato']['entero_1'],
                'orden_descuento_id' => $factura['orden_descuentos']['id'],
                    'orden_caja_cobro_id' => $factura['orden_descuento_cobro_cuotas']['orden_caja_cobro_id'],
                    'orden_descuento_cobro_id' => $factura['orden_descuento_cobro_cuotas']['orden_descuento_cobro_id'],
                    'comentario' => $factura['concepto'],

                    'factura_detalle' => array(
                            'id' => 0,
                            'cliente_factura_id' => 0,
                            'producto' => 'COMISION ' . $factura['concepto'],
                            'cantidad' => 1,
                            'precio_unitario' => $factura[0]['comision_cobranza'],
                            'precio_total' => $factura[0]['comision_cobranza']
                    )
            );
		
	}
	

	function __getPagoAnticipado($OPago){
			
		$this->Proveedor = $this->importarModelo('Proveedor', 'proveedores');
		$razon_social = $this->Proveedor->getRazonSocial($OPago['proveedor_id']);
		 
		// Armo la Cabecera de la Orden de pago
		$ordenPago = array(
			'id' => 0,
			'nro_orden_pago' => $OPago['nro_orden_pago'],
			'fecha_pago' => $OPago['fecha_comprobante'],
			'proveedor_id' => $OPago['proveedor_id'],
			'importe' => $OPago['importe'],
			'comentario' => 'COBRADO EN COMERCIO',
		
			'detalle' => array(
				'id' => 0,
				'proveedor_id' => $OPago['proveedor_id'],
				'orden_pago_id' => 0,
				'tipo_pago' => 'AN',
				'proveedor_factura_id' => 0,
				'importe' => $OPago['importe']
			),

			'forma_pago' => array(
				'id' => 0,
				'proveedor_id' => $OPago['proveedor_id'],
				'banco_cuenta_id' => $OPago['caja_id'],
				'orden_pago_id' => 0,
				'numero_operacion' => '',
				'fecha_operacion' => $OPago['fecha_comprobante'],
				'fecha_vencimiento' => $OPago['fecha_comprobante'],
				'banco_concepto_id' => $OPago['cnc_caja_id'],
				'tipo' => 7,
				'concepto' => 'CAJA',
				'destinatario' => $razon_social . ' (ANTICIPO)',
				'descripcion' => 'Orden Pago Nro.: ' . str_pad($OPago['nro_orden_pago'], 8, 0, STR_PAD_LEFT),
				'importe' => $OPago['importe'],
				'debe_haber' => 1,
				'forma_pago' => 'EF',
				'banco_cuenta_movimiento_id' => 0,
				'descripcion_pago' => 'EFECTIVO'
			),
		);
		
		return $ordenPago;
	}


	function __contado($datos){
	
		// Armo los renglones de la Forma de cobro
    	$formaCobro = array();

    	$formaCobro['id'] = 0;
		$formaCobro['banco_cuenta_id'] = $datos['caja_id'];
		$formaCobro['fecha_operacion'] = $datos['fecha_operacion'];
		$formaCobro['fecha_vencimiento'] = $datos['fecha_operacion'];
		$formaCobro['descripcion'] = 'PAGO CONTADO A COMERCIO';
		
		$formaCobro['banco_concepto_id'] = 0;
		$formaCobro['tipo'] = 7; 
		$formaCobro['concepto'] = 'CAJA';
		
		$formaCobro['importe'] = $datos['importe'];
		$formaCobro['debe_haber'] = 1;
		$formaCobro['descripcion_cobro'] = 'EFECTIVO';
		$formaCobro['orden_caja_cobro_id'] = $datos['orden_caja_cobro_id'];
		$formaCobro['orden_descuento_cobro_id'] = $datos['orden_descuento_cobro_id'];
		
		return $formaCobro;
		
	}
	
	
	function getConcepto($nId){
		$this->ClienteFactura = $this->importarModelo('ClienteFactura', 'clientes');
		$aFactura = $this->ClienteFactura->read(null,$nId);
		
		$cConcepto = '';
		if($aFactura['ClienteFactura']['tipo_comprobante'] == 'SALDOCLIENTE') $cConcepto = 'SALDO ANTERIOR';
		else $cConcepto =  $aFactura['ClienteFactura']['tipo'] . ': ' . $aFactura['ClienteFactura']['letra_comprobante'] . '-' . $aFactura['ClienteFactura']['punto_venta_comprobante'] . '-' . $aFactura['ClienteFactura']['numero_comprobante'] . ' / ' . $aFactura['ClienteFactura']['comentario'];
		
		return $cConcepto;
	}
	
	
	function recibos_entre_fecha($fecha_desde, $fecha_hasta){
		$aRecibos = $this->find('all', array('conditions' => array('Recibo.fecha_comprobante >=' => $fecha_desde, 'Recibo.fecha_comprobante <=' => $fecha_hasta)));
		$aReciboFecha = array();
		foreach($aRecibos as $recibo):
			$tmpRecibo = $this->getRecibo($recibo['Recibo']['id']);
			array_push($aReciboFecha, $tmpRecibo);
		endforeach;
		
		return $aReciboFecha;
	}
	
	
	function recibos_por_numero($datos){
		$aRecibos = $this->find('all', array('conditions' => array('Recibo.letra' => $datos['Recibo']['letra'], 'Recibo.sucursal' => $datos['Recibo']['sucursal'], 
																	'Recibo.nro_recibo >= ' => $datos['Recibo']['numero_desde'], 'Recibo.nro_recibo <= ' => $datos['Recibo']['numero_hasta'])));
		$aReciboNumero = array();
		foreach($aRecibos as $recibo):
			$tmpRecibo = $this->getRecibo($recibo['Recibo']['id']);
			array_push($aReciboNumero, $tmpRecibo);
		endforeach;
		
		return $aReciboNumero;
	}
	
	
	function getReciboBySolicitud($id=null){
		$aRecibo = array();

		if(empty($id)) return 0;
		
		$aRecibo = $this->find('list', array('conditions' => array('Recibo.nro_solicitud' => $id), 'fields' => 'Recibo.id'));

		foreach($aRecibo as $recibo):
			return $recibo;
		endforeach;
		
		return 0;
	}
	
	
//	function imprimirRecibo($nId){
//		$aRecibo = $this->getRecibo($nId);
//		
//		// Tipo de Documento a utilizar ('Recibo')
//		$this->TipoDocumento = $this->importarModelo('TipoDocumento', 'config');
//		$aDocumento = $this->TipoDocumento->find('all', array('conditions' => array('TipoDocumento.documento' => 'REC')));
//
//		debug($aDocumento);
//		
//		$cArchivoRecibo = WWW_ROOT . 'recibos' . DS . 'Recibo_' . $aRecibo['Recibo']['nro_recibo'] . '.prn';
//		
//		$inicio = chr(27) . chr(64) . chr(27) . chr(67) . $aDocumento[0]['TipoDocumento']['longitud_pagina'] . chr(27) . chr(77);
// 		$ddf = fopen($cArchivoRecibo,'w+');
// 		fwrite($ddf, "[HEADER]\r\n");
// 		fwrite($ddf, "IMPRESORA=" . $aDocumento[0]['TipoDocumento']['destino'] . "\r\n");
// 		fwrite($ddf, "COPIAS=" . $aDocumento[0]['TipoDocumento']['copias'] . "\r\n");
// 		fwrite($ddf,"[DATA]\r\n");
//		fwrite($ddf,$inicio);
//		fwrite($ddf,"\r\n");
//		fwrite($ddf,"\r\n");
//		fwrite($ddf,"\r\n");
//		fwrite($ddf,"\r\n");
//		fwrite($ddf,str_pad($aRecibo['Recibo']['fecha_comprobante'], 96, ' ', STR_PAD_LEFT)  . "\r\n");
//		fwrite($ddf,"\r\n");
//		fwrite($ddf,"\r\n");
//		fwrite($ddf,"\r\n");
//		fwrite($ddf,"\r\n");
//		fwrite($ddf,"\r\n");
//		fwrite($ddf,"\r\n");
//		fwrite($ddf,"\r\n");
//		fwrite($ddf,"\r\n");
//		
//		fwrite($ddf,"segunda linea de impresion\r\n");
//		for($j = 1; $j <= 20; $j++):
//			fwrite($ddf,"XXXXXXXXX XXXXXXXXX XXXXXXXXX XXXXXXXXX XXXXXXXXX XXXXXXXXX XXXXXXXXX XXXXXXXXX XXXXXXXXX XXXXXX\r\n");
//		endfor;
//		fwrite($ddf,"fin de impresion. Salto de hoja" . chr(12));
//		fclose($ddf);
//
//		$execute = 'type ' . $cArchivoRecibo . ' > ' . $aDocumento[0]['TipoDocumento']['destino'];
//debug($execute);		
//		$copias = intval($aDocumento['TipoDocumento']['copias']);
//		for($i = 1; $i <= 2; $i++):
//			$cDestinoImpresion = $aDocumento[0]['TipoDocumento']['destino'];
//			debug($cDestinoImpresion);
////			copy($cArchivoRecibo, $cDestinoImpresion);
//
//			$output = shell_exec($execute);
//		endfor;
//
////		unlink($cArchivoRecibo);
//		debug($cArchivoRecibo);
//		debug($aRecibo);
//		return true;
//	}


	function grabarReciboCaja($datos){
		
		if(!isset($datos['Recibo']['renglonesSerialize'])):
			return 0;
		endif;

		$renglones = base64_decode($datos['Recibo']['renglonesSerialize']);
		$renglones = unserialize($renglones);

		// Recibo Detalle
		$oReciboDetalle = $this->importarModelo('ReciboDetalle', 'clientes');

		// Recibo Factura
		$oReciboFactura = $this->importarModelo('ReciboFactura', 'clientes');

		// Recibo Forma
		$oReciboForma = $this->importarModelo('ReciboForma', 'clientes');

		// Caja y Banco Movimientos. ('Banco Cuenta Movimientos').
		$oBancoMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
			
		// Caja y Banco Cuentas. ('Banco Cuentas').
		$oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$cajaId = $oBancoCuenta->getCuentaCajaId(); 
		
		// Caja y Banco Conceptos. ('Banco Cuentas').
		$oBancoConcepto = $this->importarModelo('BancoConcepto', 'cajabanco');
		$cncBancoId = $oBancoConcepto->getConceptoByTipoId(2);
		$cncCajaId = 0;
		
		// Caja y Banco Cheques de Terceros. ('Banco Cheque Terceros').
		$oBancoChequeTercero = $this->importarModelo('BancoChequeTercero', 'cajabanco');
			
		$nroRecibo = $datos['Recibo']['nro_recibo'];
		
		// Establezco de donde es el Ingreso
		$aOrigen = array();
		$aOrigen['persona_id'] = (isset($datos['OrdenDescuentoCobro']['cabecera_persona_id']) ? $datos['OrdenDescuentoCobro']['cabecera_persona_id'] : 0);
		$aOrigen['socio_id']   = (isset($datos['OrdenDescuentoCobro']['cabecera_socio_id'])   ? $datos['OrdenDescuentoCobro']['cabecera_socio_id']   : 0);
		$aOrigen['cliente_id'] = (isset($datos['OrdenDescuentoCobro']['cabecera_cliente_id']) ? $datos['OrdenDescuentoCobro']['cabecera_cliente_id'] : 0);
		$aOrigen['banco_id']   = (isset($datos['OrdenDescuentoCobro']['cabecera_banco_id'])   ? $datos['OrdenDescuentoCobro']['cabecera_banco_id']   : null);
		$aOrigen['codigo_organismo']  = (isset($datos['OrdenDescuentoCobro']['cabecera_codigo_organismo'])  ? $datos['OrdenDescuentoCobro']['cabecera_codigo_organismo'] : null);

		// Armo la cabecera del Recibo
		$aRecibo = array(
			'id' => 0,
			'tipo_documento' => $datos['OrdenDescuentoCobro']['tipo_documento'],
			'letra' => $datos['Recibo']['letra'],
			'sucursal' => '0001',
			'nro_recibo' => $nroRecibo,
			'fecha_comprobante' => $datos['OrdenDescuentoCobro']['fecha_comprobante'],
			'persona_id' => $aOrigen['persona_id'],
			'socio_id' => $aOrigen['socio_id'],
			'cliente_id' => $aOrigen['cliente_id'],
			'banco_id' => $aOrigen['banco_id'],
			'codigo_organismo' => $aOrigen['codigo_organismo'],
			'importe' => $datos['OrdenDescuentoCobro']['importe_cobro'],
			'comentarios' => (empty($datos['OrdenDescuentoCobro']['observacion']) ? 'COBRO POR CAJA' : $datos['OrdenDescuentoCobro']['observacion']) 
		);
		

		if(!$this->save($aRecibo)):
//			debug('LA CABECERA DEL RECIBO NO SE GRABO');		
			return false;
		endif;
		
		$nReciboId = $this->getLastInsertID();
		
		// Armo los renglones de la Forma de cobro
    	$formaCobro = array();
    	$tmpCobro = array();
    	foreach($renglones as $valorP){
			if($valorP['OrdenDescuentoCobro']['forma_cobro'] == 'DB'):
				$tmpCobro = $this->formaCobroBanco($valorP['OrdenDescuentoCobro'], $aOrigen);
				$tmpCobro['banco_concepto_id'] = $cncBancoId;
			else:
				$tmpCobro = $this->formaCobroCaja($valorP['OrdenDescuentoCobro'], $aOrigen);
				$tmpCobro['banco_cuenta_id'] = $cajaId;
			endif;
			$tmpCobro['recibo_id'] = $nReciboId;
			$tmpCobro['descripcion'] = 'Recibo Nro.: ' . $nroRecibo;
			array_push($formaCobro, $tmpCobro);
		}
			
		foreach($formaCobro as $key => $valor){
			// Grabar los movimiento de Caja y Banco
			if(!$oBancoMovimiento->save($valor)):
				return false;
			endif;
			$formaCobro[$key]['banco_cuenta_movimiento_id'] = $oBancoMovimiento->getLastInsertID();
			$valor['banco_cuenta_movimiento_id'] = $oBancoMovimiento->getLastInsertID();
			// Grabar los movimiento de Caja y Banco
			if(!$oReciboForma->save($valor)):
//				debug('EL MOVIMIENTO DE CAJA Y BANCO NO SE GRABO');
				return false;
			endif;
		}

		

		// Grabar los Cheques de Terceros
		$aChqTercero = $this->ChequeTercero($formaCobro, $nReciboId);
		foreach($aChqTercero as $key => $chqTercero){
			if(!$oBancoChequeTercero->save($chqTercero)):
//				debug('CHEQUE DE TERCERO NO SE GRABO');
				return false;
			endif;
			$aChqTercero[$key]['id'] = $oBancoChequeTercero->getLastInsertID();
			$aBancoCuentaMovimiento = array(
				'id' => $chqTercero['banco_cuenta_movimiento_id'],
				'banco_cheque_tercero_id' => $aChqTercero[$key]['id']
			);
			if(!$oBancoMovimiento->save($aBancoCuentaMovimiento)):
//				debug('LA ACTUALIZACION DE CHEQUES DE TERCERO NO SE GRABO');
				return false;
			endif;
		}

		# GRABO EL DETALLE DEL RECIBO DE INGRESO
		$datos['OrdenDescuentoCobro']['recibo_id'] = $nReciboId;
		$aReciboDetalle = $oReciboDetalle->detalleReciboCaja($datos);
		if(!$oReciboDetalle->saveAll($aReciboDetalle)):
//			debug('EL DETALLE DEL RECIBO NO SE GRABO');
			return false;
		endif;
		

		return $nReciboId;		
	}
	
	
	function getRecibosFecha($BancoCuentaId){
		$this->BancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$cuenta = $this->BancoCuenta->getCuenta($BancoCuentaId);
		
		$mkDesde = mktime(0,0,0,date('m',strtotime($cuenta['BancoCuenta']['fecha_conciliacion'])),date('d',strtotime($cuenta['BancoCuenta']['fecha_conciliacion'])),date('Y',strtotime($cuenta['BancoCuenta']['fecha_conciliacion'])));
		$fecha_desde = date('Y-m-d',$this->addDayToDate($mkDesde));
		
		$recibos = $this->getRecibosEntreFecha($fecha_desde, $cuenta['BancoCuenta']['fecha_extracto']);

		return $recibos;
		
	}
	
	
	function getRecibosEntreFecha($fecha_desde, $fecha_hasta){
		$recibos = $this->find('all', array('conditions' => array('Recibo.anulado' => 0, 'Recibo.fecha_comprobante >=' => $fecha_desde, 'Recibo.fecha_comprobante <=' => $fecha_hasta), 'order' => array('Recibo.fecha_comprobante', 'Recibo.id')));
		
		$tmpRecibo = array();
		$aRecibos = array();
		foreach ($recibos as $recibo):
			$tmpRecibo = $this->getRecibo($recibo['Recibo']['id']);
			array_push($aRecibos, $tmpRecibo);
		endforeach;
		
		return $aRecibos;
	}


	function getAsientoAnticipoCredito($id){
		$aRecibo = $this->getRecibo($id);
		debug($aRecibo);
		exit;
		return $aRecibo;

/*
SELECT	odcc.*, SUM(odcc.importe) AS cobrado, od.id, odc.recibo_id, gd.concepto_1, gds.concepto_1
FROM	orden_descuento_cobro_cuotas odcc
INNER	JOIN orden_descuento_cuotas odcu
ON	odcc.orden_descuento_cuota_id = odcu.id
INNER	JOIN orden_descuentos od
ON	od.id = odcu.orden_descuento_id
INNER	JOIN orden_descuento_cobros odc
ON	odc.id = odcc.orden_descuento_cobro_id
INNER JOIN global_datos gd
ON gd.id = od.tipo_producto
INNER JOIN global_datos gds
ON gds.id = gd.concepto_2
WHERE	odc.recibo_id > 0 AND odcc.importe > 0 AND odc.anulado = 0
GROUP	BY odcc.orden_descuento_cobro_id, od.id
ORDER	BY odc.id
LIMIT	100000
*/		
	}
}
//		// Tipo de Documento a utilizar ('Recibo')
//		$oTipoDocumento = $this->importarModelo('TipoDocumento', 'config');
//			
//		// Busco el Numero de Recibo
//		$nroRecibo = $oTipoDocumento->getNumero($datos['OrdenDescuentoCobro']['tipo_documento']);
//		if($nroRecibo == 0):		
//			return false;
//		endif;
//
//		# MODELOS A UTILIZAR PARA GRABAR EL COBRO
//		$this->OrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');
//		
//		$this->OrdenDescuentoCobroCuota = $this->importarModelo('OrdenDescuentoCobroCuota', 'mutual');	
//
//		$this->OrdenDescuentoCuota = $this->importarModelo('OrdenDescuentoCuota', 'mutual');
//		
//		$this->OrdenCajaCobro = $this->importarModelo('OrdenCajaCobro', 'mutual');	
//		
//		$this->OrdenCajaCobroCuota = $this->importarModelo('OrdenCajaCobroCuota', 'mutual');	
//
//		$oCOMISION = $this->importarModelo('ProveedorComision', 'proveedores');		
//		
//		#GRABAR LA CABECERA DEL COBRO
//		$cTipoCobro = 'MUTUTCOBCAJA';
//		if($datos['OrdenDescuentoCobro']['proveedor_origen_id'] != MUTUALPROVEEDORID):
//	   		// TIPO DE COBRO EN CASO DE QUE LA COBRANZA HAYA SIDO EN COMERCIO.
//			$cTipoCobro = 'MUTUTCOBCACO';
//		endif;
//		
//		$fechaCobro = parent::armaFecha($datos['OrdenDescuentoCobro']['fecha_comprobante']);
//		$datos['OrdenDescuentoCobro']['tipo_cobro'] = $cTipoCobro;
//		$datos['OrdenDescuentoCobro']['fecha'] = $fechaCobro;
//		$datos['OrdenDescuentoCobro']['importe'] = $datos['OrdenCajaCobro']['orden_caja_cobro_importe'];
//		$datos['OrdenDescuentoCobro']['socio_id'] = $datos['OrdenDescuentoCobro']['cabecera_socio_id'];
//		$datos['OrdenDescuentoCobro']['proveedor_origen_fondo_id'] = $datos['OrdenDescuentoCobro']['proveedor_origen_id'];
//
//		$periodo_cobro = date('Ym',strtotime($fechaCobro));
//		$datos['OrdenDescuentoCobro']['periodo_cobro'] = $periodo_cobro;
//		
//		if(!$this->OrdenDescuentoCobro->save($datos)):
//			parent::rollback(); 
//			$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
//			return false;
//		endif;
//		$this->OrdenDescuentoCobro->id = $this->OrdenDescuentoCobro->getLastInsertID();
//		$datos['OrdenDescuentoCobro']['orden_descuento_cobro_id'] = $this->OrdenDescuentoCobro->getLastInsertID();
//		
//		
//		#GRABAR EL DETALLE DEL COBRO
//		//saco las cuotas de la orden
//		$cuotas = $this->OrdenCajaCobroCuota->findAllByOrdenCajaCobroId($datos['OrdenDescuentoCobro']['orden_caja_cobro_id']);
//
//		foreach($cuotas as $cuota){
//			
//			$proveedor_id = $this->OrdenDescuentoCuota->field('proveedor_id',"OrdenDescuentoCuota.id = ".$cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id']);
//			
//			#CALCULO LA COMISION POR LA COBRANZA
//			$comision = $this->OrdenDescuentoCobroCuota->calcularComisionCobranza($cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'],$cuota['OrdenCajaCobroCuota']['importe_abonado']);
//				
//			#GUARDO EL DETALLE DEL PAGO DE LA CUOTA
//			$cobroCuota = array();
//			$cobroCuota['OrdenDescuentoCobroCuota'] = array();
//			$cobroCuota['OrdenDescuentoCobroCuota']['periodo_cobro'] = $periodo_cobro;
//			$cobroCuota['OrdenDescuentoCobroCuota']['orden_descuento_cobro_id'] = $this->OrdenDescuentoCobro->id;
//			$cobroCuota['OrdenDescuentoCobroCuota']['orden_descuento_cuota_id'] = $cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'];
//			$cobroCuota['OrdenDescuentoCobroCuota']['importe'] = $cuota['OrdenCajaCobroCuota']['importe_abonado'];
//			$cobroCuota['OrdenDescuentoCobroCuota']['proveedor_id'] = $proveedor_id;
//			$cobroCuota['OrdenDescuentoCobroCuota']['alicuota_comision_cobranza'] = $comision['alicuota'];
//			$cobroCuota['OrdenDescuentoCobroCuota']['comision_cobranza'] = $comision['comision'];
//			
//			
//			if($this->OrdenDescuentoCobroCuota->save($cobroCuota)){
//				$flag = true;
//				$datos['OrdenDescuentoCobro']['orden_descuento_cobro_cuota_id'] = $this->OrdenDescuentoCobroCuota->getLastInsertID();
//				$datos['OrdenDescuentoCobro']['importe_cuota'] = $cuota['OrdenCajaCobroCuota']['importe_abonado'];
//				#MARCO LA CUOTA COMO PAGADA SI LA PAGA TOTALMENTE
//				if($cuota['OrdenCajaCobroCuota']['importe_abonado'] == $cuota['OrdenCajaCobroCuota']['importe']){
//					$this->OrdenDescuentoCuota->id = $cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'];
//					if(!$this->OrdenDescuentoCuota->saveField('estado','P')){
//						$flag = false;
//						break;
//					}
//					if(!$this->__setPeriodoCobro($cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'],$periodo_cobro)){
//						$flag = false;
//						break;				
//					}					
//				}
//			}else{
//				$flag = false;
//				break;				
//			}
//			
//			$this->OrdenDescuentoCobroCuota->id = 0;
//		}
//
//		if(!$flag):
//			parent::rollback(); 
//			$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
//			return false;
//		endif;
//		
//		# MODELOS A UTILIZAR PARA EL PROVEEDOR
//		$this->ProveedorFactura = $this->importarModelo('ProveedorFactura', 'proveedores');
//		
//		$this->ClienteFactura = $this->importarModelo('ClienteFactura', 'clientes');
//		
//    	// Factura Detalle
//    	$this->FacturaDetalle = $this->importarModelo('ClienteFacturaDetalle', 'clientes');
//		
//    	# TRAIGO DE LA ORDEN DESCUENTO COBRO CUOTAS ACUMULADO POR PROVEEDOR LOS MONTOS A FACTURAR CORRESPONDIENTE A CADA UNO INVOLUCRADO EN EL COBRO.
//		$aCobroProveedor = $this->__getCobroCuotaByProveedor($this->OrdenDescuentoCobro->id);
//
//		$importe_anticipo = 0;
//		$importe_contado = 0;
//		// Busco el Numero de la Factura
//		$nroFactura = $oTipoDocumento->getNumero('FAC');
//		if($nroFactura == 0):
//			parent::rollback(); 
//			$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
//			return false;
//		endif;
//		$nroFactura -= 1;
//		$nCantidadFactura = 0;
//
//		foreach($aCobroProveedor as $aCobro):
//			$aCobro['orden_descuento_cobro_cuotas']['orden_caja_cobro_id'] = $datos['OrdenDescuentoCobro']['orden_caja_cobro_id'];
//			$aCobro['orden_descuento_cobro_cuotas']['fecha_comprobante'] = $fechaCobro;
//			$aCobro['orden_descuento_cobro_cuotas']['periodo_cobro'] = $periodo_cobro;
//			if($aCobro['orden_descuentos']['proveedor_id'] != MUTUALPROVEEDORID):
//			   	if($aCobro['orden_descuentos']['proveedor_id'] != $datos['OrdenDescuentoCobro']['proveedor_origen_id']):
//			   		// PREPARO LA ORDEN DE PAGO DE ANTICIPO EN CASO DE QUE LA COBRANZA HAYA SIDO EN COMERCIO.
//					if(isset($datos['OrdenDescuentoCobro']['compensa_pago']) && $datos['OrdenDescuentoCobro']['proveedor_origen_id'] != MUTUALPROVEEDORID):
//						$importe_anticipo += $aCobro[0]['importe'];
//					endif;
//					// GRABO LA FACTURA DEL PROVEEDOR
//					if($aCobro[0]['importe'] > 0):
//						$aFacturaProveedor = $this->__getFacturaProveedor($aCobro);
//						if(!$this->ProveedorFactura->save($aFacturaProveedor)):
//							$flag = false;
//							break;				
//						endif;
//					endif;
//					// GRABO LA FACTURA DEL CLIENTE POR LA COMISION
//					if($aCobro[0]['comision_cobranza'] > 0):
//						$nCantidadFactura += 1;
//						$nroFactura += 1;
//						$aFacturaCliente = $this->__getFacturaCliente($aCobro);
//						$aFacturaCliente['numero_comprobante'] = str_pad($nroFactura, 8, 0, STR_PAD_LEFT);
//						if(!$this->ClienteFactura->save($aFacturaCliente)):
//							$flag = false;
//							break;				
//						endif;
//						$aFacturaCliente['factura_detalle']['cliente_factura_id'] = $this->ClienteFactura->getLastInsertID();
//						if(!$this->FacturaDetalle->save($aFacturaCliente['factura_detalle'])):
//							$flag = false;
//							break;				
//						endif;
//						$this->ClienteFactura->id = 0;
//					endif;
//				else:
//					// PREPARO UN PAGO DE CONTADO SI FUE RECAUDADO EN ESE COMERCIO
//					$importe_contado += $aCobro[0]['importe'];
//       			endif;
//			else:
//				// PREPARO LA ORDEN DE PAGO DE ANTICIPO EN CASO DE QUE LA COBRANZA HAYA SIDO EN COMERCIO.
//				if(isset($datos['OrdenDescuentoCobro']['compensa_pago']) && $datos['OrdenDescuentoCobro']['proveedor_origen_id'] != MUTUALPROVEEDORID):
//					$importe_anticipo += $aCobro[0]['importe'];
//				endif;
//			endif;
//		endforeach;
//		
//		if(!$flag):
//			parent::rollback(); 
//			$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
//			$oTipoDocumento->unLookRegistro('FAC');
//			return false;
//		endif;
//		
//		// GENERO EL PAGO DE CONTADO SI CORRESPONDE
//		if($importe_contado > 0 && $datos['OrdenDescuentoCobro']['proveedor_origen_id'] != MUTUALPROVEEDORID):
//	    	$aPagoContado = array(
//    			'caja_id' => $cajaId,
//				'fecha_operacion' => $fechaCobro,
//				'importe' => $importe_contado,
//				'orden_caja_cobro_id' => $datos['OrdenDescuentoCobro']['orden_caja_cobro_id'],
//    			'orden_descuento_cobro_id' => $datos['OrdenDescuentoCobro']['orden_descuento_cobro_id']
//	    	);
//    		$aContado = $this->__contado($aPagoContado);
//			if(!$oBancoMovimiento->save($aContado)):
//				parent::rollback(); 
//				$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
//				$oTipoDocumento->unLookRegistro('FAC');
//				$oTipoDocumento->unLookRegistro('OPA');
//				return false;
//			endif;
//			$datos['OrdenDescuentoCobro']['banco_cuenta_movimiento_id'] = $oBancoMovimiento->getLastInsertID();
//		endif;
//		
//		# GENERO UNA ORDEN DE PAGO ANTICIPADO SI CORRESPONDE
//		if($importe_anticipo > 0 && $datos['OrdenDescuentoCobro']['proveedor_origen_id'] != MUTUALPROVEEDORID):
//			// Llamo a los modelos a utilizar
//			// Orden de Pago Cabecera
//			$this->OrdenPago = $this->importarModelo('OrdenPago', 'proveedores');
//			
//			// Orden de Pago Detalle
//			$this->OrdenPagoDetalle = $this->importarModelo('OrdenPagoDetalle', 'proveedores');
//			
//			// Orden de Pago Facturas
//			$this->OrdenPagoFactura = $this->importarModelo('OrdenPagoFactura', 'proveedores');
//			
//			// Orden de Pago Valores de Cobros
//			$this->OrdenPagoForma = $this->importarModelo('OrdenPagoForma', 'proveedores');
//			
//			$cncCajaId = 0;
//			
//			$aOrdenPago = $this->getOrdenPago();
//			// Busco el Numero de la Orden de Pago
//			$nroOPago = $oTipoDocumento->getNumero('OPA');
//			if($nroOPago == 0):
//				parent::rollback(); 
//				$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
//				$oTipoDocumento->unLookRegistro('FAC');
//				$oTipoDocumento->unLookRegistro('OPA');
//				return false;
//			endif;
//			$aAnticipo = array(
//				'proveedor_id' => $datos['OrdenDescuentoCobro']['proveedor_origen_id'],
//				'nro_orden_pago' => $nroOPago,
//				'fecha_comprobante' => $fechaCobro,
//				'importe' => $importe_anticipo,
//				'caja_id' => $cajaId,
//				'cnc_caja_id' => 0,
//				'orden_caja_cobro_id' => $datos['OrdenDescuentoCobro']['orden_caja_cobro_id'],
//				'orden_descuento_cobro_id' => $datos['OrdenDescuentoCobro']['orden_descuento_cobro_id'],
//				'comentario' => ''
//			);
//			$aOrdenPagoAnticipo = $this->__getPagoAnticipado($aAnticipo);
//			if(!$this->OrdenPago->save($aOrdenPagoAnticipo)):
//				parent::rollback(); 
//				$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
//				$oTipoDocumento->unLookRegistro('FAC');
//				$oTipoDocumento->unLookRegistro('OPA');
//				return false;
//			endif;
//			$nOPagoId = $this->OrdenPago->getLastInsertID();
//			$datos['OrdenDescuentoCobro']['orden_pago_id'] = $this->OrdenPago->getLastInsertID();
//			$aOrdenPagoAnticipo['detalle']['orden_pago_id'] = $this->OrdenPago->getLastInsertID();
//			$aOrdenPagoAnticipo['forma_pago']['orden_pago_id'] = $this->OrdenPago->getLastInsertID();
//			if(!$this->OrdenPagoDetalle->save($aOrdenPagoAnticipo['detalle'])):
//				parent::rollback(); 
//				$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
//				$oTipoDocumento->unLookRegistro('FAC');
//				$oTipoDocumento->unLookRegistro('OPA');
//				return false;
//			endif;
//			if(!$oBancoMovimiento->save($aOrdenPagoAnticipo['forma_pago'])):
//				parent::rollback(); 
//				$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
//				$oTipoDocumento->unLookRegistro('FAC');
//				$oTipoDocumento->unLookRegistro('OPA');
//				return false;
//			endif;
//			$aOrdenPagoAnticipo['forma_pago']['banco_cuenta_movimiento_id'] = $oBancoMovimiento->getLastInsertID();
//			if(!$this->OrdenPagoForma->save($aOrdenPagoAnticipo['forma_pago'])):
//				parent::rollback(); 
//				$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
//				$oTipoDocumento->unLookRegistro('FAC');
//				$oTipoDocumento->unLookRegistro('OPA');
//				return false;
//			endif;
//			
//		endif;
//		#MARCO LA ORDEN DE COBRO POR CAJA COMO PROCESADA
//		$flag = $this->OrdenCajaCobro->marca_procesada($datos['OrdenDescuentoCobro']['orden_caja_cobro_id']);
//		$aOrdenCajaCobro = array(
//			'id' => $datos['OrdenDescuentoCobro']['orden_caja_cobro_id'],
//			'orden_descuento_cobro_id' =>  $datos['OrdenDescuentoCobro']['orden_descuento_cobro_id'],
//			'orden_pago_id' => $datos['OrdenDescuentoCobro']['orden_pago_id'],
//			'banco_cuenta_movimiento_id' => $datos['OrdenDescuentoCobro']['banco_cuenta_movimiento_id'],
//			'recibo_id' => $datos['OrdenDescuentoCobro']['recibo_id'],
//			'importe_contado' => $importe_contado,
//			'importe_orden_pago' => $importe_anticipo
//		);
//		$flag = $this->OrdenCajaCobro->save($aOrdenCajaCobro);
//
////		$flag = false;
//		
//		########################
//		# Genero la Provedor Liquidaciones con el id de la orden descuento cobro.
//		########################
//		$this->ProveedorLiquidacion = $this->importarModelo('ProveedorLiquidacion', 'proveedores');
//		if(!$this->ProveedorLiquidacion->grabarLiquidacionByCaja($datos['OrdenDescuentoCobro']['orden_descuento_cobro_id'])):
//			$this->rollback();
//			$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
//			$oTipoDocumento->unLookRegistro('FAC');
//			$oTipoDocumento->unLookRegistro('OPA');
//			return false;
//		endif;
//		
//		
//		########################
//		#TODO BIEN --> CERRAR LA TRANSACCION
//		########################
//		if($flag):
//			$this->commit();
//			# ACUMULO LOS NUMERO DE FACTURAS QUE HE GENERADO
//			$oTipoDocumento->putNumero($datos['OrdenDescuentoCobro']['tipo_documento']);
//			$oTipoDocumento->putNumero('FAC', $nCantidadFactura);
//			$oTipoDocumento->putNumero('OPA');
//		else: 
//			$this->rollback();
//			$oTipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
//			$oTipoDocumento->unLookRegistro('FAC');
//			$oTipoDocumento->unLookRegistro('OPA');
//			return false;
//		endif;
		
?>