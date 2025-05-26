<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 *
 *	 /opt/lampp/bin/php-5.2.8 /opt/lampp/htdocs/aman2/cake/console/cake.php tmp_genera_cuotas_migracion 80 -app /opt/lampp/htdocs/aman2/app/
 *
 */

class TmpGeneraCuotasMigracionShell extends Shell{
	
	var $uses 		= array('Mutual.OrdenDescuento','Mutual.OrdenDescuentoCuota','Pfyj.Socio','Config.GlobalDato');



	function main(){
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}		
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Asincronos.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 

		$asinc->actualizar(0,100,"ESPERE, INICIANDO PROCESO...");
		
		
		
//		$ordenes = $this->OrdenDescuento->find('all',array('conditions' => array('OrdenDescuento.tipo_orden_dto' => 'MUTUTPROEXPT', 'order' => 'OrdenDescuento.socio_id')));
//		$ordenes = $this->OrdenDescuento->find('all',array('order' => 'OrdenDescuento.socio_id'));
		
		
		$this->Socio->unbindModel(array('belongsTo' => array('OrdenDescuento','Proveedor'), 'hasMany' => array('OrdenDescuentoCuota')));
		$socios = $this->Socio->find('all');
		
		
		$total = count($socios);
		
//		print $total ."\n";
		
		$asinc->setTotal($total);
		$i = 0;
		
		foreach($socios as $socio){
			
//			DEBUG($socio);

			$asinc->actualizar($i,$total,"PROCESANDO SOCIO #".$socio['Socio']['id']." - (".$i."/".$total.")");
			
			// saco las ordenes de descuento del socio
			$this->OrdenDescuento->unbindModel(array('belongsTo' => array('Socio','Proveedor'), 'hasMany' => array('OrdenDescuentoCuota')));
			$ordenes = $this->OrdenDescuento->find('all',array('conditions' => array('OrdenDescuento.socio_id' => $socio['Socio']['id']), 'order' => array('OrdenDescuento.tipo_producto DESC')));
			
			if(count($ordenes) != 0):
			
				// proceso las ordenes de descuentos
				foreach($ordenes as $orden){
					

//					$this->generaCuotas($orden);
					
					if($asinc->detenido()) break;
					
				}
			endif;
			
//			$this->OrdenDescuentoCuota->generaCuotas($orden);
			
			if($asinc->detenido()) break;
			
			$i++;
			
		}
		
		if(!$asinc->detenido()){
			$asinc->actualizar($i,$total,"FINALIZANDO...");
			$asinc->fin("**** PROCESO FINALIZADO ****");			
		}
		
	
	}
	
	
	function migraGeneraCuotasPermanente($data){
		
		$inicio = $data['OrdenDescuento']['periodo_ini'];
		
		$mIni = substr($inicio,4,2);
		$yIni = substr($inicio,0,4);
		
		$mkIni = mktime(0,0,0,$mIni,1,$yIni);		
		$mkFin = mktime(0,0,0,date('m'),1,date('Y'));
		
		$mkIniVtoSocio = mktime(0,0,0,date('m',strtotime($data['OrdenDescuento']['primer_vto_socio'])),date('d',strtotime($data['OrdenDescuento']['primer_vto_socio'])),date('Y',strtotime($data['OrdenDescuento']['primer_vto_socio'])));
		$mkIniVtoProv = mktime(0,0,0,date('m',strtotime($data['OrdenDescuento']['primer_vto_proveedor'])),date('d',strtotime($data['OrdenDescuento']['primer_vto_proveedor'])),date('Y',strtotime($data['OrdenDescuento']['primer_vto_proveedor'])));
		
		$pIni = $data['OrdenDescuento']['periodo_ini'];
		$pFin = date('Ym');

		$pAct = $pIni;
		$i = 0;
//		$mkAct = 0;

//		debug($data);
//		debug(date('Y-m-d',$mkIni));

		
		while($pFin > $pAct){
			
			$ss = 60 * 60 * 24 * 31 * $i;
//			$mkAct = $mkIni + $ss;
			
			
			$periodoActual = date('Ym',$this->addMonthToDate($mkIni,$i));
			$vtoSocio = date('Y-m-d',$this->addMonthToDate($mkIniVtoSocio,$i));
			$vtoProv = date('Y-m-d',$this->addMonthToDate($mkIniVtoProv,$i));
			
			$pAct = date('Ym',($mkIni + $ss));
			$i++;
			
			$glb = $this->GlobalDato->read('concepto_2',$data['OrdenDescuento']['tipo_producto']);
			
			$ret = $this->OrdenDescuentoCuota->save(array('OrdenDescuentoCuota' => array(
						'orden_descuento_id' => $data['OrdenDescuento']['id'],
						'socio_id' => $data['OrdenDescuento']['socio_id'],
						'tipo_orden_dto' => $data['OrdenDescuento']['tipo_orden_dto'],
						'tipo_producto' => $data['OrdenDescuento']['tipo_producto'],
						'periodo' => $periodoActual,
						'nro_cuota' => $i,
						'tipo_cuota' => $glb['GlobalDato']['concepto_2'],
						'situacion' => 'MUTUSICUMUTU',
						'importe' => $data['OrdenDescuento']['importe_cuota'],
						'proveedor_id' => $data['OrdenDescuento']['proveedor_id'],
						'vencimiento' => $vtoSocio,
						'vencimiento_proveedor' => $vtoProv
					)));
			$this->OrdenDescuentoCuota->id = 0;			
			
//			debug($pAct);
		}
		
		
	}
	
	
	function generaCuotas($data){
		
		if($data['OrdenDescuento']['tipo_orden_dto'] == 'CMUTU') return $this->migraGeneraCuotasPermanente($data);
		
		$inicio = $data['OrdenDescuento']['periodo_ini'];
		$segundosPlan = $data['OrdenDescuento']['cuotas'] * 30 * 24 * 60 * 60;
		
		$mIni = substr($inicio,4,2);
		$yIni = substr($inicio,0,4);
		
//		debug($mIni .' / '.$yIni);
		
		$mkIni = mktime(0,0,0,$mIni,1,$yIni);
//		$mkFin = $mkIni  + $segundosPlan;
		
		//SACO EL PROVEEDOR
		
		$mkIniVtoSocio = mktime(0,0,0,date('m',strtotime($data['OrdenDescuento']['primer_vto_socio'])),date('d',strtotime($data['OrdenDescuento']['primer_vto_socio'])),date('Y',strtotime($data['OrdenDescuento']['primer_vto_socio'])));
		$mkIniVtoProv = mktime(0,0,0,date('m',strtotime($data['OrdenDescuento']['primer_vto_proveedor'])),date('d',strtotime($data['OrdenDescuento']['primer_vto_proveedor'])),date('Y',strtotime($data['OrdenDescuento']['primer_vto_proveedor'])));
		
		
		App::import('Model', 'Mutual.MutualProductoSolicitud');
		$this->MutualProductoSolicitud = new MutualProductoSolicitud(null);

		$producto = $this->MutualProductoSolicitud->read(null,$data['OrdenDescuento']['numero']);
//		debug($producto);
		
	
//		debug($data);
		
		$i = 0;
		$cuota = 1;
		
		$impoCuota = number_format($data['OrdenDescuento']['importe_cuota'],2);
		
		$impoTotal = $impoCuota * $data['OrdenDescuento']['cuotas'];
		$impoOC = $data['OrdenDescuento']['importe_total'];
		
		$diff = $impoOC - $impoTotal;
		
//		debug($diff);
		
		for($i;$i < $data['OrdenDescuento']['cuotas'];$i++){
			
			$ss = 60 * 60 * 24 * 31 * $i;
			$mkFin = $mkIni  + $ss;
			
//			debug($cuota .' --> '. date('Ym',$mkFin));

			$periodoActual = date('Ym',$this->addMonthToDate($mkIni,$i));
			$vtoSocio = date('Y-m-d',$this->addMonthToDate($mkIniVtoSocio,$i));
			$vtoProv = date('Y-m-d',$this->addMonthToDate($mkIniVtoProv,$i));
			
			$importeCuota = $data['OrdenDescuento']['importe_cuota'];
			if($cuota == $data['OrdenDescuento']['cuotas']) $importeCuota += $diff;
			
			$glb = $this->GlobalDato->read('concepto_2',$data['OrdenDescuento']['tipo_producto']);
			
			// grabo la cuota del consumo
			$ret = $this->OrdenDescuentoCuota->save(array('OrdenDescuentoCuota' => array(
						'orden_descuento_id' => $data['OrdenDescuento']['id'],
						'socio_id' => $data['OrdenDescuento']['socio_id'],
						'tipo_orden_dto' => $data['OrdenDescuento']['tipo_orden_dto'],
						'tipo_producto' => $data['OrdenDescuento']['tipo_producto'],
						'periodo' => date('Ym',$mkFin),
						'nro_cuota' => $cuota,
						'tipo_cuota' => $glb['GlobalDato']['concepto_2'],
						'situacion' => 'MUTUSICUMUTU',	
						'importe' => $importeCuota,
						'proveedor_id' => $data['OrdenDescuento']['proveedor_id'],
						'vencimiento' => $vtoSocio,
						'vencimiento_proveedor' => $vtoProv			
					)));
			$this->OrdenDescuentoCuota->id = 0;
			
//			debug($cuota);
			
			$cuota++;

			
			
		}
		
		
//		debug($mkIni);
//		debug($segundosPlan);
//		
//		

//		debug($data);
//		exit;
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