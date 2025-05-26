DROP FUNCTION IF EXISTS `FX_GENERAR_DISKETTE_BANCO`;
DROP FUNCTION IF EXISTS `FX_VALIDA_CBU`;

DELIMITER $$
CREATE FUNCTION `FX_GENERAR_DISKETTE_BANCO`(
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


DELIMITER $$
CREATE FUNCTION `FX_VALIDA_CBU`(
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

DROP FUNCTION IF EXISTS `FX_CALCULA_ADICIONAL`;
DROP FUNCTION IF EXISTS `FX_CALCULA_CUOTA_SERVICIO`;
DROP FUNCTION IF EXISTS `FX_CALCULA_CUOTA_SOCIAL`;
DROP FUNCTION IF EXISTS `FX_CALCULAR_SALDO_CUOTA`;

DELIMITER $$
CREATE  FUNCTION `FX_CALCULA_ADICIONAL`() RETURNS int
BEGIN

RETURN 1;
END$$
DELIMITER ;

DELIMITER $$
CREATE  FUNCTION `FX_CALCULA_CUOTA_SERVICIO`(
vORDEN_ID INT(11),
vTIPO_PRODUCTO VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
vORGANISMO VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
vPERIODO VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci
) RETURNS decimal(10,2)
BEGIN
DECLARE vSOLICITUD INT(11);
DECLARE vPERIODO_HASTA VARCHAR(6);
DECLARE vTIPO_ORDEN VARCHAR(5);
DECLARE vTIPO_PRODUCTO VARCHAR(12);
DECLARE vTIPO_CUOTA VARCHAR(12);
DECLARE vBENEFICIO_ID INT(11);
DECLARE vPROVEEDOR_ID INT(11);
DECLARE vNRO_REFERENCIA_PROVEEDOR VARCHAR(10);
DECLARE vIMPORTE_ORDEN_CUOTA DECIMAL(10,2);
DECLARE vIMPORTE_CUOTA_SERVICIO DECIMAL(10,2);

SET vIMPORTE_CUOTA_SERVICIO = 0;

select concepto_2 into vTIPO_CUOTA from global_datos where id = vTIPO_PRODUCTO COLLATE utf8_general_ci;

select tipo_orden_dto,numero,importe_cuota,proveedor_id into vTIPO_ORDEN,vSOLICITUD,vIMPORTE_ORDEN_CUOTA,vPROVEEDOR_ID 
from orden_descuentos
where id = vORDEN_ID;

set vIMPORTE_CUOTA_SERVICIO = vIMPORTE_ORDEN_CUOTA;

-- BUSCO SI TIENE UN IMPORTE FIJO
select ifnull(MutualProducto.importe_fijo,0) INTO vIMPORTE_CUOTA_SERVICIO
from mutual_productos as MutualProducto 
where MutualProducto.tipo_producto = vTIPO_PRODUCTO COLLATE utf8_general_ci 
and MutualProducto.proveedor_id = vPROVEEDOR_ID 
AND importe_fijo <> 0;

set vIMPORTE_CUOTA_SERVICIO = if(ifnull(vIMPORTE_CUOTA_SERVICIO,0)=0,vIMPORTE_ORDEN_CUOTA,vIMPORTE_CUOTA_SERVICIO);

-- BUSCO EN LA ESTRUCTURA DE SERVICIOS
IF vTIPO_ORDEN = 'OSERV' THEN
	set vIMPORTE_CUOTA_SERVICIO = 0;
	select mutual_servicio_valores.id, mutual_servicio_valores.mutual_servicio_id, importe_titular, importe_adicional, 
	costo_titular, costo_adicional, periodo_vigencia, fecha_vigencia 
	into @servicio_id, @mutual_servicio_id, @importe_titular, @importe_adicional, 
	@costo_titular, @costo_adicional, @periodo_vigencia, @fecha_vigencia         
	from mutual_servicio_valores
	inner join mutual_servicio_solicitudes on (mutual_servicio_solicitudes.mutual_servicio_id = mutual_servicio_valores.mutual_servicio_id)
	where codigo_organismo = vORGANISMO 
	and periodo_vigencia <= vPERIODO
	and mutual_servicio_solicitudes.id = vSOLICITUD
	order by periodo_vigencia desc limit 1;  

	SET @IMPORTE_SERVICIO_MENSUAL = @importe_titular + ifnull((SELECT ROUND(COUNT(*) * @costo_adicional,2) 
	FROM mutual_servicio_solicitud_adicionales
	where mutual_servicio_solicitud_id = vSOLICITUD 
	and ifnull(periodo_hasta,'000000') <=  vPERIODO),0);    
    
    SET vIMPORTE_CUOTA_SERVICIO = round(@IMPORTE_SERVICIO_MENSUAL,2);
    
END IF;

-- NO TIENE AMBOS, MANDO EL VALOR DE LA CUOTA QUE FIGURA EN LA CABECERA DE LA ORDEN
SET vIMPORTE_CUOTA_SERVICIO = IF(vIMPORTE_CUOTA_SERVICIO = 0,vIMPORTE_ORDEN_CUOTA,vIMPORTE_CUOTA_SERVICIO);


RETURN vIMPORTE_CUOTA_SERVICIO;
END$$
DELIMITER ;

DELIMITER $$
CREATE  FUNCTION `FX_CALCULA_CUOTA_SOCIAL`(
vSOCIO_ID INT(11),
vPROVEEDOR_ID INT(11),
vTIPO_PRODUCTO VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
vORGANISMO VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
vPERIODO VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci,
vLIQUIDA_SOLO_DEUDA BOOLEAN,
vCODIGO_EMPRESA VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci
) RETURNS decimal(10,2)
BEGIN

DECLARE vIMPORTE_CUOTA_SOCIAL DECIMAL(10,2);

SET vIMPORTE_CUOTA_SOCIAL = 0;

-- SACAR EL VALOR DE LA GLOBAL PARA EL CODIGO DE ORGANISMO
select decimal_1 into vIMPORTE_CUOTA_SOCIAL from global_datos
where id = concat('MUTUCUOS',RIGHT(vORGANISMO,4));

SELECT ifnull(decimal_1,0) into @cuota_empresa from global_datos where id = vCODIGO_EMPRESA;
IF @cuota_empresa <> 0 THEN SET vIMPORTE_CUOTA_SOCIAL = @cuota_empresa; END IF;

IF substring(vORGANISMO,9,2) = '66' THEN
	SELECT importe_cuota_social into vIMPORTE_CUOTA_SOCIAL FROM socios WHERE id = vSOCIO_ID;
END IF;

SET @cuota_social_diferenciada = 0;
SELECT MutualProducto.cuota_social_diferenciada, COUNT(*) 
into @cuota_social_diferenciada,@cantidad
FROM orden_descuentos AS OrdenDescuento 
INNER JOIN mutual_productos AS MutualProducto ON
(
	MutualProducto.tipo_orden_dto = OrdenDescuento.tipo_orden_dto
	AND MutualProducto.tipo_producto = OrdenDescuento.tipo_producto
	AND MutualProducto.proveedor_id = OrdenDescuento.proveedor_id
)	
WHERE OrdenDescuento.socio_id = vSOCIO_ID
AND OrdenDescuento.activo = 1
AND OrdenDescuento.tipo_orden_dto <> 'CMUTU'
AND MutualProducto.cuota_social_diferenciada <> 0
AND OrdenDescuento.socio_id NOT IN
(SELECT socio_id FROM orden_descuentos WHERE tipo_orden_dto <> 'CMUTU'
AND proveedor_id <> OrdenDescuento.proveedor_id)
GROUP BY MutualProducto.cuota_social_diferenciada
ORDER BY MutualProducto.cuota_social_diferenciada DESC;

IF @cantidad = 1 THEN SET @cuota_social_diferenciada = 0;
END IF;

IF (SELECT COUNT(*) FROM orden_descuentos WHERE socio_id = vSOCIO_ID and tipo_orden_dto <> 'CMUTU' and permanente = 0) > 1 THEN SET @cuota_social_diferenciada = 0;
END IF;

IF @cuota_social_diferenciada = 0 THEN
	SELECT cuota_social_diferenciada INTO @cuota_social_diferenciada 
    FROM mutual_productos where tipo_producto = vTIPO_PRODUCTO
    and proveedor_id = vPROVEEDOR_ID ORDER BY cuota_social_diferenciada DESC LIMIT 1;
END IF;

IF @cuota_social_diferenciada <> 0 THEN
	SET vIMPORTE_CUOTA_SOCIAL = @cuota_social_diferenciada;
END IF;

IF vLIQUIDA_SOLO_DEUDA = TRUE AND
	(select 
	SUM(ABS(importe) - ifnull((select sum(ABS(importe))from orden_descuento_cobro_cuotas
	where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0))
	as deuda
	from 
	orden_descuento_cuotas
	where 
	socio_id = vSOCIO_ID and estado <> 'B' 
	AND periodo <= vPERIODO
	AND proveedor_id IN (SELECT id FROM proveedores WHERE genera_cuota_social = 1 AND id <> 18)
	group by socio_id) <= 0 THEN   
    SET vIMPORTE_CUOTA_SOCIAL = 0;
END IF;

SET vIMPORTE_CUOTA_SOCIAL = IFNULL(vIMPORTE_CUOTA_SOCIAL,0);

RETURN vIMPORTE_CUOTA_SOCIAL;
END$$
DELIMITER ;

DELIMITER $$
CREATE  FUNCTION `FX_CALCULAR_SALDO_CUOTA`(`vCUOTA_ID` INT(11), `vPREIMPUTADO` BOOLEAN) RETURNS decimal(10,2)
BEGIN
DECLARE vPAGOS_CUOTA DECIMAL(10,2);
DECLARE vPREIMP_CUOTA DECIMAL(10,2);
DECLARE vIMPORTE_CUOTA DECIMAL(10,2);
DECLARE vSALDO DECIMAL(10,2);

SET vPAGOS_CUOTA = 0;
SET vPREIMP_CUOTA = 0;
SET vIMPORTE_CUOTA = 0;
SET vSALDO = 0;

SET @START_AT = SYSDATE(6);

select importe into vIMPORTE_CUOTA from orden_descuento_cuotas where id = vCUOTA_ID;
select ifnull(sum(importe),0) into vPAGOS_CUOTA from orden_descuento_cobro_cuotas where orden_descuento_cuota_id = vCUOTA_ID; 

-- BUSCO SI TIENE PREIMPUTADO
IF vPREIMPUTADO = TRUE THEN
/*	select ifnull(sum(importe_debitado),0) into vPREIMP_CUOTA from liquidacion_cuota_saldos 
	where orden_descuento_cuota_id = vCUOTA_ID
	and ifnull(orden_descuento_cobro_id,0) = 0;
*/
	select importe_debitado into vPREIMP_CUOTA from tmp_preimputado
	where orden_descuento_cuota_id = vCUOTA_ID;
END IF;



SET vSALDO = round(vIMPORTE_CUOTA - (vPAGOS_CUOTA + vPREIMP_CUOTA),2);

/*
delete from tmp_liquida_log where cuota_id = vCUOTA_ID and preimputa = vPREIMPUTADO;

SET @END_AT = SYSDATE(6);
-- TIMESTAMPDIFF(MICROSECOND, now(3), updated_at) / 1000
SET @MILISEC = TIMESTAMPDIFF(MICROSECOND,@START_AT,@END_AT)/1000;
INSERT INTO tmp_liquida_log(cuota_id,start_at,end_at,ms,preimputa)
values(vCUOTA_ID,@START_AT,@END_AT,@MILISEC,vPREIMPUTADO);
*/

RETURN IF(vSALDO < 0,0,vSALDO);
END$$
DELIMITER ;


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
CREATE PROCEDURE `SP_DISKETTE_BANCO_COMAFI`(
IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vLiqId INT(11),
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


END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_DISKETTE_BANCO_CORDOBA`(
IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vLiqId INT(11),
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


END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_DISKETTE_BANCO_CREDICOOP`(
IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vLiqId INT(11),
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



END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_DISKETTE_BANCO_GALICIA`(
IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vLiqId INT(11),
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

END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_DISKETTE_BANCO_ITAU`(
IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci ,
IN vLiqId INT(11),
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
and FIND_IN_SET (turno_pago,cast(vTurnos as char))
and diskette = TRUE;

update liquidacion_socios
set intercambio = CONCAT(
'DAFC'
,lpad(concat(lpad(liquidacion_socios.socio_id,5,0),lpad(liquidacion_id,4,0),lpad(registro,2,0)),15,0)
,rpad('',7,' ')
,'000'
,rpad(concat(lpad(liquidacion_socios.socio_id,5,0),lpad(liquidacion_id,4,0),lpad(registro,2,0)),22,vFiller)
,rpad(substr(replace(apenom,',',' '),1,60),60,' ')
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

END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_DISKETTE_BANCO_MACRO`(
IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vLiqId INT(11),
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


END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_DISKETTE_BANCO_NACION`(
IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vLiqId INT(11),
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



END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_DISKETTE_BANCO_SANTANDER`(
IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vLiqId INT(11),
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


END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_DISKETTE_BANCO_STANDARBANK`(
IN vBancoId varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vLiqId INT(11),
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



END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS `SP_LIQUIDA_ADICIONALES`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_CUOTA_SERVICIOS`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_CUOTA_SOCIAL`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_ANSES_GENERAL`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_ACUERDO_DEBITO`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_MORA_GENERAL`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_MORA_SPARAM`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_PERIODO_CONSOLIDA_MORA`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_PERIODO_DISCRI_PERM`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_PERIODO_GENERAL`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CJPC_GENERAL`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_SCORING`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_SOCIOS_SCORING`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_TOTALIZADOR`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_PUNITORIOS`;

DELIMITER $$
CREATE PROCEDURE `SP_LIQUIDA_ADICIONALES`(
vLIQUIDACION_ID INT(11),
IN vSOCIO_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
DECLARE vPROVEEDOR_ID INT(11) DEFAULT 0;
DECLARE vIMPUTAR_PROVEEDOR_ID INT(11) DEFAULT 0;
DECLARE vTIPO CHAR(1);
DECLARE vVALOR DECIMAL(10,2);
DECLARE vDEVENGA BOOLEAN;
DECLARE vCALCULO INT(11);
DECLARE vTIPO_CUOTA VARCHAR(12);
DECLARE vACTIVO BOOLEAN;

DECLARE c_adicionales CURSOR FOR 
select proveedor_id,imputar_proveedor_id,
tipo,valor,devengado_previo,deuda_calcula,tipo_cuota,activo
from mutual_adicionales
where codigo_organismo = @ORGANISMO and valor > 0
AND deuda_calcula <> 5
and ifnull(periodo_desde,'000000') <= @PERIODO
and ifnull(periodo_hasta,'999912') >= @PERIODO;

DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;

select codigo_organismo,periodo into @ORGANISMO,@PERIODO from liquidaciones where id = vLIQUIDACION_ID;

SET GLOBAL tmp_table_size = 1024 * 1024 * 64;
SET GLOBAL max_heap_table_size = 1024 * 1024 * 64;

OPEN c_adicionales;

c1_loop: LOOP
FETCH c_adicionales INTO vPROVEEDOR_ID,vIMPUTAR_PROVEEDOR_ID,vTIPO,vVALOR,vDEVENGA,vCALCULO,vTIPO_CUOTA,vACTIVO;
		
	IF (l_last_row = 1) THEN
		LEAVE c1_loop; 
	END IF;	
    
    -- SET vPROVEEDOR_ID = IFNULL(vPROVEEDOR_ID,18);
    SET vIMPUTAR_PROVEEDOR_ID = IFNULL(vIMPUTAR_PROVEEDOR_ID,18);
	-- select vPROVEEDOR_ID,vIMPUTAR_PROVEEDOR_ID,vTIPO,vVALOR,vDEVENGA,vCALCULO,vTIPO_CUOTA,vACTIVO;	
    
	IF vCALCULO = 1 THEN
		DROP TEMPORARY TABLE IF EXISTS tmp_adicionales_calculo;
		CREATE TEMPORARY TABLE IF NOT EXISTS tmp_adicionales_calculo as        
		SELECT vLIQUIDACION_ID liquidacion_id,@ORGANISMO organismo,vIMPUTAR_PROVEEDOR_ID proveedor_id,
		vTIPO tipo,vCALCULO calculo,vVALOR valor,vTIPO_CUOTA tipo_cuota,@PERIODO periodo,socio_id,SUM(saldo_actual) as saldo_actual,IFNULL(IF(vTIPO = 'P',ROUND(SUM(saldo_actual) * vVALOR / 100,2),vVALOR),0) AS adicional 
		,persona_beneficio_id
        from liquidacion_cuotas             
		where liquidacion_id = vLIQUIDACION_ID
        AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID)
		and ifnull(mutual_adicional_pendiente_id,0) = 0 
        AND IF(vPROVEEDOR_ID IS NULL,TRUE,proveedor_id = vPROVEEDOR_ID)
		group by liquidacion_id,socio_id having adicional > 0;

	END IF;

	IF vCALCULO = 2 THEN
		DROP TEMPORARY TABLE IF EXISTS tmp_adicionales_calculo;
		CREATE TEMPORARY TABLE IF NOT EXISTS tmp_adicionales_calculo as        
		SELECT vLIQUIDACION_ID liquidacion_id,@ORGANISMO organismo,vIMPUTAR_PROVEEDOR_ID proveedor_id,
		vTIPO tipo,vCALCULO calculo,vVALOR valor,vTIPO_CUOTA tipo_cuota,@PERIODO periodo,socio_id,SUM(saldo_actual) as saldo_actual,IFNULL(IF(vTIPO = 'P',ROUND(SUM(saldo_actual) * vVALOR / 100,2),vVALOR),0) AS adicional 
        ,persona_beneficio_id
		from liquidacion_cuotas             
		where liquidacion_cuotas.liquidacion_id = vLIQUIDACION_ID
		and periodo_cuota < @PERIODO
        AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID)
		and ifnull(mutual_adicional_pendiente_id,0) = 0 
        AND IF(vPROVEEDOR_ID IS NULL,TRUE,proveedor_id = vPROVEEDOR_ID)
		group by liquidacion_id,socio_id having adicional > 0;

	END IF;
	IF vCALCULO = 3 THEN	
		DROP TEMPORARY TABLE IF EXISTS tmp_adicionales_calculo;
		CREATE TEMPORARY TABLE IF NOT EXISTS tmp_adicionales_calculo as        
		SELECT vLIQUIDACION_ID liquidacion_id,@ORGANISMO organismo,vIMPUTAR_PROVEEDOR_ID proveedor_id,
		vTIPO tipo,vCALCULO calculo,vVALOR valor,vTIPO_CUOTA tipo_cuota,@PERIODO periodo,socio_id
        ,SUM(saldo_actual) as saldo_actual,
        IFNULL(IF(vTIPO = 'P',ROUND(SUM(saldo_actual) * vVALOR / 100,2),vVALOR),0) AS adicional 
        ,persona_beneficio_id
		from liquidacion_cuotas             
		where liquidacion_cuotas.liquidacion_id = vLIQUIDACION_ID
		and periodo_cuota = @PERIODO
        AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID)
		and ifnull(mutual_adicional_pendiente_id,0) = 0 
        AND IF(vPROVEEDOR_ID IS NULL,TRUE,proveedor_id = vPROVEEDOR_ID)
		group by liquidacion_id,socio_id having adicional > 0;	

	END IF;
    -- start transaction;
	insert into mutual_adicional_pendientes(liquidacion_id,socio_id,codigo_organismo,proveedor_id,
			tipo,deuda_calcula,valor,tipo_cuota,periodo,total_deuda,importe,
			orden_descuento_id,persona_beneficio_id)      
	select liquidacion_id,socio_id,organismo,proveedor_id,
	tipo,calculo,valor,tipo_cuota,periodo,saldo_actual,adicional 
	,NULL,persona_beneficio_id from tmp_adicionales_calculo where IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID);               
 
	
 
	insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,orden_descuento_id,
						orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,tipo_cuota,
						periodo_cuota,proveedor_id,vencida,importe,saldo_actual,codigo_organismo,
                        mutual_adicional_pendiente_id
				)
	SELECT a.liquidacion_id,a.socio_id,a.persona_beneficio_id,o.id,NULL,o.tipo_orden_dto,
    o.tipo_producto,a.tipo_cuota,a.periodo,a.proveedor_id,0,a.importe,a.importe,
    a.codigo_organismo,a.id
	FROM mutual_adicional_pendientes a
	left join orden_descuentos o on o.socio_id = a.socio_id and o.proveedor_id = ifnull(a.proveedor_id,18)
	and ifnull(o.nueva_orden_descuento_id,0) = 0 
	and o.tipo_orden_dto = 'CMUTU'
	where liquidacion_id = vLIQUIDACION_ID AND IF(vSOCIO_ID IS NULL,TRUE,a.socio_id = vSOCIO_ID) group by a.id;     
 
	-- actualizo la liquidacion cuotas la orden del adicional
    
    update mutual_adicional_pendientes a, liquidacion_cuotas l 
    set a.orden_descuento_id = l.orden_descuento_id
    where l.liquidacion_id = vLIQUIDACION_ID 
    AND IF(vSOCIO_ID IS NULL,TRUE,a.socio_id = vSOCIO_ID)
    and a.id = l.mutual_adicional_pendiente_id
    and ifnull(l.mutual_adicional_pendiente_id,0) <> 0;
	
	-- commit;    
    
    DROP TEMPORARY TABLE IF EXISTS tmp_adicionales_calculo;
    
END LOOP c1_loop;

CLOSE c_adicionales;

END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_LIQUIDA_CUOTA_SERVICIOS`(
vPERIODO VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci,
vORGANISMO VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci
)
BEGIN


/*INSERT INTO orden_descuento_cuotas(orden_descuento_id, 
			socio_id, persona_beneficio_id, tipo_orden_dto, 
			tipo_producto, tipo_cuota, periodo, estado, situacion, 
			vencimiento, vencimiento_proveedor, 
			nro_cuota, proveedor_id, 
			nro_referencia_proveedor,importe) */
            
DROP TEMPORARY TABLE IF EXISTS tmp_servicio_cuotas;            
CREATE TEMPORARY TABLE IF NOT EXISTS tmp_servicio_cuotas as
SELECT
	OrdenDescuento.id as orden_descuento_id,
    OrdenDescuento.socio_id,
    OrdenDescuento.persona_beneficio_id,
    OrdenDescuento.tipo_orden_dto,
    OrdenDescuento.tipo_producto,
    GlobalDato.concepto_2 as tipo_cuota,
    vPERIODO as periodo,
    'A' as estado,'MUTUSICUMUTU' as situacion,
    now() as vto_socio,now() as vto_proveedor,
    0 as nro_cuota,
    OrdenDescuento.proveedor_id,
	OrdenDescuento.nro_referencia_proveedor,
    FX_CALCULA_CUOTA_SERVICIO(OrdenDescuento.id,OrdenDescuento.tipo_producto,vORGANISMO,vPERIODO) as importe_calculado
   
FROM 
	orden_descuentos as OrdenDescuento, 
	socios as Socio,
	persona_beneficios as PersonaBeneficio,
    global_datos as GlobalDato
WHERE
	OrdenDescuento.socio_id = Socio.id 
	AND OrdenDescuento.tipo_orden_dto <> 'CMUTU'
	AND OrdenDescuento.tipo_producto <> 'MUTUPROD0003'
    AND OrdenDescuento.tipo_producto = GlobalDato.id
	AND OrdenDescuento.periodo_ini <= vPERIODO
	AND IF(Socio.activo = 0,IFNULL(OrdenDescuento.periodo_hasta,vPERIODO),IF(ISNULL(OrdenDescuento.periodo_hasta) AND OrdenDescuento.activo = 1,'999999',OrdenDescuento.periodo_hasta)) > vPERIODO
	AND OrdenDescuento.persona_beneficio_id = PersonaBeneficio.id
	AND PersonaBeneficio.codigo_beneficio = vORGANISMO
	AND OrdenDescuento.permanente = 1
    having importe_calculado > 0;


-- start transaction;

DROP TEMPORARY table if exists tmp_cuotas_servicios_actualiza;            
CREATE TEMPORARY TABLE IF NOT EXISTS tmp_cuotas_servicios_actualiza as
select ifnull(t2.id,0) as cuota_id,t1.* from tmp_servicio_cuotas t1
left join (select cu.id,cu.orden_descuento_id,cu.tipo_cuota from orden_descuento_cuotas cu
inner join persona_beneficios be on be.id = cu.persona_beneficio_id
where cu.periodo = vPERIODO and cu.estado <> 'B'
and be.codigo_beneficio = vORGANISMO group by orden_descuento_id) t2 on t1.orden_descuento_id = t2.orden_descuento_id
where ifnull(t2.id,0) <> 0 and t2.tipo_cuota = ifnull((SELECT concepto_2 
FROM global_datos gl
where gl.id = t1.tipo_producto),'MUTUTCUOCONS')
group by t1.orden_descuento_id;

update orden_descuento_cuotas cu, tmp_cuotas_servicios_actualiza t
set cu.importe = t.importe_calculado
where cu.id = t.cuota_id;

-- si no estan liquidadas las inserto
INSERT INTO orden_descuento_cuotas(orden_descuento_id, 
			socio_id, persona_beneficio_id, tipo_orden_dto, 
			tipo_producto, tipo_cuota, periodo, estado, situacion, 
			vencimiento, vencimiento_proveedor, 
			nro_cuota, proveedor_id, 
			nro_referencia_proveedor,importe)
select t1.* from tmp_servicio_cuotas t1
left join (select cu.id,cu.orden_descuento_id from orden_descuento_cuotas cu
inner join persona_beneficios be on be.id = cu.persona_beneficio_id
where cu.periodo = vPERIODO and cu.estado <> 'B'
and be.codigo_beneficio = vORGANISMO group by orden_descuento_id) t2 on t1.orden_descuento_id = t2.orden_descuento_id
where ifnull(t2.orden_descuento_id,0) = 0
group by t1.orden_descuento_id;

DROP TEMPORARY TABLE IF EXISTS tmp_servicio_cuotas; 
-- commit;

END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_LIQUIDA_CUOTA_SOCIAL`(
vPERIODO VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci,
vORGANISMO VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
vTIPO_PRODUCTO VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
vTIPO_CUOTA VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
vTIPO_ORDEN VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
vTIPO_SITUACION VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
vSOLO_DEUDA BOOLEAN
)
BEGIN


SET GLOBAL tmp_table_size = 1024 * 1024 * 64;
SET GLOBAL max_heap_table_size = 1024 * 1024 * 64;

drop temporary table if exists tmp_cuotas_sociales;            
CREATE TEMPORARY TABLE IF NOT EXISTS tmp_cuotas_sociales as
SELECT
	OrdenDescuento.id as orden_descuento_id,
    OrdenDescuento.socio_id,
    OrdenDescuento.persona_beneficio_id,
    OrdenDescuento.tipo_orden_dto,
    OrdenDescuento.tipo_producto,
    GlobalDato.concepto_2 as tipo_cuota,
    vPERIODO as periodo,
    'A' as estado,vTIPO_SITUACION as situacion,
    now() as vto_socio,now() as vto_proveedor,
    0 as nro_cuota,
    OrdenDescuento.proveedor_id,
	OrdenDescuento.nro_referencia_proveedor,
    FX_CALCULA_CUOTA_SOCIAL(OrdenDescuento.socio_id,OrdenDescuento.proveedor_id,OrdenDescuento.tipo_producto,vORGANISMO,vPERIODO,vSOLO_DEUDA,PersonaBeneficio.codigo_empresa) as importe_calculado
FROM 
	orden_descuentos as OrdenDescuento, 
	socios as Socio,
	persona_beneficios as PersonaBeneficio,
    global_datos as GlobalDato
WHERE
	OrdenDescuento.socio_id = Socio.id 
	AND OrdenDescuento.tipo_orden_dto = vTIPO_ORDEN
	AND OrdenDescuento.tipo_producto = vTIPO_PRODUCTO
    AND OrdenDescuento.tipo_producto = GlobalDato.id
	AND OrdenDescuento.periodo_ini <= vPERIODO
	AND IF(Socio.activo = 0,IFNULL(OrdenDescuento.periodo_hasta,vPERIODO),IF(ISNULL(OrdenDescuento.periodo_hasta) AND OrdenDescuento.activo = 1,'999999',OrdenDescuento.periodo_hasta)) > vPERIODO
	AND OrdenDescuento.persona_beneficio_id = PersonaBeneficio.id
	AND PersonaBeneficio.codigo_beneficio = vORGANISMO
	AND OrdenDescuento.permanente = 1
	AND OrdenDescuento.activo = 1
    group by OrdenDescuento.id having importe_calculado > 0;
-- select * from tmp_cuotas_sociales;
-- start transaction;

-- actualizo las existentes

drop temporary table if exists tmp_cuotas_sociales_actualiza;            
CREATE TEMPORARY TABLE IF NOT EXISTS tmp_cuotas_sociales_actualiza as
select ifnull(t2.id,0) as cuota_id,t1.* from tmp_cuotas_sociales t1
left join (select cu.id,cu.orden_descuento_id from orden_descuento_cuotas cu
inner join persona_beneficios be on be.id = cu.persona_beneficio_id
where cu.periodo = vPERIODO and cu.tipo_cuota = vTIPO_CUOTA and cu.estado <> 'B'
and be.codigo_beneficio = vORGANISMO group by orden_descuento_id) t2 on t1.orden_descuento_id = t2.orden_descuento_id
where ifnull(t2.id,0) <> 0
group by t1.orden_descuento_id;

update orden_descuento_cuotas cu, tmp_cuotas_sociales_actualiza t
set cu.importe = t.importe_calculado
where cu.id = t.cuota_id;


-- inserto los nuevos
INSERT INTO orden_descuento_cuotas(orden_descuento_id, 
			socio_id, persona_beneficio_id, tipo_orden_dto, 
			tipo_producto, tipo_cuota, periodo, estado, situacion, 
			vencimiento, vencimiento_proveedor, 
			nro_cuota, proveedor_id, 
			nro_referencia_proveedor,importe)    
select t1.* from tmp_cuotas_sociales t1
left join (select cu.id,cu.orden_descuento_id from orden_descuento_cuotas cu
inner join persona_beneficios be on be.id = cu.persona_beneficio_id
where cu.periodo = vPERIODO and cu.tipo_cuota = vTIPO_CUOTA and cu.estado <> 'B'
and be.codigo_beneficio = vORGANISMO group by orden_descuento_id) t2 on t1.orden_descuento_id = t2.orden_descuento_id
where ifnull(t2.orden_descuento_id,0) = 0
group by t1.orden_descuento_id;   
drop temporary table if exists tmp_cuotas_sociales;   
-- commit;

END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_LIQUIDA_DEUDA`(
IN vPERIODO VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci,
IN vORGANISMO VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_general_ci, 
IN vTIPO_LIQUIDACION INT(11), 
IN vPREIMPUTACION BOOLEAN,
IN vSOCIO_ID INT(11)
)
SP_LIQUIDA_DEUDA_LABEL:BEGIN

DECLARE vLIQUIDACION_ID INT(11);

DECLARE BLOQUEADA_EXCEPTION CONDITION FOR SQLSTATE '45000';

SET tmp_table_size = 1024 * 1024 * 512;
SET max_heap_table_size = 1024 * 1024 * 512;
SET innodb_lock_wait_timeout=1000;

select id,bloqueada,en_proceso into vLIQUIDACION_ID,@BLOQUEADA,@ENPROCESO from liquidaciones where periodo = vPERIODO and codigo_organismo = vORGANISMO;
IF IFNULL(vLIQUIDACION_ID,0) = 0 THEN
	INSERT INTO liquidaciones(periodo,codigo_organismo) values (vPERIODO,vORGANISMO);
    SET vLIQUIDACION_ID = last_insert_id();
ELSEIF (@BLOQUEADA = 1 OR @ENPROCESO = 1) THEN
    ROLLBACK;
    SIGNAL BLOQUEADA_EXCEPTION
    SET MESSAGE_TEXT = 'LIQUIDACION BLOQUEADA / EN PROCESO';    
    LEAVE SP_LIQUIDA_DEUDA_LABEL;
END IF;

-- //////////////////////////////////////////////////////////////////////
-- BORRO LO QUE ESTA
-- //////////////////////////////////////////////////////////////////////
SET FOREIGN_KEY_CHECKS = 0;
DELETE cu.* FROM orden_descuento_cuotas cu
        inner join liquidacion_cuotas lc on (lc.liquidacion_id = vLIQUIDACION_ID 
        and lc.orden_descuento_cuota_id = cu.id)
        where IF(vSOCIO_ID IS NULL,TRUE,lc.socio_id = vSOCIO_ID) AND ifnull(lc.mutual_adicional_pendiente_id,0) <> 0;
delete from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID);
delete from mutual_adicional_pendientes where liquidacion_id = vLIQUIDACION_ID AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID);
delete from liquidacion_socios where liquidacion_id = vLIQUIDACION_ID AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID);
delete from liquidacion_cuotas where liquidacion_id = vLIQUIDACION_ID AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID);
SET FOREIGN_KEY_CHECKS = 1;


-- TODO OK ARRANCO BLOQUEANDO LA LIQUIDACION
IF vSOCIO_ID IS NULL THEN
	update liquidaciones set bloqueada = true,cerrada = true,en_proceso = true where id = vLIQUIDACION_ID;
END IF;

SET vTIPO_LIQUIDACION = IFNULL(vTIPO_LIQUIDACION,0);
SET vTIPO_LIQUIDACION = IF(vTIPO_LIQUIDACION > 2,0,vTIPO_LIQUIDACION);

-- //////////////////////////////////////////////////////////////////////
-- SACO LAS CUOTAS ADEUDADAS
-- //////////////////////////////////////////////////////////////////////
select group_concat(id separator ',') into @calificaciones from global_datos where id like 'MUTUCALI%' and id <> 'MUTUCALI'
and logico_2 = 0;

IF vPREIMPUTACION THEN
	DROP TEMPORARY TABLE IF EXISTS tmp_preimputado;
	CREATE TEMPORARY TABLE IF NOT EXISTS tmp_preimputado (INDEX IDX_1(orden_descuento_cuota_id)) as 
	select lc1.orden_descuento_cuota_id,sum(lc1.importe_debitado) as importe_debitado from liquidacion_cuotas lc1
	inner join liquidaciones l on l.periodo < vPERIODO and l.id = lc1.liquidacion_id and l.imputada = 0
	inner join liquidacion_cuotas lc2 on lc2.id = lc1.id and ifnull(lc1.orden_descuento_cobro_id,0) = 0
	where 
    IF(vSOCIO_ID IS NULL,TRUE,lc1.socio_id = vSOCIO_ID)
    AND ifnull(lc1.importe_debitado,0) > 0 group by lc1.orden_descuento_cuota_id;
END IF;

DROP TEMPORARY TABLE IF EXISTS tmp_cuotas;            
CREATE TEMPORARY TABLE IF NOT EXISTS tmp_cuotas as  
select 
vLIQUIDACION_ID as liquidacion_id,cu.socio_id,cu.persona_beneficio_id,
cu.orden_descuento_id,cu.id as orden_descuento_cuota_id,cu.tipo_orden_dto,cu.tipo_producto,
cu.tipo_cuota,cu.periodo as periodo_cuota,cu.proveedor_id,if(cu.periodo < vPERIODO,1,0) as vencida,cu.importe,vORGANISMO as codigo_organismo
from orden_descuento_cuotas cu
inner join persona_beneficios be on be.id = cu.persona_beneficio_id
inner join socios so on so.id = cu.socio_id
where 
be.codigo_beneficio = vORGANISMO
AND IF(vSOCIO_ID IS NULL,TRUE,cu.socio_id = vSOCIO_ID)
and cu.estado = 'A'
and cu.situacion = 'MUTUSICUMUTU'
AND CASE vTIPO_LIQUIDACION 
	WHEN 0 THEN periodo <= vPERIODO 
	WHEN 1 THEN periodo = vPERIODO 
	WHEN 2 THEN periodo < vPERIODO
ELSE FALSE END
and find_in_set(so.calificacion,@calificaciones);

-- AND cu.importe > FX_CALCULAR_SALDO_CUOTA(cu.id,vPREIMPUTACION);  

insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,
orden_descuento_id,orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,
tipo_cuota,periodo_cuota,proveedor_id,vencida,importe,
codigo_organismo,saldo_actual)  
select liquidacion_id,socio_id,persona_beneficio_id,
orden_descuento_id,orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,
tipo_cuota,periodo_cuota,proveedor_id,vencida,importe,
codigo_organismo
,FX_CALCULAR_SALDO_CUOTA(t.orden_descuento_cuota_id,vPREIMPUTACION) as saldo_actual
from tmp_cuotas t having saldo_actual > 0;

DROP TEMPORARY TABLE IF EXISTS tmp_cuotas;    
DROP TEMPORARY TABLE IF EXISTS tmp_preimputado;

-- commit;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_LIQUIDA_DEUDA_ANSES_GENERAL`(
IN vLIQUIDACION_ID INT(11),
IN vSOCIO_ID INT(11)
)
BEGIN
/*
	var $ANSES_COD_CONS = 397109;
	var $ANSES_COD_CSOC = 324109;
	var $ANSES_SCOD_CONS = 0;
	var $ANSES_SCOD_CSOC = 0;	
*/
select codigo_organismo,periodo into @ORGANISMO,@PERIODO from liquidaciones where id = vLIQUIDACION_ID;
select entero_1,entero_2 into @ANSES_COD_CSOC,@ANSES_COD_CONS from global_datos where id = @ORGANISMO;

insert into liquidacion_socios(liquidacion_id,socio_id,
persona_beneficio_id,nro_beneficio,codigo_organismo,tipo_documento,documento,apenom,persona_id,cuit_cuil,
importe_dto,importe_adebitar,codigo_dto,sub_codigo,periodo,registro)
SELECT  
	LiquidacionCuota.liquidacion_id,
	LiquidacionCuota.socio_id,
	LiquidacionCuota.persona_beneficio_id,
    PersonaBeneficio.nro_beneficio,
    PersonaBeneficio.codigo_beneficio,
    Persona.tipo_documento,
    Persona.documento,
    concat(ifnull(Persona.apellido,'S/D'),', ',ifnull(Persona.nombre,'S/D')),
    Persona.id,
    ifnull(Persona.cuit_cuil,''),
	sum(saldo_actual) as deuda,
	sum(saldo_actual) as importe_adebitar,
	@ANSES_COD_CSOC as codigo_dto,
	0 as sub_codigo,
	0 as periodo,
	LiquidacionCuota.registro
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id)
    INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
	AND SUBSTRING(codigo_organismo,9,2) = 66
	AND IF(vSOCIO_ID IS NULL,TRUE,LiquidacionCuota.socio_id = vSOCIO_ID)
	AND LiquidacionCuota.tipo_cuota = 'MUTUTCUOCSOC'
GROUP BY
	LiquidacionCuota.codigo_organismo,
	LiquidacionCuota.socio_id,
	PersonaBeneficio.nro_beneficio
UNION
SELECT  
	LiquidacionCuota.liquidacion_id,
	LiquidacionCuota.socio_id,
	LiquidacionCuota.persona_beneficio_id,
    PersonaBeneficio.nro_beneficio,
    PersonaBeneficio.codigo_beneficio,
    Persona.tipo_documento,
    Persona.documento,
    concat(ifnull(Persona.apellido,'S/D'),', ',ifnull(Persona.nombre,'S/D')),
    Persona.id,
    ifnull(Persona.cuit_cuil,''),
	sum(saldo_actual) as deuda,
	sum(saldo_actual) as importe_adebitar,
	@ANSES_COD_CONS as codigo_dto,
	0 as sub_codigo,
	0 as periodo,
	LiquidacionCuota.registro
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id) 
    INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
	AND SUBSTRING(codigo_organismo,9,2) = 66
	AND IF(vSOCIO_ID IS NULL,TRUE,LiquidacionCuota.socio_id = vSOCIO_ID) 
	AND LiquidacionCuota.tipo_cuota <> 'MUTUTCUOCSOC'
GROUP BY
	LiquidacionCuota.codigo_organismo,
	LiquidacionCuota.socio_id,
	PersonaBeneficio.nro_beneficio;
 
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_LIQUIDA_DEUDA_CBU_ACUERDO_DEBITO`(
vLIQUIDACION_ID INT(11),
IN vSOCIO_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
DECLARE vMONTO_MAX_DTO_BENEFICIO DECIMAL(10,2) DEFAULT 0;
DECLARE vMONTO_ACUERDO DECIMAL(10,2) DEFAULT 0;
DECLARE vBENEFICIO_ID INT(11) DEFAULT 0;
DECLARE vSALDO_ACTUAL DECIMAL(10,2);
DECLARE vSALDO DECIMAL(10,2);
DECLARE vSALDO_ACUMULADO DECIMAL(10,2);

DECLARE vBENEFICIOID INT(11);
DECLARE vBANCOID VARCHAR(5);
DECLARE vSUCURSAL VARCHAR(5);
DECLARE vTIPOCTABCO VARCHAR(5);
DECLARE vNROCTABCO VARCHAR(20);
DECLARE vCBU VARCHAR(23);
DECLARE vTDOC VARCHAR(12);
DECLARE vNDOC VARCHAR(8);
DECLARE vAPENOM VARCHAR(40);
DECLARE vPERSONAID INT(11);
DECLARE vCUITCUIL VARCHAR(11);
DECLARE vCODEMPRE VARCHAR(12);
DECLARE vCODREPA VARCHAR(20);
DECLARE vTURNOPAGO VARCHAR(12);

DECLARE c_beneficios_acuerdo CURSOR FOR 
SELECT 
LiquidacionCuota.socio_id,
	LiquidacionCuota.persona_beneficio_id,
	PersonaBeneficio.banco_id,
	PersonaBeneficio.nro_sucursal,
	PersonaBeneficio.tipo_cta_bco,
	PersonaBeneficio.nro_cta_bco,
	PersonaBeneficio.cbu,
	Persona.tipo_documento,
	Persona.documento,
	concat(Persona.apellido,', ',Persona.nombre),
	Persona.id,
	Persona.cuit_cuil,
	PersonaBeneficio.codigo_empresa,
	PersonaBeneficio.codigo_reparticion,
	PersonaBeneficio.turno_pago,
IFNULL(PersonaBeneficio.acuerdo_debito,0),
IFNULL(PersonaBeneficio.importe_max_registro_cbu,0),
IFNULL(SUM(LiquidacionCuota.saldo_actual),0) saldo_actual
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (
    PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id)
    INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
    AND IF(vSOCIO_ID IS NULL,TRUE,LiquidacionCuota.socio_id = vSOCIO_ID)
	AND (IFNULL(PersonaBeneficio.importe_max_registro_cbu,0) <> 0 OR IFNULL(PersonaBeneficio.acuerdo_debito,0) <> 0)
    GROUP BY LiquidacionCuota.persona_beneficio_id;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row = 1;

-- RESET ACUERDOS EXISTENTES MAYORES AL SALDO ACTUAL LIQUIDADO
UPDATE (select lc.persona_beneficio_id,sum(saldo_actual) as saldo_actual from liquidacion_cuotas lc 
inner join persona_beneficios be on be.id = lc.persona_beneficio_id
where lc.liquidacion_id = vLIQUIDACION_ID
AND IF(vSOCIO_ID IS NULL,TRUE,lc.socio_id = vSOCIO_ID) and be.acuerdo_debito > 0 group by lc.persona_beneficio_id) t, persona_beneficios b 
SET b.acuerdo_debito = 0
where t.persona_beneficio_id = b.id and t.saldo_actual < b.acuerdo_debito;

SET vSALDO = 0;
SET vSALDO_ACUMULADO = 0;
SET @REGISTRO = 1;
SET l_last_row = 0;

SET @VALORESINSERT= '';

OPEN c_beneficios_acuerdo;
c1_loop_beneficios_acuerdo: LOOP
FETCH c_beneficios_acuerdo INTO vSOCIO_ID,vBENEFICIOID,vBANCOID,vSUCURSAL,vTIPOCTABCO,vNROCTABCO,vCBU,vTDOC,vNDOC,vAPENOM,vPERSONAID,vCUITCUIL,vCODEMPRE,vCODREPA,vTURNOPAGO,vMONTO_ACUERDO,vMONTO_MAX_DTO_BENEFICIO,vSALDO_ACTUAL;

        IF (l_last_row = 1) THEN
			LEAVE c1_loop_beneficios_acuerdo; 
		END IF;
        
		DELETE FROM liquidacion_socios where 
		liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID and
		persona_beneficio_id = vBENEFICIO_ID;          
        
        -- SELECT vSOCIO_ID,vBENEFICIO_ID,vMONTO_MAX_DTO_BENEFICIO,vSALDO_ACTUAL;
        SET vSALDO = IF(vMONTO_ACUERDO <> 0,vMONTO_ACUERDO,vSALDO_ACTUAL);
        
        IF vMONTO_MAX_DTO_BENEFICIO <> 0 THEN
			-- FRACCIONAR ACUERDO / SALDO EN VALORES SETEADOS PARA EL BENEFICIO
            SET @REG = CAST(vSALDO / vMONTO_MAX_DTO_BENEFICIO AS UNSIGNED);
			SET @N = 1;
			WHILE @N <= @REG DO
				SET @IMPO_DEBITO = vMONTO_MAX_DTO_BENEFICIO;
				SET vSALDO_ACUMULADO = vSALDO_ACUMULADO + @IMPO_DEBITO;
				IF @N = @REG THEN
					SET @IMPO_DEBITO = @IMPO_DEBITO + (vSALDO - vSALDO_ACUMULADO);
				END IF;
				SET @VALORESINSERT = CONCAT(@VALORESINSERT,IF(@N <> 1,',',''),'(',@REGISTRO,',',vLIQUIDACION_ID,',',vSOCIO_ID,',',vBENEFICIOID,',\'',@ORGANISMO,
				'\',\'',vBANCOID,'\',\'',vSUCURSAL,'\',\'',vTIPOCTABCO,'\',\'',vNROCTABCO,'\',\'',vCBU,'\',\''
				,vTDOC,'\',\'',vNDOC,'\',\'',vAPENOM,
				'\',',vPERSONAID,',\'',vCUITCUIL,'\',\'',vCODEMPRE,'\',\'',vCODREPA,'\',\'',vTURNOPAGO,'\',',0
				,',',@IMPO_DEBITO,',',@IMPO_DEBITO,',\'',concat('*** CON ACUERDO DE DEBITO ', vSALDO ,' | FRACCION = ',@IMPO_DEBITO,' ***'),'\')');                
				SET @N = @N + 1;
                
			END WHILE;
       ELSE 
		-- INSERTAR EL ACUERDO DIRECTAMENTE
		SET @VALORESINSERT = CONCAT(@VALORESINSERT,IF(@REGISTRO <> 1,',',''),'(',@REGISTRO,',',vLIQUIDACION_ID,',',vSOCIO_ID,',',vBENEFICIOID,',\'',@ORGANISMO,
		'\',\'',vBANCOID,'\',\'',vSUCURSAL,'\',\'',vTIPOCTABCO,'\',\'',vNROCTABCO,'\',\'',vCBU,'\',\''
		,vTDOC,'\',\'',vNDOC,'\',\'',vAPENOM,
		'\',',vPERSONAID,',\'',vCUITCUIL,'\',\'',vCODEMPRE,'\',\'',vCODREPA,'\',\'',vTURNOPAGO,'\',',0
				,',',vMONTO_ACUERDO,',',vMONTO_ACUERDO,',\'',concat('*** CON ACUERDO DE DEBITO ', vMONTO_ACUERDO ,' ***'),'\')');                
       END IF;
       SET @REGISTRO = @REGISTRO + 1;
END LOOP c1_loop_beneficios_acuerdo;  
CLOSE c_beneficios_acuerdo; 

IF @VALORESINSERT <> '' THEN
	SET @INSERTLS = CONCAT('insert into liquidacion_socios(registro,liquidacion_id,socio_id,
				persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
				nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
				codigo_empresa,codigo_reparticion,turno_pago,periodo,importe_dto,importe_adebitar,
				formula_criterio_deuda) VALUES',@VALORESINSERT);

	PREPARE stmt1 FROM @INSERTLS;
	EXECUTE stmt1;
	DEALLOCATE PREPARE stmt1;
END IF;

END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_LIQUIDA_DEUDA_CBU_MORA_GENERAL`(
vLIQUIDACION_ID INT(11),
IN vSOCIO_ID INT(11)
)
BEGIN

DECLARE l_last_row INT DEFAULT 0;

DECLARE vID INT(11);
DECLARE vIMPORTE_DEBITO DECIMAL(10,2);
DECLARE vIMPORTE_DEBITO_CALCULADO DECIMAL(10,2);
DECLARE vSALDO DECIMAL(10,2);
DECLARE vSALDO_ACUMULADO DECIMAL(10,2);
DECLARE vTOPE_POR_REGISTRO DECIMAL(10,2);
DECLARE vFORMULA VARCHAR(255);
DECLARE vLIMITE_INFERIOR DECIMAL(10,2);
DECLARE vLIMITE_SUPERIOR DECIMAL(10,2);
DECLARE vNCUOTAS_LIMITE_INFERIOR INT(11);
DECLARE vNCUOTAS_LIMITE_PROMEDIO INT(11);
DECLARE vNCUOTAS_LIMITE_SUPERIOR INT(11);

DECLARE vBENEFICIOID INT(11);
DECLARE vBANCOID VARCHAR(5);
DECLARE vSUCURSAL VARCHAR(5);
DECLARE vTIPOCTABCO VARCHAR(5);
DECLARE vNROCTABCO VARCHAR(20);
DECLARE vCBU VARCHAR(23);
DECLARE vTDOC VARCHAR(12);
DECLARE vNDOC VARCHAR(8);
DECLARE vAPENOM VARCHAR(40);
DECLARE vPERSONAID INT(11);
DECLARE vCUITCUIL VARCHAR(11);
DECLARE vCODEMPRE VARCHAR(12);
DECLARE vCODREPA VARCHAR(20);
DECLARE vTURNOPAGO VARCHAR(12);
DECLARE vULTIMO_COBRO DECIMAL(10,2);
DECLARE vSALDO_ACTUAL DECIMAL(10,2);

DECLARE cursor_socio CURSOR FOR 
SELECT 	
	LiquidacionCuota.socio_id,
	LiquidacionCuota.persona_beneficio_id,
    ifnull(PersonaBeneficio.banco_id,''),
    ifnull(PersonaBeneficio.nro_sucursal,''),
    ifnull(PersonaBeneficio.tipo_cta_bco,''),
    ifnull(PersonaBeneficio.nro_cta_bco,''),
    ifnull(PersonaBeneficio.cbu,''),
    Persona.tipo_documento,
    Persona.documento,
    concat(ifnull(Persona.apellido,'S/D'),', ',ifnull(Persona.nombre,'S/D')),
    Persona.id,
    ifnull(Persona.cuit_cuil,''),
    ifnull(PersonaBeneficio.codigo_empresa,''),
    ifnull(PersonaBeneficio.codigo_reparticion,''),
    ifnull(PersonaBeneficio.turno_pago,''),
    (SELECT CASE 
		WHEN sum(saldo_actual) < vLIMITE_INFERIOR 
        THEN sum(saldo_actual) / vNCUOTAS_LIMITE_INFERIOR 
		WHEN sum(saldo_actual) between vLIMITE_INFERIOR AND vLIMITE_SUPERIOR 
		THEN sum(saldo_actual) / vNCUOTAS_LIMITE_PROMEDIO 
		WHEN sum(saldo_actual) > vLIMITE_SUPERIOR 
		THEN sum(saldo_actual) / vNCUOTAS_LIMITE_SUPERIOR 
	END) as importe_adebitar,
    (SELECT CASE 
		WHEN sum(saldo_actual) < vLIMITE_INFERIOR 
		THEN CONCAT(sum(saldo_actual),' <' , vLIMITE_INFERIOR,' ==> IMPORTE A DEBITAR: ',round(sum(saldo_actual) / vNCUOTAS_LIMITE_INFERIOR,2),' (TOTAL ATRASO / ',vNCUOTAS_LIMITE_INFERIOR,')')
        WHEN sum(saldo_actual) between vLIMITE_INFERIOR AND vLIMITE_SUPERIOR 
        THEN CONCAT(vLIMITE_INFERIOR,' < ',round(sum(saldo_actual),2),' <= ' , vLIMITE_SUPERIOR,' ==> IMPORTE A DEBITAR: ', round(sum(saldo_actual) / vNCUOTAS_LIMITE_PROMEDIO,2),' (TOTAL ATRASO / ', vNCUOTAS_LIMITE_PROMEDIO,')')
        WHEN sum(saldo_actual) > vLIMITE_SUPERIOR 
        THEN CONCAT(sum(saldo_actual),' > ' , vLIMITE_SUPERIOR,' ==> IMPORTE A DEBITAR: ',round(sum(saldo_actual) / vNCUOTAS_LIMITE_SUPERIOR,2),' (TOTAL ATRASO / ',vNCUOTAS_LIMITE_SUPERIOR,')') 
        END) as formula_criterio_deuda
        ,0 as ultimo_cobro
		,sum(saldo_actual) AS saldo_actual
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (
    PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id)
    INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
    AND IF(vSOCIO_ID IS NULL,TRUE,LiquidacionCuota.socio_id = vSOCIO_ID)
	AND LiquidacionCuota.periodo_cuota < @PERIODO
    AND PersonaBeneficio.acuerdo_debito = 0
    AND PersonaBeneficio.importe_max_registro_cbu = 0
GROUP BY
	LiquidacionCuota.socio_id,
	PersonaBeneficio.cbu,
	PersonaBeneficio.turno_pago    
HAVING importe_adebitar > vTOPE_POR_REGISTRO;


DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;

select codigo_organismo,periodo into @ORGANISMO,@PERIODO from liquidaciones where id = vLIQUIDACION_ID;

select entero_1,entero_2,decimal_1,entero_3,entero_4,entero_5 into vLIMITE_INFERIOR,vLIMITE_SUPERIOR,vTOPE_POR_REGISTRO,vNCUOTAS_LIMITE_INFERIOR,vNCUOTAS_LIMITE_PROMEDIO,vNCUOTAS_LIMITE_SUPERIOR from global_datos where id = @ORGANISMO;
SET vNCUOTAS_LIMITE_INFERIOR = IF(IFNULL(vNCUOTAS_LIMITE_INFERIOR,0) <> 0, vNCUOTAS_LIMITE_INFERIOR ,1);
SET vNCUOTAS_LIMITE_PROMEDIO = IF(IFNULL(vNCUOTAS_LIMITE_PROMEDIO,0) <> 0, vNCUOTAS_LIMITE_PROMEDIO ,2);
SET vNCUOTAS_LIMITE_SUPERIOR = IF(IFNULL(vNCUOTAS_LIMITE_SUPERIOR,0) <> 0, vNCUOTAS_LIMITE_SUPERIOR ,3);
SET vLIMITE_INFERIOR = IFNULL(vLIMITE_INFERIOR,2000);
SET vLIMITE_SUPERIOR = IFNULL(vLIMITE_SUPERIOR,5000);

-- select vNCUOTAS_LIMITE_INFERIOR,vNCUOTAS_LIMITE_PROMEDIO,vNCUOTAS_LIMITE_SUPERIOR;

-- //////////////////////////////////////////////////////////////////////
-- INSERTO LA MORA QUE ES MENOR AL TOPE POR REGISTRO
-- //////////////////////////////////////////////////////////////////////
insert into liquidacion_socios(liquidacion_id,socio_id,
persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
codigo_empresa,codigo_reparticion,turno_pago,periodo,importe_dto,importe_adebitar,
formula_criterio_deuda)
SELECT 
	LiquidacionCuota.liquidacion_id,
	LiquidacionCuota.socio_id,
	LiquidacionCuota.persona_beneficio_id,
    LiquidacionCuota.codigo_organismo,
    ifnull(PersonaBeneficio.banco_id,''),
    ifnull(PersonaBeneficio.nro_sucursal,''),
    ifnull(PersonaBeneficio.tipo_cta_bco,''),
    ifnull(PersonaBeneficio.nro_cta_bco,''),
    ifnull(PersonaBeneficio.cbu,''),
    Persona.tipo_documento,
    Persona.documento,
    concat(ifnull(Persona.apellido,'S/D'),', ',ifnull(Persona.nombre,'S/D')),
    Persona.id,
    ifnull(Persona.cuit_cuil,''),
    ifnull(PersonaBeneficio.codigo_empresa,''),
    ifnull(PersonaBeneficio.codigo_reparticion,''),
    ifnull(PersonaBeneficio.turno_pago,''),
    0,
	sum(saldo_actual) as deuda,
    (SELECT CASE 
		WHEN sum(saldo_actual) < vLIMITE_INFERIOR 
        THEN sum(saldo_actual) / vNCUOTAS_LIMITE_INFERIOR 
		WHEN sum(saldo_actual) between vLIMITE_INFERIOR AND vLIMITE_SUPERIOR 
		THEN sum(saldo_actual) / vNCUOTAS_LIMITE_PROMEDIO 
		WHEN sum(saldo_actual) > vLIMITE_SUPERIOR 
		THEN sum(saldo_actual) / vNCUOTAS_LIMITE_SUPERIOR 
	END) as importe_adebitar,
    (SELECT CASE 
		WHEN sum(saldo_actual) < vLIMITE_INFERIOR 
		THEN CONCAT(sum(saldo_actual),' <' , vLIMITE_INFERIOR,' ==> IMPORTE A DEBITAR: ',round(sum(saldo_actual) / vNCUOTAS_LIMITE_INFERIOR,2),' (TOTAL ATRASO / ',vNCUOTAS_LIMITE_INFERIOR,')')
        WHEN sum(saldo_actual) between vLIMITE_INFERIOR AND vLIMITE_SUPERIOR 
        THEN CONCAT(vLIMITE_INFERIOR,' < ',round(sum(saldo_actual),2),' <= ' , vLIMITE_SUPERIOR,' ==> IMPORTE A DEBITAR: ', round(sum(saldo_actual) / vNCUOTAS_LIMITE_PROMEDIO,2),' (TOTAL ATRASO / ', vNCUOTAS_LIMITE_PROMEDIO,')')
        WHEN sum(saldo_actual) > vLIMITE_SUPERIOR 
        THEN CONCAT(sum(saldo_actual),' > ' , vLIMITE_SUPERIOR,' ==> IMPORTE A DEBITAR: ',round(sum(saldo_actual) / vNCUOTAS_LIMITE_SUPERIOR,2),' (TOTAL ATRASO / ',vNCUOTAS_LIMITE_SUPERIOR,')') 
        END) as formula_criterio_deuda
    -- '*** IMPORTE MORA ***'
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (
    PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id)
    INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
    AND IF(vSOCIO_ID IS NULL,TRUE,LiquidacionCuota.socio_id = vSOCIO_ID)
	AND LiquidacionCuota.periodo_cuota < @PERIODO
    AND PersonaBeneficio.acuerdo_debito = 0
    AND PersonaBeneficio.importe_max_registro_cbu = 0
GROUP BY
	LiquidacionCuota.socio_id,
	PersonaBeneficio.cbu,
	PersonaBeneficio.turno_pago
HAVING importe_adebitar <= vTOPE_POR_REGISTRO; 

SET @VALORESINSERT= '';

OPEN cursor_socio;
c1_loop: LOOP

	-- FETCH cursor_socio INTO vID,vSOCIO_ID,vIMPORTE_DEBITO,vFORMULA;
    FETCH cursor_socio INTO vSOCIO_ID,vBENEFICIOID,vBANCOID,vSUCURSAL,vTIPOCTABCO,vNROCTABCO,vCBU,vTDOC,vNDOC,vAPENOM,vPERSONAID,vCUITCUIL,vCODEMPRE,vCODREPA,vTURNOPAGO,vIMPORTE_DEBITO,vFORMULA,vULTIMO_COBRO,vSALDO_ACTUAL;

	IF (l_last_row = 1) THEN
		LEAVE c1_loop; 
	END IF; 
    
    

	-- SI EL IMPORTE DEL DEBITO ES MENOR AL ULTIMO COBRO Y LA DEUDA ES MAYOR, MANDO EL IMPO DEL ULTIMO COBRO
    /*IF (vULTIMO_COBRO > vIMPORTE_DEBITO) AND (vSALDO_ACTUAL > vULTIMO_COBRO) THEN
		SET @VALORESINSERT = CONCAT(@VALORESINSERT,IF(@REGISTRO <> 1,',',''),'(',@REGISTRO,',',vLIQUIDACION_ID,',',vSOCIO_ID,',',vBENEFICIOID,',\'',@ORGANISMO,
        '\',\'',vBANCOID,'\',\'',vSUCURSAL,'\',\'',vTIPOCTABCO,'\',\'',vNROCTABCO,'\',\'',vCBU,'\',\''
        ,vTDOC,'\',\'',vNDOC,'\',\'',vAPENOM,
        '\',',vPERSONAID,',\'',vCUITCUIL,'\',\'',vCODEMPRE,'\',\'',vCODREPA,'\',\'',vTURNOPAGO,'\',',0
        ,',',vULTIMO_COBRO,',',vULTIMO_COBRO,',\'',concat(vFORMULA,'\n*** IMPORTE ULTIMO DEBITO ',vULTIMO_COBRO,' ***'),'\')');      
		LEAVE c1_loop; 
    END IF;*/


    SET @N = IFNULL(vIMPORTE_DEBITO DIV vTOPE_POR_REGISTRO,0);
    
    
    SET vSALDO = vIMPORTE_DEBITO;
    SET @REGISTRO = 1;
        
    IF @N = 0 THEN
		LEAVE c1_loop;
    END IF;

	

    WHILE @REGISTRO <= @N DO

		SET @VALORESINSERT = CONCAT(IF(@VALORESINSERT = '','',CONCAT(@VALORESINSERT,',')),'(',@REGISTRO,',',vLIQUIDACION_ID,',',vSOCIO_ID,',',vBENEFICIOID,',\'',@ORGANISMO,
        '\',\'',vBANCOID,'\',\'',vSUCURSAL,'\',\'',vTIPOCTABCO,'\',\'',vNROCTABCO,'\',\'',vCBU,'\',\''
        ,vTDOC,'\',\'',vNDOC,'\',\'',vAPENOM,
        '\',',vPERSONAID,',\'',vCUITCUIL,'\',\'',vCODEMPRE,'\',\'',vCODREPA,'\',\'',vTURNOPAGO,'\',',0
        ,',',vTOPE_POR_REGISTRO,',',vTOPE_POR_REGISTRO,',\'',concat(vFORMULA,'\n*** MAXIMO POR REGISTRO ',vTOPE_POR_REGISTRO,' ***'),'\')');  

        SET vSALDO = vSALDO - vTOPE_POR_REGISTRO;
		SET @REGISTRO = @REGISTRO + 1;
        
    END WHILE;
	
    IF  vSALDO > 0 THEN

		SET @VALORESINSERT = CONCAT(IF(@VALORESINSERT = '','',CONCAT(@VALORESINSERT,',')),'(',@REGISTRO,',',vLIQUIDACION_ID,',',vSOCIO_ID,',',vBENEFICIOID,',\'',@ORGANISMO,
        '\',\'',vBANCOID,'\',\'',vSUCURSAL,'\',\'',vTIPOCTABCO,'\',\'',vNROCTABCO,'\',\'',vCBU,'\',\''
        ,vTDOC,'\',\'',vNDOC,'\',\'',vAPENOM,
        '\',',vPERSONAID,',\'',vCUITCUIL,'\',\'',vCODEMPRE,'\',\'',vCODREPA,'\',\'',vTURNOPAGO,'\',',0
        ,',',vSALDO,',',vSALDO,',\'',concat(vFORMULA,'\n*** MAXIMO POR REGISTRO ',vTOPE_POR_REGISTRO,' ***'),'\')');    
    
    END IF;
     
END LOOP c1_loop;    
CLOSE cursor_socio;

IF @VALORESINSERT <> '' THEN
	SET @INSERTLS = CONCAT('insert into liquidacion_socios(registro,liquidacion_id,socio_id,
				persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
				nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
				codigo_empresa,codigo_reparticion,turno_pago,periodo,importe_dto,importe_adebitar,
				formula_criterio_deuda) VALUES',@VALORESINSERT);

	PREPARE stmt1 FROM @INSERTLS;
	EXECUTE stmt1;
	DEALLOCATE PREPARE stmt1;
END IF;

END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_LIQUIDA_DEUDA_CBU_MORA_SPARAM`(
vLIQUIDACION_ID INT(11),
IN vSOCIO_ID INT(11)
)
BEGIN

select codigo_organismo,periodo into @ORGANISMO,@PERIODO from liquidaciones where id = vLIQUIDACION_ID;

insert into liquidacion_socios(liquidacion_id,socio_id,
persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
codigo_empresa,codigo_reparticion,turno_pago,periodo,importe_dto,importe_adebitar,
formula_criterio_deuda)
SELECT 
	LiquidacionCuota.liquidacion_id,
	LiquidacionCuota.socio_id,
	LiquidacionCuota.persona_beneficio_id,
    LiquidacionCuota.codigo_organismo,
    PersonaBeneficio.banco_id,
    PersonaBeneficio.nro_sucursal,
    PersonaBeneficio.tipo_cta_bco,
    PersonaBeneficio.nro_cta_bco,
    PersonaBeneficio.cbu,
    Persona.tipo_documento,
    Persona.documento,
    concat(Persona.apellido,', ',Persona.nombre),
    Persona.id,
    Persona.cuit_cuil,
    PersonaBeneficio.codigo_empresa,
    PersonaBeneficio.codigo_reparticion,
    PersonaBeneficio.turno_pago,
    0,
	sum(saldo_actual) as deuda,
    sum(saldo_actual) as importe_adebitar,
    '* IMPORTE MORA *' as formula_criterio_deuda
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (
    PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id
    and PersonaBeneficio.codigo_beneficio = LiquidacionCuota.codigo_organismo)
    INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
    AND IF(vSOCIO_ID IS NULL,TRUE,LiquidacionCuota.socio_id = vSOCIO_ID)
	AND LiquidacionCuota.periodo_cuota < @PERIODO
    AND PersonaBeneficio.acuerdo_debito = 0
    AND PersonaBeneficio.importe_max_registro_cbu = 0
GROUP BY
	LiquidacionCuota.socio_id,
	PersonaBeneficio.cbu,
	PersonaBeneficio.turno_pago;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_LIQUIDA_DEUDA_CBU_PERIODO_CONSOLIDA_MORA`(
vLIQUIDACION_ID INT(11),
IN vSOCIO_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
DECLARE vIMPORTE_DEBITO DECIMAL(10,2);
DECLARE vIMPORTE_DEBITO_CALCULADO DECIMAL(10,2);
DECLARE vSALDO DECIMAL(10,2);
DECLARE vSALDO_ACUMULADO DECIMAL(10,2);
DECLARE vTOPE_POR_REGISTRO DECIMAL(10,2);
DECLARE vFORMULA VARCHAR(255);

DECLARE vLIMITE_INFERIOR DECIMAL(10,2);
DECLARE vLIMITE_SUPERIOR DECIMAL(10,2);
DECLARE vNCUOTAS_LIMITE_INFERIOR INT(11);
DECLARE vNCUOTAS_LIMITE_PROMEDIO INT(11);
DECLARE vNCUOTAS_LIMITE_SUPERIOR INT(11);

DECLARE vBENEFICIOID INT(11);
DECLARE vBANCOID VARCHAR(5);
DECLARE vSUCURSAL VARCHAR(5);
DECLARE vTIPOCTABCO VARCHAR(5);
DECLARE vNROCTABCO VARCHAR(20);
DECLARE vCBU VARCHAR(23);
DECLARE vTDOC VARCHAR(12);
DECLARE vNDOC VARCHAR(8);
DECLARE vAPENOM VARCHAR(40);
DECLARE vPERSONAID INT(11);
DECLARE vCUITCUIL VARCHAR(11);
DECLARE vCODEMPRE VARCHAR(12);
DECLARE vCODREPA VARCHAR(20);
DECLARE vTURNOPAGO VARCHAR(12);
DECLARE vULTIMO_COBRO DECIMAL(10,2);
DECLARE vSALDO_ACTUAL DECIMAL(10,2);

DECLARE cursor_socio CURSOR FOR

-- //////////////////////////////////////////////////////////////////////
-- TOTAL DEL PERIODO + MORA FRACCIONADA POR REGISTROS 
-- //////////////////////////////////////////////////////////////////////
SELECT 
	LiquidacionCuota.socio_id,
	LiquidacionCuota.persona_beneficio_id,
    ifnull(PersonaBeneficio.banco_id,'') banco_id,
    ifnull(PersonaBeneficio.nro_sucursal,'') nro_sucursal,
    ifnull(PersonaBeneficio.tipo_cta_bco,'') tipo_cta_bco,
    ifnull(PersonaBeneficio.nro_cta_bco,'') nro_cta_bco,
    ifnull(PersonaBeneficio.cbu,'') cbu,
    Persona.tipo_documento,
    Persona.documento,
    concat(ifnull(Persona.apellido,'S/D'),', ',ifnull(Persona.nombre,'S/D')) AS apenom,
    Persona.id,
    ifnull(Persona.cuit_cuil,'') cuit_cuil,
    ifnull(PersonaBeneficio.codigo_empresa,'') codigo_empresa,
    ifnull(PersonaBeneficio.codigo_reparticion,'') codigo_reparticion,
    ifnull(PersonaBeneficio.turno_pago,'') turno_pago,
    (SELECT CASE 
		WHEN sum(saldo_actual) <= vLIMITE_INFERIOR 
        THEN sum(saldo_actual) / vNCUOTAS_LIMITE_INFERIOR 
		WHEN sum(saldo_actual) between vLIMITE_INFERIOR AND vLIMITE_SUPERIOR 
		THEN sum(saldo_actual) / vNCUOTAS_LIMITE_PROMEDIO 
		WHEN sum(saldo_actual) >= vLIMITE_SUPERIOR 
		THEN sum(saldo_actual) / vNCUOTAS_LIMITE_SUPERIOR 
	END) as importe_adebitar,
    (SELECT CASE 
		WHEN sum(saldo_actual) <= vLIMITE_INFERIOR 
		THEN CONCAT(sum(saldo_actual),' <=' , vLIMITE_INFERIOR,' ==> IMPORTE A DEBITAR: ',round(sum(saldo_actual) / vNCUOTAS_LIMITE_INFERIOR,2),' (TOTAL ATRASO / ',vNCUOTAS_LIMITE_INFERIOR,')')
        WHEN sum(saldo_actual) between vLIMITE_INFERIOR AND vLIMITE_SUPERIOR 
        THEN CONCAT(vLIMITE_INFERIOR,' < ',round(sum(saldo_actual),2),' <= ' , vLIMITE_SUPERIOR,' ==> IMPORTE A DEBITAR: ', round(sum(saldo_actual) / vNCUOTAS_LIMITE_PROMEDIO,2),' (TOTAL ATRASO / ', vNCUOTAS_LIMITE_PROMEDIO,')')
        WHEN sum(saldo_actual) >= vLIMITE_SUPERIOR 
        THEN CONCAT(sum(saldo_actual),' >= ' , vLIMITE_SUPERIOR,' ==> IMPORTE A DEBITAR: ',round(sum(saldo_actual) / vNCUOTAS_LIMITE_SUPERIOR,2),' (TOTAL ATRASO / ',vNCUOTAS_LIMITE_SUPERIOR,')') 
        END) as formula_criterio_deuda
        ,0 as ultimo_cobro
		,sum(saldo_actual) AS saldo_actual
    
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (
    PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id)
    INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
    AND IF(vSOCIO_ID IS NULL,TRUE,LiquidacionCuota.socio_id = vSOCIO_ID)	
    AND PersonaBeneficio.acuerdo_debito = 0 
    AND PersonaBeneficio.importe_max_registro_cbu = 0
GROUP BY
	LiquidacionCuota.socio_id,
	PersonaBeneficio.cbu,
	PersonaBeneficio.turno_pago
HAVING importe_adebitar > vTOPE_POR_REGISTRO;
    
DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;

select codigo_organismo,periodo into @ORGANISMO,@PERIODO from liquidaciones where id = vLIQUIDACION_ID;
select entero_1,entero_2,decimal_1,entero_3,entero_4,entero_5 into vLIMITE_INFERIOR,vLIMITE_SUPERIOR,vTOPE_POR_REGISTRO,vNCUOTAS_LIMITE_INFERIOR,vNCUOTAS_LIMITE_PROMEDIO,vNCUOTAS_LIMITE_SUPERIOR from global_datos where id = @ORGANISMO;
SET vNCUOTAS_LIMITE_INFERIOR = IF(IFNULL(vNCUOTAS_LIMITE_INFERIOR,0) <> 0, vNCUOTAS_LIMITE_INFERIOR ,1);
SET vNCUOTAS_LIMITE_PROMEDIO = IF(IFNULL(vNCUOTAS_LIMITE_PROMEDIO,0) <> 0, vNCUOTAS_LIMITE_PROMEDIO ,2);
SET vNCUOTAS_LIMITE_SUPERIOR = IF(IFNULL(vNCUOTAS_LIMITE_SUPERIOR,0) <> 0, vNCUOTAS_LIMITE_SUPERIOR ,3);
SET vLIMITE_INFERIOR = IFNULL(vLIMITE_INFERIOR,2000);
SET vLIMITE_SUPERIOR = IFNULL(vLIMITE_SUPERIOR,5000);

-- //////////////////////////////////////////////////////////////////////
-- INSERTO LA MORA QUE ES MENOR AL TOPE POR REGISTRO
-- //////////////////////////////////////////////////////////////////////
insert into liquidacion_socios(liquidacion_id,socio_id,
persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
codigo_empresa,codigo_reparticion,turno_pago,periodo,importe_dto,importe_adebitar,
formula_criterio_deuda)
SELECT 
	LiquidacionCuota.liquidacion_id,
	LiquidacionCuota.socio_id,
	LiquidacionCuota.persona_beneficio_id,
    LiquidacionCuota.codigo_organismo,
    ifnull(PersonaBeneficio.banco_id,''),
    ifnull(PersonaBeneficio.nro_sucursal,''),
    ifnull(PersonaBeneficio.tipo_cta_bco,''),
    ifnull(PersonaBeneficio.nro_cta_bco,''),
    ifnull(PersonaBeneficio.cbu,''),
    Persona.tipo_documento,
    Persona.documento,
    concat(ifnull(Persona.apellido,'S/D'),', ',ifnull(Persona.nombre,'S/D')),
    Persona.id,
    ifnull(Persona.cuit_cuil,''),
    ifnull(PersonaBeneficio.codigo_empresa,''),
    ifnull(PersonaBeneficio.codigo_reparticion,''),
    ifnull(PersonaBeneficio.turno_pago,''),
    0,
	sum(saldo_actual) as deuda,
    (SELECT CASE 
		WHEN sum(saldo_actual) < vLIMITE_INFERIOR 
        THEN sum(saldo_actual) / vNCUOTAS_LIMITE_INFERIOR 
		WHEN sum(saldo_actual) between vLIMITE_INFERIOR AND vLIMITE_SUPERIOR 
		THEN sum(saldo_actual) / vNCUOTAS_LIMITE_PROMEDIO 
		WHEN sum(saldo_actual) > vLIMITE_SUPERIOR 
		THEN sum(saldo_actual) / vNCUOTAS_LIMITE_SUPERIOR 
	END) as importe_adebitar,
    (SELECT CASE 
		WHEN sum(saldo_actual) < vLIMITE_INFERIOR 
		THEN CONCAT(sum(saldo_actual),' <' , vLIMITE_INFERIOR,' ==> IMPORTE A DEBITAR: ',round(sum(saldo_actual) / vNCUOTAS_LIMITE_INFERIOR,2),' (TOTAL ATRASO / ',vNCUOTAS_LIMITE_INFERIOR,')')
        WHEN sum(saldo_actual) between vLIMITE_INFERIOR AND vLIMITE_SUPERIOR 
        THEN CONCAT(vLIMITE_INFERIOR,' < ',round(sum(saldo_actual),2),' <= ' , vLIMITE_SUPERIOR,' ==> IMPORTE A DEBITAR: ', round(sum(saldo_actual) / vNCUOTAS_LIMITE_PROMEDIO,2),' (TOTAL ATRASO / ', vNCUOTAS_LIMITE_PROMEDIO,')')
        WHEN sum(saldo_actual) > vLIMITE_SUPERIOR 
        THEN CONCAT(sum(saldo_actual),' > ' , vLIMITE_SUPERIOR,' ==> IMPORTE A DEBITAR: ',round(sum(saldo_actual) / vNCUOTAS_LIMITE_SUPERIOR,2),' (TOTAL ATRASO / ',vNCUOTAS_LIMITE_SUPERIOR,')') 
        END) as formula_criterio_deuda
    -- '*** IMPORTE MORA ***'
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (
    PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id)
    INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
    AND IF(vSOCIO_ID IS NULL,TRUE,LiquidacionCuota.socio_id = vSOCIO_ID)
    AND PersonaBeneficio.acuerdo_debito = 0
    AND PersonaBeneficio.importe_max_registro_cbu = 0
GROUP BY
	LiquidacionCuota.socio_id,
	PersonaBeneficio.cbu,
	PersonaBeneficio.turno_pago
HAVING importe_adebitar <= vTOPE_POR_REGISTRO; 

SET @VALORESINSERT= '';
SET @REGISTRO = 1;
OPEN cursor_socio;
c1_loop: LOOP

	-- FETCH cursor_socio INTO vID,vSOCIO_ID,vIMPORTE_DEBITO,vFORMULA;
    FETCH cursor_socio INTO vSOCIO_ID,vBENEFICIOID,vBANCOID,vSUCURSAL,vTIPOCTABCO,vNROCTABCO,vCBU,vTDOC,vNDOC,vAPENOM,vPERSONAID,vCUITCUIL,vCODEMPRE,vCODREPA,vTURNOPAGO,vIMPORTE_DEBITO,vFORMULA,vULTIMO_COBRO,vSALDO_ACTUAL;

	IF (l_last_row = 1) THEN
		LEAVE c1_loop; 
	END IF;
    
    SET @N = IFNULL(vIMPORTE_DEBITO DIV vTOPE_POR_REGISTRO,0);
    
    SET vSALDO = vIMPORTE_DEBITO;
    SET @REGISTRO = 1;
    
    IF @N = 0 THEN
		LEAVE c1_loop;
    END IF;

    WHILE @REGISTRO <= @N DO

		SET @VALORESINSERT = CONCAT(IF(@VALORESINSERT = '','',CONCAT(@VALORESINSERT,',')),'(',@REGISTRO,',',vLIQUIDACION_ID,',',vSOCIO_ID,',',vBENEFICIOID,',\'',@ORGANISMO,
        '\',\'',vBANCOID,'\',\'',vSUCURSAL,'\',\'',vTIPOCTABCO,'\',\'',vNROCTABCO,'\',\'',vCBU,'\',\''
        ,vTDOC,'\',\'',vNDOC,'\',\'',vAPENOM,
        '\',',vPERSONAID,',\'',vCUITCUIL,'\',\'',vCODEMPRE,'\',\'',vCODREPA,'\',\'',vTURNOPAGO,'\',',0
        ,',',vTOPE_POR_REGISTRO,',',vTOPE_POR_REGISTRO,',\'',concat(vFORMULA,'\n*** MAXIMO POR REGISTRO ',vTOPE_POR_REGISTRO,' < ', vSALDO_ACTUAL ,' ***'),'\')');  

        SET vSALDO = vSALDO - vTOPE_POR_REGISTRO;
		SET @REGISTRO = @REGISTRO + 1;
        
    END WHILE;

    IF  vSALDO > 0 THEN

		SET @VALORESINSERT = CONCAT(IF(@VALORESINSERT = '','',CONCAT(@VALORESINSERT,',')),'(',@REGISTRO,',',vLIQUIDACION_ID,',',vSOCIO_ID,',',vBENEFICIOID,',\'',@ORGANISMO,
        '\',\'',vBANCOID,'\',\'',vSUCURSAL,'\',\'',vTIPOCTABCO,'\',\'',vNROCTABCO,'\',\'',vCBU,'\',\''
        ,vTDOC,'\',\'',vNDOC,'\',\'',vAPENOM,
        '\',',vPERSONAID,',\'',vCUITCUIL,'\',\'',vCODEMPRE,'\',\'',vCODREPA,'\',\'',vTURNOPAGO,'\',',0
        ,',',vSALDO,',',vSALDO,',\'',concat(vFORMULA,'\n*** MAXIMO POR REGISTRO ',vTOPE_POR_REGISTRO,' < ', vSALDO_ACTUAL ,' ***'),'\')');    
    
    END IF;
    
END LOOP c1_loop;    
CLOSE cursor_socio;

IF @VALORESINSERT <> '' THEN
	SET @INSERTLS = CONCAT('insert into liquidacion_socios(registro,liquidacion_id,socio_id,
				persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
				nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
				codigo_empresa,codigo_reparticion,turno_pago,periodo,importe_dto,importe_adebitar,
				formula_criterio_deuda) VALUES',@VALORESINSERT);           
	PREPARE stmt1 FROM @INSERTLS;
	EXECUTE stmt1;
	DEALLOCATE PREPARE stmt1;
END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_LIQUIDA_DEUDA_CBU_PERIODO_DISCRI_PERM`(
vLIQUIDACION_ID INT(11),
IN vSOCIO_ID INT(11)
)
BEGIN


select codigo_organismo,periodo into @ORGANISMO,@PERIODO from liquidaciones where id = vLIQUIDACION_ID;

-- //////////////////////////////////////////////////////////////////////
-- INSERTO EL PERIODO
-- //////////////////////////////////////////////////////////////////////

insert into liquidacion_socios(liquidacion_id,socio_id,
persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
codigo_empresa,codigo_reparticion,turno_pago,periodo,importe_dto,importe_adebitar,
formula_criterio_deuda)
select * from (SELECT 
	LiquidacionCuota.liquidacion_id,
	LiquidacionCuota.socio_id,
	LiquidacionCuota.persona_beneficio_id,
    LiquidacionCuota.codigo_organismo,
    PersonaBeneficio.banco_id,
    PersonaBeneficio.nro_sucursal,
    PersonaBeneficio.tipo_cta_bco,
    PersonaBeneficio.nro_cta_bco,
    PersonaBeneficio.cbu,
    Persona.tipo_documento,
    Persona.documento,
    concat(Persona.apellido,', ',Persona.nombre),
    Persona.id,
    Persona.cuit_cuil,
    PersonaBeneficio.codigo_empresa,
    PersonaBeneficio.codigo_reparticion,
    PersonaBeneficio.turno_pago,
    1,
    sum(saldo_actual) importe_dto,
    sum(saldo_actual) importe_adebitar,
    '* PERIODO NO PERM *' formula_criterio_deuda    
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (
    PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id
    and PersonaBeneficio.codigo_beneficio = LiquidacionCuota.codigo_organismo)
    INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
    INNER JOIN orden_descuentos OrdenDescuento on OrdenDescuento.id = LiquidacionCuota.orden_descuento_id
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
    AND IF(vSOCIO_ID IS NULL,TRUE,LiquidacionCuota.socio_id = vSOCIO_ID)
	AND LiquidacionCuota.periodo_cuota = @PERIODO	
    AND PersonaBeneficio.acuerdo_debito = 0 
    AND PersonaBeneficio.importe_max_registro_cbu = 0
    AND OrdenDescuento.permanente = 0
GROUP BY
	LiquidacionCuota.socio_id,
	PersonaBeneficio.cbu,
	PersonaBeneficio.turno_pago 
UNION
SELECT 
	LiquidacionCuota.liquidacion_id,
	LiquidacionCuota.socio_id,
	LiquidacionCuota.persona_beneficio_id,
    LiquidacionCuota.codigo_organismo,
    PersonaBeneficio.banco_id,
    PersonaBeneficio.nro_sucursal,
    PersonaBeneficio.tipo_cta_bco,
    PersonaBeneficio.nro_cta_bco,
    PersonaBeneficio.cbu,
    Persona.tipo_documento,
    Persona.documento,
    concat(Persona.apellido,', ',Persona.nombre),
    Persona.id,
    Persona.cuit_cuil,
    PersonaBeneficio.codigo_empresa,
    PersonaBeneficio.codigo_reparticion,
    PersonaBeneficio.turno_pago,
    1,
    sum(saldo_actual) importe_dto,
    sum(saldo_actual) importe_adebitar,
    '* PERIODO PERM *' formula_criterio_deuda
    
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (
    PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id
    and PersonaBeneficio.codigo_beneficio = LiquidacionCuota.codigo_organismo)
    INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
    INNER JOIN orden_descuentos OrdenDescuento on OrdenDescuento.id = LiquidacionCuota.orden_descuento_id
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
    AND IF(vSOCIO_ID IS NULL,TRUE,LiquidacionCuota.socio_id = vSOCIO_ID)
	AND LiquidacionCuota.periodo_cuota = @PERIODO	
    AND PersonaBeneficio.acuerdo_debito = 0 
    AND PersonaBeneficio.importe_max_registro_cbu = 0
    AND OrdenDescuento.permanente = 1
GROUP BY
	LiquidacionCuota.socio_id,
    OrdenDescuento.id,
	PersonaBeneficio.cbu,
	PersonaBeneficio.turno_pago) t1;

END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_LIQUIDA_DEUDA_CBU_PERIODO_GENERAL`(
vLIQUIDACION_ID INT(11),
IN vSOCIO_ID INT(11)
)
BEGIN

select codigo_organismo,periodo into @ORGANISMO,@PERIODO from liquidaciones where id = vLIQUIDACION_ID;

-- //////////////////////////////////////////////////////////////////////
-- INSERTO EL PERIODO
-- //////////////////////////////////////////////////////////////////////

insert into liquidacion_socios(liquidacion_id,socio_id,
persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
codigo_empresa,codigo_reparticion,turno_pago,periodo,importe_dto,importe_adebitar,
formula_criterio_deuda)
SELECT 
	LiquidacionCuota.liquidacion_id,
	LiquidacionCuota.socio_id,
	LiquidacionCuota.persona_beneficio_id,
    LiquidacionCuota.codigo_organismo,
    PersonaBeneficio.banco_id,
    PersonaBeneficio.nro_sucursal,
    PersonaBeneficio.tipo_cta_bco,
    PersonaBeneficio.nro_cta_bco,
    PersonaBeneficio.cbu,
    Persona.tipo_documento,
    Persona.documento,
    concat(Persona.apellido,', ',Persona.nombre),
    Persona.id,
    Persona.cuit_cuil,
    PersonaBeneficio.codigo_empresa,
    PersonaBeneficio.codigo_reparticion,
    PersonaBeneficio.turno_pago,
    1,
    sum(saldo_actual),
    sum(saldo_actual),
    '* IMPORTE PERIODO *'
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (
    PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id)
    INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
    AND IF(vSOCIO_ID IS NULL,TRUE,LiquidacionCuota.socio_id = vSOCIO_ID)
	AND LiquidacionCuota.periodo_cuota = @PERIODO	
    AND PersonaBeneficio.acuerdo_debito = 0 
    AND PersonaBeneficio.importe_max_registro_cbu = 0
GROUP BY
	LiquidacionCuota.socio_id,
	PersonaBeneficio.cbu,
	PersonaBeneficio.turno_pago;

END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_LIQUIDA_DEUDA_CJPC_GENERAL`(
vLIQUIDACION_ID INT(11),
IN vSOCIO_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
DECLARE vBENEFICIOID INT(11);
DECLARE vCBU VARCHAR(23);
DECLARE vTDOC VARCHAR(12);
DECLARE vNDOC VARCHAR(8);
DECLARE vAPENOM VARCHAR(40);
DECLARE vPERSONAID INT(11);
DECLARE vCUITCUIL VARCHAR(11);
DECLARE vORDENID INT(11);
DECLARE vORDEN_IMPORTECUOTA DECIMAL(10,2);
DECLARE vORDEN_CANTCUOTAS INT(11);
DECLARE vORDEN_FECHA DATE;
DECLARE vLEY VARCHAR(2);
DECLARE vTIPO VARCHAR(1);
DECLARE vNRO_BENEFICIO VARCHAR(6);
DECLARE vNRO_SUBBENEFICIO VARCHAR(2);
DECLARE vCJP_COD_CONS VARCHAR(5);
DECLARE vCJP_SCOD_CONS VARCHAR(1);
DECLARE vSALDO_ACTUAL DECIMAL(10,2);

DECLARE vIMPO_DEBITO DECIMAL(10,2);
DECLARE vIMPO_TOTAL DECIMAL(10,2);
DECLARE vIMPO_DEUDA_VENCIDA DECIMAL(10,2);
DECLARE vIMPO_DEUDA_NOVENCIDA DECIMAL(10,2);

DECLARE vDESCUENTO DECIMAL(10,2);
DECLARE vCONCEPTO VARCHAR(10);

DECLARE cursor_consumos CURSOR FOR
SELECT 
	LiquidacionCuota.socio_id,
	LiquidacionCuota.persona_beneficio_id,
    LiquidacionCuota.orden_descuento_id,
    OrdenDescuento.fecha,
    OrdenDescuento.importe_cuota,
    IF(OrdenDescuento.permanente = 0,OrdenDescuento.cuotas,0),
    PersonaBeneficio.nro_ley,
    PersonaBeneficio.tipo,
    PersonaBeneficio.nro_beneficio,
    lpad(ifnull(PersonaBeneficio.sub_beneficio,0),2,0) sub_beneficio,
    Persona.tipo_documento,
    Persona.documento,
    concat(ifnull(Persona.apellido,'S/D'),', ',ifnull(Persona.nombre,'S/D')),
    Persona.id,
    ifnull(Persona.cuit_cuil,''),
	IF(OrdenDescuento.permanente = 0,sum(LiquidacionCuota.saldo_actual),OrdenDescuento.importe_cuota) as saldo_actual,
	SUBSTR(@CJP_COD_CONS,1,LENGTH(@CJP_COD_CONS)-1) AS codigo_dto,
	RIGHT(@CJP_COD_CONS,1) AS sub_codigo,
    IF(OrdenDescuento.permanente = 0,IFNULL(LiquidacionSocioDescuento.importe_total,0),0) AS descuento
    ,CONCAT('CONSUMOS',IF(OrdenDescuento.permanente = 1,' (PERMANENTE)','')) as concepto
    ,OrdenDescuento.importe_cuota as importe_debito
    ,IF(OrdenDescuento.permanente = 1,0,SUM(IFNULL(LiquidacionCuotaVencido.saldo_actual,0))) as vencido
    ,IF(OrdenDescuento.permanente = 1,0,SUM(IFNULL(CuotaNoVencido.importe,0))) as novencido
FROM 	liquidacion_cuotas AS LiquidacionCuota 
INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id)
INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
INNER JOIN orden_descuentos OrdenDescuento on (OrdenDescuento.id = LiquidacionCuota.orden_descuento_id)
LEFT JOIN liquidacion_socio_descuentos LiquidacionSocioDescuento on (
	LiquidacionSocioDescuento.orden_descuento_id = LiquidacionCuota.orden_descuento_id
    AND LiquidacionSocioDescuento.socio_id = LiquidacionCuota.socio_id
    AND LiquidacionSocioDescuento.periodo_liquidacion = @PERIODO
    AND LiquidacionSocioDescuento.codigo_organismo = @ORGANISMO
)
LEFT JOIN orden_descuento_cuotas AS CuotaNoVencido on (
	CuotaNoVencido.socio_id = LiquidacionCuota.socio_id
    AND CuotaNoVencido.orden_descuento_id = LiquidacionCuota.orden_descuento_id
    AND CuotaNoVencido.periodo >= @PERIODO
    AND CuotaNoVencido.estado = 'A'
)
LEFT JOIN liquidacion_cuotas AS LiquidacionCuotaVencido on (
	LiquidacionCuotaVencido.liquidacion_id = LiquidacionCuota.liquidacion_id
    AND LiquidacionCuotaVencido.socio_id = LiquidacionCuota.socio_id
    AND LiquidacionCuotaVencido.orden_descuento_id = LiquidacionCuota.orden_descuento_id
    AND LiquidacionCuotaVencido.periodo_cuota < @PERIODO
)
WHERE 
LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
AND SUBSTRING(LiquidacionCuota.codigo_organismo,9,2) = 77
AND IF(vSOCIO_ID IS NULL,TRUE,LiquidacionCuota.socio_id = vSOCIO_ID)
AND LiquidacionCuota.tipo_cuota <> 'MUTUTCUOCSOC'
AND LiquidacionCuota.tipo_orden_dto <> 'CMUTU'
AND LiquidacionCuota.mutual_adicional_pendiente_id = 0
GROUP BY
LiquidacionCuota.codigo_organismo,
LiquidacionCuota.socio_id,
LiquidacionCuota.orden_descuento_id,
PersonaBeneficio.tipo,
PersonaBeneficio.nro_ley,
PersonaBeneficio.nro_beneficio,
PersonaBeneficio.sub_beneficio;

DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;

select codigo_organismo,periodo into @ORGANISMO,@PERIODO from liquidaciones where id = vLIQUIDACION_ID;
select entero_1,entero_2 into @CJP_COD_CSOC,@CJP_COD_CONS from global_datos where id = @ORGANISMO;

-- ////////////////////////////////////////////////////////////////
-- CAJA DE JUBILACIONES DE CORDOBA
-- ////////////////////////////////////////////////////////////////
-- CUOTA SOCIAL DEL PERIODO
insert into liquidacion_socios(liquidacion_id,socio_id,
							persona_beneficio_id,nro_ley,tipo,nro_beneficio,sub_beneficio,codigo_organismo,
							tipo_documento,documento,apenom,persona_id,cuit_cuil,
							importe_dto,importe_adebitar,codigo_dto,sub_codigo,periodo,registro,
                            formula_criterio_deuda)
SELECT 
	LiquidacionCuota.liquidacion_id,
	LiquidacionCuota.socio_id,
	LiquidacionCuota.persona_beneficio_id,
    PersonaBeneficio.nro_ley,
    PersonaBeneficio.tipo,
    PersonaBeneficio.nro_beneficio,
    lpad(ifnull(PersonaBeneficio.sub_beneficio,0),2,0) sub_beneficio,
    PersonaBeneficio.codigo_beneficio,
    Persona.tipo_documento,
    Persona.documento,
    concat(ifnull(Persona.apellido,'S/D'),', ',ifnull(Persona.nombre,'S/D')),
    Persona.id,
    ifnull(Persona.cuit_cuil,''),
SUM(saldo_actual) AS deuda,
SUM(saldo_actual) AS importe_adebitar,
SUBSTR(@CJP_COD_CSOC,1,LENGTH(@CJP_COD_CSOC)-1) AS codigo_dto,
RIGHT(@CJP_COD_CSOC,1) AS sub_codigo,
1 AS periodo,
LiquidacionCuota.registro
,'* CUOTA SOCIAL *'
FROM 	liquidacion_cuotas AS LiquidacionCuota 
INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id)
INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
WHERE 
LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
AND SUBSTRING(codigo_organismo,9,2) = 77
AND IF(vSOCIO_ID IS NULL,TRUE,LiquidacionCuota.socio_id = vSOCIO_ID)
AND LiquidacionCuota.tipo_cuota = 'MUTUTCUOCSOC'
AND LiquidacionCuota.periodo_cuota = @PERIODO
GROUP BY
LiquidacionCuota.codigo_organismo,
LiquidacionCuota.socio_id,
LiquidacionCuota.orden_descuento_id,
PersonaBeneficio.tipo,
PersonaBeneficio.nro_ley,
PersonaBeneficio.nro_beneficio,
PersonaBeneficio.sub_beneficio;



-- CONSUMOS
SET @VALORESINSERT= '';
SET @REGISTRO = 1;

OPEN cursor_consumos;
c1_loop: LOOP
    FETCH cursor_consumos INTO vSOCIO_ID,vBENEFICIOID,vORDENID,vORDEN_FECHA,
    vORDEN_IMPORTECUOTA,vORDEN_CANTCUOTAS,vLEY,vTIPO,vNRO_BENEFICIO,
    vNRO_SUBBENEFICIO,vTDOC,vNDOC,vAPENOM,vPERSONAID,vCUITCUIL,vSALDO_ACTUAL,
    vCJP_COD_CONS,vCJP_SCOD_CONS,vDESCUENTO,vCONCEPTO,vIMPO_DEBITO,vIMPO_DEUDA_VENCIDA,vIMPO_DEUDA_NOVENCIDA;

	IF (l_last_row = 1) THEN
		LEAVE c1_loop; 
	END IF; 
    
    SET @TOTAL_ORDEN = round(vORDEN_IMPORTECUOTA * vORDEN_CANTCUOTAS,2);
    
    SET @FORMULA = CONCAT(
		'ORDEN #',vORDENID,' ** ',vCONCEPTO,' **\n',
		'TOTAL ORDEN = ',@TOTAL_ORDEN, ' | TOTAL ADEUDADO = ',vSALDO_ACTUAL,'\n',
		'VENCIDO = ',vIMPO_DEUDA_VENCIDA,' | NO VENCIDO = ',vIMPO_DEUDA_NOVENCIDA,
		' | CUOTA: ',vORDEN_IMPORTECUOTA,' (',vORDEN_CANTCUOTAS,')',
		IF(vDESCUENTO <> 0,CONCAT(' (DTO APLICADO S/DEUDA ',vDESCUENTO,')'),'')
    );
    
    SET @VALORESINSERT = CONCAT(IF(@VALORESINSERT = '','',CONCAT(@VALORESINSERT,',')),'('
		,vLIQUIDACION_ID
        ,','
        ,vSOCIO_ID
        ,','
        ,vBENEFICIOID
        ,',\''
        ,vLEY
        ,'\',\''
        ,vTIPO
        ,'\',\''
        ,vNRO_BENEFICIO
        ,'\',\''
        ,vNRO_SUBBENEFICIO
        ,'\',\''
        ,@ORGANISMO
        ,'\',\''
        ,vTDOC
        ,'\',\''
        ,vNDOC
        ,'\',\''
        ,vAPENOM
        ,'\','
        ,vPERSONAID
        ,',\''
        ,vCUITCUIL
        ,'\','
        ,vIMPO_DEBITO
        ,','
        ,vIMPO_DEBITO
        ,',\''
        ,vCJP_COD_CONS
        ,'\',\''
        ,vCJP_SCOD_CONS
        ,'\','
        ,@REGISTRO
        ,',\''
		,@FORMULA
        ,'\','
        ,1
    ,')');

	SET @REGISTRO = @REGISTRO + 1;
    
END LOOP c1_loop;    
CLOSE cursor_consumos;

IF @VALORESINSERT <> '' THEN
	SET @INSERTLS = CONCAT('insert into liquidacion_socios(liquidacion_id,socio_id,
							persona_beneficio_id,nro_ley,tipo,nro_beneficio,sub_beneficio,codigo_organismo,
							tipo_documento,documento,apenom,persona_id,cuit_cuil,
							importe_dto,importe_adebitar,codigo_dto,sub_codigo,registro,
                            formula_criterio_deuda,periodo) VALUES',@VALORESINSERT);
	-- SELECT @INSERTLS;
	PREPARE stmt1 FROM @INSERTLS;
	EXECUTE stmt1;
	DEALLOCATE PREPARE stmt1;
END IF;

-- PARA LOS CONSUMOS PERMANENTES NO ENVIAR LA MORA, ENVIAR SOLAMENTE EL IMPORTE DEL PERIODO

-- LOS CONSUMOS VAN DETALLADOS ** UN DOLOR DE HUEVO DE ACA EN ADELANTE

-- CARGA UNA ORDEN CON LOS SALDOS SEGUN EL ORGANISMO

-- CONTROLAR SI NO TIENE UN DESCUENTO PARA CALCULAR

END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_LIQUIDA_DEUDA_SCORING`(IN
vLIQUIDACION_ID INT(11),
vSOCIO_ID INT(11)
)
BEGIN
/* 
ESTE PROCEDIMIENTO QUEDO VINCULADO AL ESQUEMA ANTERIOR
NO SE VA A USAR MAS UNA VEZ OFICIALIZADO EL NUEVO MODULO 
NOV2020
*/
select periodo into @periodo from liquidaciones where id = vLIQUIDACION_ID;
delete from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID);


-- insert into liquidacion_socio_scores(liquidacion_id,socio_id,`13`,`12`,`09`,`06`,`03`,`00`,cargos_adicionales,saldo_actual)

select liquidacion_id,socio_id, 
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
-- inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 12 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
-- inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 12 month),'%Y%m')  
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 9 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
-- inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 9 month),'%Y%m')
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 6 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
-- inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota  > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 6 month),'%Y%m') 
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 3 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
-- inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota  > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 3 month),'%Y%m') 
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 0 month),'%Y%m')
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
-- inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 0 month),'%Y%m')
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
-- inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id <> 0
and lc.socio_id = lc2.socio_id),0),
sum(saldo_actual) as saldo_actual
from liquidacion_cuotas lc2 where liquidacion_id = vLIQUIDACION_ID
AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID)
group by socio_id;


if cast((select `13` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID)) as decimal(10,2)) > 0 then

	update liquidacion_socio_scores set riesgo = 5 where liquidacion_id = vLIQUIDACION_ID
AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID);

else if cast((select `12` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID)) as decimal(10,2)) > 0 then

	update liquidacion_socio_scores set riesgo = 4 where liquidacion_id = vLIQUIDACION_ID
AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID);
else if cast((select `09` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID)) as decimal(10,2)) > 0 then

	update liquidacion_socio_scores set riesgo = 3 where liquidacion_id = vLIQUIDACION_ID
AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID);
else if cast((select `06` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID)) as decimal(10,2)) > 0 then

	update liquidacion_socio_scores set riesgo = 2 where liquidacion_id = vLIQUIDACION_ID
AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID);
else if cast((select `03` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID)) as decimal(10,2)) > 0 then

	update liquidacion_socio_scores set riesgo = 1 where liquidacion_id = vLIQUIDACION_ID
AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID);
else if cast((select `00` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID)) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 0 where liquidacion_id = vLIQUIDACION_ID
AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID);
end if;
end if;
end if;
end if;
end if;
end if;


END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_LIQUIDA_DEUDA_SOCIOS_SCORING`(
vLIQUIDACION_ID INT(11),
vSOCIO_ID INT(11)
)
BEGIN

DELETE FROM liquidacion_socio_scores 
WHERE
    liquidacion_id = vLIQUIDACION_ID
    AND IF(vSOCIO_ID IS NULL,
    TRUE,
    socio_id = vSOCIO_ID);

select periodo into @periodo from liquidaciones where id = vLIQUIDACION_ID;
delete from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID);

-- insert into liquidacion_socio_scores(liquidacion_id,socio_id,`13`,`12`,`09`,`06`,`03`,`00`,cargos_adicionales,saldo_actual)
DROP TEMPORARY TABLE IF EXISTS tmp_scoring;
CREATE TEMPORARY TABLE IF NOT EXISTS tmp_scoring (UNIQUE IDU_1(socio_id)) as
select liquidacion_id,socio_id, 
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
where lc.liquidacion_id = lc2.liquidacion_id and ifnull(lc.mutual_adicional_pendiente_id,0) = 0
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 12 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0) as a_13,
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
where lc.liquidacion_id = lc2.liquidacion_id and ifnull(lc.mutual_adicional_pendiente_id,0) = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 12 month),'%Y%m')  
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 9 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0) as a_12,
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
where lc.liquidacion_id = lc2.liquidacion_id and ifnull(lc.mutual_adicional_pendiente_id,0) = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 9 month),'%Y%m')
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 6 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0) as a_09,
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
where lc.liquidacion_id = lc2.liquidacion_id and ifnull(lc.mutual_adicional_pendiente_id,0) = 0
and lc.periodo_cuota  > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 6 month),'%Y%m') 
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 3 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0) as a_06,
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
where lc.liquidacion_id = lc2.liquidacion_id and ifnull(lc.mutual_adicional_pendiente_id,0) = 0
and lc.periodo_cuota  > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 3 month),'%Y%m') 
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 0 month),'%Y%m')
and lc.socio_id = lc2.socio_id),0) as a_03,
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
where lc.liquidacion_id = lc2.liquidacion_id and ifnull(lc.mutual_adicional_pendiente_id,0) = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 0 month),'%Y%m')
and lc.socio_id = lc2.socio_id),0) as a_00,
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
where lc.liquidacion_id = lc2.liquidacion_id and ifnull(lc.mutual_adicional_pendiente_id,0) <> 0
and lc.socio_id = lc2.socio_id),0) as cargos_adicionales,
sum(saldo_actual) as saldo_actual, 0 as riesgo, 0 as score
from liquidacion_cuotas lc2 where liquidacion_id = vLIQUIDACION_ID
AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID)
group by socio_id;

update tmp_scoring set riesgo = 5 where IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID) and a_13 > 0;
update tmp_scoring set riesgo = 4 where IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID) and a_12 > 0 and a_13 = 0;
update tmp_scoring set riesgo = 3 where IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID) and a_09 > 0 and (a_13 + a_12) = 0;
update tmp_scoring set riesgo = 2 where IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID) and a_06 > 0 and (a_13 + a_12 + a_09) = 0;
update tmp_scoring set riesgo = 1 where IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID) and a_03 > 0 and (a_13 + a_12 + a_09 + a_06) = 0;
update tmp_scoring set riesgo = 0 where IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID) and a_00 > 0 and (a_13 + a_12 + a_09 + a_06 + a_03) = 0;

insert into liquidacion_socio_scores(liquidacion_id,socio_id,`13`,`12`,`09`,`06`,`03`,`00`,cargos_adicionales,saldo_actual,riesgo,score)
select * from tmp_scoring;

DROP TEMPORARY TABLE IF EXISTS tmp_scoring;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_LIQUIDA_DEUDA_TOTALIZADOR`(
IN vLIQUIDACION_ID INT(11)
)
BEGIN

select periodo,codigo_organismo into @PERIODO,@ORGANISMO from liquidaciones where id = vLIQUIDACION_ID;

/* GENERO LOS TOTALES LIQUIDADOS Y LOS ACTUALIZO EN LA CABECERA */

select periodo,codigo_organismo into @PERIODO,@ORGANISMO from liquidaciones where id = vLIQUIDACION_ID;
select IFNULL(t1.saldo_actual,0),IFNULL(t2.saldo_actual,0),IFNULL(t3.saldo_actual,0),IFNULL(t4.saldo_actual,0)  
into @CUOTA_SOCIAL_VENCIDA,@CUOTA_SOCIAL_NOVENCIDA,@DEUDA_PERIODO,@DEUDA_VENCIDA
from liquidaciones l
left join (select liquidacion_id, sum(saldo_actual) as saldo_actual from liquidacion_cuotas 
                            where liquidacion_id = vLIQUIDACION_ID
							and liquidacion_cuotas.periodo_cuota < @PERIODO
                            and liquidacion_cuotas.tipo_producto = 'MUTUPROD0003'
                            and liquidacion_cuotas.tipo_cuota = 'MUTUTCUOCSOC' group by liquidacion_id
							) as t1 on t1.liquidacion_id = l.id
left join (select liquidacion_id, sum(saldo_actual) as saldo_actual from liquidacion_cuotas 
                            where liquidacion_id = vLIQUIDACION_ID
							and liquidacion_cuotas.periodo_cuota = @PERIODO
                            and liquidacion_cuotas.tipo_producto = 'MUTUPROD0003'
                            and liquidacion_cuotas.tipo_cuota = 'MUTUTCUOCSOC' group by liquidacion_id
							) as t2 on t2.liquidacion_id = l.id
left join (select liquidacion_id, sum(saldo_actual) as saldo_actual from liquidacion_cuotas 
                            where liquidacion_id = vLIQUIDACION_ID
							and liquidacion_cuotas.periodo_cuota = @PERIODO
                            and liquidacion_cuotas.tipo_cuota <> 'MUTUTCUOCSOC' group by liquidacion_id
							) as t3 on t3.liquidacion_id = l.id 
left join (select liquidacion_id, sum(saldo_actual) as saldo_actual from liquidacion_cuotas 
                            where liquidacion_id = vLIQUIDACION_ID
							and liquidacion_cuotas.periodo_cuota < @PERIODO
                            and liquidacion_cuotas.tipo_cuota <> 'MUTUTCUOCSOC' group by liquidacion_id
							) as t4 on t4.liquidacion_id = l.id
where l.id = vLIQUIDACION_ID;                              


UPDATE liquidaciones 
set 
cuota_social_vencida = @CUOTA_SOCIAL_VENCIDA,
cuota_social_periodo = @CUOTA_SOCIAL_NOVENCIDA,
deuda_periodo = @DEUDA_PERIODO,
deuda_vencida = @DEUDA_VENCIDA,
total_vencido = @CUOTA_SOCIAL_VENCIDA + @DEUDA_VENCIDA,
total_periodo = @CUOTA_SOCIAL_NOVENCIDA + @DEUDA_PERIODO,
total = total_vencido + total_periodo,
en_proceso = FALSE,
bloqueada = FALSE,
cerrada = TRUE                             
where id = vLIQUIDACION_ID;

END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_LIQUIDA_PUNITORIOS`(
vLIQUIDACION_ID INT(11),
IN vSOCIO_ID INT(11)
)
BEGIN

DECLARE l_last_row INT DEFAULT 0;
DECLARE vPROVEEDOR_ID INT(11) DEFAULT 0;
DECLARE vIMPUTAR_PROVEEDOR_ID INT(11) DEFAULT 0;
DECLARE vTIPO CHAR(1);
DECLARE vVALOR DECIMAL(10,2);
DECLARE vDEVENGA BOOLEAN;
DECLARE vCALCULO INT(11);
DECLARE vTIPO_CUOTA VARCHAR(12);
DECLARE vACTIVO BOOLEAN;

DECLARE c_adicionales CURSOR FOR 
select proveedor_id,imputar_proveedor_id,
tipo,valor,devengado_previo,deuda_calcula,tipo_cuota,activo
from mutual_adicionales
where codigo_organismo = @ORGANISMO and valor > 0
AND deuda_calcula = 5
and ifnull(periodo_desde,'000000') <= @PERIODO
and ifnull(periodo_hasta,'999912') >= @PERIODO;

DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;

select codigo_organismo,periodo into @ORGANISMO,@PERIODO from liquidaciones where id = vLIQUIDACION_ID;

SET GLOBAL tmp_table_size = 1024 * 1024 * 64;
SET GLOBAL max_heap_table_size = 1024 * 1024 * 64;

OPEN c_adicionales;

c1_loop: LOOP
FETCH c_adicionales INTO vPROVEEDOR_ID,vIMPUTAR_PROVEEDOR_ID,vTIPO,vVALOR,vDEVENGA,vCALCULO,vTIPO_CUOTA,vACTIVO;
		
	IF (l_last_row = 1) THEN
		LEAVE c1_loop; 
	END IF;	
    
    -- SET vPROVEEDOR_ID = IFNULL(vPROVEEDOR_ID,18);
    SET vIMPUTAR_PROVEEDOR_ID = IFNULL(vIMPUTAR_PROVEEDOR_ID,18);
	-- select vPROVEEDOR_ID,vIMPUTAR_PROVEEDOR_ID,vTIPO,vVALOR,vDEVENGA,vCALCULO,vTIPO_CUOTA,vACTIVO;	
    
	DROP TEMPORARY TABLE IF EXISTS tmp_adicionales_calculo;
	CREATE TEMPORARY TABLE IF NOT EXISTS tmp_adicionales_calculo as        
	SELECT vLIQUIDACION_ID liquidacion_id,@ORGANISMO organismo,vIMPUTAR_PROVEEDOR_ID proveedor_id,
	vTIPO tipo,vCALCULO calculo,vVALOR valor,vTIPO_CUOTA tipo_cuota,@PERIODO periodo,socio_id,
    SUM(saldo_actual) as saldo_actual
    ,round((PERIOD_DIFF(@PERIODO,periodo_cuota) * (vVALOR / 100)) * SUM(saldo_actual),2) as adicional
	,persona_beneficio_id
    ,PERIOD_DIFF(@PERIODO,periodo_cuota) as periodos
    ,min(periodo_cuota) as periodo_minimo
    ,max(periodo_cuota) as periodo_maximo
    ,orden_descuento_id
    ,tipo_orden_dto
    ,tipo_producto
	from liquidacion_cuotas             
	where liquidacion_cuotas.liquidacion_id = vLIQUIDACION_ID
    AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID)
	and ifnull(mutual_adicional_pendiente_id,0) = 0 
    and periodo_cuota < @PERIODO
	AND IF(vPROVEEDOR_ID IS NULL,TRUE,proveedor_id = vPROVEEDOR_ID)
	group by liquidacion_id,socio_id,proveedor_id,orden_descuento_id having adicional > 0;
    
	insert into mutual_adicional_pendientes(liquidacion_id,socio_id,codigo_organismo,proveedor_id,
			tipo,deuda_calcula,valor,tipo_cuota,periodo,total_deuda,importe,
			orden_descuento_id,persona_beneficio_id)      
	select liquidacion_id,socio_id,organismo,proveedor_id,
	tipo,calculo,valor,tipo_cuota,periodo,saldo_actual,adicional 
	,orden_descuento_id,persona_beneficio_id from tmp_adicionales_calculo;     
    
	insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,orden_descuento_id,
						orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,tipo_cuota,
						periodo_cuota,proveedor_id,vencida,importe,saldo_actual,codigo_organismo,
                        mutual_adicional_pendiente_id
				)    
    select a.liquidacion_id,a.socio_id,a.persona_beneficio_id,a.orden_descuento_id,NULL,
    t.tipo_orden_dto,
    t.tipo_producto,a.tipo_cuota,a.periodo,a.proveedor_id,0,a.importe,a.importe,
    a.codigo_organismo,a.id from tmp_adicionales_calculo t, mutual_adicional_pendientes a 
    where t.liquidacion_id = a.liquidacion_id
    AND IF(vSOCIO_ID IS NULL,TRUE,t.socio_id = vSOCIO_ID)
    and t.socio_id = a.socio_id
    and t.proveedor_id = a.proveedor_id
    and t.orden_descuento_id = a.orden_descuento_id
    and t.persona_beneficio_id = a.persona_beneficio_id;

    DROP TEMPORARY TABLE IF EXISTS tmp_adicionales_calculo;
END LOOP c1_loop;

CLOSE c_adicionales;

END$$
DELIMITER ;

