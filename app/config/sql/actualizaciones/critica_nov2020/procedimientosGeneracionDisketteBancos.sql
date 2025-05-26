DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_COMAFI`;
DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_CORDOBA`;
DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_CREDICOOP`;
DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_GALICIA`;
DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_ITAU`;
DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_MACRO`;
DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_NACION`;
DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_SANTANDER`;
DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_STANDARBANK`;
DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_COMERCIO`;


DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_COMAFI`(
IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vLiqId INT(11),
IN vEmpresas TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vTurnos TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vUUID varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vEmpresaCodigo varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vEmpresaCuit varchar(21) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vEmpresaPrestacion varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vEmpresaNombre varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vEmpresaCtaBco varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vImporteMaximoRegistro DECIMAL(10,2)
)
BEGIN

select codigo_organismo into @ORGANISMO from liquidaciones where id = vLiqId;
select decimal_2 into vImporteMaximoRegistro from global_datos where id = @ORGANISMO;

-- inicializo
update liquidacion_socios set error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
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
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;


-- GENERO EL ARCHIVO
set @fileName = concat('EB',vEmpresaCodigo,'.',date_format(vFechaPresentacion,'%Y%m%d'));
SET @ENVIOID = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vEmpresas,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,TRUE);


END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_CORDOBA`(
IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vLiqId INT(11),
IN vEmpresas TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vTurnos TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vUUID varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vNroConvenio varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vImporteMaximoRegistro DECIMAL(10,2)
)
BEGIN

select codigo_organismo into @ORGANISMO from liquidaciones where id = vLiqId;
select decimal_2 into vImporteMaximoRegistro from global_datos where id = @ORGANISMO;

-- inicializo
update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
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
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

set @fileName = concat('DEB',right(rpad(vNroConvenio,5,0),5),'.HAB');
SET @envioId = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vEmpresas,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,TRUE);


END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_CREDICOOP`(
IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vLiqId INT(11),
IN vEmpresas TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vTurnos TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vUUID varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vEmpresa varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vDescripcion varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vEmpresaCuit varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vImporteMaximoRegistro DECIMAL(10,2),
in vLongIdd int(11)
)
BEGIN

select codigo_organismo into @ORGANISMO from liquidaciones where id = vLiqId;
select decimal_2 into vImporteMaximoRegistro from global_datos where id = @ORGANISMO;


-- inicializo
update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
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
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

-- GENERO EL ARCHIVO
set @fileName = concat('MAIN',substr(vEmpresa,1,3),'_',date_format(vFechaDebito,'%Y%m%d'),'.txt');
SET @ENVIOID = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vEmpresas,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,TRUE);



END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_GALICIA`(
IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vLiqId INT(11),
IN vEmpresas TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vTurnos TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vUUID CHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vPrestacion char(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vImporteMaximoRegistro DECIMAL(10,2)
)
BEGIN

DECLARE REGISTROS INT(11);
DECLARE TOTAL_DEBITO DECIMAL(10,2);

select codigo_organismo into @ORGANISMO from liquidaciones where id = vLiqId;
select decimal_2 into vImporteMaximoRegistro from global_datos where id = @ORGANISMO;


-- inicializo
update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
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
,date_format(fecha_debito,'%Y%m%d')
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
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

-- GENERO EL ARCHIVO
set @fileName = concat('GALICIA_',date_format(vFechaDebito,'%Y%m%d'),'.txt');
SET @envioId = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vEmpresas,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,TRUE);

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

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_ITAU`(
IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci ,
IN vLiqId INT(11),
IN vEmpresas TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vTurnos TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vUUID varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vNroArchivo int,
in vCuitEmpresa varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vConvenio varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vFiller varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vImporteMaximoRegistro DECIMAL(10,2)
)
BEGIN

DECLARE REGISTROS INT(11);
DECLARE TOTAL_DEBITO DECIMAL(10,2);

select codigo_organismo into @ORGANISMO from liquidaciones where id = vLiqId;
select decimal_2 into vImporteMaximoRegistro from global_datos where id = @ORGANISMO;


update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

update liquidacion_socios
set intercambio = CONCAT(
'DAFC'
,lpad(concat(lpad(liquidacion_socios.socio_id,5,0),lpad(liquidacion_id,4,0),lpad(registro,2,0)),15,0)
,rpad('',7,' ')
,'000'
,rpad(concat(lpad(liquidacion_socios.socio_id,5,0),lpad(liquidacion_id,4,0),lpad(registro,2,0)),22,vFiller)
,rpad(substr(replace(CONVERT(CAST(CONVERT(apenom USING latin1) AS BINARY) USING utf8),',',' '),1,60),60,' ')
,'CL'
,lpad(cuit_cuil,11,0)
,rpad('',41,' ')
,rpad('',49,0)
,date_format(fecha_debito,'%Y%m%d')
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
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

set @fileName = concat('ITAU_',lpad(ifnull(vNroArchivo,1),2,0),'_',date_format(vFechaDebito,'%Y%m%d'),'.txt');
SET @envioId = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vEmpresas,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,TRUE);

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

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_MACRO`(
IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vLiqId INT(11),
IN vEmpresas TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vTurnos TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vUUID varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vNroArchivo int,
in vConvenio varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vImporteMaximoRegistro DECIMAL(10,2)
)
BEGIN

DECLARE REGISTROS INT(11);
DECLARE TOTAL_DEBITO DECIMAL(10,2);

select codigo_organismo into @ORGANISMO from liquidaciones where id = vLiqId;
select decimal_2 into vImporteMaximoRegistro from global_datos where id = @ORGANISMO;


update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
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
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

set @fileName = concat('D',lpad(vConvenio,5,0),lpad(ifnull(vNroArchivo,1),2,0),'.285');
SET @envioId = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vEmpresas,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,TRUE);

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


END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_NACION`(
IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vLiqId INT(11),
IN vEmpresas TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vTurnos TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vUUID varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vNroArchivo int,
in vSucursal varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vTipoCuenta varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vNroCuenta varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vMoneda varchar(1) CHARACTER SET utf8 COLLATE utf8_general_ci,
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

select codigo_organismo into @ORGANISMO from liquidaciones where id = vLiqId;
select decimal_2 into vImporteMaximoRegistro from global_datos where id = @ORGANISMO;


-- inicializo
update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
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
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

set @fileName = concat('NACION',date_format(vFechaDebito,'%Y%m%d'),'.txt');
SET @envioId = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vEmpresas,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,FALSE);

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



END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_SANTANDER`(
IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vLiqId INT(11),
IN vEmpresas TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vTurnos TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vUUID varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vDescripcion varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vLongPartida int(11),
in vImporteMaximoRegistro DECIMAL(10,2)
)
BEGIN

DECLARE REGISTROS INT(11);
DECLARE TOTAL_DEBITO DECIMAL(10,2);

select codigo_organismo into @ORGANISMO from liquidaciones where id = vLiqId;
select decimal_2 into vImporteMaximoRegistro from global_datos where id = @ORGANISMO;


update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
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
,rpad(replace(ltrim(rtrim(CONVERT(CAST(CONVERT(apenom USING latin1) AS BINARY) USING utf8))),',',' '),30,' ')
,rpad('',51,' ')
,'\r\n'
)
,importe_adebitar = if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar)
,banco_intercambio = vBancoId
,fecha_debito = vFechaDebito
where liquidacion_id = vLiqId
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

-- GENERO EL ARCHIVO
set @fileName = concat('RIO_',date_format(vFechaDebito,'%Y%m%d'),'.txt');
SET @ENVIOID = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vEmpresas,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,TRUE);

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


END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_STANDARBANK`(
IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vLiqId INT(11),
IN vEmpresas TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vTurnos TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vUUID varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vConcepto varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vCuitEmpresa varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vImporteMaximoRegistro DECIMAL(10,2)
)
BEGIN

DECLARE REGISTROS INT(11);
DECLARE TOTAL_DEBITO DECIMAL(10,2);


select codigo_organismo into @ORGANISMO from liquidaciones where id = vLiqId;
select decimal_2 into vImporteMaximoRegistro from global_datos where id = @ORGANISMO;

update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
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
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

set @fileName = concat('STBANK_',date_format(vFechaDebito,'%Y%m%d'),'.txt');
SET @ENVIOID = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vEmpresas,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,TRUE);

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



END$$
DELIMITER ;


DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_DISKETTE_BANCO_COMERCIO`(

IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vLiqId INT(11),
IN vEmpresas TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vTurnos TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
in vFechaDebito date,
in vFechaPresentacion date,
in vUsuario varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vUUID CHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vNroArchivo int(11),
in base_numerador INT(11)
)
BEGIN

DECLARE REGISTROS INT(11);
DECLARE TOTAL_DEBITO DECIMAL(10,2);
DECLARE vImporteMaximoRegistro DECIMAL(10,2);


select codigo_organismo into @ORGANISMO from liquidaciones where id = vLiqId;
select decimal_2 into vImporteMaximoRegistro from global_datos where id = @ORGANISMO;


-- inicializo
update liquidacion_socios set importe_adebitar = importe_dto,error_cbu = 0, error_intercambio = ''
where liquidacion_id = vLiqId
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

update liquidacion_socios
set intercambio = concat(
	vNroArchivo
    ,';'
    ,rpad(cbu,22,0)
    ,';'
    ,ifnull(base_numerador,0) + socio_id
    ,';'
    ,right(cast(rpad(registro,3,0) as unsigned) + liquidacion_id,3)
    ,';'
    ,date_format(vFechaDebito,'%d/%m/%Y')
    ,';'
    ,substr(rpad(concat('ID',concat(lpad(liquidacion_socios.socio_id,5,0),lpad(liquidacion_id,4,0),lpad(registro,2,0)),replace(CONVERT(CAST(CONVERT(apenom USING latin1) AS BINARY) USING utf8),',','')),30,' '),1,30)
    ,';'
    ,round((if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar) * 100),0)
    ,'\r\n'
)
,importe_adebitar = if(importe_adebitar > vImporteMaximoRegistro, vImporteMaximoRegistro, importe_adebitar)
,banco_intercambio = vBancoId
,fecha_debito = vFechaDebito
where liquidacion_id = vLiqId
and FIND_IN_SET (codigo_empresa,cast(vEmpresas as char))
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;


-- GENERO EL ARCHIVO
set @fileName = concat('CUOTAS_BCOMER_',lpad(ifnull(vNroArchivo,1),2,0),'_',date_format(vFechaPresentacion,'%Y%m%d'),'.csv');
SET @ENVIOID = FX_GENERAR_DISKETTE_BANCO(vBancoId,vLiqId,vEmpresas,vTurnos,vFechaDebito,vFechaPresentacion,vUsuario,vUUID,@fileName,TRUE);


END$$
DELIMITER ;


