<?php

/**
 * 
 * @author GUSTAVO LUJAN
 * @package shells
 * @subpackage background-execute
 * 
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php listado_padron_servicio 43 -app /home/adrian/dev/www/sigem/app/
 *  C:\xampp\php\php.exe C:\xampp\htdocs\sigem\cake\console\cake.php cierre_periodo 19005 -app C:\xampp\htdocs\sigem\app\
 * 
 * 
 */


class CierrePeriodoShell extends Shell {


    var $ejercicioId;
    var $fecha_cierre;
    var $ejercicio;
    var $asientos;
    var $lError = false;

    var $uses = array('contabilidad.Asiento','contabilidad.Ejercicio', 'contabilidad.PlanCuenta', 'contabilidad.AsientoRenglon');
    var $tasks = array('Temporal');
//    var $Asiento;

    function main() {
	$STOP = 0;
		
	if(empty($this->args[0])){
            $this->out("ERROR: PID NO ESPECIFICADO");
		return;
	}
		
	$pid = $this->args[0];
		
	$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
	$asinc->id = $pid; 
		
	$this->ejercicioId = $asinc->getParametro('p2');
	$this->fecha_cierre = $asinc->getParametro('p1');
	$this->ejercicio = $this->Ejercicio->getEjercicio($this->ejercicioId);		
		
	if(empty($this->ejercicio['Ejercicio']['fecha_cierre_periodo'])) $this->ejercicio['Ejercicio']['fecha_cierre_periodo'] = date('Y-m-d', $this->Ejercicio->addDayToDate(strtotime($this->ejercicio['Ejercicio']['fecha_desde'], -1))); 
		 
	$fecha_desde = date('Y-m-d',$this->Ejercicio->addDayToDate(strtotime($this->ejercicio['Ejercicio']['fecha_cierre_periodo'], 1)));
		
		
	$this->Temporal->limpiarTabla($asinc->id);

	$this->Temporal->pid = $pid;
		
	$temp = array();
	$temp1 = array();
		
	$nro_asiento = 1;
	$i = 0;
		
	$asinc->actualizar(1,3,"BORRANDO ASIENTO RENGLONES >>> ");
	$ejercicio_id = $this->ejercicioId;
	$fecha = $this->fecha_cierre;
			
	$asientoBorrar = $this->Asiento->find('all', array('conditions' => array('Asiento.borrado' => 1, 'Asiento.fecha <=' => $fecha, 'Asiento.co_ejercicio_id' => $ejercicio_id)));
				
	$total = count($asientoBorrar) + 1;
	$asinc->setTotal($total);
	$i = 1;
				
				
	$asinc->actualizar($i,$total,"ACTUALIZANDO ASIENTOS >>> ");
	$sql = "UPDATE co_asientos SET nro_asiento = nro_asiento + 1000000 WHERE co_ejercicio_id = '$ejercicio_id' AND tipo != 1";
// 	if(!$this->Asiento->updateAll(array('Asiento.nro_asiento' => $this->BancoCuentaSaldo->id), array('BancoCuentaMovimiento.banco_cuenta_id' => $banco_cuenta_id, 'BancoCuentaMovimiento.conciliado' => 1, 'BancoCuentaMovimiento.banco_cuenta_saldo_id' => 0))):
			
	$this->Asiento->query($sql);
// debug($this->lError);
// exit;
			
// 	if(!$this->Asiento->query($sql)):
//          $asinc->actualizar($i, $total, "**** ERROR EN ACTUALIZAR ASIENTOS ****");
//          return;
// 	endif;
					
	$asinc->actualizar($i,$total,"$i / $total - BORRANDO ASIENTOS >>> ");
	foreach ($asientoBorrar as $borrar):
            $asientoId = $borrar['Asiento']['id'];
            if(!$this->AsientoRenglon->deleteAll("AsientoRenglon.co_asiento_id = '$asientoId'")):
		$asinc->actualizar(50, 100, "**** ERROR EN BORRAR ASIENTO RENGLONES ****");
		$this->Temporal->setErrorMsg("**** ERROR EN BORRAR ASIENTO RENGLONES ****");
                return;
            endif;

            if(!$this->Asiento->delete($asientoId)):
		$asinc->actualizar($i, $total, "**** ERROR EN BORRAR ASIENTOS ****");
		$this->Temporal->setErrorMsg("**** ERROR EN BORRAR ASIENTOS ****");
		return;
            endif;
            $i += 1;		
			
            $asinc->actualizar($i,$total,"$i / $total - BORRANDO ASIENTOS >>> ");
	endforeach;
			
			

	$this->asientos = $this->Asiento->getAsientoAll($this->ejercicioId);
	if(empty($this->asientos)){
            $asinc->fin("**** PROCESO FINALIZADO :: NO EXISTEN REGISTROS PARA PROCESAR ****");
            return;
	}
		
		
				
	$total = count($this->asientos);
	$asinc->setTotal($total);
	$i = 1;		
		
	$asinc->actualizar(0,$total,"$i / $total - PROCESANDO ASIENTO NUMERO >>> ");
	foreach($this->asientos as $renglon):

            if($renglon['Asiento']['tipo'] === '2'):
                $nro_asiento += 1;
		$renglon['Asiento']['nro_asiento'] = $nro_asiento;
			

		if(!$this->Asiento->save($renglon)): 
                    $this->lError = true;
                    $asinc->actualizar($i,$total,"$i / $total - ERROR AL ACTUALIZAR NUMERO DE ASIENTO >>> " . $nro_asiento);
                    $this->Temporal->setErrorMsg("ERROR AL ACTUALIZAR NUMERO DE ASIENTO >>> " . $nro_asiento );
                    return;
		endif;
            endif;

            $i++;
            $asinc->actualizar($i,$total,"$i / $total - PROCESANDO ASIENTO NUMERO >>> " . $nro_asiento);
	endforeach;
		
		
	$asinc->actualizar($total,$total,"FINALIZANDO...");
// 	if(!$this->lError):
		
	$this->ejercicio['Ejercicio']['fecha_cierre_periodo'] = $this->fecha_cierre;
	$this->ejercicio['Ejercicio']['nro_asiento'] = $nro_asiento;
			
	if($this->Ejercicio->save($this->ejercicio)):
            $asinc->fin("**** PROCESO FINALIZADO SIN ERRORES ****");
	else:
            $asinc->actualizar(99, 100, "**** ERROR EN ACTUALIZAR EJERCICIO ****");
	endif;
			
// 	else:
//          $asinc->error("**** ERROR EN BORRAR ASIENTOS ****");
// 	endif;		
// 	$asinc->fin();
	
		
	}
	//FIN PROCESO ASINCRONO
	

}
?>