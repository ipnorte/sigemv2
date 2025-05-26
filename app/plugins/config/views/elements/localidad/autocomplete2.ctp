<table class="tbl_form">
    <tr>
        <td style="font-weight: bold;">
            <?php echo $this->renderElement('localidad/combo_provincias',array(
                'plugin'=>'config',
                'model' => 'Persona.provincia_id',
                'empty' => false,
                'selected' => (!empty($provincia_id) ? $provincia_id : ""),
                'label' => 'Provincia'
            ))?>            
        </td>        
        <td>
            <?php echo $frm->input('Persona.codigoPostalAproxima',array('label'=>'Aproximar por CP o Localidad *','size'=>50,'maxlenght'=>8)); ?>
            <div id="PersonaCodigoPostalAutoComplete" class="auto_complete"></div>
        </td>        
    </tr>
    <tr style="font-weight: bold;">
        <td colspan="2"><?php echo $frm->number('Persona.codigo_postal',array('label'=>'CP *','size'=>8,'maxlenght'=>8)); ?>
        <?php echo $frm->input('Persona.localidad',array('label'=>'LOCALIDAD * ','size'=>50,'maxlenght'=>100,'value' => $localidad,)); ?></td>
    </tr>
</table>
<?php echo $frm->hidden('Persona.localidad_id'); ?>
<?php // echo $frm->hidden('Persona.provincia_id'); ?>
<?php // echo $frm->hidden('Persona.codigo_postal'); ?>
<span id="ajax_loader1" style="display: none;font-size: 11px;font-style:italic;color:red;">
Procesando...<?php echo $html->image('controles/red_animated.gif') ?>
</span>	
<script type="text/javascript">
    
Event.observe(window, 'load', function(){
//    document.getElementById("PersonaCodigoPostalAproxima").value = "";
    callAutocomplete();
    $('PersonaProvinciaId').observe('change',function(){
        document.getElementById("PersonaCodigoPostalAproxima").value = "";
    });    
});

function callAutocomplete(){
    document.getElementById("PersonaLocalidad").value = "<?php echo $localidad?>";
//    document.getElementById("PersonaCodigoPostalAproxima").value = "<?php // echo $localidadAproxima?>";
//    document.getElementById("<?php echo $model?>ProvinciaId").value = "<?php echo (!empty($provincia_id) ? $provincia_id : 0)?>";
    document.getElementById("PersonaCodigoPostal").value = "<?php echo $codigo_postal?>";
    document.getElementById("PersonaLocalidadId").value = 0;						

    new Ajax.Autocompleter(
            'PersonaCodigoPostalAproxima', 
            'PersonaCodigoPostalAutoComplete', 
            '<?php echo $this->base?>/config/localidades/autocomplete2', 
            {
                minChars:2, 
                afterUpdateElement:getSelectionId2, 
                indicator:'ajax_loader1',
                callback: function(request,parameters){
                    return parameters + '&data[Persona][provincia_id]=' + $('PersonaProvinciaId').getValue();
                },
            }
    );
    function getSelectionId2(text, li) {
            var id = li.id;
            var values = id.split("|");
            document.getElementById("PersonaLocalidadId").value = 0;
            document.getElementById("PersonaLocalidad").value = values[3];
//            document.getElementById("<?php echo $model?>LocalidadAproxima").value = values[3];
            document.getElementById("PersonaProvinciaId").value = values[2];
            document.getElementById("PersonaCodigoPostal").value = values[1];
            document.getElementById("PersonaLocalidadId").value = values[0];

    }

}
</script>

<?php // echo $localidad?>