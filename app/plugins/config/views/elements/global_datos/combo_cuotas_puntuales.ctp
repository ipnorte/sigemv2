<?php 
$cuotas = $this->requestAction('/config/global_datos/get_cuotas_puntuales');
$disabled = (isset($disabled) ? $disabled : false);
$selected = (isset($selected) ? $selected : "");
echo $frm->input($model,array('type'=>'select','options'=>$cuotas,'empty'=>($empty == 0 ? false : true),'selected' => (isset($selected) ? $selected : ''),'label'=>$label,'disabled' => (isset($disabled) ? $disabled : false)));
?>