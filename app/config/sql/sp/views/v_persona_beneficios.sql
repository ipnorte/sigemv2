CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_persona_beneficios` AS
    (SELECT 
        `persona_beneficios`.`id` AS `ID`,
        `persona_beneficios`.`persona_id` AS `PERSONA_ID`,
        `persona_beneficios`.`codigo_beneficio` AS `CODIGO_ORGANISMO`,
        `persona_beneficios`.`nro_ley` AS `NRO_LEY`,
        `persona_beneficios`.`tipo` AS `TIPO`,
        `persona_beneficios`.`nro_beneficio` AS `NRO_BENEFICIO`,
        `persona_beneficios`.`sub_beneficio` AS `SUB_BENEFICIO`,
        `persona_beneficios`.`nro_legajo` AS `NRO_LEGAJO`,
        `persona_beneficios`.`fecha_ingreso` AS `FECHA_INGRESO`,
        `persona_beneficios`.`codigo_reparticion` AS `CODIGO_REPARTICION`,
        `persona_beneficios`.`turno_pago` AS `TURNO_PAGO`,
        `persona_beneficios`.`cbu` AS `CBU`,
        `persona_beneficios`.`banco_id` AS `BANCO_ID`,
        `persona_beneficios`.`nro_sucursal` AS `NRO_SUCURSAL`,
        `persona_beneficios`.`tipo_cta_bco` AS `TIPO_CTA_BCO`,
        `persona_beneficios`.`nro_cta_bco` AS `NRO_CTA_BANCO`,
        `persona_beneficios`.`codigo_empresa` AS `CODIGO_EMPRESA`,
        `persona_beneficios`.`principal` AS `PRINCIPAL`,
        `persona_beneficios`.`activo` AS `ACTIVO`,
        `persona_beneficios`.`porcentaje` AS `PORCENTAJE`,
        `persona_beneficios`.`acuerdo_debito` AS `ACUERDO_DEBITO`,
        `persona_beneficios`.`importe_max_registro_cbu` AS `IMPORTE_MAX_REGISTRO_CBU`,
        CONCAT(`gl1`.`concepto_1`,
                ' - ',
                `gl2`.`concepto_1`,
                ' | ',
                IF((`persona_beneficios`.`codigo_empresa` = 'MUTUEMPRP001'),
                    `persona_beneficios`.`turno_pago`,
                    ''),
                ' | CBU: ',
                `persona_beneficios`.`cbu`) AS `CADENA`
    FROM
        ((`persona_beneficios`
        JOIN `global_datos` `gl1` ON ((`gl1`.`id` = `persona_beneficios`.`codigo_beneficio`)))
        JOIN `global_datos` `gl2` ON ((`gl2`.`id` = `persona_beneficios`.`codigo_empresa`)))
    WHERE
        (`persona_beneficios`.`activo` = 1))