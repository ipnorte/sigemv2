CREATE DEFINER=`root`@`127.0.0.1` PROCEDURE `SP_LIQUIDA_DEUDA_CBU`(
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
		AND co.periodo_cobro <= vPERIODO),0);
        
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
		order by liquidacion_id desc limit 1));        
        
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
-- /////////////////////////////////////////////////////////////////////
-- VERIFICO ACUERDOS DE DEBITO
-- /////////////////////////////////////////////////////////////////////    
CALL SP_LIQUIDA_DEUDA_CBU_ACUERDO_DEBITO(vSOCIO_ID,vLIQUIDACION_ID);
CALL SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO_RENUMERA(vSOCIO_ID,vLIQUIDACION_ID);
CALL SP_LIQUIDA_DEUDA_SCORING(vSOCIO_ID,vLIQUIDACION_ID);
    
END