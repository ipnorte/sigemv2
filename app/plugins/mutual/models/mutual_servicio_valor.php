<?php

/**
 * 
 * MutualServicioValor
 * @author adrian [19/01/2012]
 *
 */

class MutualServicioValor extends MutualAppModel{
	
	var $name = "MutualServicioValor";
	
	
	function getValores($servicio_id){
		$conditions = array();
		$conditions['MutualServicioValor.mutual_servicio_id'] = $servicio_id;
		$datos = $this->find('all',array('conditions' => $conditions,'order' => array('MutualServicioValor.periodo_vigencia DESC')));
		if(empty($datos)) return null;
		foreach($datos as $idx => $dato):
			$datos[$idx] = $this->armaDatos($dato);
		endforeach;
		return $datos;		
	}
	
	
	function armaDatos($valor){
		$valor['MutualServicioValor']['codigo_organismo_desc'] = parent::GlobalDato("concepto_1",$valor['MutualServicioValor']['codigo_organismo']);
		return $valor;
	}
	

	function getValoresVigentes($servicio_id,$periodo,$organismo = null,$edad = null){
		$conditions = array();
		$conditions['MutualServicioValor.mutual_servicio_id'] = $servicio_id;
		if(!empty($organismo))$conditions['MutualServicioValor.codigo_organismo'] = $organismo;
		$conditions['MutualServicioValor.periodo_vigencia <='] = $periodo;
		$datos = $this->find('all',array('conditions' => $conditions,'order' => array('MutualServicioValor.periodo_vigencia DESC'),'limit' => 1));
		$valores = array();
		$valores['id'] = 0;
		$valores['importe_titular'] = 0;
		$valores['importe_adicional'] = 0;
		$valores['costo_titular'] = 0;
		$valores['costo_adicional'] = 0;
		$valores['periodo_vigencia'] = null;
		$valores['fecha_vigencia'] = null;
		
		if(empty($datos)) return $valores;
		
		$valores['id'] = $datos[0]['MutualServicioValor']['id'];
		$valores['importe_titular'] = $datos[0]['MutualServicioValor']['importe_titular'];
		$valores['importe_adicional'] = $datos[0]['MutualServicioValor']['importe_adicional'];
		$valores['costo_titular'] = $datos[0]['MutualServicioValor']['costo_titular'];
		$valores['costo_adicional'] = $datos[0]['MutualServicioValor']['costo_adicional'];
		$valores['periodo_vigencia'] = $datos[0]['MutualServicioValor']['periodo_vigencia'];
		$valores['fecha_vigencia'] = $datos[0]['MutualServicioValor']['fecha_vigencia'];
		return $valores;
	}
	
//	function getCostosVigentes($servicio_id,$fecha,$organismo){
//		$conditions = array();
//		$conditions['MutualServicioValor.mutual_servicio_id'] = $servicio_id;
//		$conditions['MutualServicioValor.codigo_organismo'] = $organismo;
//		$conditions['MutualServicioValor.fecha_vigencia <='] = $periodo;
//		$datos = $this->find('all',array('conditions' => $conditions,'order' => array('MutualServicioValor.fecha_vigencia DESC'),'limit' => 1));
//		$valores = array();
//		$valores['importe_titular'] = 0;
//		$valores['importe_adicional'] = 0;
//		$valores['costo_titular'] = 0;
//		$valores['costo_adicional'] = 0;
//		$valores['periodo_vigencia'] = null;
//		$valores['fecha_vigencia'] = null;
//		if(empty($datos)) return $valores;
//		$valores['id'] = $datos[0]['MutualServicioValor']['id'];
//		$valores['importe_titular'] = $datos[0]['MutualServicioValor']['importe_titular'];
//		$valores['importe_adicional'] = $datos[0]['MutualServicioValor']['importe_adicional'];
//		$valores['costo_titular'] = $datos[0]['MutualServicioValor']['costo_titular'];
//		$valores['costo_adicional'] = $datos[0]['MutualServicioValor']['costo_adicional'];
//		$valores['periodo_vigencia'] = $datos[0]['MutualServicioValor']['periodo_vigencia'];
//		$valores['fecha_vigencia'] = $datos[0]['MutualServicioValor']['fecha_vigencia'];
//		return $valores;
//	}	
	
	
	function calcularImporteMensual($solicitud_servicio_id,$periodoCalculo,$codigoOrganismo,$actualizarEnTabla = true){
		
		App::import('Model','Mutual.MutualServicioSolicitud');
		$oSOLSERV = new MutualServicioSolicitud();		
		
		$solicitud = $oSOLSERV->read(null,$solicitud_servicio_id);
		
        if(empty($solicitud)) return 0;
        
        $solicitud = $oSOLSERV->armaDatos($solicitud,false);
        
        
		
		$servicioID = $solicitud['MutualServicioSolicitud']['mutual_servicio_id'];
		$periodoDesde = $solicitud['MutualServicioSolicitud']['periodo_desde'];
		
		if($solicitud['MutualServicioSolicitud']['proveedor_id'] === $this->lomas_villa_allende){

			App::import('Vendor','lomas_villa_allende');
			$oLOMASVA = new LomasVillaAllende();
			$solicitud = $oLOMASVA->setValoresServicio($solicitud);
			
			if(empty($solicitud['MutualServicioSolicitud']['cuotas'])){
				
				$IMPORTE_MENSUAL = $solicitud['MutualServicioSolicitud']['importe_mensual_total'];
				if(!empty($solicitud['MutualServicioSolicitudAdicional'])){
					if($actualizarEnTabla) $oSOLSERV->saveAll($solicitud);
				}else{
					if($actualizarEnTabla) $oSOLSERV->save($solicitud);
				}
				
				//ACTUALIZO LA ORDEN DE DESCUENTO
				if($actualizarEnTabla && $solicitud['MutualServicioSolicitud']['orden_descuento_id'] != 0){
					App::import('Model','Mutual.OrdenDescuento');
					$oORDEN = new OrdenDescuento();	
					$oORDEN->actualizarValor($solicitud['MutualServicioSolicitud']['orden_descuento_id'],'importe_cuota',$IMPORTE_MENSUAL);
					$oORDEN->actualizarValor($solicitud['MutualServicioSolicitud']['orden_descuento_id'],'importe_total',$IMPORTE_MENSUAL);
					
					if($IMPORTE_MENSUAL == 0) $oORDEN->actualizarValor($solicitud['MutualServicioSolicitud']['orden_descuento_id'],'activo',0);
					else $oORDEN->actualizarValor($solicitud['MutualServicioSolicitud']['orden_descuento_id'],'activo',1);
					
				}
				return $IMPORTE_MENSUAL;
			}else{
				return 0;
			}
		}
		
		$valorCuotaServicio = $this->getValoresVigentes($servicioID,$periodoCalculo,$codigoOrganismo);
		
		if(!empty($solicitud['MutualServicioSolicitud']['periodo_hasta']) && $solicitud['MutualServicioSolicitud']['periodo_hasta'] > $periodoCalculo) return 0;
		
		$IMPORTE_MENSUAL = 0;

		$solicitud['MutualServicioSolicitud']['mutual_servicio_valor_id'] = $valorCuotaServicio['id'];
		$solicitud['MutualServicioSolicitud']['importe_mensual'] = $valorCuotaServicio['importe_titular'];
		
		
		$IMPORTE_MENSUAL += $solicitud['MutualServicioSolicitud']['importe_mensual'];
		
		if(!empty($solicitud['MutualServicioSolicitudAdicional'])){
			foreach($solicitud['MutualServicioSolicitudAdicional'] as $idx => $adicional){
				if(empty($adicional['periodo_hasta']) && $adicional['periodo_hasta'] <= $periodoCalculo){
					$adicional['mutual_servicio_valor_id'] = $valorCuotaServicio['id'];
					$adicional['importe_mensual'] = $valorCuotaServicio['importe_adicional'];
					$IMPORTE_MENSUAL += $adicional['importe_mensual'];
				}
				$solicitud['MutualServicioSolicitudAdicional'][$idx] = $adicional;
			}
			$solicitud['MutualServicioSolicitud']['importe_mensual_total'] = $IMPORTE_MENSUAL;
			if($actualizarEnTabla) $oSOLSERV->saveAll($solicitud);
		}else{
			$solicitud['MutualServicioSolicitud']['importe_mensual_total'] = $IMPORTE_MENSUAL;
			if($actualizarEnTabla) $oSOLSERV->save($solicitud);
		}
		
		//ACTUALIZO LA ORDEN DE DESCUENTO
		if($actualizarEnTabla && $solicitud['MutualServicioSolicitud']['orden_descuento_id'] != 0){
			App::import('Model','Mutual.OrdenDescuento');
			$oORDEN = new OrdenDescuento();	
			$oORDEN->actualizarValor($solicitud['MutualServicioSolicitud']['orden_descuento_id'],'importe_cuota',$IMPORTE_MENSUAL);
			$oORDEN->actualizarValor($solicitud['MutualServicioSolicitud']['orden_descuento_id'],'importe_total',$IMPORTE_MENSUAL);
			
			if($IMPORTE_MENSUAL == 0) $oORDEN->actualizarValor($solicitud['MutualServicioSolicitud']['orden_descuento_id'],'activo',0);
			else $oORDEN->actualizarValor($solicitud['MutualServicioSolicitud']['orden_descuento_id'],'activo',1);
			
		}
		
		return $IMPORTE_MENSUAL;
	}
	
	
}

?>