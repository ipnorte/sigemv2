/* LIMPIAR DATOS */
truncate table asincronos;
truncate table liquidacion_cuotas;
truncate table liquidacion_socios;
truncate table liquidaciones;


/* CONTROL DE LIQUIDACION DE DEUDA  */
select 
	socio_id,persona_beneficio_id,tipo_producto,
	SUM(importe - ifnull((select sum(importe)from orden_descuento_cobro_cuotas
	where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0))
	as deuda
from 
	orden_descuento_cuotas
where 
	-- socio_id = 2415 and persona_beneficio_id = 486 and
	periodo <= '200909' and estado = 'A' and situacion = 'MUTUSICUMUTU'
GROUP BY socio_id,persona_beneficio_id,tipo_producto

select 
	SUM(importe - ifnull((select sum(importe)from orden_descuento_cobro_cuotas
	where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0))
	as deuda
from 
	orden_descuento_cuotas
where 
	periodo <= '200909' and estado = 'A' and situacion = 'MUTUSICUMUTU'

-- --------------------------------
select * from liquidaciones
select * from liquidacion_socios where liquidacion_id = 1 order by documento

select * from liquidacion_socios where liquidacion_id = 1 and documento = '05008654'
select * from liquidacion_cuotas where liquidacion_id = 1 and socio_id = 137

select codigo_organismo,socio_id,persona_beneficio_id,1 as tipo_producto,sum(importe) 
from liquidacion_cuotas where liquidacion_id = 1 and codigo_organismo = 'MUTUCORG7701' 
and socio_id = 137 and tipo_producto = 'MUTUPROD0003'
union
select codigo_organismo,socio_id,persona_beneficio_id,2 as tipo_producto,sum(importe) 
from liquidacion_cuotas where liquidacion_id = 1 and codigo_organismo = 'MUTUCORG7701' 
and socio_id = 137 and tipo_producto <> 'MUTUPROD0003'
group by codigo_organismo,socio_id,persona_beneficio_id
union
select codigo_organismo,socio_id,persona_beneficio_id,2 as tipo_producto,sum(importe) 
from liquidacion_cuotas where liquidacion_id = 1 and codigo_organismo = 'MUTUCORG2201' 
and socio_id = 137
group by codigo_organismo,socio_id,persona_beneficio_id
order by codigo_organismo


select * from liquidacion_socios where liquidacion_id = 1 and codigo_organismo = 'MUTUCORG7701' order by documento;
select * from liquidacion_socios where liquidacion_id = 1 and codigo_organismo = 'MUTUCORG2201' order by documento


select * from liquidacion_cuotas where liquidacion_id = 1 and codigo_organismo = 'MUTUCORG7701'

select * from orden_descuento_cuotas c, persona_beneficios p
where c.persona_beneficio_id = p.id and p.codigo_beneficio = 'MUTUCORG6601'

select count(*) from persona_beneficios where codigo_beneficio = 'MUTUCORG7701'
select count(*) from persona_beneficios where codigo_beneficio = 'MUTUCORG7702'
select count(*) from persona_beneficios where codigo_beneficio = 'MUTUCORG6601'
select count(*) from persona_beneficios where codigo_beneficio = 'MUTUCORG6602'
select count(*) from persona_beneficios where codigo_beneficio = 'MUTUCORG2201'

-- ---------------------------------------------------------------
select * from liquidacion_socios where liquidacion_id = 1 and codigo_organismo = 'MUTUCORG7701' order by documento

select pj.documento,pj.tipo,pj.ley,pj.beneficio,pj.sub_beneficio,
l.nro_beneficio,l.nro_ley,l.sub_beneficio,l.documento
from cba_db.padron_jubilados pj, aman2_db.liquidacion_socios l
where pj.documento = l.documento and l.codigo_organismo = 'MUTUCORG7701' and
pj.beneficio <> l.nro_beneficio

select * from liquidacion_socios where liquidacion_id = 2 and codigo_organismo = 'MUTUCORG7701'
and documento not in(select documento from cba_db.padron_jubilados)

select * from liquidacion_socios where liquidacion_id = 2 and codigo_organismo = 'MUTUCORG7701'
and documento in(select documento from cba_db.padron_jubilados)

select length(tipo),count(1) from cba_db.padron_jubilados
group by length(tipo) having count(1) > 1
select length(ley),count(1) from cba_db.padron_jubilados
group by length(ley) having count(1) > 1
select length(beneficio),count(1) from cba_db.padron_jubilados
group by length(beneficio) having count(1) > 1
select length(sub_beneficio),count(1) from cba_db.padron_jubilados
group by length(sub_beneficio) having count(1) > 1


-- ----------------------
select pj.documento,pj.tipo,pj.ley,pj.beneficio,pj.sub_beneficio,
l.nro_beneficio,l.nro_ley,l.sub_beneficio,l.documento
from cba_db.padron_jubilados pj, aman2_db.liquidacion_socios l
where pj.documento = l.documento and l.codigo_organismo = 'MUTUCORG7701' and
pj.ley = l.nro_ley and pj.beneficio <> right(concat('000000',l.nro_beneficio),6) and length(l.nro_beneficio) < 6


select pj.documento,pj.tipo,pj.ley,pj.beneficio,pj.sub_beneficio,
l.nro_beneficio,l.nro_ley,l.sub_beneficio,l.documento
from cba_db.padron_jubilados pj, aman2_db.liquidacion_socios l
where pj.documento = l.documento and l.codigo_organismo = 'MUTUCORG7701' and
pj.ley = l.nro_ley and pj.beneficio <> right(concat('000000',l.nro_beneficio),6)
-- and length(l.nro_beneficio) < 6

select * from cba_db.padron_jubilados limit 100

-- -----------------------------------------------------------
-- -----------------------------------------------------------
/*
-- //////////////////////////////////////
-- ANSES
-- //////////////////////////////////////
XX-X-XXXXXXX-X

XX = INDICA DE QUE EX-CAJA DE JUBILACION PROVIENE
X = JUBILACION (<5 = JUB) (>=5 PENSION)
XXXXXXX = NRO
X = INICA EL COPARTICIPE (SUB-BENEFICIO) 0 = TITULAR | 1 A 9 COPARTICIPES
 

52 10 102521 0
52 10 103155 0
105377 00
52 10 108038 0

2009753650
20-0-9753650-?-?
20-0-9753650
2009753650

10-1-0618090-?
11-0-9567729-0

90-3-2905510
90-9-5278610
90-9-1027460

15-0-1076589-0-1 --> JUBILACION
15-0-2706109-0 --> jubilacion
15-0-2742073-0 --> jubilacion

15-5-0380505-0-7 --> pension
15-0-3137960-0-6 --> jubilacion
97-1-6229026-0-1 --> seguro desempleo

15-0-1367770-0
13-5-5243549-0


*/

select LENGTH(nro_beneficio),count(1) from persona_beneficios where codigo_beneficio = 'MUTUCORG6601'
group by nro_beneficio having count(1) > 1

select * from persona_beneficios where codigo_beneficio = 'MUTUCORG6601'
and LENGTH(nro_beneficio) > 10

select * from persona_beneficios where codigo_beneficio = 'MUTUCORG6601'
and nro_beneficio = '15013677700'


select * from liquidacion_socios where documento = '04563099'


update liquidacion_socios set tipo = '0', sub_beneficio = '00' where codigo_organismo = 'MUTUCORG7701'