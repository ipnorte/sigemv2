DROP FUNCTION IF EXISTS `F_PAGO_CUOTA`;
DROP FUNCTION IF EXISTS `FX_CALCULA_VENCIMIENTOS_POR_PERIODO`;
DROP FUNCTION IF EXISTS `FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE`;
DROP FUNCTION IF EXISTS `FX_TOTALES_ORDEN`;
DROP FUNCTION IF EXISTS `FX_TOTALES_SOCIO`;
DROP FUNCTION IF EXISTS `isAsincronoStop`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `F_PAGO_CUOTA`(
	vSOLICITADO DECIMAL(10,2),
    vCUOTAS INT(11),
    vTASA DECIMAL(10,4),
    vPORCENTAJE_ADICIONAL DECIMAL(10,3),
    vIVA DECIMAL(10,2),
    vTIPO CHAR(3)
	) RETURNS decimal(10,2)
    DETERMINISTIC
BEGIN
	-- select F_PAGO_CUOTA(1000,3,11.55,18,21);
	SET @VALOR_CUOTA = 0;
	
    SET vPORCENTAJE_ADICIONAL = vPORCENTAJE_ADICIONAL / 100;
	SET vIVA = vIVA / 100;
    SET vTASA = vTASA / 100;
    
	SET @CAPITAL = vSOLICITADO * (1 + vPORCENTAJE_ADICIONAL);
	SET @CALCULO = ROUND((@CAPITAL * vTASA) / (1 - (POW(1 + vTASA, vCUOTAS * -1))),2);
    SET @INTERES = ROUND(@CALCULO * vTASA,2);
    SET @IVA = ROUND(@INTERES * vIVA,2);
    SET @INTERES = ROUND(@INTERES - @IVA,2);
    SET @ADICIONALES = ROUND(@CALCULO * vPORCENTAJE_ADICIONAL,2);
    SET @CAPITAL_CUOTA = @CALCULO - (@INTERES + @IVA + @ADICIONALES);
    SET @VALOR_CUOTA = @INTERES + @IVA + @ADICIONALES + @CAPITAL_CUOTA;

	SET vSOLICITADO = ROUND(vSOLICITADO,2);
    SET @CAPITAL = ROUND(@CAPITAL,2);
    SET @VALOR_CUOTA = ROUND(@VALOR_CUOTA,2);
    SET @CAPITAL_CUOTA = ROUND(@CAPITAL_CUOTA,2);
    SET @INTERES = ROUND(@INTERES,2);
    SET @ADICIONALES = ROUND(@ADICIONALES,2);
    SET @IVA = ROUND(@IVA,2);
	
    IF vTIPO = 'CAP' THEN
		RETURN IFNULL(@CAPITAL_CUOTA,0);
	END IF;
    IF vTIPO = 'INT' THEN
		RETURN IFNULL(@INTERES,0);
	END IF;    
    IF vTIPO = 'IVA' THEN
		RETURN IFNULL(@IVA,0);
	END IF;  
    IF vTIPO = 'ADI' THEN
		RETURN IFNULL(@ADICIONALES,0);
	END IF;     
    IF vTIPO = 'CUO' THEN
		RETURN IFNULL(@VALOR_CUOTA,0);
	END IF;    
    
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `FX_CALCULA_VENCIMIENTOS_POR_PERIODO`(
vPROVEEDOR_ID INT(11),
vCODIGO_ORGANISMO VARCHAR(12),
vPERIODO VARCHAR(6),
vFECHA DATE
) RETURNS varchar(100) CHARSET latin1
    DETERMINISTIC
BEGIN

RETURN '201502|2015-02-01';
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE`(
vPROVEEDOR_ID INT(11)) RETURNS decimal(10,2)
    DETERMINISTIC
BEGIN
DECLARE vSALDO DECIMAL(10,2);
SET vSALDO = 10000000;
SELECT liquida_prestamo into @liquida_prestamo FROM proveedores WHERE id = vPROVEEDOR_ID;
IF @liquida_prestamo = 1 THEN 
	SET vSALDO = 1500000; 
END IF;
RETURN vSALDO;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `FX_TOTALES_ORDEN`(
	vOrdenID INT, 
	vPerCtrl VARCHAR(6),
	vProveedorID INT,
	vBeneficioId INT,
	vTipo VARCHAR(50)
	) RETURNS decimal(10,2)
    NO SQL
BEGIN
	DECLARE vValor FLOAT DEFAULT 0;
	
	SET vValor = 0;
	
	IF vTipo = 'TOTAL_DEVENGADO' THEN
		SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
		FROM orden_descuento_cuotas cu WHERE cu.orden_descuento_id = vOrdenID
		AND cu.proveedor_id = vProveedorID
		AND cu.persona_beneficio_id = vBeneficioId;
	END IF;
	IF vTipo = 'SALDO_AVENCER' THEN
		SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
		FROM orden_descuento_cuotas cu WHERE 
		cu.orden_descuento_id = vOrdenID AND cu.periodo > vPerCtrl 
		AND cu.proveedor_id = vProveedorID
		AND cu.persona_beneficio_id = vBeneficioId;
	END IF;
	
	IF vTipo = 'TOTAL_PAGADO' THEN
		SELECT IFNULL(SUM(cc.importe),0) INTO vValor
		FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
		orden_descuento_cuotas cu
		WHERE 
		cu.orden_descuento_id = vOrdenID AND cc.orden_descuento_cuota_id = cu.id 
		AND cc.orden_descuento_cobro_id = co.id 
		AND co.periodo_cobro <= vPerCtrl AND cu.proveedor_id = vProveedorID
		AND cu.persona_beneficio_id = vBeneficioId;
	END IF;
	
	IF vTipo = 'SALDO_VENCIDO' THEN
		SELECT IFNULL(SUM(cu.importe),0) INTO @devengado FROM orden_descuento_cuotas cu
		WHERE 
			cu.orden_descuento_id = vOrdenID 
			AND cu.periodo <= vPerCtrl 
			AND cu.proveedor_id = vProveedorID
			AND cu.persona_beneficio_id = vBeneficioId;
				
		SELECT IFNULL(SUM(cc.importe),0) INTO @pagado
		FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
		orden_descuento_cuotas cu
		WHERE 
		cu.orden_descuento_id = vOrdenID 
		AND cc.orden_descuento_cuota_id = cu.id 
		AND cc.orden_descuento_cobro_id = co.id 
		AND co.periodo_cobro <= vPerCtrl
		AND cu.proveedor_id = vProveedorID
		AND cu.persona_beneficio_id = vBeneficioId;
	
		SET vValor = @devengado - @pagado;
	
	END IF;
	
	IF vTipo = 'CUOTAS_DEVENGADAS' THEN
		SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
		WHERE cu.orden_descuento_id = vOrdenID AND cu.proveedor_id = vProveedorID
		AND cu.persona_beneficio_id = vBeneficioId;
	END IF;
	
	IF vTipo = 'CUOTAS_PAGAS' THEN
		SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
		WHERE cu.orden_descuento_id = vOrdenID
		AND cu.periodo <= vPerCtrl AND cu.proveedor_id = vProveedorID
		AND cu.persona_beneficio_id = vBeneficioId
		AND cu.importe <= (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
							INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
							WHERE 
							cocu.orden_descuento_cuota_id = cu.id
							AND co.periodo_cobro <= vPerCtrl)
		GROUP BY cu.orden_descuento_id;
	END IF;	
	
	IF vTipo = 'CUOTAS_VENCIDAS' THEN
		SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
		WHERE cu.orden_descuento_id = vOrdenID
		AND cu.periodo <= vPerCtrl AND cu.proveedor_id = vProveedorID
		AND cu.persona_beneficio_id = vBeneficioId
		AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
							INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
							WHERE 
							cocu.orden_descuento_cuota_id = cu.id
							AND co.periodo_cobro <= vPerCtrl)
		GROUP BY cu.orden_descuento_id;
	END IF;
	
	IF vTipo = 'CUOTAS_AVENCER' THEN
		SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
		WHERE cu.orden_descuento_id = vOrdenID
		AND cu.periodo > vPerCtrl AND cu.proveedor_id = vProveedorID
		AND cu.persona_beneficio_id = vBeneficioId
		AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
							WHERE 
							cocu.orden_descuento_cuota_id = cu.id)
		GROUP BY cu.orden_descuento_id;
	END IF;		
	RETURN vValor;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `FX_TOTALES_SOCIO`(
	vSocioID INT, 
	vPerCtrl VARCHAR(6),
	vProveedorID INT,
	vBeneficioId INT,
	vTipo VARCHAR(50)
	) RETURNS decimal(10,2)
    NO SQL
BEGIN
	DECLARE vValor FLOAT DEFAULT 0;
	
	SET vValor = 0;
	
	IF vTipo = 'TOTAL_DEVENGADO' THEN
		IF vProveedorID IS NOT NULL AND vBeneficioId IS NOT NULL THEN
		
			SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
			FROM orden_descuento_cuotas cu WHERE cu.socio_id = vSocioID
			AND cu.proveedor_id = vProveedorID
			AND cu.persona_beneficio_id = vBeneficioId;		
		ELSEIF vProveedorID IS NULL AND vBeneficioId IS NOT NULL THEN
			SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
			FROM orden_descuento_cuotas cu WHERE cu.socio_id = vSocioID
			AND cu.persona_beneficio_id = vBeneficioId;	
		ELSEIF vProveedorID IS NOT NULL AND vBeneficioId IS NULL THEN
			SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
			FROM orden_descuento_cuotas cu WHERE cu.socio_id = vSocioID
			AND cu.proveedor_id = vProveedorID;			
		ELSE
			SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
			FROM orden_descuento_cuotas cu WHERE cu.socio_id = vSocioID;			
		
		END IF;
		
		
	END IF;
	IF vTipo = 'SALDO_AVENCER' THEN
	
		IF vProveedorID IS NOT NULL AND vBeneficioId IS NOT NULL THEN
		
			SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
			FROM orden_descuento_cuotas cu WHERE 
			cu.socio_id = vSocioID AND cu.periodo > vPerCtrl 
			AND cu.proveedor_id = vProveedorID
			AND cu.persona_beneficio_id = vBeneficioId;	
		ELSEIF vProveedorID IS NULL AND vBeneficioId IS NOT NULL THEN
			SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
			FROM orden_descuento_cuotas cu WHERE 
			cu.socio_id = vSocioID AND cu.periodo > vPerCtrl 
			AND cu.persona_beneficio_id = vBeneficioId;		
		ELSEIF vProveedorID IS NOT NULL AND vBeneficioId IS NULL THEN
			SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
			FROM orden_descuento_cuotas cu WHERE 
			cu.socio_id = vSocioID AND cu.periodo > vPerCtrl 
			AND cu.proveedor_id = vProveedorID;	
					
		ELSE
			SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
			FROM orden_descuento_cuotas cu WHERE 
			cu.socio_id = vSocioID AND cu.periodo > vPerCtrl;			
		
		END IF;	
	
	END IF;
	
	IF vTipo = 'TOTAL_PAGADO' THEN
	
		IF vProveedorID IS NOT NULL AND vBeneficioId IS NOT NULL THEN
		
			SELECT IFNULL(SUM(cc.importe),0) INTO vValor
			FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
			orden_descuento_cuotas cu
			WHERE 
			cu.socio_id = vSocioID AND cc.orden_descuento_cuota_id = cu.id 
			AND cc.orden_descuento_cobro_id = co.id 
			AND co.periodo_cobro <= vPerCtrl AND cu.proveedor_id = vProveedorID
			AND cu.persona_beneficio_id = vBeneficioId;	
		ELSEIF vProveedorID IS NULL AND vBeneficioId IS NOT NULL THEN
			SELECT IFNULL(SUM(cc.importe),0) INTO vValor
			FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
			orden_descuento_cuotas cu
			WHERE 
			cu.socio_id = vSocioID AND cc.orden_descuento_cuota_id = cu.id 
			AND cc.orden_descuento_cobro_id = co.id 
			AND co.periodo_cobro <= vPerCtrl 
			AND cu.persona_beneficio_id = vBeneficioId;	
	
		ELSEIF vProveedorID IS NOT NULL AND vBeneficioId IS NULL THEN
			SELECT IFNULL(SUM(cc.importe),0) INTO vValor
			FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
			orden_descuento_cuotas cu
			WHERE 
			cu.socio_id = vSocioID AND cc.orden_descuento_cuota_id = cu.id 
			AND cc.orden_descuento_cobro_id = co.id 
			AND co.periodo_cobro <= vPerCtrl AND cu.proveedor_id = vProveedorID;	
					
		ELSE
			SELECT IFNULL(SUM(cc.importe),0) INTO vValor
			FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
			orden_descuento_cuotas cu
			WHERE 
			cu.socio_id = vSocioID AND cc.orden_descuento_cuota_id = cu.id 
			AND cc.orden_descuento_cobro_id = co.id 
			AND co.periodo_cobro <= vPerCtrl;	
			
		
		END IF;		
	
	END IF;
	
	IF vTipo = 'SALDO_VENCIDO' THEN
	
		IF vProveedorID IS NOT NULL AND vBeneficioId IS NOT NULL THEN
		
			SELECT IFNULL(SUM(cu.importe),0) INTO @devengado FROM orden_descuento_cuotas cu
			WHERE 
				cu.socio_id = vSocioID 
				AND cu.periodo <= vPerCtrl 
				AND cu.proveedor_id = vProveedorID
				AND cu.persona_beneficio_id = vBeneficioId;
					
			SELECT IFNULL(SUM(cc.importe),0) INTO @pagado
			FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
			orden_descuento_cuotas cu
			WHERE 
			cu.socio_id = vSocioID
			AND cc.orden_descuento_cuota_id = cu.id 
			AND cc.orden_descuento_cobro_id = co.id 
			AND co.periodo_cobro <= vPerCtrl
			AND cu.proveedor_id = vProveedorID
			AND cu.persona_beneficio_id = vBeneficioId;
		ELSEIF vProveedorID IS NULL AND vBeneficioId IS NOT NULL THEN
			SELECT IFNULL(SUM(cu.importe),0) INTO @devengado FROM orden_descuento_cuotas cu
			WHERE 
				cu.socio_id = vSocioID 
				AND cu.periodo <= vPerCtrl 
				AND cu.persona_beneficio_id = vBeneficioId;
					
			SELECT IFNULL(SUM(cc.importe),0) INTO @pagado
			FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
			orden_descuento_cuotas cu
			WHERE 
			cu.socio_id = vSocioID
			AND cc.orden_descuento_cuota_id = cu.id 
			AND cc.orden_descuento_cobro_id = co.id 
			AND co.periodo_cobro <= vPerCtrl
			AND cu.persona_beneficio_id = vBeneficioId;
	
		ELSEIF vProveedorID IS NOT NULL AND vBeneficioId IS NULL THEN
			SELECT IFNULL(SUM(cu.importe),0) INTO @devengado FROM orden_descuento_cuotas cu
			WHERE 
				cu.socio_id = vSocioID 
				AND cu.periodo <= vPerCtrl 
				AND cu.proveedor_id = vProveedorID;
					
			SELECT IFNULL(SUM(cc.importe),0) INTO @pagado
			FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
			orden_descuento_cuotas cu
			WHERE 
			cu.socio_id = vSocioID
			AND cc.orden_descuento_cuota_id = cu.id 
			AND cc.orden_descuento_cobro_id = co.id 
			AND co.periodo_cobro <= vPerCtrl
			AND cu.proveedor_id = vProveedorID;
	
					
		ELSE
			SELECT IFNULL(SUM(cu.importe),0) INTO @devengado FROM orden_descuento_cuotas cu
			WHERE 
				cu.socio_id = vSocioID 
				AND cu.periodo <= vPerCtrl;
					
			SELECT IFNULL(SUM(cc.importe),0) INTO @pagado
			FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
			orden_descuento_cuotas cu
			WHERE 
			cu.socio_id = vSocioID
			AND cc.orden_descuento_cuota_id = cu.id 
			AND cc.orden_descuento_cobro_id = co.id 
			AND co.periodo_cobro <= vPerCtrl;
		END IF;		
	
	
		SET vValor = @devengado - @pagado;
	
	END IF;
	
	IF vTipo = 'CUOTAS_DEVENGADAS' THEN
	
		IF vProveedorID IS NOT NULL AND vBeneficioId IS NOT NULL THEN
		
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID AND cu.proveedor_id = vProveedorID
			AND cu.persona_beneficio_id = vBeneficioId;	
		ELSEIF vProveedorID IS NULL AND vBeneficioId IS NOT NULL THEN
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID 
			AND cu.persona_beneficio_id = vBeneficioId;	
		
		ELSEIF vProveedorID IS NOT NULL AND vBeneficioId IS NULL THEN
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID AND cu.proveedor_id = vProveedorID;	
					
		ELSE
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID;	
			
		END IF;		
	
	END IF;
	
	IF vTipo = 'CUOTAS_PAGAS' THEN
	
		IF vProveedorID IS NOT NULL AND vBeneficioId IS NOT NULL THEN
		
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo <= vPerCtrl AND cu.proveedor_id = vProveedorID
			AND cu.persona_beneficio_id = vBeneficioId
			AND cu.importe <= (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id
								AND co.periodo_cobro <= vPerCtrl)
			GROUP BY cu.socio_id;
		ELSEIF vProveedorID IS NULL AND vBeneficioId IS NOT NULL THEN
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo <= vPerCtrl
			AND cu.persona_beneficio_id = vBeneficioId
			AND cu.importe <= (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id
								AND co.periodo_cobro <= vPerCtrl)
			GROUP BY cu.socio_id;
		
		ELSEIF vProveedorID IS NOT NULL AND vBeneficioId IS NULL THEN
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo <= vPerCtrl AND cu.proveedor_id = vProveedorID
			AND cu.importe <= (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id
								AND co.periodo_cobro <= vPerCtrl)
			GROUP BY cu.socio_id;
	
					
		ELSE
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo <= vPerCtrl
			AND cu.importe <= (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id
								AND co.periodo_cobro <= vPerCtrl)
			GROUP BY cu.socio_id;
	
			
		END IF;		
	
	END IF;		
	
	
	IF vTipo = 'CUOTAS_VENCIDAS' THEN
		IF vProveedorID IS NOT NULL AND vBeneficioId IS NOT NULL THEN
		
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo <= vPerCtrl AND cu.proveedor_id = vProveedorID
			AND cu.persona_beneficio_id = vBeneficioId
			AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id
								AND co.periodo_cobro <= vPerCtrl)
			GROUP BY cu.socio_id;	
		ELSEIF vProveedorID IS NULL AND vBeneficioId IS NOT NULL THEN
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo <= vPerCtrl
			AND cu.persona_beneficio_id = vBeneficioId
			AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id
								AND co.periodo_cobro <= vPerCtrl)
			GROUP BY cu.socio_id;		
		
		ELSEIF vProveedorID IS NOT NULL AND vBeneficioId IS NULL THEN
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo <= vPerCtrl AND cu.proveedor_id = vProveedorID
			AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id
								AND co.periodo_cobro <= vPerCtrl)
			GROUP BY cu.socio_id;		
	
					
		ELSE
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo <= vPerCtrl
			AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id
								AND co.periodo_cobro <= vPerCtrl)
			GROUP BY cu.socio_id;	
	
			
		END IF;	
	
	END IF;
	
	IF vTipo = 'CUOTAS_AVENCER' THEN
	
		IF vProveedorID IS NOT NULL AND vBeneficioId IS NOT NULL THEN
		
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo > vPerCtrl AND cu.proveedor_id = vProveedorID
			AND cu.persona_beneficio_id = vBeneficioId
			AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id)
			GROUP BY cu.socio_id;
		ELSEIF vProveedorID IS NULL AND vBeneficioId IS NOT NULL THEN
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo > vPerCtrl
			AND cu.persona_beneficio_id = vBeneficioId
			AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id)
			GROUP BY cu.socio_id;
	
		ELSEIF vProveedorID IS NOT NULL AND vBeneficioId IS NULL THEN
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo > vPerCtrl AND cu.proveedor_id = vProveedorID
			AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id)
			GROUP BY cu.socio_id;
	
	
					
		ELSE
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo > vPerCtrl
			AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id)
			GROUP BY cu.socio_id;
	
			
		END IF;	
	
	END IF;		
	RETURN vValor;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `isAsincronoStop`(vPID INT) RETURNS tinyint(1)
    NO SQL
BEGIN
	DECLARE vEstado CHAR(1);
	DECLARE vAsincStat BOOLEAN;
	DECLARE vRUN BOOLEAN;
	SELECT estado INTO vEstado FROM asincronos WHERE id = vPID;
	IF vEstado = 'S' THEN 
		SET vRUN = TRUE;
		
	ELSE SET vRUN = FALSE;
	END IF;
	RETURN vRUN;
END$$
DELIMITER ;


