<?php

//debug($invalidFiedls);

$disabled = (isset($disabled) ? $disabled : array());
$invalidFiedls = (isset($invalidFiedls) ? $invalidFiedls : array());
$apenom = (isset($apenom) ? $apenom : false);
?>


<script type="text/javascript">
Event.observe(window, 'load', function() {
    <?php if(!in_array('Persona.cuit_cuil',$disabled)):?>
    $('PersonaCuitCuil').focus();
    $('PersonaCuitCuil').observe('blur',function(){
        if(!controlCUIT($('PersonaCuitCuil').getValue(),'PersonaCuitCuil','')){
            $('PersonaCuitCuil').focus();
            document.getElementById("PersonaDocumento").value = "";
            return false;
        }
        document.getElementById("PersonaDocumento").value = $('PersonaCuitCuil').getValue().substring(2,10);
        document.getElementById("PersonaDocumento1").value = $('PersonaCuitCuil').getValue().substring(2,10);
    });
    <?php endif;?>
});





</script>
<h4>Datos Personales</h4>
<hr/>
<table class="tbl_form">
    <tr>
        <td style="font-weight: bold;"><?php echo $frm->number('Persona.cuit_cuil',array('label'=>'CUIT *','size'=>11,'maxlength'=>11,'value' => $persona['cuit_cuil'],'disabled' => in_array('Persona.cuit_cuil',$disabled))); ?></td>
        <td style="font-weight: bold;">
            <?php echo $frm->input('Persona.documento1',array('label'=>'Documento','size'=>8,'maxlength'=>8,'value' => $persona['documento'],'disabled' => 'disabled')); ?>
            <?php echo $frm->hidden('Persona.tipo_documento',array('value' => 'PERSTPDC0001'));?>
            <?php echo $frm->hidden('Persona.documento',array('value' => $persona['documento']));?>
        </td>
        <td style="font-weight: bold;"><?php echo $frm->input('Persona.apellido',array('label'=>'Apellido *','size'=>30,'maxlength'=>30,'value' => (isset($persona['apellido']) ? $persona['apellido'] : ''),'disabled' => in_array('Persona.apellido',$disabled))); ?></td>
        <td style="font-weight: bold;"><?php echo $frm->input('Persona.nombre',array('label'=>'Nombre/s *','size'=>30,'maxlength'=>30,'value' => (isset($persona['nombre']) ? $persona['nombre'] : ''),'disabled' => in_array('Persona.nombre',$disabled))); ?></td>
        <td><?php if($apenom) echo $controles->botonGenerico("/pfyj/personas/modificar_apenom/".$persona['id'],"controles/edit.png")?></td>
    </tr>
</table>   
<table class="tbl_form">
    <tr>
        <td><?php echo $form->input('Persona.sexo',array('type'=>'select','options'=>array('M' =>'MASCULINO', 'F'=>'FEMENINO'),'empty'=>false,'label'=>'Sexo','disabled' => in_array('Persona.sexo',$disabled)));?></td>
        <td><?php echo $frm->calendar('Persona.fecha_nacimiento',"Fecha Nacimiento",(isset($persona['fecha_nacimiento']) && !empty($persona['fecha_nacimiento']) ? $persona['fecha_nacimiento'] : null),date('Y') - 85,date('Y') - 18,in_array('Persona.fecha_nacimiento',$disabled))?></td>
        <td>
            <?php echo $this->renderElement('global_datos/combo_global',array(
                'plugin'=>'config',
                'metodo' => "get_estados_civil",
                'model' => 'Persona.estado_civil',
                'empty' => false,
                'selected' => (isset($persona['estado_civil']) ? $persona['estado_civil'] : ''),
                'label' => 'Estado Civil',
                'disabled' => in_array('Persona.estado_civil',$disabled)
            ))?>
        </td>         
    </tr>
</table>
<table class="tbl_form">
    <td><?php echo $frm->input('Persona.nombre_conyuge',array('label'=>'Conyuge','size'=>40,'maxlength'=>100,'value' => (isset($persona['nombre_conyuge']) ? $persona['nombre_conyuge'] : ''),'disabled' => in_array('Persona.nombre_conyuge',$disabled))); ?></td>    
</table>
<?php if(in_array('Persona.cuit_cuil',$disabled)):?>
<?php echo $frm->hidden('Persona.cuit_cuil',array('value' => $persona['cuit_cuil'])); ?> 
<?php endif;?>
<?php if(in_array('Persona.documento',$disabled)):?>
<?php echo $frm->hidden('Persona.documento',array('value' => $persona['documento'])); ?> 
<?php endif;?>
<?php if(in_array('Persona.apellido',$disabled)):?>
<?php echo $frm->hidden('Persona.apellido',array('value' => $persona['apellido'])); ?> 
<?php endif;?>
<?php if(in_array('Persona.nombre',$disabled)):?>
<?php echo $frm->hidden('Persona.nombre',array('value' => $persona['nombre'])); ?> 
<?php endif;?>
<?php echo $frm->hidden('Persona.idr',array('value' => (isset($persona['idr']) ? $persona['idr'] : ''))); ?>
<?php echo $frm->hidden('Persona.fallecida',array('value' => (isset($persona['fallecida']) ? $persona['fallecida'] : 0))); ?>
<?php echo $frm->hidden('Persona.id',array('value' => (isset($persona['id']) ? $persona['id'] : 0))); ?> 
<?php // debug($persona)?>
<?php ?>