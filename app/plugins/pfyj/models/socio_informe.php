<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of socio_informe
 *
 * @author adrian
 */
class SocioInforme extends PfyjAppModel {
    
    var $name = 'SocioInforme';
    var $belongsTo = array('Socio','SocioInformeLote');
    
    
    function getPendientes($empresa = NULL,$periodo_corte=NULL){
        $this->recursive = 3;
        $this->Socio->bindModel(array('belongsTo' => array('Persona')));
        $conditions = array();
        if(!empty($empresa)) $conditions['SocioInforme.empresa'] = $empresa;
        if(!empty($periodo_corte)) $conditions['SocioInforme.periodo_hasta'] = $periodo_corte;
        $conditions['SocioInforme.socio_informe_lote_id'] = NULL;
        $informes = $this->find('all',array('conditions' => $conditions));
        return $informes;
    }
    
    function get($id){
        $this->recursive = 3;
        $this->Socio->bindModel(array('belongsTo' => array('Persona')));
        $this->Socio->Persona->bindModel(array('belongsTo' => array('Provincia')));
        $informe = $this->read(null,$id);
        return $informe;
    }
    
}
