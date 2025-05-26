
/*****************************************************************
ANULACION DE ORDEN DE DESCUENTO
******************************************************************/
select * from permisos where id > 199;
select * from grupos_permisos where permiso_id > 199;
select * from permisos where url = '/mutual/orden_descuentos/anular_orden/'

INSERT INTO permisos (id,descripcion,url,`order`,main,`quick`,icon,activo,parent)
VALUES(203,'Anular Orden','/mutual/orden_descuentos/anular_orden',203,1,0,'arrow_right2.gif',1,200);
INSERT INTO grupos_permisos VALUES(1,203);
