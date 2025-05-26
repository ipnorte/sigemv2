<?php
/*
 * 
 * ALTER TABLE `sigem_db`.`co_ejercicios` ADD COLUMN `resultado_co_plan_cuenta_id` INT(11) DEFAULT 0 NULL AFTER `co_plan_cuenta_id`;
 */
class AsientosController extends ContabilidadAppController{
	
	var $name = 'Asientos';
	var $uses = array('contabilidad.Asiento', 'contabilidad.MutualProcesoAsiento');
	
	var $autorizar = array(
				'cargar_renglones', 'cargar_renglones_remover', 'delete', 'recuperar', 'anular', 'apertura', 'cierre_periodo', 
                                'cierre_ejercicio', 'asiento_resultado', 'asiento_final', 'asiento_resultado_borrar', 'asiento_resultado_view',
                                'asiento_final_view', 'asiento_final_borrar', 'asiento_apertura', 'apertura_manual', 'apertura_automatico'
	);
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	
	
	function index(){

            $condiciones = null;		

            $search = null;

            if(!empty($this->data)){

                $this->Session->del($this->name.'.search');
                $search = $this->data;

            }else if($this->Session->check($this->name.'.search')){

                $search = $this->Session->read($this->name.'.search');
                $this->data = $search;

            }

//			debug($search);
            if(!empty($this->data)){
                if(!empty($search['Asiento']['nro_asiento'])){
                    $ejercicio = $this->Asiento->traeEjercicio($search['Asiento']['co_ejercicio_id']);
                    $condiciones = array(
                            'Asiento.nro_asiento  >= ' => $search['Asiento']['nro_asiento'],			
                            'Asiento.co_ejercicio_id' => $ejercicio['id'],
                    );
                    $this->data['Asiento']['nro_asiento'] = '';
                    $this->paginate = array(
                                'limit' => 30,
                                'order' => array('Asiento.nro_asiento' => 'ASC')
                    );

                }else if(isset($search['Asiento']['desbalanceado'])){
                    $ejercicio = $this->Asiento->traeEjercicio($search['Asiento']['co_ejercicio_id']);
                    $condiciones = array(
                            'Asiento.debe  <> Asiento.haber',	
                            'Asiento.co_ejercicio_id' => $ejercicio['id'],
                    );					
                    $this->paginate = array(
                            'limit' => 30,
                            'order' => array('Asiento.fecha' => 'ASC')
                    );

                }else if(!empty($search['Asiento']['fecha'])){
                    $ejercicio = $this->Asiento->traeEjercicio($search['Asiento']['co_ejercicio_id']);
                    $condiciones = array(
                            'Asiento.fecha  >= ' => $search['Asiento']['fecha']['year'] ."-".$search['Asiento']['fecha']['month'] ."-".$search['Asiento']['fecha']['day'],	
                            'Asiento.co_ejercicio_id' => $ejercicio['id'],
                    );					
                    $this->paginate = array(
                            'limit' => 30,
                            'order' => array('Asiento.fecha' => 'ASC')
                    );

                }else{
                    $ejercicio = $this->Asiento->traeEjercicio($this->data['ejercicio']['id']);
                    $condiciones = array(
                            'Asiento.fecha  >= ' => substr($ejercicio['fecha_asiento'],0,8) . '01',	
                            'Asiento.co_ejercicio_id' => $ejercicio['id'],
                    );					
                    $this->paginate = array(
                            'limit' => 30,
                            'order' => array('Asiento.fecha', 'Asiento.nro_asiento')
                    );

                }

                $this->Session->write($this->name.'.search', $search);

                $this->set('ejercicio', $ejercicio);
                $this->set('asientos', $this->paginate(null,$condiciones));			

            }
		
	}
	
	function view($asiento_id=null){
		$this->set('asiento', $this->Asiento->getAsiento($asiento_id));	

	}
	
	function add($ejercicio_id=null){

            $this->Session->del('grilla_asientos');

            if(!empty($this->data)):

                # 5) ######################################################
                # reconstruyo el campo renglonesSerialize con los datos de la sesión
                # con el uuid para que el modelo ni se entere del cambio
                if(!isset($this->data['Asiento']['renglonesSerialize'])){
                    $renglones = $this->Session->read('grilla_asientos_' . $this->data['Asiento']['uuid']);
                    $this->data['Asiento']['renglonesSerialize'] = base64_encode(serialize($renglones));
                }
                ######################################################

                if($this->Asiento->guardar($this->data)):
                    $this->Mensaje->okGuardar();
                    //				$this->redirect('/contabilidad/plan_cuentas/index/'.$this->data['PlanCuenta']['co_ejercicio_id']);
                else:
                    $this->Mensaje->errorGuardar();
                endif;
            endif;

            # 1) ######################################################
            $this->set('uuid', $this->Asiento->generarPIN(20));
            //este UUID se guarda como hidden en el formulario del detalle
            #######################################################

            $ejercicio = $this->Asiento->traeEjercicio($ejercicio_id);
            $fechaControl = $ejercicio['fecha_cierre'];
            $mkTControl = mktime(0,0,0,date('m',strtotime($fechaControl)),date('d',strtotime($fechaControl)),date('Y',strtotime($fechaControl)));

            //		$fControl = $this->Asiento->addDayToDate($mkTControl);
            $ejercicio['fecha_control'] = date('d/m/Y',$mkTControl);
            $this->set('apertura', 0);
            $this->set('ejercicio', $ejercicio);
	}

        
	function delete($asiento_id=null){

            if(empty($asiento_id)) $this->redirect(array('action'=>'index'));

            $asiento['Asiento']['id'] = $asiento_id;
            $asiento['Asiento']['borrado'] = 1;
            if($this->Asiento->save($asiento)):		
                $this->Mensaje->okGuardar();
            else:
                $this->Mensaje->errorGuardar();
            endif;

            $this->redirect(array('action'=>'index'));				
		
	}
	
	
	function recuperar($asiento_id=null){

            if(empty($asiento_id)) $this->redirect(array('action'=>'index'));

            $asiento['Asiento']['id'] = $asiento_id;
            $asiento['Asiento']['borrado'] = 0;
            if($this->Asiento->save($asiento)):		
                $this->Mensaje->okGuardar();
            else:
                $this->Mensaje->errorGuardar();
            endif;

            $this->redirect(array('action'=>'index'));				
		
	}
	
	function edit($asiento_id=null){
		
            $this->Session->del('grilla_asientos');

            if(!empty($this->data)):

                $renglones = base64_decode($this->data['Asiento']['renglonesSerialize']);
                $renglones = unserialize($renglones);

//			if($this->Asiento->guardar($this->data)):
//				$this->Mensaje->okGuardar();
//				$this->redirect('/contabilidad/plan_cuentas/index/'.$this->data['PlanCuenta']['co_ejercicio_id']);
//			else:
//				$this->Mensaje->errorGuardar();
//			endif;
            endif;
            $asiento = $this->Asiento->getAsiento($asiento_id);
            $ejercicio = $this->Asiento->traeEjercicio($asiento['Asiento']['co_ejercicio_id']);
            $this->set('ejercicio', $ejercicio);
            $this->set('asiento', $asiento['Asiento']);
            $this->set('renglon', $asiento['renglones']);
	}

        
	function anular($asiento_id=null){
		
            if(empty($asiento_id)) $this->redirect(array('action'=>'index'));

            if(!empty($this->data)):
                if($this->Asiento->guardar($this->data)):
                    $this->Mensaje->okGuardar();
                else:
                    $this->Mensaje->errorGuardar();
                endif;

                $this->redirect(array('action'=>'index'));				
            endif;

            $asiento = $this->Asiento->getAsiento($asiento_id);
            $ejercicio = $this->Asiento->traeEjercicio($asiento['Asiento']['co_ejercicio_id']);

            $temp = array();
            $asientoAnular = array();
            $asientoAnular['id'] = 0;
            $asientoAnular['nro_asiento'] = 0;
            $asientoAnular['co_ejercicio_id'] = $asiento['Asiento']['co_ejercicio_id'];
            $asientoAnular['fecha'] = date('d-m-Y');
            $asientoAnular['referencia'] = 'ANULA ASIENTO NRO. ' . $asiento['Asiento']['nro_asiento'];
            $asientoAnular['debe'] = $asiento['Asiento']['debe'];
            $asientoAnular['haber'] = $asiento['Asiento']['haber'];
            $asientoAnular['borrado'] = 0;
            $asientoAnular['co_asiento_id'] = $asiento['Asiento']['id'];
//		$asientoAnular['anulado'] = 0;
//		array_push($asientoAnular, $temp);

            $temp = array();
            $renglonAnular = array();
            foreach($asiento['renglones'] as $renglon){
                $temp['Asiento']['id'] = 0;
                $temp['Asiento']['co_asiento_id'] = 0;
                $temp['Asiento']['fecha'] = $renglon['AsientoRenglon']['fecha'];
                $temp['Asiento']['co_plan_cuenta_id'] = $renglon['AsientoRenglon']['co_plan_cuenta_id'];
                $temp['Asiento']['referencia_renglon'] = $renglon['AsientoRenglon']['referencia'];
                $temp['Asiento']['debe'] = $renglon['AsientoRenglon']['haber'];
                $temp['Asiento']['haber'] = $renglon['AsientoRenglon']['debe'];
                $temp['Asiento']['codigo_cuenta'] = $renglon['AsientoRenglon']['codigo_cuenta'];
                $temp['Asiento']['descripcion_cuenta'] = $renglon['AsientoRenglon']['descripcion_cuenta'];
                $temp['Asiento']['tipo'] = ($renglon['AsientoRenglon']['debe'] > 0 ? 'H' : 'D');
                $temp['Asiento']['importe'] = ($renglon['AsientoRenglon']['debe'] > 0 ? $renglon['AsientoRenglon']['debe'] : $renglon['AsientoRenglon']['haber']);

                array_push($renglonAnular, $temp);
            }

            $this->set('ejercicio', $ejercicio);
            $this->set('asiento', $asiento['Asiento']);
            $this->set('renglon', $asiento['renglones']);
            $this->set('asientoAnular', $asientoAnular);
            $this->set('renglonAnular', $renglonAnular);
		
	}
	
	function cargar_renglones(){
            $renglones = array();
            $Ok = true;

            # 2) ######################################################
            # genero un id de session unico en base al uuid recibido por post
            if(isset($this->data['Asiento']['uuid']))$ID_SESSION = 'grilla_asientos_' . $this->data['Asiento']['uuid'];
            else $ID_SESSION = 'grilla_asientos';
            #######################################################


            if(!$this->Session->check($ID_SESSION))$this->Session->write($ID_SESSION,$renglones);
            else $renglones = $this->Session->read($ID_SESSION);	
// debug($renglones);
// exit;

            if(!empty($this->params['data']['Asiento']['co_plan_cuenta_id']) && $this->params['data']['Asiento']['importe'] > 0):
                $existe = false;
                foreach($renglones as $renglon){
                    if($renglon['Asiento']['co_plan_cuenta_id'] == $this->params['data']['Asiento']['co_plan_cuenta_id']):
                        $existe = true;
                    endif;
                }
                if(!$existe):
                    array_push($renglones,$this->data);
                else:
                    $Ok = false;
                    $msgError = 'La cuenta existe en el Asiento';
                    $this->set('msgError', $msgError);
                endif;
            else:
                $Ok = false;
                if(empty($this->params['data']['Asiento']['co_plan_cuenta_id'])):
                    $msgError = 'Debe seleccionar una cuenta';
                else: 
                    $msgError = 'El importe debe tener un valor positivo';
                endif;
                $this->set('msgError', $msgError);
            endif;
            $total = array('debe' => 0, 'haber' => 0);
            foreach($renglones as $renglon){
                if($renglon['Asiento']['tipo'] == 'D'):
                    $total['debe'] += $renglon['Asiento']['importe'];
                else:
                    $total['haber'] += $renglon['Asiento']['importe'];
                endif;
            }
            $this->Session->write($ID_SESSION,$renglones);		
            $this->set('renglones',$renglones);
            $this->set('total', $total);
            $this->set('Ok', $Ok);

            # 3) ######################################################
            # mando a la vista el uuid recibido por post para pasarlo como
            # segundo parametro al metodo cargar_renglones_remover
            $this->set('uuid', (isset($this->data['Asiento']['uuid']) ? $this->data['Asiento']['uuid'] : null));
            # en la vista cargar_renglones cambiar el hidden del serializado por un hidden con el uuid
            ########################################################

            $this->render('cargar_renglones','ajax');
	}
	
	function cargar_renglones_remover($key, $uuid = null){
            Configure::write('debug',0);

            # 4) ######################################################
            if(!empty($uuid)) $ID_SESSION = 'grilla_asientos_' . $uuid;
            else $ID_SESSION = 'grilla_asientos';
            ########################################################

            $renglones = $this->Session->read($ID_SESSION);

            if(!empty($renglones)):
                array_splice($renglones,$key,1);
                if(count($renglones) == 0)$this->Session->del($ID_SESSION);
                else $this->Session->write($ID_SESSION,$renglones);
            endif;
            $total = array('debe' => 0, 'haber' => 0);
            foreach($renglones as $renglon){
                if($renglon['Asiento']['tipo'] == 'D'):
                    $total['debe'] += $renglon['Asiento']['importe'];
                else:
                    $total['haber'] += $renglon['Asiento']['importe'];
                endif;
            }
            $this->set('renglones',$renglones);
            $this->set('Ok', true);
            $this->set('total', $total);
            $this->set('uuid', $uuid);
            $this->render('cargar_renglones','ajax');		
	}
	
	
// 	function apertura($ejercicio_id=null){
		
// 		$this->Session->del('grilla_renglones');
		
// 		if(!empty($this->data)):
		
// 			if($this->Asiento->guardar($this->data, 1)):
// 				$this->Mensaje->okGuardar();
// 			else:
// 				$this->Mensaje->errorGuardar();
// 			endif;
// 		endif;
// 		$ejercicio = $this->Asiento->traeEjercicio($ejercicio_id);
// 		$fechaControl = $ejercicio['fecha_cierre'];
// 		$mkTControl = mktime(0,0,0,date('m',strtotime($fechaControl)),date('d',strtotime($fechaControl)),date('Y',strtotime($fechaControl)));

// 		$ejercicio['fecha_control'] = date('d/m/Y',$mkTControl);
// 		$this->set('ejercicio', $ejercicio);
// 		$this->set('apertura', 1);
// 		$this->render('add');
		
// 	}
	
	
	function asiento_apertura($ejercicio_id) {
            $ejercicio = $this->Asiento->traeEjercicio($ejercicio_id);
            $ejercicio['fecha_cierre_periodo'] = (!empty($ejercicio['fecha_cierre_periodo']) ? $ejercicio['fecha_cierre_periodo'] : $ejercicio['fecha_desde']);
            $asientoFinal = $this->Asiento->getAsientoFinal($ejercicio['id']);

            $asientoApertura = $this->Asiento->find('all', array('conditions' => array('Asiento.co_ejercicio_id' => $ejercicio_id, 'Asiento.tipo' => 1)));
            if(!empty($asientoApertura)){$asientoApertura = $this->Asiento->getAsiento($asientoApertura[0]['Asiento']['id']);}

            
            if(!empty($this->data)):
            endif;

            $this->set('asientoApertura', $asientoApertura);
            $this->set('asientoFinal', $asientoFinal);
            $this->set('fecha_desde', $ejercicio['fecha_desde']);
            $this->set('fecha_hasta', $ejercicio['fecha_hasta']);
            $this->set('ejercicio', $ejercicio);
		
	}
	
	
	function apertura_manual($ejercicio_id=null){
		
            $this->Session->del('grilla_asientos');

            if(!empty($this->data)):

                # 5) ######################################################
                # reconstruyo el campo renglonesSerialize con los datos de la sesión
                # con el uuid para que el modelo ni se entere del cambio
                if(!isset($this->data['Asiento']['renglonesSerialize'])){
                    $renglones = $this->Session->read('grilla_asientos_' . $this->data['Asiento']['uuid']);
                    $this->data['Asiento']['renglonesSerialize'] = base64_encode(serialize($renglones));
                }
                ######################################################

                if($this->Asiento->guardar($this->data, 1)):
                    $this->Mensaje->okGuardar();
                else:
                    $this->Mensaje->errorGuardar();
                endif;
            endif;

            # 1) ######################################################
            $this->set('uuid', $this->Asiento->generarPIN(20));
            //este UUID se guarda como hidden en el formulario del detalle
            #######################################################

            $ejercicio = $this->Asiento->traeEjercicio($ejercicio_id);
            $fechaControl = $ejercicio['fecha_cierre'];
            $mkTControl = mktime(0,0,0,date('m',strtotime($fechaControl)),date('d',strtotime($fechaControl)),date('Y',strtotime($fechaControl)));

            $ejercicio['fecha_control'] = date('d/m/Y',$mkTControl);
            $this->set('ejercicio', $ejercicio);
            $this->set('apertura', 1);
            $this->render('add');
		
	}
	
	
	function cierre_periodo($ejercicio_id) {
            $disable_form = 0;
            $show_asincrono = 0;
//		$fecha_hasta = '';
            $asientoAnulados = array();
            $okProceso = true;

//		$ejercicioIdVigente = $this->Asiento->getEjercicioVigente();
            $ejercicioIdVigente = $ejercicio_id;
            $ejercicio = $this->Asiento->traeEjercicio($ejercicio_id);
            $ejercicio['fecha_cierre_periodo'] = (!empty($ejercicio['fecha_cierre_periodo']) ? $ejercicio['fecha_cierre_periodo'] : $ejercicio['fecha_desde']);


            $fecha_hasta = $ejercicio['fecha_hasta'];

            $asientoUno = $this->Asiento->find('first', array('conditions' => array('Asiento.co_ejercicio_id' => $ejercicio_id, 'Asiento.fecha >' => $ejercicio['fecha_cierre_periodo']), 'order' => array('Asiento.fecha', 'Asiento.nro_asiento')));
            $asientoApertura = $this->Asiento->find('all', array('conditions' => array('Asiento.co_ejercicio_id' => $ejercicio_id, 'Asiento.tipo' => 1)));

            if(!empty($this->data)):
                $disable_form = 1;
                $show_asincrono = 1;
                $fecha_hasta = $this->Asiento->armaFecha($this->data['AsientoCierre']['fecha_hasta']);
                $asientoAnulados = $this->Asiento->find('all', array('conditions' => array('Asiento.co_ejercicio_id' => $ejercicio_id, 'Asiento.borrado' => 1, 'Asiento.fecha <= ' => $fecha_hasta)));
                $aProcesoAsiento = $this->MutualProcesoAsiento->find('all', array('conditions' => array('MutualProcesoAsiento.co_ejercicio_id' => $ejercicio_id, 'MutualProcesoAsiento.cerrado' => 0)));
                if(!empty($aProcesoAsiento)):
                    foreach($aProcesoAsiento as $aProceso):
                        if($aProceso['MutualProcesoAsiento']['fecha_desde'] <= $fecha_hasta && $aProceso['MutualProcesoAsiento']['fecha_hasta'] >= $fecha_hasta):
                            $okProceso = false;
                            break;
                        endif;
                    endforeach;
                endif;
            endif;

            $this->set('show_asincrono', $show_asincrono);
            $this->set('asientoApertura', (!empty($asientoApertura) ? true : false));
            $this->set('fecha_desde', (!empty($asientoUno) ? $asientoUno['Asiento']['fecha'] : $fecha_hasta));
            $this->set('fecha_hasta', $fecha_hasta);
            $this->set('ejercicio', $ejercicio);
            $this->set('lHabilitado', ($ejercicio_id === $ejercicioIdVigente ? true : true));
            $this->set('asientoAnulados', $asientoAnulados);
            $this->set('okProceso', $okProceso);
		
	}	
	
	
	function cierre_ejercicio($ejercicio_id) {
            $disable_form = 0;
            $show_asincrono = 0;
            $asientoAnulados = [];
            $okProceso = true;

//		$ejercicioIdVigente = $this->Asiento->getEjercicioVigente();
            $ejercicioIdVigente = $ejercicio_id;
            $ejercicio = $this->Asiento->traeEjercicio($ejercicio_id);
            $ejercicio['fecha_cierre_periodo'] = (!empty($ejercicio['fecha_cierre_periodo']) ? $ejercicio['fecha_cierre_periodo'] : $ejercicio['fecha_desde']);

            $fecha_hasta = $ejercicio['fecha_hasta'];

            $asientoUno = $this->Asiento->find('first', array('conditions' => array('Asiento.co_ejercicio_id' => $ejercicio_id, 'Asiento.fecha >' => $ejercicio['fecha_cierre_periodo']), 'order' => array('Asiento.fecha', 'Asiento.nro_asiento')));
            $asientoApertura = $this->Asiento->find('all', array('conditions' => array('Asiento.co_ejercicio_id' => $ejercicio_id, 'Asiento.tipo' => 1)));

            if(!empty($this->data)):
                $disable_form = 1;
                $show_asincrono = 1;
                $fecha_hasta = $this->Asiento->armaFecha($this->data['AsientoCierre']['fecha_hasta']);
                $asientoAnulados = $this->Asiento->find('all', array('conditions' => array('Asiento.co_ejercicio_id' => $ejercicio_id, 'Asiento.borrado' => 1, 'Asiento.fecha <= ' => $fecha_hasta)));
                $aProcesoAsiento = $this->MutualProcesoAsiento->find('all', array('conditions' => array('MutualProcesoAsiento.co_ejercicio_id' => $ejercicio_id, 'MutualProcesoAsiento.cerrado' => 0)));
                if(!empty($aProcesoAsiento)):
                    foreach($aProcesoAsiento as $aProceso):
                        if($aProceso['MutualProcesoAsiento']['fecha_desde'] <= $fecha_hasta && $aProceso['MutualProcesoAsiento']['fecha_hasta'] >= $fecha_hasta):
                            $okProceso = false;
                            break;
                        endif;
                    endforeach;
                endif;
            endif;

            $this->set('show_asincrono', $show_asincrono);
            $this->set('asientoApertura', (!empty($asientoApertura) ? true : false));
            $this->set('fecha_desde', $asientoUno['Asiento']['fecha']);
            $this->set('fecha_hasta', $fecha_hasta);
            $this->set('ejercicio', $ejercicio);
            $this->set('lHabilitado', ($ejercicio_id === $ejercicioIdVigente ? true : true));
            $this->set('asientoAnulados', $asientoAnulados);
            $this->set('okProceso', $okProceso);
		
	}
        
        
        function asiento_resultado($ejercicio_id){
            App::import('Model', 'contabilidad.PlanCuenta');
            $oPlanCuenta = new PlanCuenta();
            
            if(!empty($this->data)):
                if($this->Asiento->guardarAsiEspecial($this->data, 3)):
                        $this->Mensaje->okGuardar();
                else:
                        $this->Mensaje->errorGuardar();
                endif;
                $this->redirect('cierre_ejercicio/'. $this->data['Asiento']['co_ejercicio_id']);
            endif;
            
            $ejercicio = $this->Asiento->traeEjercicio($ejercicio_id);

            /*
             * SI EXISTE EL ASIENTO DE CIERRE O FINAL MUESTRA EL ASIENTO DE RESULTADO SIN PODER 
             * HACER NADA
             */
            if($this->Asiento->existeFinal($ejercicio_id)):
                $this->redirect('asiento_resultado_view/'. $ejercicio_id);
            else:
                /*
                 * SI NO EXISTE EL ASIENTO FINA Y SI EXISTE EL ASIENTO DE RESULTADO
                 * ESTE ULTIMO SE PUEDE BORRAR Y VOLVER A GENERAR.
                 */
                if($this->Asiento->existeResultado($ejercicio_id)):
                    $this->redirect('asiento_resultado_borrar/' . $ejercicio_id);
                endif;
            endif;

            /*
             * SI NO EXISTE EL ASIENTO FINAL NI EL DE RESULTADO
             * MUESTRA EL ASIENTO DE RESULTADO PARA GENERARLO.
             */
            $update = "
                UPDATE co_plan_cuentas PlanCuenta
                SET	PlanCuenta.acumulado_debe = 0, PlanCuenta.acumulado_haber = 0
                WHERE PlanCuenta.co_ejercicio_id = '$ejercicio_id'
                ";
            $this->Asiento->query($update);
 
            $update = " 
                UPDATE co_plan_cuentas PlanCuenta
                SET PlanCuenta.acumulado_debe = ((
                SELECT SUM(AsientoRenglon.haber) 
                FROM co_asiento_renglones AsientoRenglon, co_asientos Asiento 
                WHERE AsientoRenglon.co_plan_cuenta_id = PlanCuenta.id AND AsientoRenglon.co_asiento_id = Asiento.id AND Asiento.tipo NOT IN(3,4)) - (
                SELECT SUM(AsientoRenglon.debe) 
                FROM co_asiento_renglones AsientoRenglon, co_asientos Asiento 
                WHERE AsientoRenglon.co_plan_cuenta_id = PlanCuenta.id AND AsientoRenglon.co_asiento_id = Asiento.id AND Asiento.tipo NOT IN(3,4)))
                WHERE PlanCuenta.co_ejercicio_id = '$ejercicio_id'";
            $this->Asiento->query($update);
            
            $sqlUpdate = "
                UPDATE co_plan_cuentas PlanCuenta
                SET	PlanCuenta.acumulado_debe = 0
                WHERE PlanCuenta.co_ejercicio_id = '$ejercicio_id' AND PlanCuenta.acumulado_debe IS NULL
                ";
            $this->Asiento->query($sqlUpdate);
            
            $sqlUpdate = "
                UPDATE co_plan_cuentas
                SET	acumulado_haber = acumulado_debe * (-1), acumulado_debe = 0
                WHERE co_ejercicio_id = '$ejercicio_id' AND acumulado_debe < 0
                ";
            $this->Asiento->query($sqlUpdate);
            
            $sqlDebe ="
                SELECT	PlanCuenta.*, acumulado_debe AS importe
                FROM co_plan_cuentas PlanCuenta
                WHERE co_ejercicio_id = '$ejercicio_id' AND tipo_cuenta IN('RN', 'RP') AND acumulado_debe > 0
                ORDER BY PlanCuenta.cuenta
                ";
            $aDebe = $this->Asiento->query($sqlDebe);
            
            $resultado = $ejercicio['resultado_co_plan_cuenta_id'];
            $sqlResultado = "
                SELECT PlanCuenta.*, ((
                SELECT SUM(acumulado_debe) AS t_haber
                FROM co_plan_cuentas
                WHERE co_ejercicio_id = PlanCuenta.co_ejercicio_id AND tipo_cuenta IN('RN', 'RP') AND acumulado_debe > 0
                ) - (
                SELECT SUM(acumulado_haber)
                FROM co_plan_cuentas
                WHERE co_ejercicio_id = PlanCuenta.co_ejercicio_id AND tipo_cuenta IN('RN', 'RP') AND acumulado_haber > 0
                )) AS importe
                FROM co_plan_cuentas PlanCuenta
                WHERE id = '$resultado' 
                ";
            $aResultado = $this->Asiento->query($sqlResultado);
            
            $sqlHaber ="
                SELECT	PlanCuenta.*, acumulado_haber AS importe
                FROM co_plan_cuentas PlanCuenta
                WHERE co_ejercicio_id = '$ejercicio_id' AND tipo_cuenta IN('RN', 'RP') AND acumulado_haber > 0
                ORDER BY PlanCuenta.cuenta
                ";
            $aHaber = $this->Asiento->query($sqlHaber);

            foreach($aDebe as $key => $value):
                // $oPlanCuenta->formato_cuenta($cuenta['PlanCuenta']['cuenta'], $ejercicio);           
                $aDebe[$key]['PlanCuenta']['codigo_cuenta'] = $oPlanCuenta->formato_cuenta($value['PlanCuenta']['cuenta'], $ejercicio);
            endforeach;
            
            foreach($aResultado as $key => $value):
                // $oPlanCuenta->formato_cuenta($cuenta['PlanCuenta']['cuenta'], $ejercicio);           
                $aResultado[$key]['PlanCuenta']['codigo_cuenta'] = $oPlanCuenta->formato_cuenta($value['PlanCuenta']['cuenta'], $ejercicio);
                $aResultado[$key]['PlanCuenta']['importe'] = $value[0]['importe'];
            endforeach;
            
            foreach($aHaber as $key => $value):
                // $oPlanCuenta->formato_cuenta($cuenta['PlanCuenta']['cuenta'], $ejercicio);           
                $aHaber[$key]['PlanCuenta']['codigo_cuenta'] = $oPlanCuenta->formato_cuenta($value['PlanCuenta']['cuenta'], $ejercicio);
            endforeach;

            $this->set('ejercicio',$ejercicio);
            $this->set('aDebe', $aDebe);
            $this->set('aResultado', $aResultado);
            $this->set('aHaber', $aHaber);
        }
        
        
        function asiento_resultado_borrar($ejercicio_id){
            App::import('Model', 'contabilidad.PlanCuenta');
            $oPlanCuenta = new PlanCuenta();
            
            if(!empty($this->data)):
//                debug($this->data);
//                exit;
                
                if($this->Asiento->borrarAsiento($this->data['Asiento']['id'])):
                        $this->Mensaje->okGuardar();
                else:
                        $this->Mensaje->errorGuardar();
                endif;
                $this->redirect('cierre_ejercicio/'. $this->data['Asiento']['co_ejercicio_id']);
            endif;

            $asientoResultado = $this->Asiento->getAsientoResultado($ejercicio_id);
            
            $this->set('asiento', $asientoResultado);
        }
        
        
        function asiento_resultado_view($ejercicio_id){
            App::import('Model', 'contabilidad.PlanCuenta');
            $oPlanCuenta = new PlanCuenta();
            
            $asientoResultado = $this->Asiento->getAsientoResultado($ejercicio_id);
            
            $this->set('asiento', $asientoResultado);
        }
        
        
        function asiento_final($ejercicio_id){
            App::import('Model', 'contabilidad.PlanCuenta');
            $oPlanCuenta = new PlanCuenta();
            
            if(!empty($this->data)):
                
                if($this->Asiento->guardarAsiEspecial($this->data, 4)):
                        $this->Mensaje->okGuardar();
                else:
                        $this->Mensaje->errorGuardar();
                endif;
                $this->redirect('cierre_ejercicio/'. $this->data['Asiento']['co_ejercicio_id']);
            endif;
            

            $ejercicio = $this->Asiento->traeEjercicio($ejercicio_id);
            $nAsientoResultado = 1;
            /*
             * SI EXISTE EL ASIENTO DE CIERRE O FINAL MUESTRA EL ASIENTO FINAL PARA 
             * BORRAR SIEMPRE Y CUANDO NO EXISTA EL ASIENTO DE APERTURA DEL 
             * EJERCICIO POSTERIOR.
             */
            if($this->Asiento->existeFinal($ejercicio_id)){
                if($this->Asiento->existeInicialPost($ejercicio_id)){
                    $this->redirect('asiento_final_view/' . $ejercicio_id);
                }
                else{
                    $this->redirect('asiento_final_borrar/'. $ejercicio_id);
                }
            }
            else{
                /*
                 * SI NO EXISTE EL ASIENTO FINAL Y SI EXISTE EL ASIENTO DE RESULTADO
                 * ENTONCES GENERAMOS EL ASIENTO FINAL.
                 */
                if(!$this->Asiento->existeResultado($ejercicio_id)){
                    $nAsientoResultado = 0;
                }
            }

            /*
             * SI NO EXISTE EL ASIENTO FINAL NI EL DE RESULTADO
             * MUESTRA EL ASIENTO DE RESULTADO PARA GENERARLO.
             */
            $update = "
                    UPDATE co_plan_cuentas PlanCuenta
                    SET	PlanCuenta.acumulado_debe = 0, PlanCuenta.acumulado_haber = 0
                    WHERE PlanCuenta.co_ejercicio_id = '$ejercicio_id'
                    ";
            $this->Asiento->query($update);
 
            $update = " 
                    UPDATE co_plan_cuentas PlanCuenta
                    SET	PlanCuenta.acumulado_debe = ((
                        SELECT SUM(AsientoRenglon.haber) 
                        FROM co_asiento_renglones AsientoRenglon, co_asientos Asiento 
                        WHERE AsientoRenglon.co_plan_cuenta_id = PlanCuenta.id AND AsientoRenglon.co_asiento_id = Asiento.id) - (
                        SELECT SUM(AsientoRenglon.debe) 
                        FROM co_asiento_renglones AsientoRenglon, co_asientos Asiento 
                        WHERE AsientoRenglon.co_plan_cuenta_id = PlanCuenta.id AND AsientoRenglon.co_asiento_id = Asiento.id))
                    WHERE PlanCuenta.co_ejercicio_id = '$ejercicio_id'";
            $this->Asiento->query($update);
            
            $sqlUpdate = "
                    UPDATE co_plan_cuentas PlanCuenta
                    SET	PlanCuenta.acumulado_debe = 0
                    WHERE PlanCuenta.co_ejercicio_id = '$ejercicio_id' AND PlanCuenta.acumulado_debe IS NULL
                    ";
            $this->Asiento->query($sqlUpdate);
            
            $sqlUpdate = "
                    UPDATE co_plan_cuentas
                    SET	acumulado_haber = acumulado_debe * (-1), acumulado_debe = 0
                    WHERE co_ejercicio_id = '$ejercicio_id' AND acumulado_debe < 0
                    ";
            $this->Asiento->query($sqlUpdate);
            
            $sqlDebe ="
                SELECT	PlanCuenta.*, acumulado_debe AS importe
                FROM co_plan_cuentas PlanCuenta
                WHERE co_ejercicio_id = '$ejercicio_id' AND acumulado_debe > 0
                ORDER BY PlanCuenta.cuenta
                ";
            $aDebe = $this->Asiento->query($sqlDebe);
            
            $sqlHaber ="
                SELECT	PlanCuenta.*, acumulado_haber AS importe
                FROM co_plan_cuentas PlanCuenta
                WHERE co_ejercicio_id = '$ejercicio_id' AND acumulado_haber > 0
                ORDER BY PlanCuenta.cuenta
                ";
            $aHaber = $this->Asiento->query($sqlHaber);

            foreach($aDebe as $key => $value):
                // $oPlanCuenta->formato_cuenta($cuenta['PlanCuenta']['cuenta'], $ejercicio);           
                $aDebe[$key]['PlanCuenta']['codigo_cuenta'] = $oPlanCuenta->formato_cuenta($value['PlanCuenta']['cuenta'], $ejercicio);
            endforeach;
            
            foreach($aHaber as $key => $value):
                // $oPlanCuenta->formato_cuenta($cuenta['PlanCuenta']['cuenta'], $ejercicio);           
                $aHaber[$key]['PlanCuenta']['codigo_cuenta'] = $oPlanCuenta->formato_cuenta($value['PlanCuenta']['cuenta'], $ejercicio);
            endforeach;

            $this->set('ejercicio',$ejercicio);
            $this->set('aDebe', $aDebe);
            $this->set('aHaber', $aHaber);
            $this->set('nAsientoResultado', $nAsientoResultado);
        }
        
        
        function asiento_final_view($ejercicio_id){
            App::import('Model', 'contabilidad.PlanCuenta');
            $oPlanCuenta = new PlanCuenta();
            
            $asientoFinal = $this->Asiento->getAsientoFinal($ejercicio_id);
            
            $this->set('asiento', $asientoFinal);
            
        }
        
        
        function asiento_final_borrar($ejercicio_id){
            App::import('Model', 'contabilidad.PlanCuenta');
            $oPlanCuenta = new PlanCuenta();
            
            if(!empty($this->data)):
//                debug($this->data);
//                exit;
                
                if($this->Asiento->borrarAsiento($this->data['Asiento']['id'])):
                        $this->Mensaje->okGuardar();
                else:
                        $this->Mensaje->errorGuardar();
                endif;
                $this->redirect('cierre_ejercicio/'. $this->data['Asiento']['co_ejercicio_id']);
            endif;

            $asientoFinal = $this->Asiento->getAsientoFinal($ejercicio_id);
            
            $this->set('asiento', $asientoFinal);
            
        }
        
        
        function apertura_automatico($ejercicio_id){
            $ejAnterior = $this->Asiento->traeEjercicioAnt($ejercicio_id);
            $asientoFinal = $this->Asiento->getAsientoApertura($ejAnterior['id']);
            $ejercicio = $this->Asiento->traeEjercicio($ejercicio_id);

            if(!empty($this->data)):
            
                if($this->Asiento->guardarAsiEspecial($this->data, 1)):
                        $this->Mensaje->okGuardar();
                else:
                        $this->Mensaje->errorGuardar();
                endif;
                $this->redirect('asiento_apertura/'. $this->data['Asiento']['co_ejercicio_id']);
            endif;

            $this->set('asientoApertura', $asientoFinal);
//            $this->set('asientoFinal', $asientoFinal);
            $this->set('ejercicio_id', $ejercicio_id);
            $this->set('ejercicio', $ejercicio);
            
        }
}
?>