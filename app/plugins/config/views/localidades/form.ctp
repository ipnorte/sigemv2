<table class="tbl_form">
	<tr>
		<td>
			<?php echo $frm->input($model.'.localidadAproxima',array('label'=>'LOCALIDAD (Aproximar por Nombre)','size'=>50,'maxlenght'=>100)); ?>
			<div id="<?php echo $model?>Localidad_autoComplete" class="auto_complete"></div>
		</td>
		<td><?php echo $frm->input($model.'.codigo_postal',array('label'=>'CP','size'=>8,'maxlenght'=>8)); ?></td>
		<td><?php echo $this->requestAction('/config/global_datos/cmb_provincias/PROVINCIA/'.$model.".provincia_id");?></td>
	</tr>
</table>
<?php echo $frm->hidden($model.'.localidad_id'); ?>
<?php echo $frm->hidden($model.'.localidad'); ?>
				

<span id="ajax_loader1" style="display: none;font-size: 11px;font-style:italic;color:red;">
Procesando...<?php echo $html->image('controles/red_animated.gif') ?>
</span>				

<script type="text/javascript">
//<![CDATA[

	document.getElementById("<?php echo $model?>Localidad").value = "<?php echo $this->data[$model]['localidad']?>";
	document.getElementById("<?php echo $model?>LocalidadAproxima").value = "<?php echo $this->data[$model]['localidadAproxima']?>";
	document.getElementById("<?php echo $model?>ProvinciaId").value = <?php echo $this->data[$model]['provincia_id']?>;
	document.getElementById("<?php echo $model?>CodigoPostal").value = "<?php echo $this->data[$model]['codigo_postal']?>";
//	document.getElementById("<?php echo $model?>LocalidadId").value = <?php echo (!empty($this->data[$model]['localidad_id']) ? $this->data[$model]['localidad_id'] : 0)?>;	
	document.getElementById("<?php echo $model?>LocalidadId").value = 0;						

	new Ajax.Autocompleter('<?php echo $model?>LocalidadAproxima', '<?php echo $model?>Localidad_autoComplete', '<?php echo $this->base?>/config/localidades/autocomplete/<?php echo $model?>', {minChars:3, afterUpdateElement:getSelectionId2, indicator:'ajax_loader1'});
	function getSelectionId2(text, li) {
		var id = li.id;
		var values = id.split("|");
		document.getElementById("<?php echo $model?>LocalidadId").value = 0;
		document.getElementById("<?php echo $model?>Localidad").value = values[3];
		document.getElementById("<?php echo $model?>LocalidadAproxima").value = values[3];
		document.getElementById("<?php echo $model?>ProvinciaId").value = values[2];
		document.getElementById("<?php echo $model?>CodigoPostal").value = values[1];
		document.getElementById("<?php echo $model?>LocalidadId").value = values[0];
		
	}
//]]>
</script>