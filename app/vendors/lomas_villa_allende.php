<?php

/**
*
* lomas_villa_allende.php
* @author adrian [* 14/06/2012]
*/

App::import('Model','mutual.MutualServicioSolicitud');

class LomasVillaAllende{
	
	var $proveedor_id = null;
	var $oServicioSolicitud = null;
	var $render_solicitud = null;
	
	function LomasVillaAllende($proveedor_id = null){
		if(!empty($proveedor_id)) $this->proveedor_id = $proveedor_id;
		$this->oServicioSolicitud = new MutualServicioSolicitud();
	}
	
	
	function getRender($solicitud_id){
		$codigo = $this->oServicioSolicitud->getSolicitud($solicitud_id);
		$codigo = $codigo['MutualServicioSolicitud']['mutual_servicio_codigo'];
		$cuotas = $this->oServicioSolicitud->GlobalDato('entero_2',$codigo);
		if($cuotas != 0) return 'imprimir_solicitud_lomasva_prenecesidad_pdf';
		else return 'imprimir_solicitud_lomasva_prepago_pdf';
	}
	
	
	function setValoresServicio($solicitud){
		
		$codigo_producto = $solicitud['MutualServicioSolicitud']['mutual_servicio_codigo'];
		
		$cuotas = $this->oServicioSolicitud->GlobalDato('entero_2',$codigo_producto);
		
		if($cuotas != 0){
			
			$impoCuota = $this->oServicioSolicitud->GlobalDato('decimal_1',$codigo_producto);
			
			$adicionales = count($solicitud['MutualServicioSolicitudAdicional']);
			$unidades = (empty($adicionales) ? 1 : $adicionales + 1);
			
			$impoCuotaTotal = $impoCuota * $unidades;
			
			$solicitud['MutualServicioSolicitud']['permanente'] = 0;
			$solicitud['MutualServicioSolicitud']['cuotas'] = $cuotas;
			$solicitud['MutualServicioSolicitud']['importe_cuota'] = $impoCuotaTotal;
			$solicitud['MutualServicioSolicitud']['importe_mensual'] = $impoCuota;
			$solicitud['MutualServicioSolicitud']['importe_mensual_total'] = $cuotas * $impoCuotaTotal;
			$solicitud['MutualServicioSolicitud']['importe_mensual_total_letras'] = $this->oServicioSolicitud->num2letras($solicitud['MutualServicioSolicitud']['importe_mensual_total']);
			
			if(!empty($solicitud['MutualServicioSolicitudAdicional'])){
				foreach($solicitud['MutualServicioSolicitudAdicional'] as $idx => $adicional){
					$solicitud['MutualServicioSolicitudAdicional'][$idx]['importe_mensual'] = $impoCuota;
				}
			}
			$this->render_solicitud = 'imprimir_solicitud_lomasva_prenecesidad_pdf';
			return $solicitud;
		}
		
		
		$criterios = $this->parseParametros($codigo_producto);
		
		$valoresTitular = $this->getValoresByEdadByCriterio($solicitud['MutualServicioSolicitud']['titular_edad'], $criterios);
		$solicitud['MutualServicioSolicitud']['importe_mensual'] = $solicitud['MutualServicioSolicitud']['importe_mensual_total'] = $valoresTitular['cuota'];
		$totalMensualTitular = $solicitud['MutualServicioSolicitud']['importe_mensual'];
		
		if(!empty($solicitud['MutualServicioSolicitudAdicional'])){
			
			foreach($solicitud['MutualServicioSolicitudAdicional'] as $idx => $adicional){
				$valoresAdicional = $this->getValoresByEdadByCriterio($adicional['adicional_edad'], $criterios);
				if($valoresAdicional['edad'] != 999){
					$solicitud['MutualServicioSolicitud']['importe_mensual_total'] += $valoresAdicional['cuota'];
					$solicitud['MutualServicioSolicitudAdicional'][$idx]['importe_mensual'] = $valoresAdicional['cuota'];
				}
			}
			
		}
		$this->render_solicitud = 'imprimir_solicitud_lomasva_prepago_pdf';
		return $solicitud;
	}
	
	function getValoresByEdadByCriterio($edad,$criterios){
		$valores = array();
		foreach($criterios as $edadTope => $values){
			if($edad <= $edadTope){
				$valores = $values;
				break;
			}
		}
		return $valores;
	}
	
	
	function parseParametros($codigo_producto){
		$criterios = array();
		$config = $this->oServicioSolicitud->GlobalDato('texto_1',$codigo_producto);
		if(empty($config)){
			$valorCuota = $this->oServicioSolicitud->GlobalDato('decimal_1',$codigo_producto);
			$criterios[999] = array('edad' => 999,'cuota' => $valorCuota, 'carencia' => 0, 'unidad' => 'dias');
			return $criterios;
		}
		$parametros = explode("\n",$config);
		if(!empty($parametros)):
			foreach($parametros as $parametro){
				$first = substr($parametro,0,1);
				if($first != '#'){
					$linea = explode(",", $parametro);
					$criterios[$linea[0]] = array('edad' => $linea[0], 'cuota' => $linea[1], 'carencia' => $linea[2], 'unidad' => $linea[3]);
				}
			}
		endif;
		return $criterios;
	}
	
}

?>