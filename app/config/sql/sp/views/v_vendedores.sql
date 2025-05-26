CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_vendedores` AS
    (SELECT 
        `vendedores`.`id` AS `ID`,
        `vendedores`.`persona_id` AS `PERSONA_ID`,
        `vendedores`.`usuario_id` AS `USUARIO_ID`,
        `vendedores`.`activo` AS `ACTIVO`,
        (SELECT 
                COUNT(1)
            FROM
                `v_credito_solicitudes`
            WHERE
                ((`vendedores`.`id` = `v_credito_solicitudes`.`VENDEDOR_ID`)
                    AND (`v_credito_solicitudes`.`VENDEDOR_NOTIFICAR` = 1))) AS `NOTIFICACIONES`
    FROM
        `vendedores`)