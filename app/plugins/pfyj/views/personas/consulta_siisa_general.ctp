<?php echo $this->renderElement('personas/menu_inicial',array('plugin' => 'pfyj'))?>
<h3>Servicio de Consulta SIISA</h3>

<script type="text/javascript">
	function validateForm(){
		
		if(!validRequired('PersonaDocumento','')) return false;
		if(!validRequired('PersonaNombre','')) return false;
		return true;
	}
</script>

<div class="areaDatoForm">
    <?php echo $form->create(null,array('action' => 'consulta_siisa_general', 'onsubmit' => "return validateForm()" ));?>
    <table class="tbl_form">
        <tr>
            <td><?php echo $frm->number('Persona.documento',array('label'=>'DOCUMENTO','size'=>8,'maxlength'=>8)); ?></td>
            <td><?php echo $frm->input('Persona.nombre',array('label'=>'NOMBRE','size'=>40,'maxlength'=>100)); ?></td>
            <td><?php echo $frm->money('Persona.sueldo_neto','SUELDO NETO',(isset($this->data['Persona']['sueldo_neto']) ? $this->data['Persona']['sueldo_neto'] : '0.00')); ?></td>
            <td><?php echo $frm->money('Persona.debitos_por_cbu','DEBITOS',(isset($this->data['Persona']['debitos_por_cbu']) ? $this->data['Persona']['debitos_por_cbu'] : '0.00')); ?></td>
            <td><?php echo $frm->money('Persona.cuota_credito','CUOTA',(isset($this->data['Persona']['cuota_credito']) ? $this->data['Persona']['cuota_credito'] : '0.00')); ?></td>
            <td>
                <div class="input text">
                <label for="siisa">PRODUCTO</label>
                <select name="data[Persona][producto_siisa]" id="siisa">
                <?php foreach($productos_siisa as $siisa): ?>
                    <option value="<?php echo $siisa['GlobalDato']['concepto_4']?>" class="input text"><?php echo $siisa['GlobalDato']['concepto_4']?></option>
                <?php endforeach;?>
                </select>
                </div>
            </td>             
            <td><input type="submit" class="btn_consultar" value=""></td>
        </tr>
    </table>
    <?php echo $form->end();?>
</div>


<?php if($respuestas):?>
<table>
	<tr>
		<th>#</th>
		<th>Producto SIISA</th>
		<th>Política</th>
		<th>Aprobado</th>
		<th>Monto Máximo</th>
		<th>Mínimo Dispo</th>
		<th>Respuesta SIISA</th>
	</tr>
	<?php foreach ($respuestas as $respuesta):?>
		<tr>
			<td><?php echo $respuesta['respuesta']->currentExecId?></td>
			<td><?php echo $respuesta['producto_siisa']?></td>
			<td style="text-align: center;"><?php echo $respuesta['respuesta']->executedPolicy?></td>
			<td style="text-align: center;"><?php echo ($respuesta['respuesta']->aprueba ? "SI": "NO")?></td>
			<td style="text-align: center;"><?php echo $respuesta['respuesta']->monto_max?></td>
			<td style="text-align: center;"><?php echo $respuesta['respuesta']->minimoDisponible?></td>
			<td style="color:<?php echo ($respuesta['respuesta']->aprueba == 1 ? "green" : "red")?>;">
			<?php 
			if(!$respuesta['respuesta']->onError) {
			    echo $respuesta['respuesta']->decisionResult;
			}else {
			    echo "ERROR SERVICIO COD: <strong>" . $respuesta['respuesta']->oERROR->httpCode . "</strong> | MSG SIISA: " . $respuesta['respuesta']->oERROR->message;
			}
			     
			?>
			</td>
		</tr>
	<?php endforeach;?>
</table>
<?php else:?>
<?php if(!empty($ERROR)):?>
<div class='notices_error'><?php echo $ERROR?></div>
<?php endif;?>
<?php endif;?>
