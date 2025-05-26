<?php

/**
*
* proveedor_plan_grilla_cuota.php
* @author adrian [* 20/12/2012]
*/

class ProveedorPlanGrillaCuota extends ProveedoresAppModel{
	
    var $name = 'ProveedorPlanGrillaCuota';
    var $belongsTo = array('ProveedorPlanGrilla');
    
    function get_opciones($grilla_id,$field='capital'){
        $sql = "select {$field} from proveedor_plan_grilla_cuotas as ProveedorPlanGrillaCuota
                where proveedor_plan_grilla_id = $grilla_id
                group by {$field} order by {$field};";
        $values = $this->query($sql);
        $values = Set::extract("/ProveedorPlanGrillaCuota/{$field}",$values);
        $values = array_combine($values,$values);
        return $values;
    }


    
	
}

?>