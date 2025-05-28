<?php 
echo $frm->input('Credito.banco_sucursal_id',array('type'=>'select','options'=>$sucursales,'empty'=>false,'label'=>'SUCURSAL','disabled' => $disabled));
?>