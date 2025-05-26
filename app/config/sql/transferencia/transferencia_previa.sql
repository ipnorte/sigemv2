/*** DATOS TABLA GLOBAL ***/
INSERT INTO aman2_db.global_datos(id,concepto_1)
select codigo,concepto from aman_db.tglobal where codigo like 'MUTUFILI%'

INSERT INTO aman2_db.global_datos(id,concepto_1)
select codigo,concepto from aman_db.tglobal where codigo like 'MUTUEMPR%'


select codigo,concepto,valor_1 from aman_db.tglobal ORDER BY codigo


INSERT INTO aman2_db.global_datos(id,concepto_1)
select CONCAT('PERS',codigo),concepto from aman_db.tglobal where codigo like 'TPDC%' ORDER BY codigo

delete from aman2_db.global_datos where id like 'PERSTDOC%'

INSERT INTO aman2_db.global_datos(id,concepto_1)
select CONCAT('PERS',codigo),concepto from aman_db.tglobal where codigo like 'XXEC%' ORDER BY codigo

delete from aman2_db.global_datos where id like 'PERSESTC%'


INSERT INTO aman2_db.global_datos(id,concepto_1)
select CONCAT('PERS',codigo),UPPER(concepto) from aman_db.tglobal where codigo like 'XXTI%' ORDER BY codigo



INSERT INTO aman2_db.global_datos(id,concepto_1,decimal_1)
select CONCAT('MUTU',codigo),concepto,valor_1 from aman_db.tglobal where codigo like 'CUOS%' ORDER BY codigo

select id,concepto_1,decimal_1 from aman2_db.global_datos where id like 'MUTUCU%' ORDER BY id

update aman2_db.global_datos set id = 'MUTUCUOS2201' where id = 'MUTUCUOS0022';
update aman2_db.global_datos set id = 'MUTUCUOS6601' where id = 'MUTUCUOS0066';
update aman2_db.global_datos set id = 'MUTUCUOS7701' where id = 'MUTUCUOS0077';


/********************************************************/
/*	LOCALIDADES			*/
/********************************************************/
-- truncate table aman2_db.localidades
select * from aman2_db.localidades

insert into aman2_db.localidades 
(cp,nombre,provincia_id,letra_provincia,idr)
select codigo_postal,localidad,0,codigo_provincia,id from aman_db.localidades

select * from aman2_db.provincias 

update aman2_db.localidades l,aman2_db.provincias p
set
	l.provincia_id = p.id
where l.letra_provincia = p.letra


/********************************************************/
insert into aman2_db.personas(tipo_documento,documento,apellido,nombre,estado_civil,idr)
select concat('PERSTPDC',tipo_documento),documento,apellido,nombre,
concat('PERSXXEC',if(estado_civil <> '',estado_civil,'0001')),id_persona from aman_db.personas
limit 0,1000

update aman2_db.personas p2, aman_db.personas p
set
	p2.sexo =  p.sexo,
	p2.calle =  p.calle,
	p2.numero_calle =  p.nro_calle,
	p2.piso =  p.piso,
	p2.dpto =  p.dpto,
	p2.barrio =  p.barrio,
	p2.localidad_id =  p.codigo_localidad,	
	p2.localidad =  p.localidad,
	p2.codigo_postal = p.codigo_postal,
	p2.cuit_cuil = p.cuit_cuil,
	p2.fecha_nacimiento = p.fecha_nacimiento,
	p2.telefono_fijo = p.telefono_fijo,
	p2.telefono_movil = p.telefono_movil,
	p2.telefono_referencia = p.telefono_referencia,
	p2.persona_referencia = p.persona_referencia,
	p2.e_mail = p.e_mail,
	p2.nombre_conyuge = p.nombre_conyuge,
	p2.tipo_vivienda = if(p.tipo_vivienda <> '',concat('PERSTIVI',p.tipo_vivienda),'PERSTVIV0003'),
	p2.filial = if(p.filial <> '',concat('MUTUFILI',p.filial),'MUTUFILI0001')
where
	p2.idr =  p.id_persona


select * from aman2_db.personas where fecha_nacimiento = '0000-00-00'

update aman2_db.personas set fecha_nacimiento = NULL where fecha_nacimiento = '0000-00-00';


delete from aman2_db.personas where apellido = ''



/* PASO LOS BENEFICIOS */


insert into aman2_db.persona_beneficios 
	(persona_id, 
	codigo_beneficio, 
	nro_beneficio, 
	nro_ley, 
	nro_legajo, 
	fecha_ingreso, 
	codigo_reparticion, 
	cbu, 
	banco_id, 
	nro_sucursal, 
	tipo_cta_bco, 
	nro_cta_bco, 
	codigo_empresa, 
	principal, 
	activo, 
	porcentaje, 
	idr)
select 
	p2.id, 
	concat('MUTUCORG',pb.codigo_beneficio), 
	pb.nro_beneficio, 
	pb.nro_ley, 
	pb.nro_legajo, 
	pb.fecha_ingreso, 
	pb.codigo_reparticion, 
	pb.cbu, 
	pb.codigo_banco, 
	pb.sucursal, 
	pb.tipo_cta_bco, 
	pb.nro_cta_bco, 
	concat('MUTUEMPR',pb.codigo_empresa), 
	pb.principal, 
	pb.activo, 
	pb.porcentaje,
	pb.id_beneficio
from 
	aman_db.personas_beneficios pb,
	aman2_db.personas p2
where
	p2.idr = pb.id_persona

	


update aman2_db.persona_beneficios
set
	codigo_beneficio = 'MUTUCORG2201' 
where
	codigo_beneficio = 'MUTUCORG2202';
update aman2_db.persona_beneficios
set
	codigo_beneficio = 'MUTUCORG6601' 
where
	codigo_beneficio = 'MUTUCORG6602';
update aman2_db.persona_beneficios
set
	codigo_beneficio = 'MUTUCORG7701' 
where
	codigo_beneficio = 'MUTUCORG7702';



/* PASO LOS SOCIOS */

select s.nro_socio,count(1)
from aman_db.socios s,aman2_db.personas p,aman2_db.persona_beneficios b
where p.idr = s.id_socio and p.id = b.persona_id and b.principal = 1 and b.activo = 1
and s.activo = '1'
group by s.nro_socio having count(1)> 1

insert into aman2_db.socios(id,persona_id,socio_solicitud_id,persona_beneficio_id,periodo_ini, 
activo,fecha_alta,idr,user_created,created)
select s.nro_socio,p.id,s.nro_socio,b.id,concat(year(s.fecha_alta),RIGHT(concat('00',month(s.fecha_alta)),2)),s.activo,s.fecha_alta,s.id_socio,s.usuario,s.fecha_ac 
from aman_db.socios s,aman2_db.personas p,aman2_db.persona_beneficios b
where p.idr = s.id_socio and p.id = b.persona_id and b.principal = 1 and b.activo = 1
and s.activo = '1'
order by s.nro_socio

-- -----------------------------------------------------------------
select * from aman2_db.socios

-- -----------------------------------------------------------------
-- CREO UNA SOLICITUD
-- -----------------------------------------------------------------
insert into aman2_db.socio_solicitudes(id,tipo_solicitud,aprobada,persona_id,persona_beneficio_id, 
fecha,periodo_ini,observaciones,user_created,created)
select s.nro_socio,'A',1,p.id,b.id,s.fecha_alta,concat(year(s.fecha_alta),RIGHT(concat('00',month(s.fecha_alta)),2)),
'*** TRANSFERENCIA V1 ***',s.usuario,s.fecha_ac
from aman_db.socios s,aman2_db.personas p, aman2_db.persona_beneficios b
where p.idr = s.id_socio and p.id = b.persona_id and b.principal = 1 
and p.id not in (select persona_id from socio_solicitudes)
and s.activo = '1'
order by s.nro_socio;


select * from aman2_db.socio_solicitudes

select p.id,count(1)
from aman_db.socios s,aman2_db.personas p, aman_db.personas_beneficios b
where p.idr = s.id_socio and p.idr = b.id_persona and b.principal = 1
group by p.id 
having count(1) > 1



/*
-- SOCIOS NUEVOS
insert into aman2_db.socio_solicitudes(id,tipo_solicitud,aprobada,persona_id,persona_beneficio_id, 
fecha,periodo_ini,observaciones,user_created,created)
select s.nro_solicitud,'A',1,p.id,b.id,s.fecha_solicitud,concat(year(s.fecha_estado),RIGHT(concat('00',month(s.fecha_estado)),2)),
'*** TRANSFERENCIA V1 ***',s.usuario,s.fecha_ac
from aman_db.solicitudes s,aman2_db.personas p, aman2_db.persona_beneficios b
where p.idr = s.id_persona and p.id = b.persona_id and b.principal = 1
and s.estado in(14,19)
GROUP BY s.id_persona
order by s.nro_solicitud;

-- SOCIOS VIEJOS
insert into aman2_db.socio_solicitudes(id,tipo_solicitud,aprobada,persona_id,persona_beneficio_id, 
fecha,periodo_ini,observaciones,user_created,created)
select s.nro_socio,'A',1,p.id,b.id,s.fecha_alta,concat(year(s.fecha_alta),RIGHT(concat('00',month(s.fecha_alta)),2)),
'*** TRANSFERENCIA V1 ***',s.usuario,s.fecha_ac
from aman_db.socios s,aman2_db.personas p, aman2_db.persona_beneficios b
where p.idr = s.id_socio and p.id = b.persona_id and b.principal = 1 
and p.id not in (select persona_id from socio_solicitudes)
and s.activo = '1'
order by s.nro_socio;

*/

select * from aman_db.solicitudes where monto_cuota_social > 0
select  * from aman_db.personas where id_persona = 13611

/*

select * from aman2_db.socio_solicitudes where periodo_ini = '';


update aman2_db.socio_solicitudes set anio_ini = year(fecha) where anio_ini = '';
update aman2_db.socio_solicitudes set periodo_ini = RIGHT(concat('00',month(fecha)),2) where periodo_ini = '';
*/	

/*
insert into aman2_db.socios(id,persona_id,socio_solicitud_id,persona_beneficio_id,periodo_ini, 
activo,idr,user_created,created)
select s.nro_socio,p.id,ss.id,ss.persona_beneficio_id,ss.periodo_ini,s.activo,s.id_socio,s.usuario,s.fecha_ac 
from aman_db.socios s,aman2_db.personas p,aman2_db.socio_solicitudes ss
where p.idr = s.id_socio and p.id = ss.persona_id
-- and s.activo = '1'
order by s.nro_socio
*/

select persona_id,count(1) from aman2_db.socio_solicitudes ss
group by persona_id having count(1)> 1

/*
-- ACTUALIZO LA FECHA DE ALTA
select * from aman2_db.socios s2, aman_db.socios s1
where s2.idr = s1.id_socio

update aman2_db.socios s2, aman_db.socios s1
set s2.fecha_alta = s1.fecha_alta
where s2.idr = s1.id_socio


select * from aman2_db.socios s2 where fecha_alta is null

--- actualizo el periodo de inicio en base a la fecha de alta



select * from aman2_db.socio_solicitudes ss
*/

-- verifico los que tiene solicitud aprobada y no esta el socio
select * from socio_solicitudes where aprobada = 1
and id not in (select socio_solicitud_id from socios)

update socio_solicitudes set aprobada = 0
where aprobada = 1
and id not in (select socio_solicitud_id from socios)


/* GENERO LA ORDEN DE DESCUENTO POR LA CUOTA SOCIAL*/
select * from aman2_db.orden_descuentos
select * from aman2_db.socio_solicitudes
select * from aman2_db.socios where id = 10966


insert into aman2_db.orden_descuentos 
(fecha,tipo_orden_dto,numero,mutual_producto_id,tipo_producto,proveedor_id,socio_id,persona_beneficio_id,periodo_ini,periodicidad,activo,permanente,user_created,  
created)
select ss.fecha,'MUTUTPROCFIJ',ss.id,2,'MUTUPROD0003',18,s.id,s.persona_beneficio_id,
ss.periodo_ini,0,1,1,s.user_created,s.created
from aman2_db.socio_solicitudes ss, aman2_db.socios s
where ss.id = s.socio_solicitud_id

/*
-- aman2_db.orden_descuentos
update aman2_db.expedientes
set
	periodo_ini = concat(anio_ini,periodo_ini),
	periodicidad = '0'
*/

-- actualizo el id expediente en la tabla de solicitudes de socio
select * from orden_descuentos e, socio_solicitudes s
where e.tipo_orden_dto = 'MUTUTPROCFIJ' and e.tipo_producto = 'MUTUPROD0003' 
and e.numero = s.id


update socio_solicitudes s,orden_descuentos e
set s.orden_descuento_id = e.id
where e.tipo_orden_dto = 'MUTUTPROCFIJ' and e.tipo_producto = 'MUTUPROD0003' 
and e.numero = s.id

update socios s,orden_descuentos e
set s.orden_descuento_id = e.id
where e.tipo_orden_dto = 'MUTUTPROCFIJ' and e.tipo_producto = 'MUTUPROD0003' 
and e.socio_id = s.id


-- ACTUALIZO EL IMPORTE DE LA CUOTA SOCIAL
select concat('MUTUCUOS',right(b.codigo_beneficio,4)),e.*,g.decimal_1 from orden_descuentos e, persona_beneficios b,global_datos g
where
	e.tipo_orden_dto = 'MUTUTPROCFIJ' and e.tipo_producto = 'MUTUPROD0003' 
	and e.persona_beneficio_id = b.id
	and concat('MUTUCUOS',right(b.codigo_beneficio,4)) = g.id


update orden_descuentos e, persona_beneficios b, global_datos g  
set
	e.importe_cuota = g.decimal_1,
	e.importe_total = g.decimal_1
where
	e.tipo_orden_dto = 'MUTUTPROCFIJ' and e.tipo_producto = 'MUTUPROD0003' 
	and e.persona_beneficio_id = b.id
	and concat('MUTUCUOS',right(b.codigo_beneficio,4)) = g.id


/* PASO LOS PROVEEDORES */

insert into aman2_db.proveedores 
	(cuit, 
	razon_social, 
	activo, 
	idr
	)
select codigo_proveedor,razon_social,activo,codigo_proveedor from aman_db.proveedores

update aman2_db.proveedores p2, aman_db.proveedores p1
set
	p2.calle = p1.calle,
	p2.numero_calle = p1.numero,
	p2.piso = p1.piso,
	p2.dpto = p1.dpto,
	p2.barrio = p1.barrio,
	p2.localidad = p1.localidad,
	p2.codigo_postal = p1.codigo_postal,
	p2.nro_ingresos_brutos = p1.ingreso_bruto,
	p2.condicion_iva = CONCAT('PERSXXTI',p1.nro_iva),
	p2.razon_social_resumida = p2.razon_social
where
	p2.idr = p1.codigo_proveedor

insert into aman2_db.mutual_productos 
	(descripcion, 
	tipo, 
	activo, 
	proveedor_id, 
	idr
	)
select concat(concat(concat('#',pp.codigo_producto),' - '),pp.descripcion),'MUTUPROD0001',0,p.id,pp.codigo_producto from aman_db.proveedores_productos pp,
aman2_db.proveedores p
where p.idr = pp.codigo_proveedor


-- -------------------------------------------------------
-- PASO LOS EXPEDIENTES DE LA VERSION 1
-- -------------------------------------------------------
-- CREDITO
insert into aman2_db.orden_descuentos (
fecha,tipo_orden_dto, numero, tipo_producto, proveedor_id, mutual_producto_id, socio_id, persona_beneficio_id, 
periodo_ini, periodicidad,importe_total, importe_cuota, cuotas, activo, permanente, user_created, created)
select e.fecha_inicio,'MUTUTPROEXPT',e.nro_expediente,'MUTUPROD0001',pr.id,0,s.id,b.id,
concat(year(e.fecha_inicio),RIGHT(concat('00',month(e.fecha_inicio)),2)),0,e.importe_total,e.importe_total / e.cuotas,
e.cuotas,1,0,usuario,fecha_ac
from aman_db.expedientes e, aman2_db.personas p, aman2_db.persona_beneficios b, aman2_db.proveedores pr,
aman_db.proveedores_productos pp,aman2_db.socios s
where 
	e.codigo_producto = pp.codigo_producto and
	e.id_persona = p.idr and
	e.id_beneficio = b.idr and
	pp.codigo_proveedor = pr.idr and
	p.id = s.persona_id

-- SEGURO DEL CREDITO
insert into aman2_db.orden_descuentos (
fecha,tipo_orden_dto, numero, tipo_producto, proveedor_id, mutual_producto_id, socio_id, persona_beneficio_id, 
periodo_ini, periodicidad,importe_total, importe_cuota, cuotas, activo, permanente, user_created, created)

select e.fecha_inicio,'MUTUTPROEXPT',e.nro_expediente,'MUTUPROD0002',18,0,s.id,b.id,
concat(year(e.fecha_inicio),RIGHT(concat('00',month(e.fecha_inicio)),2)),0,e.monto_seguro * e.cuotas,e.monto_seguro,
e.cuotas,1,0,usuario,fecha_ac
from aman_db.expedientes e, aman2_db.personas p, aman2_db.persona_beneficios b, aman2_db.proveedores pr,
aman_db.proveedores_productos pp,aman2_db.socios s
where 
	e.codigo_producto = pp.codigo_producto and
	e.id_persona = p.idr and
	e.id_beneficio = b.idr and
	pp.codigo_proveedor = pr.idr and
	p.id = s.persona_id

-- -------------------------------------------------------
-- ARMO LOS VENCIMIENTOS
-- CBU MES SIGUIENTE AL PERIODO DE INICIO
-- CJP MES SIGUIENTE AL PERIODO DE INICIO
-- ANSES MES DE INICIO
-- -------------------------------------------------------
-- CBU Y CJP
update aman2_db.orden_descuentos od, aman2_db.persona_beneficios b
set 
	od.primer_vto_socio = DATE_ADD(CONVERT(concat(concat(concat(year(od.fecha),'-'),RIGHT(concat('00',month(od.fecha)),2)),'-20'),DATE),INTERVAL 1 MONTH),
	od.primer_vto_proveedor = DATE_ADD(DATE_ADD(CONVERT(concat(concat(concat(year(od.fecha),'-'),RIGHT(concat('00',month(od.fecha)),2)),'-20'),DATE),INTERVAL 1 MONTH),INTERVAL 9 day)
where od.persona_beneficio_id = b.id and b.codigo_beneficio in('MUTUCORG2201','MUTUCORG7701')
-- ANSES
update aman2_db.orden_descuentos od, aman2_db.persona_beneficios b
set 
	od.primer_vto_socio = DATE_ADD(CONVERT(concat(concat(concat(year(od.fecha),'-'),RIGHT(concat('00',month(od.fecha)),2)),'-20'),DATE),INTERVAL 0 MONTH),
	od.primer_vto_proveedor = DATE_ADD(DATE_ADD(CONVERT(concat(concat(concat(year(od.fecha),'-'),RIGHT(concat('00',month(od.fecha)),2)),'-20'),DATE),INTERVAL 0 MONTH),INTERVAL 9 day)
where od.persona_beneficio_id = b.id and b.codigo_beneficio = 'MUTUCORG6601'


select
b.codigo_beneficio,od.periodo_ini, od.primer_vto_socio,od.primer_vto_proveedor
from aman2_db.orden_descuentos od, aman2_db.persona_beneficios b
where od.persona_beneficio_id = b.id



-- -------------------------------------------------------
-- ACTUALIZO EL TIPO DE CUOTA
-- -------------------------------------------------------
select * from aman2_db.orden_descuento_cuotas c, aman2_db.global_datos g
where c.tipo_producto = g.id

update aman2_db.orden_descuento_cuotas c, aman2_db.global_datos g
set c.tipo_cuota = g.concepto_2
where c.tipo_producto = g.id


