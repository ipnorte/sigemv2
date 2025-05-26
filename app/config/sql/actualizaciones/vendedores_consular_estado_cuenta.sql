/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 30/03/2019
 */

ALTER TABLE `vendedores` 
ADD COLUMN `consultar_deuda` TINYINT NOT NULL DEFAULT 0 AFTER `modified`,
ADD COLUMN `consultar_intranet` TINYINT NOT NULL DEFAULT 0 AFTER `consultar_deuda`;