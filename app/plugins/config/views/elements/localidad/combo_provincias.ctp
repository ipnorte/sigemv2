<?php 
$datos = $this->requestAction('/config/localidades/cmb_provincias');
$empty = (isset($empty) ? $empty : 0);
$selected = (isset($selected) ? $selected : "");
$label = (isset($label) ? $label : "");
$disabled = (isset($disabled) ? $disabled : false);
$model = (isset($model) ? $model : "Provincia.id");
echo $frm->input($model,array('type'=>'select','options'=>$datos,'empty'=>($empty == 0 ? false : true),'selected' => $selected,'label'=>$label,'disabled' => (isset($disabled) ? $disabled : false)));
?>
