/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 08/08/2017
 */

insert into permisos(id,descripcion,url,`order`,activo,parent)
values(322,'Imputar Archivo puntual','/mutual/liquidacion_socio_rendiciones/imputar_archivo',322,1,300);
insert into grupos_permisos (grupo_id,permiso_id) values(1,322);


ALTER TABLE `liquidacion_intercambios` ADD COLUMN `imputado` TINYINT(1) NULL DEFAULT 0 AFTER `procesado`;
