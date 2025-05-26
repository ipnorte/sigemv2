/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 03/10/2018
 */

alter table socio_calificaciones add column prioritaria boolean default false after calificacion;


/**
* MARCA COMO PRIORITARIAS A LAS CALIFICACIONES CARGADAS POR UN USUARIO
*/
update socio_calificaciones
set prioritaria = TRUE
where ifnull(user_created,'') <> ''
and prioritaria = FALSE;