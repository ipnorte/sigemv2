<?php 

class MutualServiciosController extends MutualAppController{
	
	var $name = "MutualServicios";
	
	var $autorizar = array('getServiciosActivos');
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}
	
	
	function index(){
		if(!empty($this->data)) $this->redirect('servicios_by_proveedor/' . $this->data['MutualServicio']['proveedor_id']);
	}
	
	
	function servicios_by_proveedor($proveedor_id = null){
		
		if(empty($proveedor_id)) parent::noDisponible();
		$this->set('proveedor_id',$proveedor_id);
		
		$this->set('servicios',$this->MutualServicio->getServiciosByProveedor($proveedor_id));
		
	}	

	
	function estado($id = null){
		if(empty($id)) parent::noDisponible();
		$servicio = $this->MutualServicio->read(null,$id);
		if(empty($servicio)) parent::noDisponible();
		if(!empty($this->data)):
			if($this->MutualServicio->save($this->data)) $this->redirect('servicios_by_proveedor/' . $this->data['MutualServicio']['proveedor_id']);
			else $this->Mensaje->errorGuardar();
		endif;
		$this->data = $servicio;
	}
	

	function valores($id = null){
		if(empty($id)) parent::noDisponible();
		$servicio = $this->MutualServicio->read(null,$id);
		if(empty($servicio)) parent::noDisponible();
		$this->set('servicio',$servicio);

		//cargo los valores del servicio
		App::import('Model','mutual.MutualServicioValor');
		$oSERV_VALOR = new MutualServicioValor();
		
		$this->set('valores',$oSERV_VALOR->getValores($id));
		
	}
	
	
	function valores_add($id = null){
		if(empty($id)) parent::noDisponible();
		$servicio = $this->MutualServicio->read(null,$id);
		if(empty($servicio)) parent::noDisponible();
		if(!empty($this->data)):
			$this->data['MutualServicioValor']['periodo_vigencia'] = $this->data['MutualServicioValor']['periodo_vigencia']['year'].$this->data['MutualServicioValor']['periodo_vigencia']['month'];
			$this->data['MutualServicioValor']['fecha_vigencia'] = $this->data['MutualServicioValor']['periodo_vigencia']['year']."-".$this->data['MutualServicioValor']['periodo_vigencia']['month']."-".$this->data['MutualServicioValor']['periodo_vigencia']['day'];
			App::import('Model','mutual.MutualServicioValor');
			$oSERV_VALOR = new MutualServicioValor();
			if($oSERV_VALOR->save($this->data)) $this->redirect('valores/' . $id);
			else $this->Mensaje->errorGuardar();
		endif;
		$this->set('servicio',$servicio);		
	}	
	
	function valores_del($id_valor = null){
		if(empty($id_valor)) parent::noDisponible();
		App::import('Model','mutual.MutualServicioValor');
		$oSERV_VALOR = new MutualServicioValor();
		$valor = $oSERV_VALOR->read(null,$id_valor);
		if(!$oSERV_VALOR->del($id_valor)) $this->Mensaje->errorBorrar();
		$this->redirect('valores/' . $valor['MutualServicioValor']['mutual_servicio_id']);
	}
	
    
    function add($proveedor_id = null){
        if(empty($proveedor_id)) parent::noDisponible();
        
        if(!empty($this->data)){
            if($this->MutualServicio->guardar($this->data)){
                $this->redirect("servicios_by_proveedor/$proveedor_id");
            }else{
                $this->Mensaje->errorGuardar();
            }
        }
        
        $this->set('proveedor_id',$proveedor_id);
    }
    
	
}

?>