CREATE TABLE `proveedor_liquidaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `origen_recibo_id` int(11) DEFAULT '0',
  `liquidacion_id` int(11) DEFAULT '0',
  `proveedor_origen_fondo_id` int(11) DEFAULT '0',
  `proveedor_id` int(11) DEFAULT '0',
  `cliente_id` int(11) DEFAULT '0',
  `tipo_factura` varchar(2) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `periodo_cobro` varchar(6) DEFAULT NULL,
  `importe_proveedor` decimal(10,2) DEFAULT '0.00',
  `comision_cobranza` decimal(10,2) DEFAULT '0.00',
  `orden_pago_id` int(11) DEFAULT '0',
  `recibo_id` int(11) DEFAULT '0',
  `proveedor_factura_id` int(11) DEFAULT '0',
  `cliente_factura_id` int(11) DEFAULT '0',
  `cancelacion_orden_id` int(11) DEFAULT '0',
  `concepto` varchar(100) DEFAULT NULL,
  `user_created` varchar(50) DEFAULT NULL,
  `user_modified` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proveedor_id` (`proveedor_id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `proveedor_origen_fondo_id` (`proveedor_origen_fondo_id`),
  KEY `idx_orden_pago_id` (`orden_pago_id`),
  KEY `idx_recibo_id` (`recibo_id`),
  KEY `idx_proveedor_factura_id` (`proveedor_factura_id`),
  KEY `idx_cliente_factura_id` (`cliente_factura_id`),
  KEY `idx_cancelacion_orden_id` (`cancelacion_orden_id`),
  KEY `idx_origen_recibo_id` (`origen_recibo_id`),
  KEY `idx_liquidacion_id` (`liquidacion_id`),
  CONSTRAINT `proveedor_liquidaciones_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `proveedor_liquidaciones_ibfk_2` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `proveedor_liquidaciones_ibfk_3` FOREIGN KEY (`proveedor_origen_fondo_id`) REFERENCES `proveedores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;