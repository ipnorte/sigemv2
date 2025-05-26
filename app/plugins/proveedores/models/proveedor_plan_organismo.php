<?php

/**
*
* proveedor_plan_organismos.php
* @author adrian [* 18/12/2012]
*/

class ProveedorPlanOrganismo extends ProveedoresAppModel{
	
	var $name = 'ProveedorPlanOrganismo';
	var $primaryKey = "proveedor_plan_id";
	
	var $belongsTo = array(
			'ProveedorPlan' => array('className' => 'ProveedorPlan',
								'foreignKey' => 'proveedor_plan_id',
								'conditions' => '',
								'fields' => '',
								'order' => ''
			),
			'GlobalDato' => array('className' => 'GlobalDato',
								'foreignKey' => 'codigo_organismo',
								'conditions' => '',
								'fields' => '',
								'order' => ''
			)
	);	

	
	
}

?>