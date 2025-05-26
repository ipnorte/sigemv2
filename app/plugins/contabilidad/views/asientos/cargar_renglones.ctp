<?php 
if(!$Ok){
	echo $this->renderElement('msg',array('msg' => array('ERROR' => $msgError)));
}

$diferencia = $total['debe'] - $total['haber'];
if($diferencia < 0) $diferencia *= -1;

if($diferencia <> 0) echo $this->renderElement('msg',array('msg' => array('ERROR' => 'DIFERENCIA: ' . $util->nf($diferencia))));
else  echo $this->renderElement('msg',array('msg' => array('OK' => 'DIFERENCIA: ' . $util->nf($diferencia))));

if(count($renglones) != 0):?>

	<table>
	
		<tr>
			<th></th>
			<th>CUENTA</th>
			<th>REFERENCIA</th>
			<th>DEBE</th>
			<th>HABER</th>
		</tr>
		
		<?php foreach($renglones as $key => $renglon):?>
		
			<tr>
				<td style="background-color: #FFFFFF;"><?php echo $controles->linkAjax($html->image('controles/12-em-cross.png'),'/contabilidad/asientos/cargar_renglones_remover/'.$key.'/'.$uuid,'grilla_renglones',null,'Quitar este Renglon?')?></td>
				<td style="background-color: #FFFFFF;"><?php echo $renglon['Asiento']['cuenta_seleccionada']?></td>
				<td style="background-color: #FFFFFF;"><?php echo $renglon['Asiento']['referencia_renglon']?></td>
				<td style="text-align: right;background-color: #FFFFFF;"><?php echo ( $renglon['Asiento']['tipo'] == 'D' ? $util->nf($renglon['Asiento']['importe']) : "")?></td>
				<td style="text-align: right;background-color: #FFFFFF;"><?php echo ( $renglon['Asiento']['tipo'] == 'H' ? $util->nf($renglon['Asiento']['importe']) : "")?></td>
			</tr>
		
		<?php endforeach;?>

		<tr class='totales'>
			<td colspan="3">TOTAL ASIENTO</td>
			<td align="right"><?php echo $util->nf($total['debe'])?></td>
			<td align="right"><?php echo $util->nf($total['haber'])?></td>
		</tr>
	</table>
	<input type='hidden' id='total_debe'  value='<?php echo $total['debe']?>'/>
	<input type='hidden' id='total_haber' value='<?php echo $total['haber']?>'/>
	<?php echo $frm->hidden("Asiento.uuid", array('value' => $uuid)) ?>
	<?php //   echo $frm->hidden('Asiento.renglonesSerialize', array('value' => base64_encode(serialize($renglones))))?>
<?php endif;?>	

