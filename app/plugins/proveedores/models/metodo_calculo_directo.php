<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of metodo_calculo_directo
 *
 * @author adrian
 */

require_once 'metodo_calculo_cuota.php';

class MetodoCalculoDirecto extends MetodoCalculoCuota{

    var $name = 'MetodoCalculoDirecto';
    var $useTable = false;
    
            
    function MetodoCalculoDirecto(){
        parent::__construct();
//        $this->cuota['SELLADO'] = 0;
//        $this->cuota['GASTO_ADMIN'] = 0;
    }

    
//    function armar_plan($datos){
//        $this->porcAdic = $datos['ProveedorPlanGrilla']['gasto_admin_porc'];
//        $this->porcSello = $datos['ProveedorPlanGrilla']['sellado_porc'];
//        $this->porcIVA = $datos['ProveedorPlanGrilla']['iva_porc'];
//        $this->TNA =$datos['ProveedorPlanGrilla']['tna'];
//        $this->TEM = $this->tna_to_tem($datos['ProveedorPlanGrilla']['tna']);
//        
//        parent::arma_opciones(
//                $datos['ProveedorPlanGrilla']['capital_minimo'],
//                $datos['ProveedorPlanGrilla']['capital_maximo'],
//                $datos['ProveedorPlanGrilla']['capital_incremento'],
//                explode(',', $datos['ProveedorPlanGrilla']['cuotas_disponibles'])
//        );
//        
//        debug($this);
//        exit;
//        
//    }
        
}
