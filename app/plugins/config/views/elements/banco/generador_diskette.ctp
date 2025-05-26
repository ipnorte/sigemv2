<?php 
Configure::write('debug',0);
header("Content-type: text/plain"); 
header('Content-Disposition: attachment;filename="'.$diskette['archivo'].'"');
header('Cache-Control: max-age=0');
if(!empty($diskette['cabecera'])) echo $diskette['cabecera'];
foreach($diskette['registros'] as $registro):
	if(!empty($registro)) echo $registro;
endforeach;
if(!empty($diskette['pie'])) echo $diskette['pie'];
?>