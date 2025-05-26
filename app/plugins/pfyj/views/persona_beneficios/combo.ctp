<?php 
echo $frm->input($model.'.persona_beneficio_id',array('type'=>'select','options'=>$beneficios,'empty'=>($empty == 0 ? false : true),'selected'=>$selected,'label'=>$label,'disabled' => ($disabled == 0 ? '' : 'disabled')));
?>