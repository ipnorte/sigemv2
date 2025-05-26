alter table `sigem_db`.`recibo_detalles` add column `orden_descuento_cobro_id` int(11) DEFAULT '0' NULL after `cliente_factura_id`;
alter table `sigem_db`.`recibo_detalles` add column `orden_descuento_cuota_id` int(11) DEFAULT '0' NULL after `orden_descuento_cobro_id`,change `orden_descuento_cobro_cuota_id` `orden_descuento_cobro_cuota_id` int(11) default '0' NULL ;
alter table `sigem_db`.`tipo_documentos` add column `copias` int(1) DEFAULT '1' NULL after `destino`, add column `longitud_pagina` int(2) DEFAULT '0' NULL after `copias`;
alter table `sigem_db`.`recibo_detalles` change `persona_id` `persona_id` int(11) default '0' NULL , change `socio_id` `socio_id` int(11) default '0' NULL , change `recibo_id` `recibo_id` int(11) default '0' NOT NULL;
alter table `sigem_db`.`recibos` add column `letra` varchar(1) NULL after `tipo_documento`;
alter table `sigem_db`.`proveedor_facturas` add column `cancelacion_orden_id` int(11) DEFAULT '0' NULL after `liquidacion_id`,change `orden_descuento_cobro_id` `orden_descuento_cobro_id` int(11) default '0' NULL ;
alter table `sigem_db`.`proveedor_facturas` add column `socio_id` int(11) DEFAULT '0' NULL after `proveedor_tipo_asiento_id`;
alter table `sigem_db`.`banco_cheque_terceros` change `banco_id` `banco_id` char(5) NULL ;
update sigem_db.banco_cheque_terceros set banco_id = right(concat('00000', banco_id),5) ;
alter table `sigem_db`.`orden_descuento_cobros` add column `proveedor_origen_fondo_id` int(11) DEFAULT '0' NULL after `cancelacion_orden_id`;
alter table `sigem_db`.`proveedor_facturas` add column `orden_descuento_id` int(11) DEFAULT '0' NULL after `socio_id`;
alter table `sigem_db`.`cliente_facturas` add column `socio_id` int(11) DEFAULT '0' NULL after `cliente_tipo_asiento_id`, add column `orden_descuento_id` int(11) DEFAULT '0' NULL after `socio_id`;
alter table `sigem_db`.`cancelacion_ordenes` add column `cliente_factura_id` int(11) DEFAULT '0' NULL after `proveedor_factura_id`;

alter table `sigem_db`.`recibos` add column `importe_cancela` decimal(15,2) DEFAULT '0.00' NULL after `aporte_socio`;
alter table `sigem_db`.`orden_pago_detalles` add column `orden_pago_detalle_id` int(11) DEFAULT '0' NULL after `proveedor_factura_id`;
alter table `sigem_db`.`recibo_detalles` add column `recibo_detalle_id` int(11) DEFAULT '0' NULL after `cliente_factura_id`;
alter table `sigem_db`.`cliente_facturas` add column `cancelacion_orden_id` int(11) DEFAULT '0' NULL after `liquidacion_id`;

ALTER TABLE `sigem_db`.`recibo_detalles` CHANGE `orden_descuento_cuota_id` `orden_descuento_id` INT(11) DEFAULT 0 NULL; 
ALTER TABLE `sigem_db`.`banco_cuenta_movimientos` ADD COLUMN `anulado` TINYINT(1) DEFAULT 0 NULL AFTER `co_plan_cuenta_id`, ADD COLUMN `reemplazar` TINYINT(1) DEFAULT 0 NULL AFTER `anulado`;

ALTER TABLE `sigem_db`.`orden_caja_cobros` CHANGE `importe_contado` `importe_contado` DECIMAL(10,2) DEFAULT 0.00 NULL, CHANGE `importe_orden_pago` `importe_orden_pago` DECIMAL(10,2) DEFAULT 0.00 NULL, ADD COLUMN `proveedor_factura_id` INT(11) DEFAULT 0 NULL AFTER `importe_orden_pago`, ADD COLUMN `importe_factura` DECIMAL(10,2) DEFAULT 0.00 NULL AFTER `proveedor_factura_id`; 

ALTER TABLE `sigem_db`.`proveedor_liquidaciones` ADD COLUMN `orden_caja_cobro_id` INT(11) DEFAULT 0 NULL AFTER `cancelacion_orden_id`, ADD COLUMN `orden_descuento_cobro_id` INT(11) DEFAULT 0 NULL AFTER `orden_caja_cobro_id`; 


ALTER TABLE `sigem_db`.`cancelacion_ordenes` ADD COLUMN `credito_proveedor_factura_id` INT(11) DEFAULT 0 NULL AFTER `banco_cuenta_movimiento_id`; 

ALTER TABLE `sigem_db`.`socio_reintegros` ADD COLUMN `banco_cuenta_movimiento_id` INT(11) DEFAULT 0 NULL AFTER `orden_pago_id`; 
ALTER TABLE `sigem_db`.`orden_descuento_cobro_cuotas` ADD COLUMN `banco_cuenta_movimiento_id` INT(11) DEFAULT 0 NULL AFTER `debito_reverso_id`; 


ALTER TABLE `sigem_db`.`recibo_detalles` ADD COLUMN `socio_reintegro_id` INT(11) DEFAULT 0 NULL AFTER `orden_descuento_cobro_cuota_id`; 
ALTER TABLE `sigem_db`.`socio_reintegros` ADD COLUMN `recibo_id` INT(11) DEFAULT 0 NULL AFTER `banco_cuenta_movimiento_id`; 


ALTER TABLE `sigem_db`.`banco_cuenta_saldos` ADD COLUMN `numero_extracto` VARCHAR(30) NULL AFTER `tipo_conciliacion`, ADD COLUMN `fecha_extracto` DATE NULL AFTER `numero_extracto`, ADD COLUMN `saldo_extracto` DECIMAL(15,2) DEFAULT 0.00 NULL AFTER `fecha_extracto`;
ALTER TABLE `sigem_db`.`banco_cuentas` ADD COLUMN `numero_extracto` VARCHAR(30) NULL AFTER `banco_cuenta_saldo_alta_id`, ADD COLUMN `fecha_extracto` DATE NULL AFTER `numero_extracto`, ADD COLUMN `saldo_extracto` DECIMAL(15,2) DEFAULT 0.00 NULL AFTER `fecha_extracto`; 
ALTER TABLE `sigem_db`.`banco_cuenta_movimientos` ADD COLUMN `banco_cuenta_saldo_id` INT(11) DEFAULT 0 NULL AFTER `conciliado`; 

ALTER TABLE `sigem_db`.`banco_cuenta_saldos` ADD COLUMN `debe` DECIMAL(15,2) NULL AFTER `fecha_extracto`, ADD COLUMN `haber` DECIMAL(15,2) NULL AFTER `debe`; 

ALTER TABLE `sigem_db`.`banco_cuentas` CHANGE `numero_planilla` `numero_planilla` VARCHAR(10) DEFAULT '0' NULL; 
ALTER TABLE `sigem_db`.`banco_cuenta_saldos` ADD COLUMN `fecha_anterior` DATE NULL AFTER `saldo_extracto`; 
ALTER TABLE `sigem_db`.`banco_cuenta_saldos` ADD COLUMN `asincrono_id` INT(11) DEFAULT 0 NULL AFTER `fecha_anterior`; 


/*==============================================================
* CONTABILIDAD
==============================================================*/

ALTER TABLE `sigem_db`.`co_ejercicios` ADD COLUMN `fecha_proceso` DATE NULL AFTER `fecha_asiento`;

CREATE TABLE `mutual_proceso_asientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `co_ejercicio_id` int(11) DEFAULT '0',
  `fecha_desde` date DEFAULT NULL,
  `fecha_hasta` date DEFAULT NULL,
  `cerrado` int(1) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8



CREATE TABLE `mutual_asientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mutual_proceso_asiento_id` int(11) DEFAULT '0',
  `co_asiento_id` int(11) DEFAULT '0',
  `nro_asiento` int(11) DEFAULT '0',
  `co_ejercicio_id` int(11) DEFAULT '0',
  `fecha` date DEFAULT NULL,
  `tipo_documento` varchar(3) DEFAULT NULL,
  `nro_documento` varchar(12) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `debe` decimal(15,2) DEFAULT NULL,
  `haber` decimal(15,2) DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8


CREATE TABLE `mutual_asiento_renglones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mutual_asiento_id` int(11) DEFAULT '0',
  `fecha` date DEFAULT NULL,
  `co_plan_cuenta_id` int(11) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `debe` decimal(15,2) DEFAULT NULL,
  `haber` decimal(15,2) DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8



CREATE TABLE `mutual_temporal_asiento_renglones` (
  `co_plan_cuenta_id` int(11) NOT NULL DEFAULT '0',
  `importe` decimal(15,2) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT '',
  `error` int(1) DEFAULT '0',
  `error_descripcion` varchar(100) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8



CREATE TABLE `cliente_tipo_asientos` (
  `id` int(11) NOT NULL DEFAULT '0',
  `concepto` varchar(100) DEFAULT NULL,
  `tipo_asiento` varchar(2) NOT NULL DEFAULT 'GR'
) ENGINE=InnoDB DEFAULT CHARSET=utf8



CREATE TABLE `cliente_tipo_asiento_renglones` (
  `id` int(11) NOT NULL DEFAULT '0',
  `variable` varchar(5) DEFAULT NULL,
  `proveedor_tipo_asiento_id` int(11) DEFAULT '0',
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `debe_haber` varchar(1) DEFAULT 'D',
  `tipo_asiento` varchar(2) DEFAULT 'GR'
) ENGINE=MyISAM DEFAULT CHARSET=utf8



ALTER TABLE `sigem_db`.`co_ejercicios` ADD COLUMN `fecha_proceso` DATE NULL AFTER `fecha_asiento`; 

INSERT INTO `sigem_db`.`permisos`(`id`,`descripcion`,`url`,`order`,`main`,`quick`,`icon`,`activo`,`parent`,`obs`,`created`,`modified`) VALUES ( '710','Tipos de Asientos','/clientes/cliente_tipo_asientos','710','1','0','arrow_right2.gif','1','700',NULL,NULL,NULL); 
ALTER TABLE `sigem_db`.`mutual_asiento_renglones` ADD COLUMN `mutual_proceso_asiento_id` INT(11) DEFAULT 0 NULL AFTER `mutual_asiento_id`; 
ALTER TABLE `sigem_db`.`proveedor_facturas` ADD COLUMN `proveedor_tipo_asiento_id` INT(11) DEFAULT 0 NULL AFTER `ejercicio_id`; 

ALTER TABLE `sigem_db`.`mutual_asiento_renglones` ADD COLUMN `cuenta` VARCHAR(20) DEFAULT '' NULL AFTER `co_plan_cuenta_id`, ADD COLUMN `descripcion` VARCHAR(100) DEFAULT '' NULL AFTER `cuenta`; 
ALTER TABLE `sigem_db`.`mutual_temporal_asiento_renglones` ADD COLUMN `cuenta` VARCHAR(20) DEFAULT '' NULL AFTER `co_plan_cuenta_id`, ADD COLUMN `descripcion` VARCHAR(100) DEFAULT '' NULL AFTER `cuenta`; 
ALTER TABLE `sigem_db`.`co_asientos` ADD COLUMN `mutual_asiento_id` INT(11) DEFAULT 0 NULL AFTER `id`, CHANGE `nro_asiento` `nro_asiento` INT(11) DEFAULT 0 NULL, CHANGE `co_ejercicio_id` `co_ejercicio_id` INT(11) DEFAULT 0 NULL; 
ALTER TABLE `sigem_db`.`co_ejercicios` CHANGE `nivel` `nivel` INT(1) DEFAULT 0 NULL, CHANGE `nivel_1` `nivel_1` INT(1) DEFAULT 0 NULL, CHANGE `nivel_2` `nivel_2` INT(1) DEFAULT 0 NULL, CHANGE `nivel_3` `nivel_3` INT(1) DEFAULT 0 NULL, CHANGE `nivel_4` `nivel_4` INT(1) DEFAULT 0 NULL, CHANGE `nivel_5` `nivel_5` INT(1) DEFAULT 0 NULL, CHANGE `nivel_6` `nivel_6` INT(1) DEFAULT 0 NULL, CHANGE `nro_asiento` `nro_asiento` INT(11) DEFAULT 1 NULL; 



INSERT INTO `sigem_db`.`permisos`(`id`,`descripcion`,`url`,`order`,`main`,`quick`,`icon`,`activo`,`parent`,`obs`,`created`,`modified`) VALUES ( '590','Libro diario PDF','/contabilidad/listados/libro_diario_PDF','590','0','0',NULL,'1','500',NULL,NULL,NULL); 
INSERT INTO grupos_permisos VALUES(1,590);


INSERT INTO `sigem_db`.`permisos`(`id`,`descripcion`,`url`,`order`,`main`,`quick`,`icon`,`activo`,`parent`,`obs`,`created`,`modified`) VALUES ( '591','Aprobar Asientos','/contabilidad/mutual_procesar_asientos/aprobar_asientos','591','0','0',NULL,'1','500',NULL,NULL,NULL); 
INSERT INTO grupos_permisos VALUES(1,591);


ALTER TABLE `sigem_db`.`co_ejercicios` ADD COLUMN `look` TINYINT(1) DEFAULT 0 NULL AFTER `nro_asiento`; 


INSERT INTO `sigem_db`.`permisos`(`id`,`descripcion`,`url`,`order`,`main`,`quick`,`icon`,`activo`,`parent`,`obs`,`created`,`modified`) VALUES ( '592','Plan de Cuenta','/contabilidad/listados/plan_cuenta_pdf','592','0','0',NULL,'1','500',NULL,NULL,NULL); 
INSERT INTO grupos_permisos VALUES(1,592);



ALTER TABLE `sigem_db`.`co_asientos` ADD COLUMN `borrado` TINYINT(1) DEFAULT 0 NULL AFTER `haber`; 


ALTER TABLE `sigem_db`.`mutual_asientos` ADD COLUMN `error` INT(1) DEFAULT 0 NULL AFTER `haber`; 


ALTER TABLE `sigem_db`.`mutual_temporal_asiento_renglones` ADD COLUMN `modulo` VARCHAR(12) DEFAULT '' NULL AFTER `error_descripcion`, ADD COLUMN `tipo_asiento` INT(4) DEFAULT 0 NULL AFTER `modulo`; 
ALTER TABLE `sigem_db`.`mutual_asiento_renglones` ADD COLUMN `modulo` VARCHAR(12) DEFAULT '' NULL AFTER `error_descripcion`, ADD COLUMN `tipo_asiento` INT(4) DEFAULT 0 NULL AFTER `modulo`; 
ALTER TABLE `sigem_db`.`mutual_asientos` ADD COLUMN `modulo` VARCHAR(12) DEFAULT '' NULL AFTER `error`, ADD COLUMN `tipo_asiento` INT(4) DEFAULT 0 NULL AFTER `modulo`; 
ALTER TABLE `sigem_db`.`mutual_temporal_asiento_renglones` ADD COLUMN `fecha` DATE NULL AFTER `mutual_asiento_id`; 

ALTER TABLE `sigem_db`.`mutual_proceso_asientos` ADD COLUMN `agrupar` INT(1) DEFAULT 0 NULL AFTER `cerrado`; 

ALTER TABLE `sigem_db`.`orden_pago_facturas` ADD COLUMN `fecha` DATE NULL AFTER `id`; 


ALTER TABLE `sigem_db`.`recibo_detalles` ADD COLUMN `co_plan_cuenta_id` INT(11) DEFAULT 0 NULL AFTER `concepto`; 



INSERT INTO `sigem_db`.`permisos`(`id`,`descripcion`,`url`,`order`,`main`,`quick`,`icon`,`activo`,`parent`,`obs`,`created`,`modified`) VALUES ( '420','Listados','/proveedores/listados/iva_compra','420','1','0','arrow_right2.gif','1','400',NULL,NULL,NULL); 
INSERT INTO grupos_permisos VALUES(1,420);


UPDATE `sigem_db`.`permisos` SET `url`='/proveedores/proveedor_listados/iva_compra' WHERE `id`='420'; 
UPDATE `sigem_db`.`permisos` SET `url`='/contabilidad/contabilidad_listados/libro_diario_PDF' WHERE `id`='590'; 
UPDATE `sigem_db`.`permisos` SET `url`='/cajabanco/banco_listados' WHERE `id`='606'; 

ALTER TABLE `sigem_db`.`cliente_factura_detalles` ADD COLUMN `co_plan_cuenta_id` INT(11) DEFAULT 0 NULL AFTER `producto`; 


ALTER TABLE `sigem_db`.`banco_cuenta_movimientos` ADD COLUMN `fecha_reemplazar` DATE DEFAULT '1970-01-01' NULL AFTER `reemplazar`; 



ALTER TABLE `sigem_db`.`co_plan_cuentas` ADD COLUMN `vincula_co_plan_cuenta_id` INT(11) DEFAULT 0 NULL AFTER `acumulado_haber`; 


ALTER TABLE `sigem_db`.`banco_cuentas` ADD COLUMN `caja_general` TINYINT(1) DEFAULT 0 NULL AFTER `denominacion`; 

ALTER TABLE `sigem_db`.`co_asientos` ADD COLUMN `cierre` TINYINT(1) DEFAULT 0 NULL AFTER `borrado`;


ALTER TABLE `sigem_db`.`co_ejercicios` ADD COLUMN `fecha_cierre_periodo` DATE NULL AFTER `fecha_cierre`; 
