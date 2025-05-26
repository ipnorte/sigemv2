<?php

/*
 * INSERT INTO `credifep_sigemdb`.`permisos` (`id`, `descripcion`, `url`, `order`, `main`, `icon`) VALUES ('900', 'Facturacion', '/facturacion', '900', '1', 'profiler.gif');
 * INSERT INTO `credifep_sigemdb`.`grupos_permisos` (`grupo_id`, `permiso_id`) VALUES ('1', '900');
 
 */


class FacturacionAppController extends AppController{
    
    var $autorizar = array();
    
    function beforeFilter(){
        $this->Seguridad->allow($this->autorizar);
        parent::beforeFilter();
    }
    
}

?>