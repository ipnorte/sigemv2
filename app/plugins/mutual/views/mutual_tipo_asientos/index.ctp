<?php echo $this->renderElement('mutual_tipo_asientos/menu')?>

<h3>TIPOS DE ASIENTOS</h3>
<div class="actions">
<?php echo $controles->botonGenerico('/mutual/mutual_tipo_asientos/add','controles/attach.png','NUEVO TIPO')?>
</div>
<table>
	<tr>
		<th></th>
		<th></th>
		<th>#</th>
		<th>CONCEPTO</th>
		<th>TIPO</th>
	</tr>
	<?php foreach ($tipos as $tipo):?>
		<tr>
			<td><?php echo $controles->botonGenerico('/mutual/mutual_tipo_asientos/edit/'.$tipo['MutualTipoAsiento']['id'],'controles/folder.png')?></td>
			<td><?php echo $controles->botonGenerico('/mutual/mutual_tipo_asientos/del/'.$tipo['MutualTipoAsiento']['id'],'controles/user-trash.png',null,null,"BORRAR EL TIPO ASIENTO #" .$tipo['TipoAsiento']['id'])?></td>
			<td><?php echo $tipo['MutualTipoAsiento']['id']?></td>
			<td><?php echo $tipo['MutualTipoAsiento']['concepto']?></td>
			<td align="center"><?php echo $tipo['MutualTipoAsiento']['tipo_asiento']?></td>
		</tr>

	<?php endforeach;?>
</table>