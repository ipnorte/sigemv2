<?php echo $this->renderElement('head',array('title' => 'LISTADOS','plugin' => 'config'))?>
<?php echo $this->renderElement('cliente_listados/menu_listado',array('plugin' => 'clientes'))?>
<h1>LISTADO SALDO A FECHA</h1>
<hr>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('form_saldo_a_fecha').disable();
	<?php endif;?>

});
</script>

<?php
?>

<?php echo $frm->create(null,array('action' => 'saldo_a_fecha','id' => 'form_saldo_a_fecha'))?>
<div class="areaDatoForm">

	<table class="tbl_form">
	
		<tr>
			<td>FECHA DESDE:</td>
			<td><?php echo $frm->calendar('SaldoFecha.fecha_desde', null, $fecha_desde, '2000',date("Y") + 1)?></td>
		</tr>
		
		<tr>
			<td>FECHA HASTA:</td>
			<td><?php echo $frm->calendar('SaldoFecha.fecha_hasta', null, $fecha_hasta, '2000',date("Y") + 1)?></td>
		</tr>
		
		<tr>
			<td><?php echo $frm->submit("ACEPTAR")?></td>
		</tr>
	
	</table>
	
</div>
<?php echo $frm->end()?>

 	
<?php if(isset($showTabla) && $showTabla == 1):


?>

<div class="areaDatoForm">

	<p>LISTADO CONSOLIDADO
	
	<?php 
		echo $controles->botonGenerico('/clientes/cliente_listados/saldo_a_fecha_xls/' . $fecha_desde . '/' . $fecha_hasta,'controles/ms_excel.png', null, array('id' => 'xls'));
		echo $controles->botonGenerico('/clientes/cliente_listados/saldo_a_fecha_pdf/' . $fecha_desde . '/' . $fecha_hasta,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
	?></p>
	
	<p>LISTADO DETALLADO
	
	<?php 
		echo $controles->botonGenerico('/clientes/cliente_listados/saldo_a_fecha_detalle_xls/' . $fecha_desde . '/' . $fecha_hasta,'controles/ms_excel.png', null, array('id' => 'xls'));
		echo $controles->botonGenerico('/clientes/cliente_listados/saldo_a_fecha_detalle_pdf/' . $fecha_desde . '/' . $fecha_hasta,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
	?></p>
	
<table align="center" width="100%">

<!--	<col width="100" />-->
<!--	<col width="250" />-->
<!--	<col width="250" />-->
<!--	<col width="100" />-->
<!--	<col width="100" />-->
<!--	<col width="100" />-->
		
	<tr border="0">
		<th style="font-size: small;">CUIT-CUIL</th>
		<th style="font-size: small;">RAZON SOCIAL</th>
		<th style="font-size: small;">CONDICION IVA</th>
		<th style="font-size: small;">SALDO <?php echo date('d/m/Y', strtotime($fecha_saldo_anterior))?></th>
		<th style="font-size: small;">DEBITOS</th>
		<th style="font-size: small;">N.CREDITOS</th>
		<th style="font-size: small;">COBROS</th>
		<th style="font-size: small;">SALDO PERIODO</th>
		<th style="font-size: small;">SALDO <?php echo date('d/m/Y', strtotime($fecha_hasta))?></th>
		<th style="font-size: small;">INF.DETALLADO</th>
	</tr>

	<?php
	foreach ($saldos as $saldo):
	?>
		<tr>
			<td style="font-size: x-small;"><?php echo $saldo['Cliente']['cuit']?></td>
			<td style="font-size: x-small;"><?php echo ($saldo['Cliente']['razon_social_resumida'] == "" ? $saldo['Cliente']['razon_social'] : $saldo['Cliente']['razon_social_resumida'])?></td>
			<td style="font-size: x-small;"><?php echo $saldo['GlobalDato']['concepto_1']?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($saldo['0']['saldo_anterior'],2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($saldo['0']['debito'],2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($saldo['0']['credito'],2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($saldo['0']['cobro'],2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($saldo['0']['saldo'],2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($saldo['0']['saldo_actual'],2)?></td>
			<td align="center" style="font-size: small;">
				<?php 
				echo $controles->botonGenerico('/clientes/cliente_listados/cta_cte_fecha/' . $saldo['Cliente']['id'] . '/' . $fecha_desde . '/' . $fecha_hasta . '/' . $saldo['0']['saldo_anterior'],'controles/HTML-globe.png', null, array('id' => 'html'));
				echo $controles->botonGenerico('/clientes/cliente_listados/cta_cte_fecha_xls/' . $saldo['Cliente']['id'] . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/ms_excel.png', null, array('id' => 'xls'));
				echo $controles->botonGenerico('/clientes/cliente_listados/cta_cte_fecha_pdf/' . $saldo['Cliente']['id'] . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
				?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>
	
	<p>LISTADO CONSOLIDADO
	
	<?php 
		echo $controles->botonGenerico('/clientes/cliente_listados/saldo_a_fecha_xls/' . $fecha_desde . '/' . $fecha_hasta,'controles/ms_excel.png', null, array('id' => 'xls'));
		echo $controles->botonGenerico('/clientes/cliente_listados/saldo_a_fecha_pdf/' . $fecha_desde . '/' . $fecha_hasta,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
	?></p>
	
	<p>LISTADO DETALLADO
	
	<?php 
		echo $controles->botonGenerico('/clientes/cliente_listados/saldo_a_fecha_detalle_xls/' . $fecha_desde . '/' . $fecha_hasta,'controles/ms_excel.png', null, array('id' => 'xls'));
		echo $controles->botonGenerico('/clientes/cliente_listados/saldo_a_fecha_detalle_pdf/' . $fecha_desde . '/' . $fecha_hasta,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
	?></p>
	
	
</div>
<?php endif;?>
 	