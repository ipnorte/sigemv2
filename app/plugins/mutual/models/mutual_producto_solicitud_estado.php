<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class MutualProductoSolicitudEstado extends MutualAppModel{
    
	var $name = 'MutualProductoSolicitudEstado';
	var $belongsTo = array('MutualProductoSolicitud');   
    
    function getEstadosBySolicitud($nro_solicitud){
        
        $sql = "select MutualProductoSolicitudEstado.*,
                EstadoSolicitud.concepto_1,EstadoSolicitud.concepto_2 from mutual_producto_solicitud_estados
                MutualProductoSolicitudEstado
                inner join global_datos EstadoSolicitud on (EstadoSolicitud.id = MutualProductoSolicitudEstado.estado)
                where
                MutualProductoSolicitudEstado.mutual_producto_solicitud_id = $nro_solicitud
                order by MutualProductoSolicitudEstado.created;";
        $datos = $this->query($sql);
        if(!empty($datos)){
            foreach ($datos as $i => $dato){
                $datos[$i]['MutualProductoSolicitudEstado']['estado_desc'] = $dato['EstadoSolicitud']['concepto_1'];
            }
        }
        return $datos;
        
    }
    
}

?>