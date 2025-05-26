<?php
$contenido = "Content-Disposition: attachment; filename=cta-cte-detalle.xls";
header('Content-type: application/vnd.ms-excel');
header($contenido);
header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache");
header("Expires: 0");

?>

<h2>CTA. CTE OPERATIVO::</h2>
<h2><?php echo $proveedor['Proveedor']['cuit'] . '-' . $proveedor['Proveedor']['razon_social'] ?> </h2> 
	<table class="areaDatoForm">
	
		<tr border="0">
			<th>FECHA</th>
			<th>CONCEPTO</th>
			<th>REFERENCIA</th>
			<th>DEBE</th>
			<th>HABER</th>
			<th>SALDO</th>
		</tr>
		<?php
		$i = 0;
		$saldo = 0;
		foreach ($ctaCte as $renglon):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
			$saldo += $renglon['debe'] - $renglon['haber'];
		?>
			<tr<?php echo $class;?> >
				<td align="center"><?php echo date('d/m/Y',strtotime($renglon['fecha']))?></td>
				<td><strong><?php echo $renglon['concepto']?></strong></td>
				<td><?php echo $renglon['comentario']?></td>
				<td align="right"><?php echo ($renglon['debe'] == 0  ? '' : number_format($renglon['debe'],2, ',','.'))?></td>
				<td align="right"><?php echo ($renglon['haber'] == 0 ? '' : number_format($renglon['haber'],2, ',','.'))?></td>
				<td align="right"><?php echo number_format($saldo,2, ',','.')?></td>
			</tr>
		<?php endforeach; ?>	
	</table>
	
