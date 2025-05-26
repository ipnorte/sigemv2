CREATE DEFINER=`root`@`127.0.0.1` PROCEDURE `SP_LIQUIDA_DEUDA_CABECERA`(
vSOCIO_ID INT(11),
vPERIODO VARCHAR(6),
vORGANISMO VARCHAR(12),
vLIQUIDACION_ID INT(11)
)
BEGIN

-- ----------------------------------------------------------------
-- CARGO EL PERIODO
-- ----------------------------------------------------------------
delete from liquidacion_socios where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID and periodo = 1;

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
	PersonaBeneficio.cbu,
	PersonaBeneficio.turno_pago;

-- ----------------------------------------------------------------
-- CARGO LA MORA
-- ----------------------------------------------------------------
delete from liquidacion_socios where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID and periodo = 0;

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
	PersonaBeneficio.cbu,
	PersonaBeneficio.turno_pago;


END