CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_grupos` AS
    (SELECT 
        `grupos`.`id` AS `ID`,
        `grupos`.`nombre` AS `NOMBRE`,
        `grupos`.`activo` AS `ACTIVO`
    FROM
        `grupos`)