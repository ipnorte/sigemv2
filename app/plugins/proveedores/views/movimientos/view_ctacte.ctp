<?php echo $this->renderElement('proveedor/proveedor_header',array('proveedor' => $proveedores, 'plugin' => 'proveedores'))?>

<h3>CUENTA CORRIENTE</h3>

<?php echo $this->renderElement('paginado')?>

<div>
<table class="areaDatoForm">

	<tr>
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
            
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
		<tr<?php echo $class;?> >
			<?php if($renglon['ProveedorCtacte']['tipo'] == 'OPA'): ?>
				<td align="center"><?php echo $controles->botonGenerico('edit/'.$renglon['ProveedorCtacte']['id'],'controles/folder_user.png')?></td>
			<?php else: ?>
				<td></td>
			<?php endif ?>
			<td><?php echo date('d/m/Y',strtotime($renglon['ProveedorCtacte']['fecha']))?></td>
			<td><strong><?php echo $renglon['ProveedorCtacte']['concepto']?></strong></td>
			<td align="right"><?php echo ($renglon['ProveedorCtacte']['debe'] == 0  ? '' : number_format($renglon['ProveedorCtacte']['debe'],2, ',','.'))?></td>
			<td align="right"><?php echo ($renglon['ProveedorCtacte']['haber'] == 0 ? '' : number_format($renglon['ProveedorCtacte']['haber'],2, ',','.'))?></td>
			<td align="right"><?php echo number_format($renglon['ProveedorCtacte']['saldo'],2, ',','.')?></td>
			<td><?php echo $renglon['ProveedorCtacte']['comentario']?></td>
			<?php if($renglon['ProveedorCtacte']['tipo'] == 'OPA'): ?>
				<td align="center"><?php echo ($renglon['ProveedorCtacte']['anular'] == 0 ? $controles->botonGenerico('anular/'. $renglon['ProveedorCtacte']['id'] . '/' . $proveedores['Proveedor']['id'],'controles/12-em-cross.png', '', null, 'ESTA SEGURO DE ANULAR \n LA ' . $renglon['ProveedorCtacte']['concepto']) : '');?></td>	
				<td align="center"><?php echo $controles->botonGenerico('imprimir_opago/'. $renglon['ProveedorCtacte']['id'],'controles/printer.png',null, array('target' => 'blank'));?></td>	
				<td></td>	
			<?php else: ?>
				<td align="center"><?php echo ($renglon['ProveedorCtacte']['anular'] == 0 ? $controles->botonGenerico('borrar/'. $renglon['ProveedorCtacte']['id'] . '/' . $proveedores['Proveedor']['id'],'controles/12-em-cross.png', '', null, 'ESTA SEGURO DE BORRAR \n' . $renglon['ProveedorCtacte']['concepto']) : '');?></td>
				<td></td>	
                            <?php if($renglon['ProveedorCtacte']['pagos'] > 0){
                                    $url = '/proveedores/orden_pagos/detalle_pago_facturas/' . $renglon['ProveedorCtacte']['id']?>
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
