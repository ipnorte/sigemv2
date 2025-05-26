<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 *
 */
class TmpDemoAsincronoShell extends Shell {
	
	var $uses = array('Shells.Asincrono');
	
	function main() {
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$i=0;
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 
		
		$total = $asinc->getParametro('p1');
		
		$asinc->setTotal($total);
		$stop = false;
		
		$asinc->actualizar($i,$total,"PROCESANDO REGISTRO $i/$total");
		
		do{
			
			$asinc->actualizar($i,$total,"PROCESANDO REGISTRO $i/$total");
			$stop = $asinc->detenido();
			
			$this->out("PROCESANDO REGISTRO $i/$total\n");
			
			$i++;
			
		}while($i <= $total && !$asinc->detenido());
		
		if(!$asinc->detenido())$asinc->fin("ESTE PROCESO TERMINO **** BIEN ADRIAN!!");
		
		
		
	}
}
?>