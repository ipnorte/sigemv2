<?php 

/**
 * 
 * $this->renderElement("mutual_productos/combo_productos",
 * 						array(
 * 							'plugin' => 'mutual',
 * 							'empty' => true,
 * 							'selected' => valorSelected,
 * 							'model' => nombreDelModelo (si no se especifica toma MutualProductoSolicitud),
 * 							'proveedor' => id_proveedor or NULL para todos
 * 						)
 * );
 * 
 * 
 */

$proveedor_id = (isset($proveedor) ? $proveedor : null);
$productos = $this->requestAction('/proveedores/proveedores/proveedores_productos/'.$proveedor_id);
$model = (!isset($model) ? "MutualProductoSolicitud" : $model);
$selected = (!isset($selected) ? 0 : $selected);

$cmbId = $model."TipoProductoMutualId";

//$combo = "<label for='ProductoMutual'>PRODUCTOS PERMANENTES</label>";

?>

<script type="text/javascript">
Event.observe(window, 'load', function(){

	document.getElementById("<?php echo $cmbId?>").value = "<?php echo $selected?>";
	
	var selected = $("<?php echo $cmbId?>").getValue();
	var txtSelected = getTextoSelect('<?php echo $cmbId?>');

	$('<?php echo $cmbId?>').observe('change',function(){

		selected = $("<?php echo $cmbId?>").getValue();
		txtSelected = getTextoSelect('<?php echo $cmbId?>');
		document.getElementById("<?php echo $model?>SelectedStr").value = getTextoSelect('<?php echo $cmbId?>');
	});
	
	document.getElementById("<?php echo $model?>SelectedStr").value = getTextoSelect('<?php echo $cmbId?>');	
	
});
</script>


<?php 


$combo = "<select name='data[$model][tipo_producto_mutual_producto_id]' id='$cmbId'>";

if(isset($empty)) $combo .= "<option value='0'>*** TODOS ***</option>";

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
			if($prod['tipo_orden_dto']=='OCOMP') $combo .= "<option value='".$idOpt."'>".$prod['str']."</option>";
		}
		
		$combo .= "</optgroup>";
	
	endif;
}

$combo .= "</select>";
$combo .= "<input type='hidden' name='data[$model][selected_str]' id='".$model."SelectedStr' value=''>";
echo $combo;
?>