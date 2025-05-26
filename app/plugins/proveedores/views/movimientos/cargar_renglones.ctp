<?php 

// debug($this->data);
// debug($this->params);
// debug($renglones);

$vOk = 1;
if(!$Ok){
	$vOk = 0;
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
			<th>FORMA DE PAGO</th>
			<th>IMPORTE</th>
			<th>NRO.CUENTA</th>
			<th>NRO.CH./OP.</th>
			<th>F.PAGO</th>
			<th>F.VENCIMIENTO</th>
		</tr>
		
		<?php foreach($renglones as $key => $renglon):
			$idCheque = 0;
//			$fecha_pago = $renglon['Movimiento']['fecha_pago']['year'] . $renglon['Movimiento']['fecha_pago']['month'] . $renglon['Movimiento']['fecha_pago']['day'];
			$fecha_pago = $renglon['Movimiento']['fpago']['year'] . $renglon['Movimiento']['fpago']['month'] . $renglon['Movimiento']['fpago']['day'];
			$fecha_vencimiento = $renglon['Movimiento']['fvenc']['year'] . $renglon['Movimiento']['fvenc']['month'] . $renglon['Movimiento']['fvenc']['day'];
			
			if(isset($renglon['Movimiento']['banco_cheque_tercero_id'])) $idCheque = $renglon['Movimiento']['banco_cheque_tercero_id'];
		?>
			
			<tr>
				<td style="background-color: #FFFFFF;"><?php echo $controles->linkAjaxN($html->image('controles/12-em-cross.png'),'/proveedores/movimientos/cargar_renglones_remover/'.$key . '/'. $uuid,'grilla_pagos',null,'Quitar este Renglon?',null,"actualizaImporte(".$renglon['Movimiento']['importe_efectivo'].",".$idCheque.");")?></td>
				<td style="background-color: #FFFFFF;"><?php echo $renglon['Movimiento']['tipo_pago_desc']?></td>
				<td style="text-align: right;background-color: #FFFFFF;"><?php echo $util->nf($renglon['Movimiento']['importe_efectivo'])?></td>
				<td style="background-color: #FFFFFF;"><?php echo $renglon['Movimiento']['denominacion']?></td>
				<td style="background-color: #FFFFFF;"><?php echo $renglon['Movimiento']['numero_operacion']?></td>
				<td style="text-align: center;background-color: #FFFFFF;"><?php echo $util->armaFecha($fecha_pago)?></td>
				<td style="text-align: center;background-color: #FFFFFF;"><?php echo $util->armaFecha($fecha_vencimiento)?></td>
			</tr>
		
		<?php endforeach;?>

		<tr class='totales'>
			<th colspan="2" align="right">TOTAL</th>
			<th align="right"><?php echo $util->nf($acumulado)?></th>
			<th colspan="4"></th>
		</tr>
	</table>
	<input type='hidden' id='acumulado' value='<?php echo $acumulado?>'/>
	<input type='hidden' id='ok' value='<?php echo $vOk?>'/>
	<?php //   echo $frm->hidden('Movimiento.renglonesSerialize', array('value' => base64_encode(serialize($renglones))))?>
	<?php echo $frm->hidden("Movimiento.uuid", array('value' => $uuid)) ?>
<?php endif;?>	

