<h1>CUPONES DE ANSES - SOLICITUD Nro. <?php echo $nro_solicitud?></h1>
<hr>
<table>
	<tr>
		<th>Cup&oacute;n</th>
		<th>Nro Transacci&oacute;n</th>
		<th>Concepto</th>
	</tr>
	<?php foreach($cupones as $cupon):?>
		<tr>
			<td align="center"><a href="http://<?php echo $_SERVER['HTTP_HOST']?>/<?php echo $pathToViewCuponAnses.'?UID='.$conexion.'&CUPON='. $cupon['solicitud_cupones_anses']['id']?>" target="blank"><?php echo $html->image('controles/page_world.png',array("border"=>"0"))?></a><?php //   echo $controles->openWindow('',$pathToViewCuponAnses.'?UID='.$conexion.'&CUPON='. $cupon['solicitud_cupones_anses']['id'],null,'controles/page_world.png')?></td>
			<td align="center"><?php echo $cupon['solicitud_cupones_anses']['nro_transaccion']?></td>
			<td><?php echo $cupon['solicitud_cupones_anses']['concepto']?></td>
		</tr>
	<?php endforeach;?>
</table>
<?php //   debug($cupones)?>