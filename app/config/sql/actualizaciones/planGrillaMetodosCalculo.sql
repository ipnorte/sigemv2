ALTER TABLE `proveedor_plan_grillas` 
ADD COLUMN `tipo_cuota_gasto_admin` VARCHAR(12) NULL,
ADD COLUMN `tipo_cuota_sellado` VARCHAR(12) NULL,
ADD COLUMN `gasto_admin_base_calculo` INT NULL,
ADD COLUMN `sellado_base_calculo` INT NULL,
ADD INDEX `fk_proveedor_plan_grillas_1_idx` (`tipo_cuota_gasto_admin` ASC),
ADD INDEX `fk_proveedor_plan_grillas_2_idx` (`tipo_cuota_sellado` ASC);

ALTER TABLE `proveedor_plan_grillas` 
ADD CONSTRAINT `fk_proveedor_plan_grillas_1`
  FOREIGN KEY (`tipo_cuota_gasto_admin`)
  REFERENCES `global_datos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_proveedor_plan_grillas_2`
  FOREIGN KEY (`tipo_cuota_sellado`)
  REFERENCES `global_datos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `proveedor_planes` 
ADD COLUMN `tna` DECIMAL(10,2) NULL DEFAULT 0,
ADD COLUMN `iva` DECIMAL(10,2) NULL DEFAULT 0,
ADD COLUMN `gasto_admin` DECIMAL(10,2) NULL DEFAULT 0,
ADD COLUMN `sellado` DECIMAL(10,2) NULL DEFAULT 0,
ADD COLUMN `metodo_calculo` INT NULL,
ADD COLUMN `tipo_cuota_gasto_admin` VARCHAR(12) NULL,
ADD COLUMN `tipo_cuota_sellado` VARCHAR(12) NULL,
ADD COLUMN `gasto_admin_base_calculo` INT NULL,
ADD COLUMN `sellado_base_calculo` INT NULL,
ADD COLUMN `interes_moratorio` DECIMAL(10,2) NULL DEFAULT 0,
ADD COLUMN `costo_cancelacion_anticipada` DECIMAL(10,2) NULL DEFAULT 0;

ALTER TABLE `proveedor_planes` 
ADD CONSTRAINT `fk_proveedor_planes_1`
  FOREIGN KEY (`tipo_cuota_gasto_admin`)
  REFERENCES `global_datos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_proveedor_planes_2`
  FOREIGN KEY (`tipo_cuota_sellado`)
  REFERENCES `global_datos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

  ALTER TABLE `proveedor_plan_grillas` CHANGE COLUMN `metodo_calculo` `metodo_calculo` INT NULL DEFAULT 0 ;
  update proveedor_plan_grillas set metodo_calculo = 0 where ifnull(tna,0) = 0;




ALTER TABLE `proveedor_plan_grilla_cuotas` ADD COLUMN `calculo` LONGTEXT NULL DEFAULT NULL ;
ALTER TABLE `mutual_producto_solicitudes` ADD COLUMN `detalle_calculo_plan` LONGTEXT NULL;
ALTER TABLE `proveedores` ADD COLUMN `direccion_pagare` VARCHAR(100) NULL;


ALTER TABLE `orden_descuentos` 
ADD COLUMN `mutual_producto_solicitud_id` INT NULL,
ADD INDEX `fk_orden_descuentos_2_idx` (`mutual_producto_solicitud_id` ASC);

ALTER TABLE `orden_descuentos` 
ADD CONSTRAINT `fk_orden_descuentos_2`
  FOREIGN KEY (`mutual_producto_solicitud_id`)
  REFERENCES `mutual_producto_solicitudes` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `orden_descuento_cuotas` 
    ADD COLUMN `capital` DECIMAL(10,2) NULL DEFAULT 0,
    ADD COLUMN `interes` DECIMAL(10,2) NULL DEFAULT 0,
    ADD COLUMN `iva` DECIMAL(10,2) NULL DEFAULT 0; 


ALTER TABLE `orden_descuento_cobro_cuotas` 
    ADD COLUMN `capital` DECIMAL(10,2) NULL DEFAULT 0,
    ADD COLUMN `interes` DECIMAL(10,2) NULL DEFAULT 0,
    ADD COLUMN `iva` DECIMAL(10,2) NULL DEFAULT 0; 

insert into global_datos(id,concepto_1,logico_2,entero_2)
values('MUTUTCUOGOTO','GTO.OTORGAMIENTO',1,10),('MUTUTCUOSELL','SELLADOS',1,10);


update orden_descuentos o, mutual_producto_solicitudes s 
set o.mutual_producto_solicitud_id = s.id 
where o.id = s.orden_descuento_id;

update orden_descuentos o, mutual_producto_solicitudes s 
set o.mutual_producto_solicitud_id = s.id 
where o.id = s.orden_descuento_seguro_id;



DROP FUNCTION IF EXISTS FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE;
DELIMITER $$
CREATE DEFINER=CURRENT_USER FUNCTION `FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE`(
vPROVEEDOR_ID INT(11)) RETURNS decimal(10,2)
BEGIN
DECLARE vSALDO DECIMAL(10,2);
DECLARE vLIQUIDA_PRESTAMO BOOLEAN;
SET vSALDO = 10000000;
SELECT liquida_prestamo into vLIQUIDA_PRESTAMO FROM proveedores WHERE id = vPROVEEDOR_ID;
IF vLIQUIDA_PRESTAMO = TRUE THEN 
SELECT	
		(
			SELECT IFNULL(SUM(c.importe_debitado * -1),0)
			FROM	liquidacion_cuotas c, liquidaciones l, global_datos AS g
			WHERE	c.liquidacion_id = l.id AND l.facturada = 0 AND c.proveedor_id = p.id AND l.codigo_organismo = g.id
		) +
		(
			SELECT	IFNULL(SUM(c.comision_cobranza),0)
			FROM	liquidacion_cuotas c, liquidaciones l, global_datos AS g
			WHERE	c.liquidacion_id = l.id AND l.facturada = 0 AND c.proveedor_id = p.id AND l.codigo_organismo = g.id
		) +
		(
			SELECT	IFNULL(SUM(ProveedorFactura.total_comprobante * IF(ProveedorFactura.tipo = 'SD' OR ProveedorFactura.tipo='FA',-1, 1)),0)
			FROM proveedor_facturas AS ProveedorFactura
			WHERE proveedor_id = p.id
		) +
		(
			SELECT	IFNULL(SUM(OrdenPago.importe),0)
			FROM	orden_pagos AS OrdenPago
			WHERE proveedor_id = p.id AND anulado = 0
		) +
		(
			SELECT	IFNULL(SUM(ClienteFactura.total_comprobante * IF(ClienteFactura.tipo = 'SD' OR ClienteFactura.tipo='FA' OR ClienteFactura.tipo = 'ND',1, -1)),0)
			FROM cliente_facturas AS ClienteFactura, proveedores AS Proveedor
			WHERE Proveedor.id = p.id AND ClienteFactura.cliente_id = Proveedor.cliente_id AND ClienteFactura.anulado = 0
		) +
		(
			SELECT	IFNULL(SUM(Recibo.importe * -1),0)
			FROM	recibos AS Recibo, proveedores AS Proveedor
			WHERE	Proveedor.id = p.id AND Recibo.cliente_id = Proveedor.cliente_id AND Recibo.anulado = 0 AND Recibo.cliente_id > 0
		) INTO vSALDO
		FROM	proveedores p 
		WHERE	p.id = vPROVEEDOR_ID;
        SET vSALDO = vSALDO * -1;
END IF;
RETURN vSALDO;
END$$
DELIMITER ;
