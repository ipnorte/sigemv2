CREATE DEFINER=`root`@`localhost` PROCEDURE `p_actualizar_password`(
	in vID INT(11), vPASS VARCHAR(40)
    )
BEGIN
    
	UPDATE usuarios set `password` =  vPASS
	where id = vID;
    END