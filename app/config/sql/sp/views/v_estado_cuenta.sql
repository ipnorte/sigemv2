CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_estado_cuenta` AS
    SELECT 
        `orden_descuento_cuotas`.`id` AS `ID`,
        `orden_descuento_cuotas`.`socio_id` AS `SOCIO_ID`,
        'VENCIDO' AS `TIPO`,
        `orden_descuento_cuotas`.`orden_descuento_id` AS `ORDEN_DESCUENTO_ID`,
        CONCAT(`orden_descuentos`.`tipo_orden_dto`,
                ' #',
                `orden_descuentos`.`numero`) AS `TIPO_NUMERO`,
        `tipo_cuota`.`concepto_1` AS `CONCEPTO`,
        `orden_descuento_cuotas`.`periodo` AS `PERIODO`,
        CONCAT(SUBSTR(`orden_descuento_cuotas`.`periodo`,
                    5,
                    2),
                '/',
                SUBSTR(`orden_descuento_cuotas`.`periodo`,
                    1,
                    4)) AS `PERIODO_STR`,
        CONCAT(LPAD(`orden_descuento_cuotas`.`nro_cuota`,
                        2,
                        '0'),
                '/',
                LPAD(`orden_descuentos`.`cuotas`, 2, '0')) AS `CUOTA`,
        `orden_descuento_cuotas`.`importe` AS `IMPORTE_ORIGINAL`,
        IFNULL((SELECT 
                        SUM(`orden_descuento_cobro_cuotas`.`importe`)
                    FROM
                        `orden_descuento_cobro_cuotas`
                    WHERE
                        (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),
                0) AS `PAGOS`,
        (`orden_descuento_cuotas`.`importe` - IFNULL((SELECT 
                        SUM(`orden_descuento_cobro_cuotas`.`importe`)
                    FROM
                        `orden_descuento_cobro_cuotas`
                    WHERE
                        (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),
                0)) AS `SALDO_CONCILIADO`,
        IFNULL((SELECT 
                        SUM(`liquidacion_cuotas`.`importe_debitado`)
                    FROM
                        `liquidacion_cuotas`
                    WHERE
                        ((`liquidacion_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)
                            AND (`liquidacion_cuotas`.`imputada` = 0)
                            AND (`liquidacion_cuotas`.`para_imputar` = 1))),
                0) AS `PENDIENTE_ACREDITAR`,
        ((`orden_descuento_cuotas`.`importe` - IFNULL((SELECT 
                        SUM(`orden_descuento_cobro_cuotas`.`importe`)
                    FROM
                        `orden_descuento_cobro_cuotas`
                    WHERE
                        (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),
                0)) - IFNULL((SELECT 
                        SUM(`liquidacion_cuotas`.`importe_debitado`)
                    FROM
                        `liquidacion_cuotas`
                    WHERE
                        ((`liquidacion_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)
                            AND (`liquidacion_cuotas`.`imputada` = 0)
                            AND (`liquidacion_cuotas`.`para_imputar` = 1))),
                0)) AS `SALDO`
    FROM
        ((`orden_descuento_cuotas`
        JOIN `orden_descuentos` ON ((`orden_descuentos`.`id` = `orden_descuento_cuotas`.`orden_descuento_id`)))
        JOIN `global_datos` `tipo_cuota` ON ((`tipo_cuota`.`id` = `orden_descuento_cuotas`.`tipo_cuota`)))
    WHERE
        ((`orden_descuento_cuotas`.`importe` > IFNULL((SELECT 
                        SUM(`orden_descuento_cobro_cuotas`.`importe`)
                    FROM
                        `orden_descuento_cobro_cuotas`
                    WHERE
                        (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),
                0))
            AND (`orden_descuento_cuotas`.`periodo` < DATE_FORMAT(NOW(), '%Y%m'))
            AND (`orden_descuento_cuotas`.`estado` <> 'B')) 
    UNION SELECT 
        `orden_descuento_cuotas`.`id` AS `ID`,
        `orden_descuento_cuotas`.`socio_id` AS `SOCIO_ID`,
        'CORRIENTE' AS `TIPO`,
        `orden_descuento_cuotas`.`orden_descuento_id` AS `ORDEN_DESCUENTO_ID`,
        CONCAT(`orden_descuentos`.`tipo_orden_dto`,
                ' #',
                `orden_descuentos`.`numero`) AS `TIPO_NUMERO`,
        `tipo_cuota`.`concepto_1` AS `CONCEPTO`,
        `orden_descuento_cuotas`.`periodo` AS `PERIODO`,
        CONCAT(SUBSTR(`orden_descuento_cuotas`.`periodo`,
                    5,
                    2),
                '/',
                SUBSTR(`orden_descuento_cuotas`.`periodo`,
                    1,
                    4)) AS `PERIODO_STR`,
        CONCAT(LPAD(`orden_descuento_cuotas`.`nro_cuota`,
                        2,
                        '0'),
                '/',
                LPAD(`orden_descuentos`.`cuotas`, 2, '0')) AS `CUOTA`,
        `orden_descuento_cuotas`.`importe` AS `IMPORTE_ORIGINAL`,
        IFNULL((SELECT 
                        SUM(`orden_descuento_cobro_cuotas`.`importe`)
                    FROM
                        `orden_descuento_cobro_cuotas`
                    WHERE
                        (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),
                0) AS `PAGOS`,
        (`orden_descuento_cuotas`.`importe` - IFNULL((SELECT 
                        SUM(`orden_descuento_cobro_cuotas`.`importe`)
                    FROM
                        `orden_descuento_cobro_cuotas`
                    WHERE
                        (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),
                0)) AS `SALDO_CONCILIADO`,
        IFNULL((SELECT 
                        SUM(`liquidacion_cuotas`.`importe_debitado`)
                    FROM
                        `liquidacion_cuotas`
                    WHERE
                        ((`liquidacion_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)
                            AND (`liquidacion_cuotas`.`imputada` = 0)
                            AND (`liquidacion_cuotas`.`para_imputar` = 1))),
                0) AS `PENDIENTE_ACREDITAR`,
        ((`orden_descuento_cuotas`.`importe` - IFNULL((SELECT 
                        SUM(`orden_descuento_cobro_cuotas`.`importe`)
                    FROM
                        `orden_descuento_cobro_cuotas`
                    WHERE
                        (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),
                0)) - IFNULL((SELECT 
                        SUM(`liquidacion_cuotas`.`importe_debitado`)
                    FROM
                        `liquidacion_cuotas`
                    WHERE
                        ((`liquidacion_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)
                            AND (`liquidacion_cuotas`.`imputada` = 0)
                            AND (`liquidacion_cuotas`.`para_imputar` = 1))),
                0)) AS `SALDO`
    FROM
        ((`orden_descuento_cuotas`
        JOIN `orden_descuentos` ON ((`orden_descuentos`.`id` = `orden_descuento_cuotas`.`orden_descuento_id`)))
        JOIN `global_datos` `tipo_cuota` ON ((`tipo_cuota`.`id` = `orden_descuento_cuotas`.`tipo_cuota`)))
    WHERE
        ((`orden_descuento_cuotas`.`importe` > IFNULL((SELECT 
                        SUM(`orden_descuento_cobro_cuotas`.`importe`)
                    FROM
                        `orden_descuento_cobro_cuotas`
                    WHERE
                        (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),
                0))
            AND (`orden_descuento_cuotas`.`periodo` = DATE_FORMAT(NOW(), '%Y%m'))
            AND (`orden_descuento_cuotas`.`estado` <> 'B')) 
    UNION SELECT 
        `orden_descuento_cuotas`.`id` AS `ID`,
        `orden_descuento_cuotas`.`socio_id` AS `SOCIO_ID`,
        'A_VENCER' AS `TIPO`,
        `orden_descuento_cuotas`.`orden_descuento_id` AS `ORDEN_DESCUENTO_ID`,
        CONCAT(`orden_descuentos`.`tipo_orden_dto`,
                ' #',
                `orden_descuentos`.`numero`) AS `TIPO_NUMERO`,
        `tipo_cuota`.`concepto_1` AS `CONCEPTO`,
        `orden_descuento_cuotas`.`periodo` AS `PERIODO`,
        CONCAT(SUBSTR(`orden_descuento_cuotas`.`periodo`,
                    5,
                    2),
                '/',
                SUBSTR(`orden_descuento_cuotas`.`periodo`,
                    1,
                    4)) AS `PERIODO_STR`,
        CONCAT(LPAD(`orden_descuento_cuotas`.`nro_cuota`,
                        2,
                        '0'),
                '/',
                LPAD(`orden_descuentos`.`cuotas`, 2, '0')) AS `CUOTA`,
        `orden_descuento_cuotas`.`importe` AS `IMPORTE_ORIGINAL`,
        IFNULL((SELECT 
                        SUM(`orden_descuento_cobro_cuotas`.`importe`)
                    FROM
                        `orden_descuento_cobro_cuotas`
                    WHERE
                        (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),
                0) AS `PAGOS`,
        (`orden_descuento_cuotas`.`importe` - IFNULL((SELECT 
                        SUM(`orden_descuento_cobro_cuotas`.`importe`)
                    FROM
                        `orden_descuento_cobro_cuotas`
                    WHERE
                        (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),
                0)) AS `SALDO_CONCILIADO`,
        IFNULL((SELECT 
                        SUM(`liquidacion_cuotas`.`importe_debitado`)
                    FROM
                        `liquidacion_cuotas`
                    WHERE
                        ((`liquidacion_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)
                            AND (`liquidacion_cuotas`.`imputada` = 0)
                            AND (`liquidacion_cuotas`.`para_imputar` = 1))),
                0) AS `PENDIENTE_ACREDITAR`,
        ((`orden_descuento_cuotas`.`importe` - IFNULL((SELECT 
                        SUM(`orden_descuento_cobro_cuotas`.`importe`)
                    FROM
                        `orden_descuento_cobro_cuotas`
                    WHERE
                        (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),
                0)) - IFNULL((SELECT 
                        SUM(`liquidacion_cuotas`.`importe_debitado`)
                    FROM
                        `liquidacion_cuotas`
                    WHERE
                        ((`liquidacion_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)
                            AND (`liquidacion_cuotas`.`imputada` = 0)
                            AND (`liquidacion_cuotas`.`para_imputar` = 1))),
                0)) AS `SALDO`
    FROM
        ((`orden_descuento_cuotas`
        JOIN `orden_descuentos` ON ((`orden_descuentos`.`id` = `orden_descuento_cuotas`.`orden_descuento_id`)))
        JOIN `global_datos` `tipo_cuota` ON ((`tipo_cuota`.`id` = `orden_descuento_cuotas`.`tipo_cuota`)))
    WHERE
        ((`orden_descuento_cuotas`.`importe` > IFNULL((SELECT 
                        SUM(`orden_descuento_cobro_cuotas`.`importe`)
                    FROM
                        `orden_descuento_cobro_cuotas`
                    WHERE
                        (`orden_descuento_cobro_cuotas`.`orden_descuento_cuota_id` = `orden_descuento_cuotas`.`id`)),
                0))
            AND (`orden_descuento_cuotas`.`periodo` > DATE_FORMAT(NOW(), '%Y%m'))
            AND (`orden_descuento_cuotas`.`estado` <> 'B'))
    ORDER BY `PERIODO` , `ID` , `CUOTA`