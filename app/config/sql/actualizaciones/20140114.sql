/*
CREA RELACION NO MANDATORIA PARA LA NOVACION DE LAS ORDENES
*/
SELECT * FROM orden_descuentos WHERE id = 140630

-- CAMBIAR ATRIBUTOS DEL CAMPO
ALTER TABLE `orden_descuentos` CHANGE `nueva_orden_descuento_id` `nueva_orden_descuento_id` INT(11) NULL, CHANGE `anterior_orden_descuento_id` `anterior_orden_descuento_id` INT(11) NULL; 


UPDATE orden_descuentos SET nueva_orden_descuento_id = NULL WHERE nueva_orden_descuento_id = 0;
UPDATE orden_descuentos SET anterior_orden_descuento_id = NULL WHERE anterior_orden_descuento_id = 0;

SELECT * FROM orden_descuentos
WHERE nueva_orden_descuento_id IS NOT NULL
AND nueva_orden_descuento_id NOT IN (SELECT id FROM orden_descuentos);

SELECT * FROM orden_descuentos
WHERE anterior_orden_descuento_id IS NOT NULL
AND anterior_orden_descuento_id NOT IN (SELECT id FROM orden_descuentos);

UPDATE orden_descuentos SET anterior_orden_descuento_id = NULL
WHERE id = 141038;

-- CREAR FK
ALTER TABLE `orden_descuentos` ADD CONSTRAINT `FK_orden_novada_nueva_orden_id` FOREIGN KEY (`nueva_orden_descuento_id`) REFERENCES `sigem_db`.`orden_descuentos`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION, ADD CONSTRAINT `FK_orden_novada_anterior_orden_id` FOREIGN KEY (`anterior_orden_descuento_id`) REFERENCES `sigem_db`.`orden_descuentos`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION;

SELECT * FROM orden_descuentos WHERE anterior_orden_descuento_id = 0;


-- // CREO RELACION ENTRE LA LIQUIDACION CUOTAS Y LA ORDEN DE DESCUENTO
UPDATE liquidacion_cuotas SET orden_descuento_id = NULL WHERE orden_descuento_id = 0;
ALTER TABLE `liquidacion_cuotas` CHANGE `orden_descuento_id` `orden_descuento_id` INT(11) NULL, ADD CONSTRAINT `FK_liquidacion_cuotas_orddto_orden_id` FOREIGN KEY (`orden_descuento_id`) REFERENCES `orden_descuentos`(`id`);

-- // CREO RELACION ENTRE LA LIQUIDACION CUOTAS Y LA ORDEN DE DESCUENTO CUOTA
UPDATE liquidacion_cuotas SET orden_descuento_cuota_id = NULL WHERE orden_descuento_cuota_id = 0;
ALTER TABLE `liquidacion_cuotas` CHANGE `orden_descuento_cuota_id` `orden_descuento_cuota_id` INT(11) NULL, ADD CONSTRAINT `FK_liquidacion_cuotas_orddtocta_cuota_id` FOREIGN KEY (`orden_descuento_cuota_id`) REFERENCES `orden_descuento_cuotas`(`id`); 
 
 
-- // CREO RELACIONES TABLA ASINCRONOS
DELETE FROM asincrono_errores;
DELETE FROM asincrono_temporal_detalles;
DELETE FROM asincrono_temporales;
DELETE FROM asincronos;

ALTER TABLE asincrono_errores ADD CONSTRAINT FK_ASINCRONO_ERRORES_ASINCRONOS FOREIGN KEY(asincrono_id) REFERENCES asincronos(id);
ALTER TABLE asincrono_temporal_detalles ADD CONSTRAINT FK_ASINCRONO_TEMPDET_ASINCRONOS FOREIGN KEY(asincrono_id) REFERENCES asincronos(id);
ALTER TABLE asincrono_temporales ADD CONSTRAINT FK_ASINCRONO_TEMP_ASINCRONOS FOREIGN KEY(asincrono_id) REFERENCES asincronos(id);

-- // CREO PERMISOS PARA ACCESO A PAGINA DE DESCARGA DE BACKUPS DE DATOS
INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent,icon)
VALUES(10,'Adm.de Backups','/seguridad/backups',10,1,0,1,4,'arrow_right2.gif');
INSERT INTO grupos_permisos VALUES(1,10);

INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)
VALUES(11,'Download Backup','/seguridad/backups/download',11,0,0,1,4);
INSERT INTO grupos_permisos VALUES(1,11); 
 