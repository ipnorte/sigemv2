/*
SQLyog Community Edition- MySQL GUI v8.01 
MySQL - 5.0.75-0ubuntu10.2-log : Database - aman2_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `liquidacion_cuotas` */

DROP TABLE IF EXISTS `liquidacion_cuotas`;

CREATE TABLE `liquidacion_cuotas` (
  `id` int(11) NOT NULL auto_increment,
  `liquidacion_id` int(11) default '0',
  `codigo_organismo` varchar(12) default NULL,
  `socio_id` int(11) default '0',
  `persona_beneficio_id` int(11) default '0',
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
  CONSTRAINT `FK_liquidacion_cuotas_beneficio` FOREIGN KEY (`persona_beneficio_id`) REFERENCES `persona_beneficios` (`id`),
  CONSTRAINT `FK_liquidacion_cuotas_cuotas` FOREIGN KEY (`orden_descuento_cuota_id`) REFERENCES `orden_descuento_cuotas` (`id`),
  CONSTRAINT `FK_liquidacion_cuotas_liquidacion` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidaciones` (`id`),
  CONSTRAINT `FK_liquidacion_cuotas_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `FK_liquidacion_cuotas_socios` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=695227 DEFAULT CHARSET=utf8;

/*Table structure for table `liquidacion_permanente_resumenes` */

DROP TABLE IF EXISTS `liquidacion_permanente_resumenes`;

CREATE TABLE `liquidacion_permanente_resumenes` (
  `id` int(11) NOT NULL auto_increment,
  `periodo` varchar(6) default NULL,
  `proveedor_id` int(11) default '0',
  `tipo_producto` varchar(12) default NULL,
  `tipo_cuota` varchar(12) default NULL,
  `codigo_organismo` varchar(12) default NULL,
  `importe_liquidado` decimal(10,2) default '0.00',
  `cantidad_ordenes_procesadas` int(11) default '0',
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_periodo` (`periodo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=11130 DEFAULT CHARSET=utf8;

/*Table structure for table `liquidaciones` */

DROP TABLE IF EXISTS `liquidaciones`;

CREATE TABLE `liquidaciones` (
  `id` int(11) NOT NULL auto_increment,
  `pid` int(11) default '0',
  `cerrada` tinyint(1) default '0',
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
  KEY `idx_periodo` (`periodo`,`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
