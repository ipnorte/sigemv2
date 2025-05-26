<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>

<h3>Servicio de Consulta SIISA</h3>

<div class="areaDatoForm">
	<?php echo $form->create(null,array('action' => 'consulta_siisa/' . $persona['Persona']['id']));?>
    <table class="tbl_form">
        <tr>
            <td colspan="2"><?php echo $this->requestAction('/pfyj/persona_beneficios/combo/Persona/'.$persona['Persona']['id'])?></td>
        </tr>
        <tr><td><?php echo $frm->money('Persona.sueldo_neto','SUELDO NETO',(isset($this->data['Persona']['sueldo_neto']) ? $this->data['Persona']['sueldo_neto'] : '0.00')); ?>
        <?php echo $frm->money('Persona.debitos_por_cbu','DEBITOS',(isset($this->data['Persona']['debitos_por_cbu']) ? $this->data['Persona']['debitos_por_cbu'] : '0.00')); ?>
        <?php echo $frm->money('Persona.cuota_credito','CUOTA',(isset($this->data['Persona']['cuota_credito']) ? $this->data['Persona']['cuota_credito'] : '0.00')); ?></td><td><input type="submit" value="CONSULTAR" /></td></tr>        
            
    </table>
	<?php echo $form->end();?>
</div>

<?php if($respuesta):?>

<table>

<tr>
	<th colspan="2">Par&aacute;metros Consulta</th>
</tr>
  <tr>
    <th>Variable</th>
    <th>Resultado</th>
  </tr>
  <tr><td>apellidoNombre</td><td><strong><?php echo $params->apellidoNombre?></strong></td></tr>
  <tr><td>nroDoc</td><td><strong><?php echo $params->nroDoc?></strong></td></tr>
  <tr><td>tipo_de_producto</td><td><strong><?php echo $params->tipo_de_producto?></strong></td></tr>  

</table>

<div class="areaDatoForm2" style="background-color:<?php echo ($respuesta->aprueba == 1 ? "#CDEB8B" : "#FFBBBB")?>;">

	

	<?php if(!$respuesta->onError):?>
	
		<h3>Resultado: #<?php echo $respuesta->executedPolicy?> - <?php echo $respuesta->decisionResult?></h3>
	
	<?php else:?>
	
		<?php echo "ERROR SERVICIO COD: <strong>" . $respuesta->oERROR->httpCode . "</strong> | MSG SIISA: " . $respuesta->oERROR->message;?>

	<?php endif;?>

</div>

<table>

<tr>
	<th colspan="2">Respuesta del Servicio</th>
</tr>
  <tr>
    <th>Variable</th>
    <th>Resultado</th>
  </tr>
  <tr><td>nroDoc</td><td><?php echo $respuesta->nroDoc?></td></tr>
  <tr><td>aprueba</td><td><?php echo $respuesta->aprueba?></td></tr>
  <tr><td>rechaza</td><td><?php echo $respuesta->rechaza?></td></tr>
  <tr><td>minimoDisponible</td><td><?php echo $respuesta->minimoDisponible?></td></tr>
  <tr><td>monto_max</td><td><?php echo $respuesta->monto_max?></td></tr>
  <tr><td>executedPath</td><td><?php echo $respuesta->executedPath?></td></tr>
  <tr><td>currentExecId</td><td><?php echo $respuesta->currentExecId?></td></tr>
  <tr><td>executionDate</td><td><?php echo $respuesta->executionDate?></td></tr>
  <tr><td>executionTime</td><td><?php echo $respuesta->executionTime?></td></tr>
  <tr><td>apellidoNombre</td><td><?php echo $respuesta->apellidoNombre?></td></tr>
  <tr><td>decisionResult</td><td><?php echo $respuesta->decisionResult?></td></tr>
  <tr><td>executedPolicy</td><td><?php echo $respuesta->executedPolicy?></td></tr>
  <tr><td>executedVersion</td><td><?php echo $respuesta->executedVersion?></td></tr> 
</table>
<?php else:?>
<?php if(!empty($ERROR)):?>
<div class='notices_error'><?php echo $ERROR?></div>
<?php endif;?>
<?php endif;?>
