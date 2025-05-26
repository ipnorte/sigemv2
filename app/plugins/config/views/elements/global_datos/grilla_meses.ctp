<?php 
$meses = array('01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL', '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO', '09' => 'SEPTIEMBRE', '10' => 'OCTUBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE');
?>
<?php echo $frm->checkAll(count($meses),$modelo."Mes_")?>

<table>

	<tr>
	
		<th></th>
		<th>MES</th>
		
	</tr>
	
	<?php 
		$i = 0;
		foreach($meses as $n => $mes):
			$i++;
	?>
	
		<tr>
			<td><input type="checkbox" name="data[<?php echo $modelo?>][mes][<?php echo $n?>]" value="1" id="<?php echo $modelo?>Mes_<?php echo $i?>" <?php echo (isset($selected) ? (in_array($n,$selected) ? 'checked="checked"':'') : '' ) ?> /></td>
			<td><?php echo $mes?></td>
		</tr>
	
	<?php endforeach;?>

</table>