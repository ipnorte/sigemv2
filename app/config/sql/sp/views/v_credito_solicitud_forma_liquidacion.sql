CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_credito_solicitud_forma_liquidacion` AS
    (SELECT 
        `v_global_datos`.`ID` AS `ID`,
        `v_global_datos`.`CONCEPTO_1` AS `CONCEPTO_1`,
        `v_global_datos`.`CONCEPTO_2` AS `CONCEPTO_2`,
        `v_global_datos`.`CONCEPTO_3` AS `CONCEPTO_3`,
        `v_global_datos`.`LOGICO_1` AS `LOGICO_1`,
        `v_global_datos`.`LOGICO_2` AS `LOGICO_2`,
        `v_global_datos`.`ENTERO_1` AS `ENTERO_1`,
        `v_global_datos`.`ENTERO_2` AS `ENTERO_2`,
        `v_global_datos`.`DECIMAL_1` AS `DECIMAL_1`,
        `v_global_datos`.`DECIMAL_2` AS `DECIMAL_2`,
        `v_global_datos`.`FECHA_1` AS `FECHA_1`,
        `v_global_datos`.`FECHA_2` AS `FECHA_2`,
        `v_global_datos`.`TEXTO_1` AS `TEXTO_1`,
        `v_global_datos`.`TEXTO_2` AS `TEXTO_2`
    FROM
        `v_global_datos`
    WHERE
        ((`v_global_datos`.`ID` LIKE 'MUTUFPAG%')
            AND (`v_global_datos`.`ID` <> 'MUTUFPAG'))
    ORDER BY `v_global_datos`.`CONCEPTO_1`)