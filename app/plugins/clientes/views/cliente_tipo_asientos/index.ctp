<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'CONFIGURACION TIPO DE ASIENTOS'))?>
<?php 
$tabs = array(
				0 => array('url' => '/clientes/cliente_tipo_asientos/add/','label' => 'Nuevo Tipo de Asiento', 'icon' => 'controles/add.png','atributos' => array(), 'confirm' => null)
//				1 => array('url' => '/clientes/tipo_asientos/imprimir_tipo_asiento','label' => 'Imprimir Tipo Asiento', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
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
			
			if($tipo['ClienteTipoAsiento']['tipo_asiento'] == 'GR') $tipoAsiento = 'GENERAL';
			elseif($tipo['ClienteTipoAsiento']['tipo_asiento'] == 'PR') $tipoAsiento = 'PROVEEDORES';
			else $tipoAsiento = 'CONCEPTO DEL GASTO';?>
			<tr>
				<td><?php echo $controles->botonGenerico('/clientes/cliente_tipo_asientos/edit/'.$tipo['ClienteTipoAsiento']['id'],'controles/folder.png')?></td>
				<td><?php echo $tipo['ClienteTipoAsiento']['id']?></td>
				<td><?php echo $tipo['ClienteTipoAsiento']['concepto']?></td>
				<td><?php echo $tipoAsiento ?>
				<td><?php echo $controles->botonGenerico('borrar/'. $tipo['ClienteTipoAsiento']['id'],'controles/12-em-cross.png');?></td>	
			</tr>
	
		<?php endforeach;?>
	</table>


<?php echo $this->renderElement('paginado')?>


