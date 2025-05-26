CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_cancelacion_ordenes` AS
    (SELECT 
        `cancelacion_ordenes`.`id` AS `ID`,
        `cancelacion_ordenes`.`estado` AS `ESTADO`,
        IF((`cancelacion_ordenes`.`estado` = 'E'),
            'EMITIDA',
            'PROCESADA') AS `ESTADO_DESC`,
        `cancelacion_ordenes`.`socio_id` AS `CLIENTE_ID`,
        `cancelacion_ordenes`.`importe_proveedor` AS `IMPORTE_CANCELA`,
        `cancelacion_ordenes`.`importe_seleccionado` AS `IMPORTE_SELECCIONADO`,
        `cancelacion_ordenes`.`saldo_orden_dto` AS `SALDO_ORDEN`,
        `cancelacion_ordenes`.`importe_cuota` AS `IMPORTE_CUOTA`,
        `cancelacion_ordenes`.`importe_diferencia` AS `DEBITO_CREDITO`,
        ((`cancelacion_ordenes`.`saldo_orden_dto` - `cancelacion_ordenes`.`importe_proveedor`) + `cancelacion_ordenes`.`importe_diferencia`) AS `SALDO`,
        `cancelacion_ordenes`.`fecha_vto` AS `VENCIMIENTO`,
        IF((`cancelacion_ordenes`.`tipo_cancelacion` = 'T'),
            'TOTAL',
            'PARCIAL') AS `TIPO`,
        `cancelacion_ordenes`.`observaciones` AS `OBSERVACIONES`,
        `cancelacion_ordenes`.`concepto` AS `CONCEPTO`,
        (SELECT 
                COUNT(0)
            FROM
                `cancelacion_orden_cuotas`
            WHERE
                (`cancelacion_orden_cuotas`.`cancelacion_orden_id` = `cancelacion_ordenes`.`id`)) AS `CANTIDAD_CUOTAS`,
        `proveedores`.`id` AS `PROVEEDOR_ID`,
        UCASE(`proveedores`.`razon_social`) AS `A_LA_ORDEN_DE`,
        `mutual_producto_solicitudes`.`id` AS `SOLICITUD_CREDITO_ID`
    FROM
        (((`cancelacion_ordenes`
        JOIN `proveedores` ON ((`proveedores`.`id` = `cancelacion_ordenes`.`orden_proveedor_id`)))
        LEFT JOIN `orden_descuentos` ON ((`orden_descuentos`.`id` = `cancelacion_ordenes`.`orden_descuento_id`)))
        LEFT JOIN `mutual_producto_solicitudes` ON (((`mutual_producto_solicitudes`.`id` = `orden_descuentos`.`numero`)
            AND (`mutual_producto_solicitudes`.`tipo_orden_dto` = `orden_descuentos`.`tipo_orden_dto`)))))