<?php

/**
 * REPORTE DE CONTROL DE LA LIQUIDACION
 * 
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php reporte_control_vencimientos 314 -app /home/adrian/Desarrollo/www/sigem/app/
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php reporte_control_vencimientos 77 -app /home/adrian/dev/www/sigem/app/
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 *
 */

class ReporteControlVencimientosShell extends Shell {

	var $uses = array('Mutual.OrdenDescuentoCuota','Pfyj.PersonaBeneficio');
	
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

		$periodo_ctrl = $asinc->getParametro('p1');
		$codigo_organismo = $asinc->getParametro('p2');
		$proveedor_id = $asinc->getParametro('p3');
        $tipoProducto = $asinc->getParametro('p5');
        $tipoCuota = $asinc->getParametro('p6');
		
		$asinc->actualizar(1,100,"ESPERE, INICIANDO PROCESO...");
		
		
		$this->Temporal->limpiarTabla($pid);
		
		
		$STOP = 0;
		$total = 0;
		$i = 0;
		$asinc->actualizar(2,100,"ESPERE, CONSULTANDO CUOTAS...");		
		
//		$this->out("ADRIAN $periodo_ctrl | $codigo_organismo | $proveedor_id");
		
		$cuotas = $this->cargarCuotas($periodo_ctrl,$codigo_organismo,$proveedor_id,$tipoProducto,$tipoCuota);
		
		$total = count($cuotas);
		$asinc->setTotal($total);
		$i = 0;		
		
//		App::import('model','Mutual.OrdenDescuentoCuota');
//		$oCUOTA = new OrdenDescuentoCuota();

		
		if(!empty($cuotas)):
		
		
			$temp = array();
		
			foreach($cuotas as $cuotaId):
			
				$cuota_id = $cuotaId['OrdenDescuentoCuota']['id'];
            
                $vendedor = "";
                if(!empty($cuotaId['Vendedor']['id'])){
                    $vendedor = "#".$cuotaId['Vendedor']['id'] . " - " . $cuotaId['Persona']['nombre']." ".$cuotaId['Persona']['apellido'];
                }
            
                
			
//				$this->out($cuota_id);
				
//				debug($cuotaId);
				
				$cuota = $this->OrdenDescuentoCuota->getCuota($cuota_id);
				$cuota = $cuota['OrdenDescuentoCuota'];
				
				$mora = $this->OrdenDescuentoCuota->getMoraByOrdenDtoHastaPeriodo($cuota['orden_descuento_id'],$periodo_ctrl);
				
//				debug($mora);
				
				$asinc->actualizar($i,$total,"PROCESANDO CUOTAS|" . $cuota['persona_apenom'] . "|" . $cuota['tipo_nro'] . "|" . $cuota['cuota']);
				
//				debug($cuota);
				
				$empresa = $this->PersonaBeneficio->getEmpresaDescripcion($cuota['persona_beneficio_id']);
				$turno = $this->PersonaBeneficio->getTurnoDescripcion($cuota['persona_beneficio_id']);
				$bancoNom = $this->PersonaBeneficio->getNombreBanco($cuota['persona_beneficio_id']);
				
				$cuota['beneficio'] = str_replace("|"," | ",$cuota['beneficio']);
				
//				$this->out($empresa ." | ".$turno);
				
				$temp['AsincronoTemporal'] = array(
						'asincrono_id' => $asinc->id,
						'clave_1' => 'REPORTE_1',
						'texto_1' => $cuota['persona_tipo_documento']." - ".$cuota['persona_documento'],
						'texto_2' => $cuota['persona_apenom'],
						'texto_3' => $empresa,
						'texto_4' => $turno,				
						'texto_5' => $cuota['tipo_nro'],
						'texto_6' => $cuota['producto_cuota_ref'],
						'texto_7' => $cuota['nro_referencia_proveedor'],				
						'texto_8' => $cuota['numero_odto'],
						'texto_9' => $cuota['cuota'],
						'texto_10' => $cuota['orden_descuento_periodo_ini_d'],
						'texto_11' => $cuota['orden_descuento_primer_vto_socio'],
						'texto_12' => $cuota['orden_descuento_primer_vto_proveedor'],
						'texto_13' => $cuota['proveedor'],
						'texto_14' => $cuota['beneficio'],
						'texto_15' => $bancoNom,
                        'texto_16' => $cuota['organismo'],
                        'texto_17' => $vendedor,
						'entero_1' => $cuota['nro_cuota'],
						'entero_2' => $cuota['orden_descuento_cuotas'],
						'entero_3' => $cuota['orden_descuento_cuotas'] - $cuota['nro_cuota'],
						'decimal_1' => $cuota['orden_descuento_total'],
						'decimal_2' => round($cuota['importe'],2),
						'decimal_3' => round($mora['saldo'],2),
				);

				
//				debug($temp);
				
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
		
		endif;
		
		
//		debug($cuotas);
		

		$asinc->actualizar(100,100,"FINALIZANDO...");
		$asinc->fin("**** PROCESO FINALIZADO ****");
		return;
		

	}
	//FIN PROCESO ASINCRONO
	
	####################################################################################################
	# METODOS ESPECIFICOS DEL PROCESO
	####################################################################################################
	
	function cargarCuotas($periodo,$organismo,$proveedor,$tipoProducto,$tipoCuota){
		

		
//		$this->OrdenDescuentoCuota->unbindModel(array('belongsTo' => array('OrdenDescuento','Proveedor'), 'hasMany' => array('OrdenDescuentoCobroCuota','LiquidacionCuota')));
//		$this->OrdenDescuentoCuota->bindModel(array('belongsTo' => array('PersonaBeneficio')));
//		
//		$conditions = array();
//		$conditions['OrdenDescuentoCuota.periodo'] = $periodo;
//		if(!empty($proveedor))$conditions['OrdenDescuentoCuota.proveedor_id'] = $proveedor;
//		if(!empty($organismo) || $organismo != '')$conditions['PersonaBeneficio.codigo_beneficio'] = $organismo;
//        if(!empty($tipoProducto) || $tipoProducto != '')$conditions['OrdenDescuentoCuota.tipo_producto'] = $tipoProducto;
//        if(!empty($tipoCuota) || $tipoCuota != '')$conditions['OrdenDescuentoCuota.tipo_cuota'] = $tipoCuota;
//        
////		$conditions['OrdenDescuentoCuota.estado'] = 'A';
//		$conditions['OrdenDescuentoCuota.situacion'] = 'MUTUSICUMUTU';
//		
//		$cuotas = $this->OrdenDescuentoCuota->find('all',array('conditions' => $conditions, 'fields' => array('OrdenDescuentoCuota.id,OrdenDescuentoCuota.orden_descuento_id')));	
        
        
        $sql = "select Vendedor.id,Persona.documento,Persona.apellido,Persona.nombre, OrdenDescuentoCuota.id,OrdenDescuentoCuota.orden_descuento_id from orden_descuento_cuotas OrdenDescuentoCuota
                inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
                inner join orden_descuentos OrdenDescuento on (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
                left join mutual_producto_solicitudes MutualProductoSolicitud on (MutualProductoSolicitud.id = OrdenDescuento.numero)
                left join vendedores Vendedor on (Vendedor.id = MutualProductoSolicitud.vendedor_id)
                left join personas Persona on (Persona.id = Vendedor.persona_id)
                where
                OrdenDescuentoCuota.periodo = '$periodo'
                ".(!empty($proveedor) ? "and OrdenDescuentoCuota.proveedor_id = $proveedor " : "")."
                ".(!empty($organismo) || $organismo != '' ? " and PersonaBeneficio.codigo_beneficio = '$organismo' " : "")."  
                ".(!empty($tipoProducto) || $tipoProducto != '' ? " and OrdenDescuentoCuota.tipo_producto = '$tipoProducto' " : "")."
                ".(!empty($tipoCuota) || $tipoCuota != '' ? " and OrdenDescuentoCuota.tipo_cuota = '$tipoCuota' " : "")."    
                and OrdenDescuentoCuota.estado <> 'B'";
        
        $cuotas = $this->OrdenDescuentoCuota->query($sql);
		
		return $cuotas;
		
		
	}
	

	function getMoraByOrdenDto($orden_dto_id,$periodo){
		

		
	}
	

}
?>