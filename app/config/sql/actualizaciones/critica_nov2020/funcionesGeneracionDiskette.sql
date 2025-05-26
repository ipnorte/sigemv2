DROP FUNCTION IF EXISTS `FX_VALIDA_CBU`;
DROP FUNCTION IF EXISTS `FX_VALIDA_CBU_DIGITO`;
DROP FUNCTION IF EXISTS `FX_VALIDA_CBU_GENDIGITO`;
DROP FUNCTION IF EXISTS `FX_GENERAR_DISKETTE_BANCO`;


DELIMITER $$
CREATE DEFINER=CURRENT_USER FUNCTION `FX_VALIDA_CBU`(
CBU VARCHAR(22) 
) RETURNS int(11)
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

DELIMITER $$
CREATE DEFINER=CURRENT_USER FUNCTION `FX_VALIDA_CBU_DIGITO`(
BLOQUE VARCHAR(22)
) RETURNS int(11)
BEGIN
SET @PONDERADOR = '9713';
SET @POSICION = LENGTH(BLOQUE);
SET @SUMA = 0; 
SET @DIGITO = NULL;
SET @N = 1;

WHILE @POSICION > 0 DO

	SET @VALOR = SUBSTR(BLOQUE,@POSICION,1);
    SET @RESTO = ((4 - @N) % 4);
    IF @RESTO < 0 THEN SET @RESTO = @RESTO + 4; END IF;
    SET @PONDERADORVALOR = SUBSTR(@PONDERADOR,(@RESTO + 1),1);
	SET @SUMA = @SUMA + (@VALOR * @PONDERADORVALOR); 

    SET @POSICION = @POSICION - 1;
    SET @N = @N + 1;
    
END WHILE;

RETURN CAST(RIGHT(CAST(10-(@SUMA%10) AS CHAR),1) AS UNSIGNED);
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER FUNCTION `FX_VALIDA_CBU_GENDIGITO`(
BLOQUE VARCHAR(22)
) RETURNS int(11)
BEGIN
SET @PONDERADOR = '9713';
SET @POSICION = LENGTH(BLOQUE);
SET @SUMA = 0; 
SET @DIGITO = NULL;
SET @N = 1;

WHILE @POSICION > 0 DO

	SET @VALOR = SUBSTR(BLOQUE,@POSICION,1);
    SET @RESTO = ((4 - @N) % 4);
    IF @RESTO < 0 THEN SET @RESTO = @RESTO + 4; END IF;
    SET @PONDERADORVALOR = SUBSTR(@PONDERADOR,(@RESTO + 1),1);
	SET @SUMA = @SUMA + (@VALOR * @PONDERADORVALOR); 

    SET @POSICION = @POSICION - 1;
    SET @N = @N + 1;
    
END WHILE;

RETURN CAST(RIGHT(CAST(10-(@SUMA%10) AS CHAR),1) AS UNSIGNED);
END$$
DELIMITER ;


DELIMITER $$
CREATE DEFINER=CURRENT_USER FUNCTION `FX_GENERAR_DISKETTE_BANCO`(
vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
vLiqId INT(11),
vEmpresas TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
vTurnos TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
vFechaDebito date,
vFechaPresentacion date,
vUsuario varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci,
vUUID varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
vNombreArchivo varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci,
vValidarCBU boolean
) RETURNS int(11)
BEGIN
DECLARE envioID INT(11);

select nombre into @bancoNombre from bancos where cast(id as unsigned) = cast(vBancoId as unsigned);
-- set vNOMBRE_BANCO = '';

set vNombreArchivo = ifnull(vNombreArchivo,'SIN_NOMBRE.txt');

-- MARCO LOS STOPS PARA EXCLUIRLOS
update liquidacion_socios,
(select socio_calificaciones.socio_id,socio_calificaciones.calificacion,global_datos.logico_2,global_datos.concepto_1 from socio_calificaciones
inner join global_datos on  global_datos.id = socio_calificaciones.calificacion
group by socio_calificaciones.socio_id
order by socio_calificaciones.created desc) calificaciones
set error_intercambio = if(calificaciones.logico_2,calificaciones.concepto_1,'')
,error_cbu = calificaciones.logico_2
where liquidacion_id = vLiqId
and liquidacion_socios.socio_id = calificaciones.socio_id
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

-- MARCO LOS ERRORES DE CBU
IF(vValidarCBU = TRUE) THEN
	update liquidacion_socios
	set error_intercambio = 'ERROR CBU'
	,error_cbu = 1
	where liquidacion_id = vLiqId
    and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
	and FIND_IN_SET (turno_pago,cast(vTurnos as char))
	and diskette = TRUE and FX_VALIDA_CBU(cbu) = FALSE;
END IF;

SET group_concat_max_len = 18446744073709551615;
insert into liquidacion_socio_envios(liquidacion_id,banco_id,
fecha_debito,banco_nombre,cantidad_registros,importe_debito,user_created,lote
,longitud_registro,archivo,created,status,uuid)
select vLiqId,vBancoId,vFechaDebito
,@bancoNombre,count(*),sum(importe_adebitar)
,vUsuario
,group_concat(ltrim(rtrim(intercambio)) separator '')
,length(replace(intercambio,'\r\n',''))
,vNombreArchivo
,now()
,'OK',vUUID collate utf8_general_ci
from liquidacion_socios
where liquidacion_id = vLiqId
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE and error_cbu = FALSE;


set envioID = last_insert_id();
insert into liquidacion_socio_envio_registros(liquidacion_socio_envio_id,liquidacion_socio_id,socio_id,importe_adebitar,identificador_debito,registro,excluido,motivo,user_created)
select envioID,id,socio_id,importe_adebitar,
concat(lpad(socio_id,5,0),lpad(liquidacion_id,4,0),lpad(registro,2,0)),intercambio,error_cbu,error_intercambio,vUsuario 
from liquidacion_socios
where liquidacion_id = vLiqId
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

RETURN envioID;
END$$
DELIMITER ;


