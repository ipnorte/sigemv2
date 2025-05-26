<?php 
if(!isset($persona)){
$persona = $this->requestAction('/pfyj/personas/get_persona/'.$persona_id);    
}

$linkToPadron = (isset($link_padron) ? $link_padron : true);
$infoSocio = (isset($infoSocio) ? $infoSocio : true);
//debug($persona);
?>
<div class="areaDatoForm">

<?php if(!empty($persona['Persona']['socio_nro']) && $infoSocio):?>
	<div class="areaDatoForm2">
	<h3><?php echo $persona['Persona']['socio'] ?></h3>
	CATEGORIA: <strong><?php echo $persona['Persona']['socio_categoria'] ?></strong>&nbsp;|&nbsp;ESTATUS ACTUAL: <strong><?php echo $persona['Persona']['socio_status'] ?></strong>
	&nbsp;|&nbsp;FECHA DE ALTA: <strong><?php echo $util->armaFecha($persona['Persona']['socio_fecha_alta'])?></strong>
	</div>
<?php endif;?>
<h3>DATOS PERSONALES</h3>
<?php if($persona['Persona']['fallecida'] == 1):?>
<div class="notices_error">PERSONA REGISTRADA COMO FALLECIDA EL <?php echo $util->armaFecha($persona['Persona']['fecha_fallecimiento'])?></div>
<?php endif;?>  
DOCUMENTO: <strong><?php echo $persona['Persona']['tdoc_ndoc']?></strong>
&nbsp;
APELLIDO Y NOMBRE: <strong><?php echo ($linkToPadron ?$this->renderElement('/personas/apenom_link_padron',array('persona' => $persona, 'plugin' => 'pfyj')) : $persona['Persona']['apellido'] .', '. $persona['Persona']['nombre'])?></strong>
<br/>
FECHA NACIMIENTO: <strong><?php echo $util->armaFecha($persona['Persona']['fecha_nacimiento'])?></strong>
&nbsp;
EDAD: <strong><?php echo $persona['Persona']['edad']?></strong>
&nbsp;
ESTADO CIVIL: <strong><?php echo $persona['Persona']['estado_civil_desc']?></strong>
&nbsp;
CUIT-CUIL: <strong><?php echo $persona['Persona']['cuit_cuil']?></strong>
<br/>
TELEFONO FIJO: <strong><?php echo $persona['Persona']['telefono_fijo']?></strong>
&nbsp;
TELEFONO MOVIL: <strong><?php echo $persona['Persona']['telefono_movil']?></strong>
&nbsp;
<?php if(!empty($persona['Persona']['telefono_referencia'])):?>
TELEFONO REFERENCIA: <strong><?php echo $persona['Persona']['telefono_referencia']?></strong> (REF: <?php echo $persona['Persona']['persona_referencia']?>)
<?php endif;?>
<?php if(!empty($persona['Persona']['e_mail'])):?>
<br/>
EMAIL: <strong><?php echo $text->autoLinkEmails($persona['Persona']['e_mail']);?></strong>
<br/>
<?php endif;?>
<br/>
DOMICILIO: <?php echo $persona['Persona']['domicilio']?> 
<?php 
    $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
    if(isset($INI_FILE['general']['google_api_key']) &&  !empty($INI_FILE['general']['google_api_key'])){
        echo $controles->botonGenerico('/pfyj/personas/google_maps/'.$persona_id,'controles/google-map-icon.jpg',NULL,array('target' => 'blank'));
    }    
?>

<?php if(!empty($persona['Persona']['adicionales'])):?>
	
	<br/>
	<div class="areaDatoForm3">
	<?php echo $controles->btnToggle('listaAdicionales',"<strong>ADICIONALES A CARGO</strong>",null,"cursor:pointer;padding:3px;font-size:11px;")?>
	<div id="listaAdicionales" style="display: none;">
		<br/>
		<table class='tbl_form' style="border: 1px solid;">
			<tr>
				<th>DOCUMENTO</th>
				<th>NOMBRE</th>
				<th>VINCULO</th>
				<th>EDAD</th>
				<th>DOMICILIO</th>
			</tr>
			<?php foreach($persona['Persona']['adicionales'] as $adicional):?>
				<tr>
					<td><?php echo $adicional['SocioAdicional']['tdoc_ndoc']?></td>
					<td><strong><?php echo $adicional['SocioAdicional']['apenom']?></strong></td>
					<td style="text-align: center;"><?php echo $adicional['SocioAdicional']['vinculo_desc']?></td>
					<td style="text-align: center;"><?php echo $adicional['SocioAdicional']['edad']?></td>
					<td><?php echo $adicional['SocioAdicional']['domicilio']?></td>
				</tr>
			<?php endforeach;?>
		</table>
	</div>
	</div>
<?php endif;?>

</div>

<?php //   debug($persona)?>