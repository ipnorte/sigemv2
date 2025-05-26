<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ProveedorPrioridadImputaOrganismo extends ProveedoresAppModel{
  
    var $name = 'ProveedorPrioridadImputaOrganismo';
    
    function get_by_proveedor($proveedor_id, $organismo = NULL){
        $conditions = array();
        $conditions['ProveedorPrioridadImputaOrganismo']['id'] = $proveedor_id;
        if(!empty($organismo)) $conditions['ProveedorPrioridadImputaOrganismo']['codigo_organismo'] = $proveedor_id;
        $datos = $this->find('all', $conditions);
        return $datos;
    }
    
    
}

?>