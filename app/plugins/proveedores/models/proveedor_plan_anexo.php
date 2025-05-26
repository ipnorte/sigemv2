<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of proveedor_plan_anexo
 *
 * @author adrian
 */
class ProveedorPlanAnexo extends ProveedoresAppModel {
    
    
    var $name  = 'ProveedorPlanAnexo';
    var $belongsTo = array('ProveedorPlan');
    
    
}
