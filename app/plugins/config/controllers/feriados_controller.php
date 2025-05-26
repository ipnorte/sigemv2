<?php
class FeriadosController extends ConfigAppController{
	var $name = 'Feriados';
	
	function index(){
		$this->paginate = array(
									'limit' => 30,
									'order' => array('Feriado.fecha' => 'DESC')
									);		
		$this->set('feriados',$this->paginate());
	}
	
	function add(){
		if (!empty($this->data)){
			
			if ($this->Feriado->save($this->data)){
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
			
			if ($this->Feriado->save($this->data)){
				$this->Auditoria->log();
				$this->Mensaje->okGuardar();
				$this->redirect(array('action'=>'index'));				
			}else{
				$this->Mensaje->errorGuardar();
			}				
		}		
		$this->data = $this->Feriado->read(null,$id);
	}	
	
	function del($id = null){
		if(empty($id)) $this->redirect('index');
		if ($this->Feriado->del($id)) {
			$this->Mensaje->okBorrar();
			$this->Auditoria->log();
			$this->redirect(array('action'=>'index'));
		}else{
			$this->Mensaje->errorBorrar();
		}		
	}	
	
}
?>