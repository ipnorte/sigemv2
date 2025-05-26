<?php 
$datos = $this->requestAction('/config/global_datos/'.$metodo);
$empty = (isset($empty) ? $empty : 0);
$selected = (isset($selected) ? $selected : "");
$label = (isset($label) ? $label : "");
$disabled = (isset($disabled) ? $disabled : false);
$model = (isset($model) ? $model : "GlobalDato.id");
echo $frm->input($model,array('type'=>'select','options'=>$datos,'empty'=>($empty == 0 ? false : true),'selected' => $selected,'label'=>$label,'disabled' => (isset($disabled) ? $disabled : false)));
?>