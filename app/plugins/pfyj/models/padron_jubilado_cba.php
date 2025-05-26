<?php
class PadronJubiladoCba extends PfyjAppModel{
	var $name = 'PadronJubiladoCba';
	var $useTable = "padron_jubilados";
	var $useDbConfig = "cba";
	
	function checkDatos($documento,$tipo,$ley,$beneficio,$sub_beneficio){
		$datos = $this->find('count',array('conditions' => array(
								'PadronJubiladoCba.documento' => $documento,
								'PadronJubiladoCba.tipo' => $tipo,
								'PadronJubiladoCba.ley' => $ley,
								'PadronJubiladoCba.beneficio' => $beneficio,
								'PadronJubiladoCba.sub_beneficio' => $sub_beneficio,
		)));
		if($datos==0) return false;
		else return true;
	}
	
}