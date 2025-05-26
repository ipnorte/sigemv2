<?php

/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * /usr/bin/php5 /home/adrian/Trabajo/www/sigemv2/cake/console/cake.php listado_consumos_entre_fechas 69528 -app /home/adrian/Trabajo/www/sigemv2/app/
 * 
 * 
 */

class ListadoConsumosEntreFechasShell extends Shell {

	var $fecha_desde;
	var $fecha_hasta;
	var $optionList;
    var $proveedorId;
    var $codigoOrganismo;   
    var $tipo_producto;
	var $uses = array('Mutual.MutualProductoSolicitud');
	
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

		$this->fecha_desde		= $asinc->getParametro('p1');
		$this->fecha_hasta		= $asinc->getParametro('p2');
		$this->optionList		= $asinc->getParametro('p3');
		$this->proveedorId		= $asinc->getParametro('p4');
		$this->codigoOrganismo	= $asinc->getParametro('p5'); 
                
		$periodo_corte = $asinc->getParametro('p6'); 
		
//                $this->tipo_producto	= $asinc->getParametro('p8');
                
		$asinc->actualizar(0,100,"ESPERE, INICIANDO PROCESO...");
		$STOP = 0;
		$total = 0;
		$i = 0;
		$asinc->actualizar(0,100,"ESPERE, CONSULTANDO ORDENES DE CONSUMO / SERVICIO A PROCESAR...");
		
		//limpio la tabla temporal
		if(!$this->Temporal->limpiarTabla($asinc->id)){
			$asinc->fin("SE PRODUJO UN ERROR...");
			return;
		}

		$ordenes = $this->getOrdenes();
		$total = count($ordenes);
		$asinc->setTotal($total);
		$i = 0;	
                
		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCuota = new OrdenDescuentoCuota();                

		$temp = array();
		foreach($ordenes as $orden){
			$orden = $this->MutualProductoSolicitud->armaDatos($orden,false);
			// debug($orden);
			
			$saldo = $cuotas = $pendiente = $saldoAjustado = $devengado = $cobrado = $cobrado_optimo = $cuotasNoVencidas = 0 ;
			$saldo_0003 = $saldo_0306 = $saldo_0609 = $saldo_0912 = $saldo_1213 = $saldo_avencer = $saldo_av3 = $saldo_av6 = $saldo_av9 = $saldo_av12  = $saldo_av13 = 0;
                        
			if(!empty($orden['MutualProductoSolicitud']['orden_descuento_id'])){
//              $ultimoPeriodoLiquidado = $oLiq->getUltimoPeriodoLiquidado($solicitud['MutualProductoSolicitud']['organismo']);
//              $imputado = $oLiq->isImputada($solicitud['MutualProductoSolicitud']['organismo'], $ultimoPeriodoImputado);
				$ordenDto = $oCuota->getMoraByOrdenDtoHastaPeriodo($orden['MutualProductoSolicitud']['orden_descuento_id'],$periodo_corte);
				if(!empty($ordenDto)){
					$cuotas = $ordenDto['cuotas_vencidas'];
					$cuotasNoVencidas = $ordenDto['cuotas_avencer'];
					$pendiente = $ordenDto['pendiente_acreditar'];
					$saldo = $ordenDto['saldo'];
					$saldoAjustado = $saldo - $pendiente;
					$cobrado = $ordenDto['cobrado'];
					$cobrado_optimo = $ordenDto['cobrado_optimo'];
					$saldo_0003 = $ordenDto['saldo_0003'];
					$saldo_0306 = $ordenDto['saldo_0306'];
					$saldo_0609 = $ordenDto['saldo_0609'];
					$saldo_0912 = $ordenDto['saldo_0912'];
					$saldo_1213 = $ordenDto['saldo_1213'];
					
					$saldo_avencer = $ordenDto['saldo_avencer'];
					$saldo_av3 = $ordenDto['saldoav_03'];
					$saldo_av6 = $ordenDto['saldoav_06'];
					$saldo_av9 = $ordenDto['saldoav_09'];
					$saldo_av12 = $ordenDto['saldoav_12'];
					$saldo_av13 = $ordenDto['saldoav_13'];
					
				}
			}                        
                        
			$asinc->actualizar($i,$total,"$i / $total - PROCESANDO >> " . $orden['MutualProductoSolicitud']['id'] . ' - ' . $orden['MutualProductoSolicitud']['beneficiario']);
			
			$temp['AsincronoTemporal'] = array(
				'asincrono_id' => $asinc->id,
				'clave_1' => $orden['MutualProductoSolicitud']['id'],
				'clave_2' => $orden['MutualProductoSolicitud']['orden_descuento_id'],
				'clave_3' => $orden['MutualProductoSolicitud']['tipo_producto'],
				'entero_2' => $orden['MutualProductoSolicitud']['id'],
				'texto_1' => $orden['MutualProductoSolicitud']['user_created'],
				'texto_3' => $orden['MutualProductoSolicitud']['fecha'],
				'texto_4' => $orden['MutualProductoSolicitud']['fecha_pago'],
				'texto_5' => $orden['MutualProductoSolicitud']['beneficiario'],
				'texto_6' => $orden['MutualProductoSolicitud']['beneficio_str'],
				'texto_7' => $orden['MutualProductoSolicitud']['proveedor_producto'],
				'texto_8' => $orden['MutualProductoSolicitud']['inicia_en'],
				'texto_9' => $orden['MutualProductoSolicitud']['primer_vto_socio'],
				'texto_10' => $orden['MutualProductoSolicitud']['organismo_desc'],
				'texto_11' => $orden['MutualProductoSolicitud']['permanente'],
				'texto_12' => $orden['MutualProductoSolicitud']['organismo'],			
				'texto_13' => $orden['MutualProductoSolicitud']['beneficiario_tdocndoc'],
				'texto_14' => $orden['MutualProductoSolicitud']['beneficiario_apenom'],
				'texto_15' => $orden['MutualProductoSolicitud']['turno_desc'],
				'texto_19' => $orden['MutualProductoSolicitud']['beneficiario_cuit_cuil'],
				'texto_20' => $orden['MutualProductoSolicitud']['socio_id'],			
				'decimal_1' => $orden['MutualProductoSolicitud']['importe_total'],
				'decimal_2' => $orden['MutualProductoSolicitud']['importe_cuota'],
				'decimal_3' => $orden['MutualProductoSolicitud']['cuotas'],			
				'entero_1' => $orden['MutualProductoSolicitud']['cuotas'],										
			);
                        
			$temp['AsincronoTemporal']['decimal_5'] = round($saldo,2);
			$temp['AsincronoTemporal']['decimal_6'] = round($pendiente,2);
			$temp['AsincronoTemporal']['decimal_7'] = round($saldoAjustado,2);
			$temp['AsincronoTemporal']['decimal_8'] = round($cobrado,2);
			$temp['AsincronoTemporal']['decimal_9'] = round($cobrado_optimo,2);
			$temp['AsincronoTemporal']['entero_3'] = $cuotas;
			$temp['AsincronoTemporal']['entero_4'] = $orden['MutualProductoSolicitud']['orden_descuento_id'];
			$temp['AsincronoTemporal']['entero_5'] = $periodo_corte;
			$temp['AsincronoTemporal']['entero_6'] = $cuotasNoVencidas;
			
			$temp['AsincronoTemporal']['decimal_10'] = round($saldo_0003,2);
			$temp['AsincronoTemporal']['decimal_11'] = round($saldo_0306,2);
			$temp['AsincronoTemporal']['decimal_12'] = round($saldo_0609,2);
			$temp['AsincronoTemporal']['decimal_13'] = round($saldo_0912,2);
			$temp['AsincronoTemporal']['decimal_14'] = round($saldo_1213,2);
			$temp['AsincronoTemporal']['decimal_15'] = round($orden['MutualProductoSolicitud']['importe_total'] - $cobrado_optimo,2);
		
			$temp['AsincronoTemporal']['decimal_16'] = round($saldo_av3,2);
			$temp['AsincronoTemporal']['decimal_17'] = round($saldo_av6,2);
			$temp['AsincronoTemporal']['decimal_18'] = round($saldo_av9,2);
			$temp['AsincronoTemporal']['decimal_19'] = round($saldo_av12,2);
			$temp['AsincronoTemporal']['decimal_20'] = round($saldo_av13,2);
                            
                            
                            
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
		
		
		
	}
	//FIN PROCESO ASINCRONO
	
	####################################################################################################
	# METODOS ESPECIFICOS DEL PROCESO
	####################################################################################################
	
	function getOrdenes(){
		$filtro = "";
		if(!empty($this->optionList)){
			list($producto_id,$tipo_producto,$tipo_orden_dto,$proveedor_id,$impoFijo,$cuotaSocialDiferenciada) = explode("|",$this->optionList);
			$filtro = " AND `MutualProductoSolicitud`.`proveedor_id` = $proveedor_id AND `MutualProductoSolicitud`.`tipo_producto` = '$tipo_producto' ";
		}
		
		
		$this->MutualProductoSolicitud->unbindModel(array('belongsTo' => array('Proveedor','OrdenDescuento')));
		$this->MutualProductoSolicitud->Socio->bindModel(array('belongsTo' => array('Persona')));
		$sql = "SELECT 
				`MutualProductoSolicitud`.`id`, 
				`MutualProductoSolicitud`.`aprobada`, 
				`MutualProductoSolicitud`.`fecha`, 
				`MutualProductoSolicitud`.`fecha_pago`, 
				`MutualProductoSolicitud`.`tipo_orden_dto`, 
				`MutualProductoSolicitud`.`tipo_producto`, 
				`MutualProductoSolicitud`.`proveedor_id`, 
				`MutualProductoSolicitud`.`mutual_producto_id`, 
				`MutualProductoSolicitud`.`estado`, 
				`MutualProductoSolicitud`.`socio_id`, 
				`MutualProductoSolicitud`.`persona_beneficio_id`, 
				`MutualProductoSolicitud`.`importe_total`, 
				`MutualProductoSolicitud`.`cuotas`, 
				`MutualProductoSolicitud`.`importe_cuota`, 
				`MutualProductoSolicitud`.`importe_solicitado`, 
				`MutualProductoSolicitud`.`importe_percibido`, 
				`MutualProductoSolicitud`.`periodo_ini`, 
				`MutualProductoSolicitud`.`periodicidad`, 
				`MutualProductoSolicitud`.`primer_vto_socio`, 
				`MutualProductoSolicitud`.`primer_vto_proveedor`, 
				`MutualProductoSolicitud`.`observaciones`, 
				`MutualProductoSolicitud`.`permanente`, 
				`MutualProductoSolicitud`.`orden_descuento_id`, 
				`MutualProductoSolicitud`.`nro_referencia_proveedor`, 
				`MutualProductoSolicitud`.`sin_cargo`, 
				`MutualProductoSolicitud`.`user_created`, 
				`MutualProductoSolicitud`.`user_modified`, 
				`MutualProductoSolicitud`.`created`, 
				`MutualProductoSolicitud`.`modified`,
                Vendedor.id,PersonaVendedor.cuit_cuil,concat(PersonaVendedor.apellido,', ',PersonaVendedor.nombre) as apenom
				FROM 
					`mutual_producto_solicitudes` AS `MutualProductoSolicitud` 
				LEFT JOIN `socios` AS `Socio` ON (`MutualProductoSolicitud`.`socio_id` = `Socio`.`id`) 
				INNER JOIN `persona_beneficios` AS `PersonaBeneficio` ON (`MutualProductoSolicitud`.`persona_beneficio_id` = `PersonaBeneficio`.`id`) 
				INNER JOIN `personas` AS `Persona` ON (`Socio`.`persona_id` = `Persona`.`id`) 
				INNER JOIN `global_datos` AS `GlobalDato` ON (`MutualProductoSolicitud`.`tipo_producto` = `GlobalDato`.`id`)
                LEFT JOIN `vendedores` AS `Vendedor` ON (`MutualProductoSolicitud`.`vendedor_id` = `Vendedor`.`id`) 
                LEFT JOIN `personas` AS `PersonaVendedor` ON (`Vendedor`.`persona_id` = `PersonaVendedor`.`id`) 
                
				WHERE 
					`MutualProductoSolicitud`.`aprobada` = 1 
					$filtro
					AND `MutualProductoSolicitud`.`fecha` BETWEEN '".$this->fecha_desde."' AND '".$this->fecha_hasta."'
                         ". ( !empty($this->proveedorId) ? " AND `MutualProductoSolicitud`.`proveedor_id` = " . $this->proveedorId . " " : "") ."
                         ". ( !empty($this->codigoOrganismo) ? " AND PersonaBeneficio.codigo_beneficio = '". $this->codigoOrganismo."'" : "")."                                					
				ORDER BY `GlobalDato`.`concepto_1`, `PersonaBeneficio`.`codigo_beneficio`, `Persona`.`apellido` ASC,  `Persona`.`nombre` ASC ";
		$ordenes = $this->MutualProductoSolicitud->query($sql);
		return $ordenes;
	}

}
?>