CREATE 
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
        (`proveedor_plan_grilla_cuotas`.`importe` * `proveedor_plan_grilla_cuotas`.`cuotas`) AS `TOTAL`
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
    GROUP BY `proveedor_planes`.`id` , `proveedor_plan_grillas`.`vigencia_desde` , `proveedor_plan_grilla_cuotas`.`liquido` , `proveedor_plan_grilla_cuotas`.`cuotas`
    ORDER BY `proveedor_planes`.`id` , `proveedor_plan_grilla_cuotas`.`liquido` , `proveedor_plan_grilla_cuotas`.`cuotas`)