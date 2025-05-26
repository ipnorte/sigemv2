<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'PADRON DE VENDEDORES'))?>
<?php 
$tabs = array(
				0 => array('url' => '/ventas/vendedores/alta','label' => 'Nuevo Vendedor', 'icon' => 'controles/add.png','atributos' => array(), 'confirm' => null),
// 				1 => array('url' => '/pfyj/personas/imprimir_padron','label' => 'Imprimir Padron', 'icon' => 'controles/pdf.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs);	
?>

<table>
	<tr>
		<th></th>
		<th>#</th>
		<th>DOCUMENTO</th>
		<th>VENDEDOR</th>
		<th>DOMICILIO</th>
		<th>USUARIO</th>
		<th>GRUPO</th>
	</tr>
	<?php foreach($vendedores as $vendedor):?>
	<tr>
		<td><?php echo $controles->botonGenerico('/ventas/vendedores/padron/'.$vendedor['Vendedor']['id'],'controles/folder.png')?></td>
		<td><?php echo $vendedor['Vendedor']['id']?></td>
		<td><?php echo $vendedor['Persona']['tdoc_ndoc']?></td>
		<td><strong><?php echo $vendedor['Persona']['apenom']?></strong></td>
		<td><?php echo $vendedor['Persona']['domicilio']?></td>
		<td><strong><?php echo $vendedor['Usuario']['usuario']?></strong></td>
		<td><?php echo $vendedor['Usuario']['Grupo']['nombre']?></td>
	</tr>
	<?php endforeach;?>
</table>

<?php //   debug($vendedores)?>