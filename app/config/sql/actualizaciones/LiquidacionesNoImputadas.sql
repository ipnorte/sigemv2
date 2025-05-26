/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 01/04/2019
 */

-- #################################################
-- !!! OJO CON LOS AUTOINCREMENT!!!
-- #################################################


-- drop table if exists liquidacion_cuota_noimputadas;

CREATE TABLE `liquidacion_cuota_noimputadas` (
  `id` int(11) NOT NULL auto_increment,
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
  KEY `FK_liquidacion_cuotas_liquidacion_imputadas` (`liquidacion_id`),
  KEY `FK_liquidacion_cuotas_socios_imputadas` (`socio_id`),
  KEY `FK_liquidacion_cuotas_beneficio_imputadas` (`persona_beneficio_id`),
  KEY `FK_liquidacion_cuotas_cuotas_imputadas` (`orden_descuento_cuota_id`),
  KEY `FK_liquidacion_cuotas_proveedor_imputadas` (`proveedor_id`),
  KEY `IDX_resumen_socio_imputadas` (`liquidacion_id`,`codigo_organismo`,`socio_id`,`tipo_cuota`),
  KEY `idx_liquidacion_intercambio_id_imputadas` (`liquidacion_intercambio_id`),
  KEY `idx_intercambio_imputadas` (`socio_id`,`liquidacion_intercambio_id`),
  KEY `idx_imputacion_pagos_imputadas` (`liquidacion_id`,`importe_debitado`,`liquidacion_intercambio_id`),
  KEY `idx_liquidacion_socio_registro_imputadas` (`liquidacion_id`,`socio_id`,`registro`),
  KEY `idx_imputada_imputadas` (`liquidacion_id`,`imputada`,`proveedor_id`),
  KEY `IDX_adicional_imputado_imputadas` (`liquidacion_id`,`mutual_adicional_pendiente_id`,`orden_descuento_id`,`importe_debitado`,`imputada`),
  KEY `idx_listado_proveedor_imputadas` (`liquidacion_id`,`proveedor_id`,`tipo_producto`,`tipo_cuota`),
  KEY `liquidacion_id_imputadas` (`liquidacion_id`,`socio_id`,`para_imputar`,`imputada`),
  KEY `FK_liquidacion_cuotas_orddto_orden_id_imputadas` (`orden_descuento_id`),
  KEY `idx_liquidacion_cuotas_cobro_id_imputadas` (`orden_descuento_cobro_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4366509 DEFAULT CHARSET=utf8;


-- drop table if exists liquidacion_socio_imputadas;

CREATE TABLE `liquidacion_socio_noimputadas` (
  `id` int(11) NOT NULL auto_increment,
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
  KEY `FK_liquidacion_socios_liquidacion_noimputadas` (`liquidacion_id`),
  KEY `FK_liquidacion_socios_socios_noimputadas` (`socio_id`),
  KEY `FK_liquidacion_socios_beneficio_noimputadas` (`persona_beneficio_id`),
  KEY `idx_documento_noimputadas` (`documento`),
  KEY `idx_apenom_noimputadas` (`apenom`),
  KEY `idx_producto_beneficio_socio_noimputadas` (`liquidacion_id`,`socio_id`,`persona_beneficio_id`),
  KEY `idx_debito_noimputadas` (`socio_id`,`codigo_organismo`,`nro_ley`,`tipo`,`nro_beneficio`,`sub_beneficio`,`codigo_dto`,`sub_codigo`,`banco_id`,`cbu`,`tipo_documento`,`documento`,`status`),
  KEY `idx_liquidacion_intercambio_noimputadas` (`liquidacion_intercambio_id`),
  KEY `idx_intercambio_noimputadas` (`socio_id`,`liquidacion_intercambio_id`),
  KEY `idx_liquidacion_id_status_noimputadas` (`liquidacion_id`,`status`,`liquidacion_intercambio_id`),
  KEY `idx_orden_cobro_noimputadas` (`liquidacion_id`,`orden_descuento_cobro_id`) USING BTREE,
  KEY `idx_pagos_no_imputados_noimputadas` (`liquidacion_id`,`socio_id`,`importe_debitado`,`liquidacion_intercambio_id`,`imputada`) USING BTREE,
  KEY `idx_reintegro_noimputadas` (`liquidacion_id`,`importe_reintegro`,`liquidacion_intercambio_id`),
  KEY `idx_liquidacion_socio_alta_noimputadas` (`liquidacion_id`,`socio_id`,`alta`),
  KEY `idx_liquidacion_alta_noimputadas` (`liquidacion_id`,`alta`,`indica_pago`),
  KEY `idx_empresa_noimputadas` (`codigo_empresa`,`codigo_reparticion`),
  KEY `idx_orden_descuento_id_noimputadas` (`orden_descuento_id`),
  KEY `idx_liquidacion_beneficio_turno_noimputadas` (`liquidacion_id`,`persona_beneficio_id`,`turno_pago`),
  KEY `idx_liquidacion_diskette_noimputadas` (`liquidacion_id`,`diskette`)
) ENGINE=MyISAM AUTO_INCREMENT=1469722 DEFAULT CHARSET=utf8;






DROP procedure IF EXISTS `SP_LIQUIDA_DEUDA_SCORING_NOIMPUTADAS`;

DELIMITER $$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LIQUIDA_DEUDA_SCORING_NOIMPUTADAS`(IN
vSOCIO_ID INT(11),vLIQUIDACION_ID INT(11))
BEGIN

select periodo into @periodo from liquidaciones where id = vLIQUIDACION_ID;
delete from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID;
insert into liquidacion_socio_scores(liquidacion_id,socio_id,`13`,`12`,`09`,`06`,`03`,`00`,cargos_adicionales,saldo_actual)
select liquidacion_id,socio_id, 
ifnull((select sum(saldo_actual) from liquidacion_cuota_noimputadas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 12 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuota_noimputadas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 12 month),'%Y%m')  
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 9 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuota_noimputadas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 9 month),'%Y%m')
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 6 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuota_noimputadas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota  > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 6 month),'%Y%m') 
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 3 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuota_noimputadas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota  > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 3 month),'%Y%m') 
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 0 month),'%Y%m')
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuota_noimputadas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 0 month),'%Y%m')
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuota_noimputadas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id <> 0
and lc.socio_id = lc2.socio_id),0),
sum(saldo_actual) as saldo_actual
from liquidacion_cuota_noimputadas lc2 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID
group by socio_id;

-- //// ASIGNO PUNTAJE
if cast((select `13` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 5 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `12` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 4 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `09` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 3 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `06` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 2 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `03` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 1 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `00` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 0 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
end if;
end if;
end if;
end if;
end if;
end if;


END$$

DELIMITER ;


-- drop table if exists liquidacion_socio_envio_noimputadas;

CREATE TABLE `liquidacion_socio_envio_noimputadas` (
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
  KEY `fk_liquidacion_id` (`liquidacion_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3013 DEFAULT CHARSET=utf8;


-- drop table if exists liquidacion_socio_envio_registro_noimputadas

CREATE TABLE `liquidacion_socio_envio_registro_noimputadas` (
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
  KEY `FK_LIQUIDACION_SOCIO_SOCIO_ID` (`socio_id`)
) ENGINE=MyISAM AUTO_INCREMENT=405032 DEFAULT CHARSET=utf8;
