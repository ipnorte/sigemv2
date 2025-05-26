<?php

/**
 *
 * ProveedorGrilla.php
 * @author adrian [* 14/12/2012]
 */
class ProveedorPlan extends ProveedoresAppModel {

    var $name = 'ProveedorPlan';
    var $actsAs = array('ExtendAssociations');
    var $hasMany = array('ProveedorPlanOrganismo', 'ProveedorPlanAnexo', 'ProveedorPlanDocumento');

//    var $hasAndBelongsToMany = array(
//        'CodigoOrganismo' => array(
//            'className' => 'GlobalDato',
//            'joinTable' => 'proveedor_plan_organismos',
//            'foreignKey' => 'proveedor_plan_id',
//            'associationForeignKey' => 'codigo_organismo',
//            'with' => 'ProveedorPlanOrganismo',
//    		'unique' => true,
//        ),
//      
//    );  	


    function getPlan($id) {
        $this->bindModel(array('belongsTo' => array('Proveedor')));
        $plan = $this->read(null, $id);
        return $plan;
    }

    function arma_datos($plan) {
        $plan['ProveedorPlan']['razon_social'] = $plan['Proveedor']['razon_social'];
        if (isset($plan['ProveedorPlanOrganismo']['codigo_organismo'])) {
            $plan['ProveedorPlan']['codigo_organismo'] = $plan['ProveedorPlanOrganismo']['codigo_organismo'];
            $plan['ProveedorPlan']['organismo'] = parent::GlobalDato("concepto_1", $plan['ProveedorPlanOrganismo']['codigo_organismo']);
        }

        if (isset($plan['ProveedorPlanDocumento']['codigo_documento'])) {
            $plan['ProveedorPlan']['codigo_documento'] = $plan['ProveedorPlanDocumento']['codigo_documento'];
            $plan['ProveedorPlan']['documento'] = parent::GlobalDato("concepto_1", $plan['ProveedorPlanDocumento']['codigo_documento']);
        }

        $plan['ProveedorPlan']['tipo_producto'] = $plan['ProveedorPlan']['tipo_producto'];
        $plan['ProveedorPlan']['producto'] = parent::GlobalDato("concepto_1", $plan['ProveedorPlan']['tipo_producto']);
        $plan['ProveedorPlan']['cadena'] = "#" . $plan['ProveedorPlan']['id'] . " | " . $plan['ProveedorPlan']['razon_social'] . " - " . $plan['ProveedorPlan']['producto'] . " *** " . $plan['ProveedorPlan']['descripcion'] . " ***";

        return $plan;
    }

    function getPlanesByProveedor($proveedor_id) {
        $planes = $this->find('all', array('conditions' => array('ProveedorPlan.proveedor_id' => $proveedor_id)));
        return $planes;
    }

    function guardar($datos) {

        $ERROR = false;
        
    //    debug($datos);
    //    exit;

        if (empty($datos['ProveedorPlan']['id']) && empty($datos['ProveedorPlan']['descripcion'])) {
            parent::notificar("DEBE INDICARSE UNA DESCRIPCION PARA EL PLAN");
            return false;
        }


        $plan = array();
        $plan['ProveedorPlan']['id'] = (isset($datos['ProveedorPlan']['id']) && !empty($datos['ProveedorPlan']['id']) ? $datos['ProveedorPlan']['id'] : 0);
        $plan['ProveedorPlan']['proveedor_id'] = $datos['ProveedorPlan']['proveedor_id'];
        $plan['ProveedorPlan']['ayuda_economica'] = (isset($datos['ProveedorPlan']['ayuda_economica']) ? $datos['ProveedorPlan']['ayuda_economica'] : FALSE);
        if (empty($datos['ProveedorPlan']['id'])){$plan['ProveedorPlan']['descripcion'] = $datos['ProveedorPlan']['descripcion'];}
            
        if (empty($datos['ProveedorPlan']['id'])){$plan['ProveedorPlan']['tipo_producto'] = $datos['ProveedorPlan']['tipo_producto'];}
            
        $plan['ProveedorPlan']['metodo_calculo'] = $datos['ProveedorPlan']['metodo_calculo'];
        $plan['ProveedorPlan']['tna'] = $datos['ProveedorPlan']['tna'];
        $plan['ProveedorPlan']['iva'] = $datos['ProveedorPlan']['iva'];
        $plan['ProveedorPlan']['tipo_cuota_gasto_admin'] = (empty($datos['ProveedorPlan']['tipo_cuota_gasto_admin']) ? NULL : $datos['ProveedorPlan']['tipo_cuota_gasto_admin']);
        $plan['ProveedorPlan']['gasto_admin'] = $datos['ProveedorPlan']['gasto_admin'];
        $plan['ProveedorPlan']['gasto_admin_base_calculo'] = $datos['ProveedorPlan']['gasto_admin_base_calculo'];
        $plan['ProveedorPlan']['tipo_cuota_sellado'] = (empty($datos['ProveedorPlan']['tipo_cuota_sellado']) ? NULL : $datos['ProveedorPlan']['tipo_cuota_sellado']);
        $plan['ProveedorPlan']['sellado'] = $datos['ProveedorPlan']['sellado'];
        $plan['ProveedorPlan']['sellado_base_calculo'] = $datos['ProveedorPlan']['sellado_base_calculo'];
        $plan['ProveedorPlan']['interes_moratorio'] = $datos['ProveedorPlan']['interes_moratorio'];
        $plan['ProveedorPlan']['costo_cancelacion_anticipada'] = $datos['ProveedorPlan']['costo_cancelacion_anticipada'];

        #CARGAR EL TEMPLATE
        $plan['ProveedorPlan']['modelo_solicitud_codigo'] = $datos['ProveedorPlan']['modelo_solicitud_codigo'];
        $plan['ProveedorPlan']['modelo_solicitud'] = trim(parent::GlobalDato("concepto_2", $datos['ProveedorPlan']['modelo_solicitud_codigo']));
        if (empty($plan['ProveedorPlan']['modelo_solicitud'])){$plan['ProveedorPlan']['modelo_solicitud'] = 'imprimir_credito_mutual_pdf';}
            
//            debug($plan);
//            debug($datos);
//            exit;		
//		parent::begin();


        if (!$this->save($plan) && !$ERROR) {
            parent::notificar("ERROR AL GRABAR EL PLAN");
//			$ERROR = true;
            return false;
        }

        $planID = (isset($datos['ProveedorPlan']['id']) && !empty($datos['ProveedorPlan']['id']) ? $datos['ProveedorPlan']['id'] : $this->getLastInsertID());
        $this->query("DELETE FROM proveedor_plan_organismos WHERE proveedor_plan_id = $planID;");
        if (!empty($datos['ProveedorPlan']['organismos'])) {

            App::import('Model', 'proveedores.ProveedorPlanOrganismo');
            $oPLANORG = new ProveedorPlanOrganismo();
            $organismos = array();
            foreach ($datos['ProveedorPlan']['organismos'] as $organismo) {
//                $data = array('ProveedorPlanOrganismo' => array('id' => 0, 'proveedor_plan_id' => $planID, 'codigo_organismo' =>$organismo));
//                $oPLANORG->id = 0;
//                $oPLANORG->save($data);
                $oPLANORG->query("INSERT INTO proveedor_plan_organismos(proveedor_plan_id,codigo_organismo)VALUES($planID,'$organismo');");
            }
        } else {
            parent::notificar("DEBE INDICARSE AL MENOS UN ORGANISMO");
//			$ERROR = true;
            return false;
        }

        $this->query("DELETE FROM proveedor_plan_anexos WHERE proveedor_plan_id = $planID;");
        if (!empty($datos['ProveedorPlan']['anexos'])) {

            App::import('Model', 'proveedores.ProveedorPlanAnexo');
            $oPLANANEXO = new ProveedorPlanAnexo();
            foreach ($datos['ProveedorPlan']['anexos'] as $anexo) {
                $oPLANANEXO->query("INSERT INTO proveedor_plan_anexos(proveedor_plan_id,codigo_anexo)VALUES($planID,'$anexo');");
            }
//                    exit;
        }
        $this->query("DELETE FROM proveedor_plan_documentos WHERE proveedor_plan_id = $planID;");
        if (!empty($datos['ProveedorPlan']['documentos'])) {

            App::import('Model', 'proveedores.ProveedorPlanDocumento');
            $oPLANORG = new ProveedorPlanOrganismo();
            $organismos = array();
            foreach ($datos['ProveedorPlan']['documentos'] as $documento) {
//                $data = array('ProveedorPlanOrganismo' => array('id' => 0, 'proveedor_plan_id' => $planID, 'codigo_organismo' =>$organismo));
//                $oPLANORG->id = 0;
//                $oPLANORG->save($data);
                $oPLANORG->query("INSERT INTO proveedor_plan_documentos(proveedor_plan_id,codigo_documento)VALUES($planID,'$documento');");
            }
//        } else {
//            parent::notificar("DEBE INDICARSE AL MENOS UN ORGANISMO");
////			$ERROR = true;
//            return false;
        }

        return true;
    }

    function borrarPlan($id) {

        $sql = "SELECT COUNT(*) AS cant FROM proveedor_planes pp, mutual_producto_solicitudes so
				WHERE pp.id = so.proveedor_plan_id
				AND pp.id = $id";
        $datos = $this->query($sql);
        if ($datos[0][0]['cant'] != 0){return false;}
            
        $this->query("DELETE FROM proveedor_plan_organismos WHERE proveedor_plan_id = $id;");
        $this->query("DELETE FROM proveedor_plan_anexos WHERE proveedor_plan_id = $id;");
        $this->query("DELETE FROM proveedor_plan_grilla_cuotas WHERE proveedor_plan_grilla_id IN (SELECT id FROM proveedor_plan_grillas WHERE proveedor_plan_id = $id);");
        $this->query("DELETE FROM proveedor_plan_grillas WHERE proveedor_plan_id = $id;");
        $this->query("DELETE FROM proveedor_planes WHERE id = $id;");
        $this->query("DELETE FROM proveedor_plan_documentos WHERE proveedor_plan_id = $id;");
        return true;
    }

    function getPlanesVigentes($codigo_organismo = null, $date = null, $noReasignable = false, $ayudaEconomica = false,$proveedorId = NULL,$tipoProducto = null,$agruparPorPlan=FALSE) {

        App::import('model', 'seguridad.Usuario');
        $oUSER = new Usuario();
        $vendedorId = $oUSER->get_vendedorId_logon();

        if (empty($date)){$date = date('Y-m-d');}
            
        $planes = array();
        $sql = "SELECT Proveedor.razon_social,
				ProveedorPlanOrganismo.codigo_organismo,
                                ProveedorPlan.* 
                                -- ,ProveedorPlanDocumento.codigo_documento
                                FROM proveedor_planes AS ProveedorPlan
				INNER JOIN proveedores AS Proveedor ON (Proveedor.id = ProveedorPlan.proveedor_id)
				INNER JOIN proveedor_plan_organismos AS ProveedorPlanOrganismo ON (ProveedorPlanOrganismo.proveedor_plan_id = ProveedorPlan.id)
                                -- LEFT JOIN proveedor_plan_documentos AS ProveedorPlanDocumento ON (ProveedorPlanDocumento.proveedor_plan_id = ProveedorPlan.id)
                                " . (!empty($vendedorId) ? " INNER JOIN vendedor_proveedor_planes as VendedorProveedorPlanes on (VendedorProveedorPlanes.proveedor_plan_id = ProveedorPlan.id) " : " ") . "
				WHERE ProveedorPlan.activo = 1 
                ".(!empty($proveedorId) ? " and ProveedorPlan.proveedor_id = $proveedorId": "")."
                ".(!empty($tipoProducto) ? " and ProveedorPlan.tipo_producto = '$tipoProducto'": "")."
                                " . (!empty($vendedorId) ? " AND  VendedorProveedorPlanes.vendedor_id = $vendedorId " : " ") . "
				" . ($noReasignable ? " AND Proveedor.reasignable = 1" : "") . "
                                " . ($ayudaEconomica ? " AND ProveedorPlan.ayuda_economica = 1" : " AND ProveedorPlan.ayuda_economica = 0 ") . "    
				" . (!empty($codigo_organismo) ? " AND ProveedorPlanOrganismo.codigo_organismo = '$codigo_organismo'" : "") . "
				AND ProveedorPlan.id IN (SELECT proveedor_plan_id FROM proveedor_plan_grillas
				WHERE vigencia_desde <= '$date')
				". ($agruparPorPlan ? " GROUP BY ProveedorPlan.id " : "") . " ORDER BY Proveedor.razon_social ;";
        
        $datos = $this->query($sql);
        if (empty($datos)){return $planes;}
            
        foreach ($datos as $ids => $dato) {
            $dato['ProveedorPlan']['razon_social'] = $dato['Proveedor']['razon_social'];
            $dato['ProveedorPlan']['codigo_organismo'] = $dato['ProveedorPlanOrganismo']['codigo_organismo'];
            $dato['ProveedorPlan']['organismo'] = parent::GlobalDato("concepto_1", $dato['ProveedorPlanOrganismo']['codigo_organismo']);
            $dato['ProveedorPlan']['tipo_producto'] = $dato['ProveedorPlan']['tipo_producto'];
            $dato['ProveedorPlan']['producto'] = parent::GlobalDato("concepto_1", $dato['ProveedorPlan']['tipo_producto']);
            if($agruparPorPlan){
                $dato['ProveedorPlan']['cadena'] = "#" . $dato['ProveedorPlan']['id'] . " | " . $dato['ProveedorPlan']['razon_social'] . " - " . $dato['ProveedorPlan']['producto'] . " *** " . $dato['ProveedorPlan']['descripcion'] . " ***";
            }else{
                $dato['ProveedorPlan']['cadena'] = "#" . $dato['ProveedorPlan']['id'] . " - " . $dato['ProveedorPlan']['organismo'] . " | " . $dato['ProveedorPlan']['razon_social'] . " - " . $dato['ProveedorPlan']['producto'] . " *** " . $dato['ProveedorPlan']['descripcion'] . " ***";
            }
            
            $planes[$ids]['ProveedorPlan'] = $dato['ProveedorPlan'];
        }
        return $planes;
    }

    function getPlanesVigentesTodos($date = null, $noReasignable = false, $soloActivos = true) {
        if (empty($date)){$date = date('Y-m-d');}
            
        $planes = array();
        $sql = "SELECT Proveedor.razon_social,
				ProveedorPlan.* FROM proveedor_planes AS ProveedorPlan
				INNER JOIN proveedores AS Proveedor ON (Proveedor.id = ProveedorPlan.proveedor_id)
				WHERE " . ($soloActivos ? "ProveedorPlan.activo = 1 " : " 1 = 1 ") . "
				" . ($noReasignable ? " AND Proveedor.reasignable = 1" : "") . "
					AND ProveedorPlan.id IN (SELECT proveedor_plan_id FROM proveedor_plan_grillas
					WHERE vigencia_desde <= '$date')
					ORDER BY Proveedor.razon_social,ProveedorPlan.descripcion;";
        $datos = $this->query($sql);
        if (empty($datos)){return $planes;}
            
        foreach ($datos as $ids => $dato) {
            $dato['ProveedorPlan']['razon_social'] = $dato['Proveedor']['razon_social'];
            $dato['ProveedorPlan']['tipo_producto'] = $dato['ProveedorPlan']['tipo_producto'];
            $dato['ProveedorPlan']['producto'] = parent::GlobalDato("concepto_1", $dato['ProveedorPlan']['tipo_producto']);
            $dato['ProveedorPlan']['cadena'] = "#" . $dato['ProveedorPlan']['id'] . " | " . $dato['ProveedorPlan']['razon_social'] . " - " . $dato['ProveedorPlan']['producto'] . " *** " . $dato['ProveedorPlan']['descripcion'] . " ***";
            if ($dato['ProveedorPlan']['activo'] == 0){$dato['ProveedorPlan']['cadena'] .= " (NO VIGENTE)";}
                
            $planes[$ids]['ProveedorPlan'] = $dato['ProveedorPlan'];
        }
        return $planes;
    }

    function getMontosLiquidosByPlan($plan_id) {

        $this->unbindModel(array('hasMany' => array('ProveedorPlanOrganismo', 'ProveedorPlanAnexo')));
        $plan = $this->read(null, $plan_id);

        $disponible = $this->query("SELECT FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE(" . $plan['ProveedorPlan']['proveedor_id'] . ") as FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE");
        if (isset($disponible[0][0]['FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE'])){$disponible = $disponible[0][0]['FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE'];}            
        else {$disponible = 99999999;}
            

        $sql = "SELECT ProveedorPlanGrillaCuota.id,ProveedorPlanGrillaCuota.proveedor_plan_grilla_id,ProveedorPlanGrillaCuota.liquido FROM proveedor_plan_grilla_cuotas AS ProveedorPlanGrillaCuota
            inner join proveedor_plan_grillas grilla on (grilla.id = ProveedorPlanGrillaCuota.proveedor_plan_grilla_id)
            inner join proveedor_planes plan on (plan.id = grilla.proveedor_plan_id)
                            WHERE ProveedorPlanGrillaCuota.proveedor_plan_grilla_id =
                            (SELECT id FROM proveedor_plan_grillas AS ProveedorPlanGrilla
                            WHERE ProveedorPlanGrilla.proveedor_plan_id = $plan_id AND ProveedorPlanGrilla.vigencia_desde <= '" . date('Y-m-d') . "'
                            ORDER BY ProveedorPlanGrilla.vigencia_desde DESC
                            LIMIT 1) 
                            and ProveedorPlanGrillaCuota.liquido <= $disponible
                            GROUP BY liquido
                            ORDER BY liquido;";
        $datos = $this->query($sql);
        return $datos;
    }

    function getCuotasByPlan($plan_id, $monto_id) {
        $sql = "SELECT ProveedorPlanGrillaCuota.id,
                        ProveedorPlanGrillaCuota.cuotas,
                        ProveedorPlanGrillaCuota.importe,
                        ProveedorPlanGrillaCuota.cft,
                        ProveedorPlanGrilla.tna,
                        ProveedorPlanGrilla.tem 
                        FROM proveedor_plan_grilla_cuotas AS ProveedorPlanGrillaCuota
                        inner join proveedor_plan_grillas ProveedorPlanGrilla on ProveedorPlanGrilla.id =  ProveedorPlanGrillaCuota.proveedor_plan_grilla_id
				WHERE ProveedorPlanGrillaCuota.proveedor_plan_grilla_id =
				(SELECT id FROM proveedor_plan_grillas AS ProveedorPlanGrilla
				WHERE ProveedorPlanGrilla.proveedor_plan_id = $plan_id AND ProveedorPlanGrilla.vigencia_desde <= '" . date('Y-m-d') . "'
				ORDER BY ProveedorPlanGrilla.vigencia_desde DESC
				LIMIT 1)
				AND ProveedorPlanGrillaCuota.liquido = (select liquido from proveedor_plan_grilla_cuotas AS ProveedorPlanGrillaCuota2
				WHERE ProveedorPlanGrillaCuota2.id = $monto_id)
				GROUP BY cuotas ORDER BY cuotas;";
        $datos = $this->query($sql);
        return $datos;
    }

    function get_anexos_print($planId) {
        $sql = "select GlobalDato.id,GlobalDato.concepto_2 
                    from proveedor_plan_anexos ProveedorPlanAnexo
                    inner join global_datos as GlobalDato on (GlobalDato.id = ProveedorPlanAnexo.codigo_anexo) 
                    where proveedor_plan_id = $planId;";
        $result = $this->query($sql);
        $datos = Set::extract("/GlobalDato/concepto_2", $result);
        return $datos;
    }

    function get_modelo_print($planId) {
        $sql = "select GlobalDato.id,GlobalDato.concepto_2 
                    from proveedor_planes ProveedorPlan
                    inner join global_datos as GlobalDato on (GlobalDato.id = ProveedorPlan.modelo_solicitud_codigo) 
                    where ProveedorPlan.id = $planId;";
        $result = $this->query($sql);
        $datos = Set::extract("/GlobalDato/concepto_2", $result);
        if (!empty($datos) && isset($datos[0])){return $datos[0];}
        else {return "imprimir_credito_mutual_pdf";}
            
    }

    public function get_tasas($cuotaId) {
        if(empty($cuotaId)) {return null;}
    }

}

?>