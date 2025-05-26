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
CREATE DEFINER=`root`@`%` FUNCTION `FX_ORDENDTO_CANT_CUOTAS`(vORDEN_ID INT,vPERIODO CHAR(6),vTIPO CHAR(3)) RETURNS int(11)
BEGIN
declare vCANTIDAD INT(11) DEFAULT 0;

IF vTIPO = 'ANU' THEN
SELECT count(*) INTO vCANTIDAD FROM orden_descuento_cuotas WHERE 
orden_descuento_id = vORDEN_ID and estado IN ('B','D');
END IF;

IF vTIPO = 'TOT' THEN
SELECT count(*) INTO vCANTIDAD FROM orden_descuento_cuotas WHERE 
orden_descuento_id = vORDEN_ID and estado NOT IN ('B','D');
END IF;

IF vTIPO = 'VEN' THEN
SELECT count(*) INTO vCANTIDAD FROM orden_descuento_cuotas WHERE 
orden_descuento_id = vORDEN_ID AND periodo <= vPERIODO and estado NOT IN ('B','D')
AND importe > IFNULL((
                SELECT SUM(cocu.importe) from
                orden_descuento_cobro_cuotas cocu
                INNER JOIN orden_descuento_cobros co 
                ON (co.id = cocu.orden_descuento_cobro_id) 
                WHERE 
                cocu.orden_descuento_cuota_id = orden_descuento_cuotas.id
                AND co.periodo_cobro <= vPERIODO),0);
END IF;

IF vTIPO = 'NVE' THEN
SELECT count(*) INTO vCANTIDAD FROM orden_descuento_cuotas WHERE 
orden_descuento_id = vORDEN_ID AND periodo > vPERIODO and estado NOT IN ('B','D')
AND importe > IFNULL((
                SELECT SUM(cocu.importe) from
                orden_descuento_cobro_cuotas cocu
                INNER JOIN orden_descuento_cobros co 
                ON (co.id = cocu.orden_descuento_cobro_id) 
                WHERE 
                cocu.orden_descuento_cuota_id = orden_descuento_cuotas.id),0);
END IF;

RETURN vCANTIDAD;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`%` FUNCTION `FX_ORDENDTO_DEVENGADO`(vORDEN_ID INT,vPERIODO CHAR(6),vTIPO CHAR(3)) RETURNS decimal(10,2)
BEGIN
DECLARE vSALDO DECIMAL(10,2) DEFAULT 0;
if vTIPO = 'ANU' then
SELECT ifnull(SUM(importe),0) into vSALDO FROM orden_descuento_cuotas WHERE 
                orden_descuento_id = vORDEN_ID and estado IN ('B','D');
end if;
if vTIPO = 'TOT' then
SELECT ifnull(SUM(importe),0) into vSALDO FROM orden_descuento_cuotas WHERE 
                orden_descuento_id = vORDEN_ID and estado NOT IN ('B','D');
end if;
if vTIPO = 'PER' then
SELECT ifnull(SUM(importe),0) into vSALDO FROM orden_descuento_cuotas WHERE 
                orden_descuento_id = vORDEN_ID AND periodo <= vPERIODO and estado NOT IN ('B','D');
end if;
RETURN vSALDO;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`%` FUNCTION `FX_ORDENDTO_PAGO_ACUMULADO`(vORDEN_ID INT,vPERIODO CHAR(6),vTIPO CHAR(3)) RETURNS decimal(10,2)
BEGIN
DECLARE vPAGO_ACUMULADO DECIMAL(10,2) DEFAULT 0;

-- cobrado total al periodo
if vTIPO = 'TOT' then
    select IFNULL(sum(cc.importe),0) INTO vPAGO_ACUMULADO
    from orden_descuento_cobro_cuotas cc, orden_descuento_cobros co,orden_descuento_cuotas cu 
    where 
    cu.orden_descuento_id = vORDEN_ID
    and cc.orden_descuento_cuota_id = cu.id and cc.orden_descuento_cobro_id = co.id and
    co.periodo_cobro <= vPERIODO
    group by cu.orden_descuento_id;
end if;

-- cobrado a termino
if vTIPO = 'TER' then
    select IFNULL(sum(cc.importe),0) INTO vPAGO_ACUMULADO
    from orden_descuento_cobro_cuotas cc, orden_descuento_cobros co,orden_descuento_cuotas cu 
    where 
    cu.orden_descuento_id = vORDEN_ID
    and cc.orden_descuento_cuota_id = cu.id and cc.orden_descuento_cobro_id = co.id and
    co.periodo_cobro = cu.periodo
    and co.periodo_cobro <= vPERIODO
    group by cu.orden_descuento_id;
end if;

-- cobrado vencido
if vTIPO = 'VEN' then
    select IFNULL(sum(cc.importe),0) INTO vPAGO_ACUMULADO
    from orden_descuento_cobro_cuotas cc, orden_descuento_cobros co,orden_descuento_cuotas cu 
    where 
    cu.orden_descuento_id = vORDEN_ID
    and cc.orden_descuento_cuota_id = cu.id and cc.orden_descuento_cobro_id = co.id and
    co.periodo_cobro <> cu.periodo
    and co.periodo_cobro <= vPERIODO
    group by cu.orden_descuento_id;
end if;

-- PAGADO POR CANCELACION
if vTIPO = 'CAN' then
    select IFNULL(sum(cc.importe),0) INTO vPAGO_ACUMULADO
    from orden_descuento_cobro_cuotas cc, orden_descuento_cobros co,orden_descuento_cuotas cu 
    where 
    cu.orden_descuento_id = vORDEN_ID
    and cc.orden_descuento_cuota_id = cu.id 
    and cc.orden_descuento_cobro_id = co.id 
    and co.periodo_cobro <= vPERIODO
    and ifnull(co.cancelacion_orden_id,0) <> 0
    group by cu.orden_descuento_id;
end if;
-- PAGADO POR CAJA
if vTIPO = 'CAJ' then
    select IFNULL(sum(cc.importe),0) INTO vPAGO_ACUMULADO
    from orden_descuento_cobro_cuotas cc, orden_descuento_cobros co,orden_descuento_cuotas cu 
    where 
    cu.orden_descuento_id = vORDEN_ID
    and cc.orden_descuento_cuota_id = cu.id 
    and cc.orden_descuento_cobro_id = co.id 
    and co.periodo_cobro <= vPERIODO
    and ifnull(co.cancelacion_orden_id,0) = 0
    and co.id not in (select co.id from orden_descuento_cobros co
                inner join liquidacion_socio_rendiciones lsr on lsr.socio_id = co.socio_id
                and lsr.orden_descuento_cobro_id = co.id
                where ifnull(cancelacion_orden_id,0) = 0
                group by co.id)
    group by cu.orden_descuento_id;
end if;
-- PAGADO POR LIQUIDACION
if vTIPO = 'LIQ' then
    select IFNULL(sum(cc.importe),0) INTO vPAGO_ACUMULADO
    from orden_descuento_cobro_cuotas cc, orden_descuento_cobros co,orden_descuento_cuotas cu 
    where 
    cu.orden_descuento_id = vORDEN_ID
    and cc.orden_descuento_cuota_id = cu.id 
    and cc.orden_descuento_cobro_id = co.id 
    and co.periodo_cobro <= vPERIODO
    and ifnull(co.cancelacion_orden_id,0) = 0
    and co.id in (select co.id from orden_descuento_cobros co
                inner join liquidacion_socio_rendiciones lsr on lsr.socio_id = co.socio_id
                and lsr.orden_descuento_cobro_id = co.id
                where ifnull(cancelacion_orden_id,0) = 0
                group by co.id)
    group by cu.orden_descuento_id;
end if;

RETURN vPAGO_ACUMULADO;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`%` FUNCTION `FX_ORDENDTO_PENDIENTE_ACREDITAR`(vORDEN_ID INT,vPERIODO CHAR(6)) RETURNS decimal(10,2)
BEGIN
DECLARE vPENDIENTE DECIMAL(10,2);
SET vPENDIENTE = 0;
SELECT ROUND(ifnull(SUM(importe_debitado),0),2) INTO vPENDIENTE FROM liquidacion_cuotas lc
inner join liquidaciones l on l.id = lc.liquidacion_id and l.periodo <=  vPERIODO
WHERE orden_descuento_id = vORDEN_ID
AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0;
RETURN vPENDIENTE;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`%` FUNCTION `FX_ORDENDTO_SALDO_A_VENCER_POR_RANGO`(vORDEN_ID INT,vPERIODO CHAR(6),vDESDE INT,vHASTA INT) RETURNS decimal(10,2)
BEGIN
declare vSALDO DECIMAL(10,2) default 0;
select IFNULL(sum(cu.importe),0) INTO vSALDO from orden_descuento_cuotas cu
WHERE cu.orden_descuento_id = vORDEN_ID
    AND cu.periodo > date_format(date_add(STR_TO_DATE(CONCAT(vPERIODO,'01'),'%Y%m%d'), interval vDESDE month),'%Y%m') 
and cu.periodo <= date_format(date_add(date_add(STR_TO_DATE(CONCAT(vPERIODO,'01'),'%Y%m%d'), interval vDESDE month),interval vHASTA month),'%Y%m')
and cu.estado NOT IN ('B','D','C')
    AND cu.importe > ((SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
    INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
    WHERE 
    cocu.orden_descuento_cuota_id = cu.id 
    AND co.periodo_cobro <= vPERIODO) + ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc
        inner join liquidaciones l on l.id = lc.liquidacion_id and l.periodo <= vPERIODO
    WHERE orden_descuento_cuota_id = cu.id
    AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
    order by liquidacion_id desc limit 1 ),0))
    GROUP BY cu.orden_descuento_id;
RETURN vSALDO;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`%` FUNCTION `FX_ORDENDTO_SALDO_VENCIDO_POR_RANGO`(vORDEN_ID INT,vPERIODO CHAR(6),vDESDE INT,vHASTA INT) RETURNS decimal(10,2)
BEGIN
DECLARE vSALDO DECIMAL(10,2) DEFAULT 0;

SELECT ifnull((SELECT SUM(importe) - SUM(ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
                where orden_descuento_cuotas.id = cc.orden_descuento_cuota_id
                and cc.periodo_cobro <= vPERIODO),0)) - SUM(ifnull((
                 select sum(importe_debitado) from liquidacion_cuotas lc
                where lc.orden_descuento_cuota_id = orden_descuento_cuotas.id and lc.imputada = 0 and 
                    lc.para_imputar = 1 and lc.periodo_cuota <= vPERIODO 
                    and ifnull(lc.orden_descuento_cobro_id,0) = 0)
                 ,0)) FROM orden_descuento_cuotas WHERE 
                orden_descuento_id = vORDEN_ID 
                AND periodo > date_format(date_sub(date_format(concat(vPERIODO,'01'),'%Y-%m-%d'), interval vHASTA month),'%Y%m')
                AND periodo <= date_format(date_sub(date_format(concat(vPERIODO,'01'),'%Y-%m-%d'), interval vDESDE month),'%Y%m')
                and estado NOT IN ('B','D') and importe > ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
                where orden_descuento_cuotas.id = cc.orden_descuento_cuota_id
                and cc.periodo_cobro <= vPERIODO),0)),0) INTO vSALDO;

RETURN vSALDO;
END$$
DELIMITER ;

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

DELIMITER $$
CREATE DEFINER=`root`@`%` FUNCTION `FX_SOLICITUD_GET_ORDENDTO`(vSOLICITUD_ID INT(11)) RETURNS int(11)
BEGIN
DECLARE vORDEN_ID INT(11) default 0;
DECLARE vNUEVA_ORDEN_ID INT(11) default 0;

DECLARE vNOVACION boolean default FALSE;

select ifnull(orden_descuento_id,0) into vORDEN_ID
from mutual_producto_solicitudes
where id = vSOLICITUD_ID;

select ifnull(nueva_orden_descuento_id,0) into vNUEVA_ORDEN_ID from orden_descuentos where id = vORDEN_ID;

while vNOVACION = FALSE do

    if vNUEVA_ORDEN_ID <> 0 then
    
        select id,ifnull(nueva_orden_descuento_id,0) into vORDEN_ID,vNUEVA_ORDEN_ID 
        from orden_descuentos where id = vNUEVA_ORDEN_ID;
    
    else
        set vNOVACION = TRUE;
        
    end if;
    
    


end while;

RETURN vORDEN_ID;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`%` FUNCTION `FX_VALIDA_CUIT`(CUIT BIGINT) RETURNS tinyint(1)
    DETERMINISTIC
BEGIN
DECLARE RES, DIG, NUM BIGINT;
DECLARE i INT;

IF LENGTH(CUIT) != 11 OR SUBSTR(CUIT, 1, 2) = '00' THEN
RETURN FALSE;
END IF;

SET RES = 0;
SET i = 1;
WHILE i < 11 DO
    SET NUM = (SUBSTR(CUIT, I, 1));

    IF (i = 1 OR i = 7) THEN
        SET RES = RES + NUM * 5;
    ELSEIF (I = 2 OR I = 8) THEN
        SET RES = RES + NUM * 4;
    ELSEIF (I = 3 OR I = 9) THEN
        SET RES = RES + NUM * 3;
    ELSEIF (I = 4 OR I = 10) THEN
        SET RES = RES + NUM * 2;
    ELSEIF (I = 5) THEN
        SET RES = RES + NUM * 7;
    ELSEIF (I = 6) THEN
        SET RES = RES + NUM * 6;
    END IF;
    SET i = i+1;
END WHILE;


SET DIG = 11 - MOD(RES,11);
IF DIG = 11 THEN
    SET DIG = 0;
END IF;

IF DIG = (SUBSTR(CUIT,11,1)) THEN
    RETURN TRUE;
ELSE
    RETURN FALSE;
END IF;

END$$
DELIMITER ;
