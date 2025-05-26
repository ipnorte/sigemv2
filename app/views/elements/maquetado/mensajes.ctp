<?php 

if ($session->check('Message.NOTICE')){
	print "<div class='notices'>";
//	print $html->image('controles/alert.png',array("border"=>"0"));
	$session->flash("NOTICE");
	print "<div style='clear:both;'></div>";
	print "</div>";
	$session->del('Message.NOTICE');
}
if ($session->check('Message.ERROR')){
	print "<div class='notices_error2'>";
//	print $html->image('controles/error.png',array("border"=>"0"))."&nbsp;<strong>ERROR:</strong>";
	$session->flash("ERROR");
	print "<div style='clear:both;'></div>";
	print "</div>";
	$session->del('Message.ERROR');
}
if ($session->check('Message.OK')){
	print "<div class='notices_ok'>";
//	print $html->image('controles/check.png',array("border"=>"0"))."&nbsp;<strong>OK!</strong> <br>";
	$session->flash("OK");
	print "<div style='clear:both;'></div>";
	print "</div>";
	$session->del('Message.OK');
}
if ($session->check('Message.ERRORES')){
	print "<div class='notices_error'>";
	if(isset($titulo))print "<strong>".$titulo."</strong><br/>";
	if(!empty($errores)):
		echo "<ul style='margin:5px 0px 5px 25px;list-style-type:square;padding:3px;text-indent:0px;font-size: 12px;'>";
		foreach($errores as $error):
			echo "<li>$error</li>";
		endforeach;
		echo "</ul>";
	endif;
	print "<div style='clear:both;'></div>";
	print "</div>";
	$session->del('Message.ERRORES');
}

?>