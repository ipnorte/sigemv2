CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_plan_organismos` AS
    (SELECT 
        `proveedor_plan_organismos`.`proveedor_plan_id` AS `PLAN_ID`,
        `proveedor_plan_organismos`.`codigo_organismo` AS `CODIGO_ORGANISMO`
    FROM
        `proveedor_plan_organismos`)