CREATE DEFINER=`root`@`localhost` PROCEDURE `p_actualizar_persona_domicilio`(
in vID int(11),
vCALLE varchar(150),
vNUMERO_CALLE varchar(5),
vPISO varchar(5),
vDPTO varchar(5),
vBARRIO varchar(100),
vLOCALIDAD_ID int(11),
vLOCALIDAD_DESC varchar(150),
vCODIGO_POSTAL varchar(8),
vPROVINCIA_ID int(11)
)
BEGIN
update personas
set
	calle = vCALLE,
	numero_calle = vNUMERO_CALLE,
	piso = vPISO,
	dpto = vDPTO,
	barrio = vBARRIO,
	localidad_id = vLOCALIDAD_ID,
	localidad = vLOCALIDAD_DESC,
	codigo_postal = vCODIGO_POSTAL,
	provincia_id = vPROVINCIA_ID
where id = vID;	
END