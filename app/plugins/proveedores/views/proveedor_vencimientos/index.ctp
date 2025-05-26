<h1>FECHA DE CORTE Y VENCIMIENTOS</h1>

<hr>
<div id="FormSearch">
	<?php echo $form->create(null,array('action'=>'index','type'=>'get'));?>
	<table>
		<tr>
			<td colspan="3"><strong>SELECCIONAR ORGANISMO Y MES</strong></td>
		</tr>
		<tr>
			<td>
		<?php echo $this->renderElement('global_datos/combo_global',array(
																		'plugin'=>'config',
																		'label' => " ",
																		'model' => 'ProveedorVencimiento.codigo_organismo',
																		'prefijo' => 'MUTUCORG',
																		'disabled' => false,
																		'empty' => false,
																		'metodo' => "get_organismos",
																		'selected' => $orgSel	
		))?>			
			
			<?php //   echo $this->requestAction('/config/global_datos/combo/ORGANISMO/ProveedorVencimiento.codigo_organismo/MUTUCORG/0/0/'.$orgSel);?>
			</td>
			<td><?php echo $frm->meses('ProveedorVencimiento.mes','MES',$mesSel);?></td>
			<td><?php echo $form->submit('CONSULTAR');?></td>
			<td><?php echo $controles->btnAdd('Agregar','add')?></td>
		</tr>
	</table>
	<?php echo $form->end();?> 
</div>
<div style="clear: both;"></div>

<?php if(!empty($vtos)):?>

	
	<h3 style="border-bottom: 1px solid;"><?php echo $this->requestAction('/config/global_datos/valor/'.$orgSel.'/concepto_1')?> :: <?php echo $util->mesToStr($mesSel,true)?></h3>
	
	<div class="row">
		<?php echo $controles->btnEdit('Modificar Global',"edita_masivo_organismo/$orgSel/$mesSel")?>
		<?php echo $controles->btnDrop('Borrar '.$util->mesToStr($mesSel,true),"borrar_masivo_organismo/$orgSel/$mesSel","Borrar el mes de ".$util->mesToStr($mesSel,true)."?")?>
		<?php echo $controles->btnDrop('Borrar TODOS LOS MESES',"borrar_masivo_organismo/$orgSel","Borrar todos los meses?")?>
	</div>
	<div class="row">&nbsp;</div>
	<table>
		<tr>
			<th>PROVEEDOR</th>
			<th>DIA CORTE</th>
			<th>INI AC</th>
			<th>INI DC</th>
			<th>VTO SOCIO</th>
			<th>DIA VTO.</th>
			<th>VTO.PROV.</th>
			<th></th>
		</tr>
	<?php foreach($vtos as $vto):?>
	<tr class="activo_<?php echo $vto['Proveedor']['activo']?>">
		<td><strong><?php echo $vto['Proveedor']['razon_social']?></strong></td>
		<td align="center"><?php echo $vto['ProveedorVencimiento']['d_corte']?></td>
		<td align="center">+<?php echo $vto['ProveedorVencimiento']['m_ini_socio_ac_suma']?>m</td>
		<td align="center">+<?php echo $vto['ProveedorVencimiento']['m_ini_socio_dc_suma']?>m</td>
		<td align="center">+<?php echo $vto['ProveedorVencimiento']['m_vto_socio_suma']?>m</td>
		<td align="center"><?php echo $vto['ProveedorVencimiento']['d_vto_socio']?></td>
		<td align="center">+<?php echo $vto['ProveedorVencimiento']['d_vto_proveedor_suma']?>d</td>
		<td class="actions"><?php echo $controles->getAcciones($vto['ProveedorVencimiento']['id'],false) ?></td>
	</tr>
	<?php endforeach;?>
	</table>
<?php endif;?>