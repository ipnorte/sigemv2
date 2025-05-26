DROP PROCEDURE IF EXISTS SP_LIQUIDA_CUOTA_SOCIAL;
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
