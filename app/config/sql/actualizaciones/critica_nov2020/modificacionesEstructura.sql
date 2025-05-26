-- // INSERTO EL MENU DE CONFIGURACIONES
INSERT INTO `permisos` (`id`, `descripcion`, `url`, `order`, `main`, `quick`, `icon`, `activo`, `parent`) 
VALUES ('61', 'Liquidador', '/config/global_datos/liquidaciones', '61', '1', '0', 'arrow_right2.gif', '1', '50');
insert into grupos_permisos values(1,61);

ALTER TABLE `global_datos` 
ADD COLUMN `entero_3` INT NULL AFTER `modified`,
ADD COLUMN `entero_4` INT NULL AFTER `entero_3`,
ADD COLUMN `entero_5` INT NULL AFTER `entero_4`,
ADD COLUMN `entero_6` INT NULL AFTER `entero_5`,
ADD COLUMN `entero_7` INT NULL AFTER `entero_6`,
ADD COLUMN `concepto_5` VARCHAR(100) NULL AFTER `concepto_4`,
ADD INDEX `concepto_2` (`concepto_2` ASC),
ADD INDEX `concepto_3` (`concepto_3` ASC),
ADD INDEX `concepto_4` (`concepto_4` ASC),
ADD INDEX `concepto_5` (`concepto_5` ASC);
;

UPDATE global_datos 
set entero_3 = 1, entero_4 = 2, entero_5 = 3,
entero_6 = entero_1, entero_7 = entero_2,
concepto_4 = 'SP_LIQUIDA_DEUDA_CBU_PERIODO_GENERAL', concepto_5 = 'SP_LIQUIDA_DEUDA_CBU_MORA_GENERAL'
where id like 'MUTUCORG22%' or id = 'MUTUCORGMUTU' or id = 'MUTUCORGPVOL';

UPDATE global_datos 
set concepto_4 = 'SP_LIQUIDA_DEUDA_CJPC_GENERAL', concepto_5 = ''
where id like 'MUTUCORG77%';

UPDATE global_datos 
set concepto_4 = 'SP_LIQUIDA_DEUDA_ANSES_GENERAL', concepto_5 = ''
where id like 'MUTUCORG66%';

-- /// SETEO CONVENIO ANSES MUTUAL AMAN
update global_datos set entero_1 = 324109, entero_2 = 397109 where id = 'MUTUCORG6601';
-- /// SETEO CONVENIO CJPC MUTUAL AMAN
update global_datos set entero_1 = 2070, entero_2 = 2071 where id = 'MUTUCORG7701';

-- MUTUAL AMAN
update global_datos
set decimal_2 = decimal_1
where id like 'MUTUCORG22%'
AND decimal_2 < decimal_1;


/* TABLA BANCOS */
ALTER TABLE `bancos` 
ADD COLUMN `metodo_str_encode` VARCHAR(100) NULL AFTER `parametros_intercambio`,
ADD COLUMN `metodo_str_decode` VARCHAR(100) NULL AFTER `metodo_str_encode`;

-- si ya existen
ALTER TABLE `bancos` 
CHANGE COLUMN `metodo_str_encode` `metodo_str_encode` VARCHAR(100) NULL DEFAULT NULL ,
CHANGE COLUMN `metodo_str_decode` `metodo_str_decode` VARCHAR(100) NULL DEFAULT NULL ;



-- ///