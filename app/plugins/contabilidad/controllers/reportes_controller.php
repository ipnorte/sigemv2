<?php
class ReportesController extends ContabilidadAppController{
	
	var $name = 'Reportes';
	var $uses = array('contabilidad.Asiento', 'contabilidad.PlanCuenta', 'contabilidad.MutualProcesoAsiento', 'contabilidad.MutualAsiento', 'contabilidad.Ejercicio', 'contabilidad.AsientoRenglon', 'Shells.Asincrono', 'Shells.AsincronoTemporal');
	
	var $autorizar = array( 'libro_diario', 'libro_mayor_general', 'libro_suma_saldos', 'balance_general', 'balance_sumas_saldos_pdf', 'balance_sumas_saldos_xls',
                                'libro_mayor_borrador_pdf', 'libro_mayor_borrador_xls', 'libro_diario_borrador_xls', 'libro_diario_xls', 'libro_diario_pdf',
                                'libro_diario_borrador_pdf', 'balance_sumas_saldos_borrador_pdf', 'balance_sumas_saldos_borrador_xls', 'libro_diario_agrupado_pdf', 'libro_diario_agrupado_xls',
                                'libro_mayor_general_borrador_xls', 'libro_mayor_general_borrador_pdf', 'libro_mayor_general_pdf', 'libro_mayor_general_xls', 'agrupar_asientos', 'libro_subdiario', 'libro_subdiario_pdf'
	);
	
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}		
	
	
	
	function index(){

		$ejercicio = array();
		if(!empty($this->data)):
			$ejercicio = $this->Ejercicio->traeEjercicio($this->data['ejercicio']['id']);
			$this->redirect('libro_diario/' . $this->data['ejercicio']['id']);
		endif;
		
		$this->set('ejercicio', $ejercicio);
	}
	
	
	
	function agrupar_asientos($ejercicio_id=NULL){

		$ejercicio = $this->Ejercicio->traeEjercicio($ejercicio_id);
                $show_asincrono = 0;
                $disableForm = 0;
                $showTabla = 0;
                $asincrono_id = 0;

		if(!empty($this->data)):
                    $show_asincrono = 1;
                    $disableForm = 1;
		endif;
		
		if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])):

			$aAsincrono = $this->Asincrono->find('all', array('conditions' => array('Asincrono.id' => $this->params['url']['pid'])));
//			$aTemporal = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']), 'order' => array('AsincronoTemporal.entero_2', 'AsincronoTemporal.texto_9', 'AsincronoTemporal.entero_1')));
			$showAsincrono = 0;
			$showTabla = 1;
			$disableForm = 1;
                        $asincrono_id = $this->params['url']['pid'];
                        $ejercicio_id = $aAsincrono[0]['Asincrono']['p1'];
			
		endif;
                
		$ejercicio = $this->Ejercicio->traeEjercicio($ejercicio_id);
                
                $this->set('ejercicio', $ejercicio);
		$this->set('ejercicio_id', $ejercicio_id);
                $this->set('show_asincrono', $show_asincrono);
                $this->set('disableForm', $disableForm);
//		$this->set('aTemporal', $aTemporal);
		$this->set('showTabla', $showTabla);
		$this->set('asincrono_id', $asincrono_id);
		

						
	}


	function libro_diario_agrupado_pdf($ejercicio_id, $pid=NULL, $encabezado=1){
            
                if($ejercicio_id === NULL) $this->redirect('index');
	
		$aAsincrono = $this->Asincrono->find('all', array('conditions' => array('Asincrono.id' => $pid)));
		$aTemporal = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $pid)));

                $ejercicio = $this->Ejercicio->traeEjercicio($aAsincrono[0]['Asincrono']['p1']);
		
		$this->set('ejercicio', $ejercicio);
		$this->set('ejercicio_id', $ejercicio_id);
                $this->set('aAsincrono', $aAsincrono);
                $this->set('aTemporal', $aTemporal);
                $this->set('encabezado', $encabezado);

    //            debug($ejercicio);
    //           debug($aAsincrono);
    //            debug($aTemporal);
    	
		$this->render('libro_diario_agrupado_pdf', 'pdf');
	
	
			
	}
	

	function libro_diario_agrupado_xls($ejercicio_id, $pid=NULL){
	
            
                if($ejercicio_id === NULL) $this->redirect('index');
	
		$aAsincrono = $this->Asincrono->find('all', array('conditions' => array('Asincrono.id' => $pid)));
		$aTemporal = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $pid)));

                $ejercicio = $this->Ejercicio->traeEjercicio($aAsincrono[0]['Asincrono']['p1']);
		
		$this->set('ejercicio', $ejercicio);
		$this->set('ejercicio_id', $ejercicio_id);
                $this->set('aAsincrono', $aAsincrono);
                $this->set('aTemporal', $aTemporal);

	
		$this->render('libro_diario_agrupado_xls', 'blank');

                return true;
	
			
	}
	
	
	
	function libro_diario($ejercicio_id){

		$condiciones = null;		
			
		$search = null;
			
		if(!empty($this->data)){

			$this->Session->del($this->name.'.search');
			$search = $this->data;
				
		}else if($this->Session->check($this->name.'.search')){
				
			$search = $this->Session->read($this->name.'.search');
			$this->data = $search;
				 
		}
			
		$ejercicio = $this->Ejercicio->traeEjercicio($ejercicio_id);
		$fecha_desde = $ejercicio['fecha_desde'];
		$fecha_hasta = $ejercicio['fecha_hasta'];
		$libroDiario = array();
		
		if(!empty($this->data)):
                
			$fecha_desde = $this->Asiento->armaFecha($this->data['Reporte']['fecha_desde']);
			$fecha_hasta = $this->Asiento->armaFecha($this->data['Reporte']['fecha_hasta']);
			
			$condiciones = array('Asiento.co_ejercicio_id' => $ejercicio_id, 'Asiento.fecha >=' => $fecha_desde, 'Asiento.fecha <=' => $fecha_hasta, 'Asiento.borrado' => 0);

			$this->paginate = array('limit' => 30,
			'conditions' => $condiciones,
			'order' => array('Asiento.fecha', 'Asiento.nro_asiento'));
				
			$libroDiario = $this->paginate('Asiento');

			$this->Session->write($this->name.'.search', $search);
				
				
		endif;
		
		$this->set('ejercicio', $ejercicio);
		$this->set('fecha_desde', $fecha_desde);
		$this->set('fecha_hasta', $fecha_hasta);
		$this->set('libroDiario', $libroDiario);
						
	}


	function libro_diario_pdf($ejercicio_id, $fecha_desde, $fecha_hasta){
	
		$ejercicio = $this->Ejercicio->traeEjercicio($ejercicio_id);
		
		$this->set('ejercicio', $ejercicio);
		$this->set('ejercicio_id', $ejercicio_id);
		$this->set('fecha_desde', $fecha_desde);
		$this->set('fecha_hasta', $fecha_hasta);
			
	
		$this->render('libro_diario_pdf', 'pdf');
	
	
			
	}
	

	function libro_diario_xls($ejercicio_id, $fecha_desde, $fecha_hasta){
	
		$ejercicio = $this->Ejercicio->traeEjercicio($ejercicio_id);
		
		$this->set('ejercicio', $ejercicio);
		$this->set('ejercicio_id', $ejercicio_id);
		$this->set('fecha_desde', $fecha_desde);
		$this->set('fecha_hasta', $fecha_hasta);
	
	
		$this->render('libro_diario_xls', 'blank');
		return true;
	
			
	}
	
	
	function libro_mayor_general($ejercicio_id){

		$ejercicio = $this->Ejercicio->traeEjercicio($ejercicio_id);
		$fecha_desde = $ejercicio['fecha_desde'];
		$fecha_hasta = $ejercicio['fecha_hasta'];
		$libroMayorGeneral = array();

		if(!empty($this->data)):
		
			$fecha_desde = $this->Asiento->armaFecha($this->data['Reporte']['fecha_desde']);
			$fecha_hasta = $this->Asiento->armaFecha($this->data['Reporte']['fecha_hasta']);
			
			$libroMayorGeneral = $this->PlanCuenta->getMayorDetalle($ejercicio_id, $fecha_desde, $fecha_hasta);
		endif;
		
		$this->set('ejercicio', $ejercicio);
		$this->set('fecha_desde', $fecha_desde);
		$this->set('fecha_hasta', $fecha_hasta);
		$this->set('libroMayorGeneral', $libroMayorGeneral);
		
// 		exit;
	}


	function libro_mayor_general_pdf($ejercicio_id, $fecha_desde, $fecha_hasta, $cuenta=NULL){
	
		$ejercicio = $this->Ejercicio->traeEjercicio($ejercicio_id);
		$fecha_desde = $ejercicio['fecha_desde'];
		$fecha_hasta = $ejercicio['fecha_hasta'];

		$libroMayorGeneral = $this->PlanCuenta->getMayorDetalle($ejercicio_id, $fecha_desde, $fecha_hasta, $cuenta);
			
		$this->set('ejercicio', $ejercicio);
		$this->set('fecha_desde', $fecha_desde);
		$this->set('fecha_hasta', $fecha_hasta);
		$this->set('libroMayorGeneral', $libroMayorGeneral);
	
		$this->render('libro_mayor_general_pdf', 'pdf');
	}


	function libro_mayor_general_xls($ejercicio_id, $fecha_desde, $fecha_hasta, $cuenta=NULL){
	
		$ejercicio = $this->Ejercicio->traeEjercicio($ejercicio_id);
		$fecha_desde = $ejercicio['fecha_desde'];
		$fecha_hasta = $ejercicio['fecha_hasta'];
	
		$libroMayorGeneral = $this->PlanCuenta->getMayorDetalle($ejercicio_id, $fecha_desde, $fecha_hasta, $cuenta);
			
		$this->set('ejercicio', $ejercicio);
		$this->set('fecha_desde', $fecha_desde);
		$this->set('fecha_hasta', $fecha_hasta);
		$this->set('libroMayorGeneral', $libroMayorGeneral);
	
		$this->render('libro_mayor_general_xls', 'blank');
		return true;
	}
	
	
	function libro_suma_saldos($ejercicio_id){

		$ejercicio = $this->Ejercicio->traeEjercicio($ejercicio_id);
		$fecha_desde = $ejercicio['fecha_desde'];
		$fecha_hasta = $ejercicio['fecha_hasta'];
		$libroSumasSaldos = array();
		
		if(!empty($this->data)):
		
			$fecha_desde = $this->Asiento->armaFecha($this->data['Reporte']['fecha_desde']);
			$fecha_hasta = $this->Asiento->armaFecha($this->data['Reporte']['fecha_hasta']);
				
			$libroSumasSaldos = $this->PlanCuenta->getMayoriza($ejercicio_id, $fecha_desde, $fecha_hasta);
		endif;
		
		$this->set('ejercicio', $ejercicio);
		$this->set('ejercicio_id', $ejercicio_id);
		$this->set('fecha_desde', $fecha_desde);
		$this->set('fecha_hasta', $fecha_hasta);
		$this->set('libroSumasSaldos', $libroSumasSaldos);
		
	}


	function balance_sumas_saldos_pdf($ejercicio_id, $fecha_desde, $fecha_hasta){
	
		$ejercicio = $this->Ejercicio->traeEjercicio($ejercicio_id);
	
		$libroSumasSaldos = $this->PlanCuenta->getMayoriza($ejercicio_id, $fecha_desde, $fecha_hasta);
	
		$this->set('ejercicio', $ejercicio);
		$this->set('fecha_desde', $fecha_desde);
		$this->set('fecha_hasta', $fecha_hasta);
		$this->set('libroSumasSaldos', $libroSumasSaldos);
	
		$this->render('balance_sumas_saldos_pdf', 'pdf');
	}


	function balance_sumas_saldos_xls($ejercicio_id, $fecha_desde, $fecha_hasta){
	
		$ejercicio = $this->Ejercicio->traeEjercicio($ejercicio_id);
	
		$libroSumasSaldos = $this->PlanCuenta->getMayoriza($ejercicio_id, $fecha_desde, $fecha_hasta);
	
		$this->set('ejercicio', $ejercicio);
		$this->set('fecha_desde', $fecha_desde);
		$this->set('fecha_hasta', $fecha_hasta);
		$this->set('libroSumasSaldos', $libroSumasSaldos);
	
		$this->render('balance_sumas_saldos_xls', 'blank');
	}
	
	
	function libro_diario_borrador_pdf($procesoId){
		
		$proceso = $this->MutualProcesoAsiento->read(null, $procesoId);
		
		$this->set('procesoId', $procesoId);
		$this->set('fecha_desde', $proceso['MutualProcesoAsiento']['fecha_desde']);
		$this->set('fecha_hasta', $proceso['MutualProcesoAsiento']['fecha_hasta']);
		
		
		$this->render('libro_diario_borrador_pdf', 'pdf');
		
		
			
	}
	

	function libro_diario_borrador_xls($procesoId){
		
		$proceso = $this->MutualProcesoAsiento->read(null, $procesoId);
		
		$this->set('procesoId', $procesoId);
		$this->set('fecha_desde', $proceso['MutualProcesoAsiento']['fecha_desde']);
		$this->set('fecha_hasta', $proceso['MutualProcesoAsiento']['fecha_hasta']);
		
		
		$this->render('libro_diario_borrador_xls', 'blank');
		return true;
		
			
	}
	
	
	function libro_mayor_borrador_pdf($procesoId, $cuentaId = null, $consolidado){
	
		$proceso = $this->MutualProcesoAsiento->read(null, $procesoId);
	
		$aMayorDetalle = $this->MutualProcesoAsiento->getMayorDetalle($procesoId, $cuentaId, $consolidado);
	
		$fechaAsiento = $this->MutualAsiento->find('first', array('conditions' => array('MutualAsiento.mutual_proceso_asiento_id' => $procesoId), 'order' => array('MutualAsiento.fecha')));
		
		$this->set('aMayorDetalle', $aMayorDetalle);
		$this->set('fecha_desde', $fechaAsiento['MutualAsiento']['fecha']);
		$this->set('fecha_hasta', $proceso['MutualProcesoAsiento']['fecha_hasta']);
	
	
		$this->render('libro_mayor_borrador_pdf', 'pdf');
	
	
			
	}
	
	
	function libro_mayor_borrador_xls($procesoId, $cuentaId = null, $consolidado = 0){
	
		$proceso = $this->MutualProcesoAsiento->read(null, $procesoId);
	
		$aMayorDetalle = $this->MutualProcesoAsiento->getMayorDetalle($procesoId, $cuentaId, $consolidado);
	
		$fechaAsiento = $this->MutualAsiento->find('first', array('conditions' => array('MutualAsiento.mutual_proceso_asiento_id' => $procesoId), 'order' => array('MutualAsiento.fecha')));
		
		$this->set('cuentaMayor', $aMayorDetalle);
		$this->set('fecha_desde', $fechaAsiento['MutualAsiento']['fecha']);
		$this->set('fecha_hasta', $proceso['MutualProcesoAsiento']['fecha_hasta']);
	
	
		$this->render('libro_mayor_borrador_xls', 'blank');
		return true;
	
			
	}
	
	
	function balance_sumas_saldos_borrador_pdf($procesoId, $consolidado=0){
		$aMutualProcesoAsiento = $this->MutualProcesoAsiento->read(null, $procesoId);
		$mayor = $this->MutualProcesoAsiento->getMayoriza($procesoId, $consolidado);

		$fechaAsiento = $this->MutualAsiento->find('first', array('conditions' => array('MutualAsiento.mutual_proceso_asiento_id' => $procesoId), 'order' => array('MutualAsiento.fecha')));
		$fecha_desde = $fechaAsiento['MutualAsiento']['fecha'];
		
		if($consolidado == 1):
			$fechaAsiento = $this->Asiento->find('first', array('conditions' => array('Asiento.co_ejercicio_id' => $aMutualProcesoAsiento['MutualProcesoAsiento']['co_ejercicio_id'], 'Asiento.fecha >' => $aMutualProcesoAsiento['MutualProcesoAsiento']['fecha_desde']), 'order' => array('Asiento.fecha')));
			if($fechaAsiento['Asiento']['fecha'] < $fecha_desde) $fecha_desde = $fechaAsiento['Asiento']['fecha'];
		endif;
		
		$this->set('procesoId', $procesoId);
		$this->set('aMutualProcesoAsiento', $aMutualProcesoAsiento);
		$this->set('aMayor', $mayor);
		$this->set('fecha_desde', $fecha_desde);
		$this->set('fecha_hasta', $aMutualProcesoAsiento['MutualProcesoAsiento']['fecha_hasta']);

		$this->render('balance_sumas_saldos_borrador_pdf', 'pdf');
		return true;
		
	}


	function balance_sumas_saldos_borrador_xls($procesoId, $consolidado=0){
		$aMutualProcesoAsiento = $this->MutualProcesoAsiento->read(null, $procesoId);
		$mayor = $this->MutualProcesoAsiento->getMayoriza($procesoId, $consolidado);

		$fechaAsiento = $this->MutualAsiento->find('first', array('conditions' => array('MutualAsiento.mutual_proceso_asiento_id' => $procesoId), 'order' => array('MutualAsiento.fecha')));
		$fecha_desde = $fechaAsiento['MutualAsiento']['fecha'];
		
		if($consolidado == 1):
			$fechaAsiento = $this->Asiento->find('first', array('conditions' => array('Asiento.co_ejercicio_id' => $aMutualProcesoAsiento['MutualProcesoAsiento']['co_ejercicio_id'], 'Asiento.fecha >' => $aMutualProcesoAsiento['MutualProcesoAsiento']['fecha_desde']), 'order' => array('Asiento.fecha')));
			if($fechaAsiento['Asiento']['fecha'] < $fecha_desde) $fecha_desde = $fechaAsiento['Asiento']['fecha'];
		endif;
		
		$this->set('procesoId', $procesoId);
		$this->set('aMutualProcesoAsiento', $aMutualProcesoAsiento);
		$this->set('aMayor', $mayor);
		$this->set('fecha_desde', $fecha_desde);
	
		$this->render('balance_sumas_saldos_borrador_xls', 'blank');
		return true;
			
	
	}


	function libro_mayor_general_borrador_pdf($procesoId, $cuentaId = null){
	
		$proceso = $this->MutualProcesoAsiento->read(null, $procesoId);
	
		$aMayorDetalle = $this->MutualProcesoAsiento->getMayorDetalle($procesoId, $cuentaId);
	
		$fechaAsiento = $this->MutualAsiento->find('first', array('conditions' => array('MutualAsiento.mutual_proceso_asiento_id' => $procesoId), 'order' => array('MutualAsiento.fecha')));
	
		$this->set('aMayorDetalle', $aMayorDetalle);
		$this->set('fecha_desde', $fechaAsiento['MutualAsiento']['fecha']);
		$this->set('fecha_hasta', $proceso['MutualProcesoAsiento']['fecha_hasta']);
	
	
		$this->render('libro_mayor_general_borrador_pdf', 'pdf');
	
	
			
	}
	
	
	function libro_mayor_general_borrador_xls($procesoId, $cuentaId = null){
	
		$proceso = $this->MutualProcesoAsiento->read(null, $procesoId);
	
		$aMayorDetalle = $this->MutualProcesoAsiento->getMayorDetalle($procesoId, $cuentaId);
	
		$fechaAsiento = $this->MutualAsiento->find('first', array('conditions' => array('MutualAsiento.mutual_proceso_asiento_id' => $procesoId), 'order' => array('MutualAsiento.fecha')));
	
		$this->set('cuentaMayor', $aMayorDetalle);
		$this->set('fecha_desde', $fechaAsiento['MutualAsiento']['fecha']);
		$this->set('fecha_hasta', $proceso['MutualProcesoAsiento']['fecha_hasta']);
	
	
		$this->render('libro_mayor_general_borrador_xls', 'blank');
		return true;
	
			
	}
	
	
	function balance_general($ejercicio_id){

		$ejercicio = $this->Ejercicio->traeEjercicio($ejercicio_id);
		$fecha_desde = $ejercicio['fecha_desde'];
		$fecha_hasta = $ejercicio['fecha_hasta'];
		$aBalanceGeneral = array();
		$nivel = 0;
		$aNivel = array();
		
		if(!empty($this->data)):
				
			$fecha_desde = $this->PlanCuenta->armaFecha($this->data['Balance']['fecha_desde']);
			$fecha_hasta = $this->PlanCuenta->armaFecha($this->data['Balance']['fecha_hasta']);
			$nivel = $this->data['Balance']['nivel'];
				
			$aBalanceGeneral = $this->PlanCuenta->getBalanceGeneral($ejercicio_id, $fecha_desde, $fecha_hasta, $nivel);
		endif;
		
		for($i = 1; $i <= $ejercicio['nivel']; $i++):
			$aNivel[$i] = 'NIVEL ' . $i;
		endfor;
		
		$this->set('ejercicio', $ejercicio);
		$this->set('ejercicio_id', $ejercicio_id);
		$this->set('fecha_desde', $fecha_desde);
		$this->set('fecha_hasta', $fecha_hasta);
		$this->set('aBalanceGeneral', $aBalanceGeneral);
		$this->set('nivel', $nivel);		
		$this->set('aNivel', $aNivel);
// debug($aBalanceGeneral);
// exit;		
	}
	
	
	
	function libro_subdiario($ejercicio_id=NULL){

		$ejercicio = $this->Ejercicio->traeEjercicio($ejercicio_id);
                $show_asincrono = 0;
                $disableForm = 0;
                $showTabla = 0;
                $asincrono_id = 0;

		if(!empty($this->data)):
                    $show_asincrono = 0;
                    $disableForm = 0;
		endif;
		
		if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])):

			$aAsincrono = $this->Asincrono->find('all', array('conditions' => array('Asincrono.id' => $this->params['url']['pid'])));
//			$aTemporal = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']), 'order' => array('AsincronoTemporal.entero_2', 'AsincronoTemporal.texto_9', 'AsincronoTemporal.entero_1')));
			$showAsincrono = 0;
			$showTabla = 0;
			$disableForm = 0;
                        $asincrono_id = $this->params['url']['pid'];
                        $ejercicio_id = $aAsincrono[0]['Asincrono']['p1'];
			
		endif;
                
		$ejercicio = $this->Ejercicio->traeEjercicio($ejercicio_id);
                
                $this->set('ejercicio', $ejercicio);
		$this->set('ejercicio_id', $ejercicio_id);
                $this->set('show_asincrono', $show_asincrono);
                $this->set('disableForm', $disableForm);
//		$this->set('aTemporal', $aTemporal);
		$this->set('showTabla', $showTabla);
		$this->set('asincrono_id', $asincrono_id);
		

						
	}


	function libro_subdiario_pdf($ejercicio_id, $pid=NULL, $encabezado=1){
            
                if($ejercicio_id === NULL) $this->redirect('index');
	
//		$aAsincrono = $this->Asincrono->find('all', array('conditions' => array('Asincrono.id' => $pid)));
//		$aTemporal = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $pid)));

                $ejercicio = $this->Ejercicio->traeEjercicio($ejercicio_id);
                $CuentaId = $ejercicio['co_plan_cuenta_id'];

                $sqlFechas = "SELECT Asiento.* FROM co_asientos Asiento
                                WHERE Asiento.co_ejercicio_id = '$ejercicio_id' AND Asiento.tipo = 2 AND Asiento.id IN(
                                SELECT co_asiento_id FROM co_asiento_renglones WHERE co_plan_cuenta_id = '$CuentaId')
                                GROUP BY Asiento.fecha
                                ORDER BY fecha";
                
//                $cajaDia = $this->Asiento->query($sqlFechas);
// debug($cajaDia);
// exit;
                
		$this->set('ejercicio', $ejercicio);
		$this->set('ejercicio_id', $ejercicio_id);
//                $this->set('aAsincrono', $aAsincrono);
//                $this->set('aTemporal', $aTemporal);
                $this->set('encabezado', $encabezado);
//                $this->set('cajaDia', $cajaDia);

    //            debug($ejercicio);
    //           debug($aAsincrono);
    //            debug($aTemporal);
    	
		$this->render('libro_subdiario_pdf', 'pdf');
	
	
			
	}
	

	function libro_subdiario_xls($ejercicio_id, $pid=NULL){
	
            
                if($ejercicio_id === NULL) $this->redirect('index');
	
		$aAsincrono = $this->Asincrono->find('all', array('conditions' => array('Asincrono.id' => $pid)));
		$aTemporal = $this->AsincronoTemporal->find('all', array('conditions' => array('AsincronoTemporal.asincrono_id' => $pid)));

                $ejercicio = $this->Ejercicio->traeEjercicio($aAsincrono[0]['Asincrono']['p1']);
		
		$this->set('ejercicio', $ejercicio);
		$this->set('ejercicio_id', $ejercicio_id);
                $this->set('aAsincrono', $aAsincrono);
                $this->set('aTemporal', $aTemporal);

	
		$this->render('libro_subdiario_xls', 'blank');

                return true;
	
			
	}
}
?>