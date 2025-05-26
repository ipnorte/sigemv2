<?php 
// if(!isset($accion))$accion = '/proveedores/movimientos/cta_cte/';
if(!isset($accion))$accion = '/proveedores/movimientos/pendientes/';
// if(!isset($icon))$icon = 'controles/folder_user.png';

?>

<?php if(!empty($proveedores)):?>

<?php echo $this->renderElement('paginado')?>

<script type="text/javascript">

function toggleCellMouseOver(idRw, status){
	var celdas = $(idRw).immediateDescendants();
	if(status)celdas.each(function(td){td.addClassName("selected2");});
	else celdas.each(function(td){td.removeClassName("selected2");});
}

</script>

<table cellpadding="0" cellspacing="0">

	<tr>
		<th><?php echo $paginator->sort('C.U.I.T.','cuit');?></th>
		<th><?php echo $paginator->sort('RAZON SOCIAL','razon_social');?></th>
	</tr>

	<?php
	$i = 0;
	foreach ($proveedores as $proveedor):
		$class = null;
		if ($i++ % 2 == 0) {
//			$class = ' class="altrow"';
		}
		$click = "onclick = \"window.location.href = '".$html->url($accion.$proveedor['Proveedor']['id'],true)."'\" style=\"cursor: pointer;\"";
	?>
		<tr<?php echo $class;?> id="LTR_<?php echo $i?>" onmouseover="toggleCellMouseOver('LTR_<?php echo $i?>',true)" onmouseout="toggleCellMouseOver('LTR_<?php echo $i?>',false)">
			<td <?php echo $click?>><?php echo $proveedor['Proveedor']['cuit']?></td>
			<td <?php echo $click?>><strong><?php echo $proveedor['Proveedor']['razon_social']?></strong></td>

		</tr>
	<?php endforeach; ?>	

</table>



<?php echo $this->renderElement('paginado')?>
<?php endif;?>