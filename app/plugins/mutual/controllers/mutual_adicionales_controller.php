<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage controller
 */

class MutualAdicionalesController extends MutualAppController{
	var $name = 'MutualAdicionales';
	
	function index(){
		$this->MutualAdicional->bindModel(array('belongsTo' => array('Proveedor')));
		$adicionales = $this->MutualAdicional->find('all');
		$this->set('adicionales',$adicionales);
	}
	
	function add(){
		if (!empty($this->data)) {
			if ($this->MutualAdicional->guardar($this->data)){
				$this->Mensaje->okGuardar();
				$this->redirect(array('action'=>'index'));				
			}else{
				$this->Mensaje->errorGuardar();
			}		
		}
		$this->set('tipos',$this->MutualAdicional->getTipos());
		$this->set('aplicar_sobre',$this->MutualAdicional->getOptAplica());		
	}
	
	function edit($id){
		if (!$id) $this->redirect(array('action'=>'index'));
		if (!empty($this->data)){
			if ($this->MutualAdicional->guardar($this->data)){
				$this->Mensaje->okGuardar();
				$this->redirect(array('action'=>'index'));				
			}else{
				$this->Mensaje->errorGuardar();
			}			
		}
		$adicional = $this->MutualAdicional->read(null,$id);
		if (empty($adicional)) $this->redirect(array('action'=>'index'));
		$this->data = $adicional;
		$this->set('tipos',$this->MutualAdicional->getTipos());
		$this->set('aplicar_sobre',$this->MutualAdicional->getOptAplica());		
	}
	
	function del($id = null, $cascade = true){
		if (!$id) $this->redirect(array('action'=>'index'));
		if($this->MutualAdicional->del($id))$this->Mensaje->okBorrar();
		else $this->Mensaje->errorBorrar();
		$this->redirect(array('action'=>'index'));		
	}
}
?>