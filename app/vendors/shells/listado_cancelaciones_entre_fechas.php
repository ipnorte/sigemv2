<?php

/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php listado_cancelaciones_entre_fechas 850 -app /home/adrian/Desarrollo/www/sigem/app/
 * 
 */

class ListadoCancelacionesEntreFechasShell extends Shell {
	var $forma_cancelacion;
	var $fecha_desde;
	var $fecha_hasta;
	var $criterio;
	var $proveedor_id;
	var $uses = array('Mutual.CancelacionOrden');
	
	var $tasks = array('Temporal');
	
	function main() {
		$STOP = 0;
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 

		$this->fecha_desde			= $asinc->getParametro('p1');
		$this->fecha_hasta			= $asinc->getParametro('p2');
		$this->criterio				= $asinc->getParametro('p3');
		$this->forma_cancelacion 	= $asinc->getParametro('p4');
		$this->proveedor_id	 		= $asinc->getParametro('p5');
		
		$asinc->actualizar(0,100,"ESPERE, INICIANDO PROCESO...");
		$STOP = 0;
		$total = 0;
		$i = 0;
		$asinc->actualizar(0,100,"ESPERE, CONSULTANDO ORDENES DE CANCELACIONES...");
		
		//limpio la tabla temporal
		if(!$this->Temporal->limpiarTabla($asinc->id)){
			$asinc->fin("SE PRODUJO UN ERROR...");
			return;
		}

		$ordenes = $this->getOrdenes();
		$total = count($ordenes);
		$asinc->setTotal($total);
		$i = 0;	

		$temp = array();
		foreach($ordenes as $orden){
			
			$orden = $this->CancelacionOrden->armaDato($orden,true,true);
//			debug($orden['CancelacionOrden']);
			
			$asinc->actualizar($i,$total,"$i / $total - PROCESANDO >> " . $orden['CancelacionOrden']['id'] . ' - ' . $orden['CancelacionOrden']['beneficiario']);
			
			$temp['AsincronoTemporal'] = array(
										'asincrono_id' => $asinc->id,
										'clave_1' => $orden['CancelacionOrden']['orden_proveedor_id'],
										'clave_2' => $orden['CancelacionOrden']['id'],
										'texto_2' => $orden['CancelacionOrden']['beneficiario_tdocndoc'],
										'texto_3' => $orden['CancelacionOrden']['beneficiario_apenom'],
										'texto_4' => $orden['CancelacionOrden']['tipo_nro_odto'],
										'texto_5' => $orden['CancelacionOrden']['proveedor_producto_odto'],
										'texto_6' => $orden['CancelacionOrden']['a_la_orden_de'],
										'texto_7' => $orden['CancelacionOrden']['forma_cancelacion_desc'],
										'texto_8' => $orden['CancelacionOrden']['norden_str'],
										'texto_9' => $orden['CancelacionOrden']['estado_desc'],
										'texto_10' => $orden['CancelacionOrden']['tipo_cancelacion_desc'],
										'texto_11' => (!empty($orden['CancelacionOrden']['fecha_vto']) ? date('d-m-Y',strtotime($orden['CancelacionOrden']['fecha_vto'])) : date('d-m-Y')),
										'texto_12' => $orden['CancelacionOrden']['fecha_imputacion'],
										'texto_13' => $orden['CancelacionOrden']['cuotas_str'],
										'texto_14' => $orden['CancelacionOrden']['Recibo']['numero_string2'],
										'texto_15' => $orden['CancelacionOrden']['ProveedorFactura']['tipo_comprobante_desc2'],
										'decimal_1' => $orden['CancelacionOrden']['importe_seleccionado'],
										'decimal_2' => $orden['CancelacionOrden']['importe_proveedor'],
										'decimal_3' => $orden['CancelacionOrden']['importe_diferencia'],
										'decimal_4' => $orden['CancelacionOrden']['ProveedorFactura']['total_comprobante'],
										'decimal_5' => $orden['CancelacionOrden']['ProveedorFactura']['pagos'],
										'decimal_6' => $orden['CancelacionOrden']['ProveedorFactura']['saldo'],
										'entero_1' => $orden['CancelacionOrden']['pendiente_rendicion_proveedor'],
										
			);
			//ARMO EL DETALLE
			$temp['AsincronoTemporalDetalle'] = array();
//			debug($orden);
			if(!empty($orden['CancelacionOrdenCuota'])){
				
				$detalle = array();
				
				foreach($orden['CancelacionOrdenCuota'] as $idx => $cuota){
					
//					debug($cuota);
					
					$detalle['texto_1'] = $cuota['OrdenDescuentoCuota']['orden_descuento_id'];
					$detalle['texto_2'] = $cuota['OrdenDescuentoCuota']['tipo_nro'];
					$detalle['texto_3'] = $cuota['OrdenDescuentoCuota']['organismo'];
					$detalle['texto_4'] = $cuota['OrdenDescuentoCuota']['periodo'];
					$detalle['texto_5'] = $cuota['OrdenDescuentoCuota']['cuota'];
					$detalle['texto_6'] = $cuota['OrdenDescuentoCuota']['proveedor_producto'];
					$detalle['texto_7'] = $cuota['OrdenDescuentoCuota']['tipo_cuota_desc'];
					$detalle['decimal_1'] = $cuota['importe'];
					
					array_push($temp['AsincronoTemporalDetalle'],$detalle);
					
				}
			}
			
//			debug($temp);
			if(!$this->Temporal->grabar($temp)){
				$STOP = 1;
				break;
			}
			
			if($asinc->detenido()){
				$STOP = 1;
				break;
			}			
			
			$i++;
		}
	
		if($STOP == 0){
			$asinc->actualizar($i,$total,"FINALIZANDO...");
			$asinc->fin("**** PROCESO FINALIZADO ****");
		}
		
        $asinc->actualizar(100,100,"FINALIZANDO...");
        $asinc->fin("**** PROCESO FINALIZADO ****");		
		
	}
	//FIN PROCESO ASINCRONO
	
	####################################################################################################
	# METODOS ESPECIFICOS DEL PROCESO
	####################################################################################################
	
	function getOrdenes(){
//		$this->CancelacionOrden->unbindModel(array('belongsTo' => array('Proveedor','OrdenDescuento')));
//		$this->CancelacionOrden->Socio->bindModel(array('belongsTo' => array('Persona')));
		$criterio = "";
		switch ($this->criterio){
			case "FV":
				$criterio = "IFNULL(`CancelacionOrden`.`fecha_vto`,'".date('Y-m-d')."') BETWEEN '".$this->fecha_desde."' AND '".$this->fecha_hasta."'";
				break;
			case "FI":
				$criterio = "IFNULL(`CancelacionOrden`.`fecha_imputacion`,'".date('Y-m-d')."') BETWEEN '".$this->fecha_desde."' AND '".$this->fecha_hasta."'";
				break;
			case "FA":
				$criterio = "`CancelacionOrden`.`created` BETWEEN '".$this->fecha_desde."' AND '".$this->fecha_hasta."'";
				break;								
			default:
				$criterio = "IFNULL(`CancelacionOrden`.`fecha_vto`,'".date('Y-m-d')."') BETWEEN '".$this->fecha_desde."' AND '".$this->fecha_hasta."'";
				break;	
		}
		
		$sql = "SELECT 
					`CancelacionOrden`.`id`, 
					`CancelacionOrden`.`orden_proveedor_id`, 
					`CancelacionOrden`.`socio_id`, 
					`CancelacionOrden`.`orden_descuento_id`, 
					`CancelacionOrden`.`importe_proveedor`, 
					`CancelacionOrden`.`importe_seleccionado`, 
					`CancelacionOrden`.`fecha_vto`, 
					`CancelacionOrden`.`tipo_cancelacion`, 
					`CancelacionOrden`.`importe_cuota`, 
					`CancelacionOrden`.`estado`, 
					`CancelacionOrden`.`saldo_orden_dto`, 
					`CancelacionOrden`.`persona_idr`, 
					`CancelacionOrden`.`observaciones`, 
					`CancelacionOrden`.`forma_cancelacion`, 
					`CancelacionOrden`.`forma_pago`, 
					`CancelacionOrden`.`banco_id`, 
					`CancelacionOrden`.`nro_cta_bco`, 
					`CancelacionOrden`.`nro_operacion`, 
					`CancelacionOrden`.`fecha_operacion`, 
					`CancelacionOrden`.`pendiente_rendicion_proveedor`, 
					`CancelacionOrden`.`tipo_cuota_diferencia`, 
					`CancelacionOrden`.`importe_diferencia`, 
					`CancelacionOrden`.`nro_recibo`, 
					`CancelacionOrden`.`fecha_imputacion`, 
					`CancelacionOrden`.`nueva_orden_dto_id`,
					`CancelacionOrden`.`user_created`, 
					`CancelacionOrden`.`user_modified`, 
					`CancelacionOrden`.`created`, 
					`CancelacionOrden`.`modified`
				FROM 
					`cancelacion_ordenes` AS `CancelacionOrden` 
				LEFT JOIN `socios` AS `Socio` ON (`CancelacionOrden`.`socio_id` = `Socio`.`id`) 
				LEFT JOIN `personas` AS `Persona` ON (`Socio`.`persona_id` = `Persona`.`id`) 
				WHERE 
					-- `CancelacionOrden`.`estado` = 'P' AND 
					".(!empty($this->forma_cancelacion) ? "`CancelacionOrden`.`forma_cancelacion` = '".$this->forma_cancelacion."' AND " : "")."
					$criterio ".(!empty($this->proveedor_id) ? " AND `CancelacionOrden`.`orden_proveedor_id` = $this->proveedor_id" : "")."
				ORDER BY `Persona`.`apellido` ASC,  `Persona`.`nombre` ASC, `CancelacionOrden`.`orden_proveedor_id` ";
		$ordenes = $this->CancelacionOrden->query($sql);
		return $ordenes;
	}

}
?>