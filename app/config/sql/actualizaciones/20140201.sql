UPDATE persona_beneficios SET codigo_empresa = NULL WHERE codigo_empresa = '';
UPDATE persona_beneficios SET codigo_empresa = CONCAT('MUTUEMPR',TRIM(codigo_empresa))
WHERE codigo_empresa NOT IN (SELECT id FROM global_datos);

UPDATE persona_beneficios SET turno_pago = NULL WHERE turno_pago = '';

UPDATE persona_beneficios SET turno_pago = NULL WHERE codigo_beneficio IN ('MUTUCORG7701','MUTUCORG6601');

UPDATE persona_beneficios SET turno_pago = NULL WHERE activo = 0 AND turno_pago NOT IN
(SELECT turno FROM liquidacion_turnos)
AND id IN (SELECT persona_beneficio_id FROM liquidacion_socios);


UPDATE persona_beneficios SET turno_pago = NULL WHERE turno_pago NOT IN
(SELECT turno FROM liquidacion_turnos)
AND id NOT IN (SELECT persona_beneficio_id FROM liquidacion_socios);

CREATE TABLE temporal.persona_beneficios
SELECT * FROM persona_beneficios WHERE turno_pago IS NOT NULL AND turno_pago NOT IN
(SELECT turno FROM liquidacion_turnos)
AND id IN (SELECT persona_beneficio_id FROM liquidacion_socios)

DELETE FROM temporal.persona_beneficios WHERE codigo_reparticion = '';

UPDATE temporal.persona_beneficios be, liquidacion_turnos t
SET be.turno_pago = t.turno
WHERE SUBSTR(be.codigo_reparticion,1,8) = TRIM(t.codigo_reparticion)
AND be.codigo_empresa = t.codigo_empresa;

UPDATE persona_beneficios be, temporal.persona_beneficios t
SET be.turno_pago = t.turno_pago
WHERE t.id = be.id AND t.turno_pago <> 'MUTUEMPR';

DROP TABLE temporal.persona_beneficios;

UPDATE persona_beneficios SET turno_pago = NULL WHERE turno_pago IS NOT NULL AND turno_pago NOT IN
(SELECT turno FROM liquidacion_turnos)
AND id IN (SELECT persona_beneficio_id FROM liquidacion_socios);

UPDATE persona_beneficios SET banco_id = NULL WHERE banco_id = '';

UPDATE persona_beneficios SET banco_id = NULL WHERE banco_id NOT IN (SELECT id FROM bancos);





 


ALTER TABLE `persona_beneficios` ADD CONSTRAINT `FK_persona_beneficio_codigo_beneficio_global_dato` FOREIGN KEY (`codigo_beneficio`) REFERENCES `global_datos`(`id`); 
ALTER TABLE `persona_beneficios` ADD CONSTRAINT `FK_persona_beneficio_empresa_global_dato` FOREIGN KEY (`codigo_empresa`) REFERENCES `global_datos`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION; 
-- guarda
ALTER TABLE `persona_beneficios` ADD CONSTRAINT `FK_persona_beneficio_turno_pago_liquidacion_turnos` FOREIGN KEY (`turno_pago`) REFERENCES `liquidacion_turnos`(`turno`) ON UPDATE NO ACTION ON DELETE NO ACTION; 
ALTER TABLE `persona_beneficios` ADD CONSTRAINT `FK_persona_beneficio_banco_id` FOREIGN KEY (`banco_id`) REFERENCES `bancos`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION; 

