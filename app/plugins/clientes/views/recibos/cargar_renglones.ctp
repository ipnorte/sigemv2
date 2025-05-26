<?php 
if(!$Ok){
	echo $this->renderElement('msg',array('msg' => array('ERROR' => $msgError)));
}
//
//$diferencia = $total['debe'] - $total['haber'];
//if($diferencia < 0) $diferencia *= -1;
//
//if($diferencia <> 0) echo $this->renderElement('msg',array('msg' => array('ERROR' => 'DIFERENCIA: ' . $util->nf($diferencia))));
//else  echo $this->renderElement('msg',array('msg' => array('OK' => 'DIFERENCIA: ' . $util->nf($diferencia))));

if(count($renglones) != 0):?>
	<table>
	
		<tr>
			<th></th>
			<th>FORMA DE COBRO</th>
			<th>IMPORTE</th>
			<th>CTA.BANCARIA/BANCO</th>
			<th>PLAZA</th>
			<th>NRO.CH./OPERACION</th>
			<th>FECHA</th>
			<th>F.VENCIMIENTO</th>
			<th>LIBRADOR</th>
		</tr>
		
		<?php foreach($renglones as $key => $renglon):
			$fecha_cobro = $renglon[$model]['fcobro']['year'] . $renglon[$model]['fcobro']['month'] . $renglon[$model]['fcobro']['day'];
			$fecha_vencimiento = '';
			$fecha_vencimiento = ($renglon[$model]['forma_cobro'] == 'CT' ? $renglon[$model]['fvenc']['year'] . $renglon[$model]['fvenc']['month'] . $renglon[$model]['fvenc']['day'] : '');
			?>
			
			<tr>
				<td style="background-color: #FFFFFF;"><?php echo $controles->linkAjaxN($html->image('controles/12-em-cross.png'),'/clientes/recibos/cargar_renglones_remover/'.$key.'/'.$model,'grilla_cobros',null,'Quitar este Renglon?',null,"actualizaImporte(".$renglon[$model]['importe'].");")?></td>
				<td style="background-color: #FFFFFF;"><?php echo $renglon[$model]['forma_cobro_desc']?></td>
				<td style="text-align: right;background-color: #FFFFFF;"><?php echo $util->nf($renglon[$model]['importe'])?></td>
				<td style="background-color: #FFFFFF;"><?php echo $renglon[$model]['denominacion']?></td>
				<td style="background-color: #FFFFFF;"><?php echo $renglon[$model]['plaza']?></td>
				<td style="background-color: #FFFFFF;"><?php echo $renglon[$model]['numero_operacion']?></td>
				<td style="background-color: #FFFFFF;"><?php echo $util->armaFecha($fecha_cobro)?></td>
				<td style="background-color: #FFFFFF;"><?php echo $util->armaFecha($fecha_vencimiento)?></td>
				<td style="background-color: #FFFFFF;"><?php echo $renglon[$model]['librador']?></td>
			</tr>
		
		<?php endforeach;?>

		<tr class='totales'>
			<td colspan="2" align="right">TOTAL</td>
			<td align="right"><?php echo $util->nf($acumulado)?></td>
		</tr>
	</table>
	<input type='hidden' id='acumulado' value='<?php echo $acumulado?>'/>
	<?php echo $frm->hidden('Recibo.renglonesSerialize', array('value' => base64_encode(serialize($renglones))))?>
<?php endif;?>	

