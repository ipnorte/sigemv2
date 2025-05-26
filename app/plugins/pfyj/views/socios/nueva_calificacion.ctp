<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>
<h3>INFORMACION DEL SOCIO</h3>
<div class="areaDatoForm">
	<h3>SOCIO #<?php echo $socio['Socio']['id']?></h3>
	<div class="row">
		FECHA DE ALTA:&nbsp;<strong><?php echo $util->armaFecha($socio['Socio']['fecha_alta'])?></strong>
		&nbsp;&nbsp;
		ESTADO:&nbsp;<strong><?php echo ($socio['Socio']['activo'] == 1 ? '<span style="color:green;">ACTIVO</span>' : '<span style="color:red;">NO ACTIVO</span>')?></strong>
		<?php if($socio['Socio']['activo'] == 0):?>
			&nbsp;|&nbsp;
			FECHA BAJA: &nbsp;<strong><?php echo $util->armaFecha($socio['Socio']['fecha_baja'])?></strong>
			&nbsp;|&nbsp;
			MOTIVO: <strong><?php echo $util->globalDato($socio['Socio']['codigo_baja'])?></strong>
		<?php endif;?>			
	</div>

</div>
<script type="text/javascript">
function confirmarForm(){
	var msgConfirm = "ATENCION!\n\n";
	msgConfirm = msgConfirm + "CALIFICAR AL SOCIO #<?php echo $socio['Socio']['id']?>\n\n";
	msgConfirm = msgConfirm + "CALIFICACION: *** " + getTextoSelect('SocioCalificacion') + " ***\n";
	msgConfirm = msgConfirm + "BENEFICIO: " + getTextoSelect('PersonaBeneficioPersonaBeneficioId') + "\n\n";
	msgConfirm = msgConfirm + "FECHA: <?php echo date('d/m/Y')?> \n\n";
        msgConfirm = msgConfirm + "A PARTIR DE: " + getTextoSelect('LiquidacionPeriodo') + "\n\n";
	return confirm(msgConfirm);
}
</script>
<?php echo $form->create(null,array('action' => 'nueva_calificacion/'. $socio['Socio']['id'], 'onsubmit' => "return confirmarForm()"));?>
<div class="areaDatoForm">
	<h3>NUEVA CALIFICACION</h3>
	<table class="tbl_form">
		<tr>
			<td>BENEFICIO QUE CALIFICA</td>
			<td><?php echo $this->requestAction('/pfyj/persona_beneficios/combo/PersonaBeneficio/'.$socio['Persona']['id'].'/0/0/0/.')?></td>
		</tr>	
		<tr>
			<td>NUEVA CALIFICACION</td>
			<td>
			<?php echo $this->renderElement('global_datos/combo_global',array(
																			'plugin'=>'config',
																			'label' => " ",
																			'model' => 'Socio.calificacion',
																			'prefijo' => 'MUTUCALI',
																			'disabled' => false,
																			'empty' => false,
																			'metodo' => "calificaciones_socio",
																			'selected' => "MUTUCALINORM"	
			))?>							
			</td>
		</tr>
                <tr>
                    <td>A PARTIR DE</td>
                    <td>
                        <?php 
                        echo $this->renderElement("liquidacion/combo_periodos_habilitados",array('plugin' => 'mutual', 'selected' => $periodo));
                        ?>
                    </td>
                </tr>
	</table>
	<?php echo $frm->hidden('Socio.id',array('value' => $socio['Socio']['id'])); ?>
	<?php echo $frm->hidden('Socio.persona_id',array('value' => $socio['Socio']['persona_id'])); ?>
        <?php echo $frm->hidden('SocioCalificacion.prioritaria',array('value' => 1)); ?>
</div>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/pfyj/socios/index/".$persona['Persona']['id'] : $fwrd) ))?>