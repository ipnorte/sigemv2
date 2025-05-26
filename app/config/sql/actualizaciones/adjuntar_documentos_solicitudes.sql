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
/*Table structure for table `mutual_producto_solicitud_documentos` */

DROP TABLE IF EXISTS `mutual_producto_solicitud_documentos`;

CREATE TABLE `mutual_producto_solicitud_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mutual_producto_solicitud_id` int(11) DEFAULT NULL,
  `file_name` varchar(100) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `file_data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_documentos_mutual_producto_solicitud` (`mutual_producto_solicitud_id`),
  CONSTRAINT `FK_documentos_mutual_producto_solicitud_id` FOREIGN KEY (`mutual_producto_solicitud_id`) REFERENCES `mutual_producto_solicitudes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

/*Table structure for table `mutual_producto_solicitud_preproceso` */

DROP TABLE IF EXISTS `mutual_producto_solicitud_preproceso`;

CREATE TABLE `mutual_producto_solicitud_preproceso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_identificador` varchar(100) NOT NULL,
  `tipo` int(11) NOT NULL,
  `cancelacion_id` int(11) DEFAULT NULL,
  `a_la_orden_de` varchar(255) DEFAULT NULL,
  `concepto` text,
  `importe` decimal(10,2) DEFAULT NULL,
  `file_name` varchar(100) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_data` blob,
  PRIMARY KEY (`id`,`uuid_identificador`,`tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=latin1;

/* Procedure structure for procedure `p_insertar_solicitud_credito_cancelacion_preproceso` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_insertar_solicitud_credito_cancelacion_preproceso` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_cancelacion_preproceso`(
	in vUUID VARCHAR(100),
	vCANCELACION_ID INT(11)
    )
BEGIN
	INSERT INTO mutual_producto_solicitud_preproceso(uuid_identificador,tipo,cancelacion_id)
	VALUES(vUUID,2,vCANCELACION_ID);
    END */$$
DELIMITER ;

/* Procedure structure for procedure `p_insertar_solicitud_credito_con_preproceso` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_insertar_solicitud_credito_con_preproceso` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_con_preproceso`(
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
	
	SET vSOLICITUD_ID = LAST_INSERT_ID();	
	
	-- PROCESO DATOS TEMPORALES
	-- 1) CANCELACIONES
	INSERT INTO mutual_producto_solicitud_cancelaciones(mutual_producto_solicitud_id,cancelacion_orden_id)
	SELECT vSOLICITUD_ID,cancelacion_id FROM mutual_producto_solicitud_preproceso
	WHERE uuid_identificador = vUUID AND tipo = 2;
	
	-- 2) INSTRUCCION DE PAGO
	INSERT INTO mutual_producto_solicitud_instruccion_pagos(mutual_producto_solicitud_id,a_la_orden_de,concepto,importe)
	SELECT vSOLICITUD_ID,a_la_orden_de,concepto,importe FROM mutual_producto_solicitud_preproceso
	WHERE uuid_identificador = vUUID AND tipo = 1;	
		
	-- 3) DOCUMENTOS ADJUNTOS
	INSERT INTO mutual_producto_solicitud_documentos(mutual_producto_solicitud_id,file_name,file_type,file_data)
	SELECT vSOLICITUD_ID,file_name,file_type,file_data FROM mutual_producto_solicitud_preproceso
	WHERE uuid_identificador = vUUID AND tipo = 3;	
	
	DELETE FROM mutual_producto_solicitud_preproceso WHERE uuid_identificador = vUUID;		
	
	
	-- SELECT LAST_INSERT_ID();
	SELECT * FROM v_credito_solicitudes WHERE ID = vSOLICITUD_ID;
END */$$
DELIMITER ;

/* Procedure structure for procedure `p_insertar_solicitud_credito_documento_preproceso` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_insertar_solicitud_credito_documento_preproceso` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_documento_preproceso`(
	in vUUID VARCHAR(100),
	vFILE_NAME VARCHAR(100),
	vFILE_TYPE VARCHAR(100),
	vFILE_DATA LONGBLOB 
)
BEGIN
INSERT INTO `mutual_producto_solicitud_preproceso` 
	(uuid_identificador,tipo,`file_name`, `file_type`, `file_data`)
	VALUES (vUUID,3,vFILE_NAME,vFILE_TYPE,vFILE_DATA);
END */$$
DELIMITER ;

/* Procedure structure for procedure `p_insertar_solicitud_credito_instruccion_pago_preproceso` */

/*!50003 DROP PROCEDURE IF EXISTS  `p_insertar_solicitud_credito_instruccion_pago_preproceso` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_instruccion_pago_preproceso`(
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
    END */$$
DELIMITER ;

/*Table structure for table `v_credito_solicitud_documentos` */

DROP TABLE IF EXISTS `v_credito_solicitud_documentos`;

/*!50001 DROP VIEW IF EXISTS `v_credito_solicitud_documentos` */;
/*!50001 DROP TABLE IF EXISTS `v_credito_solicitud_documentos` */;

/*!50001 CREATE TABLE  `v_credito_solicitud_documentos`(
 `ID` int(11) ,
 `MUTUAL_PRODUCTO_SOLICITUD_ID` int(11) ,
 `FILE_NAME` varchar(100) ,
 `FILE_TYPE` varchar(100) ,
 `FILE_DATA` blob 
)*/;

/*View structure for view v_credito_solicitud_documentos */

/*!50001 DROP TABLE IF EXISTS `v_credito_solicitud_documentos` */;
/*!50001 DROP VIEW IF EXISTS `v_credito_solicitud_documentos` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_credito_solicitud_documentos` AS (select `mutual_producto_solicitud_documentos`.`id` AS `ID`,`mutual_producto_solicitud_documentos`.`mutual_producto_solicitud_id` AS `MUTUAL_PRODUCTO_SOLICITUD_ID`,`mutual_producto_solicitud_documentos`.`file_name` AS `FILE_NAME`,`mutual_producto_solicitud_documentos`.`file_type` AS `FILE_TYPE`,`mutual_producto_solicitud_documentos`.`file_data` AS `FILE_DATA` from `mutual_producto_solicitud_documentos`) */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
