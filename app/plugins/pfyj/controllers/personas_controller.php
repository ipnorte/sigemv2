<?php
class PersonasController extends PfyjAppController{
	
	var $name = 'Personas';
	
	var $autorizar = array('get_persona','autocomplete','bcra','get_datos_padron_jubilados','get_datos_padron_gobierno',
							'addOrdenPago','editOrdenPago','addRecibo', 'editRecibo','search',
            'google_maps','consultaBCRA','consultar_bcra','get_consulta_bcra','validar_sms_nosis','evaluar_sms_nosis', 'consulta_siisa', 'consulta_siisa_multiple', 'consulta_siisa_general');
	
	function beforeFilter(){ 
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}		
	
	
	function search($paramsSerialize,$limit = 3){
		$params = unserialize(base64_decode($paramsSerialize));
		$condiciones = null;		
		$this->Persona->recursive = 3;		
		if(!empty($params['nro_socio'])){
			$this->Persona->bindModel(array('hasOne' => array('Socio')));
			$condiciones = array(
								'Persona.tipo_documento  LIKE ' => $params['tipo_documento'] ."%",
								'Persona.documento LIKE ' => $params['nro_documento']."%",
								'Persona.apellido LIKE ' => $params['apellido']."%",
								'Persona.nombre LIKE ' => $params['nombre']."%",	
								'Socio.id' => $params['nro_socio'],			
							);					
		}else{
			$condiciones = array(
								'Persona.tipo_documento  LIKE ' => $params['tipo_documento'] ."%",
								'Persona.documento LIKE ' => $params['nro_documento']."%",
								'Persona.apellido LIKE ' => $params['apellido']."%",
								'Persona.nombre LIKE ' => $params['nombre']."%",	
							);					
		}	
		if(empty($params['busqueda_avanzada_by_beneficio'])):
			$personas = $this->Persona->find('all',array('conditions' => $condiciones, 'order' => array('Persona.apellido' => 'ASC', 'Persona.nombre' => 'ASC'),'limit' => $limit,));
		else:
			$personas = $this->Persona->getPersonasByBusquedaAvanzada($params,$limit);
		endif;		
		
		return $personas;
		
	}
	
	function index(){
		
//			$condiciones = null;		
//			$this->Persona->recursive = 3;
//			$showBusquedaAvanzada = false;
//			
//			$search = null;
//			
//			if(!empty($this->data)){
//
//				$this->Session->del($this->name.'.search');
//				$search = $this->data;
//				
//			}else if($this->Session->check($this->name.'.search')){
//				
//				$search = $this->Session->read($this->name.'.search');
//				$this->data = $search;
//				 
//			}
//			
//			if($search['Persona']['busquedaAvanzada'] == 1) $showBusquedaAvanzada = true;
//			
//			if(!empty($search['Persona']['nro_socio'])){
//				$this->Persona->bindModel(array('hasOne' => array('Socio')));
//				$condiciones = array(
//									'Persona.tipo_documento  LIKE ' => $search['Persona']['tipo_documento'] ."%",
//									'Persona.documento LIKE ' => $search['Persona']['documento']."%",
//									'Persona.apellido LIKE ' => $search['Persona']['apellido']."%",
//									'Persona.nombre LIKE ' => $search['Persona']['nombre']."%",	
//									'Socio.id' => $search['Persona']['nro_socio'],			
//								);					
//			}else{
//				$condiciones = array(
//									'Persona.tipo_documento  LIKE ' => $search['Persona']['tipo_documento'] ."%",
//									'Persona.documento LIKE ' => $search['Persona']['documento']."%",
//									'Persona.apellido LIKE ' => $search['Persona']['apellido']."%",
//									'Persona.nombre LIKE ' => $search['Persona']['nombre']."%",	
//								);					
//			}
//			
//			
//			$this->Session->write($this->name.'.search', $search);
//			
//			if(!$showBusquedaAvanzada):
//				$this->paginate = array(
//										'limit' => 3,
//										'order' => array('Persona.apellido' => 'ASC', 'Persona.nombre' => 'ASC')
//										);
//				$this->set('personas', $this->paginate(null,$condiciones));	
//			else:
//				$personas = $this->Persona->getPersonasByBusquedaAvanzada($search);
//				$this->set('personas', $personas);
//			endif;
//			
//			
//			$this->set('showBusquedaAvanzada',$showBusquedaAvanzada);		
			
		
	}
	
	function get_persona($id=null){
		if(empty($id)) return null;
    	App::import('Model','Pfyj.Persona');
    	$oPERSONA = new Persona();		
    	$oPERSONA->recursive = 3;
    	$oPERSONA->bindModel(array('belongsTo' => array('Localidad'),'hasOne' => array('Socio'),'hasMany' => array('PersonaBeneficio' => array('conditions' => array('PersonaBeneficio.activo' => 1),'order' => 'PersonaBeneficio.created DESC'))));
	    $oPERSONA->Localidad->bindModel(array('belongsTo' => array('Provincia')));
	    $persona = $oPERSONA->getPersona($id,TRUE,FALSE);
	    if(empty($persona)){
                parent::noDisponible();
                return;
            }
            return $persona;
	}
	
    function view($id=null){
    	if(empty($id)) $this->redirect('index');
    	$this->Persona->recursive = 3;
    	$this->Persona->bindModel(array('hasOne' => array('Socio'),'hasMany' => array('PersonaBeneficio' => array('conditions' => array('PersonaBeneficio.activo' => 1),'order' => 'PersonaBeneficio.created DESC'))));
        $this->Persona->Localidad->bindModel(array('belongsTo' => array('Provincia')));
        $persona = $this->get_persona($id,TRUE,FALSE);
    	$this->set('persona',$persona);   
    }

    function bcra($id=null){
    	if(empty($id)) $this->redirect('index');
    	App::import('Model','Pfyj.BcraService');
    	$oBcra = new BcraService(NULL,NULL,NULL,NULL);
    	
    	$this->set('persona',$this->Persona->read(null,$id));   
    }
    
    function edit($id=null){
    	if(empty($id)) $this->redirect('index');
        if(!empty($this->data)){
            ###############################################################################
            $persona = $this->Persona->validar_datos_personales($this->data['Persona']);
            $this->data['Persona'] = $persona['PERSONA'];
            if(!$persona['STATUS']){
                $this->Mensaje->errores("SE DETECTARON LOS SIGUIENTES ERRORES:",$this->Persona->notificaciones); 
                $this->set('invalidFiedls',$this->Persona->invalidFields);                
            } else if($this->Persona->guardar($this->data)){
                $this->Auditoria->log();
                $this->Mensaje->okGuardar();
//                $this->data = $this->Persona->getPersona($id);
                $this->redirect('edit/'.$id);
            }else{
                $this->Mensaje->errorGuardar();
            }
            ###############################################################################
//    		if($this->Persona->guardar($this->data)){
//    			$this->Auditoria->log();
//    			$this->Mensaje->okGuardar();
//    		}else{
//    			$this->Mensaje->errorGuardar();
//    		}
    	}else{
            $this->data = $this->Persona->getPersona($id);
        }     	
    	$this->Persona->recursive = 3;
//    	$this->data = $this->Persona->getPersona($id);
	$this->set('persona',$this->data);
        $this->render('edit_nuevo');    
    }

    
    function add(){
    	
        if(!empty($this->data)){
            
            ##############################################################################
            $persona = $this->Persona->validar_datos_personales($this->data['Persona']);
            $this->data['Persona'] = $persona['PERSONA'];
            if(!$persona['STATUS']){
                $this->Mensaje->errores("SE DETECTARON LOS SIGUIENTES ERRORES:",$this->Persona->notificaciones); 
                $this->set('invalidFiedls',$this->Persona->invalidFields);                
            }  else if($this->Persona->guardar($this->data)){
                $this->redirect('/pfyj/personas/view/'.$this->Persona->getLastInsertID());                
            }else{
                $this->Mensaje->errorGuardar();
            }
            ##############################################################################
    	}
        $this->render('add_nuevo');
    }
    
    function imprimir_padron($tipoSalida='PDF'){
    	
    	$opciones = array(
    						1 => '1 - SOCIOS ACTIVOS Y ADHERENTES',
    						2 => '2 - SOLO SOCIOS ACTIVOS',
    						3 => '3 - SOLO SOCIOS ADHERENTES',
    						4 => '4 - SOCIOS NO VIGENTES',
    						5 => '5 - PERSONAS NO ASOCIADAS',
    						6 => '6 - LIBRO SOCIOS INAES'	
    	);
    	
    	$this->set('opciones',$opciones);
    	
    	if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])):
    	
			App::import('model','Mutual.ListadoService');
			$oListado = new ListadoService();
			
			App::import('model','Shells.Asincrono');
			$oASINC = new Asincrono();			
			$asinc = $oASINC->read('p1,p2,p3,p6',$this->params['url']['pid']);
			$this->set('asinc',$asinc);
			
			if($asinc['Asincrono']['p1'] == 6 && $tipoSalida == 'PDF'):
				$datos = $oListado->getTemporal($this->params['url']['pid'],false);
				$datos = Set::extract("{n}.AsincronoTemporal",$datos);
				$this->set('datos',$datos);
				$this->render('reportes/libro_foliado_pdf','pdf');
			endif;
			
			if($tipoSalida == 'PDF'):
				$datos = $oListado->getTemporal($this->params['url']['pid'],false);
				$datos = Set::extract("{n}.AsincronoTemporal",$datos);
				$this->set('datos',$datos);
				$this->render('reportes/padron_socios_pdf','pdf');
			endif;
			if($tipoSalida == 'XLS'):

                            
                            $this->redirect('/mutual/listados/download/'.$asinc['Asincrono']['p6']);
                                                    

//				$columnas = array(
//                                    'texto_1' => 'DOCUMENTO',
//                                    'texto_2' => 'APENOM',
//                                    'texto_3' => 'DOMICILIO',				
//                                    'texto_12' => 'LOCALIDAD',
//                                    'texto_13' => 'CODIGO_POSTAL',
//                                    'texto_11' => 'PROVINCIA',
//                                    'texto_14' => 'TEL_FIJO',
//                                    'texto_15' => 'TEL_MOVIL',
//                                    'texto_16' => 'TEL_MENSAJES',
//                                    'texto_17' => 'E_MAIL',
//                                    'texto_20' => 'CUIT_CUIL',
//                                    'texto_4' => 'NRO_SOCIO',
//                                    'texto_10' => 'CATEGORIA',
//                                    'texto_7' => 'FECHA_ALTA',
//                                    'texto_5' => 'CALIFICACION',
//                                    'texto_6' => 'FECHA_CALIFICACION',
//                                    'texto_8' => 'FECHA_BAJA',
//                                    'texto_9' => 'MOTIVO_BAJA',
//                                    'decimal_2' => 'CUOTA_SOCIAL_ADEUDADA',
//                                    'entero_1' => 'CANTIDAD_CUOTA_SOCIAL_ADEUDADA',
//                                    'decimal_1' => 'DEUDA_OTROS_CONCEPTOS',
//                                    'entero_2' => 'EDAD',
//                                    'texto_18' => 'USUARIO_ALTA',
//                                    'texto_19' => 'VENDEDOR',
//                                    'entero_3' => 'PERIODO_IMPUTADO',
//                                    'decimal_3' => 'IMPORTE_PAGADO_CUOTA_SOCIAL'
//									
//				);
//				$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
//				$datos = $oListado->getDetalleToExcel($conditions,array(),$columnas);
//				$this->set('datos',$datos);	
//				$this->render('reportes/padron_socios_xls','blank');							
			endif;
  	
    	endif;
    }
    
	function solicitudes_credito($persona_id){
		$aPersona = $this->Persona->read(null, $persona_id);
		$this->set('persona',$aPersona);
	}    
    
	
	function estado_cuenta($persona_id = null){
		
		if(!empty($persona_id)){
			$persona = $this->Persona->getPersona($persona_id);
			$this->redirect( '/mutual/orden_descuento_cuotas/estado_cuenta2/'.$persona['Socio']['id'].'/0');
		
		}else{
//
//			$condiciones = null;		
//			$this->Persona->recursive = 2;
//			
//			if(!empty($this->data['Persona']['nro_socio'])){
//				$this->Persona->bindModel(array('hasOne' => array('Socio')));
//				$condiciones = array(
//									'Persona.tipo_documento  LIKE ' => $this->data['Persona']['tipo_documento'] ."%",
//									'Persona.documento LIKE ' => $this->data['Persona']['documento']."%",
//									'Persona.apellido LIKE ' => $this->data['Persona']['apellido']."%",
//									'Persona.nombre LIKE ' => $this->data['Persona']['nombre']."%",	
//									'Socio.id' => $this->data['Persona']['nro_socio'],			
//								);					
//			}else{
//				$condiciones = array(
//									'Persona.tipo_documento  LIKE ' => $this->data['Persona']['tipo_documento'] ."%",
//									'Persona.documento LIKE ' => $this->data['Persona']['documento']."%",
//									'Persona.apellido LIKE ' => $this->data['Persona']['apellido']."%",
//									'Persona.nombre LIKE ' => $this->data['Persona']['nombre']."%",	
//								);					
//			}			
			
//			$this->Persona->bindModel(array('hasOne' => array('Socio')));
//			$condiciones = array(
//								'Persona.tipo_documento  LIKE ' => $this->data['Persona']['tipo_documento'] ."%",
//								'Persona.documento LIKE ' => $this->data['Persona']['documento']."%",
//								'Persona.apellido LIKE ' => $this->data['Persona']['apellido']."%",
//								'Persona.nombre LIKE ' => $this->data['Persona']['nombre']."%",	
//								'Socio.id LIKE ' => $this->data['Persona']['nro_socio']."%",		
//							);	
//			$this->paginate = array(
//									'limit' => 3,
//									'order' => array('Persona.apellido' => 'ASC', 'Persona.nombre' => 'ASC')
//									);
//			$this->set('personas', $this->paginate('Persona',$condiciones));			
			$this->render('estado_cuenta_list_personas');
			
		}
	}
	
	function get_datos_padron_jubilados($documento){
		$datos = null;
//		App::import('Model','Pfyj.' . Configure::read('APLICACION.modelo_padron_jubilados'));
//		eval("\$oPadron = new " .Configure::read('APLICACION.modelo_padron_jubilados')."();");
//		$datos = $oPadron->find('all',array('conditions' => array(Configure::read('APLICACION.modelo_padron_jubilados').'.documento' => $documento)));
		return $datos;
	}

	function get_datos_padron_gobierno($documento){
		$datos = null;
//		App::import('Model','Pfyj.' . Configure::read('APLICACION.modelo_padron_activos'));
//		eval("\$oPadron = new " .Configure::read('APLICACION.modelo_padron_activos')."();");
//		$datos = $oPadron->find('all',array('conditions' => array(Configure::read('APLICACION.modelo_padron_activos').'.documento' => $documento)));
		return $datos;
	}	
	
	function autocomplete(){
		//separo el apellido y el nombre con la coma
		$apellido = null;
		$nombre = null;
		if(stripos($this->data['Persona']['ApeNomAproxima'],',')) list($apellido,$nombre) = explode(',',$this->data['Persona']['ApeNomAproxima']);
		else $apellido = $this->data['Persona']['ApeNomAproxima'];
		$apellido = trim($apellido);
		$nombre = trim($nombre);
		$personas = $this->Persona->find('all',array('conditions' =>  array('Persona.apellido LIKE ' => $apellido."%", 'Persona.nombre LIKE ' => $nombre."%"),'order' => array('Persona.apellido','Persona.nombre'),'limit' => 100));
		$this->set('personas',$personas);
		$this->render(null,'ajax');
	}
	
	
	function modificar_apenom($id){
    	if(empty($id)) $this->redirect('index');
        if(!empty($this->data)){
    		if($this->Persona->save($this->data)){
    			$this->Mensaje->okGuardar();
    			$this->redirect('edit/'.$this->data['Persona']['id']);
    		}else{
    			$this->Mensaje->errorGuardar();
    		}
    	}     	
    	$this->Persona->recursive = 3;
    	$this->data = $this->Persona->getPersona($id);
	    $this->set('persona',$this->data);   
		$this->render();
	}
	
	
	function imprimir_folios(){
		 if(!empty($this->data)):
		 	$this->set('libro',$this->data['Persona']['libro_socios_numero']);
		 	$this->set('hoja_desde',$this->data['Persona']['hoja_desde']);
		 	$this->set('hoja_hasta',$this->data['Persona']['hoja_hasta']);
			$this->set('fillNroLibro',$this->data['Persona']['fill_nro_libro']);
		 	$this->set('fillNroHoja',$this->data['Persona']['fill_nro_hoja']);
            $this->set('nombreLibro',$this->data['Persona']['libro_socios_nombre']);
		 	
		 	$this->render('reportes/libro_foliado_blank_pdf','pdf');
		 endif;
		 $this->set('fillNroLibro',4);
		 $this->set('fillNroHoja',5);
	}
	

	function addOrdenPago($nro_solicitud=null, $persona_id){
		$this->Session->del('grilla_pagos');
		if(empty($nro_solicitud)) parent::noAutorizado();

		$aPersona = $this->Persona->read(null, $persona_id);
		
		$this->Solicitud = $this->Persona->importarModelo('Solicitud', 'v1');
		$this->CancelacionOrden = $this->Solicitud->importarModelo('CancelacionOrden', 'mutual');
			
    			# 1) ######################################################
    			$this->set('uuid', $this->Solicitud->generarPIN(20));
    			//este UUID se guarda como hidden en el formulario del detalle
    			#######################################################
			
		
		
		if(!empty($this->data)):
    			
				# 5) ######################################################
    			# reconstruyo el campo renglonesSerialize con los datos de la sessi�n 
    			# con el uuid para que el modelo ni se entere del cambio
    			if(!isset($this->data['Movimiento']['renglonesSerialize'])){
    				$renglones = $this->Session->read('grilla_pagos_' . $this->data['Movimiento']['uuid']);	
    				$this->data['Movimiento']['renglonesSerialize'] = base64_encode(serialize($renglones));
    			}
    			######################################################

			if($this->Solicitud->grabarOrdenPago($this->data)):
				$this->Mensaje->okGuardar();
				$solicitud = $this->Solicitud->getSolicitud($nro_solicitud);
    			$this->redirect('editOrdenPago/' . $solicitud['Solicitud']['orden_pago_id'] . '/' . $solicitud['Solicitud']['nro_solicitud'] . '/' . $persona_id);
			else:
				$this->Mensaje->errorGuardar();
			endif;
		endif;
		$this->chqCartera = $this->Persona->importarModelo('BancoChequeTercero', 'cajabanco');
		$chqCarteras = $this->chqCartera->getChequeCartera();
		 
		$solicitud = $this->Solicitud->getSolicitud($nro_solicitud);
		
		if($solicitud['Solicitud']['orden_pago_id'] > 0) $this->redirect('editOrdenPago/' . $solicitud['Solicitud']['orden_pago_id'] . '/' . $nro_solicitud);
		
		App::import('Model', 'Pfyj.Persona');
		$this->Persona = new Persona(null);
		$this->Persona->bindModel(array('hasOne' => array('Socio')));

		$cancelaciones = $this->CancelacionOrden->getCancelacionByNroSolicitud($solicitud['Solicitud']['nro_solicitud']);
		$persona = $this->Persona->getByTdocNdoc($solicitud['PersonaV1']['tipo_documento'],$solicitud['PersonaV1']['documento']);

		$i = 0;
		foreach($cancelaciones as $cancelacion):
			if($cancelacion['CancelacionOrden']['orden_proveedor_id'] == $solicitud['Producto']['Proveedor']['idr']):
				$i++;
			endif;
		endforeach;	
		
		$this->set('cancelaciones', $cancelaciones);
		$this->set('rows', $i);
		$this->set('personaSigem',$aPersona);
		$this->set('persona',$persona);
		$this->set('solicitud',$solicitud);
		$this->set('nro_solicitud',$nro_solicitud);	
		$this->set('regresar', '/pfyj/personas/solicitudes_credito/' . $persona_id);	
		$this->set('chqCarteras', $chqCarteras);
	}
	

	function editOrdenPago($nOrdenPago=0, $nroSolicitud=0, $persona_id){

		if(empty($nOrdenPago)) $this->redirect('solicitudes_credito/' . $persona_id);
		$aPersona = $this->Persona->read(null, $persona_id);
		
		$this->Solicitud = $this->Persona->importarModelo('Solicitud', 'v1');
		$solicitud = $this->Solicitud->getSolicitud($nroSolicitud);
		
		if(!empty($this->data)):
			if ($this->Solicitud->anularOrdenPago($nOrdenPago)):
				$solicitud['Solicitud']['orden_pago_id'] = 0;
				$this->Solicitud->save($solicitud);
				$this->Mensaje->ok('LA ORDEN DE PAGO SE ANULO CORRECTAMENTE');
				$this->redirect('solicitudes_credito/' . $persona_id);
			else:
				$this->Mensaje->errorBorrar();
			endif;
			
				
		endif;

		
		$this->oOPago = $this->Solicitud->importarModelo('OrdenPago', 'proveedores');
		$aOPago = $this->oOPago->getOrdenDePago($solicitud['Solicitud']['orden_pago_id']);

		$aOPago['OrdenPago']['action'] = "editOrdenPago/" . $nOrdenPago . '/' . $nroSolicitud . '/' . $persona_id;
		$aOPago['OrdenPago']['url'] = '/pfyj/personas/editOrdenPago/0/' . $nroSolicitud . '/' . $persona_id;
		
		$aOPago['Proveedor'] = array(
			'id' => $solicitud['PersonaV1']['id_persona'],
			'razon_social' => $solicitud['PersonaV1']['apellido'] . ' ' . $solicitud['PersonaV1']['nombre'],
			'domicilio' => $solicitud['PersonaV1']['calle'] . ' Nro.:' . $solicitud['PersonaV1']['nro_calle'],
			'iva_concepto' => 'CONSUMIDOR FINAL',
			'formato_cuit' => $solicitud['PersonaV1']['documento'],
			'nro_ingresos_brutos' => ''
		);
		
		
		$this->set('persona',$aPersona);
		$this->set('aOrdenPago', $aOPago);
	}
	
	
	function addRecibo($nro_solicitud=null, $persona_id){
		
		$this->Session->del('grilla_cobros');
		if(empty($nro_solicitud)) $this->redirect('solicitudes_credito/' . $persona_id);

		$aPersona = $this->Persona->read(null, $persona_id);
		
		$this->Solicitud = $this->Persona->importarModelo('Solicitud', 'v1');
		$this->CancelacionOrden = $this->Persona->importarModelo('CancelacionOrden', 'mutual');
		
		if(!empty($this->data)):
			if($this->Solicitud->grabarRecibo($this->data)):
				$this->Mensaje->okGuardar();
				$solicitud = $this->Solicitud->getSolicitud($nro_solicitud);
    			$this->redirect('editRecibo/' . $solicitud['Solicitud']['recibo_id'] . '/' . $solicitud['Solicitud']['nro_solicitud'] . '/' . $persona_id);
			else:
				$this->Mensaje->errorGuardar();
			endif;
		endif;
		$solicitud = $this->Solicitud->getSolicitud($nro_solicitud);
		
		if($solicitud['Solicitud']['recibo_id'] > 0) $this->redirect('editRecibo/' . $solicitud['Solicitud']['recibo_id'] . '/' . $nro_solicitud . '/' . $persona_id);
		
		App::import('Model', 'Pfyj.Persona');
		$this->Persona = new Persona(null);
		$this->Persona->bindModel(array('hasOne' => array('Socio')));

		$persona = $this->Persona->getByTdocNdoc($solicitud['PersonaV1']['tipo_documento'],$solicitud['PersonaV1']['documento']);		

		$cancelaciones = $this->CancelacionOrden->getCancelacionByNroSolicitudEstado($solicitud['Solicitud']['nro_solicitud']);
		$i = 0;
		foreach($cancelaciones as $cancelacion):
			if($cancelacion['CancelacionOrden']['orden_proveedor_id'] == $solicitud['Producto']['Proveedor']['idr']):
				$i++;
			endif;
		endforeach;	
		
		$aporte_socio = $solicitud['Solicitud']['monto_instruccion_pago'];
//		if($solicitud['Producto']['comision_instruccion_pago'] > 0) $aporte_socio = round($solicitud['Solicitud']['en_mano'] / ((100 - $solicitud['Producto']['comision_instruccion_pago']) / 100) - $solicitud['Solicitud']['en_mano'],2);

		$this->set('personaSigem',$aPersona);
		$this->set('persona',$persona);
		$this->set('solicitud',$solicitud);
		$this->set('nro_solicitud',$nro_solicitud);		
		$this->set('aporte_socio', $aporte_socio);		
		$this->set('cancelaciones', $cancelaciones);
		$this->set('rows', $i);	
		$this->set('regresar', '/pfyj/personas/solicitudes_credito/' . $persona_id);	
	}
	

	function editRecibo($nReciboId=0, $nroSolicitud=0, $persona_id=0){
		if(empty($nReciboId)) $this->redirect('solicitudes_credito/' . $persona_id);

		$aPersona = $this->Persona->read(null, $persona_id);
		
		$this->Solicitud = $this->Persona->importarModelo('Solicitud', 'v1');
		$solicitud = $this->Solicitud->getSolicitud($nroSolicitud);
		
		if(!empty($this->data)):
			if ($this->Solicitud->anularRecibo($nReciboId)):
				$solicitud['Solicitud']['recibo_id'] = 0;
				$this->Solicitud->save($solicitud);
				$this->Mensaje->ok('EL RECIBO SE ANULO CORRECTAMENTE');
				$this->redirect('addRecibo/' . $nroSolicitud . '/' . $persona_id);
			else:
				$this->Mensaje->errorGuardar();
			endif;
			
				
		endif;

		if($solicitud['Solicitud']['recibo_id'] == 0) $this->redirect('addRecibo/' . $nroSolicitud . '/' . $persona_id);
		
		$aRecibo = $this->Solicitud->getRecibo($nReciboId);

		$aRecibo['Recibo']['action'] = "editRecibo/" . $nReciboId . '/' . $nroSolicitud . '/' . $persona_id;
		$aRecibo['Recibo']['url'] = '/pfyj/personas/editRecibo/0/' . $nroSolicitud . '/' . $persona_id;
		
		
		$this->set('persona',$aPersona);
		$this->set('Recibo', $aRecibo);
		
	}
        
/**
 * ********************************
 * probando la orden caja cobro, para saber que trae
 */		
		
//		$this->CancelacionOrden = $this->Persona->importarModelo('CancelacionOrden', 'mutual');
//		$aCancelacionOrden = $this->CancelacionOrden->get(6204, true);
//		debug($aCancelacionOrden);
//		exit;

//		$this->ProveedorFactura = $this->Persona->importarModelo('ProveedorFactura', 'proveedores');
//		$retorno = $this->ProveedorFactura->grabarFacturaByCaja(364808);
//		debug($retorno);
		
		
//		$this->ClienteFactura = $this->Persona->importarModelo('ClienteFactura', 'clientes');
//		$retorno = $this->ClienteFactura->grabarFacturaByCaja(364808);
//		debug($retorno);
		
		
//		$this->ProveedorLiquidacion = $this->Persona->importarModelo('ProveedorLiquidacion', 'proveedores');
//		$retorno = $this->ProveedorLiquidacion->grabarLiquidacionByCaja(364808);
//		debug($retorno);
		
//		$oTipoDocumento = $this->Persona->importarModelo('TipoDocumento', 'config');
//		$aComprobante = $oTipoDocumento->getComprobante('FAC');
//debug($aComprobante);
//exit;		
//		
//		$this->ProveedorLiquidacion = $this->Persona->importarModelo('ProveedorLiquidacion', 'proveedores');
//		$this->CancelacionOrden = $this->Persona->importarModelo('CancelacionOrden', 'mutual');
//		$orden = $this->CancelacionOrden->get(6213);
//		
//		// grabo la liquidacion a Proveedores
//		$orden['CancelacionOrden']['proveedor_liquidacion_id'] = $this->ProveedorLiquidacion->grabarLiquidacionByCancelacion($orden['CancelacionOrden']['orden_descuento_cobro_id']);
//debug($orden);
//exit;
		
//		$this->OrdenDescuentoCobro = $this->Persona->importarModelo('OrdenDescuentoCobro', 'mutual');
//		$ordenDescuento = $this->OrdenDescuentoCobro->getCobro(373477, true);
//		debug($ordenDescuento);
//		exit;
//		
//		
//		$this->OrdenCajaCobro = $this->Persona->importarModelo('OrdenCajaCobro', 'mutual');
//		$cajaCobro = $this->OrdenCajaCobro->cargarOrdenConDetalleCuotas(3129);
//		debug($cajaCobro);
//		exit;
//
        
        
        function google_maps($id = null){
            if(empty($id)) parent::noDisponible();
            $this->Persona->recursive = 3;
            $this->Persona->bindModel(array('hasOne' => array('Socio'),'hasMany' => array('PersonaBeneficio' => array('conditions' => array('PersonaBeneficio.activo' => 1),'order' => 'PersonaBeneficio.created DESC'))));
                $this->Persona->Localidad->bindModel(array('belongsTo' => array('Provincia')));
            $this->set('persona',$this->Persona->getPersona($id));
            $this->render('google_maps','blank');
        }
        
        
        function consultar_intranet($id = NULL){
            
            $persona = NULL;
            $informe = NULL;
            $ndoc = NULL;
            $cuitCuil = NULL;
            
            
            if(!empty($this->data)){
                $ndoc = $this->data['Persona']['documento'];
                $persona = $this->Persona->getByNdoc($ndoc,FALSE);
                $cuitCuil = $persona[0]['Persona']['cuit_cuil'];
            }
            
            if(!empty($id)){
                $persona = $this->Persona->getPersona($id);
                $ndoc = $persona['Persona']['documento'];
                $cuitCuil = $persona['Persona']['cuit_cuil'];
            }
            
            if(!empty($ndoc)){
                $ws = $this->requestAction('/config/global_datos/get_webservices_intranet');
                if(!empty($ws)){
                    ini_set("soap.wsdl_cache_enabled", 0);
                    foreach($ws as $service){
                            $client = new SoapClient(trim($service['GlobalDato']['concepto_2']));
                            $json = $client->getPersonaByDocumento($ndoc);
                            $json = json_decode($json);
                            //if(!empty($persona->result)) 
                                $informe[$service['GlobalDato']['id']] = array('CLIENTE' => $service['GlobalDato']['concepto_1'],'RESULTADO' => $json->result);
                    }
                    
                }                
            }
            
            $response = NULL;
            if(!empty($cuitCuil)){
				
				App::import('Model', 'Pfyj.Persona');
				$oPERSONA = new Persona();
				$URL = $oPERSONA->GlobalDato("concepto_2","PERSINTR");
				if(!empty($URL)){
					$INI_FILE = $_SESSION['MUTUAL_INI'];
					$MOD_BCRA = (isset($INI_FILE['general']['modulo_bcra']) && $INI_FILE['general']['modulo_bcra'] != 0 ? TRUE : FALSE);
					if($MOD_BCRA){
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $URL . $cuitCuil);
						curl_setopt($ch, CURLOPT_HEADER, 0);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
						$res = curl_exec($ch);
						$response = json_decode($res); 
					}	
				}
				
			
                
               
                
            }
            
            $this->set('cuitCuil',$cuitCuil);
            $this->set('id',$id);
            $this->set('response',$response);            
            

            $this->set('persona',$persona);
            $this->set('ndoc',$ndoc);
            $this->set('informe',$informe);
            $this->set('id',$id);
        }
        
        public function consultaBCRA($id = NULL) {
            
            if(!empty($this->data)){
                $cuitCuil = $this->data['Persona']['cuit_cuil'];
                $persona = $this->Persona->getByCUIT($cuitCuil,FALSE);

            }             
            
            if(!empty($id)){
                $persona = $this->Persona->read('cuit_cuil',$id);
                $cuitCuil = $persona['Persona']['cuit_cuil'];
            }
            
            $this->set('cuitCuil',$cuitCuil);
            $this->set('id',$id);
            $this->set('response',$this->get_consulta_bcra($cuitCuil));
            $this->render('consulta_bcra');
        }
        

        public function get_consulta_bcra($cuit = NULL){
            if(empty($cuit)){
                return NULL;
            } 
            App::import('Model', 'Pfyj.Persona');
            $oPERSONA = new Persona();
            $URL = $oPERSONA->GlobalDato("concepto_2","PERSINTR");
            $KEY = $oPERSONA->GlobalDato("concepto_3","PERSINTR");
            if(empty($URL)){
                return NULL;
            }
            $ENABLED = $oPERSONA->GlobalDato("logico_1","PERSINTR");
            $ENABLED = (!empty($ENABLED) ? $ENABLED : FALSE);
            
            $INI_FILE = $_SESSION['MUTUAL_INI'];
            $MOD_BCRA = (isset($INI_FILE['general']['modulo_bcra']) && $INI_FILE['general']['modulo_bcra'] != 0 ? $ENABLED : FALSE);
            if(!$MOD_BCRA){
                return NULL;
            }
            
            $token = NULL;
            if(!$this->Session->check('BCRA_TOKEN_ID')){
                $token = $this->Session->read('BCRA_TOKEN_ID');
            }
            $serverName = $PID = filter_input(INPUT_SERVER, 'SERVER_NAME');
            App::import('Model', 'Pfyj.BcraService');
            $oBCRA = new BcraService($URL,$serverName,$KEY,$token);   
            $response = $oBCRA->getFullInfo($cuit);

            return $response;
        }

        
     function validar_sms_nosis($nDoc,$celular){
        Configure::write('debug',0);
        App::import('Model', 'Pfyj.Persona');
        $oPERSONA = new Persona();         
        $URL = trim($oPERSONA->GlobalDato("concepto_2","PERSNVID"));
        $USER = trim($oPERSONA->GlobalDato("concepto_3","PERSNVID"));
        $TOKEN = trim($oPERSONA->GlobalDato("concepto_4","PERSNVID"));
        $GRUPO = trim($oPERSONA->GlobalDato("entero_1","PERSNVID"));
        $ACTIVO = trim($oPERSONA->GlobalDato("logico_1","PERSNVID"));
        if(!empty($URL) && $ACTIVO == "1"){
            App::import('Vendor','NosisVidApi',array('file' => 'nosis_vid_api.php'));
            $oNOSIS = new NosisVidApi($URL, $USER, $TOKEN, $GRUPO, $nDoc);
            echo json_encode($oNOSIS->validarSMS($celular));
            exit;
        }else{
            return NULL;
        }         
     }
     
     function evaluar_sms_nosis($consultaId,$pin){
        Configure::write('debug',0);
        App::import('Model', 'Pfyj.Persona');
        $oPERSONA = new Persona();         
        $URL = trim($oPERSONA->GlobalDato("concepto_2","PERSNVID"));
        $USER = trim($oPERSONA->GlobalDato("concepto_3","PERSNVID"));
        $TOKEN = trim($oPERSONA->GlobalDato("concepto_4","PERSNVID"));
        $GRUPO = trim($oPERSONA->GlobalDato("entero_1","PERSNVID"));
		$ACTIVO = trim($oPERSONA->GlobalDato("logico_1","PERSNVID"));
		$nDoc = null;
        if(!empty($URL) && $ACTIVO == "1"){
            App::import('Vendor','NosisVidApi',array('file' => 'nosis_vid_api.php'));
            $oNOSIS = new NosisVidApi($URL, $USER, $TOKEN, $GRUPO, $nDoc);
            echo json_encode($oNOSIS->evaluarTokenSMS($consultaId,$pin));
            exit;
        }                  
     }
     
     function consulta_siisa($personaId) {
         $respuesta = NULL;
         $params = NULL;
         $ERROR = NULL;
         App::import('Model', 'Pfyj.Persona');
         $oPERSONA = new Persona();
         $persona = $oPERSONA->read(null, $personaId);
         if(!empty($this->data)) {
             App::import('Model', 'Pfyj.PersonaBeneficio');
             $oBENEFICIO = new PersonaBeneficio();
             $beneficio = $oBENEFICIO->read(null, $this->data['Persona']['persona_beneficio_id']);
             $productoSIISA = $oBENEFICIO::GlobalDato('concepto_4', $beneficio['PersonaBeneficio']['codigo_empresa']);
             if(!empty($productoSIISA)) {
                 App::import('Vendor','SIISAService',array('file' => 'siisa_service.php'));
                 $oSIISA = new SIISAService();
                 
                 $sueldo_neto = $this->data['Persona']['sueldo_neto'];
                 $debitos_por_cbu = $this->data['Persona']['debitos_por_cbu'];
                 $cuota_credito = $this->data['Persona']['cuota_credito'];
                 
                 $parameters = array(
                     'nroDoc' => $persona['Persona']['documento'],
                     'nombre' => $persona['Persona']['nombre'] . " " . $persona['Persona']['apellido'],
                     'tipo_de_producto' => $productoSIISA,
                     'sueldo_neto' => $sueldo_neto,
                     'debitos_por_cbu' => $debitos_por_cbu,
                     'cuota_credito' => $cuota_credito,
                 );
                 
                 $respuesta = $oSIISA->executePolicyByParameters($parameters);
                 $params = $oSIISA->params;
             } else {
                 $ERROR = "No tiene configurado el Tipo Producto para el código de empresa asignado";
             }
         }
         $this->set('respuesta',$respuesta);
         $this->set('persona',$persona);
         $this->set('mensaje',$ERROR);
         $this->set('params',$params);
     }
     
     function consulta_siisa_general() {
        $respuestas = array();
         
        App::import('Model', 'Config.GlobalDato');
        $oGLD = new GlobalDato();
        $datos = $oGLD->get_productos_siisa();         
         
         if(!empty($this->data)){
             
             
             $documento = $this->data['Persona']['documento'];
             $nombre = $this->data['Persona']['nombre'];
             
             $sueldo_neto = $this->data['Persona']['sueldo_neto'];
             $debitos_por_cbu = $this->data['Persona']['debitos_por_cbu'];
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

             /*foreach ($datos as $dato) {
                 App::import('Vendor','SIISAService',array('file' => 'siisa_service.php'));
                 $oSIISA = new SIISAService();
                 
                 $parameters = array(
                     'nroDoc' => $documento,
                     'nombre' => $nombre,
                     'tipo_de_producto' => $dato['GlobalDato']['concepto_4'],
                     'sueldo_neto' => $sueldo_neto,
                     'debitos_por_cbu' => $debitos_por_cbu,
                     'cuota_credito' => $cuota_credito,
                 );
                 
                 $respuesta = $oSIISA->executePolicyByParameters($parameters);
                 array_push($respuestas, array(
                     'producto_siisa' => $dato['GlobalDato']['concepto_4'],
                     'respuesta' => $respuesta,
                     'parametros' => $oSIISA->params
                 ));
             }*/
         }
         $this->set('respuestas',$respuestas);
         $this->set('productos_siisa', $datos);
         
     }
     
     function consulta_siisa_multiple($personaId) {
         $respuestas = array();
         App::import('Model', 'Pfyj.Persona');
         $oPERSONA = new Persona();
         $persona = $oPERSONA->read(null, $personaId);
         App::import('Model', 'Config.GlobalDato');
         $oGLD = new GlobalDato();
         $datos = $oGLD->get_productos_siisa();
         foreach ($datos as $dato) {
             App::import('Vendor','SIISAService',array('file' => 'siisa_service.php'));
             $oSIISA = new SIISAService();
             $respuesta = $oSIISA->executePolicyByParameters($persona['Persona']['documento'], $persona['Persona']['nombre'] . " " . $persona['Persona']['apellido'], $dato['GlobalDato']['concepto_4']);
             array_push($respuestas, array(
                 'producto_siisa' => $dato['GlobalDato']['concepto_4'],
                 'respuesta' => $respuesta,
                 'parametros' => $oSIISA->params
             ));
         }
         $this->set('persona',$persona);
         $this->set('respuestas',$respuestas);
     }
}
?>
