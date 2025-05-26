insert into permisos(id,descripcion,url,`order`,parent)
values(204,'Solicitudes Anuladas por Persona','/mutual/mutual_producto_solicitudes/anuladas_by_persona',207,200);
insert into permisos(id,descripcion,url,`order`,parent)
values(205,'Reactivar Solicitud Anulada','/mutual/mutual_producto_solicitudes/reactivar',205,200);
insert into grupos_permisos values(1,204);
insert into grupos_permisos values(1,205);

