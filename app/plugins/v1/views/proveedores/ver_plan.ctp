<?php echo $this->renderElement('head',array('title' => 'Actualización de Grillas :: Gestión','plugin' => 'config'))?>

<h4>Proceso de Copiado de Cuotas</h4>
<div class="areaDatoForm2">
<h3><?php echo  $proveedorv1['ProveedorV1']['codigo_proveedor']?> :: <?php echo  $proveedorv1['ProveedorV1']['razon_social']?></h3>

<?php //echo $controles->openWindow($proveedorv1['ProveedorV1']['codigo_proveedor'] .', '. $proveedorv1['ProveedorV1']['razon_social'],'/proveedores/personas/view/'.$persona['Persona']['id'])?>
</div>

<div class="areaDatoForm">
CODIGO: <strong><?php echo $producto['pp']['codigo_producto']?></strong>
&nbsp;&nbsp;DESCRIPCION: <strong><?php echo $producto['pp']['descripcion']?></strong>

<hr>
<?php 
$planes = $this->requestAction('/proveedores/proveedor_planes/get_planes_vigentes_proveedor_organismo/'.$proveedorv2['Proveedor']['id']);
?>

<script type="text/javascript">
Event.observe(window, 'load', function(){

	<?php if(empty($planes)):?>
	$('btn_submit').disable();
	<?php endif;?>
});
</script>

<?php echo $frm->create(null,array('id' => 'formCopiaCuotas','action' => 'ver_plan/' . $producto['pp']['codigo_proveedor'].'/'.$producto['pp']['codigo_producto'],))?>
<table class="tbl_form"> 
	<tr> 
	
		<td>Copiar Cuotas de</td>
		<td>

			<select name="data[ProveedorPlan][id]">
			<?php if(!empty($planes)):?>
					<?php foreach ($planes as $value) {
					    echo "<option value=\"".$value['ProveedorPlan']['id']."\">".$value['ProveedorPlan']['cadena']."</option>";
					}?>	
			<?php else:?>	
				<option>** NO EXISTEN PLANES Y/O PRODUCTOS **</option>				
			<?php endif;?>
			</select>

		
		</td>
		<td><?php echo $controles->botonGenerico('/proveedores/proveedor_planes/index/'.$proveedorv2['Proveedor']['id'],'controles/search.png',null,array('target' => 'blank'))?></td>
	
	</tr>
</table>
<?php echo $frm->hidden('Producto.codigo_proveedor',array('value' => $producto['pp']['codigo_proveedor'])); ?>
<?php echo $frm->hidden('Producto.codigo_producto',array('value' => $producto['pp']['codigo_producto'])); ?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'COPIAR CUOTAS','CONFIRM' => 'Copiar cuotas?','URL' => ( empty($fwrd) ? "/v1/proveedores/listar_planes/".$proveedorv1['ProveedorV1']['codigo_proveedor'] : $fwrd) ))?>
<!-- <br/> -->
<?php //debug($proveedorv2)?>
</div>