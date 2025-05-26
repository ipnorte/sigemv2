DROP PROCEDURE IF EXISTS `SP_LIQUIDA_CUOTA_SERVICIOS`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_CUOTA_SOCIAL`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_ACUERDO_DEBITO`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_ADICIONALES`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_GESTION_MORA`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_LIMITES`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO_RENUMERA`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_SCORING`;
DROP PROCEDURE IF EXISTS `SP_VENCIMIENTOS`;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_CUOTA_SERVICIOS`(
vSOCIO_ID INT(11),
vPERIODO VARCHAR(6),
vORGANISMO VARCHAR(12)
)
BEGIN

DECLARE vCODIGO_ORGANISMO VARCHAR(12);
DECLARE vORDEN_ID INT(11);
DECLARE vSOLICITUD INT(11);
DECLARE vFECHA DATE;
DECLARE vPERIODO_HASTA VARCHAR(6);
DECLARE vTIPO_ORDEN VARCHAR(4);
DECLARE vTIPO_PRODUCTO VARCHAR(12);
DECLARE vTIPO_CUOTA VARCHAR(12);
DECLARE vBENEFICIO_ID INT(11);
DECLARE vPROVEEDOR_ID INT(11);
DECLARE vNRO_REFERENCIA_PROVEEDOR VARCHAR(10);
DECLARE vIMPORTE_ORDEN_CUOTA DECIMAL(10,2);
DECLARE vACTIVO BOOLEAN;


-- ----------------------------------------------------------
-- BUSCO LAS ORDENES PERMANENTES
-- ----------------------------------------------------------
SELECT
	PersonaBeneficio.codigo_beneficio,
	OrdenDescuento.id,
    OrdenDescuento.numero,
    OrdenDescuento.fecha,
    ifnull(OrdenDescuento.periodo_hasta,'999912'),
	OrdenDescuento.tipo_orden_dto,
	OrdenDescuento.tipo_producto,
    GlobalDato.concepto_2,
	OrdenDescuento.persona_beneficio_id,
	OrdenDescuento.proveedor_id,
	OrdenDescuento.nro_referencia_proveedor,
	OrdenDescuento.importe_cuota,
	OrdenDescuento.activo
INTO vCODIGO_ORGANISMO,vORDEN_ID,vSOLICITUD,vFECHA,vPERIODO_HASTA,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,vBENEFICIO_ID,vPROVEEDOR_ID,vNRO_REFERENCIA_PROVEEDOR,vIMPORTE_ORDEN_CUOTA,vACTIVO    
FROM 
	orden_descuentos as OrdenDescuento, 
	socios as Socio,
	persona_beneficios as PersonaBeneficio,
    global_datos as GlobalDato
WHERE
	Socio.id = vSOCIO_ID  
	AND OrdenDescuento.socio_id = Socio.id 
	AND OrdenDescuento.tipo_orden_dto <> 'CMUTU'
	AND OrdenDescuento.tipo_producto <> 'MUTUPROD0003'
    AND OrdenDescuento.tipo_producto = GlobalDato.id
	AND OrdenDescuento.periodo_ini <= vPERIODO
	AND IF(Socio.activo = 0,IFNULL(OrdenDescuento.periodo_hasta,vPERIODO),IF(ISNULL(OrdenDescuento.periodo_hasta) AND OrdenDescuento.activo = 1,'999999',OrdenDescuento.periodo_hasta)) > vPERIODO
	AND OrdenDescuento.persona_beneficio_id = PersonaBeneficio.id
	AND PersonaBeneficio.codigo_beneficio = vORGANISMO
	AND OrdenDescuento.permanente = 1;



IF vORDEN_ID <> 0 THEN

	set @orden_descuento_cuota_id = null;
	select odc.id INTO @orden_descuento_cuota_id from orden_descuento_cuotas odc, persona_beneficios be 
			where odc.orden_descuento_id = vORDEN_ID and odc.periodo = vPERIODO
			and odc.persona_beneficio_id = be.id    
			and odc.tipo_cuota = vTIPO_CUOTA
            and odc.estado <> 'B'
			and be.codigo_beneficio = vORGANISMO;

	SET @IMPORTE_CUOTA_SERVICIO = 0;
	SET @importe_fijo_producto = 0;

	-- ----------------------------------------------------------
	-- SACO EL IMPORTE DE LA TABLA DE PRODUCTOS
    -- ----------------------------------------------------------
	select MutualProducto.importe_fijo 
	INTO @importe_fijo_producto
	from mutual_productos as MutualProducto 
	where MutualProducto.tipo_producto = vTIPO_PRODUCTO 
    and MutualProducto.proveedor_id = vPROVEEDOR_ID 
    AND importe_fijo <> 0;

	SET @IMPORTE_CUOTA_SERVICIO = IF(vIMPORTE_ORDEN_CUOTA = 0,@importe_fijo_producto,vIMPORTE_ORDEN_CUOTA);

	-- ----------------------------------------------------------
    -- SERVICIOS
    -- ----------------------------------------------------------
	IF vTIPO_ORDEN = 'SERV' THEN

        -- SACO LOS VALORES VIGENTES
        
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
        
        UPDATE mutual_servicio_solicitudes set importe_mensual = @importe_titular,
        importe_mensual_total = @IMPORTE_SERVICIO_MENSUAL
        where id = vSOLICITUD;
        
        update mutual_servicio_solicitud_adicionales
        set importe_mensual = @costo_adicional
        where mutual_servicio_solicitud_id = vSOLICITUD 
        and ifnull(periodo_hasta,'000000') <=  vPERIODO;
        
        update orden_descuentos
        set importe_total = @IMPORTE_SERVICIO_MENSUAL, importe_cuota = @IMPORTE_SERVICIO_MENSUAL
        where id = vORDEN_ID;
        
        SET @IMPORTE_CUOTA_SERVICIO = round(@IMPORTE_SERVICIO_MENSUAL,2);
        
    END IF;
	
    SET @IMPORTE_CUOTA_SERVICIO = IF(vPERIODO_HASTA > vPERIODO,@IMPORTE_CUOTA_SERVICIO,0);

	IF @IMPORTE_CUOTA_SERVICIO > 0 THEN
    
		CALL SP_VENCIMIENTOS(NULL,
		vPROVEEDOR_ID,vORGANISMO,vPERIODO,vFECHA,
		@PERIODO_INI,@VTO_SOCIO,@VTO_PROVEEDOR,@ULTIMO_PERIODO); 
        
    IF @orden_descuento_cuota_id IS NOT NULL AND vORDEN_ID IS NOT NULL THEN 
		UPDATE orden_descuento_cuotas 
        SET importe = @IMPORTE_CUOTA_SERVICIO 
        WHERE id = @orden_descuento_cuota_id;
    ELSE 
    
		IF vORDEN_ID IS NOT NULL THEN
			INSERT INTO orden_descuento_cuotas(orden_descuento_id, 
			socio_id, persona_beneficio_id, tipo_orden_dto, 
			tipo_producto, tipo_cuota, periodo, estado, situacion, 
			vencimiento, vencimiento_proveedor, 
			nro_cuota, importe, proveedor_id, 
			nro_referencia_proveedor) 
			VALUES(vORDEN_ID,vSOCIO_ID,vBENEFICIO_ID,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,
			vPERIODO,'A','MUTUSICUMUTU',@VTO_SOCIO,@VTO_PROVEEDOR,0,
			@IMPORTE_CUOTA_SERVICIO,vPROVEEDOR_ID,vNRO_REFERENCIA_PROVEEDOR);
        END IF;
    END IF;        
    
    END IF;

	-- select @IMPORTE_CUOTA_SERVICIO,vCODIGO_ORGANISMO,vORDEN_ID,vSOLICITUD,vFECHA,vPERIODO_HASTA,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,vBENEFICIO_ID,vPROVEEDOR_ID,vNRO_REFERENCIA_PROVEEDOR,vIMPORTE_ORDEN_CUOTA,vACTIVO;

END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_CUOTA_SOCIAL`(
vSOCIO_ID INT(11),
vPERIODO VARCHAR(6),
vORGANISMO VARCHAR(12)
)
BEGIN
DECLARE vCODIGO_ORGANISMO VARCHAR(12);
DECLARE vORDEN_ID INT(11);
DECLARE vFECHA DATE;
DECLARE vPERIODO_HASTA VARCHAR(6);
DECLARE vTIPO_ORDEN VARCHAR(5);
DECLARE vTIPO_PRODUCTO VARCHAR(12);
DECLARE vCODIGO_EMPRESA VARCHAR(12);
DECLARE vTIPO_CUOTA VARCHAR(12);
DECLARE vBENEFICIO_ID INT(11);
DECLARE vPROVEEDOR_ID INT(11);
DECLARE vNRO_REFERENCIA_PROVEEDOR VARCHAR(10);
DECLARE vIMPORTE_ORDEN_CUOTA DECIMAL(10,2);
DECLARE vACTIVO BOOLEAN;

DECLARE vIMPORTE_CUOTA_SOCIAL DECIMAL(10,2);
DECLARE vLIQUIDA_SOLO_DEUDA BOOLEAN;

SET vIMPORTE_CUOTA_SOCIAL = 0;
SET vLIQUIDA_SOLO_DEUDA = FALSE;

SELECT
	PersonaBeneficio.codigo_beneficio,
    PersonaBeneficio.codigo_empresa,
	OrdenDescuento.id,
    OrdenDescuento.fecha,
    ifnull(OrdenDescuento.periodo_hasta,'999912'),
	OrdenDescuento.tipo_orden_dto,
	OrdenDescuento.tipo_producto,
    GlobalDato.concepto_2,
	OrdenDescuento.persona_beneficio_id,
	OrdenDescuento.proveedor_id,
	OrdenDescuento.nro_referencia_proveedor,
	OrdenDescuento.importe_cuota,
	OrdenDescuento.activo
INTO vCODIGO_ORGANISMO,vCODIGO_EMPRESA,vORDEN_ID,vFECHA,vPERIODO_HASTA,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,vBENEFICIO_ID,vPROVEEDOR_ID,vNRO_REFERENCIA_PROVEEDOR,vIMPORTE_ORDEN_CUOTA,vACTIVO    
FROM 
	orden_descuentos as OrdenDescuento, 
	socios as Socio,
	persona_beneficios as PersonaBeneficio,
    global_datos as GlobalDato
WHERE
	Socio.id = vSOCIO_ID  
	AND OrdenDescuento.socio_id = Socio.id 
	AND OrdenDescuento.tipo_orden_dto = 'CMUTU'
	AND OrdenDescuento.tipo_producto = 'MUTUPROD0003'
    AND OrdenDescuento.tipo_producto = GlobalDato.id
	AND OrdenDescuento.periodo_ini <= vPERIODO
	AND IF(Socio.activo = 0,IFNULL(OrdenDescuento.periodo_hasta,vPERIODO),IF(ISNULL(OrdenDescuento.periodo_hasta) AND OrdenDescuento.activo = 1,'999999',OrdenDescuento.periodo_hasta)) > vPERIODO
	AND OrdenDescuento.persona_beneficio_id = PersonaBeneficio.id
	AND PersonaBeneficio.codigo_beneficio = vORGANISMO
	AND OrdenDescuento.permanente = 1
	AND OrdenDescuento.activo = 1;

-- SELECT vCODIGO_ORGANISMO,vCODIGO_EMPRESA,vORDEN_ID,vFECHA,vPERIODO_HASTA,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,vBENEFICIO_ID,vPROVEEDOR_ID,vNRO_REFERENCIA_PROVEEDOR,vIMPORTE_ORDEN_CUOTA,vACTIVO;

set @orden_descuento_cuota_id = null;
select odc.id INTO @orden_descuento_cuota_id from orden_descuento_cuotas odc, persona_beneficios be 
		where odc.orden_descuento_id = vORDEN_ID and odc.periodo = vPERIODO
		and odc.persona_beneficio_id = be.id    
		and odc.tipo_cuota = 'MUTUTCUOCSOC' and odc.estado <> 'B'
		and be.codigo_beneficio = vORGANISMO;

select decimal_1,logico_1 into vIMPORTE_CUOTA_SOCIAL, vLIQUIDA_SOLO_DEUDA 
from global_datos where id = concat('MUTUCUOS',substring(vORGANISMO,9,4));

SET vLIQUIDA_SOLO_DEUDA = IFNULL(vLIQUIDA_SOLO_DEUDA,FALSE);
SET vLIQUIDA_SOLO_DEUDA = IF(vLIQUIDA_SOLO_DEUDA = 0, FALSE,TRUE);
-- SELECT vIMPORTE_CUOTA_SOCIAL;
-- SELECT vLIQUIDA_SOLO_DEUDA;

SELECT ifnull(decimal_1,0) into @cuota_empresa from global_datos where id = vCODIGO_EMPRESA;
IF @cuota_empresa <> 0 THEN SET vIMPORTE_CUOTA_SOCIAL = @cuota_empresa; END IF;

IF substring(vORGANISMO,9,2) = '66' THEN
	SELECT importe_cuota_social into vIMPORTE_CUOTA_SOCIAL FROM socios WHERE id = vSOCIO_ID;
END IF;

-- ------------------------------------------------------------------
-- ANALIZO LA CUOTA SOCIAL DIFERENCIADA
-- ------------------------------------------------------------------
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
	SELECT cuota_social_diferenciada INTO @cuota_social_diferenciada FROM mutual_productos where tipo_producto = vTIPO_PRODUCTO
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

-- SELECT vIMPORTE_CUOTA_SOCIAL;
SET vIMPORTE_CUOTA_SOCIAL = IFNULL(vIMPORTE_CUOTA_SOCIAL,0);
-- SELECT vIMPORTE_CUOTA_SOCIAL;

IF vIMPORTE_CUOTA_SOCIAL > 0 THEN

	CALL SP_VENCIMIENTOS(NULL,
	vPROVEEDOR_ID,vORGANISMO,vPERIODO,vFECHA,
	@PERIODO_INI,@VTO_SOCIO,@VTO_PROVEEDOR,@ULTIMO_PERIODO);
    
    -- SELECT vORDEN_ID,@orden_descuento_cuota_id;
    
    IF @orden_descuento_cuota_id IS NOT NULL AND vORDEN_ID IS NOT NULL THEN 
		UPDATE orden_descuento_cuotas 
        SET importe = vIMPORTE_CUOTA_SOCIAL 
        WHERE id = @orden_descuento_cuota_id;
    ELSE 
    
		IF vORDEN_ID IS NOT NULL THEN
			INSERT INTO orden_descuento_cuotas(orden_descuento_id, 
			socio_id, persona_beneficio_id, tipo_orden_dto, 
			tipo_producto, tipo_cuota, periodo, estado, situacion, 
			vencimiento, vencimiento_proveedor, 
			nro_cuota, importe, proveedor_id, 
			nro_referencia_proveedor) 
			VALUES(vORDEN_ID,vSOCIO_ID,vBENEFICIO_ID,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,
			vPERIODO,'A','MUTUSICUMUTU',@VTO_SOCIO,@VTO_PROVEEDOR,0,
			vIMPORTE_CUOTA_SOCIAL,vPROVEEDOR_ID,vNRO_REFERENCIA_PROVEEDOR);
        END IF;
    END IF;

	/*
	SELECT @orden_descuento_cuota_id,vIMPORTE_CUOTA_SOCIAL,vCODIGO_ORGANISMO,vORDEN_ID,
	vFECHA,vPERIODO_HASTA,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,vBENEFICIO_ID,vPROVEEDOR_ID,
	vNRO_REFERENCIA_PROVEEDOR,vIMPORTE_ORDEN_CUOTA,vACTIVO,
	@VTO_SOCIO,@VTO_PROVEEDOR,@ULTIMO_PERIODO;
	*/

END IF;

    

    
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU`(
vSOCIO_ID INT(11),
vPERIODO VARCHAR(6),
vORGANISMO VARCHAR(12),
vPRE_IMPUTACION BOOLEAN
)
BEGIN

-- CALL SP_LIQUIDA_DEUDA(97,'201502','MUTUCORG2202',127,FALSE,'MUTUSICUMUTU');
DECLARE vLIQUIDACION_ID INT(11);
SELECT id into vLIQUIDACION_ID FROM liquidaciones where periodo = vPERIODO 
and codigo_organismo = vORGANISMO;

-- //////////////////////////////////////////////////////////////////////
-- BORRO LA LIQUIDACION ANTERIOR
-- //////////////////////////////////////////////////////////////////////

delete from mutual_adicional_pendientes where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
delete from liquidacion_cuotas where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
delete from liquidacion_socios where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;

-- //////////////////////////////////////////////////////////////////////
-- GENERO LA CUOTA SOCIAL y CUOTA SERVICIOS
-- //////////////////////////////////////////////////////////////////////
CALL SP_LIQUIDA_CUOTA_SOCIAL(vSOCIO_ID,vPERIODO,vORGANISMO);
CALL SP_LIQUIDA_CUOTA_SERVICIOS(vSOCIO_ID,vPERIODO,vORGANISMO);

-- //////////////////////////////////////////////////////////////////////
-- VERIFICAR QUE NO TENGA STOP DEBIT
-- //////////////////////////////////////////////////////////////////////

select calificacion into @CALIFICACION from socio_calificaciones where socio_id = vSOCIO_ID order by created desc limit 1;
SET @STOP_DEBIT = IF(IFNULL(@CALIFICACION,'') = 'MUTUCALISDEB',TRUE,FALSE);

-- //////////////////////////////////////////////////////////////////////
-- SACO LAS CUOTAS ADEUDADAS
-- //////////////////////////////////////////////////////////////////////
IF vPRE_IMPUTACION = FALSE THEN
	insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,
	orden_descuento_id,orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,
	tipo_cuota,periodo_cuota,proveedor_id,vencida,importe,
	saldo_actual,codigo_organismo)
	SELECT 
		vLIQUIDACION_ID,
		OrdenDescuentoCuota.socio_id,
		OrdenDescuentoCuota.persona_beneficio_id,
		OrdenDescuentoCuota.orden_descuento_id,
		OrdenDescuentoCuota.id,
		OrdenDescuentoCuota.tipo_orden_dto,
		OrdenDescuentoCuota.tipo_producto,
		OrdenDescuentoCuota.tipo_cuota,
		OrdenDescuentoCuota.periodo,
		OrdenDescuentoCuota.proveedor_id,
		IF(OrdenDescuentoCuota.periodo = vPERIODO, 0 , 1) as vencida,
		OrdenDescuentoCuota.importe,
		OrdenDescuentoCuota.importe - IFNULL((SELECT SUM(cocu.importe)
		FROM orden_descuento_cobro_cuotas cocu
		WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0) AS saldo_actual,    
		PersonaBeneficio.codigo_beneficio
	  
	FROM orden_descuento_cuotas AS OrdenDescuentoCuota
	INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
	WHERE 
		OrdenDescuentoCuota.socio_id = vSOCIO_ID
		AND OrdenDescuentoCuota.estado <> 'B' 
		AND PersonaBeneficio.codigo_beneficio = vORGANISMO
		AND OrdenDescuentoCuota.periodo <= vPERIODO
		-- AND OrdenDescuentoCuota.situacion = vSITUACION
		AND OrdenDescuentoCuota.importe > IFNULL((SELECT SUM(cocu.importe)
		FROM orden_descuento_cobro_cuotas cocu, orden_descuento_cobros co
		WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
		AND cocu.orden_descuento_cobro_id = co.id
		AND co.periodo_cobro <= vPERIODO),0)
        AND @STOP_DEBIT = FALSE;
        
ELSE
	insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,
	orden_descuento_id,orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,
	tipo_cuota,periodo_cuota,proveedor_id,vencida,importe,
	saldo_actual,codigo_organismo)
	SELECT 
		vLIQUIDACION_ID,
		OrdenDescuentoCuota.socio_id,
		OrdenDescuentoCuota.persona_beneficio_id,
		OrdenDescuentoCuota.orden_descuento_id,
		OrdenDescuentoCuota.id,
		OrdenDescuentoCuota.tipo_orden_dto,
		OrdenDescuentoCuota.tipo_producto,
		OrdenDescuentoCuota.tipo_cuota,
		OrdenDescuentoCuota.periodo,
		OrdenDescuentoCuota.proveedor_id,
		IF(OrdenDescuentoCuota.periodo = vPERIODO, 0 , 1) as vencida,
		OrdenDescuentoCuota.importe,
		OrdenDescuentoCuota.importe - IFNULL((SELECT SUM(cocu.importe)
		FROM orden_descuento_cobro_cuotas cocu
		WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0) 
        -
        (SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas 
		WHERE orden_descuento_cuota_id = OrdenDescuentoCuota.id
		AND para_imputar = 1 AND imputada = 0 AND orden_descuento_cobro_id = 0
		order by liquidacion_id desc limit 1)
        AS saldo_actual,    
		PersonaBeneficio.codigo_beneficio
	  
	FROM orden_descuento_cuotas AS OrdenDescuentoCuota
	INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
	WHERE 
		OrdenDescuentoCuota.socio_id = vSOCIO_ID
		AND OrdenDescuentoCuota.estado <> 'B' 
		AND PersonaBeneficio.codigo_beneficio = vORGANISMO
		AND OrdenDescuentoCuota.periodo <= vPERIODO
		-- AND OrdenDescuentoCuota.situacion = vSITUACION
		AND OrdenDescuentoCuota.importe > (IFNULL((SELECT SUM(cocu.importe)
		FROM orden_descuento_cobro_cuotas cocu, orden_descuento_cobros co
		WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
		AND cocu.orden_descuento_cobro_id = co.id
		AND co.periodo_cobro <= vPERIODO),0) + (SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas 
		WHERE orden_descuento_cuota_id = OrdenDescuentoCuota.id
		AND para_imputar = 1 AND imputada = 0 AND orden_descuento_cobro_id = 0
		order by liquidacion_id desc limit 1))
        AND @STOP_DEBIT = FALSE;
        
END IF;

-- /////////////////////////////////////////////////////////////////////
-- CALCULAR LOS ADICIONALES
-- /////////////////////////////////////////////////////////////////////   
CALL SP_LIQUIDA_DEUDA_CBU_ADICIONALES(vSOCIO_ID,vPERIODO,vORGANISMO,vLIQUIDACION_ID);

  
-- /////////////////////////////////////////////////////////////////////
-- GENERO LA CABECERA DE LA LIQUIDACION
-- ///////////////////////////////////////////////////////////////////// 
insert into liquidacion_socios(liquidacion_id,socio_id,
persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
codigo_empresa,codigo_reparticion,turno_pago,periodo,importe_dto,importe_adebitar,
formula_criterio_deuda)
SELECT 
	LiquidacionCuota.liquidacion_id,
	LiquidacionCuota.socio_id,
	LiquidacionCuota.persona_beneficio_id,
    LiquidacionCuota.codigo_organismo,
    PersonaBeneficio.banco_id,
    PersonaBeneficio.nro_sucursal,
    PersonaBeneficio.tipo_cta_bco,
    PersonaBeneficio.nro_cta_bco,
    PersonaBeneficio.cbu,
    Persona.tipo_documento,
    Persona.documento,
    concat(Persona.apellido,', ',Persona.nombre),
    Persona.id,
    Persona.cuit_cuil,
    PersonaBeneficio.codigo_empresa,
    PersonaBeneficio.codigo_reparticion,
    PersonaBeneficio.turno_pago,
    1,
	sum(saldo_actual) as deuda,
	sum(saldo_actual) as importe_adebitar,
    '*** IMPORTE PERIODO ***'
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (
    PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id
    and PersonaBeneficio.codigo_beneficio = LiquidacionCuota.codigo_organismo)
    INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
	AND LiquidacionCuota.socio_id = vSOCIO_ID
	AND LiquidacionCuota.periodo_cuota = vPERIODO					
GROUP BY
	LiquidacionCuota.codigo_organismo,
	LiquidacionCuota.socio_id,
    PersonaBeneficio.turno_pago,
	PersonaBeneficio.cbu
UNION
SELECT 
	LiquidacionCuota.liquidacion_id,
	LiquidacionCuota.socio_id,
	LiquidacionCuota.persona_beneficio_id,
    LiquidacionCuota.codigo_organismo,
    PersonaBeneficio.banco_id,
    PersonaBeneficio.nro_sucursal,
    PersonaBeneficio.tipo_cta_bco,
    PersonaBeneficio.nro_cta_bco,
    PersonaBeneficio.cbu,
    Persona.tipo_documento,
    Persona.documento,
    concat(Persona.apellido,', ',Persona.nombre),
    Persona.id,
    Persona.cuit_cuil,
    PersonaBeneficio.codigo_empresa,
    PersonaBeneficio.codigo_reparticion,
    PersonaBeneficio.turno_pago,
    0,
	sum(saldo_actual) as deuda,
	sum(saldo_actual) as importe_adebitar,
    '*** IMPORTE MORA ***'
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (
    PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id
    and PersonaBeneficio.codigo_beneficio = LiquidacionCuota.codigo_organismo)
    INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
	AND LiquidacionCuota.socio_id = vSOCIO_ID
	AND LiquidacionCuota.periodo_cuota < vPERIODO					
GROUP BY
	LiquidacionCuota.codigo_organismo,
	LiquidacionCuota.socio_id,
    PersonaBeneficio.turno_pago,
	PersonaBeneficio.cbu;

-- /////////////////////////////////////////////////////////////////////
-- TRATAMIENTO DE LA MORA
-- ///////////////////////////////////////////////////////////////////// 
CALL SP_LIQUIDA_DEUDA_CBU_GESTION_MORA(vSOCIO_ID,vORGANISMO,vLIQUIDACION_ID);
CALL SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO(vSOCIO_ID,vORGANISMO,vLIQUIDACION_ID);
-- /////////////////////////////////////////////////////////////////////
-- VERIFICO ACUERDOS DE DEBITO
-- /////////////////////////////////////////////////////////////////////    
CALL SP_LIQUIDA_DEUDA_CBU_ACUERDO_DEBITO(vSOCIO_ID,vLIQUIDACION_ID);
CALL SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO_RENUMERA(vSOCIO_ID,vLIQUIDACION_ID);
CALL SP_LIQUIDA_DEUDA_SCORING(vSOCIO_ID,vLIQUIDACION_ID);
    
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_ACUERDO_DEBITO`(
vSOCIO_ID INT(11),
vLIQUIDACION_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;

DECLARE vMONTO_ACUERDO DECIMAL(10,2) DEFAULT 0;
DECLARE vMONTO_MAX_DTO_BENEFICIO DECIMAL(10,2) DEFAULT 0;
DECLARE vCON_ACUERDO BOOLEAN DEFAULT FALSE;
DECLARE vBENEFICIO_ID INT(11) DEFAULT 0;

DECLARE vSALDO DECIMAL(10,2);
DECLARE vSALDO_ACUMULADO DECIMAL(10,2);

DECLARE c_beneficios_reset_acuerdo CURSOR FOR 
select PersonaBeneficio.id from persona_beneficios as PersonaBeneficio
where acuerdo_debito > (select sum(saldo_actual)        
from liquidacion_cuotas
where liquidacion_cuotas.liquidacion_id = vLIQUIDACION_ID and 
liquidacion_cuotas.socio_id = vSOCIO_ID and 
liquidacion_cuotas.persona_beneficio_id = PersonaBeneficio.id)
group by PersonaBeneficio.id;


DECLARE c_beneficios_acuerdo CURSOR FOR 
SELECT PersonaBeneficio.id,PersonaBeneficio.acuerdo_debito,
PersonaBeneficio.importe_max_registro_cbu 
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (
    PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id
    and PersonaBeneficio.codigo_beneficio = LiquidacionCuota.codigo_organismo)
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
	AND LiquidacionCuota.socio_id = vSOCIO_ID
	AND PersonaBeneficio.acuerdo_debito <> 0
    GROUP BY PersonaBeneficio.id;
-- ////////////////////////////////////////////////////////////////////////
-- ACUERDO DE DEBITO
-- ////////////////////////////////////////////////////////////////////////
DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row = 1;
-- DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row_2 = 1;

-- SET @LIMITE_1 = @LIMITE_2 = @TOPE_DEBITO_XREC = @TOPE_DEBITO_MAXIMO = @IMPORTE_MINIMO_REGISTRO = 0;
-- CALL SP_LIQUIDA_DEUDA_CBU_LIMITES(vORGANISMO,@LIMITE_1,@LIMITE_2,@TOPE_DEBITO_XREC,@TOPE_DEBITO_MAXIMO,@IMPORTE_MINIMO_REGISTRO);



OPEN c_beneficios_reset_acuerdo;
c1_loop_beneficios: LOOP
FETCH c_beneficios_reset_acuerdo INTO vBENEFICIO_ID;
		IF (l_last_row = 1) THEN
			LEAVE c1_loop_beneficios; 
		END IF;
        UPDATE persona_beneficios set acuerdo_debito = 0 where id = vBENEFICIO_ID;       
END LOOP c1_loop_beneficios; 
CLOSE c_beneficios_reset_acuerdo;

SET vSALDO = 0;
SET vSALDO_ACUMULADO = 0;
SET @REGISTRO = 1;
SET l_last_row = 0;
OPEN c_beneficios_acuerdo;
c1_loop_beneficios_acuerdo: LOOP
FETCH c_beneficios_acuerdo INTO vBENEFICIO_ID,vMONTO_ACUERDO,vMONTO_MAX_DTO_BENEFICIO;
		
        IF (l_last_row = 1) THEN
			LEAVE c1_loop_beneficios_acuerdo; 
		END IF;

		SET @ULTIMO_ID = 0;
        SET vSALDO = 0;

		DELETE FROM liquidacion_socios where 
		liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID and
		persona_beneficio_id = vBENEFICIO_ID;        



		IF CAST(vMONTO_ACUERDO / vMONTO_MAX_DTO_BENEFICIO AS UNSIGNED) > 1 AND vMONTO_MAX_DTO_BENEFICIO > 0 THEN
        
			SET vSALDO = vMONTO_ACUERDO;
            WHILE vSALDO > @IMPORTE_MINIMO_REGISTRO DO
            
				SET @IMPO_DEBITO = vMONTO_MAX_DTO_BENEFICIO;
				
				IF vSALDO <= vMONTO_MAX_DTO_BENEFICIO THEN
					SET @IMPO_DEBITO = vSALDO;
				END IF;            
            
				
                insert into liquidacion_socios(liquidacion_id,socio_id,
				persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
				nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
				codigo_empresa,codigo_reparticion,turno_pago,periodo,importe_dto,importe_adebitar,
				formula_criterio_deuda)
                
				SELECT 
					LiquidacionCuota.liquidacion_id,
					LiquidacionCuota.socio_id,
					LiquidacionCuota.persona_beneficio_id,
					LiquidacionCuota.codigo_organismo,
					PersonaBeneficio.banco_id,
					PersonaBeneficio.nro_sucursal,
					PersonaBeneficio.tipo_cta_bco,
					PersonaBeneficio.nro_cta_bco,
					PersonaBeneficio.cbu,
					Persona.tipo_documento,
					Persona.documento,
					concat(Persona.apellido,', ',Persona.nombre),
					Persona.id,
					Persona.cuit_cuil,
					PersonaBeneficio.codigo_empresa,
					PersonaBeneficio.codigo_reparticion,
					PersonaBeneficio.turno_pago,
					0,
					IF(@REGISTRO = 1,vMONTO_ACUERDO,0) as deuda,
					@IMPO_DEBITO,
					concat('*** CON ACUERDO DE DEBITO ', vMONTO_ACUERDO ,' | FRACCION = ',@IMPO_DEBITO,' ***')
				FROM 
					liquidacion_cuotas as LiquidacionCuota 
					INNER JOIN persona_beneficios as PersonaBeneficio on (
					PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id
					and PersonaBeneficio.codigo_beneficio = LiquidacionCuota.codigo_organismo)
					INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
				WHERE 
					LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
					AND LiquidacionCuota.socio_id = vSOCIO_ID
					AND PersonaBeneficio.id = vBENEFICIO_ID
                GROUP BY PersonaBeneficio.id;    
                
				SET @ULTIMO_ID = LAST_INSERT_ID(); 
            
				-- SELECT vBENEFICIO_ID,vMONTO_ACUERDO,vMONTO_MAX_DTO_BENEFICIO,vSALDO,vSALDO_ACUMULADO;
                
                SET vSALDO_ACUMULADO = vSALDO_ACUMULADO + @IMPO_DEBITO;
				
                SET vSALDO = vSALDO - @IMPO_DEBITO;
                SET @REGISTRO = @REGISTRO + 1;  
            
            END WHILE;
            
			IF vSALDO > 0 THEN
				UPDATE liquidacion_socios 
				SET importe_adebitar = importe_adebitar + vSALDO WHERE id = @ULTIMO_ID; 
			END IF;             
        
        ELSE
        
			insert into liquidacion_socios(liquidacion_id,socio_id,
			persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
			nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
			codigo_empresa,codigo_reparticion,turno_pago,periodo,importe_dto,importe_adebitar,
			formula_criterio_deuda)
			SELECT 
				LiquidacionCuota.liquidacion_id,
				LiquidacionCuota.socio_id,
				LiquidacionCuota.persona_beneficio_id,
				LiquidacionCuota.codigo_organismo,
				PersonaBeneficio.banco_id,
				PersonaBeneficio.nro_sucursal,
				PersonaBeneficio.tipo_cta_bco,
				PersonaBeneficio.nro_cta_bco,
				PersonaBeneficio.cbu,
				Persona.tipo_documento,
				Persona.documento,
				concat(Persona.apellido,', ',Persona.nombre),
				Persona.id,
				Persona.cuit_cuil,
				PersonaBeneficio.codigo_empresa,
				PersonaBeneficio.codigo_reparticion,
				PersonaBeneficio.turno_pago,
				0,
				sum(saldo_actual) as deuda,
				PersonaBeneficio.acuerdo_debito,
				concat('*** CON ACUERDO DE DEBITO = ',vMONTO_ACUERDO,' ***')
			FROM 
				liquidacion_cuotas as LiquidacionCuota 
				INNER JOIN persona_beneficios as PersonaBeneficio on (
				PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id
				and PersonaBeneficio.codigo_beneficio = LiquidacionCuota.codigo_organismo)
				INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
			WHERE 
				LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
				AND LiquidacionCuota.socio_id = vSOCIO_ID
				AND PersonaBeneficio.id = vBENEFICIO_ID;        
        
        END IF;

        
END LOOP c1_loop_beneficios_acuerdo;  
CLOSE c_beneficios_acuerdo;       
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_ADICIONALES`(
	vSOCIO_ID INT(11),
	vPERIODO VARCHAR(6),
	vORGANISMO VARCHAR(12),
	vLIQUIDACION_ID INT(11)
)
BEGIN
-- CALL SP_LIQUIDA_DEUDA_ADICIONALES(97,'201502','MUTUCORG2202',127);
DECLARE l_last_row INT DEFAULT 0;
DECLARE vPROVEEDOR_ID INT(11) DEFAULT 0;
DECLARE vIMPUTAR_PROVEEDOR_ID INT(11) DEFAULT 0;
DECLARE vTIPO CHAR(1);
DECLARE vVALOR DECIMAL(10,2);
DECLARE vDEVENGA BOOLEAN;
DECLARE vCALCULO INT(11);
DECLARE vTIPO_CUOTA VARCHAR(12);
DECLARE vACTIVO BOOLEAN;
DECLARE c_adicionales CURSOR FOR 
select proveedor_id,imputar_proveedor_id,
tipo,valor,devengado_previo,deuda_calcula,tipo_cuota,activo
from mutual_adicionales
where codigo_organismo = vORGANISMO and valor > 0
and ifnull(periodo_desde,'000000') <= vPERIODO
and ifnull(periodo_hasta,'999912') >= vPERIODO;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;
OPEN c_adicionales;
c1_loop: LOOP
	FETCH c_adicionales INTO vPROVEEDOR_ID,vIMPUTAR_PROVEEDOR_ID,vTIPO,vVALOR,vDEVENGA,vCALCULO,vTIPO_CUOTA,vACTIVO;
		
        IF (l_last_row = 1) THEN
			LEAVE c1_loop; 
		END IF;	    
        
		-- select vPROVEEDOR_ID,vIMPUTAR_PROVEEDOR_ID,vTIPO,vVALOR,vDEVENGA,vCALCULO,vTIPO_CUOTA,vACTIVO;
		
        IF (vVALOR <> 0) THEN
        
			set @saldo = 0;
            set @ordenDtoId = 0;
            set @beneficioId = 0;
            set @tipoProducto = null;
        
			-- SACAR LA ORDEN DE DESCUENTO A DONDE SE VA A CARGAR EN BASE AL PROVEEDOR AL CUAL SE IMPUTA
			set @STMT = CONCAT('select persona_beneficio_id,(select id from orden_descuentos where tipo_orden_dto = \'CMUTU\' and activo = 1 and socio_id = liquidacion_cuotas.socio_id order by id desc limit 1) as orden_id,(select tipo_producto from orden_descuentos where tipo_orden_dto = \'CMUTU\' and activo = 1 and socio_id = liquidacion_cuotas.socio_id order by id desc limit 1) as tipo_producto,sum(saldo_actual) into @beneficioId,@ordenDtoId,@tipoProducto,@saldo FROM liquidacion_cuotas where liquidacion_id = ? and socio_id = ? and ifnull(mutual_adicional_pendiente_id,0) = 0');
			
            IF vPROVEEDOR_ID is not null THEN
				SET @STMT = CONCAT(@STMT,' AND proveedor_id = ? ');             
			END IF;
            IF vCALCULO = 1 THEN
				SET @STMT = CONCAT(@STMT,' AND periodo_cuota <= ? '); 
            END IF;
            IF vCALCULO = 2 THEN
				SET @STMT = CONCAT(@STMT,' AND periodo_cuota < ? '); 
            END IF;            
            IF vCALCULO = 3 THEN
				SET @STMT = CONCAT(@STMT,' AND periodo_cuota = ? '); 
            END IF;

            PREPARE STMT FROM @STMT;
            
            SET @LIQ = vLIQUIDACION_ID;
            SET @SOCIO = vSOCIO_ID;
            SET @PERIODO = vPERIODO;
            
            EXECUTE STMT USING @LIQ,@SOCIO,@PERIODO;
            
            DEALLOCATE PREPARE STMT;
			SET @ADICIONAL = 0;
			IF vVALOR <> 0 AND @saldo <> 0 THEN
				SET @ADICIONAL = IF(vTIPO = 'P',ROUND(@saldo * vVALOR / 100,2),vVALOR);
            END IF;
            
			IF @ADICIONAL <> 0 AND vDEVENGA = 0 AND vACTIVO = TRUE THEN
                
                
                insert into mutual_adicional_pendientes(liquidacion_id,socio_id,codigo_organismo,proveedor_id,
                tipo,deuda_calcula,valor,tipo_cuota,periodo,total_deuda,importe,
                orden_descuento_id,persona_beneficio_id)
                values(vLIQUIDACION_ID,vSOCIO_ID,vORGANISMO,vIMPUTAR_PROVEEDOR_ID,
                vTIPO,vCALCULO,vVALOR,vTIPO_CUOTA,vPERIODO,@saldo,@ADICIONAL,@ordenDtoId,@beneficioId);
                
                set @adicional_id = last_insert_id();
                
                insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,orden_descuento_id,
						orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,tipo_cuota,
						periodo_cuota,proveedor_id,vencida,importe,saldo_actual,codigo_organismo,
                        mutual_adicional_pendiente_id
				)
                values(vLIQUIDACION_ID,vSOCIO_ID,@beneficioId,
                @ordenDtoId,null,'CMUTU',@tipoProducto,vTIPO_CUOTA,
                vPERIODO,vIMPUTAR_PROVEEDOR_ID,0,@ADICIONAL,@ADICIONAL,vORGANISMO,@adicional_id);
				
                -- SELECT vLIQUIDACION_ID,vSOCIO_ID,vORGANISMO,vIMPUTAR_PROVEEDOR_ID,
                -- vTIPO,vCALCULO,vVALOR,vTIPO_CUOTA,vPERIODO,@saldo,@ADICIONAL,@ordenDtoId,@beneficioId;
                -- select @saldo,@ADICIONAL,@orden_dto_id,@beneficio_id;
                
            END IF;
            
            IF @ADICIONAL <> 0 AND vDEVENGA = 1 THEN
            
				-- VERIFICAR QUE NO EXISTA LA CUOTA
                SET @CUOTA_EXISTENTE = NULL;
                SELECT id INTO @CUOTA_EXISTENTE FROM orden_descuento_cuotas WHERE socio_id = vSOCIO_ID
                AND orden_descuento_id = @ordenDtoId AND proveedor_id = vIMPUTAR_PROVEEDOR_ID
                AND tipo_producto = @tipoProducto AND tipo_cuota = vTIPO_CUOTA AND periodo = vPERIODO;
                
                IF @CUOTA_EXISTENTE IS NULL AND vACTIVO = TRUE THEN
                
                    -- INSERTO LA CUOTA
                    INSERT INTO orden_descuento_cuotas(orden_descuento_id,socio_id,persona_beneficio_id,
                    tipo_orden_dto,tipo_producto,tipo_cuota,periodo,estado,situacion,vencimiento,vencimiento_proveedor,
                    nro_cuota,importe,proveedor_id)
                    VALUES(@ordenDtoId,vSOCIO_ID,@beneficioId,'CMUTU',@tipoProducto,vTIPO_CUOTA,vPERIODO,'A','MUTUSICUMUTU',
                    DATE_FORMAT(now(),'%Y-%m-%d'),DATE_FORMAT(now(),'%Y-%m-%d'),0,@ADICIONAL,vIMPUTAR_PROVEEDOR_ID);
                    
                    SELECT LAST_INSERT_ID() INTO @CUOTA_EXISTENTE;
                    
					insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,orden_descuento_id,
							orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,tipo_cuota,
							periodo_cuota,proveedor_id,vencida,importe,saldo_actual,codigo_organismo
					)
					values(vLIQUIDACION_ID,vSOCIO_ID,@beneficioId,
					@ordenDtoId,@CUOTA_EXISTENTE,'CMUTU',@tipoProducto,vTIPO_CUOTA,
					vPERIODO,vIMPUTAR_PROVEEDOR_ID,0,@ADICIONAL,@ADICIONAL,vORGANISMO);                     

                ELSE
                
					/*
					SELECT 'DEVENGADO PREVIO - UPDATE',vLIQUIDACION_ID,vSOCIO_ID,vORGANISMO,vIMPUTAR_PROVEEDOR_ID,
					vTIPO,vCALCULO,vVALOR,vTIPO_CUOTA,vPERIODO,@saldo,@ADICIONAL,@ordenDtoId,@beneficioId; 
                    */
                    
                    IF vACTIVO = TRUE THEN
                    
						-- ACTUALIZO EL VALOR
						UPDATE orden_descuento_cuotas set importe = @ADICIONAL WHERE id = @CUOTA_EXISTENTE;
						
						-- VERIFICO SI EXISTE EN LA LIQUIDACION_CUOTAS
						IF (SELECT COUNT(*) FROM liquidacion_cuotas WHERE liquidacion_id = vLIQUIDACION_ID AND socio_id = vSOCIO_ID AND orden_descuento_cuota_id = @CUOTA_EXISTENTE) > 0 THEN
							UPDATE liquidacion_cuotas set importe = @ADICIONAL, saldo_actual = (@ADICIONAL - IFNULL((SELECT SUM(importe) FROM orden_descuento_cobro_cuotas WHERE orden_descuento_cuota_id = @CUOTA_EXISTENTE),0)) 
							WHERE liquidacion_id = vLIQUIDACION_ID
							AND socio_id = vSOCIO_ID AND orden_descuento_cuota_id = @CUOTA_EXISTENTE;
						ELSE
						
							insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,orden_descuento_id,
									orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,tipo_cuota,
									periodo_cuota,proveedor_id,vencida,importe,saldo_actual,codigo_organismo
							)
							values(vLIQUIDACION_ID,vSOCIO_ID,@beneficioId,
							@ordenDtoId,@CUOTA_EXISTENTE,'CMUTU',@tipoProducto,vTIPO_CUOTA,
							vPERIODO,vIMPUTAR_PROVEEDOR_ID,0,@ADICIONAL,@ADICIONAL,vORGANISMO);                    
						
						END IF;
                    
                    ELSE
                    
						IF (SELECT COUNT(*) FROM orden_descuento_cobro_cuotas WHERE orden_descuento_cuota_id = @CUOTA_EXISTENTE) = 0 THEN
							DELETE FROM liquidacion_cuotas WHERE liquidacion_id = vLIQUIDACION_ID AND socio_id = vSOCIO_ID AND orden_descuento_cuota_id = @CUOTA_EXISTENTE;
                            DELETE FROM orden_descuento_cuotas WHERE id = @CUOTA_EXISTENTE;
                        END IF;
                    
                    END IF;
                    
                
                END IF;
            

            
            END IF;
            

        END IF;

END LOOP c1_loop;
CLOSE c_adicionales;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_GESTION_MORA`(
vSOCIO_ID INT(11),
vORGANISMO VARCHAR(12),
vLIQUIDACION_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
DECLARE vID INT(11);
DECLARE vIMPORTE_DEBITO DECIMAL(10,2);
DECLARE vIMPORTE_DTO DECIMAL(10,2);
DECLARE vIMPORTE_DEBITO_CALCULADO DECIMAL(10,2);
DECLARE vFORMULA TEXT;
DECLARE cursor_mora CURSOR FOR 
SELECT id,importe_adebitar,importe_dto FROM liquidacion_socios
where liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID
and periodo = 0;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;



SET @LIMITE_1 = 100;
SET @LIMITE_2 = 200;

SET @TOPE_DEBITO_MAXIMO = 800;

SET vID = 0;
SET vIMPORTE_DEBITO = 0;
SET vIMPORTE_DTO = 0;
SET vIMPORTE_DEBITO_CALCULADO = 0;

/*
SELECT entero_1,entero_2,decimal_2 INTO @LIMITE_1,@LIMITE_2,@TOPE_DEBITO_MAXIMO
FROM global_datos WHERE id = vORGANISMO;
*/
SET @LIMITE_1 = @LIMITE_2 = @TOPE_DEBITO_XREC = @TOPE_DEBITO_MAXIMO = @IMPORTE_MINIMO_REGISTRO = 0;
CALL SP_LIQUIDA_DEUDA_CBU_LIMITES(vORGANISMO,@LIMITE_1,@LIMITE_2,@TOPE_DEBITO_MAXIMO,@TOPE_DEBITO_XREC,@IMPORTE_MINIMO_REGISTRO);
-- SELECT @LIMITE_1,@LIMITE_2,@TOPE_DEBITO_XREC,@TOPE_DEBITO_MAXIMO,@IMPORTE_MINIMO_REGISTRO;

OPEN cursor_mora;
c1_loop: LOOP
	FETCH cursor_mora INTO vID,vIMPORTE_DEBITO,vIMPORTE_DTO;
    
	IF (l_last_row = 1) THEN
		LEAVE c1_loop; 
	END IF;	      
    SET vFORMULA = '';
    SET vIMPORTE_DEBITO_CALCULADO = vIMPORTE_DEBITO;
    
    IF @LIMITE_1 >= vIMPORTE_DEBITO THEN
		SET vIMPORTE_DEBITO_CALCULADO = vIMPORTE_DEBITO;
        SET vFORMULA = CONCAT(vIMPORTE_DEBITO,' <' , @LIMITE_1,' ==> IMPORTE A DEBITAR: ',vIMPORTE_DEBITO_CALCULADO,' (TOTAL ATRASO)');
    END IF;
    

    IF @LIMITE_1 < vIMPORTE_DEBITO AND vIMPORTE_DEBITO <= @LIMITE_2 THEN
		SET vIMPORTE_DEBITO_CALCULADO = vIMPORTE_DEBITO / 2;
        SET vFORMULA = CONCAT(@LIMITE_1,' < ',vIMPORTE_DEBITO,' <= ' , @LIMITE_2,' ==> IMPORTE A DEBITAR: ',vIMPORTE_DEBITO_CALCULADO,' (TOTAL ATRASO / 2)');
    END IF;
    
    IF vIMPORTE_DEBITO > @LIMITE_2 THEN
		SET vIMPORTE_DEBITO_CALCULADO = vIMPORTE_DEBITO / 3;
        SET vFORMULA = CONCAT(vIMPORTE_DEBITO,' > ' , @LIMITE_2,' ==> IMPORTE A DEBITAR: ',vIMPORTE_DEBITO_CALCULADO,' (TOTAL ATRASO / 3)');
    END IF; 
    
    -- CONTROL DEL TOPE DE DEBITO CBU
    IF @TOPE_DEBITO_MAXIMO < vIMPORTE_DEBITO_CALCULADO THEN
		SET vIMPORTE_DEBITO_CALCULADO = @TOPE_DEBITO_MAXIMO;
        SET vFORMULA = CONCAT(vFORMULA,'\nCONTROL MONTO MAXIMO DEBITO CBU == > IMPORTE A DEBITAR: ',vIMPORTE_DEBITO_CALCULADO);
    END IF;

	update liquidacion_socios 
    set importe_adebitar = vIMPORTE_DEBITO_CALCULADO,
    importe_dto = vIMPORTE_DEBITO_CALCULADO,
    formula_criterio_deuda = vFORMULA where id = vID;
      
END LOOP c1_loop;

CLOSE cursor_mora;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_LIMITES`(
IN vORGANISMO VARCHAR(12),
OUT vLIMITE_INFERIOR INT(11),
OUT vLIMITE_SUPERIOR INT(11),
OUT vIMPORTE_MAXIMO_DEBITO DECIMAL(10,2),
OUT vIMPORTE_MAXIMO_POR_REGISTRO DECIMAL(10,2),
OUT vIMPORTE_MINIMO_POR_REGISTRO INT(11)
)
BEGIN

SET vIMPORTE_MINIMO_POR_REGISTRO = 50;

SELECT entero_1,entero_2,decimal_1,decimal_2 
INTO vLIMITE_INFERIOR,vLIMITE_SUPERIOR,vIMPORTE_MAXIMO_POR_REGISTRO,vIMPORTE_MAXIMO_DEBITO
FROM global_datos WHERE id = vORGANISMO;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO`(
vSOCIO_ID INT(11),
vORGANISMO VARCHAR(12),
vLIQUIDACION_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
DECLARE vID INT(11);
DECLARE vBENEFICIO_ID INT(11);
DECLARE vIMPORTE_DEBITO DECIMAL(10,2);
DECLARE vIMPORTE_DEBITO_CALCULADO DECIMAL(10,2);
DECLARE vSALDO DECIMAL(10,2);
DECLARE vSALDO_ACUMULADO DECIMAL(10,2);
DECLARE vFORMULA TEXT;
DECLARE cursor_socio CURSOR FOR 
SELECT id,persona_beneficio_id,importe_adebitar FROM liquidacion_socios
where liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID
order by periodo;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;

SET @TOPE_POR_REGISTRO = 0;
SET vSALDO = 0;
SET vSALDO_ACUMULADO = 0;


-- SELECT decimal_1 INTO @TOPE_POR_REGISTRO
-- FROM global_datos WHERE id = vORGANISMO;

SET @LIMITE_1 = @LIMITE_2 = @TOPE_DEBITO_XREC = @TOPE_DEBITO_MAXIMO = @IMPORTE_MINIMO_REGISTRO = 0;
CALL SP_LIQUIDA_DEUDA_CBU_LIMITES(vORGANISMO,@LIMITE_1,@LIMITE_2,@TOPE_DEBITO_MAXIMO,@TOPE_POR_REGISTRO,@IMPORTE_MINIMO_REGISTRO);
-- SELECT @LIMITE_1,@LIMITE_2,@TOPE_DEBITO_XREC,@TOPE_DEBITO_MAXIMO,@IMPORTE_MINIMO_REGISTRO;

SET vID = 0;
SET vBENEFICIO_ID = 0;
SET vIMPORTE_DEBITO = 0;

OPEN cursor_socio;
c1_loop: LOOP
	FETCH cursor_socio INTO vID,vBENEFICIO_ID,vIMPORTE_DEBITO;
    
	IF (l_last_row = 1) THEN
		LEAVE c1_loop; 
	END IF;	
    
    SET @IMPO_MAX_DBTO_BENEFICIO = 0;
    SET vSALDO = 0;
    SET @IMPO_DEBITO = 0;
    SET @ULTIMO_ID = 0;
    
    SELECT importe_max_registro_cbu into @IMPO_MAX_DBTO_BENEFICIO 
    FROM persona_beneficios where id = vBENEFICIO_ID;
    
    IF @TOPE_POR_REGISTRO > @IMPO_MAX_DBTO_BENEFICIO AND @IMPO_MAX_DBTO_BENEFICIO > 0 THEN
		SET @TOPE_POR_REGISTRO = @IMPO_MAX_DBTO_BENEFICIO;
    END IF;
    
    IF @TOPE_POR_REGISTRO <> 0 THEN
		
        SET vSALDO = vIMPORTE_DEBITO;
        
        SET @REGISTRO = 1;
        -- SET vIMPORTE_DEBITO_CALCULADO = vIMPORTE_DEBITO / @CICLOS;
        
        WHILE vSALDO > @IMPORTE_MINIMO_REGISTRO DO
        
			SET vSALDO_ACUMULADO = vSALDO_ACUMULADO + @TOPE_POR_REGISTRO;
            
            SET @IMPO_DEBITO = @TOPE_POR_REGISTRO;
            
            IF vSALDO < @TOPE_POR_REGISTRO THEN
				SET @IMPO_DEBITO = vSALDO;
            END IF;
            
            SET @IMPO_DEBITO = CAST(@IMPO_DEBITO AS DECIMAL(10,2));
            
            IF @REGISTRO = 1 THEN
			
				UPDATE liquidacion_socios 
				SET importe_adebitar = @IMPO_DEBITO,
                -- ,formula_criterio_deuda = concat(formula_criterio_deuda,'\n*** FRACCION: ',@IMPO_DEBITO,' ***'),
				registro = @REGISTRO WHERE id = vID; 
                
                SET @ULTIMO_ID = vID;
            
            ELSE
        
				insert into liquidacion_socios(liquidacion_id,socio_id,
				persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
				nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
				codigo_empresa,codigo_reparticion,turno_pago,periodo,importe_adebitar,
				formula_criterio_deuda)			
				SELECT liquidacion_id,socio_id,
				persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
				nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
				codigo_empresa,codigo_reparticion,turno_pago,periodo,@IMPO_DEBITO,
                formula_criterio_deuda
				-- concat(formula_criterio_deuda,'\n*** ACUERDO S/FRACCION: ',@TOPE_POR_REGISTRO,' ***')
                FROM liquidacion_socios where id = vID;
                
                SET @ULTIMO_ID = LAST_INSERT_ID();
			
            END IF;
			
            SET vSALDO = vSALDO - @IMPO_DEBITO;
            
			-- SELECT vID,vIMPORTE_DEBITO,@TOPE_POR_REGISTRO,@IMPO_DEBITO,vSALDO;
            
            SET @REGISTRO = @REGISTRO + 1;        
        
        END WHILE;
        
        IF vSALDO < @IMPORTE_MINIMO_REGISTRO THEN
            UPDATE liquidacion_socios 
			SET importe_adebitar = importe_adebitar + vSALDO WHERE id = @ULTIMO_ID;
        END IF;        
        
     END IF;
	
    
    
END LOOP c1_loop;

CLOSE cursor_socio;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO_RENUMERA`(
vSOCIO_ID INT(11),
vLIQUIDACION_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
DECLARE vID INT(11);
DECLARE cursor_socio CURSOR FOR 
SELECT id FROM liquidacion_socios
where liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID
order by periodo,importe_dto desc;

DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;

SET @REGISTRO = 1;

OPEN cursor_socio;
c1_loop: LOOP
	FETCH cursor_socio INTO vID;
    
	IF (l_last_row = 1) THEN
		LEAVE c1_loop; 
	END IF;	 
    
    SET @REGISTRO = @REGISTRO + 1;
    
	UPDATE liquidacion_socios 
	SET registro = @REGISTRO WHERE id = vID;
    
END LOOP c1_loop;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`127.0.0.1` PROCEDURE `SP_LIQUIDA_DEUDA_SCORING`(IN
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
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_VENCIMIENTOS`(
IN vPERSONA_BENEFICIO_ID INT(11),
IN vPROVEEDOR_ID INT(11),
IN vCODIGO_ORGANISMO VARCHAR(12),
IN vPERIODO VARCHAR(6),
IN vFECHA DATE,
OUT vPERIODO_INI VARCHAR(6),
OUT vVENCIMIENTO_SOCIO DATE,
OUT vVENCIMIENTO_PROVEEDOR DATE,
OUT vULTIMO_PERIODO_LIQUIDADO VARCHAR(6)
)
BEGIN



IF vPERSONA_BENEFICIO_ID IS NOT NULL THEN
	SELECT codigo_beneficio into vCODIGO_ORGANISMO from persona_beneficios where id = vPERSONA_BENEFICIO_ID;
END IF;

SELECT periodo into vULTIMO_PERIODO_LIQUIDADO FROM liquidaciones 
where codigo_organismo = vCODIGO_ORGANISMO
and cerrada = 1 order by periodo desc limit 1;

select d_corte, d_vto_socio, d_vto_proveedor_suma, mes, 
m_ini_socio_ac_suma, m_ini_socio_dc_suma, m_vto_socio_suma 
INTO @d_corte, @d_vto_socio, @d_vto_proveedor_suma, @mes, 
@m_ini_socio_ac_suma, @m_ini_socio_dc_suma, @m_vto_socio_suma 
from proveedor_vencimientos 
where proveedor_id = vPROVEEDOR_ID and codigo_organismo = vCODIGO_ORGANISMO
and mes = cast(trim(substring(vPERIODO,5,2)) as char(2));

IF @d_corte IS NULL THEN
select d_corte INTO @d_corte from proveedor_vencimientos where proveedor_id = 18 and codigo_organismo = 'MUTUCORG2201'and mes = '01';
END IF;

IF @m_ini_socio_dc_suma IS NULL THEN
select m_ini_socio_dc_suma INTO @m_ini_socio_dc_suma from proveedor_vencimientos where proveedor_id = 18 and codigo_organismo = 'MUTUCORG2201'and mes = '01';
END IF;

IF @m_ini_socio_ac_suma IS NULL THEN
select m_ini_socio_ac_suma INTO @m_ini_socio_ac_suma from proveedor_vencimientos where proveedor_id = 18 and codigo_organismo = 'MUTUCORG2201'and mes = '01';
END IF;

SET @PERIODO_INICIO = DATE_ADD(vFECHA, INTERVAL IF(DAY(vFECHA) <= @d_corte,@m_ini_socio_ac_suma,@m_ini_socio_dc_suma) MONTH);
SET vPERIODO_INI = DATE_FORMAT(@PERIODO_INICIO,'%Y%m');

IF vPERIODO IS NULL THEN SET vVENCIMIENTO_SOCIO = DATE_ADD(STR_TO_DATE(CONCAT(DATE_FORMAT(@PERIODO_INICIO,'%Y-%m'),'-',@d_vto_socio),'%Y-%m-%d'),INTERVAL @m_vto_socio_suma MONTH);
ELSE 
	SET vVENCIMIENTO_SOCIO = STR_TO_DATE(CONCAT(vPERIODO,@d_vto_socio),'%Y%m%d'); 
    SET vPERIODO_INI = NULL; 
END IF;
IF DATE_FORMAT(vVENCIMIENTO_SOCIO,'%w') = 6 THEN SET vVENCIMIENTO_SOCIO = DATE_ADD(vVENCIMIENTO_SOCIO, INTERVAL 2 DAY);
END IF;
IF DATE_FORMAT(vVENCIMIENTO_SOCIO,'%w') = 0 THEN SET vVENCIMIENTO_SOCIO = DATE_ADD(vVENCIMIENTO_SOCIO, INTERVAL 1 DAY);
END IF;

SET vVENCIMIENTO_PROVEEDOR = DATE_ADD(vVENCIMIENTO_SOCIO, INTERVAL @d_vto_proveedor_suma DAY);

IF DATE_FORMAT(vVENCIMIENTO_PROVEEDOR,'%w') = 6 THEN SET vVENCIMIENTO_PROVEEDOR = DATE_ADD(vVENCIMIENTO_PROVEEDOR, INTERVAL 2 DAY);
END IF;
IF DATE_FORMAT(vVENCIMIENTO_PROVEEDOR,'%w') = 0 THEN SET vVENCIMIENTO_PROVEEDOR = DATE_ADD(vVENCIMIENTO_PROVEEDOR, INTERVAL 1 DAY);
END IF;

END$$
DELIMITER ;
