<?php echo $this->renderElement('proveedor/proveedor_header',array('proveedor' => $proveedores, 'plugin' => 'proveedores'))?>

<h3>CUENTA CORRIENTE OPERATIVO</h3>

<div>
	<?php 
        echo $controles->botonGenerico('/proveedores/proveedor_listados/cta_cte_operativo_xls/' . $proveedores['Proveedor']['id'],'controles/ms_excel.png', null, array('id' => 'xls'));
        echo $controles->botonGenerico('/proveedores/proveedor_listados/cta_cte_operativo_pdf/' . $proveedores['Proveedor']['id'],'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
	?>	

    <table class="areaDatoForm">

	<tr border="0">
		<th>FECHA</th>
		<th>CONCEPTO</th>
		<th>DEBE</th>
		<th>HABER</th>
		<th>SALDO</th>
		<th>REFERENCIA</th>
	</tr>
	<?php
	$i = 0;
	foreach ($ctaCte as $renglon):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
		<tr<?php echo $class;?> >
			<td><?php echo date('d/m/Y',strtotime($renglon['fecha']))?></td>
			<td><strong><?php echo $renglon['concepto']?></strong></td>
			<td align="right"><?php echo ($renglon['debe'] == 0  ? '' : number_format($renglon['debe'],2, ',','.'))?></td>
			<td align="right"><?php echo ($renglon['haber'] == 0 ? '' : number_format($renglon['haber'],2, ',','.'))?></td>
			<td align="right"><?php echo number_format($renglon['saldo'],2, ',','.')?></td>
			<td><?php echo $renglon['comentario']?></td>
		</tr>
	<?php endforeach; ?>	
    </table>

    <?php 
        echo $controles->botonGenerico('/proveedores/proveedor_listados/cta_cte_operativo_xls/' . $proveedores['Proveedor']['id'],'controles/ms_excel.png', null, array('id' => 'xls'));
        echo $controles->botonGenerico('/proveedores/proveedor_listados/cta_cte_operativo_pdf/' . $proveedores['Proveedor']['id'],'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
    ?>	

</div>
