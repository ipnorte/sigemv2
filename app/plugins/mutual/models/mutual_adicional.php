<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */
class MutualAdicional extends MutualAppModel{
	var $name = 'MutualAdicional';
	var $tipos = array('P' => 'PORCENTAJE','I' => 'IMPORTE');
	var $aplicaSobre = array(
            1 => '1 - DEUDA TOTAL (incluye el periodo informado)',
            2 => '2 - DEUDA VENCIDA (no incluye el periodo informado)',
            3 => '3 - DEUDA PERIODO',
            4 => '4 - IMPORTE CUOTA SOCIAL (importe vigente)',
            5 => '5 - PUNITORIOS (S/Deuda Vencida)',
			6 => '6 - GTO. BANCARIO X REGISTRO',
        );
	
	
	function guardar($datos){
            
            if(isset($datos['MutualAdicional']['imputar_proveedor_id']) && empty($datos['MutualAdicional']['imputar_proveedor_id']) && $datos['MutualAdicional']['deuda_calcula'] != 5){
                $datos['MutualAdicional']['imputar_proveedor_id'] = 18;
            }  
            return parent::save($datos);
            
	}
	
	
	function getAdicionales(){
		$adicionales = $this->find('all');
		foreach($adicionales as $idx => $adicional){
			$adicionales[$idx] = $this->infoAdicional($adicional);
		}
	}
	
	function getTipos(){
		return $this->tipos;
	}
	
	function getOptAplica(){
		return $this->aplicaSobre;
	}
	
	function afterFind($resultados, $primary = false){
		foreach($resultados as $idx => $resultado){
			$resultados[$idx] = $this->infoAdicional($resultado);
		}
		return $resultados;
	}
	
	function infoAdicional($dato){
		$dato['MutualAdicional']['tipo_desc'] = $this->tipos[$dato['MutualAdicional']['tipo']];
		$dato['MutualAdicional']['deuda_calcula_desc'] = $this->aplicaSobre[$dato['MutualAdicional']['deuda_calcula']];
		
		App::import('Model','Proveedores.Proveedor');
		$oPROV = new Proveedor();
		$dato['MutualAdicional']['proveedor_deuda_aplica'] = (!empty($dato['MutualAdicional']['proveedor_id']) ? $oPROV->getRazonSocial($dato['MutualAdicional']['proveedor_id']) : "*** TODOS ***");
                
                #si no es punitorio y no viene seteado el proveedor tomo por defecto el proveedor mutual (18)
                if(empty($dato['MutualAdicional']['imputar_proveedor_id']) && $dato['MutualAdicional']['deuda_calcula'] != 5){
                    $dato['MutualAdicional']['imputar_proveedor_id'] = 18;
                }
                
		$dato['MutualAdicional']['proveedor_deuda_imputa'] = $oPROV->getRazonSocial($dato['MutualAdicional']['imputar_proveedor_id']);
		
                $dato['MutualAdicional']['organismo'] = (!empty($dato['MutualAdicional']['codigo_organismo']) ? parent::GlobalDato('concepto_1', $dato['MutualAdicional']['codigo_organismo']) : '*** TODOS ***');
                
		return $dato;
	}
	
	function getActivos(){
		$adicionales = $this->find('all',array('conditions' => array('MutualAdicional.activo' => 1)));
		return $adicionales;
	}
	
	function getActivosByOrganismo($codigo_organismo,$periodo=null,$todos=false,$deudaCalcula=NULL){
		$conditions = array();
		if(!$todos) $conditions['MutualAdicional.activo'] = 1;
		$conditions['MutualAdicional.codigo_organismo'] = $codigo_organismo;
		if(!empty($periodo)){
			$conditions['MutualAdicional.periodo_desde <='] = $periodo;
			$conditions['MutualAdicional.periodo_hasta >='] = $periodo;
		}
		if(!empty($deudaCalcula)){
			$conditions['MutualAdicional.deuda_calcula'] = $deudaCalcula;
		}
		$adicionales = $this->find('all',array('conditions' => $conditions));
//                exit;
		return $adicionales;
	}	

	
	
	function generarAdicional($liquidacion_id,$socio_id,$codigoOrganismo,$periodo,$situacionDeuda = 'MUTUSICUMUTU',$pre_imputacion=false){
		
		$calculo = array();
		
		$adicionales = $this->getActivosByOrganismo($codigoOrganismo,$periodo);
        
//        debug($adicionales);
		
		if(empty($adicionales)) return null;
		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();		
		
		foreach($adicionales as $adicional):
		
			$proveedor = $adicional['MutualAdicional']['proveedor_id'];
			$proveedor_imputa = $adicional['MutualAdicional']['imputar_proveedor_id'];
			
			// --> ESTA MAL: ANALIZAR CON EL SALDO DE LA CUOTA!!!
			$cuotas = $oCUOTA->cuotasAdeudadasBySocioAlPeriodoByOrganismo($socio_id,$periodo,$codigoOrganismo,$situacionDeuda,$pre_imputacion);
			$cuotas = Set::extract("/OrdenDescuentoCuota",$cuotas);
			
			if(!empty($proveedor))$cuotas = Set::extract("/OrdenDescuentoCuota[proveedor_id=".$proveedor."]",$cuotas);
			
					
			
			##############################################################################################################
			#DETERMINO SOBRE QUE DEUDA CALCULO (1=INCLUYE EL PERIODO LIQUIDADO | 2=DEUDA ANTERIOR AL PERIODO LIQUIDADO)
			##############################################################################################################
			
			switch ($adicional['MutualAdicional']['deuda_calcula']){
				case 1:
					//toda la deuda incluida el periodo liquidado
					break;
				case 2:
					//deuda anterior al periodo liquidado
					$cuotas = Set::extract("/OrdenDescuentoCuota[periodo_cuota<".$periodo."]",$cuotas);
					break;
				case 3:
					//deuda del periodo liquidado
					$cuotas = Set::extract("/OrdenDescuentoCuota[periodo_cuota=".$periodo."]",$cuotas);
					break;							
			}
			
			echo "<hr/>";
			debug($cuotas);	
			
			if(!empty($cuotas)):
			
				//sumo la deuda
				
			
			endif;
		
		endforeach;
		
	}
	
}
?>