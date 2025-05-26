<?php 
//debug($personaIDR." - ".$tdoc." - ".$ndoc);
$solicitudes = $this->requestAction('/v1/solicitudes/get_solicitudes_by_socio/'.(!empty($personaIDR) ? $personaIDR : 0).'/'.$tdoc.'/'.$ndoc);


if(!empty($solicitudes)):

//	debug($solicitudes);

	$solicitudes = Set::extract("/Solicitud[estado<=12]",$solicitudes);
	
//	debug($solicitudes);
	
	$solicitudes = Set::extract("/Solicitud[total_cancelado>0]",$solicitudes);
	
	$solicitudes = Set::sort($solicitudes, '{n}.Solicitud.proveedor', 'asc');
	
//	debug($solicitudes);
	
	
	$combo = array();
	
	foreach($solicitudes as $solicitud):
//		debug($solicitud);
		$combo[$solicitud['Solicitud']['nro_solicitud']] = "EXPTE #".$solicitud['Solicitud']['nro_solicitud'] . "|". utf8_encode($solicitud['Solicitud']['proveedor_producto']);
		$combo[$solicitud['Solicitud']['nro_solicitud']] .= " (".$solicitud['Solicitud']['estado_descripcion'].")";
	endforeach;
//	debug($combo);
	if(!empty($combo)):
		echo $frm->input('nro_solicitud',array('type'=>'select','options'=>$combo,'empty'=> (isset($empty) ? $empty : ""),'label' => (isset($label) ? $label : ""),'disabled' => (isset($disable) ? $disable : ""), 'selected' => (isset($selected) ? $selected : "")));
	else:
		echo "*** NO EXISTEN CREDITOS EN PROCESO CON ORDEN DE CANCELACION. ***";
	endif;
//	debug($combo);
	
endif;
?>