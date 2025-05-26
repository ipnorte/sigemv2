alter table `sigem_db`.`banco_cuenta_movimientos` add index `idx_recibo_id` (`recibo_id`);
alter table `sigem_db`.`banco_cuenta_movimientos` add index `idx_orden_pago_id` (`orden_pago_id`);

alter table `sigem_db`.`cliente_factura_detalles` add index `idx_cliente_factura_id` (`cliente_factura_id`);

alter table `sigem_db`.`cliente_facturas` add index `idx_cliente_id` (`cliente_id`);
alter table `sigem_db`.`cliente_facturas` add index `NewIndex1` (`ejercicio_id`, `co_plan_cuenta_id`);
alter table `sigem_db`.`cliente_facturas` drop key `NewIndex1`;
alter table `sigem_db`.`cliente_facturas` add index `idx_ejercio_plan_cuenta` (`ejercicio_id`, `co_plan_cuenta_id`);
alter table `sigem_db`.`cliente_facturas` add index `idx_liquidacion_id` (`liquidacion_id`);
alter table `sigem_db`.`cliente_facturas` add index `idx_orden_caja_cobro` (`orden_caja_cobro_id`);
alter table `sigem_db`.`cliente_facturas` add index `idx_orden_descuento_cobro` (`orden_descuento_cobro_id`);

alter table `sigem_db`.`orden_pago_detalles` add index `idx_proveedor` (`proveedor_id`);
alter table `sigem_db`.`orden_pago_detalles` add index `idx_socio` (`socio_id`);
alter table `sigem_db`.`orden_pago_detalles` add index `idx_persona` (`id_persona`);
alter table `sigem_db`.`orden_pago_detalles` add index `idx_orden_pago` (`orden_pago_id`);
alter table `sigem_db`.`orden_pago_detalles` add index `idx_proveedor_factura` (`proveedor_factura_id`);
alter table `sigem_db`.`orden_pago_detalles` add index `idx_mutual_producto_solicitud` (`mutual_producto_solicitud_id`);
alter table `sigem_db`.`orden_pago_detalles` add index `idx_socio_reintegro` (`socio_reintegro_id`);
alter table `sigem_db`.`orden_pago_detalles` add index `idx_nro_solicitud` (`nro_solicitud`);

alter table `sigem_db`.`orden_pago_facturas` add index `idx_proveedor` (`proveedor_id`);
alter table `sigem_db`.`orden_pago_facturas` add index `idx_socio_id` (`socio_id`);
alter table `sigem_db`.`orden_pago_facturas` add index `idx_proveedor_factura` (`proveedor_factura_id`);
alter table `sigem_db`.`orden_pago_facturas` add index `idx_orden_pago` (`orden_pago_id`);
alter table `sigem_db`.`orden_pago_facturas` add index `idx_proveedor_credito` (`proveedor_credito_id`);
alter table `sigem_db`.`orden_pago_facturas` add index `idx_orden_pago_detalle` (`orden_pago_detalle_id`);

alter table `sigem_db`.`orden_pago_formas` add index `idx_proveedor` (`proveedor_id`);
alter table `sigem_db`.`orden_pago_formas` add index `idx_socio` (`socio_id`);
alter table `sigem_db`.`orden_pago_formas` add index `idx_orden_pago` (`orden_pago_id`);
alter table `sigem_db`.`orden_pago_formas` add index `idx_banco_cuenta_movimiento` (`banco_cuenta_movimiento_id`);

alter table `sigem_db`.`orden_pagos` add index `idx_proveedor` (`proveedor_id`);
alter table `sigem_db`.`orden_pagos` add index `idx_socio` (`socio_id`);
alter table `sigem_db`.`orden_pagos` add index `idx_id_persona` (`id_persona`);

alter table `sigem_db`.`proveedor_facturas` add index `idx_proveedor` (`proveedor_id`);
alter table `sigem_db`.`proveedor_facturas` add index `idx_ejercicio_plan_cuenta` (`ejercicio_id`, `co_plan_cuenta_id`);
alter table `sigem_db`.`proveedor_facturas` add index `idx_socio` (`socio_id`);
alter table `sigem_db`.`proveedor_facturas` add index `idx_liquidacion` (`liquidacion_id`);
alter table `sigem_db`.`proveedor_facturas` add index `idx_cancelacion_orden` (`cancelacion_orden_id`);
alter table `sigem_db`.`proveedor_facturas` add index `idx_orden_caja_cobro` (`orden_caja_cobro_id`);
alter table `sigem_db`.`proveedor_facturas` add index `idx_orden_descuento_cobro` (`orden_descuento_cobro_id`);

alter table `sigem_db`.`recibo_detalles` add index `idx_persona` (`persona_id`);
alter table `sigem_db`.`recibo_detalles` add index `idx_socio` (`socio_id`);
alter table `sigem_db`.`recibo_detalles` add index `idx_cliente` (`cliente_id`);
alter table `sigem_db`.`recibo_detalles` add index `idx_banco` (`banco_id`);
alter table `sigem_db`.`recibo_detalles` add index `idx_organismo` (`codigo_organismo`);
alter table `sigem_db`.`recibo_detalles` add index `idx_recibo` (`recibo_id`);
alter table `sigem_db`.`recibo_detalles` add index `idx_cliente_factura` (`cliente_factura_id`);
alter table `sigem_db`.`recibo_detalles` add index `idx_orden_descuento_cobro` (`orden_descuento_cobro_id`);

alter table `sigem_db`.`recibo_facturas` add index `idx_cliente` (`cliente_id`);
alter table `sigem_db`.`recibo_facturas` add index `idx_cliente_factura` (`cliente_factura_id`);
alter table `sigem_db`.`recibo_facturas` add index `idx_recibo` (`recibo_id`);
alter table `sigem_db`.`recibo_facturas` add index `idx_cliente_credito` (`cliente_credito_id`);
alter table `sigem_db`.`recibo_facturas` add index `idx_recibo_detalle` (`recibo_detalle_id`);

alter table `sigem_db`.`recibo_formas` add index `idx_proveedor` (`proveedor_id`);
alter table `sigem_db`.`recibo_formas` add index `idx_socio` (`socio_id`);
alter table `sigem_db`.`recibo_formas` add index `idx_persona` (`persona_id`);
alter table `sigem_db`.`recibo_formas` add index `idx_banco` (`banco_id`);
alter table `sigem_db`.`recibo_formas` add index `idx_organismo` (`codigo_organismo`);
alter table `sigem_db`.`recibo_formas` add index `idx_recibo` (`recibo_id`);
alter table `sigem_db`.`recibo_formas` add index `idx_banco_cuenta_movimiento` (`banco_cuenta_movimiento_id`);

alter table `sigem_db`.`recibos` add index `idx_persona` (`persona_id`);
alter table `sigem_db`.`recibos` add index `idx_socio` (`socio_id`);
alter table `sigem_db`.`recibos` add index `idx_cliente` (`cliente_id`);
alter table `sigem_db`.`recibos` add index `idx_banco` (`banco_id`);
alter table `sigem_db`.`recibos` add index `idx_organismo` (`codigo_organismo`);

alter table `sigem_db`.`orden_pago_detalles` drop key `idx_proveedor`, add index `idx_proveedor` (`proveedor_id`, `tipo_pago`);

alter table `sigem_db`.`proveedor_facturas` change `importe_gravado` `importe_gravado` decimal(15,2) default '0' NULL , change `importe_no_gravado` `importe_no_gravado` decimal(15,2) default '0' NULL , change `importe_iva` `importe_iva` decimal(15,2) default '0' NULL , change `percepcion` `percepcion` decimal(15,2) default '0' NULL , change `retencion` `retencion` decimal(15,2) default '0' NULL , change `impuesto_interno` `impuesto_interno` decimal(15,2) default '0' NULL , change `ingreso_bruto` `ingreso_bruto` decimal(15,2) default '0' NULL , change `otro_impuesto` `otro_impuesto` decimal(15,2) default '0' NULL , change `total_comprobante` `total_comprobante` decimal(15,2) default '0' NULL , change `importe_venc1` `importe_venc1` decimal(15,2) default '0' NULL , change `importe_venc2` `importe_venc2` decimal(15,2) default '0' NULL , change `importe_venc3` `importe_venc3` decimal(15,2) default '0' NULL , change `importe_venc4` `importe_venc4` decimal(15,2) default '0' NULL , change `importe_venc5` `importe_venc5` decimal(15,2) default '0' NULL , change `importe_venc6` `importe_venc6` decimal(15,2) default '0' NULL , change `importe_venc7` `importe_venc7` decimal(15,2) default '0' NULL , change `importe_venc8` `importe_venc8` decimal(15,2) default '0' NULL , change `importe_venc9` `importe_venc9` decimal(15,2) default '0' NULL , change `importe_venc10` `importe_venc10` decimal(15,2) default '0' NULL ;
