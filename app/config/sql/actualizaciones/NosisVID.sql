/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 10/08/2019
 */

insert into global_datos(id,concepto_1,concepto_2,concepto_3,concepto_4,logico_1,entero_1)
values('PERSNVID','NOSIS VALIDADOR DE IDENTIDAD','https://ws02.nosis.com/api/','62378','963665',1,3);

ALTER TABLE `persona_beneficios` ADD COLUMN `cbu_nosis_validado` TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE `persona_beneficios` ADD COLUMN `cbu_nosis_fecha_validacion` TIMESTAMP NULL AFTER `cbu_nosis_validado`;

ALTER TABLE `personas` ADD COLUMN `celular_nosis_validado` TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE `personas` ADD COLUMN `celular_nosis_fecha_validacion` TIMESTAMP NULL AFTER `celular_nosis_validado`;
