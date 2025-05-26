<?php echo $this->renderElement('head',array('title' => 'REPROGRAMAR ORDEN DE DESCUENTO','plugin' => 'config'))?>
<?php echo $this->renderElement('orden_descuento/form_search_by_numero',array('accion' => 'reprogramar','plugin' => 'mutual'))?>
<?php if(isset($ordenes) && count($ordenes) != 0):?>
<div class="areaDatoForm2">Se muestran los primeros <strong><?php echo count($ordenes)?></strong> registros</div>
<h3>LISTADO DE ORDENES DE DESCUENTOS</h3>

<table>

	<tr>
		<th>ORDEN</th>
		<th>FECHA</th>
		<th>PERSONA</th>	
		<th>INICIA</th>
		<th>1er VTO</th>
		<th>TIPO / NUMERO</th>
		<th>PROVEEDOR - PRODUCTO</th>
		<th>DEVENGADO</th>
		<th>PER</th>
		<th>BENEFICIO</th>
		<th></th>
	</tr>

<?php
$i = 0;

foreach ($ordenes as $orden):

	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
//	debug($sol);
?>	
	<tr class="<?php echo $orden['OrdenDescuento']['tipo_orden_dto']?>">
	
		<td align="center"><strong><?php echo $controles->openWindow($orden['OrdenDescuento']['id'],'/mutual/orden_descuentos/view/'.$orden['OrdenDescuento']['id'].'/'.$orden['OrdenDescuento']['socio_id'].'/1/1')?></strong></td>
		<td nowrap="nowrap"><?php echo $util->armaFecha($orden['OrdenDescuento']['fecha'])?></td>
		<td nowrap="nowrap"><strong><?php echo $orden['OrdenDescuento']['persona']?></strong></td>
		<td nowrap="nowrap"><?php echo $util->periodo($orden['OrdenDescuento']['periodo_ini'])?></td>
		<td nowrap="nowrap"><?php echo $util->armaFecha($orden['OrdenDescuento']['primer_vto_socio'])?></td>
		<td nowrap="nowrap"><?php echo $orden['OrdenDescuento']['tipo_orden_dto']?> #<?php echo $orden['OrdenDescuento']['numero']?></td>
		<td nowrap="nowrap"><?php echo $orden['OrdenDescuento']['proveedor_producto']?></td>
		<td align="right"><?php echo number_format($orden['OrdenDescuento']['importe_devengado'],2)?></td>
		<td align="center"><?php echo $controles->OnOff2($orden['OrdenDescuento']['permanente'],true)?></td>
		<td><?php echo $this->requestAction('/pfyj/persona_beneficios/view/'.$orden['OrdenDescuento']['persona_beneficio_id'])?></td>
		<td><?php echo $controles->botonGenerico('/mutual/orden_descuentos/reprogramar/'.$orden['OrdenDescuento']['id'],'controles/calendar.png','')?></td>
	</tr>

<?php endforeach;?>	

</table>

<?php //   debug($ordenes)?>



<?php endif;?>