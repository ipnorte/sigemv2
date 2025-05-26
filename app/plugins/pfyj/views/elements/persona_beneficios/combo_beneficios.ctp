<?php 
// $soloActivos = (isset($soloActivos) ? $soloActivos : 1);
// $beneficios = $this->requestAction('/pfyj/persona_beneficios/beneficios_by_persona/'.$persona_id.'/'.$soloActivos);
// $model = (isset($model) ? $model."." : "");
// $options = array();
// if(!empty($beneficios)):

// 	foreach($beneficios as $beneficio):
	
// 		$options[$beneficio['PersonaBeneficio']['id']] = $beneficio['PersonaBeneficio']['string'];
	
// 	endforeach;
// 	echo $frm->input($model.'persona_beneficio_id',array('type'=>'select','options'=>$options,'empty'=>(isset($empty) ? true : false),'selected' => (isset($selected) ? $selected : ''),'label'=> (isset($label) ? $label : ""),'disabled' => (isset($disabled) ? 'disabled' : '')));
	

// endif;

$soloActivos = (isset($soloActivos) ? $soloActivos : 1);
$sinAcuerdo = (isset($sinAcuerdo) ? $sinAcuerdo : 0);
//debug('/pfyj/persona_beneficios/beneficios_by_persona/'.$persona_id.'/'.$soloActivos.'/'.$sinAcuerdo);
$beneficios = $this->requestAction('/pfyj/persona_beneficios/beneficios_by_persona/'.$persona_id.'/'.$soloActivos.'/'.$sinAcuerdo);
$model = (isset($model) ? $model."." : "");
$style = (isset($style) ? $style : "");
$options = array();
if(!empty($beneficios)):

foreach($beneficios as $beneficio):

$options[$beneficio['PersonaBeneficio']['id']] = $beneficio['PersonaBeneficio']['string'];

endforeach;
echo $frm->input($model.'persona_beneficio_id',array('type'=>'select','options'=>$options,'empty'=>(isset($empty) ? true : false),'selected' => (isset($selected) ? $selected : ''),'label'=> (isset($label) ? $label : false),'disabled' => (isset($disabled) ? 'disabled' : ''), 'style' => $style, 'div' => false));

else:
    
    echo "** SIN INFORMACION ***";

endif;

//debug($options);

?>