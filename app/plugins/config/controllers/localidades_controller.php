<?php
class LocalidadesController extends ConfigAppController{
	
	var $name = "Localidades";
	
	function beforeFilter(){  
		$this->Seguridad->allow('autocomplete','form','cmb_provincias','autocomplete2');
		parent::beforeFilter();  
	}	
	
	function index(){
            $localidades = $provincia_id = NULL;
            if(!empty($this->data)){
                $provincia_id = $this->data['Localidad']['provincia_id'];
                
		$this->Localidad->bindModel(array('belongsTo' => array('Provincia')));
                $conditions = array();
                $conditions['Localidad.nombre LIKE'] = $this->data['Localidad']['nombre'] . "%";
                $conditions['Localidad.cp LIKE'] = $this->data['Localidad']['cp'] . "%";
                $localidades = $this->Localidad->find('all',array("conditions" => array('Localidad.provincia_id' => $provincia_id,'and' => $conditions),'order' => array('Localidad.nombre'),'limit' => 100));
	
            }
            $this->set('localidades',$localidades);
            $this->set('provincia_id',$provincia_id);
	}
	
	
	function add(){
		
		if (!empty($this->data)){
			
			if ($this->Localidad->save($this->data)){
				$this->Auditoria->log();
				$this->Mensaje->okGuardar();
				$this->redirect(array('action'=>'index'));				
			}else{
				$this->Mensaje->errorGuardar();
			}				
		}
		$this->Localidad->recursive = 2;
		$this->set('provincias',$this->Localidad->Provincia->find('list',array('fields' => 'Provincia.nombre','order' => 'Provincia.nombre')));
	}
	
	function edit($id = null){
		if(empty($id)) $this->redirect('index');
		if (!empty($this->data)){
			
			if ($this->Localidad->save($this->data)){
				$this->Auditoria->log();
				$this->Mensaje->okGuardar();
				$this->redirect(array('action'=>'index'));				
			}else{
				$this->Mensaje->errorGuardar();
			}				
		}		
		$this->Localidad->recursive = 2;
		$this->data = $this->Localidad->read(null,$id);
		$this->set('provincias',$this->Localidad->Provincia->find('list',array('fields' => 'Provincia.nombre','order' => 'Provincia.nombre')));
		
	}
	
	function del($id = null){
		if(empty($id)) $this->redirect('index');
		if ($this->Localidad->del($id)) {
			$this->Mensaje->okBorrar();
			$this->Auditoria->log();
			$this->redirect(array('action'=>'index'));
		}else{
			$this->Mensaje->errorBorrar();
		}		
	}
	
	function autocomplete($model='Persona'){
		Configure::write('debug',0);
//		debug($this->data);
		$this->Localidad->bindModel(array('belongsTo' => array('Provincia')));
		$this->set('localidades',$this->Localidad->find('all',array("conditions" => array("Localidad.nombre LIKE " => $this->data[$model]['localidadAproxima'] . "%"),'order' => array('Localidad.nombre'),'limit' => 100)));
		$this->render(null,'ajax');
	}
        
	function autocomplete2($toJSON = FALSE){
		Configure::write('debug',0);
//		debug($this);
//		debug($this->params['url']);
		$this->Localidad->bindModel(array('belongsTo' => array('Provincia')));
                $conditions = array();
                
                $localidad = "";
                if(isset($this->params['url']['query'])){
                    $localidad = $this->params['url']['query'];
                }
                $provincia_id = NULL;
                if(isset($this->params['url']['provincia_id'])){
                    $provincia_id = $this->params['url']['provincia_id'];
                }                
                if(isset($this->data['Persona']['codigoPostalAproxima'])){
                    $localidad = $this->data['Persona']['codigoPostalAproxima'];
                }                
                if(isset($this->data['Persona']['provincia_id'])){
                    $provincia_id = $this->data['Persona']['provincia_id'];
                }                
                
                $conditions['Localidad.nombre LIKE'] = $localidad . "%";
                $conditions['Localidad.cp LIKE'] = $localidad . "%";

                $localidades = $this->Localidad->find('all',array("conditions" => array('Localidad.provincia_id' => $provincia_id,'or' => $conditions),'order' => array('Localidad.nombre'),'limit' => 100));
		if($toJSON){
                    if(!empty($localidades)){
                        foreach($localidades as $localidad){
                            $row = [];
                            $row['id'] = $localidad['Localidad']['id'];
                            $row['label'] = htmlentities(stripslashes($localidad['Localidad']['nombre'])) . " (CP " . $localidad['Localidad']['cp']." - ".$localidad['Provincia']['nombre'].")";
                            $row['cp'] = $localidad['Localidad']['cp'];
                            $row['localidad'] = htmlentities(stripslashes($localidad['Localidad']['nombre']));
                            $row_set[] = $row;
                        }
                    }
                    echo json_encode($row_set);
                    exit;                
                }
                
                $this->set('localidades',$localidades);
                
                
                
		$this->render(null,'ajax');
	}        

	function form($model,$localidadId=0,$localidadNombre='',$codigo_postal='',$provincia_id=0){
		$this->set('model',Inflector::camelize($model));
		$this->data[$model]['localidad_id'] = $localidadId;
		$this->data[$model]['localidadAproxima'] = $localidadNombre;
		$this->data[$model]['localidad'] = $localidadNombre;
		$this->data[$model]['codigo_postal'] = $codigo_postal;
		$this->data[$model]['provincia_id'] = $provincia_id;
		$this->render(null,'blank');
	}
        
	function cmb_provincias(){
            App::import('Model', 'Config.Provincia');
            $this->Provincia = new Provincia(null);
            $values = $this->Provincia->find('list',array('conditions'=>array(),'fields' => array('nombre'), 'order' => 'nombre'));
            return $values;					
	}        
	
}
?>