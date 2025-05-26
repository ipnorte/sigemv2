<?php echo $this->renderElement('head',array('title' => 'INFORMES E IMPRESION FACTURA AFIP','plugin' => 'config'))?>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('form_informe_impresion').disable();
	<?php endif;?>
});
</script>
<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action' => 'facturacion_informe','id' => 'form_informe_impresion'))?>
	<table class="tbl_form">
		<tr>
			<td>DESDE FECHA</td><td><?php echo $frm->calendar('Factura.fecha_desde','',$fecha_desde,'1990',date("Y"))?></td>
		</tr>
		<tr>
			<td>HASTA FECHA</td><td><?php echo $frm->calendar('Factura.fecha_hasta','',$fecha_hasta,'1990',date("Y"))?></td>
		</tr>
		<tr><td colspan="2"><?php echo $frm->submit("ACEPTAR")?></td></tr>
	</table>
	<?php echo $frm->end()?>
</div>
<?php if(isset($aFacturaInforme)): ?>

<div class="areaDatoForm">
	
	
<table align="center" width="100%">

	<col width="40" />
	<col width="150" />
	<col width="400" />
	<col width="60" />
	<col width="60" />
	<col width="60" />
	<col width="60" />
	<col width="60" />
	<col width="60" />
	<col width="60" />
	<tr>
		<td colspan="8"></td>
		<td colspan="2"><?php echo $controles->botonGenerico('reporte_xls/'.$fechas,'controles/ms_excel.png','EXPORTAR',array('target' => 'blank'))?></td>
	</tr>	
	<tr>
		<th style="font-size: small;">FECHA</th>
		<th style="font-size: small;">COMPROBANTE</th>
		<th style="font-size: small;">CONCEPTO</th>
		<th style="font-size: small;">CUIT</th>
		<th style="font-size: small;">IMP. NO GRAVADO</th>
		<th style="font-size: small;">IMP. GRAVADO</th>
		<th style="font-size: small;">IMP. IVA</th>
		<th style="font-size: small;">TOTAL</th>
        <th style="font-size: small;">CODIGO ERROR</th>
        <th style="font-size: small;"></th>
	</tr>

	<?php

        foreach ($aFacturaInforme as $renglon):
	?>
		<tr>
                    <td style="font-size: x-small;"><?php echo date('d/m/Y',strtotime($renglon['Factura']['fecha_comprobante']))?></td>

                    <?php if($renglon['Factura']['e_codigo'] > 0){ ?>
                    <td></td>
                    <?php } else { ?>
                    <td style="font-size: x-small;"><?php echo $renglon['Factura']['comprobante']?></td>
                    <?php } ?>

                    <td style="font-size: x-small;">
                    <?php // echo $renglon['Factura']['nom_apel']?>
                    <?php echo $controles->openWindow($renglon['Factura']['nom_apel'],'/pfyj/personas/view/'.$renglon['Factura']['persona_id'])?>
                    </td>
                    <td style="font-size: x-small;"><?php echo $renglon['Factura']['numero_documento']?></td>
                    <td align="right" style="font-size: small;"><?php echo number_format($renglon['Factura']['importe_total_concepto'],2)?></td>
                    <td align="right" style="font-size: small;"><?php echo number_format($renglon['Factura']['importe_neto'],2)?></td>
                    <td align="right" style="font-size: small;"><?php echo number_format($renglon['Factura']['importe_iva'],2)?></td>
                    <td align="right" style="font-size: small;"><?php echo number_format($renglon['Factura']['importe_total'],2)?></td>
                    <td align="center"><strong><?php echo ($renglon['Factura']['e_codigo'] > 0 ? $controles->linkModalBox('# '.$renglon['Factura']['e_codigo'],array('title' => 'ERROR CODIGO AFIP: ' . $renglon['Factura']['e_codigo'],'url' => '/facturacion/facturaciones/ver_error_afip/' . $renglon['Factura']['id'],'h' => 450, 'w' => 750)) :  '')?></strong></td>
                    <?php 
                    if($renglon['Factura']['e_codigo'] > 0){ ?>
                    <td></td>
                    <?php }else {?>
                    <td><?php echo $controles->botonGenerico('imprimir_factura_afip/'. $renglon['Factura']['id'],'controles/printer.png',null, array('target' => 'blank'));?></td>
                    <?php } ?>
                </tr>
	<?php endforeach; ?>
</table>
	

	
</div>

<?php 
   // debug($aFacturaInforme)
?>

<?php endif; ?>