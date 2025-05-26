/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adrian
 * Created: 30/01/2020
 */

insert into banco_rendicion_codigos(banco_id,codigo,descripcion,indica_pago,calificacion_socio)
values ('00299', '070', 'REVERSION ACEPTADA', 0, 'MUTUCALIMORO');
update liquidacion_socio_rendiciones
set `status` = '070'
where banco_intercambio = '00299' and `status` = 'ERR'
and left(registro,3) = '070';