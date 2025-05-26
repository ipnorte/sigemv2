ALTER TABLE sigem_db.asincronos ADD COLUMN txt1 TEXT NULL AFTER p13, ADD COLUMN txt2 TEXT NULL AFTER txt1;
ALTER TABLE sigem_db.liquidacion_socios ADD COLUMN error_intercambio TEXT;
ALTER TABLE sigem_db.liquidacion_socio_rendiciones ADD COLUMN saldo_operacion_informado DECIMAL (10,2) DEFAULT 0;

/*************************************************************************************************************************/
ALTER TABLE `sigem_db`.`cancelacion_orden_cuotas` ADD COLUMN `alicuota_comision_cobranza` DECIMAL(10,3) DEFAULT 0.000 NULL AFTER `proveedor_id`, ADD COLUMN `comision_cobranza` DECIMAL(10,2) DEFAULT 0.00 NULL AFTER `alicuota_comision_cobranza`;
ALTER TABLE `sigem_db`.`cancelacion_orden_cuotas` ADD COLUMN `cuota_vencida` BOOLEAN DEFAULT 0 NULL AFTER `proveedor_id`;

/*************************************************************************************************************************/
ALTER TABLE `sigem_db`.`socio_reintegros` ADD COLUMN `importe_reversado` DECIMAL(10,2) DEFAULT 0.00 NULL AFTER `importe_aplicado`, ADD COLUMN `reversado` BOOLEAN DEFAULT 0 NULL AFTER `importe_reversado`;
ALTER TABLE `sigem_db`.`socio_reintegros` ADD COLUMN `fecha_reverso` DATE NULL AFTER `reversado`, ADD COLUMN `periodo_proveedor_reverso` VARCHAR(6) NULL AFTER `fecha_reverso`, ADD COLUMN `usuario_reverso` VARCHAR(50) NULL AFTER `periodo_proveedor_reverso`; 

ALTER TABLE `sigem_db`.`socio_reintegro_pagos` ADD COLUMN `orden_pago_id` INT(11) DEFAULT 0 NULL AFTER `nro_opago`; 
ALTER TABLE `sigem_db`.`socio_reintegro_pagos` ADD COLUMN `socio_reintegro_id` INT(11) DEFAULT 0 NULL AFTER `id`; 
ALTER TABLE `sigem_db`.`socio_reintegro_pagos` ADD INDEX `idx_socio_reintegro_id` (`socio_reintegro_id`);

UPDATE socio_reintegros r, socio_reintegro_pagos p SET p.socio_reintegro_id = r.id WHERE p.id = r.socio_reintegro_pago_id;
UPDATE socio_reintegros r, socio_reintegro_pagos p SET p.socio_id = r.socio_id WHERE p.socio_reintegro_id = r.id AND p.socio_id = 0;
UPDATE socio_reintegros r, socio_reintegro_pagos p SET p.orden_pago_id = r.orden_pago_id WHERE p.socio_reintegro_id = r.id AND p.id = r.socio_reintegro_pago_id;

/*************************************************************************************************************************/
ALTER TABLE `sigem_db`.`persona_beneficios` ADD COLUMN `importe_max_registro_cbu` DECIMAL(10,2) DEFAULT 0.00 NULL AFTER `acuerdo_debito`;

/*************************************************************************************************************************/
ALTER TABLE `sigem_db`.`global_datos` ADD COLUMN `texto_2` TEXT NULL AFTER `texto_1`; 
ALTER TABLE `sigem_db`.`global_datos` ADD COLUMN `user_created` VARCHAR(50) NULL AFTER `usuario_id`, ADD COLUMN `user_modified` VARCHAR(50) NULL AFTER `user_created`;

-- OJO CON EL ID (VERIFICAR SI EL 59 ESTA LIBRE)
INSERT  INTO `permisos`(`id`,`descripcion`,`url`,`order`,`main`,`quick`,`icon`,`activo`,`parent`,`obs`,`created`,`modified`) 
VALUES (59,'Config Reasignacion de Solicitudes','/v1/solicitudes/reasignar_proveedor_config',59,0,0,NULL,1,50,NULL,NULL,NULL);
-- VER EL ID DEL PERMISO UTILIZADO EN EL INSERT ANTERIOR
INSERT INTO sigem_db.grupos_permisos(grupo_id,permiso_id) VALUES(1,59);


-- SOCIO REINTEGRO BORRAR PERMISOS MENU
INSERT  INTO `permisos`(`id`,`descripcion`,`url`,`order`,`main`,`quick`,`icon`,`activo`,`parent`,`obs`,`created`,`modified`) 
VALUES (151,'Borrar Reintegro Anticipado No Pagado','/pfyj/socio_reintegros/borrar',151,0,0,NULL,1,100,NULL,NULL,NULL);
-- VER EL ID DEL PERMISO UTILIZADO EN EL INSERT ANTERIOR
INSERT INTO sigem_db.grupos_permisos(grupo_id,permiso_id) VALUES(1,151);


/*************************************************************************************************************************/
/* GENERACION DE DISKETTES */
/*************************************************************************************************************************/

CREATE TABLE `liquidacion_socio_envios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asincrono_id` int(11) DEFAULT '0',
  `bloqueado` tinyint(1) DEFAULT '0',
  `liquidacion_id` int(11) DEFAULT '0',
  `banco_id` varchar(5) DEFAULT NULL,
  `banco_nombre` varchar(100) DEFAULT NULL,
  `fecha_debito` date DEFAULT NULL,
  `cantidad_registros` int(11) DEFAULT '0',
  `status` varchar(5) DEFAULT '',
  `importe_debito` decimal(10,2) DEFAULT '0.00',
  `observaciones` text,
  `longitud_registro` int(11) DEFAULT '0',
  `uuid` varchar(20) DEFAULT NULL,
  `lote` text,
  `archivo` varchar(100) DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_uuid` (`uuid`),
  KEY `fk_liquidacion_id` (`liquidacion_id`),
  CONSTRAINT `fk_liquidacion_id` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidaciones` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

CREATE TABLE `liquidacion_socio_envio_registros` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `liquidacion_socio_envio_id` int(11) DEFAULT '0',
  `liquidacion_socio_id` int(11) DEFAULT '0',
  `identificador_debito` char(22) DEFAULT NULL,
  `importe_adebitar` decimal(10,2) DEFAULT '0.00',
  `registro` text,
  `excluido` tinyint(1) DEFAULT '0',
  `motivo` text,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_liquidacion_socios` (`liquidacion_socio_id`),
  KEY `fk_liquidacion_socio_envio_id` (`liquidacion_socio_envio_id`),
  KEY `idx_identificador_debito` (`identificador_debito`),
  KEY `idx_excluido` (`excluido`),
  CONSTRAINT `fk_liquidacion_socios` FOREIGN KEY (`liquidacion_socio_id`) REFERENCES `liquidacion_socios` (`id`),
  CONSTRAINT `fk_liquidacion_socio_envio_id` FOREIGN KEY (`liquidacion_socio_envio_id`) REFERENCES `liquidacion_socio_envios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=176 DEFAULT CHARSET=utf8;



ALTER TABLE `sigem_db`.`liquidacion_intercambios` ADD COLUMN `lote` LONGTEXT NULL AFTER `proveedor_id`; 
ALTER TABLE `sigem_db`.`liquidacion_socio_envios` CHANGE `lote` `lote` LONGTEXT NULL; 



/*************************************************************************************************************************/
/* MARCA PARA DETECTAR EL INFORME DE ALTAS Y BAJAS DE ORDENES EN LA CAJA */
/*************************************************************************************************************************/

ALTER TABLE sigem_db.orden_descuentos ADD COLUMN alta_informada BOOLEAN DEFAULT 0 AFTER productor_ref;
ALTER TABLE sigem_db.orden_descuentos ADD COLUMN alta_informada_periodo VARCHAR(6) AFTER alta_informada;
ALTER TABLE sigem_db.orden_descuentos ADD COLUMN baja_informada BOOLEAN DEFAULT 0 AFTER alta_informada_periodo;
ALTER TABLE sigem_db.orden_descuentos ADD COLUMN baja_informada_periodo VARCHAR(6) AFTER baja_informada;


ALTER TABLE `sigem_db`.`asincronos` ADD COLUMN `shell_pid` INT(11) DEFAULT 0 NULL AFTER `id`; 
ALTER TABLE `sigem_db`.`asincrono_errores` CHANGE `mensaje_1` `mensaje_1` LONGTEXT NULL, CHANGE `mensaje_2` `mensaje_2` LONGTEXT NULL, CHANGE `mensaje_3` `mensaje_3` LONGTEXT NULL, CHANGE `mensaje_4` `mensaje_4` LONGTEXT NULL; 
 
/*****************************************************************************************************/
/*	FECHA DE VIGENCIA PARA COMPUTO DE COMISION POR COBRANZA */
/*****************************************************************************************************/
ALTER TABLE proveedor_comisiones ADD COLUMN fecha_vigencia DATE;
UPDATE proveedor_comisiones SET fecha_vigencia = '2000-01-01';	
-- ojo el id
INSERT  INTO `permisos`(`id`,`descripcion`,`url`,`order`,`main`,`quick`,`icon`,`activo`,`parent`,`obs`,`created`,`modified`) 
VALUES (420,'Proveedor Comision Cobranza','/proveedores/proveedores/comision_cobranza',420,0,0,NULL,1,400,NULL,NULL,NULL);
-- VER EL ID DEL PERMISO UTILIZADO EN EL INSERT ANTERIOR
INSERT INTO sigem_db.grupos_permisos(grupo_id,permiso_id) VALUES(1,420);


/*****************************************************************************************************/
/*	REFERENCIA A ORDEN DE DESCUENTO ANTERIOR (PARA MODIFICACIONES DE IMPORTE / BENEFICIO - CAJA DE JUBILACIONES) */
/*****************************************************************************************************/
ALTER TABLE sigem_db.orden_descuentos ADD COLUMN nueva_orden_descuento_id INT(11) DEFAULT 0 AFTER baja_informada_periodo;
ALTER TABLE sigem_db.orden_descuentos ADD COLUMN anterior_orden_descuento_id INT(11) DEFAULT 0 AFTER nueva_orden_descuento_id;
ALTER TABLE orden_descuentos ADD COLUMN motivo_novacion VARCHAR(250) AFTER anterior_orden_descuento_id;
CREATE INDEX idx_nueva_orden_descuento_id ON sigem_db.orden_descuentos(nueva_orden_descuento_id);
CREATE INDEX idx_anterior_orden_descuento_id ON sigem_db.orden_descuentos(anterior_orden_descuento_id);


/*****************************************************************************************************/
/*	AGREGO CAMPOS A LA TABLA ASINCRONO_TEMPORALES */
/*****************************************************************************************************/
ALTER TABLE `sigem_db`.`asincrono_temporales` ADD COLUMN `entero_4` INT(11) NULL AFTER `entero_3`, ADD COLUMN `entero_5` INT(11) NULL AFTER `entero_4`, ADD COLUMN `entero_6` INT(11) NULL AFTER `entero_5`;


/*****************************************************************************************************/
/*	AGREGO INDICE UNICO A TABLAS CON NUMERADOR DE DOCUMENTOS */
/*****************************************************************************************************/
ALTER TABLE `sigem_db`.`orden_pagos` ADD UNIQUE INDEX `idu_tipo_suc_nro` (`tipo_documento`, `sucursal`, `nro_orden_pago`); 
ALTER TABLE `sigem_db`.`recibos` ADD UNIQUE INDEX `idu_letra_suc_nro` (`letra`, `sucursal`, `nro_recibo`); 
ALTER TABLE `sigem_db`.`cliente_facturas` ADD UNIQUE INDEX `idu_letra_suc_nro` (`letra_comprobante`, `punto_venta_comprobante`, `numero_comprobante`); 

/*****************************************************************************************************/
/*	AGREGO PARAMETROS DE SETUP MODULO SERVICIOS */
/*****************************************************************************************************/
ALTER TABLE `sigem_db`.`mutual_servicios` ADD COLUMN `meses_antes_dia_corte` INT(11) DEFAULT 1 NULL AFTER `dia_corte`, ADD COLUMN `meses_despues_dia_corte` INT(11) DEFAULT 1 NULL AFTER `meses_antes_dia_corte`, ADD COLUMN `dia_alta` INT(11) DEFAULT 1 NULL AFTER `meses_despues_dia_corte`, ADD COLUMN `call_center` BOOLEAN DEFAULT 0 NULL AFTER `dia_alta`;

/*****************************************************************************************************/
/*	AGREGO DATOS A LA SERVICIO SOLICITUDES PARA LOS SERVICIOS CON PLAZO */
/*****************************************************************************************************/
ALTER TABLE sigem_db.mutual_servicio_solicitudes ADD COLUMN permanente BOOLEAN DEFAULT 1 AFTER importe_mensual_total;
ALTER TABLE sigem_db.mutual_servicio_solicitudes ADD COLUMN cuotas INT(11) DEFAULT 0 AFTER permanente;
ALTER TABLE sigem_db.mutual_servicio_solicitudes ADD COLUMN importe_cuota DECIMAL(10,2) DEFAULT 0 AFTER cuotas;

/*****************************************************************************************************/
/*	BORRAR RESTRICCION PARA QUE NO DE ERROR AL RELIQUIDAR UN SOCIO Y DUPLIQUE LA INFORMACION */
/*****************************************************************************************************/
ALTER TABLE `sigem_db`.`liquidacion_socio_envio_registros` DROP FOREIGN KEY `fk_liquidacion_socios`; 

/*****************************************************************************************************/
/* MODIFICAR INDICE PARA BUSCAR POR LOCALIDAD STRING */
/*****************************************************************************************************/
ALTER TABLE `sigem_db`.`personas` DROP INDEX `idx_localidad`, ADD INDEX `idx_localidad` (`localidad_id`, `localidad`); 

/*****************************************************************************************************/
/* SETEO EL TIPO PRODUCTO SIGEM EN LA V1 */
/*****************************************************************************************************/
ALTER TABLE aman_db.proveedores_productos ADD COLUMN codigo_producto_sigem VARCHAR(12) DEFAULT 'MUTUPROD0001'; 
ALTER TABLE aman_db.solicitudes ADD COLUMN codigo_producto_sigem VARCHAR(12) DEFAULT 'MUTUPROD0001';
UPDATE aman_db.proveedores_productos SET codigo_producto_sigem = 'MUTUPROD0001';
UPDATE aman_db.solicitudes SET codigo_producto_sigem = 'MUTUPROD0001';

/*****************************************************************************************************/
/* MARCA PARA IDENTIFICAR EL TIPO DE CALCULO DE LA RETENCION EN LA INSTRUCCION DE PAGO */
/*****************************************************************************************************/
ALTER TABLE aman_db.proveedores_productos ADD COLUMN comision_instruccion_pago_calculo INT(11) DEFAULT 1 AFTER comision_instruccion_pago;
ALTER TABLE `aman_db`.`solicitudes` ADD COLUMN `comision_instruccion_pago` DECIMAL(10,2) DEFAULT 0.00 NULL AFTER `monto_instruccion_pago`; 
ALTER TABLE `aman_db`.`solicitudes` ADD COLUMN `comision_instruccion_pago_calculo` INT(11) DEFAULT 1 NULL AFTER `monto_instruccion_pago`; 


/*****************************************************************************************************/
/* TABLAS PARA CONFIGURAR LOS TIPOS DE ASIENTOS PARA LOS PRODUCTOS DE LA MUTUAL */
/*****************************************************************************************************/
CREATE TABLE `mutual_tipo_asientos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `concepto` VARCHAR(100) DEFAULT NULL,
  `tipo_asiento` VARCHAR(2) NOT NULL DEFAULT 'GR',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `mutual_tipo_asiento_renglones` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `variable` VARCHAR(5) DEFAULT NULL,
  `mutual_tipo_asiento_id` INT(11) DEFAULT '0',
  `co_plan_cuenta_id` INT(11) DEFAULT '0',
  `debe_haber` VARCHAR(1) DEFAULT 'D',
  `tipo_asiento` VARCHAR(2) DEFAULT 'GR',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

INSERT INTO permisos(id,descripcion,url,`order`,main,`quick`,icon,activo,parent)
VALUES(209,'Tipos de Asientos','/mutual/tipo_asientos', 209,1,0,'arrow_right2.gif',1,200);
INSERT INTO grupos_permisos VALUES(1,209);

INSERT INTO permisos(id,descripcion,url,`order`,main,`quick`,icon,activo,parent)
VALUES(208,'Vincular Asientos Prod. Mutual','/mutual/tipo_asientos/vincular', 208,0,0,NULL,1,200);
INSERT INTO grupos_permisos VALUES(1,208);


CREATE TABLE `mutual_cuenta_asientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_orden_dto` varchar(5) NOT NULL DEFAULT '',
  `tipo_producto` varchar(12) NOT NULL DEFAULT '',
  `tipo_cuota` varchar(12) NOT NULL DEFAULT '',
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `mutual_tipo_asiento_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB

ALTER TABLE `sigem_db`.`mutual_cuenta_asientos` ADD COLUMN `instancia` VARCHAR(5) DEFAULT 'COBRO' NULL AFTER `mutual_tipo_asiento_id`, ADD COLUMN `user_created` VARCHAR(50) NULL AFTER `instancia`, ADD COLUMN `user_modified` VARCHAR(50) NULL AFTER `user_created`, ADD COLUMN `created` DATETIME NULL AFTER `user_modified`, ADD COLUMN `modified` DATETIME NULL AFTER `created`;


ALTER TABLE `sigem_db`.`liquidacion_socio_envio_registros` DROP FOREIGN KEY `fk_liquidacion_socios`;

/*****************************************************************************************************/
/* OPCION DE NOVACION MANUAL POR MENU */
/*****************************************************************************************************/
INSERT INTO permisos(id,descripcion,url,`order`,main,`quick`,icon,activo,parent)
VALUES(207,'Novar Orden','/mutual/orden_descuentos/novar', 207,1,0,'arrow_right2.gif',1,200);
INSERT INTO grupos_permisos VALUES(1,207);

/*****************************************************************************************************/
/* CAMBIO DE NOMBRE PARA MUTUAL TIPO ASIENTOS */
/*****************************************************************************************************/
-- VERIFICAR EL ID
UPDATE `sigem_db`.`permisos` SET `url`='/mutual/mutual_tipo_asientos' WHERE `id`=209;
UPDATE `sigem_db`.`permisos` SET `url`='/mutual/mutual_tipo_asientos/vincular' WHERE `id`=208;

/*****************************************************************************************************/
/* CAMBIO DE NOMBRE PARA MUTUAL TIPO ASIENTOS */
/*****************************************************************************************************/
ALTER TABLE orden_descuento_cobros ADD COLUMN observaciones TEXT AFTER proveedor_origen_fondo_id;

UPDATE orden_descuento_cobros co, recibos r SET co.observaciones = r.comentarios WHERE co.recibo_id = r.id AND r.comentarios <> '';

/*****************************************************************************************************/
/* AGREGO RESTRICCION PARA QUE NO SE PUEDA BORRAR UN PROVEEDOR SI TIENE VENCIMIENTOS DEFINIDOS */
/*****************************************************************************************************/
DELETE FROM proveedor_vencimientos WHERE proveedor_id NOT IN (SELECT id FROM proveedores);
ALTER TABLE proveedor_vencimientos ADD CONSTRAINT fk_proveedor_vencimiento_proveedores 
FOREIGN KEY (proveedor_id) REFERENCES proveedores(id); 


/*****************************************************************************************************/
/* AGREGO RESTRICCION PARA QUE NO SE GENEREN MAL LAS ORDENES DE DESCUENTO */
/*****************************************************************************************************/
ALTER TABLE `sigem_db`.`orden_descuentos` ADD CONSTRAINT `FK_orden_descuento_proveedor_id` FOREIGN KEY (`proveedor_id`) REFERENCES `sigem_db`.`proveedores`(`id`);
ALTER TABLE `sigem_db`.`orden_descuentos` CHANGE `tipo_orden_dto` `tipo_orden_dto` VARCHAR(5) NOT NULL, CHANGE `numero` `numero` INT(11) DEFAULT 0 NOT NULL, CHANGE `tipo_producto` `tipo_producto` VARCHAR(12) NOT NULL, CHANGE `proveedor_id` `proveedor_id` INT(11) DEFAULT 0 NOT NULL, CHANGE `socio_id` `socio_id` INT(11) DEFAULT 0 NOT NULL, CHANGE `persona_beneficio_id` `persona_beneficio_id` INT(11) DEFAULT 0 NOT NULL, CHANGE `periodo_ini` `periodo_ini` VARCHAR(6) NOT NULL, CHANGE `importe_total` `importe_total` DECIMAL(10,2) DEFAULT 0.00 NOT NULL, CHANGE `importe_cuota` `importe_cuota` DECIMAL(10,2) DEFAULT 0.00 NOT NULL, CHANGE `cuotas` `cuotas` INT(11) DEFAULT 0 NOT NULL; 

ALTER TABLE `sigem_db`.`persona_beneficios` ADD INDEX `idx_organismo_empresa_turno` (`codigo_beneficio`, `turno_pago`, `codigo_empresa`); 


INSERT  INTO `permisos`(`id`,`descripcion`,`url`,`order`,`main`,`quick`,`icon`,`activo`,`parent`,`obs`,`created`,`modified`) 
VALUES (297,'Listado Deuda Posicion Consolidada','/mutual/liquidaciones/consolidado',297,0,0,NULL,1,200,NULL,NULL,NULL);
INSERT INTO sigem_db.grupos_permisos(grupo_id,permiso_id) VALUES(1,297);


ALTER TABLE `sigem_db`.`socios` CHANGE `categoria` `categoria` VARCHAR(12) CHARSET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `persona_id` `persona_id` INT(11) DEFAULT 0 NOT NULL, CHANGE `persona_beneficio_id` `persona_beneficio_id` INT(11) DEFAULT 0 NOT NULL;

ALTER TABLE `sigem_db`.`liquidaciones` ADD COLUMN `sobre_pre_imputacion` BOOLEAN DEFAULT 0 NULL AFTER `codigo_organismo`; 


/*****************************************************************************************************/
/* TIPOS DE ORDENES DE DESCUENTOS SETEADOS DESDE LA GLOBAL */
/*****************************************************************************************************/
update sigem_db.global_datos set concepto_3 = 'OCOMP'
where concepto_2 = 'MUTUTCUOCONS';

update sigem_db.global_datos set concepto_3 = 'OSERV'
where concepto_2 = 'MUTUTCUOSERV';

update sigem_db.global_datos set concepto_3 = 'EXPTE'
where concepto_2 in ('MUTUTCUOCRED','MUTUTCUOSEGU','MUTUTCUOCRD2',
'MUTUTCUOCRDI');

update sigem_db.global_datos set concepto_3 = 'RECAR'
where concepto_2 = 'MUTUTCUORECU';

update sigem_db.global_datos set concepto_3 = 'CMUTU'
where concepto_2 in ('MUTUTCUOCSOC','MUTUTCUOCONV');

/*****************************************************************************************************/
/* OPCION GENERAR DISKETTE RENDICION EN BASE AL ENVIO */
/*****************************************************************************************************/
INSERT INTO permisos(id,descripcion,url,`order`,main,`quick`,icon,activo,parent)
VALUES(298,'Generar Lote por Envio','/mutual/liquidaciones/importar_generar_lote', 298,1,0,'arrow_right2.gif',1,200);
INSERT INTO grupos_permisos VALUES(1,298);

ALTER TABLE `sigem_db`.`liquidacion_socio_envio_registros` ADD COLUMN `codigo_rendicion` VARCHAR(3) NULL AFTER `modified`, ADD COLUMN `descripcion_codigo` VARCHAR(200) NULL AFTER `codigo_rendicion`, ADD COLUMN `procesado` BOOLEAN DEFAULT 0 NULL AFTER `descripcion_codigo`;