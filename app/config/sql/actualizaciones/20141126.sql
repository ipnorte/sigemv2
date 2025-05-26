INSERT INTO `permisos` (`id`, `descripcion`, `url`, `order`, `main`, `quick`, 
`icon`, `activo`, `parent`) 
VALUES ('240', 'Imprimir Folios', '/pfyj/personas/imprimir_folios', 
'240', '1', '0', 'arrow_right2.gif', '1', '200');
insert into grupos_permisos values(1,240);



ALTER TABLE `proveedores` DROP INDEX `idx_codigo_acceso_ws` ,
ADD UNIQUE INDEX `idx_codigo_acceso_ws` (`codigo_acceso_ws` ASC, `id` ASC);


ALTER TABLE `proveedores` DROP COLUMN `genera_cuota_social`;
ALTER TABLE `proveedores` ADD COLUMN `genera_cuota_social` TINYINT(1) NOT NULL DEFAULT '1' AFTER `vendedores`;

ALTER TABLE `proveedor_plan_grillas` ADD COLUMN `observaciones` TEXT NULL AFTER `xls`;
