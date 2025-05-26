<?php
/**
 * 
 * @author ADRIAN TORRES
 *
 */
class ProveedorComision extends ProveedoresAppModel{
	
    var $name = 'ProveedorComision';
	
	
   
    function guardarComision($proveedorId,$organismos = array(),$comision = 0,$tipoComision='COB') {        
        $comisiones = array();
        parent::begin();
        foreach($organismos as $organismo){
            $row = array(
                'id' => 0,
                'proveedor_id' => $proveedorId,
                'codigo_organismo' => $organismo,
                'tipo' => $tipoComision,
                'comision' => $comision
            );
            array_push($comisiones, $row);
            $this->borrarComision(null,$proveedorId,$organismo);
        }
        
        if(!$this->saveAll($comisiones)){
            parent::rollback();
            return  false;
        }
        return parent::commit();
    }
	
	
	
	/**
	 * Get Comision
	 * Devuelve el porcentaje de comision
	 * @param $codigo_organismo
	 * @param $proveedor_id
	 * @param $tipo_producto
	 * @param $tipo_cuota
	 * @param $tipo
	 * @return unknown_type
	 */
	function getComision($codigo_organismo,$proveedor_id,$tipo_producto=NULL,$tipo_cuota=NULL,$tipo='COB',$fechaDesde = null){
            /*
             * PARA ANALIZAR Y LUEGO PONER EN PRODUCCION.
                // 21/06/2017 GUSTAVO 
                // CONFIGURO LA GLOBAL DATOS PARA FACTURAR LA COMISION DE LOS COMERCIOS
                // SI EL CODIGO NO EXISTE O EL CAMPO LOGICO_1 NO ESTA TILDADO GENERA LA COMISION
                // SI EL CODIGO EXISTE Y EL CAMPO LOGICO_1 ESTA TILDADO NO GENERA LA COMISION.
                // POR DEFAULT GENERA LA COMISION.
                $glb = $this->getGlobalDato('logico_1','MUTUMUTUCOMI');
                $gnrComi = 0;
                if(!isset($glb['GlobalDato']['logico_1'])) $gnrComi = 0;
                else $gnrComi = $glb['GlobalDato']['logico_1'];
                if($gnrComi === 1) return 0;
            */    
                $conditions = array();
		$conditions['ProveedorComision.proveedor_id'] = $proveedor_id;
		$conditions['ProveedorComision.codigo_organismo'] = $codigo_organismo;
//		$conditions['ProveedorComision.tipo_producto'] = $tipo_producto;
//		$conditions['ProveedorComision.tipo_cuota'] = $tipo_cuota;
		$conditions['ProveedorComision.tipo'] = $tipo;
//		if(empty($fechaDesde)) $fechaDesde = date('Y-m-d');
//		$conditions['ProveedorComision.fecha_vigencia <='] = $fechaDesde;
		$comision = $this->find('all',array('conditions' => $conditions,'order' => array('ProveedorComision.fecha_vigencia DESC'), 'limit' => 1));
		if(empty($comision)) return 0;
		else return $comision[0]['ProveedorComision']['comision'];
	}
	
	function getComisionesByProveedor($proveedor_id,$codigo_organismo = null, $tipo='COB'){
		
		$comisiones = array();
		$comisiones = array();
		
        $sql = "select 
                ProveedorComision.id,
                ProveedorComision.proveedor_id,
                GlobalDato.concepto_1,
                ProveedorComision.comision
                from proveedor_comisiones as ProveedorComision
                inner join global_datos as GlobalDato on (GlobalDato.id = ProveedorComision.codigo_organismo)
                where proveedor_id = $proveedor_id;";
        
        $comisiones = $this->query($sql);
        return $comisiones;
	}
	
	
	function borrarComision($comision_id,$proveedor_id = null,$codigo_organismo = null){
	    if(!empty($comision_id)){return $this->del($comision_id);}
		return $this->deleteAll("ProveedorComision.proveedor_id = $proveedor_id and ProveedorComision.codigo_organismo = '$codigo_organismo'");
	}
	
}
?>