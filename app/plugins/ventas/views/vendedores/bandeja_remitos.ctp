<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'MESA DE ENTRADA DE SOLICITUDES'))?>
<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action' =>'bandeja'))?>
	<table class="tbl_form">
		<tr>
			<td>VENDEDOR</td><td><?php echo $frm->input('VendedorBandeja.vendedor_id',array('type' => 'select', 'options' => $vendedores))?></td>
			<td><input type="submit" name="data[VendedorBandeja][btn_presentar]" value="PRESENTAR SOLICITUDES"/></td>
			<td><input type="submit" name="data[VendedorBandeja][btn_ver_remitos]" value="VER CONSTANCIAS"/></td>
		</tr>
	</table>
	<?php echo $frm->end()?>
</div>
<h3>CONSTANCIAS DE ENTREGA EMITIDAS :: VENDEDOR #<?php echo $vendedor['Vendedor']['id'] . " - " . $vendedor['Persona']['tdoc_ndoc_apenom']?></h3>
<?php if(!empty($remitos)):?>
	<table>
		<tr>
			<th>#</th>
			<th>FECHA</th>
			<th>GENERADO POR</th>
			<th>OBSERVACIONES</th>
		</tr>
		<?php foreach($remitos as $remito):?>
			<tr>
				<td><strong><?php echo $controles->linkModalBox($remito['VendedorRemito']['id'],array('title' => 'CONSTANCIA DE PRESENTACION #' . $remito['VendedorRemito']['id'],'url' => '/ventas/vendedores/ficha_remito/'.$remito['VendedorRemito']['id'],'h' => 450, 'w' => 850))?></strong></td>
				<td><?php echo $remito['VendedorRemito']['created']?></td>
				<td><?php echo $remito['VendedorRemito']['user_created']?></td>
				<td><?php echo $remito['VendedorRemito']['observaciones']?></td>
			</tr>
		<?php endforeach;?>		
	</table>
	<?php //   debug($remitos)?>
<?php else:?>
	<h4>NO EXISTEN CONSTANCIAS EMITIDAS PARA EL VENDEDOR</h4>
<?php endif;?>