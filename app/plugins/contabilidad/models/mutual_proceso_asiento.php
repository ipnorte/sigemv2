<?php
class MutualProcesoAsiento extends ContabilidadAppModel {
	var $name = 'MutualProcesoAsiento';
	
//	var $use = array('mutual.MutualProcesoAsiento', 'clientes.Recibo', 'clientes.ClienteFactura', 'cajabanco.BancoCuenta', 'contabilidad.MutualAsiento');
	

	/**
	 * Procesa los recibos entre fecha para la generacion de asientos
	 * 
	 */
	function getAsientoRecibo($recibo, $procesoId, $agrupar=0){
		$oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		
                        if($recibo['Recibo']['persona_id'] > 0) $cModulo = 'RECIPERS';
				
			if($recibo['Recibo']['socio_id'] > 0) $cModulo = 'RECISOCI';
				
			if($recibo['Recibo']['cliente_id'] > 0) $cModulo = 'RECICLIE';
	
			if($recibo['Recibo']['banco_id'] > 0) $cModulo = 'RECIORGA';
				
			if($recibo['Recibo']['codigo_organismo'] != '') $cModulo = 'RECIORGA';
				
			if($recibo['Recibo']['nro_solicitud'] > 0) $cModulo = 'RECIADELCRED';
			
			foreach($recibo['Recibo']['forma'] as $key => $forma):
				$temporalAsiento = array();
				$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $recibo['Recibo']['fecha_comprobante'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $oBancoCuenta->getCodigoPlanCuenta($forma['banco_cuenta_id']);
				$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $forma['importe'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $recibo['Recibo']['razon_social'] . ' - ' . $recibo['Recibo']['numero_string2'];
				
				$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
				$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
								
				$oMutualTemporalAsientoRenglon->id = 0;
				$oMutualTemporalAsientoRenglon->save($temporalAsiento);
			
			endforeach;
		
			if($recibo['Recibo']['persona_id'] > 0):
				$this->getAsientoReciboPersona($recibo);
			endif;
			
			if($recibo['Recibo']['socio_id'] > 0):
				$this->getAsientoReciboSocio($recibo);
			endif;
			
			if($recibo['Recibo']['cliente_id'] > 0):
				$this->getAsientoReciboCliente($recibo);
			endif;

			if($recibo['Recibo']['banco_id'] > 0):
				$this->getAsientoReciboBanco($recibo);
			endif;
			
			if($recibo['Recibo']['codigo_organismo'] != ''):
				$this->getAsientoReciboOrganismo($recibo);
			endif;
			
			if($recibo['Recibo']['nro_solicitud'] > 0):
				$this->getAsientoReciboAdelantoCredito($recibo);
			endif;
			
			
			if($agrupar == 0):

				$queryImporte = "SELECT SUM(importe) as importe 
								FROM mutual_temporal_asiento_renglones
								WHERE mutual_asiento_id = 0
								GROUP BY mutual_asiento_id, co_plan_cuenta_id";
				$importeAsiento = $this->query($queryImporte);
					
				$debe = 0;
				$haber = 0;
				foreach($importeAsiento as $importe):
					if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
					else  $haber += $importe[0]['importe'];
				endforeach;
		
				$aMutualAsiento = array();
				$aMutualAsiento['MutualAsiento']['id'] = 0;
				$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
				$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
				$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
				$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
				$aMutualAsiento['MutualAsiento']['fecha'] = $recibo['Recibo']['fecha_comprobante'];
				$aMutualAsiento['MutualAsiento']['tipo_documento'] = $recibo['Recibo']['tipo_documento'];
				$aMutualAsiento['MutualAsiento']['nro_documento'] = $recibo['Recibo']['letra'] . '-' . $recibo['Recibo']['sucursal'] . '-' . $recibo['Recibo']['nro_recibo'];
				$aMutualAsiento['MutualAsiento']['referencia'] = (empty($recibo['Recibo']['comentarios']) ? $recibo['Recibo']['razon_social'] : $recibo['Recibo']['comentarios']);
				$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
				$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
				$aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
				
				$this->grabarAsiento($aMutualAsiento, $procesoId);			
			endif;			
		
		return true;
	}
	
	
	function getAsientoReciboPersona($id){
		$aRecibo = $this->Recibo->getRecibo($id);
		$asiento = array();
		
		
		return $asiento;
	}
	
	
	function getProductoSocio($ordenDescuento, $ordenCobro){
		$oOrdenDsto = $this->importarModelo('OrdenDescuento', 'mutual');
		
		while(true):
			$sql = "SELECT PersonaBeneficio.codigo_beneficio, OrdenDescuentoCuota.*, MutualCuentaAsiento.co_plan_cuenta_id, SUM(OrdenDescuentoCobroCuota.importe) AS importe_cobro
					FROM orden_descuento_cuotas OrdenDescuentoCuota
					INNER JOIN orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
					ON OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id
					INNER JOIN persona_beneficios PersonaBeneficio
					ON OrdenDescuentoCuota.persona_beneficio_id = PersonaBeneficio.id
					INNER	JOIN mutual_cuenta_asientos MutualCuentaAsiento
					ON CONCAT('ORGAN', PersonaBeneficio.codigo_beneficio) = 
					   CONCAT(MutualCuentaAsiento.tipo_orden_dto, MutualCuentaAsiento.tipo_producto)
					WHERE OrdenDescuentoCuota.orden_descuento_id = '$ordenDescuento' AND OrdenDescuentoCobroCuota.orden_descuento_cobro_id = '$ordenCobro'
					GROUP BY PersonaBeneficio.id
			";
			
	
			$productos = $this->query($sql);
			
			if(empty($productos)):
				$regOrdenDsto = $oOrdenDsto->read(null, $ordenDescuento);
				if($regOrdenDsto['OrdenDescuento']['nueva_orden_descuento_id'] > 0):
					$ordenDescuento = $regOrdenDsto['OrdenDescuento']['nueva_orden_descuento_id'];
				endif;
			else:
				break;
			endif;
		endwhile;
				
		
		
		return $productos;
	}
	

	function getProductoSocioOld($ordenDescuento, $ordenCobro){
		$oOrdenDsto = $this->importarModelo('OrdenDescuento', 'mutual');
		
		while(true):
			$regOrdenDsto = $oOrdenDsto->read(null, $ordenDescuento);
			if($regOrdenDsto['OrdenDescuento']['nueva_orden_descuento_id'] > 0):
				$ordenDescuento = $regOrdenDsto['OrdenDescuento']['nueva_orden_descuento_id'];
			else:
				break;
			endif;
		endwhile;
				
		$sql = "SELECT PersonaBeneficio.codigo_beneficio, OrdenDescuentoCuota.*, MutualCuentaAsiento.co_plan_cuenta_id, SUM(OrdenDescuentoCobroCuota.importe) AS importe_cobro
				FROM orden_descuento_cuotas OrdenDescuentoCuota
				INNER JOIN orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
				ON OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id
				INNER JOIN persona_beneficios PersonaBeneficio
				ON OrdenDescuentoCuota.persona_beneficio_id = PersonaBeneficio.id
				INNER	JOIN mutual_cuenta_asientos MutualCuentaAsiento
				ON CONCAT('ORGAN', PersonaBeneficio.codigo_beneficio) = 
				   CONCAT(MutualCuentaAsiento.tipo_orden_dto, MutualCuentaAsiento.tipo_producto)
				WHERE OrdenDescuentoCuota.orden_descuento_id = '$ordenDescuento' AND OrdenDescuentoCobroCuota.orden_descuento_cobro_id = '$ordenCobro'
				GROUP BY PersonaBeneficio.id
		";
		

		$productos = $this->query($sql);
		
		
		return $productos;
	}
	

	function getAsientoReciboSocio($recibo){
		$oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$oSocio = $this->importarModelo('Socio', 'pfyj');
		$oPersonaBeneficio = $this->importarModelo('PersonaBeneficio', 'pfyj');
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$cModulo = 'RECISOCI';
		
		$aTemporal = array();
		foreach($recibo['Recibo']['detalle'] as $key => $detalle):
			if($detalle['socio_reintegro_id'] > 0):
				$socio = $oSocio->find('all', array('conditions' => array('Socio.id' => $recibo['Recibo']['socio_id'])));
				$beneficio = $oPersonaBeneficio->find('all', array('conditions' => array('PersonaBeneficio.id' => $socio[0]['Socio']['persona_beneficio_id'])));
				$cuentaReintegro = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'ORGAN','MutualCuentaAsiento.tipo_producto' => $beneficio[0]['PersonaBeneficio']['codigo_beneficio'])));

				$temporalAsiento = array();
				$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $recibo['Recibo']['fecha_comprobante'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
				$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = ($detalle['importe']) * (-1);
				$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $recibo['Recibo']['comentarios'] . ' REINT. ' . $detalle['socio_reintegro_id'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
				$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo . 'REIN';
				$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0; 
				
				if(empty($cuentaReintegro)):
					$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA REINTEGRO NO EXITE (' . $recibo['Recibo']['numero_string2'] . ')'; 
	
				else:
					$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaReintegro[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
						
					if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
						$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
						$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA REINTEGRO NO CONFIGURADA (' . $recibo['Recibo']['numero_string2'] . ')'; 
					endif;
				endif;
				
				$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
				$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
				$oMutualTemporalAsientoRenglon->id = 0;
				$oMutualTemporalAsientoRenglon->save($temporalAsiento);
			endif;
		endforeach;
		
		
		// Se ha cambiado la forma de realizar los asientos de Socios, ya que tiene algun error y para no caer
		// en errores es mas facil buscarlo por la orden descuento cobro segun el recibo_id, y no por el detalle 
		// del recibo.
		$reciboId = $recibo['Recibo']['id'];
		
		$sql = "SELECT PersonaBeneficio.codigo_beneficio, OrdenDescuentoCobro.proveedor_origen_fondo_id, OrdenDescuentoCobroCuota.orden_descuento_cobro_id, OrdenDescuentoCuota.*, MutualCuentaAsiento.co_plan_cuenta_id, SUM(OrdenDescuentoCobroCuota.importe) AS importe_cobro
				FROM orden_descuento_cuotas OrdenDescuentoCuota
				INNER JOIN orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
				ON OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id
				
				INNER JOIN orden_descuento_cobros OrdenDescuentoCobro
				ON OrdenDescuentoCobroCuota.orden_descuento_cobro_id = OrdenDescuentoCobro.id
				
				INNER JOIN persona_beneficios PersonaBeneficio
				ON OrdenDescuentoCuota.persona_beneficio_id = PersonaBeneficio.id
				INNER	JOIN mutual_cuenta_asientos MutualCuentaAsiento
				ON CONCAT('ORGAN', PersonaBeneficio.codigo_beneficio) = 
				   CONCAT(MutualCuentaAsiento.tipo_orden_dto, MutualCuentaAsiento.tipo_producto)
/*   
                                INNER	JOIN recibo_detalles ReciboDetalle
                                ON	ReciboDetalle.orden_descuento_id = OrdenDescuentoCuota.orden_descuento_id
*/
				WHERE OrdenDescuentoCobro.recibo_id = '$reciboId'
				GROUP BY PersonaBeneficio.id, OrdenDescuentoCuota.orden_descuento_id
				";
			
	
		$productos = $this->query($sql);
			
		
		if(empty($productos)){
			$temporalAsiento = array();
			$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $recibo['Recibo']['fecha_comprobante'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $recibo['Recibo']['importe'] * (-1);
			$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $recibo['Recibo']['razon_social'] . '-' . $recibo['Recibo']['numero_string2'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'PROD. INEXIST. EN ORDEN COBRO';
			$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo . 'PROD';
			$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
			
			$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
			
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($temporalAsiento);
                }else{
			foreach($productos as $producto){
				$temporalAsiento = array();
				$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $recibo['Recibo']['fecha_comprobante'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = '';
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $producto['MutualCuentaAsiento']['co_plan_cuenta_id'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $producto[0]['importe_cobro'] *(-1);
				$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $recibo['Recibo']['razon_social'] . '-' . $recibo['Recibo']['numero_string2'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo . 'PROD';
				$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
					
				if(empty($producto['MutualCuentaAsiento']['co_plan_cuenta_id'])):
					$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CTA.INEXIST.EN PRODUCTO (' . ' OD. ' . $producto['OrdenDescuentoCuota']['orden_descuento_id'] . ' OC. ' . $producto['OrdenDescuentoCobroCuota']['orden_descuento_cobro_id'] . ')';
				endif;
				$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
				$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
				
				$oMutualTemporalAsientoRenglon->id = 0;
				$oMutualTemporalAsientoRenglon->save($temporalAsiento);
                        }
                }
// 		array_push($aTemporal, array($detalle['orden_descuento_id'], $detalle['orden_descuento_cobro_id']));
// 		endif;
		
		
		
		return true;
	}
	

	

	function getAsientoReciboSocioOld($recibo){
		$oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$oSocio = $this->importarModelo('Socio', 'pfyj');
		$oPersonaBeneficio = $this->importarModelo('PersonaBeneficio', 'pfyj');
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$cModulo = 'RECISOCI';
		
		$aTemporal = array();
		foreach($recibo['Recibo']['detalle'] as $key => $detalle):
			if($detalle['socio_reintegro_id'] > 0):
				$socio = $oSocio->find('all', array('conditions' => array('Socio.id' => $recibo['Recibo']['socio_id'])));
				$beneficio = $oPersonaBeneficio->find('all', array('conditions' => array('PersonaBeneficio.id' => $socio[0]['Socio']['persona_beneficio_id'])));
				$cuentaReintegro = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'ORGAN','MutualCuentaAsiento.tipo_producto' => $beneficio[0]['PersonaBeneficio']['codigo_beneficio'])));

				$temporalAsiento = array();
				$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $recibo['Recibo']['fecha_comprobante'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
				$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = ($detalle['importe']) * (-1);
				$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $recibo['Recibo']['comentarios'] . ' REINT. ' . $detalle['socio_reintegro_id'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
				$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo . 'REIN';
				$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0; 
				
				if(empty($cuentaReintegro)):
					$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA REINTEGRO NO EXITE (' . $recibo['Recibo']['numero_string2'] . ')'; 
	
				else:
					$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaReintegro[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
						
					if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
						$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
						$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA REINTEGRO NO CONFIGURADA (' . $recibo['Recibo']['numero_string2'] . ')'; 
					endif;
				endif;
				
				$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
				$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
				$oMutualTemporalAsientoRenglon->id = 0;
				$oMutualTemporalAsientoRenglon->save($temporalAsiento);
			else:
				$nBuscar = 0;
				if(!empty($aTemporal)):
					foreach($aTemporal as $aTmp):
						if($aTmp[0] ==  $detalle['orden_descuento_id'] && $aTmp[1] == $detalle['orden_descuento_cobro_id']) $nBuscar = 1;
					endforeach;
				endif;
				if($nBuscar == 0):
					$productos = $this->getProductoSocio($detalle['orden_descuento_id'], $detalle['orden_descuento_cobro_id']);
	
					if(empty($productos)):
						$temporalAsiento = array();
						$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $recibo['Recibo']['fecha_comprobante'];
						$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
						$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $detalle['importe'] * (-1);
						$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $recibo['Recibo']['razon_social'] . '-' . $recibo['Recibo']['numero_string2'];
						$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
						$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'PROD. INEXIST. EN ORDEN COBRO' . ' OD. ' . $detalle['orden_descuento_id'] . ' OC. ' . $detalle['orden_descuento_cobro_id']; 
						$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo . 'PROD';
						$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0; 
						
						$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
						$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
						$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
						$oMutualTemporalAsientoRenglon->id = 0;
						$oMutualTemporalAsientoRenglon->save($temporalAsiento);
					else:
						foreach($productos as $producto):
							$temporalAsiento = array();
							$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $recibo['Recibo']['fecha_comprobante'];
							$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
							$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
							$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $producto['MutualCuentaAsiento']['co_plan_cuenta_id'];
							$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $producto[0]['importe_cobro'] *(-1);
							$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $recibo['Recibo']['razon_social'] . '-' . $recibo['Recibo']['numero_string2'] . $detalle['concepto'];
							$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo . 'PROD';
							$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0; 
							
							if(empty($producto['MutualCuentaAsiento']['co_plan_cuenta_id'])):
								$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
								$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CTA.INEXIST.EN PRODUCTO (' . ' OD. ' . $detalle['orden_descuento_id'] . ' OC. ' . $detalle['orden_descuento_cobro_id'] . ')'; 
							endif;
							$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
							$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
							$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
							$oMutualTemporalAsientoRenglon->id = 0;
							$oMutualTemporalAsientoRenglon->save($temporalAsiento);
						endforeach;
					endif;
					array_push($aTemporal, array($detalle['orden_descuento_id'], $detalle['orden_descuento_cobro_id']));
				endif;
			endif;
		endforeach;
		
		return true;
	}
	

	function getAsientoReciboCliente($recibo){
		$oClienteFactura = $this->importarModelo('ClienteFactura', 'clientes');
		$oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$cModulo = 'RECICLIE';
		
		
		foreach($recibo['Recibo']['detalle'] as $key => $detalle){
                    $temporalAsiento = array();
                    $temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $recibo['Recibo']['fecha_comprobante'];
                    $temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $detalle['importe'] *(-1);
                    $temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $recibo['Recibo']['razon_social'] . $recibo['Recibo']['numero_string2'] . $detalle['concepto'];
                    $temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
                    $temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
                    $temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
                    $temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0; 
                    if($detalle['tipo_cobro'] == 'AN' || empty($detalle['tipo_cobro'])){
                        ####################################################################################################
                        # CONFIGURO LA CUENTA DE ANTICIPO. Las Ordenes de Pagos y Recibos hacen anticipos, esto pueden ir
                        # a la cuenta contable de ANTICIPO o a la cuenta contable correspondiente al PROVEEDOR/CLIENTE.
                        ####################################################################################################
                        $cnfCtaAnticipo = $this->getGlobalDato('LOGICO_1', 'CONTANTI');

                        $temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo . 'ANTI';
                        if(!$cnfCtaAnticipo['GlobalDato']['LOGICO_1']){
                            $cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'COMPR', 'MutualCuentaAsiento.tipo_producto' => 'RECIBO', 'MutualCuentaAsiento.tipo_cuota' => 'ANTICIPO')));
                            if(empty($cuentaAsiento)){
                                $temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
                                $temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                                $temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'FALTA DEFINIR ANTICIPO (' . $recibo['Recibo']['numero_string2'] . ')'; 
                            }
                            else{
                                $temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
                            }
                        }
                        else{
                            $oCliente = $this->importarModelo('Cliente', 'clientes');
                            $aCliente = $oCliente->getCliente($recibo['Recibo']['cliente_id']);
                            if(empty($aCliente['Cliente']['co_plan_cuenta_id'])){
                                $temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
                                $temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                                $temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'FALTA DEFINIR CUENTA EN CLIENTE'; 
                            }
                            else{
                                $temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $aCliente['Cliente']['co_plan_cuenta_id'];
                            }

                        }
                    }
                    else{
                        $temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo . 'CLIE';
                        $temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
                        if($detalle['co_plan_cuenta_id'] > 0){
                            $temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $detalle['co_plan_cuenta_id'];
                        }
                        else{
                            $asientoTipo = $oClienteFactura->getCodigoPlanCuenta($detalle['cliente_factura_id']);

                            $temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $asientoTipo['co_plan_cuenta_id'];
                            if($asientoTipo['error'] != 'OK'){
                                $temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                                $temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = $asientoTipo['error'] . ' (' . $recibo['Recibo']['numero_string2'] . ')'; 
                            }
                        }

                    }
                    $cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
                    $temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
                    $temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];

                    $oMutualTemporalAsientoRenglon->id = 0;
                    $oMutualTemporalAsientoRenglon->save($temporalAsiento);
                }
		
		
		return true;
	}
	

	function getAsientoReciboBanco($recibo){
		$oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$oLiquidacionIntercambio = $this->importarModelo('LiquidacionIntercambio', 'mutual');
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$cModulo = 'RECIORGABANC';		
		
		$aOrganismo = $oLiquidacionIntercambio->find('all', array('conditions' => array('LiquidacionIntercambio.recibo_id' => $recibo['Recibo']['id'])));
		if(empty($aOrganismo)):
			$temporalAsiento = array();
			$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $recibo['Recibo']['fecha_comprobante'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $recibo['Recibo']['importe'] * (-1);
			$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $recibo['Recibo']['razon_social'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'LIQUIDACION PARA ORGANISMO INEXISTENTE (' . $recibo['Recibo']['numero_string2'] . ')'; 
			$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
			$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
			
			$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($temporalAsiento);
		else:

			foreach($aOrganismo as $organismo):
				$cuentaOrganismo = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_producto' => 'ORGAN','MutualCuentaAsiento.tipo_producto' => $organismo['LiquidacionIntercambio']['codigo_organismo'])));
				$temporalAsiento = array();
				$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $recibo['Recibo']['fecha_comprobante'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $organismo['LiquidacionIntercambio']['importe_cobrado'] * (-1);
				$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $recibo['Recibo']['razon_social'] . ' - ' . $organismo['LiquidacionIntercambio']['codigo_organismo'] . ' - ' . $organismo['LiquidacionIntercambio']['archivo_nombre'] . ' - ' . $organismo['LiquidacionIntercambio']['liquidacion_id'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
				$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
				$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
				if(empty($cuentaOrganismo)):
					$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'ORGANISMO INEXISTENTE EN CONFIGURACION DE ASIENTO (' . $recibo['Recibo']['numero_string2'] . ')'; 
						
				else:
					$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaOrganismo[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
					
					if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
						$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
						$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA INEXISTENTE EN ORGANISMO (' . $recibo['Recibo']['numero_string2'] . ')'; 
					endif;
				endif;
				$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
				$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
				$oMutualTemporalAsientoRenglon->id = 0;
				$oMutualTemporalAsientoRenglon->save($temporalAsiento);
			endforeach;
		endif;
				
		
		return true;
	}
	

	function getAsientoReciboOrganismo($recibo){
		$oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$cModulo = 'RECIORGAASOC';
		
		
		foreach($recibo['Recibo']['detalle'] as $key => $detalle):
			$temporalAsiento = array();
			$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $recibo['Recibo']['fecha_comprobante'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $detalle['importe'] *(-1);
			$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $recibo['Recibo']['razon_social'] . $detalle['concepto'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
			$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
			$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0; 

			$cuentaOrganismo = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_producto' => 'ORGAN','MutualCuentaAsiento.tipo_producto' => $recibo['Recibo']['codigo_organismo'])));
                        
			if(empty($cuentaOrganismo)):
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'ORGANISMO INEXISTENTE EN CONFIGURACION DE ASIENTO (' . $recibo['Recibo']['tipo_documento'] . '-' . $recibo['Recibo']['letra'] . '-' . $recibo['Recibo']['sucursal'] . '-' . $recibo['Recibo']['nro_recibo'] . ')'; 

			else:
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaOrganismo[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
					
				if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
					$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA INEXISTENTE EN ORGANISMO (' . $recibo['Recibo']['tipo_documento'] . '-' . $recibo['Recibo']['letra'] . '-' . $recibo['Recibo']['sucursal'] . '-' . $recibo['Recibo']['nro_recibo'] . ')'; 
				endif;
			endif;
			$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
                        

			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($temporalAsiento);
		endforeach;
		
		
        	return true;
	}
	

	function getAsientoReciboOrganismoOLD($recibo){
		$oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$oLiquidacionIntercambio = $this->importarModelo('LiquidacionIntercambio', 'mutual');
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$cModulo = 'RECIORGAASOC';		
		
		$aOrganismo = $oLiquidacionIntercambio->find('all', array('conditions' => array('LiquidacionIntercambio.recibo_id' => $recibo['Recibo']['id'])));
		
		if(empty($aOrganismo)):
			$temporalAsiento = array();
			$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $recibo['Recibo']['fecha_comprobante'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $recibo['Recibo']['importe'] * (-1);
			$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $recibo['Recibo']['razon_social'] . ' - ' . $recibo['Recibo']['codigo_organismo'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'LIQUIDACION PARA ORGANISMO INEXISTENTE (' . $recibo['Recibo']['numero_string2'] . ')'; 
			$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
			$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
			
			$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($temporalAsiento);
		else:
		
			foreach($aOrganismo as $organismo):
				$cuentaOrganismo = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_producto' => 'ORGAN','MutualCuentaAsiento.tipo_producto' => $organismo['LiquidacionIntercambio']['codigo_organismo'])));
				$temporalAsiento = array();
				$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $recibo['Recibo']['fecha_comprobante'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $organismo['LiquidacionIntercambio']['importe_cobrado'] * (-1);
				$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $recibo['Recibo']['razon_social'] . ' - ' . $organismo['LiquidacionIntercambio']['codigo_organismo'] . ' - ' . $organismo['LiquidacionIntercambio']['archivo_nombre'] . ' - ' . $organismo['LiquidacionIntercambio']['liquidacion_id'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
				$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
				$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
				if(empty($cuentaOrganismo)):
					$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'ORGANISMO INEXISTENTE EN CONFIGURACION DE ASIENTO (' . $recibo['Recibo']['tipo_documento'] . '-' . $recibo['Recibo']['letra'] . '-' . $recibo['Recibo']['sucursal'] . '-' . $recibo['Recibo']['nro_recibo'] . ')'; 

				else:
					$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaOrganismo[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
					
					if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
						$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
						$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA INEXISTENTE EN ORGANISMO (' . $recibo['Recibo']['tipo_documento'] . '-' . $recibo['Recibo']['letra'] . '-' . $recibo['Recibo']['sucursal'] . '-' . $recibo['Recibo']['nro_recibo'] . ')'; 
					endif;
				endif;
				$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
				$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
				$oMutualTemporalAsientoRenglon->id = 0;
				$oMutualTemporalAsientoRenglon->save($temporalAsiento);
			endforeach;
		endif;		
		
				
		return true;
	}
	
	
	

	function getAsientoReciboAdelantoCredito($recibo){
		$oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$oSolicitud = $this->importarModelo('Solicitud', 'v1');
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$oProveedor = $this->importarModelo('proveedor', 'proveedores');
		$cModulo = 'RECIADELCRED';
				
		$solicitud = $oSolicitud->getSolicitud($recibo['Recibo']['nro_solicitud']);
		$aProveedor = $oProveedor->read(null, $solicitud['Producto']['Proveedor']['idr']);

		if(empty($aProveedor)):
			$temporalAsiento = array();
			$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $recibo['Recibo']['fecha_comprobante'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = ($recibo['Recibo']['importe'] - $recibo['Recibo']['aporte_socio']) * (-1);
			$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $recibo['Recibo']['razon_social'] . ' - ' . $recibo['Recibo']['nro_solicitud'] . ' - ' . $recibo['Recibo']['comentarios'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'PROVEEDOR NO EXISTE O NO CONFIGURADA LA CUENTA (' . $recibo['Recibo']['tipo_documento'] . '-' . $recibo['Recibo']['letra'] . '-' . $recibo['Recibo']['sucursal'] . '-' . $recibo['Recibo']['nro_recibo'] . ')'; 
			$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
			$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
			
			$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($temporalAsiento);
			
		else:
		
			$temporalAsiento = array();
			$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $recibo['Recibo']['fecha_comprobante'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $aProveedor['Proveedor']['co_plan_cuenta_id'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = ($recibo['Recibo']['importe'] - $recibo['Recibo']['aporte_socio']) * (-1);
			$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $recibo['Recibo']['razon_social'] . ' - ' . $recibo['Recibo']['nro_solicitud'] . ' - ' . $recibo['Recibo']['comentarios'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
			$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
			$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
			
			if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA INEXISTENTE EN PROVEEDOR (' . $recibo['Recibo']['numero_string2'] . ')'; 
			endif;
			$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($temporalAsiento);
		endif;		
		
		if($recibo['Recibo']['aporte_socio'] > 0):
			$cuentaAporteSocio = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'SOCIO','MutualCuentaAsiento.tipo_producto' => 'SOLICITUD', 'MutualCuentaAsiento.tipo_cuota' => 'APORTESOCIO')));

			$temporalAsiento = array();
			$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $recibo['Recibo']['fecha_comprobante'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = ($recibo['Recibo']['aporte_socio']) * (-1);
			$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $recibo['Recibo']['razon_social'] . ' - ' . $recibo['Recibo']['nro_solicitud'] . ' - ' . $recibo['Recibo']['comentarios'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
			$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
			$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
			
			if(empty($cuentaAporteSocio)):
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA APORTE SOCIO NO EXITE (' . $recibo['Recibo']['numero_string2'] . ')'; 

			else:
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAporteSocio[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
					
				if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
					$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA APORTE SOCIO NO CONFIGURADA (' . $recibo['Recibo']['numero_string2'] . ')'; 
				endif;
			endif;
			
			$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($temporalAsiento);
		endif;
		
		return true;
	}
	

	/*
	 * Busco la compensacion de las facturas de proveedores con los anticipos
	 * @param $fecha_desde
	 * @param $fecha_hasta
	 */
	function getClienteFacturaAnticipo($fecha_desde, $fecha_hasta){
		$sql = "SELECT	Cliente.razon_social, ReciboDetalle.tipo_cobro, ClienteFactura.cliente_tipo_asiento_id, ReciboFactura.*
				FROM	recibo_facturas ReciboFactura
				INNER	JOIN recibo_detalles ReciboDetalle
				ON		ReciboFactura.recibo_detalle_id = ReciboDetalle.id
				INNER	JOIN clientes Cliente
				ON		ReciboFactura.cliente_id = Cliente.id
				INNER	JOIN cliente_facturas ClienteFactura
				ON		ReciboFactura.cliente_factura_id = ClienteFactura.id
				WHERE	ReciboFactura.recibo_id = 0 AND ReciboDetalle.tipo_cobro = 'AN' AND
						ReciboFactura.fecha > '$fecha_desde' AND ReciboFactura.fecha <= '$fecha_hasta' 
					   ";
		
		return $this->query($sql);
	}
		
		
	function getAsientoClienteFacturaAnticipo($factura, $procesoId, $agrupar=0){
		$oClienteFactura = $this->importarModelo('ClienteFactura', 'clientes');
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$cModulo = 'ANTIFACT';
		
		
		$temporalAsiento = array();
		$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $factura['ReciboFactura']['fecha'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $factura['ReciboFactura']['importe'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $factura['Cliente']['razon_social'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
		$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
		$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
		$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
		
		$cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'COMPR', 'MutualCuentaAsiento.tipo_producto' => 'RECIBO', 'MutualCuentaAsiento.tipo_cuota' => 'ANTICIPO')));
		if(empty($cuentaAsiento)):
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'FALTA DEFINIR LA CUENTA ANTICIPO - ' . $factura['ReciboFactura']['id']; 
		else:
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
		endif;

		$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
		$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
		$oMutualTemporalAsientoRenglon->id = 0;
		$oMutualTemporalAsientoRenglon->save($temporalAsiento);
		
		$temporalAsiento = array();
		$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $factura['ReciboFactura']['fecha'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $factura['ReciboFactura']['importe'] * (-1);
		$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $factura['Cliente']['razon_social'] . ' ** (COMP.ANTICIPO) **';
		$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
		$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
		$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
		$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
		
		$asientoTipo = $oClienteFactura->getCodigoPlanCuenta($factura['ReciboFactura']['cliente_factura_id']);
		$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $asientoTipo['co_plan_cuenta_id'];
		if($asientoTipo['error'] != 'OK'):
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = $asientoTipo['error'] . '-' . $factura['ReciboFactura']['cliente_factura_id']; 
		endif;
			
		$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
		$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
		$oMutualTemporalAsientoRenglon->id = 0;
		$oMutualTemporalAsientoRenglon->save($temporalAsiento);
				
		if($agrupar == 0):
			$queryImporte = "SELECT SUM(importe) as importe 
							FROM mutual_temporal_asiento_renglones
							WHERE mutual_asiento_id = 0
							GROUP BY mutual_asiento_id, co_plan_cuenta_id";
			$importeAsiento = $this->query($queryImporte);
					
			$debe = 0;
			$haber = 0;
			foreach($importeAsiento as $importe):
				if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
				else  $haber += $importe[0]['importe'];
			endforeach;
					
			$aMutualAsiento = array();
			$aMutualAsiento['MutualAsiento']['id'] = 0;
			$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
			$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
			$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
			$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
			$aMutualAsiento['MutualAsiento']['fecha'] = $factura['ReciboFactura']['fecha'];
			$aMutualAsiento['MutualAsiento']['tipo_documento'] = 'ANT';
			$aMutualAsiento['MutualAsiento']['nro_documento'] = $factura['ReciboFactura']['cliente_factura_id'];
			$aMutualAsiento['MutualAsiento']['referencia'] = 'COMP.ANTICIPO CON FACT. (' . $factura['Cliente']['razon_social'] . ')';
			$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
			$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
			$aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
			$aMutualAsiento['MutualAsiento']['tipo_asiento'] = $factura['ClienteFactura']['cliente_tipo_asiento_id'];
				
			$this->grabarAsiento($aMutualAsiento, $procesoId);	
		endif;			

		return true;
				
	}
	
	
	/**
	 * Procesa las orden de pagos entre fecha para la generacion de asientos
	 * 
	 */
	function getAsientoOPago($OrdenPago, $procesoId, $agrupar=0){
		$oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		
			foreach($OrdenPago['detalle'] as $detalle):

				if($detalle['tipo_pago'] == 'AN' || $detalle['proveedor_factura_id'] > 0):
					$cModulo = 'PAGOPROV';
					$this->getAsientoOPagoProveedor($detalle, $OrdenPago['OrdenPago']['fecha_pago'], $OrdenPago);
				
				elseif($detalle['mutual_producto_solicitud_id'] > 0):
					$cModulo = 'PAGOPROD';
					$this->getAsientoOPagoMutualSolicitud($detalle, $OrdenPago['OrdenPago']['fecha_pago'], $OrdenPago);
		
				elseif($detalle['socio_reintegro_id'] > 0):
					$cModulo = 'PAGOREIN';
					$this->getAsientoOPagoSocioReintegro($detalle, $OrdenPago['OrdenPago']['fecha_pago'], $OrdenPago);
				
				elseif($detalle['nro_solicitud'] > 0):
					$cModulo = 'PAGOADELCRED';
					$this->getAsientoOPagoAdelantoCredito($detalle, $OrdenPago['OrdenPago']['fecha_pago'], $OrdenPago);

				endif;
			endforeach;
		
			foreach($OrdenPago['forma'] as $key => $forma):
				$temporalAsiento = array();
				$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $OrdenPago['OrdenPago']['fecha_pago'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $oBancoCuenta->getCodigoPlanCuenta($forma['banco_cuenta_id']);
				$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $forma['importe'] * (-1);
				$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $OrdenPago['OrdenPago']['Proveedor']['razon_social'] . '-' . $forma['descripcion_pago'] . '-' . $OrdenPago['OrdenPago']['tipo_comprobante_desc'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
				
				$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
				$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
				$oMutualTemporalAsientoRenglon->id = 0;
				$oMutualTemporalAsientoRenglon->save($temporalAsiento);
			
			endforeach;
		
			if($agrupar == 0):

				$queryImporte = "SELECT SUM(importe) as importe 
								FROM mutual_temporal_asiento_renglones
								WHERE mutual_asiento_id = 0
								GROUP BY mutual_asiento_id, co_plan_cuenta_id";
				$importeAsiento = $this->query($queryImporte);
					
				$debe = 0;
				$haber = 0;
				foreach($importeAsiento as $importe):
					if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
					else  $haber += $importe[0]['importe'];
				endforeach;
		
				
				$aMutualAsiento = array();
				$aMutualAsiento['MutualAsiento']['id'] = 0;
				$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
				$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
				$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
				$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
				$aMutualAsiento['MutualAsiento']['fecha'] = $OrdenPago['OrdenPago']['fecha_pago'];
				$aMutualAsiento['MutualAsiento']['tipo_documento'] = $OrdenPago['OrdenPago']['tipo_documento'];
				$aMutualAsiento['MutualAsiento']['nro_documento'] = $OrdenPago['OrdenPago']['sucursal'] . '-' . $OrdenPago['OrdenPago']['nro_orden_pago'];
				$aMutualAsiento['MutualAsiento']['referencia'] = (empty($OrdenPago['OrdenPago']['comentario']) ? $OrdenPago['OrdenPago']['Proveedor']['razon_social'] : $OrdenPago['OrdenPago']['comentario']);
				$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
				$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
				$aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
				
				$this->grabarAsiento($aMutualAsiento, $procesoId);			
			endif;
		
	}
	
	function getAsientoOPagoProveedor($detalle, $fecha, $OrdenPago){

		$oProveedorFactura = $this->importarModelo('ProveedorFactura', 'proveedores');
		$oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$cModulo = 'PAGOPROV';		
		
		$temporalAsiento = array();
		$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $fecha;
		$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $detalle['importe'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $OrdenPago['OrdenPago']['Proveedor']['razon_social'] . '-' . $detalle['tipo_comprobante_desc'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
		$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
		$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = 'PAGOPROV';
		$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;

		if($detalle['tipo_pago'] == 'AN'){
                    ####################################################################################################
                    # CONFIGURO LA CUENTA DE ANTICIPO. Las Ordenes de Pagos y Recibos hacen anticipos, esto pueden ir
                    # a la cuenta contable de ANTICIPO o a la cuenta contable correspondiente al PROVEEDOR/CLIENTE.
                    ####################################################################################################
                    $cnfCtaAnticipo = $this->getGlobalDato('LOGICO_1', 'CONTANTI');

                    if(!$cnfCtaAnticipo['GlobalDato']['LOGICO_1']){
                        $cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'COMPR', 'MutualCuentaAsiento.tipo_producto' => 'ORDENPAGO', 'MutualCuentaAsiento.tipo_cuota' => 'ANTICIPO')));
                        if(empty($cuentaAsiento)){
                            $temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
                            $temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                            $temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'FALTA DEFINIR LA CUENTA ANTICIPO'; 
                        }
                        else{
                            $temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
                        }
                    }
                    else{
//                        $oProveedor = $this->importarModelo('Proveedor', 'proveedores');
//                        $aProveedor = $oProveedor->getProveedor($OrdenPago['OrdenPago']['Proveedor']['proveedor_id']);
                        $aProveedor = $OrdenPago['OrdenPago']['Proveedor'];

                        if(empty($aProveedor['co_plan_cuenta_id'])){
                            $temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
                            $temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                            $temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'FALTA DEFINIR LA CUENTA EN PROVEEDOR'; 
                        }
                        else{
                            $temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $aProveedor['co_plan_cuenta_id'];
                        }
                    }
                    
                }
		else{
                    $asientoTipo = $oProveedorFactura->getCodigoPlanCuenta($detalle['proveedor_factura_id']);
                    $temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $asientoTipo['co_plan_cuenta_id'];
                    if($asientoTipo['error'] != 'OK'){
                        $temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                        $temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = $asientoTipo['error'] . '-' . $detalle['proveedor_factura_id']; 
                    }

                }

		$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
		$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
		$oMutualTemporalAsientoRenglon->id = 0;
		$oMutualTemporalAsientoRenglon->save($temporalAsiento);

		return true;
		
	}
	

	function getAsientoOPagoMutualSolicitud($detalle, $fecha, $OrdenPago){
		$oMutualProductoSolicitud = $this->importarModelo('MutualProductoSolicitud', 'mutual');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		
		$temporalAsiento = array();
		$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $fecha;
		$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $detalle['importe'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $OrdenPago['OrdenPago']['Proveedor']['razon_social'] . '-' . $detalle['tipo_comprobante_desc'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
		$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 

		$asientoTipo = $oMutualProductoSolicitud->getCodigoPlanCuentaOPago($detalle['mutual_producto_solicitud_id']);
		$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $asientoTipo['co_plan_cuenta_id'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = 'PAGOPROD'; 
		$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
		$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
		$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
		if($asientoTipo['error'] != 'OK'):
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = $detalle['mutual_producto_solicitud_id'] . '-' . $asientoTipo['error']; 
		endif;
			
		$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
		$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
		$oMutualTemporalAsientoRenglon->id = 0;
		$oMutualTemporalAsientoRenglon->save($temporalAsiento);
			
		return true;	
	}
	
	
	function getAsientoOPagoSocioReintegro($detalle, $fecha, $OrdenPago){ 
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$cuentaReintegro = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'SOCIO','MutualCuentaAsiento.tipo_producto' => 'REINTEGRO')));

		$temporalAsiento = array();
		$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $fecha;
		$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
		$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $detalle['importe'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $OrdenPago['OrdenPago']['Proveedor']['razon_social'] . '-' . $detalle['tipo_comprobante_desc'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
		$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
	
		if(empty($cuentaReintegro)):
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA REINTEGRO NO EXITE'; 
	
		else:
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaReintegro[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
						
			if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA REINTEGRO NO CONFIGURADA'; 
			endif;
		endif;
				
		$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
		$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = 'PAGOREIN';
		$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
		
		$oMutualTemporalAsientoRenglon->id = 0;
		$oMutualTemporalAsientoRenglon->save($temporalAsiento);
		
		return true;
	}
	
	
	function getAsientoOPagoAdelantoCredito($detalle, $fecha, $OrdenPago){
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$oSolicitud = $this->importarModelo('Solicitud', 'v1');
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$oProveedor = $this->importarModelo('proveedor', 'proveedores');
		
		$solicitud = $oSolicitud->getSolicitud($detalle['nro_solicitud']);
		$aProveedor = $oProveedor->read(null, $solicitud['Producto']['Proveedor']['idr']);

		if(empty($aProveedor)):
			$temporalAsiento = array();
			$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $fecha;
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $detalle['importe'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $OrdenPago['OrdenPago']['Proveedor']['razon_social'] . '-' . $detalle['tipo_comprobante_desc'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'PROVEEDOR NO EXISTE O NO CONFIGURADO LA CUENTA - ' . $detalle['nro_solicitud']; 
				
			$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = 'PAGOADELCRED';
			$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
			
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($temporalAsiento);
			
		else:
		
			$temporalAsiento = array();
			$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $fecha;
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $aProveedor['Proveedor']['co_plan_cuenta_id'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $detalle['importe'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $OrdenPago['OrdenPago']['Proveedor']['razon_social'] . '-' . $detalle['tipo_comprobante_desc'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
					
			if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA INEXISTENTE EN PROVEEDOR - ' . $detalle['nro_solicitud']; 
			endif;
			$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = 'PAGOADELCRED';
			$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
			
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($temporalAsiento);
		endif;		
		
		
		return true;
	}
	

	/*
	 * Busco la compensacion de las facturas de proveedores con los anticipos
	 * @param $fecha_desde
	 * @param $fecha_hasta
	 */
	function getProveedorFacturaAnticipo($fecha_desde, $fecha_hasta){
		$sql = "SELECT Proveedor.razon_social, OrdenPagoDetalle.tipo_pago, ProveedorFactura.proveedor_tipo_asiento_id, OrdenPagoFactura.*
				FROM orden_pago_facturas OrdenPagoFactura
				INNER JOIN orden_pago_detalles OrdenPagoDetalle
				ON OrdenPagoFactura.orden_pago_detalle_id = OrdenPagoDetalle.id
				INNER JOIN proveedores Proveedor
				ON OrdenPagoFactura.proveedor_id = Proveedor.id
				INNER JOIN proveedor_facturas ProveedorFactura
				ON OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id
				WHERE OrdenPagoFactura.orden_pago_id = 0 AND OrdenPagoDetalle.tipo_pago = 'AN' AND 
						OrdenPagoFactura.fecha > '$fecha_desde' AND OrdenPagoFactura.fecha <= '$fecha_hasta'
				ORDER BY OrdenPagoFactura.fecha
			   ";
		
		return $this->query($sql);
	}
		
		
	function getAsientoProveedorFacturaAnticipo($factura, $procesoId, $agrupar=0){
		$oProveedorFactura = $this->importarModelo('ProveedorFactura', 'proveedores');
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$cModulo = 'ANTIFACT';
		
		$temporalAsiento = array();
		$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $factura['OrdenPagoFactura']['fecha'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $factura['OrdenPagoFactura']['importe'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $factura['Proveedor']['razon_social'] . ' ** (COMP.ANTICIPO) **';
		$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
		$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
		$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
		$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
		
		$asientoTipo = $oProveedorFactura->getCodigoPlanCuenta($factura['OrdenPagoFactura']['proveedor_factura_id']);
		$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $asientoTipo['co_plan_cuenta_id'];
		if($asientoTipo['error'] != 'OK'):
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = $asientoTipo['error'] . '-' . $factura['OrdenPagoFactura']['proveedor_factura_id']; 
		endif;
			
		$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
		$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
		$oMutualTemporalAsientoRenglon->id = 0;
		$oMutualTemporalAsientoRenglon->save($temporalAsiento);
		
		
		$temporalAsiento = array();
		$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $factura['OrdenPagoFactura']['fecha'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $factura['OrdenPagoFactura']['importe'] * (-1);
		$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $factura['Proveedor']['razon_social'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
		$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
		$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
		$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
		
		$cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'COMPR', 'MutualCuentaAsiento.tipo_producto' => 'ORDENPAGO', 'MutualCuentaAsiento.tipo_cuota' => 'ANTICIPO')));
		if(empty($cuentaAsiento)):
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'FALTA DEFINIR LA CUENTA ANTICIPO - ' . $factura['OrdenPagoFactura']['id']; 
		else:
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
		endif;

		$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
		$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
		$oMutualTemporalAsientoRenglon->id = 0;
		$oMutualTemporalAsientoRenglon->save($temporalAsiento);
				
		if($agrupar == 0):
			$queryImporte = "SELECT SUM(importe) as importe 
							FROM mutual_temporal_asiento_renglones
							WHERE mutual_asiento_id = 0
							GROUP BY mutual_asiento_id, co_plan_cuenta_id";
			$importeAsiento = $this->query($queryImporte);
					
			$debe = 0;
			$haber = 0;
			foreach($importeAsiento as $importe):
				if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
				else  $haber += $importe[0]['importe'];
			endforeach;
					
			$aMutualAsiento = array();
			$aMutualAsiento['MutualAsiento']['id'] = 0;
			$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
			$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
			$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
			$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
			$aMutualAsiento['MutualAsiento']['fecha'] = $factura['OrdenPagoFactura']['fecha'];
			$aMutualAsiento['MutualAsiento']['tipo_documento'] = 'ANT';
			$aMutualAsiento['MutualAsiento']['nro_documento'] = $factura['OrdenPagoFactura']['proveedor_factura_id'];
			$aMutualAsiento['MutualAsiento']['referencia'] = 'COMP.ANTICIPO CON FACT. (' . $factura['Proveedor']['razon_social'] . ')';
			$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
			$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
			$aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
			$aMutualAsiento['MutualAsiento']['tipo_asiento'] = $factura['ProveedorFactura']['proveedor_tipo_asiento_id'];
				
			$this->grabarAsiento($aMutualAsiento, $procesoId);	
		endif;			

		return true;
				
	}
	
	
	/*
	 * Proceso Asiento cobro de liquidacion
	 * @param $fecha_desde
	 * @param $fecha_hasta
	 * @param $procesoId
	 */
	function getCobroLiquidacion($fecha_desde, $fecha_hasta){
//		$sqlCobro = "SELECT	
//						OrdenDescuentoCobro.fecha, OrdenDescuentoCobro.periodo_cobro, OrdenDescuentoCobro.proveedor_origen_fondo_id, PersonaBeneficio.codigo_beneficio, 
//						SUM(OrdenDescuentoCobroCuota.importe) AS importe_cobrado, OrdenDescuentoCuota.*
//					FROM	
//						orden_descuento_cuotas OrdenDescuentoCuota 
//					INNER JOIN 
//						orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
//					ON	
//						OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id
//					INNER JOIN 
//						orden_descuento_cobros OrdenDescuentoCobro
//					ON	
//						OrdenDescuentoCobro.id = OrdenDescuentoCobroCuota.orden_descuento_cobro_id
//					INNER JOIN 
//						persona_beneficios PersonaBeneficio
//					ON	
//						PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id
//					WHERE	
//						OrdenDescuentoCobro.tipo_cobro = 'MUTUTCOBRECS' AND OrdenDescuentoCobro.fecha > '$fecha_desde' AND OrdenDescuentoCobro.fecha <= '$fecha_hasta'
//					GROUP BY 
//						OrdenDescuentoCobro.fecha, PersonaBeneficio.codigo_beneficio, OrdenDescuentoCuota.tipo_orden_dto, 
//						OrdenDescuentoCuota.tipo_producto, OrdenDescuentoCuota.tipo_cuota
//					ORDER BY 
//						OrdenDescuentoCobro.fecha, PersonaBeneficio.codigo_beneficio
//		";
            
//                $sqlCobro = "SELECT 
//                                    OrdenDescuentoCobro.fecha, OrdenDescuentoCobro.periodo_cobro, OrdenDescuentoCobro.proveedor_origen_fondo_id, LiquidacionCuota.liquidacion_id, LiquidacionCuota.codigo_organismo, 
//                                    SUM(LiquidacionCuota.importe_debitado) AS importe_cobrado, LiquidacionCuota.*
//                            FROM	
//                                    orden_descuento_cuotas OrdenDescuentoCuota 
//                            INNER JOIN 
//                                    orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
//                            ON	
//                                    OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id
//                            INNER JOIN 
//                                    orden_descuento_cobros OrdenDescuentoCobro
//                            ON	
//                                    OrdenDescuentoCobro.id = OrdenDescuentoCobroCuota.orden_descuento_cobro_id
//                            INNER JOIN 
//                                    persona_beneficios PersonaBeneficio
//                            ON	
//                                    PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id
//                            INNER JOIN
//                                    liquidacion_cuotas LiquidacionCuota
//                            ON
//                                    LiquidacionCuota.orden_descuento_cobro_id = OrdenDescuentoCobro.id
//                            WHERE	
//                                    OrdenDescuentoCobro.tipo_cobro = 'MUTUTCOBRECS' AND OrdenDescuentoCobro.fecha > '2012-12-31' AND OrdenDescuentoCobro.fecha <= '2013-12-31'
//                            GROUP BY 
//                                    OrdenDescuentoCobro.fecha, LiquidacionCuota.codigo_organismo, LiquidacionCuota.tipo_orden_dto, 
//                                    LiquidacionCuota.tipo_producto, LiquidacionCuota.tipo_cuota
//                            ORDER BY 
//                                    OrdenDescuentoCobro.fecha, LiquidacionCuota.codigo_organismo
//                ";
                    
		
                $sqlCobro = "SELECT 
                                        OrdenDescuentoCobro.fecha, OrdenDescuentoCobro.periodo_cobro, OrdenDescuentoCobro.proveedor_origen_fondo_id, LiquidacionCuota.liquidacion_id, LiquidacionCuota.codigo_organismo, 
                                        SUM(LiquidacionCuota.importe_debitado) AS importe_cobrado, LiquidacionCuota.*
                                FROM	liquidacion_cuotas LiquidacionCuota
                                INNER	JOIN orden_descuento_cobros OrdenDescuentoCobro
                                ON	LiquidacionCuota.orden_descuento_cobro_id = OrdenDescuentoCobro.id
                                WHERE	OrdenDescuentoCobro.fecha > '$fecha_desde' AND OrdenDescuentoCobro.fecha <= '$fecha_hasta'
                                GROUP	BY OrdenDescuentoCobro.fecha, LiquidacionCuota.liquidacion_id, LiquidacionCuota.tipo_orden_dto, LiquidacionCuota.tipo_producto, LiquidacionCuota.tipo_cuota, LiquidacionCuota.proveedor_id
                                ORDER	BY OrdenDescuentoCobro.fecha, LiquidacionCuota.liquidacion_id
                ";

		return $this->query($sqlCobro);
		
	}
	
	
	function getAsientoLiquidacion($aLiquidaciones, $procesoId, $agrupar=0){
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$oTipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		

		$nIndice = 0;
		$renglones = count($aLiquidaciones);
		while ($nIndice < $renglones):
			$fecha_corte = $aLiquidaciones[$nIndice]['OrdenDescuentoCobro']['fecha'];
			$organismo = $aLiquidaciones[$nIndice]['PersonaBeneficio']['codigo_beneficio'];
			$total_organismo = 0;
			$temporalAsiento = array();

			
			while ($fecha_corte == $aLiquidaciones[$nIndice]['OrdenDescuentoCobro']['fecha'] && 
					$organismo == $aLiquidaciones[$nIndice]['PersonaBeneficio']['codigo_beneficio'] && $nIndice < $renglones):
				
				$total_organismo += $aLiquidaciones[$nIndice][0]['importe_cobrado'];
				
				$conditions = array('MutualCuentaAsiento.tipo_orden_dto' => $aLiquidaciones[$nIndice]['OrdenDescuentoCuota']['tipo_orden_dto'], 
									'MutualCuentaAsiento.tipo_producto' => $aLiquidaciones[$nIndice]['OrdenDescuentoCuota']['tipo_producto'], 
									'MutualCuentaAsiento.tipo_cuota' => $aLiquidaciones[$nIndice]['OrdenDescuentoCuota']['tipo_cuota']);
				
				$cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => $conditions));
				$tmpAsiento = array();
				$tmpAsiento['MutualTemporalAsientoRenglon']['fecha'] = $fecha_corte;
				if($cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'] == 0 && $cuentaAsiento[0]['MutualCuentaAsiento']['mutual_tipo_asiento_id'] == 0):
					$tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = '';
					$tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aLiquidaciones[$nIndice][0]['importe_cobrado'] * (-1);
					$tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'Or.Dto.: ' . $aLiquidaciones[$nIndice]['OrdenDescuentoCuota']['orden_descuento_id'] . 'Cta. ' . $aLiquidaciones[$nIndice]['OrdenDescuentoCuota']['nro_cuota'];
					$tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
					$tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
				else:
					if(!empty($cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'])):
						$tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
						$tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aLiquidaciones[$nIndice][0]['importe_cobrado'] * (-1);
					else:
						$tipoAsiento = $oTipoAsientoRenglon->find('all', array('conditions' => array('MutualTipoAsientoRenglon.mutual_tipo_asiento_id' => $cuentaAsiento[0]['MutualCuentaAsiento']['mutual_tipo_asiento_id'], 'MutualTipoAsientoRenglon.variable' => 'TOTAL')));
						if(empty($tipoAsiento)):
							$tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
							$tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aLiquidaciones[$nIndice][0]['importe_cobrado'] * (-1);
						else:
							$tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $tipoAsiento[0]['MutualTipoAsientoRenglon']['co_plan_cuenta_id'];
							$tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = ($tipoAsiento[0]['MutualTipoAsientoRenglon']['debe_haber'] == 'D' ? $aLiquidaciones[$nIndice][0]['importe_cobrado'] * (-1) : $aLiquidaciones[$nIndice][0]['importe_cobrado']);
						endif;
					endif;
					$tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'Or.Dto.: ' . $aLiquidaciones[$nIndice]['OrdenDescuentoCuota']['orden_descuento_id'] . ' Cta. ' . $aLiquidaciones[$nIndice]['OrdenDescuentoCuota']['nro_cuota'];
					$tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
					$tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
					if(empty($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
						$tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
						$tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
					endif;
				endif;
				
				$cuenta = $this->getCuenta($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
				$tmpAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
				$tmpAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
				
				array_push($temporalAsiento, $tmpAsiento);
				$nIndice += 1;			
			endwhile;

			$glb = $this->getGlobalDato('concepto_1',$organismo);
			$cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'ORGAN', 'MutualCuentaAsiento.tipo_producto' => $organismo)));
			
			$asientoOrganismo = array();
			$asientoOrganismo['MutualTemporalAsientoRenglon']['fecha'] = $fecha_corte;
			$asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
			$asientoOrganismo['MutualTemporalAsientoRenglon']['importe'] = $total_organismo;
			$asientoOrganismo['MutualTemporalAsientoRenglon']['referencia'] = $glb['GlobalDato']['concepto_1'];
			$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 0;
			$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
						
			if(empty($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
				$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 1;
				$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
			endif;

			$cuenta = $this->getCuenta($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$asientoOrganismo['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$asientoOrganismo['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($asientoOrganismo);
			
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->saveAll($temporalAsiento);
			
			if($agrupar == 0):

				$queryImporte = "SELECT SUM(importe) as importe 
								FROM mutual_temporal_asiento_renglones
								WHERE mutual_asiento_id = 0
								GROUP BY mutual_asiento_id, co_plan_cuenta_id";
				$importeAsiento = $this->query($queryImporte);
					
				$debe = 0;
				$haber = 0;
				foreach($importeAsiento as $importe):
					if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
					else  $haber += $importe[0]['importe'];
				endforeach;
				
				$aMutualAsiento = array();
				$aMutualAsiento['MutualAsiento']['id'] = 0;
				$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
				$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
				$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
				$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
				$aMutualAsiento['MutualAsiento']['fecha'] = $fecha_corte;
				$aMutualAsiento['MutualAsiento']['tipo_documento'] = '';
				$aMutualAsiento['MutualAsiento']['nro_documento'] = 'ORDEN DESCUENTO COBRO';
				$aMutualAsiento['MutualAsiento']['referencia'] = 'LIQUIDACION ' . $glb['GlobalDato']['concepto_1'];
				$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
				$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
					
				$this->grabarAsiento($aMutualAsiento, $procesoId);			
			endif;
						
		endwhile;
	}
	

	function getAsientoLiquiRenglon($aLiquidacion){
            $oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
            $oTipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
            $cModulo = 'LIQUCOBR';

            $tmpAsiento = array();
            /*
             * Separo lo que pertenece a la mutual de lo que corresponde a los comercio como cuenta
             * a imputar. Por que hay codigo que pueden formar parte de varias cuentas del plan de cuenta.
             * 08/09/2017
             * La variable tiene que ser MUTUALPROVEEDORID
             */

            if($aLiquidacion['LiquidacionCuota']['proveedor_id'] == 18){
                
                $conditions = array('MutualCuentaAsiento.tipo_orden_dto' => $aLiquidacion['LiquidacionCuota']['tipo_orden_dto'], 
                                                        'MutualCuentaAsiento.tipo_producto' => $aLiquidacion['LiquidacionCuota']['tipo_producto'], 
                                                        'MutualCuentaAsiento.tipo_cuota' => $aLiquidacion['LiquidacionCuota']['tipo_cuota']);

                $cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => $conditions));
                $tmpAsiento['MutualTemporalAsientoRenglon']['fecha'] = $aLiquidacion['OrdenDescuentoCobro']['fecha'];
		if($cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'] == 0 && $cuentaAsiento[0]['MutualCuentaAsiento']['mutual_tipo_asiento_id'] == 0){
                    $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = '';
                    $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aLiquidacion[0]['importe_cobrado'] * (-1);
                    $tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'COBRO LIQUIDACION # ' . $aLiquidacion['LiquidacionCuota']['liquidacion_id'] . ' - PERIODO: ' . $aLiquidacion['OrdenDescuentoCobro']['periodo_cobro'];
                    $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                    $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO ' . $aLiquidacion['LiquidacionCuota']['tipo_orden_dto'] . '-' . $aLiquidacion['LiquidacionCuota']['tipo_producto'] . '-' . $aLiquidacion['LiquidacionCuota']['tipo_cuota']; 
                }
		else{
                    if(!empty($cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'])){
                        $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
                        $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aLiquidacion[0]['importe_cobrado'] * (-1);
                    }
                    else{
                        $tipoAsiento = $oTipoAsientoRenglon->find('all', array('conditions' => array('MutualTipoAsientoRenglon.mutual_tipo_asiento_id' => $cuentaAsiento[0]['MutualCuentaAsiento']['mutual_tipo_asiento_id'], 'MutualTipoAsientoRenglon.variable' => 'TOTAL')));
                        if(empty($tipoAsiento)){
                            $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
                            $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aLiquidacion[0]['importe_cobrado'] * (-1);
                        }
                        else{
                            $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $tipoAsiento[0]['MutualTipoAsientoRenglon']['co_plan_cuenta_id'];
                            $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = ($tipoAsiento[0]['MutualTipoAsientoRenglon']['debe_haber'] == 'D' ? $aLiquidacion[0]['importe_cobrado'] * (-1) : $aLiquidacion[0]['importe_cobrado']);
                        }
                    }
                    $tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'COBRO LIQUIDACION # ' . $aLiquidacion['LiquidacionCuota']['liquidacion_id'] . ' - PERIODO ' . $aLiquidacion['OrdenDescuentoCobro']['periodo_cobro'];
                    $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
                    $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
                    if(empty($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])){
                        $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                        $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO ' . $aLiquidacion['LiquidacionCuota']['tipo_orden_dto'] . '-' . $aLiquidacion['LiquidacionCuota']['tipo_producto'] . '-' . $aLiquidacion['LiquidacionCuota']['tipo_cuota']; 
                    }
                }
            }
            else{

                $cuentaProveedor = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'COMER', 'MutualCuentaAsiento.tipo_producto' => 'PROVEEDOR')));
                $tmpAsiento['MutualTemporalAsientoRenglon']['fecha'] = $aLiquidacion['OrdenDescuentoCobro']['fecha'];
		if(empty($cuentaProveedor)){
                    $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = '';
                    $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aLiquidacion[0]['importe_cobrado'] * (-1);
                    $tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'COBRO LIQUIDACION # ' . $aLiquidacion['LiquidacionCuota']['liquidacion_id'] . ' - PERIODO: ' . $aLiquidacion['OrdenDescuentoCobro']['periodo_cobro'];
                    $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                    $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO ' . $aLiquidacion['LiquidacionCuota']['tipo_orden_dto'] . '-' . $aLiquidacion['LiquidacionCuota']['tipo_producto'] . '-' . $aLiquidacion['LiquidacionCuota']['tipo_cuota']; 
                }
		else{
                    $coPlanCuentaId = $cuentaProveedor[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
                    $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaProveedor[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
                    $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aLiquidacion[0]['importe_cobrado'] * (-1);
                    $tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'COBRO LIQUIDACION # ' . $aLiquidacion['LiquidacionCuota']['liquidacion_id'] . ' - PERIODO ' . $aLiquidacion['OrdenDescuentoCobro']['periodo_cobro'];
                    $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
                    $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
                }
            }
            
            $cuenta = $this->getCuenta($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
            $tmpAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
            $tmpAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
            $tmpAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
            $tmpAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;

            return $tmpAsiento;
	}
	
	
	function getAsientoLiquiOrganismo($organismo, $total_organismo, $temporalAsiento, $fecha, $procesoId, $nLiquidacion=0, $agrupar=0){
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$oTipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$cModulo = 'LIQUCOBR';
				
		$glb = $this->getGlobalDato('concepto_1',$organismo);
		$cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'ORGAN', 'MutualCuentaAsiento.tipo_producto' => $organismo)));

		$asientoOrganismo = array();
		$asientoOrganismo['MutualTemporalAsientoRenglon']['fecha'] = $fecha;
		$asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
		$asientoOrganismo['MutualTemporalAsientoRenglon']['importe'] = $total_organismo;
		$asientoOrganismo['MutualTemporalAsientoRenglon']['referencia'] = $glb['GlobalDato']['concepto_1'] . ' LIQUIDACION # ' . $nLiquidacion;
		$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 0;
		$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
		$asientoOrganismo['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
		$asientoOrganismo['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
		
		if(empty($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
			$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 1;
			$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO '; 
		endif;

		$cuenta = $this->getCuenta($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
		$asientoOrganismo['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
		$asientoOrganismo['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
		$oMutualTemporalAsientoRenglon->id = 0;
		$oMutualTemporalAsientoRenglon->save($asientoOrganismo);
			
		$oMutualTemporalAsientoRenglon->id = 0;
		$oMutualTemporalAsientoRenglon->saveAll($temporalAsiento);
			
		if($agrupar == 0):

			$queryImporte = "SELECT SUM(importe) as importe 
							FROM mutual_temporal_asiento_renglones
							WHERE mutual_asiento_id = 0
							GROUP BY mutual_asiento_id, co_plan_cuenta_id";
			$importeAsiento = $this->query($queryImporte);
					
			$debe = 0;
			$haber = 0;
			foreach($importeAsiento as $importe):
				if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
				else  $haber += $importe[0]['importe'];
			endforeach;
			
			$aMutualAsiento = array();
			$aMutualAsiento['MutualAsiento']['id'] = 0;
			$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
			$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
			$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
			$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
			$aMutualAsiento['MutualAsiento']['fecha'] = $fecha;
			$aMutualAsiento['MutualAsiento']['tipo_documento'] = 'LIQ';
			$aMutualAsiento['MutualAsiento']['nro_documento'] = $nLiquidacion;
			$aMutualAsiento['MutualAsiento']['referencia'] = 'LIQUIDACION ' . $glb['GlobalDato']['concepto_1'];
			$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
			$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
			$aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
			$aMutualAsiento['MutualAsiento']['tipo_asiento'] = 0;
			
			$this->grabarAsiento($aMutualAsiento, $procesoId);			
		endif;
		
		return true;
	}
	
	
	
	/*
	 * PROCESO ASIENTO REVERSO
	 */
	function getReverso($fecha_desde, $fecha_hasta){
		$sqlReverso = "SELECT	
							BancoCuentaMovimiento.fecha_operacion AS fecha_reverso, OrdenDescuentoCobro.proveedor_origen_fondo_id, PersonaBeneficio.codigo_beneficio, 
							SUM(OrdenDescuentoCobroCuota.importe_reversado) AS importe_reversado, OrdenDescuentoCuota.*
						FROM	
							orden_descuento_cuotas OrdenDescuentoCuota 
						INNER JOIN 
							orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
						ON	
							OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id
						INNER JOIN 
							orden_descuento_cobros OrdenDescuentoCobro
						ON	
							OrdenDescuentoCobro.id = OrdenDescuentoCobroCuota.orden_descuento_cobro_id
						INNER JOIN 
							persona_beneficios PersonaBeneficio
						ON	
							PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id
						INNER JOIN
							banco_cuenta_movimientos BancoCuentaMovimiento
						ON
							BancoCuentaMovimiento.id = OrdenDescuentoCobroCuota.banco_cuenta_movimiento_id
						WHERE	
							BancoCuentaMovimiento.fecha_operacion > '$fecha_desde' AND BancoCuentaMovimiento.fecha_operacion <= '$fecha_hasta'
						GROUP BY 
							BancoCuentaMovimiento.fecha_operacion, PersonaBeneficio.codigo_beneficio, OrdenDescuentoCuota.tipo_orden_dto, 
							OrdenDescuentoCuota.tipo_producto, OrdenDescuentoCuota.tipo_cuota
						ORDER BY 
							fecha_reverso, PersonaBeneficio.codigo_beneficio
			";


		return $this->query($sqlReverso);
		
	}
	
	
        function getReversoLiquidacion($nLiquidacion){
            $sqlReverso = "SELECT Reverso.*, OrdenDescuentoCuota.*
                            FROM orden_descuento_cuotas AS OrdenDescuentoCuota
                            INNER JOIN (SELECT OrdenDescuentoCobroCuota.importe_reversado, OrdenDescuentoCobroCuota.orden_descuento_cuota_id, Liquidacion.codigo_organismo, Liquidacion.fecha_imputacion
                            FROM liquidaciones AS Liquidacion
                            INNER JOIN orden_descuento_cobro_cuotas AS OrdenDescuentoCobroCuota ON(OrdenDescuentoCobroCuota.periodo_proveedor_reverso = Liquidacion.periodo)
                            WHERE Liquidacion.id = '$nLiquidacion') AS Reverso ON(OrdenDescuentoCuota.id = Reverso.orden_descuento_cuota_id)
                            INNER JOIN persona_beneficios AS PersonaBeneficio ON (OrdenDescuentoCuota.persona_beneficio_id = PersonaBeneficio.id)
                            WHERE PersonaBeneficio.codigo_beneficio = Reverso.codigo_organismo
            ";
//            $sqlReverso = "SELECT Liquidacion.fecha_imputacion AS fecha_reverso, Liquidacion.codigo_organismo, OrdenDescuentoCobroCuota.importe_reversado, OrdenDescuentoCuota.*
//                            FROM orden_descuento_cobro_cuotas AS OrdenDescuentoCobroCuota
//                            INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota 
//                            ON (OrdenDescuentoCobroCuota.orden_descuento_cuota_id = OrdenDescuentoCuota.id)
//                            INNER JOIN persona_beneficios AS PersonaBeneficio ON (OrdenDescuentoCuota.persona_beneficio_id = PersonaBeneficio.id)
//                            INNER JOIN liquidaciones AS Liquidacion ON(OrdenDescuentoCobroCuota.periodo_proveedor_reverso = Liquidacion.periodo AND PersonaBeneficio.codigo_beneficio = Liquidacion.codigo_organismo)
//                            WHERE
//                            OrdenDescuentoCobroCuota.reversado = 1 AND Liquidacion.id = '$nLiquidacion'
//            ";

            return $this->query($sqlReverso);
		
        }
        
        
	function getReversoReintegros($fecha_desde, $fecha_hasta){
            $sqlReverso = "SELECT BancoCuentaMovimiento.fecha_operacion AS fecha_reverso, PersonaBeneficio.codigo_beneficio, SUM(SocioReintegro.importe_reversado) AS importe_reversado
                                FROM socio_reintegros SocioReintegro
                                INNER JOIN socios Socio
                                ON SocioReintegro.socio_id = Socio.id
                                INNER JOIN persona_beneficios PersonaBeneficio
                                ON PersonaBeneficio.id = Socio.persona_beneficio_id
                                INNER JOIN banco_cuenta_movimientos BancoCuentaMovimiento
                                ON BancoCuentaMovimiento.id = SocioReintegro.banco_cuenta_movimiento_id
                                WHERE BancoCuentaMovimiento.fecha_operacion > '$fecha_desde' AND BancoCuentaMovimiento.fecha_operacion <= '$fecha_hasta'
                                GROUP BY BancoCuentaMovimiento.fecha_operacion, PersonaBeneficio.codigo_beneficio
                                    ";


            return $this->query($sqlReverso);
		
	}
	
	
	function getReversoBanco($fecha_desde, $fecha_hasta){
		$sqlReverso = "SELECT	
							BancoCuentaMovimiento.banco_cuenta_id AS banco_cuenta_id, Banco.nombre, BancoCuentaMovimiento.importe AS importe, BancoCuenta.co_plan_cuenta_id AS co_plan_cuenta_id, 
							BancoCuentaMovimiento.fecha_operacion AS fecha_reverso, PersonaBeneficio.codigo_beneficio AS codigo_beneficio, 
							SUM(OrdenDescuentoCobroCuota.importe_reversado) AS importe_reversado, OrdenDescuentoCobroCuota.banco_cuenta_movimiento_id AS banco_cuenta_movimiento_id -- , OrdenDescuentoCuota.*
						FROM	
							orden_descuento_cuotas OrdenDescuentoCuota 
						INNER JOIN 
							orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
						ON	
							OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id
						INNER JOIN 
							orden_descuento_cobros OrdenDescuentoCobro
						ON	
							OrdenDescuentoCobro.id = OrdenDescuentoCobroCuota.orden_descuento_cobro_id
						INNER JOIN
							banco_cuenta_movimientos BancoCuentaMovimiento
						ON
							BancoCuentaMovimiento.id = OrdenDescuentoCobroCuota.banco_cuenta_movimiento_id
						INNER JOIN
							banco_cuentas BancoCuenta
						ON
							BancoCuenta.id = BancoCuentaMovimiento.banco_cuenta_id
						INNER JOIN 
							persona_beneficios PersonaBeneficio
						ON	
							PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id
						INNER JOIN bancos Banco
						ON	BancoCuenta.banco_id = Banco.id
						WHERE	
							BancoCuentaMovimiento.fecha_operacion > '$fecha_desde' AND BancoCuentaMovimiento.fecha_operacion <= '$fecha_hasta'
						GROUP BY 
							BancoCuentaMovimiento.fecha_operacion, banco_cuenta_movimiento_id, codigo_beneficio
						
						UNION
						
						(
						SELECT	
							BancoCuentaMovimiento.banco_cuenta_id AS banco_cuenta_id, Banco.nombre, BancoCuentaMovimiento.importe AS importe, BancoCuenta.co_plan_cuenta_id AS co_plan_cuenta_id, 
							BancoCuentaMovimiento.fecha_operacion AS fecha_reverso, PersonaBeneficio.codigo_beneficio AS codigo_beneficio, 
							SUM(SocioReintegro.importe_reversado) AS importe_reversado, SocioReintegro.banco_cuenta_movimiento_id AS banco_cuenta_movimiento_id
						FROM	
							socio_reintegros SocioReintegro
						INNER JOIN banco_cuenta_movimientos BancoCuentaMovimiento
						ON	BancoCuentaMovimiento.id = SocioReintegro.banco_cuenta_movimiento_id
						INNER JOIN banco_cuentas BancoCuenta
						ON	BancoCuenta.id = BancoCuentaMovimiento.banco_cuenta_id
						INNER JOIN socios Socio
						ON	Socio.id = SocioReintegro.socio_id
						INNER JOIN persona_beneficios PersonaBeneficio
						ON	PersonaBeneficio.id = Socio.persona_beneficio_id
						INNER JOIN bancos Banco
						ON	BancoCuenta.banco_id = Banco.id
						WHERE	
							BancoCuentaMovimiento.fecha_operacion > '$fecha_desde' AND BancoCuentaMovimiento.fecha_operacion <= '$fecha_hasta'
						GROUP BY 
							BancoCuentaMovimiento.fecha_operacion, banco_cuenta_movimiento_id, codigo_beneficio)
						ORDER BY 
							fecha_reverso, banco_cuenta_movimiento_id, codigo_beneficio
			";


		return $this->query($sqlReverso);
		
	}
	
	
	function getAsientoReverso($aReverso, $procesoId, $agrupar=0){
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$oTipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		

		$nIndice = 0;
		$renglones = count($aReverso);
		while ($nIndice < $renglones):
			$fecha_corte = $aReverso[$nIndice][0]['fecha_reverso'];
			$organismo = $aReverso[$nIndice]['PersonaBeneficio']['codigo_beneficio'];
			$total_organismo = 0;
			$temporalAsiento = array();

			
			while ($fecha_corte == $aReverso[$nIndice][0]['fecha_reverso'] && 
					$organismo == $aReverso[$nIndice]['PersonaBeneficio']['codigo_beneficio'] && $nIndice < $renglones):
				
				$total_organismo += $aReverso[$nIndice][0]['importe_reversado'];
				
				$conditions = array('MutualCuentaAsiento.tipo_orden_dto' => $aReverso[$nIndice]['OrdenDescuentoCuota']['tipo_orden_dto'], 
									'MutualCuentaAsiento.tipo_producto' => $aReverso[$nIndice]['OrdenDescuentoCuota']['tipo_producto'], 
									'MutualCuentaAsiento.tipo_cuota' => $aReverso[$nIndice]['OrdenDescuentoCuota']['tipo_cuota']);
				
				$cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => $conditions));
				$tmpAsiento = array();
				$tmpAsiento['MutualTemporalAsientoRenglon']['fecha'] = $fecha_corte;
				if($cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'] == 0 && $cuentaAsiento[0]['MutualCuentaAsiento']['mutual_tipo_asiento_id'] == 0):
					$tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = '';
					$tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aReverso[$nIndice][0]['importe_reversado'];
					$tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = ''; //'Or.Dto.: ' . $aReverso[$nIndice]['OrdenDescuentoCuota']['orden_descuento_id'] . 'Cta. ' . $aReverso[$nIndice]['OrdenDescuentoCuota']['nro_cuota'];
					$tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
					$tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
				else:
					if(!empty($cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'])):
						$tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
						$tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aReverso[$nIndice][0]['importe_reversado'];
					else:
						$tipoAsiento = $oTipoAsientoRenglon->find('all', array('conditions' => array('MutualTipoAsientoRenglon.mutual_tipo_asiento_id' => $cuentaAsiento[0]['MutualCuentaAsiento']['mutual_tipo_asiento_id'], 'MutualTipoAsientoRenglon.variable' => 'TOTAL')));
						if(empty($tipoAsiento)):
							$tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
							$tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aReverso[$nIndice][0]['importe_reversado'];
						else:
							$tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $tipoAsiento[0]['MutualTipoAsientoRenglon']['co_plan_cuenta_id'];
							$tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = ($tipoAsiento[0]['MutualTipoAsientoRenglon']['debe_haber'] == 'H' ? $aReverso[$nIndice][0]['importe_reversado'] * (-1) : $aReverso[$nIndice][0]['importe_reversado']);
						endif;
					endif;
					$tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = '';// 'Or.Dto.: ' . $aReverso[$nIndice]['OrdenDescuentoCuota']['orden_descuento_id'] . ' Cta. ' . $aReverso[$nIndice]['OrdenDescuentoCuota']['nro_cuota'];
					$tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
					$tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
					if(empty($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
						$tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
						$tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
					endif;
				endif;
				
				$cuenta = $this->getCuenta($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
				$tmpAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
				$tmpAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
				
				array_push($temporalAsiento, $tmpAsiento);
				$nIndice += 1;			
			endwhile;

			$glb = $this->getGlobalDato('concepto_1',$organismo);
			$cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'ORGAN', 'MutualCuentaAsiento.tipo_producto' => $organismo)));
			
			$asientoOrganismo = array();
			$asientoOrganismo['MutualTemporalAsientoRenglon']['fecha'] = $fecha_corte;
			$asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
			$asientoOrganismo['MutualTemporalAsientoRenglon']['importe'] = $total_organismo  * (-1);
			$asientoOrganismo['MutualTemporalAsientoRenglon']['referencia'] = $glb['GlobalDato']['concepto_1'];
			$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 0;
			$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
						
			if(empty($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
				$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 1;
				$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
			endif;

			$cuenta = $this->getCuenta($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$asientoOrganismo['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$asientoOrganismo['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
								
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->saveAll($temporalAsiento);
			
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($asientoOrganismo);
			
			if($agrupar == 0):

				$queryImporte = "SELECT SUM(importe) as importe 
								FROM mutual_temporal_asiento_renglones
								WHERE mutual_asiento_id = 0
								GROUP BY mutual_asiento_id, co_plan_cuenta_id";
				$importeAsiento = $this->query($queryImporte);
					
				$debe = 0;
				$haber = 0;
				foreach($importeAsiento as $importe):
					if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
					else  $haber += $importe[0]['importe'];
				endforeach;
				
				$aMutualAsiento = array();
				$aMutualAsiento['MutualAsiento']['id'] = 0;
				$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
				$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
				$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
				$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
				$aMutualAsiento['MutualAsiento']['fecha'] = $fecha_corte;
				$aMutualAsiento['MutualAsiento']['tipo_documento'] = '';
				$aMutualAsiento['MutualAsiento']['nro_documento'] = 'ORDEN DESCUENTO COBRO';
				$aMutualAsiento['MutualAsiento']['referencia'] = 'REVERSO ' . $glb['GlobalDato']['concepto_1'];
				$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
				$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
					
				$this->grabarAsiento($aMutualAsiento, $procesoId);			
			endif;
						
		endwhile;
	}
	
	
        function getAsientoReversoRenLiq($aReverso){
            $oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
            $oTipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
            $cModulo = 'LIQUREVE';

            /*
             * Verifico que las cuotas sean de los Comercio para imputarla a la cuenta correspondiente
             * 08/09/2017
             * Tiene que ir con la Variable MUTALPROVEEDORID
             */
            if($aReverso['OrdenDescuentoCuota']['proveedor_id'] != 18){
                $conditions = array('MutualCuentaAsiento.tipo_orden_dto' => 'COMER', 
                                    'MutualCuentaAsiento.tipo_producto' => 'PROVEEDOR');
            }else{
                $conditions = array('MutualCuentaAsiento.tipo_orden_dto' => $aReverso['OrdenDescuentoCuota']['tipo_orden_dto'], 
                                    'MutualCuentaAsiento.tipo_producto' => $aReverso['OrdenDescuentoCuota']['tipo_producto'], 
                                    'MutualCuentaAsiento.tipo_cuota' => $aReverso['OrdenDescuentoCuota']['tipo_cuota']);
            }		
            $cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => $conditions));
            $glb = $this->getGlobalDato('concepto_1',$aReverso['Reverso']['codigo_organismo']);
            $tmpAsiento = array();
            $tmpAsiento['MutualTemporalAsientoRenglon']['fecha'] = $aReverso['Reverso']['fecha_imputacion'];
            if($cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'] == 0 && $cuentaAsiento[0]['MutualCuentaAsiento']['mutual_tipo_asiento_id'] == 0):
                $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = '';
                $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aReverso['Reverso']['importe_reversado'];
                $tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'REVERSO ' . $glb['GlobalDato']['concepto_1']; //'Or.Dto.: ' . $aReverso[$nIndice]['OrdenDescuentoCuota']['orden_descuento_id'] . 'Cta. ' . $aReverso[$nIndice]['OrdenDescuentoCuota']['nro_cuota'];
                $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO -  ' . $aReverso['OrdenDescuentoCuota']['tipo_orden_dto'] . ' - ' . $aReverso['OrdenDescuentoCuota']['tipo_producto'] . ' - ' . $aReverso['OrdenDescuentoCuota']['tipo_cuota']; 
            else:
                if(!empty($cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'])):
                    $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
                    $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aReverso['Reverso']['importe_reversado'];
                else:
                    $tipoAsiento = $oTipoAsientoRenglon->find('all', array('conditions' => array('MutualTipoAsientoRenglon.mutual_tipo_asiento_id' => $cuentaAsiento[0]['MutualCuentaAsiento']['mutual_tipo_asiento_id'], 'MutualTipoAsientoRenglon.variable' => 'TOTAL')));
                    if(empty($tipoAsiento)):
                        $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
                        $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aReverso['Reverso']['importe_reversado'];
                    else:
                        $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $tipoAsiento[0]['MutualTipoAsientoRenglon']['co_plan_cuenta_id'];
                        $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = ($tipoAsiento[0]['MutualTipoAsientoRenglon']['debe_haber'] == 'H' ? $aReverso['Reverso']['importe_reversado'] * (-1) : $aReverso['Reverso']['importe_reversado']);
                    endif;
                endif;
                $tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'REVERSO ' . $glb['GlobalDato']['concepto_1'];// 'Or.Dto.: ' . $aReverso[$nIndice]['OrdenDescuentoCuota']['orden_descuento_id'] . ' Cta. ' . $aReverso[$nIndice]['OrdenDescuentoCuota']['nro_cuota'];
                $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
                $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
                if(empty($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
                    $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                    $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO -  ' . $aReverso['OrdenDescuentoCuota']['tipo_orden_dto'] . ' - ' . $aReverso['OrdenDescuentoCuota']['tipo_producto'] . ' - ' . $aReverso['OrdenDescuentoCuota']['tipo_cuota']; 
                endif;
            endif;

            $cuenta = $this->getCuenta($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
            $tmpAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
            $tmpAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
            $tmpAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
            $tmpAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;

            return $tmpAsiento;
        }
        
        
	function getAsientoReversoOrgLiq($organismo, $total_organismo, $temporalAsiento, $fecha, $procesoId, $nLiquidacion=0, $agrupar=0){
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$oTipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$cModulo = 'LIQUREVE';
				
		$glb = $this->getGlobalDato('concepto_1',$organismo);
		$cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'ORGAN', 'MutualCuentaAsiento.tipo_producto' => $organismo)));
			
		$asientoOrganismo = array();
		$asientoOrganismo['MutualTemporalAsientoRenglon']['fecha'] = $fecha;
		$asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
		$asientoOrganismo['MutualTemporalAsientoRenglon']['importe'] = $total_organismo  * (-1);
		$asientoOrganismo['MutualTemporalAsientoRenglon']['referencia'] = 'REVERSO ' . $glb['GlobalDato']['concepto_1'] . ' LIQUIDACION # ' . $nLiquidacion;
		$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 0;
		$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
						
		if(empty($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
			$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 1;
			$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
		endif;

		$cuenta = $this->getCuenta($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
		$asientoOrganismo['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
		$asientoOrganismo['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
		$asientoOrganismo['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
		$asientoOrganismo['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
		
		$oMutualTemporalAsientoRenglon->id = 0;
		$oMutualTemporalAsientoRenglon->saveAll($temporalAsiento);
			
		$oMutualTemporalAsientoRenglon->id = 0;
		$oMutualTemporalAsientoRenglon->save($asientoOrganismo);
			
		if($agrupar == 0):

                        $queryImporte = "SELECT SUM(importe) as importe 
                                                        FROM mutual_temporal_asiento_renglones
                                                        WHERE mutual_asiento_id = 0
                                                        GROUP BY mutual_asiento_id, co_plan_cuenta_id";
                        $importeAsiento = $this->query($queryImporte);

                        $debe = 0;
                        $haber = 0;
                        foreach($importeAsiento as $importe):
                                if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
                                else  $haber += $importe[0]['importe'];
                        endforeach;
			
			$aMutualAsiento = array();
			$aMutualAsiento['MutualAsiento']['id'] = 0;
			$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
			$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
			$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
			$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
			$aMutualAsiento['MutualAsiento']['fecha'] = $fecha;
			$aMutualAsiento['MutualAsiento']['tipo_documento'] = 'LIQ';
			$aMutualAsiento['MutualAsiento']['nro_documento'] = $nLiquidacion;
//			$aMutualAsiento['MutualAsiento']['tipo_documento'] = '';
//			$aMutualAsiento['MutualAsiento']['nro_documento'] = 'ORDEN DESCUENTO COBRO';
			$aMutualAsiento['MutualAsiento']['referencia'] = 'REVERSO ' . $glb['GlobalDato']['concepto_1'];
			$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
			$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
			$aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
			$aMutualAsiento['MutualAsiento']['tipo_asiento'] = 0;
			
			$this->grabarAsiento($aMutualAsiento, $procesoId);			
		endif;
		
		return true;
	}
	
	
	function getAsientoReversoRenglon($aReverso){
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$oTipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
		$cModulo = 'LIQUREVE';
		
                /*
                 * Verifico que las cuotas sean de los Comercio para imputarla a la cuenta correspondiente
                 * 08/09/2017
                 * Tiene que ir con la Variable MUTALPROVEEDORID
                 */
                if($aReverso['OrdenDescuentoCuota']['proveedor_id'] != 18){
                    $conditions = array('MutualCuentaAsiento.tipo_orden_dto' => 'COMER', 
                                        'MutualCuentaAsiento.tipo_producto' => 'PROVEEDOR');
                }else{
                    $conditions = array('MutualCuentaAsiento.tipo_orden_dto' => $aReverso['OrdenDescuentoCuota']['tipo_orden_dto'], 
                                        'MutualCuentaAsiento.tipo_producto' => $aReverso['OrdenDescuentoCuota']['tipo_producto'], 
                                        'MutualCuentaAsiento.tipo_cuota' => $aReverso['OrdenDescuentoCuota']['tipo_cuota']);
                }		
		$cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => $conditions));
		$glb = $this->getGlobalDato('concepto_1',$aReverso['PersonaBeneficio']['codigo_beneficio']);
		$tmpAsiento = array();
		$tmpAsiento['MutualTemporalAsientoRenglon']['fecha'] = $aReverso['BancoCuentaMovimiento']['fecha_reverso'];
		if($cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'] == 0 && $cuentaAsiento[0]['MutualCuentaAsiento']['mutual_tipo_asiento_id'] == 0):
                    $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = '';
                    $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aReverso[0]['importe_reversado'];
                    $tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'REVERSO ' . $glb['GlobalDato']['concepto_1']; //'Or.Dto.: ' . $aReverso[$nIndice]['OrdenDescuentoCuota']['orden_descuento_id'] . 'Cta. ' . $aReverso[$nIndice]['OrdenDescuentoCuota']['nro_cuota'];
                    $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                    $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO -  ' . $aReverso['OrdenDescuentoCuota']['tipo_orden_dto'] . ' - ' . $aReverso['OrdenDescuentoCuota']['tipo_producto'] . ' - ' . $aReverso['OrdenDescuentoCuota']['tipo_cuota']; 
		else:
                    if(!empty($cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'])):
                        $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
                        $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aReverso[0]['importe_reversado'];
                    else:
                        $tipoAsiento = $oTipoAsientoRenglon->find('all', array('conditions' => array('MutualTipoAsientoRenglon.mutual_tipo_asiento_id' => $cuentaAsiento[0]['MutualCuentaAsiento']['mutual_tipo_asiento_id'], 'MutualTipoAsientoRenglon.variable' => 'TOTAL')));
                        if(empty($tipoAsiento)):
                            $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
                            $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aReverso[0]['importe_reversado'];
                        else:
                            $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $tipoAsiento[0]['MutualTipoAsientoRenglon']['co_plan_cuenta_id'];
                            $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = ($tipoAsiento[0]['MutualTipoAsientoRenglon']['debe_haber'] == 'H' ? $aReverso[0]['importe_reversado'] * (-1) : $aReverso[0]['importe_reversado']);
                        endif;
                    endif;
                    $tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'REVERSO ' . $glb['GlobalDato']['concepto_1'];// 'Or.Dto.: ' . $aReverso[$nIndice]['OrdenDescuentoCuota']['orden_descuento_id'] . ' Cta. ' . $aReverso[$nIndice]['OrdenDescuentoCuota']['nro_cuota'];
                    $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
                    $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
                    if(empty($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
                        $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                        $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO -  ' . $aReverso['OrdenDescuentoCuota']['tipo_orden_dto'] . ' - ' . $aReverso['OrdenDescuentoCuota']['tipo_producto'] . ' - ' . $aReverso['OrdenDescuentoCuota']['tipo_cuota']; 
                    endif;
		endif;

		$cuenta = $this->getCuenta($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
		$tmpAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
		$tmpAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
		$tmpAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
		$tmpAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
		
		return $tmpAsiento;
	}
	
	
	function getAsientoReversoOrganismo($organismo, $total_organismo, $temporalAsiento, $fecha, $procesoId, $agrupar=0){
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$oTipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$cModulo = 'REVELIQU';
				
		$glb = $this->getGlobalDato('concepto_1',$organismo);
		$cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'ORGAN', 'MutualCuentaAsiento.tipo_producto' => $organismo)));
			
		$asientoOrganismo = array();
		$asientoOrganismo['MutualTemporalAsientoRenglon']['fecha'] = $fecha;
		$asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
		$asientoOrganismo['MutualTemporalAsientoRenglon']['importe'] = $total_organismo  * (-1);
		$asientoOrganismo['MutualTemporalAsientoRenglon']['referencia'] = 'REVERSO ' . $glb['GlobalDato']['concepto_1'];
		$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 0;
		$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
						
		if(empty($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
			$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 1;
			$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
		endif;

		$cuenta = $this->getCuenta($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
		$asientoOrganismo['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
		$asientoOrganismo['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
		$asientoOrganismo['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
		$asientoOrganismo['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
		
		$oMutualTemporalAsientoRenglon->id = 0;
		$oMutualTemporalAsientoRenglon->saveAll($temporalAsiento);
			
		$oMutualTemporalAsientoRenglon->id = 0;
		$oMutualTemporalAsientoRenglon->save($asientoOrganismo);
			
		if($agrupar == 0):

				$queryImporte = "SELECT SUM(importe) as importe 
								FROM mutual_temporal_asiento_renglones
								WHERE mutual_asiento_id = 0
								GROUP BY mutual_asiento_id, co_plan_cuenta_id";
				$importeAsiento = $this->query($queryImporte);
					
				$debe = 0;
				$haber = 0;
				foreach($importeAsiento as $importe):
					if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
					else  $haber += $importe[0]['importe'];
				endforeach;
			
			$aMutualAsiento = array();
			$aMutualAsiento['MutualAsiento']['id'] = 0;
			$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
			$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
			$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
			$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
			$aMutualAsiento['MutualAsiento']['fecha'] = $fecha;
			$aMutualAsiento['MutualAsiento']['tipo_documento'] = '';
			$aMutualAsiento['MutualAsiento']['nro_documento'] = 'ORDEN DESCUENTO COBRO';
			$aMutualAsiento['MutualAsiento']['referencia'] = 'REVERSO ' . $glb['GlobalDato']['concepto_1'];
			$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
			$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
			$aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
			$aMutualAsiento['MutualAsiento']['tipo_asiento'] = 0;
			
			$this->grabarAsiento($aMutualAsiento, $procesoId);			
		endif;
		
		return true;
	}
	
	
	function getAsientoReversoReintegro($aReverso, $procesoId, $agrupar=0){
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$oTipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$cModulo = 'REVEREIN';

			$cuentaReintegro = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'SOCIO','MutualCuentaAsiento.tipo_producto' => 'REINTEGRO')));
			$glb = $this->getGlobalDato('concepto_1',$aReverso['PersonaBeneficio']['codigo_beneficio']);
			$temporalAsiento = array();
			$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $aReverso['BancoCuentaMovimiento']['fecha_reverso'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $aReverso[0]['importe_reversado'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'REVERSO REINTEGRO ' . $glb['GlobalDato']['concepto_1'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
	
			if(empty($cuentaReintegro)):
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA REINTEGRO NO EXITE'; 
	
			else:
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaReintegro[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
						
				if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
					$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA REINTEGRO NO CONFIGURADA'; 
				endif;
			endif;
				
			$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
			$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
			
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($temporalAsiento);

			$cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'ORGAN', 'MutualCuentaAsiento.tipo_producto' => $aReverso['PersonaBeneficio']['codigo_beneficio'])));
			
			$asientoOrganismo = array();
			$asientoOrganismo['MutualTemporalAsientoRenglon']['fecha'] = $aReverso['BancoCuentaMovimiento']['fecha_reverso'];
			$asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
			$asientoOrganismo['MutualTemporalAsientoRenglon']['importe'] = $aReverso[0]['importe_reversado']  * (-1);
			$asientoOrganismo['MutualTemporalAsientoRenglon']['referencia'] = 'REVERSO REINTEGRO ' . $glb['GlobalDato']['concepto_1'];
			$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 0;
			$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
						
			if(empty($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
				$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 1;
				$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
			endif;

			$cuenta = $this->getCuenta($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$asientoOrganismo['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$asientoOrganismo['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
			$asientoOrganismo['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
			$asientoOrganismo['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
			
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($asientoOrganismo);
			
			if($agrupar == 0):

				$queryImporte = "SELECT SUM(importe) as importe 
								FROM mutual_temporal_asiento_renglones
								WHERE mutual_asiento_id = 0
								GROUP BY mutual_asiento_id, co_plan_cuenta_id";
				$importeAsiento = $this->query($queryImporte);
					
				$debe = 0;
				$haber = 0;
				foreach($importeAsiento as $importe):
					if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
					else  $haber += $importe[0]['importe'];
				endforeach;
				
				$aMutualAsiento = array();
				$aMutualAsiento['MutualAsiento']['id'] = 0;
				$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
				$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
				$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
				$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
				$aMutualAsiento['MutualAsiento']['fecha'] = $aReverso['BancoCuentaMovimiento']['fecha_reverso'];
				$aMutualAsiento['MutualAsiento']['tipo_documento'] = '';
				$aMutualAsiento['MutualAsiento']['nro_documento'] = '';
				$aMutualAsiento['MutualAsiento']['referencia'] = 'REVERSO DEL REINTEGRO ' . $glb['GlobalDato']['concepto_1'];
				$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
				$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
				$aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
				$aMutualAsiento['MutualAsiento']['tipo_asiento'] = 0;
				
				$this->grabarAsiento($aMutualAsiento, $procesoId);			
			endif;
						

	}
	
	
	function getAsientoReversoBanco($aReversoBanco, $procesoId, $agrupar=0){
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$oTipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$cModulo = 'REVEBANC';		

		$nIndice = 0;
		$renglones = count($aReversoBanco);
		while ($nIndice < $renglones):
			$bancoId = $aReversoBanco[$nIndice][0]['banco_cuenta_movimiento_id'];
			$fecha_corte = $aReversoBanco[$nIndice][0]['fecha_reverso'];
			$co_plan_cuenta_id = $aReversoBanco[$nIndice][0]['co_plan_cuenta_id'];
			$total_banco = 0;
			$temporalAsiento = array();

			
			while ($bancoId == $aReversoBanco[$nIndice][0]['banco_cuenta_movimiento_id'] && $nIndice < $renglones):
				
				$total_banco += $aReversoBanco[$nIndice][0]['importe_reversado'];
				
				$glb = $this->getGlobalDato('concepto_1',$aReversoBanco[$nIndice][0]['codigo_beneficio']);
				$cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'ORGAN', 'MutualCuentaAsiento.tipo_producto' => $aReversoBanco[$nIndice][0]['codigo_beneficio'])));
				
				$asientoOrganismo = array();
				$asientoOrganismo['MutualTemporalAsientoRenglon']['fecha'] = $fecha_corte;
				$asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
				$asientoOrganismo['MutualTemporalAsientoRenglon']['importe'] = $aReversoBanco[$nIndice][0]['importe_reversado'];
				$asientoOrganismo['MutualTemporalAsientoRenglon']['referencia'] = $glb['GlobalDato']['concepto_1'];
				$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 0;
				$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
							
				if(empty($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
					$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 1;
					$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
				endif;
	
				$cuenta = $this->getCuenta($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
				$asientoOrganismo['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
				$asientoOrganismo['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
				$asientoOrganismo['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
				$asientoOrganismo['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
				
				$oMutualTemporalAsientoRenglon->id = 0;
				$oMutualTemporalAsientoRenglon->save($asientoOrganismo);
			
				$nIndice += 1;			
			endwhile;

			$temporalAsiento = array();
			$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $fecha_corte;
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $co_plan_cuenta_id;
			$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $total_banco * (-1);
			$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'REVERSO CAJA/BANCO';
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
	
			if(empty($co_plan_cuenta_id)):
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA CAJA/BANCO NO CONFIGURADA'; 
			endif;
				
			$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
			$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
			
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($temporalAsiento);

			
			if($agrupar == 0):

				$queryImporte = "SELECT SUM(importe) as importe 
								FROM mutual_temporal_asiento_renglones
								WHERE mutual_asiento_id = 0
								GROUP BY mutual_asiento_id, co_plan_cuenta_id";
				$importeAsiento = $this->query($queryImporte);
					
				$debe = 0;
				$haber = 0;
				foreach($importeAsiento as $importe):
					if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
					else  $haber += $importe[0]['importe'];
				endforeach;
				
				$aMutualAsiento = array();
				$aMutualAsiento['MutualAsiento']['id'] = 0;
				$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
				$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
				$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
				$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
				$aMutualAsiento['MutualAsiento']['fecha'] = $fecha_corte;
				$aMutualAsiento['MutualAsiento']['tipo_documento'] = '';
				$aMutualAsiento['MutualAsiento']['nro_documento'] = '';
				$aMutualAsiento['MutualAsiento']['referencia'] = 'REVERSO CAJA/BANCO ' . $glb['GlobalDato']['concepto_1'];
				$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
				$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
				$aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
				$aMutualAsiento['MutualAsiento']['tipo_asiento'] = 0;
				
				$this->grabarAsiento($aMutualAsiento, $procesoId);			
			endif;
						
		endwhile;
		
	}
	

	function getAsientoRevBcoOrganismo($aReversoBanco){
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$oTipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$cModulo = 'REVEBANC';
		
		$glb = $this->getGlobalDato('concepto_1',$aReversoBanco[0]['codigo_beneficio']);
		$cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'ORGAN', 'MutualCuentaAsiento.tipo_producto' => $aReversoBanco[0]['codigo_beneficio'])));
				
		$asientoOrganismo = array();
		$asientoOrganismo['MutualTemporalAsientoRenglon']['fecha'] = $aReversoBanco[0]['fecha_reverso'];
		$asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
		$asientoOrganismo['MutualTemporalAsientoRenglon']['importe'] = $aReversoBanco[0]['importe_reversado'];
		$asientoOrganismo['MutualTemporalAsientoRenglon']['referencia'] = $aReversoBanco['Banco']['nombre'] . ' - ' . $glb['GlobalDato']['concepto_1'];
		$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 0;
		$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
							
		if(empty($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
			$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 1;
			$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
		endif;
	
		$cuenta = $this->getCuenta($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
		$asientoOrganismo['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
		$asientoOrganismo['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
		$asientoOrganismo['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
		$asientoOrganismo['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
		
		$oMutualTemporalAsientoRenglon->id = 0;
		$oMutualTemporalAsientoRenglon->save($asientoOrganismo);
		
		return $glb;
		
	}
	
	
	function getAsientoRevBcoRenglon($co_plan_cuenta_id, $total_banco, $fecha, $glb, $procesoId, $agrupar=0){
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$oTipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$cModulo = 'REVEBANC';
		
		$temporalAsiento = array();
		$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $fecha;
		$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $co_plan_cuenta_id;
		$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $total_banco * (-1);
		$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'REVERSO CAJA/BANCO ' . $glb['GlobalDato']['concepto_1'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
		$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
	
		if(empty($co_plan_cuenta_id)):
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA CAJA/BANCO NO CONFIGURADA'; 
		endif;
				
		$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
		$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
		$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
		$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
		
		$oMutualTemporalAsientoRenglon->id = 0;
		$oMutualTemporalAsientoRenglon->save($temporalAsiento);

			
		if($agrupar == 0):

				$queryImporte = "SELECT SUM(importe) as importe 
								FROM mutual_temporal_asiento_renglones
								WHERE mutual_asiento_id = 0
								GROUP BY mutual_asiento_id, co_plan_cuenta_id";
				$importeAsiento = $this->query($queryImporte);
					
				$debe = 0;
				$haber = 0;
				foreach($importeAsiento as $importe):
					if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
					else  $haber += $importe[0]['importe'];
				endforeach;
			
			$aMutualAsiento = array();
			$aMutualAsiento['MutualAsiento']['id'] = 0;
			$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
			$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
			$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
			$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
			$aMutualAsiento['MutualAsiento']['fecha'] = $fecha;
			$aMutualAsiento['MutualAsiento']['tipo_documento'] = '';
			$aMutualAsiento['MutualAsiento']['nro_documento'] = '';
			$aMutualAsiento['MutualAsiento']['referencia'] = 'REVERSO CAJA/BANCO ' . $glb['GlobalDato']['concepto_1'];
			$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
			$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
			$aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
			$aMutualAsiento['MutualAsiento']['tipo_asiento'] = 0;
			
			$this->grabarAsiento($aMutualAsiento, $procesoId);			
		endif;			
			
		return true;
	}
	
	
// 
	/*
	 * Proceso Asiento Orden de Caja Cobro
	 * @param $fecha_desde
	 * @param $fecha_hasta
	 */
	function getOrdenCajaCobro($fecha_desde, $fecha_hasta){
/*
 * ESTAS ORDENES NO TIENEN RECIBO DE INGRESO HAY QUE VERIFICAR.
373428
376060
386314
386315 
 */		

		$sql = "SELECT 	OrdenDescuentoCobro.id, IFNULL(ProveedorFactura.total_comprobante,0.00) AS importe_comercio, OrdenDescuentoCobro.fecha, OrdenDescuentoCobro.recibo_id, OrdenDescuentoCobro.importe, OrdenDescuentoCobro.proveedor_origen_fondo_id, 
						OrdenCajaCobro.orden_pago_id, OrdenCajaCobro.banco_cuenta_movimiento_id, OrdenCajaCobro.proveedor_factura_id, 
						OrdenCajaCobro.importe_contado, OrdenCajaCobro.importe_orden_pago
				FROM 	orden_descuento_cobros OrdenDescuentoCobro
				INNER 	JOIN orden_caja_cobros OrdenCajaCobro
				ON 		OrdenCajaCobro.orden_descuento_cobro_id = OrdenDescuentoCobro.id
				LEFT	JOIN proveedor_facturas ProveedorFactura
				ON		ProveedorFactura.id = OrdenCajaCobro.proveedor_factura_id
				WHERE	OrdenDescuentoCobro.cancelacion_orden_id = 0 AND OrdenDescuentoCobro.fecha > '$fecha_desde' AND 
						OrdenDescuentoCobro.fecha <= '$fecha_hasta' AND OrdenDescuentoCobro.anulado = 0
			";

		
		
		return $this->query($sql);
	}
	
	
	function getCajaCobroSocio($ordenDescuento){
		

		$sql = "SELECT 	PersonaBeneficio.codigo_beneficio, OrdenDescuentoCuota.orden_descuento_id, OrdenDescuentoCuota.tipo_orden_dto, 
						OrdenDescuentoCuota.tipo_producto, OrdenDescuentoCuota.tipo_cuota, OrdenDescuentoCuota.proveedor_id, OrdenDescuentoCuota.nro_cuota, 
						SUM(OrdenDescuentoCobroCuota.importe) AS importe_cobrado, MutualCuentaAsiento.mutual_tipo_asiento_id, MutualCuentaAsiento.co_plan_cuenta_id
				FROM	orden_descuento_cuotas OrdenDescuentoCuota
				INNER 	JOIN 
						orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
				ON 		OrdenDescuentoCobroCuota.orden_descuento_cuota_id = OrdenDescuentoCuota.id
				INNER 	JOIN 
						mutual_cuenta_asientos MutualCuentaAsiento
				ON 		CONCAT(MutualCuentaAsiento.tipo_orden_dto, MutualCuentaAsiento.tipo_producto, MutualCuentaAsiento.tipo_cuota) = 
				   		CONCAT(OrdenDescuentoCuota.tipo_orden_dto, OrdenDescuentoCuota.tipo_producto, OrdenDescuentoCuota.tipo_cuota)
				INNER 	JOIN 
						persona_beneficios PersonaBeneficio
				ON 		PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id
				WHERE 	OrdenDescuentoCobroCuota.orden_descuento_cobro_id = '$ordenDescuento'
				GROUP 	BY 
						PersonaBeneficio.codigo_beneficio, OrdenDescuentoCuota.tipo_orden_dto, OrdenDescuentoCuota.tipo_producto, OrdenDescuentoCuota.tipo_cuota, 
					 	OrdenDescuentoCuota.proveedor_id
			";

		
		
		return $this->query($sql);
	}
	
	
	function getAsientoCajaCobro($aCobro, $procesoId, $agrupar=0){
            $oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
            $oTipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
            $oBancoCuentaMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
            $oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
            $oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
            $oProveedorFactura = $this->importarModelo('ProveedorFactura', 'proveedores');
            $cModulo = 'COBRCAJA';


            // BUCLE DE LA ORDEN DE COBRO
            // TIENE QUE IR CON LA VARIABLE "MUTUALPROVEEDORID"
            if($aCobro[0]['importe_comercio'] == 0.00 && $aCobro['OrdenDescuentoCobro']['recibo_id'] == 0 && $aCobro['OrdenDescuentoCobro']['proveedor_origen_fondo_id'] != 0 && $aCobro['OrdenDescuentoCobro']['proveedor_origen_fondo_id'] != 18){}
            else{
                $aPagoContado = array();
                $importeContado = $aCobro['OrdenCajaCobro']['importe_contado'];

                if($aCobro['OrdenCajaCobro']['banco_cuenta_movimiento_id'] > 0){ $aPagoContado = $oBancoCuentaMovimiento->find('all', array('conditions' => array('BancoCuentaMovimiento.id' => $aCobro['OrdenCajaCobro']['banco_cuenta_movimiento_id'])));}

                $temporalAsiento = array();
                $aOrdenCajas = $this->getCajaCobroSocio($aCobro['OrdenDescuentoCobro']['id']);

                if($aCobro['OrdenCajaCobro']['banco_cuenta_movimiento_id'] > 0){
                    $tmpAsiento = array();
                    $tmpAsiento['MutualTemporalAsientoRenglon']['fecha'] = $aCobro['OrdenDescuentoCobro']['fecha'];
                    if(empty($aPagoContado)){
                        $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
                        $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $importeContado * (-1);
                        $tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = $aPagoContado[0]['BancoCuentaMovimiento']['descripcion'];
                        $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                        $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO ASENTADO EN CAJA/BANCO'; 
                    }
                    else{
                        $aBancoCuenta = $oBancoCuenta->find('all', array('conditions' => array('BancoCuenta.id' => $aPagoContado[0]['BancoCuentaMovimiento']['banco_cuenta_id'])));

                        $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $aBancoCuenta[0]['BancoCuenta']['co_plan_cuenta_id'];
                        $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $importeContado * (-1);
                        $tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = $aPagoContado[0]['BancoCuentaMovimiento']['descripcion'];
                        $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
                        $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
                        if(empty($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])){
                            $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                            $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA NO DEFINIDA EN CAJA/BANCO'; 
                        }
                    }

                    $cuenta = $this->getCuenta($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
                    $tmpAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
                    $tmpAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
                    $tmpAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
                    $tmpAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;

                    array_push($temporalAsiento, $tmpAsiento);
                }

                // EMPIEZA EL BUCLE DE LAS CUOTAS
                $coPlanCuentaId = 0; // Aca pongo la cuenta del comercio que recaudo la orden.
                foreach($aOrdenCajas as $aCaja){

                    $importeCobrado = $aCaja[0]['importe_cobrado'];
                    if($aCobro['OrdenCajaCobro']['banco_cuenta_movimiento_id'] > 0){
                        if($aCobro['OrdenDescuentoCobro']['proveedor_origen_fondo_id'] == $aCaja['OrdenDescuentoCuota']['proveedor_id']){
                            $importeCobrado = ($importeContado > $importeCobrado ? 0.00 : $importeCobrado - $importeContado);
                            $importeContado = ($importeContado > $importeCobrado ? $importeContado - $importeCobrado : 0.00);
                        }
                    }

                    // Si posee una Nota de Credito es por que lo recaudo el comercio y debe anularse la cuenta de comercio, no participa el organismo
                    // ya que no hay recibo de ingreso.
                    if($aCobro['OrdenCajaCobro']['proveedor_factura_id'] > 0){
                        // Busco la cuenta del comercio.
                        if($aCaja['OrdenDescuentoCuota']['proveedor_id'] == $aCobro['OrdenDescuentoCobro']['proveedor_origen_fondo_id']){
                            $coPlanCuentaId = $aCaja['MutualCuentaAsiento']['co_plan_cuenta_id'];
                        }
                    }
                    else{
                        $glb = $this->getGlobalDato('concepto_1',$aCaja['PersonaBeneficio']['codigo_beneficio']);
                        $cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'ORGAN', 'MutualCuentaAsiento.tipo_producto' => $aCaja['PersonaBeneficio']['codigo_beneficio'])));

                        $asientoOrganismo = array();
                        $asientoOrganismo['MutualTemporalAsientoRenglon']['fecha'] = $aCobro['OrdenDescuentoCobro']['fecha'];
                        $asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
                        $asientoOrganismo['MutualTemporalAsientoRenglon']['importe'] = $aCaja[0]['importe_cobrado'];
                        $asientoOrganismo['MutualTemporalAsientoRenglon']['referencia'] = $glb['GlobalDato']['concepto_1'];
                        $asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 0;
                        $asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 

                        if(empty($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])){
                            $asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 1;
                            $asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
                        }

                        $cuenta = $this->getCuenta($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
                        $asientoOrganismo['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
                        $asientoOrganismo['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
                        $asientoOrganismo['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
                        $asientoOrganismo['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;

                        $oMutualTemporalAsientoRenglon->id = 0;
                        $oMutualTemporalAsientoRenglon->save($asientoOrganismo);

                    }

                    if($importeCobrado > 0){
                        $tmpAsiento = array();
                        $tmpAsiento['MutualTemporalAsientoRenglon']['fecha'] = $aCobro['OrdenDescuentoCobro']['fecha'];
                        /*
                         * Verifico que las cuotas sean de los Comercio para imputarla a la cuenta correspondiente
                         * 08/09/2017
                         * Tiene que ir con la Variable MUTALPROVEEDORID
                         */
                        if($aCaja['OrdenDescuentoCuota']['proveedor_id'] != 18){
                            $cuentaProveedor = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'COMER', 'MutualCuentaAsiento.tipo_producto' => 'PROVEEDOR')));
                            $aCaja['MutualCuentaAsiento']['co_plan_cuenta_id'] = $cuentaProveedor[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
                            $aCaja['MutualCuentaAsiento']['mutual_tipo_asiento_id'] = 0;
                        }
                        if($aCaja['MutualCuentaAsiento']['co_plan_cuenta_id'] == 0 && $aCaja['MutualCuentaAsiento']['mutual_tipo_asiento_id'] == 0){
                            $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
                            $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $importeCobrado * (-1);
                            $tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'Or.Dto.: ' . $aCaja['OrdenDescuentoCuota']['orden_descuento_id'] . 'Cta. ' . $aCaja['OrdenDescuentoCuota']['nro_cuota'];
                            $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                            $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
                        }
                        else{
                            if(!empty($aCaja['MutualCuentaAsiento']['co_plan_cuenta_id'])){
                                $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $aCaja['MutualCuentaAsiento']['co_plan_cuenta_id'];
                                $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $importeCobrado * (-1);
                            }
                            else{
                                $tipoAsiento = $oTipoAsientoRenglon->find('all', array('conditions' => array('MutualTipoAsientoRenglon.mutual_tipo_asiento_id' => $aCaja['MutualCuentaAsiento']['mutual_tipo_asiento_id'], 'MutualTipoAsientoRenglon.variable' => 'TOTAL')));

                                if(empty($tipoAsiento)){
                                    $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
                                    $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $importeCobrado * (-1);
                                }
                                else{
                                    $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $tipoAsiento[0]['MutualTipoAsientoRenglon']['co_plan_cuenta_id'];
                                    $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = ($tipoAsiento[0]['MutualTipoAsientoRenglon']['debe_haber'] == 'D' ? $importeCobrado * (-1) : $importeCobrado);
                                }
                            }
                            $tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'Or.Dto.: ' . $aCaja['OrdenDescuentoCuota']['orden_descuento_id'] . ' Cta. ' . $aCaja['OrdenDescuentoCuota']['nro_cuota'];
                            $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
                            $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
                            if(empty($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])){
                                $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                                $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
                            }

                        }

                        $cuenta = $this->getCuenta($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
                        $tmpAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
                        $tmpAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
                        $tmpAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
                        $tmpAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;


                        array_push($temporalAsiento, $tmpAsiento);
                    }
                }

                $oMutualTemporalAsientoRenglon->id = 0;
                $oMutualTemporalAsientoRenglon->saveAll($temporalAsiento);

                $referencia = '';
                if($aCobro['OrdenCajaCobro']['proveedor_factura_id'] > 0){

                    if($coPlanCuentaId == 0){
                        $cuentaProveedor = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'COMER', 'MutualCuentaAsiento.tipo_producto' => 'PROVEEDOR')));
                        $coPlanCuentaId = $cuentaProveedor[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
                    }

                    $asientoComercio = array();
                    $asientoComercio['MutualTemporalAsientoRenglon']['fecha'] = $aCobro['OrdenDescuentoCobro']['fecha'];
                    $asientoComercio['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $coPlanCuentaId;
                    $asientoComercio['MutualTemporalAsientoRenglon']['importe'] = $aCobro['OrdenDescuentoCobro']['importe'];
                    $asientoComercio['MutualTemporalAsientoRenglon']['referencia'] = 'COBRADO EN COMERCIO';
                    $asientoComercio['MutualTemporalAsientoRenglon']['error'] = 0;
                    $asientoComercio['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 

                    if(empty($asientoComercio['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])){
                        $asientoComercio['MutualTemporalAsientoRenglon']['error'] = 1;
                        $asientoComercio['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
                    }

                    $cuenta = $this->getCuenta($asientoComercio['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
                    $asientoComercio['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
                    $asientoComercio['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
                    $asientoComercio['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
                    $asientoComercio['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;

                    $oMutualTemporalAsientoRenglon->id = 0;
                    $oMutualTemporalAsientoRenglon->save($asientoComercio);

                    $referencia = ' ** COBRADO EN COMERCIO **';

                }


                $queryImporte = "SELECT SUM(importe) as importe 
                                    FROM mutual_temporal_asiento_renglones
                                    WHERE mutual_asiento_id = 0
                                    GROUP BY mutual_asiento_id, co_plan_cuenta_id";
                $importeAsiento = $this->query($queryImporte);

                $debe = 0;
                $haber = 0;
                foreach($importeAsiento as $importe){
                    if($importe[0]['importe'] > 0){ $debe += $importe[0]['importe'];}
                    else{  $haber += $importe[0]['importe'];}
                }

                if($debe == 0 && $haber == 0){
                    $queryImporte = "DELETE 
                                        FROM mutual_temporal_asiento_renglones
                                        WHERE mutual_asiento_id = 0";
                    $importeAsiento = $this->query($queryImporte);
                }
                else{

                    if($agrupar == 0){
                        $aMutualAsiento = array();
                        $aMutualAsiento['MutualAsiento']['id'] = 0;
                        $aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
                        $aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
                        $aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
                        $aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
                        $aMutualAsiento['MutualAsiento']['fecha'] = $aCobro['OrdenDescuentoCobro']['fecha'];
                        $aMutualAsiento['MutualAsiento']['tipo_documento'] = '';
                        $aMutualAsiento['MutualAsiento']['nro_documento'] = $aCobro['OrdenDescuentoCobro']['id'];
                        $aMutualAsiento['MutualAsiento']['referencia'] = 'ORDEN CAJA COBRO' . $referencia;
                        $aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
                        $aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
                        $aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
                        $aMutualAsiento['MutualAsiento']['tipo_asiento'] = 0;

                        $this->grabarAsiento($aMutualAsiento, $procesoId);			
                    }
                }
            }			
		
		
	}
	

	/*
	 * Proceso Asiento de Cancelaciones
	 * @param $fecha_desde
	 * @param $fecha_hasta
	 */
	function getCancelacionRecibo($fecha_desde, $fecha_hasta){
		
            
		// TIENE QUE IR CON LA VARIABLE MUTUALPROVEEDORID
		$sql = "SELECT	OrdenDescuentoCobro.fecha, CancelacionOrden.id, CancelacionOrden.recibo_id, CancelacionOrden.orden_descuento_cobro_id, CancelacionOrden.importe_proveedor
				FROM	cancelacion_ordenes CancelacionOrden
				INNER	JOIN proveedor_facturas ProveedorFactura
				ON		ProveedorFactura.cancelacion_orden_id = CancelacionOrden.id
				INNER	JOIN orden_descuento_cobros OrdenDescuentoCobro
				ON		CancelacionOrden.orden_descuento_cobro_id = OrdenDescuentoCobro.id
				WHERE	CancelacionOrden.estado = 'P' AND CancelacionOrden.recibo_id > 0 AND CancelacionOrden.importe_proveedor > 0 AND 
						OrdenDescuentoCobro.fecha > '$fecha_desde' AND OrdenDescuentoCobro.fecha <= '$fecha_hasta'
				UNION
						(
				SELECT	OrdenDescuentoCobro.fecha, CancelacionOrden.id, CancelacionOrden.recibo_id, CancelacionOrden.orden_descuento_cobro_id, CancelacionOrden.importe_proveedor
				FROM	cancelacion_ordenes CancelacionOrden
				INNER	JOIN orden_descuento_cobros OrdenDescuentoCobro
				ON		CancelacionOrden.orden_descuento_cobro_id = OrdenDescuentoCobro.id
				WHERE	CancelacionOrden.estado = 'P' AND CancelacionOrden.recibo_id > 0 AND CancelacionOrden.importe_proveedor > 0 AND 
						CancelacionOrden.orden_proveedor_id = '18' AND
						OrdenDescuentoCobro.fecha > '$fecha_desde' AND OrdenDescuentoCobro.fecha <= '$fecha_hasta')
				ORDER	BY fecha
			";

		
		
		return $this->query($sql);
	}
	

	/*
	 * Proceso Asiento de Cancelaciones
	 * @param $fecha_desde
	 * @param $fecha_hasta
	 */
	function getCancelaciones($fecha_desde, $fecha_hasta){
		
                // TIENE QUE IR CON LA VARIABLE MUTUALPROVEEDORID
		$sql = "SELECT	CancelacionOrden.id, OrdenDescuentoCobro.fecha, CancelacionOrden.id, CancelacionOrden.recibo_id, CancelacionOrden.orden_descuento_cobro_id, 
						CancelacionOrden.credito_proveedor_factura_id AS credito, CancelacionOrden.orden_proveedor_id, 
						CancelacionOrden.importe_proveedor
				FROM	cancelacion_ordenes CancelacionOrden
				INNER	JOIN proveedor_facturas ProveedorFactura
				ON		ProveedorFactura.cancelacion_orden_id = CancelacionOrden.id
				INNER	JOIN orden_descuento_cobros OrdenDescuentoCobro
				ON		CancelacionOrden.orden_descuento_cobro_id = OrdenDescuentoCobro.id
				WHERE	OrdenDescuentoCobro.fecha > '$fecha_desde' AND OrdenDescuentoCobro.fecha <= '$fecha_hasta' AND CancelacionOrden.estado = 'P' AND CancelacionOrden.importe_proveedor > 0 AND CancelacionOrden.recibo_id = 0 
				
				UNION
				(
				SELECT	CancelacionOrden.id, OrdenDescuentoCobro.fecha, CancelacionOrden.id, CancelacionOrden.recibo_id, CancelacionOrden.orden_descuento_cobro_id, 
						CancelacionOrden.credito_proveedor_factura_id AS credito, CancelacionOrden.orden_proveedor_id, 
						CancelacionOrden.importe_proveedor
				FROM	cancelacion_ordenes CancelacionOrden
				INNER	JOIN orden_descuento_cobros OrdenDescuentoCobro
				ON		CancelacionOrden.orden_descuento_cobro_id = OrdenDescuentoCobro.id
				WHERE	OrdenDescuentoCobro.fecha > '$fecha_desde' AND OrdenDescuentoCobro.fecha <= '$fecha_hasta' AND 
						CancelacionOrden.estado = 'P' AND CancelacionOrden.importe_proveedor > 0 AND CancelacionOrden.recibo_id = 0 AND 
						CancelacionOrden.orden_proveedor_id = '18'
				)
				ORDER	BY fecha, credito
		";

		
		return $this->query($sql);
	}
	
	
	function getAsientoCancelacionRecibo($aCancelacion, $procesoId, $agrupar=0){
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$oTipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
		$oBancoCuentaMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
		$oSolicitud = $this->importarModelo('Solicitud', 'v1');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$cModulo = 'CANCRECI';
		

		// BUCLE DE LA ORDEN DE COBRO
			$reciboSolicitud = array();
			if($aCancelacion[0]['recibo_id'] != 0):
				$reciboSolicitud = $oSolicitud->find('all', array('conditions' => array('Solicitud.recibo_id' => $aCancelacion[0]['recibo_id'])));
			endif;
			
			if(empty($reciboSolicitud)):
				$aOrdenCobro = $this->getCajaCobroSocio($aCancelacion[0]['orden_descuento_cobro_id']);
				// EMPIEZA EL BUCLE DE LAS CUOTAS
				foreach($aOrdenCobro as $aCobro):
							
					$glb = $this->getGlobalDato('concepto_1',$aCobro['PersonaBeneficio']['codigo_beneficio']);
					$cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'ORGAN', 'MutualCuentaAsiento.tipo_producto' => $aCobro['PersonaBeneficio']['codigo_beneficio'])));
								
					$asientoOrganismo = array();
					$asientoOrganismo['MutualTemporalAsientoRenglon']['fecha'] = $aCancelacion[0]['fecha'];
					$asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
					$asientoOrganismo['MutualTemporalAsientoRenglon']['importe'] = $aCobro[0]['importe_cobrado'];
					$asientoOrganismo['MutualTemporalAsientoRenglon']['referencia'] = $glb['GlobalDato']['concepto_1'];
					$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 0;
					$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
											
					if(empty($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
						$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 1;
						$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
					endif;
			
					$cuenta = $this->getCuenta($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
					$asientoOrganismo['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
					$asientoOrganismo['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
					$asientoOrganismo['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
					$asientoOrganismo['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0; 
					
					$oMutualTemporalAsientoRenglon->id = 0;
					$oMutualTemporalAsientoRenglon->save($asientoOrganismo);
						
					$tmpAsiento = array();
					$tmpAsiento['MutualTemporalAsientoRenglon']['fecha'] = $aCancelacion[0]['fecha'];

                                        /*
                                         * Verifico que las cuotas sean de los Comercio para imputarla a la cuenta correspondiente
                                         * 08/09/2017
                                         * Tiene que ir con la Variable MUTALPROVEEDORID
                                         */
                                        if($aCobro['OrdenDescuentoCuota']['proveedor_id'] != 18){
                                            $cuentaProveedor = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'COMER', 'MutualCuentaAsiento.tipo_producto' => 'PROVEEDOR')));
                                            $aCobro['MutualCuentaAsiento']['co_plan_cuenta_id'] = $cuentaProveedor[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
                                            $aCobro['MutualCuentaAsiento']['mutual_tipo_asiento_id'] = 0;
                                        }
					if($aCobro['MutualCuentaAsiento']['co_plan_cuenta_id'] == 0 && $aCobro['MutualCuentaAsiento']['mutual_tipo_asiento_id'] == 0):
						$tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
						$tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aCobro[0]['importe_cobrado'] * (-1);
						$tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'CANCELACION';
						$tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
						$tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
					else:
						if(!empty($aCobro['MutualCuentaAsiento']['co_plan_cuenta_id'])):
							$tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $aCobro['MutualCuentaAsiento']['co_plan_cuenta_id'];
							$tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aCobro[0]['importe_cobrado'] * (-1);
						else:
							$tipoAsiento = $oTipoAsientoRenglon->find('all', array('conditions' => array('MutualTipoAsientoRenglon.mutual_tipo_asiento_id' => $aCobro['MutualCuentaAsiento']['mutual_tipo_asiento_id'], 'MutualTipoAsientoRenglon.variable' => 'TOTAL')));
							if(empty($tipoAsiento)):
								$tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
								$tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aCobro[0]['importe_cobrado'] * (-1);
							else:
								$tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $tipoAsiento[0]['MutualTipoAsientoRenglon']['co_plan_cuenta_id'];
								$tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = ($tipoAsiento[0]['MutualTipoAsientoRenglon']['debe_haber'] == 'D' ? $aCobro[0]['importe_cobrado'] * (-1) : $aCobro[0]['importe_cobrado']);
							endif;
						endif;
						$tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'CANCELACION';
						$tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
						$tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
						if(empty($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
							$tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
							$tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
						endif;
					endif;
						
							
					$cuenta = $this->getCuenta($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
					$tmpAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
					$tmpAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
					$tmpAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
					$tmpAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0; 
					
					$oMutualTemporalAsientoRenglon->id = 0;
					$oMutualTemporalAsientoRenglon->save($tmpAsiento);

				endforeach;
	
				
				if($agrupar == 0):

					$queryImporte = "SELECT SUM(importe) as importe 
									FROM mutual_temporal_asiento_renglones
									WHERE mutual_asiento_id = 0
									GROUP BY mutual_asiento_id, co_plan_cuenta_id";
					$importeAsiento = $this->query($queryImporte);
					
					$debe = 0;
					$haber = 0;
					foreach($importeAsiento as $importe):
						if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
						else  $haber += $importe[0]['importe'];
					endforeach;
							
					$aMutualAsiento = array();
					$aMutualAsiento['MutualAsiento']['id'] = 0;
					$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
					$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
					$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
					$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
					$aMutualAsiento['MutualAsiento']['fecha'] = $aCancelacion[0]['fecha'];
					$aMutualAsiento['MutualAsiento']['tipo_documento'] = '';
					$aMutualAsiento['MutualAsiento']['nro_documento'] = $aCancelacion[0]['id'];
					$aMutualAsiento['MutualAsiento']['referencia'] = 'CANCELACION';
					$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
					$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
					$aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
					$aMutualAsiento['MutualAsiento']['tipo_asiento'] = 0;
					
					$this->grabarAsiento($aMutualAsiento, $procesoId);			
				endif;
			
			endif;
		
		
		
	}
	
	
	function getAsientoCancelaciones($aCancelacion, $procesoId, $agrupar=0){
            $oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
            $oTipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
            $oBancoCuentaMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
            $oSolicitud = $this->importarModelo('Solicitud', 'v1');
            $oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
            $cModulo = 'CANCSOCI';


            // BUCLE DE LA ORDEN DE COBRO

            $aOrdenCobro = $this->getCajaCobroSocio($aCancelacion[0]['orden_descuento_cobro_id']);

            // EMPIEZA EL BUCLE DE LAS CUOTAS
            foreach($aOrdenCobro as $aCobro):

                $cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'COMER', 'MutualCuentaAsiento.tipo_producto' => 'PROVEEDOR')));

                $asientoOrganismo = array();
                $asientoOrganismo['MutualTemporalAsientoRenglon']['fecha'] = $aCancelacion[0]['fecha'];
                $asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
                $asientoOrganismo['MutualTemporalAsientoRenglon']['importe'] = $aCobro[0]['importe_cobrado'];
                $asientoOrganismo['MutualTemporalAsientoRenglon']['referencia'] = 'A CUENTA DE COMERCIO';
                $asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 0;
                $asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 

                if(empty($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
                    $asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 1;
                    $asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
                endif;

                $cuenta = $this->getCuenta($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
                $asientoOrganismo['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
                $asientoOrganismo['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
                $asientoOrganismo['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
                $asientoOrganismo['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;

                $oMutualTemporalAsientoRenglon->id = 0;
                $oMutualTemporalAsientoRenglon->save($asientoOrganismo);

                $tmpAsiento = array();
                $tmpAsiento['MutualTemporalAsientoRenglon']['fecha'] = $aCancelacion[0]['fecha'];

                /*
                 * Verifico que las cuotas sean de los Comercio para imputarla a la cuenta correspondiente
                 * 08/09/2017
                 * Tiene que ir con la Variable MUTALPROVEEDORID
                 */
                if($aCobro['OrdenDescuentoCuota']['proveedor_id'] != 18){
                    $cuentaProveedor = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'COMER', 'MutualCuentaAsiento.tipo_producto' => 'PROVEEDOR')));
                    $aCobro['MutualCuentaAsiento']['co_plan_cuenta_id'] = $cuentaProveedor[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
                    $aCobro['MutualCuentaAsiento']['mutual_tipo_asiento_id'] = 0;
                }
                if($aCobro['MutualCuentaAsiento']['co_plan_cuenta_id'] == 0 && $aCobro['MutualCuentaAsiento']['mutual_tipo_asiento_id'] == 0):
                    $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
                    $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aCobro[0]['importe_cobrado'] * (-1);
                    $tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'CANCELACION';
                    $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                    $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
                else:
                    if(!empty($aCobro['MutualCuentaAsiento']['co_plan_cuenta_id'])):
                        $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $aCobro['MutualCuentaAsiento']['co_plan_cuenta_id'];
                        $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aCobro[0]['importe_cobrado'] * (-1);
                    else:
                        $tipoAsiento = $oTipoAsientoRenglon->find('all', array('conditions' => array('MutualTipoAsientoRenglon.mutual_tipo_asiento_id' => $aCobro['MutualCuentaAsiento']['mutual_tipo_asiento_id'], 'MutualTipoAsientoRenglon.variable' => 'TOTAL')));
                        if(empty($tipoAsiento)):
                                $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
                                $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = $aCobro[0]['importe_cobrado'] * (-1);
                        else:
                                $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $tipoAsiento[0]['MutualTipoAsientoRenglon']['co_plan_cuenta_id'];
                                $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = ($tipoAsiento[0]['MutualTipoAsientoRenglon']['debe_haber'] == 'D' ? $aCobro[0]['importe_cobrado'] * (-1) : $aCobro[0]['importe_cobrado']);
                        endif;
                    endif;
                    $tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'CANCELACION';
                    $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
                    $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
                    if(empty($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
                        $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                        $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
                    endif;
                endif;


                $cuenta = $this->getCuenta($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
                $tmpAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
                $tmpAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
                $tmpAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
                $tmpAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;

                $oMutualTemporalAsientoRenglon->id = 0;
                $oMutualTemporalAsientoRenglon->save($tmpAsiento);
            endforeach;



            $queryImporte = "SELECT SUM(importe) as importe 
                                FROM mutual_temporal_asiento_renglones
                                WHERE mutual_asiento_id = 0
                                GROUP BY mutual_asiento_id, co_plan_cuenta_id";
            $importeAsiento = $this->query($queryImporte);

            $debe = 0;
            $haber = 0;
            foreach($importeAsiento as $importe):
                if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
                else  $haber += $importe[0]['importe'];
            endforeach;

            if($debe == 0 && $haber == 0):
                $queryImporte = "DELETE
                                FROM mutual_temporal_asiento_renglones
                                WHERE mutual_asiento_id = 0";
                $importeAsiento = $this->query($queryImporte);
            else:
                if($agrupar == 0):
                    $aMutualAsiento = array();
                    $aMutualAsiento['MutualAsiento']['id'] = 0;
                    $aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
                    $aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
                    $aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
                    $aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
                    $aMutualAsiento['MutualAsiento']['fecha'] = $aCancelacion[0]['fecha'];
                    $aMutualAsiento['MutualAsiento']['tipo_documento'] = '';
                    $aMutualAsiento['MutualAsiento']['nro_documento'] = $aCancelacion[0]['id'];
                    $aMutualAsiento['MutualAsiento']['referencia'] = 'CANCELACION';
                    $aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
                    $aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
                    $aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
                    $aMutualAsiento['MutualAsiento']['tipo_asiento'] = 0;

                    $this->grabarAsiento($aMutualAsiento, $procesoId);			
                endif;			
            endif;
		
		
	}
	
	
/*
 * PROCESO SOLICITUDES DE PRODUCTOS DE LA MUTUAL A DEVENGAR EN LA ETAPA DE APROBACION.
 */
	function getMutualSolicitudes($fecha_desde, $fecha_hasta){
		$sql ="SELECT	OrdenDescuento.id, OrdenDescuento.fecha, OrdenDescuento.socio_id, CONCAT(Persona.apellido, ', ', Persona.Nombre) AS socio, OrdenDescuento.numero, GlobalDato.concepto_1, OrdenDescuento.importe_total, MutualCuentaAsiento.mutual_tipo_asiento_id
				FROM	orden_descuentos OrdenDescuento
				INNER JOIN
						socios Socio
				ON		
						OrdenDescuento.socio_id = Socio.id
				INNER JOIN	
						personas Persona
				ON
						Socio.persona_id = Persona.id
				INNER JOIN
						mutual_producto_solicitudes MutualProductoSolicitud
				ON
						MutualProductoSolicitud.orden_descuento_id = OrdenDescuento.id
				INNER JOIN 
						global_datos GlobalDato
				ON	
						GlobalDato.id = OrdenDescuento.tipo_producto
				INNER JOIN 
						mutual_cuenta_asientos MutualCuentaAsiento
				ON	
						CONCAT(OrdenDescuento.tipo_orden_dto, OrdenDescuento.tipo_producto, GlobalDato.concepto_2) = 
						CONCAT(MutualCuentaAsiento.tipo_orden_dto, MutualCuentaAsiento.tipo_producto, MutualCuentaAsiento.tipo_cuota)
				WHERE	
						MutualCuentaAsiento.instancia = 'APROB' AND OrdenDescuento.fecha > '$fecha_desde' AND OrdenDescuento.fecha <= '$fecha_hasta'
		";
		
		
		
		
		return $this->query($sql);
		
	}


	function getAsientoSolicitudes($solicitud, $procesoId, $agrupar=0){
		$oTipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
		$oMutualTipoAsiento = $this->importarModelo('MutualTipoAsiento', 'mutual');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$cModulo = 'SOLISOCI';
		
			$tipoAsiento = $oTipoAsientoRenglon->find('all', array('conditions' => array('MutualTipoAsientoRenglon.mutual_tipo_asiento_id' => $solicitud['MutualCuentaAsiento']['mutual_tipo_asiento_id'], 'MutualTipoAsientoRenglon.variable' => 'TOTAL')));
			$asientoSolicitud = array();
			$asientoSolicitud['MutualTemporalAsientoRenglon']['fecha'] = $solicitud['OrdenDescuento']['fecha'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $tipoAsiento[0]['MutualTipoAsientoRenglon']['co_plan_cuenta_id'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['importe'] = ($tipoAsiento[0]['MutualTipoAsientoRenglon']['debe_haber'] == 'D' ? $solicitud['OrdenDescuento']['importe_total'] : $solicitud['OrdenDescuento']['importe_total'] * (-1));
			$asientoSolicitud['MutualTemporalAsientoRenglon']['referencia'] = $solicitud[0]['socio'] . " NRO.SOL. " . $solicitud['OrdenDescuento']['numero'] . "-" . $solicitud['GlobalDato']['concepto_1'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['error'] = 0;
			$asientoSolicitud['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
										
			if(empty($asientoSolicitud['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
				$asientoSolicitud['MutualTemporalAsientoRenglon']['error'] = 1;
				$asientoSolicitud['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
			endif;
		
			$cuenta = $this->getCuenta($asientoSolicitud['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$asientoSolicitud['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
			$asientoSolicitud['MutualTemporalAsientoRenglon']['tipo_asiento'] = $solicitud['MutualCuentaAsiento']['mutual_tipo_asiento_id'];
			
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($asientoSolicitud);
					
			$tipoAsiento = $oTipoAsientoRenglon->find('all', array('conditions' => array('MutualTipoAsientoRenglon.mutual_tipo_asiento_id' => $solicitud['MutualCuentaAsiento']['mutual_tipo_asiento_id'], 'MutualTipoAsientoRenglon.variable' => 'PRODU')));
			$asientoSolicitud = array();
			$asientoSolicitud['MutualTemporalAsientoRenglon']['fecha'] = $solicitud['OrdenDescuento']['fecha'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $tipoAsiento[0]['MutualTipoAsientoRenglon']['co_plan_cuenta_id'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['importe'] = ($tipoAsiento[0]['MutualTipoAsientoRenglon']['debe_haber'] == 'D' ? $solicitud['OrdenDescuento']['importe_total'] : $solicitud['OrdenDescuento']['importe_total'] * (-1));
			$asientoSolicitud['MutualTemporalAsientoRenglon']['referencia'] = $solicitud[0]['socio'] . " NRO.SOL. " . $solicitud['OrdenDescuento']['numero'] . "-" . $solicitud['GlobalDato']['concepto_1'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['error'] = 0;
			$asientoSolicitud['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
										
			if(empty($asientoSolicitud['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
				$asientoSolicitud['MutualTemporalAsientoRenglon']['error'] = 1;
				$asientoSolicitud['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
			endif;
		
			$cuenta = $this->getCuenta($asientoSolicitud['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$asientoSolicitud['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
			$asientoSolicitud['MutualTemporalAsientoRenglon']['tipo_asiento'] = $solicitud['MutualCuentaAsiento']['mutual_tipo_asiento_id'];
			
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($asientoSolicitud);
					
			if($agrupar == 0):

				$queryImporte = "SELECT SUM(importe) as importe 
								FROM mutual_temporal_asiento_renglones
								WHERE mutual_asiento_id = 0
								GROUP BY mutual_asiento_id, co_plan_cuenta_id";
				$importeAsiento = $this->query($queryImporte);
					
				$debe = 0;
				$haber = 0;
				foreach($importeAsiento as $importe):
					if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
					else  $haber += $importe[0]['importe'];
				endforeach;
					
				$tipoAsiento = $oMutualTipoAsiento->find('all', array('conditions' => array('MutualTipoAsiento.id' => $solicitud['MutualCuentaAsiento']['mutual_tipo_asiento_id'])));
				$aMutualAsiento = array();
				$aMutualAsiento['MutualAsiento']['id'] = 0;
				$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
				$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
				$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
				$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
				$aMutualAsiento['MutualAsiento']['fecha'] = $solicitud['OrdenDescuento']['fecha'];
				$aMutualAsiento['MutualAsiento']['tipo_documento'] = '';
				$aMutualAsiento['MutualAsiento']['nro_documento'] = $solicitud['OrdenDescuento']['id'];
				$aMutualAsiento['MutualAsiento']['referencia'] = $tipoAsiento[0]['MutualTipoAsiento']['concepto'];
				$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
				$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
				$aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
				$aMutualAsiento['MutualAsiento']['tipo_asiento'] = $solicitud['MutualCuentaAsiento']['mutual_tipo_asiento_id'];
				
				$this->grabarAsiento($aMutualAsiento, $procesoId);			
			endif;
		
	}
	

/*
 * PROCESO CUOTAS DE LA MUTUAL A DEVENGAR EN LA ETAPA DE IMPUTACION O LIQUIDACION.
 */
	function getMutualCuotas($fecha_desde, $fecha_hasta){
		$sql = "SELECT	Liquidacion.id, Liquidacion.fecha_imputacion, OrdenDescuentoCuota.orden_descuento_id, SUM(OrdenDescuentoCuota.importe) AS importe, 
						GlobalDato.concepto_1, MutualCuentaAsiento.mutual_tipo_asiento_id
				FROM	liquidacion_cuotas LiquidacionCuota
				INNER	JOIN liquidaciones Liquidacion
				ON	Liquidacion.id = LiquidacionCuota.liquidacion_id AND Liquidacion.periodo = LiquidacionCuota.periodo_cuota
				INNER	JOIN orden_descuento_cuotas OrdenDescuentoCuota
				ON	LiquidacionCuota.orden_descuento_cuota_id = OrdenDescuentoCuota.id
				INNER	JOIN global_datos GlobalDato
				ON	GlobalDato.id = LiquidacionCuota.tipo_producto
				INNER	JOIN mutual_cuenta_asientos MutualCuentaAsiento
				ON	CONCAT(LiquidacionCuota.tipo_orden_dto, LiquidacionCuota.tipo_producto, LiquidacionCuota.tipo_cuota) = 
					CONCAT(MutualCuentaAsiento.tipo_orden_dto, MutualCuentaAsiento.tipo_producto, MutualCuentaAsiento.tipo_cuota)
				WHERE	Liquidacion.fecha_imputacion > '$fecha_desde' AND Liquidacion.fecha_imputacion <= '$fecha_hasta' AND MutualCuentaAsiento.instancia = 'LIQUI'
				GROUP	BY Liquidacion.fecha_imputacion, LiquidacionCuota.codigo_organismo, MutualCuentaAsiento.mutual_tipo_asiento_id
		";
		
		return $this->query($sql);
		
	}


	function getAsientoCuotas($cuota, $procesoId, $agrupar=0){
		$oTipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
		$oMutualTipoAsiento = $this->importarModelo('MutualTipoAsiento', 'mutual');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$cModulo = 'CUOTSOCI';
		
			$tipoAsiento = $oTipoAsientoRenglon->find('all', array('conditions' => array('MutualTipoAsientoRenglon.mutual_tipo_asiento_id' => $cuota['MutualCuentaAsiento']['mutual_tipo_asiento_id'], 'MutualTipoAsientoRenglon.variable' => 'TOTAL')));

			$asientoSolicitud = array();
			$asientoSolicitud['MutualTemporalAsientoRenglon']['fecha'] = $cuota['Liquidacion']['fecha_imputacion'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $tipoAsiento[0]['MutualTipoAsientoRenglon']['co_plan_cuenta_id'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['importe'] = ($tipoAsiento[0]['MutualTipoAsientoRenglon']['debe_haber'] == 'D' ? $cuota[0]['importe'] : $cuota[0]['importe'] * (-1));
			$asientoSolicitud['MutualTemporalAsientoRenglon']['referencia'] = $cuota['GlobalDato']['concepto_1'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['error'] = 0;
			$asientoSolicitud['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
										
			if(empty($asientoSolicitud['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
				$asientoSolicitud['MutualTemporalAsientoRenglon']['error'] = 1;
				$asientoSolicitud['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO. LIQ.NRO: ' . $cuota['Liquidacion']['id']; 
			endif;
		
			$cuenta = $this->getCuenta($asientoSolicitud['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$asientoSolicitud['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
			$asientoSolicitud['MutualTemporalAsientoRenglon']['tipo_asiento'] = $cuota['MutualCuentaAsiento']['mutual_tipo_asiento_id'];
			
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($asientoSolicitud);
					
			$tipoAsiento = $oTipoAsientoRenglon->find('all', array('conditions' => array('MutualTipoAsientoRenglon.mutual_tipo_asiento_id' => $cuota['MutualCuentaAsiento']['mutual_tipo_asiento_id'], 'MutualTipoAsientoRenglon.variable' => 'PRODU')));
			$asientoSolicitud = array();
			$asientoSolicitud['MutualTemporalAsientoRenglon']['fecha'] = $cuota['Liquidacion']['fecha_imputacion'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $tipoAsiento[0]['MutualTipoAsientoRenglon']['co_plan_cuenta_id'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['importe'] = ($tipoAsiento[0]['MutualTipoAsientoRenglon']['debe_haber'] == 'D' ? $cuota[0]['importe'] : $cuota[0]['importe'] * (-1));
			$asientoSolicitud['MutualTemporalAsientoRenglon']['referencia'] = $cuota['GlobalDato']['concepto_1'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['error'] = 0;
			$asientoSolicitud['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
										
			if(empty($asientoSolicitud['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
				$asientoSolicitud['MutualTemporalAsientoRenglon']['error'] = 1;
				$asientoSolicitud['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO. LIQ.NRO: ' . $cuota['Liquidacion']['id']; 
			endif;
		
			$cuenta = $this->getCuenta($asientoSolicitud['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$asientoSolicitud['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
			$asientoSolicitud['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
			$asientoSolicitud['MutualTemporalAsientoRenglon']['tipo_asiento'] = $cuota['MutualCuentaAsiento']['mutual_tipo_asiento_id'];
			
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($asientoSolicitud);
					
			if($agrupar == 0):

				$queryImporte = "SELECT SUM(importe) as importe 
								FROM mutual_temporal_asiento_renglones
								WHERE mutual_asiento_id = 0
								GROUP BY mutual_asiento_id, co_plan_cuenta_id";
				$importeAsiento = $this->query($queryImporte);
					
				$debe = 0;
				$haber = 0;
				foreach($importeAsiento as $importe):
					if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
					else  $haber += $importe[0]['importe'];
				endforeach;
					
				$tipoAsiento = $oMutualTipoAsiento->find('all', array('conditions' => array('MutualTipoAsiento.id' => $cuota['MutualCuentaAsiento']['mutual_tipo_asiento_id'])));
				$aMutualAsiento = array();
				$aMutualAsiento['MutualAsiento']['id'] = 0;
				$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
				$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
				$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
				$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
				$aMutualAsiento['MutualAsiento']['fecha'] = $cuota['Liquidacion']['fecha_imputacion'];
				$aMutualAsiento['MutualAsiento']['tipo_documento'] = '';
				$aMutualAsiento['MutualAsiento']['nro_documento'] = $cuota['OrdenDescuentoCuota']['orden_descuento_id'];
				$aMutualAsiento['MutualAsiento']['referencia'] = $tipoAsiento[0]['MutualTipoAsiento']['concepto'] . ' - LIQ.NRO. ' . $cuota['Liquidacion']['id'];
				$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
				$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
				$aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
				$aMutualAsiento['MutualAsiento']['tipo_asiento'] = $cuota['MutualCuentaAsiento']['mutual_tipo_asiento_id'];
				
				$this->grabarAsiento($aMutualAsiento, $procesoId);			
			endif;
		
	}
	
	
	/*
	 * PROCESO DE REINTEGROS
	 */
	function getReintegro($fecha_desde, $fecha_hasta){
//		$sqlReintegro = "SELECT	Liquidacion.fecha_imputacion AS fecha, Liquidacion.codigo_organismo AS codigo_beneficio, SUM(SocioReintegro.importe_reintegro) AS importe_reintegro -- , SocioReintegro.*
//						FROM	socio_reintegros SocioReintegro
//						INNER JOIN liquidaciones Liquidacion
//						ON	SocioReintegro.liquidacion_id = Liquidacion.id
//						WHERE	Liquidacion.fecha_imputacion > '$fecha_desde' AND Liquidacion.fecha_imputacion <= '$fecha_hasta'
//						GROUP BY Liquidacion.fecha_imputacion, Liquidacion.codigo_organismo
						
//						UNION
						
		$sqlReintegro = "SELECT	Recibo.fecha_comprobante AS fecha, PersonaBeneficio.codigo_beneficio AS codigo_beneficio, SUM(SocioReintegro.importe_reintegro) AS importe_reintegro -- , SocioReintegro.*
                                    FROM	socio_reintegros SocioReintegro
                                    INNER JOIN recibos Recibo
                                    ON	Recibo.id = SocioReintegro.recibo_id
                                    INNER JOIN socios Socio
                                    ON	Socio.id = SocioReintegro.socio_id
                                    INNER JOIN persona_beneficios PersonaBeneficio
                                    ON	PersonaBeneficio.id = Socio.persona_beneficio_id
                                    WHERE	Recibo.fecha_comprobante > '$fecha_desde' AND Recibo.fecha_comprobante <= '$fecha_hasta'
                                    GROUP BY Recibo.fecha_comprobante, PersonaBeneficio.codigo_beneficio
                                    ORDER BY fecha
		";
		
		
		return $this->query($sqlReintegro);
		
	}	
	
	
	function getAsientoReintegro($reintegro, $procesoId, $agrupar=0){
		$oMutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		$oTipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$cModulo = 'REINSOCI';
		

			$glb = $this->getGlobalDato('concepto_1',$reintegro[0]['codigo_beneficio']);
			$cuentaAsiento = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'ORGAN', 'MutualCuentaAsiento.tipo_producto' => $reintegro[0]['codigo_beneficio'])));
			
			$asientoOrganismo = array();
			$asientoOrganismo['MutualTemporalAsientoRenglon']['fecha'] = $reintegro[0]['fecha'];
			$asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
			$asientoOrganismo['MutualTemporalAsientoRenglon']['importe'] = $reintegro[0]['importe_reintegro'];
			$asientoOrganismo['MutualTemporalAsientoRenglon']['referencia'] = $glb['GlobalDato']['concepto_1'];
			$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 0;
			$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
						
			if(empty($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
				$asientoOrganismo['MutualTemporalAsientoRenglon']['error'] = 1;
				$asientoOrganismo['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO'; 
			endif;

			$cuenta = $this->getCuenta($asientoOrganismo['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$asientoOrganismo['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$asientoOrganismo['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
			$asientoOrganismo['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
			$asientoOrganismo['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
			
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($asientoOrganismo);
			
			$cuentaReintegro = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'SOCIO','MutualCuentaAsiento.tipo_producto' => 'REINTEGRO')));
			$temporalAsiento = array();
			$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $reintegro[0]['fecha'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $reintegro[0]['importe_reintegro'] * (-1);
			$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'REINTEGRO';
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
	
			if(empty($cuentaReintegro)):
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = 0;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA REINTEGRO NO EXITE'; 
	
			else:
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaReintegro[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
						
				if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
					$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'CUENTA REINTEGRO NO CONFIGURADA'; 
				endif;
			endif;
				
			$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
			$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
			
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->save($temporalAsiento);

			if($agrupar == 0):

				$queryImporte = "SELECT SUM(importe) as importe 
								FROM mutual_temporal_asiento_renglones
								WHERE mutual_asiento_id = 0
								GROUP BY mutual_asiento_id, co_plan_cuenta_id";
				$importeAsiento = $this->query($queryImporte);
					
				$debe = 0;
				$haber = 0;
				foreach($importeAsiento as $importe):
					if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
					else  $haber += $importe[0]['importe'];
				endforeach;
				
				$aMutualAsiento = array();
				$aMutualAsiento['MutualAsiento']['id'] = 0;
				$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
				$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
				$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
				$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
				$aMutualAsiento['MutualAsiento']['fecha'] = $reintegro[0]['fecha'];
				$aMutualAsiento['MutualAsiento']['tipo_documento'] = '';
				$aMutualAsiento['MutualAsiento']['nro_documento'] = '';
				$aMutualAsiento['MutualAsiento']['referencia'] = 'REINTEGRO ' . $glb['GlobalDato']['concepto_1'];
				$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
				$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
				$aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
				$aMutualAsiento['MutualAsiento']['tipo_asiento'] = 0;
				
				$this->grabarAsiento($aMutualAsiento, $procesoId);			
			endif;	
					

	}
	
	
	/*
	 * PROCESO ASIENTO FACTURAS DE PROVEEDORES
	 */
	function getProveedorFactura($fecha_desde, $fecha_hasta){
		$sqlFacturas = "SELECT	Proveedor.razon_social, ProveedorFactura.*
						FROM	proveedor_facturas ProveedorFactura
						INNER JOIN proveedores Proveedor
						ON	Proveedor.id = ProveedorFactura.proveedor_id
						WHERE	ProveedorFactura.fecha_comprobante > '$fecha_desde' AND ProveedorFactura.fecha_comprobante <= '$fecha_hasta' AND 
								ProveedorFactura.orden_descuento_cobro_id = 0 AND ProveedorFactura.liquidacion_id = 0 AND 
								ProveedorFactura.socio_id = 0 AND ProveedorFactura.tipo != 'SA'
		";
		
		
		return $this->query($sqlFacturas);
	}
	
	
	function getAsientoProveedorFactura($factura, $procesoId, $agrupar=0){
		$oProveedorFactura = $this->importarModelo('ProveedorFactura', 'proveedores');
		$oTipoAsientoProveedor = $this->importarModelo('TipoAsiento', 'proveedores');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$cModulo = 'PROVFACT';
		
			$temporalAsiento = array();
//			if($factura['ProveedorFactura']['tipo'] == 'NC'):
//				$temporalAsiento = $oProveedorFactura->getAsientoCredito($factura);	
//			else:
//				$temporalAsiento = $oProveedorFactura->getAsientoFactura($factura);	
//			endif;
			
			$temporalAsiento = $oProveedorFactura->getAsientoFactura($factura);	

                        foreach($temporalAsiento as $key => $valor):
				$cuenta = $this->getCuenta($valor['co_plan_cuenta_id']);
				$temporalAsiento[$key]['fecha'] = $factura['ProveedorFactura']['fecha_comprobante'];
				$temporalAsiento[$key]['cuenta'] = $cuenta['cuenta'];
				$temporalAsiento[$key]['descripcion'] = $cuenta['descripcion'];
				$temporalAsiento[$key]['modulo'] = $cModulo;
				$temporalAsiento[$key]['tipo_asiento'] = $factura['ProveedorFactura']['proveedor_tipo_asiento_id'];
				$temporalAsiento[$key]['referencia'] = $factura['Proveedor']['razon_social'];
                                
                                if($factura['ProveedorFactura']['tipo'] == 'NC'):
                                    $temporalAsiento[$key]['importe'] *= (-1);
                                endif;
				
			
			endforeach;
			
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->saveAll($temporalAsiento);

			if($agrupar == 0):
				$refAsiento = $oTipoAsientoProveedor->find('all', array('conditions' => array('TipoAsiento.id' =>$factura['ProveedorFactura']['proveedor_tipo_asiento_id'])));

				$queryImporte = "SELECT SUM(importe) as importe 
								FROM mutual_temporal_asiento_renglones
								WHERE mutual_asiento_id = 0
								GROUP BY mutual_asiento_id, co_plan_cuenta_id";
				$importeAsiento = $this->query($queryImporte);
					
				$debe = 0;
				$haber = 0;
				foreach($importeAsiento as $importe):
					if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
					else  $haber += $importe[0]['importe'];
				endforeach;
					
				$aMutualAsiento = array();
				$aMutualAsiento['MutualAsiento']['id'] = 0;
				$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
				$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
				$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
				$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
				$aMutualAsiento['MutualAsiento']['fecha'] = $factura['ProveedorFactura']['fecha_comprobante'];
				$aMutualAsiento['MutualAsiento']['tipo_documento'] = $factura['ProveedorFactura']['tipo'];
				$aMutualAsiento['MutualAsiento']['nro_documento'] = $factura['ProveedorFactura']['letra_comprobante'] . '-' . $factura['ProveedorFactura']['punto_venta_comprobante'] . '-' . $factura['ProveedorFactura']['numero_comprobante'];
				$aMutualAsiento['MutualAsiento']['referencia'] = $refAsiento[0]['TipoAsiento']['concepto'] . ' (' . $factura['Proveedor']['razon_social'] . ')';
				$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
				$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
				$aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
				$aMutualAsiento['MutualAsiento']['tipo_asiento'] = $factura['ProveedorFactura']['proveedor_tipo_asiento_id'];
				
				$this->grabarAsiento($aMutualAsiento, $procesoId);	
			endif;			
		
	}
	
	
	/*
	 * PROCESO ASIENTO FACTURAS DE CLIENTES
	 */
	function getClienteFactura($fecha_desde, $fecha_hasta){
		$sqlFacturas = "SELECT	Cliente.razon_social, ClienteFactura.*
						FROM 	cliente_facturas ClienteFactura
						INNER JOIN clientes Cliente
						ON 	Cliente.id = ClienteFactura.cliente_id
						WHERE	ClienteFactura.fecha_comprobante > '$fecha_desde' AND ClienteFactura.fecha_comprobante <= '$fecha_hasta' AND ClienteFactura.anulado = 0
						ORDER BY ClienteFactura.fecha_comprobante
				";
		
		
		return $this->query($sqlFacturas);
	}
	
	
	function getAsientoClienteFactura($factura, $procesoId, $agrupar=0){
		$oClienteFactura = $this->importarModelo('ClienteFactura', 'clientes');
		$oTipoAsiento = $this->importarModelo('ClienteTipoAsiento', 'clientes');
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$cModulo = 'CLIEFACT';
		
			$temporalAsiento = array();
//			if($factura['ClienteFactura']['tipo'] == 'NC'):
//				$temporalAsiento = $oClienteFactura->getAsientoCredito($factura);	
//			else:
//				$temporalAsiento = $oClienteFactura->getAsientoFactura($factura);	
//			endif;
			
			$temporalAsiento = $oClienteFactura->getAsientoFactura($factura);	
                        

                        foreach($temporalAsiento as $key => $valor):
				$cuenta = $this->getCuenta($valor['co_plan_cuenta_id']);
				$temporalAsiento[$key]['fecha'] = $factura['ClienteFactura']['fecha_comprobante'];
				$temporalAsiento[$key]['cuenta'] = $cuenta['cuenta'];
				$temporalAsiento[$key]['descripcion'] = $cuenta['descripcion'];
				$temporalAsiento[$key]['modulo'] = $cModulo;
				$temporalAsiento[$key]['tipo_asiento'] = $factura['ClienteFactura']['cliente_tipo_asiento_id'];
				$temporalAsiento[$key]['referencia'] = $factura['Cliente']['razon_social'];
                                
                                if($factura['ClienteFactura']['tipo'] == 'NC'):
                                    $temporalAsiento[$key]['importe'] *= (-1);
                                endif;
				
			
			endforeach;

			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->saveAll($temporalAsiento);

			if($agrupar == 0):
				$refAsiento = $oTipoAsiento->find('all', array('conditions' => array('ClienteTipoAsiento.id' =>$factura['ClienteFactura']['cliente_tipo_asiento_id'])));
	

				$queryImporte = "SELECT SUM(importe) as importe 
								FROM mutual_temporal_asiento_renglones
								WHERE mutual_asiento_id = 0
								GROUP BY mutual_asiento_id, co_plan_cuenta_id";
				$importeAsiento = $this->query($queryImporte);
					
				$debe = 0;
				$haber = 0;
				foreach($importeAsiento as $importe):
					if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
					else  $haber += $importe[0]['importe'];
				endforeach;
					
				$aMutualAsiento = array();
				$aMutualAsiento['MutualAsiento']['id'] = 0;
				$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
				$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
				$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
				$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
				$aMutualAsiento['MutualAsiento']['fecha'] = $factura['ClienteFactura']['fecha_comprobante'];
				$aMutualAsiento['MutualAsiento']['tipo_documento'] = $factura['ClienteFactura']['tipo'];
				$aMutualAsiento['MutualAsiento']['nro_documento'] = $factura['ClienteFactura']['letra_comprobante'] . '-' . $factura['ClienteFactura']['punto_venta_comprobante'] . '-' . $factura['ClienteFactura']['numero_comprobante'];
				$aMutualAsiento['MutualAsiento']['referencia'] = $refAsiento[0]['ClienteTipoAsiento']['concepto'] . ' (' . $factura['Cliente']['razon_social'] . ')';
				$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
				$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
				$aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
				$aMutualAsiento['MutualAsiento']['tipo_asiento'] = $factura['ClienteFactura']['cliente_tipo_asiento_id'];
				
				$this->grabarAsiento($aMutualAsiento, $procesoId);	
			endif;			
		
	}
	
	
	/*
	 * PROCESO ASIENTO CAJA Y BANCO
	 */
	function getCajaBancoIndividual($fecha_desde, $fecha_hasta){
//		$sqlCajaBanco = "SELECT	BancoConcepto.concepto, IFNULL(BancoCuenta.co_plan_cuenta_id,0) as co_plan_cuenta_id, BancoCuentaMovimiento.*
//						FROM	banco_cuenta_movimientos BancoCuentaMovimiento
//						LEFT	JOIN banco_conceptos BancoConcepto
//						ON	BancoCuentaMovimiento.banco_concepto_id = BancoConcepto.id
//						INNER	JOIN banco_cuentas BancoCuenta
//						ON	BancoCuentaMovimiento.banco_cuenta_id = BancoCuenta.id
//						WHERE	BancoCuentaMovimiento.recibo_id = 0 AND BancoCuentaMovimiento.orden_pago_id = 0 AND BancoCuentaMovimiento.orden_descuento_cobro_id = 0 AND 
//							BancoCuentaMovimiento.anulado = 0 AND BancoCuentaMovimiento.banco_cuenta_movimiento_id = 0 AND  
//                                                      BancoCuentaMovimiento.tipo != 9 AND
//							BancoCuentaMovimiento.fecha_operacion > '$fecha_desde' AND BancoCuentaMovimiento.fecha_operacion <= '$fecha_hasta'
//				ORDER BY fecha_operacion
//		";
		
		$sqlCajaBanco = "SELECT	BancoConcepto.concepto, IFNULL(BancoCuenta.co_plan_cuenta_id,0) as co_plan_cuenta_id, BancoCuentaMovimiento.*
						FROM	banco_cuenta_movimientos BancoCuentaMovimiento
						LEFT	JOIN banco_conceptos BancoConcepto
						ON	BancoCuentaMovimiento.banco_concepto_id = BancoConcepto.id
						INNER	JOIN banco_cuentas BancoCuenta
						ON	BancoCuentaMovimiento.banco_cuenta_id = BancoCuenta.id
						WHERE	BancoCuentaMovimiento.recibo_id = 0 AND BancoCuentaMovimiento.orden_pago_id = 0 AND BancoCuentaMovimiento.orden_descuento_cobro_id = 0 AND 
                                                        ((BancoCuentaMovimiento.anulado = 0 AND BancoCuentaMovimiento.banco_cuenta_movimiento_id = 0) OR (BancoCuentaMovimiento.anulado = 1 AND BancoCuentaMovimiento.reemplazar = 1)) AND 
                                                        BancoCuentaMovimiento.tipo != 9 AND
							BancoCuentaMovimiento.fecha_operacion > '$fecha_desde' AND BancoCuentaMovimiento.fecha_operacion <= '$fecha_hasta'
				ORDER BY fecha_operacion
		";

                
		return $this->query($sqlCajaBanco);
	}
	
	
	function getCajaBancoRelacionado($fecha_desde, $fecha_hasta){
		$sqlCajaBanco = "SELECT	BancoConcepto.concepto, IFNULL(BancoCuenta.co_plan_cuenta_id,0) AS co_plan_cuenta_id, BancoCuentaMovimiento.*
						FROM	banco_cuenta_movimientos BancoCuentaMovimiento
						LEFT	JOIN banco_conceptos BancoConcepto
						ON		BancoCuentaMovimiento.banco_concepto_id = BancoConcepto.id
						INNER	JOIN banco_cuentas BancoCuenta
						ON		BancoCuentaMovimiento.banco_cuenta_id = BancoCuenta.id
						WHERE	BancoCuentaMovimiento.recibo_id = 0 AND BancoCuentaMovimiento.orden_pago_id = 0 AND 
								BancoCuentaMovimiento.orden_descuento_cobro_id = 0 AND BancoCuentaMovimiento.anulado = 0 AND 
								BancoCuentaMovimiento.banco_cuenta_movimiento_id > 0 AND BancoCuentaMovimiento.debe_haber = 0 AND
								BancoCuentaMovimiento.fecha_operacion > '$fecha_desde' AND BancoCuentaMovimiento.fecha_operacion <= '$fecha_hasta' AND 
								BancoCuentaMovimiento.tipo != 9
						ORDER	BY BancoCuentaMovimiento.fecha_operacion
		";
		
		
		return $this->query($sqlCajaBanco);
	}
	
	
	function getCajaBancoReemplazo($fecha_desde, $fecha_hasta){
		$sqlCajaBanco = "SELECT	BancoConcepto.concepto, IFNULL(BancoCuenta.co_plan_cuenta_id,0) AS co_plan_cuenta_id, BancoCuentaMovimiento.*
						FROM	banco_cuenta_movimientos BancoCuentaMovimiento
						LEFT	JOIN banco_conceptos BancoConcepto
						ON		BancoCuentaMovimiento.banco_concepto_id = BancoConcepto.id
						INNER	JOIN banco_cuentas BancoCuenta
						ON		BancoCuentaMovimiento.banco_cuenta_id = BancoCuenta.id
						WHERE	BancoCuentaMovimiento.anulado = 1 AND BancoCuentaMovimiento.reemplazar = 1 AND 
								BancoCuentaMovimiento.fecha_reemplazar > '$fecha_desde' AND BancoCuentaMovimiento.fecha_reemplazar <= '$fecha_hasta'
						ORDER	BY BancoCuentaMovimiento.fecha_operacion
		";
		
		
		return $this->query($sqlCajaBanco);
	}
	
	
	function getMovimientoRelacionado($id){
		$sqlMovimiento = "SELECT	BancoConcepto.concepto, IFNULL(BancoCuenta.co_plan_cuenta_id,0) AS co_plan_cuenta_id, BancoCuentaMovimiento.*
							FROM	banco_cuenta_movimientos BancoCuentaMovimiento
							LEFT	JOIN banco_conceptos BancoConcepto
							ON		BancoCuentaMovimiento.banco_concepto_id = BancoConcepto.id
							INNER	JOIN banco_cuentas BancoCuenta
							ON		BancoCuentaMovimiento.banco_cuenta_id = BancoCuenta.id
							WHERE	BancoCuentaMovimiento.id = '$id' 
		";
		
		
		return $this->query($sqlMovimiento);
	}
	
	
	function getAsientoCajaBancoIndividual($cajaBanco, $procesoId, $agrupar=0){
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$cModulo = 'CABAINDI';
		
			$tmpCajaBanco = array();
			
			if($cajaBanco['BancoCuentaMovimiento']['debe_haber'] == 0):
				$temporalAsiento = array();
				$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $cajaBanco['BancoCuentaMovimiento']['fecha_operacion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cajaBanco[0]['co_plan_cuenta_id'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $cajaBanco['BancoCuentaMovimiento']['importe'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $cajaBanco['BancoCuentaMovimiento']['descripcion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
				if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
					$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDA LA CUENTA PARA BANCO NRO.: ' . $cajaBanco['BancoCuentaMovimiento']['id']; 
				endif;
				$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
				$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
				$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
				
				array_push($tmpCajaBanco, $temporalAsiento);
				
				$temporalAsiento = array();
				$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $cajaBanco['BancoCuentaMovimiento']['fecha_operacion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cajaBanco['BancoCuentaMovimiento']['co_plan_cuenta_id'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $cajaBanco['BancoCuentaMovimiento']['importe'] * (-1);
				$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $cajaBanco['BancoCuentaMovimiento']['descripcion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
				if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
					$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDA LA CUENTA PARA CONCEPTO NRO.: ' . $cajaBanco['BancoCuentaMovimiento']['id']; 
				endif;
				$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
				$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
				$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
				
				array_push($tmpCajaBanco, $temporalAsiento);
				
			else:
				$temporalAsiento = array();
				$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $cajaBanco['BancoCuentaMovimiento']['fecha_operacion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cajaBanco['BancoCuentaMovimiento']['co_plan_cuenta_id'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $cajaBanco['BancoCuentaMovimiento']['importe'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $cajaBanco['BancoCuentaMovimiento']['descripcion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
				if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
					$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDA LA CUENTA PARA CONCEPTO NRO.: ' . $cajaBanco['BancoCuentaMovimiento']['id']; 
				endif;
				$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
				$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
				$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
				
				array_push($tmpCajaBanco, $temporalAsiento);
				
				$temporalAsiento = array();
				$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $cajaBanco['BancoCuentaMovimiento']['fecha_operacion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cajaBanco[0]['co_plan_cuenta_id'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $cajaBanco['BancoCuentaMovimiento']['importe'] * (-1);
				$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $cajaBanco['BancoCuentaMovimiento']['descripcion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
				if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
					$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDA LA CUENTA PARA BANCO NRO.: ' . $cajaBanco['BancoCuentaMovimiento']['id']; 
				endif;
				$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
				$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
				$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
				
				array_push($tmpCajaBanco, $temporalAsiento);
				
			endif;
			
			
						
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->saveAll($tmpCajaBanco);


			if($agrupar == 0):

				$queryImporte = "SELECT SUM(importe) as importe 
								FROM mutual_temporal_asiento_renglones
								WHERE mutual_asiento_id = 0
								GROUP BY mutual_asiento_id, co_plan_cuenta_id";
				$importeAsiento = $this->query($queryImporte);
					
				$debe = 0;
				$haber = 0;
				foreach($importeAsiento as $importe):
					if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
					else  $haber += $importe[0]['importe'];
				endforeach;
				
				$aMutualAsiento = array();
				$aMutualAsiento['MutualAsiento']['id'] = 0;
				$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
				$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
				$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
				$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
				$aMutualAsiento['MutualAsiento']['fecha'] = $cajaBanco['BancoCuentaMovimiento']['fecha_operacion'];
				$aMutualAsiento['MutualAsiento']['tipo_documento'] = '';
				$aMutualAsiento['MutualAsiento']['nro_documento'] = ($cajaBanco['BancoCuentaMovimiento']['tipo'] == 1 ? 'Nro.Cheque: ' . $cajaBanco['BancoCuentaMovimiento']['numero_operacion'] : '');
				$aMutualAsiento['MutualAsiento']['referencia'] = $cajaBanco['BancoConcepto']['concepto'] . ' (' . $cajaBanco['BancoCuentaMovimiento']['descripcion'] . ')';
				$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
				$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
				$aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
				$aMutualAsiento['MutualAsiento']['tipo_asiento'] = 0;
				
				$this->grabarAsiento($aMutualAsiento, $procesoId);	
			endif;
			
		
	}
	
	
	function getAsientoCajaBancoRelacionado($cajaBanco, $procesoId, $agrupar=0){
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$cModulo = 'CABARELA';
		
			$tmpCajaBanco = array();
			
			$temporalAsiento = array();
			$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $cajaBanco['BancoCuentaMovimiento']['fecha_operacion'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cajaBanco[0]['co_plan_cuenta_id'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $cajaBanco['BancoCuentaMovimiento']['importe'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $cajaBanco['BancoCuentaMovimiento']['descripcion'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
			if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDA LA CUENTA PARA BANCO NRO.: ' . $cajaBanco['BancoCuentaMovimiento']['id']; 
			endif;
			$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
			$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
			
			array_push($tmpCajaBanco, $temporalAsiento);
				
			$aMovimiento = $this->getMovimientoRelacionado($cajaBanco['BancoCuentaMovimiento']['banco_cuenta_movimiento_id']);

			$temporalAsiento = array();
			$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $cajaBanco['BancoCuentaMovimiento']['fecha_operacion'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $aMovimiento[0][0]['co_plan_cuenta_id'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $aMovimiento[0]['BancoCuentaMovimiento']['importe'] * (-1);
			$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $cajaBanco['BancoCuentaMovimiento']['descripcion'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
			$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
			if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDA LA CUENTA PARA BANCO NRO.: ' . $aMovimiento[0]['BancoCuentaMovimiento']['id']; 
			endif;
			$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
			$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
			$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
			$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
			
			array_push($tmpCajaBanco, $temporalAsiento);
				
			
						
			$oMutualTemporalAsientoRenglon->id = 0;
			$oMutualTemporalAsientoRenglon->saveAll($tmpCajaBanco);


			if($agrupar == 0):

				$queryImporte = "SELECT SUM(importe) as importe 
								FROM mutual_temporal_asiento_renglones
								WHERE mutual_asiento_id = 0
								GROUP BY mutual_asiento_id, co_plan_cuenta_id";
				$importeAsiento = $this->query($queryImporte);
					
				$debe = 0;
				$haber = 0;
				foreach($importeAsiento as $importe):
					if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
					else  $haber += $importe[0]['importe'];
				endforeach;
				
				$aMutualAsiento = array();
				$aMutualAsiento['MutualAsiento']['id'] = 0;
				$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
				$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
				$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
				$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
				$aMutualAsiento['MutualAsiento']['fecha'] = $cajaBanco['BancoCuentaMovimiento']['fecha_operacion'];
				$aMutualAsiento['MutualAsiento']['tipo_documento'] = '';
				$aMutualAsiento['MutualAsiento']['nro_documento'] = ($cajaBanco['BancoCuentaMovimiento']['tipo'] == 1 ? 'Nro.Cheque: ' . $cajaBanco['BancoCuentaMovimiento']['numero_operacion'] : '');
				$aMutualAsiento['MutualAsiento']['referencia'] = $cajaBanco['BancoConcepto']['concepto'] . ' (' . $cajaBanco['BancoCuentaMovimiento']['descripcion'] . ')';
				$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
				$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
				$aMutualAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
				$aMutualAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
				
				$this->grabarAsiento($aMutualAsiento, $procesoId);	
			endif;
		
	}
	
	
	function getAsientoCajaBancoReemplazar($cajaBanco, $procesoId, $agrupar=0){
		$oMutualTemporalAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		$cModulo = 'CABAREEM';
				
			$tmpCajaBanco = array();
			$aMovimiento = $this->getMovimientoRelacionado($cajaBanco['BancoCuentaMovimiento']['banco_cuenta_movimiento_id']);
			
			if($cajaBanco['BancoCuentaMovimiento']['banco_cuenta_id'] != $aMovimiento[0]['BancoCuentaMovimiento']['banco_cuenta_id']):
				$temporalAsiento = array();
				$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $aMovimiento[0]['BancoCuentaMovimiento']['fecha_operacion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cajaBanco[0]['co_plan_cuenta_id'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $cajaBanco['BancoCuentaMovimiento']['importe'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $cajaBanco['BancoCuentaMovimiento']['descripcion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
				if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
					$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDA LA CUENTA PARA BANCO NRO.: ' . $cajaBanco['BancoCuentaMovimiento']['id']; 
				endif;
				$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
				$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
				$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
				
				array_push($tmpCajaBanco, $temporalAsiento);
					
				$temporalAsiento = array();
				$temporalAsiento['MutualTemporalAsientoRenglon']['fecha'] = $aMovimiento[0]['BancoCuentaMovimiento']['fecha_operacion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $aMovimiento[0][0]['co_plan_cuenta_id'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['importe'] = $aMovimiento[0]['BancoCuentaMovimiento']['importe'] * (-1);
				$temporalAsiento['MutualTemporalAsientoRenglon']['referencia'] = $cajaBanco['BancoCuentaMovimiento']['descripcion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
				$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
				if(empty($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'])):
					$temporalAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
					$temporalAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDA LA CUENTA PARA BANCO NRO.: ' . $cajaBanco['BancoCuentaMovimiento']['id']; 
				endif;
				$cuenta = $this->getCuenta($temporalAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
				$temporalAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
				$temporalAsiento['MutualTemporalAsientoRenglon']['modulo'] = $cModulo;
				$temporalAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;
				
				array_push($tmpCajaBanco, $temporalAsiento);
					
				
							
				$oMutualTemporalAsientoRenglon->id = 0;
				$oMutualTemporalAsientoRenglon->saveAll($tmpCajaBanco);
	
	
				if($agrupar == 0):

					$queryImporte = "SELECT SUM(importe) as importe 
									FROM mutual_temporal_asiento_renglones
									WHERE mutual_asiento_id = 0
									GROUP BY mutual_asiento_id, co_plan_cuenta_id";
					$importeAsiento = $this->query($queryImporte);
						
					$debe = 0;
					$haber = 0;
					foreach($importeAsiento as $importe):
						if($importe[0]['importe'] > 0) $debe += $importe[0]['importe'];
						else  $haber += $importe[0]['importe'];
					endforeach;
						
					$aMutualAsiento = array();
					$aMutualAsiento['MutualAsiento']['id'] = 0;
					$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
					$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
					$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
					$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
					$aMutualAsiento['MutualAsiento']['fecha'] = $aMovimiento[0]['BancoCuentaMovimiento']['fecha_operacion'];
					$aMutualAsiento['MutualAsiento']['tipo_documento'] = '';
					$aMutualAsiento['MutualAsiento']['nro_documento'] = ($aMovimiento[0]['BancoCuentaMovimiento']['tipo'] == 1 ? 'Nro.Cheque: ' . $cajaBanco['BancoCuentaMovimiento']['numero_operacion'] : '');
					$aMutualAsiento['MutualAsiento']['referencia'] = $aMovimiento[0]['BancoConcepto']['concepto'] . ' (' . $aMovimiento[0]['BancoCuentaMovimiento']['descripcion'] . ')';
					$aMutualAsiento['MutualAsiento']['debe'] = $debe; // $importeAsiento[0][0]['debe'];
					$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1); // $importeAsiento[0][0]['haber'] * (-1);
					$aMutualAsiento['MutualAsiento']['modulo'] = $cModulo;
					$aMutualAsiento['MutualAsiento']['tipo_asiento'] = 0;
					
					$this->grabarAsiento($aMutualAsiento, $procesoId);	
				endif;
				
			endif;
		
	}
	
	
	
	
/*
 * PROCESOS VARIOS
 */
	function grabarAsiento($aMutualAsiento, $procesoId){
		$oMutualAsiento = $this->importarModelo('MutualAsiento', 'contabilidad');
		$oMutualAsientoRenglon = $this->importarModelo('MutualAsientoRenglon', 'contabilidad');
		
		$aMutualProcesoAsiento = array('MutualProcesoAsiento' => array('id' => $procesoId, 'error' => 0));
		$this->begin();
		if(!$oMutualAsiento->save($aMutualAsiento)):
			$this->rollback();
			$aMutualProcesoAsiento['MutualProcesoAsiento']['error'] = 1;
			$this->MutualProcesoAsiento->save($aMutualProcesoAsiento);
		else:
				
			$queryRenglon = "SELECT MutualAsientoRenglon.*, SUM(MutualAsientoRenglon.importe) as importe_asiento
							FROM mutual_temporal_asiento_renglones MutualAsientoRenglon WHERE mutual_asiento_id = 0
							GROUP BY MutualAsientoRenglon.mutual_asiento_id, MutualAsientoRenglon.co_plan_cuenta_id ORDER BY id, error DESC";
				
			$asientoRenglones = $this->query($queryRenglon);
				
			foreach($asientoRenglones as $asientoRenglon):
				$asientoRenglon['MutualAsientoRenglon']['mutual_proceso_asiento_id'] = $procesoId;
				$asientoRenglon['MutualAsientoRenglon']['mutual_asiento_id'] = $oMutualAsiento->id;
				$asientoRenglon['MutualAsientoRenglon']['debe'] = ($asientoRenglon[0]['importe_asiento'] > 0 ? $asientoRenglon[0]['importe_asiento'] : 0.00);
				$asientoRenglon['MutualAsientoRenglon']['haber'] = ($asientoRenglon[0]['importe_asiento'] < 0 ? $asientoRenglon[0]['importe_asiento'] * (-1) : 0.00);
				if(!$oMutualAsientoRenglon->save($asientoRenglon)):
					$this->rollback();
					$aMutualProcesoAsiento['MutualProcesoAsiento']['error'] = 1;
					$this->MutualProcesoAsiento->save($aMutualProcesoAsiento);
					break;
				endif;
			endforeach;
				
			$queryUpdate = "UPDATE mutual_temporal_asiento_renglones set mutual_asiento_id = " . $oMutualAsiento->id . " WHERE mutual_asiento_id = 0";
			$this->query($queryUpdate);
				
				
		endif;
			
		if($aMutualProcesoAsiento['MutualProcesoAsiento']['error'] == 0):
			$this->commit();
		endif;

		return true;
	}
	
	
	function grabarAsientoAgrupados($procesoId, $agrupar, $pid){
		$oMutualAsiento = $this->importarModelo('MutualAsiento', 'contabilidad');
		$oMutualAsientoRenglon = $this->importarModelo('MutualAsientoRenglon', 'contabilidad');
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 
		
		$this->Temporal->pid = $pid;


		$aMutualProcesoAsiento = array('MutualProcesoAsiento' => array('id' => $procesoId, 'error' => 0));
		
		$asinc->actualizar(0,100,"AGRUPANDO ASIENTO ...");
		
		$query = "
			SELECT MutualAsientoRenglon.*
			FROM mutual_temporal_asiento_renglones MutualAsientoRenglon 
			WHERE mutual_asiento_id = 0 
		";
		
		
		
		$order = "ORDER BY MutualAsientoRenglon.fecha, MutualAsientoRenglon.id";
		
		if($agrupar == 2):
			$order = "ORDER BY MutualAsientoRenglon.fecha, LEFT(MutualAsientoRenglon.modulo,8), MutualAsientoRenglon.id";
		endif;

		if($agrupar == 3):
			$order = "ORDER BY MutualAsientoRenglon.fecha, LEFT(MutualAsientoRenglon.modulo,8), tipo_asiento, MutualAsientoRenglon.id";
		endif;

		$query .= $order;
		$asientos = $this->query($query);

		$cntRenglones = count($asientos);
		$nFila = 0;
		$nIndice = 0;
		while($nIndice < $cntRenglones):
			$fecha_corte = $asientos[$nIndice]['MutualAsientoRenglon']['fecha'];
			$modulo = '';
			$tipo_asiento = 0;
						
			$update = array();
			$debe = 0;
			$haber = 0;

			// AGRUPAR POR FECHA
			if($agrupar == 1):
				while($nIndice < $cntRenglones && $asientos[$nIndice]['MutualAsientoRenglon']['fecha'] == $fecha_corte):
					array_push($update, $asientos[$nIndice]['MutualAsientoRenglon']['id']);
					
					if($asientos[$nIndice]['MutualAsientoRenglon']['importe'] > 0) $debe += $asientos[$nIndice]['MutualAsientoRenglon']['importe']; 
					else $haber += $asientos[$nIndice]['MutualAsientoRenglon']['importe']; 
	
					$nIndice += 1;
					$asinc->actualizar($nIndice, $cntRenglones,"AGRUPANDO ASIENTO NUMERO " . $nIndice);
				endwhile;
			endif;
					
			// AGRUPAR POR FECHA Y MODULO
			if($agrupar == 2):
				$modulo = substr($asientos[$nIndice]['MutualAsientoRenglon']['modulo'],0,8);
				
				while($nIndice < $cntRenglones && $asientos[$nIndice]['MutualAsientoRenglon']['fecha'] == $fecha_corte && 
				      substr($asientos[$nIndice]['MutualAsientoRenglon']['modulo'],0,8) == $modulo):
					array_push($update, $asientos[$nIndice]['MutualAsientoRenglon']['id']);
					
					if($asientos[$nIndice]['MutualAsientoRenglon']['importe'] > 0) $debe += $asientos[$nIndice]['MutualAsientoRenglon']['importe']; 
					else $haber += $asientos[$nIndice]['MutualAsientoRenglon']['importe']; 
	
					$nIndice += 1;
					$asinc->actualizar($nIndice, $cntRenglones,"AGRUPANDO ASIENTO NUMERO " . $nIndice);
				endwhile;
			endif;
					
			// AGRUPAR POR FECHA, MODULO Y TIPO DE ASIENTO.
			if($agrupar == 3):
				$modulo = substr($asientos[$nIndice]['MutualAsientoRenglon']['modulo'],0,8);
				$tipo_asiento = $asientos[$nIndice]['MutualAsientoRenglon']['tipo_asiento'];
				
				while($nIndice < $cntRenglones && $asientos[$nIndice]['MutualAsientoRenglon']['fecha'] == $fecha_corte&& 
				      substr($asientos[$nIndice]['MutualAsientoRenglon']['modulo'],0,8) == $modulo && 
				      $asientos[$nIndice]['MutualAsientoRenglon']['tipo_asiento'] == $tipo_asiento):
				
					array_push($update, array('id' => $asientos[$nIndice]['MutualAsientoRenglon']['id']));
					
					if($asientos[$nIndice]['MutualAsientoRenglon']['importe'] > 0) $debe += $asientos[$nIndice]['MutualAsientoRenglon']['importe']; 
					else $haber += $asientos[$nIndice]['MutualAsientoRenglon']['importe']; 
	
					$nIndice += 1;
					$asinc->actualizar($nIndice, $cntRenglones,"AGRUPANDO ASIENTO NUMERO " . $nIndice);
				endwhile;
			endif;

			
 
			$aMutualAsiento = array();
			$aMutualAsiento['MutualAsiento']['id'] = 0;
			$aMutualAsiento['MutualAsiento']['mutual_proceso_asiento_id'] = $procesoId;
			$aMutualAsiento['MutualAsiento']['co_asiento_id'] = 0;
			$aMutualAsiento['MutualAsiento']['nro_asiento'] = 0;
			$aMutualAsiento['MutualAsiento']['co_ejercicio_id'] = 0;
			$aMutualAsiento['MutualAsiento']['fecha'] = $fecha_corte;
			$aMutualAsiento['MutualAsiento']['tipo_documento'] = '';
			$aMutualAsiento['MutualAsiento']['nro_documento'] = '';
			$aMutualAsiento['MutualAsiento']['referencia'] = '';
			$aMutualAsiento['MutualAsiento']['debe'] = $debe;
			$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1);
			$aMutualAsiento['MutualAsiento']['modulo'] = $modulo;
			$aMutualAsiento['MutualAsiento']['tipo_asiento'] = $tipo_asiento;
			
			$implodeUpdate = implode(',', $update);
			
			
			$this->begin();
			if(!$oMutualAsiento->save($aMutualAsiento)):
				$this->rollback();
				$aMutualProcesoAsiento['MutualProcesoAsiento']['error'] = 1;
				$this->MutualProcesoAsiento->save($aMutualProcesoAsiento);
			else:
			
			
				$queryUpdate = "UPDATE mutual_temporal_asiento_renglones set mutual_asiento_id = " . $oMutualAsiento->id . " WHERE id IN( " . $implodeUpdate . ")";
				$this->query($queryUpdate);
				
				$queryRenglon = "SELECT MutualAsientoRenglon.*, SUM(MutualAsientoRenglon.importe) as importe_asiento
								FROM mutual_temporal_asiento_renglones MutualAsientoRenglon 
								WHERE id IN( " . $implodeUpdate . ")
								GROUP BY MutualAsientoRenglon.mutual_asiento_id, MutualAsientoRenglon.co_plan_cuenta_id ORDER BY error DESC";
					
				$asientoRenglones = $this->query($queryRenglon);
				$debe = 0;
				$haber = 0;
				
				foreach($asientoRenglones as $asientoRenglon):
					if($asientoRenglon[0]['importe_asiento'] > 0):
						$asientoRenglon['MutualAsientoRenglon']['id'] = 0;
						$asientoRenglon['MutualAsientoRenglon']['mutual_proceso_asiento_id'] = $procesoId;
						$asientoRenglon['MutualAsientoRenglon']['mutual_asiento_id'] = $oMutualAsiento->id;
						$asientoRenglon['MutualAsientoRenglon']['debe'] = $asientoRenglon[0]['importe_asiento'];
						$asientoRenglon['MutualAsientoRenglon']['haber'] = 0.00;
						$debe += $asientoRenglon[0]['importe_asiento'];
						if(!$oMutualAsientoRenglon->save($asientoRenglon)):
							$this->rollback();
							$aMutualProcesoAsiento['MutualProcesoAsiento']['error'] = 1;
							$this->MutualProcesoAsiento->save($aMutualProcesoAsiento);
							break;
						endif;
					endif;
				endforeach;
					
				foreach($asientoRenglones as $asientoRenglon):
					if($asientoRenglon[0]['importe_asiento'] < 0):
						$asientoRenglon['MutualAsientoRenglon']['id'] = 0;
						$asientoRenglon['MutualAsientoRenglon']['mutual_proceso_asiento_id'] = $procesoId;
						$asientoRenglon['MutualAsientoRenglon']['mutual_asiento_id'] = $oMutualAsiento->id;
						$asientoRenglon['MutualAsientoRenglon']['debe'] = 0.00;
						$asientoRenglon['MutualAsientoRenglon']['haber'] = ($asientoRenglon[0]['importe_asiento'] < 0 ? $asientoRenglon[0]['importe_asiento'] * (-1) : 0.00);
						$haber += $asientoRenglon[0]['importe_asiento'];
						if(!$oMutualAsientoRenglon->save($asientoRenglon)):
							$this->rollback();
							$aMutualProcesoAsiento['MutualProcesoAsiento']['error'] = 1;
							$this->MutualProcesoAsiento->save($aMutualProcesoAsiento);
							break;
						endif;
					endif;
				endforeach;
					
				$aMutualAsiento['MutualAsiento']['id'] = $oMutualAsiento->id;
				$aMutualAsiento['MutualAsiento']['debe'] = $debe;
				$aMutualAsiento['MutualAsiento']['haber'] = $haber * (-1);
				
				if(!$oMutualAsiento->save($aMutualAsiento)):
					$this->rollback();
					$aMutualProcesoAsiento['MutualProcesoAsiento']['error'] = 1;
					$this->MutualProcesoAsiento->save($aMutualProcesoAsiento);
				endif;
				
			endif;
			
			if($aMutualProcesoAsiento['MutualProcesoAsiento']['error'] == 0):
				$this->commit();
			endif;

		endwhile;
		
		
		return true;
	}
	
	
	function getUltimoAbierto(){
		$proceso = $this->find('all', array('conditions' => array('MutualProcesoAsiento.cerrado' => 0), 'limit' => 1));

		if(empty($proceso)) return $proceso;
				
		return $proceso[0];
	}
	
	
		
		
		
/*===================================================================================
DROP	TABLE temporal.productos
CREATE	TABLE temporal.productos
SELECT	IFNULL(tipo_orden_dto, '') AS tipo_orden_dto, IFNULL(tipo_producto, '') AS tipo_producto, IFNULL(tipo_cuota, '') AS tipo_cuota
FROM	orden_descuento_cuotas
GROUP	BY tipo_orden_dto, tipo_producto, tipo_cuota


SELECT	0 AS id, prod.*, IFNULL(gd.concepto_1, '') AS concepto_producto , IFNULL(gdc.concepto_1, '') AS concepto_cuota
FROM	temporal.productos prod 
LEFT	JOIN global_datos gd
ON	prod.tipo_producto = gd.id
LEFT	JOIN global_datos gdc
ON	prod.tipo_cuota = gdc.id



SELECT	*
FROM	temporal.productos



SELECT	od.tipo_orden_dto, od.tipo_producto, odc.*
FROM	orden_descuento_cuotas odc
INNER	JOIN orden_descuentos od
ON	odc.orden_descuento_id = od.id
WHERE	tipo_cuota IS NULL



SELECT	*
FROM	proveedor_facturas
WHERE	liquidacion_id > 0
		
/*===================================================================================
############## ESTO ESTA OK #########################################################
DROP	TABLE temporal.productos
CREATE	TABLE mutual_cuenta_asientos
SELECT	IFNULL(tipo_orden_dto, '') AS tipo_orden_dto, IFNULL(tipo_producto, '') AS tipo_producto, IFNULL(tipo_cuota, '') AS tipo_cuota, IFNULL(gd.concepto_1, '') AS concepto_producto , IFNULL(gdc.concepto_1, '') AS concepto_cuota
FROM	orden_descuento_cuotas prod
LEFT	JOIN global_datos gd
ON	prod.tipo_producto = gd.id
LEFT	JOIN global_datos gdc
ON	prod.tipo_cuota = gdc.id
GROUP	BY tipo_orden_dto, tipo_producto, tipo_cuota
ORDER	BY tipo_orden_dto, tipo_producto, tipo_cuota
/*===================================================================================




SELECT	*
FROM	orden_descuento_cobro_cuotas
WHERE	orden_descuento_cobro_id = 373473


SELECT	OrdenDescuentoCuota.*
FROM	orden_descuento_cuotas OrdenDescuentoCuota
INNER	JOIN orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
ON	OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id
WHERE	OrdenDescuentoCobroCuota.orden_descuento_cobro_id = 184526
-- WHERE	OrdenDescuentoCuota.orden_descuento_id = 86635 AND OrdenDescuentoCobroCuota.orden_descuento_cobro_id = 373333
GROUP	BY OrdenDescuentoCuota.orden_descuento_id, OrdenDescuentoCuota.tipo_orden_dto, OrdenDescuentoCuota.tipo_producto, OrdenDescuentoCuota.tipo_cuota



SELECT	SUM(OrdenDescuentoCobroCuota.importe) AS importe_cobro, OrdenDescuentoCuota.*
FROM	orden_descuento_cuotas OrdenDescuentoCuota
INNER	JOIN orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
ON	OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id
WHERE	OrdenDescuentoCobroCuota.orden_descuento_cobro_id = 184526 -- 373473
-- WHERE	OrdenDescuentoCuota.orden_descuento_id = 38829 AND OrdenDescuentoCobroCuota.orden_descuento_cobro_id = 184526
GROUP	BY OrdenDescuentoCuota.orden_descuento_id, OrdenDescuentoCuota.tipo_orden_dto, OrdenDescuentoCuota.tipo_producto, OrdenDescuentoCuota.tipo_cuota, OrdenDescuentoCuota.proveedor_id

383659

SELECT	*
FROM	orden_descuento_cobros
WHERE	fecha >= '2012-01-01' AND cancelacion_orden_id = 0 AND recibo_id > 0
ORDER	BY importe DESC




SELECT	COUNT(*), odcc.*
FROM	orden_descuento_cobro_cuotas odcc
WHERE	orden_descuento_cobro_id IN(
SELECT	id
FROM	orden_descuento_cobros
WHERE	fecha >= '2012-01-01' AND cancelacion_orden_id = 0 AND recibo_id > 0)
GROUP	BY orden_descuento_cobro_id




SELECT	*
FROM	global_datos
WHERE	id LIKE 'RECAR%'
ORDER	BY id
LIMIT	10000


CREATE	TABLE temporal.productos
SELECT	tipo_producto AS id, tipo_cuota AS concepto_1
FROM	orden_descuento_cuotas
GROUP	BY tipo_orden_dto, tipo_producto, tipo_cuota



SELECT	*
FROM	orden_descuento_cobro_cuotas
WHERE	orden_descuento_cuota_id IN(2173001,2173194,2183465)
SELECT	id
FROM	orden_descuento_cuotas
WHERE	tipo_cuota IN('MUTUTCUOGGMO', 'MUTUTCUOGAGE'))



# LEO ESTO PRIMERO #
====================
SELECT 	OrdenDescuentoCobro.id, OrdenDescuentoCobro.recibo_id, OrdenDescuentoCobro.importe, OrdenDescuentoCobro.proveedor_origen_fondo_id, 
		OrdenDescuentoCobro.orden_pago_id, OrdenCajaCobro.banco_cuenta_movimiento_id, OrdenCajaCobro.proveedor_factura_id, 
		OrdenCajaCobro.importe_contado, OrdenCajaCobro.importe_orden_pago
FROM 	orden_descuento_cobros OrdenDescuentoCobro
INNER 	JOIN orden_caja_cobros OrdenCajaCobro
ON 		OrdenCajaCobro.orden_descuento_cobro_id = OrdenDescuentoCobro.id
WHERE	OrdenDescuentoCobro.cancelacion_orden_id = 0 AND OrdenDescuentoCobro.fecha > '$fecha_desde' AND 
		OrdenDescuentoCobro.fecha <= '$fecha_hasta' AND OrdenDescuentoCobro.anulado = 0



SELECT OrdenDescuentoCuota.tipo_orden_dto, OrdenDescuentoCuota.tipo_producto, OrdenDescuentoCuota.tipo_cuota, OrdenDescuentoCuota.proveedor_id, SUM(OrdenDescuentoCobroCuota.importe),
	 MutualCuentaAsiento.co_plan_cuenta_id
FROM	orden_descuento_cuotas OrdenDescuentoCuota
INNER JOIN orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
ON OrdenDescuentoCobroCuota.orden_descuento_cuota_id = OrdenDescuentoCuota.id
INNER JOIN mutual_cuenta_asientos MutualCuentaAsiento
ON CONCAT(MutualCuentaAsiento.tipo_orden_dto, MutualCuentaAsiento.tipo_producto, MutualCuentaAsiento.tipo_cuota) = CONCAT(OrdenDescuentoCuota.tipo_orden_dto, OrdenDescuentoCuota.tipo_producto, OrdenDescuentoCuota.tipo_cuota)
WHERE OrdenDescuentoCobroCuota.orden_descuento_cobro_id = '373518'
GROUP BY OrdenDescuentoCuota.tipo_orden_dto, OrdenDescuentoCuota.tipo_producto, OrdenDescuentoCuota.tipo_cuota, OrdenDescuentoCuota.proveedor_id






===================================================================================
SELECT	CancelacionOrden.proveedor_factura_id, CancelacionOrden.credito_proveedor_factura_id, ProveedorFactura.id, CancelacionOrden.recibo_id, CancelacionOrden.orden_pago_id, CancelacionOrden.*
FROM	cancelacion_ordenes CancelacionOrden
LEFT	JOIN proveedor_facturas ProveedorFactura
ON	ProveedorFactura.cancelacion_orden_id = CancelacionOrden.id
WHERE	fecha_imputacion > '2011-12-31' AND CancelacionOrden.estado = 'P' AND importe_proveedor > 0 AND CancelacionOrden.credito_proveedor_factura_id > 0
LIMIT 10000



SELECT	CancelacionOrden.proveedor_factura_id, CancelacionOrden.credito_proveedor_factura_id, ProveedorFactura.id, CancelacionOrden.recibo_id, CancelacionOrden.orden_pago_id, CancelacionOrden.*
FROM	cancelacion_ordenes CancelacionOrden
LEFT	JOIN proveedor_facturas ProveedorFactura
ON	ProveedorFactura.cancelacion_orden_id = CancelacionOrden.id
WHERE	fecha_imputacion > '2011-12-31' and orden_proveedor_id != 18 AND CancelacionOrden.estado = 'P' AND importe_proveedor > 0 AND CancelacionOrden.credito_proveedor_factura_id > 0
LIMIT 10000





===================================================================================
REVERSO
===================================================================================
SELECT	
	OrdenDescuentoCobroCuota.fecha_reverso, OrdenDescuentoCobro.proveedor_origen_fondo_id, PersonaBeneficio.codigo_beneficio, 
	SUM(OrdenDescuentoCobroCuota.importe_reversado) AS importe_reversado, OrdenDescuentoCuota.*
FROM	
	orden_descuento_cuotas OrdenDescuentoCuota 
INNER JOIN 
	orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
ON	
	OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id
INNER JOIN 
	orden_descuento_cobros OrdenDescuentoCobro
ON	
	OrdenDescuentoCobro.id = OrdenDescuentoCobroCuota.orden_descuento_cobro_id
INNER JOIN 
	persona_beneficios PersonaBeneficio
ON	
	PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id
WHERE	
	OrdenDescuentoCobroCuota.fecha_reverso > '2012-01-16' AND OrdenDescuentoCobroCuota.fecha_reverso <= '2012-02-03'
GROUP BY 
	OrdenDescuentoCobroCuota.fecha_reverso, PersonaBeneficio.codigo_beneficio, OrdenDescuentoCuota.tipo_orden_dto, 
	OrdenDescuentoCuota.tipo_producto, OrdenDescuentoCuota.tipo_cuota
ORDER BY 
	OrdenDescuentoCobroCuota.fecha_reverso, PersonaBeneficio.codigo_beneficio
	
	
	
	
===================================================================================
REVERSO BANCO
===================================================================================
SELECT	BancoCuentaMovimiento.banco_cuenta_id AS banco_cuenta_id, BancoCuentaMovimiento.importe AS importe, BancoCuenta.co_plan_cuenta_id AS co_plan_cuenta_id, 
	OrdenDescuentoCobroCuota.fecha_reverso AS fecha_reverso, PersonaBeneficio.codigo_beneficio AS codigo_beneficio, 
	SUM(OrdenDescuentoCobroCuota.importe_reversado) AS importe_reversado, OrdenDescuentoCobroCuota.banco_cuenta_movimiento_id AS banco_cuenta_movimiento_id -- , OrdenDescuentoCuota.*
FROM	
	orden_descuento_cuotas OrdenDescuentoCuota 
INNER JOIN 
	orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
ON	
	OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id
INNER JOIN 
	orden_descuento_cobros OrdenDescuentoCobro
ON	
	OrdenDescuentoCobro.id = OrdenDescuentoCobroCuota.orden_descuento_cobro_id
INNER JOIN
	banco_cuenta_movimientos BancoCuentaMovimiento
ON
	BancoCuentaMovimiento.id = OrdenDescuentoCobroCuota.banco_cuenta_movimiento_id
INNER JOIN
	banco_cuentas BancoCuenta
ON
	BancoCuenta.id = BancoCuentaMovimiento.banco_cuenta_id
INNER JOIN 
	persona_beneficios PersonaBeneficio
ON	
	PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id
WHERE	
	OrdenDescuentoCobroCuota.fecha_reverso > '2011-12-31' AND OrdenDescuentoCobroCuota.fecha_reverso <= '2012-07-27'
GROUP BY 
	fecha_reverso, banco_cuenta_movimiento_id, codigo_beneficio

UNION

(
SELECT	BancoCuentaMovimiento.banco_cuenta_id AS banco_cuenta_id, BancoCuentaMovimiento.importe AS importe, BancoCuenta.co_plan_cuenta_id AS co_plan_cuenta_id, 
	SocioReintegro.fecha_reverso AS fecha_reverso, PersonaBeneficio.codigo_beneficio AS codigo_beneficio, 
	SUM(SocioReintegro.importe_reversado) AS importe_reversado, SocioReintegro.banco_cuenta_movimiento_id AS banco_cuenta_movimiento_id
FROM	
	socio_reintegros SocioReintegro
INNER JOIN banco_cuenta_movimientos BancoCuentaMovimiento
ON	BancoCuentaMovimiento.id = SocioReintegro.banco_cuenta_movimiento_id
INNER JOIN banco_cuentas BancoCuenta
ON	BancoCuenta.id = BancoCuentaMovimiento.banco_cuenta_id
INNER JOIN socios Socio
ON	Socio.id = SocioReintegro.socio_id
INNER JOIN persona_beneficios PersonaBeneficio
ON	PersonaBeneficio.id = Socio.persona_beneficio_id
WHERE	SocioReintegro.fecha_reverso > '2011-12-31'
GROUP BY 
	fecha_reverso, banco_cuenta_movimiento_id, codigo_beneficio)
ORDER BY 
	fecha_reverso, banco_cuenta_movimiento_id, codigo_beneficio
	
===================================================================================*/
	

	
/*========================================================
SELECT MutualAsientoRenglon.*, SUM(MutualAsientoRenglon.importe) AS importe_asiento
FROM mutual_temporal_asiento_renglones MutualAsientoRenglon -- WHERE mutual_asiento_id = 0
GROUP BY MutualAsientoRenglon.fecha, MutualAsientoRenglon.co_plan_cuenta_id 
ORDER BY MutualAsientoRenglon.fecha, MutualAsientoRenglon.importe DESC



SELECT MutualAsientoRenglon.*, SUM(MutualAsientoRenglon.importe) AS importe_asiento
FROM mutual_temporal_asiento_renglones MutualAsientoRenglon -- WHERE mutual_asiento_id = 0
GROUP BY MutualAsientoRenglon.fecha, LEFT(MutualAsientoRenglon.modulo,8), MutualAsientoRenglon.co_plan_cuenta_id 
ORDER BY MutualAsientoRenglon.fecha, LEFT(MutualAsientoRenglon.modulo,8), MutualAsientoRenglon.importe DESC



SELECT MutualAsientoRenglon.*, SUM(MutualAsientoRenglon.importe) AS importe_asiento
FROM mutual_temporal_asiento_renglones MutualAsientoRenglon -- WHERE mutual_asiento_id = 0
GROUP BY MutualAsientoRenglon.fecha, LEFT(MutualAsientoRenglon.modulo,8), tipo_asiento, MutualAsientoRenglon.co_plan_cuenta_id 
ORDER BY MutualAsientoRenglon.fecha, LEFT(MutualAsientoRenglon.modulo,8), tipo_asiento, MutualAsientoRenglon.importe DESC


=============================================================*/	
	
	
	function getCuenta($id){
		$oPlanCuenta = $this->importarModelo('PlanCuenta', 'contabilidad');
		$oEjercicio = $this->importarModelo('Ejercicio', 'contabilidad');
		
		$planCuenta = $oPlanCuenta->read(null, $id);
		$ejercicio = $oPlanCuenta->traeEjercicio($planCuenta['PlanCuenta']['co_ejercicio_id']);

		$cuenta = array();
		$cuenta['cuenta'] = $oPlanCuenta->formato_cuenta($planCuenta['PlanCuenta']['cuenta'], $ejercicio);
		$cuenta['descripcion'] = $planCuenta['PlanCuenta']['descripcion'];
		
		return $cuenta;
	}
	
	
	function getAsientos($id, $limit=0, $offset=0){
		$oMutualAsiento = $this->importarModelo('MutualAsiento', 'contabilidad');
		$oMutualAsientoRenglon = $this->importarModelo('MutualAsientoRenglon', 'contabilidad');
		
		$oMutualAsiento->bindModel(array(
		            'hasMany'=>array(
		                'MutualAsientoRenglon'=>array(
		                    'className'=>'MutualAsientoRenglon',
		                    'foreignKey'=>'mutual_asiento_id'
		                )
		            )
		        ));

		
		if($limit == 0 && $offset == 0):
			$asientos = $oMutualAsiento->find('all', array('conditions' => array('MutualAsiento.mutual_proceso_asiento_id' => $id), 'order' => array('MutualAsiento.fecha')));
		else:
			$asientos = $oMutualAsiento->find('all', array('conditions' => array('MutualAsiento.mutual_proceso_asiento_id' => $id), 'order' => array('MutualAsiento.fecha'), 'limit' => $limit, 'offset' => $offset));
		endif;	

//		$returnAsientos = array();
//		foreach($asientos as $asiento):
//			$asiento['MutualAsiento']['renglon'] = array();
//			$renglones = $oMutualAsientoRenglon->find('all', array('conditions' => array('MutualAsientoRenglon.mutual_asiento_id' => $asiento['MutualAsiento']['id']), 'order' => array('MutualAsientoRenglon.debe DESC', 'MutualAsientoRenglon.haber')));
//			foreach($renglones as $renglon):
//
//				array_push($asiento['MutualAsiento']['renglon'], $renglon['MutualAsientoRenglon']);
//
//			endforeach;
//			array_push($returnAsientos, $asiento);
//
//		endforeach;

		return $asientos;
	}
	
	
	function cerrar_proceso_asientos($id){
		$oMutualAsiento = $this->importarModelo('MutualAsiento', 'contabilidad');
		$oMutualAsientoRenglon = $this->importarModelo('MutualAsientoRenglon', 'contabilidad');
		$oPlanCuenta = $this->importarModelo('PlanCuenta', 'contabilidad');
		
		$asientos = $oMutualAsiento->find('all', array('conditions' => array('MutualAsiento.mutual_proceso_asiento_id' => $id), 'order' => array('MutualAsiento.fecha')));
		
		$ejercicioVigente = $this->getGlobalDato('entero_1', 'CONTEVIG');
		$ejercicio = $oPlanCuenta->traeEjercicio($ejercicioVigente);
		foreach($asientos as $asiento):
			
		endforeach;

		return true;
		
	}
	
	
	function getAsiento($id){
		$oMutualAsiento = $this->importarModelo('MutualAsiento', 'contabilidad');
		$oMutualAsientoRenglon = $this->importarModelo('MutualTemporalAsientoRenglon', 'contabilidad');
		
		$asientos = $oMutualAsiento->find('all', array('conditions' => array('MutualAsiento.id' => $id)));
		
		$returnAsientos = array();
		foreach($asientos as $asiento):
			$asiento['MutualAsiento']['renglon'] = array();
			$renglones = $oMutualAsientoRenglon->find('all', array('conditions' => array('MutualTemporalAsientoRenglon.mutual_asiento_id' => $asiento['MutualAsiento']['id']), 'order' => array('MutualTemporalAsientoRenglon.importe DESC')));
			foreach($renglones as $renglon):

				array_push($asiento['MutualAsiento']['renglon'], $renglon['MutualTemporalAsientoRenglon']);

			endforeach;
			array_push($returnAsientos, $asiento);

		endforeach;

		return $returnAsientos;
	}
	
	
	function getMayoriza($id, $consolidado = 0){
		$oPlanCuenta = $this->importarModelo('PlanCuenta', 'contabilidad');
		$oAsientoRenglon = $this->importarModelo('AsientoRenglon', 'contabilidad');
		$oMutualProcesoAsiento = $this->importarModelo('MutualProcesoAsiento', 'contabilidad');
		$oMutualAsiento = $this->importarModelo('MutualAsiento', 'contabilidad');
		$oMutualAsientoRenglon = $this->importarModelo('MutualAsientoRenglon', 'contabilidad');
		
		$aProcesoAsiento = $oMutualProcesoAsiento->read(null, $id);		
		$ejercicio = $oPlanCuenta->traeEjercicio($aProcesoAsiento['MutualProcesoAsiento']['co_ejercicio_id']);
		
		$aPlanCuenta = $oPlanCuenta->find('all', array('conditions' => array('PlanCuenta.co_ejercicio_id' => $aProcesoAsiento['MutualProcesoAsiento']['co_ejercicio_id']), 'fields' => array(), 'order' => array('PlanCuenta.cuenta')));

		$fecha_desde = $aProcesoAsiento['MutualProcesoAsiento']['fecha_desde'];
		$fecha_hasta = $aProcesoAsiento['MutualProcesoAsiento']['fecha_hasta'];
		$ejercicio_id = $aProcesoAsiento['MutualProcesoAsiento']['co_ejercicio_id'];
		
		$aTmpMayor = array();
		$aCnsMayor = array();
		$aMayor = array();
		foreach($aPlanCuenta as $planCuenta):
		
// 			$aTmpMayor = $oMutualAsientoRenglon->find('all',array('conditions' => array('MutualAsientoRenglon.mutual_proceso_asiento_id' => $id),'fields' => array('MutualAsientoRenglon.co_plan_cuenta_id', 'REPLACE(MutualAsientoRenglon.cuenta,".","")', 'MutualAsientoRenglon.descripcion', 'SUM(MutualAsientoRenglon.debe) as debe_mayor', 'SUM(MutualAsientoRenglon.haber) as haber_mayor'),'group' => array('MutualAsientoRenglon.co_plan_cuenta_id'),'order' => array("MutualAsientoRenglon.cuenta")));
			$aTmpMayor = $oMutualAsientoRenglon->find('all',array('conditions' => array('MutualAsientoRenglon.mutual_proceso_asiento_id' => $id, 'MutualAsientoRenglon.co_plan_cuenta_id' => $planCuenta['PlanCuenta']['id']),'fields' => array('MutualAsientoRenglon.co_plan_cuenta_id', 'MutualAsientoRenglon.cuenta', 'MutualAsientoRenglon.descripcion', 'SUM(MutualAsientoRenglon.debe) as debe_mayor', 'SUM(MutualAsientoRenglon.haber) as haber_mayor'),'group' => array('MutualAsientoRenglon.co_plan_cuenta_id'),'order' => array("MutualAsientoRenglon.cuenta")));
		
			if($consolidado == 1):
				$plan_id = $planCuenta['PlanCuenta']['id'];
				$sql = "SELECT	SUM(AsientoRenglon.debe) AS debe_mayor, SUM(AsientoRenglon.haber) AS haber_mayor
						FROM	co_asiento_renglones AsientoRenglon
						INNER JOIN co_asientos Asiento
						ON Asiento.id = AsientoRenglon.co_asiento_id
						WHERE Asiento.co_ejercicio_id = '$ejercicio_id' AND Asiento.borrado = 0 AND Asiento.fecha > '$fecha_desde' AND Asiento.fecha <= '$fecha_hasta' AND AsientoRenglon.co_plan_cuenta_id = '$plan_id'
						GROUP BY AsientoRenglon.co_plan_cuenta_id
			
				";
				
				$aCnsMayor = $this->query($sql);
			
			endif;
			
			$aTmpPlanCuenta = array();
			$aTmpPlanCuenta['MutualAsientoRenglon']['co_plan_cuenta_id'] = $planCuenta['PlanCuenta']['id'];
			$aTmpPlanCuenta['MutualAsientoRenglon']['cuenta'] = $oPlanCuenta->formato_cuenta($planCuenta['PlanCuenta']['cuenta'], $ejercicio);
			$aTmpPlanCuenta['MutualAsientoRenglon']['descripcion'] = $planCuenta['PlanCuenta']['descripcion'];
			$aTmpPlanCuenta[0]['debe_mayor'] = 0;
			$aTmpPlanCuenta[0]['haber_mayor'] = 0;
			
			if(!empty($aTmpMayor)):
				$aTmpPlanCuenta[0]['debe_mayor'] += $aTmpMayor[0][0]['debe_mayor'];
				$aTmpPlanCuenta[0]['haber_mayor'] += $aTmpMayor[0][0]['haber_mayor'];
			endif;
			
			if(!empty($aCnsMayor)):
				$aTmpPlanCuenta[0]['debe_mayor'] += $aCnsMayor[0][0]['debe_mayor'];
				$aTmpPlanCuenta[0]['haber_mayor'] += $aCnsMayor[0][0]['haber_mayor'];
			endif;
			
			if($aTmpPlanCuenta[0]['debe_mayor'] > 0 || $aTmpPlanCuenta[0]['haber_mayor'] > 0):
				array_push($aMayor, $aTmpPlanCuenta);
			endif;
		endforeach;
		
		return $aMayor;
	}
	
	
	function getCuentaMayor($cuenta){
		$oMutualAsientoRenglon = $this->importarModelo('MutualAsientoRenglon', 'contabilidad');
		$aCuentaMayor = $oMutualAsientoRenglon->find('all',array('conditions' => array('MutualAsientoRenglon.co_plan_cuenta_id' => $cuenta),'fields' => array('MutualAsientoRenglon.mutual_asiento_id', 'MutualAsientoRenglon.fecha', 'MutualAsientoRenglon.referencia', 'MutualAsientoRenglon.debe', 'MutualAsientoRenglon.haber'),'order' => array("MutualAsientoRenglon.fecha")));

		return $aCuentaMayor;
		
	}
	
	
	function getMayorDetalle($id, $cuentaId = null, $consolidado = 0){
		$oMutualAsientoRenglon = $this->importarModelo('MutualAsientoRenglon', 'contabilidad');
		$oPlanCuenta = $this->importarModelo('PlanCuenta', 'contabilidad');
		$oMutualProcesoAsiento = $this->importarModelo('MutualProcesoAsiento', 'contabilidad');
		
		
		if($consolidado == 0):
			if(empty($cuentaId)) $conditions = array('MutualAsientoRenglon.mutual_proceso_asiento_id' => $id);
			else $conditions = array('MutualAsientoRenglon.mutual_proceso_asiento_id' => $id, 'MutualAsientoRenglon.co_plan_cuenta_id' => $cuentaId);
			
			$aMayorDetalle = $oMutualAsientoRenglon->find('all',array('conditions' => $conditions,'order' => array("MutualAsientoRenglon.cuenta", "MutualAsientoRenglon.fecha", "MutualAsientoRenglon.mutual_asiento_id")));
		else:
			$aProcesoAsiento = $oMutualProcesoAsiento->read(null, $id);
			$ejercicio = $oPlanCuenta->traeEjercicio($aProcesoAsiento['MutualProcesoAsiento']['co_ejercicio_id']);

			$fecha_desde = $aProcesoAsiento['MutualProcesoAsiento']['fecha_desde'];
			$fecha_hasta = $aProcesoAsiento['MutualProcesoAsiento']['fecha_hasta'];
			$ejercicio_id = $aProcesoAsiento['MutualProcesoAsiento']['co_ejercicio_id'];
			
			$sql = "
					SELECT 
					MutualAsientoRenglon.id AS id,
					MutualAsientoRenglon.mutual_asiento_id AS mutual_asiento_id,
					MutualAsientoRenglon.mutual_proceso_asiento_id AS mutual_proceso_asiento_id,
					MutualAsientoRenglon.fecha AS fecha,
					MutualAsientoRenglon.co_plan_cuenta_id AS co_plan_cuenta_id,
					REPLACE(MutualAsientoRenglon.cuenta,'.','') AS cuenta,
					MutualAsientoRenglon.descripcion AS descripcion,
					MutualAsientoRenglon.referencia AS referencia,
					MutualAsientoRenglon.debe AS debe,
					MutualAsientoRenglon.haber AS haber,
					MutualAsientoRenglon.error AS error,
					MutualAsientoRenglon.error_descripcion AS error_descripcion,
					MutualAsientoRenglon.modulo AS modulo,
					MutualAsientoRenglon.tipo_asiento AS tipo_asiento
					
					FROM mutual_asiento_renglones MutualAsientoRenglon
					WHERE MutualAsientoRenglon.mutual_proceso_asiento_id = '$id' "; 
					
					if(!empty($cuentaId)) $sql .= "AND MutualAsientoRenglon.co_plan_cuenta_id = '$cuentaId'";
					
					$sql .= " UNION
					
					SELECT 
					AsientoRenglon.id AS id, Asiento.nro_asiento AS mutual_asiento_id, Asiento.co_ejercicio_id AS mutual_proceso_asiento_id, Asiento.fecha AS fecha, 
					REPLACE(AsientoRenglon.co_plan_cuenta_id,'.','') AS co_plan_cuenta_id, PlanCuenta.cuenta AS cuenta,
					PlanCuenta.descripcion AS descripcion, Asiento.referencia AS referencia, AsientoRenglon.debe AS debe, AsientoRenglon.haber AS haber, 0 AS error, 
					'' AS error_descripcion, '' AS modulo, 0 AS tipo_asiento
					FROM co_asiento_renglones AsientoRenglon
					INNER JOIN co_asientos Asiento
					ON Asiento.id = AsientoRenglon.co_asiento_id
					INNER JOIN co_plan_cuentas PlanCuenta
					ON PlanCuenta.id = AsientoRenglon.co_plan_cuenta_id
					WHERE Asiento.borrado = 0 AND Asiento.co_ejercicio_id = '$ejercicio_id' AND Asiento.fecha > '$fecha_desde' AND Asiento.fecha <= '$fecha_hasta' "; 
					
					if(!empty($cuentaId)) $sql .= "AND AsientoRenglon.co_plan_cuenta_id = '$cuentaId'";

					$sql .= " ORDER BY cuenta, fecha, mutual_asiento_id
			";
		
			$aTmpMayor = $this->query($sql);
			
			$aMayorDetalle = array();
			foreach($aTmpMayor as $tmpMayor):
				$aTmpDetalle = array();
				$aTmpDetalle['MutualAsientoRenglon']['id'] = $tmpMayor[0]['id']; 
				$aTmpDetalle['MutualAsientoRenglon']['mutual_asiento_id'] = $tmpMayor[0]['mutual_asiento_id']; 
				$aTmpDetalle['MutualAsientoRenglon']['mutual_proceso_asiento_id'] = $tmpMayor[0]['mutual_proceso_asiento_id'];
				$aTmpDetalle['MutualAsientoRenglon']['fecha'] = $tmpMayor[0]['fecha'];
				$aTmpDetalle['MutualAsientoRenglon']['co_plan_cuenta_id'] = $tmpMayor[0]['co_plan_cuenta_id'];
				$aTmpDetalle['MutualAsientoRenglon']['cuenta'] = $oPlanCuenta->formato_cuenta($tmpMayor[0]['cuenta'], $ejercicio);
				$aTmpDetalle['MutualAsientoRenglon']['descripcion'] = $tmpMayor[0]['descripcion'];
				$aTmpDetalle['MutualAsientoRenglon']['referencia'] = $tmpMayor[0]['referencia'];
				$aTmpDetalle['MutualAsientoRenglon']['debe'] = $tmpMayor[0]['debe'];
				$aTmpDetalle['MutualAsientoRenglon']['haber'] = $tmpMayor[0]['haber'];
				$aTmpDetalle['MutualAsientoRenglon']['error'] = $tmpMayor[0]['error'];
				$aTmpDetalle['MutualAsientoRenglon']['error_descripcion'] = $tmpMayor[0]['error_descripcion'];
				$aTmpDetalle['MutualAsientoRenglon']['modulo'] = $tmpMayor[0]['modulo'];
				$aTmpDetalle['MutualAsientoRenglon']['tipo_asiento'] = $tmpMayor[0]['tipo_asiento'];
				
				array_push($aMayorDetalle, $aTmpDetalle);
			endforeach;
			
		endif;
		
		return $aMayorDetalle;
	}
	
	
}
?>

