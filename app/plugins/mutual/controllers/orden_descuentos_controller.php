<?php
class OrdenDescuentosController extends MutualAppController{

	var $name = 'OrdenDescuentos';

	var $autorizar = array(
							'view',
							'ordenes_by_socio',
							'arma_vencimientos',
							'ordenes_by_numero',
							'ordenes_by_socio_baja',
							'ordenes_by_socio_by_beneficio',
							'eliminar_orden',
							'get_orden',
							'impresion',
                                                        'ordenes_by_socio2'
	);

	function beforeFilter(){
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();
	}


	function by_socio($socio_id=null,$menuPersonas=1){

		if(empty($socio_id)) parent::noDisponible();

		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();

		$periodos = $this->OrdenDescuento->getPeriodoIni($socio_id,null,"DESC",true);
		$this->set('periodos',$periodos);

		if(!empty($this->data)){
			$estadoActual = $this->data['OrdenDescuento']['estado_actual'];
//			$ordenes = $this->OrdenDescuento->OrdenesBySocioByEstadoActual($socio_id,$estadoActual);
                        $ordenes = $this->OrdenDescuento->get_by_socio_by_estado($socio_id,$estadoActual);
			$this->set('ordenes',$ordenes);
			$this->set('socio_id',$socio_id);
			$this->set('estadoActual',$estadoActual);
		}

		App::import('Model','Pfyj.Socio');
		$oSocio = new Socio();
		$oSocio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $oSocio->read(null,$socio_id);
		$this->set('socio',$socio);
		$this->set('menuPersonas',$menuPersonas);

		if($this->RequestHandler->isAjax()):
			$this->render('by_socio_ajax2','ajax');
		else:
			$this->render();
		endif;
	}


	function view($id,$socio_id=null,$detalle=1,$menuPersonas=0,$linkToPadronPersona=0){

// 		App::import('model','Mutual.OrdenDescuento');
// 		$oORDEN = new OrdenDescuento();
		
		App::import('model','Mutual.OrdenDescuentoService');
		$oORDENSERVICE = new OrdenDescuentoService();
		App::import('model','pfyj.Socio');
		$oSOCIO = new Socio();
		

// 		$oORDEN->recursive = 3;
// 		$oORDEN->bindModel(array('belongsTo' => array('Proveedor','Socio')));
// 		$oORDEN->Socio->bindModel(array('belongsTo' => array('Persona')));
// 		$oORDEN->unbindModel(array('hasMany' => array('OrdenDescuentoCuota')));
		if(!empty($socio_id)){$socio = $oSOCIO->read(null,$socio_id);}
//		$orden = $this->OrdenDescuento->read(null,$id);
// 		$orden = $oORDEN->getOrden($id);
		$orden = $oORDENSERVICE->getOrden($id);

		
		if($orden['OrdenDescuento']['socio_id'] != $socio_id && !empty($socio_id)) parent::noAutorizado();
		$this->set('orden',$orden);
		$this->set('detalle',$detalle);
		$this->set('menuPersonas',$menuPersonas);
		$this->set('id',$id);
		$this->set('linkToPadronPersona',$linkToPadronPersona);
		$this->set('socio',(!empty($socio) ? $socio : null));
//		Configure::write('debug',0);
		$this->render();
	}

	function get_orden($id = null){
//         App::import('Model','mutual.OrdenDescuento');
//         $oORD = new OrdenDescuento();
//         $orden = $oORD->getOrden($id);
        if(empty($id)) {
            return null;
        }
        App::import('model','Mutual.OrdenDescuentoService');
        $oORDENSERVICE = new OrdenDescuentoService();
        $orden = $oORDENSERVICE->getOrden($id);
        
        return $orden;
	}

	function reporte_by_socio_pdf($socio_id,$estadoActual=1){
		$this->OrdenDescuento->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->OrdenDescuento->Socio->read(null,$socio_id);
		$this->set('socio',$socio);
		$this->OrdenDescuento->bindModel(array('belongsTo' => array('PersonaBeneficio')));
//		$ordenes = $this->OrdenDescuento->OrdenesBySocioByEstadoActual($socio_id,$estadoActual);
                $ordenes = $this->OrdenDescuento->get_by_socio_by_estado($socio_id,$estadoActual,null,null,'299912');
		$this->set('ordenes',$ordenes);
                $this->set('estadoActual',$estadoActual);
		$this->render('reportes/reporte_by_socio_pdf2','pdf');
	}


	function ordenes_by_socio($socio_id,$estadoActual=1){
		App::import('model','Mutual.OrdenDescuento');
		$oOrden = new OrdenDescuento();
		$ordenes = $oOrden->OrdenesBySocioByEstadoActual($socio_id,$estadoActual);
		return $ordenes;
	}

	function ordenes_by_socio2($socio_id,$estadoActual=1){
		App::import('model','Mutual.OrdenDescuento');
		$oOrden = new OrdenDescuento();
                $ordenes = $oOrden->get_by_socio_by_estado($socio_id,$estadoActual,null,null,'299912');
		return $ordenes;
	}

	function ordenes_by_socio_by_beneficio($socio_id,$persona_beneficio_id,$soloAdeudadas=0){
		App::import('model','Mutual.OrdenDescuento');
		$oOrden = new OrdenDescuento();
//		$ordenes = $this->OrdenDescuento->OrdenesBySocioByEstadoActual($socio_id,1,$persona_beneficio_id);
                #$socio_id,$estado=1,$persona_beneficio_id=null,$periodoIni=null,$periodoCorte=null,$fechaEmiDesde=null,$fechaEmiHasta=null,$soloAdeudadas = FALSE
                $ordenes = $oOrden->get_by_socio_by_estado($socio_id,1,$persona_beneficio_id,null,'299912',NULL,NULL,$soloAdeudadas);
		return $ordenes;
	}

	function ordenes_by_numero($tipo,$numero){
// 		App::import('model','Mutual.OrdenDescuento');
// 		$oOrden = new OrdenDescuento();
// 		$oOrden->unbindModel(array('belongsTo' => array('Socio','Proveedor'),'hasMany' => array('OrdenDescuentoCuota')));
// 		$ords = $oOrden->find('all',array('conditions' => array('OrdenDescuento.tipo_orden_dto' => $tipo, 'OrdenDescuento.numero' => $numero,'OrdenDescuento.activo' => 1),'order'=>'OrdenDescuento.periodo_ini DESC,OrdenDescuento.created DESC'));
// 		$ords = $oOrden->armaDatos($ords);
		
		App::import('model','Mutual.OrdenDescuentoService');
		$oOrden = new OrdenDescuentoService();
		$ords = $oOrden->getByTipoAndNumero($tipo,$numero);
		
		return $ords;
	}

	function eliminar_orden($id = null){
		if(empty($id)) parent::noAutorizado();
		if($this->OrdenDescuento->eliminarOrden($id)){
			$this->Mensaje->ok("La ORDEN DE DESCUENTO #$id FUE DADA DE BAJA DEL SISTEMA!.");
		}else{
			$this->Mensaje->error("SE PRODUJO UN ERROR AL BORRAR LA ORDEN DE DESCUENTO #$id!.");
		}
	}

	function baja($persona_id=null,$id=null){

		App::import('model','Pfyj.Persona');
		$this->Persona = new Persona();

		if(!empty($persona_id)){

			$persona = $this->Persona->read(null,$persona_id);
			$this->set('persona', $persona);

//			if(!empty($id)){
//
//				if($this->OrdenDescuento->baja($id)){
//					$this->Auditoria->log();
//					$this->Mensaje->ok("La ORDEN DE DESCUENTO #$id FUE DADA DE BAJA DEL SISTEMA!.");
//				}else{
//					$this->Mensaje->error("SE PRODUJO UN ERROR AL BORRAR LA ORDEN DE DESCUENTO #$id!.");
//				}
//				$this->redirect('baja/'.$persona_id);
//			}
//			$this->OrdenDescuento->unbindModel(array('belongsTo' => array('Socio','Proveedor'),'hasMany' => array('OrdenDescuentoCuota')));
			$this->OrdenDescuento->unbindModel(array('belongsTo' => array('Socio','Proveedor')));
			$ordenes = $this->OrdenDescuento->OrdenesBySocioBaja($persona['Socio']['id']);
			$this->set('ordenes', $ordenes);

			$this->render('baja_grilla_ordenes');

		}else{
			$condiciones = null;
			$this->Persona->recursive = 2;
			$condiciones = array(
								'Persona.tipo_documento  LIKE ' => $this->data['Persona']['tipo_documento'] ."%",
								'Persona.documento LIKE ' => $this->data['Persona']['documento']."%",
								'Persona.apellido LIKE ' => $this->data['Persona']['apellido']."%",
								'Persona.nombre LIKE ' => $this->data['Persona']['nombre']."%",
							);
			$this->paginate = array(
									'limit' => 30,
									'order' => array('Persona.apellido' => 'ASC', 'Persona.nombre' => 'ASC')
									);
			$this->set('personas', $this->paginate('Persona',$condiciones));
			$this->render('baja_list_socios');
		}

	}

	function ordenes_by_socio_baja($socio_id){
		App::import('model','Mutual.OrdenDescuento');
		$oOrden = new OrdenDescuento();
		$oOrden->unbindModel(array('belongsTo' => array('Socio','Proveedor'),'hasMany' => array('OrdenDescuentoCuota')));
		$ords = $oOrden->OrdenesBySocioBaja($socio_id);
		return $ords;
	}


	/**
	 * reprograma vencimientos de una orden de descuento
	 * @param $id
	 * @return unknown_type
	 */
	function reprogramar($id = null){

		$ordenes = null;

		if(!empty($this->data)){
			if(isset($this->data['OrdenDescuento']['aprox_id'])):
				$token1 = $this->Session->read('TOKEN');
				$this->Session->del('REPROGRAMACION_'.$token1);
				$this->Session->del('TOKEN');
				$ordenes = $this->OrdenDescuento->find('all',array('conditions' => array('OrdenDescuento.id LIKE' => $this->data['OrdenDescuento']['aprox_id'].'%'),'order'=>'OrdenDescuento.id','limit' => 50));
				$ordenes = $this->OrdenDescuento->armaDatos($ordenes);
				$ordenes = Set::extract('/OrdenDescuento',$ordenes);
				$this->set('ordenes',$ordenes);
				$this->render('reprogramar');
			endif;
			if(isset($this->data['OrdenDescuento']['reprogramar']) && $this->data['OrdenDescuento']['reprogramar'] == 1):
				$fechaInicio = $this->OrdenDescuento->armaFecha($this->data['OrdenDescuento']['fecha']);
				$orden = $this->OrdenDescuento->calculaReprogramacion($this->data['OrdenDescuento']['id'],$fechaInicio);
				//grabar en una sesion
				$TOKEN = rand();
				$this->Session->write('REPROGRAMACION_'.$TOKEN,$orden);
				$this->Session->write('TOKEN',$TOKEN);
				$this->set('orden',$orden);
				$this->set('TOKEN',$TOKEN);

				//VERIFICAR EL ULTIMO PERIODO CERRADO
				App::import('Model','Mutual.Liquidacion');
				$oLiq = new Liquidacion();
				$ultimoPeriodoCerrado = $oLiq->getUltimoPeriodoCerrado($orden['OrdenDescuento']['codigo_organismo']);
				$this->set('ultimoPeriodoCerrado',$ultimoPeriodoCerrado);

				$this->render('vista_previa_reprogramacion','ajax');
			endif;
		}else if(!empty($id)){
			$this->Session->del('REPROGRAMACION');
			$this->set('orden',$this->OrdenDescuento->read(null,$id));
			$this->set('orden_descuento_id',$id);
			$this->render('reprogramar_form');
		}else{
                    //verifico la session
                    $token1 = $this->Session->read('TOKEN');
                    if(isset($this->params['url']['do']) && $this->params['url']['do'] = 'REPRO' && $this->params['url']['token_id'] == $token1){
                        $token2 = $this->params['url']['token_id'];
                        $orden = $this->Session->read('REPROGRAMACION_'.$token2);
                        if($this->OrdenDescuento->reprogramarOrden($orden)){
                                $this->Session->del('REPROGRAMACION_'.$token1);
                                $this->Session->del('TOKEN');
                                $this->redirect('/mutual/orden_descuento_cuotas/estado_cuenta/'.$orden['OrdenDescuento']['socio_id'].'/1');
                        }else{
                                $this->Mensaje->error('SE PRODUJO UN ERROR AL INTENTAR REPROGRAMAR LA ORDEN #'.$orden['OrdenDescuento']['id']);
                        }
                    }
		}
	}

	/**
	 * reasigna beneficios a una o varias ordenes de descuento del socio
	 * @param $persona_id
	 * @return unknown_type
	 */
	function reasignar_beneficio($persona_id=null){

		App::import('model','Pfyj.Persona');
		$this->Persona = new Persona();

		if(!empty($persona_id)){

			if(!empty($this->data)){

			if(isset($this->data['OrdenDescuento']['reasignar_beneficio']) && $this->data['OrdenDescuento']['reasignar_beneficio'] == 1){

					if(isset($this->data['OrdenDescuento']['orden_descuento_check_id']) && count($this->data['OrdenDescuento']['orden_descuento_check_id']) != 0){
						$status = true;

                                                $periodoDesde = $this->data['PersonaBeneficio']['periodo_desde']['year'] . $this->data['PersonaBeneficio']['periodo_desde']['month'];

						foreach($this->data['OrdenDescuento']['orden_descuento_check_id'] as $orden_id => $sel){

							if(!$this->OrdenDescuento->reasignarBeneficio($orden_id,$this->data['OrdenDescuento']['persona_beneficio_id'],$periodoDesde)){
								$status = false;
								break;
							}
						}
						if($status){
							$this->Mensaje->ok("LA REASIGNACION SE PROCESO CORRECTAMENTE.");
						}else{
							$this->Mensaje->error("SE PRODUJO UN ERROR AL ACTUALIZAR EL BENEFICIO.");
						}
					}else{
						$this->Mensaje->error("Debe indicar al menos una Orden de Descuento!.");
					}

				}

			}

			$persona = $this->Persona->read(null,$persona_id);
			$this->set('persona',$persona);
			#saco las ordenes de descuento
			$ordenes = null;
			if(isset($persona['Socio']['id']) && !empty($persona['Socio']['id'])){
				$ordenes = $this->OrdenDescuento->OrdenesBySocio($persona['Socio']['id']);
			}
			$this->set('ordenes',$ordenes);
			$this->render('reasignar_beneficio_grilla');

			//VALIDAR

		}else{

			$condiciones = null;
			$this->Persona->recursive = 2;
			$condiciones = array(
								'Persona.tipo_documento  LIKE ' => $this->data['Persona']['tipo_documento'] ."%",
								'Persona.documento LIKE ' => $this->data['Persona']['documento']."%",
								'Persona.apellido LIKE ' => $this->data['Persona']['apellido']."%",
								'Persona.nombre LIKE ' => $this->data['Persona']['nombre']."%",
							);
			$this->paginate = array(
									'limit' => 30,
									'order' => array('Persona.apellido' => 'ASC', 'Persona.nombre' => 'ASC')
									);
			$this->set('personas', $this->paginate('Persona',$condiciones));
			$this->render('reasignar_beneficio');
		}
	}


	function carga_deuda($orden_dto_id = null){
		$showFormSearch = 1;
		if(!empty($this->data)){
//			$orden = $this->OrdenDescuento->read(null,$this->data['OrdenDescuento']['aprox_id']);
//			$this->set('orden',$orden);
			$this->redirect('carga_deuda/'.$this->data['OrdenDescuento']['aprox_id']);
			$showFormSearch = 0;
		}
		if(!empty($orden_dto_id)){
			$orden = $this->OrdenDescuento->read(null,$orden_dto_id);
			if(empty($orden)){
				$this->Mensaje->error("LA ORDEN #$orden_dto_id NO EXISTE!.");
				$this->redirect('carga_deuda');
			}
			$this->set('orden',$orden);
			$showFormSearch = 0;
		}
		$this->set('show_form_searh',$showFormSearch);
	}


	function suspender_permanente($id=null,$socio_id=null){
		if(empty($id)) parent::noDisponible();
		if(empty($socio_id)) parent::noDisponible();
		$this->OrdenDescuento->unbindModel(array('hasMany' => array('OrdenDescuentoCuota')));
		$orden = $this->OrdenDescuento->read('socio_id,permanente,periodo_ini',$id);
		if($orden['OrdenDescuento']['socio_id'] != $socio_id) parent::noDisponible();
		if($orden['OrdenDescuento']['permanente'] != 1){
			$this->Mensaje->error("LA ORDEN #$id NO ES PERMANENTE POR LO TANTO NO PUEDE SER DADA DE BAJA!");
			$this->redirect('by_socio/'.$socio_id.'/1');
		}

		App::import('Model','Pfyj.Socio');
		$oSocio = new Socio();
		$oSocio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $oSocio->read(null,$socio_id);
		$this->set('socio',$socio);
		$this->set('menuPersonas',1);
		$this->set('id',$id);

		//cargo las cuotas adeudadas
		App::import('Model', 'Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota(null);

		$cuotasAdeudadas = $oCUOTA->cuotasAdeudadasByOrdenDto($id);

		$this->set('cuotas_adeudadas',$cuotasAdeudadas);

		$this->set('periodo_ini',$orden['OrdenDescuento']['periodo_ini']);

		if(!empty($this->data)){

			$periodoHasta = $this->data['OrdenDescuento']['periodo_hasta']['year'].$this->data['OrdenDescuento']['periodo_hasta']['month'];
			$bajarCuotas = array();
			if(isset($this->data['OrdenDescuentoCuota']['orden_descuento_cuota_id'])){
				$bajarCuotas = array_keys($this->data['OrdenDescuentoCuota']['orden_descuento_cuota_id']);
			}
			if($this->OrdenDescuento->suspenderOrdenPermanente($this->data['OrdenDescuento']['id'],$periodoHasta,$bajarCuotas)){
				$this->Mensaje->ok("LA ORDEN #$id FUE DADA DE BAJA CORRECTAMENTE.");
			}else{
				$this->Mensaje->error("SE PRODUJO UN ERROR AL INTENTAR DAR DE BAJA LA ORDEN #$id");
			}
			$this->redirect('by_socio/'.$socio_id.'/1');

		}


	}


	function impresion($id,$salida='PDF'){

		$cuotas = $this->requestAction('/mutual/orden_descuento_cuotas/cuotas_by_odescuento/'.$id.'/1');
// 		$orden = $this->OrdenDescuento->getOrden($id);
		
		App::import('model','Mutual.OrdenDescuentoService');
		$oORDENSERVICE = new OrdenDescuentoService();
		$orden = $oORDENSERVICE->getOrden($id);

		$this->set('cuotas',$cuotas);
		$this->set('orden',$orden);

		if($salida == 'PDF') $this->render('reportes/impresion_pdf','pdf');
		else $this->render('reportes/impresion_xls','blank');

	}


	function conciliar_proveedor(){

		$disable_form = 0;
		$show_asincrono = 0;
		$periodo_cjp1 = date('Y-m-d');
		$periodo_anses1 = date('Y-m-d');
		$periodo_cbu1 = date('Y-m-d');

		if(!empty($this->data)):

//			debug($this->data);
			$disable_form = 1;
			$show_asincrono = 1;


			if($this->data['OrdenDescuento']['archivo_datos']['error'] != 4){

				$periodo_cjp = $this->data['OrdenDescuento']['periodo_control_cjp']['year'].$this->data['OrdenDescuento']['periodo_control_cjp']['month'];
				$periodo_anses = $this->data['OrdenDescuento']['periodo_control_anses']['year'].$this->data['OrdenDescuento']['periodo_control_anses']['month'];
				$periodo_cbu = $this->data['OrdenDescuento']['periodo_control_cbu']['year'].$this->data['OrdenDescuento']['periodo_control_cbu']['month'];
				$proveedor_id = $this->data['OrdenDescuento']['proveedor_id'];
//				$archivo_excel = base64_encode($this->data['OrdenDescuento']['archivo_datos']['tmp_name']);
				$archivo_excel = rand().".xls";

				echo $archivo_excel;

				$this->set('periodo_cjp',$periodo_cjp);
				$this->set('periodo_anses',$periodo_anses);
				$this->set('periodo_cbu',$periodo_cbu);
				$this->set('proveedor_id',$proveedor_id);
				$this->set('archivo_excel',$archivo_excel);

				$periodo_cjp1 = $this->OrdenDescuento->armaFecha($this->data['OrdenDescuento']['periodo_control_cjp']);
				$periodo_anses1 = $this->OrdenDescuento->armaFecha($this->data['OrdenDescuento']['periodo_control_anses']);
				$periodo_cbu1 = $this->OrdenDescuento->armaFecha($this->data['OrdenDescuento']['periodo_control_cbu']);

				$partes = explode('.',$this->data['OrdenDescuento']['archivo_datos']['name']);

				if(strtolower($partes[1]) != 'xls'):
					$disable_form = 0;
					$show_asincrono = 0;
					$this->Mensaje->error("NO ES UN ARCHIVO Microsoft Excel 97/2000/XP (.xls)");
				endif;

				//SUBO EL ARCHIVO A UN TEMPORAL


//				echo TMP;

				if(!move_uploaded_file($this->data['OrdenDescuento']['archivo_datos']['tmp_name'],TMP . $archivo_excel)){
					$disable_form = 0;
					$show_asincrono = 0;
					$this->Mensaje->error("ERROR AL INTENTAR SUBIR EL ARCHIVO AL SERVIDOR");
				}


//				if(!move_uploaded_file($this->data['OrdenDescuento']['archivo_datos']['name'],'/home/adrian/tmp/adrian.xls')) echo "error";
//
//				debug($this->data['OrdenDescuento']['archivo_datos']['tmp_name']);


//				App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
//				App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));
//				App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));
//
//				$oPHPExcel = new PHPExcel();
//
//				$archivo_excel = base64_decode($archivo_excel);
//
//				$oXLS = PHPExcel_IOFactory::load($this->data['OrdenDescuento']['archivo_datos']['tmp_name']);
//
//				debug($oXLS);

			}else{

				$disable_form = 0;
				$show_asincrono = 0;

				$this->Mensaje->error("DEBE INDICAR EL ARCHIVO EXCEL A PROCESAR. VERIFICAR EL FORMATO DEL MISMO!");

			}


		endif;

		$this->set('periodo_cjp1',$periodo_cjp1);
		$this->set('periodo_anses1',$periodo_anses1);
		$this->set('periodo_cbu1',$periodo_cbu1);


		$this->set('disable_form',$disable_form);
		$this->set('show_asincrono',$show_asincrono);


	}


	function consulta(){
		$ordenId = 0;
		$socioId = 0;
		if(!empty($this->data)):
			$this->OrdenDescuento->unbindModel(array('hasMany' => array('OrdenDescuentoCuota'),'belongsTo' => array('Socio','Proveedor')));
			$orden = $this->OrdenDescuento->read("id,socio_id",$this->data['OrdenDescuento']['aprox_id']);
			if(!empty($orden)){
				$ordenId = $orden['OrdenDescuento']['id'];
				$socioId = $orden['OrdenDescuento']['socio_id'];
			}
		endif;
		$this->set('ordenId',$ordenId);
		$this->set('socioId',$socioId);
	}


	function novar(){
		$ordenId = 0;
		if(!empty($this->data)):
			if(isset($this->data['OrdenDescuento']['aprox_id'])){
				$this->OrdenDescuento->unbindModel(array('hasMany' => array('OrdenDescuentoCuota'),'belongsTo' => array('Socio','Proveedor')));
				$orden = $this->OrdenDescuento->read("id,socio_id",$this->data['OrdenDescuento']['aprox_id']);
				if(!empty($orden)){
					$ordenId = $orden['OrdenDescuento']['id'];
					$socioId = $orden['OrdenDescuento']['socio_id'];
				}
			}else if(isset($this->data['OrdenDescuento']['anterior_orden_descuento_id'])){
				$user = $this->Seguridad->user();
				$motivo = $user['Usuario']['usuario'] . " ** " . date('d-m-Y H:i:s') . " | " . $this->data['OrdenDescuento']['motivo_novacion'];
				$ordenNovada = $this->OrdenDescuento->novarOrden($this->data['OrdenDescuento']['anterior_orden_descuento_id'],null,$motivo);
				if(!$ordenNovada){
					$this->Mensaje->error("NO SE PUDO NOVAR LA ORDEN #".$this->data['OrdenDescuento']['anterior_orden_descuento_id']);
				}else{
					$ordenId = $ordenNovada['OrdenDescuento']['id'];
					$socioId = $ordenNovada['OrdenDescuento']['socio_id'];
				}
			}

                endif;
		$this->set('ordenId',$ordenId);
		$this->set('socioId',$socioId);
	}



	function actualizar_importe(){
		$showGrilla = 0;
		$ordenes = null;
		$novar = 0;
		if(!empty($this->data)){
//			debug($this->data);

			if($this->data['MutualProductoSolicitud']['header'] == 1){

				$params = explode("|", $this->data['MutualProductoSolicitud']['tipo_producto_mutual_producto_id']);
//                debug($params);
				$periodo = $this->data['MutualProductoSolicitud']['periodo_corte']['year'].$this->data['MutualProductoSolicitud']['periodo_corte']['month'];
				$ordenes = $this->OrdenDescuento->getOrdenesByProveedor($params[3],$this->data['MutualProductoSolicitud']['codigo_organismo'],$periodo,$this->data['MutualProductoSolicitud']['actual'],$this->data['MutualProductoSolicitud']['nuevo']);
				$novar = (isset($this->data['MutualProductoSolicitud']['novar']) ? 1 : 0);
			}else{
				$novar = $this->data['MutualProductoSolicitud']['novar'];
				foreach($this->data['OrdenDescuento']['id'] as $id => $importe){
					if($importe != 0){

						if($novar == 1){
							$nuevaOrden = $this->OrdenDescuento->novarOrden($id,null,"NOVACION POR ACTUALIZACION DE IMPORTES");
							$id = $nuevaOrden['OrdenDescuento']['id'];
						}
//						if($this->OrdenDescuento->actualizarValor($id,'importe_cuota',$importe) && $this->OrdenDescuento->actualizarValor($id,'importe_total',$importe)){
                        if($this->OrdenDescuento->actualizarValorImporteTotalAndImporteCuota($id,$importe)){
							continue;
						}else{
							$this->Mensaje->error("SE PRODUJO UN ERROR AL ACTUALIZAR EL IMPORTE DE LA ORDEN #$id");
							break;
						}
					}
				}
			}
			$showGrilla = 1;
		}

		$this->set('novar',$novar);
		$this->set('ordenes',$ordenes);
		$this->set('showGrilla',$showGrilla);
	}

	function actualizar_importe_puntual($id = null){
		if(!empty($id)){
			$this->render('actualizar_importe_puntual_grilla_ordenes','ajax');
		}else{
			$this->render();
		}
	}




	function arma_vencimientos(){

	}


	function gen_expediente_cuotas(){

	}


    function anular_orden(){
		$orden = null;
        $cuotas = null;
        $idAproxima = null;
        $solicitud = null;
		if(!empty($this->data)):

            $idAproxima = (isset($this->data['OrdenDescuento']['aprox_id']) ? $this->data['OrdenDescuento']['aprox_id'] : null);

            if(isset($this->data['OrdenDescuentoCuota']['ANULAR_ORDEN']) && $this->data['OrdenDescuentoCuota']['ANULAR_ORDEN'] == 1){

                if($this->data['OrdenDescuentoCuota']['orden_activa'] == 1){
                    if($this->OrdenDescuento->anularOrden($this->data['OrdenDescuentoCuota']['orden_descuento_id'],$this->data['OrdenDescuentoCuota']['observaciones'],$this->data['OrdenDescuentoCuota']['situacion'],$this->data['OrdenDescuento']['periodo_hasta'])){
                        $this->Mensaje->ok("LA ORDEN DE DESCUENTO #".$this->data['OrdenDescuentoCuota']['orden_descuento_id']." FUE ANULADA CORRECTAMENTE");
                    }else{
                        $this->Mensaje->error("SE PRODUJO UN ERROR AL ANULAR LA ORDEN DE DESCUENTO #" . $this->data['OrdenDescuentoCuota']['orden_descuento_id']);
                    }
                }else{
                    if($this->OrdenDescuento->activarOrden($this->data['OrdenDescuentoCuota']['orden_descuento_id'])){
                        $this->Mensaje->ok("LA ORDEN DE DESCUENTO #".$this->data['OrdenDescuentoCuota']['orden_descuento_id']." FUE ACTIVADA CORRECTAMENTE");
                    }else{
                        $this->Mensaje->error("SE PRODUJO UN ERROR AL ACTIVAR LA ORDEN DE DESCUENTO #" . $this->data['OrdenDescuentoCuota']['orden_descuento_id']);
                    }

                }
                $idAproxima = $this->data['OrdenDescuentoCuota']['orden_descuento_id'];
            }

			if(!empty($idAproxima)){
                            $orden = $this->OrdenDescuento->getOrden($idAproxima);
                            App::import('model','mutual.MutualProductoSolicitud');
                            $oSOL = new MutualProductoSolicitud();
                            $solicitud = $oSOL->read(null,$orden['OrdenDescuento']['numero']);
                            $cuotas = $this->requestAction('/mutual/orden_descuento_cuotas/cuotas_by_odescuento/'.$orden['OrdenDescuento']['id'].'/1/1');
			}

			App::import('model','Mutual.Liquidacion');
			$oLiq = new Liquidacion();
			$periodos = $oLiq->getPeriodosLiquidados(FALSE,FALSE,null,'DESC',TRUE);

		endif;
        $this->set('idAproxima',$idAproxima);
		$this->set('orden',$orden);
        $this->set('cuotas',$cuotas);
        $this->set('solicitud',$solicitud);
        $this->set('detalle',0);
				$this->set('periodos',$periodos);
    }



}
?>
