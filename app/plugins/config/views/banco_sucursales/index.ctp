<?php echo $this->renderElement('head',array('title' => 'SUCURSALES BANCARIAS'))?>
<div class="actions">
<?php echo $controles->botonGenerico('add/'.$banco['Banco']['id'],'controles/add.png','Nueva Sucursal')?>
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
		<th>SUCURSAL</th>
                <th>BCRA</th>
		<th>NOMBRE</th>
		<th>DIRECCION</th>
		<th></th>
	</tr>
<?php
$i = 0;
foreach ($sucursales as $sucursal):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>	
	
<tr<?php echo $class;?>>
    <td style="text-align: center;"><?php echo $sucursal['BancoSucursal']['nro_sucursal']?></td>
        <td style="text-align: center;"><?php echo $sucursal['BancoSucursal']['codigo_bcra']?></td>
	<td><?php echo $sucursal['BancoSucursal']['nombre']?></td>
	<td><?php echo $sucursal['BancoSucursal']['direccion']?></td>
	<td class="actions"><?php echo $controles->getAcciones($sucursal['BancoSucursal']['id'],false) ?></td>
</tr>	
	
<?php endforeach;?>	

</table>

<?php // debug($sucursales)?>

