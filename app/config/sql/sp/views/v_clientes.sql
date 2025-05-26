CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_clientes` AS
    (SELECT 
        `socios`.`id` AS `ID`,
        `socios`.`categoria` AS `CATEGORIA`,
        `socios`.`persona_id` AS `PERSONA_ID`,
        `socios`.`persona_beneficio_id` AS `BENEFICIO_ID`,
        `socios`.`activo` AS `ACTIVO`,
        `socios`.`fecha_alta` AS `FECHA_ALTA`,
        `socios`.`orden_descuento_id` AS `ORDEN_DESCUENTO_ID`,
        `socios`.`calificacion` AS `CALIFICACION`,
        `socios`.`fecha_calificacion` AS `FECHA_CALIFICACION`,
        `socios`.`codigo_baja` AS `CODIGO_BAJA`,
        `socios`.`fecha_baja` AS `FECHA_BAJA`,
        `socios`.`observaciones` AS `OBSERVACIONES`
    FROM
        `socios`)