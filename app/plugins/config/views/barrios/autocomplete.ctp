<ul>
 <?php foreach($barrios as $barrio): ?>
 
 <?php

 	$nombre = trim($barrio['Barrio']['nombre']);
 	$cp = trim($barrio['Localidad']['cp']);
 	$localidad = trim($barrio['Localidad']['nombre']);
 	$provinciaId = trim($barrio['Localidad']['provincia_id']);
 	
 	$string = "<strong>".$nombre ." - $localidad &nbsp; (CP $cp) </strong>";
 	
 	$id = $barrio['Barrio']['id']."|".$cp."|".$barrio['Localidad']['id']."|".$provinciaId.'|'.$localidad.'|'.$nombre;
 
 ?>
     <li id="<?php echo $id?>"><?php echo $string?></li>
 <?php endforeach; ?>
</ul> 