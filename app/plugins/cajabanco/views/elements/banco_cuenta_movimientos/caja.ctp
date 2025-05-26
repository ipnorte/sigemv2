<div>
<br />
FECHA: <strong><?php echo $util->armaFecha($movimiento[0]['fecha_operacion'])?></strong>
<br/>
CONCEPTO: <strong><?php echo $movimiento[0]['concepto']?></strong>
<br/>
DESCRIPCION: <strong><?php echo $movimiento[0]['descripcion'] ?></strong>
<br />
DESTINATARIO: <strong><?php echo $movimiento[0]['destinatario'];?></strong>

<hr>
<br />
	<table>
		<tr>
			<th>D E S C R I P C I O N</th>
			<th>IMPORTE</th>
		</tr>
		<tr>
			<td><?php echo $movimiento[0]['banco_cuenta'] . '  EFECTIVO'?></td>
			<td><?php echo $movimiento[0]['importe'] ?></td>
		</tr>
	</table>
</div>


<!-- 
<br />
BANCO: <strong><?php echo $movimiento[0]['banco_cuenta']?></strong>
<br />
NUMERO OPERACCION: <strong><?php echo $movimiento[0]['numero_operacion']?></strong>
<br />
IMPORTE: <strong><?php echo $movimiento[0]['importe'] ?></strong>
<br />
<br />
 -->