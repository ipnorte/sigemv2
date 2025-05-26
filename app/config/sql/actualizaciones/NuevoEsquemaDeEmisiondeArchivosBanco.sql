/**/
/* TABLA BANCOS */
ALTER TABLE `bancos` 
ADD COLUMN `metodo_str_encode` VARCHAR(100) NULL AFTER `parametros_intercambio`,
ADD COLUMN `metodo_str_decode` VARCHAR(100) NULL AFTER `metodo_str_encode`;

-- si ya existen
ALTER TABLE `bancos` 
CHANGE COLUMN `metodo_str_encode` `metodo_str_encode` VARCHAR(100) NULL DEFAULT NULL ,
CHANGE COLUMN `metodo_str_decode` `metodo_str_decode` VARCHAR(100) NULL DEFAULT NULL ;


-- correr todo junto desde aca
/* CREO LAS FUNCIONES */

DROP FUNCTION IF EXISTS `FX_GENERAR_DISKETTE_BANCO`;
DROP FUNCTION IF EXISTS `FX_VALIDA_CBU`;

DELIMITER $$
CREATE DEFINER=CURRENT_USER FUNCTION `FX_GENERAR_DISKETTE_BANCO`(
vBancoId varchar(5),
vLiqId INT(11),
vTurnos TEXT,
vFechaDebito date,
vFechaPresentacion date,
vUsuario varchar(45),
vUUID varchar(20),
vNombreArchivo varchar(40),
vValidarCBU boolean
) RETURNS int
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
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

-- MARCO LOS ERRORES DE CBU
IF(vValidarCBU = TRUE) THEN
	update liquidacion_socios
	set error_intercambio = 'ERROR CBU'
	,error_cbu = 1
	where liquidacion_id = vLiqId
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
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;


set envioID = last_insert_id();
insert into liquidacion_socio_envio_registros(liquidacion_socio_envio_id,liquidacion_socio_id,socio_id,importe_adebitar,identificador_debito,registro,excluido,motivo,user_created)
select envioID,id,socio_id,importe_adebitar,
concat(lpad(socio_id,5,0),lpad(liquidacion_id,4,0),lpad(registro,2,0)),intercambio,0,'',vUsuario 
from liquidacion_socios
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

RETURN envioID;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER FUNCTION `FX_VALIDA_CBU`(
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


/*  CREO LOS PROCEDIMIENTOS */

DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_COMAFI`;
DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_CORDOBA`;
DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_CREDICOOP`;
DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_FRANCES`;
DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_GALICIA`;
DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_ITAU`;
DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_MACRO`;
DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_NACION`;
DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_SANTANDER`;
DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_STANDARBANK`;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_COMAFI`(
IN vBancoId varchar(5),
IN vLiqId INT(11),
IN vTurnos TEXT,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45),
in vUUID varchar(20),
in vEmpresaCodigo varchar(20),
in vEmpresaCuit varchar(21),
in vEmpresaPrestacion varchar(20),
in vEmpresaNombre varchar(20),
in vEmpresaCtaBco varchar(20),
in vImporteMaximoRegistro DECIMAL(10,2)
)
BEGIN

start transaction;

-- inicializo
update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

update liquidacion_socios
set intercambio = concat(
"051"
,date_format(vFechaDebito,'%d%m%Y')
,lpad(ltrim(rtrim(vEmpresaCodigo)),5,0)
,rpad(concat(lpad(liquidacion_socios.socio_id,5,0),lpad(liquidacion_id,4,0),lpad(registro,2,0)),22,' ')
,'0'
,rpad(cbu,22,0)
,lpad(round((if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar) * 100),0),10,0)
,lpad(ltrim(rtrim(vEmpresaCuit)),11,0)
,rpad(ltrim(rtrim(vEmpresaPrestacion)),10,' ')
,lpad(ltrim(rtrim(liquidacion_socios.id)),15,' ')
,rpad(concat(lpad(liquidacion_socios.socio_id,5,0),lpad(liquidacion_id,4,0),lpad(registro,2,0)),15,' ')
,lpad(ltrim(rtrim(vEmpresaCtaBco)),15,0)
,rpad('',22,' ')
,rpad('',3,' ')
,'\r\n'
)
,importe_adebitar = if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar)
,banco_intercambio = vBancoId
,fecha_debito = vFechaDebito
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;


-- GENERO EL ARCHIVO
set @fileName = concat('EB',vEmpresaCodigo,'.',date_format(vFechaPresentacion,'%Y%m%d'));
SET @ENVIOID = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,TRUE);


commit;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_CORDOBA`(
IN vBancoId varchar(5),
IN vLiqId INT(11),
IN vTurnos TEXT,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45),
in vUUID varchar(20),
in vNroConvenio varchar(20),
in vImporteMaximoRegistro DECIMAL(10,2)
)
BEGIN

start transaction;

-- inicializo
update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

update liquidacion_socios,(SELECT @reg := 0) r
set intercambio = CONCAT(
'003'
,right(concat(lpad('',5,0),if(substr(cbu,1,3) = '020',ifnull(sucursal,0),'800')),5)
,'01'
,'3'
,right(concat(lpad('',9,0),ifnull(nro_cta_bco,0)),9)
,lpad(round((if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar) * 100),0),18,0)
,date_format(vFechaDebito,'%Y%m%d')
,right(lpad(vNroConvenio,5,0),5)
,lpad(@reg := @reg + 1,6,0)
,lpad(cbu,22,0)
,lpad(registro,2,0)
,concat(lpad(socio_id,12,0),lpad(liquidacion_id,8,0),lpad(registro,2,0))
,'\r\n'
)
,importe_adebitar = if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar)
,banco_intercambio = vBancoId
,fecha_debito = vFechaDebito
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

set @fileName = concat('DEB',right(rpad(vNroConvenio,5,0),5),'.HAB');
SET @envioId = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,TRUE);


commit;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_CREDICOOP`(
IN vBancoId varchar(5),
IN vLiqId INT(11),
IN vTurnos TEXT,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45),
in vUUID varchar(20),
in vEmpresa varchar(20),
in vDescripcion varchar(20),
in vEmpresaCuit varchar(20),
in vImporteMaximoRegistro DECIMAL(10,2),
in vLongIdd int(11)
)
BEGIN

start transaction;

-- inicializo
update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;


update liquidacion_socios
set intercambio = CONCAT(
substr(cbu,1,3)
,'51'
,date_format(vFechaDebito,'%y%m%d')
,vEmpresa
,rpad(lpad(socio_id,vLongIdd,0),22,' ')
,'P'
,SUBSTR(cbu,1,8)
,SUBSTR(cbu,9,14)
,lpad(round((if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar) * 100),0),10,0)
,lpad(ltrim(rtrim(vEmpresaCuit)),11,0)
,substr(rpad(vDescripcion,10,' '),1,10)
,lpad(id,15,0)
,'\r\n'
)
,importe_adebitar = if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar)
,banco_intercambio = vBancoId
,fecha_debito = vFechaDebito
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

-- GENERO EL ARCHIVO
set @fileName = concat('MAIN',substr(vEmpresa,1,3),'_',date_format(vFechaDebito,'%Y%m%d'),'.txt');
SET @ENVIOID = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,TRUE);

commit;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_FRANCES`(
IN vBancoId varchar(5),
IN vLiqId INT(11),
IN vTurnos TEXT,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45),
in vUUID varchar(20),
in vCodigoEmpresa varchar(5),
in vSucursalCuentaCargo varchar(4),
in vCuentaCargoDV varchar(2),
in vNroCuentaCargo char(10),
in vDivisa varchar(3),
in vCodigoServicio varchar(20),
in vNombreOrdenante varchar(36),
in vTipoCuentaCBU varchar(2),
in vLongitudClave varchar(10),
in vConceptoDebito varchar(20),
in vFileNameCodEmpre boolean,
in vImporteMaximoRegistro DECIMAL(10,2)
)
BEGIN
    /**
     * LOTE DE DATOS BANCO FRANCES
     * Se generan 4 registros por cada debito
     * REGISTRO_1
     *
     *  CODIGO (4) FIX 4210
     *  ID_EMPRESA (5)
     *  LIBRE (2) BLANCOS
     *  ID_BENEFICIARIO (22)
     *  CBU (22)
     *  IMPORTE_ENTERO (13)
     *  IMPORTE_DECIMAL (2)
     *  CODIGO_DEVOLUCION (6) ENVIO COMPLETAR CON BLANCOS
     *  REFERENCIA (22) REFERENCIA DEL DEBITO
     *  FECHA_VTO (8) FECHA DEBITO AAAAMMDD
     *  LIBRE (2) BLANCOS
     *  NRO DE FACTURA (15) PARA CBU FRANCES VAN CEROS, PARA CBU <> FRANCES UN NUMERO <> 0
     *  CODIGO ESTADO DEV. DOMICILIACIONES (1) BLANCO
     *  DESCRIPCION DE LA DEVOLUCION (40) BLANCOS
     *  FILLER (86) BLANCOS
     *
     * REGISTRO_2
     *
     *  CODIGO_REGISTRO (4) FIX 4220
     *  ID_EMPRESA (5)
     *  LIBRE (2) BLANCOS
     *  ID_BENEFICIARIO (22)
     *  NOMBRE_BENEFICIARIO (36)
     *  DOMICILIO (36) BLANCOS
     *  DOMICILIO CONT (36) BLANCOS
     *  FILLER (109) BLANCOS
     *
     * REGISTRO_3

     *  CODIGO (4) FIX 4230
     *  ID_EMPRESA (5)
     *  LIBRE (2) BLANCOS
     *  ID_BENEFICIARIO (22)
     *  LOCALIDAD (36) BLANCOS
     *  PROVINCIA (36) BLANCOS
     *  CODIGO_POSTAL (36) BLANCOS
     *  FILLER (109) BLANCOS
     *
     * REGISTRO_4
     *
     *  CODIGO (4) FIX 4240
     *  ID_EMPRESA (5)
     *  LIBRE (2) BLANCOS
     *  ID_BENEFICIARIO (22)
     *  CONCEPTO_DEBITO (40)
     *  FILLER (177)
     *
     * @param type $campos
     */

DECLARE REGISTROS INT(11);
DECLARE TOTAL_DEBITO DECIMAL(10,2);

start transaction;

-- inicializo
update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

update liquidacion_socios
set intercambio = CONCAT(
-- registro 1
'4210'
-- bloque comun
,lpad(vCodigoEmpresa,5,0)
,lpad('',2,' ')
,rpad(lpad(socio_id,cast(vLongitudClave as unsigned),0),22,' ')
-- ----------------------------
,lpad(cbu,22,0)
,lpad(round((if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar) * 100),0),15,0)
,rpad('',6,' ')
,rpad(concat(lpad(socio_id,7,0),lpad(liquidacion_id,6,0),lpad(registro,2,0)),22,' ')
,date_format(vFechaDebito,'%Y%m%d')
,rpad('',2,' ')
,if(substr(cbu,1,3) = '017',lpad('',15,0),concat(lpad(socio_id,7,0),lpad(liquidacion_id,6,0),lpad(registro,2,0)))
,rpad('',127,' ')
,'\r\n'
-- registro 2
'4220'
-- bloque comun
,lpad(vCodigoEmpresa,5,0)
,lpad('',2,' ')
,rpad(lpad(socio_id,cast(vLongitudClave as unsigned),0),22,' ')
-- ----------------------------
,rpad(upper(substr(replace(ltrim(rtrim(apenom)),',',' '),1,36)),36,' ')
,lpad('',181,' ')
,'\r\n'

-- registro 3
'4230'
-- bloque comun
,lpad(vCodigoEmpresa,5,0)
,lpad('',2,' ')
,rpad(lpad(socio_id,cast(vLongitudClave as unsigned),0),22,' ')
-- ----------------------------
,lpad('',217,' ')
,'\r\n'


-- registro 4
'4240'
-- bloque comun
,lpad(vCodigoEmpresa,5,0)
,lpad('',2,' ')
,rpad(lpad(socio_id,cast(vLongitudClave as unsigned),0),22,' ')
-- ----------------------------
,rpad(upper(vConceptoDebito),40,' ')
,lpad('',177,' ')
,'\r\n'


)
,importe_adebitar = if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar)
,banco_intercambio = vBancoId
,fecha_debito = vFechaDebito
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;


-- GENERO EL ARCHIVO
set @fileName = concat('DTO',lpad(vCodigoEmpresa,5,0),'.REC');
SET @envioId = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,TRUE);

select ifnull(cantidad_registros,0),importe_debito INTO REGISTROS,TOTAL_DEBITO
from liquidacion_socio_envios where id = @envioId;

set @header = concat(
'4110'
,lpad(vCodigoEmpresa,5,0)
,date_format(vFechaPresentacion,'%Y%m%d')
,date_format(vFechaPresentacion,'%Y%m%d')
,'0017'
,rpad(vSucursalCuentaCargo,4,0)
,rpad(vCuentaCargoDV,2,0)
,rpad(vNroCuentaCargo,10,0)
,rpad(vCodigoServicio,10,' ')
,rpad(vDivisa,3,' ')
,'0'
,substr(@fileName,1,12)
,rpad(substr(ltrim(rtrim(vNombreOrdenante)),1,36),36,' ')
,lpad(vTipoCuentaCBU,2,0)
,rpad('',141,' ')
,'\r\n'
);

set @trailer=concat(
'4910'
,lpad(vCodigoEmpresa,5,0)
,lpad(round((TOTAL_DEBITO * 100),0),15,0)
,lpad(REGISTROS,8,0)
,lpad(REGISTROS + 2,10,0)
,rpad('',208,' ')
,'\r\n');

update liquidacion_socio_envios 
set lote = concat(@header,lote,@trailer)
where id = @envioId;

commit;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_GALICIA`(
IN vBancoId varchar(5),
IN vLiqId INT(11),
IN vTurnos TEXT,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45),
in vUUID CHAR(20) CHARSET UTF8 COLLATE utf8_general_ci,
in vPrestacion char(20),
in vImporteMaximoRegistro DECIMAL(10,2)
)
BEGIN

DECLARE REGISTROS INT(11);
DECLARE TOTAL_DEBITO DECIMAL(10,2);

start transaction;

-- inicializo
update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;


update liquidacion_socios
set intercambio = CONCAT(
'0370'
,rpad(concat(lpad(liquidacion_id,5,0),lpad(socio_id,6,0)),22,' ')
,concat('0',substr(cbu,1,7))
,substr(cbu,8,1)
,concat('000',substr(cbu,9,13))
,substr(cbu,22,1)
,rpad(concat(lpad(socio_id,7,0),lpad(liquidacion_id,5,0),lpad(registro,3,0)),15,' ')
,date_format(vFechaDebito,'%Y%m%d')
,lpad(round((if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar) * 100),0),14,0)
,rpad('',22,' ') -- 2do y tercer vtos
,'0' -- moneda
,rpad('',22,' ') -- varios
,rpad(id,10,' ') -- factura
,rpad('',30,' ') -- fecha cobro, importe y fecha acreditac
,rpad('',26,' ') -- filler
,'\r\n'
)
,importe_adebitar = if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar)
,banco_intercambio = vBancoId
,fecha_debito = vFechaDebito
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

-- GENERO EL ARCHIVO
set @fileName = concat('GALICIA_',date_format(vFechaDebito,'%Y%m%d'),'.txt');
SET @envioId = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,TRUE);

select ifnull(cantidad_registros,0),importe_debito INTO REGISTROS,TOTAL_DEBITO
from liquidacion_socio_envios where id = @envioId;

-- anexar cabecera y detalle
set @info = concat(vPrestacion,'C',date_format(vFechaPresentacion,'%Y%m%d'),'1','EMPRESA',lpad(round((TOTAL_DEBITO * 100),0),14,0),lpad(REGISTROS,7,0),rpad('',304,' '));
set @header=concat('0000',@info,'\r\n');
set @trailer=concat('9999',@info,'\r\n');

set @header = ifnull(@header,'ERROR HEADER\r\n');
set @trailer = ifnull(@trailer,'ERROR TRAILER\r\n');

update liquidacion_socio_envios 
set lote = concat(@header,lote,@trailer)
where id = @envioId;

commit;



END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_ITAU`(
IN vBancoId varchar(5),
IN vLiqId INT(11),
IN vTurnos TEXT,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45),
in vUUID varchar(20),
in vNroArchivo int,
in vCuitEmpresa varchar(20),
in vConvenio varchar(20),
in vFiller varchar(20),
in vImporteMaximoRegistro DECIMAL(10,2)
)
BEGIN

DECLARE REGISTROS INT(11);
DECLARE TOTAL_DEBITO DECIMAL(10,2);

start transaction;

update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

update liquidacion_socios
set intercambio = CONCAT(
'DAFC'
,lpad(concat(lpad(socio_id,5,0),lpad(liquidacion_id,4,0),lpad(registro,2,0)),15,0)
,rpad('',7,' ')
,'000'
,rpad(concat(lpad(socio_id,5,0),lpad(liquidacion_id,4,0),lpad(registro,2,0)),22,vFiller)
,rpad(substr(replace(apenom,',',' '),1,60),60,' ')
,'CL'
,lpad(cuit_cuil,11,0)
,rpad('',41,' ')
,rpad('',49,0)
,date_format(vFechaDebito,'%Y%m%d')
,lpad(round((if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar) * 100),0),17,0)
,rpad('',32,0) -- 2do vto
,rpad('',32,0) -- 3do vto
,rpad('',32,0)
,rpad('',24, ' ')
,rpad('',7,0)
,lpad(cbu,22,0)
,rpad('',52,0)
,rpad('',168, ' ')
,rpad('',6,0)
,rpad('',13, ' ')
,rpad('',4,0)
,rpad('',169, ' ')
,'\r\n'
)
,importe_adebitar = if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar)
,banco_intercambio = vBancoId
,fecha_debito = vFechaDebito
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

set @fileName = concat('ITAU_',lpad(ifnull(vNroArchivo,1),2,0),'_',date_format(vFechaDebito,'%Y%m%d'),'.txt');
SET @envioId = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,TRUE);

select ifnull(cantidad_registros,0),importe_debito INTO REGISTROS,TOTAL_DEBITO
from liquidacion_socio_envios where id = @envioId;

set @header = concat(
'H'
,lpad(ifnull(vCuitEmpresa,0),11,0)
,'300'
,lpad(ifnull(vConvenio,0),6,0)
,lpad(ifnull(vNroArchivo,0),5,0)
,date_format(vFechaPresentacion,'%Y%m%d')
,' BD'
,rpad('',763,' ')
,'\r\n'
);

set @trailer=concat(
'T'
,lpad(ifnull(vCuitEmpresa,0),11,0)
,'300'
,lpad(ifnull(vConvenio,0),6,0)
,lpad(ifnull(vNroArchivo,0),5,0)
,date_format(vFechaPresentacion,'%Y%m%d')
,rpad('',5,0)
,lpad(round((TOTAL_DEBITO * 100),0),17,0)
,lpad(REGISTROS,9,0)
,rpad('',735,' ')
,'\r\n');

update liquidacion_socio_envios 
set lote = concat(@header,lote,@trailer)
where id = @envioId;

commit;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_MACRO`(
IN vBancoId varchar(5),
IN vLiqId INT(11),
IN vTurnos TEXT,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45),
in vUUID varchar(20),
in vNroArchivo int,
in vConvenio varchar(20),
in vImporteMaximoRegistro DECIMAL(10,2)
)
BEGIN

DECLARE REGISTROS INT(11);
DECLARE TOTAL_DEBITO DECIMAL(10,2);

start transaction;

update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

update liquidacion_socios
set intercambio = CONCAT(
'0'
,lpad(vConvenio,5,0)
,rpad('',10, ' ')
,lpad('',5, 0)
,substr(cbu,1,3)
,substr(cbu,4,4)
,if(substr(cbu,1,3) = '285',substr(cbu,9,1),0)
,if(substr(cbu,1,3) = '285',concat(substr(cbu,9,1),lpad(cast(substr(cbu,4,4) as unsigned),3,0),right(substr(cbu,9,13),11)),lpad(right(cbu,14),15,0))
,rpad(concat(lpad(socio_id,12,0),lpad(liquidacion_id,8,0),lpad(registro,2,0)),22,' ')
,rpad(id,15, ' ')
,rpad('',6, ' ')
,date_format(vFechaDebito,'%Y%m%d')
,'080'
,lpad(round((if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar) * 100),0),13,0)
,lpad('',41,0)
,rpad('',67, ' ')
,'0'
,'\r\n'
)
,importe_adebitar = if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar)
,banco_intercambio = vBancoId
,fecha_debito = vFechaDebito
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

set @fileName = concat('D',lpad(vConvenio,5,0),lpad(ifnull(vNroArchivo,1),2,0),'.285');
SET @envioId = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,TRUE);

select ifnull(cantidad_registros,0),importe_debito INTO REGISTROS,TOTAL_DEBITO
from liquidacion_socio_envios where id = @envioId;

set @header = concat(
'1'
,lpad(vConvenio,5,0)
,rpad('',10, ' ')
,lpad('',5,0)
,date_format(vFechaPresentacion,'%Y%m%d')
,lpad(round((TOTAL_DEBITO * 100),0),18,0)
,'08001'
,lpad('',98,0)
,rpad('',69, ' ')
,'0'
,'\r\n'
);

update liquidacion_socio_envios 
set lote = concat(@header,lote)
where id = @envioId;


COMMIT;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_NACION`(
IN vBancoId varchar(5),
IN vLiqId INT(11),
IN vTurnos TEXT,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45),
in vUUID varchar(20),
in vNroArchivo int,
in vSucursal varchar(20),
in vTipoCuenta varchar(20),
in vNroCuenta varchar(20),
in vMoneda varchar(1),
in vImporteMaximoRegistro DECIMAL(10,2)
)
BEGIN
/*
	 * 	1	TIPO_REGISTRO (1) VALOR = 2
	 * 	2	SUCURSAL (4)
	 * 	3	SISTEMA (2) CA=CAJA DE AHORRO O CTACTE ESPECIAL | CC=CUENTA CORRIENTE
	 * 	4	NRO_CUENTA (11) 1RA POSICION = 0
	 * 	5	IMPORTE(15) 13 ENTEROS 2 DECIMALES
	 * 	6	FECHA_VTO (8) COMPLETAR CON CEROS PARA ENVIO AAAAMMDD
	 * 	7	ESTADO (1) VALOR=0 PARA ENVIO
	 * 	8	MOTIVO_RECHAZO (30) VALOR=BLANK PARA ENVIO
	 * 	9	CONCEPTO (10) CONCEPTO DEBITO (envio el ID de la liquidacion_socios)
	 * 	10	FILLER (46) RELLENO (envio el identificador del debito)
*/

DECLARE REGISTROS INT(11);
DECLARE TOTAL_DEBITO DECIMAL(10,2);

start transaction;

-- inicializo
update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;


update liquidacion_socios
set intercambio = CONCAT(
'2'
,right(lpad(ifnull(ltrim(rtrim(sucursal)),0),5,0),4)
,'CA'
,right(lpad(ifnull(nro_cta_bco,0),11,0),11)
,lpad(round((if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar) * 100),0),15,0)
,date_format(vFechaPresentacion,'%Y%m%d')
,'0'
,rpad('',30,' ')
,concat(lpad(liquidacion_id,4,0),lpad(socio_id,6,0))
,rpad('',46,' ')
,'\r\n'
)
,importe_adebitar = if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar)
,banco_intercambio = vBancoId
,fecha_debito = vFechaDebito
,error_cbu = IF(ifnull(nro_cta_bco,0)=0 or ifnull(sucursal,0)=0,1,0)
,error_intercambio = 'S/INFO.CTA'
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

set @fileName = concat('NACION',date_format(vFechaDebito,'%Y%m%d'),'.txt');
SET @envioId = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,FALSE);

select ifnull(cantidad_registros,0),importe_debito INTO REGISTROS,TOTAL_DEBITO
from liquidacion_socio_envios where id = @envioId;

set @header = concat(
'1'
,lpad(ifnull(vSucursal,0),4,0)
,lpad(ifnull(vTipoCuenta,0),2,0)
,lpad(ifnull(vNroCuenta,0),10,0)
,lpad(ifnull(vMoneda,0),1,0)
,'E'
,date_format(vFechaDebito,'%m')
,lpad(ifnull(vNroArchivo,1),2,0)
,date_format(vFechaDebito,'%Y%m%d')
,'REE'
,rpad('',94,' ')
,'\r\n'
);

set @trailer=concat(
'3'
,lpad(round((TOTAL_DEBITO * 100),0),15,0)
,lpad(REGISTROS,6,0)
,rpad('',15,0)
,rpad('',6,0)
,rpad('',85,' ')
,'\r\n');

update liquidacion_socio_envios 
set lote = concat(@header,lote,@trailer)
where id = @envioId;

commit;


END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_SANTANDER`(
IN vBancoId varchar(5),
IN vLiqId INT(11),
IN vTurnos TEXT,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45),
in vUUID varchar(20),
in vDescripcion varchar(20),
in vLongPartida int(11),
in vImporteMaximoRegistro DECIMAL(10,2)
)
BEGIN

DECLARE REGISTROS INT(11);
DECLARE TOTAL_DEBITO DECIMAL(10,2);

start transaction;

update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

update liquidacion_socios
set intercambio = concat(
'11'
,rpad(ifnull(vDescripcion,'CUOTA'),10,' ')
,rpad(lpad(socio_id,vLongPartida,0),22,' ')
,lpad(cbu,22,0)
,date_format(vFechaDebito,'%Y%m%d')
,lpad(round((if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar) * 100),0),16,0)
,rpad(concat(lpad(liquidacion_id,5,0),lpad(id,10,0)),15,' ')
,rpad(replace(ltrim(rtrim(apenom)),',',' '),30,' ')
,rpad('',51,' ')
,'\r\n'
)
,importe_adebitar = if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar)
,banco_intercambio = vBancoId
,fecha_debito = vFechaDebito
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

-- GENERO EL ARCHIVO
set @fileName = concat('RIO_',date_format(vFechaDebito,'%Y%m%d'),'.txt');
SET @ENVIOID = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,TRUE);

select ifnull(cantidad_registros,0),importe_debito INTO REGISTROS,TOTAL_DEBITO
from liquidacion_socio_envios where id = @envioId;

set @header = concat(
'10'
,date_format(vFechaPresentacion,'%Y%m%d')
,lpad(REGISTROS,5,0)
,lpad(round((TOTAL_DEBITO * 100),0),19,0)
,'\r\n'
);

update liquidacion_socio_envios 
set lote = concat(@header,lote)
where id = @envioId;

commit;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_STANDARBANK`(
IN vBancoId varchar(5),
IN vLiqId INT(11),
IN vTurnos TEXT,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45),
in vUUID varchar(20),
in vConcepto varchar(20),
in vCuitEmpresa varchar(11),
in vImporteMaximoRegistro DECIMAL(10,2)
)
BEGIN

DECLARE REGISTROS INT(11);
DECLARE TOTAL_DEBITO DECIMAL(10,2);

start transaction;

update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

update liquidacion_socios
set intercambio = concat(
'2'
,'3700'
,lpad(cbu,22,0)
,concat(lpad(socio_id,12,0),lpad(liquidacion_id,8,0),lpad(registro,2,0))
,lpad(round((if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar) * 100),0),10,0)
,lpad(id,15,0)
,date_format(vFechaDebito,'%Y%m%d')
,rpad('',18,' ')
,'\r\n'
)
,importe_adebitar = if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar)
,banco_intercambio = vBancoId
,fecha_debito = vFechaDebito
where liquidacion_id = vLiqId
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

set @fileName = concat('STBANK_',date_format(vFechaDebito,'%Y%m%d'),'.txt');
SET @ENVIOID = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,TRUE);

select ifnull(cantidad_registros,0),importe_debito INTO REGISTROS,TOTAL_DEBITO
from liquidacion_socio_envios where id = @envioId;

set @header = concat(
'1'
,lpad(ifnull(vCuitEmpresa,0),11,0)
,'0'
,rpad(ifnull(vConcepto,'CUOTA PTMO'),10,' ')
,date_format(vFechaDebito,'%Y%m%d')
,rpad('',69,' ')
,'\r\n'
);

set @trailer=concat(
'9'
,lpad(REGISTROS,8,0)
,lpad(round((TOTAL_DEBITO * 100),0),14,0)
,rpad('',77,' ')
,'\r\n');

update liquidacion_socio_envios 
set lote = concat(@header,lote,@trailer)
where id = @envioId;



COMMIT;

END$$
DELIMITER ;

