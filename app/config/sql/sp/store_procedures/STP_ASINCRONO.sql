CREATE DEFINER=`root`@`localhost` PROCEDURE `STP_ASINCRONO`(vPID INT,vStatus VARCHAR(1),vTot INT, vCont INT, vMsg VARCHAR(255))
BEGIN
	DECLARE vPorc DECIMAL(10,2) DEFAULT 0;
	
	SET vPorc = ROUND((vCont / vTot) * 100,2);
	SET vMsg = CONCAT('[',vCont,'|',vTot,'] ',vMsg);
	
	UPDATE asincronos 
	SET estado = vStatus, total = vTot, contador = vCont, porcentaje = vPorc, msg = vMsg
	WHERE id = vPID;
END