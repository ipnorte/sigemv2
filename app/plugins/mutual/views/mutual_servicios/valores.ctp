<?php echo $this->renderElement('head',array('title' => 'ADMINISTRACION DE SERVICIOS','plugin' => 'config'))?>

<?php echo $this->renderElement("proveedor/datos_proveedor",array('plugin' => 'proveedores', 'proveedor_id' => $servicio['MutualServicio']['proveedor_id']))?>

<h3><?php echo $util->globalDato($servicio['MutualServicio']['tipo_producto'])?> :: CONFIGURACION DE TARIFAS</h3>

<div class="actions">
<?php echo $controles->btnRew("Regresar","/mutual/mutual_servicios/servicios_by_proveedor/" . $servicio['MutualServicio']['proveedor_id'])?>
&nbsp;|&nbsp;
<?php echo $controles->botonGenerico('valores_add/'.$servicio['MutualServicio']['id'],'controles/calculator_add.png','Nueva Tarifa')?>
</div>

<table>

	<tr>
		<th></th>
		<th>ORGANISMO</th>
		<th>PERIODO VIGENCIA</th>
		<th>IMPORTE TITULAR</th>
		<th>IMPORTE ADICIONAL</th>
		<th>COSTO TITULAR</th>
		<th>COSTO ADICIONAL</th>
	
	</tr>
	<?php foreach($valores as $valor):?>
	
		<tr>
			<td><?php echo $controles->botonGenerico('valores_del/'.$valor['MutualServicioValor']['id'],'controles/user-trash-full.png',null,null,"Borrar el Registro?") ?></td>
			<td><?php echo $valor['MutualServicioValor']['codigo_organismo_desc']?></td>
			<td align="center"><?php echo $util->periodo($valor['MutualServicioValor']['periodo_vigencia'],true)?></td>
			<td align="right"><?php echo $util->nf($valor['MutualServicioValor']['importe_titular'])?></td>
			<td align="right"><?php echo $util->nf($valor['MutualServicioValor']['importe_adicional'])?></td>
			<td align="right"><?php echo $util->nf($valor['MutualServicioValor']['costo_titular'])?></td>
			<td align="right"><?php echo $util->nf($valor['MutualServicioValor']['costo_adicional'])?></td>
		
		</tr>
	
	<?php endforeach;?>
	
</table>
