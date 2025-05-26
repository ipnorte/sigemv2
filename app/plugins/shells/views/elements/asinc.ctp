<?php 

/***************************************************************************************************************
 * PROCESOS ASINCRONOS
 ***************************************************************************************************************/
$mensaje_error = "";
$error = false;

if(!isset($process)){
	$mensaje_error = "No esta seteado el nombre del shell a ejecutar!.";
	$error = true;	
} 
if(!isset($accion)){
	$mensaje_error = "No esta seteada la accion.";
	$error = true;		
}
if(!isset($target)){
	$target = 'blank';
}
if(!isset($btn_label)){
	$btn_label = 'Salida Proceso Asincrono';
}
if(!isset($titulo)){
	$titulo = 'PROCESO ASINCRONO';
}
if(!isset($subtitulo)){
	$subtitulo = '';
}
if(!isset($p1)){
	$p1 = '';
}
if(!isset($p2)){
	$p2 = '';
}if(!isset($p3)){
	$p3 = '';
}
if(!isset($p4)){
	$p4 = '';
}
if(!isset($p5)){
	$p5 = '';
}
if(!isset($p6)){
	$p6 = '';
}
if(!isset($p7)){
	$p7 = '';
}
if(!isset($p8)){
	$p8 = '';
}
if(!isset($p9)){
	$p9 = '';
}
if(!isset($p10)){
	$p10 = '';
}
if(!isset($p11)){
	$p11 = '';
}
if(!isset($p12)){
	$p12 = '';
}
if(!isset($p13)){
	$p13 = '';
}
	
if($error){
	print "<span style='color:red;'><strong>ERROR:</strong> $mensaje_error</span>";

}else{

	$pUID = $this->requestAction("/shells/asincronos/crear/process:$process/action:$accion/target:$target/btn_label:$btn_label/titulo:$titulo/subtitulo:$subtitulo/p1:$p1/p2:$p2/p3:$p3/p4:$p4/p5:$p5/p6:$p6/p7:$p7/p8:$p8/p9:$p9/p10:$p10/p11:$p10/p12:$p12/p13:$p13/");
	$asincrono = $this->requestAction('/shells/asincronos/getAsincrono/'.$pUID);
//	print $this->requestAction('/shells/asincronos/show/'.$pUID);	
	$pid = $asincrono['pid'];
	$otros = $asincrono['otros'];
	$refreshPogressBar = $asincrono['refreshPogressBar'];
	$pathToResponser = $asincrono['pathToResponser'];
	$conexion = $asincrono['conexion'];
	$titulo = $asincrono['titulo'];
	$subtitulo = $asincrono['subtitulo'];
	$btn_label = $asincrono['btn_label'];
	$action_do = $asincrono['action_do'];
	$target = $asincrono['target'];
	
?>	

	<style>
	/** barra de avance **/
	
	.botonera {
		empty-cells: show;
		border: 1px solid #e2e6ea;
		margin-bottom: 5px;
	}
	
	#pb_container{
		width:98%;
		/*margin: 5px;*/
		padding: 5px;
		border:1px solid #e2e6ea;
		overflow: hidden;
		background-color: #F5F7F7;
	}
	
	#pb_container td{padding:3px;background-color: #F5F7F7;}
	
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
	
	#pb_container .barra_container{
		width:400px;
		padding:5px;
		margin:5px;
	}
	
	#pb_container .barra{
		width:100%;
		height:25px;
		background-color: #e2e6ea;
		border:1px solid #e2e6ea;
		overflow: hidden;		
	}
	
	
	#pb_container .controles{
		width:50%;
		float:left;
		padding:3px;
	}
	
	#response_status{
		padding:1px;
		font-size: 90%;	
		height:40px;
		overflow: hidden;
	}
	
	#btn_accion{
		padding: 5px;
		border: 1px solid #e2e6ea;
		background: #e2e6ea;
		color: #CC0000;
		font-weight: normal;
	}
	#btn_accion a{
		text-decoration: none;
		color: #CC0000;
		font-weight: normal;	
	}
	
	#btn_accion a:hover{
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
			$('btn_stop').disable();
			$('btn_accion').hide();
			Event.observe('btn_start', 'click', function(event) {
				".(isset($remote_call_start) && !empty($remote_call_start) ? $remote_call_start.";" : "")."
				$('btn_start').disable();$('btn_stop').enable();$('btn_accion').hide();
				$START
				
				$('progress_bar').update('');
				
				new PeriodicalExecuter(function(p) {
		
					new Ajax.Request('".$pathToResponser."?PID=".$pid."&ACCION=CHECK_STATUS&UID=".$conexion."', 
						{asynchronous:true, evalScripts:true, 
						onComplete:function(request, json) 
							{
								var estatus = request.responseText;
								if(estatus == 'S' || estatus == 'E') {
									p.stop();
									$('btn_stop').disable();
									$('btn_start').enable();
								}
								if(estatus == 'F'){
									p.stop();
									$('btn_stop').disable();
									$('btn_start').enable();
									$('btn_accion').show();
								}
							}
						}	
					)
				
					new Ajax.Updater(	'progress_bar',
										'".$pathToResponser."?PID=".$pid."&ACCION=PROGRESS_BAR&UID=".$conexion."', 
										{asynchronous:true, evalScripts:true, requestHeaders:['X-Update', 'progress_bar']}
									);
					new Ajax.Updater(	'response_status',
										'".$pathToResponser."?PID=".$pid."&ACCION=STATUS&UID=".$conexion."', 
										{asynchronous:true, evalScripts:true, requestHeaders:['X-Update', 'response_status']}
									);
		
					}, $frecuencia);		
				
			}, false);
			Event.observe('btn_stop', 'click', function(event) {
				".(isset($remote_call_stop) && !empty($remote_call_stop) ? $remote_call_stop.";" : "")."
				$STOP
				$('btn_stop').disable();
				$('btn_start').enable();
				$('btn_accion').hide();
			}, false);
		"); 
//		echo $javascript->event('window','onbeforeunload',"alert('cierre ventana proceso...');$STOP");	
			
		?>

<script type="text/javascript">

// CONTROL CIERRE VENTANA

//	var ClosingVar = true;
//	window.onbeforeunload = ExitCheck;
//	function ExitCheck(){  
//		///control de cerrar la ventana///
//		if(ClosingVar) { 
//			 ExitCheck = false;
//			 new Ajax.Request('<?php echo $this->base?>/mutual/asincronos/stop/<?php echo $pid?>', {asynchronous:true, evalScripts:true});
//		     return "SI DECIDE CONTINUAR, EL PROCESO QUE SE ENCUENTRA EN EJECUCION SERA DETENIDO";
//		}else{
//			ClosingVar = true;
//		}	
//	}


//	window.onbeforeunload = function(){
//		if(confirm('ATENCION!!! \n Esta intentando cerrar una ventana con un proceso ejecutandose.')){
//			new Ajax.Request('<?php echo $this->base?>/mutual/asincronos/stop/<?php echo $pid?>', {asynchronous:true, evalScripts:true});
//		}
//	}
</script>
	
		<table id='pb_container'>
		
		  	<tr>
		   		<td colspan="2">
		   			<div class="titulo">#<?php echo $pid?>&nbsp;|&nbsp;<?php echo $titulo?></div>
		   			<div class="subtitulo"><?php echo $subtitulo?></div>
		   		</td>
			</tr>
		  <tr>
		    <td class='barra_container'>
				<div class='barra' id="progress_bar">
					<div style='float:left;width:0%;height:25px;background-color: #003366'></div>		
				</div>
			</td>
			
		    <td valign="top">
		    	<div id='response_status'></div>
		    </td>
		  </tr>
		  <tr>
		   	<td colspan="2">
		    	<table class="botonera">
		    		<tr>
						<td>
							<input type="button" id="btn_start" name="btn_start" value="COMENZAR" >
						</td>
						<td>
							<input type="button" id="btn_stop" value="DETENER">
						</td>
						<td>
							<div id="btn_accion">
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

<?php }?>

