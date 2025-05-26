/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 19/12/2019
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

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LIQUIDA_DEUDA_SCORING`(IN
vSOCIO_ID INT(11),vLIQUIDACION_ID INT(11))
BEGIN

select periodo into @periodo from liquidaciones where id = vLIQUIDACION_ID;
delete from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID;
insert into liquidacion_socio_scores(liquidacion_id,socio_id,`13`,`12`,`09`,`06`,`03`,`00`,cargos_adicionales,saldo_actual)
select liquidacion_id,socio_id, 
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 12 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 12 month),'%Y%m')  
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 9 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 9 month),'%Y%m')
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 6 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota  > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 6 month),'%Y%m') 
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 3 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota  > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 3 month),'%Y%m') 
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 0 month),'%Y%m')
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 0 month),'%Y%m')
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id <> 0
and lc.socio_id = lc2.socio_id),0),
sum(saldo_actual) as saldo_actual
from liquidacion_cuotas lc2 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID
group by socio_id;

-- //// ASIGNO PUNTAJE
if cast((select `13` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 5 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `12` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 4 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `09` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 3 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `06` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 2 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `03` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 1 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `00` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 0 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
end if;
end if;
end if;
end if;
end if;
end if;


END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_TMP_DESIMPUTA_LIQUIDACION`(
IN vLIQUIDACION_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
DECLARE nReintegros INT(11);
DECLARE vCOBRO_ID INT(11);
DECLARE reintegros_abonados CONDITION FOR SQLSTATE '45000';


DECLARE C_CUOTAS CURSOR FOR 

select orden_descuento_cobro_id 
from liquidacion_cuotas where liquidacion_id = vLIQUIDACION_ID
and imputada = 1 and ifnull(orden_descuento_cobro_id,0) <> 0 
GROUP BY orden_descuento_cobro_id;

DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;
DECLARE CONTINUE HANDLER FOR reintegros_abonados RESIGNAL SET MESSAGE_TEXT = '*** REINTEGROS ABONADOS ***';
DECLARE exit handler for sqlexception
BEGIN 
    ROLLBACK;
END;
DECLARE exit handler for sqlwarning
BEGIN 
    ROLLBACK;
END;

-- CONTROLAR SI NO TIENE REINTEGROS ABONADOS
set nReintegros = 0;

select count(*) into nReintegros from socio_reintegros r
inner join orden_pago_detalles opd on opd.socio_reintegro_id = r.id
where r.liquidacion_id = vLIQUIDACION_ID and r.anticipado = 0;

if nReintegros <> 0 then
    signal reintegros_abonados;
end if;

START TRANSACTION;

OPEN C_CUOTAS;
c1_loop: LOOP
FETCH C_CUOTAS INTO vCOBRO_ID;

        IF (l_last_row = 1) THEN
			LEAVE c1_loop; 
		END IF;	
        
        
		-- -----------------------------------------------------------------
		-- BORRO LOS ADICIONALES DEVENGADOS
        -- -----------------------------------------------------------------
        
        SET FOREIGN_KEY_CHECKS = 0;
        
        DELETE cu.* FROM orden_descuento_cuotas cu
        inner join liquidacion_cuotas lc on (lc.liquidacion_id = vLIQUIDACION_ID 
        and lc.orden_descuento_cuota_id = cu.id)
        inner join orden_descuento_cobro_cuotas co on (co.orden_descuento_cobro_id = vCOBRO_ID and co.orden_descuento_cuota_id = cu.id)
        where ifnull(lc.mutual_adicional_pendiente_id,0) <> 0;

        update liquidacion_cuotas set orden_descuento_cuota_id = null
        where liquidacion_id = vLIQUIDACION_ID and orden_descuento_cobro_id = vCOBRO_ID
        and ifnull(mutual_adicional_pendiente_id,0) <> 0;           

        /*
        -- ANULO LOS GASTOS DE GESTION EMITIDOS Y COBRADOS
        update orden_descuento_cuotas cu, liquidacion_cuotas lc,orden_descuento_cobro_cuotas co
        set cu.estado = 'B'
        where 
        lc.liquidacion_id = vLIQUIDACION_ID and lc.orden_descuento_cuota_id = cu.id
        and co.orden_descuento_cobro_id = vCOBRO_ID and co.orden_descuento_cuota_id = cu.id
        and ifnull(lc.mutual_adicional_pendiente_id,0) <> 0;  
        */
        
        update liquidacion_cuotas set imputada = 0, orden_descuento_cobro_id = 0
        where liquidacion_id = vLIQUIDACION_ID and orden_descuento_cobro_id = vCOBRO_ID
        and ifnull(mutual_adicional_pendiente_id,0) <> 0;          
        
		-- -----------------------------------------------------------------
        -- MARCO LAS CUOTAS COMO NO IMPUTADAS
        -- -----------------------------------------------------------------
        update liquidacion_cuotas set imputada = 0, orden_descuento_cobro_id = 0
        where liquidacion_id = vLIQUIDACION_ID and orden_descuento_cobro_id = vCOBRO_ID;

		-- -----------------------------------------------------------------
        -- BORRO EL DETALLE DEL COBRO Y ANULO LA CABECERA
        -- -----------------------------------------------------------------
		DELETE FROM orden_descuento_cobro_cuotas where orden_descuento_cobro_id = vCOBRO_ID;

        update orden_descuento_cobros set anulado = 1 where id = vCOBRO_ID;
        
        SET FOREIGN_KEY_CHECKS = 1;
        
        
        
END LOOP c1_loop;
CLOSE C_CUOTAS;


-- -----------------------------------------------------------------
-- SACO LA MARCA DEL COBRO EN LA SOCIO RENDICIONES
-- -----------------------------------------------------------------

update liquidacion_socio_rendiciones 
set orden_descuento_cobro_id = 0 where liquidacion_id = vLIQUIDACION_ID
and ifnull(orden_descuento_cobro_id,0) <> 0;


-- -----------------------------------------------------------------
-- BORRO LOS REINTEGROS
-- -----------------------------------------------------------------
delete from socio_reintegros where liquidacion_id = vLIQUIDACION_ID and anticipado = 0;

-- -----------------------------------------------------------------
-- MARCO LOS ADICIONALES PENDIENTES
-- -----------------------------------------------------------------

update mutual_adicional_pendientes 
set procesado = 0, orden_descuento_cuota_id = null
where liquidacion_id = vLIQUIDACION_ID
and procesado = 1 and ifnull(orden_descuento_cuota_id,0) <> 0;

-- -----------------------------------------------------------------
-- ACTUALIZO LA CABECERA DE LA LIQUIDACION
-- -----------------------------------------------------------------
update liquidaciones set imputada = 0 where id = vLIQUIDACION_ID;

COMMIT;

END$$
DELIMITER ;
