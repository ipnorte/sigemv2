CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_cliente_cancelaciones_emitidas` AS
    (SELECT 
        `v_clientes`.`ID` AS `CLIENTE_ID`,
        `v_cancelacion_ordenes`.`ID` AS `CANCELACION_ID`
    FROM
        (`v_clientes`
        JOIN `v_cancelacion_ordenes` ON ((`v_cancelacion_ordenes`.`CLIENTE_ID` = `v_clientes`.`ID`)))
    WHERE
        ((`v_cancelacion_ordenes`.`ESTADO` = 'E')
            AND (`v_cancelacion_ordenes`.`VENCIMIENTO` >= CURDATE())
            AND (NOT (`v_cancelacion_ordenes`.`ID` IN (SELECT 
                `v_credito_solcitud_cancelaciones`.`CANCELACION_ORDEN_ID`
            FROM
                `v_credito_solcitud_cancelaciones`)))))