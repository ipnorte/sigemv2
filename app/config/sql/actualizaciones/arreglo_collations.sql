
SELECT * FROM information_schema.`COLUMNS` 
WHERE TABLE_SCHEMA = 'cordobas_solydar' 
and collation_name <> "utf8_general_ci";


ALTER TABLE mutual_producto_solicitud_preproceso
CHARACTER SET utf8,
COLLATE utf8_general_ci;

ALTER TABLE `mutual_producto_solicitud_preproceso` 
CHANGE COLUMN `uuid_identificador` `uuid_identificador` VARCHAR(100) CHARACTER SET 'utf8' NOT NULL ,
CHANGE COLUMN `a_la_orden_de` `a_la_orden_de` VARCHAR(255) CHARACTER SET 'utf8' NULL DEFAULT NULL ,
CHANGE COLUMN `concepto` `concepto` TEXT CHARACTER SET 'utf8' NULL DEFAULT NULL ,
CHANGE COLUMN `file_name` `file_name` VARCHAR(100) CHARACTER SET 'utf8' NULL DEFAULT NULL ,
CHANGE COLUMN `file_type` `file_type` VARCHAR(100) CHARACTER SET 'utf8' NULL DEFAULT NULL ;



ALTER TABLE mutual_producto_solicitud_documentos
CHARACTER SET utf8,
COLLATE utf8_general_ci;

ALTER TABLE `mutual_producto_solicitud_documentos` 
CHANGE COLUMN `file_name` `file_name` VARCHAR(100) CHARACTER SET 'utf8' NOT NULL ,
CHANGE COLUMN `file_type` `file_type` VARCHAR(100) CHARACTER SET 'utf8' NOT NULL ;


ALTER TABLE mutual_producto_solicitud_estados
CHARACTER SET utf8,
COLLATE utf8_general_ci;

ALTER TABLE `mutual_producto_solicitud_estados` 
CHANGE COLUMN `estado` `estado` VARCHAR(12) CHARACTER SET 'utf8' NOT NULL ,
CHANGE COLUMN `observaciones` `observaciones` TEXT CHARACTER SET 'utf8' NULL DEFAULT NULL ,
CHANGE COLUMN `user_created` `user_created` VARCHAR(45) CHARACTER SET 'utf8' NOT NULL ;


ALTER TABLE mutual_producto_solicitud_pagos
CHARACTER SET utf8,
COLLATE utf8_general_ci;

ALTER TABLE `cordobas_soluciones`.`mutual_producto_solicitud_pagos` 
CHANGE COLUMN `user_modified` `user_modified` VARCHAR(50) CHARACTER SET 'utf8' NULL DEFAULT NULL ;


ALTER TABLE `mutual_producto_solicitud_pagos` 
CHANGE COLUMN `forma_pago` `forma_pago` VARCHAR(12) CHARACTER SET 'utf8' NULL DEFAULT NULL ,
CHANGE COLUMN `banco_id` `banco_id` VARCHAR(5) CHARACTER SET 'utf8' NULL DEFAULT NULL ,
CHANGE COLUMN `nro_comprobante` `nro_comprobante` VARCHAR(50) CHARACTER SET 'utf8' NULL DEFAULT NULL ,
CHANGE COLUMN `observaciones` `observaciones` TEXT CHARACTER SET 'utf8' NULL DEFAULT NULL ,
CHANGE COLUMN `user_created` `user_created` VARCHAR(50) CHARACTER SET 'utf8' NULL DEFAULT NULL ,
CHANGE COLUMN `user_modified` `user_modified` VARCHAR(50) CHARACTER SET 'utf8' NULL DEFAULT NULL ;


