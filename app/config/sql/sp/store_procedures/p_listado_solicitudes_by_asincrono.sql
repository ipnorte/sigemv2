CREATE DEFINER=`root`@`localhost` PROCEDURE `p_listado_solicitudes_by_asincrono`(in vPID int(11))
BEGIN
	DECLARE l_last_row INT DEFAULT 0;
	declare vFD date;
	declare vFH date;
	declare vESTADO varchar(12);
	DECLARE vID INT(11);
	
	
	
	DECLARE  c_solicitudes CURSOR FOR 
	SELECT ID FROM orden_descuento_cuotas;
	
	
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;	
	
	SELECT p1,p2,p3 INTO vFD,vFH,vESTADO FROM asincronos WHERE id = vPID;	
		
	
	open c_solicitudes;
	select FOUND_ROWS() into @REGISTROS ;
	SET @N = 1;
	c1_loop: LOOP 
		FETCH c_solicitudes INTO vID;
		IF (l_last_row = 1) THEN
			LEAVE c1_loop; 
		END IF;	
		SET @PORC = ROUND((@N / @REGISTROS) * 100,0);
		
		UPDATE asincronos set total = @REGISTROS,contador = @N,
		porcentaje = @PORC, msg = concat('PROCESANDO ', @N , '/',@REGISTROS) where id = vPID;
		
		SET @N = @N + 1;
		
	END LOOP c1_loop;
END