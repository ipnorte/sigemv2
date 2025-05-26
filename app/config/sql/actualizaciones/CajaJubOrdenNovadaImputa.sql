SELECT * FROM orden_descuentos WHERE id = 152835

ALTER TABLE `sigem_db`.`orden_descuentos` CHANGE `nueva_orden_descuento_id` `nueva_orden_descuento_id` INT(11) NULL, CHANGE `anterior_orden_descuento_id` `anterior_orden_descuento_id` INT(11) NULL;


UPDATE orden_descuentos SET nueva_orden_descuento_id = NULL WHERE nueva_orden_descuento_id = 0;
UPDATE orden_descuentos SET anterior_orden_descuento_id = NULL WHERE anterior_orden_descuento_id = 0;

SELECT * FROM orden_descuentos
WHERE nueva_orden_descuento_id IS NOT NULL
AND nueva_orden_descuento_id NOT IN (SELECT id FROM orden_descuentos);

SELECT * FROM orden_descuentos
WHERE anterior_orden_descuento_id IS NOT NULL
AND anterior_orden_descuento_id NOT IN (SELECT id FROM orden_descuentos);

UPDATE orden_descuentos SET anterior_orden_descuento_id = NULL
WHERE id = 141038;

ALTER TABLE `sigem_db`.`orden_descuentos` ADD CONSTRAINT `FK_orden_novada_nueva_orden_id` FOREIGN KEY (`nueva_orden_descuento_id`) REFERENCES `sigem_db`.`orden_descuentos`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION, ADD CONSTRAINT `FK_orden_novada_anterior_orden_id` FOREIGN KEY (`anterior_orden_descuento_id`) REFERENCES `sigem_db`.`orden_descuentos`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION;

-- /////////////////////////

SELECT OrdenDescuento.nueva_orden_descuento_id FROM liquidacion_socio_rendiciones LiquidacionSocioRendicion
INNER JOIN orden_descuentos OrdenDescuento ON (OrdenDescuento.id = LiquidacionSocioRendicion.orden_descuento_id)
WHERE 
LiquidacionSocioRendicion.liquidacion_id = 310
AND LiquidacionSocioRendicion.socio_id = 11243
AND LiquidacionSocioRendicion.orden_descuento_id = 152835

