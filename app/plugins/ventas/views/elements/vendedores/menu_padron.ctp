<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'PADRON DE VENDEDORES'))?>
<?php //   debug($vendedor)?>
<div class="areaDatoForm2" style="background-color:#eeee77;border-bottom:2px solid #660000;color:#000000;border-left: 1px solid #eeee77;border-right: 1px solid #eeee77;border-top: 1px solid #eeee77;">
<h3 style="font-family: verdana;color:#440000;font-size: 18px;"><?php echo "#".$vendedor['Vendedor']['id']." | ". $vendedor['Persona']['tdoc_ndoc_apenom'];?></h3>
</div>
<?php 
$tabs = array(
				0 => array('url' => '/ventas/vendedores/padron/'.$vendedor['Vendedor']['id'],'label' => 'Datos Generales', 'icon' => 'controles/user.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/ventas/vendedores/planes/'.$vendedor['Vendedor']['id'],'label' => 'Planes', 'icon' => 'controles/chart_organisation.png','atributos' => array(), 'confirm' => null),
				
				3 => array('url' => '/ventas/vendedores','label' => 'Otro', 'icon' => 'controles/reload3.png','atributos' => array(), 'confirm' => null),
				
			);
echo $cssMenu->menuTabs($tabs);		
?>

