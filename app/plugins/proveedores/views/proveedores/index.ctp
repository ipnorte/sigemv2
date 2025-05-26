<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'PADRON DE PROVEEDORES'))?>
<?php 
$tabs = array(
				0 => array('url' => '/proveedores/proveedores/add/'.$tipoProveedor,'label' => 'Nuevo Proveedor', 'icon' => 'controles/add.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/proveedores/proveedores/imprimir_padron','label' => 'Imprimir Padron', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs);			
?>

<div class='row'>
	<?php echo $form->create(null,array('action'=> 'index'));?>
	<table>
		<tr>
			<td colspan="7"><strong>PROVEEDOR</strong></td>
		</tr>
		<tr>
			<td>
				<?php echo $frm->input('Proveedor.cuit', array('label' => 'C.U.I.T.:'))?>
			</td>
			<td>
				<?php echo $frm->input('Proveedor.razon_social',array('label' => 'Razon Social:'))?>
			</td>
			<td><input type="submit" class="btn_consultar" value="APROXIMAR" /></td>
		</tr>
	</table>
	<?php echo $form->end();?> 
</div>

<?php if(!empty($proveedores)):?>

	<script type="text/javascript">
	
	function toggleCellMouseOver(idRw, status){
		var celdas = $(idRw).immediateDescendants();
		if(status)celdas.each(function(td){td.addClassName("selected2");});
		else celdas.each(function(td){td.removeClassName("selected2");});
	}
	
	</script>

<?php echo $this->renderElement('paginado')?>
	<table>
		<tr>
			<th></th>
			<th><?php echo $paginator->sort('C.U.I.T.','cuit');?></th>
			<th><?php echo $paginator->sort('RAZON SOCIAL','razon_social');?></th>
			<th>Domicilio</th>
			<th>Barrio</th>
			<th>Localidad</th>
			<th>Responsable</th>
			<th>Contacto</th>
			<th>Tipo Prov.</th>
                        <th>Pers.</th>
		</tr>
		<?php 
                    $i = 0;
                    foreach($proveedores as $proveedor):
			
			if($proveedor['Proveedor']['tipo_proveedor'] == '1') $tipoProveedor = 'Comercio';
			elseif($proveedor['Proveedor']['tipo_proveedor'] == '2') $tipoProveedor = 'Productor';
			else $tipoProveedor = 'Proveedor';
                        
                        $click = "onclick = \"window.location.href = '".$html->url('/proveedores/proveedores/index/'.$proveedor['Proveedor']['id'],true)."'\" style=\"cursor: pointer;\"";
                        
                        ?>
			<tr id="LTR_<?php echo $i?>" onmouseover="toggleCellMouseOver('LTR_<?php echo $i?>',true)" onmouseout="toggleCellMouseOver('LTR_<?php echo $i?>',false)"> 
				
				<td <?php echo $click?>><?php echo $controles->botonGenerico('/proveedores/proveedores/index/'.$proveedor['Proveedor']['id'],'controles/folder.png')?></td>
				<td <?php echo $click?>><?php echo $proveedor['Proveedor']['cuit']?></td>
				<td <?php echo $click?>><?php echo $proveedor['Proveedor']['razon_social']?></td>
				<td <?php echo $click?>><?php echo $proveedor['Proveedor']['calle'] . " " . $proveedor['Proveedor']['numero_calle']?></td>
				<td <?php echo $click?>><?php echo rtrim($proveedor['Proveedor']['barrio'])?></td>
				<td <?php echo $click?>><?php echo $proveedor['Proveedor']['codigo_postal'] . '-' . $proveedor['Proveedor']['localidad']?></td>
				<td <?php echo $click?>><?php echo $proveedor['Proveedor']['responsable']?></td>
				<td <?php echo $click?>><?php echo $proveedor['Proveedor']['contacto']?></td>
				<td <?php echo $click?>><?php echo $tipoProveedor ?>
                                <td <?php echo $click?>><?php echo ($proveedor['Proveedor']['tipo_persona'] == 1 ? 'Física' : 'Juridica')?></td>    
			</tr>
	
		<?php
                        $i++;
                    endforeach;
                ?>
	</table>

<?php //   debug($proveedores)?>

<?php echo $this->renderElement('paginado')?>

<?php endif;?>