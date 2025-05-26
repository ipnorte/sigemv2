/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 01/08/2016
 */

ALTER TABLE `personas` 
ADD COLUMN `maps_latitud` FLOAT(10,6) NULL DEFAULT NULL AFTER `provincia_id`,
ADD COLUMN `maps_longitud` FLOAT(10,6) NULL DEFAULT NULL AFTER `maps_latitud`,
ADD COLUMN `entre_calle_1` VARCHAR(45) NULL AFTER `maps_longitud`,
ADD COLUMN `entre_calle_2` VARCHAR(45) NULL AFTER `entre_calle_1`;


ALTER TABLE `personas` 
CHANGE COLUMN `maps_latitud` `maps_latitud` FLOAT(10,6) NULL DEFAULT NULL ,
CHANGE COLUMN `maps_longitud` `maps_longitud` FLOAT(10,6) NULL DEFAULT NULL ;

ALTER TABLE `personas` ADD COLUMN `telefono_movil_empresa` varchar(12) DEFAULT NULL AFTER `telefono_movil`;