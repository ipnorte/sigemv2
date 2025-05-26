/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 06/09/2019
 */

ALTER TABLE `persona_beneficios` 
ADD COLUMN `sueldo_neto` DECIMAL(10,2) NULL,
ADD COLUMN `debitos_bancarios` DECIMAL(10,2) NULL AFTER `sueldo_neto`;

ALTER TABLE `mutual_producto_solicitudes` 
ADD COLUMN `sueldo_neto` DECIMAL(10,2) NULL,
ADD COLUMN `debitos_bancarios` DECIMAL(10,2) NULL AFTER `sueldo_neto`;
