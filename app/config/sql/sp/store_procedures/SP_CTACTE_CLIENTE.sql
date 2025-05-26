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
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CTACTE_CLIENTE`(
	vCLIENTE_ID INT(11)
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
	
	DECLARE vfin INT(1);
	DECLARE CtaCte_Cursor CURSOR FOR
	SELECT ClienteFactura.id, ClienteFactura.fecha_comprobante AS fecha, 
	CONCAT(IF(ClienteFactura.tipo = 'SD' OR ClienteFactura.tipo = 'SD', 'SALDO ANTERIOR', 
	CONCAT(IF(ClienteFactura.tipo = 'FA', 'FACTURA',
	IF(ClienteFactura.tipo = 'ND', 'NOTA DEBITO', 'NOTA CREDITO')), ' ', ClienteFactura.letra_comprobante, '-', 
	ClienteFactura.punto_venta_comprobante, '-', ClienteFactura.numero_comprobante)))AS concepto,
	IF(ClienteFactura.tipo = 'FA' OR ClienteFactura.tipo = 'ND', ClienteFactura.total_comprobante, 0) AS debe,
	IF(ClienteFactura.tipo = 'NC', ClienteFactura.total_comprobante, 0) AS haber,
	ClienteFactura.total_comprobante * IF(ClienteFactura.tipo = 'SD' OR ClienteFactura.tipo='FA' OR ClienteFactura.tipo = 'ND',1, -1) AS saldo,
	ClienteFactura.tipo,
	IF(IFNULL(IF(ClienteFactura.tipo = 'FA' OR ClienteFactura.tipo = 'SD', (SELECT SUM(importe) FROM recibo_facturas AS ReciboFactura
	WHERE ReciboFactura.cliente_factura_id = ClienteFactura.id), (SELECT SUM(importe) FROM recibo_facturas AS ReciboFactura
	WHERE ReciboFactura.cliente_credito_id = ClienteFactura.id)),0) = 0, 0, 1) AS anular,
	ClienteFactura.comentario, ClienteFactura.orden_descuento_cobro_id AS orden_cobro, ClienteFactura.liquidacion_id AS liquidacion,
	IFNULL(IF(ClienteFactura.tipo = 'FA' OR ClienteFactura.tipo = 'SD', (SELECT SUM(importe) FROM recibo_facturas AS ReciboFactura
	WHERE ReciboFactura.cliente_factura_id = ClienteFactura.id), (SELECT SUM(importe) FROM recibo_facturas AS ReciboFactura
	WHERE ReciboFactura.cliente_credito_id = ClienteFactura.id)),0) AS cobro_comprobante
	FROM cliente_facturas AS ClienteFactura
	WHERE ClienteFactura.cliente_id = vCLIENTE_ID AND ClienteFactura.anulado = 0
	UNION
	SELECT	Recibo.id, Recibo.fecha_comprobante AS fecha, 
	CONCAT('RECIBO NRO.: ', Recibo.letra, '-', Recibo.sucursal, '-', Recibo.nro_recibo) AS concepto,
	0 AS debe, Recibo.importe AS haber, Recibo.importe * -1 AS saldo, 'REC' AS tipo, 
	IF((SELECT COUNT(*) FROM banco_cuenta_movimientos WHERE recibo_id = Recibo.id AND banco_cuenta_saldo_id > 0) > 0, 1, 0) AS anular,
	Recibo.comentarios, 0 AS orden_cobro, 0 AS liquidacion, 0 AS cobro_comprobante
	FROM	recibos AS Recibo
	WHERE	Recibo.cliente_id = vCLIENTE_ID AND Recibo.anulado = 0
	ORDER BY fecha, tipo;
-- Condición de salida
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET vfin=1;
	
	SET vITEM = 0;
	SET vTOTAL = 0;
	SET vfin = 0;
	SET vDetalle_id = 0;
	OPEN CtaCte_Cursor;
	DELETE	FROM cliente_ctactes WHERE cliente_id = vCLIENTE_ID;
	
	SELECT FOUND_ROWS() INTO vRow;
	get_CtaCte: LOOP
		SET vITEM = vITEM + 1;
		IF vITEM > vRow THEN
			LEAVE get_CtaCte;
		END IF;
		
		FETCH CtaCte_Cursor INTO vID, vFECHA, vCONCEPTO, vDEBE, vHABER, vSALDO, vTIPO, vANULAR, vCOMENTARIO, vORDEN_COBRO, vLIQUIDACION, vPAGO;
		
		SET vSocio = '';
		SET vImporte = 0.00;
		SET vDetalle_id = 0;
		
		IF vORDEN_COBRO > 0 THEN
			SELECT CONCAT('DNI ', p.documento, ' - ', p.apellido, ' ', p.nombre, ' ** ') INTO vSocio
			FROM	personas p
			INNER JOIN socios s
			ON	p.id = s.persona_id
			INNER	JOIN orden_descuento_cobros c
			ON	c.socio_id = s.id
			WHERE	c.id = vORDEN_COBRO;
			SET vCOMENTARIO = CONCAT(vSocio, vCOMENTARIO);
		END IF;
		IF vORDEN_COBRO > 0 OR vLIQUIDACION > 0 THEN
			SET vANULAR = 1;
		END IF;
		SET vTOTAL = vTOTAL + vSALDO;
		INSERT INTO `cliente_ctactes` 
			(`item`, `cliente_id`, `fecha`, `concepto`, `debe`, `haber`, `saldo`, `id`, `tipo`, `anular`, `comentario`, `pagos`)
			VALUES
			(vITEM, vCLIENTE_ID, vFECHA, vCONCEPTO, vDEBE, vHABER, vTOTAL, vID, vTIPO, vANULAR, vCOMENTARIO, vPAGO);
	END LOOP get_CtaCte;
	
    END$$
DELIMITER ;
