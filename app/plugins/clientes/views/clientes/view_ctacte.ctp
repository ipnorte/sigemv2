<?php echo $this->renderElement('clientes/cliente_header',array('cliente' => $cliente))?>

<h3>CUENTA CORRIENTE</h3>

<?php echo $this->renderElement('paginado')?>

<div>
<table class="areaDatoForm">

	<tr border="1">
		<th></th>
		<th>FECHA</th>
		<th>CONCEPTO</th>
		<th>DEBE</th>
		<th>HABER</th>
		<th>SALDO</th>
		<th>REFERENCIA</th>
		<th>ANULAR/BORRAR</th>
		<th><?php echo $controles->botonGenerico('imprimir_ctacte/'. $cliente['Cliente']['id'],'controles/printer.png',null, array('target' => 'blank'));?></th>
                <th></th>
	</tr>
	<?php
	$i = 0;
        $nItem = 0;
	foreach ($ctaCte as $renglon):
                if($nItem > 50) break;
                $nItem += 1;
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
		<tr<?php echo $class;?> >
			<?php if($renglon['ClienteCtacte']['tipo'] == 'REC'): ?>
				<td align="center"><?php echo $controles->botonGenerico('edit/'.$renglon['ClienteCtacte']['id'],'controles/folder_user.png')?></td>
			<?php else: ?>
				<td></td>
			<?php endif ?>
			<td align="center"><?php echo date('d/m/Y',strtotime($renglon['ClienteCtacte']['fecha']))?></td>
			<td><strong><?php echo $renglon['ClienteCtacte']['concepto']?></strong></td>
			<td align="right"><?php echo ($renglon['ClienteCtacte']['debe'] == 0  ? '' : number_format($renglon['ClienteCtacte']['debe'],2, ',','.'))?></td>
			<td align="right"><?php echo ($renglon['ClienteCtacte']['haber'] == 0 ? '' : number_format($renglon['ClienteCtacte']['haber'],2, ',','.'))?></td>
			<td align="right"><?php echo number_format($renglon['ClienteCtacte']['saldo'],2, ',','.')?></td>
			<td><?php echo $renglon['ClienteCtacte']['comentario'] ?></td>
			<?php if($renglon['ClienteCtacte']['tipo'] == 'REC'): ?>
				<td align="center"><?php echo ($renglon['ClienteCtacte']['anular'] == 0 ? $controles->botonGenerico('anular_recibo/'. $renglon['ClienteCtacte']['id'] . '/' . $cliente['Cliente']['id'],'controles/12-em-cross.png', '', null, 'ESTA SEGURO DE ANULAR \n EL ' . $renglon['ClienteCtacte']['concepto']) : '');?></td>	
				<td align="center"><?php echo $controles->botonGenerico('imprimir_recibo_pdf/'. $renglon['ClienteCtacte']['id'],'controles/printer.png',null, array('target' => 'blank'));?></td>	
				<td></td>	
			<?php else: ?>
				<td align="center"><?php echo ($renglon['ClienteCtacte']['anular'] == 0 ? $controles->botonGenerico('anular_factura/'. $renglon['ClienteCtacte']['id'] . '/' . $cliente['Cliente']['id'],'controles/12-em-cross.png', '', null, 'ESTA SEGURO DE ANULAR \n' . $renglon['ClienteCtacte']['concepto']) : '');?></td>
				<td align="center"><?php echo $controles->botonGenerico('imprimir_factura/'. $renglon['ClienteCtacte']['id'],'controles/printer.png',null, array('target' => 'blank'));?></td>	
                            <?php if($renglon['ClienteCtacte']['pagos'] > 0){
                                    $url = '/clientes/recibos/detalle_pago_facturas/' . $renglon['ClienteCtacte']['id']?>
                                <td><?php echo $controles->btnModalBox(array('title' => 'DETALLE DE PAGO','img'=> 'information.png','url' => $url,'h' => 350, 'w' => 550))?></td>
                            <?php }else{?>
                                <td></td>
                            <?php }?>
			<?php endif ?>
		</tr>
	<?php endforeach; ?>	
</table>
</div>

<?php echo $this->renderElement('paginado')?>
