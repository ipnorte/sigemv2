CREATE DEFINER=`root`@`localhost` PROCEDURE `p_consultar_asincrono`(in vID int(11))
BEGIN
	select * from v_asincronos where ID = vID;
    END