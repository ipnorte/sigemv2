<?php
class MutualProcesoAsientosController extends ContabilidadAppController{

	var $name = 'MutualProcesoAsientos';
	var $uses = array('Contabilidad.MutualProcesoAsiento','Shells.Asincrono', 'contabilidad.MutualAsiento', 
						'contabilidad.MutualAsientoRenglon', 'Contabilidad.Asiento');

	var $autorizar = array('procesar_asientos', 'view', 'view_mayor_borrador', 'aprobar_asientos', 'view_libro_diario_borrador', 
							'view_balance_borrador', 'view_mayor_general_borrador'
	);

	function beforeFilter(){
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();
	}

	function index($id=NULL){
		$procesoAbierto = $this->MutualProcesoAsiento->getUltimoAbierto();
		$nuevoProceso = 0;
		if(empty($procesoAbierto)) $nuevoProceso = 1;

		$this->paginate = array(
				'limit' => 50,
				'order' => array('MutualProcesoAsiento.id' => 'DESC')
		);
			
		$this->set('nuevoProceso', $nuevoProceso);
		$this->set('aMutualProcesoAsiento', $this->paginate());
		$this->render('index');

	}

	function view($asiento_id=0){
		$asiento = $this->MutualProcesoAsiento->getAsiento($asiento_id);
		$this->set('asientos', $asiento);

	}

	function add($ejercicio_id=null){
	}

	function delete($asiento_id=null){

	}


	function edit($asiento_id=null){
	}


	function procesar_asientos(){
		$disable_form = 0;
		$show_asincrono = 0;
		$procesoId = 0;
		$showTabla = 0;

		$this->Ejercicio = $this->MutualProcesoAsiento->importarModelo('Ejercicio', 'contabilidad');

		$procesoAbierto = $this->MutualProcesoAsiento->getUltimoAbierto();

		if(empty($procesoAbierto)):
			$ejercicioVigente = $this->MutualProcesoAsiento->getGlobalDato('entero_1', 'CONTEVIG');
			$ejercicioVigenteId = $ejercicioVigente['GlobalDato']['entero_1'];
			$fecha_nuevo_proceso = date("Y-m-d");
		else:
			$procesoId = $procesoAbierto['MutualProcesoAsiento']['id'];
			$ejercicioVigenteId = $procesoAbierto['MutualProcesoAsiento']['co_ejercicio_id'];
			$fecha_nuevo_proceso = $procesoAbierto['MutualProcesoAsiento']['fecha_hasta'];
		endif;

		$aEjercicio = $this->Ejercicio->read(null, $ejercicioVigenteId);
		$aEjercicio['Ejercicio']['fecha_proceso'] = (empty($aEjercicio['Ejercicio']['fecha_proceso']) ? $aEjercicio['Ejercicio']['fecha_cierre'] : $aEjercicio['Ejercicio']['fecha_proceso']);

		if(!empty($this->data)):

			if(isset($this->data['ProcesarAsiento']['cerrar']) && $this->data['ProcesarAsiento']['cerrar'] == 1):
				$aAsincrono = $this->Asincrono->read(null, $this->data['ProcesarAsiento']['asincrono_id']);
				//	    		$this->MutualProcesoAsiento->cerrar_proceso_asientos($aAsincrono['Asincrono']['p1']);
			else:
				$mutual_proceso_asiento = array('MutualProcesoAsiento' =>
						array('id' => $this->data['ProcesarAsiento']['id'],
								'co_ejercicio_id' => $aEjercicio['Ejercicio']['id'],
								'fecha_desde' => $aEjercicio['Ejercicio']['fecha_proceso'],
								'fecha_hasta' => $this->data['ProcesarAsiento']['fecha_proceso'],
								'agrupar' => $this->data['ProcesarAsiento']['agrupar']));
				if($this->MutualProcesoAsiento->save($mutual_proceso_asiento)):
					//		    		$this->MutualProcesoAsiento->ProcesoAsientoAsincrono($this->MutualProcesoAsiento->id);
					$procesoId = $this->MutualProcesoAsiento->id;
					$disable_form = 1;
					$show_asincrono = 1;
					$fecha_nuevo_proceso = $this->MutualProcesoAsiento->armaFecha($this->data['ProcesarAsiento']['fecha_proceso']);
				else:
					$this->mensaje->errorGuardar();
				endif;
			endif;
		endif;

		if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])):

			$this->render('index');

		endif;


		$this->set('procesoId', $procesoId);
		$this->set('aEjercicio', $aEjercicio);
		$this->set('fecha_nuevo_proceso', $fecha_nuevo_proceso);
		$this->set('disable_form', $disable_form);
		$this->set('show_asincrono', $show_asincrono);
		$this->set('showTabla', $showTabla);
	}


	function aprobar_asientos($procesoId){

		$aMutualProcesoAsiento = $this->MutualProcesoAsiento->read(null, $procesoId);

// 		$ejercicio = $this->Ejercicio->getEjercicio($aMutualProcesoAsiento['MutualProcesoAsiento']['co_ejercicio_id']);
		
		if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])):
			$this->render('index');
		endif;



		$fecha_nuevo_proceso = $aMutualProcesoAsiento['MutualProcesoAsiento']['fecha_hasta'];

		$this->set('aMutualProcesoAsiento', $aMutualProcesoAsiento);
		$this->set('procesoId', $procesoId);
		  
}


	function view_mayor_borrador($procesoId, $cuentaId=null, $consolidado = 0){
		$cuentaMayor = $this->MutualProcesoAsiento->getMayorDetalle($procesoId, $cuentaId, $consolidado);
		
		$this->set('cuentaMayor', $cuentaMayor);

	}


	function view_mayor_general_borrador($procesoId, $cuentaId=null){
		$aMutualProcesoAsiento = $this->MutualProcesoAsiento->read(null, $procesoId);
		$cuentaMayor = $this->MutualProcesoAsiento->getMayorDetalle($procesoId, $cuentaId);

		$fechaAsiento = $this->MutualAsiento->find('first', array('conditions' => array('MutualAsiento.mutual_proceso_asiento_id' => $procesoId), 'order' => array('MutualAsiento.fecha')));
		
		
		
		$this->set('procesoId', $procesoId);
		$this->set('aMutualProcesoAsiento', $aMutualProcesoAsiento);
		$this->set('cuentaMayor', $cuentaMayor);
		$this->set('fecha_desde', $fechaAsiento['MutualAsiento']['fecha']);
		
	}
	

	function view_libro_diario_borrador($procesoId){

		$this->paginate = array('limit' => 30,
		'conditions' => array('MutualAsiento.mutual_proceso_asiento_id' => $procesoId),
		'order' => array('MutualAsiento.fecha', 'MutualAsiento.id'));


		$aMutualProcesoAsiento = $this->MutualProcesoAsiento->read(null, $procesoId);


		$this->set('procesoId', $procesoId);
		$this->set('aMutualProcesoAsiento', $aMutualProcesoAsiento);
		$this->set('asientos', $this->paginate('MutualAsiento'));
	
	}


	function view_balance_borrador($procesoId, $consolidado = 0){
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
		$this->set('consolidado', $consolidado);
	
	}
	
	
	
}
?>