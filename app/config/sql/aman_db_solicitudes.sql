alter table `aman_db`.`solicitudes` add column `recibo_id` int(11) DEFAULT '0' NULL after `reasigna_proveedor_user`, add column `orden_pago_id` int(11) DEFAULT '0' NULL after `recibo_id`;
alter table `sigem_db`.`orden_pagos` add column `id_persona` int(11) DEFAULT '0' NULL after `socio_id`;
alter table `sigem_db`.`orden_pago_detalles` add column `id_persona` int(11) DEFAULT '0' NULL after `socio_id`;
alter table `sigem_db`.`orden_pago_detalles` add column `nro_solicitud` int(11) DEFAULT '0' NULL after `socio_reintegro_id`;
alter table `aman_db`.`solicitudes` add column `en_mano_100` decimal(10,2) DEFAULT '0' NULL after `en_mano`;
alter table `sigem_db`.`recibos` add column `nro_solicitud` int(11) DEFAULT '0' NULL after `codigo_organismo`, add column `aporte_socio` decimal(15,2) DEFAULT '0' NULL after `importe`,change `co_plan_cuenta_id` `co_plan_cuenta_id` int(11) default '0' NULL ;
