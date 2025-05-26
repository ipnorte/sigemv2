<?php
class BarriosController extends ConfigAppController{
	
	var $name = "Barrios";
	
	function beforeFilter(){  
		$this->Seguridad->allow('autocomplete');
		parent::beforeFilter();  
	}	
	
	function index(){
		$this->Barrio->recursive = 3;
		$this->Barrio->bindModel(array('belongsTo' => array('Localidad')));
		$this->set('barrios',$this->paginate());
	}
	
	function add(){
		if (!empty($this->data)){
			
			if ($this->Barrio->save($this->data)){
				$this->Auditoria->log();
				$this->Mensaje->okGuardar();
				$this->redirect(array('action'=>'index'));				
			}else{
				$this->Mensaje->errorGuardar();
			}				
		}
	}
	
	function edit($id=null){
		if(empty($id)) $this->redirect('index');
		if (!empty($this->data)){
			
			if ($this->Barrio->save($this->data)){
				$this->Auditoria->log();
				$this->Mensaje->okGuardar();
				$this->redirect(array('action'=>'index'));				
			}else{
				$this->Mensaje->errorGuardar();
			}				
		}		
		$this->Barrio->recursive = 2;
		$this->data = $this->Barrio->read(null,$id);
	}
	
	function del($id = null){
		if(empty($id)) $this->redirect('index');
		if ($this->Barrio->del($id)) {
			$this->Mensaje->okBorrar();
			$this->Auditoria->log();
			$this->redirect(array('action'=>'index'));
		}else{
			$this->Mensaje->errorBorrar();
		}		
	}	
	
	function autocomplete(){
		Configure::write('debug',0);
//		debug($this->data);
		$this->Barrio->bindModel(array('belongsTo' => array('Localidad')));
		$this->set('barrios',$this->Barrio->find('all',array("conditions" => array("Barrio.nombre LIKE " => $this->data['ClienteDomicilio']['barrioAproxima'] . "%"),'order' => array('Barrio.nombre'),'limit' => 100)));
		$this->render(null,'ajax');
	}		
	
}
?>