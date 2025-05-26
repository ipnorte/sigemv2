ALTER TABLE `co_ejercicios` ADD COLUMN `activo` TINYINT(1) DEFAULT 0 NULL AFTER `fecha_proceso`; 
ALTER TABLE `sigem_db`.`co_ejercicios` ADD COLUMN `resultado_co_plan_cuenta_id` INT(11) DEFAULT 0 NULL AFTER `co_plan_cuenta_id`;
 
