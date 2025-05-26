CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_localidades` AS
    (SELECT 
        `localidades`.`id` AS `ID`,
        `localidades`.`cp` AS `CP`,
        `localidades`.`nombre` AS `NOMBRE`,
        `localidades`.`provincia_id` AS `PROVINCIA_ID`,
        `localidades`.`letra_provincia` AS `LETRA_PROVINCIA`
    FROM
        `localidades`
    WHERE
        (`localidades`.`provincia_id` IS NOT NULL))