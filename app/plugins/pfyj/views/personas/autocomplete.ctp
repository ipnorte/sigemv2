<ul>
 <?php foreach($personas as $persona): ?>
 
 <?

 	$nombre = trim($persona['Persona']['nombre']);
 	$apellido = trim($persona['Persona']['apellido']);
 	$string = "<strong>".$apellido . ', ' . $nombre ."</strong> - " . $persona['Persona']['tipo_documento_desc'] . ' ' . $persona['Persona']['documento'] ;
 	
 	$id = $persona['Persona']['id'];
 
 ?>
     <li id="<?php echo $id?>"><?php echo $string?></li>
 <?php endforeach; ?>
</ul> 