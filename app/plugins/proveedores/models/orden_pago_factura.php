<?php
class OrdenPagoFactura extends ProveedoresAppModel{
	var $name = 'OrdenPagoFactura';
	
	function getPagoFactura($proveedor_factura_id){
		$pagos = $this->find('all',array('conditions' => array('OrdenPagoFactura.proveedor_factura_id' => $proveedor_factura_id),
					'fields' => array("SUM(OrdenPagoFactura.importe) as pago"),'group' => array('OrdenPagoFactura.proveedor_factura_id')));
		if(count($pagos)!=0) return $pagos[0][0]['pago'];
		else return 0;
		
	}

	function getPagoNotaCredito($proveedor_credito_id){
		$pagos = $this->find('all',array('conditions' => array('OrdenPagoFactura.proveedor_credito_id' => $proveedor_credito_id),
					'fields' => array("SUM(OrdenPagoFactura.importe) as pago"),'group' => array('OrdenPagoFactura.proveedor_credito_id')));
		if(count($pagos)!=0) return $pagos[0][0]['pago'];
		else return 0;
		
	}

	function getPagoCuenta($orden_pago_detalle_id){
		$pagos = $this->find('all',array('conditions' => array('OrdenPagoFactura.orden_pago_detalle_id' => $orden_pago_detalle_id),
					'fields' => array("SUM(OrdenPagoFactura.importe) as pago"),'group' => array('OrdenPagoFactura.orden_pago_detalle_id')));
		if(count($pagos)!=0) return $pagos[0][0]['pago'];
		else return 0;
		
	}
        
        function DetallePagoFacturas($proveedor_factura_id){
            $sql = "SELECT OrdenPagoFactura.*, CONCAT(OrdenPago.sucursal, '-', OrdenPago.nro_orden_pago) AS documento, OrdenPago.fecha_pago
                    FROM orden_pago_facturas OrdenPagoFactura, orden_pagos OrdenPago
                    WHERE OrdenPagoFactura.proveedor_factura_id = '$proveedor_factura_id' AND OrdenPagoFactura.orden_pago_id = OrdenPago.id 
                    UNION
                    SELECT OrdenPagoFactura.*, CONCAT(OrdenPago.sucursal, '-', OrdenPago.nro_orden_pago) AS documento, OrdenPago.fecha_pago
                    FROM orden_pago_facturas OrdenPagoFactura, orden_pago_detalles OrdenPagoDetalle, orden_pagos OrdenPago
                    WHERE OrdenPagoFactura.proveedor_factura_id = '$proveedor_factura_id' AND OrdenPagoFactura.orden_pago_detalle_id = OrdenPagoDetalle.id 
                    AND OrdenPagoDetalle.orden_pago_id = OrdenPago.id
                    UNION
                    SELECT OrdenPagoFactura.*, CONCAT(ProveedorFactura.letra_comprobante, '-', ProveedorFactura.punto_venta_comprobante, '-', numero_comprobante) AS documento, '' as fecha_pago
                    FROM orden_pago_facturas OrdenPagoFactura, proveedor_facturas ProveedorFactura
                    WHERE OrdenPagoFactura.proveedor_factura_id = '$proveedor_factura_id' AND OrdenPagoFactura.proveedor_credito_id = ProveedorFactura.id AND OrdenPagoFactura.orden_pago_id = 0 
                    ";
            
            return $this->query($sql);
            
        }
}	

?>