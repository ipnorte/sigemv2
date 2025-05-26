select persona_id,codigo_empresa,cbu,count(1) from persona_beneficios where codigo_beneficio = 'MUTUCORG2201'
and activo = 1 -- and dupli = 0
group by persona_id,codigo_empresa,cbu having count(1) > 1

select * from persona_beneficios where persona_id = 621 and cbu = '0200914211000011136558' 
and codigo_empresa = 'MUTUEMPRE010'
order by id DESC LIMIT 1;

select TABLE_NAME from information_schema.COLUMNS where TABLE_SCHEMA = 'aman2_db' and COLUMN_NAME = 'persona_beneficio_id';

select persona_id,nro_beneficio,nro_ley,count(1) from persona_beneficios where codigo_beneficio = 'MUTUCORG7701'
and activo = 1
group by persona_id,nro_beneficio,nro_ley having count(1) > 1

select persona_id,nro_beneficio,count(1) from persona_beneficios where codigo_beneficio = 'MUTUCORG6601'
and activo = 1
group by persona_id,nro_beneficio having count(1) > 1

SELECT * from persona_beneficios where dupli = 1
update persona_beneficios set fecha_baja = '2009-09-18' where dupli = 1