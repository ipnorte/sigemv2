CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_vendedor_remitos` AS
    (SELECT 
        `vendedor_remitos`.`id` AS `ID`,
        `vendedor_remitos`.`vendedor_id` AS `VENDEDOR_ID`,
        `vendedor_remitos`.`observaciones` AS `OBSERVACIONES`,
        `vendedor_remitos`.`user_created` AS `USER_CREATED`,
        `vendedor_remitos`.`created` AS `CREATED`,
        `vendedor_remitos`.`anulado` AS `ANULADO`
    FROM
        `vendedor_remitos`)