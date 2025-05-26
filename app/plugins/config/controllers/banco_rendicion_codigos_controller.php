<?php
class BancoRendicionCodigosController extends ConfigAppController{
	
	var $name = 'BancoRendicionCodigos';
	
	var $autorizar = array();
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	
	
	function index($banco_id){
		$this->paginate = array(
									'limit' => 30,
									'order' => array('BancoSucursal.nombre' => 'ASC')
									);
		$this->BancoRendicionCodigo->bindModel(array('belongsTo' => array('Banco')));	

		$this->set('banco',$this->BancoRendicionCodigo->Banco->read(null,$banco_id));
		
		$this->set('codigos',$this->BancoRendicionCodigo->find('all',array('conditions' => array('BancoRendicionCodigo.banco_id' => $banco_id),'order' => array('BancoRendicionCodigo.codigo'))));	
	}
	
	
	function add($banco_id = null){
		if (!empty($this->data)){
			if ($this->BancoRendicionCodigo->save($this->data)){
				$this->Mensaje->okGuardar();
				$this->redirect('index/'.$this->data['BancoRendicionCodigo']['banco_id']);				
			}else{
				$this->Mensaje->errorGuardar();
			}				
		}
		$this->BancoRendicionCodigo->bindModel(array('belongsTo' => array('Banco')));	
		$this->set('banco',$this->BancoRendicionCodigo->Banco->read(null,$banco_id));					
	}

	function edit($id=null){
		
		if(empty($id)) $this->redirect('index');
		if (!empty($this->data)){
			
			if ($this->BancoRendicionCodigo->save($this->data)){
				$this->Mensaje->okGuardar();
				$this->redirect('index/'.$this->data['BancoRendicionCodigo']['banco_id']);					
			}else{
				$this->Mensaje->errorGuardar();
			}				
		}		
		$this->BancoRendicionCodigo->bindModel(array('belongsTo' => array('Banco')));
		$this->data = $this->BancoRendicionCodigo->read(null,$id);		
		$this->set('banco',$this->BancoRendicionCodigo->Banco->read(null,$this->data['BancoRendicionCodigo']['banco_id']));
	}	
	

	function del($id = null){
		if(empty($id)) $this->redirect('index');
		$bancoId = $this->BancoRendicionCodigo->field('banco_id',array('BancoRendicionCodigo.id' => $id));
		if ($this->BancoRendicionCodigo->del($id)) {
			$this->Mensaje->okBorrar();
			$this->redirect('index/'.$bancoId);
		}else{
			$this->Mensaje->errorBorrar();
		}	
	}	
	

}
?>