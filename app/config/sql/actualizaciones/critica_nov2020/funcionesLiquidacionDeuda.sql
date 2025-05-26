DROP FUNCTION IF EXISTS `FX_CALCULA_ADICIONAL`;
DROP FUNCTION IF EXISTS `FX_CALCULA_CUOTA_SERVICIO`;
DROP FUNCTION IF EXISTS `FX_CALCULA_CUOTA_SOCIAL`;
DROP FUNCTION IF EXISTS `FX_CALCULAR_SALDO_CUOTA`;
DROP FUNCTION IF EXISTS `FX_GENERAR_DISKETTE_BANCO`;
DROP FUNCTION IF EXISTS `FX_ORDENDTO_CANT_CUOTAS`;
DROP FUNCTION IF EXISTS `FX_ORDENDTO_DEVENGADO`;
DROP FUNCTION IF EXISTS `FX_ORDENDTO_PAGO_ACUMULADO`;
DROP FUNCTION IF EXISTS `FX_ORDENDTO_PENDIENTE_ACREDITAR`;
DROP FUNCTION IF EXISTS `FX_ORDENDTO_SALDO_A_VENCER_POR_RANGO`;
DROP FUNCTION IF EXISTS `FX_ORDENDTO_SALDO_VENCIDO_POR_RANGO`;
DROP FUNCTION IF EXISTS `FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE`;
DROP FUNCTION IF EXISTS `FX_SOLICITUD_GET_ORDENDTO`;
DROP FUNCTION IF EXISTS `FX_VALIDA_CBU`;
DROP FUNCTION IF EXISTS `FX_VALIDA_CBU_DIGITO`;
DROP FUNCTION IF EXISTS `FX_VALIDA_CBU_GENDIGITO`;
DROP FUNCTION IF EXISTS `FX_VALIDA_CUIT`;

DELIMITER $$
CREATE DEFINER=`ipnorte`@`localhost` FUNCTION `FX_CALCULAR_SALDO_CUOTA`(`vCUOTA_ID` INT(11), `vPREIMPUTADO` BOOLEAN) RETURNS decimal(10,2)
BEGIN
DECLARE vPAGOS_CUOTA DECIMAL(10,2);
DECLARE vPREIMP_CUOTA DECIMAL(10,2);
DECLARE vIMPORTE_CUOTA DECIMAL(10,2);
DECLARE vSALDO DECIMAL(10,2);

SET vPAGOS_CUOTA = 0;
SET vPREIMP_CUOTA = 0;
SET vIMPORTE_CUOTA = 0;
SET vSALDO = 0;

SET @START_AT = SYSDATE(6);

select importe into vIMPORTE_CUOTA from orden_descuento_cuotas where id = vCUOTA_ID;
select ifnull(sum(importe),0) into vPAGOS_CUOTA from orden_descuento_cobro_cuotas where orden_descuento_cuota_id = vCUOTA_ID; 

-- BUSCO SI TIENE PREIMPUTADO
IF vPREIMPUTADO = TRUE THEN
/*	select ifnull(sum(importe_debitado),0) into vPREIMP_CUOTA from liquidacion_cuota_saldos 
	where orden_descuento_cuota_id = vCUOTA_ID
	and ifnull(orden_descuento_cobro_id,0) = 0;
*/
	select importe_debitado into vPREIMP_CUOTA from tmp_preimputado
	where orden_descuento_cuota_id = vCUOTA_ID;
END IF;



SET vSALDO = round(vIMPORTE_CUOTA - (vPAGOS_CUOTA + vPREIMP_CUOTA),2);

/*
delete from tmp_liquida_log where cuota_id = vCUOTA_ID and preimputa = vPREIMPUTADO;

SET @END_AT = SYSDATE(6);
-- TIMESTAMPDIFF(MICROSECOND, now(3), updated_at) / 1000
SET @MILISEC = TIMESTAMPDIFF(MICROSECOND,@START_AT,@END_AT)/1000;
INSERT INTO tmp_liquida_log(cuota_id,start_at,end_at,ms,preimputa)
values(vCUOTA_ID,@START_AT,@END_AT,@MILISEC,vPREIMPUTADO);
*/

RETURN IF(vSALDO < 0,0,vSALDO);
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`ipnorte`@`localhost` FUNCTION `FX_CALCULA_ADICIONAL`() RETURNS int(11)
BEGIN

RETURN 1;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`ipnorte`@`localhost` FUNCTION `FX_CALCULA_CUOTA_SERVICIO`(
vORDEN_ID INT(11),
vTIPO_PRODUCTO VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
vORGANISMO VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
vPERIODO VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci
) RETURNS decimal(10,2)
BEGIN
DECLARE vSOLICITUD INT(11);
DECLARE vPERIODO_HASTA VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci;
DECLARE vTIPO_ORDEN VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_general_ci;
DECLARE vTIPO_PRODUCTO VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci;
DECLARE vTIPO_CUOTA VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci;
DECLARE vBENEFICIO_ID INT(11);
DECLARE vPROVEEDOR_ID INT(11);
DECLARE vNRO_REFERENCIA_PROVEEDOR VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci;
DECLARE vIMPORTE_ORDEN_CUOTA DECIMAL(10,2);
DECLARE vIMPORTE_CUOTA_SERVICIO DECIMAL(10,2);

SET vIMPORTE_CUOTA_SERVICIO = 0;

select concepto_2 into vTIPO_CUOTA from global_datos where id = vTIPO_PRODUCTO COLLATE utf8_general_ci;

select tipo_orden_dto,numero,importe_cuota,proveedor_id into vTIPO_ORDEN,vSOLICITUD,vIMPORTE_ORDEN_CUOTA,vPROVEEDOR_ID 
from orden_descuentos
where id = vORDEN_ID;

set vIMPORTE_CUOTA_SERVICIO = vIMPORTE_ORDEN_CUOTA;

-- BUSCO SI TIENE UN IMPORTE FIJO
select ifnull(MutualProducto.importe_fijo,0) INTO vIMPORTE_CUOTA_SERVICIO
from mutual_productos as MutualProducto 
where MutualProducto.tipo_producto = vTIPO_PRODUCTO COLLATE utf8_general_ci 
and MutualProducto.proveedor_id = vPROVEEDOR_ID 
AND importe_fijo <> 0;

set vIMPORTE_CUOTA_SERVICIO = if(ifnull(vIMPORTE_CUOTA_SERVICIO,0)=0,vIMPORTE_ORDEN_CUOTA,vIMPORTE_CUOTA_SERVICIO);

-- BUSCO EN LA ESTRUCTURA DE SERVICIOS
IF vTIPO_ORDEN = 'OSERV' THEN
	set vIMPORTE_CUOTA_SERVICIO = 0;
	select mutual_servicio_valores.id, mutual_servicio_valores.mutual_servicio_id, importe_titular, importe_adicional, 
	costo_titular, costo_adicional, periodo_vigencia, fecha_vigencia 
	into @servicio_id, @mutual_servicio_id, @importe_titular, @importe_adicional, 
	@costo_titular, @costo_adicional, @periodo_vigencia, @fecha_vigencia         
	from mutual_servicio_valores
	inner join mutual_servicio_solicitudes on (mutual_servicio_solicitudes.mutual_servicio_id = mutual_servicio_valores.mutual_servicio_id)
	where codigo_organismo = vORGANISMO 
	and periodo_vigencia <= vPERIODO
	and mutual_servicio_solicitudes.id = vSOLICITUD
	order by periodo_vigencia desc limit 1;  

	SET @IMPORTE_SERVICIO_MENSUAL = @importe_titular + ifnull((SELECT ROUND(COUNT(*) * @costo_adicional,2) 
	FROM mutual_servicio_solicitud_adicionales
	where mutual_servicio_solicitud_id = vSOLICITUD 
	and ifnull(periodo_hasta,'000000') <=  vPERIODO),0);    
    
    SET vIMPORTE_CUOTA_SERVICIO = round(@IMPORTE_SERVICIO_MENSUAL,2);
    
END IF;

-- NO TIENE AMBOS, MANDO EL VALOR DE LA CUOTA QUE FIGURA EN LA CABECERA DE LA ORDEN
SET vIMPORTE_CUOTA_SERVICIO = IF(vIMPORTE_CUOTA_SERVICIO = 0,vIMPORTE_ORDEN_CUOTA,vIMPORTE_CUOTA_SERVICIO);


RETURN vIMPORTE_CUOTA_SERVICIO;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`ipnorte`@`localhost` FUNCTION `FX_CALCULA_CUOTA_SOCIAL`(
vSOCIO_ID INT(11),
vPROVEEDOR_ID INT(11),
vTIPO_PRODUCTO VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
vORGANISMO VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
vPERIODO VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci,
vLIQUIDA_SOLO_DEUDA BOOLEAN,
vCODIGO_EMPRESA VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci
) RETURNS decimal(10,2)
BEGIN

DECLARE vIMPORTE_CUOTA_SOCIAL DECIMAL(10,2);

SET vIMPORTE_CUOTA_SOCIAL = 0;

-- SACAR EL VALOR DE LA GLOBAL PARA EL CODIGO DE ORGANISMO
select decimal_1 into vIMPORTE_CUOTA_SOCIAL from global_datos
where id = concat('MUTUCUOS',RIGHT(vORGANISMO,4));

SELECT ifnull(decimal_1,0) into @cuota_empresa from global_datos where id = vCODIGO_EMPRESA;
IF @cuota_empresa <> 0 THEN SET vIMPORTE_CUOTA_SOCIAL = @cuota_empresa; END IF;

IF substring(vORGANISMO,9,2) = '66' THEN
	SELECT importe_cuota_social into vIMPORTE_CUOTA_SOCIAL FROM socios WHERE id = vSOCIO_ID;
END IF;

SET @cuota_social_diferenciada = 0;
SELECT MutualProducto.cuota_social_diferenciada, COUNT(*) 
into @cuota_social_diferenciada,@cantidad
FROM orden_descuentos AS OrdenDescuento 
INNER JOIN mutual_productos AS MutualProducto ON
(
	MutualProducto.tipo_orden_dto = OrdenDescuento.tipo_orden_dto
	AND MutualProducto.tipo_producto = OrdenDescuento.tipo_producto
	AND MutualProducto.proveedor_id = OrdenDescuento.proveedor_id
)	
WHERE OrdenDescuento.socio_id = vSOCIO_ID
AND OrdenDescuento.activo = 1
AND OrdenDescuento.tipo_orden_dto <> 'CMUTU'
AND MutualProducto.cuota_social_diferenciada <> 0
AND OrdenDescuento.socio_id NOT IN
(SELECT socio_id FROM orden_descuentos WHERE tipo_orden_dto <> 'CMUTU'
AND proveedor_id <> OrdenDescuento.proveedor_id)
GROUP BY MutualProducto.cuota_social_diferenciada
ORDER BY MutualProducto.cuota_social_diferenciada DESC;

IF @cantidad = 1 THEN SET @cuota_social_diferenciada = 0;
END IF;

IF (SELECT COUNT(*) FROM orden_descuentos WHERE socio_id = vSOCIO_ID and tipo_orden_dto <> 'CMUTU' and permanente = 0) > 1 THEN SET @cuota_social_diferenciada = 0;
END IF;

IF @cuota_social_diferenciada = 0 THEN
	SELECT cuota_social_diferenciada INTO @cuota_social_diferenciada 
    FROM mutual_productos where tipo_producto = vTIPO_PRODUCTO
    and proveedor_id = vPROVEEDOR_ID ORDER BY cuota_social_diferenciada DESC LIMIT 1;
END IF;

IF @cuota_social_diferenciada <> 0 THEN
	SET vIMPORTE_CUOTA_SOCIAL = @cuota_social_diferenciada;
END IF;

IF vLIQUIDA_SOLO_DEUDA = TRUE AND
	(select 
	SUM(ABS(importe) - ifnull((select sum(ABS(importe))from orden_descuento_cobro_cuotas
	where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0))
	as deuda
	from 
	orden_descuento_cuotas
	where 
	socio_id = vSOCIO_ID and estado <> 'B' 
	AND periodo <= vPERIODO
	AND proveedor_id IN (SELECT id FROM proveedores WHERE genera_cuota_social = 1 AND id <> 18)
	group by socio_id) <= 0 THEN   
    SET vIMPORTE_CUOTA_SOCIAL = 0;
END IF;

SET vIMPORTE_CUOTA_SOCIAL = IFNULL(vIMPORTE_CUOTA_SOCIAL,0);

RETURN vIMPORTE_CUOTA_SOCIAL;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`ipnorte`@`localhost` FUNCTION `FX_GENERAR_DISKETTE_BANCO`(
vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
vLiqId INT(11),
vEmpresas TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
vTurnos TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
vFechaDebito date,
vFechaPresentacion date,
vUsuario varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci,
vUUID varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
vNombreArchivo varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci,
vValidarCBU boolean
) RETURNS int(11)
BEGIN
DECLARE envioID INT(11);

select nombre into @bancoNombre from bancos where cast(id as unsigned) = cast(vBancoId as unsigned);
-- set vNOMBRE_BANCO = '';
set vNombreArchivo = ifnull(vNombreArchivo,'SIN_NOMBRE.txt');

-- MARCO LOS STOPS PARA EXCLUIRLOS
update liquidacion_socios, (
select socio_calificaciones.id,socio_calificaciones.socio_id,socio_calificaciones.periodo, socio_calificaciones.calificacion,global_datos.logico_2,global_datos.concepto_1 from socio_calificaciones
inner join global_datos on global_datos.id = socio_calificaciones.calificacion
inner join (select max(socio_calificaciones.id) id,socio_calificaciones.socio_id
from socio_calificaciones
inner join global_datos on global_datos.id = socio_calificaciones.calificacion
group by socio_id) t on t.id = socio_calificaciones.id
) AS calificaciones
set error_intercambio = if(calificaciones.logico_2,calificaciones.concepto_1,'')
,error_cbu = calificaciones.logico_2
where liquidacion_id = vLiqId
and liquidacion_socios.socio_id = calificaciones.socio_id
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

-- MARCO LOS ERRORES DE CBU
IF(vValidarCBU = TRUE) THEN
	update liquidacion_socios
	set error_intercambio = 'ERROR CBU'
	,error_cbu = 1
	where liquidacion_id = vLiqId
    and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
	and FIND_IN_SET (turno_pago,cast(vTurnos as char))
	and diskette = TRUE and FX_VALIDA_CBU(cbu) = FALSE;
END IF;

SET group_concat_max_len = 18446744073709551615;
insert into liquidacion_socio_envios(liquidacion_id,banco_id,
fecha_debito,banco_nombre,cantidad_registros,importe_debito,user_created,lote
,longitud_registro,archivo,created,status,uuid)
select vLiqId,vBancoId,vFechaDebito
,@bancoNombre,count(*),sum(importe_adebitar)
,vUsuario
,group_concat(ltrim(rtrim(intercambio)) separator '')
,length(replace(intercambio,'\r\n',''))
,vNombreArchivo
,now()
,'OK',vUUID collate utf8_general_ci
from liquidacion_socios
where liquidacion_id = vLiqId
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE and error_cbu = FALSE;


set envioID = last_insert_id();
insert into liquidacion_socio_envio_registros(liquidacion_socio_envio_id,liquidacion_socio_id,socio_id,importe_adebitar,identificador_debito,registro,excluido,motivo,user_created)
select envioID,id,socio_id,importe_adebitar,
concat(lpad(socio_id,5,0),lpad(liquidacion_id,4,0),lpad(registro,2,0)),intercambio,error_cbu,error_intercambio,vUsuario 
from liquidacion_socios
where liquidacion_id = vLiqId
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

RETURN envioID;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`ipnorte`@`localhost` FUNCTION `FX_ORDENDTO_CANT_CUOTAS`(vORDEN_ID INT,vPERIODO CHAR(6),vTIPO CHAR(3)) RETURNS int(11)
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

IF vTIPO = 'PAG' THEN
SELECT count(*) INTO vCANTIDAD FROM orden_descuento_cuotas WHERE 
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

DELIMITER $$
CREATE DEFINER=`ipnorte`@`localhost` FUNCTION `FX_ORDENDTO_DEVENGADO`(vORDEN_ID INT,vPERIODO CHAR(6),vTIPO CHAR(3)) RETURNS decimal(10,2)
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


if vTIPO = 'IPE' then
	select so.importe_percibido into vSALDO from orden_descuentos o 
	inner join mutual_producto_solicitudes so on so.id = o.numero and 
	(so.proveedor_id = o.proveedor_id or so.reasignar_proveedor_id = o.proveedor_id)
	and so.tipo_producto = o.tipo_producto
	where o.id = vORDEN_ID;
end if;


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

DELIMITER $$
CREATE DEFINER=`ipnorte`@`localhost` FUNCTION `FX_ORDENDTO_PAGO_ACUMULADO`(`vORDEN_ID` INT, `vPERIODO` CHAR(6), `vTIPO` CHAR(3)) RETURNS decimal(10,2)
BEGIN
DECLARE vPAGO_ACUMULADO DECIMAL(10,2) DEFAULT 0;


if vTIPO = 'TOT' then
    select IFNULL(sum(cc.importe),0) INTO vPAGO_ACUMULADO
    from orden_descuento_cobro_cuotas cc, orden_descuento_cobros co,orden_descuento_cuotas cu 
    where 
    cu.orden_descuento_id = vORDEN_ID
    and cc.orden_descuento_cuota_id = cu.id and cc.orden_descuento_cobro_id = co.id and
    co.periodo_cobro <= vPERIODO
    group by cu.orden_descuento_id;
end if;


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

if vTIPO = 'CAJ*' then
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

if vTIPO = 'LIQ*' then
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
CREATE DEFINER=`ipnorte`@`localhost` FUNCTION `FX_ORDENDTO_PENDIENTE_ACREDITAR`(`vORDEN_ID` INT, `vPERIODO` CHAR(6)) RETURNS decimal(10,2)
BEGIN
DECLARE vPENDIENTE DECIMAL(10,2);
SET vPENDIENTE = 0;
/*
SELECT ROUND(ifnull(SUM(importe_debitado),0),2) INTO vPENDIENTE FROM liquidacion_cuotas lc
inner join liquidaciones l on l.id = lc.liquidacion_id and l.periodo <=  vPERIODO
WHERE orden_descuento_id = vORDEN_ID
AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0;
*/
RETURN vPENDIENTE;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`ipnorte`@`localhost` FUNCTION `FX_ORDENDTO_SALDO_A_VENCER_POR_RANGO`(vORDEN_ID INT,vPERIODO CHAR(6),vDESDE INT,vHASTA INT) RETURNS decimal(10,2)
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
    AND co.periodo_cobro <= vPERIODO) /*+ ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc
        inner join liquidaciones l on l.id = lc.liquidacion_id and l.periodo <= vPERIODO
    WHERE orden_descuento_cuota_id = cu.id
    AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
    order by liquidacion_id desc limit 1 ),0)*/)
    GROUP BY cu.orden_descuento_id;
RETURN vSALDO;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`ipnorte`@`localhost` FUNCTION `FX_ORDENDTO_SALDO_VENCIDO_POR_RANGO`(vORDEN_ID INT,vPERIODO CHAR(6),vDESDE INT,vHASTA INT) RETURNS decimal(10,2)
BEGIN
DECLARE vSALDO DECIMAL(10,2) DEFAULT 0;

SELECT ifnull((SELECT SUM(importe) - SUM(ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
                where orden_descuento_cuotas.id = cc.orden_descuento_cuota_id
                and cc.periodo_cobro <= vPERIODO),0)) /*- SUM(ifnull((
                 select sum(importe_debitado) from liquidacion_cuotas lc
                where lc.orden_descuento_cuota_id = orden_descuento_cuotas.id and lc.imputada = 0 and 
                    lc.para_imputar = 1 and lc.periodo_cuota <= vPERIODO 
                    and ifnull(lc.orden_descuento_cobro_id,0) = 0)
                 ,0))*/ FROM orden_descuento_cuotas WHERE 
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
CREATE DEFINER=`ipnorte`@`localhost` FUNCTION `FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE`(
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
CREATE DEFINER=`ipnorte`@`localhost` FUNCTION `FX_SOLICITUD_GET_ORDENDTO`(vSOLICITUD_ID INT(11)) RETURNS int(11)
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
CREATE DEFINER=`ipnorte`@`localhost` FUNCTION `FX_VALIDA_CBU`(
CBU VARCHAR(22) 
) RETURNS int(11)
BEGIN
DECLARE POSICION INT;
DECLARE DIGITO INT;
DECLARE BLOQUE VARCHAR(14);
DECLARE SUMA INT;
DECLARE PONDERADOR VARCHAR(4);

SET PONDERADOR = '9713';
SET POSICION = 1;
SET SUMA = 0;

-- DIGITO VERIFICADOR 1
SET BLOQUE = SUBSTR(CBU,1,8);
SET DIGITO = SUBSTR(CBU,8,1);
WHILE POSICION <= LENGTH(BLOQUE) DO
	SET @VAL1 = CAST(SUBSTR(BLOQUE, (LENGTH(BLOQUE) - POSICION),1) AS UNSIGNED);  
    SET @VAL2 = CAST(SUBSTR(PONDERADOR ,CASE (POSICION % 4) WHEN 1 THEN 4 WHEN 2 THEN 3 WHEN 3 THEN 2 WHEN 0 THEN 1 END,1) AS UNSIGNED);
    SET SUMA = SUMA + (@VAL1 * @VAL2);
    SET POSICION = POSICION + 1;
END WHILE;
IF CAST(RIGHT(CAST(10-(SUMA%10) AS CHAR),1) AS UNSIGNED) <> DIGITO THEN
	RETURN 0;
END IF;
SET POSICION = 1;
SET SUMA = 0;
-- DIGITO VERIFICADOR 2
SET BLOQUE = SUBSTR(CBU,9,14);
SET DIGITO = SUBSTR(CBU,22,1);
WHILE POSICION <= LENGTH(BLOQUE) DO
	SET @VAL1 = CAST(SUBSTR(BLOQUE, (LENGTH(BLOQUE) - POSICION),1) AS UNSIGNED);   
    SET @VAL2 = CAST(SUBSTR(PONDERADOR ,CASE (POSICION % 4) WHEN 1 THEN 4 WHEN 2 THEN 3 WHEN 3 THEN 2 WHEN 0 THEN 1 END,1) AS UNSIGNED);
    SET SUMA = SUMA + (@VAL1 * @VAL2);
    SET POSICION = POSICION + 1;
END WHILE;
IF CAST(RIGHT(CAST(10-(SUMA%10) AS CHAR),1) AS UNSIGNED) <> DIGITO THEN
	RETURN 0;
END IF;
RETURN 1; 
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`ipnorte`@`localhost` FUNCTION `FX_VALIDA_CBU_DIGITO`(
BLOQUE VARCHAR(22)
) RETURNS int(11)
BEGIN
SET @PONDERADOR = '9713';
SET @POSICION = LENGTH(BLOQUE);
SET @SUMA = 0; 
SET @DIGITO = NULL;
SET @N = 1;

WHILE @POSICION > 0 DO

	SET @VALOR = SUBSTR(BLOQUE,@POSICION,1);
    SET @RESTO = ((4 - @N) % 4);
    IF @RESTO < 0 THEN SET @RESTO = @RESTO + 4; END IF;
    SET @PONDERADORVALOR = SUBSTR(@PONDERADOR,(@RESTO + 1),1);
	SET @SUMA = @SUMA + (@VALOR * @PONDERADORVALOR); 

    SET @POSICION = @POSICION - 1;
    SET @N = @N + 1;
    
END WHILE;

RETURN CAST(RIGHT(CAST(10-(@SUMA%10) AS CHAR),1) AS UNSIGNED);
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`ipnorte`@`localhost` FUNCTION `FX_VALIDA_CBU_GENDIGITO`(
BLOQUE VARCHAR(22)
) RETURNS int(11)
BEGIN
SET @PONDERADOR = '9713';
SET @POSICION = LENGTH(BLOQUE);
SET @SUMA = 0; 
SET @DIGITO = NULL;
SET @N = 1;

WHILE @POSICION > 0 DO

	SET @VALOR = SUBSTR(BLOQUE,@POSICION,1);
    SET @RESTO = ((4 - @N) % 4);
    IF @RESTO < 0 THEN SET @RESTO = @RESTO + 4; END IF;
    SET @PONDERADORVALOR = SUBSTR(@PONDERADOR,(@RESTO + 1),1);
	SET @SUMA = @SUMA + (@VALOR * @PONDERADORVALOR); 

    SET @POSICION = @POSICION - 1;
    SET @N = @N + 1;
    
END WHILE;

RETURN CAST(RIGHT(CAST(10-(@SUMA%10) AS CHAR),1) AS UNSIGNED);
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`ipnorte`@`localhost` FUNCTION `FX_VALIDA_CUIT`(CUIT BIGINT) RETURNS tinyint(1)
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
