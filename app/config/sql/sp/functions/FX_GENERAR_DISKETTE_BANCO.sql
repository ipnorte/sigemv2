DROP FUNCTION IF EXISTS `FX_GENERAR_DISKETTE_BANCO`;
DELIMITER $$
CREATE DEFINER= CURRENT_USER FUNCTION `FX_GENERAR_DISKETTE_BANCO`(
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
update liquidacion_socios, (
select socio_calificaciones.id,socio_calificaciones.socio_id,socio_calificaciones.periodo, socio_calificaciones.calificacion,global_datos.logico_2,global_datos.concepto_1 from socio_calificaciones
inner join global_datos on global_datos.id = socio_calificaciones.calificacion
inner join (select max(socio_calificaciones.id) id,socio_calificaciones.socio_id
from socio_calificaciones
inner join global_datos on global_datos.id = socio_calificaciones.calificacion
group by socio_id) t on t.id = socio_calificaciones.id
) AS calificaciones
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
