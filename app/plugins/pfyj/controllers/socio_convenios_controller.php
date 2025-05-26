<?php
class SocioConveniosController extends PfyjAppController{
	var $name = 'SocioConvenios';
	
	function index($socio_id = null,$menuPersonas=1){
		if(empty($socio_id)) $this->redirect('/pfyj/personas');
		$this->SocioConvenio->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->SocioConvenio->Socio->read(null,$socio_id);
		$this->set('menuPersonas',$menuPersonas);
		$this->set('socio',$socio);		
		if(empty($socio)) $this->redirect('/pfyj/personas');
		
		$convenios = $this->SocioConvenio->getConveniosBySocio($socio_id);		
		
		$this->set('convenios',$convenios);	
		
	}

	function crear_convenio($socio_id = null,$menuPersonas=1){
		if(empty($socio_id)) parent::noAutorizado();
		$this->SocioConvenio->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->SocioConvenio->Socio->read(null,$socio_id);
		$this->set('menuPersonas',$menuPersonas);
		$this->set('socio',$socio);	

		if(!empty($this->data)){
			
			if($this->RequestHandler->isAjax()){
				//guardo los datos en una session
				$this->Session->del($this->name.'.datos_convenio'); 
				$this->Session->write($this->name.'.datos_convenio', $this->data); 
				$datosConvenio = $this->data;
				$convenio = $this->SocioConvenio->generarConvenio($datosConvenio,true);
				$this->set('convenio',$convenio);
				$this->render('vista_previa_convenio_ajax','ajax');
			}else if($this->data['SocioConvenio']['generar'] == 1){
				if($this->Session->check($this->name.'.datos_convenio')){
					$datosConvenio = $this->Session->read($this->name.'.datos_convenio'); 
					if($this->SocioConvenio->generarConvenio($datosConvenio)){
						$this->redirect('index/'.$socio_id.'/1');
						$this->Mensaje->error("Convenio generado correctamente!");
					}else{
						$this->Mensaje->error("Se produjo un error al crear el Convenio!");
					}					
				}else{
					$this->Mensaje->error("Se produjo un error al crear el Convenio!");
				}

			}
			
		}

		//cargo las cuotas adeudadas
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCuota = new OrdenDescuentoCuota();
		$cuotas = $oCuota->cuotasAdeudadasBySocio($socio_id,'MUTUSICUMUTU',true);
		$this->set('cuotas',$cuotas);
		
	}
	
	function view($id=null){
		if(empty($id)) parent::noAutorizado();
		$this->set('convenio',$this->SocioConvenio->getConvenio($id));
	}
	
	
}
?>