<?php 
//$order="DESC",$abiertos=1,$imputados=0,$organismo=0
$order = (isset($order) ? $order : 'DESC');
$abiertos = (isset($abiertos) && $abiertos ? 1 : 0);
$imputados = (isset($imputados) && $imputados ? 1 : 0);

$abiertos = ($imputados == 1 ? 1 : 0);

$organismo = (isset($organismo) && !empty($organismo) ? $organismo : 0);

if(isset($facturados)):
    $facturados = ($facturados ? 1 : 0);
    $periodos = $this->requestAction("/mutual/liquidaciones/getPeriodosFacturados/$order/$facturados/$organismo");
else:
    $periodos = $this->requestAction("/mutual/liquidaciones/getPeriodosImputados/$order/$abiertos/$imputados/$organismo");
endif;

$empty = (isset($empty) ? $empty : 0);
$selected = (isset($selected) ? $selected : "");
$label = (isset($label) ? $label : "");
$disabled = (isset($disabled) ? $disabled : 0);
$model = (isset($model) ? $model : "Liquidacion.periodo");
echo $frm->input($model,array('type'=>'select','options'=>$periodos,'empty'=>($empty == 0 ? false : true),'selected' => (isset($selected) ? $selected : ''),'label'=>$label,'disabled' => ($disabled == 0 ? '' : 'disabled')));

?>