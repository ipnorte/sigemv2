<?php
class ClienteTipoAsientosController extends ClientesAppController{
	
	var $name = 'ClienteTipoAsientos';
	var $uses = array('Clientes.ClienteTipoAsiento');
	
	var $autorizar = array('combo', 'borrar');
	
	function _construct(){
		
		parent::_construct();
//		App::import('Model', 'Clientes.TipoAsiento');
//		$this->TipoAsiento = new TipoAsiento();	
	}
	
	
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
//			App::import('Model', 'Clientes.ClienteTipoAsiento');
//			$this->ClienteTipoAsiento = new ClienteTipoAsiento();
				
			if($this->ClienteTipoAsiento->grabar($this->data)):
				$this->Mensaje->okGuardar();
			else:
				$this->Mensaje->errorGuardar();
			endif;
		endif;
		
	}
	

	function edit($id=null){
		
		if(!empty($this->data)):
			if($this->ClienteTipoAsiento->grabar($this->data)):
				$this->Mensaje->okGuardar();
			else:
				$this->Mensaje->errorGuardar();
			endif;
		endif;
		
		if(empty($id)) parent::noAutorizado();
		$this->combo();
		$this->data = $this->ClienteTipoAsiento->traerTipoAsiento($id);
	}
	
	
	function borrar($id=null){
		if(empty($id)) parent::noAutorizado();
		
		if($this->ClienteTipoAsiento->borrar($id)):
			$this->Mensaje->okBorrar();
		else:
			$this->Mensaje->errorBorrar();
		endif;
		
		$this->redirect('index');
	}
	
	function combo(){
		return $this->ClienteTipoAsiento->combo();
		
	}
	
}
?>