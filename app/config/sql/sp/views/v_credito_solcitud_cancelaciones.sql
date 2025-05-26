CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_credito_solcitud_cancelaciones` AS
    (SELECT 
        `mutual_producto_solicitud_cancelaciones`.`mutual_producto_solicitud_id` AS `MUTUAL_PRODUCTO_SOLICITUD_ID`,
        `mutual_producto_solicitud_cancelaciones`.`cancelacion_orden_id` AS `CANCELACION_ORDEN_ID`
    FROM
        `mutual_producto_solicitud_cancelaciones`)