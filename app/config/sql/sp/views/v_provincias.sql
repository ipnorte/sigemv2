CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_provincias` AS
    (SELECT 
        `provincias`.`id` AS `ID`,
        `provincias`.`nombre` AS `NOMBRE`,
        `provincias`.`letra` AS `LETRA`
    FROM
        `provincias`)