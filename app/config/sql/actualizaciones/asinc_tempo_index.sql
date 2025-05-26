/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 07/10/2018
 */

ALTER TABLE `asincrono_temporales` 
DROP INDEX `idx_clave_1` ,
ADD INDEX `idx_clave_1` (`clave_1` ASC, `asincrono_id` ASC),
DROP INDEX `idx_clave_2` ,
ADD INDEX `idx_clave_2` (`clave_2` ASC, `asincrono_id` ASC),
DROP INDEX `idx_clave_3` ,
ADD INDEX `idx_clave_3` (`clave_3` ASC, `asincrono_id` ASC),
ADD INDEX `idx_clave_123` (`clave_1` ASC, `clave_2` ASC, `clave_3` ASC, `asincrono_id` ASC);
;
