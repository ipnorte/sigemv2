CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_personas` AS
    (SELECT 
        `personas`.`id` AS `ID`,
        `personas`.`id` AS `DOMICILIO_ID`,
        `personas`.`tipo_documento` AS `TIPO_DOCUMENTO`,
        `personas`.`documento` AS `DOCUMENTO`,
        `personas`.`apellido` AS `APELLIDO`,
        `personas`.`nombre` AS `NOMBRE`,
        `personas`.`fecha_nacimiento` AS `FECHA_NACIMIENTO`,
        `personas`.`fecha_fallecimiento` AS `FECHA_FALLECIMIENTO`,
        `personas`.`fallecida` AS `FALLECIDA`,
        `personas`.`sexo` AS `SEXO`,
        `personas`.`estado_civil` AS `ESTADO_CIVIL`,
        `personas`.`calle` AS `CALLE`,
        `personas`.`numero_calle` AS `NUMERO_CALLE`,
        `personas`.`piso` AS `PISO`,
        `personas`.`dpto` AS `DPTO`,
        `personas`.`barrio` AS `BARRIO`,
        `personas`.`localidad_id` AS `LOCALIDAD_ID`,
        `personas`.`localidad` AS `LOCALIDAD_DESC`,
        `personas`.`codigo_postal` AS `CODIGO_POSTAL`,
        `personas`.`provincia_id` AS `PROVINCIA_ID`,
        `personas`.`cuit_cuil` AS `CUIT_CUIL`,
        `personas`.`nombre_conyuge` AS `NOMBRE_CONYUGE`,
        `personas`.`telefono_fijo` AS `TELEFONO_FIJO`,
        `personas`.`telefono_movil` AS `TELEFONO_MOVIL`,
        `personas`.`telefono_referencia` AS `TELEFONO_REFERENCIA`,
        `personas`.`persona_referencia` AS `PERSONA_REFERENCIA`,
        `personas`.`e_mail` AS `E_MAIL`,
        `personas`.`tipo_vivienda` AS `TIPO_VIVIENDA`,
        `personas`.`filial` AS `FILIAL`,
        `personas`.`user_created` AS `USER_CREATED`,
        `personas`.`user_modified` AS `USER_MODIFIED`,
        `personas`.`created` AS `CREATED`,
        `personas`.`modified` AS `MODIFIED`
    FROM
        `personas`)