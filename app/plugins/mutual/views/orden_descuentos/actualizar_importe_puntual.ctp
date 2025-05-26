<?php echo $this->renderElement('head',array('title' => 'ACTUALIZACION DE IMPORTES :: ORDENES DE DESCUENTO ','plugin' => 'config'))?>
<?php 
$tabs = array(
				0 => array('url' => '/mutual/orden_descuentos/actualizar_importe_puntual','label' => 'Proceso Puntual', 'icon' => 'controles/user.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/mutual/orden_descuentos/actualizar_importe_masivo','label' => 'Proceso Masivo', 'icon' => 'controles/group_add.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs,false);			
?>
<h3>PROCESO PUNTUAL DE ACTUALIZACION DE IMPORTES</h3>



<?php echo $frm->create(null,array('onsubmit' => "return false;" ));?>
<div class="areaDatoForm">
	<?php echo $frm->input('Persona.ApeNomAproxima',array('label'=>'PERSONA (Aproximar por Apellido, Nombre)','size'=>50,'maxlenght'=>100)); ?>
	<div id="PersonaApeNom_autoComplete" class="auto_complete"></div>
	<div id="spinner" style="display: none; float: left;color:red;">
	Buscando Personas...<?php echo $html->image('controles/ajax-loader.gif') ?>
	</div>
	
<script type="text/javascript">

	new Ajax.Autocompleter('PersonaApeNomAproxima', 'PersonaApeNom_autoComplete', '<?php echo $this->base?>/pfyj/personas/autocomplete', {minChars:3, afterUpdateElement:getSelection, indicator:'spinner'});
	function getSelection(text, li) {
		var id = li.id;
		showDatosPersona(id);
	}

	function showDatosPersona(id){
		 new Ajax.Updater('DatosPersonaContainer','<?php echo $this->base?>/mutual/orden_descuentos/actualizar_importe_puntual/' + id, {asynchronous:true, evalScripts:true, onComplete:function(request, json) {$('DatosPersonaContainer').show();$('spinner2').hide();}, onLoading:function(request) {$('spinner2').show();$('DatosPersonaContainer').hide();}, requestHeaders:['X-Update', 'DatosPersonaContainer']})		
	}

</script>	
				
</div>
<?php echo $frm->end();?>
	<div id="spinner2" style="display: none; float: left;color:red;">
	Cargando Informacion...<?php echo $html->image('controles/ajax-loader.gif') ?>
	</div>
<div id="DatosPersonaContainer">
</div>
