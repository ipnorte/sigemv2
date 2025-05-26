<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ALTER TABLE `sigem_db`.`co_ejercicios` ADD COLUMN `co_plan_cuenta_id` INT(11) DEFAULT 0 NULL AFTER `fecha_proceso`; 

ALTER TABLE `sigem_db`.`co_asientos` ADD COLUMN `tipo` TINYINT(1) DEFAULT 2 NULL AFTER `cierre`; 

UPDATE co_asientos SET tipo = 1 WHERE nro_asiento = 1