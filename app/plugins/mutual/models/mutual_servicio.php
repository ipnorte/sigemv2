<?php 

class MutualServicio extends MutualAppModel{
	
	var $name = "MutualServicio";
	
	function getNombreProveedorServicio($servicio_id){
		$servicio = $this->read("proveedor_id,tipo_producto",$servicio_id);
		App::import('Model','proveedores.Proveedor');
		$oPROV = new Proveedor();
		$proveedor = $oPROV->getRazonSocial($servicio['MutualServicio']['proveedor_id']);
		return $proveedor . " - " . parent::GlobalDato("concepto_1",$servicio['MutualServicio']['tipo_producto']);
	}
	
	function getNombreProveedor($servicio_id){
		$servicio = $this->read("proveedor_id",$servicio_id);
		App::import('Model','proveedores.Proveedor');
		$oPROV = new Proveedor();
		$proveedor = $oPROV->getRazonSocial($servicio['MutualServicio']['proveedor_id']);
		return $proveedor;
	}
	
	function getProveedorID($servicio_id){
		$servicio = $this->read("proveedor_id",$servicio_id);
		return $servicio['MutualServicio']['proveedor_id'];
	}	

	function getNombreServicio($servicio_id){
		$servicio = $this->read("tipo_producto",$servicio_id);
		return parent::GlobalDato("concepto_1",$servicio['MutualServicio']['tipo_producto']);
	}	
	
	
	function getCodigoServicio($servicio_id){
		$servicio = $this->read("tipo_producto",$servicio_id);
		return $servicio['MutualServicio']['tipo_producto'];
	}	
	
	function getTipoOrdenDtoServicio($servicio_id){
		$servicio = $this->read("tipo_orden_dto",$servicio_id);
		return $servicio['MutualServicio']['tipo_orden_dto'];
	}	
	
	
	function getDatosAdicionales($servicio_id){
		$datos = array();
		$sql = "SELECT 
					MutualServicio.id,
					MutualServicio.tipo_producto,
					MutualServicio.proveedor_id,
					MutualServicio.tipo_orden_dto,
					Proveedor.razon_social,
					Proveedor.razon_social_resumida,
					CodigoProducto.concepto_1,
					CONCAT(Proveedor.razon_social,' - ',CodigoProducto.concepto_1) AS proveedor_servicio
				FROM mutual_servicios AS MutualServicio
				INNER JOIN proveedores AS Proveedor ON (Proveedor.id = MutualServicio.proveedor_id)
				INNER JOIN global_datos AS CodigoProducto ON(CodigoProducto.id = MutualServicio.tipo_producto)
				WHERE MutualServicio.id = $servicio_id;";
		$regs = $this->query($sql);
		if(empty($regs)) return $datos;
		$regs = $regs[0];
		$datos['MutualServicio']['id'] = $regs['MutualServicio']['id'];
		$datos['MutualServicio']['tipo_producto'] = $regs['MutualServicio']['tipo_producto'];
		$datos['MutualServicio']['tipo_orden_dto'] = $regs['MutualServicio']['tipo_orden_dto'];
		$datos['MutualServicio']['proveedor_razon_social'] = $regs['Proveedor']['razon_social'];
		$datos['MutualServicio']['proveedor_razon_social_resumida'] = $regs['Proveedor']['razon_social_resumida'];
		$datos['MutualServicio']['tipo_producto_descripcion'] = $regs['CodigoProducto']['concepto_1'];
		$datos['MutualServicio']['tipo_producto_descripcion_proveedor'] = $regs[0]['proveedor_servicio'];
        $datos['MutualServicio']['proveedor_id'] = $regs['MutualServicio']['proveedor_id'];
// 		debug($datos);
		return $datos;
	}
	
	function getServiciosByProveedor($proveedor_id){
		$servicios = $this->find('all',array('conditions' => array('MutualServicio.proveedor_id' => $proveedor_id)));
		if(!empty($servicios)):
			foreach($servicios as $idx => $servicio):
				$servicio['MutualServicio']['tipo_producto_desc'] = parent::GlobalDato("concepto_1",$servicio['MutualServicio']['tipo_producto']);
				$servicios[$idx] = $servicio;
			endforeach;
		endif;
		return $servicios;
	}
	
	
	function getValoresServicio($id){
		
	}
	
	function getParametrosFechaCobertura($id){
		$parametros = array();
		$servicio = $this->read(null,$id);
		if(empty($servicio)) return $parametros;
		$parametros['dia_corte'] = $servicio['MutualServicio']['dia_corte'];
		$parametros['meses_antes_dia_corte'] = $servicio['MutualServicio']['meses_antes_dia_corte']; 
		$parametros['meses_despues_dia_corte'] = $servicio['MutualServicio']['meses_despues_dia_corte'];
		$parametros['dia_alta'] = $servicio['MutualServicio']['dia_alta'];
		return $parametros;
	}
	
    function guardar($datos){
        $datos['MutualServicio']['tipo_orden_dto'] = parent::GlobalDato('concepto_3',$datos['MutualServicio']['tipo_producto']);
        if(empty($datos['MutualServicio']['dia_corte'])) $datos['MutualServicio']['dia_corte'] = 1;
        if(empty($datos['MutualServicio']['meses_antes_dia_corte'])) $datos['MutualServicio']['meses_antes_dia_corte'] = 1;
        if(empty($datos['MutualServicio']['meses_despues_dia_corte'])) $datos['MutualServicio']['meses_despues_dia_corte'] = 1;
        return $this->save($datos);
    }
    
	
}

?>