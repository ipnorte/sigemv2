/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 06/01/2020
 */

DROP FUNCTION IF EXISTS FX_ORDENDTO_CANT_CUOTAS;
DELIMITER $$
CREATE FUNCTION `FX_ORDENDTO_CANT_CUOTAS`(vORDEN_ID INT,vPERIODO CHAR(6),vTIPO CHAR(3)) RETURNS int(11)
BEGIN
declare vCANTIDAD INT(11) DEFAULT 0;

IF vTIPO = 'ANU' THEN
SELECT count(*) INTO vCANTIDAD FROM orden_descuento_cuotas 
inner join global_datos g on g.id = orden_descuento_cuotas.tipo_producto
and g.concepto_2 = orden_descuento_cuotas.tipo_cuota
WHERE 
orden_descuento_id = vORDEN_ID and estado IN ('B','D');
END IF;

IF vTIPO = 'TOT' THEN
SELECT count(*) INTO vCANTIDAD FROM orden_descuento_cuotas 
inner join global_datos g on g.id = orden_descuento_cuotas.tipo_producto
and g.concepto_2 = orden_descuento_cuotas.tipo_cuota
WHERE 
orden_descuento_id = vORDEN_ID and estado NOT IN ('B','D');
END IF;

IF vTIPO = 'VEN' THEN
SELECT count(*) INTO vCANTIDAD FROM orden_descuento_cuotas 
inner join global_datos g on g.id = orden_descuento_cuotas.tipo_producto
and g.concepto_2 = orden_descuento_cuotas.tipo_cuota
WHERE 
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
SELECT count(*) INTO vCANTIDAD FROM orden_descuento_cuotas 
inner join global_datos g on g.id = orden_descuento_cuotas.tipo_producto
and g.concepto_2 = orden_descuento_cuotas.tipo_cuota
WHERE 
orden_descuento_id = vORDEN_ID AND periodo > vPERIODO and estado NOT IN ('B','D')
AND importe > IFNULL((
                SELECT SUM(cocu.importe) from
                orden_descuento_cobro_cuotas cocu
                INNER JOIN orden_descuento_cobros co 
                ON (co.id = cocu.orden_descuento_cobro_id) 
                WHERE 
                cocu.orden_descuento_cuota_id = orden_descuento_cuotas.id),0);
END IF;

IF vTIPO = 'PAG' THEN
SELECT count(*) INTO vCANTIDAD FROM orden_descuento_cuotas 
inner join global_datos g on g.id = orden_descuento_cuotas.tipo_producto
and g.concepto_2 = orden_descuento_cuotas.tipo_cuota
WHERE 
orden_descuento_id = vORDEN_ID and estado NOT IN ('B','D')
AND importe <= IFNULL((
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

DROP FUNCTION IF EXISTS FX_ORDENDTO_DEVENGADO;
DELIMITER $$
CREATE FUNCTION `FX_ORDENDTO_DEVENGADO`(vORDEN_ID INT,vPERIODO CHAR(6),vTIPO CHAR(3)) RETURNS decimal(10,2)
BEGIN
DECLARE vSALDO DECIMAL(10,2) DEFAULT 0;
if vTIPO = 'ANU' then
SELECT ifnull(SUM(importe),0) into vSALDO FROM orden_descuento_cuotas 
inner join global_datos g on g.id = orden_descuento_cuotas.tipo_producto
and g.concepto_2 = orden_descuento_cuotas.tipo_cuota
WHERE 
                orden_descuento_id = vORDEN_ID and estado IN ('B','D');
end if;
if vTIPO = 'TOT' then
SELECT ifnull(SUM(importe),0) into vSALDO FROM orden_descuento_cuotas 
inner join global_datos g on g.id = orden_descuento_cuotas.tipo_producto
and g.concepto_2 = orden_descuento_cuotas.tipo_cuota
WHERE 
                orden_descuento_id = vORDEN_ID and estado NOT IN ('B','D');
end if;
if vTIPO = 'PER' then
SELECT ifnull(SUM(importe),0) into vSALDO FROM orden_descuento_cuotas 
inner join global_datos g on g.id = orden_descuento_cuotas.tipo_producto
and g.concepto_2 = orden_descuento_cuotas.tipo_cuota
WHERE 
                orden_descuento_id = vORDEN_ID AND periodo <= vPERIODO and estado NOT IN ('B','D');
end if;

-- IMPORTE PERCIBIDO POR EL SOCIO
if vTIPO = 'IPE' then
	select so.importe_percibido into vSALDO from orden_descuentos o 
	inner join mutual_producto_solicitudes so on so.id = o.numero and 
	(so.proveedor_id = o.proveedor_id or so.reasignar_proveedor_id = o.proveedor_id)
	and so.tipo_producto = o.tipo_producto
	where o.id = vORDEN_ID;
end if;

-- IMPORTE SOLICITADO
if vTIPO = 'ISO' then
	select so.importe_solicitado into vSALDO from orden_descuentos o 
	inner join mutual_producto_solicitudes so on so.id = o.numero and 
	(so.proveedor_id = o.proveedor_id or so.reasignar_proveedor_id = o.proveedor_id)
	and so.tipo_producto = o.tipo_producto
	where o.id = vORDEN_ID;
end if;

RETURN vSALDO;
END$$
DELIMITER ;

DROP FUNCTION IF EXISTS FX_ORDENDTO_PAGO_ACUMULADO;
DELIMITER $$
CREATE FUNCTION `FX_ORDENDTO_PAGO_ACUMULADO`(vORDEN_ID INT,vPERIODO CHAR(6),vTIPO CHAR(3)) RETURNS decimal(10,2)
BEGIN
DECLARE vPAGO_ACUMULADO DECIMAL(10,2) DEFAULT 0;

-- cobrado total al periodo
if vTIPO = 'TOT' then
    select IFNULL(sum(cc.importe),0) INTO vPAGO_ACUMULADO
    from orden_descuento_cobro_cuotas cc, orden_descuento_cobros co,orden_descuento_cuotas cu 
	inner join global_datos g on g.id = cu.tipo_producto
	and g.concepto_2 = cu.tipo_cuota    
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
	inner join global_datos g on g.id = cu.tipo_producto
	and g.concepto_2 = cu.tipo_cuota        
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
	inner join global_datos g on g.id = cu.tipo_producto
	and g.concepto_2 = cu.tipo_cuota        
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
	inner join global_datos g on g.id = cu.tipo_producto
	and g.concepto_2 = cu.tipo_cuota        
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
	inner join global_datos g on g.id = cu.tipo_producto
	and g.concepto_2 = cu.tipo_cuota        
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
	inner join global_datos g on g.id = cu.tipo_producto
	and g.concepto_2 = cu.tipo_cuota        
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
