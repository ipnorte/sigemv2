<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 *
 * /opt/lampp/bin/php-5.2.8 /opt/lampp/htdocs/aman2/cake/console/cake.php tmp_genera_cuotas_migracion 80 -app /opt/lampp/htdocs/aman2/app/
 *
 */

class TmpArmaVencimientosShell extends Shell{
	
	var $uses 		= array('Mutual.OrdenDescuento','Mutual.OrdenDescuentoCuota','Pfyj.Socio','Config.GlobalDato');



	function main(){
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}		
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 

		$asinc->actualizar(0,100,"ESPERE, INICIANDO PROCESO...");
		
		$cantidad = 500;
		$offSet = 0;
		
		while(1 == 1){
			
			print 'PROCESANDO ---> '.$offSet .' :: '.$cantidad ."\n";
			
			$this->OrdenDescuento->unbindModel(array('belongsTo' => array('Proveedor','Socio'),'hasMany'=>array('OrdenDescuentoCuota')));
			$ordenes = $this->OrdenDescuento->find('all',array(
														'order' => 'OrdenDescuento.socio_id',
														'limit' => "$offSet,$cantidad"
			));
			
			if(count($ordenes)==0)break;
			
			foreach($ordenes as $orden){
				
				if(empty($orden['OrdenDescuento']['primer_vto_socio']))$orden = $this->generaVencimientosOrden($orden);
				$orden = $this->generaVencimientosOrden($orden);
				if(!$this->OrdenDescuento->save($orden))break;
				$this->generaVencimientoCuotas($orden);
				
				if($asinc->detenido()) break;
				
			}			
			
			$offSet += $cantidad;
			
		}
		
		
		
		if(!$asinc->detenido()){
			$asinc->fin("**** PROCESO FINALIZADO ****");			
		}
		
	
	}
	
	/**
	 * calcula el primer vencimiento para el socio y para el proveedor de una orden de descuento
	 * @param unknown_type $orden
	 * @return unknown
	 */
	function generaVencimientosOrden($orden){
		
		$proveedor_id = $orden['OrdenDescuento']['proveedor_id'];
		$codigo_organismo = $this->__getOrganismoBeneficio($orden['OrdenDescuento']['persona_beneficio_id']);
		$periodoLiquidado = $orden['OrdenDescuento']['periodo_ini'];
		$fechaInicioOdto = $orden['OrdenDescuento']['fecha'];
		
		// calcular el primer vencimiento del socio y del proveedor
		App::import('Model','Proveedores.ProveedorVencimiento');
		$oVto = new ProveedorVencimiento();	
		
		$vtos = $oVto->calculaVencimiento($proveedor_id,$orden['OrdenDescuento']['persona_beneficio_id'],$orden['OrdenDescuento']['fecha']);
		
		$orden['OrdenDescuento']['primer_vto_socio'] = $vtos['vto_primer_cuota_socio'];
		$orden['OrdenDescuento']['primer_vto_proveedor'] = $vtos['vto_primer_cuota_proveedor'];
		
		return $orden;
		
	}
	
	
	function generaVencimientoCuotas($orden){
		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();

		$mkIniVtoSocio = mktime(0,0,0,date('m',strtotime($orden['OrdenDescuento']['primer_vto_socio'])),date('d',strtotime($orden['OrdenDescuento']['primer_vto_socio'])),date('Y',strtotime($orden['OrdenDescuento']['primer_vto_socio'])));
		$mkIniVtoProv = mktime(0,0,0,date('m',strtotime($orden['OrdenDescuento']['primer_vto_proveedor'])),date('d',strtotime($orden['OrdenDescuento']['primer_vto_proveedor'])),date('Y',strtotime($orden['OrdenDescuento']['primer_vto_proveedor'])));
		
		$i = 0;

		//saco las cuotas de la orden de descuento ordenadas por periodo
		$oCUOTA->unbindModel(array('belongsTo'=>array('Proveedor','Socio'),'hasMany'=>array('OrdenDescuentoCobroCuota','LiquidacionCuota')));
		$cuotas = $oCUOTA->find('all',array(
											'conditions' => array(
																	'OrdenDescuentoCuota.orden_descuento_id' => $orden['OrdenDescuento']['id']
											),
											'order' => array('OrdenDescuentoCuota.periodo')
		));
		foreach($cuotas as $cuota){
			$cuota['OrdenDescuentoCuota']['vencimiento'] = date('Y-m-d',$this->addMonthToDate($mkIniVtoSocio,$i));
			$cuota['OrdenDescuentoCuota']['vencimiento_proveedor'] = date('Y-m-d',$this->addMonthToDate($mkIniVtoProv,$i));
			if(!$oCUOTA->save($cuota))break;
			$i++;
		}
	}
	

	
	
	/**
	 * devuelve el codigo de organismo de un beneficio
	 * @param $persona_beneficio_id
	 * @return unknown_type
	 */
	function __getOrganismoBeneficio($persona_beneficio_id){
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBen = new PersonaBeneficio();
		$beneficio = $oBen->query("select PersonaBeneficio.codigo_beneficio from persona_beneficios as PersonaBeneficio where PersonaBeneficio.id = $persona_beneficio_id");
		if(!empty($beneficio)) return $beneficio[0]['PersonaBeneficio']['codigo_beneficio'];
		else return false;
	}	
	
    public function addMonthToDate($timeStamp, $totalMonths=1){
        // You can add as many months as you want. mktime will accumulate to the next year.
        $thePHPDate = getdate($timeStamp); // Covert to Array   
        $thePHPDate['mon'] = $thePHPDate['mon']+$totalMonths; // Add to Month   
        $timeStamp = mktime($thePHPDate['hours'], $thePHPDate['minutes'], $thePHPDate['seconds'], $thePHPDate['mon'], $thePHPDate['mday'], $thePHPDate['year']); // Convert back to timestamp
        return $timeStamp;
    }
   
    public function addDayToDate($timeStamp, $totalDays=1){
        // You can add as many days as you want. mktime will accumulate to the next month / year.
        $thePHPDate = getdate($timeStamp);
        $thePHPDate['mday'] = $thePHPDate['mday']+$totalDays;
        $timeStamp = mktime($thePHPDate['hours'], $thePHPDate['minutes'], $thePHPDate['seconds'], $thePHPDate['mon'], $thePHPDate['mday'], $thePHPDate['year']);
        return $timeStamp;
    }

    public function addYearToDate($timeStamp, $totalYears=1){
        $thePHPDate = getdate($timeStamp);
        $thePHPDate['year'] = $thePHPDate['year']+$totalYears;
        $timeStamp = mktime($thePHPDate['hours'], $thePHPDate['minutes'], $thePHPDate['seconds'], $thePHPDate['mon'], $thePHPDate['mday'], $thePHPDate['year']);
        return $timeStamp;
    }	
	
	
}
?>