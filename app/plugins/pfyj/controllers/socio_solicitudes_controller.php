<?php
class SocioSolicitudesController extends PfyjAppController{
	var $name = 'SocioSolicitudes';
	
	var $tplSolicitudAfiliacion = 'solicitud_afiliacion.html';
	
	
	function view($id=null,$render='view',$layout=null){
		if(empty($id)) $this->redirect('/pfyj/personas');
                
                $datos = $this->SocioSolicitud->infoPDF($id);
                $this->set('datos',$datos);
		
		$this->render($render,$layout);
	}
	
	
	function add($persona_id = null){
		if(empty($persona_id)) $this->redirect('/pfyj/personas');
		
		 if(!empty($this->data)){
		 	
		 	$this->data['SocioSolicitud']['periodo_ini'] = $this->data['SocioSolicitud']['fecha']['year'] . $this->data['SocioSolicitud']['fecha']['month'];
		 	$this->data['SocioSolicitud']['fecha'] = date('Y-m-d');
		 	
		    if($this->SocioSolicitud->save($this->data)){
    			$this->Auditoria->log();
    			$this->Mensaje->okGuardar();
    			$this->redirect('/pfyj/socios/index/'.$persona_id); 
    		}else{
    			$this->Mensaje->errorGuardar();
    		}		 	
		 	
		 }
		
		
		$this->SocioSolicitud->bindModel(array('belongsTo' => array('Persona')));
		$this->SocioSolicitud->Persona->recursive = 3;
		$this->SocioSolicitud->Persona->bindModel(array('hasMany' => array('PersonaBeneficio' => array('conditions' => array('PersonaBeneficio.activo' => 1),'order' => 'PersonaBeneficio.created DESC'))));
		$this->set('persona',$this->SocioSolicitud->Persona->read(null,$persona_id));
		$this->SocioSolicitud->unbindModel(array('belongsTo' => array('Persona')));	
		$this->set('tipo','A');
		$this->render();	
	}
	
	
	function aprobar($id = null,$liquiPrimeraCuotaSocial=0){
		if(empty($id)) $this->redirect('/pfyj/personas');
//		$this->SocioSolicitud->bindModel(array('belongsTo' => array('Persona','PersonaBeneficio')));
		$sol = $this->SocioSolicitud->read(null,$id);
		if($this->SocioSolicitud->aprobar($sol,null,null,($liquiPrimeraCuotaSocial == 1 ? true : false))){
			$this->Auditoria->log();
			$this->Mensaje->ok("LA SOLICITUD DE SOCIO FUE APROBADA CORRECTAMENTE");
		}else{
			$this->Mensaje->error("NO SE PUDO APROBAR LA SOLICITUD DE SOCIO --  VERIFIQUE");
		}
		
		$this->redirect('/pfyj/socios/index/'.$sol['SocioSolicitud']['persona_id']); 
		
	}
	
//	function aprobarAndLiquidarPrimeraCuotaSocial($id = null){
//		if(empty($id)) $this->redirect('/pfyj/personas');
////		$this->SocioSolicitud->bindModel(array('belongsTo' => array('Persona','PersonaBeneficio')));
//		$sol = $this->SocioSolicitud->read(null,$id);
//		if($this->SocioSolicitud->aprobar($sol,null,null,true)){
//			$this->Auditoria->log();
//			$this->Mensaje->ok("LA SOLICITUD DE SOCIO FUE APROBADA CORRECTAMENTE");
//		}else{
//			$this->Mensaje->error("NO SE PUDO APROBAR LA SOLICITUD DE SOCIO --  VERIFIQUE");
//		}
//		
//		$this->redirect('/pfyj/socios/index/'.$sol['SocioSolicitud']['persona_id']); 
//		
//	}	
	
	
}
?>