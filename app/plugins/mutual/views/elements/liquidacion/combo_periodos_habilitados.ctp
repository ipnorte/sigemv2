<?php 
//$order="DESC",$abiertos=1,$imputados=0,$organismo=0
$cantidad_periodos = (isset($cantidad_periodos) && is_int($cantidad_periodos) ? $cantidad_periodos : 12);
$organismo = (isset($organismo) && !empty($organismo) ? $organismo : 0);
$periodoMinimo = (isset($periodoMinimo) && !empty($periodoMinimo) ? $periodoMinimo : 0);

$periodos = $this->requestAction("/mutual/liquidaciones/get_periodos_disponibles/$organismo/$cantidad_periodos/$periodoMinimo");

$empty = (isset($empty) ? $empty : 0);
$selected = (isset($selected) ? $selected : "");
$label = (isset($label) ? $label : "");
$disabled = (isset($disabled) ? $disabled : 0);
$model = (isset($model) ? $model : "Liquidacion.periodo");

echo $frm->input($model,array('type'=>'select','options'=>$periodos,'empty'=>($empty == 0 ? false : true),'selected' => (isset($selected) ? $selected : ''),'label'=>$label,'disabled' => ($disabled == 0 ? '' : 'disabled')));

?>