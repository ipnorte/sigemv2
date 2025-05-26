<?php 
$ejercicio = $this->requestAction('/contabilidad/ejercicios/get_ejercicio');
echo $frm->input($model,array('type'=>'select','options'=>$ejercicio,'empty'=>($empty == 0 ? false : true),'selected' => (isset($selected) ? $selected : ''),'label'=>$label,'disabled' => ($disabled == 0 ? '' : 'disabled')));
?>