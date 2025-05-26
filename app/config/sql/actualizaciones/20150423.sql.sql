
insert into permisos (id,descripcion,url,`order`,activo,parent)
values (439,'Calcular Grilla Cuotas','/proveedores/proveedor_planes/calcular_grilla',439,1,400);
insert into grupos_permisos(grupo_id,permiso_id) values(1,439);
