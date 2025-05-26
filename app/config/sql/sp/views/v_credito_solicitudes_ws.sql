CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_credito_solicitudes_ws` AS
    SELECT 
        `proveedores`.`codigo_acceso_ws` AS `proveedor_codigo_acceso_ws`,
        `solicitudes`.`id` AS `solicitud_numero`,
        `solicitudes`.`fecha` AS `solicitud_fecha`,
        `solicitudes`.`fecha_pago` AS `solicitud_fecha_pago`,
        `solicitudes`.`aprobada` AS `solicitud_aprobada`,
        RIGHT(`solicitudes`.`estado`, 4) AS `solicitud_codigo_estado`,
        `estado`.`concepto_1` AS `solicitud_codigo_estado_descripcion`,
        RIGHT(`solicitudes`.`tipo_producto`, 4) AS `solicitud_codigo_producto`,
        `producto`.`concepto_1` AS `solicitud_codigo_producto_descripcion`,
        `proveedores`.`razon_social` AS `solicitud_proveedor`,
        `solicitudes`.`importe_total` AS `solicitud_importe_total`,
        `solicitudes`.`cuotas` AS `solicitud_cantidad_cuotas`,
        `solicitudes`.`importe_cuota` AS `solicitud_importe_cuota`,
        `solicitudes`.`importe_solicitado` AS `solicitud_importe_solicitado`,
        `solicitudes`.`importe_percibido` AS `solicitud_importe_percibido`,
        `solicitudes`.`periodo_ini` AS `solicitud_periodo_inicio`,
        `solicitudes`.`primer_vto_socio` AS `solicitud_primer_vencimiento`,
        RIGHT(`personas`.`tipo_documento`,
            4) AS `solicitante_codigo_documento`,
        `tipo_documento`.`concepto_1` AS `solicitante_codigo_documento_descripcion`,
        `personas`.`documento` AS `solicitante_documento`,
        `personas`.`apellido` AS `solicitante_apellido`,
        `personas`.`nombre` AS `solicitante_nombre`,
        `personas`.`sexo` AS `solicitante_sexo`,
        `personas`.`calle` AS `solicitante_calle`,
        `personas`.`numero_calle` AS `solicitante_numero_calle`,
        `personas`.`piso` AS `solicitante_piso`,
        `personas`.`dpto` AS `solicitante_dpto`,
        `personas`.`barrio` AS `solicitante_barrio`,
        `personas`.`localidad` AS `solicitante_localidad`,
        `personas`.`codigo_postal` AS `solicitante_codigo_postal`,
        `personas`.`provincia_id` AS `solicitante_codigo_provincia`,
        `provincias`.`nombre` AS `solicitante_codigo_provincia_nombre`,
        `personas`.`nombre_conyuge` AS `solicitante_nombre_conyuge`,
        `personas`.`telefono_fijo` AS `solicitante_telefono_fijo`,
        `personas`.`telefono_movil` AS `solicitante_telefono_movil`,
        `personas`.`telefono_referencia` AS `solicitante_telefono_referencia`,
        `personas`.`persona_referencia` AS `solicitante_persona_referencia`,
        `personas`.`e_mail` AS `solicitante_email`,
        RIGHT(`beneficios`.`codigo_beneficio`,
            4) AS `beneficio_codigo_beneficio`,
        `organismos`.`concepto_1` AS `beneficio_codigo_beneficio_descripcion`,
        `beneficios`.`nro_ley` AS `beneficio_nro_ley`,
        `beneficios`.`tipo` AS `beneficio_tipo`,
        `beneficios`.`nro_beneficio` AS `beneficio_nro_beneficio`,
        `beneficios`.`sub_beneficio` AS `beneficio_sub_beneficio`,
        `beneficios`.`nro_legajo` AS `beneficio_nro_legajo`,
        `beneficios`.`fecha_ingreso` AS `beneficio_fecha_ingreso`,
        `beneficios`.`codigo_reparticion` AS `beneficio_codigo_reparticion`,
        RIGHT(TRIM(`beneficios`.`turno_pago`),
            5) AS `beneficio_codigo_turno_pago`,
        `beneficios`.`cbu` AS `beneficio_cbu`,
        `beneficios`.`banco_id` AS `beneficio_codigo_banco`,
        `bancos`.`nombre` AS `beneficio_codigo_banco_descripcion`,
        `beneficios`.`nro_sucursal` AS `beneficio_nro_sucursal`,
        `beneficios`.`nro_cta_bco` AS `beneficio_nro_cta_bco`,
        RIGHT(`beneficios`.`codigo_empresa`, 4) AS `beneficio_codigo_empresa`,
        `empresas`.`concepto_1` AS `beneficio_codigo_empresa_descripcion`,
        `vendedores`.`id` AS `vendedor_nro`,
        `persona_vendedor`.`cuit_cuil` AS `vendedor_cuit`,
        CONCAT(`persona_vendedor`.`apellido`,
                `persona_vendedor`.`nombre`) AS `vendedor_apenom`
    FROM
        ((((((((((((`mutual_producto_solicitudes` `solicitudes`
        JOIN `proveedores` ON ((`proveedores`.`id` = `solicitudes`.`proveedor_id`)))
        JOIN `personas` ON ((`solicitudes`.`persona_id` = `personas`.`id`)))
        JOIN `global_datos` `tipo_documento` ON ((`tipo_documento`.`id` = `personas`.`tipo_documento`)))
        JOIN `persona_beneficios` `beneficios` ON ((`solicitudes`.`persona_beneficio_id` = `beneficios`.`id`)))
        JOIN `global_datos` `producto` ON ((`producto`.`id` = `solicitudes`.`tipo_producto`)))
        JOIN `global_datos` `estado` ON ((`estado`.`id` = `solicitudes`.`estado`)))
        LEFT JOIN `global_datos` `organismos` ON ((`organismos`.`id` = `beneficios`.`codigo_beneficio`)))
        LEFT JOIN `global_datos` `empresas` ON ((`empresas`.`id` = `beneficios`.`codigo_empresa`)))
        LEFT JOIN `provincias` ON ((`provincias`.`id` = `personas`.`provincia_id`)))
        LEFT JOIN `vendedores` ON ((`vendedores`.`id` = `solicitudes`.`vendedor_id`)))
        LEFT JOIN `personas` `persona_vendedor` ON ((`persona_vendedor`.`id` = `vendedores`.`persona_id`)))
        LEFT JOIN `bancos` ON ((`bancos`.`id` = `beneficios`.`banco_id`)))
    WHERE
        ((`solicitudes`.`anulada` = 0)
            AND (IFNULL(`proveedores`.`codigo_acceso_ws`,
                '') <> '')) 
    UNION SELECT 
        `proveedores`.`codigo_acceso_ws` AS `proveedor_codigo_acceso_ws`,
        `solicitudes`.`id` AS `solicitud_numero`,
        `solicitudes`.`fecha` AS `solicitud_fecha`,
        `solicitudes`.`fecha_pago` AS `solicitud_fecha_pago`,
        `solicitudes`.`aprobada` AS `solicitud_aprobada`,
        RIGHT(`solicitudes`.`estado`, 4) AS `solicitud_codigo_estado`,
        `estado`.`concepto_1` AS `solicitud_codigo_estado_descripcion`,
        RIGHT(`solicitudes`.`tipo_producto`, 4) AS `solicitud_codigo_producto`,
        `producto`.`concepto_1` AS `solicitud_codigo_producto_descripcion`,
        `proveedores`.`razon_social` AS `solicitud_proveedor`,
        `solicitudes`.`importe_total` AS `solicitud_importe_total`,
        `solicitudes`.`cuotas` AS `solicitud_cantidad_cuotas`,
        `solicitudes`.`importe_cuota` AS `solicitud_importe_cuota`,
        `solicitudes`.`importe_solicitado` AS `solicitud_importe_solicitado`,
        `solicitudes`.`importe_percibido` AS `solicitud_importe_percibido`,
        `solicitudes`.`periodo_ini` AS `solicitud_periodo_inicio`,
        `solicitudes`.`primer_vto_socio` AS `solicitud_primer_vencimiento`,
        RIGHT(`personas`.`tipo_documento`,
            4) AS `solicitante_codigo_documento`,
        `tipo_documento`.`concepto_1` AS `solicitante_codigo_documento_descripcion`,
        `personas`.`documento` AS `solicitante_documento`,
        `personas`.`apellido` AS `solicitante_apellido`,
        `personas`.`nombre` AS `solicitante_nombre`,
        `personas`.`sexo` AS `solicitante_sexo`,
        `personas`.`calle` AS `solicitante_calle`,
        `personas`.`numero_calle` AS `solicitante_numero_calle`,
        `personas`.`piso` AS `solicitante_piso`,
        `personas`.`dpto` AS `solicitante_dpto`,
        `personas`.`barrio` AS `solicitante_barrio`,
        `personas`.`localidad` AS `solicitante_localidad`,
        `personas`.`codigo_postal` AS `solicitante_codigo_postal`,
        `personas`.`provincia_id` AS `solicitante_codigo_provincia`,
        `provincias`.`nombre` AS `solicitante_codigo_provincia_nombre`,
        `personas`.`nombre_conyuge` AS `solicitante_nombre_conyuge`,
        `personas`.`telefono_fijo` AS `solicitante_telefono_fijo`,
        `personas`.`telefono_movil` AS `solicitante_telefono_movil`,
        `personas`.`telefono_referencia` AS `solicitante_telefono_referencia`,
        `personas`.`persona_referencia` AS `solicitante_persona_referencia`,
        `personas`.`e_mail` AS `solicitante_email`,
        RIGHT(`beneficios`.`codigo_beneficio`,
            4) AS `beneficio_codigo_beneficio`,
        `organismos`.`concepto_1` AS `beneficio_codigo_beneficio_descripcion`,
        `beneficios`.`nro_ley` AS `beneficio_nro_ley`,
        `beneficios`.`tipo` AS `beneficio_tipo`,
        `beneficios`.`nro_beneficio` AS `beneficio_nro_beneficio`,
        `beneficios`.`sub_beneficio` AS `beneficio_sub_beneficio`,
        `beneficios`.`nro_legajo` AS `beneficio_nro_legajo`,
        `beneficios`.`fecha_ingreso` AS `beneficio_fecha_ingreso`,
        `beneficios`.`codigo_reparticion` AS `beneficio_codigo_reparticion`,
        RIGHT(TRIM(`beneficios`.`turno_pago`),
            5) AS `beneficio_codigo_turno_pago`,
        `beneficios`.`cbu` AS `beneficio_cbu`,
        `beneficios`.`banco_id` AS `beneficio_codigo_banco`,
        `bancos`.`nombre` AS `beneficio_codigo_banco_descripcion`,
        `beneficios`.`nro_sucursal` AS `beneficio_nro_sucursal`,
        `beneficios`.`nro_cta_bco` AS `beneficio_nro_cta_bco`,
        RIGHT(`beneficios`.`codigo_empresa`, 4) AS `beneficio_codigo_empresa`,
        `empresas`.`concepto_1` AS `beneficio_codigo_empresa_descripcion`,
        `vendedores`.`id` AS `vendedor_nro`,
        `persona_vendedor`.`cuit_cuil` AS `vendedor_cuit`,
        CONCAT(`persona_vendedor`.`apellido`,
                `persona_vendedor`.`nombre`) AS `vendedor_apenom`
    FROM
        ((((((((((((`mutual_producto_solicitudes` `solicitudes`
        JOIN `proveedores` ON ((`proveedores`.`id` = `solicitudes`.`reasignar_proveedor_id`)))
        JOIN `personas` ON ((`solicitudes`.`persona_id` = `personas`.`id`)))
        JOIN `global_datos` `tipo_documento` ON ((`tipo_documento`.`id` = `personas`.`tipo_documento`)))
        JOIN `persona_beneficios` `beneficios` ON ((`solicitudes`.`persona_beneficio_id` = `beneficios`.`id`)))
        JOIN `global_datos` `producto` ON ((`producto`.`id` = `solicitudes`.`tipo_producto`)))
        JOIN `global_datos` `estado` ON ((`estado`.`id` = `solicitudes`.`estado`)))
        LEFT JOIN `global_datos` `organismos` ON ((`organismos`.`id` = `beneficios`.`codigo_beneficio`)))
        LEFT JOIN `global_datos` `empresas` ON ((`empresas`.`id` = `beneficios`.`codigo_empresa`)))
        LEFT JOIN `provincias` ON ((`provincias`.`id` = `personas`.`provincia_id`)))
        LEFT JOIN `vendedores` ON ((`vendedores`.`id` = `solicitudes`.`vendedor_id`)))
        LEFT JOIN `personas` `persona_vendedor` ON ((`persona_vendedor`.`id` = `vendedores`.`persona_id`)))
        LEFT JOIN `bancos` ON ((`bancos`.`id` = `beneficios`.`banco_id`)))
    WHERE
        ((`solicitudes`.`anulada` = 0)
            AND (IFNULL(`proveedores`.`codigo_acceso_ws`,
                '') <> ''))