<?php
class ProveedorVencimientosController extends ProveedoresAppController{
	var $name = 'ProveedorVencimientos';
	
	var $autorizar = array('grilla_by_organismo','edita_masivo_organismo','borrar_masivo_organismo','arma_vencimiento','calculaVto');
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}		
	
	function index(){
		$vtos = null;
		
		$orgSel = 'MUTUCORG2201';
		$mesSel = date('m');
		
		
//		debug($this->params['url']['data']);
		
		if (!empty($this->params['url']['data'])){
			
			$vtos = $this->ProveedorVencimiento->find('all',array(
																'conditions' => array(
																					'ProveedorVencimiento.codigo_organismo' => $this->params['url']['data']['ProveedorVencimiento']['codigo_organismo'],
																					'ProveedorVencimiento.mes' => $this->params['url']['data']['ProveedorVencimiento']['mes']['month']
																					),
																'order' => array('Proveedor.razon_social'),		
																)
																
														);
			$orgSel = $this->params['url']['data']['ProveedorVencimiento']['codigo_organismo'];
			$mesSel = $this->params['url']['data']['ProveedorVencimiento']['mes']['month'];
														
		}
		$this->set('orgSel',$orgSel);
		$this->set('mesSel',$mesSel);
		$this->set('vtos',$vtos);
		
	}
	
	function add(){
		
		if(!empty($this->data)){
			
			// debug($this->data);
			
			if(isset($this->data['ProveedorVencimiento']['mes'])){
				
				if(isset($this->data['Proveedor']['proveedor_id'])){
					
					$this->Auditoria->log();
					
					$meses = $this->data['ProveedorVencimiento']['mes'];
					$proveedores = $this->data['Proveedor']['proveedor_id'];
					
					foreach($meses as $mes => $selM){
						
						foreach($proveedores as $proveedor_id => $selP){
							
							$this->data['ProveedorVencimiento']['mes'] = $mes;
							$this->data['ProveedorVencimiento']['proveedor_id'] = $proveedor_id;
							
							$this->ProveedorVencimiento->save($this->data);
							$this->ProveedorVencimiento->id = 0;
						}
						
					}
					
					$this->redirect('index');
					
				}else{
					
					$this->Mensaje->error("Debe Seleccionar al menos un Proveedor!");
					
				}
				
								
				
			}else{
				$this->Mensaje->error("Debe Seleccionar al menos un Mes!");
			}
		}
		$this->render('add_meses_organismos');
	}
	
	
	function edit($id=null){
		
		if (!$id)$this->redirect(array('action'=>'index'));
		
		if(!empty($this->data)){
			
//			debug($this->data);
			
			if(isset($this->data['ProveedorVencimiento']['aplicar_atodoslosmeses'])){
				
				$this->ProveedorVencimiento->updateAll(array(
																'ProveedorVencimiento.d_corte' => $this->data['ProveedorVencimiento']['d_corte'],
																'ProveedorVencimiento.d_vto_socio' => $this->data['ProveedorVencimiento']['d_vto_socio'],
																'ProveedorVencimiento.m_ini_socio_ac_suma' => $this->data['ProveedorVencimiento']['m_ini_socio_ac_suma'],
																'ProveedorVencimiento.m_ini_socio_dc_suma' => $this->data['ProveedorVencimiento']['m_ini_socio_dc_suma'],
																'ProveedorVencimiento.m_vto_socio_suma' => $this->data['ProveedorVencimiento']['m_vto_socio_suma'],
																'ProveedorVencimiento.d_vto_proveedor_suma' => $this->data['ProveedorVencimiento']['d_vto_proveedor_suma']
															),													
														array(
																'ProveedorVencimiento.proveedor_id' => $this->data['ProveedorVencimiento']['proveedor_id'],
																'ProveedorVencimiento.codigo_organismo' => $this->data['ProveedorVencimiento']['codigo_organismo'],
															)
														);				
				$this->Auditoria->log();
				$this->Mensaje->okGuardar();
				$this->redirect('index/?data[ProveedorVencimiento][codigo_organismo]='.$this->data['ProveedorVencimiento']['codigo_organismo'].'&data[ProveedorVencimiento][mes][month]='.$this->data['ProveedorVencimiento']['mes']);
														
			}else if($this->ProveedorVencimiento->save($this->data)){
				$this->Auditoria->log();
				$this->Mensaje->okGuardar();
				$this->redirect('index/?data[ProveedorVencimiento][codigo_organismo]='.$this->data['ProveedorVencimiento']['codigo_organismo'].'&data[ProveedorVencimiento][mes][month]='.$this->data['ProveedorVencimiento']['mes']);
			}else{
				$this->Mensaje->errorGuardar();
			}
		}
		$this->data = $this-> ProveedorVencimiento->read(null,$id);
//		$proveedores = $this->ProveedorVencimiento->find('all',array(
//																'conditions' => array(
//																					'ProveedorVencimiento.codigo_organismo' => $this->data['ProveedorVencimiento']['codigo_organismo'],
//																					'ProveedorVencimiento.mes' => $this->data['ProveedorVencimiento']['mes']['month']
//																					),
//																'fields' => array('Proveedor.id'),					
//																'order' => array('Proveedor.razon_social'),		
//																)
//															);	
//		$proveedores = Set::extract($proveedores,'{n}.Proveedor.id');														
//		$this->set('proveedores',$proveedores);
		$this->set('id',$id);
	}	
	
	function del($id=null){
		if (!$id)$this->redirect(array('action'=>'index'));
		$vto = $this->ProveedorVencimiento->read('codigo_organismo,mes',$id);
		if ($this->ProveedorVencimiento->del($id)) {
			$this->Auditoria->log();
			$this->Mensaje->okBorrar();
			$this->redirect(array('action'=>'index/?data[ProveedorVencimiento][codigo_organismo]='.$vto['ProveedorVencimiento']['codigo_organismo'].'&data[ProveedorVencimiento][mes][month]='.$vto['ProveedorVencimiento']['mes']));
		}else{
			$this->Mensaje->errorBorrar();
			$this->redirect(array('action'=>'index/?data[ProveedorVencimiento][codigo_organismo]='.$vto['ProveedorVencimiento']['codigo_organismo'].'&data[ProveedorVencimiento][mes][month]='.$vto['ProveedorVencimiento']['mes']));
		}		
	}
	
	function view(){
		
	}
	
	function edita_masivo_organismo($organismo,$mes){
		
		
		if(!empty($this->data)){
			
			
			if(isset($this->data['ProveedorVencimiento']['aplicar_atodoslosmeses'])){
				
				$this->ProveedorVencimiento->updateAll(array(
																'ProveedorVencimiento.d_corte' => $this->data['ProveedorVencimiento']['d_corte'],
																'ProveedorVencimiento.d_vto_socio' => $this->data['ProveedorVencimiento']['d_vto_socio'],
																'ProveedorVencimiento.m_ini_socio_ac_suma' => $this->data['ProveedorVencimiento']['m_ini_socio_ac_suma'],
																'ProveedorVencimiento.m_ini_socio_dc_suma' => $this->data['ProveedorVencimiento']['m_ini_socio_dc_suma'],
																'ProveedorVencimiento.m_vto_socio_suma' => $this->data['ProveedorVencimiento']['m_vto_socio_suma'],
																'ProveedorVencimiento.d_vto_proveedor_suma' => $this->data['ProveedorVencimiento']['d_vto_proveedor_suma']
															),													
														array(
																'ProveedorVencimiento.codigo_organismo' => $this->data['ProveedorVencimiento']['codigo_organismo'],
															)
														);
				
			}else{
				
				$this->ProveedorVencimiento->updateAll(array(
																'ProveedorVencimiento.d_corte' => $this->data['ProveedorVencimiento']['d_corte'],
																'ProveedorVencimiento.d_vto_socio' => $this->data['ProveedorVencimiento']['d_vto_socio'],
																'ProveedorVencimiento.m_ini_socio_ac_suma' => $this->data['ProveedorVencimiento']['m_ini_socio_ac_suma'],
																'ProveedorVencimiento.m_ini_socio_dc_suma' => $this->data['ProveedorVencimiento']['m_ini_socio_dc_suma'],
																'ProveedorVencimiento.m_vto_socio_suma' => $this->data['ProveedorVencimiento']['m_vto_socio_suma'],
																'ProveedorVencimiento.d_vto_proveedor_suma' => $this->data['ProveedorVencimiento']['d_vto_proveedor_suma']
															),													
														array(
																'ProveedorVencimiento.codigo_organismo' => $this->data['ProveedorVencimiento']['codigo_organismo'],
																'ProveedorVencimiento.mes' => $this->data['ProveedorVencimiento']['mes']
															)
														);
				
			}
			
			$this->redirect('index/?data[ProveedorVencimiento][codigo_organismo]='.$this->data['ProveedorVencimiento']['codigo_organismo'].'&data[ProveedorVencimiento][mes][month]='.$this->data['ProveedorVencimiento']['mes']);										
		}
		$this->set('organismo',$organismo);
		$this->set('mes',$mes);
	}
	
	function borrar_masivo_organismo($organismo,$mes=null){
		if(!empty($mes)){
			$this->ProveedorVencimiento->deleteAll(
												array(
														'ProveedorVencimiento.codigo_organismo' => $organismo,
														'ProveedorVencimiento.mes' => $mes
													)			
			);
		}else{
			$this->ProveedorVencimiento->deleteAll(
												array(
														'ProveedorVencimiento.codigo_organismo' => $organismo,
													)			
			);
		}
		$this->redirect('index');
	}
	
	function grilla_by_organismo($codigo_organismo){
		$vtos = $this->ProveedorVencimiento->find('all',array('conditions' => array('ProveedorVencimiento.codigo_organismo' => $codigo_organismo),'order' => array('Proveedor.razon_social')));
		$this->set('vtos',$vtos);
		$this->render();
	}
	
	function arma_vencimiento($proveedor_id,$beneficio_id,$fecha){
		$this->set('vto',$this->ProveedorVencimiento->calculaVencimiento($proveedor_id,$beneficio_id,$fecha));
		$this->set('fecha_carga',date('Y-m-d',strtotime($fecha)));
		$this->render(null,'ajax');
	}
	
}
?>