/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 19/09/2016
 */

/* permisos */

insert into permisos(id,descripcion,url,`order`,activo,parent)
values(439,'Nueva Grilla Calculada','/proveedores/proveedor_planes/calcular_grilla',439,1,400);
insert into grupos_permisos (grupo_id,permiso_id) values(1,439);


/* proveedor_planes */
ALTER TABLE `proveedor_planes` ADD COLUMN `ayuda_economica` TINYINT(1) NULL DEFAULT 0 AFTER `modelo_solicitud`;

/* proveedor_plan_grillas */
ALTER TABLE `proveedor_plan_grillas` DROP COLUMN `tna`;
ALTER TABLE `proveedor_plan_grillas` DROP COLUMN `tnm`;
ALTER TABLE `proveedor_plan_grillas` DROP COLUMN `tem`;
ALTER TABLE `proveedor_plan_grillas` DROP COLUMN `gasto_admin`;
ALTER TABLE `proveedor_plan_grillas` DROP COLUMN `sellado`;
ALTER TABLE `proveedor_plan_grillas` DROP COLUMN `iva`;
ALTER TABLE `proveedor_plan_grillas` DROP COLUMN `iva_porc`;
ALTER TABLE `proveedor_plan_grillas` DROP COLUMN `gasto_admin_porc`;
ALTER TABLE `proveedor_plan_grillas` DROP COLUMN `sellado_porc`;
ALTER TABLE `proveedor_plan_grillas` DROP COLUMN `metodo_calculo`;

ALTER TABLE `proveedor_plan_grillas` ADD COLUMN `tna` DECIMAL(10,2) NULL DEFAULT 0 AFTER `cuotas`;
ALTER TABLE `proveedor_plan_grillas` ADD COLUMN `tem` DECIMAL(10,2) NULL DEFAULT 0 AFTER `tna`;
ALTER TABLE `proveedor_plan_grillas` ADD COLUMN `tnm` DECIMAL(10,2) NULL DEFAULT 0 AFTER `tna`;
ALTER TABLE `proveedor_plan_grillas` ADD COLUMN `gasto_admin` DECIMAL(10,2) NULL DEFAULT 0 AFTER `tem`;
ALTER TABLE `proveedor_plan_grillas` ADD COLUMN `sellado` DECIMAL(10,2) NULL DEFAULT 0 AFTER `gasto_admin`;
ALTER TABLE `proveedor_plan_grillas` ADD COLUMN `iva` DECIMAL(10,2) NULL DEFAULT 0 AFTER `sellado`;
ALTER TABLE `proveedor_plan_grillas` ADD COLUMN `metodo_calculo` INT(11) NULL DEFAULT 1 AFTER `iva`;

-- ///////////////////////////////////////////////////////////
-- ABRIL 2021
-- ///////////////////////////////////////////////////////////
ALTER TABLE `proveedor_plan_grillas` 
ADD COLUMN `tipo_cuota_gasto_admin` VARCHAR(12) NULL AFTER `modified`,
ADD COLUMN `tipo_cuota_sellado` VARCHAR(12) NULL AFTER `tipo_cuota_gasto_admin`,
ADD COLUMN `gasto_admin_base_calculo` INT NULL AFTER `tipo_cuota_sellado`,
ADD COLUMN `sellado_base_calculo` INT NULL AFTER `gasto_admin_base_calculo`,
ADD INDEX `fk_proveedor_plan_grillas_1_idx` (`tipo_cuota_gasto_admin` ASC),
ADD INDEX `fk_proveedor_plan_grillas_2_idx` (`tipo_cuota_sellado` ASC);
;
ALTER TABLE `proveedor_plan_grillas` 
ADD CONSTRAINT `fk_proveedor_plan_grillas_1`
  FOREIGN KEY (`tipo_cuota_gasto_admin`)
  REFERENCES `global_datos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_proveedor_plan_grillas_2`
  FOREIGN KEY (`tipo_cuota_sellado`)
  REFERENCES `global_datos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `proveedor_plan_grilla_cuotas` CHANGE COLUMN `calculo` `calculo` LONGTEXT NULL DEFAULT NULL ;
ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `detalle_calculo_plan` LONGTEXT NULL;

ALTER TABLE `orden_descuento_cuotas` ADD COLUMN `tna` DECIMAL(10,2) NULL DEFAULT 0;
ALTER TABLE `orden_descuento_cuotas` ADD COLUMN `tem` DECIMAL(10,2) NULL DEFAULT 0;
ALTER TABLE `orden_descuento_cuotas` ADD COLUMN `cft` DECIMAL(10,2) NULL DEFAULT 0;
ALTER TABLE `orden_descuento_cuotas` ADD COLUMN `capital` DECIMAL(10,2) NULL DEFAULT 0;
ALTER TABLE `orden_descuento_cuotas` ADD COLUMN `interes` DECIMAL(10,2) NULL DEFAULT 0;
ALTER TABLE `orden_descuento_cuotas` ADD COLUMN `iva` DECIMAL(10,2) NULL DEFAULT 0;
ALTER TABLE `orden_descuento_cuotas` ADD COLUMN `iva_porc` DECIMAL(10,2) NULL DEFAULT 0;

ALTER TABLE `orden_descuento_cobro_cuotas` ADD COLUMN `tna` DECIMAL(10,2) NULL DEFAULT 0;
ALTER TABLE `orden_descuento_cobro_cuotas` ADD COLUMN `tem` DECIMAL(10,2) NULL DEFAULT 0;
ALTER TABLE `orden_descuento_cobro_cuotas` ADD COLUMN `cft` DECIMAL(10,2) NULL DEFAULT 0;
ALTER TABLE `orden_descuento_cobro_cuotas` ADD COLUMN `capital` DECIMAL(10,2) NULL DEFAULT 0;
ALTER TABLE `orden_descuento_cobro_cuotas` ADD COLUMN `interes` DECIMAL(10,2) NULL DEFAULT 0;
ALTER TABLE `orden_descuento_cobro_cuotas` ADD COLUMN `iva` DECIMAL(10,2) NULL DEFAULT 0; 

-- ///////////////////////////////////////////////////////////

/* proveedor_plan_grilla_cuotas */
ALTER TABLE `proveedor_plan_grilla_cuotas` DROP COLUMN `tna`;
ALTER TABLE `proveedor_plan_grilla_cuotas` DROP COLUMN `tnm`;
ALTER TABLE `proveedor_plan_grilla_cuotas` DROP COLUMN `tem`;
ALTER TABLE `proveedor_plan_grilla_cuotas` DROP COLUMN `metodo_calculo`;
ALTER TABLE `proveedor_plan_grilla_cuotas` DROP COLUMN `sellado_porc`;
ALTER TABLE `proveedor_plan_grilla_cuotas` DROP COLUMN `gasto_admin_porc`;
ALTER TABLE `proveedor_plan_grilla_cuotas` DROP COLUMN `iva_porc`;
ALTER TABLE `proveedor_plan_grilla_cuotas` DROP COLUMN `intereses`;

ALTER TABLE `proveedor_plan_grilla_cuotas` ADD COLUMN `capital_puro` DECIMAL(10,2) NULL DEFAULT 0 AFTER `cft`;
ALTER TABLE `proveedor_plan_grilla_cuotas` ADD COLUMN `interes` DECIMAL(10,2) NULL DEFAULT 0 AFTER `capital_puro`;
ALTER TABLE `proveedor_plan_grilla_cuotas` ADD COLUMN `iva` DECIMAL(10,2) NULL DEFAULT 0 AFTER `interes`;
ALTER TABLE `proveedor_plan_grilla_cuotas` ADD COLUMN `gasto_admin` DECIMAL(10,2) NULL DEFAULT 0 AFTER `iva`;
ALTER TABLE `proveedor_plan_grilla_cuotas` ADD COLUMN `sellado` DECIMAL(10,2) NULL DEFAULT 0 AFTER `gasto_admin`;

/* mutual_producto_solicitudes */

ALTER TABLE `mutual_producto_solicitudes` DROP COLUMN `tna`;
ALTER TABLE `mutual_producto_solicitudes` DROP COLUMN `tnm`;
ALTER TABLE `mutual_producto_solicitudes` DROP COLUMN `tem`;
ALTER TABLE `mutual_producto_solicitudes` DROP COLUMN `cft`;
ALTER TABLE `mutual_producto_solicitudes` DROP COLUMN `capital_puro`;
ALTER TABLE `mutual_producto_solicitudes` DROP COLUMN `interes`;
ALTER TABLE `mutual_producto_solicitudes` DROP COLUMN `intereses`;
ALTER TABLE `mutual_producto_solicitudes` DROP COLUMN `iva`;
ALTER TABLE `mutual_producto_solicitudes` DROP COLUMN `iva_porc`;
ALTER TABLE `mutual_producto_solicitudes` DROP COLUMN `gasto_admin_porc`;
ALTER TABLE `mutual_producto_solicitudes` DROP COLUMN `gasto_admin`;
ALTER TABLE `mutual_producto_solicitudes` DROP COLUMN `sellado_porc`;
ALTER TABLE `mutual_producto_solicitudes` DROP COLUMN `sellado`;
ALTER TABLE `mutual_producto_solicitudes` DROP COLUMN `metodo_calculo`;
ALTER TABLE `mutual_producto_solicitudes` DROP COLUMN `ayuda_economica`;

ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `tna` DECIMAL(10,2) NULL DEFAULT 0 AFTER `importe_percibido`;
ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `tnm` DECIMAL(10,2) NULL DEFAULT 0 AFTER `tna`;
ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `tem` DECIMAL(10,2) NULL DEFAULT 0 AFTER `tnm`;
ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `cft` DECIMAL(10,2) NULL DEFAULT 0 AFTER `tnm`;
ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `capital_puro` DECIMAL(10,2) NULL DEFAULT 0 AFTER `cft`;
ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `interes` DECIMAL(10,2) NULL DEFAULT 0 AFTER `capital_puro`;
ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `iva` DECIMAL(10,2) NULL DEFAULT 0 AFTER `interes`;
ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `iva_porc` DECIMAL(10,2) NULL DEFAULT 0 AFTER `iva`;
ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `gasto_admin_porc` DECIMAL(10,2) NULL DEFAULT 0 AFTER `iva_porc`;
ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `gasto_admin` DECIMAL(10,2) NULL DEFAULT 0 AFTER `gasto_admin_porc`;
ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `sellado_porc` DECIMAL(10,2) NULL DEFAULT 0 AFTER `gasto_admin`;
ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `sellado` DECIMAL(10,2) NULL DEFAULT 0 AFTER `sellado_porc`;
ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `metodo_calculo` INT(11) NULL DEFAULT 1 AFTER `sellado`;
ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `ayuda_economica` TINYINT(1) NULL DEFAULT 0 AFTER `sellado`;

/* orden_descuentos */

ALTER TABLE `orden_descuentos` DROP COLUMN `tna`;
ALTER TABLE `orden_descuentos` DROP COLUMN `tnm`;
ALTER TABLE `orden_descuentos` DROP COLUMN `tem`;
ALTER TABLE `orden_descuentos` DROP COLUMN `cft`;
ALTER TABLE `orden_descuentos` DROP COLUMN `capital_puro`;
ALTER TABLE `orden_descuentos` DROP COLUMN `interes`;
ALTER TABLE `orden_descuentos` DROP COLUMN `intereses`;
ALTER TABLE `orden_descuentos` DROP COLUMN `iva`;
ALTER TABLE `orden_descuentos` DROP COLUMN `iva_porc`;
ALTER TABLE `orden_descuentos` DROP COLUMN `gasto_admin_porc`;
ALTER TABLE `orden_descuentos` DROP COLUMN `gasto_admin`;
ALTER TABLE `orden_descuentos` DROP COLUMN `sellado_porc`;
ALTER TABLE `orden_descuentos` DROP COLUMN `sellado`;
ALTER TABLE `orden_descuentos` DROP COLUMN `metodo_calculo`;
ALTER TABLE `orden_descuentos` DROP COLUMN `ayuda_economica`;

ALTER TABLE `orden_descuentos` ADD COLUMN `tna` DECIMAL(10,2) NULL DEFAULT 0 AFTER `importe_capital`;
ALTER TABLE `orden_descuentos` ADD COLUMN `tnm` DECIMAL(10,2) NULL DEFAULT 0 AFTER `tna`;
ALTER TABLE `orden_descuentos` ADD COLUMN `tem` DECIMAL(10,2) NULL DEFAULT 0 AFTER `tnm`;
ALTER TABLE `orden_descuentos` ADD COLUMN `cft` DECIMAL(10,2) NULL DEFAULT 0 AFTER `tnm`;
ALTER TABLE `orden_descuentos` ADD COLUMN `capital_puro` DECIMAL(10,2) NULL DEFAULT 0 AFTER `cft`;
ALTER TABLE `orden_descuentos` ADD COLUMN `interes` DECIMAL(10,2) NULL DEFAULT 0 AFTER `capital_puro`;
ALTER TABLE `orden_descuentos` ADD COLUMN `iva` DECIMAL(10,2) NULL DEFAULT 0 AFTER `interes`;
ALTER TABLE `orden_descuentos` ADD COLUMN `iva_porc` DECIMAL(10,2) NULL DEFAULT 0 AFTER `iva`;
ALTER TABLE `orden_descuentos` ADD COLUMN `gasto_admin_porc` DECIMAL(10,2) NULL DEFAULT 0 AFTER `iva_porc`;
ALTER TABLE `orden_descuentos` ADD COLUMN `gasto_admin` DECIMAL(10,2) NULL DEFAULT 0 AFTER `gasto_admin_porc`;
ALTER TABLE `orden_descuentos` ADD COLUMN `sellado_porc` DECIMAL(10,2) NULL DEFAULT 0 AFTER `gasto_admin`;
ALTER TABLE `orden_descuentos` ADD COLUMN `sellado` DECIMAL(10,2) NULL DEFAULT 0 AFTER `sellado_porc`;
ALTER TABLE `orden_descuentos` ADD COLUMN `metodo_calculo` INT(11) NULL DEFAULT 1 AFTER `sellado`;
ALTER TABLE `orden_descuentos` ADD COLUMN `ayuda_economica` TINYINT(1) NULL DEFAULT 0 AFTER `sellado`;


/* orden_descuento_cuota_items */
drop table if exists orden_descuento_cuota_items;
CREATE TABLE `orden_descuento_cuota_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orden_descuento_cuota_id` int(11) NOT NULL DEFAULT '0',
  `item` varchar(12) NOT NULL,
  `importe` decimal(10,2) NOT NULL DEFAULT '0.00',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_orden_descuento_cuotas_item_cuota` (`orden_descuento_cuota_id`),
  KEY `idx_orden_descuento_cuotas_item` (`item`),
  CONSTRAINT `FK_orden_descuento_cuotas_item_cuota` FOREIGN KEY (`orden_descuento_cuota_id`) REFERENCES `orden_descuento_cuotas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


/*********************************************************************************
CONFIGURACION DE PLANTILLAS A NIVEL PRODUCTO
**********************************************************************************/
INSERT INTO `global_datos` (`id`, `concepto_1`, `concepto_2`, `concepto_3`, `concepto_4`, `logico_1`, `logico_2`, `logico_3`, `entero_1`, `entero_2`, `decimal_1`, `decimal_2`, `fecha_1`, `fecha_2`, `texto_1`, `texto_2`, `usuario_id`, `user_created`, `user_modified`, `created`, `modified`) VALUES ('MUTUIMPR', 'CONFIGURACION IMPRESION SOLICITUDES', '', '', '', '0', '0', '0', 'NULL', 'NULL', 'NULL', 'NULL', '2016-09-24', '2016-09-24', '', '', '0', 'NULL', 'ADRIAN', '2016-09-24 11:25:03', '2016-09-24 11:25:03');
INSERT INTO `global_datos` (`id`, `concepto_1`, `concepto_2`, `concepto_3`, `concepto_4`, `logico_1`, `logico_2`, `logico_3`, `entero_1`, `entero_2`, `decimal_1`, `decimal_2`, `fecha_1`, `fecha_2`, `texto_1`, `texto_2`, `usuario_id`, `user_created`, `user_modified`, `created`, `modified`) VALUES ('MUTUIMPR0001', 'MODELO ORDEN CONSUMO ESTANDAR', 'imprimir_orden_pdf', 'TEMPLATE_OCOMP', '', '1', '0', '0', '0', 'NULL', 'NULL', 'NULL', '2016-09-24', '2016-09-24', '', '', '0', 'NULL', 'ADRIAN', '2016-09-24 13:33:23', '2016-09-24 13:34:06');
INSERT INTO `global_datos` (`id`, `concepto_1`, `concepto_2`, `concepto_3`, `concepto_4`, `logico_1`, `logico_2`, `logico_3`, `entero_1`, `entero_2`, `decimal_1`, `decimal_2`, `fecha_1`, `fecha_2`, `texto_1`, `texto_2`, `usuario_id`, `user_created`, `user_modified`, `created`, `modified`) VALUES ('MUTUIMPR1001', 'MODELO CREDITO ESTANDAR', 'imprimir_credito_mutual_pdf', 'TEMPLATE_EXPTE', '', '1', '0', '0', '1', 'NULL', 'NULL', 'NULL', '2016-09-24', '2016-09-24', '', '', '0', 'NULL', 'ADRIAN', '2016-09-24 11:31:07', '2016-09-24 13:33:31');

/* proveedor_plan_anexos */
CREATE TABLE `proveedor_plan_anexos` (
  `proveedor_plan_id` int(11) NOT NULL,
  `codigo_anexo` varchar(12) NOT NULL,
  PRIMARY KEY (`proveedor_plan_id`,`codigo_anexo`),
  KEY `fk_proveedor_plan_anexos_proveedor_planes1_idx` (`proveedor_plan_id`),
  CONSTRAINT `fk_proveedor_plan_anexos_proveedor_planes1_idx` FOREIGN KEY (`proveedor_plan_id`) REFERENCES `proveedor_planes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



ALTER TABLE `proveedor_planes` 
ADD COLUMN `modelo_solicitud_codigo` VARCHAR(12) NULL AFTER `modelo_solicitud`;
ALTER TABLE `proveedor_planes` 
ADD CONSTRAINT `fk_proveedor_planes_modelo_solicitud`
  FOREIGN KEY (`modelo_solicitud_codigo`)
  REFERENCES `global_datos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

update proveedor_planes p, global_datos g
set p.modelo_solicitud_codigo = g.id
where ltrim(rtrim(p.modelo_solicitud)) = ltrim(rtrim(g.concepto_2));


ALTER TABLE `mutual_productos` 
ADD COLUMN `modelo_solicitud_codigo` VARCHAR(12) NOT NULL DEFAULT 'MUTUIMPR0001' AFTER `prestamo`;
ALTER TABLE `mutual_productos` 
ADD CONSTRAINT `fk_mutual_productos_1_modelo_solicitud`
  FOREIGN KEY (`modelo_solicitud_codigo`)
  REFERENCES `global_datos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

update mutual_productos set modelo_solicitud_codigo = 'MUTUIMPR0001';

CREATE TABLE `mutual_producto_anexos` (
  `mutual_producto_id` int(11) NOT NULL,
  `codigo_anexo` varchar(12) NOT NULL,
  PRIMARY KEY (`mutual_producto_id`,`codigo_anexo`),
  KEY `fk_mutual_producto_anexos_mutual_producto_id_idx` (`mutual_producto_id`),
  CONSTRAINT `fk_mutual_producto_anexos_mutual_producto_id_idx` FOREIGN KEY (`mutual_producto_id`) REFERENCES `mutual_productos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*
    *************************************************
    ** OJO NO EJECUTAR EN TODAS LAS BASES DE DATOS **
    *************************************************
    FIJARSE EN EL mutual.ini que anexos tienen habilitados
;------------------------------------------------------------------
;AUTORIZACION DE DEBITO EN ORDEN DE CONSUMOS
ocom_imprime_auto_debito_nacion = 0
ocom_imprime_auto_debito_bcocba = 0
ocom_imprime_auto_debito_margen = 0
ocom_imprime_pagare_new_page = 0
ocom_imprime_pago_directo_rio = 0
ocom_imprime_mutuo = 1

CORRER EN LA MUTUAL 22

*/


-- OJO estos INSERTS ejecutarlos solamente donde tengan anexos a imprimir (por ejemplo: m22s)
INSERT INTO `global_datos` (`id`, `concepto_1`, `concepto_2`, `concepto_3`, `concepto_4`, `logico_1`, `logico_2`, `logico_3`, `entero_1`, `entero_2`, `decimal_1`, `decimal_2`, `fecha_1`, `fecha_2`, `texto_1`, `texto_2`, `usuario_id`, `user_created`, `user_modified`, `created`, `modified`) VALUES ('MUTUIMPR1002', 'MODELO AYUDA ECONOMICA RES 14818/03', 'imprimir_ayuda_mutual_pdf', 'TEMPLATE_EXPTE', '', '1', '0', '0', '1', 'NULL', 'NULL', 'NULL', '2016-09-24', '2016-09-24', '', '', '0', 'NULL', 'ADRIAN', '2016-09-24 11:32:19', '2016-09-24 13:33:47');
INSERT INTO `global_datos` (`id`, `concepto_1`, `concepto_2`, `concepto_3`, `concepto_4`, `logico_1`, `logico_2`, `logico_3`, `entero_1`, `entero_2`, `decimal_1`, `decimal_2`, `fecha_1`, `fecha_2`, `texto_1`, `texto_2`, `usuario_id`, `user_created`, `user_modified`, `created`, `modified`) VALUES ('MUTUIMPR1003', 'MODELO CREDITO MIS', 'imprimir_credito_mutual_mis_pdf', 'TEMPLATE_EXPTE', '', '1', '0', '0', '1', 'NULL', 'NULL', 'NULL', '2016-09-24', '2016-09-24', '', '', '0', 'NULL', 'ADRIAN', '2016-09-24 11:33:14', '2016-09-24 13:33:53');
INSERT INTO `global_datos` (`id`, `concepto_1`, `concepto_2`, `concepto_3`, `concepto_4`, `logico_1`, `logico_2`, `logico_3`, `entero_1`, `entero_2`, `decimal_1`, `decimal_2`, `fecha_1`, `fecha_2`, `texto_1`, `texto_2`, `usuario_id`, `user_created`, `user_modified`, `created`, `modified`) VALUES ('MUTUIMPR9001', 'DEBITO BANCO NACION', 'ocom_imprime_auto_debito_nacion', 'ANEXO', '', '1', '0', '0', '9', 'NULL', 'NULL', 'NULL', '2016-09-24', '2016-09-24', '', '', '0', 'NULL', 'ADRIAN', '2016-09-24 11:36:55', '2016-09-24 13:28:37');
INSERT INTO `global_datos` (`id`, `concepto_1`, `concepto_2`, `concepto_3`, `concepto_4`, `logico_1`, `logico_2`, `logico_3`, `entero_1`, `entero_2`, `decimal_1`, `decimal_2`, `fecha_1`, `fecha_2`, `texto_1`, `texto_2`, `usuario_id`, `user_created`, `user_modified`, `created`, `modified`) VALUES ('MUTUIMPR9002', 'DEBITO BANCO CORDOBA', 'ocom_imprime_auto_debito_bcocba', 'ANEXO', '', '1', '0', '0', '9', 'NULL', 'NULL', 'NULL', '2016-09-24', '2016-09-24', '', '', '0', 'NULL', 'ADRIAN', '2016-09-24 11:37:28', '2016-09-24 13:28:42');
INSERT INTO `global_datos` (`id`, `concepto_1`, `concepto_2`, `concepto_3`, `concepto_4`, `logico_1`, `logico_2`, `logico_3`, `entero_1`, `entero_2`, `decimal_1`, `decimal_2`, `fecha_1`, `fecha_2`, `texto_1`, `texto_2`, `usuario_id`, `user_created`, `user_modified`, `created`, `modified`) VALUES ('MUTUIMPR9003', 'DEBITO MARGEN COMERCIAL', 'ocom_imprime_auto_debito_margen', 'ANEXO', '', '1', '0', '0', '9', 'NULL', 'NULL', 'NULL', '2016-09-24', '2016-09-24', '', '', '0', 'NULL', 'ADRIAN', '2016-09-24 11:39:34', '2016-09-24 13:28:48');
INSERT INTO `global_datos` (`id`, `concepto_1`, `concepto_2`, `concepto_3`, `concepto_4`, `logico_1`, `logico_2`, `logico_3`, `entero_1`, `entero_2`, `decimal_1`, `decimal_2`, `fecha_1`, `fecha_2`, `texto_1`, `texto_2`, `usuario_id`, `user_created`, `user_modified`, `created`, `modified`) VALUES ('MUTUIMPR9004', 'PAGO DIRECTO SANTANDER RIO', 'ocom_imprime_pago_directo_rio', 'ANEXO', '', '1', '0', '0', '9', 'NULL', 'NULL', 'NULL', '2016-09-24', '2016-09-24', '', '', '0', 'NULL', 'ADRIAN', '2016-09-24 11:41:07', '2016-09-24 13:28:54');
INSERT INTO `global_datos` (`id`, `concepto_1`, `concepto_2`, `concepto_3`, `concepto_4`, `logico_1`, `logico_2`, `logico_3`, `entero_1`, `entero_2`, `decimal_1`, `decimal_2`, `fecha_1`, `fecha_2`, `texto_1`, `texto_2`, `usuario_id`, `user_created`, `user_modified`, `created`, `modified`) VALUES ('MUTUIMPR9005', 'CONTRATO MUTUO', 'ocom_imprime_mutuo', 'ANEXO', '', '1', '0', '0', '9', 'NULL', 'NULL', 'NULL', '2016-09-24', '2016-09-24', '', '', '0', 'NULL', 'ADRIAN', '2016-09-24 11:43:05', '2016-09-24 13:28:59');
INSERT INTO `global_datos` (`id`, `concepto_1`, `concepto_2`, `concepto_3`, `concepto_4`, `logico_1`, `logico_2`, `logico_3`, `entero_1`, `entero_2`, `decimal_1`, `decimal_2`, `fecha_1`, `fecha_2`, `texto_1`, `texto_2`, `usuario_id`, `user_created`, `user_modified`, `created`, `modified`) VALUES ('MUTUIMPR9006', 'MINUTA CONTRATO MUTUO', 'ocom_imprime_mutuo_minuta', 'ANEXO', '', '1', '0', '0', '9', 'NULL', 'NULL', 'NULL', '2016-09-24', '2016-09-24', '', '', '0', 'NULL', 'ADRIAN', '2016-09-24 11:43:45', '2016-09-24 13:29:04');
INSERT INTO `global_datos` (`id`, `concepto_1`, `concepto_2`, `concepto_3`, `concepto_4`, `logico_1`, `logico_2`, `logico_3`, `entero_1`, `entero_2`, `decimal_1`, `decimal_2`, `fecha_1`, `fecha_2`, `texto_1`, `texto_2`, `usuario_id`, `user_created`, `user_modified`, `created`, `modified`) VALUES ('MUTUIMPR9007', 'AUTO.DEBITO BCO. PCIA BS.AS.', 'ocom_imprime_pago_directo_bco_pcia_bsas', 'ANEXO', '', '1', '0', '0', '9', 'NULL', 'NULL', 'NULL', '2016-10-04', '2016-10-04', '', '', '0', 'NULL', 'ADRIAN', '2016-10-04 15:09:12', '2016-10-04 15:09:12');


delete from proveedor_plan_anexos;
insert into proveedor_plan_anexos(proveedor_plan_id,codigo_anexo)
select p.id,g.id from proveedor_planes p, global_datos g
where ltrim(rtrim(g.concepto_3)) = 'ANEXO';

delete from mutual_producto_anexos;
insert into mutual_producto_anexos(mutual_producto_id,codigo_anexo)
select p.id,g.id from mutual_productos p, global_datos g
where ltrim(rtrim(g.concepto_3)) = 'ANEXO';