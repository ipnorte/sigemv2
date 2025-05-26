/*
SQLyog Community Edition- MySQL GUI v8.01 
MySQL - 5.0.75-0ubuntu10.2-log : Database - aman2_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`aman2_db` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `aman2_db`;

/*Table structure for table `asincrono_temporales` */

DROP TABLE IF EXISTS `asincrono_temporales`;

CREATE TABLE `asincrono_temporales` (
  `id` int(11) NOT NULL auto_increment,
  `asincrono_id` int(11) default '0',
  `texto_1` varchar(100) default NULL,
  `texto_2` varchar(100) default NULL,
  `texto_3` varchar(100) default NULL,
  `texto_4` varchar(100) default NULL,
  `decimal_1` decimal(10,2) default NULL,
  `decimal_2` decimal(10,2) default NULL,
  `decimal_3` decimal(10,3) default NULL,
  `entero_1` int(11) default NULL,
  `entero_2` int(11) default NULL,
  `entero_3` int(11) default NULL,
  `entero_4` int(11) default NULL,
  `entero_5` int(11) default NULL,
  `fecha_1` date default NULL,
  `fecha_2` date default NULL,
  `fecha_3` date default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_asincrono` (`asincrono_id`),
  KEY `idx_textos` (`texto_1`,`texto_2`,`texto_3`),
  KEY `idx_decimales` (`decimal_1`,`decimal_2`,`decimal_3`),
  KEY `idx_fechas` (`fecha_1`,`fecha_2`,`fecha_3`),
  KEY `idx_enteros` (`entero_1`,`entero_2`,`entero_3`,`entero_4`,`entero_5`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `asincronos` */

DROP TABLE IF EXISTS `asincronos`;

CREATE TABLE `asincronos` (
  `id` int(11) NOT NULL auto_increment,
  `propietario` varchar(120) default NULL,
  `remote_ip` varchar(100) default NULL,
  `final` datetime default NULL,
  `proceso` varchar(150) default NULL,
  `p1` varchar(250) default NULL,
  `p2` varchar(250) default NULL,
  `p3` varchar(250) default NULL,
  `p4` varchar(250) default NULL,
  `p5` varchar(250) default NULL,
  `p6` varchar(250) default NULL,
  `p7` varchar(250) default NULL,
  `p8` varchar(250) default NULL,
  `p9` varchar(250) default NULL,
  `p10` varchar(250) default NULL,
  `p11` varchar(250) default NULL,
  `p12` varchar(250) default NULL,
  `p13` varchar(250) default NULL,
  `action_do` varchar(150) default NULL,
  `target` varchar(50) default NULL,
  `btn_label` varchar(150) default 'IMPRIMIR',
  `titulo` varchar(250) default NULL,
  `subtitulo` varchar(250) default NULL,
  `estado` varchar(1) default NULL,
  `total` int(11) default '0',
  `contador` int(11) default '0',
  `porcentaje` int(11) default '0',
  `msg` varchar(150) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_proceso` (`proceso`),
  KEY `idx_propietario` (`propietario`,`remote_ip`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Table structure for table `banco_rendicion_codigos` */

DROP TABLE IF EXISTS `banco_rendicion_codigos`;

CREATE TABLE `banco_rendicion_codigos` (
  `id` int(11) NOT NULL auto_increment,
  `banco_id` varchar(5) default NULL,
  `codigo` varchar(3) default NULL,
  `descripcion` varchar(100) default NULL,
  `indica_pago` tinyint(1) default '0',
  `calificacion_socio` varchar(12) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `banco_sucursales` */

DROP TABLE IF EXISTS `banco_sucursales`;

CREATE TABLE `banco_sucursales` (
  `id` int(11) NOT NULL auto_increment,
  `banco_id` char(5) default NULL,
  `nro_sucursal` char(10) default NULL,
  `nombre` char(100) default NULL,
  `direccion` char(100) default '',
  PRIMARY KEY  (`id`),
  KEY `NewIndex1` (`banco_id`),
  CONSTRAINT `FK_banco_sucursales` FOREIGN KEY (`banco_id`) REFERENCES `bancos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=279 DEFAULT CHARSET=utf8;

/*Table structure for table `bancos` */

DROP TABLE IF EXISTS `bancos`;

CREATE TABLE `bancos` (
  `id` char(5) NOT NULL,
  `nombre` char(100) default NULL,
  `activo` tinyint(1) default '1',
  `beneficio` tinyint(1) default '0',
  `fpago` tinyint(1) default '0',
  `intercambio` tinyint(1) default '0',
  `tipo_registro` int(11) default '1',
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `nombre_banco` (`nombre`),
  KEY `idx_activo_beneficio_fpago` (`activo`,`beneficio`,`fpago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `barrios` */

DROP TABLE IF EXISTS `barrios`;

CREATE TABLE `barrios` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(150) default NULL,
  `localidad_id` int(11) default '405',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `cancelacion_orden_cuotas` */

DROP TABLE IF EXISTS `cancelacion_orden_cuotas`;

CREATE TABLE `cancelacion_orden_cuotas` (
  `id` int(11) NOT NULL auto_increment,
  `cancelacion_orden_id` int(11) unsigned default '0',
  `orden_descuento_cuota_id` int(11) default '0',
  `importe` decimal(10,2) default '0.00',
  `proveedor_id` int(11) default '0',
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `cancelacion_ordenes` */

DROP TABLE IF EXISTS `cancelacion_ordenes`;

CREATE TABLE `cancelacion_ordenes` (
  `id` int(11) NOT NULL auto_increment,
  `orden_proveedor_id` int(11) default '0',
  `socio_id` int(11) default '0',
  `orden_descuento_id` int(11) default '0',
  `importe_proveedor` decimal(10,2) default '0.00',
  `importe_seleccionado` decimal(10,2) default '0.00',
  `fecha_vto` date default NULL,
  `tipo_cancelacion` varchar(1) default NULL,
  `importe_cuota` decimal(10,2) default '0.00',
  `estado` varchar(1) default 'E',
  `saldo_orden_dto` decimal(10,2) default '0.00',
  `persona_idr` int(11) default '0',
  `observaciones` text,
  `forma_cancelacion` varchar(12) default NULL,
  `forma_pago` varchar(12) default NULL,
  `banco_id` varchar(5) default NULL,
  `nro_cta_bco` varchar(50) default NULL,
  `nro_operacion` varchar(50) default NULL,
  `fecha_operacion` date default NULL,
  `pendiente_rendicion_proveedor` tinyint(1) default NULL,
  `tipo_cuota_diferencia` varchar(12) default NULL,
  `importe_diferencia` decimal(10,2) default '0.00',
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_orden_proveedor` (`orden_proveedor_id`),
  KEY `idx_orden_dto` (`orden_descuento_id`),
  KEY `idx_socio` (`socio_id`,`persona_idr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `feriados` */

DROP TABLE IF EXISTS `feriados`;

CREATE TABLE `feriados` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date default NULL,
  `anio` int(4) default NULL,
  `mes` int(2) default NULL,
  `dia` int(2) default NULL,
  PRIMARY KEY  (`id`),
  KEY `NewIndex1` (`fecha`),
  KEY `NewIndex2` (`anio`,`mes`,`dia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `global_datos` */

DROP TABLE IF EXISTS `global_datos`;

CREATE TABLE `global_datos` (
  `id` varchar(12) NOT NULL,
  `concepto_1` varchar(100) default NULL,
  `concepto_2` varchar(100) default NULL,
  `logico_1` tinyint(1) default '0',
  `logico_2` tinyint(1) default '0',
  `entero_1` int(11) default '0',
  `entero_2` int(11) default '0',
  `decimal_1` decimal(10,2) default '0.00',
  `decimal_2` decimal(10,2) default '0.00',
  `fecha_1` date default NULL,
  `fecha_2` date default NULL,
  `texto_1` text,
  `usuario_id` int(11) default '0',
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `concepto` (`concepto_1`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `grupos` */

DROP TABLE IF EXISTS `grupos`;

CREATE TABLE `grupos` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(40) NOT NULL default '',
  `activo` tinyint(1) default '1',
  `vista` tinyint(1) default '0',
  `consultar` tinyint(1) default '0',
  `agregar` tinyint(1) default '0',
  `modificar` tinyint(1) default '0',
  `borrar` tinyint(1) default '0',
  `user` varchar(150) default NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Table structure for table `grupos_permisos` */

DROP TABLE IF EXISTS `grupos_permisos`;

CREATE TABLE `grupos_permisos` (
  `grupo_id` int(11) NOT NULL default '0',
  `permiso_id` int(11) NOT NULL default '0',
  KEY `idx_grupo_permiso` (`grupo_id`,`permiso_id`),
  KEY `FK_grupos_permisos_permiso` (`permiso_id`),
  CONSTRAINT `FK_grupos_permisos_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`),
  CONSTRAINT `FK_grupos_permisos_permiso` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `liquidacion_cuotas` */

DROP TABLE IF EXISTS `liquidacion_cuotas`;

CREATE TABLE `liquidacion_cuotas` (
  `id` int(11) NOT NULL auto_increment,
  `liquidacion_id` int(11) default '0',
  `mutual_adicional_pendiente_id` int(11) default '0',
  `codigo_organismo` varchar(12) default NULL,
  `socio_id` int(11) default '0',
  `persona_beneficio_id` int(11) default '0',
  `orden_descuento_id` int(11) default '0',
  `orden_descuento_cuota_id` int(11) default '0',
  `tipo_orden_dto` varchar(5) default NULL,
  `tipo_producto` varchar(12) default NULL,
  `tipo_cuota` varchar(12) default NULL,
  `periodo_cuota` varchar(6) default NULL,
  `proveedor_id` int(11) default '0',
  `vencida` tinyint(1) default '0',
  `importe` decimal(10,2) default '0.00',
  PRIMARY KEY  (`id`),
  KEY `FK_liquidacion_cuotas_liquidacion` (`liquidacion_id`),
  KEY `FK_liquidacion_cuotas_socios` (`socio_id`),
  KEY `FK_liquidacion_cuotas_beneficio` (`persona_beneficio_id`),
  KEY `FK_liquidacion_cuotas_cuotas` (`orden_descuento_cuota_id`),
  KEY `FK_liquidacion_cuotas_proveedor` (`proveedor_id`),
  KEY `IDX_resumen_socio` (`liquidacion_id`,`codigo_organismo`,`socio_id`,`tipo_cuota`),
  CONSTRAINT `FK_liquidacion_cuotas_beneficio` FOREIGN KEY (`persona_beneficio_id`) REFERENCES `persona_beneficios` (`id`),
  CONSTRAINT `FK_liquidacion_cuotas_liquidacion` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidaciones` (`id`),
  CONSTRAINT `FK_liquidacion_cuotas_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `FK_liquidacion_cuotas_socios` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=138125 DEFAULT CHARSET=utf8;

/*Table structure for table `liquidacion_disenio_registros` */

DROP TABLE IF EXISTS `liquidacion_disenio_registros`;

CREATE TABLE `liquidacion_disenio_registros` (
  `id` int(11) NOT NULL auto_increment,
  `codigo_organismo` varchar(12) default NULL,
  `entrada_salida` varchar(1) default 'E',
  `banco_id` varchar(5) default NULL,
  `columna` int(11) default '0',
  `tipo_dato` varchar(1) default 'N',
  `longitud` int(11) default '0',
  `decimales` int(11) default '0',
  `modelo` varchar(100) default NULL,
  `modelo_campo` varchar(100) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_codigo_organismo` (`codigo_organismo`),
  KEY `idx_banco` (`banco_id`),
  KEY `idx_entrada_salida` (`entrada_salida`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `liquidacion_intercambio_registros` */

DROP TABLE IF EXISTS `liquidacion_intercambio_registros`;

CREATE TABLE `liquidacion_intercambio_registros` (
  `id` int(11) NOT NULL auto_increment,
  `liquidacion_intercambio_id` int(11) default '0',
  `liquidacion_id` int(11) default '0',
  `registro` varchar(200) default NULL,
  `C1` varchar(50) default NULL,
  `D1` decimal(10,2) default '0.00',
  `F1` date default NULL,
  `I1` int(11) default NULL,
  `C2` varchar(50) default NULL,
  `D2` decimal(10,2) default '0.00',
  `F2` date default NULL,
  `I2` int(11) default NULL,
  `C3` varchar(50) default NULL,
  `D3` decimal(10,2) default '0.00',
  `F3` date default NULL,
  `I3` int(11) default NULL,
  `C4` varchar(50) default NULL,
  `D4` decimal(10,2) default '0.00',
  `F4` date default NULL,
  `I4` int(11) default NULL,
  `C5` varchar(50) default NULL,
  `D5` decimal(10,2) default '0.00',
  `F5` date default NULL,
  `I5` int(11) default NULL,
  `C6` varchar(50) default NULL,
  `D6` decimal(10,2) default '0.00',
  `F6` date default NULL,
  `I6` int(11) default NULL,
  `C7` varchar(50) default NULL,
  `D7` decimal(10,2) default '0.00',
  `F7` date default NULL,
  `I7` int(11) default NULL,
  `C8` varchar(50) default NULL,
  `D8` decimal(10,2) default '0.00',
  `F8` date default NULL,
  `I8` int(11) default NULL,
  `C9` varchar(50) default NULL,
  `D9` decimal(10,2) default '0.00',
  `F9` date default NULL,
  `I9` int(11) default NULL,
  `C10` varchar(50) default NULL,
  `D10` decimal(10,2) default '0.00',
  `F10` date default NULL,
  `I10` int(11) default NULL,
  `C11` varchar(50) default NULL,
  `D11` decimal(10,2) default '0.00',
  `F11` date default NULL,
  `I11` int(11) default NULL,
  `C12` varchar(50) default NULL,
  `D12` decimal(10,2) default '0.00',
  `F12` date default NULL,
  `I12` int(11) default NULL,
  `C13` varchar(50) default NULL,
  `D13` decimal(10,2) default '0.00',
  `F13` date default NULL,
  `I13` int(11) default NULL,
  `C14` varchar(50) default NULL,
  `D14` decimal(10,2) default '0.00',
  `F14` date default NULL,
  `I14` int(11) default NULL,
  `C15` varchar(50) default NULL,
  `D15` decimal(10,2) default '0.00',
  `F15` date default NULL,
  `I15` int(11) default NULL,
  `C16` varchar(50) default NULL,
  `D16` decimal(10,2) default '0.00',
  `F16` date default NULL,
  `I16` int(11) default NULL,
  `C17` varchar(50) default NULL,
  `D17` decimal(10,2) default '0.00',
  `F17` date default NULL,
  `I17` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_liquidacion` (`liquidacion_intercambio_id`),
  KEY `idx_I13` (`I13`),
  KEY `idx_D6` (`D6`)
) ENGINE=InnoDB AUTO_INCREMENT=20562 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `liquidacion_intercambios` */

DROP TABLE IF EXISTS `liquidacion_intercambios`;

CREATE TABLE `liquidacion_intercambios` (
  `id` int(11) NOT NULL auto_increment,
  `liquidacion_id` int(11) default '0',
  `codigo_organismo` varchar(12) default NULL,
  `periodo` varchar(8) default NULL,
  `banco_id` varchar(5) default NULL,
  `archivo_nombre` varchar(100) default NULL,
  `archivo_file` varchar(100) default NULL,
  `target_path` varchar(200) default NULL,
  `observaciones` text,
  `procesado` tinyint(1) default '0',
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Table structure for table `liquidacion_socios` */

DROP TABLE IF EXISTS `liquidacion_socios`;

CREATE TABLE `liquidacion_socios` (
  `id` int(11) NOT NULL auto_increment,
  `liquidacion_id` int(11) default '0',
  `socio_id` int(11) default '0',
  `persona_beneficio_id` int(11) default '0',
  `codigo_organismo` varchar(12) default NULL,
  `nro_ley` varchar(2) default NULL,
  `tipo` varchar(1) default NULL,
  `nro_beneficio` varchar(20) default NULL,
  `sub_beneficio` varchar(2) default NULL,
  `codigo_dto` varchar(10) default NULL,
  `sub_codigo` varchar(1) default NULL,
  `banco` varchar(5) default NULL,
  `sucursal` varchar(5) default NULL,
  `tipo_cta_bco` varchar(4) default NULL,
  `nro_cta_bco` varchar(50) default NULL,
  `cbu` varchar(23) default NULL,
  `tipo_documento` varchar(12) default NULL,
  `documento` varchar(11) default NULL,
  `apenom` varchar(100) default NULL,
  `persona_id` int(11) default '0',
  `cuit_cuil` varchar(11) default NULL,
  `codigo_empresa` varchar(12) default NULL,
  `codigo_reparticion` varchar(11) default NULL,
  `importe_dto` decimal(10,2) default '0.00',
  `importe_recibido` decimal(10,2) default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_liquidacion_socios_liquidacion` (`liquidacion_id`),
  KEY `FK_liquidacion_socios_socios` (`socio_id`),
  KEY `FK_liquidacion_socios_beneficio` (`persona_beneficio_id`),
  KEY `idx_documento` (`documento`),
  KEY `idx_apenom` (`apenom`),
  KEY `idx_producto_beneficio_socio` (`liquidacion_id`,`socio_id`,`persona_beneficio_id`),
  CONSTRAINT `FK_liquidacion_socios_beneficio` FOREIGN KEY (`persona_beneficio_id`) REFERENCES `persona_beneficios` (`id`),
  CONSTRAINT `FK_liquidacion_socios_liquidacion` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidaciones` (`id`),
  CONSTRAINT `FK_liquidacion_socios_socios` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17590 DEFAULT CHARSET=utf8;

/*Table structure for table `liquidaciones` */

DROP TABLE IF EXISTS `liquidaciones`;

CREATE TABLE `liquidaciones` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) default '0',
  `codigo_organismo` varchar(12) default NULL,
  `cerrada` tinyint(1) default '0',
  `en_proceso` tinyint(1) default '1',
  `periodo` varchar(6) default NULL,
  `cuota_social_vencida` decimal(10,2) default '0.00',
  `cuota_social_periodo` decimal(10,2) default '0.00',
  `deuda_vencida` decimal(10,2) default '0.00',
  `deuda_periodo` decimal(10,2) default '0.00',
  `total_vencido` decimal(10,2) default '0.00',
  `total_periodo` decimal(10,2) default '0.00',
  `total` decimal(10,2) default '0.00',
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_periodo` (`periodo`,`id`,`codigo_organismo`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

/*Table structure for table `localidades` */

DROP TABLE IF EXISTS `localidades`;

CREATE TABLE `localidades` (
  `id` int(11) NOT NULL auto_increment,
  `cp` varchar(4) default NULL,
  `nombre` varchar(150) default NULL,
  `provincia_id` int(11) default '0',
  `letra_provincia` varchar(1) default NULL,
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  `idr` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `NewIndex1` (`cp`),
  KEY `NewIndex2` (`nombre`),
  KEY `NewIndex3` (`provincia_id`),
  KEY `NewIndex4` (`letra_provincia`),
  KEY `NewIndex5` (`idr`)
) ENGINE=InnoDB AUTO_INCREMENT=3510 DEFAULT CHARSET=utf8;

/*Table structure for table `mutual_adicional_pendientes` */

DROP TABLE IF EXISTS `mutual_adicional_pendientes`;

CREATE TABLE `mutual_adicional_pendientes` (
  `id` int(11) NOT NULL auto_increment,
  `liquidacion_id` int(11) default '0',
  `periodo` varchar(6) default NULL,
  `socio_id` int(11) default '0',
  `codigo_organismo` varchar(12) default NULL,
  `proveedor_id` int(11) default '0',
  `tipo` varchar(1) default 'P',
  `deuda_calcula` int(11) default '1',
  `valor` decimal(10,2) default '0.00',
  `tipo_cuota` varchar(12) default NULL,
  `total_deuda` decimal(10,2) default '0.00',
  `importe` decimal(10,2) default '0.00',
  `procesado` tinyint(1) default '0',
  `orden_descuento_id` int(11) default '0',
  `persona_beneficio_id` int(11) default '0',
  `orden_descuento_cuota_id` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `idx_socio` (`socio_id`),
  KEY `idx_proveedor` (`proveedor_id`),
  KEY `idx_liquidacion` (`liquidacion_id`),
  KEY `idx_orden_descuento` (`orden_descuento_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4082 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `mutual_adicionales` */

DROP TABLE IF EXISTS `mutual_adicionales`;

CREATE TABLE `mutual_adicionales` (
  `id` int(11) NOT NULL auto_increment,
  `codigo_organismo` varchar(12) default NULL,
  `proveedor_id` int(11) default '0',
  `tipo` varchar(1) default 'P',
  `valor` decimal(10,2) default '0.00',
  `devengado_previo` tinyint(1) default '0',
  `periodo_desde` varchar(6) default NULL,
  `periodo_hasta` varchar(6) default NULL,
  `deuda_calcula` int(11) default '1',
  `tipo_cuota` varchar(12) default NULL,
  `activo` tinyint(1) default '1',
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_organismo_proveedor` (`codigo_organismo`,`proveedor_id`),
  KEY `idx_periodo` (`periodo_desde`,`periodo_hasta`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `mutual_producto_solicitudes` */

DROP TABLE IF EXISTS `mutual_producto_solicitudes`;

CREATE TABLE `mutual_producto_solicitudes` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date default NULL,
  `fecha_pago` date default NULL,
  `tipo_orden_dto` varchar(12) default NULL,
  `tipo_producto` varchar(12) default NULL,
  `mutual_producto_id` int(11) default '0',
  `estado` varchar(12) default NULL,
  `socio_id` int(11) default '0',
  `persona_beneficio_id` int(11) default '0',
  `importe_total` decimal(10,2) default '0.00',
  `cuotas` int(11) default '1',
  `importe_cuota` decimal(10,2) default '0.00',
  `importe_solicitado` decimal(10,2) default '0.00',
  `importe_percibido` decimal(10,2) default '0.00',
  `periodo_ini` varchar(6) default NULL,
  `periodicidad` varchar(1) default '0',
  `primer_vto_socio` date default NULL,
  `primer_vto_proveedor` date default NULL,
  `observaciones` text,
  `permanente` tinyint(1) default '0',
  `orden_descuento_id` int(11) default '0',
  `nro_referencia_proveedor` varchar(20) default NULL,
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_mutual_producto_solicitudes_socios` (`socio_id`),
  KEY `idx_periodo` (`periodo_ini`),
  KEY `idx_tipo` (`tipo_orden_dto`),
  KEY `idx_producto` (`mutual_producto_id`),
  KEY `idx_expediente` (`orden_descuento_id`),
  KEY `idx_nro_ref_proveedor` (`nro_referencia_proveedor`),
  CONSTRAINT `FK_mutual_producto_solicitudes_socios` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `mutual_productos` */

DROP TABLE IF EXISTS `mutual_productos`;

CREATE TABLE `mutual_productos` (
  `id` int(11) NOT NULL auto_increment,
  `tipo_orden_dto` varchar(5) default NULL,
  `tipo_producto` varchar(12) default NULL,
  `activo` tinyint(1) default '1',
  `proveedor_id` int(11) default NULL,
  `importe_fijo` decimal(10,2) default '0.00',
  `cuota_social_diferenciada` decimal(10,2) default '0.00',
  `mensual` tinyint(1) default '0',
  `idr` varchar(5) default NULL,
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_mutual_productos_proveedor` (`proveedor_id`),
  KEY `idx_mensual` (`mensual`),
  KEY `idx_tipo_producto` (`tipo_producto`),
  CONSTRAINT `FK_mutual_productos_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8;

/*Table structure for table `orden_caja_cobro_cuotas` */

DROP TABLE IF EXISTS `orden_caja_cobro_cuotas`;

CREATE TABLE `orden_caja_cobro_cuotas` (
  `id` int(11) NOT NULL auto_increment,
  `orden_caja_cobro_id` int(11) unsigned default '0',
  `orden_descuento_cuota_id` int(11) default '0',
  `importe` decimal(10,2) default '0.00',
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_orden_caja_cobro_cuotas_cuota` (`orden_descuento_cuota_id`),
  KEY `FK_orden_caja_cobro_cuotas_caja` (`orden_caja_cobro_id`),
  CONSTRAINT `FK_orden_caja_cobro_cuotas_caja` FOREIGN KEY (`orden_caja_cobro_id`) REFERENCES `orden_caja_cobros` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `orden_caja_cobros` */

DROP TABLE IF EXISTS `orden_caja_cobros`;

CREATE TABLE `orden_caja_cobros` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `estado` varchar(1) default 'E',
  `fecha_vto` date default NULL,
  `socio_id` int(11) default '0',
  `importe` decimal(10,2) default '0.00',
  `barcode` varchar(40) default NULL,
  `orden_descuento_cobro_id` int(11) default '0',
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_barcode` (`barcode`),
  KEY `idx_vto` (`fecha_vto`),
  KEY `FK_orden_caja_cobros_socio` (`socio_id`),
  KEY `FK_orden_caja_cobros` (`orden_descuento_cobro_id`),
  CONSTRAINT `FK_orden_caja_cobros_socio` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `orden_descuento_cobro_cuotas` */

DROP TABLE IF EXISTS `orden_descuento_cobro_cuotas`;

CREATE TABLE `orden_descuento_cobro_cuotas` (
  `id` int(11) NOT NULL auto_increment,
  `orden_descuento_cobro_id` int(11) default '0',
  `orden_descuento_cuota_id` int(11) default '0',
  `proveedor_id` int(11) default '0',
  `importe` decimal(10,2) default '0.00',
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_proveedor` (`proveedor_id`),
  KEY `FK_orden_descuento_cobro_cuotas_cobros` (`orden_descuento_cobro_id`),
  KEY `FK_orden_descuento_cobro_cuotas_cuota` (`orden_descuento_cuota_id`),
  CONSTRAINT `FK_orden_descuento_cobro_cuotas_cobros` FOREIGN KEY (`orden_descuento_cobro_id`) REFERENCES `orden_descuento_cobros` (`id`),
  CONSTRAINT `FK_orden_descuento_cobro_cuotas_cuota` FOREIGN KEY (`orden_descuento_cuota_id`) REFERENCES `orden_descuento_cuotas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1388740 DEFAULT CHARSET=utf8;

/*Table structure for table `orden_descuento_cobros` */

DROP TABLE IF EXISTS `orden_descuento_cobros`;

CREATE TABLE `orden_descuento_cobros` (
  `id` int(11) NOT NULL auto_increment,
  `tipo_cobro` varchar(12) default NULL,
  `fecha` date default NULL,
  `importe` decimal(10,2) default '0.00',
  `nro_recibo` varchar(100) default NULL,
  `cancelacion_orden_id` int(11) default '0',
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_fecha` (`fecha`),
  KEY `idx_recibo` (`nro_recibo`),
  KEY `idx_tipo_cobro` (`tipo_cobro`)
) ENGINE=InnoDB AUTO_INCREMENT=32490 DEFAULT CHARSET=utf8;

/*Table structure for table `orden_descuento_cuotas` */

DROP TABLE IF EXISTS `orden_descuento_cuotas`;

CREATE TABLE `orden_descuento_cuotas` (
  `id` int(11) NOT NULL auto_increment,
  `orden_descuento_id` int(11) default '0',
  `socio_id` int(11) default '0',
  `persona_beneficio_id` int(11) default '0',
  `tipo_orden_dto` varchar(5) default NULL,
  `tipo_producto` varchar(12) default NULL,
  `tipo_cuota` varchar(12) default NULL,
  `periodo` varchar(6) default NULL,
  `periodicidad` varchar(1) default '0',
  `estado` varchar(1) default 'A',
  `situacion` varchar(12) default NULL,
  `vencimiento` date default NULL,
  `vencimiento_proveedor` date default NULL,
  `nro_cuota` int(11) default NULL,
  `importe` decimal(10,2) default '0.00',
  `proveedor_id` int(11) default '0',
  `nro_referencia_proveedor` varchar(20) default '0',
  `nro_orden_referencia` int(11) default '0',
  `codigo_comercio_referencia` varchar(10) default '0',
  `observaciones` text,
  `cancelacion_orden_id` int(11) default '0',
  `conciliada_proveedor` tinyint(1) default '0',
  `fecha_conciliacion_proveedor` date default NULL,
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  `od_detalle_id` int(11) default NULL,
  `importe_pagado` decimal(10,2) default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_orden_descuento_cuotas_od` (`orden_descuento_id`),
  KEY `FK_orden_descuento_cuotas_socio` (`socio_id`),
  KEY `FK_orden_descuento_cuotas_proveedor` (`proveedor_id`),
  KEY `idx_tipo_producto` (`tipo_producto`),
  KEY `idx_tipo_orden_dto` (`tipo_orden_dto`),
  KEY `idx_estado_situacion` (`situacion`),
  KEY `idx_periodo` (`periodo`,`periodicidad`),
  KEY `idx_referencia` (`nro_referencia_proveedor`,`nro_orden_referencia`,`codigo_comercio_referencia`),
  KEY `idx_cancelacion` (`cancelacion_orden_id`),
  KEY `idx_persona_beneficio` (`persona_beneficio_id`),
  KEY `idx_estado` (`estado`),
  KEY `tmp_od_detalle_id` (`od_detalle_id`),
  KEY `idx_periodo_estado_situacion` (`periodo`,`estado`,`situacion`),
  KEY `idx_socio_periodo` (`socio_id`,`periodo`,`estado`,`situacion`),
  KEY `idx_periodo_socio` (`socio_id`,`periodo`,`estado`,`situacion`),
  KEY `idx_liquidacion` (`orden_descuento_id`,`socio_id`,`periodo`),
  KEY `od_detalle_id` (`od_detalle_id`),
  CONSTRAINT `FK_orden_descuento_cuotas_od` FOREIGN KEY (`orden_descuento_id`) REFERENCES `orden_descuentos` (`id`),
  CONSTRAINT `FK_orden_descuento_cuotas_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `FK_orden_descuento_cuotas_socio` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1746792 DEFAULT CHARSET=utf8;

/*Table structure for table `orden_descuentos` */

DROP TABLE IF EXISTS `orden_descuentos`;

CREATE TABLE `orden_descuentos` (
  `id` int(11) NOT NULL auto_increment,
  `fecha` date default NULL,
  `tipo_orden_dto` varchar(5) default NULL,
  `numero` int(11) default '0',
  `tipo_producto` varchar(12) default NULL,
  `proveedor_id` int(11) default '0',
  `mutual_producto_id` int(11) default '0',
  `socio_id` int(11) default '0',
  `persona_beneficio_id` int(11) default '0',
  `periodo_ini` varchar(6) default NULL,
  `periodicidad` varchar(1) default '0',
  `importe_total` decimal(10,2) default '0.00',
  `importe_cuota` decimal(10,2) default '0.00',
  `primer_vto_socio` date default NULL,
  `primer_vto_proveedor` date default NULL,
  `cuotas` int(11) default '0',
  `activo` tinyint(1) default '1',
  `permanente` tinyint(1) default '0',
  `nro_referencia_proveedor` varchar(20) default NULL,
  `nro_orden_referencia` int(11) default '0',
  `codigo_comercio_referencia` varchar(10) default '0',
  `comision_cobranza` decimal(10,3) default '0.000',
  `comision_colocacion` decimal(10,3) default '0.000',
  `reprogramada` tinyint(1) default '0',
  `reasignada` tinyint(1) default '0',
  `observaciones` text,
  `conciliada_proveedor` tinyint(1) default '0',
  `fecha_conciliacion_proveedor` date default NULL,
  `mora_tecnica` tinyint(1) default '0',
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  `ztmp_odc_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_orden_descuentos_socio` (`socio_id`),
  KEY `FK_orden_descuentos_beneficio` (`persona_beneficio_id`),
  KEY `idx_periodo` (`periodo_ini`),
  KEY `idx_proveedor` (`proveedor_id`),
  KEY `idx_tipo_numero` (`tipo_orden_dto`,`numero`),
  KEY `idx_producto` (`tipo_producto`),
  KEY `idx_created` (`created`),
  KEY `idx_fecha` (`fecha`),
  KEY `idx_referencia` (`nro_orden_referencia`,`codigo_comercio_referencia`,`nro_referencia_proveedor`),
  KEY `idx_permanente` (`permanente`),
  KEY `idx_activo_permanente` (`activo`,`permanente`),
  KEY `idx_activo` (`activo`),
  KEY `idx_mora_tecnica` (`mora_tecnica`),
  KEY `idx_liquida` (`socio_id`,`activo`,`permanente`),
  KEY `ztmp_odc_id` (`ztmp_odc_id`),
  CONSTRAINT `FK_orden_descuentos_beneficio` FOREIGN KEY (`persona_beneficio_id`) REFERENCES `persona_beneficios` (`id`),
  CONSTRAINT `FK_orden_descuentos_socio` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=75696 DEFAULT CHARSET=utf8;

/*Table structure for table `permisos` */

DROP TABLE IF EXISTS `permisos`;

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(50) default NULL,
  `url` varchar(255) default NULL,
  `order` int(11) default '0',
  `main` tinyint(1) default '0',
  `quick` tinyint(1) default '0',
  `icon` varchar(50) default NULL,
  `activo` tinyint(1) default '1',
  `parent` int(11) default '0',
  `obs` mediumtext,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_parent` (`parent`),
  KEY `idx_order` (`order`),
  KEY `idx_main` (`main`),
  KEY `idx_quick` (`quick`),
  KEY `idx_url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `persona_beneficios` */

DROP TABLE IF EXISTS `persona_beneficios`;

CREATE TABLE `persona_beneficios` (
  `id` int(11) NOT NULL auto_increment,
  `persona_id` int(11) default '0',
  `codigo_beneficio` varchar(12) default NULL,
  `nro_ley` varchar(50) default NULL,
  `tipo` varchar(1) default NULL,
  `nro_beneficio` varchar(50) default NULL,
  `sub_beneficio` varchar(2) default NULL,
  `nro_legajo` varchar(50) default NULL,
  `fecha_ingreso` date default NULL,
  `codigo_reparticion` varchar(11) default NULL,
  `cbu` varchar(23) default NULL,
  `banco_id` varchar(5) default NULL,
  `sucursal_id` int(11) default '0',
  `nro_sucursal` varchar(5) default NULL,
  `tipo_cta_bco` varchar(4) default NULL,
  `nro_cta_bco` varchar(50) default NULL,
  `codigo_empresa` varchar(12) default NULL,
  `principal` tinyint(1) default '0',
  `activo` tinyint(1) default '1',
  `porcentaje` decimal(10,2) default '0.00',
  `idr` int(11) default '0',
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  `accion` varchar(1) default NULL,
  `codigo_baja` varchar(12) default NULL,
  `dupli` tinyint(1) default '0',
  `fecha_baja` date default NULL,
  `observaciones` text,
  `reasignado_id` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `FK_persona_beneficios` (`persona_id`),
  KEY `idx_codigo_beneficio` (`id`,`persona_id`,`codigo_beneficio`),
  CONSTRAINT `FK_persona_beneficios` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15011 DEFAULT CHARSET=utf8;

/*Table structure for table `personas` */

DROP TABLE IF EXISTS `personas`;

CREATE TABLE `personas` (
  `id` int(11) NOT NULL auto_increment,
  `tipo_documento` varchar(12) default NULL,
  `documento` varchar(11) default NULL,
  `apellido` varchar(100) default NULL,
  `nombre` varchar(100) default NULL,
  `fecha_nacimiento` date default NULL,
  `fecha_fallecimiento` date default NULL,
  `fallecida` tinyint(1) default '0',
  `sexo` varchar(1) default NULL,
  `estado_civil` varchar(12) default NULL,
  `calle` varchar(150) default NULL,
  `numero_calle` varchar(5) default NULL,
  `piso` varchar(5) default NULL,
  `dpto` varchar(5) default NULL,
  `barrio` varchar(100) default NULL,
  `localidad_id` int(11) default '0',
  `localidad` varchar(150) default NULL,
  `codigo_postal` varchar(8) default NULL,
  `provincia_id` int(11) default '0',
  `cuit_cuil` varchar(11) default NULL,
  `nombre_conyuge` varchar(150) default NULL,
  `telefono_fijo` varchar(50) default NULL,
  `telefono_movil` varchar(50) default NULL,
  `telefono_referencia` varchar(50) default NULL,
  `persona_referencia` varchar(100) default NULL,
  `e_mail` varchar(100) default NULL,
  `tipo_vivienda` varchar(12) default NULL,
  `filial` varchar(12) default NULL,
  `idr` int(11) default '0',
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_tdocNdoc_unico` (`tipo_documento`,`documento`),
  KEY `idx_tdoc_ndoc_apenom` (`tipo_documento`,`documento`,`apellido`,`nombre`),
  KEY `idx_localidad` (`localidad_id`),
  KEY `idx_ndoc` (`documento`),
  KEY `idx_idr_v1` (`idr`)
) ENGINE=InnoDB AUTO_INCREMENT=16816 DEFAULT CHARSET=utf8;

/*Table structure for table `proveedor_vencimientos` */

DROP TABLE IF EXISTS `proveedor_vencimientos`;

CREATE TABLE `proveedor_vencimientos` (
  `id` int(11) NOT NULL auto_increment,
  `proveedor_id` int(11) default '0',
  `codigo_organismo` varchar(12) default NULL,
  `d_corte` int(2) default NULL,
  `d_vto_socio` int(2) default NULL,
  `d_vto_proveedor_suma` int(2) default NULL,
  `mes` varchar(2) default NULL,
  `m_ini_socio_ac_suma` int(2) default '0',
  `m_ini_socio_dc_suma` int(2) default '0',
  `m_vto_socio_suma` int(2) default '0',
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_mes` (`mes`),
  KEY `idx_proveedor` (`proveedor_id`),
  KEY `idx_organismo` (`codigo_organismo`),
  KEY `idx_organismo_proveedor_mes` (`proveedor_id`,`codigo_organismo`,`mes`)
) ENGINE=InnoDB AUTO_INCREMENT=1513 DEFAULT CHARSET=utf8;

/*Table structure for table `proveedores` */

DROP TABLE IF EXISTS `proveedores`;

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL auto_increment,
  `cuit` varchar(11) default NULL,
  `razon_social` varchar(150) default NULL,
  `razon_social_resumida` varbinary(50) default NULL,
  `activo` tinyint(1) default NULL,
  `calle` varchar(150) default NULL,
  `numero_calle` varchar(5) default NULL,
  `piso` varchar(5) default NULL,
  `dpto` varchar(5) default NULL,
  `barrio` varchar(50) default NULL,
  `localidad` varchar(100) default NULL,
  `codigo_postal` varchar(11) default NULL,
  `nro_ingresos_brutos` varchar(15) default NULL,
  `condicion_iva` varchar(12) default NULL,
  `idr` varchar(11) default NULL,
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_idr` (`idr`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

/*Table structure for table `provincias` */

DROP TABLE IF EXISTS `provincias`;

CREATE TABLE `provincias` (
  `id` int(11) NOT NULL auto_increment,
  `nombre` varchar(100) default NULL,
  `letra` varchar(1) default NULL,
  PRIMARY KEY  (`id`),
  KEY `NewIndex1` (`nombre`),
  KEY `NewIndex2` (`letra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `socio_calificaciones` */

DROP TABLE IF EXISTS `socio_calificaciones`;

CREATE TABLE `socio_calificaciones` (
  `id` int(11) NOT NULL auto_increment,
  `socio_id` int(11) default '0',
  `calificacion` varchar(12) default NULL,
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_socio_calificaciones_socio` (`socio_id`),
  KEY `idx_calificacion` (`calificacion`),
  KEY `idx_created` (`created`),
  CONSTRAINT `FK_socio_calificaciones_socio` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `socio_historicos` */

DROP TABLE IF EXISTS `socio_historicos`;

CREATE TABLE `socio_historicos` (
  `id` int(11) NOT NULL auto_increment,
  `socio_id` int(11) default '0',
  `socio_solicitud_id` int(11) default '0',
  `activo` tinyint(1) default '1',
  `calificacion` varchar(12) default NULL,
  `codigo_baja` varchar(12) default NULL,
  `created` datetime default NULL,
  `fecha_alta` date default NULL,
  `fecha_baja` date default NULL,
  `fecha_calificacion` date default NULL,
  `modified` datetime default NULL,
  `observaciones` text,
  `orden_descuento_id` int(11) default '0',
  `periodicidad` varchar(1) default '0',
  `periodo_ini` varchar(6) default NULL,
  `persona_beneficio_id` int(11) default '0',
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_socio` (`socio_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `socio_solicitudes` */

DROP TABLE IF EXISTS `socio_solicitudes`;

CREATE TABLE `socio_solicitudes` (
  `id` int(11) NOT NULL auto_increment,
  `tipo_solicitud` varchar(12) default NULL,
  `aprobada` tinyint(1) default '0',
  `persona_id` int(11) default '0',
  `persona_beneficio_id` int(11) default '0',
  `fecha` date default NULL,
  `periodo_ini` varchar(6) default NULL,
  `periodicidad` varchar(1) default '0',
  `observaciones` text,
  `orden_descuento_id` int(11) default '0',
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  `primer_vto_proveedor` date default NULL,
  `primer_vto_socio` date default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_socio_solicitudes_personas` (`persona_id`),
  KEY `FK_socio_solicitudes_beneficio` (`persona_beneficio_id`),
  KEY `idx_periodo` (`periodo_ini`),
  KEY `idx_expediente` (`orden_descuento_id`),
  CONSTRAINT `FK_socio_solicitudes_beneficio` FOREIGN KEY (`persona_beneficio_id`) REFERENCES `persona_beneficios` (`id`),
  CONSTRAINT `FK_socio_solicitudes_personas` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16101 DEFAULT CHARSET=utf8;

/*Table structure for table `socios` */

DROP TABLE IF EXISTS `socios`;

CREATE TABLE `socios` (
  `id` int(11) NOT NULL auto_increment,
  `persona_id` int(11) default '0',
  `socio_solicitud_id` int(11) default '0',
  `persona_beneficio_id` int(11) default '0',
  `periodo_ini` varchar(6) default NULL,
  `periodicidad` varchar(1) default '0',
  `activo` tinyint(1) default '1',
  `fecha_alta` date default NULL,
  `orden_descuento_id` int(11) default '0',
  `calificacion` varchar(12) default NULL,
  `fecha_calificacion` date default NULL,
  `codigo_baja` varchar(12) default NULL,
  `fecha_baja` date default NULL,
  `observaciones` text,
  `idr` int(11) default '0',
  `user_created` varchar(50) default NULL,
  `user_modified` varchar(50) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_socios` (`persona_id`),
  KEY `FK_socios_solicitud` (`socio_solicitud_id`),
  KEY `FK_socios_beneficios` (`persona_beneficio_id`),
  KEY `idx_periodo` (`periodo_ini`,`periodicidad`),
  KEY `idx_idr` (`idr`),
  KEY `idx_orden_descuento` (`orden_descuento_id`),
  KEY `idx_calificacion` (`calificacion`,`fecha_calificacion`),
  KEY `idx_activo` (`activo`),
  CONSTRAINT `FK_socios_beneficios` FOREIGN KEY (`persona_beneficio_id`) REFERENCES `persona_beneficios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16101 DEFAULT CHARSET=utf8;

/*Table structure for table `tmp_cambio_estado_P` */

DROP TABLE IF EXISTS `tmp_cambio_estado_P`;

CREATE TABLE `tmp_cambio_estado_P` (
  `id` int(11) NOT NULL default '0',
  `socio_id` int(11) default '0',
  `estado` varchar(1) default 'A',
  `importe` decimal(10,2) default '0.00',
  `pago` decimal(32,2) default NULL,
  KEY `idx_id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `usuarios` */

DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL auto_increment,
  `grupo_id` int(11) default '0',
  `usuario` varchar(10) NOT NULL default '',
  `password` varchar(40) NOT NULL,
  `activo` tinyint(1) NOT NULL default '0',
  `descripcion` varchar(100) default NULL,
  `user` varchar(50) default NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_grupo` (`grupo_id`),
  KEY `idx_usuario_password` (`usuario`,`password`),
  CONSTRAINT `FK_usuarios_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
