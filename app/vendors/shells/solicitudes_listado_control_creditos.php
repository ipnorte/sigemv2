<?php 
/**
 * 
 * @author adrian
 *
 *	/usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php solicitudes_listado_control_creditos 10241 -app /home/adrian/dev/www/sigem/app/
 *      C:\xampp\php\php.exe C:\xampp\htdocs\sigem\cake\console\cake.php solicitudes_listado_control_creditos 18983 -app C:\xampp\htdocs\sigem\app\
 *
 */

App::import('Model','v1.Solicitud');
App::import('Model','mutual.OrdenDescuento');
	


class SolicitudesListadoControlCreditosShell extends Shell{
	
	
	var $tasks = array('Temporal');
	
	function main(){
		
		$STOP = 0;
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 
		
		$proveedorId = $asinc->getParametro('p1');
		$periodo = $asinc->getParametro('p2');
		$cuotas = $asinc->getParametro('p3');
		$codigoOrganismo = $asinc->getParametro('p4');
		$codigoEmpresaTurno = $asinc->getParametro('p5');
		list($codigoEmpresa,$turno) = explode("|", $codigoEmpresaTurno);
		
		$todo = $asinc->getParametro('p6');
		$todo = (!empty($todo) && $todo == 1 ? true : false);
		
		$cuotas = (empty($cuotas) ? 0 : $cuotas);
		
		$oORDEN = new OrdenDescuento();
		
//		$this->out($proveedorId ."|".$periodo."|".$cuotas);
		
		$asinc->actualizar(1,100,"CARGANDO ORDENES DE DESCUENTO PARA PROCESAR");
		
// 		$sql = "SELECT id FROM orden_descuentos as OrdenDescuento WHERE proveedor_id = $proveedorId";
		
		$sql = "SELECT OrdenDescuento.id FROM orden_descuentos as OrdenDescuento
				".(!empty($codigoOrganismo) ? "INNER JOIN persona_beneficios as PersonaBeneficio on ( PersonaBeneficio.id = OrdenDescuento.persona_beneficio_id)" : "")."
				WHERE 
					OrdenDescuento.proveedor_id = $proveedorId
					".(!empty($codigoOrganismo) ? " AND PersonaBeneficio.codigo_beneficio = '$codigoOrganismo' " : "")."
					".(!empty($codigoEmpresa) ? " AND PersonaBeneficio.codigo_empresa = '$codigoEmpresa' " : "")."
					".(!empty($turno) ? " AND PersonaBeneficio.turno_pago = '$turno' " : "")."		
					;";
		$ordenes = $oORDEN->query($sql);
		
		if(empty($ordenes)):
			$asinc->fin("**** PROCESO FINALIZADO :: NO EXISTEN REGISTROS PARA PROCESAR ****");
			return;		
		endif;		
		
		$total = count($ordenes);
		$asinc->setTotal($total);
		$i = 0;			
		
		foreach($ordenes as $rId):
		
			$ordenId = $rId['OrdenDescuento']['id'];
			
			$orden = $oORDEN->getOrden($ordenId,$periodo,true);
			$orden = $orden['OrdenDescuento'];
			
			$cuotasAdeudadas = $orden['cuotas'] - $orden['pagadas'];
			$cuotasAdeudadas = ($cuotasAdeudadas <= 0 ? 0 : $cuotasAdeudadas);
			
			if($todo) $cuotas = $cuotasAdeudadas;
			
			if($cuotasAdeudadas == $cuotas && $orden['baja_cuotas'] == 0):
			
//				$this->out($ordenId);
				
//				debug($orden);

				$asinc->actualizar($i,$total,"$i /$total - PROCESANDO " . $orden['proveedor_resumido'] . " | " . $orden['tipo_nro']);
				
				$temp = array();
				$temp['AsincronoTemporal'] = array();
				$temp['AsincronoTemporal']['asincrono_id'] = $asinc->id;				
				$temp['AsincronoTemporal']['clave_1'] = $orden['id'];
				$temp['AsincronoTemporal']['texto_1'] = $orden['persona_tdocndoc'];
				$temp['AsincronoTemporal']['texto_2'] = $orden['persona_apenom'];
				$temp['AsincronoTemporal']['texto_3'] = $orden['tipo_nro'];
				$temp['AsincronoTemporal']['texto_4'] = date('d-m-Y',strtotime($orden['fecha']));
				$temp['AsincronoTemporal']['texto_5'] = $orden['inicia_en'];
				$temp['AsincronoTemporal']['texto_6'] = $orden['proveedor_producto'];
				$temp['AsincronoTemporal']['texto_7'] = $orden['nro_referencia_proveedor'];
				$temp['AsincronoTemporal']['texto_8'] = $orden['organismo'];
				$temp['AsincronoTemporal']['texto_9'] = (!empty($orden['ultimo_cobro']) ? $orden['ultimo_cobro']['periodo_cobro_desc'] : null);
				$temp['AsincronoTemporal']['texto_10'] = (!empty($orden['ultimo_cobro']) ? $orden['ultimo_cobro']['tipo_cobro_desc'] : null);
				$temp['AsincronoTemporal']['texto_11'] = $orden['empresa'];
				$temp['AsincronoTemporal']['texto_12'] = $orden['ultima_calificacion'];
				$temp['AsincronoTemporal']['texto_13'] = $orden['cbu'];
				$temp['AsincronoTemporal']['entero_1'] = $orden['cuotas'];
				$temp['AsincronoTemporal']['entero_2'] = $orden['pagadas'];
				$temp['AsincronoTemporal']['entero_3'] = $orden['cuotas'] - $orden['pagadas'];
				$temp['AsincronoTemporal']['entero_4'] = $orden['vencidas'];
				$temp['AsincronoTemporal']['entero_5'] = $orden['avencer'];
				$temp['AsincronoTemporal']['decimal_1'] = number_format($orden['importe_devengado'],2,".",",");
				$temp['AsincronoTemporal']['decimal_2'] = number_format($orden['importe_pagado'],2,".",",");
				$temp['AsincronoTemporal']['decimal_3'] = number_format($orden['importe_cuota'],2,".",",");
				
//				debug($temp);
				$this->Temporal->grabar($temp);
						
			endif;
			
			$i++;

		
		endforeach;
		
		$asinc->actualizar(100,100,"FINALIZANDO...");
		$asinc->fin("**** PROCESO FINALIZADO ****");			
		
		
	}
	
}

?>