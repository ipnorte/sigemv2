<?php echo $this->renderElement('proveedor/proveedor_header',array('proveedor' => $proveedores, 'plugin' => 'proveedores'))?>

<h3>CUENTA CORRIENTE</h3>

<div>
<table class="areaDatoForm">

	<tr border="0">
		<th></th>
		<th>FECHA</th>
		<th>CONCEPTO</th>
		<th>DEBE</th>
		<th>HABER</th>
		<th>SALDO</th>
		<th>REFERENCIA</th>
		<th>ANULAR/BORRAR</th>
		<th><?php echo $controles->botonGenerico('imprimir_ctacte/'. $proveedores['Proveedor']['id'],'controles/printer.png',null, array('target' => '_blank'));?></th>	
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
			<?php if($renglon['tipo'] == 'OPA'): ?>
				<td align="center"><?php echo $controles->botonGenerico('edit/'.$renglon['id'],'controles/folder_user.png')?></td>
			<?php else: ?>
				<td></td>
			<?php endif ?>
			<td><?php echo date('d/m/Y',strtotime($renglon['fecha']))?></td>
			<td><strong><?php echo $renglon['concepto']?></strong></td>
			<td align="right"><?php echo ($renglon['debe'] == 0  ? '' : number_format($renglon['debe'],2, ',','.'))?></td>
			<td align="right"><?php echo ($renglon['haber'] == 0 ? '' : number_format($renglon['haber'],2, ',','.'))?></td>
			<td align="right"><?php echo number_format($renglon['saldo'],2, ',','.')?></td>
			<td><?php echo $renglon['comentario']?></td>
			<?php if($renglon['tipo'] == 'OPA'): ?>
				<td align="center"><?php echo ($renglon['anular'] == 0 ? $controles->botonGenerico('anular/'. $renglon['id'] . '/' . $proveedores['Proveedor']['id'],'controles/12-em-cross.png', '', null, 'ESTA SEGURO DE ANULAR \n LA ' . $renglon['concepto']) : '');?></td>	
				<td align="center"><?php echo $controles->botonGenerico('imprimir_opago/'. $renglon['id'],'controles/printer.png',null, array('target' => 'blank'));?></td>	
				<td></td>	
			<?php else: ?>
				<td align="center"><?php echo ($renglon['anular'] == 0 ? $controles->botonGenerico('borrar/'. $renglon['id'] . '/' . $proveedores['Proveedor']['id'],'controles/12-em-cross.png', '', null, 'ESTA SEGURO DE BORRAR \n' . $renglon['concepto']) : '');?></td>
				<td></td>	
                            <?php if($renglon['pagos'] > 0){
                                    $url = '/proveedores/orden_pagos/detalle_pago_facturas/' . $renglon['id']?>
                                <td><?php echo $controles->btnModalBox(array('title' => 'DETALLE DE PAGO','img'=> 'information.png','url' => $url,'h' => 350, 'w' => 550))?></td>
                            <?php }else{?>
                                <td></td>
                            <?php }?>
			<?php endif ?>
		</tr>
	<?php endforeach; ?>	
</table>
</div>