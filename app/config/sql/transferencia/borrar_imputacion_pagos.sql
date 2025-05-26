













SELECT * FROM orden_descuento_cobro_cuotas WHERE orden_descuento_cobro_id IN
(SELECT id FROM orden_descuento_cobros WHERE tipo_cobro = 'MUTUTCOBRECS')


SELECT * FROM liquidacion_cuotas WHERE liquidacion_id = 30 AND liquidacion_intercambio_id <> 0 AND importe_debitado > 0

SELECT * FROM liquidacion_cuotas WHERE liquidacion_id = 30 AND liquidacion_intercambio_id <> 0 AND importe_debitado > 0
AND mutual_adicional_pendiente_id <> 0

SELECT * FROM orden_descuento_cuotas odc, liquidacion_cuotas lc
WHERE odc.id = lc.orden_descuento_cuota_id AND
lc.liquidacion_id = 30 AND odc.estado = 'P'


SELECT * FROM orden_descuento_cobro_cuotas WHERE orden_descuento_cobro_id IN
(SELECT id FROM orden_descuento_cobros WHERE tipo_cobro = 'MUTUTCOBRECS')

-- marco como adeudada
UPDATE orden_descuento_cuotas odc, liquidacion_cuotas lc
SET odc.estado = 'A'
WHERE odc.id = lc.orden_descuento_cuota_id AND
lc.liquidacion_id = 30 AND odc.estado = 'P'

-- borro el detalle del pago
DELETE FROM orden_descuento_cobro_cuotas WHERE orden_descuento_cobro_id IN
(SELECT id FROM orden_descuento_cobros WHERE tipo_cobro = 'MUTUTCOBRECS')
AND orden_descuento_cuota_id IN (SELECT orden_descuento_cuota_id FROM 
liquidacion_cuotas WHERE liquidacion_id = 30)

DELETE FROM orden_descuento_cobros WHERE tipo_cobro = 'MUTUTCOBRECS'

DELETE FROM orden_descuento_cobro_cuotas WHERE orden_descuento_cuota_id IN (SELECT orden_descuento_cuota_id FROM 
liquidacion_cuotas WHERE liquidacion_id = 30) AND periodo_cobro >= '200909' AND orden_descuento_cobro_id IS NULL

DELETE FROM  orden_descuento_cobro_cuotas WHERE orden_descuento_cobro_id >= 32857

DELETE FROM orden_descuento_cobros WHERE tipo_cobro = 'MUTUTCOBRECS'

DELETE FROM orden_descuento_cobro_cuotas WHERE orden_descuento_cobro_id IS NULL

-- borro los adicionales
SELECT * FROM orden_descuento_cobro_cuotas WHERE orden_descuento_cuota_id IN
(SELECT id FROM orden_descuento_cuotas WHERE periodo = '200909' AND tipo_cuota = 'MUTUTCUO0010')




SELECT * FROM orden_descuento_cuotas WHERE periodo = '200909' AND tipo_cuota = 'MUTUTCUO0010'

SELECT * FROM orden_descuento_cuotas WHERE orden_descuento_id = 57033

-- 1772156
SELECT * FROM liquidacion_cuotas WHERE liquidacion_id = 30



UPDATE asincronos SET estado = 'C' WHERE id = 100;
UPDATE asincronos SET estado = 'S' WHERE id = 100;


DELETE FROM mutual_db.liquidacion_disenio_registros;
INSERT INTO mutual_db.liquidacion_disenio_registros
SELECT * FROM aman2_db.liquidacion_disenio_registros


SELECT * FROM liquidacion_socios WHERE liquidacion_id = 30

SELECT * FROM liquidacion_socios WHERE liquidacion_id = 30 AND socio_id = 494
SELECT SUM(importe_debitado) FROM liquidacion_cuotas WHERE liquidacion_id = 30 AND socio_id = 494

SELECT * FROM liquidacion_intercambios WHERE liquidacion_id = 30


INSERT INTO mutual_db.permisos
SELECT * FROM aman2_db.permisos WHERE url NOT IN (SELECT url FROM mutual_db.permisos)


SELECT * FROM orden_descuento_cobros WHERE tipo_cobro = 'MUTUTCOBRECS'

SELECT * FROM orden_descuento_cobro_cuotas WHERE orden_descuento_cobro_id = 32871 

SELECT * FROM liquidacion_cuotas WHERE orden_descuento_cuota_id = 1474783

SELECT * FROM liquidacion_socios WHERE liquidacion_id = 31 AND importe_debitado <> 0
AND orden_descuento_cobro_id = 0


SELECT proveedor_id,SUM(importe_debitado) FROM  liquidacion_cuotas WHERE liquidacion_id = 30 AND imputada = 1
GROUP BY proveedor_id


UPDATE orden_descuento_cuotas odc, liquidacion_cuotas lc
SET odc.estado = 'A'
WHERE odc.id = lc.orden_descuento_cuota_id AND
lc.liquidacion_id = 30 AND odc.estado = 'P'

DELETE FROM orden_descuento_cobro_cuotas WHERE orden_descuento_cobro_id IN
(SELECT orden_descuento_cobro_id FROM liquidacion_socios WHERE liquidacion_id = 30)

DELETE FROM orden_descuento_cobros WHERE id IN
(SELECT orden_descuento_cobro_id FROM liquidacion_socios WHERE liquidacion_id = 30)

UPDATE liquidacion_socios SET imputada = 0, orden_descuento_cobro_id = 0 WHERE liquidacion_id = 30

UPDATE liquidacion_cuotas SET imputada = 0 WHERE liquidacion_id = 30


UPDATE liquidaciones SET imputada = 0 WHERE id = 30

-- BORRO LOS GASTOS ADMIN CBU
SELECT * FROM orden_descuento_cuotas WHERE periodo = '200909' AND tipo_cuota = 'MUTUTCUO0010'
AND socio_id = 13740

SELECT * FROM liquidacion_cuotas WHERE socio_id = 13740 AND tipo_cuota = 'MUTUTCUO0010'

DELETE FROM orden_descuento_cuotas WHERE periodo = '200909' AND tipo_cuota = 'MUTUTCUO0010'
AND id NOT IN (SELECT orden_descuento_cuota_id FROM liquidacion_cuotas WHERE mutual_adicional_pendiente_id <> 0
AND liquidacion_id = 30) AND id NOT IN (SELECT orden_descuento_cuota_id FROM orden_descuento_cobro_cuotas)


SELECT * FROM orden_descuento_cobro_cuotas WHERE orden_descuento_cuota_id 
IN (SELECT orden_descuento_cuota_id FROM liquidacion_cuotas WHERE mutual_adicional_pendiente_id <> 0
AND liquidacion_id = 30)

SELECT * FROM liquidacion_socios WHERE liquidacion_id = 30 AND orden_descuento_cobro_id = 39557
SELECT * FROM orden_descuento_cobros WHERE id = 39557

SELECT * FROM orden_descuento_cobro_cuotas WHERE orden_descuento_cobro_id = 39557

SELECT * FROM liquidacion_socios WHERE liquidacion_id = 30 AND orden_descuento_cobro_id <> 0

SELECT * FROM liquidacion_cuotas WHERE liquidacion_id = 30 AND mutual_adicional_pendiente_id <> 0

SELECT * FROM orden_descuento_cobro_cuotas WHERE orden_descuento_cuota_id IN
(SELECT  id FROM orden_descuento_cuotas WHERE periodo = '200909' AND tipo_cuota = 'MUTUTCUO0010')

SELECT * FROM orden_descuento_cobros WHERE id = 32859


SELECT * FROM liquidacion_socios WHERE liquidacion_id = 30 AND orden_descuento_cobro_id = 0

SELECT * FROM mutual_adicional_pendientes WHERE socio_id = 5375

DELETE FROM socio_calificaciones

UPDATE liquidacion_socios SET socio_calificacion_id = 0 WHERE liquidacion_id = 30