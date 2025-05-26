<?php
class BancoSucursalesController extends ConfigAppController{
	var $name = 'BancoSucursales';
	
	var $autorizar = array('combo','nombre','get_sucursal');
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}

	function combo($banco_id){
		$sucursales = $this->BancoSucursal->find('list',array('conditions' => array('BancoSucursal.banco_id' => $banco_id),'fields' => 'BancoSucursal.nombre', 'order' => 'BancoSucursal.nombre'));
		$this->set('sucursales',$sucursales);
		$this->set('disabled','');	
		$this->render(null,'ajax');
	}

	function nombre($sucursal_id){
		$sucursal = $this->BancoSucursal->read(null,$sucursal_id);
		return $sucursal['BancoSucursal']['nombre'] . " (".$sucursal['BancoSucursal']['nro_sucursal'].")";
	}	
	
	
	function index($banco_id){
		$this->paginate = array(
									'limit' => 30,
									'order' => array('BancoSucursal.nombre' => 'ASC')
									);
		$this->BancoSucursal->bindModel(array('belongsTo' => array('Banco')));	

		$this->set('banco',$this->BancoSucursal->Banco->read(null,$banco_id));
		
		$this->set('sucursales',$this->BancoSucursal->find('all',array('conditions' => array('BancoSucursal.banco_id' => $banco_id),'order' => 'BancoSucursal.nro_sucursal')));		
	}
	
	
	function add($banco_id = null){
		if(empty($banco_id)) $this->redirect('index');
		
		if (!empty($this->data)){
			if ($this->BancoSucursal->save($this->data)){
				$this->Auditoria->log();
				$this->Mensaje->okGuardar();
				$this->redirect('index/'.$this->data['BancoSucursal']['banco_id']);				
			}else{
				$this->Mensaje->errorGuardar();
			}				
		}		
		
		$this->BancoSucursal->bindModel(array('belongsTo' => array('Banco')));	
		$this->set('banco',$this->BancoSucursal->Banco->read(null,$banco_id));
	}

	
	function edit($id=null){
		
		if(empty($id)) $this->redirect('index');
		if (!empty($this->data)){
			
			if ($this->BancoSucursal->save($this->data)){
				$this->Auditoria->log();
				$this->Mensaje->okGuardar();
				$this->redirect('index/'.$this->data['BancoSucursal']['banco_id']);		
			}else{
				$this->Mensaje->errorGuardar();
			}				
		}
		$this->BancoSucursal->bindModel(array('belongsTo' => array('Banco')));
		$this->data = $this->BancoSucursal->read(null,$id);		
		$this->set('banco',$this->BancoSucursal->Banco->read(null,$this->data['BancoSucursal']['banco_id']));		


	}		
	
	function del($id = null){
		if(empty($id)) $this->redirect('index');
		$bancoId = $this->BancoSucursal->field('banco_id',array('BancoSucursal.id' => $id));
		if ($this->BancoSucursal->del($id)) {
			$this->Mensaje->okBorrar();
			$this->Auditoria->log();
			$this->redirect('index/'.$bancoId);
		}else{
			$this->Mensaje->errorBorrar();
		}		
	}
        
        function get_sucursal($banco_id,$codigo_bcra){
            Configure::write('debug',0);
            $banco_id = str_pad($banco_id,5,0,STR_PAD_LEFT);
            $sucursal = $this->BancoSucursal->find('all',array('conditions' => array('BancoSucursal.banco_id' => $banco_id,'BancoSucursal.codigo_bcra' => $codigo_bcra),'fields' => 'BancoSucursal.nro_sucursal', 'limit' => 1));            
            if(!empty($sucursal)){
                echo $sucursal[0]['BancoSucursal']['nro_sucursal'];
            }else{
                echo NULL;
            }
            exit;
        }
	
}
?>