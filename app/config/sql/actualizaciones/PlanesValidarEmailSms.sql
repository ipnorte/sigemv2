/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 18/09/2019
 */

ALTER TABLE `proveedor_planes` 
ADD COLUMN `validar_email` TINYINT(1) NOT NULL DEFAULT 0 AFTER `ayuda_economica`,
ADD COLUMN `validar_sms` TINYINT(1) NOT NULL DEFAULT 0 AFTER `validar_email`;
