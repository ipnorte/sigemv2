alter table `aman2_db`.`liquidaciones` add column `recibo_id` int(11) NULL after `fecha_imputacion`;
alter table `aman2_db`.`proveedores` add column `cliente_id` int(11) NULL after `codigo_acceso_ws`;

