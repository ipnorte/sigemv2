/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 17/05/2019
 */

CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_socio_solicitudes` AS select `SocioSolicitud`.`id` AS `socio_solicitud_id`,`SocioSolicitud`.`fecha` AS `socio_solicitud_fecha`,`SocioSolicitud`.`tipo_solicitud` AS `socio_solicitud_tipo`,`SocioSolicitud`.`user_created` AS `socio_solicitud_usuario`,`PersonaVendedor`.`id` AS `vendedor_id`,`PersonaVendedor`.`cuit_cuil` AS `vendedor_cuit_cuil`,concat(`PersonaVendedor`.`apellido`,' ',`PersonaVendedor`.`nombre`) AS `vendedor`,`Persona`.`documento` AS `documento`,concat(`Persona`.`apellido`,', ',`Persona`.`nombre`) AS `socio_apenom`,`Socio`.`id` AS `socio_id` from ((((`socio_solicitudes` `SocioSolicitud` join `personas` `Persona` on((`Persona`.`id` = `SocioSolicitud`.`persona_id`))) left join `vendedores` `Vendedor` on((`Vendedor`.`id` = `SocioSolicitud`.`vendedor_id`))) left join `personas` `PersonaVendedor` on((`PersonaVendedor`.`id` = `Vendedor`.`persona_id`))) join `socios` `Socio` on((`Socio`.`socio_solicitud_id` = `SocioSolicitud`.`id`))) order by `PersonaVendedor`.`apellido`,`PersonaVendedor`.`nombre`,`Persona`.`apellido`,`Persona`.`nombre`,`SocioSolicitud`.`fecha`,`SocioSolicitud`.`id`;
