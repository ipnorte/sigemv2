<?php echo $this->renderElement('head',array('title' => 'BARRIOS :: MODIFICAR BARRIO'))?>

<?php echo $frm->create('Barrio');?>
<div class="areaDatoForm">
 	<div class='row'>
 		<?php echo $frm->input('nombre',array('label'=>'NOMBRE','size'=>50,'maxlength'=>50)) ?>
 	</div>
	
	<div class='row'>

		<?php echo $frm->input('Barrio.localidadAproxima',array('label'=>'Localidad (Aproximar por Nombre)','size'=>50,'maxlenght'=>100, 'value' => $this->data['Localidad']['nombre'])); ?>
		<div id="BarrioLocalidad_autoComplete" class="auto_complete"></div>
		<?php echo $frm->hidden('Barrio.localidad_id'); ?>


		<span id="ajax_loader1" style="display: none;font-size: 11px;font-style:italic;color:red;">
		Procesando...<?php echo $html->image('controles/red_animated.gif') ?>
		</span>			

		<script type="text/javascript">
		//<![CDATA[
		
			document.getElementById("BarrioLocalidadAproxima").value = "<?php echo $this->data['Localidad']['nombre']?>";
			document.getElementById("BarrioLocalidadId").value = <?php echo $this->data['Localidad']['id']?>;				
		
			new Ajax.Autocompleter('BarrioLocalidadAproxima', 'BarrioLocalidad_autoComplete', '/<?php echo $this->base?>/config/localidades/autocomplete', {minChars:3, afterUpdateElement:getSelectionId2, indicator:'ajax_loader1'});
			function getSelectionId2(text, li) {
				var id = li.id;
				var values = id.split("|");
				document.getElementById("BarrioLocalidadAproxima").value = values[3];
				document.getElementById("BarrioLocalidadId").value = values[0];
				
			}
		//]]>
		</script>

	</div>	
	
	
	<div style="clear: both;"></div>	
<?php //   debug($provincias)?>
</div>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/config/barrios'))?>