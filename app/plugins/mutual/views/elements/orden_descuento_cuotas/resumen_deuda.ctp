<?php 
$resumen = $this->requestAction('/mutual/orden_descuento_cuotas/get_resumen_deuda/'.$socio_id);
?>
<?php if(!empty($resumen)):?>
	<table>
		<tr>
			<th colspan="3">RESUMEN GENERAL DE DEUDA</th>
		</tr>
		<tr>
			<th>SITUACION</th>
			<th>ADEUDADO VENCIDO</th>
			<th>ADEUDADO A VENCER</th>
		</tr>
		<?php foreach($resumen as $item):?>
			<tr>
				<td><?php echo $item['descripcion_situacion']?></td>
				<td align="right"><?php echo $util->nf($item['total_adeudado_vencido'])?></td>
				<td align="right"><?php echo $util->nf($item['total_adeudado_avencer'])?></td>
			</tr>
		<?php endforeach;?>
	</table>

<?php endif;?>