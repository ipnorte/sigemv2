<?php

/**
 *
 * proveedor_planes_controller.php
 * @author adrian [* 20/12/2012]
 */
class ProveedorPlanesController extends ProveedoresAppController {

    var $name = 'ProveedorPlanes';
    var $uses = array('proveedores.Proveedor', 'proveedores.ProveedorPlan');
    var $autorizar = array('get_planes_vigentes', 'grilla_planes_vigentes', 'combo_planes_vigentes', 'get_todos_planes_vigentes', 'documentos_plan','get_cuota_grilla','get_planes_vigentes_proveedor_organismo');

    function beforeFilter() {
        $this->Seguridad->allow($this->autorizar);
        parent::beforeFilter();
    }

    function index($id = null) {

        if (empty($id))
            parent::noDisponible();

        App::import('Model', 'proveedores.ProveedorPlan');
        $oPLAN = new ProveedorPlan();
        $this->set('planes', $oPLAN->getPlanesByProveedor($id));
        $this->set('proveedor', $this->Proveedor->read(null, $id));
    }

    function download_grilla($grilla_id) {
        App::import('Model', 'proveedores.ProveedorPlanGrilla');
        $oPLANGRILLA = new ProveedorPlanGrilla();
        $xls = $oPLANGRILLA->read('id,xls,cuotas,descripcion,vigencia_desde', $grilla_id);
        if(!empty($xls['ProveedorPlanGrilla']['xls'])){
            Configure::write('debug', 0);
            header("Content-type: application/vnd.ms-excel");
            $fileName = "plan_cuotas_" . $xls['ProveedorPlanGrilla']['id'] . ".xls";
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');
            echo $xls['ProveedorPlanGrilla']['xls'];
            exit;
        }
        $this->set('xls',$xls);
        $this->render('download_grilla_xls','blank');
    }

    function nueva_grilla($plan_id, $UID = null) {

// 		if(!empty($UID) && $this->Session->check($UID."_GRILLA")){
// 			$grilla = $this->Session->read($UID."_GRILLA");
// 			App::import('Model','proveedores.ProveedorPlanGrilla');
// 			$oPLANGRILLA = new ProveedorPlanGrilla();
// 			if($oPLANGRILLA->cargarGrilla($grilla)){
// 				$this->redirect('grillas/'.$grilla['ProveedorPlanGrilla']['proveedor_plan_id']);
// 			}else{
// 				$this->Mensaje->error("ERROR AL GENERAR EL PLAN");
// 			}
// 		}

        $UID = String::uuid();
        $cuotas = null;



        if (!empty($this->data)) {

//                    debug($this->data);
//                    exit;

            if (isset($this->data['Proveedor']['UID']) && !empty($this->data['Proveedor']['UID']) && $this->Session->check($this->data['Proveedor']['UID'] . "_GRILLA")) {

                $grilla = $this->Session->read($this->data['Proveedor']['UID'] . "_GRILLA");
                App::import('Model', 'proveedores.ProveedorPlanGrilla');
                $oPLANGRILLA = new ProveedorPlanGrilla();
//                                debug($this->data);
//                                exit;
//                debug($grilla);
//                exit;
                if ($oPLANGRILLA->cargarGrilla($grilla)) {
                    $this->redirect('grillas/' . $grilla['ProveedorPlanGrilla']['proveedor_plan_id']);
                } else {
                    $this->Mensaje->error("ERROR AL GENERAR EL PLAN");
                }
            }

            if ($this->Session->check($UID . "_GRILLA"))
                $this->Session->del($UID . "_GRILLA");
//			debug($this->data);	
            $this->data['ProveedorPlanGrilla']['cuotas'] = $this->getGrillaXLS_to_array($this->data['ProveedorPlanGrilla']['archivo_grilla']);

//            debug($this->data['ProveedorPlanGrilla']['cuotas']);
//            exit;

            if (!empty($this->data['ProveedorPlanGrilla']['cuotas'])) {

                App::import('model', 'proveedores.metodo_calculo_cuota');
                $oCALC = new MetodoCalculoCuota();

                $this->data['ProveedorPlanGrilla']['tem'] = $oCALC->tna_to_tem($this->data['ProveedorPlanGrilla']['tna']);
                $this->data['ProveedorPlanGrilla']['tem'] = $oCALC->tna_to_tem($this->data['ProveedorPlanGrilla']['tna']);
                $this->data['ProveedorPlanGrilla']['tnm'] = $oCALC->tna_to_tnm($this->data['ProveedorPlanGrilla']['tna']);
                $this->data['ProveedorPlanGrilla']['gasto_admin'] = (isset($this->data['ProveedorPlanGrilla']['gasto_admin']) ? $this->data['ProveedorPlanGrilla']['gasto_admin'] : 0);
                $this->data['ProveedorPlanGrilla']['sellado'] = (isset($this->data['ProveedorPlanGrilla']['sellado']) ? $this->data['ProveedorPlanGrilla']['sellado'] : 0);


                //copio el archivo
                $this->data['ProveedorPlanGrilla']['vigencia_desde'] = $this->Proveedor->armaFecha($this->data['ProveedorPlanGrilla']['vigencia_desde']);
                $this->data['ProveedorPlanGrilla']['UID'] = $UID;
                App::import('Core', 'File');
                $file = new File($this->data['ProveedorPlanGrilla']['archivo_grilla']['tmp_name'], false);
                $this->data['ProveedorPlanGrilla']['excel'] = $file->read();
                $this->Session->write($UID . "_GRILLA", $this->data);
            } else {

                $this->data = null;
                $this->Mensaje->error("ERROR AL SUBIR EL ARCHIVO AL SERVIDOR");
            }
        }


        App::import('Model', 'proveedores.ProveedorPlan');
        $oPLAN = new ProveedorPlan();
        $plan = $oPLAN->read(null, $plan_id);
        $this->set('proveedor', $this->Proveedor->read(null, $plan['ProveedorPlan']['proveedor_id']));
        $this->set('plan', $plan);

        $this->set('UID', $UID);
        $this->set('cuotas', $cuotas);
    }

    function borrar_plan($grilla_id) {
        App::import('Model', 'proveedores.ProveedorPlan');
        $oPLAN = new ProveedorPlan();
        $plan = $oPLAN->read('proveedor_id', $grilla_id);
        if (!$oPLAN->borrarPlan($grilla_id)) {
            $this->Mensaje->error("NO SE PUDO BORRAR EL PLAN YA QUE EXISTEN SOLICITUDES ASOCIADAS AL MISMO");
        }
        $this->redirect('index/' . $plan['ProveedorPlan']['proveedor_id']);
    }

    function editar_plan($plan_id) {

        App::import('Model', 'proveedores.ProveedorPlan');
        $oPLAN = new ProveedorPlan();

        if (!empty($this->data)) {
            
            if ($oPLAN->guardar($this->data)) {
                $this->redirect('index/' . $this->data['ProveedorPlan']['proveedor_id']);
            } else {
                $this->Mensaje->errores("ERRORES: ", $oPLAN->notificaciones);
            }
        }

        App::import('model', 'proveedores.metodo_calculo_cuota');
        $oCALC = new MetodoCalculoCuota();  
        $this->set('metodos', $oCALC->METODOS);
        $this->set('criterios', $oCALC->CRITERIO_APLICACION);              

        $plan = $oPLAN->read(null, $plan_id);
        $this->set('proveedor', $this->Proveedor->read(null, $plan['ProveedorPlan']['proveedor_id']));
        $this->set('plan', $plan);
    }

    function grillas($plan_id, $grilla_id = NULL) {
        App::import('Model', 'proveedores.ProveedorPlan');
        $oPLAN = new ProveedorPlan();
        $plan = $oPLAN->read(null, $plan_id);
        $this->set('proveedor', $this->Proveedor->read(null, $plan['ProveedorPlan']['proveedor_id']));
        $this->set('plan', $plan);
        App::import('Model', 'proveedores.ProveedorPlanGrilla');
        $oGRILLA = new ProveedorPlanGrilla();

        $this->set('grillas', $oGRILLA->getByPlan($plan_id));

        App::import('model', 'proveedores.metodo_calculo_cuota');
        $oCALC = new MetodoCalculoCuota(); 
        
        $this->set('metodos', $oCALC->METODOS);
        $this->set('criterios', $oCALC->CRITERIO_APLICACION);         

        if (!empty($grilla_id)) {

            App::import('Model', 'proveedores.ProveedorPlanGrillaCuota');
            $oGRILLACUOTA = new ProveedorPlanGrillaCuota();
            $capital_opts = $oGRILLACUOTA->get_opciones($grilla_id);
            $liquido_opts = $oGRILLACUOTA->get_opciones($grilla_id, 'liquido');
            $cuotas_opts = $oGRILLACUOTA->get_opciones($grilla_id, 'cuotas');
            $grilla = $oGRILLA->getById($grilla_id);
            $this->set('grilla', $grilla);
            $this->set('cuotas_opts', $cuotas_opts);
            $this->set('liquido_opts', $liquido_opts);

            $calculo = $liquidacion = NULL;
            if (!empty($this->data)) {
                $oCALC->tasa = $grilla['ProveedorPlanGrilla']['tnm'];
                $oCALC->METODO_CALCULO = $grilla['ProveedorPlanGrilla']['metodo_calculo'];
                $oCALC->cuotas = $this->data['ProveedorPlanGrillaCuota']['cuotas'];
                $oCALC->porcAdic = $grilla['ProveedorPlanGrilla']['gasto_admin'];
                $oCALC->porcSello = $grilla['ProveedorPlanGrilla']['sellado'];
                $oCALC->porcIVA = $grilla['ProveedorPlanGrilla']['iva'];
                $oCALC->TNA = $grilla['ProveedorPlanGrilla']['tna'];
                $oCALC->TNM = $grilla['ProveedorPlanGrilla']['tnm'];
                $oCALC->TEM = $grilla['ProveedorPlanGrilla']['tem'];
                $oCALC->TEA = $oCALC->tna_to_tea($grilla['ProveedorPlanGrilla']['tna']);
                $oCALC->solicitado = $this->data['ProveedorPlanGrillaCuota']['liquido'];

                $oCALC->tipoCuotaGAdmin = $grilla['ProveedorPlanGrilla']['tipo_cuota_gasto_admin'];
                $oCALC->baseCalculoGadmin = $grilla['ProveedorPlanGrilla']['gasto_admin_base_calculo'];
                $oCALC->tipoCuotaSellado = $grilla['ProveedorPlanGrilla']['tipo_cuota_sellado'];
                $oCALC->baseCalculoSellado = $grilla['ProveedorPlanGrilla']['sellado_base_calculo'];

                $oCALC->armar_plan();
                $calculo = $oCALC->plan;
                $liquidacion = $oCALC->liquidacion;
            }
            $this->set('calculo', $calculo);
            $this->set('liquidacion', $liquidacion);
            $this->render('grilla_test');
        }
    }

    function borrar_grilla($grilla_id = null) {
        if (empty($grilla_id))
            parent::noDisponible();
        App::import('Model', 'proveedores.ProveedorPlanGrilla');
        $oGRILLA = new ProveedorPlanGrilla();
        $grilla = $oGRILLA->read(null, $grilla_id);
        if (empty($grilla))
            parent::noDisponible();
        $oGRILLA->borrar($grilla_id);
        $this->redirect('grillas/' . $grilla['ProveedorPlanGrilla']['proveedor_plan_id']);
    }

    function nuevo_plan($proveedor_id) {
        $UID = null;
        if (!empty($this->data)) {
            App::import('Model', 'proveedores.ProveedorPlan');
            $oPLAN = new ProveedorPlan();
            if ($oPLAN->guardar($this->data)) {
                $this->redirect('index/' . $proveedor_id);
            } else {
                $this->Mensaje->errores("ERRORES: ", $oPLAN->notificaciones);
            }
        }
        $proveedor = $this->Proveedor->read(null, $proveedor_id);
        if (empty($proveedor)){parent::noDisponible();}
            

            App::import('model', 'proveedores.metodo_calculo_cuota');
            $oCALC = new MetodoCalculoCuota();  
            $this->set('metodos', $oCALC->METODOS);
            $this->set('criterios', $oCALC->CRITERIO_APLICACION);              

        $this->set('proveedor', $proveedor);
        $this->set('UID', $UID);
    }

    function desactivar_plan($campo, $id, $option) {
        Configure::write('debug', 0);
        App::import('Model', 'proveedores.ProveedorPlan');
        $oPLAN = new ProveedorPlan();
        $oPLAN->unbindModel(array('hasAndBelongsToMany' => 'CodigoOrganismo'));
        $plan = $oPLAN->read(null, $id);
        if (parent::isAuthorized()) {
            $plan['ProveedorPlan']['activo'] = $option;
            $oPLAN->save($plan);
            echo $option;
        } else {
            echo $plan['ProveedorPlan']['activo'];
        }
        exit;
    }

    function get_todos_planes_vigentes($tipo_producto, $fecha) {
        App::import('Model', 'proveedores.ProveedorPlan');
        $oPLAN = new ProveedorPlan();
        return $oPLAN->getPlanesVigentesTodos($tipo_producto, $fecha);
    }

    function get_planes_vigentes($organismo, $tipo_producto, $fecha = null) {
        App::import('Model', 'proveedores.ProveedorPlan');
        $oPLAN = new ProveedorPlan();
        return $oPLAN->getPlanesVigentes($organismo, $tipo_producto, $fecha);
    }
    
    function get_planes_vigentes_proveedor_organismo($proveedorId,$organismo = NULL, $tipo_producto = NULL, $fecha = null) {
        App::import('Model', 'proveedores.ProveedorPlan');
        $oPLAN = new ProveedorPlan();
        //$codigo_organismo = null, $date = null, $noReasignable = false, $ayudaEconomica = false,$proveedorId = NULL,$tipoProducto = null
        return $oPLAN->getPlanesVigentes($organismo,$fecha,false,false,$proveedorId, $tipo_producto,(empty($organismo) ? true : false));
    }

    function grilla_planes_vigentes($beneficio_id, $tipo_producto, $fecha = null) {
        App::import('Model', 'pfyj.PersonaBeneficio');
        $oBENEF = new PersonaBeneficio();
        $organismo = $oBENEF->getCodigoOrganismo($beneficio_id);
        $planes = $this->get_planes_vigentes($organismo, $tipo_producto, $fecha);
        $this->set('planes', $planes);
        $this->render(null, 'ajax');
    }

    function combo_planes_vigentes($beneficio_id, $opcion = 'P', $plan_id = null, $monto_id = null, $fecha = null, $noReasignable = 0, $ayudaEconomica = 0) {
        App::import('Model', 'pfyj.PersonaBeneficio');
        $oBENEF = new PersonaBeneficio();
        $organismo = $oBENEF->getCodigoOrganismo($beneficio_id);
        App::import('Model', 'proveedores.ProveedorPlan');
        $oPLAN = new ProveedorPlan();
        switch ($opcion):
            case 'P':
                $datos = $oPLAN->getPlanesVigentes($organismo, null, ($noReasignable == 0 ? false : true), ($ayudaEconomica == 0 ? false : true));
                break;
            case 'M':
                $datos = $oPLAN->getMontosLiquidosByPlan($plan_id);
                break;
            case 'C':
                $datos = $oPLAN->getCuotasByPlan($plan_id, $monto_id);
                break;
            default:
                $datos = $oPLAN->getPlanesVigentes($organismo);
        endswitch;
        $this->set('datos', $datos);
        $this->set('opcion', $opcion);
        $this->render('combo_planes_vigentes', 'ajax');
    }

    function documentos_plan($plan_id) {
        App::import('Model', 'proveedores.ProveedorPlanDocumento');
        $oPLANDOC = new ProveedorPlanDocumento();
        $datos = $oPLANDOC->getDocumentosByPlan($plan_id);
        $this->set('datos', $datos);
        $this->render(null, 'ajax');
    }

    function calcular_grilla($plan_id, $UID = null) {

        if (empty($UID))
            $UID = String::uuid();

        App::import('Model', 'proveedores.ProveedorPlan');
        $oPLAN = new ProveedorPlan();
        $plan = $oPLAN->read(null, $plan_id);            

        App::import('model', 'proveedores.metodo_calculo_cuota');
        $oCALC = new MetodoCalculoCuota();

        $montos = $cuotas = $opciones = NULL;
        $TNA = $TNM = $TEM = $GTO = $SELL = $IVA = $METODO = 0;

        if (!empty($this->data)) {

//            debug($this->data);

            if (isset($this->data['Proveedor']['UID']) && !empty($this->data['Proveedor']['UID']) && $this->Session->check($this->data['Proveedor']['UID'] . "_GRILLA")) {
                $grilla = $this->Session->read($this->data['Proveedor']['UID'] . "_GRILLA");
                App::import('Model', 'proveedores.ProveedorPlanGrilla');
                $oPLANGRILLA = new ProveedorPlanGrilla();
                if ($oPLANGRILLA->cargar_grilla_calculada_nueva($grilla)) {
                    $this->redirect('grillas/' . $grilla['PLAN_ID']);
                } else {

                    $this->Mensaje->errores("ERRORES: ",$oPLANGRILLA->notificaciones);
                    // $this->redirect('calcular_grilla/' . $plan_id);
                }
            }
            if ($this->Session->check($UID . "_GRILLA"))
                $this->Session->del($UID . "_GRILLA");

//            debug($this->data);

            if (isset($this->data['ProveedorPlanGrilla']['PREVIEW']) && $oCALC->validate_fields($this->data)) {

//                debug($this->data);

                $this->data['ProveedorPlanGrilla']['vigencia_desde'] = $this->Proveedor->armaFecha($this->data['ProveedorPlanGrilla']['vigencia_desde']);

                $TNA = $plan['ProveedorPlan']['tna'];
                $TNM = $oCALC->tna_to_tnm($TNA);
                $TEM = $oCALC->tna_to_tem($TNA);
                $TEA = $oCALC->tna_to_tea($TNA);
                $GTO = $plan['ProveedorPlan']['gasto_admin'];
                $SELL = $plan['ProveedorPlan']['sellado'];
                $IVA = $plan['ProveedorPlan']['iva'];
                $METODO = $plan['ProveedorPlan']['metodo_calculo'];

                $oCALC->porcAdic = $GTO;
                $oCALC->porcSello = $SELL;
                $oCALC->porcIVA = $IVA;
                $oCALC->TNA = $TNA;
                $oCALC->TEM = $TEM;
                $oCALC->TEA = $TEA;
                $oCALC->TNM = $TNM;
                $oCALC->tasa = $oCALC->TNM;
                $oCALC->METODO_CALCULO = $METODO;

                $oCALC->tipoCuotaGAdmin = $plan['ProveedorPlan']['tipo_cuota_gasto_admin'];
                $oCALC->baseCalculoGadmin = $plan['ProveedorPlan']['gasto_admin_base_calculo'];
                $oCALC->tipoCuotaSellado = $plan['ProveedorPlan']['tipo_cuota_sellado'];
                $oCALC->baseCalculoSellado = $plan['ProveedorPlan']['sellado_base_calculo'];

                $oCALC->interesMoratorio = $plan['ProveedorPlan']['interes_moratorio'];
                $oCALC->costoCancelacionAnticipada = $plan['ProveedorPlan']['costo_cancelacion_anticipada'];


                $cuotas = explode(',', $this->data['ProveedorPlanGrilla']['cuotas_disponibles']);
                $opciones = $oCALC->arma_opciones(
                        $this->data['ProveedorPlanGrilla']['capital_minimo'], $this->data['ProveedorPlanGrilla']['capital_maximo'], $this->data['ProveedorPlanGrilla']['capital_incremento'], $cuotas
                );
//                 if (!empty($opciones)) {
//                     foreach ($opciones as $capital => $cuotas) {
//                         $oCALC->solicitado = $capital;
//                         foreach ($cuotas as $n => $cuota) {
//                             $oCALC->cuotas = $cuota;
//                             $oCALC->armar_plan();
//                             $opciones[$capital][$cuota] = array(
//                                 'liquidacion' => $oCALC->liquidacion,
//                                 'cuotaPromedio' => $oCALC->cuota_promedio,
//                                 'objetoCalculo' => $oCALC->get_objetoCalculo(),
//                             );
// //                            $opciones[$capital][$cuota] = $oCALC->cuota;
//                             $oCALC->reset();
//                         }
//                     }
//                 }
                // debug($oCALC);
            //    debug($opciones);
            //    exit;
                $montos = array_keys($opciones);
                $GRILLA = array(
                    'PLAN_ID' => $this->data['ProveedorPlanGrilla']['proveedor_plan_id'],
                    'DESCRIPCION' => $this->data['ProveedorPlanGrilla']['descripcion'],
                    'VIGENCIA' => $this->data['ProveedorPlanGrilla']['vigencia_desde'],
                    'TIPO_CUOTA_GADM' => $this->data['ProveedorPlanGrilla']['tipo_cuota_gasto_admin'],
                    'BASE_CALCULO_GADM' => $oCALC->baseCalculoGadmin,
                    'TIPO_CUOTA_SELL' => $this->data['ProveedorPlanGrilla']['tipo_cuota_sellado'],
                    'BASE_CALCULO_SELL' => $oCALC->baseCalculoSellado,
                    'TNA' => $TNA,
                    'TNM' => $TNM,
                    'TEM' => $TEM,
                    'TEA' => $TEA,
                    'GTO' => $GTO,
                    'SELL' => $SELL,
                    'IVA' => $IVA,
                    'METODO' => $METODO,
                    'CALCULO' => $opciones,
                    'OBJ_OPCIONES' => $oCALC->get_objetoOpciones(),
                );

                $this->Session->write($UID . "_GRILLA", $GRILLA);
            } else {
                $this->Mensaje->errores("ERRORES: ", $oCALC->notificaciones);
            }
        }

        $objOpciones = json_decode($oCALC->get_objetoOpciones());
        $objetoCalculo = NULL;
        if(!empty($objOpciones)){
            $objetoCalculo = $objOpciones[0]->objetosCalculo[0];
        }

        $this->set('montos', $montos);
        $this->set('cuotas', $cuotas);
        $this->set('opciones', $opciones);
        $this->set('metodos', $oCALC->METODOS);
        $this->set('criterios', $oCALC->CRITERIO_APLICACION);
        $this->set('liquidacion', (!empty($objetoCalculo) ? $objetoCalculo : NULL));
        $this->set('TNA', $TNA);
        $this->set('TEM', $TEM);
        $this->set('TEA', $TEA);
        $this->set('TNM', $TNM);
        $this->set('SELL', $SELL);
        $this->set('GTO', $GTO);
        $this->set('IVA', $IVA);
        $this->set('METODO', $METODO);

        $this->set('TIPO_CUOTA_GADM',$this->data['ProveedorPlanGrilla']['tipo_cuota_gasto_admin']);





        $this->set('proveedor', $this->Proveedor->read(null, $plan['ProveedorPlan']['proveedor_id']));
        $this->set('plan', $plan);
        $this->set('UID', $UID);
    }


    function get_cuota_grilla($cuotaId){
        Configure::write('debug',1);
        App::import('Model','proveedores.ProveedorPlanGrillaCuota');
        $oPCuo = new ProveedorPlanGrillaCuota();
        $cuota = $oPCuo->read(null,$cuotaId);
        if(!empty($cuota)){
            echo json_encode($cuota['ProveedorPlanGrillaCuota']);
            exit;
        }
    }

}

?>