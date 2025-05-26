<?php echo $this->renderElement('head',array('title' => 'ADMINISTRADOR DE PROCESOS BACKGROUND','plugin' => 'config'))?>

<?php echo $controles->botonGenerico('/shells/asincronos/jobs','controles/reload3.png','ACTUALIZAR')?>

<?php echo $this->renderElement('paginado')?>

<table>
<tr>
	<th>PID</th>
	<th><?php echo $paginator->sort('PROPIETARIO','propietario');?></th>
	<th><?php echo $paginator->sort('CREADO','created');?></th>
	<th><?php echo $paginator->sort('ULTIMO CAMBIO','modified');?></th>
<!--	<th>HORAS</th>-->
<!--	<th><?php echo $paginator->sort('FINALIZADO','final');?></th>-->
	<th><?php echo $paginator->sort('PROCESO','proceso');?></th>
	<th><?php echo $paginator->sort('TITULO','titulo');?></th>
	<th><?php echo $paginator->sort('SUBTITULO','subtitulo');?></th>
	<th><?php echo $paginator->sort('MENSAJE','msg');?></th>
	<th><?php echo $paginator->sort('STATUS','estado');?></th>
	<th><?php echo $paginator->sort('TOTAL','total');?></th>
	<th><?php echo $paginator->sort('ACTUAL','contador');?></th>
	<th><?php echo $paginator->sort('%','porcentaje');?></th>
	<th><?php echo $paginator->sort('IP','remote_ip');?></th>
	<th></th>
        <th></th>
	<th class="actions"></th>
</tr>
<?php
$i = 0;
foreach ($jobs as $job):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
	$creado = $job['Asincrono']['created'];
	$modified = $job['Asincrono']['modified'];
	$mktimeIni = mktime(date('H',strtotime($creado)),date('i',strtotime($creado)),date('s',strtotime($creado)),date('m',strtotime($creado)),date('d',strtotime($creado)),date('Y',strtotime($creado)));
	$mktimeMod = mktime(date('H',strtotime($modified)),date('i',strtotime($modified)),date('s',strtotime($modified)),date('m',strtotime($modified)),date('d',strtotime($modified)),date('Y',strtotime($modified)));	

	$ejecutado = ($mktimeMod - $mktimeIni) / 3600;
	
	$STATUS_DESC = "";
	switch ($job['Asincrono']['estado']) {
		case "C":
			$STATUS_DESC = "CREADO";
			break;
		case "P":
			$STATUS_DESC = "PROCESANDO";
			break;
		case "S":
			$STATUS_DESC = "DETENIDO";
			break;
		case "E":
			$STATUS_DESC = "ERROR";
			break;
		case "F":
			$STATUS_DESC = "FINALIZADO";
			break;											
		default:
			$STATUS_DESC = "NN";
			break;
	}
	
	
	
?>
	<tr class="<?php echo ($job['Asincrono']['estado'] == "F" ? "verde" : ""); ?>">
	
		<td align="center"><?php echo $job['Asincrono']['id']; ?></td>
		<td align="center"><?php echo $job['Asincrono']['propietario']; ?></td>
		<td><?php echo $job['Asincrono']['created']; ?></td>
		<td><?php echo $job['Asincrono']['modified']; ?></td>
<!--		<td align="center"><?php echo number_format($ejecutado,4); ?></td>-->
<!--		<td><?php echo $job['Asincrono']['final']; ?></td>-->
		<td><?php echo $job['Asincrono']['proceso']; ?></td>
		<td><?php echo $job['Asincrono']['titulo']; ?></td>
		<td><?php echo $job['Asincrono']['subtitulo']; ?></td>
		<td><?php echo $job['Asincrono']['msg']; ?></td>
		<td align="center"><?php echo $STATUS_DESC; ?></td>
		<td align="center"><?php echo $job['Asincrono']['total']; ?></td>
		<td align="center"><?php echo $job['Asincrono']['contador']; ?></td>
		<td align="center" style="font-weight: bold;"><?php echo ($job['Asincrono']['porcentaje'] == 100 ? "<span style='color:green;'>".$job['Asincrono']['porcentaje']."</span>" : "<span style='color:red;'>".$job['Asincrono']['porcentaje']."</span>"); ?></td>
		<td align="center"><?php echo $job['Asincrono']['remote_ip']; ?></td>
		<td align="center"><?php if($job['Asincrono']['estado'] == "F") echo $html->link($job['Asincrono']['btn_label'],$job['Asincrono']['action_do'].'/?pid='.$job['Asincrono']['id'],array('target' => '_blank')); ?></td>
                <td>
                    <?php if(!empty($job['Asincrono']['shell_status']) && $job['Asincrono']['shell_status']['run'] == 0):?>
                    <p style="color: red;">INICIALIZAR</p>
                    <?php endif;?>
                </td>
		<td>
		<?php 
		
		
		if($job['Asincrono']['estado'] == 'C' || $job['Asincrono']['estado'] == 'S')echo $html->link('Eliminar', array('action'=>'delete_job', $job['Asincrono']['id']), null, sprintf(__('Eliminar el Proceso PID#%s?', true), $job['Asincrono']['id'])); 
		if($job['Asincrono']['estado'] == 'P')echo $html->link('Detener', array('action'=>'stop_job', $job['Asincrono']['id']), null, sprintf(__('Detener el Proceso PID#%s?', true), $job['Asincrono']['id'])); 
		
		
		?>
		</td>
	
	</tr>

<?php endforeach;?>


</table>

<?php // debug($jobs) ?>

