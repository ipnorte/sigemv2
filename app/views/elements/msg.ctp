<?php
App::import('component','Util');
$this->Util = new UtilComponent(null); 
foreach($msg as $tipo=>$notice){
	switch ($tipo) {
			case 'OK':
				print "<div class='notices_ok'>";
				print $this->Util->parse2HTML($notice);
				print "</div>";
				break;
			case 'ERROR':
				print "<div class='notices_error'>";
				print $this->Util->parse2HTML($notice);
				print "</div>";
				break;
			case 'NOTICE':
				print "<div class='notices'>";
				print $this->Util->parse2HTML($notice);
				print "</div>";
				break;
			case 'INFO':
				print "<div class='notices_info'>";
				print $this->Util->parse2HTML($notice);
				print "</div>";
				break;
			default:
				break;
		}	
}
?>
