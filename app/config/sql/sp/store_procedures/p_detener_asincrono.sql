CREATE DEFINER=`root`@`localhost` PROCEDURE `p_detener_asincrono`(vID INT(11))
BEGIN
	UPDATE asincronos 
	set
		estado = 'S',
		msg = '*** DETENIDO POR EL USUARIO ***'
	where id = vID;
	SELECT * FROM v_asincronos WHERE ID = vID;	
    END