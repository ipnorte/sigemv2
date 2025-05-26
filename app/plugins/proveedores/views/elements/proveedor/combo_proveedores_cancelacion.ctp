<?php 
$proveedores = $this->requestAction('/proveedores/proveedores/proveedores_cancelacion/'.$orden_descuento_id);
echo $frm->input('proveedor_id',array('type'=>'select','options'=>$proveedores,'empty'=>FALSE,'label'=>$label,'disabled' => ''));?>