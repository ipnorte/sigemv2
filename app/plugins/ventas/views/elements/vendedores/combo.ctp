<?php 

$params = array();
$params['type'] = 'select';
$params['options'] = $this->requestAction('/ventas/vendedores/get_vendedores_list' . (isset($solo_activos) && $solo_activos ? '/1' : ''));
if(isset($empty)) $params['empty'] = TRUE;

if(isset($selected)) $params['selected'] = $selected;
if(isset($disabled)) $params['disabled'] = 'disabled';

if(!empty($Seguridad['Usuario']['vendedor_id'])){
    $params['disabled'] = 'disabled';
    $params['empty'] = FALSE;
    $params['selected'] = $Seguridad['Usuario']['vendedor_id'];
}

$model = (isset($modelo) ? $modelo.'.supervisor_id' : 'MutualProductoSolicitud.vendedor_id');
echo $frm->input($model,$params);

//echo $frm->input('MutualProductoSolicitud.vendedor_id',$params);



?>
