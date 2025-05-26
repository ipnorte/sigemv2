<h3>INFORMACION DE ERRORES DEL PROCESO [#<?php echo $job['Asincrono']['id']?>]</h3>
<div class="areaDatoForm2">

	NOMBRE: <strong><?php echo $job['Asincrono']['titulo']?></strong>
	<?php if(!empty($job['Asincrono']['subtitulo'])):?>
	&nbsp;-&nbsp;<?php echo $job['Asincrono']['subtitulo']?>
	<?php endif;?>
	<br/>
	USUARIO: <strong><?php echo $job['Asincrono']['propietario']?></strong>
	&nbsp;|&nbsp;TERMINAL: <strong><?php echo $job['Asincrono']['remote_ip']?></strong>
	<br/>
	CREADO: <strong><?php echo $job['Asincrono']['created']?></strong>	
	&nbsp;|&nbsp;FINALIZADO: <strong><?php echo $job['Asincrono']['final']?></strong>	
</div>
<h4>DETALLE DE ERRORES</h4>
<?php if(!empty($errores)):?>

	<table>
		<tr>
			<th>ID</th>
			<th>REG</th>
			<th>MENSAJE 1</th>
			<th>MENSAJE 2</th>
			<th>MENSAJE 3</th>
			<th>MENSAJE 4</th>
		
		</tr>
		<?php $n = 1;?>
		<?php foreach($errores as $error):?>
			<tr>
				<td><?php echo $error['AsincronoError']['id']?></td>
				<td align="center"><?php echo $n?></td>
				<td><?php echo $error['AsincronoError']['mensaje_1']?></td>
				<td><?php echo $error['AsincronoError']['mensaje_2']?></td>
				<td><?php echo $error['AsincronoError']['mensaje_3']?></td>
				<td><?php echo $error['AsincronoError']['mensaje_4']?></td>
			
			</tr>
			<?php $n++;?>
		<?php endforeach;?>
	</table>

<?php //   debug($errores)?>

<?php endif;?>