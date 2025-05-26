/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 07/09/2017
 */

ALTER TABLE `bancos` 
ADD COLUMN `metodo_str_encode` VARCHAR(100) NULL AFTER `parametros_intercambio`,
ADD COLUMN `metodo_str_decode` VARCHAR(100) NULL AFTER `metodo_str_encode`;

ALTER TABLE `bancos` 
CHANGE COLUMN `metodo_str_encode` `metodo_str_encode` VARCHAR(100) NULL DEFAULT NULL ,
CHANGE COLUMN `metodo_str_decode` `metodo_str_decode` VARCHAR(100) NULL DEFAULT NULL ;