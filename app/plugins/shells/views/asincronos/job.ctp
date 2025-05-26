<h3>INFORMACION DE PROCESO [#<?php echo $job['Asincrono']['id']?>]</h3>
<table>

	<tr>
		<th>CREADO EL</th><td><?php echo $job['Asincrono']['created']?></td>
	</tr>
	<tr>
		<th>USUARIO</th><td><?php echo $job['Asincrono']['propietario']?></td>
	</tr>
	<tr>
		<th>TERMINAL</th><td><?php echo $job['Asincrono']['remote_ip']?></td>
	</tr>
	<tr>
		<th>NOMBRE</th>
		<td>
			<?php echo $job['Asincrono']['titulo']?>
			<?php if(!empty($job['Asincrono']['subtitulo'])):?>
				<br/>
				<?php echo $job['Asincrono']['subtitulo']?>
			<?php endif;?>
		</td>
	</tr>
	<?php if(!empty($job['AsincronoError'])):?>
	<tr>
		<th>ERRORES</th><td style="background-color: red; color: white;font-size: 12px; font-weight: bold;"><span style="font-size: 14px;"><?php echo count($job['AsincronoError'])?></span> - <?php echo $html->link('VER ERRORES','errores/'.$job['Asincrono']['id'],array('style' => 'color:white;'))?></td>
	</tr>
	<?php endif;?>
	<tr>
		<th>ESTADO</th><td><h5><?php echo "<strong style='color:red;'>[ ".$job['Asincrono']['porcentaje']."% ]</strong> " . utf8_encode($job['Asincrono']['msg'])?></h5></td>
	</tr>
	<?php if($job['Asincrono']['estado'] == 'F'):?>		
	<tr>
		<th>ACCION</th><td><strong><?php echo $html->link("CONSULTAR",$job['Asincrono']['action_do'])?><strong></td>
	</tr>
	<?php endif;?>		

</table>

<?php 

//debug($job)

?>

