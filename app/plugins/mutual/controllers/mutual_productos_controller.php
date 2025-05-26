<?php
class MutualProductosController extends MutualAppController{
	
	var $name = 'MutualProductos';
	
//	var $tiposOrdenDto = array(
//		'CMUTU' => 'CMUTU - CARGOS MUTUAL',
//		'EXPTE' => 'EXPTE - EXPEDIENTE',
//		'OCOMP' => 'OCOMP - ORDEN DE COMPRA / SERVICIO',
//	);
	
	
	var $autorizar = array('combo','by_proveedor','productos');
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}		
	
	function index(){
		$this->set('productos', $this->MutualProducto->getListaProductos());
	}
	
	
	function by_proveedor($proveedor_id){
		$this->set('proveedor', $this->MutualProducto->Proveedor->read(null,$proveedor_id));
		$this->MutualProducto->unbindModel(array('belongsTo' => array('Proveedor')));
		$datos = $this->MutualProducto->find('all',array('conditions' => array('MutualProducto.activo' => 1,'MutualProducto.proveedor_id' => $proveedor_id)));
		$this->set('productos', $datos);
		$this->render(null,'ajax');
	}
	
	function combo(){
		$this->set('productos', $this->requestAction('/proveedores/proveedores/proveedores_productos'));
		$this->render();
	}
	
	function add(){
		if (!empty($this->data)) {
			if ($this->MutualProducto->guardar($this->data)){
				$this->Auditoria->log();
				$this->Mensaje->okGuardar();
				$this->redirect(array('action'=>'index'));				
			}else{
				$this->Mensaje->errorGuardar();
			}		
		}
		$this->set('tipos_orden_dto',$this->tiposOrdenDto);		
	}
	
	function edit($id = null){
            if(empty($id)) $this->redirect('index');
            if (!empty($this->data)){
                if ($this->MutualProducto->guardar($this->data)){
//        $this->Auditoria->log();
                        $this->Mensaje->okGuardar();
                        $this->redirect(array('action'=>'index'));				
                }else{
                        $this->Mensaje->errorGuardar();
                }				
            }		
            $this->data = $this->MutualProducto->read(null,$id);
            $this->set('tipos_orden_dto',$this->tiposOrdenDto);	
	}
	
	function productos(){
		return $this->MutualProducto->getListaProductos();
	}
	
	
	function del($id = null){
		if(empty($id)) parent::noDisponible();
		$this->MutualProducto->del($id);
		$this->redirect(array('action'=>'index'));
		$this->Mensaje->okBorrar();
		
	}
	
}
?>