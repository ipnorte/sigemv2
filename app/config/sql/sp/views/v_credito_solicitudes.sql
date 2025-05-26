CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_credito_solicitudes` AS
    (SELECT 
        `mutual_producto_solicitudes`.`id` AS `ID`,
        `mutual_producto_solicitudes`.`proveedor_id` AS `PROVEEDOR_ID`,
        `mutual_producto_solicitudes`.`proveedor_plan_id` AS `PROVEEDOR_PLAN_ID`,
        `mutual_producto_solicitudes`.`persona_id` AS `PERSONA_ID`,
        `mutual_producto_solicitudes`.`socio_id` AS `CLIENTE_ID`,
        `mutual_producto_solicitudes`.`persona_beneficio_id` AS `PERSONA_BENEFICIO_ID`,
        `mutual_producto_solicitudes`.`aprobada` AS `APROBADA`,
        `mutual_producto_solicitudes`.`anulada` AS `ANULADA`,
        `mutual_producto_solicitudes`.`fecha` AS `FECHA`,
        `mutual_producto_solicitudes`.`fecha_pago` AS `FECHA_PAGO`,
        `mutual_producto_solicitudes`.`tipo_orden_dto` AS `TIPO_ORDEN_DTO`,
        `mutual_producto_solicitudes`.`tipo_producto` AS `TIPO_PRODUCTO`,
        IF(((`mutual_producto_solicitudes`.`anulada` = 1)
                AND (`mutual_producto_solicitudes`.`estado` = 'MUTUESTA0001')),
            'MUTUESTA0000',
            `mutual_producto_solicitudes`.`estado`) AS `ESTADO`,
        `mutual_producto_solicitudes`.`importe_total` AS `IMPORTE_TOTAL`,
        `mutual_producto_solicitudes`.`cuotas` AS `CUOTAS`,
        `mutual_producto_solicitudes`.`importe_cuota` AS `IMPORTE_CUOTA`,
        `mutual_producto_solicitudes`.`importe_solicitado` AS `IMPORTE_SOLICITADO`,
        `mutual_producto_solicitudes`.`importe_percibido` AS `IMPORTE_PERCIBIDO`,
        `mutual_producto_solicitudes`.`observaciones` AS `OBSERVACIONES`,
        `mutual_producto_solicitudes`.`orden_descuento_id` AS `ORDEN_DESCUENTO_ID`,
        `mutual_producto_solicitudes`.`aprobada_por` AS `APROBADA_POR`,
        `mutual_producto_solicitudes`.`aprobada_el` AS `APROBADA_EL`,
        `mutual_producto_solicitudes`.`vendedor_id` AS `VENDEDOR_ID`,
        `mutual_producto_solicitudes`.`vendedor_remito_id` AS `VENDEDOR_REMITO_ID`,
        `mutual_producto_solicitudes`.`vendedor_notificar` AS `VENDEDOR_NOTIFICAR`,
        `mutual_producto_solicitudes`.`user_created` AS `EMITIDA_POR`,
        `mutual_producto_solicitudes`.`periodo_ini` AS `PERIODO_INI`,
        `mutual_producto_solicitudes`.`primer_vto_socio` AS `PRIMER_VTO_SOCIO`,
        `mutual_producto_solicitudes`.`forma_pago` AS `FORMA_PAGO`,
        IF(((`mutual_producto_solicitudes`.`aprobada` = 1)
                AND (`mutual_producto_solicitudes`.`anulada` = 0)),
            CONCAT(`mutual_producto_solicitudes`.`tipo_orden_dto`,
                    ' ',
                    `mutual_producto_solicitudes`.`orden_descuento_id`),
            '') AS `ORDEN_DESCUENTO`
    FROM
        `mutual_producto_solicitudes`
    WHERE
        (`mutual_producto_solicitudes`.`tipo_orden_dto` = 'EXPTE'))