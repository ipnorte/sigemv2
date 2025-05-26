<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::import('model', 'ventas.SolicitudService');

class SolicitudesController extends VentasAppController {

    var $name = 'solicitudes';
    var $uses = null;
    var $autorizar = array(
        'index',
        'alta_cuit',
        'alta_persona',
        'alta_beneficio',
        'alta_plan',
        'alta_confirm',
        'consultar_intranet',
        'adjuntar_documentacion',
        'listado',
        'password',
        'liquidacion',
        'tarjeta_edit',
        'consultar_siisa',
        'detalle_orden_descuento',
        'detalle_cuota'
    );
    var $bootstrap = FALSE;

    public function __construct() {
        parent::__construct();
        $INI_FILE = parse_ini_file(CONFIGS . 'mutual.ini', true);
        if (isset($INI_FILE['general']['bootstrap_ventas_solicitudes']) && $INI_FILE['general']['bootstrap_ventas_solicitudes'] === '1') {
            $this->bootstrap = TRUE;
        }
    }

    function beforeFilter() {
        $this->Seguridad->allow($this->autorizar);
        parent::beforeFilter();
    }

    function index() {
        $this->redirect('search');
    }

    function search() {

        $this->set('searchActive', TRUE);

        $solicitudes = null;

        $fecha_desde = $fecha_hasta = date('Y-m-d');
        if ($this->bootstrap) {
            $fecha_desde = $fecha_hasta = date('d/m/Y');
        }
        $estado = NULL;
        if (!empty($this->data)) {
//            debug($this->data);
//            App::import('model','ventas.SolicitudService');
            $oSSERVICE = new SolicitudService();
            if ($this->bootstrap) {
                $fecha_desde = date('Y-m-d', strtotime(str_replace("/", "-", $this->data['MutualProductoSolicitud']['fecha_desde'])));
                $fecha_hasta = date('Y-m-d', strtotime(str_replace("/", "-", $this->data['MutualProductoSolicitud']['fecha_hasta'])));
            } else {
                $fecha_desde = $oSSERVICE->armaFecha($this->data['MutualProductoSolicitud']['fecha_desde']);
                $fecha_hasta = $oSSERVICE->armaFecha($this->data['MutualProductoSolicitud']['fecha_hasta']);
            }
            $estado = $this->data['MutualProductoSolicitud']['estado'];
            $params = array(
                'fecha_desde' => $fecha_desde,
                'fecha_hasta' => $fecha_hasta,
                'numero' => $this->data['MutualProductoSolicitud']['numero'],
                'documento' => $this->data['MutualProductoSolicitud']['documento'],
                'estado' => (!empty($estado) ? array($estado) : NULL),
            );
//            debug($params);
            $solicitudes = $oSSERVICE->get_solicitudes($params);
            if ($this->bootstrap) {
                $fecha_desde = date('d/m/Y', strtotime($fecha_desde));
                $fecha_hasta = date('d/m/Y', strtotime($fecha_hasta));
            }
        }
        $this->set('solicitudes', $solicitudes);
        $this->set('fecha_desde', $fecha_desde);
        $this->set('fecha_hasta', $fecha_hasta);
        $this->set('estado', $estado);
        if ($this->bootstrap) {
            $this->render('nsearch', 'vendedores');
        }
    }

    function ficha($nro_solicitud) {
        if (empty($nro_solicitud))
            $this->redirect('search');
        App::import('model', 'ventas.SolicitudService');
        $oSSERVICE = new SolicitudService();
        $this->set('nro_solicitud', $nro_solicitud);
        $solicitud = $oSSERVICE->get_solicitud($nro_solicitud);
        if (empty($solicitud)) {
            $this->Mensaje->error("LA SOLICITUD #$nro_solicitud PERTENECE A OTRO VENDEDOR");
            $this->redirect('search');
        }
        $this->set('solicitud', $solicitud);
        App::import('Model', 'proveedores.ProveedorPlanDocumento');
        $oPLANDOC = new ProveedorPlanDocumento();
        $datos = $oPLANDOC->getDocumentosByPlan($solicitud['ProveedorPlan']['id']);
        $this->set('datos', $datos);
        if ($this->bootstrap) {
            $this->render('nficha', 'vendedores');
        }
    }

    function alta($step = 1, $TOKEN_ID = NULL) {

        switch ($step) {
            case 2:
                $this->redirect('alta_persona/' . $TOKEN_ID);
            case 3:
                $this->redirect('alta_plan/' . $TOKEN_ID);
            case 4:
                $this->redirect('alta_confirm/' . $TOKEN_ID);
            default:
                $this->redirect('alta_cuit');
                break;
        }
    }

    function alta_cuit() {

        $this->set('altaActive', TRUE);

        $oSSERVICE = new SolicitudService();
        if (!empty($this->data)) {

            App::import('Helper', 'Util');
            $oUT = new UtilHelper();
            if ($oUT->validar_cuit($this->data['MutualProductoSolicitud']['cuit_cuil'])) {
                $this->Session->del($this->name . "_" . $this->data['MutualProductoSolicitud']['token_id']);
                $persona = $oSSERVICE->get_persona_by_cuit($this->data['MutualProductoSolicitud']['cuit_cuil']);
                if (empty($persona)) {
                    $persona['Persona']['cuit_cuil'] = $this->data['MutualProductoSolicitud']['cuit_cuil'];
                    $persona['Persona']['documento'] = substr($persona['Persona']['cuit_cuil'], 2, 8);
                }
                $solicitud['Persona'] = $persona['Persona'];
                $this->Session->write($this->name . "_" . $this->data['MutualProductoSolicitud']['token_id'], $solicitud);
                $this->redirect('alta/2/' . $this->data['MutualProductoSolicitud']['token_id']);
            } else {
                $this->Mensaje->errores("SE DETECTARON LOS SIGUIENTES ERRORES:", array('El CUIT no es válido!'));
            }
        }
        $TOKEN_ID = $oSSERVICE->generarPIN(20);
        $this->set('TOKEN_ID', $TOKEN_ID);
//        $this->render('forms/alta_cuit');
        if ($this->bootstrap) {
            $this->render('forms/nalta_cuit', 'vendedores');
        } else {
            $this->render('forms/alta_cuit');
        }
    }

    function alta_persona($TOKEN_ID) {

        $this->set('altaActive', TRUE);

        $oSSERVICE = new SolicitudService();
        $this->set('limiteMenorEdad', $oSSERVICE->limiteMenorEdad);
        $this->set('limiteMayorEdad', $oSSERVICE->limiteMayorEdad);

        $invalidFields = array();
        if (!empty($TOKEN_ID) && $this->Session->check($this->name . "_" . $TOKEN_ID)) {
            $solicitud = $this->Session->read($this->name . "_" . $TOKEN_ID);

            if (!empty($this->data)) {
                if ($this->bootstrap) {
                    $this->data['Persona']['fecha_nacimiento'] = date('Y-m-d', strtotime(str_replace("/", "-", $this->data['Persona']['fecha_nacimiento'])));
                }
                $VALIDATE = $oSSERVICE->validate_datos_personales($this->data);
                $solicitud['Persona'] = $VALIDATE['PERSONA'];
                if (!$VALIDATE['STATUS']) {
                    $this->Mensaje->errores("SE DETECTARON LOS SIGUIENTES ERRORES:", $oSSERVICE->notificaciones);
                    $invalidFields = $oSSERVICE->invalidFields;
                } else {
                    $persona['Persona'] = $VALIDATE['PERSONA'];
                    $persona = $oSSERVICE->set_persona($persona);
                    if (!$persona) {
                        $this->Mensaje->errores("SE DETECTARON LOS SIGUIENTES ERRORES:", $oSSERVICE->notificaciones);
                        $invalidFields = $oSSERVICE->invalidFields;
                    } else {
                        $solicitud['Persona'] = $persona;
                        $this->Session->write($this->name . "_" . $TOKEN_ID, $solicitud);
                        $this->redirect('alta/3/' . $TOKEN_ID);
                    }
                }
            }
            $this->set('solicitud', $solicitud);
            $this->set('invalidFields', $invalidFields);
            $this->set('TOKEN_ID', $TOKEN_ID);
//            $this->render('forms/alta_persona');
            if ($this->bootstrap) {
                $this->render('forms/nalta_persona', 'vendedores');
            } else {
                $this->render('forms/alta_persona');
            }
        } else {
            $this->Mensaje->error('TOKEN_ID NO ESPECIFICADO');
            $this->redirect('alta');
        }
    }

    function alta_beneficio($TOKEN_ID) {

        $this->set('altaActive', TRUE);

        $oSSERVICE = new SolicitudService();

        if (!empty($TOKEN_ID) && $this->Session->check($this->name . "_" . $TOKEN_ID)) {
            $solicitud = $this->Session->read($this->name . "_" . $TOKEN_ID);
            $this->set('solicitud', $solicitud);
            $this->set('TOKEN_ID', $TOKEN_ID);

            if (!empty($this->data)) {

                if ($oSSERVICE->set_beneficio($this->data)) {
                    $this->redirect('alta/3/' . $TOKEN_ID);
                } else {
                    $this->Mensaje->errores("SE DETECTARON LOS SIGUIENTES ERRORES:", $oSSERVICE->notificaciones);
                }
            }

            $organismosCBU = $this->requestAction("/config/global_datos/get_organismos_activos_cbu");
            $organismosCBU = array_keys($organismosCBU);
            $organismosCBU = "'" . implode("','", $organismosCBU) . "'";
            $this->set('organismosCBU', $organismosCBU);

            if ($this->bootstrap) {
                $this->render('forms/nalta_plan', 'vendedores');
            } else {
                $this->render('forms/alta_beneficio');
            }
        } else {

            $this->Mensaje->error('TOKEN_ID NO ESPECIFICADO');
            $this->redirect('alta');
        }
    }

    function alta_plan($TOKEN_ID) {

        $this->set('altaActive', TRUE);

        $oSSERVICE = new SolicitudService();
        if (!empty($TOKEN_ID) && $this->Session->check($this->name . "_" . $TOKEN_ID)) {
            $solicitud = $this->Session->read($this->name . "_" . $TOKEN_ID);
            if (!empty($this->data)) {

               if(isset($this->data['MutualProductoSolicitud']['siisa'])) {
                   $solicitud['siisa'] = base64_decode($this->data['MutualProductoSolicitud']['siisa']);
               }

                $beneficio = $oSSERVICE->get_beneficio_by_id($this->data['MutualProductoSolicitud']['persona_beneficio_id']);
                $solicitud['Beneficio'] = $beneficio['PersonaBeneficio'];
                $solicitud['Beneficio']['sueldo_neto'] = $this->data['MutualProductoSolicitud']['sueldo_neto'];
                $solicitud['Beneficio']['debitos_bancarios'] = $this->data['MutualProductoSolicitud']['debitos_bancarios'];

//                debug($solicitud);
//                exit;

                $solicitud['forma_pago'] = $this->data['MutualProductoSolicitud']['forma_pago'];
                $solicitud['observaciones'] = $this->data['MutualProductoSolicitud']['observaciones'];

                ##############################################################################
                #CARGO EL PLAN SELECCIONADO
                ##############################################################################                
                $solicitud['Plan'] = $oSSERVICE->get_plan($this->data['ProveedorPlan']['id']);
                $solicitud['Cuota'] = $oSSERVICE->get_cuota($this->data['ProveedorPlanGrillaCuota']['cuota_id']);

                ##############################################################################
                #CARGO LAS CANCELACIONES
                ##############################################################################
                $TOTAL_CANCELA = 0;
                if (!empty($this->data['MutualProductoSolicitud']['CancelacionOrden'])) {
                    $solicitud['Cancelaciones'] = array();
                    foreach ($this->data['MutualProductoSolicitud']['CancelacionOrden'] as $id => $impo) {
                        $cancelacion = $oSSERVICE->get_cancelacion($id);
                        $TOTAL_CANCELA += $cancelacion['CancelacionOrden']['importe_proveedor'];
                        $solicitud['Cancelaciones'][$id] = $cancelacion;
                    }
                }

                ##############################################################################
                #CARGO LOS ADJUNTOS
                ##############################################################################
                $allowed = array('application/pdf', 'image/jpeg', 'image/png');
                $solicitud['Archivos'] = array();
                App::import('Core', 'File');
                $FILE_ERROR = FALSE;
                $FILE_ERROR_MSG = array();
                $index = 0;

                if(!is_dir(WWW_ROOT . "files" . DS . "solicitudes")){mkdir(WWW_ROOT . "files" . DS . "solicitudes");}
                
                $DIRDATAUPLOAD = WWW_ROOT . "files" . DS . "solicitudes" . DS . $TOKEN_ID;
                if (is_dir($DIRDATAUPLOAD)){rmdir($DIRDATAUPLOAD);}
                mkdir($DIRDATAUPLOAD);

                foreach ($this->data['ProveedorPlanDocumento'] as $key => $value) {

                    if ($this->data['ProveedorPlanDocumento'][$key]['error'] == 0) {

                        list($codigoDocumento, $descripcion) = explode('|', $key);

                        if (in_array($this->data['ProveedorPlanDocumento'][$key]['type'], $allowed)) {
                            $index += 1;
                            $fileData = array();
                            $fileData['file_name'] = $index."_".str_replace(' ', '', $this->data['ProveedorPlanDocumento'][$key]['name']);
                            $fileData['file_type'] = $this->data['ProveedorPlanDocumento'][$key]['type'];
                            $fileData['file_tmp_name'] = $this->data['ProveedorPlanDocumento'][$key]['tmp_name'];


                            $fileName = $DIRDATAUPLOAD . DS . $fileData['file_name'];
                            if(file_exists(WWW_ROOT . $fileName)) unlink (WWW_ROOT . $fileName);
                            if(!move_uploaded_file($fileData['file_tmp_name'], $fileName)){
                                $FILE_ERROR = TRUE;
                                array_push($FILE_ERROR_MSG, 'EL ARCHIVO #' . $key . ' [' . $this->data['ProveedorPlanDocumento'][$key]['name'] . '] NO PUDO SER PROCESADO');
                                break;    
                            }
                            // chmod($fileName, 01777);
                            // $file = new File($this->data['ProveedorPlanDocumento'][$key]['tmp_name'], false);
                            $fileData['descripcion'] = $descripcion;
                            // $fileData['file_data'] = $file->read();
//                            $tmp = array($index => $fileData);
//                            array_push($solicitud['Archivos'], $tmp);
                            $solicitud['Archivos'][$codigoDocumento] = $fileData;
                        } else {
                            $FILE_ERROR = TRUE;
                            array_push($FILE_ERROR_MSG, 'EL ARCHIVO #' . $key . ' [' . $this->data['ProveedorPlanDocumento'][$key]['name'] . '] NO ES UN FORMATO VALIDO');
                            break;
                        }
                    } else {
//                            $FILE_ERROR = TRUE;
//                            array_push($FILE_ERROR_MSG, 'EL ARCHIVO #'.$key.' ['.$fileData['file_name'].'] NO SE ENCUENTRA CARGADO');
//                            break;
                    }
                }
                
                // shell_exec("chmod -R 777 " . $DIRDATAUPLOAD);

//                debug($solicitud['Archivos']);
//                exit;
                /*                 * ******************************** */
                /*
                  if($this->data['MutualProductoSolicitud']['archivo_'.$index]['error'] == 0){
                  if(in_array($this->data['MutualProductoSolicitud']['archivo_'.$index]['type'], $allowed)){
                  $fileData = array();
                  $fileData['file_name'] = $this->data['MutualProductoSolicitud']['archivo_'.$index]['name'];
                  $fileData['file_type'] = $this->data['MutualProductoSolicitud']['archivo_'.$index]['type'];
                  $file = new File($this->data['MutualProductoSolicitud']['archivo_'.$index]['tmp_name'], false);
                  $fileData['file_data'] = $file->read();
                  //                            debug($fileData);
                  $solicitud['Archivos'][$index] = $fileData;
                  }else{
                  $FILE_ERROR = TRUE;
                  array_push($FILE_ERROR_MSG, 'EL ARCHIVO #'.$index.' ['.$fileData['file_name'].'] NO ES UN FORMATO VALIDO');
                  break;
                  }
                  }
                 */
                //}
                //debug($FILE_ERROR_MSG);

                if (!$FILE_ERROR) {
                    ############################################################################
                    # CONTROLO QUE EL TOTAL A CANCELAR SEA MENOR AL SOLICITADO
                    ############################################################################
                    if ($solicitud['Cuota']['ProveedorPlanGrillaCuota']['liquido'] >= $TOTAL_CANCELA) {
                        $this->Session->write($this->name . "_" . $TOKEN_ID, $solicitud);
                        $this->redirect('alta/4/' . $TOKEN_ID);
                    } else {
                        $this->Mensaje->error('EL IMPORTE SELECCIONADO [' . $solicitud['Cuota']['liquido'] . '] NO PUEDE SER INFERIOR AL TOTAL A CANCELAR [' . $TOTAL_CANCELA . ']');
                    }
                } else {
                    $this->Mensaje->errores("SE DETECTARON LOS SIGUIENTES ERRORES:", $FILE_ERROR_MSG);
                }
            }
            $cancelaciones = null;
            if (!empty($solicitud['Persona']['socio_nro'])) {
                $oSSERVICE = new SolicitudService();
                $cancelaciones = $oSSERVICE->get_cancelaciones_by_socio($solicitud['Persona']['socio_nro']);
            }
            $this->set('cancelaciones', $cancelaciones);
            $this->set('vendedores', $oSSERVICE->get_vendedores());
            $this->set('solicitud', $solicitud);
            $this->set('TOKEN_ID', $TOKEN_ID);
//            $this->render('forms/alta_plan');
            if ($this->bootstrap) {
                $this->render('forms/nalta_plan', 'vendedores');
            } else {
                $this->render('forms/alta_plan');
            }
        } else {
            $this->Mensaje->error('TOKEN_ID NO ESPECIFICADO');
            $this->redirect('alta');
        }
    }

    function alta_confirm($TOKEN_ID) {

        $this->set('altaActive', TRUE);

        $oSSERVICE = new SolicitudService();

        if (!empty($TOKEN_ID) && $this->Session->check($this->name . "_" . $TOKEN_ID)) {
            $solicitud = $this->Session->read($this->name . "_" . $TOKEN_ID);

            $solicitud['token'] = $TOKEN_ID;

            if (!empty($this->data)) {
                $solicitud['MutualProductoSolicitud']['vendedor_id'] = $this->data['MutualProductoSolicitud']['vendedor_id'];
                $nro_solicitud = $oSSERVICE->generar_solicitud($solicitud);
                if (!empty($nro_solicitud)) {
                    $this->Session->del($this->name . "_" . $TOKEN_ID);
                    $this->redirect('ficha/' . $nro_solicitud);
                } else {
                    $this->Mensaje->errores("SE DETECTARON LOS SIGUIENTES ERRORES:", $oSSERVICE->notificaciones);
                }
            }
            
            if(!isset($solicitud['Plan']) || empty($solicitud['Plan']['ProveedorPlan']['id'])) {
                $this->Mensaje->error('PLAN NO ESPECIFICADO');
                $this->redirect('alta');
            }

            $this->set('solicitud', $solicitud);
            $this->set('TOKEN_ID', $TOKEN_ID);
            if ($this->bootstrap) {
                $this->render('forms/nalta_confirm', 'vendedores');
            } else {
                $this->render('forms/alta_confirm');
            }
        } else {

            $this->redirect('alta');
        }
    }

    function estado_cuenta($id = NULL) {

        $this->set('estado_cuentaActive', TRUE);

        $oSSERVICE = new SolicitudService();
        $persona = $personas = $solicitudes = null;

        if (!empty($this->data)) {

            $params = array();

            $params['tipo_documento'] = (isset($this->data['Persona']['tipo_documento']) ? $this->data['Persona']['tipo_documento'] : "");
            $params['nro_documento'] = (isset($this->data['Persona']['documento']) ? $this->data['Persona']['documento'] : "");
            $params['apellido'] = (isset($this->data['Persona']['apellido']) ? $this->data['Persona']['apellido'] : "");
            $params['nombre'] = (isset($this->data['Persona']['nombre']) ? $this->data['Persona']['nombre'] : "");
            $params['nro_socio'] = (isset($this->data['Persona']['nro_socio']) ? $this->data['Persona']['nro_socio'] : "");

            $personas = $oSSERVICE->search_personas($params);
        }

        if (!empty($id)) {
            $persona = $oSSERVICE->get_persona_by_id($id);

            $cuotas = $reintegros = null;
            if (isset($persona['Socio']['id']) && !empty($persona['Socio']['id'])) {
                App::import('model', 'mutual.OrdenDescuentoCuota');
                $oCUOTA = new OrdenDescuentoCuota();
                // $periodo_desde = date("Ym",strtotime(date('Y-m-d')."- 24 month"));
                // $periodo_hasta = date("Ym",strtotime(date('Y-m-d')."+ 1 month"));
                $periodo_desde = '190001';
                $periodo_hasta = '299912';
                $cuotas = $oCUOTA->procesa_deuda($persona['Socio']['id'], $periodo_desde, $periodo_hasta, TRUE);
                
                #CARGAR LOS REINTEGROS ADEUDADOS
                App::import('model','pfyj.SocioReintegro');
                $oSR = new SocioReintegro();
                $reintegros = $oSR->getReintegrosPendientesBySocio($persona['Socio']['id']);                
            }
            $this->set('cuotas', $cuotas);
            $this->set('reintegros',$reintegros);

            $params = array(
                'persona_id' => $persona['Persona']['id'],
                'aprobada' => FALSE,
                'anulada' => FALSE,
                'estadoNotIn' => array('MUTUESTA0000', 'MUTUESTA0014'),
            );
            $solicitudes = $oSSERVICE->get_solicitudes($params, FALSE);
        }
        $this->set('personas', $personas);
        $this->set('persona', $persona);
        $this->set('solicitudes', $solicitudes);
        if ($this->bootstrap) {
            $this->render('nestado_cuenta', 'vendedores');
        }
    }

    public function consultar_intranet() {

        $this->set('consultar_intranetActive', TRUE);

        $persona = NULL;
        $informe = NULL;
        $ndoc = $cuitCuil = NULL;

        $oSSERVICE = new SolicitudService();

        if (!empty($this->data)) {
            $ndoc = $this->data['Persona']['documento'];
            $persona = $oSSERVICE->get_persona_by_dni($ndoc);
            $cuitCuil = $persona['Persona']['cuit_cuil'];
        }

//            if(!empty($id)){
//                $persona = $this->Persona->getPersona($id);
//                $ndoc = $persona['Persona']['documento'];
//                $cuitCuil = $persona['Persona']['cuit_cuil'];
//            }

        if (!empty($ndoc)) {
            $ws = $this->requestAction('/config/global_datos/get_webservices_intranet');
            if (!empty($ws)) {
                ini_set("soap.wsdl_cache_enabled", 0);
                foreach ($ws as $service) {
                    $client = new SoapClient(trim($service['GlobalDato']['concepto_2']));
                    $json = $client->getPersonaByDocumento($ndoc);
                    $json = json_decode($json);
                    //if(!empty($persona->result)) 
                    $informe[$service['GlobalDato']['id']] = array('CLIENTE' => $service['GlobalDato']['concepto_1'], 'RESULTADO' => $json->result);
                }
            }
        }

        $response = NULL;
        if (!empty($cuitCuil)) {

//                $ch = curl_init();
//                curl_setopt($ch, CURLOPT_URL, "http://cordobasoft.com:8080/bcra-api/deudores/" . $cuitCuil);
//                curl_setopt($ch, CURLOPT_HEADER, 0);
//                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
//                $res = curl_exec($ch);
            $response = $this->requestAction('/pfyj/personas/get_consulta_bcra/' . $cuitCuil);
        }

        $this->set('cuitCuil', $cuitCuil);
        $this->set('id', (isset($id) ? $id : NULL));
        $this->set('response', $response);

        $this->set('persona', $persona);
        $this->set('ndoc', $ndoc);
        $this->set('informe', $informe);

        if ($this->bootstrap) {
            $this->render('nconsultar_intranet', 'vendedores');
        }
    }

    /*
      function adjuntar_documentacion($solicitud_id = null){

      if(empty($solicitud_id)){
      $this->redirect('search');
      }

      if(!empty($this->data)){
      if($this->data['MutualProductoSolicitud']['archivo']['error'] != 4){

      $allowed = array('image/jpeg','application/pdf','image/png');

      if(in_array($this->data['MutualProductoSolicitud']['archivo']['type'],$allowed)){

      if($this->data['MutualProductoSolicitud']['archivo']['size'] <= (5 * 1024 * 1024)){

      App::import('Core', 'File');
      $file = new File($this->data['MutualProductoSolicitud']['archivo']['tmp_name'], false);
      App::import('model','mutual.MutualProductoSolicitudDocumento');
      $oSDOC = new MutualProductoSolicitudDocumento();
      $documento = array();
      $documento['MutualProductoSolicitudDocumento']['id'] = 0;
      $documento['MutualProductoSolicitudDocumento']['mutual_producto_solicitud_id'] = $this->data['MutualProductoSolicitud']['id'];
      $documento['MutualProductoSolicitudDocumento']['file_name'] = $this->data['MutualProductoSolicitud']['archivo']['name'];
      $documento['MutualProductoSolicitudDocumento']['file_type'] = $this->data['MutualProductoSolicitud']['archivo']['type'];
      $documento['MutualProductoSolicitudDocumento']['file_data'] = $file->read();

      if($oSDOC->save($documento)){

      $this->Mensaje->ok("EL ARCHIVO FUE ANEXADO A LA SOLICITUD CORRECTAMENTE");
      $this->redirect('ficha/'.$this->data['MutualProductoSolicitud']['id']);

      }else{
      $this->Mensaje->error("SE PRODUJO UN ERROR AL GUARDAR EL DOCUMENTO");
      $this->redirect('ficha/'.$this->data['MutualProductoSolicitud']['id']);
      }



      }  else {
      $this->Mensaje->error("EL ARCHIVO SUPERA EL TAMAÑO PERMITIDO (5 MB)");
      $this->redirect('ficha/'.$this->data['MutualProductoSolicitud']['id']);
      }



      }  else {
      $this->Mensaje->error("EL ARCHIVO NO CORRESPONDE A LOS FORMATOS PERMITIDOS (".  implode(",",$allowed).")");
      $this->redirect('ficha/'.$this->data['MutualProductoSolicitud']['id']);
      }


      }else{
      $this->Mensaje->error("SE PRODUJO UN ERROR AL SUBIR EL ARCHIVO");
      $this->redirect('ficha/'.$solicitud_id);
      }

      }

      $this->redirect('ficha/'.$solicitud_id);

      }
     * 
     */

    function adjuntar_documentacion($solicitud_id = null, $cargoArray = false) {
        
        if (empty($solicitud_id)) {
            $this->redirect('search');
        }

        if (!$cargoArray) {
            if (!empty($this->data)) {
                if ($this->data['MutualProductoSolicitud']['archivo']['error'] != 4) {

                    $allowed = array('image/jpeg', 'application/pdf', 'image/png');

                    if (in_array($this->data['MutualProductoSolicitud']['archivo']['type'], $allowed)) {

                        if ($this->data['MutualProductoSolicitud']['archivo']['size'] <= (5 * 1024 * 1024)) {

                            App::import('Core', 'File');
                            $file = new File($this->data['MutualProductoSolicitud']['archivo']['tmp_name'], false);
                            App::import('model', 'mutual.MutualProductoSolicitudDocumento');
                            $oSDOC = new MutualProductoSolicitudDocumento();
                            $documento = array();
                            $documento['MutualProductoSolicitudDocumento']['id'] = 0;
                            $documento['MutualProductoSolicitudDocumento']['mutual_producto_solicitud_id'] = $this->data['MutualProductoSolicitud']['id'];
                            $documento['MutualProductoSolicitudDocumento']['file_name'] = str_replace(' ','',$this->data['MutualProductoSolicitud']['archivo']['name']);
                            $documento['MutualProductoSolicitudDocumento']['file_type'] = $this->data['MutualProductoSolicitud']['archivo']['type'];
                            // $documento['MutualProductoSolicitudDocumento']['file_data'] = $file->read();

                            $DIRDATAUPLOAD = WWW_ROOT . "files" . DS . "solicitudes" . DS . $documento['MutualProductoSolicitudDocumento']['mutual_producto_solicitud_id'];
                            if (! is_dir($DIRDATAUPLOAD)){mkdir($DIRDATAUPLOAD);}

                            $fileName = $DIRDATAUPLOAD . DS . $documento['MutualProductoSolicitudDocumento']['file_name'];
                            if(file_exists($fileName)) unlink ($fileName);
                            if(!move_uploaded_file($this->data['MutualProductoSolicitud']['archivo']['tmp_name'], $fileName)){
                                $this->Mensaje->error("ERROR AL SUBIR EL ARCHIVO AL SERVIDOR");
                                $this->redirect('ficha/' . $this->data['MutualProductoSolicitud']['id']);   
                            }

                            if ($oSDOC->save($documento)) {

                                $this->Mensaje->ok("EL ARCHIVO FUE ANEXADO A LA SOLICITUD CORRECTAMENTE");
                                $this->redirect('ficha/' . $this->data['MutualProductoSolicitud']['id']);
                            } else {
                                $this->Mensaje->error("SE PRODUJO UN ERROR AL GUARDAR EL DOCUMENTO");
                                $this->redirect('ficha/' . $this->data['MutualProductoSolicitud']['id']);
                            }
                        } else {
                            $this->Mensaje->error("EL ARCHIVO SUPERA EL TAMAÑO PERMITIDO (5 MB)");
                            $this->redirect('ficha/' . $this->data['MutualProductoSolicitud']['id']);
                        }
                    } else {
                        $this->Mensaje->error("EL ARCHIVO NO CORRESPONDE A LOS FORMATOS PERMITIDOS (" . implode(",", $allowed) . ")");
                        $this->redirect('ficha/' . $this->data['MutualProductoSolicitud']['id']);
                    }
                } else {
                    $this->Mensaje->error("SE PRODUJO UN ERROR AL SUBIR EL ARCHIVO");
                    $this->redirect('ficha/' . $solicitud_id);
                }
            }

            $this->redirect('ficha/' . $solicitud_id);
            
        } else {

            if (!empty($this->data)) {

                App::import('Core', 'File');
                App::import('model', 'mutual.MutualProductoSolicitudDocumento');
                $oSDOC = new MutualProductoSolicitudDocumento();
                $allowed = array('image/jpeg', 'application/pdf', 'image/png');
                
                foreach ($this->data['ProveedorPlanDocumento'] as $key => $value) {
                    
                    if ($this->data['ProveedorPlanDocumento'][$key]['error'] != 4){
                        
                        if (in_array($this->data['ProveedorPlanDocumento'][$key]['type'], $allowed)) {

                            if ($this->data['ProveedorPlanDocumento'][$key]['size'] <= (5 * 1024 * 1024)) {

                                if ($this->data['ProveedorPlanDocumento'][$key]['error'] == 0) {

                                    $file = new File($this->data['ProveedorPlanDocumento'][$key]['tmp_name'], false);
                                    $documento = array();
                                    $documento['MutualProductoSolicitudDocumento']['id'] = 0;
                                    $documento['MutualProductoSolicitudDocumento']['mutual_producto_solicitud_id'] = $this->data['MutualProductoSolicitud']['id'];
                                    $documento['MutualProductoSolicitudDocumento']['file_name'] = str_replace(' ','',$this->data['ProveedorPlanDocumento'][$key]['name']);
                                    $documento['MutualProductoSolicitudDocumento']['file_type'] = $this->data['ProveedorPlanDocumento'][$key]['type'];
                                    list($codigoDocumento, $descripcion) = explode('|', $key);
                                    $documento['MutualProductoSolicitudDocumento']['codigo_documento'] = $codigoDocumento;
                                    // $documento['MutualProductoSolicitudDocumento']['file_data'] = $file->read();

                                    $DIRDATAUPLOAD = WWW_ROOT . "files" . DS . "solicitudes" . DS . $documento['MutualProductoSolicitudDocumento']['mutual_producto_solicitud_id'];
                                    if (! is_dir($DIRDATAUPLOAD)){mkdir($DIRDATAUPLOAD);}
                                    $fileName = $DIRDATAUPLOAD . DS . $documento['MutualProductoSolicitudDocumento']['file_name'];
                                    if(file_exists($fileName)) unlink ($fileName);
                                    if(!move_uploaded_file($this->data['ProveedorPlanDocumento'][$key]['tmp_name'], $fileName)){
                                        $this->Mensaje->error("ERROR AL SUBIR EL ARCHIVO AL SERVIDOR");
                                        $this->redirect('ficha/' . $this->data['MutualProductoSolicitud']['id']);   
                                    }                                    

                                    if ($oSDOC->save($documento)) {

                                        $this->Mensaje->ok("EL ARCHIVO FUE ANEXADO A LA SOLICITUD CORRECTAMENTE");
//                                        $this->redirect('ficha/' . $this->data['MutualProductoSolicitud']['id']);
                                    } else {
                                        $this->Mensaje->error("SE PRODUJO UN ERROR AL GUARDAR EL DOCUMENTO");
//                                        $this->redirect('ficha/' . $this->data['MutualProductoSolicitud']['id']);
                                    }
                                }else{
                                    $this->Mensaje->error("SE PRODUJO UN ERROR AL SUBIR EL ARCHIVO");
                                    $this->redirect('ficha/' . $this->data['MutualProductoSolicitud']['id']);
                                }
                            } else {
                                
                                $this->Mensaje->error("EL ARCHIVO SUPERA EL TAMAÑO PERMITIDO (2 MB)");
                                $this->redirect('ficha/' . $this->data['MutualProductoSolicitud']['id']);
                            }
                        } else {
                                $this->Mensaje->error("EL ARCHIVO NO CORRESPONDE A LOS FORMATOS PERMITIDOS (" . implode(",", $allowed) . ")");
                                $this->redirect('ficha/' . $this->data['MutualProductoSolicitud']['id']);
                        }                        
//                        
//                    }else{
//                        $this->Mensaje->error("SE PRODUJO UN ERROR AL SUBIR EL ARCHIVO");
//                        $this->redirect('ficha/' . $this->data['MutualProductoSolicitud']['id']);                        
                    }
                    

                }
                
            }else{
                $this->Mensaje->error("NO SE INDICA EL ARCHIVO O EL MISMO SUPERA EL TAMAÑO PERMITIDO (5 MB)");
                $this->redirect('ficha/' . $solicitud_id);
            }
            #todo ok
            $this->redirect('ficha/' . $solicitud_id);
        }
        
    }

    public function listado($tipoReporte = 'PDF') {

        $this->set('listadoActive', TRUE);

        $fecha_desde = $fecha_hasta = $fecha_corte = date('Y-m-d');
        $estado = NULL;
        $periodo_corte = date('Ym');
        $show_asincrono = 0;
        if ($tipoReporte == "PDF") {
            if (isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])) {
                App::import('model', 'Shells.Asincrono');
                $oASINC = new Asincrono();
                $asinc = $oASINC->read('p1,p2,p3,p4,p6,p9', $this->params['url']['pid']);
                $this->redirect('/mutual/listados/download/' . $asinc['Asincrono']['p9']);
            }
        }

        $oSSERVICE = new SolicitudService();

        if (!empty($this->data)) {
            $show_asincrono = 1;
            if ($this->bootstrap) {
                $fecha_desde = date('Y-m-d', strtotime(str_replace("/", "-", $this->data['MutualProductoSolicitud']['fecha_desde'])));
                $fecha_hasta = date('Y-m-d', strtotime(str_replace("/", "-", $this->data['MutualProductoSolicitud']['fecha_hasta'])));
            } else {
                $fecha_desde = $oSSERVICE->armaFecha($this->data['MutualProductoSolicitud']['fecha_desde']);
                $fecha_hasta = $oSSERVICE->armaFecha($this->data['MutualProductoSolicitud']['fecha_hasta']);
            }
            $estado = $this->data['MutualProductoSolicitud']['estado'];
        }

        $vendedoresString = $oSSERVICE->oUSER->get_vendedorId_logon(true);

        //$userLogin = $this->Seguridad->user();
        $this->set('show_asincrono', $show_asincrono);

        $this->set('periodo_corte', $periodo_corte);
        $this->set('fecha_corte', $fecha_corte);
        $this->set('vendedor_id', $vendedoresString);

        $this->set('fecha_desde', $fecha_desde);
        $this->set('fecha_hasta', $fecha_hasta);
        $this->set('estado', $estado);
        if ($this->bootstrap) {
            $this->render('nlistado', 'vendedores');
        }
    }

    function password() {

        $this->set('passwordActive', TRUE);

        App::import('model', 'seguridad.Usuario');
        $oUSUARIO = new Usuario();

        if (!empty($this->data)) {
            if ($oUSUARIO->password_check($this->data)) {
                $this->redirect('/seguridad/usuarios/logout');
            } else {
                $this->Mensaje->errores("SE DETECTARON LOS SIGUIENTES ERRORES: ", $oUSUARIO->notificaciones);
            }
        }

        $user = $this->Seguridad->user();



        $oUSUARIO->bindModel(array('belongsTo' => array('Grupo')));
        $usuario = $oUSUARIO->read(null, $user['Usuario']['id']);
        if (!$oUSUARIO->check_status_logon($usuario))
            $this->redirect('/seguridad/usuarios/logout');
        $this->set('user', $usuario);
        $this->render('npassword', 'vendedores');
    }

    
    public function liquidacion($id = NULL) {
        
        if(empty($id)){$this->redirect('/search');}
        
        $this->set('estado_cuentaActive', TRUE);
        
        $oSSERVICE = new SolicitudService();

        $persona = $oSSERVICE->get_persona_by_id($id);
        if(empty($persona)){$this->redirect('search');}
        
        $this->set('persona', $persona);
        
        if(empty($persona['Persona']['socio_nro'])){$this->redirect('estado_cuenta/' . $id);}

        
        App::import('model','Mutual.LiquidacionSocio');
        $oLiqSoc = new LiquidacionSocio();
        $periodoDesde = $periodoHasta = date('Ym');
        
        $ultimoPeriodo = $oLiqSoc->periodosBySocio($persona['Persona']['socio_nro'],'DESC',1);

        if(!empty($ultimoPeriodo)){
            $periodoHasta = array_shift($ultimoPeriodo);
            if(!empty($ultimoPeriodo)){$periodoDesde = array_pop($ultimoPeriodo);}
            else {$periodoDesde = $periodoHasta;}
        }

        $liquidaciones = $oLiqSoc->liquidacionesByPeriodoBySocio($persona['Persona']['socio_nro'],$periodoDesde,$periodoHasta);

        $this->set('liquidaciones', $liquidaciones);
        $this->render('liquidacion', 'vendedores');
        
    }
    
    function tarjeta_edit($TOKEN_ID){
     
        $this->set('altaActive', TRUE);
        
        $oSSERVICE = new SolicitudService();
        
        if (!empty($TOKEN_ID) && $this->Session->check($this->name . "_" . $TOKEN_ID)) {
            $solicitud = $this->Session->read($this->name . "_" . $TOKEN_ID);
            $this->set('solicitud', $solicitud);
            $this->set('TOKEN_ID', $TOKEN_ID);
            
            if (!empty($this->data)) {
                
                if ($oSSERVICE->set_tarjetaDebito($this->data)) {
                    $this->redirect('alta/3/' . $TOKEN_ID);
                } else {
                    $this->Mensaje->errores("SE DETECTARON LOS SIGUIENTES ERRORES:", $oSSERVICE->notificaciones);
                }
            }
            
            $organismosCBU = $this->requestAction("/config/global_datos/get_organismos_activos_cbu");
            $organismosCBU = array_keys($organismosCBU);
            $organismosCBU = "'" . implode("','", $organismosCBU) . "'";
            $this->set('organismosCBU', $organismosCBU);
            
            if ($this->bootstrap) {
                $this->render('forms/nalta_plan', 'vendedores');
            } else {
                $this->render('forms/alta_beneficio');
            }
        } else {
            
            $this->Mensaje->error('TOKEN_ID NO ESPECIFICADO');
            $this->redirect('alta');
        }
        
    }
    
    
    function consultar_siisa() {
        $this->set('consultar_SIISAActive', TRUE);
        $respuestas = array();
        
        App::import('Model', 'Config.GlobalDato');
        $oGLD = new GlobalDato();
        $datos = $oGLD->get_productos_siisa();        
        
        if(!empty($this->data)){
            $documento = $this->data['Persona']['documento'];
            $nombre = $this->data['Persona']['nombre'];
            
            $sueldo_neto = $this->data['Persona']['sueldo_neto'];
            $debitos_por_cbu = $this->data['Persona']['debitos_bancarios'];
            $cuota_credito = $this->data['Persona']['cuota_credito'];
            $tipo_de_producto = $this->data['Persona']['producto_siisa'];
            
            App::import('Vendor','SIISAService',array('file' => 'siisa_service.php'));
            $oSIISA = new SIISAService();

            $parameters = array(
                'nroDoc' => $documento,
                'nombre' => $nombre,
                'tipo_de_producto' => $tipo_de_producto,
                'sueldo_neto' => $sueldo_neto,
                'debitos_por_cbu' => $debitos_por_cbu,
                'cuota_credito' => $cuota_credito,
            );

            $respuesta = $oSIISA->executePolicyByParameters($parameters);
            array_push($respuestas, array(
                'producto_siisa' => $tipo_de_producto,
                'respuesta' => $respuesta,
                'parametros' => $oSIISA->params
            ));            
            

//            foreach ($datos as $dato) {
//                App::import('Vendor','SIISAService',array('file' => 'siisa_service.php'));
//                $oSIISA = new SIISAService();
//                $parameters = array(
//                    'nroDoc' => $documento,
//                    'nombre' => $nombre,
//                    'tipo_de_producto' => $dato['GlobalDato']['concepto_4'],
//                    'sueldo_neto' => floatval($sueldo_neto),
//                    'debitos_por_cbu' => floatval($debitos_por_cbu),
//                    'cuota_credito' => floatval($cuota_credito),
//                );
//                $respuesta = $oSIISA->executePolicyByParameters($parameters);
//                array_push($respuestas, array(
//                    'producto_siisa' => $dato['GlobalDato']['concepto_4'],
//                    'respuesta' => $respuesta,
//                    'parametros' => $oSIISA->params
//                ));
//            }
        }
        $this->set('respuestas',$respuestas);
        $this->set('productos_siisa', $datos);
        $this->render('consultar_siisa', 'vendedores');
    }
    
    
    function detalle_orden_descuento($orden_id = null) {
        $oSSERVICE = new SolicitudService();
        $orden = $oSSERVICE->getDetalleOrdenDeDescuento($orden_id);
        $this->set('orden',$orden);
    }
    
    function detalle_cuota($cuota_id = null) {
        $oSSERVICE = new SolicitudService();
        $cuota = $oSSERVICE->getDetalleCuota($cuota_id);
        $this->set('cuota',$cuota);        
    }
    
}
