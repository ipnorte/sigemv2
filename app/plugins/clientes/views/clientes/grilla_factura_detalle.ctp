	
	
	
<?php 
//debug($uuid);
//exit;

if(!$Ok){
	echo $this->renderElement('msg',array('msg' => array('ERROR' => $msgError)));
}


if(count($factura_detalle) != 0):?>

	<table width="100%">
	
		<tr>
			<th>COD.PROD.</th>
			<th>DESCRIPCION</th>
			<th>CTA.CONTABLE</th>
			<th>CANTIDAD</th>
			<th>P.UNITARIO</th>
			<th>TOTAL</th>
			<th></th>
		</tr>
		
		<?php 
			$nTotal = 0;
			foreach($factura_detalle as $key => $renglon):
				$nTotalRenglon =  $renglon['ClienteFacturaDetalle']['cantidad'] * $renglon['ClienteFacturaDetalle']['importe_unitario'];
				$nTotal += $nTotalRenglon;
		?>	
			<tr>
				<td style="font-size: x-small;background-color: #FFFFFF;"><?php echo $renglon['ClienteFacturaDetalle']['codigo_producto']?></td>
				<td style="font-size: x-small;background-color: #FFFFFF;"><?php echo $renglon['ClienteFacturaDetalle']['descripcion_producto']?></td>
				<td style="font-size: x-small;background-color: #FFFFFF;"><?php echo $renglon['ClienteFacturaDetalle']['descripcion_cuenta']?></td>
				<td style="font-size: x-small;text-align: right;background-color: #FFFFFF;"><?php echo $renglon['ClienteFacturaDetalle']['cantidad']?></td>
				<td style="font-size: x-small;text-align: right;background-color: #FFFFFF;"><?php echo $util->nf($renglon['ClienteFacturaDetalle']['importe_unitario'])?></td>
				<td style="font-size: x-small;text-align: right;background-color: #FFFFFF;"><?php echo $util->nf($nTotalRenglon)?></td>
				<td style="background-color: #FFFFFF;"><?php echo $controles->linkAjaxN($html->image('controles/12-em-cross.png'),'/clientes/clientes/remover_factura_detalle/'.$key . '/' . $uuid,'grilla_factura_detalle',null,'Quitar este Renglon?',null,"disparo_funcion($total_factura - $nTotalRenglon);")?></td>
			</tr>
		
		<?php endforeach;?>

		<tr class='totales'>
			<td style="border-top: 1px solid black;font-size: small;" align="right" colspan="5">TOTAL FACTURADO</td>
			<td style="border-top: 1px solid black;font-size: small;" align="right"><?php echo $util->nf($nTotal)?></td>
			<td style="border-top: 1px solid black;font-size: small;"></td>
		</tr>
	</table>
	<?php echo $frm->hidden('ClienteFacturaDetalle.renglonesSerialize', array('value' => base64_encode(serialize($factura_detalle))))?>
	<?php echo $frm->hidden('ClienteFactura.total_factura', array('value' => $total_factura))?>
	<?php echo $frm->hidden("ClienteFacturaDetalle.uuid", array('value' => $uuid)) ?>
<?php endif;?>	

