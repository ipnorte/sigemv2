/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 01/04/2019
 */

ALTER TABLE `asincrono_temporal_detalles` DROP INDEX `FK_asincrono_temporal_detalles` ;
ALTER TABLE `asincrono_temporales` DROP FOREIGN KEY `FK_ASINCRONO_TEMP_ASINCRONOS`;
ALTER TABLE `asincrono_temporales` DROP INDEX `idx_asincrono` ;
ALTER TABLE `asincrono_errores` DROP FOREIGN KEY `FK_ASINCRONO_ERRORES_ASINCRONOS`;
ALTER TABLE `asincrono_errores` DROP INDEX `FK_asincrono_errores_asincronos` ;

ALTER TABLE `asincronos` ENGINE = MyISAM ;
ALTER TABLE `asincrono_temporal_detalles` ENGINE = MyISAM ;
ALTER TABLE `asincrono_temporales` ENGINE = MyISAM ;
ALTER TABLE `asincrono_errores` ENGINE = MyISAM ;

