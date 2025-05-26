<?php
class PersonaNovedadesController extends PfyjAppController{

	var $name = "PersonaNovedades";
	
	var $autorizar = array(
							'index',
							'add',
							'view',
							'comentar_novedad',
							'novedades_by_persona'	
	);
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	
	
	function index($persona_id = null){
		if(empty($persona_id)) parent::noDisponible();
		App::import("Model","Pfyj.Persona");
		$oPERSONA = new Persona();
		$oPERSONA->bindModel(array('hasOne' => array('Socio')));
		$persona = $oPERSONA->read(null,$persona_id);
		if(empty($persona)) parent::noDisponible();
		$this->set('persona',$persona);
		$this->paginate = array(
								'limit' => 30,
								'order' => array('PersonaNovedad.created' => 'DESC'),
								);
		$condiciones = array("PersonaNovedad.persona_id" => $persona['Persona']['id']);						
	
		$this->set('novedades', $this->paginate(null,$condiciones));			
	}
	
	
	function novedades_by_persona($persona_id = null,$limit=0){
		if(empty($persona_id)) parent::noDisponible();
		if($limit == 0) $novedades = $this->PersonaNovedad->find('all',array('conditions' => array('PersonaNovedad.persona_id' => $persona_id),'order' => array('PersonaNovedad.created' => 'DESC')));
		else $novedades = $this->PersonaNovedad->find('all',array('conditions' => array('PersonaNovedad.persona_id' => $persona_id),'order' => array('PersonaNovedad.created' => 'DESC'), 'limit' => $limit));
		return $novedades;
	}
	
	
	function add($persona_id = null){
		if(empty($persona_id)) parent::noDisponible();
		App::import("Model","Pfyj.Persona");
		$oPERSONA = new Persona();
		$oPERSONA->bindModel(array('hasOne' => array('Socio')));
		$persona = $oPERSONA->read(null,$persona_id);
		if(empty($persona)) parent::noDisponible();
		$this->set('persona',$persona);
		
		$user = $this->Seguridad->user();
		$this->set('user',$user);
		
		$allowed = array('xls','doc','pdf','gif','jpg','jpeg','png','txt');
		$this->set('tipos_permitidos',$allowed);
		
		if(!empty($this->data)){
			
			
			if($this->data['PersonaNovedad']['archivo']['error'] != 4){

				
				$ext = trim(substr($this->data['PersonaNovedad']['archivo']['name'],strrpos($this->data['PersonaNovedad']['archivo']['name'],".")+1,strlen($this->data['PersonaNovedad']['archivo']['name'])));
				$ext = strtolower($ext);
				
				if(in_array($ext,$allowed)){

					list($name,$ext) = explode(".",$this->data['PersonaNovedad']['archivo']['name']);
					
					$nombre = str_replace(" ","_",$name);
					$nombre = str_replace(",","-",$name);
					$nombre = str_replace(".","-",$name);
					
//					$this->data['PersonaNovedad']['archivo_adjunto'] = $persona_id."_".$nombre.'_'.rand(100,999).".".$ext;
					$this->data['PersonaNovedad']['archivo_adjunto'] = $persona_id."_".rand(1,999999).".".$ext;
					//subir_archivo
                                        if(!file_exists(WWW_ROOT . 'files' . DS . 'socios' . DS . 'novedades')){
                                            mkdir(WWW_ROOT . 'files' . DS . 'socios' . DS . 'novedades', 0777, true);
                                        }
					$target_path = WWW_ROOT . 'files' . DS . 'socios' . DS . 'novedades' . DS . $this->data['PersonaNovedad']['archivo_adjunto'];
					
					if($this->PersonaNovedad->save($this->data)){
						
						if(move_uploaded_file($this->data['PersonaNovedad']['archivo']['tmp_name'], $target_path)){
							
							$this->Mensaje->ok("NOVEDAD GENERADA CORRECTAMENTE!");
							$this->redirect("index/".$this->data['PersonaNovedad']['persona_id']);
														
						}else{
							
							$this->Mensaje->error("SE PRODUJO UN ERROR AL INTENTAR SUBIR EL ARCHIVO ENVIADO!");
							$this->render();
							return;							
						}
						
					}else{
						
						$this->Mensaje->error("SE PRODUJO UN ERROR AL INTENTAR GUARDAR LA NOVEDAD");
						
					}					
					
					
				}else{
					$this->Mensaje->error("TIPO DE ARCHIVO NO PERMITIDO");
					$this->render();
					return;					
				}
				
			}else{

				if($this->PersonaNovedad->save($this->data)){
					$this->Mensaje->ok("NOVEDAD GENERADA CORRECTAMENTE!");
					$this->redirect("index/".$this->data['PersonaNovedad']['persona_id']);
				}else{
					$this->Mensaje->error("SE PRODUJO UN ERROR AL INTENTAR GUARDAR LA NOVEDAD");
				}
				
			}
			
		}
		
	}
	
	
	function view($id = null){
		if(empty($id)) parent::noDisponible();
		$this->PersonaNovedad->bindModel(array('hasMany' => array('PersonaNovedadComentario' => array('order' => 'PersonaNovedadComentario.fecha DESC'))));
		$novedad = $this->PersonaNovedad->read(null,$id);
		if(empty($novedad)) parent::noDisponible();
		App::import("Model","Pfyj.Persona");
		$oPERSONA = new Persona();
		$oPERSONA->bindModel(array('hasOne' => array('Socio')));
		$persona = $oPERSONA->read(null,$novedad['PersonaNovedad']['persona_id']);
		if(empty($persona)) parent::noDisponible();
		$this->set('persona',$persona);
		$this->set('novedad',$novedad);
		
	}
	
	
	function comentar_novedad($id){
		if(empty($id)) parent::noDisponible();
		$novedad = $this->PersonaNovedad->read(null,$id);
		if(empty($novedad)) parent::noDisponible();
		App::import("Model","Pfyj.Persona");
		$oPERSONA = new Persona();
		$oPERSONA->bindModel(array('hasOne' => array('Socio')));
		$persona = $oPERSONA->read(null,$novedad['PersonaNovedad']['persona_id']);
		if(empty($persona)) parent::noDisponible();
		$this->set('persona',$persona);
		$this->set('novedad',$novedad);	
		
		$user = $this->Seguridad->user();
		$this->set('user',$user);
		
		$allowed = array('xls','doc','pdf','gif','jpg','jpeg','png','txt');
		$this->set('tipos_permitidos',$allowed);
		

		if(!empty($this->data)){
			
			App::import("Model","Pfyj.PersonaNovedadComentario");
			$oCOMENTARIO = new PersonaNovedadComentario();	

			
			if($this->data['PersonaNovedadComentario']['archivo']['error'] != 4){

				$ext = trim(substr($this->data['PersonaNovedadComentario']['archivo']['name'],strrpos($this->data['PersonaNovedadComentario']['archivo']['name'],".")+1,strlen($this->data['PersonaNovedadComentario']['archivo']['name'])));
				$ext = strtolower($ext);
				
				if(in_array($ext,$allowed)){

					list($name,$ext) = explode(".",$this->data['PersonaNovedadComentario']['archivo']['name']);
					$nombre = str_replace(" ","_",$name);
					$this->data['PersonaNovedadComentario']['archivo_adjunto'] = $novedad['PersonaNovedad']['persona_id']."_".$nombre.'_'.rand(100,999).".".$ext;
					//subir_archivo
					$target_path = WWW_ROOT . 'files' . DS . 'socios' . DS . 'novedades' . DS . $this->data['PersonaNovedadComentario']['archivo_adjunto'];
//					debug($this->data);
//					exit;
					if($oCOMENTARIO->save($this->data)){
						
						if(move_uploaded_file($this->data['PersonaNovedadComentario']['archivo']['tmp_name'], $target_path)){
							
							$this->Mensaje->ok("COMENTARIO GENERADO CORRECTAMENTE!");
							$this->redirect("index/".$novedad['PersonaNovedad']['persona_id']);
														
						}else{
							
							$this->Mensaje->error("SE PRODUJO UN ERROR AL INTENTAR SUBIR EL ARCHIVO ENVIADO!");
							$this->render();
							return;							
						}
						
					}else{
						
						$this->Mensaje->error("SE PRODUJO UN ERROR AL INTENTAR GUARDAR EL COMENTARIO");
						
					}					
					
					
				}else{
					$this->Mensaje->error("TIPO DE ARCHIVO NO PERMITIDO");
					$this->render();
					return;					
				}
				
			}else{

				if($oCOMENTARIO->save($this->data)){
					$this->Mensaje->ok("COMENTARIO GENERADO CORRECTAMENTE!");
					$this->redirect("index/".$novedad['PersonaNovedad']['persona_id']);
				}else{
					$this->Mensaje->error("SE PRODUJO UN ERROR AL INTENTAR GUARDAR EL COMENTARIO");
				}
				
			}			
			
			
//			if($oCOMENTARIO->save($this->data)){
//				$this->Mensaje->ok("COMENTARIO GENERADO CORRECTAMENTE!");
//				$this->redirect("index/".$novedad['PersonaNovedad']['persona_id']);
//			}else{
//				$this->Mensaje->error("SE PRODUJO UN ERROR AL INTENTAR GUARDAR EL COMENTARIO");
//			}
		}		
		
	}
	
	
}
?>