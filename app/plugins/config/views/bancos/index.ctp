<?php echo $this->renderElement('head',array('title' => 'BANCOS'))?>
<div class="actions">
<?php echo $controles->botonGenerico('add','controles/add.png','Nuevo Banco')?>
&nbsp;|&nbsp;
<?php echo $controles->botonGenerico('/config/bancos/gen_cbu','controles/attach.png','GENERAR CBU',array('target'=>'blank'))?>

</div>

<?php echo $this->renderElement('paginado')?>

<table>
	<tr>
		<th><?php echo $paginator->sort('#BCRA','Banco.id');?></th>
		<th><?php echo $paginator->sort('DENOMINACION','Banco.nombre');?></th>
		<th><?php echo $paginator->sort('ACTIVO','Banco.activo');?></th>
		<th><?php echo $paginator->sort('BENEFICIO','Banco.beneficio');?></th>
		<th>COD.CTA.SUELDO</th>
		<th>F PAGO</th>
		<th>INTERC.</th>
		<th>SUC</th>
		<th>CODIGOS</th>
		<th></th>
	</tr>
<?php
$i = 0;
foreach ($bancos as $banco):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>	
	
<tr<?php echo $class;?>>
	<td><?php echo $banco['Banco']['id']?></td>
	<td><?php echo $banco['Banco']['nombre']?></td>
	<td align="center"><?php echo $controles->onOff($banco['Banco']['activo'])?></td>
	<td align="center"><?php echo $controles->onOff($banco['Banco']['beneficio'],true)?></td>
	<td align="center"><?php echo $banco['Banco']['tipo_cta_sueldo']?></td>
	<td align="center"><?php echo $controles->onOff($banco['Banco']['fpago'],true)?></td>
	<td align="center"><?php echo $controles->onOff($banco['Banco']['intercambio'],true)?></td>
	<td align="center"><?php echo $controles->botonGenerico('/config/banco_sucursales/index/'.$banco['Banco']['id'],'controles/folder_home.png') ?></td>
	<td align="center"><?php echo $controles->botonGenerico('/config/banco_rendicion_codigos/index/'.$banco['Banco']['id'],'controles/text_list_bullets.png') ?></td>
	<td class="actions"><?php echo $controles->getAcciones($banco['Banco']['id'],false) ?></td>
</tr>	
	
<?php endforeach;?>	
</table>

<?php //   debug($bancos)?>

<?php echo $this->renderElement('paginado')?>