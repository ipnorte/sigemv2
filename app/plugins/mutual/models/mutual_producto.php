<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */

class MutualProducto extends MutualAppModel{
	
	var $name = 'MutualProducto';
	var $belongsTo = 'Proveedor';
        var $hasMany = array('MutualProductoAnexo');
	
        
	function get($id){
            $this->bindModel(array('belongsTo' => array('Proveedor')));
            $producto = $this->read(null,$id);
            return $producto;
	}        
        
	function guardar($datos){
            if(isset($datos['MutualProducto']['tipo_producto'])){
                $tipoOrden = parent::GlobalDato("concepto_3", $datos['MutualProducto']['tipo_producto']);
                $datos['MutualProducto']['tipo_orden_dto'] = (empty($tipoOrden) ? "OCOMP" : $tipoOrden);	
            }
            if(!parent::save($datos)) return false;
            $productoID = (isset($datos['MutualProducto']['id']) && !empty($datos['MutualProducto']['id']) ? $datos['MutualProducto']['id'] : $this->getLastInsertID());
            $this->query("DELETE FROM mutual_producto_anexos WHERE mutual_producto_id = $productoID;");
            if(!empty($datos['MutualProducto']['anexos'])){
                App::import('Model','mutual.MutualProductoAnexo');
                $oANEXO = new MutualProductoAnexo();
                foreach($datos['MutualProducto']['anexos'] as $anexo){
                    $oANEXO->query("INSERT INTO mutual_producto_anexos(mutual_producto_id,codigo_anexo)VALUES($productoID,'$anexo');");
                }
            }
            return TRUE;
	}
	
	function getListaProductos(){
		$sql = "SELECT * FROM mutual_productos AS MutualProducto
				INNER JOIN proveedores AS Proveedor ON (Proveedor.id = MutualProducto.proveedor_id)
				INNER JOIN global_datos AS GlobalDato ON (GlobalDato.id = MutualProducto.tipo_producto)
				ORDER BY Proveedor.razon_social,GlobalDato.concepto_1";
		$productos = $this->query($sql);
		return $productos;		
	}
	
	
	function getMayorCuotaSocialDiferenciada($tipo_producto,$proveedor_id){
		$cuota = $this->find('all',array('conditions' => array(
																'MutualProducto.tipo_producto' => $tipo_producto,
																'MutualProducto.proveedor_id' => $proveedor_id
																),
											'fields' => array('MutualProducto.cuota_social_diferenciada'),
											'order' => array('MutualProducto.cuota_social_diferenciada DESC'),
											'limit' => 1																
										)
		);
//		debug($cuota);
		return (isset($cuota[0]['MutualProducto']['cuota_social_diferenciada']) ? $cuota[0]['MutualProducto']['cuota_social_diferenciada'] : 0);
	}
	
        
        function get_anexos_print($id){
            $sql = "select GlobalDato.id,GlobalDato.concepto_2 
                    from mutual_producto_anexos MutualProductoAnexo
                    inner join global_datos as GlobalDato on (GlobalDato.id = MutualProductoAnexo.codigo_anexo) 
                    where MutualProductoAnexo.mutual_producto_id = $id;";
            $datos = $this->query($sql);
            $datos = Set::extract("/GlobalDato/concepto_2",$datos);
            return $datos;
        }
	
	function get_modelo_print($id){
            $sql = "select GlobalDato.id,GlobalDato.concepto_2 
                    from mutual_productos MutualProducto
                    inner join global_datos as GlobalDato on (GlobalDato.id = MutualProducto.modelo_solicitud_codigo) 
                    where MutualProducto.id = $id;";
            $datos = $this->query($sql);
            $datos = Set::extract("/GlobalDato/concepto_2",$datos);
            if(!empty($datos) && isset($datos[0])) return $datos[0];
            else return "imprimir_orden_pdf";
        }        
        
        
}
?>