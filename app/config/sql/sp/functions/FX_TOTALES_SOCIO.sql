CREATE DEFINER=`root`@`%` FUNCTION `FX_TOTALES_SOCIO`(
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
    END