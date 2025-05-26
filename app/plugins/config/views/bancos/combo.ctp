<?php 
echo $frm->input($model,array('type'=>'select','options'=>$bancos,'empty'=>($empty == 0 ? false : true),'label'=> $label,'disabled' => ($disabled == 0 ? '' : 'disabled')));
?>