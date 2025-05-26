<?php
class SociosController extends PfyjAppController{
	
	var $name = 'Socios';
	
	
	var $autorizar = array('get_persona','resumen_liquidacion', 'modificar_cuotasocial', 'cobro_cuota_adelantada');
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}		
	
	function index($persona_id = null){
		if(empty($persona_id)) $this->redirect('/pfyj/personas');
		$this->Socio->recursive = 4;
		$this->Socio->bindModel(array('belongsTo' => array('Persona'), 'hasOne' => array('OrdenDescuento'),'hasMany' => array('SocioHistorico' => array('className' => 'SocioHistorico','order' => 'SocioHistorico.created DESC'),'SocioCalificacion' => array('className' => 'SocioCalificacion', 'order' => 'SocioCalificacion.periodo DESC,SocioCalificacion.prioritaria DESC,SocioCalificacion.created DESC', 'limit' => '12'),'SocioInforme' => array('className' => 'SocioInforme','order' => 'SocioInforme.created DESC'))));
//		$this->Socio->bindModel(array('belongsTo' => array('Persona'), 'hasOne' => array('OrdenDescuento'),'hasMany' => array('SocioCalificacion' => array('className' => 'SocioCalificacion', 'limit' => '12', 'order' => 'SocioCalificacion.created DESC'))));
		$this->Socio->Persona->bindModel(array('hasMany' => array('SocioSolicitud'=>array('order' => 'SocioSolicitud.created DESC')),'hasOne' => array('Socio')));
		$this->Socio->Persona->recursive = 2;
                $persona = $this->Socio->Persona->read(null,$persona_id);
                
//		App::import('Model','Pfyj.Persona');
//		$oPERSONA = new Persona();
//		$oPERSONA->bindModel(array('hasMany' => array('SocioSolicitud'=>array('order' => 'SocioSolicitud.created DESC')),'hasOne' => array('Socio')));
//		$oPERSONA->recursive = 2;                
//		$persona = $oPERSONA->getPersona($persona_id);                
                
                if(empty($persona)) parent::noDisponible ();
		$this->set('persona',$persona);
		$socio = $this->Socio->find('all',array('conditions'=>array('Socio.persona_id' => $persona_id),'order'=>'Socio.created DESC'));
		$this->set('socio',(!empty($socio) ? $socio[0] : null));  
                
            
            $ordenDto = $this->Socio->getOrdenDtoCuotaSocial($socio[0]['Socio']['id']);
            $cuotaSocialGeneral = $this->Socio->getImpoCuotaSocial($socio[0]['Socio']['id']);
		
            
            
            $this->set('ordenDto',$ordenDto);                
            $this->set('cuotaSocialGeneral',$cuotaSocialGeneral); 
		
	}
	
//	function edit($id = null){
//		if(empty($id)) $this->redirect('index');
//
//		$socio = $this->Socio->read(null,$id);
//		
//		$this->Socio->bindModel(array('belongsTo' => array('Persona')));
//		$this->Socio->Persona->recursive = 3;
//		$this->Socio->Persona->bindModel(array('hasMany' => array('PersonaBeneficio' => array('conditions' => array('PersonaBeneficio.activo' => 1),'order' => 'PersonaBeneficio.created DESC'))));
//		$this->set('persona',$this->Socio->Persona->read(null,$socio['Socio']['persona_id']));
//		$this->data = $socio;		
//	}
	
	function view($id = null){
		if(empty($id)) $this->redirect('/pfyj/personas');
		$this->Socio->bindModel(array('hasOne' => array('OrdenDescuento'),'hasMany' => array('SocioCalificacion' => array('className' => 'SocioCalificacion', 'order' => 'SocioCalificacion.created DESC'))));
		$this->Socio->recursive = 2;
		
		App::import('Model', 'Pfyj.Socio');
		$oSOCIO = new Socio(null); 		
		
		$this->set('socio',$oSOCIO->read(null,$id));
		$this->set('resumen_calificaciones',$oSOCIO->getResumenCalificaciones($id));
		$this->render();
	}
	
	
	function baja($id = null){
		if(empty($id)) $this->redirect('/pfyj/personas');
		
		if(!empty($this->data)){
			if($this->Socio->baja($this->data)){
				$this->redirect('/pfyj/socios/index/'.$this->data['Socio']['persona_id']);
			}
			
		}
		
		$this->Socio->bindModel(array('hasMany' => array('OrdenDescuento','SocioCalificacion' => array('className' => 'SocioCalificacion', 'order' => 'SocioCalificacion.created DESC'))));
		$this->Socio->recursive = 2;
		$this->set('socio',$this->Socio->read(null,$id));
		$this->render();
	}
		
	
	function get_persona($socio_id){
		$this->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->Socio->read(null,$socio_id);
		return $socio;
	}
	
	
	function nueva_calificacion($socio_id=null){
		if(empty($socio_id)) parent::noDisponible();
		$socio = $this->Socio->read(null,$socio_id);
                
                $mknow = mktime(date('H'), date('i'), date('s'), date('m'), date('d'),date('Y'));
                $fechaCalc = $this->Socio->addMonthToDate($mknow);
                $periodo = date('Ym',$fechaCalc);
                
                App::import('Model','Pfyj.SocioCalificacion');
                $oCAL = new SocioCalificacion(); 
                
		if(!empty($this->data)):
		
			$socio['Socio']['calificacion'] = $this->data['Socio']['calificacion'];
			$socio['Socio']['fecha_calificacion'] = date("Y-m-d");
			
			$fechaCalificacion = date('Y-m-d H:i:s',strtotime($socio['Socio']['fecha_calificacion']));
			
			$mktime = mktime(date('H'),date('i'),date('s'),date('m',strtotime($socio['Socio']['fecha_calificacion'])),date('d',strtotime($socio['Socio']['fecha_calificacion'])),date('Y',strtotime($socio['Socio']['fecha_calificacion'])));
			$fechaCalificacion = date('Y-m-d H:i:s',$mktime);
			

                        
                        $periodoCalifica = $this->data['Liquidacion']['periodo'];
			
			$oCAL->calificar($this->data['Socio']['id'],$this->data['Socio']['calificacion'],$this->data['PersonaBeneficio']['persona_beneficio_id'],$periodoCalifica,$fechaCalificacion,TRUE);

			$this->redirect('index/'.$this->data['Socio']['persona_id']);
			
		endif;
		
		$this->Socio->bindModel(array('belongsTo' => array('Persona')));
		$this->Socio->Persona->recursive = 3;
		$this->Socio->Persona->bindModel(array('hasOne' => array('Socio'),'hasMany' => array('PersonaBeneficio' => array('conditions' => array('PersonaBeneficio.activo' => 1),'order' => 'PersonaBeneficio.created DESC'))));
		$this->set('persona',$this->Socio->Persona->read(null,$socio['Socio']['persona_id']));
		$this->set('socio',$socio);	
                $this->set('periodo',$periodo);

	}
	
	
	function alta_directa($persona_id =null){
		
		if(empty($persona_id)) parent::noDisponible();
		App::import('Model','Pfyj.Persona');
		$oPERSONA = new Persona();
		$persona = $oPERSONA->getPersona($persona_id);
		
		if(empty($persona)) parent::noDisponible();
		
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBENEFICIO = new PersonaBeneficio();	

		$beneficios = $oBENEFICIO->beneficiosByPersona($persona_id,FALSE,TRUE);

		if(empty($beneficios)):
			$this->Mensaje->error("LA PERSONA NO POSEE BENEFICIOS CARGADOS");
			$this->redirect('/pfyj/persona_beneficios/index/' . $persona_id);
		
		endif;

		if(!empty($persona['Socio']['id'])) $this->redirect('index/' . $persona_id);
		
		if(!empty($this->data)):
                    
                
                    if(empty($this->data['Socio']['persona_beneficio_id'])){
			$this->Mensaje->error("LA PERSONA NO POSEE BENEFICIOS CARGADOS");
			$this->redirect('/pfyj/persona_beneficios/index/' . $persona_id);                        
                    }
                    
                    
			$periodoIni = date('Ym',strtotime($this->Socio->armaFecha($this->data['Socio']['periodo_ini'])));
			$fechaAlta = $this->Socio->armaFecha($this->data['Socio']['fecha_alta']);
			$nroSolicitud = $this->Socio->altaDirecta($this->data['Socio']['persona_id'],$this->data['Socio']['persona_beneficio_id'],$periodoIni,$fechaAlta);
			if(!empty($nroSolicitud) && $nroSolicitud != 0){
				$this->redirect('/pfyj/socio_solicitudes/aprobar/' . $nroSolicitud .'/1');
			}else{
				$this->Mensaje->errores("ERRORES: ",$this->Socio->notificaciones);
			}
		
		endif;
		
		$this->set('persona',$persona);
		$this->set('beneficios',$beneficios);
		
	}
	
	function reactivar($socio_id =null){
		
		if(empty($socio_id)) parent::noDisponible();
		$socio = $this->Socio->read(null,$socio_id);
		if(empty($socio)) parent::noDisponible();
		
		App::import('Model','Pfyj.Persona');
		$oPERSONA = new Persona();
		$persona = $oPERSONA->getPersona($socio['Socio']['persona_id']);
		
		if(empty($persona)) parent::noDisponible();
		
		$this->set('persona',$persona);
		
		
		if(!empty($this->data)):

			$periodoIni = date('Ym',strtotime($this->Socio->armaFecha($this->data['Socio']['periodo_ini'])));
			if($this->Socio->reactivar($this->data['Socio']['id'],$periodoIni)) $this->redirect('index/' . $socio['Socio']['persona_id']);
			$this->Mensaje->error("SE PRODUJO UN ERROR AL PASARLO A VIGENTE");
		
		endif;
		
		$this->set('socio',$socio);
		
	}	
	
	
	function modificar_categoria($socio_id =null){
		
		if(empty($socio_id)) parent::noDisponible();
		$socio = $this->Socio->read(null,$socio_id);
		if(empty($socio)) parent::noDisponible();
		
		App::import('Model','Pfyj.Persona');
		$oPERSONA = new Persona();
		$persona = $oPERSONA->getPersona($socio['Socio']['persona_id']);
		
		if(empty($persona)) parent::noDisponible();
		
		if(!empty($this->data)):
			$socio['Socio']['categoria'] = $this->data['Socio']['categoria'];
			if($this->Socio->save($socio)){
				$this->redirect('index/' . $socio['Socio']['persona_id']);
			}else{
				$this->Mensaje->error("SE PRODUJO UN ERROR AL CAMBIAR LA CATEGORIA DEL SOCIO");
			}
		endif;
		$this->set('persona',$persona);
		$this->set('socio',$socio);
	}
	
	function  alta_informe($socio_id){
            
		if(empty($socio_id)) parent::noDisponible();
		$socio = $this->Socio->read(null,$socio_id);
		if(empty($socio)) parent::noDisponible();
		
		App::import('Model','Pfyj.Persona');
		$oPERSONA = new Persona();
		$persona = $oPERSONA->getPersona($socio['Socio']['persona_id']);
		
		if(empty($persona)) parent::noDisponible();
		
		$this->set('persona',$persona);
                $deuda = NULL;
                #TRAIGO LA DEUDA DEL SOCIO
                
                $periodo_corte = date('Ym');
		$empresa = NULL;
		
		if(!empty($this->data)):

                    if(isset($this->data['SocioInforme']['saldo_conciliado'])){
                        
                        if($this->data['SocioInforme']['saldo_conciliado'] == 0){
                            $this->Mensaje->error("EL SOCIO NO POSEE DEUDA A INFORMAR");
                            $this->redirect('alta_informe/' . $socio['Socio']['id']);
                        }
                        
                        if(!$this->Socio->alta_informe($this->data)){
                            $this->Mensaje->error("SE PRODUJO UN ERROR AL GUARDAR EL INFORME");
                        }else{
                            $this->redirect('index/' . $socio['Socio']['persona_id']);
                        }
                        
//                        debug($this->data);
//                        exit;
//                    }else{
//                        $this->Mensaje->error("EL SOCIO NO POSEE DEUDA A INFORMAR");
//                        $this->redirect('alta_informe/' . $socio['Socio']['id']);                        
                    }
                    
                    
//                    debug($this->data);
                    $empresa = $this->data['SocioInforme']['empresa'];
                    if(empty($empresa)){
                        $this->Mensaje->error("NO EXISTEN EMPRESAS DE INFORMES COMERCIALES CONFIGURADAS");
                        $this->redirect('alta_informe/' . $socio['Socio']['id']);
                    }
                    $periodo_corte = $this->data['SocioInforme']['periodo_corte']['year'].$this->data['SocioInforme']['periodo_corte']['month'];
                    $deuda = $this->Socio->deuda($this->data['SocioInforme']['socio_id'],$periodo_corte);
		
		endif;
                $this->set('empresa',$empresa);
                $this->set('periodo_corte',$periodo_corte);
		$this->set('deuda',$deuda);
		$this->set('socio',$socio);            
            
        }
        
        function modificar_cuotasocial($socio_id = NULL) {
            if(empty($socio_id)) parent::noDisponible();
            $socio = $this->Socio->read(null,$socio_id);
            if(empty($socio)) parent::noDisponible();
            
            if(!empty($this->data)) {
                $socio['Socio']['importe_cuota_social'] = $this->data['Socio']['importe_cuota_social'];
                $socio['Socio']['periodo_hasta_importe_cuota_social'] = (isset($this->data['Socio']['periodo_hasta_importe_cuota_social']) ? $this->data['Socio']['periodo_hasta_importe_cuota_social']['year'].$this->data['Socio']['periodo_hasta_importe_cuota_social']['month'] : NULL);
                if($this->Socio->save($socio)){
                        $this->redirect('index/' . $socio['Socio']['persona_id']);
                }else{
                        $this->Mensaje->error("SE PRODUJO UN ERROR AL ACTUALIZAR CUOTA SOCIAL DEL SOCIO");
                }

            }
            
            $this->set('socio',$socio);
        }
        
        function cobro_cuota_adelantada($socio_id = NULL) {
            
            try {

                if(empty($socio_id)) parent::noDisponible();
                $socio = $this->Socio->read(null,$socio_id);

                if(empty($socio)) parent::noDisponible();

                App::import('Model','Pfyj.Persona');
                $oPERSONA = new Persona();
                $persona = $oPERSONA->getPersona($socio['Socio']['persona_id']);

                if(empty($persona)) parent::noDisponible();
                $cuotas = $cuotasSelected = null;

                $ordenDto = $this->Socio->getOrdenDtoCuotaSocial($socio_id);
                $cuotaSocialGeneral = $this->Socio->getImpoCuotaSocial($socio['Socio']['id']);

                $importeCuotaSocial = ($socio['Socio']['importe_cuota_social'] !== 0 ? $socio['Socio']['importe_cuota_social'] : $cuotaSocialGeneral);

                App::import('Model','mutual.Liquidacion');
                $oLIQ = new Liquidacion();

                App::import('Model','Pfyj.PersonaBeneficio');
                $oBENEFICIO = new PersonaBeneficio();      

                $beneficio = $oBENEFICIO->read('codigo_beneficio', $ordenDto['OrdenDescuento']['persona_beneficio_id']);

                $periodos = $oLIQ->generarPeriodosDesdeUltimoCerrado($beneficio['PersonaBeneficio']['codigo_beneficio'], 12);

                App::import('Model','mutual.OrdenDescuentoCuota');
                $oCUOTA = new OrdenDescuentoCuota();  

                foreach ($periodos as $periodo => $label) {
                    $cuotaSocial = $oCUOTA->getCuotaSocialByOrdenIdByPeriodo($ordenDto['OrdenDescuento']['id'], $periodo);
                    if(empty($cuotaSocial)) {
                        $cuotas[$periodo] = array(
                            'id' => 0,
                            'socio_id' => $socio_id,
                            'orden_descuento_id' => $ordenDto['OrdenDescuento']['id'],
                            'tipo_orden_dto' => $ordenDto['OrdenDescuento']['tipo_orden_dto'],
                            'proveedor_id' => $ordenDto['OrdenDescuento']['proveedor_id'],
                            'persona_beneficio_id' => $ordenDto['OrdenDescuento']['persona_beneficio_id'],
                            'tipo_producto' => $ordenDto['OrdenDescuento']['tipo_producto'],
                            'tipo_cuota' => 'MUTUTCUOCSOC',
                            'periodo' => $periodo,
                            'nro_cuota' => 0,
                            'importe' => $importeCuotaSocial,
                            'vencimiento' => date('Y-m-d'),
                            'vencimiento_proveedor' => date('Y-m-d'),
                            'estado' => 'A',	
                            'situacion' => 'MUTUSICUMUTU',
                            'tipo_numero' => $ordenDto['OrdenDescuento']['tipo_orden_dto']." #" . $ordenDto['OrdenDescuento']['numero']
                        );
                    }
                }
                $this->set('ordenDto',$ordenDto);
                $this->set('persona',$persona);
                $this->set('socio',$socio);            
                $this->set('cuotas',$cuotas);   

                $this->set('importeCuotaSocial',$importeCuotaSocial);

                if(!empty($this->data)) {

                    $periodos = $this->data['OrdenDescuentoCuota']['periodo'];
                    $importes = $this->data['OrdenDescuentoCuota']['periodo_importe'];

                    $oCUOTA->begin();

                    $cuotasSelected = array();

                    $TOTAL = 0;

                    foreach($periodos as $periodo => $value) {


                        $cuota = array('OrdenDescuentoCuota' => array(
                            'id' => 0,
                            'tipo_orden_dto' => $this->data['OrdenDescuentoCuota']['tipo_orden_dto'],
                            'orden_descuento_id' => $this->data['OrdenDescuentoCuota']['orden_descuento_id'],
                            'persona_beneficio_id' => $this->data['OrdenDescuentoCuota']['persona_beneficio_id'],
                            'socio_id' => $this->data['OrdenDescuentoCuota']['socio_id'],
                            'tipo_orden_dto' => $this->data['OrdenDescuentoCuota']['tipo_orden_dto'],
                            'tipo_producto' => $this->data['OrdenDescuentoCuota']['tipo_producto'],
                            'periodo' => $periodo,
                            'nro_cuota' =>  0,
                            'tipo_cuota' => $this->data['OrdenDescuentoCuota']['tipo_cuota'],
                            'estado' => 'A',	
                            'situacion' => 'MUTUSICUMUTU',
                            'importe' => $importes[$periodo],
                            'proveedor_id' => $this->data['OrdenDescuentoCuota']['proveedor_id'],
                            'vencimiento' => date('Y-m-d'),
                            'vencimiento_proveedor' => date('Y-m-d'),			
                        ));                    

                        if(!$oCUOTA->save($cuota)) { $oCUOTA->rollback(); break;}
                        $cuota['OrdenDescuentoCuota']['id'] = $oCUOTA->getLastInsertID();

                        $TOTAL += $importes[$periodo];

                        array_push($cuotasSelected, $cuota);



                    }

                    App::import('Model','mutual.OrdenCajaCobro');
                    $oCACOB = new OrdenCajaCobro(); 

                    App::import('Model','mutual.OrdenCajaCobroCuota');
                    $oCACOBCU = new OrdenCajaCobroCuota();                

                    $ordenCajaCobro = array(
                        'OrdenCajaCobro' => array(
                            'id' => 0,
                            'socio_id' => $this->data['OrdenDescuentoCuota']['socio_id'],
                            'fecha_vto' => date('Y-m-d'),
                            'importe' => $TOTAL,
                            'importe_cobrado' => $TOTAL,
                            'tipo_imputacion' => 0,

                        )
                    );

                    if(!$oCACOB->save($ordenCajaCobro)) {$oCUOTA->rollback();}
                    $ordenCajaCobro['OrdenCajaCobro']['id'] = $oCACOB->getLastInsertID();

                    foreach ($cuotasSelected as $key => $value) {

                            $ordenCajaCobroCuota = array();
                            $ordenCajaCobroCuota['id'] = 0;
                            $ordenCajaCobroCuota['orden_caja_cobro_id'] = $ordenCajaCobro['OrdenCajaCobro']['id'];
                            $ordenCajaCobroCuota['orden_descuento_cuota_id'] = $value['OrdenDescuentoCuota']['id'];
                            $ordenCajaCobroCuota['importe'] = $value['OrdenDescuentoCuota']['importe'];
                            $ordenCajaCobroCuota['importe_abonado'] = $value['OrdenDescuentoCuota']['importe'];
                            $ordenCajaCobroCuota['saldo_cuota'] = 0;  

                            if(!$oCACOBCU->save($ordenCajaCobroCuota)) {$oCUOTA->rollback();break;}

                    }
                    $oCUOTA->commit();
                    $this->redirect('/mutual/orden_descuento_cobros/add_recibo/'.$ordenCajaCobro['OrdenCajaCobro']['id']);
                }

            } catch (Exception $exc) {
                $oCUOTA->rollback();
                echo $exc->getTraceAsString();
            }            
            
        }
}
?>