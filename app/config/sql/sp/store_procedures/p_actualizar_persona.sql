CREATE DEFINER=`root`@`localhost` PROCEDURE `p_actualizar_persona`(
in vID int(11),
vFECHA_NACIMIENTO date,
vSEXO varchar(1),
vESTADO_CIVIL varchar(12),
vNOMBRE_CONYUGE varchar(150),
vCUIT varchar(11)
)
BEGIN
update personas
set
	fecha_nacimiento = vFECHA_NACIMIENTO,
	sexo = vSEXO,
	estado_civil = vESTADO_CIVIL,
	nombre_conyuge = vNOMBRE_CONYUGE,
	cuit_cuil = vCUIT
where id = vID;	
END