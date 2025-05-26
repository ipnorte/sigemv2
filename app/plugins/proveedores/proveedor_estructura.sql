Gustavo Luján
11/01/2021
Modificacion campo punto_venta_comprobante de la tabla proveedor_facturas de 4 a 5 caracteres.

ALTER TABLE `proveedor_facturas` CHANGE `punto_venta_comprobante` `punto_venta_comprobante` VARCHAR(5) CHARSET utf8 COLLATE utf8_general_ci NULL; 
UPDATE `proveedor_facturas` SET `punto_venta_comprobante` = RIGHT(CONCAT('00000', `punto_venta_comprobante`),5)
