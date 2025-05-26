<h5>INICIO Y VENCIMIENTOS</h5>
<table>

	<tr>
		<td>ULTIMA LIQUIDACION CERRADA</td><td style="font-weight: bold;"><?php echo (!empty($vto['ultimo_periodo_liquidado']) ? $util->periodo($vto['ultimo_periodo_liquidado'],true) : '')?></td>
	</tr>

	<tr>
		<td>FECHA DE CARGA</td><td><?php echo $util->armaFecha($vto['fecha_carga'])?></td>
	</tr>
	<tr>
		<td>ESTA ORDEN INICIA EN:</td><td style="<?php echo($vto['inicia_en'] > $vto['ultimo_periodo_liquidado'] ? 'color: green;':'color: red;')?>"><strong><?php echo $util->periodo($vto['inicia_en'],true)?></strong></td>
	</tr>
	<tr>
		<td>VTO 1er CUOTA (SOCIO):</td><td style="color: green;"><?php echo $util->armaFecha($vto['vto_primer_cuota_socio'])?></td>
	</tr>
	<tr>
		<td>VTO 1er CUOTA (PROVEEDOR):</td><td><?php echo $util->armaFecha($vto['vto_primer_cuota_proveedor'])?></td>
	</tr>			
	
</table>
<?php echo $frm->hidden("ProveedorVencimiento.inicia_en",array('value' => $vto['inicia_en']))?>
<?php echo $frm->hidden("ProveedorVencimiento.fecha_carga",array('value' => $vto['fecha_carga']))?>
<?php echo $frm->hidden("ProveedorVencimiento.vto_primer_cuota_socio",array('value' => $vto['vto_primer_cuota_socio']))?>
<?php echo $frm->hidden("ProveedorVencimiento.vto_primer_cuota_proveedor",array('value' => $vto['vto_primer_cuota_proveedor']))?>

<?php if($vto['inicia_en'] <= $vto['ultimo_periodo_liquidado']):?>
<div class="notices_error">
<strong>ATENCION!:</strong> El periodo de inicio es anterior a la &uacute;ltima liquidaci&oacute;n cerrada. Las cuotas ser&aacute;n devengadas y tratadas como DEUDA VENCIDA.
</div>
<?php endif;?>
