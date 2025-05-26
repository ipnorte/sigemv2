<?php
class TmpReciboDetalle extends ClientesAppModel{
	var $name = 'TmpReciboDetalle';
	
	function getReciboDetalle($id=null){
		$aReciboDetalle = array();

		if(empty($id)) return $aReciboDetalle;
		
		$aReciboDetalle = $this->find('all',array('conditions' => array('ReciboDetalle.recibo_id' => $id)));
		
		$aReciboDetalle = Set::extract("{n}.ReciboDetalle",$aReciboDetalle);
		return $aReciboDetalle;
	}    
    
	function getCobroDetalleCuenta($cliente_id){
		$anticipos = $this->find('all',array('conditions' => array('ReciboDetalle.cliente_id' => $cliente_id, 'ReciboDetalle.tipo_cobro' => 'AN', 'importe > ' => 0)));

		if(empty($anticipos)) return array();
		
		$anticipos = Set::extract("{n}.ReciboDetalle",$anticipos);
		return $anticipos;
		
	}


//	function grabarReciboDetalleCancelacion($datos){
//		$aTmpDetalle = array();
//		$aReciboDetalle = array();
//		
//		// Orden Descuento Cobros
//		$oOrdenDescuentoCobros = $this->importarModelo('OrdenDescuentoCobro', 'mutual');
//		$aCobro = $oOrdenDescuentoCobros->getCobro($datos['CancelacionOrden']['orden_descuento_cobro_id']);
//
//		// Establezco de donde es el Ingreso
//		$aOrigen = array();
//		$aOrigen['persona_id'] = (isset($datos['CancelacionOrden']['cabecera_persona_id']) ? $datos['CancelacionOrden']['cabecera_persona_id'] : 0);
//		$aOrigen['socio_id']   = (isset($datos['CancelacionOrden']['cabecera_socio_id'])   ? $datos['CancelacionOrden']['cabecera_socio_id']   : 0);
//		$aOrigen['cliente_id'] = (isset($datos['CancelacionOrden']['cabecera_cliente_id']) ? $datos['CancelacionOrden']['cabecera_cliente_id'] : 0);
//		$aOrigen['banco_id']   = (isset($datos['CancelacionOrden']['cabecera_banco_id'])   ? $datos['CancelacionOrden']['cabecera_banco_id']   : null);
//		$aOrigen['codigo_organismo']  = (isset($datos['CancelacionOrden']['cabecera_codigo_organismo'])  ? $datos['CancelacionOrden']['cabecera_codigo_organismo'] : null);
//
//		foreach($aCobro['OrdenDescuentoCobroCuota'] as $aOrdenDescuentoCobroCuota):
//			$aTmpDetalle['id'] = 0;
//			$aTmpDetalle['persona_id'] = $aOrigen['persona_id'];
//			$aTmpDetalle['socio_id'] = $aOrigen['socio_id'];
//			$aTmpDetalle['cliente_id'] = $aOrigen['cliente_id'];
//			$aTmpDetalle['banco_id'] = $aOrigen['banco_id'];
//			$aTmpDetalle['codigo_organismo'] = $aOrigen['codigo_organismo'];
//			$aTmpDetalle['recibo_id'] = $datos['CancelacionOrden']['recibo_id'];
//			$aTmpDetalle['tipo_cobro'] = 'FA';
//			$aTmpDetalle['orden_descuento_cobro_cuota_id'] = $aOrdenDescuentoCobroCuota['id'];
//			$aTmpDetalle['importe'] = $aOrdenDescuentoCobroCuota['importe'];
//			array_push($aReciboDetalle, $aTmpDetalle);
//		endforeach;
//
//		if(!$this->saveAll($aReciboDetalle)):		
//			return false;
//		endif;
//
//		return true;
//	}


	function grabarReciboDetalleCancelacionOld($orden){
		$aTmpDetalle = array();
		$aReciboDetalle = array();
		
		// Establezco de donde es el Ingreso
		$aOrigen = array();
		$aOrigen['persona_id'] = 0;
		$aOrigen['socio_id']   = $orden['CancelacionOrden']['socio_id'];
		$aOrigen['cliente_id'] = 0;
		$aOrigen['banco_id']   = 0;
		$aOrigen['codigo_organismo']  = null;

		$aTmpDetalle['id'] = 0;
		$aTmpDetalle['persona_id'] = $aOrigen['persona_id'];
		$aTmpDetalle['socio_id'] = $aOrigen['socio_id'];
		$aTmpDetalle['cliente_id'] = $aOrigen['cliente_id'];
		$aTmpDetalle['banco_id'] = $aOrigen['banco_id'];
		$aTmpDetalle['codigo_organismo'] = $aOrigen['codigo_organismo'];
		$aTmpDetalle['recibo_id'] = $orden['CancelacionOrden']['recibo_id'];
		$aTmpDetalle['tipo_cobro'] = 'FA';
		$aTmpDetalle['orden_descuento_cobro_id'] = $orden['CancelacionOrden']['orden_descuento_cobro_id'];
		$aTmpDetalle['concepto'] = $orden['CancelacionOrden']['recibo_detalle'];
		$aTmpDetalle['importe'] = $orden['CancelacionOrden']['total_orden'] + ($orden['CancelacionOrden']['importe_diferencia'] * -1);
		array_push($aReciboDetalle, $aTmpDetalle);

		if($orden['CancelacionOrden']['importe_diferencia'] != 0):
			$aTmpDetalle['id'] = 0;
			$aTmpDetalle['persona_id'] = $aOrigen['persona_id'];
			$aTmpDetalle['socio_id'] = $aOrigen['socio_id'];
			$aTmpDetalle['cliente_id'] = $aOrigen['cliente_id'];
			$aTmpDetalle['banco_id'] = $aOrigen['banco_id'];
			$aTmpDetalle['codigo_organismo'] = $aOrigen['codigo_organismo'];
			$aTmpDetalle['recibo_id'] = $orden['CancelacionOrden']['recibo_id'];
			$aTmpDetalle['tipo_cobro'] = 'FA';
			$aTmpDetalle['orden_descuento_cobro_id'] = $orden['CancelacionOrden']['orden_descuento_cobro_id'];
			$aTmpDetalle['orden_descuento_cuota_id'] = $orden['CancelacionOrden']['orden_descuento_cuota_id'];
			$aTmpDetalle['concepto'] = $orden['CancelacionOrden']['tipo_cuota_diferencia_desc'];
			$aTmpDetalle['importe'] = $orden['CancelacionOrden']['importe_diferencia'];
			array_push($aReciboDetalle, $aTmpDetalle);
		endif;

		if(!$this->saveAll($aReciboDetalle)):		
			return false;
		endif;

		return true;
	}
	

//	function detalleReciboCaja($datos){
//		// Establezco de donde es el Ingreso
//		$aOrigen = array();
//		$aOrigen['persona_id'] = (isset($datos['OrdenDescuentoCobro']['cabecera_persona_id']) ? $datos['OrdenDescuentoCobro']['cabecera_persona_id'] : 0);
//		$aOrigen['socio_id']   = (isset($datos['OrdenDescuentoCobro']['cabecera_socio_id'])   ? $datos['OrdenDescuentoCobro']['cabecera_socio_id']   : 0);
//		$aOrigen['cliente_id'] = (isset($datos['OrdenDescuentoCobro']['cabecera_cliente_id']) ? $datos['OrdenDescuentoCobro']['cabecera_cliente_id'] : 0);
//		$aOrigen['banco_id']   = (isset($datos['OrdenDescuentoCobro']['cabecera_banco_id'])   ? $datos['OrdenDescuentoCobro']['cabecera_banco_id']   : null);
//		$aOrigen['codigo_organismo']  = (isset($datos['OrdenDescuentoCobro']['cabecera_codigo_organismo'])  ? $datos['OrdenDescuentoCobro']['cabecera_codigo_organismo'] : null);
//
//		$aTmpDetalle = array();
//		$aTmpDetalle['id'] = 0;
//		$aTmpDetalle['persona_id'] = $aOrigen['persona_id'];
//		$aTmpDetalle['socio_id'] = $aOrigen['socio_id'];
//		$aTmpDetalle['cliente_id'] = $aOrigen['cliente_id'];
//		$aTmpDetalle['banco_id'] = $aOrigen['banco_id'];
//		$aTmpDetalle['codigo_organismo'] = $aOrigen['codigo_organismo'];
//		$aTmpDetalle['recibo_id'] = $datos['OrdenDescuentoCobro']['recibo_id'];
//		$aTmpDetalle['tipo_cobro'] = 'FA';
//		$aTmpDetalle['orden_descuento_cobro_cuota_id'] = $datos['OrdenDescuentoCobro']['orden_descuento_cobro_cuota_id'];
//		$aTmpDetalle['importe'] = $datos['OrdenDescuentoCobro']['importe_cuota'];
//		
//		return $aTmpDetalle;
//		
//	}


	function detalleReciboCaja($datos){
		$aOrigen = array();
		$aOrigen['persona_id'] = (isset($datos['OrdenDescuentoCobro']['cabecera_persona_id']) ? $datos['OrdenDescuentoCobro']['cabecera_persona_id'] : 0);
		$aOrigen['socio_id']   = (isset($datos['OrdenDescuentoCobro']['cabecera_socio_id'])   ? $datos['OrdenDescuentoCobro']['cabecera_socio_id']   : 0);
		$aOrigen['cliente_id'] = (isset($datos['OrdenDescuentoCobro']['cabecera_cliente_id']) ? $datos['OrdenDescuentoCobro']['cabecera_cliente_id'] : 0);
		$aOrigen['banco_id']   = (isset($datos['OrdenDescuentoCobro']['cabecera_banco_id'])   ? $datos['OrdenDescuentoCobro']['cabecera_banco_id']   : null);
		$aOrigen['codigo_organismo']  = (isset($datos['OrdenDescuentoCobro']['cabecera_codigo_organismo'])  ? $datos['OrdenDescuentoCobro']['cabecera_codigo_organismo'] : null);

		$cobroId = $datos['OrdenDescuentoCobro']['orden_caja_cobro_id'];
		$origenFondo = $datos['OrdenDescuentoCobro']['proveedor_origen_fondo_id'];
				
		$sql = "select global_datos.concepto_1, globaldatos.concepto_1, orden_descuentos.*, orden_descuentos.numero, orden_descuentos.cuotas, sum(orden_caja_cobro_cuotas.importe) as importe,sum(orden_caja_cobro_cuotas.importe_abonado) as importe_abonado
				from orden_descuentos
				inner join orden_descuento_cuotas
				on	orden_descuentos.id = orden_descuento_cuotas.orden_descuento_id
				inner join orden_caja_cobro_cuotas
				on	orden_descuento_cuotas.id = orden_caja_cobro_cuotas.orden_descuento_cuota_id
				inner join global_datos
				on global_datos.id = orden_descuentos.tipo_producto
				inner join global_datos as globaldatos
				on globaldatos.id = global_datos.concepto_2
				where	orden_caja_cobro_cuotas.orden_caja_cobro_id = '$cobroId'";
		
		if($origenFondo != MUTUALPROVEEDORID):
			$sql .= " and orden_descuentos.proveedor_id != $origenFondo";
		endif;
		
		$sql .= " group	by orden_descuentos.id
				order	by orden_descuentos.id";
		
		$aOrdenDescuentos =  $this->query($sql);
	
		$aTmpDetalle = array();
		$aReciboDetalle = array();
		foreach($aOrdenDescuentos as $aOrDescuento):
			$OrdenDescuentoId = $aOrDescuento['orden_descuentos']['id'];
			$sqlCuotas = "select	OrdenDescuentoCuota.nro_cuota as nro_cuota, OrdenDescuentoCuota.periodo
							from	orden_descuento_cuotas OrdenDescuentoCuota
							inner	join orden_caja_cobro_cuotas OrdenCajaCobroCuota
							on	OrdenDescuentoCuota.id = OrdenCajaCobroCuota.orden_descuento_cuota_id
							where	OrdenDescuentoCuota.orden_descuento_id = '$OrdenDescuentoId' and OrdenCajaCobroCuota.orden_caja_cobro_id = '$cobroId'";
			$aOrdenDescuentoCuotas = $this->query($sqlCuotas);
			
			$cuotas = Set::extract('/OrdenDescuentoCuota/nro_cuota',$aOrdenDescuentoCuotas);
			$strDesc = implode('-', $cuotas) ."/" . $aOrDescuento['orden_descuentos']['cuotas'];
			if($aOrDescuento[0]['importe_abonado'] < $aOrDescuento[0]['importe']){
				$strDesc .= " *** PAGO PARCIAL ***";
			}

			$periodo = Set::extract('/OrdenDescuentoCuota/periodo', $aOrdenDescuentoCuotas);
			foreach($periodo as $key => $valor):
				$periodo[$key] = substr($valor,0,4) . "/" . substr($valor,-2);
			endforeach;
			$strPeriodo = implode('-', $periodo);

			$aTmpDetalle['id'] = 0;
			$aTmpDetalle['persona_id']       = $aOrigen['persona_id'];
			$aTmpDetalle['socio_id']         = $aOrigen['socio_id'];
			$aTmpDetalle['cliente_id']       = $aOrigen['cliente_id'];
			$aTmpDetalle['banco_id']         = $aOrigen['banco_id'];
			$aTmpDetalle['codigo_organismo'] = $aOrigen['codigo_organismo'];
			$aTmpDetalle['recibo_id'] = $datos['OrdenDescuentoCobro']['recibo_id'];
			$aTmpDetalle['tipo_cobro'] = 'FA';
			$aTmpDetalle['orden_descuento_cobro_id'] = $datos['OrdenDescuentoCobro']['orden_descuento_cobro_id'];
			$aTmpDetalle['orden_descuento_id'] = $aOrDescuento['orden_descuentos']['id'];
			$aTmpDetalle['concepto'] = 'EXPTE: ' . $aOrDescuento['orden_descuentos']['numero'] . ' - ' . $aOrDescuento['global_datos']['concepto_1'] . ' - ctas: ' . $strDesc;
			if($aOrDescuento['orden_descuentos']['tipo_orden_dto'] == 'CMUTU'):
				$aTmpDetalle['concepto'] = $aOrDescuento['globaldatos']['concepto_1'] . ' - PER.: ' . $strPeriodo;
			endif; 
			$aTmpDetalle['importe'] = $aOrDescuento[0]['importe_abonado'];
			array_push($aReciboDetalle, $aTmpDetalle);
		endforeach;

		return $aReciboDetalle;
		
		
	}
	
	
	function grabarReciboDetalle($cobro_id, $recibo_id){
		$aTmpDetalle = array();
		$aReciboDetalle = array();
		
		$this->OrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');
		$ordenDescuento = $this->OrdenDescuentoCobro->getCobro($cobro_id, true);
		
		
		// Establezco que el ingreso viene del socio
		$aOrigen = array();
		$aOrigen['persona_id'] = 0;
		$aOrigen['socio_id']   = $ordenDescuento['OrdenDescuentoCobro']['socio_id'];
		$aOrigen['cliente_id'] = 0;
		$aOrigen['banco_id']   = 0;
		$aOrigen['codigo_organismo']  = null;

		foreach ($ordenDescuento['ReciboDetalle'] as $orden) {
			
			if($orden['imprimir_detalle'] != 0):
				$aTmpDetalle['id'] = 0;
				$aTmpDetalle['persona_id'] = $aOrigen['persona_id'];
				$aTmpDetalle['socio_id'] = $aOrigen['socio_id'];
				$aTmpDetalle['cliente_id'] = $aOrigen['cliente_id'];
				$aTmpDetalle['banco_id'] = $aOrigen['banco_id'];
				$aTmpDetalle['codigo_organismo'] = $aOrigen['codigo_organismo'];
				$aTmpDetalle['recibo_id'] = $recibo_id;
				$aTmpDetalle['tipo_cobro'] = 'FA';
				$aTmpDetalle['orden_descuento_cobro_id'] = $orden['orden_descuento_cobro_id'];
				$aTmpDetalle['concepto'] = $orden['concepto'];
				$aTmpDetalle['importe'] = $orden['importe'];
				array_push($aReciboDetalle, $aTmpDetalle);
			endif;
		}

		if(!empty($aReciboDetalle)):
			if(!$this->saveAll($aReciboDetalle)):		
				return false;
			endif;
		endif;
		
		return true;
	}
	
	
	function getImporte($recibo_id){
		$condiciones = array(
							'conditions' => array(
								'ReciboDetalle.recibo_id' => $recibo_id,
							),
							'fields' => array('SUM(ReciboDetalle.importe) as importe'),
		);
		$importe_detalle = $this->find('all',$condiciones);
		return (isset($importe_detalle[0][0]['importe']) ? $importe_detalle[0][0]['importe'] : 0);		
		
	}
}

?>