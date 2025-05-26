<ul>
 <?php foreach($cuentas as $cuenta): ?>
 
 <?

	$descripcion = $cuenta['PlanCuenta']['codigo'].' - '.$cuenta['PlanCuenta']['descripcion'];
 	
 	$id = $cuenta['PlanCuenta']['id']."|".$cuenta['PlanCuenta']['codigo']."|".$cuenta['PlanCuenta']['descripcion'];
 
 ?>
     <li id="<?php echo $id?>"><?php echo $descripcion?></li>
 <?php endforeach; ?>
</ul> 