<?php 

//debug($productos);

$combo = "<label for='ProductoMutual'>PRODUCTO</label>";
$combo .= "<select name='data[MutualProductoSolicitud][tipo_producto_mutual_producto_id]' id='ProductoMutual'>";

foreach($productos as $prd){

	
	if(!empty($prd['MutualProducto'])):
        
//        debug($prd);
	
		$combo .= "<optgroup label='".$prd['Proveedor']['razon_social']."' >";
		
		foreach($prd['MutualProducto'] as $prod){

			$impoFijo = $prod['importe_fijo'];
			$mensual = $prod['mensual'];
			$sinCargo = $prod['sin_cargo'];
			$prestamo = $prod['prestamo'];
			
			$impoCuotaSocialDiferenciada = $prod['cuota_social_diferenciada'];
			$tipo = $this->requestAction('/config/global_datos/valor/'.$prod['tipo_orden_dto']);
			$produ = $this->requestAction('/config/global_datos/valor/'.$prod['tipo_producto'].'/concepto_1');
			
			$idOpt = $prod['id'].'|'.$prod['tipo_producto'].'|'.$prod['tipo_orden_dto'].'|';
			$idOpt .= $prd['Proveedor']['id'].'|'.($impoFijo > 0 ? $impoFijo : 0) . '|';
			$idOpt .= ($impoCuotaSocialDiferenciada > 0 ? $impoCuotaSocialDiferenciada : 0) . '|' . $mensual . '|'.$sinCargo . '|' . $prestamo;

			$lblOpt = (!empty($prd['Proveedor']['razon_social_resumida']) ? $prd['Proveedor']['razon_social_resumida'] : $prd['Proveedor']['razon_social']) . ' - ' .$produ . ($mensual != 0 ? " (PERMANENTE".($impoFijo > 0 ? " $ $impoFijo" : '').")" : '');
			
			#SOLAMENTE CARGO LOS TIPOS OCOMP#
//			if($prod['tipo_orden_dto']=='OCOMP') $combo .= "<option value='".$idOpt."'>".$lblOpt."</option>";
            $combo .= "<option value='".$idOpt."'>".$lblOpt."</option>";
		}
		
		$combo .= "</optgroup>";
	
	endif;
}

$combo .= "</select>";
echo $combo;
?>

<?php //   debug($productos)?>