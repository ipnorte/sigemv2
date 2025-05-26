/*
SQLyog Community v11.28 (32 bit)
MySQL - 5.5.34-0ubuntu0.13.04.1 : Database - sigem_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `v_asincronos` */

DROP TABLE IF EXISTS `v_asincronos`;

/*!50001 DROP VIEW IF EXISTS `v_asincronos` */;
/*!50001 DROP TABLE IF EXISTS `v_asincronos` */;

/*!50001 CREATE TABLE  `v_asincronos`(
 `ID` int(11) ,
 `SHELL_PID` int(11) ,
 `PROPIETARIO` varchar(120) ,
 `REMOTE_IP` varchar(100) ,
 `FINAL` datetime ,
 `PROCESO` varchar(150) ,
 `BLOQUEADO` tinyint(1) ,
 `P1` varchar(250) ,
 `P2` varchar(250) ,
 `P3` varchar(250) ,
 `P4` varchar(250) ,
 `P5` varchar(250) ,
 `P6` varchar(250) ,
 `P7` varchar(250) ,
 `P8` varchar(250) ,
 `P9` varchar(250) ,
 `P10` varchar(250) ,
 `P11` varchar(250) ,
 `P12` varchar(250) ,
 `P13` varchar(250) ,
 `TXT1` text ,
 `TXT2` text ,
 `ACTION_DO` varchar(150) ,
 `TARGET` varchar(50) ,
 `BTN_LABEL` varchar(150) ,
 `TITULO` varchar(250) ,
 `SUB_TITULO` varchar(250) ,
 `ESTADO` varchar(1) ,
 `TOTAL` int(11) ,
 `CONTADOR` int(11) ,
 `PORCENTAJE` int(11) ,
 `MSG` varchar(150) ,
 `ERRORES` int(11) ,
 `CREATED` datetime ,
 `MODIFIED` datetime 
)*/;

/*Table structure for table `v_bancos` */

DROP TABLE IF EXISTS `v_bancos`;

/*!50001 DROP VIEW IF EXISTS `v_bancos` */;
/*!50001 DROP TABLE IF EXISTS `v_bancos` */;

/*!50001 CREATE TABLE  `v_bancos`(
 `ID` char(5) ,
 `NOMBRE` char(100) ,
 `ACTIVO` tinyint(1) ,
 `BENEFICIO` tinyint(1) 
)*/;

/*Table structure for table `v_cancelacion_ordenes` */

DROP TABLE IF EXISTS `v_cancelacion_ordenes`;

/*!50001 DROP VIEW IF EXISTS `v_cancelacion_ordenes` */;
/*!50001 DROP TABLE IF EXISTS `v_cancelacion_ordenes` */;

/*!50001 CREATE TABLE  `v_cancelacion_ordenes`(
 `ID` int(11) ,
 `ESTADO` varchar(1) ,
 `ESTADO_DESC` varchar(9) ,
 `CLIENTE_ID` int(11) ,
 `IMPORTE_CANCELA` decimal(10,2) ,
 `IMPORTE_SELECCIONADO` decimal(10,2) ,
 `SALDO_ORDEN` decimal(10,2) ,
 `IMPORTE_CUOTA` decimal(10,2) ,
 `DEBITO_CREDITO` decimal(10,2) ,
 `SALDO` decimal(12,2) ,
 `VENCIMIENTO` date ,
 `TIPO` varchar(7) ,
 `OBSERVACIONES` text ,
 `CONCEPTO` text ,
 `CANTIDAD_CUOTAS` bigint(21) ,
 `PROVEEDOR_ID` int(11) ,
 `A_LA_ORDEN_DE` varchar(150) ,
 `SOLICITUD_CREDITO_ID` int(11) 
)*/;

/*Table structure for table `v_cliente_calificaciones` */

DROP TABLE IF EXISTS `v_cliente_calificaciones`;

/*!50001 DROP VIEW IF EXISTS `v_cliente_calificaciones` */;
/*!50001 DROP TABLE IF EXISTS `v_cliente_calificaciones` */;

/*!50001 CREATE TABLE  `v_cliente_calificaciones`(
 `ID` varchar(12) ,
 `CONCEPTO_1` varchar(100) ,
 `CONCEPTO_2` varchar(100) ,
 `CONCEPTO_3` varchar(100) ,
 `LOGICO_1` tinyint(1) ,
 `LOGICO_2` tinyint(1) ,
 `ENTERO_1` int(11) ,
 `ENTERO_2` int(11) ,
 `DECIMAL_1` decimal(10,2) ,
 `DECIMAL_2` decimal(10,2) ,
 `FECHA_1` date ,
 `FECHA_2` date ,
 `TEXTO_1` text ,
 `TEXTO_2` text 
)*/;

/*Table structure for table `v_cliente_cancelaciones_emitidas` */

DROP TABLE IF EXISTS `v_cliente_cancelaciones_emitidas`;

/*!50001 DROP VIEW IF EXISTS `v_cliente_cancelaciones_emitidas` */;
/*!50001 DROP TABLE IF EXISTS `v_cliente_cancelaciones_emitidas` */;

/*!50001 CREATE TABLE  `v_cliente_cancelaciones_emitidas`(
 `CLIENTE_ID` int(11) ,
 `CANCELACION_ID` int(11) 
)*/;

/*Table structure for table `v_clientes` */

DROP TABLE IF EXISTS `v_clientes`;

/*!50001 DROP VIEW IF EXISTS `v_clientes` */;
/*!50001 DROP TABLE IF EXISTS `v_clientes` */;

/*!50001 CREATE TABLE  `v_clientes`(
 `ID` int(11) ,
 `CATEGORIA` varchar(12) ,
 `PERSONA_ID` int(11) ,
 `BENEFICIO_ID` int(11) ,
 `ACTIVO` tinyint(1) ,
 `FECHA_ALTA` date ,
 `ORDEN_DESCUENTO_ID` int(11) ,
 `CALIFICACION` varchar(12) ,
 `FECHA_CALIFICACION` date ,
 `CODIGO_BAJA` varchar(12) ,
 `FECHA_BAJA` date ,
 `OBSERVACIONES` text 
)*/;

/*Table structure for table `v_credito_solcitud_cancelaciones` */

DROP TABLE IF EXISTS `v_credito_solcitud_cancelaciones`;

/*!50001 DROP VIEW IF EXISTS `v_credito_solcitud_cancelaciones` */;
/*!50001 DROP TABLE IF EXISTS `v_credito_solcitud_cancelaciones` */;

/*!50001 CREATE TABLE  `v_credito_solcitud_cancelaciones`(
 `MUTUAL_PRODUCTO_SOLICITUD_ID` int(11) ,
 `CANCELACION_ORDEN_ID` int(11) 
)*/;

/*Table structure for table `v_credito_solicitud_estados` */

DROP TABLE IF EXISTS `v_credito_solicitud_estados`;

/*!50001 DROP VIEW IF EXISTS `v_credito_solicitud_estados` */;
/*!50001 DROP TABLE IF EXISTS `v_credito_solicitud_estados` */;

/*!50001 CREATE TABLE  `v_credito_solicitud_estados`(
 `ID` varchar(12) ,
 `CONCEPTO_1` varchar(100) ,
 `CONCEPTO_2` varchar(100) ,
 `CONCEPTO_3` varchar(100) ,
 `LOGICO_1` tinyint(1) ,
 `LOGICO_2` tinyint(1) ,
 `ENTERO_1` int(11) ,
 `ENTERO_2` int(11) ,
 `DECIMAL_1` decimal(10,2) ,
 `DECIMAL_2` decimal(10,2) ,
 `FECHA_1` date ,
 `FECHA_2` date ,
 `TEXTO_1` text ,
 `TEXTO_2` text 
)*/;

/*Table structure for table `v_credito_solicitud_forma_liquidacion` */

DROP TABLE IF EXISTS `v_credito_solicitud_forma_liquidacion`;

/*!50001 DROP VIEW IF EXISTS `v_credito_solicitud_forma_liquidacion` */;
/*!50001 DROP TABLE IF EXISTS `v_credito_solicitud_forma_liquidacion` */;

/*!50001 CREATE TABLE  `v_credito_solicitud_forma_liquidacion`(
 `ID` varchar(12) ,
 `CONCEPTO_1` varchar(100) ,
 `CONCEPTO_2` varchar(100) ,
 `CONCEPTO_3` varchar(100) ,
 `LOGICO_1` tinyint(1) ,
 `LOGICO_2` tinyint(1) ,
 `ENTERO_1` int(11) ,
 `ENTERO_2` int(11) ,
 `DECIMAL_1` decimal(10,2) ,
 `DECIMAL_2` decimal(10,2) ,
 `FECHA_1` date ,
 `FECHA_2` date ,
 `TEXTO_1` text ,
 `TEXTO_2` text 
)*/;

/*Table structure for table `v_credito_solicitud_instruccion_pagos` */

DROP TABLE IF EXISTS `v_credito_solicitud_instruccion_pagos`;

/*!50001 DROP VIEW IF EXISTS `v_credito_solicitud_instruccion_pagos` */;
/*!50001 DROP TABLE IF EXISTS `v_credito_solicitud_instruccion_pagos` */;

/*!50001 CREATE TABLE  `v_credito_solicitud_instruccion_pagos`(
 `ID` int(11) ,
 `MUTUAL_PRODUCTO_SOLICITUD_ID` int(11) ,
 `A_LA_ORDEN_DE` varchar(255) ,
 `CONCEPTO` text ,
 `IMPORTE` decimal(10,2) 
)*/;

/*Table structure for table `v_credito_solicitudes` */

DROP TABLE IF EXISTS `v_credito_solicitudes`;

/*!50001 DROP VIEW IF EXISTS `v_credito_solicitudes` */;
/*!50001 DROP TABLE IF EXISTS `v_credito_solicitudes` */;

/*!50001 CREATE TABLE  `v_credito_solicitudes`(
 `ID` int(11) ,
 `PROVEEDOR_ID` int(11) ,
 `PROVEEDOR_PLAN_ID` int(11) ,
 `PERSONA_ID` int(11) ,
 `CLIENTE_ID` int(11) ,
 `PERSONA_BENEFICIO_ID` int(11) ,
 `APROBADA` tinyint(1) ,
 `ANULADA` tinyint(1) ,
 `FECHA` date ,
 `FECHA_PAGO` date ,
 `TIPO_ORDEN_DTO` varchar(12) ,
 `TIPO_PRODUCTO` varchar(12) ,
 `ESTADO` varchar(12) ,
 `IMPORTE_TOTAL` decimal(10,2) ,
 `CUOTAS` int(11) ,
 `IMPORTE_CUOTA` decimal(10,2) ,
 `IMPORTE_SOLICITADO` decimal(10,2) ,
 `IMPORTE_PERCIBIDO` decimal(10,2) ,
 `OBSERVACIONES` text ,
 `ORDEN_DESCUENTO_ID` int(11) ,
 `APROBADA_POR` varchar(50) ,
 `APROBADA_EL` date ,
 `VENDEDOR_ID` int(11) ,
 `VENDEDOR_REMITO_ID` int(11) ,
 `VENDEDOR_NOTIFICAR` tinyint(1) ,
 `EMITIDA_POR` varchar(50) ,
 `PERIODO_INI` varchar(6) ,
 `PRIMER_VTO_SOCIO` date ,
 `FORMA_PAGO` varchar(12) ,
 `ORDEN_DESCUENTO` varchar(24) 
)*/;

/*Table structure for table `v_empresas` */

DROP TABLE IF EXISTS `v_empresas`;

/*!50001 DROP VIEW IF EXISTS `v_empresas` */;
/*!50001 DROP TABLE IF EXISTS `v_empresas` */;

/*!50001 CREATE TABLE  `v_empresas`(
 `ID` varchar(12) ,
 `CONCEPTO_1` varchar(100) ,
 `CONCEPTO_2` varchar(100) ,
 `CONCEPTO_3` varchar(100) ,
 `LOGICO_1` tinyint(1) ,
 `LOGICO_2` tinyint(1) ,
 `ENTERO_1` int(11) ,
 `ENTERO_2` int(11) ,
 `DECIMAL_1` decimal(10,2) ,
 `DECIMAL_2` decimal(10,2) ,
 `FECHA_1` date ,
 `FECHA_2` date ,
 `TEXTO_1` text ,
 `TEXTO_2` text 
)*/;

/*Table structure for table `v_global_datos` */

DROP TABLE IF EXISTS `v_global_datos`;

/*!50001 DROP VIEW IF EXISTS `v_global_datos` */;
/*!50001 DROP TABLE IF EXISTS `v_global_datos` */;

/*!50001 CREATE TABLE  `v_global_datos`(
 `ID` varchar(12) ,
 `CONCEPTO_1` varchar(100) ,
 `CONCEPTO_2` varchar(100) ,
 `CONCEPTO_3` varchar(100) ,
 `LOGICO_1` tinyint(1) ,
 `LOGICO_2` tinyint(1) ,
 `ENTERO_1` int(11) ,
 `ENTERO_2` int(11) ,
 `DECIMAL_1` decimal(10,2) ,
 `DECIMAL_2` decimal(10,2) ,
 `FECHA_1` date ,
 `FECHA_2` date ,
 `TEXTO_1` text ,
 `TEXTO_2` text 
)*/;

/*Table structure for table `v_grupos` */

DROP TABLE IF EXISTS `v_grupos`;

/*!50001 DROP VIEW IF EXISTS `v_grupos` */;
/*!50001 DROP TABLE IF EXISTS `v_grupos` */;

/*!50001 CREATE TABLE  `v_grupos`(
 `ID` int(11) ,
 `NOMBRE` varchar(40) ,
 `ACTIVO` tinyint(1) 
)*/;

/*Table structure for table `v_localidades` */

DROP TABLE IF EXISTS `v_localidades`;

/*!50001 DROP VIEW IF EXISTS `v_localidades` */;
/*!50001 DROP TABLE IF EXISTS `v_localidades` */;

/*!50001 CREATE TABLE  `v_localidades`(
 `ID` int(11) ,
 `CP` varchar(4) ,
 `NOMBRE` varchar(150) ,
 `PROVINCIA_ID` int(11) ,
 `LETRA_PROVINCIA` varchar(1) 
)*/;

/*Table structure for table `v_organismos` */

DROP TABLE IF EXISTS `v_organismos`;

/*!50001 DROP VIEW IF EXISTS `v_organismos` */;
/*!50001 DROP TABLE IF EXISTS `v_organismos` */;

/*!50001 CREATE TABLE  `v_organismos`(
 `ID` varchar(12) ,
 `CONCEPTO_1` varchar(100) ,
 `CONCEPTO_2` varchar(100) ,
 `CONCEPTO_3` varchar(100) ,
 `LOGICO_1` tinyint(1) ,
 `LOGICO_2` tinyint(1) ,
 `ENTERO_1` int(11) ,
 `ENTERO_2` int(11) ,
 `DECIMAL_1` decimal(10,2) ,
 `DECIMAL_2` decimal(10,2) ,
 `FECHA_1` date ,
 `FECHA_2` date ,
 `TEXTO_1` text ,
 `TEXTO_2` text 
)*/;

/*Table structure for table `v_persona_beneficios` */

DROP TABLE IF EXISTS `v_persona_beneficios`;

/*!50001 DROP VIEW IF EXISTS `v_persona_beneficios` */;
/*!50001 DROP TABLE IF EXISTS `v_persona_beneficios` */;

/*!50001 CREATE TABLE  `v_persona_beneficios`(
 `ID` int(11) ,
 `PERSONA_ID` int(11) ,
 `CODIGO_ORGANISMO` varchar(12) ,
 `NRO_LEY` varchar(50) ,
 `TIPO` varchar(1) ,
 `NRO_BENEFICIO` varchar(50) ,
 `SUB_BENEFICIO` varchar(2) ,
 `NRO_LEGAJO` varchar(50) ,
 `FECHA_INGRESO` date ,
 `CODIGO_REPARTICION` varchar(11) ,
 `TURNO_PAGO` varchar(12) ,
 `CBU` varchar(23) ,
 `BANCO_ID` varchar(5) ,
 `NRO_SUCURSAL` varchar(5) ,
 `TIPO_CTA_BCO` varchar(4) ,
 `NRO_CTA_BANCO` varchar(50) ,
 `CODIGO_EMPRESA` varchar(12) ,
 `PRINCIPAL` tinyint(1) ,
 `ACTIVO` tinyint(1) ,
 `PORCENTAJE` decimal(10,2) ,
 `ACUERDO_DEBITO` decimal(10,2) ,
 `IMPORTE_MAX_REGISTRO_CBU` decimal(10,2) ,
 `CADENA` varchar(249) 
)*/;

/*Table structure for table `v_persona_domicilios` */

DROP TABLE IF EXISTS `v_persona_domicilios`;

/*!50001 DROP VIEW IF EXISTS `v_persona_domicilios` */;
/*!50001 DROP TABLE IF EXISTS `v_persona_domicilios` */;

/*!50001 CREATE TABLE  `v_persona_domicilios`(
 `ID` int(11) ,
 `CALLE` varchar(150) ,
 `NUMERO_CALLE` varchar(5) ,
 `PISO` varchar(5) ,
 `DPTO` varchar(5) ,
 `BARRIO` varchar(100) ,
 `LOCALIDAD_ID` int(11) ,
 `LOCALIDAD_DESC` varchar(150) ,
 `CODIGO_POSTAL` varchar(8) ,
 `PROVINCIA_ID` int(11) 
)*/;

/*Table structure for table `v_persona_estados_civil` */

DROP TABLE IF EXISTS `v_persona_estados_civil`;

/*!50001 DROP VIEW IF EXISTS `v_persona_estados_civil` */;
/*!50001 DROP TABLE IF EXISTS `v_persona_estados_civil` */;

/*!50001 CREATE TABLE  `v_persona_estados_civil`(
 `ID` varchar(12) ,
 `CONCEPTO_1` varchar(100) ,
 `CONCEPTO_2` varchar(100) ,
 `CONCEPTO_3` varchar(100) ,
 `LOGICO_1` tinyint(1) ,
 `LOGICO_2` tinyint(1) ,
 `ENTERO_1` int(11) ,
 `ENTERO_2` int(11) ,
 `DECIMAL_1` decimal(10,2) ,
 `DECIMAL_2` decimal(10,2) ,
 `FECHA_1` date ,
 `FECHA_2` date ,
 `TEXTO_1` text ,
 `TEXTO_2` text 
)*/;

/*Table structure for table `v_persona_tipo_documentos` */

DROP TABLE IF EXISTS `v_persona_tipo_documentos`;

/*!50001 DROP VIEW IF EXISTS `v_persona_tipo_documentos` */;
/*!50001 DROP TABLE IF EXISTS `v_persona_tipo_documentos` */;

/*!50001 CREATE TABLE  `v_persona_tipo_documentos`(
 `ID` varchar(12) ,
 `CONCEPTO_1` varchar(100) ,
 `CONCEPTO_2` varchar(100) ,
 `CONCEPTO_3` varchar(100) ,
 `LOGICO_1` tinyint(1) ,
 `LOGICO_2` tinyint(1) ,
 `ENTERO_1` int(11) ,
 `ENTERO_2` int(11) ,
 `DECIMAL_1` decimal(10,2) ,
 `DECIMAL_2` decimal(10,2) ,
 `FECHA_1` date ,
 `FECHA_2` date ,
 `TEXTO_1` text ,
 `TEXTO_2` text 
)*/;

/*Table structure for table `v_personas` */

DROP TABLE IF EXISTS `v_personas`;

/*!50001 DROP VIEW IF EXISTS `v_personas` */;
/*!50001 DROP TABLE IF EXISTS `v_personas` */;

/*!50001 CREATE TABLE  `v_personas`(
 `ID` int(11) ,
 `DOMICILIO_ID` int(11) ,
 `TIPO_DOCUMENTO` varchar(12) ,
 `DOCUMENTO` varchar(11) ,
 `APELLIDO` varchar(100) ,
 `NOMBRE` varchar(100) ,
 `FECHA_NACIMIENTO` date ,
 `FECHA_FALLECIMIENTO` date ,
 `FALLECIDA` tinyint(1) ,
 `SEXO` varchar(1) ,
 `ESTADO_CIVIL` varchar(12) ,
 `CALLE` varchar(150) ,
 `NUMERO_CALLE` varchar(5) ,
 `PISO` varchar(5) ,
 `DPTO` varchar(5) ,
 `BARRIO` varchar(100) ,
 `LOCALIDAD_ID` int(11) ,
 `LOCALIDAD_DESC` varchar(150) ,
 `CODIGO_POSTAL` varchar(8) ,
 `PROVINCIA_ID` int(11) ,
 `CUIT_CUIL` varchar(11) ,
 `NOMBRE_CONYUGE` varchar(150) ,
 `TELEFONO_FIJO` varchar(50) ,
 `TELEFONO_MOVIL` varchar(50) ,
 `TELEFONO_REFERENCIA` varchar(50) ,
 `PERSONA_REFERENCIA` varchar(100) ,
 `E_MAIL` varchar(100) ,
 `TIPO_VIVIENDA` varchar(12) ,
 `FILIAL` varchar(12) ,
 `USER_CREATED` varchar(50) ,
 `USER_MODIFIED` varchar(50) ,
 `CREATED` datetime ,
 `MODIFIED` datetime 
)*/;

/*Table structure for table `v_plan_condiciones` */

DROP TABLE IF EXISTS `v_plan_condiciones`;

/*!50001 DROP VIEW IF EXISTS `v_plan_condiciones` */;
/*!50001 DROP TABLE IF EXISTS `v_plan_condiciones` */;

/*!50001 CREATE TABLE  `v_plan_condiciones`(
 `ID` int(11) ,
 `PLAN_ID` int(11) ,
 `VIGENCIA` date ,
 `CAPITAL` decimal(10,2) ,
 `LIQUIDO` decimal(10,2) ,
 `CUOTAS` int(11) ,
 `IMPORTE` decimal(10,2) ,
 `TOTAL` decimal(20,2) 
)*/;

/*Table structure for table `v_plan_monto_cuotas` */

DROP TABLE IF EXISTS `v_plan_monto_cuotas`;

/*!50001 DROP VIEW IF EXISTS `v_plan_monto_cuotas` */;
/*!50001 DROP TABLE IF EXISTS `v_plan_monto_cuotas` */;

/*!50001 CREATE TABLE  `v_plan_monto_cuotas`(
 `ID` int(11) ,
 `PLAN_ID` int(11) ,
 `VIGENCIA` date ,
 `CAPITAL` decimal(10,2) ,
 `LIQUIDO` decimal(10,2) ,
 `CUOTAS` int(11) ,
 `IMPORTE` decimal(10,2) ,
 `TOTAL` decimal(20,2) 
)*/;

/*Table structure for table `v_plan_montos` */

DROP TABLE IF EXISTS `v_plan_montos`;

/*!50001 DROP VIEW IF EXISTS `v_plan_montos` */;
/*!50001 DROP TABLE IF EXISTS `v_plan_montos` */;

/*!50001 CREATE TABLE  `v_plan_montos`(
 `ID` int(11) ,
 `PLAN_ID` int(11) ,
 `VIGENCIA` date ,
 `LIQUIDO` decimal(10,2) 
)*/;

/*Table structure for table `v_plan_organismos` */

DROP TABLE IF EXISTS `v_plan_organismos`;

/*!50001 DROP VIEW IF EXISTS `v_plan_organismos` */;
/*!50001 DROP TABLE IF EXISTS `v_plan_organismos` */;

/*!50001 CREATE TABLE  `v_plan_organismos`(
 `PLAN_ID` int(11) ,
 `CODIGO_ORGANISMO` varchar(12) 
)*/;

/*Table structure for table `v_planes` */

DROP TABLE IF EXISTS `v_planes`;

/*!50001 DROP VIEW IF EXISTS `v_planes` */;
/*!50001 DROP TABLE IF EXISTS `v_planes` */;

/*!50001 CREATE TABLE  `v_planes`(
 `ID` int(11) ,
 `PROVEEDOR_ID` int(11) ,
 `PROVEEDOR` varchar(150) ,
 `DESCRIPCION_PLAN` varchar(100) ,
 `DESCRIPCION` varchar(157) ,
 `ACTIVO` tinyint(1) ,
 `REASIGNABLE` tinyint(1) ,
 `VENDEDORES` tinyint(1) ,
 `MONTO_MINIMO` decimal(10,2) ,
 `MONTO_MAXIMO` decimal(10,2) ,
 `CUOTAS_MINIMO` bigint(11) ,
 `CUOTAS_MAXIMO` bigint(11) ,
 `CUOTA_MONTO_MINIMO` decimal(10,2) ,
 `CUOTA_MONTO_MAXIMO` decimal(10,2) 
)*/;

/*Table structure for table `v_productos` */

DROP TABLE IF EXISTS `v_productos`;

/*!50001 DROP VIEW IF EXISTS `v_productos` */;
/*!50001 DROP TABLE IF EXISTS `v_productos` */;

/*!50001 CREATE TABLE  `v_productos`(
 `ID` varchar(12) ,
 `CONCEPTO_1` varchar(100) ,
 `CONCEPTO_2` varchar(100) ,
 `CONCEPTO_3` varchar(100) ,
 `LOGICO_1` tinyint(1) ,
 `LOGICO_2` tinyint(1) ,
 `ENTERO_1` int(11) ,
 `ENTERO_2` int(11) ,
 `DECIMAL_1` decimal(10,2) ,
 `DECIMAL_2` decimal(10,2) ,
 `FECHA_1` date ,
 `FECHA_2` date ,
 `TEXTO_1` text ,
 `TEXTO_2` text 
)*/;

/*Table structure for table `v_provincias` */

DROP TABLE IF EXISTS `v_provincias`;

/*!50001 DROP VIEW IF EXISTS `v_provincias` */;
/*!50001 DROP TABLE IF EXISTS `v_provincias` */;

/*!50001 CREATE TABLE  `v_provincias`(
 `ID` int(11) ,
 `NOMBRE` varchar(100) ,
 `LETRA` varchar(1) 
)*/;

/*Table structure for table `v_usuarios` */

DROP TABLE IF EXISTS `v_usuarios`;

/*!50001 DROP VIEW IF EXISTS `v_usuarios` */;
/*!50001 DROP TABLE IF EXISTS `v_usuarios` */;

/*!50001 CREATE TABLE  `v_usuarios`(
 `ID` int(11) ,
 `GRUPO_ID` int(11) ,
 `USUARIO` varchar(11) ,
 `PASSWORD` varchar(40) ,
 `ACTIVO` tinyint(1) ,
 `DESCRIPCION` varchar(100) ,
 `VENDEDOR_ID` int(11) 
)*/;

/*Table structure for table `v_vendedor_remitos` */

DROP TABLE IF EXISTS `v_vendedor_remitos`;

/*!50001 DROP VIEW IF EXISTS `v_vendedor_remitos` */;
/*!50001 DROP TABLE IF EXISTS `v_vendedor_remitos` */;

/*!50001 CREATE TABLE  `v_vendedor_remitos`(
 `ID` int(11) ,
 `VENDEDOR_ID` int(11) ,
 `OBSERVACIONES` text ,
 `USER_CREATED` varchar(50) ,
 `CREATED` datetime ,
 `ANULADO` tinyint(1) 
)*/;

/*Table structure for table `v_vendedores` */

DROP TABLE IF EXISTS `v_vendedores`;

/*!50001 DROP VIEW IF EXISTS `v_vendedores` */;
/*!50001 DROP TABLE IF EXISTS `v_vendedores` */;

/*!50001 CREATE TABLE  `v_vendedores`(
 `ID` int(11) ,
 `PERSONA_ID` int(11) ,
 `USUARIO_ID` int(11) ,
 `ACTIVO` tinyint(1) ,
 `NOTIFICACIONES` bigint(21) 
)*/;

/*View structure for view v_asincronos */

/*!50001 DROP TABLE IF EXISTS `v_asincronos` */;
/*!50001 DROP VIEW IF EXISTS `v_asincronos` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_asincronos` AS (select `asincronos`.`id` AS `ID`,`asincronos`.`shell_pid` AS `SHELL_PID`,`asincronos`.`propietario` AS `PROPIETARIO`,`asincronos`.`remote_ip` AS `REMOTE_IP`,`asincronos`.`final` AS `FINAL`,`asincronos`.`proceso` AS `PROCESO`,`asincronos`.`bloqueado` AS `BLOQUEADO`,`asincronos`.`p1` AS `P1`,`asincronos`.`p2` AS `P2`,`asincronos`.`p3` AS `P3`,`asincronos`.`p4` AS `P4`,`asincronos`.`p5` AS `P5`,`asincronos`.`p6` AS `P6`,`asincronos`.`p7` AS `P7`,`asincronos`.`p8` AS `P8`,`asincronos`.`p9` AS `P9`,`asincronos`.`p10` AS `P10`,`asincronos`.`p11` AS `P11`,`asincronos`.`p12` AS `P12`,`asincronos`.`p13` AS `P13`,`asincronos`.`txt1` AS `TXT1`,`asincronos`.`txt2` AS `TXT2`,`asincronos`.`action_do` AS `ACTION_DO`,`asincronos`.`target` AS `TARGET`,`asincronos`.`btn_label` AS `BTN_LABEL`,`asincronos`.`titulo` AS `TITULO`,`asincronos`.`subtitulo` AS `SUB_TITULO`,`asincronos`.`estado` AS `ESTADO`,`asincronos`.`total` AS `TOTAL`,`asincronos`.`contador` AS `CONTADOR`,`asincronos`.`porcentaje` AS `PORCENTAJE`,`asincronos`.`msg` AS `MSG`,`asincronos`.`errores` AS `ERRORES`,`asincronos`.`created` AS `CREATED`,`asincronos`.`modified` AS `MODIFIED` from `asincronos`) */;

/*View structure for view v_bancos */

/*!50001 DROP TABLE IF EXISTS `v_bancos` */;
/*!50001 DROP VIEW IF EXISTS `v_bancos` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_bancos` AS (select `bancos`.`id` AS `ID`,`bancos`.`nombre` AS `NOMBRE`,`bancos`.`activo` AS `ACTIVO`,`bancos`.`beneficio` AS `BENEFICIO` from `bancos`) */;

/*View structure for view v_cancelacion_ordenes */

/*!50001 DROP TABLE IF EXISTS `v_cancelacion_ordenes` */;
/*!50001 DROP VIEW IF EXISTS `v_cancelacion_ordenes` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_cancelacion_ordenes` AS (select `cancelacion_ordenes`.`id` AS `ID`,`cancelacion_ordenes`.`estado` AS `ESTADO`,if((`cancelacion_ordenes`.`estado` = 'E'),'EMITIDA','PROCESADA') AS `ESTADO_DESC`,`cancelacion_ordenes`.`socio_id` AS `CLIENTE_ID`,`cancelacion_ordenes`.`importe_proveedor` AS `IMPORTE_CANCELA`,`cancelacion_ordenes`.`importe_seleccionado` AS `IMPORTE_SELECCIONADO`,`cancelacion_ordenes`.`saldo_orden_dto` AS `SALDO_ORDEN`,`cancelacion_ordenes`.`importe_cuota` AS `IMPORTE_CUOTA`,`cancelacion_ordenes`.`importe_diferencia` AS `DEBITO_CREDITO`,((`cancelacion_ordenes`.`saldo_orden_dto` - `cancelacion_ordenes`.`importe_proveedor`) + `cancelacion_ordenes`.`importe_diferencia`) AS `SALDO`,`cancelacion_ordenes`.`fecha_vto` AS `VENCIMIENTO`,if((`cancelacion_ordenes`.`tipo_cancelacion` = 'T'),'TOTAL','PARCIAL') AS `TIPO`,`cancelacion_ordenes`.`observaciones` AS `OBSERVACIONES`,`cancelacion_ordenes`.`concepto` AS `CONCEPTO`,(select count(0) from `cancelacion_orden_cuotas` where (`cancelacion_orden_cuotas`.`cancelacion_orden_id` = `cancelacion_ordenes`.`id`)) AS `CANTIDAD_CUOTAS`,`proveedores`.`id` AS `PROVEEDOR_ID`,ucase(`proveedores`.`razon_social`) AS `A_LA_ORDEN_DE`,`mutual_producto_solicitudes`.`id` AS `SOLICITUD_CREDITO_ID` from (((`cancelacion_ordenes` join `proveedores` on((`proveedores`.`id` = `cancelacion_ordenes`.`orden_proveedor_id`))) left join `orden_descuentos` on((`orden_descuentos`.`id` = `cancelacion_ordenes`.`orden_descuento_id`))) left join `mutual_producto_solicitudes` on(((`mutual_producto_solicitudes`.`id` = `orden_descuentos`.`numero`) and (`mutual_producto_solicitudes`.`tipo_orden_dto` = `orden_descuentos`.`tipo_orden_dto`))))) */;

/*View structure for view v_cliente_calificaciones */

/*!50001 DROP TABLE IF EXISTS `v_cliente_calificaciones` */;
/*!50001 DROP VIEW IF EXISTS `v_cliente_calificaciones` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_cliente_calificaciones` AS (select `v_global_datos`.`ID` AS `ID`,`v_global_datos`.`CONCEPTO_1` AS `CONCEPTO_1`,`v_global_datos`.`CONCEPTO_2` AS `CONCEPTO_2`,`v_global_datos`.`CONCEPTO_3` AS `CONCEPTO_3`,`v_global_datos`.`LOGICO_1` AS `LOGICO_1`,`v_global_datos`.`LOGICO_2` AS `LOGICO_2`,`v_global_datos`.`ENTERO_1` AS `ENTERO_1`,`v_global_datos`.`ENTERO_2` AS `ENTERO_2`,`v_global_datos`.`DECIMAL_1` AS `DECIMAL_1`,`v_global_datos`.`DECIMAL_2` AS `DECIMAL_2`,`v_global_datos`.`FECHA_1` AS `FECHA_1`,`v_global_datos`.`FECHA_2` AS `FECHA_2`,`v_global_datos`.`TEXTO_1` AS `TEXTO_1`,`v_global_datos`.`TEXTO_2` AS `TEXTO_2` from `v_global_datos` where ((`v_global_datos`.`ID` like 'MUTUCALI%') and (`v_global_datos`.`ID` <> 'MUTUCALI')) order by `v_global_datos`.`ENTERO_1`,`v_global_datos`.`CONCEPTO_1`) */;

/*View structure for view v_cliente_cancelaciones_emitidas */

/*!50001 DROP TABLE IF EXISTS `v_cliente_cancelaciones_emitidas` */;
/*!50001 DROP VIEW IF EXISTS `v_cliente_cancelaciones_emitidas` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_cliente_cancelaciones_emitidas` AS (select `v_clientes`.`ID` AS `CLIENTE_ID`,`v_cancelacion_ordenes`.`ID` AS `CANCELACION_ID` from (`v_clientes` join `v_cancelacion_ordenes` on((`v_cancelacion_ordenes`.`CLIENTE_ID` = `v_clientes`.`ID`))) where ((`v_cancelacion_ordenes`.`ESTADO` = 'E') and (`v_cancelacion_ordenes`.`VENCIMIENTO` >= curdate()) and (not(`v_cancelacion_ordenes`.`ID` in (select `v_credito_solcitud_cancelaciones`.`CANCELACION_ORDEN_ID` from `v_credito_solcitud_cancelaciones`))))) */;

/*View structure for view v_clientes */

/*!50001 DROP TABLE IF EXISTS `v_clientes` */;
/*!50001 DROP VIEW IF EXISTS `v_clientes` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_clientes` AS (select `socios`.`id` AS `ID`,`socios`.`categoria` AS `CATEGORIA`,`socios`.`persona_id` AS `PERSONA_ID`,`socios`.`persona_beneficio_id` AS `BENEFICIO_ID`,`socios`.`activo` AS `ACTIVO`,`socios`.`fecha_alta` AS `FECHA_ALTA`,`socios`.`orden_descuento_id` AS `ORDEN_DESCUENTO_ID`,`socios`.`calificacion` AS `CALIFICACION`,`socios`.`fecha_calificacion` AS `FECHA_CALIFICACION`,`socios`.`codigo_baja` AS `CODIGO_BAJA`,`socios`.`fecha_baja` AS `FECHA_BAJA`,`socios`.`observaciones` AS `OBSERVACIONES` from `socios`) */;

/*View structure for view v_credito_solcitud_cancelaciones */

/*!50001 DROP TABLE IF EXISTS `v_credito_solcitud_cancelaciones` */;
/*!50001 DROP VIEW IF EXISTS `v_credito_solcitud_cancelaciones` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_credito_solcitud_cancelaciones` AS (select `mutual_producto_solicitud_cancelaciones`.`mutual_producto_solicitud_id` AS `MUTUAL_PRODUCTO_SOLICITUD_ID`,`mutual_producto_solicitud_cancelaciones`.`cancelacion_orden_id` AS `CANCELACION_ORDEN_ID` from `mutual_producto_solicitud_cancelaciones`) */;

/*View structure for view v_credito_solicitud_estados */

/*!50001 DROP TABLE IF EXISTS `v_credito_solicitud_estados` */;
/*!50001 DROP VIEW IF EXISTS `v_credito_solicitud_estados` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_credito_solicitud_estados` AS (select `v_global_datos`.`ID` AS `ID`,`v_global_datos`.`CONCEPTO_1` AS `CONCEPTO_1`,`v_global_datos`.`CONCEPTO_2` AS `CONCEPTO_2`,`v_global_datos`.`CONCEPTO_3` AS `CONCEPTO_3`,`v_global_datos`.`LOGICO_1` AS `LOGICO_1`,`v_global_datos`.`LOGICO_2` AS `LOGICO_2`,`v_global_datos`.`ENTERO_1` AS `ENTERO_1`,`v_global_datos`.`ENTERO_2` AS `ENTERO_2`,`v_global_datos`.`DECIMAL_1` AS `DECIMAL_1`,`v_global_datos`.`DECIMAL_2` AS `DECIMAL_2`,`v_global_datos`.`FECHA_1` AS `FECHA_1`,`v_global_datos`.`FECHA_2` AS `FECHA_2`,`v_global_datos`.`TEXTO_1` AS `TEXTO_1`,`v_global_datos`.`TEXTO_2` AS `TEXTO_2` from `v_global_datos` where ((`v_global_datos`.`ID` like 'MUTUESTA%') and (`v_global_datos`.`ID` <> 'MUTUESTA')) order by `v_global_datos`.`ENTERO_1`,`v_global_datos`.`CONCEPTO_1`) */;

/*View structure for view v_credito_solicitud_forma_liquidacion */

/*!50001 DROP TABLE IF EXISTS `v_credito_solicitud_forma_liquidacion` */;
/*!50001 DROP VIEW IF EXISTS `v_credito_solicitud_forma_liquidacion` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_credito_solicitud_forma_liquidacion` AS (select `v_global_datos`.`ID` AS `ID`,`v_global_datos`.`CONCEPTO_1` AS `CONCEPTO_1`,`v_global_datos`.`CONCEPTO_2` AS `CONCEPTO_2`,`v_global_datos`.`CONCEPTO_3` AS `CONCEPTO_3`,`v_global_datos`.`LOGICO_1` AS `LOGICO_1`,`v_global_datos`.`LOGICO_2` AS `LOGICO_2`,`v_global_datos`.`ENTERO_1` AS `ENTERO_1`,`v_global_datos`.`ENTERO_2` AS `ENTERO_2`,`v_global_datos`.`DECIMAL_1` AS `DECIMAL_1`,`v_global_datos`.`DECIMAL_2` AS `DECIMAL_2`,`v_global_datos`.`FECHA_1` AS `FECHA_1`,`v_global_datos`.`FECHA_2` AS `FECHA_2`,`v_global_datos`.`TEXTO_1` AS `TEXTO_1`,`v_global_datos`.`TEXTO_2` AS `TEXTO_2` from `v_global_datos` where ((`v_global_datos`.`ID` like 'MUTUFPAG%') and (`v_global_datos`.`ID` <> 'MUTUFPAG')) order by `v_global_datos`.`CONCEPTO_1`) */;

/*View structure for view v_credito_solicitud_instruccion_pagos */

/*!50001 DROP TABLE IF EXISTS `v_credito_solicitud_instruccion_pagos` */;
/*!50001 DROP VIEW IF EXISTS `v_credito_solicitud_instruccion_pagos` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_credito_solicitud_instruccion_pagos` AS (select `mutual_producto_solicitud_instruccion_pagos`.`id` AS `ID`,`mutual_producto_solicitud_instruccion_pagos`.`mutual_producto_solicitud_id` AS `MUTUAL_PRODUCTO_SOLICITUD_ID`,`mutual_producto_solicitud_instruccion_pagos`.`a_la_orden_de` AS `A_LA_ORDEN_DE`,`mutual_producto_solicitud_instruccion_pagos`.`concepto` AS `CONCEPTO`,`mutual_producto_solicitud_instruccion_pagos`.`importe` AS `IMPORTE` from `mutual_producto_solicitud_instruccion_pagos`) */;

/*View structure for view v_credito_solicitudes */

/*!50001 DROP TABLE IF EXISTS `v_credito_solicitudes` */;
/*!50001 DROP VIEW IF EXISTS `v_credito_solicitudes` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_credito_solicitudes` AS (select `mutual_producto_solicitudes`.`id` AS `ID`,`mutual_producto_solicitudes`.`proveedor_id` AS `PROVEEDOR_ID`,`mutual_producto_solicitudes`.`proveedor_plan_id` AS `PROVEEDOR_PLAN_ID`,`mutual_producto_solicitudes`.`persona_id` AS `PERSONA_ID`,`mutual_producto_solicitudes`.`socio_id` AS `CLIENTE_ID`,`mutual_producto_solicitudes`.`persona_beneficio_id` AS `PERSONA_BENEFICIO_ID`,`mutual_producto_solicitudes`.`aprobada` AS `APROBADA`,`mutual_producto_solicitudes`.`anulada` AS `ANULADA`,`mutual_producto_solicitudes`.`fecha` AS `FECHA`,`mutual_producto_solicitudes`.`fecha_pago` AS `FECHA_PAGO`,`mutual_producto_solicitudes`.`tipo_orden_dto` AS `TIPO_ORDEN_DTO`,`mutual_producto_solicitudes`.`tipo_producto` AS `TIPO_PRODUCTO`,if(((`mutual_producto_solicitudes`.`anulada` = 1) and (`mutual_producto_solicitudes`.`estado` = 'MUTUESTA0001')),'MUTUESTA0000',`mutual_producto_solicitudes`.`estado`) AS `ESTADO`,`mutual_producto_solicitudes`.`importe_total` AS `IMPORTE_TOTAL`,`mutual_producto_solicitudes`.`cuotas` AS `CUOTAS`,`mutual_producto_solicitudes`.`importe_cuota` AS `IMPORTE_CUOTA`,`mutual_producto_solicitudes`.`importe_solicitado` AS `IMPORTE_SOLICITADO`,`mutual_producto_solicitudes`.`importe_percibido` AS `IMPORTE_PERCIBIDO`,`mutual_producto_solicitudes`.`observaciones` AS `OBSERVACIONES`,`mutual_producto_solicitudes`.`orden_descuento_id` AS `ORDEN_DESCUENTO_ID`,`mutual_producto_solicitudes`.`aprobada_por` AS `APROBADA_POR`,`mutual_producto_solicitudes`.`aprobada_el` AS `APROBADA_EL`,`mutual_producto_solicitudes`.`vendedor_id` AS `VENDEDOR_ID`,`mutual_producto_solicitudes`.`vendedor_remito_id` AS `VENDEDOR_REMITO_ID`,`mutual_producto_solicitudes`.`vendedor_notificar` AS `VENDEDOR_NOTIFICAR`,`mutual_producto_solicitudes`.`user_created` AS `EMITIDA_POR`,`mutual_producto_solicitudes`.`periodo_ini` AS `PERIODO_INI`,`mutual_producto_solicitudes`.`primer_vto_socio` AS `PRIMER_VTO_SOCIO`,`mutual_producto_solicitudes`.`forma_pago` AS `FORMA_PAGO`,if(((`mutual_producto_solicitudes`.`aprobada` = 1) and (`mutual_producto_solicitudes`.`anulada` = 0)),concat(`mutual_producto_solicitudes`.`tipo_orden_dto`,' ',`mutual_producto_solicitudes`.`orden_descuento_id`),'') AS `ORDEN_DESCUENTO` from `mutual_producto_solicitudes` where (`mutual_producto_solicitudes`.`tipo_producto` = 'MUTUPROD0001')) */;

/*View structure for view v_empresas */

/*!50001 DROP TABLE IF EXISTS `v_empresas` */;
/*!50001 DROP VIEW IF EXISTS `v_empresas` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_empresas` AS (select `v_global_datos`.`ID` AS `ID`,`v_global_datos`.`CONCEPTO_1` AS `CONCEPTO_1`,`v_global_datos`.`CONCEPTO_2` AS `CONCEPTO_2`,`v_global_datos`.`CONCEPTO_3` AS `CONCEPTO_3`,`v_global_datos`.`LOGICO_1` AS `LOGICO_1`,`v_global_datos`.`LOGICO_2` AS `LOGICO_2`,`v_global_datos`.`ENTERO_1` AS `ENTERO_1`,`v_global_datos`.`ENTERO_2` AS `ENTERO_2`,`v_global_datos`.`DECIMAL_1` AS `DECIMAL_1`,`v_global_datos`.`DECIMAL_2` AS `DECIMAL_2`,`v_global_datos`.`FECHA_1` AS `FECHA_1`,`v_global_datos`.`FECHA_2` AS `FECHA_2`,`v_global_datos`.`TEXTO_1` AS `TEXTO_1`,`v_global_datos`.`TEXTO_2` AS `TEXTO_2` from `v_global_datos` where ((`v_global_datos`.`ID` like 'MUTUEMPR%') and (`v_global_datos`.`ID` <> 'MUTUEMPR')) order by `v_global_datos`.`ENTERO_1`,`v_global_datos`.`CONCEPTO_1`) */;

/*View structure for view v_global_datos */

/*!50001 DROP TABLE IF EXISTS `v_global_datos` */;
/*!50001 DROP VIEW IF EXISTS `v_global_datos` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_global_datos` AS (select `global_datos`.`id` AS `ID`,`global_datos`.`concepto_1` AS `CONCEPTO_1`,`global_datos`.`concepto_2` AS `CONCEPTO_2`,`global_datos`.`concepto_3` AS `CONCEPTO_3`,`global_datos`.`logico_1` AS `LOGICO_1`,`global_datos`.`logico_2` AS `LOGICO_2`,`global_datos`.`entero_1` AS `ENTERO_1`,`global_datos`.`entero_2` AS `ENTERO_2`,`global_datos`.`decimal_1` AS `DECIMAL_1`,`global_datos`.`decimal_2` AS `DECIMAL_2`,`global_datos`.`fecha_1` AS `FECHA_1`,`global_datos`.`fecha_2` AS `FECHA_2`,`global_datos`.`texto_1` AS `TEXTO_1`,`global_datos`.`texto_2` AS `TEXTO_2` from `global_datos`) */;

/*View structure for view v_grupos */

/*!50001 DROP TABLE IF EXISTS `v_grupos` */;
/*!50001 DROP VIEW IF EXISTS `v_grupos` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_grupos` AS (select `grupos`.`id` AS `ID`,`grupos`.`nombre` AS `NOMBRE`,`grupos`.`activo` AS `ACTIVO` from `grupos`) */;

/*View structure for view v_localidades */

/*!50001 DROP TABLE IF EXISTS `v_localidades` */;
/*!50001 DROP VIEW IF EXISTS `v_localidades` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_localidades` AS (select `localidades`.`id` AS `ID`,`localidades`.`cp` AS `CP`,`localidades`.`nombre` AS `NOMBRE`,`localidades`.`provincia_id` AS `PROVINCIA_ID`,`localidades`.`letra_provincia` AS `LETRA_PROVINCIA` from `localidades` where (`localidades`.`provincia_id` is not null)) */;

/*View structure for view v_organismos */

/*!50001 DROP TABLE IF EXISTS `v_organismos` */;
/*!50001 DROP VIEW IF EXISTS `v_organismos` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_organismos` AS (select `v_global_datos`.`ID` AS `ID`,`v_global_datos`.`CONCEPTO_1` AS `CONCEPTO_1`,`v_global_datos`.`CONCEPTO_2` AS `CONCEPTO_2`,`v_global_datos`.`CONCEPTO_3` AS `CONCEPTO_3`,`v_global_datos`.`LOGICO_1` AS `LOGICO_1`,`v_global_datos`.`LOGICO_2` AS `LOGICO_2`,`v_global_datos`.`ENTERO_1` AS `ENTERO_1`,`v_global_datos`.`ENTERO_2` AS `ENTERO_2`,`v_global_datos`.`DECIMAL_1` AS `DECIMAL_1`,`v_global_datos`.`DECIMAL_2` AS `DECIMAL_2`,`v_global_datos`.`FECHA_1` AS `FECHA_1`,`v_global_datos`.`FECHA_2` AS `FECHA_2`,`v_global_datos`.`TEXTO_1` AS `TEXTO_1`,`v_global_datos`.`TEXTO_2` AS `TEXTO_2` from `v_global_datos` where ((`v_global_datos`.`ID` like 'MUTUCORG%') and (`v_global_datos`.`ID` <> 'MUTUCORG')) order by `v_global_datos`.`CONCEPTO_1`) */;

/*View structure for view v_persona_beneficios */

/*!50001 DROP TABLE IF EXISTS `v_persona_beneficios` */;
/*!50001 DROP VIEW IF EXISTS `v_persona_beneficios` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_persona_beneficios` AS (select `persona_beneficios`.`id` AS `ID`,`persona_beneficios`.`persona_id` AS `PERSONA_ID`,`persona_beneficios`.`codigo_beneficio` AS `CODIGO_ORGANISMO`,`persona_beneficios`.`nro_ley` AS `NRO_LEY`,`persona_beneficios`.`tipo` AS `TIPO`,`persona_beneficios`.`nro_beneficio` AS `NRO_BENEFICIO`,`persona_beneficios`.`sub_beneficio` AS `SUB_BENEFICIO`,`persona_beneficios`.`nro_legajo` AS `NRO_LEGAJO`,`persona_beneficios`.`fecha_ingreso` AS `FECHA_INGRESO`,`persona_beneficios`.`codigo_reparticion` AS `CODIGO_REPARTICION`,`persona_beneficios`.`turno_pago` AS `TURNO_PAGO`,`persona_beneficios`.`cbu` AS `CBU`,`persona_beneficios`.`banco_id` AS `BANCO_ID`,`persona_beneficios`.`nro_sucursal` AS `NRO_SUCURSAL`,`persona_beneficios`.`tipo_cta_bco` AS `TIPO_CTA_BCO`,`persona_beneficios`.`nro_cta_bco` AS `NRO_CTA_BANCO`,`persona_beneficios`.`codigo_empresa` AS `CODIGO_EMPRESA`,`persona_beneficios`.`principal` AS `PRINCIPAL`,`persona_beneficios`.`activo` AS `ACTIVO`,`persona_beneficios`.`porcentaje` AS `PORCENTAJE`,`persona_beneficios`.`acuerdo_debito` AS `ACUERDO_DEBITO`,`persona_beneficios`.`importe_max_registro_cbu` AS `IMPORTE_MAX_REGISTRO_CBU`,concat(`gl1`.`concepto_1`,' - ',`gl2`.`concepto_1`,' | ',if((`persona_beneficios`.`codigo_empresa` = 'MUTUEMPRP001'),`persona_beneficios`.`turno_pago`,''),' | CBU: ',`persona_beneficios`.`cbu`) AS `CADENA` from ((`persona_beneficios` join `global_datos` `gl1` on((`gl1`.`id` = `persona_beneficios`.`codigo_beneficio`))) join `global_datos` `gl2` on((`gl2`.`id` = `persona_beneficios`.`codigo_empresa`))) where (`persona_beneficios`.`activo` = 1)) */;

/*View structure for view v_persona_domicilios */

/*!50001 DROP TABLE IF EXISTS `v_persona_domicilios` */;
/*!50001 DROP VIEW IF EXISTS `v_persona_domicilios` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_persona_domicilios` AS (select `personas`.`id` AS `ID`,`personas`.`calle` AS `CALLE`,`personas`.`numero_calle` AS `NUMERO_CALLE`,`personas`.`piso` AS `PISO`,`personas`.`dpto` AS `DPTO`,`personas`.`barrio` AS `BARRIO`,`personas`.`localidad_id` AS `LOCALIDAD_ID`,`personas`.`localidad` AS `LOCALIDAD_DESC`,`personas`.`codigo_postal` AS `CODIGO_POSTAL`,`personas`.`provincia_id` AS `PROVINCIA_ID` from `personas`) */;

/*View structure for view v_persona_estados_civil */

/*!50001 DROP TABLE IF EXISTS `v_persona_estados_civil` */;
/*!50001 DROP VIEW IF EXISTS `v_persona_estados_civil` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_persona_estados_civil` AS (select `v_global_datos`.`ID` AS `ID`,`v_global_datos`.`CONCEPTO_1` AS `CONCEPTO_1`,`v_global_datos`.`CONCEPTO_2` AS `CONCEPTO_2`,`v_global_datos`.`CONCEPTO_3` AS `CONCEPTO_3`,`v_global_datos`.`LOGICO_1` AS `LOGICO_1`,`v_global_datos`.`LOGICO_2` AS `LOGICO_2`,`v_global_datos`.`ENTERO_1` AS `ENTERO_1`,`v_global_datos`.`ENTERO_2` AS `ENTERO_2`,`v_global_datos`.`DECIMAL_1` AS `DECIMAL_1`,`v_global_datos`.`DECIMAL_2` AS `DECIMAL_2`,`v_global_datos`.`FECHA_1` AS `FECHA_1`,`v_global_datos`.`FECHA_2` AS `FECHA_2`,`v_global_datos`.`TEXTO_1` AS `TEXTO_1`,`v_global_datos`.`TEXTO_2` AS `TEXTO_2` from `v_global_datos` where ((`v_global_datos`.`ID` like 'PERSXXEC%') and (`v_global_datos`.`ID` <> 'PERSXXEC'))) */;

/*View structure for view v_persona_tipo_documentos */

/*!50001 DROP TABLE IF EXISTS `v_persona_tipo_documentos` */;
/*!50001 DROP VIEW IF EXISTS `v_persona_tipo_documentos` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_persona_tipo_documentos` AS (select `v_global_datos`.`ID` AS `ID`,`v_global_datos`.`CONCEPTO_1` AS `CONCEPTO_1`,`v_global_datos`.`CONCEPTO_2` AS `CONCEPTO_2`,`v_global_datos`.`CONCEPTO_3` AS `CONCEPTO_3`,`v_global_datos`.`LOGICO_1` AS `LOGICO_1`,`v_global_datos`.`LOGICO_2` AS `LOGICO_2`,`v_global_datos`.`ENTERO_1` AS `ENTERO_1`,`v_global_datos`.`ENTERO_2` AS `ENTERO_2`,`v_global_datos`.`DECIMAL_1` AS `DECIMAL_1`,`v_global_datos`.`DECIMAL_2` AS `DECIMAL_2`,`v_global_datos`.`FECHA_1` AS `FECHA_1`,`v_global_datos`.`FECHA_2` AS `FECHA_2`,`v_global_datos`.`TEXTO_1` AS `TEXTO_1`,`v_global_datos`.`TEXTO_2` AS `TEXTO_2` from `v_global_datos` where ((`v_global_datos`.`ID` like 'PERSTPDC%') and (`v_global_datos`.`ID` <> 'PERSTPDC'))) */;

/*View structure for view v_personas */

/*!50001 DROP TABLE IF EXISTS `v_personas` */;
/*!50001 DROP VIEW IF EXISTS `v_personas` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_personas` AS (select `personas`.`id` AS `ID`,`personas`.`id` AS `DOMICILIO_ID`,`personas`.`tipo_documento` AS `TIPO_DOCUMENTO`,`personas`.`documento` AS `DOCUMENTO`,`personas`.`apellido` AS `APELLIDO`,`personas`.`nombre` AS `NOMBRE`,`personas`.`fecha_nacimiento` AS `FECHA_NACIMIENTO`,`personas`.`fecha_fallecimiento` AS `FECHA_FALLECIMIENTO`,`personas`.`fallecida` AS `FALLECIDA`,`personas`.`sexo` AS `SEXO`,`personas`.`estado_civil` AS `ESTADO_CIVIL`,`personas`.`calle` AS `CALLE`,`personas`.`numero_calle` AS `NUMERO_CALLE`,`personas`.`piso` AS `PISO`,`personas`.`dpto` AS `DPTO`,`personas`.`barrio` AS `BARRIO`,`personas`.`localidad_id` AS `LOCALIDAD_ID`,`personas`.`localidad` AS `LOCALIDAD_DESC`,`personas`.`codigo_postal` AS `CODIGO_POSTAL`,`personas`.`provincia_id` AS `PROVINCIA_ID`,`personas`.`cuit_cuil` AS `CUIT_CUIL`,`personas`.`nombre_conyuge` AS `NOMBRE_CONYUGE`,`personas`.`telefono_fijo` AS `TELEFONO_FIJO`,`personas`.`telefono_movil` AS `TELEFONO_MOVIL`,`personas`.`telefono_referencia` AS `TELEFONO_REFERENCIA`,`personas`.`persona_referencia` AS `PERSONA_REFERENCIA`,`personas`.`e_mail` AS `E_MAIL`,`personas`.`tipo_vivienda` AS `TIPO_VIVIENDA`,`personas`.`filial` AS `FILIAL`,`personas`.`user_created` AS `USER_CREATED`,`personas`.`user_modified` AS `USER_MODIFIED`,`personas`.`created` AS `CREATED`,`personas`.`modified` AS `MODIFIED` from `personas`) */;

/*View structure for view v_plan_condiciones */

/*!50001 DROP TABLE IF EXISTS `v_plan_condiciones` */;
/*!50001 DROP VIEW IF EXISTS `v_plan_condiciones` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_plan_condiciones` AS (select `ppgc`.`id` AS `ID`,`pp`.`ID` AS `PLAN_ID`,`ppg`.`vigencia_desde` AS `VIGENCIA`,`ppgc`.`capital` AS `CAPITAL`,`ppgc`.`liquido` AS `LIQUIDO`,`ppgc`.`cuotas` AS `CUOTAS`,`ppgc`.`importe` AS `IMPORTE`,(`ppgc`.`importe` * `ppgc`.`cuotas`) AS `TOTAL` from ((`v_planes` `pp` join `proveedor_plan_grillas` `ppg`) join `proveedor_plan_grilla_cuotas` `ppgc`) where ((`pp`.`ID` = `ppg`.`proveedor_plan_id`) and (`ppg`.`id` = `ppgc`.`proveedor_plan_grilla_id`) and (`ppg`.`vigencia_desde` < now()))) */;

/*View structure for view v_plan_monto_cuotas */

/*!50001 DROP TABLE IF EXISTS `v_plan_monto_cuotas` */;
/*!50001 DROP VIEW IF EXISTS `v_plan_monto_cuotas` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_plan_monto_cuotas` AS (select `proveedor_plan_grilla_cuotas`.`id` AS `ID`,`proveedor_planes`.`id` AS `PLAN_ID`,`proveedor_plan_grillas`.`vigencia_desde` AS `VIGENCIA`,`proveedor_plan_grilla_cuotas`.`capital` AS `CAPITAL`,`proveedor_plan_grilla_cuotas`.`liquido` AS `LIQUIDO`,`proveedor_plan_grilla_cuotas`.`cuotas` AS `CUOTAS`,`proveedor_plan_grilla_cuotas`.`importe` AS `IMPORTE`,(`proveedor_plan_grilla_cuotas`.`importe` * `proveedor_plan_grilla_cuotas`.`cuotas`) AS `TOTAL` from ((`proveedor_planes` join `proveedor_plan_grillas`) join `proveedor_plan_grilla_cuotas`) where ((`proveedor_planes`.`id` = `proveedor_plan_grillas`.`proveedor_plan_id`) and (`proveedor_plan_grillas`.`id` = (select `grillas`.`id` from `proveedor_plan_grillas` `grillas` where ((`grillas`.`proveedor_plan_id` = `proveedor_planes`.`id`) and (`grillas`.`vigencia_desde` <= curdate())) order by `grillas`.`vigencia_desde` desc limit 1)) and (`proveedor_plan_grilla_cuotas`.`proveedor_plan_grilla_id` = `proveedor_plan_grillas`.`id`)) group by `proveedor_planes`.`id`,`proveedor_plan_grillas`.`vigencia_desde`,`proveedor_plan_grilla_cuotas`.`liquido`,`proveedor_plan_grilla_cuotas`.`cuotas` order by `proveedor_planes`.`id`,`proveedor_plan_grilla_cuotas`.`liquido`,`proveedor_plan_grilla_cuotas`.`cuotas`) */;

/*View structure for view v_plan_montos */

/*!50001 DROP TABLE IF EXISTS `v_plan_montos` */;
/*!50001 DROP VIEW IF EXISTS `v_plan_montos` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_plan_montos` AS (select `proveedor_plan_grilla_cuotas`.`id` AS `ID`,`proveedor_planes`.`id` AS `PLAN_ID`,`proveedor_plan_grillas`.`vigencia_desde` AS `VIGENCIA`,`proveedor_plan_grilla_cuotas`.`liquido` AS `LIQUIDO` from ((`proveedor_planes` join `proveedor_plan_grillas`) join `proveedor_plan_grilla_cuotas`) where ((`proveedor_planes`.`id` = `proveedor_plan_grillas`.`proveedor_plan_id`) and (`proveedor_plan_grillas`.`id` = (select `grillas`.`id` from `proveedor_plan_grillas` `grillas` where ((`grillas`.`proveedor_plan_id` = `proveedor_planes`.`id`) and (`grillas`.`vigencia_desde` <= curdate())) order by `grillas`.`vigencia_desde` desc limit 1)) and (`proveedor_plan_grilla_cuotas`.`proveedor_plan_grilla_id` = `proveedor_plan_grillas`.`id`)) group by `proveedor_planes`.`id`,`proveedor_plan_grillas`.`vigencia_desde`,`proveedor_plan_grilla_cuotas`.`liquido` order by `proveedor_planes`.`id`,`proveedor_plan_grilla_cuotas`.`liquido`) */;

/*View structure for view v_plan_organismos */

/*!50001 DROP TABLE IF EXISTS `v_plan_organismos` */;
/*!50001 DROP VIEW IF EXISTS `v_plan_organismos` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_plan_organismos` AS (select `proveedor_plan_organismos`.`proveedor_plan_id` AS `PLAN_ID`,`proveedor_plan_organismos`.`codigo_organismo` AS `CODIGO_ORGANISMO` from `proveedor_plan_organismos`) */;

/*View structure for view v_planes */

/*!50001 DROP TABLE IF EXISTS `v_planes` */;
/*!50001 DROP VIEW IF EXISTS `v_planes` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_planes` AS (select `pp`.`id` AS `ID`,`pp`.`proveedor_id` AS `PROVEEDOR_ID`,`p`.`razon_social` AS `PROVEEDOR`,`pp`.`descripcion` AS `DESCRIPCION_PLAN`,concat(`p`.`razon_social_resumida`,' ** ',`pp`.`descripcion`,' **') AS `DESCRIPCION`,`pp`.`activo` AS `ACTIVO`,`p`.`reasignable` AS `REASIGNABLE`,`p`.`vendedores` AS `VENDEDORES`,ifnull((select min(`v_plan_montos`.`LIQUIDO`) from `v_plan_montos` where (`v_plan_montos`.`PLAN_ID` = `pp`.`id`)),0) AS `MONTO_MINIMO`,ifnull((select max(`v_plan_montos`.`LIQUIDO`) from `v_plan_montos` where (`v_plan_montos`.`PLAN_ID` = `pp`.`id`)),0) AS `MONTO_MAXIMO`,ifnull((select min(`v_plan_monto_cuotas`.`CUOTAS`) from `v_plan_monto_cuotas` where (`v_plan_monto_cuotas`.`PLAN_ID` = `pp`.`id`)),0) AS `CUOTAS_MINIMO`,ifnull((select max(`v_plan_monto_cuotas`.`CUOTAS`) from `v_plan_monto_cuotas` where (`v_plan_monto_cuotas`.`PLAN_ID` = `pp`.`id`)),0) AS `CUOTAS_MAXIMO`,(select min(`v_plan_monto_cuotas`.`IMPORTE`) from `v_plan_monto_cuotas` where ((`v_plan_monto_cuotas`.`PLAN_ID` = `pp`.`id`) and (`v_plan_monto_cuotas`.`CUOTAS` = (select min(`v_plan_monto_cuotas`.`CUOTAS`) from `v_plan_monto_cuotas` where (`v_plan_monto_cuotas`.`PLAN_ID` = `pp`.`id`))))) AS `CUOTA_MONTO_MINIMO`,(select max(`v_plan_monto_cuotas`.`IMPORTE`) from `v_plan_monto_cuotas` where ((`v_plan_monto_cuotas`.`PLAN_ID` = `pp`.`id`) and (`v_plan_monto_cuotas`.`CUOTAS` = (select max(`v_plan_monto_cuotas`.`CUOTAS`) from `v_plan_monto_cuotas` where (`v_plan_monto_cuotas`.`PLAN_ID` = `pp`.`id`))))) AS `CUOTA_MONTO_MAXIMO` from (`proveedor_planes` `pp` join `proveedores` `p`) where ((`pp`.`proveedor_id` = `p`.`id`) and (`p`.`vendedores` = 1))) */;

/*View structure for view v_productos */

/*!50001 DROP TABLE IF EXISTS `v_productos` */;
/*!50001 DROP VIEW IF EXISTS `v_productos` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_productos` AS (select `v_global_datos`.`ID` AS `ID`,`v_global_datos`.`CONCEPTO_1` AS `CONCEPTO_1`,`v_global_datos`.`CONCEPTO_2` AS `CONCEPTO_2`,`v_global_datos`.`CONCEPTO_3` AS `CONCEPTO_3`,`v_global_datos`.`LOGICO_1` AS `LOGICO_1`,`v_global_datos`.`LOGICO_2` AS `LOGICO_2`,`v_global_datos`.`ENTERO_1` AS `ENTERO_1`,`v_global_datos`.`ENTERO_2` AS `ENTERO_2`,`v_global_datos`.`DECIMAL_1` AS `DECIMAL_1`,`v_global_datos`.`DECIMAL_2` AS `DECIMAL_2`,`v_global_datos`.`FECHA_1` AS `FECHA_1`,`v_global_datos`.`FECHA_2` AS `FECHA_2`,`v_global_datos`.`TEXTO_1` AS `TEXTO_1`,`v_global_datos`.`TEXTO_2` AS `TEXTO_2` from `v_global_datos` where ((`v_global_datos`.`ID` like 'MUTUPROD%') and (`v_global_datos`.`ID` <> 'MUTUPROD')) order by `v_global_datos`.`ENTERO_1`,`v_global_datos`.`CONCEPTO_1`) */;

/*View structure for view v_provincias */

/*!50001 DROP TABLE IF EXISTS `v_provincias` */;
/*!50001 DROP VIEW IF EXISTS `v_provincias` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_provincias` AS (select `provincias`.`id` AS `ID`,`provincias`.`nombre` AS `NOMBRE`,`provincias`.`letra` AS `LETRA` from `provincias`) */;

/*View structure for view v_usuarios */

/*!50001 DROP TABLE IF EXISTS `v_usuarios` */;
/*!50001 DROP VIEW IF EXISTS `v_usuarios` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_usuarios` AS (select `usuarios`.`id` AS `ID`,`usuarios`.`grupo_id` AS `GRUPO_ID`,`usuarios`.`usuario` AS `USUARIO`,`usuarios`.`password` AS `PASSWORD`,`usuarios`.`activo` AS `ACTIVO`,`usuarios`.`descripcion` AS `DESCRIPCION`,`usuarios`.`vendedor_id` AS `VENDEDOR_ID` from `usuarios`) */;

/*View structure for view v_vendedor_remitos */

/*!50001 DROP TABLE IF EXISTS `v_vendedor_remitos` */;
/*!50001 DROP VIEW IF EXISTS `v_vendedor_remitos` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_vendedor_remitos` AS (select `vendedor_remitos`.`id` AS `ID`,`vendedor_remitos`.`vendedor_id` AS `VENDEDOR_ID`,`vendedor_remitos`.`observaciones` AS `OBSERVACIONES`,`vendedor_remitos`.`user_created` AS `USER_CREATED`,`vendedor_remitos`.`created` AS `CREATED`,`vendedor_remitos`.`anulado` AS `ANULADO` from `vendedor_remitos`) */;

/*View structure for view v_vendedores */

/*!50001 DROP TABLE IF EXISTS `v_vendedores` */;
/*!50001 DROP VIEW IF EXISTS `v_vendedores` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_vendedores` AS (select `vendedores`.`id` AS `ID`,`vendedores`.`persona_id` AS `PERSONA_ID`,`vendedores`.`usuario_id` AS `USUARIO_ID`,`vendedores`.`activo` AS `ACTIVO`,(select count(1) from `v_credito_solicitudes` where ((`vendedores`.`id` = `v_credito_solicitudes`.`VENDEDOR_ID`) and (`v_credito_solicitudes`.`VENDEDOR_NOTIFICAR` = 1))) AS `NOTIFICACIONES` from `vendedores`) */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
