/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 13/05/2016
 */

INSERT INTO `permisos` (`id`, `descripcion`, `url`, `order`, `main`, `icon`, `activo`, `parent`) VALUES ('870', 'Solicitudes', '/ventas/solicitudes/search', '870', '1', 'arrow_right2.gif', '1', '800');
INSERT INTO `grupos_permisos` (`grupo_id`, `permiso_id`) VALUES ('1', '870');
INSERT INTO `permisos` (`id`, `descripcion`, `url`, `order`, `main`, `icon`, `activo`, `parent`) VALUES ('871', 'Solicitudes Ficha', '/ventas/solicitudes/ficha', '871', 0, NULL, '1', '800');
INSERT INTO `grupos_permisos` (`grupo_id`, `permiso_id`) VALUES ('1', '871');
INSERT INTO `permisos` (`id`, `descripcion`, `url`, `order`, `main`, `icon`, `activo`, `parent`) VALUES ('872', 'Solicitudes Estado Cuenta', '/ventas/solicitudes/estado_cuenta', '872', 0, NULL, '1', '800');
INSERT INTO `grupos_permisos` (`grupo_id`, `permiso_id`) VALUES ('1', '872');
INSERT INTO `permisos` (`id`, `descripcion`, `url`, `order`, `main`, `icon`, `activo`, `parent`) VALUES ('873', 'Solicitudes Nueva', '/ventas/solicitudes/alta', '873', 0, NULL, '1', '800');
INSERT INTO `grupos_permisos` (`grupo_id`, `permiso_id`) VALUES ('1', '873');


-- SACAR EL ID DEL GRUPO VENDEDORES
select * from grupos; -- 6
INSERT INTO `grupos_permisos` (`grupo_id`, `permiso_id`) VALUES ('6', '1');
INSERT INTO `grupos_permisos` (`grupo_id`, `permiso_id`) VALUES ('6', '2');
INSERT INTO `grupos_permisos` (`grupo_id`, `permiso_id`) VALUES ('6', '3');
INSERT INTO `grupos_permisos` (`grupo_id`, `permiso_id`) VALUES ('6', '860');
INSERT INTO `grupos_permisos` (`grupo_id`, `permiso_id`) VALUES ('6', '800');
INSERT INTO `grupos_permisos` (`grupo_id`, `permiso_id`) VALUES ('6', '870');
INSERT INTO `grupos_permisos` (`grupo_id`, `permiso_id`) VALUES ('6', '871');
INSERT INTO `grupos_permisos` (`grupo_id`, `permiso_id`) VALUES ('6', '872');
INSERT INTO `grupos_permisos` (`grupo_id`, `permiso_id`) VALUES ('6', '873');

update grupos set vista = 1, consultar = 1, agregar = 1, modificar = 1, borrar = 1 where id = 6;


ALTER TABLE `personas` ADD COLUMN `facebook_profile` varchar(100) DEFAULT NULL AFTER `e_mail`;
ALTER TABLE `personas` ADD COLUMN `twitter_profile` varchar(100) DEFAULT NULL AFTER `facebook_profile`;


ALTER TABLE `personas` 
ADD COLUMN `telefono_fijo_c` varchar(5) DEFAULT NULL AFTER `telefono_fijo`,
ADD COLUMN `telefono_fijo_n` varchar(15) DEFAULT NULL AFTER `telefono_fijo_c`,
ADD COLUMN `telefono_movil_c` varchar(5) DEFAULT NULL AFTER `telefono_movil`,
ADD COLUMN `telefono_movil_n` varchar(15) DEFAULT NULL AFTER `telefono_movil_c`,
ADD COLUMN `telefono_referencia_c` varchar(5) DEFAULT NULL AFTER `telefono_referencia`,
ADD COLUMN `telefono_referencia_n` varchar(15) DEFAULT NULL AFTER `telefono_referencia_c`;



ALTER TABLE `socios` 
DROP INDEX `idx_activo` ,
ADD INDEX `idx_activo` (`activo` ASC, `fecha_alta` ASC, `fecha_baja` ASC);

ALTER TABLE `socios` 
ADD INDEX `idx_fecha_alta` (`fecha_alta` ASC, `fecha_baja` ASC);



ALTER TABLE`socio_solicitudes` ADD COLUMN `vendedor_id` INT(11) NULL DEFAULT NULL AFTER `importe_cuota_social`;
ALTER TABLE `socio_solicitudes` ADD CONSTRAINT `fk_socio_solicitudes_1_vendedor_id` FOREIGN KEY (`vendedor_id`) REFERENCES `vendedores` (`id`);