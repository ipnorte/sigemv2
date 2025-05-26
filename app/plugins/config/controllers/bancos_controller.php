<?php
class BancosController extends ConfigAppController{
	
	var $name = 'Bancos';
	
	var $autorizar = array('combo','nombre','info_cbu','bancos_habilitados','deco_cbu','gen_cbu','getBancosByTipo','download_diskette');
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	
	
	function index(){
		$this->paginate = array(
									'limit' => 100,
									'order' => array('Banco.nombre' => 'ASC')
									);			
		$this->set('bancos',$this->paginate());
	}
	
	
	function add(){
		if (!empty($this->data)){
			$this->data['Banco']['id'] = $this->data['Banco']['codigo'];
			if ($this->Banco->save($this->data)){
				$this->Auditoria->log();
				$this->Mensaje->okGuardar();
				$this->redirect(array('action'=>'index'));				
			}else{
				$this->Mensaje->errorGuardar();
			}				
		}	
		$this->set('metodos',$this->Banco->spGenera);		
	}

	function edit($id=null){
		
		if(empty($id)) $this->redirect('index');
		if (!empty($this->data)){
			
			if ($this->Banco->save($this->data)){
				$this->Auditoria->log();
				$this->Mensaje->okGuardar();
				$this->redirect(array('action'=>'index'));				
			}else{
				$this->Mensaje->errorGuardar();
			}				
		}		
		$this->data = $this->Banco->read(null,$id);
		$this->data['Banco']['codigo'] = $this->data['Banco']['id'];
		$this->set('metodos',$this->Banco->spGenera);
	}	
	

	function del($id = null){
		if(empty($id)) $this->redirect('index');
		$this->Banco->bindModel(array('hasMany' => array('BancoSucursal')));
		$banco = $this->Banco->read(null,$id);
		if(!empty($banco['BancoSucursal'])){
			$this->Mensaje->error("NO SE PUEDE BORRAR EL BANCO PORQUE TIENE SUCURSALES CARGADAS!.");
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Banco->del($id)) {
			$this->Mensaje->okBorrar();
			$this->Auditoria->log();
			$this->redirect(array('action'=>'index'));
		}else{
			$this->Mensaje->errorBorrar();
		}		
	}	
	
	function combo($model,$disable=0,$empty=0,$tipo=0,$label=""){
		switch ($tipo) {
			case 0:
				//todos los activos
				$bancos = $this->Banco->find('list',array('conditions' => array('Banco.activo' => 1),'fields' => 'Banco.nombre', 'order' => 'Banco.nombre'));
				break;
			case 1:
				//habilitado para beneficio solamente
				$bancos = $this->Banco->find('list',array('conditions' => array('Banco.activo' => 1,'Banco.beneficio' => 1),'fields' => 'Banco.nombre', 'order' => 'Banco.nombre'));
				break;
			case 2:
				//habilitado para forma de pago solamente
				$bancos = $this->Banco->find('list',array('conditions' => array('Banco.activo' => 1,'Banco.fpago' => 1),'fields' => 'Banco.nombre', 'order' => 'Banco.nombre'));
				break;
			case 3:
				//habilitado para beneficio y forma de pago
				$bancos = $this->Banco->find('list',array('conditions' => array('Banco.activo' => 1,'Banco.beneficio' => 1,'Banco.fpago' => 1),'fields' => 'Banco.nombre', 'order' => 'Banco.nombre'));
				break;
			case 4:
				//todos
				$bancos = $this->Banco->find('list',array('fields' => 'Banco.nombre', 'order' => 'Banco.nombre'));
				break;
			case 5:
				//habilitado para intercambio solamente
				$bancos = $this->Banco->find('list',array('conditions' => array('Banco.activo' => 1,'Banco.intercambio' => 1),'fields' => 'Banco.nombre', 'order' => 'Banco.nombre'));
				break;												
			default:
				$bancos = $this->Banco->find('list',array('conditions' => array('Banco.activo' => 1),'fields' => 'Banco.nombre', 'order' => 'Banco.nombre'));
				break;
		}
		
		$this->set('bancos',$bancos);
		$this->set('model',$model);
		$this->set('disabled',$disable);
		$this->set('empty',$empty);	
		$this->set('label',$label);	
		$this->render();
	}
	
	function nombre($banco_id=null){
		if(empty($banco_id)) return 'S/D';
		return $this->Banco->getNombreBanco($banco_id);
	}
	
	function info_cbu($cbu,$tipo=0){
		App::import('Model','Config.Banco');
		$oBanco = new Banco();
		$deco = $oBanco->deco_cbu($cbu);
		$this->set('codigo_banco',$deco['banco_id']);
		$this->set('banco',$this->nombre($deco['banco_id']));
		$this->set('sucursal',$deco['sucursal']);
		$this->set('tipo_cta_bco',$deco['tipo_cta_bco']);
		$this->set('nro_cta_bco',$deco['nro_cta_bco']);
		$this->set('check',$this->checkBanco($deco['banco_id'],$tipo));
		$this->render(null,'ajax');
	}
	
	function checkBanco($codigo,$tipo){
		switch ($tipo) {
			case 0:
				//TODOS LOS BANCOS ACTIVOS
				$bancos = $this->Banco->findCount("Banco.id = $codigo and Banco.activo = 1");
				break;
			case 1:
				//habilitado para beneficio solamente
				$bancos = $this->Banco->findCount("Banco.id = $codigo and Banco.activo = 1 and Banco.beneficio = 1");
				break;
			case 2:
				//habilitado para forma de pago solamente
				$bancos = $this->Banco->findCount("Banco.id = $codigo and Banco.activo = 1 and Banco.fpago = 1");
				break;
			case 3:
				//habilitado para beneficio y forma de pago
				$bancos = $this->Banco->findCount("Banco.id = $codigo and Banco.activo = 1 and Banco.beneficio = 1 and Banco.fpago = 1");
				break;				
				
			default:
				//TODOS LOS BANCOS ACTIVOS
				$bancos = $this->Banco->findCount("Banco.id = $codigo and Banco.activo = 1");
				break;
		}
		
		return $bancos;
		
	}
	
	
	function deco_cbu($cbu){
            Configure::write('debug',0);
            if(!isset($cbu)) return null;
            App::import('Model','Config.Banco');
            $oBanco = new Banco();
            $info = $oBanco->deco_cbu($cbu);      
            if ($this->RequestHandler->isAjax()){
                echo json_encode($info);
                exit;
            }else{
                return $info;		
            }
            
	}
	

	function gen_cbu(){
		$cbu = null;
		if(!empty($this->data)):
			$cbu = $this->Banco->genCbu($this->data['Banco']['banco_id'],$this->data['Banco']['nro_sucursal'],$this->data['Banco']['nro_cta_bco']);
		endif;
		$this->set('cbu',$cbu);
	}
	
	function getBancosByTipo($tipo=0){
		switch ($tipo) {
			case 0:
				//todos los activos
				$bancos = $this->Banco->find('list',array('conditions' => array('Banco.activo' => 1),'fields' => 'Banco.nombre', 'order' => 'Banco.nombre'));
				break;
			case 1:
				//habilitado para beneficio solamente
				$bancos = $this->Banco->find('list',array('conditions' => array('Banco.activo' => 1,'Banco.beneficio' => 1),'fields' => 'Banco.nombre', 'order' => 'Banco.nombre'));
				break;
			case 2:
				//habilitado para forma de pago solamente
				$bancos = $this->Banco->find('list',array('conditions' => array('Banco.activo' => 1,'Banco.fpago' => 1),'fields' => 'Banco.nombre', 'order' => 'Banco.nombre'));
				break;
			case 3:
				//habilitado para beneficio y forma de pago
				$bancos = $this->Banco->find('list',array('conditions' => array('Banco.activo' => 1,'Banco.beneficio' => 1,'Banco.fpago' => 1),'fields' => 'Banco.nombre', 'order' => 'Banco.nombre'));
				break;
			case 4:
				//todos
				$bancos = $this->Banco->find('list',array('fields' => 'Banco.nombre', 'order' => 'Banco.nombre'));
				break;
			case 5:
				//habilitado para intercambio solamente
				$bancos = $this->Banco->find('list',array('conditions' => array('Banco.activo' => 1,'Banco.intercambio' => 1),'fields' => 'Banco.nombre', 'order' => 'Banco.nombre'));
				break;												
			default:
				$bancos = $this->Banco->find('list',array('conditions' => array('Banco.activo' => 1),'fields' => 'Banco.nombre', 'order' => 'Banco.nombre'));
				break;
		}
		
		return $bancos;
	}	
	
	
	function download_diskette($uuid){
		if(isset($_SESSION["DISKETTE_$uuid"])){
// 		if(session_is_registered("DISKETTE_$uuid")){
			$diskette = $_SESSION["DISKETTE_$uuid"];
// 			$diskette = base64_decode($diskette);
			$diskette = unserialize($diskette);
			$this->set('diskette',$diskette['diskette']);
			$this->render(null,'blank');
			
		}else{
			
			parent::noDisponible();
		}		
		
		
	}
	
	
}
?>