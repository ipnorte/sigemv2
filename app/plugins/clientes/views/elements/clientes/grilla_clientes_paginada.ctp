<?php 
if(!isset($accion))$accion = '/clientes/clientes/pendientes/';
// if(!isset($icon))$icon = 'controles/folder_user.png';
?>

<?php if(!empty($clientes)):?>

<?php echo $this->renderElement('paginado')?>

<script language="Javascript" type="text/javascript">
	var config = <?php echo $error?>;
	Event.observe(window, 'load', function() {
		
		<?php if($error==1):?>
			alert("DATOS GRABADOS CORRECTAMENTE"); 
		<?php elseif($error==2):?>
			alert("ERROR AL GRABAR LOS DATOS"); 
		<?php endif; ?>
	
	});


	function toggleCellMouseOver(idRw, status){
		var celdas = $(idRw).immediateDescendants();
		if(status)celdas.each(function(td){td.addClassName("selected2");});
		else celdas.each(function(td){td.removeClassName("selected2");});
	}

</script>

<table cellpadding="0" cellspacing="0">

	<tr>
		<th></th>
		<th><?php echo $paginator->sort('C.U.I.T.','cuit');?></th>
		<th><?php echo $paginator->sort('RAZON SOCIAL','razon_social');?></th>
	</tr>

	<?php
	$i = 0;
	foreach ($clientes as $cliente):
		$class = null;
		if ($i++ % 2 == 0) {
//			$class = ' class="altrow"';
		}
		$click = "onclick = \"window.location.href = '".$html->url($accion.$cliente['Cliente']['id'],true)."'\" style=\"cursor: pointer;\"";
	?>
		<tr<?php echo $class;?> id="LTR_<?php echo $i?>" onmouseover="toggleCellMouseOver('LTR_<?php echo $i?>',true)" onmouseout="toggleCellMouseOver('LTR_<?php echo $i?>',false)">
			<td><?php echo $controles->botonGenerico('/clientes/clientes/edit/'.$cliente['Cliente']['id'],'controles/folder.png')?></td>
			<td <?php echo $click?>><?php echo $cliente['Cliente']['cuit']?></td>
			<td <?php echo $click?>><strong><?php echo $cliente['Cliente']['razon_social']?></strong></td>

		</tr>
	<?php endforeach; ?>	

</table>



<?php echo $this->renderElement('paginado')?>
<?php endif;?>