
DROP PROCEDURE IF EXISTS `SP_LIQUIDA_DEUDA_SCORING`;
DELIMITER $$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_LIQUIDA_DEUDA_SCORING`(IN
vSOCIO_ID INT(11),vLIQUIDACION_ID INT(11))
BEGIN

select periodo into @periodo from liquidaciones where id = vLIQUIDACION_ID;
delete from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID and socio_id = vSOCIO_ID;
insert into liquidacion_socio_scores(liquidacion_id,socio_id,`13`,`12`,`09`,`06`,`03`,`00`,cargos_adicionales,saldo_actual)
select liquidacion_id,socio_id, 
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 12 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 12 month),'%Y%m')  
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 9 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 9 month),'%Y%m')
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 6 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota  > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 6 month),'%Y%m') 
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 3 month),'%Y%m') 
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota  > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 3 month),'%Y%m') 
and lc.periodo_cuota <= date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 0 month),'%Y%m')
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id = 0
and lc.periodo_cuota > date_format(date_sub(date_sub(date_format(concat(@periodo,'01'),'%Y-%m-%d'), interval 1 month), interval 0 month),'%Y%m')
and lc.socio_id = lc2.socio_id),0),
ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
inner join liquidaciones l on (l.id = lc.liquidacion_id)
where lc.liquidacion_id = lc2.liquidacion_id and lc.mutual_adicional_pendiente_id <> 0
and lc.socio_id = lc2.socio_id),0),
sum(saldo_actual) as saldo_actual
from liquidacion_cuotas lc2 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID
group by socio_id;

-- //// ASIGNO PUNTAJE
if cast((select `13` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 5 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `12` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 4 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `09` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 3 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `06` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 2 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `03` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 1 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
else if cast((select `00` from liquidacion_socio_scores where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID) as decimal(10,2)) > 0 then
	update liquidacion_socio_scores set riesgo = 0 where liquidacion_id = vLIQUIDACION_ID
and socio_id = vSOCIO_ID;
end if;
end if;
end if;
end if;
end if;
end if;


END$$
DELIMITER ;
