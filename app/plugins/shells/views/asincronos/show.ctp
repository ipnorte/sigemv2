<?php $UID = intval(mt_rand());?>

<style>
/** barra de avance **/

.botonera {
	empty-cells: show;
	border: 1px solid #e2e6ea;
	margin-bottom: 5px;
}

#pb_container_<?php echo $UID?>{
	width:98%;
	/*margin: 5px;*/
	padding: 5px;
	border:1px solid #e2e6ea;
	overflow: hidden;
	background-color: #F5F7F7;
}

#pb_container_<?php echo $UID?> td{padding:3px;background-color: #F5F7F7;}

.titulo{
	font-family: Arial;
	font-weight: bold;
	font-size: 110%;
	margin: 0px;
	padding: 0px;
}

.subtitulo{
	font-family: Arial;
	font-size: 90%;
	margin: 0px;
	padding: 0px;
}

#pb_container_<?php echo $UID?> .barra_container{
	width:400px;
	padding:5px;
	margin:5px;
}

#pb_container_<?php echo $UID?> .barra{
	width:100%;
	height:25px;
	background-color: #e2e6ea;
	border:1px solid #e2e6ea;
	overflow: hidden;		
}


#pb_container_<?php echo $UID?> .controles{
	width:50%;
	float:left;
	padding:3px;
}

#response_status_<?php echo $UID?>{
	padding:1px;
	font-size: 90%;	
	height:40px;
	overflow: hidden;
}

#btn_accion_<?php echo $UID?>{
	padding: 5px;
	border: 1px solid #e2e6ea;
	background: #e2e6ea;
	color: #CC0000;
	font-weight: normal;
}
#btn_accion_<?php echo $UID?> a{
	text-decoration: none;
	color: #CC0000;
	font-weight: normal;	
}

#btn_accion_<?php echo $UID?> a:hover{
	text-decoration: none;
	color: #CC0000;
	font-weight: normal;	
}

</style>
<?php if(empty($otros)):?>
	<?php
	
	
	$frecuencia = $refreshPogressBar;
	
	$START	= $ajax->remoteFunction(array('url' => array( 'controller' => 'asincronos', 'action' => 'start', $pid ))); 
	$STOP 	= $ajax->remoteFunction(array('url' => array( 'controller' => 'asincronos', 'action' => 'stop', $pid )));
	
	echo $javascript->event('window','load',"
		$('btn_stop_$UID').disable();
		$('btn_accion_$UID').hide();
		Event.observe('btn_start_$UID', 'click', function(event) {
		
			".(isset($remote_call_start) && !empty($remote_call_start) ? $remote_call_start.";" : "")."
		
			$('btn_start_$UID').disable();$('btn_stop_$UID').enable();$('btn_accion_$UID').hide();
			$START
			
			$('progress_bar_$UID').update('');
			
			new PeriodicalExecuter(function(p) {
	
				new Ajax.Request('".$pathToResponser."?PID=".$pid."&ACCION=CHECK_STATUS', 
					{asynchronous:true, evalScripts:true, 
					onComplete:function(request, json) 
						{
							var estatus = request.responseText;
							if(estatus == 'S' || estatus == 'E') {
								p.stop();
								$('btn_stop_$UID').disable();
								$('btn_start_$UID').enable();
							}
							if(estatus == 'F'){
								p.stop();
								$('btn_stop_$UID').disable();
								$('btn_start_$UID').enable();
								$('btn_accion_$UID').show();
							}
						}
					}	
				)
			
				new Ajax.Updater(	'progress_bar_$UID',
									'".$pathToResponser."?PID=".$pid."&ACCION=PROGRESS_BAR', 
									{asynchronous:true, evalScripts:true, requestHeaders:['X-Update', 'progress_bar']}
								);
				new Ajax.Updater(	'response_status_$UID',
									'".$pathToResponser."?PID=".$pid."&ACCION=STATUS', 
									{asynchronous:true, evalScripts:true, requestHeaders:['X-Update', 'response_status']}
								);
	
				}, $frecuencia);		
			
		}, false);
		Event.observe('btn_stop_$UID', 'click', function(event) {
		
			". (isset($remote_call_stop) && !empty($remote_call_stop) ? $remote_call_stop.";" : "")."
		
			$STOP
			$('btn_stop_$UID').disable();
			$('btn_start_$UID').enable();
			$('btn_accion_$UID').hide();
		}, false);
	
	"); 
		
		
	?>
	
	<table id='pb_container_<?php echo $UID?>'>
	
	  	<tr>
	   		<td colspan="2">
	   			<div class="titulo">#<?php echo $pid?>&nbsp;|&nbsp;<?php echo $titulo?></div>
	   			<div class="subtitulo"><?php echo $subtitulo?></div>
	   		</td>
		</tr>
	  <tr>
	    <td class='barra_container'>
			<div class='barra' id="progress_bar_<?php echo $UID?>">
				<div style='float:left;width:0%;height:25px;background-color: #003366'></div>		
			</div>
		</td>
		
	    <td valign="top">
	    	<div id='response_status_<?php echo $UID?>'></div>
	    </td>
	  </tr>
	  <tr>
	   	<td colspan="2">
	    	<table class="botonera">
	    		<tr>
					<td>
						<input type="button" id="btn_start_<?php echo $UID?>" name="btn_start_<?php echo $UID?>" value="COMENZAR" >
					</td>
					<td>
						<input type="button" id="btn_stop_<?php echo $UID?>" value="DETENER">
					</td>
					<td>
						<div id="btn_accion_<?php echo $UID?>">
							<?php echo $html->link((isset($btn_label) ? $btn_label : 'IMPRIMIR'),$action_do.'/?pid='.$pid,array('target'=>$target)) ?>
						</div>
					</td>				
	    		
	    		</tr>
	    	</table>
		</td>
		
	  </tr>
	  
	</table>
<?php else:?>
	<h3>#<?php echo $pid?>&nbsp;|&nbsp;<?php echo $titulo?></h3>
	<div class="notices_error"><strong>ATENCION!:</strong> Este proceso fue iniciado en otra instancia. NO SE PUEDE EJECUTAR!. Comun&iacute;quese con Soporte T&eacute;cnico.</div>
	<table>
	<tr>
		<th>PID</th>
		<th>PROPIETARIO</th>
		<th>CREADO</th>
		<th>FINALIZADO</th>
		<th>PROCESO</th>
		<th>STATUS</th>
		<th>PORCENTAJE</th>
		<th>LANZADO DESDE</th>
	</tr>
	<?php
	$i = 0;
	foreach ($otros as $job):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
		<tr<?php echo $class;?>>
		
			<td align="center"><?php echo $job['Asincrono']['id']; ?></td>
			<td align="center"><?php echo $job['Asincrono']['propietario']; ?></td>
			<td><?php echo $job['Asincrono']['created']; ?></td>
			<td><?php echo $job['Asincrono']['final']; ?></td>
			<td><?php echo $job['Asincrono']['proceso']; ?></td>
			<td align="center"><?php echo $job['Asincrono']['estado']; ?></td>
			<td align="center"><?php echo $job['Asincrono']['porcentaje']; ?></td>
			<td align="center"><?php echo $job['Asincrono']['remote_ip']; ?></td>
		</tr>
	
	<?php endforeach;?>
	
	
	</table>
<?php endif;?>


