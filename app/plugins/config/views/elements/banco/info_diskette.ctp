<?php 

$diskette = null;
$uuid = (isset($uuid) ? $uuid : null);

$listadoControlURL = (isset($listado) ? $listado : null);
$toPDF = (isset($toPDF) ? $toPDF: true);
$toXLS = (isset($toXLS) ? $toXLS: false);
if(isset($_SESSION["DISKETTE_$uuid"])){
	$diskette = $_SESSION["DISKETTE_$uuid"];
// 	$diskette = base64_decode($diskette);
	$diskette = unserialize($diskette);
	$diskette = $diskette['diskette'];
}
?>

<h3><?php echo (isset($title) ? $title : "DISKETTE")?></h3>

<?php if(!empty($diskette)):?>

	<table>
		<tr><th style="text-align: right;">BANCO</th><td><h5><?php echo $diskette['banco_intercambio_nombre']?></h5></td></tr>
		<tr><th style="text-align: right;">A DEBITAR EL</th><td><h5><?php echo $diskette['fecha_debito']?></h5></td></tr>
		<tr><th style="text-align: right;">REGISTROS</th><td style="color: <?php echo($diskette['status'] == 'OK' ? "green" : "red")?>;"><h5><?php echo $diskette['cantidad_registros']?></h5></td></tr>
		<tr><th style="text-align: right;">IMPORTE</th><td style="color: <?php echo($diskette['status'] == 'OK' ? "green" : "red")?>;"><h5><?php echo $util->nf($diskette['importe_debito'])?></h5></td></tr>
		<tr><th style="text-align: right;">ESTRUCTURA</th><td style="color: <?php echo($diskette['status'] == 'OK' ? "green" : "red")?>;"><h5><?php echo ($diskette['status'] == 'OK' ? $diskette['status'] : $diskette['status'].": ".$diskette['observaciones'])?></h5></td></tr>
		<?php if($diskette['status'] == 'OK'):?>
		<tr>
			<th style="text-align: right;">ARCHIVO</th>
			<td>
				<?php if(!empty($diskette['lote'])):?>
					<strong><?php echo $controles->botonGenerico('/config/bancos/download_diskette/' . $uuid,'controles/disk.png',$diskette['archivo'],array('target' => '_blank'))?></strong>
					<?php if(!empty($listado)):?>
						<?php if($toPDF):?>
							&nbsp;|&nbsp;
							<?php echo $controles->botonGenerico($listadoControlURL . '/' . $uuid.'/PDF','controles/pdf.png','LISTADO DE CONTROL',array('target' => '_blank'))?>
						<?php endif;?>
						<?php if($toXLS):?>
							&nbsp;|&nbsp;
							<?php echo $controles->botonGenerico($listadoControlURL . '/' . $uuid.'/XLS','controles/ms_excel.png','LISTADO DE CONTROL',array('target' => '_blank'))?>
						<?php endif;?>
					<?php endif;?>
				<?php else:?>
					<h5><span style="color: red;">*** DISKETTE VACIO ***</span></h5>
				<?php endif;?>
			</td>
		</tr>
		<?php endif;?>
		<tr>
			<th valign="top" style="text-align: right;">VISTA PREVIA DEL LOTE</th>
			<td><textarea rows="10" cols="120" readonly="readonly"><?php echo $diskette['lote']?></textarea></td>
		</tr>
	</table>
<?php else:?>

	<div class="notices_error">*** ERROR AL GENERAR EL DISKETTE ****</div>

<?php endif;?>