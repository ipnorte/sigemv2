insert into global_datos(id,concepto_1)
value('MUTUSOIN','EMPRESAS INFORMES CREDITICIOS'),('MUTUSOIN0001','VERAZ');

insert into permisos(id,descripcion,url,`order`,activo,parent)
values(112,'Alta Informe Deuda','/pfyj/socios/alta_informe',112,1,100);
insert into grupos_permisos (grupo_id,permiso_id) values(1,112);


insert into permisos(id,descripcion,url,`order`,main,icon,activo,parent)
values(245,'Informe Comercial','/mutual/informe_comerciales',245,1,'arrow_right2.gif',1,200);

insert into grupos_permisos (grupo_id,permiso_id) values(1,245);

insert into permisos(id,descripcion,url,`order`,activo,parent)
values(246,'Generar Informe','/mutual/informe_comerciales/generar_informe',246,1,200);
insert into grupos_permisos (grupo_id,permiso_id) values(1,246);

insert into permisos(id,descripcion,url,`order`,activo,parent)
values(247,'Descargar Informe XLS','/mutual/informe_comerciales/download_lote_xls',247,1,200);
insert into grupos_permisos (grupo_id,permiso_id) values(1,247);

insert into permisos(id,descripcion,url,`order`,activo,parent)
values(248,'Borrar Lote','/mutual/informe_comerciales/del',248,1,200);
insert into grupos_permisos (grupo_id,permiso_id) values(1,248);


-- #########################################################################################
-- OJO: REEMPLAZAR EL NOMBRE DE LA DB PARA EJECUTARLO EN OTRA BASE QUE NO SEA LA SIGEM_DB
-- #########################################################################################

CREATE TABLE IF NOT EXISTS `socio_informe_lotes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `empresa` VARCHAR(12) NOT NULL,
  `fecha` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lote` BLOB NOT NULL,
  `created` DATETIME NULL,
  `modified` DATETIME NULL,
  `user_created` VARCHAR(45) NULL,
  `user_modified` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_socio_informe_lotes_global_datos1_idx` (`empresa` ASC),
  CONSTRAINT `fk_socio_informe_lotes_global_datos1`
    FOREIGN KEY (`empresa`)
    REFERENCES `sigem_db`.`global_datos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `socio_informes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `socio_id` INT(11) NOT NULL,
  `empresa` VARCHAR(12) NOT NULL,
  `tipo_novedad` VARCHAR(1) NOT NULL DEFAULT 'A',
  `fecha_calculo_deuda` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `periodo_hasta` VARCHAR(6) NULL,
  `deuda_informada` DECIMAL(10,2) NOT NULL,
  `socio_informe_lote_id` INT NULL,
  `created` DATETIME NULL,
  `modified` DATETIME NULL,
  `user_created` VARCHAR(45) NULL,
  `user_modified` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_socio_informes_socios1_idx` (`socio_id` ASC),
  INDEX `fk_socio_informes_global_datos1_idx` (`empresa` ASC),
  INDEX `fk_socio_informes_socio_informe_lotes1_idx` (`socio_informe_lote_id` ASC),
  CONSTRAINT `fk_socio_informes_socios1`
    FOREIGN KEY (`socio_id`)
    REFERENCES `socios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_socio_informes_global_datos1`
    FOREIGN KEY (`empresa`)
    REFERENCES `global_datos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_socio_informes_socio_informe_lotes1`
    FOREIGN KEY (`socio_informe_lote_id`)
    REFERENCES `socio_informe_lotes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `socio_informes_cuotas` (
  `orden_descuento_cuota_id` INT(11) NOT NULL,
  `socio_informe_id` INT NOT NULL,
  `saldo_informado` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`orden_descuento_cuota_id`, `socio_informe_id`),
  INDEX `fk_socio_informes_cuotas_socio_informes1_idx` (`socio_informe_id` ASC),
  CONSTRAINT `fk_socio_informes_cuotas_orden_descuento_cuotas1`
    FOREIGN KEY (`orden_descuento_cuota_id`)
    REFERENCES `sigem_db`.`orden_descuento_cuotas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_socio_informes_cuotas_socio_informes1`
    FOREIGN KEY (`socio_informe_id`)
    REFERENCES `socio_informes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


ALTER TABLE `socio_informes` 
ADD UNIQUE INDEX `idu_socio_periodo` (`socio_id` ASC, `periodo_hasta` ASC, `empresa` ASC);

ALTER TABLE `socio_informe_lotes` 
ADD COLUMN `periodo_hasta` VARCHAR(6) NOT NULL AFTER `fecha`;