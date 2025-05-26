
<?php 
if($menuPersonas == 1) {echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));}
else {echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));}
?>

<h3>REINTEGRO ANTICIPADO</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<h4>GENERAR REINTEGRO ANTICIPADO</h4>

<script type="text/javascript">

function validateForm(){
	var ret = true;
	var importe = $('SocioReintegroImporteReintegro').getValue();
	ret = validNumber('SocioReintegroImporteReintegro','Indicar el Importe',true,'');
	if(!ret) return false;
	return confirm("GENERAR UN REINTEGRO ANTICIPADO POR $" + importe + "\nAPLICADO A LA LIQUIDACION " + getTextoSelect("SocioReintegroLiquidacionId"));
}

</script>

<div class="areaDatoForm">
	
	<?php echo $frm->create(null,array('action' => 'reintegro_anticipado/'.$socio['Socio']['id'],'onsubmit' => "return validateForm()"))?>

	<table class="tbl_form">
	
		<tr>
			<td>LIQUIDACION (PENDIENTE DE IMPUTAR)</td>
			<td><?php echo $frm->input('SocioReintegro.liquidacion_id',array('type' => 'select','options' => $liquidaciones,'label' => null))?></td>
		</tr>
		<tr>
			<td>IMPORTE A REINTEGRAR</td>
			<td><?php echo $frm->money('SocioReintegro.importe_reintegro')?></td>
		</tr>		
	
	</table>
	<div class="notices" style="width: 98%"><strong>ATENCION!</strong> Para que el REINTEGRO ANTICIPADO se compense ANTES DE IMPUTAR deber&aacute; generar la ORDEN DE PAGO del mismo.</div>
	<?php echo $frm->hidden('SocioReintegro.id',array('value' => 0))?>
	<?php echo $frm->hidden('SocioReintegro.socio_id',array('value' => $socio['Socio']['id']))?>
	<?php echo $frm->hidden('SocioReintegro.anticipado',array('value' => 1))?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GENERAR REINTEGRO ANTICIPADO','URL' => ( empty($fwrd) ? "/pfyj/socio_reintegros/by_socio/".$socio['Socio']['id'] : $fwrd) ))?>

</div>

