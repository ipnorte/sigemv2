<?php 

class MutualServicioSolicitudesController extends MutualAppController{
	
	var $name = "MutualServicioSolicitudes";
	
	var $autorizar = array('imprimir_solicitud','get_solicitud','pendientes_aprobar','imprimir_solicitud_baja_adicional','get_solicitudes');
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}

	function index($persona_id = null){
		if(empty($persona_id)) $this->redirect('/pfyj/personas');
		App::import('Model','pfyj.Persona');
		$oPERSONA = new Persona();
		$oPERSONA->bindModel(array('hasOne' => array('Socio')));
		$persona = $oPERSONA->read(null,$persona_id);
		if(empty($persona)) parent::noDisponible();
		$this->set('persona',$persona);
		
		#CARGO LAS SOLICITUDES DE SERVICIOS
		$solicitudes = $this->MutualServicioSolicitud->getSolicitudesByPersonaID($persona_id);
		$this->set('solicitudes',$solicitudes);
	}
	
	
	function add($persona_id = null){
		
		if(empty($persona_id)) $this->redirect('/pfyj/personas');
		App::import('Model','pfyj.Persona');
		$oPERSONA = new Persona();
		$oPERSONA->bindModel(array('hasOne' => array('Socio')));
		$persona = $oPERSONA->read(null,$persona_id);
		if(empty($persona)) parent::noDisponible();
		$this->set('persona',$persona);
		
		#cargo los adicionales
		App::import('Model','pfyj.SocioAdicional');
		$oADICIONAL = new SocioAdicional();		
		
		$adicionales = $oADICIONAL->getAdicionalesByPersonaID($persona_id);
		$this->set('adicionales',$adicionales);
		
		if(!empty($this->data)):
			if($this->MutualServicioSolicitud->generarNuevaSolicitud($this->data)){
				$this->redirect("index/".$this->data['MutualServicioSolicitud']['persona_id']);
			}else{
				$this->Mensaje->errorGuardar();
			}
		endif;
		
		//SETEO LOS VALORES DE COBERTURA
		$fechaEmision = date('Y-m-d');
		$this->set('fechaEmision',$fechaEmision);
		$this->set('fechaCobertura',$this->MutualServicioSolicitud->calculaFechaCobertura($fechaEmision));
		
	}
	
	
	function imprimir_solicitud( $id = null, $baja = 0 ){
		if(empty($id)) parent::noDisponible();
		$solicitud = $this->MutualServicioSolicitud->getSolicitud($id);

		$this->set('solicitud',$solicitud);
		
		$proveedor_id = $this->MutualServicioSolicitud->GlobalDato('entero_1',$solicitud['MutualServicioSolicitud']['mutual_servicio_codigo']);
		
		if($proveedor_id == $this->MutualServicioSolicitud->lomas_villa_allende){
			App::import('Vendor','lomas_villa_allende');
			$oLOMASVA = new LomasVillaAllende();
			$this->set('solicitud',$solicitud);
			$this->render('reportes/' . $oLOMASVA->getRender($id),'pdf');
			return;
		}
		
		App::import('Model','pfyj.Persona');
		$oPERSONA = new Persona();
		$oPERSONA->bindModel(array('hasOne' => array('Socio')));
		$persona = $oPERSONA->read(null,$solicitud['MutualServicioSolicitud']['persona_id']);
		if(empty($persona)) parent::noDisponible();
		$this->set('persona',$persona);		
		if($baja == 0)$this->render('reportes/imprimir_solicitud_pdf','pdf');
		else $this->render('reportes/imprimir_solicitud_baja_pdf','pdf');
	}
	
	
	function imprimir_solicitud_baja_adicional($id = null, $adicional_id = null){
		if(empty($id)) parent::noDisponible();
		if(empty($adicional_id)) parent::noDisponible();
		
		$solicitud = $this->MutualServicioSolicitud->getSolicitud($id);
		
		$adicionalBaja = Set::extract("/MutualServicioSolicitudAdicional[socio_adicional_id=$adicional_id]",$solicitud);
		if(!empty($adicionalBaja)){
			$solicitud['MutualServicioSolicitudAdicional'] = array();
			$solicitud['MutualServicioSolicitudAdicional'][0] = $adicionalBaja[0]['MutualServicioSolicitudAdicional'];
		}
		$this->set('solicitud',$solicitud);
		
		
		$this->render('reportes/imprimir_solicitud_baja_adicional_pdf','pdf');
		
	}
	
	
	function baja_solicitud( $id = null ){
		if(empty($id)) parent::noDisponible();
		$solicitud = $this->MutualServicioSolicitud->getSolicitud($id);
		if(empty($solicitud)) parent::noDisponible();

		App::import('Model','pfyj.Persona');
		$oPERSONA = new Persona();
		$oPERSONA->bindModel(array('hasOne' => array('Socio')));
		$persona = $oPERSONA->read(null,$solicitud['MutualServicioSolicitud']['persona_id']);
		if(empty($persona)) parent::noDisponible();
		
		
		if(!empty($this->data)):

			$fechaBaja = $this->data['MutualServicioSolicitud']['fecha_baja_servicio'];
			$periodoHasta = $this->data['MutualServicioSolicitud']['periodo_hasta'];
//			$fechaBaja = $this->MutualServicioSolicitud->armaFecha($this->data['MutualServicioSolicitud']['fecha_baja_servicio']);
//			$periodoHasta = $this->data['MutualServicioSolicitud']['periodo_hasta']['year'].$this->data['MutualServicioSolicitud']['periodo_hasta']['month'];

			$obs = $this->data['MutualServicioSolicitud']['observaciones'];
			$solicitudID = $this->data['MutualServicioSolicitud']['id'];
			
			if($this->MutualServicioSolicitud->bajaSolicitud($solicitudID,$fechaBaja,$periodoHasta,$obs)){
				$this->redirect("index/".$this->data['MutualServicioSolicitud']['persona_id']);
			}else{
				$this->Mensaje->errorGuardar();
			}

		endif;		
		$this->set('persona',$persona);		
		
		$this->set('solicitud',$solicitud);
		
		//SETEO LOS VALORES DE COBERTURA
		$fechaEmision = date('Y-m-d');
		$this->set('fechaEmision',$fechaEmision);
		$this->set('fechaCobertura',$this->MutualServicioSolicitud->calculaFechaCobertura($fechaEmision,true));
		
		
		
	}	
	
	
	function agregar_adicional($id = null){
		
		if(empty($id)) parent::noDisponible();
		
		$solicitud = $this->MutualServicioSolicitud->getSolicitud($id);
		if(empty($solicitud)) parent::noDisponible();

		App::import('Model','pfyj.Persona');
		$oPERSONA = new Persona();
		$oPERSONA->bindModel(array('hasOne' => array('Socio')));
		$persona = $oPERSONA->read(null,$solicitud['MutualServicioSolicitud']['persona_id']);
		if(empty($persona)) parent::noDisponible();
		
		App::import('Model','pfyj.SocioAdicional');
		$oADICIONAL = new SocioAdicional();		
		
		$adicionales = $oADICIONAL->getAdicionalesByPersonaID($solicitud['MutualServicioSolicitud']['persona_id']);
		
		$adicServicio = Set::extract('/socio_adicional_id', $solicitud['MutualServicioSolicitudAdicional']);

		$adicionalesNoInc = array();
		#SACO LOS ADICIONALES QUE NO ESTAN VINCULADOS A LA SOLICITUD
		if(!empty($adicionales)):
			foreach($adicionales as $adicional):
				if(!in_array($adicional['SocioAdicional']['id'],$adicServicio))array_push($adicionalesNoInc,$adicional);
			endforeach;
		endif;
		
		$this->set('adicionales',$adicionalesNoInc);		
		
		
		if(!empty($this->data)):
		
//			debug($this->data);
//			exit;
			
			$solicitudID = $this->data['MutualServicioSolicitudAdicional']['mutual_servicio_solicitud_id'];
//			$periodoDesde = $this->data['MutualServicioSolicitudAdicional']['periodo_desde']['year'].$this->data['MutualServicioSolicitudAdicional']['periodo_desde']['month'];
			$periodoDesde = $this->data['MutualServicioSolicitudAdicional']['periodo_desde'];
			$adicionales = array_keys($this->data['MutualServicioSolicitudAdicional']['socio_adicional_id']);
			$fechaAlta = $this->data['MutualServicioSolicitudAdicional']['fecha_alta'];
			$beneficioID = (isset($this->data['MutualServicioSolicitud']['persona_beneficio_id']) ? $this->data['MutualServicioSolicitud']['persona_beneficio_id'] : 0);
			
			if($this->MutualServicioSolicitud->anexarAdicionales($solicitudID,$periodoDesde,$adicionales,$fechaAlta,$beneficioID)){
				
				$this->redirect("index/".$solicitud['MutualServicioSolicitud']['persona_id']);
				
			}else{
				$this->Mensaje->errorGuardar();
			}
			
			
		endif;		
		
		
		$this->set('persona',$persona);		
		
		$this->set('solicitud',$solicitud);
		
		//SETEO LOS VALORES DE COBERTURA
		$fechaEmision = date('Y-m-d');
		$this->set('fechaEmision',$fechaEmision);
		$fechaCobertura = $this->MutualServicioSolicitud->calculaFechaCobertura($fechaEmision);
		$this->set('fechaCobertura',$fechaCobertura);
		
		//determino el periodo de inicio del descuento
//		App::import('Model', 'Proveedores.ProveedorVencimiento');
//		$oVTOS = new ProveedorVencimiento(null);
//			
//		$vtos = $oVTOS->calculaVencimiento($solicitud['MutualServicioSolicitud']['proveedor_id'],$solicitud['MutualServicioSolicitud']['persona_beneficio_id'],$fechaCobertura);
//		
//		debug($vtos);
		
		
	}
	
	
	function baja_adicional($id = null, $adicional_id = null,$solicitud_adicional_id = null){
		if(empty($id)) parent::noDisponible();
		if(empty($adicional_id)) parent::noDisponible();
		if(empty($solicitud_adicional_id)) parent::noDisponible();
		
		$solicitud = $this->MutualServicioSolicitud->getSolicitud($id);
		if(empty($solicitud)) parent::noDisponible();

		App::import('Model','pfyj.Persona');
		$oPERSONA = new Persona();
		$oPERSONA->bindModel(array('hasOne' => array('Socio')));
		$persona = $oPERSONA->read(null,$solicitud['MutualServicioSolicitud']['persona_id']);
		if(empty($persona)) parent::noDisponible();
		
		
		App::import('Model','pfyj.SocioAdicional');
		$oADICIONAL = new SocioAdicional();		
		
		$adicional = $oADICIONAL->getAdicional($adicional_id);
		if(empty($adicional)) parent::noDisponible();
		
		
		if(!empty($this->data)):
		
			App::import('Model','mutual.MutualServicioSolicitudAdicional');
			$oSOL_ADIC = new MutualServicioSolicitudAdicional();			
		
			$solAdic = $oSOL_ADIC->read(null,$this->data['MutualServicioSolicitudAdicional']['id']);
			
			if($solAdic['MutualServicioSolicitudAdicional']['mutual_servicio_solicitud_id'] == $this->data['MutualServicioSolicitudAdicional']['mutual_servicio_solicitud_id']){
				
				$borrar = (isset($this->data['MutualServicioSolicitudAdicional']['borrar_registro']) ? true : false);
				
				$periodoHasta = $this->data['MutualServicioSolicitudAdicional']['periodo_hasta'];
				$fechaCoberturaHasta = $this->data['MutualServicioSolicitudAdicional']['fecha_baja'];
				$observaciones = $this->data['MutualServicioSolicitudAdicional']['observaciones'];
				
				if($oSOL_ADIC->bajaAdicional($this->data['MutualServicioSolicitudAdicional']['id'],$periodoHasta,$fechaCoberturaHasta,$borrar,$observaciones)){
					$this->redirect("index/".$this->data['MutualServicioSolicitudAdicional']['persona_id']);
				}else{
					$this->Mensaje->errorGuardar();
				}				
				
//				if(isset($this->data['MutualServicioSolicitudAdicional']['borrar_registro'])){
//					if($oSOL_ADIC->del($this->data['MutualServicioSolicitudAdicional']['id'])){
//						$this->redirect("index/".$this->data['MutualServicioSolicitudAdicional']['persona_id']);
//					}else{
//						$this->Mensaje->errorBorrar();
//					}
//				}else{
//					$solAdic['MutualServicioSolicitudAdicional']['periodo_hasta'] = $this->data['MutualServicioSolicitudAdicional']['periodo_hasta'];
//					$solAdic['MutualServicioSolicitudAdicional']['fecha_baja'] = $this->data['MutualServicioSolicitudAdicional']['fecha_baja'];
//					$solAdic['MutualServicioSolicitudAdicional']['observaciones'] = $this->data['MutualServicioSolicitudAdicional']['observaciones'];
//					if($oSOL_ADIC->save($solAdic)){
//						$this->redirect("index/".$this->data['MutualServicioSolicitudAdicional']['persona_id']);
//					}else{
//						$this->Mensaje->errorGuardar();
//					}					
//				}
			}else{
				$this->Mensaje->errorBorrar();
			}
		endif;
		
		
		$this->set('adicional',$adicional);		
		$this->set('persona',$persona);	
		$this->set('solicitud',$solicitud);	

		$this->set('solicitud_adicional_id',$solicitud_adicional_id);
		
		//SETEO LOS VALORES DE COBERTURA
		$fechaEmision = date('Y-m-d');
		$this->set('fechaEmision',$fechaEmision);
		$this->set('fechaCobertura',$this->MutualServicioSolicitud->calculaFechaCobertura($fechaEmision,true));
		
		
	}
	
	
	function pendientes_aprobar(){
		if(isset($this->params['url']['ORD']) && !empty($this->params['url']['ORD'])){
			$this->set('mutual_servicio_solicitud_id',$this->params['url']['ORD']);
			$solicitud = $this->MutualServicioSolicitud->read(null,$this->params['url']['ORD']);
			$this->set('solicitud',$solicitud);
			$this->render('pendiente_aprobar_formulario');
		}
		if(!empty($this->data)){
			if($this->data['MutualServicioSolicitud']['aprobar'] == 1){
				if($this->MutualServicioSolicitud->aprobar($this->data['MutualServicioSolicitud']['id'])){
					$this->Mensaje->ok("LA ORDEN DE CONSUMO / SERVICIO #".$this->data['MutualServicioSolicitud']['id']." FUE APROBADA CORRECTAMENTE!.");
				}else{
					$this->Mensaje->error("LA ORDEN DE CONSUMO / SERVICIO #".$this->data['MutualServicioSolicitud']['id']." NO SE PUDO APROBAR!.");
				}
				$this->redirect('/mutual/mutual_producto_solicitudes/pendientes_aprobar');
			}else{
				$this->redirect('/mutual/mutual_producto_solicitudes/pendientes_aprobar');
			}
		}		
	}
	
	
	function del($id=null){
		if(empty($id)) parent::noAutorizado();
		if($this->MutualServicioSolicitud->borrar($id)){
			$this->Mensaje->ok('LA SOLICITUD DE SERVICIO # ' . $id . ' FUE BORRADA CORRECTAMENTE!.');
		}else{
			$this->Mensaje->error('LA SOLICITUD DE SERVICIO # ' . $id . ' NO PUDO SER BORRADA!.');
		}
		$this->redirect('/mutual/mutual_producto_solicitudes/pendientes_aprobar');
	}	
	
	
	function get_solicitud($id){
		return $this->MutualServicioSolicitud->getSolicitud($id);
	}

	
	function get_solicitudes($persona_id,$socio_id = null){
		if(empty($socio_id)) $solicitudes = $this->MutualServicioSolicitud->getSolicitudesByPersonaID($persona_id);
		else $solicitudes = $this->MutualServicioSolicitud->getSolicitudesBySocioID($socio_id);
		return $solicitudes;
	}
	
	
}

?>