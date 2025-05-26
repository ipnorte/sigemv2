<?php if($menuPersonas == 1) echo $this->renderElement('personas/padron_header',array('persona' => $persona,'plugin'=>'pfyj'))?>
<?php 
	if(MODULO_V1) echo "<h3>ORDENES DE COMPRAS ANULADAS</h3>";
	else echo "<h3>SOLICITUDES DE CONSUMO ANULADAS</h3>";
?>
<?php echo $this->renderElement('mutual_producto_solicitudes/menu',array('persona' => $persona,'plugin'=>'mutual'))?>

<?php echo $this->renderElement('mutual_producto_solicitudes/grilla',array('solicitudes'=>$solicitudes,'edit' => true,'plugin' => 'mutual','anuladas' => 1))?>

<?php //   debug($solicitudes)?>