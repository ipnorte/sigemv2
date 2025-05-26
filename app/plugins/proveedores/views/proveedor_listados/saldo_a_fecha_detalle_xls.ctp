<?php
$contenido = "Content-Disposition: attachment; filename=cta-cte-detalle.xls";
header('Content-type: application/vnd.ms-excel');
header($contenido);
header("Content-Transfer-Encoding: binary");
header("Pragma: no-cache");
header("Expires: 0");

App::import('Model', 'proveedores.ProveedorListado');
App::import('Model', 'proveedores.Movimiento');
App::import('Model', 'proveedores.Proveedor');

$oProveedorListado = new ProveedorListado();
$oProveedor = new Proveedor();
$oMovimiento = new Movimiento();
?>

<h1>CTA. CTE :: FECHA: <?php echo date('d/m/Y', strtotime($desdeFecha)) . ' AL ' . date('d/m/Y', strtotime($hastaFecha))?></h1>
<?php
foreach($saldos as $saldo):

	if($saldo[0]['saldo_anterior'] != 0 || $saldo[0]['pago'] != 0 || $saldo[0]['credito'] != 0 || $saldo[0]['debito'] != 0 ||
	   $saldo[0]['saldo'] != 0 || $saldo[0]['saldo_actual'] != 0):
	$proveedor = $oMovimiento->traerProveedor($saldo['Proveedor']['id']);

	$ctaCte = $oProveedorListado->ctaCteFecha($saldo['Proveedor']['id'], $desdeFecha, $hastaFecha);
	
?>
	<h3><?php echo $saldo['Proveedor']['cuit'] . '-' . $saldo['Proveedor']['razon_social'] ?> </h3> 
	<table class="areaDatoForm">
	
		<tr border="0">
			<th>FECHA</th>
			<th>CONCEPTO</th>
			<th>REFERENCIA</th>
			<th>DEBE</th>
			<th>HABER</th>
			<th>SALDO</th>
		</tr>
		<tr>
			<td></td>
			<td>SALDO AL <?php echo date('d/m/Y', strtotime($fecha_saldo_anterior))?></td>		
			<td></td>
			<td></td>
			<td align="right"><?php echo number_format($saldo[0]['saldo_anterior'],2, ',','.')?></td>
			<td></td>
			<td></td>
			<td></td>	
		</tr>
		<?php
		$i = 0;
		$saldo_anterior = $saldo[0]['saldo_anterior'];
		foreach ($ctaCte as $renglon):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
			$saldo_anterior += $renglon['debe'] - $renglon['haber'];
		?>
			<tr<?php echo $class;?> >
				<td align="center"><?php echo date('d/m/Y',strtotime($renglon['fecha']))?></td>
				<td><strong><?php echo $renglon['concepto']?></strong></td>
				<td><?php echo $renglon['comentario']?></td>
				<td align="right"><?php echo ($renglon['debe'] == 0  ? '' : number_format($renglon['debe'],2, ',','.'))?></td>
				<td align="right"><?php echo ($renglon['haber'] == 0 ? '' : number_format($renglon['haber'],2, ',','.'))?></td>
				<td align="right"><?php echo number_format($saldo_anterior,2, ',','.')?></td>
			</tr>
		<?php endforeach; ?>	
	</table>
	
<?php 
	endif;
endforeach; 
?>