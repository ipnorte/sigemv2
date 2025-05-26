<?php 
$productos = $this->requestAction('/proveedores/proveedores/proveedores_productos_mensuales');
//debug($productos);
$combo = "";
//$combo .= "<label for='ProductoMutual'>PRODUCTOS PERMANENTES</label>";
$combo .= "<select name='data[MutualProductoSolicitud][tipo_producto_mutual_producto_id]' id='ProductoMutual'>";
$selected = (isset($selected) ? $selected : null);

foreach($productos as $prd){

	
	if(!empty($prd['MutualProducto'])):
	
		$combo .= "<optgroup label='".$prd['Proveedor']['razon_social']."' >";
		
		foreach($prd['MutualProducto'] as $prod){

			$impoFijo = $prod['importe_fijo'];
			$impoCuotaSocialDiferenciada = $prod['cuota_social_diferenciada'];
			$tipo = $this->requestAction('/config/global_datos/valor/'.$prod['tipo_orden_dto']);
			$produ = $this->requestAction('/config/global_datos/valor/'.$prod['tipo_producto'].'/concepto_1');
			
			$idOpt = $prod['id'].'|'.$prod['tipo_producto'].'|'.$prod['tipo_orden_dto'].'|'.$prd['Proveedor']['id'].'|'.($impoFijo > 0 ? $impoFijo : 0) . '|' .($impoCuotaSocialDiferenciada > 0 ? $impoCuotaSocialDiferenciada : 0);
			$lblOpt = $prd['Proveedor']['razon_social_resumida'] . ' - ' .$produ . ($impoFijo > 0 ? " (PERMANENTE $ $impoFijo)" : '');
			
			#SOLAMENTE CARGO LOS TIPOS OCOMP#
			if($prod['tipo_orden_dto']=='OCOMP') $combo .= "<option value='".$idOpt."' ".($selected == $idOpt ? 'selected' : '').">".$prod['str']."</option>";
		}
		
		$combo .= "</optgroup>";
	
	endif;
}

$combo .= "</select>";
echo $combo;
?>