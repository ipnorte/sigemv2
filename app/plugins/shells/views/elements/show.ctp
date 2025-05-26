
<?php 

$enableAPPLET = false;

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
if(!isset($bloqueado)){
	$bloqueado = '0';
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
if(!isset($txt1)){
	$txt1 = '';
}
if(!isset($txt2)){
	$txt2 = '';
}
if(!isset($noStop)){
	$noStop = FALSE;
}

if(!$error):
if(!isset($pUID) || empty($pUID)) $pUID = $this->requestAction("/shells/asincronos/crear/process:$process/action:$accion/target:$target/btn_label:$btn_label/titulo:$titulo/subtitulo:$subtitulo/p1:$p1/p2:$p2/p3:$p3/p4:$p4/p5:$p5/p6:$p6/p7:$p7/p8:$p8/p9:$p9/p10:$p10/p11:$p10/p12:$p12/p13:$p13/txt1:$txt1/txt2:$txt2");
$asincrono = $this->requestAction('/shells/asincronos/getAsincrono/'.$pUID);


// $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
// $newVersion = (isset($INI_FILE['general']['newVersion']) && $INI_FILE['general']['newVersion'] != 0 ? TRUE : FALSE);

$control = 'new_control';
$response_server = "asincrono.php";

/*
if($newVersion) {
    $control = 'new_control2';
    $response_server = "process.php";   
}
*/


?>
	<?php if($enableAPPLET):?>
	<div style="margin-top: 10px;">
		<APPLET code="view.ControlApplet" archive="ControlAsincrono.jar" width=560 height=160 codebase="<?php echo FULL_BASE_URL . $this->base?>" style="background-color: gray;">
			<PARAM NAME="pid" VALUE="<?php echo $pUID?>">
			<PARAM NAME="titulo" VALUE="<?php echo "#". $pUID . " - " . $asincrono['titulo']?>">
			<PARAM NAME="sub_titulo" VALUE="<?php echo ( !empty($asincrono['subtitulo']) || $asincrono['subtitulo'] != "" ? $asincrono['subtitulo'] : "...")?>">
			<PARAM NAME="url_action" VALUE="<?php echo FULL_BASE_URL . $this->base. $asincrono['action_do']."/?pid=$pUID"?>">
			<PARAM NAME="url_target" VALUE="<?php echo (!empty($asincrono['target']) ? $asincrono['target'] : "_self")?>">
			<PARAM NAME="url_status" VALUE="<?php echo FULL_BASE_URL . $this->base?>/asincrono.php">
		</APPLET>
	</div>
	<?php else:?>	
	<?php 
	
	echo $this->renderElement($control,array(
											'plugin' => 'shells',
											'PID' => $pUID,	
											'url_response_server' => FULL_BASE_URL . $this->base . "/" . $response_server,
											'url_action' => FULL_BASE_URL . $this->base . $asincrono['action_do'],
											'url_action_target' => (!empty($asincrono['target']) ? $asincrono['target'] : "_self"),
											'titulo' => $asincrono['titulo'],
											'subtitulo' => ( !empty($asincrono['subtitulo']) || $asincrono['subtitulo'] != "" ? $asincrono['subtitulo'] : "..."),
                                                                                        'noStop' => $noStop,
	));
	
	?>
	<?php endif;?>
	
	
<?php else:?>
	<span style='color:red;'><strong>ERROR:</strong><?php echo $mensaje_error?></span>
<?php endif;?>
<?php 

/***************************************************************************************************************
 * PROCESOS ASINCRONOS
 ***************************************************************************************************************/
//$mensaje_error = "";
//$error = false;
//
//if(!isset($process)){
//	$mensaje_error = "No esta seteado el nombre del shell a ejecutar!.";
//	$error = true;	
//} 
//if(!isset($accion)){
//	$mensaje_error = "No esta seteada la accion.";
//	$error = true;		
//}
//if(!isset($bloqueado)){
//	$bloqueado = '0';
//}
//if(!isset($target)){
//	$target = 'blank';
//}
//if(!isset($btn_label)){
//	$btn_label = 'Salida Proceso Asincrono';
//}
//if(!isset($titulo)){
//	$titulo = 'PROCESO ASINCRONO';
//}
//if(!isset($subtitulo)){
//	$subtitulo = '';
//}
//if(!isset($p1)){
//	$p1 = '';
//}
//if(!isset($p2)){
//	$p2 = '';
//}if(!isset($p3)){
//	$p3 = '';
//}
//if(!isset($p4)){
//	$p4 = '';
//}
//if(!isset($p5)){
//	$p5 = '';
//}
//if(!isset($p6)){
//	$p6 = '';
//}
//if(!isset($p7)){
//	$p7 = '';
//}
//if(!isset($p8)){
//	$p8 = '';
//}
//if(!isset($p9)){
//	$p9 = '';
//}
//if(!isset($p10)){
//	$p10 = '';
//}
//if(!isset($p11)){
//	$p11 = '';
//}
//if(!isset($p12)){
//	$p12 = '';
//}
//if(!isset($p13)){
//	$p13 = '';
//}
//	
//if($error){
//	print "<span style='color:red;'><strong>ERROR:</strong> $mensaje_error</span>";
//
//}else{
//
//	$remote_call_start = (isset($remote_call_start) ? $remote_call_start : "");
//	$remote_call_stop = (isset($remote_call_stop) ? $remote_call_stop : "");
//	
//	$pUID = $this->requestAction("/shells/asincronos/crear/process:$process/action:$accion/target:$target/btn_label:$btn_label/titulo:$titulo/subtitulo:$subtitulo/p1:$p1/p2:$p2/p3:$p3/p4:$p4/p5:$p5/p6:$p6/p7:$p7/p8:$p8/p9:$p9/p10:$p10/p11:$p10/p12:$p12/p13:$p13/");
//	$asincrono = $this->requestAction('/shells/asincronos/getAsincrono/'.$pUID);
//	print $this->requestAction("/shells/asincronos/show/$pUID/remote_call_start:$remote_call_start/remote_call_stop:$remote_call_stop");	
//	
//}
?>

