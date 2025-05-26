<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>

<h3>ADICIONALES</h3>

<div class="actions">
<?php if($persona['Persona']['fallecida'] == 0) echo $controles->botonGenerico('add/'.$persona['Persona']['id'],'controles/user_add.png','NUEVO ADICIONAL')?>
</div>

<?php if(!empty($adicionales)):?>

	<table>
	
		<tr>
			<th></th>
			<th></th>
			<th>DOCUMENTO</th>
			<th>NOMBRE</th>
			<th>SEXO</th>
			<th>VINCULO</th>
			<th>FECHA NACIMIENTO</th>
			<th>DOMICILIO</th>
		
		</tr>
		<?php foreach($adicionales as $adicional):?>
		
			<tr>
				<td><?php echo $controles->getAcciones($adicional['SocioAdicional']['id'],false,true,false)?></td>
				<td><?php echo $controles->botonGenerico('borrar_adicional/'.$adicional['SocioAdicional']['id'],'controles/user-trash.png','',null,"BORRAR AL ADICIONAL: ".$adicional['SocioAdicional']['tdoc_ndoc'] ." | " . $adicional['SocioAdicional']['apenom']."?")?></td>
				<td><?php echo $adicional['SocioAdicional']['tdoc_ndoc']?></td>
				<td><?php echo $adicional['SocioAdicional']['apenom']?></td>
				<td align="center"><?php echo $adicional['SocioAdicional']['sexo']?></td>
				<td><?php echo $adicional['SocioAdicional']['vinculo_desc']?></td>
				<td align="center"><?php echo $util->armaFecha($adicional['SocioAdicional']['fecha_nacimiento'])?></td>
				<td><?php echo $adicional['SocioAdicional']['domicilio']?></td>
			</tr>
		
		<?php endforeach;?>
	
	</table>

<?php //   debug($adicionales)?>

<?php endif;?>
<?php //   debug($persona)?>