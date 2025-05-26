/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 24/10/2017
 */

ALTER TABLE `cancelacion_ordenes` 
ADD INDEX `idx_cancelacion_orden_estado` (`estado` ASC);

ALTER TABLE `cancelacion_orden_cuotas` 
ADD INDEX `idx_cancelacion_cuotas_orden_dto_cuota` (`orden_descuento_cuota_id` ASC);

ALTER TABLE `liquidacion_cuotas` 
ADD INDEX `idx_liquidacion_cuotas_cobro_id` (`orden_descuento_cobro_id` ASC);


ALTER TABLE `proveedores` 
ADD INDEX `idx_1` (`id` ASC, `genera_cuota_social` ASC, `reasignable` ASC, `liquida_prestamo` ASC, `vendedores` ASC, `activo` ASC);


ALTER TABLE `liquidacion_socios` 
ADD INDEX `index18` (`liquidacion_id` ASC, `diskette` ASC, `codigo_empresa` ASC, `turno_pago` ASC);

ALTER TABLE `liquidacion_socio_rendiciones` ADD INDEX `index11` (`socio_id` ASC, `orden_descuento_cobro_id` ASC);

