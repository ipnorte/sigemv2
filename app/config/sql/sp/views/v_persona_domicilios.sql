CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_persona_domicilios` AS
    (SELECT 
        `personas`.`id` AS `ID`,
        `personas`.`calle` AS `CALLE`,
        `personas`.`numero_calle` AS `NUMERO_CALLE`,
        `personas`.`piso` AS `PISO`,
        `personas`.`dpto` AS `DPTO`,
        `personas`.`barrio` AS `BARRIO`,
        `personas`.`localidad_id` AS `LOCALIDAD_ID`,
        `personas`.`localidad` AS `LOCALIDAD_DESC`,
        `personas`.`codigo_postal` AS `CODIGO_POSTAL`,
        `personas`.`provincia_id` AS `PROVINCIA_ID`
    FROM
        `personas`)