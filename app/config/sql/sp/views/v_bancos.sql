CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_bancos` AS
    (SELECT 
        `bancos`.`id` AS `ID`,
        `bancos`.`nombre` AS `NOMBRE`,
        `bancos`.`activo` AS `ACTIVO`,
        `bancos`.`beneficio` AS `BENEFICIO`
    FROM
        `bancos`)