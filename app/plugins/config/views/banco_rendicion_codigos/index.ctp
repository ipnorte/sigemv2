<?php echo $this->renderElement('head',array('title' => 'BANCO :: CODIGOS DE RENDICION'))?>
<div class="actions">
<?php echo $controles->botonGenerico('add/'.$banco['Banco']['id'],'controles/add.png','Nuevo Codigo')?>
&nbsp;|&nbsp;
<?php echo $controles->btnRew('Regresar al Listado de Bancos','/config/bancos')?>
</div>

<div class="areaDatoForm">
	BANCO: <strong><?php echo $banco['Banco']['id']?></strong>
	&nbsp;-&nbsp;
	<strong><?php echo $banco['Banco']['nombre']?></strong>
</div>
<table>
	<tr>
		<th></th>
		<th>CODIGO</th>
		<th>DESCRIPCION</th>
		<th>INDICA PAGO</th>
		<th>CALIFICACION SOCIO</th>
	</tr>
	<?php foreach($codigos as $codigo):?>
		<tr class="activo_<?php echo $codigo['BancoRendicionCodigo']['indica_pago']?>">
			<td><?php echo $controles->getAcciones($codigo['BancoRendicionCodigo']['id'],false) ?></td>
			<td align="center"><strong><?php echo $codigo['BancoRendicionCodigo']['codigo']?></strong></td>
			<td><?php echo $codigo['BancoRendicionCodigo']['descripcion']?></td>
			<td align="center"><?php echo $controles->onOff($codigo['BancoRendicionCodigo']['indica_pago'])?></td>
			<td align="center"><?php echo $util->globalDato($codigo['BancoRendicionCodigo']['calificacion_socio'])?></td>
		</tr>
	<?php endforeach;?>
</table>
<?php //   debug($codigos)?>