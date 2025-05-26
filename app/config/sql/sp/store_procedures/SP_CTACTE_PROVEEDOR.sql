/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 25/02/2019
 */

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CTACTE_PROVEEDOR`(
	vPROVEEDOR_ID INT(11)
)
BEGIN
				
	DECLARE vID INT(11);
		
	DECLARE vFECHA DATE;
	DECLARE vCONCEPTO VARCHAR(100);
	DECLARE vDEBE DECIMAL(10,2);
	DECLARE vHABER DECIMAL(10,2);
	DECLARE vSALDO DECIMAL(10,2);
	DECLARE vTIPO VARCHAR(3);
	DECLARE vANULAR INT(1);
	DECLARE vCOMENTARIO VARCHAR(100);
	DECLARE vORDEN_COBRO INT(11);
	DECLARE vLIQUIDACION INT(11);
	DECLARE vPAGO DECIMAL(10,2);
	
	DECLARE vSocio VARCHAR(200);
	DECLARE vDetalle_id INT(11);
	DECLARE vImporte DECIMAL(10,2);
	DECLARE vITEM INT(11);
	DECLARE vTOTAL DECIMAL(10,2);
	DECLARE vRow INT(10);
	
	declare vfin int(1);
	
	DECLARE CtaCte_Cursor CURSOR FOR
	SELECT	ProveedorFactura.id AS id,
	ProveedorFactura.fecha_comprobante AS fecha, 
	CONCAT(IF(ProveedorFactura.tipo_comprobante = 'SALDOPROVEED', 'SALDO ANTERIOR', 
	(SELECT concepto_1 FROM global_datos WHERE id = ProveedorFactura.tipo_comprobante)), ' ',
	ProveedorFactura.letra_comprobante, '-', ProveedorFactura.punto_venta_comprobante, '-', ProveedorFactura.numero_comprobante) AS concepto,
	IF(ProveedorFactura.tipo = 'NC', ProveedorFactura.total_comprobante, 0) AS debe,
	IF(ProveedorFactura.tipo = 'FA', ProveedorFactura.total_comprobante, 0) AS haber,
	ProveedorFactura.total_comprobante * IF(ProveedorFactura.tipo = 'SD' OR ProveedorFactura.tipo='FA',-1, 1) AS saldo,
	ProveedorFactura.tipo, 
	IF(IFNULL(IF(ProveedorFactura.tipo = 'FA' OR ProveedorFactura.tipo = 'SD', (SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
	WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id), (SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
	WHERE OrdenPagoFactura.proveedor_credito_id = ProveedorFactura.id)),0) = 0, 0, 1) AS anular,
	ProveedorFactura.comentario, ProveedorFactura.orden_descuento_cobro_id AS orden_cobro, ProveedorFactura.liquidacion_id AS liquidacion
	FROM proveedor_facturas AS ProveedorFactura
	WHERE ProveedorFactura.proveedor_id = vPROVEEDOR_ID
	UNION
	SELECT	OrdenPago.id AS id, OrdenPago.fecha_pago AS fecha, CONCAT('ORDEN DE PAGO NRO. : ', RIGHT(CONCAT('00000000', OrdenPago.nro_orden_pago),8)) AS concepto,
	OrdenPago.importe AS debe, 0 AS haber, OrdenPago.importe AS saldo, 'OPA' AS tipo, 
	IF((SELECT COUNT(*) FROM banco_cuenta_movimientos WHERE orden_pago_id = OrdenPago.id AND banco_cuenta_saldo_id > 0) > 0, 1, 0) AS anular, OrdenPago.comentario, 
	0 AS orden_cobro, 0 AS liquidacion
	FROM	orden_pagos AS OrdenPago
	WHERE OrdenPago.proveedor_id = vPROVEEDOR_ID AND OrdenPago.anulado = 0
	ORDER BY fecha, tipo;
	
-- Condición de salida
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET vfin=1;
	
	set vITEM = 0;
	set vTOTAL = 0;
	set vfin = 0;
	set vDetalle_id = 0;
	OPEN CtaCte_Cursor;
	DELETE	FROM proveedor_ctactes WHERE proveedor_id = vPROVEEDOR_ID;
	
	select FOUND_ROWS() into vRow;
	get_CtaCte: LOOP
		SET vITEM = vITEM + 1;
		if vITEM > vRow then
			LEAVE get_CtaCte;
		end if;
		
		FETCH CtaCte_Cursor INTO vID, vFECHA, vCONCEPTO, vDEBE, vHABER, vSALDO, vTIPO, vANULAR, vCOMENTARIO, vORDEN_COBRO, vLIQUIDACION;
		
		set vSocio = '';
		set vImporte = 0.00;
		set vPAGO = 0.00;
		set vDetalle_id = 0;
		
		
		IF vTIPO = 'OPA' THEN
			SELECT	id INTO vDetalle_id
			FROM	orden_pago_detalles
			WHERE	orden_pago_id = vID AND tipo_pago = 'AN' AND importe > 0;
			
			if vDetalle_id > 0 then
				SELECT	sum(importe) INTO vImporte
				FROM	orden_pago_detalles
				WHERE	orden_pago_detalle_id = vDetalle_id;
			end if;
			
			IF vImporte <> 0.00 THEN
				set vPAGO = vImporte;
				set vANULAR = 1;
			END IF;
		ELSE
			IF vTIPO = 'FA' OR vTIPO = 'ND' THEN
				SELECT	SUM(importe) INTO vImporte
				FROM	orden_pago_facturas
				WHERE	proveedor_factura_id = vID;
			END IF;
			
			
			IF vTIPO = 'NC' THEN
				SELECT	SUM(importe) INTO vImporte
				FROM	orden_pago_facturas
				WHERE	proveedor_credito_id = vID;
			END IF;
			
			set vPAGO = vImporte;
			IF vImporte <> 0.00 THEN
				set vANULAR = 1;
			END IF;
		END IF;
		
		if vORDEN_COBRO > 0 then
			SELECT CONCAT('DNI ', p.documento, ' - ', p.apellido, ' ', p.nombre, ' ** ') into vSocio
			FROM	personas p
			INNER JOIN socios s
			ON	p.id = s.persona_id
			INNER	JOIN orden_descuento_cobros c
			ON	c.socio_id = s.id
			WHERE	c.id = vORDEN_COBRO;
			
			set vCOMENTARIO = concat(vSocio, vCOMENTARIO);
		end if;
		
		if vORDEN_COBRO > 0 or vLIQUIDACION > 0 then
			SET vANULAR = 1;
		end if;
		
		set vTOTAL = vTOTAL + vSALDO;
		INSERT INTO `proveedor_ctactes` 
			(`item`, `proveedor_id`, `fecha`, `concepto`, `debe`, `haber`, `saldo`, `id`, `tipo`, `anular`, `comentario`, `pagos`)
			VALUES
			(vITEM, vPROVEEDOR_ID, vFECHA, vCONCEPTO, vDEBE, vHABER, vTOTAL, vID, vTIPO, vANULAR, vCOMENTARIO, vPAGO);
	END LOOP get_CtaCte;
	
    END$$
DELIMITER ;

