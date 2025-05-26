/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 27/05/2016
 */

INSERT  INTO `permisos`(`id`,`descripcion`,`url`,`order`,`main`,`quick`,`icon`,`activo`,`parent`,`obs`,`created`,`modified`) 
VALUES (226,'Consultar Solicitud','/mutual/mutual_producto_solicitudes/consulta',226,1,0
,'arrow_right2.gif',1,200,NULL,NULL,NULL);
-- VER EL ID DEL PERMISO UTILIZADO EN EL INSERT ANTERIOR
INSERT INTO grupos_permisos(grupo_id,permiso_id) VALUES(1,226);


INSERT  INTO `permisos`(`id`,`descripcion`,`url`,`order`,`main`,`quick`,`icon`,`activo`,`parent`,`obs`,`created`,`modified`) 
VALUES (227,'Adjuntar Documentos Solicitud','/mutual/mutual_producto_solicitudes/adjuntar_documentacion',227,1,0
,null,0,200,NULL,NULL,NULL);
-- VER EL ID DEL PERMISO UTILIZADO EN EL INSERT ANTERIOR
INSERT INTO grupos_permisos(grupo_id,permiso_id) VALUES(1,227);