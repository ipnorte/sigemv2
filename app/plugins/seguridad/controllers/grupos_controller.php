<?php

class GruposController extends SeguridadAppController {  
	var $name = 'Grupos';
	var $uses = array('Seguridad.Grupo','Seguridad.Permiso');  

	
	function beforeFilter(){  
		$this->Seguridad->allow('getList','look_unlook');
		parent::beforeFilter();  
	}	
	
	/**
	 * getList
	 * Metodo para armar un combo
	 * @return unknown
	 */
	function getList(){
		$this->Seguridad->allow('getList');
		$grupos = $this->Grupo->find('list',array('conditions'=>array('Grupo.activo'=>'=1'),'fields' => array('nombre'), 'order' => 'nombre'));
		return $grupos;
	}
	
	/**
	 * permisos
	 * metodo para administrar los permisos del grupo
	 * @param unknown_type $id
	 */
	function permisos($id = null) {
		
		if (!$id) {
			$this->redirect(array('action'=>'index'));
		}
		
		$this->Grupo->id = $id;
		
		if(!empty($this->params['named'])){
			$accion = $this->params['named']['action'];
			$uid = $this->params['named']['uid'];
			
			$this->Auditoria->log();
			
			if($accion=='denegar'){
				if($uid == 'all')$this->Grupo->denegarPermiso();
				else $this->Grupo->denegarPermiso($uid);
			}
			if($accion=='autorizar'){
				if($uid == 'all')$this->Grupo->establecerPermiso();
				else $this->Grupo->establecerPermiso($uid);
				
			}			
		}
		
		

		$this->set('habilitados',$this->Grupo->permisosHabilitados());
		$this->set('denegados',$this->Grupo->permisosDenegados());
		$this->set('usuarios',$this->Grupo->usuarios());
		$this->set('id',$id);
		$this->set('nombre',$this->Grupo->nombre());
        
        
        $this->set('permisos',$this->Grupo->get_permisos($id));
//        $this->render('permisos_new');
		
	}
	
	function index(){
		$this->Grupo->recursive = 0;
		$this->set('grupos', $this->paginate());		
	}
	
	
	function edit($id=null){
		if(!$id && empty($this->data)) $this->redirect(array('action'=>'index'));
		if (!empty($this->data)) {
			if ($this->Grupo->save($this->data)){
				$this->Auditoria->log();
				$this->Mensaje->okGuardar();
				$this->redirect(array('action'=>'index'));				
			}else{
				$this->Mensaje->errorGuardar();
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Grupo->read(null,$id);
		}
	}	

	function add(){
		if (!empty($this->data)){
			if ($this->Grupo->save($this->data)){
				$this->Auditoria->log();
				if(!empty($this->data['Grupo']['grupo_permiso']))$this->Grupo->replicarPermisos($this->data['Grupo']['grupo_permiso']);
				$this->Mensaje->okGuardar();
				$this->redirect(array('action'=>'index'));				
			}else{
				$this->Mensaje->errorGuardar();
			}			
		}
		$this->set('grupos',$this->getList());		
	}
	
	function del($id = null) {
		if (!$id) {
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Grupo->del($id)) {
			$this->Auditoria->log();
			$this->Mensaje->okBorrar();
			$this->redirect(array('action'=>'index'));
		}else{
			$this->Mensaje->errorBorrar();
			$this->redirect(array('action'=>'index'));
		}
	}	
	
    
    function look_unlook($permiso_id,$grupo_id,$option){
        Configure::write('debug',0);
        $this->Grupo->id = $grupo_id;
        if($option == 0) $this->Grupo->denegarPermiso($permiso_id);
        if($option == 1) $this->Grupo->establecerPermiso($permiso_id);
        echo $option;
        exit;        
    }
    
    
}  
?>