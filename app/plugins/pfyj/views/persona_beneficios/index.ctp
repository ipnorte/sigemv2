<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>

<?php
#CONTROL DEL MODULO DE NOSIS VALIDACION DE IDENTIDAD
$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
$MOD_NOSIS_CBU = (isset($INI_FILE['general']['nosis_validar_cbu']) && $INI_FILE['general']['nosis_validar_cbu'] == 1 ? TRUE : FALSE);        
#MODULO DE TARJETAS DE DEBITO
$MOD_TARJETAS = (isset($INI_FILE['general']['tarjetas_de_debito']) && $INI_FILE['general']['tarjetas_de_debito'] == 1 ? TRUE : FALSE);

?> 

<h3>BENEFICIOS ASIGNADOS</h3>

<div class="actions">
<?php if($persona['Persona']['fallecida'] == 0) echo $controles->botonGenerico('add/'.$persona['Persona']['id'],'controles/chart_organisation.png','NUEVO BENEFICIO')?>
&nbsp;|&nbsp;
<?php if($persona['Persona']['fallecida'] == 0) echo $controles->botonGenerico('/config/bancos/gen_cbu','controles/attach.png','GENERAR CBU',array('target'=>'blank'))?>
</div>
<?php //   debug($beneficios)?>
<table style="width: 50%;">

	<tr>
	
		<th>#</th>
		<th></th>
		<th></th>
		<th>ORGANISMO</th>
		<th>NRO BENEFICIO</th>
		<th>%</th>
		<th>EMPRESA</th>
		<th>REPARTICION - TURNO</th>
		<th>NRO CBU</th>
		<th>BANCO</th>
		<th>SUCURSAL | CUENTA</th>
		<th>ACUERDO DEBITO</th>
		<th>MOTIVO BAJA</th>
		<th>FECHA BAJA</th>
		<th>BAJA CUOTAS</th>
		<th>REASIGNADO</th>
		<?php if($MOD_TARJETAS):?>	
		<th>TARJETA DEBITO</th>
		<?php endif;?>	
		<th></th>	
		<th></th>
		<th></th>
		<th></th>
		<th></th>
	</tr>

<?php foreach ($beneficios as $b):?>

<tr<?php echo ' class="activo_'.$b['PersonaBeneficio']['activo'].'"';?>>
	<td align="center"><?php echo $b['PersonaBeneficio']['id']?></td>
	<td align="center"><?php echo $controles->OnOff($b['PersonaBeneficio']['activo'])?></td>
<!--	<td align="center"><?php //   if(!empty($b['PersonaBeneficioCompartido'])) echo $controles->botonGenerico('sub_beneficios/'.$b['PersonaBeneficio']['id'],'controles/arrow_divide.png')?></td>-->
	<td align="center"><?php if((substr($b['PersonaBeneficio']['codigo_beneficio'],8,2) == '22' || substr($b['PersonaBeneficio']['codigo_beneficio'],8,2) == '77') && $b['PersonaBeneficio']['acuerdo_debito'] == 0) echo $controles->botonGenerico('sub_beneficios/'.$b['PersonaBeneficio']['id'],'controles/arrow_divide.png')?></td>
	<td><?php echo $this->requestAction('/config/global_datos/valor/'.$b['PersonaBeneficio']['codigo_beneficio'])?></td>
	<td align="center"><?php echo $b['PersonaBeneficio']['tipo']?>&nbsp;<?php echo $b['PersonaBeneficio']['nro_ley']?>&nbsp;<?php echo $b['PersonaBeneficio']['nro_beneficio']?>&nbsp;<?php echo $b['PersonaBeneficio']['sub_beneficio']?></td>
	<td align="center"><?php echo ($b['PersonaBeneficio']['porcentaje'] != 100 ? "<strong><span style='color:red;'>".$util->nf($b['PersonaBeneficio']['porcentaje'])."%</span></strong>" : $util->nf($b['PersonaBeneficio']['porcentaje'])."%")?></td>
	<td><?php echo (!empty($b['PersonaBeneficio']['codigo_empresa'])  && substr($b['PersonaBeneficio']['codigo_beneficio'],8,2) == '22' ? $this->requestAction('/config/global_datos/valor/'.$b['PersonaBeneficio']['codigo_empresa']) : '') ?></td>
	<td><?php echo $b['PersonaBeneficio']['codigo_reparticion']?> - <?php echo substr(trim($b['PersonaBeneficio']['turno_pago']),-5,5)?></td>
	<td><?php echo $b['PersonaBeneficio']['cbu']?></td>
	<td><?php echo (!empty($b['PersonaBeneficio']['banco_id']) ? $this->requestAction('/config/bancos/nombre/'.$b['PersonaBeneficio']['banco_id']) : '')?></td>
	<td nowrap="nowrap"><?php echo $b['PersonaBeneficio']['nro_sucursal']?> | <?php echo $b['PersonaBeneficio']['nro_cta_bco']?></td>
	<td align="right">
		<?php echo ($b['PersonaBeneficio']['acuerdo_debito'] != 0 ? "<span style='color:red;'><strong>".$util->nf($b['PersonaBeneficio']['acuerdo_debito'])."</strong></span>" : '')?>
		<?php echo ($b['PersonaBeneficio']['importe_max_registro_cbu'] != 0 ? "<span style='color:red;'><strong>".$util->nf($b['PersonaBeneficio']['importe_max_registro_cbu'])."/REG</strong></span>" : '')?>
	</td>
	
	<td><?php echo $util->globalDato($b['PersonaBeneficio']['codigo_baja'])?></td>
	<td align="center"><?php echo ($b['PersonaBeneficio']['activo'] == 0 ? $util->armaFecha($b['PersonaBeneficio']['fecha_baja']) : '')?></td>
	<td align="center"><?php echo ($b['PersonaBeneficio']['accion'] == 'B' && $b['PersonaBeneficio']['activo'] == 0 ? 'SI' : '')?></td>
	<td align="center"><?php echo (!empty($b['PersonaBeneficio']['reasignado_id']) ? '#'.$b['PersonaBeneficio']['reasignado_id'] : '')?></td>
	<?php if($MOD_TARJETAS):?><td align="center"><?php echo $b['PersonaBeneficio']['tarjeta_numero']?></td><?php endif;?>	
	<td class="actions"><?php echo ($b['PersonaBeneficio']['activo'] == 0 ? '' : $controles->getAcciones($b['PersonaBeneficio']['id'],false,true,false))?></td>
	<td><?php echo ($b['PersonaBeneficio']['activo'] == 0 ? '' : $controles->botonGenerico('baja/'.$b['PersonaBeneficio']['id'],'controles/stop1.png'))?></td>
	<td><?php echo ((substr($b['PersonaBeneficio']['codigo_beneficio'],8,2) == 22) ? $controles->botonGenerico('acuerdo_debito/'.$b['PersonaBeneficio']['id'],'controles/disk.png') : '')?></td>
	<td><?php 
	if(!empty($b['PersonaBeneficio']['tarjeta_debito']) && $MOD_TARJETAS){
			echo $controles->btnModalBox(array('title' => 'TARJETA DE DEBITO','img'=> 'vcard.png','texto' => '','url' => '/pfyj/persona_beneficios/tarjeta/'.$b['PersonaBeneficio']['id'],'h' => 450, 'w' => 850));
		}
		?>
	</td>	
	<td><?php 
	   if($MOD_TARJETAS){
		    echo $controles->botonGenerico("/pfyj/persona_beneficios/tarjeta_edit/".$b['PersonaBeneficio']['id'],"controles/application_edit.png");
		}
		?>
	</td>	
</tr>

<?php endforeach;?>

</table>
<?php //   echo $this->renderElement('persona_beneficios/' . Configure::read('APLICACION.beneficios_externos_render'),array('documento' => $persona['Persona']['documento']))?>
<?php // debug($beneficios)?>