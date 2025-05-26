<?php
class Producto extends V1AppModel{
    
	var $name = 'Producto';
	var $primaryKey = 'codigo_producto';
	var $useTable = 'proveedores_productos';

	
// 	function read($fields,$id){
// 		App::import('Model', 'V1.ProveedorV1');
// 		$this->ProveedorV1 = new ProveedorV1(null);
// 		$prod = parent::read($fields,$id);
// 		$prov = $this->ProveedorV1->read(null,$prod['Producto']['codigo_proveedor']);
// 		$prod['Producto']['Proveedor'] = $prov['ProveedorV1'];
// 		return $prod;
// 	}
	
	public function getProducto($codigoProducto){
	    $sql = "
                SELECT pp.*
                FROM mutualam_amandb.proveedores_productos pp
                where pp.activo = 1 and pp.codigo_producto = '$codigoProducto';
            ";
	    $producto = $this->query($sql);
	    return (!empty($producto) ? $producto[0] : NULl);
	}
	
	function setReasignable($id){
		$this->id = $id;
		return parent::saveField("reasignable",1);
	}

	function unsetReasignable($id = null){
		if(!empty($id)):
			$this->id = $id;
			return parent::saveField("reasignable",0);
		else:
			return parent::updateAll(array('Producto.reasignable' => 0),array("1=1"));
		endif;
	}
	
	
	function getListProductosReasignables(){
		$list = array();
		$sql = "select 
					Proveedor.razon_social,
					ProveedorProducto.codigo_producto,
					ProveedorProducto.descripcion
				from proveedores_productos as ProveedorProducto
				inner join proveedores as Proveedor on (Proveedor.codigo_proveedor = ProveedorProducto.codigo_proveedor)
				where ProveedorProducto.activo = 1 and reasignable = 1
				order by Proveedor.razon_social,ProveedorProducto.descripcion;";
		$datos = $this->query($sql);
		if(empty($datos)) return $list;
		foreach($datos as $dato):
			$list[$dato['ProveedorProducto']['codigo_producto']] = $dato['Proveedor']['razon_social'] . " | #".$dato['ProveedorProducto']['codigo_producto']." " . $dato['ProveedorProducto']['descripcion'];
		endforeach;
		return $list;
	}

	function getProductosActivosByProveedor($codigoProveedor) {
	    $sql = "
                SELECT pp.* 
                FROM mutualam_amandb.proveedores_productos pp
                where pp.activo = 1 and pp.codigo_proveedor = '$codigoProveedor' order by pp.descripcion;
            ";
// 	    $productos = $this->find('all',array('conditions' => array('Producto.codigo_proveedor' => $codigoProveedor,'Producto.activo' => 1),'order' => array('Producto.descripcion')));
	    return $this->query($sql);
	}
	
	function getListProductosActivos(){
		$list = array();
		$sql = "select 
					Proveedor.razon_social,
					ProveedorProducto.codigo_producto,
					ProveedorProducto.descripcion
				from proveedores_productos as ProveedorProducto
				inner join proveedores as Proveedor on (Proveedor.codigo_proveedor = ProveedorProducto.codigo_proveedor)
				where ProveedorProducto.activo = 1
				order by Proveedor.razon_social,ProveedorProducto.descripcion;";
		$datos = $this->query($sql);
		if(empty($datos)) return $list;
		foreach($datos as $dato):
//			$list[$dato['ProveedorProducto']['codigo_producto']] = $dato['Proveedor']['razon_social'] . " | #".$dato['ProveedorProducto']['codigo_producto']." " . $dato['ProveedorProducto']['descripcion'];
			$list[$dato['ProveedorProducto']['codigo_producto']] = array($dato['Proveedor']['razon_social'], "#".$dato['ProveedorProducto']['codigo_producto']." " . $dato['ProveedorProducto']['descripcion']);
		endforeach;
		return $list;
	}
	
	/**
	 * Valida y devuelve un array con las opciones para el formulario de reasignacion
	 * 
	 * @author adrian [17/02/2012]
	 * @param unknown_type $solicitud
	 */
	function validarProductoReasignable($solicitud){
		
		$response = array('ERROR' => 0, 'MENSAJE' => null, 'PROVEEDORES' => array());
		
		##########################################################################################################
		# CONTROL ESTADO DE LA SOLICITUD
		##########################################################################################################
		if($solicitud['Solicitud']['estado'] == 14 || $solicitud['Solicitud']['estado'] == 19){
			$response = array('ERROR' => 1, 'MENSAJE' => "LA SOLICITUD SE ENCUENTRA " . strtoupper($solicitud['Solicitud']['estado_descripcion']), 'PROVEEDORES' => array());
			return $response;
		}
		
		App::import('Model','config.GlobalDato');
		$oGLOBAL = new GlobalDato();			
		
		$globales = $oGLOBAL->find('all',array('conditions' => array('GlobalDato.id LIKE' => 'PROVREAS%','GlobalDato.concepto_2' => $solicitud['Solicitud']['codigo_producto'])));

		##########################################################################################################
		# CONTROL GENERAL DE DATOS SETEADOS
		##########################################################################################################
		if(empty($globales)){
			
			$response = array('ERROR' => 1, 'MENSAJE' => "LA SOLICITUD #". $solicitud['Solicitud']['nro_solicitud']." NO PUEDE SER REASIGNADA [PRODUCTO: ".$solicitud['Solicitud']['proveedor_producto']." *** NO HABILITADO PARA REASIGNAR ***]", 'PROVEEDORES' => array());
			return $response;
						
		}else{
			
			##########################################################################################################
			# CONTROL DE USUARIO
			##########################################################################################################
			$name_user_logon = (isset($_SESSION[$this->keyNameUserLogon]) ? $_SESSION[$this->keyNameUserLogon] : null);
			$users = Set::extract("/GlobalDato/texto_1",$globales);
			
			if(empty($users)){
				$response = array('ERROR' => 1, 'MENSAJE' => "NO EXISTEN USUARIOS HABILITADOS PARA ESTA OPERACION", 'PROVEEDORES' => array());
				return $response;
			}
			
			$autorizado = false;
			
			foreach($users as $user){
				$aUser = unserialize($user);
				if(in_array($name_user_logon, $aUser)){
					$autorizado = true;
					break;
				}
			}
			if(!$autorizado){
				$response = array('ERROR' => 1, 'MENSAJE' => "EL USUARIO [$name_user_logon] NO ESTA HABILITADO PARA ESTA OPERACION", 'PROVEEDORES' => array());
				return $response;
			}			
			##########################################################################################################
			# ARMO DATOS PARA EL FORMULARIO DE REASIGNACION
			##########################################################################################################
			$proveedorIDs = Set::extract("/GlobalDato/entero_1",$globales);
			App::import('Model','proveedores.Proveedor');
			$oPROVEEDOR = new Proveedor();
			$oPROVEEDOR->unbindModel(array('hasMany' => array('MutualProducto','MutualServicio')));
			$response['PROVEEDORES'] = $oPROVEEDOR->find('list',array('conditions' => array('Proveedor.id' => $proveedorIDs),'fields' => array('Proveedor.razon_social'),'order' => array('Proveedor.razon_social')));			

		}
		
		return $response;
		
	}
	
	
	public function copiarCuotas($codigoProveedor,$codigoProducto,$planId) {
	    $status = array(0,"OK",NULL);
	    $SPCALL = "CALL `mutualam_amandb`.`SP_COPIAR_CUOTAS`('$codigoProveedor', '$codigoProducto',$planId);";
	    $this->query($SPCALL);
	    if(!empty($this->getDataSource()->error)){
	        $status[0] = 1;
	        $status[1] = $this->getDataSource()->error;	        
	    }
	    return $status;
	}
	
}
?>