CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `mutual22`@`%` 
    SQL SECURITY DEFINER
VIEW `v_proveedores` AS
    SELECT 
        `cordobas_soluciones`.`proveedores`.`id` AS `ID`,
        `cordobas_soluciones`.`proveedores`.`cuit` AS `CUIT`,
        `cordobas_soluciones`.`proveedores`.`razon_social` AS `RAZON_SOCIAL`,
        `cordobas_soluciones`.`proveedores`.`razon_social_resumida` AS `RAZON_SOCIAL_RESUMIDA`,
        `cordobas_soluciones`.`proveedores`.`activo` AS `ACTIVO`,
        `cordobas_soluciones`.`proveedores`.`calle` AS `CALLE`,
        `cordobas_soluciones`.`proveedores`.`numero_calle` AS `NUMERO_CALLE`,
        `cordobas_soluciones`.`proveedores`.`piso` AS `PISO`,
        `cordobas_soluciones`.`proveedores`.`dpto` AS `DPTO`,
        `cordobas_soluciones`.`proveedores`.`barrio` AS `BARRIO`,
        `cordobas_soluciones`.`proveedores`.`localidad` AS `LOCALIDAD`,
        `cordobas_soluciones`.`proveedores`.`codigo_postal` AS `CODIGO_POSTAL`,
        `cordobas_soluciones`.`proveedores`.`telefono_fijo` AS `TELEFONO_FIJO`,
        `cordobas_soluciones`.`proveedores`.`telefono_movil` AS `TELEFONO_MOVIL`,
        `cordobas_soluciones`.`proveedores`.`fax` AS `FAX`,
        `cordobas_soluciones`.`proveedores`.`email` AS `EMAIL`,
        `cordobas_soluciones`.`proveedores`.`codigo_acceso_ws` AS `CODIGO_ACCESO_WS`
    FROM
        `proveedores`