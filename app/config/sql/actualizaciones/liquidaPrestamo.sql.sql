ALTER TABLE `proveedores` ADD COLUMN `liquida_prestamo` TINYINT NULL DEFAULT 0 AFTER `genera_cuota_social`;

DROP procedure IF EXISTS `p_insertar_solicitud_credito_con_preproceso`;
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


DROP procedure IF EXISTS `p_insertar_solicitud_credito`;

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

-- ////////////////////////////////////////////////////////
DROP FUNCTION IF EXISTS `FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE`;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE`(
vPROVEEDOR_ID INT(11)) RETURNS decimal(10,2)
BEGIN
DECLARE vSALDO DECIMAL(10,2);
SET vSALDO = 10000000;
SELECT liquida_prestamo into @liquida_prestamo FROM proveedores WHERE id = vPROVEEDOR_ID;
IF @liquida_prestamo = 1 THEN 
	SET vSALDO = 1500; 
END IF;
RETURN vSALDO;
END$$
DELIMITER ;


-- ////////////////////////////////////////////////////////

CREATE 
     OR REPLACE ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_plan_montos` AS
    (SELECT 
        `proveedor_plan_grilla_cuotas`.`id` AS `ID`,
        `proveedor_planes`.`id` AS `PLAN_ID`,
        `proveedor_plan_grillas`.`vigencia_desde` AS `VIGENCIA`,
        `proveedor_plan_grilla_cuotas`.`liquido` AS `LIQUIDO`
    FROM
        ((`proveedor_planes`
        JOIN `proveedor_plan_grillas`)
        JOIN `proveedor_plan_grilla_cuotas`)
    WHERE
        ((`proveedor_planes`.`id` = `proveedor_plan_grillas`.`proveedor_plan_id`)
            AND (`proveedor_plan_grilla_cuotas`.`liquido`<= FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE(`proveedor_planes`.`proveedor_id`))
            AND (`proveedor_plan_grillas`.`id` = (SELECT 
                `grillas`.`id`
            FROM
                `proveedor_plan_grillas` `grillas`
            WHERE
                ((`grillas`.`proveedor_plan_id` = `proveedor_planes`.`id`)
                    AND (`grillas`.`vigencia_desde` <= CURDATE()))
            ORDER BY `grillas`.`vigencia_desde` DESC
            LIMIT 1))
            AND (`proveedor_plan_grilla_cuotas`.`proveedor_plan_grilla_id` = `proveedor_plan_grillas`.`id`))
    GROUP BY `proveedor_planes`.`id` , `proveedor_plan_grillas`.`vigencia_desde` , `proveedor_plan_grilla_cuotas`.`liquido`
    ORDER BY `proveedor_planes`.`id` , `proveedor_plan_grilla_cuotas`.`liquido`);

CREATE 
     OR REPLACE ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_plan_montos` AS
    (SELECT 
        `proveedor_plan_grilla_cuotas`.`id` AS `ID`,
        `proveedor_planes`.`id` AS `PLAN_ID`,
        `proveedor_plan_grillas`.`vigencia_desde` AS `VIGENCIA`,
        `proveedor_plan_grilla_cuotas`.`liquido` AS `LIQUIDO`
    FROM
        ((`proveedor_planes`
        JOIN `proveedor_plan_grillas`)
        JOIN `proveedor_plan_grilla_cuotas`)
    WHERE
        ((`proveedor_planes`.`id` = `proveedor_plan_grillas`.`proveedor_plan_id`)
            AND (`proveedor_plan_grilla_cuotas`.`liquido`<= FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE(`proveedor_planes`.`proveedor_id`))
            AND (`proveedor_plan_grillas`.`id` = (SELECT 
                `grillas`.`id`
            FROM
                `proveedor_plan_grillas` `grillas`
            WHERE
                ((`grillas`.`proveedor_plan_id` = `proveedor_planes`.`id`)
                    AND (`grillas`.`vigencia_desde` <= CURDATE()))
            ORDER BY `grillas`.`vigencia_desde` DESC
            LIMIT 1))
            AND (`proveedor_plan_grilla_cuotas`.`proveedor_plan_grilla_id` = `proveedor_plan_grillas`.`id`))
    GROUP BY `proveedor_planes`.`id` , `proveedor_plan_grillas`.`vigencia_desde` , `proveedor_plan_grilla_cuotas`.`liquido`
    ORDER BY `proveedor_planes`.`id` , `proveedor_plan_grilla_cuotas`.`liquido`);

