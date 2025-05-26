CREATE DEFINER=`root`@`%` PROCEDURE `SP_POSICION_CONSOLIDADA`(
	vPID INT,
	vPer VARCHAR(6),
	vOrg VARCHAR(12),
	vProvId INT,
	vEmp VARCHAR(12),
	vTurno VARCHAR(50)
	)
BEGIN
	
	DECLARE vRows INT;
	DECLARE vCont INT;
	DECLARE done BOOLEAN DEFAULT FALSE;
	DECLARE v_last_row INT DEFAULT 0;
	DECLARE vTdocNdoc VARCHAR(50);
	DECLARE vApenom VARCHAR(255);
	DECLARE vSocioId INT(11) DEFAULT 0;
	
	DECLARE vSaldoActual DECIMAL;
	
	DECLARE cur_personas CURSOR FOR 
	SELECT 
	CONCAT(TRIM(GlobalDato.concepto_1),' ',TRIM(Persona.documento)),
	CONCAT(Persona.apellido,', ',Persona.nombre) AS apenom,
	Socio.id
	FROM personas AS Persona
	INNER JOIN socios AS Socio ON (Socio.persona_id = Persona.id)
	INNER JOIN global_datos AS GlobalDato ON(GlobalDato.id = Persona.tipo_documento)
	GROUP BY Socio.id
	ORDER BY Persona.apellido, Persona.nombre;
	
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	
	DELETE FROM asincrono_temporales WHERE asincrono_id = vPID;
	
	OPEN cur_personas;
	SET vRows = (SELECT FOUND_ROWS());
	SET vCont = 0;
	
	CALL STP_ASINCRONO(vPID,'P',vRows,vCont,'*** INICIANDO PROCESO ***');
	
	WHILE NOT isAsincronoStop(vPID) DO
	FETCH cur_personas INTO vTdocNdoc, vApenom, vSocioId;
	
		SET @query = CONCAT('	
			INSERT INTO asincrono_temporales
			(asincrono_id,clave_1,clave_2,clave_3,texto_1,texto_2,texto_3,
			texto_4,texto_5,texto_6,texto_7,
			decimal_1,decimal_2,
			decimal_3,decimal_4,entero_1,entero_2,entero_3,entero_4)
			SELECT ',vPID,',
			cuota.orden_descuento_id,
			cuota.proveedor_id,
			', vSocioId,',
			''',vTdocNdoc,''',''',vApenom,''',
			beneficio.codigo_beneficio,
			IF(SUBSTR(beneficio.codigo_beneficio,9,2) = 22,beneficio.codigo_empresa,''''),
			IF(SUBSTR(beneficio.codigo_beneficio,9,2) = 22,beneficio.turno_pago,''''),
			concat(orden.tipo_orden_dto,'' #'',orden.numero),
			IFNULL(cuota.nro_referencia_proveedor,0),
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''TOTAL_DEVENGADO'') AS TOTAL_DEVENGADO,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''SALDO_AVENCER'') AS SALDO_AVENCER,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''TOTAL_PAGADO'') AS TOTAL_PAGADO,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''SALDO_VENCIDO'') AS SALDO_VENCIDO,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''CUOTAS_DEVENGADAS'') AS CUOTAS_DEVENGADAS,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''CUOTAS_VENCIDAS'') AS CUOTAS_VENCIDAS,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''CUOTAS_AVENCER'') AS CUOTAS_AVENCER,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''CUOTAS_PAGAS'') AS CUOTAS_PAGAS  
			FROM orden_descuento_cuotas AS cuota 
			INNER JOIN persona_beneficios AS beneficio ON (beneficio.id = cuota.persona_beneficio_id)
			INNER JOIN orden_descuentos as orden on (orden.id = cuota.orden_descuento_id)
			WHERE 
				cuota.socio_id = ', vSocioId);
		IF vProvId IS NOT NULL THEN
			SET @query =  CONCAT(@query,' AND cuota.proveedor_id = ',vProvId);
		END IF;
		IF vOrg IS NOT NULL THEN
			SET @query =  CONCAT(@query,' AND beneficio.codigo_beneficio = ''',vOrg,'''');
		END IF;
		IF vEmp IS NOT NULL THEN
			SET @query =  CONCAT(@query,' AND beneficio.codigo_empresa = ''',vEmp,'''');
		END IF;
		IF vTurno IS NOT NULL THEN
			SET @query =  CONCAT(@query,' AND beneficio.turno_pago = ''',TRIM(vTurno),'''');
		END IF;						
		SET @query = CONCAT(@query,' GROUP BY cuota.orden_descuento_id,cuota.proveedor_id,cuota.persona_beneficio_id');	
	
	
		PREPARE smpt FROM @query;
		EXECUTE smpt;
		DEALLOCATE PREPARE smpt;
		DELETE FROM asincrono_temporales WHERE asincrono_id = vPID
		AND clave_3 = vSocioId AND decimal_4 = 0;
	
		CALL STP_ASINCRONO(vPID,'P',vRows,vCont,CONCAT('PROCESANDO: ',vSocioId,' *** ',vApenom));
	
		SELECT vCont + 1 INTO vCont;
		
	END WHILE;	
	CLOSE cur_personas;
	
	CALL STP_ASINCRONO(vPID,'F',vRows,vCont,'**** PROCESO FINALIZADO ***');
	
END