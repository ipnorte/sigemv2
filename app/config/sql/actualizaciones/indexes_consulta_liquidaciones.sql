/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 20/04/2018
 */

ALTER TABLE `liquidacion_socio_rendiciones` 
ADD INDEX `idx_10` (`liquidacion_id` ASC, `indica_pago` ASC);

ALTER TABLE `liquidaciones` 
ADD INDEX `idx_1` (`codigo_organismo` ASC, `en_proceso` ASC);

ALTER TABLE `liquidaciones` 
ADD INDEX `idx_organismo` (`codigo_organismo` ASC);