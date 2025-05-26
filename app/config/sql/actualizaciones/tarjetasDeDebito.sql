alter table persona_beneficios add column tarjeta_numero char(20);
alter table persona_beneficios add column tarjeta_titular char(30);
alter table persona_beneficios add column tarjeta_debito TEXT;

-- alta menu para vista de datos de la tarjeta
INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,activo,parent)
VALUES(155,'Info Tarjeta de Debito','/pfyj/persona_beneficios/tarjeta',155,0,0,1,100);
INSERT INTO grupos_permisos VALUES(1,155);

-- habilito banco zenrise
INSERT INTO `bancos` (`id`, `nombre`, `activo`, `intercambio`, `tipo_registro`, `longitud`, `longitud_salida`) 
VALUES ('99998', 'ZENRISE', '1', '1', '1', '107', '150');


-- INSTALAR LOS ORGANISMOS
insert into global_datos(id,concepto_1,logico_1,logico_2,entero_1,entero_2,decimal_1,decimal_2)
value('MUTUCORG2250','T.DEBITO * ACTIVOS CBU *',1,1,10000,10000,10000,10000),
('MUTUCORG2251','T.DEBITO * ANSES CBU *',1,1,10000,10000,10000,10000);

-- OJO!: fijarse que letra se pone para armar las empresas/turnos

select distinct substr(id,1,9) from global_datos where id like 'MUTUEMPR%';

select id,concepto_1,logico_1,entero_1 from global_datos where id like 'MUTUEMPRX%';

INSERT INTO global_datos(id,concepto_1,logico_1,entero_1)
select CONCAT('MUTUEMPRH',lpad(@n := @n + 1,3,0)),concepto_1,logico_1,2250 from global_datos, (SELECT @n := 0) r 
where id like 'MUTUEMPR%'
and id <> 'MUTUEMPR' and entero_1 = 2201;

INSERT INTO global_datos(id,concepto_1,logico_1,entero_1)
select CONCAT('MUTUEMPRI',lpad(@n := @n + 1,3,0)),concepto_1,logico_1,2251 from global_datos, (SELECT @n := 0) r 
where id like 'MUTUEMPR%'
and id <> 'MUTUEMPR' and entero_1 = 2202;

-- // ARMAR LA LIQUIDACION TURNO PARA EL GOB DE CORDOBA EN BASE AL CODIGO
-- // DE EMPRESA MUTUEMPRP001 (GOBCBA).
-- // *** OJO! FIJARSE BIEN CUAL ES EL CODIGO DE GOB CBA PARA TARJETA DE DEBITO

start transaction;
select id into @id from global_datos where id like 'MUTUEMPR%'
and entero_1 = 2250 and concepto_1 like '%GOB%CORDOBA%' ;

DELETE FROM `liquidacion_turnos` WHERE (`id` = '1661') and (`turno` =  @id ) and (`codigo_empresa` =  @id ) and (`codigo_reparticion` = ' ');
insert into liquidacion_turnos(turno,codigo_empresa,codigo_reparticion,descripcion)
select turno,@id,codigo_reparticion,descripcion from liquidacion_turnos
where codigo_empresa = 'MUTUEMPRP001';

commit;

-- COLOCAR LA CATEGORIA DEL SOCIO PARA QUE NO DE ERROR CUANDO SE GENERA UNA ALTA
UPDATE `global_datos` SET `concepto_3` = 'MUTUCASOACTI' WHERE (`id` = 'MUTUCORG2250');
UPDATE `global_datos` SET `concepto_3` = 'MUTUCASOACTI' WHERE (`id` = 'MUTUCORG2251');
