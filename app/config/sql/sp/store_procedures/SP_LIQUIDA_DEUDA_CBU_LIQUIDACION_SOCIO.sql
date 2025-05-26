CREATE DEFINER=`root`@`127.0.0.1` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO`(
vSOCIO_ID INT(11),
vORGANISMO VARCHAR(12),
vLIQUIDACION_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
DECLARE vID INT(11);
DECLARE vIMPORTE_DEBITO DECIMAL(10,2);
DECLARE vIMPORTE_DEBITO_CALCULADO DECIMAL(10,2);
DECLARE vSALDO DECIMAL(10,2);
DECLARE vSALDO_ACUMULADO DECIMAL(10,2);
DECLARE vFORMULA TEXT;
DECLARE cursor_socio CURSOR FOR 
SELECT id,importe_adebitar FROM liquidacion_socios
where liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID
order by periodo;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;

SET @TOPE_POR_REGISTRO = 0;
SET vSALDO = 0;
SET vSALDO_ACUMULADO = 0;
SELECT decimal_1 INTO @TOPE_POR_REGISTRO
FROM global_datos WHERE id = vORGANISMO;

OPEN cursor_socio;
c1_loop: LOOP
	FETCH cursor_socio INTO vID,vIMPORTE_DEBITO;
    
	IF (l_last_row = 1) THEN
		LEAVE c1_loop; 
	END IF;	 
    
    SET @N = CAST(vIMPORTE_DEBITO / @TOPE_POR_REGISTRO AS UNSIGNED);
    
    IF @N > 1 AND @TOPE_POR_REGISTRO <> 0 THEN
		
        SET vSALDO = vIMPORTE_DEBITO;
        SET @CICLOS = @N;
        
        SET @REGISTRO = 1;
        SET vIMPORTE_DEBITO_CALCULADO = vIMPORTE_DEBITO / @CICLOS;
        
        WHILE vSALDO > 50 DO
        
			SET vSALDO_ACUMULADO = vSALDO_ACUMULADO + @TOPE_POR_REGISTRO;
            
            SET @IMPO_DEBITO = @TOPE_POR_REGISTRO;
            
            IF vSALDO <= @TOPE_POR_REGISTRO THEN
				SET @IMPO_DEBITO = vSALDO;
            END IF;
            
            IF @REGISTRO = 1 THEN
			
				UPDATE liquidacion_socios 
				SET importe_adebitar = @IMPO_DEBITO,
				registro = @REGISTRO WHERE id = vID; 
            
            ELSE
        
				insert into liquidacion_socios(registro,liquidacion_id,socio_id,
				persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
				nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
				codigo_empresa,codigo_reparticion,turno_pago,periodo,importe_adebitar,
				formula_criterio_deuda)			
				SELECT @REGISTRO,liquidacion_id,socio_id,
				persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
				nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
				codigo_empresa,codigo_reparticion,turno_pago,periodo,@IMPO_DEBITO,
				formula_criterio_deuda
                FROM liquidacion_socios where id = vID;
                
                SET @ULTIMO_ID = LAST_INSERT_ID();
			
            END IF;
            -- DELETE FROM liquidacion_socios where id = vID;
			
            SET vSALDO = vSALDO - @IMPO_DEBITO;
            
			-- SELECT vID,vIMPORTE_DEBITO,@TOPE_POR_REGISTRO,@IMPO_DEBITO,@N,@CICLOS;
            
            -- SET @CICLOS = @CICLOS - 1;
            SET @REGISTRO = @REGISTRO + 1;        
        
        END WHILE;
        
        IF vSALDO > 0 THEN
			UPDATE liquidacion_socios 
			SET importe_adebitar = importe_adebitar + vSALDO WHERE id = @ULTIMO_ID; 
        END IF;        
        
     END IF;
	
    
    
END LOOP c1_loop;
END