CREATE TABLE `liquidacion_novedades` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `liquidacion_id` INT(11) NOT NULL,
  `socio_id` INT(11) NULL,
  `fecha` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_created` VARCHAR(45) NOT NULL,
  `novedad` TEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_liquidacion_novedades_1_idx` (`liquidacion_id` ASC),
  INDEX `fk_liquidacion_novedades_2_idx` (`socio_id` ASC),
  CONSTRAINT `fk_liquidacion_novedades_1`
    FOREIGN KEY (`liquidacion_id`)
    REFERENCES `liquidaciones` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_liquidacion_novedades_2`
    FOREIGN KEY (`socio_id`)
    REFERENCES `socios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);