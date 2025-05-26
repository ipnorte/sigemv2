<?php
class ReciboFactura extends ClientesAppModel{
	var $name = 'ReciboFactura';
	
	function getCobroFactura($cliente_factura_id){
		$cobros = $this->find('all',array('conditions' => array('ReciboFactura.cliente_factura_id' => $cliente_factura_id),
					'fields' => array("SUM(ReciboFactura.importe) as cobro"),'group' => array('ReciboFactura.cliente_factura_id')));
		if(count($cobros)!=0) return $cobros[0][0]['cobro'];
		else return 0;
		
	}

	function getCobroNotaCredito($cliente_credito_id){
		$cobros = $this->find('all',array('conditions' => array('ReciboFactura.cliente_credito_id' => $cliente_credito_id),
					'fields' => array("SUM(ReciboFactura.importe) as cobro"),'group' => array('ReciboFactura.cliente_credito_id')));
		if(count($cobros)!=0) return $cobros[0][0]['cobro'];
		else return 0;
		
	}

	function getCobroCuenta($recibo_detalle_id){
		$cobros = $this->find('all',array('conditions' => array('ReciboFactura.recibo_detalle_id' => $recibo_detalle_id),
					'fields' => array("SUM(ReciboFactura.importe) as cobro"),'group' => array('ReciboFactura.recibo_detalle_id')));
		if(count($cobros)!=0) return $cobros[0][0]['cobro'];
		else return 0;
		
	}
        
        function DetallePagoFacturas($cliente_factura_id){
            $sql = "SELECT ReciboFactura.*, CONCAT(Recibo.sucursal, '-', Recibo.nro_recibo) AS documento, Recibo.fecha_comprobante as rec_fecha
                    FROM recibo_facturas ReciboFactura, recibos Recibo
                    WHERE ReciboFactura.cliente_factura_id = '$cliente_factura_id' AND ReciboFactura.recibo_id = Recibo.id 
                    UNION
                    SELECT ReciboFactura.*, CONCAT(Recibo.sucursal, '-', Recibo.nro_recibo) AS documento, Recibo.fecha_comprobante as rec_fecha
                    FROM recibo_facturas ReciboFactura, recibo_detalles ReciboDetalle, recibos Recibo
                    WHERE ReciboFactura.cliente_factura_id = '$cliente_factura_id' AND ReciboFactura.recibo_detalle_id = ReciboDetalle.id 
                    AND ReciboDetalle.recibo_id = Recibo.id
                    UNION
                    SELECT ReciboFactura.*, CONCAT(ClienteFactura.letra_comprobante, '-', ClienteFactura.punto_venta_comprobante, '-', ClienteFactura.numero_comprobante) AS documento, '' as rec_fecha
                    FROM recibo_facturas ReciboFactura, cliente_facturas ClienteFactura
                    WHERE ReciboFactura.cliente_factura_id = '$cliente_factura_id' AND ReciboFactura.cliente_credito_id = ClienteFactura.id AND ReciboFactura.recibo_id = 0 
                    ";
            
            return $this->query($sql);
            
        }
}	

?>