<?php

/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php listado_ordenes_dto_entre_fechas 1276 -app /home/adrian/dev/www/sigem/app/
 * 
 */

class ListadoOrdenesDtoEntreFechasShell extends Shell {

	var $fecha_desde;
	var $fecha_hasta;
        var $periodo_corte;
    var $proveedorId;
    var $codigoOrganismo;  
    var $tipoProducto;
	var $uses = array('Mutual.OrdenDescuento');
	
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
                $this->proveedorId		= $asinc->getParametro('p3');
		$this->codigoOrganismo	= $asinc->getParametro('p4');    
                $this->periodo_corte	= $asinc->getParametro('p5');
                $this->tipoProducto	= $asinc->getParametro('p6');
		
		$asinc->actualizar(0,100,"ESPERE, INICIANDO PROCESO...");
		$STOP = 0;
		$total = 0;
		$i = 0;
		$asinc->actualizar(0,100,"ESPERE, CONSULTANDO ORDENES A PROCESAR...");
		
		//limpio la tabla temporal
		if(!$this->Temporal->limpiarTabla($asinc->id)){
			$asinc->fin("SE PRODUJO UN ERROR...");
			return;
		}

		$ordenes = $this->getOrdenesDto();
		$total = count($ordenes);
		$asinc->setTotal($total);
		$i = 0;	

		$temp = array();
		foreach($ordenes as $orden){
			
//			$orden = $this->OrdenDescuento->armaDatosByOrdenSinSaldos($orden);

//			debug($orden);
			
			$asinc->actualizar($i,$total,"$i / $total - PROCESANDO >> " . $orden[0]['tipo_nro'] . ' - ' . $orden[0]['apenom']);
			
			$temp['AsincronoTemporal'] = array(
                                        'asincrono_id' => $asinc->id,
                                        'texto_1' => $orden['OrdenDescuento']['tipo_orden_dto'],
                                        'texto_2' => $orden['OrdenDescuento']['id'],
                                        'texto_3' => $orden[0]['tipo_nro'],
                                        'texto_4' => $orden[0]['apenom'],
                                        'texto_5' => $orden[0]['proveedor_producto'],
                                        'texto_6' => $orden['OrdenDescuento']['periodo_ini'],
                                        'texto_7' => $orden['OrdenDescuento']['primer_vto_socio'],
                                        'texto_8' => $orden['OrdenDescuento']['primer_vto_proveedor'],
                                        'texto_9' => ($orden['OrdenDescuento']['permanente'] == 1 ? 'SI':'NO'),
                                        'texto_10' => $orden['Organismo']['concepto_1'],
                                        'texto_11' => $orden['OrdenDescuento']['fecha'],
                                        'texto_12' => $orden['OrdenDescuento']['tipo_orden_dto'],
                                        'texto_13' => $orden[0]['todc_ndoc'],
                                        'texto_14' => $orden[0]['apenom'],
                                        'texto_15' => $orden['Proveedor']['razon_social'],
                                        'texto_16' => ($orden['OrdenDescuento']['activo'] == 1 ? '':'***ANULADA***'),
                                        'texto_17' => $orden['OrdenDescuento']['socio_id'],
                                        'texto_18' => $orden['Persona']['cuit_cuil'],
                                        'decimal_1' => $orden['OrdenDescuento']['importe_total'],
                                        'decimal_2' => $orden['OrdenDescuento']['importe_cuota'],
                                        'decimal_3' => $orden['OrdenDescuento']['cuotas'],
                                        'decimal_4' => $orden[0]['devengado'],
                                        'decimal_5' => $orden[0]['devengado'] - $orden[0]['pagado'] - $orden[0]['anulado'],
                                        'decimal_6' => $orden[0]['anulado'],
                                        'decimal_7' => $orden[0]['pagado'],
                                        'entero_1' => $orden['OrdenDescuento']['cuotas'],
										
			);
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
	
	function getOrdenesDto(){
		$this->OrdenDescuento->unbindModel(array('belongsTo' => array('Proveedor'),'hasMany' => array('OrdenDescuentoCuota')));
		$this->OrdenDescuento->Socio->bindModel(array('belongsTo' => array('Persona')));
		$sql = "SELECT 
					`OrdenDescuento`.`id`, 
					`OrdenDescuento`.`fecha`, 
					`OrdenDescuento`.`tipo_orden_dto`, 
					`OrdenDescuento`.`numero`, 
					`OrdenDescuento`.`tipo_producto`, 
					`OrdenDescuento`.`proveedor_id`, 
					`OrdenDescuento`.`mutual_producto_id`, 
					`OrdenDescuento`.`socio_id`, 
					`OrdenDescuento`.`persona_beneficio_id`, 
					`OrdenDescuento`.`periodo_ini`, 
					`OrdenDescuento`.`periodicidad`, 
					`OrdenDescuento`.`importe_total`, 
					`OrdenDescuento`.`importe_cuota`, 
					`OrdenDescuento`.`primer_vto_socio`, 
					`OrdenDescuento`.`primer_vto_proveedor`, 
					`OrdenDescuento`.`cuotas`, 
					`OrdenDescuento`.`activo`, 
					`OrdenDescuento`.`permanente`, 
					`OrdenDescuento`.`nro_referencia_proveedor`, 
					`OrdenDescuento`.`nro_orden_referencia`, 
					`OrdenDescuento`.`codigo_comercio_referencia`, 
					`OrdenDescuento`.`comision_cobranza`, 
					`OrdenDescuento`.`comision_colocacion`, 
					`OrdenDescuento`.`reprogramada`, 
					`OrdenDescuento`.`reasignada`, 
					`OrdenDescuento`.`observaciones`, 
					`OrdenDescuento`.`conciliada_proveedor`, 
					`OrdenDescuento`.`fecha_conciliacion_proveedor`, 
					`OrdenDescuento`.`mora_tecnica`, 
					`OrdenDescuento`.`user_created`, 
					`OrdenDescuento`.`user_modified`, 
					`OrdenDescuento`.`created`, 
					`OrdenDescuento`.`modified`, 
					`OrdenDescuento`.`ztmp_odc_id` ,

                                        Organismo.concepto_1,
                                        concat(TDoc.concepto_1,' - ', Persona.documento) as todc_ndoc,
                                        concat(Persona.apellido,', ',Persona.nombre) as apenom,
                                        Persona.cuit_cuil,
                                        concat(OrdenDescuento.tipo_orden_dto,' #',OrdenDescuento.numero) as tipo_nro,
                                        Proveedor.razon_social,
                                        TipoProducto.concepto_1 as producto,
                                        concat(Proveedor.razon_social,' / ',TipoProducto.concepto_1) as proveedor_producto,

                                        ifnull((select sum(ocu.importe) from orden_descuento_cuotas ocu
                                        where ocu.orden_descuento_id = OrdenDescuento.id
                                        and ocu.periodo <= '".$this->periodo_corte."'),0) as devengado,
                                        ifnull((select sum(ocu.importe) from orden_descuento_cuotas ocu
                                        where ocu.orden_descuento_id = OrdenDescuento.id
                                        and ocu.estado IN ('B', 'D') 
                                        and ocu.periodo <= '".$this->periodo_corte."'), 0) as anulado,                                             
                                        ifnull((select sum(ocu.importe) from orden_descuento_cuotas ocu
                                        where ocu.orden_descuento_id = OrdenDescuento.id
                                        and ocu.periodo <= '".$this->periodo_corte."'),0)
                                        - (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
                                        inner join orden_descuento_cuotas c on (c.id = cc.orden_descuento_cuota_id)
                                        inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
                                        where c.orden_descuento_id = OrdenDescuento.id and co.periodo_cobro <= '".$this->periodo_corte."'
                                        ) as saldo_orden,
                                        IFNULL((
                                                select
                                                        ifnull(sum(cc.importe), 0)
                                                from
                                                        orden_descuento_cobro_cuotas cc
                                                inner join orden_descuento_cuotas c on
                                                        (c.id = cc.orden_descuento_cuota_id)
                                                inner join orden_descuento_cobros co on
                                                        (co.id = cc.orden_descuento_cobro_id)
                                                where
                                                        c.orden_descuento_id = OrdenDescuento.id
                                                        and co.periodo_cobro <= '".$this->periodo_corte."'
                                                                                ),0) pagado                                        
				FROM 
                                    `orden_descuentos` AS `OrdenDescuento` 
                                    inner join socios Socio on Socio.id = OrdenDescuento.socio_id
                                    inner join personas Persona on Persona.id = Socio.persona_id
                                    inner join persona_beneficios PersonaBeneficio on PersonaBeneficio.id = OrdenDescuento.persona_beneficio_id

                                    inner join global_datos TDoc on (TDoc.id = Persona.tipo_documento)
                                    inner join proveedores Proveedor on (Proveedor.id = OrdenDescuento.proveedor_id)
                                    inner join global_datos TipoProducto on (TipoProducto.id = OrdenDescuento.tipo_producto)
                                    inner join global_datos Organismo on (Organismo.id = PersonaBeneficio.codigo_beneficio)

				WHERE 
                                        ifnull(`OrdenDescuento`.`nueva_orden_descuento_id`, 0) = 0 
                                        AND `OrdenDescuento`.`fecha` BETWEEN '".$this->fecha_desde."' AND '".$this->fecha_hasta."'   
                                                    
                                                    
                         ". ( !empty($this->proveedorId) ? " AND `OrdenDescuento`.`proveedor_id` = " . $this->proveedorId . " " : "") ."
                         ". ( !empty($this->tipoProducto) ? " AND `OrdenDescuento`.`tipo_producto` = '" . $this->tipoProducto . "' " : "") ."    
                         ". ( !empty($this->codigoOrganismo) ? " AND PersonaBeneficio.codigo_beneficio = '". $this->codigoOrganismo."'" : "")."                                
				-- ORDER BY `OrdenDescuento`.`tipo_orden_dto`, `PersonaBeneficio`.`codigo_beneficio`, `Persona`.`apellido` ASC,  `Persona`.`nombre` ASC ";
		$ordenes = $this->OrdenDescuento->query($sql);
		return $ordenes;
	}

}
?>