DROP FUNCTION IF EXISTS FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE`(
vPROVEEDOR_ID INT(11)) RETURNS decimal(10,2)
BEGIN
DECLARE vSALDO DECIMAL(10,2);
DECLARE vLIQUIDA_PRESTAMO BOOLEAN;
SET vSALDO = 10000000;
SELECT liquida_prestamo into vLIQUIDA_PRESTAMO FROM proveedores WHERE id = vPROVEEDOR_ID;
IF vLIQUIDA_PRESTAMO = TRUE THEN 
SELECT	
		(
			SELECT IFNULL(SUM(c.importe_debitado * -1),0)
			FROM	liquidacion_cuotas c, liquidaciones l, global_datos AS g
			WHERE	c.liquidacion_id = l.id AND l.facturada = 0 AND c.proveedor_id = p.id AND l.codigo_organismo = g.id
		) +
		(
			SELECT	IFNULL(SUM(c.comision_cobranza),0)
			FROM	liquidacion_cuotas c, liquidaciones l, global_datos AS g
			WHERE	c.liquidacion_id = l.id AND l.facturada = 0 AND c.proveedor_id = p.id AND l.codigo_organismo = g.id
		) +
		(
			SELECT	IFNULL(SUM(ProveedorFactura.total_comprobante * IF(ProveedorFactura.tipo = 'SD' OR ProveedorFactura.tipo='FA',-1, 1)),0)
			FROM proveedor_facturas AS ProveedorFactura
			WHERE proveedor_id = p.id
		) +
		(
			SELECT	IFNULL(SUM(OrdenPago.importe),0)
			FROM	orden_pagos AS OrdenPago
			WHERE proveedor_id = p.id AND anulado = 0
		) +
		(
			SELECT	IFNULL(SUM(ClienteFactura.total_comprobante * IF(ClienteFactura.tipo = 'SD' OR ClienteFactura.tipo='FA' OR ClienteFactura.tipo = 'ND',1, -1)),0)
			FROM cliente_facturas AS ClienteFactura, proveedores AS Proveedor
			WHERE Proveedor.id = p.id AND ClienteFactura.cliente_id = Proveedor.cliente_id AND ClienteFactura.anulado = 0
		) +
		(
			SELECT	IFNULL(SUM(Recibo.importe * -1),0)
			FROM	recibos AS Recibo, proveedores AS Proveedor
			WHERE	Proveedor.id = p.id AND Recibo.cliente_id = Proveedor.cliente_id AND Recibo.anulado = 0 AND Recibo.cliente_id > 0
		) INTO vSALDO
		FROM	proveedores p 
		WHERE	p.id = vPROVEEDOR_ID;
        SET vSALDO = vSALDO * -1;
END IF;
RETURN vSALDO;
END$$
DELIMITER ;
