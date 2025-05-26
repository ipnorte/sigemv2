<?php echo $this->renderElement('head',array('title' => 'FORMATOS ARCHIVOS EXCEL PARA INTERCAMBIO DE INFORMACION'))?>
<table>

	<tr>
		<th colspan="2">ARCHIVO DE ENTRADA</th>
	</tr>
	<tr>
		<td>TIPOS PERMITIDOS</td>
		<td style="background: #FFFF88;">Microsoft Excel 97/2000/XP (.xls)</td>
	</tr>
	<tr>
		<td>CONSIDERACIONES GENERALES</td>
		<td style="background: #FFFF88;">LOS DATOS DEBEN COMENZAR A PARTIR DE LA FILA 1 (SIN TITULOS DE COLUMNAS)</td>
	</tr>
	<tr>
		<td colspan="2">DETALLE DE COLUMNAS</td>
	</tr>
	<tr>
		<td>COLUMNA_1</td>
		<td style="background: #FFFF88;">IDENTIFICADOR UNIVOCO DEL REGISTRO, REFERENCIA PARA IDENTIFICAR EL DEBITO EN LA RENCICION. LONGITUD MAXIMA 16 CARACTERES (BCO. NACION TOMA SOLAMENTE 10)</td>
	</tr>
	<tr>
		<td>COLUMNA_2</td>
		<td style="background: #FFFF88;">NUMERO DE REGISTRO PARA EN CASO DE DOS O MAS DEBITOS PARA UNA MISMA CUENTA</td>
	</tr>		
	<tr>
		<td>COLUMNA_3</td>
		<td style="background: #FFFF88;">NUMERO DE COMPROBANTE</td>
	</tr>
	<tr>
		<td>COLUMNA_4</td>
		<td style="background: #FFFF88;">NUMERO DE SUCURSAL</td>
	</tr>
	<tr>
		<td>COLUMNA_5</td>
		<td style="background: #FFFF88;">NUMERO DE CUENTA</td>
	</tr>
	<tr>
		<td>COLUMNA_6</td>
		<td style="background: #FFFF88;">CBU</td>
	</tr>
	<tr>
		<td>COLUMNA_7</td>
		<td style="background: #FFFF88;">IMPORTE (2 DECIMALES)</td>
	</tr>				
</table>


<table>

	<tr>
		<th colspan="2">ARCHIVO DE SALIDA</th>
	</tr>
	<tr>
		<td>TIPO</td>
		<td style="background: #CDEB8B;">Microsoft Excel 97/2000/XP (.xls)</td>
	</tr>
	<tr>
		<td colspan="2"><STRONG>DETALLE DE COLUMNAS</STRONG></td>
	</tr>
	<tr>
		<td>COLUMNA_1</td>
		<td style="background: #CDEB8B;">IDENTIFICADOR UNIVOCO DEL REGISTRO</td>
	</tr>
	<tr>
		<td>COLUMNA_2</td>
		<td style="background: #CDEB8B;">NUMERO DE REGISTRO</td>
	</tr>		
	<tr>
		<td>COLUMNA_3</td>
		<td style="background: #CDEB8B;">NUMERO DE COMPROBANTE</td>
	</tr>
	<tr>
		<td>COLUMNA_4</td>
		<td style="background: #CDEB8B;">NUMERO DE SUCURSAL</td>
	</tr>
	<tr>
		<td>COLUMNA_5</td>
		<td style="background: #CDEB8B;">NUMERO DE CUENTA</td>
	</tr>
	<tr>
		<td>COLUMNA_6</td>
		<td style="background: #CDEB8B;">CBU</td>
	</tr>
	<tr>
		<td>COLUMNA_7</td>
		<td style="background: #CDEB8B;">IMPORTE</td>
	</tr>
	<tr>
		<td>COLUMNA_8</td>
		<td style="background: #CDEB8B;">STATUS DEL DEBITO</td>
	</tr>
	<tr>
		<td>COLUMNA_9</td>
		<td style="background: #CDEB8B;">PAGO (1 = DEBITADO, 0 = NO DEBITADO)</td>
	</tr>						
</table>
