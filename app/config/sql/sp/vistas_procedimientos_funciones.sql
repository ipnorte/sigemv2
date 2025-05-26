DROP VIEW IF EXISTS `v_asincronos`;
DROP VIEW IF EXISTS `v_bancos`;
DROP VIEW IF EXISTS `v_cancelacion_ordenes`;
DROP VIEW IF EXISTS `v_cliente_calificaciones`;
DROP VIEW IF EXISTS `v_cliente_cancelaciones_emitidas`;
DROP VIEW IF EXISTS `v_clientes`;
DROP VIEW IF EXISTS `v_credito_solcitud_cancelaciones`;
DROP VIEW IF EXISTS `v_credito_solicitud_cuotas_ws`;
DROP VIEW IF EXISTS `v_credito_solicitud_documentos`;
DROP VIEW IF EXISTS `v_credito_solicitudes`;
DROP VIEW IF EXISTS `v_credito_solicitud_estados`;
DROP VIEW IF EXISTS `v_credito_solicitudes_ws`;
DROP VIEW IF EXISTS `v_credito_solicitud_forma_liquidacion`;
DROP VIEW IF EXISTS `v_credito_solicitud_instruccion_pagos`;
DROP VIEW IF EXISTS `v_credito_solitud_historicos`;
DROP VIEW IF EXISTS `v_empresas`;
DROP VIEW IF EXISTS `v_estado_cuenta`;
DROP VIEW IF EXISTS `v_global_datos`;
DROP VIEW IF EXISTS `v_grupos`;
DROP VIEW IF EXISTS `v_localidades`;
DROP VIEW IF EXISTS `v_organismos`;
DROP VIEW IF EXISTS `v_persona_beneficios`;
DROP VIEW IF EXISTS `v_persona_domicilios`;
DROP VIEW IF EXISTS `v_persona_estados_civil`;
DROP VIEW IF EXISTS `v_personas`;
DROP VIEW IF EXISTS `v_persona_tipo_documentos`;
DROP VIEW IF EXISTS `v_plan_condiciones`;
DROP VIEW IF EXISTS `v_planes`;
DROP VIEW IF EXISTS `v_plan_monto_cuotas`;
DROP VIEW IF EXISTS `v_plan_montos`;
DROP VIEW IF EXISTS `v_plan_organismos`;
DROP VIEW IF EXISTS `v_productos`;
DROP VIEW IF EXISTS `v_proveedores`;
DROP VIEW IF EXISTS `v_provincias`;
DROP VIEW IF EXISTS `v_usuarios`;
DROP VIEW IF EXISTS `v_vendedores`;
DROP VIEW IF EXISTS `v_vendedor_remitos`;
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_global_datos` AS (select `global_datos`.`id` AS `ID`,`global_datos`.`concepto_1` AS `CONCEPTO_1`,`global_datos`.`concepto_2` AS `CONCEPTO_2`,`global_datos`.`concepto_3` AS `CONCEPTO_3`,`global_datos`.`logico_1` AS `LOGICO_1`,`global_datos`.`logico_2` AS `LOGICO_2`,`global_datos`.`entero_1` AS `ENTERO_1`,`global_datos`.`entero_2` AS `ENTERO_2`,`global_datos`.`decimal_1` AS `DECIMAL_1`,`global_datos`.`decimal_2` AS `DECIMAL_2`,`global_datos`.`fecha_1` AS `FECHA_1`,`global_datos`.`fecha_2` AS `FECHA_2`,`global_datos`.`texto_1` AS `TEXTO_1`,`global_datos`.`texto_2` AS `TEXTO_2` from `global_datos`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_clientes` AS (select `socios`.`id` AS `ID`,`socios`.`categoria` AS `CATEGORIA`,`socios`.`persona_id` AS `PERSONA_ID`,`socios`.`persona_beneficio_id` AS `BENEFICIO_ID`,`socios`.`activo` AS `ACTIVO`,`socios`.`fecha_alta` AS `FECHA_ALTA`,`socios`.`orden_descuento_id` AS `ORDEN_DESCUENTO_ID`,`socios`.`calificacion` AS `CALIFICACION`,`socios`.`fecha_calificacion` AS `FECHA_CALIFICACION`,`socios`.`codigo_baja` AS `CODIGO_BAJA`,`socios`.`fecha_baja` AS `FECHA_BAJA`,`socios`.`observaciones` AS `OBSERVACIONES` from `socios`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_asincronos` AS (select `asincronos`.`id` AS `ID`,`asincronos`.`shell_pid` AS `SHELL_PID`,`asincronos`.`propietario` AS `PROPIETARIO`,`asincronos`.`remote_ip` AS `REMOTE_IP`,`asincronos`.`final` AS `FINAL`,`asincronos`.`proceso` AS `PROCESO`,`asincronos`.`bloqueado` AS `BLOQUEADO`,`asincronos`.`p1` AS `P1`,`asincronos`.`p2` AS `P2`,`asincronos`.`p3` AS `P3`,`asincronos`.`p4` AS `P4`,`asincronos`.`p5` AS `P5`,`asincronos`.`p6` AS `P6`,`asincronos`.`p7` AS `P7`,`asincronos`.`p8` AS `P8`,`asincronos`.`p9` AS `P9`,`asincronos`.`p10` AS `P10`,`asincronos`.`p11` AS `P11`,`asincronos`.`p12` AS `P12`,`asincronos`.`p13` AS `P13`,`asincronos`.`txt1` AS `TXT1`,`asincronos`.`txt2` AS `TXT2`,`asincronos`.`action_do` AS `ACTION_DO`,`asincronos`.`target` AS `TARGET`,`asincronos`.`btn_label` AS `BTN_LABEL`,`asincronos`.`titulo` AS `TITULO`,`asincronos`.`subtitulo` AS `SUB_TITULO`,`asincronos`.`estado` AS `ESTADO`,`asincronos`.`total` AS `TOTAL`,`asincronos`.`contador` AS `CONTADOR`,`asincronos`.`porcentaje` AS `PORCENTAJE`,`asincronos`.`msg` AS `MSG`,`asincronos`.`errores` AS `ERRORES`,`asincronos`.`created` AS `CREATED`,`asincronos`.`modified` AS `MODIFIED` from `asincronos`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_bancos` AS (select `bancos`.`id` AS `ID`,`bancos`.`nombre` AS `NOMBRE`,`bancos`.`activo` AS `ACTIVO`,`bancos`.`beneficio` AS `BENEFICIO` from `bancos`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_cancelacion_ordenes` AS (select `cancelacion_ordenes`.`id` AS `ID`,`cancelacion_ordenes`.`estado` AS `ESTADO`,if((`cancelacion_ordenes`.`estado` = 'E'),'EMITIDA','PROCESADA') AS `ESTADO_DESC`,`cancelacion_ordenes`.`socio_id` AS `CLIENTE_ID`,`cancelacion_ordenes`.`importe_proveedor` AS `IMPORTE_CANCELA`,`cancelacion_ordenes`.`importe_seleccionado` AS `IMPORTE_SELECCIONADO`,`cancelacion_ordenes`.`saldo_orden_dto` AS `SALDO_ORDEN`,`cancelacion_ordenes`.`importe_cuota` AS `IMPORTE_CUOTA`,`cancelacion_ordenes`.`importe_diferencia` AS `DEBITO_CREDITO`,((`cancelacion_ordenes`.`saldo_orden_dto` - `cancelacion_ordenes`.`importe_proveedor`) + `cancelacion_ordenes`.`importe_diferencia`) AS `SALDO`,`cancelacion_ordenes`.`fecha_vto` AS `VENCIMIENTO`,if((`cancelacion_ordenes`.`tipo_cancelacion` = 'T'),'TOTAL','PARCIAL') AS `TIPO`,`cancelacion_ordenes`.`observaciones` AS `OBSERVACIONES`,`cancelacion_ordenes`.`concepto` AS `CONCEPTO`,(select count(0) from `cancelacion_orden_cuotas` where (`cancelacion_orden_cuotas`.`cancelacion_orden_id` = `cancelacion_ordenes`.`id`)) AS `CANTIDAD_CUOTAS`,`proveedores`.`id` AS `PROVEEDOR_ID`,ucase(`proveedores`.`razon_social`) AS `A_LA_ORDEN_DE`,`mutual_producto_solicitudes`.`id` AS `SOLICITUD_CREDITO_ID` from (((`cancelacion_ordenes` join `proveedores` on((`proveedores`.`id` = `cancelacion_ordenes`.`orden_proveedor_id`))) left join `orden_descuentos` on((`orden_descuentos`.`id` = `cancelacion_ordenes`.`orden_descuento_id`))) left join `mutual_producto_solicitudes` on(((`mutual_producto_solicitudes`.`id` = `orden_descuentos`.`numero`) and (`mutual_producto_solicitudes`.`tipo_orden_dto` = `orden_descuentos`.`tipo_orden_dto`)))));
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_credito_solcitud_cancelaciones` AS (select `mutual_producto_solicitud_cancelaciones`.`mutual_producto_solicitud_id` AS `MUTUAL_PRODUCTO_SOLICITUD_ID`,`mutual_producto_solicitud_cancelaciones`.`cancelacion_orden_id` AS `CANCELACION_ORDEN_ID` from `mutual_producto_solicitud_cancelaciones`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_cliente_calificaciones` AS (select `v_global_datos`.`ID` AS `ID`,`v_global_datos`.`CONCEPTO_1` AS `CONCEPTO_1`,`v_global_datos`.`CONCEPTO_2` AS `CONCEPTO_2`,`v_global_datos`.`CONCEPTO_3` AS `CONCEPTO_3`,`v_global_datos`.`LOGICO_1` AS `LOGICO_1`,`v_global_datos`.`LOGICO_2` AS `LOGICO_2`,`v_global_datos`.`ENTERO_1` AS `ENTERO_1`,`v_global_datos`.`ENTERO_2` AS `ENTERO_2`,`v_global_datos`.`DECIMAL_1` AS `DECIMAL_1`,`v_global_datos`.`DECIMAL_2` AS `DECIMAL_2`,`v_global_datos`.`FECHA_1` AS `FECHA_1`,`v_global_datos`.`FECHA_2` AS `FECHA_2`,`v_global_datos`.`TEXTO_1` AS `TEXTO_1`,`v_global_datos`.`TEXTO_2` AS `TEXTO_2` from `v_global_datos` where ((`v_global_datos`.`ID` like 'MUTUCALI%') and (`v_global_datos`.`ID` <> 'MUTUCALI')) order by `v_global_datos`.`ENTERO_1`,`v_global_datos`.`CONCEPTO_1`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_cliente_cancelaciones_emitidas` AS (select `v_clientes`.`ID` AS `CLIENTE_ID`,`v_cancelacion_ordenes`.`ID` AS `CANCELACION_ID` from (`v_clientes` join `v_cancelacion_ordenes` on((`v_cancelacion_ordenes`.`CLIENTE_ID` = `v_clientes`.`ID`))) where ((`v_cancelacion_ordenes`.`ESTADO` = 'E') and (`v_cancelacion_ordenes`.`VENCIMIENTO` >= curdate()) and (not(`v_cancelacion_ordenes`.`ID` in (select `v_credito_solcitud_cancelaciones`.`CANCELACION_ORDEN_ID` from `v_credito_solcitud_cancelaciones`)))));
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_credito_solicitudes_ws` AS select `proveedores`.`codigo_acceso_ws` AS `proveedor_codigo_acceso_ws`,`proveedores`.`id` AS `client_key`,`solicitudes`.`id` AS `solicitud_numero`,`solicitudes`.`fecha` AS `solicitud_fecha`,`solicitudes`.`fecha_pago` AS `solicitud_fecha_pago`,`solicitudes`.`aprobada` AS `solicitud_aprobada`,right(`solicitudes`.`estado`,4) AS `solicitud_codigo_estado`,`estado`.`concepto_1` AS `solicitud_codigo_estado_descripcion`,right(`solicitudes`.`tipo_producto`,4) AS `solicitud_codigo_producto`,`producto`.`concepto_1` AS `solicitud_codigo_producto_descripcion`,`proveedores`.`razon_social` AS `solicitud_proveedor`,`solicitudes`.`importe_total` AS `solicitud_importe_total`,`solicitudes`.`cuotas` AS `solicitud_cantidad_cuotas`,`solicitudes`.`importe_cuota` AS `solicitud_importe_cuota`,`solicitudes`.`importe_solicitado` AS `solicitud_importe_solicitado`,`solicitudes`.`importe_percibido` AS `solicitud_importe_percibido`,`solicitudes`.`periodo_ini` AS `solicitud_periodo_inicio`,`solicitudes`.`primer_vto_socio` AS `solicitud_primer_vencimiento`,right(`personas`.`tipo_documento`,4) AS `solicitante_codigo_documento`,`personas`.`documento` AS `solicitante_documento`,`personas`.`apellido` AS `solicitante_apellido`,`personas`.`nombre` AS `solicitante_nombre`,`personas`.`sexo` AS `solicitante_sexo`,`personas`.`calle` AS `solicitante_calle`,`personas`.`numero_calle` AS `solicitante_numero_calle`,`personas`.`piso` AS `solicitante_piso`,`personas`.`dpto` AS `solicitante_dpto`,`personas`.`barrio` AS `solicitante_barrio`,`personas`.`localidad` AS `solicitante_localidad`,`personas`.`codigo_postal` AS `solicitante_codigo_postal`,`personas`.`provincia_id` AS `solicitante_codigo_provincia`,`provincias`.`nombre` AS `solicitante_codigo_provincia_nombre`,`personas`.`nombre_conyuge` AS `solicitante_nombre_conyuge`,`personas`.`telefono_fijo` AS `solicitante_telefono_fijo`,`personas`.`telefono_movil` AS `solicitante_telefono_movil`,`personas`.`telefono_referencia` AS `solicitante_telefono_referencia`,`personas`.`persona_referencia` AS `solicitante_persona_referencia`,`personas`.`e_mail` AS `solicitante_email`,right(`beneficios`.`codigo_beneficio`,4) AS `beneficio_codigo_beneficio`,`organismos`.`concepto_1` AS `beneficio_codigo_beneficio_descripcion`,`beneficios`.`nro_ley` AS `beneficio_nro_ley`,`beneficios`.`tipo` AS `beneficio_tipo`,`beneficios`.`nro_beneficio` AS `beneficio_nro_beneficio`,`beneficios`.`sub_beneficio` AS `beneficio_sub_beneficio`,`beneficios`.`nro_legajo` AS `beneficio_nro_legajo`,`beneficios`.`fecha_ingreso` AS `beneficio_fecha_ingreso`,`beneficios`.`codigo_reparticion` AS `beneficio_codigo_reparticion`,right(trim(`beneficios`.`turno_pago`),5) AS `beneficio_codigo_turno_pago`,`beneficios`.`cbu` AS `beneficio_cbu`,`beneficios`.`banco_id` AS `beneficio_codigo_banco`,`bancos`.`nombre` AS `beneficio_codigo_banco_descripcion`,`beneficios`.`nro_sucursal` AS `beneficio_nro_sucursal`,`beneficios`.`nro_cta_bco` AS `beneficio_nro_cta_bco`,right(`beneficios`.`codigo_empresa`,4) AS `beneficio_codigo_empresa`,`empresas`.`concepto_1` AS `beneficio_codigo_empresa_descripcion`,`vendedores`.`id` AS `vendedor_nro`,`persona_vendedor`.`cuit_cuil` AS `vendedor_cuit`,concat(`persona_vendedor`.`apellido`,' ',`persona_vendedor`.`nombre`) AS `vendedor_apenom` from (((((((((((`mutual_producto_solicitudes` `solicitudes` join `proveedores` on((`proveedores`.`id` = `solicitudes`.`proveedor_id`))) join `personas` on((`solicitudes`.`persona_id` = `personas`.`id`))) join `persona_beneficios` `beneficios` on((`solicitudes`.`persona_beneficio_id` = `beneficios`.`id`))) join `global_datos` `producto` on((`producto`.`id` = `solicitudes`.`tipo_producto`))) join `global_datos` `estado` on((`estado`.`id` = `solicitudes`.`estado`))) left join `global_datos` `organismos` on((`organismos`.`id` = `beneficios`.`codigo_beneficio`))) left join `global_datos` `empresas` on((`empresas`.`id` = `beneficios`.`codigo_empresa`))) left join `provincias` on((`provincias`.`id` = `personas`.`provincia_id`))) left join `vendedores` on((`vendedores`.`id` = `solicitudes`.`vendedor_id`))) left join `personas` `persona_vendedor` on((`persona_vendedor`.`id` = `vendedores`.`persona_id`))) left join `bancos` on((`bancos`.`id` = `beneficios`.`banco_id`))) where ((`solicitudes`.`anulada` = 0) and (ifnull(`proveedores`.`codigo_acceso_ws`,'') <> '')) union select `proveedores`.`codigo_acceso_ws` AS `proveedor_codigo_acceso_ws`,`proveedores`.`id` AS `client_key`,`solicitudes`.`id` AS `solicitud_numero`,`solicitudes`.`fecha` AS `solicitud_fecha`,`solicitudes`.`fecha_pago` AS `solicitud_fecha_pago`,`solicitudes`.`aprobada` AS `solicitud_aprobada`,right(`solicitudes`.`estado`,4) AS `solicitud_codigo_estado`,`estado`.`concepto_1` AS `solicitud_codigo_estado_descripcion`,right(`solicitudes`.`tipo_producto`,4) AS `solicitud_codigo_producto`,`producto`.`concepto_1` AS `solicitud_codigo_producto_descripcion`,`proveedores`.`razon_social` AS `solicitud_proveedor`,`solicitudes`.`importe_total` AS `solicitud_importe_total`,`solicitudes`.`cuotas` AS `solicitud_cantidad_cuotas`,`solicitudes`.`importe_cuota` AS `solicitud_importe_cuota`,`solicitudes`.`importe_solicitado` AS `solicitud_importe_solicitado`,`solicitudes`.`importe_percibido` AS `solicitud_importe_percibido`,`solicitudes`.`periodo_ini` AS `solicitud_periodo_inicio`,`solicitudes`.`primer_vto_socio` AS `solicitud_primer_vencimiento`,right(`personas`.`tipo_documento`,4) AS `solicitante_codigo_documento`,`personas`.`documento` AS `solicitante_documento`,`personas`.`apellido` AS `solicitante_apellido`,`personas`.`nombre` AS `solicitante_nombre`,`personas`.`sexo` AS `solicitante_sexo`,`personas`.`calle` AS `solicitante_calle`,`personas`.`numero_calle` AS `solicitante_numero_calle`,`personas`.`piso` AS `solicitante_piso`,`personas`.`dpto` AS `solicitante_dpto`,`personas`.`barrio` AS `solicitante_barrio`,`personas`.`localidad` AS `solicitante_localidad`,`personas`.`codigo_postal` AS `solicitante_codigo_postal`,`personas`.`provincia_id` AS `solicitante_codigo_provincia`,`provincias`.`nombre` AS `solicitante_codigo_provincia_nombre`,`personas`.`nombre_conyuge` AS `solicitante_nombre_conyuge`,`personas`.`telefono_fijo` AS `solicitante_telefono_fijo`,`personas`.`telefono_movil` AS `solicitante_telefono_movil`,`personas`.`telefono_referencia` AS `solicitante_telefono_referencia`,`personas`.`persona_referencia` AS `solicitante_persona_referencia`,`personas`.`e_mail` AS `solicitante_email`,right(`beneficios`.`codigo_beneficio`,4) AS `beneficio_codigo_beneficio`,`organismos`.`concepto_1` AS `beneficio_codigo_beneficio_descripcion`,`beneficios`.`nro_ley` AS `beneficio_nro_ley`,`beneficios`.`tipo` AS `beneficio_tipo`,`beneficios`.`nro_beneficio` AS `beneficio_nro_beneficio`,`beneficios`.`sub_beneficio` AS `beneficio_sub_beneficio`,`beneficios`.`nro_legajo` AS `beneficio_nro_legajo`,`beneficios`.`fecha_ingreso` AS `beneficio_fecha_ingreso`,`beneficios`.`codigo_reparticion` AS `beneficio_codigo_reparticion`,right(trim(`beneficios`.`turno_pago`),5) AS `beneficio_codigo_turno_pago`,`beneficios`.`cbu` AS `beneficio_cbu`,`beneficios`.`banco_id` AS `beneficio_codigo_banco`,`bancos`.`nombre` AS `beneficio_codigo_banco_descripcion`,`beneficios`.`nro_sucursal` AS `beneficio_nro_sucursal`,`beneficios`.`nro_cta_bco` AS `beneficio_nro_cta_bco`,right(`beneficios`.`codigo_empresa`,4) AS `beneficio_codigo_empresa`,`empresas`.`concepto_1` AS `beneficio_codigo_empresa_descripcion`,`vendedores`.`id` AS `vendedor_nro`,`persona_vendedor`.`cuit_cuil` AS `vendedor_cuit`,concat(`persona_vendedor`.`apellido`,' ',`persona_vendedor`.`nombre`) AS `vendedor_apenom` from (((((((((((`mutual_producto_solicitudes` `solicitudes` join `proveedores` on((`proveedores`.`id` = `solicitudes`.`reasignar_proveedor_id`))) join `personas` on((`solicitudes`.`persona_id` = `personas`.`id`))) join `persona_beneficios` `beneficios` on((`solicitudes`.`persona_beneficio_id` = `beneficios`.`id`))) join `global_datos` `producto` on((`producto`.`id` = `solicitudes`.`tipo_producto`))) join `global_datos` `estado` on((`estado`.`id` = `solicitudes`.`estado`))) left join `global_datos` `organismos` on((`organismos`.`id` = `beneficios`.`codigo_beneficio`))) left join `global_datos` `empresas` on((`empresas`.`id` = `beneficios`.`codigo_empresa`))) left join `provincias` on((`provincias`.`id` = `personas`.`provincia_id`))) left join `vendedores` on((`vendedores`.`id` = `solicitudes`.`vendedor_id`))) left join `personas` `persona_vendedor` on((`persona_vendedor`.`id` = `vendedores`.`persona_id`))) left join `bancos` on((`bancos`.`id` = `beneficios`.`banco_id`))) where ((`solicitudes`.`anulada` = 0) and (ifnull(`proveedores`.`codigo_acceso_ws`,'') <> ''));
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_credito_solicitud_cuotas_ws` AS select `cuotas`.`id` AS `cuota_identificador`,`ws`.`proveedor_codigo_acceso_ws` AS `proveedor_codigo_acceso_ws`,`ws`.`solicitud_numero` AS `solicitud_numero`,concat(lpad(`cuotas`.`nro_cuota`,2,'0'),'/',`ordenes`.`cuotas`) AS `cuota`,`cuotas`.`periodo` AS `periodo_actual`,(case when ((ifnull(`cuotas`.`periodo_origen`,'000000') = '000000') or (ifnull(`cuotas`.`periodo_origen`,'') = '')) then '' else `cuotas`.`periodo_origen` end) AS `periodo_origen`,(case when ((ifnull(`cuotas`.`periodo_origen`,'000000') = '000000') or (ifnull(`cuotas`.`periodo_origen`,'') = '')) then '0' else '1' end) AS `reprogramada`,`cuotas`.`vencimiento` AS `vencimiento`,`cuotas`.`vencimiento_proveedor` AS `vencimiento_proveedor`,`cuotas`.`importe` AS `importe`,right(`cuotas`.`tipo_producto`,4) AS `codigo_producto`,`producto`.`concepto_1` AS `codigo_producto_descripcion`,right(`cuotas`.`tipo_cuota`,4) AS `codigo_tipo_cuota`,`tipocuota`.`concepto_1` AS `codigo_tipo_cuota_descripcion`,right(`cuotas`.`situacion`,4) AS `codigo_situacion`,`situacion`.`concepto_1` AS `codigo_situacion_descripcion`,(case when (`cuotas`.`estado` = 'B') then 'BAJA' else '' end) AS `estado`,ifnull((select sum(`cobrocuota`.`importe`) from `orden_descuento_cobro_cuotas` `cobrocuota` where (`cobrocuota`.`orden_descuento_cuota_id` = `cuotas`.`id`)),0) AS `pagado`,(`cuotas`.`importe` - ifnull((select sum(`cobrocuota`.`importe`) from `orden_descuento_cobro_cuotas` `cobrocuota` where (`cobrocuota`.`orden_descuento_cuota_id` = `cuotas`.`id`)),0)) AS `saldo`,`ws`.`vendedor_nro` AS `vendedor_nro`,`ws`.`vendedor_cuit` AS `vendedor_cuit`,`ws`.`vendedor_apenom` AS `vendedor_apenom` from (((((`v_credito_solicitudes_ws` `ws` join `orden_descuentos` `ordenes` on(((`ordenes`.`numero` = `ws`.`solicitud_numero`) and (`ordenes`.`proveedor_id` = `ws`.`client_key`) and (`ordenes`.`activo` = 1)))) join `orden_descuento_cuotas` `cuotas` on((`cuotas`.`orden_descuento_id` = `ordenes`.`id`))) join `global_datos` `producto` on((`producto`.`id` = `cuotas`.`tipo_producto`))) join `global_datos` `tipocuota` on((`tipocuota`.`id` = `cuotas`.`tipo_cuota`))) join `global_datos` `situacion` on((`situacion`.`id` = `cuotas`.`situacion`))) where (ifnull(`ws`.`proveedor_codigo_acceso_ws`,'') <> '');
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_credito_solicitud_documentos` AS (select `mutual_producto_solicitud_documentos`.`id` AS `ID`,`mutual_producto_solicitud_documentos`.`mutual_producto_solicitud_id` AS `MUTUAL_PRODUCTO_SOLICITUD_ID`,`mutual_producto_solicitud_documentos`.`file_name` AS `FILE_NAME`,`mutual_producto_solicitud_documentos`.`file_type` AS `FILE_TYPE`,`mutual_producto_solicitud_documentos`.`file_data` AS `FILE_DATA` from `mutual_producto_solicitud_documentos`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_credito_solicitudes` AS (select `mutual_producto_solicitudes`.`id` AS `ID`,`mutual_producto_solicitudes`.`proveedor_id` AS `PROVEEDOR_ID`,`mutual_producto_solicitudes`.`proveedor_plan_id` AS `PROVEEDOR_PLAN_ID`,`mutual_producto_solicitudes`.`persona_id` AS `PERSONA_ID`,`mutual_producto_solicitudes`.`socio_id` AS `CLIENTE_ID`,`mutual_producto_solicitudes`.`persona_beneficio_id` AS `PERSONA_BENEFICIO_ID`,`mutual_producto_solicitudes`.`aprobada` AS `APROBADA`,`mutual_producto_solicitudes`.`anulada` AS `ANULADA`,`mutual_producto_solicitudes`.`fecha` AS `FECHA`,`mutual_producto_solicitudes`.`fecha_pago` AS `FECHA_PAGO`,`mutual_producto_solicitudes`.`tipo_orden_dto` AS `TIPO_ORDEN_DTO`,`mutual_producto_solicitudes`.`tipo_producto` AS `TIPO_PRODUCTO`,if(((`mutual_producto_solicitudes`.`anulada` = 1) and (`mutual_producto_solicitudes`.`estado` = 'MUTUESTA0001')),'MUTUESTA0000',`mutual_producto_solicitudes`.`estado`) AS `ESTADO`,`mutual_producto_solicitudes`.`importe_total` AS `IMPORTE_TOTAL`,`mutual_producto_solicitudes`.`cuotas` AS `CUOTAS`,`mutual_producto_solicitudes`.`importe_cuota` AS `IMPORTE_CUOTA`,`mutual_producto_solicitudes`.`importe_solicitado` AS `IMPORTE_SOLICITADO`,`mutual_producto_solicitudes`.`importe_percibido` AS `IMPORTE_PERCIBIDO`,`mutual_producto_solicitudes`.`observaciones` AS `OBSERVACIONES`,`mutual_producto_solicitudes`.`orden_descuento_id` AS `ORDEN_DESCUENTO_ID`,`mutual_producto_solicitudes`.`aprobada_por` AS `APROBADA_POR`,`mutual_producto_solicitudes`.`aprobada_el` AS `APROBADA_EL`,`mutual_producto_solicitudes`.`vendedor_id` AS `VENDEDOR_ID`,`mutual_producto_solicitudes`.`vendedor_remito_id` AS `VENDEDOR_REMITO_ID`,`mutual_producto_solicitudes`.`vendedor_notificar` AS `VENDEDOR_NOTIFICAR`,`mutual_producto_solicitudes`.`user_created` AS `EMITIDA_POR`,`mutual_producto_solicitudes`.`periodo_ini` AS `PERIODO_INI`,`mutual_producto_solicitudes`.`primer_vto_socio` AS `PRIMER_VTO_SOCIO`,`mutual_producto_solicitudes`.`forma_pago` AS `FORMA_PAGO`,if(((`mutual_producto_solicitudes`.`aprobada` = 1) and (`mutual_producto_solicitudes`.`anulada` = 0)),concat(`mutual_producto_solicitudes`.`tipo_orden_dto`,' ',`mutual_producto_solicitudes`.`orden_descuento_id`),'') AS `ORDEN_DESCUENTO` from `mutual_producto_solicitudes` where (`mutual_producto_solicitudes`.`tipo_orden_dto` = 'EXPTE'));
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_credito_solicitud_estados` AS (select `v_global_datos`.`ID` AS `ID`,`v_global_datos`.`CONCEPTO_1` AS `CONCEPTO_1`,`v_global_datos`.`CONCEPTO_2` AS `CONCEPTO_2`,`v_global_datos`.`CONCEPTO_3` AS `CONCEPTO_3`,`v_global_datos`.`LOGICO_1` AS `LOGICO_1`,`v_global_datos`.`LOGICO_2` AS `LOGICO_2`,`v_global_datos`.`ENTERO_1` AS `ENTERO_1`,`v_global_datos`.`ENTERO_2` AS `ENTERO_2`,`v_global_datos`.`DECIMAL_1` AS `DECIMAL_1`,`v_global_datos`.`DECIMAL_2` AS `DECIMAL_2`,`v_global_datos`.`FECHA_1` AS `FECHA_1`,`v_global_datos`.`FECHA_2` AS `FECHA_2`,`v_global_datos`.`TEXTO_1` AS `TEXTO_1`,`v_global_datos`.`TEXTO_2` AS `TEXTO_2` from `v_global_datos` where ((`v_global_datos`.`ID` like 'MUTUESTA%') and (`v_global_datos`.`ID` <> 'MUTUESTA')) order by `v_global_datos`.`ENTERO_1`,`v_global_datos`.`CONCEPTO_1`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_credito_solicitud_forma_liquidacion` AS (select `v_global_datos`.`ID` AS `ID`,`v_global_datos`.`CONCEPTO_1` AS `CONCEPTO_1`,`v_global_datos`.`CONCEPTO_2` AS `CONCEPTO_2`,`v_global_datos`.`CONCEPTO_3` AS `CONCEPTO_3`,`v_global_datos`.`LOGICO_1` AS `LOGICO_1`,`v_global_datos`.`LOGICO_2` AS `LOGICO_2`,`v_global_datos`.`ENTERO_1` AS `ENTERO_1`,`v_global_datos`.`ENTERO_2` AS `ENTERO_2`,`v_global_datos`.`DECIMAL_1` AS `DECIMAL_1`,`v_global_datos`.`DECIMAL_2` AS `DECIMAL_2`,`v_global_datos`.`FECHA_1` AS `FECHA_1`,`v_global_datos`.`FECHA_2` AS `FECHA_2`,`v_global_datos`.`TEXTO_1` AS `TEXTO_1`,`v_global_datos`.`TEXTO_2` AS `TEXTO_2` from `v_global_datos` where ((`v_global_datos`.`ID` like 'MUTUFPAG%') and (`v_global_datos`.`ID` <> 'MUTUFPAG')) order by `v_global_datos`.`CONCEPTO_1`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_credito_solicitud_instruccion_pagos` AS (select `mutual_producto_solicitud_instruccion_pagos`.`id` AS `ID`,`mutual_producto_solicitud_instruccion_pagos`.`mutual_producto_solicitud_id` AS `MUTUAL_PRODUCTO_SOLICITUD_ID`,`mutual_producto_solicitud_instruccion_pagos`.`a_la_orden_de` AS `A_LA_ORDEN_DE`,`mutual_producto_solicitud_instruccion_pagos`.`concepto` AS `CONCEPTO`,`mutual_producto_solicitud_instruccion_pagos`.`importe` AS `IMPORTE` from `mutual_producto_solicitud_instruccion_pagos`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_credito_solitud_historicos` AS select `h`.`id` AS `ID`,`h`.`created` AS `FECHA_NOVEDAD`,`h`.`user_created` AS `USUARIO`,`h`.`mutual_producto_solicitud_id` AS `MUTUAL_PRODUCTO_SOLICITUD_ID`,`g`.`concepto_1` AS `ESTADO`,`h`.`observaciones` AS `OBSERVACIONES` from (`mutual_producto_solicitud_estados` `h` join `global_datos` `g` on((`g`.`id` = convert(`h`.`estado` using utf8))));
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_empresas` AS (select `v_global_datos`.`ID` AS `ID`,`v_global_datos`.`CONCEPTO_1` AS `CONCEPTO_1`,`v_global_datos`.`CONCEPTO_2` AS `CONCEPTO_2`,`v_global_datos`.`CONCEPTO_3` AS `CONCEPTO_3`,`v_global_datos`.`LOGICO_1` AS `LOGICO_1`,`v_global_datos`.`LOGICO_2` AS `LOGICO_2`,`v_global_datos`.`ENTERO_1` AS `ENTERO_1`,`v_global_datos`.`ENTERO_2` AS `ENTERO_2`,`v_global_datos`.`DECIMAL_1` AS `DECIMAL_1`,`v_global_datos`.`DECIMAL_2` AS `DECIMAL_2`,`v_global_datos`.`FECHA_1` AS `FECHA_1`,`v_global_datos`.`FECHA_2` AS `FECHA_2`,`v_global_datos`.`TEXTO_1` AS `TEXTO_1`,`v_global_datos`.`TEXTO_2` AS `TEXTO_2` from `v_global_datos` where ((`v_global_datos`.`ID` like 'MUTUEMPR%') and (`v_global_datos`.`ID` <> 'MUTUEMPR')) order by `v_global_datos`.`ENTERO_1`,`v_global_datos`.`CONCEPTO_1`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_estado_cuenta` AS select `orden_descuento_cuotas`.`id` AS `ID`,`orden_descuento_cuotas`.`socio_id` AS `SOCIO_ID`,'VENCIDO' AS `TIPO`,`orden_descuento_cuotas`.`orden_descuento_id` AS `ORDEN_DESCUENTO_ID`,concat(`orden_descuentos`.`tipo_orden_dto`,' #',`orden_descuentos`.`numero`) AS `TIPO_NUMERO`,`tipo_cuota`.`concepto_1` AS `CONCEPTO`,`orden_descuento_cuotas`.`periodo` AS `PERIODO`,concat(substr(`orden_descuento_cuotas`.`periodo`,5,2),'/',substr(`orden_descuento_cuotas`.`periodo`,1,4)) AS `PERIODO_STR`,concat(lpad(`orden_descuento_cuotas`.`nro_cuota`,2,'0'),'/',lpad(`orden_descuentos`.`cuotas`,2,'0')) AS `CUOTA`,`orden_descuento_cuotas`.`importe` AS `IMPORTE_ORIGINAL`,ifnull((select sum(`orden_descuento_cobro_cuotas`.`importe`) from `orden_descuento_cobro_cuotas` where (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),0) AS `PAGOS`,(`orden_descuento_cuotas`.`importe` - ifnull((select sum(`orden_descuento_cobro_cuotas`.`importe`) from `orden_descuento_cobro_cuotas` where (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),0)) AS `SALDO_CONCILIADO`,ifnull((select sum(`liquidacion_cuotas`.`importe_debitado`) from `liquidacion_cuotas` where ((`liquidacion_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`) and (`liquidacion_cuotas`.`imputada` = 0) and (`liquidacion_cuotas`.`para_imputar` = 1))),0) AS `PENDIENTE_ACREDITAR`,((`orden_descuento_cuotas`.`importe` - ifnull((select sum(`orden_descuento_cobro_cuotas`.`importe`) from `orden_descuento_cobro_cuotas` where (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),0)) - ifnull((select sum(`liquidacion_cuotas`.`importe_debitado`) from `liquidacion_cuotas` where ((`liquidacion_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`) and (`liquidacion_cuotas`.`imputada` = 0) and (`liquidacion_cuotas`.`para_imputar` = 1))),0)) AS `SALDO` from ((`orden_descuento_cuotas` join `orden_descuentos` on((`orden_descuentos`.`id` = `orden_descuento_cuotas`.`orden_descuento_id`))) join `global_datos` `tipo_cuota` on((`tipo_cuota`.`id` = `orden_descuento_cuotas`.`tipo_cuota`))) where ((`orden_descuento_cuotas`.`importe` > ifnull((select sum(`orden_descuento_cobro_cuotas`.`importe`) from `orden_descuento_cobro_cuotas` where (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),0)) and (`orden_descuento_cuotas`.`periodo` < date_format(now(),'%Y%m')) and (`orden_descuento_cuotas`.`estado` <> 'B')) union select `orden_descuento_cuotas`.`id` AS `ID`,`orden_descuento_cuotas`.`socio_id` AS `SOCIO_ID`,'CORRIENTE' AS `TIPO`,`orden_descuento_cuotas`.`orden_descuento_id` AS `ORDEN_DESCUENTO_ID`,concat(`orden_descuentos`.`tipo_orden_dto`,' #',`orden_descuentos`.`numero`) AS `TIPO_NUMERO`,`tipo_cuota`.`concepto_1` AS `CONCEPTO`,`orden_descuento_cuotas`.`periodo` AS `PERIODO`,concat(substr(`orden_descuento_cuotas`.`periodo`,5,2),'/',substr(`orden_descuento_cuotas`.`periodo`,1,4)) AS `PERIODO_STR`,concat(lpad(`orden_descuento_cuotas`.`nro_cuota`,2,'0'),'/',lpad(`orden_descuentos`.`cuotas`,2,'0')) AS `CUOTA`,`orden_descuento_cuotas`.`importe` AS `IMPORTE_ORIGINAL`,ifnull((select sum(`orden_descuento_cobro_cuotas`.`importe`) from `orden_descuento_cobro_cuotas` where (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),0) AS `PAGOS`,(`orden_descuento_cuotas`.`importe` - ifnull((select sum(`orden_descuento_cobro_cuotas`.`importe`) from `orden_descuento_cobro_cuotas` where (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),0)) AS `SALDO_CONCILIADO`,ifnull((select sum(`liquidacion_cuotas`.`importe_debitado`) from `liquidacion_cuotas` where ((`liquidacion_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`) and (`liquidacion_cuotas`.`imputada` = 0) and (`liquidacion_cuotas`.`para_imputar` = 1))),0) AS `PENDIENTE_ACREDITAR`,((`orden_descuento_cuotas`.`importe` - ifnull((select sum(`orden_descuento_cobro_cuotas`.`importe`) from `orden_descuento_cobro_cuotas` where (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),0)) - ifnull((select sum(`liquidacion_cuotas`.`importe_debitado`) from `liquidacion_cuotas` where ((`liquidacion_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`) and (`liquidacion_cuotas`.`imputada` = 0) and (`liquidacion_cuotas`.`para_imputar` = 1))),0)) AS `SALDO` from ((`orden_descuento_cuotas` join `orden_descuentos` on((`orden_descuentos`.`id` = `orden_descuento_cuotas`.`orden_descuento_id`))) join `global_datos` `tipo_cuota` on((`tipo_cuota`.`id` = `orden_descuento_cuotas`.`tipo_cuota`))) where ((`orden_descuento_cuotas`.`importe` > ifnull((select sum(`orden_descuento_cobro_cuotas`.`importe`) from `orden_descuento_cobro_cuotas` where (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),0)) and (`orden_descuento_cuotas`.`periodo` = date_format(now(),'%Y%m')) and (`orden_descuento_cuotas`.`estado` <> 'B')) union select `orden_descuento_cuotas`.`id` AS `ID`,`orden_descuento_cuotas`.`socio_id` AS `SOCIO_ID`,'A_VENCER' AS `TIPO`,`orden_descuento_cuotas`.`orden_descuento_id` AS `ORDEN_DESCUENTO_ID`,concat(`orden_descuentos`.`tipo_orden_dto`,' #',`orden_descuentos`.`numero`) AS `TIPO_NUMERO`,`tipo_cuota`.`concepto_1` AS `CONCEPTO`,`orden_descuento_cuotas`.`periodo` AS `PERIODO`,concat(substr(`orden_descuento_cuotas`.`periodo`,5,2),'/',substr(`orden_descuento_cuotas`.`periodo`,1,4)) AS `PERIODO_STR`,concat(lpad(`orden_descuento_cuotas`.`nro_cuota`,2,'0'),'/',lpad(`orden_descuentos`.`cuotas`,2,'0')) AS `CUOTA`,`orden_descuento_cuotas`.`importe` AS `IMPORTE_ORIGINAL`,ifnull((select sum(`orden_descuento_cobro_cuotas`.`importe`) from `orden_descuento_cobro_cuotas` where (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),0) AS `PAGOS`,(`orden_descuento_cuotas`.`importe` - ifnull((select sum(`orden_descuento_cobro_cuotas`.`importe`) from `orden_descuento_cobro_cuotas` where (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),0)) AS `SALDO_CONCILIADO`,ifnull((select sum(`liquidacion_cuotas`.`importe_debitado`) from `liquidacion_cuotas` where ((`liquidacion_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`) and (`liquidacion_cuotas`.`imputada` = 0) and (`liquidacion_cuotas`.`para_imputar` = 1))),0) AS `PENDIENTE_ACREDITAR`,((`orden_descuento_cuotas`.`importe` - ifnull((select sum(`orden_descuento_cobro_cuotas`.`importe`) from `orden_descuento_cobro_cuotas` where (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),0)) - ifnull((select sum(`liquidacion_cuotas`.`importe_debitado`) from `liquidacion_cuotas` where ((`liquidacion_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`) and (`liquidacion_cuotas`.`imputada` = 0) and (`liquidacion_cuotas`.`para_imputar` = 1))),0)) AS `SALDO` from ((`orden_descuento_cuotas` join `orden_descuentos` on((`orden_descuentos`.`id` = `orden_descuento_cuotas`.`orden_descuento_id`))) join `global_datos` `tipo_cuota` on((`tipo_cuota`.`id` = `orden_descuento_cuotas`.`tipo_cuota`))) where ((`orden_descuento_cuotas`.`importe` > ifnull((select sum(`orden_descuento_cobro_cuotas`.`importe`) from `orden_descuento_cobro_cuotas` where (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),0)) and (`orden_descuento_cuotas`.`periodo` > date_format(now(),'%Y%m')) and (`orden_descuento_cuotas`.`estado` <> 'B')) order by `PERIODO`,`ID`,`CUOTA`;
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_grupos` AS (select `grupos`.`id` AS `ID`,`grupos`.`nombre` AS `NOMBRE`,`grupos`.`activo` AS `ACTIVO` from `grupos`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_localidades` AS (select `localidades`.`id` AS `ID`,`localidades`.`cp` AS `CP`,`localidades`.`nombre` AS `NOMBRE`,`localidades`.`provincia_id` AS `PROVINCIA_ID`,`localidades`.`letra_provincia` AS `LETRA_PROVINCIA` from `localidades` where (`localidades`.`provincia_id` is not null));
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_organismos` AS (select `v_global_datos`.`ID` AS `ID`,`v_global_datos`.`CONCEPTO_1` AS `CONCEPTO_1`,`v_global_datos`.`CONCEPTO_2` AS `CONCEPTO_2`,`v_global_datos`.`CONCEPTO_3` AS `CONCEPTO_3`,`v_global_datos`.`LOGICO_1` AS `LOGICO_1`,`v_global_datos`.`LOGICO_2` AS `LOGICO_2`,`v_global_datos`.`ENTERO_1` AS `ENTERO_1`,`v_global_datos`.`ENTERO_2` AS `ENTERO_2`,`v_global_datos`.`DECIMAL_1` AS `DECIMAL_1`,`v_global_datos`.`DECIMAL_2` AS `DECIMAL_2`,`v_global_datos`.`FECHA_1` AS `FECHA_1`,`v_global_datos`.`FECHA_2` AS `FECHA_2`,`v_global_datos`.`TEXTO_1` AS `TEXTO_1`,`v_global_datos`.`TEXTO_2` AS `TEXTO_2` from `v_global_datos` where ((`v_global_datos`.`ID` like 'MUTUCORG%') and (`v_global_datos`.`ID` <> 'MUTUCORG')) order by `v_global_datos`.`CONCEPTO_1`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_persona_beneficios` AS (select `persona_beneficios`.`id` AS `ID`,`persona_beneficios`.`persona_id` AS `PERSONA_ID`,`persona_beneficios`.`codigo_beneficio` AS `CODIGO_ORGANISMO`,`persona_beneficios`.`nro_ley` AS `NRO_LEY`,`persona_beneficios`.`tipo` AS `TIPO`,`persona_beneficios`.`nro_beneficio` AS `NRO_BENEFICIO`,`persona_beneficios`.`sub_beneficio` AS `SUB_BENEFICIO`,`persona_beneficios`.`nro_legajo` AS `NRO_LEGAJO`,`persona_beneficios`.`fecha_ingreso` AS `FECHA_INGRESO`,`persona_beneficios`.`codigo_reparticion` AS `CODIGO_REPARTICION`,`persona_beneficios`.`turno_pago` AS `TURNO_PAGO`,`persona_beneficios`.`cbu` AS `CBU`,`persona_beneficios`.`banco_id` AS `BANCO_ID`,`persona_beneficios`.`nro_sucursal` AS `NRO_SUCURSAL`,`persona_beneficios`.`tipo_cta_bco` AS `TIPO_CTA_BCO`,`persona_beneficios`.`nro_cta_bco` AS `NRO_CTA_BANCO`,`persona_beneficios`.`codigo_empresa` AS `CODIGO_EMPRESA`,`persona_beneficios`.`principal` AS `PRINCIPAL`,`persona_beneficios`.`activo` AS `ACTIVO`,`persona_beneficios`.`porcentaje` AS `PORCENTAJE`,`persona_beneficios`.`acuerdo_debito` AS `ACUERDO_DEBITO`,`persona_beneficios`.`importe_max_registro_cbu` AS `IMPORTE_MAX_REGISTRO_CBU`,concat(`gl1`.`concepto_1`,' - ',ifnull(`gl2`.`concepto_1`,'**'),' | ',if((`persona_beneficios`.`codigo_empresa` = 'MUTUEMPRP001'),`persona_beneficios`.`turno_pago`,''),' | CBU: ',`persona_beneficios`.`cbu`) AS `CADENA` from ((`persona_beneficios` join `global_datos` `gl1` on((`gl1`.`id` = `persona_beneficios`.`codigo_beneficio`))) left join `global_datos` `gl2` on((`gl2`.`id` = `persona_beneficios`.`codigo_empresa`))) where (`persona_beneficios`.`activo` = 1));
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_persona_domicilios` AS (select `personas`.`id` AS `ID`,`personas`.`calle` AS `CALLE`,`personas`.`numero_calle` AS `NUMERO_CALLE`,`personas`.`piso` AS `PISO`,`personas`.`dpto` AS `DPTO`,`personas`.`barrio` AS `BARRIO`,`personas`.`localidad_id` AS `LOCALIDAD_ID`,`personas`.`localidad` AS `LOCALIDAD_DESC`,`personas`.`codigo_postal` AS `CODIGO_POSTAL`,`personas`.`provincia_id` AS `PROVINCIA_ID` from `personas`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_persona_estados_civil` AS (select `v_global_datos`.`ID` AS `ID`,`v_global_datos`.`CONCEPTO_1` AS `CONCEPTO_1`,`v_global_datos`.`CONCEPTO_2` AS `CONCEPTO_2`,`v_global_datos`.`CONCEPTO_3` AS `CONCEPTO_3`,`v_global_datos`.`LOGICO_1` AS `LOGICO_1`,`v_global_datos`.`LOGICO_2` AS `LOGICO_2`,`v_global_datos`.`ENTERO_1` AS `ENTERO_1`,`v_global_datos`.`ENTERO_2` AS `ENTERO_2`,`v_global_datos`.`DECIMAL_1` AS `DECIMAL_1`,`v_global_datos`.`DECIMAL_2` AS `DECIMAL_2`,`v_global_datos`.`FECHA_1` AS `FECHA_1`,`v_global_datos`.`FECHA_2` AS `FECHA_2`,`v_global_datos`.`TEXTO_1` AS `TEXTO_1`,`v_global_datos`.`TEXTO_2` AS `TEXTO_2` from `v_global_datos` where ((`v_global_datos`.`ID` like 'PERSXXEC%') and (`v_global_datos`.`ID` <> 'PERSXXEC')));
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_personas` AS (select `personas`.`id` AS `ID`,`personas`.`id` AS `DOMICILIO_ID`,`personas`.`tipo_documento` AS `TIPO_DOCUMENTO`,`personas`.`documento` AS `DOCUMENTO`,`personas`.`apellido` AS `APELLIDO`,`personas`.`nombre` AS `NOMBRE`,`personas`.`fecha_nacimiento` AS `FECHA_NACIMIENTO`,`personas`.`fecha_fallecimiento` AS `FECHA_FALLECIMIENTO`,`personas`.`fallecida` AS `FALLECIDA`,`personas`.`sexo` AS `SEXO`,`personas`.`estado_civil` AS `ESTADO_CIVIL`,`personas`.`calle` AS `CALLE`,`personas`.`numero_calle` AS `NUMERO_CALLE`,`personas`.`piso` AS `PISO`,`personas`.`dpto` AS `DPTO`,`personas`.`barrio` AS `BARRIO`,`personas`.`localidad_id` AS `LOCALIDAD_ID`,`personas`.`localidad` AS `LOCALIDAD_DESC`,`personas`.`codigo_postal` AS `CODIGO_POSTAL`,`personas`.`provincia_id` AS `PROVINCIA_ID`,`personas`.`cuit_cuil` AS `CUIT_CUIL`,`personas`.`nombre_conyuge` AS `NOMBRE_CONYUGE`,`personas`.`telefono_fijo` AS `TELEFONO_FIJO`,`personas`.`telefono_movil` AS `TELEFONO_MOVIL`,`personas`.`telefono_referencia` AS `TELEFONO_REFERENCIA`,`personas`.`persona_referencia` AS `PERSONA_REFERENCIA`,`personas`.`e_mail` AS `E_MAIL`,`personas`.`tipo_vivienda` AS `TIPO_VIVIENDA`,`personas`.`filial` AS `FILIAL`,`personas`.`user_created` AS `USER_CREATED`,`personas`.`user_modified` AS `USER_MODIFIED`,`personas`.`created` AS `CREATED`,`personas`.`modified` AS `MODIFIED` from `personas`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_persona_tipo_documentos` AS (select `v_global_datos`.`ID` AS `ID`,`v_global_datos`.`CONCEPTO_1` AS `CONCEPTO_1`,`v_global_datos`.`CONCEPTO_2` AS `CONCEPTO_2`,`v_global_datos`.`CONCEPTO_3` AS `CONCEPTO_3`,`v_global_datos`.`LOGICO_1` AS `LOGICO_1`,`v_global_datos`.`LOGICO_2` AS `LOGICO_2`,`v_global_datos`.`ENTERO_1` AS `ENTERO_1`,`v_global_datos`.`ENTERO_2` AS `ENTERO_2`,`v_global_datos`.`DECIMAL_1` AS `DECIMAL_1`,`v_global_datos`.`DECIMAL_2` AS `DECIMAL_2`,`v_global_datos`.`FECHA_1` AS `FECHA_1`,`v_global_datos`.`FECHA_2` AS `FECHA_2`,`v_global_datos`.`TEXTO_1` AS `TEXTO_1`,`v_global_datos`.`TEXTO_2` AS `TEXTO_2` from `v_global_datos` where ((`v_global_datos`.`ID` like 'PERSTPDC%') and (`v_global_datos`.`ID` <> 'PERSTPDC')));

/*
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_plan_montos` AS (select `proveedor_plan_grilla_cuotas`.`id` AS `ID`,`proveedor_planes`.`id` AS `PLAN_ID`,`proveedor_plan_grillas`.`vigencia_desde` AS `VIGENCIA`,`proveedor_plan_grilla_cuotas`.`liquido` AS `LIQUIDO` from ((`proveedor_planes` join `proveedor_plan_grillas`) join `proveedor_plan_grilla_cuotas`) where ((`proveedor_planes`.`id` = `proveedor_plan_grillas`.`proveedor_plan_id`) and (`proveedor_plan_grillas`.`id` = (select `grillas`.`id` from `proveedor_plan_grillas` `grillas` where ((`grillas`.`proveedor_plan_id` = `proveedor_planes`.`id`) and (`grillas`.`vigencia_desde` <= curdate())) order by `grillas`.`vigencia_desde` desc limit 1)) and (`proveedor_plan_grilla_cuotas`.`proveedor_plan_grilla_id` = `proveedor_plan_grillas`.`id`)) group by `proveedor_planes`.`id`,`proveedor_plan_grillas`.`vigencia_desde`,`proveedor_plan_grilla_cuotas`.`liquido` order by `proveedor_planes`.`id`,`proveedor_plan_grilla_cuotas`.`liquido`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_plan_monto_cuotas` AS (select `proveedor_plan_grilla_cuotas`.`id` AS `ID`,`proveedor_planes`.`id` AS `PLAN_ID`,`proveedor_plan_grillas`.`vigencia_desde` AS `VIGENCIA`,`proveedor_plan_grilla_cuotas`.`capital` AS `CAPITAL`,`proveedor_plan_grilla_cuotas`.`liquido` AS `LIQUIDO`,`proveedor_plan_grilla_cuotas`.`cuotas` AS `CUOTAS`,`proveedor_plan_grilla_cuotas`.`importe` AS `IMPORTE`,(`proveedor_plan_grilla_cuotas`.`importe` * `proveedor_plan_grilla_cuotas`.`cuotas`) AS `TOTAL` from ((`proveedor_planes` join `proveedor_plan_grillas`) join `proveedor_plan_grilla_cuotas`) where ((`proveedor_planes`.`id` = `proveedor_plan_grillas`.`proveedor_plan_id`) and (`proveedor_plan_grillas`.`id` = (select `grillas`.`id` from `proveedor_plan_grillas` `grillas` where ((`grillas`.`proveedor_plan_id` = `proveedor_planes`.`id`) and (`grillas`.`vigencia_desde` >= (select max(`ppg2`.`vigencia_desde`) from `proveedor_plan_grillas` `ppg2` where (`ppg2`.`proveedor_plan_id` = `grillas`.`proveedor_plan_id`)))) order by `grillas`.`vigencia_desde` desc limit 1)) and (`proveedor_plan_grilla_cuotas`.`proveedor_plan_grilla_id` = `proveedor_plan_grillas`.`id`)) group by `proveedor_planes`.`id`,`proveedor_plan_grillas`.`vigencia_desde`,`proveedor_plan_grilla_cuotas`.`liquido`,`proveedor_plan_grilla_cuotas`.`cuotas` order by `proveedor_planes`.`id`,`proveedor_plan_grilla_cuotas`.`liquido`,`proveedor_plan_grilla_cuotas`.`cuotas`);
*/

CREATE OR REPLACE
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_plan_monto_cuotas` AS
    (SELECT 
        `proveedor_plan_grilla_cuotas`.`id` AS `ID`,
        `proveedor_planes`.`id` AS `PLAN_ID`,
        `proveedor_plan_grillas`.`vigencia_desde` AS `VIGENCIA`,
        `proveedor_plan_grilla_cuotas`.`capital` AS `CAPITAL`,
        `proveedor_plan_grilla_cuotas`.`liquido` AS `LIQUIDO`,
        `proveedor_plan_grilla_cuotas`.`cuotas` AS `CUOTAS`,
        `proveedor_plan_grilla_cuotas`.`importe` AS `IMPORTE`,
        (`proveedor_plan_grilla_cuotas`.`importe` * `proveedor_plan_grilla_cuotas`.`cuotas`) AS `TOTAL`,
        `proveedor_plan_grillas`.`tna` AS `TNA`,
        `proveedor_plan_grillas`.`tnm` AS `TEM`,
        `proveedor_plan_grilla_cuotas`.`cft` AS `CFT`
    FROM
        ((`proveedor_planes`
        JOIN `proveedor_plan_grillas`)
        JOIN `proveedor_plan_grilla_cuotas`)
    WHERE
        ((`proveedor_planes`.`id` = `proveedor_plan_grillas`.`proveedor_plan_id`)
            AND (`proveedor_plan_grillas`.`id` = (SELECT 
                `grillas`.`id`
            FROM
                `proveedor_plan_grillas` `grillas`
            WHERE
                ((`grillas`.`proveedor_plan_id` = `proveedor_planes`.`id`)
                    AND (`grillas`.`vigencia_desde` >= (SELECT 
                        MAX(`ppg2`.`vigencia_desde`)
                    FROM
                        `proveedor_plan_grillas` `ppg2`
                    WHERE
                        (`ppg2`.`proveedor_plan_id` = `grillas`.`proveedor_plan_id`))))
            ORDER BY `grillas`.`vigencia_desde` DESC
            LIMIT 1))
            AND (`proveedor_plan_grilla_cuotas`.`proveedor_plan_grilla_id` = `proveedor_plan_grillas`.`id`))
    GROUP BY `proveedor_planes`.`id` , `proveedor_plan_grillas`.`vigencia_desde` , `proveedor_plan_grilla_cuotas`.`liquido` , `proveedor_plan_grilla_cuotas`.`cuotas`
    ORDER BY `proveedor_planes`.`id` , `proveedor_plan_grilla_cuotas`.`liquido` , `proveedor_plan_grilla_cuotas`.`cuotas`);


CREATE OR REPLACE
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_plan_montos` AS
    (SELECT 
        `proveedor_plan_grilla_cuotas`.`id` AS `ID`,
        `proveedor_planes`.`id` AS `PLAN_ID`,
        `proveedor_plan_grillas`.`vigencia_desde` AS `VIGENCIA`,
        `proveedor_plan_grilla_cuotas`.`liquido` AS `LIQUIDO`,
        `proveedor_plan_grillas`.`tna` AS `TNA`,
        `proveedor_plan_grillas`.`tnm` AS `TEM`,
        `proveedor_plan_grilla_cuotas`.`cft` AS `CFT`
    FROM
        ((`proveedor_planes`
        JOIN `proveedor_plan_grillas`)
        JOIN `proveedor_plan_grilla_cuotas`)
    WHERE
        ((`proveedor_planes`.`id` = `proveedor_plan_grillas`.`proveedor_plan_id`)
            AND (`proveedor_plan_grillas`.`id` = (SELECT 
                `grillas`.`id`
            FROM
                `proveedor_plan_grillas` `grillas`
            WHERE
                ((`grillas`.`proveedor_plan_id` = `proveedor_planes`.`id`)
                    AND (`grillas`.`vigencia_desde` <= CURDATE()))
            ORDER BY `grillas`.`vigencia_desde` DESC
            LIMIT 1))
            AND (`proveedor_plan_grilla_cuotas`.`proveedor_plan_grilla_id` = `proveedor_plan_grillas`.`id`))
    GROUP BY `proveedor_planes`.`id` , `proveedor_plan_grillas`.`vigencia_desde` , `proveedor_plan_grilla_cuotas`.`liquido`
    ORDER BY `proveedor_planes`.`id` , `proveedor_plan_grilla_cuotas`.`liquido`);


CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_planes` AS (select `pp`.`id` AS `ID`,`pp`.`proveedor_id` AS `PROVEEDOR_ID`,`p`.`razon_social` AS `PROVEEDOR`,`pp`.`descripcion` AS `DESCRIPCION_PLAN`,concat(`p`.`razon_social_resumida`,' ** ',`pp`.`descripcion`,' **') AS `DESCRIPCION`,`pp`.`activo` AS `ACTIVO`,`p`.`reasignable` AS `REASIGNABLE`,`p`.`vendedores` AS `VENDEDORES`,ifnull((select min(`v_plan_montos`.`LIQUIDO`) from `v_plan_montos` where (`v_plan_montos`.`PLAN_ID` = `pp`.`id`)),0) AS `MONTO_MINIMO`,ifnull((select max(`v_plan_montos`.`LIQUIDO`) from `v_plan_montos` where (`v_plan_montos`.`PLAN_ID` = `pp`.`id`)),0) AS `MONTO_MAXIMO`,ifnull((select min(`v_plan_monto_cuotas`.`CUOTAS`) from `v_plan_monto_cuotas` where (`v_plan_monto_cuotas`.`PLAN_ID` = `pp`.`id`)),0) AS `CUOTAS_MINIMO`,ifnull((select max(`v_plan_monto_cuotas`.`CUOTAS`) from `v_plan_monto_cuotas` where (`v_plan_monto_cuotas`.`PLAN_ID` = `pp`.`id`)),0) AS `CUOTAS_MAXIMO`,(select min(`v_plan_monto_cuotas`.`IMPORTE`) from `v_plan_monto_cuotas` where ((`v_plan_monto_cuotas`.`PLAN_ID` = `pp`.`id`) and (`v_plan_monto_cuotas`.`CUOTAS` = (select min(`v_plan_monto_cuotas`.`CUOTAS`) from `v_plan_monto_cuotas` where (`v_plan_monto_cuotas`.`PLAN_ID` = `pp`.`id`))))) AS `CUOTA_MONTO_MINIMO`,(select max(`v_plan_monto_cuotas`.`IMPORTE`) from `v_plan_monto_cuotas` where ((`v_plan_monto_cuotas`.`PLAN_ID` = `pp`.`id`) and (`v_plan_monto_cuotas`.`CUOTAS` = (select max(`v_plan_monto_cuotas`.`CUOTAS`) from `v_plan_monto_cuotas` where (`v_plan_monto_cuotas`.`PLAN_ID` = `pp`.`id`))))) AS `CUOTA_MONTO_MAXIMO` from (`proveedor_planes` `pp` join `proveedores` `p`) where (`pp`.`proveedor_id` = `p`.`id`));

CREATE OR REPLACE
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_plan_condiciones` AS
    (SELECT 
        `ppgc`.`id` AS `ID`,
        `pp`.`ID` AS `PLAN_ID`,
        `ppg`.`vigencia_desde` AS `VIGENCIA`,
        `ppgc`.`capital` AS `CAPITAL`,
        `ppgc`.`liquido` AS `LIQUIDO`,
        `ppgc`.`cuotas` AS `CUOTAS`,
        `ppgc`.`importe` AS `IMPORTE`,
        (`ppgc`.`importe` * `ppgc`.`cuotas`) AS `TOTAL`,
        `ppg`.`tna` AS `TNA`,
        `ppg`.`tnm` AS `TEM`,
        `ppgc`.`cft` AS `CFT`
    FROM
        ((`v_planes` `pp`
        JOIN `proveedor_plan_grillas` `ppg`)
        JOIN `proveedor_plan_grilla_cuotas` `ppgc`)
    WHERE
        ((`pp`.`ID` = `ppg`.`proveedor_plan_id`)
            AND (`ppg`.`id` = `ppgc`.`proveedor_plan_grilla_id`)
            AND (`ppg`.`vigencia_desde` >= (SELECT 
                MAX(`ppg2`.`vigencia_desde`)
            FROM
                `proveedor_plan_grillas` `ppg2`
            WHERE
                (`ppg2`.`proveedor_plan_id` = `ppg`.`proveedor_plan_id`)))));


-- CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_plan_condiciones` AS (select `ppgc`.`id` AS `ID`,`pp`.`ID` AS `PLAN_ID`,`ppg`.`vigencia_desde` AS `VIGENCIA`,`ppgc`.`capital` AS `CAPITAL`,`ppgc`.`liquido` AS `LIQUIDO`,`ppgc`.`cuotas` AS `CUOTAS`,`ppgc`.`importe` AS `IMPORTE`,(`ppgc`.`importe` * `ppgc`.`cuotas`) AS `TOTAL` from ((`v_planes` `pp` join `proveedor_plan_grillas` `ppg`) join `proveedor_plan_grilla_cuotas` `ppgc`) where ((`pp`.`ID` = `ppg`.`proveedor_plan_id`) and (`ppg`.`id` = `ppgc`.`proveedor_plan_grilla_id`) and (`ppg`.`vigencia_desde` >= (select max(`ppg2`.`vigencia_desde`) from `proveedor_plan_grillas` `ppg2` where (`ppg2`.`proveedor_plan_id` = `ppg`.`proveedor_plan_id`)))));
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_plan_organismos` AS (select `proveedor_plan_organismos`.`proveedor_plan_id` AS `PLAN_ID`,`proveedor_plan_organismos`.`codigo_organismo` AS `CODIGO_ORGANISMO` from `proveedor_plan_organismos`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_productos` AS (select `v_global_datos`.`ID` AS `ID`,`v_global_datos`.`CONCEPTO_1` AS `CONCEPTO_1`,`v_global_datos`.`CONCEPTO_2` AS `CONCEPTO_2`,`v_global_datos`.`CONCEPTO_3` AS `CONCEPTO_3`,`v_global_datos`.`LOGICO_1` AS `LOGICO_1`,`v_global_datos`.`LOGICO_2` AS `LOGICO_2`,`v_global_datos`.`ENTERO_1` AS `ENTERO_1`,`v_global_datos`.`ENTERO_2` AS `ENTERO_2`,`v_global_datos`.`DECIMAL_1` AS `DECIMAL_1`,`v_global_datos`.`DECIMAL_2` AS `DECIMAL_2`,`v_global_datos`.`FECHA_1` AS `FECHA_1`,`v_global_datos`.`FECHA_2` AS `FECHA_2`,`v_global_datos`.`TEXTO_1` AS `TEXTO_1`,`v_global_datos`.`TEXTO_2` AS `TEXTO_2` from `v_global_datos` where ((`v_global_datos`.`ID` like 'MUTUPROD%') and (`v_global_datos`.`ID` <> 'MUTUPROD')) order by `v_global_datos`.`ENTERO_1`,`v_global_datos`.`CONCEPTO_1`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_proveedores` AS select `proveedores`.`id` AS `ID`,`proveedores`.`cuit` AS `CUIT`,`proveedores`.`razon_social` AS `RAZON_SOCIAL`,`proveedores`.`razon_social_resumida` AS `RAZON_SOCIAL_RESUMIDA`,`proveedores`.`activo` AS `ACTIVO`,`proveedores`.`calle` AS `CALLE`,`proveedores`.`numero_calle` AS `NUMERO_CALLE`,`proveedores`.`piso` AS `PISO`,`proveedores`.`dpto` AS `DPTO`,`proveedores`.`barrio` AS `BARRIO`,`proveedores`.`localidad` AS `LOCALIDAD`,`proveedores`.`codigo_postal` AS `CODIGO_POSTAL`,`proveedores`.`telefono_fijo` AS `TELEFONO_FIJO`,`proveedores`.`telefono_movil` AS `TELEFONO_MOVIL`,`proveedores`.`fax` AS `FAX`,`proveedores`.`email` AS `EMAIL`,`proveedores`.`codigo_acceso_ws` AS `CODIGO_ACCESO_WS` from `proveedores`;
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_provincias` AS (select `provincias`.`id` AS `ID`,`provincias`.`nombre` AS `NOMBRE`,`provincias`.`letra` AS `LETRA` from `provincias`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_usuarios` AS (select `usuarios`.`id` AS `ID`,`usuarios`.`grupo_id` AS `GRUPO_ID`,`usuarios`.`usuario` AS `USUARIO`,`usuarios`.`password` AS `PASSWORD`,`usuarios`.`activo` AS `ACTIVO`,`usuarios`.`descripcion` AS `DESCRIPCION`,`usuarios`.`vendedor_id` AS `VENDEDOR_ID` from `usuarios`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_vendedores` AS (select `vendedores`.`id` AS `ID`,`vendedores`.`persona_id` AS `PERSONA_ID`,`vendedores`.`usuario_id` AS `USUARIO_ID`,`vendedores`.`activo` AS `ACTIVO`,(select count(1) from `v_credito_solicitudes` where ((`vendedores`.`id` = `v_credito_solicitudes`.`VENDEDOR_ID`) and (`v_credito_solicitudes`.`VENDEDOR_NOTIFICAR` = 1))) AS `NOTIFICACIONES` from `vendedores`);
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_vendedor_remitos` AS (select `vendedor_remitos`.`id` AS `ID`,`vendedor_remitos`.`vendedor_id` AS `VENDEDOR_ID`,`vendedor_remitos`.`observaciones` AS `OBSERVACIONES`,`vendedor_remitos`.`user_created` AS `USER_CREATED`,`vendedor_remitos`.`created` AS `CREATED`,`vendedor_remitos`.`anulado` AS `ANULADO` from `vendedor_remitos`);



DROP PROCEDURE IF EXISTS `p_actualizar_password`;
DROP PROCEDURE IF EXISTS `p_actualizar_persona`;
DROP PROCEDURE IF EXISTS `p_actualizar_persona_contacto`;
DROP PROCEDURE IF EXISTS `p_actualizar_persona_domicilio`;
DROP PROCEDURE IF EXISTS `p_anular_solicitud_credito`;
DROP PROCEDURE IF EXISTS `p_calcular_saldos_socio_periodo`;
DROP PROCEDURE IF EXISTS `p_consultar_asincrono`;
DROP PROCEDURE IF EXISTS `p_detener_asincrono`;
DROP PROCEDURE IF EXISTS `p_insertar_asincrono`;
DROP PROCEDURE IF EXISTS `p_insertar_beneficio`;
DROP PROCEDURE IF EXISTS `p_insertar_localidad`;
DROP PROCEDURE IF EXISTS `p_insertar_persona`;
DROP PROCEDURE IF EXISTS `p_insertar_solicitud_credito`;
DROP PROCEDURE IF EXISTS `p_insertar_solicitud_credito_cancelacion`;
DROP PROCEDURE IF EXISTS `p_insertar_solicitud_credito_cancelacion_preproceso`;
DROP PROCEDURE IF EXISTS `p_insertar_solicitud_credito_con_preproceso`;
DROP PROCEDURE IF EXISTS `p_insertar_solicitud_credito_documento`;
DROP PROCEDURE IF EXISTS `p_insertar_solicitud_credito_documento_preproceso`;
DROP PROCEDURE IF EXISTS `p_insertar_solicitud_credito_instruccion_pago`;
DROP PROCEDURE IF EXISTS `p_insertar_solicitud_credito_instruccion_pago_preproceso`;
DROP PROCEDURE IF EXISTS `p_listado_solicitudes_by_asincrono`;
DROP PROCEDURE IF EXISTS `p_marcar_solicitud_notificacion_leida`;
DROP PROCEDURE IF EXISTS `p_socio_calificacion_score_resumen`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_CUOTA_SERVICIOS`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_CUOTA_SOCIAL`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_ACUERDO_DEBITO`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_ADICIONALES`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_GESTION_MORA`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO_RENUMERA`;
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_SCORING`;
DROP PROCEDURE IF EXISTS `SP_POSICION_CONSOLIDADA`;
DROP PROCEDURE IF EXISTS `SP_REPORTE_PADRON_SERVICIOS`;
DROP PROCEDURE IF EXISTS `SP_TMP_DESIMPUTA_LIQUIDACION`;
DROP PROCEDURE IF EXISTS `STP_ASINCRONO`;
DROP PROCEDURE IF EXISTS `tmp_crea_scoring`;
DROP PROCEDURE IF EXISTS `v_buscar_solicitudes_credito`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_actualizar_password`(
	in vID INT(11), vPASS VARCHAR(40)
    )
BEGIN
    
	UPDATE usuarios set `password` =  vPASS
	where id = vID;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_actualizar_persona`(
in vID int(11),
vFECHA_NACIMIENTO date,
vSEXO varchar(1),
vESTADO_CIVIL varchar(12),
vNOMBRE_CONYUGE varchar(150),
vCUIT varchar(11)
)
BEGIN
update personas
set
	fecha_nacimiento = vFECHA_NACIMIENTO,
	sexo = vSEXO,
	estado_civil = vESTADO_CIVIL,
	nombre_conyuge = vNOMBRE_CONYUGE,
	cuit_cuil = vCUIT
where id = vID;	
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_actualizar_persona_contacto`(
in vID int(11),
vTELEFONO_FIJO varchar(50),
vTELEFONO_MOVIL varchar(50),
vTELEFONO_REFERENCIA varchar(50),
vPERSONA_REFERENCIA varchar(100),
vE_MAIL varchar(100)
)
BEGIN
update personas
set
	telefono_fijo = vTELEFONO_FIJO,
	telefono_movil = vTELEFONO_MOVIL,
	telefono_referencia = vTELEFONO_REFERENCIA,
	persona_referencia = vPERSONA_REFERENCIA,
	e_mail = vE_MAIL
where id = vID;	
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_actualizar_persona_domicilio`(
in vID int(11),
vCALLE varchar(150),
vNUMERO_CALLE varchar(5),
vPISO varchar(5),
vDPTO varchar(5),
vBARRIO varchar(100),
vLOCALIDAD_ID int(11),
vLOCALIDAD_DESC varchar(150),
vCODIGO_POSTAL varchar(8),
vPROVINCIA_ID int(11)
)
BEGIN
update personas
set
	calle = vCALLE,
	numero_calle = vNUMERO_CALLE,
	piso = vPISO,
	dpto = vDPTO,
	barrio = vBARRIO,
	localidad_id = vLOCALIDAD_ID,
	localidad = vLOCALIDAD_DESC,
	codigo_postal = vCODIGO_POSTAL,
	provincia_id = vPROVINCIA_ID
where id = vID;	
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_anular_solicitud_credito`(
	in vID INT(11)
    )
BEGIN
    
	UPDATE mutual_producto_solicitudes
	set anulada = 1, estado = 'MUTUESTA0000'
	where
		id = vID
		and aprobada = 0 and estado = 'MUTUESTA0001';
		
	DELETE FROM mutual_producto_solicitud_instruccion_pagos WHERE mutual_producto_solicitud_id = vID;
	DELETE FROM mutual_producto_solicitud_cancelaciones WHERE mutual_producto_solicitud_id = vID;	
	delete from mutual_producto_solicitud_pagos WHERE mutual_producto_solicitud_id = vID;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_calcular_saldos_socio_periodo`(
	IN vPERIODO VARCHAR(6),
	vSOCIO_ID INT(11)
)
BEGIN
SELECT 
	periodo,
	IFNULL(SUM(importe) - IFNULL((SELECT SUM(importe) FROM orden_descuento_cobro_cuotas cocu_1
		WHERE cocu_1.orden_descuento_cuota_id = orden_descuento_cuotas.id
	),0),0) AS saldo_periodo,
	
	IFNULL((SELECT SUM(importe) FROM orden_descuento_cuotas odc_1 
	WHERE odc_1.periodo < orden_descuento_cuotas.periodo 
	AND odc_1.socio_id = orden_descuento_cuotas.socio_id),0)-
	IFNULL((SELECT SUM(cocu.importe) FROM orden_descuento_cobro_cuotas cocu,orden_descuento_cuotas odc_1 
	WHERE cocu.orden_descuento_cuota_id = odc_1.id
	AND odc_1.periodo < orden_descuento_cuotas.periodo
	AND odc_1.socio_id = orden_descuento_cuotas.socio_id),0) AS vencido,
	
	IFNULL((SELECT SUM(importe) FROM orden_descuento_cuotas odc_1 
	WHERE odc_1.periodo <= orden_descuento_cuotas.periodo 
	AND odc_1.socio_id = orden_descuento_cuotas.socio_id),0)-
	IFNULL((SELECT SUM(cocu.importe) FROM orden_descuento_cobro_cuotas cocu,orden_descuento_cuotas odc_1 
	WHERE cocu.orden_descuento_cuota_id = odc_1.id
	AND odc_1.periodo <= orden_descuento_cuotas.periodo
	AND odc_1.socio_id = orden_descuento_cuotas.socio_id),0) AS saldo_total_acumulado_periodo,

	IFNULL((SELECT SUM(importe) FROM orden_descuento_cuotas odc_1 
	WHERE odc_1.periodo > orden_descuento_cuotas.periodo 
	AND odc_1.socio_id = orden_descuento_cuotas.socio_id),0)-
	IFNULL((SELECT SUM(cocu.importe) FROM orden_descuento_cobro_cuotas cocu,orden_descuento_cuotas odc_1 
	WHERE cocu.orden_descuento_cuota_id = odc_1.id
	AND odc_1.periodo > orden_descuento_cuotas.periodo
	AND odc_1.socio_id = orden_descuento_cuotas.socio_id),0) AS a_vencer

FROM orden_descuento_cuotas WHERE socio_id = vSOCIO_ID
AND periodo = vPERIODO
GROUP BY periodo;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_consultar_asincrono`(in vID int(11))
BEGIN
	select * from v_asincronos where ID = vID;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_detener_asincrono`(vID INT(11))
BEGIN
	UPDATE asincronos 
	set
		estado = 'S',
		msg = '*** DETENIDO POR EL USUARIO ***'
	where id = vID;
	SELECT * FROM v_asincronos WHERE ID = vID;	
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_asincrono`(
	vPROPIETARIO varchar(120),
	vREMOTE_IP varchar(100),
	vPROCESO VARCHAR(150),
	vTITULO VARCHAR(250),
	vSUB_TITULO VARCHAR(250),
	vP1 VARCHAR(250),
	vP2 VARCHAR(250),
	vP3 VARCHAR(250),
	vP4 VARCHAR(250),
	vP5 VARCHAR(250)
)
BEGIN
    SET @ID = 0;
    INSERT INTO asincronos(propietario,remote_ip,proceso,titulo,subtitulo,p1,p2,p3,p4,p5)
    values(vPROPIETARIO,vREMOTE_IP,vPROCESO,vTITULO,vSUB_TITULO,vP1,vP2,vP3,vP4,vP5);
    SELECT * from v_asincronos where ID = LAST_INSERT_ID();
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_beneficio`(
in vPERSONA_ID INT(11),
vCODIGO_BENEFICIO VARCHAR(12),
vFECHA_INGRESO date,
vCODIGO_EMPRESA varchar(12),
vNRO_LEGAJO varchar(50),
vCODIGO_REPARTICION VARCHAR(11),
vTURNO_PAGO VARCHAR(12),
vCBU varchar(23),
vBANCO_ID varchar(5),
vSUCURSAL varchar(5),
vNRO_CTA_BCO varchar(50)
)
BEGIN
	SET @ID = 0;
	SELECT id into @ID FROM persona_beneficios
	where persona_id = vPERSONA_ID
	and codigo_beneficio = vCODIGO_BENEFICIO
	and codigo_empresa = vCODIGO_EMPRESA
	and turno_pago = vTURNO_PAGO
	and cbu = vCBU
	order by id DESC LIMIT 1;
	IF vCODIGO_EMPRESA <> 'MUTUEMPRP001' THEN 
		SET vTURNO_PAGO = vCODIGO_EMPRESA;
	END IF;
	IF vCODIGO_EMPRESA = 'MUTUEMPRP001' THEN
		select turno into @TURNO from liquidacion_turnos where codigo_empresa = 'MUTUEMPRP001'
		and ifnull(codigo_reparticion,'') <> ''
		and SUBSTR(trim(vTURNO_PAGO),1,8) = trim(codigo_reparticion)
		limit 1;
		IF TRIM(@TURNO) <> TRIM(vTURNO_PAGO) AND IFNULL(@TURNO,'') <> '' THEN
			SET vTURNO_PAGO = TRIM(@TURNO);
		END IF;
	end if;
	
	IF @ID = 0 THEN	
		SET vBANCO_ID = RIGHT(concat('00000',substring(vCBU,1,3)),5);
                IF vBANCO_ID = '00000' THEN SET vBANCO_ID = NULL; END IF;
		INSERT INTO persona_beneficios(persona_id,codigo_beneficio,codigo_empresa,
		codigo_reparticion,turno_pago,cbu,banco_id,nro_sucursal,nro_cta_bco,created)
		VALUES(vPERSONA_ID,vCODIGO_BENEFICIO,vCODIGO_EMPRESA,vCODIGO_REPARTICION,vTURNO_PAGO,
		vCBU,vBANCO_ID,vSUCURSAL,vNRO_CTA_BCO,now());
		SELECT LAST_INSERT_ID() INTO @ID;
	END IF;
	
	SELECT * FROM v_persona_beneficios where ID = @ID;
	
		
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`mutual22`@`%` PROCEDURE `p_insertar_localidad`(
IN vCP VARCHAR(4), vNOMBRE VARCHAR(150),vPROVINCIA_ID INT(11), vLETRA_PROVINCIA VARCHAR(1),
vUSER_CREATED VARCHAR(50)
)
BEGIN
INSERT INTO localidades(cp,nombre,provincia_id,letra_provincia,user_created,created)
VALUES (vCP,UPPER(vNOMBRE),vPROVINCIA_ID,vLETRA_PROVINCIA,vUSER_CREATED,NOW());
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_persona`(
	IN 
		vTIPO_DOCUMENTO varchar(12),
		vDOCUMENTO varchar(11),
		vAPELLIDO varchar(100),
		vNOMBRE varchar(100),
		vCUIT_CUIL varchar(11)
    )
BEGIN
	if vTIPO_DOCUMENTO is null then set vTIPO_DOCUMENTO = 'PERSTPDC0001';
	end if;
	if LENGTH(trim(vDOCUMENTO)) < 8 AND vTIPO_DOCUMENTO = 'PERSTPDC0001' then
		set vDOCUMENTO = right(concat('00000000',TRIM(vDOCUMENTO)),8);
	end if;	
	
	INSERT INTO personas (tipo_documento,documento,apellido,nombre,cuit_cuil)
	VALUES(vTIPO_DOCUMENTO,vDOCUMENTO,vAPELLIDO,vNOMBRE,vCUIT_CUIL);
	SELECT * FROM v_personas WHERE ID = (SELECT LAST_INSERT_ID());
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito`(
IN
	vPROVEEDOR_ID INT(11),
	vPROVEEDOR_PLAN_ID INT(11),
	vPERSONA_ID INT(11),
	vCLIENTE_ID INT(11),
	vPERSONA_BENEFICIO_ID INT(11),
	vCUOTAS INT(11),
	vIMPORTE_CUOTA DECIMAL(10,2),
	vIMPORTE_SOLICITADO DECIMAL(10,2),
	vIMPORTE_PERCIBIDO DECIMAL(10,2),
	vVENDEDOR_ID INT(11),
	vOBSERVACIONES TEXT,
	vUSUARIO VARCHAR(50),
	vFORMA_PAGO VARCHAR(12)
)
BEGIN
	DECLARE vESTADO VARCHAR(12);
	DECLARE vPRODUCTO VARCHAR(12);
	DECLARE vTIPOORDEN VARCHAR(5); 
    DECLARE vPRESTAMO BOOLEAN;
	SET vCUOTAS = IF(vCUOTAS=0,NULL,vCUOTAS);
	SET vIMPORTE_CUOTA = IF(vIMPORTE_CUOTA=0,NULL,vIMPORTE_CUOTA);
	SET vIMPORTE_SOLICITADO = IF(vIMPORTE_SOLICITADO=0,NULL,vIMPORTE_SOLICITADO);
	SET vIMPORTE_PERCIBIDO = IF(vIMPORTE_PERCIBIDO=0,NULL,vIMPORTE_PERCIBIDO);
	SET @TOTAL = vCUOTAS * vIMPORTE_CUOTA;
	SET vPRESTAMO = FALSE;
	set vESTADO = 'MUTUESTA0001';
	-- SET vPRODUCTO = 'MUTUPROD0001';
	select tipo_producto into vPRODUCTO from proveedor_planes where id = vPROVEEDOR_PLAN_ID;
    select liquida_prestamo into vPRESTAMO from proveedores where id = vPROVEEDOR_ID;
	
	IF vPRODUCTO IS NULL THEN
		SET vPRODUCTO = 'MUTUPROD0001';
	END IF;
	
	IF vVENDEDOR_ID IS NULL THEN
		SET vESTADO = 'MUTUESTA0002';
	END IF;
	
    
	SELECT trim(concepto_3) into vTIPOORDEN from global_datos where id = vPRODUCTO;	
	
	INSERT INTO mutual_producto_solicitudes(proveedor_id,proveedor_plan_id,
	persona_id,socio_id,persona_beneficio_id,fecha,tipo_orden_dto,tipo_producto,
	estado,importe_total,cuotas,importe_cuota,importe_solicitado,importe_percibido,
	vendedor_id,created,user_created,observaciones,forma_pago,prestamo)
	VALUES(vPROVEEDOR_ID,vPROVEEDOR_PLAN_ID,vPERSONA_ID,vCLIENTE_ID,
	vPERSONA_BENEFICIO_ID,NOW(),vTIPOORDEN,vPRODUCTO,vESTADO,
	@TOTAL,vCUOTAS,vIMPORTE_CUOTA,vIMPORTE_SOLICITADO,vIMPORTE_PERCIBIDO,
	vVENDEDOR_ID,NOW(),vUSUARIO,vOBSERVACIONES,vFORMA_PAGO,vPRESTAMO);
	
	SELECT * FROM v_credito_solicitudes WHERE ID = (SELECT LAST_INSERT_ID());
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_cancelacion`(
	in vSOLICITUD_ID INT(11),
	vCANCELACION_ID INT(11)
    )
BEGIN
	INSERT INTO mutual_producto_solicitud_cancelaciones(mutual_producto_solicitud_id,cancelacion_orden_id)
	VALUES(vSOLICITUD_ID,vCANCELACION_ID);
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_cancelacion_preproceso`(
	in vUUID VARCHAR(100),
	vCANCELACION_ID INT(11)
    )
BEGIN
	INSERT INTO mutual_producto_solicitud_preproceso(uuid_identificador,tipo,cancelacion_id)
	VALUES(vUUID,2,vCANCELACION_ID);
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_con_preproceso`(
IN
	vPROVEEDOR_ID INT(11),
	vPROVEEDOR_PLAN_ID INT(11),
	vPERSONA_ID INT(11),
	vCLIENTE_ID INT(11),
	vPERSONA_BENEFICIO_ID INT(11),
	vCUOTAS INT(11),
	vIMPORTE_CUOTA DECIMAL(10,2),
	vIMPORTE_SOLICITADO DECIMAL(10,2),
	vIMPORTE_PERCIBIDO DECIMAL(10,2),
	vVENDEDOR_ID INT(11),
	vOBSERVACIONES TEXT,
	vUSUARIO VARCHAR(50),
	vFORMA_PAGO VARCHAR(12),
	vUUID VARCHAR(100)
)
BEGIN
	declare vSOLICITUD_ID INT(11);
	DECLARE vESTADO VARCHAR(12);
	DECLARE vPRODUCTO VARCHAR(12);
	DECLARE vTIPOORDEN VARCHAR(5); 
    DECLARE vPRESTAMO BOOLEAN;
	SET vCUOTAS = IF(vCUOTAS=0,NULL,vCUOTAS);
	SET vIMPORTE_CUOTA = IF(vIMPORTE_CUOTA=0,NULL,vIMPORTE_CUOTA);
	SET vIMPORTE_SOLICITADO = IF(vIMPORTE_SOLICITADO=0,NULL,vIMPORTE_SOLICITADO);
	SET vIMPORTE_PERCIBIDO = IF(vIMPORTE_PERCIBIDO=0,NULL,vIMPORTE_PERCIBIDO);
	SET @TOTAL = vCUOTAS * vIMPORTE_CUOTA;
    SET vPRESTAMO = FALSE;
	
	set vESTADO = 'MUTUESTA0001';
	-- SET vPRODUCTO = 'MUTUPROD0001';
	select tipo_producto into vPRODUCTO from proveedor_planes where id = vPROVEEDOR_PLAN_ID;
	select liquida_prestamo into vPRESTAMO from proveedores where id = vPROVEEDOR_ID;
       
	IF vPRODUCTO IS NULL THEN
		SET vPRODUCTO = 'MUTUPROD0001';
	END IF;
	IF vVENDEDOR_ID IS NULL THEN
		SET vESTADO = 'MUTUESTA0002';
	END IF;
	
	SELECT trim(concepto_3) into vTIPOORDEN from global_datos where id = vPRODUCTO;	
	
	INSERT INTO mutual_producto_solicitudes(proveedor_id,proveedor_plan_id,
	persona_id,socio_id,persona_beneficio_id,fecha,tipo_orden_dto,tipo_producto,
	estado,importe_total,cuotas,importe_cuota,importe_solicitado,importe_percibido,
	vendedor_id,created,user_created,observaciones,forma_pago,prestamo)
	VALUES(vPROVEEDOR_ID,vPROVEEDOR_PLAN_ID,vPERSONA_ID,vCLIENTE_ID,
	vPERSONA_BENEFICIO_ID,NOW(),vTIPOORDEN,vPRODUCTO,vESTADO,
	@TOTAL,vCUOTAS,vIMPORTE_CUOTA,vIMPORTE_SOLICITADO,vIMPORTE_PERCIBIDO,
	vVENDEDOR_ID,NOW(),vUSUARIO,vOBSERVACIONES,vFORMA_PAGO,vPRESTAMO);
	SET vSOLICITUD_ID = LAST_INSERT_ID();	
	INSERT INTO mutual_producto_solicitud_estados(mutual_producto_solicitud_id,estado,observaciones,created,user_created)
	values(vSOLICITUD_ID,vESTADO,vOBSERVACIONES,NOW(),vUSUARIO);	
	
	INSERT INTO mutual_producto_solicitud_cancelaciones(mutual_producto_solicitud_id,cancelacion_orden_id)
	SELECT vSOLICITUD_ID,cancelacion_id FROM mutual_producto_solicitud_preproceso
	WHERE uuid_identificador = vUUID AND tipo = 2;
	
	
	INSERT INTO mutual_producto_solicitud_instruccion_pagos(mutual_producto_solicitud_id,a_la_orden_de,concepto,importe)
	SELECT vSOLICITUD_ID,a_la_orden_de,concepto,importe FROM mutual_producto_solicitud_preproceso
	WHERE uuid_identificador = vUUID AND tipo = 1;	
		
	
	INSERT INTO mutual_producto_solicitud_documentos(mutual_producto_solicitud_id,file_name,file_type,file_data)
	SELECT vSOLICITUD_ID,file_name,file_type,file_data FROM mutual_producto_solicitud_preproceso
	WHERE uuid_identificador = vUUID AND tipo = 3;	
	
	DELETE FROM mutual_producto_solicitud_preproceso WHERE uuid_identificador = vUUID;		
	
	
	
	SELECT * FROM v_credito_solicitudes WHERE ID = vSOLICITUD_ID;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_documento`(
	IN vSOLICITUD_ID INT (11),
	vFILE_NAME VARCHAR(100),
	vFILE_TYPE VARCHAR(100),
	vFILE_DATA LONGBLOB 
)
BEGIN
INSERT INTO `mutual_producto_solicitud_documentos` 
	(`mutual_producto_solicitud_id`,`file_name`, `file_type`, `file_data`)
	VALUES (vSOLICITUD_ID,vFILE_NAME,vFILE_TYPE,vFILE_DATA);
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_documento_preproceso`(
	in vUUID VARCHAR(100),
	vFILE_NAME VARCHAR(100),
	vFILE_TYPE VARCHAR(100),
	vFILE_DATA LONGBLOB 
)
BEGIN
INSERT INTO `mutual_producto_solicitud_preproceso` 
	(uuid_identificador,tipo,`file_name`, `file_type`, `file_data`)
	VALUES (vUUID,3,vFILE_NAME,vFILE_TYPE,vFILE_DATA);
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_instruccion_pago`(
	in vSOLICITUD_ID INT(11),
	vORDEN varchar(255),
	vCONCEPTO text,
	vIMPORTE decimal(10,2)
    )
BEGIN
	SET vORDEN = IFNULL(vORDEN,'A MI ORDEN PERSONAL');
	SET vCONCEPTO = IFNULL(vCONCEPTO,'LIQUIDACION PRESTAMO');
	INSERT INTO mutual_producto_solicitud_instruccion_pagos(mutual_producto_solicitud_id,
	a_la_orden_de,concepto,importe)
	values(vSOLICITUD_ID,vORDEN,vCONCEPTO,vIMPORTE);
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_instruccion_pago_preproceso`(
	in vUUID VARCHAR(100),
	vORDEN varchar(255),
	vCONCEPTO text,
	vIMPORTE decimal(10,2)
    )
BEGIN
	SET vORDEN = IFNULL(vORDEN,'A MI ORDEN PERSONAL');
	SET vCONCEPTO = IFNULL(vCONCEPTO,'LIQUIDACION PRESTAMO');
	INSERT INTO mutual_producto_solicitud_preproceso(uuid_identificador,tipo,
	a_la_orden_de,concepto,importe)
	values(vUUID,1,vORDEN,vCONCEPTO,vIMPORTE);
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_listado_solicitudes_by_asincrono`(in vPID int(11))
BEGIN
	DECLARE l_last_row INT DEFAULT 0;
	declare vFD date;
	declare vFH date;
	declare vESTADO varchar(12);
	DECLARE vID INT(11);
	
	
	
	DECLARE  c_solicitudes CURSOR FOR 
	SELECT ID FROM orden_descuento_cuotas;
	
	
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;	
	
	SELECT p1,p2,p3 INTO vFD,vFH,vESTADO FROM asincronos WHERE id = vPID;	
		
	
	open c_solicitudes;
	select FOUND_ROWS() into @REGISTROS ;
	SET @N = 1;
	c1_loop: LOOP 
		FETCH c_solicitudes INTO vID;
		IF (l_last_row = 1) THEN
			LEAVE c1_loop; 
		END IF;	
		SET @PORC = ROUND((@N / @REGISTROS) * 100,0);
		
		UPDATE asincronos set total = @REGISTROS,contador = @N,
		porcentaje = @PORC, msg = concat('PROCESANDO ', @N , '/',@REGISTROS) where id = vPID;
		
		SET @N = @N + 1;
		
	END LOOP c1_loop;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_marcar_solicitud_notificacion_leida`(
	in vID INT(11)
    )
BEGIN
	update mutual_producto_solicitudes SET vendedor_notificar = 0 where id = vID;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_socio_calificacion_score_resumen`(IN vSOCIO_ID INT(11))
BEGIN
DECLARE done INT DEFAULT 0;
DECLARE vCALIFICACION VARCHAR(50);
DECLARE vCANTIDAD INT(11);
DECLARE vRESUMEN TEXT;
DECLARE CURSOR_CALIFICACIONES CURSOR FOR select calificacion.concepto_1, count(*) as cantidad from socio_calificaciones 
inner join global_datos calificacion on (calificacion.id = socio_calificaciones.calificacion)
where socio_id = vSOCIO_ID
group by socio_calificaciones.calificacion
order by cantidad desc;
DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

SET vRESUMEN = '';

OPEN CURSOR_CALIFICACIONES;
REPEAT FETCH CURSOR_CALIFICACIONES INTO vCALIFICACION, vCANTIDAD;
	IF NOT done THEN
		SET vRESUMEN = CONCAT(vRESUMEN , CONCAT(vCALIFICACION,' (',vCANTIDAD,'), ')); 
	END IF;
UNTIL done END REPEAT;
CLOSE CURSOR_CALIFICACIONES;
IF vRESUMEN = '' THEN
SET vRESUMEN = '*** SIN REGISTRO ***';
END IF;
SELECT vRESUMEN;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`mutualam`@`%` PROCEDURE `SP_LIQUIDA_CUOTA_SERVICIOS`(
vSOCIO_ID INT(11),
vPERIODO VARCHAR(6),
vORGANISMO VARCHAR(12)
)
BEGIN

DECLARE vCODIGO_ORGANISMO VARCHAR(12);
DECLARE vORDEN_ID INT(11);
DECLARE vSOLICITUD INT(11);
DECLARE vFECHA DATE;
DECLARE vPERIODO_HASTA VARCHAR(6);
DECLARE vTIPO_ORDEN VARCHAR(4);
DECLARE vTIPO_PRODUCTO VARCHAR(12);
DECLARE vTIPO_CUOTA VARCHAR(12);
DECLARE vBENEFICIO_ID INT(11);
DECLARE vPROVEEDOR_ID INT(11);
DECLARE vNRO_REFERENCIA_PROVEEDOR VARCHAR(10);
DECLARE vIMPORTE_ORDEN_CUOTA DECIMAL(10,2);
DECLARE vACTIVO BOOLEAN;


-- ----------------------------------------------------------
-- BUSCO LAS ORDENES PERMANENTES
-- ----------------------------------------------------------
SELECT
	PersonaBeneficio.codigo_beneficio,
	OrdenDescuento.id,
    OrdenDescuento.numero,
    OrdenDescuento.fecha,
    ifnull(OrdenDescuento.periodo_hasta,'999912'),
	OrdenDescuento.tipo_orden_dto,
	OrdenDescuento.tipo_producto,
    GlobalDato.concepto_2,
	OrdenDescuento.persona_beneficio_id,
	OrdenDescuento.proveedor_id,
	OrdenDescuento.nro_referencia_proveedor,
	OrdenDescuento.importe_cuota,
	OrdenDescuento.activo
INTO vCODIGO_ORGANISMO,vORDEN_ID,vSOLICITUD,vFECHA,vPERIODO_HASTA,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,vBENEFICIO_ID,vPROVEEDOR_ID,vNRO_REFERENCIA_PROVEEDOR,vIMPORTE_ORDEN_CUOTA,vACTIVO    
FROM 
	orden_descuentos as OrdenDescuento, 
	socios as Socio,
	persona_beneficios as PersonaBeneficio,
    global_datos as GlobalDato
WHERE
	Socio.id = vSOCIO_ID  
	AND OrdenDescuento.socio_id = Socio.id 
	AND OrdenDescuento.tipo_orden_dto <> 'CMUTU'
	AND OrdenDescuento.tipo_producto <> 'MUTUPROD0003'
    AND OrdenDescuento.tipo_producto = GlobalDato.id
	AND OrdenDescuento.periodo_ini <= vPERIODO
	AND IF(Socio.activo = 0,IFNULL(OrdenDescuento.periodo_hasta,vPERIODO),IF(ISNULL(OrdenDescuento.periodo_hasta) AND OrdenDescuento.activo = 1,'999999',OrdenDescuento.periodo_hasta)) > vPERIODO
	AND OrdenDescuento.persona_beneficio_id = PersonaBeneficio.id
	AND PersonaBeneficio.codigo_beneficio = vORGANISMO
	AND OrdenDescuento.permanente = 1;



IF vORDEN_ID <> 0 THEN

	set @orden_descuento_cuota_id = null;
	select odc.id INTO @orden_descuento_cuota_id from orden_descuento_cuotas odc, persona_beneficios be 
			where odc.orden_descuento_id = vORDEN_ID and odc.periodo = vPERIODO
			and odc.persona_beneficio_id = be.id    
			and odc.tipo_cuota = vTIPO_CUOTA
            and odc.estado <> 'B'
			and be.codigo_beneficio = vORGANISMO;

	SET @IMPORTE_CUOTA_SERVICIO = 0;
	SET @importe_fijo_producto = 0;

	-- ----------------------------------------------------------
	-- SACO EL IMPORTE DE LA TABLA DE PRODUCTOS
    -- ----------------------------------------------------------
	select MutualProducto.importe_fijo 
	INTO @importe_fijo_producto
	from mutual_productos as MutualProducto 
	where MutualProducto.tipo_producto = vTIPO_PRODUCTO 
    and MutualProducto.proveedor_id = vPROVEEDOR_ID 
    AND importe_fijo <> 0;

	SET @IMPORTE_CUOTA_SERVICIO = IF(vIMPORTE_ORDEN_CUOTA = 0,@importe_fijo_producto,vIMPORTE_ORDEN_CUOTA);

	-- ----------------------------------------------------------
    -- SERVICIOS
    -- ----------------------------------------------------------
	IF vTIPO_ORDEN = 'SERV' THEN

        -- SACO LOS VALORES VIGENTES
        
		select mutual_servicio_valores.id, mutual_servicio_valores.mutual_servicio_id, importe_titular, importe_adicional, 
		costo_titular, costo_adicional, periodo_vigencia, fecha_vigencia 
        into @servicio_id, @mutual_servicio_id, @importe_titular, @importe_adicional, 
        @costo_titular, @costo_adicional, @periodo_vigencia, @fecha_vigencia         
		from mutual_servicio_valores
		inner join mutual_servicio_solicitudes on (mutual_servicio_solicitudes.mutual_servicio_id = mutual_servicio_valores.mutual_servicio_id)
		where codigo_organismo = vORGANISMO 
		and periodo_vigencia <= vPERIODO
		and mutual_servicio_solicitudes.id = vSOLICITUD
		order by periodo_vigencia desc limit 1;        
        

        SET @IMPORTE_SERVICIO_MENSUAL = @importe_titular + ifnull((SELECT ROUND(COUNT(*) * @costo_adicional,2) 
        FROM mutual_servicio_solicitud_adicionales
        where mutual_servicio_solicitud_id = vSOLICITUD 
        and ifnull(periodo_hasta,'000000') <=  vPERIODO),0);
        
        UPDATE mutual_servicio_solicitudes set importe_mensual = @importe_titular,
        importe_mensual_total = @IMPORTE_SERVICIO_MENSUAL
        where id = vSOLICITUD;
        
        update mutual_servicio_solicitud_adicionales
        set importe_mensual = @costo_adicional
        where mutual_servicio_solicitud_id = vSOLICITUD 
        and ifnull(periodo_hasta,'000000') <=  vPERIODO;
        
        update orden_descuentos
        set importe_total = @IMPORTE_SERVICIO_MENSUAL, importe_cuota = @IMPORTE_SERVICIO_MENSUAL
        where id = vORDEN_ID;
        
        SET @IMPORTE_CUOTA_SERVICIO = round(@IMPORTE_SERVICIO_MENSUAL,2);
        
    END IF;
	
    SET @IMPORTE_CUOTA_SERVICIO = IF(vPERIODO_HASTA > vPERIODO,@IMPORTE_CUOTA_SERVICIO,0);

	IF @IMPORTE_CUOTA_SERVICIO > 0 THEN
    
		CALL SP_VENCIMIENTOS(NULL,
		vPROVEEDOR_ID,vORGANISMO,vPERIODO,vFECHA,
		@PERIODO_INI,@VTO_SOCIO,@VTO_PROVEEDOR,@ULTIMO_PERIODO); 
        
    IF @orden_descuento_cuota_id IS NOT NULL AND vORDEN_ID IS NOT NULL THEN 
		UPDATE orden_descuento_cuotas 
        SET importe = @IMPORTE_CUOTA_SERVICIO 
        WHERE id = @orden_descuento_cuota_id;
    ELSE 
    
		IF vORDEN_ID IS NOT NULL THEN
			INSERT INTO orden_descuento_cuotas(orden_descuento_id, 
			socio_id, persona_beneficio_id, tipo_orden_dto, 
			tipo_producto, tipo_cuota, periodo, estado, situacion, 
			vencimiento, vencimiento_proveedor, 
			nro_cuota, importe, proveedor_id, 
			nro_referencia_proveedor) 
			VALUES(vORDEN_ID,vSOCIO_ID,vBENEFICIO_ID,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,
			vPERIODO,'A','MUTUSICUMUTU',@VTO_SOCIO,@VTO_PROVEEDOR,0,
			@IMPORTE_CUOTA_SERVICIO,vPROVEEDOR_ID,vNRO_REFERENCIA_PROVEEDOR);
        END IF;
    END IF;        
    
    END IF;

	-- select @IMPORTE_CUOTA_SERVICIO,vCODIGO_ORGANISMO,vORDEN_ID,vSOLICITUD,vFECHA,vPERIODO_HASTA,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,vBENEFICIO_ID,vPROVEEDOR_ID,vNRO_REFERENCIA_PROVEEDOR,vIMPORTE_ORDEN_CUOTA,vACTIVO;

END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`mutualam`@`%` PROCEDURE `SP_LIQUIDA_CUOTA_SOCIAL`(
vSOCIO_ID INT(11),
vPERIODO VARCHAR(6),
vORGANISMO VARCHAR(12)
)
BEGIN
DECLARE vCODIGO_ORGANISMO VARCHAR(12);
DECLARE vORDEN_ID INT(11);
DECLARE vFECHA DATE;
DECLARE vPERIODO_HASTA VARCHAR(6);
DECLARE vTIPO_ORDEN VARCHAR(4);
DECLARE vTIPO_PRODUCTO VARCHAR(12);
DECLARE vTIPO_CUOTA VARCHAR(12);
DECLARE vBENEFICIO_ID INT(11);
DECLARE vPROVEEDOR_ID INT(11);
DECLARE vNRO_REFERENCIA_PROVEEDOR VARCHAR(10);
DECLARE vIMPORTE_ORDEN_CUOTA DECIMAL(10,2);
DECLARE vACTIVO BOOLEAN;

SELECT
	PersonaBeneficio.codigo_beneficio,
	OrdenDescuento.id,
    OrdenDescuento.fecha,
    ifnull(OrdenDescuento.periodo_hasta,'999912'),
	OrdenDescuento.tipo_orden_dto,
	OrdenDescuento.tipo_producto,
    GlobalDato.concepto_2,
	OrdenDescuento.persona_beneficio_id,
	OrdenDescuento.proveedor_id,
	OrdenDescuento.nro_referencia_proveedor,
	OrdenDescuento.importe_cuota,
	OrdenDescuento.activo
INTO vCODIGO_ORGANISMO,vORDEN_ID,vFECHA,vPERIODO_HASTA,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,vBENEFICIO_ID,vPROVEEDOR_ID,vNRO_REFERENCIA_PROVEEDOR,vIMPORTE_ORDEN_CUOTA,vACTIVO    
FROM 
	orden_descuentos as OrdenDescuento, 
	socios as Socio,
	persona_beneficios as PersonaBeneficio,
    global_datos as GlobalDato
WHERE
	Socio.id = vSOCIO_ID  
	AND OrdenDescuento.socio_id = Socio.id 
	AND OrdenDescuento.tipo_orden_dto = 'CMUTU'
	AND OrdenDescuento.tipo_producto = 'MUTUPROD0003'
    AND OrdenDescuento.tipo_producto = GlobalDato.id
	AND OrdenDescuento.periodo_ini <= vPERIODO
	AND IF(Socio.activo = 0,IFNULL(OrdenDescuento.periodo_hasta,vPERIODO),IF(ISNULL(OrdenDescuento.periodo_hasta) AND OrdenDescuento.activo = 1,'999999',OrdenDescuento.periodo_hasta)) > vPERIODO
	AND OrdenDescuento.persona_beneficio_id = PersonaBeneficio.id
	AND PersonaBeneficio.codigo_beneficio = vORGANISMO
	AND OrdenDescuento.permanente = 1
	AND OrdenDescuento.activo = 1;


set @orden_descuento_cuota_id = null;
select odc.id INTO @orden_descuento_cuota_id from orden_descuento_cuotas odc, persona_beneficios be 
		where odc.orden_descuento_id = vORDEN_ID and odc.periodo = vPERIODO
		and odc.persona_beneficio_id = be.id    
		and odc.tipo_cuota = 'MUTUTCUOCSOC' and odc.estado <> 'B'
		and be.codigo_beneficio = vORGANISMO;

select decimal_1,logico_1 into @importe_cuota_social,@liquida_solo_deuda 
from global_datos where id = concat('MUTUCUOS',substring(vORGANISMO,9,4))
and logico_1 = 1;

IF substring(vORGANISMO,9,2) = '66' THEN
	SELECT importe_cuota_social into @importe_cuota_social FROM socios WHERE id = vSOCIO_ID;
END IF;

-- ------------------------------------------------------------------
-- ANALIZO LA CUOTA SOCIAL DIFERENCIADA
-- ------------------------------------------------------------------
SET @cuota_social_diferenciada = 0;
SELECT MutualProducto.cuota_social_diferenciada, COUNT(*) 
into @cuota_social_diferenciada,@cantidad
FROM orden_descuentos AS OrdenDescuento 
INNER JOIN mutual_productos AS MutualProducto ON
(
	MutualProducto.tipo_orden_dto = OrdenDescuento.tipo_orden_dto
	AND MutualProducto.tipo_producto = OrdenDescuento.tipo_producto
	AND MutualProducto.proveedor_id = OrdenDescuento.proveedor_id
)	
WHERE OrdenDescuento.socio_id = vSOCIO_ID
AND OrdenDescuento.activo = 1
AND OrdenDescuento.tipo_orden_dto <> 'CMUTU'
AND MutualProducto.cuota_social_diferenciada <> 0
AND OrdenDescuento.socio_id NOT IN
(SELECT socio_id FROM orden_descuentos WHERE tipo_orden_dto <> 'CMUTU'
AND proveedor_id <> OrdenDescuento.proveedor_id)
GROUP BY MutualProducto.cuota_social_diferenciada
ORDER BY MutualProducto.cuota_social_diferenciada DESC;

IF @cantidad = 1 THEN SET @cuota_social_diferenciada = 0;
END IF;

IF (SELECT COUNT(*) FROM orden_descuentos WHERE socio_id = vSOCIO_ID and tipo_orden_dto <> 'CMUTU' and permanente = 0) > 1 THEN SET @cuota_social_diferenciada = 0;
END IF;

IF @cuota_social_diferenciada = 0 THEN
	SELECT cuota_social_diferenciada INTO @cuota_social_diferenciada FROM mutual_productos where tipo_producto = vTIPO_PRODUCTO
    and proveedor_id = vPROVEEDOR_ID ORDER BY cuota_social_diferenciada DESC LIMIT 1;
END IF;

IF @cuota_social_diferenciada <> 0 THEN
	SET @importe_cuota_social = @cuota_social_diferenciada;
END IF;


IF @liquida_solo_deuda = TRUE AND
	(select 
	SUM(ABS(importe) - ifnull((select sum(ABS(importe))from orden_descuento_cobro_cuotas
	where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0))
	as deuda
	from 
	orden_descuento_cuotas
	where 
	socio_id = vSOCIO_ID and estado <> 'B' 
	AND periodo <= vPERIODO
	AND proveedor_id IN (SELECT id FROM proveedores WHERE genera_cuota_social = 1 AND id <> 18)
	group by socio_id) <= 0 THEN   
    SET @importe_cuota_social = 0;
END IF;


IF @importe_cuota_social > 0 THEN

	CALL SP_VENCIMIENTOS(NULL,
	vPROVEEDOR_ID,vORGANISMO,vPERIODO,vFECHA,
	@PERIODO_INI,@VTO_SOCIO,@VTO_PROVEEDOR,@ULTIMO_PERIODO);
    
    
    IF @orden_descuento_cuota_id IS NOT NULL AND vORDEN_ID IS NOT NULL THEN 
		UPDATE orden_descuento_cuotas 
        SET importe = @importe_cuota_social 
        WHERE id = @orden_descuento_cuota_id;
    ELSE 
    
		IF vORDEN_ID IS NOT NULL THEN
			INSERT INTO orden_descuento_cuotas(orden_descuento_id, 
			socio_id, persona_beneficio_id, tipo_orden_dto, 
			tipo_producto, tipo_cuota, periodo, estado, situacion, 
			vencimiento, vencimiento_proveedor, 
			nro_cuota, importe, proveedor_id, 
			nro_referencia_proveedor) 
			VALUES(vORDEN_ID,vSOCIO_ID,vBENEFICIO_ID,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,
			vPERIODO,'A','MUTUSICUMUTU',@VTO_SOCIO,@VTO_PROVEEDOR,0,
			@importe_cuota_social,vPROVEEDOR_ID,vNRO_REFERENCIA_PROVEEDOR);
        END IF;
    END IF;

	/*
	SELECT @orden_descuento_cuota_id,@importe_cuota_social,vCODIGO_ORGANISMO,vORDEN_ID,
	vFECHA,vPERIODO_HASTA,vTIPO_ORDEN,vTIPO_PRODUCTO,vTIPO_CUOTA,vBENEFICIO_ID,vPROVEEDOR_ID,
	vNRO_REFERENCIA_PROVEEDOR,vIMPORTE_ORDEN_CUOTA,vACTIVO,
	@VTO_SOCIO,@VTO_PROVEEDOR,@ULTIMO_PERIODO;
	*/

END IF;

    

    
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`mutualam`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU`(
vSOCIO_ID INT(11),
vPERIODO VARCHAR(6),
vORGANISMO VARCHAR(12),
vPRE_IMPUTACION BOOLEAN
)
BEGIN

-- CALL SP_LIQUIDA_DEUDA(97,'201502','MUTUCORG2202',127,FALSE,'MUTUSICUMUTU');
DECLARE vLIQUIDACION_ID INT(11);
SELECT id into vLIQUIDACION_ID FROM liquidaciones where periodo = vPERIODO 
and codigo_organismo = vORGANISMO;

-- //////////////////////////////////////////////////////////////////////
-- BORRO LA LIQUIDACION ANTERIOR
-- //////////////////////////////////////////////////////////////////////

delete from mutual_adicional_pendientes where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
delete from liquidacion_cuotas where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
delete from liquidacion_socios where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;

-- //////////////////////////////////////////////////////////////////////
-- GENERO LA CUOTA SOCIAL y CUOTA SERVICIOS
-- //////////////////////////////////////////////////////////////////////
CALL SP_LIQUIDA_CUOTA_SOCIAL(vSOCIO_ID,vPERIODO,vORGANISMO);
CALL SP_LIQUIDA_CUOTA_SERVICIOS(vSOCIO_ID,vPERIODO,vORGANISMO);

-- //////////////////////////////////////////////////////////////////////
-- VERIFICAR QUE NO TENGA STOP DEBIT
-- //////////////////////////////////////////////////////////////////////

select calificacion into @CALIFICACION from socio_calificaciones where socio_id = vSOCIO_ID order by created desc limit 1;
SET @STOP_DEBIT = IF(IFNULL(@CALIFICACION,'') = 'MUTUCALISDEB',TRUE,FALSE);

-- //////////////////////////////////////////////////////////////////////
-- SACO LAS CUOTAS ADEUDADAS
-- //////////////////////////////////////////////////////////////////////
IF vPRE_IMPUTACION = FALSE THEN
	insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,
	orden_descuento_id,orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,
	tipo_cuota,periodo_cuota,proveedor_id,vencida,importe,
	saldo_actual,codigo_organismo)
	SELECT 
		vLIQUIDACION_ID,
		OrdenDescuentoCuota.socio_id,
		OrdenDescuentoCuota.persona_beneficio_id,
		OrdenDescuentoCuota.orden_descuento_id,
		OrdenDescuentoCuota.id,
		OrdenDescuentoCuota.tipo_orden_dto,
		OrdenDescuentoCuota.tipo_producto,
		OrdenDescuentoCuota.tipo_cuota,
		OrdenDescuentoCuota.periodo,
		OrdenDescuentoCuota.proveedor_id,
		IF(OrdenDescuentoCuota.periodo = vPERIODO, 0 , 1) as vencida,
		OrdenDescuentoCuota.importe,
		OrdenDescuentoCuota.importe - IFNULL((SELECT SUM(cocu.importe)
		FROM orden_descuento_cobro_cuotas cocu
		WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0) AS saldo_actual,    
		PersonaBeneficio.codigo_beneficio
	  
	FROM orden_descuento_cuotas AS OrdenDescuentoCuota
	INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
	WHERE 
		OrdenDescuentoCuota.socio_id = vSOCIO_ID
		AND OrdenDescuentoCuota.estado <> 'B' 
		AND PersonaBeneficio.codigo_beneficio = vORGANISMO
		AND OrdenDescuentoCuota.periodo <= vPERIODO
		-- AND OrdenDescuentoCuota.situacion = vSITUACION
		AND OrdenDescuentoCuota.importe > IFNULL((SELECT SUM(cocu.importe)
		FROM orden_descuento_cobro_cuotas cocu, orden_descuento_cobros co
		WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
		AND cocu.orden_descuento_cobro_id = co.id
		AND co.periodo_cobro <= vPERIODO),0)
        AND @STOP_DEBIT = FALSE;
        
ELSE
	insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,
	orden_descuento_id,orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,
	tipo_cuota,periodo_cuota,proveedor_id,vencida,importe,
	saldo_actual,codigo_organismo)
	SELECT 
		vLIQUIDACION_ID,
		OrdenDescuentoCuota.socio_id,
		OrdenDescuentoCuota.persona_beneficio_id,
		OrdenDescuentoCuota.orden_descuento_id,
		OrdenDescuentoCuota.id,
		OrdenDescuentoCuota.tipo_orden_dto,
		OrdenDescuentoCuota.tipo_producto,
		OrdenDescuentoCuota.tipo_cuota,
		OrdenDescuentoCuota.periodo,
		OrdenDescuentoCuota.proveedor_id,
		IF(OrdenDescuentoCuota.periodo = vPERIODO, 0 , 1) as vencida,
		OrdenDescuentoCuota.importe,
		OrdenDescuentoCuota.importe - IFNULL((SELECT SUM(cocu.importe)
		FROM orden_descuento_cobro_cuotas cocu
		WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0) 
        -
        (SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas 
		WHERE orden_descuento_cuota_id = OrdenDescuentoCuota.id
		AND para_imputar = 1 AND imputada = 0 AND orden_descuento_cobro_id = 0
		order by liquidacion_id desc limit 1)
        AS saldo_actual,    
		PersonaBeneficio.codigo_beneficio
	  
	FROM orden_descuento_cuotas AS OrdenDescuentoCuota
	INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
	WHERE 
		OrdenDescuentoCuota.socio_id = vSOCIO_ID
		AND OrdenDescuentoCuota.estado <> 'B' 
		AND PersonaBeneficio.codigo_beneficio = vORGANISMO
		AND OrdenDescuentoCuota.periodo <= vPERIODO
		-- AND OrdenDescuentoCuota.situacion = vSITUACION
		AND OrdenDescuentoCuota.importe > (IFNULL((SELECT SUM(cocu.importe)
		FROM orden_descuento_cobro_cuotas cocu, orden_descuento_cobros co
		WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
		AND cocu.orden_descuento_cobro_id = co.id
		AND co.periodo_cobro <= vPERIODO),0) + (SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas 
		WHERE orden_descuento_cuota_id = OrdenDescuentoCuota.id
		AND para_imputar = 1 AND imputada = 0 AND orden_descuento_cobro_id = 0
		order by liquidacion_id desc limit 1))
        AND @STOP_DEBIT = FALSE;
        
END IF;

-- /////////////////////////////////////////////////////////////////////
-- CALCULAR LOS ADICIONALES
-- /////////////////////////////////////////////////////////////////////   
CALL SP_LIQUIDA_DEUDA_CBU_ADICIONALES(vSOCIO_ID,vPERIODO,vORGANISMO,vLIQUIDACION_ID);

  
-- /////////////////////////////////////////////////////////////////////
-- GENERO LA CABECERA DE LA LIQUIDACION
-- ///////////////////////////////////////////////////////////////////// 
insert into liquidacion_socios(liquidacion_id,socio_id,
persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
codigo_empresa,codigo_reparticion,turno_pago,periodo,importe_dto,importe_adebitar,
formula_criterio_deuda)
SELECT 
	LiquidacionCuota.liquidacion_id,
	LiquidacionCuota.socio_id,
	LiquidacionCuota.persona_beneficio_id,
    LiquidacionCuota.codigo_organismo,
    PersonaBeneficio.banco_id,
    PersonaBeneficio.nro_sucursal,
    PersonaBeneficio.tipo_cta_bco,
    PersonaBeneficio.nro_cta_bco,
    PersonaBeneficio.cbu,
    Persona.tipo_documento,
    Persona.documento,
    concat(Persona.apellido,', ',Persona.nombre),
    Persona.id,
    Persona.cuit_cuil,
    PersonaBeneficio.codigo_empresa,
    PersonaBeneficio.codigo_reparticion,
    PersonaBeneficio.turno_pago,
    1,
	sum(saldo_actual) as deuda,
	sum(saldo_actual) as importe_adebitar,
    '*** IMPORTE PERIODO ***'
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (
    PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id
    and PersonaBeneficio.codigo_beneficio = LiquidacionCuota.codigo_organismo)
    INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
	AND LiquidacionCuota.socio_id = vSOCIO_ID
	AND LiquidacionCuota.periodo_cuota = vPERIODO					
GROUP BY
	LiquidacionCuota.codigo_organismo,
	LiquidacionCuota.socio_id,
    PersonaBeneficio.turno_pago,
	PersonaBeneficio.cbu
UNION
SELECT 
	LiquidacionCuota.liquidacion_id,
	LiquidacionCuota.socio_id,
	LiquidacionCuota.persona_beneficio_id,
    LiquidacionCuota.codigo_organismo,
    PersonaBeneficio.banco_id,
    PersonaBeneficio.nro_sucursal,
    PersonaBeneficio.tipo_cta_bco,
    PersonaBeneficio.nro_cta_bco,
    PersonaBeneficio.cbu,
    Persona.tipo_documento,
    Persona.documento,
    concat(Persona.apellido,', ',Persona.nombre),
    Persona.id,
    Persona.cuit_cuil,
    PersonaBeneficio.codigo_empresa,
    PersonaBeneficio.codigo_reparticion,
    PersonaBeneficio.turno_pago,
    0,
	sum(saldo_actual) as deuda,
	sum(saldo_actual) as importe_adebitar,
    '*** IMPORTE MORA ***'
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (
    PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id
    and PersonaBeneficio.codigo_beneficio = LiquidacionCuota.codigo_organismo)
    INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
	AND LiquidacionCuota.socio_id = vSOCIO_ID
	AND LiquidacionCuota.periodo_cuota < vPERIODO					
GROUP BY
	LiquidacionCuota.codigo_organismo,
	LiquidacionCuota.socio_id,
    PersonaBeneficio.turno_pago,
	PersonaBeneficio.cbu;

-- /////////////////////////////////////////////////////////////////////
-- TRATAMIENTO DE LA MORA
-- ///////////////////////////////////////////////////////////////////// 
CALL SP_LIQUIDA_DEUDA_CBU_GESTION_MORA(vSOCIO_ID,vORGANISMO,vLIQUIDACION_ID);
CALL SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO(vSOCIO_ID,vORGANISMO,vLIQUIDACION_ID);
-- /////////////////////////////////////////////////////////////////////
-- VERIFICO ACUERDOS DE DEBITO
-- /////////////////////////////////////////////////////////////////////    
CALL SP_LIQUIDA_DEUDA_CBU_ACUERDO_DEBITO(vSOCIO_ID,vLIQUIDACION_ID);
CALL SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO_RENUMERA(vSOCIO_ID,vLIQUIDACION_ID);
    
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`mutualam`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_ACUERDO_DEBITO`(
vSOCIO_ID INT(11),
vLIQUIDACION_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;

DECLARE vMONTO_ACUERDO DECIMAL(10,2) DEFAULT 0;
DECLARE vMONTO_MAX_DTO_BENEFICIO DECIMAL(10,2) DEFAULT 0;
DECLARE vCON_ACUERDO BOOLEAN DEFAULT FALSE;
DECLARE vBENEFICIO_ID INT(11) DEFAULT 0;

DECLARE vSALDO DECIMAL(10,2);
DECLARE vSALDO_ACUMULADO DECIMAL(10,2);

DECLARE c_beneficios_reset_acuerdo CURSOR FOR 
select PersonaBeneficio.id from persona_beneficios as PersonaBeneficio
where acuerdo_debito > (select sum(saldo_actual)        
from liquidacion_cuotas
where liquidacion_cuotas.liquidacion_id = vLIQUIDACION_ID and 
liquidacion_cuotas.socio_id = vSOCIO_ID and 
liquidacion_cuotas.persona_beneficio_id = PersonaBeneficio.id)
group by PersonaBeneficio.id;


DECLARE c_beneficios_acuerdo CURSOR FOR 
SELECT PersonaBeneficio.id,PersonaBeneficio.acuerdo_debito,
PersonaBeneficio.importe_max_registro_cbu 
FROM 
	liquidacion_cuotas as LiquidacionCuota 
	INNER JOIN persona_beneficios as PersonaBeneficio on (
    PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id
    and PersonaBeneficio.codigo_beneficio = LiquidacionCuota.codigo_organismo)
WHERE 
	LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
	AND LiquidacionCuota.socio_id = vSOCIO_ID
	AND PersonaBeneficio.acuerdo_debito <> 0
    GROUP BY PersonaBeneficio.id;
-- ////////////////////////////////////////////////////////////////////////
-- ACUERDO DE DEBITO
-- ////////////////////////////////////////////////////////////////////////
DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row = 1;
-- DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row_2 = 1;

OPEN c_beneficios_reset_acuerdo;
c1_loop_beneficios: LOOP
FETCH c_beneficios_reset_acuerdo INTO vBENEFICIO_ID;
		IF (l_last_row = 1) THEN
			LEAVE c1_loop_beneficios; 
		END IF;
        UPDATE persona_beneficios set acuerdo_debito = 0 where id = vBENEFICIO_ID;       
END LOOP c1_loop_beneficios; 
CLOSE c_beneficios_reset_acuerdo;

SET vSALDO = 0;
SET vSALDO_ACUMULADO = 0;
SET @REGISTRO = 1;
SET l_last_row = 0;
OPEN c_beneficios_acuerdo;
c1_loop_beneficios_acuerdo: LOOP
FETCH c_beneficios_acuerdo INTO vBENEFICIO_ID,vMONTO_ACUERDO,vMONTO_MAX_DTO_BENEFICIO;
		
        IF (l_last_row = 1) THEN
			LEAVE c1_loop_beneficios_acuerdo; 
		END IF;

		SET @ULTIMO_ID = 0;
        SET vSALDO = 0;

		DELETE FROM liquidacion_socios where 
		liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID and
		persona_beneficio_id = vBENEFICIO_ID;        



		IF CAST(vMONTO_ACUERDO / vMONTO_MAX_DTO_BENEFICIO AS UNSIGNED) > 1 AND vMONTO_MAX_DTO_BENEFICIO > 0 THEN
        
			SET vSALDO = vMONTO_ACUERDO;
            WHILE vSALDO > 50 DO
            
				SET @IMPO_DEBITO = vMONTO_MAX_DTO_BENEFICIO;
				
				IF vSALDO <= vMONTO_MAX_DTO_BENEFICIO THEN
					SET @IMPO_DEBITO = vSALDO;
				END IF;            
            
				
                insert into liquidacion_socios(liquidacion_id,socio_id,
				persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
				nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
				codigo_empresa,codigo_reparticion,turno_pago,periodo,importe_dto,importe_adebitar,
				formula_criterio_deuda)
                
				SELECT 
					LiquidacionCuota.liquidacion_id,
					LiquidacionCuota.socio_id,
					LiquidacionCuota.persona_beneficio_id,
					LiquidacionCuota.codigo_organismo,
					PersonaBeneficio.banco_id,
					PersonaBeneficio.nro_sucursal,
					PersonaBeneficio.tipo_cta_bco,
					PersonaBeneficio.nro_cta_bco,
					PersonaBeneficio.cbu,
					Persona.tipo_documento,
					Persona.documento,
					concat(Persona.apellido,', ',Persona.nombre),
					Persona.id,
					Persona.cuit_cuil,
					PersonaBeneficio.codigo_empresa,
					PersonaBeneficio.codigo_reparticion,
					PersonaBeneficio.turno_pago,
					0,
					IF(@REGISTRO = 1,vMONTO_ACUERDO,0) as deuda,
					@IMPO_DEBITO,
					concat('*** CON ACUERDO DE DEBITO ', vMONTO_ACUERDO ,' | FRACCION = ',@IMPO_DEBITO,' ***')
				FROM 
					liquidacion_cuotas as LiquidacionCuota 
					INNER JOIN persona_beneficios as PersonaBeneficio on (
					PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id
					and PersonaBeneficio.codigo_beneficio = LiquidacionCuota.codigo_organismo)
					INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
				WHERE 
					LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
					AND LiquidacionCuota.socio_id = vSOCIO_ID
					AND PersonaBeneficio.id = vBENEFICIO_ID
                GROUP BY PersonaBeneficio.id;    
                
				SET @ULTIMO_ID = LAST_INSERT_ID(); 
            
				-- SELECT vBENEFICIO_ID,vMONTO_ACUERDO,vMONTO_MAX_DTO_BENEFICIO,vSALDO,vSALDO_ACUMULADO;
                
                SET vSALDO_ACUMULADO = vSALDO_ACUMULADO + @IMPO_DEBITO;
				
                SET vSALDO = vSALDO - @IMPO_DEBITO;
                SET @REGISTRO = @REGISTRO + 1;  
            
            END WHILE;
            
			IF vSALDO > 0 THEN
				UPDATE liquidacion_socios 
				SET importe_adebitar = importe_adebitar + vSALDO WHERE id = @ULTIMO_ID; 
			END IF;             
        
        ELSE
        
			insert into liquidacion_socios(liquidacion_id,socio_id,
			persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
			nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
			codigo_empresa,codigo_reparticion,turno_pago,periodo,importe_dto,importe_adebitar,
			formula_criterio_deuda)
			SELECT 
				LiquidacionCuota.liquidacion_id,
				LiquidacionCuota.socio_id,
				LiquidacionCuota.persona_beneficio_id,
				LiquidacionCuota.codigo_organismo,
				PersonaBeneficio.banco_id,
				PersonaBeneficio.nro_sucursal,
				PersonaBeneficio.tipo_cta_bco,
				PersonaBeneficio.nro_cta_bco,
				PersonaBeneficio.cbu,
				Persona.tipo_documento,
				Persona.documento,
				concat(Persona.apellido,', ',Persona.nombre),
				Persona.id,
				Persona.cuit_cuil,
				PersonaBeneficio.codigo_empresa,
				PersonaBeneficio.codigo_reparticion,
				PersonaBeneficio.turno_pago,
				0,
				sum(saldo_actual) as deuda,
				PersonaBeneficio.acuerdo_debito,
				concat('*** CON ACUERDO DE DEBITO = ',vMONTO_ACUERDO,' ***')
			FROM 
				liquidacion_cuotas as LiquidacionCuota 
				INNER JOIN persona_beneficios as PersonaBeneficio on (
				PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id
				and PersonaBeneficio.codigo_beneficio = LiquidacionCuota.codigo_organismo)
				INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
			WHERE 
				LiquidacionCuota.liquidacion_id = vLIQUIDACION_ID
				AND LiquidacionCuota.socio_id = vSOCIO_ID
				AND PersonaBeneficio.id = vBENEFICIO_ID;        
        
        END IF;

        
END LOOP c1_loop_beneficios_acuerdo;  
CLOSE c_beneficios_acuerdo;       
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`mutualam`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_ADICIONALES`(
	vSOCIO_ID INT(11),
	vPERIODO VARCHAR(6),
	vORGANISMO VARCHAR(12),
	vLIQUIDACION_ID INT(11)
)
BEGIN
-- CALL SP_LIQUIDA_DEUDA_ADICIONALES(97,'201502','MUTUCORG2202',127);
DECLARE l_last_row INT DEFAULT 0;
DECLARE vPROVEEDOR_ID INT(11) DEFAULT 0;
DECLARE vIMPUTAR_PROVEEDOR_ID INT(11) DEFAULT 0;
DECLARE vTIPO CHAR(1);
DECLARE vVALOR DECIMAL(10,2);
DECLARE vDEVENGA BOOLEAN;
DECLARE vCALCULO INT(11);
DECLARE vTIPO_CUOTA VARCHAR(12);
DECLARE c_adicionales CURSOR FOR 
select proveedor_id,imputar_proveedor_id,
tipo,valor,devengado_previo,deuda_calcula,tipo_cuota
from mutual_adicionales
where codigo_organismo = vORGANISMO
and activo = 1 and valor > 0
and ifnull(periodo_desde,'000000') <= vPERIODO
and ifnull(periodo_hasta,'999912') >= vPERIODO;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;
OPEN c_adicionales;
c1_loop: LOOP
	FETCH c_adicionales INTO vPROVEEDOR_ID,vIMPUTAR_PROVEEDOR_ID,vTIPO,vVALOR,vDEVENGA,vCALCULO,vTIPO_CUOTA;
		
        IF (l_last_row = 1) THEN
			LEAVE c1_loop; 
		END IF;	    
        
        
        
        

		-- select vPROVEEDOR_ID,vIMPUTAR_PROVEEDOR_ID,vTIPO,vVALOR,vDEVENGA,vCALCULO,vTIPO_CUOTA;
		
        IF (vVALOR <> 0) THEN
        
			set @saldo = 0;
            set @ordenDtoId = 0;
            set @beneficioId = 0;
            set @tipoProducto = null;
        
			-- SACAR LA ORDEN DE DESCUENTO A DONDE SE VA A CARGAR EN BASE AL PROVEEDOR AL CUAL SE IMPUTA
			set @STMT = CONCAT('select persona_beneficio_id,(select id from orden_descuentos where tipo_orden_dto = \'CMUTU\' and activo = 1 and socio_id = liquidacion_cuotas.socio_id order by id desc limit 1) as orden_id,(select tipo_producto from orden_descuentos where tipo_orden_dto = \'CMUTU\' and activo = 1 and socio_id = liquidacion_cuotas.socio_id order by id desc limit 1) as tipo_producto,sum(saldo_actual) into @beneficioId,@ordenDtoId,@tipoProducto,@saldo FROM liquidacion_cuotas where liquidacion_id = ? and socio_id = ? and ifnull(mutual_adicional_pendiente_id,0) = 0');
			
            IF vPROVEEDOR_ID is not null THEN
				SET @STMT = CONCAT(@STMT,' AND proveedor_id = ? ');             
			END IF;
            IF vCALCULO = 1 THEN
				SET @STMT = CONCAT(@STMT,' AND periodo_cuota <= ? '); 
            END IF;
            IF vCALCULO = 2 THEN
				SET @STMT = CONCAT(@STMT,' AND periodo_cuota < ? '); 
            END IF;            
            IF vCALCULO = 3 THEN
				SET @STMT = CONCAT(@STMT,' AND periodo_cuota = ? '); 
            END IF;

            PREPARE STMT FROM @STMT;
            
            SET @LIQ = vLIQUIDACION_ID;
            SET @SOCIO = vSOCIO_ID;
            SET @PERIODO = vPERIODO;
            
            EXECUTE STMT USING @LIQ,@SOCIO,@PERIODO;
            
            DEALLOCATE PREPARE STMT;
			SET @ADICIONAL = 0;
			IF vVALOR <> 0 AND @saldo <> 0 THEN
				SET @ADICIONAL = IF(vTIPO = 'P',ROUND(@saldo * vVALOR / 100,2),vVALOR);
            END IF;
            
			IF @ADICIONAL <> 0 THEN
                
                
                insert into mutual_adicional_pendientes(liquidacion_id,socio_id,codigo_organismo,proveedor_id,
                tipo,deuda_calcula,valor,tipo_cuota,periodo,total_deuda,importe,
                orden_descuento_id,persona_beneficio_id)
                values(vLIQUIDACION_ID,vSOCIO_ID,vORGANISMO,vIMPUTAR_PROVEEDOR_ID,
                vTIPO,vCALCULO,vVALOR,vTIPO_CUOTA,vPERIODO,@saldo,@ADICIONAL,@ordenDtoId,@beneficioId);
                
                set @adicional_id = last_insert_id();
                
                insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,orden_descuento_id,
						orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,tipo_cuota,
						periodo_cuota,proveedor_id,vencida,importe,saldo_actual,codigo_organismo,
                        mutual_adicional_pendiente_id
				)
                values(vLIQUIDACION_ID,vSOCIO_ID,@beneficioId,
                @ordenDtoId,null,'CMUTU',@tipoProducto,vTIPO_CUOTA,
                vPERIODO,vIMPUTAR_PROVEEDOR_ID,0,@ADICIONAL,@ADICIONAL,vORGANISMO,@adicional_id);

                -- select @saldo,@ADICIONAL,@orden_dto_id,@beneficio_id;
                
            END IF;

        END IF;

END LOOP c1_loop;
CLOSE c_adicionales;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`mutualam`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_GESTION_MORA`(
vSOCIO_ID INT(11),
vORGANISMO VARCHAR(12),
vLIQUIDACION_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
DECLARE vID INT(11);
DECLARE vIMPORTE_DEBITO DECIMAL(10,2);
DECLARE vIMPORTE_DTO DECIMAL(10,2);
DECLARE vIMPORTE_DEBITO_CALCULADO DECIMAL(10,2);
DECLARE vFORMULA TEXT;
DECLARE cursor_mora CURSOR FOR 
SELECT id,importe_adebitar,importe_dto FROM liquidacion_socios
where liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID
and periodo = 0;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;



SET @LIMITE_1 = 100;
SET @LIMITE_2 = 200;

SET @TOPE_DEBITO_MAXIMO = 800;

SET vID = 0;
SET vIMPORTE_DEBITO = 0;
SET vIMPORTE_DTO = 0;
SET vIMPORTE_DEBITO_CALCULADO = 0;

SELECT entero_1,entero_2,decimal_2 INTO @LIMITE_1,@LIMITE_2,@TOPE_DEBITO_MAXIMO
FROM global_datos WHERE id = vORGANISMO;


OPEN cursor_mora;
c1_loop: LOOP
	FETCH cursor_mora INTO vID,vIMPORTE_DEBITO,vIMPORTE_DTO;
    
	IF (l_last_row = 1) THEN
		LEAVE c1_loop; 
	END IF;	      
    SET vFORMULA = '';
    SET vIMPORTE_DEBITO_CALCULADO = vIMPORTE_DEBITO;
    
    IF @LIMITE_1 >= vIMPORTE_DEBITO THEN
		SET vIMPORTE_DEBITO_CALCULADO = vIMPORTE_DEBITO;
        SET vFORMULA = CONCAT(vIMPORTE_DEBITO,' <' , @LIMITE_1,' ==> IMPORTE A DEBITAR: ',vIMPORTE_DEBITO_CALCULADO,' (TOTAL ATRASO)');
    END IF;
    

    IF @LIMITE_1 < vIMPORTE_DEBITO AND vIMPORTE_DEBITO <= @LIMITE_2 THEN
		SET vIMPORTE_DEBITO_CALCULADO = vIMPORTE_DEBITO / 2;
        SET vFORMULA = CONCAT(@LIMITE_1,' < ',vIMPORTE_DEBITO,' <= ' , @LIMITE_2,' ==> IMPORTE A DEBITAR: ',vIMPORTE_DEBITO_CALCULADO,' (TOTAL ATRASO / 2)');
    END IF;
    
    IF vIMPORTE_DEBITO > @LIMITE_2 THEN
		SET vIMPORTE_DEBITO_CALCULADO = vIMPORTE_DEBITO / 3;
        SET vFORMULA = CONCAT(vIMPORTE_DEBITO,' > ' , @LIMITE_2,' ==> IMPORTE A DEBITAR: ',vIMPORTE_DEBITO_CALCULADO,' (TOTAL ATRASO / 3)');
    END IF; 
    
    -- CONTROL DEL TOPE DE DEBITO CBU
    IF @TOPE_DEBITO_MAXIMO < vIMPORTE_DEBITO_CALCULADO THEN
		SET vIMPORTE_DEBITO_CALCULADO = @TOPE_DEBITO_MAXIMO;
        SET vFORMULA = CONCAT(vFORMULA,'\nCONTROL MONTO MAXIMO DEBITO CBU == > IMPORTE A DEBITAR: ',vIMPORTE_DEBITO_CALCULADO);
    END IF;

	update liquidacion_socios 
    set importe_adebitar = vIMPORTE_DEBITO_CALCULADO,
    importe_dto = vIMPORTE_DEBITO_CALCULADO,
    formula_criterio_deuda = vFORMULA where id = vID;
      
END LOOP c1_loop;

CLOSE cursor_mora;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`mutualam`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO`(
vSOCIO_ID INT(11),
vORGANISMO VARCHAR(12),
vLIQUIDACION_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
DECLARE vID INT(11);
DECLARE vBENEFICIO_ID INT(11);
DECLARE vIMPORTE_DEBITO DECIMAL(10,2);
DECLARE vIMPORTE_DEBITO_CALCULADO DECIMAL(10,2);
DECLARE vSALDO DECIMAL(10,2);
DECLARE vSALDO_ACUMULADO DECIMAL(10,2);
DECLARE vFORMULA TEXT;
DECLARE cursor_socio CURSOR FOR 
SELECT id,persona_beneficio_id,importe_adebitar FROM liquidacion_socios
where liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID
order by periodo;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;

SET @TOPE_POR_REGISTRO = 0;
SET vSALDO = 0;
SET vSALDO_ACUMULADO = 0;

SELECT decimal_1 INTO @TOPE_POR_REGISTRO
FROM global_datos WHERE id = vORGANISMO;

SET vID = 0;
SET vBENEFICIO_ID = 0;
SET vIMPORTE_DEBITO = 0;

OPEN cursor_socio;
c1_loop: LOOP
	FETCH cursor_socio INTO vID,vBENEFICIO_ID,vIMPORTE_DEBITO;
    
	IF (l_last_row = 1) THEN
		LEAVE c1_loop; 
	END IF;	
    
    SET @IMPO_MAX_DBTO_BENEFICIO = 0;
    SET vSALDO = 0;
    SET @IMPO_DEBITO = 0;
    SET @ULTIMO_ID = 0;
    
    SELECT importe_max_registro_cbu into @IMPO_MAX_DBTO_BENEFICIO 
    FROM persona_beneficios where id = vBENEFICIO_ID;
    
    IF @TOPE_POR_REGISTRO > @IMPO_MAX_DBTO_BENEFICIO AND @IMPO_MAX_DBTO_BENEFICIO > 0 THEN
		SET @TOPE_POR_REGISTRO = @IMPO_MAX_DBTO_BENEFICIO;
    END IF;
    
    IF @TOPE_POR_REGISTRO <> 0 THEN
		
        SET vSALDO = vIMPORTE_DEBITO;
        
        SET @REGISTRO = 1;
        -- SET vIMPORTE_DEBITO_CALCULADO = vIMPORTE_DEBITO / @CICLOS;
        
        WHILE vSALDO > 50 DO
        
			SET vSALDO_ACUMULADO = vSALDO_ACUMULADO + @TOPE_POR_REGISTRO;
            
            SET @IMPO_DEBITO = @TOPE_POR_REGISTRO;
            
            IF vSALDO < @TOPE_POR_REGISTRO THEN
				SET @IMPO_DEBITO = vSALDO;
            END IF;
            
            SET @IMPO_DEBITO = CAST(@IMPO_DEBITO AS DECIMAL(10,2));
            
            IF @REGISTRO = 1 THEN
			
				UPDATE liquidacion_socios 
				SET importe_adebitar = @IMPO_DEBITO,
                -- ,formula_criterio_deuda = concat(formula_criterio_deuda,'\n*** FRACCION: ',@IMPO_DEBITO,' ***'),
				registro = @REGISTRO WHERE id = vID; 
                
                SET @ULTIMO_ID = vID;
            
            ELSE
        
				insert into liquidacion_socios(liquidacion_id,socio_id,
				persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
				nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
				codigo_empresa,codigo_reparticion,turno_pago,periodo,importe_adebitar,
				formula_criterio_deuda)			
				SELECT liquidacion_id,socio_id,
				persona_beneficio_id,codigo_organismo,banco_id,sucursal,tipo_cta_bco,
				nro_cta_bco,cbu,tipo_documento,documento,apenom,persona_id,cuit_cuil,
				codigo_empresa,codigo_reparticion,turno_pago,periodo,@IMPO_DEBITO,
                formula_criterio_deuda
				-- concat(formula_criterio_deuda,'\n*** ACUERDO S/FRACCION: ',@TOPE_POR_REGISTRO,' ***')
                FROM liquidacion_socios where id = vID;
                
                SET @ULTIMO_ID = LAST_INSERT_ID();
			
            END IF;
			
            SET vSALDO = vSALDO - @IMPO_DEBITO;
            
			-- SELECT vID,vIMPORTE_DEBITO,@TOPE_POR_REGISTRO,@IMPO_DEBITO,vSALDO;
            
            SET @REGISTRO = @REGISTRO + 1;        
        
        END WHILE;
        
        IF vSALDO < 50 THEN
            UPDATE liquidacion_socios 
			SET importe_adebitar = importe_adebitar + vSALDO WHERE id = @ULTIMO_ID;
        END IF;        
        
     END IF;
	
    
    
END LOOP c1_loop;

CLOSE cursor_socio;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`mutualam`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_LIQUIDACION_SOCIO_RENUMERA`(
vSOCIO_ID INT(11),
vLIQUIDACION_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
DECLARE vID INT(11);
DECLARE cursor_socio CURSOR FOR 
SELECT id FROM liquidacion_socios
where liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID
order by periodo,importe_dto desc;

DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;

SET @REGISTRO = 1;

OPEN cursor_socio;
c1_loop: LOOP
	FETCH cursor_socio INTO vID;
    
	IF (l_last_row = 1) THEN
		LEAVE c1_loop; 
	END IF;	 
    
    SET @REGISTRO = @REGISTRO + 1;
    
	UPDATE liquidacion_socios 
	SET registro = @REGISTRO WHERE id = vID;
    
END LOOP c1_loop;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LIQUIDA_DEUDA_SCORING`(IN
vSOCIO_ID INT(11),vLIQUIDACION_ID INT(11))
BEGIN

select periodo into @periodo from liquidaciones where id = vLIQUIDACION_ID;
delete from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID;
insert into liquidacion_socio_scores(liquidacion_id,socio_id,`13`,`12`,`09`,`06`,`03`,`00`,cargos_adicionales,saldo_actual)
select liquidacion_id,socio_id, 
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 12 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 12 month),'%Y%m')  
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 9 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 9 month),'%Y%m')
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 6 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota  > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 6 month),'%Y%m') 
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 3 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota  > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 3 month),'%Y%m') 
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 0 month),'%Y%m')
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 0 month),'%Y%m')
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id <> 0
and lc.socio_id = lc2.socio_id),0),
sum(saldo_actual) as saldo_actual
from liquidacion_cuotas lc2 where liquidacion_id = vLIQUIDACION_ID
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

DELIMITER $$
CREATE DEFINER=`root`@`%` PROCEDURE `SP_POSICION_CONSOLIDADA`(
	vPID INT,
	vPer VARCHAR(6),
	vOrg VARCHAR(12),
	vProvId INT,
	vEmp VARCHAR(12),
	vTurno VARCHAR(50)
	)
BEGIN
	
	DECLARE vRows INT;
	DECLARE vCont INT;
	DECLARE done BOOLEAN DEFAULT FALSE;
	DECLARE v_last_row INT DEFAULT 0;
	DECLARE vTdocNdoc VARCHAR(50);
	DECLARE vApenom VARCHAR(255);
	DECLARE vSocioId INT(11) DEFAULT 0;
	
	DECLARE vSaldoActual DECIMAL;
	
	DECLARE cur_personas CURSOR FOR 
	SELECT 
	CONCAT(TRIM(GlobalDato.concepto_1),' ',TRIM(Persona.documento)),
	CONCAT(Persona.apellido,', ',Persona.nombre) AS apenom,
	Socio.id
	FROM personas AS Persona
	INNER JOIN socios AS Socio ON (Socio.persona_id = Persona.id)
	INNER JOIN global_datos AS GlobalDato ON(GlobalDato.id = Persona.tipo_documento)
	GROUP BY Socio.id
	ORDER BY Persona.apellido, Persona.nombre;
	
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	
	DELETE FROM asincrono_temporales WHERE asincrono_id = vPID;
	
	OPEN cur_personas;
	SET vRows = (SELECT FOUND_ROWS());
	SET vCont = 0;
	
	CALL STP_ASINCRONO(vPID,'P',vRows,vCont,'*** INICIANDO PROCESO ***');
	
	WHILE NOT isAsincronoStop(vPID) DO
	FETCH cur_personas INTO vTdocNdoc, vApenom, vSocioId;
	
		SET @query = CONCAT('	
			INSERT INTO asincrono_temporales
			(asincrono_id,clave_1,clave_2,clave_3,texto_1,texto_2,texto_3,
			texto_4,texto_5,texto_6,texto_7,
			decimal_1,decimal_2,
			decimal_3,decimal_4,entero_1,entero_2,entero_3,entero_4)
			SELECT ',vPID,',
			cuota.orden_descuento_id,
			cuota.proveedor_id,
			', vSocioId,',
			''',vTdocNdoc,''',''',vApenom,''',
			beneficio.codigo_beneficio,
			IF(SUBSTR(beneficio.codigo_beneficio,9,2) = 22,beneficio.codigo_empresa,''''),
			IF(SUBSTR(beneficio.codigo_beneficio,9,2) = 22,beneficio.turno_pago,''''),
			concat(orden.tipo_orden_dto,'' #'',orden.numero),
			IFNULL(cuota.nro_referencia_proveedor,0),
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''TOTAL_DEVENGADO'') AS TOTAL_DEVENGADO,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''SALDO_AVENCER'') AS SALDO_AVENCER,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''TOTAL_PAGADO'') AS TOTAL_PAGADO,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''SALDO_VENCIDO'') AS SALDO_VENCIDO,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''CUOTAS_DEVENGADAS'') AS CUOTAS_DEVENGADAS,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''CUOTAS_VENCIDAS'') AS CUOTAS_VENCIDAS,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''CUOTAS_AVENCER'') AS CUOTAS_AVENCER,
			FX_TOTALES_ORDEN(orden_descuento_id,',vPer,',cuota.proveedor_id,cuota.persona_beneficio_id,''CUOTAS_PAGAS'') AS CUOTAS_PAGAS  
			FROM orden_descuento_cuotas AS cuota 
			INNER JOIN persona_beneficios AS beneficio ON (beneficio.id = cuota.persona_beneficio_id)
			INNER JOIN orden_descuentos as orden on (orden.id = cuota.orden_descuento_id)
			WHERE 
				cuota.socio_id = ', vSocioId);
		IF vProvId IS NOT NULL THEN
			SET @query =  CONCAT(@query,' AND cuota.proveedor_id = ',vProvId);
		END IF;
		IF vOrg IS NOT NULL THEN
			SET @query =  CONCAT(@query,' AND beneficio.codigo_beneficio = ''',vOrg,'''');
		END IF;
		IF vEmp IS NOT NULL THEN
			SET @query =  CONCAT(@query,' AND beneficio.codigo_empresa = ''',vEmp,'''');
		END IF;
		IF vTurno IS NOT NULL THEN
			SET @query =  CONCAT(@query,' AND beneficio.turno_pago = ''',TRIM(vTurno),'''');
		END IF;						
		SET @query = CONCAT(@query,' GROUP BY cuota.orden_descuento_id,cuota.proveedor_id,cuota.persona_beneficio_id');	
	
	
		PREPARE smpt FROM @query;
		EXECUTE smpt;
		DEALLOCATE PREPARE smpt;
		DELETE FROM asincrono_temporales WHERE asincrono_id = vPID
		AND clave_3 = vSocioId AND decimal_4 = 0;
	
		CALL STP_ASINCRONO(vPID,'P',vRows,vCont,CONCAT('PROCESANDO: ',vSocioId,' *** ',vApenom));
	
		SELECT vCont + 1 INTO vCont;
		
	END WHILE;	
	CLOSE cur_personas;
	
	CALL STP_ASINCRONO(vPID,'F',vRows,vCont,'**** PROCESO FINALIZADO ***');
	
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_REPORTE_PADRON_SERVICIOS`(
IN 
	vPID INT(11),
    vSOCIO_ID INT(11),
    vSERVICIO_ID INT(11),
    vFECHA_COBERTURA DATE,
    vCUOTAS_SOCIALES_MINIMAS INT(11)
)
BEGIN
		
		INSERT INTO asincrono_temporales(asincrono_id,clave_1,entero_1,texto_1,texto_2,
		texto_3,texto_4,texto_5,texto_6,texto_7,texto_8,
		texto_9,texto_10,texto_11,texto_12,texto_13,texto_14,texto_15,texto_16,texto_17,decimal_1)
        
		select
			vPID,
			'REPORTE_1',
			MutualServicioSolicitud.id,
			TDOC.concepto_1,
			Persona.documento,
			concat(Persona.apellido,', ',Persona.nombre) as apenom,
			Persona.sexo,
			Persona.calle,
			Persona.numero_calle,
			Persona.piso,
			Persona.dpto,
			Persona.barrio,
			Persona.localidad,
			Persona.codigo_postal,
			Provincia.nombre,
			date_format(MutualServicioSolicitud.fecha_alta_servicio,'%d-%m-%Y') as fecha_alta_servicio,
			date_format(MutualServicioSolicitud.fecha_baja_servicio,'%d-%m-%Y') as fecha_baja_servicio,
			'TIT' as condicion,
			Persona.fecha_nacimiento,
            CORG.concepto_1,
			ifnull((select costo_titular from mutual_servicio_valores v where
			v.mutual_servicio_id = MutualServicioSolicitud.mutual_servicio_id
			and v.periodo_vigencia <= date_format(now(),'%Y%m')
			and v.codigo_organismo = PersonaBeneficio.codigo_beneficio
			order by v.periodo_vigencia desc limit 1),0) as costo_titular
		from mutual_servicio_solicitudes MutualServicioSolicitud
		inner join personas Persona on (Persona.id = MutualServicioSolicitud.persona_id)
		inner join global_datos TDOC on (TDOC.id = Persona.tipo_documento)
		inner join provincias Provincia on (Provincia.id = Persona.provincia_id)
		inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = MutualServicioSolicitud.persona_beneficio_id)
        inner join global_datos CORG on (CORG.id = PersonaBeneficio.codigo_beneficio)
		where
		MutualServicioSolicitud.socio_id = vSOCIO_ID
		and MutualServicioSolicitud.mutual_servicio_id = vSERVICIO_ID
		and ifnull((select count(*) from orden_descuento_cuotas where socio_id = vSOCIO_ID
		and proveedor_id = 18 and tipo_cuota = 'MUTUTCUOCSOC'
		and importe > (select ifnull(sum(importe),0) from orden_descuento_cobro_cuotas 
		where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id)),0) <= vCUOTAS_SOCIALES_MINIMAS
        and ifnull((select count(*) from orden_descuento_cuotas where socio_id = vSOCIO_ID
		and proveedor_id = 18 and tipo_cuota = 'MUTUTCUOCSOC'),0) > 0
        and MutualServicioSolicitud.fecha_baja_servicio IS NULL 
        GROUP BY Persona.documento
union
		(
select
			vPID,
            'REPORTE_2',
			MutualServicioSolicitud.id,
			TDOC.concepto_1,
			Persona.documento,
			concat(Persona.apellido,', ',Persona.nombre) as apenom,
			Persona.sexo,
			Persona.calle,
			Persona.numero_calle,
			Persona.piso,
			Persona.dpto,
			Persona.barrio,
			Persona.localidad,
			Persona.codigo_postal,
			Provincia.nombre,
			date_format(MutualServicioSolicitud.fecha_alta_servicio,'%d-%m-%Y') as fecha_alta_servicio,
			date_format(MutualServicioSolicitud.fecha_baja_servicio,'%d-%m-%Y') as fecha_baja_servicio,
			'TIT' as condicion,
			Persona.fecha_nacimiento,   
            CORG.concepto_1,
			ifnull((select costo_titular from mutual_servicio_valores v where
			v.mutual_servicio_id = MutualServicioSolicitud.mutual_servicio_id
			and v.periodo_vigencia <= date_format(now(),'%Y%m')
			and v.codigo_organismo = PersonaBeneficio.codigo_beneficio
			order by v.periodo_vigencia desc limit 1),0) as costo_titular
		from mutual_servicio_solicitudes MutualServicioSolicitud
		inner join personas Persona on (Persona.id = MutualServicioSolicitud.persona_id)
		inner join global_datos TDOC on (TDOC.id = Persona.tipo_documento)
		inner join provincias Provincia on (Provincia.id = Persona.provincia_id)
		inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = MutualServicioSolicitud.persona_beneficio_id)
        inner join global_datos CORG on (CORG.id = PersonaBeneficio.codigo_beneficio)
		where
		MutualServicioSolicitud.socio_id = vSOCIO_ID
		and MutualServicioSolicitud.mutual_servicio_id = vSERVICIO_ID
		and ifnull((select count(*) from orden_descuento_cuotas where socio_id = vSOCIO_ID
		and proveedor_id = 18 and tipo_cuota = 'MUTUTCUOCSOC'
		and importe > (select ifnull(sum(importe),0) from orden_descuento_cobro_cuotas 
		where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id)),0) <= vCUOTAS_SOCIALES_MINIMAS
        and ifnull((select count(*) from orden_descuento_cuotas where socio_id = vSOCIO_ID
		and proveedor_id = 18 and tipo_cuota = 'MUTUTCUOCSOC'),0) > 0        
        and MutualServicioSolicitud.fecha_baja_servicio IS NOT NULL GROUP BY Persona.documento         
        )
union
		(select
			vPID,
			'REPORTE_1',
			MutualServicioSolicitud.id,
			TDOC.concepto_1,
			SocioAdicional.documento,
			concat(SocioAdicional.apellido,', ',SocioAdicional.nombre) as apenom,
			SocioAdicional.sexo,
			SocioAdicional.calle,
			SocioAdicional.numero_calle,
			SocioAdicional.piso,
			SocioAdicional.dpto,
			SocioAdicional.barrio,
			SocioAdicional.localidad,
			SocioAdicional.codigo_postal,
			Provincia.nombre,
			date_format(MutualServicioSolicitudAdicional.fecha_alta,'%d-%m-%Y') as fecha_alta,
			date_format(MutualServicioSolicitudAdicional.fecha_baja,'%d-%m-%Y') as fecha_baja,
			'ADI' as condicion,   
			SocioAdicional.fecha_nacimiento, 
            CORG.concepto_1,
			ifnull((select costo_adicional from mutual_servicio_valores v where
			v.mutual_servicio_id = MutualServicioSolicitud.mutual_servicio_id
			and v.periodo_vigencia <= date_format(now(),'%Y%m')
			and v.codigo_organismo = PersonaBeneficio.codigo_beneficio
			order by v.periodo_vigencia desc limit 1),0) as costo_adicional
		from mutual_servicio_solicitud_adicionales MutualServicioSolicitudAdicional
		inner join mutual_servicio_solicitudes MutualServicioSolicitud on (MutualServicioSolicitud.id = MutualServicioSolicitudAdicional.mutual_servicio_solicitud_id)
		inner join socio_adicionales SocioAdicional on (SocioAdicional.id = MutualServicioSolicitudAdicional.socio_adicional_id)
		inner join global_datos TDOC on (TDOC.id = SocioAdicional.tipo_documento)
		inner join provincias Provincia on (Provincia.id = SocioAdicional.provincia_id)
		inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = MutualServicioSolicitud.persona_beneficio_id)
        inner join global_datos CORG on (CORG.id = PersonaBeneficio.codigo_beneficio)
		where
		MutualServicioSolicitud.socio_id = vSOCIO_ID
		and MutualServicioSolicitud.mutual_servicio_id = vSERVICIO_ID
		and MutualServicioSolicitudAdicional.fecha_alta <= vFECHA_COBERTURA
		and ifnull((select count(*) from orden_descuento_cuotas where socio_id = vSOCIO_ID
		and proveedor_id = 18 and tipo_cuota = 'MUTUTCUOCSOC'
		and importe > (select ifnull(sum(importe),0) from orden_descuento_cobro_cuotas 
		where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id)),0) <= vCUOTAS_SOCIALES_MINIMAS
        and ifnull((select count(*) from orden_descuento_cuotas where socio_id = vSOCIO_ID
		and proveedor_id = 18 and tipo_cuota = 'MUTUTCUOCSOC'),0) > 0        
        and MutualServicioSolicitud.fecha_baja_servicio IS NULL GROUP BY SocioAdicional.documento
		order by SocioAdicional.apellido,SocioAdicional.nombre)
union 
(select
			vPID,
            'REPORTE_1',
			MutualServicioSolicitud.id,
			TDOC.concepto_1,
			SocioAdicional.documento,
			concat(SocioAdicional.apellido,', ',SocioAdicional.nombre) as apenom,
			SocioAdicional.sexo,
			SocioAdicional.calle,
			SocioAdicional.numero_calle,
			SocioAdicional.piso,
			SocioAdicional.dpto,
			SocioAdicional.barrio,
			SocioAdicional.localidad,
			SocioAdicional.codigo_postal,
			Provincia.nombre,
			date_format(MutualServicioSolicitudAdicional.fecha_alta,'%d-%m-%Y') as fecha_alta,
			date_format(MutualServicioSolicitudAdicional.fecha_baja,'%d-%m-%Y') as fecha_baja,
			'ADI' as condicion,   
			SocioAdicional.fecha_nacimiento,  
            CORG.concepto_1,
			ifnull((select costo_adicional from mutual_servicio_valores v where
			v.mutual_servicio_id = MutualServicioSolicitud.mutual_servicio_id
			and v.periodo_vigencia <= date_format(now(),'%Y%m')
			and v.codigo_organismo = PersonaBeneficio.codigo_beneficio
			order by v.periodo_vigencia desc limit 1),0) as costo_adicional
		from mutual_servicio_solicitud_adicionales MutualServicioSolicitudAdicional
		inner join mutual_servicio_solicitudes MutualServicioSolicitud on (MutualServicioSolicitud.id = MutualServicioSolicitudAdicional.mutual_servicio_solicitud_id)
		inner join socio_adicionales SocioAdicional on (SocioAdicional.id = MutualServicioSolicitudAdicional.socio_adicional_id)
		inner join global_datos TDOC on (TDOC.id = SocioAdicional.tipo_documento)
		inner join provincias Provincia on (Provincia.id = SocioAdicional.provincia_id)
		inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = MutualServicioSolicitud.persona_beneficio_id)
        inner join global_datos CORG on (CORG.id = PersonaBeneficio.codigo_beneficio)
		where
		MutualServicioSolicitud.socio_id = vSOCIO_ID
		and MutualServicioSolicitud.mutual_servicio_id = vSERVICIO_ID
		and MutualServicioSolicitudAdicional.fecha_alta <= vFECHA_COBERTURA
		and ifnull((select count(*) from orden_descuento_cuotas where socio_id = vSOCIO_ID
		and proveedor_id = 18 and tipo_cuota = 'MUTUTCUOCSOC'
		and importe > (select ifnull(sum(importe),0) from orden_descuento_cobro_cuotas 
		where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id)),0) <= vCUOTAS_SOCIALES_MINIMAS
        and ifnull((select count(*) from orden_descuento_cuotas where socio_id = vSOCIO_ID
		and proveedor_id = 18 and tipo_cuota = 'MUTUTCUOCSOC'),0) > 0        
        and MutualServicioSolicitud.fecha_baja_servicio IS NOT NULL GROUP BY SocioAdicional.documento
		order by SocioAdicional.apellido,SocioAdicional.nombre);              
        
 
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_TMP_DESIMPUTA_LIQUIDACION`(
IN vLIQUIDACION_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
DECLARE vCOBRO_ID INT(11);

DECLARE C_CUOTAS CURSOR FOR 

select orden_descuento_cobro_id 
from liquidacion_cuotas where liquidacion_id = vLIQUIDACION_ID
and imputada = 1 and ifnull(orden_descuento_cobro_id,0) <> 0 
GROUP BY orden_descuento_cobro_id;

DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;

OPEN C_CUOTAS;
c1_loop: LOOP
FETCH C_CUOTAS INTO vCOBRO_ID;

        IF (l_last_row = 1) THEN
			LEAVE c1_loop; 
		END IF;	
		-- -----------------------------------------------------------------
		-- BORRO LOS ADICIONALES DEVENGADOS
        -- -----------------------------------------------------------------
        update liquidacion_cuotas set orden_descuento_cuota_id = null
        where liquidacion_id = vLIQUIDACION_ID and orden_descuento_cobro_id = vCOBRO_ID
        and ifnull(mutual_adicional_pendiente_id,0) <> 0;           
        
        DELETE cu.* FROM orden_descuento_cuotas cu
        inner join liquidacion_cuotas lc on (lc.liquidacion_id = vLIQUIDACION_ID 
        and lc.orden_descuento_cuota_id = cu.id)
        inner join orden_descuento_cobro_cuotas co on (co.orden_descuento_cobro_id = vCOBRO_ID and co.orden_descuento_cuota_id = cu.id)
        where ifnull(lc.mutual_adicional_pendiente_id,0) <> 0;
        
        update liquidacion_cuotas set imputada = 0, orden_descuento_cobro_id = 0
        where liquidacion_id = vLIQUIDACION_ID and orden_descuento_cobro_id = vCOBRO_ID
        and ifnull(mutual_adicional_pendiente_id,0) <> 0;          
        
		-- -----------------------------------------------------------------
        -- MARCO LAS CUOTAS COMO NO IMPUTADAS
        -- -----------------------------------------------------------------
        update liquidacion_cuotas set imputada = 0, orden_descuento_cobro_id = 0
        where liquidacion_id = vLIQUIDACION_ID and orden_descuento_cobro_id = vCOBRO_ID;

		-- -----------------------------------------------------------------
        -- BORRO EL DETALLE DEL COBRO Y ANULO LA CABECERA
        -- -----------------------------------------------------------------
		DELETE FROM orden_descuento_cobro_cuotas where orden_descuento_cobro_id = vCOBRO_ID;

        update orden_descuento_cobros set anulado = 1 where id = vCOBRO_ID;
        
END LOOP c1_loop;
CLOSE C_CUOTAS;

-- -----------------------------------------------------------------
-- SACO LA MARCA DEL COBRO EN LA SOCIO RENDICIONES
-- -----------------------------------------------------------------
update liquidacion_socio_rendiciones 
set orden_descuento_cobro_id = 0 where liquidacion_id = vLIQUIDACION_ID
and ifnull(orden_descuento_cobro_id,0) <> 0;

-- -----------------------------------------------------------------
-- BORRO LOS REINTEGROS
-- -----------------------------------------------------------------
delete from socio_reintegros where liquidacion_id = vLIQUIDACION_ID and anticipado = 0;

-- -----------------------------------------------------------------
-- MARCO LOS ADICIONALES PENDIENTES
-- -----------------------------------------------------------------
update mutual_adicional_pendientes 
set procesado = 0, orden_descuento_cuota_id = null
where liquidacion_id = vLIQUIDACION_ID
and procesado = 1 and ifnull(orden_descuento_cuota_id,0) <> 0;

-- -----------------------------------------------------------------
-- ACTUALIZO LA CABECERA DE LA LIQUIDACION
-- -----------------------------------------------------------------
update liquidaciones set imputada = 0 where id = vLIQUIDACION_ID;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `STP_ASINCRONO`(vPID INT,vStatus VARCHAR(1),vTot INT, vCont INT, vMsg VARCHAR(255))
BEGIN
	DECLARE vPorc DECIMAL(10,2) DEFAULT 0;
	
	SET vPorc = ROUND((vCont / vTot) * 100,2);
	SET vMsg = CONCAT('[',vCont,'|',vTot,'] ',vMsg);
	
	UPDATE asincronos 
	SET estado = vStatus, total = vTot, contador = vCont, porcentaje = vPorc, msg = vMsg
	WHERE id = vPID;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `tmp_crea_scoring`(IN vLIQUIDACION INT(11))
BEGIN
 DECLARE done INT DEFAULT FALSE;
 DECLARE vSOCIO_ID INT(11);
 DECLARE cur1 CURSOR FOR SELECT socio_id FROM liquidacion_cuotas
 where liquidacion_id = vLIQUIDACION group by socio_id;
 DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
 OPEN cur1;
 read_loop: LOOP
 FETCH cur1 INTO vSOCIO_ID;
 IF done THEN LEAVE read_loop; END IF;
    CALL SP_LIQUIDA_DEUDA_SCORING(vSOCIO_ID,vLIQUIDACION);
 END LOOP;
 CLOSE cur1;
 update liquidaciones set scoring = 1 where id = vLIQUIDACION;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `v_buscar_solicitudes_credito`()
BEGIN
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION `FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE`(
vPROVEEDOR_ID INT(11)) RETURNS decimal(10,2)
BEGIN
DECLARE vSALDO DECIMAL(10,2);
DECLARE vLIQUIDA_PRESTAMO BOOLEAN;
SET vSALDO = 10000000;
SELECT liquida_prestamo into vLIQUIDA_PRESTAMO FROM proveedores WHERE id = vPROVEEDOR_ID;
IF vLIQUIDA_PRESTAMO = TRUE THEN 
SELECT	
		(
			SELECT IFNULL(SUM(c.importe_debitado * -1),0)
			FROM	liquidacion_cuotas c, liquidaciones l, global_datos AS g
			WHERE	c.liquidacion_id = l.id AND l.facturada = 0 AND c.proveedor_id = p.id AND l.codigo_organismo = g.id
		) +
		(
			SELECT	IFNULL(SUM(c.comision_cobranza),0)
			FROM	liquidacion_cuotas c, liquidaciones l, global_datos AS g
			WHERE	c.liquidacion_id = l.id AND l.facturada = 0 AND c.proveedor_id = p.id AND l.codigo_organismo = g.id
		) +
		(
			SELECT	IFNULL(SUM(ProveedorFactura.total_comprobante * IF(ProveedorFactura.tipo = 'SD' OR ProveedorFactura.tipo='FA',-1, 1)),0)
			FROM proveedor_facturas AS ProveedorFactura
			WHERE proveedor_id = p.id
		) +
		(
			SELECT	IFNULL(SUM(OrdenPago.importe),0)
			FROM	orden_pagos AS OrdenPago
			WHERE proveedor_id = p.id AND anulado = 0
		) +
		(
			SELECT	IFNULL(SUM(ClienteFactura.total_comprobante * IF(ClienteFactura.tipo = 'SD' OR ClienteFactura.tipo='FA' OR ClienteFactura.tipo = 'ND',1, -1)),0)
			FROM cliente_facturas AS ClienteFactura, proveedores AS Proveedor
			WHERE Proveedor.id = p.id AND ClienteFactura.cliente_id = Proveedor.cliente_id AND ClienteFactura.anulado = 0
		) +
		(
			SELECT	IFNULL(SUM(Recibo.importe * -1),0)
			FROM	recibos AS Recibo, proveedores AS Proveedor
			WHERE	Proveedor.id = p.id AND Recibo.cliente_id = Proveedor.cliente_id AND Recibo.anulado = 0 AND Recibo.cliente_id > 0
		) INTO vSALDO
		FROM	proveedores p 
		WHERE	p.id = vPROVEEDOR_ID;
        SET vSALDO = vSALDO * -1;
END IF;
RETURN vSALDO;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`%` FUNCTION `FX_TOTALES_ORDEN`(
	vOrdenID INT, 
	vPerCtrl VARCHAR(6),
	vProveedorID INT,
	vBeneficioId INT,
	vTipo VARCHAR(50)
	) RETURNS decimal(10,2)
    NO SQL
BEGIN
	DECLARE vValor FLOAT DEFAULT 0;
	
	SET vValor = 0;
	
	IF vTipo = 'TOTAL_DEVENGADO' THEN
		SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
		FROM orden_descuento_cuotas cu WHERE cu.orden_descuento_id = vOrdenID
		AND cu.proveedor_id = vProveedorID
		AND cu.persona_beneficio_id = vBeneficioId;
	END IF;
	IF vTipo = 'SALDO_AVENCER' THEN
		SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
		FROM orden_descuento_cuotas cu WHERE 
		cu.orden_descuento_id = vOrdenID AND cu.periodo > vPerCtrl 
		AND cu.proveedor_id = vProveedorID
		AND cu.persona_beneficio_id = vBeneficioId;
	END IF;
	
	IF vTipo = 'TOTAL_PAGADO' THEN
		SELECT IFNULL(SUM(cc.importe),0) INTO vValor
		FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
		orden_descuento_cuotas cu
		WHERE 
		cu.orden_descuento_id = vOrdenID AND cc.orden_descuento_cuota_id = cu.id 
		AND cc.orden_descuento_cobro_id = co.id 
		AND co.periodo_cobro <= vPerCtrl AND cu.proveedor_id = vProveedorID
		AND cu.persona_beneficio_id = vBeneficioId;
	END IF;
	
	IF vTipo = 'SALDO_VENCIDO' THEN
		SELECT IFNULL(SUM(cu.importe),0) INTO @devengado FROM orden_descuento_cuotas cu
		WHERE 
			cu.orden_descuento_id = vOrdenID 
			AND cu.periodo <= vPerCtrl 
			AND cu.proveedor_id = vProveedorID
			AND cu.persona_beneficio_id = vBeneficioId;
				
		SELECT IFNULL(SUM(cc.importe),0) INTO @pagado
		FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
		orden_descuento_cuotas cu
		WHERE 
		cu.orden_descuento_id = vOrdenID 
		AND cc.orden_descuento_cuota_id = cu.id 
		AND cc.orden_descuento_cobro_id = co.id 
		AND co.periodo_cobro <= vPerCtrl
		AND cu.proveedor_id = vProveedorID
		AND cu.persona_beneficio_id = vBeneficioId;
	
		SET vValor = @devengado - @pagado;
	
	END IF;
	
	IF vTipo = 'CUOTAS_DEVENGADAS' THEN
		SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
		WHERE cu.orden_descuento_id = vOrdenID AND cu.proveedor_id = vProveedorID
		AND cu.persona_beneficio_id = vBeneficioId;
	END IF;
	
	IF vTipo = 'CUOTAS_PAGAS' THEN
		SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
		WHERE cu.orden_descuento_id = vOrdenID
		AND cu.periodo <= vPerCtrl AND cu.proveedor_id = vProveedorID
		AND cu.persona_beneficio_id = vBeneficioId
		AND cu.importe <= (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
							INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
							WHERE 
							cocu.orden_descuento_cuota_id = cu.id
							AND co.periodo_cobro <= vPerCtrl)
		GROUP BY cu.orden_descuento_id;
	END IF;	
	
	IF vTipo = 'CUOTAS_VENCIDAS' THEN
		SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
		WHERE cu.orden_descuento_id = vOrdenID
		AND cu.periodo <= vPerCtrl AND cu.proveedor_id = vProveedorID
		AND cu.persona_beneficio_id = vBeneficioId
		AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
							INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
							WHERE 
							cocu.orden_descuento_cuota_id = cu.id
							AND co.periodo_cobro <= vPerCtrl)
		GROUP BY cu.orden_descuento_id;
	END IF;
	
	IF vTipo = 'CUOTAS_AVENCER' THEN
		SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
		WHERE cu.orden_descuento_id = vOrdenID
		AND cu.periodo > vPerCtrl AND cu.proveedor_id = vProveedorID
		AND cu.persona_beneficio_id = vBeneficioId
		AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
							WHERE 
							cocu.orden_descuento_cuota_id = cu.id)
		GROUP BY cu.orden_descuento_id;
	END IF;		
	RETURN vValor;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`%` FUNCTION `FX_TOTALES_SOCIO`(
	vSocioID INT, 
	vPerCtrl VARCHAR(6),
	vProveedorID INT,
	vBeneficioId INT,
	vTipo VARCHAR(50)
	) RETURNS decimal(10,2)
    NO SQL
BEGIN
	DECLARE vValor FLOAT DEFAULT 0;
	
	SET vValor = 0;
	
	IF vTipo = 'TOTAL_DEVENGADO' THEN
		IF vProveedorID IS NOT NULL AND vBeneficioId IS NOT NULL THEN
		
			SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
			FROM orden_descuento_cuotas cu WHERE cu.socio_id = vSocioID
			AND cu.proveedor_id = vProveedorID
			AND cu.persona_beneficio_id = vBeneficioId;		
		ELSEIF vProveedorID IS NULL AND vBeneficioId IS NOT NULL THEN
			SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
			FROM orden_descuento_cuotas cu WHERE cu.socio_id = vSocioID
			AND cu.persona_beneficio_id = vBeneficioId;	
		ELSEIF vProveedorID IS NOT NULL AND vBeneficioId IS NULL THEN
			SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
			FROM orden_descuento_cuotas cu WHERE cu.socio_id = vSocioID
			AND cu.proveedor_id = vProveedorID;			
		ELSE
			SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
			FROM orden_descuento_cuotas cu WHERE cu.socio_id = vSocioID;			
		
		END IF;
		
		
	END IF;
	IF vTipo = 'SALDO_AVENCER' THEN
	
		IF vProveedorID IS NOT NULL AND vBeneficioId IS NOT NULL THEN
		
			SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
			FROM orden_descuento_cuotas cu WHERE 
			cu.socio_id = vSocioID AND cu.periodo > vPerCtrl 
			AND cu.proveedor_id = vProveedorID
			AND cu.persona_beneficio_id = vBeneficioId;	
		ELSEIF vProveedorID IS NULL AND vBeneficioId IS NOT NULL THEN
			SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
			FROM orden_descuento_cuotas cu WHERE 
			cu.socio_id = vSocioID AND cu.periodo > vPerCtrl 
			AND cu.persona_beneficio_id = vBeneficioId;		
		ELSEIF vProveedorID IS NOT NULL AND vBeneficioId IS NULL THEN
			SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
			FROM orden_descuento_cuotas cu WHERE 
			cu.socio_id = vSocioID AND cu.periodo > vPerCtrl 
			AND cu.proveedor_id = vProveedorID;	
					
		ELSE
			SELECT IFNULL(SUM(cu.importe),0) INTO vValor 
			FROM orden_descuento_cuotas cu WHERE 
			cu.socio_id = vSocioID AND cu.periodo > vPerCtrl;			
		
		END IF;	
	
	END IF;
	
	IF vTipo = 'TOTAL_PAGADO' THEN
	
		IF vProveedorID IS NOT NULL AND vBeneficioId IS NOT NULL THEN
		
			SELECT IFNULL(SUM(cc.importe),0) INTO vValor
			FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
			orden_descuento_cuotas cu
			WHERE 
			cu.socio_id = vSocioID AND cc.orden_descuento_cuota_id = cu.id 
			AND cc.orden_descuento_cobro_id = co.id 
			AND co.periodo_cobro <= vPerCtrl AND cu.proveedor_id = vProveedorID
			AND cu.persona_beneficio_id = vBeneficioId;	
		ELSEIF vProveedorID IS NULL AND vBeneficioId IS NOT NULL THEN
			SELECT IFNULL(SUM(cc.importe),0) INTO vValor
			FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
			orden_descuento_cuotas cu
			WHERE 
			cu.socio_id = vSocioID AND cc.orden_descuento_cuota_id = cu.id 
			AND cc.orden_descuento_cobro_id = co.id 
			AND co.periodo_cobro <= vPerCtrl 
			AND cu.persona_beneficio_id = vBeneficioId;	
	
		ELSEIF vProveedorID IS NOT NULL AND vBeneficioId IS NULL THEN
			SELECT IFNULL(SUM(cc.importe),0) INTO vValor
			FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
			orden_descuento_cuotas cu
			WHERE 
			cu.socio_id = vSocioID AND cc.orden_descuento_cuota_id = cu.id 
			AND cc.orden_descuento_cobro_id = co.id 
			AND co.periodo_cobro <= vPerCtrl AND cu.proveedor_id = vProveedorID;	
					
		ELSE
			SELECT IFNULL(SUM(cc.importe),0) INTO vValor
			FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
			orden_descuento_cuotas cu
			WHERE 
			cu.socio_id = vSocioID AND cc.orden_descuento_cuota_id = cu.id 
			AND cc.orden_descuento_cobro_id = co.id 
			AND co.periodo_cobro <= vPerCtrl;	
			
		
		END IF;		
	
	END IF;
	
	IF vTipo = 'SALDO_VENCIDO' THEN
	
		IF vProveedorID IS NOT NULL AND vBeneficioId IS NOT NULL THEN
		
			SELECT IFNULL(SUM(cu.importe),0) INTO @devengado FROM orden_descuento_cuotas cu
			WHERE 
				cu.socio_id = vSocioID 
				AND cu.periodo <= vPerCtrl 
				AND cu.proveedor_id = vProveedorID
				AND cu.persona_beneficio_id = vBeneficioId;
					
			SELECT IFNULL(SUM(cc.importe),0) INTO @pagado
			FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
			orden_descuento_cuotas cu
			WHERE 
			cu.socio_id = vSocioID
			AND cc.orden_descuento_cuota_id = cu.id 
			AND cc.orden_descuento_cobro_id = co.id 
			AND co.periodo_cobro <= vPerCtrl
			AND cu.proveedor_id = vProveedorID
			AND cu.persona_beneficio_id = vBeneficioId;
		ELSEIF vProveedorID IS NULL AND vBeneficioId IS NOT NULL THEN
			SELECT IFNULL(SUM(cu.importe),0) INTO @devengado FROM orden_descuento_cuotas cu
			WHERE 
				cu.socio_id = vSocioID 
				AND cu.periodo <= vPerCtrl 
				AND cu.persona_beneficio_id = vBeneficioId;
					
			SELECT IFNULL(SUM(cc.importe),0) INTO @pagado
			FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
			orden_descuento_cuotas cu
			WHERE 
			cu.socio_id = vSocioID
			AND cc.orden_descuento_cuota_id = cu.id 
			AND cc.orden_descuento_cobro_id = co.id 
			AND co.periodo_cobro <= vPerCtrl
			AND cu.persona_beneficio_id = vBeneficioId;
	
		ELSEIF vProveedorID IS NOT NULL AND vBeneficioId IS NULL THEN
			SELECT IFNULL(SUM(cu.importe),0) INTO @devengado FROM orden_descuento_cuotas cu
			WHERE 
				cu.socio_id = vSocioID 
				AND cu.periodo <= vPerCtrl 
				AND cu.proveedor_id = vProveedorID;
					
			SELECT IFNULL(SUM(cc.importe),0) INTO @pagado
			FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
			orden_descuento_cuotas cu
			WHERE 
			cu.socio_id = vSocioID
			AND cc.orden_descuento_cuota_id = cu.id 
			AND cc.orden_descuento_cobro_id = co.id 
			AND co.periodo_cobro <= vPerCtrl
			AND cu.proveedor_id = vProveedorID;
	
					
		ELSE
			SELECT IFNULL(SUM(cu.importe),0) INTO @devengado FROM orden_descuento_cuotas cu
			WHERE 
				cu.socio_id = vSocioID 
				AND cu.periodo <= vPerCtrl;
					
			SELECT IFNULL(SUM(cc.importe),0) INTO @pagado
			FROM orden_descuento_cobro_cuotas cc, orden_descuento_cobros co, 
			orden_descuento_cuotas cu
			WHERE 
			cu.socio_id = vSocioID
			AND cc.orden_descuento_cuota_id = cu.id 
			AND cc.orden_descuento_cobro_id = co.id 
			AND co.periodo_cobro <= vPerCtrl;
		END IF;		
	
	
		SET vValor = @devengado - @pagado;
	
	END IF;
	
	IF vTipo = 'CUOTAS_DEVENGADAS' THEN
	
		IF vProveedorID IS NOT NULL AND vBeneficioId IS NOT NULL THEN
		
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID AND cu.proveedor_id = vProveedorID
			AND cu.persona_beneficio_id = vBeneficioId;	
		ELSEIF vProveedorID IS NULL AND vBeneficioId IS NOT NULL THEN
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID 
			AND cu.persona_beneficio_id = vBeneficioId;	
		
		ELSEIF vProveedorID IS NOT NULL AND vBeneficioId IS NULL THEN
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID AND cu.proveedor_id = vProveedorID;	
					
		ELSE
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID;	
			
		END IF;		
	
	END IF;
	
	IF vTipo = 'CUOTAS_PAGAS' THEN
	
		IF vProveedorID IS NOT NULL AND vBeneficioId IS NOT NULL THEN
		
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo <= vPerCtrl AND cu.proveedor_id = vProveedorID
			AND cu.persona_beneficio_id = vBeneficioId
			AND cu.importe <= (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id
								AND co.periodo_cobro <= vPerCtrl)
			GROUP BY cu.socio_id;
		ELSEIF vProveedorID IS NULL AND vBeneficioId IS NOT NULL THEN
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo <= vPerCtrl
			AND cu.persona_beneficio_id = vBeneficioId
			AND cu.importe <= (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id
								AND co.periodo_cobro <= vPerCtrl)
			GROUP BY cu.socio_id;
		
		ELSEIF vProveedorID IS NOT NULL AND vBeneficioId IS NULL THEN
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo <= vPerCtrl AND cu.proveedor_id = vProveedorID
			AND cu.importe <= (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id
								AND co.periodo_cobro <= vPerCtrl)
			GROUP BY cu.socio_id;
	
					
		ELSE
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo <= vPerCtrl
			AND cu.importe <= (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id
								AND co.periodo_cobro <= vPerCtrl)
			GROUP BY cu.socio_id;
	
			
		END IF;		
	
	END IF;		
	
	
	IF vTipo = 'CUOTAS_VENCIDAS' THEN
		IF vProveedorID IS NOT NULL AND vBeneficioId IS NOT NULL THEN
		
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo <= vPerCtrl AND cu.proveedor_id = vProveedorID
			AND cu.persona_beneficio_id = vBeneficioId
			AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id
								AND co.periodo_cobro <= vPerCtrl)
			GROUP BY cu.socio_id;	
		ELSEIF vProveedorID IS NULL AND vBeneficioId IS NOT NULL THEN
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo <= vPerCtrl
			AND cu.persona_beneficio_id = vBeneficioId
			AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id
								AND co.periodo_cobro <= vPerCtrl)
			GROUP BY cu.socio_id;		
		
		ELSEIF vProveedorID IS NOT NULL AND vBeneficioId IS NULL THEN
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo <= vPerCtrl AND cu.proveedor_id = vProveedorID
			AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id
								AND co.periodo_cobro <= vPerCtrl)
			GROUP BY cu.socio_id;		
	
					
		ELSE
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo <= vPerCtrl
			AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id
								AND co.periodo_cobro <= vPerCtrl)
			GROUP BY cu.socio_id;	
	
			
		END IF;	
	
	END IF;
	
	IF vTipo = 'CUOTAS_AVENCER' THEN
	
		IF vProveedorID IS NOT NULL AND vBeneficioId IS NOT NULL THEN
		
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo > vPerCtrl AND cu.proveedor_id = vProveedorID
			AND cu.persona_beneficio_id = vBeneficioId
			AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id)
			GROUP BY cu.socio_id;
		ELSEIF vProveedorID IS NULL AND vBeneficioId IS NOT NULL THEN
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo > vPerCtrl
			AND cu.persona_beneficio_id = vBeneficioId
			AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id)
			GROUP BY cu.socio_id;
	
		ELSEIF vProveedorID IS NOT NULL AND vBeneficioId IS NULL THEN
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo > vPerCtrl AND cu.proveedor_id = vProveedorID
			AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id)
			GROUP BY cu.socio_id;
	
	
					
		ELSE
			SELECT IFNULL(COUNT(*),0) INTO vValor FROM orden_descuento_cuotas cu
			WHERE cu.socio_id = vSocioID
			AND cu.periodo > vPerCtrl
			AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
								WHERE 
								cocu.orden_descuento_cuota_id = cu.id)
			GROUP BY cu.socio_id;
	
			
		END IF;	
	
	END IF;		
	RETURN vValor;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`%` FUNCTION `isAsincronoStop`(vPID INT) RETURNS tinyint(1)
    NO SQL
BEGIN
	DECLARE vEstado CHAR(1);
	DECLARE vAsincStat BOOLEAN;
	DECLARE vRUN BOOLEAN;
	SELECT estado INTO vEstado FROM asincronos WHERE id = vPID;
	IF vEstado = 'S' THEN 
		SET vRUN = TRUE;
		
	ELSE SET vRUN = FALSE;
	END IF;
	RETURN vRUN;
END$$
DELIMITER ;



CREATE 
     OR REPLACE ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_plan_condiciones` AS
    (SELECT 
        `ppgc`.`id` AS `ID`,
        `pp`.`ID` AS `PLAN_ID`,
        `ppg`.`vigencia_desde` AS `VIGENCIA`,
        `ppgc`.`capital` AS `CAPITAL`,
        `ppgc`.`liquido` AS `LIQUIDO`,
        `ppgc`.`cuotas` AS `CUOTAS`,
        `ppgc`.`importe` AS `IMPORTE`,
        (`ppgc`.`importe` * `ppgc`.`cuotas`) AS `TOTAL`,
        `ppgc`.`tna` AS `TNA`,
        `ppgc`.`tem` AS `TEM`,
        `ppgc`.`cft` AS `CFT`
    FROM
        ((`v_planes` `pp`
        JOIN `proveedor_plan_grillas` `ppg`)
        JOIN `proveedor_plan_grilla_cuotas` `ppgc`)
    WHERE
        ((`pp`.`ID` = `ppg`.`proveedor_plan_id`)
            AND (`ppg`.`id` = `ppgc`.`proveedor_plan_grilla_id`)
            AND (`ppg`.`vigencia_desde` >= (SELECT 
                MAX(`ppg2`.`vigencia_desde`)
            FROM
                `proveedor_plan_grillas` `ppg2`
            WHERE
                (`ppg2`.`proveedor_plan_id` = `ppg`.`proveedor_plan_id`)))));



CREATE 
     OR REPLACE ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_plan_monto_cuotas` AS
    (SELECT 
        `proveedor_plan_grilla_cuotas`.`id` AS `ID`,
        `proveedor_planes`.`id` AS `PLAN_ID`,
        `proveedor_plan_grillas`.`vigencia_desde` AS `VIGENCIA`,
        `proveedor_plan_grilla_cuotas`.`capital` AS `CAPITAL`,
        `proveedor_plan_grilla_cuotas`.`liquido` AS `LIQUIDO`,
        `proveedor_plan_grilla_cuotas`.`cuotas` AS `CUOTAS`,
        `proveedor_plan_grilla_cuotas`.`importe` AS `IMPORTE`,
        (`proveedor_plan_grilla_cuotas`.`importe` * `proveedor_plan_grilla_cuotas`.`cuotas`) AS `TOTAL`,
        `proveedor_plan_grilla_cuotas`.`tna` AS `TNA`,
        `proveedor_plan_grilla_cuotas`.`tem` AS `TEM`,
        `proveedor_plan_grilla_cuotas`.`cft` AS `CFT`
    FROM
        ((`proveedor_planes`
        JOIN `proveedor_plan_grillas`)
        JOIN `proveedor_plan_grilla_cuotas`)
    WHERE
        ((`proveedor_planes`.`id` = `proveedor_plan_grillas`.`proveedor_plan_id`)
            AND (`proveedor_plan_grillas`.`id` = (SELECT 
                `grillas`.`id`
            FROM
                `proveedor_plan_grillas` `grillas`
            WHERE
                ((`grillas`.`proveedor_plan_id` = `proveedor_planes`.`id`)
                    AND (`grillas`.`vigencia_desde` >= (SELECT 
                        MAX(`ppg2`.`vigencia_desde`)
                    FROM
                        `proveedor_plan_grillas` `ppg2`
                    WHERE
                        (`ppg2`.`proveedor_plan_id` = `grillas`.`proveedor_plan_id`))))
            ORDER BY `grillas`.`vigencia_desde` DESC
            LIMIT 1))
            AND (`proveedor_plan_grilla_cuotas`.`proveedor_plan_grilla_id` = `proveedor_plan_grillas`.`id`))
    GROUP BY `proveedor_planes`.`id` , `proveedor_plan_grillas`.`vigencia_desde` , `proveedor_plan_grilla_cuotas`.`liquido` , `proveedor_plan_grilla_cuotas`.`cuotas`
    ORDER BY `proveedor_planes`.`id` , `proveedor_plan_grilla_cuotas`.`liquido` , `proveedor_plan_grilla_cuotas`.`cuotas`);


CREATE 
     OR REPLACE ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_plan_montos` AS
    (SELECT 
        `proveedor_plan_grilla_cuotas`.`id` AS `ID`,
        `proveedor_planes`.`id` AS `PLAN_ID`,
        `proveedor_plan_grillas`.`vigencia_desde` AS `VIGENCIA`,
        `proveedor_plan_grilla_cuotas`.`liquido` AS `LIQUIDO`,
        `proveedor_plan_grilla_cuotas`.`tna` AS `TNA`,
        `proveedor_plan_grilla_cuotas`.`tem` AS `TEM`,
        `proveedor_plan_grilla_cuotas`.`cft` AS `CFT`        
    FROM
        ((`proveedor_planes`
        JOIN `proveedor_plan_grillas`)
        JOIN `proveedor_plan_grilla_cuotas`)
    WHERE
        ((`proveedor_planes`.`id` = `proveedor_plan_grillas`.`proveedor_plan_id`)
            AND (`proveedor_plan_grillas`.`id` = (SELECT 
                `grillas`.`id`
            FROM
                `proveedor_plan_grillas` `grillas`
            WHERE
                ((`grillas`.`proveedor_plan_id` = `proveedor_planes`.`id`)
                    AND (`grillas`.`vigencia_desde` <= CURDATE()))
            ORDER BY `grillas`.`vigencia_desde` DESC
            LIMIT 1))
            AND (`proveedor_plan_grilla_cuotas`.`proveedor_plan_grilla_id` = `proveedor_plan_grillas`.`id`))
    GROUP BY `proveedor_planes`.`id` , `proveedor_plan_grillas`.`vigencia_desde` , `proveedor_plan_grilla_cuotas`.`liquido`
    ORDER BY `proveedor_planes`.`id` , `proveedor_plan_grilla_cuotas`.`liquido`);



CREATE 
     OR REPLACE ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_credito_solicitudes` AS
    (SELECT 
        `mutual_producto_solicitudes`.`id` AS `ID`,
        `mutual_producto_solicitudes`.`proveedor_id` AS `PROVEEDOR_ID`,
        `mutual_producto_solicitudes`.`proveedor_plan_id` AS `PROVEEDOR_PLAN_ID`,
        `mutual_producto_solicitudes`.`persona_id` AS `PERSONA_ID`,
        `mutual_producto_solicitudes`.`socio_id` AS `CLIENTE_ID`,
        `mutual_producto_solicitudes`.`persona_beneficio_id` AS `PERSONA_BENEFICIO_ID`,
        `mutual_producto_solicitudes`.`aprobada` AS `APROBADA`,
        `mutual_producto_solicitudes`.`anulada` AS `ANULADA`,
        `mutual_producto_solicitudes`.`fecha` AS `FECHA`,
        `mutual_producto_solicitudes`.`fecha_pago` AS `FECHA_PAGO`,
        `mutual_producto_solicitudes`.`tipo_orden_dto` AS `TIPO_ORDEN_DTO`,
        `mutual_producto_solicitudes`.`tipo_producto` AS `TIPO_PRODUCTO`,
        IF(((`mutual_producto_solicitudes`.`anulada` = 1)
                AND (`mutual_producto_solicitudes`.`estado` = 'MUTUESTA0001')),
            'MUTUESTA0000',
            `mutual_producto_solicitudes`.`estado`) AS `ESTADO`,
        `mutual_producto_solicitudes`.`importe_total` AS `IMPORTE_TOTAL`,
        `mutual_producto_solicitudes`.`cuotas` AS `CUOTAS`,
        `mutual_producto_solicitudes`.`importe_cuota` AS `IMPORTE_CUOTA`,
        `mutual_producto_solicitudes`.`importe_solicitado` AS `IMPORTE_SOLICITADO`,
        `mutual_producto_solicitudes`.`importe_percibido` AS `IMPORTE_PERCIBIDO`,
        `mutual_producto_solicitudes`.`tna` AS `TNA`,
        `mutual_producto_solicitudes`.`tem` AS `TEM`,
        `mutual_producto_solicitudes`.`cft` AS `CFT`,
        `mutual_producto_solicitudes`.`observaciones` AS `OBSERVACIONES`,
        `mutual_producto_solicitudes`.`orden_descuento_id` AS `ORDEN_DESCUENTO_ID`,
        `mutual_producto_solicitudes`.`aprobada_por` AS `APROBADA_POR`,
        `mutual_producto_solicitudes`.`aprobada_el` AS `APROBADA_EL`,
        `mutual_producto_solicitudes`.`vendedor_id` AS `VENDEDOR_ID`,
        `mutual_producto_solicitudes`.`vendedor_remito_id` AS `VENDEDOR_REMITO_ID`,
        `mutual_producto_solicitudes`.`vendedor_notificar` AS `VENDEDOR_NOTIFICAR`,
        `mutual_producto_solicitudes`.`user_created` AS `EMITIDA_POR`,
        `mutual_producto_solicitudes`.`periodo_ini` AS `PERIODO_INI`,
        `mutual_producto_solicitudes`.`primer_vto_socio` AS `PRIMER_VTO_SOCIO`,
        `mutual_producto_solicitudes`.`forma_pago` AS `FORMA_PAGO`,
        IF(((`mutual_producto_solicitudes`.`aprobada` = 1)
                AND (`mutual_producto_solicitudes`.`anulada` = 0)),
            CONCAT(`mutual_producto_solicitudes`.`tipo_orden_dto`,
                    ' ',
                    `mutual_producto_solicitudes`.`orden_descuento_id`),
            '') AS `ORDEN_DESCUENTO`
    FROM
        `mutual_producto_solicitudes`
    WHERE
        (`mutual_producto_solicitudes`.`tipo_orden_dto` = 'EXPTE'));



DROP procedure IF EXISTS `p_insertar_solicitud_credito_con_preproceso`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_con_preproceso`(
IN
	vPROVEEDOR_ID INT(11),
	vPROVEEDOR_PLAN_ID INT(11),
	vPERSONA_ID INT(11),
	vCLIENTE_ID INT(11),
	vPERSONA_BENEFICIO_ID INT(11),
	vCUOTAS INT(11),
	vIMPORTE_CUOTA DECIMAL(10,2),
	vIMPORTE_SOLICITADO DECIMAL(10,2),
	vIMPORTE_PERCIBIDO DECIMAL(10,2),
	vVENDEDOR_ID INT(11),
	vOBSERVACIONES TEXT,
	vUSUARIO VARCHAR(50),
	vFORMA_PAGO VARCHAR(12),
	vUUID VARCHAR(100),
    vTNA DECIMAL(10,2),
    vTEM DECIMAL(10,2),
    vCFT DECIMAL(10,2)
)
BEGIN
	declare vSOLICITUD_ID INT(11);
	DECLARE vESTADO VARCHAR(12);
	DECLARE vPRODUCTO VARCHAR(12);
	DECLARE vTIPOORDEN VARCHAR(5); 
    DECLARE vPRESTAMO BOOLEAN;
	SET vCUOTAS = IF(vCUOTAS=0,NULL,vCUOTAS);
	SET vIMPORTE_CUOTA = IF(vIMPORTE_CUOTA=0,NULL,vIMPORTE_CUOTA);
	SET vIMPORTE_SOLICITADO = IF(vIMPORTE_SOLICITADO=0,NULL,vIMPORTE_SOLICITADO);
	SET vIMPORTE_PERCIBIDO = IF(vIMPORTE_PERCIBIDO=0,NULL,vIMPORTE_PERCIBIDO);
	SET @TOTAL = vCUOTAS * vIMPORTE_CUOTA;
    SET vPRESTAMO = FALSE;
	
	set vESTADO = 'MUTUESTA0001';
	-- SET vPRODUCTO = 'MUTUPROD0001';
	select tipo_producto into vPRODUCTO from proveedor_planes where id = vPROVEEDOR_PLAN_ID;
	select liquida_prestamo into vPRESTAMO from proveedores where id = vPROVEEDOR_ID;
       
	IF vPRODUCTO IS NULL THEN
		SET vPRODUCTO = 'MUTUPROD0001';
	END IF;
	IF vVENDEDOR_ID IS NULL THEN
		SET vESTADO = 'MUTUESTA0002';
	END IF;
	
	SELECT trim(concepto_3) into vTIPOORDEN from global_datos where id = vPRODUCTO;	
	
	INSERT INTO mutual_producto_solicitudes(proveedor_id,proveedor_plan_id,
	persona_id,socio_id,persona_beneficio_id,fecha,tipo_orden_dto,tipo_producto,
	estado,importe_total,cuotas,importe_cuota,importe_solicitado,importe_percibido,
	vendedor_id,created,user_created,observaciones,forma_pago,prestamo,tna,tem,cft)
	VALUES(vPROVEEDOR_ID,vPROVEEDOR_PLAN_ID,vPERSONA_ID,vCLIENTE_ID,
	vPERSONA_BENEFICIO_ID,NOW(),vTIPOORDEN,vPRODUCTO,vESTADO,
	@TOTAL,vCUOTAS,vIMPORTE_CUOTA,vIMPORTE_SOLICITADO,vIMPORTE_PERCIBIDO,
	vVENDEDOR_ID,NOW(),vUSUARIO,vOBSERVACIONES,vFORMA_PAGO,vPRESTAMO,vTNA,vTEM,vCFT);
	SET vSOLICITUD_ID = LAST_INSERT_ID();	
	INSERT INTO mutual_producto_solicitud_estados(mutual_producto_solicitud_id,estado,observaciones,created,user_created)
	values(vSOLICITUD_ID,vESTADO,vOBSERVACIONES,NOW(),vUSUARIO);	
	
	INSERT INTO mutual_producto_solicitud_cancelaciones(mutual_producto_solicitud_id,cancelacion_orden_id)
	SELECT vSOLICITUD_ID,cancelacion_id FROM mutual_producto_solicitud_preproceso
	WHERE uuid_identificador = vUUID AND tipo = 2;
	
	
	INSERT INTO mutual_producto_solicitud_instruccion_pagos(mutual_producto_solicitud_id,a_la_orden_de,concepto,importe)
	SELECT vSOLICITUD_ID,a_la_orden_de,concepto,importe FROM mutual_producto_solicitud_preproceso
	WHERE uuid_identificador = vUUID AND tipo = 1;	
		
	
	INSERT INTO mutual_producto_solicitud_documentos(mutual_producto_solicitud_id,file_name,file_type,file_data)
	SELECT vSOLICITUD_ID,file_name,file_type,file_data FROM mutual_producto_solicitud_preproceso
	WHERE uuid_identificador = vUUID AND tipo = 3;	
	
	DELETE FROM mutual_producto_solicitud_preproceso WHERE uuid_identificador = vUUID;		
	
	
	
	SELECT * FROM v_credito_solicitudes WHERE ID = vSOLICITUD_ID;
END$$

DELIMITER ;