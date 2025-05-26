/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  Lucho
 * Created: 04/11/2019
 */

ALTER TABLE `vendedores`
  ADD `supervisor_id` int(11) DEFAULT NULL
     AFTER `consultar_intranet`;

ALTER TABLE `vendedores`
  ADD `mail_contacto` varchar(255) DEFAULT NULL
     AFTER `supervisor_id`;


ALTER TABLE `vendedores`
  ADD CONSTRAINT `FK_SUPERVISOR` FOREIGN KEY (`supervisor_id`) REFERENCES `vendedores` (`id`);
COMMIT;