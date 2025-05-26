<?php 
$proveedores = $this->requestAction('/proveedores/proveedores/'.$metodo);
//debug($proveedores);
echo $frm->input((isset($model) && !empty($model) ? $model : "proveedor_id"),array('type'=>'select','options'=> $proveedores,'empty'=> (isset($empty) ? $empty : false),'label'=> (isset($label) ? $label : ""),'disabled' => (isset($disabled) ? $disabled : false),'selected' => (isset($selected) ? $selected : "")));
?>