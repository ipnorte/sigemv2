CREATE DEFINER=`root`@`localhost` PROCEDURE `p_socio_calificacion_score_resumen`(IN vSOCIO_ID INT(11))
BEGIN
DECLARE done INT DEFAULT 0;
DECLARE vCALIFICACION VARCHAR(50);
DECLARE vCANTIDAD INT(11);
DECLARE vRESUMEN TEXT;
DECLARE CURSOR_CALIFICACIONES CURSOR FOR select calificacion.concepto_1, count(*) as cantidad from socio_calificaciones 
inner join global_datos calificacion on (calificacion.id = socio_calificaciones.calificacion)
where socio_id = vSOCIO_ID
group by socio_calificaciones.calificacion
order by cantidad desc;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

SET vRESUMEN = '';

OPEN CURSOR_CALIFICACIONES;
REPEAT FETCH CURSOR_CALIFICACIONES INTO vCALIFICACION, vCANTIDAD;
	IF NOT done THEN
		SET vRESUMEN = CONCAT(vRESUMEN , CONCAT(vCALIFICACION,' (',vCANTIDAD,'), ')); 
	END IF;
UNTIL done END REPEAT;
CLOSE CURSOR_CALIFICACIONES;
IF vRESUMEN = '' THEN
SET vRESUMEN = '*** SIN REGISTRO ***';
END IF;
SELECT vRESUMEN;
END