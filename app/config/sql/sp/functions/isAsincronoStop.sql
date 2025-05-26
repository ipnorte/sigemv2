CREATE DEFINER=`root`@`%` FUNCTION `isAsincronoStop`(vPID INT) RETURNS tinyint(1)
    NO SQL
BEGIN
	DECLARE vEstado CHAR(1);
	DECLARE vAsincStat BOOLEAN;
	DECLARE vRUN BOOLEAN;
	SELECT estado INTO vEstado FROM asincronos WHERE id = vPID;
	IF vEstado = 'S' THEN 
		SET vRUN = TRUE;
		
	ELSE SET vRUN = FALSE;
	END IF;
	RETURN vRUN;
END