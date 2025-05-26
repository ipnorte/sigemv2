CREATE DEFINER=`root`@`127.0.0.1` PROCEDURE `SP_LIQUIDA_DEUDA_ACUERDO_DEBITO`(
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


		DELETE FROM liquidacion_socios where 
		liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID and
		persona_beneficio_id = vBENEFICIO_ID;        



		IF CAST(vMONTO_ACUERDO / vMONTO_MAX_DTO_BENEFICIO AS UNSIGNED) > 1 AND vMONTO_MAX_DTO_BENEFICIO > 0 THEN
        
			SET vSALDO = vMONTO_ACUERDO;
            WHILE vSALDO > 50 DO
            
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


        /*
        -- ----------------------------------------------------------------
        -- BORRO LA CABECERA DE LA LIQUIDACION DEL SOCIO PARA EL BENEFICIO 
        -- CON ACUERDO Y CARGO EL ACUERDO
        -- ----------------------------------------------------------------
        DELETE FROM liquidacion_socios where 
        liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID and
        persona_beneficio_id = vBENEFICIO_ID;
        
        
        
        -- ----------------------------------------------------------------
        -- GENERO UNA CABECERA DE LIQUIDACION DEL SOCIO CON EL ACUERDO
        -- PARA EL BENEFICIO QUE TIENE ACUERDO DE DEBITO
        -- ----------------------------------------------------------------
        
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
            
		*/  
        
END LOOP c1_loop_beneficios_acuerdo;  
CLOSE c_beneficios_acuerdo;       
END