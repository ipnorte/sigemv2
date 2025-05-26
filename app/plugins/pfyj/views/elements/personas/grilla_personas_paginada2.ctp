<?php 

/*********************************************************************************************
 * RENDER GRILLA BUSQUEDA DE PERSONAS
 ********************************************************************************************/
$icon 		= 'controles/folder_user.png';

?>

<?php if(!empty($personas)):?>

	
	<script type="text/javascript">
	
	function toggleCellMouseOver(idRw, status){
		var celdas = $(idRw).immediateDescendants();
		if(status)celdas.each(function(td){td.addClassName("selected2");});
		else celdas.each(function(td){td.removeClassName("selected2");});
	}
	
	</script>
	<?php if(count($personas) == $limit):?>
	<div class="notices">*** SE MUESTRAN LOS <?php echo $limit?> PRIMEROS REGISTROS ENCONTRADOS PARA EL CRITERIO DE BUSQUEDA ***</div>
	<?php endif;?>
	<div style="clear: both;"/>
	<table cellpadding="0" cellspacing="0">
	
		<tr>
			<th></th>
			<th>TIPO</th>
			<th>DOCUMENTO</th>
			<th>APELLIDO</th>
			<th>NOMBRE</th>
			<th></th>
			<th colspan="2">#SOCIO</th>
			<th>CATEGORIA</th>
			<th>ESTADO</th>
			<th>ULTIMA CALIFICACION</th>
	<!--		<th>DOMICILIO</th>-->
			<?php if(!empty($params['busqueda_avanzada_by_beneficio'])):?>
			<th>BENEFICIO</th>
			<?php endif;?>
		</tr>
	
		<?php
		$i = 0;
		foreach ($personas as $persona):
			$class = null;
			if ($i++ % 2 == 0) {
	//			$class = ' class="altrow"';
			}
	//		debug($persona);
			$click = "onclick = \"window.location.href = '".$html->url($accion.$persona['Persona']['id'],true)."'\" style=\"cursor: pointer;\"";
		?>
			<tr<?php echo $class;?> id="LTR_<?php echo $i?>" onmouseover="toggleCellMouseOver('LTR_<?php echo $i?>',true)" onmouseout="toggleCellMouseOver('LTR_<?php echo $i?>',false)">
				<td align="center" <?php echo $click?>><?php echo $controles->botonGenerico($accion.$persona['Persona']['id'],$icon)?></td>
				<td <?php echo $click?>><?php echo $this->requestAction('/config/global_datos/valor/'.$persona['Persona']['tipo_documento'])?></td>
				<td <?php echo $click?>><?php echo $persona['Persona']['documento']?></td>
				<td <?php echo $click?>><strong><?php echo $persona['Persona']['apellido']?></strong></td>
				<td <?php echo $click?>><strong><?php echo $persona['Persona']['nombre']?></strong></td>
				<td <?php echo $click?>>
				
				<?php if($persona['Persona']['fallecida'] == 1):?>
					<span style="color:white;background-color:red;font-size: 10px;font-weight: normal;padding:2px;">F</span>
				<?php endif;?>			
				
				</td>
				<td <?php echo $click?> align="center"><?php echo (!empty($persona['Socio']['id']) ? $html->image('controles/medal_gold_1.png') : '')?></td>
				<td <?php echo $click?>><strong><?php echo (!empty($persona['Socio']['id']) ? "#".$persona['Socio']['id'] : '')?></strong></td>
				<td <?php echo $click?>><?php echo $util->globalDato($persona['Socio']['categoria'])?></td>
				<td <?php echo $click?>>
					<?php if(!empty($persona['Socio']['id'])):?>
						<strong>
							<?php if($persona['Socio']['activo']==1):?>
								<span style="color:green;">VIGENTE</span>
							<?php else:?>
								<span style="color:red;">NO VIGENTE</span>
							<?php endif;?>	
						</strong>	
					<?php endif;?>
				</td>
				<td <?php echo $click?>>
					<?php if(!empty($persona['Socio']['id'])):?>
						<?php echo $util->armaFecha($persona['Socio']['fecha_calificacion'])?>
						<span class="<?php echo $persona['Socio']['calificacion']?>">
						<strong><?php echo $util->globalDato($persona['Socio']['calificacion'])?></strong>
						</span>
					<?php endif;?>
				</td>
				<!-- 
				<td <?php echo $click?>>
					<?php 
						echo $persona['Persona']['calle'] . " " .($persona['Persona']['numero_calle'] != 0 ? $persona['Persona']['numero_calle'] : '');
						echo " - ";
						if(!empty($persona['Localidad']['nombre'])){
							echo $persona['Localidad']['nombre'] ." (CP ".$persona['Localidad']['cp'].")"; 
						}else{
							echo $persona['Persona']['localidad'] ." (CP ".$persona['Persona']['codigo_postal'].")";
						}					
					?>
				</td>
				 -->
				<?php if(!empty($params['busqueda_avanzada_by_beneficio'])):?>
				<td <?php echo $click?> nowrap="nowrap">
				<?php echo $persona['PersonaBeneficio']['string']?>
				</td>
				<?php endif;?>
			</tr>
		<?php endforeach; ?>	
	
	</table>
	
	
	<?php //   debug($personas)?>
	
	

<?php endif;?>