DROP PROCEDURE IF EXISTS `SP_DISKETTE_BANCO_COMAFI`;
DELIMITER $$
CREATE DEFINER=`root`@`%` PROCEDURE `SP_DISKETTE_BANCO_COMAFI`(
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
