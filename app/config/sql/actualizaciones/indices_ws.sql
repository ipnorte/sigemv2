/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 23/11/2018
 */

ALTER TABLE `mutual_producto_solicitudes` 
ADD INDEX `idx_webservices` (`aprobada` ASC, `anulada` ASC, `fecha` ASC, `estado` ASC, `proveedor_id` ASC, `reasignar_proveedor_id` ASC);
;