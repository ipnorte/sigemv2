/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 29/05/2019
 */

CREATE  OR REPLACE VIEW `v_socios` AS
select 
p.documento
,p.apellido
,p.nombre
,p.fecha_nacimiento
,if(p.fecha_fallecimiento = '0000-00-00',NULL,p.fecha_fallecimiento) as fecha_fallecimiento
,p.localidad
,pr.nombre as provincia
,s.id as socio_id
,s.categoria
,g2.concepto_1 as categoria_descripcion
,s.fecha_alta
,s.activo
,if(s.fecha_baja = '0000-00-00',NULL,s.fecha_baja) AS fecha_baja
,s.codigo_baja
,g3.concepto_1 as codigo_baja_descripcion
from personas p 
INNER JOIN socios s on p.id = s.persona_id 
left join provincias pr on pr.id = p.provincia_id
INNER JOIN global_datos g1 on p.tipo_documento = g1.id
INNER JOIN global_datos g2 on s.categoria = g2.id
LEFT JOIN global_datos g3 on s.codigo_baja = g3.id;
