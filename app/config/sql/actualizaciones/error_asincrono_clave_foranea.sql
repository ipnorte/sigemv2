/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 05/11/2018
 */

ALTER TABLE `asincrono_errores` 
DROP FOREIGN KEY `FK_ASINCRONO_ERRORES_ASINCRONOS`;
ALTER TABLE `asincrono_errores` 
CHANGE COLUMN `asincrono_id` `asincrono_id` INT(11) NOT NULL DEFAULT '0' ;
ALTER TABLE `asincrono_errores` 
ADD CONSTRAINT `FK_ASINCRONO_ERRORES_ASINCRONOS`
  FOREIGN KEY (`asincrono_id`)
  REFERENCES `asincronos` (`id`)
  ON DELETE RESTRICT
  ON UPDATE RESTRICT;
  
ALTER TABLE `asincrono_temporal_detalles` 
DROP FOREIGN KEY `FK_ASINCRONO_TEMPDET_ASINCRONOS`;
ALTER TABLE `asincrono_temporal_detalles` 
CHANGE COLUMN `asincrono_id` `asincrono_id` INT(11) NOT NULL ,
CHANGE COLUMN `asincrono_temporal_id` `asincrono_temporal_id` INT(11) NULL DEFAULT NULL ;
ALTER TABLE `asincrono_temporal_detalles` 
ADD CONSTRAINT `FK_ASINCRONO_TEMPDET_ASINCRONOS`
  FOREIGN KEY (`asincrono_id`)
  REFERENCES `asincronos` (`id`)
  ON DELETE RESTRICT
  ON UPDATE RESTRICT;
  
ALTER TABLE `asincrono_temporales` 
DROP FOREIGN KEY `FK_ASINCRONO_TEMP_ASINCRONOS`;
ALTER TABLE `asincrono_temporales` 
CHANGE COLUMN `asincrono_id` `asincrono_id` INT(11) NOT NULL ;
ALTER TABLE `asincrono_temporales` 
ADD CONSTRAINT `FK_ASINCRONO_TEMP_ASINCRONOS`
  FOREIGN KEY (`asincrono_id`)
  REFERENCES `asincronos` (`id`)
  ON DELETE RESTRICT
  ON UPDATE RESTRICT;  