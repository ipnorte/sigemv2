<?php 
$call_center = (isset($call_center) ? 1 : 0);
$solo_activos = (!isset($solo_activos) ? 1 : $solo_activos);
$productos = $this->requestAction('/proveedores/proveedores/proveedores_servicios_activos/' . $call_center . '/' . $solo_activos);

$cantidad = 0;
$selected = (isset($selected) ? $selected : "");

$combo = "<div class='input select'><label for='".$model."TipoMutualServicioId'></label>";
$combo .= "<select name='data[$model][tipo_servicio_mutual_producto_id]' id='".$model."TipoMutualServicioId'>";

foreach($productos as $prd):

	if(!empty($prd['MutualServicio'])):
	
		$combo .= "<optgroup label='".$prd['Proveedor']['razon_social']."' >";
		foreach($prd['MutualServicio'] as $prod){
			$idOpt = $prod['id'].'|'.$prod['tipo_producto'].'|'.$prod['tipo_orden_dto'].'|'.$prd['Proveedor']['id'];
			$combo .= "<option value='".$idOpt."' ".($selected == $idOpt ? "selected='selected'" : "")."  >".$prod['str']."</option>";
			$cantidad++;
		}
		$combo .= "</optgroup>";
	endif;

endforeach;

$combo .= "</select></div>";
$combo .= "<input type='hidden' name='data[$model][cantidad]' id='".$model."Cantidad' value='$cantidad' />";

echo $combo;

?>