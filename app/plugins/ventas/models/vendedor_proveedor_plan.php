<?php
class VendedorProveedorPlan extends VentasAppModel{
	
	var $name = 'VendedorProveedorPlan';
	var $belonsTo = array('Vendedor','ProveedorPlan');
	
	function getPlanComision($id){
		$dato = parent::read(null,$id);
		return $dato;
	}
	
	function borrar($id){
		return parent::del($id);
	}
	
	function getPlanesDisponibles($vendedor_id){
		$disponibles = array();
		App::import('model','proveedores.proveedor_plan');
		$oPROPLAN = new ProveedorPlan();
		$planes = $oPROPLAN->getPlanesVigentesTodos(null,false,true);
		foreach ($planes as $i => $plan){
			$plan['ProveedorPlan']['vendedor_proveedor_plan'] = false;
			$plan['ProveedorPlan']['vendedor_proveedor_plan_id'] = 0;
			$plan['ProveedorPlan']['vendedor_proveedor_plan_monto_venta'] = 0;
			$plan['ProveedorPlan']['vendedor_proveedor_plan_comision'] = 0;
			$planesVendedor = $this->find('all',array('conditions' => array('VendedorProveedorPlan.vendedor_id' => $vendedor_id,'VendedorProveedorPlan.proveedor_plan_id' => $plan['ProveedorPlan']['id']),'order' => 'VendedorProveedorPlan.monto_venta ASC'));
			if(empty($planesVendedor)){
				array_push($disponibles, $plan);
			}
		}
		return $disponibles;
	}
	
	function getPlanes($vendedor_id){
		
		$sql = "SELECT 
					VendedorProveedorPlan.*,
					ProveedorPlan.id,
					ProveedorPlan.activo,
                                        ProveedorPlan.descripcion,
					Proveedor.razon_social
				FROM vendedor_proveedor_planes AS VendedorProveedorPlan
				INNER JOIN proveedor_planes AS ProveedorPlan ON (ProveedorPlan.id = VendedorProveedorPlan.proveedor_plan_id)
				INNER JOIN proveedores AS Proveedor ON (Proveedor.id = ProveedorPlan.proveedor_id)
				WHERE VendedorProveedorPlan.vendedor_id = $vendedor_id
				ORDER BY Proveedor.razon_social,VendedorProveedorPlan.monto_venta;";
		$planes = $this->query($sql);
		

		return $planes;
	}
	
	function guardarComision($data){
		return parent::save($data);
	}
	
	function is_activo($vendedor_id,$plan_id){
            $sql = "select * from vendedor_proveedor_planes
                    where vendedor_id = $vendedor_id
                    and proveedor_plan_id = $plan_id;";
            $data = $this->query($sql);
            if(empty($data)) return FALSE;
            else return TRUE;
        }	
}
?>