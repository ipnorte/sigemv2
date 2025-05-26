<?php
class ProveedoresController extends ProveedoresAppController{
	
	var $name = 'Proveedores';
	
	var $autorizar = array('sel_productos','proveedores_con_producto','view','combo','proveedores','proveedores_productos_mensuales','proveedores_cancelacion','proveedores_list','get_razon_social','get_proveedor','proveedores_servicios_activos','proveedores_productos','proveedores_reasignables_list','proveedores_liquidados_list','genwspin','prioridad_imputacion_update');
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	
	
	
	function index($id = null){
		$tipoProveedor = "";
                $condiciones = array();
		if(empty($id)){
                    
                    if(!empty($this->data)){
                            $this->Session->del($this->name.'.search');
                            $search = $this->data;
                            $condiciones = array(
                                                                    'Proveedor.cuit  LIKE ' => $search['Proveedor']['cuit'] ."%",
                                                                    'Proveedor.razon_social LIKE ' => "%" . $search['Proveedor']['razon_social']."%",
                                                            );				

                    }else if($this->Session->check($this->name.'.search')){

                            $search = $this->Session->read($this->name.'.search');
                            $this->data = $search;

                    }                    
                    
                    $this->paginate = array(
                        'limit' => 50,
                        'order' => array('Proveedor.razon_social' => 'ASC')
                    );

                    $this->set('tipoProveedor', $tipoProveedor);			
                    $this->set('proveedores', $this->paginate(null, $condiciones));			
                    $this->render('index');		
		}else{
			$this->redirect('edit/'.$id);
		}
		
	}
	
	function add(){
		if(!empty($this->data)):
			$id = $this->Proveedor->grabar($this->data);
			if($id != 0):
				$this->Mensaje->okGuardar();
			   	$this->redirect('edit/'.$id);
			else:
	    		$this->Mensaje->errorGuardar();
			endif;
//			$aFactura = array();
//			$aFactura['tipo'] = $this->data['Proveedor']['tipo_saldo'] == 0 ? 'SA' : 'SD';
//			$aFactura['fecha_comprobante'] = $this->data['Proveedor']['fecha_saldo'];
//			$aFactura['vencimiento1'] = $this->data['Proveedor']['fecha_saldo'];
//			$aFactura['tipo_comprobante'] = 'SALDOPROVEED';
//			$aFactura['importe_no_gravado'] = $this->data['Proveedor']['importe_saldo']; 
//			$aFactura['total_comprobante'] = $this->data['Proveedor']['importe_saldo']; 
//			$aFactura['importe_venc1'] = $this->data['Proveedor']['importe_saldo']; 
//			$aFactura['estado'] = 'A'; 
//			
//			$this->Proveedor->begin();
//			if($this->Proveedor->save($this->data)):
//				$id = $this->Proveedor->getLastInsertID();
//				
//				if($this->data['Proveedor']['importe_saldo'] != 0):
//					$aFactura['proveedor_id'] = $id;
//    				App::import('Model','Proveedores.ProveedorFactura');
//    				$oFactura = new ProveedorFactura();	
//		    		if($oFactura->save($aFactura)):
//		    			$this->data['Proveedor']['factura_id'] = $oFactura->getLastInsertID();
//						if($this->Proveedor->save($this->data)):
//							$this->Proveedor->commit();
//				    		$this->Mensaje->okGuardar();
//					    	$this->redirect('edit/'.$id);
//						else:
//							$this->Proveedor->rollback();
//							$this->Mensaje->errorGuardar();
//						endif;
//					else:
//						$this->Proveedor->rollback();
//						$this->Mensaje->errorGuardar();
//					endif;
//				else:
//					$this->Proveedor->commit();
//					$this->Mensaje->okGuardar();
//				   	$this->redirect('edit/'.$id);
//				endif;
//		    else:
//				$this->Proveedor->rollback();
//		    	$this->Mensaje->errorGuardar();
//			endif;
		endif;
	}
	
	function edit($id = null){
		if(!empty($this->data)){
			if($this->Proveedor->grabar($this->data)):
				$this->Mensaje->okGuardar();
//			   	$this->redirect('edit/'.$id);
			else:
	    		$this->Mensaje->errorGuardar();
			endif;
			$id = $this->data['Proveedor']['id'];
//			$aFactura = array();
//			
//			if($this->data['Proveedor']['proveedor_factura_id'] != 0) $aFactura['id'] = $this->data['Proveedor']['proveedor_factura_id'];
//
//			$aFactura['proveedor_id'] = $this->data['Proveedor']['id'];
//			$aFactura['tipo'] = $this->data['Proveedor']['tipo_saldo'] == 0 ? 'SD' : 'SA';
//			$aFactura['fecha_comprobante'] = $this->data['Proveedor']['fecha_saldo'];
//			$aFactura['vencimiento1'] = $this->data['Proveedor']['fecha_saldo'];
//			$aFactura['tipo_comprobante'] = 'SALDOPROVEED';
//			$aFactura['importe_no_gravado'] = $this->data['Proveedor']['importe_saldo']; 
//			$aFactura['total_comprobante'] = $this->data['Proveedor']['importe_saldo']; 
//			$aFactura['importe_venc1'] = $this->data['Proveedor']['importe_saldo']; 
//
//			$this->Proveedor->begin();
//			if($this->Proveedor->save($this->data)){
//				
//    			App::import('Model','Proveedores.ProveedorFactura');
//    			$oFactura = new ProveedorFactura();	
//		    	if($oFactura->save($aFactura)){
//		    		if(empty($this->data['Proveedor']['proveedor_factura_id'])){
//			    		$this->data['Proveedor']['proveedor_factura_id'] = $oFactura->getLastInsertID();
//		    			
//			    		if($this->Proveedor->save($this->data)){
//							$this->Proveedor->commit();
//							$this->Mensaje->okGuardar();
//					    	$this->redirect('edit/'.$id);
//						}else{
//							$this->Proveedor->rollback();
//							$this->Mensaje->errorGuardar();
//						}
//		    		}else{
//						$this->Proveedor->commit();
//						$this->Mensaje->okGuardar();
//					   	$this->redirect('edit/'.$id);
//		    		}
//				}else{
//					$this->Proveedor->rollback();
//					$this->Mensaje->errorGuardar();
//				}
//		    }else{
//				$this->Proveedor->rollback();
//		    	$this->Mensaje->errorGuardar();
//			}
		}		
		if(empty($id)) parent::noAutorizado();
		$this->data = $this->Proveedor->read(null,$id);
		
//		if(!empty($this->data['Proveedor']['proveedor_factura_id'])):
//			App::import('Model','Proveedores.ProveedorFactura');
//    		$oFactura = new ProveedorFactura();
//    		$estado = $oFactura->read('estado',$this->data['Proveedor']['proveedor_factura_id']);
//    		$estado = $estado['ProveedorFactura']['estado'];
//    	else:
//    		$estado = 'A';
//    	endif;
    	$this->data['Proveedor']['estado'] = $this->Proveedor->getEstado($this->data);
	}
	

	function sel_productos(){
		$combo = array();
		$proveedores = $this->Proveedor->find('all',array('conditions' => array('Proveedor.activo' => 1),'order' => 'Proveedor.razon_social'));
		foreach($proveedores as $proveedor){
			if(!empty($proveedor['MutualProducto']))$combo[$proveedor['Proveedor']['id']] = $proveedor['Proveedor']['razon_social'];
		}
		$this->set('proveedores',$combo);
		$this->render();
	}
	
	function proveedores_con_producto(){
		$datos = array();
		$this->Proveedor->recursive = 2;
		$proveedores = $this->Proveedor->find('all',array('conditions' => array('Proveedor.activo' => 1),'order' => 'Proveedor.razon_social'));
		return $proveedores;		
	}
	
	function proveedores_productos_mensuales(){
		App::import('Model','Proveedores.Proveedor');
		$oProveedor = new Proveedor();
		$proveedores = $oProveedor->proveedoresProductosMensuales();
		return $proveedores;
	}
	
	function proveedores_productos($proveedor_id = null){
		App::import('Model','Proveedores.Proveedor');
		$oProveedor = new Proveedor();
		$proveedores = $oProveedor->proveedoresProductos($proveedor_id);
		return $proveedores;
	}	
	
	function proveedores_servicios_activos($soloCallCenter = 0,$soloActivos = 1){
		App::import('Model','Proveedores.Proveedor');
		$oProveedor = new Proveedor();
		$proveedores = $oProveedor->proveedoresServiciosActivos(($soloCallCenter == 0 ? false : true),($soloActivos == 1 ? true : false));
		return $proveedores;
	}	
	
	function view($id){
		$this->Proveedor->recursive = 0;
		$this->set('proveedor',$this->Proveedor->read(null,$id));
		$this->render('view_engrilla');
	}
	
	function combo($selected=0,$label='',$model='proveedor_id'){
		$proveedores = $this->Proveedor->find('list',array('conditions' => array('Proveedor.activo' => 1),'fields' => array('Proveedor.razon_social'),'order' => 'Proveedor.razon_social'));
		$this->set('proveedores',$proveedores);
		$this->set('selected',$selected);
		$this->set('label',$label);
		$this->set('model',$model);
		$this->render();
	}
	
	function proveedores($soloActivos = 0,$tipo = null, $bindMutualProducto = 1, $bindMutualServicio = 1,$toList = 0){
		$conditions = array();
		if($soloActivos != 0) $conditions['Proveedor.activo'] = 1;
		if(!empty($tipo)) $conditions['Proveedor.tipo_proveedor'] = $tipo;
		if($bindMutualProducto == 0) $this->Proveedor->unbindModel(array('hasMany' => array('MutualProducto')));
		if($bindMutualServicio == 0) $this->Proveedor->unbindModel(array('hasMany' => array('MutualServicio')));
		if($toList == 0)return $this->Proveedor->find('all',array('conditions' => $conditions,'order' => 'Proveedor.razon_social'));
		else return $this->Proveedor->find('list',array('conditions' => $conditions,'fields' => array('Proveedor.razon_social'),'order' => 'Proveedor.razon_social'));
	}
	
	/**
	 * metodo que devuelve un array para armar el combo (con un element) para ser usado en el formulario
	 * de generacion de la cancelacion. El combo tiene que tener el o los proveedores de las cuotas adeudadas
	 * de una orden de descuento mas la mutual para en caso de que la cancelacion no sea en comercio.
	 * @param unknown_type $orden_descuento_id
	 */
	function proveedores_cancelacion($orden_descuento_id,$soloCuotasAdeudadas=1){
		App::import('Model','Proveedores.Proveedor');
		$oPROVEEDOR = new Proveedor();
		$proveedores = $oPROVEEDOR->proveedoresOrdenDto($orden_descuento_id,($soloCuotasAdeudadas==1 ? true : false));
		return $proveedores;
	}
	
	function proveedores_list($tipo = null){
		$conditions = array();
		if(!empty($tipo)) $conditions = array("Proveedor.tipo_proveedor" => $tipo);
		return $this->Proveedor->find('list',array('conditions' => $conditions,'fields' => array('Proveedor.razon_social'),'order' => 'Proveedor.razon_social'));
	}
	
	
	function get_razon_social($id){
		$proveedor = $this->Proveedor->read(null,$id);
		return $proveedor['Proveedor']['razon_social'];
	}
	
	function proveedores_reasignables_list($codigoProductoSolicitud){
		$configs = $this->requestAction('/config/global_datos/get_config_reasignacion_proveedores/' . $codigoProductoSolicitud);
		$configs = Set::extract("/GlobalDato/concepto_3",$configs);
		return $this->Proveedor->find('list',array('conditions' => array('Proveedor.reasignable' => 1, 'Proveedor.cuit' => $configs),'fields' => array('Proveedor.razon_social'),'order' => 'Proveedor.razon_social'));
	}

	
	function proveedores_liquidados_list($liquidacion_id, $imputados = 0){
		
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();		
		
		$proveedores = $oLC->getProveedoresLiquidados($liquidacion_id,true);
		
		if(empty($proveedores)) return null;
		
		$list = array();
		
		foreach($proveedores as $proveedor){
			
			$list[$proveedor['LiquidacionCuota']['proveedor_id']] = $proveedor['Proveedor']['razon_social'];
			
		}
		return $list;
	}
	
	
	function comision_cobranza($proveedor_id = null,$action = null, $comision_id = null){
		
        if(empty($proveedor_id)) parent::noAutorizado();
        
		$proveedor = $this->Proveedor->read(null,$proveedor_id);		
		$this->set('proveedor',$proveedor);
		
		if($action == 'DROP'){
            if(empty($comision_id)) parent::noAutorizado();
			if($this->Proveedor->borrarComision($comision_id)){
				$this->Mensaje->ok("COMISION BORRADA CORRECTAMENTE!");
			}else{
				$this->Mensaje->error("NO SE PUDO BORRAR LA COMISION!");
			}
		}
		
		if($action == 'ADD'){
			if(!empty($this->data)){
			    if($this->Proveedor->guardarComision($this->data)) {$this->Mensaje->ok("COMISION GUARDADA CORRECTAMENTE!");}
			    else {$this->Mensaje->error("NO SE PUDO GUARDAR LA COMISION!");}
				$this->redirect('comision_cobranza/' . $this->data['ProveedorComision']['proveedor_id']);
			}
			$this->render("comision_cobranza_add");
		}
		
		$comisiones = $this->Proveedor->getComisiones($proveedor_id);
		
		$this->set('comisiones',$comisiones);
	}
	
    function genwspin($proveedorId){
        Configure::write('debug',0);
        $ln = 12 - strlen(strval($proveedorId));
        $pin = $this->Proveedor->generarPIN($ln);
        echo $pin.$proveedorId;
        exit;
    }    
	
    
    function prioridad_imputacion($id = NULL){
        if(empty($id)) parent::noDisponible();
        $this->Proveedor->bindModel(array('hasMany' => array('ProveedorPrioridadImputaOrganismo')));
        $proveedor = $this->Proveedor->read(null,$id);
        $this->set('proveedor',$proveedor);
    }
    
    
    function prioridad_imputacion_update(){
        Configure::write('debug',0);
        App::import('model','proveedores.ProveedorPrioridadImputaOrganismo');
        $oPIP = new ProveedorPrioridadImputaOrganismo();
        echo $oPIP->updateAll(
            array('ProveedorPrioridadImputaOrganismo.prioridad' => $this->data['ProveedorPrioridadImputaOrganismo']['prioridad']),
            array(
                'ProveedorPrioridadImputaOrganismo.proveedor_id' => $this->data['ProveedorPrioridadImputaOrganismo']['proveedor_id'],
                'ProveedorPrioridadImputaOrganismo.codigo_organismo' => $this->data['ProveedorPrioridadImputaOrganismo']['codigo_organismo'],
            )
        );
        exit;
    }
    
    
}
?>