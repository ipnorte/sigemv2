/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 21/02/2017
 */


/*
ALTER TABLE `proveedores` ADD COLUMN `prioridad_imputa` INT(11) NOT NULL DEFAULT 5 AFTER `liquida_prestamo`;
UPDATE proveedores set prioridad_imputa = 1 where id = 18;
*/
alter table proveedores drop COLUMN prioridad_imputa;

drop table if exists proveedor_prioridad_imputa_organismos;
CREATE TABLE `proveedor_prioridad_imputa_organismos` (
  `proveedor_id` INT NOT NULL,
  `codigo_organismo` VARCHAR(12) NOT NULL,
  `prioridad` INT(11) NOT NULL,
  PRIMARY KEY (`proveedor_id`, `codigo_organismo`),
  INDEX `fk_proveedor_prioridad_imputa_organismos_2_idx` (`codigo_organismo` ASC),
  CONSTRAINT `fk_proveedor_prioridad_imputa_organismos_1`
    FOREIGN KEY (`proveedor_id`)
    REFERENCES `proveedores` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_proveedor_prioridad_imputa_organismos_2`
    FOREIGN KEY (`codigo_organismo`)
    REFERENCES `global_datos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


insert into proveedor_prioridad_imputa_organismos
select p.id,g.id,if(p.id = 18,1,5) from proveedores p, global_datos g
where g.id like 'MUTUCORG%' and g.id <> 'MUTUCORG'
order by p.id, g.id;




insert into permisos(id,descripcion,url,`order`,activo,parent)
values(450,'Proveedor Prioridad Imputacion','/proveedores/proveedores/prioridad_imputacion',450,1,400);
insert into grupos_permisos (grupo_id,permiso_id) values(1,450);

