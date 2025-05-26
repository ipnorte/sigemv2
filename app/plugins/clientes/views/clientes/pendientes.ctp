<?php 
//debug($clientes);
?>
<script language="Javascript" type="text/javascript">
	var estadoFiltro = 0;
//	var colum = 2;		
	Event.observe(window, 'load', function() {
	});


</script>		
<?php echo $this->renderElement('clientes/cliente_header',array('cliente' => $clientes))?>
<h3>PENDIENTES DE PAGOS</h3>

 
<div class="contenedor">

	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<col width="163" />
		<col width="519" />
		<col width="95" />
		<col width="80" />
		<col width="80" />
		<col width="80" />
		<col width="74" />
		<col width="80" />
		
		<tr>
			<th class="dato">Comprobante</th>
			<th class="dato">Comentario</th>
			<th class="dato" align="center">Fecha Comp.</th>
			<th class="dato">Vencim.</th>
			<th class="dato" align="right">Imp.Comp.</th>
			<th class="dato" align="right">Imp.Venc.</th>
			<th class="dato" align="right">Pagado</th>
			<th class="dato" align="right">Saldo</th>
		</tr>

	</table>

	<div class="cuerpo">
		<table id="data_table" border="0" cellpadding="0" cellspacing="0" width="100%">
			<col width="210" />
			<col width="550" />
			<col width="100" />
			<col width="80" />
			<col width="80" />
			<col width="80" />
			<col width="80" />
		
			<?php
				$i = 0;
				$nTotal = 0;
			  	foreach($facturaPendiente as $fPendiente):
			  		$i++;
			  		$linea = "TRL_" . $i;
			  		$nTotal += $fPendiente['saldo']; 

		  		?>
		  		<tr id='LTR_<?php echo $i;?>'>
					<td class="dato"><?php echo $fPendiente['tipo_comprobante_desc'] ?></td>
					<td class="dato"><?php echo $fPendiente['comentario'] ?></td>
					<td class="dato" align="center"><?php echo $fPendiente['fecha_comprobante'] == '  /  /  ' ? '' : $util->armaFecha($fPendiente['fecha_comprobante'])?></td>
					<td class="dato" align="center"><?php echo $fPendiente['vencimiento'] == '  /  /  ' ? '' : $util->armaFecha($fPendiente["vencimiento"])?></td>
					<td class="dato" align="right"><?php echo number_format($fPendiente['total_comprobante'],2) ?></td>
					<td class="dato" align="right"><?php echo number_format($fPendiente["importe"],2) ?></td>
					<td class="dato" align="right"><?php echo number_format($fPendiente["pago"],2) ?></td>
					<td class="dato" align="right"><?php echo number_format($fPendiente["saldo"],2) ?></td>
		  		</tr>
	  		<?php endforeach;?>
 		</table>
 	</div>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="1%">
					<?php echo $controles->botonGenerico('/clientes/clientes/salida/' . $clientes['Cliente']['id'] . '/XLS','controles/ms_excel.png')?>
				</td>
				<td width="1%">
					<?php echo $controles->botonGenerico('/clientes/clientes/salida/' . $clientes['Cliente']['id'] . '/PDF','controles/pdf.png', null, array('target' => '_blank'))?>
				</td>
				<td colspan="6" align="right">SALDO CLIENTE: <?php echo number_format($nTotal,2) ?></td>
				</td>
			</tr>
		</table> 	
 	
 </div>
 