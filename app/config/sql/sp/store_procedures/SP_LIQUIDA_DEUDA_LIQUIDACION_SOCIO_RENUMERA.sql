CREATE DEFINER=`root`@`127.0.0.1` PROCEDURE `SP_LIQUIDA_DEUDA_LIQUIDACION_SOCIO_RENUMERA`(
vSOCIO_ID INT(11),
vLIQUIDACION_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
DECLARE vID INT(11);
DECLARE cursor_socio CURSOR FOR 
SELECT id FROM liquidacion_socios
where liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID
order by periodo desc;

DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;

SET @REGISTRO = 1;

OPEN cursor_socio;
c1_loop: LOOP
	FETCH cursor_socio INTO vID;
    
	IF (l_last_row = 1) THEN
		LEAVE c1_loop; 
	END IF;	 
    
    SET @REGISTRO = @REGISTRO + 1;
    
	UPDATE liquidacion_socios 
	SET registro = @REGISTRO WHERE id = vID;
    
END LOOP c1_loop;
END