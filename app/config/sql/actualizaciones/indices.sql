/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 03/04/2019
 */

ALTER TABLE `mutual_adicionales` 
ADD INDEX `idx_1` (`activo` ASC),
ADD INDEX `idx_2` (`deuda_calcula` ASC),
ADD INDEX `idx_3` (`periodo_desde` ASC, `periodo_hasta` ASC);

ALTER TABLE `mutual_productos`  ADD INDEX `idx_activo` (`activo` ASC);

ALTER TABLE `liquidacion_turnos` 
CHANGE COLUMN `turno` `turno` VARCHAR(12) NOT NULL ,
CHANGE COLUMN `codigo_empresa` `codigo_empresa` VARCHAR(12) NOT NULL ,
CHANGE COLUMN `codigo_reparticion` `codigo_reparticion` VARCHAR(15) NOT NULL DEFAULT ' ' ,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`id`, `turno`, `codigo_empresa`, `codigo_reparticion`);


ALTER TABLE `asincrono_temporales` 
DROP PRIMARY KEY,
ADD PRIMARY KEY (`id`, `asincrono_id`);

ALTER TABLE `asincrono_temporales` 
ADD COLUMN `clave_4` VARCHAR(50) NULL AFTER `clave_3`,
ADD COLUMN `clave_5` VARCHAR(50) NULL AFTER `clave_4`,
DROP INDEX `idx_clave_123` ,
ADD INDEX `idx_clave_123` (`clave_1` ASC, `clave_2` ASC, `clave_3` ASC, `asincrono_id` ASC, `clave_4` ASC,`clave_5` ASC),
ADD INDEX `idx_clave_4` (`clave_4` ASC),
ADD INDEX `idx_clave_5` (`clave_5` ASC);


ALTER TABLE `liquidaciones` 
ADD CONSTRAINT `fk_liquidaciones_1`
  FOREIGN KEY (`codigo_organismo`)
  REFERENCES `global_datos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `liquidaciones` 
DROP FOREIGN KEY `fk_liquidaciones_1`;
ALTER TABLE `liquidaciones` 
CHANGE COLUMN `codigo_organismo` `codigo_organismo` VARCHAR(12) NOT NULL ;
ALTER TABLE `liquidaciones` 
ADD CONSTRAINT `fk_liquidaciones_1`
  FOREIGN KEY (`codigo_organismo`)
  REFERENCES `global_datos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `liquidaciones` 
ADD INDEX `idx_enproceso` (`en_proceso` ASC);


update personas set provincia_id = NULL where provincia_id not in (select id from provincias);

ALTER TABLE `personas` 
ADD INDEX `fk_personas_1_idx` (`provincia_id` ASC);
;
ALTER TABLE `personas` 
ADD CONSTRAINT `fk_personas_1`
  FOREIGN KEY (`provincia_id`)
  REFERENCES `provincias` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
  
  
update personas set localidad_id = NULL where localidad_id not in (select id from localidades);

ALTER TABLE `personas` 
DROP FOREIGN KEY `fk_personas_2`;
ALTER TABLE `personas` 
CHANGE COLUMN `localidad_id` `localidad_id` INT(11) NULL DEFAULT NULL ;
ALTER TABLE `personas` 
ADD CONSTRAINT `fk_personas_2`
  FOREIGN KEY (`localidad_id`)
  REFERENCES `localidades` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;