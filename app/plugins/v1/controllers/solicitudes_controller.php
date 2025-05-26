<?php
class SolicitudesController extends V1AppController{
	var $name = 'Solicitudes';

	var $autorizar = array('index','socio','persona_padron','caratula','get_solicitudes_by_socio',
							'addOrdenPago', 'editOrdenPago', 'addRecibo', 'editRecibo');

	function beforeFilter(){
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();
	}

	function index(){
		$this->set('solicitudes', $this->paginate());
	}


	function socio($socio_id=null){
		$this->set('solicitudes', $this->Solicitud->bySocio($socio_id));
		$this->render();
	}

	function get_solicitudes_by_socio($socio_id,$tdoc=null,$ndoc=null){
		return $this->Solicitud->bySocio($socio_id,$tdoc,$ndoc);
	}

	function a_verificar(){
		$fecha_desde = "29/03/2008";
		$fecha_hasta = date("d/m/Y");
		$solicitudes = null;
		$beneficio = null;
		$apellido = null;
		$nombre = null;
		$solicitud = null;
		if(!empty($this->data)){
			$solicitudes = $this->Solicitud->aGenerarExpediente($this->data);
			$fecha_desde = $this->data['Solicitud']['fecha_d'];
			$fecha_hasta = $this->data['Solicitud']['fecha_h'];
			$beneficio = $this->data['Solicitud']['codigo_beneficio'];
			$apellido = $this->data['Persona']['apellido'];
			$nombre = $this->data['Persona']['nombre'];
			$solicitud = $this->data['Solicitud']['nro_solicitud_aprox'];

		}
		$this->set('beneficio',$beneficio);
		$this->set('apellido',$apellido);
		$this->set('nombre',$nombre);
		$this->set('solicitud',$solicitud);
		$this->set('fecha_desde',$fecha_desde);
		$this->set('fecha_hasta',$fecha_hasta);
		$this->set('solicitudes',$solicitudes);

	}


	function persona_padron($persona_v1_id){
		App::import('Model', 'Pfyj.Persona');
		$this->Persona = new Persona(null);
		$persona = $this->Persona->findAllByIdr($persona_v1_id);
		if(empty($persona))	$this->redirect('/pfyj/personas/add/');
		else $this->redirect('/pfyj/personas/view/'.$persona[0]['Persona']['id']);
	}

	function generar_expediente($nro_solicitud){

		$solicitud = $this->Solicitud->getSolicitud($nro_solicitud);


		if($solicitud['Solicitud']['estado'] != 12) $this->redirect('a_verificar');

		if(!empty($this->data)){

			$error = $this->Solicitud->generarExpediente($this->data);

			if($error[0] == 0){

				$this->redirect('resumen_expediente/'.$nro_solicitud);

			}else{

				$this->Mensaje->error($error[1]);

			}

		}


//		$solicitud = $this->Solicitud->getSolicitud($nro_solicitud);

		App::import('Model', 'Pfyj.Persona');
		$this->Persona = new Persona(null);
		$this->Persona->bindModel(array('hasOne' => array('Socio')));
		$persona = $this->Persona->getByTdocNdoc($solicitud['PersonaV1']['tipo_documento'],$solicitud['PersonaV1']['documento']);


//		$persona = $this->Persona->findAllByIdr($solicitud['Solicitud']['id_persona']);

		##########################################################################################
		# SI TIENE TARJETA DE DEBITO LA DECODIFICO Y LA ENVIO A LA VISTA PARA MOSTRARLA AL OPERADOR
		# QUE ESTA APROBANDO PARA CONTROL DE CARGA
		##########################################################################################
		$tarjeta = NULL;
		if(isset($solicitud['Beneficio']['tarjeta_debito']) && !empty($solicitud['Beneficio']['tarjeta_debito'])){
			App::import('Vendor','crypt');
			$oCRYPT = new Crypt();
			$tarjeta = unserialize($oCRYPT->decrypt($solicitud['Beneficio']['tarjeta_debito']));	
		}
		$this->set('tarjeta',$tarjeta);
		##########################################################################################
		

		$this->set('nro_solicitud',$nro_solicitud);
		$this->set('solicitud',$solicitud);

		$this->set('persona',(!empty($persona) ? $persona : null));

		App::import('Model', 'Pfyj.PersonaBeneficio');
		$oBenficio = new PersonaBeneficio(null);

		$this->set('bcos_hab',$oBenficio->bancosHabilitadosToCBU());

		App::import('Model', 'Mutual.CancelacionOrden');
		$oCancOrd = new CancelacionOrden(null);
		$this->set('cancelaciones_emitidas',(!empty($persona['Socio']['id']) ? $oCancOrd->getCancelacionesBySolicitudV1($persona['Socio']['id'],$nro_solicitud) : null));

	}

	function actualizar_beneficio($idr_beneficio,$nro_solicitud){


		App::import('Model', 'Pfyj.PersonaBeneficio');
		$oBenficio = new PersonaBeneficio(null);


		if(!empty($this->data)){
			$oBenficio->actualizarBeneficio($this->data);
			$this->redirect('generar_expediente/'.$nro_solicitud);
		}



		$beneficio = $oBenficio->getByIdr($idr_beneficio);

		$this->set('nro_solicitud',$nro_solicitud);
		$this->data = $beneficio;
		$this->set('bcos_hab',$oBenficio->bancosHabilitadosToCBU());



	}

	function caratula($nro_solicitud = null){
		if(empty($nro_solicitud)) parent::noAutorizado();
		$solicitud = $this->Solicitud->getSolicitud($nro_solicitud);
		App::import('Model', 'Pfyj.Persona');
		$this->Persona = new Persona(null);
		$this->Persona->bindModel(array('hasOne' => array('Socio')));
//		$persona = $this->Persona->findAllByIdr($solicitud['Solicitud']['id_persona']);

		$persona = $this->Persona->getByTdocNdoc($solicitud['PersonaV1']['tipo_documento'],$solicitud['PersonaV1']['documento']);

		$this->set('persona',$persona);
		$this->set('solicitud',$solicitud);
		$this->set('nro_solicitud',$nro_solicitud);
	}


	function resumen_expediente($nro_solicitud){

		$solicitud = $this->Solicitud->getSolicitud($nro_solicitud);

//		debug($solicitud);
//		exit;

		if( $solicitud['Solicitud']['estado'] != 14 ):
			if( $solicitud['Solicitud']['estado'] != 19 ) parent::noAutorizado();
		endif;

//		if($solicitud['Solicitud']['estado'] != 14 && $solicitud['Solicitud']['estado'] != 19) parent::noAutorizado();
		App::import('Model', 'Pfyj.Persona');
		$this->Persona = new Persona(null);
		$this->Persona->bindModel(array('hasOne' => array('Socio')));
//		$persona = $this->Persona->findAllByIdr($solicitud['Solicitud']['id_persona']);

		$persona = $this->Persona->getByTdocNdoc($solicitud['PersonaV1']['tipo_documento'],$solicitud['PersonaV1']['documento']);
		$this->set('persona',$persona);


		$this->set('solicitud',$solicitud);
		$this->set('nro_solicitud',$nro_solicitud);
	}


	function caratula_expediente_pdf($nro_solicitud){
		$this->set('nro_solicitud',$nro_solicitud);
		$solicitud = $this->Solicitud->getSolicitudPDF($nro_solicitud);
		if(empty($solicitud)) parent::noDisponible();

		$this->set('solicitud',$solicitud);
		$this->render('reportes/caratula_expediente','pdf');
	}


	function editar_fpago(){
		$solicitudes = null;
		$solicitud_nro = null;
		if(!empty($this->data)){

			if(isset($this->data['Solicitud']['guardar_fpago']) && $this->data['Solicitud']['guardar_fpago'] == 1){
				if($this->Solicitud->guardarFormaPago($this->data)){
					$this->Mensaje->okGuardar();
				}else{
					$this->Mensaje->errorGuardar();
				}
			}

			$solicitud_nro = $this->data['Solicitud']['nro_solicitud_aprox'];
			$solicitudes = $this->Solicitud->find('all',array('conditions' => array('Solicitud.nro_solicitud' => $this->data['Solicitud']['nro_solicitud_aprox'])));
			if(!empty($solicitudes)){
				$solicitudes = $this->Solicitud->armaDatos($solicitudes);
				$this->data = $solicitudes[0];
			}

		}
		$this->set('solicitud_nro',$solicitud_nro);
		$this->set('solicitudes',$solicitudes);

	}


	function listados($listado = 1,$salida = 'XLS'){

		$show_asincrono = 0;

		if(!empty($this->data)) $show_asincrono = 1;

		if(isset($this->params['url']['pid']) && !empty($this->params['url']['pid'])){

			App::import('model','Mutual.ListadoService');
			$oListado = new ListadoService();


			App::import('model','Shells.Asincrono');
			$oASINC = new Asincrono();

			$asinc = $oASINC->read('p1,p2,p3',$this->params['url']['pid']);

			$periodoDesde = $asinc['Asincrono']['p1'];
			$periodoHasta = $asinc['Asincrono']['p2'];


			if($listado == 1):

				$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
				$order = array('AsincronoTemporal.texto_2,AsincronoTemporal.texto_1');
				$fields = array('AsincronoTemporal.clave_1,AsincronoTemporal.clave_2,AsincronoTemporal.texto_1,AsincronoTemporal.texto_2,AsincronoTemporal.texto_3,AsincronoTemporal.texto_4,AsincronoTemporal.decimal_1,AsincronoTemporal.decimal_2,AsincronoTemporal.entero_1');
				$datos = $oListado->getTemporalByConditions(false,$conditions,$order,$fields);
				$datos = Set::extract('{n}.AsincronoTemporal',$datos);

				$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
				$order = array('AsincronoTemporal.texto_3');
				$fields = array('AsincronoTemporal.entero_1,AsincronoTemporal.texto_3');
				$group = array('AsincronoTemporal.entero_1');
				$cols = $oListado->getTemporalByConditions(false,$conditions,$order,$fields,$group);
				$cols = Set::extract('{n}.AsincronoTemporal',$cols);


				$this->set('periodoDesde',$periodoDesde);
				$this->set('periodoHasta',$periodoHasta);
				$this->set('cols',$cols);
				$this->set('datos',$datos);

				$this->render('reportes/ventas_productores_xls','blank');

			endif;

			if($listado == 2):

				$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'LISTADO_1');
				$order = array('AsincronoTemporal.texto_1,AsincronoTemporal.texto_3');
				$fields = array('AsincronoTemporal.clave_2,AsincronoTemporal.texto_1,AsincronoTemporal.texto_2,AsincronoTemporal.texto_3,AsincronoTemporal.texto_4,AsincronoTemporal.texto_5,AsincronoTemporal.texto_6,AsincronoTemporal.texto_7,AsincronoTemporal.texto_8,AsincronoTemporal.texto_9,AsincronoTemporal.decimal_1,AsincronoTemporal.decimal_2,AsincronoTemporal.decimal_3,AsincronoTemporal.entero_1');
				$datos = $oListado->getTemporalByConditions(false,$conditions,$order,$fields);
				$datos = Set::extract('{n}.AsincronoTemporal',$datos);
//				$this->set('periodoControl',$periodoControl);

				$this->set('periodoDesde',$periodoDesde);
				$this->set('periodoHasta',$periodoHasta);

				$this->set('datos',$datos);

				$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'LISTADO_2');
				$order = array('AsincronoTemporal.texto_1,AsincronoTemporal.clave_3');
				$fields = array('AsincronoTemporal.clave_2,AsincronoTemporal.clave_3,AsincronoTemporal.texto_1,AsincronoTemporal.texto_2,AsincronoTemporal.decimal_1,AsincronoTemporal.entero_1');
				$datos2 = $oListado->getTemporalByConditions(false,$conditions,$order,$fields);
				$datos2 = Set::extract('{n}.AsincronoTemporal',$datos2);

				$this->set('datos2',$datos2);

				//COLUMNAS
				$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'LISTADO_2');
				$order = array('AsincronoTemporal.clave_3');
				$fields = array('AsincronoTemporal.clave_3,AsincronoTemporal.texto_2,AsincronoTemporal.entero_1');
				$group = array('AsincronoTemporal.clave_3');
				$cols = $oListado->getTemporalByConditions(false,$conditions,$order,$fields,$group);
				$cols = Set::extract('{n}.AsincronoTemporal',$cols);
				$this->set('cols',$cols);

				$this->render('reportes/ventas_proveedores_xls','blank');

			endif;

			if($listado == 3):
				$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
				$order = array('AsincronoTemporal.texto_2,AsincronoTemporal.texto_5');
				$fields = array(
								'AsincronoTemporal.clave_1,
								AsincronoTemporal.texto_1,
								AsincronoTemporal.texto_2,
								AsincronoTemporal.texto_3,
								AsincronoTemporal.texto_4,
								AsincronoTemporal.texto_5,
								AsincronoTemporal.texto_6,
								AsincronoTemporal.texto_7,
								AsincronoTemporal.texto_8,
								AsincronoTemporal.texto_9,
								AsincronoTemporal.texto_10,
								AsincronoTemporal.texto_12,
								AsincronoTemporal.texto_13,
								AsincronoTemporal.decimal_1,
								AsincronoTemporal.decimal_2,
								AsincronoTemporal.decimal_3,
								AsincronoTemporal.entero_1,
								AsincronoTemporal.entero_2,
								AsincronoTemporal.entero_3'
				);

				$cols = array(
					'texto_1' => 'DOCUMENTO',
					'texto_2' => 'APELLIDO_NOMBRE',
					'clave_1' => 'ORDEN_DTO',
					'texto_3' => 'EXPEDIENTE',
					'texto_4' => 'FECHA',
					'texto_5' => 'INICIA_EN',
					'texto_6' => 'CONCEPTO',
					'texto_7' => 'NRO_REF_PROV',
					'texto_8' => 'ORGANISMO',
					'decimal_1' => 'DEVENGADO',
					'entero_1' => 'CUOTAS',
					'decimal_3' => 'IMPORTE_CUOTA',
					'decimal_2' => 'IMPORTE_PAGADO',
					'entero_2' => 'CUOTAS_PAGAS',
					'entero_3' => 'CUOTAS_ADEUDADAS',
					'entero_4' => 'CUOTAS_VENCIDAS',
					'entero_5' => 'CUOTAS_AVENCER',
					'texto_9' => 'PERIODO_ULTIMO_COBRO',
					'texto_10' => 'CONCEPTO_ULTIMO_COBRO',
                                        'texto_12' => 'ULTIMA_CALIFICACION',
                                        'texto_13' => 'CBU'
				);

				$datos = $oListado->getDetalleToExcel($conditions,$order,$cols);
				$this->set('datos',$datos);

				App::import('Model','Proveedores.Proveedor');
				$oPRV = new Proveedor();

				$this->set('proveedor',$oPRV->getRazonSocial($asinc['Asincrono']['p1']));
				$this->set('periodo_corte',$oPRV->periodo($asinc['Asincrono']['p2'],true));
				$this->set('cuotas_adeudadas',(!empty($asinc['Asincrono']['p3']) ? $asinc['Asincrono']['p3'] : 0));


				$this->render('reportes/control_creditos_xls','blank');

			endif;

			return;

		}


		$periodos = $this->Solicitud->getPeriodosProductoresLiquidados();
		$this->set('periodos',$periodos);
		$this->set('show_asincrono',$show_asincrono);

		$periodosLiquidados = $this->Solicitud->getPeriodosLiquidados();
		$this->set('periodosLiquidados',$periodosLiquidados);

		if($listado == 2) $this->set('proveedores',$this->Solicitud->getProveedoresLiquidados(true));

		if($listado == 1) $this->render('listado_venta_productores');
		if($listado == 2) $this->render('listado_venta_proveedores');
		if($listado == 3) $this->render('listado_control_creditos');

	}


	function reasignar_proveedor(){


		$estadosNoDispo = array(14,19);

		App::import('Model', 'V1.Producto');
		$oPRODUCTO = new Producto(null);

		$show = 0;
		$codigoProducto = null;
		$codigoProductoDescripcion = null;

//		$productos = $oPRODUCTO->getListProductosReasignables();

		$nroSolicitud = "";
		$datosOperativos = null;

		if(!empty($this->params['pass'][0]) && $this->params['url']['do'] == 'ANULAR'){
			if($this->Solicitud->anularReasignarProveedorSolicitud($this->params['pass'][0])){
				$this->Mensaje->ok("LA REASIGNACION DE LA SOLICITUD #".$this->params['pass'][0]." FUE PROCESADA CORRECTAMENTE!" );
				$this->redirect('reasignar_proveedor');
			}
		}

		if(!empty($this->data)){
			$show = 1;
			$nroSolicitud = $this->data['Solicitud']['numero'];
			$solicitud = $this->Solicitud->getSolicitud($nroSolicitud);

//			debug($solicitud);

			$datosOperativos = $oPRODUCTO->validarProductoReasignable($solicitud);

			$this->set('solicitud',$solicitud);


			if($this->data['Solicitud']['reasigna'] == 1):

				if($this->Solicitud->reasignarProveedorSolicitud($this->data['Solicitud']['numero'],$this->data['Solicitud']['reasignar_proveedor_id'])){
					$this->Mensaje->ok("LA SOLICITUD #".$this->data['Solicitud']['numero']." FUE REASIGNADA CORRECTAMENTE!" );
					$this->redirect('reasignar_proveedor');
				}

			endif;

		}



		$this->set('nroSolicitud',$nroSolicitud);
		$this->set('datosOperativos',$datosOperativos);

//		$this->set('productos',$productos);
		$this->set('show',$show);
		$this->set('codigoProducto',$codigoProducto);
		$this->set('codigoProductoDescripcion',$codigoProductoDescripcion);

		$this->render("reasignar_proveedor_by_nrosolicitud");


	}


	function addOrdenPago($nro_solicitud=null){
		$this->Session->del('grilla_pagos');
		if(empty($nro_solicitud)) parent::noAutorizado();

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
    			$this->redirect('editOrdenPago/' . $solicitud['Solicitud']['orden_pago_id'] . '/' . $solicitud['Solicitud']['nro_solicitud']);
			else:
				$this->Mensaje->errorGuardar();
			endif;
		endif;
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
		$this->set('persona',$persona);
		$this->set('solicitud',$solicitud);
		$this->set('nro_solicitud',$nro_solicitud);
		$this->set('regresar', "/v1/solicitudes/a_verificar");
	}


	function editOrdenPago($nOrdenPago=0, $nroSolicitud=0){

		if(empty($nOrdenPago)) $this->redirect('a_verificar');

		$solicitud = $this->Solicitud->getSolicitud($nroSolicitud);

		if(!empty($this->data)):
			if ($this->Solicitud->anularOrdenPago($nOrdenPago)):
				$solicitud['Solicitud']['orden_pago_id'] = 0;
				$this->Solicitud->save($solicitud);
				$this->Mensaje->ok('LA ORDEN DE PAGO SE ANULO CORRECTAMENTE');
				$this->redirect('addOrdenPago/' . $nroSolicitud);
			else:
				$this->Mensaje->errorBorrar();
			endif;


		endif;

		if($solicitud['Solicitud']['orden_pago_id'] == 0) $this->redirect('addOrdenPago/' . $nroSolicitud);


		$this->oOPago = $this->Solicitud->importarModelo('OrdenPago', 'proveedores');
		$aOPago = $this->oOPago->getOrdenDePago($solicitud['Solicitud']['orden_pago_id']);

		$aOPago['OrdenPago']['action'] = "editOrdenPago/" . $nOrdenPago . '/' . $nroSolicitud;
		$aOPago['OrdenPago']['url'] = '/v1/solicitudes/editOrdenPago/0/' . $nroSolicitud;

		$aOPago['Proveedor'] = array(
			'id' => $solicitud['PersonaV1']['id_persona'],
			'razon_social' => $solicitud['PersonaV1']['apellido'] . ' ' . $solicitud['PersonaV1']['nombre'],
			'domicilio' => $solicitud['PersonaV1']['calle'] . ' Nro.:' . $solicitud['PersonaV1']['nro_calle'],
			'iva_concepto' => 'CONSUMIDOR FINAL',
			'formato_cuit' => $solicitud['PersonaV1']['documento'],
			'nro_ingresos_brutos' => ''
		);


		$this->set('aOrdenPago', $aOPago);

	}


	function addRecibo($nro_solicitud=null){
		$this->Session->del('grilla_cobros');
		if(empty($nro_solicitud)) parent::noAutorizado();

		$this->SolicitudCancelacion = $this->Solicitud->importarModelo('SolicitudCancelaciones', 'v1');
		$this->CancelacionOrden = $this->Solicitud->importarModelo('CancelacionOrden', 'mutual');


		if(!empty($this->data)):

			if($this->Solicitud->grabarRecibo($this->data)):
				$this->Mensaje->okGuardar();
				$solicitud = $this->Solicitud->getSolicitud($nro_solicitud);

    			$this->redirect('editRecibo/' . $solicitud['Solicitud']['recibo_id'] . '/' . $solicitud['Solicitud']['nro_solicitud']);
			else:
				$this->Mensaje->errores("ERRORES:",$this->Solicitud->notificaciones);
//				$this->Mensaje->errorGuardar();
			endif;
		endif;
		$solicitud = $this->Solicitud->getSolicitud($nro_solicitud);

		if($solicitud['Solicitud']['recibo_id'] > 0) $this->redirect('editRecibo/' . $solicitud['Solicitud']['recibo_id'] . '/' . $nro_solicitud);

		App::import('Model', 'Pfyj.Persona');
		$this->Persona = new Persona(null);
		$this->Persona->bindModel(array('hasOne' => array('Socio')));

		$aporte_socio = $solicitud['Solicitud']['monto_instruccion_pago'];
//		if($solicitud['Producto']['comision_instruccion_pago'] > 0) $aporte_socio = round($solicitud['Solicitud']['en_mano'] / ((100 - $solicitud['Producto']['comision_instruccion_pago']) / 100) - $solicitud['Solicitud']['en_mano'],2);

		$persona = $this->Persona->getByTdocNdoc($solicitud['PersonaV1']['tipo_documento'],$solicitud['PersonaV1']['documento']);


		$cancelaciones = $this->CancelacionOrden->getCancelacionByNroSolicitud($solicitud['Solicitud']['nro_solicitud']);
//debug($solicitud);
//debug($cancelacion);
		$i = 0;
		foreach($cancelaciones as $cancelacion):
			if($cancelacion['CancelacionOrden']['orden_proveedor_id'] == $solicitud['Producto']['Proveedor']['idr']):
				$i++;
			endif;
		endforeach;


		$this->set('persona',$persona);
		$this->set('solicitud',$solicitud);
		$this->set('nro_solicitud',$nro_solicitud);
		$this->set('aporte_socio', $aporte_socio);
		$this->set('cancelaciones', $cancelaciones);
		$this->set('rows', $i);
		$this->set('regresar', "/v1/solicitudes/a_verificar");
	}


	function editRecibo($nReciboId=null, $nroSolicitud){
		if(empty($nReciboId)) $this->redirect('a_verificar');

		$solicitud = $this->Solicitud->getSolicitud($nroSolicitud);

		if(!empty($this->data)):
			if ($this->Solicitud->anularRecibo($nReciboId)):
				$solicitud['Solicitud']['recibo_id'] = 0;
				$this->Solicitud->save($solicitud);
				$this->Mensaje->ok('EL RECIBO SE ANULO CORRECTAMENTE');
				$this->redirect('addRecibo/' . $nroSolicitud);
			else:
				$this->Mensaje->errorGuardar();
			endif;


		endif;

		if($solicitud['Solicitud']['recibo_id'] == 0) $this->redirect('addRecibo/' . $nroSolicitud);

		$aRecibo = $this->Solicitud->getRecibo($nReciboId);

		$aRecibo['Recibo']['action'] = "editRecibo/" . $nReciboId . '/' . $nroSolicitud;
		$aRecibo['Recibo']['url'] = '/v1/solicitudes/editRecibo/0';


		$this->set('Recibo', $aRecibo);
//		$this->render('recibos/recibo');

	}


	/**
	 * Configuracion reasignacion de solicitudes de creditos
	 *
	 * @author adrian [17/02/2012]
	 * @param unknown_type $codigo
	 */
	function reasignar_proveedor_config($codigo = null){

		App::import('Model','config.GlobalDato');
		$oGLOBAL = new GlobalDato();

		App::import('Model','v1.Producto');
		$oPRODUCTO = new Producto();

		App::import('Model','proveedores.Proveedor');
		$oPROVEEDOR = new Proveedor();

		$prefix = 'PROVREAS';

		// debug($this->data);
		// exit;

		if(!empty($codigo)){

			if($codigo == 'DELETEALL'){
				$oGLOBAL->deleteAll("GlobalDato.id like '$prefix%' and GlobalDato.id <> '$prefix'");
				$oPRODUCTO->unsetReasignable();
				$oPROVEEDOR->unsetReasignable();
			}else if($oGLOBAL->del($codigo)){
				$this->Mensaje->ok("CONFIGURACION BORRADA CORRECTAMENTE!");
				$this->redirect('reasignar_proveedor_config');
			}
		}


		if(!empty($this->data) && !empty($this->data['GlobalDato']['codigo_producto'])):

			//PROCESO LOS DATOS PARA GUARDAR EN LA GLOBAL
			$config = array();

			$i = 1;

			foreach($this->data['GlobalDato']['codigo_producto'] as $producto):

				list($codigoProducto,$descripcionProducto) = explode("|", $producto);

				#borro el codigo actual
				$oGLOBAL->deleteAll("GlobalDato.id like '$prefix%' and GlobalDato.concepto_2 = '$codigoProducto'");

				#MARCO EL PRODUCTO COMO REASIGNABLE
				$oPRODUCTO->setReasignable($codigoProducto);

				if(!empty($this->data['GlobalDato']['proveedor_id'])):

					foreach($this->data['GlobalDato']['proveedor_id'] as $proveedor):

						list($proveedorId,$proveedorCuit,$proveedorRZ) = explode("|", $proveedor);

						$oPROVEEDOR->setReasignable($proveedorId);

//						$nReg = $oGLOBAL->find('count',array('conditions' => array('GlobalDato.id like' => $prefix.'%', 'GlobalDato.id <> ' => $prefix)));
//						$nReg += 1;

                                                $data = $oGLOBAL->query("SELECT concat('$prefix',lpad(cast(max(right(`GlobalDato`.`id`,4)) as unsigned) + ".$i.",4,0)) as nID,
                                                                        cast(max(right(`GlobalDato`.`id`,4)) as unsigned) + ".$i." as nReg
                                                                        FROM `global_datos` AS `GlobalDato` WHERE `GlobalDato`.`id`
                                                                        like '$prefix%' AND `GlobalDato`.`id` <> '$prefix' ");
                                                $nReg = (isset($data[0][0]['nReg']) ? $data[0][0]['nReg'] : 0);
                                                $nID = (isset($data[0][0]['nID']) ? $data[0][0]['nID'] : 0);
												// debug(($data));
                                                if(empty($nID)){
                                                    $nID = "$prefix". str_pad($i,4,"0",STR_PAD_LEFT);
                                                }

						$config[$nReg + $i] = array(
												'id' => $nID,
												'concepto_1' => $descripcionProducto . " A " . $proveedorRZ,
												'concepto_2' => $codigoProducto,
												'concepto_3' => $proveedorCuit,
												'logico_1' => 1,
												'entero_1' => $proveedorId,
												'texto_1' => serialize($this->data['GlobalDato']['usuario_id']),
												'fecha_1' => date('Y-m-d'),
						);

						$i++;

					endforeach;

				endif;

			endforeach;
			// debug($config);
			// exit;

			if(!empty($config)):

				if(!$oGLOBAL->saveAll($config)){
					$this->Mensaje->error("SE PRODUJO UN ERROR AL GUARDAR LA CONFIGURACION");
				}else{
					$this->Mensaje->ok("CONFIGURACION GUARDADA CORRECTAMENTE");
					$this->redirect('reasignar_proveedor_config');
				}

			endif;

		endif;


		####################################################################
		#CARGO LA CONFIGURACION ACTUAL
		####################################################################
		$datosGlobales = $this->requestAction("/config/global_datos/datos_globales/PROVREAS");
		$this->set('configuraciones',$datosGlobales);

		####################################################################
		#CARGO LOS USUARIOS ACTIVOS ACTUALES
		####################################################################
		App::import('Model','seguridad.Usuario');
		$oUSER = new Usuario();
		$usuarios = $oUSER->getUsersActivos(true);
		$this->set('usuarios',$usuarios);

		####################################################################
		#CARGO LOS PRODUCTOS VIGENTES DE LA V1 (CREDITOS)
		####################################################################

		$this->set('productos',$oPRODUCTO->getListProductosActivos());

		####################################################################
		#CARGO LOS PROVEEDORES VIGENTES ACTUALES
		####################################################################
		$this->set('proveedores',$this->requestAction("/proveedores/proveedores/proveedores/1/1/0/0"));



	}

}
?>
