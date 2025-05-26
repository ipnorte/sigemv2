CREATE DEFINER=`root`@`localhost` PROCEDURE `p_estado_cuenta`(
	IN vSOCIO_ID INT(11)
)
BEGIN
select 
orden_descuento_cuotas.id as ID,
'VENCIDO' AS TIPO,
orden_descuento_id AS ORDEN_DESCUENTO_ID,
concat(orden_descuentos.tipo_orden_dto,' #',orden_descuentos.numero) as TIPO_NUMERO,
tipo_cuota.concepto_1 as CONCEPTO,
orden_descuento_cuotas.periodo AS PERIODO,
concat(substr(orden_descuento_cuotas.periodo,5,2),'/',substr(orden_descuento_cuotas.periodo,1,4)) as PERIODO_STR,
concat(lpad(orden_descuento_cuotas.nro_cuota,2,'0'),'/',lpad(orden_descuentos.cuotas,2,'0')) as CUOTA,
orden_descuento_cuotas.importe AS IMPORTE_ORIGINAL,
ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0) as PAGOS,
orden_descuento_cuotas.importe - ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0) as SALDO_CONCILIADO,
ifnull((select sum(importe_debitado) from liquidacion_cuotas where liquidacion_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id
and liquidacion_cuotas.imputada = 0 and liquidacion_cuotas.para_imputar = 1),0) as PENDIENTE_ACREDITAR,
orden_descuento_cuotas.importe - ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0) - 
ifnull((select sum(importe_debitado) from liquidacion_cuotas where liquidacion_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id
and liquidacion_cuotas.imputada = 0 and liquidacion_cuotas.para_imputar = 1),0) as SALDO
from orden_descuento_cuotas 
inner JOIN orden_descuentos on (orden_descuentos.id = orden_descuento_cuotas.orden_descuento_id)
inner join global_datos as tipo_cuota on (tipo_cuota.id = orden_descuento_cuotas.tipo_cuota)
where orden_descuento_cuotas.socio_id = vSOCIO_ID
and orden_descuento_cuotas.importe > ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0)
and orden_descuento_cuotas.periodo < date_format(now(),'%Y%m') 
union
select 
orden_descuento_cuotas.id as ID,
'CORRIENTE' AS TIPO,
orden_descuento_id AS ORDEN_DESCUENTO_ID,
concat(orden_descuentos.tipo_orden_dto,' #',orden_descuentos.numero) as TIPO_NUMERO,
tipo_cuota.concepto_1 as CONCEPTO,
orden_descuento_cuotas.periodo AS PERIODO,
concat(substr(orden_descuento_cuotas.periodo,5,2),'/',substr(orden_descuento_cuotas.periodo,1,4)) as PERIODO_STR,
concat(lpad(orden_descuento_cuotas.nro_cuota,2,'0'),'/',lpad(orden_descuentos.cuotas,2,'0')) as CUOTA,
orden_descuento_cuotas.importe AS IMPORTE_ORIGINAL,
ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0) as PAGOS,
orden_descuento_cuotas.importe - ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0) as SALDO_CONCILIADO,
ifnull((select sum(importe_debitado) from liquidacion_cuotas where liquidacion_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id
and liquidacion_cuotas.imputada = 0 and liquidacion_cuotas.para_imputar = 1),0) as PENDIENTE_ACREDITAR,
orden_descuento_cuotas.importe - ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0) - 
ifnull((select sum(importe_debitado) from liquidacion_cuotas where liquidacion_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id
and liquidacion_cuotas.imputada = 0 and liquidacion_cuotas.para_imputar = 1),0) as SALDO
from orden_descuento_cuotas 
inner JOIN orden_descuentos on (orden_descuentos.id = orden_descuento_cuotas.orden_descuento_id)
inner join global_datos as tipo_cuota on (tipo_cuota.id = orden_descuento_cuotas.tipo_cuota)
where orden_descuento_cuotas.socio_id = vSOCIO_ID
and orden_descuento_cuotas.importe > ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0)
and orden_descuento_cuotas.periodo = date_format(now(),'%Y%m') 
union
select 
orden_descuento_cuotas.id as ID,
'A_VENCER' AS TIPO,
orden_descuento_id AS ORDEN_DESCUENTO_ID,
concat(orden_descuentos.tipo_orden_dto,' #',orden_descuentos.numero) as TIPO_NUMERO,
tipo_cuota.concepto_1 as CONCEPTO,
orden_descuento_cuotas.periodo AS PERIODO,
concat(substr(orden_descuento_cuotas.periodo,5,2),'/',substr(orden_descuento_cuotas.periodo,1,4)) as PERIODO_STR,
concat(lpad(orden_descuento_cuotas.nro_cuota,2,'0'),'/',lpad(orden_descuentos.cuotas,2,'0')) as CUOTA,
orden_descuento_cuotas.importe AS IMPORTE_ORIGINAL,
ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0) as PAGOS,
orden_descuento_cuotas.importe - ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0) as SALDO_CONCILIADO,
ifnull((select sum(importe_debitado) from liquidacion_cuotas where liquidacion_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id
and liquidacion_cuotas.imputada = 0 and liquidacion_cuotas.para_imputar = 1),0) as PENDIENTE_ACREDITAR,
orden_descuento_cuotas.importe - ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0) - 
ifnull((select sum(importe_debitado) from liquidacion_cuotas where liquidacion_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id
and liquidacion_cuotas.imputada = 0 and liquidacion_cuotas.para_imputar = 1),0) as SALDO
from orden_descuento_cuotas 
inner JOIN orden_descuentos on (orden_descuentos.id = orden_descuento_cuotas.orden_descuento_id)
inner join global_datos as tipo_cuota on (tipo_cuota.id = orden_descuento_cuotas.tipo_cuota)
where orden_descuento_cuotas.socio_id = vSOCIO_ID
and orden_descuento_cuotas.importe > ifnull((select sum(importe) from orden_descuento_cobro_cuotas
where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0)
and orden_descuento_cuotas.periodo > date_format(now(),'%Y%m') 
order by PERIODO ASC,ID,CUOTA ASC;

END