CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_global_datos` AS
    (SELECT 
        `global_datos`.`id` AS `ID`,
        `global_datos`.`concepto_1` AS `CONCEPTO_1`,
        `global_datos`.`concepto_2` AS `CONCEPTO_2`,
        `global_datos`.`concepto_3` AS `CONCEPTO_3`,
        `global_datos`.`logico_1` AS `LOGICO_1`,
        `global_datos`.`logico_2` AS `LOGICO_2`,
        `global_datos`.`entero_1` AS `ENTERO_1`,
        `global_datos`.`entero_2` AS `ENTERO_2`,
        `global_datos`.`decimal_1` AS `DECIMAL_1`,
        `global_datos`.`decimal_2` AS `DECIMAL_2`,
        `global_datos`.`fecha_1` AS `FECHA_1`,
        `global_datos`.`fecha_2` AS `FECHA_2`,
        `global_datos`.`texto_1` AS `TEXTO_1`,
        `global_datos`.`texto_2` AS `TEXTO_2`
    FROM
        `global_datos`)