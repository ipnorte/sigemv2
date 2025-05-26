<?php

/**
*
* mutual_tipo_asientos_controller.php
* @author adrian [* 19/07/2012]
*/

class MutualTipoAsientosController extends MutualAppController{
	
	var $name = 'MutualTipoAsientos';
	var $uses = array('mutual.MutualTipoAsiento');
	
	function index(){
		$this->set('tipos',$this->MutualTipoAsiento->cargar());
	}
	
	
	function add(){
		if(!empty($this->data)){
			if($this->MutualTipoAsiento->guardar($this->data)){
				$this->Mensaje->ok("TIPO DE ASIENTO GENERADO CORRECTAMENTE!");
				$this->redirect('index');
			}else{
				$this->Mensaje->error("SE PRODUJO UN ERROR AL GUARDAR EL TIPO DE ASIENTO");
			}
		}
	}

	function del($id = null){
		if(empty($id)) $this->redirect('index');
		if(!$this->MutualTipoAsiento->borrar($id)){
			$this->Mensaje->error("NO SE PUDO BORRAR EL TIPO DE ASIENTO");
		}else{
			$this->Mensaje->ok("TIPO DE ASIENTO BORRADO CORRECTAMENTE!");
		}
		$this->redirect('index');
	}
	
	
	function edit($id = null){
		if(empty($id)) $this->redirect('index');
		if(!empty($this->data)){
			if($this->MutualTipoAsiento->guardar($this->data)){
				$this->Mensaje->ok("TIPO DE ASIENTO GUARDADO CORRECTAMENTE!");
				$this->redirect('index');
			}else{
				$this->Mensaje->error("SE PRODUJO UN ERROR AL GUARDAR EL TIPO DE ASIENTO");
			}
		}
		
		$this->set('tipo',$this->MutualTipoAsiento->cargar($id));
	}
	
	
	function vincular($action = null, $id = null){
		
		$showFormEdit = 0;
		
		App::import('Model','mutual.MutualCuentaAsiento');
		$oVINC = new MutualCuentaAsiento();		
		
		$vinculos = $oVINC->cargarVinculos();
		$vinculo = null;
		$tiposAsientos = null;
		
		if(!empty($id)){
			
			$vinculo = $oVINC->getVinculo($id);
			if($action == 'edit'){
				$showFormEdit = 1;
				$tiposAsientos = $this->MutualTipoAsiento->cargarList();
				if(!empty($this->data)){
					if($oVINC->vincular($this->data)){
						$this->Mensaje->ok("VINCULACION GUARDADA CORRECTAMENTE");
						$this->redirect('vincular');
					}else{
						$this->Mensaje->error("ERROR AL GUARDAR LA VINCULACION");
					}
				}
				
			}else if($action == 'drop'){
				$oVINC->desvincular($id);
				$this->redirect('vincular');
			}
		}
		
		$this->set('tiposAsientos',$tiposAsientos);
		$this->set('showFormEdit',$showFormEdit);
		$this->set('vinculos',$vinculos);
		$this->set('vinculo',$vinculo);
		
	}
	
}


?>