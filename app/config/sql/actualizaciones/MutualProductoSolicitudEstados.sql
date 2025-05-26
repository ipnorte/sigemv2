-- OJO CON EL PREFIJO DE LA BASE DE DATOS
CREATE TABLE `mutual22_sigemdb`.`mutual_producto_solicitud_estados` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `mutual_producto_solicitud_id` INT NOT NULL,
  `estado` VARCHAR(12) NOT NULL,
  `observaciones` TEXT NULL,  
  `user_created` VARCHAR(45) NOT NULL,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_mutual_producto_solicitud_estados_1_idx` (`mutual_producto_solicitud_id` ASC),
  INDEX `fk_mutual_producto_solicitud_estados_2_idx` (`estado` ASC),
  CONSTRAINT `fk_mutual_producto_solicitud_estados_1`
    FOREIGN KEY (`mutual_producto_solicitud_id`)
    REFERENCES `mutual22_sigemdb`.`mutual_producto_solicitudes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

USE `mutual22_sigemdb`;
DROP procedure IF EXISTS `p_insertar_solicitud_credito_con_preproceso`;

DELIMITER $$
USE `mutual22_sigemdb`$$
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
	
	INSERT INTO mutual_producto_solicitudes(proveedor_id,proveedor_plan_id,
	persona_id,socio_id,persona_beneficio_id,fecha,tipo_orden_dto,tipo_producto,
	estado,importe_total,cuotas,importe_cuota,importe_solicitado,importe_percibido,
	vendedor_id,created,user_created,observaciones,forma_pago)
	VALUES(vPROVEEDOR_ID,vPROVEEDOR_PLAN_ID,vPERSONA_ID,vCLIENTE_ID,
	vPERSONA_BENEFICIO_ID,NOW(),vTIPOORDEN,vPRODUCTO,vESTADO,
	@TOTAL,vCUOTAS,vIMPORTE_CUOTA,vIMPORTE_SOLICITADO,vIMPORTE_PERCIBIDO,
	vVENDEDOR_ID,NOW(),vUSUARIO,vOBSERVACIONES,vFORMA_PAGO);
	
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

