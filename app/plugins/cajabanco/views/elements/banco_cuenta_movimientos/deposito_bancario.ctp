<div>
<br />
FECHA: <strong><?php echo $util->armaFecha($movimiento[0]['fecha_operacion'])?></strong>
<br/>
CONCEPTO: <strong><?php echo $movimiento[0]['concepto']?></strong>
<br/>
DESCRIPCION: <strong><?php echo $movimiento[0]['descripcion'] ?></strong>
<br />
DESTINATARIO: <strong><?php echo $movimiento[0]['banco_cuenta']?></strong>
<br />
NUMERO OPERACCION: <strong><?php echo $movimiento[0]['numero_operacion']?></strong>
<br />
<br />
<?php if(isset($movimiento[0]['cheque_tercero'])):?>
	<table>
		<caption><strong>CHEQUE EN CARTERA DEPOSITADOS</strong></caption>
		<tr>
			<th>VENCIMIENTO</th>
			<th>CHEQUE NRO.</th>
			<th>LIBRADOR</th>
			<th>BANCO</th>
			<th>IMPORTE</th>
		</tr>
		<?php foreach($movimiento[0]['cheque_tercero'] as $cheque):?>
			<tr>
				<td align="center"><?php echo $util->armaFecha($cheque['BancoChequeTercero']['fecha_vencimiento'])?></td>
				<td><?php echo $cheque['BancoChequeTercero']['numero_cheque']?></td>
				<td><?php echo $cheque['BancoChequeTercero']['librador']?></td>
				<td><?php echo $cheque['BancoChequeTercero']['banco']?></td>
				<td align="right"><?php echo $cheque['BancoChequeTercero']['importe'] ?></td>
			</tr>
		<?php endforeach;?>
	</table>
<?php else:?>
	<strong>DEPOSITO EN EFECTIVO</strong>
<?php endif;?>
<br />
IMPORTE DEPOSITADO: <strong><?php echo $movimiento[0]['importe'] ?></strong>
<br />
<br />
