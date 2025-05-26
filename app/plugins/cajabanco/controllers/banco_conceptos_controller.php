<?php
class BancoConceptosController extends CajabancoAppController{
	
	var $name = 'BancoConceptos';
	
	var $autorizar = array('index','add','edit','del','get', 'combo');
	
	
	function index($tipo=6){
		if($tipo == 7):
			$condiciones = array(
							'BancoConcepto.tipo' => 7
			);
		else:
			$condiciones = array(
							'BancoConcepto.tipo != ' => 7
			);
		endif;
		$this->paginate = array(
									'limit' => 30,
									'order' => array('BancoConcepto.concepto' => 'ASC')
									);		
		$this->set('tipo', $tipo);							
		$this->set('conceptos',$this->paginate(null, $condiciones));
	}
	
	function add($tipo=6){
		if(!empty($this->data)):
			if($this->BancoConcepto->guardar($this->data)):
				$this->Mensaje->okGuardar();
			else:
				$this->Mensaje->errorGuardar();
			endif;
			$tipo = 7;
			if($this->data['BancoConcepto']['tipo'] != 7) $tipo = 6;
			$this->redirect('index/' . $tipo);
		endif;
		$this->set('tipo', $tipo);							
	}
	
	
	function del($id = null){
		if(empty($id)) $this->redirect('index');
		$this->data = $this->BancoConcepto->read(null,$id);
		$tipo = $this->data['BancoConcepto']['tipo'];
		if (!$this->BancoConcepto->del($id))$this->Mensaje->errorBorrar();
		$this->redirect('index/' . $tipo);			
	}
	
	
	function edit($id = null){
		if(empty($id)) $this->redirect('index');
		
		if(!empty($this->data)):
			if($this->BancoConcepto->save($this->data)):
				$this->Mensaje->okGuardar();
			else:
				$this->Mensaje->errorGuardar();
			endif;
			$tipo = 7;
			if($this->data['BancoConcepto']['tipo'] != 7) $tipo = 6;
			$this->redirect('index/' . $tipo);
		endif;
		$this->data = $this->BancoConcepto->read(null,$id);
	}	
	
	function cmbCncCaja(){
		return $this->BancoConcepto->combo('CAJA');
	}

	function cmbCncBanco(){
		return $this->BancoConcepto->combo('BANCO');
	}

}


?>