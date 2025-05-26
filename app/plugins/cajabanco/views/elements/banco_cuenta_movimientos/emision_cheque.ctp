<div>
<br />
FECHA: <strong><?php echo $util->armaFecha($movimiento[0]['fecha_operacion'])?></strong>
<br/>
CONCEPTO: <strong><?php echo $movimiento[0]['concepto']?></strong>
<br/>
DESCRIPCION: <strong><?php echo $movimiento[0]['descripcion'] ?></strong>
<br />
DESTINATARIO: <strong><?php echo $movimiento[0]['destinatario']?></strong>
<br />
<?php if($movimiento[0]['anulado'] == 1):?>
ESTADO: <strong><span style="color:red;">ANULADO</span></strong>
<br />
<?php endif;?>
<hr>
<br />
	<table>
		<tr>
			<th>NUMERO CHEQUE</th>
			<th>VENCIMIENTO</th>
			<th>IMPORTE</th>
		</tr>
		<tr>
			<td><?php echo $movimiento[0]['numero_operacion']?></td>
			<td><?php echo $util->armaFecha($movimiento[0]['fecha_vencimiento'])?></td>
			<td><?php echo $movimiento[0]['importe'] ?></td>
		</tr>
	</table>
</div>

<?php if($movimiento[0]['reemplazar'] == 1):?>
	<hr>
	<div class="areaDatoForm">
		<h3>REEMPLAZADO POR</h3>
	
		<?php if($movimiento[1]['tipo'] == 7):?>
			<h3><?php echo $movimiento[1]['banco_cuenta'] . '  EFECTIVO'?></h3>
			FECHA: <strong><?php echo $util->armaFecha($movimiento[1]['fecha_operacion'])?></strong>
			<br/>
			CONCEPTO: <strong><?php echo $movimiento[1]['concepto']?></strong>
			<br/>
			DESCRIPCION: <strong><?php echo $movimiento[1]['descripcion'] ?></strong>
			<br />
			DESTINATARIO: <strong><?php echo $movimiento[1]['destinatario']?></strong>
			<br />
			IMPORTE: <strong><?php echo $movimiento[1]['importe'] ?></strong>
			<hr/>
		<?php else:?>
			<h3><?php echo $movimiento[1]['banco_cuenta']?></h3>
			FECHA: <strong><?php echo $util->armaFecha($movimiento[1]['fecha_operacion'])?></strong>
			<br/>
			CONCEPTO: <strong><?php echo $movimiento[1]['concepto']?></strong>
			<br/>
			DESCRIPCION: <strong><?php echo $movimiento[1]['descripcion'] ?></strong>
			<br />
			DESTINATARIO: <strong><?php echo $movimiento[1]['destinatario']?></strong>
			<br />
			<br />
			<hr>
			<table>
				<tr>
					<th>NUMERO CHEQUE</th>
					<th>VENCIMIENTO</th>
					<th>IMPORTE</th>
				</tr>
				<tr>
					<td><?php echo $movimiento[1]['numero_operacion']?></td>
					<td><?php echo $util->armaFecha($movimiento[1]['fecha_vencimiento'])?></td>
					<td><?php echo $movimiento[1]['importe'] ?></td>
				</tr>
			</table>

		<?php endif;?>
	</div>
<?php endif;?>
<br />
