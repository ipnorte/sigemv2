<?php
class TglobalController extends V1AppController{
	
	var $name = 'Tglobal';
	
	var $autorizar = array('combo');
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	
	
	function combo($label,$model,$prefix,$disable=0,$empty=0,$selected=''){
		$values = $this->Tglobal->find('list',array('conditions' => array('Tglobal.codigo LIKE ' => $prefix . '%', 'Tglobal.codigo <> ' => $prefix),'fields' => array('concepto'),'order' => array('Tglobal.codigo')));
		$this->set('values',$values);
		$this->set('model',$model);
		$this->set('label',$label);
		$this->set('disabled',$disable);
		$this->set('empty',$empty);
		$this->set('selected',$selected);
		$this->render();
	}	
	
}