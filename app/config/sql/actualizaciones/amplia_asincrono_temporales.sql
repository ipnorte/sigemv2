/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 23/03/2019
 */

delete from asincrono_temporales;

ALTER TABLE asincrono_temporales ADD COLUMN decimal_16 DECIMAL(10,2) AFTER decimal_15;
ALTER TABLE asincrono_temporales ADD COLUMN decimal_17 DECIMAL(10,2) AFTER decimal_16;
ALTER TABLE asincrono_temporales ADD COLUMN decimal_18 DECIMAL(10,2) AFTER decimal_17;
ALTER TABLE asincrono_temporales ADD COLUMN decimal_19 DECIMAL(10,2) AFTER decimal_18;
ALTER TABLE asincrono_temporales ADD COLUMN decimal_20 DECIMAL(10,2) AFTER decimal_19;