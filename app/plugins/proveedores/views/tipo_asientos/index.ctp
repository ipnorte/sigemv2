<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'CONFIGURACION TIPO DE ASIENTOS'))?>
<?php 
$tabs = array(
				0 => array('url' => '/proveedores/tipo_asientos/add/','label' => 'Nuevo Tipo de Asiento', 'icon' => 'controles/add.png','atributos' => array(), 'confirm' => null)
//				1 => array('url' => '/proveedores/proveedores/imprimir_padron','label' => 'Imprimir Padron', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs);	
		
?>

<?php echo $this->renderElement('paginado')?>
	<table>
		<tr>
			<th></th>
			<th><?php echo $paginator->sort('NUMERO','id');?></th>
			<th><?php echo $paginator->sort('C O N C E P T O','concepto');?></th>
			<th>Tipo Asiento</th>
			<th>Borrar</th>
		</tr>
		<?php foreach($tipoAsientos as $tipo):
			
			if($tipo['TipoAsiento']['tipo_asiento'] == 'GR') $tipoAsiento = 'GENERAL';
			elseif($tipo['TipoAsiento']['tipo_asiento'] == 'PR') $tipoAsiento = 'PROVEEDORES';
			else $tipoAsiento = 'CONCEPTO DEL GASTO';?>
			<tr>
				<td><?php echo $controles->botonGenerico('/proveedores/tipo_asientos/edit/'.$tipo['TipoAsiento']['id'],'controles/folder.png')?></td>
				<td><?php echo $tipo['TipoAsiento']['id']?></td>
				<td><?php echo $tipo['TipoAsiento']['concepto']?></td>
				<td><?php echo $tipoAsiento ?>
				<td><?php echo $controles->botonGenerico('borrar/'. $tipo['TipoAsiento']['id'],'controles/12-em-cross.png');?></td>	
			</tr>
	
		<?php endforeach;?>
	</table>


<?php echo $this->renderElement('paginado')?>