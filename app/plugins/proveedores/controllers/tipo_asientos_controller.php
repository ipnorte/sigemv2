<?php
class TipoAsientosController extends ProveedoresAppController{
	
	var $name = 'TipoAsientos';
	var $uses = array('Proveedores.TipoAsiento');
	
	var $autorizar = array('combo', 'borrar');
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	
	
	
	function index($id = null){
		$this->paginate = array(
								'limit' => 50
								);
									
		$this->set('tipoAsientos', $this->paginate());			
		$this->render('index');		
	}
	
	
	function add(){
		if(!empty($this->data)):
			if($this->TipoAsiento->grabar($this->data)):
				$this->Mensaje->okGuardar();
			else:
				$this->Mensaje->errorGuardar();
			endif;
		endif;
		
	}
	

	function edit($id=null){
		
		if(!empty($this->data)):
			if($this->TipoAsiento->grabar($this->data)):
				$this->Mensaje->okGuardar();
			else:
				$this->Mensaje->errorGuardar();
			endif;
		endif;
		
		if(empty($id)) parent::noAutorizado();
		$this->combo();
		$this->data = $this->TipoAsiento->traerTipoAsiento($id);
	}
	
	
	function borrar($id=null){
		if(empty($id)) parent::noAutorizado();
		
		if($this->TipoAsiento->borrar($id)):
			$this->Mensaje->okBorrar();
		else:
			$this->Mensaje->errorBorrar();
		endif;
		
		$this->redirect('index');
	}
	
	function combo(){
		return $this->TipoAsiento->combo();
		
	}
	
}
?>