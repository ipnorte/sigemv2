/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 15/05/2019
 */

DELIMITER $$
CREATE DEFINER=`root`@`%` FUNCTION `FX_SOLICITUD_GET_ORDENDTO`(vSOLICITUD_ID INT(11)) RETURNS int(11)
BEGIN
DECLARE vORDEN_ID INT(11) default 0;
DECLARE vNUEVA_ORDEN_ID INT(11) default 0;

DECLARE vNOVACION boolean default FALSE;

select ifnull(orden_descuento_id,0) into vORDEN_ID
from mutual_producto_solicitudes
where id = vSOLICITUD_ID;

select ifnull(nueva_orden_descuento_id,0) into vNUEVA_ORDEN_ID from orden_descuentos where id = vORDEN_ID;

while vNOVACION = FALSE do

    if vNUEVA_ORDEN_ID <> 0 then
    
        select id,ifnull(nueva_orden_descuento_id,0) into vORDEN_ID,vNUEVA_ORDEN_ID 
        from orden_descuentos where id = vNUEVA_ORDEN_ID;
    
    else
        set vNOVACION = TRUE;
        
    end if;
    
    


end while;

RETURN vORDEN_ID;
END$$
DELIMITER ;



CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_solicitudes` AS select `MutualProductoSolicitud`.`id` AS `numero_solicitud`,`MutualProductoSolicitud`.`permanente` AS `permanente`,`MutualProductoSolicitud`.`estado` AS `estado`,`Estado`.`concepto_1` AS `estado_descripcion`,`MutualProductoSolicitud`.`fecha` AS `fecha_emision`,`MutualProductoSolicitud`.`fecha_pago` AS `fecha_pago`,`MutualProductoSolicitud`.`aprobada` AS `aprobada`,`MutualProductoSolicitud`.`tipo_orden_dto` AS `tipo_orden_dto`,`MutualProductoSolicitud`.`tipo_producto` AS `tipo_producto`,`Producto`.`concepto_1` AS `tipo_producto_concepto`,`MutualProductoSolicitud`.`proveedor_id` AS `proveedor_id`,`ProveedorOrigen`.`razon_social` AS `proveedor_origen_razon_social`,`MutualProductoSolicitud`.`reasignar_proveedor_id` AS `proveedor_reasigna_id`,`ProveedorReasigna`.`razon_social` AS `proveedor_reasigna_razon_social`,`MutualProductoSolicitud`.`reasignar_proveedor_fecha` AS `reasignar_proveedor_fecha`,`MutualProductoSolicitud`.`reasignar_proveedor_usuario` AS `reasignar_proveedor_usuario`,concat(`PersonaVendedor`.`apellido`,' ',`PersonaVendedor`.`nombre`) AS `vendedor`,`MutualProductoSolicitud`.`user_created` AS `usuario`,`MutualProductoSolicitud`.`importe_solicitado` AS `importe_solicitado`,`MutualProductoSolicitud`.`importe_percibido` AS `importe_percibido`,`MutualProductoSolicitud`.`cuotas` AS `cuotas`,`MutualProductoSolicitud`.`importe_cuota` AS `importe_cuota`,`MutualProductoSolicitud`.`importe_total` AS `importe_total`,`MutualProductoSolicitud`.`socio_id` AS `socio_id`,`MutualProductoSolicitud`.`persona_id` AS `persona_id`,`Persona`.`documento` AS `documento`,concat(`Persona`.`apellido`,', ',`Persona`.`nombre`) AS `solicitante`,`PersonaBeneficio`.`codigo_beneficio` AS `codigo_organismo`,`Organismo`.`concepto_1` AS `codigo_organismo_concepto`,`PersonaBeneficio`.`codigo_empresa` AS `codigo_empresa`,`Empresa`.`concepto_1` AS `codigo_empresa_concepto`,`FX_SOLICITUD_GET_ORDENDTO`(`MutualProductoSolicitud`.`id`) AS `orden_descuento_id` from (((((((((((`mutual_producto_solicitudes` `MutualProductoSolicitud` join `personas` `Persona` on((`Persona`.`id` = `MutualProductoSolicitud`.`persona_id`))) join `persona_beneficios` `PersonaBeneficio` on((`PersonaBeneficio`.`id` = `MutualProductoSolicitud`.`persona_beneficio_id`))) join `global_datos` `Organismo` on((`Organismo`.`id` = `PersonaBeneficio`.`codigo_beneficio`))) join `global_datos` `Empresa` on((`Empresa`.`id` = `PersonaBeneficio`.`codigo_empresa`))) left join `socios` `Socio` on((`Socio`.`id` = `MutualProductoSolicitud`.`socio_id`))) join `global_datos` `Producto` on((`Producto`.`id` = `MutualProductoSolicitud`.`tipo_producto`))) join `global_datos` `Estado` on((`Estado`.`id` = `MutualProductoSolicitud`.`estado`))) join `proveedores` `ProveedorOrigen` on((`ProveedorOrigen`.`id` = `MutualProductoSolicitud`.`proveedor_id`))) left join `proveedores` `ProveedorReasigna` on((`ProveedorReasigna`.`id` = `MutualProductoSolicitud`.`reasignar_proveedor_id`))) left join `vendedores` `Vendedor` on((`Vendedor`.`id` = `MutualProductoSolicitud`.`vendedor_id`))) left join `personas` `PersonaVendedor` on((`PersonaVendedor`.`id` = `Vendedor`.`persona_id`))) where (`MutualProductoSolicitud`.`anulada` = 0);



select 
MutualProductoSolicitud.id as numero_solicitud
,MutualProductoSolicitud.estado
,Estado.concepto_1 as estado_descripcion
,MutualProductoSolicitud.fecha as fecha_emision
,MutualProductoSolicitud.fecha_pago
,MutualProductoSolicitud.aprobada
,MutualProductoSolicitud.tipo_orden_dto
,MutualProductoSolicitud.tipo_producto
,Producto.concepto_1 as tipo_producto_concepto
,MutualProductoSolicitud.proveedor_id
,ProveedorOrigen.razon_social as proveedor_origen_razon_social
,MutualProductoSolicitud.reasignar_proveedor_id as proveedor_reasigna_id
,ProveedorReasigna.razon_social as proveedor_reasigna_razon_social
, concat(PersonaVendedor.apellido,' ',PersonaVendedor.nombre) as vendedor
from 
mutual_producto_solicitudes MutualProductoSolicitud
INNER JOIN personas AS Persona ON (Persona.id = MutualProductoSolicitud.persona_id)
INNER JOIN global_datos as Producto ON Producto.id = MutualProductoSolicitud.tipo_producto
INNER JOIN global_datos as Estado ON Estado.id = MutualProductoSolicitud.estado
INNER JOIN proveedores as ProveedorOrigen on ProveedorOrigen.id =  MutualProductoSolicitud.proveedor_id
LEFT JOIN proveedores as ProveedorReasigna on ProveedorReasigna.id =  MutualProductoSolicitud.reasignar_proveedor_id
LEFT JOIN vendedores AS Vendedor ON (Vendedor.id = MutualProductoSolicitud.vendedor_id)
LEFT JOIN personas AS PersonaVendedor ON (PersonaVendedor.id = Vendedor.persona_id)
WHERE
MutualProductoSolicitud.anulada = 0
ORDER BY 
PersonaVendedor.apellido,PersonaVendedor.nombre,		
Persona.apellido,Persona.nombre,MutualProductoSolicitud.fecha,MutualProductoSolicitud.id;