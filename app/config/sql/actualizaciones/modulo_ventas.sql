DROP TABLE IF EXISTS proveedor_planes;
CREATE TABLE `proveedor_planes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `proveedor_id` INT(11) NOT NULL DEFAULT '0',
  `tipo_producto` VARCHAR(12) NOT NULL,
  `activo` TINYINT(1) DEFAULT '1',
  `descripcion` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_created` VARCHAR(50) DEFAULT NULL,
  `user_modified` VARCHAR(50) DEFAULT NULL,
  `created` DATETIME DEFAULT NULL,
  `modified` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_proveedor_condiciones_proveedores1_idx` (`proveedor_id`),
  CONSTRAINT `fk_proveedor_planes_proveedores` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`)
) ENGINE=INNODB;

DROP TABLE IF EXISTS proveedor_plan_organismos;
CREATE TABLE `proveedor_plan_organismos` (
  `proveedor_plan_id` INT(11) NOT NULL,
  `codigo_organismo` VARCHAR(12) NOT NULL,
  PRIMARY KEY (`proveedor_plan_id`,`codigo_organismo`),
  KEY `fk_proveedor_condiciones_organismos_proveedor_condiciones1_idx` (`proveedor_plan_id`),
  CONSTRAINT `fk_proveedor_condiciones_organismos_proveedor_condiciones1` FOREIGN KEY (`proveedor_plan_id`) REFERENCES `proveedor_planes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=INNODB;

DROP TABLE IF EXISTS proveedor_plan_grillas;
CREATE TABLE `proveedor_plan_grillas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `proveedor_plan_id` INT(11) NOT NULL,
  `descripcion` VARCHAR(150) DEFAULT NULL,
  `vigencia_desde` DATE DEFAULT NULL,
  `cuotas` LONGTEXT,
  `xls` LONGBLOB,
  `user_created` VARCHAR(50) DEFAULT NULL,
  `user_modified` VARCHAR(50) DEFAULT NULL,
  `created` DATETIME DEFAULT NULL,
  `modified` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_proveedor_plan_grillas_proveedor_planes1_idx` (`proveedor_plan_id`),
  CONSTRAINT `fk_proveedor_plan_grillas_proveedor_planes11` FOREIGN KEY (`proveedor_plan_id`) REFERENCES `proveedor_planes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=INNODB;

DROP TABLE IF EXISTS proveedor_plan_grilla_cuotas;
CREATE TABLE `proveedor_plan_grilla_cuotas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `proveedor_plan_grilla_id` INT(11) NOT NULL,
  `capital` DECIMAL(10,2) DEFAULT '0.00',
  `liquido` DECIMAL(10,2) DEFAULT '0.00',
  `cuotas` INT(11) DEFAULT '0',
  `importe` DECIMAL(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `idx_capital_cuotas` (`capital`,`cuotas`),
  KEY `idx_liquido_cuotas` (`liquido`,`cuotas`),
  KEY `idx_cuota_importe` (`cuotas`,`importe`),
  KEY `fk_proveedor_plan_grilla_cuotas_proveedor_grillas1_idx` (`proveedor_plan_grilla_id`),
  CONSTRAINT `fk_proveedor_plan_grilla_cuotas_proveedor_plan_grillas11` FOREIGN KEY (`proveedor_plan_grilla_id`) REFERENCES `proveedor_plan_grillas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=INNODB;

DROP TABLE IF EXISTS proveedor_grillas;
CREATE TABLE `proveedor_grillas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `proveedor_plan_id` INT(11) NOT NULL,
  `descripcion` VARCHAR(150) DEFAULT NULL,
  `vigencia_desde` DATE DEFAULT NULL,
  `cuotas` LONGTEXT,
  `xls` LONGBLOB,
  `user_created` VARCHAR(50) DEFAULT NULL,
  `user_modified` VARCHAR(50) DEFAULT NULL,
  `created` DATETIME DEFAULT NULL,
  `modified` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_proveedor_plan_grillas_proveedor_planes1_idx` (`proveedor_plan_id`),
  CONSTRAINT `fk_proveedor_plan_grillas_proveedor_planes1` FOREIGN KEY (`proveedor_plan_id`) REFERENCES `proveedor_planes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=INNODB;

DROP TABLE IF EXISTS proveedor_grilla_cuotas;
CREATE TABLE `proveedor_grilla_cuotas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `proveedor_grilla_id` INT(11) NOT NULL,
  `capital` DECIMAL(10,2) DEFAULT '0.00',
  `liquido` DECIMAL(10,2) DEFAULT '0.00',
  `cuotas` INT(11) DEFAULT '0',
  `importe` DECIMAL(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `idx_capital_cuotas` (`capital`,`cuotas`),
  KEY `idx_liquido_cuotas` (`liquido`,`cuotas`),
  KEY `idx_cuota_importe` (`cuotas`,`importe`),
  KEY `fk_proveedor_plan_grilla_cuotas_proveedor_grillas1_idx` (`proveedor_grilla_id`),
  CONSTRAINT `fk_proveedor_plan_grilla_cuotas_proveedor_plan_grillas1` FOREIGN KEY (`proveedor_grilla_id`) REFERENCES `proveedor_grillas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=INNODB;


INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)
VALUES(430,'Administracion de Planes','/proveedores/proveedor_planes',430,0,0,1,400);
INSERT INTO grupos_permisos VALUES(1,430);
INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)
VALUES(431,'Administracion grillas','/proveedores/proveedor_planes/grillas',431,0,0,1,400);
INSERT INTO grupos_permisos VALUES(1,431);
INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)
VALUES(432,'Alta Grilla','/proveedores/proveedor_planes/nueva_grilla',432,0,0,1,400);
INSERT INTO grupos_permisos VALUES(1,432);
INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)
VALUES(433,'Descargar Grilla','/proveedores/proveedor_planes/download_grilla',433,0,0,1,400);
INSERT INTO grupos_permisos VALUES(1,433);
INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)
VALUES(434,'Borrar Grilla','/proveedores/proveedor_planes/borrar_grilla',434,0,0,1,400);
INSERT INTO grupos_permisos VALUES(1,434);
INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)
VALUES(435,'Alta Plan','/proveedores/proveedor_planes/nuevo_plan',435,0,0,1,400);
INSERT INTO grupos_permisos VALUES(1,435);
INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)
VALUES(436,'Modificar Plan','/proveedores/proveedor_planes/editar_plan',436,0,0,1,400);
INSERT INTO grupos_permisos VALUES(1,436);

INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)
VALUES(437,'Borrar Plan','/proveedores/proveedor_planes/borrar_plan',437,0,0,1,400);
INSERT INTO grupos_permisos VALUES(1,437);

INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)
VALUES(438,'Desactivar Plan','/proveedores/proveedor_planes/desactivar_plan',438,0,0,1,400);
INSERT INTO grupos_permisos VALUES(1,438);


INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)
VALUES(201,'Alta Credito','/mutual/mutual_producto_solicitudes/nuevo_credito',201,0,0,1,200);
INSERT INTO grupos_permisos VALUES(1,201);


DROP TABLE IF EXISTS vendedores;
CREATE TABLE `vendedores` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `persona_id` INT(11) DEFAULT '0',
  `usuario_id` INT(11) DEFAULT '0',
  `activo` TINYINT(1) DEFAULT '1',
  `user_created` VARCHAR(50) DEFAULT NULL,
  `user_modified` VARCHAR(50) DEFAULT NULL,
  `created` DATETIME DEFAULT NULL,
  `modified` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDU_PERSONA` (`persona_id`),
  UNIQUE KEY `IDU_USUARIO` (`usuario_id`),
  CONSTRAINT `FK_PERSONA_VENDEDOR` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`),
  CONSTRAINT `FK_USUARIO_VENDEDOR` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=INNODB;

DROP TABLE IF EXISTS vendedor_proveedor_planes;
CREATE TABLE `vendedor_proveedor_planes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `vendedor_id` INT(11) NOT NULL,
  `proveedor_plan_id` INT(11) NOT NULL,
  `monto_venta` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `comision` DECIMAL(10,3) NOT NULL DEFAULT '0.000',
  `activo` TINYINT(1) DEFAULT '1',
  `user_created` VARCHAR(50) DEFAULT NULL,
  `user_modified` VARCHAR(50) DEFAULT NULL,
  `created` DATETIME DEFAULT NULL,
  `modified` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_PROVEEDOR_PLAN_VENDEDOR` (`proveedor_plan_id`),
  KEY `FK_VENDEDOR_VENDEDOR` (`vendedor_id`),
  CONSTRAINT `FK_PROVEEDOR_PLAN_VENDEDOR` FOREIGN KEY (`proveedor_plan_id`) REFERENCES `proveedor_planes` (`id`),
  CONSTRAINT `FK_VENDEDOR_VENDEDOR` FOREIGN KEY (`vendedor_id`) REFERENCES `vendedores` (`id`)
) ENGINE=INNODB;

DROP TABLE IF EXISTS vendedor_remitos;
CREATE TABLE `vendedor_remitos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `vendedor_id` INT(11) NOT NULL,
  `observaciones` TEXT,
  `user_created` VARCHAR(50) DEFAULT NULL,
  `created` DATETIME DEFAULT NULL,
  `anulado` TINYINT(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_vendedor_remito_vendedores` (`vendedor_id`),
  CONSTRAINT `fk_vendedor_remito_vendedores` FOREIGN KEY (`vendedor_id`) REFERENCES `vendedores` (`id`)
) ENGINE=INNODB;


SET FOREIGN_KEY_CHECKS=0;
DELETE FROM global_datos WHERE id LIKE 'MUTUESTA%'
AND id <> 'MUTUESTA';

INSERT INTO global_datos(id,concepto_1,concepto_2,logico_1)
VALUES('MUTUESTA0000','ANULADA','',0);
INSERT INTO global_datos(id,concepto_1,concepto_2,logico_1)
VALUES('MUTUESTA0001','EMITIDA','MUTUESTA0002',0);
INSERT INTO global_datos(id,concepto_1,concepto_2,logico_1)
VALUES('MUTUESTA0002','PRESENTADA','MUTUESTA0014',0);
INSERT INTO global_datos(id,concepto_1,concepto_2,logico_1)
VALUES('MUTUESTA0004','OBSERVADA','MUTUESTA0002',1);
INSERT INTO global_datos(id,concepto_1,concepto_2,logico_1)
VALUES('MUTUESTA0014','APROBADA','MUTUESTA0002',1);
SET FOREIGN_KEY_CHECKS=1;

ALTER TABLE `mutual_producto_solicitudes` 
CHANGE `fecha` `fecha` DATE NOT NULL, 
CHANGE `tipo_orden_dto` `tipo_orden_dto` VARCHAR(12) NOT NULL, 
CHANGE `tipo_producto` `tipo_producto` VARCHAR(12) NOT NULL, 
CHANGE `estado` `estado` VARCHAR(12) NOT NULL, 
CHANGE `importe_total` `importe_total` DECIMAL(10,2) NOT NULL, 
CHANGE `cuotas` `cuotas` INT(11) DEFAULT 1 NOT NULL, 
CHANGE `importe_cuota` `importe_cuota` DECIMAL(10,2) NOT NULL, 
CHANGE `importe_solicitado` `importe_solicitado` DECIMAL(10,2) NOT NULL, 
CHANGE `importe_percibido` `importe_percibido` DECIMAL(10,2) NOT NULL; 


ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `proveedor_plan_id` INT(11) DEFAULT 0 NULL AFTER `proveedor_id`, ADD INDEX idx_proveedor_plan_id(proveedor_plan_id);

ALTER TABLE `mutual_producto_solicitudes` CHANGE `proveedor_plan_id` `proveedor_plan_id` INT(11) NULL, CHANGE `mutual_producto_id` `mutual_producto_id` INT(11) NULL;
UPDATE mutual_producto_solicitudes SET proveedor_plan_id = NULL WHERE proveedor_plan_id = 0;

ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `reasignar_proveedor_id` INT(11) DEFAULT 0 NULL AFTER `modified`, ADD INDEX idx_reasignar_proveedor(reasignar_proveedor_id);
ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `reasignar_proveedor_fecha` DATETIME NULL AFTER `reasignar_proveedor_id`;
ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `reasignar_proveedor_usuario` VARCHAR(50) NULL AFTER `reasignar_proveedor_fecha`;


ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `vendedor_id` INT(11) DEFAULT 0 NULL AFTER `reasignar_proveedor_usuario`, ADD INDEX `idx_vendedor_id` (`vendedor_id`);
ALTER TABLE `mutual_producto_solicitudes` ADD CONSTRAINT `FK_SOLICITUD_PROVEEDOR_PLAN` FOREIGN KEY (`proveedor_plan_id`) REFERENCES `proveedor_planes`(`id`);
ALTER TABLE `mutual_producto_solicitudes` CHANGE `vendedor_id` `vendedor_id` INT(11) NULL, ADD COLUMN `vendedor_remito_id` INT(11) NULL AFTER `vendedor_id`, ADD CONSTRAINT `fk_solicitud_vendedor_remito_id` FOREIGN KEY (`vendedor_remito_id`) REFERENCES `vendedor_remitos`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `mutual_producto_solicitudes` ADD CONSTRAINT `fk_solicitud_vendedor_id` FOREIGN KEY (`vendedor_id`) REFERENCES `vendedores`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE mutual_producto_solicitudes ADD COLUMN `vendedor_notificar` BOOLEAN DEFAULT 0 NULL AFTER `vendedor_remito_id`;
ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `forma_pago` VARCHAR(12) NULL AFTER `vendedor_notificar`;

UPDATE mutual_producto_solicitudes SET estado = 'MUTUESTA0014' WHERE aprobada = 1;
UPDATE mutual_producto_solicitudes SET estado = 'MUTUESTA0002' WHERE aprobada = 0;
ALTER TABLE `mutual_producto_solicitudes` ADD CONSTRAINT `FK_SOLICITUD_ESTADO` FOREIGN KEY (`estado`) REFERENCES `global_datos`(`id`); 

ALTER TABLE `personas` CHANGE `tipo_documento` `tipo_documento` VARCHAR(12) NOT NULL, CHANGE `documento` `documento` VARCHAR(11) NOT NULL, CHANGE `apellido` `apellido` VARCHAR(100) NOT NULL, CHANGE `nombre` `nombre` VARCHAR(100) NOT NULL;

ALTER TABLE `usuarios` CHANGE `usuario` `usuario` VARCHAR(11) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL;
ALTER TABLE `usuarios` ADD COLUMN `vendedor_id` INT(11) NULL AFTER `modified`;


UPDATE global_datos SET concepto_2 = '' WHERE id = 'MUTUESTA0014';
UPDATE global_datos SET concepto_2 = '' WHERE id = 'MUTUESTA0000';
UPDATE global_datos SET concepto_2 = 'MUTUESTA0014' WHERE id = 'MUTUESTA0002';
UPDATE global_datos SET concepto_2 = 'MUTUESTA0002' WHERE id = 'MUTUESTA0001';


UPDATE mutual_producto_solicitudes SET vendedor_id = NULL WHERE vendedor_id = 0; 

UPDATE mutual_producto_solicitudes SET estado = 'MUTUESTA0002' WHERE IFNULL(vendedor_id,0) = 0 AND aprobada = 0 AND anulada = 0;

INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)VALUES(800,'Vendedores','/ventas/',800,1,0,1,0);
INSERT INTO grupos_permisos VALUES(1,800);
INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)VALUES(801,'Padron Acceso','/ventas/vendedores/index',801,1,0,1,800);
INSERT INTO grupos_permisos VALUES(1,801);
INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)VALUES(802,'Padron','/ventas/vendedores/padron',802,0,0,1,800);
INSERT INTO grupos_permisos VALUES(1,802);
INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)VALUES(803,'Alta Vendedor','/ventas/vendedores/alta',803,0,0,1,800);
INSERT INTO grupos_permisos VALUES(1,803);
INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)VALUES(804,'Planes','/ventas/vendedores/planes',804,0,0,1,800);
INSERT INTO grupos_permisos VALUES(1,804);
INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)VALUES(805,'Habilitar Plan','/ventas/vendedores/nuevo_plan',805,0,0,1,800);
INSERT INTO grupos_permisos VALUES(1,805);
INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)VALUES(806,'Habilitar Plan','/ventas/vendedores/borrar_plan',806,0,0,1,800);
INSERT INTO grupos_permisos VALUES(1,806);
INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)VALUES(807,'Modificar Plan','/ventas/vendedores/modificar_plan',807,0,0,1,800);
INSERT INTO grupos_permisos VALUES(1,807);
INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)VALUES(808,'Suspender Plan','/ventas/vendedores/desactivar_plan',808,0,0,1,800);
INSERT INTO grupos_permisos VALUES(1,808);
INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)VALUES(850,'Mesa de Entrada','/ventas/vendedores/bandeja',850,1,0,1,800);
INSERT INTO grupos_permisos VALUES(1,850);
INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)VALUES(860,'Listados','/ventas/vendedores/listados',860,1,0,1,800);
INSERT INTO grupos_permisos VALUES(1,860);


UPDATE permisos SET icon = 'yast_sysadmin.png' WHERE id = 800;
UPDATE permisos SET icon = 'arrow_right2.gif' WHERE id IN(801,802,850,860);

-- //////////// OBSERVACIONES DE SOLICITUDES ////////////////////////////////
INSERT INTO global_datos(id,concepto_1,concepto_2,logico_1) VALUES('MUTUESTA0004','OBSERVADA','MUTUESTA0002',1);

INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)VALUES(299,'Observar Solicitud','/mutual/mutual_producto_solicitudes/observar',290,0,0,1,200);
INSERT INTO grupos_permisos VALUES(1,299);


-- /// ---- CANCELACIONES --- ////

ALTER TABLE `cancelacion_ordenes` ADD CONSTRAINT `FK_CANCELACION_TIPO_CUOTA_DIFERENCIA` FOREIGN KEY (`tipo_cuota_diferencia`) REFERENCES `global_datos`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION;

INSERT INTO global_datos(id,concepto_1)
VALUES('MUTUTCUOCANC','N.CRED.CANCELACION');
INSERT INTO global_datos(id,concepto_1)
VALUES('MUTUTCUOCAND','N.DEBI.CANCELACION');


CREATE TABLE `mutual_producto_solicitud_cancelaciones` (
  `mutual_producto_solicitud_id` INT(11) NOT NULL,
  `cancelacion_orden_id` INT(11) NOT NULL,
  PRIMARY KEY (`mutual_producto_solicitud_id`,`cancelacion_orden_id`),
  KEY `FK_SOLICITUD_CANCELACIONES_CANCELACION` (`cancelacion_orden_id`),
  CONSTRAINT `FK_SOLICITUD_CANCELACIONES_CANCELACION` FOREIGN KEY (`cancelacion_orden_id`) REFERENCES `cancelacion_ordenes` (`id`),
  CONSTRAINT `FK_SOLICITUD_CANCELACIONES_SOLICITUD` FOREIGN KEY (`mutual_producto_solicitud_id`) REFERENCES `mutual_producto_solicitudes` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `mutual_producto_solicitud_instruccion_pagos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `mutual_producto_solicitud_id` INT(11) NOT NULL DEFAULT '0',
  `a_la_orden_de` VARCHAR(255) NOT NULL,
  `concepto` TEXT NOT NULL,
  `importe` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `FK_IPAGO_MUTUAL_PRODUCTO_SOLICITUD` (`mutual_producto_solicitud_id`),
  CONSTRAINT `FK_IPAGO_MUTUAL_PRODUCTO_SOLICITUD` FOREIGN KEY (`mutual_producto_solicitud_id`) REFERENCES `mutual_producto_solicitudes` (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


ALTER TABLE `cancelacion_ordenes` ADD CONSTRAINT `FK_CANCELACION_ORDEN_DESCUENTO` FOREIGN KEY (`orden_descuento_id`) REFERENCES `orden_descuentos`(`id`);
ALTER TABLE `cancelacion_ordenes` ADD CONSTRAINT `FK_CANCELACION_SOCIO` FOREIGN KEY (`socio_id`) REFERENCES `socios`(`id`);
ALTER TABLE `cancelacion_ordenes` ADD COLUMN `mutual_producto_solicitud_id` INT NULL AFTER `modified`, ADD CONSTRAINT `FK_CANCELACION_MUTUAL_PRODUCTO_SOLICITUD` FOREIGN KEY (`mutual_producto_solicitud_id`) REFERENCES `mutual_producto_solicitudes`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `cancelacion_ordenes` ADD COLUMN `concepto` TEXT NULL AFTER `mutual_producto_solicitud_id`; 



INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)VALUES(231,'Generar Ord.Canc.de Terceros','/mutual/cancelacion_ordenes/terceros_generar',291,0,0,1,200);
INSERT INTO grupos_permisos VALUES(1,231);

ALTER TABLE `cancelacion_ordenes` DROP FOREIGN KEY `FK_CANCELACION_ORDEN_DESCUENTO`;
ALTER TABLE `cancelacion_ordenes` CHANGE `orden_descuento_id` `orden_descuento_id` INT(11) NULL; 
ALTER TABLE `cancelacion_ordenes` ADD CONSTRAINT `FK_CANCELACION_ORDEN_DESCUENTO` FOREIGN KEY (`orden_descuento_id`) REFERENCES `orden_descuentos`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION;


ALTER TABLE proveedores ADD COLUMN vendedores BOOLEAN DEFAULT 0 AFTER reasignable;

ALTER TABLE `global_datos` ADD COLUMN `concepto_4` VARCHAR(100) NULL AFTER `concepto_3`;
UPDATE global_datos SET concepto_4 = 'MUTUPROD0002' WHERE id = 'MUTUPROD0001';

UPDATE mutual_producto_solicitudes SET orden_descuento_id = NULL WHERE orden_descuento_id = 0;

SELECT * FROM mutual_producto_solicitudes
WHERE orden_descuento_id NOT IN (SELECT id FROM orden_descuentos)

SELECT * FROM orden_descuentos WHERE numero = 505874 AND tipo_orden_dto = 'OCOMP'

UPDATE mutual_producto_solicitudes SET orden_descuento_id = 76491
WHERE id = 500004;
UPDATE mutual_producto_solicitudes SET orden_descuento_id = 76503
WHERE id = 500009;
UPDATE mutual_producto_solicitudes SET orden_descuento_id = NULL
WHERE id = 500208;
UPDATE mutual_producto_solicitudes SET orden_descuento_id = NULL
WHERE id = 500566;
UPDATE mutual_producto_solicitudes SET orden_descuento_id = NULL
WHERE id = 500721;
UPDATE mutual_producto_solicitudes SET orden_descuento_id = NULL
WHERE id = 501131;
UPDATE mutual_producto_solicitudes SET orden_descuento_id = NULL
WHERE id = 501132;
UPDATE mutual_producto_solicitudes SET orden_descuento_id = NULL
WHERE id = 505874;

UPDATE mutual_producto_solicitudes SET orden_descuento_id = NULL WHERE orden_descuento_id = 0;

ALTER TABLE `mutual_producto_solicitudes` CHANGE `orden_descuento_id` `orden_descuento_id` INT(11) NULL, ADD COLUMN `orden_descuento_seguro_id` INT(11) NULL AFTER `orden_descuento_id`, ADD CONSTRAINT `fk_orden_dto_seguro_id_orden_dto` FOREIGN KEY (`orden_descuento_seguro_id`) REFERENCES `orden_descuentos`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION, ADD CONSTRAINT `fk_orden_dto_id_orden_dto` FOREIGN KEY (`orden_descuento_id`) REFERENCES `orden_descuentos`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION; 

UPDATE global_datos SET logico_2 = 1 WHERE id = 'MUTUPROD0001';

UPDATE global_datos SET logico_1 = 1 WHERE id = 'MUTUCORG2201';
UPDATE global_datos SET logico_1 = 1 WHERE id = 'MUTUCORG2202';
UPDATE global_datos SET logico_1 = 1 WHERE id = 'MUTUCORGMUTU';

UPDATE global_datos SET decimal_1 = 4 WHERE id = 'MUTUPROD0002';

UPDATE mutual_producto_solicitudes SET proveedor_plan_id = NULL WHERE proveedor_plan_id = 0;
ALTER TABLE `mutual_producto_solicitudes` DROP FOREIGN KEY `FK_SOLICITUD_PROVEEDOR_PLAN`; 
ALTER TABLE `mutual_producto_solicitudes` ADD CONSTRAINT `FK_SOLICITUD_PROVEEDOR_PLAN` FOREIGN KEY (`proveedor_plan_id`) REFERENCES `proveedor_planes`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION; 


 -- OJO
ALTER TABLE `mutual_producto_solicitudes` AUTO_INCREMENT=5000;
ALTER TABLE `socios` AUTO_INCREMENT=5000;
ALTER TABLE `socio_solicitudes` AUTO_INCREMENT=5000;
ALTER TABLE `orden_descuentos` AUTO_INCREMENT=5000;
 
 SELECT MAX(id) FROM socios
 SELECT MAX(id) FROM socio_solicitudes;
 
 SELECT MAX(id) FROM orden_descuentos;
 
 INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)
VALUES(202,'Anular Solicitud','/mutual/mutual_producto_solicitudes/anular',202,0,0,1,200);
INSERT INTO grupos_permisos VALUES(1,202);

-- ///
CREATE TABLE `mutual_producto_solicitud_documentos`( `id` INT NOT NULL AUTO_INCREMENT, `mutual_producto_solicitud` INT, `blob` BLOB, PRIMARY KEY (`id`), CONSTRAINT `FK_documentos_mutual_producto_solicitud` FOREIGN KEY (`mutual_producto_solicitud`) REFERENCES `mutual_producto_solicitudes`(`id`) );

ALTER TABLE `vendedores` CHANGE `persona_id` `persona_id` INT(11) DEFAULT NULL NULL, CHANGE `usuario_id` `usuario_id` INT(11) DEFAULT NULL NULL; 
ALTER TABLE `vendedores` DROP FOREIGN KEY `FK_PERSONA_VENDEDOR`; 
ALTER TABLE `vendedores` ADD CONSTRAINT `FK_PERSONA_VENDEDOR` FOREIGN KEY (`persona_id`) REFERENCES `personas`(`id`) ON UPDATE RESTRICT ON DELETE RESTRICT; 
ALTER TABLE `vendedores` DROP FOREIGN KEY `FK_USUARIO_VENDEDOR`; 
ALTER TABLE `vendedores` ADD CONSTRAINT `FK_USUARIO_VENDEDOR` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON UPDATE RESTRICT ON DELETE RESTRICT; 

 