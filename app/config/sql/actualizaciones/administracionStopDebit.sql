/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 22/02/2020
 */

CREATE TABLE `liquidacion_socio_rendicion_stops` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `banco_id` CHAR(5) NOT NULL,
  `socio_id` INT NOT NULL,
  `liquidacion_id` INT NOT NULL,
  `nuevo_organismo` VARCHAR(12) NOT NULL,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_created` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_liquidacion_socio_rendicion_stops_3_idx` (`socio_id` ASC),
  INDEX `fk_liquidacion_socio_rendicion_stops_2_idx` (`banco_id` ASC),
  INDEX `fk_liquidacion_socio_rendicion_stops_1_idx` (`liquidacion_id` ASC),
  INDEX `fk_liquidacion_socio_rendicion_stops_4_idx` (`nuevo_organismo` ASC),
  CONSTRAINT `fk_liquidacion_socio_rendicion_stops_2`
    FOREIGN KEY (`banco_id`)
    REFERENCES `bancos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_liquidacion_socio_rendicion_stops_3`
    FOREIGN KEY (`socio_id`)
    REFERENCES `socios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_liquidacion_socio_rendicion_stops_1`
    FOREIGN KEY (`liquidacion_id`)
    REFERENCES `liquidaciones` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_liquidacion_socio_rendicion_stops_4`
    FOREIGN KEY (`nuevo_organismo`)
    REFERENCES `global_datos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);
