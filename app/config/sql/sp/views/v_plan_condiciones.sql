CREATE 
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
        (`ppgc`.`importe` * `ppgc`.`cuotas`) AS `TOTAL`
    FROM
        ((`v_planes` `pp`
        JOIN `proveedor_plan_grillas` `ppg`)
        JOIN `proveedor_plan_grilla_cuotas` `ppgc`)
    WHERE
        ((`pp`.`ID` = `ppg`.`proveedor_plan_id`)
            AND (`ppg`.`id` = `ppgc`.`proveedor_plan_grilla_id`)
            AND (`ppg`.`vigencia_desde` < NOW())))