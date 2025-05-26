<?php

class MutualProductoSolicitudesController extends MutualAppController {

    var $name = 'MutualProductoSolicitudes';
    var $autorizar = array('by_socio', 'imprimir_orden_pdf', 'aprobar', 'cargar_forma_pago', 'eliminar_forma_pago', 'pendientes_aprobar',
        'pendientes_aprobar_opago', 'imprimir_orden_pago', 'editOrdenPago', 'consumos_by_persona', 'creditos_pendientes_aprobar_opago',
        'imprimir_credito_mutual_pdf', 'creditos_pendientes_aprobar',
        'download_attach', 'download_attach_zipped', 'view', 'get_operaciones_pendientes_by_persona', 'borrar_documentacion_adjunta');

    function beforeFilter() {
        $this->Seguridad->allow($this->autorizar);
        parent::beforeFilter();
    }

    function by_persona($persona_id, $menuPersonas = 1) {
        App::import('model', 'Pfyj.Persona');
        $oPersona = new Persona();
        $persona = $oPersona->read(null, $persona_id);
//		$solicitudes = $this->MutualProductoSolicitud->getOrdenesBySocio($persona['Socio']['id']);
//		$solicitudes = $this->MutualProductoSolicitud->getOrdenesByPersona($persona['Persona']['id']);
//		$solicitudes['MutualProductoSolicitud']['regresar'] = 1;

        //NUEVO ESQUEMA DE BUSQUEDA
        App::import('model','Mutual.MutualProductoSolicitudService');
        $oSERVICIO = new MutualProductoSolicitudService();
        $solicitudes = $oSERVICIO->getByPersona($persona_id);
//         exit;
        
//         $conditions = array();
//         $conditions['MutualProductoSolicitud.persona_id'] = $persona_id;
//         $conditions['MutualProductoSolicitud.anulada'] = 0;
//         if (MODULO_V1) {
//             $conditions['MutualProductoSolicitud.tipo_orden_dto <>'] = Configure::read('APLICACION.tipo_orden_dto_credito');
//         }
//         $this->paginate = array(
//             'MutualProductoSolicitud' => array('limit' => 10,
//                 'order' => array('MutualProductoSolicitud.id' => 'desc'),
//                 'conditions' => $conditions,
//         ));
//         $solicitudes = $this->paginate();
//         if (!empty($solicitudes)) {

//             App::import('Model', 'mutual.MutualProductoSolicitud');
//             $oSOL = new MutualProductoSolicitud();

//             foreach ($solicitudes as $idx => $solicitud) {
//                 $solicitud = $oSOL->armaDatos($solicitud);
//                 $solicitud = $oSOL->getSolicitud($solicitud['MutualProductoSolicitud']['id']);
//                 $solicitudes[$idx] = $solicitud;
//             }
//         }
//         $solicitudes['MutualProductoSolicitud']['regresar'] = 1;
        $this->set('solicitudes', $solicitudes);
        $this->set('persona', $persona);
        $this->set('menuPersonas', $menuPersonas);
        $this->render();
    }

    function anuladas_by_persona($persona_id, $menuPersonas = 1) {
        App::import('model', 'Pfyj.Persona');
        $oPersona = new Persona();
        $persona = $oPersona->read(null, $persona_id);
//		$solicitudes = $this->MutualProductoSolicitud->getOrdenesBySocio($persona['Socio']['id']);
//         $solicitudes = $this->MutualProductoSolicitud->getOrdenesByPersona($persona['Persona']['id'], false, true);
//         $solicitudes['MutualProductoSolicitud']['regresar'] = 1;
        
        //NUEVO ESQUEMA DE BUSQUEDA
        App::import('model','Mutual.MutualProductoSolicitudService');
        $oSERVICIO = new MutualProductoSolicitudService();
        $solicitudes = $oSERVICIO->getByPersona($persona_id,true);
        
        $this->set('solicitudes', $solicitudes);
        $this->set('persona', $persona);
        $this->set('menuPersonas', $menuPersonas);
        $this->render();
    }

    /**
     * @deprecated
     * @param $persona_id
     * @param $menuPersonas
     * @return unknown_type
     */
    function by_persona_OLD($persona_id) {
        App::import('model', 'Pfyj.Persona');
        $this->Persona = new Persona();
        $persona = $this->Persona->read(null, $persona_id);
        $this->MutualProductoSolicitud->recursive = 4;
        $this->MutualProductoSolicitud->MutualProducto->bindModel(array('belongsTo' => array('Proveedor')));
        $solicitudes = $this->MutualProductoSolicitud->find('all', array('conditions' => array('MutualProductoSolicitud.socio_id' => $persona['Socio']['id']), 'order' => 'MutualProductoSolicitud.created DESC'));
        $this->set('solicitudes', $solicitudes);
        $this->set('persona', $persona);
        $this->render();
    }

    /**
     * @deprecated
     * ver metodo by_persona
     * @param $socio_id
     * @param $menuPersonas
     */
    function by_socio($socio_id = null, $menuPersonas = 1) {
        $this->MutualProductoSolicitud->Socio->bindModel(array('belongsTo' => array('Persona')));
        $socio = $this->MutualProductoSolicitud->Socio->read(null, $socio_id);
        $this->MutualProductoSolicitud->recursive = 4;
        $this->MutualProductoSolicitud->MutualProducto->bindModel(array('belongsTo' => array('Proveedor')));
        $solicitudes = $this->MutualProductoSolicitud->find('all', array('conditions' => array('MutualProductoSolicitud.socio_id' => $socio_id), 'order' => 'MutualProductoSolicitud.created DESC'));
        $this->set('solicitudes', $solicitudes);
        $this->set('socio', $socio);
        $this->set('menuPersonas', $menuPersonas);
        $this->render();
    }

    function add($persona_id, $menuPersonas = 1) {

        if (!empty($this->data)) {

            if ($this->MutualProductoSolicitud->nueva($this->data)) {
                $this->Auditoria->log();
                $this->Mensaje->okGuardar();
                $this->redirect('by_persona/' . $persona_id);
            } else {
                $this->Mensaje->error("Se produjo un error al intentar generar la Solicitud.");
            }
        }
        $this->MutualProductoSolicitud->recursive = 3;
        $this->MutualProductoSolicitud->Socio->bindModel(array('belongsTo' => array('Persona')));
        $this->MutualProductoSolicitud->Socio->Persona->bindModel(array('hasMany' => array('PersonaBeneficio'), 'hasOne' => array('Socio')));
        $persona = $this->MutualProductoSolicitud->Socio->Persona->read(null, $persona_id);
        $this->set('persona', $persona);
        $this->set('menuPersonas', $menuPersonas);
    }

    function imprimir_orden_pdf($id, $permanente = 0) {
//		Configure::write('debug',0);
//		$path = APP . 'plugins'. DS. 'mutual' . DS . 'views' . DS . 'templates' . DS . ($permanente==0 ? 'orden_consumo_mutual.html' : 'orden_servicio_mutual.html');
//		$tpl = $this->Util->openFile($path);
//		$tpl = $this->MutualProductoSolicitud->replaceCampos($tpl,$id);

        $orden = $this->MutualProductoSolicitud->read(null, $id);
        $orden = $this->MutualProductoSolicitud->armaDatos($orden, false, true);
        $this->set('orden', $orden);

//		$this->set('tpl',$tpl);
        $this->set('id', $id);
        $this->render('reportes/imprimir_orden_pdf', 'pdf');
    }

    function imprimir_orden_pago($id, $permanente = 0) {
        $this->persona = $this->MutualProductoSolicitud->importarModelo('Persona', 'pfyj');
        $orden = $this->MutualProductoSolicitud->read(null, $id);
        $orden = $this->MutualProductoSolicitud->armaDatos($orden, false);
        $this->oOPago = $this->MutualProductoSolicitud->importarModelo('OrdenPago', 'proveedores');
        $aOPago = $this->oOPago->getOrdenDePago($orden['MutualProductoSolicitud']['orden_pago_id']);
        $aPersona = $this->persona->getPersona($orden['MutualProductoSolicitud']['persona_id']);
        $aOPago['Proveedor'] = array(
            'id' => $aPersona['Persona']['id'],
            'razon_social' => $aPersona['Persona']['apenom'],
            'domicilio' => $aPersona['Persona']['domicilio'],
            'iva_concepto' => 'CONSUMIDOR FINAL',
            'formato_cuit' => $aPersona['Persona']['documento'],
            'nro_ingresos_brutos' => ''
        );
        $this->set('aOrdenDePago', $aOPago);
        $this->render('reportes/imprimir_opago', 'pdf');
    }

    function baja($persona_id = null) {
        $personas = null;
        $persona = null;
        $solicitudes = null;

        if (empty($persona_id)) {
            $condiciones = null;
            $this->Persona->recursive = 2;
            $search = null;
            if (!empty($this->data)) {
                $this->Session->del($this->name . '.search');
                $search = $this->data;
            } else if ($this->Session->check($this->name . '.search')) {
                $search = $this->Session->read($this->name . '.search');
                $this->data = $search;
            }

            $condiciones = array(
                'Persona.tipo_documento  LIKE ' => $search['Persona']['tipo_documento'] . "%",
                'Persona.documento LIKE ' => $search['Persona']['documento'] . "%",
                'Persona.apellido LIKE ' => $search['Persona']['apellido'] . "%",
                'Persona.nombre LIKE ' => $search['Persona']['nombre'] . "%",
                'Socio.id <>' => 0
            );
            $this->Session->write($this->name . '.search', $search);
            $this->paginate = array(
                'limit' => 30,
                'order' => array('Persona.apellido,Persona.nombre ASC')
            );

//			$condiciones = array(
//								'Persona.tipo_documento  LIKE ' => $this->data['Persona']['tipo_documento'] ."%",
//								'Persona.documento LIKE ' => $this->data['Persona']['documento']."%",
//								'Persona.apellido LIKE ' => $this->data['Persona']['apellido']."%",
//								'Persona.nombre LIKE ' => $this->data['Persona']['nombre']."%",			
//							);	
//			$this->paginate = array(
//									'limit' => 30,
//									'order' => array('Persona.apellido' => 'ASC', 'Persona.nombre' => 'ASC')
//									);
            App::import('model', 'Pfyj.Persona');
            $this->Persona = new Persona();
            $personas = $this->paginate('Persona', $condiciones);
        } else {

            if (!empty($this->data)) {
                if (isset($this->data['MutualProductoSolicitud']['procesar_baja']) && $this->data['MutualProductoSolicitud']['procesar_baja'] == 1) {
                    if (isset($this->data['MutualProductoSolicitud']['check_id']) && count($this->data['MutualProductoSolicitud']['check_id']) != 0) {
                        $baja = true;
                        foreach ($this->data['MutualProductoSolicitud']['check_id'] as $id => $sel) {
                            if (!$this->MutualProductoSolicitud->baja($id)) {
                                $baja = false;
                                break;
                            }
                        }
                        if ($baja) {
                            $this->Mensaje->ok("La o las Ordenes de Consumo / Servicio fueron dadas de baja del Sistema!.");
                        } else {
                            $this->Mensaje->error("SE PRODUJO UN ERROR AL INTENTAR DAR DE BAJA LA O LAS ORDENES!.");
                        }
                        $this->redirect('baja/' . $persona_id);
                    } else {
                        $this->Mensaje->error("Debe indicar al menos una Orden de Consumo / Servicio!.");
                    }
                }
            }


            App::import('model', 'Pfyj.Persona');
            $this->Persona = new Persona();
            $persona = $this->Persona->read(null, $persona_id);
            $this->MutualProductoSolicitud->recursive = 4;
            $this->MutualProductoSolicitud->MutualProducto->bindModel(array('belongsTo' => array('Proveedor')));
            $solicitudes = $this->MutualProductoSolicitud->find('all', array('conditions' => array('MutualProductoSolicitud.socio_id' => $persona['Socio']['id']), 'order' => 'MutualProductoSolicitud.created DESC'));
            $this->set('solicitudes', $solicitudes);
            $this->set('persona', $persona);
        }

        $this->set('personas', $personas);
        $this->set('solicitudes', $solicitudes);
        $this->set('persona', $persona);
    }

    function modificar_importe_orden_permanente($id = null) {

        if (!empty($this->data)) {

            if (empty($this->data['MutualProductoSolicitud']['importe_cuota']))
                $this->data['MutualProductoSolicitud']['importe_cuota'] = 0;

            if (!is_nan($this->data['MutualProductoSolicitud']['importe_cuota']) && $this->data['MutualProductoSolicitud']['importe_cuota'] != 0) {
                $recalcula = (isset($this->data['MutualProductoSolicitud']['recalcular_cuotas']) ? true : false);
                $periodoDesde = (isset($this->data['MutualProductoSolicitud']['aplicar_desde']) ? $this->data['MutualProductoSolicitud']['aplicar_desde'] : null);
                if ($this->MutualProductoSolicitud->modificarImportePermanente($this->data['MutualProductoSolicitud']['id'], $this->data['MutualProductoSolicitud']['importe_cuota'], $recalcula, $periodoDesde)) {
                    $this->Mensaje->okGuardar();
                    $this->redirect('by_persona/' . $this->data['MutualProductoSolicitud']['persona_id']);
                } else {
                    $this->Mensaje->error("Se produjo un error al modificar el importe de la Solicitud.");
                }
            } else {
                $this->Mensaje->error("ERROR: El importe debe indicar el nuevo importe!.");
            }


//			$this->Mensaje->error("Modificar Datos: FUNCION NO HABILITADA!");
        }
//         $this->MutualProductoSolicitud->recursive = 4;
//         $this->MutualProductoSolicitud->Socio->bindModel(array('belongsTo' => array('Persona')));
//         $solicitud = $this->MutualProductoSolicitud->read(null, $id);
//         $solicitud = $this->MutualProductoSolicitud->armaDatos($solicitud);
        
        $solicitud = $this->MutualProductoSolicitud->getSolicitud($id,false,false);
        
        $this->set('solicitud', $solicitud);
        
//         $this->MutualProductoSolicitud->Socio->Persona->bindModel(array('hasMany' => array('PersonaBeneficio'), 'hasOne' => array('Socio')));
//         $persona = $this->MutualProductoSolicitud->Socio->Persona->read(null, $solicitud['Socio']['Persona']['id']);
        
        App::import('model','pfyj.Persona');
        $oPersona = new Persona();
        $persona = $oPersona->read(null, $solicitud['MutualProductoSolicitud']['persona_id']);
        
        $this->set('persona', $persona);
        $this->set('menuPersonas', 1);
        $periodos = null;
        if ($solicitud['MutualProductoSolicitud']['orden_descuento_id'] != 0):
            //saco los periodos
            App::import('model', 'Mutual.OrdenDescuentoCuota');
            $oCuota = new OrdenDescuentoCuota();
            $periodos = $oCuota->periodosBySocio($solicitud['MutualProductoSolicitud']['socio_id'], false, $solicitud['MutualProductoSolicitud']['orden_descuento_id'], true);
            $periodos = Set::extract('/OrdenDescuentoCuota/periodo', $periodos);
        endif;
        $this->set('periodos', $periodos);
    }

    function view($id, $showOrdenDto = 0) {
//         $this->MutualProductoSolicitud->Socio->bindModel(array('belongsTo' => array('Persona')));
//         $solicitud = $this->MutualProductoSolicitud->read(null, $id);
//         if (empty($solicitud))
//             parent::noDisponible();
//         $solicitud = $this->MutualProductoSolicitud->armaDatos($solicitud);
        $solicitud = $this->MutualProductoSolicitud->getSolicitud($id,false,false);
        $this->set('solicitud', $solicitud);
        $this->set('showOrdenDto', $showOrdenDto);
        $this->render();
    }

    function pendientes_aprobar() {
        $this->redirect('pendientes_aprobar_opago');
        if (isset($this->params['url']['ORD']) && !empty($this->params['url']['ORD'])) {
            $this->set('mutual_producto_solicitud_id', $this->params['url']['ORD']);
            $solicitud = $this->MutualProductoSolicitud->read(null, $this->params['url']['ORD']);
            $this->set('solicitud', $solicitud);
            $this->render('pendiente_aprobar_formulario');
        }
        if (!empty($this->data)) {
            if ($this->data['MutualProductoSolicitud']['aprobar'] == 1) {
                if ($this->MutualProductoSolicitud->aprobar($this->data['MutualProductoSolicitud']['id'])) {
                    $this->Mensaje->ok("LA ORDEN DE CONSUMO / SERVICIO #" . $this->data['MutualProductoSolicitud']['id'] . " FUE APROBADA CORRECTAMENTE!.");
                    $this->data = null;
                }
            }
        }
        $solicitudes = $this->MutualProductoSolicitud->getNoAprobadas();

        App::import('Model', 'mutual.MutualServicioSolicitud');
        $oSERV = new MutualServicioSolicitud();

        $servicios = $oSERV->getNoAprobadas();

        $aAprobar = array();
        $row = array();

        if (!empty($solicitudes)):

            foreach ($solicitudes as $solicitud):

                $row['tipo_orden_dto'] = $solicitud['MutualProductoSolicitud']['tipo_orden_dto'];
                $row['id'] = $solicitud['MutualProductoSolicitud']['id'];
                $row['tipo_numero'] = $solicitud['MutualProductoSolicitud']['tipo_numero'];
                $row['beneficiario'] = $solicitud['MutualProductoSolicitud']['beneficiario'];
                $row['beneficiario_apenom'] = $solicitud['MutualProductoSolicitud']['beneficiario_apenom'];
                $row['beneficiario_tdocndoc'] = $solicitud['MutualProductoSolicitud']['beneficiario_tdocndoc'];
                $row['socio_id'] = $solicitud['MutualProductoSolicitud']['socio_id'];
                $row['beneficio_str'] = $solicitud['MutualProductoSolicitud']['beneficio_str'];
                $row['fecha'] = $solicitud['MutualProductoSolicitud']['fecha'];
                $row['fecha_pago'] = $solicitud['MutualProductoSolicitud']['fecha_pago'];
                $row['periodo_ini'] = $solicitud['MutualProductoSolicitud']['periodo_ini'];
                $row['proveedor_producto'] = $solicitud['MutualProductoSolicitud']['proveedor_producto'];
                $row['importe_total'] = $solicitud['MutualProductoSolicitud']['importe_total'];
                $row['cuotas'] = $solicitud['MutualProductoSolicitud']['cuotas'];
                $row['importe_cuota'] = $solicitud['MutualProductoSolicitud']['importe_cuota'];
                $row['permanente'] = $solicitud['MutualProductoSolicitud']['permanente'];
                $row['sin_cargo'] = $solicitud['MutualProductoSolicitud']['sin_cargo'];
                $row['emitida_por'] = $solicitud['MutualProductoSolicitud']['user_created'] . ' - ' . $solicitud['MutualProductoSolicitud']['created'];

                array_push($aAprobar, $row);



            endforeach;

        endif;

        if (!empty($servicios)):

            foreach ($servicios as $servicio):

                $row['tipo_orden_dto'] = $servicio['MutualServicioSolicitud']['tipo_orden_dto'];
                $row['id'] = $servicio['MutualServicioSolicitud']['id'];
                $row['tipo_numero'] = $servicio['MutualServicioSolicitud']['tipo_numero'];
                $row['beneficiario'] = $servicio['MutualServicioSolicitud']['titular_tdocndoc_apenom'];
                $row['beneficiario_apenom'] = $servicio['MutualServicioSolicitud']['titular_apenom'];
                $row['beneficiario_tdocndoc'] = $servicio['MutualServicioSolicitud']['titular_tdocndoc'];
                $row['socio_id'] = $servicio['MutualServicioSolicitud']['socio_id'];
                $row['beneficio_str'] = $servicio['MutualServicioSolicitud']['beneficio'];
                $row['fecha'] = $servicio['MutualServicioSolicitud']['fecha_emision'];
                $row['fecha_pago'] = $servicio['MutualServicioSolicitud']['fecha_alta_servicio'];
                $row['periodo_ini'] = $servicio['MutualServicioSolicitud']['periodo_desde'];
                $row['proveedor_producto'] = $servicio['MutualServicioSolicitud']['mutual_proveedor_servicio'];
                $row['importe_total'] = $servicio['MutualServicioSolicitud']['importe_mensual_total'];
                $row['cuotas'] = $servicio['MutualServicioSolicitud']['cuotas'];
                $row['importe_cuota'] = ($row['cuotas'] != 0 ? $servicio['MutualServicioSolicitud']['importe_cuota'] : $servicio['MutualServicioSolicitud']['importe_mensual_total']);
                $row['permanente'] = ($row['cuotas'] != 0 ? 0 : 1);
                $row['sin_cargo'] = 0;
                $row['emitida_por'] = $servicio['MutualServicioSolicitud']['user_created'] . ' - ' . $servicio['MutualServicioSolicitud']['created'];

                array_push($aAprobar, $row);



            endforeach;

        endif;

        $aAprobar = Set::sort($aAprobar, '{n}.beneficiario_apenom', 'asc');


        $this->set('aAprobar', $aAprobar);
        $this->set('solicitudes', $solicitudes);
    }

    function aprobar($persona_id = null) {
        $this->redirect('pendientes_aprobar_opago');
//		$personas = null;
//		$persona = null;
//		$solicitudes = null;
//		
//		if(empty($persona_id)){
//			$condiciones = null;		
//			$this->Persona->recursive = 2;
//			$search = null;
//			if(!empty($this->data)){
//				$this->Session->del($this->name.'.search');
//				$search = $this->data;
//			}else if($this->Session->check($this->name.'.search')){
//				$search = $this->Session->read($this->name.'.search');
//				$this->data = $search; 
//			}
//			
//			$condiciones = array(
//								'Persona.tipo_documento  LIKE ' => $search['Persona']['tipo_documento'] ."%",
//								'Persona.documento LIKE ' => $search['Persona']['documento']."%",
//								'Persona.apellido LIKE ' => $search['Persona']['apellido']."%",
//								'Persona.nombre LIKE ' => $search['Persona']['nombre']."%",
//								'Socio.id <>' => 0			
//							);	
//			$this->Session->write($this->name.'.search', $search);
//			$this->paginate = array(
//									'limit' => 30,
//									'order' => array('Persona.apellido,Persona.nombre ASC')
//									);		
//			
//			App::import('model','Pfyj.Persona');
//			$this->Persona = new Persona();	
//			$personas = $this->paginate('Persona',$condiciones);					
//					
//		}else{
//			$this->Session->del($this->name.'.search');
//			if(!empty($this->data)){
//				if($this->data['MutualProductoSolicitud']['aprobar'] == 1){
//					if($this->MutualProductoSolicitud->aprobar($this->data['MutualProductoSolicitud']['id'])){
//						$this->Mensaje->ok("LA ORDEN DE CONSUMO / SERVICIO #".$this->data['MutualProductoSolicitud']['id']." FUE APROBADA CORRECTAMENTE!.");
////					}else{
////						$this->Mensaje->error("SE PRODUJO UN ERROR AL INTENTAR APROBAR LA ORDEN DE CONSUMO / SERVICIO #".$this->data['MutualProductoSolicitud']['id']);
//					}
//				}
//			}			
//			
//			App::import('model','Pfyj.Persona');
//			$oPersona = new Persona();			
//			//CARGO LA ORDEN
//			if(isset($this->params['url']['ORD']) && !empty($this->params['url']['ORD'])){
//				$this->set('mutual_producto_solicitud_id',$this->params['url']['ORD']);	
//				$persona = $oPersona->read(null,$persona_id);
//				$this->set('persona',$persona);
//				$this->set('solicitudes',null);
//			}else{
//				$this->set('mutual_producto_solicitud_id',NULL);	
//				$persona = $oPersona->read(null,$persona_id);
//				$this->MutualProductoSolicitud->recursive = 4;
//				$this->MutualProductoSolicitud->MutualProducto->bindModel(array('belongsTo' => array('Proveedor')));
//				$solicitudes = $this->MutualProductoSolicitud->getBySocioNoAprobadas($persona['Socio']['id']);
//				$this->set('solicitudes',$solicitudes);
//				$this->set('persona',$persona);
//				
//			}
//			
//		}
//		
//		$this->set('personas',$personas);
//		$this->set('solicitudes',$solicitudes);
//		$this->set('persona',$persona);
    }

    function cargar_forma_pago($mutual_producto_solicitud_id) {
        App::import('model', 'Mutual.MutualProductoSolicitudPago');
        $oMPS = new MutualProductoSolicitudPago();

        if (!empty($this->data)) {
            $oMPS->grabarPago($this->data, $mutual_producto_solicitud_id);
        }

        $pagos = $oMPS->getPagosBySolicitud($mutual_producto_solicitud_id);
        $this->set('pagos', $pagos);
        $this->render('grilla_forma_pago_ajax', 'ajax');
    }

    function eliminar_forma_pago($formaPagoId) {
        App::import('model', 'Mutual.MutualProductoSolicitudPago');
        $oMPS = new MutualProductoSolicitudPago();

        $pago = $oMPS->read('mutual_producto_solicitud_id', $formaPagoId);
        $oMPS->del($formaPagoId);
        $this->redirect('/mutual/mutual_producto_solicitudes/cargar_forma_pago/' . $pago['MutualProductoSolicitudPago']['mutual_producto_solicitud_id']);
    }

    function del($id = null) {
        if (empty($id))
            parent::noAutorizado();
        if ($this->MutualProductoSolicitud->anular($id)) {
            $this->Mensaje->ok('La Solicitud # ' . $id . ' fue Anulada correctamente');
        } else {
            $this->Mensaje->error('La Solicitud # ' . $id . ' no fue Anulada');
        }
        $this->redirect('pendientes_aprobar_opago');
    }

    function pendientes_aprobar_opago() {
        $this->Session->del('grilla_pagos');
        if (isset($this->params['url']['ORD']) && !empty($this->params['url']['ORD'])) {
            $this->set('mutual_producto_solicitud_id', $this->params['url']['ORD']);
            $solicitud = $this->MutualProductoSolicitud->read(null, $this->params['url']['ORD']);
            $solicitud = $this->MutualProductoSolicitud->armaDatos($solicitud);

            # 1) ######################################################
            $this->set('uuid', $this->MutualProductoSolicitud->generarPIN(20));
            //este UUID se guarda como hidden en el formulario del detalle
            #######################################################

            $this->chqCartera = $this->MutualProductoSolicitud->importarModelo('BancoChequeTercero', 'cajabanco');
            $chqCarteras = $this->chqCartera->getChequeCartera();


            $this->set('chqCarteras', $chqCarteras);
            $this->set('solicitud', $solicitud);
            $this->render('pendiente_aprobar_formulario_opago');
            return;
        }

        $nroSolSearch = $nDocSearch = $apeSearch = $nomSearch = NULL;

        if (!empty($this->data)) {

            if (isset($this->data['MutualProductoSolicitud']['search'])) {
                $nroSolSearch = $this->data['MutualProductoSolicitud']['numero'];
                $nDocSearch = $this->data['Persona']['documento'];
                $apeSearch = $this->data['Persona']['apellido'];
                $nomSearch = $this->data['Persona']['nombre'];
            }
            if (isset($this->data['MutualProductoSolicitud']['aprobar']) && $this->data['MutualProductoSolicitud']['aprobar'] == 1) {

                # 5) ######################################################
                # reconstruyo el campo renglonesSerialize con los datos de la sessi�n 
                # con el uuid para que el modelo ni se entere del cambio
                if (!isset($this->data['Movimiento']['renglonesSerialize'])) {
                    $renglones = $this->Session->read('grilla_pagos_' . $this->data['Movimiento']['uuid']);
                    $this->data['Movimiento']['renglonesSerialize'] = base64_encode(serialize($renglones));
                }
                ######################################################

                if ($this->MutualProductoSolicitud->aprobar_opago($this->data)) {
                    $this->Mensaje->ok("LA ORDEN DE CONSUMO / SERVICIO #" . $this->data['MutualProductoSolicitud']['id'] . " FUE APROBADA CORRECTAMENTE!.");
                    $solicitud = $this->MutualProductoSolicitud->read(null, $this->data['MutualProductoSolicitud']['id']);
                    $this->data = null;
                    if ($solicitud['MutualProductoSolicitud']['orden_pago_id'] > 0):
                        $this->redirect('editOrdenPago/' . $solicitud['MutualProductoSolicitud']['orden_pago_id'] . '/' . $solicitud['MutualProductoSolicitud']['id']);
                    endif;
                }
            }
        }
//		$solicitudes = $this->MutualProductoSolicitud->getNoAprobadas();
        $estado = 'MUTUESTA0002';
        $solicitudes = $this->MutualProductoSolicitud->getNoAprobadas2($estado, $nroSolSearch, $nDocSearch, $apeSearch, $nomSearch);
        $this->set('solicitudes', $solicitudes);
    }

    function editOrdenPago($nOrdenPago = 0, $producto = 0, $regresar = 0) {
        $redirect = 'pendientes_aprobar_opago';

        $this->persona = $this->MutualProductoSolicitud->importarModelo('Persona', 'pfyj');
        $orden = $this->MutualProductoSolicitud->read(null, $producto);
        $orden = $this->MutualProductoSolicitud->armaDatos($orden, false);

        if ($regresar == 1)
            $redirect = 'by_persona/' . $orden['MutualProductoSolicitud']['persona_id'] . '/1';

        if (empty($nOrdenPago))
            $this->redirect($redirect);


        if (!empty($this->data)):
            if ($this->MutualProductoSolicitud->anularOrdenPago($producto)):
                $this->Mensaje->ok('LA ORDEN DE PAGO SE ANULO CORRECTAMENTE');
            else:
                $this->Mensaje->errorBorrar();
            endif;

            $this->redirect('pendientes_aprobar_opago');

        endif;


        $this->oOPago = $this->MutualProductoSolicitud->importarModelo('OrdenPago', 'proveedores');
        $aOPago = $this->oOPago->getOrdenDePago($orden['MutualProductoSolicitud']['orden_pago_id']);

        $aOPago['OrdenPago']['action'] = "editOrdenPago/" . $nOrdenPago . '/' . $producto;
//		$aOPago['OrdenPago']['url'] = '/mutual/mutual_producto_solicitudes/editOrdenPago/0/' . $producto;
        $aOPago['OrdenPago']['url'] = '/mutual/mutual_producto_solicitudes/editOrdenPago/0/' . $producto . '/' . $regresar;

        $aPersona = $this->persona->getPersona($orden['MutualProductoSolicitud']['persona_id']);
        $aOPago['Proveedor'] = array(
            'id' => $aPersona['Persona']['id'],
            'razon_social' => $aPersona['Persona']['apenom'],
            'domicilio' => $aPersona['Persona']['domicilio'],
            'iva_concepto' => 'CONSUMIDOR FINAL',
            'formato_cuit' => $aPersona['Persona']['documento'],
            'nro_ingresos_brutos' => ''
        );
        $this->set('aOrdenPago', $aOPago);
    }

//	function editOrdenPago($nOrdenPago=0, $nroSolicitud=0){
//		
//		if(empty($nOrdenPago)) $this->redirect('a_verificar');
//
//		$solicitud = $this->Solicitud->getSolicitud($nroSolicitud);
//		
//		if(!empty($this->data)):
//			if ($this->Solicitud->anularOrdenPago($nOrdenPago)):
//				$solicitud['Solicitud']['orden_pago_id'] = 0;
//				$this->Solicitud->save($solicitud);
//				$this->Mensaje->ok('LA ORDEN DE PAGO SE ANULO CORRECTAMENTE');
//				$this->redirect('addOrdenPago/' . $nroSolicitud);
//			else:
//				$this->Mensaje->errorBorrar();
//			endif;
//			
//				
//		endif;
//
//		
//		$this->oOPago = $this->Solicitud->importarModelo('OrdenPago', 'proveedores');
//		$aOPago = $this->oOPago->getOrdenDePago($solicitud['Solicitud']['orden_pago_id']);
//
//		$aOPago['OrdenPago']['action'] = "editOrdenPago/" . $nOrdenPago . '/' . $nroSolicitud;
//		$aOPago['OrdenPago']['url'] = '/v1/solicitudes/editOrdenPago/0/' . $nroSolicitud;
//		
//		$aOPago['Proveedor'] = array(
//			'id' => $solicitud['PersonaV1']['id_persona'],
//			'razon_social' => $solicitud['PersonaV1']['apellido'] . ' ' . $solicitud['PersonaV1']['nombre'],
//			'domicilio' => $solicitud['PersonaV1']['calle'] . ' Nro.:' . $solicitud['PersonaV1']['nro_calle'],
//			'iva_concepto' => 'CONSUMIDOR FINAL',
//			'formato_cuit' => $solicitud['PersonaV1']['documento'],
//			'nro_ingresos_brutos' => ''
//		);
//		
//		
//		$this->set('aOrdenPago', $aOPago);
//		
//	}


    function consumos_by_persona($persona_id, $menuPersonas = 1) {
        App::import('model', 'Pfyj.Persona');
        $this->Persona = new Persona();
        $persona = $this->Persona->read(null, $persona_id);
        //		$solicitudes = $this->MutualProductoSolicitud->getOrdenesBySocio($persona['Socio']['id']);
        $solicitudes = $this->MutualProductoSolicitud->getOrdenesByPersona($persona['Persona']['id']);
        $solicitudes['MutualProductoSolicitud']['regresar'] = 1;
        $this->set('solicitudes', $solicitudes);
        $this->set('persona', $persona);
        $this->set('menuPersonas', $menuPersonas);
        $this->render();
    }

    function nuevo_credito($persona_id, $ayudaEcono = 0) {
        $this->MutualProductoSolicitud->recursive = 3;
        $this->MutualProductoSolicitud->Socio->bindModel(array('belongsTo' => array('Persona')));
        $this->MutualProductoSolicitud->Socio->Persona->bindModel(array('hasMany' => array('PersonaBeneficio'), 'hasOne' => array('Socio')));
        $persona = $this->MutualProductoSolicitud->Socio->Persona->read(null, $persona_id);

        if (!empty($this->data)) {

            if ($this->MutualProductoSolicitud->nuevaSolicitudCredito($this->data)) {
                $this->redirect('by_persona/' . $persona_id);
            } else {
                $this->Mensaje->errores("ERROR AL GENERAR LA SOLICITUD DE CREDITO", $this->MutualProductoSolicitud->notificaciones);
                //$this->Mensaje->error("ERROR AL GENERAR LA SOLICITUD DE CREDITO");
            }
        }
        App::import('model', 'mutual.CancelacionOrden');
        $oCANC = new CancelacionOrden();
        $this->set('persona', $persona);
        $this->set('menuPersonas', 1);
        if (isset($persona['Socio']['id']) && !empty($persona['Socio']['id']))
            $this->set('cancelaciones', $oCANC->getPendientes($persona['Socio']['id']));
        else
            $this->set('cancelaciones', null);

        App::import('model', 'ventas.Vendedor');
        $oVENDEDOR = new Vendedor();
        $this->set('vendedores', $oVENDEDOR->getVendedores(TRUE, TRUE));
        if ($ayudaEcono == 1)
            $this->render('nuevo_credito_ayuda_economica');
    }

    function imprimir_credito_mutual_pdf($id = null, $talonControl = 0) {
        
        if (empty($id)){ parent::noDisponible();}
        
        $ordenRead = $this->MutualProductoSolicitud->read(null, $id);
        
        if (empty($ordenRead)) {parent::noDisponible();}
        
        $orden = $this->MutualProductoSolicitud->armaDatos($ordenRead, true, true);
        
        App::import('model', 'seguridad.Usuario');
        $oUSER = new Usuario();
        $vendedorId = $oUSER->get_vendedorId_logon(true);
        $vendedorArray = (!empty($vendedorId) ? explode(',', $vendedorId) : NULL) ;
        
        if(!empty($vendedorArray) && !in_array($orden['MutualProductoSolicitud']['vendedor_id'], $vendedorArray)){
            echo "LA SOLICITUD #$id PERTENECE A OTRO VENDEDOR";
            exit();
        }
        
        $this->set('orden', $orden);
        $this->set('id', $id);
        $this->set('imprime_talon_control', $talonControl);
        
        $MODELO_DEFAULTDatoGlobal = $this->MutualProductoSolicitud->GlobalDato('concepto_2', 'MUTUIMPR1001'); 
        $MODELO_DEFAULT = (!empty($MODELO_DEFAULTDatoGlobal) ? $MODELO_DEFAULTDatoGlobal : "imprimir_credito_mutual_pdf");
        
//                debug($orden);
        $render = (!empty($orden['MutualProductoSolicitud']['proveedor_plan_modelo_solicitud_2']) ? $orden['MutualProductoSolicitud']['proveedor_plan_modelo_solicitud_2'] : $MODELO_DEFAULT);
        //si es una orden que no sea un credito cambio el render
        if ($orden['MutualProductoSolicitud']['tipo_orden_dto'] !== Configure::read('APLICACION.tipo_orden_dto_credito')) {
            $render = 'imprimir_orden_pdf';
        }

//                $render = ($orden['MutualProductoSolicitud']['ayuda_economica'] == 1 ? "imprimir_ayuda_mutual_pdf" : "imprimir_credito_mutual_pdf");
//                debug($render);
//                exit;
        $this->render('reportes/' . $render, 'pdf');
    }

    function creditos_pendientes_aprobar($id = null) {

        if (isset($this->params['url']['ORD']) && !empty($this->params['url']['ORD'])) {
            $this->set('mutual_producto_solicitud_id', $this->params['url']['ORD']);
            $solicitud = $this->MutualProductoSolicitud->read(null, $this->params['url']['ORD']);
            $solicitud = $this->MutualProductoSolicitud->armaDatos($solicitud);
            $this->set('solicitud', $solicitud);
            $this->render('creditos_pendientes_aprobar_formulario');
        }

        if (!empty($this->data)) {
            if ($this->data['MutualProductoSolicitud']['aprobar'] == 1) {
//                $organismoOriginal = $this->data['MutualProductoSolicitud']['organismo'];
//                $organismoNuevo = $this->data['MutualProductoSolicitud']['organismo_reasignado'];
//                $reasignar = ($organismoOriginal === $organismoNuevo ? null : $organismoNuevo);
                $reasignar = null;

                if ($this->MutualProductoSolicitud->aprobar($this->data, $reasignar)) {
//				if($this->MutualProductoSolicitud->aprobar($this->data['MutualProductoSolicitud']['id'],$reasignar)){
                    $this->Mensaje->ok("LA SOLICITUD DE CREDITO #" . $this->data['MutualProductoSolicitud']['id'] . " FUE APROBADA CORRECTAMENTE!.");
                    // $this->redirect('pendientes_aprobar_opago/' . $this->data['MutualProductoSolicitud']['id']);
                    $this->redirect('pendientes_aprobar_opago/');
                } else {
                    $this->Mensaje->errores("LA SOLICITUD DE CREDITO #" . $this->data['MutualProductoSolicitud']['id'] . " NO SE PUDO APROBAR!", $this->MutualProductoSolicitud->notificaciones);
// 					debug($this->MutualProductoSolicitud->notificaciones);
// 					exit;
                    // $this->Mensaje->error("ERROR AL APROBAR LA SOLICITUD");
                    $this->redirect('pendientes_aprobar_opago/');
                    // 					$this->redirect('creditos_pendientes_aprobar/?ORD=' . $this->data['MutualProductoSolicitud']['id']);
                }
            }
        }

        if (!empty($id)) {
            $solicitud = $this->MutualProductoSolicitud->read(null, $id);
            if ($solicitud['MutualProductoSolicitud']['aprobada'] != 1)
                $this->redirect('creditos_pendientes_aprobar');
            $this->set('solicitud', $solicitud);
            $this->render('creditos_pendientes_aprobar_confirm');
        }

        $solicitudes = $this->MutualProductoSolicitud->getCreditosNoAprobados();
        $aAprobar = array();
        $row = array();

        // 		if(!empty($solicitudes)):
        // 			foreach($solicitudes as $solicitud):
        // 				$row['tipo_orden_dto'] = $solicitud['MutualProductoSolicitud']['tipo_orden_dto'];
        // 				$row['id'] = $solicitud['MutualProductoSolicitud']['id'];
        // 				$row['nro_print'] = $solicitud['MutualProductoSolicitud']['nro_print'];
        // 				$row['tipo_numero'] = $solicitud['MutualProductoSolicitud']['tipo_numero'];
        // 				$row['beneficiario'] = $solicitud['MutualProductoSolicitud']['beneficiario'];
        // 				$row['beneficiario_apenom'] = $solicitud['MutualProductoSolicitud']['beneficiario_apenom'];
        // 				$row['beneficiario_tdocndoc'] = $solicitud['MutualProductoSolicitud']['beneficiario_tdocndoc'];
        // 				$row['socio_id'] = $solicitud['MutualProductoSolicitud']['socio_id'];
        // 				$row['beneficio_str'] = $solicitud['MutualProductoSolicitud']['beneficio_str'];
        // 				$row['fecha'] = $solicitud['MutualProductoSolicitud']['fecha'];
        // 				$row['fecha_pago'] = $solicitud['MutualProductoSolicitud']['fecha_pago'];
        // 				$row['periodo_ini'] = $solicitud['MutualProductoSolicitud']['periodo_ini'];
        // 				$row['proveedor_producto'] = $solicitud['MutualProductoSolicitud']['proveedor_producto'];
        // 				$row['importe_total'] = $solicitud['MutualProductoSolicitud']['importe_total'];
        // 				$row['cuotas'] = $solicitud['MutualProductoSolicitud']['cuotas'];
        // 				$row['importe_cuota'] = $solicitud['MutualProductoSolicitud']['importe_cuota'];
        // 				$row['permanente'] = $solicitud['MutualProductoSolicitud']['permanente'];
        // 				$row['sin_cargo'] = $solicitud['MutualProductoSolicitud']['sin_cargo'];
        // 				$row['emitida_por'] = $solicitud['MutualProductoSolicitud']['user_created'] .' - '.$solicitud['MutualProductoSolicitud']['created'];
        // 				$row['importe_percibido'] = $solicitud['MutualProductoSolicitud']['importe_percibido'];
        // 				array_push($aAprobar,$row);
        // 			endforeach;
        // 		endif;
        // 		$aAprobar = Set::sort($aAprobar, '{n}.beneficiario_apenom', 'asc');
        $this->set('aAprobar', $aAprobar);
        $this->set('solicitudes', $solicitudes);
    }

//	function reasignar(){
//		$show = 0;
//		$nroSolicitud = null;
//		$solicitud = null;
//		if(!empty($this->params['pass'][0]) && $this->params['url']['do'] == 'ANULAR'){
//			if($this->MutualProductoSolicitud->reasignarProveedor($this->params['pass'][0])){
//				$this->Mensaje->ok("LA ANULACION DE LA REASIGNACION DE LA SOLICITUD #".$this->params['pass'][0]." FUE PROCESADA CORRECTAMENTE!" );
//				$this->redirect('reasignar');
//			}
//		}
//		if(!empty($this->data)){
//			$show = 1;
//			$nroSolicitud = $this->data['Solicitud']['numero'];
//			$this->MutualProductoSolicitud->Socio->bindModel(array('belongsTo' => array('Persona')));
//			$solicitud = $this->MutualProductoSolicitud->read(null,$this->data['Solicitud']['numero']);
//			if(empty($solicitud)) parent::noAutorizado();
//			$solicitud = $this->MutualProductoSolicitud->armaDatos($solicitud);
//			if($solicitud['MutualProductoSolicitud']['proveedor_reasignable'] == 0){
//				$msg = "LA SOLICITUD #$nroSolicitud PERTENECE A UN PROVEEDOR NO REASIGNABLE (".$solicitud['MutualProductoSolicitud']['proveedor'].")";
//				$this->Mensaje->error($msg);
//				$solicitud = null;
//				$show = 0;
//			}
//			if($solicitud['MutualProductoSolicitud']['aprobada'] == 1){
//				$msg = "LA SOLICITUD #$nroSolicitud SE ENCUENTRA APROBADA";
//				$this->Mensaje->error($msg);
//				$solicitud = null;
//				$show = 0;
//			}
//			if($this->data['MutualProductoSolicitud']['reasigna'] == 1){
//				if($this->MutualProductoSolicitud->reasignarProveedor($this->data['Solicitud']['numero'],$this->data['Solicitud']['reasignar_proveedor_id'])){
//					$this->Mensaje->ok("LA SOLICITUD #".$this->data['Solicitud']['numero']." FUE REASIGNADA CORRECTAMENTE!" );
//					$this->redirect('reasignar');
//				}else{
//					$this->Mensaje->error("SE PRODUJO UN ERROR AL REASIGNAR LA SOLICITUD #".$this->data['Solicitud']['numero']);
//				}
//			}
//		}
//		$this->set('show',$show);
//		$this->set('solicitud',$solicitud);
//		$this->set('nroSolicitud',$nroSolicitud);
//	
//	}


    function reasignar() {
        $show = 0;
        $nroSolicitud = null;
        $solicitud = null;
        if (!empty($this->params['pass'][0]) && $this->params['url']['do'] == 'ANULAR') {
            if ($this->MutualProductoSolicitud->reasignarProveedor($this->params['pass'][0])) {
                $this->Mensaje->ok("LA ANULACION DE LA REASIGNACION DE LA SOLICITUD #" . $this->params['pass'][0] . " FUE PROCESADA CORRECTAMENTE!");
                $this->redirect('reasignar');
            }
        }
        if (!empty($this->data)) {
            $show = 1;
            $nroSolicitud = $this->data['MutualProductoSolicitud']['numero'];
            $this->MutualProductoSolicitud->Socio->bindModel(array('belongsTo' => array('Persona')));
            $solicitud = $this->MutualProductoSolicitud->read(null, $this->data['MutualProductoSolicitud']['numero']);
            if (empty($solicitud))
                parent::noAutorizado();
            $solicitud = $this->MutualProductoSolicitud->armaDatos($solicitud);
            if ($solicitud['MutualProductoSolicitud']['proveedor_reasignable'] == 0) {
                $msg = "LA SOLICITUD #$nroSolicitud PERTENECE A UN PROVEEDOR NO REASIGNABLE (" . $solicitud['MutualProductoSolicitud']['proveedor'] . ")";
                $this->Mensaje->error($msg);
                $solicitud = null;
                $show = 0;
            }
            if ($solicitud['MutualProductoSolicitud']['aprobada'] == 1) {
                $msg = "LA SOLICITUD #$nroSolicitud SE ENCUENTRA APROBADA";
                $this->Mensaje->error($msg);
                $solicitud = null;
                $show = 0;
            }
            if ($this->data['MutualProductoSolicitud']['reasigna'] == 1) {
                if ($this->MutualProductoSolicitud->reasignarProveedor($this->data['MutualProductoSolicitud']['numero'], $this->data['MutualProductoSolicitud']['reasignar_proveedor_id'])) {
                    $this->Mensaje->ok("LA SOLICITUD #" . $this->data['MutualProductoSolicitud']['numero'] . " FUE REASIGNADA CORRECTAMENTE!");
                    $this->redirect('reasignar');
                } else {
                    $this->Mensaje->error("SE PRODUJO UN ERROR AL REASIGNAR LA SOLICITUD #" . $this->data['MutualProductoSolicitud']['numero']);
                }
            }
        }
        $this->set('show', $show);
        $this->set('solicitud', $solicitud);
        $this->set('nroSolicitud', $nroSolicitud);
    }

    function observar($id = null) {

        if (empty($id))
            parent::noDisponible();

        if (!empty($this->data)) {
            if ($this->MutualProductoSolicitud->observar($this->data)) {
                $this->redirect('creditos_pendientes_aprobar');
            } else {
                $this->Mensaje->error("NO SE PUDO CARGAR LA OBSERVACION");
            }
        }

        $this->MutualProductoSolicitud->Socio->bindModel(array('belongsTo' => array('Persona')));
        $solicitud = $this->MutualProductoSolicitud->read(null, $id);
        if (empty($solicitud))
            parent::noDisponible();
        $solicitud = $this->MutualProductoSolicitud->armaDatos($solicitud);
        $this->set('solicitud', $solicitud);
    }

    function anular($id = null) {

        $this->MutualProductoSolicitud->Socio->bindModel(array('belongsTo' => array('Persona')));
        $solicitud = $this->MutualProductoSolicitud->read(null, $id);
        if (empty($solicitud))
            parent::noAutorizado();
        $solicitud = $this->MutualProductoSolicitud->armaDatos($solicitud);


        if (!empty($this->data)) {
            if ($this->MutualProductoSolicitud->anular($this->data['MutualProductoSolicitud']['id'])) {
                $this->Mensaje->ok("LA SOLICITUD #" . $this->data['MutualProductoSolicitud']['id'] . " FUE ANULADA CORRECTAMENTE!");
                $this->redirect('by_persona/' . $solicitud['MutualProductoSolicitud']['persona_id']);
            } else {
                $this->Mensaje->error("NO SE PUDO ANULAR LA SOLICITUD");
            }
        }

        App::import('model', 'Pfyj.Persona');
        $this->Persona = new Persona();
        $persona = $this->Persona->read(null, $solicitud['MutualProductoSolicitud']['persona_id']);

        $this->set('solicitud', $solicitud);
        $this->set('persona', $persona);
        $this->set('menuPersonas', 1);
    }

    function download_attach($attachId) {
        if (empty($attachId)){parent::noDisponible();}           
        $sql = "select mutual_producto_solicitud_id,file_name,file_type,file_data from mutual_producto_solicitud_documentos as MutualProductoSolicitudDocumento where id = $attachId";
        $bin = $this->MutualProductoSolicitud->query($sql);
        if (empty($bin)){parent::noDisponible();}
        $fileName = WWW_ROOT . "files" . DS . "solicitudes" . DS . $bin[0]['MutualProductoSolicitudDocumento']['mutual_producto_solicitud_id'] . DS . $bin[0]['MutualProductoSolicitudDocumento']['file_name'];
        header("Content-type: " . $bin[0]['MutualProductoSolicitudDocumento']['file_type']);
        header('Content-Disposition: attachment;filename="' . $bin[0]['MutualProductoSolicitudDocumento']['file_name'] . '"');
        header('Cache-Control: max-age=0');
        if(file_exists($fileName)){
            echo file_get_contents($fileName);
        }else{
            echo $bin[0]['MutualProductoSolicitudDocumento']['file_data'];
        }
        
    }

    function download_attach_zipped($solicitud_id = null) {
        if (empty($solicitud_id)){parent::noDisponible();}            
        $sql = "select mutual_producto_solicitud_id,file_name,file_type,file_data from mutual_producto_solicitud_documentos as MutualProductoSolicitudDocumento where mutual_producto_solicitud_id = $solicitud_id";
        $bin = $this->MutualProductoSolicitud->query($sql);
        if (empty($bin)){parent::noDisponible();}
            
        foreach ($bin as $data) {
            $fileName = WWW_ROOT . "files" . DS . "solicitudes" . DS . $data['MutualProductoSolicitudDocumento']['mutual_producto_solicitud_id'] . DS . $data['MutualProductoSolicitudDocumento']['file_name'];
            if(file_exists($fileName)){
                $this->Zip->addData(file_get_contents($fileName), "SOLICITUD_$solicitud_id/" . $data['MutualProductoSolicitudDocumento']['file_name']);
            }else{
                $this->Zip->addData($data['MutualProductoSolicitudDocumento']['file_data'], "SOLICITUD_$solicitud_id/" . $data['MutualProductoSolicitudDocumento']['file_name']);
            }
            
        }
        $fileName = "SOLICITUD_$solicitud_id.zip";
        $this->Zip->forceDownload($fileName);
        exit;
    }

    function creditos_pendientes_aprobar_opago($id = null) {

        $this->Session->del('grilla_pagos');
        if (isset($this->params['url']['ORD']) && !empty($this->params['url']['ORD'])) {
            $this->set('mutual_producto_solicitud_id', $this->params['url']['ORD']);
            $solicitud = $this->MutualProductoSolicitud->read(null, $this->params['url']['ORD']);
            $solicitud = $this->MutualProductoSolicitud->armaDatos($solicitud);

            # 1) ######################################################
            $this->set('uuid', $this->MutualProductoSolicitud->generarPIN(20));
            //este UUID se guarda como hidden en el formulario del detalle
            #######################################################

            $this->chqCartera = $this->MutualProductoSolicitud->importarModelo('BancoChequeTercero', 'cajabanco');
            $chqCarteras = $this->chqCartera->getChequeCartera();


            $this->set('chqCarteras', $chqCarteras);
            $this->set('solicitud', $solicitud);

            if ($solicitud['MutualProductoSolicitud']['prestamo'] == 1 && $solicitud['MutualProductoSolicitud']['importe_percibido'] <= $solicitud['MutualProductoSolicitud']['proveedor_saldo_operativo']):
                $this->render('creditos_pendientes_aprobar_formulario_opago');
                return;
            else:
                $this->render('creditos_pendientes_aprobar_formulario');
                return;
            endif;
        }

        if (!empty($this->data)) {
//                debug($this->data);
//                exit;

            if ($this->data['MutualProductoSolicitud']['aprobar'] == 1) {

                # 5) ######################################################
                # reconstruyo el campo renglonesSerialize con los datos de la sessi�n 
                # con el uuid para que el modelo ni se entere del cambio
                if (!isset($this->data['Movimiento']['renglonesSerialize'])) {
                    $renglones = $this->Session->read('grilla_pagos_' . $this->data['Movimiento']['uuid']);
                    $this->data['Movimiento']['renglonesSerialize'] = base64_encode(serialize($renglones));
                }
                ######################################################

                $reasignar = null;
//                    if($this->MutualProductoSolicitud->aprobar($this->data['MutualProductoSolicitud']['id'],$reasignar)){
                if ($this->MutualProductoSolicitud->aprobar($this->data, $reasignar)) {
                    $this->Mensaje->ok("LA SOLICITUD DE CREDITO #" . $this->data['MutualProductoSolicitud']['id'] . " FUE APROBADA CORRECTAMENTE!.");
                    $solicitud = $this->MutualProductoSolicitud->read(null, $this->data['MutualProductoSolicitud']['id']);
                    $this->data = null;
                    if ($solicitud['MutualProductoSolicitud']['orden_pago_id'] > 0):
                        $this->redirect('editOrdenPago/' . $solicitud['MutualProductoSolicitud']['orden_pago_id'] . '/' . $solicitud['MutualProductoSolicitud']['id']);
                    else:
                        $this->redirect('pendientes_aprobar_opago/' . $solicitud['MutualProductoSolicitud']['id']);
                    endif;
                }else {
                    $this->Mensaje->errores("LA SOLICITUD DE CREDITO #" . $this->data['MutualProductoSolicitud']['id'] . " NO SE PUDO APROBAR!", $this->MutualProductoSolicitud->notificaciones);
// 					debug($this->MutualProductoSolicitud->notificaciones);
// 					exit;
                    $this->Mensaje->error("ERROR AL APROBAR LA SOLICITUD");
                    $this->redirect('pendientes_aprobar_opago/');
// 					$this->redirect('creditos_pendientes_aprobar/?ORD=' . $this->data['MutualProductoSolicitud']['id']);
                }
            }
        }

        if (!empty($id)) {
            $solicitud = $this->MutualProductoSolicitud->read(null, $id);
            if ($solicitud['MutualProductoSolicitud']['aprobada'] != 1)
                $this->redirect('creditos_pendientes_aprobar');
            $this->set('solicitud', $solicitud);
            $this->render('creditos_pendientes_aprobar_confirm');
        }

        $solicitudes = $this->MutualProductoSolicitud->getCreditosNoAprobados();
        $aAprobar = array();
        $row = array();

        $this->set('aAprobar', $aAprobar);
        $this->set('solicitudes', $solicitudes);
    }

    function consulta() {
        $solicitudID = 0;
        if (!empty($this->data)):
            $solicitud = $this->MutualProductoSolicitud->read("id", $this->data['MutualProductoSolicitud']['aprox_id']);
            if (!empty($solicitud)) {
                $solicitudID = $solicitud['MutualProductoSolicitud']['id'];
            }
        endif;
        $this->set('solicitudID', $solicitudID);
    }

   /*
    function adjuntar_documentacion($solicitud_id = null, $cargoArray = false) {

        if (empty($solicitud_id))
            parent::noDisponible();

        if (!empty($this->data)) {

            if ($this->data['MutualProductoSolicitud']['archivo']['error'] != 4) {

                $allowed = array('image/jpeg', 'application/pdf', 'image/png');

                if (in_array($this->data['MutualProductoSolicitud']['archivo']['type'], $allowed)) {

                    if ($this->data['MutualProductoSolicitud']['archivo']['size'] <= (2 * 1024 * 1024)) {

                        App::import('Core', 'File');
                        $file = new File($this->data['MutualProductoSolicitud']['archivo']['tmp_name'], false);
                        App::import('model', 'mutual.MutualProductoSolicitudDocumento');
                        $oSDOC = new MutualProductoSolicitudDocumento();
                        $documento = array();
                        $documento['MutualProductoSolicitudDocumento']['id'] = 0;
                        $documento['MutualProductoSolicitudDocumento']['mutual_producto_solicitud_id'] = $this->data['MutualProductoSolicitud']['id'];
                        $documento['MutualProductoSolicitudDocumento']['file_name'] = $this->data['MutualProductoSolicitud']['archivo']['name'];
                        $documento['MutualProductoSolicitudDocumento']['file_type'] = $this->data['MutualProductoSolicitud']['archivo']['type'];
                        $documento['MutualProductoSolicitudDocumento']['file_data'] = $file->read();

                        if ($oSDOC->save($documento)) {

                            $this->Mensaje->ok("EL ARCHIVO FUE ANEXADO A LA SOLICITUD CORRECTAMENTE");
                            $this->redirect('adjuntar_documentacion/' . $this->data['MutualProductoSolicitud']['id']);
                        } else {
                            $this->Mensaje->error("SE PRODUJO UN ERROR AL GUARDAR EL DOCUMENTO");
                            $this->redirect('adjuntar_documentacion/' . $this->data['MutualProductoSolicitud']['id']);
                        }
                    } else {
                        $this->Mensaje->error("EL ARCHIVO SUPERA EL TAMAÑO PERMITIDO (5 MB)");
                        $this->redirect('adjuntar_documentacion/' . $this->data['MutualProductoSolicitud']['id']);
                    }
                } else {
                    $this->Mensaje->error("EL ARCHIVO NO CORRESPONDE A LOS FORMATOS PERMITIDOS (" . implode(",", $allowed) . ")");
                    $this->redirect('adjuntar_documentacion/' . $this->data['MutualProductoSolicitud']['id']);
                }
            } else {
                $this->Mensaje->error("SE PRODUJO UN ERROR AL SUBIR EL ARCHIVO");
                $this->redirect('adjuntar_documentacion/' . $solicitud_id);
            }
        }


        $solicitud = $this->MutualProductoSolicitud->read(null, $solicitud_id);
        if (empty($solicitud))
            parent::noDisponible();
        $solicitud = $this->MutualProductoSolicitud->armaDatos($solicitud);
        App::import('model', 'Pfyj.Persona');
        $this->Persona = new Persona();
        $persona = $this->Persona->read(null, $solicitud['MutualProductoSolicitud']['persona_id']);
        $this->set('solicitud', $solicitud);
        $this->set('persona', $persona);
        $this->set('menuPersonas', 1);
    }
   

    */
    
    function adjuntar_documentacion($solicitud_id = null, $cargoArray = false) {
         
        if (empty($solicitud_id)) {
            //$this->redirect('search');
            parent::noDisponible();
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
                            $documento['MutualProductoSolicitudDocumento']['file_name'] = $this->data['MutualProductoSolicitud']['archivo']['name'];
                            $documento['MutualProductoSolicitudDocumento']['file_type'] = $this->data['MutualProductoSolicitud']['archivo']['type'];
                            // $documento['MutualProductoSolicitudDocumento']['file_data'] = $file->read();

                            $DIRDATAUPLOAD = WWW_ROOT . "files" . DS . "solicitudes" . DS . $documento['MutualProductoSolicitudDocumento']['mutual_producto_solicitud_id'];
                            if (! is_dir($DIRDATAUPLOAD)){mkdir($DIRDATAUPLOAD);}
                            $fileName = $DIRDATAUPLOAD . DS . $documento['MutualProductoSolicitudDocumento']['file_name'];
                            if(file_exists(WWW_ROOT . $fileName)) unlink (WWW_ROOT . $fileName);
                            if(!move_uploaded_file($this->data['MutualProductoSolicitud']['archivo']['tmp_name'], $fileName)){
                                $this->Mensaje->error("ERROR AL SUBIR EL ARCHIVO AL SERVIDOR");
                                $this->redirect('adjuntar_documentacion/' . $this->data['MutualProductoSolicitud']['id']);   
                            }                            

                            if ($oSDOC->save($documento)) {

                                $this->Mensaje->ok("EL ARCHIVO FUE ANEXADO A LA SOLICITUD CORRECTAMENTE");
                                $this->redirect('adjuntar_documentacion/' . $this->data['MutualProductoSolicitud']['id']);
                            } else {
                                $this->Mensaje->error("SE PRODUJO UN ERROR AL GUARDAR EL DOCUMENTO");
                                $this->redirect('adjuntar_documentacion/' . $this->data['MutualProductoSolicitud']['id']);
                            }
                        } else {
                            $this->Mensaje->error("EL ARCHIVO SUPERA EL TAMAÑO PERMITIDO (2 MB)");
                            $this->redirect('adjuntar_documentacion/' . $this->data['MutualProductoSolicitud']['id']);
                        }
                    } else {
                        $this->Mensaje->error("EL ARCHIVO NO CORRESPONDE A LOS FORMATOS PERMITIDOS (" . implode(",", $allowed) . ")");
                        $this->redirect('adjuntar_documentacion/' . $this->data['MutualProductoSolicitud']['id']);
                    }
                } else {
                    $this->Mensaje->error("SE PRODUJO UN ERROR AL SUBIR EL ARCHIVO");
                    $this->redirect('adjuntar_documentacion/' . $solicitud_id);
                }
            }

            $this->redirect('adjuntar_documentacion/' . $solicitud_id);
            
        } else {
            
            if (!empty($this->data)) {

                App::import('Core', 'File');
                App::import('model', 'mutual.MutualProductoSolicitudDocumento');
                $oSDOC = new MutualProductoSolicitudDocumento();
                $allowed = array('image/jpeg', 'application/pdf', 'image/png');
                
                foreach ($this->data['ProveedorPlanDocumento'] as $key => $value) {
                    
                    if ($this->data['ProveedorPlanDocumento'][$key]['error'] != 4){
                        
                        if (in_array($this->data['ProveedorPlanDocumento'][$key]['type'], $allowed)) {

                            if ($this->data['ProveedorPlanDocumento'][$key]['size'] <= (2 * 1024 * 1024)) {

                                if ($this->data['ProveedorPlanDocumento'][$key]['error'] == 0) {

                                    $file = new File($this->data['ProveedorPlanDocumento'][$key]['tmp_name'], false);
                                    $documento = array();
                                    $documento['MutualProductoSolicitudDocumento']['id'] = 0;
                                    $documento['MutualProductoSolicitudDocumento']['mutual_producto_solicitud_id'] = $this->data['MutualProductoSolicitud']['id'];
                                    $documento['MutualProductoSolicitudDocumento']['file_name'] = $this->data['ProveedorPlanDocumento'][$key]['name'];
                                    $documento['MutualProductoSolicitudDocumento']['file_type'] = $this->data['ProveedorPlanDocumento'][$key]['type'];
                                    list($codigoDocumento, $descripcion) = explode('|', $key);
                                    $documento['MutualProductoSolicitudDocumento']['codigo_documento'] = $codigoDocumento;
                                    // $documento['MutualProductoSolicitudDocumento']['file_data'] = $file->read();
                                    
                                    $DIRDATAUPLOAD = WWW_ROOT . "files" . DS . "solicitudes" . DS . $documento['MutualProductoSolicitudDocumento']['mutual_producto_solicitud_id'];
                                    if (! is_dir($DIRDATAUPLOAD)){mkdir($DIRDATAUPLOAD);}
                                    $fileName = $DIRDATAUPLOAD . DS . $documento['MutualProductoSolicitudDocumento']['file_name'];
                                    if(file_exists(WWW_ROOT . $fileName)) unlink (WWW_ROOT . $fileName);
                                    if(!move_uploaded_file($this->data['ProveedorPlanDocumento'][$key]['tmp_name'], $fileName)){
                                        $this->Mensaje->error("ERROR AL SUBIR EL ARCHIVO AL SERVIDOR");
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
                                    $this->redirect('adjuntar_documentacion/' . $this->data['MutualProductoSolicitud']['id']);
                                }
                            } else {
                                
                                $this->Mensaje->error("EL ARCHIVO SUPERA EL TAMAÑO PERMITIDO (2 MB)");
                                $this->redirect('adjuntar_documentacion/' . $this->data['MutualProductoSolicitud']['id']);
                            }
                        } else {
                                $this->Mensaje->error("EL ARCHIVO NO CORRESPONDE A LOS FORMATOS PERMITIDOS (" . implode(",", $allowed) . ")");
                                $this->redirect('adjuntar_documentacion/' . $this->data['MutualProductoSolicitud']['id']);
                        }                        
//                        
//                    }else{
//                        $this->Mensaje->error("SE PRODUJO UN ERROR AL SUBIR EL ARCHIVO");
//                        $this->redirect('ficha/' . $this->data['MutualProductoSolicitud']['id']);                        
                    }
                    

                }
                
//            }else{
//                $this->Mensaje->error("NO SE INDICA EL ARCHIVO O EL MISMO SUPERA EL TAMAÑO PERMITIDO (2 MB)");
                //$this->redirect('adjuntar_documentacion/' . $solicitud_id);
            }
            #todo ok
            //$this->redirect('adjuntar_documentacion/' . $solicitud_id);
        }
        
        $solicitud = $this->MutualProductoSolicitud->read(null, $solicitud_id);
        
        if (empty($solicitud))
            parent::noDisponible();
        
        $solicitud = $this->MutualProductoSolicitud->armaDatos($solicitud);
        App::import('model', 'Pfyj.Persona');
        $this->Persona = new Persona();
        $persona = $this->Persona->read(null, $solicitud['MutualProductoSolicitud']['persona_id']);
        $this->set('solicitud', $solicitud);
        $this->set('persona', $persona);
        $this->set('menuPersonas', 1);
        App::import('Model', 'proveedores.ProveedorPlanDocumento');
        $oPLANDOC = new ProveedorPlanDocumento();
        $datos = $oPLANDOC->getDocumentosByPlan($solicitud['ProveedorPlan']['id']);
        $this->set('datos', $datos);
    }

    
    function get_operaciones_pendientes_by_persona($persona_id) {
        $solicitudesPendientes = $this->MutualProductoSolicitud->getSolicitudesByVendedorGrillaModuloVentas(NULL, array(), NULL, NULL, NULL, NULL, $persona_id, FALSE, FALSE, array('MUTUESTA0000', 'MUTUESTA0014'));
        return $solicitudesPendientes;
    }

    function borrar_documentacion_adjunta($documento_id = NULL) {
        if (empty($documento_id))
            parent::noDisponible();
        App::import('model', 'mutual.MutualProductoSolicitudDocumento');
        $oSDOC = new MutualProductoSolicitudDocumento();
        $documento = $oSDOC->read(NULL, $documento_id);
        $solicitud_id = $documento['MutualProductoSolicitudDocumento']['mutual_producto_solicitud_id'];
        if($oSDOC->del($documento_id)){
            $fileName = WWW_ROOT . "files" . DS . "solicitudes" . DS . $documento['MutualProductoSolicitudDocumento']['mutual_producto_solicitud_id'] . DS . $documento['MutualProductoSolicitudDocumento']['file_name'];
            if(file_exists($fileName)){unlink($fileName);}
        };
        $this->redirect('adjuntar_documentacion/' . $solicitud_id.'/1');
    }

}

?>