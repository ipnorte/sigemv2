<?php 

class SocioAdicionalesController extends PfyjAppController{
	
	var $name = 'SocioAdicionales';
	
	
	var $autorizar = array('add','edit');
	
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
		
		#cargo los adicionales
		$adicionales = $this->SocioAdicional->getAdicionalesByPersonaID($persona_id);
		$this->set('adicionales',$adicionales);
		
	}
	

	function add($persona_id = null){
		if(empty($persona_id)) $this->redirect('/pfyj/personas');
		App::import('Model','pfyj.Persona');
		$oPERSONA = new Persona();
		$oPERSONA->bindModel(array('hasOne' => array('Socio')));
		$persona = $oPERSONA->read(null,$persona_id);
		if(empty($persona)) parent::noDisponible();
		$this->set('persona',$persona);
		
		#FIJO LOS DATOS DEL DOMICILIO DE LA PERSONA
		$this->set('localidad_id',$persona['Persona']['localidad_id']);
		$this->set('localidad',$persona['Persona']['localidad']);
		$this->set('codigo_postal',$persona['Persona']['codigo_postal']);
		$this->set('provincia_id',$persona['Persona']['provincia_id']);
		$this->set('calle',$persona['Persona']['calle']);
		$this->set('numero_calle',$persona['Persona']['numero_calle']);
		$this->set('piso',$persona['Persona']['piso']);
		$this->set('dpto',$persona['Persona']['dpto']);
		$this->set('barrio',$persona['Persona']['barrio']);
		
		if(!empty($this->data)):
			if($this->SocioAdicional->guardar($this->data)){
				$this->redirect("index/".$this->data['SocioAdicional']['persona_id']);
			}else{
				$this->Mensaje->errorGuardar();
			}
		endif;
		
	}
	
	function edit($id = null){
		if(empty($id)) parent::noDisponible();
		$adicional = $this->SocioAdicional->getAdicional($id);
		App::import('Model','pfyj.Persona');
		$oPERSONA = new Persona();
		$oPERSONA->bindModel(array('hasOne' => array('Socio')));
		$persona = $oPERSONA->read(null,$adicional['SocioAdicional']['persona_id']);
		if(empty($persona)) parent::noDisponible();
		$this->set('persona',$persona);
		if(!empty($this->data)):
			if($this->SocioAdicional->guardar($this->data)){
				$this->redirect("index/".$this->data['SocioAdicional']['persona_id']);
			}else{
				$this->Mensaje->errorGuardar();
			}
		endif;		
		$this->data = $adicional;
		$this->set('localidad_id',$adicional['SocioAdicional']['localidad_id']);
		$this->set('localidad',$adicional['SocioAdicional']['localidad']);
		$this->set('codigo_postal',$adicional['SocioAdicional']['codigo_postal']);
		$this->set('provincia_id',$adicional['SocioAdicional']['provincia_id']);
		
		
		
	}
	
	
	function borrar_adicional($id = null){
		if(empty($id)) parent::noDisponible();
		$adicional = $this->SocioAdicional->read(null,$id);
		if(!$this->SocioAdicional->borrar($id)){
			$this->Mensaje->error("NO SE PUDO BORRAR EL ADICIONAL. VERIFIQUE QUE NO ESTE VINCULADO A ALGUN SERVICIO DEL SOCIO TITULAR");
		}
		$this->redirect("index/".$adicional['SocioAdicional']['persona_id']);		
	}
	
	
	
	
	
}

?>