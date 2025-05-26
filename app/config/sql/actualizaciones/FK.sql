/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 16/12/2019
 */

ALTER TABLE `orden_descuento_cobros` 
ADD CONSTRAINT `fk_orden_descuento_cobros_1`
  FOREIGN KEY (`socio_id`)
  REFERENCES `socios` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `orden_descuento_cuotas` 
ADD INDEX `fk_orden_descuento_cuotas_2_idx` (`tipo_cuota` ASC);
;
ALTER TABLE `orden_descuento_cuotas` 
ADD CONSTRAINT `fk_orden_descuento_cuotas_1`
  FOREIGN KEY (`tipo_producto`)
  REFERENCES `global_datos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_orden_descuento_cuotas_2`
  FOREIGN KEY (`tipo_cuota`)
  REFERENCES `global_datos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


ALTER TABLE `orden_descuento_cuotas` 
ADD CONSTRAINT `fk_orden_descuento_cuotas_3`
  FOREIGN KEY (`persona_beneficio_id`)
  REFERENCES `persona_beneficios` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `orden_descuentos` 
ADD CONSTRAINT `fk_orden_descuentos_1`
  FOREIGN KEY (`tipo_producto`)
  REFERENCES `global_datos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;