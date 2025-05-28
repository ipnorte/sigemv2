<?php 
echo $frm->input($model,array('type'=>'select','options'=>$values,'empty'=>($empty == 0 ? false : true),'selected' => (isset($selected) ? $selected : ''),'label'=>$label,'disabled' => ($disabled == 0 ? '' : 'disabled')));
?>