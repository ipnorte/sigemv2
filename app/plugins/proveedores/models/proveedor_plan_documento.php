<?php

/**
 *
 * proveedor_plan_organismos.php
 * @author adrian [* 18/12/2012]
 */
class ProveedorPlanDocumento extends ProveedoresAppModel {

    var $name = 'ProveedorPlanDocumento';
    var $primaryKey = "proveedor_plan_id";
    var $belongsTo = array(
        'ProveedorPlan' => array('className' => 'ProveedorPlan',
            'foreignKey' => 'proveedor_plan_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'GlobalDato' => array('className' => 'GlobalDato',
            'foreignKey' => 'codigo_documento',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    function getDocumentosByPlan($id_plan,$limit=10) {
        if (empty($id_plan)) {
            return null;
        } else {
            $planes = $this->find('all', array('conditions' => array('ProveedorPlanDocumento.proveedor_plan_id' => $id_plan),'limit' => $limit));
            return $planes;
        }
    }

}

?>