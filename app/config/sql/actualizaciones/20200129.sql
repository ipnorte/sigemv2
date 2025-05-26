/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 29/01/2020
 */

ALTER TABLE `bancos` 
CHANGE COLUMN `indicador_cabecera` `indicador_cabecera` VARCHAR(50) NULL DEFAULT NULL ,
CHANGE COLUMN `indicador_detalle` `indicador_detalle` VARCHAR(50) NULL DEFAULT NULL ,
CHANGE COLUMN `indicador_pie` `indicador_pie` VARCHAR(50) NULL DEFAULT NULL ;