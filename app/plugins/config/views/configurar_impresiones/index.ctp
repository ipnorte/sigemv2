<?php
//debug($cnfImpresion)
$formato_cheque = array('0' => 'Papel continuo o cheques sueltos en horizontal', 
                        '1' => 'Hoja de cheques (varios cheques en una hoja)', 
                        '2' => 'Cheques sueltos en vertical');
?>
<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'CONFIGURAR IMPRESION DE CHEQUES'))?>

<?php 
$tabs = array(
				0 => array('url' => '/config/configurar_impresiones/add','label' => 'Nueva Configuracion', 'icon' => 'controles/add.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs);
?>


<?php if(!empty($cnfImpresion)):?>

	<?php echo $this->renderElement('paginado')?>
	
		<script language="Javascript" type="text/javascript">
			var config = <?php echo $error?>;
			Event.observe(window, 'load', function() {
				
				<?php if($error==1):?>
					alert("DATOS GRABADOS CORRECTAMENTE"); 
				<?php elseif($error==2):?>
					alert("ERROR AL GRABAR LOS DATOS"); 
				<?php endif; ?>
			
			});
		
		
			function toggleCellMouseOver(idRw, status){
				var celdas = $(idRw).immediateDescendants();
				if(status)celdas.each(function(td){td.addClassName("selected2");});
				else celdas.each(function(td){td.removeClassName("selected2");});
			}
		
		</script>
		
		<table cellpadding="0" cellspacing="0">
		
			<tr>
				<th>Id</th>
				<th>Descripcion</th>
				<th>Ancho/Largo</th>
				<th>Alto</th>
				<th>Formato de Impresion</th>
                                <th></th>
			</tr>
		
			<?php
				$i = 0;
				foreach ($cnfImpresion as $impresion):
					$class = null;
                                        $formato = $impresion['ConfigurarImpresion']['talonario'];
					if ($i++ % 2 == 0) {
						$class = ' class="altrow"';
					}
					?>
					<tr>
						<td><?php echo $controles->botonGenerico('/config/configurar_impresiones/edit/'.$impresion['ConfigurarImpresion']['id'],'controles/folder.png')?></td>
						<td><?php echo $impresion['ConfigurarImpresion']['descripcion']?></td>
						<td><?php echo $impresion['ConfigurarImpresion']['ancho']?> cm.</td>
						<td><?php echo $impresion['ConfigurarImpresion']['alto']?> cm.</td>
						<td><?php echo strtoupper($formato_cheque[$formato])?></td>
                                                <td><?php echo $controles->botonGenerico('/config/configurar_impresiones/imprimir_cheque_ejemplo_pdf/' . $impresion['ConfigurarImpresion']['id'],'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));?></td>
				
					</tr>
			<?php endforeach; ?>	
		
		</table>
	
	
	
	<?php echo $this->renderElement('paginado')?>
<?php endif;?>
