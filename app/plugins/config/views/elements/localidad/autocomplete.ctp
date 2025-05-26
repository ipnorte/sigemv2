<table class="tbl_form">
    <tr>
        <td>
            <?php echo $frm->input($model.'.localidadAproxima',array('label'=>'LOCALIDAD (Aproximar por Nombre) *','size'=>50,'maxlenght'=>100,'value' => $localidad)); ?>
            <div id="<?php echo $model?>Localidad_autoComplete" class="auto_complete"></div>
        </td>
        <td><?php echo $frm->number($model.'.codigo_postal',array('label'=>'CP *','size'=>8,'maxlenght'=>8)); ?></td>
        <td>
            <?php echo $this->renderElement('localidad/combo_provincias',array(
                'plugin'=>'config',
                'model' => $model.'.provincia_id',
                'empty' => false,
                'selected' => (!empty($provincia_id) ? $provincia_id : ""),
                'label' => 'Provincia'
            ))?>            
        </td>
    </tr>
</table>
<?php echo $frm->hidden($model.'.localidad_id'); ?>
<?php echo $frm->hidden($model.'.localidad'); ?>
<span id="ajax_loader1" style="display: none;font-size: 11px;font-style:italic;color:red;">
Procesando...<?php echo $html->image('controles/red_animated.gif') ?>
</span>	
<script type="text/javascript">
	document.getElementById("<?php echo $model?>Localidad").value = "<?php echo $localidad?>";
	document.getElementById("<?php echo $model?>LocalidadAproxima").value = "<?php echo $localidadAproxima?>";
	document.getElementById("<?php echo $model?>ProvinciaId").value = "<?php echo (!empty($provincia_id) ? $provincia_id : 0)?>";
	document.getElementById("<?php echo $model?>CodigoPostal").value = "<?php echo $codigo_postal?>";
//	document.getElementById("<?php echo $model?>LocalidadId").value = <?php echo (!empty($localidad_id) ? $localidad_id : 0)?>;	
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
</script>