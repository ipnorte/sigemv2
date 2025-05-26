CREATE DEFINER=`root`@`127.0.0.1` PROCEDURE `SP_LIQUIDA_CUOTA_SERVICIOS`(
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
END