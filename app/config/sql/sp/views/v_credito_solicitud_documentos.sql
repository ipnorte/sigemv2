CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_credito_solicitud_documentos` AS
    (SELECT 
        `mutual_producto_solicitud_documentos`.`id` AS `ID`,
        `mutual_producto_solicitud_documentos`.`mutual_producto_solicitud_id` AS `MUTUAL_PRODUCTO_SOLICITUD_ID`,
        `mutual_producto_solicitud_documentos`.`file_name` AS `FILE_NAME`,
        `mutual_producto_solicitud_documentos`.`file_type` AS `FILE_TYPE`,
        `mutual_producto_solicitud_documentos`.`file_data` AS `FILE_DATA`
    FROM
        `mutual_producto_solicitud_documentos`)