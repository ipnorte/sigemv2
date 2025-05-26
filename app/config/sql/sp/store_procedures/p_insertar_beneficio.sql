CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_beneficio`(
in vPERSONA_ID INT(11),
vCODIGO_BENEFICIO VARCHAR(12),
vFECHA_INGRESO date,
vCODIGO_EMPRESA varchar(12),
vNRO_LEGAJO varchar(50),
vCODIGO_REPARTICION VARCHAR(11),
vTURNO_PAGO VARCHAR(12),
vCBU varchar(23),
vBANCO_ID varchar(5),
vSUCURSAL varchar(5),
vNRO_CTA_BCO varchar(50)
)
BEGIN
	SET @ID = 0;
	SELECT id into @ID FROM persona_beneficios
	where persona_id = vPERSONA_ID
	and codigo_beneficio = vCODIGO_BENEFICIO
	and codigo_empresa = vCODIGO_EMPRESA
	and turno_pago = vTURNO_PAGO
	and cbu = vCBU
	order by id DESC LIMIT 1;
	IF vCODIGO_EMPRESA <> 'MUTUEMPRP001' THEN 
		SET vTURNO_PAGO = vCODIGO_EMPRESA;
	END IF;
	IF vCODIGO_EMPRESA = 'MUTUEMPRP001' THEN
		select turno into @TURNO from liquidacion_turnos where codigo_empresa = 'MUTUEMPRP001'
		and ifnull(codigo_reparticion,'') <> ''
		and SUBSTR(trim(vTURNO_PAGO),1,8) = trim(codigo_reparticion)
		limit 1;
		IF TRIM(@TURNO) <> TRIM(vTURNO_PAGO) AND IFNULL(@TURNO,'') <> '' THEN
			SET vTURNO_PAGO = TRIM(@TURNO);
		END IF;
	end if;
	
	IF @ID = 0 THEN	
		SET vBANCO_ID = RIGHT(concat('00000',substring(vCBU,1,3)),5);
		INSERT INTO persona_beneficios(persona_id,codigo_beneficio,codigo_empresa,
		codigo_reparticion,turno_pago,cbu,banco_id,nro_sucursal,nro_cta_bco,created)
		VALUES(vPERSONA_ID,vCODIGO_BENEFICIO,vCODIGO_EMPRESA,vCODIGO_REPARTICION,vTURNO_PAGO,
		vCBU,vBANCO_ID,vSUCURSAL,vNRO_CTA_BCO,now());
		SELECT LAST_INSERT_ID() INTO @ID;
	END IF;
	
	SELECT * FROM v_persona_beneficios where ID = @ID;
	
		
END