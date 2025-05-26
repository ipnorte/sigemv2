/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 25/09/2018
 */

alter table asincrono_temporales add column decimal_16 decimal(10,2) default 0 after decimal_15;
alter table asincrono_temporales add column decimal_17 decimal(10,2) default 0 after decimal_16;
alter table asincrono_temporales add column decimal_18 decimal(10,2) default 0 after decimal_17;
alter table asincrono_temporales add column decimal_19 decimal(10,2) default 0 after decimal_18;
alter table asincrono_temporales add column decimal_20 decimal(10,2) default 0 after decimal_19;