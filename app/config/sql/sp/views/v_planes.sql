CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_planes` AS
    (SELECT 
        `pp`.`id` AS `ID`,
        `pp`.`proveedor_id` AS `PROVEEDOR_ID`,
        `p`.`razon_social` AS `PROVEEDOR`,
        `pp`.`descripcion` AS `DESCRIPCION_PLAN`,
        CONCAT(`p`.`razon_social_resumida`,
                ' ** ',
                `pp`.`descripcion`,
                ' **') AS `DESCRIPCION`,
        `pp`.`activo` AS `ACTIVO`,
        `p`.`reasignable` AS `REASIGNABLE`,
        `p`.`vendedores` AS `VENDEDORES`,
        IFNULL((SELECT 
                        MIN(`v_plan_montos`.`LIQUIDO`)
                    FROM
                        `v_plan_montos`
                    WHERE
                        (`v_plan_montos`.`PLAN_ID` = `pp`.`id`)),
                0) AS `MONTO_MINIMO`,
        IFNULL((SELECT 
                        MAX(`v_plan_montos`.`LIQUIDO`)
                    FROM
                        `v_plan_montos`
                    WHERE
                        (`v_plan_montos`.`PLAN_ID` = `pp`.`id`)),
                0) AS `MONTO_MAXIMO`,
        IFNULL((SELECT 
                        MIN(`v_plan_monto_cuotas`.`CUOTAS`)
                    FROM
                        `v_plan_monto_cuotas`
                    WHERE
                        (`v_plan_monto_cuotas`.`PLAN_ID` = `pp`.`id`)),
                0) AS `CUOTAS_MINIMO`,
        IFNULL((SELECT 
                        MAX(`v_plan_monto_cuotas`.`CUOTAS`)
                    FROM
                        `v_plan_monto_cuotas`
                    WHERE
                        (`v_plan_monto_cuotas`.`PLAN_ID` = `pp`.`id`)),
                0) AS `CUOTAS_MAXIMO`,
        (SELECT 
                MIN(`v_plan_monto_cuotas`.`IMPORTE`)
            FROM
                `v_plan_monto_cuotas`
            WHERE
                ((`v_plan_monto_cuotas`.`PLAN_ID` = `pp`.`id`)
                    AND (`v_plan_monto_cuotas`.`CUOTAS` = (SELECT 
                        MIN(`v_plan_monto_cuotas`.`CUOTAS`)
                    FROM
                        `v_plan_monto_cuotas`
                    WHERE
                        (`v_plan_monto_cuotas`.`PLAN_ID` = `pp`.`id`))))) AS `CUOTA_MONTO_MINIMO`,
        (SELECT 
                MAX(`v_plan_monto_cuotas`.`IMPORTE`)
            FROM
                `v_plan_monto_cuotas`
            WHERE
                ((`v_plan_monto_cuotas`.`PLAN_ID` = `pp`.`id`)
                    AND (`v_plan_monto_cuotas`.`CUOTAS` = (SELECT 
                        MAX(`v_plan_monto_cuotas`.`CUOTAS`)
                    FROM
                        `v_plan_monto_cuotas`
                    WHERE
                        (`v_plan_monto_cuotas`.`PLAN_ID` = `pp`.`id`))))) AS `CUOTA_MONTO_MAXIMO`
    FROM
        (`proveedor_planes` `pp`
        JOIN `proveedores` `p`)
    WHERE
        (`pp`.`proveedor_id` = `p`.`id`))