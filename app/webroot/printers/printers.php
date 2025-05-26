<?php 
$baseDir = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . basename(dirname(dirname(__FILE__)));
$printer =  $baseDir . DIRECTORY_SEPARATOR . "printers" . DIRECTORY_SEPARATOR . (isset($_POST['prn']) && !empty($_POST['prn']) ? $_POST['prn'] : 'p1');

$action = (isset($_POST['action']) && !empty($_POST['action']) ? $_POST['action'] : 'read');
$file = (isset($_POST['file']) && !empty($_POST['file']) ? $_POST['file'] : null);


if($action == "read"):
	$d = dir($printer);
	while (false !== ($fileName = $d->read())) {
	   if(!is_dir($fileName))echo $fileName."\n";
	}
	$d->close();
endif;

if($action == "remove" && file_exists($printer .DIRECTORY_SEPARATOR . $file)) unlink($printer .DIRECTORY_SEPARATOR . $file);


?>