<?php
ob_start ();
header('Content-type: application/pdf;');
header('Cache-Control: must-revalidate');
$offset = -1;
$ExpStr = "Expires: " . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT';
header($ExpStr);
echo $content_for_layout;
?>