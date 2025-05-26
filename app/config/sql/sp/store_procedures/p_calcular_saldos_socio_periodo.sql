CREATE DEFINER=`root`@`localhost` PROCEDURE `p_calcular_saldos_socio_periodo`(
	IN vPERIODO VARCHAR(6),
	vSOCIO_ID INT(11)
)
BEGIN
SELECT periodo,
IFNULL(SUM(importe) - IFNULL((SELECT SUM(importe) FROM orden_descuento_cobro_cuotas cocu_1
WHERE cocu_1.orden_descuento_cuota_id = orden_descuento_cuotas.id
),0),0) AS saldo_periodo,
IFNULL((SELECT SUM(importe) FROM orden_descuento_cuotas odc_1 
WHERE odc_1.periodo < orden_descuento_cuotas.periodo 
AND odc_1.socio_id = orden_descuento_cuotas.socio_id)-
IFNULL((SELECT SUM(cocu.importe) FROM orden_descuento_cobro_cuotas cocu,orden_descuento_cuotas odc_1 
WHERE cocu.orden_descuento_cuota_id = odc_1.id
AND odc_1.periodo < orden_descuento_cuotas.periodo
AND odc_1.socio_id = orden_descuento_cuotas.socio_id),0),0) AS vencido,
IFNULL((SELECT SUM(importe) FROM orden_descuento_cuotas odc_1 
WHERE odc_1.periodo <= orden_descuento_cuotas.periodo 
AND odc_1.socio_id = orden_descuento_cuotas.socio_id)-
IFNULL((SELECT SUM(cocu.importe) FROM orden_descuento_cobro_cuotas cocu,orden_descuento_cuotas odc_1 
WHERE cocu.orden_descuento_cuota_id = odc_1.id
AND odc_1.periodo <= orden_descuento_cuotas.periodo
AND odc_1.socio_id = orden_descuento_cuotas.socio_id),0),0) AS saldo_total_acumulado_periodo,
IFNULL((SELECT SUM(importe) FROM orden_descuento_cuotas odc_1 
WHERE odc_1.periodo > orden_descuento_cuotas.periodo 
AND odc_1.socio_id = orden_descuento_cuotas.socio_id)-
IFNULL((SELECT SUM(cocu.importe) FROM orden_descuento_cobro_cuotas cocu,orden_descuento_cuotas odc_1 
WHERE cocu.orden_descuento_cuota_id = odc_1.id
AND odc_1.periodo > orden_descuento_cuotas.periodo
AND odc_1.socio_id = orden_descuento_cuotas.socio_id),0),0) AS a_vencer
FROM orden_descuento_cuotas WHERE socio_id = vSOCIO_ID
AND periodo = vPERIODO
GROUP BY periodo;
END