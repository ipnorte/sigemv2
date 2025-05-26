/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 04/07/2016
 */
ALTER TABLE `usuarios` 
ADD COLUMN `email` VARCHAR(100) NOT NULL AFTER `vendedor_id`,
ADD COLUMN `pin` VARCHAR(10) NULL AFTER `email`,
ADD COLUMN `caduca` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER `pin`,
ADD COLUMN `validado` TINYINT(1) NOT NULL DEFAULT 1 AFTER caduca,
ADD COLUMN `ultimo_password` VARCHAR(45) NULL AFTER `validado`,
ADD COLUMN `ip_registro` VARCHAR(45) NULL AFTER `ultimo_password`,
ADD COLUMN `host_registro` VARCHAR(45) NULL AFTER `ip_registro`,
CHANGE COLUMN `reset_password` `reset_password` TINYINT(1) NOT NULL DEFAULT 1;

UPDATE usuarios set reset_password = 0, pin = null, validado = 1, caduca = '2050-12-31 23:59:59';