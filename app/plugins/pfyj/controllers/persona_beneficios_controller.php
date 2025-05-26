<?php
class PersonaBeneficiosController extends PfyjAppController{
	var $name = 'PersonaBeneficios';

	var $autorizar = array(
							'combo',
							'beneficios_by_persona',
							'get_beneficio',
							'get_by_idr',
							'bancosHabilitadosToCBU',
							'importe_cuota_social',
							'sub_beneficios',
							'agregar_beneficio_compartido',
							'borrar_beneficio_compartido',
							'get_by_OrdenDto',
                            'validar_cbu_nosis',
                            'get_capacidadad_pago',
	                       'tarjeta_edit',
	                       'consulta_siisa_ajax'
	);

	function beforeFilter(){
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();
	}

	function index($persona_id = null){
		if(empty($persona_id)) $this->redirect('/pfyj/personas');
		$this->PersonaBeneficio->bindModel(array('belongsTo' => array('Persona')));
		$this->PersonaBeneficio->Persona->bindModel(array('hasMany' => array('Socio')));
		$persona = $this->PersonaBeneficio->Persona->read(null,$persona_id);

		if(!empty($persona['Socio']))$persona['Socio'] = $persona['Socio'][0];
		$this->set('persona',$persona);
		$this->PersonaBeneficio->unbindModel(array('belongsTo' => array('Persona')));
		$this->set('beneficios',$this->PersonaBeneficio->find('all',array('conditions'=>array('PersonaBeneficio.persona_id' => $persona_id),'order'=>'PersonaBeneficio.activo DESC, PersonaBeneficio.created DESC')));
	}

	function beneficios_by_persona($persona_id,$soloActivos=1,$sinAcuerdo=0){
            App::import('Model','Pfyj.PersonaBeneficio');
            $oBeneficio = new PersonaBeneficio();
            $beneficios = array();
            if($soloActivos==0){
                $beneficios = $oBeneficio->beneficiosByPersona($persona_id);
            }else if($soloActivos && !$sinAcuerdo){
				// $beneficios = $oBeneficio->beneficiosActivosByPersona($persona_id);
				$beneficios = $oBeneficio->beneficiosByPersona($persona_id,TRUE,TRUE);
            }else if($soloActivos && $sinAcuerdo){
                $beneficios = $oBeneficio->beneficiosActivosSinAcuerdoByPersona($persona_id);
            }
            return $beneficios;
	}

	function get_beneficio($id){
		return $this->PersonaBeneficio->read(null,$id);
	}

	function add($persona_id = null){

		if(empty($persona_id)) $this->redirect('/pfyj/personas');

		if(!empty($this->data)){
//			debug($this->data);

//			if(!empty($this->data['PersonaBeneficio']['cbu'])){
//				App::import('Model'.'Config.Banco');
//				$oBANCO = new Banco();
//				$decoCBU = $oBANCO->deco_cbu($this->data['PersonaBeneficio']['cbu']);
////				$decoCBU = $this->requestAction('/config/bancos/deco_cbu/'.$this->data['PersonaBeneficio']['cbu']);
//
//				$this->data['PersonaBeneficio']['banco_id'] = $decoCBU['codigo_banco'];
//				$this->data['PersonaBeneficio']['nro_sucursal'] = $decoCBU['sucursal'];
//				$this->data['PersonaBeneficio']['tipo_cta_bco'] = $decoCBU['tipo_cta_bco'];
//				$this->data['PersonaBeneficio']['nro_cta_bco'] = $decoCBU['nro_cta_bco'];
//			}

			if($this->PersonaBeneficio->guardar($this->data)){
    			$this->Auditoria->log();
    			$this->Mensaje->okGuardar();
    			$this->redirect('/pfyj/persona_beneficios/index/'.$persona_id);
			}else{
				$this->Mensaje->errores("ERRORES: ",$this->PersonaBeneficio->notificaciones);
			}
		}


		$this->PersonaBeneficio->bindModel(array('belongsTo' => array('Persona')));
		$this->PersonaBeneficio->Persona->bindModel(array('hasMany' => array('Socio')));
		$persona = $this->PersonaBeneficio->Persona->read(null,$persona_id);
		$persona['Socio'] = (isset($persona['Socio'][0]) ? $persona['Socio'][0] : null);
		$this->set('persona',$persona);
		$this->PersonaBeneficio->unbindModel(array('belongsTo' => array('Persona')));
		$this->set('bcos_hab',$this->PersonaBeneficio->bancosHabilitadosToCBU());

        $organismosCBU = $this->requestAction("/config/global_datos/get_organismos_activos_cbu");
        $organismosCBU = array_keys($organismosCBU);
        $organismosCBU = "'".implode("','", $organismosCBU)."'";
        $this->set('organismosCBU',$organismosCBU);


	}

	function edit($id = null){

		if(empty($id)) $this->redirect('/pfyj/personas');

		if(!empty($this->data)){

//			if(!empty($this->data['PersonaBeneficio']['cbu'])){
//				$decoCBU = $this->requestAction('/config/bancos/deco_cbu/'.$this->data['PersonaBeneficio']['cbu']);
//
//				$this->data['PersonaBeneficio']['banco_id'] = $decoCBU['codigo_banco'];
//				$this->data['PersonaBeneficio']['nro_sucursal'] = $decoCBU['sucursal'];
//				$this->data['PersonaBeneficio']['tipo_cta_bco'] = $decoCBU['tipo_cta_bco'];
//				$this->data['PersonaBeneficio']['nro_cta_bco'] = $decoCBU['nro_cta_bco'];
//			}

			if($this->PersonaBeneficio->guardar($this->data)){
//    			$this->Auditoria->log();
    			$this->Mensaje->okGuardar();
    			$this->redirect('/pfyj/persona_beneficios/index/'.$this->data['PersonaBeneficio']['persona_id']);
			}else{
				$this->Mensaje->errores("ERRORES: ",$this->PersonaBeneficio->notificaciones);
			}
		}

		$beneficio = $this->PersonaBeneficio->read(null,$id);
		$this->PersonaBeneficio->bindModel(array('belongsTo' => array('Persona')));
		$this->PersonaBeneficio->Persona->bindModel(array('hasOne' => array('Socio')));
		$this->set('persona',$this->PersonaBeneficio->Persona->read(null,$beneficio['PersonaBeneficio']['persona_id']));
		$this->PersonaBeneficio->unbindModel(array('belongsTo' => array('Persona')));
		$this->data = $beneficio;
		$this->set('bcos_hab',$this->PersonaBeneficio->bancosHabilitadosToCBU());
        $organismosCBU = $this->requestAction("/config/global_datos/get_organismos_activos_cbu");
        $organismosCBU = array_keys($organismosCBU);
        $organismosCBU = "'".implode("','", $organismosCBU)."'";
        $this->set('organismosCBU',$organismosCBU);

	}

	function view($id = null,$render='view'){
		if(empty($id)) $this->redirect('/pfyj/personas');
		$beneficio = $this->PersonaBeneficio->read(null,$id);
		if($render!='view'){
			$this->PersonaBeneficio->recursive = 2;
			$this->PersonaBeneficio->bindModel(array('belongsTo' => array('Persona')));
			$this->PersonaBeneficio->Persona->bindModel(array('hasOne' => array('Socio')));
			$this->set('persona',$this->PersonaBeneficio->Persona->read(null,$beneficio['PersonaBeneficio']['persona_id']));
		}
		$this->set('beneficio',$beneficio);
		$this->render($render);
	}


	function baja($id = null){
		if(empty($id)) $this->redirect('/pfyj/personas');

		if(!empty($this->data)){
			if($this->PersonaBeneficio->baja($this->data)){
				$this->redirect('/pfyj/persona_beneficios/index/'.$this->data['PersonaBeneficio']['persona_id']);
			}else{
				$this->Mensaje->error("NO SE PUDO EFECTUAR LA BAJA");
			}

		}

		App::import('model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$periodos = $oLiq->getPeriodosLiquidados(FALSE,FALSE,null,'DESC',TRUE);

		$beneficio = $this->PersonaBeneficio->read(null,$id);
		$this->PersonaBeneficio->bindModel(array('belongsTo' => array('Persona')));
		$this->PersonaBeneficio->Persona->bindModel(array('hasOne' => array('Socio')));
		$this->set('persona',$this->PersonaBeneficio->Persona->read(null,$beneficio['PersonaBeneficio']['persona_id']));
		$this->set('beneficio',$beneficio);
		$this->set('periodos',$periodos);
		$this->render();
	}

//	function bancosHabilitadosToCBU(){
//		App::import('Model', 'Config.Banco');
//		$this->Banco = new Banco(null);
//		$bancos = $this->Banco->find('list',array('conditions' => array('Banco.activo' => 1,'Banco.beneficio' => 1),'fields' => 'Banco.nombre', 'order' => 'Banco.nombre'));
//		$bancos = array_keys($bancos);
//		$tmp = array();
//		foreach($bancos as $idx => $banco){
//			$bcoCbu = substr($banco,-3,3);
//			array_push($tmp,"'".$bcoCbu."'");
//		}
//		$bancos = implode(',',$tmp);
//		return $bancos;
//	}


	function combo($model,$persona_id = 0,$disable=0,$empty=0,$excluye=0,$label="BENEFICIO",$selected=0){

		App::import('Model', 'Pfyj.PersonaBeneficio');
		$oBen = new PersonaBeneficio();

		if($persona_id == 0) $this->redirect('/pfyj/personas');
		if($excluye==0)$beneficios = $oBen->find('all',array('conditions'=>array('PersonaBeneficio.persona_id' => $persona_id,'PersonaBeneficio.activo' => 1),'order'=>'PersonaBeneficio.created DESC'));
		else $beneficios = $oBen->find('all',array('conditions'=>array('PersonaBeneficio.persona_id' => $persona_id,'PersonaBeneficio.activo' => 1,'PersonaBeneficio.id <> ' => $excluye),'order'=>'PersonaBeneficio.created DESC'));
		$cmb = array();

		foreach($beneficios as $b){

//			$str = $oBen->GlobalDato('concepto_1',$b['PersonaBeneficio']['codigo_beneficio']) . " - " .$oBen->getStrBeneficio($b['PersonaBeneficio']['id']);
			$str = $oBen->getStrBeneficio($b['PersonaBeneficio']['id']);
			$cmb[$b['PersonaBeneficio']['id']] = $str;

		}

		$this->set('beneficios',$cmb);

		$this->set('label',$label);
		$this->set('selected',$selected);
		$this->set('model',$model);
		$this->set('disabled',$disable);
		$this->set('empty',$empty);

		$this->render('combo','blank');
	}

	function getListaBeneficiosByPersona($persona_id,$soloActivos=true){
		App::import('Model', 'Pfyj.PersonaBeneficio');
		$oBen = new PersonaBeneficio();
		if(!$soloActivos)$beneficios = $oBen->find('all',array('conditions'=>array('PersonaBeneficio.persona_id' => $persona_id,'PersonaBeneficio.activo' => 1),'order'=>'PersonaBeneficio.created DESC'));
		else $beneficios = $oBen->find('all',array('conditions'=>array('PersonaBeneficio.persona_id' => $persona_id,'PersonaBeneficio.activo' => 1),'order'=>'PersonaBeneficio.created DESC'));

	}

	function get_by_idr($idr){
		$beneficio = $this->PersonaBeneficio->getByIdr($idr);
		return $beneficio;
	}

	function get_by_OrdenDto($ordenDtoId){
		$beneficio = $this->PersonaBeneficio->getByOrdenDto($ordenDtoId);
		return $beneficio;
	}


	function importe_cuota_social($id){
		$this->set('importe_cuota_social',$this->PersonaBeneficio->getImporteCuotaSocial($id));
		$this->render('importe_cuota_social','ajax');
	}


	function reasignar_beneficio_puntual($socio_id){
		if(empty($socio_id)) parent::noDisponible();
		if(empty($socio_id)) $this->redirect('/pfyj/personas');

		App::import('Model','Pfyj.Socio');
		$oSOCIO = new Socio();
		$oSOCIO->bindModel(array('belongsTo' => array('Persona')));
		$socio = $oSOCIO->read(null,$socio_id);

		$this->set('menuPersonas',1);
		$this->set('socio',$socio);
		if(empty($socio)) $this->redirect('/pfyj/personas');
		$cuotas = NULL;

		if(!empty($this->data)){
			App::import('Model','Mutual.OrdenDescuentoCuota');
			$oCUOTA = new OrdenDescuentoCuota();
			$cuotas = $oCUOTA->cuotasAdeudadasBySocioByBeneficio($socio_id,$this->data['PersonaBeneficio']['persona_beneficio_id']);
			if($this->data['PersonaBeneficio']['reasignar'] == 1){
				foreach($this->data['OrdenDescuentoCuota']['orden_descuento_cuota_id'] as $idCuota => $importe){
					$oCUOTA->modificaBeneficio($idCuota,$this->data['OrdenDescuentoCuota']['persona_beneficio_id']);
				}
				$this->Mensaje->ok('LAS CUOTAS FUERON REASIGNADAS DE BENEFICIO CORRECTAMENTE!');
				$this->redirect('reasignar_beneficio_puntual/'.$socio_id);

			}
		}
		$this->set('cuotas',$cuotas);

	}


	function sub_beneficios($persona_beneficio_id){

		if(empty($persona_beneficio_id)) parent::noDisponible();
		$this->PersonaBeneficio->bindModel(array('belongsTo' => array('Persona')));
		$this->PersonaBeneficio->Persona->bindModel(array('hasMany' => array('Socio')));
		$beneficio = $this->PersonaBeneficio->read(null,$persona_beneficio_id);
		if(empty($beneficio))$this->redirect('/pfyj/personas');
		$persona = $this->PersonaBeneficio->Persona->read(null,$beneficio['PersonaBeneficio']['persona_id']);

		if(!empty($persona['Socio']))$persona['Socio'] = $persona['Socio'][0];

		$beneficio = $this->PersonaBeneficio->armaDatos($beneficio);

		$this->set('beneficio',$beneficio);
		$this->set('persona',$persona);

	}


	function acuerdo_debito($persona_beneficio_id){

		if(empty($persona_beneficio_id)) parent::noDisponible();

		#GUARDO EL ACUERDO DE DEBITO
		if(!empty($this->data) && $this->data['PersonaBeneficio']['action'] == 'ACUERDO_DEBITO'){
			$this->PersonaBeneficio->id = $this->data['PersonaBeneficio']['id'];
			if(empty($this->data['PersonaBeneficio']['acuerdo_debito'])) $this->data['PersonaBeneficio']['acuerdo_debito'] = 0;
			if($this->PersonaBeneficio->saveField('acuerdo_debito',$this->data['PersonaBeneficio']['acuerdo_debito'])){
    			if($this->data['PersonaBeneficio']['acuerdo_debito'] != 0)$this->Mensaje->ok("SE FIJO UN ACUERDO DE DEBITO DE $ ".number_format($this->data['PersonaBeneficio']['acuerdo_debito'])." PARA EL BENEFICIO #$persona_beneficio_id");
    			$this->redirect('/pfyj/persona_beneficios/index/'.$this->data['PersonaBeneficio']['persona_id']);				;
			}else{
				$this->Mensaje->errores("ERRORES: ",$this->PersonaBeneficio->notificaciones);
			}
		}

		#GUARDO EL IMPORTE DE FRACCIONAMIENTO DEL REGISTRO
		if(!empty($this->data) && $this->data['PersonaBeneficio']['action'] == 'MAXIMO_REG_CBU'){
			$this->PersonaBeneficio->id = $this->data['PersonaBeneficio']['id'];
			if(empty($this->data['PersonaBeneficio']['importe_max_registro_cbu'])) $this->data['PersonaBeneficio']['importe_max_registro_cbu'] = 0;
			if($this->PersonaBeneficio->saveField('importe_max_registro_cbu',$this->data['PersonaBeneficio']['importe_max_registro_cbu'])){
    			if($this->data['PersonaBeneficio']['importe_max_registro_cbu'] != 0)$this->Mensaje->ok("SE FIJO UN MONTO MAXIMO DE FRACCIONAMIENTO DE $ ".number_format($this->data['PersonaBeneficio']['importe_max_registro_cbu'])." PARA EL BENEFICIO #$persona_beneficio_id");
    			$this->redirect('/pfyj/persona_beneficios/index/'.$this->data['PersonaBeneficio']['persona_id']);				;
			}else{
				$this->Mensaje->errores("ERRORES: ",$this->PersonaBeneficio->notificaciones);
			}
		}

		$this->PersonaBeneficio->bindModel(array('belongsTo' => array('Persona')));
		$this->PersonaBeneficio->Persona->bindModel(array('hasMany' => array('Socio')));
		$beneficio = $this->PersonaBeneficio->read(null,$persona_beneficio_id);
		if(empty($beneficio))$this->redirect('/pfyj/personas');
		$persona = $this->PersonaBeneficio->Persona->read(null,$beneficio['PersonaBeneficio']['persona_id']);

		if(!empty($persona['Socio']))$persona['Socio'] = $persona['Socio'][0];

		$beneficio = $this->PersonaBeneficio->armaDatos($beneficio);

		$this->set('beneficio',$beneficio);
		$this->set('persona',$persona);
	}


	function agregar_beneficio_compartido($persona_beneficio_id){

		if(empty($persona_beneficio_id)) parent::noDisponible();

		if(!empty($this->data)){

			App::import('Model','Pfyj.PersonaBeneficioCompartido');
			$oBC = new PersonaBeneficioCompartido();
			$error = $oBC->agregar($this->data);
			if(empty($error)){
				$this->redirect('sub_beneficios/'.$this->data['PersonaBeneficioCompartido']['persona_beneficio_id']);
				$this->Mensaje->ok("DATOS GRABADOS CORRECTAMENTE!");
			}else{
				$this->Mensaje->error($error);
			}

		}

		$this->PersonaBeneficio->bindModel(array('belongsTo' => array('Persona')));
		$this->PersonaBeneficio->Persona->bindModel(array('hasMany' => array('Socio')));
		$beneficio = $this->PersonaBeneficio->read(null,$persona_beneficio_id);
		if(empty($beneficio))$this->redirect('/pfyj/personas');
		$persona = $this->PersonaBeneficio->Persona->read(null,$beneficio['PersonaBeneficio']['persona_id']);

		if(!empty($persona['Socio']))$persona['Socio'] = $persona['Socio'][0];

		$beneficio = $this->PersonaBeneficio->armaDatos($beneficio);

		$this->set('beneficio',$beneficio);
		$this->set('persona',$persona);


	}


	function borrar_beneficio_compartido($id = null){

		if(empty($id)) parent::noDisponible();

		App::import('Model','Pfyj.PersonaBeneficioCompartido');
		$oBC = new PersonaBeneficioCompartido();
		$bc = $oBC->read(null,$id);
		if(empty($bc)) parent::noDisponible();

		if($oBC->borrar($id)) $this->Mensaje->ok("SUB-BENEFICIO BORRADO CORRECTAMENTE!");
		else $this->Mensaje->error("ERROR AL BORRAR EL SUB-BENEFICIO!");

		$this->redirect('sub_beneficios/'.$bc['PersonaBeneficioCompartido']['persona_beneficio_id']);


	}


        public function validar_cbu_nosis($nDoc,$cbu){
            Configure::write('debug',0);
            App::import('Model','Pfyj.PersonaBeneficio');
            $oBeneficio = new PersonaBeneficio();
            $URL = trim($oBeneficio->GlobalDato("concepto_2","PERSNVID"));
            $USER = trim($oBeneficio->GlobalDato("concepto_3","PERSNVID"));
            $TOKEN = trim($oBeneficio->GlobalDato("concepto_4","PERSNVID"));
            $GRUPO = trim($oBeneficio->GlobalDato("entero_1","PERSNVID"));
            $ACTIVO = trim($oBeneficio->GlobalDato("logico_1","PERSNVID"));
            if(!empty($URL) && $ACTIVO == "1"){
                App::import('Vendor','NosisVidApi',array('file' => 'nosis_vid_api.php'));
                $oNOSIS = new NosisVidApi($URL, $USER, $TOKEN, $GRUPO, $nDoc);
                echo json_encode($oNOSIS->validarCBU($cbu));
                exit;
            }
        }


        public function get_capacidadad_pago($id = NULL){
            if(empty($id)){
                echo "";
                exit;
            }
            Configure::write('debug',0);
            App::import('Model','Pfyj.PersonaBeneficio');
            $oBeneficio = new PersonaBeneficio();
            $datos = $oBeneficio->read('sueldo_neto,debitos_bancarios',$id);
            if(!empty($datos)){
                $response = array(
                    'sueldo_neto' => (!empty($datos['PersonaBeneficio']['sueldo_neto']) ? $datos['PersonaBeneficio']['sueldo_neto'] : 0),
                    'debitos_bancarios' => (!empty($datos['PersonaBeneficio']['debitos_bancarios']) ? $datos['PersonaBeneficio']['debitos_bancarios'] : 0)
                );
                echo json_encode($response);
            }
            exit;
        }

		function tarjeta($beneficioId = NULL){

			if(empty($beneficioId)){parent::noDisponible();}
            
			App::import('Model','Pfyj.PersonaBeneficio');
            $oBeneficio = new PersonaBeneficio();
			$beneficio = $oBeneficio->read(null,$beneficioId);
			if(empty($beneficio)){
				parent::noDisponible();
			}


			App::import('Vendor','crypt');
			$oCRYPT = new Crypt();
			$tarjeta = unserialize($oCRYPT->decrypt($beneficio['PersonaBeneficio']['tarjeta_debito']));


			$this->set('beneficio',$beneficio);
			$this->set('tarjeta',$tarjeta);

			$user = $this->Seguridad->user();
			$this->set('user',$user);

		}
		
		function tarjeta_edit($beneficioId = NULL) {
		    if(empty($beneficioId)){parent::noDisponible();}
		    App::import('Model','Pfyj.PersonaBeneficio');
		    $oBeneficio = new PersonaBeneficio();
		    $beneficio = $oBeneficio->read(null,$beneficioId);
		    if(empty($beneficio)){
		        parent::noDisponible();
		    }
		    
		    if(!empty($this->data)){
		        if($oBeneficio->actualizarTarjetaDebito($this->data)){
		            $this->Mensaje->okGuardar();
		            $this->redirect('/pfyj/persona_beneficios/index/'.$beneficio['PersonaBeneficio']['persona_id']);
		        }else{
		            $this->Mensaje->errores("ERRORES: ",$oBeneficio->notificaciones);
		        }
		        
		    }
		    
		    $beneficio = $oBeneficio->armaDatos($beneficio);
		    App::import('model','pfyj.Persona');
		    $oPER = new Persona();
		    $persona = $oPER->read(null,$beneficio['PersonaBeneficio']['persona_id']);
		    
		    $this->set('beneficio',$beneficio);
		    $this->set('persona',$persona);
		}

		
		function consulta_siisa_ajax($beneficioId) {
		    Configure::write('debug',0);
		    App::import('Model', 'Pfyj.PersonaBeneficio');
		    $oBENEFICIO = new PersonaBeneficio();
		    $beneficio = $oBENEFICIO->read(null, $beneficioId);
		    $productoSIISA = $oBENEFICIO::GlobalDato('concepto_4', $beneficio['PersonaBeneficio']['codigo_empresa']);
		    $productoSIISA = (empty($productoSIISA) ? "Privados" : $productoSIISA);
		    if(!empty($productoSIISA)) {
		        
		        $sueldo_neto = $this->data['sueldo_neto'];
		        $debitos_por_cbu = $this->data['debitos_bancarios'];
		        $cuota_credito = $this->data['cuota_credito'];

		        App::import('Model', 'Pfyj.Persona');
		        $oPERSONA = new Persona();
		        $persona = $oPERSONA->read(null, $beneficio['PersonaBeneficio']['persona_id']);
		        
		        $parameters = array(
		            'nroDoc' => $persona['Persona']['documento'],
		            'nombre' => $persona['Persona']['nombre'] . " " . $persona['Persona']['apellido'],
		            'tipo_de_producto' => $productoSIISA,
		            'sueldo_neto' => floatval($sueldo_neto),
		            'debitos_por_cbu' => floatval($debitos_por_cbu),
		            'cuota_credito' => floatval($cuota_credito),
		        );
		        
		        App::import('Vendor','SIISAService',array('file' => 'siisa_service.php'));
		        $oSIISA = new SIISAService();
		        $respuesta = $oSIISA->executePolicyByParameters($parameters);
		        echo json_encode($respuesta);
		        exit;
		    }
		    
		}
}
?>
