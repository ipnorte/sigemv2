<ul>
 <?php foreach($localidades as $localidad): ?>
 
 <?

 	$nombre = utf8_encode(trim($localidad['Localidad']['nombre']));
 	$cp = trim($localidad['Localidad']['cp']);
 	$pcia = trim($localidad['Provincia']['nombre']);
 	
 	$string = "<strong>".$nombre ."&nbsp; (CP $cp) - $pcia</strong>";
 	
 	$id = $localidad['Localidad']['id']."|".$cp."|".$localidad['Provincia']['id']."|".$nombre;
 
 ?>
     <li id="<?php echo $id?>"><?php echo $string?></li>
 <?php endforeach; ?>
</ul> 