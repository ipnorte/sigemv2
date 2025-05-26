<h4>Domicilio</h4>
<hr/>
<table class="tbl_form">
    <tr>
        <td style="font-weight: bold;"><?php echo $frm->input('Persona.calle',array('label'=>'Calle *','size'=>40,'maxlength'=>100,'value' => (isset($persona['calle']) ? $persona['calle'] : ''))); ?></td>
        <td style="font-weight: bold;"><?php echo $frm->number('Persona.numero_calle',array('label'=>'Nro *','size'=>5,'maxlength'=>5,'value' => (isset($persona['numero_calle']) ? $persona['numero_calle'] : ''))); ?></td>
        <td><?php echo $frm->input('Persona.piso',array('label'=>'Piso','size'=>3,'maxlength'=>3,'value' => (isset($persona['piso']) ? $persona['piso'] : ''))); ?></td>
        <td><?php echo $frm->input('Persona.dpto',array('label'=>'Dpto','size'=>3,'maxlength'=>3,'value' => (isset($persona['dpto']) ? $persona['dpto'] : ''))); ?></td>
        <td><?php echo $frm->input('Persona.barrio',array('label'=>'Barrio','size'=>30,'maxlength'=>100,'value' => (isset($persona['barrio']) ? $persona['barrio'] : ''))); ?></td>
    </tr>
</table>
<table class="tbl_form">
    <tr>
        <td><?php echo $frm->input('Persona.entre_calle_1',array('label'=>'Entre Calle','size'=>40,'maxlength'=>40,'value' => (isset($persona['entre_calle_1']) ? $persona['entre_calle_1'] : ''))); ?></td>
        <td colspan="4"><?php echo $frm->input('Persona.entre_calle_2',array('label'=>'y Calle','size'=>40,'maxlength'=>40,'value' => (isset($persona['entre_calle_2']) ? $persona['entre_calle_2'] : ''))); ?></td>
    </tr>
</table>    
<table class="tbl_form">    
    <tr>
        <td colspan="5">
            <?php echo $this->renderElement('localidad/autocomplete2',array('plugin' => 'config','model' => 'Persona','localidad_id' => (isset($persona['localidad_id']) ? $persona['localidad_id'] : ''),'localidadAproxima' => (isset($persona['localidad']) ? $persona['localidad'] : ''),'localidad' => (isset($persona['localidad']) ? $persona['localidad'] : ''),'codigo_postal' => (isset($persona['codigo_postal']) ? $persona['codigo_postal'] : ''),'provincia_id' => (isset($persona['provincia_id']) ? $persona['provincia_id'] : NULL)))?>
    </tr>
    <tr>
        <td colspan="5">
            <?php echo $this->renderElement('personas/forms/geolocalizacion_latlong_form',array('persona_id' => (isset($persona['id']) ? $persona['id'] : 0),'maps_latitud' => (isset($persona['maps_latitud']) ? $persona['maps_latitud'] : ''),'maps_longitud' => (isset($persona['maps_longitud']) ? $persona['maps_longitud'] : ''),'plugin' => 'pfyj'))?>         
        </td>
    </tr>
</table>
<?php // debug($persona['localidad'])?>