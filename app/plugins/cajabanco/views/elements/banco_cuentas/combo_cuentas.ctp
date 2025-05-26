<?php 
if(!isset($ecepto)) $ecepto = 0;

if(isset($caja)):
	$cuenta = $this->requestAction("/cajabanco/banco_cuentas/combo/" . $ecepto . "/" . $caja);
else:
	$cuenta = $this->requestAction("/cajabanco/banco_cuentas/combo/" . $ecepto);
endif;
if(!isset($onChange)) $onChange = null;

echo $frm->input($model,array('type'=>'select','options'=>$cuenta,'empty'=>($empty == 0 ? false : true), 'onchange' => $onChange, 'selected' => (isset($selected) ? $selected : ''),'label'=>$label,'disabled' => ($disabled == 0 ? '' : 'disabled')));
?>