/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 27/07/2016
 */

ALTER TABLE `proveedor_plan_grillas` 
ADD COLUMN `tna` DECIMAL(10,2) NULL DEFAULT 0 AFTER `cuotas`,
ADD COLUMN `tem` DECIMAL(10,2) NULL DEFAULT 0 AFTER `tna`;

ALTER TABLE `proveedor_plan_grilla_cuotas` 
ADD COLUMN `tna` DECIMAL(10,2) NULL DEFAULT 0 AFTER `importe`,
ADD COLUMN `tem` DECIMAL(10,2) NULL DEFAULT 0 AFTER `tna`,
ADD COLUMN `cft` DECIMAL(10,2) NULL DEFAULT 0 AFTER `tem`;

ALTER TABLE `mutual_producto_solicitudes` 
ADD COLUMN `tna` DECIMAL(10,2) NULL DEFAULT 0 AFTER `importe_percibido`,
ADD COLUMN `tem` DECIMAL(10,2) NULL DEFAULT 0 AFTER `tna`,
ADD COLUMN `cft` DECIMAL(10,2) NULL DEFAULT 0 AFTER `tem`;

ALTER TABLE `orden_descuentos` 
ADD COLUMN `tna` DECIMAL(10,2) NULL DEFAULT 0 AFTER `importe_capital`,
ADD COLUMN `tem` DECIMAL(10,2) NULL DEFAULT 0 AFTER `tna`,
ADD COLUMN `cft` DECIMAL(10,2) NULL DEFAULT 0 AFTER `tem`;


CREATE 
     OR REPLACE ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_plan_condiciones` AS
    (SELECT 
        `ppgc`.`id` AS `ID`,
        `pp`.`ID` AS `PLAN_ID`,
        `ppg`.`vigencia_desde` AS `VIGENCIA`,
        `ppgc`.`capital` AS `CAPITAL`,
        `ppgc`.`liquido` AS `LIQUIDO`,
        `ppgc`.`cuotas` AS `CUOTAS`,
        `ppgc`.`importe` AS `IMPORTE`,
        (`ppgc`.`importe` * `ppgc`.`cuotas`) AS `TOTAL`,
        `ppgc`.`tna` AS `TNA`,
        `ppgc`.`tem` AS `TEM`,
        `ppgc`.`cft` AS `CFT`
    FROM
        ((`v_planes` `pp`
        JOIN `proveedor_plan_grillas` `ppg`)
        JOIN `proveedor_plan_grilla_cuotas` `ppgc`)
    WHERE
        ((`pp`.`ID` = `ppg`.`proveedor_plan_id`)
            AND (`ppg`.`id` = `ppgc`.`proveedor_plan_grilla_id`)
            AND (`ppg`.`vigencia_desde` >= (SELECT 
                MAX(`ppg2`.`vigencia_desde`)
            FROM
                `proveedor_plan_grillas` `ppg2`
            WHERE
                (`ppg2`.`proveedor_plan_id` = `ppg`.`proveedor_plan_id`)))));



CREATE 
     OR REPLACE ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_plan_monto_cuotas` AS
    (SELECT 
        `proveedor_plan_grilla_cuotas`.`id` AS `ID`,
        `proveedor_planes`.`id` AS `PLAN_ID`,
        `proveedor_plan_grillas`.`vigencia_desde` AS `VIGENCIA`,
        `proveedor_plan_grilla_cuotas`.`capital` AS `CAPITAL`,
        `proveedor_plan_grilla_cuotas`.`liquido` AS `LIQUIDO`,
        `proveedor_plan_grilla_cuotas`.`cuotas` AS `CUOTAS`,
        `proveedor_plan_grilla_cuotas`.`importe` AS `IMPORTE`,
        (`proveedor_plan_grilla_cuotas`.`importe` * `proveedor_plan_grilla_cuotas`.`cuotas`) AS `TOTAL`,
        `proveedor_plan_grilla_cuotas`.`tna` AS `TNA`,
        `proveedor_plan_grilla_cuotas`.`tem` AS `TEM`,
        `proveedor_plan_grilla_cuotas`.`cft` AS `CFT`
    FROM
        ((`proveedor_planes`
        JOIN `proveedor_plan_grillas`)
        JOIN `proveedor_plan_grilla_cuotas`)
    WHERE
        ((`proveedor_planes`.`id` = `proveedor_plan_grillas`.`proveedor_plan_id`)
            AND (`proveedor_plan_grillas`.`id` = (SELECT 
                `grillas`.`id`
            FROM
                `proveedor_plan_grillas` `grillas`
            WHERE
                ((`grillas`.`proveedor_plan_id` = `proveedor_planes`.`id`)
                    AND (`grillas`.`vigencia_desde` >= (SELECT 
                        MAX(`ppg2`.`vigencia_desde`)
                    FROM
                        `proveedor_plan_grillas` `ppg2`
                    WHERE
                        (`ppg2`.`proveedor_plan_id` = `grillas`.`proveedor_plan_id`))))
            ORDER BY `grillas`.`vigencia_desde` DESC
            LIMIT 1))
            AND (`proveedor_plan_grilla_cuotas`.`proveedor_plan_grilla_id` = `proveedor_plan_grillas`.`id`))
    GROUP BY `proveedor_planes`.`id` , `proveedor_plan_grillas`.`vigencia_desde` , `proveedor_plan_grilla_cuotas`.`liquido` , `proveedor_plan_grilla_cuotas`.`cuotas`
    ORDER BY `proveedor_planes`.`id` , `proveedor_plan_grilla_cuotas`.`liquido` , `proveedor_plan_grilla_cuotas`.`cuotas`);


CREATE 
     OR REPLACE ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_plan_montos` AS
    (SELECT 
        `proveedor_plan_grilla_cuotas`.`id` AS `ID`,
        `proveedor_planes`.`id` AS `PLAN_ID`,
        `proveedor_plan_grillas`.`vigencia_desde` AS `VIGENCIA`,
        `proveedor_plan_grilla_cuotas`.`liquido` AS `LIQUIDO`,
        `proveedor_plan_grilla_cuotas`.`tna` AS `TNA`,
        `proveedor_plan_grilla_cuotas`.`tem` AS `TEM`,
        `proveedor_plan_grilla_cuotas`.`cft` AS `CFT`        
    FROM
        ((`proveedor_planes`
        JOIN `proveedor_plan_grillas`)
        JOIN `proveedor_plan_grilla_cuotas`)
    WHERE
        ((`proveedor_planes`.`id` = `proveedor_plan_grillas`.`proveedor_plan_id`)
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
VIEW `v_credito_solicitudes` AS
    (SELECT 
        `mutual_producto_solicitudes`.`id` AS `ID`,
        `mutual_producto_solicitudes`.`proveedor_id` AS `PROVEEDOR_ID`,
        `mutual_producto_solicitudes`.`proveedor_plan_id` AS `PROVEEDOR_PLAN_ID`,
        `mutual_producto_solicitudes`.`persona_id` AS `PERSONA_ID`,
        `mutual_producto_solicitudes`.`socio_id` AS `CLIENTE_ID`,
        `mutual_producto_solicitudes`.`persona_beneficio_id` AS `PERSONA_BENEFICIO_ID`,
        `mutual_producto_solicitudes`.`aprobada` AS `APROBADA`,
        `mutual_producto_solicitudes`.`anulada` AS `ANULADA`,
        `mutual_producto_solicitudes`.`fecha` AS `FECHA`,
        `mutual_producto_solicitudes`.`fecha_pago` AS `FECHA_PAGO`,
        `mutual_producto_solicitudes`.`tipo_orden_dto` AS `TIPO_ORDEN_DTO`,
        `mutual_producto_solicitudes`.`tipo_producto` AS `TIPO_PRODUCTO`,
        IF(((`mutual_producto_solicitudes`.`anulada` = 1)
                AND (`mutual_producto_solicitudes`.`estado` = 'MUTUESTA0001')),
            'MUTUESTA0000',
            `mutual_producto_solicitudes`.`estado`) AS `ESTADO`,
        `mutual_producto_solicitudes`.`importe_total` AS `IMPORTE_TOTAL`,
        `mutual_producto_solicitudes`.`cuotas` AS `CUOTAS`,
        `mutual_producto_solicitudes`.`importe_cuota` AS `IMPORTE_CUOTA`,
        `mutual_producto_solicitudes`.`importe_solicitado` AS `IMPORTE_SOLICITADO`,
        `mutual_producto_solicitudes`.`importe_percibido` AS `IMPORTE_PERCIBIDO`,
        `mutual_producto_solicitudes`.`tna` AS `TNA`,
        `mutual_producto_solicitudes`.`tem` AS `TEM`,
        `mutual_producto_solicitudes`.`cft` AS `CFT`,
        `mutual_producto_solicitudes`.`observaciones` AS `OBSERVACIONES`,
        `mutual_producto_solicitudes`.`orden_descuento_id` AS `ORDEN_DESCUENTO_ID`,
        `mutual_producto_solicitudes`.`aprobada_por` AS `APROBADA_POR`,
        `mutual_producto_solicitudes`.`aprobada_el` AS `APROBADA_EL`,
        `mutual_producto_solicitudes`.`vendedor_id` AS `VENDEDOR_ID`,
        `mutual_producto_solicitudes`.`vendedor_remito_id` AS `VENDEDOR_REMITO_ID`,
        `mutual_producto_solicitudes`.`vendedor_notificar` AS `VENDEDOR_NOTIFICAR`,
        `mutual_producto_solicitudes`.`user_created` AS `EMITIDA_POR`,
        `mutual_producto_solicitudes`.`periodo_ini` AS `PERIODO_INI`,
        `mutual_producto_solicitudes`.`primer_vto_socio` AS `PRIMER_VTO_SOCIO`,
        `mutual_producto_solicitudes`.`forma_pago` AS `FORMA_PAGO`,
        IF(((`mutual_producto_solicitudes`.`aprobada` = 1)
                AND (`mutual_producto_solicitudes`.`anulada` = 0)),
            CONCAT(`mutual_producto_solicitudes`.`tipo_orden_dto`,
                    ' ',
                    `mutual_producto_solicitudes`.`orden_descuento_id`),
            '') AS `ORDEN_DESCUENTO`
    FROM
        `mutual_producto_solicitudes`
    WHERE
        (`mutual_producto_solicitudes`.`tipo_orden_dto` = 'EXPTE'));



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
	vUUID VARCHAR(100),
    vTNA DECIMAL(10,2),
    vTEM DECIMAL(10,2),
    vCFT DECIMAL(10,2)
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
	vendedor_id,created,user_created,observaciones,forma_pago,prestamo,tna,tem,cft)
	VALUES(vPROVEEDOR_ID,vPROVEEDOR_PLAN_ID,vPERSONA_ID,vCLIENTE_ID,
	vPERSONA_BENEFICIO_ID,NOW(),vTIPOORDEN,vPRODUCTO,vESTADO,
	@TOTAL,vCUOTAS,vIMPORTE_CUOTA,vIMPORTE_SOLICITADO,vIMPORTE_PERCIBIDO,
	vVENDEDOR_ID,NOW(),vUSUARIO,vOBSERVACIONES,vFORMA_PAGO,vPRESTAMO,vTNA,vTEM,vCFT);
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

