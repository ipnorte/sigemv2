/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 24/02/2019
 */

ALTER TABLE `mutual_producto_solicitudes` 
ADD CONSTRAINT `fk_mutual_producto_solicitudes_personas`
  FOREIGN KEY (`persona_id`)
  REFERENCES `personas` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;