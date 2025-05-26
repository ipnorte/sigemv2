DROP PROCEDURE IF EXISTS `p_actualizar_persona`;
DROP PROCEDURE IF EXISTS `p_actualizar_persona_contacto`;
DROP PROCEDURE IF EXISTS `p_actualizar_persona_domicilio`;
DROP PROCEDURE IF EXISTS `p_anular_solicitud_credito`;
DROP PROCEDURE IF EXISTS `p_calcular_saldos_socio_periodo`;
DROP PROCEDURE IF EXISTS `p_consultar_asincrono`;
DROP PROCEDURE IF EXISTS `p_detener_asincrono`;
DROP PROCEDURE IF EXISTS `p_estado_cuenta`;
DROP PROCEDURE IF EXISTS `p_fpago`;
DROP PROCEDURE IF EXISTS `p_fpago_inverso`;
DROP PROCEDURE IF EXISTS `p_insertar_asincrono`;
DROP PROCEDURE IF EXISTS `p_insertar_beneficio`;
DROP PROCEDURE IF EXISTS `p_insertar_localidad`;
DROP PROCEDURE IF EXISTS `p_insertar_persona`;
DROP PROCEDURE IF EXISTS `p_insertar_solicitud_credito`;
DROP PROCEDURE IF EXISTS `p_insertar_solicitud_credito_cancelacion`;
DROP PROCEDURE IF EXISTS `p_insertar_solicitud_credito_cancelacion_preproceso`;
DROP PROCEDURE IF EXISTS `p_insertar_solicitud_credito_con_preproceso`;
DROP PROCEDURE IF EXISTS `p_insertar_solicitud_credito_documento`;
DROP PROCEDURE IF EXISTS `p_insertar_solicitud_credito_documento_preproceso`;
DROP PROCEDURE IF EXISTS `p_insertar_solicitud_credito_instruccion_pago`;
DROP PROCEDURE IF EXISTS `p_insertar_solicitud_credito_instruccion_pago_preproceso`;
DROP PROCEDURE IF EXISTS `p_listado_solicitudes_by_asincrono`;
DROP PROCEDURE IF EXISTS `p_marcar_solicitud_notificacion_leida`;
DROP PROCEDURE IF EXISTS `P_RESTABLECER_PASSWORD`;
DROP PROCEDURE IF EXISTS `p_socio_calificacion_score_resumen`;
DROP PROCEDURE IF EXISTS `p_validar_cbu`;
DROP PROCEDURE IF EXISTS `SP_CONTROL_ASINCRONO`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_CUOTA_SERVICIOS`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_CUOTA_SOCIAL`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_ACUERDO_DEBITO`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_ADICIONALES`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_GESTION_MORA`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_LIMITES`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO_RENUMERA`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_SCORING`;
DROP PROCEDURE IF EXISTS `SP_POSICION_CONSOLIDADA`;
DROP PROCEDURE IF EXISTS `SP_VENCIMIENTOS`;
DROP PROCEDURE IF EXISTS `v_buscar_solicitudes_credito`;
DROP PROCEDURE IF EXISTS `SP_REPORTE_PADRON_SERVICIOS`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_actualizar_password`(
	in vID INT(11), vPASS VARCHAR(40)
    )
BEGIN
    
	UPDATE usuarios set `password` =  vPASS
	where id = vID;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_actualizar_persona`(
in vID int(11),
vFECHA_NACIMIENTO date,
vSEXO varchar(1),
vESTADO_CIVIL varchar(12),
vNOMBRE_CONYUGE varchar(150),
vCUIT varchar(11)
)
BEGIN
update personas
set
	fecha_nacimiento = vFECHA_NACIMIENTO,
	sexo = vSEXO,
	estado_civil = vESTADO_CIVIL,
	nombre_conyuge = vNOMBRE_CONYUGE,
	cuit_cuil = vCUIT
where id = vID;	
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_actualizar_persona_contacto`(
in vID int(11),
vTELEFONO_FIJO varchar(50),
vTELEFONO_MOVIL varchar(50),
vTELEFONO_REFERENCIA varchar(50),
vPERSONA_REFERENCIA varchar(100),
vE_MAIL varchar(100)
)
BEGIN
update personas
set
	telefono_fijo = vTELEFONO_FIJO,
	telefono_movil = vTELEFONO_MOVIL,
	telefono_referencia = vTELEFONO_REFERENCIA,
	persona_referencia = vPERSONA_REFERENCIA,
	e_mail = vE_MAIL
where id = vID;	
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_actualizar_persona_domicilio`(
in vID int(11),
vCALLE varchar(150),
vNUMERO_CALLE varchar(5),
vPISO varchar(5),
vDPTO varchar(5),
vBARRIO varchar(100),
vLOCALIDAD_ID int(11),
vLOCALIDAD_DESC varchar(150),
vCODIGO_POSTAL varchar(8),
vPROVINCIA_ID int(11)
)
BEGIN
update personas
set
	calle = vCALLE,
	numero_calle = vNUMERO_CALLE,
	piso = vPISO,
	dpto = vDPTO,
	barrio = vBARRIO,
	localidad_id = vLOCALIDAD_ID,
	localidad = vLOCALIDAD_DESC,
	codigo_postal = vCODIGO_POSTAL,
	provincia_id = vPROVINCIA_ID
where id = vID;	
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_anular_solicitud_credito`(
	in vID INT(11)
    )
BEGIN
    
	UPDATE mutual_producto_solicitudes
	set anulada = 1, estado = 'MUTUESTA0000'
	where
		id = vID
		and aprobada = 0 and estado = 'MUTUESTA0001';
		
	DELETE FROM mutual_producto_solicitud_instruccion_pagos WHERE mutual_producto_solicitud_id = vID;
	DELETE FROM mutual_producto_solicitud_cancelaciones WHERE mutual_producto_solicitud_id = vID;	
	delete from mutual_producto_solicitud_pagos WHERE mutual_producto_solicitud_id = vID;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_calcular_saldos_socio_periodo`(
	IN vPERIODO VARCHAR(6),
	vSOCIO_ID INT(11)
)
BEGIN
SELECT periodo,
IFNULL(SUM(importe) - IFNULL((SELECT SUM(importe) FROM orden_descuento_cobro_cuotas cocu_1
WHERE cocu_1.orden_descuento_cuota_id = orden_descuento_cuotas.id
),0),0) AS saldo_periodo,
IFNULL((SELECT SUM(importe) FROM orden_descuento_cuotas odc_1 
WHERE odc_1.periodo < orden_descuento_cuotas.periodo 
AND odc_1.socio_id = orden_descuento_cuotas.socio_id)-
IFNULL((SELECT SUM(cocu.importe) FROM orden_descuento_cobro_cuotas cocu,orden_descuento_cuotas odc_1 
WHERE cocu.orden_descuento_cuota_id = odc_1.id
AND odc_1.periodo < orden_descuento_cuotas.periodo
AND odc_1.socio_id = orden_descuento_cuotas.socio_id),0),0) AS vencido,
IFNULL((SELECT SUM(importe) FROM orden_descuento_cuotas odc_1 
WHERE odc_1.periodo <= orden_descuento_cuotas.periodo 
AND odc_1.socio_id = orden_descuento_cuotas.socio_id)-
IFNULL((SELECT SUM(cocu.importe) FROM orden_descuento_cobro_cuotas cocu,orden_descuento_cuotas odc_1 
WHERE cocu.orden_descuento_cuota_id = odc_1.id
AND odc_1.periodo <= orden_descuento_cuotas.periodo
AND odc_1.socio_id = orden_descuento_cuotas.socio_id),0),0) AS saldo_total_acumulado_periodo,
IFNULL((SELECT SUM(importe) FROM orden_descuento_cuotas odc_1 
WHERE odc_1.periodo > orden_descuento_cuotas.periodo 
AND odc_1.socio_id = orden_descuento_cuotas.socio_id)-
IFNULL((SELECT SUM(cocu.importe) FROM orden_descuento_cobro_cuotas cocu,orden_descuento_cuotas odc_1 
WHERE cocu.orden_descuento_cuota_id = odc_1.id
AND odc_1.periodo > orden_descuento_cuotas.periodo
AND odc_1.socio_id = orden_descuento_cuotas.socio_id),0),0) AS a_vencer
FROM orden_descuento_cuotas WHERE socio_id = vSOCIO_ID
AND periodo = vPERIODO
GROUP BY periodo;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_consultar_asincrono`(in vID int(11))
BEGIN
	select * from v_asincronos where ID = vID;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_detener_asincrono`(vID INT(11))
BEGIN
	UPDATE asincronos 
	set
		estado = 'S',
		msg = '*** DETENIDO POR EL USUARIO ***'
	where id = vID;
	SELECT * FROM v_asincronos WHERE ID = vID;	
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_estado_cuenta`(
	IN vSOCIO_ID INT(11)
)
BEGIN
select 
orden_descuento_cuotas.id as ID,
'VENCIDO' AS TIPO,
orden_descuento_id AS ORDEN_DESCUENTO_ID,
concat(orden_descuentos.tipo_orden_dto,' #',orden_descuentos.numero) as TIPO_NUMERO,
tipo_cuota.concepto_1 as CONCEPTO,
orden_descuento_cuotas.periodo AS PERIODO,
concat(substr(orden_descuento_cuotas.periodo,5,2),'/',substr(orden_descuento_cuotas.periodo,1,4)) as PERIODO_STR,
concat(lpad(orden_descuento_cuotas.nro_cuota,2,'0'),'/',lpad(orden_descuentos.cuotas,2,'0')) as CUOTA,
orden_descuento_cuotas.importe AS IMPORTE_ORIGINAL,
ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0) as PAGOS,
orden_descuento_cuotas.importe - ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0) as SALDO_CONCILIADO,
ifnull((select sum(importe_debitado) from liquidacion_cuotas where liquidacion_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id
and liquidacion_cuotas.imputada = 0 and liquidacion_cuotas.para_imputar = 1),0) as PENDIENTE_ACREDITAR,
orden_descuento_cuotas.importe - ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0) - 
ifnull((select sum(importe_debitado) from liquidacion_cuotas where liquidacion_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id
and liquidacion_cuotas.imputada = 0 and liquidacion_cuotas.para_imputar = 1),0) as SALDO
from orden_descuento_cuotas 
inner JOIN orden_descuentos on (orden_descuentos.id = orden_descuento_cuotas.orden_descuento_id)
inner join global_datos as tipo_cuota on (tipo_cuota.id = orden_descuento_cuotas.tipo_cuota)
where orden_descuento_cuotas.socio_id = vSOCIO_ID
and orden_descuento_cuotas.importe > ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0)
and orden_descuento_cuotas.periodo < date_format(now(),'%Y%m') 
union
select 
orden_descuento_cuotas.id as ID,
'CORRIENTE' AS TIPO,
orden_descuento_id AS ORDEN_DESCUENTO_ID,
concat(orden_descuentos.tipo_orden_dto,' #',orden_descuentos.numero) as TIPO_NUMERO,
tipo_cuota.concepto_1 as CONCEPTO,
orden_descuento_cuotas.periodo AS PERIODO,
concat(substr(orden_descuento_cuotas.periodo,5,2),'/',substr(orden_descuento_cuotas.periodo,1,4)) as PERIODO_STR,
concat(lpad(orden_descuento_cuotas.nro_cuota,2,'0'),'/',lpad(orden_descuentos.cuotas,2,'0')) as CUOTA,
orden_descuento_cuotas.importe AS IMPORTE_ORIGINAL,
ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0) as PAGOS,
orden_descuento_cuotas.importe - ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0) as SALDO_CONCILIADO,
ifnull((select sum(importe_debitado) from liquidacion_cuotas where liquidacion_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id
and liquidacion_cuotas.imputada = 0 and liquidacion_cuotas.para_imputar = 1),0) as PENDIENTE_ACREDITAR,
orden_descuento_cuotas.importe - ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0) - 
ifnull((select sum(importe_debitado) from liquidacion_cuotas where liquidacion_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id
and liquidacion_cuotas.imputada = 0 and liquidacion_cuotas.para_imputar = 1),0) as SALDO
from orden_descuento_cuotas 
inner JOIN orden_descuentos on (orden_descuentos.id = orden_descuento_cuotas.orden_descuento_id)
inner join global_datos as tipo_cuota on (tipo_cuota.id = orden_descuento_cuotas.tipo_cuota)
where orden_descuento_cuotas.socio_id = vSOCIO_ID
and orden_descuento_cuotas.importe > ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0)
and orden_descuento_cuotas.periodo = date_format(now(),'%Y%m') 
union
select 
orden_descuento_cuotas.id as ID,
'A_VENCER' AS TIPO,
orden_descuento_id AS ORDEN_DESCUENTO_ID,
concat(orden_descuentos.tipo_orden_dto,' #',orden_descuentos.numero) as TIPO_NUMERO,
tipo_cuota.concepto_1 as CONCEPTO,
orden_descuento_cuotas.periodo AS PERIODO,
concat(substr(orden_descuento_cuotas.periodo,5,2),'/',substr(orden_descuento_cuotas.periodo,1,4)) as PERIODO_STR,
concat(lpad(orden_descuento_cuotas.nro_cuota,2,'0'),'/',lpad(orden_descuentos.cuotas,2,'0')) as CUOTA,
orden_descuento_cuotas.importe AS IMPORTE_ORIGINAL,
ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0) as PAGOS,
orden_descuento_cuotas.importe - ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0) as SALDO_CONCILIADO,
ifnull((select sum(importe_debitado) from liquidacion_cuotas where liquidacion_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id
and liquidacion_cuotas.imputada = 0 and liquidacion_cuotas.para_imputar = 1),0) as PENDIENTE_ACREDITAR,
orden_descuento_cuotas.importe - ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0) - 
ifnull((select sum(importe_debitado) from liquidacion_cuotas where liquidacion_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id
and liquidacion_cuotas.imputada = 0 and liquidacion_cuotas.para_imputar = 1),0) as SALDO
from orden_descuento_cuotas 
inner JOIN orden_descuentos on (orden_descuentos.id = orden_descuento_cuotas.orden_descuento_id)
inner join global_datos as tipo_cuota on (tipo_cuota.id = orden_descuento_cuotas.tipo_cuota)
where orden_descuento_cuotas.socio_id = vSOCIO_ID
and orden_descuento_cuotas.importe > ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0)
and orden_descuento_cuotas.periodo > date_format(now(),'%Y%m') 
order by PERIODO ASC,ID,CUOTA ASC;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_fpago`(
	vSOLICITADO DECIMAL(10,2),
    vCUOTAS INT(11),
    vTASA DECIMAL(10,4),
    vPORCENTAJE_ADICIONAL DECIMAL(10,3),
    vIVA DECIMAL(10,2)
)
BEGIN

    
	SET @CAPITAL = vSOLICITADO * (1 + vPORCENTAJE_ADICIONAL);

	SET vSOLICITADO = ROUND(vSOLICITADO,2);
    SET @CAPITAL = ROUND(@CAPITAL,2);
    
    SELECT F_PAGO_CUOTA(vSOLICITADO,vCUOTAS,vTASA,vPORCENTAJE_ADICIONAL,vIVA,'CUO') INTO @VALOR_CUOTA;
    SELECT F_PAGO_CUOTA(vSOLICITADO,vCUOTAS,vTASA,vPORCENTAJE_ADICIONAL,vIVA,'CAP') INTO @CAPITAL_CUOTA;
    SELECT F_PAGO_CUOTA(vSOLICITADO,vCUOTAS,vTASA,vPORCENTAJE_ADICIONAL,vIVA,'INT') INTO @INTERES;
    SELECT F_PAGO_CUOTA(vSOLICITADO,vCUOTAS,vTASA,vPORCENTAJE_ADICIONAL,vIVA,'IVA') INTO @IVA;
    SELECT F_PAGO_CUOTA(vSOLICITADO,vCUOTAS,vTASA,vPORCENTAJE_ADICIONAL,vIVA,'ADI') INTO @ADICIONALES;

    SET @VALOR_CUOTA = ROUND(@VALOR_CUOTA,2);
    SET @CAPITAL_CUOTA = ROUND(@CAPITAL_CUOTA,2);
    SET @INTERES = ROUND(@INTERES,2);
    SET @ADICIONALES = ROUND(@ADICIONALES,2);
    SET @IVA = ROUND(@IVA,2);
    
    SELECT vSOLICITADO AS PERCIBIDO,@CAPITAL AS SOLICITADO,vCUOTAS AS CUOTAS,
    @VALOR_CUOTA AS CUOTA,@CAPITAL_CUOTA AS CAPITAL, @INTERES AS INTERES,
    @IVA AS IVA_CUOTA,@ADICIONALES AS ADICIONAL;
    

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_fpago_inverso`(
	vSOLICITADO DECIMAL(10,2),
    vCUOTAS INT(11),
    vVALOR_CUOTA DECIMAL(10,2),
    vPORCENTAJE_ADICIONAL DECIMAL(10,3),
    vIVA DECIMAL(10,2)
)
BEGIN
	-- CALL p_fpago_inverso(1000,3,487.50,18,21);
	DECLARE vVALOR_CUOTA_REFERENCIA DECIMAL(10,2);
    DECLARE vTASA_AUX DECIMAL(10,2);
    DECLARE vINTENTOS INT;
    
    SET vTASA_AUX = 0;
	
    
    SET vINTENTOS = 10000;
    
    loop_label:  LOOP
    
		SET vTASA_AUX = vTASA_AUX + 0.01;
        SELECT F_PAGO_CUOTA(vSOLICITADO,vCUOTAS,vTASA_AUX,vPORCENTAJE_ADICIONAL,vIVA,'CUO') into vVALOR_CUOTA_REFERENCIA;
		SET @DIFF = vVALOR_CUOTA_REFERENCIA - vVALOR_CUOTA;
        IF @DIFF = 0 THEN
			LEAVE  loop_label;
        END IF;
		SET vINTENTOS = vINTENTOS - 1;
        IF vINTENTOS = 0 THEN
			LEAVE  loop_label;
        END IF;
		ITERATE  loop_label;
    
    END LOOP;
    
    CALL p_fpago(vSOLICITADO,vCUOTAS,vTASA_AUX,vPORCENTAJE_ADICIONAL,vIVA);

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_asincrono`(
	vPROPIETARIO varchar(120),
	vREMOTE_IP varchar(100),
	vPROCESO VARCHAR(150),
	vTITULO VARCHAR(250),
	vSUB_TITULO VARCHAR(250),
	vP1 VARCHAR(250),
	vP2 VARCHAR(250),
	vP3 VARCHAR(250),
	vP4 VARCHAR(250),
	vP5 VARCHAR(250)
)
BEGIN
    SET @ID = 0;
    INSERT INTO asincronos(propietario,remote_ip,proceso,titulo,subtitulo,p1,p2,p3,p4,p5)
    values(vPROPIETARIO,vREMOTE_IP,vPROCESO,vTITULO,vSUB_TITULO,vP1,vP2,vP3,vP4,vP5);
    SELECT * from v_asincronos where ID = LAST_INSERT_ID();
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_beneficio`(
in vPERSONA_ID INT(11),
vCODIGO_BENEFICIO VARCHAR(12),
vFECHA_INGRESO date,
vCODIGO_EMPRESA varchar(12),
vNRO_LEGAJO varchar(50),
vCODIGO_REPARTICION VARCHAR(11),
vTURNO_PAGO VARCHAR(12),
vCBU varchar(23),
vBANCO_ID varchar(5),
vSUCURSAL varchar(5),
vNRO_CTA_BCO varchar(50)
)
BEGIN
	SET @ID = 0;
	SELECT id into @ID FROM persona_beneficios
	where persona_id = vPERSONA_ID
	and codigo_beneficio = vCODIGO_BENEFICIO
	and codigo_empresa = vCODIGO_EMPRESA
	and turno_pago = vTURNO_PAGO
	and cbu = vCBU
	order by id DESC LIMIT 1;
	IF vCODIGO_EMPRESA <> 'MUTUEMPRP001' THEN 
		SET vTURNO_PAGO = vCODIGO_EMPRESA;
	END IF;
	IF vCODIGO_EMPRESA = 'MUTUEMPRP001' THEN
		select turno into @TURNO from liquidacion_turnos where codigo_empresa = 'MUTUEMPRP001'
		and ifnull(codigo_reparticion,'') <> ''
		and SUBSTR(trim(vTURNO_PAGO),1,8) = trim(codigo_reparticion)
		limit 1;
		IF TRIM(@TURNO) <> TRIM(vTURNO_PAGO) AND IFNULL(@TURNO,'') <> '' THEN
			SET vTURNO_PAGO = TRIM(@TURNO);
		END IF;
	end if;
	
	IF @ID = 0 THEN	
		SET vBANCO_ID = RIGHT(concat('00000',substring(vCBU,1,3)),5);
		INSERT INTO persona_beneficios(persona_id,codigo_beneficio,codigo_empresa,
		codigo_reparticion,turno_pago,cbu,banco_id,nro_sucursal,nro_cta_bco,created)
		VALUES(vPERSONA_ID,vCODIGO_BENEFICIO,vCODIGO_EMPRESA,vCODIGO_REPARTICION,vTURNO_PAGO,
		vCBU,vBANCO_ID,vSUCURSAL,vNRO_CTA_BCO,now());
		SELECT LAST_INSERT_ID() INTO @ID;
	END IF;
	
	SELECT * FROM v_persona_beneficios where ID = @ID;
	
		
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`mutual22`@`%` PROCEDURE `p_insertar_localidad`(
IN vCP VARCHAR(4), vNOMBRE VARCHAR(150),vPROVINCIA_ID INT(11), vLETRA_PROVINCIA VARCHAR(1),
vUSER_CREATED VARCHAR(50)
)
BEGIN
INSERT INTO localidades(cp,nombre,provincia_id,letra_provincia,user_created,created)
VALUES (vCP,UPPER(vNOMBRE),vPROVINCIA_ID,vLETRA_PROVINCIA,vUSER_CREATED,NOW());
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_persona`(
	IN 
		vTIPO_DOCUMENTO varchar(12),
		vDOCUMENTO varchar(11),
		vAPELLIDO varchar(100),
		vNOMBRE varchar(100),
		vCUIT_CUIL varchar(11)
    )
BEGIN
	if vTIPO_DOCUMENTO is null then set vTIPO_DOCUMENTO = 'PERSTPDC0001';
	end if;
	if LENGTH(trim(vDOCUMENTO)) < 8 AND vTIPO_DOCUMENTO = 'PERSTPDC0001' then
		set vDOCUMENTO = right(concat('00000000',TRIM(vDOCUMENTO)),8);
	end if;	
	
	INSERT INTO personas (tipo_documento,documento,apellido,nombre,cuit_cuil)
	VALUES(vTIPO_DOCUMENTO,vDOCUMENTO,vAPELLIDO,vNOMBRE,vCUIT_CUIL);
	SELECT * FROM v_personas WHERE ID = (SELECT LAST_INSERT_ID());
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito`(
IN
	vPROVEEDOR_ID INT(11),
	vPROVEEDOR_PLAN_ID INT(11),
	vPERSONA_ID INT(11),
	vCLIENTE_ID INT(11),
	vPERSONA_BENEFICIO_ID INT(11),
	vCUOTAS INT(11),
	vIMPORTE_CUOTA DECIMAL(10,2),
	vIMPORTE_SOLICITADO DECIMAL(10,2),
	vIMPORTE_PERCIBIDO DECIMAL(10,2),
	vVENDEDOR_ID INT(11),
	vOBSERVACIONES TEXT,
	vUSUARIO VARCHAR(50),
	vFORMA_PAGO VARCHAR(12)
)
BEGIN
	DECLARE vESTADO VARCHAR(12);
	DECLARE vPRODUCTO VARCHAR(12);
	DECLARE vTIPOORDEN VARCHAR(5); 
    DECLARE vPRESTAMO BOOLEAN;
	SET vCUOTAS = IF(vCUOTAS=0,NULL,vCUOTAS);
	SET vIMPORTE_CUOTA = IF(vIMPORTE_CUOTA=0,NULL,vIMPORTE_CUOTA);
	SET vIMPORTE_SOLICITADO = IF(vIMPORTE_SOLICITADO=0,NULL,vIMPORTE_SOLICITADO);
	SET vIMPORTE_PERCIBIDO = IF(vIMPORTE_PERCIBIDO=0,NULL,vIMPORTE_PERCIBIDO);
	SET @TOTAL = vCUOTAS * vIMPORTE_CUOTA;
	SET vPRESTAMO = FALSE;
	set vESTADO = 'MUTUESTA0001';
	-- SET vPRODUCTO = 'MUTUPROD0001';
	select tipo_producto into vPRODUCTO from proveedor_planes where id = vPROVEEDOR_PLAN_ID;
    select liquida_prestamo into vPRESTAMO from proveedores where id = vPROVEEDOR_ID;
	
	IF vPRODUCTO IS NULL THEN
		SET vPRODUCTO = 'MUTUPROD0001';
	END IF;
	
	IF vVENDEDOR_ID IS NULL THEN
		SET vESTADO = 'MUTUESTA0002';
	END IF;
	
    
	SELECT trim(concepto_3) into vTIPOORDEN from global_datos where id = vPRODUCTO;	
	
	INSERT INTO mutual_producto_solicitudes(proveedor_id,proveedor_plan_id,
	persona_id,socio_id,persona_beneficio_id,fecha,tipo_orden_dto,tipo_producto,
	estado,importe_total,cuotas,importe_cuota,importe_solicitado,importe_percibido,
	vendedor_id,created,user_created,observaciones,forma_pago)
	VALUES(vPROVEEDOR_ID,vPROVEEDOR_PLAN_ID,vPERSONA_ID,vCLIENTE_ID,
	vPERSONA_BENEFICIO_ID,NOW(),vTIPOORDEN,vPRODUCTO,vESTADO,
	@TOTAL,vCUOTAS,vIMPORTE_CUOTA,vIMPORTE_SOLICITADO,vIMPORTE_PERCIBIDO,
	vVENDEDOR_ID,NOW(),vUSUARIO,vOBSERVACIONES,vFORMA_PAGO);
	
	SELECT * FROM v_credito_solicitudes WHERE ID = (SELECT LAST_INSERT_ID());
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_cancelacion`(
	in vSOLICITUD_ID INT(11),
	vCANCELACION_ID INT(11)
    )
BEGIN
	INSERT INTO mutual_producto_solicitud_cancelaciones(mutual_producto_solicitud_id,cancelacion_orden_id)
	VALUES(vSOLICITUD_ID,vCANCELACION_ID);
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_cancelacion_preproceso`(
	in vUUID VARCHAR(100),
	vCANCELACION_ID INT(11)
    )
BEGIN
	INSERT INTO mutual_producto_solicitud_preproceso(uuid_identificador,tipo,cancelacion_id)
	VALUES(vUUID,2,vCANCELACION_ID);
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_con_preproceso`(
IN
	vPROVEEDOR_ID INT(11),
	vPROVEEDOR_PLAN_ID INT(11),
	vPERSONA_ID INT(11),
	vCLIENTE_ID INT(11),
	vPERSONA_BENEFICIO_ID INT(11),
	vCUOTAS INT(11),
	vIMPORTE_CUOTA DECIMAL(10,2),
	vIMPORTE_SOLICITADO DECIMAL(10,2),
	vIMPORTE_PERCIBIDO DECIMAL(10,2),
	vVENDEDOR_ID INT(11),
	vOBSERVACIONES TEXT,
	vUSUARIO VARCHAR(50),
	vFORMA_PAGO VARCHAR(12),
	vUUID VARCHAR(100)
)
BEGIN
	declare vSOLICITUD_ID INT(11);
	DECLARE vESTADO VARCHAR(12);
	DECLARE vPRODUCTO VARCHAR(12);
	DECLARE vTIPOORDEN VARCHAR(5); 
    DECLARE vPRESTAMO BOOLEAN;
	SET vCUOTAS = IF(vCUOTAS=0,NULL,vCUOTAS);
	SET vIMPORTE_CUOTA = IF(vIMPORTE_CUOTA=0,NULL,vIMPORTE_CUOTA);
	SET vIMPORTE_SOLICITADO = IF(vIMPORTE_SOLICITADO=0,NULL,vIMPORTE_SOLICITADO);
	SET vIMPORTE_PERCIBIDO = IF(vIMPORTE_PERCIBIDO=0,NULL,vIMPORTE_PERCIBIDO);
	SET @TOTAL = vCUOTAS * vIMPORTE_CUOTA;
    SET vPRESTAMO = FALSE;
	
	set vESTADO = 'MUTUESTA0001';
	-- SET vPRODUCTO = 'MUTUPROD0001';

	select tipo_producto into vPRODUCTO from proveedor_planes where id = vPROVEEDOR_PLAN_ID;
	select liquida_prestamo into vPRESTAMO from proveedores where id = vPROVEEDOR_ID;
       
	IF vPRODUCTO IS NULL THEN
		SET vPRODUCTO = 'MUTUPROD0001';
	END IF;

	IF vVENDEDOR_ID IS NULL THEN
		SET vESTADO = 'MUTUESTA0002';
	END IF;
	
	SELECT trim(concepto_3) into vTIPOORDEN from global_datos where id = vPRODUCTO;	
	
	INSERT INTO mutual_producto_solicitudes(proveedor_id,proveedor_plan_id,
	persona_id,socio_id,persona_beneficio_id,fecha,tipo_orden_dto,tipo_producto,
	estado,importe_total,cuotas,importe_cuota,importe_solicitado,importe_percibido,
	vendedor_id,created,user_created,observaciones,forma_pago,prestamo)
	VALUES(vPROVEEDOR_ID,vPROVEEDOR_PLAN_ID,vPERSONA_ID,vCLIENTE_ID,
	vPERSONA_BENEFICIO_ID,NOW(),vTIPOORDEN,vPRODUCTO,vESTADO,
	@TOTAL,vCUOTAS,vIMPORTE_CUOTA,vIMPORTE_SOLICITADO,vIMPORTE_PERCIBIDO,
	vVENDEDOR_ID,NOW(),vUSUARIO,vOBSERVACIONES,vFORMA_PAGO,vPRESTAMO);

	SET vSOLICITUD_ID = LAST_INSERT_ID();	

	INSERT INTO mutual_producto_solicitud_estados(mutual_producto_solicitud_id,estado,observaciones,created,user_created)
	values(vSOLICITUD_ID,vESTADO,vOBSERVACIONES,NOW(),vUSUARIO);	
	
	INSERT INTO mutual_producto_solicitud_cancelaciones(mutual_producto_solicitud_id,cancelacion_orden_id)
	SELECT vSOLICITUD_ID,cancelacion_id FROM mutual_producto_solicitud_preproceso
	WHERE uuid_identificador = vUUID AND tipo = 2;
	
	
	INSERT INTO mutual_producto_solicitud_instruccion_pagos(mutual_producto_solicitud_id,a_la_orden_de,concepto,importe)
	SELECT vSOLICITUD_ID,a_la_orden_de,concepto,importe FROM mutual_producto_solicitud_preproceso
	WHERE uuid_identificador = vUUID AND tipo = 1;	
		
	
	INSERT INTO mutual_producto_solicitud_documentos(mutual_producto_solicitud_id,file_name,file_type,file_data)
	SELECT vSOLICITUD_ID,file_name,file_type,file_data FROM mutual_producto_solicitud_preproceso
	WHERE uuid_identificador = vUUID AND tipo = 3;	
	
	DELETE FROM mutual_producto_solicitud_preproceso WHERE uuid_identificador = vUUID;		
	
	
	
	SELECT * FROM v_credito_solicitudes WHERE ID = vSOLICITUD_ID;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_documento`(
	IN vSOLICITUD_ID INT (11),
	vFILE_NAME VARCHAR(100),
	vFILE_TYPE VARCHAR(100),
	vFILE_DATA LONGBLOB 
)
BEGIN
INSERT INTO `mutual_producto_solicitud_documentos` 
	(`mutual_producto_solicitud_id`,`file_name`, `file_type`, `file_data`)
	VALUES (vSOLICITUD_ID,vFILE_NAME,vFILE_TYPE,vFILE_DATA);
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_documento_preproceso`(
	in vUUID VARCHAR(100),
	vFILE_NAME VARCHAR(100),
	vFILE_TYPE VARCHAR(100),
	vFILE_DATA LONGBLOB 
)
BEGIN
INSERT INTO `mutual_producto_solicitud_preproceso` 
	(uuid_identificador,tipo,`file_name`, `file_type`, `file_data`)
	VALUES (vUUID,3,vFILE_NAME,vFILE_TYPE,vFILE_DATA);
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_instruccion_pago`(
	in vSOLICITUD_ID INT(11),
	vORDEN varchar(255),
	vCONCEPTO text,
	vIMPORTE decimal(10,2)
    )
BEGIN
	SET vORDEN = IFNULL(vORDEN,'A MI ORDEN PERSONAL');
	SET vCONCEPTO = IFNULL(vCONCEPTO,'LIQUIDACION PRESTAMO');
	INSERT INTO mutual_producto_solicitud_instruccion_pagos(mutual_producto_solicitud_id,
	a_la_orden_de,concepto,importe)
	values(vSOLICITUD_ID,vORDEN,vCONCEPTO,vIMPORTE);
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_instruccion_pago_preproceso`(
	in vUUID VARCHAR(100),
	vORDEN varchar(255),
	vCONCEPTO text,
	vIMPORTE decimal(10,2)
    )
BEGIN
	SET vORDEN = IFNULL(vORDEN,'A MI ORDEN PERSONAL');
	SET vCONCEPTO = IFNULL(vCONCEPTO,'LIQUIDACION PRESTAMO');
	INSERT INTO mutual_producto_solicitud_preproceso(uuid_identificador,tipo,
	a_la_orden_de,concepto,importe)
	values(vUUID,1,vORDEN,vCONCEPTO,vIMPORTE);
    END$$
DELIMITER ;

DELIMITER $$
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
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_marcar_solicitud_notificacion_leida`(
	in vID INT(11)
    )
BEGIN
	update mutual_producto_solicitudes SET vendedor_notificar = 0 where id = vID;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem3_sa`@`localhost` PROCEDURE `P_RESTABLECER_PASSWORD`(
	in vID INT(11)
    )
BEGIN
    
UPDATE USUARIOS SET USERPASS = SHA2(USERNAME,256) WHERE ID = vID;
END$$
DELIMITER ;

DELIMITER $$
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
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_validar_cbu`(vCBU VARCHAR(23))
BEGIN

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CONTROL_ASINCRONO`(vPID INT,vStatus VARCHAR(1),vTot INT, vCont INT, vMsg VARCHAR(255))
BEGIN
	DECLARE vPorc DECIMAL(10,2) DEFAULT 0;
	
	SET vPorc = ROUND((vCont / vTot) * 100,2);
	SET vMsg = CONCAT('[',vCont,'|',vTot,'] ',vMsg);
	
	UPDATE asincronos 
	SET estado = vStatus, total = vTot, contador = vCont, porcentaje = vPorc, msg = vMsg
	WHERE id = vPID;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LIMPIA_BASE`(IN vPIN CHAR(12))
BEGIN
-- SIGEM*180407

IF  vPIN = 'SIGEM*180407' THEN 

	delete from asincrono_temporal_detalles;
	delete from asincrono_temporales;
	delete from asincrono_errores;
	delete from asincronos;

	alter table asincrono_temporal_detalles AUTO_INCREMENT = 1;
	alter table asincrono_temporales AUTO_INCREMENT = 1;
	alter table asincrono_errores AUTO_INCREMENT = 1;
	alter table asincronos AUTO_INCREMENT = 1;
    
	delete from liquidacion_socio_rendiciones;
	delete from liquidacion_intercambio_registro_procesados;
	delete from liquidacion_intercambio_registros;
	delete from liquidacion_intercambios;
	delete from liquidacion_socio_envio_registros;
	delete from liquidacion_socio_envios;
	delete from liquidacion_socios;
	delete from liquidacion_cuotas;
	delete from liquidaciones;

	alter table liquidacion_socio_rendiciones AUTO_INCREMENT = 1;
	alter table liquidacion_intercambio_registro_procesados AUTO_INCREMENT = 1;
	alter table liquidacion_intercambio_registros AUTO_INCREMENT = 1;
	alter table liquidacion_intercambios AUTO_INCREMENT = 1;
	alter table liquidacion_socio_envio_registros AUTO_INCREMENT = 1;
	alter table liquidacion_socio_envios AUTO_INCREMENT = 1;
	alter table liquidacion_socios AUTO_INCREMENT = 1;
	alter table liquidacion_cuotas AUTO_INCREMENT = 1;
	alter table liquidaciones AUTO_INCREMENT = 1;   
    
	delete from mutual_adicional_pendientes;
	delete from mutual_adicionales;

	alter table mutual_adicional_pendientes AUTO_INCREMENT = 1;
	alter table mutual_adicionales AUTO_INCREMENT = 1;


	update orden_descuentos set anterior_orden_descuento_id = null, nueva_orden_descuento_id = null;

	delete from cancelacion_orden_cuotas;
	delete from cancelacion_ordenes;
	delete from orden_descuento_cobro_cuotas;
	delete from orden_descuento_cobros;
	delete from orden_caja_cobro_cuotas;
	delete from orden_caja_cobros;
	delete from orden_descuento_cuotas;
    delete from mutual_producto_solicitud_estados;
	delete from mutual_producto_solicitud_instruccion_pagos;
	delete from mutual_producto_solicitud_documentos;
	delete from mutual_producto_solicitud_cancelaciones;
	delete from mutual_producto_solicitud_pagos;
	delete from mutual_producto_solicitud_preproceso;
	delete from mutual_producto_solicitudes;
	delete from mutual_productos;
	delete from mutual_servicio_solicitud_adicionales;
	delete from mutual_servicio_solicitudes;
	delete from mutual_servicio_valores;
	delete from mutual_servicios;
	delete from orden_descuentos;
	delete from orden_pago_formas;
	delete from orden_pago_facturas;
	delete from orden_pago_detalles;
	delete from orden_pagos;
	delete from recibo_formas;
	delete from recibo_facturas;
	delete from recibo_detalles;
	delete from recibos;
	delete from banco_cuenta_movimientos;
	delete from banco_cuentas;
	delete from banco_cheque_terceros;
	delete from banco_cuenta_chequeras;
	delete from co_asiento_renglones;
	delete from co_asientos;
	delete from co_plan_cuentas;
	delete from co_ejercicios;


	update tipo_documentos set numero = 0;


	alter table cancelacion_orden_cuotas AUTO_INCREMENT = 1;
	alter table cancelacion_ordenes AUTO_INCREMENT = 1;
	alter table orden_descuento_cobro_cuotas AUTO_INCREMENT = 1;
	alter table orden_descuento_cobros AUTO_INCREMENT = 1;
	alter table orden_caja_cobro_cuotas AUTO_INCREMENT = 1;
	alter table orden_caja_cobros AUTO_INCREMENT = 1;
	alter table orden_descuento_cuotas AUTO_INCREMENT = 1;
    alter table mutual_producto_solicitud_estados AUTO_INCREMENT = 1;
	alter table mutual_producto_solicitud_instruccion_pagos AUTO_INCREMENT = 1;
	alter table mutual_producto_solicitud_documentos AUTO_INCREMENT = 1;
	alter table mutual_producto_solicitud_cancelaciones AUTO_INCREMENT = 1;
	alter table mutual_producto_solicitud_pagos AUTO_INCREMENT = 1;
	alter table mutual_producto_solicitud_preproceso AUTO_INCREMENT = 1;
	alter table mutual_producto_solicitudes AUTO_INCREMENT = 1;
	alter table mutual_productos AUTO_INCREMENT = 1;
	alter table mutual_servicio_solicitud_adicionales AUTO_INCREMENT = 1;
	alter table mutual_servicio_solicitudes AUTO_INCREMENT = 1;
	alter table mutual_servicio_valores AUTO_INCREMENT = 1;
	alter table mutual_servicios AUTO_INCREMENT = 1;
	alter table orden_descuentos AUTO_INCREMENT = 1;
	alter table orden_pago_formas AUTO_INCREMENT = 1;
	alter table orden_pago_facturas AUTO_INCREMENT = 1;
	alter table orden_pago_detalles AUTO_INCREMENT = 1;
	alter table orden_pagos AUTO_INCREMENT = 1;
	alter table recibo_formas AUTO_INCREMENT = 1;
	alter table recibo_facturas AUTO_INCREMENT = 1;
	alter table recibo_detalles AUTO_INCREMENT = 1;
	alter table recibos AUTO_INCREMENT = 1;
	alter table banco_cuenta_movimientos AUTO_INCREMENT = 1;
	alter table banco_cuentas AUTO_INCREMENT = 1;
	alter table banco_cheque_terceros AUTO_INCREMENT = 1;
	alter table banco_cuenta_chequeras AUTO_INCREMENT = 1;
	alter table co_asiento_renglones AUTO_INCREMENT = 1;
	alter table co_asientos AUTO_INCREMENT = 1;
	alter table co_plan_cuentas AUTO_INCREMENT = 1;
	alter table co_ejercicios AUTO_INCREMENT = 1;   
    
	delete from vendedor_remitos;
	delete from vendedor_proveedor_planes;
	delete from vendedores;

	alter table vendedor_remitos AUTO_INCREMENT = 1;
	alter table vendedor_proveedor_planes AUTO_INCREMENT = 1;
	alter table vendedores AUTO_INCREMENT = 1;

	delete from proveedor_liquidaciones;
	delete from proveedor_comisiones where proveedor_id <> 18;
	delete from proveedor_facturas;
	delete from proveedor_grilla_cuotas;
	delete from proveedor_grillas;
	delete from proveedor_vencimientos where proveedor_id <> 18;
	delete from proveedor_tipo_asiento_renglones;
	delete from proveedor_tipo_asientos;
	delete from proveedor_plan_organismos;
	delete from proveedor_plan_grilla_cuotas;
	delete from proveedor_plan_grillas;
	delete from proveedor_planes;
	delete from proveedores where id <> 18;
	delete from cliente_tipo_asiento_renglones;
	delete from cliente_tipo_asientos;
	delete from cliente_factura_detalles;
	delete from cliente_facturas;
	delete from clientes;

	alter table proveedor_liquidaciones AUTO_INCREMENT = 1;
	alter table proveedor_comisiones AUTO_INCREMENT = 19;
	alter table proveedor_facturas AUTO_INCREMENT = 1;
	alter table proveedor_grilla_cuotas AUTO_INCREMENT = 1;
	alter table proveedor_grillas AUTO_INCREMENT = 1;
	alter table proveedor_vencimientos AUTO_INCREMENT = 19;
	alter table proveedor_tipo_asiento_renglones AUTO_INCREMENT = 1;
	alter table proveedor_tipo_asientos AUTO_INCREMENT = 1;
	alter table proveedor_plan_organismos AUTO_INCREMENT = 1;
	alter table proveedor_plan_grilla_cuotas AUTO_INCREMENT = 1;
	alter table proveedor_plan_grillas AUTO_INCREMENT = 1;
	alter table proveedor_planes AUTO_INCREMENT = 1;
	alter table proveedores AUTO_INCREMENT = 19;
	alter table cliente_tipo_asiento_renglones AUTO_INCREMENT = 1;
	alter table cliente_tipo_asientos AUTO_INCREMENT = 1;
	alter table cliente_factura_detalles AUTO_INCREMENT = 1;
	alter table cliente_facturas AUTO_INCREMENT = 1;
	alter table clientes AUTO_INCREMENT = 19;   
    
    
	delete from socio_calificaciones;
	delete from socio_adicionales;
	delete from socio_convenio_cuotas;
	delete from socio_convenios;
	delete from socio_reintegro_pagos;
	delete from socio_reintegros;
	delete from socio_historicos;
	delete from socio_solicitudes;
	delete from socios;
	delete from persona_beneficio_compartidos;
	delete from persona_beneficios;
    delete from persona_novedades;
    delete from personas;
    
	alter table socio_calificaciones AUTO_INCREMENT = 1;
	alter table socio_adicionales AUTO_INCREMENT = 1;
	alter table socio_convenio_cuotas AUTO_INCREMENT = 1;
	alter table socio_convenios AUTO_INCREMENT = 1;
	alter table socio_reintegro_pagos AUTO_INCREMENT = 1;
	alter table socio_reintegros AUTO_INCREMENT = 1;
	alter table socio_historicos AUTO_INCREMENT = 1;
	alter table socio_solicitudes AUTO_INCREMENT = 1;
	alter table socios AUTO_INCREMENT = 1; 
    alter table persona_beneficio_compartidos AUTO_INCREMENT = 1;
    alter table persona_beneficios AUTO_INCREMENT = 1;
    alter table persona_novedades AUTO_INCREMENT = 1;
    alter table personas AUTO_INCREMENT = 1;
    
    delete from global_datos where id like 'MUTUPROD%' and id > 'MUTUPROD0003';
    
    delete from usuarios where usuario not in ('ADRIAN','GUSTAVO');
    update usuarios set password = sha1(usuario);
    
	update proveedores set cuit = '00000000001',
	razon_social = 'CORDOBA SOFT IT', razon_social_resumida = 'CORDOBASOFT',
	calle = 'GRAL PAZ', numero_calle = '94', piso = '3', dpto = '2', localidad = 'CORDOBA',
    codigo_postal = '5000', barrio = 'CENTRO', email = 'atorres@cordobasoft.com', telefono_movil = '3515171828' where id = 18;
    

END IF;





END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_CUOTA_SERVICIOS`(
vSOCIO_ID INT(11),
vPERIODO VARCHAR(6),
vORGANISMO VARCHAR(12)
)
BEGIN

DECLARE vCODIGO_ORGANISMO VARCHAR(12);
DECLARE vORDEN_ID INT(11);
DECLARE vSOLICITUD INT(11);
DECLARE vFECHA DATE;
DECLARE vPERIODO_HASTA VARCHAR(6);
DECLARE vTIPO_ORDEN VARCHAR(4);
DECLARE vTIPO_PRODUCTO VARCHAR(12);
DECLARE vTIPO_CUOTA VARCHAR(12);
DECLARE vBENEFICIO_ID INT(11);
DECLARE vPROVEEDOR_ID INT(11);
DECLARE vNRO_REFERENCIA_PROVEEDOR VARCHAR(10);
DECLARE vIMPORTE_ORDEN_CUOTA DECIMAL(10,2);
DECLARE vACTIVO BOOLEAN;


-- ----------------------------------------------------------
-- BUSCO LAS ORDENES PERMANENTES
-- ----------------------------------------------------------
SELECT
	PersonaBeneficio.codigo_beneficio,
	OrdenDescuento.id,
    OrdenDescuento.numero,
    OrdenDescuento.fecha,
    ifnull(OrdenDescuento.periodo_hasta,'999912'),
	OrdenDescuento.tipo_orden_dto,
	OrdenDescuento.tipo_producto,
    GlobalDato.concepto_2,
	OrdenDescuento.persona_beneficio_id,
	OrdenDescuento.proveedor_id,
	OrdenDescuento.nro_referencia_proveedor,
	OrdenDescuento.importe_cuota,
	OrdenDescuento.activo
INTO vCODIGO_ORGANISMO,vORDEN_ID,vSOLICITUD,vFECHA,vPERIODO_HASTA,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,vBENEFICIO_ID,vPROVEEDOR_ID,vNRO_REFERENCIA_PROVEEDOR,vIMPORTE_ORDEN_CUOTA,vACTIVO    
FROM 
	orden_descuentos as OrdenDescuento, 
	socios as Socio,
	persona_beneficios as PersonaBeneficio,
    global_datos as GlobalDato
WHERE
	Socio.id = vSOCIO_ID  
	AND OrdenDescuento.socio_id = Socio.id 
	AND OrdenDescuento.tipo_orden_dto <> 'CMUTU'
	AND OrdenDescuento.tipo_producto <> 'MUTUPROD0003'
    AND OrdenDescuento.tipo_producto = GlobalDato.id
	AND OrdenDescuento.periodo_ini <= vPERIODO
	AND IF(Socio.activo = 0,IFNULL(OrdenDescuento.periodo_hasta,vPERIODO),IF(ISNULL(OrdenDescuento.periodo_hasta) AND OrdenDescuento.activo = 1,'999999',OrdenDescuento.periodo_hasta)) > vPERIODO
	AND OrdenDescuento.persona_beneficio_id = PersonaBeneficio.id
	AND PersonaBeneficio.codigo_beneficio = vORGANISMO
	AND OrdenDescuento.permanente = 1;



IF vORDEN_ID <> 0 THEN

	set @orden_descuento_cuota_id = null;
	select odc.id INTO @orden_descuento_cuota_id from orden_descuento_cuotas odc, persona_beneficios be 
			where odc.orden_descuento_id = vORDEN_ID and odc.periodo = vPERIODO
			and odc.persona_beneficio_id = be.id    
			and odc.tipo_cuota = vTIPO_CUOTA
            and odc.estado <> 'B'
			and be.codigo_beneficio = vORGANISMO;

	SET @IMPORTE_CUOTA_SERVICIO = 0;
	SET @importe_fijo_producto = 0;

	-- ----------------------------------------------------------
	-- SACO EL IMPORTE DE LA TABLA DE PRODUCTOS
    -- ----------------------------------------------------------
	select MutualProducto.importe_fijo 
	INTO @importe_fijo_producto
	from mutual_productos as MutualProducto 
	where MutualProducto.tipo_producto = vTIPO_PRODUCTO 
    and MutualProducto.proveedor_id = vPROVEEDOR_ID 
    AND importe_fijo <> 0;

	SET @IMPORTE_CUOTA_SERVICIO = IF(vIMPORTE_ORDEN_CUOTA = 0,@importe_fijo_producto,vIMPORTE_ORDEN_CUOTA);

	-- ----------------------------------------------------------
    -- SERVICIOS
    -- ----------------------------------------------------------
	IF vTIPO_ORDEN = 'SERV' THEN

        -- SACO LOS VALORES VIGENTES
        
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
        
        UPDATE mutual_servicio_solicitudes set importe_mensual = @importe_titular,
        importe_mensual_total = @IMPORTE_SERVICIO_MENSUAL
        where id = vSOLICITUD;
        
        update mutual_servicio_solicitud_adicionales
        set importe_mensual = @costo_adicional
        where mutual_servicio_solicitud_id = vSOLICITUD 
        and ifnull(periodo_hasta,'000000') <=  vPERIODO;
        
        update orden_descuentos
        set importe_total = @IMPORTE_SERVICIO_MENSUAL, importe_cuota = @IMPORTE_SERVICIO_MENSUAL
        where id = vORDEN_ID;
        
        SET @IMPORTE_CUOTA_SERVICIO = round(@IMPORTE_SERVICIO_MENSUAL,2);
        
    END IF;
	
    SET @IMPORTE_CUOTA_SERVICIO = IF(vPERIODO_HASTA > vPERIODO,@IMPORTE_CUOTA_SERVICIO,0);

	IF @IMPORTE_CUOTA_SERVICIO > 0 THEN
    
		CALL SP_VENCIMIENTOS(NULL,
		vPROVEEDOR_ID,vORGANISMO,vPERIODO,vFECHA,
		@PERIODO_INI,@VTO_SOCIO,@VTO_PROVEEDOR,@ULTIMO_PERIODO); 
        
    IF @orden_descuento_cuota_id IS NOT NULL AND vORDEN_ID IS NOT NULL THEN 
		UPDATE orden_descuento_cuotas 
        SET importe = @IMPORTE_CUOTA_SERVICIO 
        WHERE id = @orden_descuento_cuota_id;
    ELSE 
    
		IF vORDEN_ID IS NOT NULL THEN
			INSERT INTO orden_descuento_cuotas(orden_descuento_id, 
			socio_id, persona_beneficio_id, tipo_orden_dto, 
			tipo_producto, tipo_cuota, periodo, estado, situacion, 
			vencimiento, vencimiento_proveedor, 
			nro_cuota, importe, proveedor_id, 
			nro_referencia_proveedor) 
			VALUES(vORDEN_ID,vSOCIO_ID,vBENEFICIO_ID,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,
			vPERIODO,'A','MUTUSICUMUTU',@VTO_SOCIO,@VTO_PROVEEDOR,0,
			@IMPORTE_CUOTA_SERVICIO,vPROVEEDOR_ID,vNRO_REFERENCIA_PROVEEDOR);
        END IF;
    END IF;        
    
    END IF;

	-- select @IMPORTE_CUOTA_SERVICIO,vCODIGO_ORGANISMO,vORDEN_ID,vSOLICITUD,vFECHA,vPERIODO_HASTA,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,vBENEFICIO_ID,vPROVEEDOR_ID,vNRO_REFERENCIA_PROVEEDOR,vIMPORTE_ORDEN_CUOTA,vACTIVO;

END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_CUOTA_SOCIAL`(
vSOCIO_ID INT(11),
vPERIODO VARCHAR(6),
vORGANISMO VARCHAR(12)
)
BEGIN
DECLARE vCODIGO_ORGANISMO VARCHAR(12);
DECLARE vORDEN_ID INT(11);
DECLARE vFECHA DATE;
DECLARE vPERIODO_HASTA VARCHAR(6);
DECLARE vTIPO_ORDEN VARCHAR(4);
DECLARE vTIPO_PRODUCTO VARCHAR(12);
DECLARE vCODIGO_EMPRESA VARCHAR(12);
DECLARE vTIPO_CUOTA VARCHAR(12);
DECLARE vBENEFICIO_ID INT(11);
DECLARE vPROVEEDOR_ID INT(11);
DECLARE vNRO_REFERENCIA_PROVEEDOR VARCHAR(10);
DECLARE vIMPORTE_ORDEN_CUOTA DECIMAL(10,2);
DECLARE vACTIVO BOOLEAN;

SELECT
	PersonaBeneficio.codigo_beneficio,
    PersonaBeneficio.codigo_empresa,
	OrdenDescuento.id,
    OrdenDescuento.fecha,
    ifnull(OrdenDescuento.periodo_hasta,'999912'),
	OrdenDescuento.tipo_orden_dto,
	OrdenDescuento.tipo_producto,
    GlobalDato.concepto_2,
	OrdenDescuento.persona_beneficio_id,
	OrdenDescuento.proveedor_id,
	OrdenDescuento.nro_referencia_proveedor,
	OrdenDescuento.importe_cuota,
	OrdenDescuento.activo
INTO vCODIGO_ORGANISMO,vCODIGO_EMPRESA,vORDEN_ID,vFECHA,vPERIODO_HASTA,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,vBENEFICIO_ID,vPROVEEDOR_ID,vNRO_REFERENCIA_PROVEEDOR,vIMPORTE_ORDEN_CUOTA,vACTIVO    
FROM 
	orden_descuentos as OrdenDescuento, 
	socios as Socio,
	persona_beneficios as PersonaBeneficio,
    global_datos as GlobalDato
WHERE
	Socio.id = vSOCIO_ID  
	AND OrdenDescuento.socio_id = Socio.id 
	AND OrdenDescuento.tipo_orden_dto = 'CMUTU'
	AND OrdenDescuento.tipo_producto = 'MUTUPROD0003'
    AND OrdenDescuento.tipo_producto = GlobalDato.id
	AND OrdenDescuento.periodo_ini <= vPERIODO
	AND IF(Socio.activo = 0,IFNULL(OrdenDescuento.periodo_hasta,vPERIODO),IF(ISNULL(OrdenDescuento.periodo_hasta) AND OrdenDescuento.activo = 1,'999999',OrdenDescuento.periodo_hasta)) > vPERIODO
	AND OrdenDescuento.persona_beneficio_id = PersonaBeneficio.id
	AND PersonaBeneficio.codigo_beneficio = vORGANISMO
	AND OrdenDescuento.permanente = 1
	AND OrdenDescuento.activo = 1;


set @orden_descuento_cuota_id = null;
select odc.id INTO @orden_descuento_cuota_id from orden_descuento_cuotas odc, persona_beneficios be 
		where odc.orden_descuento_id = vORDEN_ID and odc.periodo = vPERIODO
		and odc.persona_beneficio_id = be.id    
		and odc.tipo_cuota = 'MUTUTCUOCSOC' and odc.estado <> 'B'
		and be.codigo_beneficio = vORGANISMO;

select decimal_1,logico_1 into @importe_cuota_social,@liquida_solo_deuda 
from global_datos where id = concat('MUTUCUOS',substring(vORGANISMO,9,4))
and logico_1 = 1;

SELECT ifnull(decimal_1,0) into @cuota_empresa from global_datos where id = vCODIGO_EMPRESA;
IF @cuota_empresa <> 0 THEN SET @importe_cuota_social = @cuota_empresa; END IF;

IF substring(vORGANISMO,9,2) = '66' THEN
	SELECT importe_cuota_social into @importe_cuota_social FROM socios WHERE id = vSOCIO_ID;
END IF;

-- ------------------------------------------------------------------
-- ANALIZO LA CUOTA SOCIAL DIFERENCIADA
-- ------------------------------------------------------------------
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
	SELECT cuota_social_diferenciada INTO @cuota_social_diferenciada FROM mutual_productos where tipo_producto = vTIPO_PRODUCTO
    and proveedor_id = vPROVEEDOR_ID ORDER BY cuota_social_diferenciada DESC LIMIT 1;
END IF;

IF @cuota_social_diferenciada <> 0 THEN
	SET @importe_cuota_social = @cuota_social_diferenciada;
END IF;


IF @liquida_solo_deuda = TRUE AND
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
    SET @importe_cuota_social = 0;
END IF;


IF @importe_cuota_social > 0 THEN

	CALL SP_VENCIMIENTOS(NULL,
	vPROVEEDOR_ID,vORGANISMO,vPERIODO,vFECHA,
	@PERIODO_INI,@VTO_SOCIO,@VTO_PROVEEDOR,@ULTIMO_PERIODO);
    
    
    IF @orden_descuento_cuota_id IS NOT NULL AND vORDEN_ID IS NOT NULL THEN 
		UPDATE orden_descuento_cuotas 
        SET importe = @importe_cuota_social 
        WHERE id = @orden_descuento_cuota_id;
    ELSE 
    
		IF vORDEN_ID IS NOT NULL THEN
			INSERT INTO orden_descuento_cuotas(orden_descuento_id, 
			socio_id, persona_beneficio_id, tipo_orden_dto, 
			tipo_producto, tipo_cuota, periodo, estado, situacion, 
			vencimiento, vencimiento_proveedor, 
			nro_cuota, importe, proveedor_id, 
			nro_referencia_proveedor) 
			VALUES(vORDEN_ID,vSOCIO_ID,vBENEFICIO_ID,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,
			vPERIODO,'A','MUTUSICUMUTU',@VTO_SOCIO,@VTO_PROVEEDOR,0,
			@importe_cuota_social,vPROVEEDOR_ID,vNRO_REFERENCIA_PROVEEDOR);
        END IF;
    END IF;

	/*
	SELECT @orden_descuento_cuota_id,@importe_cuota_social,vCODIGO_ORGANISMO,vORDEN_ID,
	vFECHA,vPERIODO_HASTA,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,vBENEFICIO_ID,vPROVEEDOR_ID,
	vNRO_REFERENCIA_PROVEEDOR,vIMPORTE_ORDEN_CUOTA,vACTIVO,
	@VTO_SOCIO,@VTO_PROVEEDOR,@ULTIMO_PERIODO;
	*/

END IF;

    

    
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU`(
vSOCIO_ID INT(11),
vPERIODO VARCHAR(6),
vORGANISMO VARCHAR(12),
vPRE_IMPUTACION BOOLEAN
)
BEGIN

-- CALL SP_LIQUIDA_DEUDA(97,'201502','MUTUCORG2202',127,FALSE,'MUTUSICUMUTU');
DECLARE vLIQUIDACION_ID INT(11);
SELECT id into vLIQUIDACION_ID FROM liquidaciones where periodo = vPERIODO 
and codigo_organismo = vORGANISMO;

-- //////////////////////////////////////////////////////////////////////
-- BORRO LA LIQUIDACION ANTERIOR
-- //////////////////////////////////////////////////////////////////////

delete from mutual_adicional_pendientes where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
delete from liquidacion_cuotas where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
delete from liquidacion_socios where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;

-- //////////////////////////////////////////////////////////////////////
-- GENERO LA CUOTA SOCIAL y CUOTA SERVICIOS
-- //////////////////////////////////////////////////////////////////////
CALL SP_LIQUIDA_CUOTA_SOCIAL(vSOCIO_ID,vPERIODO,vORGANISMO);
CALL SP_LIQUIDA_CUOTA_SERVICIOS(vSOCIO_ID,vPERIODO,vORGANISMO);

-- //////////////////////////////////////////////////////////////////////
-- VERIFICAR QUE NO TENGA STOP DEBIT
-- //////////////////////////////////////////////////////////////////////

select calificacion into @CALIFICACION from socio_calificaciones where socio_id = vSOCIO_ID order by created desc limit 1;
SET @STOP_DEBIT = IF(IFNULL(@CALIFICACION,'') = 'MUTUCALISDEB',TRUE,FALSE);

-- //////////////////////////////////////////////////////////////////////
-- SACO LAS CUOTAS ADEUDADAS
-- //////////////////////////////////////////////////////////////////////
IF vPRE_IMPUTACION = FALSE THEN
	insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,
	orden_descuento_id,orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,
	tipo_cuota,periodo_cuota,proveedor_id,vencida,importe,
	saldo_actual,codigo_organismo)
	SELECT 
		vLIQUIDACION_ID,
		OrdenDescuentoCuota.socio_id,
		OrdenDescuentoCuota.persona_beneficio_id,
		OrdenDescuentoCuota.orden_descuento_id,
		OrdenDescuentoCuota.id,
		OrdenDescuentoCuota.tipo_orden_dto,
		OrdenDescuentoCuota.tipo_producto,
		OrdenDescuentoCuota.tipo_cuota,
		OrdenDescuentoCuota.periodo,
		OrdenDescuentoCuota.proveedor_id,
		IF(OrdenDescuentoCuota.periodo = vPERIODO, 0 , 1) as vencida,
		OrdenDescuentoCuota.importe,
		OrdenDescuentoCuota.importe - IFNULL((SELECT SUM(cocu.importe)
		FROM orden_descuento_cobro_cuotas cocu
		WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0) AS saldo_actual,    
		PersonaBeneficio.codigo_beneficio
	  
	FROM orden_descuento_cuotas AS OrdenDescuentoCuota
	INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
	WHERE 
		OrdenDescuentoCuota.socio_id = vSOCIO_ID
		AND OrdenDescuentoCuota.estado <> 'B' 
		AND PersonaBeneficio.codigo_beneficio = vORGANISMO
		AND OrdenDescuentoCuota.periodo <= vPERIODO
		-- AND OrdenDescuentoCuota.situacion = vSITUACION
		AND OrdenDescuentoCuota.importe > IFNULL((SELECT SUM(cocu.importe)
		FROM orden_descuento_cobro_cuotas cocu, orden_descuento_cobros co
		WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
		AND cocu.orden_descuento_cobro_id = co.id
		AND co.periodo_cobro <= vPERIODO),0)
        AND @STOP_DEBIT = FALSE;
        
ELSE
	insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,
	orden_descuento_id,orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,
	tipo_cuota,periodo_cuota,proveedor_id,vencida,importe,
	saldo_actual,codigo_organismo)
	SELECT 
		vLIQUIDACION_ID,
		OrdenDescuentoCuota.socio_id,
		OrdenDescuentoCuota.persona_beneficio_id,
		OrdenDescuentoCuota.orden_descuento_id,
		OrdenDescuentoCuota.id,
		OrdenDescuentoCuota.tipo_orden_dto,
		OrdenDescuentoCuota.tipo_producto,
		OrdenDescuentoCuota.tipo_cuota,
		OrdenDescuentoCuota.periodo,
		OrdenDescuentoCuota.proveedor_id,
		IF(OrdenDescuentoCuota.periodo = vPERIODO, 0 , 1) as vencida,
		OrdenDescuentoCuota.importe,
		OrdenDescuentoCuota.importe - IFNULL((SELECT SUM(cocu.importe)
		FROM orden_descuento_cobro_cuotas cocu
		WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0) 
        -
        (SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas 
		WHERE orden_descuento_cuota_id = OrdenDescuentoCuota.id
		AND para_imputar = 1 AND imputada = 0 AND orden_descuento_cobro_id = 0
		order by liquidacion_id desc limit 1)
        AS saldo_actual,    
		PersonaBeneficio.codigo_beneficio
	  
	FROM orden_descuento_cuotas AS OrdenDescuentoCuota
	INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
	WHERE 
		OrdenDescuentoCuota.socio_id = vSOCIO_ID
		AND OrdenDescuentoCuota.estado <> 'B' 
		AND PersonaBeneficio.codigo_beneficio = vORGANISMO
		AND OrdenDescuentoCuota.periodo <= vPERIODO
		-- AND OrdenDescuentoCuota.situacion = vSITUACION
		AND OrdenDescuentoCuota.importe > (IFNULL((SELECT SUM(cocu.importe)
		FROM orden_descuento_cobro_cuotas cocu, orden_descuento_cobros co
		WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
		AND cocu.orden_descuento_cobro_id = co.id
		AND co.periodo_cobro <= vPERIODO),0) + (SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas 
		WHERE orden_descuento_cuota_id = OrdenDescuentoCuota.id
		AND para_imputar = 1 AND imputada = 0 AND orden_descuento_cobro_id = 0
		order by liquidacion_id desc limit 1))
        AND @STOP_DEBIT = FALSE;
        
END IF;

-- /////////////////////////////////////////////////////////////////////
-- CALCULAR LOS ADICIONALES
-- /////////////////////////////////////////////////////////////////////   
CALL SP_LIQUIDA_DEUDA_CBU_ADICIONALES(vSOCIO_ID,vPERIODO,vORGANISMO,vLIQUIDACION_ID);

  
-- /////////////////////////////////////////////////////////////////////
-- GENERO LA CABECERA DE LA LIQUIDACION
-- ///////////////////////////////////////////////////////////////////// 
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
	sum(saldo_actual) as deuda,
	sum(saldo_actual) as importe_adebitar,
    '*** IMPORTE PERIODO ***'
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (
    PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id
    and PersonaBeneficio.codigo_beneficio = LiquidacionCuota.codigo_organismo)
    INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
	AND LiquidacionCuota.socio_id = vSOCIO_ID
	AND LiquidacionCuota.periodo_cuota = vPERIODO					
GROUP BY
	LiquidacionCuota.codigo_organismo,
	LiquidacionCuota.socio_id,
    PersonaBeneficio.turno_pago,
	PersonaBeneficio.cbu
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
    0,
	sum(saldo_actual) as deuda,
	sum(saldo_actual) as importe_adebitar,
    '*** IMPORTE MORA ***'
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (
    PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id
    and PersonaBeneficio.codigo_beneficio = LiquidacionCuota.codigo_organismo)
    INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
	AND LiquidacionCuota.socio_id = vSOCIO_ID
	AND LiquidacionCuota.periodo_cuota < vPERIODO					
GROUP BY
	LiquidacionCuota.codigo_organismo,
	LiquidacionCuota.socio_id,
    PersonaBeneficio.turno_pago,
	PersonaBeneficio.cbu;

-- /////////////////////////////////////////////////////////////////////
-- TRATAMIENTO DE LA MORA
-- ///////////////////////////////////////////////////////////////////// 
CALL SP_LIQUIDA_DEUDA_CBU_GESTION_MORA(vSOCIO_ID,vORGANISMO,vLIQUIDACION_ID);
CALL SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO(vSOCIO_ID,vORGANISMO,vLIQUIDACION_ID);
-- /////////////////////////////////////////////////////////////////////
-- VERIFICO ACUERDOS DE DEBITO
-- /////////////////////////////////////////////////////////////////////    
CALL SP_LIQUIDA_DEUDA_CBU_ACUERDO_DEBITO(vSOCIO_ID,vLIQUIDACION_ID);
CALL SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO_RENUMERA(vSOCIO_ID,vLIQUIDACION_ID);
CALL SP_LIQUIDA_DEUDA_SCORING(vSOCIO_ID,vLIQUIDACION_ID);
    
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_ACUERDO_DEBITO`(
vSOCIO_ID INT(11),
vLIQUIDACION_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;

DECLARE vMONTO_ACUERDO DECIMAL(10,2) DEFAULT 0;
DECLARE vMONTO_MAX_DTO_BENEFICIO DECIMAL(10,2) DEFAULT 0;
DECLARE vCON_ACUERDO BOOLEAN DEFAULT FALSE;
DECLARE vBENEFICIO_ID INT(11) DEFAULT 0;

DECLARE vSALDO DECIMAL(10,2);
DECLARE vSALDO_ACUMULADO DECIMAL(10,2);

DECLARE c_beneficios_reset_acuerdo CURSOR FOR 
select PersonaBeneficio.id from persona_beneficios as PersonaBeneficio
where acuerdo_debito > (select sum(saldo_actual)        
from liquidacion_cuotas
where liquidacion_cuotas.liquidacion_id = vLIQUIDACION_ID and 
liquidacion_cuotas.socio_id = vSOCIO_ID and 
liquidacion_cuotas.persona_beneficio_id = PersonaBeneficio.id)
group by PersonaBeneficio.id;


DECLARE c_beneficios_acuerdo CURSOR FOR 
SELECT PersonaBeneficio.id,PersonaBeneficio.acuerdo_debito,
PersonaBeneficio.importe_max_registro_cbu 
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (
    PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id
    and PersonaBeneficio.codigo_beneficio = LiquidacionCuota.codigo_organismo)
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
	AND LiquidacionCuota.socio_id = vSOCIO_ID
	AND PersonaBeneficio.acuerdo_debito <> 0
    GROUP BY PersonaBeneficio.id;
-- ////////////////////////////////////////////////////////////////////////
-- ACUERDO DE DEBITO
-- ////////////////////////////////////////////////////////////////////////
DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row = 1;
-- DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row_2 = 1;

-- SET @LIMITE_1 = @LIMITE_2 = @TOPE_DEBITO_XREC = @TOPE_DEBITO_MAXIMO = @IMPORTE_MINIMO_REGISTRO = 0;
-- CALL SP_LIQUIDA_DEUDA_CBU_LIMITES(vORGANISMO,@LIMITE_1,@LIMITE_2,@TOPE_DEBITO_XREC,@TOPE_DEBITO_MAXIMO,@IMPORTE_MINIMO_REGISTRO);



OPEN c_beneficios_reset_acuerdo;
c1_loop_beneficios: LOOP
FETCH c_beneficios_reset_acuerdo INTO vBENEFICIO_ID;
		IF (l_last_row = 1) THEN
			LEAVE c1_loop_beneficios; 
		END IF;
        UPDATE persona_beneficios set acuerdo_debito = 0 where id = vBENEFICIO_ID;       
END LOOP c1_loop_beneficios; 
CLOSE c_beneficios_reset_acuerdo;

SET vSALDO = 0;
SET vSALDO_ACUMULADO = 0;
SET @REGISTRO = 1;
SET l_last_row = 0;
OPEN c_beneficios_acuerdo;
c1_loop_beneficios_acuerdo: LOOP
FETCH c_beneficios_acuerdo INTO vBENEFICIO_ID,vMONTO_ACUERDO,vMONTO_MAX_DTO_BENEFICIO;
		
        IF (l_last_row = 1) THEN
			LEAVE c1_loop_beneficios_acuerdo; 
		END IF;

		SET @ULTIMO_ID = 0;
        SET vSALDO = 0;

		DELETE FROM liquidacion_socios where 
		liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID and
		persona_beneficio_id = vBENEFICIO_ID;        



		IF CAST(vMONTO_ACUERDO / vMONTO_MAX_DTO_BENEFICIO AS UNSIGNED) > 1 AND vMONTO_MAX_DTO_BENEFICIO > 0 THEN
        
			SET vSALDO = vMONTO_ACUERDO;
            WHILE vSALDO > @IMPORTE_MINIMO_REGISTRO DO
            
				SET @IMPO_DEBITO = vMONTO_MAX_DTO_BENEFICIO;
				
				IF vSALDO <= vMONTO_MAX_DTO_BENEFICIO THEN
					SET @IMPO_DEBITO = vSALDO;
				END IF;            
            
				
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
					IF(@REGISTRO = 1,vMONTO_ACUERDO,0) as deuda,
					@IMPO_DEBITO,
					concat('*** CON ACUERDO DE DEBITO ', vMONTO_ACUERDO ,' | FRACCION = ',@IMPO_DEBITO,' ***')
				FROM 
					liquidacion_cuotas as LiquidacionCuota 
					INNER JOIN persona_beneficios as PersonaBeneficio on (
					PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id
					and PersonaBeneficio.codigo_beneficio = LiquidacionCuota.codigo_organismo)
					INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
				WHERE 
					LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
					AND LiquidacionCuota.socio_id = vSOCIO_ID
					AND PersonaBeneficio.id = vBENEFICIO_ID
                GROUP BY PersonaBeneficio.id;    
                
				SET @ULTIMO_ID = LAST_INSERT_ID(); 
            
				-- SELECT vBENEFICIO_ID,vMONTO_ACUERDO,vMONTO_MAX_DTO_BENEFICIO,vSALDO,vSALDO_ACUMULADO;
                
                SET vSALDO_ACUMULADO = vSALDO_ACUMULADO + @IMPO_DEBITO;
				
                SET vSALDO = vSALDO - @IMPO_DEBITO;
                SET @REGISTRO = @REGISTRO + 1;  
            
            END WHILE;
            
			IF vSALDO > 0 THEN
				UPDATE liquidacion_socios 
				SET importe_adebitar = importe_adebitar + vSALDO WHERE id = @ULTIMO_ID; 
			END IF;             
        
        ELSE
        
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
				PersonaBeneficio.acuerdo_debito,
				concat('*** CON ACUERDO DE DEBITO = ',vMONTO_ACUERDO,' ***')
			FROM 
				liquidacion_cuotas as LiquidacionCuota 
				INNER JOIN persona_beneficios as PersonaBeneficio on (
				PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id
				and PersonaBeneficio.codigo_beneficio = LiquidacionCuota.codigo_organismo)
				INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
			WHERE 
				LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
				AND LiquidacionCuota.socio_id = vSOCIO_ID
				AND PersonaBeneficio.id = vBENEFICIO_ID;        
        
        END IF;

        
END LOOP c1_loop_beneficios_acuerdo;  
CLOSE c_beneficios_acuerdo;       
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_ADICIONALES`(
	vSOCIO_ID INT(11),
	vPERIODO VARCHAR(6),
	vORGANISMO VARCHAR(12),
	vLIQUIDACION_ID INT(11)
)
BEGIN
-- CALL SP_LIQUIDA_DEUDA_ADICIONALES(97,'201502','MUTUCORG2202',127);
DECLARE l_last_row INT DEFAULT 0;
DECLARE vPROVEEDOR_ID INT(11) DEFAULT 0;
DECLARE vIMPUTAR_PROVEEDOR_ID INT(11) DEFAULT 0;
DECLARE vTIPO CHAR(1);
DECLARE vVALOR DECIMAL(10,2);
DECLARE vDEVENGA BOOLEAN;
DECLARE vACTIVO BOOLEAN;
DECLARE vCALCULO INT(11);
DECLARE vTIPO_CUOTA VARCHAR(12);

DECLARE vORDEN_DTO_ID INT(11);
DECLARE vBENEFICIO_ID INT(11);

DECLARE c_adicionales CURSOR FOR 
select proveedor_id,imputar_proveedor_id,
tipo,valor,devengado_previo,deuda_calcula,tipo_cuota,activo
from mutual_adicionales
where codigo_organismo = vORGANISMO
and valor > 0
and ifnull(periodo_desde,'000000') <= vPERIODO
and ifnull(periodo_hasta,'999912') >= vPERIODO;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;

SET vORDEN_DTO_ID = NULL;
SET vBENEFICIO_ID = NULL;

OPEN c_adicionales;
c1_loop: LOOP
	FETCH c_adicionales INTO vPROVEEDOR_ID,vIMPUTAR_PROVEEDOR_ID,vTIPO,vVALOR,vDEVENGA,vCALCULO,vTIPO_CUOTA,vACTIVO;
		
        IF (l_last_row = 1) THEN
			LEAVE c1_loop; 
		END IF;	    
        
        
		-- select vPROVEEDOR_ID,vIMPUTAR_PROVEEDOR_ID,vTIPO,vVALOR,vDEVENGA,vCALCULO,vTIPO_CUOTA;
		
        IF (vVALOR <> 0) THEN
        
			set @saldo = 0;
            set @ordenDtoId = 0;
            set @beneficioId = 0;
            set @tipoProducto = null;
        
			-- SACAR LA ORDEN DE DESCUENTO A DONDE SE VA A CARGAR EN BASE AL PROVEEDOR AL CUAL SE IMPUTA
			set @STMT = CONCAT('select persona_beneficio_id,(select id from orden_descuentos where tipo_orden_dto = \'CMUTU\' and activo = 1 and socio_id = liquidacion_cuotas.socio_id order by id desc limit 1) as orden_id,(select tipo_producto from orden_descuentos where tipo_orden_dto = \'CMUTU\' and activo = 1 and socio_id = liquidacion_cuotas.socio_id order by id desc limit 1) as tipo_producto,sum(saldo_actual) into @beneficioId,@ordenDtoId,@tipoProducto,@saldo FROM liquidacion_cuotas where liquidacion_id = ? and socio_id = ? and ifnull(mutual_adicional_pendiente_id,0) = 0');
			
            IF vPROVEEDOR_ID is not null THEN
				SET @STMT = CONCAT(@STMT,' AND proveedor_id = ? ');             
			END IF;
            IF vCALCULO = 1 THEN
				SET @STMT = CONCAT(@STMT,' AND periodo_cuota <= ? '); 
            END IF;
            IF vCALCULO = 2 THEN
				SET @STMT = CONCAT(@STMT,' AND periodo_cuota < ? '); 
            END IF;            
            IF vCALCULO = 3 THEN
				SET @STMT = CONCAT(@STMT,' AND periodo_cuota = ? '); 
            END IF;

            PREPARE STMT FROM @STMT;
            
            SET @LIQ = vLIQUIDACION_ID;
            SET @SOCIO = vSOCIO_ID;
            SET @PERIODO = vPERIODO;
            
            EXECUTE STMT USING @LIQ,@SOCIO,@PERIODO;
            
            DEALLOCATE PREPARE STMT;
			SET @ADICIONAL = 0;
			IF vVALOR <> 0 AND @saldo <> 0 THEN
				SET @ADICIONAL = IF(vTIPO = 'P',ROUND(@saldo * vVALOR / 100,2),vVALOR);
            END IF;
            -- SELECT @ADICIONAL;
			IF @ADICIONAL <> 0 THEN
                
                IF vDEVENGA = 0 AND vACTIVO = TRUE THEN
                
					delete from liquidacion_cuotas where liquidacion_id = vLIQUIDACION_ID
                    and socio_id = vSOCIO_ID and mutual_adicional_pendiente_id <> 0;
                    
                    delete from mutual_adicional_pendientes where liquidacion_id = vLIQUIDACION_ID
                    and socio_id = vSOCIO_ID;
                
					insert into mutual_adicional_pendientes(liquidacion_id,socio_id,codigo_organismo,proveedor_id,
					tipo,deuda_calcula,valor,tipo_cuota,periodo,total_deuda,importe,
					orden_descuento_id,persona_beneficio_id)
					values(vLIQUIDACION_ID,vSOCIO_ID,vORGANISMO,vIMPUTAR_PROVEEDOR_ID,
					vTIPO,vCALCULO,vVALOR,vTIPO_CUOTA,vPERIODO,@saldo,@ADICIONAL,@ordenDtoId,@beneficioId);
					
					set @adicional_id = last_insert_id();
					
					insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,orden_descuento_id,
							orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,tipo_cuota,
							periodo_cuota,proveedor_id,vencida,importe,saldo_actual,codigo_organismo,
							mutual_adicional_pendiente_id
					)
					values(vLIQUIDACION_ID,vSOCIO_ID,@beneficioId,
					@ordenDtoId,null,'CMUTU',@tipoProducto,vTIPO_CUOTA,
					vPERIODO,vIMPUTAR_PROVEEDOR_ID,0,@ADICIONAL,@ADICIONAL,vORGANISMO,@adicional_id);
                    
                ELSE 
                
					-- CREAR LA CUOTA
                    set @cantidad = 0;
                    select count(OrdenDescuentoCuota.id) into @cantidad_1 from orden_descuento_cuotas OrdenDescuentoCuota
                            where OrdenDescuentoCuota.socio_id = vSOCIO_ID
                            and OrdenDescuentoCuota.orden_descuento_id = @ordenDtoId
                            and OrdenDescuentoCuota.persona_beneficio_id = @beneficioId
                            and OrdenDescuentoCuota.proveedor_id = vIMPUTAR_PROVEEDOR_ID
                            and OrdenDescuentoCuota.periodo = vPERIODO
                            and OrdenDescuentoCuota.tipo_cuota = vTIPO_CUOTA
                            and OrdenDescuentoCuota.id in (select orden_descuento_cuota_id from
                            orden_descuento_cobro_cuotas cocu inner join orden_descuento_cobros co
                            on (co.id = cocu.orden_descuento_cobro_id) where co.socio_id = vSOCIO_ID);

                    
                    if @cantidad = 0 then
                    
						DELETE FROM liquidacion_cuotas where liquidacion_id = vLIQUIDACION_ID
                        and socio_id = vSOCIO_ID and orden_descuento_cuota_id in (
							select OrdenDescuentoCuota.id from orden_descuento_cuotas OrdenDescuentoCuota
							where OrdenDescuentoCuota.socio_id = vSOCIO_ID
							and OrdenDescuentoCuota.orden_descuento_id = @ordenDtoId
							and OrdenDescuentoCuota.persona_beneficio_id = @beneficioId
							and OrdenDescuentoCuota.proveedor_id = vIMPUTAR_PROVEEDOR_ID
							and OrdenDescuentoCuota.periodo = vPERIODO
							and OrdenDescuentoCuota.tipo_cuota = vTIPO_CUOTA
							and OrdenDescuentoCuota.id not in (select orden_descuento_cuota_id from
							orden_descuento_cobro_cuotas cocu inner join orden_descuento_cobros co
							on (co.id = cocu.orden_descuento_cobro_id) where co.socio_id = vSOCIO_ID)
                        );
                    
						delete OrdenDescuentoCuota.* from orden_descuento_cuotas OrdenDescuentoCuota
						where OrdenDescuentoCuota.socio_id = vSOCIO_ID
						and OrdenDescuentoCuota.orden_descuento_id = @ordenDtoId
						and OrdenDescuentoCuota.persona_beneficio_id = @beneficioId
						and OrdenDescuentoCuota.proveedor_id = vIMPUTAR_PROVEEDOR_ID
						and OrdenDescuentoCuota.periodo = vPERIODO
						and OrdenDescuentoCuota.tipo_cuota = vTIPO_CUOTA
						and OrdenDescuentoCuota.id not in (select orden_descuento_cuota_id from
						orden_descuento_cobro_cuotas cocu inner join orden_descuento_cobros co
						on (co.id = cocu.orden_descuento_cobro_id) where co.socio_id = vSOCIO_ID);
                    
						IF vACTIVO = TRUE THEN
                    
							INSERT INTO orden_descuento_cuotas(orden_descuento_id, 
							socio_id, persona_beneficio_id, tipo_orden_dto, 
							tipo_producto, tipo_cuota, periodo,vencimiento, vencimiento_proveedor, 
							nro_cuota, importe, proveedor_id) 
							VALUES(@ordenDtoId,vSOCIO_ID,@beneficioId,'CMUTU',@tipoProducto,
							vTIPO_CUOTA,vPERIODO,now(),now(),0,@ADICIONAL,vIMPUTAR_PROVEEDOR_ID);                    
						
							insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,orden_descuento_id,
									orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,tipo_cuota,
									periodo_cuota,proveedor_id,vencida,importe,saldo_actual,codigo_organismo
							)
							values(vLIQUIDACION_ID,vSOCIO_ID,@beneficioId,
							@ordenDtoId,last_insert_id(),'CMUTU',@tipoProducto,vTIPO_CUOTA,
							vPERIODO,vIMPUTAR_PROVEEDOR_ID,0,@ADICIONAL,@ADICIONAL,vORGANISMO);
                     
						END IF;
                    
                    end if;
                    
                    
                   
				END IF;
                -- select @saldo,@ADICIONAL,@orden_dto_id,@beneficio_id;
                
            END IF;

        END IF;

END LOOP c1_loop;
CLOSE c_adicionales;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_GESTION_MORA`(
vSOCIO_ID INT(11),
vORGANISMO VARCHAR(12),
vLIQUIDACION_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
DECLARE vID INT(11);
DECLARE vIMPORTE_DEBITO DECIMAL(10,2);
DECLARE vIMPORTE_DTO DECIMAL(10,2);
DECLARE vIMPORTE_DEBITO_CALCULADO DECIMAL(10,2);
DECLARE vFORMULA TEXT;
DECLARE cursor_mora CURSOR FOR 
SELECT id,importe_adebitar,importe_dto FROM liquidacion_socios
where liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID
and periodo = 0;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;



SET @LIMITE_1 = 100;
SET @LIMITE_2 = 200;

SET @TOPE_DEBITO_MAXIMO = 800;

SET vID = 0;
SET vIMPORTE_DEBITO = 0;
SET vIMPORTE_DTO = 0;
SET vIMPORTE_DEBITO_CALCULADO = 0;

/*
SELECT entero_1,entero_2,decimal_2 INTO @LIMITE_1,@LIMITE_2,@TOPE_DEBITO_MAXIMO
FROM global_datos WHERE id = vORGANISMO;
*/
SET @LIMITE_1 = @LIMITE_2 = @TOPE_DEBITO_XREC = @TOPE_DEBITO_MAXIMO = @IMPORTE_MINIMO_REGISTRO = 0;
CALL SP_LIQUIDA_DEUDA_CBU_LIMITES(vORGANISMO,@LIMITE_1,@LIMITE_2,@TOPE_DEBITO_MAXIMO,@TOPE_DEBITO_XREC,@IMPORTE_MINIMO_REGISTRO);
-- SELECT @LIMITE_1,@LIMITE_2,@TOPE_DEBITO_XREC,@TOPE_DEBITO_MAXIMO,@IMPORTE_MINIMO_REGISTRO;

OPEN cursor_mora;
c1_loop: LOOP
	FETCH cursor_mora INTO vID,vIMPORTE_DEBITO,vIMPORTE_DTO;
    
	IF (l_last_row = 1) THEN
		LEAVE c1_loop; 
	END IF;	      
    SET vFORMULA = '';
    SET vIMPORTE_DEBITO_CALCULADO = vIMPORTE_DEBITO;
    
    IF @LIMITE_1 >= vIMPORTE_DEBITO THEN
		SET vIMPORTE_DEBITO_CALCULADO = vIMPORTE_DEBITO;
        SET vFORMULA = CONCAT(vIMPORTE_DEBITO,' <' , @LIMITE_1,' ==> IMPORTE A DEBITAR: ',vIMPORTE_DEBITO_CALCULADO,' (TOTAL ATRASO)');
    END IF;
    

    IF @LIMITE_1 < vIMPORTE_DEBITO AND vIMPORTE_DEBITO <= @LIMITE_2 THEN
		SET vIMPORTE_DEBITO_CALCULADO = vIMPORTE_DEBITO / 2;
        SET vFORMULA = CONCAT(@LIMITE_1,' < ',vIMPORTE_DEBITO,' <= ' , @LIMITE_2,' ==> IMPORTE A DEBITAR: ',vIMPORTE_DEBITO_CALCULADO,' (TOTAL ATRASO / 2)');
    END IF;
    
    IF vIMPORTE_DEBITO > @LIMITE_2 THEN
		SET vIMPORTE_DEBITO_CALCULADO = vIMPORTE_DEBITO / 3;
        SET vFORMULA = CONCAT(vIMPORTE_DEBITO,' > ' , @LIMITE_2,' ==> IMPORTE A DEBITAR: ',vIMPORTE_DEBITO_CALCULADO,' (TOTAL ATRASO / 3)');
    END IF; 
    
    -- CONTROL DEL TOPE DE DEBITO CBU
    IF @TOPE_DEBITO_MAXIMO < vIMPORTE_DEBITO_CALCULADO THEN
		SET vIMPORTE_DEBITO_CALCULADO = @TOPE_DEBITO_MAXIMO;
        SET vFORMULA = CONCAT(vFORMULA,'\nCONTROL MONTO MAXIMO DEBITO CBU == > IMPORTE A DEBITAR: ',vIMPORTE_DEBITO_CALCULADO);
    END IF;

	update liquidacion_socios 
    set importe_adebitar = vIMPORTE_DEBITO_CALCULADO,
    importe_dto = vIMPORTE_DEBITO_CALCULADO,
    formula_criterio_deuda = vFORMULA where id = vID;
      
END LOOP c1_loop;

CLOSE cursor_mora;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_LIMITES`(
IN vORGANISMO VARCHAR(12),
OUT vLIMITE_INFERIOR INT(11),
OUT vLIMITE_SUPERIOR INT(11),
OUT vIMPORTE_MAXIMO_DEBITO DECIMAL(10,2),
OUT vIMPORTE_MAXIMO_POR_REGISTRO DECIMAL(10,2),
OUT vIMPORTE_MINIMO_POR_REGISTRO INT(11)
)
BEGIN

SET vIMPORTE_MINIMO_POR_REGISTRO = 50;

SELECT entero_1,entero_2,decimal_1,decimal_2 
INTO vLIMITE_INFERIOR,vLIMITE_SUPERIOR,vIMPORTE_MAXIMO_POR_REGISTRO,vIMPORTE_MAXIMO_DEBITO
FROM global_datos WHERE id = vORGANISMO;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO`(
vSOCIO_ID INT(11),
vORGANISMO VARCHAR(12),
vLIQUIDACION_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
DECLARE vID INT(11);
DECLARE vBENEFICIO_ID INT(11);
DECLARE vIMPORTE_DEBITO DECIMAL(10,2);
DECLARE vIMPORTE_DEBITO_CALCULADO DECIMAL(10,2);
DECLARE vSALDO DECIMAL(10,2);
DECLARE vSALDO_ACUMULADO DECIMAL(10,2);
DECLARE vFORMULA TEXT;
DECLARE cursor_socio CURSOR FOR 
SELECT id,persona_beneficio_id,importe_adebitar FROM liquidacion_socios
where liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID
order by periodo;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;

SET @TOPE_POR_REGISTRO = 0;
SET vSALDO = 0;
SET vSALDO_ACUMULADO = 0;


-- SELECT decimal_1 INTO @TOPE_POR_REGISTRO
-- FROM global_datos WHERE id = vORGANISMO;

SET @LIMITE_1 = @LIMITE_2 = @TOPE_DEBITO_XREC = @TOPE_DEBITO_MAXIMO = @IMPORTE_MINIMO_REGISTRO = 0;
CALL SP_LIQUIDA_DEUDA_CBU_LIMITES(vORGANISMO,@LIMITE_1,@LIMITE_2,@TOPE_DEBITO_MAXIMO,@TOPE_POR_REGISTRO,@IMPORTE_MINIMO_REGISTRO);
-- SELECT @LIMITE_1,@LIMITE_2,@TOPE_DEBITO_XREC,@TOPE_DEBITO_MAXIMO,@IMPORTE_MINIMO_REGISTRO;

SET vID = 0;
SET vBENEFICIO_ID = 0;
SET vIMPORTE_DEBITO = 0;

OPEN cursor_socio;
c1_loop: LOOP
	FETCH cursor_socio INTO vID,vBENEFICIO_ID,vIMPORTE_DEBITO;
    
	IF (l_last_row = 1) THEN
		LEAVE c1_loop; 
	END IF;	
    
    SET @IMPO_MAX_DBTO_BENEFICIO = 0;
    SET vSALDO = 0;
    SET @IMPO_DEBITO = 0;
    SET @ULTIMO_ID = 0;
    
    SELECT importe_max_registro_cbu into @IMPO_MAX_DBTO_BENEFICIO 
    FROM persona_beneficios where id = vBENEFICIO_ID;
    
    IF @TOPE_POR_REGISTRO > @IMPO_MAX_DBTO_BENEFICIO AND @IMPO_MAX_DBTO_BENEFICIO > 0 THEN
		SET @TOPE_POR_REGISTRO = @IMPO_MAX_DBTO_BENEFICIO;
    END IF;
    
    IF @TOPE_POR_REGISTRO <> 0 THEN
		
        SET vSALDO = vIMPORTE_DEBITO;
        
        SET @REGISTRO = 1;
        -- SET vIMPORTE_DEBITO_CALCULADO = vIMPORTE_DEBITO / @CICLOS;
        
        WHILE vSALDO > @IMPORTE_MINIMO_REGISTRO DO
        
			SET vSALDO_ACUMULADO = vSALDO_ACUMULADO + @TOPE_POR_REGISTRO;
            
            SET @IMPO_DEBITO = @TOPE_POR_REGISTRO;
            
            IF vSALDO < @TOPE_POR_REGISTRO THEN
				SET @IMPO_DEBITO = vSALDO;
            END IF;
            
            SET @IMPO_DEBITO = CAST(@IMPO_DEBITO AS DECIMAL(10,2));
            
            IF @REGISTRO = 1 THEN
			
				UPDATE liquidacion_socios 
				SET importe_adebitar = @IMPO_DEBITO,
                -- ,formula_criterio_deuda = concat(formula_criterio_deuda,'\n*** FRACCION: ',@IMPO_DEBITO,' ***'),
				registro = @REGISTRO WHERE id = vID; 
                
                SET @ULTIMO_ID = vID;
            
            ELSE
        
				insert into liquidacion_socios(liquidacion_id,socio_id,
				persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
				nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
				codigo_empresa,codigo_reparticion,turno_pago,periodo,importe_adebitar,
				formula_criterio_deuda)			
				SELECT liquidacion_id,socio_id,
				persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
				nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
				codigo_empresa,codigo_reparticion,turno_pago,periodo,@IMPO_DEBITO,
                formula_criterio_deuda
				-- concat(formula_criterio_deuda,'\n*** ACUERDO S/FRACCION: ',@TOPE_POR_REGISTRO,' ***')
                FROM liquidacion_socios where id = vID;
                
                SET @ULTIMO_ID = LAST_INSERT_ID();
			
            END IF;
			
            SET vSALDO = vSALDO - @IMPO_DEBITO;
            
			-- SELECT vID,vIMPORTE_DEBITO,@TOPE_POR_REGISTRO,@IMPO_DEBITO,vSALDO;
            
            SET @REGISTRO = @REGISTRO + 1;        
        
        END WHILE;
        
        IF vSALDO < @IMPORTE_MINIMO_REGISTRO THEN
            UPDATE liquidacion_socios 
			SET importe_adebitar = importe_adebitar + vSALDO WHERE id = @ULTIMO_ID;
        END IF;        
        
     END IF;
	
    
    
END LOOP c1_loop;

CLOSE cursor_socio;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO_RENUMERA`(
vSOCIO_ID INT(11),
vLIQUIDACION_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
DECLARE vID INT(11);
DECLARE cursor_socio CURSOR FOR 
SELECT id FROM liquidacion_socios
where liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID
order by periodo,importe_dto desc;

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
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LIQUIDA_DEUDA_SCORING`(IN
vSOCIO_ID INT(11),vLIQUIDACION_ID INT(11))
BEGIN

select periodo into @periodo from liquidaciones where id = vLIQUIDACION_ID;
delete from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID;
insert into liquidacion_socio_scores(liquidacion_id,socio_id,`13`,`12`,`09`,`06`,`03`,`00`,cargos_adicionales,saldo_actual)
select liquidacion_id,socio_id, 
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 12 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 12 month),'%Y%m')  
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 9 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 9 month),'%Y%m')
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 6 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota  > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 6 month),'%Y%m') 
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 3 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota  > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 3 month),'%Y%m') 
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 0 month),'%Y%m')
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 0 month),'%Y%m')
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id <> 0
and lc.socio_id = lc2.socio_id),0),
sum(saldo_actual) as saldo_actual
from liquidacion_cuotas lc2 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID
group by socio_id;

-- //// ASIGNO PUNTAJE
if cast((select `13` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 5 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `12` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 4 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `09` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 3 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `06` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 2 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `03` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 1 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `00` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 0 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
end if;
end if;
end if;
end if;
end if;
end if;


END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_POSICION_CONSOLIDADA`(
	vPID INT,
	vPer VARCHAR(6),
	vOrg VARCHAR(12),
	vProvId INT,
	vEmp VARCHAR(12),
	vTurno VARCHAR(50)
	)
BEGIN
	
	DECLARE vRows INT;
	DECLARE vCont INT;
	DECLARE done BOOLEAN DEFAULT FALSE;
	DECLARE v_last_row INT DEFAULT 0;
	DECLARE vTdocNdoc VARCHAR(50);
	DECLARE vApenom VARCHAR(255);
	DECLARE vSocioId INT(11) DEFAULT 0;
	
	DECLARE vSaldoActual DECIMAL;
	
	DECLARE cur_personas CURSOR FOR 
	SELECT 
	CONCAT(TRIM(GlobalDato.concepto_1),' ',TRIM(Persona.documento)),
	CONCAT(Persona.apellido,', ',Persona.nombre) AS apenom,
	Socio.id
	FROM personas AS Persona
	INNER JOIN socios AS Socio ON (Socio.persona_id = Persona.id)
	INNER JOIN global_datos AS GlobalDato ON(GlobalDato.id = Persona.tipo_documento)
	GROUP BY Socio.id
	ORDER BY Persona.apellido, Persona.nombre;
	
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	
	DELETE FROM asincrono_temporales WHERE asincrono_id = vPID;
	
	OPEN cur_personas;
	SET vRows = (SELECT FOUND_ROWS());
	SET vCont = 0;
	
	CALL STP_ASINCRONO(vPID,'P',vRows,vCont,'*** INICIANDO PROCESO ***');
	
	WHILE NOT isAsincronoStop(vPID) DO
	FETCH cur_personas INTO vTdocNdoc, vApenom, vSocioId;
	
		SET @query = CONCAT('	
			INSERT INTO asincrono_temporales
			(asincrono_id,clave_1,clave_2,clave_3,texto_1,texto_2,texto_3,
			texto_4,texto_5,texto_6,texto_7,
			decimal_1,decimal_2,
			decimal_3,decimal_4,entero_1,entero_2,entero_3,entero_4)
			SELECT ',vPID,',
			cuota.orden_descuento_id,
			cuota.proveedor_id,
			', vSocioId,',
			''',vTdocNdoc,''',''',vApenom,''',
			beneficio.codigo_beneficio,
			IF(SUBSTR(beneficio.codigo_beneficio,9,2) = 22,beneficio.codigo_empresa,''''),
			IF(SUBSTR(beneficio.codigo_beneficio,9,2) = 22,beneficio.turno_pago,''''),
			concat(orden.tipo_orden_dto,'' #'',orden.numero),
			IFNULL(cuota.nro_referencia_proveedor,0),
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''TOTAL_DEVENGADO'') AS TOTAL_DEVENGADO,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''SALDO_AVENCER'') AS SALDO_AVENCER,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''TOTAL_PAGADO'') AS TOTAL_PAGADO,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''SALDO_VENCIDO'') AS SALDO_VENCIDO,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''CUOTAS_DEVENGADAS'') AS CUOTAS_DEVENGADAS,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''CUOTAS_VENCIDAS'') AS CUOTAS_VENCIDAS,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''CUOTAS_AVENCER'') AS CUOTAS_AVENCER,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''CUOTAS_PAGAS'') AS CUOTAS_PAGAS  
			FROM orden_descuento_cuotas AS cuota 
			INNER JOIN persona_beneficios AS beneficio ON (beneficio.id = cuota.persona_beneficio_id)
			INNER JOIN orden_descuentos as orden on (orden.id = cuota.orden_descuento_id)
			WHERE 
				cuota.socio_id = ', vSocioId);
		IF vProvId IS NOT NULL THEN
			SET @query =  CONCAT(@query,' AND cuota.proveedor_id = ',vProvId);
		END IF;
		IF vOrg IS NOT NULL THEN
			SET @query =  CONCAT(@query,' AND beneficio.codigo_beneficio = ''',vOrg,'''');
		END IF;
		IF vEmp IS NOT NULL THEN
			SET @query =  CONCAT(@query,' AND beneficio.codigo_empresa = ''',vEmp,'''');
		END IF;
		IF vTurno IS NOT NULL THEN
			SET @query =  CONCAT(@query,' AND beneficio.turno_pago = ''',TRIM(vTurno),'''');
		END IF;						
		SET @query = CONCAT(@query,' GROUP BY cuota.orden_descuento_id,cuota.proveedor_id,cuota.persona_beneficio_id');	
	
	
		PREPARE smpt FROM @query;
		EXECUTE smpt;
		DEALLOCATE PREPARE smpt;
		DELETE FROM asincrono_temporales WHERE asincrono_id = vPID
		AND clave_3 = vSocioId AND decimal_4 = 0;
	
		CALL STP_ASINCRONO(vPID,'P',vRows,vCont,CONCAT('PROCESANDO: ',vSocioId,' *** ',vApenom));
	
		SELECT vCont + 1 INTO vCont;
		
	END WHILE;	
	CLOSE cur_personas;
	
	CALL STP_ASINCRONO(vPID,'F',vRows,vCont,'**** PROCESO FINALIZADO ***');
	
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_VENCIMIENTOS`(
IN vPERSONA_BENEFICIO_ID INT(11),
IN vPROVEEDOR_ID INT(11),
IN vCODIGO_ORGANISMO VARCHAR(12),
IN vPERIODO VARCHAR(6),
IN vFECHA DATE,
OUT vPERIODO_INI VARCHAR(6),
OUT vVENCIMIENTO_SOCIO DATE,
OUT vVENCIMIENTO_PROVEEDOR DATE,
OUT vULTIMO_PERIODO_LIQUIDADO VARCHAR(6)
)
BEGIN



IF vPERSONA_BENEFICIO_ID IS NOT NULL THEN
	SELECT codigo_beneficio into vCODIGO_ORGANISMO from persona_beneficios where id = vPERSONA_BENEFICIO_ID;
END IF;

SELECT periodo into vULTIMO_PERIODO_LIQUIDADO FROM liquidaciones 
where codigo_organismo = vCODIGO_ORGANISMO
and cerrada = 1 order by periodo desc limit 1;

select d_corte, d_vto_socio, d_vto_proveedor_suma, mes, 
m_ini_socio_ac_suma, m_ini_socio_dc_suma, m_vto_socio_suma 
INTO @d_corte, @d_vto_socio, @d_vto_proveedor_suma, @mes, 
@m_ini_socio_ac_suma, @m_ini_socio_dc_suma, @m_vto_socio_suma 
from proveedor_vencimientos 
where proveedor_id = vPROVEEDOR_ID and codigo_organismo = vCODIGO_ORGANISMO
and mes = cast(trim(substring(vPERIODO,5,2)) as char(2));

IF @d_corte IS NULL THEN
select d_corte INTO @d_corte from proveedor_vencimientos where proveedor_id = 18 and codigo_organismo = 'MUTUCORG2201'and mes = '01';
END IF;

IF @m_ini_socio_dc_suma IS NULL THEN
select m_ini_socio_dc_suma INTO @m_ini_socio_dc_suma from proveedor_vencimientos where proveedor_id = 18 and codigo_organismo = 'MUTUCORG2201'and mes = '01';
END IF;

IF @m_ini_socio_ac_suma IS NULL THEN
select m_ini_socio_ac_suma INTO @m_ini_socio_ac_suma from proveedor_vencimientos where proveedor_id = 18 and codigo_organismo = 'MUTUCORG2201'and mes = '01';
END IF;

SET @PERIODO_INICIO = DATE_ADD(vFECHA, INTERVAL IF(DAY(vFECHA) <= @d_corte,@m_ini_socio_ac_suma,@m_ini_socio_dc_suma) MONTH);
SET vPERIODO_INI = DATE_FORMAT(@PERIODO_INICIO,'%Y%m');

IF vPERIODO IS NULL THEN SET vVENCIMIENTO_SOCIO = DATE_ADD(STR_TO_DATE(CONCAT(DATE_FORMAT(@PERIODO_INICIO,'%Y-%m'),'-',@d_vto_socio),'%Y-%m-%d'),INTERVAL @m_vto_socio_suma MONTH);
ELSE 
	SET vVENCIMIENTO_SOCIO = STR_TO_DATE(CONCAT(vPERIODO,@d_vto_socio),'%Y%m%d'); 
    SET vPERIODO_INI = NULL; 
END IF;
IF DATE_FORMAT(vVENCIMIENTO_SOCIO,'%w') = 6 THEN SET vVENCIMIENTO_SOCIO = DATE_ADD(vVENCIMIENTO_SOCIO, INTERVAL 2 DAY);
END IF;
IF DATE_FORMAT(vVENCIMIENTO_SOCIO,'%w') = 0 THEN SET vVENCIMIENTO_SOCIO = DATE_ADD(vVENCIMIENTO_SOCIO, INTERVAL 1 DAY);
END IF;

SET vVENCIMIENTO_PROVEEDOR = DATE_ADD(vVENCIMIENTO_SOCIO, INTERVAL @d_vto_proveedor_suma DAY);

IF DATE_FORMAT(vVENCIMIENTO_PROVEEDOR,'%w') = 6 THEN SET vVENCIMIENTO_PROVEEDOR = DATE_ADD(vVENCIMIENTO_PROVEEDOR, INTERVAL 2 DAY);
END IF;
IF DATE_FORMAT(vVENCIMIENTO_PROVEEDOR,'%w') = 0 THEN SET vVENCIMIENTO_PROVEEDOR = DATE_ADD(vVENCIMIENTO_PROVEEDOR, INTERVAL 1 DAY);
END IF;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `STP_ASINCRONO`(vPID INT,vStatus VARCHAR(1),vTot INT, vCont INT, vMsg VARCHAR(255))
BEGIN
	DECLARE vPorc DECIMAL(10,2) DEFAULT 0;
	
	SET vPorc = ROUND((vCont / vTot) * 100,2);
	SET vMsg = CONCAT('[',vCont,'|',vTot,'] ',vMsg);
	
	UPDATE asincronos 
	SET estado = vStatus, total = vTot, contador = vCont, porcentaje = vPorc, msg = vMsg
	WHERE id = vPID;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `v_buscar_solicitudes_credito`()
BEGIN
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_REPORTE_PADRON_SERVICIOS`(
IN 
	vPID INT(11),
    vSOCIO_ID INT(11),
    vSERVICIO_ID INT(11),
    vFECHA_COBERTURA DATE,
    vCUOTAS_SOCIALES_MINIMAS INT(11)
)
BEGIN
		
		INSERT INTO asincrono_temporales(asincrono_id,clave_1,entero_1,texto_1,texto_2,
		texto_3,texto_4,texto_5,texto_6,texto_7,texto_8,
		texto_9,texto_10,texto_11,texto_12,texto_13,texto_14,texto_15,texto_16,decimal_1)
        
		select
			vPID,
			IF(MutualServicioSolicitud.fecha_baja_servicio IS NULL,'REPORTE_1','REPORTE_2') as tipo_reporte,
			MutualServicioSolicitud.id,
			TDOC.concepto_1,
			Persona.documento,
			concat(Persona.apellido,', ',Persona.nombre) as apenom,
			Persona.sexo,
			Persona.calle,
			Persona.numero_calle,
			Persona.piso,
			Persona.dpto,
			Persona.barrio,
			Persona.localidad,
			Persona.codigo_postal,
			Provincia.nombre,
			date_format(MutualServicioSolicitud.fecha_alta_servicio,'%d-%m-%Y') as fecha_alta_servicio,
			date_format(MutualServicioSolicitud.fecha_baja_servicio,'%d-%m-%Y') as fecha_baja_servicio,
			'TIT' as condicion,
			Persona.fecha_nacimiento,            
			(select costo_titular from mutual_servicio_valores v where
			v.mutual_servicio_id = MutualServicioSolicitud.mutual_servicio_id
			and v.periodo_vigencia <= date_format(now(),'%Y%m')
			and v.codigo_organismo = PersonaBeneficio.codigo_beneficio
			order by v.periodo_vigencia desc limit 1) as costo_titular
		from mutual_servicio_solicitudes MutualServicioSolicitud
		inner join personas Persona on (Persona.id = MutualServicioSolicitud.persona_id)
		inner join global_datos TDOC on (TDOC.id = Persona.tipo_documento)
		inner join provincias Provincia on (Provincia.id = Persona.provincia_id)
		inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = MutualServicioSolicitud.persona_beneficio_id)
		where
		MutualServicioSolicitud.socio_id = vSOCIO_ID
		and MutualServicioSolicitud.mutual_servicio_id = vSERVICIO_ID
		and ifnull((select count(*) from orden_descuento_cuotas where socio_id = vSOCIO_ID
		and proveedor_id = 18 and tipo_cuota = 'MUTUTCUOCSOC'
		and importe > (select sum(importe) from orden_descuento_cobro_cuotas 
		where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id)),0) <= vCUOTAS_SOCIALES_MINIMAS
		union
		(select
			vPID,
			IF(MutualServicioSolicitudAdicional.fecha_baja IS NULL,'REPORTE_1','REPORTE_2') as tipo_reporte,
			MutualServicioSolicitud.id,
			TDOC.concepto_1,
			SocioAdicional.documento,
			concat(SocioAdicional.apellido,', ',SocioAdicional.nombre) as apenom,
			SocioAdicional.sexo,
			SocioAdicional.calle,
			SocioAdicional.numero_calle,
			SocioAdicional.piso,
			SocioAdicional.dpto,
			SocioAdicional.barrio,
			SocioAdicional.localidad,
			SocioAdicional.codigo_postal,
			Provincia.nombre,
			date_format(MutualServicioSolicitudAdicional.fecha_alta,'%d-%m-%Y') as fecha_alta,
			date_format(MutualServicioSolicitudAdicional.fecha_baja,'%d-%m-%Y') as fecha_baja,
			'ADI' as condicion,   
			SocioAdicional.fecha_nacimiento,            
			(select costo_adicional from mutual_servicio_valores v where
			v.mutual_servicio_id = MutualServicioSolicitud.mutual_servicio_id
			and v.periodo_vigencia <= date_format(now(),'%Y%m')
			and v.codigo_organismo = PersonaBeneficio.codigo_beneficio
			order by v.periodo_vigencia desc limit 1) as costo_adicional
		from mutual_servicio_solicitud_adicionales MutualServicioSolicitudAdicional
		inner join mutual_servicio_solicitudes MutualServicioSolicitud on (MutualServicioSolicitud.id = MutualServicioSolicitudAdicional.mutual_servicio_solicitud_id)
		inner join socio_adicionales SocioAdicional on (SocioAdicional.id = MutualServicioSolicitudAdicional.socio_adicional_id)
		inner join global_datos TDOC on (TDOC.id = SocioAdicional.tipo_documento)
		inner join provincias Provincia on (Provincia.id = SocioAdicional.provincia_id)
		inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = MutualServicioSolicitud.persona_beneficio_id)
		where
		MutualServicioSolicitud.socio_id = vSOCIO_ID
		and MutualServicioSolicitud.mutual_servicio_id = vSERVICIO_ID
		and MutualServicioSolicitudAdicional.fecha_alta <= vFECHA_COBERTURA
		and ifnull((select count(*) from orden_descuento_cuotas where socio_id = vSOCIO_ID
		and proveedor_id = 18 and tipo_cuota = 'MUTUTCUOCSOC'
		and importe > (select sum(importe) from orden_descuento_cobro_cuotas 
		where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id)),0) <= vCUOTAS_SOCIALES_MINIMAS
		order by SocioAdicional.apellido,SocioAdicional.nombre);        
        
 
END$$
DELIMITER ;