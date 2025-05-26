<?php
class OrdenCajaCobrosController extends MutualAppController{
	var $name = 'OrdenCajaCobros';
	
	var $autorizar = array(
							'index_anterior'
	);
	
	
	function index(){
		$this->OrdenCajaCobro->unbindModel(array('hasMany' => array('OrdenCajaCobroCuota')));
		$this->OrdenCajaCobro->bindModel(array('belongsTo' => array('Socio')));
		$this->OrdenCajaCobro->Socio->bindModel(array('belongsTo' => array('Persona')));
		$this->OrdenCajaCobro->recursive = 3;
		$ordenes = $this->OrdenCajaCobro->find('all',array('conditions' => array('OrdenCajaCobro.fecha_vto' => date('Y-m-d'),'OrdenCajaCobro.estado' => 'E'),'order'=> array('OrdenCajaCobro.created DESC')));
		$this->set('ordenes',$ordenes);
	}
	
	function add(){
		if(!empty($this->data)){
			#GENERO LA CABECERA
			$this->data['OrdenCajaCobro']['fecha_vto'] = date('Y-m-d');
// 			if($this->OrdenCajaCobro->generarOrdenImputaByVencido($this->data)){
			if($this->OrdenCajaCobro->generarOrdenImputaByImporte($this->data)){				
				$occ = $this->OrdenCajaCobro->cargarOrdenConCuotasDetalladas($this->OrdenCajaCobro->getLastInsertID());
				$this->set('orden',$occ);
				$this->render(null,'ajax');
			}
			
		}
		
	}
	

	function del($id = null, $cascade = true){
		if (!$id) $this->redirect(array('action'=>'index'));
		$this->OrdenCajaCobro->bindModel(array('hasMany' => array('OrdenCajaCobroCuota' => array('dependent'=> true))));
		if($this->OrdenCajaCobro->OrdenCajaCobroCuota->deleteAll('OrdenCajaCobroCuota.orden_caja_cobro_id = '. $id)){
			if ($this->OrdenCajaCobro->del($id,true)) {
				$this->Mensaje->okBorrar();
				$this->Auditoria->log();
			}else{
				$this->Mensaje->errorBorrar();
			}
		}else{
			$this->Mensaje->errorBorrar();
		}
		$this->redirect(array('action'=>'index'));		
	}
	
	function view($id=null){
		if(empty($id)) $this->redirect('index');
		$this->OrdenCajaCobro->recursive = 3;
		$this->OrdenCajaCobro->bindModel(array('hasMany' => array('OrdenCajaCobroCuota')));
		$this->OrdenCajaCobro->OrdenCajaCobroCuota->bindModel(array('belongsTo' => array('OrdenDescuentoCuota')));
		$this->OrdenCajaCobro->OrdenCajaCobroCuota->OrdenDescuentoCuota->bindModel(array('belongsTo' => array('OrdenDescuento','Proveedor')));
		$orden = $this->OrdenCajaCobro->read(null,$id);
		$this->set('orden',$orden);
		$this->render();
	}
	
	
	function index_anterior(){
		$this->OrdenCajaCobro->unbindModel(array('hasMany' => array('OrdenCajaCobroCuota')));
		$this->OrdenCajaCobro->bindModel(array('belongsTo' => array('Socio')));
		$this->OrdenCajaCobro->Socio->bindModel(array('belongsTo' => array('Persona')));
		$this->OrdenCajaCobro->recursive = 3;
		$ordenes = $this->OrdenCajaCobro->find('all',array('conditions' => array('OrdenCajaCobro.fecha_vto' => date('Y-m-d'),'OrdenCajaCobro.estado' => 'E'),'order'=> array('OrdenCajaCobro.created DESC')));
		$this->set('ordenes',$ordenes);
	}
	
	
}
?>