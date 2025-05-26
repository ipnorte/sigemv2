<?php

/**
 * Listado Cobros
 * Genera un listado por tipo de cobro, proveedor y entre fechas
 * <br/>
 * <li>PARAMETRO_1 = tipo_cobro</li>
 * <li>PARAMETRO_2 = proveedor_id</li>
 * <li>PARAMETRO_3 = fecha_desde</li>
 * <li>PARAMETRO_4 = fecha_hasta</li>
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 */

// /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php listado_cobros 532 -app /home/adrian/Desarrollo/www/sigem/app/
// /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php listado_cobros 4885 -app /home/adrian/dev/www/sigem/app/
// /usr/bin/php5 /home/mutualam/public_html/sigem/cake/console/cake.php listado_cobros 22263 -app /home/mutualam/public_html/sigem/app/
// /usr/bin/php5 /home/adrian/Trabajo/www/sigemv2/cake/console/cake.php listado_cobros 1368 -app /home/adrian/Trabajo/www/sigemv2/app/

class ListadoCobrosShell extends Shell {
	
	var $tipo_cobro;
	var $fecha_desde;
	var $fecha_hasta;
	var $proveedor_id;
        var $codigo_organismo;
	
	/**
	 * Referencia a Modelos que usa
	 * @var array
	 */
	var $uses = array('Mutual.OrdenDescuentoCobro','Proveedores.ProveedorComision');
	
	/**
	 * Referencia a tareas que usa
	 * @var array
	 */
	var $tasks = array('Temporal');
	
	/**
	 * Main
	 * Metodo principal
	 * @return unknown_type
	 */
	function main() {
	    
	    Configure::write('debug', 1);
		
		$STOP = 0;
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 

		$this->tipo_cobro		= $asinc->getParametro('p1');
		$this->fecha_desde		= $asinc->getParametro('p2');
		$this->fecha_hasta		= $asinc->getParametro('p3');
		$this->proveedor_id		= $asinc->getParametro('p4');
                $this->codigo_organismo		= $asinc->getParametro('p5');
		
		$asinc->actualizar(0,100,"ESPERE, INICIANDO PROCESO...");
		$STOP = 0;
		$total = 0;
		$i = 0;
		$asinc->actualizar(1,100,"ESPERE, CONSULTANDO COBROS DEL PERIODO...");
		
		//limpio la tabla temporal
		if(!$this->Temporal->limpiarTabla($asinc->id)){
			$asinc->fin("SE PRODUJO UN ERROR...");
			return;
		}

		$cobros = $this->getCobros();
		$total = count($cobros);
		$asinc->setTotal($total);
		$i = 0;	

		$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
	    $FAC_ELECTRONICA = false;

		if(isset($INI_FILE['general']['factura_electronica']) && $INI_FILE['general']['factura_electronica'] != 0){
		    $FAC_ELECTRONICA = TRUE;
		} 
		
		$temp = array();
		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();		

		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();		
		
		foreach($cobros as $cobro):
		
// 			debug($cobro);
		
// 			$cuota =  $oCUOTA->getCuota($cobro['OrdenDescuentoCobro']['orden_descuento_cuota_id']);
// 			$cuota = $cuota['OrdenDescuentoCuota'];
			
// 			$cobro['OrdenDescuentoCobro']['numero_odto'] = $cuota['numero_odto'];
// 			$cobro['OrdenDescuentoCobro']['tipo_nro'] = $cuota['tipo_nro'];
// 			$cobro['OrdenDescuentoCobro']['producto_cuota'] = $cuota['producto_cuota'];
// 			$cobro['OrdenDescuentoCobro']['proveedor_producto'] = $cuota['proveedor_producto'];
// 			$cobro['OrdenDescuentoCobro']['tipo_cuota_desc'] = $cuota['tipo_cuota_desc'];
// 			$cobro['OrdenDescuentoCobro']['cuota'] = $cuota['cuota'];
// 			$cobro['OrdenDescuentoCobro']['periodo'] = $cuota['periodo'];
// 			$cobro['OrdenDescuentoCobro']['cuota'] = $cuota['cuota'];
// 			$cobro['OrdenDescuentoCobro']['importe_cuota'] = $cuota['importe'];
// 			$cobro['OrdenDescuentoCobro']['pagado_acumulado'] = $cuota['pagado'];
// 			$cobro['OrdenDescuentoCobro']['saldo_actual'] = $cuota['saldo_cuota'];
// 			$cobro['OrdenDescuentoCobro']['organismo'] = $cuota['organismo'];
// 			$cobro['OrdenDescuentoCobro']['beneficio'] = $cuota['beneficio'];
// 			$cobro['OrdenDescuentoCobro']['nro_referencia_proveedor'] = $cuota['nro_referencia_proveedor'];
			
		
			
			//busco la comision
			$porcenajeComision = 0;
			$comision = 0;
			
//			if($cobro['OrdenDescuentoCobro']['tipo_cobro'] == 'MUTUTCOBCAJA'):
//				$porcenajeComision = $this->ProveedorComision->getComision($cuota['codigo_organismo'],$cuota['proveedor_id'],$cuota['tipo_producto'],$cuota['tipo_cuota']);
//				$comision = $cobro['OrdenDescuentoCobro']['importe'] * $porcenajeComision / 100;
//			endif;
			$asinc->actualizar($i,$total,"$i / $total - PROCESANDO >> " . $cobro['Proveedor']['razon_social'] . ' - ' . $cobro[0]['apenom']);
			
			
			$temp['AsincronoTemporal'] = array(
										'asincrono_id' => $asinc->id,
                                        'clave_1' => $cobro['OrdenDescuentoCobro']['anulado'],
										'texto_1' => $cobro['GlobalDato']['tipo_cobro_desc'],
										'texto_2' => date('d-m-Y',strtotime($cobro['OrdenDescuentoCobro']['fecha'])),
										'texto_3' => $cobro['Proveedor']['razon_social'],
										'texto_4' => $cobro['Persona']['documento'],
										'texto_5' => $cobro[0]['apenom'],
										'texto_6' => $cobro[0]['tipo_nro'],
										'texto_7' => $cobro[0]['proveedor_producto'],
										'texto_8' => $cobro['tipoCuota']['tipo_cuota_desc'],
										'texto_9' => $cobro[0]['cuota'],
										'texto_10' => $cobro['OrdenDescuentoCuota']['periodo'],
										'texto_11' => $cobro['Organismo']['organismo'],
										'texto_13' => $cobro['OrdenDescuento']['nro_referencia_proveedor'],
                                        'texto_14' => $cobro[0]['recibo'],
                                        'texto_15' => ($cobro['OrdenDescuentoCobro']['anulado'] == '1' ? '*** ANULADO ***' : ''),
										'decimal_1' => $cobro['OrdenDescuentoCobroCuota']['importe'],
										'decimal_2' => $cobro['OrdenDescuentoCuota']['importe'],
										'decimal_5' => $cobro['OrdenDescuentoCobroCuota']['alicuota_comision_cobranza'],
										'decimal_6' => $cobro['OrdenDescuentoCobroCuota']['comision_cobranza'],
										'decimal_7' => round($cobro['OrdenDescuentoCobroCuota']['importe'] - $cobro['OrdenDescuentoCobroCuota']['comision_cobranza'],2),
			                            'entero_1' => $cobro['OrdenDescuentoCobroCuota']['proveedor_id'],
										'entero_2' => $cobro['OrdenDescuento']['numero'],
			);			
			
			if($FAC_ELECTRONICA) {
			    $temp['AsincronoTemporal']['decimal_8'] = $cobro[0]['iva'];
			    $temp['AsincronoTemporal']['decimal_9'] = $cobro[0]['neto'];
			    $temp['AsincronoTemporal']['decimal_10'] = $cobro[0]['capital'];
			}
			
// 			debug($temp);
			
			if(!$this->Temporal->grabar($temp)){
				$STOP = 1;
				break;
			}			
			
			if($asinc->detenido()){
				$STOP = 1;
				break;
			}			
			
			$i++;				
			
		
		endforeach;
//
//			$asinc->actualizar($i,$total,"$i / $total - PROCESANDO >> " . $cobro['OrdenDescuentoCobro']['razon_social'] . ' - ' . $cobro['OrdenDescuentoCobro']['concepto_1']);
//		
//			$temp['AsincronoTemporal'] = array(
//										'asincrono_id' => $asinc->id,
//										'texto_1' => $cobro['OrdenDescuentoCobro']['tipo_cobro'],
//										'texto_2' => $cobro['OrdenDescuentoCobro']['concepto_1'],
//										'texto_3' => $cobro['OrdenDescuentoCobro']['razon_social'],
//										'decimal_1' => $cobro['OrdenDescuentoCobro']['importe'],
//										'entero_1' => $cobro['OrdenDescuentoCobro']['proveedor_id']
//										
//			);			
//		
//			debug($temp);
//			if(!$this->Temporal->grabar($temp)){
//				$STOP = 1;
//				break;
//			}			
//			
//			if($asinc->detenido()){
//				$STOP = 1;
//				break;
//			}			
//			
//			$i++;			
//		
//		endforeach;
		

//		if($STOP == 1){
//			$asinc->actualizar(100,100,"SE PRODUJO UN ERROR...");
//			return;
//		}		
		
		if($STOP == 0){
			$asinc->actualizar($i,$total,"FINALIZANDO...");
			$asinc->fin("**** PROCESO FINALIZADO ****");
		}
		
		return;
		
	}
	//FIN PROCESO ASINCRONO
	
	####################################################################################################
	# METODOS ESPECIFICOS DEL PROCESO
	####################################################################################################

	function getCobros(){

// 		$sql = "	SELECT 
// 						OrdenDescuentoCobroCuota.proveedor_id,
// 						OrdenDescuentoCobroCuota.alicuota_comision_cobranza,
// 						OrdenDescuentoCobroCuota.comision_cobranza,
// 						OrdenDescuentoCobro.tipo_cobro,
// 						OrdenDescuentoCobro.fecha,
//                                                 OrdenDescuentoCobro.anulado,
// 						GlobalDato.concepto_1, 
// 						Proveedor.razon_social, 
// 						OrdenDescuentoCobroCuota.importe,
// 						OrdenDescuentoCuota.id,
// 						Persona.tipo_documento,
// 						Persona.documento,
// 						Persona.apellido,
// 						Persona.nombre,
//                         Recibo.tipo_documento,
//                         Recibo.letra,
//                         Recibo.sucursal,
//                         Recibo.nro_recibo
// 					FROM orden_descuento_cobros as OrdenDescuentoCobro
// 						LEFT JOIN orden_descuento_cobro_cuotas as OrdenDescuentoCobroCuota on (OrdenDescuentoCobroCuota.orden_descuento_cobro_id = OrdenDescuentoCobro.id)
// 						LEFT JOIN proveedores as Proveedor on (OrdenDescuentoCobroCuota.proveedor_id = Proveedor.id)
// 						LEFT JOIN global_datos as GlobalDato on (GlobalDato.id = OrdenDescuentoCobro.tipo_cobro)
// 						LEFT JOIN orden_descuento_cuotas as OrdenDescuentoCuota on (OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id)
// 						LEFT JOIN socios as Socio on (Socio.id = OrdenDescuentoCuota.socio_id)
// 						LEFT JOIN personas as Persona on (Persona.id = Socio.persona_id)
//                                                 LEFT JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
//                                                 LEFT JOIN recibos as Recibo on (Recibo.id = OrdenDescuentoCobro.recibo_id)
// 					WHERE 
// 						".(!empty($this->tipo_cobro) ? " OrdenDescuentoCobro.tipo_cobro = '".$this->tipo_cobro."' AND " : " ")."
// 						OrdenDescuentoCobro.fecha BETWEEN '".$this->fecha_desde."' AND '".$this->fecha_hasta."'
// 						".(!empty($this->proveedor_id) ? "AND OrdenDescuentoCobroCuota.proveedor_id = '".$this->proveedor_id."'" : "")."
//                                                 ".(!empty($this->codigo_organismo) ? "AND PersonaBeneficio.codigo_beneficio = '".$this->codigo_organismo."'" : "")."    
// 					ORDER BY 
// 						OrdenDescuentoCobro.tipo_cobro,Proveedor.razon_social,Persona.apellido,Persona.nombre,OrdenDescuentoCuota.orden_descuento_id,OrdenDescuentoCuota.nro_cuota";
// // 		debug($sql);
// 		$cobros = $this->OrdenDescuentoCobro->query($sql);
// 		foreach($cobros as $idx => $cobro):
// 			$cobro['OrdenDescuentoCobro']['proveedor_id'] = $cobro['OrdenDescuentoCobroCuota']['proveedor_id'];
// 			$cobro['OrdenDescuentoCobro']['medio_cobro'] = $cobro['GlobalDato']['concepto_1'];
// 			$cobro['OrdenDescuentoCobro']['razon_social'] = $cobro['Proveedor']['razon_social'];
// 			$cobro['OrdenDescuentoCobro']['importe'] = $cobro['OrdenDescuentoCobroCuota']['importe'];
// 			$cobro['OrdenDescuentoCobro']['orden_descuento_cuota_id'] = $cobro['OrdenDescuentoCuota']['id'];
// //			$cobro['OrdenDescuentoCobro']['tipo_documento'] = $this->OrdenDescuentoCobro->GlobalDato("concepto_1",$cobro['Persona']['tipo_documento']);
// 			$cobro['OrdenDescuentoCobro']['documento'] = $this->OrdenDescuentoCobro->GlobalDato("concepto_1",$cobro['Persona']['tipo_documento']).' '.$cobro['Persona']['documento'];
// 			$cobro['OrdenDescuentoCobro']['apenom'] = $cobro['Persona']['apellido'].', '.$cobro['Persona']['nombre'];
// 			$cobro['OrdenDescuentoCobro']['alicuota_comision_cobranza'] = $cobro['OrdenDescuentoCobroCuota']['alicuota_comision_cobranza'];
// 			$cobro['OrdenDescuentoCobro']['comision_cobranza'] = $cobro['OrdenDescuentoCobroCuota']['comision_cobranza'];
// //            $cobro['OrdenDescuentoCobro']['recibo'] = $cobro['Recibo']['tipo_documento'] . " " . $cobro['Recibo']['letra'] ." ". $cobro['Recibo']['sucursal'] . "-" . $cobro['Recibo']['nro_recibo'];
//             $cobro['OrdenDescuentoCobro']['recibo'] = $cobro['Recibo']['sucursal'] . "-" . $cobro['Recibo']['nro_recibo'];
// 			$cobros[$idx] = $cobro;
			
// 		endforeach;
		
// 		$cobros = Set::extract('/OrdenDescuentoCobro',$cobros);

	    
	    $sql =  "SELECT 
	OrdenDescuentoCobroCuota.proveedor_id,
	OrdenDescuentoCobroCuota.alicuota_comision_cobranza,
	OrdenDescuentoCobroCuota.comision_cobranza,
	OrdenDescuentoCobro.tipo_cobro,
	OrdenDescuentoCobro.fecha,
	OrdenDescuentoCobro.anulado,
	GlobalDato.concepto_1 as tipo_cobro_desc, 
	Proveedor.razon_social, 
	OrdenDescuentoCobroCuota.importe,
ROUND(OrdenDescuentoCobroCuota.importe * (OrdenDescuentoCuota.iva / OrdenDescuentoCuota.importe),2) AS iva,
ROUND(OrdenDescuentoCobroCuota.importe * (OrdenDescuentoCuota.interes / OrdenDescuentoCuota.importe),2) AS neto,
OrdenDescuentoCobroCuota.importe - (
ROUND(OrdenDescuentoCobroCuota.importe * (OrdenDescuentoCuota.iva / OrdenDescuentoCuota.importe),2) +
ROUND(OrdenDescuentoCobroCuota.importe * (OrdenDescuentoCuota.interes / OrdenDescuentoCuota.importe),2)) AS capital,    
	OrdenDescuentoCuota.id,
	Persona.tipo_documento,
	Persona.documento,
	Persona.apellido,
	Persona.nombre,
concat(Persona.apellido,', ',Persona.nombre) as apenom,
	concat(Recibo.tipo_documento,' ',Recibo.letra,' ',Recibo.sucursal,'-',Recibo.nro_recibo) as recibo,
tipoCuota.concepto_1 as tipo_cuota_desc,
tipoProducto.concepto_1 as tipo_producto_desc,
concat(Proveedor.razon_social,'/',tipoProducto.concepto_1) as proveedor_producto ,
concat(lpad(OrdenDescuentoCuota.nro_cuota,2,0),'/',lpad(OrdenDescuento.cuotas,2,0)) as cuota,
Organismo.concepto_1 as organismo,
concat(OrdenDescuento.tipo_orden_dto,' #',OrdenDescuento.numero) as tipo_nro,
OrdenDescuento.numero,
OrdenDescuento.nro_referencia_proveedor,
OrdenDescuentoCuota.importe,
OrdenDescuentoCuota.periodo
FROM orden_descuento_cobros as OrdenDescuentoCobro
	INNER JOIN orden_descuento_cobro_cuotas as OrdenDescuentoCobroCuota on (OrdenDescuentoCobroCuota.orden_descuento_cobro_id = OrdenDescuentoCobro.id)
	INNER JOIN proveedores as Proveedor on (OrdenDescuentoCobroCuota.proveedor_id = Proveedor.id)
	LEFT JOIN global_datos as GlobalDato on (GlobalDato.id = OrdenDescuentoCobro.tipo_cobro)
	INNER JOIN orden_descuento_cuotas as OrdenDescuentoCuota on (OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id)
    LEFT JOIN orden_descuentos OrdenDescuento on OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id
	LEFT JOIN socios as Socio on (Socio.id = OrdenDescuentoCuota.socio_id)
	LEFT JOIN personas as Persona on (Persona.id = Socio.persona_id)
	LEFT JOIN persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
	LEFT JOIN recibos as Recibo on (Recibo.id = OrdenDescuentoCobro.recibo_id)
	INNER JOIN global_datos tipoCuota on tipoCuota.id = OrdenDescuentoCuota.tipo_cuota
	INNER JOIN global_datos tipoProducto on tipoProducto.id = OrdenDescuentoCuota.tipo_producto
	INNER JOIN global_datos Organismo on Organismo.id = PersonaBeneficio.codigo_beneficio
WHERE 
    ".(!empty($this->tipo_cobro) ? " OrdenDescuentoCobro.tipo_cobro = '".$this->tipo_cobro."' AND " : " ")."
    OrdenDescuentoCobro.fecha BETWEEN '".$this->fecha_desde."' AND '".$this->fecha_hasta."'
    ".(!empty($this->proveedor_id) ? "AND OrdenDescuentoCobroCuota.proveedor_id = '".$this->proveedor_id."'" : "")."
    ".(!empty($this->codigo_organismo) ? "AND PersonaBeneficio.codigo_beneficio = '".$this->codigo_organismo."'" : "")."
ORDER BY 
    OrdenDescuentoCobro.fecha ASC
";

	    $cobros = $this->OrdenDescuentoCobro->query($sql);
		return $cobros;
	}

}
?>