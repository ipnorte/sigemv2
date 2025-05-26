/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 14/11/2018
 */

ALTER TABLE `cliente_factura_detalles` 
ADD CONSTRAINT `fk_cliente_factura_detalles_1`
  FOREIGN KEY (`cliente_factura_id`)
  REFERENCES `cliente_facturas` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


update cliente_facturas set cliente_id = NULL
where cliente_id not in (select id from clientes);


ALTER TABLE `cliente_facturas` 
ADD CONSTRAINT `fk_cliente_facturas_1`
  FOREIGN KEY (`cliente_id`)
  REFERENCES `clientes` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


update cliente_factura_detalles
set co_plan_cuenta_id = NULL
where co_plan_cuenta_id not in (select id from co_plan_cuentas);



ALTER TABLE `cliente_factura_detalles` 
ADD INDEX `fk_cliente_factura_detalles_2_idx` (`co_plan_cuenta_id` ASC);
;
ALTER TABLE `cliente_factura_detalles` 
ADD CONSTRAINT `fk_cliente_factura_detalles_2`
  FOREIGN KEY (`co_plan_cuenta_id`)
  REFERENCES `co_plan_cuentas` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


update cliente_facturas
set ejercicio_id = NULL
where ejercicio_id = 0;

update cliente_facturas
set co_plan_cuenta_id = NULL
where co_plan_cuenta_id = 0;

ALTER TABLE `cliente_facturas` 
DROP FOREIGN KEY `fk_cliente_facturas_1`;
ALTER TABLE `cliente_facturas` 
CHANGE COLUMN `cliente_id` `cliente_id` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `ejercicio_id` `ejercicio_id` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `co_plan_cuenta_id` `co_plan_cuenta_id` INT(11) NULL DEFAULT NULL ;
ALTER TABLE `cliente_facturas` 
ADD CONSTRAINT `fk_cliente_facturas_1`
  FOREIGN KEY (`cliente_id`)
  REFERENCES `clientes` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


UPDATE cliente_ctactes
set cliente_id = NULL
WHERE cliente_id not in (select id from clientes);

ALTER TABLE `cliente_ctactes` 
CHANGE COLUMN `cliente_id` `cliente_id` INT(11) NULL DEFAULT NULL ,
ADD INDEX `fk_cliente_ctactes_1_idx` (`cliente_id` ASC);
;
ALTER TABLE `cliente_ctactes` 
ADD CONSTRAINT `fk_cliente_ctactes_1`
  FOREIGN KEY (`cliente_id`)
  REFERENCES `clientes` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


ALTER TABLE `cliente_tipo_asiento_renglones` 
CHANGE COLUMN `cliente_tipo_asiento_id` `cliente_tipo_asiento_id` INT(11) NOT NULL ,
CHANGE COLUMN `co_plan_cuenta_id` `co_plan_cuenta_id` INT(11) NOT NULL ,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`id`, `cliente_tipo_asiento_id`, `co_plan_cuenta_id`),
ADD INDEX `fk_cliente_tipo_asiento_renglones_1_idx` (`cliente_tipo_asiento_id` ASC),
ADD INDEX `fk_cliente_tipo_asiento_renglones_2_idx` (`co_plan_cuenta_id` ASC);
;
ALTER TABLE `cliente_tipo_asiento_renglones` 
ADD CONSTRAINT `fk_cliente_tipo_asiento_renglones_1`
  FOREIGN KEY (`cliente_tipo_asiento_id`)
  REFERENCES `cliente_tipo_asientos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_cliente_tipo_asiento_renglones_2`
  FOREIGN KEY (`co_plan_cuenta_id`)
  REFERENCES `co_plan_cuentas` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

-- ///////////////////////////////////
update recibos set persona_id = NULL where persona_id = 0;
update recibos set socio_id = NULL where socio_id = 0;
update recibos set cliente_id = NULL where cliente_id = 0;
update recibos set banco_id = NULL where banco_id = 0;
update recibos set co_plan_cuenta_id = NULL where co_plan_cuenta_id = 0;

ALTER TABLE `recibos` 
CHANGE COLUMN `persona_id` `persona_id` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `socio_id` `socio_id` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `cliente_id` `cliente_id` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `co_plan_cuenta_id` `co_plan_cuenta_id` INT(11) NULL DEFAULT NULL ;


ALTER TABLE `recibos` 
ADD INDEX `fk_recibos_5_idx` (`co_plan_cuenta_id` ASC);
;
ALTER TABLE `recibos` 
ADD CONSTRAINT `fk_recibos_1`
  FOREIGN KEY (`persona_id`)
  REFERENCES `personas` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_recibos_2`
  FOREIGN KEY (`socio_id`)
  REFERENCES `socios` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_recibos_3`
  FOREIGN KEY (`cliente_id`)
  REFERENCES `clientes` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_recibos_4`
  FOREIGN KEY (`banco_id`)
  REFERENCES `bancos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_recibos_5`
  FOREIGN KEY (`co_plan_cuenta_id`)
  REFERENCES `co_plan_cuentas` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


update recibo_detalles set persona_id = NULL where persona_id = 0;
update recibo_detalles set socio_id = NULL where socio_id = 0;
update recibo_detalles set cliente_id = NULL where cliente_id = 0;
update recibo_detalles set banco_id = NULL where banco_id = 0;
update recibo_detalles set recibo_id = NULL where recibo_id = 0;
update recibo_detalles set cliente_factura_id = NULL where cliente_factura_id = 0;
update recibo_detalles set recibo_detalle_id = NULL where recibo_detalle_id = 0;
update recibo_detalles set orden_descuento_cobro_id = NULL where orden_descuento_cobro_id = 0;
update recibo_detalles set orden_descuento_cuota_id = NULL where orden_descuento_cuota_id = 0;
update recibo_detalles set orden_descuento_id = NULL where orden_descuento_id = 0;
update recibo_detalles set orden_descuento_cobro_cuota_id = NULL where orden_descuento_cobro_cuota_id = 0;
update recibo_detalles set socio_reintegro_id = NULL where socio_reintegro_id = 0;
update recibo_detalles set co_plan_cuenta_id = NULL where co_plan_cuenta_id = 0;

ALTER TABLE `recibo_detalles` 
CHANGE COLUMN `persona_id` `persona_id` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `socio_id` `socio_id` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `cliente_id` `cliente_id` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `banco_id` `banco_id` CHAR(5) NULL DEFAULT NULL ,
CHANGE COLUMN `recibo_id` `recibo_id` INT(11) NOT NULL ,
CHANGE COLUMN `cliente_factura_id` `cliente_factura_id` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `recibo_detalle_id` `recibo_detalle_id` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `orden_descuento_cobro_id` `orden_descuento_cobro_id` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `orden_descuento_cuota_id` `orden_descuento_cuota_id` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `orden_descuento_id` `orden_descuento_id` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `orden_descuento_cobro_cuota_id` `orden_descuento_cobro_cuota_id` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `socio_reintegro_id` `socio_reintegro_id` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `co_plan_cuenta_id` `co_plan_cuenta_id` INT(11) NULL DEFAULT NULL ,
ADD INDEX `fk_recibo_detalles_3_idx` (`co_plan_cuenta_id` ASC),
ADD INDEX `fk_recibo_detalles_9_idx` (`orden_descuento_cuota_id` ASC),
ADD INDEX `fk_recibo_detalles_10_idx` (`orden_descuento_id` ASC),
ADD INDEX `fk_recibo_detalles_11_idx` (`orden_descuento_cobro_cuota_id` ASC),
ADD INDEX `fk_recibo_detalles_12_idx` (`socio_reintegro_id` ASC);
;
ALTER TABLE `recibo_detalles` 
ADD CONSTRAINT `fk_recibo_detalles_1`
  FOREIGN KEY (`recibo_id`)
  REFERENCES `recibos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_recibo_detalles_2`
  FOREIGN KEY (`cliente_id`)
  REFERENCES `clientes` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_recibo_detalles_3`
  FOREIGN KEY (`co_plan_cuenta_id`)
  REFERENCES `co_plan_cuentas` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_recibo_detalles_4`
  FOREIGN KEY (`banco_id`)
  REFERENCES `bancos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_recibo_detalles_5`
  FOREIGN KEY (`persona_id`)
  REFERENCES `personas` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_recibo_detalles_6`
  FOREIGN KEY (`cliente_id`)
  REFERENCES `socios` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_recibo_detalles_7`
  FOREIGN KEY (`cliente_factura_id`)
  REFERENCES `cliente_facturas` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_recibo_detalles_8`
  FOREIGN KEY (`orden_descuento_cobro_id`)
  REFERENCES `orden_descuento_cobros` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_recibo_detalles_9`
  FOREIGN KEY (`orden_descuento_cuota_id`)
  REFERENCES `orden_descuento_cuotas` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_recibo_detalles_10`
  FOREIGN KEY (`orden_descuento_id`)
  REFERENCES `orden_descuentos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_recibo_detalles_11`
  FOREIGN KEY (`orden_descuento_cobro_cuota_id`)
  REFERENCES `orden_descuento_cobro_cuotas` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_recibo_detalles_12`
  FOREIGN KEY (`socio_reintegro_id`)
  REFERENCES `socio_reintegros` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

