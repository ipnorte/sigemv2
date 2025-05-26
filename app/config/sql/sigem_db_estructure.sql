-- MySQL dump 10.13  Distrib 5.7.22, for Linux (x86_64)
--
-- Host: 190.12.101.100    Database: mutual22_sigemdb
-- ------------------------------------------------------
-- Server version	5.5.59-cll

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `asincrono_errores`
--

DROP TABLE IF EXISTS `asincrono_errores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asincrono_errores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asincrono_id` int(11) DEFAULT '0',
  `porcentaje` int(11) DEFAULT '0',
  `registro_nro` int(11) DEFAULT '0',
  `mensaje_1` longtext,
  `mensaje_2` longtext,
  `mensaje_3` longtext,
  `mensaje_4` longtext,
  PRIMARY KEY (`id`),
  KEY `FK_asincrono_errores_asincronos` (`asincrono_id`),
  CONSTRAINT `FK_ASINCRONO_ERRORES_ASINCRONOS` FOREIGN KEY (`asincrono_id`) REFERENCES `asincronos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asincrono_temporal_detalles`
--

DROP TABLE IF EXISTS `asincrono_temporal_detalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asincrono_temporal_detalles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asincrono_id` int(11) DEFAULT '0',
  `asincrono_temporal_id` int(11) DEFAULT '0',
  `texto_1` varchar(100) DEFAULT NULL,
  `texto_2` varchar(100) DEFAULT NULL,
  `texto_3` varchar(100) DEFAULT NULL,
  `texto_4` varchar(100) DEFAULT NULL,
  `texto_5` varchar(100) DEFAULT NULL,
  `texto_6` varchar(100) DEFAULT NULL,
  `texto_7` varchar(100) DEFAULT NULL,
  `texto_8` varchar(100) DEFAULT NULL,
  `texto_9` varchar(100) DEFAULT NULL,
  `texto_10` varchar(100) DEFAULT NULL,
  `texto_11` varchar(100) DEFAULT NULL,
  `texto_12` varchar(100) DEFAULT NULL,
  `decimal_1` decimal(10,2) DEFAULT '0.00',
  `decimal_2` decimal(10,2) DEFAULT '0.00',
  `decimal_3` decimal(10,2) DEFAULT '0.00',
  `decimal_4` decimal(10,2) DEFAULT '0.00',
  `decimal_5` decimal(10,2) DEFAULT '0.00',
  `decimal_6` decimal(10,2) DEFAULT '0.00',
  `entero_1` int(11) DEFAULT '0',
  `entero_2` int(11) DEFAULT '0',
  `entero_3` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_asincrono_temporal_detalles` (`asincrono_temporal_id`),
  KEY `idx_asincrono_id` (`asincrono_id`),
  CONSTRAINT `FK_ASINCRONO_TEMPDET_ASINCRONOS` FOREIGN KEY (`asincrono_id`) REFERENCES `asincronos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asincrono_temporales`
--

DROP TABLE IF EXISTS `asincrono_temporales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asincrono_temporales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asincrono_id` int(11) DEFAULT '0',
  `clave_1` varchar(50) DEFAULT NULL,
  `clave_2` varchar(50) DEFAULT NULL,
  `clave_3` varchar(50) DEFAULT NULL,
  `texto_1` varchar(100) DEFAULT NULL,
  `texto_2` varchar(100) DEFAULT NULL,
  `texto_3` varchar(100) DEFAULT NULL,
  `texto_4` varchar(100) DEFAULT NULL,
  `texto_5` varchar(100) DEFAULT NULL,
  `texto_6` varchar(100) DEFAULT NULL,
  `texto_7` varchar(100) DEFAULT NULL,
  `texto_8` varchar(100) DEFAULT NULL,
  `texto_9` varchar(100) DEFAULT NULL,
  `texto_10` varchar(100) DEFAULT NULL,
  `texto_11` varchar(100) DEFAULT NULL,
  `texto_12` varchar(100) DEFAULT NULL,
  `texto_13` varchar(100) DEFAULT NULL,
  `texto_14` varchar(100) DEFAULT NULL,
  `texto_15` varchar(100) DEFAULT NULL,
  `texto_16` varchar(100) DEFAULT NULL,
  `texto_17` varchar(100) DEFAULT NULL,
  `texto_18` varchar(100) DEFAULT NULL,
  `texto_19` varchar(100) DEFAULT NULL,
  `texto_20` varchar(100) DEFAULT NULL,
  `decimal_1` decimal(10,2) DEFAULT '0.00',
  `decimal_2` decimal(10,2) DEFAULT '0.00',
  `decimal_3` decimal(10,2) DEFAULT '0.00',
  `decimal_4` decimal(10,2) DEFAULT '0.00',
  `decimal_5` decimal(10,2) DEFAULT '0.00',
  `decimal_6` decimal(10,2) DEFAULT '0.00',
  `decimal_7` decimal(10,2) DEFAULT '0.00',
  `decimal_8` decimal(10,2) DEFAULT '0.00',
  `decimal_9` decimal(10,2) DEFAULT '0.00',
  `decimal_10` decimal(10,2) DEFAULT '0.00',
  `decimal_11` decimal(10,2) DEFAULT '0.00',
  `decimal_12` decimal(10,2) DEFAULT '0.00',
  `decimal_13` decimal(10,2) DEFAULT '0.00',
  `decimal_14` decimal(10,2) DEFAULT '0.00',
  `decimal_15` decimal(10,2) DEFAULT '0.00',
  `entero_1` int(11) DEFAULT '0',
  `entero_2` int(11) DEFAULT '0',
  `entero_3` int(11) DEFAULT '0',
  `entero_4` int(11) DEFAULT NULL,
  `entero_5` int(11) DEFAULT NULL,
  `entero_6` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_asincrono` (`asincrono_id`),
  KEY `idx_clave_1` (`clave_1`),
  KEY `idx_clave_2` (`clave_2`),
  KEY `idx_clave_3` (`clave_3`),
  CONSTRAINT `FK_ASINCRONO_TEMP_ASINCRONOS` FOREIGN KEY (`asincrono_id`) REFERENCES `asincronos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2334473 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asincronos`
--

DROP TABLE IF EXISTS `asincronos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asincronos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shell_pid` int(11) DEFAULT '0',
  `propietario` varchar(120) DEFAULT NULL,
  `remote_ip` varchar(100) DEFAULT NULL,
  `final` datetime DEFAULT NULL,
  `proceso` varchar(150) DEFAULT NULL,
  `bloqueado` tinyint(1) DEFAULT '0',
  `p1` varchar(250) DEFAULT NULL,
  `p2` varchar(250) DEFAULT NULL,
  `p3` varchar(250) DEFAULT NULL,
  `p4` varchar(250) DEFAULT NULL,
  `p5` varchar(250) DEFAULT NULL,
  `p6` varchar(250) DEFAULT NULL,
  `p7` varchar(250) DEFAULT NULL,
  `p8` varchar(250) DEFAULT NULL,
  `p9` varchar(250) DEFAULT NULL,
  `p10` varchar(250) DEFAULT NULL,
  `p11` varchar(250) DEFAULT NULL,
  `p12` varchar(250) DEFAULT NULL,
  `p13` varchar(250) DEFAULT NULL,
  `txt1` text,
  `txt2` text,
  `action_do` varchar(150) DEFAULT NULL,
  `target` varchar(50) DEFAULT NULL,
  `btn_label` varchar(150) DEFAULT 'IMPRIMIR',
  `titulo` varchar(250) DEFAULT NULL,
  `subtitulo` varchar(250) DEFAULT NULL,
  `estado` varchar(1) DEFAULT NULL,
  `total` int(11) DEFAULT '0',
  `contador` int(11) DEFAULT '0',
  `porcentaje` int(11) DEFAULT '0',
  `msg` varchar(150) DEFAULT NULL,
  `errores` int(11) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `parent_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_proceso` (`proceso`),
  KEY `idx_propietario` (`propietario`,`remote_ip`),
  KEY `idx_parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=52970 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banco_cheque_terceros`
--

DROP TABLE IF EXISTS `banco_cheque_terceros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banco_cheque_terceros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banco_cuenta_movimiento_id` int(11) DEFAULT '0',
  `salida_banco_cuenta_movimiento_id` int(11) DEFAULT '0',
  `recibo_id` int(11) DEFAULT '0',
  `orden_pago_id` int(11) DEFAULT '0',
  `banco_id` char(5) DEFAULT NULL,
  `plaza` varchar(100) DEFAULT NULL,
  `numero_cheque` varchar(12) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `fecha_cheque` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `fecha_baja` date DEFAULT NULL,
  `librador` varchar(100) DEFAULT NULL,
  `destinatario` varchar(100) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `persona_id` int(11) DEFAULT '0',
  `proveedor_id` int(11) DEFAULT '0',
  `codigo_organismo` varchar(12) DEFAULT NULL,
  `importe` decimal(12,2) DEFAULT '0.00',
  `caja` tinyint(1) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=222 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banco_concepto_individuales`
--

DROP TABLE IF EXISTS `banco_concepto_individuales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banco_concepto_individuales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banco_cuenta_id` int(11) DEFAULT '0',
  `banco_concepto_id` int(11) DEFAULT '0',
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `debe_haber` tinyint(1) DEFAULT '0',
  `tipo` int(1) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banco_conceptos`
--

DROP TABLE IF EXISTS `banco_conceptos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banco_conceptos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `concepto` varchar(100) DEFAULT NULL,
  `debe_haber` tinyint(1) DEFAULT '0',
  `tipo` int(1) DEFAULT '6',
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banco_cuenta_chequeras`
--

DROP TABLE IF EXISTS `banco_cuenta_chequeras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banco_cuenta_chequeras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banco_cuenta_id` int(11) DEFAULT '0',
  `concepto` varchar(200) DEFAULT NULL,
  `serie` varchar(1) DEFAULT NULL,
  `desde_numero` int(11) DEFAULT '0',
  `hasta_numero` int(11) DEFAULT '0',
  `proximo_numero` int(11) DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  `look` tinyint(1) DEFAULT '0',
  `user_created` varchar(100) DEFAULT NULL,
  `user_modified` varchar(100) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banco_cuenta_movimientos`
--

DROP TABLE IF EXISTS `banco_cuenta_movimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banco_cuenta_movimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banco_cuenta_id` int(11) DEFAULT '0',
  `banco_cuenta_chequera_id` int(11) DEFAULT '0',
  `recibo_id` int(11) DEFAULT '0',
  `orden_pago_id` int(11) DEFAULT '0',
  `cancelacion_orden_id` int(11) DEFAULT '0',
  `orden_caja_cobro_id` int(11) DEFAULT '0',
  `orden_descuento_cobro_id` int(11) DEFAULT '0',
  `banco_cuenta_movimiento_id` int(11) DEFAULT '0',
  `numero_operacion` varchar(12) DEFAULT NULL,
  `fecha_operacion` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `banco_concepto_id` int(11) DEFAULT '0',
  `destinatario` varchar(100) DEFAULT NULL,
  `descripcion` varchar(50) DEFAULT NULL,
  `importe` decimal(15,2) DEFAULT '0.00',
  `codigo_conciliacion` varchar(12) DEFAULT NULL,
  `conciliado` tinyint(1) DEFAULT '0',
  `banco_cuenta_saldo_id` int(11) DEFAULT '0',
  `debe_haber` tinyint(1) DEFAULT '0',
  `tipo` int(1) DEFAULT '1',
  `banco_cheque_tercero_id` int(11) DEFAULT '0',
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `anulado` tinyint(1) DEFAULT '0',
  `reemplazar` tinyint(1) DEFAULT '0',
  `fecha_reemplazar` date DEFAULT '1970-01-01',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_banco_cuenta_id` (`banco_cuenta_id`),
  KEY `idx_banco_cuenta_chequera_id` (`banco_cuenta_chequera_id`),
  KEY `idx_recibo_id` (`recibo_id`),
  KEY `idx_orden_pago_id` (`orden_pago_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24206 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banco_cuenta_saldos`
--

DROP TABLE IF EXISTS `banco_cuenta_saldos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banco_cuenta_saldos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banco_cuenta_id` int(11) DEFAULT '0',
  `numero` varchar(12) DEFAULT NULL,
  `fecha_cierre` date DEFAULT NULL,
  `saldo_anterior` decimal(15,2) DEFAULT '0.00',
  `saldo_referencia_1` decimal(15,2) DEFAULT '0.00',
  `saldo_referencia_2` decimal(15,2) DEFAULT '0.00',
  `saldo_conciliacion` decimal(15,2) DEFAULT '0.00',
  `tipo_conciliacion` tinyint(1) DEFAULT '0',
  `numero_extracto` varchar(30) DEFAULT NULL,
  `fecha_extracto` date DEFAULT NULL,
  `debe` decimal(15,2) DEFAULT NULL,
  `haber` decimal(15,2) DEFAULT NULL,
  `saldo_extracto` decimal(15,2) DEFAULT '0.00',
  `fecha_anterior` date DEFAULT NULL,
  `asincrono_id` int(11) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=373 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banco_cuentas`
--

DROP TABLE IF EXISTS `banco_cuentas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banco_cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banco_id` int(5) unsigned zerofill DEFAULT '00000',
  `fecha_apertura` date DEFAULT NULL,
  `chequeras` tinyint(1) DEFAULT '0',
  `denominacion` varchar(150) DEFAULT NULL,
  `caja_general` tinyint(1) DEFAULT '0',
  `numero` varchar(20) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `fecha_conciliacion` date DEFAULT NULL,
  `importe_conciliacion` decimal(15,2) DEFAULT '0.00',
  `tipo_conciliacion` tinyint(1) DEFAULT '0',
  `numero_planilla` varchar(10) DEFAULT '0',
  `banco_cuenta_saldo_id` int(11) DEFAULT '0',
  `banco_cuenta_saldo_alta_id` int(11) DEFAULT '0',
  `numero_extracto` varchar(30) DEFAULT NULL,
  `fecha_extracto` date DEFAULT NULL,
  `saldo_extracto` decimal(15,2) DEFAULT '0.00',
  `user_created` varchar(100) DEFAULT NULL,
  `user_modified` varchar(100) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_banco_id` (`banco_id`),
  KEY `idx_banco_id_nro_cuenta` (`banco_id`,`numero`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banco_rendicion_codigos`
--

DROP TABLE IF EXISTS `banco_rendicion_codigos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banco_rendicion_codigos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banco_id` varchar(5) DEFAULT NULL,
  `codigo` varchar(3) DEFAULT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `indica_pago` tinyint(1) DEFAULT '0',
  `calificacion_socio` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=413 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banco_sucursales`
--

DROP TABLE IF EXISTS `banco_sucursales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banco_sucursales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banco_id` char(5) DEFAULT NULL,
  `nro_sucursal` char(10) DEFAULT NULL,
  `nombre` char(100) DEFAULT NULL,
  `direccion` char(100) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `NewIndex1` (`banco_id`),
  CONSTRAINT `FK_banco_sucursales` FOREIGN KEY (`banco_id`) REFERENCES `bancos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=277 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bancos`
--

DROP TABLE IF EXISTS `bancos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bancos` (
  `id` char(5) NOT NULL,
  `nombre` char(100) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `beneficio` tinyint(1) DEFAULT '0',
  `fpago` tinyint(1) DEFAULT '0',
  `intercambio` tinyint(1) DEFAULT '0',
  `tipo_registro` int(11) DEFAULT '1',
  `longitud` int(11) DEFAULT '0',
  `longitud_salida` int(11) DEFAULT '0',
  `indicador_cabecera` varchar(5) DEFAULT NULL,
  `indicador_detalle` varchar(5) DEFAULT NULL,
  `indicador_pie` varchar(5) DEFAULT NULL,
  `tipo_cta_sueldo` varchar(2) DEFAULT NULL,
  `nro_cta_acredita_debito` varchar(50) DEFAULT NULL,
  `parametros_intercambio` longtext,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nombre_banco` (`nombre`),
  KEY `idx_activo_beneficio_fpago` (`activo`,`beneficio`,`fpago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `barrios`
--

DROP TABLE IF EXISTS `barrios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `barrios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) DEFAULT NULL,
  `localidad_id` int(11) DEFAULT '405',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cancelacion_orden_cuotas`
--

DROP TABLE IF EXISTS `cancelacion_orden_cuotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cancelacion_orden_cuotas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cancelacion_orden_id` int(11) DEFAULT '0',
  `orden_descuento_cuota_id` int(11) DEFAULT '0',
  `importe` decimal(10,2) DEFAULT '0.00',
  `proveedor_id` int(11) DEFAULT '0',
  `cuota_vencida` tinyint(1) DEFAULT '0',
  `alicuota_comision_cobranza` decimal(10,3) DEFAULT '0.000',
  `comision_cobranza` decimal(10,2) DEFAULT '0.00',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cancelacion_cuota_cancelacion_id` (`cancelacion_orden_id`),
  KEY `idx_cancelacion_cuota_orden_dto_cuota_id` (`orden_descuento_cuota_id`),
  KEY `idx_cancelacion_cuotas_orden_dto_cuota` (`orden_descuento_cuota_id`),
  CONSTRAINT `fk_cancelacion_cuota_cancelacion_id` FOREIGN KEY (`cancelacion_orden_id`) REFERENCES `cancelacion_ordenes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8402 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cancelacion_ordenes`
--

DROP TABLE IF EXISTS `cancelacion_ordenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cancelacion_ordenes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `origen_proveedor_id` int(11) DEFAULT '0',
  `orden_proveedor_id` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `orden_descuento_id` int(11) DEFAULT NULL,
  `importe_proveedor` decimal(10,2) DEFAULT '0.00',
  `importe_seleccionado` decimal(10,2) DEFAULT '0.00',
  `fecha_vto` date DEFAULT NULL,
  `tipo_cancelacion` varchar(1) DEFAULT NULL,
  `importe_cuota` decimal(10,2) DEFAULT '0.00',
  `estado` varchar(1) DEFAULT 'E',
  `saldo_orden_dto` decimal(10,2) DEFAULT '0.00',
  `persona_idr` int(11) DEFAULT '0',
  `observaciones` text,
  `forma_cancelacion` varchar(12) DEFAULT NULL,
  `forma_pago` varchar(12) DEFAULT NULL,
  `banco_id` varchar(5) DEFAULT NULL,
  `nro_cta_bco` varchar(50) DEFAULT NULL,
  `nro_operacion` varchar(50) DEFAULT NULL,
  `fecha_operacion` date DEFAULT NULL,
  `pendiente_rendicion_proveedor` tinyint(1) DEFAULT '0',
  `tipo_cuota_diferencia` varchar(12) DEFAULT NULL,
  `importe_diferencia` decimal(10,2) DEFAULT '0.00',
  `cuota_diferencia_id` int(11) DEFAULT '0',
  `recibo_id` int(11) DEFAULT '0',
  `nro_recibo` varchar(100) DEFAULT NULL,
  `fecha_imputacion` date DEFAULT NULL,
  `nueva_orden_dto_id` int(11) DEFAULT '0',
  `nro_solicitud` int(11) DEFAULT '0',
  `orden_pago_id` int(11) DEFAULT '0',
  `proveedor_factura_id` int(11) DEFAULT '0',
  `cliente_factura_id` int(11) DEFAULT '0',
  `orden_descuento_cobro_id` int(11) DEFAULT '0',
  `banco_cuenta_movimiento_id` int(11) DEFAULT '0',
  `credito_proveedor_factura_id` int(11) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `mutual_producto_solicitud_id` int(11) DEFAULT NULL,
  `concepto` text,
  PRIMARY KEY (`id`),
  KEY `idx_orden_proveedor` (`orden_proveedor_id`),
  KEY `idx_orden_dto` (`orden_descuento_id`),
  KEY `idx_socio` (`socio_id`,`persona_idr`),
  KEY `idx_nueva_orden_dto` (`nueva_orden_dto_id`),
  KEY `idx_reporte` (`fecha_vto`,`forma_cancelacion`,`fecha_imputacion`,`created`),
  KEY `FK_CANCELACION_TIPO_CUOTA_DIFERENCIA` (`tipo_cuota_diferencia`),
  KEY `FK_CANCELACION_MUTUAL_PRODUCTO_SOLICITUD` (`mutual_producto_solicitud_id`),
  KEY `idx_cancelacion_orden_estado` (`estado`),
  CONSTRAINT `FK_CANCELACION_MUTUAL_PRODUCTO_SOLICITUD` FOREIGN KEY (`mutual_producto_solicitud_id`) REFERENCES `mutual_producto_solicitudes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_cancelacion_ordenes_proveedor_destino` FOREIGN KEY (`orden_proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `FK_CANCELACION_ORDEN_DESCUENTO` FOREIGN KEY (`orden_descuento_id`) REFERENCES `orden_descuentos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_CANCELACION_SOCIO` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`),
  CONSTRAINT `FK_CANCELACION_TIPO_CUOTA_DIFERENCIA` FOREIGN KEY (`tipo_cuota_diferencia`) REFERENCES `global_datos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2712 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cliente_ctactes`
--

DROP TABLE IF EXISTS `cliente_ctactes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cliente_ctactes` (
  `item` int(11) DEFAULT '0',
  `cliente_id` int(11) DEFAULT '0',
  `fecha` date DEFAULT NULL,
  `concepto` varchar(50) DEFAULT NULL,
  `debe` decimal(12,2) DEFAULT '0.00',
  `haber` decimal(12,2) DEFAULT '0.00',
  `saldo` decimal(12,2) DEFAULT '0.00',
  `id` int(11) DEFAULT '0',
  `tipo` varchar(5) DEFAULT NULL,
  `anular` tinyint(1) DEFAULT '0',
  `comentario` varchar(100) DEFAULT NULL,
  `pagos` decimal(12,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cliente_factura_detalles`
--

DROP TABLE IF EXISTS `cliente_factura_detalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cliente_factura_detalles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_factura_id` int(11) DEFAULT '0',
  `producto` varchar(100) DEFAULT NULL,
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `cantidad` decimal(5,2) DEFAULT '0.00',
  `precio_unitario` decimal(15,2) DEFAULT '0.00',
  `precio_total` decimal(15,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `idx_cliente_factura_id` (`cliente_factura_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2684 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cliente_facturas`
--

DROP TABLE IF EXISTS `cliente_facturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cliente_facturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(2) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT '0',
  `fecha_comprobante` date DEFAULT NULL,
  `tipo_comprobante` varchar(12) DEFAULT NULL,
  `letra_comprobante` varchar(1) DEFAULT NULL,
  `punto_venta_comprobante` varchar(4) DEFAULT NULL,
  `numero_comprobante` varchar(8) DEFAULT NULL,
  `importe_gravado` decimal(15,2) DEFAULT '0.00',
  `importe_no_gravado` decimal(15,2) DEFAULT '0.00',
  `tasa_iva` int(3) DEFAULT '0',
  `importe_iva` decimal(15,2) DEFAULT '0.00',
  `percepcion` decimal(15,2) DEFAULT '0.00',
  `retencion` decimal(15,2) DEFAULT '0.00',
  `impuesto_interno` decimal(15,2) DEFAULT '0.00',
  `ingreso_bruto` decimal(15,2) DEFAULT '0.00',
  `otro_impuesto` decimal(15,2) DEFAULT '0.00',
  `total_comprobante` decimal(15,2) DEFAULT '0.00',
  `ejercicio_id` int(11) DEFAULT '0',
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `vencimiento1` date DEFAULT NULL,
  `vencimiento2` date DEFAULT NULL,
  `vencimiento3` date DEFAULT NULL,
  `vencimiento4` date DEFAULT NULL,
  `vencimiento5` date DEFAULT NULL,
  `vencimiento6` date DEFAULT NULL,
  `vencimiento7` date DEFAULT NULL,
  `vencimiento8` date DEFAULT NULL,
  `vencimiento9` date DEFAULT NULL,
  `vencimiento10` date DEFAULT NULL,
  `importe_venc1` decimal(15,2) DEFAULT '0.00',
  `importe_venc2` decimal(15,2) DEFAULT '0.00',
  `importe_venc3` decimal(15,2) DEFAULT '0.00',
  `importe_venc4` decimal(15,2) DEFAULT '0.00',
  `importe_venc5` decimal(15,2) DEFAULT '0.00',
  `importe_venc6` decimal(15,2) DEFAULT '0.00',
  `importe_venc7` decimal(15,2) DEFAULT '0.00',
  `importe_venc8` decimal(15,2) DEFAULT '0.00',
  `importe_venc9` decimal(15,2) DEFAULT '0.00',
  `importe_venc10` decimal(15,2) DEFAULT '0.00',
  `estado` varchar(1) DEFAULT NULL,
  `cliente_tipo_asiento_id` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `orden_descuento_id` int(11) DEFAULT '0',
  `liquidacion_id` int(11) DEFAULT '0',
  `cancelacion_orden_id` int(11) DEFAULT '0',
  `orden_caja_cobro_id` int(11) DEFAULT '0',
  `orden_descuento_cobro_id` int(11) DEFAULT '0',
  `comentario` varchar(100) DEFAULT NULL,
  `anulado` tinyint(1) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `proveedor_liquidacion_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idu_letra_suc_nro` (`letra_comprobante`,`punto_venta_comprobante`,`numero_comprobante`),
  KEY `idx_cliente_id` (`cliente_id`),
  KEY `idx_ejercio_plan_cuenta` (`ejercicio_id`,`co_plan_cuenta_id`),
  KEY `idx_liquidacion_id` (`liquidacion_id`),
  KEY `idx_orden_caja_cobro` (`orden_caja_cobro_id`),
  KEY `idx_orden_descuento_cobro` (`orden_descuento_cobro_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2684 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cliente_tipo_asiento_renglones`
--

DROP TABLE IF EXISTS `cliente_tipo_asiento_renglones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cliente_tipo_asiento_renglones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variable` varchar(5) DEFAULT NULL,
  `cliente_tipo_asiento_id` int(11) DEFAULT '0',
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `debe_haber` varchar(1) DEFAULT 'D',
  `tipo_asiento` varchar(2) DEFAULT 'GR',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cliente_tipo_asientos`
--

DROP TABLE IF EXISTS `cliente_tipo_asientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cliente_tipo_asientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `concepto` varchar(100) DEFAULT NULL,
  `tipo_asiento` varchar(2) NOT NULL DEFAULT 'GR',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cuit` varchar(11) DEFAULT NULL,
  `razon_social` varchar(150) DEFAULT NULL,
  `razon_social_resumida` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT NULL,
  `calle` varchar(150) DEFAULT NULL,
  `numero_calle` varchar(5) DEFAULT NULL,
  `piso` varchar(5) DEFAULT NULL,
  `dpto` varchar(5) DEFAULT NULL,
  `barrio` varchar(50) DEFAULT NULL,
  `localidad` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(11) DEFAULT NULL,
  `telefono_fijo` varchar(50) DEFAULT NULL,
  `telefono_movil` varchar(50) DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `concepto_gasto` varchar(12) DEFAULT NULL,
  `cliente_tipo_asiento_id` int(11) DEFAULT '0',
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `nro_ingresos_brutos` varchar(15) DEFAULT NULL,
  `condicion_iva` varchar(12) DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT '0',
  `responsable` varchar(150) DEFAULT NULL,
  `contacto` varchar(150) DEFAULT NULL,
  `fecha_saldo` date DEFAULT NULL,
  `importe_saldo` decimal(15,2) DEFAULT '0.00',
  `tipo_saldo` varchar(1) DEFAULT NULL,
  `cliente_factura_id` int(11) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=464 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `co_asiento_renglones`
--

DROP TABLE IF EXISTS `co_asiento_renglones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `co_asiento_renglones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `co_asiento_id` int(11) DEFAULT '0',
  `fecha` date DEFAULT NULL,
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `referencia` varchar(100) DEFAULT NULL,
  `debe` decimal(15,2) DEFAULT '0.00',
  `haber` decimal(15,2) DEFAULT '0.00',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_asiento_plancuenta` (`co_asiento_id`,`co_plan_cuenta_id`),
  KEY `FK_co_asiento_renglones_plancuenta` (`co_plan_cuenta_id`),
  CONSTRAINT `FK_co_asiento_renglones_asiento` FOREIGN KEY (`co_asiento_id`) REFERENCES `co_asientos` (`id`),
  CONSTRAINT `FK_co_asiento_renglones_plancuenta` FOREIGN KEY (`co_plan_cuenta_id`) REFERENCES `co_plan_cuentas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=75892 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `co_asientos`
--

DROP TABLE IF EXISTS `co_asientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `co_asientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mutual_asiento_id` int(11) DEFAULT '0',
  `nro_asiento` int(11) DEFAULT '0',
  `co_ejercicio_id` int(11) DEFAULT '0',
  `fecha` date DEFAULT NULL,
  `tipo_documento` varchar(3) DEFAULT NULL,
  `nro_documento` varchar(30) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `debe` decimal(15,2) DEFAULT '0.00',
  `haber` decimal(15,2) DEFAULT '0.00',
  `co_asiento_id` int(11) DEFAULT '0',
  `anulado` tinyint(1) DEFAULT '0',
  `borrado` tinyint(1) DEFAULT '0',
  `cierre` tinyint(1) DEFAULT '0',
  `tipo` int(1) DEFAULT '2',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_nroasiento_ejercicio` (`nro_asiento`,`co_ejercicio_id`),
  KEY `FK_co_asientos` (`co_ejercicio_id`),
  CONSTRAINT `FK_co_asientos` FOREIGN KEY (`co_ejercicio_id`) REFERENCES `co_ejercicios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39982 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `co_ejercicios`
--

DROP TABLE IF EXISTS `co_ejercicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `co_ejercicios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) DEFAULT NULL,
  `fecha_desde` date DEFAULT NULL,
  `fecha_hasta` date DEFAULT NULL,
  `fecha_cierre` date DEFAULT NULL,
  `fecha_cierre_periodo` date DEFAULT NULL,
  `nivel` int(1) DEFAULT '0',
  `nivel_1` int(1) DEFAULT '0',
  `nivel_2` int(1) DEFAULT '0',
  `nivel_3` int(1) DEFAULT '0',
  `nivel_4` int(1) DEFAULT '0',
  `nivel_5` int(1) DEFAULT '0',
  `nivel_6` int(1) DEFAULT '0',
  `nro_asiento` int(11) DEFAULT '1',
  `look` tinyint(1) DEFAULT '0',
  `fecha_asiento` date DEFAULT NULL,
  `fecha_proceso` date DEFAULT NULL,
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `resultado_co_plan_cuenta_id` int(11) DEFAULT '0',
  `activo` tinyint(1) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `co_plan_cuentas`
--

DROP TABLE IF EXISTS `co_plan_cuentas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `co_plan_cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cuenta` varchar(12) DEFAULT NULL,
  `co_ejercicio_id` int(11) DEFAULT '0',
  `descripcion` varchar(50) DEFAULT NULL,
  `imputable` tinyint(1) DEFAULT '0' COMMENT '0-No Imputable, 1-Imputalbe',
  `actualiza` tinyint(1) DEFAULT '0' COMMENT '0-No Actualiza, 1-Actualiza',
  `nivel` int(1) DEFAULT '0',
  `tipo_cuenta` varchar(2) DEFAULT NULL COMMENT 'AC-Activo, PA-Pasivo, PN-Patr.Neto, RP-Resultado Positivo, RN-Resultado Negativo',
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `sumariza` varchar(12) DEFAULT NULL,
  `acumulado_debe` decimal(15,2) DEFAULT '0.00',
  `acumulado_haber` decimal(15,2) DEFAULT '0.00',
  `vincula_co_plan_cuenta_id` int(11) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_cuenta_ejercicio` (`cuenta`,`co_ejercicio_id`),
  KEY `idx_descripcion` (`co_ejercicio_id`,`descripcion`),
  CONSTRAINT `FK_co_plan_cuentas` FOREIGN KEY (`co_ejercicio_id`) REFERENCES `co_ejercicios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2383 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `configuracion_asientos`
--

DROP TABLE IF EXISTS `configuracion_asientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuracion_asientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_orden_dto` varchar(5) NOT NULL DEFAULT '',
  `tipo_producto` varchar(12) NOT NULL DEFAULT '',
  `tipo_cuota` varchar(12) NOT NULL DEFAULT '',
  `concepto_producto` varchar(100) NOT NULL DEFAULT '',
  `concepto_cuota` varchar(100) NOT NULL DEFAULT '',
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `configurar_impresion_detalles`
--

DROP TABLE IF EXISTS `configurar_impresion_detalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configurar_impresion_detalles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `configurar_impresion_id` int(11) NOT NULL,
  `variable` varchar(12) DEFAULT NULL,
  `izquierda` decimal(5,2) DEFAULT NULL,
  `superior` decimal(5,2) DEFAULT NULL,
  `ancho` decimal(5,2) DEFAULT NULL,
  `alto` decimal(5,2) DEFAULT NULL,
  `formato` varchar(25) DEFAULT NULL,
  `imprime` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_configurar_impresion_detalles_configurar_impresiones1` (`configurar_impresion_id`),
  CONSTRAINT `fk_configurar_impresion_detalles_configurar_impresiones1` FOREIGN KEY (`configurar_impresion_id`) REFERENCES `configurar_impresiones` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `configurar_impresiones`
--

DROP TABLE IF EXISTS `configurar_impresiones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configurar_impresiones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) DEFAULT NULL,
  `ancho` decimal(5,2) DEFAULT NULL,
  `alto` decimal(5,2) DEFAULT NULL,
  `talonario` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feriados`
--

DROP TABLE IF EXISTS `feriados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feriados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `anio` int(4) DEFAULT '0',
  `mes` int(2) DEFAULT '0',
  `dia` int(2) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `NewIndex1` (`fecha`),
  KEY `NewIndex2` (`anio`,`mes`,`dia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `global_datos`
--

DROP TABLE IF EXISTS `global_datos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `global_datos` (
  `id` varchar(12) NOT NULL,
  `concepto_1` varchar(100) DEFAULT NULL,
  `concepto_2` varchar(100) DEFAULT NULL,
  `concepto_3` varchar(100) DEFAULT NULL,
  `concepto_4` varchar(100) DEFAULT NULL,
  `logico_1` tinyint(1) DEFAULT '0',
  `logico_2` tinyint(1) DEFAULT '0',
  `logico_3` tinyint(1) DEFAULT '0',
  `entero_1` int(11) DEFAULT '0',
  `entero_2` int(11) DEFAULT '0',
  `decimal_1` decimal(10,2) DEFAULT '0.00',
  `decimal_2` decimal(10,2) DEFAULT '0.00',
  `fecha_1` date DEFAULT NULL,
  `fecha_2` date DEFAULT NULL,
  `texto_1` text,
  `texto_2` text,
  `usuario_id` int(11) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `concepto` (`concepto_1`),
  KEY `idx_entero_1` (`entero_1`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `grupos`
--

DROP TABLE IF EXISTS `grupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grupos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(40) NOT NULL DEFAULT '',
  `activo` tinyint(1) DEFAULT '1',
  `vista` tinyint(1) DEFAULT '0',
  `consultar` tinyint(1) DEFAULT '0',
  `agregar` tinyint(1) DEFAULT '0',
  `modificar` tinyint(1) DEFAULT '0',
  `borrar` tinyint(1) DEFAULT '0',
  `user` varchar(150) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `grupos_permisos`
--

DROP TABLE IF EXISTS `grupos_permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grupos_permisos` (
  `grupo_id` int(11) NOT NULL DEFAULT '0',
  `permiso_id` int(11) NOT NULL DEFAULT '0',
  KEY `idx_grupo_permiso` (`grupo_id`,`permiso_id`),
  KEY `FK_grupos_permisos_permiso` (`permiso_id`),
  CONSTRAINT `FK_grupos_permisos_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`),
  CONSTRAINT `FK_grupos_permisos_permiso` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inaes_localidades`
--

DROP TABLE IF EXISTS `inaes_localidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inaes_localidades` (
  `cod_provin` int(11) DEFAULT NULL,
  `cod_local` int(11) DEFAULT NULL,
  `localidad` varchar(150) DEFAULT NULL,
  `c_postal` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inaes_provincias`
--

DROP TABLE IF EXISTS `inaes_provincias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inaes_provincias` (
  `cod_prov` varchar(150) DEFAULT NULL,
  `provincia` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `liquidacion_cuota_recuperos`
--

DROP TABLE IF EXISTS `liquidacion_cuota_recuperos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liquidacion_cuota_recuperos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `liquidacion_cuota_id` int(11) DEFAULT '0',
  `liquidacion_id` int(11) DEFAULT '0',
  `orden_descuento_cobro_id` int(11) DEFAULT '0',
  `orden_descuento_cuota_id` int(11) DEFAULT '0',
  `orden_descuento_id` int(11) DEFAULT '0',
  `proveedor_factura_id` int(11) DEFAULT '0',
  `cliente_factura_id` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `proveedor_id` int(11) DEFAULT '0',
  `periodo_socio` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `periodo_proveedor` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `cuotas` int(11) DEFAULT '1',
  `importe_liquidado` decimal(10,2) DEFAULT '0.00',
  `saldo_actual` decimal(10,2) DEFAULT '0.00',
  `alicuota_comision_cobranza` decimal(10,3) DEFAULT '0.000',
  `comision_cobranza` decimal(10,2) DEFAULT '0.00',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `user_created` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `user_modified` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index2` (`liquidacion_cuota_id`,`liquidacion_id`,`orden_descuento_cobro_id`,`socio_id`,`proveedor_id`,`orden_descuento_id`),
  KEY `fk_orden_descuento_cobro` (`orden_descuento_cobro_id`),
  KEY `fk_proveedores` (`proveedor_id`),
  KEY `fk_socios` (`socio_id`),
  KEY `fk_orden_descuento_cuotas` (`orden_descuento_cuota_id`),
  KEY `fk_liquidaciones` (`liquidacion_id`),
  KEY `fk_proveedor_facturas` (`proveedor_factura_id`),
  KEY `fk_cliente_factura` (`cliente_factura_id`),
  CONSTRAINT `fk_liquidaciones` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidaciones` (`id`),
  CONSTRAINT `fk_liquidacion_cuotas` FOREIGN KEY (`liquidacion_cuota_id`) REFERENCES `liquidacion_cuotas` (`id`),
  CONSTRAINT `fk_orden_descuento_cobro` FOREIGN KEY (`orden_descuento_cobro_id`) REFERENCES `orden_descuento_cobros` (`id`),
  CONSTRAINT `fk_orden_descuento_cuotas` FOREIGN KEY (`orden_descuento_cuota_id`) REFERENCES `orden_descuento_cuotas` (`id`),
  CONSTRAINT `fk_proveedores` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `fk_socios` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `liquidacion_cuotas`
--

DROP TABLE IF EXISTS `liquidacion_cuotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liquidacion_cuotas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `liquidacion_id` int(11) DEFAULT '0',
  `registro` int(11) DEFAULT '1',
  `mutual_adicional_pendiente_id` int(11) DEFAULT '0',
  `codigo_organismo` varchar(12) DEFAULT NULL,
  `socio_id` int(11) DEFAULT '0',
  `persona_beneficio_id` int(11) DEFAULT '0',
  `orden_descuento_id` int(11) DEFAULT NULL,
  `orden_descuento_cuota_id` int(11) DEFAULT NULL,
  `tipo_orden_dto` varchar(5) DEFAULT NULL,
  `tipo_producto` varchar(12) DEFAULT NULL,
  `tipo_cuota` varchar(12) DEFAULT NULL,
  `periodo_cuota` varchar(6) DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT '0',
  `vencida` tinyint(1) DEFAULT '0',
  `importe` decimal(10,2) DEFAULT '0.00',
  `saldo_actual` decimal(10,2) DEFAULT '0.00',
  `importe_debitado` decimal(10,2) DEFAULT '0.00',
  `liquidacion_intercambio_id` int(11) DEFAULT '0',
  `para_imputar` tinyint(1) DEFAULT '0',
  `imputada` tinyint(1) DEFAULT '0',
  `orden_descuento_cobro_id` int(11) DEFAULT '0',
  `orden_descuento_cobro_cuota_id` int(11) DEFAULT '0',
  `proveedor_liquidacion_id` int(11) DEFAULT '0',
  `socio_reintegro_id` int(11) DEFAULT '0',
  `alicuota_comision_cobranza` decimal(10,3) DEFAULT '0.000',
  `comision_cobranza` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `FK_liquidacion_cuotas_liquidacion` (`liquidacion_id`),
  KEY `FK_liquidacion_cuotas_socios` (`socio_id`),
  KEY `FK_liquidacion_cuotas_beneficio` (`persona_beneficio_id`),
  KEY `FK_liquidacion_cuotas_cuotas` (`orden_descuento_cuota_id`),
  KEY `FK_liquidacion_cuotas_proveedor` (`proveedor_id`),
  KEY `IDX_resumen_socio` (`liquidacion_id`,`codigo_organismo`,`socio_id`,`tipo_cuota`),
  KEY `idx_liquidacion_intercambio_id` (`liquidacion_intercambio_id`),
  KEY `idx_intercambio` (`socio_id`,`liquidacion_intercambio_id`),
  KEY `idx_imputacion_pagos` (`liquidacion_id`,`importe_debitado`,`liquidacion_intercambio_id`),
  KEY `idx_liquidacion_socio_registro` (`liquidacion_id`,`socio_id`,`registro`),
  KEY `idx_imputada` (`liquidacion_id`,`imputada`,`proveedor_id`),
  KEY `IDX_adicional_imputado` (`liquidacion_id`,`mutual_adicional_pendiente_id`,`orden_descuento_id`,`importe_debitado`,`imputada`),
  KEY `idx_listado_proveedor` (`liquidacion_id`,`proveedor_id`,`tipo_producto`,`tipo_cuota`),
  KEY `liquidacion_id` (`liquidacion_id`,`socio_id`,`para_imputar`,`imputada`),
  KEY `FK_liquidacion_cuotas_orddto_orden_id` (`orden_descuento_id`),
  KEY `idx_liquidacion_cuotas_cobro_id` (`orden_descuento_cobro_id`),
  CONSTRAINT `FK_liquidacion_cuotas_beneficio` FOREIGN KEY (`persona_beneficio_id`) REFERENCES `persona_beneficios` (`id`),
  CONSTRAINT `FK_liquidacion_cuotas_liquidacion` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidaciones` (`id`),
  CONSTRAINT `FK_liquidacion_cuotas_orddtocta_cuota_id` FOREIGN KEY (`orden_descuento_cuota_id`) REFERENCES `orden_descuento_cuotas` (`id`),
  CONSTRAINT `FK_liquidacion_cuotas_orddto_orden_id` FOREIGN KEY (`orden_descuento_id`) REFERENCES `orden_descuentos` (`id`),
  CONSTRAINT `FK_liquidacion_cuotas_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `FK_liquidacion_cuotas_socios` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2844649 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `liquidacion_disenio_registros`
--

DROP TABLE IF EXISTS `liquidacion_disenio_registros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liquidacion_disenio_registros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_organismo` varchar(12) DEFAULT NULL,
  `entrada_salida` varchar(1) DEFAULT 'E',
  `banco_id` varchar(5) DEFAULT NULL,
  `columna` int(11) DEFAULT '0',
  `tipo_dato` varchar(1) DEFAULT 'C',
  `longitud` int(11) DEFAULT '0',
  `decimales` int(11) DEFAULT '0',
  `columna_destino` varchar(5) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `modelo_campo` varchar(100) DEFAULT NULL,
  `condicion_igualacion` tinyint(1) DEFAULT '0',
  `orden_igualacion` int(11) DEFAULT '0',
  `codigo_status` tinyint(1) DEFAULT '0',
  `campo_consulta` tinyint(1) DEFAULT '0',
  `agrupa` tinyint(1) DEFAULT '0',
  `sumar` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_codigo_organismo` (`codigo_organismo`),
  KEY `idx_banco` (`banco_id`),
  KEY `idx_entrada_salida` (`entrada_salida`),
  KEY `idx_criterios` (`modelo`,`modelo_campo`,`orden_igualacion`,`codigo_status`,`campo_consulta`,`agrupa`,`sumar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `liquidacion_intercambio_registro_procesados`
--

DROP TABLE IF EXISTS `liquidacion_intercambio_registro_procesados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liquidacion_intercambio_registro_procesados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `liquidacion_id` int(11) DEFAULT '0',
  `codigo_organismo` varchar(12) CHARACTER SET latin1 DEFAULT NULL,
  `nro_ley` varchar(10) DEFAULT NULL,
  `tipo` varchar(1) DEFAULT NULL,
  `nro_beneficio` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `sub_beneficio` varchar(2) DEFAULT NULL,
  `codigo_dto` varchar(10) CHARACTER SET latin1 DEFAULT '0',
  `sub_codigo` varchar(1) CHARACTER SET latin1 DEFAULT '0',
  `cbu` varchar(23) CHARACTER SET latin1 DEFAULT NULL,
  `banco_intercambio` varchar(5) CHARACTER SET latin1 DEFAULT NULL,
  `documento` varchar(11) CHARACTER SET latin1 DEFAULT NULL,
  `importe_debitado` decimal(10,2) DEFAULT '0.00',
  `status` varchar(3) CHARACTER SET latin1 DEFAULT NULL,
  `indica_pago` tinyint(1) DEFAULT '0',
  `fecha_pago` date DEFAULT NULL,
  `liquidacion_socio_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_liquidacion` (`liquidacion_id`),
  KEY `idx_documento` (`documento`),
  KEY `idx_codigo_dto` (`codigo_dto`,`sub_codigo`),
  KEY `idx_beneficio` (`codigo_organismo`,`nro_beneficio`),
  KEY `idx_indica_pago` (`liquidacion_id`,`indica_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `liquidacion_intercambio_registros`
--

DROP TABLE IF EXISTS `liquidacion_intercambio_registros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liquidacion_intercambio_registros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `liquidacion_intercambio_id` int(11) DEFAULT '0',
  `liquidacion_id` int(11) DEFAULT '0',
  `banco_intercambio` varchar(5) DEFAULT NULL,
  `registro` varchar(200) DEFAULT NULL,
  `C1` varchar(50) DEFAULT NULL,
  `D1` decimal(10,2) DEFAULT '0.00',
  `F1` date DEFAULT NULL,
  `I1` int(11) DEFAULT '0',
  `C2` varchar(50) DEFAULT NULL,
  `D2` decimal(10,2) DEFAULT '0.00',
  `F2` date DEFAULT NULL,
  `I2` int(11) DEFAULT '0',
  `C3` varchar(50) DEFAULT NULL,
  `D3` decimal(10,2) DEFAULT '0.00',
  `F3` date DEFAULT NULL,
  `I3` int(11) DEFAULT '0',
  `C4` varchar(50) DEFAULT NULL,
  `D4` decimal(10,2) DEFAULT '0.00',
  `F4` date DEFAULT NULL,
  `I4` int(11) DEFAULT '0',
  `C5` varchar(50) DEFAULT NULL,
  `D5` decimal(10,2) DEFAULT '0.00',
  `F5` date DEFAULT NULL,
  `I5` int(11) DEFAULT '0',
  `C6` varchar(50) DEFAULT NULL,
  `D6` decimal(10,2) DEFAULT '0.00',
  `F6` date DEFAULT NULL,
  `I6` int(11) DEFAULT '0',
  `C7` varchar(50) DEFAULT NULL,
  `D7` decimal(10,2) DEFAULT '0.00',
  `F7` date DEFAULT NULL,
  `I7` int(11) DEFAULT '0',
  `C8` varchar(50) DEFAULT NULL,
  `D8` decimal(10,2) DEFAULT '0.00',
  `F8` date DEFAULT NULL,
  `I8` int(11) DEFAULT '0',
  `C9` varchar(50) DEFAULT NULL,
  `D9` decimal(10,2) DEFAULT '0.00',
  `F9` date DEFAULT NULL,
  `I9` int(11) DEFAULT '0',
  `C10` varchar(50) DEFAULT NULL,
  `D10` decimal(10,2) DEFAULT '0.00',
  `F10` date DEFAULT NULL,
  `I10` int(11) DEFAULT '0',
  `C11` varchar(50) DEFAULT NULL,
  `D11` decimal(10,2) DEFAULT '0.00',
  `F11` date DEFAULT NULL,
  `I11` int(11) DEFAULT '0',
  `C12` varchar(50) DEFAULT NULL,
  `D12` decimal(10,2) DEFAULT '0.00',
  `F12` date DEFAULT NULL,
  `I12` int(11) DEFAULT '0',
  `C13` varchar(50) DEFAULT NULL,
  `D13` decimal(10,2) DEFAULT '0.00',
  `F13` date DEFAULT NULL,
  `I13` int(11) DEFAULT '0',
  `C14` varchar(50) DEFAULT NULL,
  `D14` decimal(10,2) DEFAULT '0.00',
  `F14` date DEFAULT NULL,
  `I14` int(11) DEFAULT '0',
  `C15` varchar(50) DEFAULT NULL,
  `D15` decimal(10,2) DEFAULT '0.00',
  `F15` date DEFAULT NULL,
  `I15` int(11) DEFAULT '0',
  `C16` varchar(50) DEFAULT NULL,
  `D16` decimal(10,2) DEFAULT '0.00',
  `F16` date DEFAULT NULL,
  `I16` int(11) DEFAULT '0',
  `C17` varchar(50) DEFAULT NULL,
  `D17` decimal(10,2) DEFAULT '0.00',
  `F17` date DEFAULT NULL,
  `I17` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_I13` (`I13`),
  KEY `idx_D6` (`D6`),
  KEY `idx_liquidacion` (`liquidacion_intercambio_id`,`liquidacion_id`),
  KEY `idx_C11` (`C11`),
  KEY `idx_analisis` (`liquidacion_id`,`banco_intercambio`,`liquidacion_intercambio_id`,`C14`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `liquidacion_intercambios`
--

DROP TABLE IF EXISTS `liquidacion_intercambios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liquidacion_intercambios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `liquidacion_id` int(11) DEFAULT '0',
  `codigo_organismo` varchar(12) DEFAULT NULL,
  `periodo` varchar(8) DEFAULT NULL,
  `banco_id` varchar(5) DEFAULT NULL,
  `archivo_nombre` varchar(100) DEFAULT NULL,
  `archivo_file` varchar(100) DEFAULT NULL,
  `target_path` varchar(200) DEFAULT NULL,
  `observaciones` text,
  `fragmentado` tinyint(1) DEFAULT '0',
  `procesado` tinyint(1) DEFAULT '0',
  `total_registros` int(10) DEFAULT '0',
  `registros_cobrados` int(10) DEFAULT '0',
  `importe_cobrado` decimal(15,2) DEFAULT '0.00',
  `recibo_id` int(11) DEFAULT '0',
  `proveedor_id` int(11) DEFAULT '0',
  `lote` longtext,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2054 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `liquidacion_socio_descuentos`
--

DROP TABLE IF EXISTS `liquidacion_socio_descuentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liquidacion_socio_descuentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `periodo_liquidacion` varchar(6) DEFAULT NULL,
  `codigo_organismo` varchar(12) DEFAULT NULL,
  `socio_id` int(11) DEFAULT '0',
  `orden_descuento_id` int(11) DEFAULT '0',
  `comprobante` int(11) DEFAULT '0',
  `periodo_origen_comprobante` varchar(6) DEFAULT NULL,
  `fecha_comprobante` date DEFAULT NULL,
  `importe_total` decimal(10,2) DEFAULT '0.00',
  `cuotas` int(11) DEFAULT '0',
  `importe_cuota` decimal(10,2) DEFAULT '0.00',
  `inicia_en` varchar(6) DEFAULT NULL,
  `finaliza_en` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_orden_descuento` (`orden_descuento_id`,`periodo_liquidacion`,`codigo_organismo`),
  KEY `idx_liquidacion` (`periodo_liquidacion`,`codigo_organismo`,`socio_id`),
  KEY `idx_comprobante` (`comprobante`),
  KEY `idx_inicio_fin` (`inicia_en`,`finaliza_en`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `liquidacion_socio_envio_registros`
--

DROP TABLE IF EXISTS `liquidacion_socio_envio_registros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liquidacion_socio_envio_registros` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `liquidacion_socio_envio_id` int(11) DEFAULT '0',
  `liquidacion_socio_id` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT NULL,
  `identificador_debito` char(22) DEFAULT NULL,
  `importe_adebitar` decimal(10,2) DEFAULT '0.00',
  `registro` text,
  `excluido` tinyint(1) DEFAULT '0',
  `motivo` text,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `codigo_rendicion` varchar(3) DEFAULT NULL,
  `descripcion_codigo` varchar(200) DEFAULT NULL,
  `procesado` tinyint(1) DEFAULT '0',
  `fecha_proceso` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_liquidacion_socios` (`liquidacion_socio_id`),
  KEY `fk_liquidacion_socio_envio_id` (`liquidacion_socio_envio_id`),
  KEY `idx_identificador_debito` (`identificador_debito`),
  KEY `idx_excluido` (`excluido`),
  KEY `FK_LIQUIDACION_SOCIO_SOCIO_ID` (`socio_id`),
  CONSTRAINT `fk_liquidacion_socio_envio_id` FOREIGN KEY (`liquidacion_socio_envio_id`) REFERENCES `liquidacion_socio_envios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=298167 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `liquidacion_socio_envios`
--

DROP TABLE IF EXISTS `liquidacion_socio_envios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liquidacion_socio_envios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asincrono_id` int(11) DEFAULT '0',
  `bloqueado` tinyint(1) DEFAULT '0',
  `liquidacion_id` int(11) DEFAULT NULL,
  `banco_id` varchar(5) DEFAULT NULL,
  `banco_nombre` varchar(100) DEFAULT NULL,
  `fecha_debito` date DEFAULT NULL,
  `cantidad_registros` int(11) DEFAULT '0',
  `status` varchar(5) DEFAULT NULL,
  `importe_debito` decimal(10,2) DEFAULT '0.00',
  `observaciones` text,
  `longitud_registro` int(11) DEFAULT '0',
  `uuid` varchar(20) DEFAULT NULL,
  `lote` longtext,
  `archivo` varchar(100) DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_uuid` (`uuid`),
  KEY `fk_liquidacion_id` (`liquidacion_id`),
  CONSTRAINT `fk_liquidacion_id` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidaciones` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2232 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `liquidacion_socio_rendiciones`
--

DROP TABLE IF EXISTS `liquidacion_socio_rendiciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liquidacion_socio_rendiciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `periodo` varchar(6) DEFAULT NULL,
  `liquidacion_id` int(11) DEFAULT '0',
  `liquidacion_intercambio_id` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `codigo_organismo` varchar(12) DEFAULT NULL,
  `nro_ley` varchar(2) DEFAULT NULL,
  `tipo` varchar(1) DEFAULT NULL,
  `nro_beneficio` varchar(20) DEFAULT NULL,
  `sub_beneficio` varchar(2) DEFAULT NULL,
  `codigo_dto` varchar(10) DEFAULT '0',
  `sub_codigo` varchar(1) DEFAULT '0',
  `porcentaje` decimal(10,2) DEFAULT '100.00',
  `banco_id` varchar(5) DEFAULT NULL,
  `sucursal` varchar(5) DEFAULT NULL,
  `tipo_cta_bco` varchar(4) DEFAULT NULL,
  `nro_cta_bco` varchar(50) DEFAULT NULL,
  `cbu` varchar(23) DEFAULT NULL,
  `documento` varchar(11) DEFAULT NULL,
  `banco_intercambio` varchar(5) DEFAULT NULL,
  `importe_debitado` decimal(10,2) DEFAULT '0.00',
  `status` varchar(10) DEFAULT NULL,
  `indica_pago` tinyint(1) DEFAULT '0',
  `fecha_debito` date DEFAULT NULL,
  `registro` varchar(200) DEFAULT NULL,
  `orden_descuento_cobro_id` int(11) DEFAULT '0',
  `orden_descuento_id` int(11) DEFAULT '0',
  `nro_cuota` int(11) DEFAULT '0',
  `proveedor_id` int(11) DEFAULT '0',
  `saldo_operacion_informado` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `IDX_1` (`liquidacion_id`,`documento`),
  KEY `idx_4` (`liquidacion_id`),
  KEY `idx_5` (`liquidacion_id`,`socio_id`,`banco_id`,`documento`,`status`),
  KEY `idx_2` (`liquidacion_id`,`socio_id`,`indica_pago`),
  KEY `idx_3` (`socio_id`,`periodo`,`indica_pago`),
  KEY `idx_6` (`banco_intercambio`,`status`),
  KEY `idx_7` (`socio_id`),
  KEY `idx_8` (`liquidacion_intercambio_id`),
  KEY `idx_orden_descuento_id` (`orden_descuento_id`),
  KEY `idx_9` (`cbu`,`banco_intercambio`)
) ENGINE=InnoDB AUTO_INCREMENT=246116 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `liquidacion_socio_scores`
--

DROP TABLE IF EXISTS `liquidacion_socio_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liquidacion_socio_scores` (
  `liquidacion_id` int(11) NOT NULL,
  `socio_id` int(11) NOT NULL,
  `13` decimal(10,2) NOT NULL DEFAULT '0.00',
  `12` decimal(10,2) NOT NULL DEFAULT '0.00',
  `09` decimal(10,2) NOT NULL DEFAULT '0.00',
  `06` decimal(10,2) NOT NULL DEFAULT '0.00',
  `03` decimal(10,2) NOT NULL DEFAULT '0.00',
  `00` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cargos_adicionales` decimal(10,2) NOT NULL DEFAULT '0.00',
  `saldo_actual` decimal(10,2) DEFAULT NULL,
  `riesgo` int(11) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`liquidacion_id`,`socio_id`),
  KEY `fk_liquidacion_socio_scores_2_idx` (`socio_id`),
  CONSTRAINT `fk_liquidacion_socio_scores_1` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidaciones` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_liquidacion_socio_scores_2` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `liquidacion_socios`
--

DROP TABLE IF EXISTS `liquidacion_socios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liquidacion_socios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `liquidacion_id` int(11) DEFAULT '0',
  `registro` int(11) DEFAULT '1',
  `criterio_deuda` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `persona_beneficio_id` int(11) DEFAULT '0',
  `codigo_organismo` varchar(12) DEFAULT NULL,
  `nro_ley` varchar(2) DEFAULT NULL,
  `tipo` varchar(1) DEFAULT NULL,
  `nro_beneficio` varchar(20) DEFAULT NULL,
  `sub_beneficio` varchar(2) DEFAULT NULL,
  `codigo_dto` varchar(10) DEFAULT '0',
  `sub_codigo` varchar(1) DEFAULT '0',
  `porcentaje` decimal(10,2) DEFAULT '100.00',
  `banco_id` varchar(5) DEFAULT NULL,
  `sucursal` varchar(5) DEFAULT NULL,
  `tipo_cta_bco` varchar(4) DEFAULT NULL,
  `nro_cta_bco` varchar(50) DEFAULT NULL,
  `cbu` varchar(23) DEFAULT NULL,
  `error_cbu` tinyint(1) DEFAULT '0',
  `tipo_documento` varchar(12) DEFAULT NULL,
  `documento` varchar(11) DEFAULT NULL,
  `apenom` varchar(100) DEFAULT NULL,
  `persona_id` int(11) DEFAULT '0',
  `cuit_cuil` varchar(11) DEFAULT NULL,
  `codigo_empresa` varchar(12) DEFAULT NULL,
  `codigo_reparticion` varchar(11) DEFAULT NULL,
  `turno_pago` varchar(12) DEFAULT NULL,
  `alta` tinyint(1) DEFAULT '0',
  `banco_intercambio` varchar(5) DEFAULT NULL,
  `periodo` tinyint(1) DEFAULT '0',
  `ultima_calificacion` varchar(12) DEFAULT NULL,
  `importe_dto` decimal(10,2) DEFAULT '0.00',
  `importe_adebitar` decimal(10,2) DEFAULT '0.00',
  `importe_debitado` decimal(10,2) DEFAULT '0.00',
  `importe_imputado` decimal(10,2) DEFAULT '0.00',
  `importe_reintegro` decimal(10,2) DEFAULT '0.00',
  `status` varchar(10) DEFAULT NULL,
  `indica_pago` tinyint(1) DEFAULT '0',
  `fecha_pago` date DEFAULT NULL,
  `liquidacion_intercambio_id` int(11) DEFAULT '0',
  `imputada` tinyint(1) DEFAULT '0',
  `orden_descuento_cobro_id` int(11) DEFAULT '0',
  `socio_calificacion_id` int(11) DEFAULT '0',
  `socio_reintegro_id` int(11) DEFAULT '0',
  `intercambio` text,
  `fecha_debito` date DEFAULT NULL,
  `diskette` tinyint(1) DEFAULT '1',
  `formula_criterio_deuda` text,
  `detalla` tinyint(1) DEFAULT '0',
  `fecha_otorgamiento` date DEFAULT NULL,
  `importe_total` decimal(10,2) DEFAULT '0.00',
  `cuotas` int(11) DEFAULT '0',
  `importe_cuota` decimal(10,2) DEFAULT '0.00',
  `importe_deuda` decimal(10,2) DEFAULT '0.00',
  `importe_deuda_vencida` decimal(10,2) DEFAULT '0.00',
  `importe_deuda_no_vencida` decimal(10,2) DEFAULT '0.00',
  `orden_descuento_id` int(11) DEFAULT '0',
  `error_intercambio` text,
  PRIMARY KEY (`id`),
  KEY `FK_liquidacion_socios_liquidacion` (`liquidacion_id`),
  KEY `FK_liquidacion_socios_socios` (`socio_id`),
  KEY `FK_liquidacion_socios_beneficio` (`persona_beneficio_id`),
  KEY `idx_documento` (`documento`),
  KEY `idx_apenom` (`apenom`),
  KEY `idx_producto_beneficio_socio` (`liquidacion_id`,`socio_id`,`persona_beneficio_id`),
  KEY `idx_debito` (`socio_id`,`codigo_organismo`,`nro_ley`,`tipo`,`nro_beneficio`,`sub_beneficio`,`codigo_dto`,`sub_codigo`,`banco_id`,`cbu`,`tipo_documento`,`documento`,`status`),
  KEY `idx_liquidacion_intercambio` (`liquidacion_intercambio_id`),
  KEY `idx_intercambio` (`socio_id`,`liquidacion_intercambio_id`),
  KEY `idx_liquidacion_id_status` (`liquidacion_id`,`status`,`liquidacion_intercambio_id`),
  KEY `idx_orden_cobro` (`liquidacion_id`,`orden_descuento_cobro_id`) USING BTREE,
  KEY `idx_pagos_no_imputados` (`liquidacion_id`,`socio_id`,`importe_debitado`,`liquidacion_intercambio_id`,`imputada`) USING BTREE,
  KEY `idx_reintegro` (`liquidacion_id`,`importe_reintegro`,`liquidacion_intercambio_id`),
  KEY `idx_liquidacion_socio_alta` (`liquidacion_id`,`socio_id`,`alta`),
  KEY `idx_liquidacion_alta` (`liquidacion_id`,`alta`,`indica_pago`),
  KEY `idx_empresa` (`codigo_empresa`,`codigo_reparticion`),
  KEY `idx_orden_descuento_id` (`orden_descuento_id`),
  KEY `idx_liquidacion_beneficio_turno` (`liquidacion_id`,`persona_beneficio_id`,`turno_pago`),
  KEY `idx_liquidacion_diskette` (`liquidacion_id`,`diskette`),
  CONSTRAINT `FK_liquidacion_socios_beneficio` FOREIGN KEY (`persona_beneficio_id`) REFERENCES `persona_beneficios` (`id`),
  CONSTRAINT `FK_liquidacion_socios_liquidacion` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidaciones` (`id`),
  CONSTRAINT `FK_liquidacion_socios_socios` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=911680 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `liquidacion_turnos`
--

DROP TABLE IF EXISTS `liquidacion_turnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liquidacion_turnos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `turno` varchar(12) DEFAULT NULL,
  `codigo_empresa` varchar(12) DEFAULT NULL,
  `codigo_reparticion` varchar(15) DEFAULT ' ',
  `descripcion` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reparticion` (`codigo_reparticion`),
  KEY `idx_turno` (`turno`)
) ENGINE=InnoDB AUTO_INCREMENT=1306 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `liquidaciones`
--

DROP TABLE IF EXISTS `liquidaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liquidaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT '0',
  `codigo_organismo` varchar(12) DEFAULT NULL,
  `sobre_pre_imputacion` tinyint(1) DEFAULT '0',
  `bloqueada` tinyint(1) DEFAULT '0',
  `cerrada` tinyint(1) DEFAULT '0',
  `en_proceso` tinyint(1) DEFAULT '1',
  `archivos_procesados` tinyint(1) DEFAULT '0',
  `imputada` tinyint(1) DEFAULT '0',
  `facturada` tinyint(1) DEFAULT '0',
  `periodo` varchar(6) DEFAULT NULL,
  `cuota_social_vencida` decimal(10,2) DEFAULT '0.00',
  `cuota_social_periodo` decimal(10,2) DEFAULT '0.00',
  `deuda_vencida` decimal(10,2) DEFAULT '0.00',
  `deuda_periodo` decimal(10,2) DEFAULT '0.00',
  `total_vencido` decimal(10,2) DEFAULT '0.00',
  `total_periodo` decimal(10,2) DEFAULT '0.00',
  `total` decimal(10,2) DEFAULT '0.00',
  `altas` int(11) DEFAULT '0',
  `registros_enviados` int(11) DEFAULT '0',
  `registros_recibidos` int(11) DEFAULT '0',
  `importe_recibido` decimal(10,2) DEFAULT '0.00',
  `importe_cobrado` decimal(10,2) DEFAULT '0.00',
  `importe_no_cobrado` decimal(10,2) DEFAULT '0.00',
  `importe_imputado` decimal(10,2) DEFAULT '0.00',
  `importe_reintegro` decimal(10,2) DEFAULT '0.00',
  `fecha_imputacion` date DEFAULT NULL,
  `recibo_id` int(11) DEFAULT '0',
  `asincrono_id` int(11) DEFAULT '0',
  `nro_recibo` varchar(50) DEFAULT NULL,
  `scoring` tinyint(4) NOT NULL DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_periodo` (`periodo`,`id`,`codigo_organismo`)
) ENGINE=InnoDB AUTO_INCREMENT=419 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `localidades`
--

DROP TABLE IF EXISTS `localidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `localidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cp` varchar(4) DEFAULT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  `provincia_id` int(11) DEFAULT '0',
  `letra_provincia` varchar(1) DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `idr` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `NewIndex1` (`cp`),
  KEY `NewIndex2` (`nombre`),
  KEY `NewIndex3` (`provincia_id`),
  KEY `NewIndex4` (`letra_provincia`),
  KEY `NewIndex5` (`idr`)
) ENGINE=InnoDB AUTO_INCREMENT=11850 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_adicional_pendientes`
--

DROP TABLE IF EXISTS `mutual_adicional_pendientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_adicional_pendientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `liquidacion_id` int(11) DEFAULT '0',
  `periodo` varchar(6) DEFAULT NULL,
  `socio_id` int(11) DEFAULT '0',
  `codigo_organismo` varchar(12) DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT '0',
  `tipo` varchar(1) DEFAULT 'P',
  `deuda_calcula` int(11) DEFAULT '1',
  `valor` decimal(10,2) DEFAULT '0.00',
  `tipo_cuota` varchar(12) DEFAULT NULL,
  `total_deuda` decimal(10,2) DEFAULT '0.00',
  `importe` decimal(10,2) DEFAULT '0.00',
  `procesado` tinyint(1) DEFAULT '0',
  `orden_descuento_id` int(11) DEFAULT NULL,
  `persona_beneficio_id` int(11) DEFAULT NULL,
  `orden_descuento_cuota_id` int(11) DEFAULT NULL,
  `observaciones` text,
  PRIMARY KEY (`id`),
  KEY `idx_socio` (`socio_id`),
  KEY `idx_proveedor` (`proveedor_id`),
  KEY `idx_liquidacion` (`liquidacion_id`),
  KEY `idx_orden_descuento` (`orden_descuento_id`),
  KEY `idx_socio_periodo` (`periodo`,`socio_id`),
  KEY `FK_adicional_beneficio_id` (`persona_beneficio_id`),
  KEY `fk_adicional_cuota_id` (`orden_descuento_cuota_id`),
  CONSTRAINT `FK_adicional_beneficio_id` FOREIGN KEY (`persona_beneficio_id`) REFERENCES `persona_beneficios` (`id`),
  CONSTRAINT `fk_adicional_cuota_id` FOREIGN KEY (`orden_descuento_cuota_id`) REFERENCES `orden_descuento_cuotas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_adicional_orden_dto_id` FOREIGN KEY (`orden_descuento_id`) REFERENCES `orden_descuentos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57082 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_adicionales`
--

DROP TABLE IF EXISTS `mutual_adicionales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_adicionales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_organismo` varchar(12) DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT '0',
  `imputar_proveedor_id` int(11) DEFAULT '0',
  `tipo` varchar(1) DEFAULT 'P',
  `valor` decimal(10,2) DEFAULT '0.00',
  `devengado_previo` tinyint(1) DEFAULT '0',
  `periodo_desde` varchar(6) DEFAULT NULL,
  `periodo_hasta` varchar(6) DEFAULT NULL,
  `deuda_calcula` int(11) DEFAULT '1',
  `tipo_cuota` varchar(12) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_organismo_proveedor` (`codigo_organismo`,`proveedor_id`),
  KEY `idx_periodo` (`periodo_desde`,`periodo_hasta`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_asiento_renglones`
--

DROP TABLE IF EXISTS `mutual_asiento_renglones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_asiento_renglones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mutual_asiento_id` int(11) DEFAULT '0',
  `mutual_proceso_asiento_id` int(11) DEFAULT '0',
  `fecha` date DEFAULT NULL,
  `co_plan_cuenta_id` int(11) DEFAULT NULL,
  `cuenta` varchar(20) DEFAULT '',
  `descripcion` varchar(100) DEFAULT '',
  `referencia` varchar(100) DEFAULT '',
  `debe` decimal(15,2) DEFAULT NULL,
  `haber` decimal(15,2) DEFAULT NULL,
  `error` int(1) DEFAULT '0',
  `error_descripcion` varchar(100) DEFAULT '',
  `modulo` varchar(12) DEFAULT '',
  `tipo_asiento` int(4) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1207339 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_asientos`
--

DROP TABLE IF EXISTS `mutual_asientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_asientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mutual_proceso_asiento_id` int(11) DEFAULT '0',
  `co_asiento_id` int(11) DEFAULT '0',
  `nro_asiento` int(11) DEFAULT '0',
  `co_ejercicio_id` int(11) DEFAULT '0',
  `fecha` date DEFAULT NULL,
  `tipo_documento` varchar(3) DEFAULT NULL,
  `nro_documento` varchar(20) DEFAULT '',
  `referencia` varchar(100) DEFAULT '',
  `debe` decimal(15,2) DEFAULT '0.00',
  `haber` decimal(15,2) DEFAULT '0.00',
  `error` int(1) DEFAULT '0',
  `modulo` varchar(12) DEFAULT '',
  `tipo_asiento` int(4) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=457736 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_cuenta_asientos`
--

DROP TABLE IF EXISTS `mutual_cuenta_asientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_cuenta_asientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_orden_dto` varchar(5) NOT NULL DEFAULT '',
  `tipo_producto` varchar(12) NOT NULL DEFAULT '',
  `tipo_cuota` varchar(12) NOT NULL DEFAULT '',
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `mutual_tipo_asiento_id` int(11) DEFAULT '0',
  `instancia` varchar(5) DEFAULT 'COBRO',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_ORDEN_PROD_TCUO` (`tipo_orden_dto`,`tipo_producto`,`tipo_cuota`)
) ENGINE=InnoDB AUTO_INCREMENT=262 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_proceso_asientos`
--

DROP TABLE IF EXISTS `mutual_proceso_asientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_proceso_asientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `co_ejercicio_id` int(11) DEFAULT '0',
  `fecha_desde` date DEFAULT NULL,
  `fecha_hasta` date DEFAULT NULL,
  `cerrado` int(1) DEFAULT '0',
  `agrupar` int(1) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_producto_anexos`
--

DROP TABLE IF EXISTS `mutual_producto_anexos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_producto_anexos` (
  `mutual_producto_id` int(11) NOT NULL,
  `codigo_anexo` varchar(12) NOT NULL,
  PRIMARY KEY (`mutual_producto_id`,`codigo_anexo`),
  KEY `fk_mutual_producto_anexos_mutual_producto_id_idx` (`mutual_producto_id`),
  CONSTRAINT `fk_mutual_producto_anexos_mutual_producto_id_idx` FOREIGN KEY (`mutual_producto_id`) REFERENCES `mutual_productos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_producto_solicitud_cancelaciones`
--

DROP TABLE IF EXISTS `mutual_producto_solicitud_cancelaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_producto_solicitud_cancelaciones` (
  `mutual_producto_solicitud_id` int(11) NOT NULL,
  `cancelacion_orden_id` int(11) NOT NULL,
  PRIMARY KEY (`mutual_producto_solicitud_id`,`cancelacion_orden_id`),
  KEY `FK_SOLICITUD_CANCELACIONES_CANCELACION` (`cancelacion_orden_id`),
  CONSTRAINT `FK_SOLICITUD_CANCELACIONES_CANCELACION` FOREIGN KEY (`cancelacion_orden_id`) REFERENCES `cancelacion_ordenes` (`id`),
  CONSTRAINT `FK_SOLICITUD_CANCELACIONES_SOLICITUD` FOREIGN KEY (`mutual_producto_solicitud_id`) REFERENCES `mutual_producto_solicitudes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_producto_solicitud_documentos`
--

DROP TABLE IF EXISTS `mutual_producto_solicitud_documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_producto_solicitud_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mutual_producto_solicitud_id` int(11) DEFAULT NULL,
  `file_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `file_type` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `file_data` longblob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_documentos_mutual_producto_solicitud` (`mutual_producto_solicitud_id`),
  CONSTRAINT `FK_documentos_mutual_producto_solicitud_id` FOREIGN KEY (`mutual_producto_solicitud_id`) REFERENCES `mutual_producto_solicitudes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3694 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_producto_solicitud_estados`
--

DROP TABLE IF EXISTS `mutual_producto_solicitud_estados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_producto_solicitud_estados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mutual_producto_solicitud_id` int(11) NOT NULL,
  `estado` varchar(12) NOT NULL,
  `observaciones` text,
  `user_created` varchar(45) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_mutual_producto_solicitud_estados_1_idx` (`mutual_producto_solicitud_id`),
  KEY `fk_mutual_producto_solicitud_estados_2_idx` (`estado`),
  CONSTRAINT `fk_mutual_producto_solicitud_estados_1` FOREIGN KEY (`mutual_producto_solicitud_id`) REFERENCES `mutual_producto_solicitudes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=43251 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_producto_solicitud_instruccion_pagos`
--

DROP TABLE IF EXISTS `mutual_producto_solicitud_instruccion_pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_producto_solicitud_instruccion_pagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mutual_producto_solicitud_id` int(11) NOT NULL DEFAULT '0',
  `a_la_orden_de` varchar(255) NOT NULL,
  `concepto` text NOT NULL,
  `importe` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `FK_IPAGO_MUTUAL_PRODUCTO_SOLICITUD` (`mutual_producto_solicitud_id`),
  CONSTRAINT `FK_IPAGO_MUTUAL_PRODUCTO_SOLICITUD` FOREIGN KEY (`mutual_producto_solicitud_id`) REFERENCES `mutual_producto_solicitudes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10918 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_producto_solicitud_pagos`
--

DROP TABLE IF EXISTS `mutual_producto_solicitud_pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_producto_solicitud_pagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mutual_producto_solicitud_id` int(11) DEFAULT '0',
  `forma_pago` varchar(12) DEFAULT NULL,
  `banco_id` varchar(5) DEFAULT NULL,
  `nro_comprobante` varchar(50) DEFAULT NULL,
  `importe` decimal(10,2) DEFAULT '0.00',
  `observaciones` text,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_mutual_producto_solicitud_pagos_solicitud_id` (`mutual_producto_solicitud_id`),
  CONSTRAINT `FK_mutual_producto_solicitud_pagos_solicitud_id` FOREIGN KEY (`mutual_producto_solicitud_id`) REFERENCES `mutual_producto_solicitudes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5704 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_producto_solicitud_preproceso`
--

DROP TABLE IF EXISTS `mutual_producto_solicitud_preproceso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_producto_solicitud_preproceso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_identificador` varchar(100) NOT NULL,
  `tipo` int(11) NOT NULL,
  `cancelacion_id` int(11) DEFAULT NULL,
  `a_la_orden_de` varchar(255) DEFAULT NULL,
  `concepto` text,
  `importe` decimal(10,2) DEFAULT NULL,
  `file_name` varchar(100) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_data` longblob,
  PRIMARY KEY (`id`,`uuid_identificador`,`tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_producto_solicitudes`
--

DROP TABLE IF EXISTS `mutual_producto_solicitudes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_producto_solicitudes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aprobada` tinyint(1) DEFAULT '0',
  `anulada` tinyint(1) DEFAULT '0',
  `fecha` date NOT NULL,
  `fecha_pago` date DEFAULT NULL,
  `tipo_orden_dto` varchar(12) NOT NULL,
  `tipo_producto` varchar(12) NOT NULL,
  `proveedor_id` int(11) DEFAULT '0',
  `proveedor_plan_id` int(11) DEFAULT NULL,
  `mutual_producto_id` int(11) DEFAULT NULL,
  `estado` varchar(12) NOT NULL,
  `persona_id` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `persona_beneficio_id` int(11) DEFAULT '0',
  `orden_pago_id` int(11) DEFAULT '0',
  `importe_total` decimal(10,2) NOT NULL,
  `cuotas` int(11) NOT NULL DEFAULT '1',
  `importe_cuota` decimal(10,2) NOT NULL,
  `importe_solicitado` decimal(10,2) NOT NULL,
  `importe_percibido` decimal(10,2) NOT NULL,
  `tna` decimal(10,2) DEFAULT '0.00',
  `tnm` decimal(10,2) DEFAULT '0.00',
  `cft` decimal(10,2) DEFAULT '0.00',
  `capital_puro` decimal(10,2) DEFAULT '0.00',
  `interes` decimal(10,2) DEFAULT '0.00',
  `iva` decimal(10,2) DEFAULT '0.00',
  `iva_porc` decimal(10,2) DEFAULT '0.00',
  `gasto_admin_porc` decimal(10,2) DEFAULT '0.00',
  `gasto_admin` decimal(10,2) DEFAULT '0.00',
  `sellado_porc` decimal(10,2) DEFAULT '0.00',
  `sellado` decimal(10,2) DEFAULT '0.00',
  `ayuda_economica` tinyint(1) DEFAULT '0',
  `metodo_calculo` int(11) DEFAULT '1',
  `tem` decimal(10,2) DEFAULT '0.00',
  `periodo_ini` varchar(6) DEFAULT NULL,
  `periodicidad` varchar(1) DEFAULT '0',
  `primer_vto_socio` date DEFAULT NULL,
  `primer_vto_proveedor` date DEFAULT NULL,
  `observaciones` text,
  `permanente` tinyint(1) DEFAULT '0',
  `orden_descuento_id` int(11) DEFAULT NULL,
  `orden_descuento_seguro_id` int(11) DEFAULT NULL,
  `nro_referencia_proveedor` varchar(20) DEFAULT NULL,
  `sin_cargo` tinyint(1) DEFAULT '0',
  `prestamo` tinyint(1) DEFAULT '0',
  `aprobada_por` varchar(50) DEFAULT NULL,
  `aprobada_el` date DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `reasignar_proveedor_id` int(11) DEFAULT '0',
  `reasignar_proveedor_fecha` datetime DEFAULT NULL,
  `reasignar_proveedor_usuario` varchar(50) DEFAULT NULL,
  `vendedor_id` int(11) DEFAULT NULL,
  `vendedor_remito_id` int(11) DEFAULT NULL,
  `vendedor_notificar` tinyint(1) DEFAULT '0',
  `forma_pago` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_mutual_producto_solicitudes_socios` (`socio_id`),
  KEY `idx_periodo` (`periodo_ini`),
  KEY `idx_tipo` (`tipo_orden_dto`),
  KEY `idx_producto` (`mutual_producto_id`),
  KEY `idx_expediente` (`orden_descuento_id`),
  KEY `idx_nro_ref_proveedor` (`nro_referencia_proveedor`),
  KEY `idx_aprobada_anulada` (`aprobada`,`anulada`),
  KEY `fk_orden_dto_seguro_id_orden_dto` (`orden_descuento_seguro_id`),
  KEY `idx_proveedor_plan_id` (`proveedor_plan_id`),
  KEY `idx_reasignar_proveedor` (`reasignar_proveedor_id`),
  KEY `idx_vendedor_id` (`vendedor_id`),
  KEY `fk_solicitud_vendedor_remito_id` (`vendedor_remito_id`),
  KEY `FK_SOLICITUD_ESTADO` (`estado`),
  CONSTRAINT `fk_orden_dto_id_orden_dto` FOREIGN KEY (`orden_descuento_id`) REFERENCES `orden_descuentos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_orden_dto_seguro_id_orden_dto` FOREIGN KEY (`orden_descuento_seguro_id`) REFERENCES `orden_descuentos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_SOLICITUD_ESTADO` FOREIGN KEY (`estado`) REFERENCES `global_datos` (`id`),
  CONSTRAINT `FK_SOLICITUD_PROVEEDOR_PLAN` FOREIGN KEY (`proveedor_plan_id`) REFERENCES `proveedor_planes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_solicitud_vendedor_id` FOREIGN KEY (`vendedor_id`) REFERENCES `vendedores` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_solicitud_vendedor_remito_id` FOREIGN KEY (`vendedor_remito_id`) REFERENCES `vendedor_remitos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=26434 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_productos`
--

DROP TABLE IF EXISTS `mutual_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_orden_dto` varchar(5) DEFAULT NULL,
  `tipo_producto` varchar(12) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `proveedor_id` int(11) DEFAULT '0',
  `importe_fijo` decimal(10,2) DEFAULT '0.00',
  `cuota_social_diferenciada` decimal(10,2) DEFAULT '0.00',
  `mensual` tinyint(1) DEFAULT '0',
  `sin_cargo` tinyint(1) DEFAULT '0',
  `prestamo` tinyint(1) DEFAULT '0',
  `modelo_solicitud_codigo` varchar(12) NOT NULL DEFAULT 'MUTUIMPR0001',
  `idr` varchar(5) DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idu_proveedor_producto` (`tipo_producto`,`proveedor_id`,`sin_cargo`,`mensual`),
  KEY `FK_mutual_productos_proveedor` (`proveedor_id`),
  KEY `idx_mensual` (`mensual`),
  KEY `idx_tipo_producto` (`tipo_producto`),
  KEY `fk_mutual_productos_1_modelo_solicitud` (`modelo_solicitud_codigo`),
  CONSTRAINT `fk_mutual_productos_1_modelo_solicitud` FOREIGN KEY (`modelo_solicitud_codigo`) REFERENCES `global_datos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_mutual_productos_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `FK_mutual_producto_tipo_producto` FOREIGN KEY (`tipo_producto`) REFERENCES `global_datos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=626 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_servicio_solicitud_adicionales`
--

DROP TABLE IF EXISTS `mutual_servicio_solicitud_adicionales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_servicio_solicitud_adicionales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mutual_servicio_solicitud_id` int(11) DEFAULT '0',
  `socio_adicional_id` int(11) DEFAULT '0',
  `fecha_emision_alta` date DEFAULT NULL,
  `fecha_emision_baja` date DEFAULT NULL,
  `fecha_alta` date DEFAULT NULL,
  `fecha_baja` date DEFAULT NULL,
  `periodo_desde` varchar(6) DEFAULT NULL,
  `periodo_hasta` varchar(6) DEFAULT NULL,
  `orden_descuento_id` decimal(10,2) DEFAULT '0.00',
  `importe_mensual` decimal(10,2) DEFAULT '0.00',
  `mutual_servicio_valor_id` int(11) DEFAULT '0',
  `observaciones` text,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_mutual_servicio_solicitud_beneficiarios` (`mutual_servicio_solicitud_id`),
  KEY `FK_mutual_servicio_solicitud_beneficiarios_adicional` (`socio_adicional_id`),
  CONSTRAINT `FK_mutual_servicio_solicitud_adicionales` FOREIGN KEY (`mutual_servicio_solicitud_id`) REFERENCES `mutual_servicio_solicitudes` (`id`),
  CONSTRAINT `FK_mutual_servicio_solicitud_beneficiarios_adicional` FOREIGN KEY (`socio_adicional_id`) REFERENCES `socio_adicionales` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_servicio_solicitudes`
--

DROP TABLE IF EXISTS `mutual_servicio_solicitudes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_servicio_solicitudes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mutual_servicio_id` int(11) DEFAULT '0',
  `persona_id` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `persona_beneficio_id` int(11) DEFAULT '0',
  `fecha_emision` date DEFAULT NULL,
  `fecha_emision_baja` date DEFAULT NULL,
  `aprobada` tinyint(1) DEFAULT '0',
  `fecha_aprobacion` date DEFAULT NULL,
  `fecha_alta_servicio` date DEFAULT NULL,
  `fecha_baja_servicio` date DEFAULT NULL,
  `periodo_desde` varchar(6) DEFAULT NULL,
  `periodo_hasta` varchar(6) DEFAULT NULL,
  `orden_descuento_id` int(11) DEFAULT '0',
  `importe_mensual` decimal(10,2) DEFAULT '0.00',
  `importe_mensual_total` decimal(10,2) DEFAULT '0.00',
  `permanente` tinyint(1) DEFAULT '1',
  `cuotas` int(11) DEFAULT '0',
  `importe_cuota` decimal(10,2) DEFAULT '0.00',
  `mutual_servicio_valor_id` int(11) DEFAULT '0',
  `nro_referencia_proveedor` varchar(50) DEFAULT NULL,
  `observaciones` text,
  `aprobada_por` varchar(50) DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_mutual_servicio_solicitudes_socio` (`socio_id`),
  KEY `FK_mutual_servicio_solicitudes_beneficio` (`persona_beneficio_id`),
  KEY `FK_mutual_servicio_solicitudes_persona` (`persona_id`),
  KEY `FK_mutual_servicio_solicitudes_mutual_servicio` (`mutual_servicio_id`),
  KEY `idx_orden_descuento_id` (`orden_descuento_id`),
  CONSTRAINT `FK_mutual_servicio_solicitudes_mutual_servicio` FOREIGN KEY (`mutual_servicio_id`) REFERENCES `mutual_servicios` (`id`),
  CONSTRAINT `FK_mutual_servicio_solicitudes_persona` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`),
  CONSTRAINT `FK_mutual_servicio_solicitudes_socio` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_servicio_valores`
--

DROP TABLE IF EXISTS `mutual_servicio_valores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_servicio_valores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mutual_servicio_id` int(11) DEFAULT '0',
  `codigo_organismo` varchar(12) DEFAULT NULL,
  `importe_titular` decimal(10,2) DEFAULT '0.00',
  `importe_adicional` decimal(10,2) DEFAULT '0.00',
  `costo_titular` decimal(10,2) DEFAULT '0.00',
  `costo_adicional` decimal(10,2) DEFAULT '0.00',
  `periodo_vigencia` varchar(6) DEFAULT NULL,
  `fecha_vigencia` date DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_mutual_servicio_valores_servicios` (`mutual_servicio_id`),
  CONSTRAINT `FK_mutual_servicio_valores_servicios` FOREIGN KEY (`mutual_servicio_id`) REFERENCES `mutual_servicios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_servicios`
--

DROP TABLE IF EXISTS `mutual_servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_servicios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_orden_dto` varchar(5) DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT '0',
  `tipo_producto` varchar(12) DEFAULT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `dia_corte` int(11) DEFAULT '25',
  `meses_antes_dia_corte` int(11) DEFAULT '1',
  `meses_despues_dia_corte` int(11) DEFAULT '1',
  `dia_alta` int(11) DEFAULT '1',
  `call_center` tinyint(1) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_proveedor` (`proveedor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_temporal_asiento_renglones`
--

DROP TABLE IF EXISTS `mutual_temporal_asiento_renglones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_temporal_asiento_renglones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mutual_asiento_id` int(11) DEFAULT '0',
  `fecha` date DEFAULT NULL,
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `cuenta` varchar(20) DEFAULT '',
  `descripcion` varchar(100) DEFAULT '',
  `importe` decimal(15,2) DEFAULT '0.00',
  `referencia` varchar(100) DEFAULT '',
  `error` int(1) DEFAULT '0',
  `error_descripcion` varchar(100) DEFAULT '',
  `modulo` varchar(12) DEFAULT '',
  `tipo_asiento` int(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1207339 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_tipo_asiento_renglones`
--

DROP TABLE IF EXISTS `mutual_tipo_asiento_renglones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_tipo_asiento_renglones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variable` varchar(5) DEFAULT NULL,
  `mutual_tipo_asiento_id` int(11) DEFAULT '0',
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `debe_haber` varchar(1) DEFAULT 'D',
  `tipo_asiento` varchar(2) DEFAULT 'GR',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_tipo_asientos`
--

DROP TABLE IF EXISTS `mutual_tipo_asientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_tipo_asientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `concepto` varchar(100) DEFAULT NULL,
  `tipo_asiento` varchar(2) NOT NULL DEFAULT 'GR',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orden_caja_cobro_cuotas`
--

DROP TABLE IF EXISTS `orden_caja_cobro_cuotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orden_caja_cobro_cuotas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orden_caja_cobro_id` int(11) unsigned DEFAULT '0',
  `orden_descuento_cuota_id` int(11) DEFAULT '0',
  `importe` decimal(10,2) DEFAULT '0.00',
  `importe_abonado` decimal(10,2) DEFAULT '0.00',
  `saldo_cuota` decimal(10,2) DEFAULT '0.00',
  `pendiente_imputacion` tinyint(1) DEFAULT '0',
  `orden_descuento_id` int(11) DEFAULT '0',
  `tipo_cuota` varchar(12) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_orden_caja_cobro_cuotas_cuota` (`orden_descuento_cuota_id`),
  KEY `FK_orden_caja_cobro_cuotas_caja` (`orden_caja_cobro_id`),
  CONSTRAINT `FK_orden_caja_cobro_cuotas_caja` FOREIGN KEY (`orden_caja_cobro_id`) REFERENCES `orden_caja_cobros` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16463 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orden_caja_cobros`
--

DROP TABLE IF EXISTS `orden_caja_cobros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orden_caja_cobros` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `estado` varchar(1) DEFAULT 'E',
  `fecha_vto` date DEFAULT NULL,
  `socio_id` int(11) DEFAULT '0',
  `importe` decimal(10,2) DEFAULT '0.00',
  `importe_cobrado` decimal(10,2) DEFAULT '0.00',
  `tipo_imputacion` int(11) DEFAULT '0',
  `barcode` varchar(40) DEFAULT NULL,
  `orden_cancelacion_id` int(11) DEFAULT '0',
  `orden_descuento_cobro_id` int(11) DEFAULT '0',
  `orden_pago_id` int(11) DEFAULT '0',
  `banco_cuenta_movimiento_id` int(11) DEFAULT '0',
  `recibo_id` int(11) DEFAULT '0',
  `importe_contado` decimal(10,2) DEFAULT '0.00',
  `importe_orden_pago` decimal(10,2) DEFAULT '0.00',
  `proveedor_factura_id` int(11) DEFAULT '0',
  `importe_factura` decimal(10,2) DEFAULT '0.00',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_barcode` (`barcode`),
  KEY `idx_vto` (`fecha_vto`),
  KEY `FK_orden_caja_cobros_socio` (`socio_id`),
  KEY `FK_orden_caja_cobros` (`orden_descuento_cobro_id`),
  CONSTRAINT `FK_orden_caja_cobros_socio` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3443 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orden_descuento_cobro_cuotas`
--

DROP TABLE IF EXISTS `orden_descuento_cobro_cuotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orden_descuento_cobro_cuotas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `periodo_cobro` varchar(6) DEFAULT NULL,
  `orden_descuento_cobro_id` int(11) DEFAULT '0',
  `orden_descuento_cuota_id` int(11) DEFAULT '0',
  `proveedor_id` int(11) DEFAULT '0',
  `importe` decimal(10,2) DEFAULT '0.00',
  `proveedor_liquidacion_id` int(11) DEFAULT '0',
  `reversado` tinyint(1) DEFAULT '0',
  `importe_reversado` decimal(10,2) DEFAULT '0.00',
  `periodo_proveedor_reverso` varchar(6) DEFAULT NULL,
  `fecha_reverso` date DEFAULT NULL,
  `usuario_reverso` varchar(50) DEFAULT NULL,
  `debito_reverso_id` int(11) DEFAULT '0',
  `banco_cuenta_movimiento_id` int(11) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `alicuota_comision_cobranza` decimal(10,3) DEFAULT '0.000',
  `comision_cobranza` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `idx_proveedor` (`proveedor_id`),
  KEY `FK_orden_descuento_cobro_cuotas_cobros` (`orden_descuento_cobro_id`),
  KEY `FK_orden_descuento_cobro_cuotas_cuota` (`orden_descuento_cuota_id`),
  KEY `idx_periodo_cobro` (`periodo_cobro`,`orden_descuento_cuota_id`),
  KEY `IDX_reverso` (`proveedor_id`,`reversado`,`periodo_proveedor_reverso`,`orden_descuento_cuota_id`),
  CONSTRAINT `FK_orden_descuento_cobro_cuotas_cobros` FOREIGN KEY (`orden_descuento_cobro_id`) REFERENCES `orden_descuento_cobros` (`id`),
  CONSTRAINT `FK_orden_descuento_cobro_cuotas_cuota` FOREIGN KEY (`orden_descuento_cuota_id`) REFERENCES `orden_descuento_cuotas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=399135 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orden_descuento_cobros`
--

DROP TABLE IF EXISTS `orden_descuento_cobros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orden_descuento_cobros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anulado` tinyint(1) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `tipo_cobro` varchar(12) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `periodo_cobro` varchar(6) DEFAULT NULL,
  `importe` decimal(10,2) DEFAULT '0.00',
  `recibo_id` int(11) DEFAULT '0',
  `nro_recibo` varchar(100) DEFAULT NULL,
  `cancelacion_orden_id` int(11) DEFAULT '0',
  `proveedor_origen_fondo_id` int(11) DEFAULT '0',
  `observaciones` text,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `cobro_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_fecha` (`fecha`),
  KEY `idx_recibo` (`nro_recibo`),
  KEY `idx_tipo_cobro` (`tipo_cobro`),
  KEY `idx_periodo_cobro` (`periodo_cobro`),
  KEY `idx_socio_id` (`socio_id`),
  KEY `idx_cancelacion_orden_id` (`cancelacion_orden_id`),
  KEY `idx_recibo_id` (`recibo_id`),
  CONSTRAINT `FK_TIPO_COBRO_GLOBAL_DATOS` FOREIGN KEY (`tipo_cobro`) REFERENCES `global_datos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=144630 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orden_descuento_cuota_items`
--

DROP TABLE IF EXISTS `orden_descuento_cuota_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orden_descuento_cuotas`
--

DROP TABLE IF EXISTS `orden_descuento_cuotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orden_descuento_cuotas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orden_descuento_id` int(11) NOT NULL DEFAULT '0',
  `socio_id` int(11) NOT NULL DEFAULT '0',
  `persona_beneficio_id` int(11) NOT NULL DEFAULT '0',
  `tipo_orden_dto` varchar(5) NOT NULL,
  `tipo_producto` varchar(12) NOT NULL,
  `tipo_cuota` varchar(12) NOT NULL,
  `periodo` varchar(6) NOT NULL,
  `periodo_origen` varchar(6) DEFAULT NULL,
  `periodicidad` varchar(1) DEFAULT '0',
  `estado` varchar(1) NOT NULL DEFAULT 'A',
  `situacion` varchar(12) NOT NULL,
  `vencimiento` date DEFAULT NULL,
  `vencimiento_proveedor` date DEFAULT NULL,
  `nro_cuota` int(11) NOT NULL DEFAULT '0',
  `importe` decimal(10,2) NOT NULL DEFAULT '0.00',
  `proveedor_id` int(11) NOT NULL DEFAULT '0',
  `nro_referencia_proveedor` varchar(20) DEFAULT '0',
  `nro_orden_referencia` int(11) DEFAULT '0',
  `codigo_comercio_referencia` varchar(10) DEFAULT '0',
  `observaciones` text,
  `cancelacion_orden_id` int(11) DEFAULT '0',
  `conciliada_proveedor` tinyint(1) DEFAULT '0',
  `fecha_conciliacion_proveedor` date DEFAULT NULL,
  `para_imputar` tinyint(1) DEFAULT '0',
  `liquidacion_id` int(11) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `od_detalle_id` int(11) DEFAULT '0',
  `importe_pagado` decimal(10,2) DEFAULT '0.00',
  `periodo_transferencia` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
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
  KEY `idx_persona_beneficio_id,idx_periodo` (`persona_beneficio_id`,`periodo`,`periodicidad`),
  KEY `aux_orcom` (`nro_orden_referencia`),
  CONSTRAINT `FK_orden_descuento_cuotas_od` FOREIGN KEY (`orden_descuento_id`) REFERENCES `orden_descuentos` (`id`),
  CONSTRAINT `FK_orden_descuento_cuotas_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `FK_orden_descuento_cuotas_socio` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=603489 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orden_descuentos`
--

DROP TABLE IF EXISTS `orden_descuentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orden_descuentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `tipo_orden_dto` varchar(5) NOT NULL,
  `numero` int(11) NOT NULL DEFAULT '0',
  `tipo_producto` varchar(12) NOT NULL,
  `proveedor_id` int(11) NOT NULL DEFAULT '0',
  `mutual_producto_id` int(11) DEFAULT '0',
  `socio_id` int(11) NOT NULL DEFAULT '0',
  `persona_beneficio_id` int(11) NOT NULL DEFAULT '0',
  `periodo_ini` varchar(6) NOT NULL,
  `periodicidad` varchar(1) DEFAULT '0',
  `periodo_hasta` varchar(6) DEFAULT NULL,
  `importe_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `importe_cuota` decimal(10,2) NOT NULL DEFAULT '0.00',
  `primer_vto_socio` date DEFAULT NULL,
  `primer_vto_proveedor` date DEFAULT NULL,
  `cuotas` int(11) NOT NULL DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  `permanente` tinyint(1) DEFAULT '0',
  `nro_referencia_proveedor` varchar(20) DEFAULT NULL,
  `nro_orden_referencia` int(11) DEFAULT '0',
  `codigo_comercio_referencia` varchar(10) DEFAULT '0',
  `comision_cobranza` decimal(10,3) DEFAULT '0.000',
  `comision_colocacion` decimal(10,3) DEFAULT '0.000',
  `reprogramada` tinyint(1) DEFAULT '0',
  `reasignada` tinyint(1) DEFAULT '0',
  `observaciones` text,
  `conciliada_proveedor` tinyint(1) DEFAULT '0',
  `fecha_conciliacion_proveedor` date DEFAULT NULL,
  `mora_tecnica` tinyint(1) DEFAULT '0',
  `productor_id` int(11) DEFAULT '0',
  `importe_solicitado` decimal(10,2) DEFAULT '0.00',
  `importe_capital` decimal(10,2) DEFAULT '0.00',
  `tna` decimal(10,2) DEFAULT '0.00',
  `tnm` decimal(10,2) DEFAULT '0.00',
  `cft` decimal(10,2) DEFAULT '0.00',
  `capital_puro` decimal(10,2) DEFAULT '0.00',
  `interes` decimal(10,2) DEFAULT '0.00',
  `iva` decimal(10,2) DEFAULT '0.00',
  `iva_porc` decimal(10,2) DEFAULT '0.00',
  `gasto_admin_porc` decimal(10,2) DEFAULT '0.00',
  `gasto_admin` decimal(10,2) DEFAULT '0.00',
  `sellado_porc` decimal(10,2) DEFAULT '0.00',
  `sellado` decimal(10,2) DEFAULT '0.00',
  `ayuda_economica` tinyint(1) DEFAULT '0',
  `metodo_calculo` int(11) DEFAULT '1',
  `tem` decimal(10,2) DEFAULT '0.00',
  `productor_ref` varchar(100) DEFAULT NULL,
  `alta_informada` tinyint(1) DEFAULT '0',
  `alta_informada_periodo` varchar(6) DEFAULT NULL,
  `baja_informada` tinyint(1) DEFAULT '0',
  `baja_informada_periodo` varchar(6) DEFAULT NULL,
  `nueva_orden_descuento_id` int(11) DEFAULT NULL,
  `anterior_orden_descuento_id` int(11) DEFAULT NULL,
  `motivo_novacion` varchar(250) DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `ztmp_odc_id` int(11) DEFAULT '0',
  `periodo_transferencia` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
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
  KEY `aux_orcom` (`nro_orden_referencia`),
  KEY `aux_proveedor` (`nro_referencia_proveedor`),
  KEY `idx_proveedor_periodo_ini` (`proveedor_id`,`periodo_ini`),
  KEY `idx_nueva_orden_descuento_id` (`nueva_orden_descuento_id`),
  KEY `idx_anterior_orden_descuento_id` (`anterior_orden_descuento_id`),
  CONSTRAINT `FK_orden_descuentos_beneficio` FOREIGN KEY (`persona_beneficio_id`) REFERENCES `persona_beneficios` (`id`),
  CONSTRAINT `FK_orden_descuentos_socio` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`),
  CONSTRAINT `FK_orden_descuento_proveedor_id` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `FK_orden_novada_anterior_orden_id` FOREIGN KEY (`anterior_orden_descuento_id`) REFERENCES `orden_descuentos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_orden_novada_nueva_orden_id` FOREIGN KEY (`nueva_orden_descuento_id`) REFERENCES `orden_descuentos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=65697 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orden_pago_detalles`
--

DROP TABLE IF EXISTS `orden_pago_detalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orden_pago_detalles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_id` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `id_persona` int(11) DEFAULT '0',
  `orden_pago_id` int(11) DEFAULT '0',
  `tipo_pago` varchar(2) DEFAULT NULL,
  `proveedor_factura_id` int(11) DEFAULT '0',
  `orden_pago_detalle_id` int(11) DEFAULT '0',
  `mutual_producto_solicitud_id` int(11) DEFAULT '0',
  `socio_reintegro_id` int(11) DEFAULT '0',
  `nro_solicitud` int(11) DEFAULT '0',
  `importe` decimal(15,2) DEFAULT '0.00',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_socio` (`socio_id`),
  KEY `idx_persona` (`id_persona`),
  KEY `idx_orden_pago` (`orden_pago_id`),
  KEY `idx_proveedor_factura` (`proveedor_factura_id`),
  KEY `idx_mutual_producto_solicitud` (`mutual_producto_solicitud_id`),
  KEY `idx_socio_reintegro` (`socio_reintegro_id`),
  KEY `idx_nro_solicitud` (`nro_solicitud`),
  KEY `idx_proveedor` (`proveedor_id`,`tipo_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=14089 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orden_pago_facturas`
--

DROP TABLE IF EXISTS `orden_pago_facturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orden_pago_facturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `proveedor_factura_id` int(11) DEFAULT '0',
  `orden_pago_id` int(11) DEFAULT '0',
  `proveedor_credito_id` int(11) DEFAULT '0',
  `orden_pago_detalle_id` int(11) DEFAULT '0',
  `importe` decimal(15,2) DEFAULT '0.00',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_proveedor` (`proveedor_id`),
  KEY `idx_socio_id` (`socio_id`),
  KEY `idx_proveedor_factura` (`proveedor_factura_id`),
  KEY `idx_orden_pago` (`orden_pago_id`),
  KEY `idx_proveedor_credito` (`proveedor_credito_id`),
  KEY `idx_orden_pago_detalle` (`orden_pago_detalle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12363 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orden_pago_formas`
--

DROP TABLE IF EXISTS `orden_pago_formas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orden_pago_formas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_id` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `orden_pago_id` int(11) DEFAULT '0',
  `forma_pago` varchar(2) DEFAULT NULL,
  `banco_cuenta_id` int(11) DEFAULT '0',
  `banco_cuenta_movimiento_id` int(11) DEFAULT '0',
  `numero_cuenta` varchar(20) DEFAULT NULL,
  `numero_operacion` varchar(20) DEFAULT NULL,
  `fecha_operacion` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `importe` decimal(15,2) DEFAULT '0.00',
  `concepto` varchar(100) DEFAULT NULL,
  `descripcion_pago` varchar(50) DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_proveedor` (`proveedor_id`),
  KEY `idx_socio` (`socio_id`),
  KEY `idx_orden_pago` (`orden_pago_id`),
  KEY `idx_banco_cuenta_movimiento` (`banco_cuenta_movimiento_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12938 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orden_pagos`
--

DROP TABLE IF EXISTS `orden_pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orden_pagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_documento` varchar(3) DEFAULT 'OPA',
  `sucursal` varchar(4) DEFAULT '0001',
  `nro_orden_pago` varchar(8) DEFAULT NULL,
  `fecha_pago` date DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `id_persona` int(11) DEFAULT '0',
  `importe` decimal(15,2) DEFAULT '0.00',
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `comentario` varchar(100) DEFAULT NULL,
  `anulado` tinyint(1) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `importe_detalle` decimal(15,2) DEFAULT '0.00',
  `importe_forma` decimal(15,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idu_tipo_suc_nro` (`tipo_documento`,`sucursal`,`nro_orden_pago`),
  KEY `idx_proveedor` (`proveedor_id`),
  KEY `idx_socio` (`socio_id`),
  KEY `idx_id_persona` (`id_persona`)
) ENGINE=InnoDB AUTO_INCREMENT=11839 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permisos`
--

DROP TABLE IF EXISTS `permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(50) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `order` int(11) DEFAULT '0',
  `main` tinyint(1) DEFAULT '0',
  `quick` tinyint(1) DEFAULT '0',
  `icon` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `parent` int(11) DEFAULT '0',
  `obs` mediumtext,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent` (`parent`),
  KEY `idx_order` (`order`),
  KEY `idx_main` (`main`),
  KEY `idx_quick` (`quick`),
  KEY `idx_url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `persona_beneficio_compartidos`
--

DROP TABLE IF EXISTS `persona_beneficio_compartidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `persona_beneficio_compartidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `persona_id` int(11) DEFAULT '0',
  `persona_beneficio_id` int(11) DEFAULT '0',
  `documento` varchar(8) DEFAULT NULL,
  `beneficiario` varchar(150) DEFAULT NULL,
  `codigo_beneficio` varchar(12) DEFAULT NULL,
  `nro_ley` varchar(2) DEFAULT NULL,
  `tipo` varchar(1) DEFAULT NULL,
  `nro_beneficio` varchar(11) DEFAULT NULL,
  `sub_beneficio` varchar(2) DEFAULT NULL,
  `cbu` varchar(23) DEFAULT NULL,
  `banco_id` varchar(5) DEFAULT NULL,
  `nro_sucursal` varchar(5) DEFAULT NULL,
  `tipo_cta_bco` varchar(4) DEFAULT NULL,
  `nro_cta_bco` varchar(50) DEFAULT NULL,
  `codigo_empresa` varchar(12) DEFAULT NULL,
  `codigo_reparticion` varchar(11) DEFAULT NULL,
  `turno_pago` varchar(12) DEFAULT NULL,
  `porcentaje` decimal(10,2) DEFAULT '0.00',
  `principal` tinyint(1) DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_persona_beneficio_compartidos_persona_id` (`persona_id`),
  KEY `FK_persona_beneficio_compartidos_beneficio_id` (`persona_beneficio_id`),
  CONSTRAINT `FK_persona_beneficio_compartidos_beneficio_id` FOREIGN KEY (`persona_beneficio_id`) REFERENCES `persona_beneficios` (`id`),
  CONSTRAINT `FK_persona_beneficio_compartidos_persona_id` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `persona_beneficios`
--

DROP TABLE IF EXISTS `persona_beneficios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `persona_beneficios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `persona_id` int(11) DEFAULT '0',
  `codigo_beneficio` varchar(12) DEFAULT NULL,
  `nro_ley` varchar(50) DEFAULT NULL,
  `tipo` varchar(1) DEFAULT NULL,
  `nro_beneficio` varchar(50) DEFAULT NULL,
  `sub_beneficio` varchar(2) DEFAULT NULL,
  `nro_legajo` varchar(50) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `codigo_reparticion` varchar(11) DEFAULT NULL,
  `turno_pago` varchar(12) DEFAULT NULL,
  `cbu` varchar(23) DEFAULT NULL,
  `banco_id` varchar(5) DEFAULT NULL,
  `sucursal_id` int(11) DEFAULT '0',
  `nro_sucursal` varchar(5) DEFAULT NULL,
  `tipo_cta_bco` varchar(4) DEFAULT NULL,
  `nro_cta_bco` varchar(50) DEFAULT NULL,
  `codigo_empresa` varchar(12) DEFAULT NULL,
  `principal` tinyint(1) DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  `porcentaje` decimal(10,2) DEFAULT '100.00',
  `idr` int(11) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `accion` varchar(1) DEFAULT NULL,
  `codigo_baja` varchar(12) DEFAULT NULL,
  `dupli` tinyint(1) DEFAULT '0',
  `fecha_baja` date DEFAULT NULL,
  `observaciones` text,
  `reasignado_id` int(11) DEFAULT '0',
  `periodo_mayor_envio` varchar(6) DEFAULT NULL,
  `periodo_mayor_debito` varchar(6) DEFAULT NULL,
  `importe_mayor_envio` decimal(10,2) DEFAULT '0.00',
  `importe_mayor_debito` decimal(10,2) DEFAULT '0.00',
  `calificacion_socio` varchar(12) DEFAULT NULL,
  `acuerdo_debito` decimal(10,2) DEFAULT '0.00',
  `importe_max_registro_cbu` decimal(10,2) DEFAULT '0.00',
  `nvo_nro_cta` varchar(50) DEFAULT NULL,
  `res_nro_cta` varchar(50) DEFAULT NULL,
  `codigo_beneficio_res` varchar(12) DEFAULT NULL,
  `codigo_empresa_res` varchar(12) DEFAULT NULL,
  `turno_pago_res` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_persona_beneficios` (`persona_id`),
  KEY `idx_codigo_beneficio` (`id`,`persona_id`,`codigo_beneficio`),
  KEY `idx_control_cbu` (`persona_id`,`cbu`,`banco_id`,`nro_sucursal`,`nro_cta_bco`),
  KEY `idx_organismo_empresa_turno` (`codigo_beneficio`,`turno_pago`,`codigo_empresa`),
  KEY `FK_persona_beneficio_empresa_global_dato` (`codigo_empresa`),
  KEY `FK_persona_beneficio_turno_pago_liquidacion_turnos` (`turno_pago`),
  KEY `FK_persona_beneficio_banco_id` (`banco_id`),
  CONSTRAINT `FK_persona_beneficios` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`),
  CONSTRAINT `FK_persona_beneficio_banco_id` FOREIGN KEY (`banco_id`) REFERENCES `bancos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_persona_beneficio_codigo_beneficio_global_dato` FOREIGN KEY (`codigo_beneficio`) REFERENCES `global_datos` (`id`),
  CONSTRAINT `FK_persona_beneficio_empresa_global_dato` FOREIGN KEY (`codigo_empresa`) REFERENCES `global_datos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10140 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `persona_novedad_comentarios`
--

DROP TABLE IF EXISTS `persona_novedad_comentarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `persona_novedad_comentarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `persona_novedad_id` int(11) DEFAULT '0',
  `fecha` date DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `descripcion` text,
  `archivo_adjunto` varchar(50) DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_persona_novedad_comentarios_novedad_id` (`persona_novedad_id`),
  CONSTRAINT `FK_persona_novedad_comentarios_novedad_id` FOREIGN KEY (`persona_novedad_id`) REFERENCES `persona_novedades` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `persona_novedades`
--

DROP TABLE IF EXISTS `persona_novedades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `persona_novedades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `persona_id` int(11) DEFAULT '0',
  `fecha` date DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `descripcion` text,
  `archivo_adjunto` varchar(50) DEFAULT NULL,
  `finalizada` tinyint(1) DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_persona_novedades_persona_id` (`persona_id`),
  CONSTRAINT `FK_persona_novedades_persona_id` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2588 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `personas`
--

DROP TABLE IF EXISTS `personas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_documento` varchar(12) NOT NULL,
  `documento` varchar(11) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `fecha_fallecimiento` date DEFAULT NULL,
  `fallecida` tinyint(1) DEFAULT '0',
  `sexo` varchar(1) DEFAULT NULL,
  `estado_civil` varchar(12) DEFAULT NULL,
  `calle` varchar(150) DEFAULT NULL,
  `numero_calle` varchar(5) DEFAULT NULL,
  `piso` varchar(5) DEFAULT NULL,
  `dpto` varchar(5) DEFAULT NULL,
  `barrio` varchar(100) DEFAULT NULL,
  `localidad_id` int(11) DEFAULT '0',
  `localidad` varchar(150) DEFAULT NULL,
  `codigo_postal` varchar(8) DEFAULT NULL,
  `provincia_id` int(11) DEFAULT '0',
  `maps_latitud` float(10,6) DEFAULT NULL,
  `maps_longitud` float(10,6) DEFAULT NULL,
  `entre_calle_1` varchar(45) DEFAULT NULL,
  `entre_calle_2` varchar(45) DEFAULT NULL,
  `cuit_cuil` varchar(11) DEFAULT NULL,
  `nombre_conyuge` varchar(150) DEFAULT NULL,
  `telefono_fijo` varchar(50) DEFAULT NULL,
  `telefono_fijo_c` varchar(5) DEFAULT NULL,
  `telefono_fijo_n` varchar(15) DEFAULT NULL,
  `telefono_movil` varchar(50) DEFAULT NULL,
  `telefono_movil_c` varchar(5) DEFAULT NULL,
  `telefono_movil_n` varchar(15) DEFAULT NULL,
  `telefono_movil_empresa` varchar(12) DEFAULT NULL,
  `telefono_referencia` varchar(50) DEFAULT NULL,
  `telefono_referencia_c` varchar(5) DEFAULT NULL,
  `telefono_referencia_n` varchar(15) DEFAULT NULL,
  `persona_referencia` varchar(100) DEFAULT NULL,
  `e_mail` varchar(100) DEFAULT NULL,
  `facebook_profile` varchar(100) DEFAULT NULL,
  `twitter_profile` varchar(100) DEFAULT NULL,
  `tipo_vivienda` varchar(12) DEFAULT NULL,
  `filial` varchar(12) DEFAULT NULL,
  `idr` int(11) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_tdocNdoc_unico` (`tipo_documento`,`documento`),
  KEY `idx_tdoc_ndoc_apenom` (`tipo_documento`,`documento`,`apellido`,`nombre`),
  KEY `idx_ndoc` (`documento`),
  KEY `idx_idr_v1` (`idr`),
  KEY `idx_localidad` (`localidad_id`,`localidad`)
) ENGINE=InnoDB AUTO_INCREMENT=9794 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedor_comisiones`
--

DROP TABLE IF EXISTS `proveedor_comisiones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedor_comisiones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_id` int(11) DEFAULT '0',
  `tipo` varchar(12) DEFAULT NULL,
  `codigo_organismo` varchar(12) DEFAULT NULL,
  `tipo_producto` varchar(12) DEFAULT NULL,
  `tipo_cuota` varchar(12) DEFAULT NULL,
  `comision` decimal(10,3) DEFAULT '0.000',
  `fecha_vigencia` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idu_1` (`proveedor_id`,`codigo_organismo`),
  KEY `idx_1` (`proveedor_id`,`tipo`,`tipo_producto`,`tipo_cuota`),
  CONSTRAINT `FK_proveedor_comisiones_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=140 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedor_ctactes`
--

DROP TABLE IF EXISTS `proveedor_ctactes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedor_ctactes` (
  `item` int(11) DEFAULT '0',
  `proveedor_id` int(11) DEFAULT '0',
  `fecha` date DEFAULT NULL,
  `concepto` varchar(50) DEFAULT NULL,
  `debe` decimal(12,2) DEFAULT '0.00',
  `haber` decimal(12,2) DEFAULT '0.00',
  `saldo` decimal(12,2) DEFAULT '0.00',
  `id` int(11) DEFAULT '0',
  `tipo` varchar(5) DEFAULT NULL,
  `anular` tinyint(1) DEFAULT '0',
  `comentario` varchar(100) DEFAULT NULL,
  `pagos` decimal(12,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedor_facturas`
--

DROP TABLE IF EXISTS `proveedor_facturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedor_facturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(2) DEFAULT NULL,
  `proveedor_id` int(11) DEFAULT '0',
  `fecha_comprobante` date DEFAULT NULL,
  `tipo_comprobante` varchar(12) DEFAULT NULL,
  `letra_comprobante` varchar(1) DEFAULT NULL,
  `punto_venta_comprobante` varchar(4) DEFAULT NULL,
  `numero_comprobante` varchar(8) DEFAULT NULL,
  `importe_gravado` decimal(15,2) DEFAULT '0.00',
  `importe_no_gravado` decimal(15,2) DEFAULT '0.00',
  `tasa_iva` int(3) DEFAULT '0',
  `importe_iva` decimal(15,2) DEFAULT '0.00',
  `percepcion` decimal(15,2) DEFAULT '0.00',
  `retencion` decimal(15,2) DEFAULT '0.00',
  `impuesto_interno` decimal(15,2) DEFAULT '0.00',
  `ingreso_bruto` decimal(15,2) DEFAULT '0.00',
  `otro_impuesto` decimal(15,2) DEFAULT '0.00',
  `total_comprobante` decimal(15,2) DEFAULT '0.00',
  `ejercicio_id` int(11) DEFAULT '0',
  `proveedor_tipo_asiento_id` int(11) DEFAULT '0',
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `periodo_iva` varchar(6) DEFAULT NULL,
  `vencimiento1` date DEFAULT NULL,
  `vencimiento2` date DEFAULT NULL,
  `vencimiento3` date DEFAULT NULL,
  `vencimiento4` date DEFAULT NULL,
  `vencimiento5` date DEFAULT NULL,
  `vencimiento6` date DEFAULT NULL,
  `vencimiento7` date DEFAULT NULL,
  `vencimiento8` date DEFAULT NULL,
  `vencimiento9` date DEFAULT NULL,
  `vencimiento10` date DEFAULT NULL,
  `importe_venc1` decimal(15,2) DEFAULT '0.00',
  `importe_venc2` decimal(15,2) DEFAULT '0.00',
  `importe_venc3` decimal(15,2) DEFAULT '0.00',
  `importe_venc4` decimal(15,2) DEFAULT '0.00',
  `importe_venc5` decimal(15,2) DEFAULT '0.00',
  `importe_venc6` decimal(15,2) DEFAULT '0.00',
  `importe_venc7` decimal(15,2) DEFAULT '0.00',
  `importe_venc8` decimal(15,2) DEFAULT '0.00',
  `importe_venc9` decimal(15,2) DEFAULT '0.00',
  `importe_venc10` decimal(15,2) DEFAULT '0.00',
  `estado` varchar(1) DEFAULT NULL,
  `concepto_gasto` varchar(12) DEFAULT NULL,
  `socio_id` int(11) DEFAULT '0',
  `orden_descuento_id` int(11) DEFAULT '0',
  `liquidacion_id` int(11) DEFAULT '0',
  `cancelacion_orden_id` int(11) DEFAULT '0',
  `orden_caja_cobro_id` int(11) DEFAULT '0',
  `orden_descuento_cobro_id` int(11) DEFAULT '0',
  `comentario` varchar(100) DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `proveedor_liquidacion_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_proveedor` (`proveedor_id`),
  KEY `idx_ejercicio_plan_cuenta` (`ejercicio_id`,`co_plan_cuenta_id`),
  KEY `idx_socio` (`socio_id`),
  KEY `idx_liquidacion` (`liquidacion_id`),
  KEY `idx_cancelacion_orden` (`cancelacion_orden_id`),
  KEY `idx_orden_caja_cobro` (`orden_caja_cobro_id`),
  KEY `idx_orden_descuento_cobro` (`orden_descuento_cobro_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11465 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedor_grilla_cuotas`
--

DROP TABLE IF EXISTS `proveedor_grilla_cuotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedor_grilla_cuotas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_grilla_id` int(11) NOT NULL,
  `capital` decimal(10,2) DEFAULT '0.00',
  `liquido` decimal(10,2) DEFAULT '0.00',
  `cuotas` int(11) DEFAULT '0',
  `importe` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `idx_capital_cuotas` (`capital`,`cuotas`),
  KEY `idx_liquido_cuotas` (`liquido`,`cuotas`),
  KEY `idx_cuota_importe` (`cuotas`,`importe`),
  KEY `fk_proveedor_plan_grilla_cuotas_proveedor_grillas1_idx` (`proveedor_grilla_id`),
  CONSTRAINT `fk_proveedor_plan_grilla_cuotas_proveedor_plan_grillas1` FOREIGN KEY (`proveedor_grilla_id`) REFERENCES `proveedor_grillas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedor_grillas`
--

DROP TABLE IF EXISTS `proveedor_grillas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedor_grillas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_plan_id` int(11) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `vigencia_desde` date DEFAULT NULL,
  `cuotas` longtext,
  `xls` longblob,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_proveedor_plan_grillas_proveedor_planes1_idx` (`proveedor_plan_id`),
  CONSTRAINT `fk_proveedor_plan_grillas_proveedor_planes1` FOREIGN KEY (`proveedor_plan_id`) REFERENCES `proveedor_planes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedor_liquidaciones`
--

DROP TABLE IF EXISTS `proveedor_liquidaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedor_liquidaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `origen_recibo_id` int(11) DEFAULT '0',
  `liquidacion_id` int(11) DEFAULT '0',
  `proveedor_origen_fondo_id` int(11) DEFAULT '0',
  `proveedor_id` int(11) DEFAULT '0',
  `cliente_id` int(11) DEFAULT '0',
  `tipo_factura` varchar(2) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `periodo_cobro` varchar(6) DEFAULT NULL,
  `importe_proveedor` decimal(10,2) DEFAULT '0.00',
  `comision_cobranza` decimal(10,2) DEFAULT '0.00',
  `orden_pago_id` int(11) DEFAULT '0',
  `recibo_id` int(11) DEFAULT '0',
  `proveedor_factura_id` int(11) DEFAULT '0',
  `cliente_factura_id` int(11) DEFAULT '0',
  `cancelacion_orden_id` int(11) DEFAULT '0',
  `orden_caja_cobro_id` int(11) DEFAULT '0',
  `orden_descuento_cobro_id` int(11) DEFAULT '0',
  `concepto` varchar(100) DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proveedor_id` (`proveedor_id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `proveedor_origen_fondo_id` (`proveedor_origen_fondo_id`),
  KEY `idx_orden_pago_id` (`orden_pago_id`),
  KEY `idx_recibo_id` (`recibo_id`),
  KEY `idx_proveedor_factura_id` (`proveedor_factura_id`),
  KEY `idx_cliente_factura_id` (`cliente_factura_id`),
  KEY `idx_cancelacion_orden_id` (`cancelacion_orden_id`),
  KEY `idx_origen_recibo_id` (`origen_recibo_id`),
  KEY `idx_liquidacion_id` (`liquidacion_id`),
  CONSTRAINT `proveedor_liquidaciones_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `proveedor_liquidaciones_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `proveedor_liquidaciones_ibfk_3` FOREIGN KEY (`proveedor_origen_fondo_id`) REFERENCES `proveedores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3530 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedor_plan_anexos`
--

DROP TABLE IF EXISTS `proveedor_plan_anexos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedor_plan_anexos` (
  `proveedor_plan_id` int(11) NOT NULL,
  `codigo_anexo` varchar(12) NOT NULL,
  PRIMARY KEY (`proveedor_plan_id`,`codigo_anexo`),
  KEY `fk_proveedor_plan_anexos_proveedor_planes1_idx` (`proveedor_plan_id`),
  CONSTRAINT `fk_proveedor_plan_anexos_proveedor_planes1_idx` FOREIGN KEY (`proveedor_plan_id`) REFERENCES `proveedor_planes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedor_plan_grilla_cuotas`
--

DROP TABLE IF EXISTS `proveedor_plan_grilla_cuotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedor_plan_grilla_cuotas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_plan_grilla_id` int(11) NOT NULL,
  `capital` decimal(10,2) DEFAULT '0.00',
  `liquido` decimal(10,2) DEFAULT '0.00',
  `cuotas` int(11) DEFAULT '0',
  `importe` decimal(10,2) DEFAULT '0.00',
  `cft` decimal(10,2) DEFAULT '0.00',
  `capital_puro` decimal(10,2) DEFAULT '0.00',
  `interes` decimal(10,2) DEFAULT '0.00',
  `iva` decimal(10,2) DEFAULT '0.00',
  `gasto_admin` decimal(10,2) DEFAULT '0.00',
  `sellado` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `idx_capital_cuotas` (`capital`,`cuotas`),
  KEY `idx_liquido_cuotas` (`liquido`,`cuotas`),
  KEY `idx_cuota_importe` (`cuotas`,`importe`),
  KEY `fk_proveedor_plan_grilla_cuotas_proveedor_grillas1_idx` (`proveedor_plan_grilla_id`),
  CONSTRAINT `fk_proveedor_plan_grilla_cuotas_proveedor_plan_grillas11` FOREIGN KEY (`proveedor_plan_grilla_id`) REFERENCES `proveedor_plan_grillas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=59492 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedor_plan_grillas`
--

DROP TABLE IF EXISTS `proveedor_plan_grillas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedor_plan_grillas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_plan_id` int(11) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `vigencia_desde` date DEFAULT NULL,
  `cuotas` longtext,
  `tna` decimal(10,2) DEFAULT '0.00',
  `tnm` decimal(10,2) DEFAULT '0.00',
  `tem` decimal(10,2) DEFAULT '0.00',
  `gasto_admin` decimal(10,2) DEFAULT '0.00',
  `sellado` decimal(10,2) DEFAULT '0.00',
  `iva` decimal(10,2) DEFAULT '0.00',
  `metodo_calculo` int(11) DEFAULT '1',
  `xls` longblob,
  `observaciones` text,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_proveedor_plan_grillas_proveedor_planes1_idx` (`proveedor_plan_id`),
  KEY `IDX_VIGENCIA` (`vigencia_desde`),
  CONSTRAINT `fk_proveedor_plan_grillas_proveedor_planes11` FOREIGN KEY (`proveedor_plan_id`) REFERENCES `proveedor_planes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedor_plan_organismos`
--

DROP TABLE IF EXISTS `proveedor_plan_organismos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedor_plan_organismos` (
  `proveedor_plan_id` int(11) NOT NULL,
  `codigo_organismo` varchar(12) NOT NULL,
  PRIMARY KEY (`proveedor_plan_id`,`codigo_organismo`),
  KEY `fk_proveedor_condiciones_organismos_proveedor_condiciones1_idx` (`proveedor_plan_id`),
  CONSTRAINT `fk_proveedor_condiciones_organismos_proveedor_condiciones1` FOREIGN KEY (`proveedor_plan_id`) REFERENCES `proveedor_planes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedor_planes`
--

DROP TABLE IF EXISTS `proveedor_planes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedor_planes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_id` int(11) NOT NULL DEFAULT '0',
  `tipo_producto` varchar(12) NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `descripcion` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `modelo_solicitud` varchar(200) DEFAULT 'imprimir_credito_mutual_pdf',
  `modelo_solicitud_codigo` varchar(12) DEFAULT NULL,
  `ayuda_economica` tinyint(1) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_proveedor_condiciones_proveedores1_idx` (`proveedor_id`),
  KEY `fk_proveedor_planes_productos_idx` (`tipo_producto`),
  KEY `fk_proveedor_planes_modelo_solicitud` (`modelo_solicitud_codigo`),
  CONSTRAINT `fk_proveedor_planes_modelo_solicitud` FOREIGN KEY (`modelo_solicitud_codigo`) REFERENCES `global_datos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_proveedor_planes_productos` FOREIGN KEY (`tipo_producto`) REFERENCES `global_datos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_proveedor_planes_proveedores` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedor_prioridad_imputa_organismos`
--

DROP TABLE IF EXISTS `proveedor_prioridad_imputa_organismos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedor_prioridad_imputa_organismos` (
  `proveedor_id` int(11) NOT NULL,
  `codigo_organismo` varchar(12) NOT NULL,
  `prioridad` int(11) NOT NULL,
  PRIMARY KEY (`proveedor_id`,`codigo_organismo`),
  KEY `fk_proveedor_prioridad_imputa_organismos_2_idx` (`codigo_organismo`),
  CONSTRAINT `fk_proveedor_prioridad_imputa_organismos_1` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_proveedor_prioridad_imputa_organismos_2` FOREIGN KEY (`codigo_organismo`) REFERENCES `global_datos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedor_tipo_asiento_renglones`
--

DROP TABLE IF EXISTS `proveedor_tipo_asiento_renglones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedor_tipo_asiento_renglones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variable` varchar(5) DEFAULT NULL,
  `proveedor_tipo_asiento_id` int(11) DEFAULT '0',
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `debe_haber` varchar(1) DEFAULT 'D',
  `tipo_asiento` varchar(2) DEFAULT 'GR',
  PRIMARY KEY (`id`),
  KEY `FK_proveedor_tipo_asiento_renglones` (`proveedor_tipo_asiento_id`),
  CONSTRAINT `FK_proveedor_tipo_asiento_renglones` FOREIGN KEY (`proveedor_tipo_asiento_id`) REFERENCES `proveedor_tipo_asientos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=235 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedor_tipo_asientos`
--

DROP TABLE IF EXISTS `proveedor_tipo_asientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedor_tipo_asientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `concepto` varchar(100) DEFAULT NULL,
  `tipo_asiento` varchar(2) NOT NULL DEFAULT 'GR',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedor_vencimientos`
--

DROP TABLE IF EXISTS `proveedor_vencimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedor_vencimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_id` int(11) DEFAULT '0',
  `codigo_organismo` varchar(12) DEFAULT NULL,
  `d_corte` int(2) DEFAULT '0',
  `d_vto_socio` int(2) DEFAULT '0',
  `d_vto_proveedor_suma` int(2) DEFAULT '0',
  `mes` varchar(2) DEFAULT NULL,
  `m_ini_socio_ac_suma` int(2) DEFAULT '0',
  `m_ini_socio_dc_suma` int(2) DEFAULT '0',
  `m_vto_socio_suma` int(2) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_organismo_proveedor_mes` (`proveedor_id`,`codigo_organismo`,`mes`),
  KEY `idx_mes` (`mes`),
  KEY `idx_proveedor` (`proveedor_id`),
  KEY `idx_organismo` (`codigo_organismo`),
  CONSTRAINT `fk_proveedor_vencimiento_proveedores` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35170 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cuit` varchar(11) NOT NULL,
  `razon_social` varchar(150) DEFAULT NULL,
  `razon_social_resumida` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT NULL,
  `calle` varchar(150) DEFAULT NULL,
  `numero_calle` varchar(5) DEFAULT NULL,
  `piso` varchar(5) DEFAULT NULL,
  `dpto` varchar(5) DEFAULT NULL,
  `barrio` varchar(50) DEFAULT NULL,
  `localidad` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(11) DEFAULT NULL,
  `telefono_fijo` varchar(50) DEFAULT NULL,
  `telefono_movil` varchar(50) DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `concepto_gasto` varchar(12) DEFAULT NULL,
  `proveedor_tipo_asiento_id` int(11) DEFAULT '0',
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `nro_ingresos_brutos` varchar(15) DEFAULT NULL,
  `condicion_iva` varchar(12) DEFAULT NULL,
  `intercambio` tinyint(1) DEFAULT '0',
  `template_intercambio` varchar(100) DEFAULT NULL,
  `idr` varchar(11) DEFAULT NULL,
  `situacion_cuota` varchar(12) DEFAULT 'MUTUSICUMUTU',
  `estado_cuota` varchar(1) DEFAULT 'A',
  `tipo_proveedor` varchar(1) DEFAULT '0',
  `responsable` varchar(150) DEFAULT NULL,
  `contacto` varchar(150) DEFAULT NULL,
  `fecha_saldo` date DEFAULT NULL,
  `importe_saldo` decimal(15,2) DEFAULT '0.00',
  `tipo_saldo` varchar(1) DEFAULT NULL,
  `proveedor_factura_id` int(11) DEFAULT '0',
  `codigo_acceso_ws` varchar(50) DEFAULT NULL,
  `pagare_blank` tinyint(1) DEFAULT '1',
  `cliente_id` int(11) DEFAULT '0',
  `reasignable` tinyint(1) DEFAULT '0',
  `vendedores` tinyint(1) DEFAULT '0',
  `genera_cuota_social` tinyint(1) NOT NULL DEFAULT '1',
  `liquida_prestamo` tinyint(1) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idu_proveedor_cuit` (`cuit`),
  KEY `idx_idr` (`idr`),
  KEY `idx_codigo_acceso_ws` (`codigo_acceso_ws`)
) ENGINE=InnoDB AUTO_INCREMENT=368 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `provincias`
--

DROP TABLE IF EXISTS `provincias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `provincias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `letra` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `NewIndex1` (`nombre`),
  KEY `NewIndex2` (`letra`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recibo_detalles`
--

DROP TABLE IF EXISTS `recibo_detalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recibo_detalles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `persona_id` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `cliente_id` int(11) DEFAULT '0',
  `banco_id` varchar(5) DEFAULT NULL,
  `codigo_organismo` varchar(12) DEFAULT NULL,
  `recibo_id` int(11) NOT NULL DEFAULT '0',
  `tipo_cobro` varchar(2) DEFAULT NULL,
  `cliente_factura_id` int(11) DEFAULT '0',
  `recibo_detalle_id` int(11) DEFAULT '0',
  `orden_descuento_cobro_id` int(11) DEFAULT '0',
  `orden_descuento_cuota_id` int(11) DEFAULT '0',
  `orden_descuento_id` int(11) DEFAULT '0',
  `orden_descuento_cobro_cuota_id` int(11) DEFAULT '0',
  `socio_reintegro_id` int(11) DEFAULT '0',
  `concepto` varchar(100) DEFAULT NULL,
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `importe` decimal(15,2) DEFAULT '0.00',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `verificar_orden_descuento_id` int(11) DEFAULT '0',
  `numero` int(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_persona` (`persona_id`),
  KEY `idx_socio` (`socio_id`),
  KEY `idx_cliente` (`cliente_id`),
  KEY `idx_banco` (`banco_id`),
  KEY `idx_organismo` (`codigo_organismo`),
  KEY `idx_recibo` (`recibo_id`),
  KEY `idx_cliente_factura` (`cliente_factura_id`),
  KEY `idx_orden_descuento_cobro` (`orden_descuento_cobro_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14833 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recibo_facturas`
--

DROP TABLE IF EXISTS `recibo_facturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recibo_facturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `cliente_id` int(11) DEFAULT '0',
  `cliente_factura_id` int(11) DEFAULT '0',
  `recibo_id` int(11) DEFAULT '0',
  `cliente_credito_id` int(11) DEFAULT '0',
  `recibo_detalle_id` int(11) DEFAULT '0',
  `importe` decimal(15,2) DEFAULT '0.00',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cliente` (`cliente_id`),
  KEY `idx_cliente_factura` (`cliente_factura_id`),
  KEY `idx_recibo` (`recibo_id`),
  KEY `idx_cliente_credito` (`cliente_credito_id`),
  KEY `idx_recibo_detalle` (`recibo_detalle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=746 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recibo_formas`
--

DROP TABLE IF EXISTS `recibo_formas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recibo_formas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_id` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `persona_id` int(11) DEFAULT '0',
  `banco_id` int(11) DEFAULT '0',
  `codigo_organismo` int(11) DEFAULT '0',
  `recibo_id` int(11) DEFAULT '0',
  `forma_cobro` varchar(2) DEFAULT NULL,
  `banco_cuenta_id` int(11) DEFAULT '0',
  `banco_cuenta_movimiento_id` int(11) DEFAULT '0',
  `numero_cuenta` varchar(20) DEFAULT NULL,
  `numero_operacion` varchar(20) DEFAULT NULL,
  `fecha_operacion` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `importe` decimal(15,2) DEFAULT '0.00',
  `concepto` varchar(100) DEFAULT NULL,
  `descripcion_cobro` varchar(50) DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_proveedor` (`proveedor_id`),
  KEY `idx_socio` (`socio_id`),
  KEY `idx_persona` (`persona_id`),
  KEY `idx_banco` (`banco_id`),
  KEY `idx_organismo` (`codigo_organismo`),
  KEY `idx_recibo` (`recibo_id`),
  KEY `idx_banco_cuenta_movimiento` (`banco_cuenta_movimiento_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6368 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recibos`
--

DROP TABLE IF EXISTS `recibos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recibos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_documento` varchar(3) DEFAULT 'RCI',
  `letra` varchar(1) DEFAULT NULL,
  `sucursal` varchar(4) DEFAULT '0001',
  `nro_recibo` varchar(8) DEFAULT NULL,
  `fecha_comprobante` date DEFAULT NULL,
  `persona_id` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `cliente_id` int(11) DEFAULT '0',
  `banco_id` char(5) DEFAULT NULL,
  `codigo_organismo` varchar(12) DEFAULT NULL,
  `nro_solicitud` int(11) DEFAULT '0',
  `importe` decimal(15,2) DEFAULT '0.00',
  `aporte_socio` decimal(15,2) DEFAULT '0.00',
  `importe_cancela` decimal(15,2) DEFAULT '0.00',
  `co_plan_cuenta_id` int(11) DEFAULT '0',
  `comentarios` varchar(100) DEFAULT NULL,
  `anulado` tinyint(1) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idu_letra_suc_nro` (`letra`,`sucursal`,`nro_recibo`),
  KEY `idx_persona` (`persona_id`),
  KEY `idx_socio` (`socio_id`),
  KEY `idx_cliente` (`cliente_id`),
  KEY `idx_banco` (`banco_id`),
  KEY `idx_organismo` (`codigo_organismo`)
) ENGINE=InnoDB AUTO_INCREMENT=5859 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `socio_adicionales`
--

DROP TABLE IF EXISTS `socio_adicionales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `socio_adicionales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `persona_id` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `vinculo` varchar(12) DEFAULT NULL,
  `tipo_documento` varchar(12) DEFAULT NULL,
  `documento` varchar(11) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `sexo` varchar(1) DEFAULT NULL,
  `calle` varchar(150) DEFAULT NULL,
  `numero_calle` varchar(5) DEFAULT NULL,
  `piso` varchar(5) DEFAULT NULL,
  `dpto` varchar(5) DEFAULT NULL,
  `barrio` varchar(100) DEFAULT NULL,
  `localidad_id` int(11) DEFAULT '0',
  `localidad` varchar(150) DEFAULT NULL,
  `codigo_postal` varchar(8) DEFAULT NULL,
  `provincia_id` int(11) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idu_tdoc_ndoc` (`tipo_documento`,`documento`),
  KEY `FK_socio_adicionales_socios` (`socio_id`),
  KEY `FK_socio_adicionales_persona` (`persona_id`),
  CONSTRAINT `FK_socio_adicionales_persona` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `socio_calificaciones`
--

DROP TABLE IF EXISTS `socio_calificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `socio_calificaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `socio_id` int(11) DEFAULT '0',
  `persona_beneficio_id` int(11) DEFAULT '0',
  `periodo` varchar(6) DEFAULT NULL,
  `calificacion` varchar(12) DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_socio_calificaciones_socio` (`socio_id`),
  KEY `idx_calificacion` (`calificacion`),
  KEY `idx_created` (`created`),
  KEY `idx_socio_periodo_beneficio` (`socio_id`,`persona_beneficio_id`,`periodo`),
  CONSTRAINT `FK_socio_calificaciones_socio` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=163445 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `socio_convenio_cuotas`
--

DROP TABLE IF EXISTS `socio_convenio_cuotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `socio_convenio_cuotas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `socio_convenio_id` int(11) DEFAULT '0',
  `orden_descuento_cuota_id` int(11) DEFAULT '0',
  `proveedor_id` int(11) DEFAULT '0',
  `importe` decimal(10,2) DEFAULT '0.00',
  `ponderacion` decimal(10,3) DEFAULT '0.000',
  PRIMARY KEY (`id`),
  KEY `FK_socio_convenio_cuotas_convenio_id` (`socio_convenio_id`),
  KEY `FK_socio_convenio_cuotas_cuota_id` (`orden_descuento_cuota_id`),
  CONSTRAINT `FK_socio_convenio_cuotas_convenio_id` FOREIGN KEY (`socio_convenio_id`) REFERENCES `socio_convenios` (`id`),
  CONSTRAINT `FK_socio_convenio_cuotas_cuota_id` FOREIGN KEY (`orden_descuento_cuota_id`) REFERENCES `orden_descuento_cuotas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `socio_convenios`
--

DROP TABLE IF EXISTS `socio_convenios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `socio_convenios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_id` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `persona_beneficio_id` int(11) DEFAULT '0',
  `fecha` date DEFAULT NULL,
  `periodo_ini` varchar(6) DEFAULT NULL,
  `primer_vto_socio` date DEFAULT NULL,
  `primer_vto_proveedor` date DEFAULT NULL,
  `tipo_convenio` varchar(12) DEFAULT NULL,
  `importe_total` decimal(10,2) DEFAULT '0.00',
  `cuotas` int(11) DEFAULT '0',
  `importe_cuota` decimal(10,2) DEFAULT '0.00',
  `baja` tinyint(1) DEFAULT '0',
  `fecha_baja` date DEFAULT NULL,
  `orden_descuento_id` int(11) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_socio_convenios_socio_id` (`socio_id`),
  KEY `FK_socio_convenios_persona_beneficio` (`persona_beneficio_id`),
  KEY `FK_socio_convenios_proveedor_id` (`proveedor_id`),
  KEY `idx_periodo_ini` (`periodo_ini`),
  KEY `FK_socio_convenios_orden_descuento_id` (`orden_descuento_id`),
  CONSTRAINT `FK_socio_convenios_persona_beneficio` FOREIGN KEY (`persona_beneficio_id`) REFERENCES `persona_beneficios` (`id`),
  CONSTRAINT `FK_socio_convenios_proveedor_id` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `FK_socio_convenios_socio_id` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `socio_historicos`
--

DROP TABLE IF EXISTS `socio_historicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `socio_historicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `socio_id` int(11) DEFAULT '0',
  `socio_solicitud_id` int(11) DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  `calificacion` varchar(12) DEFAULT NULL,
  `codigo_baja` varchar(12) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `fecha_alta` date DEFAULT NULL,
  `fecha_baja` date DEFAULT NULL,
  `fecha_calificacion` date DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `observaciones` text,
  `orden_descuento_id` int(11) DEFAULT '0',
  `periodicidad` varchar(1) DEFAULT '0',
  `periodo_ini` varchar(6) DEFAULT NULL,
  `persona_beneficio_id` int(11) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_socio` (`socio_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1909 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `socio_padron_inaes`
--

DROP TABLE IF EXISTS `socio_padron_inaes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `socio_padron_inaes` (
  `socio_id` int(11) NOT NULL DEFAULT '0',
  `fecha_informe` date DEFAULT NULL,
  `tipo_novedad` varchar(1) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `Tipo_Persona` varchar(1) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `Categoria` int(0) NOT NULL DEFAULT '0',
  `Numero_Asociado` int(11) NOT NULL DEFAULT '0',
  `Apellido` varchar(100) CHARACTER SET utf8 NOT NULL,
  `Nombre` varchar(100) CHARACTER SET utf8 NOT NULL,
  `Denominacion` char(0) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `Tipo_Documento` int(11) DEFAULT '0',
  `Numero_de_Documento` varchar(11) CHARACTER SET utf8 NOT NULL,
  `Cuit_CUIL_CDI` varchar(11) CHARACTER SET utf8 DEFAULT NULL,
  `Calle` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `Numero` varchar(5) CHARACTER SET utf8 DEFAULT NULL,
  `Piso` varchar(5) CHARACTER SET utf8 DEFAULT NULL,
  `Departamento` varchar(5) CHARACTER SET utf8 DEFAULT NULL,
  `Provincia` bigint(11) NOT NULL DEFAULT '0',
  `Localidad` bigint(11) NOT NULL DEFAULT '0',
  `Codigo_Postal` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `Fecha_Ingreso` date DEFAULT NULL,
  `Fecha_Resolucion` char(0) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `Organo_Emisor` char(0) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `Fecha_Egreso` char(0) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `Causa` char(0) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `Medida_Disiplinaria` char(0) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `Ctas_Sociales_Suscriptas` char(0) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `Ctas_Sociales_Integradas` char(0) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `Observacion` char(0) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `Tipo_de_Cambio` varchar(1) CHARACTER SET utf8 NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `socio_reintegro_pagos`
--

DROP TABLE IF EXISTS `socio_reintegro_pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `socio_reintegro_pagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `socio_reintegro_id` int(11) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `forma_pago` varchar(12) CHARACTER SET utf8 DEFAULT NULL,
  `banco_id` varchar(5) DEFAULT NULL,
  `nro_operacion` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `fecha_operacion` date DEFAULT NULL,
  `nro_opago` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `orden_pago_id` int(11) DEFAULT '0',
  `importe` decimal(10,2) DEFAULT '0.00',
  `user_created` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `user_modified` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_socio_reintegro_pagos_socio` (`socio_id`),
  KEY `idx_socio_reintegro_id` (`socio_reintegro_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1247 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `socio_reintegros`
--

DROP TABLE IF EXISTS `socio_reintegros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `socio_reintegros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anticipado` tinyint(1) DEFAULT '0',
  `compensa_imputacion` tinyint(1) DEFAULT '0',
  `socio_id` int(11) DEFAULT '0',
  `persona_beneficio_id` int(11) DEFAULT '0',
  `liquidacion_id` int(11) DEFAULT '0',
  `liquidacion_socio_id` int(11) DEFAULT '0',
  `periodo` varchar(6) DEFAULT NULL,
  `importe_dto` decimal(10,2) DEFAULT '0.00',
  `importe_debitado` decimal(10,2) DEFAULT '0.00',
  `importe_reintegro` decimal(10,2) DEFAULT '0.00',
  `importe_imputado` decimal(10,2) DEFAULT '0.00',
  `importe_aplicado` decimal(10,2) DEFAULT '0.00',
  `importe_reversado` decimal(10,2) DEFAULT '0.00',
  `reversado` tinyint(1) DEFAULT '0',
  `fecha_reverso` date DEFAULT NULL,
  `periodo_proveedor_reverso` varchar(6) DEFAULT NULL,
  `usuario_reverso` varchar(50) DEFAULT NULL,
  `procesado` tinyint(1) DEFAULT '0',
  `reintegrado` tinyint(1) DEFAULT '0',
  `imputado_deuda` tinyint(1) DEFAULT '0',
  `orden_descuento_cobro_id` int(11) DEFAULT '0',
  `orden_descuento_cuota_nc_id` int(11) DEFAULT '0',
  `socio_reintegro_pago_id` int(11) DEFAULT '0',
  `recupero_cuota_id` int(11) DEFAULT '0',
  `orden_pago_id` int(11) DEFAULT '0',
  `banco_cuenta_movimiento_id` int(11) DEFAULT '0',
  `recibo_id` int(11) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_beneficio` (`persona_beneficio_id`),
  KEY `idx_socio_beneficio` (`socio_id`,`persona_beneficio_id`),
  KEY `idx_liquidacion` (`liquidacion_id`),
  KEY `idx_reintegrado` (`reintegrado`),
  KEY `idx_imputado_deuda` (`imputado_deuda`),
  KEY `idx_socio_reintegro_pago` (`socio_reintegro_pago_id`),
  KEY `IDX_COMPENSA` (`anticipado`,`compensa_imputacion`,`socio_id`,`liquidacion_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8593 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `socio_solicitudes`
--

DROP TABLE IF EXISTS `socio_solicitudes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `socio_solicitudes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_solicitud` varchar(12) DEFAULT NULL,
  `aprobada` tinyint(1) DEFAULT '0',
  `persona_id` int(11) DEFAULT '0',
  `persona_beneficio_id` int(11) DEFAULT '0',
  `fecha` date DEFAULT NULL,
  `periodo_ini` varchar(6) DEFAULT NULL,
  `periodicidad` varchar(1) DEFAULT '0',
  `observaciones` text,
  `orden_descuento_id` int(11) DEFAULT '0',
  `importe_cuota_social` decimal(10,2) DEFAULT '0.00',
  `vendedor_id` int(11) DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `primer_vto_proveedor` date DEFAULT NULL,
  `primer_vto_socio` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_socio_solicitudes_personas` (`persona_id`),
  KEY `FK_socio_solicitudes_beneficio` (`persona_beneficio_id`),
  KEY `idx_periodo` (`periodo_ini`),
  KEY `idx_expediente` (`orden_descuento_id`),
  KEY `fk_socio_solicitudes_1_vendedor_id` (`vendedor_id`),
  CONSTRAINT `fk_socio_solicitudes_1_vendedor_id` FOREIGN KEY (`vendedor_id`) REFERENCES `vendedores` (`id`),
  CONSTRAINT `FK_socio_solicitudes_beneficio` FOREIGN KEY (`persona_beneficio_id`) REFERENCES `persona_beneficios` (`id`),
  CONSTRAINT `FK_socio_solicitudes_personas` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4855 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `socios`
--

DROP TABLE IF EXISTS `socios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `socios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria` varchar(12) NOT NULL,
  `persona_id` int(11) NOT NULL DEFAULT '0',
  `socio_solicitud_id` int(11) DEFAULT '0',
  `persona_beneficio_id` int(11) NOT NULL DEFAULT '0',
  `periodo_ini` varchar(6) DEFAULT NULL,
  `periodicidad` varchar(1) DEFAULT '0',
  `periodo_hasta` varchar(6) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_alta` date DEFAULT NULL,
  `orden_descuento_id` int(11) DEFAULT '0',
  `calificacion` varchar(12) DEFAULT NULL,
  `fecha_calificacion` date DEFAULT NULL,
  `codigo_baja` varchar(12) DEFAULT NULL,
  `fecha_baja` date DEFAULT NULL,
  `observaciones` text,
  `importe_cuota_social` decimal(10,2) DEFAULT '0.00',
  `periodo_mayor_envio` varchar(6) DEFAULT NULL,
  `importe_mayor_envio` decimal(10,2) DEFAULT '0.00',
  `periodo_mayor_debito` varchar(6) DEFAULT NULL,
  `importe_mayor_debito` decimal(10,2) DEFAULT '0.00',
  `idr` int(11) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `FK_socios` (`persona_id`),
  KEY `FK_socios_solicitud` (`socio_solicitud_id`),
  KEY `FK_socios_beneficios` (`persona_beneficio_id`),
  KEY `idx_periodo` (`periodo_ini`,`periodicidad`),
  KEY `idx_idr` (`idr`),
  KEY `idx_orden_descuento` (`orden_descuento_id`),
  KEY `idx_calificacion` (`calificacion`,`fecha_calificacion`),
  KEY `IDX_categoria` (`id`,`categoria`,`activo`),
  KEY `idx_activo` (`activo`,`fecha_alta`,`fecha_baja`),
  KEY `idx_fecha_alta` (`fecha_alta`,`fecha_baja`),
  CONSTRAINT `FK_socios_beneficios` FOREIGN KEY (`persona_beneficio_id`) REFERENCES `persona_beneficios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3904 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `soporte_tickets`
--

DROP TABLE IF EXISTS `soporte_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `soporte_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emisor` varchar(50) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `estado` varchar(1) DEFAULT 'E',
  `prioridad` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_documentos`
--

DROP TABLE IF EXISTS `tipo_documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_documento` varchar(3) DEFAULT NULL,
  `documento` varchar(3) DEFAULT NULL,
  `descripcion` varchar(50) DEFAULT NULL,
  `letra` varchar(1) DEFAULT NULL,
  `sucursal` int(4) DEFAULT '0',
  `numero` int(8) DEFAULT '0',
  `destino` varchar(100) DEFAULT NULL,
  `copias` int(1) DEFAULT '1',
  `longitud_pagina` int(2) DEFAULT '0',
  `look` tinyint(1) DEFAULT '0',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grupo_id` int(11) DEFAULT '0',
  `usuario` varchar(11) NOT NULL DEFAULT '',
  `password` varchar(40) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '0',
  `descripcion` varchar(100) DEFAULT NULL,
  `reset_password` tinyint(1) NOT NULL DEFAULT '1',
  `user` varchar(50) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `vendedor_id` int(11) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `pin` varchar(10) DEFAULT NULL,
  `caduca` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `validado` tinyint(1) NOT NULL DEFAULT '1',
  `ultimo_password` varchar(45) DEFAULT NULL,
  `ip_registro` varchar(45) DEFAULT NULL,
  `host_registro` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_grupo` (`grupo_id`),
  KEY `idx_usuario_password` (`usuario`,`password`),
  CONSTRAINT `FK_usuarios_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vendedor_proveedor_planes`
--

DROP TABLE IF EXISTS `vendedor_proveedor_planes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendedor_proveedor_planes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendedor_id` int(11) NOT NULL,
  `proveedor_plan_id` int(11) NOT NULL,
  `monto_venta` decimal(10,2) NOT NULL DEFAULT '0.00',
  `comision` decimal(10,3) NOT NULL DEFAULT '0.000',
  `activo` tinyint(1) DEFAULT '1',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_PROVEEDOR_PLAN_VENDEDOR` (`proveedor_plan_id`),
  KEY `FK_VENDEDOR_VENDEDOR` (`vendedor_id`),
  CONSTRAINT `FK_PROVEEDOR_PLAN_VENDEDOR` FOREIGN KEY (`proveedor_plan_id`) REFERENCES `proveedor_planes` (`id`),
  CONSTRAINT `FK_VENDEDOR_VENDEDOR` FOREIGN KEY (`vendedor_id`) REFERENCES `vendedores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=194 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vendedor_remitos`
--

DROP TABLE IF EXISTS `vendedor_remitos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendedor_remitos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendedor_id` int(11) NOT NULL,
  `observaciones` text,
  `user_created` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `anulado` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_vendedor_remito_vendedores` (`vendedor_id`),
  CONSTRAINT `fk_vendedor_remito_vendedores` FOREIGN KEY (`vendedor_id`) REFERENCES `vendedores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5403 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vendedores`
--

DROP TABLE IF EXISTS `vendedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vendedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `persona_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDU_PERSONA` (`persona_id`),
  UNIQUE KEY `IDU_USUARIO` (`usuario_id`),
  CONSTRAINT `FK_PERSONA_VENDEDOR` FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`),
  CONSTRAINT `FK_USUARIO_VENDEDOR` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-05-28 10:04:25
