SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `proveedor_facturas` 
ADD CONSTRAINT `fk_proveedor_facturas_1`
  FOREIGN KEY (`proveedor_id`)
  REFERENCES `proveedores` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


select * from proveedor_facturas where proveedor_id not in (select id from proveedores);



ALTER TABLE `orden_pago_facturas` 
ADD CONSTRAINT `fk_orden_pago_facturas_1`
  FOREIGN KEY (`proveedor_factura_id`)
  REFERENCES `proveedor_facturas` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_orden_pago_facturas_2`
  FOREIGN KEY (`proveedor_id`)
  REFERENCES `proveedores` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

select * from orden_pago_facturas where proveedor_id not in (select id from proveedores);
select * from orden_pago_facturas where proveedor_factura_id not in (select id from proveedor_facturas);

ALTER TABLE `orden_pago_detalles` 
ADD CONSTRAINT `fk_orden_pago_detalles_1`
  FOREIGN KEY (`orden_pago_id`)
  REFERENCES `orden_pagos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `orden_pago_facturas` 
ADD CONSTRAINT `fk_orden_pago_facturas_1`
  FOREIGN KEY (`proveedor_factura_id`)
  REFERENCES `proveedor_facturas` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_orden_pago_facturas_2`
  FOREIGN KEY (`proveedor_id`)
  REFERENCES `proveedores` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_orden_pago_facturas_3`
  FOREIGN KEY (`orden_pago_id`)
  REFERENCES `orden_pagos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `orden_pago_formas` 
ADD CONSTRAINT `fk_orden_pago_formas_1`
  FOREIGN KEY (`orden_pago_id`)
  REFERENCES `orden_pagos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;