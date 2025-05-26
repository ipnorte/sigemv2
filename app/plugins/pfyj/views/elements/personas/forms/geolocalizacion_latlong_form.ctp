<?php 
    $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
    if(isset($INI_FILE['general']['google_api_key']) &&  !empty($INI_FILE['general']['google_api_key'])):
?>        
<div class="areaDatoForm3">
    <h3 style="color: gray;">COORDENADAS GEOGRAFICAS DEL DOMICILIO</h3>
<table class="tbl_form">
    <tr>
        <td><?php echo $frm->input('Persona.maps_latitud',array('label'=>'LATITUD','size'=>20,'maxlength'=>20,'value' => $maps_latitud)); ?></td>
        <td><?php echo $frm->input('Persona.maps_longitud',array('label'=>'LONGITUD','size'=>20,'maxlength'=>20,'value' => $maps_longitud)); ?></td>
        <td><?php if(isset($INI_FILE['general']['google_api_key']) &&  !empty($INI_FILE['general']['google_api_key']) && !empty($maps_latitud) && !empty($maps_longitud)) echo $controles->botonGenerico('/pfyj/personas/google_maps/'.$persona_id,'controles/google-map-icon.jpg',NULL,array('target' => 'blank'));?></td>
    </tr>
</table>
    <hr/>
<h3 style="color: gray;">Obtener las coordenadas de un lugar</h3>
<div style="color: gray;">
<p>Para buscar las coordenadas de un lugar en Google Maps, sigue los pasos que se indican a continuación.</p>

<ol>
    <li style="text-indent:0px;margin:5px 0px 5px 25px;">Abre <a href="//www.google.com/maps" target="_blank" style="text-decoration: underline;color: #006AA9;">Google Maps</a>.</li>
  <li style="text-indent:0px;margin:5px 0px 5px 25px;">Haz clic derecho en el lugar o en el área del mapa.</li>
  <li style="text-indent:0px;margin:5px 0px 5px 25px;">Selecciona <strong>¿Qué hay aquí?</strong></li>
  <li style="text-indent:0px;margin:5px 0px 5px 25px;">Aparece una tarjeta en la parte inferior de la pantalla con más información. Muestra las coordenadas Geográficas (latitud,longitud).</li>
</ol> 
</div>
</div>	
<?php  endif;?>  
