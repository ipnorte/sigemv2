<?php 
$combo = $this->requestAction('/proveedores/tipo_asientos/combo');
echo $frm->input($model,array('type'=>'select','options'=>$combo,'empty'=>($empty == 0 ? false : true),'selected' => (isset($selected) ? $selected : ''),'label'=>$label,'disabled' => ($disabled == 0 ? '' : 'disabled')));
?>