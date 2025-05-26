CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_persona`(
	IN 
		vTIPO_DOCUMENTO varchar(12),
		vDOCUMENTO varchar(11),
		vAPELLIDO varchar(100),
		vNOMBRE varchar(100),
		vCUIT_CUIL varchar(11)
    )
BEGIN
	if vTIPO_DOCUMENTO is null then set vTIPO_DOCUMENTO = 'PERSTPDC0001';
	end if;
	if LENGTH(trim(vDOCUMENTO)) < 8 AND vTIPO_DOCUMENTO = 'PERSTPDC0001' then
		set vDOCUMENTO = right(concat('00000000',TRIM(vDOCUMENTO)),8);
	end if;	
	
	INSERT INTO personas (tipo_documento,documento,apellido,nombre,cuit_cuil)
	VALUES(vTIPO_DOCUMENTO,vDOCUMENTO,vAPELLIDO,vNOMBRE,vCUIT_CUIL);
	SELECT * FROM v_personas WHERE ID = (SELECT LAST_INSERT_ID());
    END