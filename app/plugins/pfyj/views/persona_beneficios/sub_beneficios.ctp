<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>

<h3>BENEFICIO COMPARTIDO</h3>

<div class="areaDatoForm">
	<h4>BENEFICIO PRINCIPAL AL CUAL SE ASOCIAN LOS BENEFICIOS COMPARTIDOS</h4>
	ORGANISMO: <strong><?php echo $beneficio['PersonaBeneficio']['codigo_beneficio_desc']?></strong>
	<br/>
	BENEFICIO: <strong><?php echo $beneficio['PersonaBeneficio']['string']?></strong>
	<br/>
	PORCENTAJE: <strong><?php echo $util->nf($beneficio['PersonaBeneficio']['porcentaje'])?> %</strong>
</div>
<div>
	<?php echo $controles->btnRew('REGRESAR A BENEFICIOS','/pfyj/persona_beneficios/index/'.$persona['Persona']['id'])?>
	<?php if(substr($beneficio['PersonaBeneficio']['codigo_beneficio'],8,2) == '22'):?>
	&nbsp;|&nbsp;
	<?php echo $controles->botonGenerico('agregar_beneficio_compartido/'.$beneficio['PersonaBeneficio']['id'],'controles/chart_organisation.png','AGREGAR')?>
	<?php endif;?>
</div>
<?php if(!empty($beneficio['PersonaBeneficioCompartido'])):?>
	<table>
		<tr>
			<th></th>
			<th></th>
			<th>DOCUMENTO</th>
			<th>BENEFICIARIO</th>
			<th>ORGANISMO</th>
			<th>TIPO</th>
			<th>LEY</th>
			<th>NRO BENEFICIO</th>
			<th>SUB-BENEFICIO</th>
			<th>EMPRESA</th>
			<th>REPARTICION - TURNO</th>
			<th>NRO CBU</th>
			<th>BANCO</th>
			<th>SUCURSAL</th>
			<th>CUENTA</th>
			<th>PORCENTAJE</th>
			<th>PRINCIPAL</th>
			<th>ACTIVO</th>
		</tr>
	<?php foreach($beneficio['PersonaBeneficioCompartido'] as $beneficioCompartido):?>
		<?php //   debug($beneficioCompartido)?>
		<tr <?php echo ' class="activo_'.$beneficioCompartido['principal'].'"';?>>
			<td><?php echo $controles->botonGenerico('borrar_beneficio_compartido/'.$beneficioCompartido['id'],'controles/user-trash-full.png',null,null,"BORRAR EL SUB-BENEFICIO #".$beneficioCompartido['id']."?")?></td>
			<td>#<?php echo $beneficioCompartido['id']?></td>
			<td><?php echo $beneficioCompartido['documento']?></td>
			<td><?php echo $beneficioCompartido['beneficiario']?></td>
			<td><?php echo $util->globalDato($beneficioCompartido['codigo_beneficio'])?></td>
			<td align="center"><?php echo $beneficioCompartido['tipo']?></td>
			<td align="center"><?php echo $beneficioCompartido['nro_ley']?></td>
			<td align="center"><?php echo $beneficioCompartido['nro_beneficio']?></td>
			<td align="center"><?php echo $beneficioCompartido['sub_beneficio']?></td>
			<td><?php echo (!empty($beneficioCompartido['codigo_empresa'])  && $beneficioCompartido['codigo_beneficio'] == 'MUTUCORG2201' ? $this->requestAction('/config/global_datos/valor/'.$beneficioCompartido['codigo_empresa']) : '') ?></td>
			<td><?php echo $beneficioCompartido['codigo_reparticion']?> - <?php echo substr(trim($beneficioCompartido['turno_pago']),-5,5)?></td>
			<td><?php echo $beneficioCompartido['cbu']?></td>
			<td><?php echo (!empty($beneficioCompartido['banco_id']) ? $this->requestAction('/config/bancos/nombre/'.$beneficioCompartido['banco_id']) : '')?></td>
			<td><?php echo $beneficioCompartido['nro_sucursal']?></td>
			<td><?php echo $beneficioCompartido['nro_cta_bco']?></td>
			<td align="right"><?php echo $util->nf($beneficioCompartido['porcentaje'])?> %</td>
			<td align="center"><?php echo $controles->onOff($beneficioCompartido['principal'],true)?></td>
			<td align="center"><?php echo $controles->onOff($beneficioCompartido['activo'])?></td>
		</tr>		
	<?php endforeach;?>
	</table>
<?php endif;?>