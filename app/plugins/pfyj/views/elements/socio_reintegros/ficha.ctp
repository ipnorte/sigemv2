<table>
	<tr>
		<th colspan="2">DATOS DEL REINTEGRO</th>
	</tr>
	<tr>
		<td align="right">ORDEN DE REINTEGRO:</td><td><strong>#<?php echo $reintegro['SocioReintegro']['id']?></strong></td>
	</tr>	
	<tr>
		<td align="right">LIQUIDACION:</td><td><strong><?php echo $reintegro['SocioReintegro']['liquidacion_str']?></strong></td>
	</tr>
	<tr>
		<td align="right">TIPO:</td><td><strong><?php echo $reintegro['SocioReintegro']['tipo']?></strong></td>
	</tr>	
	<tr>
		<td align="right">FECHA:</td><td><strong><?php echo $util->armaFecha($reintegro['SocioReintegro']['created'])?></strong></td>
	</tr>	

	<tr>
		<td align="right">DEBITADO:</td><td align="right"><strong><?php echo $util->nf($reintegro['SocioReintegro']['importe_debitado'])?></strong></td>
	</tr>
	<tr>
		<td align="right">IMPUTADO:</td><td align="right"><strong><?php echo $util->nf($reintegro['SocioReintegro']['importe_imputado'])?></strong></td>
	</tr>
	<tr>
		<td align="right">REINTEGRO:</td><td align="right"><strong><?php echo $util->nf($reintegro['SocioReintegro']['importe_reintegro'])?></strong></td>
	</tr>						
	<tr>
		<td align="right">REVERSADO:</td><td align="right"><strong><?php echo $util->nf($reintegro['SocioReintegro']['importe_reversado'])?></strong></td>
	</tr>
	<tr>
		<td align="right">IMP.DEUDA:</td><td align="right"><strong><?php echo $util->nf($reintegro['SocioReintegro']['importe_aplicado'])?></strong></td>
	</tr>			
	<tr>
		<td align="right">PAGOS AL SOCIO:</td><td align="right"><strong><?php echo $util->nf($reintegro['SocioReintegro']['pagos'])?></strong></td>
	</tr>						
	<tr>
		<td align="right">SALDO:</td><td align="right" class="selected"><strong><?php echo $util->nf($reintegro['SocioReintegro']['saldo'])?></strong></td>
	</tr>						
</table>
	
<?php //   debug($reintegro)?>
