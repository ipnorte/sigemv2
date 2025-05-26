<?php
class ProveedorListado extends ProveedoresAppModel{
	var $name = 'ProveedorListado';
	var $useTable = false;
	
	
	function facturas_periodo($fecha_desde, $fecha_hasta, $tipo=0){
		$oFacturas = $this->importarModelo('ProveedorFactura', 'proveedores');
		$oProveedor = $this->importarModelo('Proveedor', 'proveedores');
		
		$aFacturas = $oFacturas->find('all', array('conditions' => array('ProveedorFactura.fecha_comprobante >=' => $fecha_desde, 'ProveedorFactura.fecha_comprobante <=' => $fecha_hasta, 'ProveedorFactura.tipo !=' => 'SA'), 'order' => array('ProveedorFactura.fecha_comprobante')));
		
		// Armo los datos para el IVA Compra
		$aReturnFacturas = array();
		foreach($aFacturas as $key => $factura ):
			$aProveedor = $oProveedor->read(null, $factura['ProveedorFactura']['proveedor_id']);
			$glb = $this->getGlobalDato('concepto_2',$factura['ProveedorFactura']['tipo_comprobante']);
			$factura['ProveedorFactura']['razon_social'] = $aProveedor['Proveedor']['razon_social'];
			$factura['ProveedorFactura']['cuit'] = $aProveedor['Proveedor']['cuit'];
			$factura['ProveedorFactura']['comprobante_libro'] = $glb['GlobalDato']['concepto_2'] . ' ' . $factura['ProveedorFactura']['letra_comprobante'] . '-' . $factura['ProveedorFactura']['punto_venta_comprobante'] . '-' . $factura['ProveedorFactura']['numero_comprobante'];
			if($tipo == 3 || $tipo == $aProveedor['Proveedor']['tipo_proveedor']):
				array_push($aReturnFacturas, $factura);
			endif;
		endforeach;
		
		return $aReturnFacturas;
	}
	

	// ESTA FUNCION NO ESTA IMPLEMENTADA.
//	function factura_concepto_gasto($fecha_desde,$fecha_hasta, $tipo=0){
//		$oFacturas = $this->importarModelo('ProveedorFactura', 'proveedores');
//		
//		$concepto_gasto = $oFacturas->find('all',array('conditions' => array('ProveedorFactura.fecha_comprobante >=' => $fecha_desde, 'ProveedorFactura.fecha_comprobante <=' => $fecha_hasta),
//					'fields' => array("ProveedorFactura.concepto_gasto", "SUM(ProveedorFactura.total_comprobante) as importe_gasto"),'group' => array('ProveedorFactura.concepto_gasto')));
//		
//		debug($concepto_gasto);
//		exit;
//		
//		
//	}

	
	function saldo_a_fecha($fechaDesde, $fechaHasta, $tipo = 0, $proveedor_id = 0){
		$oFacturas = $this->importarModelo('ProveedorFactura', 'proveedores');
		$oProveedor = $this->importarModelo('Proveedor', 'proveedores');
		$oOrdenPago = $this->importarModelo('Proveedor', 'orden_pagos');
		
		$sql = "
				SELECT Proveedor.*, GlobalDato.concepto_1,
				IFNULL(
				(
					SELECT SUM(total_comprobante)
					FROM proveedor_facturas ProveedorFactura
					WHERE ProveedorFactura.tipo != 'SD' AND ProveedorFactura.tipo != 'FA' AND ProveedorFactura.proveedor_id = Proveedor.id AND 
					ProveedorFactura.fecha_comprobante < '$fechaDesde'
				),0) +
				IFNULL(
				(
					SELECT 	SUM(importe)
					FROM	orden_pagos OrdenPago
					WHERE	OrdenPago.proveedor_id = Proveedor.id AND OrdenPago.anulado = 0 AND OrdenPago.fecha_pago < '$fechaDesde'
				),0) - 
				IFNULL((
					SELECT SUM(total_comprobante)
					FROM proveedor_facturas ProveedorFactura
					WHERE (ProveedorFactura.tipo = 'SD' OR ProveedorFactura.tipo='FA') AND ProveedorFactura.proveedor_id = Proveedor.id AND 
						  ProveedorFactura.fecha_comprobante < '$fechaDesde'
				),0) AS saldo_anterior,


				IFNULL((
					SELECT SUM(total_comprobante)
					FROM proveedor_facturas ProveedorFactura
					WHERE (ProveedorFactura.tipo = 'SD' OR ProveedorFactura.tipo='FA') AND ProveedorFactura.proveedor_id = Proveedor.id AND 
					ProveedorFactura.fecha_comprobante BETWEEN '$fechaDesde' AND '$fechaHasta'
				),0) AS debito,

				IFNULL(
				(
					SELECT SUM(total_comprobante)
					FROM proveedor_facturas ProveedorFactura
					WHERE ProveedorFactura.tipo != 'SD' AND ProveedorFactura.tipo != 'FA' AND ProveedorFactura.proveedor_id = Proveedor.id AND 
					ProveedorFactura.fecha_comprobante BETWEEN '$fechaDesde' AND '$fechaHasta'
				),0) AS credito,

				IFNULL(
				(
					SELECT 	SUM(importe)
					FROM	orden_pagos OrdenPago
					WHERE	OrdenPago.proveedor_id = Proveedor.id AND OrdenPago.anulado = 0 AND OrdenPago.fecha_pago BETWEEN '$fechaDesde' AND '$fechaHasta'
				),0) AS pagos,


				IFNULL(
				(
					SELECT SUM(total_comprobante)
					FROM proveedor_facturas ProveedorFactura
					WHERE ProveedorFactura.tipo != 'SD' AND ProveedorFactura.tipo != 'FA' AND ProveedorFactura.proveedor_id = Proveedor.id AND 
					ProveedorFactura.fecha_comprobante BETWEEN '$fechaDesde' AND '$fechaHasta'
				),0) +
				IFNULL(
				(
					SELECT 	SUM(importe)
					FROM	orden_pagos OrdenPago
					WHERE	OrdenPago.proveedor_id = Proveedor.id AND OrdenPago.anulado = 0 AND OrdenPago.fecha_pago BETWEEN '$fechaDesde' AND '$fechaHasta'
				),0) -
				IFNULL((
					SELECT SUM(total_comprobante)
					FROM proveedor_facturas ProveedorFactura
					WHERE (ProveedorFactura.tipo = 'SD' OR ProveedorFactura.tipo='FA') AND ProveedorFactura.proveedor_id = Proveedor.id AND 
					ProveedorFactura.fecha_comprobante BETWEEN '$fechaDesde' AND '$fechaHasta'
				),0) AS saldo,


				IFNULL(
				(
					SELECT SUM(total_comprobante)
					FROM proveedor_facturas ProveedorFactura
					WHERE ProveedorFactura.tipo != 'SD' AND ProveedorFactura.tipo != 'FA' AND ProveedorFactura.proveedor_id = Proveedor.id AND 
					ProveedorFactura.fecha_comprobante <= '$fechaHasta'
				),0) +
				IFNULL(
				(
					SELECT 	SUM(importe)
					FROM	orden_pagos OrdenPago
					WHERE	OrdenPago.proveedor_id = Proveedor.id AND OrdenPago.anulado = 0 AND OrdenPago.fecha_pago <= '$fechaHasta'
				),0) -
				IFNULL((
					SELECT SUM(total_comprobante)
					FROM proveedor_facturas ProveedorFactura
					WHERE (ProveedorFactura.tipo = 'SD' OR ProveedorFactura.tipo='FA') AND ProveedorFactura.proveedor_id = Proveedor.id AND 
					ProveedorFactura.fecha_comprobante <= '$fechaHasta'
				),0) AS saldo_actual


				FROM	proveedores Proveedor
				INNER JOIN global_datos GlobalDato
				ON Proveedor.condicion_iva = GlobalDato.id
		";
		
		if($proveedor_id != 0):
			$sql .= "
					WHERE Proveedor.id = '$proveedor_id'
					";
		else:
			if($tipo != 3):
				$sql .= "
						WHERE Proveedor.tipo_proveedor = '$tipo'
					";
			endif;
		endif;
		
		return $oProveedor->query($sql);
	}
	

	function ctaCteFecha($id, $desdeFecha, $hastaFecha){
		$oMovimiento = $this->importarModelo('Movimiento', 'proveedores');

		$sqlCtaCte = "SELECT
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
		WHERE proveedor_id = $id AND fecha_comprobante BETWEEN '$desdeFecha' AND '$hastaFecha'
			
		UNION
			
		SELECT	OrdenPago.fecha_pago as fecha, concat('ORDEN DE PAGO NRO. : ', right(concat('00000000', OrdenPago.nro_orden_pago),8)) as concepto,
		OrdenPago.importe as debe, 0 as haber, OrdenPago.importe as saldo, OrdenPago.id, 'OPA' as tipo, 0 as anular, OrdenPago.comentario, 0 as orden_cobro
		FROM	orden_pagos as OrdenPago
		WHERE OrdenPago.proveedor_id = $id and OrdenPago.anulado = 0 AND OrdenPago.fecha_pago BETWEEN '$desdeFecha' AND '$hastaFecha' 
		ORDER BY fecha, tipo";
	
		$aCtaCte = $this->query($sqlCtaCte);
	
		$ctaCte = array();
		$tmpCtaCte = array();
		$saldo = 0;
		foreach($aCtaCte as $factura){
		$socio = '';
		if($factura[0]['orden_cobro'] > 0) $socio = $oMovimiento->getNombreSocio($factura[0]['orden_cobro']). ' ** ';
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
	
	
	function factura_tipo_asiento($fecha_desde, $fecha_hasta, $tipo = 3, $tipoAsiento = 0){

		$sql = "
				SELECT	ProveedorTipoAsiento.id, ProveedorTipoAsiento.concepto,
	
				IFNULL((
				SELECT SUM(total_comprobante)
				FROM proveedor_facturas ProveedorFactura
				WHERE ProveedorFactura.tipo='FA' AND ProveedorFactura.proveedor_tipo_asiento_id = ProveedorTipoAsiento.id AND
				ProveedorFactura.fecha_comprobante BETWEEN '$fecha_desde' AND '$fecha_hasta' AND ProveedorFactura.proveedor_id IN(
				SELECT id
				FROM proveedores Proveedor";
				
				if($tipo != 3):
					$sql .= " WHERE Proveedor.tipo_proveedor = '$tipo'";
				endif;
				
				$sql .= ")),0) AS facturado,
				
				IFNULL(
				(
				SELECT SUM(total_comprobante)
				FROM proveedor_facturas ProveedorFactura
				WHERE ProveedorFactura.tipo = 'NC' AND ProveedorFactura.proveedor_tipo_asiento_id = ProveedorTipoAsiento.id AND
				ProveedorFactura.fecha_comprobante BETWEEN '$fecha_desde' AND '$fecha_hasta' AND ProveedorFactura.proveedor_id IN(
				SELECT id
				FROM proveedores Proveedor";
				
				if($tipo != 3):
					$sql .= " WHERE Proveedor.tipo_proveedor = '$tipo'";
				endif;
				
				$sql .= ")),0) AS credito
				
				FROM	proveedor_tipo_asientos ProveedorTipoAsiento
		";
		
		if($tipoAsiento != 0) $sql .= " WHERE ProveedorTipoAsiento.id = '$tipoAsiento'";
		
		return $this->query($sql);
		
	}
	
	
	function factura_tipo_asiento_detalle($tipo_asiento, $desdeFecha, $hastaFecha, $tipo = 3){
		$sql = "
				SELECT Proveedor.cuit, Proveedor.razon_social, GlobalDato.concepto_1 AS tipo_iva, 
				       CONCAT(GlobalDato1.concepto_2, '-', ProveedorFactura.letra_comprobante, '-', ProveedorFactura.punto_venta_comprobante, '-', ProveedorFactura.numero_comprobante) AS comprobante_libro, 
				       ProveedorFactura.*
				FROM proveedores Proveedor
				INNER JOIN proveedor_facturas ProveedorFactura
				ON Proveedor.id = ProveedorFactura.proveedor_id
				LEFT JOIN global_datos GlobalDato
				ON Proveedor.condicion_iva = GlobalDato.id
				LEFT JOIN global_datos GlobalDato1
				ON ProveedorFactura.tipo_comprobante = GlobalDato1.id
				WHERE ProveedorFactura.tipo NOT IN('SA', 'SD') AND ProveedorFactura.proveedor_tipo_asiento_id = '$tipo_asiento' AND ProveedorFactura.fecha_comprobante BETWEEN '$desdeFecha' AND '$hastaFecha'";
		
				if($tipo != 3) $sql .= " AND Proveedor.tipo_proveedor = '$tipo'";
				
				$sql .= " ORDER BY ProveedorFactura.fecha_comprobante
				
		";
		
		return $this->query($sql);
		
	}
	
	
	function factura_concepto_gastos($fecha_desde, $fecha_hasta, $tipo = 3, $conceptoGasto = ''){
		
		$sql = '';
		
		if(empty($conceptoGasto) || $conceptoGasto == 'PROVCGAS'):
			$sql .= "
					SELECT 'PROVCGAS' AS id, 'CONCEPTO DE GASTO' AS concepto_1, 
					IFNULL((
					SELECT SUM(ProveedorFactura.total_comprobante)
					FROM proveedor_facturas ProveedorFactura
					INNER JOIN proveedores Proveedor
					ON	ProveedorFactura.proveedor_id = Proveedor.id
					WHERE ProveedorFactura.concepto_gasto IS NULL AND Proveedor.concepto_gasto IS NULL AND ProveedorFactura.tipo = 'FA' 
					AND ProveedorFactura.fecha_comprobante BETWEEN '$fecha_desde' AND '$fecha_hasta'"; 
	
					if($tipo != 3) $sql .= " AND Proveedor.tipo_proveedor = '$tipo'";
					
					$sql .= "),0) AS facturado,
					
					IFNULL((
					SELECT SUM(ProveedorFactura.total_comprobante)
					FROM proveedor_facturas ProveedorFactura
					INNER JOIN proveedores Proveedor
					ON	ProveedorFactura.proveedor_id = Proveedor.id
					WHERE ProveedorFactura.concepto_gasto IS NULL AND Proveedor.concepto_gasto IS NULL AND ProveedorFactura.tipo = 'NC' 
					AND ProveedorFactura.fecha_comprobante BETWEEN '$fecha_desde' AND '$fecha_hasta'";
					
					if($tipo != 3) $sql .= " AND Proveedor.tipo_proveedor = '$tipo'";
					
					$sql .= "),0) AS credito";
		else:
			$sql .= "SELECT '' AS id, '' AS concepto_1, 0 AS facturado, 0 AS credito UNION ";
		endif;
				
		if(empty($conceptoGasto)) $sql .= " UNION";
		
		if(empty($conceptoGasto) || $conceptoGasto != 'PROVCGAS'):
			$sql .= "
					(
					SELECT GlobalDato.id, GlobalDato.concepto_1, 
					
					IFNULL(( 
					SELECT SUM(ProveedorFactura.total_comprobante)
					FROM proveedor_facturas ProveedorFactura
					INNER JOIN proveedores Proveedor
					ON	ProveedorFactura.proveedor_id = Proveedor.id
					WHERE	ProveedorFactura.concepto_gasto IS NULL AND ProveedorFactura.tipo = 'FA' AND Proveedor.concepto_gasto = GlobalDato.id 
					AND ProveedorFactura.fecha_comprobante BETWEEN '$fecha_desde' AND '$fecha_hasta'";
	
					if($tipo != 3) $sql .= " AND Proveedor.tipo_proveedor = '$tipo'";
					
					$sql .= ")
					,0) +
					
					
					IFNULL(( 
					SELECT SUM(total_comprobante) 
					FROM proveedor_facturas ProveedorFactura 
					WHERE ProveedorFactura.tipo='FA' AND ProveedorFactura.concepto_gasto = GlobalDato.id 
					AND ProveedorFactura.fecha_comprobante BETWEEN '$fecha_desde' AND '$fecha_hasta' AND ProveedorFactura.proveedor_id IN( 
					SELECT id FROM proveedores Proveedor";
	
					if($tipo != 3) $sql .= " WHERE Proveedor.tipo_proveedor = '$tipo'";
					
					$sql .= ")),0) AS facturado, 
					
					IFNULL(( 
					SELECT SUM(ProveedorFactura.total_comprobante)
					FROM proveedor_facturas ProveedorFactura
					INNER JOIN proveedores Proveedor
					ON	ProveedorFactura.proveedor_id = Proveedor.id
					WHERE	ProveedorFactura.concepto_gasto IS NULL AND ProveedorFactura.tipo = 'NC' AND Proveedor.concepto_gasto = GlobalDato.id 
					AND ProveedorFactura.fecha_comprobante BETWEEN '$fecha_desde' AND '$fecha_hasta'";
					
					if($tipo != 3) $sql .= " AND Proveedor.tipo_proveedor = '$tipo'";
					
					$sql .= ")
					,0) +
					
					IFNULL(( 
					SELECT SUM(total_comprobante) 
					FROM proveedor_facturas ProveedorFactura 
					WHERE ProveedorFactura.tipo = 'NC' AND ProveedorFactura.concepto_gasto = GlobalDato.id 
					AND ProveedorFactura.fecha_comprobante BETWEEN '$fecha_desde' AND '$fecha_hasta' AND ProveedorFactura.proveedor_id IN( 
					SELECT id FROM proveedores Proveedor";
					
					if($tipo != 3) $sql .= " WHERE Proveedor.tipo_proveedor = '$tipo'";
					
					$sql .= ")),0) AS credito 
					
					FROM global_datos GlobalDato";
					
					if(empty($conceptoGasto)):
						$sql .= " WHERE	GlobalDato.id LIKE 'PROVCGAS%' AND GlobalDato.id != 'PROVCGAS'";
					else:
						$sql .= " WHERE	GlobalDato.id = '$conceptoGasto'";
					endif;
					
					$sql .= " ORDER	BY globalDato.id)
			";
		endif;
		
		return $this->query($sql);
		
	}
	
	
	function factura_concepto_gasto_detalle($concepto_gasto, $desdeFecha, $hastaFecha, $tipo = 3){
		
		$sql = "
				SELECT Proveedor.cuit, Proveedor.razon_social, GlobalDato.concepto_1 AS tipo_iva,
				CONCAT(GlobalDato1.concepto_2, '-', ProveedorFactura.letra_comprobante, '-', ProveedorFactura.punto_venta_comprobante, '-', ProveedorFactura.numero_comprobante) AS comprobante_libro,
				ProveedorFactura.*
				FROM proveedores Proveedor
				INNER JOIN proveedor_facturas ProveedorFactura
				ON Proveedor.id = ProveedorFactura.proveedor_id
				LEFT JOIN global_datos GlobalDato
				ON Proveedor.condicion_iva = GlobalDato.id
				LEFT JOIN global_datos GlobalDato1
				ON ProveedorFactura.tipo_comprobante = GlobalDato1.id
				WHERE ProveedorFactura.tipo NOT IN('SA', 'SD') AND ProveedorFactura.fecha_comprobante BETWEEN '$desdeFecha' AND '$hastaFecha'";
		
				if($concepto_gasto == 'PROVCGAS') $sql .= " AND (ProveedorFactura.concepto_gasto IS NULL AND Proveedor.concepto_gasto IS NULL)"; 
				else $sql .= " AND (ProveedorFactura.concepto_gasto = '$concepto_gasto' OR (ProveedorFactura.concepto_gasto IS NULL AND Proveedor.concepto_gasto = '$concepto_gasto'))";
		
				if($tipo != 3) $sql .= " AND Proveedor.tipo_proveedor = '$tipo'";
				
		return $this->query($sql);
		
	}

	
}
?>