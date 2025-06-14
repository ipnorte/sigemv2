DELIMITER $$
CREATE DEFINER=current_user FUNCTION `FX_VALIDA_CBU`(
CBU VARCHAR(22) 
) RETURNS int
BEGIN
DECLARE POSICION INT;
DECLARE DIGITO INT;
DECLARE BLOQUE VARCHAR(14);
DECLARE SUMA INT;
DECLARE PONDERADOR VARCHAR(4);

SET PONDERADOR = '9713';
SET POSICION = 1;
SET SUMA = 0;

-- DIGITO VERIFICADOR 1
SET BLOQUE = SUBSTR(CBU,1,8);
SET DIGITO = SUBSTR(CBU,8,1);
WHILE POSICION <= LENGTH(BLOQUE) DO
	SET @VAL1 = CAST(SUBSTR(BLOQUE, (LENGTH(BLOQUE) - POSICION),1) AS UNSIGNED);  
    SET @VAL2 = CAST(SUBSTR(PONDERADOR ,CASE (POSICION % 4) WHEN 1 THEN 4 WHEN 2 THEN 3 WHEN 3 THEN 2 WHEN 0 THEN 1 END,1) AS UNSIGNED);
    SET SUMA = SUMA + (@VAL1 * @VAL2);
    SET POSICION = POSICION + 1;
END WHILE;
IF CAST(RIGHT(CAST(10-(SUMA%10) AS CHAR),1) AS UNSIGNED) <> DIGITO THEN
	RETURN 0;
END IF;
SET POSICION = 1;
SET SUMA = 0;
-- DIGITO VERIFICADOR 2
SET BLOQUE = SUBSTR(CBU,9,14);
SET DIGITO = SUBSTR(CBU,22,1);
WHILE POSICION <= LENGTH(BLOQUE) DO
	SET @VAL1 = CAST(SUBSTR(BLOQUE, (LENGTH(BLOQUE) - POSICION),1) AS UNSIGNED);   
    SET @VAL2 = CAST(SUBSTR(PONDERADOR ,CASE (POSICION % 4) WHEN 1 THEN 4 WHEN 2 THEN 3 WHEN 3 THEN 2 WHEN 0 THEN 1 END,1) AS UNSIGNED);
    SET SUMA = SUMA + (@VAL1 * @VAL2);
    SET POSICION = POSICION + 1;
END WHILE;
IF CAST(RIGHT(CAST(10-(SUMA%10) AS CHAR),1) AS UNSIGNED) <> DIGITO THEN
	RETURN 0;
END IF;
RETURN 1; 
END$$
DELIMITER ;
