<?php 
header ("Cache-Control: no-cache, must-revalidate");
ob_start();
?>
<?php echo $content_for_layout ?>
