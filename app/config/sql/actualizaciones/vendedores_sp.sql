/*
SQLyog Community v11.28 (32 bit)
MySQL - 5.5.34-0ubuntu0.13.04.1 : Database - sigem_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/* Procedure structure for procedure `p_actualizar_password` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_actualizar_password` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_actualizar_password`(
	in vID INT(11), vPASS VARCHAR(40)
    )
BEGIN
    
	UPDATE usuarios set `password` =  vPASS
	where id = vID;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `p_actualizar_persona` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_actualizar_persona` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_actualizar_persona`(
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
END */$$
DELIMITER ;

/* Procedure structure for procedure `p_actualizar_persona_contacto` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_actualizar_persona_contacto` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_actualizar_persona_contacto`(
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
END */$$
DELIMITER ;

/* Procedure structure for procedure `p_actualizar_persona_domicilio` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_actualizar_persona_domicilio` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_actualizar_persona_domicilio`(
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
END */$$
DELIMITER ;

/* Procedure structure for procedure `p_anular_solicitud_credito` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_anular_solicitud_credito` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_anular_solicitud_credito`(
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
    END */$$
DELIMITER ;

/* Procedure structure for procedure `p_calcular_saldos_socio_periodo` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_calcular_saldos_socio_periodo` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_calcular_saldos_socio_periodo`(
	in vPERIODO VARCHAR(6),
	vSOCIO_ID INT(11)
)
BEGIN
SELECT periodo,
IFNULL(SUM(importe) - IFNULL((SELECT SUM(importe) FROM orden_descuento_cobro_cuotas cocu_1
WHERE cocu_1.orden_descuento_cuota_id = orden_descuento_cuotas.id
),0),0) AS saldo_periodo,
(SELECT SUM(importe) FROM orden_descuento_cuotas odc_1 
WHERE odc_1.periodo < orden_descuento_cuotas.periodo 
AND odc_1.socio_id = orden_descuento_cuotas.socio_id)-
IFNULL((SELECT SUM(cocu.importe) FROM orden_descuento_cobro_cuotas cocu,orden_descuento_cuotas odc_1 
WHERE cocu.orden_descuento_cuota_id = odc_1.id
AND odc_1.periodo < orden_descuento_cuotas.periodo
AND odc_1.socio_id = orden_descuento_cuotas.socio_id),0) AS vencido,
(SELECT SUM(importe) FROM orden_descuento_cuotas odc_1 
WHERE odc_1.periodo <= orden_descuento_cuotas.periodo 
AND odc_1.socio_id = orden_descuento_cuotas.socio_id)-
IFNULL((SELECT SUM(cocu.importe) FROM orden_descuento_cobro_cuotas cocu,orden_descuento_cuotas odc_1 
WHERE cocu.orden_descuento_cuota_id = odc_1.id
AND odc_1.periodo <= orden_descuento_cuotas.periodo
AND odc_1.socio_id = orden_descuento_cuotas.socio_id),0) AS saldo_total_acumulado_periodo,
(SELECT SUM(importe) FROM orden_descuento_cuotas odc_1 
WHERE odc_1.periodo > orden_descuento_cuotas.periodo 
AND odc_1.socio_id = orden_descuento_cuotas.socio_id)-
IFNULL((SELECT SUM(cocu.importe) FROM orden_descuento_cobro_cuotas cocu,orden_descuento_cuotas odc_1 
WHERE cocu.orden_descuento_cuota_id = odc_1.id
AND odc_1.periodo > orden_descuento_cuotas.periodo
AND odc_1.socio_id = orden_descuento_cuotas.socio_id),0) AS a_vencer
FROM orden_descuento_cuotas WHERE socio_id = vSOCIO_ID
AND periodo = vPERIODO
GROUP BY periodo;
END */$$
DELIMITER ;

/* Procedure structure for procedure `p_consultar_asincrono` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_consultar_asincrono` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_consultar_asincrono`(in vID int(11))
BEGIN
	select * from v_asincronos where ID = vID;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `p_detener_asincrono` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_detener_asincrono` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_detener_asincrono`(vID INT(11))
BEGIN
	UPDATE asincronos 
	set
		estado = 'S',
		msg = '*** DETENIDO POR EL USUARIO ***'
	where id = vID;
	SELECT * FROM v_asincronos WHERE ID = vID;	
    END */$$
DELIMITER ;

/* Procedure structure for procedure `p_insertar_asincrono` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_insertar_asincrono` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_asincrono`(
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
END */$$
DELIMITER ;

/* Procedure structure for procedure `p_insertar_beneficio` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_insertar_beneficio` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_beneficio`(
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
		set vBANCO_ID = right(concat('00000',vBANCO_ID),5);
		INSERT INTO persona_beneficios(persona_id,codigo_beneficio,codigo_empresa,
		codigo_reparticion,turno_pago,cbu,banco_id,nro_sucursal,nro_cta_bco,created)
		VALUES(vPERSONA_ID,vCODIGO_BENEFICIO,vCODIGO_EMPRESA,vCODIGO_REPARTICION,vTURNO_PAGO,
		vCBU,vBANCO_ID,vSUCURSAL,vNRO_CTA_BCO,now());
		SELECT LAST_INSERT_ID() INTO @ID;
	END IF;
	
	SELECT * FROM v_persona_beneficios where ID = @ID;
	
		
END */$$
DELIMITER ;

/* Procedure structure for procedure `p_insertar_persona` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_insertar_persona` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_persona`(
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
    END */$$
DELIMITER ;

/* Procedure structure for procedure `p_insertar_solicitud_credito` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_insertar_solicitud_credito` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito`(
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
	DECLARE vTIPOORDEN VARCHAR(5); -- EXPTE
	SET vCUOTAS = IF(vCUOTAS=0,NULL,vCUOTAS);
	SET vIMPORTE_CUOTA = IF(vIMPORTE_CUOTA=0,NULL,vIMPORTE_CUOTA);
	SET vIMPORTE_SOLICITADO = IF(vIMPORTE_SOLICITADO=0,NULL,vIMPORTE_SOLICITADO);
	SET vIMPORTE_PERCIBIDO = IF(vIMPORTE_PERCIBIDO=0,NULL,vIMPORTE_PERCIBIDO);
	SET @TOTAL = vCUOTAS * vIMPORTE_CUOTA;
	
	set vESTADO = 'MUTUESTA0001';
	SET vPRODUCTO = 'MUTUPROD0001';
	
	IF vVENDEDOR_ID IS NULL THEN
		SET vESTADO = 'MUTUESTA0002';
	END IF;
	
	SELECT trim(concepto_3) into vTIPOORDEN from global_datos where id = vPRODUCTO;	
	/*
	SELECT vPROVEEDOR_ID,vPROVEEDOR_PLAN_ID,vPERSONA_ID,vCLIENTE_ID,
	vPERSONA_BENEFICIO_ID,NOW(),vTIPOORDEN,vPRODUCTO,vESTADO,
	@TOTAL,vCUOTAS,vIMPORTE_CUOTA,vIMPORTE_SOLICITADO,vIMPORTE_PERCIBIDO,
	vVENDEDOR_ID,NOW(),vUSUARIO,vOBSERVACIONES,vFORMA_PAGO;	
	*/
	INSERT INTO mutual_producto_solicitudes(proveedor_id,proveedor_plan_id,
	persona_id,socio_id,persona_beneficio_id,fecha,tipo_orden_dto,tipo_producto,
	estado,importe_total,cuotas,importe_cuota,importe_solicitado,importe_percibido,
	vendedor_id,created,user_created,observaciones,forma_pago)
	VALUES(vPROVEEDOR_ID,vPROVEEDOR_PLAN_ID,vPERSONA_ID,vCLIENTE_ID,
	vPERSONA_BENEFICIO_ID,NOW(),vTIPOORDEN,vPRODUCTO,vESTADO,
	@TOTAL,vCUOTAS,vIMPORTE_CUOTA,vIMPORTE_SOLICITADO,vIMPORTE_PERCIBIDO,
	vVENDEDOR_ID,NOW(),vUSUARIO,vOBSERVACIONES,vFORMA_PAGO);
	-- SELECT LAST_INSERT_ID();
	SELECT * FROM v_credito_solicitudes WHERE ID = (SELECT LAST_INSERT_ID());
END */$$
DELIMITER ;

/* Procedure structure for procedure `p_insertar_solicitud_credito_cancelacion` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_insertar_solicitud_credito_cancelacion` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_cancelacion`(
	in vSOLICITUD_ID INT(11),
	vCANCELACION_ID INT(11)
    )
BEGIN
	INSERT INTO mutual_producto_solicitud_cancelaciones(mutual_producto_solicitud_id,cancelacion_orden_id)
	VALUES(vSOLICITUD_ID,vCANCELACION_ID);
    END */$$
DELIMITER ;

/* Procedure structure for procedure `p_insertar_solicitud_credito_instruccion_pago` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_insertar_solicitud_credito_instruccion_pago` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_instruccion_pago`(
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
    END */$$
DELIMITER ;

/* Procedure structure for procedure `p_listado_solicitudes_by_asincrono` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_listado_solicitudes_by_asincrono` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_listado_solicitudes_by_asincrono`(in vPID int(11))
BEGIN
	DECLARE l_last_row INT DEFAULT 0;
	declare vFD date;
	declare vFH date;
	declare vESTADO varchar(12);
	DECLARE vID INT(11);
	
	
	
	DECLARE  c_solicitudes CURSOR FOR 
	SELECT ID FROM orden_descuento_cuotas;
	-- WHERE FECHA BETWEEN vFD AND vFH ORDER BY FECHA;	
	
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
END */$$
DELIMITER ;

/* Procedure structure for procedure `p_marcar_solicitud_notificacion_leida` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_marcar_solicitud_notificacion_leida` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_marcar_solicitud_notificacion_leida`(
	in vID INT(11)
    )
BEGIN
	update mutual_producto_solicitudes SET vendedor_notificar = 0 where id = vID;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `v_buscar_solicitudes_credito` */

/*!50003 DROP PROCEDURE IF EXISTS  `v_buscar_solicitudes_credito` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `v_buscar_solicitudes_credito`()
BEGIN
    END */$$
DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
