<?php echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$persona,'link'=>true,'plugin' => 'pfyj'))?>
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


?>
<h3>DETALLE DEL BENEFICIO</h3>
<div class="areaDatoForm2"><?php echo $str?></div>
<h3>ORDENES DE DESCUENTO VIGENTES ASOCIADAS A ESTE BENEFICIO</h3>
<?php echo $this->renderElement('orden_descuento/grilla_ordenes_by_beneficio',array('socio_id' => $persona['Socio']['id'],'persona_beneficio_id' => $beneficio['PersonaBeneficio']['id'],'plugin' => 'mutual'))?>
