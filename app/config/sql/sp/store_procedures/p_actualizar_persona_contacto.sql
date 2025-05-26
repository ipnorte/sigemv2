CREATE DEFINER=`root`@`localhost` PROCEDURE `p_actualizar_persona_contacto`(
in vID int(11),
vTELEFONO_FIJO varchar(50),
vTELEFONO_MOVIL varchar(50),
vTELEFONO_REFERENCIA varchar(50),
vPERSONA_REFERENCIA varchar(100),
vE_MAIL varchar(100)
)
BEGIN
update personas
set
	telefono_fijo = vTELEFONO_FIJO,
	telefono_movil = vTELEFONO_MOVIL,
	telefono_referencia = vTELEFONO_REFERENCIA,
	persona_referencia = vPERSONA_REFERENCIA,
	e_mail = vE_MAIL
where id = vID;	
END