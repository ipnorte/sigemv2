<?php 

$org = $this->requestAction('/config/global_datos/valor/'.$beneficio['PersonaBeneficio']['codigo_beneficio']);
$tipo = $this->requestAction('/config/global_datos/valor/'.$beneficio['PersonaBeneficio']['codigo_beneficio'].'/concepto_2');

$nroBen = $beneficio['PersonaBeneficio']['nro_beneficio'];
$nroLey = $beneficio['PersonaBeneficio']['nro_ley'];

$bco = (!empty($beneficio['PersonaBeneficio']['banco_id']) ? $this->requestAction('/config/bancos/nombre/'.$beneficio['PersonaBeneficio']['banco_id']) : '') ;
$cbu = $beneficio['PersonaBeneficio']['cbu'];


switch ($tipo) {
	case 'AC':
//		$str = '#'.$beneficio['PersonaBeneficio']['id'] . ' - ' . $org . ' - ' . $bco . ' - CBU: '.$cbu ;
		$str = $org . ' - ' . $bco . ' - CBU: '.$cbu ;
		break;
	case 'JP':
//		$str = '#'.$beneficio['PersonaBeneficio']['id'] . ' - ' . $org . ' - NRO.: ' . $nroBen . ' - LEY: '.$nroLey ;
		$str = $org . ' - NRO.: ' . $nroBen . ' - LEY: '.$nroLey ;
		break;
	case 'JN':
//		$str = '#'.$beneficio['PersonaBeneficio']['id'] . ' - ' . $org . ' - NRO.: ' . $nroBen;
		$str = $org . ' - NRO.: ' . $nroBen;
		break;
		
}

echo $str;
?>